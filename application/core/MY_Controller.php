<?php

/**
 * For default operation
 * @author KU
 * */
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public $is_loggedin = false;

    public function __construct() {
        parent::__construct();
        $session = $this->session->userdata('extracredit_user');
        if (!empty($session['id']) && !empty($session['email']))
            $this->is_loggedin = true;
        else {
            $encoded_email = get_cookie(REMEMBER_ME_COOKIE_NAME);
            $email = $this->encrypt->decode($encoded_email);
            if (!empty($email)) {
                $user = $this->users_model->get_user_detail(['email' => $email]);
                if (!empty($user)) {
                    $this->session->set_userdata('extracredit_user', $user);
                    $this->is_loggedin = true;
                }
            }
        }
        $this->controller = $this->router->fetch_class();
        $this->action = $this->router->fetch_method();
        //-- If not logged in and try to access inner pages then redirect user to login page
        if (!$this->is_loggedin) {
            if (strtolower($this->controller) != 'login') {
                $redirect = site_url(uri_string());
                redirect('login?redirect=' . base64_encode($redirect));
            }
        } else { //-- If logged in and access login page the redirect user to home page
            if (strtolower($this->controller) == 'login' && strtolower($this->action) != 'logout') {
                redirect('home');
            }
        }
        $this->admin_fund = $this->users_model->get_admin_fund();
        $this->total_users = ($this->users_model->sql_select(TBL_USERS, 'id', ['where' => ['is_delete' => 0]], ['count' => true])) - 1;
    }

}
