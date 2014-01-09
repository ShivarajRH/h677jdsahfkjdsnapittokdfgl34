<div class="container">

<h2>Proforma Invoice : <?=$invoice['p_invoice_no']?></h2>

<?php
	$proforma_det = $this->db->query("select split_inv_grpno,dispatch_id,a.transid from proforma_invoices a join shipment_batch_process_invoice_link c on c.p_invoice_no = a.p_invoice_no left join king_invoice b on c.invoice_no = b.invoice_no where a.p_invoice_no = ? ",$batch['p_invoice_no'])->row_array();
?>


<table class="datagrid noprint">
<tbody>
<tr><td>No :</td><td><?=$invoice['p_invoice_no']?></td></tr>
<tr><td>Date :</td><td><?=format_datetime_ts($invoice['createdon'])?></td></tr>
<tr><td>Transid :</td><td><a href="<?=site_url("admin/trans/{$proforma_det['transid']}")?>"><?=$proforma_det['transid']?></a></td></tr>
<tr><td>Status :</td><td><?=$invoice['invoice_status']==0?"CANCELLED":($batch['invoice_no']==0?"NOT INVOICED":"INVOICED")?>
&nbsp;&nbsp; 
<?php if($invoice['invoice_status']==1 && $batch['invoice_no']==0){?>
<a href="<?=site_url("admin/cancel_proforma_invoice/{$invoice['p_invoice_no']}")?>" class="danger_link">CANCEL</a>
<?php }?>
</td></tr>
<?php if($proforma_det['split_inv_grpno'] == 0) { ?>
<tr><td>Invoice no :</td><td><?php if($batch['invoice_no']!=0){?><a href="<?=site_url("admin/invoice/{$batch['invoice_no']}")?>"><?=$batch['invoice_no']?></a><?php }?></td></tr>
<?php }else
	{
?>
<tr><td>DispatchID :</td><td><a target="_blank" href="<?=site_url("admin/invoice/{$proforma_det['dispatch_id']}")?>"><?=$proforma_det['dispatch_id']?></a></td></tr>

<?php } ?>
<tr><td>Batch :</td><td><a href="<?=site_url("admin/batch/{$batch['batch_id']}")?>"><?=$batch['batch_id']?></a></td></tr>
</tbody>
</table>


<h3>Items in the proforma invoice</h3>
<table class="datagrid">
<thead><tr><th>Sno</th><th>Item</th><th>Qty</th><th>MRP</th><th>Offer Price</th><th>Discount</th><th>Final</th><th>Invoice</th></tr></thead>
<tbody>
<?php $sno=1; 
$ttl_amt = 0;
foreach($orders as $o){
	$o_det = $this->db->query("select b.invoice_no,a.i_orgprice as mrp,(a.i_orgprice-(i_discount)) as offer_price,(a.i_orgprice-(i_discount+i_coup_discount)) as amt,i_coup_discount as coup_discount,a.quantity,d.shipped
	from king_orders a 
	join king_invoice b on a.id = b.order_id 
	join proforma_invoices c on c.order_id = b.order_id and c.invoice_status = 1 
	join shipment_batch_process_invoice_link d on d.p_invoice_no = c.p_invoice_no 
	where c.p_invoice_no = ? and a.id = ? 
group by a.id,b.invoice_no  
order by a.sno ",array($batch['p_invoice_no'],$o['id']))->row_array();	
?>
<tr>
<td><?=$sno++?></td>
<td><a href="<?=site_url("admin/deal/{$o['itemid']}")?>"><?=$o['product']?></a></td>
<td><?=$o['quantity']?></td>
<td><?php echo $o_det['mrp']*$o_det['quantity'];?></td>
<td><?php echo $o_det['offer_price']*$o_det['quantity'];?></td>
<td><?php echo $o_det['coup_discount']*$o_det['quantity'];?></td>
<td><?php echo $o_det['amt']*$o_det['quantity'];?></td>
<td><a target="_blank" href="<?=site_url("admin/invoice/{$o_det['invoice_no']}")?>"><?=$o_det['invoice_no']?></a></td>
<td><a target="_blank" href="<?php echo site_url('admin/product/'.$o['product_id']);?>">View Product</a> </td>
</tr>
<?php 
	$ttl_amt+= $o_det['amt']*$o['quantity'];
}?>
<tr>
	<td colspan="6" align="right">Total</td>
	<td><?php echo $ttl_amt;?></td>
</tr>
</tbody>
</table>
</div>
<?php
