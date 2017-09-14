<div class="row">
    <div class="col-md-12">
        <div class="clearfix"></div>
        <div class="panel_s">
            <?php render_datatable(array(
                _l('Mã phiếu báo giá'),
                _l('total_amount'),
                _l('Người tạo'),
                _l('Trạng thái'),
                _l('Được duyệt bởi'),
                _l('Ngày tạo'),
                _l('options')
            ),'quotes'); ?>
        </div>
    </div>
</div>
<script>
  function create_quote_lead(client){
    initDataTable('.table-quotes', admin_url+'quotes/init_client_quotes/'+client, [1], [1],'');
  }
</script>