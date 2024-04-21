<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Journal_voucher extends CI_Controller {
 
	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_common","mod_voucher"
        ));
        
    }

	public function index()
	{
		$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
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

	   
		$data['paymentreceipt_list'] = $this->db->query("select * from tbltrans_master where vtype='JV' and vno!='0-JV-1' and created_date between '$from_date' and '$to_date' $where_sale_point_id order by masterid  desc")->result_array();
		
 		//pm($data['paymentreceipt_list']);

		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Journal Vouchers";	
		$this->load->view($this->session->userdata('language')."/Journal_voucher/manage_paymentreceipt",$data);
	}


	public function add()
	{
		
        $data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$login_user=$this->session->userdata('id');
       $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
       $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $cash_code=$fix_code['cash_code'];
			

		//	pm($this->input->post());
			$this->db->trans_start();
			
			if(empty($this->input->post("acode"))){
				 $this->session->set_flashdata('err_message', 'Something went wrong.');
	             redirect(SURL . 'Journal_voucher/');
			}
			$totaldebit = array_sum($this->input->post("debit"));
	     	$totalcredit = array_sum($this->input->post("credit"));
		

			

			$array = array(
				            "vno"=>$this->input->post("transcode"),
							"vtype"=>"JV",
							"damount"=>$totaldebit,
							"camount"=>$totalcredit,
							"created_date"=>$this->input->post("date"),
							"sale_point_id"=>$sale_point_id,
						  );

			if(!empty($this->input->post("edit"))){

				$insert = $this->input->post("edit");
				$this->mod_common->update_table("tbltrans_master",array("masterid"=>$insert), $array);
				$this->db->query("delete from tbltrans_detail where ig_detail_id='$insert'");
				 $vno =$this->input->post("transcode");
			}else{
				$insert = $this->mod_common->insert_into_table("tbltrans_master", $array);
				 $vno =$this->input->post("transcode");
			}
			

			//$this->db->query("update tbltrans_master set vno='$vno' where masterid='$insert'");
			$transid=explode('-', $vno);
			$trans_id=$transid[2];


			$i=0;$j=1;
			foreach ($this->input->post("acode") as $key => $value) {
				

				$array = array(
							"vno"=>$vno,
							"ig_detail_id"=>$insert,
							"srno"=>$j,
							"acode"=>$this->input->post("acode")[$i],
						    "damount"=>$this->input->post("debit")[$i],
							"camount"=>$this->input->post("credit")[$i],
							"remarks"=>$this->input->post("remarks")[$i],
							"vtype"=>"JV",
							"trans_id"=>$trans_id,
							"sale_point_id"=>$sale_point_id,
							"vdate"=>$this->input->post("date")
						  );

				$add =$this->mod_common->insert_into_table("tbltrans_detail", $array);
	
          $i++;$j++;
			}

			
			
			$this->db->trans_complete();

			
			if ($add) {
			 	if(!empty($this->input->post("edit"))){
			 	$this->session->set_flashdata('ok_message', 'Updated Successfully!');
	            redirect(SURL . 'Journal_voucher/');
	        }else{
	        	$this->session->set_flashdata('ok_message', 'Added Successfully!');
	            redirect(SURL . 'Journal_voucher/add');
	        }
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'Journal_voucher/');
	        }
	    }
	 //    $login_user=$this->session->userdata('id');
		// $role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '321' limit 1")->row_array();
		// if ($role['add']!=1) {
		// 	$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
		// 	redirect(SURL . 'Journal_voucher/index/');
		// 	}
	    $login_user=$this->session->userdata('id');
       $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
       $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
       $customer_code=$fix_code['customer_code'];
        $vendor_code=$fix_code['vendor_code'];
        $cash_code=$fix_code['cash_code'];
        $sale_point_id=$fix_code['sale_point_id'];
        $expense_code=$fix_code['expense_code'];
         //   if($sale_point_id==''){
        	$data['aname'] =  $this->db->query("select * from tblacode  where atype='Child' ")->result_array();

			// }else{
				//  $data['aname'] =  $this->db->query("select * from tblacode  where atype='Child' and general in ('$customer_code','$vendor_code','$bank_code','$expense_code','1001012000','1002001000','2001005000','2002001000','2005002000','2006001000','4001012000')")->result_array();
			// }

		$userid=$this->session->userdata('id'); 
		$type="JV"; 
		$data['cash_in_hand'] = $this->db->query("select sum(damount)-sum(camount) as cash_in_hand from tbltrans_detail where acode='$cash_code'")->row_array()['cash_in_hand'];
        // pm($data['cash_in_hand']);
         $trans_id = $this->db->query("select max(trans_id) as trans_id from tbltrans_detail where sale_point_id='$sale_point_id' and vno!='0-JV-1' and vtype='$type'")->row_array()['trans_id'];
      if($trans_id==''){
      	 $trans_id=1;
      	}else{
      		 $trans_id=$trans_id+1;
      	}
	
		
	
		$data['jv']=$sale_point_id . "-" . $type . "-" . $trans_id;
		//q();
		


        $data["filter"] = 'add';
        $data["title"] = "Add Journal Voucher";    			
		$this->load->view($this->session->userdata('language')."/Journal_voucher/add",$data);
	}

	public function edit($id='')
	{
		$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
		 $login_user=$this->session->userdata('id');
       $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
       $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
       $customer_code=$fix_code['customer_code'];
        $vendor_code=$fix_code['vendor_code'];
        $cash_code=$fix_code['cash_code'];
        $sale_point_id=$fix_code['sale_point_id'];
         $expense_code=$fix_code['expense_code'];
         //   if($sale_point_id==''){
        	$data['aname'] =  $this->db->query("select * from tblacode  where atype='Child' ")->result_array();

			// }else{
				//  $data['aname'] =  $this->db->query("select * from tblacode  where atype='Child' and general in ('$customer_code','$vendor_code','$bank_code','$expense_code','1001012000','1002001000','2001005000','2002001000','2005002000','2006001000','4001012000')")->result_array();
			// }

		$data['record'] = $this->db->query("select tbltrans_detail.*,tblacode.aname from tbltrans_detail inner join tblacode on tblacode.acode=tbltrans_detail.acode where ig_detail_id='$id' and vtype='JV' ")->result_array();
		//pm($data['record']);

        $data["filter"] = 'add';
        $data["title"] = "Edit Journal Voucher";    			
		$this->load->view($this->session->userdata('language')."/Journal_voucher/add",$data);
	}

	

	public function delete($id='') {

// $login_user=$this->session->userdata('id');
// 		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '321' limit 1")->row_array();
// 		if ($role['delete']!=1) {
// 			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
// 			redirect(SURL . 'Journal_voucher/index/');
// 			}
		$this->db->trans_start();
        $table = "tbltrans_master";
        $where = array("masterid"=>$id);
       	$delete = $this->mod_common->delete_record($table, $where);

        $table = "tbltrans_detail";
        $where = array("ig_detail_id"=>$id);
        $delete = $this->mod_common->delete_record($table, $where);
		$this->db->trans_complete();

		if ($this->db->trans_status() === TRUE)
		{
		    $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Journal_voucher/');
		}else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Journal_voucher/');
        }
    }
    public function detail($id=''){
	$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
	
		if($id){
			$login_user=$this->session->userdata('id');
       $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
       $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
       $cash_code=$fix_code['cash_code'];
			$table='tblacode';
			$where = array('atype' => 'Child','acode!='=>'$cash_code');
			$data['aname'] = $this->mod_common->select_array_records($table,"*",$where);

			$table='tbl_company';       
        	$data['company'] = $this->mod_common->get_all_records($table,"*");


			$table='tbltrans_master';
			$where = "vno='$id'";
			$data['single_edit'] = $this->mod_common->select_single_records($table,$where);
        	//pm($data['company'] )
			
			$tables='tbltrans_detail';
			$wheres = "vno='$id'";
			$data['paymentreceipt_list'] = $this->mod_voucher->select_trans_print_records($wheres);

			//pm($data['paymentreceipt_list']);

	        $data["filter"] = 'edit';
        	$data["title"] = "Journal Voucher";
			$this->load->view($this->session->userdata('language')."/Journal_voucher/single_new",$data);
		}
		else{
			redirect(SURL.'PaymentReceipt');
		}
	}

}
