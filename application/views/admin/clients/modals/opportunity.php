
<div class="row">
    <div class="col-md-12">
        <div class="clearfix"></div>
        <div class="panel_s">
            <?php render_datatable(array(
                _l('campaign'),
                _l('contact'),
                _l('performance'),
                _l('staff_in'),
                _l('expected'),
                _l('__end_date'),
                _l('options'),
                _l('step')
            ),
                'opportunity'); ?>
        </div>
    </div>
</div>
<script>
    var filterList="";
    function tab_opportunity(client) {
        initDataTable('.table-opportunity', admin_url + 'clients/init_opportunity/' + client, [0], [0], filterList, [0, 'DESC']);
    }

    function update_status(id,status)
    {
        dataString={id:id,status:status};
        jQuery.ajax({
            type: "post",
            url:"<?=admin_url()?>opportunity/update_status",
            data: dataString,
            cache: false,
            success: function (response) {
                console.log(response);
                response = JSON.parse(response);
                if (response.success == true) {
                    $('.table-opportunity').DataTable().ajax.reload();
                    alert_float('success', response.message);
                }
            }
        });
        return false;
    }

    function delete_opportunity(id)
    {
        dataString={id:id};
        jQuery.ajax({
            type: "post",
            url:"<?=admin_url()?>clients/delete_opportunity",
            data: dataString,
            cache: false,
            success: function (response) {
                response = JSON.parse(response);
                if (response.success == true) {
                    $('.table-opportunity').DataTable().ajax.reload();
                    alert_float('success', response.message);
                }
            }
        });
        return false;
    }
//  function create_care_of_lead(client){
//
//      initDataTable('.table-opportunity', window.location.href, [0], [0], filterList, [0,'DESC']);
//      initDataTable('.table-care_of', admin_url+'clients/init_client_care_of/'+client, [1], [1],'');
//      init_datepicker();
//  }
//  function create_care_of()
//  {
//      var _form=$('#add_care_of').prop('action');
//      var start_date=$('#start_date').val();
//      var note=$('#note').val();
//      var client=<?//=$client->userid?>//;
//      jQuery.ajax({
//          type: "post",
//          url: _form,
//          data: {start_date:start_date,note:note,client:client},
//          dataType: "json",
//          cache: false,
//          success: function (data) {
//              if(data.success)
//              {
//                  alert_float('success',data.message);
//                  $('#note').val('');
//                  $('.table-care_of').DataTable().ajax.reload();
//              }
//              else
//              {
//                  alert_float('danger',data.message);
//              }
//          }
//      });
//  }
//    function get_data_care_of(id)
//    {
//        var opt = {
//            format: 'Y-m-d',
//            timepicker: false,
//            scrollInput: false,
//            lazyInit: true,
//            dayOfWeekStart: calendar_first_day,
//        };
//        $('.note_'+id).prop('style','display:block');
//        $('._note_'+id).prop('style','display:none!important');
//
//        $('.start_date__'+id).prop('style','display:block');
//        $('.start_date_'+id).prop('style','display:block');
//        $('.start_date_'+id).datetimepicker(opt);
//        $('._start_date_'+id).prop('style','display:none!important');
//    }
//    function upadte_date(id,start_date)
//    {
//        var _form='<?//=admin_url()?>//clients/care_of/'+id;
//        jQuery.ajax({
//            type: "post",
//            url: _form,
//            data: {start_date:start_date},
//            dataType: "json",
//            cache: false,
//            success: function (data) {
//                if(data.success)
//                {
//                    alert_float('success',data.message);
//                    $('.table-care_of').DataTable().ajax.reload();
//                }
//                else
//                {
//                    alert_float('danger',data.message);
//                }
//            }
//        });
//    }
//    function upadte_note(id,note)
//    {
//        var _form='<?//=admin_url()?>//clients/care_of/'+id;
//        jQuery.ajax({
//            type: "post",
//            url: _form,
//            data: {note:note},
//            dataType: "json",
//            cache: false,
//            success: function (data) {
//                if(data.success)
//                {
//                    alert_float('success',data.message);
//                    $('.table-care_of').DataTable().ajax.reload();
//                }
//                else
//                {
//                    alert_float('danger',data.message);
//                }
//            }
//        });
//    }
//    function delete_care_of(id)
//    {
//        var kiemtra= confirm("Bạn chắc có muốn xóa");
//        if(kiemtra)
//        {
//            var _form='<?//=admin_url()?>//clients/delete_care_of/'+id;
//            jQuery.ajax({
//                type: "post",
//                url: _form,
//                data: '',
//                dataType: "json",
//                cache: false,
//                success: function (data) {
//                    if(data.success)
//                    {
//                        alert_float('success',data.message);
//                        $('.table-care_of').DataTable().ajax.reload();
//                    }
//                    else
//                    {
//                        alert_float('danger',data.message);
//                    }
//                }
//            });
//        }
//    }
</script>