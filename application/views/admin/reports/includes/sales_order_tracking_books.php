    <div id="order-tracking-book-report" class="hide">
      <div class="row">
         
         <div class="col-md-4">
            <div class="form-group">
               <label for="SO_status"><?php echo _l('report_invoice_status'); ?></label>
               <select name="SO_status" class="selectpicker"  data-width="100%">
                  <option value="" selected><?php echo _l('invoice_status_report_all'); ?></option>
                  <option value="1"><?= _l('Chưa duyệt')?></option>
                  <option value="2"><?= _l('Đã duyệt')?></option>
                  <option value="3"><?= _l('Chưa tạo phiếu xuất')?></option>
                  <option value="4"><?= _l('Đang tạo phiếu xuất')?></option>
                  <option value="5"><?= _l('Đã tạo phiếu xuất')?></option>
                  <option value="6"><?= _l('Giao hàng')?></option>
                  <!-- <option value="7"><?= _l('Thanh toán')?></option> -->
               </select>
            </div>
         </div>

         <div class="clearfix"></div>
         <style>
            .dt-buttons .buttons-collection{
               display: none!important;
            }
         </style>
         <a href="<?=admin_url('reports/order_tracking_book_report_pdf')?>" class="btn mright5 btn-info pull-left display-block">Xuất Excel</a>
      </div>
      <table class="table table table-striped table-order-tracking-book-report">
         <thead>
            <tr>
               <th><?php echo _l('view_date'); ?></th>
               <th><?php echo _l('account_date'); ?></th>               
               <th><?php echo _l('code_noo'); ?></th>
               <th><?php echo _l('orders_explan'); ?></th>
               <th><?php echo _l('product_code'); ?></th>
               <th><?php echo _l('product_name'); ?></th>
               <th><?php echo _l('unit_name'); ?></th>
               <th><?php echo _l('quantity'); ?></th>
               <th><?php echo _l('unit_cost'); ?></th>
               <th><?php echo _l('sale_revenue'); ?></th>
               <!-- Giao hàng -->
               <th><?php echo _l('delivered_quantity'); ?></th>
               <th><?php echo _l('rest_quantity'); ?></th>
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
               <td></td>
               <td class="SL"></td>
               <td></td>
               <td class="DSB"></td>
               <td class="DG"></td>
               <td class="CG"></td>
            </tr>
         </tfoot>
      </table>
   </div>
