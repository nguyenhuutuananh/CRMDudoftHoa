
<div class="row">
    <div class="col-md-6">
    <?php
        if (has_permission('customers', '', 'edit')) 
        {
        ?>
            <a href="#add_care_of" class="btn btn-info mbot15" data-toggle="collapse">Thêm</a>
        <?php
        }
    ?>
        
    </div>
    <div class="col-md-12">
        <form id="add_care_of" action="<?=admin_url()?>clients/care_of" class="collapse">
            <div class="col-md-6">
                <?php echo render_date_input('start_date','start_date')?>
            </div>
            <div class="col-md-6">
                <a href="#" onclick="create_care_of()" class="mtop30 btn btn-info">Lưu</a>
            </div>
            <div class="col-md-12">
                <?php echo render_textarea('note','note')?>
            </div>
        </form>
    </div>
    <div class="col-md-12">
        <form id="update_care_of" action="<?=admin_url()?>clients/care_of" class="collapse">
            <div class="col-md-6">
                <?php echo render_date_input('start_date','start_date')?>
            </div>
            <div class="col-md-6">
                <a href="#" onclick="create_care_of()" class="mtop30 btn btn-info">Lưu</a>
            </div>
            <div class="col-md-12">
                <?php echo render_textarea('note','note')?>
            </div>
        </form>
    </div>
    <div class="col-md-12">
        <div class="clearfix"></div>
        <div class="panel_s">
            <?php render_datatable(array(
                _l('create_by'),
                _l('start_date'),
                _l('note'),
                _l('options')
            ),'care_of'); ?>
        </div>
    </div>
</div>
<script>
  function create_care_of_lead(client){
      initDataTable('.table-care_of', admin_url+'clients/init_client_care_of/'+client, [1], [1],'');
      init_datepicker();
  }
  function create_care_of()
  {
      var _form=$('#add_care_of').prop('action');
      var start_date=$('#start_date').val();
      var note=$('#note').val();
      var client=<?=$client->userid?>;
      jQuery.ajax({
          type: "post",
          url: _form,
          data: {start_date:start_date,note:note,client:client},
          dataType: "json",
          cache: false,
          success: function (data) {
              if(data.success)
              {
                  alert_float('success',data.message);
                  $('#note').val('');
                  $('.table-care_of').DataTable().ajax.reload();
              }
              else
              {
                  alert_float('danger',data.message);
              }
          }
      });
  }
    function get_data_care_of(id)
    {
        var opt = {
            format: 'Y-m-d',
            timepicker: false,
            scrollInput: false,
            lazyInit: true,
            dayOfWeekStart: calendar_first_day,
        };
        $('.note_'+id).prop('style','display:block');
        $('._note_'+id).prop('style','display:none!important');

        $('.start_date__'+id).prop('style','display:block');
        $('.start_date_'+id).prop('style','display:block');
        $('.start_date_'+id).datetimepicker(opt);
        $('._start_date_'+id).prop('style','display:none!important');
    }
    function upadte_date(id,start_date)
    {
        var _form='<?=admin_url()?>clients/care_of/'+id;
        jQuery.ajax({
            type: "post",
            url: _form,
            data: {start_date:start_date},
            dataType: "json",
            cache: false,
            success: function (data) {
                if(data.success)
                {
                    alert_float('success',data.message);
                    $('.table-care_of').DataTable().ajax.reload();
                }
                else
                {
                    alert_float('danger',data.message);
                }
            }
        });
    }
    function upadte_note(id,note)
    {
        var _form='<?=admin_url()?>clients/care_of/'+id;
        jQuery.ajax({
            type: "post",
            url: _form,
            data: {note:note},
            dataType: "json",
            cache: false,
            success: function (data) {
                if(data.success)
                {
                    alert_float('success',data.message);
                    $('.table-care_of').DataTable().ajax.reload();
                }
                else
                {
                    alert_float('danger',data.message);
                }
            }
        });
    }
    function delete_care_of(id)
    {
        var kiemtra= confirm("Bạn chắc có muốn xóa");
        if(kiemtra)
        {
            var _form='<?=admin_url()?>clients/delete_care_of/'+id;
            jQuery.ajax({
                type: "post",
                url: _form,
                data: '',
                dataType: "json",
                cache: false,
                success: function (data) {
                    if(data.success)
                    {
                        alert_float('success',data.message);
                        $('.table-care_of').DataTable().ajax.reload();
                    }
                    else
                    {
                        alert_float('danger',data.message);
                    }
                }
            });
        }
    }
</script>