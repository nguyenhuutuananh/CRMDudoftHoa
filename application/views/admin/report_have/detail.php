<?php init_head(); ?>
<div id="wrapper" class="customer_profile">
 <div class="content">
   <div class="row">

  <div class="col-md-12">
   <div class="panel_s">
     <div class="panel-body">
      <div class="clearfix"></div>
          <h4 class="bold no-margin"><?php echo (isset($heading) ? $heading : ''); ?></h4>
  <hr class="no-mbot no-border" />
    <div class="row">
    <div class="additional"></div>
    <div class="col-md-12">
        <?php
         if(isset($report_have))
            {
                if($report_have->status==0)
                {
                    $type='warning';
                    $status='Chưa duyệt';
                }
                elseif($report_have->status==1)
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
                $status='Đề xuất mới';
            }

        ?>
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
                    <div class="pull-right">
                        <?php if( isset($report_have) ) { ?>
                        <a href="<?php echo admin_url('purchase_suggested/detail_pdf/' . $report_have->id . '?print=true') ?>" target="_blank" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="In" aria-describedby="tooltip652034"><i class="fa fa-print"></i></a>
                        <a href="<?php echo admin_url('purchase_suggested/detail_pdf/' . $report_have->id  ) ?>" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Xem PDF"><i class="fa fa-file-pdf-o"></i></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            
            <?php echo form_open_multipart($this->uri->uri_string(), array('class' => 'client-form', 'autocomplete' => 'off')); ?>
                <div class="row">
                  <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                      <?php
                      $receiver = (isset($report_have) ? $report_have->receiver : "");
                      echo  render_input('receiver', _l('__receiver'), $receiver);
                      ?>
                      <?php
                          $reason = (isset($report_have) ? $report_have->reason : "");
                          echo  render_input('reason', _l('reason')._l('_save'), $reason);
                      ?>
                    <?php
                        $default_date = ( isset($report_have) ? _d($report_have->date_create) : _d(date('Y-m-d')));
                        echo render_date_input( 'date_create', 'project_datecreated' , $default_date , 'date');
                    ?>
                    
                </div>
                
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                    <?php
                    $default_date = ( isset($report_have) ? _d($report_have->date_of_accounting) : _d(date('Y-m-d')));
                    echo render_date_input( 'date_of_accounting', 'date_of_accounting' , $default_date , 'date_of_accounting');
                    ?>
                    <?php
                    $default_date = ( isset($report_have) ? _d($report_have->day_vouchers) : _d(date('Y-m-d')));
                    echo render_date_input( 'day_vouchers', 'day_vouchers' , $default_date , 'day_vouchers');
                    ?>
                    <?php
                    $code_vouchers = (isset($report_have) ? $report_have->code_vouchers : $code_vouchers);
                    echo  render_input('code_vouchers', _l('code_noo'),$code_vouchers,'text',array('readonly'=>'readonly'));
                    ?>
                </div>
                

                <!-- Edited -->
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <!-- Cusstomize from invoice -->
                    <div class="panel-body mtop10">

                        <?php $readonly='';$display="";?>
                    <?php if(($report_haves->status!=0)&&isset($report_haves)){ $display='style="display: none;"'; $readonly='readonly';  }?>
                        <div class="table-responsive s_table">
                            <table class="table items item-export no-mtop">
                                <thead>
                                    <tr>
                                        <th><input type="hidden" id="itemID" value="" /></th>
                                        <th width="" class="text-left"><?php echo _l('Diển giải'); ?></th>
                                        <th width="" class="text-left"><?php echo _l('tk_no'); ?></th>
                                        <th width="" class="text-left"><?php echo _l('tk_co'); ?></th>
                                        <th width="" class="text-left"><?php echo _l('contract_'); ?></th>
                                        <th width="" class="text-left"><?php echo _l('client'); ?></th>
                                        <th width="" class="text-left"><?php echo _l('total_money'); ?></th>
                                        <th></th>

                                    </tr>
                                </thead>
                                
                                <tbody>
                                    <tr class="main" <?=$display?> >
                                        <td><input type="hidden" id="itemID" value="" /></td>
                                       <td style="padding-top: 8px;"><div class="form-group"><input type="text" id="note" class="form-control" value=""></div></td>
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
                                           <select class="selectpicker" id="contracts" data-width="100%" data-none-selected-text="<?php echo _l('contracts'); ?>">

                                               <option></option>
                                               <?php if($contract){?>
                                                   <?php foreach($contract as $con){?>
                                                       <option value="<?=$con['id']?>" data-subtext=""><?=$con['fullcode']?></option>
                                                   <?php }?>
                                               <?php }?>
                                           </select>
                                       </td>
                                       <td>
                                            <div class="form-group">
                                                <input type="text" readonly id="client" class="form-control" value="">
                                            </div>
                                       </td>
                                        <td>
                                           <div class="form-group">
                                               <input type="text" id="subtotal" class="form-control" value="">
                                           </div>
                                       </td>
                                       <td>
                                           <button style="" id="btnAdd" type="button" onclick="createTrItem(); return false;" class="btn pull-right btn-info"><i class="fa fa-check"></i></button>
                                       </td>
                                    </tr>
                                    <?php
                                    $i=0;
                                    $totalPrice=0;
                                    $subtotal=0;
                                    $subdiscount=0;

                                    if(isset($report_haves) && count($report_haves) > 0) {
                                        
                                        foreach($report_haves as $value) {
                                        ?>
                                    <tr class="sortable item">
                                        <td  class="dragger">
                                            <input type="hidden" name="item[<?php echo $i; ?>][id]" value="<?php echo $value['id']; ?>">
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input <?=$readonly?> type="text" name="item[<?php echo $i ?>][note]" id="note" class="form-control" value="<?php echo $value['note']; ?>">
                                            </div>
                                        </td>
                                        <td>
                                            <select class="selectpicker" name="item[<?php echo $i ?>][tk_no]" data-width="100%" data-none-selected-text="<?php  echo _l('tk_no')?>">
                                                <?php if($tk_no){?>
                                                    <option></option>
                                                    <?php foreach($tk_no as $rom){?>
                                                        <?php $selected="";?>
                                                        <?php if($rom['idAccount']==$value['tk_no']){$selected='selected';}?>
                                                        <option value="<?=$rom['idAccount']?>" data-subtext="<?=$rom['accountName']?>" <?=$selected?>><?=$rom['accountCode']?></option>
                                                    <?php }?>
                                                <?php }?>

                                            </select>
                                        </td>
                                        <td>
                                            <select class="selectpicker" name="item[<?php echo $i ?>][tk_co]" data-width="100%" data-none-selected-text="<?php  echo _l('tk_co')?>">
                                                <?php if($tk_co){?>
                                                    <option></option>
                                                    <?php foreach($tk_co as $rom){?>
                                                        <?php $selected="";?>
                                                        <?php if($rom['idAccount']==$value['tk_co']){$selected='selected';}?>
                                                        <option value="<?=$rom['idAccount']?>" data-subtext="<?=$rom['accountName']?>" <?=$selected?>><?=$rom['accountCode']?></option>
                                                    <?php }?>
                                                <?php }?>

                                            </select>
                                        </td>

                                        <td>
                                            <select class="selectpicker" name="item[<?php echo $i ?>][contract]" data-width="100%" <?=$disabled?> data-none-selected-text="<?php echo _l('contract'); ?>">
                                                <?php if($contract){?>
                                                    <option></option>
                                                    <?php foreach($contract as $rom){?>
                                                        <?php $selected="";?>
                                                        <?php if($rom['id']==$value['contract']){$selected='selected';}?>
                                                        <option value="<?=$rom['id']?>" <?=$selected?>><?=$rom['fullcode']?></option>
                                                    <?php }?>
                                                <?php }?>

                                            </select>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <?php $_client= $this->report_have_model->get_client_contract($value['contract']);?>
                                                <input readonly type="text"  class="form-control" value="<?php if(isset($_client)) echo $_client->company ?>">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input <?=$readonly?> type="text" name="item[<?php echo $i ?>][subtotal]" class="form-control _subtotal" value="<?=number_format($value['subtotal'])?>">
                                                <?php $subtotal +=$value['subtotal'] ?>
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
                        <div class="col-md-8 col-md-offset-4">
                            <table class="table text-right">
                                <tbody>
                                    <tr>
                                        <td><span class="bold"><?php echo _l('total_invoices'); ?> :</span>
                                        </td>
                                        <td class="_count">
                                            <?php echo $i ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="bold"><?php echo _l('subtotal'); ?> :</span>
                                        </td>
                                        <td class="subtotal">
                                            <?php echo number_format($subtotal); ?>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- End Customize from invoice -->
                </div>
                <!-- End edited -->
                
                <?php if(isset($report_haves) && $report_haves->status == 0 || !isset($report_haves)) { ?>
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
    var itemList = <?php echo json_encode($products);?>;

    var item_report_haves_contract = <?php echo json_encode($report_haves_contract);?>;
    console.log(item_report_haves_contract);
    var itemList_report_haves_contract_purchase = <?php echo json_encode($report_haves_contract_purchase);?>;

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
            $.each(item_report_haves_contract, (index,value) => {
                console.log(value.sum_contract);
                if(id == index) {
                    itemResult = value;
                    return false;
                }
            });
        }
        else
        {
            $.each(itemList_report_haves_contract_purchase, (index,value) => {
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

//        if(!isNew) return;
        if(!$('tr.main #tk_no option:selected').length || $('tr.main #tk_no option:selected').val() == '') {
            alert_float('danger', "Vui lòng chọn tài khoản nợ!");
            return;
        }
        if(!$('tr.main #tk_co option:selected').length || $('tr.main #tk_co option:selected').val() == '') {
            alert_float('danger', "Vui lòng chọn tài khoản có!");
            return;
        }
        if($('tr.main').find('td:nth-child(4) > input').val() > $('tr.main #select_warehouse option:selected').data('store')) {
            alert_float('danger', 'Kho ' + $('tr.main #select_warehouse option:selected').text() + '. Bạn đã nhập ' + $('tr.main').find('td:nth-child(4) > input').val() + ' là quá số lượng cho phép.');
            return;
        }
        var newTr = $('<tr class="sortable item"></tr>');

        var td1 = $('<td><input type="hidden" name="items[' + uniqueArray + '][id]" value="" /></td>');
        var td2 = $('<td><div class="form-group"><input type="text" name="items[' + uniqueArray + '][note]" value="" class="form-control" /></div></td>');
        var td3 = $('<td></td>');
        var td4 = $('<td></td>');
        var td5 = $('<td></td>');
        var td6 = $('<td><div class="form-group"><input class="form-control" readonly type="text" value="" /></div></td>');
        var td7 = $('<td><div class="form-group"><input class="_subtotal form-control" type="number" name="items[' + uniqueArray + '][subtotal]" value="" /></div></td>');
        var td8 = $('<td></td>');

        td1.find('input').val($('tr.main').find('td:nth-child(1) input').val());
        td2.find('input').val($('tr.main').find('td:nth-child(2) input').val());

//        td3.text($('tr.main').find('td:nth-child(3) option:selected').text())
        let tk_no = $('tr.main').find('td:nth-child(3)').find('select').clone();
        tk_no.attr('name', 'items[' + uniqueArray + '][tk_no]');
        tk_no.removeAttr('id').val($('tr.main').find('td:nth-child(3)').find('select').selectpicker('val'));
        td3.append(tk_no);

//        td4.find('input').val($('tr.main').find('td:nth-child(4) option:selected').val());

        let tk_co = $('tr.main').find('td:nth-child(4)').find('select').clone();
        tk_co.attr('name', 'items[' + uniqueArray + '][tk_co]');
        tk_co.removeAttr('id').val($('tr.main').find('td:nth-child(4)').find('select').selectpicker('val'));
        td4.append(tk_co);

//		td5.find('input').val($('tr.main').find('td:nth-child(5) option:selected').val());
        let contract = $('tr.main').find('td:nth-child(5)').find('select').clone();
        contract.attr('name', 'items[' + uniqueArray + '][contract]');
        contract.attr('dis', 'items[' + uniqueArray + '][contract]');
        contract.removeAttr('id').val($('tr.main').find('td:nth-child(5)').find('select').selectpicker('val'));
        td5.append(contract);
        td6.find('input').val($('tr.main').find('td:nth-child(6) input').val());

        td7.find('input').val($('tr.main').find('td:nth-child(7) input').val());
        td8.find('input').val($('tr.main').find('td:nth-child(8) input').val());

		newTr.append(td1);
        newTr.append(td2);
        newTr.append(td3);
        newTr.append(td4);
        newTr.append(td5);
        newTr.append(td6);
        newTr.append(td7);
        newTr.append(td8);

        newTr.append('<td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td');
        $('table.item-export tbody').append(newTr);
        total++;
        totalPrice += $('tr.main').find('td:nth-child(4) > input').val() * $('tr.main').find('td:nth-child(5)').text().replace(/\+/g, ' ');
        uniqueArray++;
        refreshTotal();
        refreshAll();
    };
    var refreshAll = () => {
        isNew = false;
        var trBar = $('tr.main');
        trBar.find('td:first > input').val("");
        trBar.find('td:nth-child(1) input').val('');
        trBar.find('td:nth-child(2) input').val('');
        trBar.find('td:nth-child(5) select').val('').selectpicker('refresh');
        trBar.find('td:nth-child(6) select').val('').selectpicker('refresh');
        trBar.find('td:nth-child(7) input').val('').selectpicker('refresh');
        trBar.find('td:nth-child(8) input').val('');
        trBar.find('td:nth-child(9) input').val('');
        trBar.find('td:nth-child(10) input').val('').selectpicker('refresh');


    };
    var deleteTrItem = (trItem) => {
        var current = $(trItem).parent().parent();
        totalPrice -= current.find('td:nth-child(4) > input').val() * current.find('td:nth-child(5)').text().replace(/\,/g, '');
        $(trItem).parent().parent().remove();
        total--;
        refreshTotal();
    };

    var refreshTotal =()=>{

        var tr_subtotal = $('._subtotal');
        var _count = 0;
        var _subtotal=0;
        $.each($(tr_subtotal), function(st,s){
            _subtotal+= parseInt(s.value.replace(/\,|%/g, ''));
            _count++;
        })
        $('.subtotal').html(formatNumber(_subtotal));
        $('._count').html(formatNumber(_count));
        $('.selectpicker').selectpicker('refresh');
    }
    $('#custom_item_select').change((e)=>{
        var id = $(e.currentTarget).val();
        var itemFound = findItem(id);

        $('#select_kindof_warehouse').val('');
        $('#select_kindof_warehouse').selectpicker('refresh');
		$('#select_currency').find('option:first').attr('selected', 'selected');
        $('#select_currency').selectpicker('refresh');
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

            trBar.find('td:nth-child(5)');
            trBar.find('td:nth-child(6)');
            trBar.find('td:nth-child(7)');
            trBar.find('td:nth-child(8)');
            trBar.find('td:nth-child(10)').text(itemFound.tax_rate + " %");
            trBar.find('td:nth-child(11)');
            isNew = true;
            $('#btnAdd').show();
        }
        else {
            isNew = false;
            $('#btnAdd').hide();
        }
    });
    $('#contracts').change((e)=>{
        var id = $(e.currentTarget).val();
        jQuery.ajax({
            type: "post",
            url:"<?=admin_url()?>report_have/get_client",
            data: {id:id},
            cache: false,
            success: function (result){
                result = JSON.parse(result);
                $('#client').val(result.company);
                $('#subtotal').val(result.contract_value);
            }
        });
    });

    $('#contract').change((e)=>{
        var trBar = $('tr.main');
        $('#purchase_contracts').val('');
        $('#purchase_contracts').selectpicker('refresh');
        var id = $(e.currentTarget).val();
        var itemFound = findItem(id,1);
        trBar.find('td:nth-child(7) select').val(itemFound.currency).selectpicker('refresh');
        trBar.find('td:nth-child(8) input').val(itemFound.sum_contract);
    });
    $('#select_warehouse').on('change', (e)=>{
        if($(e.currentTarget).val() != '') {
            $(e.currentTarget).parents('tr').find('input.mainQuantity').attr('data-store', $(e.currentTarget).find('option:selected').data('store'));
        }
    });
    $( "#id_client" ).change(function() {
        $('#invoices').html('').selectpicker('refresh');
        var lengthcode=6;
        var id_client=$('#id_client').val();
        jQuery.ajax({
            type: "post",
            url:"<?=admin_url()?>report_haves/get_invoices",
            data: {id_client:id_client},
            cache: false,
            success: function (data) {
                data = JSON.parse(data);
                $('#invoices').append('<option></option>');
                $.each(data, function (key, value) {
                    var lengthnumber=lengthcode-value.number.length;
                    var allnumber="";
                    if(lengthnumber>0)
                    {
                        for(var i=0;i<lengthnumber;i++)
                        {
                            allnumber=allnumber+'0';
                        }
                    }
                    $('#invoices').append('<option value="'+value.id+'">'+value.prefix+allnumber+value.number+'</option>');
                })
                $('#invoices').selectpicker('refresh');
            }
        });
        return false;
    });
    $( "#invoices" ).change(function() {
        var lengthcode=6;
        var invoices=$('#invoices').val();
        jQuery.ajax({
            type: "post",
            url:"<?=admin_url()?>report_haves/get_invoices_id",
            data: {invoices:invoices},
            cache: false,
            success: function (result){
            result = JSON.parse(result);
                console.log(result);
                $('#total').val(result.total);
                $('#discount').val(result.subtotal-result.total);
                $('#subtotal').val(result.subtotal);
                if(result.currency==0)
                {
                    $('#currencies').val('3').selectpicker('refresh');
                }
                else
                {
                    $('#currencies').val(result.currency).selectpicker('refresh');
                }
            }
        });
        return false;
    });

	var calculateTotal = (currentInput) => {
		currentInput = $(currentInput);		
		let soLuong = currentInput.parents('tr').find('.mainQuantity'); 
		let gia = currentInput.parents('tr').find('.mainPriceBuy'); 
		let tdTong = gia.parent().find(' + td');
		tdTong.text( formatNumber( String(soLuong.val()).replace(/\,/g, '') * String(gia.val()).replace(/\,/g, '')) );
        let tdtax = gia.parent().find(' + td + td');
        let tdmoneytax = gia.parent().find(' + td + td +td');
        let tong = String(soLuong.val()).replace(/\,/g, '') * String(gia.val()).replace(/\,/g, '');
        let vartax=$(tdtax).html().replace(/\,|%/g, '');
        tdTong.text(formatNumber( tong ) );
        tdmoneytax.text(formatNumber( (tong*vartax) / 100 ));
        refreshTotal();
	};
	$(document).on('keyup','._subtotal', (e)=>{
        refreshTotal();
	});
    $(document).on('keyup', '.mainQuantity', (e)=>{
        var currentQuantityInput = $(e.currentTarget);
        let elementToCompare;
        if(typeof(currentQuantityInput.attr('data-store')) == 'undefined' )
            elementToCompare = currentQuantityInput.parents('tr').find('input[data-store]');
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
    $('#select_kindof_warehouse').change(function(e){
        var warehouse_type = $(e.currentTarget).val();
        var product = $(e.currentTarget).parents('tr').find('td:first input');
        if(warehouse_type != '' && product.val() != '') {
            loadWarehouses(warehouse_type,product.val()); 
        }
    });
    $('._total,._discount,._subtotal').keyup(function(e){

        refreshTotal();
    })

    function loadWarehouses(warehouse_type, filter_by_product,default_value=''){
        var warehouse_id=$('#select_warehouse');
        warehouse_id.find('option:gt(0)').remove();
        warehouse_id.selectpicker('refresh');
        if(warehouse_id.length) {
            $.ajax({
                url : admin_url + 'warehouses/getWarehouses/' + warehouse_type + '/' + filter_by_product + '/true',
                dataType : 'json',
            })
            .done(function(data){
				console.log(data);
                $.each(data, function(key,value){
                    var stringSelected = "";
                    if(value.warehouseid == default_value) {
                        stringSelected = ' selected="selected"';
                    }
					warehouse_id.append('<option data-store="'+(value.items[0].maximum_quantity - value.items[0].product_quantity)+'" value="' + value.warehouseid + '"'+stringSelected+'>' + value.warehouse + '(nhập tối đa '+(value.items[0].maximum_quantity - value.items[0].product_quantity)+')</option>');
                });
                warehouse_id.selectpicker('refresh');
            });
        }
    }
    $('.client-form').on('submit', (e)=>{
        if($('input.error').length > 0) {
            e.preventDefault();
            alert_float('danger', 'Giá trị không hợp lệ!');    
        }
        
    });
</script>
</body>
</html>