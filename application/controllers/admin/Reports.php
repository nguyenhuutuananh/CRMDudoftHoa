<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Reports extends Admin_controller
{
    private $_instance;
    function __construct()
    {
        parent::__construct();
        if (!has_permission('reports', '', 'view')) {
            access_denied('reports');
        }
        $this->_instance =& get_instance();
        $this->load->model('reports_model');
    }
    /* No access on this url */
    public function index()
    {
        redirect(site_url('admin'));
    }
    /* See knowledge base article reports*/
    public function knowledge_base_articles()
    {
        $this->load->model('knowledge_base_model');
        $data['groups'] = $this->knowledge_base_model->get_kbg();
        $data['title']  = _l('kb_reports');
        $this->load->view('admin/reports/knowledge_base_articles', $data);
    }
/*
    public function tax_summary(){
       $this->load->model('taxes_model');
       $this->load->model('payments_model');
       $this->load->model('invoices_model');
       $data['taxes'] = $this->db->query("SELECT DISTINCT taxname,taxrate FROM tblitemstax WHERE rel_type='invoice'")->result_array();
        $this->load->view('admin/reports/tax_summary',$data);
    }*/
    /* Rerport leads conversions */
    public function leads()
    {
        $type = 'leads';
        if ($this->input->get('type')) {
            $type                       = $type . '_' . $this->input->get('type');
            $data['leads_staff_report'] = json_encode($this->reports_model->leads_staff_report());
        }
        $this->load->model('leads_model');
        $data['statuses']               = $this->leads_model->get_status();
        $data['leads_this_week_report'] = json_encode($this->reports_model->leads_this_week_report());
        $data['leads_sources_report']   = json_encode($this->reports_model->leads_sources_report());
        $data['chart_js_assets']   = true;
        $this->load->view('admin/reports/' . $type, $data);
    }
    /* Sales reportts */
    public function sales()
    {
        if (is_using_multiple_currencies()) {
            $this->load->model('currencies_model');
            $data['currencies'] = $this->currencies_model->get();
        }
        $this->load->model('sales_model');
        $this->load->model('sale_oders_model');
        $this->load->model('invoices_model');
        $this->load->model('estimates_model');
        $this->load->model('proposals_model');
        $data['sale_statuses']      = $this->sales_model->get_statuses();
        $data['invoice_statuses']      = $this->invoices_model->get_statuses();
        $data['estimate_statuses']     = $this->estimates_model->get_statuses();
        $data['payments_years']        = $this->reports_model->get_distinct_payments_years();
        $data['estimates_sale_agents'] = $this->estimates_model->get_sale_agents();

        $data['invoices_sale_agents']  = $this->invoices_model->get_sale_agents();


        $data['proposals_sale_agents']  = $this->proposals_model->get_sale_agents();
        $data['proposals_statuses'] = $this->proposals_model->get_statuses();
        $data['order_years'] = $this->sale_oders_model->getYears();
        $data['MONTHS']=[
                    "Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"
                ];
        $data['chart_js_assets']   = true;
        $data['title']                 = _l('sales_reports');
        $this->load->view('admin/reports/sales', $data);
    }

    /* Buys reportts */
    public function buys()
    {
        var_dump(expression);
    }

    /* Customer report */
    public function customers_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $select = array(
                'CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END as company',
                '(SELECT COUNT(clientid) FROM tblinvoices WHERE tblinvoices.clientid = tblclients.userid AND status != 5)',
                '(SELECT SUM(subtotal) FROM tblinvoices WHERE tblinvoices.clientid = tblclients.userid AND status != 5)',
                '(SELECT SUM(total) FROM tblinvoices WHERE tblinvoices.clientid = tblclients.userid AND status != 5)'
            );

            $custom_date_select = $this->get_where_report_period();
            if ($custom_date_select != '') {
                $i = 0;
                foreach ($select as $_select) {
                    if ($i !== 0) {
                        $_temp = substr($_select, 0, -1);
                        $_temp .= ' ' . $custom_date_select . ')';
                        $select[$i] = $_temp;
                    }
                    $i++;
                }
            }

            $by_currency     = $this->input->post('report_currency');
            $currency        = $this->currencies_model->get_base_currency();
            $currency_symbol = $this->currencies_model->get_currency_symbol($currency->id);
            if ($by_currency) {
                $i = 0;
                foreach ($select as $_select) {
                    if ($i !== 0) {
                        $_temp = substr($_select, 0, -1);
                        $_temp .= ' AND currency =' . $by_currency . ')';
                        $select[$i] = $_temp;
                    }
                    $i++;
                }
                $currency        = $this->currencies_model->get($by_currency);
                $currency_symbol = $this->currencies_model->get_currency_symbol($currency->id);
            }
            $aColumns     = $select;
            $sIndexColumn = "userid";
            $sTable       = 'tblclients';
            $where        = array();

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, array(), $where, array(
                'userid'
            ));
            $output  = $result['output'];
            $rResult = $result['rResult'];
            $x       = 0;
            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }
                    if ($i == 0) {
                        $_data = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '" target="_blank">' . $aRow['company'] . '</a>';
                    } else if ($aColumns[$i] == $select[2] || $aColumns[$i] == $select[3]) {
                        if ($_data == null) {
                            $_data = 0;
                        }
                        $_data = format_money($_data, $currency_symbol);
                    }
                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
                $x++;
            }
            echo json_encode($output);
            die();
        }
    }

    public function payments_received()
    {

        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $this->load->model('payment_modes_model');
            $online_modes = $this->payment_modes_model->get_online_payment_modes(true);
            $select       = array(
                'tblinvoicepaymentrecords.id',
                'tblinvoicepaymentrecords.date',
                'invoiceid',
                'CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END as company',
                'paymentmode',
                'transactionid',
                'note',
                'amount'
            );
            $where        = array(
                'AND status != 5'
            );

            $custom_date_select = $this->get_where_report_period('tblinvoicepaymentrecords.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {
                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }

            $currency_symbol = $this->currencies_model->get_currency_symbol($currency->id);

            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblinvoicepaymentrecords';
            $join         = array(
                'JOIN tblinvoices ON tblinvoices.id = tblinvoicepaymentrecords.invoiceid',
                'LEFT JOIN tblclients ON tblclients.userid = tblinvoices.clientid',
                'LEFT JOIN tblinvoicepaymentsmodes ON tblinvoicepaymentsmodes.id = tblinvoicepaymentrecords.paymentmode'
            );

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'number',
                'clientid',
                'tblinvoicepaymentsmodes.name',
                'tblinvoicepaymentsmodes.id as paymentmodeid',
                'paymentmethod'
            ));

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data['total_amount'] = 0;
            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }
                    if ($aColumns[$i] == 'paymentmode') {
                        $_data = $aRow['name'];
                        if (is_null($aRow['paymentmodeid'])) {
                            foreach ($online_modes as $online_mode) {
                                if ($aRow['paymentmode'] == $online_mode['id']) {
                                    $_data = $online_mode['name'];
                                }
                            }
                        }
                        if (!empty($aRow['paymentmethod'])) {
                            $_data .= ' - ' . $aRow['paymentmethod'];
                        }
                    } else if ($aColumns[$i] == 'tblinvoicepaymentrecords.id') {
                        $_data = '<a href="' . admin_url('payments/payment/' . $_data) . '" target="_blank">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblinvoicepaymentrecords.date') {
                        $_data = _d($_data);
                    } else if ($aColumns[$i] == 'invoiceid') {
                        $_data = '<a href="' . admin_url('invoices/list_invoices/' . $aRow[$aColumns[$i]]) . '" target="_blank">' . format_invoice_number($aRow['invoiceid']) . '</a>';
                    } else if ($i == 3) {
                        $_data = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '" target="_blank">' . $aRow['company'] . '</a>';
                    } else if ($aColumns[$i] == 'amount') {
                        $footer_data['total_amount'] += $_data;
                        $_data = format_money($_data, $currency_symbol);
                    }

                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
            }

            $footer_data['total_amount'] = format_money($footer_data['total_amount'], $currency_symbol);
            $output['sums']              = $footer_data;
            echo json_encode($output);
            die();
        }
    }
    public function proposals_report(){
        if($this->input->is_ajax_request()){

            $this->load->model('currencies_model');
            $this->load->model('proposals_model');

            $select = array(
                'id',
                'subject',
                'proposal_to',
                'date',
                'open_till',
                'subtotal',
                'total',
                'total_tax',
                '1',
                'discount_total',
                'adjustment',
                'status'
            );

            $where              = array();
            $custom_date_select = $this->get_where_report_period();
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            if ($this->input->post('proposal_status')) {
                $statuses  = $this->input->post('proposal_status');
                $_statuses = array();
                if (is_array($statuses)) {
                    foreach ($statuses as $status) {
                        if ($status != '') {
                            array_push($_statuses, $status);
                        }
                    }
                }
                if (count($_statuses) > 0) {
                    array_push($where, 'AND status IN (' . implode(', ', $_statuses) . ')');
                }
            }

            if ($this->input->post('proposals_sale_agents')) {
                $agents  = $this->input->post('proposals_sale_agents');
                $_agents = array();
                if (is_array($agents)) {
                    foreach ($agents as $agent) {
                        if ($agent != '') {
                            array_push($_agents, $agent);
                        }
                    }
                }
                if (count($_agents) > 0) {
                    array_push($where, 'AND assigned IN (' . implode(', ', $_agents) . ')');
                }
            }


            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {
                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }

            $currency_symbol = $this->currencies_model->get_currency_symbol($currency->id);

            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblproposals';
            $join         = array();

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'rel_id',
                'rel_type',
                'discount_percent'
            ));

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $x       = 0;
            $footer_data = array(
                'total' => 0,
                'subtotal' => 0,
                'total_tax' => 0,
                'discount_total' => 0,
                'adjustment' => 0
            );

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }
                    if ($i == 'id' || $aColumns[$i] == 'subject') {
                         $_data = '<a href="'.admin_url('proposals/list_proposals/'.$aRow['id']).'" target="_blank">' .
                         ($aColumns[$i] == 'id' ? format_proposal_number($aRow['id']) : $_data) . '</a>';
                    } else if ($aColumns[$i] == 'total' || $aColumns[$i] == 'subtotal' || $aColumns[$i] == 'total_tax' || $aColumns[$i] == 'discount_total' || $aColumns[$i] == 'adjustment') {
                        if ($_data == null) {
                            $_data = 0;
                        }
                        $footer_data[$aColumns[$i]] += $_data;
                        $_data = format_money($_data, $currency_symbol);
                    } else if ($aColumns[$i] == '1') {
                        $_data = $this->get_report_tax_breakdown_column('proposals', $aRow['id'], $_data, $currency_symbol);
                    } else if ($aColumns[$i] == 'status') {
                        $_data = format_proposal_status($aRow['status']);
                    } else if ($aColumns[$i] == 'date' || $aColumns[$i] == 'open_till') {
                        $_data = _d($_data);
                    } else if($aColumns[$i] == 'proposal_to'){
                           if(!empty($_data)){
                              if(!empty($aRow['rel_id']) && $aRow['rel_id'] != 0){
                                if($aRow['rel_type'] == 'lead'){
                                  $_data = '<a href="#" onclick="init_lead('.$aRow['rel_id'].');return false;" target="_blank" data-toggle="tooltip" data-title="'._l('lead').'">'.$_data.'</a>'. '<span class="hide">'._l('lead').'</span>';
                              } else if($aRow['rel_type'] == 'customer'){
                                  $_data = '<a href="'.admin_url('clients/client/'.$aRow['rel_id']).'" target="_blank" data-toggle="tooltip" data-title="'._l('client').'">'.$_data.'</a>' . '<span class="hide">'._l('client').'</span>';
                              }
                          }
                      }
                    }
                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
                $x++;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = format_money($total, $currency_symbol);
            }

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }
    public function estimates_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $this->load->model('estimates_model');

            $select = array(
                'id',
                'CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END as company',
                'invoiceid',
                'YEAR(date)',
                'date',
                'expirydate',
                'subtotal',
                'total',
                'total_tax',
                '1',
                'discount_total',
                'adjustment',
                'reference_no',
                'status'
            );

            $where              = array();
            $custom_date_select = $this->get_where_report_period();
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            if ($this->input->post('estimate_status')) {
                $statuses  = $this->input->post('estimate_status');
                $_statuses = array();
                if (is_array($statuses)) {
                    foreach ($statuses as $status) {
                        if ($status != '') {
                            array_push($_statuses, $status);
                        }
                    }
                }
                if (count($_statuses) > 0) {
                    array_push($where, 'AND status IN (' . implode(', ', $_statuses) . ')');
                }
            }

            if ($this->input->post('sale_agent_estimates')) {
                $agents  = $this->input->post('sale_agent_estimates');
                $_agents = array();
                if (is_array($agents)) {
                    foreach ($agents as $agent) {
                        if ($agent != '') {
                            array_push($_agents, $agent);
                        }
                    }
                }
                if (count($_agents) > 0) {
                    array_push($where, 'AND sale_agent IN (' . implode(', ', $_agents) . ')');
                }
            }

            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {
                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }
            $currency_symbol = $this->currencies_model->get_currency_symbol($currency->id);

            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblestimates';
            $join         = array(
                'JOIN tblclients ON tblclients.userid = tblestimates.clientid'
            );

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'userid',
                'clientid',
                'discount_percent'
            ));

            $output  = $result['output'];
            $rResult = $result['rResult'];
            $x       = 0;

            $footer_data = array(
                'total' => 0,
                'subtotal' => 0,
                'total_tax' => 0,
                'discount_total' => 0,
                'adjustment' => 0
            );

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }
                    if ($i == 1) {
                        $_data = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '" target="_blank">' . $aRow['company'] . '</a>';
                    } else if ($aColumns[$i] == 'total' || $aColumns[$i] == 'subtotal' || $aColumns[$i] == 'total_tax' || $aColumns[$i] == 'discount_total' || $aColumns[$i] == 'adjustment') {
                        if ($_data == null) {
                            $_data = 0;
                        }
                        $footer_data[$aColumns[$i]] += $_data;
                        $_data = format_money($_data, $currency_symbol);

                    } else if ($aColumns[$i] == '1') {
                        $_data = $this->get_report_tax_breakdown_column('estimates', $aRow['id'], $_data, $currency_symbol);
                    } else if ($aColumns[$i] == 'id') {
                        $_data = '<a href="' . admin_url('estimates/list_estimates/' . $aRow['id']) . '" target="_blank">' . format_estimate_number($aRow['id']) . '</a>';
                    } else if ($aColumns[$i] == 'status') {
                        $_data = format_estimate_status($aRow['status']);
                    } else if ($aColumns[$i] == 'date' || $aColumns[$i] == 'expirydate') {
                        $_data = _d($_data);
                    } else if ($aColumns[$i] == 'invoiceid') {
                        if ($_data == NULL) {
                            $_data = '';
                        } else {
                            $_data = '<a href="' . admin_url('invoices/list_invoices/' . $_data) . '" target="_blank">' . format_invoice_number($_data) . '</a>';
                        }
                    }
                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
                $x++;
            }
            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = format_money($total, $currency_symbol);
            }
            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();

        }
    }
    private function get_where_report_period($field = 'date')
    {
        $months_report      = $this->input->post('report_months');
        $custom_date_select = '';
        if ($months_report != '') {
            if (is_numeric($months_report)) {
                $minus_months       = date('Y-m-d', strtotime("-$months_report MONTH"));
                $custom_date_select = 'AND (' . $field . ' BETWEEN "' . $minus_months . '" AND "' . date('Y-m-d') . '")';
            } else if ($months_report == 'custom') {
                $from_date = to_sql_date($this->input->post('report_from'));
                $to_date   = to_sql_date($this->input->post('report_to'));
                if ($from_date == $to_date) {
                    $custom_date_select = 'AND ' . $field . ' = "' . $from_date . '"';
                } else {
                    $custom_date_select = 'AND (' . $field . ' BETWEEN "' . $from_date . '" AND "' . $to_date . '")';
                }
            }
        }
        return $custom_date_select;
    }
    private function get_report_tax_breakdown_column($type, $id, $_data, $currency_symbol)
    {
        if ($type == 'estimates') {
            $table = 'tblestimates';
            $items = $this->estimates_model->get_estimate_items($id);
        } else if($type == 'proposals') {
            $items = $this->proposals_model->get_proposal_items($id);
            $table = 'tblproposals';
        } else {
            $items = $this->invoices_model->get_invoice_items($id);
            $table = 'tblinvoices';
        }

        $taxes             = array();
        $_calculated_taxes = array();
        $multiple_taxes    = false;
        $one_tax           = false;
        foreach ($items as $item) {
            if ($type == 'estimates') {
                 $item_taxes = get_estimate_item_taxes($item['id']);
            } else if($type == 'proposals') {
                 $item_taxes = get_proposal_item_taxes($item['id']);
            } else {
                 $item_taxes = get_invoice_item_taxes($item['id']);
            }

            if (count($item_taxes) > 0) {
                foreach ($item_taxes as $tax) {
                    $calc_tax     = 0;
                    $tax_not_calc = false;
                    if (!in_array($tax['taxname'], $_calculated_taxes)) {
                        array_push($_calculated_taxes, $tax['taxname']);
                        $tax_not_calc = true;
                    }
                    if ($tax_not_calc == true) {
                        $taxes[$tax['taxname']]          = array();
                        $taxes[$tax['taxname']]['total'] = array();
                        array_push($taxes[$tax['taxname']]['total'], (($item['qty'] * $item['rate']) / 100 * $tax['taxrate']));
                        $taxes[$tax['taxname']]['tax_name'] = $tax['taxname'];
                        $taxes[$tax['taxname']]['taxrate']  = $tax['taxrate'];
                    } else {
                        array_push($taxes[$tax['taxname']]['total'], (($item['qty'] * $item['rate']) / 100 * $tax['taxrate']));
                    }
                }
            }
        }
        $_tax = '';
        $this->db->select('discount_type,discount_percent');
        $this->db->where('id', $id);
        $necessary_data = $this->db->get($table)->row();
        $count          = count($taxes);
        if ($count >= 1) {
            if ($count == 1) {
                $one_tax = true;
            }
            foreach ($taxes as $tax) {
                $total = array_sum($tax['total']);
                if ($necessary_data->discount_percent != 0 && $necessary_data->discount_type == 'before_tax') {
                    $total_tax_calculated = ($total * $necessary_data->discount_percent) / 100;
                    $total                = ($total - $total_tax_calculated);
                }
                $_tax_name = explode('|', $tax['tax_name']);
                $_tax .= '<b>' . $_tax_name[0] . '(' . _format_number($tax['taxrate']) . '%)</b> - ' . format_money($total, $currency_symbol) . ' | ';
            }
            $_tax = mb_substr($_tax, 0, -2);
        } else if ($count == 0) {
            $_data = 0;
            $_tax  = format_money($_data, $currency_symbol);
        }

        if ($one_tax == true) {
            if (strrpos($_tax, "|") !== false)
                $_tax = mb_substr($_tax, 0, -2);
        }

        return $_tax;
    }
    public function invoices_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $this->load->model('invoices_model');

            $select = array(
                'id',
                'CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END as company',
                'YEAR(date)',
                'date',
                'duedate',
                'subtotal',
                'total',
                'total_tax',
                '1',
                'discount_total',
                'adjustment',
                '(SELECT SUM(amount) FROM tblinvoicepaymentrecords WHERE invoiceid = tblinvoices.id)',
                'status'
            );

            $where  = array(
                'AND status != 5'
            );

            $custom_date_select = $this->get_where_report_period();
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            if ($this->input->post('sale_agent_invoices')) {
                $agents  = $this->input->post('sale_agent_invoices');
                $_agents = array();
                if (is_array($agents)) {
                    foreach ($agents as $agent) {
                        if ($agent != '') {
                            array_push($_agents, $agent);
                        }
                    }
                }
                if (count($_agents) > 0) {
                    array_push($where, 'AND sale_agent IN (' . implode(', ', $_agents) . ')');
                }
            }

            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {

                $_temp = substr($select[11], 0, -1);
                $_temp .= ' AND currency =' . $by_currency . ')';
                $select[11] = $_temp;

                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }
            $currency_symbol = $this->currencies_model->get_currency_symbol($currency->id);

            if ($this->input->post('invoice_status')) {
                $statuses  = $this->input->post('invoice_status');
                $_statuses = array();
                if (is_array($statuses)) {
                    foreach ($statuses as $status) {
                        if ($status != '') {
                            array_push($_statuses, $status);
                        }
                    }
                }
                if (count($_statuses) > 0) {
                    array_push($where, 'AND status IN (' . implode(', ', $_statuses) . ')');
                }
            }

            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblinvoices';
            $join         = array(
                'JOIN tblclients ON tblclients.userid = tblinvoices.clientid'
            );

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'userid',
                'clientid',
                'discount_percent'
            ));
            $output  = $result['output'];
            $rResult = $result['rResult'];
            $x       = 0;

            $footer_data = array(
                'total' => 0,
                'subtotal' => 0,
                'total_tax' => 0,
                'discount_total' => 0,
                'adjustment' => 0,
                'amount_open' => 0
            );
            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }
                    if ($i == 1) {
                        $_data = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '" target="_blank">' . $aRow['company'] . '</a>';
                    } else if ($aColumns[$i] == 'total' || $aColumns[$i] == 'subtotal' || $aColumns[$i] == 'total_tax' || $aColumns[$i] == 'discount_total' || $aColumns[$i] == 'adjustment') {
                        if ($_data == null) {
                            $_data = 0;
                        }
                        $footer_data[$aColumns[$i]] += $_data;
                        $_data = format_money($_data, $currency_symbol);

                    } else if ($aColumns[$i] == '1') {
                        $_data = $this->get_report_tax_breakdown_column('invoices', $aRow['id'], $_data, $currency_symbol);
                    } else if ($aColumns[$i] == $select[11]) {
                        $_amount_open = $aRow['total'] - $_data;
                        $footer_data['amount_open'] += $_amount_open;
                        $_data = format_money($_amount_open, $currency_symbol);
                    } else if ($aColumns[$i] == 'id') {
                        $_data = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['id']) . '" target="_blank">' . format_invoice_number($aRow['id']) . '</a>';
                    } else if ($aColumns[$i] == 'status') {
                        $_data = format_invoice_status($aRow['status']);
                    } else if ($aColumns[$i] == 'date' || $aColumns[$i] == 'duedate') {
                        $_data = _d($_data);
                    }
                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
                $x++;
            }
            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = format_money($total, $currency_symbol);
            }


            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    public function order_tracking_book_report_PO()
    {
        if ($this->input->is_ajax_request()) 
        {
            $this->load->model('currencies_model');
            $this->load->model('invoices_model');
            $this->load->model('sales_model');

            $select = array(
                'tblsale_orders.date',
                'tblsale_orders.date_ht',                
                'CONCAT(tblsale_orders.prefix,tblsale_orders.code) as sale_code',
                'tblsale_orders.reason',
                'tblitems.code',
                'tblitems.name',
                'tblunits.unit',
                'tblsale_order_items.quantity',
                'tblsale_order_items.unit_cost',
                'tblsale_order_items.amount'
            );

            
            $where  = array(
                // 'AND status != 5'
            );

            $custom_date_select = $this->get_where_report_period('tblsale_orders.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {

                $_temp = substr($select[11], 0, -1);
                $_temp .= ' AND currency =' . $by_currency . ')';
                $select[11] = $_temp;

                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }
            $currency_symbol = $this->currencies_model->get_currency_symbol($currency->id);

            // if ($this->input->post('invoice_status')) {
            //     $statuses  = $this->input->post('invoice_status');
            //     $_statuses = array();
            //     if (is_array($statuses)) {
            //         foreach ($statuses as $status) {
            //             if ($status != '') {
            //                 array_push($_statuses, $status);
            //             }
            //         }
            //     }
            //     if (count($_statuses) > 0) {
            //         array_push($where, 'AND status IN (' . implode(', ', $_statuses) . ')');
            //     }
            // }
            // var_dump($select);die;
            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblsale_orders';
            $join         = array(
                'LEFT JOIN tblclients ON tblclients.userid = tblsale_orders.customer_id',
                'LEFT JOIN tblsale_order_items ON tblsale_order_items.sale_id = tblsale_orders.id',
                'LEFT JOIN tblitems ON tblitems.id = tblsale_order_items.product_id',
                'LEFT JOIN tblunits ON tblunits.unitid = tblsale_order_items.unit_id'
            );

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                // 'tblinvoices.prefix',
                // 'tblsale_orders.customer_id',                
                'tblsale_orders.id as sale_id',
                // 'tblinvoices.id as invoice_id'
            ));
            $output  = $result['output'];
            $rResult = $result['rResult'];

            $x       = 0;

            // $footer_data = array(
            //     'total' => 0,
            //     'subtotal' => 0,
            //     'total_tax' => 0,
            //     'discount_total' => 0,
            //     'adjustment' => 0,
            //     'amount_open' => 0
            // );

            $footer_data = array(
                'SL' => 0,
                'DTB' => 0
            );
            
            foreach ($rResult as $aRow) {

                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if(strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]]) && strafter($aColumns[$i], 'as ')=='sale_code')
                    {
                        $_data = '<a href="' . admin_url('sale_orders/sale_detail/' . $aRow['sale_id']) . '" target="_blank">' . $aRow['sale_code'] . '</a>';
                    }
                    if($aColumns[$i]=='tblsale_orders.date_ht' || $aColumns[$i]=='tblsale_orders.date')
                    {
                        $_data=_d($aRow[$aColumns[$i]]);
                    }
                    if($aColumns[$i]=='tblsale_order_items.quantity')
                    {
                        $footer_data['SL']+=$aRow[$aColumns[$i]];
                        $_data = _format_number($aRow['tblsale_order_items.quantity']);
                    }
                    if($aColumns[$i]=='tblsale_order_items.unit_cost' || $aColumns[$i]=='tblsale_order_items.amount')
                    {
                        if($aColumns[$i]=='tblsale_order_items.amount')
                            $footer_data['DTB']+=$aRow[$aColumns[$i]];
                        $_data = format_money($aRow[$aColumns[$i]]);
                    }
                    

                    $row[] = $_data;
                }

                $output['aaData'][] = $row;
                $x++;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = format_money($total, $currency_symbol);
                if($key=='SL')
                    $footer_data[$key] = _format_number($total);

            }

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    public function order_tracking_monthly_report()
    {
        if ($this->input->is_ajax_request()) 
        {
            $this->load->model('currencies_model');
            $this->load->model('invoices_model');
            $this->load->model('sale_oders_model');

            $months=array('01','02','03','04','05','06','07','08','09','10','11','12');
            //13 col
            //2 row

            $year=date('Y');
            if($this->input->post('years_report'))
                $year=$this->input->post('years_report');
            $aaData=array();
            $rowQ=array(_l('quantity'));
            $rowT=array(_l('revenue'));
            foreach ($months as $key => $month) {
                $month_detail=$this->sale_oders_model->getSaleOrderDetails($month,$year);
                $rowQ[]=_format_number($month_detail->quantity);
                $rowT[]=format_money($month_detail->grand_total);
            }
            $aaData[]=$rowQ;
            $aaData[]=$rowT;

            // var_dump($aaData);die;

            $select = array(
                'tblsale_orders.date',
                'tblsale_orders.date_ht',                
                'CONCAT(tblsale_orders.prefix,tblsale_orders.code) as sale_code',
                'tblsale_orders.reason',
                'tblitems.code',
                'tblitems.name',
                'tblunits.unit',
                'tblsale_order_items.quantity',
                'tblsale_order_items.unit_cost',
                'tblsale_order_items.amount'
            );

            $result=new stdClass();
            $result->draw=1;
            $result->iTotalDisplayRecords=2;
            $result->iTotalRecords=2;
            $result->aaData=array();
            $result->aaData=$aaData;

            echo json_encode($result);die;

            
            $where  = array(
                // 'AND status != 5'
            );

            $custom_date_select = $this->get_where_report_period('tblsale_orders.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {

                $_temp = substr($select[11], 0, -1);
                $_temp .= ' AND currency =' . $by_currency . ')';
                $select[11] = $_temp;

                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }
            $currency_symbol = $this->currencies_model->get_currency_symbol($currency->id);

            // if ($this->input->post('invoice_status')) {
            //     $statuses  = $this->input->post('invoice_status');
            //     $_statuses = array();
            //     if (is_array($statuses)) {
            //         foreach ($statuses as $status) {
            //             if ($status != '') {
            //                 array_push($_statuses, $status);
            //             }
            //         }
            //     }
            //     if (count($_statuses) > 0) {
            //         array_push($where, 'AND status IN (' . implode(', ', $_statuses) . ')');
            //     }
            // }
            // var_dump($select);die;
            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblsale_orders';
            $join         = array(
                'LEFT JOIN tblclients ON tblclients.userid = tblsale_orders.customer_id',
                'LEFT JOIN tblsale_order_items ON tblsale_order_items.sale_id = tblsale_orders.id',
                'LEFT JOIN tblitems ON tblitems.id = tblsale_order_items.product_id',
                'LEFT JOIN tblunits ON tblunits.unitid = tblsale_order_items.unit_id'
            );

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                // 'tblinvoices.prefix',
                // 'tblsale_orders.customer_id',                
                'tblsale_orders.id as sale_id',
                // 'tblinvoices.id as invoice_id'
            ));
            $output  = $result['output'];
            $rResult = $result['rResult'];

            $x       = 0;

            // $footer_data = array(
            //     'total' => 0,
            //     'subtotal' => 0,
            //     'total_tax' => 0,
            //     'discount_total' => 0,
            //     'adjustment' => 0,
            //     'amount_open' => 0
            // );

            $footer_data = array(
                'SL' => 0,
                'DTB' => 0
            );
            
            foreach ($rResult as $aRow) {

                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if(strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]]) && strafter($aColumns[$i], 'as ')=='sale_code')
                    {
                        $_data = '<a href="' . admin_url('sale_orders/sale_detail/' . $aRow['sale_id']) . '" target="_blank">' . $aRow['sale_code'] . '</a>';
                    }
                    if($aColumns[$i]=='tblsale_orders.date_ht' || $aColumns[$i]=='tblsale_orders.date')
                    {
                        $_data=_d($aRow[$aColumns[$i]]);
                    }
                    if($aColumns[$i]=='tblsale_order_items.quantity')
                    {
                        $footer_data['SL']+=$aRow[$aColumns[$i]];
                        $_data = _format_number($aRow['tblsale_order_items.quantity']);
                    }
                    if($aColumns[$i]=='tblsale_order_items.unit_cost' || $aColumns[$i]=='tblsale_order_items.amount')
                    {
                        if($aColumns[$i]=='tblsale_order_items.amount')
                            $footer_data['DTB']+=$aRow[$aColumns[$i]];
                        $_data = format_money($aRow[$aColumns[$i]]);
                    }
                    

                    $row[] = $_data;
                }

                $output['aaData'][] = $row;
                $x++;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = format_money($total, $currency_symbol);
                if($key=='SL')
                    $footer_data[$key] = _format_number($total);

            }

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    public function order_tracking_book_report()
    {
        if ($this->input->is_ajax_request()) 
        {
            $this->load->model('currencies_model');
            $this->load->model('invoices_model');
            $this->load->model('sales_model');

            $select = array(
                'tblsales.date',
                'tblsales.account_date',                
                'CONCAT(tblsales.prefix,tblsales.code) as sale_code',
                'tblsales.reason',
                'tblitems.code',
                'tblitems.name',
                'tblunits.unit',
                'tblsale_items.quantity',
                'tblsale_items.unit_cost',
                'tblsale_items.amount'
            );

            
            $where  = array(
                // 'AND status != 5'
            );

            $custom_date_select = $this->get_where_report_period('tblsales.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {

                $_temp = substr($select[11], 0, -1);
                $_temp .= ' AND currency =' . $by_currency . ')';
                $select[11] = $_temp;

                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }
            $currency_symbol = $this->currencies_model->get_currency_symbol($currency->id);

            // if ($this->input->post('invoice_status')) {
            //     $statuses  = $this->input->post('invoice_status');
            //     $_statuses = array();
            //     if (is_array($statuses)) {
            //         foreach ($statuses as $status) {
            //             if ($status != '') {
            //                 array_push($_statuses, $status);
            //             }
            //         }
            //     }
            //     if (count($_statuses) > 0) {
            //         array_push($where, 'AND status IN (' . implode(', ', $_statuses) . ')');
            //     }
            // }
            // var_dump($select);die;
            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblsales';
            $join         = array(
                'LEFT JOIN tblclients ON tblclients.userid = tblsales.customer_id',
                'LEFT JOIN tblsale_items ON tblsale_items.sale_id = tblsales.id',
                'LEFT JOIN tblitems ON tblitems.id = tblsale_items.product_id',
                'LEFT JOIN tblunits ON tblunits.unitid = tblsale_items.unit_id'
            );

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                // 'tblinvoices.prefix',
                // 'tblsales.customer_id',                
                'tblsales.id as sale_id',
                // 'tblinvoices.id as invoice_id'
            ));
            $output  = $result['output'];
            $rResult = $result['rResult'];

            
            $x       = 0;

            // $footer_data = array(
            //     'total' => 0,
            //     'subtotal' => 0,
            //     'total_tax' => 0,
            //     'discount_total' => 0,
            //     'adjustment' => 0,
            //     'amount_open' => 0
            // );

            $footer_data = array(
                'SL' => 0,
                'DTB' => 0
            );
            
            foreach ($rResult as $aRow) {

                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if(strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]]) && strafter($aColumns[$i], 'as ')=='sale_code')
                    {
                        $_data = '<a href="' . admin_url('sales/sale_detail/' . $aRow['sale_id']) . '" target="_blank">' . $aRow['sale_code'] . '</a>';
                    }
                    if($aColumns[$i]=='tblsales.account_date' || $aColumns[$i]=='tblsales.date' || $aColumns[$i]=='tblinvoices.date')
                    {
                        $_data=_d($aRow['tblsales.account_date']);
                    }
                    if($aColumns[$i]=='tblsale_items.quantity')
                    {
                        $footer_data['SL']+=$aRow[$aColumns[$i]];
                        $_data = _format_number($aRow['tblsale_items.quantity']);
                    }
                    if($aColumns[$i]=='tblsale_items.unit_cost' || $aColumns[$i]=='tblsale_items.amount')
                    {
                        if($aColumns[$i]=='tblsale_items.amount')
                            $footer_data['DTB']+=$aRow[$aColumns[$i]];
                        $_data = format_money($aRow[$aColumns[$i]]);
                    }
                    

                    $row[] = $_data;
                }

                // var_dump($row);die;
                $output['aaData'][] = $row;
                $x++;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = format_money($total, $currency_symbol);
                if($key=='SL')
                    $footer_data[$key] = _format_number($total);

            }

            

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    public function diaries_report()
    {
        if ($this->input->is_ajax_request()) 
        {
            $this->load->model('currencies_model');
            $this->load->model('invoices_model');
            $this->load->model('sales_model');

            $select = array(
                'tblsales.date',
                'tblsales.account_date',                
                'CONCAT(tblsales.prefix,tblsales.code) as sale_code',
                'tblinvoices.date',
                'tblinvoices.number',
                'tblsales.reason',
                '1',
                'tblsales.total',
                '2',
                'tblsales.discount',
                'tblsales.return_value',
                '5',
                'tblclients.company'
            );

            
            $where  = array(
                // 'AND status != 5'
            );

            $custom_date_select = $this->get_where_report_period('tblsales.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            // if ($this->input->post('sale_agent_invoices')) {
            //     $agents  = $this->input->post('sale_agent_invoices');
            //     $_agents = array();
            //     if (is_array($agents)) {
            //         foreach ($agents as $agent) {
            //             if ($agent != '') {
            //                 array_push($_agents, $agent);
            //             }
            //         }
            //     }
            //     if (count($_agents) > 0) {
            //         array_push($where, 'AND sale_agent IN (' . implode(', ', $_agents) . ')');
            //     }
            // }
            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {

                $_temp = substr($select[11], 0, -1);
                $_temp .= ' AND currency =' . $by_currency . ')';
                $select[11] = $_temp;

                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }
            $currency_symbol = $this->currencies_model->get_currency_symbol($currency->id);

            // if ($this->input->post('invoice_status')) {
            //     $statuses  = $this->input->post('invoice_status');
            //     $_statuses = array();
            //     if (is_array($statuses)) {
            //         foreach ($statuses as $status) {
            //             if ($status != '') {
            //                 array_push($_statuses, $status);
            //             }
            //         }
            //     }
            //     if (count($_statuses) > 0) {
            //         array_push($where, 'AND status IN (' . implode(', ', $_statuses) . ')');
            //     }
            // }

            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblsales';
            $join         = array(
                'JOIN tblclients ON tblclients.userid = tblsales.customer_id',
                'LEFT JOIN tblinvoices ON tblinvoices.rel_id = tblsales.id'
            );

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'tblinvoices.prefix',
                'tblsales.customer_id',                
                'tblsales.id as sale_id',
                'tblinvoices.id as invoice_id'
            ));
            $output  = $result['output'];
            $rResult = $result['rResult'];

            
            $x       = 0;

            // $footer_data = array(
            //     'total' => 0,
            //     'subtotal' => 0,
            //     'total_tax' => 0,
            //     'discount_total' => 0,
            //     'adjustment' => 0,
            //     'amount_open' => 0
            // );

            $footer_data = array(
                'TDT' => 0,
                'DTHH' => 0,
                'DTK' => 0,
                'CK' => 0,
                'GTTV' => 0,
                'DTT' => 0
            );
            
            foreach ($rResult as $aRow) {

                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if(strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]]) && strafter($aColumns[$i], 'as ')=='sale_code')
                    {
                        $_data = '<a href="' . admin_url('sales/sale_detail/' . $aRow['sale_id']) . '" target="_blank">' . $aRow['sale_code'] . '</a>';
                    }
                    if($aColumns[$i]=='tblsales.account_date' || $aColumns[$i]=='tblsales.date' || $aColumns[$i]=='tblinvoices.date')
                    {
                        $_data=_d($aRow['tblsales.account_date']);
                    }
                    if($aColumns[$i]=='tblinvoices.number')
                    {
                        $code=$aRow['prefix'].str_pad($aRow['tblinvoices.number'], get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
                        $_data = '<a href="' . admin_url('invoices/#' . $aRow['invoice_id']) . '" target="_blank">' . $code . '</a>';
                    }
                    if($aColumns[$i]=='1')
                    {
                        $DTK=getTotalReceiptByClientID($aRow['customer_id'],$aRow['tblsales.date'],true);  
                        $TDT=$aRow['tblsales.total']+$DTK;
                        $footer_data['TDT']+=$TDT;
                        $_data = format_money($TDT);
                    }
                    if($aColumns[$i]=='tblsales.total')
                    {   
                        $footer_data['DTHH']+=$aRow['tblsales.total'];
                        $_data = format_money($aRow['tblsales.total']);
                    }
                    if($aColumns[$i]=='2')
                    {
                        $DTK=getTotalReceiptByClientID($aRow['customer_id'],$aRow['tblsales.date'],true);
                        $footer_data['DTK']+=$DTK;
                        $_data = format_money($DTK);
                    }
                    if($aColumns[$i]=='tblsales.discount')
                    {
                        $footer_data['CK']+=$aRow['tblsales.discount'];
                        $_data = format_money($aRow['tblsales.discount']);
                    }
                    if($aColumns[$i]=='tblsales.return_value')
                    {
                        $footer_data['GTTV']+=$aRow['tblsales.return_value'];
                        $_data = format_money($aRow['tblsales.return_value']);
                    }
                    if($aColumns[$i]=='5')
                    {
                        $DTK=getTotalReceiptByClientID($aRow['customer_id'],$aRow['tblsales.date'],true);   
                        $DTT=($aRow['tblsales.total']+$DTK)-($aRow['tblsales.discount']+$aRow['tblsales.return_value']);
                        $footer_data['DTT']+=$DTT;
                        $_data = format_money($DTT);
                    }


                    
                    $row[] = $_data;
                }

                // var_dump($row);die;
                $output['aaData'][] = $row;
                $x++;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = format_money($total, $currency_symbol);
            }

            

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    public function expenses($type = 'simple_report')
    {
        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['currencies']    = $this->currencies_model->get();

        $data['title'] = _l('expenses_report');
        if ($type != 'simple_report') {
            $this->load->model('expenses_model');
            $data['categories'] = $this->expenses_model->get_category();
            $data['years']      = $this->expenses_model->get_expenses_years();

            if ($this->input->is_ajax_request()) {
                $aColumns = array(
                    'category',
                    'amount',
                    'tax',
                    '(SELECT taxrate FROM tbltaxes WHERE id=tblexpenses.tax)',
                    'amount as amount_with_tax',
                    'billable',
                    'date',
                    'CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END as company',
                    'invoiceid',
                    'reference_no',
                    'paymentmode'
                );
                $join     = array(
                    'LEFT JOIN tblclients ON tblclients.userid = tblexpenses.clientid',
                    'LEFT JOIN tblexpensescategories ON tblexpensescategories.id = tblexpenses.category'
                );
                $where    = array();
                $filter   = array();
                include_once(APPPATH . 'views/admin/tables/includes/expenses_filter.php');
                if (count($filter) > 0) {
                    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
                }

                $by_currency = $this->input->post('currency');
                if ($by_currency) {
                    $currency = $this->currencies_model->get($by_currency);
                    array_push($where, 'AND currency=' . $by_currency);
                } else {
                    $currency = $this->currencies_model->get_base_currency();
                }
                $currency_symbol = $this->currencies_model->get_currency_symbol($currency->id);

                $sIndexColumn = "id";
                $sTable       = 'tblexpenses';
                $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                    'tblexpensescategories.name as category_name',
                    'tblexpenses.id',
                    'tblexpenses.clientid',
                    'currency'
                ));
                $output       = $result['output'];
                $rResult      = $result['rResult'];
                $this->load->model('currencies_model');
                $this->load->model('payment_modes_model');

                $footer_data = array(
                    'amount' => 0,
                    'total_tax' => 0,
                    'amount_with_tax' => 0
                );

                foreach ($rResult as $aRow) {
                    $row = array();
                    for ($i = 0; $i < count($aColumns); $i++) {
                        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                            $_data = $aRow[strafter($aColumns[$i], 'as ')];
                        } else {
                            $_data = $aRow[$aColumns[$i]];
                        }
                        if ($aRow['tax'] != 0) {
                            $_tax = get_tax_by_id($aRow['tax']);
                        }
                        if ($aColumns[$i] == 'category') {
                            $_data = '<a href="' . admin_url('expenses/list_expenses/' . $aRow['id']) . '" target="_blank">' . $aRow['category_name'] . '</a>';
                        } else if ($aColumns[$i] == 'amount' || $i == 4) {

                            $total = $_data;
                            if ($i != 4) {
                                $footer_data['amount'] += $total;
                            } else {
                                if ($aRow['tax'] != 0 && $i == 4) {
                                    $total += ($total / 100 * $_tax->taxrate);
                                }
                                $footer_data['amount_with_tax'] += $total;
                            }

                            $_data = format_money($total, $currency_symbol);
                        } else if ($i == 7) {
                            $_data = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $aRow['company'] . '</a>';
                        } else if ($aColumns[$i] == 'paymentmode') {
                            $_data = '';
                            if ($aRow['paymentmode'] != '0' && !empty($aRow['paymentmode'])) {
                                $payment_mode = $this->payment_modes_model->get($aRow['paymentmode'], array(), false, true);
                                if ($payment_mode) {
                                    $_data = $payment_mode->name;
                                }
                            }
                        } else if ($aColumns[$i] == 'date') {
                            $_data = _d($_data);
                        } else if ($aColumns[$i] == 'tax') {
                            if ($aRow['tax'] != 0) {
                                $_data = $_tax->name . ' - ' . _format_number($_tax->taxrate) . '%';
                            } else {
                                $_data = '';
                            }
                        } else if ($i == 3) {
                            if ($aRow['tax'] != 0) {
                                $total = ($total / 100 * $_tax->taxrate);
                                $_data = format_money($total, $currency_symbol);
                                $footer_data['total_tax'] += $total;
                            } else {
                                $_data = _format_number(0);
                            }
                        } else if ($aColumns[$i] == 'billable') {
                            if ($aRow['billable'] == 1) {
                                $_data = _l('expenses_list_billable');
                            } else {
                                $_data = _l('expense_not_billable');
                            }
                        } else if ($aColumns[$i] == 'invoiceid') {
                            if ($_data) {
                                $_data = '<a href="' . admin_url('invoices/list_invoices/' . $_data) . '">' . format_invoice_number($_data) . '</a>';
                            } else {
                                $_data = '';
                            }

                        }
                        $row[] = $_data;
                    }
                    $output['aaData'][] = $row;

                }

                foreach ($footer_data as $key => $total) {
                    $footer_data[$key] = format_money($total, $currency_symbol);
                }

                $output['sums'] = $footer_data;
                echo json_encode($output);
                die;
            }
            $this->load->view('admin/reports/expenses_detailed', $data);
        } else {
            if (!$this->input->get('year')) {
                $data['current_year'] = date('Y');
            } else {
                $data['current_year'] = $this->input->get('year');
            }

            $data['chart_js_assets']   = true;

            $data['export_not_supported'] = ($this->agent->browser() == 'Internet Explorer' || $this->agent->browser() == 'Spartan');

            $this->load->model('expenses_model');

            $data['chart_not_billable'] = json_encode($this->reports_model->get_stats_chart_data(_l('not_billable_expenses_by_categories'), array(
                'billable' => 0
            ), array(
                'backgroundColor' => 'rgba(252,45,66,0.4)',
                'borderColor' => '#fc2d42'
            ), $data['current_year']));

            $data['chart_billable'] = json_encode($this->reports_model->get_stats_chart_data(_l('billable_expenses_by_categories'), array(
                'billable' => 1
            ), array(
                'backgroundColor' => 'rgba(37,155,35,0.2)',
                'borderColor' => '#84c529'
            ), $data['current_year']));

            $data['expense_years'] = $this->expenses_model->get_expenses_years();
            $data['categories']    = $this->expenses_model->get_category();

            $this->load->view('admin/reports/expenses', $data);
        }
    }
    public function expenses_vs_income($year = '')
    {
        $_expenses_years = array();
        $_years          = array();
        $this->load->model('expenses_model');
        $expenses_years = $this->expenses_model->get_expenses_years();
        $payments_years = $this->reports_model->get_distinct_payments_years();
        foreach ($expenses_years as $y) {
            array_push($_years, $y['year']);
        }
        foreach ($payments_years as $y) {
            array_push($_years, $y['year']);
        }
        $_years                                  = array_map("unserialize", array_unique(array_map("serialize", $_years)));
        $data['years']                           = $_years;
        $data['chart_expenses_vs_income_values'] = json_encode($this->reports_model->get_expenses_vs_income_report($year));
        $data['title']                           = _l('als_expenses_vs_income');
        $data['chart_js_assets']   = true;
        $this->load->view('admin/reports/expenses_vs_income', $data);
    }

    /* Total income report / ajax chart*/
    public function total_income_report()
    {
        echo json_encode($this->reports_model->total_income_report());
    }
    public function report_by_payment_modes()
    {
        echo json_encode($this->reports_model->report_by_payment_modes());
    }
    public function report_by_customer_groups()
    {
        echo json_encode($this->reports_model->report_by_customer_groups());
    }
    /* Leads conversion monthly report / ajax chart*/
    public function leads_monthly_report($month)
    {
        echo json_encode($this->reports_model->leads_monthly_report($month));
    }
}
