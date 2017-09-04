<?php

/**
 * Reports Controller - Manage Reports
 * @author REP
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('guests_model');
        $this->load->model('donors_model');
    }

    /**
     * Listing of All Guests
     */
    public function donors_report() {
        $data['title'] = 'Extracredit | Guests';
        $this->template->load('default', 'reports/donors_report', $data);
    }
    
     /**
     * Get donors data for ajax table
     * */
    public function get_donors_reports() {
        $final['recordsFiltered'] = $final['recordsTotal'] = $this->donors_model->get_donors_reports('count');
        $final['redraw'] = 1;
        $donors = $this->donors_model->get_donors_reports('result');
        $start = $this->input->get('start') + 1;

        foreach ($donors as $key => $val) {
            $donors[$key] = $val;
            $donors[$key]['date'] = date('d M, Y', strtotime($val['date']));
            $donors[$key]['post_date'] = date('d M, Y', strtotime($val['post_date']));
            $donors[$key]['created'] = date('d M, Y', strtotime($val['created']));
        }

        $final['data'] = $donors;
        echo json_encode($final);
    }

}
