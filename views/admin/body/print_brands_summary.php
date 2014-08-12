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
	 	<?php if($po_brand_name){ ?>
			<h1 style="text-align: center;">Warehouse Location Summary for Brand <?=$po_brand_name?></h1>
		<?php }
			else { 
		?>
			<h1 style="text-align: center;">Warehouse Location Summary for Category <?=$po_cat_name?></h1>
		<?php } ?>	
		<div align="right" class="hideinprint">
			<input type="button" value="Print" onClick="window.print()" >
			<input type="button" value="Close" onClick="window.close()" >
		</div>	
		<div id="container">
		<table class="print_tbl" border="0" cellspacing="0" cellpadding="4" width="100%" >
			<thead>
				<tr>
					<th align="left">Sl.No</th>
					<th align="left">ProductID</th>
					<th align="left">Product Name</th>
					<th align="center">Stock Qty</th>
					<th align="center">Sourceable</th>
					<th align="right">DP</th>
					<th align="right">15day sales</th>
					<th align="right">30day sales</th>
					<th align="right">60day sales</th>
				</tr>
			</thead>
			<tbody>
			<?php 
				$t_sv=$t_avg=0;$i=1; $qty_t = 0;$k=1; 
				foreach($products as $p){
			?>
				<tr>
					<td><?=$k++?></td>
					<td><?=$p['product_id']?></td>
					<td style="text-align:left !important"><?=$p['product_name']?></td>
					
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
					<td>
						<?php 
							if($p['is_sourceable'] == 1)
							{
								echo '<span style="color:green">Sourceable</span>';
							}
							else 
							{
								echo '<span style="color:red">Not Sourceable</span>';
							}
						
					
						?>
					</td>	
					<td align="right"><?=format_price($p['price'],0)?></td>
					
					<td align="right"><?=$this->db->query("SELECT IFNULL(SUM(o.quantity*d.qty),0) AS o 
										FROM m_product_deal_link d
										JOIN m_product_info a ON d.product_id = a.product_id  
										LEFT JOIN king_orders o ON o.itemid = d.itemid AND o.time > (UNIX_TIMESTAMP()-(24*15*60*60))  
										WHERE a.product_id=".$p['product_id']."
										GROUP BY d.id ")->row()->o ?></td>
					<td align="right"><?=$this->db->query("SELECT IFNULL(SUM(o.quantity*d.qty),0) AS o 
										FROM m_product_deal_link d
										JOIN m_product_info a ON d.product_id = a.product_id  
										LEFT JOIN king_orders o ON o.itemid = d.itemid AND o.time > (UNIX_TIMESTAMP()-(24*30*60*60))  
										WHERE a.product_id=".$p['product_id']."
										GROUP BY d.id ")->row()->o ?></td>
					<td align="right"><?=$this->db->query("SELECT IFNULL(SUM(o.quantity*d.qty),0) AS o 
										FROM m_product_deal_link d
										JOIN m_product_info a ON d.product_id = a.product_id  
										LEFT JOIN king_orders o ON o.itemid = d.itemid AND o.time > (UNIX_TIMESTAMP()-(24*60*60*60))  
										WHERE a.product_id=".$p['product_id']."
										GROUP BY d.id ")->row()->o ?></td>
				</tr>
				<?php 
						$t_sv+=$p['stock_value']; 
						$t_avg+=$p['stock']*$avg; 
					}
				?>
				<tr>
					<td colspan="2" align="right">Total </td>
					<td align="center"><?=$qty_t;?> Qtys</td>
					<td align="right"><b><?=format_price($t_sv)?></b></td>
					<td></td><td></td><td></td>
				</tr>
			</tbody>
		</table>
	</div>
</body>
</html>