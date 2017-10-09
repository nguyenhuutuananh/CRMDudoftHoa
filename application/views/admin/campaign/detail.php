<?php init_head(); ?>
<div id="wrapper" class="customer_profile">
 <div class="content">
   <div class="row">

  <div class="col-md-12">
   <div class="panel_s">
     <div class="panel-body">
      <div class="clearfix"></div>
         <h4 class="bold no-margin"><?php echo (isset($campaign) ? _l('campaign_edit') : _l('campaign_add')); ?></h4>
  <hr class="no-mbot no-border" />
    <div class="row">
    <div class="additional"></div>
    <div class="col-md-12">
        <?php $disabled='disabled';?>
        <ul class="nav nav-tabs profile-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#item_detail" aria-controls="item_detail" role="tab" data-toggle="tab">
                    <?php echo _l('purchase_suggested_information'); ?>
                </a>
            </li>
            <li role="presentation">
                <a href="#_items" aria-controls="_items" role="tab" data-toggle="tab">
                    <?php echo _l('title_items'); ?>
                </a>
            </li>
        </ul>
        <?php echo form_open_multipart($this->uri->uri_string(), array('class' => 'client-form', 'autocomplete' => 'off')); ?>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="item_detail">
                    <div class="row">

                    <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">

                    </div>

                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 _buttons">
                        <div class="pull-right"></div>
                    </div>
                </div>
                    <div class="row">
                      <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                          <?php
                          $name = (isset($campaign) ? $campaign->name : "");
                          echo  render_input('name', _l('_campaign_name'), $name);
                          $client_disabled=array();
                          ?>
                          <?php $selected_staff = (isset($campaign) ? $campaign->staff_manage : "");?>
    <!--                      --><?php //$client_disabled = (isset($receipt) ? array('disabled'=>$disabled) : array());?>
                          <?php echo render_select('staff_manage',$staff,array('staffid','fullname','staff_code'),_('staff_manage'),$selected_staff,$client_disabled); ?>

                          <?php
                            if(isset($__staff))
                            {
                                $campaign_staff=array();
                                foreach($__staff as $rom)
                                {
                                    $campaign_staff[]=$rom['id_staff'];
                                }
                            }
                          ?>
                          <?php $selected_staff = (isset($campaign_staff) ? $campaign_staff : array());?>
                          <?php echo render_select('campaign_staff[]',$staff,array('staffid','fullname','staff_code'),_('staff'),$selected_staff,array('multiple'=>true)); ?>
                          <?php
                          $expense = (isset($campaign) ? $campaign->expense : "");
                          echo  render_input('expense', _l('expense'), _format_number($expense),'type',array("onkeyup"=>"formart_num('expense')"));
                          ?>



                    </div>

                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                        <?php
                        $default_date = ( isset($campaign) ? _d($campaign->start_data) : _d(date('Y-m-d')));
                        echo render_date_input( 'start_data', 'start_data' , $default_date , 'date');
                        ?>
                        <?php
                        $default_date = ( isset($campaign) ? _d($campaign->end_date) : _d(date('Y-m-d')));
                        echo render_date_input( 'end_date', '_end_date' , $default_date , 'date');
                        ?>
                    </div>


                    <!-- Edited -->
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <!-- Cusstomize from invoice -->
                        <div class="panel-body mtop10 col-xs-9 col-sm-9 col-md-9 col-lg-9">

                            <?php $readonly='';$display="";?>
                            <div class="table-responsive s_table">
                                <table class="table items item-export no-mtop">
                                    <thead>
                                        <tr>
                                            <th><input type="hidden" id="itemID" value="" /></th>
                                            <th width="width:85%" class="text-left"><?php echo _l('step_name_campaign'); ?></th>
                                            <th></th>

                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr class="main">
                                            <td><input type="hidden" id="itemID" value="" /></td>
                                           <td style="padding-top: 8px;width:85%"><div class="form-group"><input type="text" id="name_step" class="form-control" value=""></div></td>
                                           <td>
                                               <button style="" id="btnAdd" type="button" onclick="createTrItem(); return false;" class="btn pull-right btn-info"><i class="fa fa-check"></i></button>
                                           </td>
                                        </tr>
                                        <?php
                                        $i=0;
                                        $totalPrice=0;
                                        $subtotal=0;
                                        $subdiscount=0;
                                            if(isset($campaign_step) && count($campaign_step) > 0) {

                                                foreach($campaign_step as $value) {
                                                ?>
                                                    <tr class="sortable item">
                                                        <td>
                                                            <input type="hidden" name="item[<?php echo $i; ?>][id]" value="<?php echo $value['id']; ?>">
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input  type="text" name="item[<?php echo $i ?>][name]" class="form-control" value="<?php echo $value['name']; ?>">
                                                            </div>
                                                        </td>
                                                        <td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td>
                                                    </tr>
                                                <?php
                                                    $i++;
                                                }
                                            }
                                            ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- End Customize from invoice -->
                    </div>
                    <!-- End edited -->
                  </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="_items">
                    <?php $this->load->view('admin/campaign/items_detail');?>
                </div>
                <button class="btn btn-info mtop20 only-save customer-form-submiter" style="margin-left: 15px">
                    <?php echo _l('submit'); ?>
                </button>
            </div>
        <?php echo form_close(); ?>

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
    var itemList = <?php echo json_encode($products);?>;

    var item_receipts_contract = <?php echo json_encode($receipts_contract);?>;
    console.log(item_receipts_contract);
    var itemList_receipts_contract_purchase = <?php echo json_encode($receipts_contract_purchase);?>;

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
    $('._subtotal').on('change', function() {
        //this = $(this);
        this.value = formatNumber(this.value);
    });
    var findItem = (id,type) => {
        var itemResult;
        if(type==1)
        {
            $.each(item_receipts_contract, (index,value) => {
                console.log(value.sum_contract);
                if(id == index) {
                    itemResult = value;
                    return false;
                }
            });
        }
        else
        {
            $.each(itemList_receipts_contract_purchase, (index,value) => {
                if(id == index) {
                    itemResult = value;
                    return false;
                }
            });
        }
        return itemResult;
    }

    var total = <?php echo $i ?>;
    var totalPrice = <?php echo $totalPrice ?>;
    var uniqueArray = <?php echo $i ?>;
    var isNew = false;
	// Remove select name
	$('#select_kindof_warehouse').removeAttr('name');
	$('#select_warehouse').removeAttr('name');
    $('#select_currency').removeAttr('name');
    var createTrItem = () => {
        if($('#name_step').val()==""||$('#name').val()==null)
        {
            alert_float('danger','Tên bước chiến dịch không được để trống');
            return false;
        }
        var newTr = $('<tr class="sortable item"></tr>');

        var td1 = $('<td><input type="hidden" name="items[' + uniqueArray + '][id]" value="" /></td>');
        var td2 = $('<td><div class="form-group"><input type="text" name="items[' + uniqueArray + '][name]" value="" class="form-control" /></div></td>');

        td1.find('input').val($('tr.main').find('td:nth-child(1) input').val());
        td2.find('input').val($('tr.main').find('td:nth-child(2) input').val());

		newTr.append(td1);
        newTr.append(td2);

        newTr.append('<td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td');
        $('table.item-export tbody').append(newTr);
        uniqueArray++;
        refreshAll();
    };
    var refreshAll = () => {
        isNew = false;
        var trBar = $('tr.main');
        trBar.find('td:first > input').val("");
        $('#name_step').val('');


    };
    var deleteTrItem = (trItem) => {
        var current = $(trItem).parent().parent();
        totalPrice -= current.find('td:nth-child(4) > input').val() * current.find('td:nth-child(5)').text().replace(/\,/g, '');
        $(trItem).parent().parent().remove();
        total--;
        refreshTotal();
    };

    $('.client-form').on('submit', (e)=>{
        if($('input.error').length > 0) {
            e.preventDefault();
            alert_float('danger', 'Giá trị không hợp lệ!');    
        }
        
    });
</script>















    <script>
        _validate_form($('.sales-form'),{code:'required',date:'required',customer_id:'required'});

        var _itemList = <?php echo json_encode($items);?>;
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

        var _findItem = (id) => {
            var _itemResult;
            $.each(_itemList, (index,value) => {
                if(value.id == id) {
                _itemResult = value;
                return false;
            }
        });
            return _itemResult;
        };
        var _total = <?php echo $i ?>;
        var _totalPrice = <?php echo $totalPrice ?>;
        var _uniqueArray = <?php echo $i ?>;
        var _isNew = false;
        var _createTrItem = () => {
//            if(!_isNew) return;
            if( $('table._item-export tbody tr:gt(0)').find('input[value=' + $('tr._main').find('td:nth-child(1) > input').val() + ']').length ) {
                $('table._item-export tbody tr:gt(0)').find('input[value=' + $('tr._main').find('td:nth-child(1) > input').val() + ']').parent().find('td:nth-child(2) > input').focus();
                alert_float('danger', "Sản phẩm này đã được thêm, vui lòng lòng kiểm tra lại!");
                return;
            }
            if($('tr.main').find('td:nth-child(4) > input').val() > $('tr.main #select_warehouse option:selected').data('store')) {
                alert_float('danger', 'Kho ' + $('tr.main #select_warehouse option:selected').text() + '. Bạn đã nhập ' + $('tr.main').find('td:nth-child(4) > input').val() + ' là quá số lượng cho phép.');
                return;
            }
            var newTr = $('<tr class="_sortable _item"></tr>');

            var td1 = $('<td><input type="hidden" name="_items[' + uniqueArray + '][id]" value="" /></td>');
            var td2 = $('<td class="dragger"></td>');
            var td3 = $('<td></td>');
            var td4 = $('<td><input style="width: 100px" class="_mainQuantity form-control" type="number" name="_items[' + uniqueArray + '][quantity]" value="" /></td>');
            var td5 = $('<td></td>');
            var td6 = $('<td></td>');
            var td7 = $('<td></td>');

            td1.find('input').val($('tr._main').find('td:nth-child(1) > input').val());
            td2.text($('tr._main').find('td:nth-child(2)').text());
            td3.text($('tr._main').find('td:nth-child(3)').text());
            td4.find('input').val($('tr._main').find('td:nth-child(4) > input').val());

            td5.text( $('tr._main').find('td:nth-child(5)').text());
            td6.text( $('tr._main').find('td:nth-child(6)').text());
            td7.text($('tr._main').find('td:nth-child(7)').text());
            newTr.append(td1);
            newTr.append(td2);
            newTr.append(td3);
            newTr.append(td4);
            newTr.append(td5);
            newTr.append(td6);
            newTr.append(td7);

            newTr.append('<td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td');
            $('table._item-export tbody').append(newTr);
            _total++;
            _totalPrice += $('tr._main').find('td:nth-child(4) > input').val() * $('tr._main').find('td:nth-child(5)').text().replace(/\+/g, ' ');
           _uniqueArray++;
            _refreshTotal();
            get_total();
        };
        var _refreshAll = () => {
            isNew = false;
//            $('#btnAdd').hide();
            $('#custom_item_select').val('');
            $('#custom_item_select').selectpicker('refresh');
            var trBar = $('tr._main');

            trBar.find('td:first > input').val("");
            // trBar.find('td:nth-child(1) > input').val('');
            trBar.find('td:nth-child(2)').text("<?=_l('item_name')?>");
            trBar.find('td:nth-child(3)').text("<?=_l('item_unit')?>");
            trBar.find('td:nth-child(4) > input').val('1');
            trBar.find('td:nth-child(5)').text("<?=_l('item_price')?>");
            trBar.find('td:nth-child(6)').text(0);
            trBar.find('td:nth-child(7)').text(0);
        };
        var _deleteTrItem = (trItem) => {
            var _current = $(trItem).parent().parent();
            _totalPrice -= _current.find('td:nth-child(4) > input').val() * _current.find('td:nth-child(5)').text().replace(/\,/g, '');
            $(trItem).parent().parent().remove();
            _total--;
            _refreshTotal();
        };
        var _refreshTotal = () => {
            $('.total').text(formatNumber(_total));
            var _items = $('table.item-export tbody tr:gt(0)');
            _totalPrice = 0;
            $.each(_items, (index,value)=>{
                _totalPrice += parseFloat($(value).find('td:nth-child(6)').text().replace(/\,/g, ''))+parseFloat($(value).find('td:nth-child(7)').text().replace(/\,/g, ''));
            // *
        });
            $('.totalPrice').text(formatNumber(_totalPrice));
        };
        $('#custom_item_select').change((e)=>{
            var id = $(e.currentTarget).val();
            jQuery.ajax({
                type: "post",
                dataType: "json",
                url: "<?=admin_url()?>campaign/get_items/"+id,
                data: "",
                cache: false,
                success: function (itemFound) {
                    console.log(itemFound)
                    $('#select_kindof_warehouse').val('');
                    $('#select_kindof_warehouse').selectpicker('refresh');
                    var _warehouse_id = $('#select_warehouse');
                    _warehouse_id.find('option:gt(0)').remove();
                    _warehouse_id.selectpicker('refresh');

                    if (typeof(itemFound) != 'undefined') {
                        var trBar = $('tr.main._main');

                        trBar.find('td:first > input').val(itemFound.id);
                        trBar.find('td:nth-child(2)').text(itemFound.name + ' (' + itemFound.prefix + itemFound.code + ')');
                        trBar.find('td:nth-child(3)').text(itemFound.unit_name);
                        trBar.find('td:nth-child(3) > input').val(itemFound.unit);
                        trBar.find('td:nth-child(4) > input').val(1);
                        trBar.find('td:nth-child(5)').text(formatNumber(parseFloat(itemFound.price)));
                        trBar.find('td:nth-child(6)').text(formatNumber(parseFloat(itemFound.price * 1)));
                        trBar.find('td:nth-child(7)').text(formatNumber(parseFloat(itemFound.price)));
                        isNew = true;
                        $('#_btnAdd').show();
                    }
//                    else {
//                        _isNew = false;
//                        $('#_btnAdd').hide();
//                    }
                }
            });
        });

        $('#select_warehouse').on('change', (e)=>{
            if($(e.currentTarget).val() != '') {
            $(e.currentTarget).parents('tr').find('input._mainQuantity').attr('data-store', $(e.currentTarget).find('option:selected').data('store'));
        }
        });
        $(document).on('keyup', '._mainQuantity', (e)=>{
            var currentQuantityInput = $(e.currentTarget);
        let elementToCompare;
        if(typeof(currentQuantityInput.attr('data-store')) == 'undefined' )
            elementToCompare = currentQuantityInput.parents('tr').find('input:last');
        else
            elementToCompare = currentQuantityInput;
        // console.log(elementToCompare)
        if(parseInt(currentQuantityInput.val()) > parseInt(elementToCompare.attr('data-store'))){
            currentQuantityInput.attr("style", "width: 100px;border: 1px solid red !important");
            currentQuantityInput.attr('data-toggle', 'tooltip');
            currentQuantityInput.attr('data-trigger', 'manual');
            currentQuantityInput.attr('title', 'Số lượng vượt mức cho phép!');
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

        var Gia = currentQuantityInput.parent().find(' +td+td');
        var GiaTri = Gia.find(' +td');
        GiaTri.text(formatNumber(Gia.text().replace(/\,/g, '') * currentQuantityInput.val()) );
        _refreshTotal();
        });
        $('#select_kindof_warehouse').change(function(e){
            var warehouse_type = $(e.currentTarget).val();
            var product = $(e.currentTarget).parents('tr').find('td:first input');
            if(warehouse_type != '' && product.val() != '') {
                loadWarehouses(warehouse_type,product.val());
            }
        });
        $('#warehouse_type').change(function(e){
            var warehouse_type = $(e.currentTarget).val();
            if(warehouse_type != '') {
                getWarehouses(warehouse_type);
            }
        });

        function _loadProductsInWarehouse(warehouse_id){
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

        });

        $(document).on('keyup', '._mainQuantity,.mainQuantity', (e)=>{
            let total=0;
            var colum = $('tr._sortable');
            $.each(colum, function( index, value ) {
                total+=parseFloat($(value).find('td:nth-child(7)').text().replace(/\,/g, ''));
            })
            $('.totalPrice').html(formatNumber(total));
        })
        function get_total()
        {
            let total=0;
            var colum = $('tr._sortable');
            $.each(colum, function( index, value ) {
                total+=parseFloat($(value).find('td:nth-child(7)').text().replace(/\,/g, ''));
            })
            $('.totalPrice').html(formatNumber(total));
        }
        function format_Number(nStr, decSeperate, groupSeperate) {
            //decSeperate= ki tu cach,groupSeperate= ki tu noi
            nStr += '';
            x = nStr.split(decSeperate);
            x1 = x[0];
            x2 = x.length > 1 ? ',' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
            }
            return x1 + x2;
        }
        function formart_num(id_input)
        {
            key="";
            money=$("#"+id_input).val().replace(/[^\d\.]/g, '');
            a=money.split(",");
            $.each(a , function (index, value){
                key=key+value;
            });
            $("#"+id_input).val(format_Number(key, ',', ','));
        }


    </script>
</body>
</html>