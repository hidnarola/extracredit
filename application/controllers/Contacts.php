<?php

/**
 * Contacts Controller - Manage contacts
 * @author KU
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Contacts extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('contacts_model');
    }

    /**
     * Listing of all contacts
     */
    public function index() {
        checkPrivileges('accounts', 'view');
        $data['perArr'] = checkPrivileges('accounts');
        $data['title'] = 'Extracredit | Contacts';
        $this->template->load('default', 'contacts/list_contacts', $data);
    }

    /**
     * Get contacts data for ajax table
     * */
    public function get_contacts() {
        checkPrivileges('accounts', 'view');
        $final['recordsFiltered'] = $final['recordsTotal'] = $this->contacts_model->get_contacts('count');
        $final['redraw'] = 1;
        $final['data'] = $this->contacts_model->get_contacts('result');
        echo json_encode($final);
    }

    /**
     * Add /edit contact data
     * @param type $id
     */
    public function add($id = NULL) {
        if (!is_null($id))
            $id = base64_decode($id);
        if (is_numeric($id)) {
            $contact = $this->contacts_model->get_contact_details($id);
            if ($contact) {
                $data['contact'] = $contact;
                $data['title'] = 'Extracredit | Edit Contact';
                $data['heading'] = 'Edit Contact';
            } else {
                show_404();
            }
        } else {
            //-- Check logged in user has access to add account
            checkPrivileges('accounts', 'add');
            $data['title'] = 'Extracredit | Add Contact';
            $data['heading'] = 'Add Contact';
            $data['cities'] = [];
        }

        $this->form_validation->set_rules('name', 'Contact Name', 'trim|required');

        if ($this->form_validation->run() == TRUE) {
            //-- Get state id from post value
            $state_id = $city_id = NULL;

            $state_code = $this->input->post('state_short');
            if (!empty($state_code)) {
                $post_city = $this->input->post('city_id');
                $state = $this->contacts_model->sql_select(TBL_STATES, 'id', ['where' => ['short_name' => $state_code]], ['single' => true]);
                $state_id = $state['id'];
                if (!empty($post_city)) {
                    $city = $this->contacts_model->sql_select(TBL_CITIES, 'id', ['where' => ['state_id' => $state_id, 'name' => $post_city]], ['single' => true]);
                    if (!empty($city)) {
                        $city_id = $city['id'];
                    } else {
                        $city_id = $this->contacts_model->common_insert_update('insert', TBL_CITIES, ['name' => $post_city, 'state_id' => $state_id]);
                    }
                }
            }

            $dataArr = array(
                'name' => trim($this->input->post('name')),
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
                $this->contacts_model->common_insert_update('update', TBL_CONTACTS, $dataArr, ['id' => $id]);
                $this->session->set_flashdata('success', 'Contact details has been updated successfully.');
            } else {
                $dataArr['created'] = date('Y-m-d H:i:s');
                $this->contacts_model->common_insert_update('insert', TBL_CONTACTS, $dataArr);
                $this->session->set_flashdata('success', 'Contact details has been added successfully');
            }
            redirect('contacts');
        }
        $this->template->load('default', 'contacts/form', $data);
    }

    /**
     * Edit Account data
     * @param int $id
     * */
    public function edit($id) {
        //-- Check logged in user has access to edit account
        checkPrivileges('accounts', 'edit');
        $this->add($id);
    }

    /**
     * Delete contact
     * @param int $id
     * */
    public function delete($id = NULL) {
        checkPrivileges('accounts', 'delete');
        $id = base64_decode($id);
        if (is_numeric($id)) {
            $contact = $this->contacts_model->sql_select(TBL_CONTACTS, 'id,email', ['where' => ['id' => $id]], ['single' => true]);
            if (!empty($contact)) {
                $update_array = array(
                    'is_delete' => 1
                );
                $this->contacts_model->common_insert_update('update', TBL_CONTACTS, $update_array, ['id' => $id]);
                $this->session->set_flashdata('success', 'Contact has been deleted successfully!');
            } else {
                $this->session->set_flashdata('error', 'Invalid request. Please try again!');
            }
            redirect('contacts');
        } else {
            show_404();
        }
    }

    /**
     * View Contact Details
     * @return Partial View
     */
    public function view() {
        checkPrivileges('account', 'view');
        $contact_id = base64_decode($this->input->post('id'));
        $contact = $this->contacts_model->get_contact_details($contact_id);
        if (!empty($contact)) {
            $data['contact'] = $contact;
            return $this->load->view('contacts/contact_view', $data);
        } else {
            show_404();
        }
    }

    /**
     * Ajax call to this function checks Unique contact at the time of contact's add and edit
     * */
    public function checkUniqueContact($id = NULL) {
        $where = ['name' => trim($this->input->get('name'))];
        if (!is_null($id)) {
            $id = base64_decode($id);
            $where['id!='] = $id;
        }
        $contact = $this->contacts_model->sql_select(TBL_CONTACTS, 'id', ['where' => $where], ['single' => true]);
        if (!empty($contact)) {
            echo "false";
        } else {
            echo "true";
        }
        exit;
    }

}

/* End of file Contacts.php */
/* Location: ./application/controllers/Contacts.php */