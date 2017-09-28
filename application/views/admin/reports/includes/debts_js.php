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
  $('#genernal-receivables-debts-report').addClass('hide');

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

       if (type == 'genernal-receivables-debts-report') {
         $('#genernal-receivables-debts-report').removeClass('hide');
       }
      gen_reports();
    }
  // Main generate report function
   function gen_reports() { 
     if (!$('#genernal-receivables-debts-report').hasClass('hide')) { 
       genernal_receivable_debts_report();
     }
  }
  function genernal_receivable_debts_report() {
    if ($.fn.DataTable.isDataTable('.table-genernal-receivables-debts-report')) {
     $('.table-genernal-receivables-debts-report').DataTable().destroy();
    }

     initDataTable('.table-genernal-receivables-debts-report', admin_url + 'reports/genernal_receivable_debts_report', false, false, fnServerParams, [0, 'DESC']);
   }
</script>