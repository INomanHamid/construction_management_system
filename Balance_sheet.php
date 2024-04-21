<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Balance_sheet extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_common","mod_profitloss"
        ));
        
    }

	public function index()
	{
	
		#----load view----------#
		$data["title"] = "Balance Sheet Report";	
	    $this->load->view($this->session->userdata('language')."/Balance_sheet/search",$data);       	
	}


	public function detail_report()
	{							
		//pm($this->input->post());
		$data['daterange'] = "2020-01-01 to ".$this->input->post("fdate");
		$data['from_date'] ='2020-01-01';
		$data['to_date'] = $this->input->post("fdate");
		$report=  $this->mod_profitloss->get_report($data);
		foreach ($report as $key => $value) {
		  $Net_Profit =$value['Net_Profit'];
		  $intransit_amt =$value['intransit_amt'];
		   

		}
		$data['Net_Profit'] = $Net_Profit;
		$data['intransit_amt'] = $intransit_amt;
		//pm($data['Net_Profit']);exit;
		$table='tbl_company';       
       	$data['company'] = $this->mod_common->get_all_records($table,"*");
	    $this->load->view($this->session->userdata('language')."/Balance_sheet/detail_report",$data);
	        

	         
	}
		public function report()
	{							
		//pm($this->input->post());exit;
		$data['daterange'] = "2020-01-01 to ".$this->input->post("to_date");
		$data['from_date']=$from_date ='2020-01-01';
		$data['to_date']=$to_date = $this->input->post("to_date");
		$data['side']=$side = $this->input->post("side");
		$data['acode'] =$acode= $this->input->post("acode");
		$data['parentacodee'] =$parentacodee = $acode[0].$acode[1].$acode[2].$acode[3].$acode[4].$acode[5].$acode[6];
		$data['report'] = $this->db->query("select DISTINCT acode from tbltrans_detail where vdate between '$from_date' and '$to_date'  and left(acode,7)=$parentacodee")->result_array();
	
		
		//pm($data['report']);exit;
		$table='tbl_company';       
       	$data['company'] = $this->mod_common->get_all_records($table,"*");
	    $this->load->view($this->session->userdata('language')."/Balance_sheet/report",$data);
	        

	         
	}

	


}

?>
