<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main_menu extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_user","mod_common"
        ));
    }

	public function index()
	{
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
          // if($sale_point_id=='1'){$where_cat="where tblclass.classcode='2'";}else if($sale_point_id=='2'){$where_cat="where tblclass.classcode='1'";}



		$data['menu_list'] =$this->db->query("select * from tblclass inner join tblcategory on tblclass.classcode = tblcategory.classcode $where_cat order by tblcategory.classcode desc")->result_array();

 $data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];

		
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Tables";		
		$this->load->view($this->session->userdata('language')."/main_menu/manage_menu",$data);
	}


	public function add_menu()
	{	
    	$data["title"] = "Add Main Menu";
    	$login_user=$this->session->userdata('id');
    	 $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
          // if($sale_point_id=='1'){$where_cat="where tblclass.classcode='2'";}else if($sale_point_id=='2'){$where_cat="where tblclass.classcode='1'";}
    	$data['class_name'] = $this->db->query("select * from tblclass $where_cat")->result_array();   
    	 $data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
  
		$this->load->view($this->session->userdata('language')."/main_menu/add_main_menu",$data);
	}
		
	public function add(){


		$user_id=$this->session->userdata('id');
		$cdate=date('Y-m-d');
		$filename = "";

        if ($_FILES['file']['name'] != "") {

            $projects_folder_path = './assets/images/menu/';


            $orignal_file_name = $_FILES['file']['name'];

            $file_ext = ltrim(strtolower(strrchr($_FILES['file']['name'], '.')), '.');

            $rand_num = rand(1, 1000);

            $config['upload_path'] = $projects_folder_path;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['overwrite'] = false;
            $config['encrypt_name'] = TRUE;
            //$config['file_name'] = $file_name;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if (!$this->upload->do_upload('file')) {

                $error_file_arr = array('error' => $this->upload->display_errors());
                //print_r($error_file_arr); exit;
                //return $error_file_arr;
            } else {
            
                $data_image_upload = array('upload_image_data' => $this->upload->data());
                $filename = $data_image_upload['upload_image_data']['file_name'];
                $full_path =   $data_image_upload['upload_image_data']['full_path'];
            }
        }

        $catcode = $this->db->query("select max(catcode) as catcode from tblcategory")->row_array()['catcode'];
      if($catcode==''){
      	 $catcode=1;
      	}else{
      		 $catcode=$catcode+1;
      	}
      


		if($this->input->server('REQUEST_METHOD') == 'POST'){

			#----check name already exist---------#
			$udata['catname'] = $this->input->post('classname');
			$udata['catcode'] = $catcode;
			$udata['bg'] = '0';
			$udata['image'] = $filename;
			$udata['status'] = $this->input->post('status');
			//$udata['item_type'] =$this->input->post('item_type');
			$udata['classcode'] =$this->input->post('classcode');
			$udata['vat_perc'] = $this->input->post('vat_perc');
			$udata['unit'] = $this->input->post('unit');
			$udata['created_by'] =$user_id;
			$udata['created_date'] =$cdate;
			$udata['modified_by'] =$user_id;
			$udata['modified_date'] =$cdate;

			$table='tblcategory';
			$res = $this->mod_common->insert_into_table($table,$udata);

			if ($res) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully added.');
	            redirect(SURL . 'Main_menu/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'Main_menu/');
	        }
	    }
	}

	public function edit($id){
		$login_user=$this->session->userdata('id');
    	 $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
          // if($sale_point_id=='1'){$where_cat="where tblclass.classcode='2'";}else if($sale_point_id=='2'){$where_cat="where tblclass.classcode='1'";}
    	$data['class_name'] = $this->db->query("select * from tblclass $where_cat")->result_array(); 
		
		if($id){
			$data['record'] = $this->db->query("select * from tblclass inner join tblcategory on tblclass.classcode = tblcategory.classcode where id='$id'")->row_array();
			 
			 $data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];

			$this->load->view($this->session->userdata('language')."/main_menu/edit", $data);
		}
	}

	public function update(){
		$user_id=$this->session->userdata('id');
		$cdate=date('Y-m-d');
	  $filename =  $this->input->post('old_file');

        if ($_FILES['file']['name'] != "") {

            $projects_folder_path = './assets/images/menu/';


            $orignal_file_name = $_FILES['file']['name'];

            $file_ext = ltrim(strtolower(strrchr($_FILES['file']['name'], '.')), '.');

            $rand_num = rand(1, 1000);

            $config['upload_path'] = $projects_folder_path;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['overwrite'] = false;
            $config['encrypt_name'] = TRUE;
            //$config['file_name'] = $file_name;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if (!$this->upload->do_upload('file')) {

                $error_file_arr = array('error' => $this->upload->display_errors());
                //print_r($error_file_arr); exit;
                //return $error_file_arr;
            } else {
            
                $data_image_upload = array('upload_image_data' => $this->upload->data());
                $filename = $data_image_upload['upload_image_data']['file_name'];
                $full_path =   $data_image_upload['upload_image_data']['full_path'];
            }
        }

		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$cdata['catname'] = trim($this->input->post('classname'));
			$cdata['image'] = $filename;
			$cdata['catcode'] = $_POST['id'];
			$cdata['status'] = trim($this->input->post('status'));
			$cdata['vat_perc'] = trim($this->input->post('vat_perc'));
			//$cdata['item_type'] = trim($this->input->post('item_type'));
			$cdata['classcode'] = trim($this->input->post('classcode'));
			$cdata['unit'] = trim($this->input->post('unit'));
			$cdata['modified_by'] =$user_id; 
			$cdata['modified_date'] =$cdate; 
			$id = $_POST['id'];

			
			#----check name already exist---------
			$where = "id='$id'";
			$table='tblcategory';
			$res=$this->mod_common->update_table($table,$where,$cdata);

			if ($res) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully updated.');
	            redirect(SURL . 'Main_menu/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'Main_menu/');
	        }
	    }

	}

	public function delete($id) {

       
        $itemcode = $this->db->query("select * from tblmaterial_coding where itemcode ='$id'")->row_array()['itemcode'];
		if ($itemcode>0) {
			$this->session->set_flashdata('err_message', 'Main menu is used for item You can not delete it.');
			redirect(SURL . 'Main_menu/');
			exit();
			}
			 $table = "tblcategory";
        $where = "id = '" . $id . "'";
        $delete_country = $this->mod_common->delete_record($table, $where);

        if ($delete_country) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Main_menu/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Main_menu/');
        }
    }
}
