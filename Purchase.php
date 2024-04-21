<?php  defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase extends CI_Controller {

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
       $where_sale_point_id="and isnull(tbl_goodsreceiving.sale_point_id)";
        }else{
       $where_sale_point_id="and tbl_goodsreceiving.sale_point_id='$sale_point_id'";
        }

		$data['category_list'] = $this->db->query("select tbl_goodsreceiving.*,tblacode.aname from tbl_goodsreceiving inner join tblacode on acode=suppliercode  where tbl_goodsreceiving.type='PV' and tbl_goodsreceiving.receiptdate  between '$from_date' and '$to_date' $where_sale_point_id and  tbl_goodsreceiving.po_no='0' ORDER BY tbl_goodsreceiving.receiptnos DESC")->result_array();

		$data["title"] = "Manage Purchase";		

		$this->load->view($this->session->userdata('language')."/Purchase/manage_category",$data);
	} 
	 function get_p_price()
	{
	    
		$id=	$this->input->post('id');
		$date=	trim($this->input->post('date'));

		
		 $result = $this->db->query("SELECT tblmaterial_coding.*,tblcategory.* From tblmaterial_coding INNER JOIN tblcategory ON tblmaterial_coding.catcode = tblcategory.catcode where tblmaterial_coding.materialcode='$id' ")->row_array()['vat_perc'];;
		
	

if ($result=='') {
	$result=0;
}
	echo	$result;										 
          
			
		 }
	public function add_category()
	{

	$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
		$login_user=$this->session->userdata('id');
		 $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		  if ($sale_point_id=='0') {
	  	$this->session->set_flashdata('err_message', 'Admin Has No Rights To Add Purchase.');
			redirect(SURL . 'Purchase');
			exit();
	  }
      $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
       $aname=$fix_code['vendor_code'];
       $bank_code=$fix_code['bank_code'];
		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '603' limit 1")->row_array();
		if ($role['add']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Purchase/index/');
			}
        $data['vendors'] = $this->db->query("select * from tblacode where general = '$aname'")->result_array();
        $data['bank_list'] = $this->db->query("select * from tblacode where general = '$bank_code'")->result_array();
       // pm($data['vendors']);
        	 if($sale_point_id=='1'){$where_cat="where classcode='2'";}else if($sale_point_id=='2'){$where_cat="where classcode='1'";}
        $data['itemss'] = $this->db->query("select * from tblmaterial_coding ")->result_array();

        $data['products'] = $this->db->query("select * from tblmaterial_coding")->result_array();
        $data['d'] =$this->mod_common->select_last_records('tbl_posting_stock')['post_date'];
		$this->load->view($this->session->userdata('language')."/Purchase/add_category",$data);
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
			redirect(SURL . 'Purchase/index/');
			}
	

	$data['vendors'] = $this->db->query("select * from tblacode where general = '$aname'")->result_array();
        $data['bank_list'] = $this->db->query("select * from tblacode where general = '$bank_code'")->result_array();


			 if($sale_point_id=='1'){$where_cat="where classcode='2'";}else if($sale_point_id=='2'){$where_cat="where classcode='1'";}
        $data['itemss'] = $this->db->query("select * from tblmaterial_coding  ")->result_array();
//
		$data['record'] = $this->db->query("select * from tbl_goodsreceiving where receiptnos='$id'")->result_array()[0];
		$data['items'] = $this->db->query("select tbl_goodsreceiving_detail.*,tblmaterial_coding.itemname from tbl_goodsreceiving_detail inner join tblmaterial_coding on materialcode=itemid where receipt_detail_id='$id'")->result_array();

//pm($data['record']);








		$this->load->view($this->session->userdata('language')."/Purchase/add_category", $data);
	}
public function add(){
//pm($this->input->post());exit;
	$login_user=$this->session->userdata('id');
$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
if (empty($this->input->post("item"))) {
	$this->session->set_flashdata('err_message1', 'Please Select Item !');

			redirect(SURL . 'Purchase/add_category');
}
if(empty($this->input->post("edit"))){
$invoice_no = $this->input->post('invoice_no');
$invoice_date = $this->input->post('invoice_date');
		$invoice =$this->db->query("select * from tbl_goodsreceiving where invoice_no='$invoice_no' and sale_point_id='$sale_point_id' and invoice_date='$invoice_date'")->row_array();
	//	pm($invoice);
if(!empty($invoice)){
$this->session->set_flashdata('err_message1', 'Invoice Number with same Date already exist!');

			redirect(SURL . 'Purchase/add_category');
}}
//echo "select * from tbl_goodsreceiving where invoice_no='$invoice_no' and invoice_date='$invoice_date'";exit();
$order_date = $this->input->post('order_date');
$closing =$this->db->query("select trans_id from tbl_posting_stock where post_date='$order_date'")->row_array();
$c= count($closing);

if ($c!=0) {
	$this->session->set_flashdata('err_message1', 'Day is Closed !');

			redirect(SURL . 'Purchase/index');
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
       
      $trans_id = $this->db->query("select max(trans_id) as trans_id from tbl_goodsreceiving where sale_point_id='$sale_point_id'")->row_array()['trans_id'];
     
      if($trans_id==''){
      	 $trans_id=1;
      	}else{
      		 $trans_id=$trans_id+1;
      	}

       }	
				
		

	
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			

			
			$v = $this->input->post("vendor");
			$net_payable = $this->input->post("net_payable");
			$total_paid = $this->input->post("total_paid");
			$invoice_balance=$net_payable-$total_paid;
			 if($invoice_balance<0){
				$invoice_balance=0.000;
			}
			$array = array(
					

					
						
	    				"suppliercode"=>$v,
	    				"total_bill"=>$this->input->post("total_bill"),
	    				"totall_vat"=>$this->input->post("totall_vat"),
	    				"totall_bill_in_vat"=>$this->input->post("totall_bill_in_vat"),
	    				"totall_bill_ex_vat"=>$this->input->post("totall_bill_ex_vat"),
	    				"tax_amount"=>$this->input->post("tax_amount"),
	    				"tax_percentage"=>$this->input->post("tax_percentage"),
	    				"discount_amt"=>$this->input->post("total_discount"),
	    				"item_disc"=>$this->input->post("item_disc"),
	    				"other_disc"=>$this->input->post("other_discount"),
	    				"receiptdate"=>$this->input->post("receiptdate"),
	    				"net_payable"=>$this->input->post("net_payable"),
	    				"remarks"=>$this->input->post("remarks"),
	    				"invoice_no"=>$this->input->post("invoice_no"),
	    				"invoice_date"=>$this->input->post("invoice_date"),
	    				"scheme_on"=>$this->input->post("scheme_on"),
	    				"total_paid"=>$this->input->post("total_paid"),
	    				"pay_mode"=>$this->input->post("payy_mode"),
	    				"bank_code"=>$this->input->post("bank_name"),
	    				"cheque_no"=>$this->input->post("cheque_no"),
	    				"cheque_dt"=>$this->input->post("cheque_date"),
	    				"invoice_balance"=>$invoice_balance,
	    				"sale_point_id"=>$sale_point_id,
	    				"trans_id"=>$trans_id,
	    				"po_no"=>0,
	    				
	    				"type"=>'PV',
						  );

			if(!empty($this->input->post("edit"))){
			
				$insertid = $this->input->post("edit");
				
				
			$this->mod_common->update_table("tbl_goodsreceiving",array("receiptnos"=>$insertid), $array);
			//echo 'asda';exit;
				$vno = $sale_point_id."-PV-".$trans_id;

	    	$this->db->query("delete from tbltrans_master where vno='$vno'");

	    	$this->db->query("delete from tbltrans_detail where vno='$vno'");
			$this->db->query("delete from tbl_goodsreceiving_detail where receipt_detail_id='$insertid'");

				
	

			}else{
			$insertid = $this->mod_common->insert_into_table("tbl_goodsreceiving",$array);

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
							

						"receipt_detail_id"=>$insertid,
	    				"itemid"=>$this->input->post("item")[$i],
	    				"item_serial"=>$this->input->post("item_serial")[$i],
	    				"quantity"=>$this->input->post("qty")[$i],
	    				"vat_amount"=>$this->input->post("disc")[$i],
	    				"rate"=>$this->input->post("unitcost")[$i],
	    				"amount"=>$this->input->post("amount")[$i],
	    				"vat_perc"=>$this->input->post("vat_perc")[$i],
	    				"vat_amt"=>$this->input->post("vat_amt")[$i],
	    				"ex_vat_amount"=>$this->input->post("amount")[$i],
	    				"category_id"=>$classcode,
						"recvd_date"=>$this->input->post("receiptdate"),
						"trans_id"=>$trans_id,
	    				"sale_point_id"=>$sale_point_id,
						"type"=>'PV',
						
							
							
						  );

			$r=	$this->mod_common->insert_into_table("tbl_goodsreceiving_detail",$array1);

				$totalamt = $this->input->post("total_bill");
				$Customer = $this->input->post("Customer");
$v = $this->input->post("vendor");
    $product = $this->input->post("item")[$i];
 $itemanem = $this->db->query("select * from tblmaterial_coding where materialcode='$product'")->result_array()[0]['itemname'];

$qty=$qty.','.$this->input->post("qty")[$i];
$totalamt=$this->input->post("total_bill");
$tamount=$this->input->post("amount")[$i];
$rate=$this->input->post("unitcost")[$i];
	   
$vno = $sale_point_id."-PV-".$trans_id;
	   
   $nar = $nar.','."$itemanem qty $qty@$rate amt=$tamount  otherdiscount=". $this->input->post("other_discount") ;
   $date = $this->input->post("receiptdate");


$i++;	}
	    $array = array(
	    				"vno"=>$vno,
	    				"vtype"=>"PV",
	    				"damount"=>$totalamt,
	    				"camount"=>$totalamt,
	    				"created_date"=>$date,
	    				"created_by"=>$login_user,
	    				"sale_point_id"=>$sale_point_id,
	    				"trans_id"=>$trans_id,
	    				"svtype"=>"WOP",
	    			  );	

	    $master_id=$this->mod_common->insert_into_table("tbltrans_master",$array);
	    $j=1;
	    $array = array(
	    				"vno"=>$vno,
	    				"srno"=>$j,
	    				"acode"=>$stock_code,//stock code will come here
	    				"damount"=>$this->input->post("totall_bill_ex_vat")-$this->input->post("other_discount"),
	    				"camount"=>"0",
	    				"remarks"=>$nar,
	    				"ig_detail_id"=>$master_id,
	    				"vtype"=>"PV",
	    				"svtype"=>"WOP",
	    				"vdate"=>$date,
	    				"sale_point_id"=>$sale_point_id,
	    				"trans_id"=>$trans_id,
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_detail",$array);
	    $j++;
	   $tax=$this->input->post("tax_amount")+$this->input->post("totall_vat");
	    	
	    	$taxnar1='Tax Paid against Purchase #:'.$insertid.',Tax Amount :'.$tax.'';
	    	$array = array(
	    				"vno"=>$vno,
	    				"srno"=>$j,
	    				"acode"=>$v,//stock code will come here
	    				"damount"=>"0",
	    				"camount"=>$tax,
	    				"remarks"=>$taxnar1,
	    				"ig_detail_id"=>$master_id,
	    				"vtype"=>"PV",
	    				"svtype"=>"WOP",
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
	    				"damount"=>"0",
	    				"camount"=>$this->input->post("totall_bill_ex_vat")-$this->input->post("other_discount"),
	    				"remarks"=>$nar,
	    				"ig_detail_id"=>$master_id,
	    				"vtype"=>"PV",
	    				"svtype"=>"WOP",
	    				"vdate"=>$date,
	    				"sale_point_id"=>$sale_point_id,
	    				"trans_id"=>$trans_id,
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_detail",$array);
	    $j++;
	    if ($this->input->post("tax_amount")>0 || $this->input->post("totall_vat")>0) {
	    	$tax=$this->input->post("tax_amount")+$this->input->post("totall_vat");
	    	
	    	$taxnar1='Tax Paid against Purchase #:'.$insertid.',Tax Amount :'.$tax.'';
	    	   $array = array(
	    				"vno"=>$vno,
	    				"srno"=>$j,
	    				"acode"=>$tax_receive,
	    				"damount"=>$tax,
	    				"camount"=>"0",
	    				"remarks"=>$taxnar1,
	    				"ig_detail_id"=>$master_id,
	    				"vtype"=>"PV",
	    				"svtype"=>"WOP",
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
	    				"damount"=>$enter_amount,
	    				"camount"=>"0",
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
	    				"damount"=>"0",
	    				"camount"=>$enter_amount,
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
				
//echo $insertid;exit();
//$this->session->set_flashdata('ok_message', 'Insert Successfully!');
redirect(SURL."Purchase/detail_invoice/".$insertid);

			

	    }
	    	function del()
	{
	    
		$id=	$this->input->post('id');


$i_d =$this->db->query("select packing,p_in_corton from tblmaterial_coding where materialcode='$id'")->row_array();
$packing = $i_d['packing'];
$p_in_corton = $i_d['p_in_corton'];


if (!empty($packing)) {
			echo $packing . 'L * ' . $p_in_corton.' CRTNS';
				
?>

<?php
	}	 }


	public function delete($id) {
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '603' limit 1")->row_array();
		if ($role['delete']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Purchase/index/');
			}
		$query = $this->db->query("delete from tbl_goodsreceiving where trans_id='$id' and sale_point_id='$sale_point_id'");
		$query = $this->db->query("delete from tbl_goodsreceiving_detail where trans_id='$id' and sale_point_id='$sale_point_id'");
		$vno=$sale_point_id."-PV-".$id;
		$this->db->query("delete from tbltrans_master where vno='$vno'");
	    $this->db->query("delete from tbltrans_detail where vno='$vno'");

        if ($query) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Purchase/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Purchase/');
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
		echo number_format($acc_balance,3);

	}
	public function detail_invoice($id){
 //echo $id;exit();
		if($id){

		//$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
		$con['conditions']=array("receiptnos"=>$id);
		//$data['master_record'] = $this->common->get_single_row("tbl_goodsreceiving",$con);
		 $data['master_record'] = $this->db->query("select  * from  tbl_goodsreceiving where receiptnos='$id'")->row_array();
        $data['res_record'] = $this->db->query("select  * from  tbl_company")->row_array();
         
        
        

		$data['detail_record'] = $this->db->query("SELECT tbl_goodsreceiving_detail.*,tblmaterial_coding.itemname FROM `tbl_goodsreceiving_detail` inner join tblmaterial_coding on tbl_goodsreceiving_detail.itemid=tblmaterial_coding.materialcode where receipt_detail_id='$id'")->result_array();
		//pm($data['master_record']);
		$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];

		
		$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");
		//exit;
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Customer Invoice";
		$this->load->view($this->session->userdata('language')."/Purchase/detail_invoice",$data);
		}
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
