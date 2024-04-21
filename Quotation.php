<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quotation extends CI_Controller {
 
	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_transaction","mod_common","mod_admin","mod_customerledger","common"
        ));
        
    }

	public function index()
	{
		if(isset($_POST['submit'])){			
			$from_date=$data["from_date"] = date("Y-m-d", strtotime($_POST['from']));
			
			$to_date=$data["to_date"] = date("Y-m-d", strtotime($_POST['to']));
			
		}else{
			$from_date=$data["from_date"] = date('Y-m-d', strtotime('-7 day'));
			$to_date =$data["to_date"]= date('Y-m-d');
		}

		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
         if($sale_point_id==''){
       $where_sale_point_id="and isnull(sale_point_id)";
        }else{
       $where_sale_point_id="and sale_point_id='$sale_point_id'";
        }
		
		$data['query']=$this->db->query("SELECT * FROM tbl_quotation where status='Completed' $where_sale_point_id order by quot_no desc ")->result_array();
		//spm($data['query']);exit();
		$data['aname'] = $this->db->query("select aname from tblacode where general='1001001000'")->result_array();
		
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Quotation";
			  $data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];	
		$this->load->view($this->session->userdata('language')."/Quotation/manage_quotation",$data);
	}


	public function add()
	{
       $login_user=$this->session->userdata('id');
	   $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
	    if ($sale_point_id=='0') {
	  	$this->session->set_flashdata('err_message', 'Admin Has No Rights To Add Quotation.');
			redirect(SURL . 'Quotation');
			exit();
	  }
	   $data['stock_check'] = $this->db->query("select stock from tbl_company ")->row_array()['stock'];
       $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
       $customer=$fix_code['customer_code'];
       $sales_code=$fix_code['sales_code'];
       $cash_code=$fix_code['cash_code'];
        $tax_pay=$fix_code['tax_pay'];
     
       
		$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
		$data['item_list'] = $this->db->query("select * from tblmaterial_coding")->result_array();

		$data['aname'] = $this->db->query("select * from tblacode where general='$customer'")->result_array();
		if($this->input->server('REQUEST_METHOD') == 'POST'){
		 $login_user=$this->session->userdata('id');
	   $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		 $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
       $customer=$fix_code['customer_code'];
       $sales_code=$fix_code['sales_code'];
       $cash_code=$fix_code['cash_code'];
       $trans_id=$this->input->post("trans_id");
       if ($trans_id=='') {
       
      $trans_id = $this->db->query("select max(trans_id) as trans_id from tbl_quotation where sale_point_id='$sale_point_id'")->row_array()['trans_id'];
     
      if($trans_id==''){
      	 $trans_id=1;
      	}else{
      		 $trans_id=$trans_id+1;
      	}

       }
		//echo $trans_id;exit;
     //pm($this->input->post());
		$this->db->trans_start();
		 $created_date= date('Y-m-d');
		 $type = $this->input->post("type");
		 $print = $this->input->post("print");

		 	if($type=='Credit'){
		 $acode = $this->input->post("customer");
		 $customer = $this->db->query("select aname from tblacode where acode='$acode'")->row_array()['aname'];
	}
	else{
		 $customer = $this->input->post("customer_cash");
	}
	
			
$balance=$this->input->post("totalamtafterdis")-$this->input->post("cash_recvd");
		 date_default_timezone_set("Asia/Muscat");
		 $order_time=date("h:i:sa");

		 $invoice=$this->input->post("print_edit");
		 if ($invoice=='') {
		 	$invoice = $this->db->query("SELECT max(invoice)  as invoice FROM  tbl_quotation")->row_array();
		 	if ($invoice=='') {
		 		$invoice="1";
		 	}else{
		 		$invoice=$invoice['invoice']+1;
		 	}
		 }
//echo $this->input->post("discount");exit();
 
		$array= array('total_amount' => $this->input->post("totalamt"),
					  'rcv_amt' =>$this->input->post("cash_recvd") ,
					  'change_cash' =>$this->input->post("change") ,
					  'disc_amt' =>$this->input->post("discount") ,
					   'issuedto' =>$this->input->post("customer") ,
					   'remarks' =>$this->input->post("remarks") ,
					  'cname' =>$customer ,
					  'party_type' =>$this->input->post("type"),
					  'car_no' =>0,
					  'c_phone' =>0,
					  'c_street' =>0,
					  'order_type' =>0,
					  'order_time' => $order_time ,
					   'order_date' => date("Y-m-d"),
					   'trip_id' => 0,
					     'tcode' => 0,
					    'status' => 'Completed',
						'balance' => $balance,
						'sync_status' =>'No',
					  "total_amt_without_disc"=>$this->input->post("totalamtafterdis"),
					  "rate_type"=>$this->input->post("rate_type"),
					  "address"=>$this->input->post("address"),
					  "c_phone"=>$this->input->post("c_phone"),
					  "vat_amount"=>$this->input->post("vat_amount"),
					  "vat_percentage"=>$this->input->post("vat_percentage"),
					  'invoice' =>$invoice,
					  "sale_point_id"=>$sale_point_id,
	    				"trans_id"=>$trans_id,
	    				 "net_payable"=>$this->input->post("net_amount"),
					  
					  
					);
		//pm($array);exit();

		if(!empty($this->input->post("edit"))){
           $con['conditions']=array("quot_no"=>$this->input->post("edit"));
			
			$this->common->update("tbl_quotation",$array,$con);
			$this->common->delete("tbl_quotation_detail",array("ig_detail_id"=>$this->input->post("edit")));

			$insert = $this->input->post("edit");
			
			}else{
				
			$insert = $this->common->insert("tbl_quotation",$array);
		}
		$i=0;
		$c_date = date('Y-m-d');
		foreach ($this->input->post("levelid") as $key => $value) {
			
	$array= array('ig_detail_id' => $insert,
					  'levelid' =>$this->input->post("levelid")[$i],
					  'qty' =>$this->input->post("qty")[$i],
					  'sprice' =>$this->input->post("price")[$i],
					  'total_amount' => $this->input->post("tprice")[$i],
					  'created_date' => $c_date,
					  "trans_id"=>$trans_id,
	    			   "sale_point_id"=>$sale_point_id,
					);

			$this->common->insert("tbl_quotation_detail",$array);
			$i++;
		}
		
	
			
			
			$this->db->trans_complete();
			
			//redirect(SURL."Quotation/detail_invoice/".$insert);
			

			
			if ($insert) {
			 	if(!empty($this->input->post("edit"))){
			 	$this->session->set_flashdata('ok_message', 'Updated Successfully!');
	            redirect(SURL . 'Quotation/');
	        }else{
	        	$this->session->set_flashdata('ok_message', 'Added Successfully!');
	            redirect(SURL . 'Quotation/');
	            
	        }
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'Quotation/');
	        }
	    	

}
       
        $data["title"] = "Add Quotation";    			
		$this->load->view($this->session->userdata('language')."/Quotation/add",$data);
	}

	public function edit($id)
	{
		$login_user=$this->session->userdata('id');
	    $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $customer=$fix_code['customer_code'];
	    $data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
		$data['aname'] = $this->db->query("select * from tblacode where general='$customer'")->result_array();
		$con['conditions']=array("quot_no"=>$id);
		$data['master_record'] = $this->common->get_single_row("tbl_quotation",$con);
		
 

		$data['detail_record'] = $this->db->query("SELECT tbl_quotation_detail.*,tblmaterial_coding.itemname FROM `tbl_quotation_detail` inner join tblmaterial_coding on tbl_quotation_detail.levelid=tblmaterial_coding.materialcode where ig_detail_id='$id'")->result_array();

//echo "<pre>";var_dump($data['detail_record']);exit();
		//pm($data['edit_list']);exit();


        $data["filter"] = 'add';
        $data["title"] = "Edit Cash Payment";    			
		$this->load->view($this->session->userdata('language')."/Quotation/add",$data);
	}

	

		public function delete($id) {
			//echo $id;exit();
        
		$this->db->trans_start();
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

        $table = "tbl_quotation";
        $where = "trans_id = '" . $id . "' and sale_point_id = '" . $sale_point_id . "'";
       	$delete = $this->mod_common->delete_record($table, $where);

        $table = "tbl_quotation_detail";
        $where = "trans_id = '" . $id . "' and sale_point_id = '" . $sale_point_id . "'";
        $delete = $this->mod_common->delete_record($table, $where);
    
        // $table = "tbltrans_master";
        // $where = "vno = '".$vno."'";
        // $delete = $this->mod_common->delete_record($table, $where);

        // $table = "tbltrans_detail";
        // $where = "vno = '".$vno."'";
        // $delete = $this->mod_common->delete_record($table, $where);

		$this->db->trans_complete();

		if ($this->db->trans_status() === TRUE)
		{
		    $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Quotation/');
		}else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Quotation/');
        }
    }
    	public function invoice($id){
		//echo $id;exit;
		$con['conditions']=array("quot_no"=>$id);
		$data['master_record'] = $this->common->get_single_row("tbl_quotation",$con);
        $data['res_record'] = $this->db->query("select  * from  tbl_company")->row_array();
        // $data['record'] =  $this->db->query("select * from tbldeal_config_detail")->result_array();
        
        

		$data['detail_record'] = $this->db->query("SELECT tbl_quotation_detail.*,tblmaterial_coding.itemname FROM `tbl_quotation_detail` inner join tblmaterial_coding on tbl_quotation_detail.levelid=tblmaterial_coding.materialcode where ig_detail_id='$id'")->result_array();
		$data['aname'] = $this->db->query("select * from tblacode where general='1001001000'")->result_array();
		//echo "<pre>"; var_dump($data['detail_record']);
		
		//$data['deal_record'] = $this->db->query("select * from tblmaterial_coding inner join tblorder_temp on tblmaterial_coding.materialcode = tblorder_temp.item_desc order by tblorder_temp.order_id")->result_array();
		 //echo "<pre>"; var_dump($data['detail_record']);exit();

		$this->load->view($this->session->userdata('language')."/Quotation/invoice",$data);
	}
	public function detail_invoice($id){
	
		if($id){
		//$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
		$con['conditions']=array("quot_no"=>$id);
	
		$data['master_record'] = $this->db->query("select  * from  tbl_quotation where quot_no='$id'")->row_array();
		$data['terms'] = $this->db->query("select  * from  tbl_term_condition")->result_array();
		 //pm($data['terms']);
        $data['res_record'] = $this->db->query("select  * from  tbl_company")->row_array();
         $data['record'] =  $this->db->query("select * from tbl_quotation")->result_array();
        
        

		$data['detail_record'] = $this->db->query("SELECT tbl_quotation_detail.*,tblmaterial_coding.itemname FROM `tbl_quotation_detail` inner join tblmaterial_coding on tbl_quotation_detail.levelid=tblmaterial_coding.materialcode where ig_detail_id='$id'")->result_array();
			// pm($data['detail_record']);
		$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];

		
		$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");
		//exit;
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Customer Invoice";
		$this->load->view($this->session->userdata('language')."/Quotation/detail_invoice",$data);
		}
	}
	public function get_data()
	{
		$customer=$this->input->post("customer");
		$record=$this->db->query("select * from tblacode where acode='$customer'")->row_array();
		echo json_encode($record);
	}
	public function get_stock()
	{
		$levelid=$this->input->post("levelid");
		$login_user=$this->session->userdata('id');
	    $location = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
	    $to_date=date('Y-m-d');
	    $stock = $this->mod_common->stock($levelid,$to_date,$location);
	    echo $stock;
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

}
