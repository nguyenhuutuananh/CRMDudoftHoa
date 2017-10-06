<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$plan_status=array(
    "1"=>"Đã được xác nhận",
    "0"=>"Chưa được xác nhận chọn để xác nhận"
);

$aColumns     = array(
    'tblcampaign.name',
    'tblcampaign.staff_manage',
    '3',
    'tblcampaign.expense',
    'tblcampaign.create_by',
    'tblcampaign.start_data',
    'tblcampaign.end_date',
);

$sIndexColumn = "id";
$sTable       = 'tblcampaign';

$where = array();
$order_by = 'tblcampaign.id ASC';
$order_by = '';
$status=$this->_instance->input->post('filterStatus');
$join             = array(

);
$additionalSelect = array(
    'tblcampaign.id',
    '(select tblstaff.fullname from tblstaff where tblstaff.staffid = tblcampaign.create_by) as creator'
);
$result           = data_tables_init($aColumns, $sIndexColumn, $sTable ,$join, $where, $additionalSelect, $order_by);

$output           = $result['output'];
$rResult          = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = array();
    $approval = false;
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if($aColumns[$i] == "tblcampaign.name"){
            $_data = '<a href="'.admin_url('campaign/campaign/').$aRow['id'].'">'.$_data.'</a>';
        }
        if($aColumns[$i]=='tblcampaign.create_by') {
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
        if($aColumns[$i]=='tblcampaign.expense')
        {
            $_data=_format_number($aRow['tblcampaign.expense']);
        }
        if($aColumns[$i]=='tblcampaign.staff_manage') {
            if($_data != '0') {
                $_data = '<a href="' . admin_url('profile/' . $_data) . '">' . staff_profile_image($_data, array(
                        'staff-profile-image-small mright5'
                    ), 'small', array(
                        'data-toggle' => 'tooltip',
                        'data-title' => get_staff_full_name($aRow["tblcampaign.staff_manage"]),
                    )) . '</a>';
            }
            else {
                $_data = "";
            }
        }
        if($aColumns[$i]=='3') {
            $staff_campaign=get_table_where('tblcampaign_staff',array('id_campaign'=>$aRow['id']));
            $_data="";
            if($staff_campaign!=array()){
                foreach($staff_campaign as $s)
                {
                    $_data .= '<a href="' . admin_url('profile/' . $s['id_staff']) . '">' . staff_profile_image($s['id_staff'], array(
                            'staff-profile-image-small mright5'
                        ), 'small', array(
                            'data-toggle' => 'tooltip',
                            'data-title' => get_staff_full_name($s['id_staff']),
                        )) . '</a>';
                }
            }
        }
        $row[] = $_data;
    }
    $options = '';
    $options.='<a href="'.admin_url().'campaign/campaign/'.$aRow['id'].'" class="btn btn-default btn-icon">
                    <i class="fa fa-pencil-square-o"></i>
              </a>';
    $options.='<a onclick="get_client_campaign('.$aRow['id'].')" data-toggle="modal" title="Send email" data-original-title="Send email" data-placement="left" data-target="#send_email-campaign" class="btn btn-success btn-icon">
                    <i class="glyphicon glyphicon-envelope"></i>
              </a>';
    $options.='<a href="'.admin_url().'campaign/delete/'.$aRow['id'].'" class="btn btn-danger _delete btn-icon" data-toggle="tooltip" data-placement="left" title="'._l('_tb_delete_campaign').'" data-original-title="'._l('_tb_delete_campaign').'">
            <i class="fa fa-remove"></i>
            </a>';
    $row[] = $options;

    $output['aaData'][] = $row;
}
