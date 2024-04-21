<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Boq_copy extends CI_Controller {


	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_vendor","mod_common","mod_customer"
        ));
        
    }
	public function index()
	{   
		$data["project_list"]= $this->db->query("SELECT * FROM tbl_projects")->result_array();
		$data["sub_project_list"]= $this->db->query("SELECT * FROM tbl_sub_projects")->result_array();

	$data["filter"] = '';
	$this->load->view($this->session->userdata('language')."/Boq_copy/manage_copy",$data);

	}


 

	public function add(){
        
		if($this->input->server('REQUEST_METHOD') == 'POST'){
		$this->db->trans_start(); 
	//	pm($this->input->post());exit;
$login_user=$this->session->userdata('id');

$projectcode=$this->input->post("projectcode");
$projectcode_sub=$this->input->post("projectcode_sub");
$projectcode_to=$this->input->post("projectcode_to");
$projectcode_to_sub=$this->input->post("projectcode_to_sub");

$result1= $this->db->query("SELECT * FROM tbl_projects_boq WHERE projectid ='$projectcode' AND sub_project_id ='$projectcode_sub'")->result_array();
foreach ($result1 as $key => $row) {

$itemcode=$row['itemcode'];
$unit=$row['unit'];
$quantity=$row['quantity'];
$purchase_rate=$row['purchase_rate'];
$amount=$row['amount'];
$remarks=$row['remarks'];
$mainpid=$projectcode_to;
$p_id=$projectcode_to_sub;
 
$created_date=date('Y-m-d');

	$array = array(
			
			'itemcode' =>$itemcode,
			'unit' =>$unit,
			'quantity' =>$quantity,
			'purchase_rate' =>$purchase_rate,
			'amount' =>$amount,
			'remarks' =>$remarks,
			'projectid' =>$mainpid, 
			'sub_project_id' =>$p_id,
			'created_by' =>$login_user,
			'created_date' =>$created_date, 
			

			);
	$res = $this->mod_common->insert_into_table("tbl_projects_boq", $array);
 
 
}
$total_cost=0;

$total_cost= $this->db->query("select sum(amount) as total_cost from tbl_projects_boq where  projectid ='$projectcode_to' and sub_project_id ='$projectcode_to_sub'")->row_array()['total_cost'];
 
 
$this->db->query("update tbl_sub_projects set total_cost='$total_cost',boq_status='Pending' where sub_projectid=$projectcode_to_sub and sub_projectid='$projectcode_to' "); 		 

 $total_cost_main=0;

$total_cost_main= $this->db->query("select sum(amount) as total_cost_main from tbl_projects_boq where  projectid ='$projectcode_to' ")->row_array()['total_cost_main'];
	$this->db->query("update tbl_projects set total_cost='$total_cost_main' where projectid='$projectcode_to'"); 		 

  
 



 
		    $this->db->trans_complete();

		    
				 	$this->session->set_flashdata('ok_message', 'BOQ Copy succesfully.');
		            redirect(SURL . 'Boq_copy/');
		            //$this->load->view('Company/add',$add);
		     
		        
				
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
		public function show_class_course_sec()
	{
		$project=$this->input->post("project");
		//echo $class_id;
		 $class_record = $this->db->query("SELECT * FROM tbl_sub_projects where projectid='$project' and sub_projectid not in (select distinct sub_project_id from tbl_projects_boq )")->result_array(); //pm($class_record);exit;?>
		 
		
		 	<?php

												foreach ($class_record as $key => $data) {
												# code...
												?>
			<option value="<?php echo $data['sub_projectid']; ?>"><?php echo ucwords($data['sub_project_title']); ?></option>
	
	<?php	 }
				
}
}
