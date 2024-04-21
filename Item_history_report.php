<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Item_history_report extends CI_Controller {



	public function __construct() {
        parent::__construct();

        $this->load->model(array(
           "mod_common","mod_salelpg"
        ));
        
    }

	public function index()
	{
		
		#----load view----------#
			$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];

		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array();
        $data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];
        

        if($sale_point_id !=''){ $where_sale_point_id= "where sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
		$data['location']=$this->db->query("select * from tbl_sales_point $where_sale_point_id")->result_array();
		//$data['item_list'] =$this->db->query("select * from tblmaterial_coding where status='Active'")->result_array();
		$data["title"] = "Item History Report";	
		$this->load->view($this->session->userdata('language')."/Item_history_report/search",$data);
	}
		function search()
	{
	 $name=$this->input->post('name');
	 $itemname =$this->db->query("select materialcode,itemname,bar_code from tblmaterial_coding WHERE itemname LIKE '%$name%' LIMIT 20")->result_array();
	?>

	   <?php
		foreach ($itemname as $key => $data) {	
		$val=$data['materialcode'].'_'.$data['itemname'];								
			?> 
		

			<option   value="<?php echo $val; ?>"></option>
			


		<?php } ?>

<?php	
	}

	public function report()
	{
			$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];

		if($this->input->server('REQUEST_METHOD') == 'POST'){
        $from_date =$data['from_date']=$this->input->post("from_date");
		 $to_date=$data['to_date']=$this->input->post("to_date");
		 $date= date("Y-m-d", strtotime($from_date ."-1 days" )); 
		 $item_id=$data['item_id']=$this->input->post("item_id");
	    $location=$data['location']=$this->input->post("location");
	   
	    $balance_qty =$data['balance_qty']= $this->mod_common->stock($item_id,$date,$location);
	   // echo $balance_qty;exit;
	    $data["title"] = "Item&nbsp;History&nbsp;Report";
	    $table='tbl_company';       
       		$data['company'] = $this->mod_common->get_all_records($table,"*");
			
		 $this->load->view($this->session->userdata('language')."/Item_history_report/single",$data);
                        		
           
	    }
	}
	


}
