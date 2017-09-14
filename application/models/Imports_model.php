<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Imports_model extends CRM_Model
{
    private $statuses;
    function __construct()
    {
        parent::__construct();
    }
    public function getImportByID($id = '')
    {
        $this->db->select('tblimports.*,tblstaff.fullname as creater,(SELECT fullname  FROM tblstaff WHERE user_head_id=tblstaff.staffid) as head,(SELECT fullname  FROM tblstaff WHERE user_admin_id=tblstaff.staffid) as admin');
        $this->db->from('tblimports');
        $this->db->join('tblstaff','tblstaff.staffid=tblimports.create_by','left');
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            $invoice = $this->db->get()->row();
            if ($invoice) {
                $invoice->items       = $this->getImportItems($id);
            }
            return $invoice;
        }

        return false;
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

    public function update_status($id,$data)
    {
        $this->db->where('id',$id);
        $this->db->update('tblimports',$data);
        if ($this->db->affected_rows() > 0) {
            if($data['status']==2)
            {
                $this->updateWarehouse($id);
            }
            return true;
        }
        return false;
    }
    public function updateWarehouse($id)
    {
        $imports=$this->getImportByID($id);
        $count=0;
        if($imports)
        {
            foreach ($imports->items as $key => $value) 
            {
                //Chuyen Kho
                if($imports->rel_type=='transfer' && isset($value->warehouse_id_to))
                {
                    $count+=increaseProductQuantity($value->warehouse_id_to,$value->product_id,$value->quantity);
                    $count+=decreaseProductQuantity($value->warehouse_id,$value->product_id,$value->quantity);

                }
                //Tang kho
                else
                {
                    $item=$this->db->get_where('tblwarehouses_products',array('product_id'=>$value->product_id,'warehouse_id'=>$value->warehouse_id))->row();
                    if($item)
                    {   
                        $total_quantity=$value->quantity+$item->product_quantity;
                        $data=array('product_quantity'=>$total_quantity);
                        $this->db->update('tblwarehouses_products',$data,array('id'=>$item->id));
                        $count++;
                    }
                    else
                    {
                        $data=array(
                            'product_id'=>$value->product_id,
                            'warehouse_id'=>$value->warehouse_id,
                            'product_quantity'=>$value->quantity
                            );
                        $this->db->insert('tblwarehouses_products',$data);
                        $insert_id=$this->db->insert_id();
                        if($insert_id)
                        {
                            logActivity('Insert Warehouse Product [ID:' . $insert_id . ', Item ID' . $value->product_id . ']');
                            $count++;
                        }
                    }
                }
                
                
            }
        }        
        if ($count > 0) {
            return true;
        }
        return false;
    }

    public function getImportItems($id)
    {
        $this->db->select('tblimport_items.*,tblitems.name as product_name,tblitems.description,tblunits.unit as unit_name,tblunits.unitid as unit_id,tblitems.prefix,tblitems.code,tblwarehouses.warehouse as warehouse_name');
        $this->db->from('tblimport_items');
        $this->db->join('tblitems','tblitems.id=tblimport_items.product_id','left');
        $this->db->join('tblunits','tblunits.unitid=tblitems.unit','left');
        $this->db->join('tblwarehouses','tblwarehouses.warehouseid=tblimport_items.warehouse_id','left');
        $this->db->where('import_id', $id);
        $items = $this->db->get()->result();
        return $items;

    }
    public function get_roles()
    {
        $is_admin = is_admin();
        $roles = $this->db->get('tblroles')->result_array();
        return $roles;
    }
    public function add_warehouses_adjustment($data)
    {
        if (is_admin()) {
            $this->db->insert('tblwarehouses_products',$data);
            if ($this->db->affected_rows() >0) {
                return true;
            }
            return false;
        }
        return false;
    }
    public function update_warehouses_adjustment($data_vestion,$id)
    {
        if (is_admin()) {
            // var_dump($data_vestion);die();
            $this->db->where('id',$id);
            $this->db->update('tblwarehouses_products',$data_vestion);
            if ($this->db->affected_rows() >0) {
                return true;
            }
            return false;
        }
        return false;
    }
    public function delete_warehouses_adjustment($id)
    {
        if (is_admin()) {
            $this->db->where('id', $id);
            $this->db->delete('tblimports');
            if ($this->db->affected_rows() > 0) {
                $this->db->where('import_id', $id);
                $this->db->delete('tblimport_items');
                return true;
            }
        }
        return false;
    }
    public function cancel_warehouses_adjustment($id)
    {
        $import=$this->getImportByID($id);
        if (is_admin()) {
            $this->db->where('id', $id);
            $this->db->update('tblimports',array('canceled_at'=>date('Y-m-d H:i:s')));
            if ($this->db->affected_rows() > 0) {
                if($import->status==2)
                {
                    foreach ($import->items as $key => $item) {
                        //Chuyen kho
                        if($import->rel_type=='transfer')
                        {
                            decreaseProductQuantity($item->warehouse_id_to,$item->product_id,$item->quantity);
                            increaseProductQuantity($item->warehouse_id,$item->product_id,$item->quantity);
                        }
                        //Nhap kho theo HD
                        elseif($import->rel_type=='contract')
                        {
                          decreaseProductQuantity($item->warehouse_id,$item->product_id,$item->quantity_net);
                        }
                        //Khac
                        else
                        {
                            decreaseProductQuantity($item->warehouse_id,$item->product_id,$item->quantity);
                        }
                    }
                }
                return true;
            }
        }
        return false;
    }
    public function restore_warehouses_adjustment($id)
    {
        $import=$this->getImportByID($id);
        if (is_admin()) {
            $this->db->where('id', $id);
            $this->db->update('tblimports',array('canceled_at'=>NULL));
            if ($this->db->affected_rows() > 0) {
                if($import->status==2)
                {
                    foreach ($import->items as $key => $item) {
                        //Chuyen kho
                        if($import->rel_type=='transfer')
                        {
                            increaseProductQuantity($item->warehouse_id_to,$item->product_id,$item->quantity);
                            decreaseProductQuantity($item->warehouse_id,$item->product_id,$item->quantity);
                        }
                        //Nhap kho theo HD
                        elseif($import->rel_type=='contract')
                        {
                          increaseProductQuantity($item->warehouse_id,$item->product_id,$item->quantity_net);
                        }
                        //Khac
                        else
                        {
                            increaseProductQuantity($item->warehouse_id,$item->product_id,$item->quantity);
                        }
                    }
                }
                return true;
            }
        }
        return false;
    }
    public function get_row_unit($id)
    {
        if (is_admin()) {
            $this->db->select('tblwarehouses_products.*');
            $this->db->where('tblwarehouses_products.id', $id);
            return $this->db->get('tblwarehouses_products')->row();
        }
    }

    public function getProductById($id)
    {       
            $this->db->select('tblitems.*,tblunits.unit as unit_name,tbltaxes.name as tax_name, tbltaxes.taxrate as tax_rate');
            $this->db->join('tblunits','tblunits.unitid=tblitems.unit','left');
            $this->db->join('tbltaxes','tbltaxes.id=tblitems.tax','left');
            $this->db->where('tblitems.id', $id);
            return $this->db->get('tblitems')->row();
    }


    /**
     * Get all invoice items
     * @param  mixed $id invoiceid
     * @return array
     */
    public function get_invoice_items($id)
    {
        $this->db->select('tblpurchase_plan_details.*,tblitems.name,tblitems.description,tblunits.unit as unit_name,tblunits.unitid as unit_id');
        $this->db->from('tblpurchase_plan_details');
        $this->db->join('tblitems','tblitems.id=tblpurchase_plan_details.product_id','left');
        $this->db->join('tblunits','tblunits.unitid=tblitems.unit','left');
        $this->db->where('purchase_plan_id', $id);
        $items = $this->db->get()->result_array();
        return $items;

    }

   public function add($data)
   {
        $import=array(
            'supplier_id'=>$data['supplier_id'],
            'customer_id'=>$data['customer_id'],
            'rel_type'=>$data['rel_type'],
            'rel_id'=>$data['rel_id'],
            'prefix'=>$data['prefix'],
            'name'=>$data['name'],
            'code'=>$data['code'],
            'reason'=>$data['reason'],
            'date'=>to_sql_date($data['date']),
            'account_date'=>to_sql_date($data['account_date']),
            'create_by'=>get_staff_user_id()
            );
        
        $this->db->insert('tblimports', $import);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('New Import Added [ID:' . $insert_id . ', ' . $data['description'] . ']');
            $items=$data['items'];
             $total=0;

            foreach ($items as $key => $item) {
                $product=$this->getProductById($item['id']);
                $sub_total=$product->price*$item['quantity'];                
                if($data['rel_id'] && $data['rel_type']=='contract')
                {
                    $sub_total=$item['quantity_net']*$item['exchange_rate']*$item['price_buy'];
                }
                if($data['rel_type']=='return')
                {
                    $sub=$item['unit_cost']*$item['quantity'];
                    $tax=$sub*$product->tax_rate/100;
                    $sub_total=$sub+$tax;
                }
                $total+=$sub_total;
                $item_data=array(
                    'import_id'=>$insert_id,
                    'product_id'=>$item['id'],
                    'specifications'=>$product->description,
                    'unit_id'=>$product->unit,
                    'quantity'=>$item['quantity'],
                    'quantity_net'=>$item['quantity_net'],
                    'exchange_rate'=>$item['exchange_rate'],
                    'unit_cost'=>(($data['rel_id'] && $data['rel_type']=='contract')?$item['price_buy']:$product->price),
                    'sub_total'=>$sub_total,
                    'tk_no'=>$item['tk_no'],
                    'tk_co'=>$item['tk_co'],
                    'tax_id'=>$product->tax,
                    'tax_rate'=>$product->tax_rate,
                    'tax'=>$tax,
                    'warehouse_id'=>$data['warehouse_id'],
                    'warehouse_id_to'=>$data['warehouse_id_to']
                    );
                 $this->db->insert('tblimport_items', $item_data);
                 if($this->db->affected_rows()>0)
                 {
                    logActivity('Insert Import Item Added [ID:' . $insert_id . ', Item ID' . $item['id'] . ']');
                 }
            }
            $this->db->update('tblimports',array('total'=>$total),array('id'=>$insert_id));
            return $insert_id;
        }
        return false;
    }

     public function update($data,$id)
   {

        $affected=0;
        $import=array(
            'supplier_id'=>$data['supplier_id'],
            'customer_id'=>$data['customer_id'],
            'rel_id'=>$data['rel_id'],
            'prefix'=>$data['prefix'],
            'name'=>$data['name'],
            'code'=>$data['code'],
            'date'=>to_sql_date($data['date']),
            'account_date'=>to_sql_date($data['account_date']),
            'reason'=>$data['reason']
            );
        
        if($this->db->update('tblimports',$import,array('id'=>$id)) && $this->db->affected_rows()>0)
        {
            logActivity('Edit Import Item Updated [ID:' . $id . ', Item ID' . $item['id'] . ']');
            $count=0;
            $affected=1;
        }
        if ($id) {
            $items=$data['items'];
            $total=0;
            $affected_id=array();
            foreach ($items as $key => $item) {
                $affected_id[]=$item['id'];
                $product=$this->getProductById($item['id']);
                $sub_total=$product->price*$item['quantity'];
                if($data['rel_id'] && $data['rel_type']=='contract')
                {
                    $sub_total=$item['quantity_net']*$item['exchange_rate']*$item['price_buy'];
                }
                if($data['rel_type']=='return')
                {
                    $sub=$item['unit_cost']*$item['quantity'];
                    $tax=$sub*$product->tax_rate/100;
                    $sub_total=$sub+$tax;
                }
                $total+=$sub_total;
                $itm=$this->getImportItem($id,$item['id']);
                $item_data=array(
                    'import_id'=>$id,
                    'product_id'=>$item['id'],
                    'specifications'=>$product->description,
                    'unit_id'=>$product->unit,
                    'quantity'=>$item['quantity'],
                    'unit_cost'=>(($data['rel_id'] && $data['rel_type']=='contract')?$item['price_buy']:$product->price),
                    'sub_total'=>$sub_total,
                    'tk_no'=>$item['tk_no'],
                    'tk_co'=>$item['tk_co'],
                    'tax_id'=>$product->tax,
                    'tax_rate'=>$product->tax_rate,
                    'tax'=>$tax,
                    'warehouse_id'=>$data['warehouse_id'],
                    'warehouse_id_to'=>$data['warehouse_id_to']
                    );
                if($itm)
                {
                    $this->db->update('tblimport_items', $item_data,array('id'=>$itm->id));
                    if($this->db->affected_rows()>0)
                     {
                        logActivity('Edit Import Item Updated [ID:' . $id . ', Item ID' . $item['id'] . ']');
                     }
                }
                else
                {
                    $this->db->insert('tblimport_items', $item_data);
                    if($this->db->affected_rows()>0)
                     {
                        logActivity('Insert Import Item Added [ID:' . $id . ', Item ID' . $item['id'] . ']');
                     }
                }
            }
                if(!empty($affected_id))
                {
                    $this->db->where('import_id', $id);
                    $this->db->where_not_in('product_id', $affected_id);
                    $this->db->delete('tblimport_items');
                }

            $this->db->update('tblimports',array('total'=>$total),array('id'=>$id));
            return $id;
        }
        return false;
    }

    

    public function getImportItem($import_id,$product_id)
    {
        if (is_numeric($import_id) && is_numeric($product_id)) {
            $this->db->where('import_id', $import_id);
            $this->db->where('product_id', $product_id);
            return $this->db->get('tblimport_items')->row();
        }
        return false;
    }

    
}
