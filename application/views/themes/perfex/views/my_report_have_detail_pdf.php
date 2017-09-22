<?php
$dimensions = $pdf->getPageDimensions();


function mb_ucfirst($string, $encoding)
{
    return mb_convert_case($string, MB_CASE_TITLE, $encoding);
}
$combo=1;
if($report_have->combo)
{
    $combo=$report_have->combo;
}
for($i=0;$i<$combo;$i++) {
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

    $pdf->ln(2);
    $y = $pdf->getY();
    $money = get_table_where('tblreport_have_contract', array('id_report_have' => $report_have->id));
    $total = 0;
    $tk_no = "";
    $tk_co = "";
    foreach ($money as $rom) {
        $tk_no = $tk_no . ', ' . get_code_tk($rom['tk_no']);
        $tk_co = $tk_co . ', ' . get_code_tk($rom['tk_co']);
        $total += $rom['subtotal'];
    }
    $tk_no = "Nợ: " . trim($tk_no, ',');
    $tk_co = "Có: " . trim($tk_co, ',');
    $_debit = "Số: " . $receipts->code_vouchers;
    $count_quyen_so = "Quyển số:";
    $pdf->writeHTMLCell((true ? ($dimensions['wk']) - ($dimensions['lm'] * 2) : ($dimensions['wk'] / 2) - $dimensions['lm']), '', '', $y, $invoice_info, 0, 0, false, true, ($swap == '1' ? 'L' : 'J'), true);
    $pdf->ln(23);


    $y = $pdf->getY();
    $plan_name = _l('als_report_have_');

    $pdf->SetFont($font_name, 'B', 20);
    $pdf->Cell(0, 0, mb_strtoupper($plan_name, 'UTF-8'), 0, 1, 'C', 0, '', 0);
    $pdf->SetFont($font_name, 'I', $font_size);
    $pdf->Ln(5);
    $report_have_contract = get_table_where('tblreport_have_contract', array('id_report_have' => $report_have->id));
    $_data_contract = "";
    foreach ($report_have_contract as $rc) {
        $_data_contract .= '
            <tr>
                <td style="text-align:left;width: 30%;border: 1px black solid">' . $rc['note'] . '</td>
                <td style="text-align:right;width: 30%;border: 1px black solid">' . _format_number($rc['subtotal']) . '</td>
                <td style="text-align:right;width: 20%;border: 1px black solid">' . _format_number($rc['subtotal']) . '</td>
                <td style="text-align:left;width: 10%;border: 1px black solid">' . get_code_tk($rc['tk_no']) . '</td>
                <td style="text-align:left;width: 10%;border: 1px black solid">' . get_code_tk($rc['tk_co']) . '</td>
            </tr>';
    }
    $info_table = '<table style="float: right;border: 1px black solid;line-height:2;" >
        <tr>
            <td style="text-align: left;width: 80%;border: 1px black solid" colspan="4"><br />
                <span>Người nộp tiền: ' . $report_have->receiver . '</span><br />
                <span>Địa chỉ' . $report_have->address . '</span><br />
                <span>Lý do: ' . $report_have->reason . '</span>
            </td>
            <td style="width: 20%;border: 1px black solid;text-align: left"><br />
                <span>Số: ' . $report_have->code_vouchers . '</span><br />
                <span>Ngày: ' . $report_have->date_create . '</span><br />
                <span>' . $tk_no . '</span>
            </td>
        </tr>
        <tr>
            <td style="width: 100%;text-align: left" colspan="5"><br />
                <span>Số tài khoản thụ hưởng: ' . $report_have->account . ' </span>
                <span>Tại ngân hàng: ' . $report_have->name_hank . '</span><br />
                <span>Số tiền: ' . _format_number($report_have->sum_total) . '(VND)</span><br />
                <span>Số tiền bằng chử: ' . $CI->numberword->convert($report_have->sum_total, 'VNĐ') . '</span>
            </td>
        </tr>
        <tr>
            <td style="width: 30%;border: 1px black solid"><b>Diển giải</b></td>
            <td style="width: 30%;border: 1px black solid"><b>Số tiền nguyên tệ(VND)</b></td>
            <td style="width: 20%;border: 1px black solid"><b>Số tiền(VND)</b></td>
            <td style="width: 10%;border: 1px black solid"><b>Ghi nợ</b></td>
            <td style="width: 10%;border: 1px black solid"><b>Ghi có</b></td>
        </tr>
            ' . $_data_contract . '
    </table>';
    $tblhtml = $info_table;
    $pdf->writeHTML($tblhtml, true, false, false, false, 'C');

    $table = "<table style=\"width: 100%;text-align: center\" border=\"0\">
            <tr>
                <td><b>" . mb_ucfirst(_l('director'), "UTF-8") . "</b></td>
                <td><b>" . mb_ucfirst(_l('chief_accountant'), "UTF-8") . "</b></td>
                <td><b>" . mb_ucfirst(_l('treasurer'), "UTF-8") . "</b></td>
                <td><b>" . mb_ucfirst(_l('the_person_making_the_votes'), "UTF-8") . "</b></td>
                <td><b>" . mb_ucfirst(_l('_receiver_money'), "UTF-8") . "</b></td>
            </tr>
            <tr>
                <td>(ký, họ tên, đóng dấu)</td>
                <td>(ký, họ tên)</td>
                <td>(ký, họ tên)</td>
                <td>(ký, họ tên)</td>
                <td>(ký, họ tên)</td>
            </tr>
            <tr>
                <td style=\"height: 100px\" colspan=\"5\"></td>
            </tr>
            <tr>
                <td><i><b></b></i></td>
                <td><i><b></b></i></td>
                <td><i><b></b></i></td>
                <td><i><b>" . mb_ucfirst(get_staff_full_name($report_have->id_staff), "UTF-8") . "</b></i></td>
                <td><i><b>" . mb_ucfirst($report_have->receiver, "UTF-8") . "</b></i></td>
            </tr>
    </table>";
    $pdf->writeHTML($table, true, false, false, false, '');
    $divide = '<hr style="margin-top: 20px;margin-bottom: 20px;border: 0;border-top: 1px solid #eee;" />';
    $pdf->ln(6);
    $y = $pdf->getY();
    $pdf->writeHTMLCell('', '', '', $y, $divide, 0, 0, false, true, ($swap == '1' ? 'R' : 'J'), true);
}


