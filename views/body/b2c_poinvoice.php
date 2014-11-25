<div class="container" style="background: #fff;">
	<div style="width: 100%; margin: 0px auto">
		<div style="margin: 10px;" class="hideinprint">
			<table width="100%">
			</table>
		</div>
	</div>
</div>
<div id="invoice">
<style>
table{
	font-size:12px;
}
.showinprint{
		display: none;
}

@media print {
	.cancelled_invoice_text{
		font-size: 800% !important;
	}
	#print_inv_msg{
		display:none;
	}
	.hideinprint{
		display:none;
	}
	.showinprint{
		display: block;
	}
}
</style>
<?php 
$p_tax_list = array();
$p_tax_amount_list = array();
$orderslist_byproduct = array();

foreach($invoice_list as $inv_det)
{
	
	$invoice_no = $inv_det['invoice_no'];
	
	

	$sql="SELECT in.invoice_no,item.nlc,item.phc,ordert.*,
			item.service_tax_cod,item.name,IF(LENGTH(item.print_name),item.print_name,item.name) AS print_name,in.invoice_no,item.member_price,
			brand.name AS brandname,
			in.mrp,in.tax AS tax,
							in.discount,
							in.phc,in.nlc,
							in.service_tax,
			item.pnh_id,invoice_status,
			in.invoice_qty AS quantity
			FROM t_mps_po_product_link AS ordert
			JOIN t_mps_po_info p ON p.id=ordert.po_id
			JOIN king_dealitems AS item ON item.id=ordert.itemid 
			JOIN king_deals AS deal ON deal.dealid=item.dealid 
			JOIN king_brands AS brand ON brand.id=deal.brandid 
			JOIN king_invoice `in` ON in.transid=p.transid AND in.order_id=ordert.order_id  
			WHERE in.invoice_no=?";
	$q=$this->db->query($sql,array($invoice_no));
	//echo $this->db->last_query();die();
	$orders=$q->result_array();
	
	$order=$orders[0];
	$sellerid=$inv_det['seller_id'];
	$seller_det=$this->db->query(" SELECT id,name AS seller_name,address AS seller_address,city,pincode FROM m_seller_info where id=?",$sellerid)->row_array();
	$inv_createdon=$inv_det['invoiced_on'];
	$po_id=$inv_det['po_id'];
	$po_createdon=$inv_det['created_on'];
	$transid=$inv_det['transid'];
	$is_pnh=$this->db->query("select is_pnh as p from king_transactions where transid=?",$transid)->row()->p;
	

?>
<div class="invoice" style="padding:10px;page-break-after:always"> 
	<div style="font-family:arial;font-size:12px;">
		<div style="font-family:arial;font-size:13px;padding-top:10px;">
			<?php if($order['invoice_status']==0){?>
				<div><h1 style="margin:0px;border:1px solid #000;padding:3px;background:#eee;">CANCELLED INVOICE</h1></div>
			<?php }?>	
			
			<div class="inv_logo_area">
				<div style="border-bottom:2px solid #000;padding:5px;font-weight:bold;text-align:center;overflow: hidden;margin-top:20px;" align="center">
					 <?php if($invdet['total_prints'] > 1){ ?>
					 <span style="float: right;font-size:32px;vertical-align: top;font-weight: normal;margin-top: -10px"><?php echo $invdet['total_prints'] ?></span>
					 <?php } ?>
					 TAX INVOICE
				</div>
				<div class="top_tax_inv_bar" style="border-bottom:2px solid #555;padding:15px;min-height: 40px;">
					
					<table width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td  align="left">
								
								<img class="pnh_logo" src="<?=IMAGES_URL?>paynearhome.png">
								
							</td>
							<td align="center" style="display: none">
								<?php if($awb_img){?>
								<img style="margin-top: -7px;margin-right:40px;" src="data:image/png;base64,<?=base64_encode($awb_img);?>" />
								<?php }?>
							</td>
							<td align="right">
								<img style="margin-top: -7px;" src="data:image/png;base64,<?=base64_encode($barcode_img_data);?>" />
							</td>
						</tr>	
					</table>
				</div>
			</div>

			<table width="100%" style="margin-top:10px">
				<tr>
					<td valign="top">
					<?php 
						$cin_no = '';
						$service_no = '';
						$tin_no = '';
						
						$cin_no = 'U51909KA2012PTC063576';
						$tin_no = '29230678061';
						$service_no = 'AACCL2418ASD001';
						echo 'LocalCube Commerce Pvt Ltd<br>Plot 3B1,KIADB Industrial Area,Kumbalagudu 1st Phase,Mysore Road,Bangalore -560074';
						?>
						</td>
						<td align="right" valign="top">
						<table border=1 cellspacing	=0 cellpadding=5>
							<tr><td>Invoice<br>No:</td>
							<td width=200><b><?=isset($invoice_no)?$this->uri->segment(3):$order['invoice_no']?></b><br>
							
							</td>
							
							<td>Invoice<br>Date:</td><td width="100"><b><?=date("d/m/Y",$inv_createdon)?></b></td>
							<td>Purchase Order<br>
							<div align="center">ID/Date :</div></td><td width="100"><b><?=$po_id.'('.$transid.')';?></b> 
								<br />
								(<?php echo date('dM',$po_createdon)?>)
								<span style="font-size: 8px;">
								<?php
									
								?>
								</span>
							</td>
							</tr>
						</table>
					</td>
					</tr>
					</table>
					
					<table width="100%" cellpadding=5 style="margin-bottom:5px;margin-top:5px;">
					<tr>
						<td width="100%" valign="top" style="padding:0px;">
							<table cellspacing=0 border=1 cellpadding=3 width="100%">
								<tr>
									<th width="100">BILL TO</th>
									<th width="400"><?=$seller_det['seller_name']?></th>
								</tr>
								<tr><td><b>Address :</b></td><td colspan="3"><?=nl2br($seller_det['seller_address'])?>, <?=$seller_det['city']?> <?=$seller_det['state']?> - <?=$seller_det['pincode']?> 
								
							</table>
						</td>
					</tr>
					</table>
	<?php }?>
	<table cellspacing=0 cellpadding=5 border=1 width="100%" style="margin-top:10px;">
				<tr>
					
					<td><b>SLno</b></td>
					
					<td width="<?=$is_pnh?"70":"45"?>%"><b>Product Item Name</b></td>
					
					<td align="center" width="80" ><b>MRP</b></td>
						
					<!-- <td align="right"><b>Discount</b></td> -->
						
					<td align="center" width="70"><b>Price</b></td>
					
					
					<td align="center" width="50" ><b>VAT (%)</b></td>
					<td align="center" width="70"><b>Qty</b></td>
					
					
					
					<td align="right" width=100><b>Total</b></td>
				</tr>
				
	<?php 
		$i=1;
		$tpc_tax=0;
		$tpc=0;
		foreach($orders as $order)
		{
	
		$consider_item_total = 1;
		$ptax=$order['tax']/100;
		$p1tax=$order['tax'];
		$qty=$order['quantity'];
		//$order['nlc']=$order['dp_price']-$order['margin'];
		$mrp_total += round($order['mrp']*$qty,2);
		$tpc += $product_rate = round(($order['dp_price']-$order['margin'])*$qty*100/(100+$ptax),2);
		$item_disc=round($consider_item_total*($order['margin']),2);
		$purchaseval=$order['dp_price']-$order['margin'];
		$item_purchasettl=$purchaseval*$qty;
		$total_item_amount += $item_purchasettl;
		
		$invdet=$this->db->query("select service_tax,ifnull(giftwrap_charge,0) as giftwrap_charge,cod,ship,invoice_status,transid,is_partial_invoice,createdon,total_prints
										from king_invoice
										where invoice_no=? ",$order['invoice_no'])->row_array();

		$pstax = $invdet['service_tax']/100;
		
		$trans_total=$this->db->query("select amount as t,cod,ship from king_transactions where transid=?",$order['transid'])->row_array();
		$cod_ship_charges = 0;
		$sgc = 0;
		if($trans_total['ship']){
			$ship = $ship+$giftwrap_charge;
			$sship=$ship*100/(100+$pstax);
			$thc+=$ship;
			$cod_ship_charges = $ship;
		}else if($trans_total['cod'] && $order['mode']==1){
			$cod = $cod+$giftwrap_charge;
			$ccod=$cod*100/(100+$pstax);
			$thc+=$cod;
			$cod_ship_charges = $cod;
		}else{
			if($giftwrap_charge){
				$gc = $giftwrap_charge;
				$sgc=$gc*100/(100+$pstax);
				$thc+=$gc;
				$cod_ship_charges = $gc;
			}
		}
		$stax_tot = ($sship+$ccod+$sgc);
		$s_tax_apl = ($stax_tot*$pstax/100);
		
		if(!isset($p_tax_list[$p1tax])){
			$p_tax_list[$p1tax] = 0;
			$p_tax_amount_list[$p1tax] = 0;
		}
		//$tpc_tax += $product_rate_tax = round(($order['i_price']-MARKET_PLACE_SELLER_PROFIT_VAL*$qty-$product_rate),2);
		$tpc_tax += $product_rate_tax = round(($order['dp_price']-$order['margin']*$qty)-$product_rate,2);
		$p_tax_list[$p1tax] += $product_rate_tax;
		$p_tax_amount_list[$p1tax] += $product_rate;
		
		if(!isset($orderslist_byproduct[$order['itemid']]))
		{
			$orderslist_byproduct[$order['itemid']] = array('det'=>array('name'=>$order['name'],'print_name'=>$order['print_name'],'itemid'=>$order['itemid'],'pnh_id'=>$order['pnh_id'],'order_product_id'=>$order['order_product_id']),'qty'=>0,'amt'=>0,'invs'=>array());
			$orderslist_byproduct[$order['itemid']]['qty'] += $order['quantity'];
			$orderslist_byproduct[$order['itemid']]['amt'] += $item_total_amount;
			array_push($orderslist_byproduct[$order['itemid']]['invs'],$invoice_no);
		}
		?>
			<tr>
			
			<td><?php echo $i;?></td>
			<td><?=$order['name']?>
			<?php 
					$itmid = $order['itemid'];
					$i_inv = $order['invoice_no'];
					//echo implode(', ',$itm_ord['invs']);
					$imei=$this->db->query("select imei_no from t_imei_no where order_id=? and is_returned=0",$order['order_id'])->result_array(); $inos=array(); foreach($imei as $im) $inos[]=$im['imei_no'];						
					 if(!empty($inos)){
						echo '<br><b>Imeino: '.implode(", ",$inos).'</b>';
						}
					
				
				?>
			</td>
			<td><?=$order['mrp']?></td>
			<td><?=$purchaseval;?></td>
			<td><?=$ptax ?></td>
			<td><?=$qty ?></td>
			<td><?=($purchaseval)*$qty; ?></td>
			
			</tr>
	<?php  $i++;}?>
			<tr style="font-weight: bold;">
				<td colspan="6" align="right">
					&nbsp; 
				</td>
				<td align="right" ><?=number_format($total_item_amount,2)?></td>
			</tr>
			
				
		</table>
		<table width="100%" class="tax_block_content" style="margin-top:5px;">
			<tr>
				<td width="50%">
					<div style="margin-right:10px;">
						<table cellspacing=0 border=1 cellpadding=2 >
							<tr>
								<td width="100">VAT/TIN No</td>
								<td width="300" align="center"><?php echo $tin_no;?></td>
							</tr>
							<tr>
								<td>Service Tax No</td>
								<td align="center"><?php echo $service_no;?></td>
							</tr>
							<tr>
								<td>CIN No</td>
								<td align="center"><?php echo $cin_no;?></td>
							</tr>
						</table>
					</div>
					</td>
				</tr>
				<tr>
				<td>
				<table cellspacing=0 cellpadding=5 border=1 style="margin:10px 0px;" width=400>
						<?php 
							foreach($p_tax_list as $ptax_t=>$ptax_a){
						?> 
						<tr>
							<td>Total VAT collected @ <b><?=number_format($ptax_t/100,2)?>%</b> on <b>Rs <?=number_format($p_tax_amount_list[$ptax_t],2)?></b></td>
							<td align="right"><b>Rs <?=number_format($ptax_a,2)?></b></td>
						</tr>
						<?php } ?> 
						<?php if($s_tax_apl){ ?>
						<tr>
							<td>Total Service Tax collected @ <b><?=number_format($pstax,2)?>%</b> on <b>Rs <?=number_format($stax_tot,2)?></b></td>
							<td align="right"><b>Rs <?=number_format($s_tax_apl,2)?></b></td>
						</tr>
						<?php } ?>
						
				</table>
				</td>
				<td>&nbsp;</td>
				
				<td align="right" valign="top">
					<table cellspacing=0 border=1 cellpadding=5 style="border-top:0px;" >
						<tr>
							<td width="180"><b>Total Amount </b></td>
							
							<td align="right" ><b>Rs. <?=$total_item_amount?></b></td>
						</tr>
					</table>
				</td>
				</tr>
			</table>
			<div style="padding:5px 0px 10px 0px;font-size:10px;margin-left: 5px;">
			<b>Terms &amp; Conditions</b>
			<ol>
				<li>All Disputes Subject to Bangalore Jurisdiction</li>	
				<li>Goods once sold will not be taken back or exchanged</li>	
				<li>Guarantee / Warranty should be claimed from the Brand Only</li>
				<li>Prices Mentioned above are After Discount/Offer if any</li>
				<?php if($is_pnh){?>
				<li>Cheque to be issued in the name of 'Local Cube Commerce Pvt Ltd'</li>
				<?php }?>
			</ol>
			<div class="eoe_txt" style="padding-top:5px;padding-left:200px;">E &amp; O.E.</div>
		</div>
	</div>