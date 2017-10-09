<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <?php if(has_permission('items','','create')){ ?>
        <div class="panel_s">
          <div class="panel-body _buttons">
            <a href="<?php echo admin_url('campaign/campaign') ?>" class="btn btn-info pull-left"><?php echo _l('create_campaign'); ?></a>
          </div>

        </div>
        <?php } ?>
        <div class="panel_s">
          <div class="panel-body">
            <div class="clearfix"></div>
            <p></p>
            <?php render_datatable(array(
              _l('name_campaign'),
              _l('manage_campaign'),
              _l('staff_participation'),
              _l('cost_campaign'),
              _l('create_by'),
              _l('_start_date'),
              _l('__end_date'),
              _l('options')
              ),
              'campaign'); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<style>
    .modal-lg-80{
        width: 80%!important;
    }
</style>
  <?php $this->load->view('admin/invoice_items/item'); ?>



        <div id="send_email-campaign" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg-80">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><?=_l('send_email_client_on_campaign')?></h4>
                    </div>
                    <div class="modal-body">
                        <div class="col-md-6 form-send">
                            <div style="text-align: right"><a onclick="full_col()"><i class="fa fa-expand"></i></a></div>
                            <form method="post" id="form-sendemail" action="<?=admin_url()?>campaign/send_email">
                                <input type="hidden" name="email_bcc" id="email_bcc">
                                <input type="hidden" name="campaign" id="campaign">
                                <div class="form-group">
                                    <div class="checkbox checkbox-success mbot20 no-mtop">
                                        <input type="checkbox" name="type_email" id="type_email" onchange="kiemtra_type_email(this.value)">
                                        <label for="type_email">Sử dụng template</label>
                                    </div>
                                </div>
                                <div class="form-group select_template" style="display:none;">
                                    <label for="view_template">Mẫu email:</label>
                                    <?php echo render_select('view_template',$email_plate,array('id','name'),'','',array('onchange'=>'get_contentemail(this.value)','data-width'=>'100%','data-none-selected-text'=>_l('chọn Mẫu email'))); ?>
                                </div>
                                <div class="form-group">
                                    <label for="subject">Chủ đề:</label>
                                    <input type="text" class="form-control" id="subject" name="subject"  value="" placeholder="Nhập chủ đề của bạn..." required >
                                </div>
                                <div class="form-group">
                                    <p class="bold"><?php echo _l('email_content'); ?></p>
                                    <?php echo render_textarea('message','','',array('data-task-ae-editor'=>true),array(),'','tinymce-task'); ?>
                                </div>
                                <div class="form-group" style="display: none;">
                                    <p class="bold"><?php echo _l('file'); ?></p>
                                    <?php echo render_textarea('file_send','','',array('data-task-ae-editor'=>true),array(),'',''); ?>
                                </div>

                            </form>
                            <div class="form-group file_dropzone"></div>
                            <div class="clearfix"></div>
                            <div class="form-group">
                                <?php echo form_open_multipart(admin_url('email_marketing/upload_file'),array('class'=>'dropzone','id'=>'email-upload','onchane'=>'get_delete(this)')); ?>
                                <input type="file" name="file" multiple />
                                <?php echo form_close(); ?>
                                <div class="text-right mtop15">
                                    <div id="dropbox-chooser"></div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="col-md-6 form-code">
                            <div class="panel_s">
                                <div class="panel-body">
                                    <div class="col-md-6 merge_fields_col">
                                        <hr>
                                        <h5>Thông tin chung</h5>
                                        <hr>
                                        <?php foreach($field as $row=> $fi){?>
                                            <p style="font-size: 12px;"><?=_l('tblclients.'.$fi)?><span class="pull-right"><a href="#" class="add_merge_field">{tblclients.<?=$fi?>}</a></span></p>
                                        <?php }?>
                                    </div>

                                    <div class="col-md-6 merge_fields_col">
                                        <hr>
                                        <h5>Doanh nghiệp</h5>
                                        <hr>
                                        <?php foreach($field2 as $row2=> $fi2){?>
                                            <p><?=_l('tblclients.'.$fi2)?><span class="pull-right"><a href="#" class="add_merge_field">{tblclients.<?=$fi2?>}</a></span></p>
                                        <?php }?>
                                        <hr>
                                        <h5>Nhân viên</h5>
                                        <hr>
                                        <?php foreach($fieldstaff as $num=>$fis){?>
                                            <p style="font-size: 12px;"><?=_l('tblstaff.'.$fis)?><span class="pull-right"><a href="#" class="add_merge_field">{tblstaff.<?=$fis?>}</a></span></p>
                                        <?php }?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="modal-footer">
                        <button type="button" onclick="send_email_campaign('form-sendemail')" class="btn btn-info">Gửi</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>








  <div class="modal fade" id="groups" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">
            <?php echo _l('item_groups'); ?>
          </h4>
        </div>
        <div class="modal-body">
          <?php if(has_permission('items','','create')){ ?>
          <div class="input-group">
            <input type="text" name="item_group_name" id="item_group_name" class="form-control" placeholder="<?php echo _l('item_group_name'); ?>">
            <span class="input-group-btn">
              <button class="btn btn-info p7" type="button" id="new-item-group-insert"><?php echo _l('new_item_group'); ?></button>
            </span>
          </div>
          <hr />
          <?php } ?>
          <div class="row">
           <div class="container-fluid">
            <table class="table table-striped dt-table table-purchase" data-order-col="0" data-order-type="asc">
              <thead>
                <tr>
                  <th><?php echo _l('item_group_name'); ?></th>
                  <th><?php echo _l('options'); ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($items_groups as $group){ ?>
                <tr data-group-row-id="<?php echo $group['id']; ?>">
                  <td data-order="<?php echo $group['name']; ?>">
                    <span class="group_name_plain_text"><?php echo $group['name']; ?></span>
                    <div class="group_edit hide">
                     <div class="input-group">
                      <input type="text" class="form-control">
                      <span class="input-group-btn">
                        <button class="btn btn-info p7 update-item-group" type="button"><?php echo _l('submit'); ?></button>
                      </span>
                    </div>
                  </div>
                </td>
                <td align="right">
                  <?php if(has_permission('items','','edit')){ ?><button type="button" class="btn btn-default btn-icon edit-item-group"><i class="fa fa-pencil-square-o"></i></button><?php } ?>
                  <?php if(has_permission('items','','delete')){ ?><a href="<?php echo admin_url('invoice_items/delete_group/'.$group['id']); ?>" class="btn btn-danger btn-icon delete-item-group _delete"><i class="fa fa-remove"></i></a><?php } ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>

<script>
    init_editor('.tinymce-task',{height:300});
    $('.add_merge_field').on('click', function(e) {
        e.preventDefault();
        tinymce.activeEditor.execCommand('mceInsertContent', false, $(this).text());
    });
    function kiemtra_type_email(id)
    {
        if($('#type_email').prop('checked')==true)
        {
            $('.select_template').show();
        }
        else
        {
            $('#view_template').val(0).selectpicker('refresh');
            $('.select_template').hide();
            var content = tinymce.get("message").setContent('');
        }
    }
    function get_contentemail(id)
    {
        jQuery.ajax({
            type: "post",
            url: "<?=admin_url()?>email_marketing/get_email/"+id,
            data: '',
            dataType:"json",
            cache: false,
            success: function (data) {
                var content = tinymce.get("message").setContent(data.content);
                $('#subject').val(data.subject);
            }
        });
    }
  function var_status(status,id)
    {
        dataString={id:id,status:status};
        jQuery.ajax({
            type: "post",
            url:"<?=admin_url()?>receipts/update_status",
            data: dataString,
            cache: false,
            success: function (response) {
                response = JSON.parse(response);
                if (response.success == true) {
                    $('.table-campaign').DataTable().ajax.reload();
                    alert_float('success', response.message);
                }
            }
        });
        return false;
    }

    Dropzone.options.clientAttachmentsUpload = false;
    if ($('#email-upload').length > 0) {
        new Dropzone('#email-upload', {
            paramName: "file",
            dictDefaultMessage:drop_files_here_to_upload,
            dictFallbackMessage:browser_not_support_drag_and_drop,
            dictRemoveFile:remove_file,
            dictFileTooBig: file_exceds_maxfile_size_in_form,
            dictMaxFilesExceeded:you_can_not_upload_any_more_files,
            maxFilesize: max_php_ini_upload_size.replace(/\D/g, ''),
            addRemoveLinks: false,
            accept: function(file, done) {
                done();
            },
            acceptedFiles: allowed_files,
            error: function(file, response) {
                alert_float('danger', response);
            },
            success: function(file, response) {

                var mang=$('#file_send').val();
                $('#file_send').val(mang+','+response);
                s_tring=$('#file_send').val()
                jQuery.ajax({
                    type: "post",
                    url: "<?=admin_url()?>email_marketing/tring_field",
                    data: {s_tring:s_tring},
                    cache: false,
                    success: function (data) {
                        debugger;
                        $('#file_send').val(data);
                    }
                });

                if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                    $('.dz-preview').remove();
                    $('.dz-default').show();
                    count=$('.c_file').length;
                    $('.file_dropzone').append('<div class="col-md-2 c_file" id="i_file-'+count+'" title='+response+'>'+response+' <a class="btn  btn-icon" onclick="delete_file('+count+')"><i class="glyphicon glyphicon-remove-circle"></i></a>' +
                        '<img src="<?=base_url()?>assets/images/document.png" style="height:100px">'
                        +'</div>')
                }
            }
        });
    }
    function get_client_campaign(id)
    {
        $('#email_bcc').val('');
        $('#view_template').val('').selectpicker('refresh');
        $('#subject').val('');
        var content = tinymce.get("message").setContent('');
        jQuery.ajax({
            type: "post",
            url: "<?=admin_url()?>campaign/get_opportunity",
            data: {id:id},
            cache: false,
            success: function (data) {
                if(data!="")
                {
                    $('#email_bcc').val(data);
                    $('#campaign').val(id);
                }
                else
                {
                    alert_float('danger','<?=_l('not_find_email_campaign')?>')
                }

            }
        });

    }
    function full_col()
    {
        var form_send=$('.form-send').attr('class');
        if(form_send=='col-md-6 form-send')
        {
            $('.form-send').prop('class','col-md-12 form-send');
            $('.form-code').prop('class','col-md-6 form-code hide');
        }
        else
        {
            $('.form-send').prop('class','col-md-6 form-send');
            $('.form-code').prop('class','col-md-6 form-code');
        }

    }
  $(function(){
    $('[data-toggle="btns"] .btn').on('click', function(){
        var $this = $(this);
        $this.parent().find('.active').removeClass('active');
        $this.addClass('active');
    });
    $('#btnDatatableFilterAll').click(() => {
        $('#filterStatus').val('');
        $('#filterStatus').change();
    });
    $('#btnDatatableFilterNotApproval').click(() => {
        $('#filterStatus').val(0);
        $('#filterStatus').change();
    });
    $('#btnDatatableFilterApproval').click(() => {
        $('#filterStatus').val(2);
        $('#filterStatus').change();
    });

    var filterList = {
        'filterStatus' : '[id="filterStatus"]',
    };
    initDataTable('.table-campaign', window.location.href, [0], [0], filterList, [0,'DESC']);
    $.each(filterList, (filterIndex, filterItem) => {
      $('input' + filterItem).on('change', () => {
          $('.table-campaign').DataTable().ajax.reload();
      });
    });
  });

    function send_email_campaign(idfrom)
    {
        $('#message').val(tinymce.get("message").getContent());
        var action=$('#'+idfrom).attr('action');
        var form = $('#'+idfrom);
        $.ajax( {
            type: "POST",
            url:action,
            dataType : 'json',
            data:form.serialize(),
            success: function(data) {
                if(data.tb)
                {
                    alert_float(data.tb,data.message_display);
                    $('#send_email-campaign').modal('hide');
                }

            }
        } );
    }
</script>
</body>
</html>