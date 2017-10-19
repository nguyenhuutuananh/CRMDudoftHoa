<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sale_oders_model extends CRM_Model
{
    private $statuses;
    function __construct()
    {
        parent::__construct();
    }

    public function getSaleByID($id = '')
    {
        $this->db->select('tblsale_orders.*,tblstaff.fullname as creater,(SELECT fullname  FROM tblstaff WHERE user_head_id=tblstaff.staffid) as head,(SELECT fullname  FROM tblstaff WHERE user_admin_id=tblstaff.staffid) as admin');
        $this->db->from('tblsale_orders');
        $this->db->join('tblstaff','tblstaff.staffid=tblsale_orders.create_by','left');
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

    public function getYears()
    {
        $this->db->distinct();
        $this->db->select('YEAR(date) as year');
        $this->db->order_by('YEAR(date)');
        $this->db->from('tblsale_orders');
        $q=$this->db->get();
        if($q->num_rows()>0)
            return $q->result();

        return false;
    }

    public function getSaleOrderDetails($month=NULL,$year=NULL)
    {
        $this->db->select('COUNT(code) as quantity, SUM(total) as grand_total ,MONTH(date) as month,YEAR(date) as year');
        $this->db->from('tblsale_orders');
        if(is_numeric($month))
            $this->db->where('MONTH(date)',$month);
        if(is_numeric($year))
            $this->db->where('YEAR(date)',$year);
        $orders = $this->db->get()->row();
        if($orders)
            return $orders;
        return false;

    }

    public function getSaleSODetails($month=NULL,$year=NULL)
    {
        $this->db->select('COUNT(code) as quantity, SUM(total) as grand_total ,MONTH(date) as month,YEAR(date) as year');
        $this->db->from('tblsales');
        if(is_numeric($month))
            $this->db->where('MONTH(date)',$month);
        if(is_numeric($year))
            $this->db->where('YEAR(date)',$year);
        $orders = $this->db->get()->row();
        if($orders)
            return $orders;
        return false;

    }

    public function getSaleItems($id)
    {
        $this->db->select('tblsale_order_items.*,tblitems.name as product_name,tblitems.description,tblunits.unit as unit_name,tblunits.unitid as unit_id,tblitems.prefix,tblitems.code,tblitems.short_name');
        $this->db->from('tblsale_order_items');
        $this->db->join('tblitems','tblitems.id=tblsale_order_items.product_id','left');
        $this->db->join('tblunits','tblunits.unitid=tblitems.unit','left');
        $this->db->where('sale_id', $id);
        $items = $this->db->get()->result();
        return $items;

    }
    public function getReturnSaleItems($id)
    {
        $this->db->select('tblsale_order_items.*,tblitems.name as product_name,tblitems.description,tblunits.unit as unit_name,tblunits.unitid as unit_id,tblitems.prefix,tblitems.code,');
        $this->db->from('tblsale_order_items');
        $this->db->join('tblitems','tblitems.id=tblsale_order_items.product_id','left');
        $this->db->join('tblunits','tblunits.unitid=tblitems.unit','left');
        $this->db->where('reject_id', $id);
        $items = $this->db->get()->result();
        return $items;

    }

    public function update_status($id,$data)
    {
        $this->db->where('id',$id);
        $this->db->update('tblsale_orders',$data);
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
            'date_ht'=>$data['date_ht'],
            'customer_id'=>$data['customer_id'],
            'reason'=>$data['reason'],
            'date'=>to_sql_date($data['date']),
            'create_by'=>get_staff_user_id(),
            'discount_percent'=>$data['discount_percent'],
            'adjustment'=>$data['adjustment'],
            'transport_fee'=>$data['transport_fee'],
            'installation_fee'=>$data['installation_fee']
            );
        
        
        $this->db->insert('tblsale_orders', $import);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            logActivity('New Sale Added [ID:' . $insert_id . ', ' . $data['date'] . ']');
            $items=$data['items'];
            $total=0;

            //New TK
            $itemsTK=$data['item'];
            $i=0;
            foreach ($items as $key => $item) {
                $product=$this->getProductById($item['id']);
                $sub_total=$product->price*$item['quantity'];
                $tax=$sub_total-$sub_total/($product->tax_rate*0.01+1);
                $discount=$sub_total*$item['discount_percent']/100;
                $amount=$sub_total-$discount;
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
                    'tk_no'=>($itemsTK[$i]['tk_no']?$itemsTK[$i]['tk_no']:$item['tk_no']),
                    'tk_co'=>($itemsTK[$i]['tk_co']?$itemsTK[$i]['tk_co']:$item['tk_co']),
                    'tk_ck'=>$item['tk_ck'],
                    'tk_thue'=>$item['tk_thue'],
                    'tk_gv'=>$item['tk_gv'],
                    'tk_kho'=>$item['tk_kho'],
                    'tax_rate'=>$product->tax_rate,
                    'tax'=>$tax,
                    'amount'=>$amount,
                    'warehouse_id'=>$data['warehouse_name'],
                    'discount'=>$item['discount'],
                    'discount_percent'=>$item['discount_percent']
                    );
                
                 $this->db->insert('tblsale_order_items', $item_data);
                 if($this->db->affected_rows()>0)
                 {
                    // Chuyen qua kho cho ban
                    $product_id=$item['id'];
                    $quantity=$item['quantity'];
                    $warehouse_id_from=$data['warehouse_name'];
                    $warehouse_id_to=get_option('default_PSO_warehouse');
                    //Start Tang kho cho
                    increaseProductQuantity($warehouse_id_to,$product_id,$quantity);
                    // Giam kho hang ban
                    decreaseProductQuantity($warehouse_id_from,$product_id,$quantity);
                    // Chuyen qua kho cho ban end
                    updateSaleProductDetail($insert_id,$item['id'],$item['exports']);
                    logActivity('Insert Sale Item Added [ID:' . $insert_id . ', Product ID' . $item['id'] . ']');
                 }
                 $i++;
            }

            $total_discount=$data['discount_percent']*$total/100;
            $total=$total-$total_discount+$data['adjustment']+$data['transport_fee']+$data['installation_fee'];
            $this->db->update('tblsale_orders',array('total'=>$total,'discount'=>$total_discount),array('id'=>$insert_id));
            $this->db->update('tblcontracts',array('export_status'=>1),array('id'=>$data['rel_id']));
            return $insert_id;
        }
        return false;
    }

    public function update($data,$id)
    {
        $warehouse_id=NULL;
        $affected=0;
        
         $import=array(
            'rel_type'=>$data['rel_type'],
            'prefix'=>$data['prefix'],
            'name'=>$data['name'],
            'code'=>$data['code'],
            'date_ht'=>$data['date_ht'],
            'customer_id'=>$data['customer_id'],
            'reason'=>$data['reason'],
            'date'=>to_sql_date($data['date']),
            'discount_percent'=>$data['discount_percent'],
            'adjustment'=>$data['adjustment'],
            'transport_fee'=>$data['transport_fee'],
            'installation_fee'=>$data['installation_fee']
            );
        
        if($this->db->update('tblsale_orders',$import,array('id'=>$id)) && $this->db->affected_rows()>0)
        {
            logActivity('Edit Sale Updated [ID:' . $id . ', Date' . date('Y-m-d') . ']');
            $count=0;
            $affected=1;
        }
        $this->setDafaultConfirm($id);
        if ($id) {
            $items=$data['items'];
            $itemsR=$data['itemsR'];
            $total=0;
            $affected_id=array();
            $affected_idR=array();
            //New TK
            $itemsTK=$data['item'];
            $i=0;
            
            foreach ($items as $key => $item) {
                $affected_id[]=$item['id'];
                $product=$this->getProductById($item['id']);
                $sub_total=$product->price*$item['quantity'];
                $tax=$sub_total-$sub_total/($product->tax_rate*0.01+1);
                $discount=$sub_total*$item['discount_percent']/100;
                $amount=$sub_total-$discount;
                $total+=$amount;
                $warehouse_id=$data['warehouse_name'];
                $item_data=array(
                    'sale_id'=>$id,
                    'product_id'=>$item['id'],
                    'serial_no'=>$item['serial_no'],
                    'unit_id'=>$product->unit,
                    'quantity'=>$item['quantity'],
                    'unit_cost'=>$product->price,
                    'sub_total'=>$sub_total,
                    'tk_no'=>$itemsTK[$i]['tk_no'],
                    'tk_co'=>$itemsTK[$i]['tk_co'],
                    'tk_ck'=>$item['tk_ck'],
                    'tk_thue'=>$item['tk_thue'],
                    'tk_gv'=>$item['tk_gv'],
                    'tk_kho'=>$item['tk_kho'],
                    'tax_id'=>$product->tax,
                    'tax_rate'=>$product->tax_rate,
                    'tax'=>$tax,
                    'amount'=>$amount,
                    'warehouse_id'=>$data['warehouse_name'],
                    'discount'=>$item['discount'],
                    'discount_percent'=>$item['discount_percent']
                    );


                $itm=$this->getSaleItem($id,$item['id']);

                if($itm)
                {
                    $this->db->update('tblsale_order_items', $item_data,array('id'=>$itm->id));
                    if($this->db->affected_rows()>0)
                     {
                        // Chuyen qua kho cho ban
                        $product_id=$item['id'];
                        $quantity=$item['quantity']-$itm->quantity;
                        $warehouse_id_from=$data['warehouse_name'];
                        $warehouse_id_to=get_option('default_PSO_warehouse');
                        if($quantity>0)
                        {
                            //Start Tang kho cho
                            increaseProductQuantity($warehouse_id_to,$product_id,$quantity);
                            // Giam kho hang ban
                            decreaseProductQuantity($warehouse_id_from,$product_id,$quantity);
                            // Chuyen qua kho cho ban end
                        }
                        else
                        {
                            $quantity=abs($quantity);
                            //Start Giam kho cho
                            decreaseProductQuantity($warehouse_id_to,$product_id,$quantity);
                            // Tang kho hang ban
                            increaseProductQuantity($warehouse_id_from,$product_id,$quantity);
                            // Chuyen qua kho cho ban end                            
                        }
                        logActivity('Edit Sale Item Updated [ID:' . $id . ', Item ID' . $item['id'] . ']');
                     }
                }
                else
                {
                    $this->db->insert('tblsale_order_items', $item_data);
                    if($this->db->affected_rows()>0)
                     {
                        // Chuyen qua kho cho ban
                        $product_id=$item['id'];
                        $quantity=$item['quantity'];
                        $warehouse_id_from=$data['warehouse_name'];
                        $warehouse_id_to=get_option('default_PSO_warehouse');
                        //Start Tang kho cho
                        increaseProductQuantity($warehouse_id_to,$product_id,$quantity);
                        // Giam kho hang ban
                        decreaseProductQuantity($warehouse_id_from,$product_id,$quantity);
                        // Chuyen qua kho cho ban end
                        logActivity('Insert Sale Item Added [ID:' . $id . ', Item ID' . $item['id'] . ']');
                     }
                }
                updateSaleProductDetail($id,$item['id'],$item['exports']);
                $i++;
            }

            if(!empty($affected_id))
            {
                $this->db->where('sale_id', $id);
                $this->db->where_not_in('product_id', $affected_id);
                $del_items=$this->db->get('tblsale_order_items')->result();
                foreach ($del_items as $key => $item) {
                    // Chuyen qua kho ban
                    $product_id=$item->product_id;
                    $quantity=$item->quantity;
                    $warehouse_id_from=$item->warehouse_id;
                    $warehouse_id_to=get_option('default_PSO_warehouse');
                    //Start Tang kho ban
                    increaseProductQuantity($warehouse_id_from,$product_id,$quantity);
                    // Giam kho cho ban
                    decreaseProductQuantity($warehouse_id_to,$product_id,$quantity);
                    // Chuyen qua kho cho ban end
                }

                $this->db->where('sale_id', $id);
                $this->db->where_not_in('product_id', $affected_id);
                $this->db->delete('tblsale_order_items');
            }
            $total_discount=$data['discount_percent']*$total/100;
            $total=$total-$total_discount+$data['adjustment']+$data['transport_fee']+$data['installation_fee'];
            $this->db->update('tblsale_orders',array('total'=>$total,'discount'=>$total_discount),array('id'=>$id));

            foreach ($itemsR as $key => $item) {
                $affected_idR[]=$item['id'];
                $product=$this->getProductById($item['id']);
                $sub_total=$product->price*$item['quantity'];
                $tax=$sub_total*$product->tax_rate/100;
                $amount=$sub_total+$tax;
                $itm=$this->getSaleItemReturn($id,$item['id']);
                $item_dataa=array(
                    'reject_id'=>$id,
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
                    'warehouse_id'=>$warehouse_id
                    );
                if($itm)
                {
                    $this->db->update('tblsale_order_items', $item_dataa,array('id'=>$itm->id));
                    if($this->db->affected_rows()>0)
                     {
                        logActivity('Edit  Item Return Updated [ID:' . $id . ', Item ID' . $item['id'] . ']');
                     }
                }
                else
                {
                    $this->db->insert('tblsale_order_items', $item_dataa);
                    if($this->db->affected_rows()>0)
                     {
                        logActivity('Insert Sale Item Return Added [ID:' . $id . ', Item ID' . $item['id'] . ']');
                     }
                }
            }

            if(!empty($affected_idR))
                {
                    $this->db->where('reject_id', $id);
                    $this->db->where_not_in('product_id', $affected_idR);
                    $this->db->delete('tblsale_order_items');
                }
            else       
            {
                $this->db->where('reject_id', $id);
                $this->db->delete('tblsale_order_items');
            }     
                
            return $id;
        }
        return false;
    }

    public function getReturn($order_id)
    {
        $this->db->where('status <>',2);
        $result=$this->db->get_where('tblimports',array('rel_type'=>'return','rel_id'=>$order_id))->row();
        if($result)
        {
            $result->items=$this->db->get_where('tblimport_items',array('import_id'=>$result->id))->result();
            return $result;
        }
        return false;
    }
    //Tao phieu tra hang
    public function createReturnItems($order_id)
    {

        if (is_numeric($order_id)) {
            $info=$this->db->get_where('tblsale_orders',array('id'=>$order_id))->row(); 

            $this->db->where('reject_id', $order_id);
            $items=$this->db->get('tblsale_order_items')->result();

            $returns=$this->getReturn($order_id);
            //Chua duyet Phieu Tra Hang(Sua tren don tra ve)
            if($returns)
            {
                if($items)
                {
                    //Data Return
                    $returnData=array(
                        'customer_id'=>$info->customer_id,
                        'rel_type'=>'return',
                        'rel_id'=>$order_id,
                        'prefix'=>get_option('prefix_return'),
                        'name'=>_l('als_return'),
                        'date'=>to_sql_date(date('Y-m-d H:i:s'),true)
                        );
                    $this->db->update('tblimports', $returnData,array('id'=>$returns->id));
                    if($this->db->affected_rows()) $insert_id = $returns->id;
                    if($insert_id)
                    {
                        logActivity('Eidt Import Updated [ID:' . $insert_id . ', ' . $data['description'] . ']');
                        $total=0;
                        $affected_id=array(); 
                        foreach ($items as $key => $item) {
                            //Data Return Item(Quantity)
                            $affected_id[]=$item->product_id;

                            $isupdate=false;
                            foreach ($returns->items as $key => $val) {
                                $quantity_net=$item->quantity;
                                if($val->product_id==$item->product_id)
                                {
                                    $quantitydiff=$this->getQuantityBeforeDiff($order_id,$item->product_id,$item->quantity);

                                    $quantity_net=$val->quantity+$quantitydiff;
                                    $isupdate=$val->id;
                                    break;
                                }
                            }
                            $product=$this->getProductById($item->product_id);
                            $sub=$item->unit_cost*$quantity_net;
                            $tax=$sub*$item->tax_rate/100;
                            $sub_total=$sub+$tax;
                            $total+=$sub_total;
                            $item_data=array(
                                'import_id'=>$insert_id,
                                'product_id'=>$item->product_id,
                                'specifications'=>$product->description,
                                'unit_id'=>$item->unit_id,
                                'quantity'=>$quantity_net,
                                'quantity_net'=>NULL,
                                'exchange_rate'=>NULL,
                                'unit_cost'=>$item->unit_cost,
                                'sub_total'=>$sub_total,
                                'tax_id'=>$item->tax_id,
                                'tax_rate'=>$item->tax_rate,
                                'tax'=>$tax,
                                'warehouse_id'=>$item->warehouse_id,
                                'warehouse_id_to'=>NULL
                                );

                            if($isupdate)
                            {
                                $this->db->update('tblimport_items', $item_data,array('id'=>$isupdate));
                                 if($this->db->affected_rows()>0)
                                 {
                                    logActivity('Edit Import Item Updated [ID:' . $insert_id . ', Item ID' . $item->product_id . ']');
                                 }
                            }
                            else
                            {
                                $this->db->insert('tblimport_items', $item_data);
                                 if($this->db->affected_rows()>0)
                                 {
                                    logActivity('Insert Import Item Added [ID:' . $insert_id . ', Item ID' . $item->product_id . ']');
                                 }
                            }

                        }
                        //Xoa DL

                        $this->db->where('import_id',$insert_id);
                        $this->db->where_not_in('product_id',$affected_id);
                        $this->db->delete('tblimport_items');
                    }
                }
            }
            //Da duyet Phieu Tra Hang/Them moi
            else
            {
                if($items)
                {
                    //Data Return
                    $returnData=array(
                        'customer_id'=>$info->customer_id,
                        'rel_type'=>'return',
                        'rel_id'=>$order_id,
                        'prefix'=>get_option('prefix_return'),
                        'name'=>_l('als_return'),
                        'code'=>sprintf('%06d',getMaxID('id','tblimports')+1),
                        'reason'=>NULL,
                        'date'=>to_sql_date(date('Y-m-d H:i:s'),true),
                        'account_date'=>NULL,
                        'create_by'=>get_staff_user_id()
                        );
                    $this->db->insert('tblimports', $returnData);
                    $insert_id = $this->db->insert_id();
                    if($insert_id)
                    {
                        logActivity('New Import Added [ID:' . $insert_id . ', ' . $data['description'] . ']');
                        $total=0;
                        foreach ($items as $key => $item) {
                            //Data Return Item
                            $product=$this->getProductById($item->product_id);
                            $quantity_net=$this->getQuantityBeforeDiff($order_id,$item->product_id,$item->quantity);
                            $sub=$item->unit_cost*$quantity_net;
                            $tax=$sub*$item->tax_rate/100;
                            $sub_total=$sub+$tax;
                            $total+=$sub_total;
                            $item_data=array(
                                'import_id'=>$insert_id,
                                'product_id'=>$item->product_id,
                                'specifications'=>$product->description,
                                'unit_id'=>$item->unit_id,
                                'quantity'=>$quantity_net,
                                'quantity_net'=>NULL,
                                'exchange_rate'=>NULL,
                                'unit_cost'=>$item->unit_cost,
                                'sub_total'=>$sub_total,
                                'tax_id'=>$item->tax_id,
                                'tax_rate'=>$item->tax_rate,
                                'tax'=>$tax,
                                'warehouse_id'=>$item->warehouse_id,
                                'warehouse_id_to'=>NULL
                                );

                             $this->db->insert('tblimport_items', $item_data);

                             if($this->db->affected_rows()>0)
                             {
                                logActivity('Insert Import Item Added [ID:' . $insert_id . ', Item ID' . $item->product_id . ']');
                             }
                        }
                    }
                    
                    $this->db->update('tblimports',array('total'=>$total),array('id'=>$insert_id));
                    return true;
                }
            }
        }
        return false;
    }
    public function getQuantityBeforeDiff($rel_id,$product_id,$quantity)
    {
        $this->db->select_sum('quantity');
        $this->db->join('tblimports','tblimports.id=tblimport_items.import_id','left');
        $result=$this->db->get_where('tblimport_items',array('product_id'=>$product_id,'rel_id'=>$rel_id))->row();
        if($result) return $quantity-$result->quantity;
        return $quantity;
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
        $this->db->update('tblsale_orders',$data,array('id'=>$id));
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
            return $this->db->get('tblsale_order_items')->row();
        }
        return false;
    }

    public function getSaleItemReturn($sale_id,$product_id)
    {
        if (is_numeric($sale_id) && is_numeric($product_id)) {
            $this->db->where('reject_id', $sale_id);
            $this->db->where('product_id', $product_id);
            return $this->db->get('tblsale_order_items')->row();
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
        $rel_contract=$this->db->get_where('tblsale_orders',array('id'=>$id))->row();
        if(deleteSalePSOToWWH($id,'PO') && $this->db->delete('tblsale_orders',array('id'=>$id)) && $this->db->delete('tblsale_order_items',array('sale_id'=>$id)));
        if ($this->db->affected_rows() > 0) {
            deleteSaleProductDetails($id);
            $this->db->update('tblcontracts',array('export_status'=>0),array('id'=>$rel_contract->rel_id));
            $this->db->delete('tblsale_order_items',array('reject_id'=>$id));
            return true;
        }
        return false;
    }
}
