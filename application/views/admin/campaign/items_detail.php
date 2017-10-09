<div class="col-md-12">
    <div class="panel_s">
        <div class="panel-body">

            <?php if (isset($item)) { ?>
                <?php echo form_hidden('isedit'); ?>
                <?php echo form_hidden('itemid', $item->id); ?>
                <div class="clearfix"></div>
                <?php
            } ?>
            <div class="row">
                <div class="additional"></div>
                <div class="col-md-12">
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane " id="item_detail"></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <!-- Cusstomize from invoice -->
                            <div class="panel-body mtop10">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mbot25">
                                            <label for="custom_item_select" class="control-label"><?=_l('item_name')?></label>
                                            <select class="selectpicker no-margin" data-width="100%" id="custom_item_select" data-none-selected-text="<?php echo _l('add_item'); ?>" data-live-search="true">
                                                <option value=""></option>
                                                <?php foreach ($items as $product) { ?>
                                                    <option value="<?php echo $product['id']; ?>" data-subtext="">(<?php echo $product['code']; ?>) <?php echo $product['name']; ?></option>
                                                    <?php
                                                } ?>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="table-responsive s_table">
                                    <table class="table items _table _items _item-export no-mtop" border="">
                                        <thead>
                                        <tr>
                                            <th><input type="hidden" id="itemID" value="" /></th>
                                            <th width="" class="text-left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_name'); ?>"></i> <?php echo _l('item_name'); ?></th>
                                            <th width="" class="text-left"><?php echo _l('item_unit'); ?></th>
                                            <th width="" class="text-left"><?php echo _l('item_quantity'); ?></th>

                                            <th width="" class="text-left"><?php echo _l('item_price'); ?></th>
                                            <th width="" class="text-left"><?php echo _l('amount'); ?></th>

                                            <th width="" class="text-left"><?php echo _l('sub_amount'); ?></th>
                                            <th></th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <tr class="main _main">
                                            <td><input type="hidden" id="itemID" value="" /></td>
                                            <td>
                                                <?php echo _l('item_name'); ?>
                                            </td>
                                            <td>
                                                <input type="hidden" id="item_unit" value="" />
                                                <?php echo _l('item_unit'); ?>
                                            </td>

                                            <td>
                                                <input style="width: 100px" class="_mainQuantity form-control" type="number" min="1" value="1" placeholder="<?php echo _l('item_quantity'); ?>">
                                            </td>

                                            <td>
                                                <?php echo _l('item_price'); ?>
                                            </td>

                                            <td>
                                                0
                                            </td>
                                            <td>
                                                0
                                            </td>
                                            <td>
                                                <button  id="_btnAdd" type="button" onclick="_createTrItem(); return false;" class="btn pull-right btn-info"><i class="fa fa-check"></i></button>
                                            </td>
                                        </tr>
                                        <?php
                                        $i=0;
                                        $totalPrice=0;
                                        if(isset($_item) && count($_item) > 0) {

                                            foreach($_item as $value) {
                                                ?>
                                                <tr class="_sortable _item">
                                                    <td>
                                                        <input type="hidden" name="_item[<?php echo $i; ?>][id]" value="<?php echo $value['id_c']; ?>">
                                                    </td>
                                                    <td class="dragger"><?php echo $value['name']; ?></td>
                                                    <td><?php echo $value['unit']; ?></td>
                                                    <?php
                                                    $err='';
                                                    if($value->quantity>$value->warehouse_type->product_quantity)
                                                    {
                                                        $err='error';
                                                        $style='border: 1px solid red !important';
                                                    }
                                                    ?>
                                                    <td>
                                                        <input style="width: 100px;" class="mainQuantity form-control" type="number" name="_item[<?php echo $i; ?>][quantity]" value="<?php echo $value['quantity_item']; ?>">
                                                    </td>

                                                    <td><?php echo number_format($value['price']); ?></td>
                                                    <td><?php echo number_format($value['price_single']); ?></td>
                                                    <td><?php echo number_format($value['total']) ?></td>
                                                    <td><a href="#" class="btn btn-danger pull-right" onclick="_deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td>
                                                </tr>
                                                <?php
                                                $totalPrice += $value['total'];
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

                <!-- END PI -->
            </div>
        </div>
    </div>
</div>