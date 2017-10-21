    <div id="stock-card-report" class="hide">
      <div class="row">
         <div class="clearfix"></div>
         <style>
            .dt-buttons .buttons-collection{
               display: none!important;
            }
         </style>
         <a href="<?=admin_url('reports/genernal_receivable_debts_report_pdf')?>" class="btn mright5 btn-info pull-left display-block">Xuất Excel</a>
      </div>
      <table class="table table table-striped table-stock-card-report">
         <thead>
            <tr class="bold" style="text-align: center;font-weight: bold;">
               <th style="text-align: center;"><?php echo _l('stock_card_date'); ?></th>
               <th style="text-align: center;"><?php echo _l('view_date'); ?></th>
               <th style="text-align: center;"><?php echo _l('code_noo'); ?></th>
               <th style="text-align: center;"><?php echo _l('orders_explan'); ?></th>
               <th style="text-align: center;"><?php echo _l('short_unit_name'); ?></th>
               <th style="text-align: center;"><?php echo _l('import_quantity'); ?></th>
               <th style="text-align: center;"><?php echo _l('export_quantity'); ?></th>
               <th style="text-align: center;"><?php echo _l('revenue_quantity'); ?></th>
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
               <td></td>
            </tr>
         </tfoot>
      </table>
   </div>
