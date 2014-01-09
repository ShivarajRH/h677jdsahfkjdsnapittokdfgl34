<div class="container">
<h2>Order Details</h2>

<div style="font-size:13px;margin:10px;background:#fff;padding:5px;">
<table cellpadding=5>
<tr>
<tD>Order ID</td><td>:</td>
<td><?=$order['id']?></td>
</tr>
<tr>
<tD>Item Name</td><td>:</td>
<td><?=$order['name']?></td>
</tr>
<tr>
<tD>Amount paid</td><td>:</td>
<td>Rs <?=$order['paid']?></td>
</tr>
<tr>
<tD>Quantity</td><td>:</td>
<td><?=$order['quantity']?></td>
</tr>
<tr>
<tD>Status</td><td>:</td>
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
</tr>
<tr>
<tD>Order time</td><td>:</td>
<td><?=date("g:ia d/m/y",$order['time'])?></td>
</tr>
<tr>
<tD>Order Action on</td><td>:</td>
<td><?php if($order['actiontime']!=0) echo date("g:ia d/m/y",$order['actiontime']); else echo "n/a";?></td>
</tr>
<tr>
<tD>Ship to person</td><td>:</td>
<td><?=$order['ship_person']?></td>
</tr>
<tr>
<tD valign="top">Shipping Addr</td><td valign="top">:</td>
<td><?=nl2br($order['ship_address']).",<br>".$order['ship_city']?></td>
</tr>
<tr>
<td>Mobile</td>
<td>:</td>
<td><?=$order['ship_phone']?></td>
</tr>
<tr>
<tD>Delivery details</td><td>:</td>
<td>
<?php if($order['status']==2){?>
Medium : <?=$orde['medium']?><br>
Track ID : <?=$order['shipid']?>
<?php }else echo "n/a";?>
</td>
</tr>
</table>
</div>

</div>
<?php
