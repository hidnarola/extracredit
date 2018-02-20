<?php

/**
 * Manage communication manager table related database operations
 * @author REP
 */
class Communication_manager_model extends MY_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get communication manager for datatable
     * @param string $type - Either result or count
     * @return array for result or int for count
     */
    public function get_communication_manager($type = 'result') {
        $columns = ['id,firstlast', 'category', 'follow_up_date'];
        $keyword = $this->input->get('search');
        $this->db->select('cm.*,CONCAT(u.firstname, " ", u.lastname) as firstlast');
        $this->db->join(TBL_COMMUNICATIONS . ' as c', 'cm.communication_id=c.id', 'left');
        $this->db->join(TBL_USERS . ' as u', 'cm.user_id=u.id', 'left');

        if (!empty($keyword['value'])) {
            $this->db->where('(CONCAT(u.firstname, " ", u.lastname) LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR cm.follow_up_date LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR cm.category LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
        }
        $this->db->where('status', 0);
        $this->db->order_by($columns[$this->input->get('order')[0]['column']], $this->input->get('order')[0]['dir']);
        if ($type == 'result') {
            $this->db->limit($this->input->get('length'), $this->input->get('start'));
            $query = $this->db->get(TBL_COMMUNICATIONS_MANAGER . ' cm');
            return $query->result_array();
        } else {
            $query = $this->db->get(TBL_COMMUNICATIONS_MANAGER . ' cm');
            return $query->num_rows();
        }
    }

    /**
     * Get communication manager details of particular id
     * @param type $id
     */
    public function get_communication_manager_details($id) {
        $this->db->select('cm.*,c.note,CONCAT(d.firstname, " ",d.lastname) AS donor_fullname,d.email as donor_email,d.phone as donor_phone,CONCAT(g.firstname, " ",g.lastname) AS guest_fullname,g.email as guest_email,g.phone as guest_phone,a.action_matters_campaign,a.vendor_name,a.email as account_email,a.phone as account_phone');
        $this->db->join(TBL_COMMUNICATIONS . ' as c', 'cm.communication_id=c.id', 'left');
//        $this->db->join(TBL_USERS . ' as u', 'cm.user_id=u.id', 'left');
        $this->db->join(TBL_DONORS . ' as d', 'c.donor_id=d.id', 'left');
        $this->db->join(TBL_GUESTS . ' as g', 'c.guest_id=g.id', 'left');
        $this->db->join(TBL_ACCOUNTS . ' as a', 'c.account_id=a.id', 'left');
        $this->db->where('cm.id', $id);
        $query = $this->db->get(TBL_COMMUNICATIONS_MANAGER . ' cm');
        return $query->row_array();
    }

}
