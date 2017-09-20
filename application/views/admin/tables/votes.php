<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$plan_status=array(
    "1"=>"Đã được xác nhận",
    "0"=>"Chưa được xác nhận chọn để xác nhận"
);

$aColumns     = array(
    'tblvotes.code_vouchers',
    'tblvotes.receiver',
    'tblvotes.id_supplier',
    'tblvotes.date_create',
    'tblvotes.status',
    '6',
    'tblvotes.reason',
    'tblvotes.staff_browse',
    'tblvotes.id_staff'
);

$sIndexColumn = "id";
$sTable       = 'tblvotes';

$where = array();
$order_by = 'tblvotes.id ASC';
$order_by = '';
$status=$this->_instance->input->post('filterStatus');
if($status)
{
    array_push($where,' AND status='.$status);
}
$join             = array(
);
$additionalSelect = array(
    'tblvotes.id',
    '(select tblstaff.fullname from tblstaff where tblstaff.staffid = tblvotes.id_user_create) as creator',
    '(select tblsuppliers.company from tblsuppliers where tblsuppliers.userid = tblvotes.id_supplier) as name_supplier',
);
$result           = data_tables_init($aColumns, $sIndexColumn, $sTable ,$join, $where, $additionalSelect, $order_by);

$output           = $result['output'];
$rResult          = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = array();
    $approval = false;
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if($aColumns[$i] == "tblvotes.code_vouchers"){
            $_data = '<a href="'.admin_url('votes/votes/').$aRow['id'].'">'.$_data.'</a>';
        }
        if($aColumns[$i] == "tblvotes.id_supplier"){
            $_data = '<a href="'.admin_url('suppliers/supplier').$aRow['tblvotes.id_supplier'].'">'.$aRow['name_supplier'].'</a>';
        }
        if($aColumns[$i] == "6"){
            $_total= get_table_where('tblvotes_contract','id_votes='.$aRow['id']);
            if($_total!=array())
            {
                $total=0;
                foreach($_total as $r)
                {
                    $total+=$r['total'];
                }
                $_data=_format_number($total);
            }
            else
            {
                $_data="";
            }
        }
        if($aColumns[$i] == 'tblvotes.status') {
            if ($aRow['tblvotes.status'] == 0) {
                $type = 'warning';
                $status = 'Chưa duyệt';
            } elseif ($aRow['tblvotes.status'] == 1) {
                $type = 'info';
                $status = 'Đã xác nhận';
            } else {
                $type = 'success';
                $status = 'Đã duyệt';
            }
            $_data = '<span class="inline-block label label-' . $type . '" task-status-table="' . $aRow['tblpurchase_suggested.status'] . '">' . $status . '';
            if (has_permission('invoices', '', 'view') && has_permission('invoices', '', 'view_own')) {
                if ($aRow['tblvotes.status'] != 2) {
                    $_data .= '<a href="javacript:void(0)" onclick="return var_status(' . $aRow['tblvotes.status'] . ',' . $aRow['id'] . ')">
                    <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" title="' . _l($plan_status[$aRow['tblvotes.status']]) . '"></i>
                    ';
                } else {
                    $_data .= '<a href="javacript:void(0)">
                    <i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip" title="' . _l($plan_status[$aRow['tblvotes.status']]) . '"></i>';
                }
            }
        }
        $array_user = ['tblvotes.id_staff','tblvotes.staff_browse'];
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
    $options .= icon_btn('votes/pdf/'. $aRow['tblvotes.id'] .'?pdf=true' , 'file-pdf-o', 'btn-default', array('target'=>'_blank'));
    $options .= icon_btn('votes/pdf/'. $aRow['tblvotes.id'] .'?print=true' , 'print', 'btn-default', array('target'=>'_blank'));
    $options .= icon_btn('votes/pdf/'. $aRow['tblvotes.id'] , 'download', 'btn-default', array('target'=>'_blank'));
    $row[] = $options;

    $output['aaData'][] = $row;
}
