<?php

/**
 * Manage accounts table related database operations
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
        $columns = ['a.is_delete', 'f.name', 'program_name,action_matters_campaign,vendor_name', 'contact_names', 'contact_emails', 'phone', 'total_fund', 'created', 'is_active'];
        $keyword = $this->input->get('search');
        $this->db->select('a.*,f.name as fund_type,f.type,contact_tbl.contact_names,contact_tbl.contact_emails');
        $this->db->join(TBL_FUND_TYPES . ' as f', 'a.fund_type_id=f.id', 'left');
        $this->db->join('(select GROUP_CONCAT(name) contact_names,GROUP_CONCAT(email) contact_emails,associated_id FROM ' . TBL_ASSOCIATED_CONTACTS . ' '
                . 'WHERE type="account" AND is_delete=0 group by associated_id) as contact_tbl', 'a.id=contact_tbl.associated_id', 'left');

        if (!empty($keyword['value'])) {
            $this->db->where('(action_matters_campaign LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR contact_tbl.contact_names LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR contact_tbl.contact_emails LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR program_name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR vendor_name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR email LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR contact_name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR phone LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR total_fund LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR f.name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
        }
        $this->db->where(['a.is_delete' => 0]);
        if ($this->input->get('order')) {
            $this->db->order_by($columns[$this->input->get('order')[0]['column']], $this->input->get('order')[0]['dir']);
        }
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
        $this->db->select('a.*,IF(a.program_name = \'\',a.action_matters_campaign,a.program_name) as program,f.type,c.name as city,s.name as state,s.short_name as state_short');
        $this->db->join(TBL_FUND_TYPES . ' as f', 'a.fund_type_id=f.id', 'left');
        $this->db->join(TBL_CITIES . ' as c', 'a.city_id=c.id', 'left');
        $this->db->join(TBL_STATES . ' as s', 'a.state_id=s.id', 'left');

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
        $columns = ['id', 'check_date', 'check_number', 'v.name', 'address', 'city', 'state', 'zip', 'p.amount'];
        $keyword = $this->input->get('search');
        $this->db->select('p.*,v.*,c.name as city,s.name as state');
        $this->db->join(TBL_VENDORS . ' as v', 'v.id=p.account_id AND v.is_delete=0', 'left');
        $this->db->join(TBL_CITIES . ' as c', 'v.city_id=c.id', 'left');
        $this->db->join(TBL_STATES . ' as s', 'v.state_id=s.id', 'left');

        if (!empty($keyword['value'])) {
            $this->db->where('(v.name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR p.check_date LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR p.check_number LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR address LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR c.name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR s.name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR v.zip LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR p.amount LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
        }
        $post_date_filter = $this->input->get('post_date_filter');
        if ($post_date_filter != '') {
            $dates = explode('-', $post_date_filter);
            $startdate = date('Y-m-d', strtotime($dates[0]));
            $enddate = date('Y-m-d', strtotime($dates[1]));
            $this->db->where('p.check_date >=', $startdate);
            $this->db->where('p.check_date <=', $enddate);
        }
        $this->db->where(['p.is_delete' => 0, 'p.payer' => 'vendor']);
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
     * Get AMC balance payment report
     * @param string $type - Either result or count
     * @return array for result or int for count
     */
    public function get_amc_balance_report($type = 'result') {
//               $columns = ['id', 'is_active', 'fund_type', 'action_matters_campaign,vendor_name', 'address', 'city', 'state', 'zip', 'contact_name', 'email', 'phone', 'tax_id', 'program_type', 'status', 'total_fund'];
        $columns = ['a.is_active', 'sub_category', 'a.address', 'a.city', 'a.state', 'a.zip', 'a.contact_name', 'a.email', 'a.phone', 'a.tax_id', 'a.program_type', 'a.status', 'inc.income', 'p.no_of_payments', 'p.payment_amount', 'a.total_fund', 'address', 'city', 'state', 'zip', 'total_fund'];
        $keyword = $this->input->post('search');
        $select1 = '(SELECT post_date FROM ' . TBL_FUNDS . ' WHERE account_id=a.id AND is_delete=0 AND is_refund=0 order by id DESC LIMIT 1) post_date';
        $select2 = '(SELECT check_date FROM ' . TBL_PAYMENTS . ' WHERE account_id=a.id AND is_delete=0 order by id DESC LIMIT 1) check_date';
//        $this->db->select('a.action_matters_campaign,a.vendor_name,inc.income,f.name as fund_type,f.type,a.total_fund as balance_amount,p.no_of_payments,p.payment_amount,' . $select1 . ',' . $select2);
        $this->db->select('a.*,IF(a.program_name = \'\',a.action_matters_campaign,a.program_name) as sub_category,f.name as fund_type,c.name as city,s.name as state,f.type,pt.type as program_type,ps.status,inc.income,f.name as fund_type,f.type,a.total_fund as balance_amount,p.no_of_payments,p.payment_amount,' . $select1 . ',' . $select2);
        $this->db->join(TBL_FUND_TYPES . ' as f', 'a.fund_type_id=f.id', 'left');
        $this->db->join('(SELECT sum(account_fund) income,account_id FROM ' . TBL_FUNDS . ' WHERE is_delete=0 group by account_id) inc', 'a.id=inc.account_id', 'left');
        $this->db->join('(SELECT sum(amount) payment_amount,count(id) no_of_payments,account_id FROM ' . TBL_PAYMENTS . ' WHERE is_delete=0 AND payer="account" group by account_id) p', 'a.id=p.account_id', 'left');

        $this->db->join(TBL_CITIES . ' as c', 'a.city_id=c.id', 'left');
        $this->db->join(TBL_STATES . ' as s', 'a.state_id=s.id', 'left');
        $this->db->join(TBL_PROGRAM_TYPES . ' as pt', 'a.program_type_id=pt.id', 'left');
        $this->db->join(TBL_PROGRAM_STATUS . ' as ps', 'a.program_status_id=ps.id', 'left');
        if (!empty($keyword['value'])) {
            $this->db->where('(a.vendor_name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR a.action_matters_campaign LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR a.program_name ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR a.address LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR c.name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR s.name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR a.zip LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR a.email LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR a.phone LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR pt.type LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR ps.status LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR inc.income LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR p.no_of_payments LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR p.payment_amount LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR a.total_fund LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
        }
        $post_date_filter = $this->input->post('post_date_filter');
        if ($post_date_filter != '') {
            $dates = explode('-', $post_date_filter);
            $startdate = date('Y-m-d', strtotime($dates[0]));
            $enddate = date('Y-m-d', strtotime($dates[1]));
            $this->db->where('payments.check_date >=', $startdate);
            $this->db->where('payments.check_date <=', $enddate);
        }
        $this->db->where(['a.is_delete' => 0]);
        $this->db->order_by($columns[$this->input->post('order')[0]['column']], $this->input->post('order')[0]['dir']);
        if ($type == 'result') {
            $this->db->limit($this->input->post('length'), $this->input->post('start'));
            $query = $this->db->get(TBL_ACCOUNTS . ' a');
            return $query->result_array();
        } else {
            $query = $this->db->get(TBL_ACCOUNTS . ' a');
            return $query->num_rows();
        }
    }

    /**
     * Get account's transactions
     * @param int $account_id
     * @author KU
     */
    public function get_account_transactions($account_id) {
        $sql = 'SELECT f.created,f.date,f.post_date,d.firstname,d.lastname,pt.type as payment_method,f.payment_number,f.memo,"" as debit_amt,f.account_fund as credit_amt,f.account_balance as balance,0 as is_refund '
                . 'FROM ' . TBL_FUNDS . ' f LEFT JOIN ' . TBL_DONORS . ' d ON f.donor_id=d.id AND d.is_delete=0 '
                . 'LEFT JOIN ' . TBL_ACCOUNTS . ' a ON f.account_id=a.id AND a.is_delete=0 '
                . 'LEFT JOIN ' . TBL_PAYMENT_TYPES . ' pt ON f.payment_type_id=pt.id AND pt.is_delete=0 '
                . 'WHERE f.is_delete=0 AND f.account_id=' . $account_id . ' AND d.is_delete=0'
                . ' UNION ALL '
                . 'SELECT p.created,p.check_date as date,"" as post_date,"" as firstname,"" as lastname,"" as payment_method,p.check_number as payment_number,"" as memo,p.amount as debit_amt,"" as credit_amt,p.account_balance as balance,0 as refund '
                . 'FROM ' . TBL_PAYMENTS . ' p LEFT JOIN ' . TBL_ACCOUNTS . ' ac ON p.account_id=ac.id AND ac.is_delete=0 '
                . 'WHERE p.is_delete=0 AND p.account_id=' . $account_id .
                ' UNION ALL ' .
                'SELECT f.created,f.date,f.post_date,d.firstname,d.lastname,pt.type as payment_method,f.payment_number,f.memo,"" as debit_amt,f.account_fund as credit_amt,f.account_balance as balance,f.is_refund '
                . 'FROM ' . TBL_FUNDS . ' f LEFT JOIN ' . TBL_DONORS . ' d ON f.donor_id=d.id AND d.is_delete=0 '
                . 'LEFT JOIN ' . TBL_ACCOUNTS . ' a ON f.account_id=a.id AND a.is_delete=0 '
                . 'LEFT JOIN ' . TBL_PAYMENT_TYPES . ' pt ON f.payment_type_id=pt.id AND pt.is_delete=0 '
                . 'WHERE f.is_delete=0 AND f.is_refund=1 AND f.account_id=' . $account_id . ' AND d.is_delete=0' .
                ' UNION ALL ' .
                'SELECT af.created,af.created as date,"" as post_date,a.action_matters_campaign as firstname,"" as lastname,"" as payment_method,"" as payment_number,"" as memo,af.amount as debit_amt,"" as credit_amt,af.account1_fund as balance,-1 as is_refund '
                . 'FROM ' . TBL_ACCOUNTS_TRANSFER . ' af LEFT JOIN ' . TBL_ACCOUNTS . ' a ON af.account_id_to=a.id AND a.is_delete=0 '
                . 'WHERE af.is_delete=0 AND af.account_id_from=' . $account_id .
                ' UNION ALL ' .
                'SELECT af.created,af.created as date,"" as post_date,a.action_matters_campaign as firstname,"" as lastname,"" as payment_method,"" as payment_number,"" as memo,"" as debit_amt,af.amount as credit_amt,af.account1_fund as balance,-2 as is_refund '
                . 'FROM ' . TBL_ACCOUNTS_TRANSFER . ' af LEFT JOIN ' . TBL_ACCOUNTS . ' a ON af.account_id_from=a.id AND a.is_delete=0 '
                . 'WHERE af.is_delete=0 AND af.account_id_to=' . $account_id
        ;

        $sql .= ' ORDER BY created';
//        echo $sql;
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * Get guest communication for datatable
     * @param string $type - Either result or count
     * @return array for result or int for count
     * @author REP
     */
    public function get_accounts_communication($type = 'result', $id) {
        $columns = ['id', 'ac.name', 'c.subject', 'c.communication_date', 'c.follow_up_date', 'c.note', 'c.media', 'c.created'];
        $keyword = $this->input->get('search');
        $this->db->select('c.*,ac.name as conversation_contact');
        $this->db->join(TBL_ASSOCIATED_CONTACTS . ' as ac', 'ac.id=c.associated_contact_id', 'left');
        if (!empty($keyword['value'])) {
            $this->db->where('(c.note LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ' OR ac.name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ' OR c.subject LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
        }
        $this->db->where(['c.is_delete' => 0]);
        $this->db->where(['c.account_id' => $id]);
        $this->db->where(['c.type' => 3]);
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
     * Get guest communication details of particular id
     * @param type $id
     * @author REP
     */
    public function get_account_communication_details($id) {
        $this->db->select('c.*,c.id as conversation_id,action_matters_campaign,vendor_name');
        $this->db->join(TBL_ACCOUNTS . ' as a', 'a.id=c.account_id', 'left');
        $this->db->where(['c.id' => $id, 'c.is_delete' => 0]);
        $query = $this->db->get(TBL_COMMUNICATIONS . ' c');
        return $query->row_array();
    }

    /**
     * Get account fund with fund type[is_vendor or not] field of particular id
     * @param int $id Account Id
     * @author REP
     */
    public function get_account_fund($id) {
        $this->db->select('a.total_fund,f.type');
        $this->db->join(TBL_FUND_TYPES . ' as f', 'a.fund_type_id=f.id', 'left');
        $this->db->where(['a.id' => $id, 'a.is_delete' => 0]);
        $query = $this->db->get(TBL_ACCOUNTS . ' a');
        return $query->row_array();
    }

    public function allow_delete($id) {
        $this->db->select('f.*');
        $this->db->join(TBL_FUNDS . ' as f', 'a.id=f.account_id', 'left');
        $this->db->join(TBL_DONORS . ' as d', 'd.id=f.donor_id', 'left');
        $this->db->where(['a.id' => $id, 'd.is_delete' => 0]);
        $query = $this->db->get(TBL_ACCOUNTS . ' a');
        return $query->row_array();
    }

    public function get_accounts_report()
    {
        $this->db->select('*');
        $this->db->where(['a.is_delete' => 0,'a.is_subscribed' => 1]);
        $query = $this->db->get(TBL_ACCOUNTS . ' a');
        return $query->result_array();
    }

}
