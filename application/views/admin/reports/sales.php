<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <div class="row">
                     <div class="col-md-4 border-right">
                      <h4 class="bold no-margin font-medium"><?php echo _l('sales_report_heading'); ?></h4>
                      <!-- Sale Diary -->
                      <hr class="hr-10" />
                      <p><a href="#" class="font-medium" onclick="init_report(this,'diaries-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('sale_diary'); ?></a></p>
                      <!-- detailed_sales_contract_report -->
                      <hr class="hr-10" />
                      <p><a href="#" class="font-medium" onclick="init_report(this,'detailed-sales-contract-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('detailed_sales_contract_report'); ?></a></p>
                      <!-- So theo doi tong hop don dat hang PO -->
                      <hr class="hr-10" />
                      <p><a href="#" class="font-medium" onclick="init_report(this,'general-order-tracking-book-report-PO'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('general_order_tracking_book_PO'); ?></a></p>
                      <!-- So theo doi chi tiet don dat hang PO -->
                      <hr class="hr-10" />
                      <p><a href="#" class="font-medium" onclick="init_report(this,'order-tracking-book-report-PO'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('order_tracking_book_PO'); ?></a></p>
                      <!-- So theo doi tong hop don hang ban SO -->
                      <hr class="hr-10" />
                      <p><a href="#" class="font-medium" onclick="init_report(this,'general-order-tracking-book-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('general_order_tracking_book_SO'); ?></a></p>
                      

                      

                      <hr class="hr-10" />
                      <?php if(total_rows('tblinvoices',array('status'=>5)) > 0){ ?>
                      <p class="text-danger">
                        <i class="fa fa-exclamation-circle" aria-hidden="true"></i> <?php echo _l('sales_report_cancelled_invoices_not_included'); ?>
                     </p>
                     <?php } ?>
                  </div>
                  <div class="col-md-4 border-right">
                    <!--  -->
                    <h4 class="bold no-margin font-medium"><?php echo _l('sales_report_heading'); ?></h4>
                      <!-- So theo doi chi tiet don hang ban SO -->
                      <hr class="hr-10" />
                      <p><a href="#" class="font-medium" onclick="init_report(this,'order-tracking-book-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('order_tracking_book_SO'); ?></a></p>                      
                      <!-- Tổng cộng đơn hàng bán trong tháng -->
                      <hr class="hr-10" />
                      <p><a href="#" class="font-medium" onclick="init_report(this,'order-tracking-monthly-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('sales_order_tracking_monthly_report'); ?></a></p>
                      <!-- Sổ quỹ -->
                      <hr class="hr-10" />
                      <p><a href="#" class="font-medium" onclick="init_report(this,'cash-funds-detailing-accounting-books'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('cash_funds_detailing_accounting_books'); ?></a></p>
                      <!-- Tổng cộng đơn hàng bán trong tháng -->
                      <hr class="hr-10" />
                      <p><a href="#" class="font-medium" onclick="init_report(this,'bank-deposit-books'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('bank_deposit_books'); ?></a></p>
                      <!-- sales_analysis_report -->
                      <hr class="hr-10" />
                      <p><a href="#" class="font-medium" onclick="init_report(this,'sales-analysis-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('sales_analysis_report'); ?></a></p>
                      <div  style="display: none;">
                      <hr class="hr-10" />
                      <p><a href="#" class="font-medium" onclick="init_report(this,'invoices-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('invoice_report'); ?></a></p>
                      <hr class="hr-10" />
                      <p><a href="#" class="font-medium" onclick="init_report(this,'payments-received'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('payments_received'); ?></a></p>
                      <hr class="hr-10" />
                      <p><a href="#" class="font-medium" onclick="init_report(this,'proposals-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('proposals_report'); ?></a></p>
                      <hr class="hr-10" />
                      <p><a href="#" class="font-medium" onclick="init_report(this,'estimates-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('estimates_report'); ?></a></p>
                      <hr class="hr-10" />
                      <p><a href="#" class="font-medium" onclick="init_report(this,'customers-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('report_sales_type_customer'); ?></a></p>
                    <!--  -->
                    <h4 class="bold no-margin font-medium"><?php echo _l('charts_based_report'); ?></h4>
                    <hr class="hr-10" />
                    <p><a href="#" class="font-medium" onclick="init_report(this,'total-income'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('report_sales_type_income'); ?></a></p>
                    <hr class="hr-10" />
                    <p><a href="#" class="font-medium" onclick="init_report(this,'payment-modes'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('payment_modes_report'); ?></a></p>
                    <hr class="hr-10" />
                    <p><a href="#" class="font-medium" onclick="init_report(this,'customers-group'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('report_by_customer_groups'); ?></a></p>
                    </div>
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
        <!-- CHART -->
        <div class="row" id="PO_SO">
          <div class="col-md-6 col-sm-12">
            <!-- sales_chart_PO_SO -->
            <?php $this->load->view('admin/reports/includes/sales_chart_PO'); ?>
          </div>
          <div class="col-md-6 col-sm-12">
            <!-- sales_chart_PO_SO -->
            <?php $this->load->view('admin/reports/includes/sales_chart_SO'); ?>
          </div>
        </div>
         
         <div class="panel_s hide" id="report">
            <div class="panel-body">
               <h4 class="no-mtop" id="report_tiltle" ><?php echo _l('reports_sales_generated_report'); ?></h4>
               <hr />
               <?php $this->load->view('admin/reports/includes/sales_income'); ?>
               <?php $this->load->view('admin/reports/includes/sales_payment_modes'); ?>
               <?php $this->load->view('admin/reports/includes/sales_customers_groups'); ?>
               <?php $this->load->view('admin/reports/includes/sales_customers'); ?>
               <?php $this->load->view('admin/reports/includes/sales_invoices'); ?>
               <?php $this->load->view('admin/reports/includes/sales_estimates'); ?>
               <?php $this->load->view('admin/reports/includes/sales_payments'); ?>
               <?php $this->load->view('admin/reports/includes/sales_proposals'); ?>
               <!-- Sale Diary -->
               <?php $this->load->view('admin/reports/includes/sales_diaries'); ?>
               <!-- Sale sales_order_tracking_books SO -->
               <?php $this->load->view('admin/reports/includes/sales_order_tracking_books'); ?>
               <!-- Sale sales_order_tracking_books PO -->
               <?php $this->load->view('admin/reports/includes/sales_order_tracking_books_PO'); ?>
               <!-- Sale sales_order_tracking_monthly -->
               <?php $this->load->view('admin/reports/includes/sales_order_tracking_monthly_report'); ?>
               <!-- Sale sales_general_order_tracking_books_PO -->
               <?php $this->load->view('admin/reports/includes/sales_general_order_tracking_books_PO'); ?>
               <!-- Sale sales_general_order_tracking_books -->
               <?php $this->load->view('admin/reports/includes/sales_general_order_tracking_books'); ?>
               <!-- sale_cash_funds_detailing_accounting_books -->
               <?php $this->load->view('admin/reports/includes/sale_cash_funds_detailing_accounting_books'); ?>
              <!-- sale_bank_deposit_books -->
               <?php $this->load->view('admin/reports/includes/sale_bank_deposit_books'); ?>
              <!-- sales_detailed_sales_contract_report -->
               <?php $this->load->view('admin/reports/includes/sales_detailed_sales_contract_report'); ?>
              <!-- sales_analysis_report -->
              <?php $this->load->view('admin/reports/includes/sales_analysis_report'); ?>
            </div>
         </div>
      </div>
   </div>
</div>
</div>
<?php init_tail(); ?>
<?php $this->load->view('admin/reports/includes/sales_js'); ?>
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
</style>