<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sub_project_listing extends CI_Controller {


	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_vendor","mod_common","mod_customer"
        ));
        
    }
	public function index()
	{   
			if(isset($_POST['submit'])){	
			$projectcode = $this->input->post("projectcode");
			//echo $projectcode;exit();
			if($projectcode!='All')
				{ 
			$sub_project_title= "where tbl_sub_projects.projectid='$projectcode'"; }
			else{ 
				$sub_project_title= ""; }
		}
		$data['project_list'] =  $this->db->query("select * from tbl_projects where status='Active'  ")->result_array();
		 
     
      	$data['course_list'] =  $this->db->query("select * from tbl_sub_projects $sub_project_title")->result_array();
      	 $data["id"] = $id;

	$data["filter"] = '';
	$this->load->view($this->session->userdata('language')."/Sub_project_listing/sub_manage",$data);



	}

	public function detail($p_id)
	{   
	$data['p_id']=$p_id;
	//echo $data['p_id'];exit();	
	$data["filter"] = '';
	$this->load->view($this->session->userdata('language')."/Sub_project_listing/manage_detail",$data);

	}



	public function sub_add($id)
	{ 
// $data['add'] = $this->db->query("select * from sms_template")->result_array();
// 		$data["filter"] = 'add';
		$data["id"] = $id;
			$data["project_list"]= $this->db->query("SELECT * FROM tbl_projects")->result_array();
			//echo $data["sub_project_list"];exit();
		$data['location'] = $this->db->query("select * from tbllocation ")->result_array();
		$this->load->view($this->session->userdata('language')."/Sub_project_listing/sub_add",$data);
	}

	public function add(){
        
		if($this->input->server('REQUEST_METHOD') == 'POST'){

			$login_user=$this->session->userdata('id');


				$array = array(
			
			'sub_project_title' =>$this->input->post("title"),
			'projectid' =>$this->input->post("projectid"),
			'sdate' =>$this->input->post("sdate"),
			'edate' =>$this->input->post("edate"),
			'duration' =>$this->input->post("duration"),
			'total_cost' =>$this->input->post("total_cost"),
			'main_details' =>$this->input->post("main_details"),
			'area_details' =>$this->input->post("area_details"),
			'location' =>$this->input->post("location"),
			'boq_status' =>'Approve',
			'status' =>$this->input->post("status"),
			'created_by' =>$login_user,
			'created_date' =>date('Y-m-d'),
			'updated_by' =>$login_user,
			'updated_date' =>date('Y-m-d'),
		 

			);
				$id=$this->input->post("projectid");
            if(empty($this->input->post("edit"))){
		    
			$res = $this->mod_common->insert_into_table("tbl_sub_projects", $array);
		 
		}  	else
			{
				 $edit = intval($this->input->post("edit")); 
				$query = $this->mod_common->update_table("tbl_sub_projects",array("sub_projectid"=>$edit),$array);
		
		    }
		    $this->db->trans_complete();

		    	if ($res) {
				 	$this->session->set_flashdata('ok_message', 'You have succesfully added.');
		            redirect(SURL . 'Sub_project_listing/index/'.$id);
		            //$this->load->view('Company/add',$add);
		        } 
		        else if ($query) {
				 	$this->session->set_flashdata('ok_message', 'You have succesfully Updated.');
		            redirect(SURL . 'Sub_project_listing/index/'.$id);
		            //$this->load->view('Company/add',$add);
		        }else {
		            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
		            redirect(SURL . 'Sub_project_listing/index/'.$id);
		        }
		        
				
	    }
        $this->load->view($this->session->userdata('language')."/Sub_project_listing/add",$data);
		}

	


	public function delete($id) {
      	$this->db->trans_start();
      	$record_exit= $this->db->query("select projectid,sub_project_id from tbl_projects_boq where sub_project_id='$id'")->row_array();
		if(!empty($record_exit))
			{
				
				//echo "string";
				$this->session->set_flashdata('err_message', 'Sub Project is used in BOQ Detail You Can Not Delete it');
				redirect(SURL . 'Sub_project_listing');
			}
      	 $projectid =$this->db->query("select projectid from tbl_sub_projects where sub_projectid='$id'")->row_array()['projectid'];
		$delete =$this->db->delete("tbl_sub_projects",array("sub_projectid"=>$id));
		//pm($delete);exit();
        $this->db->trans_complete();
		$id=$projectid;
        if ($delete) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Sub_project_listing/index/'.$projectid);
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Sub_project_listing/index/'.$projectid);
        }
    }
    public function edit($id){

		
	    $data['location'] = $this->db->query("select * from tbllocation ")->result_array();
	    $data["project_list"]= $this->db->query("SELECT * FROM tbl_projects")->result_array();
		 if($id){
			 $data['record'] =$this->db->query("select * from tbl_sub_projects where sub_projectid='$id'")->result_array()[0];
			 	$data["id"] = $data['record']['projectid'];
			
			// pm($data['record']);exit;

	    	$this->load->view($this->session->userdata('language')."/Sub_project_listing/sub_add",$data);
			
		}
	}


}
