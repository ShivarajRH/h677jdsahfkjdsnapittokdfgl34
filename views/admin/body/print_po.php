<?php 
	$print_type = $this->uri->segment(4);
?>
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
			.clear{clear:both}
		</style>
	</head>
	<body onload="window.print()">
		<h1 style="text-align: center;">Purchase Order - <?php echo (($print_type=='acct')?'Accounts Copy':'Sourcing Copy')?></h1>
		
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
			
			<div align="center" >
				<table class="print_tbl" border="0" cellspacing="0" cellpadding="4" width="100%" >
					<thead><tr><th>Slno</th><th style="text-align: left">Product name</th>
					<?php if($print_type == 'acct'){?>
					<th style="text-align: right">Sold Qty <small>[30 Days]</small></th>
					<th style="text-align: right">Sold Qty <small>[60 Days]</small></th>
					<th style="text-align: right">Partner Sold Qty <small>[60 Days]</small></th>
					<th style="text-align: right">Available <small>[Partner]</small> Qty</th>
					<th style="text-align: right">Available Qty</th>
					<th style="text-align: right">Required Qty</th>
						<th style="text-align: right">Mrp</th>
						<th style="text-align: right">DP Price</th>
						<th style="text-align: right">Unit price</th>
						<th>Margin (%)</th>
					<?php 
						}
						else
						{ 
					?>
						<th style="text-align: right">MRP</th>
					<?php } ?>
					<th style="text-align: right">PO Qty</th>
					<?php if($print_type == 'acct'){?>
						<th style="text-align: right">Sub Total </th>
					<?php } ?>
					</tr></thead>
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
								
								$po['partner_sales_60days']=$this->partner->get_partner_sales($po['product_id'],$po['created_on'],2); // 2 months sale
								//$po['partner_sales_30days']=$this->partner->get_partner_sales($po['product_id'],$po['created_on'],1); // 1 months sale
								$po['partner_avilable']=$this->partner->get_partner_avail_stock($po['product_id']);
								
								$po['sales_60days']=$this->erpm->get_sales_count($po['product_id'],$po['created_on'],2); // from 2 month sales
								$po['sales_30days']=$this->erpm->get_sales_count($po['product_id'],$po['created_on'],1); // from 1 month sales
								
								$po['pen_ord_qty']=$this->db->query("select ifnull(sum(o.quantity*l.qty),0) as s from m_product_deal_link l join king_orders o on o.itemid=l.itemid where l.product_id=? and o.status = 0 and o.time < ? ",array($po['product_id'],strtotime($po['created_on'])))->row()->s;
								
								$prd_stock = $this->erpm->get_product_stock($po['product_id']);
								$po['cur_avail_qty'] =  $prd_stock['current_stock'];
								
					?>
								<tr>
									<td width="1%" style="text-align:center;" width="10"><?=$i;?></td>
									<td><?=$po['product_name'] ?></td>
									<?php if($print_type == 'acct'){?>
									<td width="1%" style="text-align:right;"><?=$po['sales_30days'] ?></td>
									<td width="1%" style="text-align:right;"><?=$po['sales_60days'] ?></td>
									<td width="1%" style="text-align:right;"><?=$po['partner_sales_60days'] ?></td>
									<td width="1%" style="text-align:right;"><?=$po['partner_avilable'] ?></td>
									<td width="1%" style="text-align:right;"><?=$po['cur_avail_qty'] ?></td>
									<td width="1%" style="text-align:right;"><?=$po['pen_ord_qty'] ?></td>
									<td width="1%" style="text-align:right;"><?=format_price($po['mrp'])?></td>
									<td width="1%" style="text-align:right;"><?=format_price($po['dp_price'])?></td>
									<td width="1%" style="text-align:right;"><?=format_price($po['purchase_price'])?></td>
									<td width="1%" style="text-align:right;"><?=($po['margin']+$po['scheme_discount_value'])?></td>
									<?php }else{
									?>
									<td width="1%" style="text-align:right;"><?=format_price($po['mrp'])?></td>
									<?php 	
									} ?>
									<td width="1%" style="text-align:right;"><?=$po['order_qty'] ?></td>
									<?php if($print_type == 'acct'){?>
									<td width="1%" style="text-align:right;"><?=format_price($po_p_price)?></td>
									<?php }?>
								</tr>
					<?php 
								$i++;
							}
						}
					?>
					<tfoot>
						<tr>
							<td colspan="<?php echo (($print_type == 'acct')?12:5);?>" align="right"><b>Total</b></td>
							<td align="right"><b><?php echo format_price($ttl_order_qty,0);?></b></td>
							<?php if($print_type == 'acct'){?>
							<td align="right"><b><?php echo format_price($ttl_order_price,2);?></b></td>
							<?php }?>
						</tr>
					</tfoot>
					</tbody>
				</table>
			</div>
			
			<div class="clear" style="margin-top:10px;">
				<div align="right"  class="fl_right">
				<?php if($print_type == 'acct'){?>
					Total Purchase Order Value : <b> Rs <?=format_price($ttl_order_price,2) ?></b>
				<?php }?>
				</div>
				<div align="left" class="fl_left" style="width: 300px;">
					<b>PO Remarks:</b>
					<p><?=$po_det['remarks'] ?></p>
				</div>
				<div align="left" class="fl_left">
					<?php if($print_type == 'acct'){?>
					Please note : Tax already included in price.
					<?php } ?>  
				</div>
			</div>
			<br>
		</div>
	</body>
</html>