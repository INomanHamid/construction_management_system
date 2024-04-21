<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bank extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_bank","mod_common"
        ));
        
    }
	public function index()
	{

		// $table='tblacode';
		// $data['bank_list'] = $this->mod_bank->getOnlyBanks();
		$login_user=$this->session->userdata('id');
       $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
       $general = $this->db->query("select bank_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['bank_code'];
        if($sale_point_id=='0'){
       $where_general="and left(acode,6)='200401' and atype='Child'";
        }else{
       $where_general="and general='$general'";
        }
		$data['bank_list'] = $this->db->query("select * from tblacode where ac_status='Active' $where_general")->result_array(); 
		$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
		
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Bank";


		$this->load->view($this->session->userdata('language')."/bank_coding/manage_bank",$data);
	}

	public function add_bank()
	{
		$table='tbl_country';       
        $data['country_list'] = $this->mod_common->get_all_records($table,"*"); 
		$table='tbl_city';       
        $data['city_list'] = $this->mod_common->get_all_records($table,"*"); 
        $login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array();
        $data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];

        if($sale_point_id !=''){ $where_sale_point_id= "where sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
		$data['location']=$this->db->query("select * from tbl_sales_point $where_sale_point_id")->result_array();
        $data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
		$data["filter"] = 'add';
		$this->load->view($this->session->userdata('language')."/bank_coding/add_bank",$data);
	}

	public function add(){
		//echo "<pre>";print_r($_POST);exit;
		
		//$login_user=$this->session->userdata('id');
       //$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		 $sale_point_id=$_POST["location"];
       $general = $this->db->query("select bank_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['bank_code'];
       $rest_creditors_code=$general[0].$general[1].$general[2].$general[3].$general[4].$general[5].$general[6];

		$data['datas'] = $this->mod_bank->accountcode_forbank($rest_creditors_code);

		$adata['acode']=$data['datas'];
		$adata['aname']=trim($_POST["bankname"]);
		$adata['account_no']=trim($_POST["account_no"]);
		$adata['email']=trim($_POST["email"]);
		$adata['address']=trim($_POST["address"]);
		$adata['phone_no']=trim($_POST["phoneno"]);
		$adata['cell']=trim($_POST["cellno"]);
		$adata['cont_person']=trim($_POST["contactperson"]);
		$adata['reg_date']=$_POST["regdate"];
		$adata['credit_days']=trim($_POST["creditdays"]);
		$adata['reg_no']=trim($_POST["regno"]);
		$adata['opngbl']=trim($_POST["openingbalance"]);
		$adata['optype']=trim($_POST["openingtype"]);
		$adata['vat_no']=trim($_POST["vatno"]);
		$adata['ac_status']=trim($_POST["status"]);
		$adata['segment']=trim($_POST["segment"]);
		$adata['country_id']=trim($_POST["country"]);
		$adata['city_id']=trim($_POST["city"]);
		$adata['area_id']=trim($_POST["area"]);
		/* fixed values */
		$adata['general']=$general;
		$adata['atype']="Child";
		$adata['family']="L";
		$adata['sledger']="No";
		$adata['dlimit']=0;
		$adata['climit']=0;
		
        $where =  array('aname' => trim($_POST["bankname"]));

		$data['bank_list'] = $this->mod_bank->checkOnlyBank($where);

		if(!empty($data['bank_list']))
		{
	            $this->session->set_flashdata('err_message', 'Already added.');
	            redirect(SURL . 'Bank/');
		}
		else 
		{

			$table='tblacode';
			$res = $this->mod_common->insert_into_table($table,$adata);


			if ($res) {
			 	$this->session->set_flashdata('ok_message', 'You have successfully added.');
	            redirect(SURL . 'Bank/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'Bank/');
	        }
    	}


	}

	public function delete($id) {
		
			if ($this->mod_bank->under_items($id)) {
			$this->session->set_flashdata('err_message', 'Transaction is recorded for this Bank , you can not delete it.');
			redirect(SURL . 'Bank/');
			exit();
		}
		
		#-------------delete record--------------#
        $table = "tblacode";
        $where = "acode = '" . $id . "'";
        $delete_area = $this->mod_common->delete_record($table, $where);

        if ($delete_area) {
            $this->session->set_flashdata('ok_message', 'You have successfully deleted.');
            redirect(SURL . 'Bank/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Bank/');
        }
    }

    public function edit($id){
    	$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
		$table='tbl_country';       
        $data['country_list'] = $this->mod_common->get_all_records($table,"*");

        $data['city'] = $this->mod_bank->edit_record($id);

			if(empty($data['city']))
			{
	 			$this->session->set_flashdata('err_message', 'Record Not Exist!');			
				redirect(SURL.'Bank');
			}
    	$where_id = array('country_id' => $data['city']['country_id']);
    	$table='tbl_city';       
    	$data['city_list']= $this->mod_common->select_array_records($table,"*");

        $data['area'] = $this->mod_bank->edit_record($id);
			if(empty($data['area']))
			{
	 			$this->session->set_flashdata('err_message', 'Record Not Exist!');			
				redirect(SURL.'Bank');
			}
    	$where_id = array('city_id' => $data['area']['city_id']);
    	$table='tbl_area';       
    	$data['area_list']= $this->mod_common->select_array_records($table,"*",$where_id);

		$table='tblacode';
		$where = "acode='$id'";
		$data['bank'] = $this->mod_common->select_single_records($table,$where);
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array();
        $data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];

        if($sale_point_id !=''){ $where_sale_point_id= "where sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
		$data['location']=$this->db->query("select * from tbl_sales_point $where_sale_point_id")->result_array();
		$data["filter"] = 'update';
		//echo "<pre>";print_r($data);
		$this->load->view($this->session->userdata('language')."/bank_coding/add_bank", $data);
	}
	public function update(){
		// $login_user=$this->session->userdata('id');
  //     $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
       $sale_point_id=$_POST["location"];
       $general = $this->db->query("select bank_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['bank_code'];

		$adata['aname']=trim($_POST["bankname"]);
		$adata['account_no']=trim($_POST["account_no"]);
		$adata['email']=trim($_POST["email"]);
		$adata['address']=trim($_POST["address"]);
		$adata['phone_no']=trim($_POST["phoneno"]);
		$adata['cell']=trim($_POST["cellno"]);
		$adata['cont_person']=trim($_POST["contactperson"]);
		$adata['reg_date']=$_POST["regdate"];
		$adata['credit_days']=trim($_POST["creditdays"]);
		$adata['reg_no']=trim($_POST["regno"]);
		$adata['opngbl']=trim($_POST["openingbalance"]);
		$adata['optype']=trim($_POST["openingtype"]);
		$adata['vat_no']=trim($_POST["vatno"]);
		$adata['ac_status']=trim($_POST["status"]);
		$adata['segment']=trim($_POST["segment"]);
		$adata['country_id']=trim($_POST["country"]);
		$adata['city_id']=trim($_POST["city"]);
		$adata['area_id']=trim($_POST["area"]);
		/* fixed values */
		$adata['general']=$general;
		$adata['atype']="Child";
		$adata['family']="L";
		$adata['sledger']="No";
		$adata['dlimit']=0;
		$adata['climit']=0;
 		
 		$id = trim($_POST["id"]);
		$where = "acode='$id'";
		

        $where_new =  array('aname' => trim($_POST["bankname"]), 'acode!=' => $id);

		$data['bank_list'] = $this->mod_bank->checkOnlyBank($where_new);

		if(!empty($data['bank_list']))
		{
	            $this->session->set_flashdata('err_message', 'Already added.');
	            redirect(SURL . 'Bank/');
		}

		$table='tblacode';
		$res=$this->mod_common->update_table($table,$where,$adata);


		if ($res) {
		 	$this->session->set_flashdata('ok_message', 'You have successfully updated.');
            redirect(SURL . 'Bank/');
        } else {
            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
            redirect(SURL . 'Bank/');
        }
	}

	function get_city()
	{
	    $table='tbl_city';
		$country_id=	$this->input->post('country_id');
		$where = array('country_id' => $country_id);
		$data['city_list'] = $this->mod_common->select_array_records($table,"*",$where);

		if($data['city_list']){?>
			<option value="">Choose a City...</option>
		<?php
			foreach ($data['city_list'] as $key => $value) {
				?>
				
				<option value="<?php echo  $value['city_id']; ?>"><?php echo  $value['city_name']; ?></option>
				
			<?php }
		}
		
	}

	function get_area()
	{
	    $table='tbl_area';
		$city_id=	$this->input->post('city_id');
		$where = array('city_id' => $city_id);
		$data['area_list'] = $this->mod_common->select_array_records($table,"*",$where);

		foreach ($data['area_list'] as $key => $value) {
			?>
			<option value="<?php echo  $value['area_id']; ?>"><?php echo  $value['aname']; ?></option>
			
		<?php }
		
	}

}
