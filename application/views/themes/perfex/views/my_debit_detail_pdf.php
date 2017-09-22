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
$combo=1;
if($debit->combo)
{
    $combo=$debit->combo;
}
for($i=0;$i<$combo;$i++) {
    $pdf_text_color_array = hex2rgb(get_option('pdf_text_color'));
    if (is_array($pdf_text_color_array) && count($pdf_text_color_array) == 3) {
        $pdf->SetTextColor($pdf_text_color_array[0], $pdf_text_color_array[1], $pdf_text_color_array[2]);
    }

    $info_right_column = '';
    $info_left_column = '';
    $info_right_column = $info_right_column .= '<a href="' . admin_url('#') . '" style="color:#4e4e4e;text-decoration:none;"><b> ' . date('Y-m-d H:i:s') . '</b></a>';

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
    $info_left_column .= pdf_logo_url();


    $pdf->ln(2);
    $y = $pdf->getY();
    $money = get_table_where('tbldebit_contract', array('id_debit' => $debit->id));
    $total = 0;
    $tk_no = "";
    $tk_co = "";
    foreach ($money as $rom) {
        $tk_no = $tk_no . ',' . get_code_tk($rom['tk_no']);
        $tk_co = $tk_co . ',' . get_code_tk($rom['tk_co']);
        $total += $rom['total'];
    }
    $tk_no = "Nợ: " . trim($tk_no, ',');
    $tk_co = "Có: " . trim($tk_co, ',');
    $_debit = "Số: " . $debit->code_vouchers;
    $count_quyen_so = "Quyển số:";
    $info_right = '<table style="float: right" >
        <tr>
            <td style="width: 70%" align="right"></td>
            <td style="width: 30%" align="left">' . $count_quyen_so . '</td>
        </tr>
         <tr>
            <td style="width: 70%" align="right"></td>
            <td style="width: 30%" align="left">' . $_debit . '</td>
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
    $pdf->writeHTMLCell((true ? ($dimensions['wk']) - ($dimensions['lm'] * 2) : ($dimensions['wk'] / 2) - $dimensions['lm']), '', '', $y, $invoice_info, 0, 0, false, true, ($swap == '1' ? 'L' : 'J'), true);
    $pdf->writeHTMLCell(200, '', '', $y, $info_right, 0, 0, false, true, ('R'), true);
    $pdf->ln(18);


    $y = $pdf->getY();
    $pdf->ln(5);
    $plan_name = _l('debit');

    $pdf->SetFont($font_name, 'B', 20);
    $pdf->Cell(0, 0, mb_strtoupper($plan_name, 'UTF-8'), 0, 1, 'C', 0, '', 0);
    $pdf->SetFont($font_name, 'I', $font_size);

    $day = date('d', strtotime($debit->day_vouchers));
    $month = date('m', strtotime($debit->day_vouchers));
    $year = date('Y', strtotime($debit->day_vouchers));
    $pdf->Cell(0, 0, _l('_day') . ' ' . $day . ' ' . _l('_month') . ' ' . $month . ' ' . _l('_year') . ' ' . $year, 0, 1, 'C', 0, '', 0);
    $pdf->ln(2);
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Cell(0, 0, _l('receiver') . ': ' . mb_strtoupper($debit->receiver, 'UTF-8'), 0, 1, 'L', 0, '', 0);
    $pdf->ln(2);

    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Cell(0, 0, _l('address') . ': ' . mb_strtoupper($debit->address, 'UTF-8'), 0, 1, 'L', 0, '', 0);
    $pdf->ln(2);
    $pdf->SetFont($font_name, '', $font_size);

    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Cell(0, 0, _l('reason') . ': ' . mb_strtoupper($debit->reason, 'UTF-8'), 0, 1, 'L', 0, '', 0);
    $pdf->ln(2);

    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Cell(0, 0, _l('money') . ': ' . _format_number($total), 0, 1, 'L', 0, '', 0);
    $pdf->ln(2);
    $pdf->Cell(0, 0, _l('_money_') . ': ' . $CI->numberword->convert($total, 'VNĐ'), 0, 1, 'L', 0, '', 0);
    $pdf->ln(2);

    $pdf->Cell(0, 0, _l('_attach') . '..................... ' . _l('_documents'), 0, 1, 'L', 0, '', 0);
    $pdf->ln(2);

    // The Table
    $pdf->Ln(3);
    $tblhtml = '';
    $pdf->writeHTML($tblhtml, true, false, false, false, '');

    $table = "<table style=\"width: 100%;text-align: center\" border=\"0\">
            <tr>
                <td><b>" . mb_ucfirst(_l('director'), "UTF-8") . "</b></td>
                <td><b>" . mb_ucfirst(_l('chief_accountant'), "UTF-8") . "</b></td>
                <td><b>" . mb_ucfirst(_l('treasurer'), "UTF-8") . "</b></td>
                <td><b>" . mb_ucfirst(_l('the_person_making_the_votes'), "UTF-8") . "</b></td>
                <td><b>" . mb_ucfirst(_l('money_receiver'), "UTF-8") . "</b></td>
            </tr>
            <tr>
                <td>(ký, họ tên, đóng dấu)</td>
                <td>(ký, họ tên)</td>
                <td>(ký, họ tên)</td>
                <td>(ký, họ tên)</td>
                <td>(ký, họ tên)</td>
            </tr>
            <tr>
                <td style=\"height: 50px\" colspan=\"5\"></td>
            </tr>
            <tr>
                <td><i><b></b></i></td>
                <td><i><b></b></i></td>
                <td><i><b></b></i></td>
                <td><i><b>" . mb_ucfirst(get_staff_full_name($debit->id_staff), "UTF-8") . "</b></i></td>
                <td><i><b>" . mb_ucfirst($debit->receiver, "UTF-8") . "</b></i></td>
            </tr>
    </table>";
    $pdf->writeHTML($table, true, false, false, false, '');
    $divide = '<hr style="margin-top: 20px;margin-bottom: 20px;border: 0;border-top: 1px solid #eee;" />';
    $pdf->ln(6);
    $y = $pdf->getY();
    $pdf->writeHTMLCell('', '', '', $y, $divide, 0, 0, false, true, ($swap == '1' ? 'R' : 'J'), true);
}


