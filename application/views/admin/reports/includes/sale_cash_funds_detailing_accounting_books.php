    <div id="cash-funds-detailing-accounting-books" class="hide">
      <div class="row">
         
         <!-- <div class="col-md-4">
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
               </select>
            </div>
         </div> -->

         <div class="clearfix"></div>
      </div>
      <table class="table table table-striped table-cash-funds-detailing-accounting-books-report">
         <thead>
            <tr>
               <th><?php echo _l('account_date'); ?></th>
               <th><?php echo _l('view_date'); ?></th>               
               <th><?php echo _l('code_vouchers_receipts'); ?></th>
               <th><?php echo _l('code_vouchers_votes'); ?></th>
               <th><?php echo _l('orders_explan'); ?></th>
               <th><?php echo _l('reciprocal_tk'); ?></th>
               <th><?php echo _l('incurred_tk_no'); ?></th>
               <th><?php echo _l('incurred_tk_co'); ?></th>
               <th><?php echo _l('rest_tk'); ?></th>
               <th><?php echo _l('receiver_submitter'); ?></th>
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
               <td class="SPSN"></td>
               <td class="SPSC"></td>
               <td class="ST"></td>
               <td></td>
            </tr>
         </tfoot>
      </table>
   </div>
