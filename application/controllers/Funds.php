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
     * Listing of All Funds
     */
    public function index() {
        $data['title'] = 'Extracredit | Funds';
    }

}

/* End of file Funds.php */
/* Location: ./application/controllers/Funds.php */