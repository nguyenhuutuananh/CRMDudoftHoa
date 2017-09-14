<div class="row">
    <div class="col-md-12">
        <div class="clearfix"></div>
        <div class="panel_s">
            <?php render_datatable(array(
                _l('code_sales_EO'),
                _l('Mã đơn hàng(PO)'),
                _l('total_amount'),
                _l('status'),
                _l('staff_browse'),
                _l('date_create'),
                _l('options')
            ),'sales'); ?>
        </div>
    </div>
</div>
<script>
  function create_sales_lead(client){
    initDataTable('.table-sales', admin_url+'sales/list_sales_client/'+client, [1], [1],'');
  }
</script>