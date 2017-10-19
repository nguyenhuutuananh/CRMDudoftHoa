<?php
$dimensions = $pdf->getPageDimensions();

// function xHeader() {
    // set bacground image
    // $img_file = FCPATH.'uploads/company/background_pdf.png';
    // exit($img_file);
    // $pdf->Image('E:\xampp\htdocs\01917F\uploads/company/logo2.png', 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
// }
// xHeader();
// exit(K_PATH_IMAGES);
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
$info_right_column=$info_right_column .= '<a href="' . admin_url('#') . '" style="color:#4e4e4e;text-decoration:none;"><b> ' . date('Y-m-d H:i:s') . '</b></a>';

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
// write the first column
// $info_left_column .= pdf_logo_url();
// $pdf->MultiCell(($dimensions['wk'] / 2) - $dimensions['lm'], 0, $info_left_column, 0, 'J', 0, 0, '', '', true, 0, true, true, 0);
// // write the second column
// $pdf->MultiCell(($dimensions['wk'] / 2) - $dimensions['rm'], 0, $info_right_column, 0, 'R', 0, 1, '', '', true, 0, true, false, 0);

// $divide=_l('divider');
// $pdf->ln(6);
// $y            = $pdf->getY();
// $pdf->writeHTMLCell('', '', '', $y, $divide, 0, 0, false, true, ($swap == '1' ? 'R' : 'J'), true);
// $pdf->ln(2);
$y            = $pdf->getY();
$pdf->writeHTMLCell((true ? ($dimensions['wk']) - ($dimensions['lm'] * 2) : ($dimensions['wk'] / 2) - $dimensions['lm']), '', '', 20, $invoice_info, 0, 0, false, true, ($swap == '1' ? 'R' : 'J'), true);
$pdf->ln(23);
// Set Head
$plan_name=_l('sales');

$pdf->SetFont($font_name, 'B', 20);
$pdf->Cell(0, 0, mb_strtoupper($plan_name, 'UTF-8') , 0, 1, 'C', 0, '', 0);
//Set code
$pdf->SetFont($font_name, 'I', $font_size);
$pdf->Cell(0, 0, _l('code_no').($invoice_number) , 0, 1, 'C', 0, '', 0);
// $pdf->ln(4);
//Set date
$pdf->SetFont($font_name, 'I', $font_size);
$pdf->Cell(0, 0, _l('view_date').': '._d($invoice->date) , 0, 1, 'C', 0, '', 0);
$pdf->ln(4);
//Set detail
$pdf->SetFont($font_name, '', $font_size);
$pdf->Cell(0, 0, _l('customer_name').': '.$customer->company , 0, 1, 'L', 0, '', 0);
$pdf->ln(2);
$adress1='';
$adress2=array();
if($customer->address_building){$adress2[]=_l('Tòa nhà ').$customer->address_building;}
if($customer->address_home_number){$adress2[]=_l('Số ').$customer->address_home_number;}
if($customer->address_town){$adress2[]=_l('Đường ').$customer->address_town;}
if($customer->address_ward){$adress2[]=_l('Phường/xã ').getWard($customer->address_ward)->name;}
if($customer->state){$adress2[]=_l('Quận/huyện ').getDistrict($customer->state)->name;}
if($customer->city){$adress2[]=_l('Tỉnh/tp ').getProvince($customer->city)->name;}
$address=($adress1)? $adress1 : implode(', ', $adress2);
$shipping_address=array();
if($customer->shipping_building){$adress2[]=_l('Tòa nhà ').$customer->shipping_building;}
if($customer->shipping_home_number){$adress2[]=_l('Số ').$customer->shipping_home_number;}
if($customer->shipping_street){$adress2[]=_l('Đường ').$customer->shipping_street;}
if($customer->shipping_ward){$adress2[]=_l('Phường/xã ').getWard($customer->shipping_ward)->name;}
if($customer->shipping_state){$adress2[]=_l('Quận/huyện ').getDistrict($customer->shipping_state)->name;}
if($customer->shipping_city){$adress2[]=_l('Tỉnh/tp ').getProvince($customer->shipping_city);}
$pdf->SetFont($font_name, '', $font_size);
// $pdf->Cell(0, 0, _l('address').': '.'<div width="100%">'.$address.'</div>', 0, 1, 'L', 0, '', 0);
// $pdf->writeHTMLCell(0, '', '', $y, $address, 0, 0, false, true, 'C', true);
// $pdf->writeHTML(_l('address').': '.'<div width="100%">'.$address.'</div>', true, false, false, false, '');
$pdf->writeHTMLCell(0, '', '', '', '<div width="100%">'._l('address').': '.$address.'</div>', 0, 1, false, true, 'L', true);
$pdf->ln(2);

$pdf->SetFont($font_name, '', $font_size);
$pdf->Cell(0, 0, _l('tel').': '.$customer->phonenumber.'     '. _l('fax').':'.$customer->fax , 0, 1, 'L', 0, '', 0);
$pdf->ln(2);

$pdf->Cell(0, 0, _l('shipping_address').': '.implode(', ', $shipping_address) , 0, 1, 'L', 0, '', 0);
$pdf->ln(2);

// Bill to
// The Table
$pdf->Ln(5);
$tblhtml = '
<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1px">
    <tr height="30" bgcolor="' . get_option('pdf_table_heading_color') . '" style="color:' . get_option('pdf_table_heading_text_color') . ';">
        <th scope="col"  width="5%" align="center">STT</th>
        <th scope="col"  width="25%" align="center">' . _l('Sản phẩm') . '</th>
        <th scope="col"  width="15%" align="center">' . _l('Mã số') . '</th>
        <th scope="col"  width="17%" align="center">' . _l('Số lượng') . '</th>';
$tblhtml .='<th  width="18%" align="center">' . _l('Đơn giá') . '</th>
            <th  width="20%" align="center">' . _l('Thành tiền') . '</th>';
$tblhtml .= '</tr>';
// Items
$tblhtml .= '<tbody>';
$grand_total=0;
for ($i=0; $i < count($invoice->items) ; $i++) { 
    // var_dump($invoice->items[$i]);die();
    $grand_total+=$invoice->items[$i]->sub_total;
    $tblhtml.='<tr>';
    $tblhtml.='<td align="center">'.($i+1).'</td>';
    $tblhtml.='<td>'.$invoice->items[$i]->product_name.'</td>';
    $tblhtml.='<td>'.$invoice->items[$i]->prefix.$invoice->items[$i]->code.'</td>';
    $tblhtml.='<td align="right">'._format_number($invoice->items[$i]->quantity).'</td>';
    $tblhtml.='<td align="right">'.format_money($invoice->items[$i]->unit_cost).'</td>';
    $tblhtml.='<td align="right">'.format_money($invoice->items[$i]->sub_total).'</td>';
    $tblhtml.='</tr>';
}
    $total_fees=$invoice->transport_fee+$invoice->installation_fee;
    $grand_total_plus=$grand_total+$total_fees-$invoice->discount;

    $tblhtml.='<tr>';
    $tblhtml.='<td colspan="4" align="right">'._l('discount').'</td>';
    $tblhtml.='<td colspan="2" align="right">'.format_money($invoice->discount,get_option('default_currency'));
    $tblhtml.='</td>';
    $tblhtml.='</tr>';    

    $tblhtml.='<tr>';
    $tblhtml.='<td colspan="4" align="right">'._l('total_fees').'</td>';
    $tblhtml.='<td colspan="2" align="right">'.format_money($total_fees,get_option('default_currency'));
    $tblhtml.='</td>';
    $tblhtml.='</tr>';

    $tblhtml.='<tr>';
    $tblhtml.='<td colspan="4" align="right">'._l('total_amount').'</td>';
    $tblhtml.='<td colspan="2" align="right">'.format_money($grand_total_plus,get_option('default_currency'));
    $tblhtml.='</td>';
    $tblhtml.='</tr>';

    $tblhtml.='<tr>';
    $tblhtml.='<td colspan="4" align="right">'._l('payment_amount').'</td>';
    $tblhtml.='<td colspan="2" align="right">'.format_money($invoice->payment_amount,get_option('default_currency'));
    $tblhtml.='</td>';
    $tblhtml.='</tr>';

    $tblhtml.='<tr>';
    $tblhtml.='<td colspan="4" align="right">'._l('left_amount').'</td>';
    $tblhtml.='<td colspan="2" align="right">'.format_money($grand_total_plus-$invoice->payment_amount,get_option('default_currency'));
    $tblhtml.='</td>';
    $tblhtml.='</tr>';

$tblhtml .= '</tbody>';
$tblhtml .= '</table>';
$pdf->writeHTML($tblhtml, true, false, false, false, '');

$pdf->Ln(1);
$check_note='<div style="width:100%"><b>'._l('invoice_note').'</b>'._l('check_note').'</div>';
$pdf->writeHTML($check_note, true, false, false, false, '');

$pdf->Ln(1);
$table = "<table style=\"width: 100%;text-align: center\" border=\"0\">
        <tr>
            <td><b>" . mb_ucfirst(_l('buyers'), "UTF-8") . "</b></td>
            <td><b>" . mb_ucfirst(_l('billers'), "UTF-8") . "</b></td>
            <td><b>" . mb_ucfirst(_l('unit_heads'), "UTF-8") . "</b></td>
        </tr>   
        <tr>
            <td>(ký, ghi rõ họ tên)</td>
            <td>(ký, ghi rõ họ tên)</td>
            <td>(ký, ghi rõ họ tên)</td>
        </tr>
        <tr>
            <td style=\"height: 100px\" colspan=\"3\"></td>
        </tr>
        <tr>
            <td>" . mb_ucfirst($customer->company,"UTF-8") . "</td>
            <td>" . mb_ucfirst($invoice->creater,"UTF-8") . "</td>
            <td>" . mb_ucfirst($invoice->admin,"UTF-8") . "</td>
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

