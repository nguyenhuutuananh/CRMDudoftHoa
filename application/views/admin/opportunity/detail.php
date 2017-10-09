<?php init_head(); ?>
<div id="wrapper" class="customer_profile">
 <div class="content">
   <div class="row">

  <div class="col-md-12">
   <div class="panel_s">
     <div class="panel-body">
      <div class="clearfix"></div>
         <h4 class="bold no-margin"><?php echo (isset($opportunity) ? _l('opportunity_edit') : _l('opportunity_add')); ?></h4>
  <hr class="no-mbot no-border" />
    <div class="row">
    <div class="additional"></div>
    <div class="col-md-12">
        <?php $disabled='disabled';?>
        <div class="ribbon <?=$type?>"><span><?=$status?></span></div>
        <ul class="nav nav-tabs profile-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#item_detail" aria-controls="item_detail" role="tab" data-toggle="tab">
                    <?php echo _l('purchase_suggested_information'); ?>
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="item_detail">
            <div class="row">
                
                <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                    
                </div>
                
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 _buttons">
                    <div class="pull-right"></div>
                </div>
            </div>
            
            <?php echo form_open_multipart($this->uri->uri_string(), array('class' => 'client-form', 'autocomplete' => 'off')); ?>
                <div class="row">
                  <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                      <?php
                      $client_disabled=array('onchange'=>'chang_client(this.value)');
                      ?>
                      <?php $selected_client = (isset($opportunity) ? $opportunity->client : "");?>
                      <?php echo render_select('client',$client,array('userid','company','code'),_('client'),$selected_client,$client_disabled); ?>

                      <div class="form-group">
                          <label for="client" class="control-label"><?=_l('contact')?></label>
                          <select id="contact" name="contact" class="selectpicker" data-width="100%" onchange="get_data_contact(this.value)" data-none-selected-text="Chọn người liên hệ" data-live-search="true" tabindex="-98">
                              <option value=""></option>
                              <?php if($contact){?>
                                  <?php foreach($contact as $rom){?>
                                      <?php $selected="";?>
                                      <?php if($rom['id']==$opportunity->contact) $selected='selected';?>
                                        <option value="<?=$rom['id']?>" <?=$selected?>><?=$rom['firstname']?> <?=$rom['lastname']?></option>
                                      <?php }?>
                              <?php }?>
                          </select>
                      </div>
                      <?php
                      $expense="";
                      ?>
                      <?php $value = (isset($opportunity) ? $opportunity->email : "");?>
                      <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="margin-left: -15px;">
                        <?php echo  render_input('email', _l('email'), $value);?>
                      </div>
                      <?php $value = (isset($opportunity) ? $opportunity->phone : "");?>
                      <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="margin-right: -15px;">
                        <?php echo  render_input('phone', _l('_phone'), $value);?>
                      </div>
                      <?php $value = (isset($opportunity) ? $opportunity->content : "");?>
                      <?php echo  render_textarea('content', _l('content'), $value);?>

                      <?php $value = (isset($opportunity) ? $opportunity->performance : "");?>
                      <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="margin-left: -15px;">
                        <?php echo  render_input('performance', _l('performance'), $value);?>
                      </div>

                      <?php $value = (isset($opportunity) ? $opportunity->expected : "");?>
                      <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="margin-right: -15px;">
                        <?php echo  render_input('expected', _l('_expected'), $value);?>
                      </div>

                      <?php $selected_staff = (isset($opportunity) ? $opportunity->staff_in : "");?>
                      <?php echo render_select('staff_in',$staff,array('staffid','fullname','staff_code'),_('staff_in'),$selected_staff,$client_disabled); ?>

                      <?php $selected_campaign = (isset($opportunity) ? $opportunity->campaign : "");?>
                      <?php echo render_select('campaign',$campaign,array('id','name'),_('campaign'),$selected_campaign,array('onchangea'=>'select_step(this.value)')); ?>


                      <?php $selected_campaign_step = (isset($opportunity) ? $opportunity->step : "");?>
                      <?php echo render_select('step',$step,array('id','name'),_('step'),$selected_campaign_step,$client_disabled); ?>

                      <?php $value = (isset($opportunity) ? $opportunity->status : "");?>
                      <?php echo  render_input('status', _l('status_opportunity'), $value);?>

                      <?php $value = (isset($opportunity) ? $opportunity->end_date : "");?>
                      <?php echo  render_date_input('end_date', _l('_end_date'), $value);?>

                      <?php $selected_source = (isset($opportunity) ? $opportunity->source : "");?>
                      <?php echo  render_select('source',$source,array('id','name'), _l('source'), $selected_source);?>

                      <?php $value = (isset($opportunity) ? $opportunity->source_details : "");?>
                      <?php echo  render_textarea('source_details', _l('source_details'), $value);?>
                  </div>


                

                <!-- Edited -->
                <!-- End edited -->
                
<!--                --><?php //if(isset($receipts) && $receipts->status == 0 || !isset($receipts)) { ?>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                      <button class="btn btn-info mtop20 only-save customer-form-submiter" style="margin-left: 15px">
                        <?php echo _l('submit'); ?>
                        </button>
                    </div>
<!--                --><?php //} ?>
              </div>
            <?php echo form_close(); ?>
            </div>
        </div>

      </div>

        <!-- END PI -->        
  </div>
</div>
</div>
</div>
</div>
</div>
<?php init_tail(); ?>
<script>
    function chang_client(id)
    {
        jQuery.ajax({
            type: "post",
            url: "<?=admin_url().'opportunity/get_contact/'?>"+id,
            data: '',
            dataType: "json",
            cache: false,
            success: function (data) {
                $('#contact').html('<option></option>');
                $.each(data, function( index, value ) {
                    $('#contact').append('<option value="'+value.id+'">'+value.firstname+' '+value.lastname+'</option>');
                });
                $('#contact').selectpicker('refresh');

            }
        });
    }
    function get_data_contact(id)
    {
        jQuery.ajax({
            type: "post",
            url: "<?=admin_url().'opportunity/get_contact_id/'?>"+id,
            data: '',
            dataType: "json",
            cache: false,
            success: function (data) {
                $('#email').val(data.email);
                $('#phone').val(data.phonenumber);
                $('#email').val(data.email);

            }
        });
    }
    function select_step(id)
    {
        jQuery.ajax({
            type: "post",
            url: "<?=admin_url().'opportunity/get_select_step/'?>"+id,
            data: '',
            dataType: "json",
            cache: false,
            success: function (data) {
                console.log(data);
                $('#step').html('<option></option>');
                $.each(data, function( index, value ) {
                    $('#step').append('<option value="'+value.id+'">'+value.name+'</option>');
                });
                $('#step').selectpicker('refresh');

            }
        });
    }

</script>
</body>
</html>