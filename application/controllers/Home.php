<?php

/**
 * Home Controller for Admin/Staff dashboard
 * @author KU 
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('funds_model');
    }

    /**
     * Display dashboard page
     */
    public function index() {
        $data['title'] = 'Extra Credit | Dashboard';
        $data['users'] = $this->users_model->sql_select(TBL_USERS, 'id', ['where' => ['role' => 'staff', 'is_delete' => 0]], ['count' => true]);
        $data['accounts'] = $this->users_model->sql_select(TBL_ACCOUNTS, 'id', ['where' => ['is_delete' => 0]], ['count' => true]);
        $data['donors'] = $this->users_model->sql_select(TBL_DONORS, 'id', ['where' => ['is_delete' => 0]], ['count' => true]);
        $data['guests'] = $this->users_model->sql_select(TBL_GUESTS, 'id', ['where' => ['is_delete' => 0]], ['count' => true]);

        //-- Get todays admin fund
        $sql = 'SELECT sum(admin_fund)-(SELECT IF(sum(p.amount) IS NULL,0,sum(p.amount)) FROM ' . TBL_PAYMENTS . ' p LEFT JOIN ' . TBL_ACCOUNTS . ' a ON p.account_id=a.id AND a.is_delete=0 LEFT JOIN ' . TBL_FUND_TYPES . ' f ON a.fund_type_id=f.id AND f.is_delete=0 WHERE p.is_delete=0 AND f.type=1 AND p.created >= "' . date('Y-m-d') . '") as final FROM ' . TBL_FUNDS . ' WHERE ' . TBL_FUNDS . '.is_delete =0 AND ' . TBL_FUNDS . '.created >= "' . date('Y-m-d') . '"';
        $total = $this->users_model->customQuery($sql, 2);
        $data['today_admin_fund'] = $total['final'];

        //-- Get todays account fund
        $sql = 'SELECT sum(account_fund)-(SELECT IF(sum(p.amount) IS NULL,0,sum(p.amount)) FROM ' . TBL_PAYMENTS . ' p LEFT JOIN ' . TBL_ACCOUNTS . ' a ON p.account_id=a.id AND a.is_delete=0 LEFT JOIN ' . TBL_FUND_TYPES . ' f ON a.fund_type_id=f.id AND f.is_delete=0 WHERE p.is_delete=0 AND f.type=0 AND p.created >= "' . date('Y-m-d') . '") as final FROM ' . TBL_FUNDS . ' WHERE ' . TBL_FUNDS . '.is_delete =0 AND ' . TBL_FUNDS . '.created >= "' . date('Y-m-d') . '"';
        $total = $this->users_model->customQuery($sql, 2);
        $data['today_account_fund'] = $total['final'];

        $monday = strtotime("last monday");
        $monday = date('w', $monday) == date('w') ? $monday + 7 * 86400 : $monday;
        $sunday = strtotime(date("Y-m-d", $monday) . " +6 days");
        $this_week_sd = date("Y-m-d", $monday);
        $this_week_ed = date("Y-m-d", $sunday);

        //-- Get this week admin fund
        $sql = 'SELECT sum(admin_fund)-(SELECT IF(sum(p.amount) IS NULL,0,sum(p.amount)) FROM ' . TBL_PAYMENTS . ' p LEFT JOIN ' . TBL_ACCOUNTS . ' a ON p.account_id=a.id AND a.is_delete=0 LEFT JOIN ' . TBL_FUND_TYPES . ' f ON a.fund_type_id=f.id AND f.is_delete=0 WHERE p.is_delete=0 AND f.type=1 AND p.created >= "' . $this_week_sd . '" AND p.created <= "' . $this_week_ed . '") as final FROM ' . TBL_FUNDS . ' WHERE ' . TBL_FUNDS . '.is_delete =0 AND ' . TBL_FUNDS . '.created >= "' . $this_week_sd . '"  AND ' . TBL_FUNDS . '.created <= "' . $this_week_ed . '"';
        $total = $this->users_model->customQuery($sql, 2);
        $data['week_admin_fund'] = $total['final'];

        //-- Get this week account fund
        $sql = 'SELECT sum(account_fund)-(SELECT IF(sum(p.amount) IS NULL,0,sum(p.amount)) FROM ' . TBL_PAYMENTS . ' p LEFT JOIN ' . TBL_ACCOUNTS . ' a ON p.account_id=a.id AND a.is_delete=0 LEFT JOIN ' . TBL_FUND_TYPES . ' f ON a.fund_type_id=f.id AND f.is_delete=0 WHERE p.is_delete=0 AND f.type=0 AND p.created >= "' . $this_week_sd . '" AND p.created <= "' . $this_week_ed . '") as final FROM ' . TBL_FUNDS . ' WHERE ' . TBL_FUNDS . '.is_delete =0 AND ' . TBL_FUNDS . '.created >= "' . $this_week_sd . '"  AND ' . TBL_FUNDS . '.created <= "' . $this_week_ed . '"';
        $total = $this->users_model->customQuery($sql, 2);
        $data['week_account_fund'] = $total['final'];

        //-- Get total account fund
        $sql = 'SELECT sum(account_fund)-(SELECT IF(sum(p.amount) IS NULL,0,sum(p.amount)) FROM ' . TBL_PAYMENTS . ' p LEFT JOIN ' . TBL_ACCOUNTS . ' a ON p.account_id=a.id AND a.is_delete=0 LEFT JOIN ' . TBL_FUND_TYPES . ' f ON a.fund_type_id=f.id AND f.is_delete=0 WHERE p.is_delete=0 AND f.type=0) as final FROM ' . TBL_FUNDS . ' WHERE ' . TBL_FUNDS . '.is_delete =0 AND ' . TBL_FUNDS . '.is_refund =0';
        $total = $this->users_model->customQuery($sql, 2);
        $data['total_account_fund'] = $total['final'];

        $sql = 'SELECT count(p.id) as total FROM ' . TBL_PAYMENTS . ' p LEFT JOIN ' . TBL_ACCOUNTS . ' a ON p.account_id=a.id WHERE p.is_delete=0 AND a.is_delete=0';
        $total = $this->users_model->customQuery($sql, 2);
        $data['payments'] = $total['total'];

        //-- Chart data
        //-- Returns the number of free images purchased
        $date = $this->input->get('date');
        $date_array = array();
        $date_string = '';
        $event_arr = array();
        //-- By default take current months start and ending date
        $start_date = date('Y-m-01'); // hard-coded '01' for first day
        $end_date = date('Y-m-t');

        if ($date != '') {
            $dates = explode('-', $date);
            $start_date = $dates[0];
            $end_date = $dates[1];
        }
        $date_array = array('created >= ' => date('Y-m-d', strtotime($start_date)), 'created <= ' => date('Y-m-d', strtotime($end_date)));
        $date_string = ' AND created >= "' . date('Y-m-d', strtotime($start_date)) . '" AND created <= "' . date('Y-m-d', strtotime($end_date)) . '"';
        $event_arr['from_date'] = date('Y-m-d', strtotime($start_date));
        $event_arr['to_date'] = date('Y-m-d', strtotime($end_date));
        $data['json'] = json_encode("");

        //-- Json data for chart
        $json_data = array(
            'donors' => $this->users_model->num_of_records_by_date(TBL_DONORS, array_merge($date_array, array('is_delete' => 0, 'refund' => 0))),
            'incoming_money' => $this->funds_model->get_incoming_money(array_merge($date_array, array('is_delete' => 0, 'is_refund' => 0))),
        );

        $new_json_data = array();
        $key_arrays = array();

        foreach ($json_data as $key => $val) {
            $new_array = array();
            foreach ($val as $val1) {
                $new_array[$val1['date']] = $val1['count'];
                $key_arrays[] = array($val1['date'], date('jS M \'y', strtotime($val1['date'])));
            }
            $new_json_data[$key] = $new_array;
        }

        $key_arrays = array_unique($key_arrays, SORT_REGULAR);
        usort($key_arrays, array($this, 'sortFunction'));

        $actions = [];
        foreach ($new_json_data as $k => $data_value) {
            $actions[$k] = array();
            foreach ($key_arrays as $key => $value) {
                if (isset($data_value[$value[0]])) {
                    $actions[$k][$value[0]] = array(
                        $data_value[$value[0]], $value[1]
                    );
                }
            }
        }

        $actions['key_array'] = $key_arrays;
        $data['json'] = json_encode($actions);
        $this->template->load('default', 'dashboard', $data);
    }

    /**
     * Updates user profile
     */
    public function profile() {
        $data['title'] = 'Extra Credit | Profile';
        $this->form_validation->set_rules('firstname', 'First Name', 'trim|required');
        if ($this->form_validation->run() == TRUE) {
            $flag = 0;
            $profile_image = $this->session->userdata('extracredit_user')['profile_image'];
            if ($_FILES['profile_image']['name'] != '') {
                $image_data = upload_image('profile_image', USER_IMAGES);
                if (is_array($image_data)) {
                    $flag = 1;
                    $data['profile_image_validation'] = $image_data['errors'];
                } else {
                    if ($profile_image != '') {
                        unlink(USER_IMAGES . $profile_image);
                    }
                    $profile_image = $image_data;
                }
            }
            if ($flag != 1) {

                //--Unlink the previosly uploaded image if new image is uploaded
                $update_array = array(
                    'firstname' => trim($this->input->post('firstname')),
                    'lastname' => trim($this->input->post('lastname')),
                    'profile_image' => $profile_image,
                    'modified' => date('Y-m-d H:i:s')
                );
                $this->users_model->common_insert_update('update', TBL_USERS, $update_array, ['id' => $this->session->userdata('extracredit_user')['id']]);
                $this->session->set_flashdata('success', 'Profile updated successfully!');
                $result = $this->users_model->get_user_detail(['email' => $this->session->userdata('extracredit_user')['email']]);
                $this->session->set_userdata('extracredit_user', $result);
                redirect('home/profile');
            }
        }
        $this->template->load('default', 'profile', $data);
    }

    /**
     * Updates passowrd for entered user
     */
    public function update_password() {
        $this->form_validation->set_rules('old_password', 'Password', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        $this->form_validation->set_rules('confirm_password', 'Confirm password', 'trim|required|matches[password]');

        if ($this->form_validation->run() == FALSE) {
            $error = validation_errors();
            $this->session->set_flashdata('error', $error);
        } else {
            $result = $this->users_model->get_user_detail(['email' => $this->session->userdata('extracredit_user')['email']]);
            if (!password_verify($this->input->post('old_password'), $result['password'])) {
                $this->session->set_flashdata('error', 'You have entered wrong old password! Please try again.');
            } else {
                $id = $result['id'];
                $data = array(
                    'password' => password_hash($this->input->post('password'), PASSWORD_BCRYPT),
                );
                $this->users_model->common_insert_update('update', TBL_USERS, $data, ['id' => $id]);
                $this->session->set_flashdata('success', 'Your password changed successfully');
            }
        }
        redirect('home/profile');
    }

    /**
     * Specifies sort for date array
     * @param string $a
     * @param string $b
     * @return type
     */
    function sortFunction($a, $b) {
        return strtotime($a[0]) - strtotime($b[0]);
    }

}
