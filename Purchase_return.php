<?php  defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_return extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_category","common","mod_common"
        ));
        $this->load->library('Uploadimage');
        
    }

	public function index()
	{

	$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
		if(isset($_POST['submit'])){			
			$from_date = date("Y-m-d", strtotime($_POST['from']));
			
			$to_date = date("Y-m-d", strtotime($_POST['to']));
			
		}else{
			$from_date = date('Y-m-d', strtotime('-15 day'));
			$to_date = date('Y-m-d');
		}

		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
         if($sale_point_id==''){
       $where_sale_point_id="and isnull(tbl_issue_return.sale_point_id)";
        }else{
       $where_sale_point_id="and tbl_issue_return.sale_point_id='$sale_point_id'";
        }

		$data['category_list'] = $this->db->query("select tbl_issue_return.*,tblacode.aname from tbl_issue_return inner join tblacode on acode=scode  where tbl_issue_return.type='PR' and tbl_issue_return.irdate  between '$from_date' and '$to_date' $where_sale_point_id  ORDER BY tbl_issue_return.irnos DESC")->result_array();

		$data["title"] = "Manage Purchase Return";		

		$this->load->view($this->session->userdata('language')."/Purchase_return/manage_category",$data);
	} 
	 function get_p_price()
	{
	    
		$id=	$this->input->post('id');
		$date=	trim($this->input->post('date'));

		
		
		
		$rate =$this->db->query("select rate as rte from tbl_rate tbl_rate where itemid='$id' and date<='$date' and type='Purchase' order by date desc LIMIT 1")->row_array()['rte'];

if ($rate=='') {
	$rate=0;
}
	echo	$rate;										 
          
			
		 }
	public function add_category()
	{

	$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
		$login_user=$this->session->userdata('id');
		 $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		  if ($sale_point_id=='0') {
	  	$this->session->set_flashdata('err_message', 'Admin Has No Rights To Add Purchase.');
			redirect(SURL . 'Purchase_return');
			exit();
	  }
      $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
       $aname=$fix_code['vendor_code'];
       $bank_code=$fix_code['bank_code'];
		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '603' limit 1")->row_array();
		if ($role['add']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Purchase_return/index/');
			}
        $data['vendors'] = $this->db->query("select * from tblacode where general = '$aname'")->result_array();
        $data['bank_list'] = $this->db->query("select * from tblacode where general = '$bank_code'")->result_array();
       // pm($data['vendors']);
        	$data['itemss'] = $this->db->query("select * from tblmaterial_coding")->result_array();

        $data['products'] = $this->db->query("select * from tblmaterial_coding")->result_array();
        $data['d'] =$this->mod_common->select_last_records('tbl_posting_stock')['post_date'];
		$this->load->view($this->session->userdata('language')."/Purchase_return/add_category",$data);
	}

	public function edit($id){
		
	$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
		$login_user=$this->session->userdata('id');
		 $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
      $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
       $aname=$fix_code['vendor_code'];
       $bank_code=$fix_code['bank_code'];
		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '603' limit 1")->row_array();
		if ($role['edit']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Purchase_return/index/');
			}
	

	$data['vendors'] = $this->db->query("select * from tblacode where general = '$aname'")->result_array();
        $data['bank_list'] = $this->db->query("select * from tblacode where general = '$bank_code'")->result_array();


			$data['itemss'] = $this->db->query("select * from tblmaterial_coding")->result_array();
//
		$data['record'] = $this->db->query("select * from tbl_issue_return where irnos='$id'")->result_array()[0];
		$data['items'] = $this->db->query("select tbl_issue_return_detail.*,tblmaterial_coding.itemname from tbl_issue_return_detail inner join tblmaterial_coding on materialcode=itemid where irnos='$id'")->result_array();

//pm($data['items']);








		$this->load->view($this->session->userdata('language')."/Purchase_return/add_category", $data);
	}
public function add(){
//pm($this->input->post());exit;
	$login_user=$this->session->userdata('id');
$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
if (empty($this->input->post("item"))) {
	$this->session->set_flashdata('err_message1', 'Please Select Item !');

			redirect(SURL . 'Purchase_return/add_category');
}

//echo "select * from tbl_goodsreceiving where invoice_no='$invoice_no' and invoice_date='$invoice_date'";exit();
$order_date = $this->input->post('order_date');
$closing =$this->db->query("select trans_id from tbl_posting_stock where post_date='$order_date'")->row_array();
$c= count($closing);

if ($c!=0) {
	$this->session->set_flashdata('err_message1', 'Day is Closed !');

			redirect(SURL . 'Purchase_return/index');
}




$amt = $this->input->post('rec_amt');
$id = $this->input->post('Customer');
$login_user=$this->session->userdata('id');
$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

$fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
$stock_code=$fix_code['stock_code'];
$tax_receive=$fix_code['tax_receive'];
$cash_code=$fix_code['cash_code'];
$trans_id=$this->input->post("trans_id");
       if ($trans_id=='') {
       
      $trans_id = $this->db->query("select max(trans_id) as trans_id from tbl_issue_return where sale_point_id='$sale_point_id'")->row_array()['trans_id'];
     
      if($trans_id==''){
      	 $trans_id=1;
      	}else{
      		 $trans_id=$trans_id+1;
      	}

       }	
				
		

	
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			

			
			$v = $this->input->post("vendor");
			$array = array(
					

					
						
	    				"scode"=>$v,
	    				"total_bill"=>$this->input->post("total_bill"),
	    				"tax_amount"=>$this->input->post("tax_amount"),
	    				"pur_inv_no"=>$this->input->post("purchase_no"),
	    				"tax_percentage"=>$this->input->post("tax_percentage"),
	    				"discount_amt"=>$this->input->post("total_discount"),
	    				"item_disc"=>$this->input->post("item_disc"),
	    				"other_disc"=>$this->input->post("other_discount"),
	    				"irdate"=>$this->input->post("receiptdate"),
	    				"net_payable"=>$this->input->post("net_payable"),
	    				"remarks"=>$this->input->post("remarks"),
	    				"invoice_no"=>$this->input->post("invoice_no"),
	    				"invoice_date"=>$this->input->post("invoice_date"),
	    				"scheme_on"=>$this->input->post("scheme_on"),
	    				"total_paid"=>$this->input->post("total_paid"),
	    				"pay_mode"=>$this->input->post("pay_mode"),
	    				"bank_code"=>$this->input->post("bank_name"),
	    				"cheque_no"=>$this->input->post("cheque_no"),
	    				"cheque_dt"=>$this->input->post("cheque_date"),
	    				"sale_point_id"=>$sale_point_id,
	    				"trans_id"=>$trans_id,
	    				 "type"=>'PR',
						  );

			if(!empty($this->input->post("edit"))){
			
				$insertid = $this->input->post("edit");
				
				
			$this->mod_common->update_table("tbl_issue_return",array("irnos"=>$insertid), $array);
			//echo 'asda';exit;
				$vno = $sale_point_id."-PR-".$trans_id;

	    	$this->db->query("delete from tbltrans_master where vno='$vno'");

	    	$this->db->query("delete from tbltrans_detail where vno='$vno'");
			$this->db->query("delete from tbl_issue_return_detail where irnos='$insertid'");

				
	

			}else{
			$insertid = $this->mod_common->insert_into_table("tbl_issue_return",$array);

			}
			//exit();

			$i=0;
			
			foreach ($this->input->post("item") as $key => $value) {
//echo "kl";exit();
				$id = $this->input->post("item")[$i];
			
				

				$date = $this->input->post("receiptdate");
			


	

		
				$classcode = $this->db->query("select classcode from tblmaterial_coding where materialcode='$id'")->row_array()['classcode'];
				//$sub_cat = $this->db->query("select sub_cat from tblmaterial_coding where materialcode='$id'")->row_array()['sub_cat'];
				//$p_q = $this->db->query("select * from tbl_goodsreceiving_detail where itemid='$id' and batch_status='open' ORDER BY date asc  LIMIT 1")->row_array();
				$s_qan =$this->input->post("qty")[$i];
	
	//$pur_qty =$this->db->query("select sum(batch_stock) as qan from  tbl_goodsreceiving_detail where itemid='$id' and batch_status='open'")->row_array()['qan'];
$itm = $this->input->post("item")[$i];
$q = $this->input->post("qty")[$i];

$array1 = array(
							

						"irnos"=>$insertid,
	    				"itemid"=>$this->input->post("item")[$i],
	    				"quantity"=>$this->input->post("qty")[$i],
	    				"vat_amount"=>$this->input->post("disc")[$i],
	    				"rate"=>$this->input->post("unitcost")[$i],
	    				"amount"=>$this->input->post("amount")[$i],
	    				"ex_vat_amount"=>$this->input->post("amount")[$i],
	    				"pur_qty"=>$this->input->post("pur_qty")[$i],
	    				"category_id"=>$classcode,
						"recvd_date"=>$this->input->post("receiptdate"),
						"pur_inv_no"=>$this->input->post("purchase_no"),
						"trans_id"=>$trans_id,
	    				"sale_point_id"=>$sale_point_id,
						"type"=>'PR',
						
							
							
						  );

			$r=	$this->mod_common->insert_into_table("tbl_issue_return_detail",$array1);

				$totalamt = $this->input->post("total_bill");
				$Customer = $this->input->post("Customer");
$v = $this->input->post("vendor");
    $product = $this->input->post("item")[$i];
 $itemanem = $this->db->query("select * from tblmaterial_coding where materialcode='$product'")->result_array()[0]['itemname'];

$qty=$qty.','.$this->input->post("qty")[$i];
$totalamt=$this->input->post("total_bill");
$tamount=$this->input->post("amount")[$i];
$rate=$this->input->post("unitcost")[$i];
	   
$vno = $sale_point_id."-PR-".$trans_id;
	   
   $nar = $nar.','."$itemanem qty $qty@$rate amt=$tamount";
   $date = $this->input->post("receiptdate");


$i++;	}
	    $array = array(
	    				"vno"=>$vno,
	    				"vtype"=>"PR",
	    				"damount"=>$totalamt,
	    				"camount"=>$totalamt,
	    				"created_date"=>$date,
	    				"created_by"=>$login_user,
	    				"sale_point_id"=>$sale_point_id,
	    				"trans_id"=>$trans_id,
	    				"svtype"=>"PR",
	    			  );	

	    $master_id=$this->mod_common->insert_into_table("tbltrans_master",$array);
	    $j=1;
	    $array = array(
	    				"vno"=>$vno,
	    				"srno"=>$j,
	    				"acode"=>$stock_code,//stock code will come here
	    				"damount"=>"0",
	    				"camount"=>$totalamt,
	    				"remarks"=>$nar,
	    				"ig_detail_id"=>$master_id,
	    				"vtype"=>"PR",
	    				"svtype"=>"PR",
	    				"vdate"=>$date,
	    				"sale_point_id"=>$sale_point_id,
	    				"trans_id"=>$trans_id,
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_detail",$array);
	    $j++;

	    $array = array(
	    				"vno"=>$vno,
	    				"srno"=>$j,
	    				"acode"=>$v,
	    				"damount"=>$totalamt,
	    				"camount"=>"0",
	    				"remarks"=>$nar,
	    				"ig_detail_id"=>$master_id,
	    				"vtype"=>"PR",
	    				"svtype"=>"PR",
	    				"vdate"=>$date,
	    				"sale_point_id"=>$sale_point_id,
	    				"trans_id"=>$trans_id,
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_detail",$array);
	    $j++;
	    if ($this->input->post("tax_amount")>0) {
	    	$nar1='Tax Paid against Purchase #:'.$insertid.',Tax % : '.$this->input->post("tax_percentage").',Tax Amount :'.$this->input->post("tax_amount").'';

	    	$array = array(
	    				"vno"=>$vno,
	    				"srno"=>$j,
	    				"acode"=>$tax_receive,//stock code will come here
	    				"damount"=>"0",
	    				"camount"=>$this->input->post("tax_amount"),
	    				"remarks"=>$nar1,
	    				"ig_detail_id"=>$master_id,
	    				"vtype"=>"PR",
	    				"svtype"=>"PR",
	    				"vdate"=>$date,
	    				"sale_point_id"=>$sale_point_id,
	    				"trans_id"=>$trans_id,
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_detail",$array);
	    $j++;

	    $array = array(
	    				"vno"=>$vno,
	    				"srno"=>$j,
	    				"acode"=>$v,
	    				"damount"=>$this->input->post("tax_amount"),
	    				"camount"=>"0",
	    				"remarks"=>$nar1,
	    				"ig_detail_id"=>$master_id,
	    				"vtype"=>"PR",
	    				"svtype"=>"PR",
	    				"vdate"=>$date,
	    				"sale_point_id"=>$sale_point_id,
	    				"trans_id"=>$trans_id,
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_detail",$array);
	    $j++;
	    	
	    }
	    if ($this->input->post("total_paid")>0) {
	    	
	    	$pay_mode=$this->input->post("pay_mode");
	    	if($pay_mode=='bank')
		{
			$vtype='BP';
			$svtype='BP';
			$cheque_no= $this->input->post("cheque_no");
			$cheque_date= $this->input->post("cheque_date");
			$enter_amount= $this->input->post("net_payable");
			$bank_cash_code= $this->input->post("bank_name");
			
		}
		else
		{
			$vtype='CP';
			$svtype='CP';
			$enter_amount= $this->input->post("total_paid");
			$bank_cash_code=$cash_code;
			$cheque_no= '';
			$cheque_date= '';

			
		}
	    	$new_nar='Payment against Purchase #:'.$insertid.'';
	    	
	    	$array = array(
	    				"vno"=>$vno,
	    				"srno"=>$j,
	    				"acode"=>$v,//stock code will come here
	    				"damount"=>"0",
	    				"camount"=>$enter_amount,
	    				"remarks"=>$new_nar,
	    				"vtype"=>$vtype,
	    			    "ig_detail_id"=>$master_id,
	    				"svtype"=>$svtype,
	    				"chequeno"=>$cheque_no,
	    				"chequedate"=>$cheque_date,
	    				"vdate"=>$date,
	    				"sale_point_id"=>$sale_point_id,
	    				"trans_id"=>$trans_id,
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_detail",$array);
	    $j++;

	    $array = array(
	    				"vno"=>$vno,
	    				"srno"=>$j,
	    				"acode"=>$bank_cash_code,
	    				"damount"=>$enter_amount,
	    				"camount"=>"0",
	    				"remarks"=>$new_nar,
	    				"vtype"=>$vtype,
	    				"svtype"=>$svtype,
	    				"vdate"=>$date,
	    				"chequeno"=>$cheque_no,
	    				"chequedate"=>$cheque_date,
	    				"ig_detail_id"=>$master_id,
	    				"sale_point_id"=>$sale_point_id,
	    				"trans_id"=>$trans_id,
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_detail",$array);
	    $j++;
	    	
	    }

}
				

$this->session->set_flashdata('ok_message', 'Insert Successfully!');

			redirect(SURL . 'Purchase_return/');

	    }



	public function delete($id) {
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '603' limit 1")->row_array();
		if ($role['delete']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Purchase_return/index/');
			}
		$query = $this->db->query("delete from tbl_issue_return where trans_id='$id' and sale_point_id='$sale_point_id'");
		$query = $this->db->query("delete from tbl_issue_return_detail where trans_id='$id' and sale_point_id='$sale_point_id'");
		$vno=$sale_point_id."-PR-".$id;
		$this->db->query("delete from tbltrans_master where vno='$vno'");
	    $this->db->query("delete from tbltrans_detail where vno='$vno'");

        if ($query) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Purchase_return/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Purchase_return/');
        }
    }
    function get_accbal()
	{
		 
		$customer=$this->input->post('customer');

		$balance=$this->db->query("select SUM(damount)-SUM(camount) as balance from tbltrans_detail  where acode='$customer' ")->row_array()['balance'];
		$opening_balance=$this->db->query("select * from tblacode  where acode='$customer' ")->row_array();
		 $opening=$opening_balance['opngbl'];

		if($opening_balance[optype]=='Credit'){ 
			$opening=-1*$opening;
			 } 
			 $acc_balance=$balance+$opening;
		echo round($acc_balance,3);

	}
	public function detail_invoice($id){
		if($id){

		//$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
		$data['master_record'] = $this->db->query("select  * from  tbl_issue_return where irnos='$id'")->row_array();
        $data['res_record'] = $this->db->query("select  * from  tbl_company")->row_array();
         
        
        

		$data['detail_record'] = $this->db->query("SELECT tbl_issue_return_detail.*,tblmaterial_coding.itemname FROM `tbl_issue_return_detail` inner join tblmaterial_coding on tbl_issue_return_detail.itemid=tblmaterial_coding.materialcode where irnos='$id'")->result_array();
		
		$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];

		
		$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");
		//exit;
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Customer Invoice";
		$this->load->view($this->session->userdata('language')."/Purchase_return/detail_invoice",$data);
		}
	}
	  function get_data()
	{
		 
		$purchase_no=$this->input->post('purchase_no');
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

		$master_data=$this->db->query("select * from tbl_goodsreceiving  where receiptnos='$purchase_no' and sale_point_id='$sale_point_id' ")->row_array();
		
		echo json_encode($master_data);

	}
	 public function get_vendor(){
		$purchase_no = $this->input->post("purchase_no");
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		
		$record = $this->db->query("select * from tblacode where acode in(select suppliercode from tbl_goodsreceiving where receiptnos='$purchase_no' and sale_point_id='$sale_point_id')")->result_array();?>
		<?php
		foreach ($record as $key => $data) {
		 	
										
			?>
            
			<option   value="<?php echo $data['acode']; ?>"><?php echo ucwords($data['aname']); ?></option>
		<?php } ?>
	
<?php	

	}
	public function get_item(){
		$purchase_no = $this->input->post("purchase_no");
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		
		$record = $this->db->query("select * from tblmaterial_coding where materialcode in(select itemid from tbl_goodsreceiving_detail where receipt_detail_id='$purchase_no' and sale_point_id='$sale_point_id')")->result_array();
		?>
		<?php
		foreach ($record as $key => $data) {
		 	
										
			?>
            
			<option   value="<?php echo $data['materialcode']; ?>"><?php echo ucwords($data['itemname']." ".$data['bar_code']." ".$data['short_code']); ?></option>
		<?php } ?>
	
<?php	

	}
	 function get_detail()
	{
	    
	 	$id=	$this->input->post('id');
		$purchase_no = $this->input->post("purchase_no");
		

		
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

		 $recv_qty =$this->db->query("select sum(quantity) as quantity from tbl_issue_return_detail  where itemid='$id' and pur_inv_no='$purchase_no' and sale_point_id='$sale_point_id'")->row_array()['quantity'];

		

		$rate =$this->db->query("select rate as rate from tbl_goodsreceiving_detail  where itemid='$id' and receipt_detail_id='$purchase_no' and sale_point_id='$sale_point_id'")->row_array()['rate'];
		// echo"select rate as rate from tbl_goodsreceiving_detail  where itemid='$id' and receipt_detail_id='$purchase_no' and sale_point_id='$sale_point_id'";exit();

		$discount =$this->db->query("select vat_amount as discount from tbl_goodsreceiving_detail  where itemid='$id' and receipt_detail_id='$purchase_no' and sale_point_id='$sale_point_id'")->row_array()['discount'];

		$quantity =$this->db->query("select quantity as quantity from tbl_goodsreceiving_detail  where itemid='$id' and receipt_detail_id='$purchase_no' and sale_point_id='$sale_point_id'")->row_array()['quantity'];

		
		
		
		
$unitcost=round($rate,3);
$disc=round($discount,3);

	echo	$recv_qty.'_'.$unitcost.'_'.$disc.'_'.$quantity;										 
          
			
		 }
	function get_short_code()
	{
	    
		$id=	$this->input->post('id');
		 
		
		 $result = $this->db->query("SELECT short_code from tblmaterial_coding where materialcode='$id' ")->row_array()['short_code'];
		
	

if ($result=='') {
	$result=0;
}
	echo	$result;										 
          
			
		 }

    


}
