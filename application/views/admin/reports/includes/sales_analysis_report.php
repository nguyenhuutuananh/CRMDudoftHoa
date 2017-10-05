    <div id="sales-analysis-report" class="hide">
      <div class="row">
         <ul class="nav nav-tabs" role="tablist">

            <li role="presentation" class="active">
               <a href="#report-products" aria-controls="products_report" role="tab" data-toggle="tab">
                  <i class="fa fa-product-hunt menu-icon text-info"></i><?php echo _l('products_report'); ?>
               </a>
            </li>

            <li role="presentation">
               <a href="#report-staffs" aria-controls="staffs_report" role="tab" data-toggle="tab">
                  <i class="fa fa-user-circle-o menu-icon text-info"></i><?php echo _l('staffs_report'); ?>
               </a>
            </li>

            <li role="presentation">
               <a href="#report-customers" aria-controls="customers_report" role="tab" data-toggle="tab">
                  <i class="fa fa-users menu-icon text-info"></i><?php echo _l('customers_report'); ?>
               </a>
            </li>

            <li role="presentation">
               <a href="#report-consume-areas" aria-controls="consume_area_report" role="tab" data-toggle="tab">
                  <i class="fa fa-map menu-icon text-info"></i><?php echo _l('consume_area_report'); ?>
               </a>
            </li>

         </ul>
         <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="report-products">
               <!-- <div class="row"> -->
                  <!-- Chart -->
                  <p class="padding-5 mtop5"><?php echo _l('Biểu đồ'); ?></p>
                  <hr class="no-mtop" />
                  <div class="relative" style="height:250px">
                     <canvas class="chart" height="250" id="chart-PRODUCTS"></canvas>
                  </div>
                  <!-- List -->
                  <div class="clearfix"></div>
                  <p class="padding-5 mtop5"><?php echo _l('Danh sách sản phẩm bán'); ?></p>
                  <hr class="no-mtop" />
                  <div class="relative">
                     <table class="table table table-striped table-products-report">
                        <thead>
                           <tr>
                              <th><?php echo _l('category_name'); ?></th>
                              <th><?php echo _l('product_name'); ?></th>               
                              <th><?php echo _l('unit'); ?></th>
                              <th><?php echo _l('quantity'); ?></th>
                              <th><?php echo _l('discount'); ?></th>
                              <th><?php echo _l('revenue'); ?></th>
                              <th><?php echo _l('income'); ?></th>
                              <th><?php echo _l('proportion'); ?></th>
                              <th><?php echo _l('profit'); ?></th>
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
               <!-- </div>                -->
            </div>

            <div role="tabpanel" class="tab-pane" id="report-staffs">
               b
            </div>

            <div role="tabpanel" class="tab-pane" id="report-customers">
               c
            </div>

            <div role="tabpanel" class="tab-pane" id="report-consume-areas">
               d
            </div>
         </div>
      </div>      
   </div>
