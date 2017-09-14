<?php

/**
 * Donors Controller - Manage Donors
 * @author KU
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Donors extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('donors_model');
    }

    /**
     * Listing of All Donors
     */
    public function index() {
        checkPrivileges('donors', 'view');
        $data['perArr'] = checkPrivileges('donors');
        $data['comperArr'] = checkPrivileges('donors_communication');
        $data['title'] = 'Extracredit | Donors';
        $this->template->load('default', 'donors/list_donors', $data);
    }

    /**
     * Get donors data for ajax table
     * */
    public function get_donors() {
        checkPrivileges('donors', 'view');
        $data['perArr'] = checkPrivileges('donors');
        $final['recordsFiltered'] = $final['recordsTotal'] = $this->donors_model->get_donors('count');
        $final['redraw'] = 1;
        $donors = $this->donors_model->get_donors('result');
        $start = $this->input->get('start') + 1;

        foreach ($donors as $key => $val) {
            $donors[$key] = $val;
            $donors[$key]['created'] = date('d,M Y', strtotime($val['created']));
        }

        $final['data'] = $donors;
        echo json_encode($final);
    }

    /**
     * Add /edit donors data
     * @param int $id
     * */
    public function add($id = NULL) {
        checkPrivileges('donors', 'add');
        $data['perArr'] = checkPrivileges('donors');
        if (!is_null($id))
            $id = base64_decode($id);
        if (is_numeric($id)) {
            $donor = $this->donors_model->get_donor_details($id);
            if ($donor) {
                $data['donor'] = $donor;
                $data['title'] = 'Extracredit | Edit Donor';
                $data['heading'] = 'Edit Donor';
                $data['cities'] = $this->donors_model->sql_select(TBL_CITIES, NULL, ['where' => ['state_id' => $donor['state_id']]]);
                $data['accounts'] = $this->donors_model->sql_select(TBL_ACCOUNTS, 'id,action_matters_campaign,vendor_name', ['where' => ['fund_type_id' => $donor['fund_type_id']]]);
            } else {
                show_404();
            }
        } else {
            $data['title'] = 'Extracredit | Add Donor';
            $data['heading'] = 'Add Donor';
            $data['cities'] = [];
            $data['accounts'] = [];
            $this->form_validation->set_rules('fund_type_id', 'Fund Type', 'trim|required');
            $this->form_validation->set_rules('account_id', 'Program/AMC', 'trim|required');
        }
        $settings = $this->users_model->sql_select(TBL_SETTINGS);
        $settings_arr = [];
        foreach ($settings as $val) {
            $settings_arr[$val['setting_key']] = $val['setting_value'];
        }
        $data['settings'] = $settings_arr;
        $data['fund_types'] = $this->donors_model->sql_select(TBL_FUND_TYPES, 'id,name', ['where' => ['is_delete' => 0]]);
        $data['payment_types'] = $this->donors_model->sql_select(TBL_PAYMENT_TYPES, 'id,type', ['where' => ['is_delete' => 0]]);
        $data['states'] = $this->donors_model->sql_select(TBL_STATES, NULL);

        $this->form_validation->set_rules('date', 'Date', 'trim|required');
        $this->form_validation->set_rules('post_date', 'Post Date', 'trim|required');
        $this->form_validation->set_rules('firstname', 'First Name', 'trim|required');
        $this->form_validation->set_rules('lastname', 'Last Name', 'trim|required');
        $this->form_validation->set_rules('address', 'Address', 'trim|required');

        $this->form_validation->set_rules('state_id', 'State', 'trim|required');
        $this->form_validation->set_rules('city_id', 'City', 'trim|required');

        $this->form_validation->set_rules('zip', 'Zip', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules('payment_type_id', 'PAyment Type', 'trim|required');
        $this->form_validation->set_rules('payment_number', 'Payment Number', 'trim|required');

        if ($this->form_validation->run() == TRUE) {
            $dataArr = array(
                'account_id' => $this->input->post('account_id'),
                'firstname' => $this->input->post('firstname'),
                'lastname' => $this->input->post('lastname'),
                'address' => $this->input->post('address'),
                'email' => $this->input->post('email'),
                'state_id' => $this->input->post('state_id'),
                'city_id' => $this->input->post('city_id'),
                'zip' => $this->input->post('zip'),
                'date' => date('Y-m-d', strtotime($this->input->post('date'))),
                'post_date' => date('Y-m-d', strtotime($this->input->post('post_date'))),
                'amount' => $this->input->post('amount'),
                'payment_type_id' => $this->input->post('payment_type_id'),
                'payment_number' => $this->input->post('payment_number'),
                'memo' => $this->input->post('memo')
            );
            $amount = $this->input->post('amount');
            $admin_amount = ($settings_arr['admin-donation-percent'] * $amount) / 100;
            $admin_amount = round($admin_amount, 2);
            $account_amount = $amount - $admin_amount;
            $fund_array = array('admin_fund' => $admin_amount, 'account_fund' => $account_amount);

            $this->db->trans_begin();
            if (is_numeric($id)) {
                $account_id = $donor['account_id'];
                $dataArr['account_id'] = $account_id;
                $dataArr['modified'] = date('Y-m-d H:i:s');
                $this->donors_model->common_insert_update('update', TBL_DONORS, $dataArr, ['id' => $id]);

                $fund_array['account_id'] = $account_id;
                $fund_array['donor_id'] = $id;
                $fund_array['modified'] = date('Y-m-d H:i:s');
                $this->donors_model->common_insert_update('update', TBL_FUNDS, $fund_array, ['account_id' => $account_id, 'donor_id' => $id]);
                $this->session->set_flashdata('success', 'Donor details has been updated successfully.');

                //---get account's total fund 
                $account = $this->donors_model->sql_select(TBL_ACCOUNTS, 'total_fund,admin_fund', ['where' => ['id' => $account_id]], ['single' => true]);
                $total_fund = $account['total_fund'] - $donor['account_fund'];
                $admin_fund = $account['admin_fund'] - $donor['admin_fund'];
                $total_admin_fund = $this->admin_fund - $donor['admin_fund'];
            } else {
                $account_id = $this->input->post('account_id');
                $dataArr['created'] = date('Y-m-d H:i:s');
                $id = $this->donors_model->common_insert_update('insert', TBL_DONORS, $dataArr);

                $fund_array['account_id'] = $account_id;
                $fund_array['donor_id'] = $id;
                $fund_array['created'] = date('Y-m-d H:i:s');
                $this->donors_model->common_insert_update('insert', TBL_FUNDS, $fund_array);
                $this->session->set_flashdata('success', 'Donor has been added successfully');

                //---get account's total fund 
                $account = $this->donors_model->sql_select(TBL_ACCOUNTS, 'total_fund,admin_fund', ['where' => ['id' => $account_id]], ['single' => true]);
                $total_fund = $account['total_fund'];
                $admin_fund = $account['admin_fund'];
                $total_admin_fund = $this->admin_fund;
            }

            $this->donors_model->common_insert_update('update', TBL_ACCOUNTS, ['total_fund' => $total_fund + $account_amount, 'admin_fund' => $admin_fund + $admin_amount], ['id' => $account_id]);

            $this->donors_model->update_admin_fund($total_admin_fund + $admin_amount);
            $this->db->trans_complete();

            redirect('donors');
        }
        $this->template->load('default', 'donors/form', $data);
    }

    /**
     * Edit Donor data
     * @param int $id
     * */
    public function edit($id) {
        checkPrivileges('donors', 'edit');
        $data['perArr'] = checkPrivileges('donors');
        $this->add($id);
    }

    /**
     * Ajax call to this function return accounts of particular fund type id
     */
    public function get_accounts() {
        $id = base64_decode($this->input->post('id'));
        $accounts = $this->donors_model->sql_select(TBL_ACCOUNTS, 'id,action_matters_campaign,vendor_name', ['where' => ['is_delete' => 0, 'fund_type_id' => $id]]);
        echo json_encode($accounts);
    }

    /**
     * Ajax call to this function return fund type details
     */
    public function get_cities() {
        $id = base64_decode($this->input->post('id'));
        $cities = $this->donors_model->sql_select(TBL_CITIES, 'name,id', ['where' => ['state_id' => $id]]);
        echo json_encode($cities);
    }

    /**
     * This function used to check Unique email at the time of account's add
     * */
    public function checkUniqueEmail($id = NULL) {
        $where = ['email' => trim($this->input->get('email'))];
        if (!is_null($id)) {
            $id = base64_decode($id);
            $where['id!='] = $id;
        }
        $donor = $this->donors_model->sql_select(TBL_DONORS, 'id', ['where' => $where], ['single' => true]);
        if (!empty($donor)) {
            echo "false";
        } else {
            echo "true";
        }
        exit;
    }

    /**
     * Delete donor
     * @param int $id
     * */
    public function delete($id = NULL) {
        checkPrivileges('donors', 'delete');
        $data['perArr'] = checkPrivileges('donors');
        $id = base64_decode($id);
        if (is_numeric($id)) {
            $donor = $this->donors_model->get_donor_details($id);
            if ($donor) {
                $update_array = array(
                    'is_delete' => 1
                );
                $account = $this->donors_model->sql_select(TBL_ACCOUNTS, 'total_fund,admin_fund', ['where' => ['id' => $donor['account_id']]], ['single' => true]);

                $this->db->trans_begin();
                $this->donors_model->common_insert_update('update', TBL_DONORS, $update_array, ['id' => $id]);
                $this->donors_model->common_insert_update('update', TBL_FUNDS, $update_array, ['account_id' => $donor['account_id'], 'donor_id' => $id]);
                $total_fund = $account['total_fund'] - $donor['account_fund'];
                $admin_fund = $account['admin_fund'] - $donor['admin_fund'];
                $this->donors_model->common_insert_update('update', TBL_ACCOUNTS, ['total_fund' => $total_fund, 'admin_fund' => $admin_fund], ['id' => $donor['account_id']]);
                $total_admin_fund = $this->admin_fund - $donor['admin_fund'];
                $this->donors_model->update_admin_fund($total_admin_fund);

                $this->db->trans_complete();

                $this->session->set_flashdata('success', 'Donor has been deleted successfully!');
            } else {
                $this->session->set_flashdata('error', 'Invalid request. Please try again!');
            }
            redirect('donors');
        } else {
            show_404();
        }
    }

    /**
     * Listing of All Donors Communication
     */
    public function communication($id = null) {
        checkPrivileges('donors_communication', 'view');
        $data['perArr'] = checkPrivileges('donors_communication');
        $data['title'] = 'Extracredit | Donors Communication';
        $data['id'] = $id;
        $this->template->load('default', 'donors/list_communication', $data);
    }

    /**
     * Get Donors communication data for ajax table
     * */
    public function get_donors_communication($id) {
        checkPrivileges('donors_communication', 'view');
        $data['perArr'] = checkPrivileges('donors_communication');
        $id = base64_decode($id);
        $final['recordsFiltered'] = $final['recordsTotal'] = $this->donors_model->get_donors_communication('count', $id);
        $final['redraw'] = 1;
        $donors = $this->donors_model->get_donors_communication('result', $id);
        $start = $this->input->get('start') + 1;

        foreach ($donors as $key => $val) {
            $donors[$key] = $val;
            $donors[$key]['created'] = date('d M, Y', strtotime($val['created']));
        }


        $final['data'] = $donors;
        echo json_encode($final);
    }

    /**
     * Get donors communication data for ajax call for view
     * */
    public function get_communication_by_id() {
        $id = $this->input->post('id');
        $id = base64_decode($id);
        $donor_communication = $this->donors_model->get_donor_communication_details($id);
        $donor_communication['follow_up_date'] = date('d M, Y', strtotime($donor_communication['follow_up_date']));
        $donor_communication['communication_date'] = date('d M, Y', strtotime($donor_communication['communication_date']));
        echo json_encode($donor_communication);
    }

    /**
     * Add Donors communication 
     * @param type $donor_id
     * @param type $comm_id
     */
    public function add_communication($donor_id = null, $comm_id = null) {
        checkPrivileges('donors_communication', 'add');
        $data['perArr'] = checkPrivileges('donors_communication');
        if (!is_null($donor_id))
            $donor_id = base64_decode($donor_id);
        $comm_id = base64_decode($comm_id);
        if (is_numeric($comm_id)) {
            $donor_communication = $this->donors_model->get_donor_communication_details($comm_id);
            $data['donor_communication'] = $donor_communication;
            $data['title'] = 'Extracredit | Edit Communication';
            $data['heading'] = 'Edit Communication';
            if ($donor_communication['media'] != '')
                $media = $donor_communication['media'];
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
                    'donor_id' => $donor_id,
                    'guest_id' => 0,
                    'type' => 1,
                    'media' => $media
                );

                if (is_numeric($comm_id)) {
                    $dataArr['modified'] = date('Y-m-d H:i:s');
                    $this->donors_model->common_insert_update('update', TBL_COMMUNICATIONS, $dataArr, ['id' => $comm_id]);
                    $this->session->set_flashdata('success', 'Donor communication details has been updated successfully.');
                } else {
                    $dataArr['created'] = date('Y-m-d H:i:s');
                    $this->donors_model->common_insert_update('insert', TBL_COMMUNICATIONS, $dataArr);
                    $this->session->set_flashdata('success', 'Donor communication has been added successfully');
                }
                redirect('donors/communication/' . base64_encode($donor_id));
            }
        }
        $this->template->load('default', 'donors/add_communication', $data);
    }

    /**
     * Delete Donor Communication
     * @param int $id
     * */
    public function delete_communication($donor_id = null, $id = NULL) {
        checkPrivileges('donors_communication', 'delete');
        $data['perArr'] = checkPrivileges('donors_communication');
        $id = base64_decode($id);
        if (is_numeric($id)) {
            $donor_communication = $this->donors_model->get_donor_communication_details($id);
            if ($donor_communication) {
                $update_array = array(
                    'is_delete' => 1
                );
                $this->donors_model->common_insert_update('update', TBL_COMMUNICATIONS, $update_array, ['id' => $id, 'type' => 2]);
                $this->session->set_flashdata('success', 'Donor communication has been deleted successfully!');
            } else {
                $this->session->set_flashdata('error', 'Invalid request. Please try again!');
            }
            redirect('donors/communication/' . $donor_id);
        } else {
            show_404();
        }
    }

    /**
     * Import donor data from CSV file
     * @author KU
     */
    public function import_donor() {
        $fileDirectory = DONORS_CSV;
        $config['overwrite'] = FALSE;
        $config['remove_spaces'] = TRUE;
        $config['upload_path'] = DONORS_CSV;
        $config['allowed_types'] = 'csv|CSV';
        $this->load->library('upload', $config);

        //-- Upload csv file
        if ($this->upload->do_upload('import_donor')) {
            $fileDetails = $this->upload->data();

            $accounts = $this->donors_model->get_all_accounts();
            $account_name_arr = $cities_arr = $states_arr = $payments_type_arr = [];
            foreach ($accounts as $account) {
                if (!empty($account['action_matters_campaign'])) {
                    $account_name_arr[$account['id']] = $account['action_matters_campaign'];
                } else {
                    $account_name_arr[$account['id']] = $account['vendor_name'];
                }
            }

            //-- Get cities array
            $cities = $this->donors_model->sql_select(TBL_CITIES);
            foreach ($cities as $city) {
                $cities_arr[$city['id']] = $city['name'];
                $states_arr[$city['id']] = $city['state_id'];
            }

            //-- Get donor emails
            $donor_emails = $this->donors_model->sql_select(TBL_DONORS, 'email', ['where' => ['is_delete' => 0]]);
            $donor_emails_arr = array_column($donor_emails, 'email');

            $payment_types = $this->donors_model->sql_select(TBL_PAYMENT_TYPES, 'id,type', ['where' => ['is_delete' => 0]]);
            foreach ($payment_types as $payment_type) {
                $payments_type_arr[$payment_type['id']] = $payment_type['type'];
            }

            $row = 1;
            $handle = fopen($fileDirectory . "/" . $fileDetails['file_name'], "r");
            $donor_data = $check_account = $check_email = $check_city = $check_date = $check_postdate = $check_amount = $check_payment = $imported_emails = [];
            if (($data2 = fgetcsv($handle)) !== FALSE) {
                $data_format2 = array('amc', 'firstname', 'lastname', 'email', 'address', 'city', 'zip', 'date', 'post_date', 'amount', 'payment_type', 'payment_number', 'memo');

                //-- check if first colums is according to predefined row
                if ($data_format2 == $data2) {
                    while (($col_data = fgetcsv($handle)) !== FALSE) {
                        $donor = [];
                        p($col_data);
                        if ($col_data[0] == '' || $col_data[1] == '' || $col_data[2] == '' || $col_data[3] == '' || $col_data[4] == '' || $col_data[5] == '' || $col_data[6] == '' || $col_data[7] == '' || $col_data[8] == '' || $col_data[9] == '' || $col_data[10] == '' || $col_data[11] == '') {
                            fclose($handle);
                            $this->session->set_flashdata('error', 'Some fields are missing in Row No. ' . $row);
                            redirect('donors');
                        } else {
                            $row++;
                            //-- Check if program/amc name is valid or not if not then add it into array
                            $account_name_arr = array_map('strtolower', $account_name_arr);
                            if (array_search(strtolower($col_data[0]), $account_name_arr) != FALSE) {
                                $donor['account_id'] = array_search(strtolower($col_data[0]), $account_name_arr);
                            } else {
                                $check_account[] = $row;
                            }

                            //--check email is unique or not
                            $donor_emails_arr = array_map('strtolower', $donor_emails_arr);
                            if (array_search(strtolower($col_data[3]), $donor_emails_arr) != FALSE) {
                                $check_email[] = $row;
                            } else {
                                $donor['email'] = $col_data[3];
                            }

                            $imported_emails[] = $col_data[3];

                            //--check city is valid or not
                            if (array_search(strtolower($col_data[5]), array_map('strtolower', $cities_arr)) != FALSE) {
                                $donor['city_id'] = array_search(strtolower($col_data[5]), array_map('strtolower', $cities_arr));
                                $donor['state_id'] = $states_arr[$donor['city_id']];
                            } else {
                                $check_city[] = $row;
                            }

                            $donor['firstname'] = $col_data[1];
                            $donor['lastname'] = $col_data[2];
                            $donor['address'] = $col_data[4];
                            $donor['zip'] = $col_data[6];

                            //-- Date and post date validation 
                            //-- Check date is valid or not
                            $date_arr = explode('-', $col_data[7]);
                            if (count($date_arr) == 3) {
                                list($y, $m, $d) = explode('-', $col_data[7]);
                                if (!checkdate($m, $d, $y)) {
                                    $check_date[] = $row;
                                } else {
                                    $donor['date'] = $col_data[7];
                                }
                            } else {
                                $check_date[] = $row;
                            }

                            //-- Check post date is valid or not
                            $date_arr = explode('-', $col_data[8]);
                            if (count($date_arr) == 3) {
                                list($y, $m, $d) = explode('-', $col_data[8]);
                                if (!checkdate($m, $d, $y)) {
                                    $check_postdate[] = $row;
                                } else {
                                    $donor['post_date'] = $col_data[8];
                                }
                            } else {
                                $check_postdate[] = $row;
                            }
                            //-- Check amount is valid or not
                            if (is_numeric($col_data[9]) && $col_data[9] != 0) {
                                $donor['amount'] = $col_data[9];
                            } else {
                                $check_amount[] = $row;
                            }

                            //-- Check payment type is valid or not
                            if (array_search(strtolower($col_data[10]), array_map('strtolower', $payments_type_arr)) != FALSE) {
                                $donor['payment_type_id'] = array_search(strtolower($col_data[10]), array_map('strtolower', $payments_type_arr));
                            } else {
                                $check_payment[] = $row;
                            }
                            $donor['payment_number'] = $col_data[11];
                            $donor['memo'] = $col_data[12];
                            $donor['created'] = date('Y-m-d H:i:s');


                            $donor_data[] = $donor;
                        }
                    }

                    //-- check email in column are unique or not
                    if (count(array_unique($imported_emails)) != count($imported_emails)) {
                        fclose($handle);
                        $this->session->set_flashdata('error', "Duplicate value in email column.");
                    } else if (!empty($check_email)) { //-- check Account/Program in columns are valid or not
                        $rows = implode(',', $check_email);
                        $this->session->set_flashdata('error', "Donor Email already exist in the system. Please check entries at row number - " . $rows);
                    } else if (!empty($check_account)) { //-- check Account/Program in columns are valid or not
                        $rows = implode(',', $check_account);
                        $this->session->set_flashdata('error', "Account/Program doesn't exist in the system. Please check entries at row number - " . $rows);
                    } else if (!empty($check_city)) { //-- check city in column are unique or not
                        $rows = implode(',', $check_city);
                        $this->session->set_flashdata('error', "City doesn't exist in the system. Please check entries at row number - " . $rows);
                    } else if (!empty($check_date)) {  //-- check dates in column are valid or not
                        $rows = implode(',', $check_date);
                        $this->session->set_flashdata('error', "Invalid date in date column. Please check entries at row number - " . $rows);
                    } else if (!empty($check_postdate)) {   //-- check post dates in column are valid or not
                        $rows = implode(',', $check_postdate);
                        $this->session->set_flashdata('error', "Invalid post date in post_date column. Please check entries at row number - " . $rows);
                    } else if (!empty($check_amount)) { //-- check amount in column is valid or not
                        $rows = implode(',', $check_amount);
                        $this->session->set_flashdata('error', "Invalid amount in amount column. Please check entries at row number - " . $rows);
                    } else if (!empty($check_payment)) { //-- check payment in column is valid or not
                        $rows = implode(',', $check_payment);
                        $this->session->set_flashdata('error', "Payment type doesn't exist. Please check entries at row number - " . $rows);
                    } else {

                        if (!empty($donor_data)) {

                            //-- Insert dnonor details into database
                            $settings = $this->donors_model->sql_select(TBL_SETTINGS);
                            $settings_arr = [];
                            foreach ($settings as $val) {
                                $settings_arr[$val['setting_key']] = $val['setting_value'];
                            }
                            foreach ($donor_data as $val) {
                                $amount = $val['amount'];
                                $account_id = $val['account_id'];

                                $admin_amount = ($settings_arr['admin-donation-percent'] * $amount) / 100;
                                $admin_amount = round($admin_amount, 2);
                                $account_amount = $amount - $admin_amount;

                                $account = $this->donors_model->sql_select(TBL_ACCOUNTS, 'total_fund,admin_fund', ['where' => ['id' => $account_id]], ['single' => true]);
                                $total_fund = $account['total_fund'];
                                $admin_fund = $account['admin_fund'];

                                $this->db->trans_begin();

                                $donor_id = $this->donors_model->common_insert_update('insert', TBL_DONORS, $val);

                                $fund_array = array(
                                    'account_id' => $account_id,
                                    'donor_id' => $donor_id,
                                    'admin_fund' => $admin_amount,
                                    'account_fund' => $account_amount,
                                    'created' => date('Y-m-d H:i:s')
                                );

                                $this->donors_model->common_insert_update('insert', TBL_FUNDS, $fund_array);

                                $this->donors_model->common_insert_update('update', TBL_ACCOUNTS, ['total_fund' => $total_fund + $account_amount, 'admin_fund' => $admin_fund + $admin_amount], ['id' => $account_id]);
                                $this->donors_model->update_admin_fund($this->admin_fund + $admin_amount);

                                $this->db->trans_complete();
                            }
                            $this->session->set_flashdata('success', "CSV file imported successfully!Donor data added successfully");
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
            redirect('donors');
        } else {
            $this->session->set_flashdata('error', strip_tags($this->upload->display_errors()));
            redirect('donors');
        }
    }

}

/* End of file Donors.php */
/* Location: ./application/controllers/Donors.php */