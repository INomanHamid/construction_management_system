<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_tracking extends CI_Controller {


	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_salereport","mod_common","mod_customerstockledger","mod_customer","mod_salelpg"
        ));
        
    }

	public function index()
	{
		$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
        
        $login_user=$this->session->userdata('id');
   $data['sale_point_id']=  $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
   $data['project_list'] =  $this->db->query("select * from tbl_projects where status='Active'  ")->result_array();
     // $data['location']=$this->db->query("select * from tbl_sales_point $where_sale_point_id")->result_array();
		// pm($data['items']);
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Project Tracking";	
		$this->load->view($this->session->userdata('language')."/Project_tracking/search",$data);


	}

	
	public function report($id)
	{
		//pm($this->input->post(""));exit();
 
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			//$data['report']=  $this->input->post();
			 $data['projectid'] = $projectid=$this->input->post('projectid');
              $data['project_name']= $this->db->query("select title from tbl_projects where projectid='$projectid'")->row_array()['title'];
 
	 
	    $data["title"] = "Project&nbsp;Tracking";
	    $table='tbl_company';       
       $data['company'] = $this->mod_common->get_all_records($table,"*");
			
		 $this->load->view($this->session->userdata('language')."/Project_tracking/single",$data);
                        		
           
	    }else{
	    	$data['projectid'] = $projectid=$id;
        $data['project_name']= $this->db->query("select title from tbl_projects where projectid='$projectid'")->row_array()['title'];
 
	 
	    $data["title"] = "Project&nbsp;Tracking";
	    $table='tbl_company';       
       $data['company'] = $this->mod_common->get_all_records($table,"*");
			
		 $this->load->view($this->session->userdata('language')."/Project_tracking/single",$data);

	    }
	}






}
