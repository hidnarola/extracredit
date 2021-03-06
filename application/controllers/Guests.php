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
        $this->load->model('communication_manager_model');
    }

    /**
     * Listing of All Guests
     */
    public function index() {
        checkPrivileges('guests', 'view');
        $data['perArr'] = checkPrivileges('guests');
        $data['comperArr'] = checkPrivileges('guests_communication');
        $data['title'] = 'Extracredit | Guests';
        $this->template->load('default', 'guests/list_guests', $data);
    }

    /**
     * Get guests data for ajax table
     * */
    public function get_guests() {
        checkPrivileges('guests', 'view');
        $final['recordsFiltered'] = $final['recordsTotal'] = $this->guests_model->get_guests('count');
        $final['redraw'] = 1;
        $final['data'] = $this->guests_model->get_guests('result');
        echo json_encode($final);
    }

    /**
     * Add /edit guests data
     * @param int $id
     * */
    public function add($id = NULL) {
        $data['contacts'] = [];

        if (!is_null($id))
            $id = base64_decode($id);
        if (is_numeric($id)) {
            $guest_id = $id;
            $guest = $this->guests_model->get_guest_details($id);
            if ($guest) {
                $data['guest'] = $guest;
                $data['title'] = 'Extracredit | Edit Guest';
                $data['heading'] = 'Edit Guest';
                $data['contacts'] = $this->guests_model->sql_select(TBL_ASSOCIATED_CONTACTS, 'id,name,email,phone', ['where' => ['is_delete' => 0, 'type' => 'guest', 'associated_id' => $id]]);

                if ($this->input->post('email') && $this->input->post('email_unavailable') != 1) {
                    $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|callback_check_email_edit[' . $id . ']');
                }
                $logo = $guest['logo'];
            } else {
                show_404();
            }
        } else {
            checkPrivileges('guests', 'add');

            if ($this->input->post('email') && $this->input->post('email_unavailable') != 1) {
                $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|callback_is_uniquemail');
            }

            $data['title'] = 'Extracredit | Add Guest';
            $data['heading'] = 'Add Guest';
            $logo = NULL;
        }
        $data['accounts'] = $this->guests_model->get_amc_accounts();

        $this->form_validation->set_rules('firstname', 'First Name', 'trim|required');

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

            //-- Get state id from post value
            $state_id = $city_id = NULL;
            $state_code = $this->input->post('state_short');
            $post_city = $this->input->post('city_id');
            if (!empty($state_code)) {
                $state = $this->guests_model->sql_select(TBL_STATES, 'id', ['where' => ['short_name' => $state_code]], ['single' => true]);
                if (empty($state) && $this->input->post('state_id') != '') {
                    $state = $this->vendors_model->sql_select(TBL_STATES, 'id', ['where' => ['name' => $this->input->post('state_id')]], ['single' => true]);
                }
                if (!empty($state)) {
                    $state_id = $state['id'];
                    if (!empty($post_city)) {
                        $city = $this->guests_model->sql_select(TBL_CITIES, 'id', ['where' => ['state_id' => $state_id, 'name' => $post_city]], ['single' => true]);
                        if (!empty($city)) {
                            $city_id = $city['id'];
                        } else {
                            $city_id = $this->guests_model->common_insert_update('insert', TBL_CITIES, ['name' => $post_city, 'state_id' => $state_id]);
                        }
                    }
                }
            }
            $invite_date = $guest_date = $AIR_date = NULL;
            if (!empty($this->input->post('invite_date'))) {
                $invite_date = date('Y-m-d', strtotime($this->input->post('invite_date')));
            }
            if (!empty($this->input->post('guest_date'))) {
                $guest_date = date('Y-m-d', strtotime($this->input->post('guest_date')));
            }
            if (!empty($this->input->post('AIR_date'))) {
                $AIR_date = date('Y-m-d', strtotime($this->input->post('AIR_date')));
            }
            if ($flag == 0) {
                $dataArr = array(
                    'account_id' => $this->input->post('account_id'),
                    'firstname' => $this->input->post('firstname'),
                    'lastname' => $this->input->post('lastname'),
                    'companyname' => $this->input->post('companyname'),
                    'company_website' => trim($this->input->post('company_website')),
                    'logo' => $logo,
                    'address' => $this->input->post('address'),
                    'city_id' => $city_id,
                    'state_id' => $state_id,
                    'zip' => $this->input->post('zip'),
                    'email' => ($this->input->post('email_unavailable') == 1) ? null : $this->input->post('email'),
                    'email_unavailable' => ($this->input->post('email_unavailable') == 1) ? 1 : 0,
                    'phone' => $this->input->post('phone'),
                    'invite_date' => $invite_date,
                    'guest_date' => $guest_date,
                    'AIR_date' => $AIR_date,
                    'AMC_created' => ($this->input->post('AMC_created') == 1) ? 'Yes' : 'No',
                    'AMC_active' => ($this->input->post('AMC_active') == 1) ? 'Yes' : 'No',
                    'assistant' => $this->input->post('assistant'),
                    'assistant_phone' => $this->input->post('assistant_phone'),
                    'assistant_email' => $this->input->post('assistant_email'),
                );

                require_once(APPPATH . "libraries/Mailin.php");
                $mailin = new Mailin('https://api.sendinblue.com/v2.0', 'VGcJrUg9ypYRjExh', 50000);    //Optional parameter: Timeout in MS
                //Api Key(v2.0) : VGcJrUg9ypYRjExh

                if ($this->input->post('is_subscribed') == 1 && $this->input->post('is_subscribed') != '') {
                    $dataArr['is_subscribed'] = 1; //insert in guest table
                    $data = array("email" => $this->input->post('email'),
                        "attributes" => array("FIRSTNAME" => $this->input->post('firstname'), "LASTNAME" => $this->input->post('lastname')),
                        "listid" => array(5)
                    );

                    $mailin->create_update_user($data);
                } else {
                    $dataArr['is_subscribed'] = 0; //update in guest table
                    $data = array("email" => $this->input->post('email'),
                        "listid_unlink" => array(5)
                    );
                    $mailin->create_update_user($data);
                }

//                p($dataArr, 1);
                if (is_numeric($id)) {
                    $dataArr['modified'] = date('Y-m-d H:i:s');
                    $this->guests_model->common_insert_update('update', TBL_GUESTS, $dataArr, ['id' => $id]);

                    if ($guest['email'] != $dataArr['email']) {

                        if (!empty($guest['email'])) {
                            $subscriber = get_mailchimp_subscriber($guest['email']);
                            if (!empty($subscriber)) {
                                $interests = $subscriber['interests'];
                                if ($interests[DONORS_GROUP_ID] == 1 || $interests[ACCOUNTS_GROUP_ID] == 1) {
                                    $mailchimp_data = array(
                                        'email_address' => $guest['email'],
                                        'interests' => array(GUESTS_GROUP_ID => false)
                                    );
                                    mailchimp($mailchimp_data);
                                } else {
                                    //-- Delete old subscriber from mailchimp subscriber list
                                    $mailchimp_data = array(
                                        'email_address' => $guest['email'],
                                    );
                                    delete_mailchimp_subscriber($mailchimp_data);
                                }
                            }
                        }

                        if (!empty($dataArr['email'])) {
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
                    }

                    $this->session->set_flashdata('success', 'Guest details has been updated successfully.');
                } else {
                    $dataArr['created'] = date('Y-m-d H:i:s');
                    $guest_id = $this->guests_model->common_insert_update('insert', TBL_GUESTS, $dataArr);
                    //-- Insert account email into mailchimp subscriber list
                    if (!empty($dataArr['email'])) {
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
                    }
                    $this->session->set_flashdata('success', 'Guest has been added successfully');
                }

                //-- Delete old contact data
                $this->guests_model->common_delete(TBL_ASSOCIATED_CONTACTS, ['associated_id' => $guest_id, 'type' => 'guest']);

                $contact_names = $this->input->post('contact_name');
                $contact_arr = [];
                foreach ($contact_names as $key => $name) {
                    $email = null;
                    if ($this->input->post('contact_email')[$key] != '') {
                        $email = $this->input->post('contact_email')[$key];
                    }
                    $arr = ['name' => $name, 'email' => $email, 'phone' => $this->input->post('contact_phone')[$key], 'type' => 'guest', 'associated_id' => $guest_id, 'created' => date('Y-m-d H:i:s')];
                    $contact_arr[] = $arr;
                }

                if (!empty($contact_arr)) {
                    $this->guests_model->batch_insert_update('insert', TBL_ASSOCIATED_CONTACTS, $contact_arr);
                }

                if (isset($_POST['save_add_another'])) {
                    redirect('guests/add');
                } else {
                    redirect('guests');
                }
            }
        }
        $this->template->load('default', 'guests/form', $data);
    }

    /**
     * Edit Guest data
     * @param int $id
     * */
    public function edit($id) {
        checkPrivileges('guests', 'edit');
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
        checkPrivileges('guests', 'delete');
        $id = base64_decode($id);
        if (is_numeric($id)) {
            $guest = $this->guests_model->get_guest_details($id);
            if ($guest) {
                //Remove Subscribed user from SendInBlue.com
                require_once(APPPATH . "libraries/Mailin.php");
                $mailin = new Mailin('https://api.sendinblue.com/v2.0', 'VGcJrUg9ypYRjExh', 50000);    //Optional parameter: Timeout in MS
                $data = array("email" => $guest['email'],
                    "listid_unlink" => array(5)
                );
                $mailin->create_update_user($data);

                $update_array = array(
                    'is_delete' => 1,
                    'is_subscribed' => 0
                );
                $this->guests_model->common_insert_update('update', TBL_GUESTS, $update_array, ['id' => $id]);
                //--delete subscriber from list
                if (!empty($guest['email'])) {
                    $subscriber = get_mailchimp_subscriber($guest['email']);
                    if (!empty($subscriber)) {
                        $interests = $subscriber['interests'];
                        if ($interests[DONORS_GROUP_ID] == 1 || $interests[ACCOUNTS_GROUP_ID] == 1) {
                            $mailchimp_data = array(
                                'email_address' => $guest['email'],
                                'interests' => array(GUESTS_GROUP_ID => false)
                            );
                            mailchimp($mailchimp_data);
                        } else {
                            //-- Delete old entry from mailchimp subscriber's guest list
                            $mailchimp_data = array(
                                'email_address' => $guest['email'],
                            );
                            delete_mailchimp_subscriber($mailchimp_data);
                        }
                    }
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
     * Archive the current guest season
     * @param type $guest_id
     */
    public function archive_current_season() {
        $id = base64_decode($this->input->post('id'));
//        $id = base64_decode($guest_id);
        if (is_numeric($id)) {
            $guest = $this->guests_model->get_guest_details($id);
            if ($guest) {
                $note = '<p>Invite Date: ' . date('m-d-Y', strtotime($guest['invite_date'])) . '</p>'
                        . '<p>Guest Date: ' . date('m-d-Y', strtotime($guest['guest_date'])) . '</p>'
                        . '<p>Air Date: ' . date('m-d-Y', strtotime($guest['AIR_date'])) . '</p>'
                        . '<p>AMC Created? ' . $guest['AMC_created'] . '</p>'
                        . '<p>AMC Active? ' . $guest['AMC_active'] . '</p>';
                //--- communication_array to store in communication table
                $communication_array = array(
                    'communication_date' => date('Y-m-d'),
                    'subject' => 'Archived Show Appearance',
                    'follow_up_date' => NULL,
                    'guest_id' => $id,
                    'donor_id' => 0,
                    'account_id' => 0,
                    'type' => 2,
                    'media' => NULL,
                    'note' => $note
                );
                $this->guests_model->common_insert_update('insert', TBL_COMMUNICATIONS, $communication_array);
                //--- update invite date, Air Date, Guest Date, AMC Created and AMC Active as null in guest table
                $update_array = array(
                    'invite_date' => NULL,
                    'guest_date' => NULL,
                    'AIR_date' => NULL,
                    'AMC_created' => NULL,
                    'AMC_active' => 'No'
                );
                $this->guests_model->common_insert_update('update', TBL_GUESTS, $update_array, ['id' => $id]);
                $this->session->set_flashdata('success', 'Guest is archived successfully!');
                $final = 0;
            } else {
                $this->session->set_flashdata('error', 'Invalid request. Please try again!');
                $final = 1;
            }
            echo json_encode($final);
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
        if ($return_value == 1) {
            $this->form_validation->set_message('check_email_edit', 'Sorry, This email is already Exists..!');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Listing of All Guests communication
     */
    public function communication($id = null) {
        checkPrivileges('guests_communication', 'view');
        $guest = $this->guests_model->sql_select(TBL_GUESTS, 'firstname,lastname', ['where' => ['id' => base64_decode($id), 'is_delete' => 0]], ['single' => true]);
        if (!empty($guest)) {
            $data['perArr'] = checkPrivileges('guests_communication');
            $data['title'] = 'Extracredit | Guests Communication';
            $data['guest'] = $guest;
            $data['id'] = $id;
            $this->template->load('default', 'guests/list_communication', $data);
        } else {
            show_404();
        }
    }

    /**
     * Get guests communication data for ajax table
     * */
    public function get_guests_communication($id) {
        checkPrivileges('guests_communication', 'view');
        $id = base64_decode($id);
        $final['recordsFiltered'] = $final['recordsTotal'] = $this->guests_model->get_guests_communication('count', $id);
        $final['redraw'] = 1;
        $guests = $this->guests_model->get_guests_communication('result', $id);
        $start = $this->input->get('start') + 1;

        foreach ($guests as $key => $val) {
            $guests[$key] = $val;
            $guests[$key]['created'] = date('m/d/Y', strtotime($val['created']));
            $guests[$key]['follow_up_date'] = ($val['follow_up_date'] != '') ? date('m/d/Y', strtotime($val['follow_up_date'])) : '';
            $guests[$key]['communication_date'] = ($val['communication_date'] != '') ? date('m/d/Y', strtotime($val['communication_date'])) : '';
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
        $guest_communication['follow_up_date'] = ($guest_communication['follow_up_date'] != '') ? date('m/d/Y', strtotime($guest_communication['follow_up_date'])) : '';
        $guest_communication['communication_date'] = ($guest_communication['communication_date'] != '') ? date('m/d/Y', strtotime($guest_communication['communication_date'])) : '';
        echo json_encode($guest_communication);
    }

    public function add_communication($guest_id = null, $comm_id = null) {
        if (!is_null($guest_id))
            $guest_id = base64_decode($guest_id);

        $data['guest'] = $this->guests_model->sql_select(TBL_GUESTS, 'id,firstname,lastname', ['where' => ['id' => $guest_id]], ['single' => true]);
        $data['contacts'] = $this->guests_model->sql_select(TBL_ASSOCIATED_CONTACTS, 'id,name', ['where' => ['associated_id' => $guest_id, 'type' => 'guest', 'is_delete' => 0]]);

        $comm_id = base64_decode($comm_id);
        if (is_numeric($comm_id)) {
            checkPrivileges('guests_communication', 'edit');
            $guest_communication = $this->guests_model->get_guest_communication_details($comm_id);
            $data['guest_communication'] = $guest_communication;
            $data['title'] = 'Extracredit | Edit Communication';
            $data['heading'] = 'Edit Communication';
            if ($guest_communication['media'] != '')
                $media = $guest_communication['media'];
            else
                $media = NULL;
        } else {
            checkPrivileges('guests_communication', 'add');
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
                    'type' => 2,
                    'communication_date' => ($this->input->post('communication_date') != '') ? date('Y-m-d', strtotime($this->input->post('communication_date'))) : NULL,
                    'subject' => $this->input->post('subject'),
                    'follow_up_date' => ($this->input->post('follow_up_date') != '') ? date('Y-m-d', strtotime($this->input->post('follow_up_date'))) : NULL,
                    'donor_id' => 0,
                    'guest_id' => $guest_id,
                    'account_id' => 0,
                    'associated_contact_id' => $this->input->post('associated_contact_id'),
                    'note' => $this->input->post('note'),
                    'media' => $media
                );

                if (is_numeric($comm_id)) {
                    $dataArr['modified'] = date('Y-m-d H:i:s');
                    $this->guests_model->common_insert_update('update', TBL_COMMUNICATIONS, $dataArr, ['id' => $comm_id]);
                    $this->session->set_flashdata('success', 'Guest communication details has been updated successfully.');
                } else {
                    $dataArr['created'] = date('Y-m-d H:i:s');
                    $this->guests_model->common_insert_update('insert', TBL_COMMUNICATIONS, $dataArr);
                    if (!empty($this->input->post('follow_up_date'))) {
                        $communication_ManagerArr = array(
                            'user_id' => $this->session->userdata('extracredit_user')['id'],
                            'communication_id' => $this->db->insert_id(),
                            'follow_up_date' => date('Y-m-d', strtotime($this->input->post('follow_up_date'))),
                            'category' => 'guest',
                        );
                        $this->communication_manager_model->common_insert_update('insert', TBL_COMMUNICATIONS_MANAGER, $communication_ManagerArr);
                    }
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
        checkPrivileges('guests', 'view');
        $guest_id = base64_decode($this->input->post('id'));
        $guest = $this->guests_model->get_guest_details_view($guest_id);
        if ($guest) {
            $guest['invite_date'] = ($guest['invite_date'] != '') ? date('m/d/Y', strtotime($guest['invite_date'])) : '';
            $guest['guest_date'] = ($guest['guest_date'] != '') ? date('m/d/Y', strtotime($guest['guest_date'])) : '';
            $guest['AIR_date'] = ($guest['AIR_date'] != '') ? date('m/d/Y', strtotime($guest['AIR_date'])) : '';
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
        checkPrivileges('guests', 'add');
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

            $account_name_arr = array_map('strtolower', $account_name_arr);
            $guest_emails_arr = array_map('strtolower', $guest_emails_arr);
            $cities_arr = array_map('strtolower', $cities_arr);

            $row = 1;
            $handle = fopen($fileDirectory . "/" . $fileDetails['file_name'], "r");
            $guest_data = $check_account = $check_email = $check_email_valid = $check_city = $check_invite_date = $check_guest_date = $check_AIR_date = $check_amc_created = $check_assiatnt_email = $check_phone = $check_assistant_phone = $check_assistant_email = $imported_emails = [];
            if (($data2 = fgetcsv($handle)) !== FALSE) {
                $data_format2 = array('firstname', 'lastname', 'companyname', 'invite_date', 'guest_date', 'AIR_date', 'AMC_created', 'amc', 'address', 'city', 'zip', 'email', 'phone', 'assistant', 'assistant_phone', 'assistant_email');
//                p($data2);
//                p($data_format2,1);
                //-- check if first colums is according to predefined row
                if ($data_format2 == $data2) {
                    while (($col_data = fgetcsv($handle)) !== FALSE) {
                        $guest = [];
                        if ($col_data[0] == '') {
                            fclose($handle);
                            $this->session->set_flashdata('error', 'First Name is missing in Row No. ' . $row);
                            redirect('guests');
                        } else {
                            $guest['firstname'] = $col_data[0];
                            $guest['lastname'] = $col_data[1];
                            $guest['companyname'] = (!empty($col_data[2])) ? $col_data[2] : NULL;

                            //-- invite Date, guest date and AIR date validation 
                            //-- Check date is valid or not
                            if (!empty($col_data[3])) {
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
                            } else {
                                $guest['invite_date'] = NULL;
                            }

                            //-- Check Guest date is valid or not
                            if (!empty($col_data[4])) {
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
                            } else {
                                $guest['guest_date'] = NULL;
                            }
                            //-- Check post date is valid or not
                            if (!empty($col_data[5])) {
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
                            } else {
                                $guest['AIR_date'] = NULL;
                            }

                            if (!empty($col_data[6])) {
                                if ($col_data[6] == 'Yes' || $col_data[6] == 'No')
                                    $guest['AMC_created'] = $col_data[6];
                                else
                                    $check_amc_created[] = $row;
                            } else {
                                $guest['AMC_created'] = NULL;
                            }

                            //-- Check if program/amc name is valid or not if not then add it into array
                            if (!empty($col_data[7])) {
                                if (array_search(strtolower($col_data[7]), $account_name_arr) != FALSE) {
                                    $guest['account_id'] = array_search(strtolower($col_data[7]), $account_name_arr);
                                } else {
                                    $check_account[] = $row;
                                }
                            } else {
                                $guest['account_id'] = NULL;
                            }

                            $guest['address'] = (!empty($col_data[8])) ? $col_data[8] : NULL;
                            //--check city is valid or not
                            if (!empty($col_data[9])) {
                                if (array_search(strtolower($col_data[9]), $cities_arr) != FALSE) {
                                    $guest['city_id'] = array_search(strtolower($col_data[9]), $cities_arr);
                                    $guest['state_id'] = $states_arr[$guest['city_id']];
                                } else {
                                    $check_city[] = $row;
                                }
                            } else {
                                $guest['city_id'] = NULL;
                                $guest['state_id'] = NULL;
                            }

                            $guest['zip'] = (!empty($col_data[10])) ? $col_data[10] : NULL;
                            //--check email is unique or not
                            if (!empty($col_data[11])) {
                                if (filter_var($col_data[11], FILTER_VALIDATE_EMAIL)) {
                                    if (array_search(strtolower($col_data[11]), $guest_emails_arr) != FALSE) {
                                        $check_email[] = $row;
                                    } else {
                                        $guest['email'] = $col_data[11];
                                    }
                                } else {
                                    $check_email_valid[] = $row;
                                }
                            } else {
                                $guest['email'] = NULL;
                            }


                            $imported_emails[] = (!empty($col_data[11])) ? $col_data[11] : NULL;

                            if (!empty($col_data[12])) {
                                if (is_numeric($col_data[12])) {
                                    $guest['phone'] = $col_data[12];
                                } else {
                                    $check_phone[] = $row;
                                }
                            } else {
                                $guest['phone'] = NULL;
                            }

                            $guest['assistant'] = (!empty($col_data[13])) ? $col_data[13] : NULL;

                            if (!empty($col_data[14])) {
                                if (is_numeric($col_data[14])) {
                                    $guest['assistant_phone'] = $col_data[14];
                                } else {
                                    $check_assistant_phone[] = $row;
                                }
                            } else {
                                $guest['assistant_phone'] = NULL;
                            }

                            if (!empty($col_data[15])) {
                                if (filter_var($col_data[15], FILTER_VALIDATE_EMAIL)) {
                                    $guest['assistant_email'] = $col_data[15];
                                    $guest['email_unavailable'] = 0;
                                } else {
                                    $check_assistant_email[] = $row;
                                }
                            } else {
                                $guest['email_unavailable'] = 1;
                                $guest['assistant_email'] = NULL;
                            }
                            $guest['created'] = date('Y-m-d H:i:s');
                            $guest_data[] = $guest;
                            $row++;
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
                    } elseif (!empty($check_amc_created)) {
                        $rows = implode(',', $check_amc_created);
                        $this->session->set_flashdata('error', "Invalid AMC_created value in AMC_created column. Only 'Yes/No' values are allowed. Please check entries at row number - " . $rows);
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
                                //-- if email is not empty then save it into mailchimp subscriber list
                                if (!empty($val['email'])) {
                                    $mailchimp_data = array(
                                        'email_address' => $val['email'],
                                        'status' => 'subscribed',
                                        'merge_fields' => [
                                            'FNAME' => $val['firstname'],
                                            'LNAME' => $val['lastname']
                                        ],
                                        'interests' => array(GUESTS_GROUP_ID => true)
                                    );
                                    mailchimp($mailchimp_data);
                                }
                                $this->guests_model->common_insert_update('insert', TBL_GUESTS, $val);
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