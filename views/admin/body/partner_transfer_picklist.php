<?php
/**
 * @author Shivaraj <shivaraj@storeking.in>_Sep_10_2014
 */
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Partner Stock Transfer Picklist</title>
	</head>
	<body onload='window.print()' style="font-family:arial;font-size:14px;">
		<div style="float:right;color:#aaa;font-size:12px;"><?=date('d/M/Y H:i:s',time());?></div>

		<h2 style="margin:5px 0px;">Partner Stock Transfer Picklist</h2>

		<!--<h4 style="margin-bottom:0px;">PNH42373 (123291)</h4>-->
		<table border=1 style="font-family:arial;font-size:13px;" cellpadding=3>
			<tr style="background:#aaa">
				<th>Product ID</th><th>Product Name</th><th>Qty</th><Th>MRP</Th><th>Location</th>
				<th>Barcodes</th>
			</tr>
	<?php
			foreach($pic_prod_lst as $prod)
			{
	?>
			<tr style="background:#eee;">
				<td width="50"><a target="_blank" href="<?php site_url("/admin/product/".$prod['product_id']);?>"><?=$prod['product_id'];?></a></td>
				<td width="250"><?=$prod['product_name'];?></td>
				<td width="30"><?=$prod['qty'];?></td>
				<td width="30"><?=$prod['mrp'];?></td>
				<td width="150"><?=$prod['rbname'];?></td>
				<td ><?=$prod['barcodes'];?></td>
			</tr>
	<?php
			}
	?>
		</table>
	</body>
</html>