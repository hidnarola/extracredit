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
        $data['fund_types'] = $this->donors_model->sql_select(TBL_FUND_TYPES, 'id,type', ['where' => ['is_delete' => 0]]);
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
        $accounts = $this->donors_model->get_all_accounts();
        $account_name_arr = array_column($accounts, 'action_matters_campaign');
        $vendor_name_arr = array_column($accounts, 'vendor_name');

        //-- Remove blank values from Account name and vendor name array
        $account_name_arr = array_filter($account_name_arr);
        $vendor_name_arr = array_filter($vendor_name_arr);

        $cities = $this->donors_model->sql_select(TBL_CITIES);
        $cities_arr = array_column($cities, 'name');


        $fileDirectory = DONORS_CSV;
        $file = $this->input->post('import_donor');
        $config['overwrite'] = FALSE;
        $config['remove_spaces'] = TRUE;
        $config['upload_path'] = $fileDirectory;
        $config['allowed_types'] = 'csv|CSV';
        $this->load->library('upload', $config);
        $file_element_name = 'import_donor';

        //-- Upload csv file
        if ($this->upload->do_upload('import_donor')) {
            $fileDetails = $this->upload->data();

            $accounts = $this->donors_model->get_all_accounts();
            $account_name_arr = array_column($accounts, 'action_matters_campaign');
            $vendor_name_arr = array_column($accounts, 'vendor_name');

            $cities = $this->donors_model->sql_select(TBL_CITIES);
            $payment_types = $this->donors_model->sql_select(TBL_PAYMENT_TYPES, 'type', ['where' => ['is_delete' => 0]]);
            $donor_emails = $this->donors_model->sql_select(TBL_DONORS, 'email', ['where' => ['is_delete' => 0]]);
            $donor_emails = $this->donors_model->sql_select(TBL_DONORS, 'email', ['where' => ['is_delete' => 0]]);
            $donor_emails = array_column($donor_emails, 'email');

            $coun = 0;
            $row = 1;
            $handle = fopen($fileDirectory . "/" . $fileDetails['file_name'], "r");
            $error = [];
            if (($data2 = fgetcsv($handle)) !== FALSE) {
                $data_format2 = array('program/amc', 'firstname', 'lastname', 'email', 'address', 'city', 'zip', 'date', 'post_date', 'amount', 'payment_type', 'payment_number', 'memo');

                if ($data_format2 == $data2) {
                    fclose($handle);
                    $handle = fopen($fileDirectory . "/" . $fileDetails['file_name'], "r");
                    $email_test = $account_name = array();
                    while (($col_data = fgetcsv($handle)) !== FALSE) {
                        if ($col_data[0] == '' || $col_data[1] == '' || $col_data[2] == '' || $col_data[3] == '' || $col_data[4] == '' || $col_data[5] == '' || $col_data[6] == '' || $col_data[7] == '' || $col_data[8] == '' || $col_data[9] == '' || $col_data[10] == '' || $col_data[11] == '') {
                            fclose($handle);
                            $this->session->set_flashdata('error', 'Some fields are missing in Row No. ' . $row);
                            redirect('donors');
                        }
                        $email_test[] = $col_data[3];
                        $row++;
                    }

                    if (count(array_unique($email_test)) != count($email_test)) {
                        fclose($handle);
                        $this->session->set_flashdata('error', 'Duplicate value in email column.');
                        redirect('donors');
                    }

                    $handle = fopen($fileDirectory . "/" . $fileDetails['file_name'], "r");
                    $coun = 0;
                    $row = 1;
                    $email_error_row = array();
                    while (($col_data = fgetcsv($handle)) !== FALSE) {
                        if ($row == 1) {
                            $row++;
                            continue;
                        }
                        if (in_array($col_data[3], $donor_emails)) {
                            array_push($email_error_row, $col_data[3]);
                        }
                        $row++;
                    }

                    if (!empty($email_error_row)) {
                        fclose($handle);
                        $this->session->set_flashdata('error', 'Donor email already exist!');
                        redirect('donors');
                    } else {
                        fclose($handle);
                    }
                } else {
                    fclose($handle);
                    $this->session->set_flashdata('error', 'The columns in this csv file does not match to the database');
                    redirect('donors');
                }
            }
            fclose($handle);

            $coun = 0;
            $row = 1;
            $handle = fopen($fileDirectory . "/" . $fileDetails['file_name'], "r");
            if (($data1 = fgetcsv($handle)) !== FALSE) {
                $data_format2 = array('program/amc', 'firstname', 'lastname', 'email', 'address', 'city', 'zip', 'date', 'post_date', 'amount', 'payment_type', 'payment_number', 'memo');
                if ($data_format == $data1) {
                    fclose($handle);
                    $handle = fopen($fileDirectory . "/" . $fileDetails['file_name'], "r");
                    while (($data = fgetcsv($handle)) !== FALSE) {
                        if ($row == 1) {
                            $row++;
                            continue;
                        }
                        $finalImgArr = array();

                        $program_amc = $data[0];
                        $firstname = $data[1];
                        $lastname = $data[2];
                        $email = $data[3];
                        $address = $data[4];
                        $city = $data[5];
                        $zip = $data[6];
                        $date = $data[7];
                        $post_date = $data[8];
                        $amount = $data[9];
                        $payment_type = $data[10];
                        $payment_number = $data[11];
                        $memo = $data[12];

                        $donor_arr = [];

                        $db_product_name = $this->product_model->get_all_details(PRODUCT, array('product_name' => $name))->result_array();

                        $seller_product_id = mktime();
                        $checkId = $this->check_product_id($seller_product_id);
                        while ($checkId->num_rows() > 0) {
                            $seller_product_id = mktime();
                            $checkId = $this->check_product_id($seller_product_id);
                        }


                        $finalimage_name = implode(',', $finalImgArr);
                        $category_Arr = explode(':-:', $category);
                        foreach ($category_Arr as $category) {
                            if (is_numeric($category)) {
                                $where_condition = array('id' => $category);
                            } else {
                                $where_condition = array('cat_name' => $category);
                            }
                            $catArr = $this->product_model->get_all_details(CATEGORY, $where_condition);
                            if ($catArr->num_rows() > 0) {
                                $category_id_arr = array($catArr->row()->id);
                                while ($catArr->row()->rootID > 0) {
                                    $catArr = $this->product_model->get_all_details(CATEGORY, array('id' => $catArr->row()->rootID));
                                    if ($catArr->num_rows() > 0) {
                                        $category_id_arr[] = $catArr->row()->id;
                                    } else {
                                        break;
                                    }
                                }
                                $category_id = implode(',', array_reverse($category_id_arr));
                            } else {
                                $catArr = $this->product_model->get_all_details(CATEGORY, array('cat_name' => 'Miscellaneous'));
                                $category_id = $catArr->row()->id;
                            }
                        }

                        $seourlBase = $seourl = url_title($name, '-', TRUE);
                        $seourl_check = '0';
                        $duplicate_url = $this->product_model->get_all_details(PRODUCT, array('seourl' => $seourl));
                        if ($duplicate_url->num_rows() > 0) {
                            $seourl = $seourlBase . '-' . $duplicate_url->num_rows();
                        } else {
                            $seourl_check = '1';
                        }
                        $urlCount = $duplicate_url->num_rows();
                        while ($seourl_check == '0') {
                            $urlCount++;
                            $duplicate_url = $this->product_model->get_all_details(PRODUCT, array('seourl' => $seourl));
                            if ($duplicate_url->num_rows() > 0) {
                                $seourl = $seourlBase . '-' . $urlCount;
                            } else {
                                $seourl_check = '1';
                            }
                        }
                        $modifyDate = '';
                        if ($this->checkLogin('U') == 1) {
                            $status = 'Publish';
                            $pay_status = 'Paid';
                        } else {
                            $status = 'UnPublish';
                            $pay_status = 'paid';
                        }

                        $ship_duration = '';
                        $insertdata1 = array(
                            'seller_product_id' => $seller_product_id,
                            'modified' => $modifyDate,
                            'product_name' => $name,
                            'product_name_ar' => $arabic_name,
                            'arabic_description' => $arabic_description,
                            'weight' => $weight,
                            'description' => $description,
                            'image' => $finalimage_name,
                            'category_id' => $category_id,
                            'status' => $status,
                            'seourl' => $seourl,
                            'brand' => $brand,
                            'model' => $model,
                            'width' => $width,
                            'height' => $height,
                            'depth' => $depth
                        );

                        $insertdata2 = array(
                            'made_by' => $made_by,
                            'product_condition' => $product_condition,
                            'modified' => $modifyDate,
                            'maked_on' => $maked_on,
                            'ship_duration' => $processing_time,
                            'price' => $price,
                            'base_price' => $price,
                            'quantity' => $quantity,
                            'status' => 'UnPublish',
                            'pay_status' => $pay_status,
                            'ship_from' => $country,
                            'user_id' => $this->input->post('seller_list'),
                            'sku' => $sku,
                            'type' => $product_type,
                            'condition' => $condition,
                            'warranty' => $warranty,
                            'country_origin' => $country_origin,
                            'variation' => $variation,
                            'offer' => $offer,
                            'deal_date' => $deal_date,
                            'deal_date_to' => $deal_date_to,
                            'deal_time_from' => $deal_time_from,
                            'deal_time_to' => $deal_time_to,
                            'discount' => $discount
                        );

                        if (count($db_product_name)) {
                            $insertdata2['product_id'] = $db_product_name[0]['id'];
                        } else {
                            $this->product_model->simple_insert(PRODUCT, $insertdata1);
                            $idArr = $this->product_model->get_last_insert_id();
                            $insertdata2['product_id'] = $idArr;
                        }

                        $this->product_model->simple_insert('shopsy_seller_product', $insertdata2);
                        $sp_id = $this->product_model->get_last_insert_id();
                        if ($variation == 'yes') {
                            if ($variation_value1[0] != '') {
                                for ($i = 0; $i < count($variation_value1); $i++) {
                                    $variation_attr = explode(':', $variation_value1[$i]);
                                    $attr_data_arr = array(
                                        'attr_name' => $variation1,
                                        'attr_value' => $variation_attr[0],
                                        'pricing' => $variation_attr[1],
                                        'stock_status' => '1',
                                        'product_id' => $sp_id
                                    );
                                    $this->product_model->add_subproduct_insert($attr_data_arr);
                                }
                            }

                            if ($variation_value2[0] != '') {
                                for ($i = 0; $i < count($variation_value2); $i++) {
                                    $variation_attr = explode(':', $variation_value2[$i]);
                                    $attr_data_arr = array(
                                        'attr_name' => $variation2,
                                        'attr_value' => $variation_attr[0],
                                        'pricing' => $variation_attr[1],
                                        'stock_status' => '1',
                                        'product_id' => $sp_id
                                    );
                                    $this->product_model->add_subproduct_insert($attr_data_arr);
                                }
                            }
                        }
                        //echo $this->db->last_query();die;
                        $ship_to = $this->input->post('shipping_to');
                        $ship_to_id = $this->input->post('ship_to_id');

                        $cost_individual = $ship_cost;
                        $cost_with_another = $ship_cost_with_other;
                        $shipName = $country;

                        $countryInfo = $this->product_model->get_all_details(COUNTRY_LIST, array('name' => $country));
                        $shipId = $countryInfo->row()->id;

                        $seourlBase = $seourl = url_title($shipName, '-', TRUE);
                        $seourl_check = '0';
                        $duplicate_url = $this->product_model->get_all_details(SUB_SHIPPING, array('ship_seourl' => $seourl));
                        if ($duplicate_url->num_rows() > 0) {
                            $seourl = $seourlBase . '-' . $duplicate_url->num_rows();
                        } else {
                            $seourl_check = '1';
                        }
                        $urlCount = $duplicate_url->num_rows();
                        while ($seourl_check == '0') {
                            $urlCount++;
                            $duplicate_url = $this->product_model->get_all_details(SUB_SHIPPING, array('ship_seourl' => $seourl));
                            if ($duplicate_url->num_rows() > 0) {
                                $seourl = $seourlBase . '-' . $urlCount;
                            } else {
                                $seourl_check = '1';
                            }
                        }

                        $dataArrShip = array('product_id' => $sp_id,
                            'ship_id' => $shipId,
                            'ship_name' => $shipName,
                            'ship_cost' => $cost_individual,
                            'ship_seourl' => $seourl,
                            'ship_other_cost' => $cost_with_another
                        );
                        $this->product_model->simple_insert(SUB_SHIPPING, $dataArrShip);

                        $usrdetails = $this->product_model->get_all_details(USERS, array('id' => $this->checkLogin('U')));
                        if ($usrdetails->num_rows() > 0) {
                            $prodCount = $usrdetails->row()->products;
                            $prodCount = $prodCount + 1;
                            $this->product_model->update_details(USERS, array('products' => $prodCount), array('id' => $this->checkLogin('U')));
                        }
                        $row++;
                    }
                    fclose($handle);
                    $this->setErrorMessage('success', 'Your csv file is uploaded and the product details are added');
                    redirect(base_url() . 'upload-products-csv');
                } else {
                    fclose($handle);
                    $this->setErrorMessage('error', 'The coloumns in this csv file does not match to the database');
                    redirect('upload-products-csv');
                }
            }
            fclose($handle);
            $this->setErrorMessage('error', 'The coloumns in this csv file does not match to the database');
            redirect('upload-products');
        } else {
            $this->session->set_flashdata('error', strip_tags($this->upload->display_errors()));
            redirect('donors');
        }
    }

}

/* End of file Donors.php */
/* Location: ./application/controllers/Donors.php */