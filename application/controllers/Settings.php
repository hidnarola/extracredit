<?php

/**
 * Settings Controller - Manage settings
 * @author KU
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('settings_model');
    }

    /**
     * Donation split settings page
     */
    public function index() {
        $data['title'] = 'Extracredit | Settings';
        $settings = $this->settings_model->get_settings();
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
                $this->settings_model->batch_insert_update('update', TBL_SETTINGS, $update_arr, 'setting_key');
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
        $data['title'] = 'Extracredit | Fund Types';
        $data['fund_types'] = $this->settings_model->sql_select(TBL_FUND_TYPES, null, ['where' => ['is_delete' => 0]]);
        $this->form_validation->set_rules('fund_type', 'Fund Type', 'trim|required');
        if ($this->form_validation->run() == TRUE) {
            $id = $this->input->post('fund_type_id');
            //-- If id is not blank then update fund type else insert new fund type
            if ($id != '') {
                $result = $this->settings_model->sql_select(TBL_FUND_TYPES, NULL, ['where' => ['id' => $id, 'is_delete' => 0]], ['single' => true]);
                if (!empty($result)) {
                    $update_array = array(
                        'type' => trim($this->input->post('fund_type')),
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
        $fund_type = $this->settings_model->sql_select(TBL_FUND_TYPES, null, ['where' => ['is_delete' => 0, 'id' => $id]], ['single' => true]);
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
        $result = $this->settings_model->sql_select(TBL_FUND_TYPES, NULL, ['where' => $where], ['single' => true]);
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
        $id = base64_decode($id);
        if (is_numeric($id)) {
            $result = $this->settings_model->sql_select(TBL_FUND_TYPES, NULL, ['where' => ['id' => $id, 'is_delete' => 0]], ['single' => true]);
            if (!empty($result)) {
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
        $data['title'] = 'Extracredit | Payment Types';
        $data['payment_types'] = $this->settings_model->sql_select(TBL_PAYMENT_TYPES, null, ['where' => ['is_delete' => 0]]);
        $this->form_validation->set_rules('payment_type', 'Payment Type', 'trim|required');
        if ($this->form_validation->run() == TRUE) {
            $id = $this->input->post('payment_type_id');
            //-- If id is not blank then update payment type else insert new payment type
            if ($id != '') {
                $result = $this->settings_model->sql_select(TBL_PAYMENT_TYPES, NULL, ['where' => ['id' => $id, 'is_delete' => 0]], ['single' => true]);
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
        $id = base64_decode($id);
        if (is_numeric($id)) {
            $result = $this->settings_model->sql_select(TBL_PAYMENT_TYPES, NULL, ['where' => ['id' => $id, 'is_delete' => 0]], ['single' => true]);
            if (!empty($result)) {
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
        $result = $this->settings_model->sql_select(TBL_PAYMENT_TYPES, NULL, ['where' => $where], ['single' => true]);
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
        $payment_type = $this->settings_model->sql_select(TBL_PAYMENT_TYPES, null, ['where' => ['is_delete' => 0, 'id' => $id]], ['single' => true]);
        echo json_encode($payment_type);
    }

}

/* End of file Settings.php */
/* Location: ./application/controllers/Settings.php */