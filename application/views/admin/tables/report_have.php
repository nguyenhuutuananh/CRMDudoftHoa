<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$plan_status=array(
    "1"=>"Đã được xác nhận",
    "0"=>"Chưa được xác nhận chọn để xác nhận"
);

$aColumns     = array(
    'tblreport_have.code_vouchers',
    'tblreport_have.receiver',
    'tblreport_have.date_create',
    'tblreport_have.status',
    'tblreport_have.reason',
    'tblreport_have.staff_browse',
    'tblreport_have.id_staff'
);

$sIndexColumn = "id";
$sTable       = 'tblreport_have';

$where = array();
$order_by = 'tblreport_have.id ASC';
$order_by = '';
$status=$this->_instance->input->post('filterStatus');
if($status!="")
{
    array_push($where,' AND status='.$status);
}
$join             = array(
);
$additionalSelect = array(
    'tblreport_have.id',
    '(select tblstaff.fullname from tblstaff where tblstaff.staffid = tblreport_have.id_user_create) as creator',
);
$result           = data_tables_init($aColumns, $sIndexColumn, $sTable ,$join, $where, $additionalSelect, $order_by);
//var_dump($result);die();
$output           = $result['output'];
$rResult          = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = array();
    $approval = false;
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if($aColumns[$i] == "tblreport_have.code_vouchers"){
            $_data = '<a href="'.admin_url('report_have/report_have/').$aRow['id'].'">'.$_data.'</a>';
        }
        if($aColumns[$i] == 'tblreport_have.status') {
            if ($aRow['tblreport_have.status'] == 0) {
                $type = 'warning';
                $status = 'Chưa duyệt';
            } elseif ($aRow['tblreport_have.status'] == 1) {
                $type = 'info';
                $status = 'Đã xác nhận';
            } else {
                $type = 'success';
                $status = 'Đã duyệt';
            }
            $_data = '<span class="inline-block label label-' . $type . '" task-status-table="' . $aRow['tblreport_have.status'] . '">' . $status . '';
            if (has_permission('invoices', '', 'view') && has_permission('invoices', '', 'view_own')) {
                if ($aRow['tblreport_have.status'] != 2) {
                    $_data .= '<a href="javacript:void(0)" onclick="return var_status(' . $aRow['tblreport_have.status'] . ',' . $aRow['id'] . ')">
                    <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" title="' . _l($plan_status[$aRow['tblreport_have.status']]) . '"></i>
                    ';
                } else {
                    $_data .= '<a href="javacript:void(0)">
                    <i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip" title="' . _l($plan_status[$aRow['tblreport_have.status']]) . '"></i>';
                }
            }
        }
        $array_user = ['tblreport_have.id_staff','tblreport_have.staff_browse'];
        if(in_array($aColumns[$i],$array_user)) {
            if($_data != '0') {
                $_data = '<a href="' . admin_url('profile/' . $_data) . '">' . staff_profile_image($_data, array(
                        'staff-profile-image-small mright5'
                    ), 'small', array(
                        'data-toggle' => 'tooltip',
                        'data-title' => $aRow["creator"],
                    )) . '</a>';
            }
            else {
                $_data = "";
            }
        }
        $row[] = $_data;
    }
    $options = '';
    $options.='<div class="dropdown" style="position: absolute;">
                    <a class="dropdown-toggle btn btn-default btn-icon" data-toggle="dropdown"><i class="fa fa-print"></i></a>
                    <ul class="dropdown-menu">
                      <li class="dropdown-header">LIÊN</li>
                      <li><a href="'.admin_url().'report_have/pdf/' . $aRow['tblreport_have.id'].'?print=true" target="_blank">Liên 1</a></li>
                      <li><a href="#">Liên 2</a></li>
                      <li><a href="#">Liên 3</a></li>
                    </ul>
                 </div>
                ';
    $mleft30='mleft30';

//    $options .= icon_btn('report_have/pdf/'. $aRow['tblreport_have.id'] .'?print=true' , 'print', 'btn-default', array('target'=>'_blank'));
    $options .= icon_btn('report_have/pdf/'. $aRow['tblreport_have.id'] .'?pdf=true' , 'file-pdf-o', 'btn-default '.$mleft30, array('target'=>'_blank'));
    $options .= icon_btn('report_have/pdf/'. $aRow['tblreport_have.id'] , 'download', 'btn-default', array('target'=>'_blank'));
    $row[] = $options;

    $output['aaData'][] = $row;
}
