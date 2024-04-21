<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Activity_mapping extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model(array(
            "mod_common"
        ));
    }
    public function index($id = '')
    {
        $data['project_list'] =  $this->db->query("select * from tbl_projects where status='Active' ")->result_array();
        $res = explode('_', $id);
        $data['project'] = $res[0];
        $data['sub_project'] = $res[1];
        $data["title"] = "Activity Mapping";
        $this->load->view($this->session->userdata('language') . "/Activity_mapping/add", $data);
    }

    public function get_subproject()
    {
        $project = $this->input->post("project");
        $sub_project_list = $this->db->query("SELECT * FROM tbl_sub_projects WHERE projectid='$project' AND status = 'Active'")->result_array();
        $sub_project = $_SESSION['sub_project'];
        foreach ($sub_project_list as $sub_project) { ?>
            <option value="<?php echo $sub_project['sub_projectid'] ?>" <?php if ($sub_project == $sub_project['sub_projectid']) { ?> selected <?php } ?>><?php echo  $sub_project['sub_project_title'] ?></option>
        <?php }
    }

    public function add()
    {

        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $array_master = array(
                'project' => $project = $this->input->post('project'),
                'sub_project' => $sub_project = $this->input->post('sub_project'),
            );
            if (empty($this->input->post("edit"))) {
                $check = $this->db->query("SELECT * FROM tbl_map_activity_master WHERE project='$project' and sub_project='$sub_project' ")->row_array();
                if (!empty($check)) {
                    $this->session->set_flashdata('err_message', 'Activity Already Added!');
                    redirect(SURL . 'Activity_mapping/index/');
                }
                $master_id = $this->mod_common->insert_into_table("tbl_map_activity_master", $array_master);
            } else {
                $edit_id = $this->input->post('edit');
                $check = $this->db->query("SELECT * FROM tbl_map_activity_master WHERE project='$project' and sub_project='$sub_project' and id !='$edit_id' ")->row_array();
                if (!empty($check)) {
                    $this->session->set_flashdata('err_message', 'Activity Already Added!');
                    redirect(SURL . 'Activity_mapping/index/');
                }
                $this->mod_common->update_table("tbl_map_activity_master", array("id" => $edit_id), $array_master);
                $master_id = $edit_id;
                $this->db->query("DELETE from tbl_map_activity_detail where masterid='$master_id'");
            }
            foreach ($this->input->post("select_activity") as $key => $value) {
                if ($value > 0) {
                    $array_detail = array(
                        'activity_id' => $value,
                        'masterid' => $master_id
                    );

                    $res_detail = $this->mod_common->insert_into_table("tbl_map_activity_detail", $array_detail);

                    if (!$res_detail) {

                        $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
                        redirect(SURL . 'Activity_mapping/index/');
                        return;
                    }
                }
            }

            $this->session->set_flashdata('ok_message', 'Activity has been added successfully.');
            redirect(SURL . 'Activity_mapping/index/' . $project . "_" . $sub_project);
        }
    }
    public function get_activity()
    {
        $project = $_POST['project'];
        $sub_project = $_POST['sub_project'];

        $srno = 0;
        if ($project && $sub_project > 0 ) {
        $activities_records = $this->db->query("SELECT * FROM `tbl_activities` where status='Active' ")->result_array();
        foreach ($activities_records as $record) {
            $srno++;
            $id = $record['id'];
            $masterid = $this->db->query("SELECT id FROM `tbl_map_activity_master` where project='$project' and sub_project='$sub_project'")->row_array()['id'];
            $activity_map = $this->db->query("SELECT activity_id FROM `tbl_map_activity_detail` WHERE activity_id ='$id' and masterid= '$masterid' ")->row_array()['activity_id'];
        ?>
            <tr>
                <td align="left" class="col-xs-1"><?= $srno ?></td>
                <td align="left" class="col-xs-1">
                    <input type="text" class="form-control" value="<?= $record['activity_name']  ?>" placeholder="Enter an activity..." readonly>
                    <input type="hidden" class="form-control" value="<?= $record['id'] ?>">
                </td>
                <td align="left" class="col-xs-1" id="select_activity_<?= $srno ?>">

                    <input type="checkbox" name="select_activity[]" id="select_activity" <?php if ($activity_map) {
                                                                                                echo "checked";
                                                                                            } ?> value="<?= $record['id'] ?>">
                    <input type="hidden" id="edit" name="edit" value="<?php echo $masterid; ?>" />

                </td>
            </tr>
<?php }
    }
}
}
