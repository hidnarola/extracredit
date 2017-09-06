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
        $columns = ['p.id', 'f.type', 'a.action_matters_campaign,a.vendor_name', 'p.check_date', 'p.check_number', 'p.amount', 'p.created', 'p.is_delete'];
        $keyword = $this->input->get('search');
        $this->db->select('p.*,a.action_matters_campaign,a.vendor_name,f.type as fund_type,f.is_vendor');
        $this->db->join(TBL_ACCOUNTS . ' as a', 'p.account_id=a.id', 'left');
        $this->db->join(TBL_FUND_TYPES . ' as f', 'a.fund_type_id=f.id', 'left');

        if (!empty($keyword['value'])) {
            $this->db->where('(a.action_matters_campaign LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR a.vendor_name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR p.check_date LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR p.check_number LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR p.amount LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR f.type LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
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
        $this->db->select('p.*,a.action_matters_campaign,a.vendor_name,a.fund_type_id,a.total_fund,f.is_vendor');
        $this->db->join(TBL_FUND_TYPES . ' as f', 'a.fund_type_id=f.id', 'left');
        $this->db->join(TBL_ACCOUNTS . ' as a', 'p.account_id=a.id', 'left');
        $this->db->where(['p.id' => $id, 'p.is_delete' => 0]);
        $query = $this->db->get(TBL_PAYMENTS . ' p');
        return $query->row_array();
    }

    /**
     * Get account fund with is_vendor field of particular id
     * @param int $id Account Id
     */
    public function get_account_fund($id) {
        $this->db->select('a.total_fund,f.is_vendor');
        $this->db->join(TBL_FUND_TYPES . ' as f', 'a.fund_type_id=f.id', 'left');
        $this->db->where(['a.id' => $id, 'a.is_delete' => 0]);
        $query = $this->db->get(TBL_ACCOUNTS . ' a');
        return $query->row_array();
    }

}
