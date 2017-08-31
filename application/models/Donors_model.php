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

        $this->db->where(['a.is_delete' => 0]);
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
        $this->db->select('d.*,a.fund_type_id');
        $this->db->join(TBL_ACCOUNTS . ' as a', 'd.account_id=a.id', 'left');
        $this->db->where(['d.id' => $id, 'd.is_delete' => 0]);
        $query = $this->db->get(TBL_DONORS . ' d');
        return $query->row_array();
    }

}
