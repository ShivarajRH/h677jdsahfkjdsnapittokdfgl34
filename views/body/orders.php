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
		location="<?=site_url("order")?>/"+oid;
		return false;
	});
});
//-->
</script>
<div class="container" style="text-align:left;padding:10px 0px 30px 0px;">
<h1>My Orders</h1>
<div style="padding:0px 10px;" align="center">
<?php /*
<div align="right" style="padding-bottom:10px;">
<form id="orderidf">
Enter Transaction ID : <input type="text" id="orderidv" name="orderid"> <input type="submit" value="Get Order">
</form>
</div>
*/ ?>
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
<th>Trans ID</th>
<?php if(isset($user['aid'])){?>
<th>VIA Trans ID</th>
<?php }?>
<th>Product Name</th>
<th>Qty</th>
<th>Paid</th>
<th>Status</th>
<th>Order Date</th>
<th></th>
</tr>
<?php 
$status=array("Pending","Processed","Shipped");
foreach($orders as $i=>$order){?>
<tr>
<td><?=($i+1)?></td>
<td><?=$order['transid']?></td>
<?php if(isset($user['aid'])){?>
<td><?=$order['via_transid']?></td>
<?php }?>
<td><?=$order['name']?></td>
<td><?=$order['quantity']?></td>
<td>Rs <?=$order['paid']?></td>
<td><?=$status[$order['status']];?></td>
<td><?=date("g:ia d/m/y",$order['time'])?></td>
<td><?php if($order['status']!=0){?><a href="<?=site_url("order/".$order['transid'])?>" style="color:blue">view invoice</a><?php }?></td>
</tr>
<?php }?>
</table>
</div>
<?php }?>
</div>
</div>
<?php
