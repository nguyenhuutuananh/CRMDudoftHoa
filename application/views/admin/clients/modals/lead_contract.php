<div class="row">
    <div class="col-md-12">
        <div class="clearfix"></div>
        <div class="panel_s">
            <?php render_datatable(array(
                _l('contract_list_code'),
                _l('contract_types_list_name'),
                _l('contract_value'),
                _l('contract_list_start_date'),
                _l('contract_list_end_date'),
                _l('options')
            ),'contracts'); ?>
        </div>
    </div>
</div>
<script>
  function create_contract_lead(client){
      initDataTable('.table-contracts', admin_url+'contracts/init_contract_client/'+client, [1], [1],'');
  }
</script>