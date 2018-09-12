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
     * check the communication and send email to that user who added communication
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
                //----- Donor, Guest, Account Name
                if ($communication_manager['donor_fullname'] != '') {
                    $fullname = $communication_manager['donor_fullname'];
                } elseif ($communication_manager['guest_fullname'] != '') {
                    $fullname = $communication_manager['guest_fullname'];
                } elseif ($communication_manager['action_matters_campaign'] != '') {
                    $fullname = $communication_manager['action_matters_campaign'];
                } else {
                    $fullname = $communication_manager['vendor_name'];
                }

                //----- Donor, Guest, Account Email
                if ($communication_manager['donor_email'] != '') {
                    $ofemail = $communication_manager['donor_email'];
                } elseif ($communication_manager['guest_email'] != '') {
                    $ofemail = $communication_manager['guest_email'];
                } elseif ($communication_manager['account_email'] != '') {
                    $ofemail = $communication_manager['account_email'];
                } else {
                    $ofemail = '';
                }

                //----- Donor, Guest, Account Phone Number
                if ($communication_manager['donor_phone'] != '') {
                    $phone_number = $communication_manager['donor_phone'];
                } elseif ($communication_manager['guest_phone'] != '') {
                    $phone_number = $communication_manager['guest_phone'];
                } elseif ($communication_manager['account_phone'] != '') {
                    $phone_number = $communication_manager['account_phone'];
                } else {
                    $phone_number = '';
                }
                $email_data = array(
                    'firstname' => $user['firstname'],
                    'lastname' => $user['lastname'],
                    'fullname' => $fullname,
                    'ofemail' => $ofemail,
                    'phone_number' => $phone_number,
                    'note' => $communication_manager['note'],
                    'email' => $user_email,
                    'category' => $communication_manager['category'],
                    'follow_up_date' => date('m/d/Y', strtotime($follow_up_date)),
                    'url' => site_url('login'),
                    'subject' => 'Follow-up Reminder',
                );
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

    /**
     * Get communication data by its ID 
     * @author KU
     * */
    public function get_communication_by_id() {
        $id = $this->input->post('id');
        $id = base64_decode($id);
        $contact_communication = $this->communication_manager_model->sql_select(TBL_COMMUNICATIONS, '*', ['where' => ['id' => $id, 'is_delete' => 0]], ['single' => true]);
        if (!empty($contact_communication)) {
            $contact_communication['type_id'] = 0;
            $contact_communication['follow_up_date'] = ($contact_communication['follow_up_date'] != '') ? date('m/d/Y', strtotime($contact_communication['follow_up_date'])) : '';
            $contact_communication['communication_date'] = ($contact_communication['communication_date'] != '') ? date('m/d/Y', strtotime($contact_communication['communication_date'])) : '';
            
            if ($contact_communication['donor_id'] != 0)
                $contact_communication['type_id'] = $contact_communication['donor_id'];
            else if ($contact_communication['guest_id'] != 0)
                $contact_communication['type_id'] = $contact_communication['guest_id'];
            else if ($contact_communication['account_id'] != 0)
                $contact_communication['type_id'] = $contact_communication['account_id'];
            else if ($contact_communication['vendor_id'] != 0)
                $contact_communication['type_id'] = $contact_communication['vendor_id'];
            else if ($contact_communication['contact_id'] != 0)
                $contact_communication['type_id'] = $contact_communication['contact_id'];
        }
        echo json_encode($contact_communication);
    }

}
