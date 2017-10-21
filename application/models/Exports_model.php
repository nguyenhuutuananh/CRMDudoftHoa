<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Exports_model extends CRM_Model
{
    private $statuses;
    function __construct()
    {
        parent::__construct();
    }

    public function getExportByID($id = '')
    {
        $this->db->select('tblexports.*,tblstaff.fullname as creater,(SELECT fullname  FROM tblstaff WHERE user_head_id=tblstaff.staffid) as head,(SELECT fullname  FROM tblstaff WHERE user_admin_id=tblstaff.staffid) as admin,(SELECT fullname  FROM tblstaff WHERE receiver_id=tblstaff.staffid) as receiver_name,(SELECT tblroles.name  FROM tblstaff JOIN tblroles ON tblroles.roleid=tblstaff.role WHERE receiver_id=tblstaff.staffid) as receiver_department,tblclients.company as customer_name,(SELECT tblsales.date  FROM tblsales  WHERE tblexports.rel_id=tblsales.id) as order_date');
        $this->db->from('tblexports');
        $this->db->join('tblstaff','tblstaff.staffid=tblexports.create_by','left');//,tblsales.date as order_date
        $this->db->join('tblclients','tblclients.userid=tblexports.customer_id','left');
        $this->db->join('tblroles','tblroles.roleid=tblstaff.role','left');
        // $this->db->join('tblsales','tblsales.id=tblexports.rel_id','left');
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            $invoice = $this->db->get()->row();
            // var_dump($invoice);die();
            if ($invoice) {
                $invoice->items       = $this->getExportItems($id);
            }
            return $invoice;
        }

        return false;
    }

    public function getExportItems($id)
    {
        $this->db->select('tblexport_items.*,tblitems.name as product_name,tblitems.description,tblunits.unit as unit_name,tblunits.unitid as unit_id,tblitems.prefix,tblitems.code,tblitems.short_name');
        $this->db->from('tblexport_items');
        $this->db->join('tblitems','tblitems.id=tblexport_items.product_id','left');
        $this->db->join('tblunits','tblunits.unitid=tblitems.unit','left');
        $this->db->where('export_id', $id);
        $items = $this->db->get()->result();
        return $items;

    }

    public function update_status($id,$data)
    {
        $this->db->where('id',$id);
        $this->db->update('tblexports',$data);

        if ($this->db->affected_rows() > 0) 
        {
            if($data['status']==2)
            {
                $this->decreaseWarehouse($id);
            }
            if($data['delivery_status']==2)
            {
                $this->updateSODeliveryQuanity($id);
            }
            return true;
        }
        return false;
    }

    public function updateSODeliveryQuanity($id)
    {
        $this->db->select('tblexport_items.*,tblexports.rel_id');
        $this->db->where('tblexports.id',$id);
        $this->db->join('tblexport_items','tblexport_items.export_id=tblexports.id');
        $info=$this->db->get('tblexports')->result();
        $sale_id=$info[0]->rel_id;
        if($info)
        {
            foreach ($info as $key => $item) {
                $this->increaseSODeliveryQuanity($sale_id,$item->product_id,$item->quantity);
            }
            return true;
        }
        return false;
    }

    public function increaseSODeliveryQuanity($sale_id,$product_id,$quantity)
    {        
        $product=$this->getSaleItemByID($sale_id,$product_id);
        $delivery_quantity=$quantity+$product->delivery_quantity;
        $this->db->update('tblsale_items',array('delivery_quantity'=>$delivery_quantity),array('id'=>$product->id));
        if ($this->db->affected_rows()>0) {
            return true;
        }
        return false;
    }

    public function decreaseSODeliveryQuanity($sale_id,$product_id,$quantity)
    {
        $product=$this->getSaleItemByID($sale_id,$product_id);
        $delivery_quantity=$product->delivery_quantity-$quantity;
        $this->db->update('tblsale_items',array('delivery_quantity'=>$delivery_quantity),array('id'=>$product->id));
        if ($this->db->affected_rows()>0) {
            return true;
        }
        return false;
    }





    public function decreaseWarehouse($id)
    {
        $export=$this->getExportByID($id);
        $count=0;
        if($export)
        {
            $warehouse_id_temp=get_option('default_PSO_warehouse');
            foreach ($export->items as $key => $value) 
            {   
                $warehouse_id=$value->warehouse_id;
                $item=$this->db->get_where('tblwarehouses_products',array('product_id'=>$value->product_id,'warehouse_id'=>$warehouse_id_temp))->row();

                if($item)
                {
                    //Temp: >0 con hang <0 Thieu hang
                    $total_quantity=$item->product_quantity-$value->quantity;
                    // $total_quantity=(($item->product_quantity-$value->quantity)>0)? ($item->product_quantity-$value->quantity): 0 ;
                    $data=array('product_quantity'=>$total_quantity);
                    $this->db->update('tblwarehouses_products',$data,array('id'=>$item->id));
                    $count++;
                }
                else
                {
                    //Temp: ko co hang(Thieu hang <0)
                    $data=array(
                        'product_id'=>$value->product_id,
                        'warehouse_id'=>$warehouse_id_temp,
                        'product_quantity'=>$value->quantity*(-1)
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
        if ($count > 0) {
            return true;
        }
        return false;
    }

    public function increaseWarehouse($id)
    {

        $exports=$this->getExportByID($id);
        $count=0;
        if($exports)
        {   
            $warehouse_id_temp=get_option('default_PSO_warehouse');
            foreach ($exports->items as $key => $value) 
            {
                $warehouse_id=$value->warehouse_id;
                $item=$this->db->get_where('tblwarehouses_products',array('product_id'=>$value->product_id,'warehouse_id'=>$warehouse_id_temp))->row();

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
                        'warehouse_id'=>$warehouse_id_temp,
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
        if ($count > 0) {
            return true;
        }
        return false;
    }

    public function add($data)
    {
        $export=array(
            'rel_type'=>$data['rel_type'],
            'rel_id'=>$data['rel_id'],
            'rel_code'=>$data['rel_code'],
            'prefix'=>$data['prefix'],
            'name'=>$data['name'],
            'code'=>$data['code'],
            'customer_id'=>$data['customer_id'],
            'receiver_id'=>$data['receiver_id'],
            'reason'=>$data['reason'],
            'date'=>to_sql_date($data['date']),
            'create_by'=>get_staff_user_id(),
            'transport_fee'=>$data['transport_fee'],
            'installation_fee'=>$data['installation_fee'],
            'delivery_fee'=>$data['delivery_fee']
            );
        $this->db->insert('tblexports', $export);        
        $insert_id = $this->db->insert_id();
        
        if ($insert_id) {
            logActivity('New Export Added [ID:' . $insert_id . ', ' . $data['date'] . ']');
            $items=$data['items'];
             $total=0;
             $count=0;
             $affect_product=array();
            foreach ($items as $key => $item) {

                $product=$this->getProductById($item['id']);
                $sub_total=$product->price*$item['quantity'];
                $tax=$sub_total-$sub_total/($product->tax_rate*0.01+1);
                $discount=$sub_total*$item['discount_percent']/100;
                $amount=$sub_total-$discount;
                $total+=$amount;
                
                $item_data=array(
                    'export_id'=>$insert_id,
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
                    'discount'=>$item['discount'],
                    'discount_percent'=>$item['discount_percent']
                    );
                 $this->db->insert('tblexport_items', $item_data);
                 // var_dump($data);die();
                 if($this->db->affected_rows()>0)
                 {        
                     
                    $affect_product[]=$item['id'];  
                    if(!empty($data['rel_id']))
                    {
                        $sale=$this->getSaleItemByID($data['rel_id'],$item['id']);
                        $export_quantity=$sale->export_quantity+$item['quantity'];
                        $this->db->update('tblsale_items',array('export_quantity'=>$export_quantity),array('id'=>$sale->id));                       
                    }                            
                    logActivity('Insert Export Item Added [ID:' . $insert_id . ', Product ID' . $item['id'] . ']');
                 }
            }
                
            $this->checkExportSale($data['rel_id']);
            $total_discount=$data['discount_percent']*$total/100;
            $total=$total-$total_discount+$data['adjustment']+$data['transport_fee']+$data['installation_fee']+$data['delivery_fee'];
            $this->db->update('tblexports',array('total'=>$total),array('id'=>$insert_id));
            return $insert_id;
        }
        return false;
    }

    public function checkExportSale($id)
    {
        if(!$id)
        {
            return false;
        }

        $items=$this->getSaleItems($id);
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
            $this->db->update('tblsales',array('export_status'=>2),array('id'=>$id));
            return true;
        }
        if($pending)
        {
            $this->db->update('tblsales',array('export_status'=>1),array('id'=>$id));
            return true;
        }
        return false;
    }

    

    public function getSaleItemByID($id,$product_id)
    {       
            $this->db->where('sale_id', $id);
            $this->db->where('product_id', $product_id);
            $q=$this->db->get('tblsale_items');
            if($q->num_rows() > 0)
            {
                return $q->row();
            }
            return false;
    }

    public function getSaleItems($id,$product_id)
    {       
        $this->db->where('sale_id', $id);
        $this->db->where_in('product_id', $product_id);
        $q=$this->db->get('tblsale_items');
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
        return false;
    }

    public function getSaleByID($id)
    {       
        $this->db->where('id', $id);
        $q=$this->db->get('tblsales');
        if($q->num_rows() > 0)
        {
            $res=$q->row();
            $res->items=$this->getSaleItems($id);
            return $res;
        }
        return false;
    }

    public function update($data,$id)
   {
        $affected=false;
        $export=array(
            'rel_type'=>$data['rel_type'],
            'rel_code'=>$data['rel_code'],
            'prefix'=>$data['prefix'],
            'name'=>$data['name'],
            'code'=>$data['code'],
            'customer_id'=>$data['customer_id'],
            'receiver_id'=>$data['receiver_id'],
            'reason'=>$data['reason'],
            'date'=>to_sql_date($data['date']),
            'transport_fee'=>$data['transport_fee'],
            'installation_fee'=>$data['installation_fee'],
            'delivery_fee'=>$data['delivery_fee']
            );



        $export_detail=$this->getExportByID($id);
        if($this->db->update('tblexports',$export,array('id'=>$id)) && $this->db->affected_rows()>0)
        {
            logActivity('Edit Export Updated [ID:' . $id . ', ' . date('Y-m-d') . ']');
            $count=0;
            $affected=true;
        }
        if ($id) {
            $items=$data['items'];
            $total=0;
            $affected_id=array();
            foreach ($items as $key => $item) {
                $affected_id[]=$item['id'];
                $product=$this->getProductById($item['id']);
                $sub_total=$product->price*$item['quantity'];
                $tax=$sub_total-$sub_total/($product->tax_rate*0.01+1);
                $discount=$sub_total*$item['discount_percent']/100;
                $amount=$sub_total+$discount;
                $total+=$amount;
                $itm=$this->getExportItem($id,$item['id']);
                $item_data=array(
                    'export_id'=>$id,
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
                    'discount'=>$item['discount'],
                    'discount_percent'=>$item['discount_percent']
                    );
                $export_quantity=$item['quantity']-$itm->quantity;
                if($itm)
                {
                    $this->db->update('tblexport_items', $item_data,array('id'=>$itm->id));
                    if($this->db->affected_rows()>0)
                     {
                        logActivity('Edit Export Item Updated [ID:' . $id . ', Item ID' . $item['id'] . ']');
                     }
                }
                else
                {
                    $this->db->insert('tblexport_items', $item_data);
                    if($this->db->affected_rows()>0)
                     {                        
                        logActivity('Insert Export Item Added [ID:' . $id . ', Item ID' . $item['id'] . ']');
                     }
                }
                if(isset($export_detail->rel_id))
                {
                    $this->updateSOExportQuantity($export_detail->rel_id,$item['id'],$export_quantity);
                }

            }
                if(!empty($affected_id))
                {
                    if(isset($export_detail->rel_id))
                    {
                        $this->db->where('export_id', $id);
                        $this->db->where_not_in('product_id', $affected_id);
                        $del_items=$this->db->get('tblexport_items')->result();
                        $sale_id=$export_detail->rel_id;
                        foreach ($del_items as $key => $item) {
                            $this->updateSOExportQuantity($sale_id,$item->product_id,(-1)*$item->quantity);
                        }
                        
                    }

                    $this->db->where('export_id', $id);
                    $this->db->where_not_in('product_id', $affected_id);
                    $this->db->delete('tblexport_items');
                }
            $total_discount=$data['discount_percent']*$total/100;
            $total=$total-$total_discount+$data['adjustment']+$data['transport_fee']+$data['installation_fee']+$data['delivery_fee'];
            $this->db->update('tblexports',array('total'=>$total),array('id'=>$id));
            return $id;
        }
        return false;
    }

    public function updateSOExportQuantity($sale_id=NULL,$product_id=NULL,$quantity)
    {

        if(is_numeric($sale_id) && is_numeric($product_id) && is_numeric($quantity))
        {
            $item=$this->db->get_where('tblsale_items',array('sale_id'=>$sale_id,'product_id'=>$product_id))->row();
            $total_quantity_export=$quantity+$item->export_quantity;
            $this->db->update('tblsale_items',array('export_quantity'=>$total_quantity_export),array('id'=>$item->id));  
            if($this->db->affected_rows()>0)
            {
                return true;
            }  
        }
        return false;
    }

    public function update_delivery($data,$id)
   {
        $affected=false;
        $delivery=array(
            'delivery_code'=>$data['delivery_code'],
            'deliverer_id'=>$data['deliverer_id'],
            'note'=>$data['note'],
            'delivery_date'=>to_sql_date($data['delivery_date'])
            );
        
        if($this->db->update('tblexports',$delivery,array('id'=>$id)) && $this->db->affected_rows()>0)
        {
            logActivity('Edit Delivery From Export Updated [ID:' . $id . ', ' . date('Y-m-d') . ']');
            $affected=true;
        }
        if($affected)
        {
            return true;
        }
        
        return false;
    }

    public function getExportItem($import_id,$product_id)
    {
        if (is_numeric($import_id) && is_numeric($product_id)) {
            $this->db->where('export_id', $import_id);
            $this->db->where('product_id', $product_id);
            return $this->db->get('tblexport_items')->row();
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
        $this->db->where('id', $id);
        $this->db->delete('tblexports');
        if ($this->db->affected_rows() > 0) {
            $this->db->where('export_id', $id);
            $this->db->delete('tblexport_items');
            return true;
        }
        return false;
    }

    public function delete_delivery($id)
    {
        $delivery=array(
            'delivery_code'=>NULL,
            'deliverer_id'=>NULL,
            'note'=>NULL,
            'delivery_date'=>NULL,
            'delivery_status'=>0
            );

        $this->db->select('tblexport_items.*,tblexports.rel_id,tblexports.delivery_status');
        $this->db->where('tblexports.id',$id);
        $this->db->join('tblexport_items','tblexport_items.export_id=tblexports.id');
        $info=$this->db->get('tblexports')->result();

        $status=$info[0]->delivery_status;
        $sale_id=$info[0]->rel_id;
        if($status==2)
        {
            foreach ($info as $key => $item) {
                $this->decreaseSODeliveryQuanity($sale_id,$item->product_id,$item->quantity);
            }
        }

        $this->db->update('tblexports',$delivery,array('id'=>$id));
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
    public function cancel($id)
    {
        $export=$this->getExportByID($id);
        $data=array('canceled_at'=>date('Y-m-d H:i:s'));
        $this->db->where('id', $id);
        $this->db->update('tblexports',$data,array('id'=>$id));
        if ($this->db->affected_rows() > 0) {
            if($export->status==2)
            {
                $this->increaseWarehouse($id);
            }
            $del_items=$export->items;
            $sale_id=$export->rel_id;
            foreach ($del_items as $key => $item) {
                $this->updateSOExportQuantity($sale_id,$item->product_id,(-1)*$item->quantity);
            }
            // $this->db->where('export_id', $id);
            // $this->db->delete('tblexport_items');
            return true;
        }
        return false;
    }

    public function restore($id)
    {
        $export=$this->getExportByID($id);
        $data=array('canceled_at'=>NULL);
        $this->db->where('id', $id);
        $this->db->update('tblexports',$data,array('id'=>$id));
        if ($this->db->affected_rows() > 0) {
            if($export->status==2)
            {
                $this->decreaseWarehouse($id);
            }
            $del_items=$export->items;
            $sale_id=$export->rel_id;
            foreach ($del_items as $key => $item) {
                $this->updateSOExportQuantity($sale_id,$item->product_id,$item->quantity);
            }
            // $this->db->where('export_id', $id);
            // $this->db->delete('tblexport_items');
            return true;
        }
        return false;
    }
}
