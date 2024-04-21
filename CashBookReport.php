<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CashBookReport extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_cashbookreport","mod_common","mod_customer","mod_salelpg"
        ));
        
    }

	public function index()
	{
		$table='tblacode';
		$where = "general='2003013000'";
		$data['customers'] = $this->mod_common->select_array_records($table,'*',$where);
$data['result1'] = $this->db->query("select * from tblacode where general='2003013000' and atype='Child'")->result_array();
		$table='tblmaterial_coding';
		$data['items'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Cash Book Report";	
			$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
		$this->load->view($this->session->userdata('language')."/cashbookreport/search",$data);
	}

	public function report()
	{
		        	$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
		if($this->input->server('REQUEST_METHOD') == 'POST'){

			$detailrec=$this->mod_cashbookreport->get_report($this->input->post());
    //pm($detailrec); exit;
			  $userid=$this->session->userdata('id'); 
			  $this->db->query("delete from cash_book_detail_temp where userid='$userid'");
			   foreach ($detailrec as $key => $value) {
			 $array = array(
							"userid"=>$userid,
							"acnumber"=>$value['acnumber'],
							"fromdate"=>$value['fromdate'],
							"todate"=>$value['todate'],
							"openingreceipt"=>$value['openingreceipt'],
							"openingbalance"=>$value['openingbalance'],
							"voucherno"=>$value['voucherno'],
							"voucherdate"=>$value['voucherdate'],
							"accountcode"=>$value['accountcode'],
							"acname"=>$value['acname'],
							"description"=>$value['description'],
							"receipt"=>$value['receipt'],
							"payment"=>$value['payment'],
							"balance"=>$value['balance'],
						   
							
						  );

				$this->mod_common->insert_into_table("cash_book_detail_temp", $array);
}
         $data['report'] = $this->db->query("SELECT * FROM `cash_book_detail_temp` where userid='$userid' order by voucherdate")->result_array();
			//pm($data['report']); exit;
			
			#----check name already exist---------#
			// if ($this->mod_city->get_by_title($data['city_name'])) {
			// 	$this->session->set_flashdata('err_message', 'Name Already Exist.');
			// 	redirect(SURL . 'city/add_city');
			// 	exit();
			// }
			$table='tbl_company';       
       		$data['company'] = $this->mod_common->get_all_records($table,"*");
			if ($data['report']) {
			 	//$this->session->set_flashdata('ok_message', 'You have succesfully added.');
	            //redirect(SURL . 'cashbookreport/detail',$data);
	            $data["title"] = "Cash Book";
	            $this->load->view($this->session->userdata('language')."/cashbookreport/single",$data);
	        } else {
	            //$this->session->set_flashdata('err_message', 'No Record Found.');
	            //redirect(SURL . 'cashbookreport/');
	            $data["title"] = "Cash Book";
	            $this->load->view($this->session->userdata('language')."/cashbookreport/single",$data);
	        }
	    }else{
	        //$data["filter"] = 'add';
	        /////////////////////////////////For Dashboard Link/////////////////////////////////
	        	$array= array(
	    		      'from_date' => date('Y-m-d'),
					  'to_date' => date('Y-m-d'),
					  'acode' =>'2003013001',
					  'id' =>'',
				 
					);
	        $detailrec=$this->mod_cashbookreport->get_report($array);
    //pm($detailrec); exit;
			  $userid=$this->session->userdata('id'); 
			  $this->db->query("delete from cash_book_detail_temp where userid='$userid'");
			   foreach ($detailrec as $key => $value) {
			 $array = array(
							"userid"=>$userid,
							"acnumber"=>$value['acnumber'],
							"fromdate"=>$value['fromdate'],
							"todate"=>$value['todate'],
							"openingreceipt"=>$value['openingreceipt'],
							"openingbalance"=>$value['openingbalance'],
							"voucherno"=>$value['voucherno'],
							"voucherdate"=>$value['voucherdate'],
							"accountcode"=>$value['accountcode'],
							"acname"=>$value['acname'],
							"description"=>$value['description'],
							"receipt"=>$value['receipt'],
							"payment"=>$value['payment'],
							"balance"=>$value['balance'],
						   
							
						  );

				$this->mod_common->insert_into_table("cash_book_detail_temp", $array);
}
         $data['report'] = $this->db->query("SELECT * FROM `cash_book_detail_temp` where userid='$userid' order by voucherdate")->result_array();
         $table='tbl_company';       
       		$data['company'] = $this->mod_common->get_all_records($table,"*");
	        /////////////////////////////////For Dashboard Link Ends///////////////////////////
	        $data["title"] = "Cash Book";    			
			$this->load->view($this->session->userdata('language')."/cashbookreport/single",$data);
		}
	}

	public function detail($id){
		if($id){
		$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
		$table='tblmaterial_coding';       
        $data['item_list'] = $this->mod_common->get_all_records($table,"*");
		$table='tbl_issue_goods';
		$where = "issuenos='$id'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);

		$data['edit_list'] = $this->mod_salelpg->edit_salelpg($id);
		//echo '<pre>';print_r($data['edit_list']);exit;
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Cash Book";
		$this->load->view($this->session->userdata('language')."/cashbookreport/single",$data);
		}
	}

	public function edit($id){
		if($id){
			$table='tbltrans_detail';
			$where = "vno='$id'";
			$data['payemetreceipt'] = $this->mod_common->select_single_records($table,$where);
			//pm($data['payemetreceipt']);exit;
	        $data["filter"] = 'edit';
        	$data["title"] = "Update Payment/Receipt";
			$this->load->view($this->session->userdata('language')."/cashbookreport/add", $data);
		}
		/* Update Data */
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$update=  $this->mod_transaction->update_transaction($this->input->post());
			
			// $transaction = $this->input->post('transaction');
			// if($transaction=="Payment"){ $vtype="CP"; }else{ $vtype="CR";}

			// $data['vtype'] = $vtype;
			// $data['type'] = $this->input->post('types');
			// $data['name'] = mysql_real_escape_string(trim($this->input->post('name')));
			// $data['created_date'] = $this->input->post('date');
			// $data['damount'] = $this->input->post('amount');
			// $data['remarks'] = $this->input->post('remarks');
			// //$data['modify_by'] = $_SESSION['id'];
			// //$data['modify_date']= date('Y-m-d');
			// $editid = $this->input->post('id');

			// // 		#----check name already exist---------#
			// // 			if ($this->mod_city->edit_by_title($cdata['city_name'],$id)) {
			// // 				$this->session->set_flashdata('err_message', 'Name Already Exist.');
			// // 				redirect(SURL . 'city/edit/'.$id);
			// // 				exit();
			// // 			}

			// $table='tbltrans_detail';
			// $where = "id='$editid'";
	 	// 	$res=$this->mod_common->update_table($table,$where,$data);

			if ($res) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully updated.');
	            redirect(SURL . 'CashBookReport/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Operation Failed.');
	            redirect(SURL . 'CashBookReport/');
	        }
	    }
	}

	public function delete($id) {
		// if ($this->mod_city->under_area($id)) {
		// 	$this->session->set_flashdata('err_message', 'There are areas under city you can not delete it.');
		// 	redirect(SURL . 'city/');
		// 	exit();
		// } 
		#-------------delete record--------------#
        $table = "tbltrans_detail";
        $where = "vno = '" . $id . "'";
        $delete = $this->mod_common->delete_record($table, $where);

        if ($delete) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'CashBookReport/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'CashBookReport/');
        }
    }

	function get_expensetypename()
	{
	    $table='tbl_exptype_coding';
		$t_id=	$this->input->post('t_id');
		$where = array('type' => $t_id);
		$data['expense_name'] = $this->mod_common->select_array_records($table,"*",$where);

		foreach ($data['expense_name'] as $key => $value) {
			?>
			<option value="<?php echo  $value['id']; ?>"><?php echo  $value['name']; ?></option>
			
		<?php }
		
	}
	function export()
	{
		if(isset($_POST["Export"])){
		 
      header('Content-Type: text/csv; charset=utf-8');  
      header('Content-Disposition: attachment; filename=data.csv');  
      $output = fopen("php://output", "w");
	  
      fputcsv($output, array('Voucher No', 'Voucher Date', 'Account Code', 'Account Name', 'Description', 'Receipt', 'Payment', 'Balance'));  
      $query = "SELECT * from tbltrans_detail between '$fromdate' and '$todate' ORDER BY testid DESC";  
      $result = mysqli_query($con, $query);  
      while($row = mysqli_fetch_assoc($result))  
      {  
           fputcsv($output, $row);  
      }  
      fclose($output);  
 }
	} 

}
