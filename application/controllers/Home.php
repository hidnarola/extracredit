<?php

/**
 * Home Controller for Admin/Staff dashboard
 * @author KU 
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Display dashboard page
     */
    public function index() {
        $data['title'] = 'Extra Credit | Dashboard';
        $data['users'] = $this->users_model->sql_select(TBL_USERS, 'id', ['where' => ['role' => 'staff']], ['count' => true]);
        $data['donors'] = 0;
        $data['guests'] = 0;
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

}
