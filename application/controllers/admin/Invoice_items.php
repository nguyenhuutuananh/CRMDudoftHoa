<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Invoice_items extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('invoice_items_model');
        $this->load->model('category_model');
        $this->load->model('warehouse_model');
    }
    /* List all available items */
    public function index()
    {
        if (!has_permission('items', '', 'view')) {
            access_denied('Invoice Items');
        }

        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('invoice_items');
        }
        $this->load->model('taxes_model');
        $data['taxes']        = $this->taxes_model->get();
        $data['items_groups'] = $this->invoice_items_model->get_groups();
        $data['items_units'] = $this->invoice_items_model->get_units();
        $data['category_1'] = $this->category_model->get_level1();
        $data['lightbox_assets'] = true;
        $data['title'] = _l('invoice_items');
        $this->load->view('admin/invoice_items/manage', $data);
    }
    public function summary() {
        if (!has_permission('items', '', 'view')) {
            access_denied('Invoice Items');
        }
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('invoice_items_summary');
        }
        $data['warehouses']   = $this->warehouse_model->getWarehouses();
        $data['title'] = _l('Kho hàng');
        $this->load->view('admin/invoice_items/summary', $data);
    }
    public function get_tax($id_tax) {
        if (!has_permission('items', '', 'view')) {
            access_denied('Invoice Items');
        }
        if ($this->input->is_ajax_request()) {
            $this->load->model('taxes_model');
            exit(json_encode($this->taxes_model->get($id_tax)));
        }
    }
    /* Edit client or add new client*/
    public function item($id = '')
    {
        if (!has_permission('items', '', 'view')) {
            if ($id != '' && !is_customer_admin($id)) {
                access_denied('customers');
            }
        }
        // Add new without ajax
        if ($this->input->post()) {
            if ($id == '') {
                if (!has_permission('items', '', 'create')) {
                    access_denied('items');
                }
                $data                 = $this->input->post();

                $data['price']=str_replace('.','',$data['price']);
                $data['price_buy']=str_replace('.','',$data['price_buy']);
                $data['minimum_quantity']=str_replace('.','',$data['minimum_quantity']);
                $data['maximum_quantity']=str_replace('.','',$data['maximum_quantity']);
                $data['long_description']=htmlspecialchars_decode($data['long_description']);
                $data['description']=htmlspecialchars_decode($data['description']);
                $data['product_features']=htmlspecialchars_decode($data['product_features']);
                $save_and_add_contact = false;
                // Category 4rd level
                if(is_array($data['category_id'])) {
                    for ($i=count($data['category_id'])-1; $i >= 0 ; $i--) { 
                        if( $data['category_id'][$i] != 0 ) {
                            $data['category_id'] = $data['category_id'][$i];
                            break;
                        }
                    }
                }
                // End
                if (isset($data['save_and_add_contact'])) {
                    unset($data['save_and_add_contact']);
                    $save_and_add_contact = true;
                }
                $id = $this->invoice_items_model->add($data);
                if (!has_permission('items', '', 'view')) {
                    $assign['customer_admins']   = array();
                    $assign['customer_admins'][] = get_staff_user_id();
                    $this->invoice_items_model->assign_admins($assign, $id);
                }
                if ($id) {
                    handle_item_avatar_image_upload($id);
                    set_alert('success', _l('added_successfuly', _l('als_products')));
                    //redirect(admin_url('invoice_items/item/' . $id . '?new_contact=true'));
                    if(get_option('prefix_add_continuous')==0)
                    {
                        redirect(admin_url('invoice_items'));
                    }
                    else
                    {
                        redirect(admin_url('invoice_items/item'));
                    }
                }
            } else {
                if (!has_permission('invoice_items', '', 'edit')) {
                    if (!is_customer_admin($id)) {
                        access_denied('invoice_items');
                    }
                }
                $data = $this->input->post();
                $data['price']=str_replace('.','',$data['price']);
                $data['price_buy']=str_replace('.','',$data['price_buy']);
                $data['minimum_quantity']=str_replace('.','',$data['minimum_quantity']);
                $data['maximum_quantity']=str_replace('.','',$data['maximum_quantity']);
                $data['long_description']=htmlspecialchars_decode($data['long_description']);
                $data['description']=htmlspecialchars_decode($data['description']);
                $data['product_features']=htmlspecialchars_decode($data['product_features']);

                $data['itemid'] = $id;
                $item = $this->invoice_items_model->get_full($id);
                if(is_array($data['category_id'])) {
                    for ($i=count($data['category_id'])-1; $i >= 0 ; $i--) { 
                        if( $data['category_id'][$i] != 0 ) {
                            $data['category_id'] = $data['category_id'][$i];
                            break;
                        }
                    }
                }
                
                $success = $this->invoice_items_model->edit($data, $item);
                $success_avatar = handle_item_avatar_image_upload($id);
                if ($success == true || $success_avatar == true) {
                    set_alert('success', _l('updated_successfuly', _l('als_products')));
                }
                redirect(admin_url('invoice_items/item/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('als_products'));
            $array_categories[] = array(0, $this->invoice_items_model->get_same_level_categories(0));
            $array_categories[1] = array(0, array());
            $array_categories[2] = array(0, array());
            $array_categories[3] = array(0, array());
            $data['array_categories'] = $array_categories;
            
        } else {
            $title = _l('invoice_item_edit_heading');
            $item = $this->invoice_items_model->get_full($id);
            $array_categories = [];

            $array_categories[] = array($item->category_id, $this->invoice_items_model->get_same_level_categories($item->category_id));
            $this->invoice_items_model->get_category_parent_id($item->category_id, $array_categories);
            
            if(count($array_categories) < 4) {
                if(!isset($array_categories[1])) {
                    $array_categories[1] = array(0, array());
                }
                if(!isset($array_categories[2])) {
                    $array_categories[2] = array(0, array());
                }
                if(!isset($array_categories[3])) {
                    $array_categories[3] = array(0, array());
                }
            }
            if (!$item) {
                blank_page('Client Not Found');
            }
            $data['array_categories'] = $array_categories;
            $data['item'] = $item;
        }

        $data['lightbox_assets'] = true;
        $data['title'] = $title;
        $this->load->view('admin/invoice_items/item_details', $data);
    }
    public function get_categories($id=0) {
        if (!has_permission('items', '', 'view')) {
            if ($id != '' && !is_customer_admin($id)) {
                access_denied('customers');
            }
        }
        echo json_encode($this->invoice_items_model->get_categories($id));
    }
    public function get_invoice_item_attachment($id) {
        if (!has_permission('items', '', 'view')) {
            if ($id != '' && !is_customer_admin($id)) {
                access_denied('customers');
            }
        }
        if(is_numeric($id)) {
            $item = $this->invoice_items_model->get_full($id);
            if($item) {
                $this->load->view('admin/invoice_items/item_attachments_template', array('attachments'=>$item->attachments));
            }
        }
        
    }
    public function delete_attachment($id)
    {
        echo json_encode(array(
            'success' => $this->invoice_items_model->delete_invoice_item_attachment($id)
        ));
    }
    public function add_item_attachment()
    {
        $item_id = $this->input->post('leadid');
        echo json_encode(handle_invoice_attachments($item_id));
    }
    public function price_history($id = '') {
        if (!has_permission('items', '', 'view')) {
            if ($id != '' && !is_customer_admin($id)) {
                access_denied('customers');
            }
        }
        if($id!='') {
            if($this->input->is_ajax_request()) {
                $this->perfex_base->get_table_data('item_price_history', array(
                    'rel_id' => $id,
                )); 
            }
        }
    }
    public function price_buy_history($id = '') {
        if (!has_permission('items', '', 'view')) {
            if ($id != '' && !is_customer_admin($id)) {
                access_denied('customers');
            }
        }
        if($id!='') {
            
            if($this->input->is_ajax_request()) {
                $this->perfex_base->get_table_data('item_price_buy_history', array(
                    'rel_id' => $id,
                )); 
            }
        }
    }
    /* Edit or update items / ajax request /*/
    public function manage()
    {
        if (has_permission('items', '', 'view')) {
            if ($this->input->post()) {
                $data = $this->input->post();
                
                if ($data['itemid'] == '') {
                    if (!has_permission('items', '', 'create')) {
                        header('HTTP/1.0 400 Bad error');
                        echo _l('access_denied');
                        die;
                    }
                    $id      = $this->invoice_items_model->add($data);
                    $success = false;
                    $message = '';
                    if ($id) {
                        $success = true;
                        $message = _l('added_successfuly', _l('invoice_item'));
                    }
                    echo json_encode(array(
                        'success' => $success,
                        'message' => $message,
                        'item' => $this->invoice_items_model->get($id)
                    ));
                } else {
                    if (!has_permission('items', '', 'edit')) {
                        header('HTTP/1.0 400 Bad error');
                        echo _l('access_denied');
                        die;
                    }
                    $success = $this->invoice_items_model->edit($data);
                    $message = '';
                    if ($success) {
                        $message = _l('updated_successfuly', _l('invoice_item'));
                    }
                    echo json_encode(array(
                        'success' => $success,
                        'message' => $message
                    ));
                }
            }
        }
    }
    public function add_landtype()
    {

        if ($this->input->post() && has_permission('items', '', 'create')) {

            $this->invoice_items_model->add_landtype($this->input->post());
            set_alert('success', _l('added_successfuly', 'Loại nhà đất'));
        }
    }
    public function add_group()
    {

        if ($this->input->post() && has_permission('items', '', 'create')) {
            $this->invoice_items_model->add_group($this->input->post());
            set_alert('success', _l('added_successfuly', _l('item_group')));
        }
    }

    public function update_group($id)
    {
        if ($this->input->post() && has_permission('items', '', 'edit')) {
            $this->invoice_items_model->edit_group($this->input->post(), $id);
            set_alert('success', _l('updated_successfuly', _l('item_group')));
        }
    }
    public function update_landtype($id)
    {
        if ($this->input->post() && has_permission('items', '', 'edit')) {
            $this->invoice_items_model->edit_landtype($this->input->post(), $id);
            set_alert('success', _l('updated_successfuly', "Loại nhà đất"));
        }
    }
    public function delete_group($id)
    {
        if (has_permission('items', '', 'delete')) {
            if ($this->invoice_items_model->delete_group($id)) {
                set_alert('success', _l('deleted', _l('item_group')));
            }
        }
        redirect(admin_url('invoice_items?groups_modal=true'));
    }
    public function delete_landtype($id)
    {
        if (has_permission('items', '', 'delete')) {
            if ($this->invoice_items_model->delete_landtype($id)) {
                set_alert('success', _l('deleted', 'Loại nhà đất'));
            }
        }
        redirect(admin_url('invoice_items?groups_modal=true'));
    }
    /* Delete item*/
    public function delete($id)
    {
        if (!has_permission('items', '', 'delete')) {
            access_denied('Invoice Items');
        }

        if (!$id) {
            redirect(admin_url('invoice_items'));
        }

        $response = $this->invoice_items_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('invoice_item_lowercase')));
        } else if ($response == true) {
            set_alert('success', _l('deleted', _l('invoice_item')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('invoice_item_lowercase')));
        }
        redirect(admin_url('invoice_items'));
    }
    /* Get item by id / ajax */
    public function get_item_by_id($id)
    {
        if ($this->input->is_ajax_request()) {
            
            $item                   = $this->invoice_items_model->get_full($id);
            $item->long_description = nl2br($item->long_description);
            echo json_encode($item);
        }
    }

    /* Get all items */
    public function get_all_items_ajax()
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->invoice_items_model->get_all_items_ajax());
        }
    }
    public function import()
    {
        $total_imported = 0;
        $load_result = false;
        $alert = [
            'success' => 0,
            'fail'    => [],
        ];
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
                                
                                // $objReader->setReadDataOnly(true);
                                
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
                                        $rows[$row - 2][$col] = $value;
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

                        // Works with difficulty
                        $query_array = [];
                        $backup_rows = $rows;

                        $result_array = [];
                        $important_columns = array(
                            'code'                      => array('Mã',                  -1),
                            'name'                      => array('Tên',                 -1),
                            'short_name'                => array('Tên ngắn',            -1),
                            'description'               => array('Miêu tả',             -1),
                            'long_description'          => array('Miêu tả dài',         -1),
                            'unit'                      => array('Đơn vị',              -1),
                            'group_id'                  => array('Nhóm',                -1),
                            'release_date'              => array('Ngày công bố',        -1),
                            'date_of_removal_of_sample' => array('Ngày bỏ mẫu',         -1),
                            'country_id'                => array('Xuất xứ',             -1),
                            'specification'             => array('Quy cách',            -1),
                            'size'                      => array('Kích thước',          -1),
                            'weight'                    => array('Trọng lượng',         -1),
                            'product_features'          => array('Đặc tính sản phẩm',   -1),
                            'price'                     => array('Giá bán',             -1),
                            'price_buy'                 => array('Giá nhập',            -1),
                            'minimum_quantity'          => array('Số lượng tối thiểu',  -1),
                            'maximum_quantity'          => array('Số lượng tối đa',     -1),
                            'quantity'                  => array('Số lượng',            -1),
                            'category_id'               => array('Danh mục',            -1),
                        );
                        $fetch_columns_step = true;
                        $fetch_product_step = false;
                        $columns_found = 0;
                        $product_count = 0;
                        foreach($rows as $row) {
                            if($fetch_columns_step) {
                                $stt=0;
                                foreach($important_columns as $column_key=>$column_value) {
                                    // Nếu bảng tính không có cột để xét thì thoát, kết quả sẽ không thể nhập
                                    if(!isset($row[$stt])){
                                        exit("what");
                                        break;
                                    }
                                        
                                    // Kiểm tra nếu nội dung của ô bằng với nội dung cột cần nhập
                                    if(trim($row[$stt]) == trim($column_value[0])) {
                                        $columns_found++;
                                    }
                                    else {
                                        // var_dump(trim($row[$stt]), trim($column_value[0]));
                                    }
                                    // Nếu tìm được đủ cột không tìm nữa và bắt đầu chạy thêm sản phẩm
                                    if($columns_found >= count($important_columns)) {
                                        $fetch_columns_step = false;
                                        $fetch_product_step = true;
                                        break;
                                    }
                                    $stt++;
                                }
                                continue;
                            }
                            if($fetch_product_step) {
                                $product_count++;
                                $data = [];
                                $stt = 0;
                                $data_ok = true;
                                $reason = "";
                                // Gán từng ô là field tương ứng trong csdl
                                foreach($important_columns as $column_key=>$column_value) {
                                    if($column_key == 'group_id') {
                                        $all_groups = get_item_groups();
                                        $result_search = false;
                                        
                                        foreach($all_groups as $key=>$group) {
                                            if(trim($group['name']) == trim($row[$stt])) {
                                                $result_search = $key;
                                                break;
                                            }
                                        }
                                        if($result_search !== false) {
                                            $data[$column_key] = $all_groups[$result_search]['name'];
                                        }
                                        else {
                                            $reason .= "Không tìm thấy " . $column_value[0] . " ".$row[$stt] ."<br />";
                                            $data_ok = false;
                                        }
                                    }
                                    if($column_key == 'country_id') {
                                        $all_countries = get_all_countries();
                                        $result_search = false;
                                        foreach($all_countries as $key=>$country) {
                                            if(trim($country['short_name']) == trim($row[$stt])) {
                                                $result_search = $key;
                                                break;
                                            }
                                        }
                                        
                                        if($result_search !== false) {
                                            $data[$column_key] = $all_countries[$result_search]['country_id'];
                                        }
                                        else {
                                            $reason .= "Không tìm thấy " . $column_value[0] . " ".$row[$stt] ."<br />";
                                            $data_ok = false;
                                        }
                                    }
                                    if($column_key == 'unit') {
                                        $all_units = get_units();
                                        $result_search = false;
                                        foreach($all_units as $key=>$unit) {
                                            if(trim($unit['unit']) == trim($row[$stt])) {
                                                $result_search = $key;
                                                break;
                                            }
                                        }
                                        
                                        if($result_search !== false) {
                                            $data[$column_key] = $all_units[$result_search]['unitid'];
                                        }
                                        else {
                                            $reason .= "Không tìm thấy " . $column_value[0] . " ".$row[$stt] ."<br />";
                                            $data_ok = false;
                                        }
                                    }
                                    if($column_key == 'category_id') {
                                        $category_name = trim($row[$stt]);
                                        $category = $this->category_model->get_single_by_name($category_name);
                                        if($category)
                                            $data[$column_key] = $category->id;
                                        else {
                                            $reason .= "Không tìm thấy " . $column_value[0] . " ".$category_name ."<br />";
                                            $data_ok = false;
                                        }
                                    }
                                    if($column_key == 'release_date' || $column_key == 'date_of_removal_of_sample') {
                                        $data[$column_key] = date("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($row[$stt]));
                                    }
                                    if($data[$column_key] == '') {
                                        $data[$column_key] = $row[$stt];
                                    }
                                    $stt++;
                                }
                                if($data_ok) {
                                    $this->db->insert('tblitems',$data);
                                    if($this->db->affected_rows() > 0) {
                                        $alert['success']++;
                                    }
                                    else {
                                        $alert['fail'][] = [$product_count, array_values($data)[0], $reason];
                                    }
                                }
                                else {
                                    $alert['fail'][] = [$product_count, array_values($data)[0], $reason];
                                }
                            }
                        }
                        // var_dump($fetch_columns_step, $fetch_product_step, $columns_found, $alert);
                        // exit();
                        $data['message'] = "
                            Nhập thành công " . $alert['success'] . " sản phẩm. <br />
                        ";
                        if(count($alert['fail']) > 0) {
                            foreach($alert['fail'] as $item) {
                                $data['message'] .= "Dòng ".$item[0]." gặp lỗi ".$item[2];
                            }
                        }
                    }
                } else {
                    set_alert('warning', _l('import_upload_failed'));
                }
            }
            
        }
        
        if (isset($load_result) && $load_result == true) {
            set_alert('success', _l('load_import_success'));
        }

        $data['title']          = 'Import';
        $this->load->view('admin/invoice_items/import', $data);
    }


    public function exportexcel()
    {
        $this->db->select('tblitems.*,tblunits.unit as n_unit,tblitems_groups.name as name_groups');
        $this->db->join('tblunits','tblunits.unitid=tblitems.unit','left')
            ->join('tblitems_groups','tblitems_groups.id = tblitems.group_id','left');
        $items=$this->db->get('tblitems')->result_array();
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        // $objPHPExcel->getActiveSheet()->setTitle('tiêu đề');
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);

        $colum_array=array('I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
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
        for($row = 1; $row <= 100; $row++)
        {
            $styleArray = [
                'font' => [
                    'size' => 12
                ]
            ];
            $objPHPExcel->getActiveSheet()
                ->getStyle("A1:N2")
                ->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->SetCellValue('A1','CÔNG TY TNHH DUDOFF VIỆT NAM');
            $objPHPExcel->getActiveSheet()->SetCellValue('A2','DANH SÁCH SẢN PHẨM')->getStyle('A2')->applyFromArray($BStyle);
            $objPHPExcel->getActiveSheet()->getStyle()->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(100);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:N1');
            $objPHPExcel->getActiveSheet()->mergeCells('A2:N2');
        }

        $objPHPExcel->getActiveSheet()->setCellValue('A3','STT')->getStyle('A3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('B3','ẢNH ĐẠI DIỆN')->getStyle('B3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('C3','MÃ SỐ SẢN PHẨM')->getStyle('C3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('D3','TÊN HÀNG HÓA')->getStyle('D3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('E3','TÊN NGẮN')->getStyle('E3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('F3','MÔ TẢ CHỨC NĂNG')->getStyle('F3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('G3','ĐẶC TÍNH SẢN PHẨM')->getStyle('G3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('H3','KÍCH THƯỚC')->getStyle('H3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('I3','QUY CÁCH')->getStyle('I3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('J3','TRỌNG LƯỢNG')->getStyle('J3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('K3','GIÁ BÁN')->getStyle('k3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('L3','ĐƠN VỊ TÍNH')->getStyle('L3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('M3','NHÓM')->getStyle('M3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('N3','SỐ LƯỢNG TỐI THIỂU')->getStyle('N3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('O3','SỐ LƯỢNG TỐI ĐA')->getStyle('O3')->applyFromArray($BStyle);
        foreach($items as $rom => $item)
        {
            $objPHPExcel->getActiveSheet()->setCellValue('A'.($rom+4),($rom+1));
            $objPHPExcel->getActiveSheet()->setCellValue('B'.($rom+4),$item['avatar']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.($rom+4),$item['code']);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.($rom+4),$item['name']);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.($rom+4),$item['short_name']);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.($rom+4),htmlspecialchars_decode (strip_tags($item['description']),ENT_QUOTES));
            $objPHPExcel->getActiveSheet()->setCellValue('G'.($rom+4),strip_tags($item['product_features']));
            $objPHPExcel->getActiveSheet()->setCellValue('H'.($rom+4),$item['size']);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.($rom+4),$item['specification']);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.($rom+4),$item['weight']);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.($rom+4),$item['price']);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.($rom+4),$item['n_unit']);
            $objPHPExcel->getActiveSheet()->setCellValue('M'.($rom+4),$item['name_groups']);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.($rom+4),$item['minimum_quantity']);
            $objPHPExcel->getActiveSheet()->setCellValue('O'.($rom+4),$item['maximum_quantity']);
        }
        $objPHPExcel->getActiveSheet()->freezePane('A4');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
        header('Content-Encoding: UTF-8');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Danh_sach_San_Pham.xls"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
        exit();


    }
}