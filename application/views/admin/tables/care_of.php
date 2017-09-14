<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$plan_status=array(
    "1"=>"Đã được xác nhận",
    "0"=>"Chưa được xác nhận chọn để xác nhận"
);

$aColumns     = array(
    'create_by',
    'start_date',
    'note',
);

$sIndexColumn = "id";
$sTable       = 'tblcare_of';

$where = array();
$order_by = 'id ASC';
$order_by = '';
$join             = array(
);
//array_push($where,'AND client='.$client);
$additionalSelect = array(
    'id',
    'client',
);
$result           = data_tables_init($aColumns, $sIndexColumn, $sTable ,$join, $where, $additionalSelect, $order_by);

$output           = $result['output'];
$rResult          = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = array();
    $approval = false;
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == "create_by") {
            if ($_data != '0') {
                $_data = '<a href="' . admin_url('profile/' . $_data) . '">' . staff_profile_image($_data, array(
                        'staff-profile-image-small mright5'
                    ), 'small', array(
                        'data-toggle' => 'tooltip',
                        'data-title' => get_staff_full_name($_data),
                    )) . '</a>';
            } else {
                $_data = "";
            }
        }
        if($aColumns[$i]=='note')
        {
            $_data= '<div class="_note_'.$aRow['id'].'">'.$aRow['note'].'</div>'.'<div class="form-group">
                        <textarea class="note_'.$aRow['id'].' form-control" style="display: none!important;" name="note" onchange="upadte_note('.$aRow['id'].',this.value)"  rows="4">'.$aRow['note'].'</textarea>
                    </div>';
        }
        if($aColumns[$i]=='start_date')
        {
            $_data= '<div class="_start_date_'.$aRow['id'].'">'.$aRow['start_date'].'</div>'.
                    '<div class="form-group start_date__'.$aRow['id'].'" style="display: none!important;">
                           <div class="input-group date">
                                <input type="text"  name="start_date_'.$aRow['id'].'"  class="form-control datepicker start_date_'.$aRow['id'].'" onchange="upadte_date('.$aRow['id'].',this.value)" value="'.$aRow['start_date'].'" aria-invalid="false">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar calendar-icon"></i>
                                    </div>
                           </div>
                    </div>';
        }
        $row[]=$_data;
    }

    $options = '<a  type="button" onclick="get_data_care_of('.$aRow['id'].')" class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i></a>';
    $options .= '<a  type="button" onclick="delete_care_of('.$aRow['id'].')" class="btn btn-danger btn-icon"><i class="fa fa-remove"></i></a>';
    $row[] = $options;

    $output['aaData'][] = $row;
}
