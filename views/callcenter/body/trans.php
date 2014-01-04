<div style="padding:10px 20px;">
<h2>Transaction Details for <?=$trans['transid']?></h2>
<table style="background:#fff;color:#000;font-size:120%;padding:5px;min-width:400px;" cellpadding=5>
<tr><td>Trans ID</td><td>:</td><td><b><?=($trans['transid'])?></b></td></tr>
<tr><td>Amount</td><td>:</td><td><b><?=($trans['amount'])?></b></td></tr>
<tr><td>Paid</td><td>:</td><td><b><?=($trans['paid'])?></b></td></tr>
<tr><td>Mode</td><td>:</td><td><b><?=$trans['mode']==0?"Payment Gateway":"COD"?></b></td></tr>
<tr><td>Response Code</td><td>:</td><td><b><?=($trans['response_code'])?></b></td></tr>
<tr><td>Response Msg</td><td>:</td><td><b><?=($trans['msg'])?></b></td></tr>
<tr><td>Payment ID</td><td>:</td><td><b><?=($trans['payment_id'])?></b></td></tr>
<tr><td>PG Transaction ID</td><td>:</td><td><b><?=($trans['pg_transaction_id'])?></b></td></tr>
<tr><td>Is Flagged</td><td>:</td><td><b><?=($trans['is_flagged'])?></b></td></tr>
<tr><td>Started on</td><td>:</td><td><b><?=date("g:ia d/m/y",$trans['init'])?></b></td></tr>
<tr><td>Ended on</td><td>:</td><td><b><?=$trans['actiontime']==0?"na":date("g:ia d/m/y",$trans['actiontime'])?></b></td></tr>
<tr><td>Coupon Used</td><td>:</td>
<td>
<?php
$coupon=$this->db->query("select coupon from king_used_coupons where transid=? and status=1",$trans['transid'])->row_array();
if(!empty($pendings))
	echo "n/a";
else if(empty($coupon))
	echo "<i>none</i>";
else
	echo $coupon['coupon'];
?>
</td>
</tr>
<tr>
<td>Status</td>
<td>:</td>
<td><?php if(!empty($orders)){?>
Authorized
<?php }else{?>
PONR
<br><a href="<?=site_url("admin/authorize_trans/{$trans['transid']}")?>" onclick='return confirm("sure?");'>authorize</a>
<?php }?>
</td>
</tr>
</table>

<div style="padding:5px;">
<h2>Authorized Orders</h2>
<?php if(!empty($orders)){?>
<table border=1 style="background:#fff;color:#000;padding:5px;" cellpadding=5>
<tr><th>Order ID</th><th>Product Name</th><th>Quantity</th><th>Status</th><th>Billing Details</th><th>Shipping Details</th><th>Ordered on</th><th>Action Time</th></tr>
<?php foreach($orders as $p){?>
<tr><td><?=$p['id']?></td><td><?=$p['item']?></td><td><?=$p['quantity']?></td>
<?php $status=array("Pending","Processed","Shipped")?>
<td><b><?=$status[$p['status']]?></b></td>
<td><?=$p['bill_person']."<br>".$p['bill_address']."<br>".$p['bill_city']."<br>".$p['bill_state']."<br>".$p['bill_pincode']."<br>".$p['bill_phone']."<br>".$p['bill_email']?></td>
<td><?=$p['ship_person']."<br>".$p['ship_address']."<br>".$p['ship_city']."<br>".$p['ship_state']."<br>".$p['ship_pincode']."<br>".$p['ship_phone']."<br>".$p['ship_email']?></td>
<td><?=date("g:ia d/m/y",$p['time'])?></td>
<td><?=$p['actiontime']==0?"na":date("g:ia d/m/y",$p['actiontime'])?></td>
</tr>
<?php } ?>
</table>
<?php }else {  ?>
	<h3>No unauthorized orders</h3>
<?php } ?>
</div>

<div style="padding:5px;">
<h2>Unauthorized Orders</h2>
<?php if(!empty($pendings)){?>
<table border=1 style="background:#fff;color:#000;padding:5px;" cellpadding=5>
<tr><th>Order ID</th><th>Product Name</th><th>Quantity</th><th>Billing Details</th><th>Shipping Details</th><th>Time</th></tr>
<?php foreach($pendings as $p){?>
<tr><td><?=$p['id']?></td><td><?=$p['item']?></td><td><?=$p['quantity']?></td>
<td><?=$p['bill_person']."<br>".$p['bill_address']."<br>".$p['bill_city']."<br>".$p['bill_state']."<br>".$p['bill_pincode']."<br>".$p['bill_phone']."<br>".$p['bill_email']?></td>
<td><?=$p['ship_person']."<br>".$p['ship_address']."<br>".$p['ship_city']."<br>".$p['ship_state']."<br>".$p['ship_pincode']."<br>".$p['ship_phone']."<br>".$p['ship_email']?></td>
<td><?=date("g:ia d/m/y",$p['time'])?></td>
</tr>
<?php } ?>
</table>
<?php }else {  ?>
	<h3>No unauthorized orders</h3>
<?php } ?>
</div>

</div>