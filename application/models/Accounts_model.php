<?php

/**
 * Manage aacounts table related database operations
 * @author KU
 */
class Accounts_model extends MY_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get accounts for datatable
     * @param string $type - Either result or count
     * @return array for result or int for count
     */
    public function get_accounts($type = 'result') {
        $columns = ['id', 'fund_type', 'action_matters_campaign,vendor_name', 'email', 'contact_name', 'city', 'total_fund', 'created', 'is_active'];
        $keyword = $this->input->get('search');
        $this->db->select('a.*,f.name as fund_type,c.name as city,f.type');
        $this->db->join(TBL_FUND_TYPES . ' as f', 'a.fund_type_id=f.id', 'left');
        $this->db->join(TBL_CITIES . ' as c', 'a.city_id=c.id', 'left');

        if (!empty($keyword['value'])) {
            $this->db->where('(action_matters_campaign LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR vendor_name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR email LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR contact_name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR c.name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR total_fund LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR f.type LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
        }

        $this->db->where(['a.is_delete' => 0]);
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
     * Get account details of particular id
     * @param int $id
     */
    public function get_account_details($id) {
        $this->db->select('a.*,f.type');
        $this->db->join(TBL_FUND_TYPES . ' as f', 'a.fund_type_id=f.id', 'left');
        $this->db->where(['a.id' => $id, 'a.is_delete' => 0]);
        $query = $this->db->get(TBL_ACCOUNTS . ' a');
        return $query->row_array();
    }

    /**
     * Get accounts for datatable for report
     * @param string $type - Either result or count
     * @return array for result or int for count
     */
    public function get_programs_amc_report($type = 'result') {
        $columns = ['id', 'is_active', 'fund_type', 'action_matters_campaign,vendor_name', 'address', 'city', 'state', 'zip', 'contact_name', 'email', 'phone', 'tax_id', 'program_type', 'status', 'total_fund'];
        $keyword = $this->input->get('search');
        $this->db->select('a.*,f.name as fund_type,c.name as city,s.name as state,f.type,pt.type as program_type,ps.status');
        $this->db->join(TBL_FUND_TYPES . ' as f', 'a.fund_type_id=f.id', 'left');
        $this->db->join(TBL_CITIES . ' as c', 'a.city_id=c.id', 'left');
        $this->db->join(TBL_STATES . ' as s', 'a.state_id=s.id', 'left');
        $this->db->join(TBL_PROGRAM_TYPES . ' as pt', 'a.program_type_id=pt.id', 'left');
        $this->db->join(TBL_PROGRAM_STATUS . ' as ps', 'a.program_status_id=ps.id', 'left');

        if (!empty($keyword['value'])) {
            $this->db->where('(action_matters_campaign LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR vendor_name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR email LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR contact_name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR c.name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR total_fund LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR ps.status LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR pt.type LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR f.type LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
        }

        $this->db->where(['a.is_delete' => 0]);
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
     * Get awards from payments table for datatable for report
     * @param string $type - Either result or count
     * @return array for result or int for count
     */
    public function get_awards_report($type = 'result') {
        $columns = ['check_date', 'check_number', 'id', 'action_matters_campaign', 'address', 'city', 'state', 'zip', 'total_fund'];
        $keyword = $this->input->get('search');
        $this->db->select('p.*,a.*,f.name as fund_type,c.name as city,s.name as state,f.type');
        $this->db->join(TBL_ACCOUNTS . ' as a', 'a.id=p.account_id', 'left');
        $this->db->join(TBL_FUND_TYPES . ' as f', 'a.fund_type_id=f.id', 'left');
        $this->db->join(TBL_CITIES . ' as c', 'a.city_id=c.id', 'left');
        $this->db->join(TBL_STATES . ' as s', 'a.state_id=s.id', 'left');

        if (!empty($keyword['value'])) {
            $this->db->where('(action_matters_campaign LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR address LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR s.name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR a.zip LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR c.name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR a.total_fund LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
        }
        $this->db->where(['p.is_delete' => 0]);
        $this->db->where(['f.type' => 0]);
        $this->db->order_by($columns[$this->input->get('order')[0]['column']], $this->input->get('order')[0]['dir']);
        if ($type == 'result') {
            $this->db->limit($this->input->get('length'), $this->input->get('start'));
            $query = $this->db->get(TBL_PAYMENTS . ' p');
//            echo $this->db->last_query();
            return $query->result_array();
        } else {
            $query = $this->db->get(TBL_PAYMENTS . ' p');
            return $query->num_rows();
        }
    }

    /**
     * Get awards from payments table for datatable for report
     * @param string $type - Either result or count
     * @return array for result or int for count
     */
    public function get_vendor_admin_report($type = 'result') {
        $columns = ['check_date', 'check_number', 'id', 'vendor_name', 'address', 'city', 'state', 'zip', 'total_fund'];
        $keyword = $this->input->get('search');
        $this->db->select('p.*,a.*,f.name as fund_type,c.name as city,s.name as state,f.type');
        $this->db->join(TBL_ACCOUNTS . ' as a', 'a.id=p.account_id', 'left');
        $this->db->join(TBL_FUND_TYPES . ' as f', 'a.fund_type_id=f.id', 'left');
        $this->db->join(TBL_CITIES . ' as c', 'a.city_id=c.id', 'left');
        $this->db->join(TBL_STATES . ' as s', 'a.state_id=s.id', 'left');

        if (!empty($keyword['value'])) {
            $this->db->where('(vendor_name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR address LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR s.name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR a.zip LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR c.name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR a.total_fund LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
        }
        $this->db->where(['p.is_delete' => 0]);
        $this->db->where(['f.type' => 1]);
        $this->db->order_by($columns[$this->input->get('order')[0]['column']], $this->input->get('order')[0]['dir']);
        if ($type == 'result') {
            $this->db->limit($this->input->get('length'), $this->input->get('start'));
            $query = $this->db->get(TBL_PAYMENTS . ' p');
//            echo $this->db->last_query();
            return $query->result_array();
        } else {
            $query = $this->db->get(TBL_PAYMENTS . ' p');
            return $query->num_rows();
        }
    }

    /**
     * Get AMC balance payment report
     * @param string $type - Either result or count
     * @return array for result or int for count
     */
    public function get_amc_balance_report($type = 'result') {
        $columns = ['a.action_matters_campaign,a.vendor_name', 'inc.income', 'p.no_of_payments', 'p.payment_amount', 'a.total_fund', 'address', 'city', 'state', 'zip', 'total_fund'];
        $keyword = $this->input->get('search');
        $select1 = '(SELECT post_date FROM ' . TBL_DONORS . ' WHERE account_id=a.id AND is_delete=0 order by id DESC LIMIT 1) post_date';
        $select2 = '(SELECT check_date FROM ' . TBL_PAYMENTS . ' WHERE account_id=a.id AND is_delete=0 order by id DESC LIMIT 1) check_date';
        $this->db->select('a.action_matters_campaign,a.vendor_name,inc.income,f.name as fund_type,f.type,a.total_fund as balance_amount,p.no_of_payments,p.payment_amount,' . $select1 . ',' . $select2);
        $this->db->join(TBL_FUND_TYPES . ' as f', 'a.fund_type_id=f.id', 'left');
        $this->db->join('(SELECT sum(account_fund) income,account_id FROM ' . TBL_FUNDS . ' WHERE is_delete=0 group by account_id) inc', 'a.id=inc.account_id', 'left');
        $this->db->join('(SELECT sum(amount) payment_amount,count(id) no_of_payments,account_id FROM ' . TBL_PAYMENTS . ' WHERE is_delete=0 group by account_id) p', 'a.id=p.account_id', 'left');

        if (!empty($keyword['value'])) {
            $this->db->where('(a.vendor_name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR a.action_matters_campaign LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR inc.income LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR p.no_of_payments LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR p.payment_amount LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR a.total_fund LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
        }
        $this->db->where(['a.is_delete' => 0]);
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

}
