    <div id="diaries-report" class="hide">
      <div class="row">
         <!-- <div class="col-md-4">
            <div class="form-group">
               <label for="diary_status"><?php echo _l('report_diary_status'); ?></label>
               <select name="invoice_status" class="selectpicker" multiple data-width="100%">
                  <option value="" selected><?php echo _l('invoice_status_report_all'); ?></option>
                  <?php  foreach($sale_statuses as $status){ if($status ==5){continue;}  ?>
                  <option value="<?php echo $status; ?>"><?php echo format_diary_status($status,'',false) ?></option>
                  <?php } ?>
               </select>
            </div>
         </div> -->
         <!-- <?php if(count($invoices_sale_agents) > 0 ) { ?>
         <div class="col-md-4">
            <div class="form-group">
               <label for="sale_agent_invoices"><?php echo _l('sale_agent_string'); ?></label>
               <select name="sale_agent_invoices" class="selectpicker" multiple data-width="100%">
                  <option value="" selected><?php echo _l('invoice_status_report_all'); ?></option>
                  <?php foreach($invoices_sale_agents as $agent){ ?>
                  <option value="<?php echo $agent['sale_agent']; ?>"><?php echo get_staff_full_name($agent['sale_agent']); ?></option>
                  <?php } ?>
               </select>
            </div>
         </div>
         <?php } ?> -->
         <div class="clearfix"></div>
         <style>
            .dt-buttons .buttons-collection{
               display: none!important;
            }
         </style>
         <a href="<?=admin_url('reports/diaries_report_pdf')?>" class="btn mright5 btn-info pull-left display-block">Xuất Excel</a>
      </div>
      <table class="table table table-striped table-diaries-report">
         <thead>
            <tr>
               <th><?php echo _l('view_date'); ?></th>
               <th><?php echo _l('account_date'); ?></th>               
               <th><?php echo _l('code_noo'); ?></th>
               <th><?php echo _l('invoice_date'); ?></th>
               <th><?php echo _l('invoice_no'); ?></th>
               <th><?php echo _l('orders_explan'); ?></th>
               <th><?php echo _l('total_revenue'); ?></th>
               <th><?php echo _l('goods_revenue'); ?></th>
               <th><?php echo _l('others_revenue'); ?></th>
               <th><?php echo _l('discount'); ?></th>
               <th><?php echo _l('returns_value'); ?></th>
               <th><?php echo _l('net_revenue'); ?></th>
               <th><?php echo _l('customer_name'); ?></th>
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
               <td class="TDT"></td>
               <td class="DTHH"></td>
               <td class="DTK"></td>
               <td class="CK"></td>
               <td class="GTTV"></td>
               <td class="DTT"></td>
               <td></td>
            </tr>
         </tfoot>
      </table>
   </div>
