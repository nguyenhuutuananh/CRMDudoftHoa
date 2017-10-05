<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sale_orders extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('sale_oders_model');
        $this->load->model('invoice_items_model');
        $this->load->model('clients_model');
        $this->load->model('warehouse_model');
        $this->load->model('receipts_model');
    }
    

    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('sales');
        }
        $data['title'] = _l('sale_orders');
        $this->load->view('admin/sales/manage', $data);
    }

    public function list_sale_orders()
    {
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('sale_orders');
        }
    }


    public function sale_detail($id='') 
    {
          
        if (!has_permission('sale_items', '', 'view')) {
            if ($id != '' && !is_customer_admin($id)) {
                access_denied('sale_items');
            }
        }
        if ($this->input->post() && !$this->input->is_ajax_request()) {
            if ($id == '') {
                if (!has_permission('sale_items', '', 'create')) {
                    access_denied('sale_items');
                }

                $data                 = $this->input->post();
                if(isset($data['items']) && count($data['items']) > 0)
                {
                    $id = $this->sale_oders_model->add($data);
                }
                
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('sale')));
                    redirect(admin_url('sales'));
                }
            } else {

                if (!has_permission('sale_items', '', 'edit')) {
                        access_denied('sale_items');
                }
                $success = $this->sale_oders_model->update($this->input->post(), $id);
                if ($success == true) {
                    set_alert('success', _l('updated_successfuly', _l('sale')));
                    redirect(admin_url('sales'));
                }
                else
                {
                    redirect(admin_url('sales/sale_detail/'.$id));
                }
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('sales_po'));
      
        } 
        else 
        {
            $data['item'] = $this->sale_oders_model->getSaleByID($id);
            if($data['item']->rel_id)
            {
                $data['khoa']=true;
            }
            
            $data['item_returns'] = $this->sale_oders_model->getReturnSaleItems($id);
            $i=0;
            foreach ($data['item']->items as $key => $value) {       
                $data['item']->items[$i]->warehouse_type=$this->warehouse_model->getWarehouseProduct($value->warehouse_id,$value->product_id);
                $i++;
            }
            $data['isedit']='1';
            $data['warehouse_id'] = $data['item']->items[0]->warehouse_id;
            $data['warehouse_type_id']=$data['item']->items[0]->warehouse_type->kindof_warehouse;
           
            if(isset($data['item']->rel_id))
            {
                $data['isedit']=false;
            }
            if (!$data['item']) {
                blank_page('Sale Not Found');
            }
        }  

        $where_clients = 'tblclients.active=1';

        if (!has_permission('customers', '', 'view')) {
            $where_clients .= ' AND tblclients.userid IN (SELECT customer_id FROM tblcustomeradmins WHERE staff_id=' . get_staff_user_id() . ')';
        }

        $data['tk_no']=$this->receipts_model->get_table_where('tblaccounts','idAccountAttribute=1 or idAccountAttribute=3');
        $data['tk_co']=$this->receipts_model->get_table_where('tblaccounts','idAccountAttribute=2 or idAccountAttribute=3');

        $data['warehouse_types']= $this->warehouse_model->getWarehouseTypes();
        $data['items']= $this->invoice_items_model->get_full('',$data['warehouse_id']);
        $data['warehouses']= $this->warehouse_model->getWarehouses();
        $data['customers']= $this->clients_model->get('',$where_clients);
        $data['title'] = $title;
        $this->load->view('admin/sales/order_detail', $data);
    }

    public function sale_output($id)
    {

         if (!has_permission('sale_items', '', 'view')) {
            if ($id != '' && !is_customer_admin($id)) {
                access_denied('sale_items');
            }
        }


        {            
            $data['item'] = $this->sale_oders_model->getSaleByID($id);
            $i=0;
            foreach ($data['item']->items as $key => $value) {    
                $warehouse=(is_array($this->warehouse_model->getWarehouseProduct($value->warehouse_id,$value->product_id))&& count($this->warehouse_model->getWarehouseProduct($value->warehouse_id,$value->product_id))==1)? ($this->warehouse_model->getWarehouseProduct($value->warehouse_id,$value->product_id)[0]) : ($this->warehouse_model->getWarehouseProduct($value->warehouse_id,$value->product_id));
                $data['item']->items[$i]->warehouse_type=$warehouse;

                $i++;
            }
            if (!$data['item']) {
                blank_page('Export Not Found');
            }   
        }

        $data['warehouse_id'] = $data['item']->items[0]->warehouse_id;
        $data['warehouse_type_id']=$data['item']->items[0]->warehouse_type->kindof_warehouse;
        // var_dump($data['item']->items[0]);die;
        $where_clients = 'tblclients.active=1';

        if (!has_permission('customers', '', 'view')) {
            $where_clients .= ' AND tblclients.userid IN (SELECT customer_id FROM tblcustomeradmins WHERE staff_id=' . get_staff_user_id() . ')';
        }

        

        $data['warehouse_types']= $this->warehouse_model->getWarehouseTypes();
        $data['warehouses']= $this->warehouse_model->getWarehouses();
        $data['receivers'] = $this->staff_model->get('','',array('staffid<>'=>1));
        
        $data['customers'] = $this->clients_model->get('', $where_clients);
        $data['items']= $this->invoice_items_model->get_full();
        $data['tk_no']=$this->receipts_model->get_table_where('tblaccounts','idAccountAttribute=1 or idAccountAttribute=3');
        $data['tk_co']=$this->receipts_model->get_table_where('tblaccounts','idAccountAttribute=2 or idAccountAttribute=3');

        $data['title'] = _l('Tạo phiếu bán hàng(SO)');        
        $this->load->view('admin/sales/order_export_detail', $data);
    }



    /* Get task data in a right pane */
    public function delete($id)
    {
        if (!$id) {
            die('Không tìm thấy mục nào');
        }

        $success    = $this->sale_oders_model->delete($id);
        $alert_type = 'warning';
        $message    = _l('Không thể xóa dữ liệu');
        if ($success) {
            $alert_type = 'success';
            $message    = _l('Xóa dữ liệu thành công');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));

    }

    public function update_status()
    {
        
        $id=$this->input->post('id');
        $status=$this->input->post('status');
        $staff_id=get_staff_user_id();
        $date=date('Y-m-d H:i:s');
        $data=array('status'=>$status);

        $inv=$this->sale_oders_model->getSaleByID($id);
        if(is_admin() && $status==0)
        {
            $data['user_head_id']=$staff_id;
            $data['user_head_date']=$date;

            $data['user_admin_id']=$staff_id;
            $data['user_admin_date']=$date;

            $data['status']=2;
        }
        elseif(is_admin() && $status==1)
        {
            $data['status']=2;
            if($inv->user_head_id==NULL || $inv->user_head_id=='')
            {
                $data['user_head_id']=$staff_id;
                $data['user_head_date']=$date;
            }
            if($inv->user_admin_id==NULL || $inv->user_admin_id=='')
            {
                $data['user_admin_id']=$staff_id;
                $data['user_admin_date']=$date;
            }
        }
        elseif(is_head($inv->create_by))
        {
            $data['status']+=1;
            $data['user_head_id']=$staff_id;
            $data['user_head_date']=$date;
        }

        $success=fale;
        
        if(is_admin() || is_head($inv->create_by))
        {
            $success=$this->sale_oders_model->update_status($id,$data);
        }
        if($success) {
            echo json_encode(array(
                'success' => $success,
                'message' => _l('Xác nhận phiếu thành công')
            ));
        }
        else
        {
            echo json_encode(array(
                'success' => $success,
                'message' => _l('Không thể cập nhật dữ liệu')
            ));
        }
        die;
    }

    public function test($order_id)
    {
        $this->sale_oders_model->createReturnItems($order_id);
    }

    
    public function pdf($id)
    {
        if (!has_permission('sale_items', '', 'view') && !has_permission('sale_items', '', 'view_own')) {
            access_denied('sale_items');
        }
        if (!$id) {
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $invoice        = $this->sale_oders_model->getSaleByID($id);
        $invoice_number = $invoice->prefix.$invoice->code;

        $pdf            = sale_order_detail_pdf($invoice);
        $type           = 'D';
        if ($this->input->get('pdf') || $this->input->get('print')) {
            $type = 'I';
        }
        $pdf->Output(mb_strtoupper(slug_it($invoice_number)) . '.pdf', $type);
    }
    
}