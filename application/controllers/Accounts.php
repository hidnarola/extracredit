<?php

/**
 * Accounts Controller - Manage Program/Accounts
 * @author KU
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Accounts extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('accounts_model');
    }

    /**
     * Listing of All Accounts
     */
    public function index() {
        $data['title'] = 'Extracredit | Accounts';
        $this->template->load('default', 'accounts/list_accounts', $data);
    }

    /**
     * Get accounts data for ajax table
     * */
    public function get_accounts() {
        $final['recordsFiltered'] = $final['recordsTotal'] = $this->accounts_model->get_accounts('count');
        $final['redraw'] = 1;
        $accounts = $this->accounts_model->get_accounts('result');
        $start = $this->input->get('start') + 1;

        foreach ($accounts as $key => $val) {
            $accounts[$key] = $val;
            $accounts[$key]['sr_no'] = $start++;
            $accounts[$key]['created'] = date('d,M Y', strtotime($val['created']));
        }

        $final['data'] = $accounts;
        echo json_encode($final);
    }

    /**
     * Add /edit accounts data
     * @param int $id
     * */
    public function add($id = NULL) {
        if (!is_null($id))
            $id = base64_decode($id);
        $data['cities'] = [];
        if (is_numeric($id)) {
            $account = $this->accounts_model->get_account_details($id);
            if ($account) {
                $data['account'] = $account;
                $data['title'] = 'Extracredit | Edit Account';
                $data['heading'] = 'Edit Account';
                $data['cities'] = $this->accounts_model->sql_select(TBL_CITIES, NULL, ['where' => ['state_id' => $account['state_id']]]);
            } else {
                show_404();
            }
        } else {
            $data['title'] = 'Extracredit | Add Account';
            $data['heading'] = 'Add Account';
        }
        $data['fund_types'] = $this->accounts_model->sql_select(TBL_FUND_TYPES, 'id,type,is_vendor', ['where' => ['is_delete' => 0]]);
        $data['states'] = $this->accounts_model->sql_select(TBL_STATES, NULL);

        $this->form_validation->set_rules('fund_type', 'Fund Type', 'trim|required');
        if ($this->input->post('fund_type_id') != '') {
            $fund_type = $this->accounts_model->sql_select(TBL_FUND_TYPES, 'is_vendor', ['where' => ['is_delete' => 0]], ['single' => true]);
            if ($fund_type['is_vendor'] == 1) {
                $this->form_validation->set_rules('vendor_name', 'Vendor Name', 'trim|required');
            } else {
                $this->form_validation->set_rules('action_matters_campaign', 'Action Matters Campaign', 'trim|required');
            }
        } else {
            $this->form_validation->set_rules('action_matters_campaign', 'Action Matters Campaign', 'trim|required');
        }
        $this->form_validation->set_rules('conatact_name', 'Contact Name', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');

        if ($this->form_validation->run() == TRUE) {

            $dataArr = array(
                'fund_type_id' => $this->input->post('fund_type'),
                'is_active' => ($this->input->post('is_active') == 1) ? 1 : 0,
            );
            if (is_numeric($id)) {
                $dataArr['modified'] = date('Y-m-d H:i:s');
                $this->accounts_model->common_insert_update('update', TBL_ACCOUNTS, $dataArr, ['id' => $id]);
                $this->session->set_flashdata('success', 'User\'s data has been updated successfully.');
            } else {
                $this->accounts_model->common_insert_update('insert', TBL_ACCOUNTS, $dataArr);
                $this->session->set_flashdata('success', 'User has been added successfully and Email has been sent to user successfully');
            }
            redirect('accounts');
        }
        $this->template->load('default', 'accounts/form', $data);
    }

    /**
     * Ajax call to this function return fund type details
     */
    public function get_fund_type() {
        $id = base64_decode($this->input->post('id'));
        $fund_type = $this->accounts_model->sql_select(TBL_FUND_TYPES, 'type,is_vendor', ['where' => ['is_delete' => 0, 'id' => $id]], ['single' => true]);
        echo json_encode($fund_type);
    }

    /**
     * Ajax call to this function return fund type details
     */
    public function get_cities() {
        $id = base64_decode($this->input->post('id'));
        $cities = $this->accounts_model->sql_select(TBL_CITIES, 'name,state_id', ['where' => ['state_id' => $id]]);
        echo json_encode($cities);
    }

}

/* End of file Accounts.php */
/* Location: ./application/controllers/Accounts.php */