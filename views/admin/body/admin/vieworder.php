<script type="text/javascript">
<!--
$(function(){
	$("select[name=action]").val("select");
	$("select[name=action]").change(function(){
		if($(this).val()=="sent")
			$("#shipform").show();
		else
			$("#shipform").hide();
	});
	$("#actform").submit(function(){
		if($("select[name=action]",$(this)).val()=="sent")
		{
			if(!is_required($("input[name=dmed]",$(this)).val()))
			{
				alert("Enter delivery medium. Ex: courier name");
				return false;
			}
			if(!is_required($("input[name=track]",$(this)).val()))
			{
				alert("Enter tracking id for delivery");
				return false;
			}
			if(!is_required($("input[name=shipdate]",$(this)).val()))
			{
				alert("Enter shipped date");
				return false;
			}
		}
		return true;
	});
	$("#shipdate").datepicker({showOn: 'both', dateFormat: 'yy-mm-dd' , buttonImage: '<?=base_url()?>images/calendar_old.png', buttonImageOnly: true});
});
//-->
</script>
<?php 
$user=$this->session->userdata("admin_user");
?>
<style>
td{
vertical-align:top;
}
</style>
<div class="heading" style="margin-bottom:0px;">
<div class="headingtext container">
View Order
<?php if(isset($order->id)){?> No. <?=$order->id?> <?php }?>
</div>
</div>
<div align="center" class="container" style="padding:10px;font-family:arial;">
<div align="right" style="padding-bottom:5px;font-size:12px;"><a href="<?=site_url("admin/orders")?>">back to orders</a></div>
<div style="padding:10px;border:1px solid #ddd;background:#fff">
<div style="float:right;">
<Div style="float:right"><?=date("M d, g:i a",$order->time)?></div>
<div style="font-size:14px;clear:right;float:right;padding-top:50px;">Status : 
<?php

	$order_status_flags = $this->config->item('order_status');
	$status_color_codes = $this->config->item('status_color_codes');	
	
echo "<span style='color:".$status_color_codes[$order->admin_order_status]."'>".$order_status_flags[$order->admin_order_status]."</span>";


?>
</div>
<?php
if($order->status!=0)
{
?>
<div style="clear:right;float:right;font-size:14px;padding-top:7px;">Action taken on <b><?=date("M d, g:i a",$order->actiontime)?></b></div>
<?php 	
}
?>
<div style="float:right;clear:right;">
<table width="200" border=1 cellpadding=5 cellspacing=0>
<tr>
<td>Price</td><td>Rs <?=$order->price?></td>
</tr>
<tr>
<td>MRP</td><td>Rs <?=$order->orgprice?></td>
</tr>
<tr>
<td>Product Cost</td><td>Rs <?=$order->nlc?></td>
</tr>
<tr>
<td>Handling Cost</td><td>Rs <?=$order->phc?></td>
</tr>
<tr>
<td>TAX</td><td><?=$order->tax/100?>%</td>
</tr>
<tr>
<td>Service TAX</td><td><?=$order->service_tax/100?>%</td>
</tr>
</table>
</div>
<div style="clear:right;float:right;padding-top:20px;margin-right:0px;">
<table cellpadding="5" style="width:320px;clear:right;font-size:14px;">
</table>
</div>
</div>
 
<!--<div style="padding-top:7px;font-family:'trebuchet ms';color:green;font-size:20px;font-weight:bold;">Order Details</div>-->
<div style="color:green;font-family:'trebuchet ms';font-size:18px;padding-top:10px;">Order details</div>
<div style="padding-top:20px;font-size:13px;">
<table border="1" class="datagrid" cellspacing="0" cellpadding="5" width="370">
<tr>
<td>Order ID</td><td>:</td><td><?=$order->id?></td>
</tr>
<tr>
<td>Within Transaction</td><td>:</td><td><?=$order->transid?></td>
</tr>
<tr>
<td>Billing</td>
<td>:</td>
<td><?=$order->bill_person?><br><?=nl2br($order->bill_address)?><br><?=$order->bill_city?></td>
</tr>
<tr>
<td valign="top">Item Name</td>
<td valign="top">:</td>
<td style="font-weight:normal"><?=$order->name?><br>
<!--<img src="<?=base_url()?>images/items/thumbs/<?=$order->itempic?>.jpg">--->
</td>
</tr>
<tr>
<td>Mode</td><td>:</td>
<td><?=$order->mode==0?"Payment Gateway":"Cash On Delivery"?></td>
</tr>
<tr>
<td>Quantity</td>
<td>:</td>
<td style="font-weight:normal"><?=$order->quantity?></td>
</tr>
<?php $buyer_options=unserialize($order->buyer_options);
if(is_array($buyer_options))
foreach($buyer_options as $mean=>$opt){?>
<tr>
<td><?=$mean?></td>
<td>:</td>
<td><?=$opt?></td>
</tr>
<?php }?>
<tr>
<td>Delivery Address</td>
<td>:</td>
<td style="font-weight:normal"><?=$order->ship_person?><br><?=nl2br($order->ship_address)?><br><?=$order->ship_city." ".$order->ship_pincode?><br><?=$order->ship_phone?></td>
</tr>
<tr>
<td>Contact Number</td>
<td>:</td>
<td style="font-weight:normal"><?php 
if($order->ship_phone!=$order->bill_phone)
	echo "Billing : ".$order->bill_phone."<br>Shipping :".$order->ship_phone;
else
	echo $order->bill_phone;
?></td>
</tr>
<tr>
<td>Email</td>
<td>:</td>
<td style="font-weight:normal"><?php 
if($order->ship_email!=$order->bill_email)
	echo "Billing : ".$order->bill_email."<br>Shipping :".$order->ship_email;
else
	echo $order->bill_email;
?></td>
</tr>
<tr>
<td>Shipped on</td>
<td>:</td>
<td><?=$order->shiptime==0?"n/a":date("d/m/y",$order->shiptime);?></td>
</tr>
<tr>
<td>Courier Tracking ID</td>
<td>:</td>
<td><?=$order->shipid==""?"n/a":$order->shipid;?></td>
</tr>
<tr>
<td>Delivery Medium</td>
<td>:</td>
<td><?=$order->medium==""?"n/a":$order->medium;?></td>
</tr>
</table>
</div>

<div style="padding:10px 0px;">
<a href="<?=site_url("callcenter/trans/".$order->transid)?>" target="_blank">Check PG details</a>
</div>

 


</div>
</div>
</div>