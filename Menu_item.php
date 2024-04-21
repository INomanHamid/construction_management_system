<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu_item extends CI_Controller {

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
         
        $data['item'] = $this->db->query("select * from tblmaterial_coding order by materialcode desc ")->result_array();
 
		 // $data['item'] =$this->db->query("select * from tblclass inner join tblmaterial_coding on tblclass.classcode = tblmaterial_coding.itemcode order by tblmaterial_coding.materialcode desc")->result_array();
				//pm($data['item']);
		$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
		
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Menu Item (إدارة عنصر القائمة)";		
		$this->load->view($this->session->userdata('language')."/menu_item/manage_menu_item",$data);
	}


	public function add_menu_item()
	{	
		$login_user=$this->session->userdata('id');
    	 $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
 
    	$data['class_name'] = $this->db->query("select * from tblclass ")->result_array(); 
	    $data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
    	$data["title"] = "Add Menu Item";     
		$this->load->view($this->session->userdata('language')."/menu_item/add_menu_item",$data);
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

     //    $bar_code=$this->input->post('bar_code');
     //    $short_code=$this->input->post('short_code');
     //    $bar_code_check=$this->db->query("select bar_code from tblmaterial_coding where bar_code='$bar_code' and short_code='$short_code'")->row_array()['bar_code'];
     //    if ($bar_code==$bar_code_check) {
     //    	$this->session->set_flashdata('err_message','This Bar Code Is already added.');
					// redirect(SURL . 'Menu_item/add_menu_item');
     //    }


		if($this->input->server('REQUEST_METHOD') == 'POST'){

			#----check name already exist---------#
			$udata['materialcode_new'] ='';
			$udata['itemcode'] = $this->input->post('itemcode');
			$udata['catcode'] = $this->input->post('itemcode');
			$udata['size'] = '';
			$udata['unit'] = $this->input->post('unit');
			
			$udata['weight'] = '';
			$udata['groupcode'] ='';
			$udata['length'] ='';
			$udata['saleprice'] =$this->input->post('saleprice');
			$udata['whole_sale_rate'] =$this->input->post('whole_sale_rate');
			$udata['minqty'] ='';
			$udata['maxqty'] ='';
			$udata['re_order'] ='';
			$udata['rolevel'] ='';
			$udata['sale_code'] ='';
			$udata['image_path'] =$filename;
			$udata['stock_code'] ='';
			$udata['item_type'] ="Finished Goods";
			$udata['item_state'] ='';
			$udata['created_by'] =$user_id;
			$udata['created_date'] =date('Y-m-d');
			$udata['modified_date'] ='';
			$udata['modified_by'] ='';
			$udata['status'] =$this->input->post('status');
			$udata['classcode'] =$this->input->post('classname');
			$udata['sku'] ='';
			$udata['itemname'] =$this->input->post('itemname');
			$udata['description'] =$this->input->post('description');
			$udata['add_information'] ='';
			$udata['featured'] ='';
			$udata['emb_video'] ='';
			$udata['topping'] ='';
			$udata['dressing'] ='';
			$udata['have_recipe'] ='';
			$udata['have_variations'] ='';
			$udata['itemnameint'] ='';
			$udata['brandname'] ='';
			$udata['serving'] ='';
			$udata['packing'] ='';
			$udata['bar_code'] =$this->input->post('bar_code');
			$udata['short_code'] =$this->input->post('short_code');
			$udata['bar_code_type'] =$this->input->post('bar_code_type');
			$udata['item_serial_no'] =$this->input->post('item_serial_no');
			//pm($udata);exit();
            if(empty($this->input->post("id"))){
		    
			$res = $this->mod_common->insert_into_table("tblmaterial_coding", $udata);
		 
		}else{
		    
		      //pm($this->input->post());exit();
			$last_id = $this->input->post("id");
             $res=$this->mod_common->update_table("tblmaterial_coding",array("materialcode"=>$last_id), $udata);
           	
		}
			// $table='tblmaterial_coding';
			// $res = $this->mod_common->insert_into_table($table,$udata);

			if ($res) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully added.');
	            redirect(SURL . 'Menu_item/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'Menu_item/');
	        }
	    }
	}

	public function edit($id){
		//echo $id;exit();
		$login_user=$this->session->userdata('id');
    	 $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
  
    	$data['class_name'] = $this->db->query("select * from tblclass")->result_array(); 
		$data['main_menu'] = $this->db->query("select * from tblcategory ")->result_array();
		//pm($data['main_menu']);exit();
		$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
		
		if($id){
			$table='tblmaterial_coding';
			$where = "materialcode='$id'";
			$data['record'] = $this->db->query("select tblclass.*,tblcategory.*,tblmaterial_coding.* from tblclass inner join tblmaterial_coding on tblclass.classcode = tblmaterial_coding.classcode inner join tblcategory on tblcategory.catcode = tblmaterial_coding.itemcode where tblmaterial_coding.materialcode='$id'")->row_array();
			//pm($data['record']);exit();
			$this->load->view($this->session->userdata('language')."/menu_item/add_menu_item", $data);
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
			$cdata['itemcode'] = trim($this->input->post('itemcode'));
			$cdata['catcode'] = trim($this->input->post('itemcode'));
			
			$cdata['image_path'] = $filename;
			$cdata['status'] = trim($this->input->post('status'));
			$cdata['unit'] = trim($this->input->post('unit'));  
			$cdata['description'] = trim($this->input->post('description')); 
			$cdata['itemname'] = trim($this->input->post('itemname')); 
			$cdata['unit'] = trim($this->input->post('unit')); 
			$cdata['saleprice'] =trim($this->input->post('saleprice'));
			$cdata['whole_sale_rate'] =trim($this->input->post('whole_sale_rate'));
			$cdata['bar_code_type'] =trim($this->input->post('bar_code_type')); 
			$cdata['bar_code'] =trim($this->input->post('bar_code')); 
			$cdata['short_code'] =trim($this->input->post('short_code')); 
			$cdata['classcode'] =trim($this->input->post('classname')); 
			$cdata['item_serial_no'] =trim($this->input->post('item_serial_no')); 
			$cdata['modified_date'] =date('Y-m-d');
			$cdata['modified_by'] = $user_id;
		    $id = $_POST['id'];


		   //  $bar_code=trim($this->input->post('bar_code'));
		   //  $short_code=trim($this->input->post('short_code'));
     //        $bar_code_check=$this->db->query("select bar_code from tblmaterial_coding where bar_code='$bar_code' and short_code='$short_code' and materialcode!='$id'")->row_array()['bar_code'];
        
     //    if ($bar_code==$bar_code_check) {
     //    	$this->session->set_flashdata('err_message','This Bar Code Is already added.');
					// redirect(SURL . 'Menu_item/edit/'.$id);
     //    }

			
			#----check name already exist---------
			$where = "materialcode='$id'";
			$table='tblmaterial_coding';
			$res=$this->mod_common->update_table($table,$where,$cdata);

			if ($res) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully updated.');
	            redirect(SURL . 'Menu_item/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'Menu_item/');
	        }
	    }

	}

	public function delete($id) {

		 $menu_item_id = $this->db->query("select * from tbl_recepi where menu_item_id ='$id'")->row_array()['menu_item_id'];
		if ($menu_item_id>0) {
			$this->session->set_flashdata('err_message', 'Main Item is used for Recepi You can not delete it.');
			redirect(SURL . 'Menu_item/');
			exit();
			}

        $table = "tblmaterial_coding";
        $where = "materialcode = '" . $id . "'";
        $delete_country = $this->mod_common->delete_record($table, $where);

        if ($delete_country) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Menu_item/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Menu_item/');
        }
    }
    public function get_detail(){
	
		$classcode = $this->input->post("classname");
		 $itemcode =$_SESSION["itemcode"];
		//echo $classcode;exit();
		
       $menu = $this->db->query("select * from tblcategory where classcode='$classcode'")->result_array();
      ?>
	   <?php								
												
											
		
		foreach ($menu as $key => $data) {									
			?> 
		

			<option   value="<?php echo $data['catcode']; ?>"<?php if($data['catcode']==$itemcode){ ?> selected <?php } ?>><?php echo ucwords($data['catname']); ?></option>
		<?php } ?>
		
<?php	}
 public function check_bar_code(){
	
		
		 $bar_code=$this->input->post('bar_code');
		 //echo $bar_code;exit();
        
        $bar_code_check=$this->db->query("select bar_code from tblmaterial_coding where bar_code='$bar_code' ")->row_array()['bar_code'];
        if ($bar_code==$bar_code_check) {
        	$record = $this->db->query("select * from tblmaterial_coding where bar_code='$bar_code_check'")->result_array()[0];

        	echo json_encode($record);
        }
		
      
	   
}
 public function generate_bar_code(){
	
		
		 $itemcode=$this->input->post('itemcode');
		 $classname=$this->input->post('classname');
        $material_code=$this->db->query("select max(materialcode) as materialcode from tblmaterial_coding  ")->row_array()['materialcode'];
        $materialcode=$material_code+1;
        if ($materialcode <=9) {
        	$materialcode = "0000" . $materialcode;
        }else if($materialcode <=99){
        	$materialcode = "000" . $materialcode;

        }else if($materialcode <=999){
        	$materialcode = "00" . $materialcode;

        }else if($materialcode <=9999){
        	$materialcode = "0" . $materialcode;
        }
        if ($itemcode <=9) {
        	$itemcode = "0" . $itemcode;
        }
        if ($classname <=9) {
        	$classname = "0" . $classname;
        }
        $bar_code=$classname.$itemcode.$materialcode;
        echo $bar_code;



        
		
      
	   
}
}
