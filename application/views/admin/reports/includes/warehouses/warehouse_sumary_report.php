    <div id="warehouse-sumary-report" class="hide">
      <div class="row">
         <div class="clearfix"></div>
         <style>
            .dt-buttons .buttons-collection{
               display: none!important;
            }
         </style>
         <a href="<?=admin_url('reports/genernal_receivable_debts_report_pdf')?>" class="btn mright5 btn-info pull-left display-block">Xuất Excel</a>
      </div>
      <table class="table table table-striped table-warehouse-sumary-report">
         <thead>
            <tr>
               <th rowspan="2"><?php echo _l('customer_code'); ?></th>
               <th rowspan="2"><?php echo _l('customer_name'); ?></th>
               <th rowspan="2"><?php echo _l('tk_debt'); ?></th>
               <th colspan="2">SỐ DƯ ĐẦU KỲ</th>
               <th colspan="2">SỐ PHÁT SINH</th>
               <th colspan="2">SỐ PHÁT SINH</th>
            </tr>
            <tr>
               <th><?php echo _l('debt_no'); ?></th>
               <th><?php echo _l('debt_co'); ?></th>
               <th><?php echo _l('incurred_debt_no'); ?></th>
               <th><?php echo _l('incurred_debt_co'); ?></th>
               <th><?php echo _l('surplus_debt_no'); ?></th>
               <th><?php echo _l('surplus_debt_co'); ?></th>
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
