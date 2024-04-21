<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Sub_boq_detail extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array(
			"mod_vendor", "mod_common", "mod_customer"
		));
	}
	public function index($id)
	{
		//echo $id;exit();
		$data['project_list'] =  $this->db->query("select * from tbl_projects where status='Active'")->result_array();
		//pm($data['project_list']);exit;
		$data['records'] = $this->db->query("select * from tbl_sequence_activity_master ")->result_array();

		$data['sub_project_name'] =  $this->db->query("select sub_project_title from tbl_sub_projects where sub_projectid='$id'")->row_array()['sub_project_title'];
		$projectid =  $this->db->query("select projectid from tbl_sub_projects where sub_projectid='$id'")->row_array()['projectid'];
		$data['projectid'] =  $this->db->query("select projectid from tbl_sub_projects where sub_projectid='$id'")->row_array()['projectid'];
		$data['sub_project_id'] = $id;
		$data['project_name'] =  $this->db->query("select title from tbl_projects where projectid='$projectid'")->row_array()['title'];
		//echo ($data['project_name']);exit();
		//echo ($data['sub_project_name']);exit();
		$data['course_list'] =  $this->db->query("select tbllocation.*,tbl_projects.* from tbllocation inner join tbl_projects on tbllocation.loccode=tbl_projects.location  order by tbl_projects.projectid desc")->result_array();
		$data['item_list'] =  $this->db->query("select * from tblmaterial_coding where status='Active'")->result_array();
		//$data['course_list'] =$this->db->query("select * From tbl_projects order by projectid desc")->result_array();
		//pm($data['emp_list']);exit();
		$data["filter"] = '';
		$this->load->view($this->session->userdata('language') . "/Sub_boq_detail/manage_boq", $data);
	}
	public function add_boq()
	{
		// $data['add'] = $this->db->query("select * from sms_template")->result_array();
		// 		$data["filter"] = 'add';
		$data['location'] = $this->db->query("select * from tbllocation ")->result_array();
		$this->load->view($this->session->userdata('language') . "/Sub_boq_detail/add_boq", $data);
	}
	public function add()
	{

		$this->db->trans_start();
		// $login_user=$this->session->userdata("id");
		// $date = date("Y-m-d");
		$item_id = $this->input->post("item_id");
		$quantity = $this->input->post("quantity");
		$purchase_rate = $this->input->post("purchase_rate");
		$unit = $this->input->post("unit");
		$amount = $this->input->post("amount");
		$remarks = $this->input->post("remarks");
		$projectid = $this->input->post("projectid");
		$sub_project_id = $this->input->post("sub_project_id");
		$activity_id = $this->input->post("activity_id");

		// $created_by =>$login_user;
		// $created_date =>$date;
		// $updated_by =>$login_user;
		// $updated_date =>$date;
		//      $array=array(
		//    	        "itemcode"=>$item_id,
		// "quantity"=>$quantity,
		// "purchase_rate"=>$purchase_rate,
		// "unit"=>$unit,
		// "amount"=>$amount,
		// "remarks"=>$remarks,
		// "projectid"=>$projectid,
		// "sub_project_id"=>$sub_project_id,
		//  );
		$query = "insert into tbl_projects_boq (itemcode,quantity,purchase_rate,unit ,amount,remarks,projectid,sub_project_id,activity_id)values('$item_id','$quantity','$purchase_rate','$unit','$amount','$remarks','$projectid','$sub_project_id','$activity_id')";
		$this->db->query($query);
		$this->db->trans_complete();
		// $last_id = $this->mod_common->insert_into_table("tbl_projects_boq", $array);
	}
	public function update()
	{
		$this->db->trans_start();
		$quantity = $this->input->post("quantity");
		$purchase_rate = $this->input->post("purchase_rate");
		$amount = $this->input->post("amount");
		$remarks = $this->input->post("remarks");
		$boqid = $this->input->post("boqid");
		$query = "update tbl_projects_boq set quantity='$quantity',purchase_rate='$purchase_rate',amount='$amount',remarks='$remarks' where boqid='$boqid'";
		$this->db->query($query);
		$this->db->trans_complete();
	}
	public function get_unit_1()
	{
		$item_1 = $this->input->post("item_1");
		$result = $this->db->query("select * from tblmaterial_coding where materialcode='$item_1'")->row_array();
		echo json_encode($result);
	}
	public function get_row()
	{
		$projectid = $this->input->post('projectid');
		$sub_project_id = $this->input->post('sub_project_id');
		$edit_list = $this->db->query("select *  from tbl_projects_boq where projectid='$projectid' and sub_project_id ='$sub_project_id'")->result_array();
		$count = 0;
		for ($i = 0; $i < count($edit_list); $i++) {
			$itemcode = $edit_list[$i]['itemcode'];
			$itemname = $this->db->query("select itemname from tblmaterial_coding where materialcode='$itemcode'")->row_array()['itemname'];
			$count++;
?>
			<tr id="row<?php echo $i; ?>">
				<td style="width:10%">
					<input type="text" id="itemname_<?php echo $i; ?>" readonly="" value="<?php echo  $itemname; ?>" name="itemname[]">
					<input type="hidden" id="item_id_<?php echo $i; ?>" readonly="" value="<?php echo  $edit_list[$i]['itemcode']; ?>" name="id[]">
				</td>
				</td>
				<td>
					<input style="width:150%" type="text" id="unit_<?php echo $i; ?>" readonly="" value="<?php echo  $edit_list[$i]['unit']; ?>" name="unit[]">
				</td>
				<td>
					<input style="width:116%;margin-left: 15%;" onkeypress="return /[0-9 . ]/i.test(event.key)" type="text" id="quantity_<?php echo $i; ?>" maxlength="7" readonly="" value="<?php echo  $edit_list[$i]['quantity']; ?>" name="quantity[]">
				</td>
				<td>
					<input style="width:108%;margin-left: 6%;" onkeypress="return /[0-9 . ]/i.test(event.key)" type="text" id="purchase_rate_<?php echo $i; ?>" maxlength="7" readonly="" value="<?php echo  $edit_list[$i]['purchase_rate']; ?>" name="purchase_rate[]">
				<td>
					<input type="text" id="amount_<?php echo $i; ?>" readonly="" value="<?php echo  $edit_list[$i]['amount']; ?>" name="amount[]">
				</td>
				<td>
					<input type="text" id="remarks_<?php echo $i; ?>" readonly="" value="<?php echo  $edit_list[$i]['remarks']; ?>" name="remarks[]">
				</td>
				<td style='display: inline-flex; border: 0px;'>
					<input style='width: 100%' type="button" id="edit_button<?php echo $i; ?>" value="Edit" data-id1='<?php echo $i; ?>' class="editrow btn btn-xs btn-success" onclick="edit_row(<?php echo $i; ?>)">
					<input style='display:none;width:100%' type="button" id="save_button<?php echo $i; ?>" data-id1='<?php echo $i; ?>' value="Save" class="btn btn-xs btn-warning" onclick="savechecking(<?php echo $i; ?>)">
					<input style='width: 100%' type="button" value="Delete" class="btn btn-xs btn-danger btn_del" onclick="delete_record(<?php echo  $edit_list[$i]['boqid']; ?>)">
				</td>
			</tr>
			<input type="hidden" name="id" id="boqid_<?php echo $i; ?>" value="<?php echo $edit_list[$i]['boqid']; ?>" />
			<style type="text/css">
				#data_table1 {
					display: block;
				}
			</style>
<?php
		}
	}
	public function delete()
	{
		$id = $this->input->post('id');
		//echo $id;exit;
		$this->db->query("delete from tbl_projects_boq where boqid='$id'");
		echo 'Deleted succesfully';
	}
	public function get_subproject()
    {
        $projectid = $this->input->post("projectid");
        $sub_project_edit = $this->input->post("sub_project_edit");
        if ($sub_project_edit > 0) {
            $where_sub_project = "and sub_projectid='$sub_project_edit'";
        } else {
            $where_sub_project = '';
        }
        $sub_project_list = $this->db->query("SELECT * FROM tbl_sub_projects WHERE projectid='$projectid' AND status = 'Active' $where_sub_project")->result_array();

        $options = '';

        foreach ($sub_project_list as $sub_project) {

            $selected = ($sub_project['sub_projectid'] == $sub_project_edit) ? 'selected' : '';

            $options .= '<option value="' . $sub_project['sub_projectid'] . '" ' . $selected . '>' . $sub_project['sub_project_title'] . '</option>';
        }

        echo $options;
    }
    public function get_activity()
    {
        $projectid = $this->input->post("projectid");
        $sub_project_id = $this->input->post("sub_project_id");
        $masterid = $this->db->query("SELECT id FROM tbl_sequence_activity_master WHERE project='$projectid' AND sub_project='$sub_project_id'")->row_array()['id'];
        $activity_list = $this->db->query("SELECT activity_id FROM tbl_sequence_activity_detail WHERE masterid='$masterid'")->result_array();

        foreach ($activity_list as $value) {
            $activity_id = $value['activity_id'];
            $activity_name = $this->db->query("SELECT activity_name FROM tbl_activities WHERE id='$activity_id'")->row_array()['activity_name'];

?>
            <option value="<?php echo $activity_id; ?>"><?php echo $activity_name; ?></option>
<?php }
    }
	public function edit($id)
	{
		//    $data['institute_name'] =$this->db->query("select business_name from tbl_company")->row_array();
		// $data['city_list'] =$this->db->query("select * from tbl_city")->result_array();
		// $data['desg_list'] =$this->db->query("select * from tbldesgcode where status='Active'")->result_array();
		$data['location'] = $this->db->query("select * from tbllocation ")->result_array();
		if ($id) {
			$data['record'] = $this->db->query("select * from tbl_projects where projectid='$id'")->result_array()[0];
			// pm($data['record'] );
			$this->load->view($this->session->userdata('language') . "/Project_listing/add_project", $data);
		}
	}
}
