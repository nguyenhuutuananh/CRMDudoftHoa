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
  <h4 class="bold no-margin"><?php echo (isset($item) ? _l('edit_sale_order_so') : _l('add_sale_order_so')); ?></h4>

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
                
                if($item->invoice_status==1)
                {
                    $type='success';
                    $status='Đã lập hóa đơn';
                    $style='style="font-size: 8px"';
                }
            }
            else
            {
                $type='warning';
                $status='Phiếu mới';
            }

        ?>
        <div class="ribbon <?=$type?>"><span <?=$style?>><?=$status?></span></div>
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
                    <div class="pull-right">
                        <?php if( isset($item) ) { ?>
                        <a href="<?php echo admin_url('sales/pdf/' . $item->id . '?print=true') ?>" target="_blank" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="In" aria-describedby="tooltip652034"><i class="fa fa-print"></i></a>
                        <a href="<?php echo admin_url('sales/pdf/' . $item->id  ) ?>" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Xem PDF"><i class="fa fa-file-pdf-o"></i></a>
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
                         <label for="number"><?php echo _l('code_noo'); ?></label>
                         <div class="input-group">
                          <span class="input-group-addon">
                          <?php $prefix =($item) ? $item->prefix : get_option('prefix_sale'); ?>
                            <?=$prefix?>
                            <?php echo form_hidden('rel_type', 'sale_order_direct'); ?>
                            <?=form_hidden('prefix',$prefix)?>    
                            </span>
                            <?php 
                                if($item)
                                {
                                    $number=$item->code;
                                }
                                else
                                {
                                    $number=sprintf('%06d',getMaxID('id','tblsales')+1);
                                }
                            ?>
                            <input type="text" name="code" class="form-control" id="code" value="<?=$number ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $data_original_number; ?>" readonly>
                          </div>
                    </div>


                    <?php $value = (isset($item) ? _d($item->date) : _d(date('Y-m-d')));?>
                    <?php echo render_date_input('date','view_date',$value); ?>

                    <?php $value = (isset($item) ? _d($item->account_date) : _d(date('Y-m-d')));?>
                    <?php echo render_date_input('account_date','account_date',$value); ?>
                    
                    <?php
                    $default_name = (isset($item) ? $item->name : _l('sale_name'));
                    echo form_hidden('name', _l('sale_name'), $default_name);
                    ?>

                    <!-- <?php
                    $selected=(isset($item) ? $warehouse_type : '');
                    echo render_select('warehouse_type',$warehouse_types,array('id','name'),'warehouse_type',$selected); 
                    ?>

                    <?php
                    $selected=(isset($item) ? $warehouse_id : '');
                    echo render_select('warehouse_id',$warehouses,array('warehouseid','warehouse'),'warehouse_name',$selected); 
                    ?> -->

                    <?php
                    $selected=(isset($item) ? $item->customer_id : '');
                    echo render_select('customer_id',$customers,array('userid','company'),'client',$selected); 
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
                        <div class="row">
                            
                            <div class="col-md-4">
                                <?php 
                                    echo render_select('warehouse_name', $warehouses, array('warehouseid', 'warehouse'),'warehouse_name',$warehouse_id);
                                ?>
                            </div>
                            <div class="col-md-4">
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
                        

                        <div class="table-responsive s_table no-dt" style="overflow-x: auto;overflow-y: hidden;padding-bottom: 100px">
                            <table class="table items item-purchase no-mtop">
                                <thead>
                                    <tr>
                                        <th><input type="hidden" id="itemID" value="" /></th>
                                        <th style="min-width: 200px" class="text-left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_name'); ?>"></i> <?php echo _l('item_name'); ?></th>
                                        <th style="min-width: 100px;max-width: 100px;" class="text-left"><?php echo _l('tk_no'); ?></th>
                                        <th style="min-width: 100px;max-width: 100px;" class="text-left"><?php echo _l('tk_co'); ?></th>
                                        <th style="min-width: 80px" class="text-left"><?php echo _l('item_unit'); ?></th>
                                        <th style="min-width: 100px" class="text-left"><?php echo _l('item_quantity'); ?></th>
                                        
                                        <th style="min-width: 100px" class="text-left"><?php echo _l('item_price'); ?></th>
                                        <th style="min-width: 100px" class="text-left"><?php echo _l('amount'); ?></th>
                                        <th style="min-width: 100px" class="text-left"><?php echo _l('tax'); ?></th>

                                        <th style="min-width: 100px" class="text-left"><?php echo _l('sub_amount'); ?></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    <tr class="main">
                                        <td><input type="hidden" id="itemID" value="" /></td>
                                        <td>
                                            <?php echo _l('item_name'); ?>
                                        </td>
                                        <!-- TKno -->
                                        <td>
                                            <?php
                                            $selected=(isset($item) ? $item->tk_no : '');
                                            echo render_select('tk_no',$accounts_no,array('idAccount','accountCode','accountName'),'',$selected); 
                                            ?>
                                        </td>
                                        <!-- TKCo -->

                                        <td>
                                            <?php
                                            $selected=(isset($item) ? $item->tk_co : '');
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
                                        <td>
                                            <?php echo _l('tax'); ?>
                                            <input type="hidden" id="tax" data-taxid="" data-taxrate="" value="" />
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

                                        <td class="dragger"><?php echo $value->product_name.' ('.$value->prefix.$value->code.')'; ?></td>
                                        <!-- TK NO -->
                                        <td>
                                            <?php
                                            $selected=(isset($value) ? $value->tk_no : '');
                                            echo render_select('items['.$i.'][tk_no]',$accounts_no,array('idAccount','accountCode','accountName'),'',$selected); 
                                            ?>
                                        </td>
                                        <!-- TK CO -->
                                        <td>
                                            <?php
                                            $selected=(isset($value) ? $value->tk_co : '');
                                            echo render_select('items['.$i.'][tk_co]',$accounts_co,array('idAccount','accountCode','accountName'),'',$selected); 
                                            ?>
                                        </td>
                                        <td><?php echo $value->unit_name; ?></td>
                                        <td><input style="width: 100px" class="mainQuantity" type="number" name="items[<?php echo $i; ?>][quantity]" value="<?php echo $value->quantity; ?>"></td>
                                            
                                        <td><?php echo number_format($value->unit_cost); ?></td>
                                        <td><?php echo number_format($value->sub_total); ?></td>
                                        <td><?php echo number_format($value->tax) ?>
                                            <input type="hidden" id="tax" data-taxrate="<?=$value->tax_rate?>" value="<?=$value->tax_id?>">
                                        </td>
                                        <td><?php echo number_format($value->amount) ?></td>
                                        <td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td>
                                    </tr>
                                        <?php
                                            $totalPrice += $value->amount;
                                            $i++;
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
                                        <td class="total">
                                            <?php echo $i ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="bold"><?php echo _l('purchase_total_price'); ?> :</span>
                                        </td>
                                        <td class="totalPrice">
                                            <?php echo number_format($totalPrice) ?> VND
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>



                    </div>
                    </div>

                    


                </div>
                
                <?php { ?>
                  <button class="btn btn-info mtop20 only-save customer-form-submiter" style="margin-left: 15px">
                  <?php if($item->rel_id){
                    echo _l('return');
                    }
                    else
                        echo _l('submit');
                     ?>
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

    $('#warehouse_name').change(function(e){
        $('table tr.sortable.item').remove();
        total=0;
        var warehouse_id=$(this).val();
        loadProductsInWarehouse(warehouse_id)
        refreshAll();
        refreshTotal();
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
        var td6 = $('<td><input style="width: 100px" class="mainQuantity" type="number" name="items[' + uniqueArray + '][quantity]" value="" /></td>');
        var td7 = $('<td></td>');
        var td8 = $('<td></td>');
        var td9 = $('<td></td>');
        var td10 = $('<td></td>');

        td1.find('input').val($('tr.main').find('td:nth-child(1) > input').val());
        td2.text($('tr.main').find('td:nth-child(2)').text());

        var selectTd3 = $('tr.main').find('td:nth-child(3) select').clone();
        selectTd3.val($('tr.main').find('td:nth-child(3) select').selectpicker('val'));
        selectTd3.removeAttr('id');
        var tk_no='items['+uniqueArray+'][tk_no]';
        selectTd3.attr('name',tk_no);
        td3.append(selectTd3);


        var selectTd4 = $('tr.main').find('td:nth-child(4) select').clone();
        selectTd4.val($('tr.main').find('td:nth-child(4) select').selectpicker('val'));
        selectTd4.removeAttr('id');
        var tk_co='items['+uniqueArray+'][tk_co]';
        selectTd4.attr('name',tk_co);
        td4.append(selectTd4);

        td5.text($('tr.main').find('td:nth-child(5)').text());
        td6.find('input').val($('tr.main').find('td:nth-child(6) > input').val());        
        td7.text( $('tr.main').find('td:nth-child(7)').text() );
        td8.text( $('tr.main').find('td:nth-child(8)').text() );
        var inputTax=$('tr.main').find('td:nth-child(9) > input');
        td9.text( $('tr.main').find('td:nth-child(9)').text());
        td9.append(inputTax);
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
        //console.log(trBar.find('td:nth-child(2) > input'));
        
        trBar.find('td:first > input').val("");
        trBar.find('td:nth-child(2) ').text('<?=_l('item_name')?>');
        trBar.find('td:nth-child(5) ').text('');
        trBar.find('td:nth-child(6) > input').val('');
        trBar.find('td:nth-child(7) ').text('<?=_l("item_price")?>');
        trBar.find('td:nth-child(8) ').text('<?=_l("0")?>');
        trBar.find('td:nth-child(9) ').text('Thuế');
        trBar.find('td:nth-child(10) ').text('0');
    };
    var deleteTrItem = (trItem) => {
        var current = $(trItem).parent().parent();
        totalPrice -= current.find('td:nth-child(4) > input').val() * current.find('td:nth-child(5)').text().replace(/\,/g, '');
        $(trItem).parent().parent().remove();
        total--;
        refreshTotal();
    };
    var refreshTotal = () => {
        $('.selectpicker').selectpicker('refresh');
        $('.total').text(formatNumber(total));
        var items = $('table.item-purchase tbody tr:gt(0)');
        totalPrice = 0;
        $.each(items, (index,value)=>{
            totalPrice += parseFloat($(value).find('td:nth-child(8)').text().replace(/\,/g, ''))+parseFloat($(value).find('td:nth-child(9)').text().replace(/\,/g, ''));
            // * 
        });
        $('.totalPrice').text(formatNumber(totalPrice));
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


            trBar.find('td:nth-child(5)').text(itemFound.unit_name);
            trBar.find('td:nth-child(5) > input').val(itemFound.unit);
            trBar.find('td:nth-child(6) > input').val(1);
            trBar.find('td:nth-child(7)').text(formatNumber(itemFound.price));
            trBar.find('td:nth-child(8)').text(formatNumber(itemFound.price * 1) );
            var taxValue = (parseFloat(itemFound.tax_rate)*parseFloat(itemFound.price)/100);
            var inputTax = $('<input type="hidden" id="tax" data-taxrate="'+itemFound.tax_rate+'" value="'+itemFound.tax+'" />');
            trBar.find('td:nth-child(9)').text(formatNumber(taxValue));
            trBar.find('td:nth-child(9)').append(inputTax);
            trBar.find('td:nth-child(10)').text(formatNumber(parseFloat(taxValue)+parseFloat(itemFound.price)));
            isNew = true;
            $('#btnAdd').show();
        }
        else {
            isNew = false;
            $('#btnAdd').hide();
        }
    });
    $('#select_warehouse').on('change', (e)=>{
        if($(e.currentTarget).val() != '') {
            $(e.currentTarget).parents('tr').find('input.mainQuantity').attr('data-store', $(e.currentTarget).find('option:selected').data('store'));
        }
    });

    $('#select_warehouse').on('change', (e)=>{
        if($(e.currentTarget).val() != '') {
            $(e.currentTarget).parents('tr').find('input.mainQuantity').attr('data-store', $(e.currentTarget).find('option:selected').data('store'));
        }
    });

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
            var a=confirm("Bạn có chắc muốn ");
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
    
</script>
</body>
</html>
