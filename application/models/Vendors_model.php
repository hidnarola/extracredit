<?php

/**
 * Manage vendors table related database operations
 * @author KU
 */
class Vendors_model extends MY_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get vendors for datatable
     * @param string $type - Either result or count
     * @return array for result or int for count
     */
    public function get_vendors($type = 'result') {
        $columns = ['is_delete', 'name', 'contact_name', 'email', 'phone', 'website', 'created', 'is_active'];
        $keyword = $this->input->get('search');
        $this->db->select('id,name,contact_name,email,phone,website,date(created) created');

        if (!empty($keyword['value'])) {
            $this->db->where('(name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR contact_name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR email LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR phone LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR website LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR created LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
        }
        $this->db->where(['is_delete' => 0]);
        if ($this->input->get('order')) {
            $this->db->order_by($columns[$this->input->get('order')[0]['column']], $this->input->get('order')[0]['dir']);
        }
        if ($type == 'result') {
            $this->db->limit($this->input->get('length'), $this->input->get('start'));
            $query = $this->db->get(TBL_VENDORS);
            return $query->result_array();
        } else {
            $query = $this->db->get(TBL_VENDORS);
            return $query->num_rows();
        }
    }

    /**
     * Get vendor details of particular id
     * @param int $id
     */
    public function get_vendor_details($id) {
        $this->db->select('v.id,v.name,v.contact_name,v.address,v.zip,v.email,v.phone,v.website,date(created) created,c.name as city,s.name as state,s.short_name as state_short');
        $this->db->join(TBL_CITIES . ' as c', 'v.city_id=c.id', 'left');
        $this->db->join(TBL_STATES . ' as s', 'v.state_id=s.id', 'left');
        $this->db->where(['v.id' => $id, 'v.is_delete' => 0]);
        $query = $this->db->get(TBL_VENDORS . ' v');
        return $query->row_array();
    }

}
