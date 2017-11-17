<?php

/**
 * Communication_manager Controller - Manage Communication manager
 * @author REP
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Communication_manager extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('communication_manager_model');
    }

    /**
     * Listing of All Communication_manager details
     */
    public function index() {
        checkPrivileges('communication_manager', 'view');
        $data['perArr'] = checkPrivileges('communication_manager');
        $data['title'] = 'Extracredit | Communication Manager';
        $this->template->load('default', 'communication_manager/list_communication_manager', $data);
    }

    /**
     * Get Communication_manager data for ajax table
     */
    public function get_communication_manager() {
        checkPrivileges('communication_manager', 'view');
        $final['recordsFiltered'] = $final['recordsTotal'] = $this->communication_manager_model->get_communication_manager('count');
        $final['redraw'] = 1;
        $final['data'] = $this->communication_manager_model->get_communication_manager('result');
        echo json_encode($final);
    }

    /**
     * check the communication and send email to that user
     */
    public function check_communication($id = NULL) {
        checkPrivileges('communication_manager', 'delete');
        $id = base64_decode($id);
        if (is_numeric($id)) {
            $communication_manager = $this->communication_manager_model->get_communication_manager_details($id);
            if ($communication_manager) {
                $update_array = array(
                    'status' => 1
                );
                $this->communication_manager_model->common_insert_update('update', TBL_COMMUNICATIONS_MANAGER, $update_array, ['id' => $id]);
                $user_id = $communication_manager['user_id'];
                $follow_up_date = $communication_manager['follow_up_date'];
                $user = $this->users_model->get_user_detail(['id' => $user_id]);
                $user_email = $user['email'];
                $email_data = ['firstname' => $user['firstname'], 'lastname' => $user['lastname'], 'email' => $user_email,'category' => $communication_manager['category'], 'follow_up_date' => $follow_up_date, 'url' => site_url('login'), 'subject' => 'Follow Up Date Reminder - Extracredit'];
                send_email($user_email, 'check_communication_manager', $email_data);

                $this->session->set_flashdata('success', 'Communication has been checked and email sent successfully!');
            } else {
                $this->session->set_flashdata('error', 'Invalid request. Please try again!');
            }
            redirect('communication_manager');
        } else {
            show_404();
        }
    }

}
