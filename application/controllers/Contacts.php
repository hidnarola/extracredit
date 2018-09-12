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
        $this->load->model('communication_manager_model');
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
        $data['contact_types'] = $this->contacts_model->sql_select(TBL_CONTACT_TYPES, 'id,type', ['where' => ['is_delete' => 0]]);
        
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
                'contact_type_id' => $this->input->post('contact_type_id'),
                'phone' => $this->input->post('phone'),
                'website' => trim($this->input->post('website')),
                'created' => date('Y-m-d H:i:s')
            );

            require_once(APPPATH."libraries/Mailin.php");
            // $this->load->library('Mailin'); //Load library for subscribe user in SendInBlue.com
            $mailin = new Mailin('https://api.sendinblue.com/v2.0','VGcJrUg9ypYRjExh',50000);    //Optional parameter: Timeout in MS
            //Api Key(v2.0) : VGcJrUg9ypYRjExh
            
            if($this->input->post('is_subscribed') == 1 && $this->input->post('is_subscribed') != '')
            {
                $dataArr['is_subscribed'] = 1; //insert in contact table
                $data = array( "email" => $this->input->post('email'),
                "attributes" => array("FIRSTNAME" => $this->input->post('name'), "LASTNAME"=>""),
                "listid" => array(2)
                );

                $mailin->create_update_user($data);
            }
            else
            {
                $dataArr['is_subscribed'] = 0; //update in contact table
                $data = array( "email" => $this->input->post('email'),
                "listid_unlink" => array(2)
                );
                $mailin->create_update_user($data);
            }
           
             
            if (is_numeric($id)) {
                $dataArr['modified'] = date('Y-m-d H:i:s');
                $this->contacts_model->common_insert_update('update', TBL_CONTACTS, $dataArr, ['id' => $id]);
                $this->session->set_flashdata('success', 'Contact details has been updated successfully.');
            } else{
                $dataArr['created'] = date('Y-m-d H:i:s');
                $this->contacts_model->common_insert_update('insert', TBL_CONTACTS, $dataArr);
                $this->session->set_flashdata('success', 'Contact details has been added successfully');
            }

            if (isset($_POST['save_add_another']))
            {
                redirect('contacts/add');
            }
            else
            {
                redirect('contacts');
            }
            
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
                //Remove Subscribed user from SendInBlue.com
                require_once(APPPATH."libraries/Mailin.php");
                $mailin = new Mailin('https://api.sendinblue.com/v2.0','VGcJrUg9ypYRjExh',50000);    //Optional parameter: Timeout in MS
                $data = array( "email" => $contact['email'],
                "listid_unlink" => array(2)
                );
                $mailin->create_update_user($data);
                
                $update_array = array(
                    'is_delete' => 1,
                    'is_subscribed' => 0
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
        checkPrivileges('accounts', 'view');
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

        checkPrivileges('accounts', 'add');
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
            // $contact_type = $this->contacts_model->sql_select(TBL_CONTACT_TYPES);
            $contact_type = $this->contacts_model->sql_select(TBL_CONTACT_TYPES, 'type,id', ['where' => ['is_delete' => 0]]);
    
            foreach ($contact_type as $type) 
            {
                $contact_type_arr[$type['id']] = $type['type'];
            }
            $contact_type_arr = array_map('strtolower', $contact_type_arr);
            
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

            $contact_data = $check_email_valid = $check_email = $check_contact_type = $check_city = $imported_emails = [];
            if (($data2 = fgetcsv($handle)) !== FALSE) {
                $data_format2 = array('name', 'email', 'contact_type', 'address', 'zip', 'city', 'phone', 'website');

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

                            //--check contact type is valid or not
                            if (!empty($col_data[2])) {
                                if (array_search(strtolower($col_data[2]), $contact_type_arr) != FALSE) {
                                    $contact['contact_type_id'] = array_search(strtolower($col_data[2]), $contact_type_arr);
                                    
                                } else {
                                    $check_contact_type[] = $row;
                                }
                            } else {
                                $contact['contact_type_id'] = NULL;
                            }

                            $imported_emails[] = $col_data[1];
                            $contact['contact_type'] = $col_data[2];
                            $contact['address'] = $col_data[3];
                            $contact['zip'] = $col_data[4];

                            //--check city is valid or not
                            if (!empty($col_data[5])) {
                                if (array_search(strtolower($col_data[5]), $cities_arr) != FALSE) {
                                    $contact['city_id'] = array_search(strtolower($col_data[5]), $cities_arr);
                                    $contact['state_id'] = $states_arr[$contact['city_id']];
                                } else {
                                    $check_city[] = $row;
                                }
                            } else {
                                $contact['city_id'] = NULL;
                                $contact['state_id'] = NULL;
                            }

                            $contact['phone'] = $col_data[6];
                            $contact['website'] = $col_data[7];
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
                    } else if (!empty($check_contact_type)) { //-- check city in column are unique or not
                        $rows = implode(',', $check_contact_type);
                        $this->session->set_flashdata('error', "Contact type doesn't exist in the system. Please check entries at row number - " . $rows);
                    }else {
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
                                    'contact_type_id' => $val['contact_type_id'],
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

    /**
     * Listing of All Contact communication
     * @author KU
     */
    public function communication($id = null) {
        checkPrivileges('accounts', 'view');
        $contact = $this->contacts_model->sql_select(TBL_CONTACTS, 'name', ['where' => ['id' => base64_decode($id), 'is_delete' => 0]], ['single' => true]);
        if (!empty($contact)) {
            $data['contact'] = $contact;
            $data['perArr'] = checkPrivileges('accounts_communication');
            $data['title'] = 'Extracredit | Contact Communication';
            $data['id'] = $id;
            $this->template->load('default', 'contacts/list_communication', $data);
        } else {
            show_404();
        }
    }

    /**
     * Get Contacts communication data for ajax table
     * @author KU
     * */
    public function get_contacts_communication($id) {
        checkPrivileges('accounts_communication', 'view');
        $id = base64_decode($id);
        $final['recordsFiltered'] = $final['recordsTotal'] = $this->contacts_model->get_contacts_communication('count', $id);
        $final['redraw'] = 1;
        $contacts_com = $this->contacts_model->get_contacts_communication('result', $id);
        $start = $this->input->get('start') + 1;

        foreach ($contacts_com as $key => $val) {
            $contacts_com[$key] = $val;
            $contacts_com[$key]['sr_no'] = $start;
            $contacts_com[$key]['created'] = date('m/d/Y', strtotime($val['created']));
            $contacts_com[$key]['follow_up_date'] = ($val['follow_up_date'] != '') ? date('m/d/Y', strtotime($val['follow_up_date'])) : '';
            $contacts_com[$key]['communication_date'] = ($val['communication_date'] != '') ? date('m/d/Y', strtotime($val['communication_date'])) : '';
            $start++;
        }
        $final['data'] = $contacts_com;
        echo json_encode($final);
    }

    /**
     * Get Contact communication data by its ID 
     * @author KU
     * */
    public function get_communication_by_id() {
        $id = $this->input->post('id');
        $id = base64_decode($id);
        $contact_communication = $this->contacts_model->sql_select(TBL_COMMUNICATIONS, '*', ['where' => ['id' => $id, 'is_delete' => 0, 'type' => 5]], ['single' => true]);
        if (!empty($contact_communication)) {
            $contact_communication['follow_up_date'] = ($contact_communication['follow_up_date'] != '') ? date('m/d/Y', strtotime($contact_communication['follow_up_date'])) : '';
            $contact_communication['communication_date'] = ($contact_communication['communication_date'] != '') ? date('m/d/Y', strtotime($contact_communication['communication_date'])) : '';
        }
        echo json_encode($contact_communication);
    }

    /**
     * Add contact communication data
     * @param type $contact_id
     * @param type $comm_id
     * @author KU
     */
    public function add_communication($contact_id = null, $comm_id = null) {
        if (!is_null($contact_id))
            $contact_id = base64_decode($contact_id);

        $data['contact'] = $this->contacts_model->sql_select(TBL_CONTACTS, 'id', ['where' => ['id' => $contact_id]], ['single' => true]);
        $comm_id = base64_decode($comm_id);
        if (is_numeric($comm_id)) {
            checkPrivileges('accounts_communication', 'edit');
            $contact_communication = $this->contacts_model->sql_select(TBL_COMMUNICATIONS, '*', ['where' => ['id' => $comm_id, 'type' => 5, 'is_delete' => 0]], ['single' => true]);
            $data['contact_communication'] = $contact_communication;
            $data['title'] = 'Extracredit | Edit Communication';
            $data['heading'] = 'Edit Communication';
            if ($contact_communication['media'] != '')
                $media = $contact_communication['media'];
            else
                $media = NULL;
        } else {
            checkPrivileges('accounts_communication', 'add');
            $media = NULL;
            $data['title'] = 'Extracredit | Add Communication';
            $data['heading'] = 'Add Communication';
            $data['cities'] = [];
            $data['accounts'] = [];
        }
        $this->form_validation->set_rules('note', 'Note', 'trim|required');
        if ($this->form_validation->run() == TRUE) {
            $flag = 0;
            if ($_FILES['media']['name'] != '') {
                $image_data = upload_communication('media', COMMUNICATION_IMAGES);
                if (is_array($image_data)) {
                    $flag = 1;
                    $data['media_validation'] = $image_data['errors'];
                } else {
                    if ($media != '') {
                        unlink(COMMUNICATION_IMAGES . $media);
                    }
                    $media = $image_data;
                }
            }

            if ($flag == 0) {
                $dataArr = array(
                    'type' => 5,
                    'communication_date' => ($this->input->post('communication_date') != '') ? date('Y-m-d', strtotime($this->input->post('communication_date'))) : NULL,
                    'subject' => $this->input->post('subject'),
                    'follow_up_date' => ($this->input->post('follow_up_date') != '') ? date('Y-m-d', strtotime($this->input->post('follow_up_date'))) : NULL,
                    'donor_id' => 0,
                    'guest_id' => 0,
                    'account_id' => 0,
                    'vendor_id' => 0,
                    'contact_id' => $contact_id,
                    'note' => $this->input->post('note'),
                    'media' => $media
                );

                if (is_numeric($comm_id)) {
                    $dataArr['modified'] = date('Y-m-d H:i:s');
                    $this->contacts_model->common_insert_update('update', TBL_COMMUNICATIONS, $dataArr, ['id' => $comm_id]);
                    $this->session->set_flashdata('success', 'Contact communication details has been updated successfully.');
                } else {
                    $dataArr['created'] = date('Y-m-d H:i:s');
                    $communication_id = $this->contacts_model->common_insert_update('insert', TBL_COMMUNICATIONS, $dataArr);
                    if (!empty($this->input->post('follow_up_date'))) {
                        $communication_ManagerArr = array(
                            'communication_id' => $communication_id,
                            'user_id' => $this->session->userdata('extracredit_user')['id'],
                            'category' => 'Contact',
                            'follow_up_date' => date('Y-m-d', strtotime($this->input->post('follow_up_date'))),
                        );
                        $this->communication_manager_model->common_insert_update('insert', TBL_COMMUNICATIONS_MANAGER, $communication_ManagerArr);
                    }
                    $this->session->set_flashdata('success', 'Contact communication has been added successfully');
                }
                redirect('contacts/communication/' . base64_encode($contact_id));
            }
        }
        $this->template->load('default', 'contacts/add_communication', $data);
    }

    /**
     * Delete contact communication
     * @param int $contact_id Contact Id
     * @param int $id Communication Id
     * @author KU
     */
    public function delete_communication($contact_id = null, $id = NULL) {
        checkPrivileges('accounts_communication', 'delete');
        $id = base64_decode($id);
        if (is_numeric($id)) {
            $contact_communication = $this->contacts_model->sql_select(TBL_COMMUNICATIONS, '*', ['where' => ['id' => $id, 'type' => 5, 'is_delete' => 0]], ['single' => true]);
            if ($contact_communication) {
                $update_array = array(
                    'is_delete' => 1
                );

                $this->contacts_model->common_insert_update('update', TBL_COMMUNICATIONS, $update_array, ['id' => $id, 'type' => 5]);
                $this->session->set_flashdata('success', 'Contact communication has been deleted successfully!');
            } else {
                $this->session->set_flashdata('error', 'Invalid request. Please try again!');
            }
            redirect('contacts/communication/' . $contact_id);
        } else {
            show_404();
        }
    }

    public function testing()
    {
        $this->load->library('Mailin');
        $mailin = new Mailin('https://api.sendinblue.com/v2.0','VGcJrUg9ypYRjExh',50000);    //Optional parameter: Timeout in MS
      
        $data = array( "email" => "sm@narola.email",
        "attributes" => array("FIRSTNAME"=>"name", "LASTNAME"=>"surname"),
        "listid" => array(2)
        );

        var_dump($mailin->create_update_user($data));   
    }

}

/* End of file Contacts.php */
/* Location: ./application/controllers/Contacts.php */