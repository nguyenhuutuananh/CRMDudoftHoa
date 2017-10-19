<div role="tabpanel" class="tab-pane" id="client_report">
    <?php if($_client) $client->userid=$_client;?>
    <?php
        if (has_permission('customers', '', 'edit')) 
        {
        ?>
            
        <?php echo form_open(admin_url('clients/add_report_client/'.$client->userid),array('id'=>'client-report')); ?>
            <?php echo render_textarea('note'); ?>
            <button type="button" onclick="addreport_client(<?=$client->userid?>)" class="btn btn-info pull-right"><?php echo _l('lead_add_edit_add_note'); ?></button>
            <div class="clearfix"></div>
        <?php echo form_close(); ?>
            
        <?php
        }
    ?>
    
    
    <hr />
    <div class="panel_s mtop20 view_report">
        <?php $notes=$this->clients_model->get_table_where('tblreport_client','id_client='.$client->userid,'id desc')?>
        <?php
        $len = count($notes);
        $i = 0;
        foreach($notes as $note){ ?>
            <div class="media client_report">
                <a href="<?php echo admin_url('profile/'.$note["addedfrom"]); ?>" target="_blank">
                    <?php echo staff_profile_image($note['addedfrom'],array('staff-profile-image-small','pull-left mright10')); ?>
                </a>
                <div class="media-body">
                    <?php if($note['addedfrom'] == get_staff_user_id() || is_admin()){ ?>
                        <a href="#" class="pull-right text-danger" onclick="delete_client_report(<?=$client->userid?>,<?php echo $note['id']; ?>);return false;"><i class="fa fa fa-times"></i></a>
                        <a href="#" class="pull-right mright5" onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><i class="fa fa-pencil-square-o"></i></a>
                    <?php } ?>
                    <?php if(!empty($note['date'])){ ?>
                        <span data-toggle="tooltip" data-title="<?php echo _d($note['date']); ?>">
               <i class="fa fa-phone-square text-success font-medium valign" aria-hidden="true"></i>
            </span>
                    <?php } ?>
                    <small><?php echo _l('lead_note_date_added',_d($note['date'])); ?></small>
                    <a href="<?php echo admin_url('profile/'.$note["addedfrom"]); ?>" target="_blank">
                        <h5 class="media-heading bold"><?php echo get_staff_full_name($note['addedfrom']); ?></h5>
                    </a>
                    <div data-note-description="<?php echo $note['id']; ?>" class="text-muted">
                        <?php echo $note['note']; ?>
                    </div>
                    <div data-note-edit-textarea="<?php echo $note['id']; ?>" class="hide mtop15">
                        <?php echo render_textarea('note_'.$note['id'],'',$note['note']); ?>
                        <div class="text-right">
                            <button type="button" class="btn btn-default" onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><?php echo _l('cancel'); ?></button>
                            <button type="button" class="btn btn-info" onclick="edit_report_client(<?=$client->userid?>,<?php echo $note['id']; ?>);"><?php echo _l('update_note'); ?></button>
                        </div>
                    </div>
                </div>
                <?php if ($i >= 0 && $i != $len - 1) {
                    echo '<hr />';
                }
                ?>
            </div>
            <?php $i++; } ?>
    </div>
</div>
<script>
    function addreport_client(id_client)
    {
        var _form=$('#client-report').prop('action');
        var note=$('#client-report #note').val();
        var client=id_client;
        jQuery.ajax({
            type: "post",
            url: _form,
            data: {note:note,client:client},
            dataType: "json",
            cache: false,
            success: function (data) {
                console.log(data);
                if(data.success)
                {
                    alert_float('success',data.message);
                    $("#client_report .view_report").load('<?=admin_url()?>clients/model_comment/'+id_client + " .view_report");
                }
                else
                {
                    alert_float('danger',data.message);
                }
            }
        });
    }
    function edit_report_client(id_client,id)
    {
        var _form="<?=admin_url('clients/add_report_client/'.$client->userid)?>/"+id;
        var note=$('#note_'+id).val();
        var client=id_client;
        jQuery.ajax({
            type: "post",
            url: _form,
            data: {note:note,client:client,id:id},
            dataType: "json",
            cache: false,
            success: function (data) {
                if(data.success)
                {
                    alert_float('success',data.message);

                    $("#client_report .view_report").load('<?=admin_url()?>clients/model_comment/'+id_client + " .view_report");
                }
                else
                {
                    alert_float('danger',data.message);
                }
            }
        });
    }
    function delete_client_report(id_client,id)
    {
        var _form="<?=admin_url('clients/delete_report_client/'.$client->userid)?>/"+id;
        var note=$('#note_'+id).val();
        var client=id_client;
        jQuery.ajax({
            type: "post",
            url: _form,
            data: {note:note,client:client,id:id},
            dataType: "json",
            cache: false,
            success: function (data) {
                if(data.success)
                {
                    alert_float('success',data.message);

                    $("#client_report .view_report").load('<?=admin_url()?>clients/model_comment/'+id_client + " .view_report");
                }
                else
                {
                    alert_float('danger',data.message);
                }
            }
        });
    }
</script>