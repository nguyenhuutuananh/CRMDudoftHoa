<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Quotes extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('quotes_model');
    }
    public function index() 
    {
        // echo "test";
    }

    public function imp_adjustment() 
    {
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('adjustments',array('rel_type'=>'adjustment'));
        }
        $data['title'] = _l('Điều chỉnh kho hàng');
        $this->load->view('admin/imports/adjustments/adjustment', $data);
    }

    public function adjustment_detail($id='') 
    {
        if (!has_permission('import_items', '', 'view')) {
            if ($id != '' && !is_customer_admin($id)) {
                access_denied('import_items');
            }
        }
        if ($this->input->post() && !$this->input->is_ajax_request()) {
            if ($id == '') {
                if (!has_permission('import_items', '', 'create')) {
                    access_denied('import_items');
                }

                $data                 = $this->input->post();
                // var_dump($data);die();
                if(isset($data['items']) && count($data['items']) > 0)
                {
                    $id = $this->imports_model->add($data);
                }
                
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('adjustments')));
                    redirect(admin_url('imports/imp_adjustment'));
                }
            } else {

                if (!has_permission('import_items', '', 'edit')) {
                        access_denied('import_items');
                }
                $success = $this->imports_model->update($this->input->post(), $id);
                if ($success == true) {
                    set_alert('success', _l('updated_successfuly', _l('adjustments')));
                    redirect(admin_url('imports/imp_adjustment'));
                }
                else
                {
                    redirect(admin_url('imports/adjustment_detail/'.$id));
                }
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('adjustments'));

        } else {
            $data['item'] = $this->imports_model->getImportByID($id);
            $data['warehouse_id']=$data['item']->items[0]->warehouse_id;
            $data['warehouse_type']=$this->warehouse_model->getWarehouses($data['warehouse_id'])->kindof_warehouse;
            if (!$data['item']) {
                blank_page('Purchase Not Found');
            }
        }
        $data['items']= $this->invoice_items_model->get_full();
        
        $data['warehouse_types']= $this->imports_model->getWarehouseTypes();
        $data['warehouses']= $this->warehouse_model->getWarehouses();
        $data['title'] = $title;
        $this->load->view('admin/imports/adjustments/detail', $data);
    }



    /* Get task data in a right pane */
    public function delete_import($id)
    {
        if (!$id) {
            die('Không tìm thấy mục nào');
        }

        $success    = $this->imports_model->delete_warehouses_adjustment($id);
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

    
    public function pdf($id)
    {
        if (!has_permission('import_items', '', 'view') && !has_permission('import_items', '', 'view_own')) {
            access_denied('import_items');
        }
        if (!$id) {
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $invoice        = $this->imports_model->getImportByID($id);
        $invoice_number = $invoice->prefix.$invoice->code;

        $pdf            = import_detail_pdf($invoice);
        $type           = 'D';
        if ($this->input->get('pdf') || $this->input->get('print')) {
            $type = 'I';
        }
        $pdf->Output(mb_strtoupper(slug_it($invoice_number)) . '.pdf', $type);
    }
    
}