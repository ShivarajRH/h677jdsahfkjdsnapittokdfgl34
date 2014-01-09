<?php 
$items=$order['items'];
$o=$order['order'];
?>
<div class="container">
<h2>Client Order: <?=$order['order']["order_id"]?></h2>

<table class="datagrid smallheader noprint" style="float:left;">
<tbody>
<tr><td>Order :</td><td><a class="link" href="<?=site_url("admin/client_order/{$o['order_id']}")?>">O<?=$o['order_id']?></a></td></tr>
<tr><td>Client :</td><td><a href="<?=site_url("admin/client/{$o['client_id']}")?>"><?=$o['client']?></a></td></tr>
<tr><td>Order Reference :</td><td><?=$o['order_reference_no']?></td></tr>
<tr><td>Status</td><td><?php switch($o['order_status']){
	case 0:
		echo "Pending";break;
	case 1:
		echo "Partial";break;
	case 2:
		echo "Complete";break;
	case 3:
		echo "closed";break;
}?></td></tr>
<tr><td>Created on :</td><td><?=$o['created_on']?></td></tr>
<tr><td>Created By :</td><td><?=$o['created_by']?></td></tr>
<?php if(!empty($o['closed_by'])){?>
<tr><td>Closed By :</td><td><?=$o['closed_by']?></td></tr>
<?php }?>
</tbody>
</table>

<div style="float:left;padding-left:20px;">
<h4>Invoices</h4>
<table class="datagrid smallheader noprint">
<thead><tr><th>Invoice</th><th>Value</th><th>Payment Status</th></tr></thead>
<tbody>
<?php foreach($invoices as $inv){?>
<tr>
<td><a class="link" href="<?=site_url("admin/client_invoice/{$inv['invoice_id']}")?>"><?=$inv['invoice_no']?></a></td>
<td>Rs <?=$inv['total_invoice_value']?></td>
<td>
<?php switch($inv['payment_status']){
	case 0: echo "pending";break;
	case 1: echo "complete";break;
	case 2: echo "partial";break;
}?>
</td>
</tr>
<?php }if(empty($invoices)){?><tr>
<td colspan="100%">no invoices for this order</td>
</tr>
<?php }?>
</tbody>
</table>
<?php if($o['order_status']!=2 && $o['order_status']!=3){?>
<div style="padding-top:5px;">
<form method="post" onsubmit='return confirm("Are you sure?")'>
<input type="hidden" name="close" value="yes">
<input type="submit" value="Close this order">
</form>
</div>
<?php }?>
</div>

<div class="clear" style="padding-bottom:20px;"></div>

<table class="datagrid">
<thead>
<tr><th>Product Name</th><th>Ordered Qty</th><th>Invoiced Qty</th></tr>
</thead>
<tbody>
<?php foreach($items as $i){?>
<tr>
<td><a href="<?=site_url("admin/product/{$i['product_id']}")?>"><?=$i['product']?></a></td>
<td><?=$i['order_qty']?></td>
<td><?=$i['invoiced_qty']?></td>
</tr>
<?php }?>
</tbody>
</table>
</div>
<?php
