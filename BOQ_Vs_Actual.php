<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BOQ_Vs_Actual extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_vendorwise","mod_common","mod_vendor","mod_girndirect"
        ));
        
    }

	public function index()
	{
		  	$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
		$login_user=$this->session->userdata('id');

 		$data['itemss'] = $this->db->query("select * from tblmaterial_coding $where_cat ")->result_array();
 		$data['project_list'] =  $this->db->query("select * from tbl_projects where status='Active'  ")->result_array();
 		$data["sub_project_list"]= $this->db->query("SELECT * FROM tbl_sub_projects")->result_array();
		$table='tblmaterial_coding';
		$data['items'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "";	
		$this->load->view($this->session->userdata('language')."/BOQ_Vs_Actual/search",$data);
	}

	public function details()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST'){
        $from_date =$data['from_date']=$this->input->post("from_date");
		 $to_date=$data['to_date']=$this->input->post("to_date");
		 $projectid=$data['projectid']=$this->input->post("projectid");
		 $projectcode_sub=$data['projectcode_sub']=$this->input->post("projectcode_sub");
		 $item=$data['itemname']=$this->input->post("item");
	    $data["title"] = "Store&nbsp;Issuance&nbsp;Report";
	    $table='tbl_company';       
       $data['company'] = $this->mod_common->get_all_records($table,"*");
			
		 $this->load->view($this->session->userdata('language')."/BOQ_Vs_Actual/single",$data);
                        		
           
	    }
		 
	}
			public function show_class_course()
	{
		$project=$this->input->post("project");
		//echo $class_id;
		 $class_record = $this->db->query("SELECT * FROM tbl_sub_projects where projectid='$project'")->result_array(); //pm($class_record);exit;?>
		 
		
		 	<?php

												foreach ($class_record as $key => $data) {
												# code...
												?>
			<option value="<?php echo $data['sub_projectid']; ?>"><?php echo ucwords($data['sub_project_title']); ?></option>
	
	<?php	 }
				
}

public function detail($id){
	   	$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
		if($id){
		$data['vendor_list'] = $this->mod_vendor->getOnlyVendors_only();
		$table='tblmaterial_coding';       
        $data['item_list'] = $this->mod_common->get_all_records($table,"*");
		$table='tbl_goodsreceiving';
		$where = "receiptnos='$id'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);
   //   echo '<pre>';print_r($data['single_edit']);
		$data['edit_list'] = $this->mod_girndirect->edit_directgirn($id);
		//echo '<pre>';print_r($data['edit_list']);
		$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "BOQ VS ACTUAL";
		//pm($data['edit_list']);
		$this->load->view($this->session->userdata('language')."/BOQ_Vs_Actual/single",$data);
		}
	}


}
