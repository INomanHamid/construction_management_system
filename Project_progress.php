<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Project_progress extends CI_Controller
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
        $res = explode('_', $id);
        $project = $res[0];
        $sub_project = $res[1];
        if ($id != '') {
            $data['where_project'] = "where project='$project' and sub_project='$sub_project'";
        } else {
            $data['where_project'] = '';
        }
        $data["title"] = "Activities Progress";

        $this->load->view($this->session->userdata('language') . "/Project_progress/add", $data);
    }
}
