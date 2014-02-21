<?php
	$user = $this->erpm->auth(); 
	$po_ven_name =$po['vendor_name'];
?>
<div class="container">
<h2>Purchase Order : <?=$po['po_id']?></h2>
<table class="datagrid" style="float:left">
<tr><td>Vendor :</td><td><a href="<?=site_url("admin/vendor/{$po['vendor_id']}")?>"><?=$po['vendor_name']?></a></td></tr>
<tr><td>Total Value :</td><td>Rs <b><?=format_price($po['total_value'])?></b></td></tr>
<tr><td>Remarks :</td><td><?=$po['remarks']?></td></tr>

<tr><td>Created on :</td><td><?=date("d/m/Y g:ia",strtotime($po['created_on']))?></td></tr>
<tr><td>Created By :</td><td><?=$this->db->query("select username from king_admin where id = ? ",$po['created_by'])->row()->username;?></td></tr>
<tr>
<td>Date of Delivery :</td>
<?php if(!$po['date_of_delivery']!=null){?>
<form method="post" action="<?php echo site_url("admin/updatedeliverydate/{$po['po_id']}")?>" >
<td>
<input type="text" name="po_deliverydate" id="po_deliverydate" value="" >
<input type="submit" value="save" >
</td>
</form>
<?php }else{?>
<td><?php echo format_datetime($po['date_of_delivery']);?></td>
<?php }?>
</tr>
<!--  <td style="font-weight:bold;<?php echo (strtotime($po['date_of_delivery']) < time())?'color:#cd0000;':'' ?> "><?=date("d/m/Y g:ia",strtotime($po['date_of_delivery']))?></td>-->

<tr><td>Status :</td>
<td>

<?php 
switch($po['po_status']){
	case 0: echo 'Open'; break;
	case 1: echo 'Partially Received'; break;
	case 2: echo 'Complete'; break;
	case 3: echo 'Cancelled';
}

?></td></tr>

<tr>
<td colspan=2 align="right">
<?php if($po['po_status']!="2" && $po['po_status']!="3"){?>
<input onclick='closepo()' type="button" value="Close PO">
<?php } ?>
</td>
</tr>
</table>

<div style="float:left;margin-left:20px;">
<h4 style="margin:0px;">Stock Intakes</h4>
<table class="datagrid">
<thead>
<tr><th>Stock Intake No</th><th>Status</th><th>Total Invoice Value</th></tr>
</thead>
<tbody>
<?php foreach($grns as $grn) {?>
<tr>
<td><a href="<?=site_url("admin/viewgrn/{$grn['grn_id']}")?>" class="link">GRN<?=$grn['grn_id']?></a></td>
<td><?php switch($grn['payment_status']){
	case 0: echo "Unaccounted";?><br><a href="<?=site_url("admin/account_grn/{$grn['grn_id']}")?>">account</a> <?php break;
	case 1: echo "Accounted, ready for payment";?><br><a href="<?=site_url("admin/create_voucher")?>">make payment</a> <?php break;
	case 2: echo "Payment made";break;
}?></td>
<td><?=$this->db->query("select sum(purchase_inv_value) as v from `t_grn_invoice_link` where grn_id=?",$grn['grn_id'])->row()->v?></td>
</tr>
<?php } if(empty($grns)){?><tr>
<td colspan="100%">No Stock Intakes made</td>
</tr>
<?php }?>
</tbody>
</table>
</div>

<div style="float:left;margin-left:20px;">
<div>
<h4 style="margin:0px;">Vouchers</h4>
<table class="datagrid">
<thead><tr><th>Voucher ID</th><th>Voucher Value</th><th>Amount paid for this PO</th><th>Created On</th></tr></thead>
<tbody>
<?php foreach($vouchers as $v){?>
<tr>
<td><a class="link" href="<?=site_url("admin/voucher/{$v['voucher_id']}")?>"><?=$v['voucher_id']?></a></td>
<td><?=$v['voucher_value']?></td>
<td><?=$v['adjusted_amount']?></td>
<td><?=$v['created_on']?></td>
</tr>
<?php }if(empty($vouchers)){?><tr><td colspan="100%">No Payments made</td></tr><?php }?>
</tbody>
</table>
</div>
</div>

<div class="clear"></div>


<div style="padding:20px 0px;">
<h4 style="margin-bottom:3px;">PO Product list</h4>
<table id="po_prod_list" class="datagrid nofooter" style="width:100%">
<thead>
<tr>
<th>Sno</th>
<th width="250" style="text-align:left">Product Name</th>
<th>Available Qty</th>
<th style="text-align: left" width="50">Last <br> 30 Days Sales</th>
<th style="text-align: left" width="50">Required <br> Qty</th>
<th>PO <br> Order <br> Qty</th>
<th>Received <br> Qty</th>
<th class="hideinprint">MRP</th>
<th class="hideinprint">DP Price</th>
<th class="hideinprint">Margin</th>
<th class="hideinprint">Scheme Discount</th>
<th class="hideinprint">Purchase Price</th>
<th class="hideinprint hideinprint1">FOC</th>
<th class="hideinprint hideinprint1">Has Offer</th>
<th class="hideinprint hideinprint1">Note </th>
</tr>
</thead>
<tbody>
<?php $sno=1; 

	$total_item_qty = 0;
	$total_item_rcvd_qty = 0;
	$total_pprice = 0;
foreach($items as $i){
	
	$i['sales_30days']=$this->db->query("select ifnull(sum(o.quantity*l.qty),0) as s from m_product_deal_link l join king_orders o on o.itemid=l.itemid where l.product_id=? and o.time>".(time()-(24*60*60*30)).' and o.time < ?  ',array($i['product_id'],strtotime($po['created_on'])))->row()->s;
	$i['sales_30days'] += $this->db->query("select ifnull(sum(o.quantity*l.qty),0) as s from m_product_group_deal_link l join king_orders o on o.itemid=l.itemid join products_group_orders pgo on pgo.order_id = o.id where pgo.product_id=? and o.time>".(time()-(24*60*60*30)).' and o.time < ?  ',array($i['product_id'],strtotime($po['created_on'])))->row()->s;
	
	$i['pen_ord_qty']=$this->db->query("select ifnull(sum(o.quantity*l.qty),0) as s from m_product_deal_link l join king_orders o on o.itemid=l.itemid where l.product_id=? and o.status = 0 and o.time < ? ",array($i['product_id'],strtotime($po['created_on'])))->row()->s;

	$i['cur_avail_qty'] = $this->db->query("select current_stock from t_stock_update_log where product_id = ?  order by id desc limit 1 ",array($i['product_id']))->row()->current_stock;
	
	$total_item_qty += $i['order_qty'];
	$total_item_rcvd_qty += $i['received_qty'];

	$total_pprice += $i['order_qty']*$i['purchase_price'];
?>
<tr>
	<td><?=$sno++?></td>
	<td align="left"><a href="<?=site_url("admin/product/{$i['product_id']}")?>"><?=$i['product_name']?></a></td>
	<td align="right"><?=$i['cur_avail_qty']*1?></td>
	<td align="right"><?=$i['sales_30days']*1?></td>
	<td align="right"><?=$i['pen_ord_qty']*1?></td>
	<td align="right"><?=$i['order_qty']*1?></td>
	<td align="right"><?=$i['received_qty']?></td>
	<td class="hideinprint" align="right"><?=$i['mrp']?></td>
	<td class="hideinprint" align="right"><?=$i['dp_price']?></td>
	<td class="hideinprint" align="right"><?=$i['margin']?> <?=($i['scheme_discount_value']==1?'%':'')?></td>
	<td class="hideinprint" align="right"><?=$i['scheme_discount_value']?></td>
	<td class="hideinprint" align="right"><?=$i['purchase_price']?></td>
	<td class="hideinprint hideinprint1"><?=$i['is_foc']?"YES":"NO"?></td>
	<td class="hideinprint hideinprint1"><?=$i['has_offer']?"YES":"NO"?></td>
	<td	class="hideinprint hideinprint1"><?=$i['special_note']?></td>
</tr>
<?php }?>
	
	<tr class="tbl_stats_row">
		<td colspan="5" align="right">Total</td>
		<td align="right"><?php echo $total_item_qty; ?></td>
		<td align="right"><?php echo $total_item_rcvd_qty; ?></td>
		<td class="hideinprint" colspan="4">&nbsp;</td>
		<td class="hideinprint" align="right"><?php echo format_price($total_pprice,4); ?></td>
		<td colspan="3" class="hideinprint hideinprint1">&nbsp;</td>
	</tr>
</tbody>
</table>

<div style="text-align: right;padding-top:5px;">
	<a href="javascript:void(0)" class="button button-tiny button-info" onclick="print_poaccdoc()">Print Accounts Copy</a> 
	<a href="javascript:void(0)" class="button button-tiny button-info" onclick="print_podoc()">Print Vendor Copy</a>
</div>

</div>

</div>


<style>
#po_prod_list th{text-align:right;}
.tbl_stats_row td{background:#ffffD0 !important}
</style>


<script>
/*var button = $('<input type="button" value="save" id="po_deliverydate_button">');
$('#po_deliverydate_button').after(button);
if("#po_deliverydate_button").click(function(){
	var value = $(this).prev().val();
	 save(value);
});*/



function print_podoc()
{
	var html = '<div><style> body{font-size:12px;font-family:arial;} .hideinprint{display:none}</style> <h2 align="center">Purchase Order Product List</h2> <div> <b style="float:right">Printed By : <?php echo $user['username'];?> <br> Printed On : <?php echo format_datetime_ts(time());?>  </b> <b style="font-size:14px;">PO: #<?=$po['po_id']?></b><br><b style="font-size:14px;">PO Date:<?=date("d/m/Y g:ia",strtotime($po['created_on']))?></b><br><b style="font-size:14px;">Vendor: <?=$po_ven_name;?></b>  </div><table cellpadding=5 cellspacing=0 border=1 width="100%" style="font-size:12px;font-family:arial;">'+$('#po_prod_list').html()+'</table></div>';
		prw=window.open("",'');
		prw.document.write(html);
		prw.focus();
		prw.print();
}

function print_poaccdoc()
{
	var html = '<div><style> body{font-size:12px;font-family:arial;} .hideinprint1{display:none}</style> <h2 align="center">PO Product List</h2> <div> <b style="float:right">Printed By : <?php echo $user['username'];?> <br> Printed On : <?php echo format_datetime_ts(time());?>  </b> <b style="font-size:14px;">PO: #<?=$po['po_id']?></b><br><b style="font-size:14px;">PO Date:<?=date("d/m/Y g:ia",strtotime($po['created_on']))?></b><br><b style="font-size:14px;">Vendor: <?=$po_ven_name;?></b> </div><table cellpadding=5 cellspacing=0 border=1 width="100%" style="font-size:12px;font-family:arial;">'+$('#po_prod_list').html()+'</table></div>';
		prw=window.open("",'');
		prw.document.write(html);
		prw.focus();
		prw.print();
}


$("#po_deliverydate").datetimepicker({
	timeFormat: "hh:mm tt",
	dateFormat: "D MM d, yy"
});
function closepo()
{
	if(confirm("Are you sure?"))
		location="<?=site_url("admin/closepo/{$po['po_id']}")?>";
}

function updateexpected_podeliverydate()
{
	if(confirm("Are you sure?"))
		location="<?=site_url("admin/updatedeliverydate/{$po['po_id']}")?>";
}

$(function(){
	$('#po_prod_list').jq_fix_header_onscroll();
});


</script>

<?php
