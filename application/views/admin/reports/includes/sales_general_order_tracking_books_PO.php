    <div id="general-order-tracking-book-report-PO" class="hide">
      <div class="row">

         <div class="col-md-4">
            <div class="form-group">               
               <label for="PO_status_gen"><?php echo _l('report_invoice_status'); ?></label>
               <select name="PO_status_gen" class="selectpicker" data-width="100%">
                  <option value="" selected><?php echo _l('invoice_status_report_all'); ?></option>
                  <option value="1"><?=_l('Chưa duyệt')?></option>
                  <option value="2"><?=_l('Đã duyệt')?></option>
                  <option value="3"><?=_l('Chưa tạo đơn hàng')?></option>
                  <option value="4"><?=_l('Đang tạo đơn hàng')?></option>
                  <option value="5"><?=_l('Đã tạo đơn hàng')?></option>
               </select>
            </div>
         </div>

         <div class="clearfix"></div>
         <style>
            .dt-buttons .buttons-collection{
               display: none!important;
            }
         </style>
         <a href="<?=admin_url('reports/general_order_tracking_book_report_PO_pdf')?>" class="btn mright5 btn-info pull-left display-block">Xuất Excel</a>
      </div>
      <table class="table table table-striped table-general-order-tracking-book-PO-report">
         <thead>
            <tr>
               <th><?php echo _l('view_date'); ?></th>
               <th><?php echo _l('code_noo'); ?></th>               
               <th><?php echo _l('customer_name'); ?></th>
               <th><?php echo _l('sale_quantity'); ?></th>
               <th><?php echo _l('sale_revenue'); ?></th>
               <th><?php echo _l('net_revenue'); ?></th>
            </tr>
         </thead>
         <tbody></tbody>
         <tfoot>
            <tr>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>               
            </tr>
         </tfoot>
      </table>
   </div>
