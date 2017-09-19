<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Warehouses extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('projects_model');
        $this->load->model('warehouse_model');
        $this->load->model('kind_of_warehouse_model');
        $this->load->model('category_model');
    }
    /* Open also all taks if user access this /tasks url */
    public function index()
    {

        $this->list_warehouses();
    }
    public function get_all_products($category_id) {
        if (!is_admin()) {
            access_denied('contracts');
        }
        if ($this->input->is_ajax_request()) {
            exit(json_encode($this->warehouse_model->get_products($category_id)));
        }
    } 
    /* List all tasks */
    public function list_warehouses()
    {
        if (!is_admin()) {
            access_denied('contracts');
        }
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('warehouses');
        }
        $data['roles']=$this->warehouse_model->get_roles();
        $data['title'] = _l('Kho hàng');
        $data['kind_of_warehouse'] = $this->kind_of_warehouse_model->get_array_list();
        $data['categories'] = [];
        $this->category_model->get_by_id(0,$data['categories']);

        $this->load->view('admin/warehouses/manage', $data);
    }
    /* Get task data in a right pane */
    public function delete_warehouse($id)
    {
        if (!$id) {
            die('Không tìm thấy mục nào');
        }
        $success    = $this->warehouse_model->delete_warehouse($id);
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
    public function add_warehouse()
    {
        if ($this->input->post()) {
            $message = '';
                $id = $this->warehouse_model->add_warehouse($this->input->post(NULL, FALSE));
                if ($id) {
                    $success = true;
                    $message = _l('added_successfuly', _l('als_categories'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
            die;
        }
    }
    public function update_warehouse($id="")
    {
        if($id!=""){
            $message    = '';
            $alert_type = 'warning';
            if ($this->input->post()) {
                $success = $this->warehouse_model->update_warehouse($this->input->post(), $id);
                if ($success) {
                    $message    = 'Cập nhật dữ liệu thành công';
                };
            }
            echo json_encode(array(
                'success' => $success,
                'message' => $message
            ));
        }
        else
        {
            if ($this->input->post()) {
                $success = $this->warehouse_model->add_warehouse($this->input->post());
                if ($success) {
                    $alert_type = 'success';
                    $message    = 'Thêm dữ liệu thành công';
                }
            }
            echo json_encode(array(
                'alert_type' => $alert_type,
                'message' => $message
            ));
        }
        die;
    }
    public function get_row_warehouse($id)
    {
        echo json_encode($this->warehouse_model->get_row_warehouse($id));
    }
    public function detail($id) {
        $warehouse = $this->warehouse_model->get_full($id);

        if( $id != '' && $warehouse) {
            
            $data['title'] = _l('purchase_suggested_edit_heading');
            $data['warehouse'] = $warehouse;
            $this->load->view('admin/warehouses/detail', $data);
        }
        else {
            redirect(admin_url('warehouses'));
        }
    }
    // public function modal_detail($id) {
    //     $warehouse = $this->warehouse_model->get_full($id);

    //     if( $id != '' && $warehouse) {
    //         $result = new stdClass();
    //         $data['warehouse'] = $warehouse;
    //         $result->body = $this->load->view('admin/warehouses/modal_detail', $data, TRUE);
    //         $result->header = _l('warehouse_info') . " " . $warehouse->warehouse;
    //         exit(json_encode($result));
    //     }
    // }
    public function modal_detail($id) {
        if($this->input->is_ajax_request() && !$this->input->get('get')) {
            $this->perfex_base->get_table_data('warehouse_detail', array('warehouse_id' => $id));
        }
        $warehouse = $this->warehouse_model->get_full($id);

        if( $id != '' && $warehouse) {
            $result = new stdClass();
            $data['warehouse'] = $warehouse;
            $data['categories'] = [];
            $this->category_model->get_by_id(0,$data['categories']);
            $data['products_in_warehouse'] = $this->warehouse_model->get_products_in_warehouse($id);
            $product_category = array();
            $product_outof_date = 0;
            $product_low_quantity = 0;
            
            foreach($data['products_in_warehouse'] as $key=>$value) {
                if(!in_array($value['category_id'], $product_category)) {
                    array_push($product_category, $value['category_id']);
                }
                
            }
            $data['product_category'] = $product_category;
            $data['product_outof_date'] = $product_outof_date;
            $data['product_low_quantity'] = $product_low_quantity;

            $result->body = $this->load->view('admin/warehouses/modal_detail', $data, TRUE);
            $result->header = _l('warehouse_info') . " " . $warehouse->warehouse;
            exit(json_encode($result));
        }
    }

    public function getWarehouses($warehouse_type,$filterByProduct='', $includeDoesntContain=false) {
        if(is_numeric($warehouse_type) && $this->input->is_ajax_request()) {
            echo json_encode($this->warehouse_model->getWarehousesByType($warehouse_type, $filterByProduct, $includeDoesntContain));
        }
    }

    public function getQuantityPIW($warehouse_id=NULL,$product_id=NULL) {
        if(is_numeric($warehouse_id) && is_numeric($product_id) && $this->input->is_ajax_request()) {
            echo json_encode($this->warehouse_model->getQuantityProductInWarehouses($warehouse_id, $product_id));
        }
    }

    public function getProductQuantity($warehouse_id,$product_id) {
        if(is_numeric($product_id) && is_numeric($warehouse_id) && $this->input->is_ajax_request()) {
            echo json_encode($this->warehouse_model->getProductQuantity($warehouse_id,$product_id));
        }
    }

    public function getProductsInWH($warehouse_id) {
        if(is_numeric($warehouse_id) && $this->input->is_ajax_request()) {
            echo json_encode($this->warehouse_model->getProductsByWarehouseID($warehouse_id));
        }
    }
    public  function exportexcel()
    {
        $categori=$this->db->get('tblcategories')->result_array();

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

        $this->db->select('tblclients.*,tblcontacts.firstname as contact_firstname,tblcontacts.lastname as contact_lastname');
        $this->db->join('tblcontacts','tblcontacts.userid=tblclients.userid','left');
        $client=$this->db->get('tblclients')->result_array();
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
            $objPHPExcel->getActiveSheet()->SetCellValue('A2','DANH SÁCH KHO')->getStyle('A2')->applyFromArray($BStyle);
            $objPHPExcel->getActiveSheet()->getStyle()->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(100);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:N1');
            $objPHPExcel->getActiveSheet()->mergeCells('A2:N2');
        }
        $objPHPExcel->getActiveSheet()->setCellValue('A3','ID')->getStyle('A3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('B3','STT')->getStyle('B3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('C3','MÃ SẢN PHẨM')->getStyle('C3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('D3','SẢN PHẨM')->getStyle('D3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('E3','ĐƠN VỊ TÍNH')->getStyle('E3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('F3','GIÁ BÁN')->getStyle('F3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('G3','GIÁ VỐN')->getStyle('G3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('H3','HÀNG CÓ THỂ BÁN')->getStyle('H3')->applyFromArray($BStyle);
        $warehouses=$this->db->get('tblwarehouses')->result_array();
        foreach($warehouses as $num_ware=> $warehouse)
        {
            $objPHPExcel->getActiveSheet()->setCellValue($colum_array[$num_ware].'3',$warehouse['warehouse'])->getStyle($colum_array[$num_ware].'3')->applyFromArray($BStyle);
        }
        $rom=3;
        foreach($categori as $rom_cate => $value_categori)
        {
            $this->db->select('tblitems.*,tblunits.unit as name_unit');
            $this->db->where('category_id',$value_categori['id']);
            $this->db->join('tblunits','tblunits.unitid=tblitems.unit','left');
            $product=$this->db->get('tblitems')->result_array();
            if($product!=array()){

                $rom++;
                for($row = 1; $row <= 100; $row++)
                {
                    $styleArray = [
                        'font' => [
                            'size' => 12
                        ]
                    ];
                    $objPHPExcel->getActiveSheet()
                        ->getStyle("A".$rom.":N".$rom)
                        ->applyFromArray($styleArray);
                    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rom,$value_categori['category']);
                    $objPHPExcel->getActiveSheet()->getStyle()->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(100);
                    $objPHPExcel->getActiveSheet()->mergeCells("A".$rom.":N".$rom);
                }
                foreach($product as $r=>$value)
                {
                    $rom=($rom+1);
                    $objPHPExcel->getActiveSheet()->setCellValue('A'.$rom,$value['id']);
                    $objPHPExcel->getActiveSheet()->setCellValue('B'.$rom,($r+1));
                    $objPHPExcel->getActiveSheet()->setCellValue('C'.$rom,$value['code']);
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('D'.$rom,$value['name']);
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$rom,$value['name_unit']);
                    $objPHPExcel->getActiveSheet()->setCellValue('F'.$rom,$value['price']);
                    $objPHPExcel->getActiveSheet()->setCellValue('G'.$rom,$value['price_buy']);
                    $objPHPExcel->getActiveSheet()->setCellValue('H'.$rom,$value['price_buy']);
                    foreach($warehouses as $num_ware=> $warehouse)
                    {
                        $this->db->where('tblwarehouses_products.product_id',$value['id']);
                        $this->db->where('tblwarehouses_products.warehouse_id',$warehouse['warehouseid']);
                        $this->db->join('tblwarehouses','tblwarehouses.warehouseid=tblwarehouses_products.warehouse_id');
                        $warehouse_product=$this->db->get('tblwarehouses_products')->row();
                        if($warehouse_product)
                        {
                            $objPHPExcel->getActiveSheet()->setCellValue($colum_array[$num_ware].$rom,$warehouse_product->product_quantity);
                        }
                    }

                }
            }
        }
//        die();
        $objPHPExcel->getActiveSheet()->freezePane('A4');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Danh_sach_Kho.xls"');
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');
        exit();


    }
}