<?php

/**
 * Manage donors table related database operations
 * @author KU
 */
class Donors_model extends MY_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get donors for datatable
     * @param string $type - Either result or count
     * @return array for result or int for count
     */
    public function get_donors($type = 'result') {
        $columns = ['d.firstname', 'd.lastname', 'd.email', 'd.phone', 'd.amount', 'd.created', 'd.is_active'];
        $keyword = $this->input->get('search');
        $this->db->select('d.*');

        if (!empty($keyword['value'])) {
            $this->db->where('(d.firstname LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR d.lastname LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR email LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR d.phone LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
        }

        $this->db->where(['d.is_delete' => 0]);
        $this->db->order_by($columns[$this->input->get('order')[0]['column']], $this->input->get('order')[0]['dir']);
        if ($type == 'result') {
            $this->db->limit($this->input->get('length'), $this->input->get('start'));
            $query = $this->db->get(TBL_DONORS . ' d');
            return $query->result_array();
        } else {
            $query = $this->db->get(TBL_DONORS . ' d');
            return $query->num_rows();
        }
    }

    /**
     * Get account details of particular id
     * @param int $id
     */
    public function get_donor_details($id) {
        $this->db->select('d.*,s.name as state,c.name as city,s.short_name as state_short');
        $this->db->join(TBL_STATES . ' as s', 'd.state_id=s.id', 'left');
        $this->db->join(TBL_CITIES . ' as c', 'd.city_id=c.id', 'left');
        $this->db->where(['d.id' => $id, 'd.is_delete' => 0]);
        $query = $this->db->get(TBL_DONORS . ' d');
        return $query->row_array();
    }

    /**
     * Get donor communication for datatable
     * @param string $type - Either result or count
     * @return array for result or int for count
     */
    public function get_donors_communication($type = 'result', $id) {
        $columns = ['id', 'c.subject', 'c.communication_date', 'c.follow_up_date', 'c.note', 'c.media', 'c.created'];
        $keyword = $this->input->get('search');
        $this->db->select('c.*');

        if (!empty($keyword['value'])) {
            $this->db->where('(note LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
        }
        $this->db->where(['c.is_delete' => 0]);
        $this->db->where(['c.donor_id' => $id]);
        $this->db->where(['c.type' => 1]);
        $this->db->order_by($columns[$this->input->get('order')[0]['column']], $this->input->get('order')[0]['dir']);
        if ($type == 'result') {
            $this->db->limit($this->input->get('length'), $this->input->get('start'));
            $query = $this->db->get(TBL_COMMUNICATIONS . ' c');
            return $query->result_array();
        } else {
            $query = $this->db->get(TBL_COMMUNICATIONS . ' c');
            return $query->num_rows();
        }
    }

    /**
     * Get donor communication details of particular id
     * @param type $id
     */
    public function get_donor_communication_details($id) {
        $this->db->select('dc.*,dc.id as conversation_id,d.firstname,d.lastname');
        $this->db->join(TBL_DONORS . ' as d', 'd.id=dc.donor_id', 'left');
        $this->db->where(['dc.id' => $id, 'dc.is_delete' => 0]);
        $query = $this->db->get(TBL_COMMUNICATIONS . ' dc');
        return $query->row_array();
    }

    /**
     * To generate donors report
     * @param type $type
     * @return type
     */
    public function get_donors_reports($type = 'result') {
        $columns = ['fund_type', 'action_matters_campaign,vendor_name', 'fu.date', 'fu.post_date', 'id', 'd.firstname', 'd.lastname', 'd.address', 'state', 'city', 'd.zip', 'd.email', 'd.amount', 'd.refund', 'p.type', 'fu.payment_number', 'fu.memo'];
        $keyword = $this->input->get('search');
        $this->db->select('d.*,fu.date,fu.post_date,fu.payment_type_id,fu.payment_number,fu.memo,f.type as fund_type,a.action_matters_campaign,a.vendor_name,f.name as fund_type,c.name as city,s.name as state,f.type,p.type as payment_type');

        $this->db->join(TBL_DONORS . ' as d', 'fu.donor_id=d.id', 'left');
        $this->db->join(TBL_ACCOUNTS . ' as a', 'fu.account_id=a.id', 'left');
        $this->db->join(TBL_FUND_TYPES . ' as f', 'a.fund_type_id=f.id', 'left');
        $this->db->join(TBL_CITIES . ' as c', 'd.city_id=c.id', 'left');
        $this->db->join(TBL_STATES . ' as s', 'd.state_id=s.id', 'left');
        $this->db->join(TBL_PAYMENT_TYPES . ' as p', 'fu.payment_type_id=p.id', 'left');

        if (!empty($keyword['value'])) {
            $this->db->where('(f.type LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR action_matters_campaign LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR vendor_name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR d.firstname LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR d.lastname LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR d.email LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR c.name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR p.type LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR f.type LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
        }

        $post_date_filter = $this->input->get('post_date_filter');
        if ($post_date_filter != '') {
            $dates = explode('-', $post_date_filter);
            $startdate = date('Y-m-d', strtotime($dates[0]));
            $enddate = date('Y-m-d', strtotime($dates[1]));
            $this->db->where('fu.post_date >=', $startdate);
            $this->db->where('fu.post_date <=', $enddate);
        }

        $this->db->where(['a.is_delete' => 0, 'd.is_delete' => 0, 'fu.is_delete' => 0]);
        $this->db->order_by($columns[$this->input->get('order')[0]['column']], $this->input->get('order')[0]['dir']);
        if ($type == 'result') {
            $this->db->limit($this->input->get('length'), $this->input->get('start'));
            $query = $this->db->get(TBL_FUNDS . ' fu');
            return $query->result_array();
        } else {
            $query = $this->db->get(TBL_FUNDS . ' fu');
            return $query->num_rows();
        }
    }

    /**
     * Get all accounts 
     */
    public function get_all_accounts() {
        $this->db->select('f.name as fund_type,a.id,a.fund_type_id,a.action_matters_campaign,a.vendor_name,f.type');
        $this->db->join(TBL_FUND_TYPES . ' as f', 'a.fund_type_id=f.id', 'left');
        $this->db->where(['a.is_delete' => 0]);
        $query = $this->db->get(TBL_ACCOUNTS . ' a');
        return $query->result_array();
    }

    /**
     * Get donor details of particular id
     * @param int $id
     */
    public function get_donor_details_view($id) {
        $this->db->select('d.*,c.name as cityname, s.name as statename');
        $this->db->join(TBL_CITIES . ' as c', 'd.city_id=c.id', 'left');
        $this->db->join(TBL_STATES . ' as s', 'd.state_id=s.id', 'left');
        $this->db->where(['d.id' => $id, 'd.is_delete' => 0]);
        $query = $this->db->get(TBL_DONORS . ' d');
        return $query->row_array();
    }

    /**
     * Get donor's donations for datatable
     * @param string $type Either result or count
     * @param int $id - Id of donor
     * @return array for result or int for count
     * @author KU
     */
    public function get_donations($type = 'result', $id) {
        $columns = ['a.action_matters_campaign,a.vendor_name', 'amount', 'f.date', 'f.post_date', 'f.payment_number', 'p.type', 'f.memo', 'f.is_delete'];
        $keyword = $this->input->get('search');
        $this->db->select('f.*,(f.admin_fund+f.account_fund) as amount,p.type as payment_type,a.action_matters_campaign,a.vendor_name,ft.type');
        $this->db->join(TBL_PAYMENT_TYPES . ' as p', 'f.payment_type_id=p.id AND p.is_delete=0', 'left');
        $this->db->join(TBL_ACCOUNTS . ' as a', 'f.account_id=a.id AND a.is_delete=0', 'left');
        $this->db->join(TBL_FUND_TYPES . ' as ft', 'a.fund_type_id=ft.id AND ft.is_delete=0', 'left');

        if (!empty($keyword['value'])) {
            $this->db->where('(a.action_matters_campaign LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ''
                    . 'OR a.vendor_name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ''
                    . 'OR (f.admin_fund+f.account_fund) LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ''
                    . 'OR f.date LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ''
                    . 'OR f.post_date LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ''
                    . 'OR f.payment_number LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ''
                    . 'OR f.memo LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ''
                    . ')');
        }
        $this->db->where(['f.is_delete' => 0, 'f.donor_id' => $id]);
        $this->db->order_by($columns[$this->input->get('order')[0]['column']], $this->input->get('order')[0]['dir']);
        if ($type == 'result') {
            $this->db->limit($this->input->get('length'), $this->input->get('start'));
            $query = $this->db->get(TBL_FUNDS . ' f');
            return $query->result_array();
        } else {
            $query = $this->db->get(TBL_FUNDS . ' f');
            return $query->num_rows();
        }
    }

    /**
     * Get donation detail
     * @param int $id
     * @author KU
     */
    public function get_donation_details($id) {
        $this->db->select('f.*,a.action_matters_campaign,a.vendor_name,a.fund_type_id,ft.type,(f.admin_fund+f.account_fund) as amount');
        $this->db->join(TBL_ACCOUNTS . ' as a', 'f.account_id=a.id', 'left');
        $this->db->join(TBL_FUND_TYPES . ' as ft', 'a.fund_type_id=ft.id AND ft.is_delete=0', 'left');
        $this->db->where(['f.id' => $id, 'f.is_delete' => 0]);
        $query = $this->db->get(TBL_FUNDS . ' f');
        return $query->row_array();
    }

    /**
     * Get donor's donations
     * @param int $id
     */
    public function get_donor_donations($id = NULL, $type) {
        if ($type == 'group')
            $this->db->select('f.id,f.account_id,sum(f.admin_fund) as admin_fund,sum(f.account_fund) as account_fund,a.total_fund,a.action_matters_campaign');
        else
            $this->db->select('f.id,f.account_id,f.admin_fund,f.account_fund,a.total_fund,a.action_matters_campaign');

        $this->db->join(TBL_ACCOUNTS . ' as a', 'f.account_id=a.id', 'left');
        $this->db->where(['f.donor_id' => $id, 'f.is_delete' => 0]);

        if ($type == 'group')
            $this->db->group_by('account_id,donor_id');

        $query = $this->db->get(TBL_FUNDS . ' f');
        return $query->result_array();
    }

}
