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
        $columns = ['created', 'date', 'post_date', 'firstname', 'lastname', 'fund_type', 'sub_category', 'payment_method', 'payment_number', 'memo', 'debit_amt', 'credit_amt', 'balance'];
        $keyword = $this->input->get('search');

        $where = $where1 = '';
        if (!empty($keyword['value'])) {
            $where = ' AND (a.action_matters_campaign LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR f.date LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR f.post_date LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR d.firstname LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR d.lastname LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR ft.name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR pt.type LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR f.payment_number LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR f.memo LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR f.admin_fund LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR f.admin_balance LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')';
            $where1 = ' AND (ac.vendor_name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ''
                    . ' OR p.check_date LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ''
                    . ' OR p.check_number LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ''
                    . ' OR p.amount LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ''
                    . ' OR p.account_balance LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')';
        }

        $sql = 'SELECT f.created,f.date,f.post_date,d.firstname,d.lastname,ft.name as fund_type,a.action_matters_campaign as sub_category,pt.type as payment_method,f.payment_number,f.memo,"" as debit_amt,f.admin_fund as credit_amt,f.admin_balance as balance '
                . 'FROM ' . TBL_FUNDS . ' f LEFT JOIN ' . TBL_DONORS . ' d ON f.donor_id=d.id AND d.is_delete=0 '
                . 'LEFT JOIN ' . TBL_ACCOUNTS . ' a ON f.account_id=a.id '
                . 'LEFT JOIN ' . TBL_FUND_TYPES . ' ft ON a.fund_type_id=ft.id AND ft.is_delete=0 '
                . 'LEFT JOIN ' . TBL_PAYMENT_TYPES . ' pt ON f.payment_type_id=pt.id AND pt.is_delete=0 '
                . 'WHERE f.is_delete=0 AND f.is_refund=0 AND a.is_delete=0 ' . $where
                . 'UNION ALL '
                . 'SELECT p.created,p.check_date as date,"" as post_date,"" as firstname,"" as lastname,"" as fund_type,ac.vendor_name as sub_category,"" as payment_method,p.check_number as payment_number,"" as memo,p.amount as debit_amt,"" as credit_amt,p.account_balance as balance '
                . 'FROM ' . TBL_PAYMENTS . ' p LEFT JOIN ' . TBL_ACCOUNTS . ' ac ON p.account_id=ac.id '
                . 'LEFT JOIN ' . TBL_FUND_TYPES . ' fnt ON ac.fund_type_id=fnt.id AND fnt.is_delete=0 '
                . 'WHERE p.is_delete=0 AND fnt.type=1 AND ac.is_delete=0 ' . $where1;

        $sql.=' ORDER BY ' . $columns[$this->input->get('order')[0]['column']] . ' ' . $this->input->get('order')[0]['dir'];
        if ($type == 'result') {
            $sql.=' LIMIT ' . $this->input->get('start') . ', ' . $this->input->get('length');
            $query = $this->db->query($sql);
            return $query->result_array();
        } else {
            $query = $this->db->query($sql);
            return $query->num_rows();
        }
    }

    /**
     * Get Admin fund for datatable
     * @param string $type - Either result or count
     * @return array for result or int for count
     */
    public function get_adminfund_old($type = 'result') {
        $columns = ['f.date', 'f.post_date', 'd.firstname', 'd.lastname', 'ft.type', 'a.action_matters_campaign,a.vendor_name', 'p.type', 'f.payment_number', 'f.memo', 'p.amount', 'f.admin_fund', 'f.admin_balance'];
        $keyword = $this->input->get('search');
        $this->db->select('f.date,f.post_date,d.firstname,d.lastname,ft.name as fund_type,a.action_matters_campaign,a.vendor_name,p.type as payment_type,f.payment_number,f.memo,d.amount,ft.type,f.admin_fund,f.admin_balance');

        $this->db->join(TBL_DONORS . ' as d', 'f.donor_id=d.id AND d.is_delete=0', 'left');
        $this->db->join(TBL_ACCOUNTS . ' as a', 'f.account_id=a.id AND a.is_delete=0', 'left');
        $this->db->join(TBL_FUND_TYPES . ' as ft', 'a.fund_type_id=ft.id AND ft.is_delete=0', 'left');
        $this->db->join(TBL_PAYMENT_TYPES . ' as p', 'f.payment_type_id=p.id AND p.is_delete=0', 'left');

        if (!empty($keyword['value'])) {
            $this->db->where('(action_matters_campaign LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR vendor_name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR f.date LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR f.post_date LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR ft.type LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR p.type LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR f.memo LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR f.admin_fund LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR d.amount LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR a.total_fund LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
        }

        $this->db->where(['f.is_delete' => 0]);
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
     * Get account fund for datatable
     * @param string $type - Either result or count
     * @return array for result or int for count
     */
    public function get_accountfund($type = 'result') {
        $columns = ['a.id', 'a.action_matters_campaign', 'ft.name', 'a.total_fund', 'a.is_delete'];
        $keyword = $this->input->get('search');
        $this->db->select('a.id,a.action_matters_campaign as sub_category,ft.name as fund_type,a.total_fund as balance');

        $this->db->join(TBL_FUND_TYPES . ' as ft', 'a.fund_type_id=ft.id AND ft.is_delete=0', 'left');

        if (!empty($keyword['value'])) {
            $this->db->where('(action_matters_campaign LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR ft.name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR a.total_fund LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
        }

        $this->db->where(['a.is_delete' => 0, 'ft.type!=' => 1]);
        $this->db->order_by($columns[$this->input->get('order')[0]['column']], $this->input->get('order')[0]['dir']);
        if ($type == 'result') {
            $this->db->limit($this->input->get('length'), $this->input->get('start'));
            $query = $this->db->get(TBL_ACCOUNTS . ' a');
            return $query->result_array();
        } else {
            $query = $this->db->get(TBL_ACCOUNTS . ' a');
            return $query->num_rows();
        }
    }

    /**
     * Get donor fund for datatable
     * @param string $type - Either result or count
     * @return array for result or int for count
     */
    public function get_donorfund($type = 'result') {
        $columns = ['f.date', 'f.post_date', 'ft.type', 'a.action_matters_campaign,a.vendor_name', 'd.id', 'd.lastname', 'd.firstname', 'p.type', 'f.payment_number', 'f.memo', 'd.amount'];
        $keyword = $this->input->get('search');
        $this->db->select('f.date,f.post_date,ft.name as fund_type,a.action_matters_campaign,a.vendor_name,d.id,d.lastname,d.firstname,'
                . 'p.type as payment_type,f.payment_number,f.memo,f.account_fund,d.amount,a.total_fund as balance,ft.type');

        $this->db->join(TBL_FUNDS . ' as f', 'f.donor_id=d.id AND f.is_delete=0', 'left');
        $this->db->join(TBL_ACCOUNTS . ' as a', 'f.account_id=a.id AND a.is_delete=0', 'left');
        $this->db->join(TBL_FUND_TYPES . ' as ft', 'a.fund_type_id=ft.id AND ft.is_delete=0', 'left');
        $this->db->join(TBL_PAYMENT_TYPES . ' as p', 'f.payment_type_id=p.id AND p.is_delete=0', 'left');

        if (!empty($keyword['value'])) {
            $this->db->where('(action_matters_campaign LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR vendor_name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR f.date LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR f.post_date LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR ft.type LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR d.id LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR d.lastname LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR d.firstname LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR p.type LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR f.payment_number LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR f.memo LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR d.amount LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
        }

        //-- Date filter
        $date_filter = $this->input->get('date_filter');
        if ($date_filter != '') {
            $dates = explode('-', $date_filter);
            $startdate = date('Y-m-d', strtotime($dates[0]));
            $enddate = date('Y-m-d', strtotime($dates[1]));
            $this->db->where('f.date >=', $startdate);
            $this->db->where('f.date <=', $enddate);
        }

        $this->db->where(['d.is_delete' => 0, 'd.refund' => 0]);
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
     * Get payment fund for datatable
     * @param string $type - Either result or count
     * @return array for result or int for count
     */
    public function get_paymentfund($type = 'result') {
        $columns = ['ft.type', 'a.action_matters_campaign,a.vendor_name', 'p.check_date', 'p.check_number', 'p.amount'];
        $keyword = $this->input->get('search');
        $this->db->select('ft.name as fund_type,a.action_matters_campaign,a.vendor_name,p.check_date,p.check_number,p.amount,'
                . 'a.total_fund as balance,ft.type');

        $this->db->join(TBL_ACCOUNTS . ' as a', 'p.account_id=a.id AND a.is_delete=0', 'left');
        $this->db->join(TBL_FUND_TYPES . ' as ft', 'a.fund_type_id=ft.id AND ft.is_delete=0', 'left');

        if (!empty($keyword['value'])) {
            $this->db->where('(action_matters_campaign LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR vendor_name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR ft.type LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR p.check_date LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR p.check_number LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR p.amount LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
        }
        //-- Check date filter
        $date_filter = $this->input->get('date_filter');
        if ($date_filter != '') {
            $dates = explode('-', $date_filter);
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
     * Get incoming money from donors model
     * @param string/array $where
     * @author KU
     */
    public function get_incoming_money($where = NULL) {
        $this->db->select("sum(account_fund)+sum(admin_fund) as count,DATE_FORMAT(created,'%Y-%m-%d') as date");
        if (!is_null($where)) {
            $this->db->where($where);
        }
        $this->db->group_by("DATE_FORMAT(created,'%Y-%m-%d')");
        $query = $this->db->get(TBL_FUNDS);
        return $query->result_array();
    }

}
