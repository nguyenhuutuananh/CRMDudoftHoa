<?php
$dimensions = $pdf->getPageDimensions();


function mb_ucfirst($string, $encoding)
{
    return mb_convert_case($string, MB_CASE_TITLE, $encoding);
}
// $pdf->WatermarkText();
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



// $pdf->setPage( 1 );
// // Get the page width/height
// $myPageWidth = $pdf->getPageWidth();
// $myPageHeight = $pdf->getPageHeight();
// // Find the middle of the page and adjust.
// $myX = ( $myPageWidth / 2 ) - 75;
// $myY = ( $myPageHeight / 2 ) + 25;
// // Set the transparency of the text to really light
// $pdf->SetAlpha(0.09);
// // Rotate 45 degrees and write the watermarking text
// $pdf->StartTransform();
// $pdf->Rotate(45, $myX, $myY);
// $pdf->SetFont($font_name, "", 30);
// $pdf->Text($myX, $myY,"PROPERTY OF LOCALHOST CORPORATION"); 
// $pdf->StopTransform();
// // Reset the transparency to default
// $pdf->SetAlpha(1);

$pdf->SetFont($font_name, '', $font_size);
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
$grand_total_tax=0;
$grand_total_discount=0;
for ($i=0; $i < count($invoice->items) ; $i++) {

     $unit_cost=$invoice->items[$i]->unit_cost;
     $sub_total=$invoice->items[$i]->sub_total;

     if($invoice->rel_type=='contract') 
        {
            $unit_cost=$invoice->items[$i]->exchange_rate*$invoice->items[$i]->unit_cost;
            $sub_total=$unit_cost*$invoice->items[$i]->quantity_net;
            $tax=$sub_total*$invoice->items[$i]->tax_rate/100;
            $discount=$sub_total*$invoice->items[$i]->discount_percent/100;
            // var_dump($invoice->items[$i]->discount_percent);die;
            $grand_total_tax+=$tax;
            $grand_total_discount+=$discount;
        }
    $rowspan=count($invoice->items);
    $grand_total+=$sub_total;
    $tblhtml.='<tr>';
    $tblhtml.='<td align="center">'.($i+1).'</td>';
    $tblhtml.='<td>'.$invoice->items[$i]->product_name.'</td>';
    $tblhtml.='<td>'.$invoice->items[$i]->prefix.$invoice->items[$i]->code.'</td>';
    $tblhtml.='<td align="right">'.$invoice->items[$i]->unit_name.'</td>';
    $tblhtml.='<td align="right">'._format_number($invoice->items[$i]->quantity).'</td>';    
    $tblhtml.='<td align="right">'._format_number($invoice->items[$i]->quantity_net).'</td>';
    $tblhtml.='<td align="right">'.format_money($unit_cost).'</td>';
    $tblhtml.='<td align="right">'.format_money($invoice->items[$i]->sub_total).'</td>';
    $tblhtml.='<td  rowspan="'.$rowspan.'" align="left">'.$invoice->reason.'</td>';
    $tblhtml.='</tr>';

}
    $relsult=$grand_total+$grand_total_tax-$grand_total_discount;
    if($invoice->rel_type='contract'){
    $tblhtml.='<tr>';
    // $tblhtml.='<td align="right"></td>';
    $tblhtml.='<td colspan="6" align="right"><b>'._l('Tổng tiền').'</b></td>';
    $tblhtml.='<td colspan="2" align="right">'.format_money($grand_total).'</td>';
    $tblhtml.='</tr>';

    $tblhtml.='<tr>';
    // $tblhtml.='<td align="right"></td>';
    $tblhtml.='<td colspan="6" align="right"><b>'._l('Tổng thuế').'</b></td>';
    $tblhtml.='<td colspan="2" align="right">'.format_money($grand_total_tax).'</td>';
    $tblhtml.='</tr>';

    $tblhtml.='<tr>';
    // $tblhtml.='<td align="right"></td>';
    $tblhtml.='<td colspan="6" align="right"><b>'._l('Tổng chiết khấu').'</b></td>';
    $tblhtml.='<td colspan="2" align="right">'.format_money($grand_total_discount).'</td>';
    $tblhtml.='</tr>';
    }

    $tblhtml.='<tr>';
    // $tblhtml.='<td align="right"></td>';
    $tblhtml.='<td colspan="6" align="right"><b>'._l('Tổng giá trị').'</b></td>';
    $tblhtml.='<td colspan="2" align="right">'.format_money($relsult).'</td>';
    $tblhtml.='</tr>';

$tblhtml .= '</tbody>';
$tblhtml .= '</table>';
$pdf->writeHTML($tblhtml, true, false, false, false, '');
$certificate_root=_l('certificate_root').($contract->code?_l('blank10').$contract->code:_l('blank___'));
$strmoney='<ul>';
$strmoney.='<li>'._l('str_money').'<i>'.$CI->numberword->convert($relsult,get_option('default_currency')).'</i>'.'</li>';
$strmoney.='<li>'.$certificate_root.'</li>';;
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

$pdf->WatermarkText();
