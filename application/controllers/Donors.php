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
            $store_admin_fund = $this->donors_model->sql_select(TBL_USERS, 'total_fund,id', ['where' => ['role' => 'admin']], ['single' => true]);

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
                $total_admin_fund = $store_admin_fund['total_fund'] - $donor['admin_fund'];
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
                $total_admin_fund = $store_admin_fund['total_fund'];
            }

            $this->donors_model->common_insert_update('update', TBL_ACCOUNTS, ['total_fund' => $total_fund + $account_amount, 'admin_fund' => $admin_fund + $admin_amount], ['id' => $account_id]);
            $this->donors_model->common_insert_update('update', TBL_USERS, ['total_fund' => $total_admin_fund + $admin_amount], ['id' => $store_admin_fund['id']]);
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

}

/* End of file Donors.php */
/* Location: ./application/controllers/Donors.php */