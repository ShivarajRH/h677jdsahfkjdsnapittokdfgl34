<?php 
$total=0;
$c_total=0;
foreach($orders as $o)
{
	$total+=$o['i_partner_price']*$o['qty'];
	if($o['status']==2)
	$c_total+=$o['i_partner_price']*$o['qty'];
}
?>
<div class="container">

<h2>Partner order list for the log created on '<?=date("g:ia d/m/y",$this->db->query("select created_on as d from partner_orders_log where id=?",$log_id)->row()->d)?>'</h2>

<div class="dash_bar">Total Partner price : <span>Rs <?=$total?></span></div>
<div class="dash_bar">Partner price for completed orders : <span>Rs <?=$c_total?></span></div>

<div class="clear"></div>

<table class="datagrid">
<thead><tr><th>Sno</th><th>Order</th><th>Customer price</th><th>Partner Price</th><th>Status</th></tr></thead>
<tbody>
<?php $status=array("Pending","Invoiced","Shipped","Cancelled"); $sno=1; foreach($orders as $o){?>
<tr>
<td><?=$sno++?></td>
<td><a href="<?=site_url("admin/trans/{$o['transid']}")?>" class="link"><?=$o['transid']?></a></td>
<td><?=$o['i_customer_price']*$o['qty']?></td>
<td><?=$o['i_partner_price']*$o['qty']?></td>
<td><?=$status[$o['status']]?>
</tr>
<?php }?>
</tbody>
</table>

</div>
<?php
