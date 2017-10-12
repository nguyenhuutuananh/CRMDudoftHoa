<?php init_head(); ?>
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
  <h4 class="bold no-margin"><?php echo _l('add_sale_order_so'); ?></h4>
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
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 _buttons">
                    <div class="pull-right" style="display: none;">
                        <?php if( isset($item) ) { ?>
                        <a href="<?php echo admin_url('sales/pdf/' . $item->id . '?print=true') ?>" target="_blank" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="In" ><i class="fa fa-print"></i></a>
                        <a href="<?php echo admin_url('sales/pdf/' . $item->id  ) ?>" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Xem PDF"><i class="fa fa-file-pdf-o"></i></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            
            <?php echo form_open_multipart(admin_url('sales/sale_detail'), array('class' => 'sales-form', 'autocomplete' => 'off')); ?>
                <div class="row">
                  <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">            
                    <?php
                    $attrs_not_select = array('data-none-selected-text' => _l('system_default_string'));
                    ?>
                    <div class="form-group">
                         <label for="number"><?php echo _l('sale_code'); ?></label>
                         <div class="input-group">
                          <span class="input-group-addon">
                          <?php $prefix =get_option('prefix_sale'); ?>
                            <?=$prefix?>
                            <?php echo form_hidden('rel_type', 'sale_order'); ?>
                            <?=form_hidden('prefix',$prefix)?>
                            </span>
                            <?php
                                {
                                    $number=sprintf('%06d',getMaxID('id','tblsales')+1);
                                }
                            ?>
                            <input type="text" name="code" class="form-control" id="code" value="<?=$number ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $data_original_number; ?>" readonly>
                          </div>
                    </div>
                    <?php
                    //Lock when export
                    if($item)
                    {
                        $arr=array('disabled'=>true);  
                        $style='style="display: none;"';
                    }
                    ?>
                    <?php $value = (isset($item) ? $item->prefix.$item->code : '');?>
                    <?php echo render_input('rel_code','rel_code_order',$value,'text',array('readonly'=>true)); ?>

                    <?php
                    $default_name = (isset($item) ? $item->id : NULL);
                    echo form_hidden('rel_id',  $default_name);
                    ?>

                    <?php $value = (isset($item) ? _d($item->date) : _d(date('Y-m-d')));?>
                    <?php echo render_date_input('date','view_date',$value); ?>

                    <?php $value = (isset($item) ? _d($item->date_ht) : _d(date('Y-m-d')));?>
                    <?php echo render_date_input('account_date','account_date',$value); ?>

                    <?php
                    $default_name = _l('sale_name_SO');
                    echo form_hidden('name', _l('sale_name_SO'));
                    ?>

                    <?php
                    $selected=(isset($item) ? $item->customer_id : '');
                    echo render_select('customer_id',$customers,array('userid','company'),'client',$selected,$arr);
                    if($arr)
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
                            <div class="hidden">
                                <?php 
                                    $selected=69;
                                    echo render_select('tk_gv', $tk_no, array('idAccount','accountCode', 'accountName'),'',$selected);
                                ?>
                                <?php 
                                    $selected=107;
                                    echo render_select('tk_kho', $tk_no, array('idAccount','accountCode', 'accountName'),'',$selected);
                                ?>
                            </div>
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

                                            if($arr) echo form_hidden('warehouse_name',$warehouse_id);
                                                echo render_select('warehouse_name', $warehouses, array('warehouseid', 'warehouse'),'warehouse_name',$warehouse_id,$arr);
                                            ?>
                                        </div>
                                        <div class="col-md-4" <?=$style?>>
                                            <div class="form-group mbot25">
                                            <label for="custom_item_select" class="control-label"><?=_l('item_name')?></label>
                                                <select class="selectpicker no-margin" data-width="100%" id="custom_item_select" data-none-selected-text="<?php echo _l('add_item'); ?>" data-live-search="true" <?=($khoa? 'disabled': '')?> >
                                                    <option value=""></option>

                                                    <?php foreach ($items as $product) { ?>
                                                    <option value="<?php echo $product['id']; ?>" data-subtext="">(<?php echo $product['code']; ?>) <?php echo $product['name']; ?></option>
                                                    <?php 
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-5 text-right show_quantity_as_wrapper"></div>
                                    </div>

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
                                                <tr class="main" <?=$style?>>
                                                    <td><input type="hidden" id="itemID" value="" /></td>
                                                    <td>
                                                        <?php echo _l('item_name'); ?>
                                                    </td>
                                                    
                                                    <td>
                                                        <select class="selectpicker" id="tk_no" data-width="100%" data-none-selected-text="<?php  echo _l('tk_no')?>">
                                                            <?php if($tk_no){?>
                                                                <option></option>
                                                                <?php foreach($tk_no as $rom){?>
                                                                    <option value="<?=$rom['idAccount']?>" data-subtext="<?=$rom['accountName']?>"><?=$rom['accountCode']?></option>
                                                                <?php }?>
                                                            <?php }?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="selectpicker" id="tk_co" data-width="100%" data-none-selected-text="<?php  echo _l('tk_co')?>">
                                                            <?php if($tk_co){?>
                                                                <option></option>
                                                                <?php foreach($tk_co as $rom){?>
                                                                    <option value="<?=$rom['idAccount']?>" data-subtext="<?=$rom['accountName']?>"><?=$rom['accountCode']?></option>
                                                                <?php }?>
                                                            <?php }?>

                                                        </select>
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
                                                    <!-- TK thue -->
                                                    <td>
                                                        <?php 
                                                            echo render_select('tk_thue', $tk_no, array('idAccount','accountCode', 'accountName'));
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php echo _l('0'); ?>
                                                        <input type="hidden" id="tax" data-taxid="" data-taxrate="" value="" />
                                                    </td>
                                                    <!-- TK ck -->
                                                    <td>
                                                        <?php 
                                                            echo render_select('tk_ck', $tk_no, array('idAccount','accountCode', 'accountName'));
                                                        ?>
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
                                                        $type='';
                                                        $class='';
                                                        if($value->quantity>$value->warehouse_type->product_quantity)
                                                        {
                                                            $type="width: 100px;border: 1px solid red !important";
                                                            $class='error';
                                                            $title='title="Số lượng vượt mức cho phép!" data-toggle="tooltip"';
                                                        }
                                                        $max=0;
                                                        $export=0;
                                                        $strexport='';
                                                        if($value->export_quantity!=$value->quantity)
                                                        {
                                                            $max=$value->quantity-$value->export_quantity;
                                                            $export=(empty($value->export_quantity) ? 0 : $value->export_quantity);
                                                            $sub_total=$max*$value->unit_cost;
                                                            $tax=$sub_total*$value->tax_rate/100;
                                                            $discount=$sub_total*$value->discount_percent/100;
                                                            $amount=$sub_total+$tax-$discount;
                                                        
                                                        $strexport='('.$export.'/'.$value->quantity.')';
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
                                                        echo render_select('items['.$i.'][tk_no]',$tk_no,array('idAccount','accountCode','accountName'),'',$selected); 
                                                        ?>
                                                    </td>
                                                    <!-- TK CO -->
                                                    <td>
                                                        <?php
                                                        $selected=(isset($value) ? $value->tk_co : '187');
                                                        if(empty($selected)) $selected=187;
                                                        echo render_select('items['.$i.'][tk_co]',$tk_co,array('idAccount','accountCode','accountName'),'',$selected); 
                                                        ?>
                                                    </td>
                                                    <td><?php echo $value->unit_name; ?></td>
                                                    <?php
                                                        $data_store=$value->warehouse_type->product_quantity; 
                                                        if($arr) $strmax='max="'.$max.'"';
                                                    ?>
                                                    <td><input style="width: 100px;<?=$type?>" class="mainQuantity <?=$class?>" min="0" <?=$strmax?>  type="number" name="items[<?php echo $i; ?>][quantity]" value="<?php echo $max; ?>" <?='data-store="'.$data_store.'"'?> <?=$title?> <?=$item->rel_id?'readonly':''?>><?=$strexport?></td>
                                                        
                                                    <td><?php echo number_format($value->unit_cost); ?></td>
                                                    <td><?php echo number_format($sub_total); ?></td>
                                                    <!-- TK Thue -->
                                                    <td>
                                                        <?php
                                                        $selected=(isset($value) ? $value->tk_thue : '92');
                                                        if(empty($selected)) $selected=92;
                                                        echo render_select('items['.$i.'][tk_thue]',$tk_no,array('idAccount','accountCode','accountName'),'',$selected); 
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php echo number_format($tax); ?>
                                                        <input type="hidden" id="tax" data-taxrate="<?=$value->tax_rate?>" value="<?=$value->tax_id?>">
                                                    </td>
                                                    <!-- TK Chiet Khau -->
                                                    <td>
                                                        <?php
                                                        $selected=(isset($value) ? $value->tk_ck : '193');
                                                        if(empty($selected)) $selected=93;
                                                        echo render_select('items['.$i.'][tk_ck]',$tk_no,array('idAccount','accountCode','accountName'),'',$selected); 
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <input style="width: 100px;" name="items[<?=$i?>][discount_percent]" min="0" class="discount_percent" type="number" value="<?php echo $value->discount_percent; ?>">
                                                    </td>
                                                    <td>
                                                        <input style="width: 100px;" name="items[<?=$i?>][discount]" class="discount" type="number" value="<?php echo $value->discount; ?>">
                                                    </td>
                                                    <td><?php echo number_format($amount); ?></td>
                                                    <td><a <?=$display?> href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td>
                                                </tr>
                                                    <?php
                                                        $totalPrice += $amount;
                                                        $i++;
                                                    }
                                                    }
                                                    $discount=$item->discount;
                                                    $adjustment=$item->adjustment;
                                                    $grand_total=$totalPrice+$adjustment-$discount;
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
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
                                                          <input type="number" name="adjustment" id="adjustment" class="form-control" placeholder="Giá trị điều chỉnh" aria-describedby="basic-addon2" value="<?=($adjustment?$adjustment:0)?>">
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
                                                $index=0;
                                                $totalPrice=0;
                                                if(isset($item) && count($item->items) > 0) {
                                                
                                                foreach($item->items as $value) {
                                                    $max=0;
                                                    if($value->quantity!=$value->export_quantity)
                                                    {
                                                        $max=$value->quantity-$value->export_quantity;
                                                    
                                                ?>
                                                <tr class="sortable item">
                                                    <td>
                                                        <input type="hidden" id="product_id" value="<?php echo $value->product_id; ?>">
                                                    </td>
                                                    <td class="dragger"><?php echo $value->product_name.' ('.$value->prefix.$value->code.')'; ?></td>
                                                    <!-- TK Gia Von -->
                                                    <td>
                                                        <?php
                                                        $selected=(isset($value) ? $value->tk_gv : '69');
                                                        if(empty($selected)) $selected=69;
                                                        echo render_select('items['.$index.'][tk_gv]',$tk_no,array('idAccount','accountCode','accountName'),'',$selected); 
                                                        ?>
                                                    </td>
                                                    <!-- TK Kho -->
                                                    <td>
                                                        <?php
                                                        $selected=(isset($value) ? $value->tk_kho : '107');
                                                        if(empty($selected)) $selected=107;
                                                        echo render_select('items['.$index.'][tk_kho]',$tk_no,array('idAccount','accountCode','accountName'),'',$selected); 
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $value->unit_name; ?>
                                                    </td>
                                                    <td><?php echo _format_number($max); ?>
                                                        <?php 
                                                        $tong_gv=0;
                                                        $item_exports=getSaleProductDetail($value->product_id,$warehouse_id,$max);
                                                        foreach ($item_exports as $key => $valP) {
                                                            $tong_gv+=$valP->quantity*$valP->entered_price;
                                                            echo form_hidden('items['.$index.'][exports]['.$key.'][wp_detail_id]',$valP->wp_detail_id);
                                                            echo form_hidden('items['.$index.'][exports]['.$key.'][quantity]',$valP->quantity);
                                                            echo form_hidden('items['.$index.'][exports]['.$key.'][entered_price]',$valP->entered_price);
                                                        }
                                                        ?>                                                        
                                                    </td>
                                                        
                                                    <td><?php echo format_money($tong_gv); ?></td>
                                                    
                                                </tr>

                                                <?php $index++;} }  } ?>
                                            </tbody>
                                        </table>
                                   </div>
                                </div>
                            </div>

                        </div>
                        
                        </div>
                    </div>
                    <?php (isset($item)) ? $display='block' : $display='none' ?>
                    <div class="panel panel-info mtop20" style="display: <?='none'?>">
                        <div class="panel-heading"><?=_l('list_returns')?></div>
                        <div class="panel-body">
                            <!-- <div class="panel-body mtop10"> -->
                            <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mbot25">
                                    <select class="selectpicker no-margin" data-width="100%" id="custom_item_select_return" data-none-selected-text="<?php echo _l('add_item'); ?>" data-live-search="true">
                                        <option value=""></option>
                                        <?php foreach ($item->items as $product) { ?>
                                        <option value="<?php echo $product->product_id ?>" data-subtext="">(<?php echo $product->code ?>) <?php echo $product->product_name; ?></option>
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
                            <table class="table items item-return no-mtop">
                                <thead>
                                    <tr>
                                        <th><input type="hidden" id="itemID" value="" /></th>
                                        <th style="min-width: 200px" class="text-left"><?php echo _l('item_name'); ?></th>
                                        <th width="10%" class="text-left"><?php echo _l('item_unit'); ?></th>
                                        <th width="" class="text-left"><?php echo _l('item_quantity'); ?></th>
                                        
                                        <th width="" class="text-left"><?php echo _l('item_price'); ?></th>
                                        <th width="" class="text-left"><?php echo _l('amount'); ?></th>
                                        <th width="" class="text-left"><?php echo _l('tax'); ?></th>

                                        <th width="" class="text-left"><?php echo _l('sub_amount'); ?></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    <tr class="mains">
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
                                            <?php echo _l('tax'); ?>
                                            <input type="hidden" id="tax" data-taxid="" data-taxrate="" value="" />
                                        </td>
                                        <td>
                                            0
                                        </td>
                                        <td>
                                            <button style="display:none" id="btnRAdd" type="button" onclick="createTrItemR(); return false;" class="btn pull-right btn-info"><i class="fa fa-check"></i></button>
                                        </td>
                                    </tr>
                                <?php if(empty($item_returns)) {?>
                                    <tr class="empty">
                                        <td colspan="7"><?=_l('no_items_return')?></td>
                                    </tr>
                                <?php } ?>
                                    <?php
                                    $j=0;
                                    $totalPriceR=0;
                                    if(isset($item_returns) && count($item_returns) > 0) {
                                        
                                        foreach($item_returns as $value) {
                                            foreach ($item->items as $key => $val) {
                                                if($value->product_id==$val->product_id)
                                                {
                                                    $maxQ=$val->quantity;
                                                    break;
                                                }
                                            }
                                        ?>
                                    <tr class="sortable item">
                                        <td>
                                            <input type="hidden" name="itemsR[<?php echo $j; ?>][id]" value="<?php echo $value->product_id; ?>">
                                        </td>
                                        <td class="dragger"><?php echo $value->product_name.' ('.$value->prefix.$value->code.')'; ?></td>
                                        <td><?php echo $value->unit_name; ?></td>
                                        <td><input style="width: 100px" class="mainQuantity" type="number" min="0" max="<?=$maxQ?>" name="itemsR[<?php echo $j; ?>][quantity]" value="<?php echo $value->quantity; ?>"></td>
                                            
                                        <td><?php echo number_format($value->unit_cost); ?></td>
                                        <td><?php echo number_format($value->sub_total); ?></td>
                                        <td><?php echo number_format($value->tax) ?>
                                            <input type="hidden" id="tax" data-taxrate="<?=$value->tax_rate?>" value="<?=$value->tax_id?>">
                                        </td>
                                        <td><?php echo number_format($value->amount) ?></td>                                        
                                        <td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItemR(this); return false;"><i class="fa fa-times"></i></a></td>
                                    </tr>
                                        <?php
                                            $totalPriceR += $value->amount;
                                            $j++;
                                        }
                                    }
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
                                        <td class="totalR">
                                            <?php echo $j; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="bold"><?php echo _l('purchase_total_price'); ?> :</span>
                                        </td>
                                        <td class="totalPriceR">
                                            <?php echo number_format($totalPriceR); ?> VND
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                            <!-- </div> -->
                        </div>
                    </div>

                <!-- End Customize from invoice if(isset($item) && $item->status != 2 || !isset($item))  -->
                
                    <!-- Tra or ko nhan hang -->
                </div>
                
                <?php { ?>
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

    $('#warehouse_type').change(function(e){
        var warehouse_type = $(e.currentTarget).val();
        if(warehouse_type != '') {
            getWarehouses(warehouse_type); 
        }
    });
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
    var totalR = <?php echo $j ?>;
    var totalPrice = <?php echo $totalPrice ?>;
    var totalPriceR = <?php echo $totalPriceR ?>;
    var uniqueArray = <?php echo $i ?>;
    var uniqueArrayR = <?php echo $j ?>;
    var isNew = false;
    var createTrItem = () => {
        if(!isNew) return;
        if(!$('div #warehouse_name option:selected').length || $('div #warehouse_name option:selected').val() == '') {
            alert_float('danger', "Vui lòng chọn kho chứa sản phẩm!");
            return;
        }
        if( $('table.item-purchase tbody tr:gt(0)').find('input[value=' + $('tr.main').find('td:nth-child(1) > input').val() + ']').length ) {
            $('table.item-purchase tbody tr:gt(0)').find('input[value=' + $('tr.main').find('td:nth-child(1) > input').val() + ']').parent().find('td:nth-child(2) > input').focus();
            alert('Sản phẩm này đã được thêm, vui lòng lòng kiểm tra lại!');
            return;
        }
        if($('tr.main').find('input.mainQuantity').val()>$('tr.main').find('input.mainQuantity').attr('data-store'))
        {
            alert_float('danger','Sản phẩm bạn nhập là ['+$('tr.main').find('input.mainQuantity').val()+'] lớn hơn số lượng trong kho ['+$('tr.main').find('input.mainQuantity').attr('data-store')+'], vui lòng lòng kiểm tra lại!');
            return;
        }
        var newTr = $('<tr class="sortable item"></tr>');        
        var td1 = $('<td><input type="hidden" name="items[' + uniqueArray + '][id]" value="" /></td>');
        var td2 = $('<td class="dragger"></td>');
        var td3 = $('<td></td>');
        var td4 = $('<td></td>');
        var td5 = $('<td></td>');
        var td6 = $('<td><input style="width: 100px" class="mainQuantity" min="0" type="number" name="items[' + uniqueArray + '][quantity]" value="" /></td>');
        var td7 = $('<td></td>');
        var td8 = $('<td></td>');
        var td9 = $('<td></td>');
        var td10 = $('<td></td>');
        var td11 = $('<td></td>');
        var td12 = $('<td><input style="width: 100px" class="discount_percent" min="0" type="number" name="items[' + uniqueArray + '][discount_percent]" value="" /></td>');
        var td13 = $('<td><input style="width: 100px" class="discount" min="0" type="number" name="items[' + uniqueArray + '][discount]" value="" /></td>');
        var td14 = $('<td></td>');

        td1.find('input').val($('tr.main').find('td:nth-child(1) > input').val());
        td2.text($('tr.main').find('td:nth-child(2)').text());   

        let tk_no = $('tr.main').find('td:nth-child(3)').find('select').clone();
        tk_no.attr('name', 'items[' + uniqueArray + '][tk_no]');
        tk_no.removeAttr('id').val($('tr.main').find('td:nth-child(3)').find('select').selectpicker('val'));
        td3.append(tk_no);

        let tk_co = $('tr.main').find('td:nth-child(4)').find('select').clone();
        tk_co.attr('name', 'items[' + uniqueArray + '][tk_co]');
        tk_co.removeAttr('id').val($('tr.main').find('td:nth-child(4)').find('select').selectpicker('val'));
        td4.append(tk_co);

        td5.text($('tr.main').find('td:nth-child(5)').text());
        td6.find('input').val($('tr.main').find('td:nth-child(6) > input').val());
        td6.find('input').attr('data-store',$('tr.main').find('td:nth-child(6) > input').attr('data-store'));
        
        td7.text( $('tr.main').find('td:nth-child(7)').text());
        td8.text( $('tr.main').find('td:nth-child(8)').text());

        let tk_thue = $('tr.main').find('td:nth-child(9)').find('select').clone();
        tk_thue.attr('name', 'items[' + uniqueArray + '][tk_thue]');
        tk_thue.removeAttr('id').val($('tr.main').find('td:nth-child(9)').find('select').selectpicker('val'));
        td9.append(tk_thue);

        var inputTax=$('tr.main').find('td:nth-child(10) > input');
        td10.text( $('tr.main').find('td:nth-child(10)').text());
        td10.append(inputTax);

        let tk_ck = $('tr.main').find('td:nth-child(11)').find('select').clone();
        tk_ck.attr('name', 'items[' + uniqueArray + '][tk_ck]');
        tk_ck.removeAttr('id').val($('tr.main').find('td:nth-child(11)').find('select').selectpicker('val'));
        td11.append(tk_ck);
        td12.find('input').val($('tr.main').find('td:nth-child(12) > input').val());
        td13.find('input').val($('tr.main').find('td:nth-child(13) > input').val());

        td14.text($('tr.main').find('td:nth-child(14)').text());

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
        newTr.append(td11);
        newTr.append(td12);
        newTr.append(td13);
        newTr.append(td14);

        newTr.append('<td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td>');
        $('table.item-purchase tbody').append(newTr);
        var product_id=$('tr.main').find('td:nth-child(1) > input').val();
        var warehouse_id=$('#warehouse_name').val();
        var quantity=$('tr.main').find('input.mainQuantity').val();
        addCapitalExpendituresItem(product_id,warehouse_id,quantity,uniqueArray);
        total++;
        
        uniqueArray++;
        refreshTotal();
        $('.selectpicker').selectpicker('refresh');
    };
    var refreshAll = () => {
        
        isNew = false;
        $('#btnAdd').hide();
        $('#custom_item_select').val('');
        $('#custom_item_select').selectpicker('refresh');
        var trBar = $('tr.main');
        trBar.find('td:first > input').val("");
        trBar.find('td:nth-child(2) ').text('<?=_l('item_name')?>');
        trBar.find('td:nth-child(3) select').val('').selectpicker('refresh');
        trBar.find('td:nth-child(4) select').val('').selectpicker('refresh');
        trBar.find('td:nth-child(5) ').text('<?=_l('item_unit')?>');        
        trBar.find('td:nth-child(6) > input').val('1');
        trBar.find('td:nth-child(7) ').text('<?=_l("item_price")?>');
        trBar.find('td:nth-child(8) ').text('<?=_l("0")?>');
        trBar.find('td:nth-child(9) select').val('').selectpicker('refresh');
        trBar.find('td:nth-child(10)').val('0');
        trBar.find('td:nth-child(11) select').val('').selectpicker('refresh');
        trBar.find('td:nth-child(12) > input').val('0');
        trBar.find('td:nth-child(13) > input').val('0');
        trBar.find('td:nth-child(14)').text('0');
    };
    var deleteTrItem = (trItem) => {
        var current = $(trItem).parent().parent();
        totalPrice -= current.find('td:nth-child(4) > input').val() * current.find('td:nth-child(5)').text().replace(/\,/g, '');
        var product_id=$(trItem).parents('tr').find('td:nth-child(1) > input').val();
        let row = $('#capital_expenditures tbody').find('td:has(#product_id[value='+product_id+'])').parent();
        row.remove();
        $(trItem).parent().parent().remove();
        total--;
        refreshTotal();
    };
    var refreshTotal = () => {
        $('.total').text(formatNumber(total));
        var items = $('table.item-purchase tbody tr:gt(0)');
        totalPrice = 0;
        $.each(items, (index,value)=>{
            totalPrice += parseFloat($(value).find('td:nth-child(14)').text().replace(/\,/g, ''));
        });
        var discount_percent=$('#discount_percent').val();

        var discount=discount_percent*totalPrice/100;
        
        var adjustment=parseFloat($('#adjustment').val());
        if(isNaN(adjustment)) adjustment=0;
        var grand_total=totalPrice-discount+adjustment;
        $('.discount_percent_total').text(formatNumber(discount));
        $('.totalPrice').text(formatNumber(grand_total));
    };
    $('#custom_item_select').change((e)=>{
        var id = $(e.currentTarget).val();
        var itemFound = findItem(id);
        if(typeof(itemFound) != 'undefined') {
            var trBar = $('tr.main');
            
            trBar.find('td:first > input').val(itemFound.id);
            trBar.find('td:nth-child(2)').text(itemFound.name+' ('+itemFound.prefix+itemFound.code+')');
            trBar.find('td:nth-child(3) select').val('6').selectpicker('refresh');
            trBar.find('td:nth-child(4) select').val('187').selectpicker('refresh');
            trBar.find('td:nth-child(5)').text(itemFound.unit_name);
            trBar.find('td:nth-child(5) > input').val(itemFound.unit);
            trBar.find('td:nth-child(6) > input').val(1);
            trBar.find('td:nth-child(7)').text(formatNumber(itemFound.price));
            trBar.find('td:nth-child(8)').text(formatNumber(itemFound.price*1));
            trBar.find('td:nth-child(9) select').val('92').selectpicker('refresh');            
            var taxValue = (parseFloat(itemFound.tax_rate)*parseFloat(itemFound.price)/100);
            var inputTax = $('<input type="hidden" id="tax" data-taxrate="'+itemFound.tax_rate+'" value="'+itemFound.tax+'" />');
            trBar.find('td:nth-child(10)').text(formatNumber(taxValue));
            trBar.find('td:nth-child(10)').append(inputTax);
            trBar.find('td:nth-child(11) select').val('193').selectpicker('refresh');    
            trBar.find('td:nth-child(12) > input').val(0);
            trBar.find('td:nth-child(13) > input').val(0);
            trBar.find('td:nth-child(14)').text(formatNumber(parseFloat(taxValue)+parseFloat(itemFound.price)));
            isNew = true;
            $('#btnAdd').show();
        }
        else {
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
            trBar.find('td:first > input').val(itemFound.product_id);
            trBar.find('td:nth-child(2)').text(itemFound.product_name+' ('+itemFound.prefix+itemFound.code+')');
            trBar.find('td:nth-child(3)').text(itemFound.unit_name);
            trBar.find('td:nth-child(3) > input').val(itemFound.unit);
            trBar.find('td:nth-child(4) > input').val(1);
            //max min =>1 quantity
            trBar.find('td:nth-child(4) > input').attr('max',itemFound.quantity);

            trBar.find('td:nth-child(5)').text(formatNumber(itemFound.unit_cost));
            trBar.find('td:nth-child(6)').text(formatNumber(itemFound.unit_cost * 1) );
            var taxValue = (parseFloat(itemFound.tax_rate)*parseFloat(itemFound.unit_cost)/100);
            var inputTax = $('<input type="hidden" id="tax" data-taxrate="'+itemFound.tax_rate+'" value="'+itemFound.tax_id+'" />');
            trBar.find('td:nth-child(7)').text(formatNumber(taxValue));
            trBar.find('td:nth-child(7)').append(inputTax);
            trBar.find('td:nth-child(8)').text(formatNumber(parseFloat(taxValue)+parseFloat(itemFound.unit_cost)));
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
            totalPriceR += parseFloat($(value).find('td:nth-child(6)').text().replace(/\,/g, ''))+parseFloat($(value).find('td:nth-child(7)').text().replace(/\,/g, ''));
            // * 
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
        var max=$('tr.mains').find('td:nth-child(4) > input').attr('max');

        if( $('table.item-return tbody tr:gt(0)').find('input[value=' + $('tr.mains').find('td:nth-child(1) > input').val() + ']').length ) {
            $('table.item-return tbody tr:gt(0)').find('input[value=' + $('tr.mains').find('td:nth-child(1) > input').val() + ']').parent().find('td:nth-child(2) > input').focus();
            alert('Sản phẩm này đã được thêm, vui lòng lòng kiểm tra lại!');
            return;
        }
        var newTr = $('<tr class="sortable item"></tr>');        
        var td1 = $('<td><input type="hidden" name="itemsR[' + uniqueArray + '][id]" value="" /></td>');
        var td2 = $('<td class="dragger"></td>');
        var td3 = $('<td></td>');
        var td4 = $('<td><input style="width: 100px" class="mainQuantity" type="number" name="itemsR[' + uniqueArray + '][quantity]" value="" /></td>');
        var td5 = $('<td></td>');
        var td6 = $('<td></td>');
        var td7 = $('<td></td>');
        var td8 = $('<td></td>');

        td1.find('input').val($('tr.mains').find('td:nth-child(1) > input').val());
        td2.text($('tr.mains').find('td:nth-child(2)').text());
        td3.text($('tr.mains').find('td:nth-child(3)').text());

        td4.find('input').val($('tr.mains').find('td:nth-child(4) > input').val());
        td4.find('input').attr('min',min);
        td4.find('input').attr('max',max);

        td5.text( $('tr.mains').find('td:nth-child(5)').text() );
        td6.text( $('tr.mains').find('td:nth-child(6)').text() );
        var inputTax=$('tr.mains').find('td:nth-child(7) > input');
        td7.text( $('tr.mains').find('td:nth-child(7)').text());
        td7.append(inputTax);
        td8.text($('tr.mains').find('td:nth-child(8)').text());

        newTr.append(td1);
        newTr.append(td2);
        newTr.append(td3);
        newTr.append(td4);
        newTr.append(td5);
        newTr.append(td6);
        newTr.append(td7);
        newTr.append(td8);

        newTr.append('<td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItemR(this); return false;"><i class="fa fa-times"></i></a></td');
        $('table.item-return tbody').append(newTr);
        totalR++;
        totalPriceR += $('tr.mains').find('td:nth-child(4) > input').val() * $('tr.mains').find('td:nth-child(5)').text().replace(/\+/g, ' ');
        uniqueArrayR++;
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


        calculateTotal(e.currentTarget);
        refreshTotalR();
    });

    var calculateTotal = (currentInput) => {
        currentInput = $(currentInput);   
        let quantity = currentInput.parents('tr').find('.mainQuantity');
        let quantityTd = quantity.parent();

        let priceTd = quantityTd.find('+ td');

        let amountTd = priceTd.find('+ td');
        var amount=priceTd.text().replace(/\,/g, '') * quantity.val();
        amountTd.text(formatNumber(amount));

        let taxTd=amountTd.find('+ td + td');
        var inputTax=taxTd.find('input')
        var tax=parseFloat(inputTax.data('taxrate'))/100*parseFloat(amount);
        taxTd.text(formatNumber(tax));
        taxTd.append(inputTax);

        let discountPercent=currentInput.parents('tr').find('.discount_percent');

        let discount=currentInput.parents('tr').find('.discount');
        var discountTd=discount.parent();
        var discountValue=amount*discountPercent.val()/100;
        discount.val(discountValue);

        let subTotalTd=discountTd.find('+ td');
        subTotalTd.text(formatNumber(amount+tax-discountValue));

        refreshTotal();
    };

    function addCapitalExpendituresItem(product_id,warehouse_id,quantity,uniqueArray)
    {
        var data={
         product_id: product_id,
         warehouse_id: warehouse_id,
         quantity: quantity
       };
        $.post(admin_url + 'sale_orders/getSaleProductDetail', data).done(function(response) {
        response = JSON.parse(response);
        // console.log(response);
         // <input type="hidden" id="itemID" value="131">
        var newTr = $('<tr class="sortable item"></tr>');        
        var td1 = $('<td><input type="hidden" id="product_id" value="" /></td>');
        var td2 = $('<td class="dragger"></td>');
        var td3 = $('<td></td>');
        var td4 = $('<td></td>');
        var td5 = $('<td></td>');
        var td6 = $('<td></td>');
        var td7 = $('<td></td>');

        td1.find('input').val($('tr.main').find('td:nth-child(1) > input').val());
        td2.text($('tr.main').find('td:nth-child(2)').text());

        let tk_gv = $('#tk_gv').clone();
        tk_gv.attr('name', 'items[' + uniqueArray + '][tk_gv]');
        tk_gv.removeAttr('id').val($('#tk_gv').selectpicker('val'));
        td3.append(tk_gv);

        let tk_kho = $('#tk_kho').clone();
        tk_kho.attr('name', 'items[' + uniqueArray + '][tk_kho]');
        tk_kho.removeAttr('id').val($('#tk_kho').selectpicker('val'));
        td4.append(tk_kho);

        td5.text($('tr.main').find('td:nth-child(5)').text());
        td6.text(formatNumber($('tr.main').find('td:nth-child(6) > input').val()));
        var strExInput='';
        var amount=0;
        $.each(response,function(index,item){
            amount+=item.quantity*item.entered_price;
            strExInput+=hidden_input('items['+uniqueArray+'][exports]['+index+'][wp_detail_id]',item.wp_detail_id);
            strExInput+=hidden_input('items['+uniqueArray+'][exports]['+index+'][quantity]',item.quantity)
            strExInput+=hidden_input('items['+uniqueArray+'][exports]['+index+'][entered_price]',item.entered_price)
        });
        td6.append(strExInput);

        td7.text(formatNumber(amount));
        newTr.append(td1);
        newTr.append(td2);
        newTr.append(td3);
        newTr.append(td4);
        newTr.append(td5);
        newTr.append(td6);
        newTr.append(td7);

        $('table.item-purchase-capital-expenditures tbody').append(newTr);
        $('.selectpicker').selectpicker('refresh');
       });
        
    }
    // <tr class="sortable item">

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
    $(document).on('change', '#discount_percent', (e)=>{
        var currentInput = $(e.currentTarget);
        calculateTotal(e.currentTarget);
    });
    // $(document).ready(function(){
    //     var str='items[0][exports][0][quantity]';
    //     console.log(str.split('][')[0].split('[')[1]);
    // });

    $(document).on('change', '.mainQuantity', (e)=>{
        var currentInput = $(e.currentTarget);
        var product_id=currentInput.parents('tr').find('td:nth-child(1) > input').val();
        let row = $('#capital_expenditures tbody').find('td:has(#product_id[value='+product_id+'])').parent();
        var quantityTd=row.find('td:has(input[name$="[quantity]"])');
        try 
        {
            var uniqueIndex=quantityTd.find('input[name$="[quantity]"]').attr('name').split('][')[0].split('[')[1];       
            var warehouse_id=$('#warehouse_name').val();
            var quantity=currentInput.val();
            var data={
                     product_id: product_id,
                     warehouse_id: warehouse_id,
                     quantity: quantity
                    };
            quantityTd.text(formatNumber(currentInput.val()));
            $.post(admin_url + 'sale_orders/getSaleProductDetail', data, 'json').done(function(response) {
                response = JSON.parse(response);
                var amount=0;
                var strExInput='';
                $.each(response,function(index,item){
                amount+=item.quantity*item.entered_price;
                strExInput+=hidden_input('items['+uniqueIndex+'][exports]['+index+'][wp_detail_id]',item.wp_detail_id);
                strExInput+=hidden_input('items['+uniqueIndex+'][exports]['+index+'][quantity]',item.quantity)
                strExInput+=hidden_input('items['+uniqueIndex+'][exports]['+index+'][entered_price]',item.entered_price)
                    });
                quantityTd.append(strExInput);
                quantityTd.find('+ td').text(formatNumber(amount));
            });
        }
        catch(err) {
            //Error
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

     $('.customer-form-submiter').on('click', (e)=>{
        if($('input.error').length) {
            e.preventDefault();
            alert('Giá trị không hợp lệ!');  
            return;  
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
