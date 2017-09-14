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
        checkPrivileges('accounts', 'view');
        $data['perArr'] = checkPrivileges('accounts');
        $data['title'] = 'Extracredit | Accounts';
        $this->template->load('default', 'accounts/list_accounts', $data);
    }

    /**
     * Get accounts data for ajax table
     * */
    public function get_accounts() {
        checkPrivileges('accounts', 'view');
        $data['perArr'] = checkPrivileges('accounts');
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
     * @param type $id
     */
    public function add($id = NULL) {
        checkPrivileges('accounts', 'add');
        $data['perArr'] = checkPrivileges('accounts');
        if (!is_null($id))
            $id = base64_decode($id);
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
            $data['cities'] = [];
        }
        $data['fund_types'] = $this->accounts_model->sql_select(TBL_FUND_TYPES, 'id,name,type', ['where' => ['is_delete' => 0]]);
        $data['program_types'] = $this->accounts_model->sql_select(TBL_PROGRAM_TYPES, 'id,type', ['where' => ['is_delete' => 0]]);
        $data['program_status'] = $this->accounts_model->sql_select(TBL_PROGRAM_STATUS, 'id,status', ['where' => ['is_delete' => 0]]);
        $data['states'] = $this->accounts_model->sql_select(TBL_STATES, NULL);

        $this->form_validation->set_rules('fund_type_id', 'Fund Type', 'trim|required');
        $this->form_validation->set_rules('contact_name', 'Contact Name', 'trim|required');
        $this->form_validation->set_rules('address', 'Address', 'trim|required');
        $this->form_validation->set_rules('state_id', 'State', 'trim|required');
        $this->form_validation->set_rules('city_id', 'City', 'trim|required');
        $this->form_validation->set_rules('zip', 'Zip', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');
        $this->form_validation->set_rules('website', 'Website', 'trim|required');

        if ($this->input->post('fund_type_id') != '') {
            $fund_type = $this->accounts_model->sql_select(TBL_FUND_TYPES, 'type', ['where' => ['is_delete' => 0, 'id' => $this->input->post('fund_type_id')]], ['single' => true]);
            if ($fund_type['type'] == 1) {
                $this->form_validation->set_rules('vendor_name', 'Vendor Name', 'trim|required');
            } else {
                $this->form_validation->set_rules('action_matters_campaign', 'Action Matters Campaign', 'trim|required');
                $this->form_validation->set_rules('tax_id', 'Tax ID', 'trim|required');
                $this->form_validation->set_rules('program_type_id', 'Prgram Type', 'trim|required');
            }
        } else {
            $this->form_validation->set_rules('action_matters_campaign', 'Action Matters Campaign', 'trim|required');
        }


        if ($this->form_validation->run() == TRUE) {
            $dataArr = array(
                'fund_type_id' => $this->input->post('fund_type_id'),
                'is_active' => ($this->input->post('is_active') == 1) ? 1 : 0,
                'contact_name' => $this->input->post('contact_name'),
                'address' => $this->input->post('address'),
                'state_id' => $this->input->post('state_id'),
                'city_id' => $this->input->post('city_id'),
                'zip' => $this->input->post('zip'),
                'email' => $this->input->post('email'),
                'phone' => $this->input->post('phone'),
                'website' => $this->input->post('website'),
            );
            $fund_type = $this->accounts_model->sql_select(TBL_FUND_TYPES, 'type', ['where' => ['is_delete' => 0, 'id' => $this->input->post('fund_type_id')]], ['single' => true]);
            if ($fund_type['type'] == 1) {
                $dataArr['vendor_name'] = $this->input->post('vendor_name');
                $dataArr['action_matters_campaign'] = NULL;
                $dataArr['tax_id'] = NULL;
                $dataArr['program_type_id'] = NULL;
                $dataArr['program_status_id'] = NULL;
            } else {
                $dataArr['vendor_name'] = NULL;
                $dataArr['action_matters_campaign'] = $this->input->post('action_matters_campaign');
                $dataArr['tax_id'] = $this->input->post('tax_id');
                $dataArr['program_type_id'] = $this->input->post('program_type_id');
                $dataArr['program_status_id'] = $this->input->post('program_status_id');
            }
            if (is_numeric($id)) {
                $dataArr['modified'] = date('Y-m-d H:i:s');
                $this->accounts_model->common_insert_update('update', TBL_ACCOUNTS, $dataArr, ['id' => $id]);
                $this->session->set_flashdata('success', 'Account details has been updated successfully.');
            } else {
                $dataArr['created'] = date('Y-m-d H:i:s');
                $this->accounts_model->common_insert_update('insert', TBL_ACCOUNTS, $dataArr);
                $this->session->set_flashdata('success', 'Account has been added successfully');
            }
            redirect('accounts');
        }
        $this->template->load('default', 'accounts/form', $data);
    }

    /**
     * Edit Account data
     * @param int $id
     * */
    public function edit($id) {
        checkPrivileges('accounts', 'edit');
        $data['perArr'] = checkPrivileges('accounts');
        $this->add($id);
    }

    /**
     * Ajax call to this function return fund type details
     */
    public function get_fund_type() {
        $id = base64_decode($this->input->post('id'));
        $fund_type = $this->accounts_model->sql_select(TBL_FUND_TYPES, 'type,name', ['where' => ['is_delete' => 0, 'id' => $id]], ['single' => true]);
        echo json_encode($fund_type);
    }

    /**
     * Ajax call to this function return fund type details
     */
    public function get_cities() {
        $id = base64_decode($this->input->post('id'));
        $cities = $this->accounts_model->sql_select(TBL_CITIES, 'name,id', ['where' => ['state_id' => $id]]);
        echo json_encode($cities);
    }

    /**
     * This function used to check Unique email at the time of account's add and edit
     * */
    public function checkUniqueEmail($id = NULL) {
        $where = ['email' => trim($this->input->get('email'))];
        if (!is_null($id)) {
            $id = base64_decode($id);
            $where['id!='] = $id;
        }
        $account = $this->accounts_model->sql_select(TBL_ACCOUNTS, 'id', ['where' => $where], ['single' => true]);
        if (!empty($account)) {
            echo "false";
        } else {
            echo "true";
        }
        exit;
    }

    /**
     * Delete account
     * @param int $id
     * */
    public function delete($id = NULL) {
        checkPrivileges('accounts', 'delete');
        $data['perArr'] = checkPrivileges('accounts');
        $id = base64_decode($id);
        if (is_numeric($id)) {
            $account = $this->accounts_model->sql_select(TBL_ACCOUNTS, 'id', ['where' => ['id' => $id]], ['single' => true]);
            if ($account) {
                //-- Check if account is assigned to donor then don't allow to delete that account
                $is_assigned = $this->accounts_model->sql_select(TBL_DONORS, NULL, ['where' => ['account_id' => $id, 'is_delete' => 0]], ['single' => true]);
                if (!empty($is_assigned)) {
                    $this->session->set_flashdata('error', 'You can not delete account,It is assigned to donor');
                    redirect('accounts');
                }
                $update_array = array(
                    'is_delete' => 1
                );
                $this->accounts_model->common_insert_update('update', TBL_ACCOUNTS, $update_array, ['id' => $id]);
                $this->session->set_flashdata('success', 'Account has been deleted successfully!');
            } else {
                $this->session->set_flashdata('error', 'Invalid request. Please try again!');
            }
            redirect('accounts');
        } else {
            show_404();
        }
    }

    /**
     * This function used to check Unique Vedor at the time of account's add and edit
     * */
    public function checkUniqueVendor($id = NULL) {
        $where = ['vendor_name' => trim($this->input->get('vendor_name'))];
        if (!is_null($id)) {
            $id = base64_decode($id);
            $where['id!='] = $id;
        }
        $account = $this->accounts_model->sql_select(TBL_ACCOUNTS, 'id', ['where' => $where], ['single' => true]);
        if (!empty($account)) {
            echo "false";
        } else {
            echo "true";
        }
        exit;
    }

    /**
     * This function used to check Unique AMC at the time of account's add and edit
     * */
    public function checkUniqueAMC($id = NULL) {
        $where = ['action_matters_campaign' => trim($this->input->get('action_matters_campaign'))];
        if (!is_null($id)) {
            $id = base64_decode($id);
            $where['id!='] = $id;
        }
        $account = $this->accounts_model->sql_select(TBL_ACCOUNTS, 'id', ['where' => $where], ['single' => true]);
        if (!empty($account)) {
            echo "false";
        } else {
            echo "true";
        }
        exit;
    }

}

/* End of file Accounts.php */
/* Location: ./application/controllers/Accounts.php */