<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Purchases extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('purchases_model');
    }
    /* Get all invoices in case user go on index page */
    public function index($id = false)
    {
        $this->list_invoices($id);
    }
    /* List all invoices datatables */
    public function list_invoices($id = false, $clientid = false)
    {

        if (!has_permission('invoices', '', 'view') && !has_permission('invoices', '', 'view_own')) {
            access_denied('invoices');
        }
        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', array(), true);
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('purchases');
        }
        $data['invoiceid'] = '';
        if (is_numeric($id)) {
            $data['invoiceid'] = $id;
        }
        $data['title']                = _l('Kế hoạch mua hàng');
        $data['invoices_years']       = $this->purchases_model->get_invoices_years();
        $data['invoices_sale_agents'] = $this->purchases_model->get_sale_agents();
        $data['invoices_statuses']    = $this->purchases_model->get_statuses();
        // var_dump($data['invoices_sale_agents']);die();
        $data['bodyclass']            = 'invoices_total_manual';
        $this->load->view('admin/purchases/manage', $data);
    }

    /* Edit client or add new client*/
    public function purchase($id = '')
    {
        // var_dump('AA'.sprintf('%05d',getMaxID('id','tblpurchase_plan')));die();
        if (!has_permission('customers', '', 'view')) {
            if ($id != '' && !is_customer_admin($id)) {
                access_denied('customers');
            }
        }
        if ($this->input->post() && !$this->input->is_ajax_request()) {
            if ($id == '') {
                if (!has_permission('customers', '', 'create')) {
                    access_denied('customers');
                }
                $data                 = $this->input->post();
                $id = $this->purchases_model->add($data);
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('Kế hoạch')));
                    redirect(admin_url('purchases/purchase/' . $id));
                }
            } else {
                if (!has_permission('customers', '', 'edit')) {
                    if (!is_customer_admin($id)) {
                        access_denied('customers');
                    }
                }
                $success = $this->purchases_model->update($this->input->post(), $id);
                if ($success == true) {
                    set_alert('success', _l('updated_successfuly', _l('Kế hoạch')));
                }
                redirect(admin_url('purchases/purchase/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('kế hoạch mua'));

        } else {
            $data['purchase'] = $this->purchases_model->getPurchaseByID($id);
            // var_dump($data['purchase']);die();
            if (!$data['purchase']) {
                blank_page('Purchase Not Found');
            }
        }

        $data['bodyclass'] = 'customer-profile';
        $this->load->model('taxes_model');
        $data['taxes']        = $this->taxes_model->get();
        $this->load->model('invoice_items_model');
        $data['items_groups'] = $this->invoice_items_model->get_groups();
        $data['items_units'] = $this->invoice_items_model->get_units();
        // $data['items']        = $this->invoice_items_model->get_grouped();
        $data['items']        = $this->invoice_items_model->get_full();


        $data['title'] = $title;
        $this->load->view('admin/purchases/purchase', $data);
    }
    
}