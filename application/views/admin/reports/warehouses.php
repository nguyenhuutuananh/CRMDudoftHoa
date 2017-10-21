<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
              <div class="panel-body">
                <h4 class="bold no-margin"><?php echo _l('warehouses_reports'); ?></h4>
              </div>
            </div>
            <div class="panel_s">
               <div class="panel-body">
                  <div class="row">
                    <div class="col-md-4 border-right">
                      
                      <!-- stock_card -->
                      <p><a href="#" class="font-medium" onclick="init_report(this,'stock-card-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('stock_card'); ?></a></p>
                      <hr class="hr-10" />
                      <!-- detail_goods_book -->
                      <p><a href="#" class="font-medium" onclick="init_report(this,'detail-goods-book-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('detail_goods_book'); ?></a></p>
                      <hr class="hr-10" />
                      <!-- warehouse_sumary_report -->
                      <p><a href="#" class="font-medium" onclick="init_report(this,'warehouse-sumary-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('warehouse_sumary_report'); ?></a></p>
                        <hr class="hr-10" />


                    </div>
                  <div class="col-md-4 border-right" >
                      <!-- warehouse_detail_report -->
                      <p><a href="#" class="font-medium" onclick="init_report(this,'warehouse-detail-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('warehouse_detail_report'); ?></a></p>
                      <hr class="hr-10" />
                      <!-- inventory_report -->
                      <p><a href="#" class="font-medium" onclick="init_report(this,'inventory-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('inventory_report'); ?></a></p>
                      <hr class="hr-10" />
                      <!-- max_min_inventory_report -->
                      <p><a href="#" class="font-medium" onclick="init_report(this,'max-min-inventory-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('max_min_inventory_report'); ?></a></p>    
                      <hr class="hr-10" />              
                  </div>
                 <div class="col-md-4">
                  <?php if(isset($currencies)){ ?>
                  <div id="currency" class="form-group hide">
                     <label for="currency"><i class="fa fa-question-circle" data-toggle="tooltip" title="<?php echo _l('report_sales_base_currency_select_explanation'); ?>"></i> <?php echo _l('currency'); ?></label><br />
                     <select class="selectpicker" name="currency" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <?php foreach($currencies as $currency){
                           $selected = '';
                           if($currency['isdefault'] == 1){
                              $selected = 'selected';
                           }
                           ?>
                           <option value="<?php echo $currency['id']; ?>" <?php echo $selected; ?>><?php echo $currency['name']; ?></option>
                           <?php } ?>
                        </select>
                     </div>
                     <?php } ?>
                     <div id="income-years" class="hide mbot15">
                        <label for="payments_years"><?php echo _l('year'); ?></label><br />
                        <select class="selectpicker" name="payments_years" data-width="100%" onchange="total_income_bar_report();" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                           <?php foreach($payments_years as $year) { ?>
                           <option value="<?php echo $year['year']; ?>"<?php if($year['year'] == date('Y')){echo 'selected';} ?>>
                              <?php echo $year['year']; ?>
                           </option>
                           <?php } ?>
                        </select>
                     </div>

                     <div class="form-group hide" id="report-time">
                        <label for="months-report"><?php echo _l('period_datepicker'); ?></label><br />
                        <select class="selectpicker" name="months-report" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                           <option value=""><?php echo _l('report_sales_months_all_time'); ?></option>
                           <option value="3"><?php echo _l('report_sales_months_three_months'); ?></option>
                           <option value="6"><?php echo _l('report_sales_months_six_months'); ?></option>
                           <option value="12"><?php echo _l('report_sales_months_twelve_months'); ?></option>
                           <option value="custom"><?php echo _l('period_datepicker'); ?></option>
                        </select>
                     </div>

                     <div class="form-group hide" id="report-year">
                        <label for="years_report"><?php echo _l('year_report'); ?></label><br />
                        <select class="selectpicker" name="years_report" id="years_report" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                           <option value=""><?php echo _l(''); ?></option>
                           <?php foreach ($order_years as $key => $year) { 
                              $selected='';
                              if($year->year==date('Y'))
                                  $selected='selected';
                            ?>
                                  <option value="<?=$year->year?>" <?=$selected?> ><?=$year->year?></option>
                           <?php } ?>
                        </select>
                     </div>

                     <div id="date-range" class="hide animated mbot15">
                        <div class="row">
                           <div class="col-md-6">
                              <label for="report-from" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
                              <div class="input-group date">
                                 <input type="text" class="form-control datepicker" id="report-from" name="report-from">
                                 <div class="input-group-addon">
                                    <i class="fa fa-calendar calendar-icon"></i>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <label for="report-to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
                              <div class="input-group date">
                                 <input type="text" class="form-control datepicker" disabled="disabled" id="report-to" name="report-to">
                                 <div class="input-group-addon">
                                    <i class="fa fa-calendar calendar-icon"></i>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-12">
         <div class="panel_s hide" id="report">
            <div class="panel-body">
               <h4 class="no-mtop" id="report_tiltle" ><?php echo _l('reports_sales_generated_report'); ?></h4>
               <hr />
               <!-- stock_card -->
               <?php $this->load->view('admin/reports/includes/warehouses/warehouse_stock_card'); ?>
                <!-- stock_card -->
               <?php $this->load->view('admin/reports/includes/warehouses/warehouse_detail_goods_book'); ?>
               <!-- warehouse_sumary_report -->
               <?php $this->load->view('admin/reports/includes/warehouses/warehouse_sumary_report'); ?>
               <!-- warehouse_detail_report -->
               <?php $this->load->view('admin/reports/includes/warehouses/warehouse_detail_report'); ?>



            </div>
         </div>
      </div>
   </div>
</div>
</div>
<?php init_tail(); ?>
<?php $this->load->view('admin/reports/includes/warehouses/warehouses_js'); ?>
</body>
</html>

<style type="text/css">
  .textR{
    color: red;
    font-weight: bold;  
  }
  .textB{
    color: blue;
    font-weight: bold;
  }
  .textG{
    color: green;
    font-weight: bold;
  }
  .title{
    font-weight: bold;
    font-style: italic;
  }
  /*tr.alert-header
  {
    background-color: #c986ae !important;
  }*/
  tr.alert-header.odd.parent+tr.child {
        display: none;
    }
    tr.alert-header.odd>td:first-child:before
    {
        content:''!important;
        background-color: unset!important;
        border:0px!important;
    }
    tr.alert-header.odd.parent>td:first-child:before
    {
        background-color: unset!important;
        content:''!important;
        border:0px!important;
    }
    tr.alert-header.even.parent+tr.child {
        display: none;
    }
    tr.alert-header.even>td:first-child:before
    {
        content:''!important;
        background-color: unset!important;
        border:0px!important;
    }
    tr.alert-header.even.parent>td:first-child:before
    {
        background-color: unset!important;
        content:''!important;
        border:0px!important;
    }
    fieldset
    {
        border: 1px solid #ddd !important;
        margin: 0;
        xmin-width: 0;
        padding: 10px;
        position: relative;
        border-radius:4px;
        background-color:#f5f5f5;
        padding-left:10px!important;
    }

    legend
    {
        font-size:14px;
        font-weight:bold;
        margin-bottom: 0px;
        width: 35%;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px 5px 5px 10px;
        background-color: #ffffff;
    }
</style>