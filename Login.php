<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	
	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_login","mod_user","mod_common"
        ));
    }
 //    public function index() {

 //        $table='tbl_admin';

 // 		$session_array = array('email' => $this->session->userdata('email'),
 //        	'admin_pwd'=>$this->session->userdata('admin_pwd'),
 //         );

	// 	$login_success =  $this->mod_common->select_single_records($table,$session_array);

	// 	if($this->session->userdata('logincode')!=$login_success['logincode'])
	// 	{
	// 		//echo 'sssssssssss'; exit;
	// 		redirect(SURL.'login/ses_session');
	// 	}
 //    	if($this->session->userdata('email')==''){

 //        	if($this->input->post('login_submit')){

 //        		//pm($this->input->post());
	// 			 $login_success =  $this->mod_login->check_login($table,$this->input->post());

	// 	        $randon_code=rand(1,100000);

	// 	        //$this->session->set_userdata('logrand',$randon_code);

	// 	        if ($login_success) {

	// 	        	if($login_success['logincode']!=0)
	// 	        	{
	// 	        		// $this->session->set_userdata('id',$login_success['id']);
	// 	        	 //    $this->session->set_flashdata('logout', 'Logout from other browser');
	// 	        	 //    //$this->session->set_flashdata('err_message', '-Already login from other browser');
	// 	        	 //    redirect(SURL . 'login');

	// 	        	    $temp_login=$login_success;
	// 	        		//$this->session->set_userdata($temp_login);
	// 	        		//pm($this->session->userdata);

	// 	        		$this->session->set_userdata('temp_email',$login_success['email']);
	// 	        		$this->session->set_userdata('randon_code',$randon_code);
	// 	        		$this->session->set_userdata('id',$login_success['id']);
	// 	        	    $this->session->set_flashdata('logout', 'Logout from other browser');
	// 	        	   // $this->session->set_flashdata('err_message', '-Already login from other browser');
	// 	        	    redirect(SURL . 'login');
	// 	        	}
		        	
	// 	        	$login_success['logincode']=$randon_code;

	// 	        	$this->session->set_userdata($login_success);		

	// 	        	$where = array('id' => $this->session->userdata('id'));

	// 	        	$data  = array('logincode' =>$randon_code );

	// 				$update_success =  $this->mod_common->update_table($table,$where,$data);

	// 	            $this->session->set_flashdata('ok_message', '- Login successfully!');
	// 	            redirect(SURL . 'country');
	// 	        } else {
	// 	            $this->session->set_flashdata('err_message', '- Error in login please try again!');
	// 	            redirect(SURL . 'login');
	// 	        }
	// 	    }
 //        //pm($data['restaurant_list']);
	// 	$data["filter"] = '';
	// 	#----load view----------#
	// 	$data["title"] = "Login";		
	// 	$this->load->view('login', $data);
	// 	}
	// 	else {
	// 	    $this->session->set_flashdata('ok_message', '- Already Login !');			
	// 		redirect(SURL.'country');
	// 	}
	// }
	
	    public function index() { 
    	//pm($this->session->userdata());

        $table='tbl_admin';

 		$session_array = array('email' => $this->session->userdata('email'),
        	'admin_pwd'=>$this->session->userdata('admin_pwd'),
         );

		$login_success =  $this->mod_common->select_single_records($table,$session_array);

		if($this->session->userdata('logincode')!=$login_success['logincode'])
		{
			//echo 'sssssssssss'; exit;
			redirect(SURL.'login/se_session');
		}
    	if($this->session->userdata('email')==''){

        	if($this->input->post('login_submit')){
        			$company_list= $this->mod_common->get_all_records("tbl_company","*");
				
				$startDate= $company_list[0]['lic_expiry_dt']; 
				$today=date('Y-m-d'); 
				 $days_int=0;
				// echo ($startDate);exit;
				   
				$start = strtotime($startDate);
				$end = strtotime($today);

				$days_int = ceil(($start - $end) / 86400); 

				if($days_int < 0){


					$this->session->set_flashdata('err_message', 'Login Failed ! Please note your License has expired, for renewal contact us :  +92 300 856 7797');
				
					//$this->session->set_flashdata('err_message', '- Error in login please try again!');
		             redirect(SURL . 'login');

				}

        	// pm($this->input->post());
				 $login_success =  $this->mod_login->check_login($table,$this->input->post());

		        $randon_code=rand(1,100000);

		        //$this->session->set_userdata('logrand',$randon_code);

		        if ($login_success) {

		        	

		        	if($login_success['logincode']!=0)
		        	{
		        		$temp_login=$login_success;
		        	 
		        		$this->session->set_userdata('temp_email',$login_success['email']);
		        		$this->session->set_userdata('randon_code',$randon_code);
		        		$this->session->set_userdata('id',$login_success['id']);

		        		$result = $this->mod_user->get_language($login_success['comp_id']);

						if ($result['lang_opt']=='both') {
			        		
			        		$this->session->set_userdata('temp_language',$login_success['language']);
			        		$this->session->set_userdata('language',$login_success['language']);
		        		}
		        		else 
		        		{
		        			$this->session->set_userdata('temp_language',$result['lang_opt']);
		        			$this->session->set_userdata('language',$result['lang_opt']);
		        		}

		        	    $this->session->set_flashdata('logout', 'Logout from other browser');
		        	   // $this->session->set_flashdata('err_message', '-Already login from other browser');
		        	    redirect(SURL . 'login');
		        	}

		        	

		        	$login_success['logincode']=$randon_code;

		        	$this->session->set_userdata($login_success);		

		 
					if($this->session->userdata('id')){
		        	$where = array('id' => $this->session->userdata('id'));

		        	$data  = array('logincode' =>$randon_code);

					 
						$update_success =  $this->mod_common->update_table($table,$where,$data);
						 
					}
						$result = $this->mod_user->get_language();

						 if ($result['lang_opt']=='both') {
			        		
			        		$this->session->set_userdata('language',$login_success['language']);
		        		}
		        		else 
		        		{
		        			  $this->session->set_userdata('language',$result['lang_opt']);
		        		}


		            $this->session->set_flashdata('ok_message', '- Login successfully!');
		            redirect(SURL . 'login/redirect');
		        } else {
		            $this->session->set_flashdata('err_message', '- Error in login please try again!');
		            redirect(SURL . 'login');
		        }
		    }
        //pm($data['company_list']);
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Login";		
		$this->load->view('login', $data);
		}
		else {
		    $this->session->set_flashdata('ok_message', '- Already Login !');			
			redirect(SURL.'login/redirect');
		}
	}

	public function logout()
	{
        $table='tbl_admin';
   		$where = array('id' => $this->session->userdata('id'));
    	$data  = array('logincode' =>0);
		$update_success =  $this->mod_common->update_table($table,$where,$data);
		$this->session->unset_userdata();
		$this->session->sess_destroy();
		redirect(SURL.'login');
	}
	public function ses_session()
	{
       // $table='tbl_admin';
   		//$where = array('id' => $this->session->userdata('id'));
    	//$data  = array('logincode' =>0);
		//$update_success =  $this->mod_common->update_table($table,$where,$data);
		$this->session->unset_userdata();
		$this->session->sess_destroy();
		redirect(SURL.'login');
	}
	public function language()
	{

		$url=SURL;

		$result = $this->mod_user->get_language();

		 if ($result['lang_opt']=='both') {

		$table='tbl_admin';
		$change_language='en';

		$url=$_GET['url'];

		$current_language=$this->session->userdata('language');
		if($current_language=='en')
		{
			$change_language='ur';
		}

		$where = array('id' => $this->session->userdata('id'));
    	
    	$data  = array('language' => $change_language);

		$this->session->set_userdata('language',$change_language);

		//echo $this->session->userdata('language');

		$update_success =  $this->mod_common->update_table($table,$where,$data);
	}


		redirect($url);

	}

public function change_password()
	{
		$table='tbl_admin';
		if($this->session->userdata('email')!=''){
			if($this->input->post('change_password_btn'))
			{
        	    
				$session_array = array('admin_pwd' => base64_encode(trim($this->input->post('old_password'))),'email' => $this->session->userdata('email'));

        	    $login_success =  $this->mod_common->select_single_records($table,$session_array);
	       	    if($login_success)
        	    {

        	    	if($this->input->post('new_password')==$this->input->post('con_password'))
        	    	{
				   		$where = array('id' => $this->session->userdata('id'));
				    	$data  = array('admin_pwd' =>base64_encode(trim($this->input->post('new_password'))));

						$update_success =  $this->mod_common->update_table($table,$where,$data);
						if($update_success)
						{
							//echo 'ssssssss'; exit;
							$session_array = array('admin_pwd' => $this->input->post('new_password'),'email' => $this->session->userdata('email'));

							$login_success =  $this->mod_common->select_single_records($table,$session_array);

							$this->session->set_userdata($login_success);
							$this->session->set_flashdata('ok_message', '- Password change successfully !');
							//echo $this->session->userdata('logincode');
							// pm($this->session->userdata());
							 redirect(SURL . 'login');
						}

					}
					else{
						$this->session->set_flashdata('err_message', 'Confirm password does not match');
					}
        	    }
        	    else{

        	    		$this->session->set_flashdata('err_message', 'Enter the correct old password');
						redirect(SURL.'login/change_password');
        	    }
	
			}

			$this->load->view('change_password');
		}
		else
		{
			$this->session->set_flashdata('err_message', 'First login to change password');
			redirect(SURL.'login');
		}

	}

	public function se_session($val)
	{
		//pm($this->session->userdata('id'));
		if($val==1){
	        $table='tbl_admin';
	   		$where = array('id' => $this->session->userdata('id'));
	    	$data  = array('logincode' =>$this->session->userdata('randon_code'));

			$update_success =  $this->mod_common->update_table($table,$where,$data);
				
	   		$session_array = array('id' => $this->session->userdata('id'),'email' => $this->session->userdata('temp_email'));
			if($update_success)
			{
				$login_success =  $this->mod_common->select_single_records($table,$session_array);
				$this->session->set_userdata($login_success);
				$this->session->set_userdata('language',$this->session->userdata('temp_language'));
			}
		}
		else 
		{
			$this->session->unset_userdata();
			$this->session->sess_destroy();
		}	

		redirect(SURL.'login');
	}

	public function redirect()
	{
		$table='tbl_company';       
						$data['company_list'] = $this->mod_common->get_all_records($table,"*");
						//pm($data['company_list']);exit;
						$startDate= $data['company_list'][0]['lic_expiry_dt'];
						$today=date('Y-m-d');
						 $days_int=0;
						// echo ($startDate);exit;
						   
						$start = strtotime($startDate);
						$end = strtotime($today);

						  $days_int = ceil(abs($end - $start) / 86400);
//echo ($days_int);exit;
						if($days_int <=10){

							$this->session->set_flashdata('ok_message', 'Login successfully ! Please note your License will be expired after '.$days_int.' days, for renewal contact us :  +92 300 856 7797');
						 
						}else{
									$this->session->set_flashdata('ok_message', '- Login successfully!');
						}
 
		redirect(SURL.'');
	}
}
