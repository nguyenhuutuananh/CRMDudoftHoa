<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$plan_status=array(
    "2"=>"Phiếu báo giá",
    "1"=>"Phiếu báo giá được xác nhận chọn để duyệt đơn đặt hàng",
    "0"=>"Phiếu báo giá chưa được xác nhận chọn để xác nhận"
);

$aColumns     = array(
    '1',
    'tblquotes.code',
    'tblquotes.company',
    'tblquotes.total',
    '(SELECT fullname FROM tblstaff WHERE tblquotes.create_by=tblstaff.staffid)',
    'tblquotes.status',
    'CONCAT((SELECT fullname FROM tblstaff  WHERE user_head_id=tblstaff.staffid),",",(SELECT fullname FROM tblstaff  WHERE user_admin_id=tblstaff.staffid)) as confirm',
    'tblquotes.date'
);
if($customer_id)
{
    $aColumns     = array(
        'tblquotes.code',
        'total',
        '(SELECT fullname FROM tblstaff WHERE create_by=tblstaff.staffid)',
        'status',
        'CONCAT((SELECT fullname FROM tblstaff  WHERE user_head_id=tblstaff.staffid),",",(SELECT fullname FROM tblstaff  WHERE user_admin_id=tblstaff.staffid)) as confirm',
        'date'
    );
}
$sIndexColumn = "id";
$sTable       = 'tblquotes';
$where        = array(
    // 'AND rel_type="'.$rel_type.'"',
);
if(!empty($sale_id))
{
    $where[]='AND rel_id="'.$sale_id.'"';
}
if($customer_id)
{
    array_push($where, 'AND customer_id='.$customer_id);
}
if($this->_instance->input->post()) {
    $filter_status = $this->_instance->input->post('filterStatus');
    if(is_numeric($filter_status)) {
        if($filter_status == 2)
            array_push($where, 'AND status='.$filter_status);
        elseif($filter_status == 4)
            array_push($where, 'AND export_status=1');
        elseif($filter_status == 3)
            array_push($where, 'AND export_status=0');
        else {
            array_push($where, 'AND status<>2');
        }
    }
}

$join         = array(
    'LEFT JOIN tblclients ON tblclients.userid = tblquotes.customer_id',
    'LEFT JOIN tblstaff  ON tblstaff.staffid=tblquotes.create_by'
);

$result       = data_tables_init($aColumns, $sIndexColumn, $sTable,$join, $where, array(
    'id',
    'prefix',
    'export_status',
    'tblstaff.fullname',
    'CONCAT(user_head_id,",",user_admin_id) as confirm_ids'
));
$output       = $result['output'];
$rResult      = $result['rResult'];


$j=0;
foreach ($rResult as $aRow) {
    $row = array();
    $j++;
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == '1') {
            $_data=$j;
        }
        if ($aColumns[$i] == 'rel_code') {
            $_data='<a href="'.admin_url('sales/sale_detail/'.$aRow['rel_id']).'">'.$aRow['rel_code'].'</a>';
        }
        if ($aColumns[$i] == 'tblquotes.code') {
            $_data=$aRow['prefix'].$aRow['tblquotes.code'];
        }
        if ($aColumns[$i] == 'tblquotes.date') {
            $_data=_d($aRow['tblquotes.date']);
        }
        if ($aColumns[$i] == 'tblquotes.total') {
            $_data=format_money($aRow['tblquotes.total']);
        }
        // var_dump($aRow);die;
        if ($aColumns[$i] == 'tblquotes.status') {
            $_data='<span class="inline-block label label-'.get_status_label($aRow['tblquotes.status']).'" task-status-table="'.$aRow['tblquotes.status'].'">' . format_status_quote($aRow['tblquotes.status'],false,true).'';
            if(has_permission('invoices', '', 'view') && has_permission('invoices', '', 'view_own'))
            {
                if($aRow['tblquotes.status']!=2){
                    $_data.='<a href="javacript:void(0)" onclick="var_status('.$aRow['tblquotes.status'].','.$aRow['id'].')">';
                }
                else
                {
                    $_data.='<a href="javacript:void(0)">';
                }
            }
            else {
                if($aRow['tblquotes.status']==0) {
                    $_data .= '<a href="javacript:void(0)" onclick="var_status(' . $aRow['tblquotes.status'] . ',' . $aRow['id'] . ')">';
                }
                else
                {
                    $_data .= '<a href="javacript:void(0)">';
                }
            }
            $_data.='<i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip" title="' . _l( $plan_status[$aRow['tblquotes.status']]) . '"></i>
                    </a>
                </span>';
        }
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow['confirm'];
            $confirms=array_unique(explode(',', $_data));
            $confirm_ids=array_unique(explode(',', $aRow['confirm_ids']));
            $_data            = '';
            $result = '';
            $as = 0;
            for ($x=0; $x < count($confirms); $x++) {
                if($confirms[$x]!='')
                {
                    $_data .= '<a href="' . admin_url('profile/' . $confirm_ids[$x]) . '">' . staff_profile_image($confirm_ids[$x], array(
                            'staff-profile-image-small mright5'
                        ), 'small', array(
                            'data-toggle' => 'tooltip',
                            'data-title' => $confirms[$x]
                        )) . '</a>';
                }
            }
        }
        $row[] = $_data;
    }
    $_data='';
    if ($aRow['create_by'] == get_staff_user_id() || is_admin()) {
        $_data .= icon_btn('quotes/pdf/' . $aRow['id'].'?pdf=true', 'print', 'btn-default',array('target' => '_blank','data-toggle'=>'tooltip',
            'title'=>_l('dt_button_print'),
            'data-placement'=>'top'));
        if($aRow['tblquotes.status']==2 && $aRow['export_status']!=1)
        {
            //Tao Hop Dong
            $_data .= icon_btn('quotes/contract_output/'. $aRow['id'] , 'exchange','btn-default',array(
                'data-toggle'=>'tooltip',
                'title'=>_l('create_contract'),
                'data-placement'=>'top'
            ));

        }

        $_data .= icon_btn('quotes/quote_detail/'. $aRow['id'] , 'edit','btn-default',array('data-toggle'=>'tooltip',
            'title'=>_l('edit'),
            'data-placement'=>'top'));
        $row[] =$_data.icon_btn('quotes/delete/'. $aRow['id'] , 'remove', 'btn-danger delete-remind',array('data-toggle'=>'tooltip',
                'title'=>_l('delete'),
                'data-placement'=>'top'));
    } else {
        $row[] = '';
    }
    $output['aaData'][] = $row;
}

