    <div id="order-tracking-book-report" class="hide">
      <div class="row">
         <div class="col-md-4">
            <div class="form-group">
               <!-- multiple -->
               <label for="SO_status"><?php echo _l('report_invoice_status'); ?></label>
               <select name="SO_status"  class="selectpicker"  data-width="100%">
                  <option value="" selected><?php echo _l('invoice_status_report_all'); ?></option>
                  <?php foreach($sale_SO_statuses as $status){ ?>
                  <option value="<?=$status['id']?>"><?=$status['text']?></option>
                  <?php } ?>
               </select>
            </div>
         </div>

         <div class="clearfix"></div>
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
               <td class="DTB"></td>
            </tr>
         </tfoot>
      </table>
   </div>
