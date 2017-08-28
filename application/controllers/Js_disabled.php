<?php

/**
 * Js_disabled Controller
 * Display js_disabled page when javascript is disabled
 * @author KU
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Js_disabled extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->load->view('Templates/javascript_disabled');
    }

}

?> 