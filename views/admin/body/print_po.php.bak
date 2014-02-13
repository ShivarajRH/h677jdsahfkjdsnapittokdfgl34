<html>
	<head>
		<title>Po Print</title>
		<style>
			body{font-family: arial;margin:10px;}
			.print_tbl{font-size: 14px;}
			.print_tbl th{font-size: 14px;padding:5px}
			.print_tbl td{font-size: 12px;vertical-align: top;}
			.task_info{margin:0px;}
			.task_info .task_town_name{border-bottom: 1px solid #000;text-align: center;padding:3px;}
			.task_info .task_name{border-bottom: 1px solid #000;text-align: center;padding:3px;}
			.print_tbl table{font-size: 14px;border-collapse: collapse;border-collapse: collapse;margin-bottom: 5px;}
			.print_tbl table th{font-size: 12px;}
			.print_tbl table td{padding:5px;}
			.tsk_blck{margin:5px;}
		</style>
	</head>
	<body onload="window.print()">
	 <div id="header">
			<input style="float: right;" type="button" value="Close" onClick="window.close()" class="hide" id="noprint">
			<input style="float: right;margin-right: 10px;" type="button" value="Print" onClick="window.print()" class="hide" id="noprint" >
	</div>	
			
		
<div id="container">
<div align="left">
<h3 style="text-align: center;">Purchase Order</h3> 
<br><br>
<b style='font-size: 11px;'>Purchase From</b><br>
<b><?= $vendor_details['vendor_name'];?></b>
<b style="font-size: 12px;margin-top:29px;margin-left:-140px;"><?=ucwords($vendor_details['address_line_1']) . $vendor_details['address_line_2'].'<br>'.ucwords($vendor_details['city_name']).$vendor_details['post_code'] ?></b><br>
</div>
<div class="clear"></div>
<div align="right" >
<table>

<div align="right" style="margin-top: -50px;">

<tr><td><b style="margin-right: 4px;font-size: 11px;">POno:<?=$poid?></b></td></tr>
<tr><td><b style="margin-right: 4px;font-size: 11px;">Date:<?=format_date($po_det['created_on'])?></b></td></tr>
<tr><td><b style="margin-right: 4px;font-size: 11px;">Expected Delivery Date:<?=format_date($po_det['date_of_delivery'])?></b></td></tr>
</div>
</table>
</div>
<div class="clear"></div>
<div align="center">
<table class="print_tbl" border=1 cellspacing="0" cellpadding="0" width="100%" >
<thead><th>Slno</th><th>Product name</th><th>Mrp</th><th>Unit price</th><th>Qty</th><th>Sub Total </th></thead>
<?php if($po_product_list){
	$i=1;
	foreach($po_product_list as $po){
?>
<tbody><tr><td style="text-align:center;"><?=$i;?></td><td><?=$po['product_name'] ?></td><td style="text-align:center;"><?=$po['mrp']?></td><td style="text-align:center;"><?=format_price($po['purchase_price'])?></td><td style="text-align:center;"><?=$po['order_qty'] ?></td><td style="text-align:right;"><?=format_price($po['purchase_price']*$po['order_qty'])?></td></tr></tbody>
<?php $i++;}}?>
</table>
</div>
<div class="clear"></div>

<div align="left" style="margin-left: 0px;font-size:12px;">
<b>Comment:</b><br>
<?=$po_det['remarks'] ?>
</div>
<div align="right" style="margin-right:2px;margin-top: -19px;">
<b>Total:Rs <?=format_price($ttl_po_value) ?></b><br>
</div>
<div >

</div>
</div>