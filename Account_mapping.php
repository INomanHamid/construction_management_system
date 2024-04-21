<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account_mapping extends CI_Controller {


	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_vendor","mod_common","mod_bulkpurchase","mod_salelpg","mod_bank","mod_admin","mod_vendorledger"
        ));
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

		  $data['account_mapping'] = $this->db->query("select * from tbl_sales_point inner join tbl_code_mapping on tbl_sales_point.sale_point_id = tbl_code_mapping.sale_point_id where tbl_code_mapping.created_dt between '$from_date' and '$to_date' order by tbl_code_mapping.trans_id desc")->result_array();


	  $data['customer_list'] = $this->db->query("select * from tblacode where atype='Child' and general='2002004000'")->result_array()[0];
	  //pm($data['customer_list']);
$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
		$this->load->view($this->session->userdata('language')."/Account_mapping/bulkpurchase",$data);
	}

	public function add_bulkpurchase()
	{

		
		  $data['tank_list'] = $this->db->query("select * from tbl_sales_point where sts='Active' and sale_point_id not in (select sale_point_id from tbl_code_mapping)")->result_array();  
//	  pm($data['tank_list'] );exit;

		   $data['cash_code'] = $this->db->query("select * from tblacode where atype='Child' and acode not in(select cash_code from tbl_code_mapping )")->result_array();
		   $data['stock_code'] = $this->db->query("select * from tblacode where atype='Child' and acode not in(select stock_code from tbl_code_mapping )")->result_array();
		   $data['tax_pay'] = $this->db->query("select * from tblacode where atype='Child' and acode not in(select tax_pay from tbl_code_mapping )")->result_array();
		   $data['tax_receive'] = $this->db->query("select * from tblacode where atype='Child' and acode not in(select tax_receive from tbl_code_mapping )")->result_array();
		   $data['sales_code'] = $this->db->query("select * from tblacode where atype='Child' and acode not in(select sales_code from tbl_code_mapping )")->result_array();
		   $data['cost_of_goods_code'] = $this->db->query("select * from tblacode where atype='Child' and acode not in(select cost_of_goods_code from tbl_code_mapping )")->result_array();
		   $data['bulk_sales_code'] = $this->db->query("select * from tblacode where atype='Child' and acode not in(select bulk_sales_code from tbl_code_mapping )")->result_array();
		  
		   $data['customer_code'] = $this->db->query("select * from tblacode where atype='Parent' and acode not in(select customer_code from tbl_code_mapping )")->result_array();
		   $data['vendor_code'] = $this->db->query("select * from tblacode where atype='Parent' and acode not in(select vendor_code from tbl_code_mapping )")->result_array();
		   $data['bank_code'] = $this->db->query("select * from tblacode where atype='Parent' and acode not in(select bank_code from tbl_code_mapping )")->result_array();
		   $data['expense_code'] = $this->db->query("select * from tblacode where atype='Parent' and acode not in(select expense_code from tbl_code_mapping )")->result_array();
		   $data['customer_code'] = $this->db->query("select * from tblacode where atype='Parent' and acode not in(select customer_code from tbl_code_mapping )")->result_array();
		     	$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];	 

		$this->load->view($this->session->userdata('language')."/Account_mapping/add_bulkpurchase",$data);
	}

	public function edit($id)
	{
		
             $data['tank_list'] = $this->db->query("select * from tbl_sales_point where sale_point_id='$id'")->result_array();

           $data['cash_code'] = $this->db->query("select * from tblacode where atype='Child' ")->result_array();
		   $data['stock_code'] = $this->db->query("select * from tblacode where atype='Child' ")->result_array();
		   $data['tax_pay'] = $this->db->query("select * from tblacode where atype='Child' ")->result_array();
		   $data['tax_receive'] = $this->db->query("select * from tblacode where atype='Child' ")->result_array();
		   $data['sales_code'] = $this->db->query("select * from tblacode where atype='Child' ")->result_array();
		   $data['cost_of_goods_code'] = $this->db->query("select * from tblacode where atype='Child' ")->result_array();
		   $data['bulk_sales_code'] = $this->db->query("select * from tblacode where atype='Child' ")->result_array();
		  
		   $data['customer_code'] = $this->db->query("select * from tblacode where atype='Parent' ")->result_array();
		   $data['vendor_code'] = $this->db->query("select * from tblacode where atype='Parent' ")->result_array();
		   $data['bank_code'] = $this->db->query("select * from tblacode where atype='Parent' ")->result_array();
		   $data['expense_code'] = $this->db->query("select * from tblacode where atype='Parent' ")->result_array();
		   $data['customer_code'] = $this->db->query("select * from tblacode where atype='Parent' ")->result_array();
		    $data['record'] = $this->db->query("select * from tbl_code_mapping where trans_id='$id'")->result_array()[0];   
		// pm($data['record']);    		 
$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
		$this->load->view($this->session->userdata('language')."/Account_mapping/add_bulkpurchase",$data);
	}

	public function add(){

		//pm($this->input->post());exit;

		$login_user=$this->session->userdata('id');
        $array=array(
        	        "sale_point_id"=>$this->input->post("sale_point_id"),
					"cash_code"=>$this->input->post("cash_code"),
					"tax_pay"=>$this->input->post("tax_pay"),
					"tax_receive"=>$this->input->post("tax_receive"),
					"customer_code"=>$this->input->post("customer_code"),
					"vendor_code"=>$this->input->post("vendor_code"),
					"sales_code"=>$this->input->post("sales_code"),
					"stock_code"=>$this->input->post("stock_code"),
					"bank_code"=>$this->input->post("bank_code"),
					"expense_code"=>$this->input->post("expense_code"),
					"cost_of_goods_code"=>$this->input->post("cost_of_goods_code"),
					"frieght_code"=>$this->input->post("frieght_code"),
					"bulk_sales_code"=>$this->input->post("bulk_sales_code"),
					"transporter_code"=>$this->input->post("transporter_code"),
					"created_by"=>$login_user,
					"created_dt"=>date('Y-m-d'),
					
					
					
			   );

// pm($array);exit();
		$this->db->trans_start();

		if(empty($this->input->post("edit"))){
		    
			$last_id = $this->mod_common->insert_into_table("tbl_code_mapping", $array);
		 
		}else{
		    
		      //pm($this->input->post());exit();
			$last_id = $this->input->post("edit");
             $this->mod_common->update_table("tbl_code_mapping",array("trans_id"=>$last_id), $array);
           	
		}
		
	
		
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->session->set_flashdata('err_message', '- Error in adding please try again!');
            redirect(SURL . 'Account_mapping/');
        }else{
        	$this->session->set_flashdata('ok_message', 'Added Successfully!');
            redirect(SURL . 'Account_mapping/');
        }
		}
public function delete($id) {
			//echo $id;exit();

		$this->db->trans_start();
        $table = "tbl_code_mapping";
        $where = array("trans_id"=>$id);
       	$delete = $this->mod_common->delete_record($table, $where);

        
		$this->db->trans_complete();

		if ($this->db->trans_status() === TRUE)
		{
		    $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Account_mapping/');
		}else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Account_mapping/');
        }
    }


	
	
}
