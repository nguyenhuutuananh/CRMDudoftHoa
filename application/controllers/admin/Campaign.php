<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Campaign extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('campaign_model');
    }
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('campaign');
        }
        if (!has_permission('customers', '', 'view')) {
            if (!is_customer_admin(get_staff_user_id())) {
                access_denied('customers');
            }
        }
        $data['title'] = _l('campaign');
        $this->load->view('admin/campaign/manage', $data);
    }
    public function campaign($id="")
    {
        if($this->input->post())
        {
            if($id=="")
            {
                $_data=$this->input->post();
                $__data['items']=$_data['items'];
                $__data['campaign_staff']=$_data['campaign_staff'];
                unset($_data['items']);
                unset($_data['campaign_staff']);
                $_data['create_by']=get_staff_user_id();
                $_data['create_data']=date('Y-m-d');
                $id=$this->campaign_model->insert($_data);
                if($id)
                {
                    $step_campaign=$this->campaign_model->insert_step($id,$__data['items']);
                    $staff_campaign=$this->campaign_model->insert_staff($id,$__data['campaign_staff']);
                    if($step_campaign&&$staff_campaign)
                    {
                        set_alert('success',_l('add_campain_true'));
                    }
                    else
                    {
                        set_alert('danger',_l('add_campain_false'));
                    }

                    redirect(admin_url('campaign/campaign/'.$id));
                }
            }
            else
            {
                $_data=$this->input->post();
                $__data['items']=$_data['items'];
                $__data['item']=$_data['item'];
                $__data['campaign_staff']=$_data['campaign_staff'];
                unset($_data['items']);
                unset($_data['item']);
                unset($_data['campaign_staff']);

                $update_campaign=$this->campaign_model->update($id,$_data);
                $_result=$this->campaign_model->update_step($id,$__data['item']);
                $__result= $this->campaign_model->insert_step($id,$__data['items']);
                $___result=$this->campaign_model->update_staff($id,$__data['campaign_staff']);
                if($update_campaign||$_result||$__result||$___result)
                {
                    set_alert('success',_l('update_campain_true'));
                }
                redirect(admin_url('campaign/campaign/'.$id));
            }
        }
        else
        {
            if($id=="")
            {

            }
            else
            {
                $data['campaign']=$this->campaign_model->get_table_id('tblcampaign',array('id'=>$id));
                if($data['campaign'])
                {
                    $data['campaign_step']=$this->campaign_model->get_table_where('tblcampaign_step',array('id_campaign'=>$data['campaign']->id));
                    $data['_staff']=$this->campaign_model->get_table_where('tblcampaign_staff',array('id_campaign'=>$data['campaign']->id));
                }
            }
            $data['staff']=$this->campaign_model->get_table_where('tblstaff');
            $data['title']="Chiến dịch";

            $data['warehouses']=$this->campaign_model->get_table_where('tblwarehouses');
            $data['items']= $this->campaign_model->get_full_items('','');

            $this->load->view('admin/campaign/detail',$data);
        }

    }
    public function get_items($id)
    {
        $data['items']= $this->campaign_model->get_full_items($id);
        echo json_encode($data['items']);
    }
    public function delete($id="")
    {
        $this->db->where('id',$id);
        $this->db->delete('tblcampaign');
        $this->db->where('id_campaign',$id);
        $this->db->delete('tblcampaign_staff');
        $this->db->where('id_campaign',$id);
        $this->db->delete('tblcampaign_step');
        if ($this->db->affected_rows() > 0) {
            set_alert('success',_l('delete_true'));
        }
        redirect(admin_url('campaign'));

    }

}
