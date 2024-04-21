<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Class_coding extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_user","mod_common"
        ));
    }

	public function index()
	{   $login_user=$this->session->userdata('id');
    	 $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
          if($sale_point_id=='1'){$where_cat="where tblclass.classcode='2'";}else if($sale_point_id=='2'){$where_cat="where tblclass.classcode='1'";}
    	$data['class_code'] = $this->db->query("select * from tblclass $where_cat order by classcode desc")->result_array(); 
	$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Class Code";		
		$this->load->view($this->session->userdata('language')."/class_coding/manage_class",$data);
	}


	public function add_class()
	{		
    	$data["title"] = "Add Class Code"; 
    	$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];    
		$this->load->view($this->session->userdata('language')."/class_coding/add_class",$data);
	}
		
	public function add(){
		$user_hcode=$this->session->userdata('id');
		$stcode='0';
		$cdate=date('Y-m-d');

		if($this->input->server('REQUEST_METHOD') == 'POST'){

			#----check name already exist---------#
			$udata['stcode'] = $stcode;
			$udata['classname'] = $this->input->post('classname');
			$udata['status'] = $this->input->post('status');
			$udata['scode'] =$stcode;
			$udata['created_by'] =$user_hcode;
			$udata['created_date'] =$cdate;
			$udata['modified_by'] =$user_hcode;
			$udata['modified_date'] =$cdate;

			$table='tblclass';
			$res = $this->mod_common->insert_into_table($table,$udata);

			if ($res) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully added.');
	            redirect(SURL . 'Class_coding/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'Class_coding/');
	        }
	    }
	}

	public function edit($id){
		//echo "$id";exit();
		if($id){
			$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
			$table='tblclass';
			$where = "classcode='$id'";
			$data['class_code'] = $this->mod_common->select_single_records($table,$where);
			$this->load->view($this->session->userdata('language')."/class_coding/edit", $data);
		}
	}

	public function update(){
		$cdate=date('y-m-d');
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$cdata['classname'] = trim($this->input->post('classname'));
			$cdata['status'] = trim($this->input->post('status'));
			$cdata['modified_date'] =$cdate;  
			$id = $_POST['id'];

			//pm($_POST['hcode']);
			#----check name already exist---------
			$where = "classcode='$id'";
			$table='tblclass';
			$res=$this->mod_common->update_table($table,$where,$cdata);

			if ($res) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully updated.');
	            redirect(SURL . 'Class_coding/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'Class_coding/');
	        }
	    }

	}

	public function delete($id) {
		$classcode = $this->db->query("select * from tblcategory where classcode ='$id'")->row_array()['classcode'];
		if ($classcode>0) {
			$this->session->set_flashdata('err_message', 'Class Code is used for Main Menu You can not delete it.');
			redirect(SURL . 'Class_coding/');
			exit();
			}

        $table = "tblclass";
        $where = "classcode = '" . $id . "'";
        $delete_country = $this->mod_common->delete_record($table, $where);

        if ($delete_country) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Class_coding/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Class_coding/');
        }
    }
}
