<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Check if client have transactions recorded
 * @param  mixed $id clientid
 * @return boolean
 */
function client_have_transactions($id)
{
    $total_transactions = 0;
    $total_transactions += total_rows('tblinvoices', array(
        'clientid' => $id
    ));
    $total_transactions += total_rows('tblestimates', array(
        'clientid' => $id
    ));
    $total_transactions += total_rows('tblexpenses', array(
        'clientid' => $id,
        'billable' => 1
    ));
    $total_transactions += total_rows('tblproposals', array(
        'rel_id' => $id,
        'rel_type' => 'customer'
    ));

    if ($total_transactions > 0) {
        return true;
    }
    return false;
}
/**
 * Get option value
 * @param  string $name Option name
 * @return mixed
 */
function getMaxID($id,$table)
{

    $CI =& get_instance();
    if (!class_exists('perfex_base')) {
        $CI->load->library('perfex_base');
    }
    return $CI->perfex_base->getMaxID($id,$table);
}

function getRow($table,$where=array())
{

    $CI =& get_instance();
    if (!class_exists('perfex_base')) {
        $CI->load->library('perfex_base');
    }
    return $CI->perfex_base->getRow($table,$where);
}

function getProvince($id)
{

    $CI =& get_instance();
    if (!class_exists('perfex_base')) {
        $CI->load->library('perfex_base');
    }
    return $CI->perfex_base->getProvince($id);
}

function getDistrict($id)
{

    $CI =& get_instance();
    if (!class_exists('perfex_base')) {
        $CI->load->library('perfex_base');
    }
    return $CI->perfex_base->getDistrict($id);
}

function getWard($id)
{

    $CI =& get_instance();
    if (!class_exists('perfex_base')) {
        $CI->load->library('perfex_base');
    }
    return $CI->perfex_base->getWard($id);
}
// Increase Quanity Product In Warehouse
function increaseProductQuantity($warehouse_id,$product_id,$quantity)
{
    $CI =& get_instance();
    if (!class_exists('perfex_base')) {
        $CI->load->library('perfex_base');
    }
    return $CI->perfex_base->increaseProductQuantity($warehouse_id,$product_id,$quantity);
}

// Decrease Quanity Product In Warehouse
function decreaseProductQuantity($warehouse_id,$product_id,$quantity)
{
    $CI =& get_instance();
    if (!class_exists('perfex_base')) {
        $CI->load->library('perfex_base');
    }
    return $CI->perfex_base->decreaseProductQuantity($warehouse_id,$product_id,$quantity);
}

function getWHTIDByWHID($warehouse_id)
{
    $CI =& get_instance();
    if (!class_exists('perfex_base')) {
        $CI->load->library('perfex_base');
    }
    return $CI->perfex_base->getWHTIDByWHID($warehouse_id);
}


function getClient($id,$address_type=NULL)
{

    $CI =& get_instance();
    if (!class_exists('perfex_base')) {
        $CI->load->library('perfex_base');
    }
    return $CI->perfex_base->getClient($id,$address_type);
}

function getSupllier($id,$address_type=NULL)
{

    $CI =& get_instance();
    if (!class_exists('perfex_base')) {
        $CI->load->library('perfex_base');
    }
    return $CI->perfex_base->getSupllier($id,$address_type);
}

function getCurrencyByID($id)
{

    $CI =& get_instance();
    if (is_numeric($id)) {
        $currency=$CI->db->get_where('tblcurrencies',array('id'=>$id),1)->row();
        if($currency) return $currency;
    }
    return false;
    
}

/**
 * Check if contact id passed is primary contact
 * If you dont pass $contact_id the current logged in contact will be checked
 * @param  string  $contact_id
 * @return boolean
 */
function is_primary_contact($contact_id = '')
{
    if (!is_numeric($contact_id)) {
        $contact_id = get_contact_user_id();
    }

    if (total_rows('tblcontacts', array(
            'id' => $contact_id,
            'is_primary' => 1
        )) > 0) {
        return true;
    }

    return false;
}
/**
 * Function used to check if is really empty customer company
 * Can happen user to have selected that the company field is not required and the primary contact name is auto added in the company field
 * @param  mixed  $id
 * @return boolean
 */
function is_empty_customer_company($id)
{
    $CI =& get_instance();
    $CI->db->select('company');
    $CI->db->from('tblclients');
    $CI->db->where('userid', $id);
    $row = $CI->db->get()->row();
    if ($row) {
        if ($row->company == '') {
            return true;
        }
        return false;
    }
    return true;
}
/**
 * Get project name by passed id
 * @param  mixed $id
 * @return string
 */
function get_project_name_by_id($id)
{

    $CI =& get_instance();
    $CI->db->select('name');
    $CI->db->where('id', $id);
    $project = $CI->db->get('tblprojects')->row();
    if ($project) {
        return $project->name;
    }

    return '';
}
/**
 * Get ids to check what files with contacts are shared
 * @param  array  $where
 * @return array
 */
function get_customer_profile_file_sharing($where = array())
{
    $CI =& get_instance();
    $CI->db->where($where);
    return $CI->db->get('tblcustomerfiles_shares')->result_array();
}
/**
 * Check if field is used in table
 * @param  string  $field column
 * @param  string  $table table name to check
 * @param  integer  $id   ID used
 * @return boolean
 */
function is_reference_in_table($field, $table, $id)
{
    $CI =& get_instance();
    $CI->db->where($field, $id);
    $row = $CI->db->get($table)->row();
    if ($row) {
        return true;
    }
    return false;
}
/**
 * Function that add views tracking for proposals,estimates,invoices,knowledgebase article in database.
 * This function tracks activity only per hour
 * Eq customer viewed invoice at 15:00 and then 15:05 the activity will be tracked only once.
 * If customer view the invoice again in 16:01 there will be activity tracked.
 * @param string $rel_type
 * @param mixed $rel_id
 */
function add_views_tracking($rel_type, $rel_id)
{
    $CI =& get_instance();
    if (!is_staff_logged_in()) {
        $CI->db->where('rel_id', $rel_id);
        $CI->db->where('rel_type', $rel_type);
        $CI->db->order_by('id', 'DESC');
        $CI->db->limit(1);
        $row = $CI->db->get('tblviewstracking')->row();
        if ($row) {
            $dateFromDatabase = strtotime($row->date);
            $date1HourAgo     = strtotime("-1 hours");
            if ($dateFromDatabase >= $date1HourAgo) {
                return false;
            }
        }
    }

    do_action('before_insert_views_tracking', array(
        'rel_id' => $rel_id,
        'rel_type' => $rel_type
    ));

    $CI->db->insert('tblviewstracking', array(
        'rel_id' => $rel_id,
        'rel_type' => $rel_type,
        'date' => date('Y-m-d H:i:s'),
        'view_ip' => $CI->input->ip_address()
    ));

}
/**
 * Get views tracking based on rel type and rel id
 * @param  string $rel_type
 * @param  mixed $rel_id
 * @return array
 */
function get_views_tracking($rel_type, $rel_id)
{
    $CI =& get_instance();
    $CI->db->where('rel_id', $rel_id);
    $CI->db->where('rel_type', $rel_type);
    $CI->db->order_by('date', 'DESC');
    return $CI->db->get('tblviewstracking')->result_array();
}
/**
 * Get customer id by passed contact id
 * @param  mixed $id
 * @return mixed
 */
function get_user_id_by_contact_id($id)
{
    $CI =& get_instance();
    $CI->db->select('userid');
    $CI->db->where('id', $id);
    $client = $CI->db->get('tblcontacts')->row();
    if ($client) {
        return $client->userid;
    }
    return false;
}
/**
 * Add option in table
 * @since  Version 1.0.1
 * @param string $name  option name
 * @param string $value option value
 */
function add_option($name, $value = '')
{
    $CI =& get_instance();
    $exists = total_rows('tbloptions', array(
        'name' => $name
    ));
    if ($exists == 0) {
        $CI->db->insert('tbloptions', array(
            'name' => $name,
            'value' => $value
        ));
        $insert_id = $CI->db->insert_id();
        if ($insert_id) {
            return true;
        }
        return false;
    }
    return false;
}
/**
 * Get primary contact user id for specific customer
 * @param  mixed $userid
 * @return mixed
 */
function get_primary_contact_user_id($userid)
{

    $CI =& get_instance();
    $CI->db->where('userid', $userid);
    $CI->db->where('is_primary', 1);
    $row = $CI->db->get('tblcontacts')->row();

    if ($row) {
        return $row->id;
    }
    return false;
}

/**
 * Get option value
 * @param  string $name Option name
 * @return mixed
 */
function get_option($name)
{

    $CI =& get_instance();
    if (!class_exists('perfex_base')) {
        $CI->load->library('perfex_base');
    }
    return $CI->perfex_base->get_option($name);
}

function check_option($name) {
    $CI =& get_instance();
    if (!class_exists('perfex_base')) {
        $CI->load->library('perfex_base');
    }
    return $CI->perfex_base->check_option($name);
}

function getWareHouse($id)
{

    $CI =& get_instance();
    if (!class_exists('perfex_base')) {
        $CI->load->library('perfex_base');
    }
    return $CI->perfex_base->getWareHouse($id);
}

// function getProvince($id)
// {

//     $CI =& get_instance();
//     if (!class_exists('perfex_base')) {
//         $CI->load->library('perfex_base');
//     }
//     return $CI->perfex_base->getProvince($id);
// }
// function getDistrict($id)
// {

//     $CI =& get_instance();
//     if (!class_exists('perfex_base')) {
//         $CI->load->library('perfex_base');
//     }
//     return $CI->perfex_base->getDistrict($id);
// }
// function getAward($id)
// {

//     $CI =& get_instance();
//     if (!class_exists('perfex_base')) {
//         $CI->load->library('perfex_base');
//     }
//     return $CI->perfex_base->getAward($id);
// }
/**
 * Get option value from database
 * @param  string $name Option name
 * @return mixed
 */
function update_option($name, $value)
{
    $CI =& get_instance();
    $CI->db->where('name', $name);
    $CI->db->update('tbloptions', array(
        'value' => $value
    ));
    if ($CI->db->affected_rows() > 0) {
        return true;
    }
    return false;
}
/**
 * Delete option
 * @since  Version 1.0.4
 * @param  mixed $id option id
 * @return boolean
 */
function delete_option($id)
{
    $CI =& get_instance();
    $CI->db->where('id', $id);
    $CI->db->or_where('name', $id);
    $CI->db->delete('tbloptions');
    if ($CI->db->affected_rows() > 0) {
        return true;
    }
    return false;
}

/**
 * Get staff full name
 * @param  string $userid Optional
 * @return string Firstname and Lastname
 */
function get_staff_full_name($userid = '')
{
    $_userid = get_staff_user_id();
    if ($userid !== '') {
        $_userid = $userid;
    }
    $CI =& get_instance();
    $CI->db->where('staffid', $_userid);
    $staff = $CI->db->select('firstname,lastname,fullname')->from('tblstaff')->get()->row();
    if ($staff) {
        return ($staff->fullname)? ($staff->fullname) : ($staff->firstname . ' ' . $staff->lastname);
    } else {
        return '';
    }

}
/**
 * Get client full name
 * @param  string $userid Optional
 * @return string Firstname and Lastname
 */
function get_contact_full_name($userid = '')
{
    $_userid = get_contact_user_id();
    if ($userid !== '') {
        $_userid = $userid;
    }
    $CI =& get_instance();
    $CI->db->where('id', $_userid);
    $client = $CI->db->select('firstname,lastname')->from('tblcontacts')->get()->row();
    if (!empty($client->firstname) && !empty($client->lastname)) {
        return $client->firstname . ' ' . $client->lastname;
    } else {
        return '';
    }

}
/**
 * This function currently is used in search for contact, ticket add for contacts and ticket edit for contacts
 * @param  [type] $userid [description]
 * @return [type]         [description]
 */
function get_company_name($userid, $prevent_empty_company = TRUE)
{
    $_userid = get_client_user_id();
    if ($userid !== '') {
        $_userid = $userid;
    }
    $CI =& get_instance();
    $CI->db->where('userid', $_userid);
    $client = $CI->db->select(($prevent_empty_company == false ? 'CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END as company' : 'company'))->from('tblclients')->get()->row();
    if ($client) {
        return $client->company;
    } else {
        return '';
    }
}
/**
 * Get client default language
 * @param  mixed $clientid
 * @return mixed
 */
function get_client_default_language($clientid = '')
{
    if (!is_numeric($clientid)) {
        $clientid = get_client_user_id();
    }
    $CI =& get_instance();
    $CI->db->select('default_language');
    $CI->db->from('tblclients');
    $CI->db->where('userid', $clientid);
    $client = $CI->db->get()->row();
    if ($client) {
        return $client->default_language;
    }
    return '';
}
/**
 * Get staff default language
 * @param  mixed $staffid
 * @return mixed
 */
function get_staff_default_language($staffid = '')
{
    if (!is_numeric($staffid)) {
        $staffid = get_staff_user_id();
    }
    $CI =& get_instance();
    $CI->db->select('default_language');
    $CI->db->from('tblstaff');
    $CI->db->where('staffid', $staffid);
    $staff = $CI->db->get()->row();
    if ($staff) {
        return $staff->default_language;
    }
    return '';
}
/**
 * Log Activity for everything
 * @param  string $description Activity Description
 * @param  integer $staffid    Who done this activity
 */
function logActivity($description, $staffid = NULL)
{
    $CI =& get_instance();
    $log = array(
        'description' => $description,
        'date' => date('Y-m-d H:i:s')
    );
    if (!DEFINED('CRON')) {
        if ($staffid != NULL && is_numeric($staffid)) {
            $log['staffid'] = get_staff_full_name($staffid);
        } else {
            if (is_staff_logged_in()) {
                $log['staffid'] = get_staff_full_name(get_staff_user_id());
            } else {
                $log['staffid'] = NULL;
            }
        }
    } else {
        // manually invoked cron
        if (is_staff_logged_in()) {
            $log['staffid'] = get_staff_full_name(get_staff_user_id());
        } else {
            $log['staffid'] = '[CRON]';
        }
    }

    $CI->db->insert('tblactivitylog', $log);
}
/**
 * Note well tested function do not use it, is optimized only when doing updates in the menu items
 */
function add_main_menu_item($options = array(), $parent = '')
{
    $default_options = array(
        'name',
        'permission',
        'icon',
        'url',
        'id'
    );
    $order           = '';
    if (isset($options['order'])) {
        $order = $options['order'];
        unset($options['order']);
    }
    $data = array();
    for ($i = 0; $i < count($default_options); $i++) {
        if (isset($options[$default_options[$i]])) {
            $data[$default_options[$i]] = $options[$default_options[$i]];
        } else {
            $data[$default_options[$i]] = '';
        }
    }
    $menu = get_option('aside_menu_active');
    $menu = json_decode($menu);
    // check if the id exists
    if ($data['id'] == '') {
        $data['id'] = slug_it($data['name']);
    }
    $total_exists = 0;
    foreach ($menu->aside_menu_active as $item) {
        if ($item->id == $data['id']) {
            $total_exists++;
        }
    }
    if ($total_exists > 0) {
        return false;
    }
    $_data = new stdClass();
    foreach ($data as $key => $val) {
        $_data->{$key} = $val;
    }

    $data = $_data;
    if ($parent == '') {
        if ($order == '') {
            array_push($menu->aside_menu_active, $data);
        } else {
            if ($order == 1) {
                array_unshift($menu->aside_menu_active, array());
            } else {
                $order = $order - 1;
                array_splice($menu->aside_menu_active, $order, 0, array(
                    ''
                ));
            }
            $menu->aside_menu_active[$order] = $_data;
        }
    } else {
        $i = 0;
        foreach ($menu->aside_menu_active as $item) {
            if ($item->id == $parent) {
                if (!isset($item->children)) {
                    $menu->aside_menu_active[$i]->children   = array();
                    $menu->aside_menu_active[$i]->children[] = $data;
                    break;
                } else {
                    if ($order == '') {
                        $menu->aside_menu_active[$i]->children[] = $data;
                    } else {
                        if ($order == 1) {
                            array_unshift($menu->aside_menu_active[$i]->children, array());
                        } else {
                            $order = $order - 1;
                            array_splice($menu->aside_menu_active[$i]->children, $order, 0, array(
                                ''
                            ));
                        }
                        $menu->aside_menu_active[$i]->children[$order] = $data;
                    }
                    break;
                }
            }
            $i++;
        }
    }
    if (update_option('aside_menu_active', json_encode($menu))) {
        return true;
    }
    return false;
}
/**
 * Note well tested function do not use it, is optimized only when doing updates in the menu items
 */
function add_setup_menu_item($options = array(), $parent = '')
{
    $default_options = array(
        'name',
        'permission',
        'icon',
        'url',
        'id'
    );
    $order           = '';
    if (isset($options['order'])) {
        $order = $options['order'];
        unset($options['order']);
    }
    $data = array();
    for ($i = 0; $i < count($default_options); $i++) {
        if (isset($options[$default_options[$i]])) {
            $data[$default_options[$i]] = $options[$default_options[$i]];
        } else {
            $data[$default_options[$i]] = '';
        }
    }
    if ($data['id'] == '') {
        $data['id'] = slug_it($data['name']);
    }

    $menu = get_option('setup_menu_active');
    $menu = json_decode($menu);
    // check if the id exists
    if ($data['id'] == '') {
        $data['id'] = slug_it($data['name']);
    }
    $total_exists = 0;
    foreach ($menu->setup_menu_active as $item) {
        if ($item->id == $data['id']) {
            $total_exists++;
        }
    }
    if ($total_exists > 0) {
        return false;
    }
    $_data = new stdClass();
    foreach ($data as $key => $val) {
        $_data->{$key} = $val;
    }
    $data = $_data;
    if ($parent == '') {
        if ($order == 1) {
            array_unshift($menu->setup_menu_active, array());
        } else {
            $order = $order - 1;
            array_splice($menu->setup_menu_active, $order, 0, array(
                ''
            ));
        }
        $menu->setup_menu_active[$order] = $_data;
    } else {
        $i = 0;
        foreach ($menu->setup_menu_active as $item) {
            if ($item->id == $parent) {
                if (!isset($item->children)) {
                    $menu->setup_menu_active[$i]->children   = array();
                    $menu->setup_menu_active[$i]->children[] = $data;
                    break;
                } else {
                    $menu->setup_menu_active[$i]->children[] = $data;
                    break;
                }
            }
            $i++;
        }
    }
    if (update_option('setup_menu_active', json_encode($menu))) {
        return true;
    }
    return false;
}
/**
 * Add user notifications
 * @param array $values array of values [description,fromuserid,touserid,fromcompany,isread]
 */
function add_notification($values)
{
    $CI =& get_instance();
    foreach ($values as $key => $value) {
        $data[$key] = $value;
    }
    if (is_client_logged_in()) {
        $data['fromuserid']    = 0;
        $data['fromclientid']  = get_contact_user_id();
        $data['from_fullname'] = get_contact_full_name(get_contact_user_id());
    } else {
        $data['fromuserid']    = get_staff_user_id();
        $data['fromclientid']  = 0;
        $data['from_fullname'] = get_staff_full_name(get_staff_user_id());
    }

    if (isset($data['fromcompany'])) {
        unset($data['fromuserid']);
        unset($data['from_fullname']);
    }

    // Prevent sending notification to non active users.
    if (isset($data['touserid']) && $data['touserid'] != 0) {
        $CI->db->where('staffid', $data['touserid']);
        $user = $CI->db->get('tblstaff')->row();
        if (!$user) {
            return false;
        }
        if ($user) {
            if ($user->active == 0) {
                return false;
            }
        }
    }
    $data['date'] = date('Y-m-d H:i:s');
    $CI->db->insert('tblnotifications', $data);
}
/**
 * Count total rows on table based on params
 * @param  string $table Table from where to count
 * @param  array  $where
 * @return mixed  Total rows
 */
function total_rows($table, $where = array())
{
    $CI =& get_instance();
    if (is_array($where)) {
        if (sizeof($where) > 0) {
            $CI->db->where($where);
        }
    } else if (strlen($where) > 0) {
        $CI->db->where($where);
    }
    return $CI->db->count_all_results($table);
}
/**
 * Sum total from table
 * @param  string $table table name
 * @param  array  $attr  attributes
 * @return mixed
 */
function sum_from_table($table, $attr = array())
{
    if (!isset($attr['field'])) {
        show_error('sum_from_table(); function expect field to be passed.');
    }

    $CI =& get_instance();
    if (isset($attr['where']) && is_array($attr['where'])) {
        $i = 0;
        foreach ($attr['where'] as $key => $val) {
            if (is_numeric($key)) {
                $CI->db->where($val);
                unset($attr['where'][$key]);
            }
            $i++;
        }
        $CI->db->where($attr['where']);
    }
    $CI->db->select_sum($attr['field']);
    $CI->db->from($table);
    $result = $CI->db->get()->row();
    return $result->{$attr['field']};
}
/**
 * General function for all datatables, performs search,additional select,join,where,orders
 * @param  array $aColumns           table columns
 * @param  mixed $sIndexColumn       main column in table for bettter performing
 * @param  string $sTable            table name
 * @param  array  $join              join other tables
 * @param  array  $where             perform where in query
 * @param  array  $additionalSelect  select additional fields
 * @param  string $orderby
 * @param  string $groupBy - note yet tested
 * @return array
 */
function data_tables_init($aColumns, $sIndexColumn, $sTable, $join = array(), $where = array(), $additionalSelect = array(), $orderby = '', $groupBy = '')
{
    $CI =& get_instance();
    $__post = $CI->input->post();

    /*
     * Paging
     */
    $sLimit = "";
    if ((is_numeric($CI->input->post('start'))) && $CI->input->post('length') != '-1') {
        $sLimit = "LIMIT " . intval($CI->input->post('start')) . ", " . intval($CI->input->post('length'));
    }
    $_aColumns = array();
    foreach ($aColumns as $column) {
        // if found only one dot
        if (substr_count($column, '.') == 1 && strpos($column, ' as ') === false) {
            $_column = explode('.', $column);
            if (isset($_column[1])) {
                if (_startsWith($_column[0], 'tbl')) {
                    $_prefix = prefixed_table_fields_wildcard($_column[0], $_column[0], $_column[1]);
                    array_push($_aColumns, $_prefix);
                } else {
                    array_push($_aColumns, $column);
                }
            } else {
                array_push($_aColumns, $_column[0]);
            }
        } else {
            array_push($_aColumns, $column);
        }
    }
    /*
     * Ordering
     */
    $sOrder = "";
    if ($CI->input->post('order')) {
        $sOrder = "ORDER BY  ";
        foreach ($CI->input->post('order') as $key => $val) {

            $sOrder .= $aColumns[intval($__post['order'][$key]['column'])];

            $__order_column = $sOrder;
            if (strpos($__order_column, ' as ') !== false) {
                $sOrder = strbefore($__order_column, ' as');
            }
            $_order = strtoupper($__post['order'][$key]['dir']);
            if ($_order == 'ASC') {
                $sOrder .= ' ASC';
            } else {
                $sOrder .= ' DESC';
            }
            $sOrder .= ', ';
        }
        if (trim($sOrder) == "ORDER BY") {
            $sOrder = "";
        }
        if ($sOrder == '' && $orderby != '') {
            $sOrder = $orderby;
        } else {
            $sOrder = substr($sOrder, 0, -2);
        }

    } else {
        $sOrder = $orderby;
    }
    /*
     * Filtering
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here, but concerned about efficiency
     * on very large tables, and MySQL's regex functionality is very limited
     */
    $sWhere = "";
    if ((isset($__post['search'])) && $__post['search']['value'] != "") {
        $search_value = $__post['search']['value'];

        $sWhere = "WHERE (";
        for ($i = 0; $i < count($aColumns); $i++) {
            $__search_column = $aColumns[$i];
            if (strpos($__search_column, ' as ') !== false) {
                $__search_column = strbefore($__search_column, ' as');
            }
            if (($__post['columns'][$i]) && $__post['columns'][$i]['searchable'] == "true") {
                $sWhere .= $__search_column . " LIKE '%" . $search_value . "%' OR ";
            }
        }
        if (count($additionalSelect) > 0) {
            foreach ($additionalSelect as $searchAdditionalField) {
                if (strpos($searchAdditionalField, ' as ') !== false) {
                    $searchAdditionalField = strbefore($searchAdditionalField, ' as');
                }

                $sWhere .= $searchAdditionalField . " LIKE '%" . $search_value . "%' OR ";
            }
        }
        $sWhere = substr_replace($sWhere, "", -3);
        $sWhere .= ')';
    } else {
        // Check for custom filtering
        $searchFound = 0;
        $sWhere      = "WHERE (";
        for ($i = 0; $i < count($aColumns); $i++) {
            if (($__post['columns'][$i]) && $__post['columns'][$i]['searchable'] == "true") {
                $search_value    = $__post['columns'][$i]['search']['value'];
                $__search_column = $aColumns[$i];
                if (strpos($__search_column, ' as ') !== false) {
                    $__search_column = strbefore($__search_column, ' as');
                }
                if ($search_value != '') {
                    $sWhere .= $__search_column . " LIKE '%" . $search_value . "%' OR ";
                    if (count($additionalSelect) > 0) {
                        foreach ($additionalSelect as $searchAdditionalField) {
                            $sWhere .= $searchAdditionalField . " LIKE '%" . $search_value . "%' OR ";
                        }
                    }
                    $searchFound++;
                }
            }
        }
        if ($searchFound > 0) {
            $sWhere = substr_replace($sWhere, "", -3);
            $sWhere .= ')';
        } else {
            $sWhere = '';
        }
    }

    /*
     * SQL queries
     * Get data to display
     */
    $_additionalSelect = '';
    if (count($additionalSelect) > 0) {
        $_additionalSelect = ',' . implode(',', $additionalSelect);
    }
    $where = implode(' ', $where);
    if ($sWhere == '') {
        $where = trim($where);
        if (_startsWith($where, 'AND') || _startsWith($where, 'OR')) {
            if (_startsWith($where, 'OR')) {
                $where = substr($where, 2);
            } else {
                $where = substr($where, 3);
            }
            $where = 'WHERE ' . $where;
        }
    }
    $sQuery  = "
    SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $_aColumns)) . " " . $_additionalSelect . "
    FROM   $sTable
    " . implode(' ', $join) . "
    $sWhere
    " . $where . "
    $sOrder
    $groupBy
    $sLimit
    ";

//return $sQuery;
    $rResult = $CI->db->query($sQuery)->result_array();

    /* Data set length after filtering */
    $sQuery         = "
    SELECT FOUND_ROWS()
    ";
    $_query         = $CI->db->query($sQuery)->result_array();
    $iFilteredTotal = $_query[0]['FOUND_ROWS()'];
    if (_startsWith($where, 'AND')) {
        $where = 'WHERE ' . substr($where, 3);
    }
    /* Total data set length */
    $sQuery = "
    SELECT COUNT(" . $sTable . '.' . $sIndexColumn . ")
    FROM $sTable " . implode(' ', $join) . ' ' . $where;
    $_query = $CI->db->query($sQuery)->result_array();
    $iTotal = $_query[0]['COUNT(' . $sTable . '.' . $sIndexColumn . ')'];
    /*
     * Output
     */
    $output = array(
        "draw" => $__post['draw'] ? intval($__post['draw']) : 0,
        "iTotalRecords" => $iTotal,
        "iTotalDisplayRecords" => $iFilteredTotal,
        "aaData" => array()
    );

    return array(
        'rResult' => $rResult,
        'output' => $output
    );
}
/**
 * Prefix field name with table ex. table.column
 * @param  string $table
 * @param  string $alias
 * @param  string $field field to check
 * @return string
 */
function prefixed_table_fields_wildcard($table, $alias, $field)
{
    $CI =& get_instance();
    $columns     = $CI->db->query("SHOW COLUMNS FROM $table")->result_array();
    $field_names = array();
    foreach ($columns as $column) {
        $field_names[] = $column["Field"];
    }
    $prefixed = array();
    foreach ($field_names as $field_name) {
        if ($field == $field_name) {
            $prefixed[] = "`{$alias}`.`{$field_name}` AS `{$alias}.{$field_name}`";
        }
    }
    return implode(", ", $prefixed);
}
/**
 * Prefix all columns from table with the table name
 * Used for select statements eq tblclients.company
 * @param  string $table table name
 * @return array
 */
function prefixed_table_fields_array($table)
{
    $CI =& get_instance();
    $fields = $CI->db->list_fields($table);
    $i      = 0;
    foreach ($fields as $f) {
        $fields[$i] = $table . '.' . $f;
        $i++;
    }
    return $fields;
}
/**
 * Function used to get related data based on rel_id and rel_type
 * Eq in the tasks section there is field where this task is related eq invoice with number INV-0005
 * @param  string $type
 * @param  string $rel_id
 * @param  string $connection_type
 * @param  string $connection_id
 * @return mixed
 */
function get_relation_data($type, $rel_id = '', $connection_type = '', $connection_id = '')
{
    $CI =& get_instance();
    $q = '';
    if ($CI->input->post('q')) {
        $q = $CI->input->post('q');
        $q = trim($q);
    }
    $data = array();
    if ($type == 'customer' || $type == 'customers') {
        $where_clients = 'tblclients.active=1';
        if ($connection_id != '') {
            if ($connection_type == 'proposal') {
                $where_clients = 'CASE
                WHEN tblclients.userid NOT IN(SELECT rel_id FROM tblproposals WHERE id=' . $connection_id . ' AND rel_type="customer") THEN tblclients.active=1
                ELSE 1=1
                END';
            } else if ($connection_type == 'task') {
                $where_clients = 'CASE
                WHEN tblclients.userid NOT IN(SELECT rel_id FROM tblstafftasks WHERE id=' . $connection_id . ' AND rel_type="customer") THEN tblclients.active=1
                ELSE 1=1
                END';
            }
        }

        if ($q) {
            $where_clients .= ' AND (company LIKE "%' . $q . '%" OR CONCAT(firstname, " ", lastname) LIKE "%' . $q . '%" OR email LIKE "%' . $q . '%")';
        }

        $data = $CI->clients_model->get($rel_id, $where_clients);

    } else if ($type == 'invoice') {

        if ($rel_id != '') {
            $CI->load->model('invoices_model');
            $data = $CI->invoices_model->get($rel_id);
        } else {
            $search = $CI->misc_model->_search_invoices($q);
            $data   = $search['result'];
        }
    } else if ($type == 'estimate') {
        if ($rel_id != '') {
            $CI->load->model('estimates_model');
            $data = $CI->estimates_model->get($rel_id);
        } else {
            $search = $CI->misc_model->_search_estimates($q);
            $data   = $search['result'];
        }
    } else if ($type == 'contract' || $type == 'contracts') {
        $CI->load->model('contracts_model');

        if ($rel_id != '') {
            $CI->load->model('contracts_model');
            $data = $CI->contracts_model->get($rel_id);
        } else {
            $search = $CI->misc_model->_search_contracts($q);
            $data   = $search['result'];
        }
    } else if ($type == 'ticket') {
        if ($rel_id != '') {
            $CI->load->model('tickets_model');
            $data = $CI->tickets_model->get($rel_id);
        } else {
            $search = $CI->misc_model->_search_tickets($q);
            $data   = $search['result'];
        }
    } else if ($type == 'expense' || $type == 'expenses') {
        if ($rel_id != '') {
            $CI->load->model('expenses_model');
            $data = $CI->expenses_model->get($rel_id);
        } else {
            $search = $CI->misc_model->_search_expenses($q);
            $data   = $search['result'];
        }
    } else if ($type == 'lead' || $type == 'leads') {

        if ($rel_id != '') {
            $CI->load->model('leads_model');
            $data = $CI->leads_model->get($rel_id);
        } else {
            $search = $CI->misc_model->_search_leads($q, 0, array(
                'junk' => 0
            ));
            $data   = $search['result'];
        }
    } else if ($type == 'proposal') {
        if ($rel_id != '') {
            $CI->load->model('proposals_model');
            $data = $CI->proposals_model->get($rel_id);
        } else {
            $search = $CI->misc_model->_search_proposals($q);
            $data   = $search['result'];
        }
    } else if ($type == 'project') {
        if ($rel_id != '') {
            $CI->load->model('projects_model');
            $data = $CI->projects_model->get($rel_id);
        } else {
            $search = $CI->misc_model->_search_projects($q);
            $data   = $search['result'];
        }
    }
    return $data;
}
/**
 * Ger relation values eq invoice number or project name etc based on passed relation parsed results
 * from function get_relation_data
 * $relation can be object or array
 * @param  mixed $relation
 * @param  string $type
 * @return mixed
 */
function get_relation_values($relation, $type)
{
    if ($relation == '') {
        return array(
            'name' => '',
            'id' => '',
            'link' => '',
            'addedfrom' => 0
        );
    }

    $addedfrom = 0;
    $name      = '';
    $id        = '';
    $link      = '';

    if ($type == 'customer' || $type == 'customers') {
        if (is_array($relation)) {
            $id   = $relation['userid'];
            $name = $relation['company'];
        } else {
            $id   = $relation->userid;
            $name = $relation->company;
        }
        $link = admin_url('clients/client/' . $id);
    } else if ($type == 'invoice') {
        if (is_array($relation)) {
            $id        = $relation['id'];
            $name      = format_invoice_number($id);
            $addedfrom = $relation['addedfrom'];
        } else {
            $id        = $relation->id;
            $name      = format_invoice_number($id);
            $addedfrom = $relation->addedfrom;
        }
        $link = admin_url('invoices/list_invoices/' . $id);
    } else if ($type == 'estimate') {
        if (is_array($relation)) {
            $id        = $relation['estimateid'];
            $name      = format_estimate_number($id);
            $addedfrom = $relation['addedfrom'];
        } else {
            $id        = $relation->id;
            $name      = format_estimate_number($id);
            $addedfrom = $relation->addedfrom;
        }
        $link = admin_url('estimates/list_estimates/' . $id);
    } else if ($type == 'contract' || $type == 'contracts') {
        if (is_array($relation)) {
            $id        = $relation['id'];
            $name      = $relation['subject'];
            $addedfrom = $relation['addedfrom'];
        } else {
            $id        = $relation->id;
            $name      = $relation->subject;
            $addedfrom = $relation->addedfrom;
        }
        $link = admin_url('contracts/contract/' . $id);
    } else if ($type == 'ticket') {
        if (is_array($relation)) {
            $id   = $relation['ticketid'];
            $name = '#' . $relation['ticketid'];
            $name .= ' - ' . $relation['subject'];
        } else {
            $id   = $relation->ticketid;
            $name = '#' . $relation->ticketid;
            $name .= ' - ' . $relation->subject;
        }
        $name = _l('ticket') . ' ' . $name;
        $link = admin_url('tickets/ticket/' . $id);
    } else if ($type == 'expense' || $type == 'expenses') {
        if (is_array($relation)) {
            $id        = $relation['expenseid'];
            $name      = $relation['category_name'] . ' - ' . _format_number($relation['amount']);
            $addedfrom = $relation['addedfrom'];

            if (!empty($relation['expense_name'])) {
                $name .= ' (' . $relation['expense_name'] . ')';
            }
        } else {
            $id        = $relation->expenseid;
            $name      = $relation->category_name . ' - ' . _format_number($relation->amount);
            $addedfrom = $relation->addedfrom;
            if (!empty($relation->expense_name)) {
                $name .= ' (' . $relation->expense_name . ')';
            }
        }
        $link = admin_url('expenses/list_expenses/' . $id);
    } else if ($type == 'lead' || $type == 'leads') {
        if (is_array($relation)) {
            $id   = $relation['id'];
            $name = $relation['name'];
            if ($relation['email'] != '') {
                $name .= ' - ' . $relation['email'];
            }
        } else {
            $id   = $relation->id;
            $name = $relation->name;
            if ($relation->email != '') {
                $name .= ' - ' . $relation->email;
            }
        }
        $link = admin_url('leads/index/' . $id);
    } else if ($type == 'proposal') {
        if (is_array($relation)) {
            $id        = $relation['id'];
            $name      = format_proposal_number($id);
            $addedfrom = $relation['addedfrom'];
            if (!empty($relation['subject'])) {
                $name .= ' - ' . $relation['subject'];
            }
        } else {
            $id        = $relation->id;
            $name      = format_proposal_number($id);
            $addedfrom = $relation->addedfrom;
            if (!empty($relation->subject)) {
                $name .= ' - ' . $relation->subject;
            }
        }
        $link = admin_url('proposals/proposal/' . $id);
    } else if ($type == 'tasks') {
        if (is_array($relation)) {
            $id   = $relation['id'];
            $name = $relation['name'];

        } else {
            $id   = $relation->id;
            $name = $relation->name;
        }
        $link = admin_url('tasks/list_tasks/' . $id);
    } else if ($type == 'staff') {
        if (is_array($relation)) {
            $id   = $relation['staffid'];
            $name = $relation['firstname'] . ' ' . $relation['lastname'];
        } else {
            $id   = $relation->staffid;
            $name = $relation->firstname . ' ' . $relation->lastname;
        }
        $link = admin_url('profile/' . $id);
    } else if ($type == 'project') {
        if (is_array($relation)) {
            $id   = $relation['id'];
            $name = $relation['name'];
        } else {
            $id   = $relation->id;
            $name = $relation->name;
        }
        $link = admin_url('projects/view/' . $id);
    }
    return array(
        'name' => $name,
        'id' => $id,
        'link' => $link,
        'addedfrom' => $addedfrom
    );
}
/**
 * Helper function to get all knowledge base groups in the parents groups
 * @param  boolean $only_customers prevent showing internal kb articles in customers area
 * @param  array   $where
 * @return array
 */
function get_all_knowledge_base_articles_grouped($only_customers = true, $where = array())
{
    $CI =& get_instance();
    $CI->load->model('knowledge_base_model');
    $groups = $CI->knowledge_base_model->get_kbg('', 1);
    $i      = 0;
    foreach ($groups as $group) {
        $CI->db->select('slug,subject,description,tblknowledgebase.active as active_article,articlegroup,articleid,staff_article');
        $CI->db->from('tblknowledgebase');
        $CI->db->where('articlegroup', $group['groupid']);
        $CI->db->where('active', 1);
        if ($only_customers == true) {
            $CI->db->where('staff_article', 0);
        }
        $CI->db->where($where);
        $CI->db->order_by('article_order', 'asc');
        $articles = $CI->db->get()->result_array();
        if (count($articles) == 0) {
            unset($groups[$i]);
            $i++;
            continue;
        }
        $groups[$i]['articles'] = $articles;
        $i++;
    }
    return $groups;
}
/**
 * Helper function to get all announcements for user
 * @param  boolean $staff Is this client or staff
 * @return array
 */
function get_announcements_for_user($staff = true)
{
    if (!is_logged_in()) {
        return array();
    }

    $CI =& get_instance();
    $CI->db->select();

    if ($staff == true) {
        $CI->db->where('announcementid NOT IN (SELECT announcementid FROM tbldismissedannouncements WHERE staff=1 AND userid = ' . get_staff_user_id() . ') AND showtostaff = 1');
    } else {
        if (!is_client_logged_in()) {
            return array();
        }
        $CI->db->where('announcementid NOT IN (SELECT announcementid FROM tbldismissedannouncements WHERE staff=0 AND userid = ' . get_contact_user_id() . ') AND showtousers = 1');
    }

    return $CI->db->get('tblannouncements')->result_array();
}
/**
 * Helper function to get text question answers
 * @param  integer $questionid
 * @param  itneger $surveyid
 * @return array
 */
function get_text_question_answers($questionid, $surveyid)
{
    $CI =& get_instance();
    $CI->db->select('answer,resultid');
    $CI->db->from('tblformresults');
    $CI->db->where('questionid', $questionid);
    $CI->db->where('rel_id', $surveyid);
    $CI->db->where('rel_type', 'survey');
    return $CI->db->get()->result_array();
}
/**
 * Get department email address
 * @param  mixed $id department id
 * @return mixed
 */
function get_department_email($id)
{
    $CI =& get_instance();
    $CI->db->where('departmentid', $id);
    return $CI->db->get('tbldepartments')->row()->email;
}
/**
 * Helper function to get all knowledbase groups
 * @return array
 */
function get_kb_groups()
{
    $CI =& get_instance();
    return $CI->db->get('tblknowledgebasegroups')->result_array();
}
/**
 * Get all countries stored in database
 * @return array
 */
function get_all_countries()
{
    $CI =& get_instance();
    return $CI->db->get('tblcountries')->result_array();
}
// Customize function TAAAAA
function get_all_wards($district_id='')
{
    $CI =& get_instance();
    if(is_numeric($district_id)) {
        $CI->db->where('districtid', $district_id);
    }
    return $CI->db->get('ward')->result_array();
}
function get_all_district($province_id) {
    $CI =& get_instance();
    if(is_numeric($province_id)) {
        $CI->db->where('provinceid', $province_id);
    }
    return $CI->db->get('district')->result_array();
}
function get_all_province()
{
    $CI =& get_instance();

    return $CI->db->get('province')->result_array();
}
function get_all_taxes() {
    $CI =& get_instance();
    return $CI->db->get('tbltaxes')->result_array();
}
// END Customize function TAAAAA
/**
 * Get country row from database based on passed country id
 * @param  mixed $id
 * @return object
 */
function get_country($id)
{
    $CI =& get_instance();
    $CI->db->where('country_id', $id);
    return $CI->db->get('tblcountries')->row();
}
/**
 * Get country short name by passed id
 * @param  mixed $id county id
 * @return mixed
 */
function get_country_short_name($id)
{
    $CI =& get_instance();
    $CI->db->where('country_id', $id);
    $country = $CI->db->get('tblcountries')->row();
    if ($country) {
        return $country->iso2;
    }
    return '';
}
/**
 * Function that add and edit tags based on passed arguments
 * @param  string $tags
 * @param  mixed $rel_id
 * @param  string $rel_type
 * @return boolean
 */
function handle_tags_save($tags,$rel_id,$rel_type){
    $CI =& get_instance();

    $affectedRows = 0;
    if($tags == ''){
        $CI->db->where('rel_id',$rel_id);
        $CI->db->where('rel_type',$rel_type);
        $CI->db->delete('tbltags_in');
        if($CI->db->affected_rows() > 0){
            $affectedRows++;
        }
    } else {

        $tags_array = array();
        if(!is_array($tags)){
            $tags = explode(',',$tags);
        }

        foreach($tags as $tag){
            $tag = trim($tag);
            if($tag != ''){
                array_push($tags_array,$tag);
            }
        }

        // Check if there is removed tags
        $current_tags = get_tags_in($rel_id,$rel_type);

        foreach($current_tags as $tag){
            if(!in_array($tag,$tags_array)){
                $tag = get_tag_by_name($tag);
                $CI->db->where('rel_id',$rel_id);
                $CI->db->where('rel_type',$rel_type);
                $CI->db->where('tag_id',$tag->id);
                $CI->db->delete('tbltags_in');
                if($CI->db->affected_rows() > 0){
                    $affectedRows++;
                }
            }
        }

        // Insert new ones
        $order = 1;
        foreach($tags_array as $tag){

            $tag = str_replace('"', '\'', $tag);

            $CI->db->where('name',$tag);
            $tag_row = $CI->db->get('tbltags')->row();
            if($tag_row){
                $tag_id = $tag_row->id;
            } else {
                // Double quotes not allowed
                $CI->db->insert('tbltags',array('name'=>$tag));
                $tag_id = $CI->db->insert_id();
            }

            if(total_rows('tbltags_in',array('tag_id'=>$tag_id,'rel_id'=>$rel_id,'rel_type'=>$rel_type)) == 0){
                $CI->db->insert('tbltags_in',
                    array(
                        'tag_id'=>$tag_id,
                        'rel_id'=>$rel_id,
                        'rel_type'=>$rel_type,
                        'tag_order'=>$order
                    ));

                if($CI->db->affected_rows() > 0){
                    $affectedRows++;
                }
            }
            $order++;
        }
    }

    return ($affectedRows > 0 ? true : false);
}
/**
 * Get tag from db by name
 * @param  string $name
 * @return object
 */
function get_tag_by_name($name){
    $CI =& get_instance();
    $CI->db->where('name',$name);
    return $CI->db->get('tbltags')->row();
}
/**
 * Function that will return all tags used in the app
 * @return array
 */
function get_tags(){
    $CI = &get_instance();
    $CI->db->order_by('name','ASC');
    return $CI->db->get('tbltags')->result_array();
}
/**
 * Array of available tags without the keys
 * @return array
 */
function get_tags_clean(){
    $tmp_tags = array();
    $tags = get_tags();
    foreach($tags as $tag){
        array_push($tmp_tags,$tag['name']);
    }
    $tags = $tmp_tags;
    return $tags;
}
/**
 * Get all tag ids
 * @return array
 */
function get_tags_ids(){
    $tmp_tags = array();
    $tags = get_tags();
    foreach($tags as $tag){
        array_push($tmp_tags,$tag['id']);
    }
    $tags = $tmp_tags;
    return $tags;
}
/**
 * Function that will parse all the tags and return array with the names
 * @param  string $rel_id
 * @param  string $rel_type
 * @return array
 */
function get_tags_in($rel_id,$rel_type){

    $CI =& get_instance();
    $CI->db->where('rel_id',$rel_id);
    $CI->db->where('rel_type',$rel_type);
    $CI->db->order_by('tag_order','ASC');
    $tags = $CI->db->get('tbltags_in')->result_array();

    $tag_names = array();
    foreach($tags as $tag){
        $CI->db->where('id',$tag['tag_id']);
        $tag_row = $CI->db->get('tbltags')->row();
        if($tag_row){
            array_push($tag_names,$tag_row->name);
        }
    }
    return $tag_names;
}
/**
 * This text is used in WHERE statements for tasks if the staff member dont have permission for tasks VIEW
 * This query will shown only tasks that are created from current user, public tasks or where this user is added is task follower.
 * Other statement will be included the tasks to be visible for this user only if Show All Tasks For Project Members is set to YES
 * @return [type] [description]
 */
function get_tasks_where_string()
{
    $_tasks_where = 'AND (tblstafftasks.id IN (SELECT taskid FROM tblstafftaskassignees WHERE staffid = ' . get_staff_user_id() . ') OR tblstafftasks.id IN (SELECT taskid FROM tblstafftasksfollowers WHERE staffid = ' . get_staff_user_id() . ') OR addedfrom=' . get_staff_user_id();
    if (get_option('show_all_tasks_for_project_member') == 1) {
        $_tasks_where .= ' OR (rel_type="project" AND rel_id IN (SELECT project_id FROM tblprojectmembers WHERE staff_id=' . get_staff_user_id() . '))';
    }
    $_tasks_where .= ' OR is_public = 1)';
    return $_tasks_where;
}
/**
 * Show all units exists in database
 *
 * @return array
 */
function get_units() {
    $CI =& get_instance();
    $units = $CI->db->get('tblunits')->result_array();
    return $units;
}
/**
 * Get all groups of item in database
 *
 * @return array
 */
function get_item_groups() {
    $CI =& get_instance();
    $groups = $CI->db->get('tblitems_groups')->result_array();
    return $groups;
}

function get_table_where($table, $where = array(),$order_by="",$result='result_array')
{
    $CI =& get_instance();
    if($where!=array())
    {
        $CI->db->where($where);
    }
    if($order_by!="")
    {
        $CI->db->order_by($order_by);
    }
    $result=$CI->db->get($table)->$result();
    if ($result) {
        return $result;
    } else {
        return array();
    }
}

function get_table_where_sum($table, $where = array(),$field='total')
{
    $CI =& get_instance();
    $CI->db->select_sum($field);
    if($where!=array())
    {
        $CI->db->where($where);
    }
    
    $result=$CI->db->get($table)->row();
    if ($result) {
        return $result->$field;
    } else {
        return 0;
    }
}
function get_code_tk($id)
{
    $CI =& get_instance();
    if(is_numeric($id))
    {
        $CI->db->where('idAccount',$id);
        $result=$CI->db->get('tblaccounts')->row();
        if ($result) {
            return $result->accountCode;
        }
        return "";
    }
}

function get_value_tk_no($table,$field,$id,$id_tk)
{
    $CI =& get_instance();
    if(is_numeric($id))
    {
        $CI->db->select_sum('sub_total');
        $CI->db->where('tk_no',$id_tk);
        $CI->db->where($field,$id);
        $result=$CI->db->get($table)->row();
        if ($result) {
            return $result->sub_total;
        }
        return "";
    }
}

function get_value_tk_co($table,$field,$id,$id_tk)
{
    $CI =& get_instance();
    if(is_numeric($id))
    {
        $CI->db->select_sum('sub_total');
        $CI->db->where('tk_co',$id_tk);
        $CI->db->where($field,$id);
        $result=$CI->db->get($table)->row();
        if ($result) {
            return $result->sub_total;
        }
        return "";
    }
}

function getDeliverdQuantity($sale_id,$product_id=NULL)
{
    $CI =& get_instance();
    if(is_numeric($sale_id))
    {
        $CI->db->select_sum('delivery_quantity');
        $CI->db->select_sum('quantity');
        if($product_id)
        {
            $CI->db->where('product_id',$product_id);
        }
        $CI->db->where('sale_id',$sale_id);
        $result=$CI->db->get('tblsale_items')->row();

        if ($result) {
            return $result;
        }
        return false;
    }
}

function getTotalSOPayment($sale_id)
{
    $CI =& get_instance();
    if(is_numeric($sale_id))
    {
        $CI->db->select('tblsales.total');
        $CI->db->select_sum('subtotal');
        $CI->db->where('sales',$sale_id);
        $CI->db->join('tblsales','tblsales.id=tblreceipts_contract.sales');
        $result=$CI->db->get('tblreceipts_contract')->row();
        if ($result) {
            return $result;
        }
        return false;
    }
}



function getDeliverdQuantityByContractID($contract_id,$product_id=NULL)
{
    $CI =& get_instance();
    if(is_numeric($contract_id))
    {
        // $CI->db->select('tblsales.id,tblsale_items.product_id,tblsale_items.quantity,tblsale_items.export_quantity,tblsale_items.delivery_quantity');
        $CI->db->select('tblcontracts.id as contract_id,tblsale_items.product_id,tblsale_items.unit_cost,tblsale_items.tax_rate');
        $CI->db->select_sum('tblsale_items.amount');
        $CI->db->select_sum('tblsale_items.delivery_quantity');
        $CI->db->select_sum('tblsale_items.export_quantity');
        $CI->db->select_sum('tblsale_items.quantity');
        if($product_id)
        {
            $CI->db->where('product_id',$product_id);
        }
        $CI->db->where('tblcontracts.id',$contract_id);
        $CI->db->join('tblsales','tblsales.id=tblsale_items.sale_id');
        $CI->db->join('tblsale_orders','tblsale_orders.id=tblsales.rel_id');        
        $CI->db->join('tblcontracts','tblcontracts.id=tblsale_orders.rel_id'); 
        $result=$CI->db->get('tblsale_items')->row();

        if ($result) {
            return $result;
        }
        return false;
    }
}

function getEffectuatedAmount($price,$quantity,$tax_rate)
{
    $CI =& get_instance();
    if(is_numeric($price) && is_numeric($quantity) && is_numeric($tax_rate))
    {
        $sub_total=$price*$quantity;
        $tax=$sub_total*$tax_rate/100;
        $amount=$sub_total+$tax;

        if ($amount) {
            return $amount;
        }
        return 0;
    }
}

function updateProductTotalQuantityByID($product_id=NULL)
{
    $CI =& get_instance();
    $affected=false;
    if(is_numeric($product_id) || is_null($product_id))
    {
        $CI->db->select('tblitems.id as product_id,tblwarehouses_products.product_quantity');
        if($product_id)
        {
            $CI->db->select_sum('tblwarehouses_products.product_quantity');
            $CI->db->where('tblwarehouses_products.product_id',$product_id);
        }
        $CI->db->join('tblwarehouses_products','tblwarehouses_products.product_id=tblitems.id','left'); 
        $result=$CI->db->get('tblitems')->result();
        
        foreach ($result as $key => $item) {
            $total_quantity=$item->product_quantity;
            if(empty($item->product_quantity)) $total_quantity=0;
            $CI->db->update('tblitems',array('quantity'=>$total_quantity),array('id'=>$item->product_id));

            if($CI->db->affected_rows()>0)
            {
                $affected=true;
            }
        }
        if ($affected) {
            return true;
        }
        return false;
    }
}
function getTaxByRate($tax_rate)
{
    $CI =& get_instance();
    if(is_numeric($tax_rate))
    {
        $result=$CI->db->get_where('tbltaxes',array('taxrate'=>$tax_rate))->row();

        if ($result) {
            return $result;
        }
        return false;
    }
}

function updateWarehouseProductDetails($product_id,$warehouse_id,$entered_date,$entered_quantity,$entered_price)
{
    $CI =& get_instance();
    if(is_numeric($product_id) && is_numeric($entered_quantity) && is_numeric($entered_price))
    {
        $data_warehouse_detail=array(
            'product_id'=>$product_id,
            'entered_date'=>$entered_date,
            'entered_quantity'=>$entered_quantity,
            'entered_price'=>$entered_price
        );

        $CI->db->insert('tblwarehouse_product_details',$data_warehouse_detail);

        if ($CI->db->affected_rows()>0) {
            return true;
        }
        return false;
    }
}

function increaseWarehouseProductDetail($import_id,$product_id,$warehouse_id,$entered_quantity,$entered_price,$entered_date=NULL)
{   
    $CI =& get_instance();
    if(is_null($entered_date)) $entered_date=date("Y-m-d H:i:s");
    if(is_null($entered_price)) 
    {
        $entered_price=getOrginalPrice($import_id,$product_id)->original_price_buy;
    }
    
    $data=array(
        'import_id'=>$import_id,
        'product_id'=>$product_id,
        'warehouse_id'=>$warehouse_id,
        'entered_quantity'=>$entered_quantity,
        'entered_price'=>$entered_price,
        'entered_date'=>$entered_date
    );

    if (isset($import_id) && isset($product_id) && isset($warehouse_id) && is_numeric($entered_quantity)) {
        $product=$CI->db->get_where('tblwarehouse_product_details',array('import_id'=>$import_id,'product_id'=>$product_id,'warehouse_id'=>$warehouse_id,'entered_date'=>$entered_date,))->row();

        if($product)
        {
            $total_quantity=$entered_quantity+$product->entered_quantity;
            $data['entered_quantity']=$total_quantity;
            $CI->db->update('tblwarehouse_product_details',$data,array('id'=>$product->id));
        }
        else
        {
            $CI->db->insert('tblwarehouse_product_details',$data);
        }
        if($CI->db->affected_rows()>0) 
            return true;
    }
    return false;
}

function decreaseWarehouseProductDetail($import_id,$product_id,$warehouse_id,$entered_quantity,$entered_price=NULL,$entered_date=NULL)
{   
    $CI =& get_instance();
    if(is_null($entered_date)) $entered_date=date("Y-m-d H:i:s");
    if(is_null($entered_price)) 
    {
        $entered_price=getOrginalPrice($import_id,$product_id)->original_price_buy;
    }
    $data=array(
        'import_id'=>$import_id,
        'product_id'=>$product_id,
        'warehouse_id'=>$warehouse_id,
        'entered_quantity'=>$entered_quantity,
        'entered_price'=>$entered_price,
        'entered_date'=>$entered_date
    );
    if (isset($product_id) && isset($product_id) && isset($warehouse_id) && is_numeric($entered_quantity)) {
        $product=$CI->db->get_where('tblwarehouse_product_details',array('import_id'=>$import_id,'product_id'=>$product_id,'warehouse_id'=>$warehouse_id,'entered_date'=>$entered_date,))->row();
        if($product)
        {
            $total_quantity=$product->entered_quantity-$entered_quantity;
            $data['entered_quantity']=$total_quantity;
            $CI->db->update('tblwarehouse_product_details',$data,array('id'=>$product->id));
        }
        else
        {
            $CI->db->insert('tblwarehouse_product_details',$data);
        }
        if($CI->db->affected_rows()>0) 
            return true;
    }
    return false;
}

function getOrginalPrice($import_id=NULL,$product_id=NULL)
{
    $CI =& get_instance();
    if(is_numeric($import_id) && is_numeric($product_id))
    {
        $CI->db->select('tblorders_detail.id as orders_detail_id ,tblimports.id as import_id,tblimports.rel_type,tblimports.rel_id as contract_id,tblpurchase_contracts.id_order as order_id, tblorders_detail.product_id, tblorders_detail.entered_quantity, tblorders_detail.original_price_buy');
        $CI->db->join('tblpurchase_contracts','tblpurchase_contracts.id=tblimports.rel_id','left'); 
        $CI->db->join('tblorders_detail','tblorders_detail.order_id=tblpurchase_contracts.id_order','left'); 
        $CI->db->where('tblimports.id',$import_id);
        $CI->db->where('tblorders_detail.product_id',$product_id);
        $import=$CI->db->get('tblimports')->row();
        if ($import) {
            return $import;
        }
        return false;
    }
}
function updateTotalQuantityImport($import_id=NULL,$product_id=NULL)
{
    $CI =& get_instance();
    if(is_numeric($import_id) && is_numeric($product_id))
    {
        
        $import=$CI->db->get_where('tblimports',array('id'=>$import_id))->row();
        $CI->db->select_sum('quantity_net');
        $CI->db->join('tblimports','tblimports.id=tblimport_items.import_id','left');
        $total_quantity=$CI->db->get_where('tblimport_items',array('rel_id'=>$import->rel_id,'product_id'=>$product_id))->row()->quantity_net;

        $order_item=getOrginalPrice($import_id,$product_id);
        $CI->db->update('tblorders_detail',array('entered_quantity'=>$total_quantity),array('id'=>$order_item->orders_detail_id));
        if ($CI->db->affected_rows()) {
            
            return true;
        }
        return false;
    }
}

function getSaleProductDetail($product_id=NULL,$warehouse_id=NULL,$export_quantity=NULL)
{
    
    $CI =& get_instance();
    if(is_numeric($warehouse_id) && is_numeric($product_id) && is_numeric($export_quantity))
    {
        $CI->db->where('entered_quantity >',0);
        $CI->db->order_by('entered_date','ASC');
        $details=$CI->db->get_where('tblwarehouse_product_details',array('warehouse_id'=>$warehouse_id,'product_id'=>$product_id))->result();
        $result=array();

        foreach ($details as $key => $item) {
            $quantity=0;
            if($export_quantity<=$item->entered_quantity) 
            {
                $quantity=$export_quantity;
                $export_quantity=0;
            }
            else
            {
                $quantity=$item->entered_quantity;
                $export_quantity-=$quantity;
            }
            array_push($result, (object)(array('wp_detail_id'=>$item->id,
                                                  'product_id'=>$item->product_id,
                                                  'quantity'=>$quantity,
                                                  'entered_price'=>$item->entered_price
                                                )));
            if($export_quantity==0) break;
        }
        if ($result) {
            
            return $result;
        }
    }
    return false;
}

function updateSaleProductDetail($rel_id=NULL,$product_id=NULL,$items=array(),$type='PO')
{
    $CI =& get_instance();
    if(is_numeric($rel_id) && is_numeric($product_id) && is_array($items))
    {

        foreach ($items as $key => $item) {
            $data=array(
                        'rel_type'=>$type,
                        'rel_id'=>$rel_id,
                        'wp_detail_id'=>$item['wp_detail_id'],
                        'product_id'=>$product_id,
                        'quantity'=>$item['quantity'],
                        'entered_price'=>$item['entered_price']
                );
            // var_dump($items);die;
            $detail=$CI->db->get_where('tblsale_details',array('rel_type'=>$type,'rel_id'=>$rel_id,'wp_detail_id'=>$item['wp_detail_id'],'product_id'=>$product_id,'entered_price'=>$item['entered_price']))->row();

            $affected=array();
            if($detail)
            {
                $CI->db->update('tblsale_details',$data,array('id'=>$detail->id));
                $affected[]=$detail->id;
            }
            else
            {
                $CI->db->insert('tblsale_details',$data);
                $affected[]=$CI->db->insert_id();
            }
            if($CI->db->affected_rows()>0) $result=true;

            if($affected)
            {
                $CI->db->where('rel_type',$type);
                $CI->db->where('rel_id',$rel_id);
                $CI->db->where_not_in('id', $affected);
                $CI->db->delete('tblsale_details');
            }
        }
        if ($result) {
            
            return true;
        }
    }
    return false;
}

function getAllSaleProductDetails($rel_id=NULL,$product_id=NULL,$type='PO')
{
    
    $CI =& get_instance();
    if(is_numeric($rel_id) && is_numeric($product_id))
    {
        $details=$CI->db->get_where('tblsale_details',array('rel_type'=>$type,'rel_id'=>$rel_id,'product_id'=>$product_id))->result();
        if ($details) {
            
            return $details;
        }
    }
    return false;
}
