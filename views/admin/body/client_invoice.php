<div class="container">
<h2>Client Invoice : <?=$invoice['invoice_no']?></h2>

<table class="datagrid" style="float:left">
<tbody>
<tr><td>Invoice No :</td><td><a target="_blank" href="<?=site_url("admin/print_client_invoice/{$invoice['invoice_id']}")?>" class="link"><?=$invoice['invoice_no']?></a></td></tr>
<tr><Td>Client</Td><td><?=$this->db->query("select client_name from m_client_info where client_id=?",$invoice['client_id'])->row()->client_name?></td></tr>
<tr><td>Invoice Date :</td><td><?=$invoice['invoice_date']?></td></tr>
<tr><td>Status :</td><td><?php 
switch($invoice['invoice_status']){
	case 0:
		echo "Not packed / pending";
?><br><a href="<?=site_url("admin/pack_client_invoice/{$invoice['invoice_id']}")?>">pack</a>
<?php 
		break;
	case 1:
		echo "Active";
?><br><a target="_blank" href="<?=site_url("admin/print_client_invoice/{$invoice['invoice_id']}")?>">print</a><?php 
		break;
}?></td></tr>
<tr><td>Payment :</td><td><?php switch($invoice['payment_status']){
	case 0:
		echo 'Pending';
		break;
	case 1:
		echo "Partial";
		break;
	case 2:
		echo "Complete";
		break;
}?><br><a href="<?=site_url("admin/payment_client_invoice/{$invoice['invoice_id']}")?>">update</a>
</td></tr>
</tbody>
</table>

<div style="float:left;padding-left:20px;">
<h4>Payments</h4>
<table class="datagrid">
<thead><tr><th>Amount Paid</th><th>Payment Type</th><th>Instrument No</th><th>Instrument Date</th><th>Bank</th><th>Is Cleared</th><th>Remarks</th></tr></thead>
<tbody>
<?php foreach($payments as $p){?>
<tr><td>Rs <?=$p['amount_paid']?></td><td><?php switch ($p['payment_type']){
	case 0:
		echo "Cash";
		break;
	case 1:
		echo "Cheque";
		break;
	case 2:
		echo "Transfer";
		break;
	case 3:
		echo "DD";
		break;
}?></td>
<td><?=$p['instrument_no']?></td>
<td><?=$p['instrument_date']?></td>
<td><?=$p['bank_name']?></td>
<td><?=$p['is_cleared']?"YES":"NO"?></td>
<td><?=$p['remarks']?></td>
</tr>
<?php }?>
</tbody>
</table>
</div>

<div class="clear"></div>

<h4>Products in invoice</h4>
<table class="datagrid">
<theaD><tr><th>Product</th><th>Qty</th><th>MRP</th><th>Offer price</th><th>Order No</th></tr></theaD>
<tbody>
<?php foreach($items as $item){?>
<tr>
<td><?=$item['product_name']?></td>
<td><?=$item['invoice_qty']?></td>
<td><?=$item['mrp']?></td>
<td><?=$item['offer_price']?></td>
<td><a href="<?=site_url("admin/client_order/{$item['order_id']}")?>">ORD<?=$item['order_id']?></a></td>
</tr>
<?php }?>
</tbody>
</table>


</div>
<?php
