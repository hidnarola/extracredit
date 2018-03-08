<?php

/**
 * Manage contacts table related database operations
 * @author KU
 */
class Contacts_model extends MY_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get contacts for datatable
     * @param string $type - Either result or count
     * @return array for result or int for count
     */
    public function get_contacts($type = 'result') {
        $columns = ['is_delete', 'name', 'email', 'phone', 'website', 'created', 'is_active'];
        $keyword = $this->input->get('search');
        $this->db->select('id,name,email,phone,website,date(created) created');

        if (!empty($keyword['value'])) {
            $this->db->where('(name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
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
            $query = $this->db->get(TBL_CONTACTS);
            return $query->result_array();
        } else {
            $query = $this->db->get(TBL_CONTACTS);
            return $query->num_rows();
        }
    }

    /**
     * Get contact details of particular id
     * @param int $id
     */
    public function get_contact_details($id) {
        $this->db->select('co.id,co.name,co.address,co.zip,co.email,co.phone,co.website,date(co.created) created,c.name as city,s.name as state,s.short_name as state_short');
        $this->db->join(TBL_CITIES . ' as c', 'co.city_id=c.id', 'left');
        $this->db->join(TBL_STATES . ' as s', 'co.state_id=s.id', 'left');
        $this->db->where(['co.id' => $id, 'co.is_delete' => 0]);
        $query = $this->db->get(TBL_CONTACTS . ' co');
        return $query->row_array();
    }

    /**
     * Get vendors communication for data-table
     * @param string/int $type Either result or count
     * @param int $id
     * @return array/int
     * @author KU
     */
    public function get_contacts_communication($type = 'result', $id) {
        $columns = ['id', 'c.media', 'c.subject', 'c.communication_date', 'c.follow_up_date', 'c.note', 'c.created'];
        $keyword = $this->input->get('search');
        $this->db->select('c.*');

        if (!empty($keyword['value'])) {
            $this->db->where('(c.note LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ' OR c.subject LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
        }
        $this->db->where(['c.is_delete' => 0]);
        $this->db->where(['c.contact_id' => $id]);
        $this->db->where(['c.type' => 5]);
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

}
