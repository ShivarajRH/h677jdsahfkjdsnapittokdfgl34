<?php
	$user = $this->erpm->auth(); 
	$grn['po_id'] = $this->db->query("select group_concat(distinct po_id) as po_id from t_grn_product_link where grn_id = ? ",$grn['grn_id'])->row()->po_id;
	$grn_date = $this->db->query("select created_on from t_grn_product_link where grn_id = ? ",$grn['grn_id'])->row()->created_on;
?>
<div class="container">

<h2>Stock Intake (GRN<?=$grn['grn_id']?>)</h2>
<div style="float:left;">
<table class="datagrid noprint">
<thead>
<tr><th colspan="100%">Stock Intake Details</th></tr>
</thead>
<tbody>
<tr><td>Vendor :</td><td><a href="<?=site_url("admin/vendor/{$grn['vendor_id']}")?>"><?=$grn['vendor_name']?></a></td></tr>
<tr><td>Status :</td><td><?php switch($grn['payment_status']){
	case 0: echo "Unaccounted";?><br><a href="<?=site_url("admin/account_grn/{$grn['grn_id']}")?>">account</a> <?php break;
	case 1: echo "Accounted, ready for payment";?><br><a href="<?=site_url("admin/create_voucher")?>">make payment</a> <?php break;
	case 2: echo "Payment made";break;
}?></td></tr>
<tr><td>Remarks :</td><td><?=$grn['remarks']?></td></tr>
<tr><td>Stock Taken By : </td><td><?=$this->db->query("select a.name from king_admin a join t_stock_update_log l on l.grn_id=? where a.id=l.created_by",$grn['grn_id'])->row()->name?>
</tbody>
</table>
</div>

<div style="float:left;margin-left:20px;">
<h4 style="margin:0px;">Invoices</h4>
<table id="grn_inv_list" class="datagrid">
<thead><tr><th>Invoice No</th><th>Invoice date</th><th>Invoice Value</th><th class="hideinprint">Scan Copy</th></thead>
<tbody>
<?php foreach($invoices as $inv){?>
<tr>
<td><?=$inv['purchase_inv_no']?></td>
<td><?=$inv['purchase_inv_date']?></td>
<td><?=$inv['purchase_inv_value']?></td>
<td class="hideinprint"><?php if(file_exists(ERP_PHYSICAL_IMAGES."invoices/{$inv['id']}.jpg")){?>
<a target="_blank" href="<?=ERP_IMAGES_URL?>invoices/<?=$inv['id']?>.jpg">view</a><?php } else echo "na";?>
</td>
</tr>
<?php }?>
</tbody>
</table>
</div>

<div style="float:left;margin-left:20px;">
<h4 style="margin:0px;">Vouchers</h4>
<table class="datagrid">
<thead><tr><th>Voucher</th><th>Total Value</th><th>Adjusted amount for GRN</th><th>Created on</th><th>Created by</th></tr></thead>
<tbody>
<?php foreach($vouchers as $v){?>
<tr>
<td><a href="<?=site_url("admin/voucher/{$v['voucher_id']}")?>"><?=$v['voucher_id']?></a></td>
<td>Rs <?=$v['voucher_value']?></td>
<td>Rs <?=$v['adjusted_amount']?></td>
<td><?=$v['created_on']?></td>
<td><?=$v['created_by']?></td>
</tr>
<?php }?>
</tbody>
</table>
</div>

<div class="clear"></div>
<h4 style="margin-bottom:3px;">GRN Product list</h4>
<table id="grn_prod_list" class="datagrid nofooter" style="width:100%">
<thead>
<tr><th>Sno</th><th width="250" style="text-align:left">Product Name</th><th>POID</th><th>Invoiced <br>Qty</th><th>Received <br>Qty</th><th>MRP</th><th>DP Price</th><th>Base <br>Price</th><th>Tax</th><th>Purchase <br>Price</th><th>Margin</th><th>Scheme <br>discount</th><th>FOC</th><th>Has Offer</th></tr>
</thead>
<tbody>
<?php $sno=0; 
		$total_inv_qty = 0;
		$total_rcvd_qty = 0;
		$total_pprice = 0;

	foreach($prods as $p){
		$total_inv_qty += $p['invoice_qty'];
		$total_rcvd_qty += $p['received_qty'];
		$total_pprice += $p['received_qty']*$p['purchase_price'];
	?>
<tr>
	<td><?=++$sno?></td>
	<td><a target="_blank" href="<?php echo site_url('admin/product/'.$p['product_id']) ?>"><?=$p['product_name']?></a></td>
	<td><a href="<?=site_url("admin/viewpo/{$p['po_id']}")?>" target="_blank">PO<?=$p['po_id']?></a></td>
	<td align="right"><?=$p['invoice_qty']?></td>
	<td align="right"><?=$p['received_qty']?></td>
	<td align="right"><?=$p['mrp']?></td>
	<td align="right"><?=$p['dp_price']?></td>
	<td class="hide" align="right"><?=$p['purchase_price']-($p['purchase_price']*$p['tax_percent']/100)?></td>
	<td align="right"><?=$p['tax_percent']?></td>
	<td align="right"><?=$p['purchase_price']?></td>
	<td align="right"><?=$p['margin']?></td>
	<td align="right"><?=$p['scheme_discunt_type']==2?"Rs":""?><?=$p['scheme_discount_value']?><?=$p['scheme_discunt_type']==1?"":"%"?></td>
	<td align="right"><?=$p['is_foc']==2?"YES":"NO"?></td>
	<td align="right"><?=$p['has_offer']==2?"Yes":"NO"?></td>
</tr>
<?php }?>

	<tr class="tbl_stats_row">
		<td colspan="3" align="right">Total</td>
		<td align="right"><?php echo $total_inv_qty; ?></td>
		<td align="right"><?php echo $total_rcvd_qty; ?></td>
		<td colspan="4">&nbsp;</td>
		<td align="right"><?php echo format_price($total_pprice,4); ?></td>
		<td colspan="4">&nbsp;</td>
	</tr>
</tbody>


</table>

<div style="text-align: right;padding-top:5px;">
	<a href="javascript:void(0)" class="button button-tiny button-info" onclick="print_grndoc()">Print</a> 
</div>

</div>

<style>
#grn_prod_list th{text-align:right;}
.tbl_stats_row td{background:#ffffD0 !important}
</style>

<script>
	
function print_grndoc()
{
	$('#grn_inv_list tfoot').hide();
	var grninvhtml = '<table border=1 cellpadding=2 cellspacing=0 style="font-size:10px;">'+$('#grn_inv_list').html()+'</table>';
	$('#grn_inv_list tfoot').show();
	var html = '<div><style> body{font-size:12px;font-family:arial;} .hideinprint{display:none}</style> <h2 align="center">GRN Document</h2> <div> <b style="float:right"> <br> Printed On : <?php echo format_datetime_ts(time());?> <br> '+grninvhtml+'  </b> <b style="font-size:14px;">GRN: #<?=$grn['grn_id']?> - (<?php echo format_datetime($grn_date); ?>) <br> Vendor: <?=$grn['vendor_name']?> <br> PO: #<?=$grn['po_id']?> </b>  </div><table cellpadding=5 cellspacing=0 border=1 width="100%" style="font-size:12px;font-family:arial;">'+$('#grn_prod_list').html()+'</table></div>';
		prw=window.open("",'');
		prw.document.write(html);
		prw.focus();
		prw.print();
}

$(function(){
	$('#grn_prod_list').jq_fix_header_onscroll();
});
</script>
<?php
