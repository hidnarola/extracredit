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
        checkPrivileges('payments', 'view');
        $data['perArr'] = checkPrivileges('payments');
        $data['title'] = 'Extracredit | Payments';
        $this->template->load('default', 'payments/list_payments', $data);
    }

    /**
     * Get payments data for ajax table
     * */
    public function get_payments() {
        checkPrivileges('payments', 'view');
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
        $data['perArr'] = checkPrivileges('payments');
        if (!is_null($id))
            $id = base64_decode($id);
        if (is_numeric($id)) {
            $payment = $this->payments_model->get_payment_details($id);
            if (!empty($payment)) {
                $data['payment'] = $payment;
                $data['title'] = 'Extracredit | Edit Payment';
                $data['heading'] = 'Edit Payment';
                if ($payment['type'] == 1) {
                    $data['account_fund'] = $this->admin_fund + $payment['amount'];
                } else {
                    $data['account_fund'] = $payment['total_fund'] + $payment['amount'];
                }
                $data['accounts'] = $this->payments_model->sql_select(TBL_ACCOUNTS, 'id,action_matters_campaign,vendor_name', ['where' => ['fund_type_id' => $payment['fund_type_id']]]);
            } else {
                show_404();
            }
        } else {
            checkPrivileges('payments', 'add');
            $data['title'] = 'Extracredit | Add Payment';
            $data['heading'] = 'Add Payment';
            $data['accounts'] = [];
            $data['account_fund'] = 0;
            $this->form_validation->set_rules('fund_type_id', 'Fund Type', 'trim|required');
            $this->form_validation->set_rules('account_id', 'Program/AMC', 'trim|required');
        }
        $data['fund_types'] = $this->payments_model->sql_select(TBL_FUND_TYPES, 'id,name as type', ['where' => ['is_delete' => 0, 'type!=' => 2]]);

        $this->form_validation->set_rules('check_date', 'Check Date', 'trim|required');
        $this->form_validation->set_rules('check_number', 'Post Date', 'trim|required');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required|numeric');

        if ($this->form_validation->run() == TRUE) {
            $dataArr = array(
                'amount' => $this->input->post('amount'),
                'check_number' => $this->input->post('check_number'),
                'check_date' => date('Y-m-d', strtotime($this->input->post('check_date'))),
            );

            $this->db->trans_begin();
            if (is_numeric($id)) {
                $account_id = $payment['account_id'];
                $dataArr['account_id'] = $account_id;
                $dataArr['modified'] = date('Y-m-d H:i:s');
                //-- If account is vendor then update admin fund amount
                $is_valid = 1;
                if ($payment['type'] == 1) {
                    $admin_fund = $this->admin_fund + $payment['amount'];
                    $dataArr['account_fund'] = $admin_fund;
                    if ($admin_fund >= $this->input->post('amount')) {
                        $admin_fund = $admin_fund - $this->input->post('amount');
                        $this->payments_model->update_admin_fund($admin_fund);
                    } else {
                        $is_valid = 0;
                        $this->session->set_flashdata('error', 'Fail to update payment! You have entered more amount than Admin fund');
                    }
                } else {
                    $account_fund = $payment['total_fund'] + $payment['amount'];
                    $dataArr['account_fund'] = $account_fund;
                    if ($account_fund >= $this->input->post('amount')) {
                        $account_fund = $account_fund - $this->input->post('amount');
                        $this->payments_model->common_insert_update('update', TBL_ACCOUNTS, ['total_fund' => $account_fund], ['id' => $account_id]);
                    } else {
                        $is_valid = 0;
                        $this->session->set_flashdata('error', 'Fail to update payment! You have entered more amount than Account fund');
                    }
                }

                if ($is_valid == 1) {
                    $this->payments_model->common_insert_update('update', TBL_PAYMENTS, $dataArr, ['id' => $id]);
                    $this->session->set_flashdata('success', 'Payment details has been updated successfully.');
                }
            } else {
                $account_id = $this->input->post('account_id');
                $dataArr['account_id'] = $account_id;
                $dataArr['created'] = date('Y-m-d H:i:s');

                $account_details = $this->payments_model->get_account_fund($account_id);
                $is_valid = 1;
                if ($account_details['type'] == 1) {
                    $admin_fund = $this->admin_fund;
                    $dataArr['account_fund'] = $admin_fund;

                    if ($admin_fund >= $this->input->post('amount')) {
                        $admin_fund = $admin_fund - $this->input->post('amount');
                        $this->payments_model->update_admin_fund($admin_fund);
                    } else {
                        $is_valid = 0;
                        $this->session->set_flashdata('error', 'Fail to update payment! You have entered more amount than Admin fund');
                    }
                } else {
                    $account_fund = $account_details['total_fund'];
                    $dataArr['account_fund'] = $account_fund;

                    if ($account_fund >= $this->input->post('amount')) {
                        $account_fund = $account_fund - $this->input->post('amount');
                        $this->payments_model->common_insert_update('update', TBL_ACCOUNTS, ['total_fund' => $account_fund], ['id' => $account_id]);
                    } else {
                        $is_valid = 0;
                        $this->session->set_flashdata('error', 'Fail to update payment! You have entered more amount than Account fund');
                    }
                }
                if ($is_valid == 1) {
                    $id = $this->payments_model->common_insert_update('insert', TBL_PAYMENTS, $dataArr);
                    $this->session->set_flashdata('success', 'Payment has been added successfully');
                }
            }

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
        checkPrivileges('payments', 'edit');
        $data['perArr'] = checkPrivileges('payments');
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
        //-- If not vendor then return accounts fund else return admin fund
        if ($account_details['type'] == 0) {
            $data = ['amount' => $account_details['total_fund'], 'type' => 0];
        } else {
            $store_admin_fund = $this->payments_model->sql_select(TBL_USERS, 'total_fund,id', ['where' => ['role' => 'admin']], ['single' => true]);
            $data = ['amount' => $store_admin_fund['total_fund'], 'type' => 1];
        }
        echo json_encode($data);
    }

    /**
     * Delete Payment
     * @param int $id
     * */
    public function delete($id = NULL) {
        checkPrivileges('payments', 'delete');
        $id = base64_decode($id);
        if (is_numeric($id)) {
            $payment = $this->payments_model->get_payment_details($id);
            if ($payment) {
                $update_array = array(
                    'is_delete' => 1
                );

                $this->db->trans_begin();
                $this->payments_model->common_insert_update('update', TBL_PAYMENTS, $update_array, ['id' => $id]);

                if ($payment['type'] == 1) {
                    $admin_fund = $this->admin_fund + $payment['amount'];
                    $this->payments_model->update_admin_fund($admin_fund);
                } else {
                    $account_fund = $payment['total_fund'] + $payment['amount'];
                    $this->payments_model->common_insert_update('update', TBL_ACCOUNTS, ['total_fund' => $account_fund], ['id' => $payment['account_id']]);
                }

                $this->db->trans_complete();
                $this->session->set_flashdata('success', 'Payment has been deleted successfully!');
            } else {
                $this->session->set_flashdata('error', 'Invalid request. Please try again!');
            }
            redirect('payments');
        } else {
            show_404();
        }
    }

}

/* End of file Payments.php */
/* Location: ./application/controllers/Payments.php */