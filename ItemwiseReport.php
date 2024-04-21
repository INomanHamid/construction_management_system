<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class itemwiseReport extends CI_Controller {


	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_salereport","mod_common","mod_customerstockledger","mod_customer","mod_salelpg"
        ));
        
    }

	public function index()
	{

		$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];

		$table='tblmaterial_coding';
		$data['items'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$table='tblcategory';       
        $data['category_list'] = $this->mod_common->get_all_records($table,"*");
        $data['class_name'] = $this->db->query("select * from tblclass")->result_array();

        $login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array();
        $data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];

        if($sale_point_id !=''){ $where_sale_point_id= "where sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
		$data['location']=$this->db->query("select * from tbl_sales_point $where_sale_point_id")->result_array();
        // $table='tbl_sales_point';   
$data['item'] = $this->db->query("select * from tblmaterial_coding ")->result_array();
        // $data['location'] = $this->mod_common->get_all_records($table,"*");
		$data["title"] = "Item Wise Reports";	
		$this->load->view($this->session->userdata('language')."/itemwiseReport/search_report_item",$data);


	}

	public function getitem($id){ 
		if($id=='All'){ ?>
		
		<option value="All">All Items</option>
<?php	}else{ 

		$query = $this->db->query("select * from tblmaterial_coding where catcode='$id'");
		//echo "select * from tblmaterial_coding where catcode='$id'";exit();

		$i=1;

		
			foreach ($query->result_array() as $key => $value) { ?>
		<?php if($i == 1){ ?><option value="All">All Items</option><?php } ?>
			<option value="<?php echo $value['materialcode']?>"><?php echo $value['itemname']?></option>
	
		<?php $i++; }
		
}	
	}

	public function details()
	{
		$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
		if($this->input->server('REQUEST_METHOD') == 'POST'){
		

	 
          $data['report1']=  $this->input->post();
		 
			$data['report']=  $this->mod_salereport->get_details($this->input->post());
			
			

			$table='tbl_company';       
        	$data['company'] = $this->mod_common->get_all_records($table,"*");

				$data['from_date'] = date('Y-m-d');
				$data['to_date'] = date('Y-m-d');
				$data["title"] = "Today Sale  Report";


				if($this->input->post('from_date')!='')
				{
				$data['from_date'] = trim($this->input->post('from_date'));
				$data['to_date'] = trim($this->input->post('to_date'));
				$data["title"] = "Sale B/W Date Report";

			}
  
	            $this->load->view($this->session->userdata('language')."/saledatereport/detail",$data);
	       
			 }else{
	       
		    
		   
		  
 	$date_array = array('from_date' =>  date('Y-m-d') , 'to_date' => date('Y-m-d') );
	$data['report1']=  $date_array;
	$data['report']=  $this->mod_salereport->get_details($date_array);
	//echo "<pre>";print_r($data['report']);exit;		

			$table='tbl_company';       
        	$data['company'] = $this->mod_common->get_all_records($table,"*");

				$data['from_date'] = date('Y-m-d');
				$data['to_date'] = date('Y-m-d');
				$data["title"] = "Today Sale  Report";


				if($this->input->post('from_date')!='')
				{
				$data['from_date'] = trim($this->input->post('from_date'));
				$data['to_date'] = trim($this->input->post('to_date'));
				$data["title"] = "Sale B/W Date Report";

			}
  
	            $this->load->view($this->session->userdata('language')."/saledatereport/detail",$data);
	}
	}
	public function item_report()
	{								

$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
			$data['report']=  $this->mod_salereport->get_details_item_report($this->input->post());
			//echo "<pre>";var_dump($data['report']);
			//pm($data['report']);


			$table='tbl_company';       
			//pm($data['report']);
        	$data['company'] = $this->mod_common->get_all_records($table,"*");

			$data['from_date'] = date('Y-m-d');
			$data['to_date'] = date('Y-m-d');
			$data["title"] = "Today Sale  Report";

			$new_date['from_date']=$this->input->post('from_date');
			$new_date['to_date']=$this->input->post('to_date');
			$data['one_date_report'] = $this->mod_customerstockledger->getdate_stock_report($new_date,2);


			if($this->input->post('from_date')!='')
			{
				$data['from_date'] = trim($this->input->post('from_date'));
				$data['to_date'] = trim($this->input->post('to_date'));
				$data["title"] = "Sale B/W Date Report";
			}
			if (!$data['report']) {

	            $this->load->view($this->session->userdata('language')."/saledatereport/detail_report",$data);
	        } else {
	            $this->session->set_flashdata('err_message', 'No Record Found.');
	            redirect(SURL . 'SaleDateReport');
	        }
	}
public function item_detail_report($id)
	{
		$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
		$daterange = $this->input->post('daterangee');
		$location = $this->input->post('location');
		$date=explode("/",$daterange);
		$fdate=$date[0];
		$tdate=$date[1];
	 $sql=$this->db->query("SELECT * from `tblmaterial_coding` where  materialcode='$id'")->row_array();
		
               
			$data['shartcode'] = $sql['shartcode'];
               $data['itemname'] = $sql['itemname'];
			$data['itemreport']=$this->db->query("SELECT tbl_issue_goods_detail.qty, tbl_issue_goods_detail.total_amount, tbl_issue_goods_detail.sprice,tbl_issue_goods.* FROM `tbl_issue_goods_detail` INNER JOIN `tbl_issue_goods` ON `tbl_issue_goods_detail`.`ig_detail_id` = `tbl_issue_goods`.`issuenos`  
				where   levelid='$id' and  tbl_issue_goods.order_date  BETWEEN '$fdate' AND '$tdate' and tbl_issue_goods.sale_point_id='$location'")->result_array();
			//print_r($data['itemreport']);exit;
				
		
		$data['daterange'] = $this->input->post('daterangee');
		$table='tbl_company';       
       		$data['company'] = $this->mod_common->get_all_records($table,"*");
				if ($data['itemreport']) {
	            $data["title"] = "Item Detail Sale Report";
	            $this->load->view($this->session->userdata('language')."/itemwiseReport/item_detail_report_item",$data);
	        }
	        else{
	        	$this->session->set_flashdata('err_message', 'No Record Found.');
	        	 redirect(SURL . 'itemwiseReport');
			}
	}
	public function item_report_detail()
	 {		
	 $data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];																	
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			
			$data['acode'] = $this->input->post('acode');
				
				// echo "<pre>"; var_dump($this->input->post());exit;
			if($this->input->post('from_date')!='')
			{ 
				 $data['daterange'] = trim($this->input->post('from_date').'/'.$this->input->post('to_date'));
				$new_date['from_date']=$this->input->post('from_date');
				 $new_date['to_date']=$this->input->post('to_date');
				  $data['location']=$this->input->post('location');
				  $data['class_name']=$this->input->post("class_name");
		 
		 $data['item']=$this->input->post("item");
		 $data['category']=$this->input->post("category");
				$data['sale'] = $this->mod_customerstockledger->getsale_ledger($this->input->post());
		// echo "<pre>"; var_dump( $data['sale']);exit;		
				$date_for_item['to_date']=$this->input->post('to_date');
				
				$data['from_date'] = $new_date['from_date'];
				$data['to_date'] = $new_date['to_date'];
			}	
			else 
			{
				
			}



		//	echo "<pre>"; var_dump( $data);exit;	  
			$table='tbl_company';       
       		$data['company'] = $this->mod_common->get_all_records($table,"*");


			if ($data['sale']) {
	            $data["title"] = "Item Wise Sale Report";
	            $this->load->view($this->session->userdata('language')."/itemwiseReport/detail_report_item",$data);
	        }
	        else{
	        	$this->session->set_flashdata('err_message', 'No Record Found.');
	        	 redirect(SURL . 'itemwiseReport');
			}
		}
		else
		{
			 redirect(SURL . 'itemwiseReport');
		}
	}
	
		public function single_report()
	{		
	$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];																	
		if($this->input->server('REQUEST_METHOD') == 'POST'){

			
			$data['acode'] = $this->input->post('acode');
			
			$data['daterange'] = trim($this->input->post('from_date').'/'.$this->input->post('to_date'));

			$data['name'] = $this->input->post('name');
			$data['single'] = 1;

			$table='tblacode';
			$where = "acode='".$data['acode']."'";
			$data['name'] = $this->mod_common->select_single_records($table,$where);

			
			$data['sale']=  $this->mod_customerstockledger->getsaler($this->input->post(),3);

			$table='tbl_company';       
       		$data['company'] = $this->mod_common->get_all_records($table,"*");

       	
       		$tables='tblmaterial_coding';       
       		$where='catcode=1';       
       		$data['itemname'] = $this->mod_common->select_array_records($tables,"*",$where);
       		$data['itemname_return'] = $this->mod_common->select_array_records($tables,"*",$where);
			
			$new_date['from_date']=$this->input->post('from_date');
			$new_date['to_date']=$this->input->post('to_date');
			
			
			$data['one_date_report'] = $this->mod_customerstockledger->getdate_stock_report_customer($new_date,2,$data['acode']);

			
			$data['from_date'] = $new_date['from_date'];
			$data['to_date'] = $new_date['to_date'];
			$acode = $data['acode'];
			
			if ($data['sale']) {
	            $data["title"] = "Customer Sale Report";
	            $this->load->view($this->session->userdata('language')."/saledatereport/detail_report_item",$data);
	        }
	        else{
	        	$this->session->set_flashdata('err_message', 'No Record Found.');
	        	 redirect(SURL . 'SaleDateReport/single_customer_report');
			}
		}
		else
		{
			 redirect(SURL . 'SaleDateReport/single_customer_report');
		}
	}
	public function single_customer_report()
	{
		$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
		$data['customer_list'] = $this->mod_customer->getOnlyCustomers();

		$table='tblmaterial_coding';
		$data['items'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "single Party";	
		$this->load->view($this->session->userdata('language')."/saledatereport/single_party_report",$data);
	}

	public function detail($id){
		$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
		
		if($id){
		$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
		$table='tblmaterial_coding';       
        $data['item_list'] = $this->mod_common->get_all_records($table,"*");
		$table='tbl_issue_goods';

		$where = "issuenos='$id'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);


		$data['edit_list'] = $this->mod_salelpg->edit_salelpg($id);
		//echo '<pre>';print_r($data['edit_list']);exit;
		$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Sale B/W Date Report";
		$this->load->view($this->session->userdata('language')."/saledatereport/single",$data);
		}
	}






}
