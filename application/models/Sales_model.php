<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sales_model extends CRM_Model
{
    private $statuses;
    function __construct()
    {
        parent::__construct();
    }

    public function get($id = '', $where = array())
    {
        $this->db->select('*,tblclients.company');
        $this->db->from('tblsales');
        $this->db->join('tblclients', 'tblclients.userid = tblsales.customer_id', 'left');
        $this->db->where($where);

        if (is_numeric($id)) {
            $this->db->where('tblsales.id', $id);
            $sale = $this->db->get()->row();
            
            return $sale;
        }
        $this->db->order_by('date', 'desc');
        return $this->db->get()->result();
    }

    public function getAllSalesByCustomerID($customer_id = '')
    {

        $this->db->select('*,tblclients.company');
        $this->db->from('tblsales');
        $this->db->join('tblclients', 'tblclients.userid = tblsales.customer_id', 'left');
        $this->db->where('tblsales.customer_id', $customer_id);
        $this->db->where('tblsales.invoice_status <>', 1);
        if (is_numeric($customer_id)) 
        {
            $sales = $this->db->get()->result();
            if($sales)
            {
                return $sales;
            }
        }
        return false;
    }

    public function getSaleByID($id = '')
    {
        $this->db->select('tblsales.*,tblstaff.fullname as creater,(SELECT fullname  FROM tblstaff WHERE user_head_id=tblstaff.staffid) as head,(SELECT fullname  FROM tblstaff WHERE user_admin_id=tblstaff.staffid) as admin');
        $this->db->from('tblsales');
        $this->db->join('tblstaff','tblstaff.staffid=tblsales.create_by','left');
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            $invoice = $this->db->get()->row();

            if ($invoice) {
                $invoice->items       = $this->getSaleItems($id);
            }
            return $invoice;
        }

        return false;
    }

    public function getSaleItems($id)
    {
        $this->db->select('tblsale_items.*,tblitems.name as product_name,tblitems.description,tblunits.unit as unit_name,tblunits.unitid as unit_id,tblitems.prefix,tblitems.code,');
        $this->db->from('tblsale_items');
        $this->db->join('tblitems','tblitems.id=tblsale_items.product_id','left');
        $this->db->join('tblunits','tblunits.unitid=tblitems.unit','left');
        $this->db->where('sale_id', $id);
        $items = $this->db->get()->result();
        return $items;

    }

    public function update_status($id,$data)
    {
        $this->db->where('id',$id);
        $this->db->update('tblsales',$data);
        if ($this->db->affected_rows() > 0) 
        {
            return true;
        }
        return false;
    }

    public function add($data)
   {
        $import=array(
            'rel_type'=>$data['rel_type'],
            'rel_id'=>$data['rel_id'],
            'rel_code'=>$data['rel_code'],
            'prefix'=>$data['prefix'],
            'name'=>$data['name'],
            'code'=>$data['code'],
            'customer_id'=>$data['customer_id'],
            'reason'=>$data['reason'],
            'date'=>to_sql_date($data['date']),
            'account_date'=>to_sql_date($data['account_date']),
            'create_by'=>get_staff_user_id()
            );
        
        $this->db->insert('tblsales', $import);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('New Sale Added [ID:' . $insert_id . ', ' . $data['date'] . ']');
            $items=$data['items'];
             $total=0;
             $count=0;
             $affect_product=array();
            foreach ($items as $key => $item) {
                $product=$this->getProductById($item['id']);
                $sub_total=$product->price*$item['quantity'];
                $tax=$sub_total*$product->tax_rate/100;
                $amount=$sub_total+$tax;
                $total+=$amount;
                $item_data=array(
                    'sale_id'=>$insert_id,
                    'product_id'=>$item['id'],
                    'serial_no'=>$item['serial_no'],
                    'unit_id'=>$product->unit,
                    'quantity'=>$item['quantity'],
                    'unit_cost'=>$product->price,
                    'sub_total'=>$sub_total,
                    'tax_id'=>$product->tax,
                    'tax_rate'=>$product->tax_rate,
                    'tax'=>$tax,
                    'amount'=>$amount,
                    'warehouse_id'=>$data['warehouse_name'],
                    'tk_no'=>$item['tk_no'],
                    'tk_co'=>$item['tk_co']
                    );
                 $this->db->insert('tblsale_items', $item_data);
                 if($this->db->affected_rows()>0)
                 {
                    $affect_product[]=$item['id'];  
                    logActivity('Insert Sale Item Added [ID:' . $insert_id . ', Product ID' . $item['id'] . ']');
                    if(!empty($data['rel_id']))
                    {
                        $sale=$this->getSaleOrderItemByID($data['rel_id'],$item['id']);
                        $export_quantity=$sale->export_quantity+$item['quantity'];
                        $this->db->update('tblsale_order_items',array('export_quantity'=>$export_quantity),array('id'=>$sale->id));                       
                    }   
                 }
            }
            $this->checkExportOrder($data['rel_id']);
            $this->db->update('tblsales',array('total'=>$total),array('id'=>$insert_id));
            return $insert_id;
        }
        return false;
    }
    public function checkExportOrder($id)
    {
        if(!$id)
        {
            return false;
        }

        $items=$this->getSaleOrderItems($id);
        $count=0;
        $pending=0;
        foreach ($items as $key => $item) {
            if($item->quantity==$item->export_quantity)
            {
                $count++;
            }
            else
            {
                $pending=1;
            }
        }
        if($count==count($items))
        {
            $this->db->update('tblsale_orders',array('export_status'=>2),array('id'=>$id));
            return true;
        }
        if($pending)
        {
            $this->db->update('tblsale_orders',array('export_status'=>1),array('id'=>$id));
            return true;
        }
        return false;
    }

    public function getSaleOrderItems($id)
    {       
        $this->db->where('sale_id', $id);
        $q=$this->db->get('tblsale_order_items');
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
        return false;
    }

    public function getSaleOrderItemByID($id,$product_id)
    {       
            $this->db->where('sale_id', $id);
            $this->db->where('product_id', $product_id);
            $q=$this->db->get('tblsale_order_items');
            if($q->num_rows() > 0)
            {
                return $q->row();
            }
            return false;
    }

     public function update($data,$id)
   {
        $affected=0;
         $import=array(
            'rel_type'=>$data['rel_type'],
            'prefix'=>$data['prefix'],
            'name'=>$data['name'],
            'code'=>$data['code'],
            'customer_id'=>$data['customer_id'],
            'reason'=>$data['reason'],
            'date'=>to_sql_date($data['date']),
            'account_date'=>to_sql_date($data['account_date'])
            );
        
        if($this->db->update('tblsales',$import,array('id'=>$id)) && $this->db->affected_rows()>0)
        {
            logActivity('Edit Sale Updated [ID:' . $id . ', Date' . date('Y-m-d') . ']');
            $count=0;
            $affected=1;
        }
        $this->setDafaultConfirm($id);
        if ($id) {
            $items=$data['items'];
            $total=0;
            $affected_id=array();
            foreach ($items as $key => $item) {
                $affected_id[]=$item['id'];
                $product=$this->getProductById($item['id']);
                $sub_total=$product->price*$item['quantity'];
                $tax=$sub_total*$product->tax_rate/100;
                $amount=$sub_total+$tax;
                $total+=$amount;
                $itm=$this->getSaleItem($id,$item['id']);
                $item_data=array(
                    'sale_id'=>$id,
                    'product_id'=>$item['id'],
                    'serial_no'=>$item['serial_no'],
                    'unit_id'=>$product->unit,
                    'quantity'=>$item['quantity'],
                    'unit_cost'=>$product->price,
                    'sub_total'=>$sub_total,
                    'tax_id'=>$product->tax,
                    'tax_rate'=>$product->tax_rate,
                    'tax'=>$tax,
                    'amount'=>$amount,
                    'warehouse_id'=>$data['warehouse_name'],
                    'tk_no'=>$item['tk_no'],
                    'tk_co'=>$item['tk_co']
                    );
                if($itm)
                {
                    $this->db->update('tblsale_items', $item_data,array('id'=>$itm->id));
                    if($this->db->affected_rows()>0)
                     {
                        logActivity('Edit Sale Item Updated [ID:' . $id . ', Item ID' . $item['id'] . ']');
                     }
                }
                else
                {
                    $this->db->insert('tblsale_items', $item_data);
                    if($this->db->affected_rows()>0)
                     {
                        logActivity('Insert Sale Item Added [ID:' . $id . ', Item ID' . $item['id'] . ']');
                     }
                }
            }
                if(!empty($affected_id))
                {
                    $this->db->where('sale_id', $id);
                    $this->db->where_not_in('product_id', $affected_id);
                    $this->db->delete('tblsale_items');
                }

            $this->db->update('tblsales',array('total'=>$total),array('id'=>$id));
            return $id;
        }
        return false;
    }

    public function setDafaultConfirm($id)
    {
        $data=array(
            'user_head_id'=>NULL,
            'user_admin_id'=>NULL,
            'user_head_date'=>NULL,
            'user_admin_date'=>NULL,
            'status'=>0
            );
        $this->db->update('tblsales',$data,array('id'=>$id));
        if($this->db->affected_rows()>0)
        {
            return true;
        }
        return false;
    }

    public function getSaleItem($sale_id,$product_id)
    {
        if (is_numeric($sale_id) && is_numeric($product_id)) {
            $this->db->where('sale_id', $sale_id);
            $this->db->where('product_id', $product_id);
            return $this->db->get('tblsale_items')->row();
        }
        return false;
    }

    public function getProductById($id)
    {       
            $this->db->select('tblitems.*,tblunits.unit as unit_name,tbltaxes.name as tax_name, tbltaxes.taxrate as tax_rate');
            $this->db->join('tblunits','tblunits.unitid=tblitems.unit','left');
            $this->db->join('tbltaxes','tbltaxes.id=tblitems.tax','left');
            $this->db->where('tblitems.id', $id);
            return $this->db->get('tblitems')->row();
    }
    
    public function getWarehouseTypes($id = '')
    {
        $this->db->select('tbl_kindof_warehouse.*');
        $this->db->from('tbl_kindof_warehouse');
        if (is_numeric($id)) 
        {
            $this->db->where('id', $id);
            return $this->db->get()->row();
        }
        else 
        {
            return $this->db->get()->result_array();
        }

        return false;
    }

    public function delete($id)
    {
        if($this->db->delete('tblsales',array('id'=>$id)) && $this->db->delete('tblsale_items',array('sale_id'=>$id)));
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
}
