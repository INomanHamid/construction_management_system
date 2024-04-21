<?php  defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_po extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_category","mod_common"
        ));
        $this->load->library('Uploadimage');
        
    }

	public function index()
	{
		
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

	

		$data['category_list'] = $this->db->query("select tbl_goodsreceiving.*,tblacode.aname from tbl_goodsreceiving inner join tblacode on acode=suppliercode  where tbl_goodsreceiving.type='PV' and tbl_goodsreceiving.receiptdate  between '$from_date' and '$to_date' $where_sale_point_id and tbl_goodsreceiving.po_no!='' ORDER BY tbl_goodsreceiving.receiptnos DESC")->result_array();
		// echo "select tbl_goodsreceiving.*,tblacode.aname from tbl_goodsreceiving inner join tblacode on acode=suppliercode  where tbl_goodsreceiving.type='PV' and tbl_goodsreceiving.receiptdate  between '$from_date' and '$to_date' $where_sale_point_id and tbl_goodsreceiving.po_no!=''";exit(); 

		$data["title"] = "Manage Purchase With PO";	
		 $data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];		

		$this->load->view($this->session->userdata('language')."/Purchase_po/manage_category",$data);
	} 
	 function get_p_price()
	{
	    
	 	$id=	$this->input->post('id');
		$po_no=	trim($this->input->post('po_no'));
		//echo $po_no;

		
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		
		$qty =$this->db->query("select qty as qty from tbl_po_detail  where itemcode='$id' and po_no='$po_no' and sale_point_id='$sale_point_id'")->row_array()['qty'];
		// echo "select qty as qty from tbl_po_detail  where itemcode='$id' and po_no='$po_no' and sale_point_id='$sale_point_id'";exit();
		$recv_qty =$this->db->query("select sum(quantity) as quantity from tbl_goodsreceiving_detail  where itemid='$id' and po_no='$po_no' and sale_point_id='$sale_point_id'")->row_array()['quantity'];
		$unit_price =$this->db->query("select unit_price as unit_price from tbl_po_detail  where itemcode='$id' and po_no='$po_no' and sale_point_id='$sale_point_id'")->row_array()['unit_price'];
		$discount =$this->db->query("select discount as discount from tbl_po_detail  where itemcode='$id' and po_no='$po_no' and sale_point_id='$sale_point_id'")->row_array()['discount'];
		$gst =$this->db->query("select gst as gst from tbl_po_detail  where itemcode='$id' and po_no='$po_no' and sale_point_id='$sale_point_id'")->row_array()['gst'];
		
		
		
$unitcost=number_format($unit_price,3);
$disc=number_format($discount,3);

	echo	$qty.'_'.$recv_qty.'_'.$unitcost.'_'.$disc.'_'.$gst;										 
          
			
		 }
		  function get_p_date()
	{
	    
	 	$po_no=	trim($this->input->post('po_no'));
		 $po_date =$this->db->query("select po_date from tbl_po  where po_no='$po_no'")->row_array()['po_date'];
	      echo	$po_date;										 
          
			
		 }
	public function add_category()
	{
		 $data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];	
		$login_user=$this->session->userdata('id');
		 $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		  if ($sale_point_id=='0') {
	  	$this->session->set_flashdata('err_message', 'Admin Has No Rights To Add Purchase With Purchase Order.');
			redirect(SURL . 'Purchase');
			exit();
	  }
      $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
       $aname=$fix_code['vendor_code'];
		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '603' limit 1")->row_array();
		if ($role['add']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Purchase_po/index/');
			}
        $data['vendors'] = $this->db->query("select * from tblacode where general = '$aname'")->result_array();
        $data['order_list'] = $this->db->query("SELECT * FROM `tbl_po` WHERE po_no NOT IN (SELECT po_no FROM tbl_goodsreceiving WHERE po_status='Complete' ) and sale_point_id='$sale_point_id'")->result_array();
       // pm($data['vendors']);
        	$data['itemss'] = $this->db->query("select * from tblmaterial_coding")->result_array();

        $data['products'] = $this->db->query("select * from tblmaterial_coding")->result_array();
        $data['d'] =$this->mod_common->select_last_records('tbl_posting_stock')['post_date'];
		$this->load->view($this->session->userdata('language')."/Purchase_po/add_category",$data);
	}

	public function edit($id){
		 $data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];	
		$login_user=$this->session->userdata('id');
		 $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
      $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
       $aname=$fix_code['vendor_code'];
		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '603' limit 1")->row_array();
		if ($role['edit']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Purchase_po/index/');
			}
	

	$data['vendors'] =$this->db->query("select * from tblacode where general = '$aname'")->result_array();

			$data['itemss'] = $this->db->query("select * from tblmaterial_coding")->result_array();
			$data['order_list'] = $this->db->query("SELECT * FROM `tbl_po` where sale_point_id=$sale_point_id")->result_array();
//
		$data['record'] = $this->db->query("select * from tbl_goodsreceiving where receiptnos='$id'")->result_array()[0];
		$data['items'] = $this->db->query("select tbl_goodsreceiving_detail.*,tblmaterial_coding.itemname from tbl_goodsreceiving_detail inner join tblmaterial_coding on materialcode=itemid where receipt_detail_id='$id'")->result_array();

//pm($data['items']);








		$this->load->view($this->session->userdata('language')."/Purchase_po/add_category", $data);
	}
public function add(){
//pm($this->input->post());exit;

if (empty($this->input->post("item"))) {
	$this->session->set_flashdata('err_message1', 'Please Select Item !');

			redirect(SURL . 'Purchase_po/add_category');
}

//echo "select * from tbl_goodsreceiving where invoice_no='$invoice_no' and invoice_date='$invoice_date'";exit();
$order_date = $this->input->post('order_date');
$closing =$this->db->query("select trans_id from tbl_posting_stock where post_date='$order_date'")->row_array();
$c= count($closing);

if ($c!=0) {
	$this->session->set_flashdata('err_message1', 'Day is Closed !');

			redirect(SURL . 'Purchase_po/index');
}
//  $receiptdate = $this->input->post('receiptdate');
//  $invoice_date = $this->input->post('invoice_date');

//  $po_no = $this->input->post('po_no');
// $po_date =$this->db->query("select po_date from tbl_po where po_no='$po_no'")->row_array()['po_date'];
// //echo $po_date;exit();
// if ($receiptdate<$po_date) {
// 	$this->session->set_flashdata('err_message1', 'Date must be greater or equal to purchase order date !');

// 			redirect(SURL . 'Purchase_po/add_category');
// }
// if ($invoice_date<$po_date) {
// 	$this->session->set_flashdata('err_message1', 'Purchase Invoice Date must be greater or equal to purchase order date !');

// 			redirect(SURL . 'Purchase_po/add_category');
// }




$amt = $this->input->post('rec_amt');
$id = $this->input->post('Customer');
$login_user=$this->session->userdata('id');
$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
$fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
$stock_code=$fix_code['stock_code'];
$tax_code=$fix_code['tax_receive'];


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
			$array = array(
				        "suppliercode"=>$this->input->post("vendor"),
	    				"total_bill"=>$this->input->post("total_bill"),
	    				"receiptdate"=>$this->input->post("receiptdate"),
	    				"net_payable"=>$this->input->post("net_payable"),
	    				"remarks"=>$this->input->post("remarks"),
	    				"invoice_no"=>$this->input->post("invoice_no"),
	    				"invoice_date"=>$this->input->post("invoice_date"),
	    				"scheme_on"=>$this->input->post("scheme_on"),
	    				"po_no"=>$this->input->post("po_no"),
	    				"po_status"=>$this->input->post("po_status"),
	    				"tax_amount"=>$this->input->post("tax_amount"),
	    				//"discount_amt"=>$this->input->post("total_disc"),
	    				"tax_percentage"=>$this->input->post("tax_percentage"),
	    				
	    				"sale_point_id"=>$sale_point_id,
	    				"trans_id"=>$trans_id,
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
			$tot_gst_amount=0;
			
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
	    				"po_no"=>$this->input->post("po_no"),
	    				"po_qty"=>$this->input->post("po_qty")[$i],
	    				"recv_qty"=>$this->input->post("recv_qty")[$i],
	    				"quantity"=>$this->input->post("qty")[$i],
	    				"rate"=>$this->input->post("unitcost")[$i],
	    				"amount"=>$this->input->post("amount")[$i],
	    				"item_serial"=>$this->input->post("item_serial")[$i],
	    				"inc_vat_amount"=>$this->input->post("amount")[$i],
	    				"category_id"=>$classcode,
	    				"recvd_date"=>$this->input->post("receiptdate"),
	    				"gstp"=>$this->input->post("gst")[$i],
	    				"vat_amount"=>$this->input->post("discount")[$i],
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
$totalamt=$this->input->post("total_payable");
$quantity=$this->input->post("qty")[$i];
$rate=$this->input->post("unitcost")[$i];
$gstp=$this->input->post("gst")[$i];
$discount=$this->input->post("discount")[$i];
$rate_dist=$rate-$discount;
$tot_amount=$quantity*$rate_dist;
$gst_amount=$tot_amount*$gstp/100;
$tot_gst_amount=$this->input->post("tax_amount");

$tamount=$this->input->post("amount")[$i];
$rate=$this->input->post("unitcost")[$i];
	   
$vno = $sale_point_id."-PV-".$trans_id;
	   
   $nar = $nar.','."$itemanem qty $qty@$rate amt=$tamount";
   $date = $this->input->post("receiptdate");
   $login_user=$this->session->userdata('id');


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
	    				"svtype"=>"WPO",
	    			  );	

	    $master_id=$this->mod_common->insert_into_table("tbltrans_master",$array);
	    $j=1;
	    $array = array(
	    				"vno"=>$vno,
	    				"ig_detail_id"=>$master_id,
	    				"srno"=>$j,
	    				"acode"=>$stock_code,//stock code will come here
	    				"damount"=>$totalamt,
	    				"camount"=>"0",
	    				"remarks"=>$nar,
	    				"vtype"=>"PV",
	    				"svtype"=>"WPO",
	    				"vdate"=>$date,
	    				"sale_point_id"=>$sale_point_id,
	    				"trans_id"=>$trans_id,
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_detail",$array);
	    $j++;

	    $array = array(
	    				"vno"=>$vno,
	    				"srno"=>$j,
	    				"ig_detail_id"=>$master_id,
	    				"acode"=>$v,
	    				"damount"=>"0",
	    				"camount"=>$totalamt,
	    				"remarks"=>$nar,
	    				"vtype"=>"PV",
	    				"svtype"=>"WPO",
	    				"vdate"=>$date,
	    				"sale_point_id"=>$sale_point_id,
	    				"trans_id"=>$trans_id,
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_detail",$array);
	    if($tot_gst_amount>0){

	  
	    $j++;
	     $array = array(
	    				"vno"=>$vno,
	    				"ig_detail_id"=>$master_id,
	    				"srno"=>$j,
	    				"acode"=>$tax_code,//stock code will come here
	    				"damount"=>$tot_gst_amount,
	    				"camount"=>"0",
	    				"remarks"=>$nar,
	    				"vtype"=>"PV",
	    				"svtype"=>"WPO",
	    				"vdate"=>$date,
	    				"sale_point_id"=>$sale_point_id,
	    				"trans_id"=>$trans_id,
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_detail",$array);
	    $j++;

	    $array = array(
	    				"vno"=>$vno,
	    				"srno"=>$j,
	    				"ig_detail_id"=>$master_id,
	    				"acode"=>$v,
	    				"damount"=>"0",
	    				"camount"=>$tot_gst_amount,
	    				"remarks"=>$nar,
	    				"vtype"=>"PV",
	    				"svtype"=>"WPO",
	    				"vdate"=>$date,
	    				"sale_point_id"=>$sale_point_id,
	    				"trans_id"=>$trans_id,
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_detail",$array);
	    $j++;
	}

}
				

$this->session->set_flashdata('ok_message', 'Insert Successfully!');

			redirect(SURL . 'Purchase_po/');

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
		//echo $id;exit();
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '603' limit 1")->row_array();
		if ($role['delete']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Purchase_po/index/');
			}
		$query = $this->db->query("delete from tbl_goodsreceiving where trans_id='$id' and sale_point_id='$sale_point_id'");
		$query = $this->db->query("delete from tbl_goodsreceiving_detail where trans_id='$id' and sale_point_id='$sale_point_id'");

		$vno=$sale_point_id."-PV-".$id;
		$this->db->query("delete from tbltrans_master where vno='$vno'");
	    $this->db->query("delete from tbltrans_detail where vno='$vno'");

        if ($query) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Purchase_po/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Purchase_po/');
        }
    }

   public function get_vendor(){
		$po_no = $this->input->post("po_no");
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		
		$record = $this->db->query("select * from tblacode where acode in(select vendor_code from tbl_po where po_no='$po_no' and sale_point_id='$sale_point_id')")->result_array();?>
		<?php
		foreach ($record as $key => $data) {
		 	
										
			?>
            
			<option   value="<?php echo $data['acode']; ?>"><?php echo ucwords($data['aname']); ?></option>
		<?php } ?>
	
<?php	

	}

public function get_item(){
		$po_no = $this->input->post("po_no");
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		
		$record = $this->db->query("select * from tblmaterial_coding where materialcode in(select itemcode from tbl_po_detail where po_no='$po_no' and sale_point_id='$sale_point_id')")->result_array();
		?>
		<?php
		foreach ($record as $key => $data) {
		 	
										
			?>
            
			<option   value="<?php echo $data['materialcode']; ?>"><?php echo ucwords($data['itemname']." ".$data['bar_code']." ".$data['short_code']); ?></option>
		<?php } ?>
	
<?php	

	}
	

}


