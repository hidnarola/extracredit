<?php

/**
 * Manage funds table related database operations
 * @author KU
 */
class Funds_model extends MY_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get Admin fund for datatable
     * @param string $type - Either result or count
     * @return array for result or int for count
     */
    public function get_adminfund($type = 'result') {
        $columns = ['d.date', 'd.post_date', 'ft.type', 'a.action_matters_campaign,a.vendor_name', 'p.type', 'd.payment_number', 'd.memo', 'f.admin_fund', 'd.amount', 'a.total_fund'];
        $keyword = $this->input->get('search');
        $this->db->select('d.date,d.post_date,ft.type as fund_type,a.action_matters_campaign,a.vendor_name,p.type as payment_type,d.payment_number,d.memo,f.admin_fund,d.amount,a.total_fund as balance,ft.is_vendor');

        $this->db->join(TBL_ACCOUNTS . ' as a', 'd.account_id=a.id AND a.is_delete=0', 'left');
        $this->db->join(TBL_FUND_TYPES . ' as ft', 'a.fund_type_id=ft.id AND ft.is_delete=0', 'left');
        $this->db->join(TBL_PAYMENT_TYPES . ' as p', 'd.payment_type_id=p.id AND p.is_delete=0', 'left');
        $this->db->join(TBL_FUNDS . ' as f', 'f.donor_id=d.id AND f.is_delete=0', 'left');

        if (!empty($keyword['value'])) {
            $this->db->where('(action_matters_campaign LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR vendor_name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR d.date LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR d.post_date LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR ft.type LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR p.type LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR d.memo LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR f.admin_fund LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR d.amount LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR a.total_fund LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
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
     * Get account fund for datatable
     * @param string $type - Either result or count
     * @return array for result or int for count
     */
    public function get_accountfund($type = 'result') {
        $columns = ['d.date', 'd.post_date', 'ft.type', 'a.action_matters_campaign,a.vendor_name', 'p.type', 'd.payment_number', 'd.memo', 'f.account_fund', 'd.amount', 'a.total_fund'];
        $keyword = $this->input->get('search');
        $this->db->select('d.date,d.post_date,ft.type as fund_type,a.action_matters_campaign,a.vendor_name,p.type as payment_type,d.payment_number,d.memo,f.account_fund,d.amount,a.total_fund as balance,ft.is_vendor');

        $this->db->join(TBL_ACCOUNTS . ' as a', 'd.account_id=a.id AND a.is_delete=0', 'left');
        $this->db->join(TBL_FUND_TYPES . ' as ft', 'a.fund_type_id=ft.id AND ft.is_delete=0', 'left');
        $this->db->join(TBL_PAYMENT_TYPES . ' as p', 'd.payment_type_id=p.id AND p.is_delete=0', 'left');
        $this->db->join(TBL_FUNDS . ' as f', 'f.donor_id=d.id AND f.is_delete=0', 'left');

        if (!empty($keyword['value'])) {
            $this->db->where('(action_matters_campaign LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR vendor_name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR d.date LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR d.post_date LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR ft.type LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR p.type LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR d.memo LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR f.account_fund LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR d.amount LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR a.total_fund LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
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
     * Get donor fund for datatable
     * @param string $type - Either result or count
     * @return array for result or int for count
     */
    public function get_donorfund($type = 'result') {
        $columns = ['d.date', 'd.post_date', 'ft.type', 'a.action_matters_campaign,a.vendor_name', 'd.id','d.lastname','d.firstname', 'p.type', 'd.payment_number', 'd.memo', 'f.account_fund', 'd.amount', 'a.total_fund'];
        $keyword = $this->input->get('search');
        $this->db->select('d.date,d.post_date,ft.type as fund_type,a.action_matters_campaign,a.vendor_name,d.id,d.lastname,d.firstname,'
                . 'p.type as payment_type,d.payment_number,d.memo,f.account_fund,d.amount,a.total_fund as balance,ft.is_vendor');

        $this->db->join(TBL_ACCOUNTS . ' as a', 'd.account_id=a.id AND a.is_delete=0', 'left');
        $this->db->join(TBL_FUND_TYPES . ' as ft', 'a.fund_type_id=ft.id AND ft.is_delete=0', 'left');
        $this->db->join(TBL_PAYMENT_TYPES . ' as p', 'd.payment_type_id=p.id AND p.is_delete=0', 'left');
        $this->db->join(TBL_FUNDS . ' as f', 'f.donor_id=d.id AND f.is_delete=0', 'left');

        if (!empty($keyword['value'])) {
            $this->db->where('(action_matters_campaign LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR vendor_name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR d.date LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR d.post_date LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR ft.type LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR d.id LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR d.lastname LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR d.firstname LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR p.type LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR d.memo LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR f.account_fund LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR d.amount LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR a.total_fund LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
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

}
