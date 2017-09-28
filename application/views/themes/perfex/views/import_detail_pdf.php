<?php
$dimensions = $pdf->getPageDimensions();


function mb_ucfirst($string, $encoding)
{
    return mb_convert_case($string, MB_CASE_TITLE, $encoding);
}
// Tag - used in BULK pdf exporter
if ($tag != '') {
    $pdf->SetFillColor(240, 240, 240);
    $pdf->SetDrawColor(245, 245, 245);
    $pdf->SetXY(0, 0);
    $pdf->SetFont($font_name, 'B', 15);
    $pdf->SetTextColor(0);
    $pdf->SetLineWidth(0.75);
    $pdf->StartTransform();
    $pdf->Rotate(-35, 109, 235);
    $pdf->Cell(100, 1, mb_strtoupper($tag, 'UTF-8'), 'TB', 0, 'C', '1');
    $pdf->StopTransform();
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->setX(10);
    $pdf->setY(10);
}

$pdf_text_color_array = hex2rgb(get_option('pdf_text_color'));
if (is_array($pdf_text_color_array) && count($pdf_text_color_array) == 3) {
    $pdf->SetTextColor($pdf_text_color_array[0], $pdf_text_color_array[1], $pdf_text_color_array[2]);
}

$info_right_column = '';
$info_left_column  = '';

$items=$invoice->items;

$tk_no = "";
$tk_co="";
$accountNo=array();
$accountCo=array();
$warehouse_id=false;
foreach ($items as $item) 
{
    $warehouse_id=$item->warehouse_id;
    $accountNo[]=$item->tk_no;
    $accountCo[]=$item->tk_co;
}
$accountNo=array_unique($accountNo);
$accountCo=array_unique($accountCo);

foreach ($accountNo as $key => $account) {
    if(empty($tk_no))
    {  $tk_no .= '<br />' . get_code_tk($account)." ".format_money(get_value_tk_no(' tblimport_items','import_id',$invoice->id,$account));
    }
    else
    {
        $tk_no .= '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . get_code_tk($account)." ".format_money(get_value_tk_no(' tblimport_items','import_id',$invoice->id,$account));
    }
}

foreach ($accountCo as $key => $account) {
    if(empty($tk_co)){
    $tk_co .= '<br />' . get_code_tk($account)." ".format_money(get_value_tk_co(' tblimport_items','import_id',$invoice->id,$account));
    }
    else
    {
        $tk_co .= '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . get_code_tk($account)." ".format_money(get_value_tk_co(' tblimport_items','import_id',$invoice->id,$account));
    }
}

    // foreach ($items as $rom) {
    //     $tk_no .= '<br />' . get_code_tk($rom->tk_no);
    //     $tk_co = $tk_co . '<br />' . get_code_tk($rom->tk_co);
    //     $total += $rom->total;
    // }
    $tk_no = "Nợ: " . trim($tk_no, '<br />');
    $tk_co = "Có: " . trim($tk_co, '<br />');

    $mau = '<b align="center">Mẩu số 01-VT</b><br />
        <i style="font-weight: 100;font-size: 12px;">(Ban hành theo QĐ số 436/2016/QĐ-BTC<br /> Ngày 14/09/2016 của BTC)</i>
    ';
    $info_right = '
    <table style="float: right" >
        <tr>
            <td style="width: 60%" align="right"></td>
            <td style="width: 40%" align="left">' . $mau . '</td>
        </tr>
         <tr>
            <td style="width: 70%" align="right"></td>
            <td style="width: 30%" align="left">' . $tk_no . '</td>
        </tr>
        <tr>
            <td style="width: 70%" align="right"></td>
            <td style="width: 30%" align="left">' . $tk_co . '</td>
        </tr>
    </table>';


    
// $info_right_column=$info_right_column .= '<a href="' . admin_url('#') . '" style="color:#4e4e4e;text-decoration:none;"><b> ' . date('Y-m-d H:i:s') . '</b></a>';

    $invoice_info = '';
    $invoice_info = '<b>' . get_option('invoice_company_name') . '</b><br />';
    $invoice_info .= _l('address') . ': ' . get_option('invoice_company_address') . '<br/>';
    // if (get_option('invoice_company_city') != '') {
    //     $invoice_info .= get_option('invoice_company_city') . ', ';
    // }
    if (get_option('company_vat') != '') {
        $invoice_info .= _l('vat_no') . ': ' . get_option('company_vat') . '<br/>';
    }
    $invoice_info .= get_option('invoice_company_country_code') . ' ';
    $invoice_info .= get_option('invoice_company_postal_code') . ' ';
    $invoice_info .= _l('company_bank_account') . get_option('company_contract_blank_account') . '<br />';
    if (get_option('invoice_company_phonenumber') != '') {
        $invoice_info .= _l('Tel') . ': ' . get_option('invoice_company_phonenumber') . '  ';
    }
    if (get_option('invoice_company_faxnumber') != '') {
        $invoice_info .= _l('Fax') . ': ' . get_option('invoice_company_faxnumber') . '  ';
    }
    if (get_option('main_domain') != '') {
        $invoice_info .= _l('Website') . ': ' . get_option('main_domain');
    }

// $pdf->MultiCell(($dimensions['wk'] / 2) - $dimensions['lm'], 0, $info_left_column, 0, 'J', 0, 0, '', '', true, 0, true, true, 0);
// write the second column
// $pdf->MultiCell(($dimensions['wk'] / 2) - $dimensions['rm'], 0, $info_right_column, 0, 'R', 0, 1, '', '', true, 0, true, false, 0);
// $pdf->MultiCell(0, 0, $invoice_info, 0, 'C', 0, 1, '', '', true, 0, true, false, 0);
// $y            = $pdf->getY();

$pdf->writeHTMLCell((true ? ($dimensions['wk']) - ($dimensions['lm'] * 2) : ($dimensions['wk'] / 2) - $dimensions['lm']), '', '', 20, $invoice_info, 0, 0, false, true, ($swap == '1' ? 'R' : 'J'), true);

$pdf->writeHTMLCell(200, '', '', 20, $info_right, 0, 0, false, true, ('R'), true);

$pdf->ln(25);
// Set Head
if($invoice->rel_type=='adjustment')
{
    $plan_name=_l('adjustments');
}
if($invoice->rel_type=='internal')
{
    $plan_name=_l('internals');
}
if($invoice->rel_type=='return')
{
    $plan_name=_l('returns');
}
if($invoice->rel_type=='contract')
{
    $plan_name=_l('importfromcontract');
}



$pdf->SetFont($font_name, 'B', 20);
$pdf->Cell(0, 0, mb_strtoupper($plan_name, 'UTF-8') , 0, 1, 'C', 0, '', 0);

$pdf->SetFont($font_name, '', $font_size);
$pdf->writeHTMLCell('', '', '', '', '<i>'.getStrDate($invoice->date).'</i>', 0, 1, false, true, 'C', true);

$pdf->SetFont($font_name, '', $font_size);
$pdf->writeHTMLCell('', '', '', '', _l('no').$invoice_number, 0, 1, false, true, 'C', true);
$pdf->ln(10);

// //Set detail
// $pdf->SetFont($font_name, '', $font_size);
// $pdf->Cell(0, 0, _l('Mã phiếu: ').$invoice_number , 0, 1, 'L', 0, '', 0);
// $pdf->ln(4);

$pdf->SetFont($font_name, '', $font_size);
$pdf->writeHTMLCell('', '', '', '', _l('Họ tên người giao hàng: ').'<b>'.$invoice->deliver_name.'</b>', 0, 1, false, true, 'L', true);
$pdf->ln(2);

$contract=getContractByImportID($invoice->id);
$strHD='';
if($contract) $strHD=_l('blank10').$contract->code._l('blank10').mb_strtolower(getStrDate($contract->date_create),'UTF-8')._l(' của ').$contract->supplier_name;
$pdf->writeHTMLCell('', '', '', '', _l('Theo HĐ số ').$strHD , 0, 1, false, true, 'L', true);
$pdf->ln(2);

$warehouse=getWarehouseByID($warehouse_id);
$strWH=_l('blank10')._l('blank10')._l('blank10')._l('blank10');
if($warehouse)
{
    $strWH=_l('blank10').'<b>'.$warehouse->code.' - '.$warehouse->warehouse.'</b>'._l('blank10');
}

$pdf->writeHTMLCell('', '', '', '', _l('Nhập tại kho ').$strWH._l('Địa điểm')._l('blank10').$warehouse->address, 0, 1, false, true, 'L', true);

$pdf->ln(4);

// $strDetails='';
// $strDetails='<ul style="list-style-type: square;">';
// $strDetails.='<li>'._l('Họ tên người giao hàng: ').'<b>'.$invoice->deliver_name.'</b>'.'</i>'.'</li>';
// $strDetails.='<li>'._l('Theo HĐ số ').'</li>';
// $strDetails.='<li>'._l('Nhập tại kho ')._l('blank10').''._l('Địa điểm').'</li>';
// $strDetails.='</ul>';
// $pdf->writeHTML($strDetails, true, false, false, false, 'L');


// Bill to
// $client_details = '<b>' . _l('invoice_bill_to') . '</b><br />';
// if($invoice->client->show_primary_contact == 1){
//     $pc_id = get_primary_contact_user_id($invoice->clientid);
//     if($pc_id){
//         $client_details .= get_contact_full_name($pc_id) .'<br />';
//     }
// }
// $client_details .= $invoice->client->company . '<br />';
// $client_details .= $invoice->billing_street . '<br />';
// if (!empty($invoice->billing_city)) {
//     $client_details .= $invoice->billing_city;
// }
// if (!empty($invoice->billing_state)) {
//     $client_details .= ', ' . $invoice->billing_state;
// }
// $billing_country = get_country_short_name($invoice->billing_country);
// if (!empty($billing_country)) {
//     $client_details .= '<br />' . $billing_country;
// }
// if (!empty($invoice->billing_zip)) {
//     $client_details .= ', ' . $invoice->billing_zip;
// }
// if (!empty($invoice->client->vat)) {
//     $client_details .= '<br />' . _l('invoice_vat') . ': ' . $invoice->client->vat;
// }
// check for invoice custom fields which is checked show on pdf
// $pdf_custom_fields = get_custom_fields('customers', array(
//     'show_on_pdf' => 1
// ));
// if (count($pdf_custom_fields) > 0) {
//     $client_details .= '<br />';
//     foreach ($pdf_custom_fields as $field) {
//         $value = get_custom_field_value($invoice->clientid, $field['id'], 'customers');
//         if ($value == '') {
//             continue;
//         }
//         $client_details .= $field['name'] . ': ' . $value . '<br />';
//     }
// }
// $pdf->writeHTMLCell(($dimensions['wk'] / 2) - $dimensions['rm'], '', '', ($swap == '1' ? $y : ''), $client_details, 0, 1, false, true, ($swap == '1' ? 'J' : 'R'), true);
// $pdf->Ln(5);
// ship to to
// if ($invoice->include_shipping == 1 && $invoice->show_shipping_on_invoice == 1) {
//     $pdf->Ln(5);
//     $shipping_details = '<b>' . _l('ship_to') . '</b><br />';
//     $shipping_details .= $invoice->shipping_street . '<br />' . $invoice->shipping_city . ', ' . $invoice->shipping_state . '<br />' . get_country_short_name($invoice->shipping_country) . ', ' . $invoice->shipping_zip;
//     $pdf->writeHTMLCell(($dimensions['wk'] - ($dimensions['rm'] + $dimensions['lm'])), '', '', '', $shipping_details, 0, 1, false, true, ($swap == '1' ? 'L' : 'R'), true);
//     $pdf->Ln(5);
// }
// Dates
// $pdf->Cell(0, 0, _l('invoice_data_date') . ' ' . _d($invoice->date), 0, 1, ($swap == '1' ? 'L' : 'R'), 0, '', 0);
// if (!empty($invoice->duedate)) {
//     $pdf->Cell(0, 0, _l('invoice_data_duedate') . ' ' . _d($invoice->duedate), 0, 1, ($swap == '1' ? 'L' : 'R'), 0, '', 0);
// }
// if ($invoice->sale_agent != 0) {
//     if (get_option('show_sale_agent_on_invoices') == 1) {
//         $pdf->Cell(0, 0, _l('sale_agent_string') . ': ' . get_staff_full_name($invoice->sale_agent), 0, 1, ($swap == '1' ? 'L' : 'R'), 0, '', 0);
//     }
// }
// check for invoice custom fields which is checked show on pdf
// $pdf_custom_fields = get_custom_fields('invoice', array(
//     'show_on_pdf' => 1
// ));
// foreach ($pdf_custom_fields as $field) {
//     $value = get_custom_field_value($invoice->id, $field['id'], 'invoice');
//     if ($value == '') {
//         continue;
//     }
//     $pdf->writeHTMLCell(0, '', '', '', $field['name'] . ': ' . $value, 0, 1, false, true, ($swap == '1' ? 'J' : 'R'), true);
// }
// The Table
$pdf->Ln(5);
$tblhtml = '
<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1px">
    <tr height="30" bgcolor="' . get_option('pdf_table_heading_color') . '" style="color:' . get_option('pdf_table_heading_text_color') . ';">
        <th scope="col"  width="5%" align="center">STT</th>
        <th scope="col"  width="17%" align="center">' . _l('Tên, nhãn hiệu, quy cách, phẩm chất vật tư, dụng cụ sản phẩm, hàng hóa') . '</th>
        <th scope="col"  width="12%" align="center">' . _l('Mã số') . '</th>
        <th scope="col"  width="8%" align="center">' . _l('Đơn vị tính') . '</th>
        <th scope="col"  width="9%" align="center">' . _l('Số lượng (Chứng từ)') . '</th>
        <th scope="col"  width="9%" align="center">' . _l('Số lượng (Thực nhập)') . '</th>
        <th  width="15%" align="center">' . _l('Đơn giá') . '</th>
        <th  width="15%" align="center">' . _l('Thành tiền') . '</th>
        <th  width="10%" align="center">' . _l('Ghi chú') . '</th>';
$tblhtml .= '</tr>';
$tblhtml.='<tr bgcolor="' . get_option('pdf_table_heading_color') . '" style="color:' . get_option('pdf_table_heading_text_color') . ';">';
$tblhtml.='<th align="center">A</th>';
$tblhtml.='<th align="center">B</th>';
$tblhtml.='<th align="center">C</th>';
$tblhtml.='<th align="center">D</th>';
$tblhtml.='<th align="center">1</th>';
$tblhtml.='<th align="center">2</th>';
$tblhtml.='<th align="center">3</th>';
$tblhtml.='<th align="center">4</th>';
$tblhtml.='<th align="center">5</th>';
$tblhtml.='</tr>';


// Items
$tblhtml .= '<tbody>';
$grand_total=0;
for ($i=0; $i < count($invoice->items) ; $i++) { 
    $rowspan=count($invoice->items);
    $grand_total+=$invoice->items[$i]->sub_total;
    $tblhtml.='<tr>';
    $tblhtml.='<td align="center">'.($i+1).'</td>';
    $tblhtml.='<td>'.$invoice->items[$i]->product_name.'</td>';
    $tblhtml.='<td>'.$invoice->items[$i]->prefix.$invoice->items[$i]->code.'</td>';
    $tblhtml.='<td align="right">'.$invoice->items[$i]->unit_name.'</td>';
    $tblhtml.='<td align="right">'._format_number($invoice->items[$i]->quantity).'</td>';    
    $tblhtml.='<td align="right">'.$invoice->items[$i]->quantity_net.'</td>';
    $tblhtml.='<td align="right">'.format_money($invoice->items[$i]->unit_cost).'</td>';
    $tblhtml.='<td align="right">'.format_money($invoice->items[$i]->sub_total).'</td>';
    $tblhtml.='<td  rowspan="'.$rowspan.'" align="left">'.$invoice->reason.'</td>';
    $tblhtml.='</tr>';
}


    $tblhtml.='<tr>';
    $tblhtml.='<td align="right"></td>';
    $tblhtml.='<td align="right"><b>'._l('Tổng tiền').'</b></td>';
    $tblhtml.='<td align="right"></td>';
    $tblhtml.='<td align="right"></td>';
    $tblhtml.='<td align="right"></td>';
    $tblhtml.='<td align="right"></td>';
    $tblhtml.='<td align="right"></td>';
    $tblhtml.='<td align="right">'.format_money($grand_total).'</td>';
    $tblhtml.='</tr>';
$tblhtml .= '</tbody>';
$tblhtml .= '</table>';
$pdf->writeHTML($tblhtml, true, false, false, false, '');

$strmoney='<ul>';
$strmoney.='<li>'._l('str_money').'<i>'.$CI->numberword->convert($grand_total,get_option('default_currency')).'</i>'.'</li>';
$strmoney.='<li>'._l('certificate_root')._l('blank10').$contract->code.'</li>';;
$strmoney.='</ul>';
$pdf->writeHTML($strmoney, true, false, false, false, 'L');
$pdf->Ln(5);

$pdf->SetFont($font_name, '', $font_size);
$pdf->writeHTMLCell('', '', '', '', '<i>'.getStrDate($invoice->date).'</i>', 0, 1, false, true, 'R', true);

$pdf->Ln(5);
$table = "<table style=\"width: 100%;text-align: center\" border=\"0\">
        <tr>
            <td><b>" . mb_ucfirst(_l('creater'), "UTF-8") . "</b></td>
            <td><b>" . mb_ucfirst(_l('deliver'), "UTF-8") . "</b></td>
            <td><b>" . mb_ucfirst(_l('warehouseman'), "UTF-8") . "</b></td>
            <td><b>" . mb_ucfirst(_l('chief_accountant'), "UTF-8") . "</b></td>
        </tr>
        <tr>
            <td>(ký, ghi rõ họ tên)</td>
            <td>(ký, ghi rõ họ tên)</td>
            <td>(ký, ghi rõ họ tên)</td>
            <td>(ký, ghi rõ họ tên)</td>
        </tr>
        <tr>
            <td style=\"height: 100px\" colspan=\"3\"></td>
        </tr>
        <tr>
            <td>" . mb_ucfirst($invoice->creater,"UTF-8") . "</td>
            <td>" . mb_ucfirst($invoice->deliver_name,"UTF-8") . "</td>
            <td>" . mb_ucfirst($invoice->warehouseman,"UTF-8") . "</td>
            <td>" . mb_ucfirst($invoice->chief_accountant,"UTF-8") . "</td>
        </tr>

</table>";
$pdf->writeHTML($table, true, false, false, false, '');


// $pdf->Ln(8);
// $tbltotal = '';
// $tbltotal .= '<table cellpadding="6">';
// $tbltotal .= '
// <tr>
//     <td align="right" width="80%">' . _l('Sản phẩm') . '</td>
//     <td align="right" width="20%">' . count($invoice->items) . '</td>
// </tr>';
//     $tbltotal .= '
//     <tr>
//         <td align="right" width="80%">' . _l('Tổng tiền') . '</td>
//         <td align="right" width="20%">' . format_money($grand_total) . '</td>
//     </tr>';
// foreach ($taxes as $tax) {
//     $total = array_sum($tax['total']);
//     if ($invoice->discount_percent != 0 && $invoice->discount_type == 'before_tax') {
//         $total_tax_calculated = ($total * $invoice->discount_percent) / 100;
//         $total                = ($total - $total_tax_calculated);
//     }
//     // The tax is in format TAXNAME|20
//     $_tax_name = explode('|', $tax['tax_name']);
//     $tbltotal .= '<tr>
//     <td align="right" width="80%">' . $_tax_name[0] . '(' . _format_number($tax['taxrate']) . '%)' . '</td>
//     <td align="right" width="20%">' . format_money($total, $invoice->symbol) . '</td>
// </tr>';
// }
// if ($invoice->adjustment != '0.00') {
//     $tbltotal .= '<tr>
//     <td align="right" width="80%">' . _l('invoice_adjustment') . '</td>
//     <td align="right" width="20%">' . format_money($invoice->adjustment, $invoice->symbol) . '</td>
// </tr>';
// }
// $tbltotal .= '
// <tr style="background-color:#f0f0f0;">
//     <td align="right" width="80%">' . _l('invoice_total') . '</td>
//     <td align="right" width="20%">' . format_money($invoice->total, $invoice->symbol) . '</td>
// </tr>';

// if ($invoice->status == 3) {
//     $tbltotal .= '
//     <tr>
//         <td align="right" width="80%">' . _l('invoice_total_paid') . '</td>
//         <td align="right" width="20%">' . format_money(sum_from_table('tblinvoicepaymentrecords', array(
//         'field' => 'amount',
//         'where' => array(
//             'invoiceid' => $invoice->id
//         )
//     )), $invoice->symbol) . '</td>
//     </tr>
//     <tr style="background-color:#f0f0f0;">
//        <td align="right" width="80%">' . _l('invoice_amount_due') . '</td>
//        <td align="right" width="20%">' . format_money(get_invoice_total_left_to_pay($invoice->id, $invoice->total), $invoice->symbol) . '</td>
//    </tr>';
// }
// $tbltotal .= '</table>';
// $pdf->writeHTML($tbltotal, true, false, false, false, '');

// if (get_option('total_to_words_enabled') == 1) {
//     // Set the font bold
//     $pdf->SetFont($font_name, 'B', $font_size);
//     $pdf->Cell(0, 0, _l('num_word') . ': ' . $CI->numberword->convert($invoice->total, $invoice->currency_name), 0, 1, 'C', 0, '', 0);
//     // Set the font again to normal like the rest of the pdf
//     $pdf->SetFont($font_name, '', $font_size);
//     $pdf->Ln(4);
// }

// if (count($invoice->payments) > 0 && get_option('show_transactions_on_invoice_pdf') == 1) {
//     $pdf->Ln(4);
//     $border = 'border-bottom-color:#000000;border-bottom-width:1px;border-bottom-style:solid; 1px solid black;';
//     $pdf->SetFont($font_name, 'B', $font_size);
//     $pdf->Cell(0, 0, _l('invoice_received_payments'), 0, 1, 'L', 0, '', 0);
//     $pdf->SetFont($font_name, '', $font_size);
//     $pdf->Ln(4);
//     $tblhtml = '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="0">
//         <tr height="20"  style="color:#000;border:1px solid #000;">
//         <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_number_heading') . '</th>
//         <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_mode_heading') . '</th>
//         <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_date_heading') . '</th>
//         <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_amount_heading') . '</th>
//     </tr>';
//     $tblhtml .= '<tbody>';
//     foreach ($invoice->payments as $payment) {
//         $payment_name = $payment['name'];
//         if (!empty($payment['paymentmethod'])) {
//             $payment_name .= ' - ' . $payment['paymentmethod'];
//         }
//         $tblhtml .= '
//             <tr>
//             <td>' . $payment['paymentid'] . '</td>
//             <td>' . $payment_name . '</td>
//             <td>' . _d($payment['date']) . '</td>
//             <td>' . format_money($payment['amount'], $invoice->symbol) . '</td>
//             </tr>
//         ';
//     }
//     $tblhtml .= '</tbody>';
//     $tblhtml .= '</table>';
//     $pdf->writeHTML($tblhtml, true, false, false, false, '');
// }

// if (found_invoice_mode($payment_modes, $invoice->id, true, true)) {
//     $pdf->Ln(4);
//     $pdf->SetFont($font_name, 'B', 10);
//     $pdf->Cell(0, 0, _l('invoice_html_offline_payment'), 0, 1, 'L', 0, '', 0);
//     $pdf->SetFont($font_name, '', 10);
//     foreach ($payment_modes as $mode) {
//         if (is_numeric($mode['id'])) {
//             if (!is_payment_mode_allowed_for_invoice($mode['id'], $invoice->id)) {
//                 continue;
//             }
//         }
//         if (isset($mode['show_on_pdf']) && $mode['show_on_pdf'] == 1) {
//             $pdf->Ln(2);
//             $pdf->Cell(0, 0, $mode['name'], 0, 1, 'L', 0, '', 0);
//             $pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + $dimensions['rm']), 0, clear_textarea_breaks($mode['description']), 0, 'L');
//         }
//     }
// }

// if (!empty($invoice->clientnote)) {
//     $pdf->Ln(4);
//     $pdf->SetFont($font_name, 'B', 10);
//     $pdf->Cell(0, 0, _l('invoice_note'), 0, 1, 'L', 0, '', 0);
//     $pdf->SetFont($font_name, '', 10);
//     $pdf->Ln(2);
//     $pdf->MultiCell(0, 0, clear_textarea_breaks($invoice->clientnote), 0, 'L');
// }

// if (!empty($invoice->terms)) 
// {
//     $pdf->Ln(4);
//     $pdf->SetFont($font_name, 'B', 10);
//     $pdf->Cell(0, 0, _l('terms_and_conditions'), 0, 1, 'L', 0, '', 0);
//     $pdf->SetFont($font_name, '', 10);
//     $pdf->Ln(2);
//     $pdf->MultiCell(0, 0, clear_textarea_breaks($invoice->terms), 0, 'L');
// }

