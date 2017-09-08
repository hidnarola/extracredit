<?php

/**
 * Settings Controller - Manage settings
 * @author KU
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Donation split settings page
     */
    public function index() {
        checkPrivileges('donation_split_settings', 'view');
        $data['perArr'] = checkPrivileges('donation_split_settings');
        $data['title'] = 'Extracredit | Settings';
        $settings = $this->users_model->sql_select(TBL_SETTINGS);
        $settings_arr = [];
        foreach ($settings as $val) {
            $settings_arr[$val['setting_key']] = $val['setting_value'];
        }
        $data['settings'] = $settings_arr;
        $this->form_validation->set_rules('admin-donation-percent', 'Admin Donation(Percentage)', 'trim|required|greater_than_equal_to[0]|less_than_equal_to[100]');
        $this->form_validation->set_rules('program-donation-percent', 'Program/campaign Donation(Percentage)', 'trim|required|greater_than_equal_to[0]|less_than_equal_to[100]');
        if ($this->form_validation->run() == TRUE) {
            $admin_donatoin = $this->input->post('admin-donation-percent');
            $program_donatoin = $this->input->post('program-donation-percent');
            $total = $admin_donatoin + $program_donatoin;
            if ($total == 100) {
                $update_arr = [
                    ['setting_key' => 'admin-donation-percent', 'setting_value' => $this->input->post('admin-donation-percent')],
                    ['setting_key' => 'program-donation-percent', 'setting_value' => $this->input->post('program-donation-percent')]
                ];
                $this->users_model->batch_insert_update('update', TBL_SETTINGS, $update_arr, 'setting_key');
                $this->session->set_flashdata('success', 'Settings updated successfully');
            } else {
                $this->session->set_flashdata('error', 'You have entered invalid data. Please try again later');
            }
            redirect('settings');
        }
        $this->template->load('default', 'settings/index', $data);
    }

    /**
     * List all fund types added
     */
    public function fund_types() {
        checkPrivileges('fund_types', 'view');
        $data['perArr'] = checkPrivileges('fund_types');
        $data['title'] = 'Extracredit | Fund Types';
        $data['fund_types'] = $this->users_model->sql_select(TBL_FUND_TYPES, null, ['where' => ['is_delete' => 0]]);
        $this->form_validation->set_rules('fund_type', 'Fund Type', 'trim|required');
        if ($this->form_validation->run() == TRUE) {
            $id = $this->input->post('fund_type_id');
            //-- If id is not blank then update fund type else insert new fund type
            if ($id != '') {
                $result = $this->users_model->sql_select(TBL_FUND_TYPES, NULL, ['where' => ['id' => $id, 'is_delete' => 0]], ['single' => true]);
                if (!empty($result)) {
                    $update_array = array(
                        'type' => trim($this->input->post('fund_type')),
                        'is_vendor' => ($this->input->post('is_vendor')) ? 1 : 0,
                        'modified' => date('Y-m-d H:i:s')
                    );
                    $this->users_model->common_insert_update('update', TBL_FUND_TYPES, $update_array, ['id' => $id]);
                    $this->session->set_flashdata('success', 'Fund type updated successfully');
                } else {
                    $this->session->set_flashdata('error', 'Invalid request! Please try again later');
                }
            } else {
                $update_array = array(
                    'type' => trim($this->input->post('fund_type')),
                    'is_vendor' => ($this->input->post('is_vendor')) ? 1 : 0,
                    'created' => date('Y-m-d H:i:s')
                );
                $this->users_model->common_insert_update('insert', TBL_FUND_TYPES, $update_array);
                $this->session->set_flashdata('success', trim($this->input->post('fund_type')) . ' Fund type inserted successfully');
            }
            redirect('settings/fund_types');
        }
        $this->template->load('default', 'settings/fund_types', $data);
    }

    /**
     * Get fund type by id
     */
    public function get_fund_type_by_id() {
        $id = base64_decode($this->input->post('id'));
        $fund_type = $this->users_model->sql_select(TBL_FUND_TYPES, null, ['where' => ['is_delete' => 0, 'id' => $id]], ['single' => true]);
        echo json_encode($fund_type);
    }

    /**
     * Check fund type exist or not
     * @param int $id If Id is passed then do not consider that id to check fund type
     */
    public function check_fund_type($id = NULL) {
        $fund_type = trim($this->input->get('fund_type'));
        $where = ['is_delete' => 0, 'type' => $fund_type];
        if (!is_null($id)) {
            $where = array_merge($where, ['id!=' => $id]);
        }
        $result = $this->users_model->sql_select(TBL_FUND_TYPES, NULL, ['where' => $where], ['single' => true]);
        if (!empty($result)) {
            echo "false";
        } else {
            echo "true";
        }
        exit;
    }

    /**
     * Delete fund type
     * @param int $id
     */
    public function delete_fundtype($id = NULL) {
        checkPrivileges('fund_types', 'delete');
        $data['perArr'] = checkPrivileges('fund_types');
        $id = base64_decode($id);
        if (is_numeric($id)) {
            $result = $this->users_model->sql_select(TBL_FUND_TYPES, NULL, ['where' => ['id' => $id, 'is_delete' => 0]], ['single' => true]);
            if (!empty($result)) {
                //-- check if it is assigned to any account or not
                $is_assigned = $this->users_model->sql_select(TBL_ACCOUNTS, NULL, ['where' => ['fund_type_id' => $id, 'is_delete' => 0]], ['single' => true]);
                if (!empty($is_assigned)) {
                    $this->session->set_flashdata('error', 'You can not delete ' . $result['type'] . ' fund type,It is assigned to account');
                    redirect('settings/fund_types');
                }
                $update_array = array(
                    'is_delete' => 1
                );
                $this->users_model->common_insert_update('update', TBL_FUND_TYPES, $update_array, ['id' => $id]);
                $this->session->set_flashdata('success', $result['type'] . ' deleted successfully');
            } else {
                $this->session->set_flashdata('error', 'Invalid request! Please try again later');
            }
            redirect('settings/fund_types');
        } else {
            show_404();
        }
    }

    /**
     * List all payment types added and add/update payment type
     */
    public function payment_types() {
        checkPrivileges('payment_types', 'view');
        $data['perArr'] = checkPrivileges('payment_types');
        $data['title'] = 'Extracredit | Payment Types';
        $data['payment_types'] = $this->users_model->sql_select(TBL_PAYMENT_TYPES, null, ['where' => ['is_delete' => 0]]);
        $this->form_validation->set_rules('payment_type', 'Payment Type', 'trim|required');
        if ($this->form_validation->run() == TRUE) {
            $id = $this->input->post('payment_type_id');
            //-- If id is not blank then update payment type else insert new payment type
            if ($id != '') {
                $result = $this->users_model->sql_select(TBL_PAYMENT_TYPES, NULL, ['where' => ['id' => $id, 'is_delete' => 0]], ['single' => true]);
                if (!empty($result)) {
                    $update_array = array(
                        'type' => trim($this->input->post('payment_type')),
                        'modified' => date('Y-m-d H:i:s')
                    );
                    $this->users_model->common_insert_update('update', TBL_PAYMENT_TYPES, $update_array, ['id' => $id]);
                    $this->session->set_flashdata('success', 'Payment type updated successfully');
                } else {
                    $this->session->set_flashdata('error', 'Invalid request! Please try again later');
                }
            } else {
                $update_array = array(
                    'type' => trim($this->input->post('payment_type')),
                    'created' => date('Y-m-d H:i:s')
                );
                $this->users_model->common_insert_update('insert', TBL_PAYMENT_TYPES, $update_array);
                $this->session->set_flashdata('success', trim($this->input->post('payment_type')) . ' Payment Type inserted successfully');
            }
            redirect('settings/payment_types');
        }
        $this->template->load('default', 'settings/payment_types', $data);
    }

    /**
     * Delete payment type
     * @param int $id
     */
    public function delete_paymenttype($id = NULL) {
        checkPrivileges('payment_types', 'delete');
        $data['perArr'] = checkPrivileges('payment_types');
        $id = base64_decode($id);
        if (is_numeric($id)) {
            $result = $this->users_model->sql_select(TBL_PAYMENT_TYPES, NULL, ['where' => ['id' => $id, 'is_delete' => 0]], ['single' => true]);
            if (!empty($result)) {
                //-- check if it is assigned to any donor or not
                $is_assigned = $this->users_model->sql_select(TBL_DONORS, NULL, ['where' => ['payment_type_id' => $id, 'is_delete' => 0]], ['single' => true]);
                if (!empty($is_assigned)) {
                    $this->session->set_flashdata('error', 'You can not delete ' . $result['type'] . ' payment type,It is assigned to donors');
                    redirect('settings/payment_types');
                }

                $update_array = array(
                    'is_delete' => 1
                );
                $this->users_model->common_insert_update('update', TBL_PAYMENT_TYPES, $update_array, ['id' => $id]);
                $this->session->set_flashdata('success', $result['type'] . ' deleted successfully');
            } else {
                $this->session->set_flashdata('error', 'Invalid request! Please try again later');
            }
            redirect('settings/payment_types');
        } else {
            show_404();
        }
    }

    /**
     * Check payment type exist or not
     * @param int $id If Id is passed then do not consider that id to check payment type
     */
    public function check_payment_type($id = NULL) {
        $payment_type = trim($this->input->get('payment_type'));
        $where = ['is_delete' => 0, 'type' => $payment_type];
        if (!is_null($id)) {
            $where = array_merge($where, ['id!=' => $id]);
        }
        $result = $this->users_model->sql_select(TBL_PAYMENT_TYPES, NULL, ['where' => $where], ['single' => true]);
        if (!empty($result)) {
            echo "false";
        } else {
            echo "true";
        }
        exit;
    }

    /**
     * Get payment type details by id
     */
    public function get_payment_type_by_id() {
        $id = base64_decode($this->input->post('id'));
        $payment_type = $this->users_model->sql_select(TBL_PAYMENT_TYPES, null, ['where' => ['is_delete' => 0, 'id' => $id]], ['single' => true]);
        echo json_encode($payment_type);
    }

    /**
     * List all program/AMC types added and add/update program type
     */
    public function program_types() {
        checkPrivileges('program_types', 'view');
        $data['perArr'] = checkPrivileges('program_types');
        $data['title'] = 'Extracredit | Prgram Types';
        $data['program_types'] = $this->users_model->sql_select(TBL_PROGRAM_TYPES, null, ['where' => ['is_delete' => 0]]);
        $this->form_validation->set_rules('program_type', 'Program Type', 'trim|required');
        if ($this->form_validation->run() == TRUE) {
            $id = $this->input->post('program_type_id');
            //-- If id is not blank then update program type else insert new program type
            if ($id != '') {
                $result = $this->users_model->sql_select(TBL_PROGRAM_TYPES, NULL, ['where' => ['id' => $id, 'is_delete' => 0]], ['single' => true]);
                if (!empty($result)) {
                    $update_array = array(
                        'type' => trim($this->input->post('program_type')),
                        'modified' => date('Y-m-d H:i:s')
                    );
                    $this->users_model->common_insert_update('update', TBL_PROGRAM_TYPES, $update_array, ['id' => $id]);
                    $this->session->set_flashdata('success', 'Program type updated successfully');
                } else {
                    $this->session->set_flashdata('error', 'Invalid request! Please try again later');
                }
            } else {
                $update_array = array(
                    'type' => trim($this->input->post('program_type')),
                    'created' => date('Y-m-d H:i:s')
                );
                $this->users_model->common_insert_update('insert', TBL_PROGRAM_TYPES, $update_array);
                $this->session->set_flashdata('success', trim($this->input->post('payment_type')) . ' Program Type inserted successfully');
            }
            redirect('settings/program_types');
        }
        $this->template->load('default', 'settings/program_types', $data);
    }

    /**
     * Delete program type
     * @param int $id
     */
    public function delete_programtype($id = NULL) {
        checkPrivileges('program_types', 'delete');
        $data['perArr'] = checkPrivileges('program_types');
        $id = base64_decode($id);
        if (is_numeric($id)) {
            $result = $this->users_model->sql_select(TBL_PROGRAM_TYPES, NULL, ['where' => ['id' => $id, 'is_delete' => 0]], ['single' => true]);
            if (!empty($result)) {
                //-- check if it is assigned to any account or not
                $is_assigned = $this->users_model->sql_select(TBL_ACCOUNTS, NULL, ['where' => ['program_type_id' => $id, 'is_delete' => 0]], ['single' => true]);
                if (!empty($is_assigned)) {
                    $this->session->set_flashdata('error', 'You can not delete ' . $result['type'] . ' program type,It is assigned to account');
                    redirect('settings/program_types');
                }
                $update_array = array(
                    'is_delete' => 1
                );
                $this->users_model->common_insert_update('update', TBL_PROGRAM_TYPES, $update_array, ['id' => $id]);
                $this->session->set_flashdata('success', $result['type'] . ' deleted successfully');
            } else {
                $this->session->set_flashdata('error', 'Invalid request! Please try again later');
            }
            redirect('settings/program_types');
        } else {
            show_404();
        }
    }

    /**
     * Check program type exist or not
     * @param int $id If Id is passed then do not consider that id to check program type
     */
    public function check_program_type($id = NULL) {
        $program_type = trim($this->input->get('program_type'));
        $where = ['is_delete' => 0, 'type' => $program_type];
        if (!is_null($id)) {
            $where = array_merge($where, ['id!=' => $id]);
        }
        $result = $this->users_model->sql_select(TBL_PROGRAM_TYPES, NULL, ['where' => $where], ['single' => true]);
        if (!empty($result)) {
            echo "false";
        } else {
            echo "true";
        }
        exit;
    }

    /**
     * Get program type details by id
     */
    public function get_program_type_by_id() {
        $id = base64_decode($this->input->post('id'));
        $program_type = $this->users_model->sql_select(TBL_PROGRAM_TYPES, null, ['where' => ['is_delete' => 0, 'id' => $id]], ['single' => true]);
        echo json_encode($program_type);
    }

    /**
     * List all program/AMC satus added and add/update program status
     */
    public function program_status() {
        checkPrivileges('program_status', 'view');
        $data['perArr'] = checkPrivileges('program_status');
        $data['title'] = 'Extracredit | Prgram Status';
        $data['program_status'] = $this->users_model->sql_select(TBL_PROGRAM_STATUS, null, ['where' => ['is_delete' => 0]]);
        $this->form_validation->set_rules('program_status', 'Program Status', 'trim|required');
        if ($this->form_validation->run() == TRUE) {
            $id = $this->input->post('program_status_id');
            //-- If id is not blank then update program type else insert new program type
            if ($id != '') {
                $result = $this->users_model->sql_select(TBL_PROGRAM_STATUS, NULL, ['where' => ['id' => $id, 'is_delete' => 0]], ['single' => true]);
                if (!empty($result)) {
                    $update_array = array(
                        'status' => trim($this->input->post('program_status')),
                        'modified' => date('Y-m-d H:i:s')
                    );
                    $this->users_model->common_insert_update('update', TBL_PROGRAM_STATUS, $update_array, ['id' => $id]);
                    $this->session->set_flashdata('success', 'Program status updated successfully');
                } else {
                    $this->session->set_flashdata('error', 'Invalid request! Please try again later');
                }
            } else {
                $update_array = array(
                    'status' => trim($this->input->post('program_status')),
                    'created' => date('Y-m-d H:i:s')
                );
                $this->users_model->common_insert_update('insert', TBL_PROGRAM_STATUS, $update_array);
                $this->session->set_flashdata('success', trim($this->input->post('program_status')) . ' Program Status inserted successfully');
            }
            redirect('settings/program_status');
        }
        $this->template->load('default', 'settings/program_status', $data);
    }

    /**
     * Delete program status
     * @param int $id
     */
    public function delete_programstatus($id = NULL) {
        checkPrivileges('program_status', 'delete');
        $data['perArr'] = checkPrivileges('program_status');
        $id = base64_decode($id);
        if (is_numeric($id)) {
            $result = $this->users_model->sql_select(TBL_PROGRAM_STATUS, NULL, ['where' => ['id' => $id, 'is_delete' => 0]], ['single' => true]);
            if (!empty($result)) {
                //-- check if it is assigned to any account or not
                $is_assigned = $this->users_model->sql_select(TBL_ACCOUNTS, NULL, ['where' => ['program_status_id' => $id, 'is_delete' => 0]], ['single' => true]);
                if (!empty($is_assigned)) {
                    $this->session->set_flashdata('error', 'You can not delete ' . $result['status'] . ' program status,It is assigned to account');
                    redirect('settings/program_status');
                }
                $update_array = array(
                    'is_delete' => 1
                );
                $this->users_model->common_insert_update('update', TBL_PROGRAM_STATUS, $update_array, ['id' => $id]);
                $this->session->set_flashdata('success', $result['status'] . ' deleted successfully');
            } else {
                $this->session->set_flashdata('error', 'Invalid request! Please try again later');
            }
            redirect('settings/program_status');
        } else {
            show_404();
        }
    }

    /**
     * Check program status exist or not
     * @param int $id If Id is passed then do not consider that id to check program status
     */
    public function check_program_status($id = NULL) {
        $program_status = trim($this->input->get('program_status'));
        $where = ['is_delete' => 0, 'status' => $program_status];
        if (!is_null($id)) {
            $where = array_merge($where, ['id!=' => $id]);
        }
        $result = $this->users_model->sql_select(TBL_PROGRAM_STATUS, NULL, ['where' => $where], ['single' => true]);
        if (!empty($result)) {
            echo "false";
        } else {
            echo "true";
        }
        exit;
    }

    /**
     * Get program status details by id
     */
    public function get_program_status_by_id() {
        $id = base64_decode($this->input->post('id'));
        $program_status = $this->users_model->sql_select(TBL_PROGRAM_STATUS, null, ['where' => ['is_delete' => 0, 'id' => $id]], ['single' => true]);
        echo json_encode($program_status);
    }

}

/* End of file Settings.php */
/* Location: ./application/controllers/Settings.php */