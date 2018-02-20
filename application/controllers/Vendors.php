<?php

/**
 * Vendors Controller - Manage vendors
 * @author KU
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Vendors extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('vendors_model');
    }

    /**
     * Listing of all vendors
     */
    public function index() {
        checkPrivileges('accounts', 'view');
        $data['perArr'] = checkPrivileges('accounts');
        $data['title'] = 'Extracredit | Vendors';
        $this->template->load('default', 'vendors/list_vendors', $data);
    }

    /**
     * Get vendors data for ajax table
     * */
    public function get_vendors() {
        checkPrivileges('accounts', 'view');
        $final['recordsFiltered'] = $final['recordsTotal'] = $this->vendors_model->get_vendors('count');
        $final['redraw'] = 1;
        $final['data'] = $this->vendors_model->get_vendors('result');
        echo json_encode($final);
    }

    /**
     * Add /edit accounts data
     * @param type $id
     */
    public function add($id = NULL) {
        if (!is_null($id))
            $id = base64_decode($id);
        if (is_numeric($id)) {
            $vendor = $this->vendors_model->get_vendor_details($id);
            if ($vendor) {
                $data['vendor'] = $vendor;
                $data['title'] = 'Extracredit | Edit Vendor';
                $data['heading'] = 'Edit Vendor';
            } else {
                show_404();
            }
        } else {
            //-- Check logged in user has access to add account
            checkPrivileges('accounts', 'add');
            $data['title'] = 'Extracredit | Add Vendor';
            $data['heading'] = 'Add Vendor';
            $data['cities'] = [];
        }

        $this->form_validation->set_rules('name', 'Vendor Name', 'trim|required');
        $this->form_validation->set_rules('contact_name', 'Contact Name', 'trim|required');

        if ($this->form_validation->run() == TRUE) {
            //-- Get state id from post value
            $state_id = $city_id = NULL;

            $state_code = $this->input->post('state_short');
            if (!empty($state_code)) {
                $post_city = $this->input->post('city_id');
                $state = $this->vendors_model->sql_select(TBL_STATES, 'id', ['where' => ['short_name' => $state_code]], ['single' => true]);
                $state_id = $state['id'];
                if (!empty($post_city)) {
                    $city = $this->vendors_model->sql_select(TBL_CITIES, 'id', ['where' => ['state_id' => $state_id, 'name' => $post_city]], ['single' => true]);
                    if (!empty($city)) {
                        $city_id = $city['id'];
                    } else {
                        $city_id = $this->vendors_model->common_insert_update('insert', TBL_CITIES, ['name' => $post_city, 'state_id' => $state_id]);
                    }
                }
            }

            $dataArr = array(
                'name' => trim($this->input->post('name')),
                'contact_name' => trim($this->input->post('contact_name')),
                'address' => trim($this->input->post('address')),
                'city_id' => $city_id,
                'state_id' => $state_id,
                'zip' => $this->input->post('zip'),
                'email' => $this->input->post('email'),
                'phone' => $this->input->post('phone'),
                'website' => $this->input->post('website'),
                'created' => date('Y-m-d H:i:s')
            );

            if (is_numeric($id)) {
                $dataArr['modified'] = date('Y-m-d H:i:s');
                $this->vendors_model->common_insert_update('update', TBL_VENDORS, $dataArr, ['id' => $id]);
                $this->session->set_flashdata('success', 'Vendor details has been updated successfully.');
            } else {
                $dataArr['created'] = date('Y-m-d H:i:s');
                $this->vendors_model->common_insert_update('insert', TBL_VENDORS, $dataArr);
                $this->session->set_flashdata('success', 'Vendor details has been added successfully');
            }
            redirect('vendors');
        }
        $this->template->load('default', 'vendors/form', $data);
    }

    /**
     * Edit Vendor data
     * @param int $id
     * */
    public function edit($id) {
        //-- Check logged in user has access to edit account
        checkPrivileges('accounts', 'edit');
        $this->add($id);
    }

    /**
     * Delete vendor
     * @param int $id
     * */
    public function delete($id = NULL) {
        checkPrivileges('accounts', 'delete');
        $id = base64_decode($id);
        if (is_numeric($id)) {
            $vendor = $this->vendors_model->sql_select(TBL_VENDORS, 'id,email', ['where' => ['id' => $id]], ['single' => true]);
            if (!empty($vendor)) {
                $update_array = array(
                    'is_delete' => 1
                );
                $this->vendors_model->common_insert_update('update', TBL_VENDORS, $update_array, ['id' => $id]);
                $this->session->set_flashdata('success', 'Vendor has been deleted successfully!');
            } else {
                $this->session->set_flashdata('error', 'Invalid request. Please try again!');
            }
            redirect('vendors');
        } else {
            show_404();
        }
    }

    /**
     * View vendor
     * @return Partial View
     */
    public function view() {
        checkPrivileges('account', 'view');
        $vendor_id = base64_decode($this->input->post('id'));
        $vendor = $this->vendors_model->get_vendor_details($vendor_id);
        if (!empty($vendor)) {
            $data['vendor'] = $vendor;
            return $this->load->view('vendors/vendor_view', $data);
        } else {
            show_404();
        }
    }

    /**
     * Ajax call to this function checks Unique Vendor at the time of vendor's add and edit
     * */
    public function checkUniqueVendor($id = NULL) {
        $where = ['name' => trim($this->input->get('name'))];
        if (!is_null($id)) {
            $id = base64_decode($id);
            $where['id!='] = $id;
        }
        $vendor = $this->vendors_model->sql_select(TBL_VENDORS, 'id', ['where' => $where], ['single' => true]);
        if (!empty($vendor)) {
            echo "false";
        } else {
            echo "true";
        }
        exit;
    }

}

/* End of file Vendors.php */
/* Location: ./application/controllers/Vendors.php */