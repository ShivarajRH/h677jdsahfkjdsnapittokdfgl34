<div class="headingtext">Order ID : <?=$this->uri->segment(2)?></div>
<div style="padding:10px;padding-left:50px;font-size:13px;" align="left">
<style>
td{
vertical-align:top;
}
</style>
<table cellpadding="5" style="background:#eee;">
<tr><td align="left">Item Name </td><td>:</td><td align="left"><b><?=$order['name']?></b></td></tr>
<tr><td align="left">Quantity </td><td>:</td><td align="left"><b><?=$order['quantity']?></b></td></tr>
<tr><td align="left">Paid </td><td>:</td><td align="left">Rs <b><?=$order['paid']?></b></td></tr>
<tr><td align="left">Shipping Address </td><td>:</td>
<td align="left">
<b><?=$order['ship_person']?></b><br>
<?=nl2br($order['ship_address'])?><br>
<?=$order['ship_city']?> <?=$order['ship_pincode']?><br>
<?=$order['ship_phone']?>
</td></tr>
<tr>
<td>Status</td>
<td>:</td>
<?php $status=array("Pending","Processed","Shipped")?>
<td><b><?=$status[$order['status']]?></b></td>
</tr>
<tr><td>Shipped on</td><td>:</td><td><?=$order['shiptime']==0?"n/a":date("d/m/y",$order['shiptime']);?></td></tr>
<tr><Td>Delivery Medium</Td><td>:</td><td><?=$order['medium']==""?"n/a":$order['medium'];?></td></tr>
<tr><td>Courier Tracking ID</td><td>:</td><td><?=$order['shipid']==""?"n/a":$order['shipid'];?></td></tr>
</table>
</div>
<?php
