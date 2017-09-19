<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Opportunity extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('opportunity_model');
    }
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('opportunity');
        }
        if (!has_permission('customers', '', 'view')) {
            if (!is_customer_admin(get_staff_user_id())) {
                access_denied('customers');
            }
        }
        $data['title'] = _l('receipts');
        $this->load->view('admin/opportunity/manage', $data);
    }
    public function opportunity($id="")
    {
        if($this->input->post())
        {
            if($id=="")
            {
                $_data=$this->input->post();
                $_data['create_by']=get_staff_user_id();
                $_data['date_create']=date('Y-m-d');
                $id=$this->opportunity_model->insert($_data);
                if($id)
                {
                    set_alert('success',_l('add_opportunity_true'));
                }
                else
                {
                    set_alert('danger',_l('add_opportunity_false'));
                }

                redirect(admin_url('opportunity/opportunity/'.$id));
            }
            else
            {
                $_data=$this->input->post();
                $update_opportunity=$this->opportunity_model->update($id,$_data);
                if($update_opportunity)
                {
                    set_alert('success',_l('update_campain_true'));
                }
                redirect(admin_url('opportunity/opportunity/'.$id));
            }
        }
        else
        {
            if($id=="")
            {

            }
            else
            {
                $data['opportunity']=$this->opportunity_model->get_table_id('tblopportunity',array('id'=>$id));
                if($data['opportunity'])
                {
                    $data['contact']=$this->opportunity_model->get_table_where('tblcontacts',array('userid'=>$data['opportunity']->client));
                }
                $data['step']=$this->opportunity_model->get_table_where('tblcampaign_step',array('id_campaign'=>$data['opportunity']->campaign));

            }
            $data['client']=$this->opportunity_model->get_table_where('tblclients');
            $data['staff']=$this->opportunity_model->get_table_where('tblstaff');
            $data['source']=$this->opportunity_model->get_table_where('tblleadssources');
            $data['campaign']=$this->opportunity_model->get_table_where('tblcampaign');
            $data['source']=$this->opportunity_model->get_table_where('tblleadssources');
            $data['title']="";
            $this->load->view('admin/opportunity/detail',$data);
        }

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
    public function get_contact($id)
    {
        $contact=$this->opportunity_model->get_table_where('tblcontacts',array('userid'=>$id));
        echo  json_encode($contact);
    }
    public function get_select_step($id)
    {
        $step=$this->opportunity_model->get_table_where('tblcampaign_step',array('id_campaign'=>$id));
        echo  json_encode($step);
    }
    public function get_contact_id($id_contact)
    {
        $contact=$this->opportunity_model->get_table_id('tblcontacts',array('id'=>$id_contact));
        echo  json_encode($contact);
    }
    public function update_status()
    {
        $id=$this->input->post('id');
        $status=$this->input->post('status');

        if($id&&$status)
        {
//            echo $status;
            $this->db->where('id',$id);
            $this->db->update('tblopportunity',array('step'=>$status));
            if($this->db->affected_rows() > 0){
                echo json_encode(array('success'=>true,'message'=>_l('war_step')));
            }

        }
    }

}
