<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$plan_status=array(
    "1"=>"Đã được xác nhận",
    "0"=>"Chưa được xác nhận chọn để xác nhận"
);

$aColumns     = array(
    'tbldebit.code_vouchers',
    'tbldebit.receiver',
    'tbldebit.id_supplier',
    'tbldebit.date_create',
    'tbldebit.status',
    'tbldebit.reason',
    'tbldebit.staff_browse',
    'tbldebit.id_staff'
);

$sIndexColumn = "id";
$sTable       = 'tbldebit';

$where = array();
$order_by = 'tbldebit.id ASC';
$order_by = '';
$status=$this->_instance->input->post('filterStatus');
if($status)
{
    array_push($where,' AND status='.$status);
}
$join             = array(
);
$additionalSelect = array(
    'tbldebit.id',
    '(select tblstaff.fullname from tblstaff where tblstaff.staffid = tbldebit.id_user_create) as creator',
    '(select tblsuppliers.company from tblsuppliers where tblsuppliers.userid = tbldebit.id_supplier) as name_supplier',
);
$result           = data_tables_init($aColumns, $sIndexColumn, $sTable ,$join, $where, $additionalSelect, $order_by);

$output           = $result['output'];
$rResult          = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = array();
    $approval = false;
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if($aColumns[$i] == "tbldebit.code_vouchers"){
            $_data = '<a href="'.admin_url('debit/debit/').$aRow['id'].'">'.$_data.'</a>';
        }
        if($aColumns[$i] == "tbldebit.id_supplier"){
            $_data = '<a href="'.admin_url('suppliers/supplier').$aRow['tbldebit.id_supplier'].'">'.$aRow['name_supplier'].'</a>';
        }
        if($aColumns[$i] == 'tbldebit.status') {
            if ($aRow['tbldebit.status'] == 0) {
                $type = 'warning';
                $status = 'Chưa duyệt';
            } elseif ($aRow['tbldebit.status'] == 1) {
                $type = 'info';
                $status = 'Đã xác nhận';
            } else {
                $type = 'success';
                $status = 'Đã duyệt';
            }
            $_data = '<span class="inline-block label label-' . $type . '" task-status-table="' . $aRow['tblpurchase_suggested.status'] . '">' . $status . '';
            if (has_permission('invoices', '', 'view') && has_permission('invoices', '', 'view_own')) {
                if ($aRow['tbldebit.status'] != 2) {
                    $_data .= '<a href="javacript:void(0)" onclick="return var_status(' . $aRow['tbldebit.status'] . ',' . $aRow['id'] . ')">
                    <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" title="' . _l($plan_status[$aRow['tbldebit.status']]) . '"></i>
                    ';
                } else {
                    $_data .= '<a href="javacript:void(0)">
                    <i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip" title="' . _l($plan_status[$aRow['tbldebit.status']]) . '"></i>';
                }
            }
        }
        $array_user = ['tbldebit.id_staff','tbldebit.staff_browse'];
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
                      <li><a href="'.admin_url().'debit/pdf/' . $aRow['id'].'?print=true" target="_blank">Liên 1</a></li>
                      <li><a href="'.admin_url().'debit/pdf/' . $aRow['id'].'?print=true&combo=2" target="_blank">Liên 2</a></li>
                    </ul>
                 </div>
                ';
    $mleft30='mleft30';
//    $options .= icon_btn('debit/pdf/'. $aRow['tbldebit.id'] .'?pdf=true' , 'print', 'btn-default', array('target'=>'_blank'));
    $options .= icon_btn('debit/pdf/'. $aRow['id'] .'?pdf=true' , 'file-pdf-o', 'btn-default '.$mleft30, array('target'=>'_blank'));
    $options .= icon_btn('debit/pdf/'. $aRow['id'] , 'download', 'btn-default', array('target'=>'_blank'));
    $row[] = $options;

    $output['aaData'][] = $row;
}
