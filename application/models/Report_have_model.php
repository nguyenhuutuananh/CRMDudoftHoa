<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Report_have_model extends CRM_Model
{
    function __construct()
    {
        parent::__construct();
    }
    public function get_table_where($table,$where=array())
    {
        if($where!=array())
        {
            $this->db->where($where);
        }
        return $this->db->get($table)->result_array();
    }
    public function get_table_id($table,$where="")
    {
        if($where!="")
        {
            $this->db->where($where);
        }
        return $this->db->get($table)->row();
    }
    public function get_contract()
    {
        $this->db->select('id,concat(prefix,code) as fullcode');
        return $this->db->get('tblcontracts')->result_array();
    }
    public function insert($data)
    {
        $this->db->insert('tblreport_have',$data);
        $id=$this->db->insert_id();
        if($id){
            return $this->db->insert_id();
        }
        return false;
    }
    public function insert_report_have_contract($id,$data)
    {
        foreach($data as $_data)
        {

            unset($_data['id']);
            $_data['subtotal']=str_replace(',','',$_data['subtotal']);
            $_data['id_report_have']=$id;
            $this->db->insert('tblreport_have_contract',$_data);
        }
        $id=$this->db->insert_id();
        if($id){
            return $this->db->insert_id();
        }
        return false;

    }
    public function update_status($id,$data)
    {
        $this->db->where('id',$id);
        $this->db->update('tblreport_have',$data);
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }
    public function get_index_report_have($id="")
    {
        $this->db->where('id',$id);
        return $this->db->get('tblreport_have')->row();
    }
    public function index_report_have_contract($id_report_have="")
    {
        $this->db->where('id_report_have',$id_report_have);
        return $this->db->get('tblreport_have_contract')->result_array();
    }
    public function update($id="",$data=array())
    {
        $this->db->where('id',$id);
        $this->db->update('tblreport_have',$data);
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }
    public function update_report_have_cotract($id_report_have,$data=array())
    {
        $ass=0;
        $_array_id=array();
        foreach($data as $rom)
        {
            $_array_id[]=$rom['id'];
        }
        $this->db->where('id_report_have',$id_report_have);
        $this->db->where_not_in('id',$_array_id);
        $this->db->delete('tblreport_have_contract');
        if($this->db->affected_rows() > 0){
            $ass++;
        }


        foreach($data as $rom)
        {
            $id=$rom['id'];
            unset($rom['id']);
            $rom['subtotal']=str_replace(',','',$rom['subtotal']);
            $this->db->where('id',$id);
            $this->db->update('tblreport_have_contract',$rom);
            if($this->db->affected_rows() > 0){
                $ass++;
            }
        }
        if($ass > 0){
            return true;
        }
        return false;
    }
    public function get_data_pdf($id)
    {
        $this->db->select('tblreport_have.*,sum(tblreport_have_contract.subtotal) as sum_total,tblaccount_person.name_bank,tblaccount_person.account,branch');
        $this->db->where('tblreport_have.id',$id);
        $this->db->join('tblreport_have_contract','tblreport_have_contract.id_report_have=tblreport_have.id','left');
        $this->db->join('tblaccount_person','tblaccount_person.id=tblreport_have.id_account_person','left');
        return $this->db->get('tblreport_have')->row();
    }
    public function get_invoices_report_have($id_client="")
    {
        $this->db->select('tblinvoices.*');
        $this->db->where('tblinvoices.clientid',$id_client);
        $this->db->where('tblinvoices.status=2 or tblinvoices.status=3');
        $this->db->join('tblreport_have_contract','tblreport_have_contract.invoices!=tblinvoices.id');
        return $this->db->get('tblinvoices')->result_array();
    }
    public function get_vouchers()
    {
        $this->db->select_max('id');
        $id_max = $this->db->get('tblreport_have')->row();
        $last_id = strlen(($id_max->id) + 1);
        $max_code = 5;
        $n = $max_code - $last_id;
        $_code = "";
        if ($n > 0) {
            for ($i = 0; $i < $n; $i++) {
                $_code .= 0;
            }
        }
        return $last_code = get_option('prefix_vouchers_report_have') . $_code . ($id_max->id + 1);
    }
    public function get_client_contract($id_contract="")
    {
        $this->db->select('tblclients.*,tblcontracts.contract_value');
        $this->db->where('tblcontracts.id',$id_contract);
        $this->db->join('tblclients','tblclients.userid=tblcontracts.client');
        return $this->db->get('tblcontracts')->row();

    }
}
