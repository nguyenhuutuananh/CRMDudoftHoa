<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Report_have extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('report_have_model');
    }
    public function index()
    {
        if (!has_permission('report_have', '', 'view')) {
            access_denied('report_have');
        }
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('report_have');
        }

        $data['title'] = _l('report_have');
        $this->load->view('admin/report_have/manage', $data);
    }
    public function report_have($id="")
    {
        if($this->input->post())
        {
            if($id=="")
            {
                if (!has_permission('report_have', '', 'create')) {
                    access_denied('report_have');
                }
                $data=$this->input->post();
                $_data=$data['items'];
                unset($data['items']);
                $data['id_staff']=  get_staff_user_id();
                $id = $this->report_have_model->insert($data);
                if($id) {
                    $_id = $this->report_have_model->insert_report_have_contract($id, $_data);
                    if($_id)
                    {
                        set_alert('success', _l('added_successfuly', _l('report_have')));
                        redirect(admin_url('report_have/report_have/'.$id));
                    }
                    else
                    {
                        set_alert('danger', _l('problem_adding'));
                        redirect(admin_url('report_have/report_have/'.$id));
                    }
                }
                else
                {
                    set_alert('danger', _l('problem_adding'));
                    redirect(admin_url('report_have/report_have'));
                }
            }
            else
            {
                if (!has_permission('report_have', '', 'edit')) {
                    access_denied('report_have');
                }
                $data=$this->input->post();
                $_data['item']=$data['item'];
                $_data['items']=$data['items'];
                unset($data['item']);
                unset($data['items']);
                $result=$this->report_have_model->update($id,$data);
                $_result=$this->report_have_model->update_report_have_cotract($id,$_data['item']);
                $__result=$this->report_have_model->insert_report_have_contract($id,$_data['items']);
                if($result||$_result||$__result)
                {
                    set_alert('success', _l('updated_successfuly'));
                }
                redirect(admin_url('report_have/report_have/'.$id));
            }

        }
        else
        {
            if($id=="")
            {
                if (!has_permission('report_have', '', 'create')) {
                    access_denied('report_have');
                }
                $data['heading']=_l('report_have_add_heading');
                $data['title']=_l('report_have_add_heading');
                $data['client']=$this->report_have_model->get_table_where('tblclients');
                $data['currencies']=$this->report_have_model->get_table_where('tblcurrencies');
                $data['tk_no']=$this->report_have_model->get_table_where('tblaccounts','idAccountAttribute=1 or idAccountAttribute=3');
                $data['tk_co']=$this->report_have_model->get_table_where('tblaccounts','idAccountAttribute=2 or idAccountAttribute=3');
                $data['tk_ck']=$this->report_have_model->get_table_where('tblaccounts');
                $data['contract']=$this->report_have_model->get_contract();
                $data['code_vouchers']=$this->report_have_model->get_vouchers();
            }
            else
            {
                if (!has_permission('report_have', '', 'view')) {
                    access_denied('report_have');
                }
                $data['heading']=_l('report_have_update_heading');
                $data['title']=_l('report_have_update_heading');
                $data['client']=$this->report_have_model->get_table_where('tblclients');
                $data['account_person']=$this->report_have_model->get_table_where('tblaccount_person');
                $data['contract']=$this->report_have_model->get_contract();
                $data['tk_no']=$this->report_have_model->get_table_where('tblaccounts','idAccountAttribute=1 or idAccountAttribute=3');
                $data['tk_co']=$this->report_have_model->get_table_where('tblaccounts','idAccountAttribute=2 or idAccountAttribute=3');
                $data['report_have']=$this->report_have_model->get_index_report_have($id);
                if($data['report_have'])
                {
                    $data['report_haves']= $this->report_have_model->index_report_have_contract($id);
                }
                else
                {
                    set_alert('warning', _l('no_data_found', _l('report_have')));
                    redirect(admin_url('report_have/report_have'));
                }
            }
            $this->load->view('admin/report_have/detail',$data);
        }

    }
    public function get_invoices()
    {
        $id_client=$this->input->post('id_client');
        $client=$this->report_have_model->get_invoices_report_have($id_client);
        echo json_encode($client);
    }
    public function get_invoices_id()
    {
        $id=$this->input->post('invoices');
        if($id){
            $result=$this->report_have_model->get_table_id('tblinvoices','id='.$id);
            echo json_encode($result);
        }
    }
    public function get_client()
    {
        $id_contract=$this->input->post('id');
        $result=$this->report_have_model->get_client_contract($id_contract);
        echo json_encode($result);
    }
    public function pdf($id="")
    {
        $report_have = $this->report_have_model->get_data_pdf($id);
        if ($this->input->get('combo')) {
            $report_have->combo=$this->input->get('combo');
        }
        $pdf      = report_have_pdf($report_have);

        $type     = 'D';
        if ($this->input->get('print')) {
            $type = 'I';
        }
        $pdf->Output(slug_it($report_have->code_vouchers) . '.pdf', $type);
    }


    public function update_status()
    {
        $id=$this->input->post('id');
        $status=$this->input->post('status');

        $id='1';
        $status='0';

        $staff_id=get_staff_user_id();
        $date=date('Y-m-d H:i:s');
        $data=array('status'=>$status);

        $inv=$this->report_have_model->get_index_report_have($id);

        if(is_admin() && $status==0)
        {
            $data['staff_browse']=$staff_id;
            $data['date_status']=$date;

            $data['staff_browse']=$staff_id;
            $data['date_status']=$date;

            $data['status']=2;
        }
        elseif(is_admin() && $status==1)
        {
            $data['status']=2;
            $data['staff_browse']=$staff_id;
            $data['date_status']=$date;
        }
        elseif(is_head($inv->id_staff))
        {
            $data['status']+=1;
            $data['staff_browse']=$staff_id;
            $data['date_status']=$date;
        }
        $success=false;

        if(is_admin() || is_head($inv->id_staff))
        {
            $success=$this->report_have_model->update_status($id,$data);
        }

        if($success) {
            echo json_encode(array(
                'success' => $success,
                'message' => _l('Xác nhận đề xuất thành công')
            ));
        }
        else
        {
            echo json_encode(array(
                'success' => $success,
                'message' => _l('Không thể cập nhật dữ liệu')
            ));
        }
        die;
    }
    public function account_person($id)
    {
        if($this->input->post($id))
        {
            if($id=="")
            {
                $data= $this->input->post();
                $data['id_staff']=get_staff_user_id();
                $data['date_create']=date('Y-m-d');
                if($data['account']=="")
                {
                    echo json_encode(array(
                        'success' => false,
                        'message' => _l('Vui lòng nhập số tài khoản hưởng thụ')
                    ));die();
                }
                if($data['account_holder']=="")
                {
                    echo json_encode(array(
                        'success' => false,
                        'message' => _l('Vui lòng nhập chủ tài khoản')
                    ));die();
                }
                $this->db->insert('tblaccount_person',$data);
                $id=$this->db->insert_id();
                if($id)
                {
                    echo json_encode(array(
                        'success' => true,
                        'message' => _l('add_true'),
                        'adddata'=>true,
                        'data'=>json_encode(array('id'=>$id,'name_bank'=>$data['name_bank'],'account'=>$data['account']))
                    ));
                }
                else
                {
                    echo json_encode(array(
                        'success' => false,
                        'message' => _l('add_false')
                    ));
                }
            }
        }
    }

}
