<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class opportunity_model extends CRM_Model
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
    public function get_table_id($table,$where=array())
    {
        if($where!=array())
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
    public function get_votes_contract()
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
    public function get_votes_purchase()
    {
        $contract=$this->db->get('tblpurchase_contracts')->result_array();
        $data=array();
        foreach($contract as $rom)
        {
            $this->db->select('sum(product_price_buy) as sum_contract,currency_id');
            $this->db->where('order_id',$rom['id_order']);
            $sum_contract=$this->db->get('tblorders_detail')->row();
            $data[$rom['id']]=array('sum_contract'=>$sum_contract->sum_contract,'currency'=>$sum_contract->currency_id);

        }
        return $data;
    }
    public function insert($data)
    {
        $this->db->insert('tblopportunity',$data);
        $id=$this->db->insert_id();
        if($id){
            return $this->db->insert_id();
        }
        return false;
    }
    public function insert_staff($id,$data)
    {
        foreach($data as $_data)
        {
            $__data=array();
            $__data['id_campaign']=$id;
            $__data['id_staff']=$_data;
            $this->db->where('id_campaign',$__data['id_campaign']);
            $this->db->where('id_staff',$__data['id_staff']);
            $result=$this->db->get('tblcampaign_staff')->row();
            if($result)
            {
                $this->db->insert('tblcampaign_staff',$__data);
            }
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
        $this->db->update('tblvotes',$data);
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }
    public function get_index_votes($id="")
    {
        $this->db->where('id',$id);
        return $this->db->get('tblvotes')->row();
    }
    public function index_votes_contract($id_votes="")
    {
        $this->db->where('id_votes',$id_votes);
        return $this->db->get('tblvotes_contract')->result_array();
    }
    public function update($id="",$data=array())
    {
        $this->db->where('id',$id);
        $this->db->update('tblopportunity',$data);
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }
    public function update_step($id_campaign,$data=array())
    {
        $ass=0;
        $_array_id=array();
        foreach($data as $rom)
        {
            $_array_id[]=$rom['id'];
        }
        $this->db->where('id_campaign',$id_campaign);
        $this->db->where_not_in('id',$_array_id);
        $this->db->delete('tblcampaign_step');
        if($this->db->affected_rows() > 0){
            $ass++;
        }


        foreach($data as $rom)
        {
            $id=$rom['id'];
            unset($rom['id']);
            $rom['id_campaign']=$id_campaign;
            $this->db->where('id',$id);
            $this->db->update('tblcampaign_step',$rom);
            if($this->db->affected_rows() > 0){
                $ass++;
            }
        }
        if($ass > 0){
            return true;
        }
        return false;
    }
    public function update_staff($id_campaign,$data=array())
    {
        $ass=0;
        $this->db->where('id_campaign',$id_campaign);
        $this->db->where_not_in('id_staff',$data);
        $this->db->delete('tblcampaign_staff');
        if($this->db->affected_rows() > 0){
            $ass++;
        }
        foreach($data as $rom)
        {
            $_data=array();
            $_data['id_campaign']=$id_campaign;
            $_data['id_staff']=$rom;
            $this->db->where('id_staff!='.$rom);
            $this->db->where('id_campaign!='.$id_campaign);
            $result=$this->db->get('tblcampaign_staff')->row();
            if(!$result)
            {
                $this->db->insert('tblcampaign_staff',$_data);
                if($this->db->affected_rows() > 0){
                    $ass++;
                }
            }
        }
        if($ass > 0){
            return true;
        }
        return false;
    }
    public function get_data_pdf($id)
    {
        $this->db->select('tblvotes.*,sum(tblvotes_contract.total) as sum_total');
        $this->db->where('tblvotes.id',$id);
        $this->db->join('tblvotes_contract','tblvotes_contract.id_votes=tblvotes.id','left');
        return $this->db->get('tblvotes')->row();
    }
    public function get_vouchers()
    {
        $this->db->select_max('id');
        $id_max = $this->db->get('tbldebit')->row();
        $last_id = strlen(($id_max->id) + 1);
        $max_code = 6;
        $n = $max_code - $last_id;
        $_code = "";
        if ($n > 0) {
            for ($i = 0; $i < $n; $i++) {
                $_code .= 0;
            }
        }
        return $last_code = get_option('prefix_vouchers_votes') . $_code . ($id_max->id + 1);
    }
}
