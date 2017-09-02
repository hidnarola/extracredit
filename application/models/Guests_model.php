<?php

/**
 * Manage donors table related database operations
 * @author REP
 */
class Guests_model extends MY_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Get donors for datatable
     * @param string $type - Either result or count
     * @return array for result or int for count
     */
    public function get_guests($type = 'result') {
        $columns = ['id', 'logo', 'action_matters_campaign,vendor_name', 'g.firstname', 'g.lastname', 'g.companyname', 'g.email', 'c.name', 'g.created'];
//        $columns = ['id','logo', 'action_matters_campaign,vendor_name', 'g.firstname', 'g.lastname','g.companyname', 'g.email', 'c.name','g.invite_date', 'g.created'];
        $keyword = $this->input->get('search');
        $this->db->select('g.*,a.action_matters_campaign,a.vendor_name,f.type as fund_type,c.name as city,f.is_vendor');

        $this->db->join(TBL_ACCOUNTS . ' as a', 'g.account_id=a.id', 'left');
        $this->db->join(TBL_FUND_TYPES . ' as f', 'a.fund_type_id=f.id', 'left');
        $this->db->join(TBL_CITIES . ' as c', 'a.city_id=c.id', 'left');

        if (!empty($keyword['value'])) {
            $this->db->where('(action_matters_campaign LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR vendor_name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR g.firstname LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR g.lastname LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR g.companyname LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR g.email LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR c.name LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') .
                    ' OR f.type LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
        }

        $this->db->where(['g.is_delete' => 0]);
        $this->db->order_by($columns[$this->input->get('order')[0]['column']], $this->input->get('order')[0]['dir']);
        if ($type == 'result') {
            $this->db->limit($this->input->get('length'), $this->input->get('start'));
            $query = $this->db->get(TBL_GUESTS . ' g');
            return $query->result_array();
        } else {
            $query = $this->db->get(TBL_GUESTS . ' g');
            return $query->num_rows();
        }
    }

    /**
     * Get guest details of particular id
     * @param int $id
     */
    public function get_guest_details($id) {
        $this->db->select('g.*,a.fund_type_id');
        $this->db->join(TBL_ACCOUNTS . ' as a', 'g.account_id=a.id', 'left');
        $this->db->where(['g.id' => $id, 'g.is_delete' => 0]);
        $query = $this->db->get(TBL_GUESTS . ' g');
        return $query->row_array();
    }

    /**
     * Check email exist or not for unique email
     * @param string $email
     * @return array
     */
    public function check_unique_email($email) {
        $this->db->where('email', $email);
        $this->db->where('is_delete', 0);
        $query = $this->db->get(TBL_GUESTS);
        return $query->row_array();
    }

    /**
     * At edit time check email unique
     * @param type $email
     * @param type $id
     * @return int
     */
    function check_email_edit($email, $id) {
        $this->db->select('email');
        $this->db->where('is_delete!=', 1);
        $this->db->where('email=', $email);
        $this->db->where('id!=', $id);
        $result = $this->db->get(TBL_GUESTS);
        if ($result->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Get guest communication for datatable
     * @param string $type - Either result or count
     * @return array for result or int for count
     */
    public function get_guests_communication($type = 'result', $id) {
        $columns = ['id', 'c.note', 'c.media', 'c.created'];
        $keyword = $this->input->get('search');
        $this->db->select('c.*');

        if (!empty($keyword['value'])) {
            $this->db->where('(note LIKE ' . $this->db->escape('%' . $keyword['value'] . '%') . ')');
        }
        $this->db->where(['c.is_delete' => 0]);
        $this->db->where(['c.guest_id' => $id]);
        $this->db->where(['c.type' => 2]);
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
     */
    public function get_guest_communication_details($id) {
        $this->db->select('gc.*,gc.id as conversation_id,g.firstname,g.lastname');
        $this->db->join(TBL_GUESTS . ' as g', 'g.id=gc.guest_id', 'left');
        $this->db->where(['gc.id' => $id, 'gc.is_delete' => 0]);
        $query = $this->db->get(TBL_COMMUNICATIONS . ' gc');
        return $query->row_array();
    }

}
