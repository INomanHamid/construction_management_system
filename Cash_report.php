<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cash_report extends CI_Controller {



	public function __construct() {
        parent::__construct();

        $this->load->model(array(
           "mod_common","mod_salelpg"
        ));
        
    }

	public function index()
	{
		$data['result1'] = $this->db->query("select * from tblacode where general='2002001400' and atype='Child'")->result_array();
	$data["title"] = "Monthly Cash Report";	
		$this->load->view($this->session->userdata('language')."/Cash_report/search",$data);

	}

	public function report()
	{
			$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
		if($this->input->server('REQUEST_METHOD') == 'POST'){
        $from_date =$data['from_date']=$this->input->post("from_date");
		$to_date=$data['to_date']=$this->input->post("to_date");
		$acode=$data['acode']=$this->input->post("acode");
	    $table='tbl_company';       
       $data['company'] = $this->mod_common->get_all_records($table,"*");
		$data["title"] = "Monthly&nbsp;Cash&nbsp;Report";
	    $this->load->view($this->session->userdata('language')."/Cash_report/single",$data);
	        
	    }
	}


}
