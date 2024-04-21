<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Common extends CI_Controller {


	public function __construct() {

        parent::__construct();
        error_reporting(0);

        $this->load->model(array(
            "mod_common"  , "mod_sale"
        ));
        
    }
	public function price()
	{
		$id=$this->input->post('item_id');
		$date=$this->input->post('date');
		$condition = $_POST['condition'];
 
		$item_value=$this->mod_sale->getcurrent_price_avg($date);
		$price=0;
		
		//return   $price_11.'~~'.$price_45.'~~'.$price_15.'~~'.$price_6.'~~'.$price_18.'~~'.$price_30.'~~'.$price_35;
		$total_array=explode('~~',$item_value);
		
		if($id=='1')	{		$price=$total_array[0]; 	}
		if($id=='2')	{		$price=$total_array[2]; 	}
		if($id=='3')	{		$price=$total_array[1]; 	}
		if($id=='4')	{		$price=$total_array[3]; 	}
		if($id=='6')	{		$price=$total_array[4]; 	}
		if($id=='7')	{		$price=$total_array[5]; 	}
		if($id=='5')	{		$price=$total_array[6]; 	}
		
		echo $price;
		exit;
		
 
	}
	public function stock()
	{
		$id=$this->input->post('item_id');
		$date=$this->input->post('date');
		$condition = $_POST['condition'];

		$cate_id=5;

		$where_item = "materialcode = '" . $id . "'";

		$item_value=$this->mod_common->select_single_records('tblmaterial_coding',$where_item);

		if(!empty($item_value))
		{
			$cate_id=$item_value['catcode'];
		}
		echo $today_stock=$this->mod_common->stock($id,$condition,$date,1);
		exit;
		
 
	}
	public function stock_tank()
	{
		$id=$this->input->post('item_id');
		$date=$this->input->post('date');
		 
		echo $today_stock=$this->mod_common->stock_tank($id,$date);
		exit;
		
 
	}
	public function stock_tank_closing()
	{
		$id=$this->input->post('item_id');
		$date=$this->input->post('date');
		 //echo $date;
		echo $today_stock=$this->mod_common->stock_tank($id,$date);
		exit;
		
 
	}
	public function current_rate()
	{
		 
		  $trans_date=$this->input->post('dt');

		echo $today_stock=$this->mod_sale->getcurrent_price($trans_date);
		exit;
		 
	}
	
	
	public function similaritem()
	{
		$id=$this->input->post('item_id');
		$date=$this->input->post('date');
		$itemnameint='';
		$catcode='';

		$where_item = "materialcode = '" . $id . "'";

		$item_value=$this->mod_common->select_single_records('tblmaterial_coding',$where_item);

		if(!empty($item_value))
		{
			$itemnameint=$item_value['itemnameint'];
			$catcode=$item_value['catcode'];
		}
	 

        $where_cat_id = array('itemnameint' => $itemnameint);
        $data['item_list']= $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_cat_id);
        
		print $catcode.'_';
         	
        foreach ($data['item_list'] as $key => $value) { ?>
        	<option value="<?php echo $value['materialcode']; ?>" <?php if($id==$value['materialcode']) { ?> selected <?php } ?>><?php echo $value['itemname']; ?></option>
        <?php }
        exit;

	  
	}
	
	public function customergst()
	{
		$acode=$this->input->post('acode');
		 
		$where_item = "acode = '" . $acode . "'";

		$item_value=$this->mod_common->select_single_records('tblacode',$where_item);

		if(!empty($item_value))
		{
		 	$reg_no=$item_value['reg_no'];
		 	$vat_no=$item_value['vat_no'];
		}
	 if($reg_no!='' || $vat_no!=''){
		 print '17';
		 
	 }else{
		 print '18';
	 }

        
	  
	}
	public function export()
	{ 
	//echo "string";exit();
	$out = '';
$file="";
//Next we'll check to see if our variables posted and if they did we'll simply append them to out.
if (isset($_POST['csv_hdr'])) {
$out .= $_POST['csv_hdr'];
$out .= "\n";
}

if (isset($_POST['csv_output'])) {
$out .= $_POST['csv_output'];
}

//Now we're ready to create a file. This method generates a filename based on the current date & time.
$filename = $file."_".date("Y-m-d_H-i",time());

//Generate the CSV file header
header("Content-type: application/vnd.ms-excel");
header("Content-disposition: csv" . date("Y-m-d") . ".csv");
header("Content-disposition: filename=".$filename.".csv");
//Print the contents of out to the generated file.
print $out;

//Exit the script
exit;
        
	  
	}
}