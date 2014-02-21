<html>
	<head>
		<title>Warehouse Summary</title>
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
	 
		<h1 style="text-align: center;">Warehouse Location Summary for <?=$po_brand_name?></h1>
		<div align="right" class="hideinprint">
			<input type="button" value="Print" onClick="window.print()" >
			<input type="button" value="Close" onClick="window.close()" >
		</div>	
		<div id="container">
		<table class="print_tbl" border="0" cellspacing="0" cellpadding="4" width="100%" >
			<thead><tr><Th>Sno</Th><th align="left">ProductID</th><th align="left">Product Name</th><th align="right">MRP</th><th align="center">Stock Qty</th><th align="right">MRP Value</th><th align="right">Avg <br /> Purchase <br />Price</th><th align="right">Avg <br /> Total <br />purchase</th></tr></thead>
			<tbody>
			<?php 
				$t_sv=$t_avg=0;$i=1; $qty_t = 0; 
				foreach($products as $p){
			?>
				<tr>
					<td><?=$i++?></td>
					<td><?=$p['product_id']?></td>
					<td style="text-align:left !important"><?=$p['product_name']?></td>
					<td align="right"><?=format_price($p['mrp'],0)?></td>
					<td align="center">
						<?=$p['stock']*1?>
						<?php
							$p_mrpstk_arr = $this->db->query("select mrp,sum(available_qty) as qty from t_stock_info where product_id = ? and available_qty > 0 group by mrp order by mrp asc ",$p['product_id'])->result_array();
							if($p_mrpstk_arr)
							{
								echo '<div style="background:#ffffa0;font-size:10px;padding:2px 5px;min-width:60px;border-radius:3px;text-align:left">';
								foreach($p_mrpstk_arr as $p_mrpstk)
									echo '<div class="clearboth"><b>Rs '.format_price($p_mrpstk['mrp'],0).'</b>  <b class="fl_right" style="float:right">'.($p_mrpstk['qty']).'</b></div>';
								echo '</div>';
							}
							
							$qty_t += $p['stock']*1;
						?>
					</td>
					<td align="right"><?=format_price($p['stock_value'],0)?></td>
					<td align="right"><?php $avg=round($this->db->query("select avg(purchase_price) as a from t_grn_product_link where product_id=?",$p['product_id'])->row()->a,2); echo format_price($avg);?></td>
					<td align="right"><?=format_price($p['stock']*$avg,2)?></td>
				</tr>
				<?php 
						$t_sv+=$p['stock_value']; 
						$t_avg+=$p['stock']*$avg; 
					}
				?>
				<tr>
					<td colspan="4" align="right">Total </td>
					<td align="center"><?=$qty_t;?> Qtys</td>
					<td align="right"><b><?=format_price($t_sv)?></b></td>
					<td></td>
					<td align="right"><b><?=format_price($t_avg)?></b></td>
				</tr>
			</tbody>
		</table>
	</div>
</body>
</html>