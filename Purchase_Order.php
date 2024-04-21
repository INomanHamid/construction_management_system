<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_Order extends CI_Controller {
 
	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_transaction","mod_common","mod_admin","common","mod_customerledger"
        ));
        
    }

	public function index()
	{
		if(isset($_POST['submit'])){			
			$from_date=$data["from_date"] = date("Y-m-d", strtotime($_POST['from']));
			
			$to_date=$data["to_date"] = date("Y-m-d", strtotime($_POST['to']));
			
		}else{
			$from_date=$data["from_date"] = date('Y-m-d', strtotime('-7 day'));
			$to_date =$data["to_date"]= date('Y-m-d');
		}

		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
         if($sale_point_id==''){
       $where_sale_point_id="and isnull(tbl_po.sale_point_id)";
        }else{
       $where_sale_point_id="and tbl_po.sale_point_id='$sale_point_id'";
        }

		$data['purchase_order'] = $this->db->query("select * from tblacode inner join tbl_po on tblacode.acode=tbl_po.vendor_code where tbl_po.created_date between '$from_date' and '$to_date' $where_sale_point_id order by tbl_po.po_no  desc")->result_array();
		
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Purchase Order";
			  $data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];	
		$this->load->view($this->session->userdata('language')."/Purchase_Order/manage_purchase_order",$data);
	}


	public function add()
	{    

	  $login_user=$this->session->userdata('id');
      $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
      $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
       $aname=$fix_code['vendor_code'];

        if ($sale_point_id=='0') {
	  	$this->session->set_flashdata('err_message', 'Admin Has No Rights To Add Purchase Order.');
			redirect(SURL . 'Purchase_Order');
			exit();
	  }


       $po_no=$this->input->post("po_no");
       if ($po_no=='') {
       
      $po_no = $this->db->query("select max(po_no) as po_no from tbl_po where sale_point_id='$sale_point_id'")->row_array()['po_no'];
     
      if($po_no==''){
      	 $po_no=1;
      	}else{
      		 $po_no=$po_no+1;
      	}

       }

		$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
	  if($sale_point_id=='1'){$where_cat="where classcode='2'";}else if($sale_point_id=='2'){$where_cat="where classcode='1'";}
        $data['item_list'] = $this->db->query("select * from tblmaterial_coding $where_cat ")->result_array();

		$data['aname'] = $this->db->query("select * from tblacode where general='$aname'")->result_array();
		if($this->input->server('REQUEST_METHOD') == 'POST'){
		
		 $created_date= date('Y-m-d');

			

			$array = array(
		   			   "po_type" =>'Purchase Order',
		   			   "po_no" =>$po_no,
		   			   'sale_point_id' =>$sale_point_id,
					    "pr_no" =>"",
					    "payment_terms" =>$this->input->post("pay_mode"),
					    "vendor_code" =>$this->input->post("supplier_name"),
					    "partial_delivery" =>$this->input->post("partial_delivery"),
					    "remarks" =>$this->input->post("remarks"),
					    "created_by" =>$login_user,
					    "created_date" =>$created_date,
					    "modified_by" =>"",
					    "modified_date" =>"",
					    "po_date" =>$this->input->post("date"),  
					    "po_status" =>$this->input->post("status"),
					    "po_app_id" =>"",
					    'po_app_dt' =>"",
					    'po_close_open' =>"",
					    "total_bill" =>$this->input->post("total_bill"),
					    "tax_percentage" =>$this->input->post("tax_percentage"),
					    "tax_amount" =>$this->input->post("tax_amount"),
					    "net_payable" =>$this->input->post("net_payable"),
				 );
				

		$this->db->trans_start();

		if(empty($this->input->post("edit"))){

			$last_id = $this->mod_common->insert_into_table("tbl_po", $array);
			}else{
		   $last_id = $this->input->post("edit");
			$this->mod_common->update_table("tbl_po",array("trans_id_m"=>$last_id), $array);
		
			 $this->db->query("delete from tbl_po_detail where trans_id_m='$last_id'");

		}
		$i=0;
		$j=1;
		
			 foreach ($this->input->post("item") as $key => $value) {
			 	//pm($this->input->post("item"));exit();
			 	
			   $arrayY = array(
					    
						"trans_id_m"=>$last_id,
						'po_no' => $po_no,
						'srno' => $j,
						'sale_point_id' =>$sale_point_id,
						"itemcode"=>$this->input->post("item")[$i],
				 	    "qty"=>$this->input->post("qty")[$i],
				   		"unit"=>"",
				   		"sed"=>"",
				   		"other"=>"",
				    	"unit_price"=>$this->input->post("unit_price")[$i],
				    	"gst"=>$this->input->post("gst")[$i],
				    	"discount"=>$this->input->post("discount")[$i],
				    	"delivery_place"=>""[$i],
				    	"totalamount" => $this->input->post("amount")[$i],
				    	"ex_gst_amt" => $this->input->post("total_amount")[$i],
					);
			  // pm($arrayY);exit();
				$add= $this->mod_common->insert_into_table("tbl_po_detail", $arrayY);
             	$i++;$j++;
             	
			 }

			

			
			
			$this->db->trans_complete();
redirect(SURL."/Purchase_Order/detail_invoice/".$last_id);
			
			if ($add) {
			 	if(!empty($this->input->post("edit"))){
			 	$this->session->set_flashdata('ok_message', 'Updated Successfully!');
	            redirect(SURL . 'Purchase_Order/');
	        }else{
	        	$this->session->set_flashdata('ok_message', 'Added Successfully!');
	            redirect(SURL . 'Purchase_Order/add');
	            
	        }
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'Purchase_Order/');
	        }
	    	

}
       
        $data["title"] = "Add Purchase With Order";    			
		$this->load->view($this->session->userdata('language')."/Purchase_Order/add",$data);
	}

	public function edit($id)
	{
	  $login_user=$this->session->userdata('id');
      $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
      $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
      $po_no = $this->db->query("select max(po_no) as po_no from tbl_po where sale_point_id='$sale_point_id'")->row_array()['po_no'];
        $aname=$fix_code['vendor_code'];
	    $data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];
		$data['aname'] = $this->db->query("select * from tblacode where general='$aname'")->result_array();
		  if($sale_point_id=='1'){$where_cat="where classcode='2'";}else if($sale_point_id=='2'){$where_cat="where classcode='1'";}
        $data['item_list'] = $this->db->query("select * from tblmaterial_coding $where_cat ")->result_array();

		$data['record'] = $this->db->query("select tbl_po.*,tbl_po_detail.*,tblacode.* from tbl_po inner join tbl_po_detail on tbl_po.trans_id_m = tbl_po_detail.trans_id_m inner join tblacode on tbl_po.vendor_code = tblacode.acode where tbl_po.trans_id_m ='$id'")->result_array()[0];
		//pm($data['record']);

		$data['edit_list'] = $this->db->query("select tblmaterial_coding.*,tbl_po_detail.* from tblmaterial_coding inner join tbl_po_detail on tblmaterial_coding.materialcode=tbl_po_detail.itemcode where tbl_po_detail.trans_id_m ='$id'")->result_array();


		//pm($data['edit_list']);exit();

        $data["filter"] = 'add';
        $data["title"] = "Edit Cash Payment";    			
		$this->load->view($this->session->userdata('language')."/Purchase_Order/add",$data);
	}

	

		public function delete($id) {
			//echo $id;exit();
			 $login_user=$this->session->userdata('id');
      $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
			$po = $this->db->query("select po_no from tbl_po where trans_id_m ='$id' and sale_point_id='$sale_point_id'")->row_array()['po_no'];
			// echo $po;exit();|
			$po_no = $this->db->query("select po_no from tbl_goodsreceiving where po_no ='$po'")->row_array()['po_no'];
		if ($po_no>0) {
			$this->session->set_flashdata('err_message', 'Purchase Is Received Against This Order,So You Can Not Delete This.');
			redirect(SURL . 'Purchase_Order/');
			exit();
			}

		$this->db->trans_start();
        $table = "tbl_po";
        $where = array("trans_id_m"=>$id);
       	$delete = $this->mod_common->delete_record($table, $where);

        $table = "tbl_po_detail";
        $where = array("trans_id_d"=>$id);
        $delete = $this->mod_common->delete_record($table, $where);
		$this->db->trans_complete();

		if ($this->db->trans_status() === TRUE)
		{
		    $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Purchase_Order/');
		}else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Purchase_Order/');
        }
    }
    public function detail_invoice($id){
		if($id){
         //echo "asd";exit();
		//$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
		$con['conditions']=array("trans_id_m"=>$id);
		$data['master_record'] = $this->common->get_single_row("tbl_po",$con);
        $data['res_record'] = $this->db->query("select  * from  tbl_company")->row_array();
         
        
        

		$data['detail_record'] = $this->db->query("SELECT tbl_po_detail.*,tblmaterial_coding.itemname,tblmaterial_coding.unit FROM `tbl_po_detail` inner join tblmaterial_coding on tbl_po_detail.itemcode=tblmaterial_coding.materialcode where trans_id_m='$id'")->result_array();
		
		
		$data['arabic_check']=$this->db->query("select arabic_check from tbl_company")->row_array()['arabic_check'];

		
		$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");
		//exit;
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Customer Invoice";
		$this->load->view($this->session->userdata('language')."/Purchase_Order/detail_invoice",$data);
		}
	}

}
