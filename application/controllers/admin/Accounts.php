<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Accounts extends Admin_controller {
    function __construct() {
        parent::__construct();
        $this->load->model('accounts_model');
    }
    public function index() {
        if (!has_permission('customers', '', 'view')) {
            if ($id != '' && !is_customer_admin($id)) {
                access_denied('customers');
            }
        }
        $data['title'] = "Tài khoản kế toán";
        $accounts = $this->accounts_model->get_accounts(array(), true);
        $accountAttributes = $this->accounts_model->get_account_attributes(true);
        $data['accounts'] = $accounts;
        $data['accountAttributes'] = $accountAttributes;

        if($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('account');
        }
        $this->load->view('admin/accounts/manage', $data);
    }
    public function getAccounts() {
        if (!has_permission('customers', '', 'view')) {
            if ($id != '' && !is_customer_admin($id)) {
                access_denied('customers');
            }
        }
        if($this->input->is_ajax_request()) {
            $accounts = $this->accounts_model->get_accounts_tree();
            exit(json_encode($accounts));
        }
    }
    public function get_row($id) {
        if (!has_permission('customers', '', 'view')) {
            if ($id != '' && !is_customer_admin($id)) {
                access_denied('customers');
            }
        }
        if($this->input->is_ajax_request()) {
            $account = $this->accounts_model->get_single($id);
            exit(json_encode($account));
        }
    }
    public function ajax($id='') {
        if (!has_permission('customers', '', 'view')) {
            if ($id != '' && !is_customer_admin($id)) {
                access_denied('customers');
            }
        }
        $result = new stdClass();
        $result->success = false;
        $result->message = 'Xử lý không thành công!';
        if($this->input->is_ajax_request() && $this->input->post()) {
            $data = $this->input->post();
            if($id!='') {
                $result->success = $this->accounts_model->edit($id, $data);
                if($result->success)
                {
                    $result->message = "Sửa thành công";
                }
            }
            else {
                $result->success = $this->accounts_model->add($data);
                if($result->success)
                {
                    $result->message = "Thêm thành công";
                }
            }
        }
        exit(json_encode($result));
    }
    public function attributes() {
        if (!has_permission('customers', '', 'view')) {
            if ($id != '' && !is_customer_admin($id)) {
                access_denied('customers');
            }
        }
        if($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('account_attributes');
        }
    }

    public function delete($id='') {
        if (!$id) {
            die('Không tìm thấy mục nào');
        }
        // $success    = $this->accounts_model->delete($id);
        $success = false;
        $alert_type = 'warning';
        $message    = _l('Không thể xóa dữ liệu');
        if ($success) {
            $alert_type = 'success';
            $message    = _l('Xóa dữ liệu thành công');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }
    
    public function import() {
        $total_imported = 0;
        $load_result = false;
        if ($this->input->post()) {
            if (isset($_FILES['file_import']['name']) && $_FILES['file_import']['name'] != '') {
                // Get the temp file path
                $tmpFilePath = $_FILES['file_import']['tmp_name'];
                $ext = strtolower(pathinfo($_FILES['file_import']['name'], PATHINFO_EXTENSION));
                $type = $_FILES["file_import"]["type"];
                // Make sure we have a filepath
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    // Setup our new file path
                    $newFilePath = TEMP_FOLDER . $_FILES['file_import']['name'];
                    if (!file_exists(TEMP_FOLDER)) {
                        mkdir(TEMP_FOLDER, 777);
                    }
                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        $load_result = true;
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
                                        $value                     = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                                        $rows[$row - 1][$col] = $value;
                                    }
                                }
                            }
                        }
                        else {
                            fclose($fd);
                            unlink($newFilePath);
                            redirect('/');
                        }

                        fclose($fd);
                        $data['total_rows_post'] = count($rows);
                        unlink($newFilePath);
                        
                        // Demo xóa 2 row đầu
                        unset($rows[0]);
                        unset($rows[1]);
                        usort($rows, function($a, $b) {
                            if($a[0] >= $b[0]) {
                                return 1;
                            }
                            else {
                                return 0;
                            }
                        });
                        // print_r($rows);
                        // exit();
                        foreach($rows as $row_array) {
                            // get parent
                            $id_parent = substr($row_array[0], 0, -1);
                            
                            if(is_numeric($id_parent)) {
                                $data_array = array(
                                    'accountCode' => $row_array[0],
                                    'accountName' => $row_array[1],
                                    'accountEnglishName' => $row_array[3],
                                    'generalAccount' => 0,
                                    'idAccountAttribute' => $row_array[2],
                                    'accountExplain' => ($row_array[4] == null ? '' : $row_array[4]),
                                );
                                // find attribute
                                $this->db->like('attributeName', $row_array[2]);
                                $attribute = $this->db->get('tblaccount_attributes')->row();
                                
                                // find parent
                                $this->db->where('accountCode', $id_parent);
                                $parent = $this->db->get('tblaccounts')->row();
                                if($attribute) {
                                    if($parent) {
                                        $data_array['generalAccount'] = $parent->idAccount;
                                    }
                                    $data_array['idAccountAttribute'] = $attribute->idAttribute;
                                    
                                    $this->db->insert('tblaccounts', $data_array);
                                    if($this->db->affected_rows() > 0) {
                                        $total_imported++;
                                    }
                                }
                            }
                        }
                        exit();

                    }
                } else {
                    set_alert('warning', _l('import_upload_failed'));
                }
            }
            
        }

        $data['title']          = 'Import tài khoản';
        $this->load->view('admin/accounts/import', $data);
    }
}