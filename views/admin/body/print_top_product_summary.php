<?php 
if($type == 1)
{
	$this->erpm->export_csv("Products",$products); 
}
else if($type == 0)  
{
?>
	<html>
		<head>
		<title>Top Products Summary</title>
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
			.ext_data
			{
				 margin: 14px 0;
			}
			.ext_data span
			{
				margin: 0 20px 0 0;
			}
			.ext_data b
			{
				
			}
		</style>
		</head>
		<!--<body onload="window.print()">-->
		 	<body>
		 		<h1 style="text-align: center;">
		 			<?php
		 			$menu_type_title_cond='';
					$sales_type_title_cond='';
		 				if($menu_type=='1')
						{
							$menu_type_title_cond=' SK ';
						}else if($menu_type=='0')
						{
							$menu_type_title_cond=' SIT ';
						}else 
						{
							$menu_type_title_cond=' ';
						} 
						if($sales_type=='new')
						{
							$sales_type_title_cond=' newly added ';
						}
						
						if($days==60)
						{
							echo "Top".$sales_type_title_cond."".$menu_type_title_cond."Product Sales Summary for 60 Days";
						}else if($days==30)
						{
							echo "Top".$sales_type_title_cond."".$menu_type_title_cond."Product Sales Summary for 30 Days";
						}else if($days==7)
						{
							echo "Top".$sales_type_title_cond."".$menu_type_title_cond."Product Sales Summary for 1 Week";
						}else if($days=='all')
						{
							echo "Top".$sales_type_title_cond."".$menu_type_title_cond."Top Product Sales Summary";
						}
					?>
		 		</h1>
			
			<div align="right" class="hideinprint">
				<input type="button" value="Print" onClick="window.print()" >
				<input type="button" value="Close" onClick="window.close()" >
			</div>	
			<div id="container">
			<table class="print_tbl" border="0" cellspacing="0" cellpadding="4" width="100%" >
				<thead>
					<tr>
						<Th>PNH ID</Th>
						<Th>Product ID</Th>
						<th align="center" width="120px">Product</th>
						<th align="center" width="">Menu</th>
						<th align="left" width="">Category</th>
						<th align="left" width="">Brand</th>
						<th align="center">MRP</th>
						<th align="center">Offer Price</th>
						<th align="right">
							<?php
								if($days==60)
								{
									echo "60 day Sales";
								}else if($days==30)
								{
									echo "30 day Sales";
								}else if($days==7)
								{
									echo "1 week Sales";
								}else if($days=='all')
								{
									echo "Total Sales";
								}
							?>
						</th>
						<th align="right">Current Stock</th>
						<th align="right">Expected Stock</th>
					</tr>
				</thead>
				<tbody>
				<?php 
					$i=1;
					foreach($products as $p){
				?>
					<tr>
						<td><?=$p['pnh_id']?></b></td>
						<td><?=$p['product_id']?></b><br /><?php if($p['is_sourceable'] == 1){?> <span style="color:green">Sourceable</span> <?php } else if($p['is_sourceable'] == 0){ ?> <span style="color:red">Not Sourceable</span> <?php } ?></td>
						<td width="180px">
							<b><?=$p['product_name']?></b>
						</td>	
						<td style="text-align:left !important"><?=$p['menu']?></td>
						<td>
							<?=$p['cat_name']?>
						</td>
						<td>		
							<?=$p['brandname']?>
						</td>
						<?php $offer_price=$this->db->query("select de.price as price FROM m_product_deal_link d
												JOIN m_product_info a ON d.product_id = a.product_id
												JOIN king_dealitems de ON de.id = d.itemid 
												where a.product_id=?",$p['product_id'])->row()->price; ?> 
						<td align="right"><?=format_price($p['mrp'],0)?></td>
						<td align="center"><?=format_price($offer_price,0)?></td>
						<td align="center"><?=$p['sold_qty']?></td>
						<td align="center"><?=$p['stk']?></td>
						<td align="center"><?=$p['exp_stk']?></td>
					</tr>
				<?php } 	
				?>	
				</tbody>
			</table>
		</div>
	</body>
	</html>	
<?php 
}
?>