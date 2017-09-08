<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Kind_of_warehouse extends Admin_controller
{
    function __construct() {
        parent::__construct();
        $this->load->model('kind_of_warehouse_model');
    }
    public function index() {
        if (!is_admin()) {
            access_denied('contracts');
        }
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('kind_of_warehouses');
        }
        $data['title'] = _l('Loại kho');
        $this->load->view('admin/kind_of_warehouse/manage', $data);
    }
    public function get_row($id) {
        $item = $this->kind_of_warehouse_model->get_row($id);
        if($item)
            exit(json_encode($item));
        else {
            echo false;
        }
    }
    public function add() {
        if (!is_admin()) {
            access_denied('contracts');
        }
        if($this->input->is_ajax_request() && $this->input->post()) {
            $data = $this->input->post();
            $this->db->insert('tbl_kindof_warehouse', $data);
            $success = true;
            $message = "Thêm thành công!";
        }

        exit(json_encode(array(
                'alert_type' => $success,
                'message' => $message
            )));
    }
    public function update($id) {
        if (!is_admin()) {
            access_denied('contracts');
        }
        $this->db->where('id', $id);
        $item = $this->db->get('tbl_kindof_warehouse')->row();
        if($item && $this->input->post()) {
            $this->db->where('id', $id);
            $data = $this->input->post();
            $this->db->update('tbl_kindof_warehouse', $data);
            $success = true;
            $message = "Cập nhật thành công!";
        }
        exit(json_encode(array(
                'alert_type' => $success,
                'message' => $message
            )));
    }
    public function delete($id) {
        if (!is_admin()) {
            access_denied('contracts');
        }
        if($this->input->is_ajax_request()) {
            $this->db->where('kindof_warehouse', $id);
            $items = $this->db->get('tblwarehouses')->result();

            if(count($items) == 0) {
                $this->db->where('id', $id);
                $this->db->delete('tbl_kindof_warehouse');

                $success = true;
                $message = "Cập nhật thành công!";
            }
            else
                $success = false;
        }
        exit(json_encode(array(
                'alert_type' => $success,
                'message' => $message
            )));
    }
    public function exportexcel()
    {
        $this->db->join('tbl_kindof_warehouse','tbl_kindof_warehouse.id=tblwarehouses.kindof_warehouse');
        $warehouses=$this->db->get('tblwarehouses')->result_array();

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
            $objPHPExcel->getActiveSheet()->SetCellValue('A2','DANH SÁCH LOẠI KHO')->getStyle('A2')->applyFromArray($BStyle);
            $objPHPExcel->getActiveSheet()->getStyle()->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(100);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:N1');
        }
        $objPHPExcel->getActiveSheet()->setCellValue('A3','STT')->getStyle('A3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('B3','MÃ KHO')->getStyle('B3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('C3','TÊN LOẠI KHO')->getStyle('C3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('D3','ĐỊA CHỈ')->getStyle('D3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('E3','BỘ PHẬN QUẢN LÝ')->getStyle('E3')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->setCellValue('F3','DÒNG LƯU CHUYỂN')->getStyle('F3')->applyFromArray($BStyle);

        foreach($warehouses as $rom => $value_warehouse)
        {
            $objPHPExcel->getActiveSheet()->setCellValue('A'.($rom+4),($rom+1));
            $objPHPExcel->getActiveSheet()->setCellValue('B'.($rom+4),$value_warehouse['code']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.($rom+4),$value_warehouse['name']);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.($rom+4),$value_warehouse['address']);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.($rom+4),'');
            $objPHPExcel->getActiveSheet()->setCellValue('F'.($rom+4),'');

        }
        $objPHPExcel->getActiveSheet()->freezePane('A4');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Danh_sach_kho_hang.xls"');
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');
        exit();
    }
}