<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Activities_progress extends CI_Controller
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
        $data['project_list'] =  $this->db->query("select * from tbl_projects where status='Active'")->result_array();
        $data['record'] = $this->db->query("select * from tbl_sequence_activity_master ")->result_array();
        $data['records'] = $this->db->query("select * from tbl_activities_progress_master ")->result_array();

        $data["title"] = "Activities Progress";
        $this->load->view($this->session->userdata('language') . "/Activities_progress/add", $data);
    }

    public function add()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $array_master = array(
                'project' => $this->input->post('project'),
                'sub_project' => $this->input->post('sub_project'),
            );
            $edit_id = $this->input->post("edit");
            if (empty($edit_id)) {
                $check = $this->db->query("SELECT * FROM tbl_activities_progress_master WHERE project='$project' and sub_project='$sub_project' ")->row_array();
                if (!empty($check)) {
                    $this->session->set_flashdata('err_message', 'Activity Already Added!');
                    redirect(SURL . 'Activities_progress/index/');
                }
                $master_id = $this->mod_common->insert_into_table("tbl_activities_progress_master", $array_master);
            } else {
                $edit_id = $this->input->post('edit');
                $check = $this->db->query("SELECT * FROM tbl_activities_progress_master WHERE project='$project' and sub_project='$sub_project' and id !='$edit_id' ")->row_array();
                if (!empty($check)) {
                    $this->session->set_flashdata('err_message', 'Activity Already Added!');
                    redirect(SURL . 'Activities_progress/index/');
                }
                $this->mod_common->update_table("tbl_activities_progress_master", array("id" => $edit_id), $array_master);
                $master_id = $edit_id;
                $this->db->query("DELETE FROM tbl_activities_progress_detail WHERE masterid='$master_id'");
            }
            $activities = $this->input->post("activity");
            foreach ($activities as $key => $value) {
                $array_detail = array(
                    'masterid' => $master_id,
                    'activity_id' => $this->input->post('activity')[$key],
                    'start_date' => $this->input->post('start_date')[$key],
                    'end_date' => $this->input->post('end_date')[$key],
                    'actual_start_date' => $this->input->post('actual_start_date')[$key],
                    'actual_end_date' => $this->input->post('actual_end_date')[$key],
                    'activity_status' => $this->input->post('activity_status')[$key],
                    'status' => $this->input->post('status')[$key]
                );
                $detail_id = $this->mod_common->insert_into_table("tbl_activities_progress_detail", $array_detail);
                if (!$detail_id) {
                    $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
                    redirect(SURL . 'Activities_progress/index');
                    return;
                }
            }
            redirect(SURL . 'Activities_progress/index');
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
    public function get_project_date()
    {
        $project = $this->input->post("project");
        $sub_project = $this->input->post("sub_project");
        $record = $this->db->query("SELECT sdate,edate FROM tbl_sub_projects WHERE projectid='$project' AND sub_projectid = '$sub_project'")->row_array();
        echo $record['sdate'] . "|" . $record['edate'];
    }
    public function chart_btn()
    {

        $project = $this->input->post("project");
        $sub_project = $this->input->post("sub_project");
?>
        <a href="<?php echo SURL . "Project_progress/index/" . $project . "_" . $sub_project ?>" target="_blank" class="btn btn-info btn-xs" id="submitbtn" style="margin-left: -20%;">
            <i class="ace-icon fa fa-check bigger-110"></i>
            Click Here To See Project Chart
        </a>
        <?php
    }
    public function get_activity_progress()
    {
        $project = $this->input->post("project");
        $sub_project = $this->input->post("sub_project");
        // $this->change_date_base_status($project, $sub_project);
        $masterid = $this->db->query("SELECT id FROM `tbl_sequence_activity_master` where project='$project' and sub_project='$sub_project'")->row_array()['id'];
        $this->change_actual_status($project, $sub_project, $masterid);
        $activities_records = $this->db->query("SELECT * FROM `tbl_sequence_activity_detail` WHERE masterid='$masterid' ")->result_array();
        $progressmasterid = $this->db->query("SELECT id FROM `tbl_activities_progress_master` where project='$project' and sub_project='$sub_project'")->row_array()['id'];

        $srno = 0;
        foreach ($activities_records as $record) {
            $srno++;
            $id = $record['activity_id'];
            $activity_details = $this->db->query("SELECT * FROM `tbl_activities` where status='Active' and id='$id' ")->row_array();
            $act_id = $activity_details['id'];
            $activity_record = $this->db->query("SELECT * FROM `tbl_activities_progress_detail` WHERE activity_id='$act_id' and masterid='$progressmasterid' ")->row_array();
        ?>
            <tr>
                <td align="right"><?= $srno ?></td>
                <td class="col-xs-2" align="left">
                    <input readonly type="text" class="activity form-control" value="<?= $activity_details['activity_name']; ?>" style="width: 100%;">
                    <input readonly type="hidden" class="activity form-control" name="activity[]" value="<?= $activity_details['id']; ?>" style="width: 100%;" id="activity_<?= $srno ?>">
                </td>
                <td class="col-xs-2" align="center">
                    <div class="input-group">
                        <input readonly class="form-control" name="start_date[]" id="start_date_<?= $activity_record['id'] ?>" type="text" required value="<?= $activity_record['start_date']; ?>">
                    </div>
                </td>
                <td class="col-xs-2" align="center">
                    <div class="input-group">
                        <input readonly class="form-control " name="end_date[]" maxlength="10" minlength="10" id="end_date_<?= $activity_record['id'] ?>" type="text" required value="<?= $activity_record['end_date']; ?>">
                    </div>
                </td>
                <td class="col-xs-2" align="center">
                    <div class="input-group">
                        <input readonly class="form-control" name="actual_start_date[]" id="actual_start_date_<?= $activity_record['id'] ?>" type="text" required value="<?= $activity_record['actual_start_date']; ?>">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar bigger-110"></i>
                        </span>
                    </div>
                </td>
                <td class="col-xs-2" align="center">
                    <div class="input-group">
                        <input readonly class="form-control date-picker" name="actual_end_date[]" id="actual_end_date_<?= $activity_record['id'] ?>" type="text" required value="<?php if (is_null($activity_record['actual_end_date'])) {
                                                                                                                                                                                        echo $activity_record['end_date'];
                                                                                                                                                                                    } else {
                                                                                                                                                                                        echo  $activity_record['actual_end_date'];
                                                                                                                                                                                    }; ?>">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar bigger-110"></i>
                        </span>
                    </div>
                </td>
                <td class="col-xs-1" align="center">
                    <?php if ($activity_record['activity_status'] == 'Start') { ?>
                        <button type="button" style="font-weight: bolder;" onclick="updateStatus(<?= $activity_record['id']; ?>, 'Closed')" id="actual_status_button_<?= $activity_record['id'] ?>" class="btn btn-sm btn-danger" data-value="Closed">Closed</button>
                    <?php } else { ?>
                        <button type="button" style="font-weight: bolder;" onclick="updateStatus(<?= $activity_record['id']; ?>, 'Start')" id="actual_status_button_<?= $activity_record['id'] ?>" class="btn btn-sm btn-primary" data-value="Start">Start</button>
                    <?php } ?>
                </td>
                <td class="col-xs-2" align="center" id="status_<?= $srno ?>">
                    <input readonly class="form-control " name="status[]" type="text" required value="<?= $activity_record['status']; ?>">
                    <!-- <span align="center"  ><b>Pending</b></span> -->
                </td>
                <!-- <input type="hidden" id="edit_<?= $srno ?>" name="edit" value="<?= $masterid; ?>" /> -->
            </tr>
<?php
        }
        // }
    }
    public function change_status()
    {
        $progress = $this->input->post("progress");
        $status = $this->input->post("status");
        $actual_end_date = $this->input->post("actual_end_date");
        $end_date = $this->input->post("end_date");
        if ($status == 'Closed') {
            if ($actual_end_date <= $end_date) {
                $ddata['status'] = "In Time";
            } elseif ($actual_end_date > $end_date) {
                $ddata['status'] = "Late";
            }
            $ddata['actual_end_date'] = $actual_end_date;
        } else {
            $ddata['actual_end_date'] = NULL;
            $ddata['status'] = "Start";
        }
        $ddata['activity_status'] = $status;
        $response = $this->mod_common->update_table("tbl_activities_progress_detail", array("id" => $progress), $ddata);

        if ($response) {
            echo 'success';
        }
    }
    public function change_actual_status($project, $sub_project, $sequence_id)
    {

        $masterid = $this->db->query("SELECT id FROM `tbl_activities_progress_master` where project='$project' and sub_project='$sub_project'")->row_array()['id'];
        if (empty($masterid)) {
            $mdata = array(
                'project' => $this->input->post('project'),
                'sub_project' => $this->input->post('sub_project'),
            );
            $masterid = $this->mod_common->insert_into_table("tbl_activities_progress_master", $mdata);
        }
        $this->db->query("DELETE from tbl_activities_progress_temp where masterid='$masterid' ");

        $progress_detail = $this->db->query("SELECT * from tbl_activities_progress_detail where masterid='$masterid'")->result_array();
        foreach ($progress_detail as $key => $value) {
            $tdata['masterid'] = $masterid;
            $tdata['activity_id'] = $value['activity_id'];
            $tdata['start_date'] = $value['start_date'];
            $tdata['end_date'] = $value['end_date'];
            $tdata['actual_start_date'] = $value['actual_start_date'];
            $tdata['actual_end_date'] = $value['actual_end_date'];
            $tdata['activity_status'] = $value['activity_status'];
            $tdata['status'] = $value['status'];

            $this->mod_common->insert_into_table("tbl_activities_progress_temp", $tdata);
        }

        $this->db->query("DELETE from tbl_activities_progress_detail where masterid='$masterid'");

        $activities = $this->db->query("SELECT * FROM `tbl_sequence_activity_detail` WHERE masterid='$sequence_id' ")->result_array();
        foreach ($activities as $record) {
            $from_date = $record['from_date'];
            $to_date = $record['to_date'];
            $activity_id = $record['activity_id'];

            $temp_progress = $this->db->query("SELECT * from tbl_activities_progress_temp where masterid='$masterid' and activity_id='$activity_id'")->row_array();

            $ddata['masterid'] = $masterid;
            $ddata['activity_id'] = $activity_id;
            $ddata['start_date'] = $from_date;
            $ddata['end_date'] = $to_date;
            $ddata['actual_start_date'] = $from_date;
            $ddata['actual_end_date'] = NULL;

            $today_date = date('Y-m-d');
            if ($from_date <= $today_date) {
                $ddata['activity_status'] = 'Start';
                $ddata['status'] = 'Start';
            } elseif ($from_date >= $today_date) {
                $ddata['activity_status'] = 'Pending';
                $ddata['status'] = 'Pending';
            }
            if ($temp_progress['activity_status'] == 'Closed') {
                $ddata['actual_end_date'] = $temp_progress['actual_end_date'];
                $ddata['activity_status'] = $temp_progress['activity_status'];
                $ddata['status'] = $temp_progress['status'];
            }
            if ($temp_progress['activity_status'] == 'Start') {
                $ddata['actual_start_date'] = $temp_progress['actual_start_date'];
                $ddata['activity_status'] = $temp_progress['activity_status'];
                $ddata['status'] = $temp_progress['status'];
            }
            $this->mod_common->insert_into_table("tbl_activities_progress_detail", $ddata);

            if ($this->db->affected_rows()) {
                echo 'success';
            }
        }
    }
}
