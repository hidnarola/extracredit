<?php

/**
 * Users Controller - Manage Users
 * @author KU
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller {

    public function __construct() {
        parent::__construct();
        if ($this->session->userdata('extracredit_user')['role'] == 'staff') {
            $this->session->set_flashdata('error', 'You are not authorized to access this page');
            redirect('home');
        }
    }

    public function index() {
        $data['title'] = 'Extracredit | Users';
        $this->template->load('default', 'users/list_users', $data);
    }

    /**
     * This function used to get users data for ajax table
     * */
    public function get_users() {
        $final['recordsFiltered'] = $final['recordsTotal'] = $this->users_model->get_users('count');
        $final['redraw'] = 1;
        $users = $this->users_model->get_users('result');
        $start = $this->input->get('start') + 1;

        foreach ($users as $key => $val) {
            $users[$key] = $val;
            $users[$key]['sr_no'] = $start++;
            $users[$key]['created'] = date('d,M Y', strtotime($val['created']));
        }

        $final['data'] = $users;
        echo json_encode($final);
    }

    /**
     * This function used to add / edit user(staff) data 
     * @param int $id
     * */
    public function add($id = NULL) {
        if (!is_null($id))
            $id = base64_decode($id);
        if (is_numeric($id)) {
            $user = $this->users_model->get_user_detail(['id' => $id, 'is_delete' => 0, 'is_active' => 1]);
            if ($user) {
                $data['user'] = $user;
                $data['title'] = 'Extracredit | Edit User';
                $data['heading'] = 'Edit User';
                $this->form_validation->set_rules('firstname', 'Firstname', 'trim|required');
                $profile_image = $user['profile_image'];
            } else {
                show_404();
            }
        } else {
            $this->form_validation->set_rules('firstname', 'Firstname', 'trim|required');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|callback_is_uniquemail');
            $profile_image = NULL;
            $data['title'] = 'Extracredit | Add User';
            $data['heading'] = 'Add User';
        }

        if ($this->form_validation->run() == TRUE) {
            $flag = 0;
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
            $dataArr = array(
                'role' => 'staff',
                'firstname' => trim($this->input->post('firstname')),
                'lastname' => trim($this->input->post('lastname')),
                'profile_image' => $profile_image,
                'is_active' => 1,
            );
            if (is_numeric($id)) {
                $dataArr['modified'] = date('Y-m-d H:i:s');
                $this->users_model->common_insert_update('update', TBL_USERS, $dataArr, ['id' => $id]);
                $this->session->set_flashdata('success', 'User\'s data has been updated successfully.');
            } else {
                $password = randomPassword();
                $dataArr['email'] = trim($this->input->post('email'));
                $dataArr['password'] = password_hash($password, PASSWORD_BCRYPT);
                $dataArr['created'] = date('Y-m-d H:i:s');
                $email_data = ['firstname' => trim($this->input->post('firstname')), 'lastname' => trim($this->input->post('lastname')), 'email' => trim($this->input->post('email')), 'url' => site_url('login'), 'password' => $password, 'subject' => 'Invitation - Extracredit'];
                send_email(trim($this->input->post('email')), 'add_user', $email_data);
                $this->users_model->common_insert_update('insert', TBL_USERS, $dataArr);
                $this->session->set_flashdata('success', 'User has been added successfully and Email has been sent to user successfully');
            }
            redirect('users');
        }
        $this->template->load('default', 'users/form', $data);
    }

    /**
     * Edit user data
     * @param int $id
     * */
    public function edit($id) {
        $this->add($id);
    }

    /**
     * Callback function to check email validation - Email is unique or not
     * @param string $str
     * @return boolean
     */
    public function is_uniquemail() {
        $email = trim($this->input->post('email'));
        $user = $this->users_model->check_unique_email($email);
        if ($user) {
            $this->form_validation->set_message('is_uniquemail', 'Email address is already in use!');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Delete user
     * @param int $id
     * */
    public function delete($id = NULL) {
        $id = base64_decode($id);
        if (is_numeric($id)) {
            $user = $this->users_model->get_user_detail(['id' => $id], 'id,firstname');
            if ($user) {
                $update_array = array(
                    'is_delete' => 1
                );
                $this->users_model->common_insert_update('update', TBL_USERS, $update_array, ['id' => $id]);
                $this->session->set_flashdata('success', $user['firstname'] . ' has been deleted successfully!');
            } else {
                $this->session->set_flashdata('error', 'Invalid request. Please try again!');
            }
            redirect('users');
        } else {
            show_404();
        }
    }

    /**
     * Block/Unblock user
     * @param int $id
     * */
    public function block($id = NULL) {
        $id = base64_decode($id);
        if (is_numeric($id)) {
            $user = $this->users_model->get_user_detail(['id' => $id], 'firstname,is_active');
            if ($user) {
                if ($user['is_active'] == 0) {
                    $update_array = array(
                        'is_active' => 1
                    );
                    $this->session->set_flashdata('success', $user['firstname'] . ' has been unblocked successfully!');
                } else {
                    $update_array = array(
                        'is_active' => 0
                    );
                    $this->session->set_flashdata('success', $user['firstname'] . ' has been blocked successfully!');
                }
                $this->users_model->common_insert_update('update', TBL_USERS, $update_array, ['id' => $id]);
            } else {
                $this->session->set_flashdata('error', 'Invalid request. Please try again!');
            }
            redirect('users');
        } else {
            show_404();
        }
    }

    /**
     * This function used to check Unique email at the time of user's add at admin side
     * */
    public function checkUniqueEmail() {
        $email = trim($this->input->get('email'));
        $user = $this->users_model->check_unique_email($email);
        if ($user) {
            echo "false";
        } else {
            echo "true";
        }
        exit;
    }

}

/* End of file Users.php */
/* Location: ./application/controllers/Users.php */