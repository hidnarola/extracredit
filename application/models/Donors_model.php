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
        $columns = ['id', 'action_matters_campaign,vendor_name', 'd.firstname', 'd.lastname', 'd.email', 'c.name', 'p.type', 'd.amount', 'd.created', 'd.is_active'];
        $keyword = $this->input->get('search');
        $this->db->select('d.*,a.action_matters_campaign,a.vendor_name,f.type as fund_type,c.name as city,f.is_vendor,p.type as payment_type');

        $this->db->join(TBL_ACCOUNTS . ' as a', 'd.account_id=a.id', 'left');
        $this->db->join(TBL_FUND_TYPES . ' as f', 'a.fund_type_id=f.id', 'left');
        $this->db->join(TBL_CITIES . ' as c', 'a.city_id=c.id', 'left');
        $this->db->join(TBL_PAYMENT_TYPES . ' as p', 'd.payment_type_id=p.id', 'left');

        if (!empty($keyword['value'])) {
            $this->db->where('(action_matters_campaign LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR vendor_name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR d.firstname LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR d.lastname LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR email LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR c.name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR p.type LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR f.type LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
        }

        $this->db->where(['a.is_delete' => 0, 'd.is_delete' => 0]);
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
        $this->db->select('d.*,a.fund_type_id,f.admin_fund,f.account_fund');
        $this->db->join(TBL_ACCOUNTS . ' as a', 'd.account_id=a.id', 'left');
        $this->db->join(TBL_FUNDS . ' as f', 'd.account_id=f.account_id AND d.id=f.donor_id AND f.is_delete=0', 'left');
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
        $columns = ['fund_type', 'action_matters_campaign,vendor_name', 'd.date', 'd.post_date', 'id', 'd.firstname', 'd.lastname', 'd.address', 'city', 'state', 'd.zip', 'd.email', 'd.amount', 'd.refund', 'p.type', 'd.payment_number', 'd.memo'];
        $keyword = $this->input->get('search');
        $this->db->select('d.*,f.type as fund_type,a.action_matters_campaign,a.vendor_name,f.type as fund_type,c.name as city,s.name as state,f.is_vendor,p.type as payment_type');

        $this->db->join(TBL_ACCOUNTS . ' as a', 'd.account_id=a.id', 'left');
        $this->db->join(TBL_FUND_TYPES . ' as f', 'a.fund_type_id=f.id', 'left');
        $this->db->join(TBL_CITIES . ' as c', 'd.city_id=c.id', 'left');
        $this->db->join(TBL_STATES . ' as s', 'd.state_id=s.id', 'left');
        $this->db->join(TBL_PAYMENT_TYPES . ' as p', 'd.payment_type_id=p.id', 'left');

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

        $this->db->where(['a.is_delete' => 0, 'd.is_delete' => 0]);
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
     * Get all accounts 
     */
    public function get_all_accounts() {
        $this->db->select('f.type as fund_type,a.id,a.fund_type_id,a.action_matters_campaign,a.vendor_name,f.is_vendor');
        $this->db->join(TBL_FUND_TYPES . ' as f', 'a.fund_type_id=f.id', 'left');
        $this->db->where(['a.is_delete' => 0]);
        $query = $this->db->get(TBL_ACCOUNTS . ' a');
        return $query->result_array();
    }

}
