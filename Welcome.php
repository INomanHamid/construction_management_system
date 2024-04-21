<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Restaurant extends CI_Controller {

	
	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_restaurant"
        ));
        
    }

	public function index() {

	}    
	public function add() {

        if ($this->input->post("add_new_restaurant_submit")) {

     	  
	  	if ($this->form_validation->run() == FALSE){
                   $this->session->set_flashdata('err_message', validation_errors());
					redirect(SURL . 'restaurant/add');
		  }else{
			#---------- add restaurant record---------------#
			 $add_restaurant =  $this->mod_restaurant->add_restaurant();
            
				if ($add_restaurant) {
					$this->session->set_flashdata('ok_message', '- Restaurant added successfully!');
					redirect(SURL . 'restaurant');
				} else {
                $this->session->set_flashdata('err_message', '- Error in adding Restaurant please try again!');
                redirect(SURL . 'restaurant/add');
            	}
            }
        }

		#--------- load view----------------#
		$data["title"] = "Add Restaurant";
	  	$this->load->view('restaurant/add', $data);
    }

}
