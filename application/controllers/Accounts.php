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
        $this->load->model('communication_manager_model');
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
        $final['recordsFiltered'] = $final['recordsTotal'] = $this->accounts_model->get_accounts('count');
        $final['redraw'] = 1;
        $final['data'] = $this->accounts_model->get_accounts('result');
        echo json_encode($final);
    }

    /**
     * Add /edit accounts data
     * @param type $id
     */
    public function add($id = NULL) {
        if (!is_null($id))
            $id = base64_decode($id);
        if (is_numeric($id)) {
            $account = $this->accounts_model->get_account_details($id);
            if ($account) {
                $data['account'] = $account;
                $data['title'] = 'Extracredit | Edit Account';
                $data['heading'] = 'Edit Account';
            } else {
                show_404();
            }
        } else {
            //-- Check logged in user has access to add account
            checkPrivileges('accounts', 'add');
            $data['title'] = 'Extracredit | Add Account';
            $data['heading'] = 'Add Account';
            $data['cities'] = [];
        }
        $data['fund_types'] = $this->accounts_model->sql_select(TBL_FUND_TYPES, 'id,name,type', ['where' => ['is_delete' => 0, 'type!=' => 1]], ['order_by' => 'name']);
        $data['program_types'] = $this->accounts_model->sql_select(TBL_PROGRAM_TYPES, 'id,type', ['where' => ['is_delete' => 0]]);
        $data['program_status'] = $this->accounts_model->sql_select(TBL_PROGRAM_STATUS, 'id,status', ['where' => ['is_delete' => 0]]);
        $data['states'] = $this->accounts_model->sql_select(TBL_STATES, NULL);

        $this->form_validation->set_rules('fund_type_id', 'Fund Type', 'trim|required');
        $this->form_validation->set_rules('program_name', 'Program Name', 'trim|required');
        $this->form_validation->set_rules('contact_name', 'Contact Name', 'trim|required');

        if ($this->input->post('fund_type_id') != '') {
            $fund_type = $this->accounts_model->sql_select(TBL_FUND_TYPES, 'type', ['where' => ['is_delete' => 0, 'id' => $this->input->post('fund_type_id')]], ['single' => true]);
            if ($fund_type['type'] == 1) {
                $this->form_validation->set_rules('vendor_name', 'Vendor Name', 'trim|required');
            }
        }

        if ($this->form_validation->run() == TRUE) {
            //-- Get state id from post value
            $state_id = $city_id = NULL;

            $state_code = $this->input->post('state_short');
            if (!empty($state_code)) {
                $post_city = $this->input->post('city_id');
                $state = $this->accounts_model->sql_select(TBL_STATES, 'id', ['where' => ['short_name' => $state_code]], ['single' => true]);
                $state_id = $state['id'];
                if (!empty($post_city)) {
                    $city = $this->accounts_model->sql_select(TBL_CITIES, 'id', ['where' => ['state_id' => $state_id, 'name' => $post_city]], ['single' => true]);
                    if (!empty($city)) {
                        $city_id = $city['id'];
                    } else {
                        $city_id = $this->accounts_model->common_insert_update('insert', TBL_CITIES, ['name' => $post_city, 'state_id' => $state_id]);
                    }
                }
            }

            $dataArr = array(
                'fund_type_id' => $this->input->post('fund_type_id'),
                'program_name' => trim($this->input->post('program_name')),
                'is_active' => ($this->input->post('is_active') == 1) ? 1 : 0,
                'contact_name' => $this->input->post('contact_name'),
                'address' => $this->input->post('address'),
                'state_id' => $state_id,
                'city_id' => $city_id,
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

                if ($account['email'] != $dataArr['email']) {
                    if (!empty($account['email'])) {
                        $subscriber = get_mailchimp_subscriber($account['email']);
                        if (!empty($subscriber)) {
                            $interests = $subscriber['interests'];
                            if ($interests[DONORS_GROUP_ID] == 1 || $interests[GUESTS_GROUP_ID] == 1) {
                                $mailchimp_data = array(
                                    'email_address' => $account['email'],
                                    'interests' => array(ACCOUNTS_GROUP_ID => false)
                                );
                                mailchimp($mailchimp_data);
                            } else {
                                //-- Update old entry to unsubscribed and add new to subscribed
                                /*
                                  $mailchimp_data = array(
                                  'email_address' => $account['email'],
                                  'status' => 'unsubscribed', // "subscribed","unsubscribed","cleaned","pending"
                                  'interests' => array(ACCOUNTS_GROUP_ID => false)
                                  ); */
                                $mailchimp_data = array(
                                    'email_address' => $account['email'],
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
                                'FNAME' => $dataArr['contact_name']
                            ],
                            'interests' => array(ACCOUNTS_GROUP_ID => true)
                        );
                        mailchimp($mailchimp_data);
                    }
                }
                $this->session->set_flashdata('success', 'Account details has been updated successfully.');
            } else {
                $dataArr['created'] = date('Y-m-d H:i:s');
                $this->accounts_model->common_insert_update('insert', TBL_ACCOUNTS, $dataArr);

                //-- Insert account email into mailchimp subscriber list
                if (!empty($dataArr['email'])) {
                    $mailchimp_data = array(
                        'email_address' => $dataArr['email'],
                        'status' => 'subscribed', // "subscribed","unsubscribed","cleaned","pending"
                        'merge_fields' => [
                            'FNAME' => $dataArr['contact_name']
                        ],
                        'interests' => array(ACCOUNTS_GROUP_ID => true)
                    );
                    mailchimp($mailchimp_data);
                }
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
        //-- Check logged in user has access to edit account
        checkPrivileges('accounts', 'edit');
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
        $id = base64_decode($id);
        if (is_numeric($id)) {
            $account = $this->accounts_model->sql_select(TBL_ACCOUNTS, 'id,email', ['where' => ['id' => $id]], ['single' => true]);
            if ($account) {
                //-- Check if account is assigned to donor then don't allow to delete that account
//                $is_assigned = $this->accounts_model->sql_select(TBL_FUNDS, NULL, ['where' => ['account_id' => $id, 'is_delete' => 0, 'is_refund' => 0]]);
                $is_assigned = $this->accounts_model->allow_delete($id);
                if (!empty($is_assigned)) {
                    $this->session->set_flashdata('error', 'You can not delete account,It is assigned to donor');
                    redirect('accounts');
                }
                $update_array = array(
                    'is_delete' => 1
                );
                $this->accounts_model->common_insert_update('update', TBL_ACCOUNTS, $update_array, ['id' => $id]);

                //--Delete subscriber from account list
                if (!empty($account['email'])) {
                    $subscriber = get_mailchimp_subscriber($account['email']);
                    if (!empty($subscriber)) {
                        $interests = $subscriber['interests'];
                        if ($interests[DONORS_GROUP_ID] == 1 || $interests[GUESTS_GROUP_ID] == 1) {
                            $mailchimp_data = array(
                                'email_address' => $account['email'],
                                'interests' => array(ACCOUNTS_GROUP_ID => false)
                            );
                            mailchimp($mailchimp_data);
                        } else {
                            //-- Update old entry to unsubscribed and add new to subscribed
//                            $mailchimp_data = array(
//                                'email_address' => $account['email'],
//                                'status' => 'unsubscribed', // "subscribed","unsubscribed","cleaned","pending"
//                                'interests' => array(ACCOUNTS_GROUP_ID => false)
//                            );

                            $mailchimp_data = array(
                                'email_address' => $account['email'],
                            );
                            delete_mailchimp_subscriber($mailchimp_data);
                        }
                    }
                }
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
     * Ajax call to this function checks Unique Vedor at the time of account's add and edit
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
     * Ajax call to this function checks Unique AMC at the time of accounts add and edit
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

    /**
     * Ajax call to this function checks Unique Program at the time of accounts add and edit
     * */
    public function checkUniqueProgram($fund_type, $id = NULL) {
        $where = ['fund_type_id' => $fund_type, 'program_name' => trim($this->input->get('program_name'))];
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
     * Callback Validate function to check state is valid or not
     * @return boolean
     * @author KU
     */
    public function state_validation() {
        $state_code = $this->input->post('state_short');
        $state = $this->accounts_model->sql_select(TBL_STATES, 'id', ['where' => ['short_name' => $state_code]], ['single' => true]);
        if (empty($state)) {
            $this->form_validation->set_message('state_validation', 'State does not exist in the database! Please enter correct zipcode');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Get all accounts transactions
     */
    public function transactions($account_id = NULL) {
        checkPrivileges('accounts', 'view');
        $account_id = base64_decode($account_id);
        if (is_numeric($account_id)) {
            $account = $this->accounts_model->sql_select(TBL_ACCOUNTS, 'id,,IF(program_name = \'\',action_matters_campaign,program_name) as program_name,vendor_name', ['where' => ['id' => $account_id]], ['single' => true]);
            if (!empty($account)) {
                $data['account'] = $account;
                $data['title'] = 'Extracredit | Account Transactions';
                $data['transactions'] = $this->accounts_model->get_account_transactions($account_id);
                $this->template->load('default', 'accounts/transactions', $data);
            } else {
                $this->session->set_flashdata('error', 'Invalid request. Please try again!');
                redirect('accounts');
            }
        } else {
            show_404();
        }
    }

    /**
     * Listing of All Accounts communication
     * @author REP
     */
    public function communication($id = null) {
        checkPrivileges('accounts_communication', 'view');
        $data['perArr'] = checkPrivileges('accounts_communication');
        $data['title'] = 'Extracredit | Accounts Communication';
        $data['id'] = $id;
        $this->template->load('default', 'accounts/list_communication', $data);
    }

    /**
     * Get Accounts communication data for ajax table
     * @author REP
     * */
    public function get_accounts_communication($id) {
        checkPrivileges('accounts_communication', 'view');
        $id = base64_decode($id);
        $final['recordsFiltered'] = $final['recordsTotal'] = $this->accounts_model->get_accounts_communication('count', $id);
        $final['redraw'] = 1;
        $accounts = $this->accounts_model->get_accounts_communication('result', $id);
        $start = $this->input->get('start') + 1;

        foreach ($accounts as $key => $val) {
            $accounts[$key] = $val;
            $accounts[$key]['created'] = date('m/d/Y', strtotime($val['created']));
            $accounts[$key]['follow_up_date'] = ($val['follow_up_date'] != '') ? date('m/d/Y', strtotime($val['follow_up_date'])) : '';
            $accounts[$key]['communication_date'] = ($val['communication_date'] != '') ? date('m/d/Y', strtotime($val['communication_date'])) : '';
        }
        $final['data'] = $accounts;
        echo json_encode($final);
    }

    /**
     * Get Accounts communication data for ajax call for view
     * @author REP
     * */
    public function get_communication_by_id() {
        $id = $this->input->post('id');
        $id = base64_decode($id);
        $accounts_communication = $this->accounts_model->get_account_communication_details($id);
        $accounts_communication['follow_up_date'] = ($accounts_communication['follow_up_date'] != '') ? date('m/d/Y', strtotime($accounts_communication['follow_up_date'])) : '';
        $accounts_communication['communication_date'] = ($accounts_communication['communication_date'] != '') ? date('m/d/Y', strtotime($accounts_communication['communication_date'])) : '';
        echo json_encode($accounts_communication);
    }

    /**
     * Add Accounts communication data for ajax call for view
     * @author REP
     * */
    public function add_communication($account_id = null, $comm_id = null) {
        if (!is_null($account_id))
            $account_id = base64_decode($account_id);

        $data['account'] = $this->accounts_model->sql_select(TBL_ACCOUNTS, 'id,action_matters_campaign,vendor_name', ['where' => ['id' => $account_id]], ['single' => true]);
        $comm_id = base64_decode($comm_id);
        if (is_numeric($comm_id)) {
            checkPrivileges('accounts_communication', 'edit');
            $accounts_communication = $this->accounts_model->get_account_communication_details($comm_id);
            $data['account_communication'] = $accounts_communication;
            $data['title'] = 'Extracredit | Edit Communication';
            $data['heading'] = 'Edit Communication';
            if ($accounts_communication['media'] != '')
                $media = $accounts_communication['media'];
            else
                $media = NULL;
        } else {
            checkPrivileges('accounts_communication', 'add');
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
                    'communication_date' => ($this->input->post('communication_date') != '') ? date('Y-m-d', strtotime($this->input->post('communication_date'))) : NULL,
                    'follow_up_date' => ($this->input->post('follow_up_date') != '') ? date('Y-m-d', strtotime($this->input->post('follow_up_date'))) : NULL,
                    'subject' => $this->input->post('subject'),
                    'account_id' => $account_id,
                    'guest_id' => 0,
                    'donor_id' => 0,
                    'type' => 3,
                    'media' => $media
                );

                if (is_numeric($comm_id)) {
                    $dataArr['modified'] = date('Y-m-d H:i:s');
                    $this->accounts_model->common_insert_update('update', TBL_COMMUNICATIONS, $dataArr, ['id' => $comm_id]);
                    $this->session->set_flashdata('success', 'Account communication details has been updated successfully.');
                } else {
                    $dataArr['created'] = date('Y-m-d H:i:s');
                    $this->accounts_model->common_insert_update('insert', TBL_COMMUNICATIONS, $dataArr);
                    if (!empty($this->input->post('follow_up_date'))) {
                        $communication_ManagerArr = array(
                            'user_id' => $this->session->userdata('extracredit_user')['id'],
                            'communication_id' => $this->db->insert_id(),
                            'follow_up_date' => date('Y-m-d', strtotime($this->input->post('follow_up_date'))),
                            'category' => 'account',
                        );
                        $this->communication_manager_model->common_insert_update('insert', TBL_COMMUNICATIONS_MANAGER, $communication_ManagerArr);
                    }
                    $this->session->set_flashdata('success', 'Account communication has been added successfully');
                }
                redirect('accounts/communication/' . base64_encode($account_id));
            }
        }
        $this->template->load('default', 'accounts/add_communication', $data);
    }

    /**
     * Delete Guest Communication
     * @param int $id
     * @author REP
     * */
    public function delete_communication($account_id = null, $id = NULL) {
        checkPrivileges('accounts_communication', 'delete');
        $id = base64_decode($id);
        if (is_numeric($id)) {
            $accounts_communication = $this->accounts_model->get_account_communication_details($id);
            if ($accounts_communication) {
                $update_array = array(
                    'is_delete' => 1
                );
                $this->accounts_model->common_insert_update('update', TBL_COMMUNICATIONS, $update_array, ['id' => $id, 'type' => 3]);
                $this->session->set_flashdata('success', 'Account communication has been deleted successfully!');
            } else {
                $this->session->set_flashdata('error', 'Invalid request. Please try again!');
            }
            redirect('accounts/communication/' . $account_id);
        } else {
            show_404();
        }
    }

    /**
     * Ajax call to this function return Account fund 
     */
    public function get_account_fund() {
        $id = base64_decode($this->input->post('id'));
        $account_details = $this->accounts_model->get_account_fund($id);
        //-- If not vendor then return accounts fund else return admin fund
        if ($account_details['type'] == 0) {
            $data = ['amount' => $account_details['total_fund'], 'type' => 0];
        } else {
            $store_admin_fund = $this->accounts_model->sql_select(TBL_USERS, 'total_fund,id', ['where' => ['role' => 'admin']], ['single' => true]);
            $data = ['amount' => $store_admin_fund['total_fund'], 'type' => 1];
        }
        echo json_encode($data);
    }

    /**
     * Ajax call to this function return accounts of particular fund type id
     */
    public function get_accounts_transfer() {
        $id = base64_decode($this->input->post('id'));
        $account_id = base64_decode($this->input->post('account_id'));
        $accounts = $this->accounts_model->sql_select(TBL_ACCOUNTS, 'id,IF(program_name = \'\',action_matters_campaign,program_name) as program,vendor_name', ['where' => ['is_delete' => 0, 'fund_type_id' => $id, 'id!=' => $account_id]], ['order_by' => 'program']);
        echo json_encode($accounts);
    }

    /**
     * Add transfer account data
     * @param int $id
     * @author REP
     * */
    public function transfer_account($id = NULL) {
        $data['perArr'] = checkPrivileges('transfer_account');
        if (!is_null($id))
            $id = base64_decode($id);
        if (is_numeric($id)) {
            $account = $this->accounts_model->get_account_details($id);
//             p($account,1);
            checkPrivileges('transfer_account', 'add');
            $data['title'] = 'Extracredit | Transfer Money';
            $data['heading'] = 'Account Transfer';
            $data['account'] = $account;
            $data['accounts'] = [];
            $data['account_fund'] = $account['total_fund'];
            $data['fund_types'] = $this->accounts_model->sql_select(TBL_FUND_TYPES, 'id,name as type', ['where' => ['is_delete' => 0, 'type!=' => 2]], ['order_by' => 'name']);
        }

//        $this->form_validation->set_rules('account_id_from', 'Account Name', 'trim|required|numeric');
        $this->form_validation->set_rules('account_id_to', 'Account To Name', 'trim|required|numeric');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required|numeric');

        if ($this->form_validation->run() == TRUE) {
            $is_valid = 1;
            $account_id_from_fund = $this->accounts_model->sql_select(TBL_ACCOUNTS, 'total_fund', ['where' => ['is_delete' => 0, 'id=' => $this->input->post('hidden_account_id_from')]], 'row_array');
            $account_id_to_fund = $this->accounts_model->sql_select(TBL_ACCOUNTS, 'total_fund', ['where' => ['is_delete' => 0, 'id=' => $this->input->post('account_id_to')]], 'row_array');
            $amount = $this->input->post('amount');
            $dataArr = array(
                'amount' => $amount,
                'account_id_from' => $this->input->post('hidden_account_id_from'),
                'account_id_to' => $this->input->post('account_id_to'),
                'account1_fund' => $account_id_from_fund['total_fund'] - $amount,
                'account2_fund' => $account_id_to_fund['total_fund'] + $amount,
            );

            $this->db->trans_begin();
            $dataArr['created'] = date('Y-m-d H:i:s');
            if ($account_id_from_fund['total_fund'] >= $this->input->post('amount')) {
                $account_fund = $account_id_from_fund['total_fund'] - $amount;
                $account_fund_to = $account_id_to_fund['total_fund'] + $amount;
                $this->accounts_model->common_insert_update('update', TBL_ACCOUNTS, ['total_fund' => $account_fund], ['id' => $id]);
                $this->accounts_model->common_insert_update('update', TBL_ACCOUNTS, ['total_fund' => $account_fund_to], ['id' => $this->input->post('account_id_to')]);
            } else {
                $is_valid = 0;
                $this->session->set_flashdata('error', 'Fail to update payment! You have entered more amount than Account fund');
            }
            if ($is_valid == 1) {
                $this->accounts_model->common_insert_update('insert', TBL_ACCOUNTS_TRANSFER, $dataArr);
                $this->session->set_flashdata('success', 'Money has been transferred successfully');
            }
            $this->db->trans_complete();
            redirect('accounts');
        }
        $this->template->load('default', 'accounts/transfer_account', $data);
    }

    /**
     * Import account data from CSV file
     * @author KU
     */
    public function import_account() {

        checkPrivileges('account', 'add');
        $fileDirectory = ACCOUNT_CSV;
        $config['overwrite'] = FALSE;
        $config['remove_spaces'] = TRUE;
        $config['upload_path'] = ACCOUNT_CSV;
        $config['allowed_types'] = 'csv|CSV';
        $this->load->library('upload', $config);

        //-- Upload csv file
        if ($this->upload->do_upload('import_account')) {
            $fileDetails = $this->upload->data();

            //-- fund types array
            $fund_types = $this->accounts_model->sql_select(TBL_FUND_TYPES, 'name,id', ['where' => ['is_delete' => 0]]);
            $fund_types_arr = $cities_arr = $states_arr = $program_types_arr = $program_status_arr = [];
            foreach ($fund_types as $fund_type) {
                $fund_types_arr[$fund_type['id']] = $fund_type['name'];
            }

            //-- cities array
            $cities = $this->accounts_model->sql_select(TBL_CITIES);
            foreach ($cities as $city) {
                $cities_arr[$city['id']] = $city['name'];
                $states_arr[$city['id']] = $city['state_id'];
            }

            //-- account email array
            $account_emails = $this->accounts_model->sql_select(TBL_ACCOUNTS, 'email', ['where' => ['is_delete' => 0, 'email!=' => '']]);
            $account_emails_arr = array_column($account_emails, 'email');

            //-- program type array
            $program_types = $this->accounts_model->sql_select(TBL_PROGRAM_TYPES, 'id,type', ['where' => ['is_delete' => 0]]);
            foreach ($program_types as $program_type) {
                $program_types_arr[$program_type['id']] = $program_type['type'];
            }

            //-- program status array
            $program_status = $this->accounts_model->sql_select(TBL_PROGRAM_STATUS, 'id,status', ['where' => ['is_delete' => 0]]);
            foreach ($program_status as $status) {
                $program_status_arr[$status['id']] = $status['status'];
            }

            $fund_types_arr = array_map('strtolower', $fund_types_arr);
            $cities_arr = array_map('strtolower', $cities_arr);
            $account_emails_arr = array_map('strtolower', $account_emails_arr);
            $program_types_arr = array_map('strtolower', $program_types_arr);
            $program_status_arr = array_map('strtolower', $program_status_arr);

            $row = 1;
            $handle = fopen($fileDirectory . "/" . $fileDetails['file_name'], "r");

            $account_data = $check_fundtype = $check_email_valid = $check_email = $check_city = $check_program_type = $check_program_status = $imported_emails = [];
            if (($data2 = fgetcsv($handle)) !== FALSE) {
                $data_format2 = array('fundtype', 'program', 'contactname', 'email', 'address', 'zip', 'city', 'phone', 'tax', 'programtype', 'programstatus', 'website', 'active');

                //-- check if first colums is according to predefined row
                if ($data_format2 == $data2) {
                    while (($col_data = fgetcsv($handle)) !== FALSE) {
                        $account = [];
                        if (empty($col_data[0])) {
                            fclose($handle);
                            $this->session->set_flashdata('error', 'Fund type is missing in Row No. ' . $row);
                            redirect('accounts');
                        } elseif ($col_data[1] == '') {
                            fclose($handle);
                            $this->session->set_flashdata('error', 'AMC/Program name is missing in Row No. ' . $row);
                            redirect('accounts');
                        } elseif ($col_data[2] == '') {
                            fclose($handle);
                            $this->session->set_flashdata('error', 'Contact name is missing in Row No. ' . $row);
                            redirect('accounts');
                        } else {

                            //-- Check fund type is valid or not
                            if (array_search(strtolower($col_data[0]), $fund_types_arr) != FALSE) {
                                $account['fund_type_id'] = array_search(strtolower($col_data[0]), $fund_types_arr);
                            } else {
                                $check_fundtype[] = $row;
                            }
                            $account['program_name'] = $col_data[1];
                            $account['contact_name'] = $col_data[2];

                            //--check email is unique or not
                            if (!empty($col_data[3])) {
                                if (!filter_var($col_data[3], FILTER_VALIDATE_EMAIL)) {
                                    $check_email_valid[] = $row;
                                } else if (array_search(strtolower($col_data[3]), $account_emails_arr) != FALSE) {
                                    $check_email[] = $row;
                                } else {
                                    $account['email'] = $col_data[3];
                                }
                            } else {
                                $account['email'] = NULL;
                            }

                            $imported_emails[] = $col_data[3];
                            $account['address'] = $col_data[4];
                            $account['zip'] = $col_data[5];

                            //--check city is valid or not
                            if (!empty($col_data[6])) {
                                if (array_search(strtolower($col_data[6]), $cities_arr) != FALSE) {
                                    $account['city_id'] = array_search(strtolower($col_data[6]), $cities_arr);
                                    $account['state_id'] = $states_arr[$account['city_id']];
                                } else {
                                    $check_city[] = $row;
                                }
                            } else {
                                $account['city_id'] = NULL;
                                $account['state_id'] = NULL;
                            }

                            $account['phone'] = $col_data[7];
                            $account['tax_id'] = $col_data[8];

                            //-- Check if program type is valid or not
                            if (!empty($col_data[9])) {
                                if (array_search(strtolower($col_data[9]), $program_types_arr) != FALSE) {
                                    $account['program_type_id'] = array_search(strtolower($col_data[9]), $program_types_arr);
                                } else {
                                    $check_program_type[] = $row;
                                }
                            } else {
                                $account['program_type_id'] = NULL;
                            }

                            //-- Check if program status is valid or not
                            if (!empty($col_data[10])) {
                                if (array_search(strtolower($col_data[10]), $program_status_arr) != FALSE) {
                                    $account['program_status_id'] = array_search(strtolower($col_data[10]), $program_status_arr);
                                } else {
                                    $check_program_status[] = $row;
                                }
                            } else {
                                $account['program_status_id'] = NULL;
                            }

                            $account['website'] = $col_data[11];

                            if ($col_data[12] != '') {
                                if (strtolower($col_data[12]) == 'yes') {
                                    $account['is_active'] = 1;
                                } else {
                                    $account['is_active'] = 0;
                                }
                            } else {
                                $account['is_active'] = 0;
                            }

                            $account_data[] = $account;
                            $row++;
                        }
                    }
                    //- check entered fundtype is valid or not
                    if (!empty($check_fundtype)) {
                        $rows = implode(',', $check_fundtype);
                        $this->session->set_flashdata('error', "Fund type doesn't exist in the system. Please check entries at row number - " . $rows);
                    } else if (count(array_unique($imported_emails)) != count($imported_emails)) { //-- check emails in column are unique or not
                        fclose($handle);
                        $this->session->set_flashdata('error', "Duplicate value in email column.");
                    } else if (!empty($check_email_valid)) { //-- check Account/Program in columns are valid or not
                        $rows = implode(',', $check_email_valid);
                        $this->session->set_flashdata('error', "Account's Email is not in valid format. Please check entries at row number - " . $rows);
                    } else if (!empty($check_email)) { //-- check Account/Program in columns are valid or not
                        $rows = implode(',', $check_email);
                        $this->session->set_flashdata('error', "Account's Email already exist in the system. Please check entries at row number - " . $rows);
                    } else if (!empty($check_city)) { //-- check city in column are unique or not
                        $rows = implode(',', $check_city);
                        $this->session->set_flashdata('error', "City doesn't exist in the system. Please check entries at row number - " . $rows);
                    } else if (!empty($check_program_type)) {  //-- check dates in column are valid or not
                        $rows = implode(',', $check_program_type);
                        $this->session->set_flashdata('error', "Invalid program type in programtype column. Please check entries at row number - " . $rows);
                    } else if (!empty($check_program_status)) {   //-- check post dates in column are valid or not
                        $rows = implode(',', $check_program_status);
                        $this->session->set_flashdata('error', "Invalid post date in programstatus column. Please check entries at row number - " . $rows);
                    } else {
                        if (!empty($account_data)) {
                            //-- Insert account details into database
                            foreach ($account_data as $val) {

                                //-- If email is not empty subscribe email to accounts interest
                                if (!empty($val['email'])) {
                                    $mailchimp_data = array(
                                        'email_address' => $val['email'],
                                        'status' => 'subscribed', // "subscribed","unsubscribed","cleaned","pending"
                                        'merge_fields' => [
                                            'FNAME' => $val['contact_name']
                                        ],
                                        'interests' => array(ACCOUNTS_GROUP_ID => true)
                                    );
                                    mailchimp($mailchimp_data);
                                }


                                $account_arr = [
                                    'fund_type_id' => $val['fund_type_id'],
                                    'is_active' => $val['is_active'],
                                    'action_matters_campaign' => $val['action_matters_campaign'],
                                    'contact_name' => $val['contact_name'],
                                    'address' => $val['address'],
                                    'city_id' => $val['city_id'],
                                    'state_id' => $val['state_id'],
                                    'zip' => $val['zip'],
                                    'email' => $val['email'],
                                    'phone' => $val['phone'],
                                    'tax_id' => $val['tax_id'],
                                    'program_type_id' => $val['program_type_id'],
                                    'program_status_id' => $val['program_status_id'],
                                    'website' => trim($val['website']),
                                    'created' => date('Y-m-d H:i:s')
                                ];
                                $account_id = $this->accounts_model->common_insert_update('insert', TBL_ACCOUNTS, $account_arr);
                            }
                            $this->session->set_flashdata('success', "CSV file imported successfully! Account data added successfully");
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
            redirect('accounts');
        } else {
            $this->session->set_flashdata('error', strip_tags($this->upload->display_errors()));
            redirect('accounts');
        }
    }

}

/* End of file Accounts.php */
/* Location: ./application/controllers/Accounts.php */