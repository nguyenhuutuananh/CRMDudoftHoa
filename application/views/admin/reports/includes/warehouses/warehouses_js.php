<script type="text/javascript">
  var report_from = $('input[name="report-from"]');
 var report_to = $('input[name="report-to"]');
 var report_customers = $('#customers-report');
 var report_customers_groups = $('#customers-group');
 var report_from_choose = $('#report-time');
 var date_range = $('#date-range');
 var fnServerParams = {
   "report_months": '[name="months-report"]',
   "report_from": '[name="report-from"]',
   "report_to": '[name="report-to"]',
   "report_currency": '[name="currency"]',   
   "years_report": '[name="years_report"]',


 }

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
  function init_report(e, type) {
  $('#report_tiltle').text($(e).text());   
  var report_wrapper = $('#report');
   if (report_wrapper.hasClass('hide')) {
     report_wrapper.removeClass('hide');
   }
  $('#stock-card-report').addClass('hide');
  $('#detail-goods-book-report').addClass('hide');
  $('#warehouse-sumary-report').addClass('hide');
  $('#warehouse-detail-report').addClass('hide');

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

       if (type == 'stock-card-report') {
         $('#stock-card-report').removeClass('hide');
       }
      if (type == 'detail-goods-book-report') {
         $('#detail-goods-book-report').removeClass('hide');
       }
       if (type == 'warehouse-sumary-report') {
         $('#warehouse-sumary-report').removeClass('hide');
       }
       if (type == 'warehouse-detail-report') {
         $('#warehouse-detail-report').removeClass('hide');
       }
       // if (type == 'warehouse-detail-report') {
       //   $('#warehouse-detail-report').removeClass('hide');
       // }
       // if (type == 'warehouse-detail-report') {
       //   $('#warehouse-detail-report').removeClass('hide');
       // }
      gen_reports();
    }
  // Main generate report function
   function gen_reports() { 
     if (!$('#stock-card-report').hasClass('hide')) { 
       stock_card_report();
     }
     if (!$('#detail-goods-book-report').hasClass('hide')) {
       detail_goods_book_report();
     }
     if (!$('#warehouse-sumary-report').hasClass('hide')) {
       warehouse_sumary_report();
     }
     if (!$('#warehouse-detail-report').hasClass('hide')) {
       warehouse_detail_report();
     }
  }
  function stock_card_report() {
    if ($.fn.DataTable.isDataTable('.table-stock-card-report')) {
     $('.table-stock-card-report').DataTable().destroy();
    }
     initDataTable('.table-stock-card-report', admin_url + 'reports/stock_card_report', false, false, fnServerParams, [0, 'DESC']);

  }
  function genernal_receivables_suppliers_debts_report() {
    if ($.fn.DataTable.isDataTable('.table-genernal-receivables-suppliers-debts-report')) {
     $('.table-genernal-receivables-suppliers-debts-report').DataTable().destroy();
    }
     initDataTable('.table-genernal-receivables-suppliers-debts-report', admin_url + 'reports/genernal_receivables_suppliers_debts_report', false, false, fnServerParams, [0, 'DESC']);
   }
</script>