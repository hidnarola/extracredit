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
        $columns = ['id', 'fund_type', 'action_matters_campaign,vendor_name', 'email', 'contact_name', 'city', 'created', 'is_active'];
        $keyword = $this->input->get('search');
        $this->db->select('a.*,f.type as fund_type,c.name as city');
        $this->db->join(TBL_FUND_TYPES . ' as f', 'a.fund_type_id=f.id', 'left');
        $this->db->join(TBL_CITIES . ' as c', 'a.city_id=c.id', 'left');

        if (!empty($keyword['value'])) {
            $this->db->where('(action_matters_campaign LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR vendor_name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR email LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR contact_name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR c.name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
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
        $this->db->select('a.*,f.is_vendor');
        $this->db->join(TBL_FUND_TYPES . ' as f', 'a.fund_type_id=f.id', 'left');
        $this->db->where(['a.id' => $id, 'a.is_delete' => 0]);
        $query = $this->db->get(TBL_ACCOUNTS . ' a');
        return $query->row_array();
    }

}
