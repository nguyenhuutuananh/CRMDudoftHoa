<?php init_head(); ?>
<link rel="stylesheet" href="<?=base_url('assets/treegrid/')?>css/jquery.treegrid.css">
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body _buttons">
                        <a href="#" onclick="new_account(); return false;" class="btn mright5 btn-info pull-left display-block"><?php echo _l('Thêm tài khoản mới'); ?></a>

                        <!-- <a href="<?php echo admin_url() . 'warehouses'?> " class="btn btn-info pull-left display-block"><?php echo _l('Trở lại Kho'); ?></a> -->
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <table class="table tree">
                            <thead>
                                <th>Số tài khoản</th>
                                <th>Tên tài khoản</th>
                                <th>Tính chất</th>
                                <th>Tên tiếng anh</th>
                                <th>Số dư đầu kỳ</th>
                                <th><?=_l('actions')?></th>
                            </thead>
                            <tbody>
                                
                            </tbody>
                            
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="type" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('accounts/ajax'),array('id'=>'id_type')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('Sửa tài khoản'); ?></span>
                    <span class="add-title"><?php echo _l('Thêm tài khoản'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo render_input('accountCode','Số tài khoản'); ?>
                    </div>
                    <div class="col-md-12">
                        <?php echo render_input('accountName','Tên tài khoản'); ?>
                    </div>
                    <div class="col-md-12">
                        <?php echo render_input('accountEnglishName','Tên tiếng Anh'); ?>
                    </div>
                    <div class="col-md-12">
                        <?php echo render_select('generalAccount', $accounts, array('idAccount', 'accountCode'),'TK tổng hợp'); ?>
                    </div>
                    <div class="col-md-12">
                        <?php echo render_select('idAccountAttribute', $accountAttributes, array('idAttribute', 'attributeName'), 'Tính chất', '', array(), array(), '', '', false); ?>
                    </div>
                    <div class="col-md-12">
                        <?php echo render_input('amount','Số dư đầu kỳ','0','number'); ?>
                    </div>
                    <div class="col-md-12">
                        <?php echo render_textarea('accountExplain', 'Diễn giải'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button data-loading-text="<i class=\'fa fa-circle-o-notch fa-spin\'></i> Đang xử lý" type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div><!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal fade lead-modal" id="detail" tabindex="-1" role="dialog"  >
    <div class="modal-dialog" style="width: 70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('warehouse_info'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">

                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php init_tail(); ?>
<script type="text/javascript" src="<?=base_url('assets/treegrid/')?>js/jquery.treegrid.js"></script>
<script>
    let TreeGridLoad = () => {
        $.ajax({
            url: admin_url + 'accounts/getAccounts',
            dataType: 'json',
        }).done((data) => {
            $('.table.tree tbody').find('tr').remove();
            data.forEach(obj => {
                let newTr = $('<tr class="treegrid-'+obj.idAccount+'"></tr>');
                
                newTr.append('<td>'+obj.accountCode+'</td>');
                newTr.append('<td>'+obj.accountName+'</td>');
                newTr.append('<td>'+obj.attributeName+'</td>');
                newTr.append('<td>'+obj.accountEnglishName+'</td>');
                newTr.append('<td>'+obj.amount+'</td>');
                newTr.append('<td>' 
                + '<a href="#" data-loading-text="<i class=\'fa fa-circle-o-notch fa-spin\'></i>" class="btn btn-default btn-icon" onclick="view_init_department('+obj.idAccount+', this); return false;"><i class="fa fa-eye"></i></a>'
                + '<a href="' + admin_url +'accounts/delete/' + obj.idAccount + '" class="btn btn-danger btn-icon delete-reminder-other"><i class="fa fa-remove"></i></a></td>');
                if(obj.generalAccount != 0)
                {
                    newTr.addClass('treegrid-parent-' + obj.generalAccount);
                }
                else {
                    newTr.find('td').wrapInner('<b></b>');
                }
                $('.table.tree').append(newTr);
                
            });
            $('.tree').treegrid({
                //initialState: 'collapsed',
            });
        });
    };
    $(() => {
        $('body').on('click', '.delete-reminder-other', (e) => {
            var r = confirm(confirm_action_prompt);
            if (r == false) {
                return false;
            } else {
                $.get($(e.currentTarget).attr('href'), function(response) {
                    alert_float(response.alert_type, response.message);
                    TreeGridLoad();
                }, 'json');
            }
            return false;
        });
        TreeGridLoad();
        
    });
    
    function view_init_department(id, button)
    {
        $(button).button('loading');
        jQuery.ajax({
            type: "post",
            url:admin_url+"accounts/get_row/"+id,
            data: '',
            cache: false,
            dataType: 'json',
            success: function (data) {
               if(data!="")
                {
                    console.log(data);
                    $(button).button('reset');
                    $('#type').modal('show');
                    $('.add-title').addClass('hide');
                    $('#accountCode').val(data.accountCode);
                    $('#accountName').val(data.accountName);
                    $('#accountEnglishName').val(data.accountEnglishName);
                    $('#generalAccount').selectpicker('val', data.generalAccount);
                    $('#idAccountAttribute').selectpicker('val', data.idAccountAttribute);
                    $('#amount').val(data.amount);
                    $('#accountExplain').val(data.accountExplain);
                    jQuery('#id_type').prop('action',admin_url+'accounts/ajax/'+id);
                }
            }
        });
    }

    $(function(){
        _validate_form($('form'),{
            accountCode:'required',
            accountName:'required',
            idAccountAttribute:'required',

            }, manage_contract_types);
        $('#type').on('hidden.bs.modal', function(event) {
            $('#additional').html('');
            $('#type input').val('');
            $('.add-title').removeClass('hide');
            $('.edit-title').removeClass('hide');
        });
    });

    
    function manage_contract_types(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            $(form).find('button[type="submit"]').button('loading');
            if(response.success == true){
                alert_float('success',response.message);
                TreeGridLoad();
            }
            $(form).find('button[type="submit"]').button('reset');
            $('#type').modal('hide');
        });
        return false;
    }

    function new_account(){
        $('#type').modal('show');
        $('.edit-title').addClass('hide');
        jQuery('#category').val('');
        jQuery('#id_type').prop('action',admin_url+'accounts/ajax');
    }
    function edit_type(invoker,id){
        var name = $(invoker).data('name');
        $('#additional').append(hidden_input('id',id));
        $('#type input[name="category"]').val(name);
        $('#type').modal('show');
        $('.add-title').addClass('hide');
    }
</script>
</body>
</html>