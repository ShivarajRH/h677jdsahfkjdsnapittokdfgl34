<div class="container" id="account_grn_present">
<h2>Account GRN<?=$this->uri->segment(3)?></h2>

<form method="post" id="cv_form" enctype="multipart/form-data">
<input type="hidden" name="grn" value="<?=$this->uri->segment(3)?>">
<div style="padding:20px 0px;">
<table class="datagrid" width="100%">
<thead>
<tr>
<th>Product</th>
<th>PO</th>
<th>Invoice Qty</th>
<th>Received Qty</th>
<th>MRP</th>
<th>DP Price</th>
<th>Margin</th>
<th>Scheme Discount</th>
<th>Discount Type</th>
<th>Purchase Price in GRN</th>
<th>Purchase Price</th>
<th>Invoice</th>
</tr>
</thead>
<tbody>
<?php foreach($items as $i){?>
<tr>
<td><?=$i['product_name']?><input type="hidden" name="items[]" value="<?=$i['id']?>"></td>
<td><a href="<?=site_url("admin/viewpo/{$i['po_id']}")?>">PO<?=$i['po_id']?></a></td>
<td><?=$i['invoice_qty']?></td>
<td class="qty"><?=$i['received_qty']?></td>
<td><input type="hidden" name="mrp[]" value="<?=$i['mrp']?>"><span class="mrp"><?=$i['mrp']?></span></td>
<td><input type="hidden" name="dp_price[]" value="<?=$i['dp_price']?>"><span class="dp_price"><?=$i['dp_price']?></span></td>
<td><nobr><input type="text" class="inp margin readonly" size=7 name="margin[]" value="<?=$i['margin']?>" readonly="readonly">%</nobr></td>
<td><input class="sdiscount inp" type="text" size=7 name="discount[]" value="<?=$i['scheme_discount_value']?>">%</td>
<td>
<select class="stype" name="type[]">
<option value="1">Percent</option>
<option value="2">Value</option>
</select>
</td>
<td><?=$i['purchase_price']?></td>
<td><span class="pprice"></span></td>
<td>
<select class="invoice" name="invoice[]">
<?php foreach($invoices as $inv){?>
<option value="<?=$inv['id']?>"><?=$inv['purchase_inv_no']?></option>
<?php }?>
</select>
</td>
</tr>
<?php }?>
</tbody>
</table>
</div>


<div>
<h3>Purchase Invoices Details</h3>
<table class="datagrid nofooter" width="100%">
<thead>
<tr>
<th>Purchase Invoice no</th>
<th>Invoice Date</th>
<th>Amount</th>
<th>Calc. Total</th>
<th>Scanned Copy (Image)</th>
</tr>
</thead>
<tbody>
<?php foreach($invoices as $inv){?>
<tr>
<td><?=$inv['purchase_inv_no']?><input type="hidden" name="inv_ids[]" value="<?=$inv['id']?>"></td>
<td><?=format_date($inv['purchase_inv_date'])?></td>
<td>Rs <b><input type="text" class="inp readonly" name="inv_amounts[]" readonly="readonly" value="<?=$inv['purchase_inv_value']?>" size=10></b></td>
<!--  <td>Rs <b><input type="text" class="inp" name="grn_validated_inv_amounts[]" value="<?=$inv['grn_validated_inv_value']?>" size=10></b></td>-->
<td><input type="hidden" name="grn_validated_inv_val" value="">Rs <b><span id="inv_<?=$inv['id']?>">0</span></b></td>

<td>
	<?php if(file_exists(ERP_PHYSICAL_IMAGES."invoices/{$inv['id']}.jpg")){ ?>
		<a href="<?=ERP_IMAGES_URL?>invoices/<?=$inv['id']?>.jpg" target="_blank"><img src="<?=ERP_IMAGES_URL?>invoices/<?=$inv['id']?>.jpg" height=50></a>
	<?php }else{?>
	<input type="file" name="scaninv_<?=$inv['id']?>">
	<?php }?>
</td>
</tr>
<?php }?>
</tbody>
</table>
</div>

<div style="padding:20px 0px;float: right;" >
<input type="submit" value="Update GRN" class="button button-rounded button-action button-small">
</div>

</form>

</div>

<script>
$(function(){
	
$('.stype').val("1").trigger('change');
calc_vvalue();
});
var vvalue=0;
function calc_vvalue()
{
	<?php foreach($invoices as $inv){?>
	totals[<?=$inv['id']?>]=0;
	<?php }?>
	vvalue=0;
	$(".sdiscount").each(function(){
		$p=$(this).parents("tr").get(0);
		mrp=parseInt($(".mrp",$p).html());
		dp_price=parseInt($(".dp_price",$p).html());
		
		
		
		stype=parseInt($(".stype",$p).val());
		sdiscount=parseFloat($(".sdiscount",$p).val());
		margin=parseFloat($(".margin",$p).val());
		
		if(dp_price*1 > 0)
		{
			
			
			price=dp_price-(dp_price*margin/100);
			if(stype==1)
				price=price-(dp_price*sdiscount/100);
			else
				price=price-sdiscount;
				
				
					
		}else
		{
			price=mrp-(mrp*margin/100);
			if(stype==1)
				price=price-(mrp*sdiscount/100);
			else
				price=price-sdiscount;	
		}
		
		if(isNaN(price))
			return;
		$('.pprice',$p).html(price);	
		qty=parseInt($(".qty",$p).html());
		inv=parseInt($(".invoice",$p).val());
		totals[inv]=totals[inv]+(price*qty);
	});
	$.each(totals,function(i,v){
		if(typeof(v)=="undefined")
			return;
		$("#inv_"+i).html(v);
		$('input[name="grn_validated_inv_val"]').val(v);
	});
}
var totals=[];
$('.leftcont').hide();
</script>
<style>
.readonly {
    background-color: #E6E6E6 !important;
    color: #808080 !important;
    font-size: 13px;
    width: 54px;
}	
	
</style>
<?php
