<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Accounts_model extends CRM_Model
{
    function __construct() {
        parent::__construct();
    }
    public function add($data) {
        $this->db->insert('tblaccounts', $data);
        if($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
    public function edit($id, $data) {
        if(is_numeric($id)) {
            $this->db->where('idAccount', $id);
            $this->db->update('tblaccounts', $data);
            if($this->db->affected_rows() > 0)
                return true;
        }
        return false;
    }
    public function delete($id) {
        // It's specical, i wonder how to delete it when it existed in other columns
        if(is_numeric($id)) {
            $this->db->where('idAccount', $id);
            $account = $this->db->get('tblaccounts')->row();
            if($account && $account->generalAccount == 0) {
                $this->db->where('idAccount', $id);
                $this->db->delete('tblaccounts');
                if($this->db->affected_rows() > 0) {
                    return true;
                }
            }
        }
        return false;
    }
    public function get_accounts_tree($parent='') {
        $accounts = array();
        $this->db->join('tblaccount_attributes', 'tblaccount_attributes.idAttribute = tblaccounts.idAccountAttribute', 'left');
        if($parent=='') {
            $accounts = $this->db->where('generalAccount', '0')->get('tblaccounts')->result();
        }
        else {
            $accounts = $this->db->where('generalAccount', $parent)->get('tblaccounts')->result();
        }
        if(count($accounts) == 0) {
            return array();
        }
        else {
            $reSortAccounts = array();
            foreach($accounts as $key=>$account) {
                array_push($reSortAccounts, $account);
                $subAccount = $this->get_accounts_tree($account->idAccount);
                if(count($subAccount) > 0)
                {
                    foreach($subAccount as $key2=>$account2) {
                        array_push($reSortAccounts, $account2);
                    }
                    
                }
            }
            return $reSortAccounts;
        }
    }

    public function get_tk_no() {
        $this->db->where('idAccountAttribute',1);
        $this->db->or_where('idAccountAttribute',3);
        $account=$this->db->get('tblaccounts');
        if($account->num_rows()>0)
            return $account->result_array();
        else return false;
    }

    public function get_tk_co() {
        $this->db->where('idAccountAttribute',2);
        $this->db->or_where('idAccountAttribute',3);
        $account=$this->db->get('tblaccounts');
        if($account->num_rows()>0)
            return $account->result_array();
        else return false;
    }

    public function get_accounts($except_id = array(), $ARRAY_RESULT = FALSE) {
        if(is_array($except_id) && count($except_id) > 0) {
            $this->db->where_not_in('idAccount', $except_id);
        }
        $this->db->join('tblaccount_attributes', 'tblaccount_attributes.idAttribute = tblaccounts.idAccountAttribute', 'left');
        if(!$ARRAY_RESULT) {
            return $this->db->order_by('accountCode', 'ASC')->get('tblaccounts')->result();
        }
        else {
            return $this->db->order_by('accountCode', 'ASC')->get('tblaccounts')->result_array();
        }
    }
    public function get_single($id) {
        $this->db->join('tblaccount_attributes', 'tblaccount_attributes.idAttribute = tblaccounts.idAccountAttribute', 'left');
        $this->db->where('idAccount', $id);
        return $this->db->order_by('accountCode', 'ASC')->get('tblaccounts')->row();
    }
    public function get_account_attributes($ARRAY_RESULT = FALSE) {
        if(!$ARRAY_RESULT) {
            return $this->db->get('tblaccount_attributes')->result();
        }
        else {
            return $this->db->get('tblaccount_attributes')->result_array();
        }
    }
}