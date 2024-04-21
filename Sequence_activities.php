<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sequence_activities extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model(array(
            "mod_common"
        ));
    }
    public function index()
    {

        $data['record'] = $this->db->query("select * from tbl_sequence_activity_master order by id desc")->result_array();

        $data["title"] = "Sequence Activities";

        $this->load->view($this->session->userdata('language') . "/sequence_activities/manage", $data);
    }
    public function add_sequence_activity()
    {

        $data['project_list'] =  $this->db->query("select * from tbl_projects where status='Active'")->result_array();
        $data['record'] = $this->db->query("select * from tbl_sequence_activity_master ")->result_array();

        $data["title"] = "Sequence Activities";

        $this->load->view($this->session->userdata('language') . "/sequence_activities/add", $data);
    }
    public function add()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $array_master = array(
                'project' => $project = $this->input->post('project'),
                'sub_project' => $sub_project = $this->input->post('sub_project')
            );


            $edit_id = $this->input->post("edit");

            if (empty($edit_id)) {

                $check = $this->db->query("SELECT * FROM tbl_sequence_activity_master WHERE project='$project' and sub_project='$sub_project' ")->row_array();
                if (!empty($check)) {
                    $this->session->set_flashdata('err_message', 'Activity Already Added!');
                    redirect(SURL . 'Sequence_activities/index/');
                }
                $master_id = $this->mod_common->insert_into_table("tbl_sequence_activity_master", $array_master);
            } else {
                $edit_id = $this->input->post("edit");
                $check = $this->db->query("SELECT * FROM tbl_sequence_activity_master WHERE project='$project' and sub_project='$sub_project' and id !='$edit_id' ")->row_array();
                if (!empty($check)) {
                    $this->session->set_flashdata('err_message', 'Activity Already Added!');
                    redirect(SURL . 'Sequence_activities/index/');
                }
                $this->mod_common->update_table("tbl_sequence_activity_master", array("id" => $edit_id), $array_master);
                $master_id = $edit_id;
                $this->db->query("DELETE FROM tbl_sequence_activity_detail WHERE masterid='$master_id'");
            }


            $activities = $this->input->post("activity");

            foreach ($activities as $key => $value) {
                if ($value > 0) {
                    $array_detail = array(
                        'activity_id' => $this->input->post('activity')[$key],
                        'from_date' => $this->input->post('from_date')[$key],
                        'to_date' => $this->input->post('to_date')[$key],
                        'masterid' => $master_id
                    );

                    $res_detail = $this->mod_common->insert_into_table("tbl_sequence_activity_detail", $array_detail);

                    if (!$res_detail) {
                        $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
                        redirect(SURL . 'Sequence_activities/index/');
                        return;
                    }
                }
            }

            $this->session->set_flashdata('ok_message', 'Activity has been added successfully.');
            redirect(SURL . 'Sequence_activities/index/');
        }
    }

    public function edit($id = '')
    {
        if ($id) {
            $data['edit_records'] = $this->db->query("select * from tbl_sequence_activity_detail where masterid='$id' ")->result_array();

            $data['record'] = $record = $this->db->query("select * from tbl_sequence_activity_master where id='$id'")->row_array();

            $projectid = $record['project'];
            $data['project_list'] =  $this->db->query("select * from tbl_projects where status='Active' and projectid='$projectid' ")->result_array();


            $data["filter"] = '';
            #----load view----------#
            $data["title"] = "Update Activities";
            $this->load->view($this->session->userdata('language') . "/sequence_activities/add", $data);
        }
    }
    public function get_subproject()
    {
        $project = $this->input->post("project");
        $sub_project_edit = $this->input->post("sub_project_edit");
        if ($sub_project_edit > 0) {
            $where_sub_project = "and sub_projectid='$sub_project_edit'";
        } else {
            $where_sub_project = '';
        }
        $sub_project_list = $this->db->query("SELECT * FROM tbl_sub_projects WHERE projectid='$project' AND status = 'Active' $where_sub_project")->result_array();

        $options = '';

        foreach ($sub_project_list as $sub_project) {

            $selected = ($sub_project['sub_projectid'] == $sub_project_edit) ? 'selected' : '';

            $options .= '<option value="' . $sub_project['sub_projectid'] . '" ' . $selected . '>' . $sub_project['sub_project_title'] . '</option>';
        }

        echo $options;
    }
    public function get_activity()
    {
        $project = $this->input->post("project");
        $sub_project = $this->input->post("sub_project");
        $masterid = $this->db->query("SELECT id FROM tbl_map_activity_master WHERE project='$project' AND sub_project = '$sub_project'")->row_array()['id'];
        $activity_list = $this->db->query("SELECT activity_id FROM tbl_map_activity_detail WHERE masterid='$masterid'")->result_array();

        foreach ($activity_list as $value) {
            $activity_id = $value['activity_id'];
            $activity_name = $this->db->query("SELECT activity_name FROM tbl_activities WHERE id='$activity_id'")->row_array()['activity_name'];

?>
            <option value="<?php echo $activity_id ?>"><?php echo $activity_name ?></option>
<?php }
    }

    public function get_project_date()
    {
        $project = $this->input->post("project");
        $sub_project = $this->input->post("sub_project");
        $record = $this->db->query("SELECT sdate,edate FROM tbl_sub_projects WHERE projectid='$project' AND sub_projectid = '$sub_project'")->row_array();
        echo $record['sdate'] . "|" . $record['edate'];
    }


    public function delete($id = '')
    {
        #-------------delete record--------------#
        $delete =  $this->db->query("delete from tbl_sequence_activity_detail where masterid='$id'");
        $delete = $this->db->query("delete from tbl_sequence_activity_master where id='$id'");

        if ($delete) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Sequence_activities/index/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Sequence_activities/index/');
        }
    }
}
