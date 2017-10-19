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
$info_right_column=$info_right_column .= '<a href="' . admin_url('#') . '" style="color:#4e4e4e;text-decoration:none;"><b> ' . date('Y-m-d H:i:s') . '</b></a>';

$invoice_info = '';
    $invoice_info = '<b>' . get_option('invoice_company_name') . '</b><br />';
    $invoice_info .= _l('address') . ': ' . get_option('invoice_company_address') . '<br/>';
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

// $y            = $pdf->getY();
// $pdf->writeHTMLCell((true ? ($dimensions['wk']) - ($dimensions['lm'] * 2) : ($dimensions['wk'] / 2) - $dimensions['lm']), '', '', 20, $invoice_info, 0, 0, false, true, ($swap == '1' ? 'R' : 'J'), true);
// $pdf->ln(23);
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
        <th scope="col"  width="33%" align="center">' . _l('Sản phẩm') . '</th>
        <th scope="col"  width="10%" align="center">' . _l('Mã số') . '</th>        
        <th scope="col"  width="7%" align="center">' . _l('Số lượng') . '</th>';
$tblhtml .='<th  width="15%" align="center">' . _l('Đơn giá') . '</th>
            <th  width="15%" align="center">' . _l('Chiết khấu/ Khuyến mãi') . '</th>
            <th  width="15%" align="center">' . _l('Thành tiền') . '</th>';
$tblhtml .= '</tr>';
// Items
$tblhtml .= '<tbody>';
$grand_total=0;
$grand_discount=0;
$grand_tran_ins=0;
$grand_total_payment=0;
$grand_total_paid=0;
$grand_total_left=0;
for ($i=0; $i < count($invoice->items) ; $i++) {
    $grand_total+=$invoice->items[$i]->sub_total;
    $grand_discount+=$invoice->items[$i]->discount;
    $tblhtml.='<tr>';
    $tblhtml.='<td align="center">'.($i+1).'</td>';
    $tblhtml.='<td>'.$invoice->items[$i]->product_name.'</td>';
    $tblhtml.='<td>'.$invoice->items[$i]->prefix.$invoice->items[$i]->short_name.'</td>';
    $tblhtml.='<td align="center">'._format_number($invoice->items[$i]->quantity).'</td>';
    $tblhtml.='<td align="right">'.format_money($invoice->items[$i]->unit_cost).'</td>';
    $tblhtml.='<td align="right">'.format_money($invoice->items[$i]->discount).'</td>';
    $tblhtml.='<td align="right">'.format_money($invoice->items[$i]->amount).'</td>';
    $tblhtml.='</tr>';
}
    $grand_tran_ins=$invoice->transport_fee+$invoice->installation_fee;
    $grand_total_payment=$grand_total+$grand_tran_ins-$invoice->discount-$grand_discount;
    $grand_total_paid=0;
    $grand_total_left=$grand_total_payment-$grand_total_paid;
$tblhtml .= '</tbody>';
$tblhtml .= '</table>';
$pdf->writeHTML($tblhtml, false, false, false, false, '');
    
    $tblhtml_bottom='<table width="100%" style="float: right">';
    $tblhtml_bottom.='<tr style="border:none !important">';
    $tblhtml_bottom.='<td align="right">'._l('total_amount_money').'</td>';
    $tblhtml_bottom.='<td align="right">'.format_money($grand_total,get_option('default_currency'));
    $tblhtml_bottom.='</td>';
    $tblhtml_bottom.='</tr>';    

    $tblhtml_bottom.='<tr>';
    $tblhtml_bottom.='<td align="right">'._l('total_discount_money').'</td>';
    $tblhtml_bottom.='<td align="right">'.format_money($grand_discount+$invoice->discount,get_option('default_currency'));
    $tblhtml_bottom.='</td>';
    $tblhtml_bottom.='</tr>';

    $tblhtml_bottom.='<tr>';
    $tblhtml_bottom.='<td align="right">'._l('total_transport_installation_money').'</td>';
    $tblhtml_bottom.='<td align="right">'.format_money($grand_tran_ins,get_option('default_currency'));
    $tblhtml_bottom.='</td>';
    $tblhtml_bottom.='</tr>';

    $tblhtml_bottom.='<tr>';
    $tblhtml_bottom.='<td align="right">'._l('total_payment_money').'</td>';
    $tblhtml_bottom.='<td align="right">'.format_money($grand_total_payment,get_option('default_currency'));
    $tblhtml_bottom.='</td>';
    $tblhtml_bottom.='</tr>';

    $tblhtml_bottom.='<tr>';
    $tblhtml_bottom.='<td align="right">'._l('total_paid_money').'</td>';
    $tblhtml_bottom.='<td align="right">'.format_money($grand_total_paid,get_option('default_currency'));
    $tblhtml_bottom.='</td>';
    $tblhtml_bottom.='</tr>';

    $tblhtml_bottom.='<tr>';
    $tblhtml_bottom.='<td align="right">'._l('total_left_amount').'</td>';
    $tblhtml_bottom.='<td align="right">'.format_money($grand_total_left,get_option('default_currency'));
    $tblhtml_bottom.='</td>';
    $tblhtml_bottom.='</tr>';
    $tblhtml_bottom.='</table>';

$pdf->Ln(3);    
$x=$pdf->getX();
$y=$pdf->getY();
$pdf->SetFont($font_name, '', $font_size-1);
$note='<div style="width:100%"><b>'._l('invoice_note').'</b>'.$invoice->reason.'</div>';
$pdf->writeHTML($note, true, false, false, false, '');

$x=$pdf->getX();
$pdf->writeHTMLCell('', '', $x+120, $y, $tblhtml_bottom, 0, 0, false, true, ('R'), true);

$pdf->SetFont($font_name, '', $font_size);
$pdf->Ln(30);
$table = "<table style=\"width: 100%;text-align: center\" border=\"0\">
        <tr>
            <td><b>" . (_l('buyers')) . "</b></td>
            <td><b>" . (_l('billers_')) . "</b></td>
            <td><b>" . (_l('lead_sale_department')) . "</b></td>
        </tr>        
        <tr>
            <td>(ký, ghi rõ họ tên)</td>
            <td>(ký, ghi rõ họ tên)</td>
            <td>(ký, đóng dấu, ghi rõ họ tên)</td>
        </tr>
        <tr>
            <td style=\"height: 100px\" colspan=\"3\"></td>
        </tr>
        <tr>
            <td><i><b>" . mb_ucfirst($customer->company,"UTF-8") . "</b></i></td>
            <td><i><b>" . mb_ucfirst($invoice->creater,"UTF-8") . "</b></i></td>
            <td><i><b>" . mb_ucfirst($invoice->admin,"UTF-8") . "</b></i></td>
        </tr>
</table>";
// var_dump($invoice);die();
$y=$pdf->getY();
$pdf->writeHTMLCell('', '', '', $y, $table, 0, 0, false, true, ('L'), true);

$pdf->SetFont($font_name, '', $font_size-2);
$pdf->Ln(35);
$check_note='<div style="width:100%">'._l('check_note2').'</div>';
$y=$pdf->getY();
$pdf->writeHTMLCell('', '', '', '', $check_note, 0, 0, false, true, ('L'), true);
// $pdf->writeHTML($check_note, true, false, false, false, '');
$pdf->WatermarkText();

