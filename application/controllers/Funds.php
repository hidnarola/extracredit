<?php

/**
 * Funds Controller - Manage Funds
 * @author KU
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Funds extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('funds_model');
    }

    /**
     * Listing of admin fund
     */
    public function admin_fund() {
        $data['title'] = 'Extracredit | Admin Fund';
        $this->template->load('default', 'funds/admin_fund', $data);
    }

    /**
     * This function used to get admin fund data for ajax table
     * */
    public function get_adminfund() {
        $final['recordsFiltered'] = $final['recordsTotal'] = $this->funds_model->get_adminfund('count');
        $final['redraw'] = 1;
        $admin_fund = $this->funds_model->get_adminfund('result');
        $final['data'] = $admin_fund;
        echo json_encode($final);
    }

    /**
     * Listing of account fund
     */
    public function accounts() {
        $data['title'] = 'Extracredit | Account Fund';
        $this->template->load('default', 'funds/accounts', $data);
    }

    /**
     * This function used to get account fund data for ajax table
     * */
    public function get_accountfund() {
        $final['recordsFiltered'] = $final['recordsTotal'] = $this->funds_model->get_accountfund('count');
        $final['redraw'] = 1;
        $account_fund = $this->funds_model->get_accountfund('result');
        $final['data'] = $account_fund;
        echo json_encode($final);
    }

    /**
     * Listing of donor fund
     */
    public function donors() {
        $data['title'] = 'Extracredit | Donor Fund';
        $this->template->load('default', 'funds/donors', $data);
    }

    /**
     * This function used to get donor fund data for ajax table
     * */
    public function get_donorfund() {
        $final['recordsFiltered'] = $final['recordsTotal'] = $this->funds_model->get_donorfund('count');
        $final['redraw'] = 1;
        $donor_fund = $this->funds_model->get_donorfund('result');
        $final['data'] = $donor_fund;
        echo json_encode($final);
    }

}

/* End of file Funds.php */
/* Location: ./application/controllers/Funds.php */