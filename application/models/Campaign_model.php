<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Campaign_model extends CRM_Model
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
    public function insert($data)
    {
        $this->db->insert('tblcampaign',$data);
        $id=$this->db->insert_id();
        if($id){
            return $this->db->insert_id();
        }
        return false;
    }
    public function insert_step($id,$data)
    {
        foreach($data as $_data)
        {

            unset($_data['id']);
            $_data['id_campaign']=$id;
            $this->db->insert('tblcampaign_step',$_data);
        }
        $id=$this->db->insert_id();
        if($id){
            return $this->db->insert_id();
        }
        return false;

    }
    public function insert_items($id,$data)
    {
        foreach($data as $_data)
        {

            $this->db->where('id',$_data['id']);
            $item=$this->db->get('tblitems')->row();
            $_data['id_item']=$_data['id'];
            unset($_data['id']);
            $_data['id_campaign']=$id;
            $_data['price_single']=$item->price;
            $_data['total']=$item->price*$_data['quantity'];
            $this->db->insert('tblcampaign_items',$_data);
        }
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
            $this->db->where('id_staff',$_data);
            $this->db->where('id_campaign',$id);
            $result=$this->db->get('tblcampaign_staff')->row();
            if(!$result)
            {
                $__data['id_campaign']=$id;
                $__data['id_staff']=$_data;
                $this->db->insert('tblcampaign_staff',$__data);
            }
        }
        if($this->db->affected_rows() > 0){

            return true;
        }
        return false;

    }
    public function get_campaign_items($id)
    {
        $this->db->select('tblcampaign_items.*,tblcampaign_items.id as id_c,tblcampaign_items.quantity as quantity_item,tblitems.*,tblunits.unit');
        $this->db->where('id_campaign',$id);
        $this->db->join('tblitems','tblitems.id=tblcampaign_items.id_item');
        $this->db->join('tblunits','tblunits.unitid=tblitems.unit');
        return $this->db->get('tblcampaign_items')->result_array();
    }
    public function update($id="",$data=array())
    {
        $this->db->where('id',$id);
        $this->db->update('tblcampaign',$data);
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }
    public function update_items($id_campaign,$data)
    {
        $ass=0;
        $_array_id=array();
        foreach($data as $rom)
        {
            $_array_id[]=$rom['id'];
        }
        $this->db->where('id_campaign',$id_campaign);
        $this->db->where_not_in('id',$_array_id);
        $this->db->delete('tblcampaign_items');
        if($this->db->affected_rows() > 0){
            $ass++;
        }
        foreach($data as $rom)
        {
            $id=$rom['id'];
            unset($rom['id']);
            $this->db->where('id',$id);
            $row=$this->db->get('tblcampaign_items')->row();
            if($row)
            {
                $rom['id_campaign']=$id_campaign;
                $rom['price_single']=$row->price_single;
                $rom['total']=$row->price_single*$rom['quantity'];
                $this->db->where('id',$id);
                $this->db->update('tblcampaign_items',$rom);
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
    public function get_full_items($id = '',$warehouse_id='')
    {
        $this->db->select('tblitems.*,tblunits.unit as unit_name,tbltaxes.name as tax_name, tbltaxes.taxrate as tax_rate')->distinct();
        $this->db->from('tblitems');
        $this->db->join('tblunits','tblunits.unitid=tblitems.unit','left');
        $this->db->join('tbltaxes','tbltaxes.id=tblitems.tax','left');
        $this->db->join('tblwarehouses_products', 'tblwarehouses_products.product_id = tblitems.id', 'left');
        $this->db->order_by('tblitems.id', 'desc');
        if (is_numeric($warehouse_id)) {
            $this->db->where('tblwarehouses_products.warehouse_id', $warehouse_id);
        }
        if (is_numeric($id)) {

            $this->db->where('tblitems.id', $id);
            $item = $this->db->get()->row();
            $item->attachments = $this->get_invoice_attachments($id);
            return $item;
        }

        return $this->db->get()->result_array();
    }
    public function get_invoice_attachments($id = '', $attachment_id = '')
    {
        if (is_numeric($attachment_id)) {
            $this->db->where('id', $attachment_id);
            return $this->db->get('tblfiles')->row();
        }

        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'invoice_item');
        $this->db->order_by('dateadded', 'DESC');
        return $this->db->get('tblfiles')->result_array();
    }
}
