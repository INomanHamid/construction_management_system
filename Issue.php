<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Issue extends CI_Controller {


	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_vendor","mod_common","mod_customer"
        ));
        
    }
	public function index()
	{   

	
        $login_user=$this->session->userdata('id');
        	if(isset($_POST['submit'])){			
			$from_date = date("Y-m-d", strtotime($_POST['from']));
			
			$to_date = date("Y-m-d", strtotime($_POST['to']));
			
		}else{
			$from_date = date('Y-m-d', strtotime('-15 day'));
			$to_date = date('Y-m-d');
		}

       $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
       $data['sale_list'] = $this->db->query("select * from tbl_issue_goods  where tbl_issue_goods.order_date  between '$from_date' and '$to_date' ")->result_array();
       $data["project_list"]= $this->db->query("SELECT * FROM tbl_projects")->result_array();
       //$data["sale_list"]= $this->db->query("SELECT * FROM tbl_projects")->result_array();
       $data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Issue";
		$this->load->view($this->session->userdata('language')."/Issue/manage",$data);
	}

	public function add_issue()
	{   
		$login_user=$this->session->userdata('id');
		$data["department_list"]= $this->db->query("SELECT * FROM tbldepartment")->result_array();
		$data["project_list"]= $this->db->query("SELECT * FROM tbl_projects")->result_array();
		$data["itemname"] = $this->db->query("select * from tblmaterial_coding ")->result_array();
		$data['customer_list'] = $this->db->query("select * from tblacode where atype='child'")->result_array();
		//pm($itemname);exit();

	 $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location']; 
		 $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
       $aname=$fix_code['vendor_code'];
        $customer=$fix_code['customer_code'];
 
		$data["filter"] = 'add';
		$this->load->view($this->session->userdata('language')."/Issue/add",$data);
	}

	public function add(){
	$this->db->trans_start();
	$login_user=$this->session->userdata('id');
	$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location']; 
		 
    		if($this->input->server('REQUEST_METHOD') == 'POST'){
    		//pm($this->input->post());exit;
			
			$udata['order_date'] =$this->input->post('order_date');
			$udata['projectcode'] =$this->input->post('projectcode');
			$udata['projectcode_sub'] =$this->input->post('projectcode_sub');
			$udata['department'] =$this->input->post('department');
			// $udata['issuedto'] =$this->input->post('customer');
			$udata['total_amount'] =$this->input->post('total_amt');
			$udata['disc_amt'] =$this->input->post('discount');
			$udata['net_payable'] =$this->input->post('net_payable');
			$udata['rcv_amt'] =$this->input->post('received_amount');
		


            if(empty($this->input->post("id"))){
		    
			$last_id = $this->mod_common->insert_into_table("tbl_issue_goods", $udata);
		 
		}  if(!empty($this->input->post("id"))){
		 
			$last_id = $this->input->post("id");
             $this->mod_common->update_table("tbl_issue_goods",array("issuenos"=>$last_id), $udata);
             $this->db->query("delete from tbl_issue_goods_detail where ig_detail_id='$last_id'");
              $vno = $last_id."-IS";
             $this->db->query("delete from tbltrans_master where vno='$vno'");
             $this->db->query("delete from tbltrans_detail where vno='$vno'");
           
            
             $res = $this->input->post("id");
           	
		}
				$i=0;
			
			foreach ($this->input->post("item") as $key => $value) {
				
$array1 = array(
							

						"ig_detail_id"=>$last_id,
	    				"created_date"=>$this->input->post("order_date"),
	    				"itemid"=>$this->input->post("item")[$i],
	    				"qty"=>$this->input->post("qty")[$i],
	    				"sprice"=>$this->input->post("rate")[$i],
	    				"amount"=>$this->input->post("amount")[$i],
						
						);

			$res=	$this->mod_common->insert_into_table("tbl_issue_goods_detail",$array1);
			//pm($res);exit();
			   $item = $this->input->post("item")[$i];
  $itemanem = $this->db->query("select itemname from tblmaterial_coding where materialcode='$item'")->result_array()[0]['itemname'];
 
 $qty=$this->input->post("qty")[$i]; 
 $tamount=$this->input->post("amount")[$i];
 $rate=$this->input->post("rate")[$i];

  $nar ="$itemanem qty: $qty @ $rate amt=$tamount";

 $i++;	}
  $nar ='Sale against '. $nar.','."$itemanem qty $qty@$rate amt=$tamount";
  $vno = $last_id."-IS";
    $array = array(
	    				"vno"=>$vno,
	    				"vtype"=>"PV",
	    				"damount"=>$this->input->post('net_payable'),
	    				"camount"=>$this->input->post('net_payable'),
	    				"created_date"=>$this->input->post('order_date'),
	    				"created_by"=>$login_user,
	    				"svtype"=>"WOP",
	    			  );	

	    $master_id=$this->mod_common->insert_into_table("tbltrans_master",$array);
	    $j=1;
	    $stock_code='2003001001';
	    $sale_code='3001001001';
	    $array = array(
	    				"vno"=>$vno,
	    				"srno"=>$j,
	    				"acode"=>$sale_code,//stock code will come here
	    				"damount"=>$this->input->post('net_payable'),
	    				"camount"=>"0",
	    				"remarks"=>$nar,
	    				"ig_detail_id"=>$master_id,
	    				"vtype"=>"PS",
	    				"svtype"=>"WOP",
	    				"vdate"=>$this->input->post('order_date'),
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_detail",$array);
	    $j++;

	    $array = array(
	    				"vno"=>$vno,
	    				"srno"=>$j,
	    			    "acode"=>$stock_code,
	    				"damount"=>"0",
	    				"camount"=>$this->input->post('net_payable'),
	    				"remarks"=>$nar,
	    				"ig_detail_id"=>$master_id,
	    				"vtype"=>"PS",
	    				"svtype"=>"WOP",
	    				"vdate"=>$this->input->post('order_date'),
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_detail",$array);



	

			$this->db->trans_complete();
			if ($res) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully added.');
	            redirect(SURL . 'Issue/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'Issue/');
	        }
	    }
	}
		public function show_class_course()
	{
		$project=$this->input->post("project");
		//echo $class_id;
		$projectcode_sub =$_SESSION["projectcode_sub"];
		//pm($projectcode_sub);exit;
		 $class_record = $this->db->query("SELECT * FROM tbl_sub_projects where projectid='$project'")->result_array(); //pm($class_record);exit;?>
		
		 	<?php

												foreach ($class_record as $key => $data) {
												# code...
												?>
		<option value="<?php echo $data['sub_projectid']; ?>"<?php if($data['sub_project_title']==$projectcode_sub){ ?> selected <?php } ?>><?php echo ucwords($data['sub_project_title']); ?></option>
	
	<?php	 }
				
}
		public function get_unit_1()
	{
		$item=$this->input->post("item");
		$sub_id=$this->input->post("sub_id");
		 $result = $this->db->query("select * from tbl_projects_boq where itemcode='$item' and sub_project_id='$sub_id'")->row_array();
		
		echo json_encode($result);
		 
				
}

		public function show_sub_project()
	{
		$projectcode_sub=$this->input->post("projectcode_sub");
		$projectid=$this->input->post("projectid");


		//echo $class_id;
		 $class_record = $this->db->query("SELECT * FROM tblmaterial_coding where materialcode in (select itemcode from tbl_projects_boq where sub_project_id='$projectcode_sub' and projectid='$projectid')")->result_array(); 
 
		 ?>
		 	<option value="">Select Item</option>
		<?php

												foreach ($class_record as $key => $data) {
												# code...
												?>
			<option value="<?php echo $data['materialcode']; ?>"><?php echo ucwords($data['itemname']); ?></option>
	
	<?php	 }
				
}



	public function delete($id) {

		$this->db->trans_start();
		#-------------delete record--------------#
        $table = "tbl_issue_goods";
        $where = "issuenos = '" . $id . "'";
        $delete = $this->mod_common->delete_record($table, $where);
         $table = "tbl_issue_goods_detail";
        $where = "ig_detail_id = '" . $id . "'";
        $delete = $this->mod_common->delete_record($table, $where);
        $vno = $id."-IS";
             $this->db->query("delete from tbltrans_master where vno='$vno'");
             $this->db->query("delete from tbltrans_detail where vno='$vno'");
         
	$this->db->trans_complete();
        if ($delete) {
            $this->session->set_flashdata('ok_message', 'You have successfully deleted.');
            redirect(SURL . 'Issue/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Issue/');
        }
    }

    public function edit($id){
		$data['customer_list'] = $this->db->query("select * from tblacode where atype='child'")->result_array();
         $data['itemname'] = $this->db->query("select * from tblmaterial_coding")->result_array();
         $data["department_list"]= $this->db->query("SELECT * FROM tbldepartment")->result_array();
		$data["project_list"]= $this->db->query("SELECT * FROM tbl_projects")->result_array();
		if($id){
			 $data['record'] = $this->db->query("select * from tbl_issue_goods where issuenos='$id'")->row_array();
			  $data['record_detail'] = $this->db->query("select tbl_issue_goods_detail.*,tblmaterial_coding.* from tbl_issue_goods_detail inner join tblmaterial_coding on tblmaterial_coding.materialcode=tbl_issue_goods_detail.itemid  where tbl_issue_goods_detail.ig_detail_id='$id'")->result_array();
                //pm($data['record_detail']);exit();
			$data["filter"] = 'add';
		 
		$this->load->view($this->session->userdata('language')."/Issue/add", $data);
		}

	
	}
	public function get_stock(){
	 	$item =$this->input->post('item');
	 	$order_date =$this->input->post('order_date');
	 	$sub_id =$this->input->post('sub_id');

	 	

    // $purchase_qty = $this->db->query("select sum(quantity) as purchase_qty from tbl_projects_boq  where itemcode='$item' and sub_project_id='$sub_id' ")->row_array()['purchase_qty'];
	 	$purchase_qty = $this->db->query("select sum(quantity) as purchase_qty from tbl_goodsreceiving_detail  where itemid='$item' ")->row_array()['purchase_qty'];
     // echo "select sum(quantity) as purchase_qty from tbl_projects_boq  where itemcode='$item' and sub_project_id='$sub_id' and  created_date<='$order_date'";exit();

    $sale_qty = $this->db->query("select sum(qty) as sale_qty from tbl_issue_goods_detail where itemid='$item' ")->row_array()['sale_qty'];

    // echo $sale_qty;exit;
    $stock=$purchase_qty-$sale_qty;
//pm($design_no);exit();
    echo $stock;

	 }




}
