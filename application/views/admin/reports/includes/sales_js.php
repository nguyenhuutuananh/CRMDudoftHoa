<script>
 var salesChart;
 var groupsChart;
 var paymentMethodsChart;
 var customersTable;
 var report_from = $('input[name="report-from"]');
 var report_to = $('input[name="report-to"]');
 var report_customers = $('#customers-report');
 var report_customers_groups = $('#customers-group');
 var report_invoices = $('#invoices-report');
 var report_estimates = $('#estimates-report');
 var report_payments_received = $('#payments-received-report');
 var date_range = $('#date-range');
 var report_from_choose = $('#report-time');
 var report_year_choose = $('#report-year');
 var fnServerParams = {
   "report_months": '[name="months-report"]',
   "report_from": '[name="report-from"]',
   "report_to": '[name="report-to"]',
   "report_currency": '[name="currency"]',
   "invoice_status": '[name="invoice_status"]',
   "PO_status_gen": '[name="PO_status_gen"]',
   "PO_status": '[name="PO_status"]',
   "SO_status_gen": '[name="SO_status_gen"]',
   "SO_status": '[name="SO_status"]',
   "estimate_status": '[name="estimate_status"]',
   "sale_agent_invoices": '[name="sale_agent_invoices"]',
   "sale_agent_estimates": '[name="sale_agent_estimates"]',
   "proposals_sale_agents": '[name="proposals_sale_agents"]',
   "proposal_status": '[name="proposal_status"]',
   "years_report": '[name="years_report"]',
   "SO_status_delivery": '[name="SO_status_delivery"]',
   "SO_status_payment": '[name="SO_status_payment"]',


 }
 $(function() {
   $('select[name="currency"],select[name="PO_status_gen"],select[name="PO_status"],select[name="SO_status_gen"],select[name="SO_status"],select[name="SO_status_payment"],select[name="SO_status_delivery"],select[name="years_report"],select[name="invoice_status"],select[name="estimate_status"],select[name="sale_agent_invoices"],select[name="sale_agent_estimates"],select[name="payments_years"],select[name="proposals_sale_agents"],select[name="proposal_status"]').on('change', function() {
     gen_reports();
   });

   $('select[name="invoice_status"],select[name="estimate_status"],select[name="sale_agent_invoices"],select[name="sale_agent_estimates"],select[name="proposals_sale_agents"],select[name="proposal_status"]').on('change', function() {
     var value = $(this).val();
     if (value != null) {
       if (value.indexOf('') > -1) {
         if (value.length > 1) {
           value.splice(0, 1);

           $(this).selectpicker('val', value);
         }
       }
     }
   });
   report_from.on('change', function() {
     var val = $(this).val();
     var report_to_val = report_to.val();
     if (val != '') {
       report_to.attr('disabled', false);
       if (report_to_val != '') {
         gen_reports();
       }
     } else {
       report_to.attr('disabled', true);
     }
   });

   report_to.on('change', function() {
     var val = $(this).val();
     if (val != '') {
       gen_reports();
     }
   });

   $('select[name="months-report"]').on('change', function() {
     var val = $(this).val();
     report_to.attr('disabled', true);
     report_to.val('');
     report_from.val('');
     if (val == 'custom') {
       date_range.addClass('fadeIn').removeClass('hide');
       return;
     } else {
       if (!date_range.hasClass('hide')) {
         date_range.removeClass('fadeIn').addClass('hide');
       }
     }
     gen_reports();
   });


   $('.table-payments-received-report').on('draw.dt', function() {
     var paymentReceivedReportsTable = $(this).DataTable();
     var sums = paymentReceivedReportsTable.ajax.json().sums;
     $(this).find('tfoot').addClass('bold');
     $(this).find('tfoot td').eq(0).html("<?php echo _l('invoice_total'); ?>");
     $(this).find('tfoot td.total').html(sums.total_amount);
   });

   $('.table-proposals-report').on('draw.dt', function() {
     var proposalsReportTable = $(this).DataTable();
     var sums = proposalsReportTable.ajax.json().sums;
     $(this).find('tfoot').addClass('bold');
     $(this).find('tfoot td').eq(0).html("<?php echo _l('invoice_total'); ?>");
     $(this).find('tfoot td.subtotal').html(sums.subtotal);
     $(this).find('tfoot td.total').html(sums.total);
     $(this).find('tfoot td.total_tax').html(sums.total_tax);
     $(this).find('tfoot td.discount').html(sums.discount);
     $(this).find('tfoot td.adjustment').html(sums.adjustment);
   });

   $('.table-invoices-report').on('draw.dt', function() {
     var invoiceReportsTable = $(this).DataTable();
     var sums = invoiceReportsTable.ajax.json().sums;
     $(this).find('tfoot').addClass('bold');
     $(this).find('tfoot td').eq(0).html("<?php echo _l('invoice_total'); ?>");
     $(this).find('tfoot td.subtotal').html(sums.subtotal);
     $(this).find('tfoot td.total').html(sums.total);
     $(this).find('tfoot td.total_tax').html(sums.total_tax);
     $(this).find('tfoot td.discount_total').html(sums.discount_total);
     $(this).find('tfoot td.adjustment').html(sums.adjustment);
     $(this).find('tfoot td.amount_open').html(sums.amount_open);
   });

   $('.table-diaries-report').on('draw.dt', function() {
     var invoiceReportsTable = $(this).DataTable();
     var sums = invoiceReportsTable.ajax.json().sums;
     $(this).find('tfoot').addClass('bold');
     // $(this).find('tfoot').addClass('textR');
     $(this).find('tfoot td').eq(0).html("<b><?php echo mb_strtoupper(_l('invoice_total'),'UTF-8'); ?></b>");
     $(this).find('tfoot td.TDT').html(sums.TDT);
     $(this).find('tfoot td.DTHH').html(sums.DTHH);
     $(this).find('tfoot td.DTK').html(sums.DTK);
     $(this).find('tfoot td.CK').html(sums.CK);
     $(this).find('tfoot td.GTTV').html(sums.GTTV);
     $(this).find('tfoot td.DTT').html(sums.DTT);
   });

   $('.table-order-tracking-book-report').on('draw.dt', function() {
     var invoiceReportsTable = $(this).DataTable();
     var sums = invoiceReportsTable.ajax.json().sums;
     $(this).find('tfoot').addClass('bold');
     $(this).find('tfoot td').eq(0).html("<b><?php echo mb_strtoupper(_l('invoice_total'),'UTF-8'); ?></b>");
     $(this).find('tfoot td.SL').html(sums.SL);
     $(this).find('tfoot td.DSB').html(sums.DSB);
     $(this).find('tfoot td.DG').html(sums.DG);
     $(this).find('tfoot td.CG').html(sums.CG);
   });
  $('.table-general-order-tracking-book-report').on('draw.dt', function() {
       var invoiceReportsTable = $(this).DataTable();
       var sums = invoiceReportsTable.ajax.json().sums;
       $(this).find('tfoot').addClass('bold');
       $(this).find('tfoot td').eq(0).html("<b><?php echo mb_strtoupper(_l('invoice_total'),'UTF-8'); ?></b>");
       $(this).find('tfoot td.SL').html(sums.SL);
       $(this).find('tfoot td.DSB').html(sums.DSB);
       $(this).find('tfoot td.DTT').html(sums.DTT);
       $(this).find('tfoot td.DG').html(sums.DG);
       $(this).find('tfoot td.CG').html(sums.CG);
       $(this).find('tfoot td.DT').html(sums.DT);
       $(this).find('tfoot td.CT').html(sums.CT);
     });
   $('.table-order-tracking-book-PO-report').on('draw.dt', function() {
     var invoiceReportsTable = $(this).DataTable();
     var sums = invoiceReportsTable.ajax.json().sums;
     $(this).find('tfoot').addClass('bold');
     $(this).find('tfoot td').eq(0).html("<b><?php echo mb_strtoupper(_l('invoice_total'),'UTF-8'); ?></b>");
     $(this).find('tfoot td.SL').html(sums.SL);
     $(this).find('tfoot td.DTB').html(sums.DTB);
   });

   $('.table-order-tracking-monthly-report').on('draw.dt', function() {
     var invoiceReportsTable = $(this).DataTable();
     var sums = invoiceReportsTable.ajax.json().sums;
     $(this).find('tbody tr').eq(0).find('td').eq(0).addClass('title');
     $(this).find('tbody tr').eq(1).find('td').eq(0).addClass('title');

     // $(this).find('tbody tr').eq(0).find('td').eq(1).addClass('textR');
     // $(this).find('tbody tr').eq(0).find('td').eq(2).addClass('textR');
     // $(this).find('tbody tr').eq(0).find('td').eq(3).addClass('textR');
     // $(this).find('tbody tr').eq(0).find('td').eq(4).addClass('textR');
     // $(this).find('tbody tr').eq(0).find('td').eq(5).addClass('textR');
     // $(this).find('tbody tr').eq(0).find('td').eq(6).addClass('textR');
     // $(this).find('tbody tr').eq(0).find('td').eq(7).addClass('textR');
     // $(this).find('tbody tr').eq(0).find('td').eq(8).addClass('textR');
     // $(this).find('tbody tr').eq(0).find('td').eq(9).addClass('textR');
     // $(this).find('tbody tr').eq(0).find('td').eq(10).addClass('textR');
     // $(this).find('tbody tr').eq(0).find('td').eq(11).addClass('textR');
     // $(this).find('tbody tr').eq(0).find('td').eq(12).addClass('textR');

     // $(this).find('tbody tr').eq(1).find('td').eq(1).addClass('textG');
     // $(this).find('tbody tr').eq(1).find('td').eq(2).addClass('textG');
     // $(this).find('tbody tr').eq(1).find('td').eq(3).addClass('textG');
     // $(this).find('tbody tr').eq(1).find('td').eq(4).addClass('textG');
     // $(this).find('tbody tr').eq(1).find('td').eq(5).addClass('textG');
     // $(this).find('tbody tr').eq(1).find('td').eq(6).addClass('textG');
     // $(this).find('tbody tr').eq(1).find('td').eq(7).addClass('textG');
     // $(this).find('tbody tr').eq(1).find('td').eq(8).addClass('textG');
     // $(this).find('tbody tr').eq(1).find('td').eq(9).addClass('textG');
     // $(this).find('tbody tr').eq(1).find('td').eq(10).addClass('textG');
     // $(this).find('tbody tr').eq(1).find('td').eq(11).addClass('textG');
     // $(this).find('tbody tr').eq(1).find('td').eq(12).addClass('textG');
   });

   $('.table-estimates-report').on('draw.dt', function() {
     var estimatesReportsTable = $(this).DataTable();
     var sums = estimatesReportsTable.ajax.json().sums;
     $(this).find('tfoot').addClass('bold');
     $(this).find('tfoot td').eq(0).html("<?php echo _l('invoice_total'); ?>");
     $(this).find('tfoot td.subtotal').html(sums.subtotal);
     $(this).find('tfoot td.total').html(sums.total);
     $(this).find('tfoot td.total_tax').html(sums.total_tax);
     $(this).find('tfoot td.discount_total').html(sums.discount_total);
     $(this).find('tfoot td.adjustment').html(sums.adjustment);
   });
 });

 function init_report(e, type) {
    $('#report_tiltle').text($(e).text());
   var report_wrapper = $('#report');
   if (report_wrapper.hasClass('hide')) {
     report_wrapper.removeClass('hide');
   }
   $('head title').html($(e).text());
   $('.customers-group-gen').addClass('hide');
   report_customers_groups.addClass('hide');
   report_customers.addClass('hide');
   report_invoices.addClass('hide');
   report_estimates.addClass('hide');
   report_payments_received.addClass('hide');
   $('#income-years').addClass('hide');
   $('.chart-income').addClass('hide');
   $('.chart-payment-modes').addClass('hide');
   $('#proposals-reports').addClass('hide');
   $('#diaries-report').addClass('hide');
   $('#order-tracking-book-report').addClass('hide');
   $('#order-tracking-book-report-PO').addClass('hide');
   $('#order-tracking-monthly-report').addClass('hide');
   report_from_choose.addClass('hide');
   report_year_choose.addClass('hide');
   $('#general-order-tracking-book-report-PO').addClass('hide');
   $('#general-order-tracking-book-report').addClass('hide');
    $('#cash-funds-detailing-accounting-books').addClass('hide');


   $('select[name="months-report"]').selectpicker('val', '');
       // Clear custom date picker
       report_to.val('');
       report_from.val('');
       $('#currency').removeClass('hide');
       if (type != 'total-income' && type != 'payment-modes') {
         report_from_choose.removeClass('hide');
       }

       if (type =='order-tracking-monthly-report') {
         report_year_choose.removeClass('hide');
         report_from_choose.addClass('hide');
       }

       if (type == 'total-income') {
         $('.chart-income').removeClass('hide');
         $('#income-years').removeClass('hide');
         date_range.addClass('hide');
       } else if (type == 'customers-report') {
         report_customers.removeClass('hide');
       } else if (type == 'customers-group') {
         $('.customers-group-gen').removeClass('hide');
       } else if (type == 'invoices-report') {
         report_invoices.removeClass('hide');
       } else if (type == 'payment-modes') {
         $('.chart-payment-modes').removeClass('hide');
         $('#income-years').removeClass('hide');
       } else if (type == 'payments-received') {
         report_payments_received.removeClass('hide');
       } else if (type == 'estimates-report') {
         report_estimates.removeClass('hide');
       } else if(type == 'proposals-report'){
        $('#proposals-reports').removeClass('hide');
      }else if(type == 'diaries-report'){
        $('#diaries-report').removeClass('hide');
      }else if(type == 'order-tracking-book-report'){
        $('#order-tracking-book-report').removeClass('hide');
      }else if(type == 'order-tracking-book-report-PO'){
        $('#order-tracking-book-report-PO').removeClass('hide');
      }else if(type == 'order-tracking-monthly-report'){
        $('#order-tracking-monthly-report').removeClass('hide');
      }else if(type == 'general-order-tracking-book-report-PO'){
        $('#general-order-tracking-book-report-PO').removeClass('hide');
      }else if(type == 'general-order-tracking-book-report'){
        $('#general-order-tracking-book-report').removeClass('hide');
      }else if(type == 'cash-funds-detailing-accounting-books'){
        $('#cash-funds-detailing-accounting-books').removeClass('hide');
      }

      gen_reports();
    }

   // Generate total income bar
   function total_income_bar_report() {
     if (typeof(salesChart) !== 'undefined') {
       salesChart.destroy();
     }
     var data = {};
     data.year = $('select[name="payments_years"]').val();
     var currency = $('#currency');
     if (currency.length > 0) {
       data.report_currency = $('select[name="currency"]').val();
     }
     $.post(admin_url + 'reports/total_income_report', data).done(function(response) {
       response = JSON.parse(response);
       salesChart = new Chart($('#chart-income'), {
         type: 'bar',
         data: response,
         options: {
           responsive: true,
           maintainAspectRatio:false,
           legend: {
            display: false,
          },
          scales: {
            yAxes: [{
              ticks: {
                beginAtZero: true,
              }
            }]
          },
        }
      })
     });
   }

   function report_by_payment_modes() {
     if (typeof(paymentMethodsChart) !== 'undefined') {
       paymentMethodsChart.destroy();
     }
     var data = {};
     data.year = $('select[name="payments_years"]').val();
     var currency = $('#currency');
     if (currency.length > 0) {
       data.report_currency = $('select[name="currency"]').val();
     }
     $.post(admin_url + 'reports/report_by_payment_modes', data).done(function(response) {
       response = JSON.parse(response);
       paymentMethodsChart = new Chart($('#chart-payment-modes'), {
         type: 'bar',
         data: response,
         options: {
           responsive: true,
           maintainAspectRatio:false,
           scales: {
            yAxes: [{
              ticks: {
                beginAtZero: true,
              }
            }]
          },
        }
      })
     });
   }
   // Generate customers report
   function customers_report() {
     if ($.fn.DataTable.isDataTable('.table-customers-report')) {
       $('.table-customers-report').DataTable().destroy();
     }
     initDataTable('.table-customers-report', admin_url + 'reports/customers_report', false, false, fnServerParams, [0, 'ASC']);
   }

   function report_by_customer_groups() {
     if (typeof(groupsChart) !== 'undefined') {
       groupsChart.destroy();
     }
     var data = {};
     data.months_report = $('select[name="months-report"]').val();
     data.report_from = report_from.val();
     data.report_to = report_to.val();

     var currency = $('#currency');
     if (currency.length > 0) {
       data.report_currency = $('select[name="currency"]').val();
     }
     $.post(admin_url + 'reports/report_by_customer_groups', data).done(function(response) {
       response = JSON.parse(response);
       groupsChart = new Chart($('#customers-group-gen'), {
         type: 'line',
         data: response,
         options:{
          maintainAspectRatio:false,
          legend: {
            display: false,
          },
          scales: {
            yAxes: [{
              ticks: {
                beginAtZero: true,
              }
            }]
          }}
        });
     });
   }

   function diaries_report() {
     if ($.fn.DataTable.isDataTable('.table-diaries-report')) {
       $('.table-diaries-report').DataTable().destroy();
     }
     initDataTable('.table-diaries-report', admin_url + 'reports/diaries_report', false, false, fnServerParams, [0, 'DESC']);
   }

   function order_tracking_book_report() {
     if ($.fn.DataTable.isDataTable('.table-order-tracking-book-report')) {
       $('.table-order-tracking-book-report').DataTable().destroy();
     }

     initDataTable('.table-order-tracking-book-report', admin_url + 'reports/order_tracking_book_report', false, false, fnServerParams, [0, 'DESC']);
     var SO_status=$('select[name="SO_status"]').selectpicker('val');
     if(SO_status==6)
     {
         _table_api = initDataTable('.table-order-tracking-book-report', admin_url + 'reports/order_tracking_book_report', false, false, fnServerParams, [0, 'DESC']);
     }
     else
     {
        _table_api = initDataTable('.table-order-tracking-book-report', admin_url + 'reports/order_tracking_book_report', false, false, fnServerParams, [0, 'DESC']);
        _table_api.column(10).visible(false, false);
        _table_api.column(11).visible(false, false).columns.adjust();
     }
   }

   function order_tracking_book_report_PO() {
     if ($.fn.DataTable.isDataTable('.table-order-tracking-book-PO-report')) {
       $('.table-order-tracking-book-PO-report').DataTable().destroy();
     }

     initDataTable('.table-order-tracking-book-PO-report', admin_url + 'reports/order_tracking_book_report_PO', false, false, fnServerParams, [0, 'DESC']);
   }

   function general_order_tracking_book_report_PO() {
     if ($.fn.DataTable.isDataTable('.table-general-order-tracking-book-PO-report')) {
       $('.table-general-order-tracking-book-PO-report').DataTable().destroy();
     }

     initDataTable('.table-general-order-tracking-book-PO-report', admin_url + 'reports/general_order_tracking_book_report_PO', false, false, fnServerParams, [0, 'DESC']);
   }

   function general_order_tracking_book_report() {

     if ($.fn.DataTable.isDataTable('.table-general-order-tracking-book-report')) {
       $('.table-general-order-tracking-book-report').DataTable().destroy();
     }
     var SO_status_gen=$('select[name="SO_status_gen"]').selectpicker('val');
     if(SO_status_gen==6)
     {
         _table_api = initDataTable('.table-general-order-tracking-book-report', admin_url + 'reports/general_order_tracking_book_report', false, false, fnServerParams, [0, 'DESC']);
         _table_api.column(8).visible(false, false);
         _table_api.column(9).visible(false, false).columns.adjust();
     }
     else if(SO_status_gen==7)
     {
         _table_api = initDataTable('.table-general-order-tracking-book-report', admin_url + 'reports/general_order_tracking_book_report', false, false, fnServerParams, [0, 'DESC']);
         _table_api.column(6).visible(false, false);
         _table_api.column(7).visible(false, false).columns.adjust();
     }
     else
     {
        _table_api = initDataTable('.table-general-order-tracking-book-report', admin_url + 'reports/general_order_tracking_book_report', false, false, fnServerParams, [0, 'DESC']);
        _table_api.column(6).visible(false, false);
        _table_api.column(7).visible(false, false);
        _table_api.column(8).visible(false, false);
        _table_api.column(9).visible(false, false).columns.adjust();
     }
   }

   function order_tracking_monthly_report() {
     if ($.fn.DataTable.isDataTable('.table-order-tracking-monthly-report')) {
       $('.table-order-tracking-monthly-report').DataTable().destroy();
     }

     initDataTable('.table-order-tracking-monthly-report', admin_url + 'reports/order_tracking_monthly_report', false, false, fnServerParams, [0, 'DESC']);
   }

   function invoices_report() {
     if ($.fn.DataTable.isDataTable('.table-invoices-report')) {
       $('.table-invoices-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-invoices-report', admin_url + 'reports/invoices_report', false, false, fnServerParams, [
       [2, 'DESC'],
       [0, 'DESC']
       ]).column(2).visible(false, false).columns.adjust();
   }

   function estimates_report() {
     if ($.fn.DataTable.isDataTable('.table-estimates-report')) {
       $('.table-estimates-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-estimates-report', admin_url + 'reports/estimates_report', false, false, fnServerParams, [
       [3, 'DESC'],
       [0, 'DESC']
       ]).column(3).visible(false, false).columns.adjust();
   }

   function payments_received_reports() {
     if ($.fn.DataTable.isDataTable('.table-payments-received-report')) {
       $('.table-payments-received-report').DataTable().destroy();
     }
     initDataTable('.table-payments-received-report', admin_url + 'reports/payments_received', false, false, fnServerParams, [1, 'DESC']);
   }

   function proposals_report() {
    if ($.fn.DataTable.isDataTable('.table-proposals-report')) {
     $('.table-proposals-report').DataTable().destroy();
    }

     initDataTable('.table-proposals-report', admin_url + 'reports/proposals_report', false, false, fnServerParams, [0, 'DESC']);
   }

   function cash_funds_detailing_accounting_books() {
    if ($.fn.DataTable.isDataTable('.table-cash-funds-detailing-accounting-books-report')) {
     $('.table-cash-funds-detailing-accounting-books-report').DataTable().destroy();
    }

     initDataTable('.table-cash-funds-detailing-accounting-books-report', admin_url + 'reports/cash_funds_detailing_accounting_books', false, false, fnServerParams, [0, 'DESC']);
   }

   // Main generate report function
   function gen_reports() { 
     if (!$('.chart-income').hasClass('hide')) {
       total_income_bar_report();
     } else if (!$('.chart-payment-modes').hasClass('hide')) {
       report_by_payment_modes();
     } else if (!report_customers.hasClass('hide')) {
       customers_report();
     } else if (!$('.customers-group-gen').hasClass('hide')) {
       report_by_customer_groups();
     } else if (!report_invoices.hasClass('hide')) {
       invoices_report();
       // Sales Diaries
     }else if(!$('#order-tracking-book-report').hasClass('hide')){
      order_tracking_book_report();
     }
      else if(!$('#diaries-report').hasClass('hide')){
      diaries_report();
     } 
     else if (!report_payments_received.hasClass('hide')) {
       payments_received_reports();
     } 
     else if (!report_estimates.hasClass('hide')) {
       estimates_report();
     } 
     else if(!$('#proposals-reports').hasClass('hide')){
      proposals_report();
    }
    else if(!$('#order-tracking-book-report-PO').hasClass('hide')){
      order_tracking_book_report_PO();
     }
     else if(!$('#order-tracking-monthly-report').hasClass('hide')){
      order_tracking_monthly_report();
     }
     else if(!$('#general-order-tracking-book-report-PO').hasClass('hide')){
      general_order_tracking_book_report_PO();
     }
     else if(!$('#general-order-tracking-book-report').hasClass('hide')){
      general_order_tracking_book_report();
     }
     else if(!$('#cash-funds-detailing-accounting-books').hasClass('hide')){
      cash_funds_detailing_accounting_books();
     }
  }

</script>
