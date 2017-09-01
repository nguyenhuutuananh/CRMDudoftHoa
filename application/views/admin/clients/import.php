<?php
$client_contacts_fields = $this->db->list_fields('tblcontacts');
$i = 0;
foreach($client_contacts_fields as $cf){
  if($cf == 'phonenumber'){
    $client_contacts_fields[$i] = 'contact_phonenumber';
  }
  $i++;
}
$client_db_fields = $this->db->list_fields('tblclients');
$custom_fields = get_custom_fields('customers');
if($this->input->post('download_sample') === 'true'){
  $_total_sample_fields = 0;
  header("Pragma: public");
  header("Expires: 0");
  header('Content-Type: application/csv');
  header("Content-Disposition: attachment; filename=\"sample_import_file.csv\";");
  header("Content-Transfer-Encoding: binary");
  foreach($client_contacts_fields as $field){
    if(in_array($field,$not_importable)){continue;}
    echo '"'.ucfirst($field).'",';
    $_total_sample_fields++;
  }
  foreach($client_db_fields as $field){
    if(in_array($field,$not_importable)){continue;}
    echo '"'.ucfirst($field).'",';
    $_total_sample_fields++;
  }
  foreach($custom_fields as $field){
    echo '"'.$field['name'].'",';
  }
  echo "\n";
  $sample_data = 'Sample Data';
  for($f = 0;$f<$_total_sample_fields;$f++){
   echo '"'.$sample_data.'",';
 }
 echo "\n";
 exit;
}
?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <a href="<?=base_url('uploads/importKH.xls')?>" class="btn btn-success">Download Sample</a>
            <hr />
            <?php $max_input = ini_get('max_input_vars');
            if(($max_input>0 && isset($total_rows_post) && $total_rows_post >= $max_input)){ ?>
            <div class="alert alert-warning">
              Your hosting provider has PHP setting <b>max_input_vars</b> at <?php echo $max_input;?>.<br/>
              Ask your hosting provider to increase the <b>max_input_vars</b> setting to <?php echo $total_rows_post;?> or higher or import less rows.
            </div>
            <?php } ?>
            <?php

            if(!isset($simulate) > 0) { ?>
            <p>
              Mọi dữ liệu yêu cầu nhập chính xác.
            </p>
            <p></p>
            <p class="text-danger">Nếu email đã tồn tại thì sẽ không được nhập.</p>
            
                <?php } ?>
                
                  <div class="row">
                    <div class="col-md-4 mtop15">
                      <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'import_form')) ;?>
                      <?php echo form_hidden('clients_import','true'); ?>
                      <?php echo render_input('file_csv','import_choose_file','','file'); ?>
                      <?php echo render_select('groups_in[]',$groups,array('id','name'),'customer_groups',($this->input->post('groups_in') ? $this->input->post('groups_in') : array()),array('multiple'=>true,'data-none-selected-text'=>_l('dropdown_non_selected_tex'))); ?>
                      <?php echo render_input('default_pass_all','default_pass_clients_import',$this->input->post('default_pass_all')); ?>
                      <div class="form-group">
                        <button type="button" class="btn btn-info import btn-import-submit"><?php echo _l('import'); ?></button>
                      </div>
                      <?php echo form_close(); ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php init_tail(); ?>
      <script src="<?php echo base_url('assets/plugins/jquery-validation/additional-methods.min.js'); ?>"></script>
      <script>
        _validate_form($('#import_form'),{file_csv:{required:true,extension: "xls, xlsx"},source:'required',status:'required'});
        $(function(){
         $('.btn-import-submit').on('click',function(){
           if($(this).hasClass('simulate')){
             $('#import_form').append(hidden_input('simulate',true));
           }
           $('#import_form').submit();
         });
       })
     </script>
   </body>
   </html>
