<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Campaign extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('campaign_model');
        $this->load->model('email_marketing_model');
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

        $field=array('code','title','company','short_name','phonenumber',
            'mobilephone_number','address_room_number','address_building','address_home_number',
            'address','address_town','country','address_area','city','state','address_ward','fax',
            'email','id_card','vat','birthday','user_referrer','groups_in','source_approach',
            'default_currency','debt','shipping_area','shipping_country','shipping_area',
            'shipping_city','shipping_state','shipping_ward','shipping_room_number',
            'shipping_building','shipping_home_number','shipping_street','shipping_town',
            'shipping_zip',

        );
        $field2=array(
            'type_of_organization','bussiness_registration_number','legal_representative','website',
            'business','cooperative_day',
        );
        $field_staff=array(
            'staff_code','fullname','email','phonenumber',

        );
        $data['field']=$field;
        $data['field2']=$field2;
        $data['fieldstaff']=$field_staff;
        $data['email_plate'] = $this->email_marketing_model->get_email_templete();
        $data['title'] = _l('campaign');
        $this->load->view('admin/campaign/manage', $data);
    }
    public function send_email()
    {
        $data=$this->input->post();
        $this->email->initialize();
        $message = $data['message'];
        $name_file = $data['file_send'];
        $to_email_bc = $data['email_bcc'];
        $subject = $data['subject'];
        if($to_email_bc=="")
        {
            $_data['message_display'] = _l('find_email_not_null');
            $_data['tb']='danger';
            echo json_encode($_data);die();
        }
        if($subject=="")
        {
            $_data['message_display'] = _l('subject_not_null');
            $_data['tb']='danger';
            echo json_encode($_data);die();
        }


        $config_email=get_table_where('tblstaff',array('staffid'=>get_staff_user_id()));

        $sender_email = $config_email[0]['email_marketing'];
        $user_password = $config_email[0]['password_email_marketing'];
        if($sender_email==""||$user_password=="")
        {

            $sender_email=get_option('smtp_email');
            $user_password=get_option('smtp_password');
        }
        if($sender_email!=""&&$user_password!="") {

            $template = $data['view_template'];
            $count_send = 0;
            $username = get_staff_full_name();
            $id_log = $this->email_marketing_model->log_sent_email($subject, $message, $data['file_send'], $template);
            if ($name_file) {
                $name_file = explode(',', $name_file);
                if ($name_file != array()) {
                    foreach ($name_file as $file) {
                        if ($file != "") {
                            $this->email->attach(get_upload_path_by_type('email') . $file);
                        }
                    }
                }
            }

            $to_email_bc = explode(',', $to_email_bc);
            foreach ($to_email_bc as $rom_bc) {
                $config['smtp_user'] = $sender_email;
                $config['smtp_pass'] = $user_password;
                $this->email->initialize($config);
                $this->email->set_newline("\r\n");
                $this->email->from($sender_email, $username);
                $this->email->set_mailtype("html");
                $this->email->bcc($rom_bc);

                $message_sent = $this->get_content($rom_bc, $message);
                $this->email->subject($subject);
                $id_email = $this->log_sent_email($rom_bc, 2, $id_log);
                $this->email->message($message_sent . "<img border='0' src='" . admin_url() . "images_code/images_code?id=" . $id_email . "' width='1' height='1'>");
                if ($this->email->send()) {
                    $count_send++;
                }
            }
            if ($count_send > 0) {
                $_data['message_display'] = 'Message has been sent';
                $_data['tb']='info';
            }
            else {
                $this->email_marketing_model->delete_log_email($id_log);
                $_data['message_display'] = 'Message could not be sent!. <br>' . 'Mailer Error: ' . $this->email->print_debugger();
                $_data['tb']='danger';
            }
        }
        else
        {
            $_data['message_display'] = 'Can not find email account';
            $_data['tb']='danger';
        }
        echo json_encode($_data);

    }
    public function get_content($id,$content="")
    {
        $this->db->where('email',$id);
        $client=$this->db->get('tblclients')->row();
        $field=$this->db->list_fields('tblclients');
        $field_staff=$this->db->list_fields('tblclients');
        foreach($field as $rom)
        {
            $content=preg_replace('"{tblclients.'.$rom.'}"',$client->$rom,$content);
        }
        foreach($field_staff as $rom_s)
        {
            $content=preg_replace('"{tblstaff.'.$rom_s.'}"',$client->$rom_s,$content);
        }
        return $content;

    }
    public function log_sent_email($email,$type,$id_log)
    {
        $this->db->insert('tblemail_send',array('email'=>$email,'type'=>$type,'id_log'=>$id_log));
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('Send email [ID:' . $insert_id);
            return $insert_id;
        }
        return false;
    }

    public function get_opportunity()
    {
        $id=$this->input->post('id');
        if(is_numeric($id))
        {
            $this->db->select('email');
            $this->db->where('campaign',$id);
            $data_opprtunity=$this->db->get('tblopportunity')->result_array();
            $array=array_values($data_opprtunity);
            $data=array();
            foreach($array as $key=>$value)
            {
                $data[]=$value['email'];
            }
            $_data=implode(',',array_unique($data));
            if($_data!="")
            {
                echo $_data;
            }
        }
    }
    public function campaign($id="")
    {
        if($this->input->post())
        {
            if($id=="")
            {
                $_data=$this->input->post();
                $__data['items']=$_data['items'];
                $__data['_items']=$_data['_items'];
                $__data['campaign_staff']=$_data['campaign_staff'];
                $_data['expense']=str_replace(',','',$_data['expense']);
                unset($_data['items']);
                unset($_data['_items']);
                unset($_data['warehouse_name']);
                unset($_data['campaign_staff']);
                $_data['create_by']=get_staff_user_id();
                $_data['create_data']=date('Y-m-d');
                $id=$this->campaign_model->insert($_data);
                if($id)
                {
                    $step_campaign=$this->campaign_model->insert_step($id,$__data['items']);
                    $items_campaign=$this->campaign_model->insert_items($id,$__data['_items']);
                    $staff_campaign=$this->campaign_model->insert_staff($id,$__data['campaign_staff']);
                    if(($step_campaign)||($staff_campaign)||$items_campaign)
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
                $__data['_items']=$_data['_items'];
                $__data['_item']=$_data['_item'];
                $__data['campaign_staff']=$_data['campaign_staff'];
                $_data['expense']=str_replace(',','',$_data['expense']);
                unset($_data['items']);
                unset($_data['item']);
                unset($_data['_items']);
                unset($_data['_item']);
                unset($_data['warehouse_name']);
                unset($_data['campaign_staff']);
                $update_campaign=$this->campaign_model->update($id,$_data);
                $_result=$this->campaign_model->update_step($id,$__data['item']);
                $__result= $this->campaign_model->insert_step($id,$__data['items']);
                $___result=$this->campaign_model->update_staff($id,$__data['campaign_staff']);


                $_____result=$this->campaign_model->update_items($id,$__data['_item']);
                $______result=$this->campaign_model->insert_items($id,$__data['_items']);
                if($update_campaign||$_result||$__result||$___result||$_____result||$______result)
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
                    $data['__staff']=$this->campaign_model->get_table_where('tblcampaign_staff',array('id_campaign'=>$data['campaign']->id));
                    $data['_item']=$this->campaign_model->get_campaign_items($data['campaign']->id);
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
