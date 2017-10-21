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
               <th style="text-align: center;" rowspan="2"><?php echo _l('STT'); ?></th>
               <th style="text-align: center;" rowspan="2"><?php echo _l('report_date'); ?></th>
               <th style="text-align: center;" colspan="2"><?php echo _l('certificate_code'); ?></th>
               <th style="text-align: center;" rowspan="2"><?php echo _l('orders_explan'); ?></th>
               <th style="text-align: center;" rowspan="2"><?php echo _l('report_dateIE'); ?></th>
               <th style="text-align: center;" colspan="3"><?php echo _l('report_quantity'); ?></th>
            </tr>
            <tr>
               <th style="text-align: center;"><?php echo _l('import'); ?></th>
               <th style="text-align: center;"><?php echo _l('export'); ?></th>
               <th style="text-align: center;"><?php echo _l('import'); ?></th>
               <th style="text-align: center;"><?php echo _l('export'); ?></th>
               <th style="text-align: center;"><?php echo _l('report_revenue'); ?></th>
            </tr>
            <tr>
               <th style="text-align: center;">A</th>
               <th style="text-align: center;">B</th>
               <th style="text-align: center;">C</th>
               <th style="text-align: center;">D</th>
               <th style="text-align: center;">E</th>
               <th style="text-align: center;">F</th>
               <th style="text-align: center;">1</th>
               <th style="text-align: center;">2</th>
               <th style="text-align: center;">3</th>
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
               <td></td>
            </tr>
         </tfoot>
      </table>
   </div>
