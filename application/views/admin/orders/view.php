<?php init_head(); ?>
<div id="wrapper" class="customer_profile">
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
        

          <h4 class="bold no-margin"><?php echo _l('orders_view_heading') ?></h4>
  <hr class="no-mbot no-border" />
  <div class="row">
    <div class="additional"></div>
    <div class="col-md-12">
        
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
                    <div class="pull-right">
                    </div>
                </div>
            </div>
            
                <div class="row">
                  <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">            
                    <?php
                      // config
                    $attrs_not_select = array('data-none-selected-text' => _l('system_default_string'));
                    ?>
                    <!-- prefix_purchase_order -->
                    <div class="form-group">
                        <label for="number"><?php echo _l('orders_code'); ?></label>  
                                    
                        <input type="text" name="code" class="form-control" value="<?=$item->code ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $data_original_number; ?>" readonly>
                                  
                    </div>
                    
                    <div class="form-group">
                        <label for="id_purchase_suggested"><?php echo _l('purchase_suggested_code') ?></label>
                        <input type="text" class="form-control" value="<?php echo $item->code_purchase_suggested ?>" readonly>
                    </div>

                    
                    <?php 
                        $default_supplier = $item->id_supplier;
                        echo render_select('id_supplier', $suppliers, array('userid', 'company'), 'suppliers', $default_supplier, array('disabled'=>'disabled'));
                    ?>
                    <?php 
                        $default_warehouse = $item->id_warehouse;
                        echo render_select('id_warehouse', $warehouses, array('warehouseid', 'warehouse'), 'als_warehouses', $default_warehouse, array('disabled'=>'disabled'));
                    ?>
                    <?php
                        $default_date_create = date("Y-m-d", strtotime($item->date_create));
                        echo render_date_input( 'date_create', 'project_datecreated' , $default_date_create , array('readonly'=>'readonly')); 
                    ?>
                    <?php
                        $default_date_import = date("Y-m-d", strtotime($item->date_import));
                        echo render_date_input( 'date_import', 'orders_date_import' , $default_date_import, array('readonly'=>'readonly')); 
                    ?>
                    <?php 
                    $reason = $item->explan;
                    echo render_textarea('explan', 'orders_explan', $reason, array(), array(), '', 'tinymce');
                    ?>
                </div>

                
                
                
                <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                    <!-- Cusstomize from invoice -->
                    <div class="panel-body mtop10">
                                               

                        <div class="table-responsive s_table">
                            <table class="table items item-purchase no-mtop">
                                <thead>
                                    <tr>
                                        <th><input type="hidden" id="itemID" value="" /></th>
                                        <th width="15%" class="text-left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_code'); ?>"></i> <?php echo _l('item_code'); ?></th>
                                        <th width="15%" class="text-left"><?php echo _l('item_name'); ?></th>
                                        <th width="10%" class="text-left"><?php echo _l('item_unit'); ?></th>
                                        <th width="10%" class="text-left"><?php echo _l('item_quantity'); ?></th>
                                        
                                        <th width="10%" class="text-left"><?php echo _l('item_price_buy'); ?></th>
                                        <th width="10%" class="text-left"><?php echo _l('purchase_total_price'); ?></th>
                                        <th width="15%" class="text-left"><?php echo _l('item_specification'); ?></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    <?php
                                    $i=0;
                                    $totalPrice=0;
                                    if(isset($item->products) && count($item->products) > 0) {
                                        
                                        foreach($item->products as $value) {
                                        ?>
                                    <tr class="sortable item">
                                        <td>
                                            <input type="hidden"  value="<?php echo $value->product_id; ?>">
                                        </td>
                                        <td class="dragger"><?php echo $value->code; ?></td>
                                        <td><?php echo $value->product_name; ?></td>
                                        <td><?php echo $value->product_unit; ?></td>
                                        <td><input readonly="readonly" class="mainQuantity" type="number" value="<?php echo $value->product_quantity; ?>"></td>
                                            
                                        <td><?php echo number_format($value->product_price_buy); ?></td>
                                        <td><?php echo number_format($value->product_quantity*$value->product_price_buy); ?></td>
                                        <td><?php echo $value->product_specifications	; ?></td>
                                        <td></td>
                                    </tr>
                                        <?php
                                            $totalPrice += $value->product_quantity*$value->product_price_buy;
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
                <!-- End Customize from invoice -->
                </div>
                
              </div>
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
    
</script>
</body>
</html>
