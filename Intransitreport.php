<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Intransitreport extends CI_Controller {



	public function __construct() {
        parent::__construct();
		

        $this->load->model(array(
            "mod_salereport","mod_common","mod_customerstockledger","mod_customer","mod_salelpg"
        ));
        
    }

	public function index()
	{

		
		$data["title"] = "Intransit Report";	
		$this->load->view($this->session->userdata('language')."/Intransitreport/search",$data);	
		

	}
	

	public function intrasit_report()
	{			//pm($this->input->post());						
		$date=$this->input->post("date");
		$data['date']=$date;
		$data['record'] = $this->db->query("SELECT tbl_goodsreceiving.*,tbl_goodsreceiving.vehicle as veh,tbl_driver.* FROM `tbl_goodsreceiving` inner join tbl_driver on tbl_driver.id=tbl_goodsreceiving.vehicle where  receiptdate<='$date' and tbl_goodsreceiving.receiptnos not in (SELECT good_receving_id FROM `tbl_bulk_sale` where tbl_bulk_sale.is_approved='0' and vehicle_status!='Open')  order by vehicle_status,receiptdate asc")->result_array();
		//pm($data['record']);
			if ($data['record']) {
				$this->load->view($this->session->userdata('language')."/Intransitreport/intrasit_report",$data);
				
	
	        } else {
				
	            $this->session->set_flashdata('err_message', 'No Record Found.');
	            redirect(SURL . 'Intransitreport/');
	        }
		
		
		
		
	}
		public function newpdf(){

		if($this->input->server('REQUEST_METHOD') == 'POST' || $id !=''){
		
		$date=$this->input->post("date");
		$data['date']=$date;
		$data['record'] = $this->db->query("SELECT tbl_goodsreceiving.*,tbl_goodsreceiving.vehicle as veh,tbl_driver.* FROM `tbl_goodsreceiving` inner join tbl_driver on tbl_driver.id=tbl_goodsreceiving.vehicle where  receiptdate<='$date' and tbl_goodsreceiving.receiptnos not in (SELECT good_receving_id FROM `tbl_bulk_sale` where tbl_bulk_sale.is_approved='0' and vehicle_status!='Open')  order by vehicle_status,receiptdate asc")->result_array();
			
		
			
	    }

	  
	    	 $profilename =  $from_date;
	  
	  


		$this->load->view($this->session->userdata('language')."/Intransitreport/pdffile",$data);

		$this->load->library('pdf');
			 $html = $this->output->get_output();
			 $this->dompdf->loadHtml($html);
			 $this->dompdf->setPaper('A4', 'landscape');
	        $this->dompdf->render();


	        
	        $this->dompdf->stream( $profilename.".pdf", array("Attachment"=>0));	
	}

}

