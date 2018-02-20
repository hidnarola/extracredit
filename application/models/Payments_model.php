<?php

/**
 * Manage payments table related database operations
 * @author KU
 */
class Payments_model extends MY_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get accounts for datatable
     * @param string $type - Either result or count
     * @return array for result or int for count
     */
    public function get_payments($type = 'result') {
        $columns = ['f.type', 'a.action_matters_campaign,v.name', 'p.check_date', 'p.check_number', 'p.amount', 'p.created', 'p.is_delete'];
        $keyword = $this->input->get('search');
        $this->db->select('p.*,a.action_matters_campaign,v.name as vendor_name,f.name as fund_type,f.type');
        $this->db->join(TBL_ACCOUNTS . ' a', 'p.account_id=a.id AND p.payer="account"', 'left');
        $this->db->join(TBL_VENDORS . ' v', 'p.account_id=v.id AND p.payer="vendor"', 'left');
        $this->db->join(TBL_FUND_TYPES . ' f', 'a.fund_type_id=f.id', 'left');

        if (!empty($keyword['value'])) {
            $this->db->where('(a.action_matters_campaign LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR v.name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR p.check_date LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR p.check_number LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR p.amount LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR p.payer LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR f.name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
        }
        $post_date_filter = $this->input->get('post_date_filter');
        if ($post_date_filter != '') {
            $dates = explode('-', $post_date_filter);
            $startdate = date('Y-m-d', strtotime($dates[0]));
            $enddate = date('Y-m-d', strtotime($dates[1]));
            $this->db->where('p.check_date >=', $startdate);
            $this->db->where('p.check_date <=', $enddate);
        }
        $this->db->where(['p.is_delete' => 0]);
        $this->db->order_by($columns[$this->input->get('order')[0]['column']], $this->input->get('order')[0]['dir']);
        if ($type == 'result') {
            $this->db->limit($this->input->get('length'), $this->input->get('start'));
            $query = $this->db->get(TBL_PAYMENTS . ' p');
            return $query->result_array();
        } else {
            $query = $this->db->get(TBL_PAYMENTS . ' p');
            return $query->num_rows();
        }
    }

    /**
     * Get account details of particular id
     * @param int $id
     */
    public function get_payment_details($id) {
        $this->db->select('p.*,a.action_matters_campaign,a.vendor_name,a.fund_type_id,a.total_fund,f.type');
        $this->db->join(TBL_ACCOUNTS . ' as a', 'p.account_id=a.id', 'left');
        $this->db->join(TBL_FUND_TYPES . ' as f', 'a.fund_type_id=f.id', 'left');
        $this->db->where(['p.id' => $id, 'p.is_delete' => 0]);
        $query = $this->db->get(TBL_PAYMENTS . ' p');
        return $query->row_array();
    }

    /**
     * Get account fund with fund type[is_vendor or not] field of particular id
     * @param int $id Account Id
     */
    public function get_account_fund($id) {
        $this->db->select('a.total_fund,f.type');
        $this->db->join(TBL_FUND_TYPES . ' as f', 'a.fund_type_id=f.id', 'left');
        $this->db->where(['a.id' => $id, 'a.is_delete' => 0]);
        $query = $this->db->get(TBL_ACCOUNTS . ' a');
        return $query->row_array();
    }

    /**
     * Get Payments Made report
     * @param string $type - Either result or count
     * @return array for result or int for count
     */
    public function get_payments_made_report($type = 'result') {
        $columns = ['a.action_matters_campaign,v.name', 'a.address,v.address', 'city,vc.name', 'state,vs.name', 'a.zip', 'p.amount', 'p.check_date', 'p.check_number'];
        $keyword = $this->input->get('search');
        $this->db->select('a.action_matters_campaign,v.name as vendor_name,a.address,v.address as vendor_address,a.zip,'
                . 'v.zip as vendor_zip,p.amount,p.check_date,p.check_number,p.payer,c.name as city,vc.name as vendor_city,s.name as state,vs.name as vendor_state');
        $this->db->join(TBL_ACCOUNTS . ' as a', 'a.id=p.account_id AND p.payer="account"', 'left');
        $this->db->join(TBL_VENDORS . ' as v', 'v.id=p.account_id AND p.payer="vendor"', 'left');
        $this->db->join(TBL_CITIES . ' as c', 'a.city_id=c.id', 'left');
        $this->db->join(TBL_STATES . ' as s', 'a.state_id=s.id', 'left');
        $this->db->join(TBL_CITIES . ' as vc', 'v.city_id=vc.id', 'left');
        $this->db->join(TBL_STATES . ' as vs', 'v.state_id=vs.id', 'left');

        if (!empty($keyword['value'])) {
            $this->db->where('(v.name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR a.action_matters_campaign LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR a.address LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR v.address LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR a.zip LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR v.zip LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR c.name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR vc.name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR s.name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR vs.name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR p.amount LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR p.check_date LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR p.check_number LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
        }
        $post_date_filter = $this->input->get('post_date_filter');
        if ($post_date_filter != '') {
            $dates = explode('-', $post_date_filter);
            $startdate = date('Y-m-d', strtotime($dates[0]));
            $enddate = date('Y-m-d', strtotime($dates[1]));
            $this->db->where('p.check_date >=', $startdate);
            $this->db->where('p.check_date <=', $enddate);
        }
        $this->db->where(['p.is_delete' => 0]);
        $this->db->order_by($columns[$this->input->get('order')[0]['column']], $this->input->get('order')[0]['dir']);
        if ($type == 'result') {
            $this->db->limit($this->input->get('length'), $this->input->get('start'));
            $query = $this->db->get(TBL_PAYMENTS . ' p');
            return $query->result_array();
        } else {
            $query = $this->db->get(TBL_PAYMENTS . ' p');
            return $query->num_rows();
        }
    }

}
