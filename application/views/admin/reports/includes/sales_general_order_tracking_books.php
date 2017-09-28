    <div id="general-order-tracking-book-report" class="hide">
      <div class="row">

         <div class="col-md-4">
            <div class="form-group">
               <label for="SO_status_gen"><?php echo _l('report_invoice_status'); ?></label>
               <select name="SO_status_gen" class="selectpicker"  data-width="100%">
                  <option value="" selected><?php echo _l('invoice_status_report_all'); ?></option>
                  <option value="1"><?= _l('Chưa duyệt')?></option>
                  <option value="2"><?= _l('Đã duyệt')?></option>
                  <option value="3"><?= _l('Chưa tạo phiếu xuất')?></option>
                  <option value="4"><?= _l('Đang tạo phiếu xuất')?></option>
                  <option value="5"><?= _l('Đã tạo phiếu xuất')?></option>
                  <option value="6"><?= _l('Giao hàng')?></option>
                  <option value="7"><?= _l('Thanh toán')?></option>
               </select>
            </div>
         </div>

         <div class="clearfix"></div>
      </div>
      <table class="table table table-striped table-general-order-tracking-book-report">
         <thead>
            <tr>
               <th><?php echo _l('view_date'); ?></th>
               <th><?php echo _l('code_noo'); ?></th>               
               <th><?php echo _l('customer_name'); ?></th>
               <th><?php echo _l('sale_quantity'); ?></th>
               <th><?php echo _l('sale_revenue'); ?></th>
               <th><?php echo _l('net_revenue'); ?></th>
               <!-- Giao hàng -->
               <th><?php echo _l('delivered_quantity'); ?></th>
               <th><?php echo _l('rest_quantity'); ?></th>
               <!-- Thanh toán -->
               <th><?php echo _l('paid_payment'); ?></th>
               <th><?php echo _l('rest_payment'); ?></th>
            </tr>
         </thead>
         <tbody></tbody>
         <tfoot>
            <tr>
               <td></td>
               <td></td>
               <td></td>
               <td class="SL"></td>
               <td class="DSB"></td>
               <td class="DTT"></td>               
               <td class="DG"></td>
               <td class="CG"></td>
               <td class="DT"></td>
               <td class="CT"></td> 
            </tr>
         </tfoot>
      </table>
   </div>
