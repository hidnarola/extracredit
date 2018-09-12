<?php

/**
 * Reports Controller - Manage Reports
 * @author REP
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(['guests_model', 'donors_model', 'accounts_model', 'payments_model','contacts_model','vendors_model']);
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

        foreach ($donors as $key => $val) {
            $donors[$key] = $val;
            $donors[$key]['date'] = ($val['date'] != '') ? date('m/d/Y', strtotime($val['date'])) : '';
            $donors[$key]['post_date'] = ($val['post_date'] != '') ? date('m/d/Y', strtotime($val['post_date'])) : '';
            $donors[$key]['created'] = date('m/d/Y', strtotime($val['created']));
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

        foreach ($guests as $key => $val) {
            $guests[$key] = $val;
            $guests[$key]['AIR_date'] = ($val['AIR_date'] != '') ? date('m/d/Y', strtotime($val['AIR_date'])) : '';
            $guests[$key]['invite_date'] = ($val['invite_date'] != '') ? date('m/d/Y', strtotime($val['invite_date'])) : '';
            $guests[$key]['guest_date'] = ($val['guest_date'] != '') ? date('m/d/Y', strtotime($val['guest_date'])) : '';
            $guests[$key]['created'] = date('m/d/Y', strtotime($val['created']));
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
            $programs[$key]['created'] = date('m/d/Y', strtotime($val['created']));
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
            $awards[$key]['created'] = date('m/d/Y', strtotime($val['created']));
            $awards[$key]['check_date'] = ($val['check_date'] != '') ? date('m/d/Y', strtotime($val['check_date'])) : '';
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
            $vendor_admin[$key]['created'] = date('m/d/Y', strtotime($val['created']));
            $vendor_admin[$key]['check_date'] = ($val['check_date'] != '') ? date('m/d/Y', strtotime($val['check_date'])) : '';
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
        foreach ($balance_report as $key => $val) {
            $balance_report[$key] = $val;
            $balance_report[$key]['post_date'] = ($val['post_date'] != '') ? date('m/d/Y', strtotime($val['post_date'])) : '';
            $balance_report[$key]['check_date'] = ($val['check_date'] != '') ? date('m/d/Y', strtotime($val['check_date'])) : '';
        }
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
        foreach ($payment_report as $key => $val) {
            $payment_report[$key] = $val;
            $payment_report[$key]['check_date'] = ($val['check_date'] != '') ? date('m/d/Y', strtotime($val['check_date'])) : '';
        }
        $final['data'] = $payment_report;
        echo json_encode($final);
    }

     /**
     * Get Donor subscriber report
     * developed by : sm
     * */
    public function get_donor_subscriber()
    {
        $data['title'] = 'Extracredit | Donors Report';
        $data['donor'] = $this->donors_model->get_donors_report();
        $this->template->load('default', 'subscribers/donors_report', $data);
    }

    /**
     * Get Donor subscriber report via AJAX call
     * */
    public function get_donor_subscriber_report() {
        $final['recordsFiltered'] = $final['recordsTotal'] = $this->donors_model->get_donors_report('count');
        $final['redraw'] = 1;
        $balance_report = $this->donors_model->get_donors_report('result');
        $final['data'] = $balance_report;
        // print_r($final);
        // die;
        echo json_encode($final);
    }

    /**
     * Get Donor subscriber report via AJAX call
     * */
    public function get_contact_subscriber_report() {
        $final['recordsFiltered'] = $final['recordsTotal'] = $this->contacts_model->get_contacts_report('count');
        $final['redraw'] = 1;
        $balance_report = $this->contacts_model->get_contacts_report('result');
        $final['data'] = $balance_report;
        echo json_encode($final);
    }

    /**
     * Get Contacts subscriber report
     * developed by : sm
     * */
    public function get_contact_subscriber()
    {
        $data['title'] = 'Extracredit | Contacts Report';
        $data['contacts'] = $this->contacts_model->get_contacts_report('result');
        $this->template->load('default', 'subscribers/contacts_report', $data);
    }

    /**
     * Get Account subscriber report
     * developed by : sm
     * */
    public function get_accounts_subscriber()
    {
        $data['title'] = 'Extracredit | Award Recipients Report';
        $data['accounts'] = $this->accounts_model->get_accounts_report();
        $this->template->load('default', 'subscribers/accounts_report', $data);
    }

    /**
     * Get Guests subscriber report
     * developed by : sm
     * */
    public function get_guests_subscriber()
    {
        $data['title'] = 'Extracredit | Guests Report';
        $data['guests'] = $this->guests_model->get_guests_report();
        $this->template->load('default', 'subscribers/guests_report', $data);
    }

    public function get_vendors_subscriber()
    {
        $data['title'] = 'Extracredit | Vendors Report';
        $data['vendors'] = $this->vendors_model->get_vendors_report();
        $this->template->load('default', 'subscribers/vendors_report', $data);  
    }
}
