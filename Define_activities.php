<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Define_activities extends CI_Controller
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
        // $data['customer_list'] = $this->mod_customer->getOnlyCustomers();

        $table = 'tbl_activities';
        $data['activity_list'] = $this->db->query("select * from tbl_activities ")->result_array();
        // $table = 'tblacode';
        // $where = array('general' => 3001000000, 'ac_status' => "Active");
        // $data['salesman'] = $this->mod_common->select_array_records($table, "*", $where);
        $data["title"] = "Manage Define Activities";
        $this->load->view($this->session->userdata('language') . "/define_activities/manage", $data);
    }
    public function add_activity()
    {
        // $data['customer_list'] = $this->mod_customer->getOnlyCustomers();

        $data['record'] = $this->db->query("select * from tbl_activities ")->result_array();

        $data["title"] = "Define Activities";

        $this->load->view($this->session->userdata('language') . "/define_activities/add", $data);
    }
    public function add()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $array = array(
                'activity_name' => $activity_name = $this->input->post('activityname'),
                'status' => $this->input->post('status')
            );
            if (empty($this->input->post("edit"))) {
                $check = $this->db->query("SELECT * FROM tbl_activities WHERE activity_name ='$activity_name' ")->row_array();
                if (!empty($check)) {
                    $this->session->set_flashdata('err_message', 'Activity Already Added!');
                    redirect(SURL . 'Define_activities/index/');
                }
                $add = $this->mod_common->insert_into_table("tbl_activities", $array);
            } else {
                $edit_id = $this->input->post("edit");
                $check = $this->db->query("SELECT * FROM tbl_activities WHERE activity_name ='$activity_name'  and id !='$edit_id' ")->row_array();
                if (!empty($check)) {
                    $this->session->set_flashdata('err_message', 'Activity Already Added!');
                    redirect(SURL . 'Define_activities/index/');
                }
                $update = $this->mod_common->update_table("tbl_activities", array("id" => $edit_id), $array);
            }


            if ($add || $update) {
                $this->session->set_flashdata('ok_message', 'Activity has been added successfully.');
                redirect(SURL . 'Define_activities/index/');
            } else {
                $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
                redirect(SURL . 'Define_activities/index/');
            }
        }
    }

    public function edit($id = '')
    {
        if ($id) {

            $date_array = array('id' => $id);
            $data['edit_list'] =  $this->mod_common->select_single_records('tbl_activities', $date_array);
            $data["filter"] = '';
            #----load view----------#
            $data["title"] = "Update Activities";
            $this->load->view($this->session->userdata('language') . "/define_activities/add", $data);
        }
    }

    public function delete($id = '')
    {
        #-------------delete record--------------#
        $table = "tbl_activities";
        $where = "id = '" . $id . "'";
        $delete = $this->mod_common->delete_record($table, $where);

        if ($delete) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Define_activities/index/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Define_activities/index/');
        }
    }
}
