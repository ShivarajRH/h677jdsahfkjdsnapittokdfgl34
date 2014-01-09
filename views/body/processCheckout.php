<div class="heading">
<div class="headingtext container">Purchased</div>
</div>
<style>
th {
	text-align: left;
	padding: 3px;
	padding-top: 5px;
}

td {
	padding-top: 5px;
	padding-left: 5px;
	height:25px;
}
</style>

<div class="container" style="padding-top:15px;font-family:arial;">
The following items purchased
<div style="margin:10px 20%;font-family:arial;font-size:14px;">
<table width="100%" cellpadding="0" cellspacing="0"
	style="border: 1px solid #bbb; background: #eee;">
	<tr style="background: #ccc; font-weight: bold;">
		<th>Item Name</th>
		<th>Quantity</th>
		<th>Price</th>
	</tr>
	<?php
	$total=0;
	foreach($items as $item)
	{
		echo "<tr><td>{$item['name']}</td><td>{$item['qty']}</td><td>".($item['price']*$item['qty'])."</td></tr>";
		$total+=$item['price']*$item['qty'];
	}
	?>
</table>
<div align="right" style="padding-top:3px;">Total : <b>Rs <?=$total?></b></div>
You will be contacted by our representative to confirm this order
</div>