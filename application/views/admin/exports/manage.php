<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body _buttons">                   
                    <h4 class="bold no-margin"><?=_l('export_orders')?></h4>
                    <hr class="no-mbot no-border">
                    <a href="<?=admin_url('exports/export_detail')?>" class="btn btn-info pull-left display-block"><?php echo _l('add_export_order'); ?></a>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="panel_s">
                    <div class="panel-body">
                    <input type="hidden" id="filterStatus" value="" />
                    <div data-toggle="btn" class="btn-group mbot15">
                        <button style=" font-size: 11px;" type="button" id="btnDatatableFilterAll" data-toggle="tab" class="btn btn-info active">Tất cả</button>
                        <button style=" font-size: 11px;" type="button" id="btnDatatableFilterNotApproval" data-toggle="tab" class="btn btn-info">Chưa duyệt</button>
                        <button style=" font-size: 11px;" type="button" id="btnDatatableFilterApproval" data-toggle="tab" class="btn btn-info">Đã duyệt</button>
                        <button style=" font-size: 11px;" type="button" id="btnDatatableFilterNotCreateDelivery" data-toggle="tab" class="btn btn-info">Chưa tạo phiếu giao hàng</button>
                        <button style=" font-size: 11px;" type="button" id="btnDatatableFilterCreatingDelivery" data-toggle="tab" class="btn btn-info">Đã tạo phiếu giao hàng</button>
                        <button style=" font-size: 11px;" type="button" id="btnDatatableFilterCancel" data-toggle="tab" class="btn btn-info">Phiếu hủy</button>
                    </div>
                    <?php render_datatable(array(
                            _l('#'),
                            _l('Mã phiếu xuất kho'),
                            _l('Mã đơn hàng'),
                            _l('Khách hàng'),
                            _l('total_amount'),
                            _l('Người tạo'),
                            _l('Trạng thái'),
                            // _l('Giao hàng'),
                            _l('Được duyệt bởi'),
                            _l('Ngày tạo'),
                            _l('options')
                        ),'exports'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript">
    // $(function(){
    //     initDataTable('.table-exports', window.location.href, [1], [1]);
    //     _validate_form($('form'),{unit:'required'},manage_contract_types);
    //     $('#adjustment_type').on('hidden.bs.modal', function(event) {
    //         $('#additional').html('');
    //         $('#adjustment_type input').val('');
    //         $('.add-title').removeClass('hide');
    //         $('.edit-title').removeClass('hide');
    //     });
    //     function manage_contract_types(form) {
    //     var data = $(form).serialize();
    //     var url = form.action;
    //     $.post(url, data).done(function(response) {
    //         response = JSON.parse(response);
    //         if(response.success == true){
    //             alert_float('success',response.message);
    //         }
    //         $('.table-units').DataTable().ajax.reload();
    //         $('#type').modal('hide');
    //     });
    //     return false;
    // }
    // });

    $(function(){
         $('[data-toggle="btn"] .btn').on('click', function(){
            var $this = $(this);
            $this.parent().find('.active').removeClass('active');
            $this.addClass('active');
        });
        $('#btnDatatableFilterAll').click(() => {
            $('#filterStatus').val('');
            $('#filterStatus').change();
        });
        $('#btnDatatableFilterNotApproval').click(() => {
            $('#filterStatus').val(1);
            $('#filterStatus').change();
        });
        $('#btnDatatableFilterApproval').click(() => {
            $('#filterStatus').val(2);
            $('#filterStatus').change();
        });
        $('#btnDatatableFilterNotCreateDelivery').click(() => {
            $('#filterStatus').val(3);
            $('#filterStatus').change();
        });
        $('#btnDatatableFilterCreatingDelivery').click(() => {
            $('#filterStatus').val(4);
            $('#filterStatus').change();
        });
        $('#btnDatatableFilterCancel').click(() => {
            $('#filterStatus').val(5);
            $('#filterStatus').change();
        });
        var filterList = {
            'filterStatus' : '[id="filterStatus"]',
        };
        initDataTable('.table-exports', window.location.href, [1], [1], filterList,[1,'DESC']);
        $.each(filterList, (filterIndex, filterItem) => {
            $('input' + filterItem).on('change', () => {
                $('.table-exports').DataTable().ajax.reload();
            });
        });
    });

    function var_status(status,id)
    {
        // alert("<?=admin_url()?>exports/update_status")
        dataString={id:id,status:status};
        jQuery.ajax({
            type: "post",
            url:"<?=admin_url()?>exports/update_status",
            data: dataString,
            cache: false,
            success: function (response) {
                response = JSON.parse(response);
                if (response.success == true) {
                    $('.table-exports').DataTable().ajax.reload();
                    alert_float('success', response.message);
                }
                return false;
            }
        });

    }
//     function view_init_adjustment(id)
//     {
//         $('#adjustment_type').modal('show');
//         $('.add-title').addClass('hide');
//         jQuery.ajax({
//             type: "post",
//             url:admin_url+"units/get_row_unit/"+id,
//             data: '',
//             cache: false,
//             success: function (data) {
//                 var json = JSON.parse(data);
// //                if($data!="")
//                 {
//                     $('#unit').val(json.unit);
//                     jQuery('#id_type').prop('action',admin_url+'units/update_unit/'+id);
//                 }
//             }
//         });
//     }
    $('body').on('click', '.delete-remind,.restore-remind', function() {
        var r = confirm(confirm_action_prompt);
        var table='.table-exports';
        if (r == false) {
            return false;
        } else {
            $.get($(this).attr('href'), function(response) {
                alert_float(response.alert_type, response.message);
                // Looop throug all availble reminders table to reload the data
                    if ($.fn.DataTable.isDataTable(table)) {
                        $('body').find(table).DataTable().ajax.reload();
                    }
            }, 'json');
        }
        return false;
    });
</script>

