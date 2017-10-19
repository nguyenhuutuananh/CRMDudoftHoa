<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Clients extends Admin_controller
{
    private $not_importable_clients_fields = array('userid', 'id', 'is_primary', 'password', 'datecreated', 'last_ip', 'last_login', 'last_password_change', 'active', 'new_pass_key', 'new_pass_key_requested', 'leadid', 'default_currency', 'profile_image', 'default_language', 'direction','show_primary_contact');
    public $pdf_zip;
    function __construct()
    {
        parent::__construct();
    }
    /* List all clients */
    public function index()
    {
        // var_dump(have_assigned_customers());
        // exit();
        // if (!has_permission('customers', '', 'view')) {
        //     if (!have_assigned_customers() && !has_permission('customers','','create')) {
        //         access_denied('customers');
        //     }
        // }
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('clients');
        }
        $this->load->model('contracts_model');
        $data['contract_types'] = $this->contracts_model->get_contract_types();
        $data['groups']         = $this->clients_model->get_groups();
        $data['title']          = _l('clients');

        $this->load->model('proposals_model');
        $data['proposal_statuses'] = $this->proposals_model->get_statuses();

        $this->load->model('invoices_model');
        $data['invoice_statuses'] = $this->invoices_model->get_statuses();

        $this->load->model('estimates_model');
        $data['estimate_statuses'] = $this->estimates_model->get_statuses();

        $this->load->model('projects_model');
        $data['project_statuses'] = $this->projects_model->get_project_statuses();

        $data['customer_admins'] = $this->clients_model->get_customers_admin_unique_ids();

        $this->load->view('admin/clients/manage', $data);
    }
    public function get_wards($district_id) {
        if(is_numeric($district_id) && $this->input->is_ajax_request()) {
            echo json_encode(get_all_wards($district_id));
        }
    }
    public function get_districts($province_id) {
        if(is_numeric($province_id) && $this->input->is_ajax_request()) {
            echo json_encode(get_all_district($province_id));
        }
    }
    public function get_province($country_id) {
        if(is_numeric($province_id) && $this->input->is_ajax_request()) {
            echo json_encode(get_all_province($country_id));
        }
    }

    public function getClientByID($id=NULL)
    {
        if(empty($id)) $id=$this->input->post('customer_id');
        echo json_encode(array(
            'success' => $this->clients_model->get($id)
        ));
    }
    
    /* Edit client or add new client*/



    public function init_opportunity($client="")
    {
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('opportunity',array('client'=>$client));
        }
    }
    public function delete_opportunity()
    {
        if($this->input->post())
        {
            $id=$this->input->post('id');
            $this->db->where('id',$id);
            $this->db->delete('tblopportunity');
            if($this->db->affected_rows() > 0){
                echo json_encode(array('success'=>true,'message'=>_l('delete_true')));
            }
            else
            {
                echo json_encode(array('success'=>false,'message'=>_l('delete_false')));
            }
        }
    }
    public function client($id = '')
    {
        
        if ($this->input->post() && !$this->input->is_ajax_request()) {
            if ($id == '') {
                if (!has_permission('customers', '', 'create')) {
                    access_denied('customers');
                }
                $data                 = $this->input->post();
                $save_and_add_contact = false;
                if (isset($data['save_and_add_contact'])) {
                    unset($data['save_and_add_contact']);
                    $save_and_add_contact = true;
                }
                $id = $this->clients_model->add($data);
                if (!has_permission('customers', '', 'view')) {
                    $assign['customer_admins']   = array();
                    $assign['customer_admins'][] = get_staff_user_id();
                    $this->clients_model->assign_admins($assign, $id);
                }
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('client')));
                    if(get_option('prefix_add_continuous')==0)
                    {
                        if ($save_and_add_contact == false) {
                            redirect(admin_url('clients/client/' . $id));
                        } else {
                            redirect(admin_url('clients/client/' . $id . '?new_contact=true'));
                        }
                    }
                    else
                    {
                        if ($save_and_add_contact == false) {
                            redirect(admin_url('clients/client'));
                        } else {
                            redirect(admin_url('clients/client?new_contact=true'));
                        }
                    }
                }
            } else {
                if (!has_permission('customers', '', 'edit')) {
                    if (!is_customer_admin($id)) {
                        access_denied('customers');
                    }
                }
                $success = $this->clients_model->update($this->input->post(), $id);
                if ($success == true) {
                    set_alert('success', _l('updated_successfuly', _l('client')));
                }
                redirect(admin_url('clients/client/' . $id));
            }
        }
        if ($id == '') {
            if (!has_permission('customers', '', 'create')) {
                access_denied('customers');
            }
            $title = _l('add_new', _l('client_lowercase'));
        } else {
            if (!has_permission('customers', '', 'edit')) {
                if ($id != '' && !is_customer_admin($id)) {
                    access_denied('customers');
                }
            }
            $client = $this->clients_model->get($id);

            if (!$client) {
                blank_page('Client Not Found');
            }

            $data['lightbox_assets'] = true;
            $this->load->model('staff_model');
            $data['staff']           = $this->staff_model->get('', 1);
            $data['customer_admins'] = $this->clients_model->get_admins($id);
            $this->load->model('payment_modes_model');
            $data['payment_modes'] = $this->payment_modes_model->get();
            $data['attachments']   = $this->clients_model->get_all_customer_attachments($id);
            $data['client']        = $client;
            $title                 = $client->company;
            // Get all active staff members (used to add reminder)
            $this->load->model('staff_model');
            $data['members'] = $this->staff_model->get('', 1);
            if ($this->input->is_ajax_request()) {
                $this->perfex_base->get_table_data('tickets', array(
                    'userid' => $id
                ));
            }
            $data['customer_groups'] = $this->clients_model->get_customer_groups($id);

            $this->load->model('estimates_model');
            $data['estimate_statuses'] = $this->estimates_model->get_statuses();

            $this->load->model('invoices_model');
            $data['invoice_statuses'] = $this->invoices_model->get_statuses();

            if (!empty($data['client']->company)) {
                // Check if is realy empty client company so we can set this field to empty
                // The query where fetch the client auto populate firstname and lastname if company is empty
                if (is_empty_customer_company($data['client']->userid)) {
                    $data['client']->company = '';
                }
            }
        }
        
        if (!$this->input->get('group')) {
            $group = 'profile';
        } else {
            $group = $this->input->get('group');
        }
        
        $data['group']  = $group;
        $data['groups'] = $this->clients_model->get_groups();
        $data['users'] = $this->clients_model->get();
        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();
        $data['user_notes'] = $this->misc_model->get_notes($id, 'customer');
        $data['bodyclass'] = 'customer-profile';
        $this->load->model('projects_model');
        $data['project_statuses'] = $this->projects_model->get_project_statuses();
        $data['contacts']         = $this->clients_model->get_contacts($id);
        $data['sources']  = $this->clients_model->get_source();
        $data['areas']  = $this->clients_model->get_area();
        $data['title'] = $title;
        $this->load->view('admin/clients/client', $data);
    }
    public function modal($id = '') {
        if (!has_permission('customers', '', 'view')) {
            if ($id != '' && !is_customer_admin($id)) {
                access_denied('customers');
            }
        }
        if($id!='') {
            $client = $this->clients_model->get($id);
            if (!$client) {
                blank_page('Client Not Found');
            }
            $data['customer_groups'] = $this->clients_model->get_customer_groups($id);
            $this->load->model('currencies_model');
            $data['currencies'] = $this->currencies_model->get();
            $data['groups'] = $this->clients_model->get_groups();
            $data['users'] = $this->clients_model->get();
            $data['sources']  = $this->clients_model->get_source();
            $data['areas']  = $this->clients_model->get_area();
            $data['client'] = $client;
        }
        echo json_encode(array(
            'data' => $this->load->view('admin/clients/modals/client', $data, TRUE),
        ));
    }
    public function contact($customer_id, $contact_id = '')
    {
        if (!has_permission('customers', '', 'view')) {
            if (!is_customer_admin($customer_id)) {
                echo _l('access_denied');
                die;
            }
        }
        $data['customer_id'] = $customer_id;
        $data['contactid']   = $contact_id;
        if ($this->input->post()) {
            $data = $this->input->post();
            unset($data['contactid']);
            if ($contact_id == '') {
                if (!has_permission('customers', '', 'create')) {
                    if (!is_customer_admin($customer_id)) {
                        header('HTTP/1.0 400 Bad error');
                        echo json_encode(array(
                            'success' => false,
                            'message' => _l('access_denied')
                        ));
                        die;
                    }
                }
                $id      = $this->clients_model->add_contact($data, $customer_id);
                $message = '';
                $success = false;
                if ($id) {
                    handle_contact_profile_image_upload($id);
                    $success = true;
                    $message = _l('added_successfuly', _l('contact'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
                die;
            } else {
                if (!has_permission('customers', '', 'edit')) {
                    if (!is_customer_admin($customer_id)) {
                        header('HTTP/1.0 400 Bad error');
                        echo json_encode(array(
                            'success' => false,
                            'message' => _l('access_denied')
                        ));
                        die;
                    }
                }
                $original_contact = $this->clients_model->get_contact($contact_id);
                $success          = $this->clients_model->update_contact($data, $contact_id);
                $message          = '';
                $proposal_warning = false;
                $original_email   = '';
                $updated          = false;
                if (is_array($success)) {
                    if (isset($success['set_password_email_sent'])) {
                        $message = _l('set_password_email_sent_to_client');
                    } else if (isset($success['set_password_email_sent_and_profile_updated'])) {
                        $updated = true;
                        $message = _l('set_password_email_sent_to_client_and_profile_updated');
                    }
                } else {
                    if ($success == true) {
                        $updated = true;
                        $message = _l('updated_successfuly', _l('contact'));
                    }
                }
                if (handle_contact_profile_image_upload($contact_id) && !$updated) {
                    $message = _l('updated_successfuly', _l('contact'));
                    $success = true;
                }
                if ($updated == true) {
                    $contact = $this->clients_model->get_contact($contact_id);
                    if (total_rows('tblproposals', array(
                        'rel_type' => 'customer',
                        'rel_id' => $contact->userid,
                        'email' => $original_contact->email
                    )) > 0 && ($original_contact->email != $contact->email)) {
                        $proposal_warning = true;
                        $original_email   = $original_contact->email;
                    }
                }
                echo json_encode(array(
                    'success' => $success,
                    'proposal_warning' => $proposal_warning,
                    'message' => $message,
                    'original_email' => $original_email
                ));
                die;
            }
        }
        if ($contact_id == '') {
            $title = _l('add_new', _l('contact_lowercase'));
        } else {
            $data['contact'] = $this->clients_model->get_contact($contact_id);

            if (!$data['contact']) {
                header('HTTP/1.0 400 Bad error');
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Contact Not Found'
                ));
                die;
            }
            $title = $data['contact']->firstname . ' ' . $data['contact']->lastname;
        }

        $data['customer_permissions'] = $this->perfex_base->get_contact_permissions();
        $data['title']                = $title;
        $this->load->view('admin/clients/modals/contact', $data);
    }
    public function update_file_share_visibility()
    {
        if ($this->input->post()) {

            $file_id           = $this->input->post('file_id');
            $share_contacts_id = array();

            if ($this->input->post('share_contacts_id')) {
                $share_contacts_id = $this->input->post('share_contacts_id');
            }

            $this->db->where('file_id', $file_id);
            $this->db->delete('tblcustomerfiles_shares');

            foreach ($share_contacts_id as $share_contact_id) {
                $this->db->insert('tblcustomerfiles_shares', array(
                    'file_id' => $file_id,
                    'contact_id' => $share_contact_id
                ));
            }

        }
    }
    public function delete_contact_profile_image($contact_id)
    {
        do_action('before_remove_contact_profile_image');
        if (file_exists(get_upload_path_by_type('contact_profile_images') . $contact_id)) {
            delete_dir(get_upload_path_by_type('contact_profile_images') . $contact_id);
        }
        $this->db->where('id', $contact_id);
        $this->db->update('tblcontacts', array(
            'profile_image' => NULL
        ));
    }
    public function mark_as_active($id)
    {
        $this->db->where('userid', $id);
        $this->db->update('tblclients', array(
            'active' => 1
        ));
        redirect(admin_url('clients/client/' . $id));
    }
    public function update_all_proposal_emails_linked_to_customer($contact_id)
    {

        $success = false;
        $email   = '';
        if ($this->input->post('update')) {
            $this->load->model('proposals_model');

            $this->db->select('email,userid');
            $this->db->where('id', $contact_id);
            $contact = $this->db->get('tblcontacts')->row();

            $proposals     = $this->proposals_model->get('', array(
                'rel_type' => 'customer',
                'rel_id' => $contact->userid,
                'email' => $this->input->post('original_email')
            ));
            $affected_rows = 0;

            foreach ($proposals as $proposal) {
                $this->db->where('id', $proposal['id']);
                $this->db->update('tblproposals', array(
                    'email' => $contact->email
                ));
                if ($this->db->affected_rows() > 0) {
                    $affected_rows++;
                }
            }

            if ($affected_rows > 0) {
                $success = true;
            }

        }
        echo json_encode(array(
            'success' => $success,
            'message' => _l('proposals_emails_updated', array(
                _l('contact_lowercase'),
                $contact->email
            ))
        ));
    }

    public function assign_admins($id)
    {
        if (!has_permission('customers', '', 'create') && !has_permission('customers', '', 'edit')) {
            access_denied('customers');
        }
        $success = $this->clients_model->assign_admins($this->input->post(), $id);
        if ($success == true) {
            set_alert('success', _l('updated_successfuly', _l('client')));
        }

        redirect(admin_url('clients/client/' . $id . '?tab=customer_admins'));

    }

    public function delete_customer_admin($customer_id,$staff_id){

        if (!has_permission('customers', '', 'create') && !has_permission('customers', '', 'edit')) {
            access_denied('customers');
        }

        $this->db->where('customer_id',$customer_id);
        $this->db->where('staff_id',$staff_id);
        $this->db->delete('tblcustomeradmins');
        redirect(admin_url('clients/client/'.$customer_id).'?tab=customer_admins');
    }
    public function delete_contact($customer_id, $id)
    {
        if (!has_permission('customers', '', 'delete')) {
            if (!is_customer_admin($customer_id)) {
                access_denied('customers');
            }
        }

        $this->clients_model->delete_contact($id);
        redirect(admin_url('clients/client/' . $customer_id . '?tab=contacts'));
    }
    public function contacts($client_id)
    {
        $this->perfex_base->get_table_data('contacts', array(
            'client_id' => $client_id
        ));
    }
    public function upload_attachment($id)
    {
        handle_client_attachments_upload($id);
    }
    public function add_external_attachment()
    {
        if ($this->input->post()) {
            $this->misc_model->add_attachment_to_database($this->input->post('clientid'), 'customer', $this->input->post('files'), $this->input->post('external'));
        }
    }
    public function delete_attachment($customer_id, $id)
    {
        if (has_permission('customers', '', 'delete') || is_customer_admin($customer_id)) {
            $this->clients_model->delete_attachment($id);
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
    /* Delete client */
    public function delete($id)
    {
        if (!has_permission('customers', '', 'delete')) {
            access_denied('customers');
        }
        if (!$id) {
            redirect(admin_url('clients'));
        }
        $response = $this->clients_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('client_delete_invoices_warning'));
        } else if ($response == true) {
            set_alert('success', _l('deleted', _l('client')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('client_lowercase')));
        }
        redirect(admin_url('clients'));
    }
    /* Staff can login as client */
    public function login_as_client($id)
    {
        if (is_admin()) {
            $this->clients_model->login_as_client($id);
        }
        do_action('after_contact_login');
        redirect(site_url());
    }
    public function get_customer_billing_and_shipping_details($id)
    {
        echo json_encode($this->clients_model->get_customer_billing_and_shipping_details($id));
    }
    /* Change client status / active / inactive */
    public function change_contact_status($id, $status)
    {
        if (has_permission('customers', '', 'edit')) {
            if ($this->input->is_ajax_request()) {
                $this->clients_model->change_contact_status($id, $status);
            }
        }
    }
    /* Change client status / active / inactive */
    public function change_client_status($id, $status)
    {

        if ($this->input->is_ajax_request()) {
            $this->clients_model->change_client_status($id, $status);
        }

    }
    /* Since version 1.0.2 zip client invoices */
    public function zip_invoices($id)
    {
        $has_permission_view = has_permission('invoices', '', 'view');
        if (!$has_permission_view && !has_permission('invoices', '', 'view_own')) {
            access_denied('Zip Customer Invoices');
        }
        if ($this->input->post()) {
            $status        = $this->input->post('invoice_zip_status');
            $zip_file_name = $this->input->post('file_name');
            if ($this->input->post('zip-to') && $this->input->post('zip-from')) {
                $from_date = to_sql_date($this->input->post('zip-from'));
                $to_date   = to_sql_date($this->input->post('zip-to'));
                if ($from_date == $to_date) {
                    $this->db->where('date', $from_date);
                } else {
                    $this->db->where('date BETWEEN "' . $from_date . '" AND "' . $to_date . '"');
                }
            }
            $this->db->select('id');
            $this->db->from('tblinvoices');
            if ($status != 'all') {
                $this->db->where('status', $status);
            }
            $this->db->where('clientid', $id);
            $this->db->order_by('number,YEAR(date)', 'desc');

            if (!$has_permission_view) {
                $this->db->where('addedfrom', get_staff_user_id());
            }

            $invoices = $this->db->get()->result_array();
            $this->load->model('invoices_model');
            $this->load->helper('file');
            if (!is_really_writable(TEMP_FOLDER)) {
                show_error('/temp folder is not writable. You need to change the permissions to 755');
            }
            $dir = TEMP_FOLDER . $zip_file_name;
            if (is_dir($dir)) {
                delete_dir($dir);
            }
            if (count($invoices) == 0) {
                set_alert('warning', _l('client_zip_no_data_found', _l('invoices')));
                redirect(admin_url('clients/client/' . $id . '?group=invoices'));
            }
            mkdir($dir, 0777);
            foreach ($invoices as $invoice) {
                $invoice_data    = $this->invoices_model->get($invoice['id']);
                $this->pdf_zip   = invoice_pdf($invoice_data);
                $_temp_file_name = slug_it(format_invoice_number($invoice_data->id));
                $file_name       = $dir . '/' . strtoupper($_temp_file_name);
                $this->pdf_zip->Output($file_name . '.pdf', 'F');
            }
            $this->load->library('zip');
            // Read the invoices
            $this->zip->read_dir($dir, false);
            // Delete the temp directory for the client
            delete_dir($dir);
            $this->zip->download(slug_it(get_option('companyname')) . '-invoices-' . $zip_file_name . '.zip');
            $this->zip->clear_data();
        }
    }
    /* Since version 1.0.2 zip client invoices */
    public function zip_estimates($id)
    {
        $has_permission_view = has_permission('estimates', '', 'view');
        if (!$has_permission_view && !has_permission('estimates', '', 'view_own')) {
            access_denied('Zip Customer Estimates');
        }


        if ($this->input->post()) {
            $status        = $this->input->post('estimate_zip_status');
            $zip_file_name = $this->input->post('file_name');
            if ($this->input->post('zip-to') && $this->input->post('zip-from')) {
                $from_date = to_sql_date($this->input->post('zip-from'));
                $to_date   = to_sql_date($this->input->post('zip-to'));
                if ($from_date == $to_date) {
                    $this->db->where('date', $from_date);
                } else {
                    $this->db->where('date BETWEEN "' . $from_date . '" AND "' . $to_date . '"');
                }
            }
            $this->db->select('id');
            $this->db->from('tblestimates');
            if ($status != 'all') {
                $this->db->where('status', $status);
            }
            if (!$has_permission_view) {
                $this->db->where('addedfrom', get_staff_user_id());
            }
            $this->db->where('clientid', $id);
            $this->db->order_by('number,YEAR(date)', 'desc');
            $estimates = $this->db->get()->result_array();
            $this->load->helper('file');
            if (!is_really_writable(TEMP_FOLDER)) {
                show_error('/temp folder is not writable. You need to change the permissions to 777');
            }
            $this->load->model('estimates_model');
            $dir = TEMP_FOLDER . $zip_file_name;
            if (is_dir($dir)) {
                delete_dir($dir);
            }
            if (count($estimates) == 0) {
                set_alert('warning', _l('client_zip_no_data_found', _l('estimates')));
                redirect(admin_url('clients/client/' . $id . '?group=estimates'));
            }
            mkdir($dir, 0777);
            foreach ($estimates as $estimate) {
                $estimate_data   = $this->estimates_model->get($estimate['id']);
                $this->pdf_zip   = estimate_pdf($estimate_data);
                $_temp_file_name = slug_it(format_estimate_number($estimate_data->id));
                $file_name       = $dir . '/' . strtoupper($_temp_file_name);
                $this->pdf_zip->Output($file_name . '.pdf', 'F');
            }
            $this->load->library('zip');
            // Read the invoices
            $this->zip->read_dir($dir, false);
            // Delete the temp directory for the client
            delete_dir($dir);
            $this->zip->download(slug_it(get_option('companyname')) . '-estimates-' . $zip_file_name . '.zip');
            $this->zip->clear_data();
        }
    }
    public function zip_payments($id)
    {
        if (!$id) {
            die('No user id');
        }

        $has_permission_view = has_permission('payments', '', 'view');
        if (!$has_permission_view && !has_permission('invoices', '', 'view_own')) {
            access_denied('Zip Customer Payments');
        }

        if ($this->input->post('zip-to') && $this->input->post('zip-from')) {
            $from_date = to_sql_date($this->input->post('zip-from'));
            $to_date   = to_sql_date($this->input->post('zip-to'));
            if ($from_date == $to_date) {
                $this->db->where('tblinvoicepaymentrecords.date', $from_date);
            } else {
                $this->db->where('tblinvoicepaymentrecords.date BETWEEN "' . $from_date . '" AND "' . $to_date . '"');
            }
        }
        $this->db->select('tblinvoicepaymentrecords.id as paymentid');
        $this->db->from('tblinvoicepaymentrecords');
        $this->db->where('tblclients.userid', $id);
        if (!$has_permission_view) {
            $this->db->where('invoiceid IN (SELECT id FROM tblinvoices WHERE addedfrom=' . get_staff_user_id() . ')');
        }
        $this->db->join('tblinvoices', 'tblinvoices.id = tblinvoicepaymentrecords.invoiceid', 'left');
        $this->db->join('tblclients', 'tblclients.userid = tblinvoices.clientid', 'left');
        if ($this->input->post('paymentmode')) {
            $this->db->where('paymentmode', $this->input->post('paymentmode'));
        }
        $payments      = $this->db->get()->result_array();
        $zip_file_name = $this->input->post('file_name');
        $this->load->helper('file');
        if (!is_really_writable(TEMP_FOLDER)) {
            show_error('/temp folder is not writable. You need to change the permissions to 777');
        }
        $dir = TEMP_FOLDER . $zip_file_name;
        if (is_dir($dir)) {
            delete_dir($dir);
        }
        if (count($payments) == 0) {
            set_alert('warning', _l('client_zip_no_data_found', _l('payments')));
            redirect(admin_url('clients/client/' . $id . '?group=payments'));
        }
        mkdir($dir, 0777);
        $this->load->model('payments_model');
        $this->load->model('invoices_model');
        foreach ($payments as $payment) {
            $payment_data               = $this->payments_model->get($payment['paymentid']);
            $payment_data->invoice_data = $this->invoices_model->get($payment_data->invoiceid);
            $this->pdf_zip              = payment_pdf($payment_data);
            $file_name                  = $dir;
            $file_name .= '/' . strtoupper(_l('payment'));
            $file_name .= '-' . strtoupper($payment_data->paymentid) . '.pdf';
            $this->pdf_zip->Output($file_name, 'F');
        }
        $this->load->library('zip');
        // Read the invoices
        $this->zip->read_dir($dir, false);
        // Delete the temp directory for the client
        delete_dir($dir);
        $this->zip->download(slug_it(get_option('companyname')) . '-payments-' . $zip_file_name . '.zip');
        $this->zip->clear_data();
    }
    public function import()
    {
        if (!has_permission('customers', '', 'create')) {
            access_denied('customers');
        }
        $simulate_data  = array();
        $total_imported = 0;
        if ($this->input->post()) {
            if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
                // Get the temp file path
                $tmpFilePath = $_FILES['file_csv']['tmp_name'];
                
                // Make sure we have a filepath
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    // Setup our new file path
                    $ext = strtolower(pathinfo($_FILES['file_csv']['name'], PATHINFO_EXTENSION));
                    $type = $_FILES["file_csv"]["type"];
                    $newFilePath = TEMP_FOLDER . $_FILES['file_csv']['name'];
                    if (!file_exists(TEMP_FOLDER)) {
                        mkdir(TEMP_FOLDER, 777);
                    }
                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        $import_result = true;
                        $fd            = fopen($newFilePath, 'r');
                        $rows          = array();
                        
                        if($ext == 'csv') {
                            while ($row = fgetcsv($fd)) {
                                $rows[] = $row;
                            }
                        }
                        else if($ext == 'xlsx' || $ext == 'xls') {
                            if($type == "application/octet-stream" || $type == "application/vnd.ms-excel" || $type == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
                                require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');

                                $inputFileType = PHPExcel_IOFactory::identify($newFilePath);
                                
                                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                                
                                $objReader->setReadDataOnly(true);
                                
                                /**  Load $inputFileName to a PHPExcel Object  **/
                            $objPHPExcel =           $objReader->load($newFilePath);
                                $allSheetName       = $objPHPExcel->getSheetNames();
                                $objWorksheet       = $objPHPExcel->setActiveSheetIndex(0);
                                $highestRow         = $objWorksheet->getHighestRow();
                                $highestColumn      = $objWorksheet->getHighestColumn();
                                
                                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                                
                                for ($row = 1; $row <= $highestRow; ++$row) {
                                    for ($col = 0; $col < $highestColumnIndex; ++$col) {
                                        $value                     = $objWorksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
                                        $rows[$row - 1][$col] = $value;
                                    }
                                }
                            }
                        }
                        $data['total_rows_post'] = count($rows);
                        fclose($fd);

                        if (count($rows) <= 1) {
                            set_alert('warning', 'Not enought rows for importing');
                            redirect(admin_url('clients/import'));
                        }
                        
                        unset($rows[0]);
                        
                        if ($this->input->post('simulate')) {
                            if (count($rows) > 500) {
                                set_alert('warning', 'Recommended splitting the CSV file into smaller files. Our recomendation is 500 row, your CSV file has ' . count($rows));
                            }
                        }
                        
                        // $client_contacts_fields = $this->db->list_fields('tblcontacts');
                        // $i                      = 0;
                        // foreach ($client_contacts_fields as $cf) {
                        //     if ($cf == 'phonenumber') {
                        //         $client_contacts_fields[$i] = 'contact_phonenumber';
                        //     }
                        //     $i++;
                        // }
                        // $db_temp_fields = $this->db->list_fields('tblclients');
                        // $db_temp_fields = array_merge($client_contacts_fields, $db_temp_fields);
                        // $db_fields      = array();
                        // foreach ($db_temp_fields as $field) {
                        //     if (in_array($field, $this->not_importable_clients_fields)) {
                        //         continue;
                        //     }
                        //     $db_fields[] = $field;
                        // }
                        // $custom_fields = get_custom_fields('customers');
                        $_row_simulate = 0;

                        $required = array(
                            'firstname',
                            'lastname',
                            'email'
                        );

                        if (get_option('company_is_required') == 1) {
                            array_push($required, 'company');
                        }
                        // print_r($rows);
                        // exit();
                        foreach($rows as $row) {
                            $data_customer = array(
                                'company' => $row[0],
                                'client_type' => (!empty($row[1]) ? 1 : 2),
                                'phonenumber' => $row[4],
                                'id_card' => $row[5],
                                'mobilephone_number' => $row[6],
                                'fax' => $row[7],
                                'email' => $row[8],
                                'website' => $row[9],
                                'business' => $row[10],
                                'city' => $row[11],
                                'state' => $row[12],
                                'address_ward' => $row[13],
                                'address_room_number' => $row[14],
                                'address_town' => $row[15],
                                'address' => $row[16],
                                'country' => $row[17],
                                'bussiness_registration_number' => $row[18],
                            );
                            $contact_first_name = 'N/A';
                            $contact_last_name = 'N/A';

                            if(isset($row[19]) && trim($row[19]) != '') {
                                $split_name = split(' ', $row[19]);
                                if(count($split_name) >= 2) {
                                    $contact_first_name = $split_name[0];
                                    $contact_last_name = substr($row[19], strpos($row[19], $contact_first_name) + strlen($contact_first_name) + 1);
                                }
                            }
                            $data_contact = false;
                            if(trim($row[19]) != '') {
                                $data_contact = array(
                                    'userid' => '',
                                    'is_primary' => '1',
                                    'firstname' => $contact_first_name,
                                    'lastname' => $contact_last_name,
                                    'title' => $row[20],
                                    'phonenumber' => $row[21],
                                    'email' => $row[22],
                                    'address' => $row[23],
                                );
                            }
                            
                            // Check exists email
                            $exists_email = $this->db->where('email', $data_customer['email'])->get('tblclients')->row() || $this->db->where('email', $data_customer['email'])->get('tblcontacts')->row() ? true : false ;
                            if($exists_email) {
                                $duplicate = true;
                            }
                            else {
                                $duplicate = false;
                            }
                            if($duplicate) {
                                continue;
                            }
                            
                            // Get country, province, district, ward
                            // Country
                            $this->db->like('short_name', $data_customer['country']);
                            $country = $this->db->get('tblcountries')->row();
                            if($country) {
                                $data_customer['country'] = $country->country_id;
                            }
                            else {
                                $data_customer['country'] = 0;
                            }
                            // City
                            $this->db->like('name', $data_customer['city']);
                            $city = $this->db->get('province')->row();
                            if($city) {
                                $data_customer['city'] = $city->provinceid;
                            }
                            else {
                                $data_customer['city'] = 0;
                            }
                            // district
                            $this->db->like('name', $data_customer['state']);
                            $state = $this->db->get('district')->row();
                            if($state) {
                                $data_customer['state'] = $state->districtid;
                            }
                            else {
                                $data_customer['state'] = 0;
                            }
                            // ward
                            $this->db->like('name', $data_customer['address_ward']);
                            $address_ward = $this->db->get('ward')->row();
                            if($address_ward) {
                                $data_customer['address_ward'] = $address_ward->wardid;
                            }
                            else {
                                $data_customer['address_ward'] = 0;
                            }

                            // Insert database
                            $customer_id = 0;
                            $this->db->insert('tblclients', $data_customer);
                            if($this->db->affected_rows()) {
                                $customer_id = $this->db->insert_id();
                                $total_imported++;
                            }
                            if($customer_id > 0 && $data_contact) {
                                // insert contact
                                $data_contact['userid'] = $customer_id;
                                $this->db->insert('tblcontacts', $data_contact);
                            }
                        }
                        unlink($newFilePath);
                    }
                } else {
                    set_alert('warning', _l('import_upload_failed'));
                }
            }
        }
        if (count($simulate_data) > 0) {
            $data['simulate'] = $simulate_data;
        }
        if (isset($import_result)) {
            set_alert('success', _l('import_total_imported', $total_imported));
        }
        $data['groups']         = $this->clients_model->get_groups();
        $data['not_importable'] = $this->not_importable_clients_fields;
        $data['title']          = 'Import';
        $this->load->view('admin/clients/import', $data);
    }
    public function groups()
    {
        if (!is_admin()) {
            access_denied('Customer Groups');
        }
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('customers_groups');
        }
        $data['title'] = _l('customer_groups');
        $this->load->view('admin/clients/groups_manage', $data);
    }
    public function group()
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            if ($data['id'] == '') {
                $success = $this->clients_model->add_group($data);
                $message = '';
                if ($success == true) {
                    $message = _l('added_successfuly', _l('customer_group'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
            } else {
                $success = $this->clients_model->edit_group($data);
                $message = '';
                if ($success == true) {
                    $message = _l('updated_successfuly', _l('customer_group'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
            }
        }
    }
    public function delete_group($id)
    {
        if (!is_admin()) {
            access_denied('Delete Customer Group');
        }
        if (!$id) {
            redirect(admin_url('clients/groups'));
        }
        $response = $this->clients_model->delete_group($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('customer_group')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('customer_group_lowercase')));
        }
        redirect(admin_url('clients/groups'));
    }

    public function bulk_action()
    {
        do_action('before_do_bulk_action_for_customers');
        $total_deleted = 0;
        if ($this->input->post()) {
            $ids    = $this->input->post('ids');
            $groups = $this->input->post('groups');

            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($this->input->post('mass_delete')) {
                        if ($this->clients_model->delete($id)) {
                            $total_deleted++;
                        }
                    } else {

                        if (!is_array($groups)) {
                            $groups = false;
                        }
                        $this->clients_model->handle_update_groups($id, $groups);
                    }
                }
            }
        }

        if ($this->input->post('mass_delete')) {
            set_alert('success', _l('total_clients_deleted', $total_deleted));
        }
    }

    public  function exportexcel()
    {
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->setTitle('tiêu đề');
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        
        $BStyle = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '111112'),
                'size'  => 11,
                'name'  => 'Times New Roman'
            )
        );

        $objPHPExcel->getActiveSheet()->SetCellValue('A1','CÔNG TY TNHH DUDOFF VIỆT NAM');
        $objPHPExcel->getActiveSheet()->SetCellValue('A2','DANH SÁCH KHÁCH HÀNG')->getStyle('A2')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->getStyle()->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(100);
        $objPHPExcel->getActiveSheet()->mergeCells('A1:N1');
        $objPHPExcel->getActiveSheet()->mergeCells('A2:N2');

        $this->db->select('tblclients.*,tblcontacts.firstname as contact_firstname,tblcontacts.lastname as contact_lastname');
        $this->db->join('tblcontacts','tblcontacts.userid=tblclients.userid','left');
        $client=$this->db->get('tblclients')->result_array();
        $BStyle = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '111112'),
                'size'  => 11,
                'name'  => 'Times New Roman'
            )
        );
        $objPHPExcel->getActiveSheet()->setCellValue('A3','STT')->getStyle('A3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('B3','MÃ KH')->getStyle('B3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('C3','Công ty')->getStyle('C3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('D3','Điện thoại')->getStyle('D3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('E3','% CHIẾT KHẤU')->getStyle('E3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('F3','Liên hệ chính')->getStyle('F3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('G3','Email chính')->getStyle('G3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('H3','Địa chỉ')->getStyle('H3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('I3','Mã Nhân viên')->getStyle('I3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('J3','Hoạt động')->getStyle('J3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('K3','NHÓM KH')->getStyle('K3')->applyFromArray($BStyle);

        foreach($client as $rom=>$value)
        {
            $objPHPExcel->getActiveSheet()->setCellValue('A'.($rom+4),($rom+1));
            $objPHPExcel->getActiveSheet()->setCellValue('B'.($rom+4),$value['userid']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.($rom+4),$value['company']);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('D'.($rom+4),$value['phonenumber']);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.($rom+4),'chiết khấu');
            $objPHPExcel->getActiveSheet()->setCellValue('F'.($rom+4),$value['contact_firstname'].' '.$value['contact_lastname']);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.($rom+4),$value['email']);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.($rom+4),$value['address']);

            $code_staff="";
            $this->db->select('staff_code');
            $this->db->join('tblstaff','tblstaff.staffid=tblcustomeradmins.staff_id')->where('tblcustomeradmins.customer_id',$value['userid']);
            $codestaff=$this->db->get('tblcustomeradmins')->result_array();
            foreach($codestaff as $code)
            {
                $code_staff.=$code['staff_code'];
            }


            $objPHPExcel->getActiveSheet()->setCellValue('I'.($rom+4),$code_staff);
            if($value['active']==1)
            {
                $active='Có';
            }
            else
            {
                $active="Không";
            }
            $objPHPExcel->getActiveSheet()->setCellValue('J'.($rom+4),$active);

            $this->db->select('tblcustomersgroups.name as namegroup');
            $this->db->where('tblcustomergroups_in.customer_id',$value['userid']);
            $this->db->join('tblcustomersgroups','tblcustomersgroups.id=tblcustomergroups_in.groupid');
            $group=$this->db->get('tblcustomergroups_in')->result_array();
            $group_clients="";
            foreach($group as $group_name)
            {
                $group_clients.=$group_name['namegroup'].' ';
            }

            $objPHPExcel->getActiveSheet()->setCellValue('K'.($rom+4),$group_clients);
        }
        $objPHPExcel->getActiveSheet()->freezePane('A3');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Danh_sach_khach_hang.xls"');
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');
        exit();
    }

    public function init_client_care_of($client="")
    {
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('care_of',array('client'=>$client));
        }
    }
    public function care_of($id="")
    {
        if($this->input->post()){
            $data=$this->input->post();
            $data['create_by']=get_staff_user_id();
            if($id!="")
            {
                $this->db->where('id',$id);
                $this->db->update('tblcare_of',$data);
                if($this->db->affected_rows() > 0){
                    echo json_encode(array(
                        'success' => true,
                        'message' => _l('updated_successfuly')
                    ));
                }
            }
            else
            {
                $this->db->insert('tblcare_of',$data);
                $_id=$this->db->insert_id();
                if($_id)
                {
                     echo json_encode(array(
                        'success' => true,
                        'message' => _l('added_successfuly')
                    ));
                }
                else
                {
                    echo json_encode(array(
                        'success' => false,
                        'message' => _l('problem_adding')
                    ));
                }
            }
        }
    }
    public function delete_care_of($id)
    {
        $this->db->where('id',$id);
        $this->db->delete('tblcare_of');
        if($this->db->affected_rows() > 0){
            echo json_encode(array(
                'success' => true,
                'message' => _l('delete_true')
            ));
        }
        else
        {
            echo json_encode(array(
                'success' => false,
                'message' => _l('delete_false')
            ));
        }
    }
    public function delete_client_report($id)
    {
        $this->db->where('id',$id);
        $this->db->delete('tblreport_client');
        if($this->db->affected_rows() > 0){
            echo json_encode(array(
                'success' => true,
                'message' => _l('delete_true')
            ));
        }
        else
        {
            echo json_encode(array(
                'success' => false,
                'message' => _l('delete_false')
            ));
        }
    }
    public function add_report_client($client,$id="")
    {
        if($this->input->post())
        {
            $data=$this->input->post();
            if($id)
            {
                unset($data['client']);
                $this->db->where('id',$id);
                $this->db->update('tblreport_client',$data);
                if($this->db->affected_rows() > 0){
                    echo json_encode(array(
                        'success' => true,
                        'message' => _l('updated_successfuly')
                    ));
                }
            }
            else
            {
                $data['date']=date('Y-m-d');
                $data['id_client']=$client;
                unset($data['client']);
                $data['addedfrom']=get_staff_user_id();
                $this->db->insert('tblreport_client',$data);
                $_id=$this->db->insert_id();
                if($_id)
                {
                    echo json_encode(array(
                        'success' => true,
                        'message' => _l('added_successfuly')
                    ));
                }
                else
                {
                    echo json_encode(array(
                        'success' => false,
                        'message' => _l('problem_adding')
                    ));
                }
            }
        }
    }
    public function delete_report_client($client,$id="")
    {
        $this->db->where('id',$id);
        $this->db->delete('tblreport_client');
        if($this->db->affected_rows() > 0){
            echo json_encode(array(
                'success' => true,
                'message' => _l('delete_comment_client')
            ));
        }
        else
        {
            echo json_encode(array(
                'success' => true,
                'message' => _l('not_delete_comment_client')
            ));
        }
    }
    public function model_comment($id="")
    {
        $data['_client']=$id;
        $this->load->view('admin/clients/modals/report',$data);
    }

}