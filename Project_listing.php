<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_listing extends CI_Controller {


	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_vendor","mod_common","mod_customer"
        ));
        
    }
	public function index()
	{   
      	$data['course_list'] =  $this->db->query("select tbllocation.*,tbl_projects.* from tbllocation inner join tbl_projects on tbllocation.loccode=tbl_projects.location  order by tbl_projects.projectid desc")->result_array();

        //$data['course_list'] =$this->db->query("select * From tbl_projects order by projectid desc")->result_array();
	//pm($data['emp_list']);exit();
	$data["filter"] = '';
	$this->load->view($this->session->userdata('language')."/Project_listing/manage_project",$data);

	}


	public function detail($p_id)
	{   
	$data['p_id']=$p_id;
	//echo $data['p_id'];exit();
	$data['company'] = $this->db->query("select * from tbl_company ")->row_array();	
	$data["filter"] = '';
	$this->load->view($this->session->userdata('language')."/Project_listing/manage_detail",$data);

	}

	public function add_project()
	{ 
// $data['add'] = $this->db->query("select * from sms_template")->result_array();
// 		$data["filter"] = 'add';
		$data['location'] = $this->db->query("select * from tbllocation ")->result_array();
		$this->load->view($this->session->userdata('language')."/Project_listing/add_project",$data);
	}

	public function add(){
        

$login_user=$this->session->userdata('id');
				$array = array(
			
			'title' =>$this->input->post("title"),
			'sdate' =>$this->input->post("sdate"),
			'edate' =>$this->input->post("edate"),
			'duration' =>$this->input->post("duration"),
			'total_cost' =>$this->input->post("total_cost"),
			'main_details' =>$this->input->post("main_details"),
			'area_details' =>$this->input->post("area_details"),
			'location' =>$this->input->post("location"),
			'status' =>$this->input->post("status"),
			'created_by' =>$login_user,
			'created_date' =>date('Y-m-d'),
			'updated_by' =>$login_user,
			'updated_date' =>date('Y-m-d'),
			'boq_approve_by' =>$login_user,
			'boq_status' =>'Approve',
			'boq_approve_dt' =>date('Y-m-d'),
			

			);

            if(empty($this->input->post("edit"))){
		    
			$res = $this->mod_common->insert_into_table("tbl_projects", $array);
		 
		}  	else
			{
				 $edit = intval($this->input->post("edit")); 
				$query = $this->mod_common->update_table("tbl_projects",array("projectid"=>$edit),$array);
				
				// $query = $this->mod_common->update_table("tbl_admin",array("emp_code"=>$edit),$array_admin);

		    }
		    $this->db->trans_complete();

		    	if ($res) {
				 	$this->session->set_flashdata('ok_message', 'You have succesfully added.');
		            redirect(SURL . 'Project_listing/');
		            //$this->load->view('Company/add',$add);
		        }else if ($query) {
				 	$this->session->set_flashdata('ok_message', 'You have succesfully Updated.');
		            redirect(SURL . 'Project_listing/');
		            //$this->load->view('Company/add',$add);
		        } else {
		            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
		            redirect(SURL . 'Project_listing/');
		        }
		        
				
	    
        $this->load->view($this->session->userdata('language')."/Project_listing/add",$data);
		}

	


	public function delete($id) {
      	$this->db->trans_start();
      	$record_exit= $this->db->query("select projectid from tbl_sub_projects where projectid='$id'")->row_array();
		if(!empty($record_exit))
			{

				//echo "string";
				$this->session->set_flashdata('err_message', 'Project is used in Sub Projects You Can Not Delete it');
				redirect(SURL . 'Project_listing');
			}
		$delete =$this->db->delete("tbl_projects",array("projectid"=>$id));
        $this->db->trans_complete();

        if ($delete) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Project_listing/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Project_listing/');
        }
    }
    public function edit($id){

		
	 //    $data['institute_name'] =$this->db->query("select business_name from tbl_company")->row_array();
		// $data['city_list'] =$this->db->query("select * from tbl_city")->result_array();
		// $data['desg_list'] =$this->db->query("select * from tbldesgcode where status='Active'")->result_array();
		$data['location'] = $this->db->query("select * from tbllocation ")->result_array();
		if($id){
			 $data['record'] =$this->db->query("select * from tbl_projects where projectid='$id'")->result_array()[0];
			
			// pm($data['record'] );

	    	$this->load->view($this->session->userdata('language')."/Project_listing/add_project",$data);
			
		}
	}


}
