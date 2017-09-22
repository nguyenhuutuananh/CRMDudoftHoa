<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php
			echo form_open($this->uri->uri_string(),array('id'=>'invoice-form','class'=>'_transaction_form invoice-form'));
			if(isset($invoice)){
				echo form_hidden('isedit');
			}
			?>
			<div class="col-md-12">
				<?php $this->load->view('admin/invoices/invoice_template'); ?>
			</div>
			<?php echo form_close(); ?>
			<?php $this->load->view('admin/invoice_items/item'); ?>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<script>
	var uniqueArray=<?=count($invoice->items)?>;
	var total=0; 
	$(function(){
		validate_invoice_form();
	    // Init accountacy currency symbol
	    init_currency_symbol();
	});
	$('#clientid').change(function(){
		var customer_id=$(this).val();
		$('table.invoice-items-table tbody tr').remove();
		loadAllSalesByCustomerID(customer_id);

	});
	function loadAllSalesByCustomerID(customer_id){
        var invoice_item=$('#invoice_item_select');
        invoice_item.find('option:gt(0)').remove();
        invoice_item.selectpicker('refresh');
        if(invoice_item.length) {
            $.ajax({
                url : admin_url + 'sales/getAllSalesByCustomerID/' + customer_id,
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

    function formatNumber(nStr, decSeperate=".", groupSeperate=",") {
    	if(typeof(nStr)==NaN)
    		nStr=0;
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

    function loadAllItemsBySaleID(sale_id){
        $('table.invoice-items-table tbody tr').remove();
        if(sale_id) {
            $.ajax({
                url : admin_url + 'sales/getAllItemsBySaleID/' + sale_id,
                dataType : 'json',
            })
            .done(function(data){ 
            
            	total=0;
                $.each(data, function(key,value){
                	var newTr = $('<tr class="item"></tr>');
                    var td1 = $('<td class="dragger"><input type="hidden" name="items[' + uniqueArray + '][id]" value="'+value.product_id+'" /></td>');
			        var td2 = $('<td>'+value.product_name+'</td>');
			        var td3 = $('<td>'+value.code+'</td>');
			        var td4 = $('<td><input style="width: 100px" class="mainQuantity" type="number" name="items[' + uniqueArray + '][quantity]" value="'+formatNumber(value.quantity)+'" readonly/></td>');
			        var td5 = $('<td>'+formatNumber(value.unit_cost)+'</td>');
			        var td6 = $('<td>'+formatNumber(value.sub_total)+'</td>');
			        var td7 = $('<td>'+formatNumber(value.tax)+'</td>');
			        var amount=parseFloat(value.amount);
                    var td8 = $('<td><input type="hidden" class="discount" name="items[' + uniqueArray + '][discount]" value="'+0+'" /><input type="number" value="0" class="form-control pull-left discount_percent" min="0" max="100" name="items[' + uniqueArray + '][discount_percent]"></td>');
			        if(isNaN(parseFloat(value.amount)))
			        {
			        	amount=0;
			        }
			        
                    var td9 = $('<td>'+formatNumber(amount)+'</td>');

			        newTr.append(td1);
			        newTr.append(td2);
			        newTr.append(td3);
			        newTr.append(td4);
			        newTr.append(td5);
			        newTr.append(td6);
			        newTr.append(td7);
			        newTr.append(td8);
                    newTr.append(td9);
			        $('table.invoice-items-table tbody').append(newTr);
			        uniqueArray++;
			        total+=amount;
			        
                });
                Total();
            });
        }
    }

    $("body").on("change", 'input.discount_percent', function() {
        var inputdiscount=$(this).parent().find('input[type="hidden"]');
        var amount=$(this).parent().find(' + td').text().replace(/\,/g, '');
        var discount=parseFloat(amount)*$(this).val()/100;
        inputdiscount.val(discount);

        Total();
    });



    function Total()
    {
        rows = $('.table.invoice-items-table tbody tr.item');
        var total_discount=0;
        $.each(rows, function() {
            total_discount+=parseFloat($(this).find('input.discount').val());
    });
            $('.discount_percent').text(formatNumber(total_discount))
    	var grand_total=0;
    	$('.subtotal').text(formatNumber(total));
    	
    	grand_total=total-total_discount;
    	$('.total').text(formatNumber(grand_total))
    }

    $('#invoice_item_select').change(function(){
      var sale_id=($(this).val());
      loadAllItemsBySaleID(sale_id);
    });
</script>
</body>
</html>

