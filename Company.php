<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Company extends CI_Controller {
 
	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_company","mod_common"
        ));
        
    }

	public function index($id)
	{
		$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records_row($table,"*");
        if($data['company']){
        	$data["filter"] = 'edit';
        }else{
        	$data["filter"] = 'add';
        }
		#----load view----------#
		$data["title"] = "Company";

		$this->load->view($this->session->userdata('language')."/Company/add",$data);
	}

	public function add()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST'){

			$add=  $this->mod_company->add_company($this->input->post());

			if ($add) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully added.');
	            redirect(SURL . 'Company/edit/'.$add); 
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'Company/');
	        }
	    }
	   	$data["filter"] = 'Company';
		#----load view----------#
		$data["title"] = "Company";	
		$this->load->view($this->session->userdata('language')."/Company/add",$data);
	}


	public function edit($id){
		if($id){
			$table='tbl_company';
			$where = "id='$id'";
			$data['company'] = $this->mod_common->select_single_records($table,$where);
			$data["filter"] = 'edit';
			$data["title"] = "Company";	
			$this->load->view($this->session->userdata('language')."/Company/add",$data);
		}
		/* Update Data */
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$update=  $this->mod_company->update_company($this->input->post());

			if ($update) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully updated.');
	            redirect(SURL . 'Company/edit/'.$update);
	        } else {
	            $this->session->set_flashdata('err_message', 'Operation Failed.');
	            redirect(SURL . 'Company/edit/'.$update);
	        }
	    }
	}

}
