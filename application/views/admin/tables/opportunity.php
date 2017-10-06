<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$plan_status=array(
    "1"=>"Đã được xác nhận",
    "0"=>"Chưa được xác nhận chọn để xác nhận"
);

if(!$client)
{
    $aColumns     = array(
        'campaign',
        'client',
        'contact',
        'create_by',
        'performance',
        'staff_in',
        'expected',
        'end_date',
        '8',
        'step'
    );
}
else
{
    $aColumns     = array(
        'campaign',
        'contact',
        'performance',
        'staff_in',
        'expected',
        'end_date',
        '8',
        'step'
    );
}

$sIndexColumn = "id";
$sTable       = 'tblopportunity';

$where = array();
$order_by = 'tblopportunity.id ASC';
$order_by = '';
$status=$this->_instance->input->post('filterStatus');
if($client)
{
    array_push($where,' AND client='.$client);
}
$join             = array(
);


array_push($join, 'LEFT JOIN tblclients ON tblclients.userid=tblopportunity.client');
array_push($join, 'LEFT JOIN tblcampaign_step ON tblcampaign_step.id=step');

$additionalSelect = array(
    'tblopportunity.id',
    '(select tblcampaign.name from tblcampaign where tblcampaign.id = tblopportunity.campaign) as name_campaign',
    '(select tblcampaign.id from tblcampaign where tblcampaign.id = tblopportunity.campaign) as id_campaign',
    'tblclients.company',
    'userid',
    'tblcampaign_step.name',
    '(select tblstaff.fullname from tblstaff where tblstaff.staffid = tblopportunity.create_by) as creator',
    '(select tblstaff.fullname from tblstaff where tblstaff.staffid = tblopportunity.staff_in) as _staff_in',
    '(select CONCAT(tblcontacts.firstname," ",tblcontacts.lastname) as fullname_contact from tblcontacts where tblcontacts.id = tblopportunity.contact) as name_contact'
);
$result           = data_tables_init($aColumns, $sIndexColumn, $sTable ,$join, $where, $additionalSelect, $order_by);

$output           = $result['output'];
$rResult          = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = array();
    $approval = false;
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if($aColumns[$i] == "client"){
            $_data = '<a href="'.admin_url('client/client/').$aRow['userid'].'">'.$aRow['company'].'</a>';
        }
        if($aColumns[$i] == "campaign"){
            $_data = '<a href="'.admin_url('campaign/campaign/').$aRow['id_campaign'].'">'.$aRow['name_campaign'].'</a>';
        }
        if($aColumns[$i] == "step"){
            $step=get_table_where('tblcampaign_step',array('tblcampaign_step.id_campaign'=>$aRow['id_campaign']),'id asc');
            $style="";
            if($client)
            {
                $style='style="max-width:600px"';
            }
            $_data='<div class="container" '.$style.'>
                <div class="row bs-wizard" style="border-bottom:0;">';
                    foreach($step as $st)
                    {
                        if($st['id']<=$aRow['step'])
                        {
                            $_data.='<div class="col-xs-1 bs-wizard-step complete">
                                            <div class="text-center bs-wizard-stepnum">'.$st['name'].'</div>
                                            <div class="progress">
                                                <div class="progress-bar"></div>
                                            </div>
                                         <a href="javacript:void(0)" onclick="update_status('.$aRow['id'].','.$st['id'].')" class="bs-wizard-dot"></a>
                                     </div>';
                        }
                        else
                        {
                            $_data.='<div class="col-xs-1 bs-wizard-step disabled">
                                            <div class="text-center bs-wizard-stepnum">'.$st['name'].'</div>
                                            <div class="progress">
                                                <div class="progress-bar"></div>
                                            </div>
                                         <a href="javacript:void(0)" onclick="update_status('.$aRow['id'].','.$st['id'].')" class="bs-wizard-dot"></a>
                                     </div>';
                        }
                    }

            $_data.='</div></div>';
        }
        if($aColumns[$i] == "contact"){
            $_data = $aRow['name_contact'];
        }
        if($aColumns[$i]=='create_by') {
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
        if($aColumns[$i]=='staff_in') {
            if($_data != '0') {
                $_data = '<a href="' . admin_url('profile/' . $_data) . '">' . staff_profile_image($_data, array(
                        'staff-profile-image-small mright5'
                    ), 'small', array(
                        'data-toggle' => 'tooltip',
                        'data-title' => $aRow["_staff_in"],
                    )) . '</a>';
            }
            else {
                $_data = "";
            }
        }
        if($aColumns[$i]=='expected')
        {
            $_data=_format_number($aRow['expected']);
        }
        if($aColumns[$i]=='8')
        {
            $options = '';
            if(!$client)
            {
                $options.='<a href="'.admin_url().'opportunity/opportunity/'.$aRow['id'].'" class="btn btn-default btn-icon">
                        <i class="fa fa-pencil-square-o"></i>
                  </a>';
                $options.='<a href="'.admin_url().'opportunity/delete/'.$aRow['id'].'" class="btn btn-danger _delete btn-icon" data-toggle="tooltip" data-placement="left" title="'._l('_tb_delete_campaign').'" data-original-title="'._l('_tb_delete_campaign').'">
                <i class="fa fa-remove"></i>
                </a>';
            }
            else
            {
                $options.='<a href="javacript:void(0)" onclick="delete_opportunity('.$aRow['id'].')" class="btn btn-danger _delete btn-icon" data-toggle="tooltip" data-placement="left" title="'._l('_tb_delete_campaign').'" data-original-title="'._l('_tb_delete_campaign').'">
                <i class="fa fa-remove"></i>
                </a>';
            }

            $_data=$options;
        }
        $row[] = $_data;

    }

//    $row[] = $options;

    $output['aaData'][] = $row;
}
