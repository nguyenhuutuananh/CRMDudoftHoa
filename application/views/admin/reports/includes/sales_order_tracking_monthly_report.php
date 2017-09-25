    <div id="order-tracking-monthly-report" class="hide">
      <div class="row">
         
         <div class="clearfix"></div>
      </div>
      <table class="table table table-striped table-order-tracking-monthly-report">
         <thead>
            <tr>
               <th width="100px"></th>
               <?php foreach ($MONTHS as $key => $month) { ?>
                  <th><?php echo _l($month); ?></th>
               <?php } ?>
            </tr>
         </thead>
         <tbody>
            <tr>
               <td class="title" width="100px"><?=_l('quantity')?></td>
               <td></td>
               <td></td>
               <td></td>
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
            <tr>
               <td class="title" width="100px"><?=_l('revenue')?></td>
               <td></td>
               <td></td>
               <td></td>
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
         </tbody>
         <tfoot>
            <tr  class="textR">
               <td width="100px"><!-- <?=mb_strtoupper(_l('invoice_total'),'UTF-8')?> --></td>
               <?php foreach ($MONTHS as $key => $month) { ?>
                  <td><?php echo _l($montd); ?></td>
               <?php } ?>
            </tr>
         </tfoot>
      </table>
   </div>
