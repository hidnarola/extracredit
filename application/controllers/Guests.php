<?php

/**
 * Guests Controller - Manage Guests
 * @author REP
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Guests extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('guests_model');
    }

    /**
     * Listing of All Guests
     */
    public function index() {
        $data['title'] = 'Extracredit | Guests';
        $this->template->load('default', 'guests/list_guests', $data);
    }

    /**
     * Get guests data for ajax table
     * */
    public function get_guests() {
        $final['recordsFiltered'] = $final['recordsTotal'] = $this->guests_model->get_guests('count');
        $final['redraw'] = 1;
        $guests = $this->guests_model->get_guests('result');
        $start = $this->input->get('start') + 1;

        foreach ($guests as $key => $val) {
            $guests[$key] = $val;
            $guests[$key]['invite_date'] = date('d M, Y', strtotime($val['invite_date']));
            $guests[$key]['created'] = date('d M, Y', strtotime($val['created']));
        }

        $final['data'] = $guests;
        echo json_encode($final);
    }

    /**
     * Add /edit guests data
     * @param int $id
     * */
    public function add($id = NULL) {
        if (!is_null($id))
            $id = base64_decode($id);
        if (is_numeric($id)) {

            $guest = $this->guests_model->get_guest_details($id);
            if ($guest) {
                $data['guest'] = $guest;
                $data['title'] = 'Extracredit | Edit Guest';
                $data['heading'] = 'Edit Guest';
                $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|callback_check_email_edit[' . $id . ']');
                $data['cities'] = $this->guests_model->sql_select(TBL_CITIES, NULL, ['where' => ['state_id' => $guest['state_id']]]);
                $data['accounts'] = $this->guests_model->sql_select(TBL_ACCOUNTS, 'id,action_matters_campaign,vendor_name', ['where' => ['fund_type_id' => $guest['fund_type_id']]]);

                $logo = $guest['logo'];
            } else {
                show_404();
            }
        } else {
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|callback_is_uniquemail');
            $logo = NULL;
            $data['title'] = 'Extracredit | Add Guest';
            $data['heading'] = 'Add Guest';
            $data['cities'] = [];
            $data['accounts'] = [];
        }
        $data['fund_types'] = $this->guests_model->sql_select(TBL_FUND_TYPES, 'id,type', ['where' => ['is_delete' => 0]]);
        $data['states'] = $this->guests_model->sql_select(TBL_STATES, NULL);

        $this->form_validation->set_rules('fund_type_id', 'Fund Type', 'trim|required');
        $this->form_validation->set_rules('account_id', 'Program/AMC', 'trim|required');

        $this->form_validation->set_rules('firstname', 'First Name', 'trim|required');
        $this->form_validation->set_rules('lastname', 'Last Name', 'trim|required');
//        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('address', 'Address', 'trim|required');
        $this->form_validation->set_rules('state_id', 'State', 'trim|required');
        $this->form_validation->set_rules('city_id', 'City', 'trim|required');
        $this->form_validation->set_rules('zip', 'Zip', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');
        $this->form_validation->set_rules('companyname', 'Company Name', 'trim|required');

        $this->form_validation->set_rules('invite_date', 'Invite Date', 'trim|required');
        $this->form_validation->set_rules('guest_date', 'Guest Date', 'trim|required');
        $this->form_validation->set_rules('AIR_date', 'AIR Date', 'trim|required');
        $this->form_validation->set_rules('assistant', 'Assisatnt', 'trim|required');
        $this->form_validation->set_rules('assistant_email', 'Assisatnt Email', 'trim|required');
        $this->form_validation->set_rules('assistant_phone', 'Assisatnt Phone', 'trim|required');


        if ($this->form_validation->run() == TRUE) {
            $flag = 0;
            if ($_FILES['logo']['name'] != '') {
                $image_data = upload_image('logo', GUEST_IMAGES);
                if (is_array($image_data)) {
                    $flag = 1;
                    $data['logo_validation'] = $image_data['errors'];
                } else {
                    if ($logo != '') {
                        unlink(GUEST_IMAGES . $logo);
                    }
                    $logo = $image_data;
                }
            }
            if ($flag == 0) {
                $dataArr = array(
                    'account_id' => $this->input->post('account_id'),
                    'firstname' => $this->input->post('firstname'),
                    'lastname' => $this->input->post('lastname'),
                    'companyname' => $this->input->post('companyname'),
                    'logo' => $logo,
                    'address' => $this->input->post('address'),
                    'city_id' => $this->input->post('city_id'),
                    'state_id' => $this->input->post('state_id'),
                    'zip' => $this->input->post('zip'),
                    'email' => $this->input->post('email'),
                    'phone' => $this->input->post('phone'),
                    'invite_date' => date('Y-m-d', strtotime($this->input->post('invite_date'))),
                    'guest_date' => date('Y-m-d', strtotime($this->input->post('guest_date'))),
                    'AIR_date' => date('Y-m-d', strtotime($this->input->post('AIR_date'))),
                    'AMC_created' => ($this->input->post('AMC_created') == 1) ? 1 : 0,
                    'assistant' => $this->input->post('assistant'),
                    'assistant_phone' => $this->input->post('assistant_phone'),
                    'assistant_email' => $this->input->post('assistant_email'),
                );

//                p($dataArr,1);

                if (is_numeric($id)) {
                    $dataArr['modified'] = date('Y-m-d H:i:s');
                    $this->guests_model->common_insert_update('update', TBL_GUESTS, $dataArr, ['id' => $id]);
                    $this->session->set_flashdata('success', 'Guest details has been updated successfully.');
                } else {
                    $dataArr['created'] = date('Y-m-d H:i:s');
                    $this->guests_model->common_insert_update('insert', TBL_GUESTS, $dataArr);
                    $this->session->set_flashdata('success', 'Guest has been added successfully');
                }
                redirect('guests');
            }
        }
        $this->template->load('default', 'guests/form', $data);
    }

    /**
     * Edit Guest data
     * @param int $id
     * */
    public function edit($id) {
        $this->add($id);
    }

    /**
     * Delete Guest
     * @param int $id
     * */
    public function delete($id = NULL) {
        $id = base64_decode($id);
        if (is_numeric($id)) {
            $guest = $this->guests_model->get_guest_details($id);
            if ($guest) {
                $update_array = array(
                    'is_delete' => 1
                );
                $this->guests_model->common_insert_update('update', TBL_GUESTS, $update_array, ['id' => $id]);
                $this->session->set_flashdata('success', $guest['firstname'] . ' ' . $guest['lastname'] . ' has been deleted successfully!');
            } else {
                $this->session->set_flashdata('error', 'Invalid request. Please try again!');
            }
            redirect('guests');
        } else {
            show_404();
        }
    }

    /**
     * Callback function to check email validation - Email is unique or not
     * @param string $str
     * @return boolean
     */
    public function is_uniquemail() {
        $email = trim($this->input->post('email'));
        $guest = $this->guests_model->check_unique_email($email);
        if ($guest) {
            $this->form_validation->set_message('is_uniquemail', 'Email address is already in use!');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function check_email_edit($email, $id) {
        $return_value = $this->guests_model->check_email_edit($email, $id);
//        pr($return_value,1);
        if ($return_value == 1) {
            $this->form_validation->set_message('check_email_edit', 'Sorry, This email is already Exists..!');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Listing of All Guests
     */
    public function communication($id = null) {
        $data['title'] = 'Extracredit | Guests Communication';
        $data['id'] = $id;
        $this->template->load('default', 'guests/list_communication', $data);
    }

    /**
     * Get guests communication data for ajax table
     * */
    public function get_guests_communication($id) {
        $id = base64_decode($id);
        $final['recordsFiltered'] = $final['recordsTotal'] = $this->guests_model->get_guests_communication('count', $id);
        $final['redraw'] = 1;
        $guests = $this->guests_model->get_guests_communication('result', $id);
        $start = $this->input->get('start') + 1;

        foreach ($guests as $key => $val) {
            $guests[$key] = $val;
            $guests[$key]['created'] = date('d M, Y', strtotime($val['created']));
        }
        $final['data'] = $guests;
        echo json_encode($final);
    }

    /**
     * Get guests communication data for ajax call for view
     * */
    public function get_communication_by_id() {
        $id = $this->input->post('id');
        $id = base64_decode($id);
        $guest_communication = $this->guests_model->get_guest_communication_details($id);
        $guest_communication['follow_up_date'] = date('d F, Y', strtotime($guest_communication['follow_up_date']));
        $guest_communication['communication_date'] = date('d F, Y', strtotime($guest_communication['communication_date']));
//        $final['data'] = $guest_communication;
        echo json_encode($guest_communication);
    }

    public function add_communication($guest_id = null, $comm_id = null) {
        if (!is_null($guest_id))
            $guest_id = base64_decode($guest_id);
        $comm_id = base64_decode($comm_id);
        if (is_numeric($comm_id)) {
            $guest_communication = $this->guests_model->get_guest_communication_details($comm_id);
            $data['guest_communication'] = $guest_communication;
            $data['title'] = 'Extracredit | Edit Communication';
            $data['heading'] = 'Edit Communication';
            if ($guest_communication['media'] != '')
                $media = $guest_communication['media'];
            else
                $media = NULL;
        } else {
            $media = NULL;
            $data['title'] = 'Extracredit | Add Communication';
            $data['heading'] = 'Add Communication';
            $data['cities'] = [];
            $data['accounts'] = [];
        }
        $this->form_validation->set_rules('note', 'Note', 'trim|required');
        if ($this->form_validation->run() == TRUE) {
            $flag = 0;
            if ($_FILES['media']['name'] != '') {
                $image_data = upload_communication('media', COMMUNICATION_IMAGES);
                if (is_array($image_data)) {
                    $flag = 1;
                    $data['media_validation'] = $image_data['errors'];
                } else {
                    if ($media != '') {
                        unlink(COMMUNICATION_IMAGES . $media);
                    }
                    $media = $image_data;
                }
            }

            if ($flag == 0) {
                $dataArr = array(
                    'note' => $this->input->post('note'),
                    'communication_date' => date('Y-m-d', strtotime($this->input->post('communication_date'))),
                    'follow_up_date' => date('Y-m-d', strtotime($this->input->post('follow_up_date'))),
                    'subject' => $this->input->post('subject'),
                    'guest_id' => $guest_id,
                    'donor_id' => 0,
                    'type' => 2,
                    'media' => $media
                );

                if (is_numeric($comm_id)) {
                    $dataArr['modified'] = date('Y-m-d H:i:s');
                    $this->guests_model->common_insert_update('update', TBL_COMMUNICATIONS, $dataArr, ['id' => $comm_id]);
                    $this->session->set_flashdata('success', 'Guest communication details has been updated successfully.');
                } else {
                    $dataArr['created'] = date('Y-m-d H:i:s');
                    $this->guests_model->common_insert_update('insert', TBL_COMMUNICATIONS, $dataArr);
                    $this->session->set_flashdata('success', 'Guest communication has been added successfully');
                }
                redirect('guests/communication/' . base64_encode($guest_id));
            }
        }
        $this->template->load('default', 'guests/add_communication', $data);
    }

    /**
     * Delete Guest Communication
     * @param int $id
     * */
    public function delete_communication($guest_id = null, $id = NULL) {
        $id = base64_decode($id);
        if (is_numeric($id)) {
            $guest_communication = $this->guests_model->get_guest_communication_details($id);
            if ($guest_communication) {
                $update_array = array(
                    'is_delete' => 1
                );
                $this->guests_model->common_insert_update('update', TBL_COMMUNICATIONS, $update_array, ['id' => $id, 'type' => 2]);
                $this->session->set_flashdata('success', 'Guest communication has been deleted successfully!');
            } else {
                $this->session->set_flashdata('error', 'Invalid request. Please try again!');
            }
            redirect('guests/communication/' . $guest_id);
        } else {
            show_404();
        }
    }

}

/* End of file Guests.php */
/* Location: ./application/controllers/Guests.php */