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
        $this->load->model('communication_manager_model');
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
        $data['contacts'] = [];
        if (!is_null($id))
            $id = base64_decode($id);
        if (is_numeric($id)) {
            $vendor = $this->vendors_model->get_vendor_details($id);
            if ($vendor) {
                $data['vendor'] = $vendor;
                $data['title'] = 'Extracredit | Edit Vendor';
                $data['heading'] = 'Edit Vendor';
                $data['contacts'] = $this->vendors_model->sql_select(TBL_ASSOCIATED_CONTACTS, 'id,name,email,phone', ['where' => ['is_delete' => 0, 'type' => 'vendor', 'associated_id' => $id]]);
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
//        $this->form_validation->set_rules('contact_name', 'Contact Name', 'trim|required');

        if ($this->form_validation->run() == TRUE) {
            //-- Get state id from post value
            $state_id = $city_id = NULL;

            $state_code = $this->input->post('state_short');
            if (!empty($state_code)) {
                $post_city = $this->input->post('city_id');
                $state = $this->vendors_model->sql_select(TBL_STATES, 'id', ['where' => ['short_name' => $state_code]], ['single' => true]);
                if (empty($state) && $this->input->post('state_id') != '') {
                    $state = $this->vendors_model->sql_select(TBL_STATES, 'id', ['where' => ['name' => $this->input->post('state_id')]], ['single' => true]);
                }
                if (!empty($state)) {
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
            }

            $dataArr = array(
                'name' => trim($this->input->post('name')),
                'address' => trim($this->input->post('address')),
                'city_id' => $city_id,
                'state_id' => $state_id,
                'zip' => $this->input->post('zip'),
                'phone' => $this->input->post('phone'),
                'website' => trim($this->input->post('website')),
                'created' => date('Y-m-d H:i:s')
            );

            require_once(APPPATH . "libraries/Mailin.php");
            $mailin = new Mailin('https://api.sendinblue.com/v2.0', 'VGcJrUg9ypYRjExh', 50000);    //Optional parameter: Timeout in MS
            //Api Key(v2.0) : VGcJrUg9ypYRjExh

            if ($this->input->post('is_subscribed') == 1 && $this->input->post('is_subscribed') != '') {
                $dataArr['is_subscribed'] = 1; //insert in guest table
            } else {
                $dataArr['is_subscribed'] = 0; //update in guest table
            }

            if (is_numeric($id)) {
                $vendor_id = $id;
                $dataArr['modified'] = date('Y-m-d H:i:s');
                $this->vendors_model->common_insert_update('update', TBL_VENDORS, $dataArr, ['id' => $id]);
                $this->session->set_flashdata('success', 'Vendor details has been updated successfully.');
            } else {
                $dataArr['created'] = date('Y-m-d H:i:s');
                $vendor_id = $this->vendors_model->common_insert_update('insert', TBL_VENDORS, $dataArr);
                $this->session->set_flashdata('success', 'Vendor details has been added successfully');
            }

            //-- Delete old contact data
            $this->vendors_model->common_delete(TBL_ASSOCIATED_CONTACTS, ['associated_id' => $vendor_id, 'type' => 'vendor']);

            $contact_names = $this->input->post('contact_name');
            $contact_arr = [];
            foreach ($contact_names as $key => $name) {
                $email = null;
                if ($this->input->post('contact_email')[$key] != '') {
                    $email = $this->input->post('contact_email')[$key];
                }
                $arr = ['name' => $name, 'email' => $email, 'phone' => $this->input->post('contact_phone')[$key], 'type' => 'vendor', 'associated_id' => $vendor_id, 'created' => date('Y-m-d H:i:s')];
                $contact_arr[] = $arr;
                //-- Insert account email into sendinblue subscriber list
                if ($this->input->post('is_subscribed') == 1 && $this->input->post('is_subscribed') != '') {
                    $data = array("email" => $email,
                        "attributes" => array("FIRSTNAME" => $name, "LASTNAME" => ""),
                        "listid" => array(7)
                    );
                    $mailin->create_update_user($data);
                } else {
                    $data = array("email" => $email,
                        "listid_unlink" => array(7)
                    );
                    $mailin->create_update_user($data);
                }
            }

            if (!empty($contact_arr)) {
                $this->vendors_model->batch_insert_update('insert', TBL_ASSOCIATED_CONTACTS, $contact_arr);
            }
            if (isset($_POST['save_add_another'])) {
                redirect('vendors/add');
            } else {
                redirect('vendors');
            }
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
                //Remove Subscribed user from SendInBlue.com
                require_once(APPPATH . "libraries/Mailin.php");
                $mailin = new Mailin('https://api.sendinblue.com/v2.0', 'VGcJrUg9ypYRjExh', 50000);    //Optional parameter: Timeout in MS
                $data = array("email" => $vendor['email'],
                    "listid_unlink" => array(7)
                );
                $mailin->create_update_user($data);

                $update_array = array(
                    'is_delete' => 1,
                    'is_subscribed' => 0
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
        $where = ['name' => trim($this->input->get('name')), 'is_delete' => 0];
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

    /**
     * Import vendor data from CSV file
     * @author KU
     */
    public function import_vendor() {

        checkPrivileges('account', 'add');
        $fileDirectory = VENDOR_CSV;
        $config['overwrite'] = FALSE;
        $config['remove_spaces'] = TRUE;
        $config['upload_path'] = VENDOR_CSV;
        $config['allowed_types'] = 'csv|CSV';
        $this->load->library('upload', $config);

        //-- Upload csv file
        if ($this->upload->do_upload('import_vendor')) {
            $fileDetails = $this->upload->data();

            //-- cities array
            $cities = $this->vendors_model->sql_select(TBL_CITIES);
            foreach ($cities as $city) {
                $cities_arr[$city['id']] = $city['name'];
                $states_arr[$city['id']] = $city['state_id'];
            }
            $cities_arr = array_map('strtolower', $cities_arr);

            //-- vendor email array
            $vendor_emails = $this->vendors_model->sql_select(TBL_VENDORS, 'email', ['where' => ['is_delete' => 0, 'email!=' => '']]);
            $vendor_emails_arr = array_column($vendor_emails, 'email');
            $vendor_emails_arr = array_map('strtolower', $vendor_emails_arr);


            $row = 1;
            $handle = fopen($fileDirectory . "/" . $fileDetails['file_name'], "r");

            $vendor_data = $check_email_valid = $check_email = $check_city = $imported_emails = [];
            if (($data2 = fgetcsv($handle)) !== FALSE) {
                $data_format2 = array('name', 'contactname', 'email', 'address', 'zip', 'city', 'phone', 'website');

                //-- check if first colums is according to predefined row
                if ($data_format2 == $data2) {
                    while (($col_data = fgetcsv($handle)) !== FALSE) {
                        $vendor = [];
                        if (empty($col_data[0])) {
                            fclose($handle);
                            $this->session->set_flashdata('error', 'Vendor name is missing in Row No. ' . $row);
                            redirect('vendors');
                        } elseif ($col_data[1] == '') {
                            fclose($handle);
                            $this->session->set_flashdata('error', 'Contact name is missing in Row No. ' . $row);
                            redirect('vendors');
                        } else {

                            $vendor['name'] = $col_data[0];
                            $vendor['contact_name'] = $col_data[1];

                            //--check email is unique or not
                            if (!empty($col_data[2])) {
                                if (!filter_var($col_data[2], FILTER_VALIDATE_EMAIL)) {
                                    $check_email_valid[] = $row;
                                } else if (array_search(strtolower($col_data[2]), $vendor_emails_arr) != FALSE) {
                                    $check_email[] = $row;
                                } else {
                                    $vendor['email'] = $col_data[2];
                                }
                            } else {
                                $vendor['email'] = NULL;
                            }

                            $imported_emails[] = $col_data[2];
                            $vendor['address'] = $col_data[3];
                            $vendor['zip'] = $col_data[4];

                            //--check city is valid or not
                            if (!empty($col_data[5])) {
                                if (array_search(strtolower($col_data[5]), $cities_arr) != FALSE) {
                                    $vendor['city_id'] = array_search(strtolower($col_data[5]), $cities_arr);
                                    $vendor['state_id'] = $states_arr[$vendor['city_id']];
                                } else {
                                    $check_city[] = $row;
                                }
                            } else {
                                $vendor['city_id'] = NULL;
                                $vendor['state_id'] = NULL;
                            }

                            $vendor['phone'] = $col_data[6];
                            $vendor['website'] = $col_data[7];
                            $vendor_data[] = $vendor;
                            $row++;
                        }
                    }
                    //- check if email is valid or not
                    if (count(array_unique($imported_emails)) != count($imported_emails)) { //-- check emails in column are unique or not
                        fclose($handle);
                        $this->session->set_flashdata('error', "Duplicate value in email column.");
                    } else if (!empty($check_email_valid)) { //-- check Email is valid or not
                        $rows = implode(',', $check_email_valid);
                        $this->session->set_flashdata('error', "Donor's Email is not in valid format. Please check entries at row number - " . $rows);
                    } else if (!empty($check_email)) { //-- check Account/Program in columns are valid or not
                        $rows = implode(',', $check_email);
                        $this->session->set_flashdata('error', "Account's Email already exist in the system. Please check entries at row number - " . $rows);
                    } else if (!empty($check_city)) { //-- check city in column are unique or not
                        $rows = implode(',', $check_city);
                        $this->session->set_flashdata('error', "City doesn't exist in the system. Please check entries at row number - " . $rows);
                    } else {
                        if (!empty($vendor_data)) {
                            //-- Insert vendor details into database
                            foreach ($vendor_data as $val) {
                                $vendor_arr = [
                                    'name' => $val['name'],
                                    'address' => $val['address'],
                                    'city_id' => $val['city_id'],
                                    'state_id' => $val['state_id'],
                                    'zip' => $val['zip'],
                                    'phone' => $val['phone'],
                                    'website' => $val['website'],
                                    'created' => date('Y-m-d H:i:s')
                                ];
                                $vendor_id = $this->vendors_model->common_insert_update('insert', TBL_VENDORS, $vendor_arr);

                                $associated_contact = ['name' => $val['contact_name'], 'email' => $val['email'], 'phone' => $val['phone'], 'type' => 'vendor', 'associated_id' => $vendor_id, 'created' => date('Y-m-d H:i:s')];
                                $this->vendors_model->common_insert_update('insert', TBL_ASSOCIATED_CONTACTS, $associated_contact);
                            }
                            $this->session->set_flashdata('success', "CSV file imported successfully! Vendor data added successfully");
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
            redirect('vendors');
        } else {
            $this->session->set_flashdata('error', strip_tags($this->upload->display_errors()));
            redirect('vendors');
        }
    }

    /**
     * Listing of All Vendor's communication
     * @author KU
     */
    public function communication($id = null) {
        checkPrivileges('account', 'view');
        $vendor = $this->vendors_model->sql_select(TBL_VENDORS, 'name', ['where' => ['id' => base64_decode($id), 'is_delete' => 0]], ['single' => true]);
        if (!empty($vendor)) {
            $data['vendor'] = $vendor;
            $data['perArr'] = checkPrivileges('accounts_communication');
            $data['title'] = 'Extracredit | Vendor Communication';
            $data['id'] = $id;
            $this->template->load('default', 'vendors/list_communication', $data);
        } else {
            show_404();
        }
    }

    /**
     * Get Vendors communication data for ajax table
     * @author REP
     * */
    public function get_vendors_communication($id) {
        checkPrivileges('accounts_communication', 'view');
        $id = base64_decode($id);
        $final['recordsFiltered'] = $final['recordsTotal'] = $this->vendors_model->get_vendors_communication('count', $id);
        $final['redraw'] = 1;
        $vendors = $this->vendors_model->get_vendors_communication('result', $id);
        $start = $this->input->get('start') + 1;

        foreach ($vendors as $key => $val) {
            $vendors[$key] = $val;
            $vendors[$key]['sr_no'] = $start;
            $vendors[$key]['created'] = date('m/d/Y', strtotime($val['created']));
            $vendors[$key]['follow_up_date'] = ($val['follow_up_date'] != '') ? date('m/d/Y', strtotime($val['follow_up_date'])) : '';
            $vendors[$key]['communication_date'] = ($val['communication_date'] != '') ? date('m/d/Y', strtotime($val['communication_date'])) : '';
            $start++;
        }
        $final['data'] = $vendors;
        echo json_encode($final);
    }

    /**
     * Get Vendors communication data by its ID 
     * @author KU
     * */
    public function get_communication_by_id() {
        $id = $this->input->post('id');
        $id = base64_decode($id);
        $vendor_communication = $this->vendors_model->sql_select(TBL_COMMUNICATIONS, '*', ['where' => ['id' => $id, 'is_delete' => 0, 'type' => 4]], ['single' => true]);
        if (!empty($vendor_communication)) {
            $vendor_communication['follow_up_date'] = ($vendor_communication['follow_up_date'] != '') ? date('m/d/Y', strtotime($vendor_communication['follow_up_date'])) : '';
            $vendor_communication['communication_date'] = ($vendor_communication['communication_date'] != '') ? date('m/d/Y', strtotime($vendor_communication['communication_date'])) : '';
        }
        echo json_encode($vendor_communication);
    }

    /**
     * Add vendor communication data
     * @param int $vendor_id
     * @param int $comm_id
     */
    public function add_communication($vendor_id = null, $comm_id = null) {
        if (!is_null($vendor_id))
            $vendor_id = base64_decode($vendor_id);

        $data['vendor'] = $this->vendors_model->sql_select(TBL_VENDORS, 'id', ['where' => ['id' => $vendor_id]], ['single' => true]);
        $data['contacts'] = $this->vendors_model->sql_select(TBL_ASSOCIATED_CONTACTS, 'id,name', ['where' => ['associated_id' => $vendor_id, 'type' => 'vendor', 'is_delete' => 0]]);
        $comm_id = base64_decode($comm_id);
        if (is_numeric($comm_id)) {
            checkPrivileges('accounts_communication', 'edit');
            $vendor_communication = $this->vendors_model->sql_select(TBL_COMMUNICATIONS, '*', ['where' => ['id' => $comm_id, 'type' => 4, 'is_delete' => 0]], ['single' => true]);
            $data['vendor_communication'] = $vendor_communication;
            $data['title'] = 'Extracredit | Edit Communication';
            $data['heading'] = 'Edit Communication';
            if ($vendor_communication['media'] != '')
                $media = $vendor_communication['media'];
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
                    'type' => 4,
                    'communication_date' => ($this->input->post('communication_date') != '') ? date('Y-m-d', strtotime($this->input->post('communication_date'))) : NULL,
                    'subject' => $this->input->post('subject'),
                    'follow_up_date' => ($this->input->post('follow_up_date') != '') ? date('Y-m-d', strtotime($this->input->post('follow_up_date'))) : NULL,
                    'donor_id' => 0,
                    'guest_id' => 0,
                    'account_id' => 0,
                    'vendor_id' => $vendor_id,
                    'associated_contact_id' => $this->input->post('associated_contact_id'),
                    'note' => $this->input->post('note'),
                    'media' => $media
                );

                if (is_numeric($comm_id)) {
                    $dataArr['modified'] = date('Y-m-d H:i:s');
                    $this->vendors_model->common_insert_update('update', TBL_COMMUNICATIONS, $dataArr, ['id' => $comm_id]);
                    $this->session->set_flashdata('success', 'Vendor communication details has been updated successfully.');
                } else {
                    $dataArr['created'] = date('Y-m-d H:i:s');
                    $communication_id = $this->vendors_model->common_insert_update('insert', TBL_COMMUNICATIONS, $dataArr);
                    if (!empty($this->input->post('follow_up_date'))) {
                        $communication_ManagerArr = array(
                            'communication_id' => $communication_id,
                            'user_id' => $this->session->userdata('extracredit_user')['id'],
                            'category' => 'Vendor',
                            'follow_up_date' => date('Y-m-d', strtotime($this->input->post('follow_up_date'))),
                        );
                        $this->communication_manager_model->common_insert_update('insert', TBL_COMMUNICATIONS_MANAGER, $communication_ManagerArr);
                    }
                    $this->session->set_flashdata('success', 'Vendor communication has been added successfully');
                }
                redirect('vendors/communication/' . base64_encode($vendor_id));
            }
        }
        $this->template->load('default', 'vendors/add_communication', $data);
    }

    /**
     * Delete vendor communication 
     * @param int $vendor_id - Vendor Id
     * @param int $id - Communication Id
     * @author KU
     */
    public function delete_communication($vendor_id = null, $id = NULL) {
        checkPrivileges('accounts_communication', 'delete');
        $id = base64_decode($id);
        if (is_numeric($id)) {
            $vendor_communication = $this->vendors_model->sql_select(TBL_COMMUNICATIONS, '*', ['where' => ['id' => $id, 'type' => 4, 'is_delete' => 0]], ['single' => true]);
            if ($vendor_communication) {
                $update_array = array(
                    'is_delete' => 1
                );

                $this->vendors_model->common_insert_update('update', TBL_COMMUNICATIONS, $update_array, ['id' => $id, 'type' => 4]);
                $this->session->set_flashdata('success', 'Vendor communication has been deleted successfully!');
            } else {
                $this->session->set_flashdata('error', 'Invalid request. Please try again!');
            }
            redirect('vendors/communication/' . $vendor_id);
        } else {
            show_404();
        }
    }

}

/* End of file Vendors.php */
/* Location: ./application/controllers/Vendors.php */