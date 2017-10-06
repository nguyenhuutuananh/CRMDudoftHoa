    <div id="detailed-sales-contract-report" class="hide">
      <div class="row">

         <div class="clearfix"></div>
         <style>
            .dt-buttons .buttons-collection{
               display: none!important;
            }
         </style>
         <a href="<?=admin_url('reports/detailed_sales_contract_report_pdf')?>" class="btn mright5 btn-info pull-left display-block">Xuất Excel</a>
      </div>
      <table class="table table table-striped table-detailed-sales-contract-report">
         <thead>
            <tr>
               <th><?php echo _l('contract_date'); ?></th>
               <th><?php echo _l('contract_code'); ?></th>               
               <th><?php echo _l('customer_name'); ?></th>
               <th><?php echo _l('product_code'); ?></th>
               <th><?php echo _l('product_name'); ?></th>
               <th><?php echo _l('contract_unit'); ?></th>
               <th><?php echo _l('contract_quantity'); ?></th>
               <th><?php echo _l('contract_quantity_delivered'); ?></th>
               <th><?php echo _l('contract_quantity_rest'); ?></th>
               <th><?php echo _l('contract_sales'); ?></th>
               <th><?php echo _l('contract_sales_effectuated'); ?></th>
               <th><?php echo _l('contract_sales_rest'); ?></th>
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
               <td class="SL"></td>               
               <td class="SLDG"></td>
               <td class="SLCL"></td>
               <td class="DSHD"></td>
               <td class="DSTH"></td> 
               <td class="DSCL"></td>
            </tr>
         </tfoot>
      </table>
   </div>
