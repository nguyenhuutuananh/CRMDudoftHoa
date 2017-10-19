<?php init_head(); ?>
<style type="text/css">
</style>
<div id="wrapper">
 <div class="content">
   <div class="row">
  <div class="col-md-12">
   <div class="panel_s">
     <div class="panel-body">

        <?php if (isset($item)) { ?>
        <?php echo form_hidden('isedit'); ?>
        <?php echo form_hidden('itemid', $item->id); ?>
      <div class="clearfix"></div>
        <?php 
        } ?>
  <h4 class="bold no-margin"><?php echo (isset($item) ? _l('edit_export_order') : _l('add_export_order')); ?></h4>
  <hr class="no-mbot no-border" />
  <div class="row">
    <div class="additional"></div>
    <div class="col-md-12">
        <?php
         if(isset($item))
            {
                if($item->status==0)
                {
                    $type='warning';
                    $status='Chưa duyệt';
                }
                elseif($item->status==1)
                {
                    $type='info';
                    $status='Đã xác nhận';
                }
                else
                {
                    $type='success';
                    $status='Đã duyệt';
                }
            }
            else
            {
                $type='warning';
                $status='Phiếu mới';
            }

        ?>
        <div class="ribbon <?=$type?>"><span><?=$status?></span></div>
        <ul class="nav nav-tabs profile-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#item_detail" aria-controls="item_detail" role="tab" data-toggle="tab">
                    <?php echo _l('export_detail'); ?>
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="item_detail">
            <div class="row">
                
                <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                    
                </div>
                
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 _buttons">
                    <div class="pull-right">
                        <?php if( isset($item) ) { ?>
                        <a href="<?php echo admin_url('exports/pdf/' . $item->id . '?print=true') ?>" target="_blank" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="In" aria-describedby="tooltip652034"><i class="fa fa-print"></i></a>
                        <a href="<?php echo admin_url('exports/pdf/' . $item->id  ) ?>" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Xem PDF"><i class="fa fa-file-pdf-o"></i></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            
            <?php echo form_open_multipart($this->uri->uri_string(), array('class' => 'sales-form', 'autocomplete' => 'off')); ?>
                <div class="row">
                  <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">            
                    <?php
                    $attrs_not_select = array('data-none-selected-text' => _l('system_default_string'));
                    ?>
                    <div class="form-group">
                         <label for="number"><?php echo _l('export_code'); ?></label>
                         <div class="input-group">
                          <span class="input-group-addon">
                          <?php $prefix =($item) ? $item->prefix : get_option('prefix_export'); ?>
                            <?=$prefix?>
                            <?php echo form_hidden('rel_type', (($item->rel_type)? $item->rel_type :'export_warehouse_transfer')); ?>
                            <?=form_hidden('prefix',$prefix)?>    
                            </span>
                            <?php 
                                if($item)
                                {
                                    $number=$item->code;
                                }
                                else
                                {
                                    $number=sprintf('%06d',getMaxID('id','tblexports')+1);
                                }
                            ?>
                            <input type="text" name="code" class="form-control" id="code" value="<?=$number ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $data_original_number; ?>" readonly>
                          </div>
                    </div>

                    <?php
                    $value= ( isset($item) ? $item->rel_code : ''); 
                    $attrs = array('readonly'=>true);
                    if(!empty('rel_id') || !empty('rel_code'))
                    {
                        $frmattrs['style']="display: none;";
                    }
                    ?>
                    <?php echo render_input( 'rel_code', _l("sale_code"),$value,'text',$attrs,$frmattrs); ?>

                    <?php $value = (isset($item) ? _d($item->date) : _d(date('Y-m-d')));?>
                    <?php echo render_date_input('date','view_date',$value); ?>
                    
                    <?php
                    $default_name = (isset($item) ? $item->name : _l('export_name'));
                    echo form_hidden('name', _l('export_name'), $default_name);
                    ?>

                    <?php
                    $selected=(isset($item) ? $item->customer_id : '');
                    if($item->rel_id)
                    {
                        $arr=array('disabled'=>true);
                        echo form_hidden('customer_id',$selected);
                    }
                    echo render_select('customer_id',$customers,array('userid','company'),'client',$selected,$arr);
                    ?>

                    <?php
                    $selected=(isset($item) ? $item->receiver_id : '');
                    echo render_select('receiver_id',$receivers,array('staffid','fullname'),'staffs',$selected); 
                    ?>

                    <!-- <?php
                    $selected=(isset($item) ? $warehouse_id : '');
                    echo render_select('warehouse_id',$warehouses,array('warehouseid','warehouse'),'warehouse_name',$selected); 
                    ?> -->

                    <?php 
                    $reason = (isset($item) ? $item->reason : "");
                    echo render_textarea('reason', 'note', $reason, array(), array(), '', 'tinymce');
                    ?>
                </div>

                
                
                
                <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                    <!-- Cusstomize from invoice -->
                    <div class="panel-body mtop10">
                    <?php if(!empty($item->rel_id) || !empty($item->rel_code)){ $display='style="display: none;"';  }?>
                        <div class="row"  >
                        <div class="col-md-4" style="display: none;">
                            <?php 
                                if($item->rel_id)
                                    {
                                        $arr=array('disabled'=>true);
                                        echo form_hidden('warehouse_type',$warehouse_type_id);
                                        echo form_hidden('warehouse_name',$warehouse_id);
                                    }
                            ?>
                        </div>
                            <div class="col-md-4">
                                <?php 
                                    echo render_select('warehouse_name', $warehouses, array('warehouseid', 'warehouse'),'warehouse_name',$warehouse_id,$arr);
                                ?>
                            </div>
                            <div class="col-md-4" <?=$display?>>
                                <div class="form-group mbot25">
                                    <label for="custom_item_select" class="control-label"><?=_l('item_name')?></label>
                                    <select class="selectpicker no-margin" data-width="100%" id="custom_item_select" data-none-selected-text="<?php echo _l('add_item'); ?>" data-live-search="true">
                                        <option value=""></option>

                                        <?php foreach ($items as $product) { ?>
                                        <option value="<?php echo $product['id']; ?>" data-subtext="">(<?php echo $product['code']; ?>) <?php echo $product['name']; ?></option>
                                        <?php 
                                        } ?>

                                    <!-- <?php if (has_permission('items', '', 'create')) { ?>
                                    <option data-divider="true"></option>
                                    <option value="newitem" data-content="<span class='text-info'><?php echo _l('new_invoice_item'); ?></span>"></option>
                                    <?php } ?> -->
                                    </select>
                                </div>
                            </div>
                        
                            <div class="col-md-5 text-right show_quantity_as_wrapper">
                                
                            </div>
                        </div>
                        <div class="table-responsive s_table" style="overflow-x: auto;overflow-y: hidden;">
                            <table class="table items item-export no-mtop">
                                <thead>
                                    <tr>
                                        <th><input type="hidden" id="itemID" value="" /></th>
                                        <th style="min-width: 200px" class="text-left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_name'); ?>"></i> <?php echo _l('item_name'); ?></th>
                                        <th width="" class="text-left"><?php echo _l('item_unit'); ?></th>
                                        <th width="" class="text-left"><?php echo _l('item_quantity'); ?></th>
                                        
                                        <th width="" class="text-left"><?php echo _l('item_price'); ?></th>
                                        <th width="" class="text-left"><?php echo _l('amount'); ?></th>
                                        <th width="" class="text-left"><?php echo _l('tax'); ?></th>
                                        <th style="min-width: 80px" class="text-left"><?php echo _l('discount').'(%)'; ?></th>
                                        <th style="min-width: 100px" class="text-left"><?php echo _l('discount_money'); ?></th>
                                        <th width="" class="text-left"><?php echo _l('sub_amount'); ?></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    <tr class="main" <?=$display?> >
                                        <td><input type="hidden" id="itemID" value="" /></td>
                                        <td>
                                            <?php echo _l('item_name'); ?>
                                        </td>
                                        <td>
                                            <input type="hidden" id="item_unit" value="" />
                                            <?php echo _l('item_unit'); ?>
                                        </td>

                                        <td>
                                            <input style="width: 100px" class="mainQuantity" type="number" min="1" value="1"  class="form-control" placeholder="<?php echo _l('item_quantity'); ?>">
                                        </td>
                                        
                                        <td>
                                            <?php echo _l('item_price'); ?>
                                        </td>
                                        
                                        <td>
                                            0
                                        </td>
                                        <td>
                                            <?php echo _l('tax'); ?>
                                            <input type="hidden" id="tax" data-taxid="" data-taxrate="" value="" />
                                        </td>
                                        <td>
                                            <input style="width: 100px" class="discount_percent" type="number" min="0" value="0" placeholder="" aria-invalid="false">
                                        </td>
                                        <td>
                                            <input style="width: 100px" class="discount" type="number" min="0" value="0" placeholder="" aria-invalid="false">
                                        </td>
                                        <td>
                                            0
                                        </td>
                                        <td>
                                            <button style="display:none" id="btnAdd" type="button" onclick="createTrItem(); return false;" class="btn pull-right btn-info"><i class="fa fa-check"></i></button>
                                        </td>
                                    </tr>
                                    <?php
                                    $i=0;
                                    $totalPrice=0;
                                    if(isset($item) && count($item->items) > 0) {
                                        
                                        foreach($item->items as $value) {
                                        ?>
                                    <tr class="sortable item">
                                        <td>
                                            <input type="hidden" name="items[<?php echo $i; ?>][id]" value="<?php echo $value->product_id; ?>">
                                        </td>
                                        <td class="dragger"><?php echo $value->product_name; ?></td>
                                        <td><?php echo $value->unit_name; ?></td>
                                        <?php
                                        $err='';
                                            if($value->quantity>$value->warehouse_type->product_quantity)
                                            {
                                                $err='error';
                                                $style='border: 1px solid red !important';
                                            }
                                            $data_store=$value->warehouse_type->product_quantity;
                                        ?>
                                        <td>
                                            <?php 
                                            $maxQ=getMaxQuanitySOExport($id,$value->product_id)+$value->quantity;
                                            $strminmax='min="1" max="'.$maxQ.'"';
                                            if($maxQ==0)
                                            {
                                                $strminmax='readonly';
                                            }
                                            ?>
                                        <input style="width: 100px;" <?=$strminmax?>  class="mainQuantity" type="number" name="items[<?php echo $i; ?>][quantity]" value="<?php echo $value->quantity; ?>">
                                        </td>
                                            
                                        <td><?php echo number_format($value->unit_cost); ?></td>
                                        <td><?php echo number_format($value->sub_total); ?></td>
                                        <td><?php echo number_format($value->tax) ?>
                                            <input type="hidden" id="tax" data-taxrate="<?=$value->tax_rate?>" value="<?=$value->tax_id?>">
                                        </td>
                                        <td>
                                            <input style="width: 100px;" name="items[<?php echo $i; ?>][discount_percent]" min="0" class="discount_percent" type="number" value="<?=$value->discount_percent?>">
                                        </td>
                                        <td>
                                            <input style="width: 100px;" name="items[<?php echo $i; ?>][discount]" min="0" class="discount" type="number" value="<?=$value->discount?>">
                                        </td>
                                        <td><?php echo number_format($value->amount) ?></td>
                                        <td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td>
                                    </tr>
                                        <?php
                                            $totalPrice += $value->amount;
                                            $i++;
                                        }
                                    }
                                    $discount=$item->discount;
                                    $adjustment=$item->adjustment;
                                    $transport_fee=$item->transport_fee;
                                    $installation_fee=$item->installation_fee;
                                    $delivery_fee=$item->delivery_fee;
                                    $grand_total=$item->total;
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-8 col-md-offset-4">
                            <table class="table text-right">
                                <tbody>
                                    <tr>
                                        <td><span class="bold"><?php echo _l('purchase_total_items'); ?> :</span>
                                        </td>
                                        <td colspan="2" class="total">
                                            <?php echo $i ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="bold"><?php echo _l('discount_percent_total'); ?> :</span>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                              <input type="number" name="discount_percent" id="discount_percent" min="0" class="form-control" placeholder="Phần trăm chiết khấu" aria-describedby="basic-addon2" value="<?=$item->discount_percent?$item->discount_percent:0?>">
                                              <span class="input-group-addon" id="basic-addon2">%</span>
                                            </div>
                                        </td>
                                        <td class="discount_total">
                                            <input type="number" min="0" name="discount" id="discount" class="form-control" placeholder="Giá trị chiết khấu" aria-describedby="basic-addon2" value="<?=($discount?$discount:0)?>">
                                            <!-- <?=format_money($discount?$discount:0)?> -->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="bold"><?php echo _l('transport_fee'); ?> :</span>
                                        </td>
                                        <td>
                                            <div class="form-group no-mbot">
                                              <input type="number" min="0" name="transport_fee" id="transport_fee" class="form-control" placeholder="Giá trị điều chỉnh" aria-describedby="basic-addon2" value="<?=($transport_fee?$transport_fee:0)?>">
                                            </div>
                                        </td>
                                        <td class="transport_fee">
                                            <?=format_money($transport_fee?$transport_fee:0)?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="bold"><?php echo _l('installation_fee'); ?> :</span>
                                        </td>
                                        <td>
                                            <div class="form-group no-mbot">
                                              <input type="number" min="0" name="installation_fee" id="installation_fee" class="form-control" placeholder="Giá trị điều chỉnh" aria-describedby="basic-addon2" value="<?=($installation_fee?$installation_fee:0)?>">
                                            </div>
                                        </td>
                                        <td class="installation_fee">
                                            <?=format_money($installation_fee?$installation_fee:0)?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="bold"><?php echo _l('delivery_fee'); ?> :</span>
                                        </td>
                                        <td>
                                            <div class="form-group no-mbot">
                                              <input type="number" min="0" name="delivery_fee" id="delivery_fee" class="form-control" placeholder="Giá trị điều chỉnh" aria-describedby="basic-addon2" value="<?=($delivery_fee?$delivery_fee:0)?>">
                                            </div>
                                        </td>
                                        <td class="delivery_fee">
                                            <?=format_money($delivery_fee?$delivery_fee:0)?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="bold"><?php echo _l('purchase_total_price'); ?> :</span>
                                        </td>
                                        <td colspan="2" class="totalPrice">
                                            <?php echo number_format($grand_total) ?> <!-- <?=($currency->symbol)?$currency->symbol:_l('VNĐ')?> -->
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- End Customize from invoice -->
                </div>
                
                <?php if(isset($item) && $item->status != 2 || !isset($item)) { ?>
                  <button class="btn btn-info mtop20 only-save customer-form-submiter" style="margin-left: 15px">
                    <?php echo _l('submit'); ?>
                </button>
                <?php } ?>
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

    _validate_form($('.sales-form'),{code:'required',date:'required',customer_id:'required',receiver_id:'required'});
    
    var itemList = <?php echo json_encode($items);?>;

    $('#warehouse_name').change(function(e){
        $('table tr.sortable.item').remove();
        total=0;
        var warehouse_id=$(this).val();
        loadProductsInWarehouse(warehouse_id)
        refreshAll();
        refreshTotal();
    });

    function loadProductsInWarehouse(warehouse_id){
        var product_id=$('#custom_item_select');
        product_id.find('option:gt(0)').remove();
        product_id.selectpicker('refresh');
        if(product_id.length) {
            $.ajax({
                url : admin_url + 'warehouses/getProductsInWH/' + warehouse_id,
                dataType : 'json',
            })
            .done(function(data){         
                $.each(data, function(key,value){

                    product_id.append('<option data-store="'+value.product_quantity+'" value="' + value.product_id + '">'+'('+ value.code +') '  + value.name + '</option>');
                });
                product_id.selectpicker('refresh');
            });
        }
    }


    $('#warehouse_type').change(function(e){
        var warehouse_type = $(e.currentTarget).val();
        if(warehouse_type != '') {
            getWarehouses(warehouse_type); 
        }
    });
    function getWarehouses(warehouse_type){
        var warehouse_id=$('#warehouse_name');
        warehouse_id.find('option:gt(0)').remove();
        warehouse_id.selectpicker('refresh');
        if(warehouse_id.length) {
            $.ajax({
                url : admin_url + 'warehouses/getWarehouses/' + warehouse_type ,
                dataType : 'json',
            })
            .done(function(data){  
                
                $.each(data, function(key,value){
                    warehouse_id.append('<option value="' + value.warehouseid +'">' + value.warehouse + '</option>');
                });

                warehouse_id.selectpicker('refresh');
            });
        }
    }

    //format currency
    function formatNumber(nStr, decSeperate=".", groupSeperate=",") {
        nStr += '';
        x = nStr.split(decSeperate);
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
        }
        return x1 + x2;
    }

    var findItem = (id) => {
        var itemResult;
        $.each(itemList, (index,value) => {
            if(value.id == id) {
                itemResult = value;
                return false;
            }
        });
        return itemResult;
    };
    var total = <?php echo $i ?>;
    var totalPrice = <?php echo $totalPrice ?>;
    var uniqueArray = <?php echo $i ?>;
    var isNew = false;
    var createTrItem = () => {
        if(!isNew) return;
        if(!$('div #warehouse_name option:selected').length || $('div #warehouse_name option:selected').val() == '') {
            alert_float('danger', "Vui lòng chọn kho chứa sản phẩm!");
            return;
        }
        if( $('table.item-export tbody tr:gt(0)').find('input[value=' + $('tr.main').find('td:nth-child(1) > input').val() + ']').length ) {
            $('table.item-export tbody tr:gt(0)').find('input[value=' + $('tr.main').find('td:nth-child(1) > input').val() + ']').parent().find('td:nth-child(2) > input').focus();
            alert_float('danger', "Sản phẩm này đã được thêm, vui lòng lòng kiểm tra lại!");
            return;
        }
        if(parseFloat($('tr.main').find('input.mainQuantity').val())>parseFloat($('tr.main').find('input.mainQuantity').attr('data-store')))
        {
            alert_float('danger','Sản phẩm bạn nhập là ['+$('tr.main').find('input.mainQuantity').val()+'] lớn hơn số lượng trong kho ['+$('tr.main').find('input.mainQuantity').attr('data-store')+'], vui lòng lòng kiểm tra lại!');
            return;
        }
        var newTr = $('<tr class="sortable item"></tr>');
        
        var td1 = $('<td><input type="hidden" name="items[' + uniqueArray + '][id]" value="" /></td>');
        var td2 = $('<td class="dragger"></td>');
        var td3 = $('<td></td>');
        var td4 = $('<td><input style="width: 100px" class="mainQuantity" type="number" name="items[' + uniqueArray + '][quantity]" value="" /></td>');        
        var td5 = $('<td></td>');
        var td6 = $('<td></td>');
        var td7 = $('<td></td>');
        var td8 = $('<td><input style="width: 100px" class="discount_percent" type="number" name="items[' + uniqueArray + '][discount_percent]" value="" /></td>');
        var td9 = $('<td><input style="width: 100px" class="discount" type="number" name="items[' + uniqueArray + '][discount]" value="" /></td>');
        var td10 = $('<td></td>');

        td1.find('input').val($('tr.main').find('td:nth-child(1) > input').val());
        td2.text($('tr.main').find('td:nth-child(2)').text());
        td3.text($('tr.main').find('td:nth-child(3)').text());
        td4.find('input').val($('tr.main').find('td:nth-child(4) > input').val());
        td4.find('input').attr('data-store',$('tr.main').find('td:nth-child(4) > input').attr('data-store'));
        td5.text( $('tr.main').find('td:nth-child(5)').text());
        td6.text( $('tr.main').find('td:nth-child(6)').text());
        var inputTax=$('tr.main').find('td:nth-child(7) > input');
        td7.text( $('tr.main').find('td:nth-child(7)').text());
        td7.append(inputTax);
        td8.find('input').val($('tr.main').find('td:nth-child(8) > input').val());
        td9.find('input').val($('tr.main').find('td:nth-child(9) > input').val());
        td10.text($('tr.main').find('td:nth-child(10)').text());
        newTr.append(td1);
        newTr.append(td2);
        newTr.append(td3);
        newTr.append(td4);
        newTr.append(td5);
        newTr.append(td6);
        newTr.append(td7);
        newTr.append(td8);
        newTr.append(td9);
        newTr.append(td10);

        newTr.append('<td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td');
        $('table.item-export tbody').append(newTr);
        total++;
        uniqueArray++;
        refreshTotal();
        // refreshAll();
    };
    var refreshAll = () => {
        isNew = false;
        $('#btnAdd').hide();
        $('#custom_item_select').val('');
        $('#custom_item_select').selectpicker('refresh');
        var trBar = $('tr.main');
        
        trBar.find('td:first > input').val("");
        trBar.find('td:nth-child(2) > input').val('');
        trBar.find('td:nth-child(3) > input').val(1);
        trBar.find('td:nth-child(4) > input').val('');
        trBar.find('td:nth-child(5) > textarea').text('');


    };
    var deleteTrItem = (trItem) => {
        var current = $(trItem).parent().parent();
        totalPrice -= current.find('td:nth-child(4) > input').val() * current.find('td:nth-child(5)').text().replace(/\,/g, '');
        $(trItem).parent().parent().remove();
        total--;
        refreshTotal();
    };
    var refreshTotal = () => {
         $('.total').text(formatNumber(total));
        var items = $('table.item-export tbody tr:gt(0)');
        totalPrice = 0;
        $.each(items, (index,value)=>{
            totalPrice += parseFloat($(value).find('td:nth-child(10)').text().replace(/\,/g, ''));
        });
        var discount_percent=$('#discount_percent').val();

        var discount=discount_percent*totalPrice/100;
        
        var transport_fee=parseFloat($('#transport_fee').val());
        if(isNaN(transport_fee)) transport_fee=0;

        var installation_fee=parseFloat($('#installation_fee').val());
        if(isNaN(installation_fee)) installation_fee=0;

        var delivery_fee=parseFloat($('#delivery_fee').val());
        if(isNaN(delivery_fee)) delivery_fee=0;

        var grand_total=totalPrice-discount+transport_fee+installation_fee+delivery_fee;
        
        $('#discount').val(discount);
        $('.totalPrice').text(formatNumber(grand_total));
    };
    $('#custom_item_select').change((e)=>{
        var id = $(e.currentTarget).val();
        var itemFound = findItem(id);

        $('#select_kindof_warehouse').val('');
        $('#select_kindof_warehouse').selectpicker('refresh');
        var warehouse_id=$('#select_warehouse');
        warehouse_id.find('option:gt(0)').remove();
        warehouse_id.selectpicker('refresh');

        if(typeof(itemFound) != 'undefined') {
            var trBar = $('tr.main');
            
            trBar.find('td:first > input').val(itemFound.id);
            trBar.find('td:nth-child(2)').text(itemFound.name+' ('+itemFound.prefix+itemFound.code+')');
            trBar.find('td:nth-child(3)').text(itemFound.unit_name);
            trBar.find('td:nth-child(3) > input').val(itemFound.unit);
            trBar.find('td:nth-child(4) > input').val(1);
            
            trBar.find('td:nth-child(5)').text(formatNumber(itemFound.price));
            trBar.find('td:nth-child(6)').text(formatNumber(itemFound.price * 1) );
            var taxValue = parseFloat(itemFound.price)-(parseFloat(itemFound.price)/(parseFloat(itemFound.tax_rate)*0.01+1));
            var inputTax = $('<input type="hidden" id="tax" data-taxrate="'+itemFound.tax_rate+'" value="'+itemFound.tax+'" />');
            trBar.find('td:nth-child(7)').text(formatNumberDec(taxValue));
            trBar.find('td:nth-child(7)').append(inputTax);
            trBar.find('td:nth-child(8) > input').val(0);
            trBar.find('td:nth-child(9) > input').val(0);
            trBar.find('td:nth-child(10)').text(formatNumber(parseFloat(itemFound.price)));
            isNew = true;
            $('#btnAdd').show();
        }
        else {
            isNew = false;
            $('#btnAdd').hide();
        }
    });
    $('select[id^="select_warehouse"]').on('change', (e)=>{
        if($(e.currentTarget).val() != '') {
            $(e.currentTarget).parents('tr').find('input.mainQuantity').attr('data-store', $(e.currentTarget).find('option:selected').data('store'));
        }
    });
    $(document).on('keyup', '.mainQuantity', (e)=>{
        var currentQuantityInput = $(e.currentTarget);
        let elementToCompare;
        if(typeof(currentQuantityInput.attr('data-store')) == 'undefined' )
            elementToCompare = currentQuantityInput.parents('tr').find('input[aria-label!="Search"]:last');
        else
            elementToCompare = currentQuantityInput;
        if(parseInt(currentQuantityInput.val()) > parseInt(elementToCompare.attr('data-store'))) {
            currentQuantityInput.attr("style", "width: 100px;border: 1px solid red !important");
            currentQuantityInput.attr('data-toggle', 'tooltip');
            currentQuantityInput.attr('data-trigger', 'manual');
            currentQuantityInput.attr('title', 'Số lượng vượt mức cho phép!');
            // $('[data-toggle="tooltip"]').tooltip();
            currentQuantityInput.off('focus', '**').off('hover', '**');
            currentQuantityInput.tooltip('fixTitle').focus(()=>$(this).tooltip('show')).hover(()=>$(this).tooltip('show'));
            // error flag
            currentQuantityInput.addClass('error');
            currentQuantityInput.focus();
        }
        else {
            currentQuantityInput.attr('title', 'OK!').tooltip('fixTitle').tooltip('show');
            currentQuantityInput.attr("style", "width: 100px;");
            // remove flag
            currentQuantityInput.removeClass('error');
            currentQuantityInput.focus();
        }
        
        // var Gia = currentQuantityInput.parent().find(' + td');
        // var GiaTri = Gia.find(' + td');
        // var Thue = GiaTri.find(' + td');
        // var Tong = Thue.find(' + td');
        // var inputTax=Thue.find('input');        
        // GiaTri.text(formatNumber(Gia.text().replace(/\,/g, '') * currentQuantityInput.val()) );
        // Thue.text(formatNumber(parseFloat(inputTax.data('taxrate'))/100*parseFloat(GiaTri.text().replace(/\,/g,''))));
        // Thue.append(inputTax);
        // Tong.text(formatNumber(parseFloat(Thue.text().replace(/\,/g,''))+parseFloat(GiaTri.text().replace(/\,/g,''))));
        calculateTotal(e.currentTarget);
        refreshTotal();
    });
    var calculateTotal = (currentInput) => {
        currentInput = $(currentInput);   
        let quantity = currentInput.parents('tr').find('.mainQuantity');
        let quantityTd = quantity.parent();

        let priceTd = quantityTd.find('+ td');

        let amountTd = priceTd.find('+ td');
        var amount=priceTd.text().replace(/\,/g, '') * quantity.val();
        amountTd.text(formatNumber(amount));

        let taxTd=amountTd.find('+ td');
        var inputTax=taxTd.find('input')
        var tax = parseFloat(amount)-(parseFloat(amount)/(parseFloat(inputTax.data('taxrate'))*0.01+1));
        taxTd.text(formatNumber(tax));
        taxTd.append(inputTax);

        let discountPercent=currentInput.parents('tr').find('.discount_percent');

        let discount=currentInput.parents('tr').find('.discount');
        var discountTd=discount.parent();
        var discountValue=amount*discountPercent.val()/100;
        discount.val(discountValue);

        let subTotalTd=discountTd.find('+ td');
        subTotalTd.text(formatNumber(amount-discountValue));

        refreshTotal();
    };
    $(document).on('keyup', '.discount', (e)=>{
        var currentDiscountInput = $(e.currentTarget);
        var discount_percent=currentDiscountInput.parents('td').prev().find('input');
        var tong=currentDiscountInput.parents('tr').find('.mainQuantity').parents().find('+ td + td').text().trim().replace(/\,|%/g, '');
        discount_percent.val(currentDiscountInput.val()*100/tong);
        calculateTotal(e.currentTarget);
    });
    $(document).on('keyup', '.discount_percent', (e)=>{
        var currentDiscountPercentInput = $(e.currentTarget);
        calculateTotal(e.currentTarget);
    });
    $(document).on('change', '#adjustment', (e)=>{
        var currentInput = $(e.currentTarget);
        var adjustment=parseFloat(currentInput.val());
        if(isNaN(adjustment)) adjustment=0;
        $('.adjustment_total').text(formatNumber(adjustment));
        calculateTotal(e.currentTarget);
    });
    $(document).on('change', '#transport_fee', (e)=>{
        var currentInput = $(e.currentTarget);
        var transport_fee=parseFloat(currentInput.val());
        if(isNaN(transport_fee)) transport_fee=0;
        $('.transport_fee').text(formatNumber(transport_fee));
        calculateTotal(e.currentTarget);
    });
    $(document).on('change', '#installation_fee', (e)=>{
        var currentInput = $(e.currentTarget);
        var installation_fee=parseFloat(currentInput.val());
        if(isNaN(installation_fee)) installation_fee=0;
        $('.installation_fee').text(formatNumber(installation_fee));
        calculateTotal(e.currentTarget);
    });
    $(document).on('change', '#delivery_fee', (e)=>{
        var currentInput = $(e.currentTarget);
        var delivery_fee=parseFloat(currentInput.val());
        if(isNaN(delivery_fee)) delivery_fee=0;
        $('.delivery_fee').text(formatNumber(delivery_fee));
        calculateTotal(e.currentTarget);
    });
    $(document).on('change', '#discount_percent', (e)=>{
        var currentInput = $(e.currentTarget);
        calculateTotal(e.currentTarget);
    });
    $(document).on('change', '#discount', (e)=>{
        var currentDiscountInput = $(e.currentTarget);
        var discount_percent=$('#discount_percent');
        var transport_fee=$('#transport_fee');
        var installation_fee=$('#installation_fee');
        var grand_total=parseFloat($('.totalPrice').text().replace(/\,/g, ''))-transport_fee.val()-installation_fee.val();
        discount_percent.val(roundNumber(currentDiscountInput.val()*100/(grand_total),3))
        var pc=currentDiscountInput.val()*100/grand_total;
        calculateTotal(e.currentTarget);
    });
    $('#select_kindof_warehouse').change(function(e){
        

        var warehouse_type = $(e.currentTarget).val();
        var product = $(e.currentTarget).parents('tr').find('td:first input');
        if(warehouse_type != '' && product.val() != '') {
            loadWarehouses(warehouse_type,product.val()); 
        }
    });
    function loadWarehouses(warehouse_type, filter_by_product,default_value=''){
        alert(warehouse_type);
        var warehouse_id=$('#select_warehouse');
        warehouse_id.find('option:gt(0)').remove();
        warehouse_id.selectpicker('refresh');
        if(warehouse_id.length) {
            $.ajax({
                url : admin_url + 'warehouses/getWarehouses/' + warehouse_type + '/' + filter_by_product,
                dataType : 'json',
            })
            .done(function(data){          
                $.each(data, function(key,value){
                    var stringSelected = "";
                    if(value.warehouseid == default_value) {
                        stringSelected = ' selected="selected"';
                    }
                    warehouse_id.append('<option data-store="'+value.items[0].product_quantity+'" value="' + value.warehouseid + '"'+stringSelected+'>' + value.warehouse + '(có '+value.items[0].product_quantity+')</option>');
                });
                warehouse_id.selectpicker('refresh');
            });
        }
    }

    function loadWarehouseByID(warehouse_type, filter_by_product,id){
        var warehouse_id=$('#select_warehouse'+id);
        warehouse_id.find('option:gt(0)').remove();
        warehouse_id.selectpicker('refresh');
        if(warehouse_id.length) {
            $.ajax({
                url : admin_url + 'warehouses/getWarehouses/' + warehouse_type + '/' + filter_by_product,
                dataType : 'json',
            }).done(function(data){          
                $.each(data, function(key,value){
                    var stringSelected = "";
                    warehouse_id.append('<option data-store="'+value.items[0].product_quantity+'" value="' + value.warehouseid + '"'+stringSelected+'>' + value.warehouse + '(có '+value.items[0].product_quantity+')</option>');
                });
                warehouse_id.selectpicker('refresh');
            });
        }
    }

    $('.customer-form-submiter').on('click', (e)=>{
        if($('input.error').length) {
            e.preventDefault();
            alert_float('warning','Giá trị không hợp lệ!');    
        }
    });

    $(document).on('change', '#warehouse_name,#custom_item_select',function(e){
        var warehouse_id=$('#warehouse_name').val();
        var product_id=$('#custom_item_select').val();
        if(warehouse_id.length && product_id.length) {
            $.ajax({
                url : admin_url + 'warehouses/getProductQuantity/' + warehouse_id + '/' + product_id,
                dataType : 'json',
            })
            .done(function(data){
            var quantityMax=data.product_quantity;
            if(isNaN(parseFloat(quantityMax))) quantityMax=0;          
               $('#warehouse_name option:selected').attr('data-store',quantityMax);
               $('tr.main').find('input.mainQuantity').attr('data-store',quantityMax);
            });
        }
    });

    $('#customer_id').change(function(e){
        var customer_id=$(this).val();
        var data={};
        data.customer_id=customer_id;
        var url=admin_url + 'clients/getClientByID';
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            var discount_percent=response.success.discount_percent;
            if(discount_percent==null) discount_percent=0;
            $('#discount_percent').val(discount_percent);
            refreshTotal();
        });
    });
    
</script>
</body>
</html>
