<?php  defined('BASEPATH') OR exit('No direct script access allowed');

class Department_coding extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
           "mod_common"
        ));
        $this->load->library('Uploadimage');
        
    }

	public function index()
	{
		$data['dept_list'] =  $this->db->query("select * from tbldepartment  order by  deptcode desc")->result_array();
		
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Department";		

		$this->load->view($this->session->userdata('language')."/Department_coding/manage_department",$data);
		
		
			 
			
	}

	public function add_department()
	{
		$login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '4' limit 1")->row_array();
		// if ($role['add']!=1) {
		// 	$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
		// 	redirect(SURL . 'Department_coding/index/');
		// 	}    			
		$this->load->view($this->session->userdata('language')."/Department_coding/add_department",$data);
	}

	public function add(){

	

		if($this->input->server('REQUEST_METHOD') == 'POST'){

						$this->db->trans_start();
						$login_user=$this->session->userdata('id');

			$array = array(
						'deptname' => $this->input->post("deptname"),
						'status' => $this->input->post("status"),
						'created_by' =>$login_user,
						'created_datetime' =>date('Y-m-d'),
						'modified_by' =>$login_user,
						'modified_datetime' =>date('Y-m-d'),

						);
				
			    
				
	if(empty($this->input->post("edit"))){
			$deptname=$this->input->post("deptname");
		$deptname = $this->db->query("select deptname as deptname from tbldepartment where deptname = '$deptname'")->row_array()['deptname'];
		 
		if ($deptname!='') {
			$this->session->set_flashdata('err_message', 'Department Name Is Already Added .');
			redirect(SURL . 'Department_coding/index/');
			}

				
			    $query = $this->mod_common->insert_into_table("tbldepartment",$array);
				
			}
			else
			{

				 $edit = intval($this->input->post("edit")); 
				$query = $this->mod_common->update_table("tbldepartment",array("deptcode"=>$edit),$array);

		    }

		    $this->db->trans_complete();

			if ($query) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully added.');
	            redirect(SURL . 'Department_coding/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'Department_coding/');
	        }
	    }
	}

	    public function edit($id){

		if($id){
			$login_user=$this->session->userdata('id');
        // echo $id;exit();
			$data['record'] = $this->db->query("select * from tbldepartment where deptcode='$id'")->row_array();
 //pm($data['record']);exit();
			$data["filter"] = 'add';
		//echo "<pre>";print_r($data);
		$this->load->view($this->session->userdata('language')."/Department_coding/add_department", $data);
		}

	
	}

	public function delete($id) {
     $deptcode =$this->db->query("select deptcode from tbldepartment where deptcode='$id'")->row_array()['deptcode'];
		#-------------delete record--------------#
        $table = "tbldepartment";
        $where = "deptcode = '" . $id . "'";
        $delete_country = $this->mod_common->delete_record($table, $where);

        if ($delete_country) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Department_coding/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Department_coding/');
        }
    }


}
