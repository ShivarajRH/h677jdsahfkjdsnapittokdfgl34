<?php 
$statuses=array("pending","process","ship","canceled");
?>
<style>
table{
font-size:11px;
font-family:arial;
}
h3{
font-family:arial;
margin-bottom:0px;
}
body{
margin:0px;
padding:0px;
font-size:11px;
font-family:arial;
}
</style>
<div style="float:right;font-size:11px;"><?=date("g:ia d/m/y")?></div>
<h3><?=ucfirst($status)?> orders <?php if(isset($readytoship) && $readytoship){?>readytoship<?php }?> <?php if(!empty($from)){?>from <?=date("d-m-y",$from)?> to <?=date("d-m-y",$to)?><?php }?></h3>
<table width="100%" cellpadding=3 cellspacing=0 border=1>
<tr>

<th width="20">No</th>
<th width="60">Transid</th>
<th width="300">Item Name</th>
<th width="10">QTY</th>
<th width="60">Status</th>
<th width="60">Price</th>
<th width="60">MRP</th>
<th width="200">Remarks</th>
<th width="100">Order Time</th>

</tr>
<?php foreach($list as $l){?>
<tr>
<td><?=$l[0]['si']?></td>
<td><?=$l[0]['transid']?></td>
<td style="padding:0px;">
<table width="100%" cellpadding=3 border=1 cellspacing=0 style="border:0px;">
<?php foreach($l as $o){?>
<tr>
<td><?=$o['name']?>

<?php $buyer_options=unserialize($o['buyer_options']);
if(is_array($buyer_options))
foreach($buyer_options as $mean=>$opt){?>
(<b>Buyer options : </b><?=$mean?>:<?=$opt?>),
<?php }?>

</td>
</tr>
<?php }?>
</table>
</td>


<td style="padding:0px;">
<table width="100%" cellpadding=3 border=1 cellspacing=0 style="border:0px;">
<?php foreach($l as $o){?>
<tr>
<td><?=$o['quantity']?></td>
</tr>
<?php }?>
</table>
</td>



<td style="padding:0px;">
<table width="100%" cellpadding=3 border=1 cellspacing=0 style="border:0px;">
<?php foreach($l as $o){?>
<tr>
<td><?=$statuses[$o['status']]?></td>
</tr>
<?php }?>
</table>
</td>

<td style="padding:0px;">
<table width="100%" cellpadding=3 border=1 cellspacing=0 style="border:0px;">
<?php foreach($l as $o){?>
<tr>
<td><?=$o['price']?></td>
</tr>
<?php }?>
</table>
</td>
<td style="padding:0px;">
<table width="100%" cellpadding=3 border=1 cellspacing=0 style="border:0px;">
<?php foreach($l as $o){?>
<tr>
<td><?=$o['orgprice']?></td>
</tr>
<?php }?>
</table>
</td>
<td style="padding:0px;">
<table width="100%" cellpadding=3 border=1 cellspacing=0 style="border:0px;">
<?php foreach($l as $o){?>
<tr>
<td>&nbsp;</td>
</tr>
<?php }?>
</table>
</td>

<td>
<?=date("d/m/y g:ia",$o['time'])?>
</td>

</tr>
<?php }?>
</table>
<script>
	window.print();
</script>
<?php
