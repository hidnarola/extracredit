<?php

/**
 * Manage donors table related database operations
 * @author REP
 */
class Guests_model extends MY_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get donors for datatable
     * @param string $type - Either result or count
     * @return array for result or int for count
     */
    public function get_guests($type = 'result') {
        $columns = ['logo', 'g.firstname', 'g.lastname', 'g.email', 'g.guest_date', 'g.AIR_date', 'g.AMC_active', 'g.created'];
        $keyword = $this->input->get('search');
        $this->db->select('g.*,c.name as city');

        $this->db->join(TBL_CITIES . ' as c', 'g.city_id=c.id', 'left');

        if (!empty($keyword['value'])) {
            $this->db->where('(g.firstname LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR g.lastname LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
//                    ' OR g.companyname LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR g.email LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR g.phone LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR g.AMC_active LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ')');
        }

        $this->db->where(['g.is_delete' => 0]);
//        if ($this->input->get('order')) {
        $this->db->order_by($columns[$this->input->get('order')[0]['column']], $this->input->get('order')[0]['dir']);
//        }
        if ($type == 'result') {
            $this->db->limit($this->input->get('length'), $this->input->get('start'));
            $query = $this->db->get(TBL_GUESTS . ' g');
//            qry();
            return $query->result_array();
        } else {
            $query = $this->db->get(TBL_GUESTS . ' g');
            return $query->num_rows();
        }
    }

    /**
     * Get guest details of particular id
     * @param int $id
     */
    public function get_guest_details($id) {
        $this->db->select('g.*,a.fund_type_id,c.name as city,s.name as state,s.short_name as state_short');
        $this->db->join(TBL_ACCOUNTS . ' as a', 'g.account_id=a.id', 'left');
        $this->db->join(TBL_CITIES . ' as c', 'g.city_id=c.id', 'left');
        $this->db->join(TBL_STATES . ' as s', 'g.state_id=s.id', 'left');
        $this->db->where(['g.id' => $id, 'g.is_delete' => 0]);
        $query = $this->db->get(TBL_GUESTS . ' g');
        return $query->row_array();
    }

    /**
     * Check email exist or not for unique email
     * @param string $email
     * @return array
     */
    public function check_unique_email($email) {
        $this->db->where('email', $email);
        $this->db->where('is_delete', 0);
        $query = $this->db->get(TBL_GUESTS);
        return $query->row_array();
    }

    /**
     * At edit time check email unique
     * @param type $email
     * @param type $id
     * @return int
     */
    function check_email_edit($email, $id) {
        $this->db->select('email');
        $this->db->where('is_delete!=', 1);
        $this->db->where('email=', $email);
        $this->db->where('id!=', $id);
        $result = $this->db->get(TBL_GUESTS);
        if ($result->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Get guest communication for datatable
     * @param string $type - Either result or count
     * @return array for result or int for count
     */
    public function get_guests_communication($type = 'result', $id) {
        $columns = ['id', 'fullname', 'g.companyname', 'g.AMC_active', 'c.subject', 'c.communication_date', 'c.follow_up_date', 'c.note', 'c.media', 'c.created'];
        $keyword = $this->input->get('search');
        $this->db->select('c.*,CONCAT(g.firstname, " ",g.lastname) AS fullname,g.companyname,g.AMC_active');
        $this->db->join(TBL_GUESTS . ' as g', 'g.id=c.guest_id', 'left');
        if (!empty($keyword['value'])) {
            $this->db->where('(c.note LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
        }
        $this->db->where(['c.is_delete' => 0]);
        $this->db->where(['c.guest_id' => $id]);
        $this->db->where(['c.type' => 2]);
        $this->db->order_by($columns[$this->input->get('order')[0]['column']], $this->input->get('order')[0]['dir']);
        if ($type == 'result') {
            $this->db->limit($this->input->get('length'), $this->input->get('start'));
            $query = $this->db->get(TBL_COMMUNICATIONS . ' c');
//            echo $this->db->last_query();
//            p($query->result_array(),1);
            return $query->result_array();
        } else {
            $query = $this->db->get(TBL_COMMUNICATIONS . ' c');
            return $query->num_rows();
        }
    }

    /**
     * Get guest communication details of particular id
     * @param type $id
     */
    public function get_guest_communication_details($id) {
        $this->db->select('gc.*,gc.id as conversation_id,g.firstname,g.lastname');
        $this->db->join(TBL_GUESTS . ' as g', 'g.id=gc.guest_id', 'left');
        $this->db->where(['gc.id' => $id, 'gc.is_delete' => 0]);
        $query = $this->db->get(TBL_COMMUNICATIONS . ' gc');
        return $query->row_array();
    }

    /**
     * To generate donors report
     * @param type $type
     * @return type
     */
    public function get_guests_reports($type = 'result') {
        $columns = ['id', 'g.firstname', 'g.lastname', 'g.company_name', 'g.invite_date', 'g.guest_date', 'g.AIR_date', 'g.AMC_created', 'action_matters_campaign,vendor_name', 'g.address', 'city', 'state', 'g.zip', 'g.email', 'g.phone', 'g.assistant', 'g.assistant_phone', 'g.assistant_email'];
        $keyword = $this->input->post('search');
        $this->db->select('g.*,a.action_matters_campaign,a.vendor_name,c.name as city,s.name as state,f.type');

        $this->db->join(TBL_ACCOUNTS . ' as a', 'g.account_id=a.id', 'left');
        $this->db->join(TBL_FUND_TYPES . ' as f', 'a.fund_type_id=f.id', 'left');
        $this->db->join(TBL_CITIES . ' as c', 'g.city_id=c.id', 'left');
        $this->db->join(TBL_STATES . ' as s', 'g.state_id=s.id', 'left');

        if (!empty($keyword['value'])) {
            $this->db->where('(f.type LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR action_matters_campaign LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR vendor_name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR g.firstname LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR g.lastname LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR g.email LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR c.name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
        }

        $post_date_filter = $this->input->post('post_date_filter');
        if ($post_date_filter != '') {
            $dates = explode('-', $post_date_filter);
            $startdate = date('Y-m-d', strtotime($dates[0]));
            $enddate = date('Y-m-d', strtotime($dates[1]));
            $this->db->where('g.created >=', $startdate);
            $this->db->where('g.created <=', $enddate);
        }
        $this->db->where(['g.is_delete' => 0]);
        $this->db->order_by($columns[$this->input->post('order')[0]['column']], $this->input->post('order')[0]['dir']);
        if ($type == 'result') {
            $this->db->limit($this->input->post('length'), $this->input->post('start'));
            $query = $this->db->get(TBL_GUESTS . ' g');
            return $query->result_array();
        } else {
            $query = $this->db->get(TBL_GUESTS . ' g');
            return $query->num_rows();
        }
    }

    /**
     * Get guest details of particular id
     * @param int $id
     */
    public function get_guest_details_view($id) {
        $this->db->select('g.*,a.fund_type_id,f.type,f.name,a.action_matters_campaign,a.vendor_name,c.name as cityname, s.name as statename');
        $this->db->join(TBL_ACCOUNTS . ' as a', 'g.account_id=a.id', 'left');
        $this->db->join(TBL_FUND_TYPES . ' as f', 'f.id=a.fund_type_id', 'left');
        $this->db->join(TBL_CITIES . ' as c', 'g.city_id=c.id', 'left');
        $this->db->join(TBL_STATES . ' as s', 'g.state_id=s.id', 'left');
        $this->db->where(['g.id' => $id, 'g.is_delete' => 0]);
        $query = $this->db->get(TBL_GUESTS . ' g');
        return $query->row_array();
    }

    /**
     * Get all accounts 
     */
    public function get_all_accounts() {
        $this->db->select('f.name as fund_type,a.id,a.fund_type_id,a.action_matters_campaign,a.vendor_name,f.type');
        $this->db->join(TBL_FUND_TYPES . ' as f', 'a.fund_type_id=f.id', 'left');
        $this->db->where(['a.is_delete' => 0, 'f.is_delete' => 0]);
        $query = $this->db->get(TBL_ACCOUNTS . ' a');
        return $query->result_array();
    }

    /**
     * Check state is saved in database
     * @param type $state
     * @return int
     */
    public function check_state($state) {
        $this->db->select('*');
        $this->db->where('short_name=', $state);
        $result = $this->db->get(TBL_STATES);
        if ($result->num_rows() > 0) {
            return $result->row_array();
        } else {
            return 0;
        }
    }

    /**
     * check city is saved in database and if not save new city in database
     * @param type $city
     * @param type $state_id
     * @return type
     */
    public function check_city($city, $state_id) {
        $this->db->select('*');
        $this->db->where('name=', $city);
        $this->db->where('state_id=', $state_id);
        $result = $this->db->get(TBL_CITIES);
        if ($result->num_rows() > 0) {
            return $result->row_array();
        } else {
            $record_array = array(
                'name' => $city,
                'state_id' => $state_id
            );
            $this->db->insert(TBL_CITIES, $record_array);
            $insert_id = $this->db->insert_id(TBL_CITIES);
            $data = array('id' => $insert_id);
            return $data;
        }
    }

    /**
     * Get amc accounts which are having normal fund type 
     * @author KU 
     */
    public function get_amc_accounts() {
        $this->db->select('a.id,a.action_matters_campaign,a.vendor_name,f.type');
        $this->db->join(TBL_FUND_TYPES . ' as f', 'a.fund_type_id=f.id', 'left');
        $this->db->where(['a.is_delete' => 0, 'f.is_delete' => 0, 'f.type' => 0]);
        $query = $this->db->get(TBL_ACCOUNTS . ' a');
        return $query->result_array();
    }

}
