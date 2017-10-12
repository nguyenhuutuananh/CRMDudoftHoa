<?php init_head(); ?>
<div id="wrapper">
 <div class="content">
   <div class="row">
  <div class="col-md-12">
   <div class="panel_s">
     <div class="panel-body">
     <?php if($convert==false) { ?>
        <div class="col-md-12">
            <div class="alert alert-info">
                <a href="#" onclick="init_lead(1); return false;"><?=_l('converted_contract')?></a>
            </div>
        </div>
        <?php } ?>
        <?php if (isset($item)) { ?>
        <?php echo form_hidden('isedit'); ?>
        <?php echo form_hidden('itemid', $item->id); ?>
        <?php $display='style="display: none;"';?>
      <div class="clearfix"></div>
        <?php 
        } ?>
  <h4 class="bold no-margin"><?php echo _l('add_sale_order'); ?></h4>
  <hr class="no-mbot no-border" />
  <div class="row">
    <div class="additional"></div>
    <div class="col-md-12">
        <?php
            {
                $type='warning';
                $status='Phiếu mới';
            }

        ?>
        <div class="ribbon <?=$type?>"><span><?=$status?></span></div>
        <ul class="nav nav-tabs profile-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#item_detail" aria-controls="item_detail" role="tab" data-toggle="tab">
                    <?php echo _l('sale_detail'); ?>
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="item_detail">
            <div class="row">
                
                <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                    
                </div>
                
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 _buttons" <?=$display?>>
                    <div class="pull-right">
                        <?php if( isset($item) ) { ?>
                        <a href="<?php echo admin_url('sales/pdf/' . $item->id . '?print=true') ?>" target="_blank" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="In" aria-describedby="tooltip652034"><i class="fa fa-print"></i></a>
                        <a href="<?php echo admin_url('sales/pdf/' . $item->id  ) ?>" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Xem PDF"><i class="fa fa-file-pdf-o"></i></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            
            <?php echo form_open_multipart(admin_url('sale_orders/sale_detail'), array('class' => 'sales-form', 'autocomplete' => 'off')); ?>
                <div class="row">
                  <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">            
                    <?php
                    $attrs_not_select = array('data-none-selected-text' => _l('system_default_string'));
                    ?>
                    <div class="form-group">
                         <label for="number"><?php echo _l('sale_code'); ?></label>
                         <div class="input-group">
                          <span class="input-group-addon">
                          <?php $prefix =($item) ? get_option('prefix_sale_order') : ''; ?>
                            <?=$prefix?>
                            <?php echo form_hidden('rel_type', 'contract'); ?>
                            <?=form_hidden('prefix',$prefix)?>    
                            </span>
                            <?php 
                                $number=sprintf('%06d',getMaxID('id','tblsale_orders')+1);
                            ?>
                            <input type="text" name="code" class="form-control" id="code" value="<?=$number ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $data_original_number; ?>" readonly>
                          </div>
                    </div>

                    <?php $value = (isset($item) ? _d($item->datestart) : _d(date('Y-m-d')));?>
                    <?php echo render_date_input('date','view_date',$value); ?>

                    <?php $value = (isset($item) ?_d(date('Y-m-d')) : '' );?>
                    <?php echo render_date_input('date_ht','date_of_accounting',$value); ?>
                    
                    <?=form_hidden('rel_id',$item->id);?>

                    <?=render_input('rel_code','contract_code',$item->prefix.$item->code,'text',array('readonly'=>true))?>
                    <?php
                    $default_name = (isset($item) ? $item->name : _l('sale_name'));
                    echo form_hidden('name', _l('sale_name'), $default_name);
                    ?>


                    <?php

                    $selected=(isset($item) ? $item->client : '');
                    echo render_select('customer_id',$customers,array('userid','company'),'client',$selected,array('disabled'=>true)); 
                    echo form_hidden('customer_id',$selected);
                    ?>


                    <?php 
                    $reason = (isset($item) ? $item->reason : "");
                    echo render_textarea('reason', 'note', $reason, array(), array(), '', 'tinymce');

                    ?>
                </div>

                
                
                
                <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                    <!-- Cusstomize from invoice -->
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?=_l('list_products')?></div>
                        <div class="panel-body">
                            <!-- <div class="panel-body mtop10"> -->
                            
                        <div class="row">
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#money_goods" onclick="" aria-controls="money_goods" role="tab" data-toggle="tab">
                                        <i class="fa fa-money menu-icon text-info"></i><?php echo _l('money_goods'); ?>
                                    </a>
                                </li>
                                <li role="presentation" >
                                    <a href="#capital_expenditures" onclick="" aria-controls="capital_expenditures" role="tab" data-toggle="tab">
                                        <i class="fa fa-credit-card-alt menu-icon text-info"></i><?php echo _l('capital_expenditures'); ?>
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="money_goods">
                                    <div class="row">
                                    <div class="col-md-4">
                                        <?php 
                                            echo render_select('warehouse_name', $warehouses, array('warehouseid', 'warehouse'),'warehouse_name',$warehouse_id,array('disabled'=>true));

                                            echo form_hidden('warehouse_name',$warehouse_id);
                                        ?>
                                    </div>
                                    <div class="col-md-4" <?=$display?> >
                                        <div class="form-group mbot25">
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
                                    <div class="col-md-5 text-right show_quantity_as_wrapper"></div>
                                    </div>
                                    <!-- <div class="row"> -->
                                    <div class="table-responsive s_table" style="overflow-x: auto;overflow-y: hidden;padding-bottom: 100px">
                                        <table class="table items item-purchase no-mtop">
                                            <thead>
                                                <tr>
                                                    <th><input type="hidden" id="itemID" value="" /></th>
                                                    <th style="min-width: 200px" class="text-left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_name'); ?>"></i> <?php echo _l('item_name'); ?></th>
                                                    <th style="min-width: 100px;max-width: 100px;" class="text-left"><?php echo _l('tk_no_131'); ?></th>
                                                    <th style="min-width: 100px;max-width: 100px;" class="text-left"><?php echo _l('tk_co_5111'); ?></th>
                                                    <th style="min-width: 80px" class="text-left"><?php echo _l('item_unit'); ?></th>
                                                    <th width="" class="text-left"><?php echo _l('item_quantity'); ?></th>
                                                    
                                                    <th width="" class="text-left"><?php echo _l('item_price'); ?></th>
                                                    <th width="" class="text-left"><?php echo _l('amount'); ?></th>
                                                    <th style="min-width: 100px;max-width: 100px;" class="text-left"><?php echo _l('tk_tax_1331'); ?></th>
                                                    <th width="" class="text-left"><?php echo _l('tax'); ?></th>
                                                    <th style="min-width: 100px;max-width: 100px;" class="text-left"><?php echo _l('tk_ck_5211'); ?></th>
                                                    <th style="min-width: 80px" class="text-left"><?php echo _l('discount').'(%)'; ?></th>
                                                    <th style="min-width: 100px" class="text-left"><?php echo _l('discount_money'); ?></th>
                                                    <th style="min-width: 100px" class="text-left"><?php echo _l('sub_amount'); ?></th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            
                                            <tbody>
                                                <tr class="main" <?=$display?>>
                                                    <td><input type="hidden" id="itemID" value="" /></td>
                                                    <td>
                                                        <?php echo _l('item_name'); ?>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" id="item_unit" value="" />
                                                        <?php echo _l('item_unit'); ?>
                                                    </td>

                                                    <td>
                                                        <input style="width: 100px " class="mainQuantity" type="number" min="1" value="1"  class="form-control" placeholder="<?php echo _l('item_quantity'); ?>">
                                                    </td>
                                                    
                                                    <td>
                                                        <?php echo _l('item_price'); ?>
                                                    </td>
                                                    <td>
                                                        0
                                                    </td>
                                                    <td>
                                                        <?php 
                                                            echo render_select('select_kindof_warehouse', $warehouse_types, array('id', 'name'));
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                        echo render_select('select_warehouse', array(), array('id', 'name'));
                                                        ?>
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
                                                    <td class="dragger"><?php echo $value->product_name.' ('.$value->prefix.$value->code.')'; ?></td>
                                                    <!-- TK NO -->
                                                    <td>
                                                        <?php
                                                        $selected=(isset($value) ? $value->tk_no : '6');
                                                        if(empty($selected)) $selected=6;
                                                        echo render_select('items['.$i.'][tk_no]',$accounts_no,array('idAccount','accountCode','accountName'),'',$selected); 
                                                        ?>
                                                    </td>
                                                    <!-- TK CO -->
                                                    <td>
                                                        <?php
                                                        $selected=(isset($value) ? $value->tk_co : '187');
                                                        if(empty($selected)) $selected=187;
                                                        echo render_select('items['.$i.'][tk_co]',$accounts_co,array('idAccount','accountCode','accountName'),'',$selected); 
                                                        ?>
                                                    </td>
                                                    <td><?php echo $value->unit_name; ?></td>
                                                    <td><input style="width: 100px" class="mainQuantity" min="<?=$value->quantity?>" max="<?=$value->quantity?>" type="number" name="items[<?php echo $i; ?>][quantity]" value="<?php echo $value->quantity; ?>" readonly></td>
                                                        
                                                    <td><?php echo number_format($value->unit_cost); ?></td>
                                                    <td><?php echo number_format($value->sub_total); ?></td>
                                                    <!-- TK Thue -->
                                                    <td>
                                                        <?php
                                                        $selected=(isset($value) ? $value->tk_thue : '92');
                                                        if(empty($selected)) $selected=92;
                                                        echo render_select('items['.$i.'][tk_thue]',$accounts_no,array('idAccount','accountCode','accountName'),'',$selected); 
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php echo number_format($value->tax); ?>
                                                        <input type="hidden" id="tax" data-taxrate="<?=$value->tax_rate?>" value="<?=$value->tax_id?>">
                                                    </td>
                                                    <!-- TK Chiet Khau -->
                                                    <td>
                                                        <?php
                                                        $selected=(isset($value) ? $value->tk_ck : '193');
                                                        if(empty($selected)) $selected=193;
                                                        echo render_select('items['.$i.'][tk_ck]',$accounts_no,array('idAccount','accountCode','accountName'),'',$selected); 
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <input style="width: 100px;" name="items[<?=$i?>][discount_percent]" min="0" class="discount_percent" type="number" value="<?php echo $value->discount_percent; ?>">
                                                    </td>
                                                    <td>
                                                        <input style="width: 100px;" name="items[<?=$i?>][discount]" class="discount" type="number" value="<?php echo $value->discount; ?>">
                                                    </td>
                                                    <td><?php echo number_format($value->amount); ?></td>
                                                    <td><a <?=$display?> href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td>
                                                </tr>
                                                    <?php
                                                        $totalPrice += $value->amount;
                                                        $i++;
                                                    }
                                                    $discount=$item->discount;
                                                    $adjustment=$item->adjustment;
                                                    $grand_total=$item->contract_value;
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- </div> -->
                                    <div class="col-md-10 col-md-offset-2">
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
                                                          <input type="text" name="discount_percent" id="discount_percent" min="0" class="form-control" placeholder="Phần trăm giảm giá" aria-describedby="basic-addon2" value="<?=$item->discount_percent?$item->discount_percent:0?>">
                                                          <span class="input-group-addon" id="basic-addon2">%</span>
                                                        </div>
                                                    </td>
                                                    <td class="discount_percent_total">
                                                        <?=format_money($discount?$discount:0)?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><span class="bold"><?php echo _l('adjustment_total'); ?> :</span>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                          <input type="number" name="adjustment" id="adjustment" class="form-control" placeholder="Giá trị điều chỉnh" aria-describedby="basic-addon2" value="0">
                                                          <!-- <span class="input-group-addon" id="basic-addon2"><?=($currency->symbol)?$currency->symbol:_l('VNĐ')?></span> -->
                                                        </div>
                                                    </td>
                                                    <td class="adjustment_total">
                                                        <?=format_money($adjustment?$adjustment:0)?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><span class="bold"><?php echo _l('purchase_total_price'); ?> :</span>
                                                    </td>
                                                    <td colspan="2" class="totalPrice">
                                                        <?php echo number_format($grand_total) ?><?=($currency->symbol)?$currency->symbol:_l('VNĐ')?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div role="tabpanel" class="tab-pane" id="capital_expenditures">
                                   <div class="table-responsive s_table" style="overflow-x: auto;overflow-y: hidden;padding-bottom: 100px">
                                        <table class="table items item-purchase-capital-expenditures no-mtop">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th style="min-width: 200px" class="text-left"><?php echo _l('item_name'); ?></th>
                                                    <th style="min-width: 100px;max-width: 100px;" class="text-left"><?php echo _l('tk_orginal_price'); ?></th>
                                                    <th style="min-width: 100px;max-width: 100px;" class="text-left"><?php echo _l('tk_kho'); ?></th>
                                                    <th style="min-width: 80px" class="text-left"><?php echo _l('item_unit'); ?></th>
                                                    <th width="" class="text-left"><?php echo _l('item_quantity'); ?></th>
                                                    
                                                    <th width="" class="text-left"><?php echo _l('item_price_orginal'); ?></th>
                                                </tr>
                                            </thead>
                                            
                                            <tbody>
                                                <?php
                                                $i=0;
                                                $totalPrice=0;
                                                if(isset($item) && count($item->items) > 0) {
                                                
                                                foreach($item->items as $value) {
                                                ?>
                                                <tr class="sortable item">
                                                    <td>
                                                        <input type="hidden" id="items[<?php echo $i; ?>][id]" value="<?php echo $value->product_id; ?>">
                                                    </td>
                                                    <td class="dragger"><?php echo $value->product_name.' ('.$value->prefix.$value->code.')'; ?></td>
                                                    <!-- TK Gia Von -->
                                                    <td>
                                                        <?php
                                                        $selected=(isset($value) ? $value->tk_gv : '69');
                                                        if(empty($selected)) $selected=69;
                                                        echo render_select('items['.$i.'][tk_gv]',$accounts_no,array('idAccount','accountCode','accountName'),'',$selected); 
                                                        ?>
                                                    </td>
                                                    <!-- TK Kho -->
                                                    <td>
                                                        <?php
                                                        $selected=(isset($value) ? $value->tk_kho : '107');
                                                        if(empty($selected)) $selected=107;
                                                        echo render_select('items['.$i.'][tk_kho]',$accounts_no,array('idAccount','accountCode','accountName'),'',$selected); 
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $value->unit_name; ?>

                                                        <?php 
                                                        $tong_gv=0;
                                                        foreach ($value->exports as $key => $valP) {
                                                            $tong_gv+=$valP->quantity*$valP->entered_price;
                                                            echo form_hidden('items['.$i.'][exports]['.$key.'][wp_detail_id]',$valP->wp_detail_id);
                                                            echo form_hidden('items['.$i.'][exports]['.$key.'][quantity]',$valP->quantity);
                                                            echo form_hidden('items['.$i.'][exports]['.$key.'][entered_price]',$valP->entered_price);
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?php echo _format_number($value->quantity); ?></td>
                                                        
                                                    <td><?php echo format_money($tong_gv); ?></td>
                                                    
                                                </tr>

                                                <?php } } ?>
                                            </tbody>
                                        </table>
                                   </div>
                                </div>

                            </div>
                            
                            
                        
                            
                        </div>
                    </div>
                </div>
                    

                <!-- End Customize from invoice if(isset($item) && $item->status != 2 || !isset($item))  -->
                
                    <!-- Tra or ko nhan hang -->
                </div>
                
                <?php if($convert==true) { ?>
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
    _validate_form($('.sales-form'),{code:'required',date:'required',customer_id:'required'});
    
    var itemList = <?php echo json_encode($items);?>;

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
    // var totalR = <?php echo $j ?>;
    var totalPrice = <?php echo $totalPrice ?>;
    // var totalPriceR = <?php echo $totalPriceR ?>;
    var uniqueArray = <?php echo $i ?>;
    var isNew = false;
    var createTrItem = () => {
        if(!isNew) return;
        if(!$('tr.main #select_warehouse option:selected').length || $('tr.main #select_warehouse option:selected').val() == '') {
            alert_float('danger', "Vui lòng chọn kho chứa sản phẩm!");
            return;
        }
        if( $('table.item-purchase tbody tr:gt(0)').find('input[value=' + $('tr.main').find('td:nth-child(1) > input').val() + ']').length ) {
            $('table.item-purchase tbody tr:gt(0)').find('input[value=' + $('tr.main').find('td:nth-child(1) > input').val() + ']').parent().find('td:nth-child(2) > input').focus();
            alert('Sản phẩm này đã được thêm, vui lòng lòng kiểm tra lại!');
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
        var td8 = $('<td></td>');

        td1.find('input').val($('tr.main').find('td:nth-child(1) > input').val());
        td2.text($('tr.main').find('td:nth-child(2)').text());
        td3.text($('tr.main').find('td:nth-child(3)').text());
        td4.find('input').val($('tr.main').find('td:nth-child(4) > input').val());
        
        td5.text( $('tr.main').find('td:nth-child(5)').text() );
        td6.text( $('tr.main').find('td:nth-child(6)').text() );
        td7.text( $('tr.main').find('td:nth-child(7) select option:selected').text() );
        td8.append( '<input type="hidden" data-store="'+$('tr.main').find('td:nth-child(8) select option:selected').data('store')+'" name="items[' + uniqueArray + '][warehouse]" value="'+$('tr.main').find('td:nth-child(8) select option:selected').val()+'" />');
        td8.append($('tr.main').find('td:nth-child(8) select option:selected').text());
        
        newTr.append(td1);
        newTr.append(td2);
        newTr.append(td3);
        newTr.append(td4);
        newTr.append(td5);
        newTr.append(td6);
        newTr.append(td7);
        newTr.append(td8);

        newTr.append('<td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td');
        $('table.item-purchase tbody').append(newTr);
        total++;
        totalPrice += $('tr.main').find('td:nth-child(4) > input').val() * $('tr.main').find('td:nth-child(5)').text().replace(/\+/g, ' ');
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
        // trBar.find('td:nth-child(1) > input').val('');
        trBar.find('td:nth-child(2)').text("<?=_l('item_name')?>");
        trBar.find('td:nth-child(3)').text("<?=_l('item_unit')?>");
        trBar.find('td:nth-child(4) > input').val('1');
        trBar.find('td:nth-child(5)').text("<?=_l('item_price')?>");
        trBar.find('td:nth-child(6)').text(0);
        trBar.find('td:nth-child(7)').text("<?=_l('tax')?>");
        trBar.find('td:nth-child(8)').text(0);
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

        $('.total').text(formatNumber(total));
        var items = $('table.item-purchase tbody tr:gt(0)');
        totalPrice = 0;
        $.each(items, (index,value)=>{
            totalPrice += parseFloat($(value).find('td:nth-child(6)').text().replace(/\,/g, ''))+parseFloat($(value).find('td:nth-child(7)').text().replace(/\,/g, ''));
            // * 
        });
        $('.totalPrice').text(formatNumber(totalPrice));
    };

    $('#warehouse_name').change(function(e){
        $('table tr.sortable.item').remove();
        total=0;
        refreshAll();
        refreshTotal();
    });


    $('#custom_item_select').change((e)=>{
        
        var id = $(e.currentTarget).val();
        $('#select_kindof_warehouse').val('');
        $('#select_kindof_warehouse').selectpicker('refresh');
        var warehouse_id=$('#select_warehouse');
        warehouse_id.find('option:gt(0)').remove();
        warehouse_id.selectpicker('refresh');
        var itemFound = findItem(id);
        if(typeof(itemFound) != 'undefined') {
            var trBar = $('tr.main');
            //console.log(trBar.find('td:nth-child(2) > input'));
            
            trBar.find('td:first > input').val(itemFound.id);
            trBar.find('td:nth-child(2)').text(itemFound.name+' ('+itemFound.prefix+itemFound.code+')');
            trBar.find('td:nth-child(3)').text(itemFound.unit_name);
            trBar.find('td:nth-child(3) > input').val(itemFound.unit);
            trBar.find('td:nth-child(4) > input').val(1);
            trBar.find('td:nth-child(5)').text(formatNumber(itemFound.price));
            trBar.find('td:nth-child(6)').text(formatNumber(itemFound.price * 1) );
            // trBar.find('td:nth-child(7)').text(itemFound.specification);
            // trBar.find('td:nth-child(8)').text(itemFound.specification);
            isNew = true;
            $('#btnAdd').show();
        }
        else 
        {
            isNew = false;
            $('#btnAdd').hide();
        }
    });
    var deleteTrItemR = (trItem) => {
        var current = $(trItem).parent().parent();
        totalPriceR -= current.find('td:nth-child(4) > input').val() * current.find('td:nth-child(5)').text().replace(/\,/g, '');
        $(trItem).parent().parent().remove();
        totalR--;
        refreshTotalR();
    };
    var isNewR = false;
    $('#custom_item_select_return').change(function(e){
        var id = $(e.currentTarget).val();
        var itemFound = findItemR(id);
        // console.log(itemFound);
        if(typeof(itemFound) != 'undefined') {
            var trBar = $('tr.mains');
            //console.log(trBar.find('td:nth-child(2) > input'));
            
            trBar.find('td:first > input').val(itemFound.product_id);
            trBar.find('td:nth-child(2)').text(itemFound.product_name+' ('+itemFound.prefix+itemFound.code+')');
            trBar.find('td:nth-child(3)').text(itemFound.unit_name);
            trBar.find('td:nth-child(3) > input').val(itemFound.unit);
            trBar.find('td:nth-child(4) > input').val(1);
            trBar.find('td:nth-child(4) > input').attr('min',0);
            trBar.find('td:nth-child(4) > input').attr('max',itemFound.quantity);
            trBar.find('td:nth-child(5)').text(formatNumber(itemFound.unit_cost));
            trBar.find('td:nth-child(6)').text(formatNumber(itemFound.unit_cost * 1) );
            // trBar.find('td:nth-child(7)').text(itemFound.specification);
            // trBar.find('td:nth-child(8)').text(itemFound.specification);
            isNewR = true;
            $('#btnRAdd').show();
        }
        else 
        {
            isNewR = false;
            $('#btnRAdd').hide();
        }
    });
    var refreshTotalR = () => {

        $('.totalR').text(formatNumber(totalR));
        var items = $('table.item-return tbody tr:gt(0)');
        totalPriceR = 0;
        $.each(items, (index,value)=>{
            totalPriceR += $(value).find('td:nth-child(4) > input').val() * $(value).find('td:nth-child(5)').text().replace(/\,/g, '');
        });
        $('.totalPriceR').text(formatNumber(totalPriceR));
    };
    var itemRs= <?php echo json_encode($item->items);?>;
    function findItemR(id){
        var itemResult;
        $.each(itemRs, (index,value) => {
            if(value.product_id == id) {
                itemResult = value;
                return false;
            }
        });
        return itemResult;
    };

    $('#select_warehouse').on('change', (e)=>{
        if($(e.currentTarget).val() != '') {
            $(e.currentTarget).parents('tr').find('input.mainQuantity').attr('data-store', $(e.currentTarget).find('option:selected').data('store'));
        }
    });

    
    function createTrItemR(){
        if(!isNewR) return;
        $('table.item-return tbody tr.empty').remove();
        var min=$('tr.mains').find('td:nth-child(4) > input').attr('min');
        var max=$('tr.mains').find('td:nth-child(4) > input').attr('max')
        if( $('table.item-return tbody tr:gt(0)').find('input[value=' + $('tr.mains').find('td:nth-child(1) > input').val() + ']').length ) {
            $('table.item-return tbody tr:gt(0)').find('input[value=' + $('tr.mains').find('td:nth-child(1) > input').val() + ']').parent().find('td:nth-child(2) > input').focus();
            alert('Sản phẩm này đã được thêm, vui lòng lòng kiểm tra lại!');
            return;
        }
        var newTr = $('<tr class="sortable item"></tr>');
        
        var td1 = $('<td><input type="hidden" name="itemsR[' + uniqueArray + '][id]" value="" /></td>');
        var td2 = $('<td class="dragger"></td>');
        var td3 = $('<td></td>');
        var td4 = $('<td><input style="width: 100px" class="mainQuantity" type="number" min="'+min+'" max="'+max+'" name="itemsR[' + uniqueArray + '][quantity]" value="" /></td>');
        var td5 = $('<td></td>');
        var td6 = $('<td></td>');
        var td7 = $('<td></td>');
        var td8 = $('<td></td>');

        td1.find('input').val($('tr.mains').find('td:nth-child(1) > input').val());
        td2.text($('tr.mains').find('td:nth-child(2)').text());
        td3.text($('tr.mains').find('td:nth-child(3)').text());
        td4.find('input').val($('tr.mains').find('td:nth-child(4) > input').val());
        
        td5.text( $('tr.mains').find('td:nth-child(5)').text() );
        td6.text( $('tr.mains').find('td:nth-child(6)').text() );
        // td7.text( $('tr.main').find('td:nth-child(7) select option:selected').text() );
        // td8.append( '<input type="hidden" data-store="'+$('tr.main').find('td:nth-child(8) select option:selected').data('store')+'" name="items[' + uniqueArray + '][warehouse]" value="'+$('tr.main').find('td:nth-child(8) select option:selected').val()+'" />');
        // td8.append($('tr.main').find('td:nth-child(8) select option:selected').text());
        
        newTr.append(td1);
        newTr.append(td2);
        newTr.append(td3);
        newTr.append(td4);
        newTr.append(td5);
        newTr.append(td6);
        // newTr.append(td7);
        // newTr.append(td8);

        newTr.append('<td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItemR(this); return false;"><i class="fa fa-times"></i></a></td');
        $('table.item-return tbody').append(newTr);
        totalR++;
        totalPriceR += $('tr.mains').find('td:nth-child(4) > input').val() * $('tr.mains').find('td:nth-child(5)').text().replace(/\+/g, ' ');
        uniqueArray++;
        refreshTotalR();
        // refreshAll();
    };

    $(document).on('keyup', '.mainQuantity',(e)=>{
        
        var currentQuantityInput = $(e.currentTarget);

        let elementToCompare;
        if(typeof(currentQuantityInput.attr('data-store')) == 'undefined' )
            elementToCompare = currentQuantityInput.parents('tr').find('input:last');
        else
            elementToCompare = currentQuantityInput;
        
        if(parseInt(currentQuantityInput.val()) > parseInt(elementToCompare.attr('data-store'))){
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
        // var Tong = Gia.find(' + td');
        // Tong.text( formatNumber(Gia.text().replace(/\,/g, '') * currentQuantityInput.val()) );
        // refreshTotal();
        
        var Gia = currentQuantityInput.parent().find(' + td');
        var GiaTri = Gia.find(' + td');
        var Thue = GiaTri.find(' + td');
        var Tong = Thue.find(' + td');
        var inputTax=Thue.find('input');        
        GiaTri.text(formatNumber(Gia.text().replace(/\,/g, '') * currentQuantityInput.val()) );
        Thue.text(formatNumber(parseFloat(inputTax.data('taxrate'))/100*parseFloat(GiaTri.text().replace(/\,/g,''))));
        Thue.append(inputTax);
        Tong.text(formatNumber(parseFloat(Thue.text().replace(/\,/g,''))+parseFloat(GiaTri.text().replace(/\,/g,''))));
        refreshTotal();
        refreshTotalR();
    });





    $('#select_kindof_warehouse').change(function(e){      
        var warehouse_type = $(e.currentTarget).val();
        var product = $(e.currentTarget).parents('tr').find('td:first input');
        // alert(warehouse_type+'=='+product.val())
        if(warehouse_type != '' && product.val() != '') {
            loadWarehouses(warehouse_type,product.val()); 
        }
    });
    function loadWarehouses(warehouse_type, filter_by_product,default_value=''){
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

     $('.customer-form-submiter').on('click', (e)=>{
        if($('input.error').length) {
            e.preventDefault();
            alert('Giá trị không hợp lệ!');    
        }
        if(<?=json_encode($item)?>)
        {
            var a=confirm("Bạn có chắc muốn cập nhật dữ liệu");
            if(a===false)
            {
                e.preventDefault();    
            }
            else
            {
                $('.sales-form').submit();
            }

        }
    });
    
</script>
</body>
</html>
