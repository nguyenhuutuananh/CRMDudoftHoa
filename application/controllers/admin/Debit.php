<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Debit extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('debit_model');
    }
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('debit');
        }
        if (!has_permission('customers', '', 'view')) {
            if (!is_customer_admin(get_staff_user_id())) {
                access_denied('customers');
            }
        }
        $data['title'] = _l('debit');
        $this->load->view('admin/debit/manage', $data);
    }
    public function debit($id="")
    {
        if($this->input->post())
        {
            if($id=="")
            {

                $data=$this->input->post();
                $_data=$data['items'];
                unset($data['items']);
                $_data['id_staff']=get_staff_user_id();
                $id=$this->debit_model->insert($data);
                if($id) {
                    $_id = $this->debit_model->insert_debit_contract($id, $_data);
                    if($_id)
                    {
                        set_alert('success', _l('added_successfuly', _l('debit')));
                        redirect(admin_url('debit/debit/'.$id));
                    }
                    else
                    {
                        set_alert('danger', _l('problem_adding'));
                        redirect(admin_url('debit/debit/'.$id));
                    }
                }
                else
                {
                    set_alert('danger', _l('problem_adding'));
                    redirect(admin_url('debit/debit'));
                }
            }
            else
            {
                $data=$this->input->post();
                $_data['item']=$data['item'];
                $_data['items']=$data['items'];
                unset($data['item']);
                unset($data['items']);
                $result=$this->debit_model->update($id,$data);
                $_result=$this->debit_model->update_debit_cotract($id,$_data['item']);
                $__result=$this->debit_model->insert_debit_contract($id,$_data['items']);
                if($result||$_result||$__result)
                {
                    set_alert('success', _l('updated_successfuly'));
                }
                redirect(admin_url('debit/debit/'.$id));


            }

        }
        else
        {
            if($id=="")
            {
                $data['heading']=_l('debit_add_heading');
                $data['title']=_l('debit_add_heading');
                $data['supplier']=$this->debit_model->get_table_where('tblsuppliers');
                $data['purchase_contracts']=$this->debit_model->get_table_where('tblpurchase_contracts');
                $data['currencies']=$this->debit_model->get_table_where('tblcurrencies');
                $data['contract']=$this->debit_model->get_contract();
                $data['tk_no']=$this->debit_model->get_table_where('tblaccounts','idAccountAttribute=1 or idAccountAttribute=3');
                $data['tk_co']=$this->debit_model->get_table_where('tblaccounts','idAccountAttribute=2 or idAccountAttribute=3');
                $data['debit_contract']=$this->debit_model->get_debit_contract();
                $data['debit_contract_purchase']=$this->debit_model->get_debit_purchase();
                $data['code_vouchers']=$this->debit_model->get_vouchers();

            }
            else
            {
                $data['heading']=_l('debit_update_heading');
                $data['title']=_l('debit_update_heading');
                $data['supplier']=$this->debit_model->get_table_where('tblsuppliers');
                $data['purchase_contracts']=$this->debit_model->get_table_where('tblpurchase_contracts');
                $data['currencies']=$this->debit_model->get_table_where('tblcurrencies');
                $data['contract']=$this->debit_model->get_contract();
                $data['tk_no']=$this->debit_model->get_table_where('tblaccounts','idAccountAttribute=1 or idAccountAttribute=3');
                $data['tk_co']=$this->debit_model->get_table_where('tblaccounts','idAccountAttribute=2 or idAccountAttribute=3');
                $data['debit_contract']=$this->debit_model->get_debit_contract();
                $data['debit_contract_purchase']=$this->debit_model->get_debit_purchase();

                $data['supplier']=$this->debit_model->get_table_where('tblsuppliers');
                $data['vote']=$this->debit_model->get_index_debit($id);
                if($data['vote'])
                {
                    $data['debit']= $this->debit_model->index_debit_contract($id);
                }
                else
                {
                    set_alert('warning', _l('no_data_found', _l('debit')));
                    redirect(admin_url('debit/debit'));
                }
            }
            $this->load->view('admin/debit/detail',$data);
        }

    }
    public function pdf($id="")
    {
        $debit = $this->debit_model->get_data_pdf($id);
        if ($this->input->get('combo')) {
            $debit->combo=$this->input->get('combo');
        }
        $pdf      = debit_pdf($debit);

        $type     = 'D';
        if ($this->input->get('print')) {
            $type = 'I';
        }
        $pdf->Output(slug_it($debit->code_vouchers) . '.pdf', $type);
    }


    public function update_status()
    {
        $id=$this->input->post('id');
        $status=$this->input->post('status');


        $staff_id=get_staff_user_id();
        $date=date('Y-m-d H:i:s');
        $data=array('status'=>$status);

        $inv=$this->debit_model->get_index_debit($id);

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
            $success=$this->debit_model->update_status($id,$data);
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

}
