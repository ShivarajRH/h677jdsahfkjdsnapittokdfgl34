<?php
$transid = $orders[0]['transid'];

$partner_id=$this->db->query("select partner_id from king_transactions where transid=?",$orders[0]['transid'])->row()->partner_id;

$is_pnh=$this->db->query("select is_pnh as p from king_transactions where transid=?",$orders[0]['transid'])->row()->p;

if($is_pnh)
	$fid=$this->db->query("select t.franchise_id as fid from king_transactions t where transid=?",$orders[0]['transid'])->row()->fid;

$this->load->plugin('barcode');

$batch=$this->db->query("select courier_id,awb from shipment_batch_process_invoice_link where invoice_no=?",$invoice_no)->row_array();
$awb=$batch['awb'];
$courier=$this->db->query("select courier_name from m_courier_info where courier_id=?",$batch['courier_id'])->row_array();
if(!empty($courier))
	$courier=$courier['courier_name'];
else
	$courier="";

$barcode_img_data = generate_barcode($invoice_no,400,60,2);
$awb_img=false;
if(!empty($awb))
$awb_img=generate_barcode($awb,200,40,1);
 


$order=$orders[0];

$t_invoiceno = $invoice_no; 
	
$invdet=$this->db->query("select service_tax,ifnull(giftwrap_charge,0) as giftwrap_charge,cod,ship,invoice_status,transid,is_partial_invoice,createdon,total_prints 
								from king_invoice 
								where invoice_no=? ",$order['invoice_no'])->row_array();

 

$giftwrap_charge=$invdet['giftwrap_charge'];
$cod=$invdet['cod'];
$ship=$invdet['ship'];
$pstax = $invdet['service_tax']/100;

 
 


$tphc=$ttax=$tpc=$sship=$ccod=0;
$is_partial_invoice = $invdet['ship'];
$inv_createdon = $invdet['createdon'];
$inv_total_prints = $invdet['total_prints'];
 
/* 
$this->db->where('status !','6');
$total_orders_intrans = $this->db->count_all_results('king_orders');  

$this->db->where('invoice_no',$invdet['invoice_no']);
$total_orders_in_invoice = $this->db->count_all_results('king_invoice');  

if($total_orders_intrans){
	
} */
	
?>
<div class="container" style="background:#fff;">
<div style="width: 100%;margin: 0px auto">	
<?php if($this->session->userdata("admin_user")){?>
<div style="margin:10px;" class="hideinprint">
<table width="100%">
	<tr>
		<td align="left" width="33%"><input type="button" value="<?php echo $inv_total_prints?'RePrint':'Print' ?> invoice" onclick='printinv(this)'></td>
		<td align="center" width="33%"><input class="print_partner_orderfrm_btn" style="display: none;" type="button" value="<?php echo $inv_total_prints?'RePrint':'Print' ?> Partner Order Form" onclick='printpartorderform(this)'></td>
		<td align="right" width="33%"><input class="print_partner_orderfrm_btn" style="display: none;" type="button" value="<?php echo $inv_total_prints?'RePrint':'Print' ?> Invoice and Partner Order Form" onclick='printinvpartorderform(this)'></td>
	</tr>
</table>


</div>
<?php }?>

<div id="invoice" style="padding:10px;page-break-after:always"> 
<style>
table{
	font-size:12px;
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
}
</style>
<div style="font-family:arial;font-size:12px;">
	<div style="font-family:arial;font-size:13px;padding-top:10px;">
<?php if($invdet['invoice_status']==0){?>
	<div><h1 style="margin:0px;border:1px solid #000;padding:3px;background:#eee;">CANCELLED INVOICE</h1></div>
<?php }?>	
		<div id="inv_logo_area">
			<div style="border-bottom:2px solid #000;padding:5px;font-weight:bold;text-align:center;overflow: hidden;" align="center">
				 <?php if($invdet['total_prints'] > 1){ ?>
				 <span style="float: right;font-size:32px;vertical-align: top;font-weight: normal;margin-top: -10px"><?php echo $invdet['total_prints'] ?></span>
				 <?php } ?>
				 TAX INVOICE
			</div>
			<div  style="border-bottom:2px solid #555;padding:15px;min-height: 40px;">
				<?php if($order['mode']==1){?>
					<div style="border:2px solid #000;padding:5px;font-size:150%;font-weight:bold;float:right;">CASH ON DELIVERY</div>
				<?php }?>
				<img style="float: right;margin-top: -7px;" src="data:image/png;base64,<?=base64_encode($barcode_img_data);?>" />
				<?php if($awb_img){?>
				<img style="float: right;margin-top: -7px;margin-right:40px;" src="data:image/png;base64,<?=base64_encode($awb_img);?>" />
				<?php }?>
				<?php if($is_pnh){?>
				<img src="<?=IMAGES_URL?>paynearhome.png">
				<?php }else{?>
				<img src="<?=IMAGES_URL?>logo_wap.png">
				<?php }?>
			</div>
		</div>
		<div id="pnh_inv_print_header" style="border-bottom:2px solid #000;padding:5px;font-weight:bold;text-align:center;overflow: hidden;display: none;text-align: center">
			Paynearhome - TAX INVOICE - Acknowledgement Copy 
		</div>
		<table width="100%" style="margin-top:10px">
			<tr>
				<td valign="top">
<?php 
	$service_no = '';
	$tin_no = '';
?>				
				
<?php 
if($is_pnh){
		$tin_no = '29230678061';
		$service_no = 'AACCL2418ASD001';	
		echo 'Local Cube commerce Pvt Ltd<br>1060,15th cross,BSK 2nd stage,bangalore -560070';
}else{					
		if($inv_createdon >= strtotime('2013-04-01'))
		{
			$tin_no = '29180691717';
			$service_no = 'AADCE1297KSD001';
			echo 'Eleven feet technologies<br>#1751, 18th B main,Jayanagar 4th T block,  Bangalore : 560 041<br>';
		}else
		{
			$tin_no = '29390606969';
			$service_no = 'AABCL7597DSD001';
			echo '#9, 5th Main, Sameerpura, Chamrajpet, Bangalore : 560 018<br>';
		}
		echo 'contact@snapittoday.com<br>';
}?>
				</td>
				<td align="right" valign="top">
					<table border=1 cellspacing	=0 cellpadding=5>
						<tr><td>Invoice<br>No:</td><td width=100><b><?=isset($invoice_no)?$invoice_no:$order['invoice_no']?></b></td>
						<td>Invoice<br>Date:</td><td width="100"><b><?=date("d/m/Y",$inv_createdon)?></b></td>
						<td>Transaction<br>
						
						<div align="center">ID/Date :</div></td><td width="100"><b><?=$order['transid']?></b> 
							<br />
							(<?php echo date('dM',$trans['init'])?>)
						</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding=5 style="margin-bottom:5px;margin-top:5px;">
			<tr>
			
				<td width=100%" valign="top" style="padding:0px;">
					<table cellspacing=0 border=1 cellpadding=3 width="100%">
						<tr><th>BILL TO</th><th><?=$order['bill_person']?></th></tr>
						<tr><td><b>Address :</b></td><td><?=nl2br($order['bill_address'])?>, <?=$order['bill_landmark']?>, <?=$order['bill_city']?> <?=$order['bill_state']?> - <?=$order['bill_pincode']?> 
						<?php
							if($inv_type !='auditing'){
						?>
						Mobile : <?=$order['bill_phone']?>
						<?php } ?>
						</td></tr>
					</table>
				</td>
			</tr>
		</table>
		<table cellspacing=0 cellpadding=5 border=1 width="100%" style="margin-top:10px;">
			<tr>
				<td width="<?=$is_pnh?"70":"45"?>%"><b>Product Item Name</b></td>
				<?php if($is_pnh){?>
					<td align="right" width="80" ><b>MRP</b></td>
				<?php 
					if($inv_type =='auditing'){
				?>
					
					<td align="right"><b>Discount</b></td>
				<?php } ?>
				<td align="right" width="70"><b>Base Price</b></td>
				<?php }?>
				<td align="center" width="50" ><b>VAT (%)</b></td>
				<td align="center" width="70"><b>Qty</b></td>
				<?php if(!$is_pnh){?>
				<td align="right" width="80" ><b>MRP</b></td>
				<td align="right"><b>Sub Total</b></td>
				<td align="right"><b>Discount</b></td>
				<?php }?>
				<?php 
					if($inv_type =='auditing'){
				?>
				<td align="right"><b>Product Rate</b></td>
				<td align="right"><b>Tax</b></td>
				<?php 		
					}  
				?>
				<?php 
					/*if($inv_type =='auditing'){
				?>
					<td align="right"><b>Product Rate</b></td>
					<td align="right"><b>Product Rate Tax</b></td>
					<td align="right"><b>Handling Charge</b></td>
					<td align="right"><b>Handling Charge Tax</b></td>
				<?php 		
					} */ 
				?>
				
				 
				<td align="right" width=100><b>Total</b></td>
			</tr>
<?php
$tpc_tax = 0;
$thc_tax = 0;
$mrp_total=$discount=$rejected=$cphc=$total=$stax=0; 
$thc = 0;
$total_item_amount = 0;

$s_tax_on = 0; 


$p_tax_list = array();
$p_tax_amount_list = array();


foreach($orders as $order){
	
	//$pstax=PRODUCT_SERVICE_TAX;

	/*$t_order=$this->db->query("select mrp,discount,nlc,phc,tax from king_invoice where order_id=?",$order['id'])->row_array();
	
	$ptax=$t_order['tax']/100;
	
	$order['mrp']=$t_order['mrp'];
	$order['discount']=$t_order['discount'];
	$order['nlc'] = $t_order['nlc'];
	$order['phc'] = $t_order['phc'];*/
	
	
	$ptax=$order['tax']/100;
	$p1tax=$order['tax'];
	
	
	$qty=$order['quantity'];
	
	$discount += round($order['discount']*$qty,2);
	
	
	/* if($order['status']==3 || $order['status']==0 || (isset($includes) && !in_array($order['id'],$includes)))
	{
		$p=$this->db->query("select i.price from king_orders o join king_dealitems i on i.id=o.itemid where o.id=?",$order['id'])->row()->price;
		//$rejected+=($order['nlc']+$order['phc'])*$order['quantity'];
		continue;
	} */
	
	$mrp_total += round($order['mrp']*$qty,2);
	 
	 
	
	$tpc += $product_rate = round(($order['nlc']*$qty*100/(100+$ptax)),2);
	$tpc_tax += $product_rate_tax = round(($order['nlc']*$qty-$product_rate),2);
	
	
	if(!isset($p_tax_list[$p1tax])){
		$p_tax_list[$p1tax] = 0;
		$p_tax_amount_list[$p1tax] = 0;
	} 
	
	$p_tax_list[$p1tax] += $product_rate_tax;
	$p_tax_amount_list[$p1tax] += $product_rate;
	
	
	$thc += $handling_cost = round((($order['phc']*$qty*100)/(100+$pstax)),2);
	$thc_tax += $handling_cost_tax = round((($order['phc']*$qty)-$handling_cost),2); 
	
	 
	$tphc += $handling_cost;
	
	$item_total_amount = $product_rate+$product_rate_tax+$handling_cost+$handling_cost_tax;
	$total_item_amount += $item_total_amount; 
?>			
			
			<tr>
				<td><?=$order['name'].($order['pnh_id']?' - <b>'.$order['pnh_id'].'</b>':'')?>
				<?php $imei=$this->db->query("select imei_no from t_imei_no where status=1 and order_id=?",$order['id'])->result_array(); $inos=array(); foreach($imei as $im) $inos[]=$im['imei_no'];?>
				<?php if(!empty($inos)){?>
				<br><b>SNo: <?=implode(", ",$inos)?></b>
				<?php }?>
				</td>
				<?php if($is_pnh){ ?>
					<td align="right" width="80" ><?=number_format($order['mrp'],2)?></td>
					<?php 
						if($inv_type =='auditing'){
					?>
						
						<td align="right"><?=number_format($order['discount'],2)?></td>
					<?php } ?>
				<td align="right"><?=number_format($product_rate/$order['quantity'],2)?></td>
				<?php } ?>
				<td align="center"><?=$ptax?></td>
				<td align="center"><?=$order['quantity']?></td>
				<?php if(!$is_pnh){?>
				<td align="right"><?=$order['mrp']?></td>
				<td align="right"><?=number_format($order['mrp']*$order['quantity'],2)?></td>
				<td align="right"><?=number_format($order['discount']*$order['quantity'],2)?></td>
				<?php }?>
				<?php 
					if($inv_type == 'auditing'){
				?>
				<td align="right"><?=number_format($product_rate,2)?></td> 
				<td align="right"><?=number_format($product_rate_tax,2)?></td>
				<?php } ?>
				
				<?php 
					/*if($inv_type == 'auditing'){
				?>
				<td align="right"><?=number_format($product_rate,2)?></td> 
				<td align="right"><?=number_format($product_rate_tax,2)?></td>
				<td align="right"><?=number_format($handling_cost,2)?></td> 
				<td align="right"><?=number_format($handling_cost_tax,2)?></td>
				<?php } */ ?>
				<?php 
					//$ttl_amt = number_format($item_total_amount,2);
					//if($is_partial_invoice){
						//$ttl_amt = number_format(round($item_total_amount));
					//}
					$ttl_amt = number_format(round($item_total_amount));
				?>
				<td align="right"><?=$ttl_amt?></td>
			</tr>
<?php } ?>

<?php 
$fs_list_res = $this->db->query("select *
		from king_freesamples_order fso
		join king_freesamples fs on fs.id = fso.fsid
		where invoice_no = ? ",$t_invoiceno);
if($fs_list_res->num_rows()){
	foreach($fs_list_res->result_array() as $fs_row){
?>
	<tr>
		<td><b>Free Sample</b> - <?=$fs_row['name']?></td>
		<td align="center">0</td>
		<td align="center">1</td>
		<td align="right">0</td>
		<td align="right">0</td>
		<td align="right">0</td>
		<?php 
			if($inv_type == 'auditing'){
		?>
		<td align="right">0</td>
		<td align="right">0</td>
		<?php } ?>
		<td align="right">0</td>
	</tr>
<?php 		
	}
}
?>


<?php 


	
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
		
		/*if($trans_total < MIN_AMT_FREE_SHIP)
		{
			$sship=$ship*100/(100+$pstax);
			$thc+=$ship;
			$s_tax_on += $ship*100/(100+$pstax); 
		}
		
		if($order['mode']==1 && $trans_total>MIN_AMT_FREE_SHIP)
		{
			$ccod=$cod*100/(100+$pstax);
			$thc+=$cod;
			
		} */
		
		
		$stax_tot = ($sship+$ccod+$sgc); 
		
		 
		
	 	$s_tax_apl = ($stax_tot*$pstax/100); 
		
		
		 
		
		/*$stax+=$handling_cost*$pstax/100;
		$stax+=($ccod+$sship)*$pstax/100;
		
		$stax=$thc*$pstax/100;
		$gtotal=$total+$handling_cost+$ccod+$stax+$sship;*/
	 	
	 	
	 	 
		
?>


			<tr style="font-weight: bold;">
				<td colspan="<?=($is_pnh&&$inv_type =='auditing')?"5":($is_pnh?"5":"4")?>" align="right">
					&nbsp; 
				</td>
				
				<?php if(!$is_pnh){?>
				<td align="right" ><?=number_format($mrp_total,2)?></td>
				<td align="right" ><?=number_format($discount,2)?></td>
				<?php }?>
				<?php 
					if($inv_type =='auditing'){
				?>
				<td align="right" ><?=number_format($tpc,2)?></td>
				<td align="right" ><?=number_format($tpc_tax,2)?></td>
				<?php } ?>
				<td align="right" ><?=number_format($total_item_amount,2)?></td>
			</tr>	
				
			 

		</table>
		<table width="100%" class="tax_block_content" cellspacing=0 cellpadding=0 style="margin:0px;">
			<tr>
				<td valign="top" style="padding:10px 0px;">
				Payment Mode : <b><?=$order['mode']==0?"Credit card/Net Banking":"Cash On Delivery"?></b> 
				<?php 
					if($giftwrap_charge){
						echo ' | Package Type : <b>Gift Wrap</b>';
					}
				?>
				<?php if($is_pnh){
							$ttl_refund_amt = @$this->db->query("select sum(amount) as amount from t_refund_info where refund_for = 'mrpdiff' and invoice_no = ? ",$invoice_no)->row()->amount;
							if($ttl_refund_amt){
					?>
					<div>Refund Amount : <b> Rs <?php echo round($ttl_refund_amt);?></b></div>
					<?php }?>
				<?php }?>	
				<table cellspacing=0 cellpadding=5 border=1 style="margin:10px 0px;" width=400>
						 
						<?php 
							foreach($p_tax_list as $ptax_t=>$ptax_a){
						?> 
						<tr>
							<td>Total VAT collected @ <b><?=number_format($ptax_t/100,2)?>%</b> on <b>Rs <?=number_format($p_tax_amount_list[$ptax_t],2)?></b></td>
							<td align="right"><b>Rs <?=number_format($ptax_a,2)?></b></td>
						</tr>
						<?php } ?> 
						<?php if($s_tax_apl){?>
						<tr>
							<td>Total Service Tax collected @ <b><?=number_format($pstax,2)?>%</b> on <b>Rs <?=number_format($stax_tot,2)?></b></td>
							<td align="right"><b>Rs <?=number_format($s_tax_apl,2)?></b></td>
						</tr>
						<?php } ?>
				</table>
				</td>
				<td align="right" valign="top">
				 
					<table cellspacing=0 border=1 cellpadding=5 style="border-top:0px;" >
						
						<tr style="display: none;">
							<td>
								<b>Total Order Value</b>
							</td>
							<td width="100" align="right">
								<?=number_format($mrp_total,2)?>
							</td>
						</tr>
 

						<tr style="display: none;">
							<td><b>Discount</b></td>
							<td align="right"><?=number_format($discount,2)?></td>
						</tr>

<?php if($ccod!=0 || $sship!=0 || $giftwrap_charge!=0){?>
						<tr>
							<td><b>COD/Handling/Packaging Charges</b></td>
							<td align="right"><?=number_format($cod_ship_charges,2)?></td>
						</tr>
<?php }?>

						<tr>
							<td width="180"><b>Total Amount </b></td>
							<td align="right" ><b>Rs. <?=number_format($cod_ship_charges+$total_item_amount,0)?></b></td>
						</tr>
					</table>
					 
				</td>
			</tr>
		</table>
		<table width="100%" class="tax_block_content" style="margin-top:5px;">
			<tr>
				<td width="50%">
					<div style="margin-right:10px;">
						<table cellspacing=0 border=1 cellpadding=2 width="100%">
							<tr>
								<td>VAT/TIN No</td>
								<td align="center"><?php echo $tin_no;?></td>
							</tr>
							<tr>
								<td>Service Tax No</td>
								<td align="center"><?php echo $service_no;?></td>
							</tr>
						</table>
					</div>
				</td>
				<td width="50%">
					<div style="margin-left:10px;border:2px solid #000;padding:2px;font-size:120%;">
						This is a electronically generated document and doesn't require signature
					</div>
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
			<div id="eoe_txt" style="padding-top:5px;padding-left:200px;">E &amp; O.E.</div>
		</div>
	</div>
	
	
	
<?php if($this->session->userdata("admin_user")){?>
	<div id="customer_ship_details" style="margin-top:5px;padding-bottom:10px;text-transform:uppercase;font-family:arial">
	<div style="float:right;width:200px;">
	<?php if($is_pnh){
				$mem_det = $this->db->query("select pnh_member_id as mid,mobile,concat(first_name,' ',last_name) as mem_name from pnh_member_info where user_id=?",$order['userid'])->row_array();
				
				echo $mem_det['mid']?'<div><b>MEMBER ID : </b>'.$mem_det['mid'].'</div>':'';
				echo $mem_det['mem_name']?'<div><b>NAME : </b>'.$mem_det['mem_name'].'</div>':''; 
				echo $mem_det['mobile']?'<div><b>Mob : </b>'.$mem_det['mobile'].'</div>':'';
		
			$fran_det = $this->db->query("select franchise_name,current_balance,store_tin_no,pnh_franchise_id as f from pnh_m_franchise_info where franchise_id=?",$fid)->row_array();
		?>
		<div><b>FID :</b> <?=$fran_det['f']?></div>
		<div><b>Current Balance :</b> <?=$fran_det['current_balance']?> <br /> <span style="font-size: 10px">( as on <?php echo date('d/m/Y h:i a')?>)</span></div>
		<div style="padding-top:5px;">Other Invoices:<?php foreach($this->db->query("select invoice_no,createdon from king_invoice where transid=? and invoice_no!=? group by invoice_no",array($order['transid'],$invoice_no))->result_array() as $i){?>
		<div><?=$i['invoice_no']?> (<?=date("d/m/y",$i['createdon'])?>)</div>
		<?php }?>
		</div>
	<?php }?>
		<?php $user_notes=$this->db->query("select note from king_transaction_notes where transid=?",$trans['transid'])->result_array();
		foreach($user_notes as $n){?>
			<div style="padding:5px;"><?=$n['note']?></div>
		<?php }?>
	</div>
		<div style="width: 350px;margin-left: 20px;">
				<?php 
					if(!$is_pnh)
					{
				 	 	if($trans['mode'] == 1){
				?>
							 <div style="border:2px solid #000;padding:7px;font-size:130%;width: 100%;" align="center">
								<div><b>CASH ON DELIVERY</b> : <b style="font-size:130%;">Rs <?=number_format($cod_ship_charges+$total_item_amount,2)?></b></div>
								</div>
					 	<br />
								
				<?php }else{?>
								<div style="border:2px solid #000;padding:7px;font-size:130%;width: 100%;" align="center">
					 			<div style="font-size:120%;"><b>CALL BEFORE DELIVERY</b></div>
								</div>
								<br />
								<!--<div>Total Amount: <b style="font-size:130%;">Rs <?=number_format($cod_ship_charges+$total_item_amount,2)?></b></div>-->
					 	<?php 	
					 	} 
					 } 
				?>	 	
					
					<table width="367" cellspacing=0 border=1 cellpadding=5 >
						<tr>
						<th align="left">
							<div style="float: left">
								<img style="width: 300px;" src="data:image/png;base64,<?php echo base64_encode($barcode_img_data);?>" />
							</div>
							<div style="float: right;vertical-align: top">
								<b style="font-size: 14px;"><?php echo $invoice_no;?></b>
							</div>
														
						 </th>
						 </tr>
						
						<?php 
							if(!$is_pnh)
							{
						?>
								<tr><td width=350> <?=$order['ship_person']?></td></tr>
								<tr>
									<td><?=nl2br($order['ship_address'])?><br>
										<?=$order['ship_landmark']?><br>
										<?=$order['ship_city']?> <?=$order['ship_pincode']?>
									</td>
								</tr>
								<tr>
									<td><?=$order['ship_state']?>
										<span style="float: right">Mobile : <?=$order['ship_phone']?></span>
									</td>
								</tr>
						<?php 
							}
							else
							{
								
						?>
							<tr>
								<td width=350> <?=$order['ship_person']?></td>
							</tr>
							<?php 
								if($fran_det['store_tin_no'])
								{
							?>
								<tr>
									<td width=350> VAT/TIN No : <?=$fran_det['store_tin_no']?></td>
								</tr>
							<?php 			
								}
							?>
							<tr>
								<td>
									<?=$order['ship_city'].','.$order['ship_state']?>
									<span style="float: right">Mobile : <?=$order['ship_phone']?></span>
								</td>
							</tr>
						<?php 				
							}
						?>
					</table>
					<?php if($awb_img){ ?>
						<div class="hideinprint" style="margin-top: 10px;">	
							<div style="margin-bottom: 0px;"><b>Courier : <?=$courier?> : <?php echo $awb;?></b></div>
							<img src="data:image/png;base64,<?=base64_encode($awb_img);?>" />
						<div>
					<?php }else{
						if(!$is_pnh)
						{
							$suggest_clist = $this->db->query("select group_concat(b.courier_name) as c from m_courier_pincodes a join m_courier_info b on a.courier_id = b.courier_id where  a.pincode = ? ",$order['ship_pincode'])->row()->c;
							if($suggest_clist && $partner_id != 5)
							{
						?>
							<div style="margin-bottom: 0px;font-size: 10px;"><b>Courier Suggestion : </b><br /><?=$suggest_clist?></div>
						<?php 		
							}  	
						}
					} 
					?>
		</div>					 
	</div>
<?php }?>
</div>

</div>
</div>
</div>
<?php
	if(!$is_pnh)
	{
		$has_order_form_res = $this->db->query("select * from partner_transaction_details where transid = ? ",$transid);
		if($has_order_form_res->num_rows())
		{	
			$part_order_det = $has_order_form_res->row_array();
?>
			<div id="partner_order_form" style="page-break-after:always">
			<style>
			table{
				font-size:12px;
				font-family: arial;
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
			}
			</style>
				
				<div style="width: 98%;margin:0px auto;">
				<table width="100%" cellpadding="3" cellspacing="0" border=1>
					<thead>
						<tr><th colspan="4" style="color:#000;text-align: center;font-size: 16px;padding:5px;">
							 <?php if($invdet['total_prints'] > 1){ ?>
							 	<span style="float: right;font-size:32px;vertical-align: top;font-weight: normal;margin-top: -10px"><?php echo $invdet['total_prints'] ?></span>
							 <?php } ?>
						Prepaid Order - DO NOT COLLECT CASH</th></tr> 
					</thead>
					<tbody>
						<tr>
							<td width="100">Suborder number</td>
							<td><?php echo $part_order_det['order_no'] ?></td>
							<td colspan="2" align="center">
								<?php $p_order_no = $part_order_det['order_no']; 
									if($p_order_no)
									{
										$p_order_no_bc = generate_barcode($p_order_no,500,70,2);
								?>
										<b>Homeshop18</b>
										<div><img src="data:image/png;base64,<?=base64_encode($p_order_no_bc);?>" /></div>
										<b><?php echo $part_order_det['order_no'] ?></b>
								<?php 		
									}
								?>
								&nbsp;
							</td>
						</tr>
						<tr>
							<td>Merchant Name</td>
							<td width="300">Localcube Commerce Pvt ltd-1811</td>
							<td width="100">TIN NO</td>
							<td>&nbsp;</td>
						</tr>
					</tbody>
				</table>
				<div align="center">
					<h3 style="margin:5px 0px">Delivery Address</h3>
				</div>
				<table width="100%" cellpadding="3" cellspacing="0" border=1>
					<tr>
						<td width="150">Name</td>
						<td><?php echo $order['ship_person'] ?></td>
					</tr>
					<tr>
						<td width="150">Address</td>
						<td><?=nl2br($order['ship_address'])?><br>
										<?=$order['ship_landmark']?>
							</td>
					</tr>
					<tr>
						<td width="150">City</td>
						<td><?php echo $order['ship_city'] ?></td>
					</tr>
					<tr>
						<td width="150">Pin Code</td>
						<td><?php echo $order['ship_pincode'] ?></td>
					</tr>
					<tr>
						<td width="150">State</td>
						<td><?php echo $order['ship_state'] ?></td>
					</tr>
					<tr>
						<td width="150">Country</td>
						<td><?php echo $order['ship_country'] ?></td>
					</tr>
					<tr>
						<td width="150">BC</td>
						<td>NA</td>
					</tr>
					<tr>
						<td width="150">Phone Number</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td width="150">Day Phone Number</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td width="150">Mobile Number</td>
						<td><?php echo $order['ship_phone'] ?></td>
					</tr>
					<tr>
						<td width="150">Order Number</td>
						<td><?php echo $p_order_no ?></td>
					</tr>
					<tr>
						<td width="150">Order Date</td>
						<td><?php echo $part_order_det['order_date'] ?></td>
					</tr>
					<tr>
						<td width="150">Desired  Date of Delivery</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td width="150">Sender Name</td>
						<td><?php echo $order['ship_person'] ?></td>
					</tr>
					<tr>
						<td width="150">Sender Message</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td width="150">Courier Name</td>
						<td><?php echo $part_order_det['courier_name'] ?>&nbsp;</td>
					</tr>
					<tr>
						<td width="150">AWB Number</td>
						<td><?php echo $part_order_det['awb_no'] ?>&nbsp;</td>
					</tr>
					<tr>
						<td width="150">AWB Barcode</td>
						<td style="font-size: 12px;text-align: center;">
							<?php $p_order_awbno = $part_order_det['awb_no']; 
									if($p_order_awbno)
									{
										$p_order_awbno_bc = generate_barcode($p_order_awbno,400,60,2);
								?>
										<b><?php echo $part_order_det['courier_name'] ?></b>
										<div><img src="data:image/png;base64,<?=base64_encode($p_order_awbno_bc);?>" /></div>
										<b><?php echo $part_order_det['awb_no'] ?></b>
								<?php 		
									}
								?>
								&nbsp;
						</td>
					</tr>
				</table>
				<div align="center">
					<h3 style="margin:5px 0px">Product Details</h3>
				</div>
				<table width="100%" cellpadding="5" cellspacing="0" border=1>
					<thead>
						<tr>
							<th>Name</th>
							<th>Qty</th>
							<th>Shipping</th>
							<th>Net Amount</th>
						</tr>
					</thead>
					<tbody>
						<?php 
							foreach($orders as $order){
						?>
						<tr>
							<td><?php echo $order['name'] ?></td>
							<td align="center"><?php echo $order['quantity'] ?></td>
							<td align="center"><?php echo $part_order_det['ship_charges'] ?></td>
							<td align="center"><?php echo $part_order_det['net_amt'] ?>&nbsp;</td>
						</tr>
						<?php } ?>
						<tr>
							<td colspan="4" style="padding:10px 5px;">
								Courier Remarks : 
							</td>
						</tr>
						
					</tbody>
					
				</table>	
			</div>
			</div>
			
			
			<style>
				#partner_order_form table{font-size: 110%;}
				#partner_order_form td{vertical-align: middle !important;}
			</style>
			<script>
				$('.print_partner_orderfrm_btn').show();
			</script>
<?php 
		}
	}	
?>

<div id="customer_acknowlegment" style="display: none">
	<div style="font-family: arial;">
		<hr>	
		<h3>Customer Acknowledgement</h3> 
		<div style="margin: 5px auto">
		<p>This it to acknowledge that we have received all products corresponding to Invoice No: <b><?php echo $invoice_no;?></b> of value Rs <b><?php echo $cod_ship_charges+$total_item_amount;?>/-</b></p>
		
		<div>
			<table width="100%">
				<tr>
					<td align="left">
						Date&amp;Time : <b>________________________</b> <br>
						<br>
						Mob No: <b>________________________</b> <br>
					</td>
					<td align="right">
						<div style="width: 300px;text-align: left;">
						For "<?php echo $fran_det['franchise_name'];?>"  
						<br><br><br><br>
						Seal &amp; Signature : _______________________
						</div>
					</td>
				</tr>
			</table>
		</div>
		<br>
		<hr>
		<div>
			<h3>For Office use Only</h3>
			<table cellpadding="10" width="100%">
				<tr><td colspan="2" align="left">Payment Mode <b style="border:2px solid #000">&nbsp;&nbsp;&nbsp;</b> &nbsp; Cash <b style="border:2px solid #000">&nbsp;&nbsp;&nbsp;</b> &nbsp; Cheque  <b style="border:2px solid #000">&nbsp;&nbsp;&nbsp;</b> &nbsp; DD </td></tr>
				<tr><td colspan="2" align="left">Instrument No : ____________________ &nbsp;&nbsp;&nbsp;&nbsp; Instrument Date :_______________________</td></tr>
				<tr style="display:none"><td colspan="2" align="left">Notes : ____________________________________________________________________________________________________________________________________________ <br><br> ____________________________________________________________________________________________________________________________________________ </td></tr>
			</table>
		</div>
		</div>
	</div>
</div>


<script type="text/javascript">
var inv_no = '<?php echo $invoice_no;?>';
function printinv(ele){
ele.value="RePrint Invoice";
log_printcount(); 
myWindow=window.open('','','width=950,height=600,scrollbars=yes,resizable=yes');
<?php if($is_pnh) { ?>
		
	var inv_html = $("#invoice").html().replace('TAX INVOICE','TAX INVOICE - Customer Copy');
		
		$('#customer_ship_details').hide();
		inv_html += '<div style="page-break-before:always">';
		inv_html += '</div>';
		
		$('.tax_block_content').hide();
		$('#inv_logo_area').hide();
		$('#pnh_inv_print_header').show();
		$('#eoe_txt').hide();
		
		inv_html += $("#invoice").html();
		
		$('#customer_ship_details').show();
		inv_html += $('#customer_acknowlegment').html();
		
		$('.tax_block_content').show();
		
	myWindow.document.write(inv_html);	
		$('#eoe_txt').show();	
		$('#inv_logo_area').show();
		$('#pnh_inv_print_header').hide();
		
		
<?php }else { ?>
myWindow.document.write($("#invoice").html());
<?php } ?>
myWindow.focus();
myWindow.print();
}
function printpartorderform(ele){ 
ele.value="RePrint Partner Order Form";
log_printcount();
myWindow=window.open('','','width=950,height=600,scrollbars=yes,resizable=yes');
myWindow.document.write($("#partner_order_form").html());
myWindow.focus();
myWindow.print();
}

function printinvpartorderform(ele){ 
ele.value="RePrint Invoice and Partner Order Form";
log_printcount();
myWindow=window.open('','','width=950,height=600,scrollbars=yes,resizable=yes');
myWindow.document.write($("#invoice").html()+'<div style="page-break-before:always"></div>'+$("#partner_order_form").html());
myWindow.focus();
myWindow.print();
}

function log_printcount()
{
	$.post(site_url+'/admin/jx_update_invoiceprint_count','invno='+inv_no);
}

</script>


<STYLE TYPE="text/css">
     H2.page_break{page-break-before: always}
     .note{margin-bottom:5px;border-bottom:1px solid #e3e3e3;}
</STYLE> 
<h2>&nbsp;</h2>