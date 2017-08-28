<?php

/**
 * Manage users table related database operation
 * @author KU
 */
class Settings_model extends MY_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Return settings set by admin
     * @param string/array $where
     * @return array
     */
    public function get_settings($where = NULL) {
        if (!is_null($where)) {
            $this->db->where($where);
        }
        return $this->db->get(TBL_SETTINGS)->result_array();
    }

}
