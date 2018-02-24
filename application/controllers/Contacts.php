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

    /**
     * Import contact data from CSV file
     * @author KU
     */
    public function import_contact() {

        checkPrivileges('account', 'add');
        $fileDirectory = CONTACT_CSV;
        $config['overwrite'] = FALSE;
        $config['remove_spaces'] = TRUE;
        $config['upload_path'] = CONTACT_CSV;
        $config['allowed_types'] = 'csv|CSV';
        $this->load->library('upload', $config);

        //-- Upload csv file
        if ($this->upload->do_upload('import_contact')) {
            $fileDetails = $this->upload->data();

            //-- cities array
            $cities = $this->contacts_model->sql_select(TBL_CITIES);
            foreach ($cities as $city) {
                $cities_arr[$city['id']] = $city['name'];
                $states_arr[$city['id']] = $city['state_id'];
            }
            $cities_arr = array_map('strtolower', $cities_arr);

            //-- vendor email array
            $contact_emails = $this->contacts_model->sql_select(TBL_CONTACTS, 'email', ['where' => ['is_delete' => 0, 'email!=' => '']]);
            $contact_emails_arr = array_column($contact_emails, 'email');
            $contact_emails_arr = array_map('strtolower', $contact_emails_arr);


            $row = 1;
            $handle = fopen($fileDirectory . "/" . $fileDetails['file_name'], "r");

            $contact_data = $check_email_valid = $check_email = $check_city = $imported_emails = [];
            if (($data2 = fgetcsv($handle)) !== FALSE) {
                $data_format2 = array('name', 'email', 'address', 'zip', 'city', 'phone', 'website');

                //-- check if first colums is according to predefined row
                if ($data_format2 == $data2) {
                    while (($col_data = fgetcsv($handle)) !== FALSE) {
                        $contact = [];
                        if (empty($col_data[0])) {
                            fclose($handle);
                            $this->session->set_flashdata('error', 'Contact name is missing in Row No. ' . $row);
                            redirect('contacts');
                        } else {

                            $contact['name'] = $col_data[0];

                            //--check email is unique or not
                            if (!empty($col_data[1])) {
                                if (!filter_var($col_data[1], FILTER_VALIDATE_EMAIL)) {
                                    $check_email_valid[] = $row;
                                } else if (array_search(strtolower($col_data[1]), $contact_emails_arr) != FALSE) {
                                    $check_email[] = $row;
                                } else {
                                    $contact['email'] = $col_data[1];
                                }
                            } else {
                                $contact['email'] = NULL;
                            }

                            $imported_emails[] = $col_data[1];
                            $contact['address'] = $col_data[2];
                            $contact['zip'] = $col_data[3];

                            //--check city is valid or not
                            if (!empty($col_data[4])) {
                                if (array_search(strtolower($col_data[4]), $cities_arr) != FALSE) {
                                    $contact['city_id'] = array_search(strtolower($col_data[4]), $cities_arr);
                                    $contact['state_id'] = $states_arr[$contact['city_id']];
                                } else {
                                    $check_city[] = $row;
                                }
                            } else {
                                $contact['city_id'] = NULL;
                                $contact['state_id'] = NULL;
                            }

                            $contact['phone'] = $col_data[5];
                            $contact['website'] = $col_data[6];
                            $contact_data[] = $contact;
                            $row++;
                        }
                    }
                    //- check if email is valid or not
                    if (count(array_unique($imported_emails)) != count($imported_emails)) { //-- check emails in column are unique or not
                        fclose($handle);
                        $this->session->set_flashdata('error', "Duplicate value in email column.");
                    } else if (!empty($check_email_valid)) { //-- check Account/Program in columns are valid or not
                        $rows = implode(',', $check_email_valid);
                        $this->session->set_flashdata('error', "Donor's Email is not in valid format. Please check entries at row number - " . $rows);
                    } else if (!empty($check_email)) { //-- check Account/Program in columns are valid or not
                        $rows = implode(',', $check_email);
                        $this->session->set_flashdata('error', "Account's Email already exist in the system. Please check entries at row number - " . $rows);
                    } else if (!empty($check_city)) { //-- check city in column are unique or not
                        $rows = implode(',', $check_city);
                        $this->session->set_flashdata('error', "City doesn't exist in the system. Please check entries at row number - " . $rows);
                    } else {
                        if (!empty($contact_data)) {
                            //-- Insert contact details into database
                            foreach ($contact_data as $val) {
                                $contact_arr = [
                                    'name' => $val['name'],
                                    'address' => $val['address'],
                                    'city_id' => $val['city_id'],
                                    'state_id' => $val['state_id'],
                                    'zip' => $val['zip'],
                                    'email' => $val['email'],
                                    'phone' => $val['phone'],
                                    'website' => $val['website'],
                                    'created' => date('Y-m-d H:i:s')
                                ];
                                $contact_id = $this->contacts_model->common_insert_update('insert', TBL_CONTACTS, $contact_arr);
                            }
                            $this->session->set_flashdata('success', "CSV file imported successfully! Contact data added successfully");
                        } else {
                            $this->session->set_flashdata('error', "CSV file is empty! Please upload valid file");
                        }
                    }
                } else {
                    fclose($handle);
                    $this->session->set_flashdata('error', 'The columns in this csv file does not match to the database');
                }
            } else {
                $this->session->set_flashdata('error', "CSV file is empty! Please upload valid file");
            }
            fclose($handle);
            redirect('contacts');
        } else {
            $this->session->set_flashdata('error', strip_tags($this->upload->display_errors()));
            redirect('contacts');
        }
    }

}

/* End of file Contacts.php */
/* Location: ./application/controllers/Contacts.php */