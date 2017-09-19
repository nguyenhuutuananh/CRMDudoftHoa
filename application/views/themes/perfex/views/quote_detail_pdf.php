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


if (is_array($pdf_text_color_array) && count($pdf_text_color_array) == 3) {
    $pdf->SetTextColor($pdf_text_color_array[0], $pdf_text_color_array[1], $pdf_text_color_array[2]);
}

$info_right_column = '';
$info_left_column  = '';

$info_right_column=get_option('invoice_company_city').', '._l('date_',date('d'))._l('month_',date('m'))._l('date_',date('Y'));

$invoice_info='';
$invoice_info = '<b>' . get_option('invoice_company_name') . '</b><br />';
$invoice_info .= _l('address').': '.get_option('invoice_company_address') . '<br/>';

if(get_option('company_vat') != ''){
    $invoice_info .= _l('vat_no').': '.get_option('company_vat').'<br/>';
}
$invoice_info .= get_option('invoice_company_country_code') . ' ';
$invoice_info .= get_option('invoice_company_postal_code') . ' ';

if (get_option('invoice_company_phonenumber') != '') {
    $invoice_info .= _l('Tel').': '.get_option('invoice_company_phonenumber').'  ';
}
if (get_option('invoice_company_faxnumber') != '') {
    $invoice_info .= _l('Fax').': '.get_option('invoice_company_faxnumber').'  ';
}
if (get_option('main_domain') != '') {
    $invoice_info .= _l('Website').': '.get_option('main_domain');
}



// write the first column
$info_left_column .= pdf_logo_url();

$pdf->MultiCell(($dimensions['wk'] / 2) - $dimensions['lm'], 0, $info_left_column, 0, 'J', 0, 0, '', '', true, 0, true, true, 0);
// write the second column
$pdf->MultiCell(($dimensions['wk'] / 2) - $dimensions['rm'], 0, $info_right_column, 0, 'R', 0, 1, '', '', true, 0, true, false, 0);
// $pdf->MultiCell(0, 0, $invoice_info, 0, 'C', 0, 1, '', '', true, 0, true, false, 0);
// $y            = $pdf->getY();
$divide=_l('divider');
$pdf->ln(6);
$y            = $pdf->getY();
$pdf->writeHTMLCell('', '', '', $y, $divide, 0, 0, false, true, ($swap == '1' ? 'R' : 'J'), true);
$pdf->ln(1);
$y            = $pdf->getY();
$pdf->writeHTMLCell((true ? ($dimensions['wk']) - ($dimensions['lm'] * 2) : ($dimensions['wk'] / 2) - $dimensions['lm']), '', '', $y, $invoice_info, 0, 0, false, true, ($swap == '1' ? 'R' : 'J'), true);
$pdf->ln(20);
// Set Head
$plan_name=_l('als_quotation');

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




// Get Y position for the separation
// $y            = $pdf->getY();


//Set detail
$pdf->SetFont($font_name, '', $font_size);
// var_dump($customer->client_type);die();
if($customer->client_type==1)
{
    $pdf->writeHTMLCell(0, '', '', '', _l('dear').'<b>'.'<i>'.$customer->title.'</i>: '.$invoice->customer_name.'</b>', 0, 1, false, true, 'L', true);
}
else 
{
    $pdf->writeHTMLCell(0, '', '', '', _l('dear').'<b>'.'<i>'.$customer->contact_title.'</i>: '.$customer->primary_contact.'<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$invoice->customer_name.'</b>', 0, 1, false, true, 'L', true);
}
$pdf->writeHTMLCell(0, '', '', '', _l('address').': '.getClient($invoice->customer_id,1), 0, 1, false, true, 'L', true);

$pdf->ln(2);

$pdf->SetFont($font_name, '', $font_size);
$pdf->writeHTMLCell(0, '', '', '', '<div style="padding-left: 100px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'._l('first_1st').'</div>', 0, 1, false, true, 'L', true);
$pdf->ln(2);


// The Table
$pdf->Ln(5);
$tblhtml = '
<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1px">
    <tr height="30" bgcolor="' . get_option('pdf_table_heading_color') . '" style="color:' . get_option('pdf_table_heading_text_color') . ';">
        <th scope="col"  width="5%" align="center" valign="middle">STT</th>
        <th scope="col"  width="15%" align="center" valign="middle">' . _l('Danh mục') . '</th>
        <th scope="col"  width="12%" align="center" valign="middle">' . _l('Sản phẩm') . '</th>
        <th scope="col"  width="14%" align="center" valign="middle">' . _l('Chức năng/Đặc tính/Quy cách') . '</th>
        <th scope="col"  width="8%" align="center" valign="middle">' . _l('Hình ảnh') . '</th>
        <th scope="col"  width="10%" align="center" valign="middle">' . _l('Kích thước') . '</th>
        <th scope="col" width="5%" align="center" valign="middle">' . _l('Số lượng').'</th>
        <th scope="col" width="5%" align="center" valign="middle">' . _l('Đơn vị tính'). '</th>
        <th scope="col" width="12%" align="center" valign="middle">' . _l('Đơn giá').' ('.get_option('default_currency').')' . '</th>
        <th scope="col" width="14%" align="center" valign="middle">' . _l('Giá trị').' ('.get_option('default_currency').')' . '</th>';
$tblhtml .= '</tr>';
// Items


$tblhtml .= '<tbody>';
$grand_total=0;
$quantity_total=0;
for ($i=0; $i < count($invoice->items) ; $i++) { 
    $grand_total+=$invoice->items[$i]->amount;
    $quantity_total+=$invoice->items[$i]->quantity;
    
// var_dump($invoice->items[$i]);die();
    $img='';
    if($invoice->items[$i]->image)
    {
        $img=FCPATH .$invoice->items[$i]->image;
    }
    $tblhtml.='<tr>';
    $tblhtml.='<td align="center">'.($i+1).'</td>';
    $tblhtml.='<td>'.$invoice->items[$i]->category.'</td>';
    $tblhtml.='<td align="center">'.$invoice->items[$i]->short_name.'</td>';
    $tblhtml.='<td align="right" nowrap>'.$invoice->items[$i]->product_features.'</td>';
    $tblhtml.='<td align="right" >'.'<img width="80px" src="' .$img.'" style="padding: 5px">'.'</td>';
    $tblhtml.='<td align="right">'.$invoice->items[$i]->size.'</td>';
    $tblhtml.='<td align="right">'._format_number($invoice->items[$i]->quantity).'</td>';
    $tblhtml.='<td align="right">'.$invoice->items[$i]->unit_name.'</td>';
    $tblhtml.='<td align="right">'.format_money($invoice->items[$i]->unit_cost).'</td>';
    $tblhtml.='<td align="right">'.format_money($invoice->items[$i]->amount).'</td>';
    $tblhtml.='</tr>';
}
    $tblhtml.='<tr>';
    $tblhtml.='<td colspan="6" align="center"><b>'.mb_strtoupper(_l('total'),'UTF-8').'</b></td>';
    $tblhtml.='<td align="right"><b>'._format_number($quantity_total).'</b></td>';
    $tblhtml.='<td align="right"></td>';
    $tblhtml.='<td align="right"></td>';
    $tblhtml.='<td align="right"><b>'.format_money($grand_total,get_option('default_currency')).'</b></td>';
    $tblhtml.='</tr>';

$tblhtml .= '</tbody>';
$tblhtml .= '</table>';
$pdf->writeHTML($tblhtml, true, false, false, false, '');



if (get_option('total_to_words_enabled') == 1) {
    // Set the font again to normal like the rest of the pdf
    $pdf->SetFont($font_name, '', $font_size);
    $strmoney='<ul>';
    $strmoney.='<li>'._l('str_money').'<i>'.$CI->numberword->convert($grand_total, get_option('default_currency')).'</i>'.'</li>';
    $strmoney.='<li>'._l('certificate_root')._l('blank___').'</li>';
    $strmoney.='</ul>';
    $pdf->writeHTMLCell(0, '', '', '', $strmoney, 0, 1, false, true, 'L', true);
}

$pdf->ln(5);
$pdf->SetFont($font_name, '', $font_size);
$pdf->writeHTMLCell(0, '', '', '', '<b>'.mb_strtoupper(_l('sumary_note').': </b>', 'UTF-8').(($invoice->reason)? $invoice->reason : _l('sumary_detail_html')), 0, 1, false, true, 'L', true);
$pdf->ln(5);

$department=$invoice->department;
if($invoice->create_by==1)
{
    $department=_l('directer_department');
    $strshortD='NV';
    foreach (explode(' ',mb_ucfirst($department,"UTF-8")) as $key => $value) {
        $strshortD.=mb_substr($value,0,1,'utf-8');
    }
}


$table = "<table style=\"width: 100%;text-align: center\" border=\"0\">
        <tr>
            <td>" . mb_ucfirst('', "UTF-8") . "</td>
            <td><b>" . mb_strtoupper(get_option('invoice_company_name'), "UTF-8") . "</b></td>
        </tr>
        <tr  style=\"margin-bottom:200px\">
            <td>" . mb_ucfirst('', "UTF-8") . "</td>
            <td><i>" . mb_ucfirst($department, "UTF-8") . "</i></td>
        </tr>
        <tr>
            <td style=\"height: 100px\" colspan=\"3\"></td>
        </tr>
        <tr>
            <td>" . mb_ucfirst('', "UTF-8") . "</td>
            <td>". $strshortD.': '.mb_ucfirst($invoice->creater, "UTF-8") ."</td>
        </tr>
        
</table>";
$pdf->writeHTML($table, true, false, false, false, '');



