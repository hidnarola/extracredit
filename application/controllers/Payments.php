<?php

/**
 * Payments Controller - Manage Payments
 * @author KU
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Payments extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('payments_model');
    }

    /**
     * Listing of All payments
     */
    public function index() {
        $data['title'] = 'Extracredit | Payments';
        $this->template->load('default', 'payments/list_payments', $data);
    }

    /**
     * Get payments data for ajax table
     * */
    public function get_payments() {
        $final['recordsFiltered'] = $final['recordsTotal'] = $this->payments_model->get_payments('count');
        $final['redraw'] = 1;
        $payments = $this->payments_model->get_payments('result');
        $start = $this->input->get('start') + 1;

        foreach ($payments as $key => $val) {
            $payments[$key] = $val;
            $payments[$key]['created'] = date('d,M Y', strtotime($val['created']));
        }

        $final['data'] = $payments;
        echo json_encode($final);
    }

    /**
     * Add/edit payments data
     * @param int $id
     * */
    public function add($id = NULL) {
        if (!is_null($id))
            $id = base64_decode($id);
        if (is_numeric($id)) {
            $payment = $this->payments_model->get_payment_details($id);
            if (!empty($payment)) {
                $data['payment'] = $payment;
                $data['title'] = 'Extracredit | Edit Payment';
                $data['heading'] = 'Edit Payment';
                $data['accounts'] = $this->payments_model->sql_select(TBL_ACCOUNTS, 'id,action_matters_campaign,vendor_name', ['where' => ['fund_type_id' => $payment['fund_type_id']]]);
            } else {
                show_404();
            }
        } else {
            $data['title'] = 'Extracredit | Add Payment';
            $data['heading'] = 'Add Payment';
            $data['accounts'] = [];
            $this->form_validation->set_rules('fund_type_id', 'Fund Type', 'trim|required');
            $this->form_validation->set_rules('account_id', 'Program/AMC', 'trim|required');
        }
        $data['fund_types'] = $this->payments_model->sql_select(TBL_FUND_TYPES, 'id,type', ['where' => ['is_delete' => 0]]);

        $this->form_validation->set_rules('check_date', 'Check Date', 'trim|required');
        $this->form_validation->set_rules('check_number', 'Post Date', 'trim|required');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required|numeric');

        if ($this->form_validation->run() == TRUE) {
            $store_admin_fund = $this->payments_model->sql_select(TBL_USERS, 'total_fund,id', ['where' => ['role' => 'admin']], ['single' => true]);

            $dataArr = array(
                'account_id' => $this->input->post('account_id'),
                'amount' => $this->input->post('amount'),
                'check_number' => $this->input->post('check_number'),
                'check_date' => date('Y-m-d', strtotime($this->input->post('check_date'))),
            );
            $amount = $this->input->post('amount');
            $admin_amount = ($settings_arr['admin-donation-percent'] * $amount) / 100;
            $admin_amount = round($admin_amount, 2);
            $account_amount = $amount - $admin_amount;
            $fund_array = array('admin_fund' => $admin_amount, 'account_fund' => $account_amount);

            $this->db->trans_begin();
            if (is_numeric($id)) {
                $account_id = $payment['account_id'];
                $dataArr['account_id'] = $account_id;
                $dataArr['modified'] = date('Y-m-d H:i:s');
                $this->payments_model->common_insert_update('update', TBL_PAYMENTS, $dataArr, ['id' => $id]);

                $fund_array['account_id'] = $account_id;
                $fund_array['donor_id'] = $id;
                $fund_array['modified'] = date('Y-m-d H:i:s');
                $this->payments_model->common_insert_update('update', TBL_FUNDS, $fund_array, ['account_id' => $account_id, 'donor_id' => $id]);
                $this->session->set_flashdata('success', 'Donor details has been updated successfully.');

                //---get account's total fund 
                $account = $this->payments_model->sql_select(TBL_ACCOUNTS, 'total_fund,admin_fund', ['where' => ['id' => $account_id]], ['single' => true]);
                $total_fund = $account['total_fund'] - $donor['account_fund'];
                $admin_fund = $account['admin_fund'] - $donor['admin_fund'];
                $total_admin_fund = $store_admin_fund['total_fund'] - $donor['admin_fund'];
            } else {
                $account_id = $this->input->post('account_id');
                $dataArr['created'] = date('Y-m-d H:i:s');
                $id = $this->payments_model->common_insert_update('insert', TBL_PAYMENTS, $dataArr);

                $fund_array['account_id'] = $account_id;
                $fund_array['donor_id'] = $id;
                $fund_array['created'] = date('Y-m-d H:i:s');
                $this->payments_model->common_insert_update('insert', TBL_FUNDS, $fund_array);
                $this->session->set_flashdata('success', 'Donor has been added successfully');

                //---get account's total fund 
                $account = $this->payments_model->sql_select(TBL_ACCOUNTS, 'total_fund,admin_fund', ['where' => ['id' => $account_id]], ['single' => true]);
                $total_fund = $account['total_fund'];
                $admin_fund = $account['admin_fund'];
                $total_admin_fund = $store_admin_fund['total_fund'];
            }

            $this->payments_model->common_insert_update('update', TBL_ACCOUNTS, ['total_fund' => $total_fund + $account_amount, 'admin_fund' => $admin_fund + $admin_amount], ['id' => $account_id]);
            $this->payments_model->common_insert_update('update', TBL_USERS, ['total_fund' => $total_admin_fund + $admin_amount], ['id' => $store_admin_fund['id']]);
            $this->db->trans_complete();

            redirect('payments');
        }
        $this->template->load('default', 'payments/form', $data);
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
        $accounts = $this->payments_model->sql_select(TBL_ACCOUNTS, 'id,action_matters_campaign,vendor_name', ['where' => ['is_delete' => 0, 'fund_type_id' => $id]]);
        echo json_encode($accounts);
    }

    /**
     * Ajax call to this funnction return Account fund 
     */
    public function get_account_fund() {
        $id = base64_decode($this->input->post('id'));
        $account_details = $this->payments_model->get_account_fund($id);
        if ($account_details['is_vendor'] == 1) {
            $data = ['amount' => $account_details['total_fund']];
        } else {
            $store_admin_fund = $this->payments_model->sql_select(TBL_USERS, 'total_fund,id', ['where' => ['role' => 'admin']], ['single' => true]);
            $data = ['amount' => $store_admin_fund['total_fund']];
        }
        echo json_encode($data);
    }

}

/* End of file Payments.php */
/* Location: ./application/controllers/Payments.php */