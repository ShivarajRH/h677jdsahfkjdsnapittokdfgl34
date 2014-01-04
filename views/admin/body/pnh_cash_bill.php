<?php 
$this->load->plugin('barcode');
$barcode=generate_barcode($bill['bill_no']);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head><title></title>
<style>
body{
font-family:arial;
font-size:12px;
width:400px;
border:1px solid #aaa;
margin:10px;
padding:10px;
text-align:left;
}
</style>
</head>

<body>

<div align="center"><h3> </h3></div>

<div style="float:right;">
<div style="font-size:110%;">REF : <b><?=$fran['pnh_franchise_id']?>-<?=$bill['bill_no']?></b></div>
<div align="center"><img src="data:image/png;base64,<?=base64_encode($barcode);?>"></div>
</div>

<div style="padding:5px;"><b><?=$fran['franchise_name']?></b><br><?=$fran['address']?>, <?=$fran['locality']?>, <br><?=$fran['city']?> <?=$fran['postcode']?></div>

<table cellpadding=0 border=0 style="margin-top:15px;">
<tr><Th colspan="100%">Customer Details</Th></tr>
<tr><td>Customer :</td><td><?=$member['first_name']!=""?$member['first_name'].$member['last_name']:"Cash Customer"?></td></tr>
<tr><td valign="top">Addresss :</td><td><?=$member['address']?> <?=$member['city']?>
</table>

<h4 style="margin-bottom:0px;">Products</h4>
<table width="100%">
<tr><th>SNo</th><th>Product Name</th><th>Unit Price</th><th>Qty</th><th>Sub Total</th></tr>
<?php 
$g_total=0;
$t_pc=0;$t_pc_tax=0; $i=1; foreach($orders as $o){
$qty=$o['quantity'];
$tax=$o['i_tax']/100;
$t_pc += $pc = round(($o['i_price']*$qty*100/(100+$tax)),2);
$t_pc_tax += $pc_tax = round(($o['i_price']*$qty-$pc),2);
$g_total+=($o['i_price']*$qty);
?>
<tr>
<td><?=$i++?></td><td><?=$o['product']?></td><th><?=$o['i_price']?></th><th>x <?=$qty?></th><th align="right"><?=$o['i_price']*$qty?></th>
</tr>
<?php }?>
<?php /*?><tr><td colspan=3></td><td>Total</td><td><?=$t_pc?></td></tr> */ ?>
<tr><td colspan=3><div style="padding:10px;">Total Tax deducted : Rs <?=$t_pc_tax?></div></td></tr>
<tr style="font-size:120%;padding:5px;"><td colspan=1></td><td colspan=3 align="right">Grand Total</td><td><b style="white-space:nowrap;">Rs <?=$g_total?></b></td></tr>
</table>

</body>
</html>
<?php
