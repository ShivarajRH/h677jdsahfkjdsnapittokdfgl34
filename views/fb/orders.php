<script type="text/javascript">
<!--
$(function(){
	$("#orderidf").submit(function(){
		oid=$("#orderidv").val();
		if(!is_required(oid))
		{
			alert("Please enter order ID");
			return false;
		}
		if(!is_naturalnonzero(oid))
		{
			alert("Order ID can be only numbers");
			return false;
		}
		location="<?=site_url("order")?>/"+oid;
		return false;
	});
});
//-->
</script>
<div class="headingtext">Orders</div>
<div style="padding:0px 10px;" align="center">
<div align="right" style="padding-bottom:10px;">
<form id="orderidf">
Enter Order ID : <input type="text" id="orderidv" name="orderid"> <input type="submit" value="Get Order">
</form>
</div>
<?php if(empty($orders)){?>
<h2>No orders available for you</h2>
<?php }else{?>
<style>
.orderta td, .orderta th{
padding:5px;
}
</style>
<div align="left">
<table class="orderta" width="100%" cellpadding="0" cellspacing="0" border="1">
<tr>
<th>S.No</th>
<th>Order Id</th>
<?php if(isset($user['aid'])){?>
<th>VIA Trans ID</th>
<?php }?>
<th>Product Name</th>
<th>Qty</th>
<th>Paid</th>
<th>Status</th>
<th>Order Date</th>
</tr>
<?php 
$status=array("Pending","Processed","Shipped");
foreach($orders as $i=>$order){?>
<tr>
<td><?=($i+1)?></td>
<td><a href="<?=site_url("order/".$order['id'])?>" style="color:blue"><?=$order['id']?></a></td>
<?php if(isset($user['aid'])){?>
<td><?=$order['via_transid']?></td>
<?php }?>
<td><?=$order['name']?></td>
<td><?=$order['quantity']?></td>
<td>Rs <?=$order['paid']?></td>
<td><?=$status[$order['status']];?></td>
<td><?=date("g:ia d/m/y",$order['time'])?></td>
</tr>
<?php }?>
</table>
</div>
<?php }?>
</div>
<?php
