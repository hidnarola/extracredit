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
        $this->list_id = '1dfb45ca7d';
    }

    /**
     * Listing of All Guests
     */
    public function index() {
        checkPrivileges('guest', 'view');
        $data['perArr'] = checkPrivileges('guest');
        $data['comperArr'] = checkPrivileges('guests_communication');
        $data['title'] = 'Extracredit | Guests';
        $this->template->load('default', 'guests/list_guests', $data);
    }

    /**
     * Get guests data for ajax table
     * */
    public function get_guests() {
        checkPrivileges('guest', 'view');
        $data['perArr'] = checkPrivileges('guest');
        $data['comperArr'] = checkPrivileges('guests_communication');
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
        checkPrivileges('guest', 'add');
        $data['perArr'] = checkPrivileges('guest');
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
                $city_id = $this->guests_model->sql_select(TBL_CITIES, NULL, ['where' => ['id' => $guest['city_id']]]);
                $data['city_id'] = $city_id[0]['name'];
                $state_id = $this->guests_model->sql_select(TBL_STATES, NULL, ['where' => ['id' => $guest['state_id']]]);
                $data['state_id'] = $state_id[0]['name'];
                $data['state_short'] = $state_id[0]['short_name'];
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
//        $data['fund_types'] = $this->guests_model->sql_select(TBL_FUND_TYPES, 'id,name,type', ['where' => ['is_delete' => 0]]);
        $data['fund_types'] = $this->guests_model->custom_Query('SELECT id,name FROM ' . TBL_FUND_TYPES . ' WHERE is_delete=0 AND (type=0 OR type=2)')->result_array();

        $data['states'] = $this->guests_model->sql_select(TBL_STATES, NULL);

        $this->form_validation->set_rules('fund_type_id', 'Fund Type', 'trim|required');
        $this->form_validation->set_rules('account_id', 'Program/AMC', 'trim|required');

        $this->form_validation->set_rules('firstname', 'First Name', 'trim|required');
        $this->form_validation->set_rules('lastname', 'Last Name', 'trim|required');
//        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('address', 'Address', 'trim|required');
        $this->form_validation->set_rules('state_id', 'State', 'trim|required|callback_state_validation');
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
            $state = $this->input->post('state_short');
            $city = $this->input->post('city_id');
//            echo $state;
            $check_state = $this->guests_model->check_state($state);
            if (!empty($check_state)) {
                $state_id = $check_state['id'];
            }
//            p($check_state);
            $check_city = $this->guests_model->check_city($city, $state_id);
//            p($check_city);
            if (!empty($check_city)) {
                $city_id = $check_city['id'];
            }
            if ($flag == 0) {
                $dataArr = array(
                    'account_id' => $this->input->post('account_id'),
                    'firstname' => $this->input->post('firstname'),
                    'lastname' => $this->input->post('lastname'),
                    'companyname' => $this->input->post('companyname'),
                    'logo' => $logo,
                    'address' => $this->input->post('address'),
                    'city_id' => $city_id,
                    'state_id' => $state_id,
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

                if (is_numeric($id)) {
                    $dataArr['modified'] = date('Y-m-d H:i:s');
                    $this->guests_model->common_insert_update('update', TBL_GUESTS, $dataArr, ['id' => $id]);

                    if ($guest['email'] != $dataArr['email']) {
                        $subscriber = get_mailchimp_subscriber($guest['email']);
                        if (!empty($subscriber)) {
                            $interests = $subscriber['interests'];
                            if ($interests[DONORS_GROUP_ID] == 1 || $interests[ACCOUNTS_GROUP_ID] == 1) {
                                $mailchimp_data = array(
                                    'email_address' => $guest['email'],
                                    'interests' => array(GUESTS_GROUP_ID => false)
                                );
                            } else {
                                //-- Update old entry to unsubscribed and add new to subscribed
                                $mailchimp_data = array(
                                    'email_address' => $guest['email'],
                                    'status' => 'unsubscribed', // "subscribed","unsubscribed","cleaned","pending"
                                    'interests' => array(GUESTS_GROUP_ID => false)
                                );
                            }
                            mailchimp($mailchimp_data);
                        }

                        $mailchimp_data = array(
                            'email_address' => $dataArr['email'],
                            'status' => 'subscribed', // "subscribed","unsubscribed","cleaned","pending"
                            'merge_fields' => [
                                'FNAME' => $dataArr['firstname'],
                                'LNAME' => $dataArr['lastname']
                            ],
                            'interests' => array(GUESTS_GROUP_ID => true)
                        );
                        mailchimp($mailchimp_data);
                    }

                    $this->session->set_flashdata('success', 'Guest details has been updated successfully.');
                } else {
                    $dataArr['created'] = date('Y-m-d H:i:s');
                    $this->guests_model->common_insert_update('insert', TBL_GUESTS, $dataArr);
                    //-- Insert account email into mailchimp subscriber list
                    $mailchimp_data = array(
                        'email_address' => $dataArr['email'],
                        'status' => 'subscribed', // "subscribed","unsubscribed","cleaned","pending"
                        'merge_fields' => [
                            'FNAME' => $dataArr['firstname'],
                            'LNAME' => $dataArr['lastname'],
                        ],
                        'interests' => array(GUESTS_GROUP_ID => true)
                    );
                    mailchimp($mailchimp_data);
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
        checkPrivileges('guest', 'edit');
        $data['perArr'] = checkPrivileges('guest');
        $this->add($id);
    }

    /**
     * Callback Validate function to check state is valid or not
     * @return boolean
     * @author KU
     */
    public function state_validation() {
        $state_code = $this->input->post('state_short');
        $state = $this->guests_model->sql_select(TBL_STATES, 'id', ['where' => ['short_name' => $state_code]], ['single' => true]);
        if (empty($state)) {
            $this->form_validation->set_message('state_validation', 'State does not exist in the database! Please enter correct zipcode');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Delete Guest
     * @param int $id
     * */
    public function delete($id = NULL) {
        checkPrivileges('guest', 'delete');
        $data['perArr'] = checkPrivileges('guest');
        $id = base64_decode($id);
        if (is_numeric($id)) {
            $guest = $this->guests_model->get_guest_details($id);
            if ($guest) {
                $update_array = array(
                    'is_delete' => 1
                );
                $this->guests_model->common_insert_update('update', TBL_GUESTS, $update_array, ['id' => $id]);
                //--delete subscriber from list
                $subscriber = get_mailchimp_subscriber($guest['email']);
                if (!empty($subscriber)) {
                    $interests = $subscriber['interests'];
                    if ($interests[DONORS_GROUP_ID] == 1 || $interests[ACCOUNTS_GROUP_ID] == 1) {
                        $mailchimp_data = array(
                            'email_address' => $guest['email'],
                            'interests' => array(GUESTS_GROUP_ID => false)
                        );
                    } else {
                        //-- Update old entry to unsubscribed and add new to subscribed
                        $mailchimp_data = array(
                            'email_address' => $guest['email'],
                            'status' => 'unsubscribed', // "subscribed","unsubscribed","cleaned","pending"
                            'interests' => array(GUESTS_GROUP_ID => false)
                        );
                    }
                    mailchimp($mailchimp_data);
                }

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
        checkPrivileges('guests_communication', 'view');
        $data['perArr'] = checkPrivileges('guests_communication');
        $data['title'] = 'Extracredit | Guests Communication';
        $data['id'] = $id;
        $this->template->load('default', 'guests/list_communication', $data);
    }

    /**
     * Get guests communication data for ajax table
     * */
    public function get_guests_communication($id) {
        checkPrivileges('guests_communication', 'view');
        $data['perArr'] = checkPrivileges('guests_communication');
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
        echo json_encode($guest_communication);
    }

    public function add_communication($guest_id = null, $comm_id = null) {
        checkPrivileges('guests_communication', 'add');
        $data['perArr'] = checkPrivileges('guests_communication');
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
        checkPrivileges('guests_communication', 'delete');
        $data['perArr'] = checkPrivileges('guests_communication');
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

    /**
     * View user
     * @return : Partial View
     * @author : REP
     */
    public function view_guest() {
        $guest_id = base64_decode($this->input->post('id'));
        $guest = $this->guests_model->get_guest_details_view($guest_id);
        if ($guest) {
//            p($guest,1);
            $guest['invite_date'] = date('d F, Y', strtotime($guest['invite_date']));
            $guest['guest_date'] = date('d F, Y', strtotime($guest['guest_date']));
            $guest['AIR_date'] = date('d F, Y', strtotime($guest['AIR_date']));
            $data['guest_details'] = $guest;
            return $this->load->view('guests/guest_view', $data);
        } else {
            show_404();
        }
    }

    /**
     * Import guest data from CSV file
     * @author REP
     */
    public function import_guest() {
        $fileDirectory = GUEST_CSV;
        $config['overwrite'] = FALSE;
        $config['remove_spaces'] = TRUE;
        $config['upload_path'] = GUEST_CSV;
        $config['allowed_types'] = 'csv|CSV';
        $this->load->library('upload', $config);

        //-- Upload csv file
        if ($this->upload->do_upload('import_guest')) {
            $fileDetails = $this->upload->data();

            $accounts = $this->guests_model->get_all_accounts();
            $account_name_arr = $cities_arr = $states_arr = [];
            foreach ($accounts as $account) {
                if (!empty($account['action_matters_campaign'])) {
                    $account_name_arr[$account['id']] = $account['action_matters_campaign'];
                } else {
                    $account_name_arr[$account['id']] = $account['vendor_name'];
                }
            }

            //-- Get cities array
            $cities = $this->guests_model->sql_select(TBL_CITIES);
            foreach ($cities as $city) {
                $cities_arr[$city['id']] = $city['name'];
                $states_arr[$city['id']] = $city['state_id'];
            }

            //-- Get guest emails
            $guest_emails = $this->guests_model->sql_select(TBL_GUESTS, 'email', ['where' => ['is_delete' => 0]]);
            $guest_emails_arr = array_column($guest_emails, 'email');

            $row = 1;
            $handle = fopen($fileDirectory . "/" . $fileDetails['file_name'], "r");
            $guest_data = $check_account = $check_email = $check_email_valid = $check_city = $check_invite_date = $check_guest_date = $check_AIR_date = $check_assiatnt_email = $check_phone = $check_assistant_phone = $check_assistant_email = $imported_emails = [];
            if (($data2 = fgetcsv($handle)) !== FALSE) {
                $data_format2 = array('firstname', 'lastname', 'companyname', 'invite_date', 'guest_date', 'AIR_date', 'AMC_created', 'amc', 'address', 'city', 'zip', 'email', 'phone', 'assistant', 'assistant_phone', 'assistant_email');

                //-- check if first colums is according to predefined row
                if ($data_format2 == $data2) {
                    while (($col_data = fgetcsv($handle)) !== FALSE) {
                        $guest = [];
//                        p($col_data);
                        if ($col_data[0] == '' || $col_data[1] == '' || $col_data[2] == '' || $col_data[3] == '' || $col_data[4] == '' || $col_data[5] == '' || $col_data[6] == '' || $col_data[7] == '' || $col_data[8] == '' || $col_data[9] == '' || $col_data[10] == '' || $col_data[11] == '' || $col_data[12] == '' || $col_data[12] == '' || $col_data[13] == '' || $col_data[14] == '' || $col_data[15] == '') {
                            fclose($handle);
                            $this->session->set_flashdata('error', 'Some fields are missing in Row No. ' . $row);
                            redirect('guests');
                        } else {
                            $row++;
                            $guest['firstname'] = $col_data[0];
                            $guest['lastname'] = $col_data[1];
                            $guest['companyname'] = $col_data[2];

                            //-- invite Date, guest date and AIR date validation 
                            //-- Check date is valid or not
                            $date_arr = explode('-', $col_data[3]);
                            if (count($date_arr) == 3) {
                                list($y, $m, $d) = explode('-', $col_data[3]);
                                if (!checkdate($m, $d, $y)) {
                                    $check_invite_date[] = $row;
                                } else {
                                    $guest['invite_date'] = $col_data[3];
                                }
                            } else {
                                $check_invite_date[] = $row;
                            }

                            //-- Check Guest date is valid or not
                            $date_arr = explode('-', $col_data[4]);
                            if (count($date_arr) == 3) {
                                list($y, $m, $d) = explode('-', $col_data[4]);
                                if (!checkdate($m, $d, $y)) {
                                    $check_guest_date[] = $row;
                                } else {
                                    $guest['guest_date'] = $col_data[4];
                                }
                            } else {
                                $check_guest_date[] = $row;
                            }
                            //-- Check post date is valid or not
                            $date_arr = explode('-', $col_data[5]);
                            if (count($date_arr) == 3) {
                                list($y, $m, $d) = explode('-', $col_data[5]);
                                if (!checkdate($m, $d, $y)) {
                                    $check_AIR_date[] = $row;
                                } else {
                                    $guest['AIR_date'] = $col_data[5];
                                }
                            } else {
                                $check_AIR_date[] = $row;
                            }

                            $guest['AMC_created'] = $col_data[6];
                            //-- Check if program/amc name is valid or not if not then add it into array
                            $account_name_arr = array_map('strtolower', $account_name_arr);
                            if (array_search(strtolower($col_data[7]), $account_name_arr) != FALSE) {
                                $guest['account_id'] = array_search(strtolower($col_data[7]), $account_name_arr);
                            } else {
                                $check_account[] = $row;
                            }

                            $guest['address'] = $col_data[8];
                            //--check city is valid or not
                            if (array_search(strtolower($col_data[9]), array_map('strtolower', $cities_arr)) != FALSE) {
                                $guest['city_id'] = array_search(strtolower($col_data[9]), array_map('strtolower', $cities_arr));
                                $guest['state_id'] = $states_arr[$guest['city_id']];
                            } else {
                                $check_city[] = $row;
                            }

                            $guest['zip'] = $col_data[10];
                            //--check email is unique or not
                            $guest_emails_arr = array_map('strtolower', $guest_emails_arr);
                            if (array_search(strtolower($col_data[11]), $guest_emails_arr) != FALSE) {
                                $check_email[] = $row;
                            } else {
                                $guest['email'] = $col_data[11];
                            }
                            if (filter_var($col_data[11], FILTER_VALIDATE_EMAIL)) {
                                $guest['email'] = $col_data[11];
                            } else {
                                $check_email_valid[] = $row;
                            }

                            $imported_emails[] = $col_data[11];
                            if (is_numeric($col_data[12])) {
                                $guest['phone'] = $col_data[12];
                            } else {
                                $check_phone[] = $row;
                            }

                            $guest['assistant'] = $col_data[13];
                            if (is_numeric($col_data[14])) {
                                $guest['assistant_phone'] = $col_data[14];
                            } else {
                                $check_assistant_phone[] = $row;
                            }

                            if (filter_var($col_data[15], FILTER_VALIDATE_EMAIL)) {
                                $guest['assistant_email'] = $col_data[15];
                            } else {
                                $check_assistant_email[] = $row;
                            }
                            $guest['created'] = date('Y-m-d H:i:s');
                            $guest_data[] = $guest;
                        }
                    }

                    //-- check email in column are unique or not
                    if (count(array_unique($imported_emails)) != count($imported_emails)) {
                        fclose($handle);
                        $this->session->set_flashdata('error', "Duplicate value in email column.");
                    } else if (!empty($check_email)) { //-- check Account/Program in columns are valid or not
                        $rows = implode(',', $check_email);
                        $this->session->set_flashdata('error', "Guest Email already exist in the system. Please check entries at row number - " . $rows);
                    } else if (!empty($check_email_valid)) { //-- check Account/Program in columns are valid or not
                        $rows = implode(',', $check_email_valid);
                        $this->session->set_flashdata('error', "Invalid email in email column. Please check entries at row number - " . $rows);
                    } else if (!empty($check_account)) { //-- check Account/Program in columns are valid or not
                        $rows = implode(',', $check_account);
                        $this->session->set_flashdata('error', "Account/Program doesn't exist in the system. Please check entries at row number - " . $rows);
                    } else if (!empty($check_city)) { //-- check city in column are unique or not
                        $rows = implode(',', $check_city);
                        $this->session->set_flashdata('error', "City doesn't exist in the system. Please check entries at row number - " . $rows);
                    } else if (!empty($check_invite_date)) {  //-- check dates in column are valid or not
                        $rows = implode(',', $check_invite_date);
                        $this->session->set_flashdata('error', "Invalid invite date in invite_date column. Please check entries at row number - " . $rows);
                    } else if (!empty($check_guest_date)) {   //-- check post dates in column are valid or not
                        $rows = implode(',', $check_guest_date);
                        $this->session->set_flashdata('error', "Invalid guest date in guest_date column. Please check entries at row number - " . $rows);
                    } else if (!empty($check_AIR_date)) {   //-- check post dates in column are valid or not
                        $rows = implode(',', $check_AIR_date);
                        $this->session->set_flashdata('error', "Invalid Air date in AIR_date column. Please check entries at row number - " . $rows);
                    } else if (!empty($check_phone)) {   //-- check post dates in column are valid or not
                        $rows = implode(',', $check_phone);
                        $this->session->set_flashdata('error', "Invalid phone number in phone column. Please check entries at row number - " . $rows);
                    } else if (!empty($check_assistant_phone)) {   //-- check post dates in column are valid or not
                        $rows = implode(',', $check_assistant_phone);
                        $this->session->set_flashdata('error', "Invalid assistant phone number in assistant phone column. Please check entries at row number - " . $rows);
                    } else if (!empty($check_assistant_email)) {   //-- check post dates in column are valid or not
                        $rows = implode(',', $check_assistant_email);
                        $this->session->set_flashdata('error', "Invalid assistant email in assistant email column. Please check entries at row number - " . $rows);
                    } else {
                        if (!empty($guest_data)) {
                            //-- Insert Guest details into database
                            foreach ($guest_data as $val) {
                                $this->db->trans_begin();
                                $this->guests_model->common_insert_update('insert', TBL_GUESTS, $val);
                                $this->db->trans_complete();
                            }
                            $this->session->set_flashdata('success', "CSV file imported successfully! Guest data added successfully");
                        } else {
                            $this->session->set_flashdata('error', "CSV file is empty! Please upload valid file");
                        }
                    }
                } else {
                    fclose($handle);
                    $this->session->set_flashdata('error', 'The columns in this csv file does not match to the database');
                }
            } else {
                $this->session->set_flashdata('error', "CSV file is empty! Please upload valid file");
            }
            fclose($handle);
            redirect('guests');
        } else {
            $this->session->set_flashdata('error', strip_tags($this->upload->display_errors()));
            redirect('guests');
        }
    }

    public function storeState() {
        $content = file_get_contents(UPLOADS . "/" . "USStates.txt");
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            $row = explode(":", $line);
            $data = array(
                'name' => trim($row[0])
            );
            $this->db->insert('states_new', $data);
        }
    }

}

/* End of file Guests.php */
/* Location: ./application/controllers/Guests.php */