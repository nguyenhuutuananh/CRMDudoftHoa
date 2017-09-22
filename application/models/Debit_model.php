<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Debit_model extends CRM_Model
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
    public function get_contract()
    {
        $this->db->select('id,concat(prefix,code) as fullcode');
        return $this->db->get('tblcontracts')->result_array();
    }
    public function get_debit_contract()
    {
        $contract=$this->db->get('tblcontracts')->result_array();
        $data=array();
        foreach($contract as $rom)
        {
            $this->db->select('sum(amount) as sum_contract');
            $this->db->where('contract_id',$rom['id']);
            $sum_contract=$this->db->get('tblcontract_items')->row();
            $data[$rom['id']]=array('sum_contract'=>$sum_contract->sum_contract,'currency'=>3);
        }
        return $data;
    }
    public function get_debit_purchase()
    {
        $contract=$this->db->get('tblpurchase_contracts')->result_array();
        $data=array();
        foreach($contract as $rom)
        {
            $this->db->select('sum(product_price_buy*exchange_rate) as sum_contract,currency_id');
            $this->db->where('order_id',$rom['id_order']);
            $sum_contract=$this->db->get('tblorders_detail')->row();
            $data[$rom['id']]=array('sum_contract'=>$sum_contract->sum_contract,'currency'=>$sum_contract->currency_id);

        }
        return $data;
    }
    public function insert($data)
    {
        $this->db->insert('tbldebit',$data);
        $id=$this->db->insert_id();
        if($id){
            return $this->db->insert_id();
        }
        return false;
    }
    public function insert_debit_contract($id,$data)
    {
        foreach($data as $_data)
        {

            unset($_data['id']);
            $_data['total']=str_replace(',','',$_data['total']);
            $_data['id_debit']=$id;
            $this->db->insert('tbldebit_contract',$_data);
        }
        $id=$this->db->insert_id();
        if($id){
            return $this->db->insert_id();
        }
        return $_data;

    }
    public function update_status($id,$data)
    {
        $this->db->where('id',$id);
        $this->db->update('tbldebit',$data);
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }
    public function get_index_debit($id="")
    {
        $this->db->where('id',$id);
        return $this->db->get('tbldebit')->row();
    }
    public function index_debit_contract($id_debit="")
    {
        $this->db->where('id_debit',$id_debit);
        return $this->db->get('tbldebit_contract')->result_array();
    }
    public function update($id="",$data=array())
    {
        $this->db->where('id',$id);
        $this->db->update('tbldebit',$data);
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }
    public function update_debit_cotract($id_debit,$data=array())
    {
        $ass=0;
        $_array_id=array();
        foreach($data as $rom)
        {
            $_array_id[]=$rom['id'];
        }
        $this->db->where('id_debit',$id_debit);
        $this->db->where_not_in('id',$_array_id);
        $this->db->delete('tbldebit_contract');
        if($this->db->affected_rows() > 0){
            $ass++;
        }


        foreach($data as $rom)
        {
            $id=$rom['id'];
            unset($rom['id']);
            $rom['total']=str_replace(',','',$rom['total']);
            $this->db->where('id',$id);
            $this->db->update('tbldebit_contract',$rom);
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
        $this->db->select('tbldebit.*,sum(tbldebit_contract.total) as sum_total');
        $this->db->where('tbldebit.id',$id);
        $this->db->join('tbldebit_contract','tbldebit_contract.id_debit=tbldebit.id','left');
        return $this->db->get('tbldebit')->row();
    }
    public function get_vouchers()
    {
        $this->db->select_max('id');
        $id_max=$this->db->get('tbldebit')->row();
        $last_id= strlen(($id_max->id)+1);
        $max_code=5;
        $n=$max_code-$last_id;
        $_code="";
        if($n>0)
        {
            for($i=0;$i<$n;$i++)
            {
                $_code.=0;
            }
        }
       return $last_code=get_option('prefix_vouchers_debit').$_code.($id_max->id+1);
    }
}
