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
        $data['title'] = 'Extracredit | Donors';
        $this->template->load('default', 'donors/list_donors', $data);
    }

    /**
     * Get donors data for ajax table
     * */
    public function get_donors() {
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
        $data['title'] = 'Extracredit | Donors Communication';
        $data['id'] = $id;
        $this->template->load('default', 'donors/list_communication', $data);
    }

    /**
     * Get Donors communication data for ajax table
     * */
    public function get_donors_communication($id) {
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
     */
    public function import_donor() {
        $condition = array();
        $sku_arr = array();
        $this->data['product_details'] = $this->product_model->get_all_details('shopsy_seller_product', $condition)->result_array();
        for ($sku_cnt = 0; $sku_cnt < sizeof($this->data['product_details']); $sku_cnt++) {
            array_push($sku_arr, $this->data['product_details'][$sku_cnt]['sku']);
        }

        header('Content-Type: text/html; charset=UTF-8');

        $file = $this->input->post('upload_csv');
        $fileDirectory = './images/csv';
        if (!is_dir($fileDirectory)) {
            mkdir($fileDirectory, 0777);
        }
        $config['overwrite'] = FALSE;
        $config['remove_spaces'] = TRUE;
        $config['upload_path'] = $fileDirectory;
        $config['allowed_types'] = '*';
        $this->load->library('upload', $config);
        $file_element_name = 'upload_csv';
        if ($this->upload->do_upload('upload_csv')) {
            $fileDetails = $this->upload->data();


            $coun = 0;
            $row = 1;
            $handle = fopen($fileDirectory . "/" . $fileDetails['file_name'], "r");
            if (($data2 = fgetcsv($handle, 10000, ",")) !== FALSE) {
                $data_format2 = array('type', 'product_condition', 'when_did_you_make_it', 'category', 'name', 'arabic_name', 'description', 'arabic_description', 'image', 'price', 'quantity', 'country', 'ship_cost', 'ship_cost_with_other', 'weight', 'processing_time', 'sku', 'brand', 'model', 'width', 'height', 'depth', 'product_type', 'condition', 'warranty', 'country_origin', 'variation1', 'value1', 'variation2', 'value2', 'offer', 'deal_date', 'deal_date_to', 'deal_time_from', 'deal_time_to', 'discount');
                #print_r($data_format2); echo "<br>"; print_r($data2);die;
                if ($data_format2 == $data2) {
                    fclose($handle);
                    $handle = fopen($fileDirectory . "/" . $fileDetails['file_name'], "r");
                    $name_test = array();
                    $seourl_test = array();
                    $sku_test = array();
                    while (($col_data = fgetcsv($handle, 10000, ",")) !== FALSE) {
                        if ($row == 1) {
                            $row++;
                            continue;
                        }
                        if ($col_data[3] == '' || $col_data[4] == '' || $col_data[6] == '' || $col_data[8] == '' || $col_data[9] == '' || $col_data[10] == '' || $col_data[14] == '' || $col_data[15] == '' || $col_data[16] == '' || $col_data[17] == '' || $col_data[19] == '' || $col_data[20] == '' || $col_data[21] == '') {
                            fclose($handle);
                            $this->setErrorMessage('error', 'Some fields are missing in Row No. ' . $row);
                            redirect('upload-products-csv', 'refresh');
                        }
                        $sku_test[] = $col_data[16];
                        $row++;
                    }

                    if (count(array_unique($sku_test)) != count($sku_test)) {
                        fclose($handle);
                        $this->setErrorMessage('error', 'Duplicate value in sku column.');
                        redirect('upload-products-csv', 'refresh');
                    }

                    $handle = fopen($fileDirectory . "/" . $fileDetails['file_name'], "r");
                    $coun = 0;
                    $row = 1;
                    $this->data['sku_error_row'] = array();
                    while (($col_data = fgetcsv($handle, 10000, ",")) !== FALSE) {
                        if ($row == 1) {
                            $row++;
                            continue;
                        }
                        if (in_array($col_data[16], $sku_arr)) {
                            array_push($this->data['sku_error_row'], $col_data[16]);
                        }
                        $row++;
                    }

                    if (!empty($this->data['sku_error_row'])) {
                        fclose($handle);
                        $this->setErrorMessage('error', 'SKU Already Exists');
                        redirect('upload-products-csv', $this->data);
                    } else {
                        fclose($handle);
                    }
                } else {
                    fclose($handle);
                    $this->setErrorMessage('error', 'The columns in this csv file does not match to the database');
                    redirect('upload-products-csv', 'refresh');
                }
            }
            fclose($handle);

            $coun = 0;
            $row = 1;
            $handle = fopen($fileDirectory . "/" . $fileDetails['file_name'], "r");
            if (($data1 = fgetcsv($handle, 10000, ",")) !== FALSE) {
                $data_format = array('type', 'product_condition', 'when_did_you_make_it', 'category', 'name', 'arabic_name', 'description', 'arabic_description', 'image', 'price', 'quantity', 'country', 'ship_cost', 'ship_cost_with_other', 'weight', 'processing_time', 'sku', 'brand', 'model', 'width', 'height', 'depth', 'product_type', 'condition', 'warranty', 'country_origin', 'variation1', 'value1', 'variation2', 'value2', 'offer', 'deal_date', 'deal_date_to', 'deal_time_from', 'deal_time_to', 'discount');
                if ($data_format == $data1) {
                    fclose($handle);
                    $handle = fopen($fileDirectory . "/" . $fileDetails['file_name'], "r");
                    while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
                        if ($row == 1) {
                            $row++;
                            continue;
                        }
                        $finalImgArr = array();

                        $who_made_it = $data[0];
                        $what_is_it = $data[1];
                        $when_did_you_make_it = $data[2];
                        $category = $data[3];
                        $name = $data[4];
                        $arabic_name = $data[5];
                        $description = $data[6];
                        $arabic_description = $data[7];
                        $image = $data[8];
                        $price = $data[9];
                        $quantity = $data[10];
                        $country = $data[11];
                        $ship_cost = $data[12];
                        $ship_cost_with_other = $data[13];
                        $weight = $data[14];
                        $processing_time = $data[15];
                        $sku = $data[16];
                        $brand = $data[17];
                        $model = $data[18];
                        $width = $data[19];
                        $height = $data[20];
                        $depth = $data[21];
                        $product_type = $data[22];
                        $condition = $data[23];
                        $warranty = $data[24];
                        $country_origin = $data[25];
                        $variation1 = $data[26];
                        $value1 = $data[27];
                        $variation2 = $data[28];
                        $value2 = $data[29];
                        $offer = strtolower($data[30]);
                        $deal_date = $data[31];
                        $deal_date_to = $data[32];
                        $deal_time_from = $data[33];
                        $deal_time_to = $data[34];
                        $discount = $data[35];

                        $price_range = 0;
                        if ($sale_price > 0 && $sale_price < 21)
                            $price_range = '1-20';
                        else if ($sale_price > 20 && $sale_price < 101)
                            $price_range = '21-100';
                        else if ($sale_price > 100 && $sale_price < 201)
                            $price_range = '101-200';
                        else if ($sale_price > 200 && $sale_price < 501)
                            $price_range = '201-500';
                        else if ($sale_price > 500)
                            $price_range = '501+';

                        if ($who_made_it == 'handmade') {
                            $made_by = 1;
                        } else if ($who_made_it == 'vintage') {
                            $made_by = 2;
                        } else if ($who_made_it == 'craft supply') {
                            $made_by = 3;
                        } else {
                            $made_by = '';
                        }

                        if ($what_is_it == 'finished') {
                            $product_condition = 1;
                        } else if ($what_is_it == 'unfinished') {
                            $product_condition = 2;
                        } else {
                            $product_condition = '';
                        }

                        $maked_on = str_replace('-', ',', $when_did_you_make_it);

                        $variation_value1 = explode('|', $value1);
                        $variation_value2 = explode('|', $value2);
                        if (count($variation_value1) > 0) {
                            $variation = 'yes';
                        } else {
                            $variation = 'no';
                        }

                        // Default Value
                        if ($quantity == '') {
                            $quantity = 1;
                        }
                        if ($warranty == '' || ($warranty != "doesn't apply" && $warranty != "3 months" && $warranty != "6 months" && $warranty != "12 months" && $warranty != "2 years" && $warranty != "3 years" && $warranty != "4 years" && $warranty != "5 years")) {
                            $warranty = "doesn't apply";
                        }

                        if ($sku == '') {
                            $sku = $this->input->post('seller_list') . time();
                        }
                        if ($weight == '') {
                            $weight = 5;
                        }
                        if ($width == '' || $height == '' || $depth == '') {
                            $width = 1;
                            $height = 1;
                            $depth = 1;
                        }
                        if ($processing_time == '') {
                            $processing_time = "1-3 business day";
                        }
                        if ($country == '') {
                            $country = 'UAE';
                        }
                        if ($product_type == '') {
                            $product_type = 'New';
                        }
                        if ($product_type == 'New') {
                            $condition = '';
                        }

                        $db_product_name = $this->product_model->get_all_details(PRODUCT, array('product_name' => $name))->result_array();

                        $seller_product_id = mktime();
                        $checkId = $this->check_product_id($seller_product_id);
                        while ($checkId->num_rows() > 0) {
                            $seller_product_id = mktime();
                            $checkId = $this->check_product_id($seller_product_id);
                        }

                        /*                         * **----------Move image to server-------------*** */

                        $image_url = $image;
                        $image = rtrim($image, ",");
                        $imageurlArr = @explode(',', $image);
                        foreach ($imageurlArr as $image_url) {

                            // echo $image_url;
                            // die;
                            $img = @getimagesize($image_url);
                            if ($img) {
                                $image_url = $image_url;
                            } else {
                                $image_url = base_url() . "images/dummyProductImage.jpg";
                            }
                            //echo $image_url;
                            $img_data = file_get_contents($image_url);
                            $image_url = urldecode($image_url);
                            $img_full_name = substr($image_url, strrpos($image_url, '/') + 1);
                            $img_name_arr = explode('.', $img_full_name);
                            $img_name = $img_name_arr[0];
                            $ext = $img_name_arr[1];
                            $ext_arr = explode('?', $ext);
                            $ext = $ext_arr[0];
                            if (!$ext)
                                $ext = 'jpg';
                            $new_name = str_replace(array(',', '$', '(', ')', '~', '&', '%20'), '', $img_name . mktime() . '.' . $ext);
                            $new_img = 'images/product/temp_img/' . $new_name;
                            file_put_contents($new_img, $img_data);

                            /*                             * **----------Move image to server-------------*** */

                            $image_name = $new_name;
                            #$this->imageResizeWithSpace(600, 600, $image_name, './images/product/');

                            @copy('./images/product/temp_img/' . $image_name, './images/product/org-image/' . $image_name);
                            @copy('./images/product/temp_img/' . $image_name, './images/product/' . $image_name);
                            $this->ImageResizeWithCrop(550, 350, $image_name, './images/product/');
                            @copy('./images/product/' . $image_name, './images/product/thumb/' . $image_name);
                            $this->ImageResizeWithCrop(141, 181, $image_name, './images/product/thumb/');
                            @copy('./images/product/temp_img/' . $image_name, './images/product/list-image/' . $image_name);
                            $this->ImageResizeWithCrop(75, 75, $image_name, './images/product/list-image/');


                            $finalImgArr[] = $image_name;
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
                        //echo "<pre>";
                        //var_dump($insertdata1);
                        // echo "<pre>";
                        // var_dump($insertdata2);
                        //die;	
                        if (count($db_product_name)) {
                            $insertdata2['product_id'] = $db_product_name[0]['id'];
                        } else {
                            $this->product_model->simple_insert(PRODUCT, $insertdata1);
                            $idArr = $this->product_model->get_last_insert_id();

                            /*                             * ** Image Upload **** */
                            // $cstrong = '';
                            // $random_string = bin2hex( openssl_random_pseudo_bytes(15, $cstrong));
                            // $root_folder = 'images_new/products';
                            // if (!file_exists($root_folder)){mkdir($root_folder,0777,true);}
                            // $folder_name = $root_folder.'/'.$idArr;
                            // if (!file_exists($folder_name)){mkdir($folder_name);}
                            // $img_str = '';
                            // /****** 1st Image ********/
                            // $finalimage_name = rtrim($finalimage_name,',');
                            // $images = explode(',',$finalimage_name);
                            // for($i=0;$i<count($images);$i++){
                            //     $cnt = $i;
                            //     $ext = trim(pathinfo($images[$i], PATHINFO_EXTENSION));
                            //     $folder_name2 = $folder_name.'/'.$cnt;
                            //     if (!file_exists($folder_name2)){mkdir($folder_name2);}
                            //     $this->aws_product_image_upload($folder_name2,$images[$i],$random_string,'products',$idArr,$cnt,AWS_REAL_PATH,AWS_BUCKET);
                            //     $img_str = $img_str.$random_string.'.'.$ext.',';
                            // }
                            // $img_str = rtrim($img_str,',');
                            // $img_dataArr = array('image'=>$img_str,'is_cdn_uploaded'=>1);
                            // $condition = array('id'=>$idArr);
                            // $this->product_model->insert_update('update','shopsy_product',$img_dataArr,$condition);

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
            $this->setErrorMessage('error', strip_tags($this->upload->display_errors()));
            redirect(base_url() . 'upload-products-csv');
        }
    }

}

/* End of file Donors.php */
/* Location: ./application/controllers/Donors.php */