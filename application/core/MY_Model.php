<?php

/**
 * MY_Model for common model function
 * @author KU
 * */
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * This function used to add or update records in particular table based on condition. 
     * @param String $mode (insert/update)
     * @param String $table
     * @param Array $dataArr        	
     * @param Array $condition
     * @return Integer $affected_row
     * */
    public function common_insert_update($mode = '', $table = '', $dataArr = '', $condition = '') {
        if ($mode == 'insert') {
            $this->db->insert($table, $dataArr);
            $affected_row = $this->db->insert_id(); // fetch last inserted id in table
        } else if ($mode == 'update') {
            $this->db->where($condition);
            $this->db->update($table, $dataArr);
            $affected_row = $this->db->affected_rows();
        }
        return $affected_row;
    }

    /**
     * This function returns the table contents based on data. 
     * @param String $table
     * @param Array $condition        	
     * @param Array $sortArr
     * @return Array
     * @author pav
     * */
    public function get_all_details($table = '', $condition = '', $sortArr = '', $limitArr = '') {
        if ($sortArr != '' && is_array($sortArr)) {
            foreach ($sortArr as $sortRow) {
                if (is_array($sortRow)) {
                    $this->db->order_by($sortRow ['field'], $sortRow ['type']);
                }
            }
        }
        if ($limitArr != '') {
            return $this->db->get_where($table, $condition, $limitArr['l1'], $limitArr['l2']);
        } else {
            return $this->db->get_where($table, $condition);
        }
    }

    /**
     * Master function to select required data from DB
     * @param string $table - name of the table
     * @param string $select - select statement e.g user.id or MAX(user.id)
     * @param type $where - mixed optional where condition ex : ['where_in'=> ['users.id' => 5]]
     * @param type $options
     * @return type
     */
    public function sql_select($table, $select = null, $where = null, $options = null) {

        if (!empty($select)) {
            $this->db->select($select);
        }

        $this->db->from($table);

        /* Check wheather where conditions is required or not. */
        if (!empty($where)) {
            if (is_array($where)) {
                $check_where = array(
                    'where',
                    'or_where',
                    'where_in',
                    'or_where_in',
                    'where_not_in',
                    'or_where_not_in',
                    'like', 'or_like',
                    'not_like',
                    'or_not_like'
                );

                foreach ($where as $key => $value) {
                    if (in_array($key, $check_where)) {
                        foreach ($value as $k => $v) {
                            if (in_array($key, array('like', 'or_like', 'not_like', 'or_not_like'))) {
                                $check = 'both';
                                if ($v[0] == '%') {
                                    $check = 'before';
                                    $v = ltrim($v, '%');
                                } else if ($v[strlen($v) - 1] == '%') {
                                    $check = 'after';
                                    $v = rtrim($v, '%');
                                }
                                $this->db->$key($k, $v, $check);
                            } else {
                                $this->db->$key($k, $v);
                            }
                        }
                    }
                }
            } else {
                $this->db->where($where);
            }
        }

        /* Check fourth parameter is passed and process 4th param. */
        if (!empty($options) && is_array($options)) {
            $check_key = array('group_by', 'order_by');
            foreach ($options as $key => $value) {
                if (in_array($key, $check_key)) {
                    if (is_array($value)) {
                        foreach ($value as $k => $v) {
                            $this->db->$key($v);
                        }
                    } else {
                        $this->db->$key($value);
                    }
                }
            }
        }

        /* Check query needs to limit or not.  */
        if (isset($options['limit']) && !empty($options['limit']) && isset($options['offset']) && !empty($options['offset'])) {
            $this->db->limit($options['limit'], $options['offset']);
        } else if (isset($options['limit'])) {
            $this->db->limit($options['limit']);
        }

        /* Check tables need to join or not */
        if (isset($options['join']) && !empty($options['join'])) {
            foreach ($options['join'] as $join) {
                if (!isset($join['join']))
                    $join['join'] = 'left';
                $this->db->join($join['table'], $join['condition'], $join['join']);
            }
        }


        $method = 'result_array';
        /* Check wheather return only single row or not. */
        if (isset($options) && ((!is_array($options) && $options == true) || (isset($options['single']) && $options['single'] == true ))) {
            $method = 'row_array';
        }

        /* Check to return only count or full data */
        if (isset($options['count']) && $options['count'] == true) {
            return $this->db->count_all_results();
        } else {
            return $this->db->get()->$method();
        }
    }

    /**
     * This function used to add/edit in bulk. 
     * @param String $mode
     * @param String $table
     * @param Array $dataArr          
     * @param String $column
     * @param String $condition
     * */
    public function batch_insert_update($mode = '', $table = '', $dataArr = '', $column = '', $condition = NULL) {
        if ($mode == 'insert') {
            $this->db->insert_batch($table, $dataArr);
        } else if ($mode == 'update') {
            if (!is_null($condition)) {
                $this->db->where($condition);
            }
            $this->db->update_batch($table, $dataArr, $column);
        }
    }

    /**
     * Common function to update admin fund
     * @param float $amount
     */
    public function update_admin_fund($amount) {
        $this->db->where(['id' => 1]);
        $this->db->update(TBL_USERS, ['total_fund' => $amount]);
    }

    /**
     * Custom Query
     * $option = 1 if return result array
     * $option = 2 if return row array
     */
    public function customQuery($query, $option) {
        $result = $this->db->query($query);
        if ($option == 1) {
            return $result->result_array();
        } else {
            return $result->row_array();
        }
    }

    /**
     * Custom Query
     * @author REP
     */
    public function custom_Query($query) {
        $result = $this->db->query($query);
        return $result;
    }

    /**
     * This function used to check Privileges for a particular user (page wise).
     * @param String $page_name
     * @param String $user_id
     * @return Object 
     * @author PAV
     * */
    public function checkPrivleges($page_name = '', $user_id = '') {
        $this->db->select('up.*');
        $this->db->from(TBL_USER_PERMISSION . ' as up');
        $this->db->join(TBL_USERS . ' as u', 'up.user_id=u.id', 'left');
        $this->db->join(TBL_PAGES . ' as p', 'up.page_id=p.id', 'left');
        $this->db->where(
                array(
                    'p.page_name' => $page_name,
                    'u.is_delete' => 0,
                    'u.is_active' => 1,
                    'up.user_id' => $user_id
                )
        );
        return $this->db->get();
    }

    /**
     * Returns number of records by date as key array
     * @param string $table - Table name
     * @param string $where - Where condition
     * @return array
     */
    public function num_of_records_by_date($table, $where = NULL) {
        $this->db->select("count(id) as count,DATE_FORMAT(created,'%Y-%m-%d') as date");
        if (!is_null($where)) {
            $this->db->where($where);
        }
        $this->db->group_by("DATE_FORMAT(created,'%Y-%m-%d')");
        $query = $this->db->get($table);
        return $query->result_array();
    }

}

/* End of file MY_Model.php */
/* Location: ./application/core/MY_Model.php */