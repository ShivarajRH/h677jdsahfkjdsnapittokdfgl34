<style>
h1,h2,h3,h4{
margin:0px;
}
table{
	font-size:inherit;
}
table th{
text-align:left;
background:#bbb;
}
</style>
<div style="font-size:12px;font-family:arial;">
<h1 style="margin:0px;font-size:300%">Local Cube</h1>

<div style="border-top:1px solid #000;padding-top:10px;">
	<table style="float:right;" cellpadding=3 cellspacing=0 border=1>
	<tr><td>Invoice Number :</td><td width="150"><?=$invoice['invoice_no']?></td></tr>
	<tr><td>Invoice Date :</td><td><?=date("d.m.Y",strtotime($invoice['invoice_date']))?></td></tr>
	<tr><td>Due Date :</td><td>Immediate</td></tr>
	</table>
	<h4>Local Cube Commerce Pvt Ltd.</h4>
	No.
	1096, 16th Cross, 25th Main,<br>
	Banashankari 2nd Stage,<br>
	Bangalore : 560 070
</div>

<table style="margin:20px 0px;clear:both;" width="100%">
<tr>
<td valign="top" width="50%" align="left">
	<table cellpadding=3 cellspacing=0 border=1 width="400">
	<tr><th colspan="100%">BILL TO</th></tr>
	<tr><td width=100>Name :</td><td><?=$invoice['client_name']?></td></tr>
	<tr><td>Contact :</td><td><?=$invoice['contact_name']?></td></tr>
	<tr><td>Address :</td><td><?=$invoice['address_line1']?> <?=$invoice['address_line2']?> <?=$invoice['locality']?> </td></tr>
	<tr><td>City, State :</td><td><?=$invoice['city_name']?>, <?=$invoice['state_name']?></td></tr>
	<tr><td>TIN No :</td><td><?=$invoice['vat_no']?></td></tr>
	</table>
</td>
<td valign="top" align="right">
	<table cellpadding=3 cellspacing=0 border=1 width="400">
	<tr><th colspan="100%">REMIT PAYMENT TO</th></tr>
	<tr><td>
	<h4>Local Cube Commerce Pvt Ltd.</h4>
	No.
	1096, 16th Cross, 25th Main,<br>
	Banashankari 2nd Stage,<br>
	Bangalore : 560 070
	</td></tr>
	<tr><td>TIN No : 29230678061</td></tr>
	</table>
</td>
</tr>
</table>

<table cellpadding=5 cellspacing=0 border=1 width="100%">
<tr><th>SI No.</th><th>Product</th><th>MRP</th><th>Offer Price</th><th>Tax %</th><th>Qty</th><th>Unit Price</th><th>Unit Tax</th><th>Amount</th></tr>
<?php $total_tax=$total_disc=0; foreach($orders as $i=>$o){
	$up=$o['offer_price']/($o['tax_percent']+100)*100;
	$ut=$o['offer_price']-$up;
	$total_tax+=($ut*$o['invoice_qty']);
	$total_disc+=($o['mrp']-$o['offer_price'])*$o['invoice_qty'];
?>
<tr>
<td><?=($i+1)?></td>
<td><?=$o['product_name']?></td>
<td align="right"><?=number_format($o['mrp'],2)?></td>
<td align="right"><?=number_format($o['offer_price'],2)?></td>
<td align="right"><?=$o['tax_percent']?></td>
<td align="right"><?=$o['invoice_qty']?></td>
<td align="right"><?=number_format($up,2)?></td>
<td align="right"><?=number_format($ut,2)?></td>
<td width=150 align="right"><?=number_format($o['offer_price']*$o['invoice_qty'],2)?></td>
</tr>
<?php }?>
<tr style="font-weight:bold"><td> &nbsp; </td><td colspan=5 align="right">Total Discount : Rs <?=number_format($total_disc,2)?></td><td> &nbsp;</td><td> &nbsp;</td><td> &nbsp;</td></tr>
<tr style="font-weight:bold"><td> &nbsp;</td><td colspan=6 align="right">Total VAT Collected : Rs <?=number_format($total_tax,2)?></td><td> &nbsp;</td><td> &nbsp;</td></tr>
</table>
<table cellspacing=0 cellpadding=5 style="float:right;font-weight:bold;font-size:13px;">
<tr><td style="border:1px solid #000;border-color:inherit;border-width:0px 2px 2px 2px;width:200px;" align="center">Total</td><td style="border:1px solid #000;border-width:0px 2px 2px 0px;border-color:inherit;" width="150" align="right"><?=number_format($invoice['total_invoice_value'],2)?></td></tr>
</table>

<div style="clear:both;padding:30px 0px 20px 0px;">
<table cellpadding=2 cellspacing=0 border=1 width="100%" style="font-size:120%">
<tr><th colspan="100%">Approved :</th></tr>
<tr><td style="padding-top:20px;"> For Local Cube Commerce Pvt Ltd</td><td style="padding-top:20px;">Sridhar G</td><td style="padding-top:20px;"><?=date("d.m.Y",strtotime($invoice['invoice_date']))?></td></tr>
<tr><th> &nbsp;</th><th> &nbsp;</th><th> Date&nbsp;</th></tr>
</table>
</div>


<b>If you have any questions regarding this invoice, please contact:</b>
<table cellpadding=2 cellspacing=0 border=1 width="100%">
<tr><td>Sridhar G</td><td> &nbsp;</td><td> &nbsp;</td></tr>
<tr><th>Name :</th><th>Phone :</th><th>Email :</th></tr>
</table>

</div>
<script>
window.print();
</script>
<?php
