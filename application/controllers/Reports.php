<?php

/**
 * Reports Controller - Manage Reports
 * @author REP
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(['guests_model', 'donors_model', 'accounts_model', 'payments_model']);
    }

    /**
     * Listing of All Guests for reports
     */
    public function donors_report() {
        checkPrivileges('donor_report', 'view');
        $data['title'] = 'Extracredit | Donors Report';
        $this->template->load('default', 'reports/donors_report', $data);
    }

    /**
     * Get donors data for ajax table
     * */
    public function get_donors_reports() {
        checkPrivileges('donor_report', 'view');
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
     * Listing of All Guests for reports
     */
    public function guests_report() {
        checkPrivileges('guest_report', 'view');
        $data['title'] = 'Extracredit | Guests Report';
        $this->template->load('default', 'reports/guests_report', $data);
    }

    /**
     * Get Guest data for ajax table
     * */
    public function get_guests_reports() {
        checkPrivileges('guest_report', 'view');
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

    /**
     * Listing of All Programs/amc for reports
     */
    public function programs_amc_report() {
        checkPrivileges('account_report', 'view');
        $data['title'] = 'Extracredit | Program / AMC';
        $this->template->load('default', 'reports/programs_amc_report', $data);
    }

    /**
     * Get programs/amc data for ajax table
     * */
    public function get_programs_amc_report() {
        checkPrivileges('account_report', 'view');
        $final['recordsFiltered'] = $final['recordsTotal'] = $this->accounts_model->get_programs_amc_report('count');
        $final['redraw'] = 1;
        $programs = $this->accounts_model->get_programs_amc_report('result');
        $start = $this->input->get('start') + 1;

        foreach ($programs as $key => $val) {
            $programs[$key] = $val;
            $programs[$key]['created'] = date('d M, Y', strtotime($val['created']));
        }

        $final['data'] = $programs;
        echo json_encode($final);
    }

    /**
     * Listing of All awards 90%(outgoing money) for reports
     */
    public function awards_report() {
        checkPrivileges('account_report', 'view');
        $data['title'] = 'Extracredit | Awards';
        $this->template->load('default', 'reports/awards_report', $data);
    }

    /**
     * Get programs/awards 90% data for ajax table
     * */
    public function get_awards_report() {
        checkPrivileges('account_report', 'view');
        $final['recordsFiltered'] = $final['recordsTotal'] = $this->accounts_model->get_awards_report('count');
        $final['redraw'] = 1;
        $awards = $this->accounts_model->get_awards_report('result');
        $start = $this->input->get('start') + 1;

        foreach ($awards as $key => $val) {
            $awards[$key] = $val;
            $awards[$key]['created'] = date('d M, Y', strtotime($val['created']));
        }

        $final['data'] = $awards;
        echo json_encode($final);
    }

    /**
     * Listing of VENDOR ADMIN(10%) for reports
     */
    public function vendor_admin_report() {
        checkPrivileges('account_report', 'view');
        $data['title'] = 'Extracredit | Awards';
        $this->template->load('default', 'reports/vendor_admin_report', $data);
    }

    /**
     * Get programs/awards 90% data for ajax table
     * */
    public function get_vendor_admin_report() {
        checkPrivileges('account_report', 'view');
        $final['recordsFiltered'] = $final['recordsTotal'] = $this->accounts_model->get_vendor_admin_report('count');
        $final['redraw'] = 1;
        $vendor_admin = $this->accounts_model->get_vendor_admin_report('result');
        $start = $this->input->get('start') + 1;

        foreach ($vendor_admin as $key => $val) {
            $vendor_admin[$key] = $val;
            $vendor_admin[$key]['created'] = date('d M, Y', strtotime($val['created']));
        }
        $final['data'] = $vendor_admin;
        echo json_encode($final);
    }

    /**
     * Display Program/AMC Balances Report
     * @author KU
     */
    public function amc_balance_report() {
        checkPrivileges('amc_balance_report', 'view');
        $data['title'] = 'Extracredit | Program/AMC Balances Report';
        $this->template->load('default', 'reports/amc_balance_report', $data);
    }

    /**
     * Get program/AMC Balance report
     * */
    public function get_amc_balance_report() {
        checkPrivileges('amc_balance_report', 'view');
        $final['recordsFiltered'] = $final['recordsTotal'] = $this->accounts_model->get_amc_balance_report('count');
        $final['redraw'] = 1;
        $balance_report = $this->accounts_model->get_amc_balance_report('result');
        $final['data'] = $balance_report;
        echo json_encode($final);
    }

    /**
     * Display Payments Made Report
     * @author KU
     */
    public function payments_made_report() {
        checkPrivileges('payments_made_report', 'view');
        $data['title'] = 'Extracredit | Payments Made Report';
        $this->template->load('default', 'reports/payments_made_report', $data);
    }

    /**
     * Get program/AMC Balance report
     * */
    public function get_payments_made_report() {
        checkPrivileges('payments_made_report', 'view');
        $final['recordsFiltered'] = $final['recordsTotal'] = $this->payments_model->get_payments_made_report('count');
        $final['redraw'] = 1;
        $payment_report = $this->payments_model->get_payments_made_report('result');
        $final['data'] = $payment_report;
        echo json_encode($final);
    }

}
