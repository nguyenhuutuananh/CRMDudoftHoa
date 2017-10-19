<?php init_head(); ?>
<style type="text/css">
        fieldset 
    {
        border: 1px solid #ddd !important;
        margin: 0;
        xmin-width: 0;
        padding: 10px;       
        position: relative;
        border-radius:4px;
        background-color:#f5f5f5;
        padding-left:10px!important;
    }   
    
        legend
        {
            font-size:14px;
            font-weight:bold;
            margin-bottom: 0px; 
            width: 35%; 
            border: 1px solid #ddd;
            border-radius: 4px; 
            padding: 5px 5px 5px 10px; 
            background-color: #ffffff;
        }
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
        <!-- Product information -->
        

  <h4 class="bold no-margin"><?php echo (isset($item) ? (($item->status==2)?_l('Xem phiếu trả hàng'):_l('Sửa phiếu trả hàng')) : _l('Tạo phiếu trả hàng')); ?></h4>
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
                // if($item->invoice_status==1)
                // {
                //     $type='success';
                //     $status='Đã lập hóa đơn';
                //     $style='style="font-size: 8px"';
                // }
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
                    <?php echo _l('Chi tiết phiếu trả hàng'); ?>
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
                        <a href="<?php echo admin_url('imports/detail_pdf/' . $item->id . '?print=true') ?>" target="_blank" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="In" aria-describedby="tooltip652034"><i class="fa fa-print"></i></a>
                        <a href="<?php echo admin_url('imports/detail_pdf/' . $item->id  ) ?>" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Xem PDF"><i class="fa fa-file-pdf-o"></i></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            
            <?php echo form_open_multipart($this->uri->uri_string(), array('class' => 'client-form', 'autocomplete' => 'off')); ?>
                <div class="row">
                  <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">            
                    <?php
                    $attrs_not_select = array('data-none-selected-text' => _l('system_default_string'));
                    ?>
                    <div class="form-group">
                         <label for="number"><?php echo _l('code_noo'); ?></label>
                         <div class="input-group">
                          <span class="input-group-addon">
                          <?php $prefix =($item) ? $item->prefix : get_option('prefix_return'); ?>
                            <?=$prefix?>
                            <?php echo form_hidden('rel_type', 'return'); ?>
                            <?=form_hidden('prefix',$prefix)?>    
                            </span>
                            <?php 
                                if($item)
                                {
                                    $number=$item->code;
                                }
                                else
                                {
                                    $number=sprintf('%06d',getMaxID('id','tblimports')+1);
                                }
                            ?>
                            <input type="text" name="code" id="number" class="form-control" value="<?=$number ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $data_original_number; ?>" readonly>
                          </div>
                    </div>
                    <?php if(isset($item) && isset($item->rel_id)){ ?>
                    <?=form_hidden('rel_id',$item->rel_id)?>
                    <?php $row=getRow('tblsale_orders',array('id'=>$item->rel_id));?>
                    <?php $value = (isset($item) ? $row->prefix.$row->code : '');?>
                    <?php echo render_input('rel_code','orders_code',$value,'text',array('readonly'=>true)); ?>
                    <?php } ?>
                    

                    <?php $value = (isset($item) ? _d($item->date) : _d(date('Y-m-d')));?>
                    <?php echo render_date_input('date','view_date',$value); ?>

                    <?php $value = (isset($item) ? _d($item->account_date) : _d(date('Y-m-d')));?>
                    <?php echo render_date_input('account_date','account_date',$value); ?>
                    
                    <?php $selected = (isset($item) ? $item->customer_id : ''); ?>
                    <?php echo render_select('customer_id',$customers,array('userid','company'),'client',$selected); ?>

                    <?php $selected = (isset($item) ? $item->rel_id : ''); ?>
                    <?php echo render_select('rel_id',array(),array(),'sale_item_select',$selected); ?>

                    <?php
                    $default_name = (isset($item) ? $item->name : "Phiếu trả hàng");
                    echo render_input('name', _l('import_name'), $default_name);
                    ?>

                    <?php 
                    $reason = (isset($item) ? $item->reason : "");
                    echo render_textarea('reason', 'note', $reason, array(), array(), '', 'tinymce');
                    ?>
                </div>

                <div id="tk" class="hide">
                    <!-- TKno -->
                   
                        <?php
                        $selected=(isset($item) ? $item->tk_no : '');
                        echo render_select('tk_no_id',$accounts_no,array('idAccount','accountCode','accountName'),'',$selected); 
                        ?>
                    
                    <!-- TKCo -->

                   
                        <?php
                        $selected=(isset($item) ? $item->tk_co : '');
                        echo render_select('tk_co_id',$accounts_co,array('idAccount','accountCode','accountName'),'',$selected); 
                        ?>
                    
                </div>
                
                
                <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                    <!-- Cusstomize from invoice -->
                    <div class="panel-body mtop10">
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
                                    <div class="hide">
                                        <?php 
                                            $selected=69;
                                            echo render_select('tk_gv', $accounts_no, array('idAccount','accountCode', 'accountName'),'',$selected);
                                        ?>
                                        <?php 
                                            $selected=107;
                                            echo render_select('tk_kho', $accounts_no, array('idAccount','accountCode', 'accountName'),'',$selected);
                                        ?>
                                    </div>
                                    <?php if($item->rel_id) $display='style="display: none;"';?>
                                    <div class="col-md-4">
                                        <?php
                                        $selected=(isset($item) ? $warehouse_id : '');
                                        echo render_select('warehouse_id',$warehouses,array('warehouseid','warehouse'),'warehouse_name',$selected); 
                                        ?>
                                    </div>
                                    <div class="col-md-4 custom_item_select" <?=$display?> >
                                        <div class="form-group mbot25">
                                        <label for="custom_item_select"><?=_l('item_name')?></label>
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
                                <div class="table-responsive s_table mtop10" style="overflow-x: auto;overflow-y: hidden;padding-bottom: 200px">
                                    <table class="table items item-purchase no-mtop">
                                        <thead>
                                            <tr>
                                                <th><input type="hidden" id="itemID" value="" /></th>
                                                <th style="min-width: 200px" class="text-left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_name'); ?>"></i> <?php echo _l('item_name'); ?></th>
                                                <th style="min-width: 100px;max-width: 100px;" class="text-left"><?php echo _l('tk_no_5212'); ?></th>
                                                <th style="min-width: 100px;max-width: 100px;" class="text-left"><?php echo _l('tk_co_131'); ?></th>
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
                                                        <?php
                                                            // $selected=(isset($item) ? $item->tk_no : '194');
                                                            echo render_select('tk_no',$accounts_no,array('idAccount','accountCode','accountName'),'',$selected); 
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                            // $selected=(isset($item) ? $item->tk_co : '6');
                                                            echo render_select('tk_co',$accounts_co,array('idAccount','accountCode','accountName'),'',$selected); 
                                                        ?>
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
                                                            echo render_select('tk_thue', $accounts_no, array('idAccount','accountCode', 'accountName'));
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php echo _l('0'); ?>
                                                        <input type="hidden" id="tax" data-taxid="" data-taxrate="" value="" />
                                                    </td>
                                                    <!-- TK ck -->
                                                    <td>
                                                        <?php 
                                                            echo render_select('tk_ck', $accounts_no, array('idAccount','accountCode', 'accountName'));
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
                                                    <?php
                                                        $data_store=$value->warehouse_type->product_quantity; 
                                                        if($arr) $strmax='max="'.$value->quantity.'"';
                                                    ?>
                                                    <td><input style="width: 100px;<?=$type?>" class="mainQuantity <?=$class?>" min="0" <?=$strmax?>  type="number" name="items[<?php echo $i; ?>][quantity]" value="<?php echo $value->quantity; ?>" <?='data-store="'.$data_store.'"'?> <?=$title?> <?=$item->rel_id?'readonly':''?>>
                                                    </td>
                                                        
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
                                                        if(empty($selected)) $selected=93;
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
                                                    $grand_total=$item->total;
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
                                                        echo render_select('items['.$index.'][tk_gv]',$accounts_no,array('idAccount','accountCode','accountName'),'',$selected); 
                                                        ?>
                                                    </td>
                                                    <!-- TK Kho -->
                                                    <td>
                                                        <?php
                                                        $selected=(isset($value) ? $value->tk_kho : '107');
                                                        if(empty($selected)) $selected=107;
                                                        echo render_select('items['.$index.'][tk_kho]',$accounts_no,array('idAccount','accountCode','accountName'),'',$selected); 
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $value->unit_name; ?>
                                                    </td>
                                                    <td><?php echo _format_number($value->quantity); ?>
                                                        <?php 
                                                        $tong_gv=0;
                                                        foreach ($value->exports as $key => $valP) {
                                                            $tong_gv+=$valP->quantity*$valP->entered_price;
                                                            echo form_hidden('items['.$index.'][exports]['.$key.'][wp_detail_id]',$valP->wp_detail_id);
                                                            echo form_hidden('items['.$index.'][exports]['.$key.'][quantity]',$valP->quantity);
                                                            echo form_hidden('items['.$index.'][exports]['.$key.'][entered_price]',$valP->entered_price);
                                                        }
                                                        ?>                                                        
                                                    </td>
                                                        
                                                    <td><?php echo format_money($tong_gv); ?></td>
                                                    
                                                </tr>

                                                <?php $index++; }  } ?>
                                            </tbody>
                                        </table>
                                   </div>
                            </div>
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
    _validate_form($('.client-form'),{number:'required',warehouse_id:'required',date:'required',customer_id:'required'});
    

    var itemList = <?php echo json_encode($items);?>;


    $('#warehouse_type_id').change(function(e){
        var warehouse_type = $(e.currentTarget).val();
        if(warehouse_type != '') {
            getWarehouses('#warehouse_id',warehouse_type); 

        }
    });

    function getWarehouses(selector_warehouse,warehouse_type){
        var warehouse_id=$(selector_warehouse);
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
    

    $('#warehouse_id').change(function(e){
        $('table tr.sortable.item').remove();
        total=0;
        var warehouse_id=$(this).val();
        // loadProductsInWarehouse(warehouse_id);
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
        if(!$('div #warehouse_id option:selected').length || $('div #warehouse_id option:selected').val() == '') {
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
        var warehouse_id=$('#warehouse_id').val();
        var quantity=$('tr.main').find('input.mainQuantity').val();
        addCapitalExpendituresItem(product_id,warehouse_id,quantity,uniqueArray);
        total++;
        
        uniqueArray++;
        refreshTotal();
        refreshAll();
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
        if(isNaN(discount)) discount=0;
        var grand_total=totalPrice-discount+adjustment;
        $('.discount_percent_total').text(formatNumber(discount));

        $('.totalPrice').text(formatNumber(grand_total));
    };
    $('#custom_item_select').change((e)=>{
        var id = $(e.currentTarget).val();
        var warehouse_id=$('#warehouse_id').val();
        
        // var data=getMaxProductQuantity(warehouse_id,id);
        // var maxquantity=data.product_quantity;
            var itemFound = findItem(id);
            
            if(typeof(itemFound) != 'undefined') {
            var trBar = $('tr.main');
            
            trBar.find('td:first > input').val(itemFound.id);
            trBar.find('td:nth-child(2)').text(itemFound.name+' ('+itemFound.prefix+itemFound.code+')');
            trBar.find('td:nth-child(3) select').val('194').selectpicker('refresh');
            trBar.find('td:nth-child(4) select').val('6').selectpicker('refresh');
            trBar.find('td:nth-child(5)').text(itemFound.unit_name);
            trBar.find('td:nth-child(5) > input').val(itemFound.unit);
            trBar.find('td:nth-child(6) > input').val(1);
            trBar.find('td:nth-child(7)').text(formatNumber(itemFound.price));
            trBar.find('td:nth-child(8)').text(formatNumber(itemFound.price*1));
            trBar.find('td:nth-child(9) select').val('227').selectpicker('refresh');            
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
    // function getMaxProductQuantity(warehouse_id,product_id, callback){
    function getMaxProductQuantity(warehouse_id,product_id){
        $.ajax({
          url : admin_url + 'warehouses/getProductQuantity/' +warehouse_id+'/'+ product_id,
          dataType : 'json',
        })
        .done(function(data){
            console.log(data)
            if(data) 
            {
                return data;
                // callback(data);
            }
            return 0;
        });
    }
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
        var newTr = $('<tr class="sortable item"></tr>');        
        var td1 = $('<td><input type="hidden" id="product_id" value="" /></td>');
        var td2 = $('<td class="dragger"></td>');
        var td3 = $('<td></td>');
        var td4 = $('<td></td>');
        var td5 = $('<td></td>');
        var td6 = $('<td></td>');
        var td7 = $('<td><input class="totalOrginalPrice" type="number" min="1" value="1" placeholder="Tiền vốn" aria-invalid="false"></td>');

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
        var totalOrginalPrice=1;
        var entered_price=totalOrginalPrice;
        var totalOrginalPrice=totalOrginalPrice*quantity;
        var index=0;
            amount+=totalOrginalPrice;
            strExInput+=hidden_input('items['+uniqueArray+'][exports]['+index+'][wp_detail_id]','import-return');
            strExInput+=hidden_input('items['+uniqueArray+'][exports]['+index+'][quantity]',quantity)
            strExInput+=hidden_input('items['+uniqueArray+'][exports]['+index+'][entered_price]',entered_price)
        td6.append(strExInput);

        td7.find('input').val(totalOrginalPrice);
        newTr.append(td1);
        newTr.append(td2);
        newTr.append(td3);
        newTr.append(td4);
        newTr.append(td5);
        newTr.append(td6);
        newTr.append(td7);

        $('table.item-purchase-capital-expenditures tbody').append(newTr);
        $('.selectpicker').selectpicker('refresh');
        
    }

    $(document).on('change', '.mainQuantity', (e)=>{
        var currentInput = $(e.currentTarget);
        var product_id=currentInput.parents('tr').find('td:nth-child(1) > input').val();
        let row = $('#capital_expenditures tbody').find('td:has(#product_id[value='+product_id+'])').parent();
        var quantityTd=row.find('td:has(input[name$="[quantity]"])');
        try 
        {
            var quantityInput=quantityTd.find('input[name$="[quantity]"]');
            quantityInput.val(currentInput.val());
            var wp_detail_idInput=quantityTd.find('input[name$="[wp_detail_id]"]');
            var entered_priceInput=quantityTd.find('input[name$="[entered_price]"]');
            quantityTd.text(formatNumber(currentInput.val()));
            quantityTd.append(wp_detail_idInput);
            quantityTd.append(quantityInput);
            quantityTd.append(entered_priceInput);
            var totalOrginalPrice=quantityInput.val()*entered_priceInput.val();
            quantityTd.find('+ td').find('input').val(totalOrginalPrice);
        }
        catch(err) 
        {
            //Error
        }
    });
    $(document).on('change', '.totalOrginalPrice', (e)=>{
        var currentInput = $(e.currentTarget);
        var product_id=currentInput.parents('tr').find('td:nth-child(1) > input').val();
        let row = $('#capital_expenditures tbody').find('td:has(#product_id[value='+product_id+'])').parent();
        var quantityTd=row.find('td:has(input[name$="[quantity]"])');
        try 
        {
            
            var quantityInput=quantityTd.find('input[name$="[quantity]"]');
            var wp_detail_idInput=quantityTd.find('input[name$="[wp_detail_id]"]');
            var entered_priceInput=quantityTd.find('input[name$="[entered_price]"]');
            var entered_price=currentInput.val()/quantityInput.val();
            entered_priceInput.val(entered_price);
        }
        catch(err) 
        {
            //Error
        }
    });

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

    $('#warehouse_type').change(function(e){
      var warehouse_type = $(e.currentTarget).val();
      // $('table tr.sortable.item').remove();
      loadWarehouses(warehouse_type,'');
    });
    function loadWarehouses(warehouse_type,default_value=''){
        var warehouse_id=$('#warehouse_id');
        warehouse_id.find('option').remove()
        warehouse_id.selectpicker("refresh");
        if(warehouse_id != 0 && warehouse_id != '') {
        $.ajax({
          url : admin_url + 'warehouses/getWarehouses/' + warehouse_type,
          dataType : 'json',
        })
        .done(function(data){          
          warehouse_id.find('option').remove();
          warehouse_id.append('<option value=""></option>');
          $.each(data, function(key,value){
            var stringSelected = "";
            if(value.warehouseid == default_value) {
              stringSelected = ' selected="selected"';
            }
            warehouse_id.append('<option value="' + value.warehouseid + '"'+stringSelected+'>' + value.warehouse + '</option>');
          });
          warehouse_id.selectpicker('refresh');
        });
      }
    }

    $('.customer-form-submiter').on('click', function(e){
        var warehouse_id=$('#warehouse_id').val();
        var tk=$('select[name^="items"]');
        if($('input.error').length) {
            e.preventDefault();
            alert_float('danger', "Giá trị không hợp lệ!"); 
        }
        if(!warehouse_id)
        {
            alert_float('danger', "Vui lòng chọn kho chứa sản phẩm!");
            e.preventDefault(); 
        }
        $.each(tk, function(key,value){
        if($(value).val()=='')
        {
            alert_float('danger', "Vui lòng chọn tài khoản hạch toán!");
            e.preventDefault();
            
            return;
        }
        });
    });

    $('#customer_id').change(function(){
        var customer_id=$(this).val();
        $('table.item-purchase tbody tr.sortable.item').remove();
        loadAllSalesByCustomerID(customer_id);

    });
    function loadAllSalesByCustomerID(customer_id){
        var invoice_item=$('#rel_id');
        invoice_item.find('option:gt(0)').remove();
        invoice_item.selectpicker('refresh');
        if(invoice_item.length) {
            $.ajax({
                url : admin_url + 'sales/getAllSalesByCustomerID/' + customer_id+'/'+true,
                dataType : 'json',
            })
            .done(function(data){ 
                $.each(data, function(key,value){

                    invoice_item.append('<option value="' + value.id + '">'+ value.prefix+value.code + '</option>');
                });
                invoice_item.selectpicker('refresh');
            });
        }
    }

    $('#rel_id').change(function(){
      var sale_id=($(this).val());
      if(sale_id.length)
      {
        loadAllItemsBySaleID(sale_id);
      }
      else
      {
        $('table.item-purchase tbody tr.main').removeClass('hide');
        $('table.item-purchase tbody tr.sortable.item').remove();
        $('.custom_item_select').removeClass('hide');
      }
        
    });

    function loadAllItemsBySaleID(sale_id){
        $('table.item-purchase tbody tr.sortable.item').remove();
        $('table.item-purchase tbody tr.main').addClass('hide');
        $('.custom_item_select').addClass('hide');

        if(sale_id) {
            $.ajax({
                url : admin_url + 'sales/getAllItemsBySaleID/' + sale_id,
                dataType : 'json',
            })
            .done(function(data){ 
            
                total=0;
                $.each(data, function(key,value){
                    var newTr = $('<tr class="sortable item"></tr>');
                    var td1 = $('<td class="dragger"><input type="hidden" name="items[' + uniqueArray + '][id]" value="'+value.product_id+'" /></td>');
                    var td2 = $('<td>'+value.product_name+'</td>');

                    var td3 = $('<td></td>');
                    var selectTd3 = $('#tk_no_id').clone();
                    selectTd3.val('');
                    selectTd3.removeAttr('id');
                    var tk_no='items['+uniqueArray+'][tk_no]';
                    selectTd3.attr('name',tk_no);
                    td3.append(selectTd3);

                    var td4 = $('<td></td>');
                    var selectTd4 = $('#tk_co_id').clone();
                    selectTd4.val('');
                    selectTd4.removeAttr('id');
                    var tk_no='items['+uniqueArray+'][tk_co]';
                    selectTd4.attr('name',tk_no);
                    td4.append(selectTd4);
                    var maxQ=value.quantity-value.quantity_return;
                    var td5 = $('<td>'+value.unit_name+'</td>');
                    var td6 = $('<td><input style="width: 100px" class="mainQuantity" min="1" max="'+maxQ+'" type="number" name="items[' + uniqueArray + '][quantity]" value="'+formatNumber(value.quantity)+'" /></td>');
                    var td7 = $('<td>'+formatNumber(value.unit_cost)+'</td>');
                    var sub_total=value.unit_cost*maxQ;
                    var tax=sub_total*value.tax_rate/100;
                    var td8 = $('<td>'+formatNumber(sub_total)+'</td>');
                    var td9 = $('<td>'+formatNumber(tax)+'</td>');
                    td9.append('<input type="hidden" id="tax" data-taxrate="'+value.tax_rate+'" value="'+value.tax_id+'">')
                    var amount=sub_total+tax;
                    
                    if(isNaN(parseFloat(value.amount)))
                    {
                        amount=0;
                    }
                    
                    var td10 = $('<td>'+formatNumber(amount)+'</td>');
                    var td11 = $('<td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td>');

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
                    $('table.item-purchase tbody').append(newTr);
                    $('.selectpicker').selectpicker('refresh');
                    uniqueArray++;
                    total+=amount;
                    
                });
                // Total();
            });
        }
    }
        
</script>
</body>
</html>
