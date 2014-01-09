<div class="container">
<h2>Orders</h2>
<table cellpadding=5 cellspacing=0 width="100%" bgcolor="#FFFFFF" style="margin:10px">
<tr>
<th>ID</th>
<th>Item Name</th>
<th>Paid</th>
<th>Qty</th>
<th>Status</th>
<th>Order Date</th>
<th>Action Date</th>
<th>Ship to</th>
<th>Shipping Address</th>
<th>Shipping details</th>
</tr>
<?php 
foreach($orders as $order){
?>
<tr onmouseover='$(this).css("background","#eee")' onmouseout='$(this).css("background","#fff")'>
<td><?=$order['id']?></td>
<td><?=$order['name']?></td>
<td><?=$order['paid']?></td>
<td><?=$order['quantity']?></td>
<td><?php 
switch($order['status'])
{
	case 0:
		echo "pending";break;
	case 1:
		echo "processed";break;
	case 2:
		echo "shipped";break;
	case 3:
		echo "rejected/cancelled";break;
}
?></td>
<td><?=date("g:ia d/m/y",$order['time'])?></td>
<td><?php if($order['actiontime']!=0) echo date("g:ia d/m/y",$order['actiontime']); else echo "n/a";?></td>
<td><?=$order['ship_person']?></td>
<td><?=$order['ship_address'].", ".$order['ship_city']."<br>".$order['ship_phone']?></td>
<td>
<?php if($order['status']==2){?>
Medium : <?=$orde['medium']?><br>
Track ID : <?=$order['shipid']?>
<?php }else echo "n/a";?>
</td>
</tr>
<?php }?>
</table>
</div>
<?php
