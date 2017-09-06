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
    /**
     * Listing of All Guests
     */
    public function guests_report() {
        $data['title'] = 'Extracredit | Guests';
        $this->template->load('default', 'reports/guests_report', $data);
    }
    
     /**
     * Get donors data for ajax table
     * */
    public function get_guests_reports() {
        $final['recordsFiltered'] = $final['recordsTotal'] = $this->guests_model->get_guests_reports('count');
        $final['redraw'] = 1;
        $guests = $this->guests_model->get_guests_reports('result');
        $start = $this->input->get('start') + 1;

        foreach ($guests as $key => $val) {
            $guests[$key] = $val;
            $guests[$key]['AIR_date'] = date('d M, Y', strtotime($val['AIR_date']));
            $guests[$key]['invite_date'] = date('d M, Y', strtotime($val['invite_date']));
            $guests[$key]['guest_date'] = date('d M, Y', strtotime($val['guest_date']));
            $guests[$key]['created'] = date('d M, Y', strtotime($val['created']));
        }

        $final['data'] = $guests;
        echo json_encode($final);
    }

}
