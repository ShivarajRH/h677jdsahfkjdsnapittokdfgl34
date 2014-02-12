<html>
	<head>
		<title>Po Print</title>
		<style>
			body{font-family: arial;margin:10px;font-size: 80%;width: 970px;margin:0px auto;padding:10px;}
			@media print {.hideinprint{display: none}}
			.print_tbl{border-collapse: collapse;}
			.print_tbl th{font-size: 90%;padding:5px;border:1px solid #000;}
			.print_tbl td{font-size: 80%;vertical-align: top;padding:5px;border:1px solid #000;}
			.tsk_blck{margin:5px;}
			p{margin:4px 0px}
			.fl_left{float:left;}
			.fl_right{float:right;}
		</style>
	</head>
	<body onload="window.print()">
		<h1 style="text-align: center;">Purchase Order</h1>
		<div align="right" class="hideinprint">
			<input type="button" value="Print" onClick="window.print()" >
			<input type="button" value="Close" onClick="window.close()" >
		</div>	
		<div id="container">
			<div class="clear">
				<div align="left" class="fl_left" style="width: 35%">
					<b>Purchase From :</b>
					<p><?= $vendor_details['vendor_name'];?><?=ucwords($vendor_details['address_line_1']) . $vendor_details['address_line_2'].'<br>'.ucwords($vendor_details['city_name']).$vendor_details['post_code'] ?></p>
				</div>
				<div align="center" class="fl_left" style="width: 30%">
					&nbsp;
				</div>
				<div align="right" class="fl_right" style="width: 35%">
					<div><b>PO refno:</b> #<?=$poid?></div>
					<div><b>PO Date:</b> <?=format_date($po_det['created_on'])?></div>
					<div><b>Expected DOD:</b> <?=format_date($po_det['date_of_delivery'])?></div>
				</div>
			</div>
			<div class="clear">&nbsp;</div>
			
			<div align="center">
				<table class="print_tbl" border="0" cellspacing="0" cellpadding="4" width="100%" >
					<thead><tr><th>Slno</th><th style="text-align: left">Product name</th><th style="text-align: right">Mrp</th><th style="text-align: right">Unit price</th><th style="text-align: right">Qty</th><th style="text-align: right">Sub Total </th></tr></thead>
					<tbody>
					<?php
						$ttl_order_qty = 0; 
						$ttl_order_price = 0;
						if($po_product_list)
						{
							$i=1;
							foreach($po_product_list as $po){
								$ttl_order_qty += $po['order_qty'];
								$po_p_price = $po['purchase_price']*$po['order_qty'];
								$ttl_order_price += $po_p_price;
					?>
								<tr>
									<td width="1%" style="text-align:center;" width="10"><?=$i;?></td>
									<td><?=$po['product_name'] ?></td>
									<td width="1%" style="text-align:right;"><?=$po['mrp']?></td>
									<td width="1%" style="text-align:right;"><?=format_price($po['purchase_price'])?></td>
									<td width="1%" style="text-align:right;"><?=$po['order_qty'] ?></td>
									<td width="1%" style="text-align:right;"><?=format_price($po_p_price)?></td>
								</tr>
					<?php 
								$i++;
							}
						}
					?>
					<tfoot>
						<tr>
							<td colspan="4" align="right"><b>Total</b></td>
							<td align="right"><b><?php echo format_price($ttl_order_qty,0);?></b></td>
							<td align="right"><b><?php echo format_price($ttl_order_price,2);?></b></td>
						</tr>
					</tfoot>
					</tbody>
				</table>
			</div>
			<div class="clear" style="margin-top:10px;">
				<div align="right"  class="fl_right">
					Total Purchase Order Value : <b> Rs <?=format_price($ttl_order_price,2) ?></b>
				</div>
				<div align="left" class="fl_left">
					<b>PO Remarks:</b>
					<p><?=$po_det['remarks'] ?></p>
				</div>
			</div>
		</div>
	</body>
</html>