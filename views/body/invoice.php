<?php
$this->load->plugin('barcode');

$transid = $trans['transid'];
$partner_id=$this->db->query("select partner_id from king_transactions where transid=?",$transid)->row()->partner_id;
$is_pnh=$this->db->query("select is_pnh as p from king_transactions where transid=?",$transid)->row()->p;
$ttl_inv_amt = 0;
$ttl_inv_list = array();
// check if the invoice is split invoice 	
$mem_det = array();	
$invoice_credit_note_res = $this->db->query("select group_concat(id) as id,sum(amount) as amount from t_invoice_credit_notes a where invoice_no in (select invoice_no from king_invoice where split_inv_grpno = ? or invoice_no = ? ) ",array($invoice_no,$invoice_no));
$credit_days=$this->db->query("select credit_days from king_transactions where transid=?",$transid)->row()->credit_days;

$inv_total_prints = $this->db->query("select total_prints from king_invoice where invoice_no = ? ",$invoice_no)->row()->total_prints;

//echo $this->db->last_query();

?>
<div class="container" style="background:#fff;">
    <div style="width: 100%;margin: 0px auto">	
	<?php if($this->session->userdata("admin_user")){?>
	<div style="margin:10px;" class="hideinprint">
	<table width="100%">
		<tr>
			<td align="left" width="33%">
				<input type="button" value="<?php echo $inv_total_prints?'RePrint':'Print' ?> invoice" onclick='printinv(this)'>
				<?php
					$dispatch_id = @$this->db->query("
										select dispatch_id 
											from proforma_invoices a 
											join shipment_batch_process_invoice_link b on a.p_invoice_no = b.p_invoice_no
											join king_invoice c on c.invoice_no = b.invoice_no  
											where (b.invoice_no = ?  or split_inv_grpno = ? or ref_dispatch_id = ? ) 
										group by a.p_invoice_no ",array($invoice_no,$invoice_no,$invoice_no))->row()->dispatch_id;
					if($dispatch_id)
					{					
				?>
					<input type="button" value="Print Dispatch Document" onclick="printdispatchdoc(this)" >
				<?php } ?> 
				<?php if($invoice_credit_note_res->num_rows()) {
						$invoice_credit_note_det = $invoice_credit_note_res->row_array(); 
						if($invoice_credit_note_det['amount'])
						{
				?>
						<input type="button" value="Print Credit Note" onclick='printcreditnote(this)'> 
				<?php 			
						}
					} 
				?>
				
				<?php if($is_pnh){ ?>
					<input type="button" value="Print Acknowledgement" onclick="print_tax_acknowledgement()" >
				<?php } ?>
				
			</td>
			<td align="center" width="33%"><input class="print_partner_orderfrm_btn" style="display: none;" type="button" value="<?php echo $inv_total_prints?'RePrint':'Print' ?> Partner Order Form" onclick='printpartorderform(this)'></td>
			<td align="right" width="33%"><input class="print_partner_orderfrm_btn" style="display: none;" type="button" value="<?php echo $inv_total_prints?'RePrint':'Print' ?> Invoice and Partner Order Form" onclick='printinvpartorderform(this)'></td>
		</tr>
	</table>
	</div>
	<?php }?>

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
	
	$orderslist_byproduct = array();
	
	foreach($invoice_list as $inv_det)
	{
		$invoice_no = $inv_det['invoice_no'];
		$sql="select in.invoice_no,item.nlc,item.phc,ordert.*,
							item.service_tax_cod,item.name,if(length(item.print_name),item.print_name,item.name) as print_name,in.invoice_no,
							brand.name as brandname,
							in.mrp,in.tax as tax,
							in.discount,
							in.phc,in.nlc,
							in.service_tax,
							item.pnh_id,f.offer_text,f.immediate_payment,
							in.invoice_qty as quantity,
							ordert.member_id  as alloted_mem_id,ordert.has_insurance,ordert.insurance_amount
						from king_orders as ordert
						join king_dealitems as item on item.id=ordert.itemid 
						join king_deals as deal on deal.dealid=item.dealid 
						left join king_brands as brand on brand.id=deal.brandid 
						left join pnh_m_offers f on f.id= ordert.offer_refid
						#left join pnh_member_offers mo on mo.transid_ref = ordert.transid 
						join king_invoice `in` on in.transid=ordert.transid and in.order_id=ordert.id  
						where in.invoice_no=? or split_inv_grpno = ?
				";
		$q=$this->db->query($sql,array($invoice_no,$invoice_no));
		$orders=$q->result_array();
		
		if($is_pnh)
		{
			$fid=$this->db->query("select t.franchise_id as fid from king_transactions t where transid=?",$trans['transid'])->row()->fid;
			$mem_det = $this->db->query("select pnh_member_id as mid,mobile,concat(first_name,' ',last_name) as mem_name from pnh_member_info where user_id=?",$orders[0]['userid'])->row_array();
		}



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
		
		//checking this order created from voucher
		$is_voucher_order_res=$this->db->query("select voucher_payment from king_transactions where transid=? and is_pnh=1",$orders[0]['transid']);
		$show_voucher_det=0;
		if($is_voucher_order_res->num_rows())
		{
			$is_voucher_order=$is_voucher_order_res->row_array();
			
			//get the voucher codes
			if($is_voucher_order['voucher_payment'])
			{
				$show_voucher_det=1;
				$voucher_codes=$this->db->query("select group_concat(voucher_slno) as voucher_codes,transid from pnh_voucher_activity_log where transid=? group by transid",$orders[0]['transid'])->row_array();
			}
		}
		
		//check as imei scheme
		$imei_credit=$order['imei_reimbursement_value_perunit'];
	


		$inv_has_insurance = $this->db->query("select count(*) as t from king_invoice a join king_orders b on a.order_id = b.id where a.invoice_no = ? and insurance_id > 0 ",$order['invoice_no'])->row()->t;
		
		$inv_has_insurance = $inv_has_insurance?1:0;
		

?>
<div class="invoice" style="padding:10px;page-break-after:always"> 
	<div style="font-family:arial;font-size:12px;">
		<div style="font-family:arial;font-size:13px;padding-top:10px;">
			<?php if($invdet['invoice_status']==0){?>
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
					<?php if($order['mode']==1){?>
						<div style="border:2px solid #000;padding:5px;font-size:150%;font-weight:bold;float:right;">CASH ON DELIVERY</div>
					<?php }?>
					<table width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td  align="left">
								<?php if($is_pnh){?>
								<img class="pnh_logo" src="<?=IMAGES_URL?>paynearhome.png">
								<?php }else{?>
								<img src="<?=IMAGES_URL?>logo_wap.png">
								<?php }?>
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
						$service_no = '';
						$tin_no = '';
						if($is_pnh){
								$tin_no = '29230678061';
								$service_no = 'AACCL2418ASD001';	
								echo 'LocalCube Commerce Pvt Ltd<br>Plot 3B1,KIADB Industrial Area,Kumbalagudu 1st Phase,Mysore Road,Bangalore -560074';
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
						}
					?>
					</td>
					<td align="right" valign="top">
						<table border=1 cellspacing	=0 cellpadding=5>
							<tr><td>Invoice<br>No:</td>
							<td width=200><b><?=isset($invoice_no)?$invoice_no:$order['invoice_no']?></b><br>
							<?php if($is_pnh){?>
								<span style="font-size: 11px;"><b><?=$credit_days ?> Days Payment</b></span>
							<?php } ?>
							</td>
							
							<td>Invoice<br>Date:</td><td width="100"><b><?=date("d/m/Y",$inv_createdon)?></b></td>
							<td>Transaction<br>
							<div align="center">ID/Date :</div></td><td width="100"><b><?=$order['transid']?></b> 
								<br />
								(<?php echo date('dM',$trans['init'])?>)
								<span style="font-size: 8px;">
								<?php
									//$spn = $this->db->query("select split_inv_grpno from king_invoice where invoice_no = ? ",$invoice_no)->row()->split_inv_grpno;
									if($dispatch_id)
										echo $dispatch_id; 
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
							<tr><th width="100">BILL TO</th>
								<?php
									if($is_pnh)
									{
								?>
									<th width="400"><?=$order['bill_person']?></th>
									<th width="80">Customer</th>
									<th><?php echo $mem_det['mem_name'].' ('.$mem_det['mid'].')' ?></th>
								<?php	
									}else
									{
								?>
									<th colspan="3"><?=$order['bill_person']?></th>
								<?php		
									}
								?>
							</tr>
							<tr><td><b>Address :</b></td><td colspan="3"><?=nl2br($order['bill_address'])?>, <?=$order['bill_landmark']?>, <?=$order['bill_city']?> <?=$order['bill_state']?> - <?=$order['bill_pincode']?> 
							<?php
								if($inv_type !='auditing'){
							?>
							Mobile : <?=$order['bill_phone']?>
							<?php } ?>
							</td>
							</tr>
                                                        <?php 
                                                        if( isset($arr_offer_type[$order['offer_type']]) ) {
                                                            ?>
                                                        
                                                            <tr><th>Offers :</th>
                                                                <td>
                                                                    <?php 
                                                                    $arr_offer_type = array(0=>"Insurance Opted",1=>"Free Recharge",2=>"Free Insurance",3=>"N/A or Not Opted",4=>"Requested for Insurance");
                                                                    echo $arr_offer_type[$order['offer_type']];

                                                                    ?>
                                                                </td>
                                                            </tr>
                                                    <?php
                                                        }
                                                    ?>
						</table>
					</td>
				</tr>
			</table>
			<table cellspacing=0 cellpadding=5 border=1 width="100%" style="margin-top:10px;">
				<tr>
					<?php if($orders[0]['alloted_mem_id']){?>
					<td width="10"><b>MemberID</b></td>
					<?php }?>
					<td width="<?=$is_pnh?"70":"45"?>%"><b>Product Item Name</b></td>
					<?php if($is_pnh){?>
							<td align="right" width="80" ><b>MRP</b></td>
						<?php if($inv_type =='auditing'){ ?>
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
					<?php } ?>
					<?php if($is_pnh && $inv_has_insurance){ ?>
					<td align="right"><b>Insurance Cost</b></td>
					<?php 
						}
						if($inv_type =='auditing'){
					?>
					<td align="right"><b>Product Rate</b></td>
					<td align="right"><b>Tax</b></td>
					<?php 		
						}  
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
		
		$col_span_fix = $orders[0]['alloted_mem_id']?1:0; 
		
		
		$col_span_fix = $col_span_fix+$inv_has_insurance;
		
		
		$p_tax_list = array();
		$p_tax_amount_list = array();
		
		if($invdet['invoice_status'] == 1)
			$returned_item_amt = 0;
		else
			$returned_item_amt = 1;

		foreach($orders as $order){
		
			// if reutrnd
			if($order['status'] == 4 && $is_pnh == 1)
			{
				if($invdet['invoice_status']==1)
					continue;
			}

			$consider_item_total = 1;
		
			$ptax=$order['tax']/100;
			$p1tax=$order['tax'];
			$qty=$order['quantity'];
			$discount += round($consider_item_total*$order['discount']*$qty,2);
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
            $ttl_insu_val=0;
            if($order['has_insurance'] == 1 && $order['insurance_amount'] != 0 && $order['insurance_amount'] !='' && $is_pnh) {
                $ttl_insu_val = round($order['insurance_amount'],2);
            }
			$item_total_amount =  ($product_rate+$product_rate_tax+$handling_cost+$handling_cost_tax + $ttl_insu_val);

			$total_item_amount += $item_total_amount; 
			
			if($order['status'] == 4)
				$returned_item_amt += $item_total_amount;
				
?>			
			<tr>
				<?php if($order['alloted_mem_id']){?>
					<td width="10"><b><?php echo $order['alloted_mem_id'];?></b></td>
				<?php }?>
				<td>
					<?php if($inv_type !='original'){ ?>
						<span class="hideinprint">
							<?php
								if($order['status'] == 4)
								{
									echo '<strike>'.$order['name'].'-'.$order['pnh_id'].'</strike> - Product Returned';
								}else
								{
									echo $order['name'].'-'.$order['pnh_id'];
								}
							?>
							<?php $imei=$this->db->query("select imei_no from t_imei_no where order_id=?",$order['id'])->result_array(); $inos=array(); foreach($imei as $im) $inos[]=$im['imei_no'];?>
							<?php if(!empty($inos)){?>
							<br><b>Imeino: <?=implode(", ",$inos)?></b>
							<?php if($is_pnh) {?>
							<br><span> Activation : <b> <?php echo $imei_credit ?></b></span>
							<?php }?>
							<?php }?>
							<br>
						</span>
						
						<span class="showinprint">
							<?php
								if($order['status'] == 4)
								{
									echo '<strike>'.$order['print_name'].'-'.$order['pnh_id'].'</strike> - Product Returned';
								}else
								{
									echo $order['print_name'].'-'.$order['pnh_id'];
								}
							?>
							<?php $imei=$this->db->query("select imei_no from t_imei_no where order_id=?",$order['id'])->result_array(); $inos=array(); foreach($imei as $im) $inos[]=$im['imei_no'];?>
							<?php if(!empty($inos)){?>
							<br><b>Imeino: <?=implode(", ",$inos)?></b>
							<br><span> Activation : <b> <?php echo $imei_credit ?></b></span>
							<?php }?>
							<br>
						</span>
					<?php }else
						{
					?>
					
						<span class="">
							<?php
								if($order['status'] == 4)
								{
									echo '<strike>'.$order['name'].'-'.$order['pnh_id'].'</strike> - Product Returned';
								}else
								{
									echo $order['name'].'-'.$order['pnh_id'];
								}
							?>
							<?php $imei=$this->db->query("select imei_no from t_imei_no where order_id=?",$order['id'])->result_array(); $inos=array(); foreach($imei as $im) $inos[]=$im['imei_no'];?>
							<?php if(!empty($inos)){?>
							<br><b>Imeino: <?=implode(", ",$inos)?></b>
							<?php }?>
						</span>
						
					<?php		
						}
					?>
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
				<?php }
					if($inv_type == 'auditing')
					{
				?>
				<td align="right"><?=number_format($product_rate,2)?></td> 
				<td align="right"><?=number_format($product_rate_tax,2)?></td>
				<?php 
					}
					
					if($is_pnh && $inv_has_insurance)
					{
				?>
				<td align="right">
					<?php
                        if($order['has_insurance'] == 1)
                        {
					?>
                          	<?=($order['insurance_amount']==0)?'Free':formatInIndianStyle($order['insurance_amount']);?>
					<?php
                        }
                    ?>
				</td>
				<?php
					}  
				$ttl_amt = number_format(round($item_total_amount));
				?>
				<td align="right"><?php echo ($order['status'] == 4)?'<strike>':'';?><?=$ttl_amt; ?><?php echo ($order['status'] == 4)?'</strike>':'';?></td>
			</tr>
                        
<?php
			if($order['status'] != 4)
			{
				if(!isset($orderslist_byproduct[$order['itemid']]))
					$orderslist_byproduct[$order['itemid']] = array('det'=>array('name'=>$order['name'],'print_name'=>$order['print_name'],'pnh_id'=>$order['pnh_id']),'qty'=>0,'amt'=>0,'invs'=>array());
				
				$orderslist_byproduct[$order['itemid']]['qty'] += $order['quantity'];
				$orderslist_byproduct[$order['itemid']]['amt'] += $item_total_amount;
				
				array_push($orderslist_byproduct[$order['itemid']]['invs'],$invoice_no);
				
			}
				
?>			
			
			
<?php }  
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

		 	$col_span_fix 
?>


			<tr style="font-weight: bold;">
				<td colspan="<?=($is_pnh&&$inv_type =='auditing')?"5"+$col_span_fix:($is_pnh?"5":"4")+$col_span_fix?>" align="right">
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
				<td align="right" ><?=number_format($total_item_amount-$returned_item_amt,2)?></td>
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
				<td>&nbsp;</td>
				
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
						<?php        } 
                                                    $mem_reg_fee = 0;
                                                    $recharge_offer_res = $this->db->query("select * from pnh_member_offers where process_status = 0 and mem_fee_applicable = 1 and transid_ref = ? ",$order['transid']);
                                                    if($recharge_offer_res->num_rows())
                                                    {
                                                                $recharge_offer = $recharge_offer_res->$num_recharge_offer;
								$mem_reg_fee = $recharge_offer['pnh_member_fee'];
						?>
						<tr>
							<td>Member Registration Fee</td>
							<td>Rs. <?=$mem_reg_fee;?></td>
						</tr>
						<?php }  ?>
						<tr>
							<td width="180"><b>Total Amount </b></td>
							<?php $ttl_invoice_amt = number_format( ($cod_ship_charges +  $total_item_amount + $mem_reg_fee) - $returned_item_amt,0); ?>
							<td align="right" ><b>Rs. <?=$ttl_invoice_amt?></b></td>
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
					<div style="margin:5px 10px 0px 0px;">
						<?php if($show_voucher_det){?>
						<table cellspacing=0 border=1 cellpadding=2 width="100%">
							<tr>
								<td width="25%">Redeemed vouchers</td>
								<td align="center"><?php echo $voucher_codes['voucher_codes']; ?></td>
							</tr>	
						</table>
						<?php }?>
					</div>
				</td>
				<td width="50%">
					<?php 
						$offer_det_res = $this->db->query("select b.invoice_no,has_offer,offer_refid,b.invoice_no,c.offer_text,c.immediate_payment 
	from king_orders a
	join king_invoice b on a.id = b.order_id
	join pnh_m_offers c on c.id = a.offer_refid and is_active = 1 
	where b.invoice_no != 0 and b.invoice_no = ? and has_offer = 1  ",$invoice_no);
					if($offer_det_res->num_rows()){
						$offer_det  = $offer_det_res->row_array();
					?>
						<div style="margin-left:10px;border: 2px solid rgb(0, 0, 0);font-size: 110%;margin-bottom: 10px;padding: 5px;margin-top:-40px">
							<b><?php echo $offer_det['offer_text'] ;?>
							<?php echo $offer_det['immediate_payment']?'<br />Require Immediate Payment':'';?>
							</b> 
						</div>
					<?php }?>
					<div style="margin-left:10px;border:1px solid #000;padding:2px;font-size:80%;">
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
			<div class="eoe_txt" style="padding-top:5px;padding-left:200px;">E &amp; O.E.</div>
		</div>
	</div>
	
<?php if($this->session->userdata("admin_user")){?>
	<div class="customer_ship_details" style="margin-top:5px;padding-bottom:10px;text-transform:uppercase;font-family:arial">
	<div style="float:right;width:200px;">
	<?php if($is_pnh){
				$mem_det = $this->db->query("select pnh_member_id as mid,mobile,concat(first_name,' ',last_name) as mem_name from pnh_member_info where user_id=?",$order['userid'])->row_array();
				echo $mem_det['mid']?'<div><b>MEMBER ID : </b>'.$mem_det['mid'].'</div>':'';
				echo $mem_det['mem_name']?'<div><b>NAME : </b>'.$mem_det['mem_name'].'</div>':''; 
				echo $mem_det['mobile']?'<div><b>Mob : </b>'.$mem_det['mobile'].'</div>':'';
				$fran_det = $this->db->query("select franchise_name,current_balance,store_tin_no,pnh_franchise_id as f from pnh_m_franchise_info where franchise_id=?",$fid)->row_array();
				$fr_acc_det = $this->erpm->get_franchise_account_stat_byid($fid);
		?>
		<div><b>FID :</b> <?=$fran_det['f']?></div>
		<?php /*
		<div><b>Current Balance :</b> <?=$fr_acc_det['current_balance']?> </div>
		 */ ?>
		<?php if($fr_acc_det['uncleared_payment']){?>
			<div><b>Uncleared Payment:</b> <?=$fr_acc_det['uncleared_payment']?>) </div>
		<?php } ?> 
		<div><span style="font-size: 10px">( as on <?php echo date('d/m/Y h:i a')?>)</span></div>
		
		<?php if(count($invoice_list) == 1 && 0){ ?>
		<div style="padding-top:5px;">Other Invoices:<?php foreach($this->db->query("select invoice_no,createdon from king_invoice where transid=? and invoice_no!=? group by invoice_no",array($order['transid'],$invoice_no))->result_array() as $i){?>
		<div><?=$i['invoice_no']?> (<?=date("d/m/y",$i['createdon'])?>)</div>
		<?php }?>
		</div>
		<?php }?>
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
						</div> 
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
<?php 

	$ttl_inv_amt += $cod_ship_charges+$total_item_amount;
	array_push($ttl_inv_list,$invoice_no); 
}
?>

</div>

<div id="tax_inv_ack_copy" style="display:none">
<div style="font-family: arial">	
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
	.showinprint{
					display:block;
				}
}
</style>

	<div style="border-bottom:2px solid #000;padding:5px;font-weight:bold;text-align:center;overflow: hidden;text-align: center">
	<br><br>
	<br><br>
	TAX INVOICE - Acknowledgement Copy 
	</div>
	<table width="100%" style="margin-top:10px">
				<tr>
					<td valign="top">
					<?php 
						$service_no = '';
						$tin_no = '';
						if($is_pnh){
								$tin_no = '29230678061';
								$service_no = 'AACCL2418ASD001';	
								echo 'LocalCube Commerce Pvt Ltd<br>Plot 3B1,KIADB Industrial Area,Kumbalagudu 1st Phase,Mysore Road,Bangalore -560074';
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
						}
					?>
					</td>
					<td align="right" valign="top">
						<table border=1 cellspacing	=0 cellpadding=5>
							<td><br>Date:</td><td width="100"><b><?=date("d/m/Y",$inv_createdon)?></b></td>
							<td>Transaction<br><div align="center">ID/Date :</div></td>
							<td width="100">
								<b><?=$order['transid']?></b> 
								<br />
								(<?php echo date('dM',$trans['init'])?>) 
							</td>
							</tr>
						</table>
					</td>
					
				</tr>
				
			</table>
			 
			<table cellspacing=0 border=1 cellpadding=3 width="100%">
				<tr><th width="100">BILL TO</th>
					<th colspan="3"><?=$order['bill_person']?></th>
					<th>Dispatch ID</th>
				</tr>
				<tr><td><b>Address :</b></td><td colspan="3"><?=nl2br($order['bill_address'])?>, <?=$order['bill_landmark']?>, <?=$order['bill_city']?> <?=$order['bill_state']?> - <?=$order['bill_pincode']?> 
				<?php
					if($inv_type !='auditing'){
				?>
				Mobile : <?=$order['bill_phone']?>
				<?php } 
				
				 
				?>
				</td>
				<?php if($dispatch_id) { ?>
				<td align="right" valign="top">
					<?php
						$dispatch_bc = generate_barcode($dispatch_id,300,40,2); 
					?>
					<div align="center"><?php echo count($invoice_list);?></div>
					<div align="center"><img src="data:image/png;base64,<?=base64_encode($dispatch_bc);?>" /></div>
					<div align="center"><b><?php echo $dispatch_id ?></b>  </div>
				</td>
				<?php } ?>
				</tr>
			</table>
			<br>		 
	<table width="100%" cellpadding="5" cellspacing="0" border="1">
		<tr>
			<th>No</th>
			<th>Item</th>
			<th>Invoice</th>
			<th>Qty</th>
			<th>Total</th>
		</tr>
		<?php 
			$k1=0;
			foreach($orderslist_byproduct as $itmid=>$itm_ord){ 
			?>
		<tr>
			<td><?php echo ++$k1; ?></td>
			<td>
					<span class="showinprint"><?php echo $itm_ord['det']['print_name'].'-'.$itm_ord['det']['pnh_id'];?></span>
					<span class="hideinprint"><?php echo $itm_ord['det']['name'].'-'.$itm_ord['det']['pnh_id'];?></span>
				</td>
			<td><?php echo implode(', ',array_unique($itm_ord['invs']));?></td>
			<td><?php echo $itm_ord['qty'];?></td>
			<td><?php echo $itm_ord['amt'];?></td>
		</tr>
		<?php 		 
			} 
		?>
		<tr>
			<td colspan="4">&nbsp;</td>
			<td><?php echo $ttl_inv_amt;?></td>
		</tr>	
	</table>
	
	</div>
</div>

<?php if($is_pnh) { ?>

<div id="gate_pass_copy" style="display:none">
<div style="font-family: arial">	
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
	.showinprint{
					display:block;
				}
}
</style>

	<div style="border-bottom:2px solid #000;padding:5px;font-weight:bold;text-align:center;overflow: hidden;text-align: center">
	<br><br>
	<br><br>
	Gate Pass  
	</div>
	<table width="100%" style="margin-top:10px">
				<tr>
					<td valign="top">
					<?php 
						$service_no = '';
						$tin_no = '';
						if($is_pnh){
								$tin_no = '29230678061';
								$service_no = 'AACCL2418ASD001';	
								echo 'LocalCube Commerce Pvt Ltd<br>Plot 3B1,KIADB Industrial Area,Kumbalagudu 1st Phase,Mysore Road,Bangalore -560074';
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
						}
					?>
					</td>
					<td align="right" valign="top">
						<table border=1 cellspacing	=0 cellpadding=5>
							<td>Invoice<br>No:</td><td width=100><b><?=$invoice_no?></b></td>
							<td><br>Date:</td><td width="100"><b><?=date("d/m/Y",$inv_createdon)?></b></td>
							<td>Transaction<br><div align="center">ID/Date :</div></td>
							<td width="100">
								<b><?=$order['transid']?></b> 
								<br />
								(<?php echo date('dM',$trans['init'])?>) 
							</td>
							</tr>
						</table>
					</td>
					
				</tr>
				
			</table>
			 
			<table cellspacing=0 border=1 cellpadding=3 width="100%">
				<tr><th width="100">BILL TO</th>
					<th colspan="3"><?=$order['bill_person']?></th>
					<th>Dispatch ID</th>
				</tr>
				<tr><td><b>Address :</b></td><td colspan="3"><?=nl2br($order['bill_address'])?>, <?=$order['bill_landmark']?>, <?=$order['bill_city']?> <?=$order['bill_state']?> - <?=$order['bill_pincode']?> 
				<?php
					if($inv_type !='auditing'){
				?>
				Mobile : <?=$order['bill_phone']?>
				<?php } 
				
				 
				?>
				</td>
				<?php if($dispatch_id) { ?>
				<td align="right" valign="top">
					<?php
						$dispatch_bc = generate_barcode($dispatch_id,300,40,2); 
					?>
					<div align="center"><?php echo count($invoice_list);?></div>
					<div align="center"><img src="data:image/png;base64,<?=base64_encode($dispatch_bc);?>" /></div>
					<div align="center"><b><?php echo $dispatch_id ?></b>  </div>
				</td>
				<?php } ?>
				</tr>
			</table>
			<br>		 
	<table width="100%" cellpadding="5" cellspacing="0" border="1">
		<tr>
			<th>No</th>
			<th>Item</th>
			<th>Serialnos</th>
			<th>Qty</th>
			<th>Total</th>
		</tr>
		<?php 
			$k1=0;
			foreach($orderslist_byproduct as $itmid=>$itm_ord){ 
				
			?>
		<tr>
			<td><?php echo ++$k1; ?></td>
			<td>
				<span><?php echo $itm_ord['det']['name'].'-'.$itm_ord['det']['pnh_id'];?></span>
				
			</td>
			<td><?php 
					$itm_ord['invs'] = array_filter(array_unique($itm_ord['invs']));
					//echo implode(', ',$itm_ord['invs']);
					foreach($itm_ord['invs'] as $i_inv)
					{
						$i_imei_no = @$this->db->query("select group_concat(imei_no) as inv_imei_no from t_imei_no a join king_invoice b on a.order_id = b.order_id join king_orders c on c.id = b.order_id where b.invoice_no = ? and c.itemid = ? ",array($i_inv,$itmid))->row()->inv_imei_no;
						
						echo $i_imei_no?str_replace(',', '<br>', $i_imei_no):'';
					}
				?>
			</td>
			<td><?php echo $itm_ord['qty'];?></td>
			<td><?php echo $itm_ord['amt'];?></td>
		</tr>
		<?php 		 
			} 
		?>
		<tr>
			<td colspan="4">&nbsp;</td>
			<td><?php echo $ttl_inv_amt;?></td>
		</tr>	
	</table>
	<br>
	<table width="100%">
		<tr>
			<td width="50%" valign="top" align="left">
				<b>Invoiced By</b>
				<div style="border-bottom: 0px solid #000;width: 250px;">&nbsp;
					 
					<span style="font-size: 14px;">
					<?php 
						$user_det = $this->erpm->auth();
						echo $user_det['username'].' <br><br>';
						echo date('d/m/Y h:i a');
					?>
					</span>
				</div>
			</td>
			<td width="50%" align="right">
				<b>Verified By</b>
				<div style="border-bottom: 1px solid #000;height: 50px;width: 250px;">&nbsp;</div>
			</td>
		</tr>
	</table>
	<br>
	<br>
	<br>
	</div>
</div>
<?php } ?>


<?php if($dispatch_id){ ?>
<div id="dispatch_document" >
<div style="font-family: arial">	
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
	.showinprint{
		display:block;
	}
}
</style>

	<div style="border-bottom:2px solid #000;padding:5px;font-weight:bold;text-align:center;overflow: hidden;text-align: center">
		<span style="float: right">Date:<b><?=date("d/m/Y",$inv_createdon)?></b></span>
		Storeking - Dispatch document 
	</div>
	<table width="100%" style="margin-top:10px">
				<tr>
					<td valign="top">
					<?php 
						$service_no = '';
						$tin_no = '';
						if($is_pnh){
								$tin_no = '29230678061';
								$service_no = 'AACCL2418ASD001';	
								echo 'LocalCube Commerce Pvt Ltd<br>Plot 3B1,KIADB Industrial Area,Kumbalagudu 1st Phase,Mysore Road,Bangalore -560074';
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
						}
					?>
					</td>
					<td align="right" valign="top">
						<div align="center"><?php echo count($invoice_list);?></div>
						<?php
							$dispatch_bc = generate_barcode($dispatch_id,300,40,2); 						?>
						<div align="center"><img src="data:image/png;base64,<?=base64_encode($dispatch_bc);?>" /></div>
						<div align="center"><b><?php echo $dispatch_id ?></b></div>
					</td>
					 
				</tr>
			</table>
			
			<table cellspacing=0 border=1 cellpadding=3 width="100%">
				<tr><td width="100">Dispatch TO</td>
					<td colspan="3">
						<?=$order['bill_person']?><br>
						<?=nl2br($order['bill_address'])?>, <?=$order['bill_landmark']?>, <?=$order['bill_city']?> <?=$order['bill_state']?> - <?=$order['bill_pincode']?>
					</td>
				</tr>
			</table>
				<br>	
	<table width="100%" cellpadding="5" cellspacing="0" border="1">
		<tr>
			<th>No</th>
			<th>Item</th>
			<th>Qty</th>
		</tr>
		<?php 
			$k1=0;
			foreach($orderslist_byproduct as $itmid=>$itm_ord){
			?>
			<tr>
				<td><?php echo ++$k1; ?></td>
				<td>
					<span class="showinprint"><?php echo $itm_ord['det']['print_name'].'-'.$itm_ord['det']['pnh_id'];?></span>
					<span class="hideinprint"><?php echo $itm_ord['det']['name'].'-'.$itm_ord['det']['pnh_id'];?></span>
				</td>
				<td align="center"><?php echo $itm_ord['qty'];?></td>
			</tr>
		<?php 		 
			} 
		?>
	</table>
	</div>
</div>
<?php } ?>
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
										<b><?php echo $this->db->query("select * from partner_info where id = ? ",$part_order_det['partner_id'])->row()->name; ?></b>
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
	
	
	if($invoice_credit_note_res->num_rows())
	{
		$invoice_credit_note_det = $invoice_credit_note_res->row_array();
		
		if($invoice_credit_note_det['amount'])
		{
?>
		<div  id="invoice_credit_note" style="width:98%">
			<style>
			body{
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
				.showinprint{
					display:block;
				}
			}
			</style>
			<div align="center">
					
					<b style="font-size: 140%">LocalCube Commerce Pvt Ltd</b><br>
					Plot 3B1,KIADB Industrial Area,Kumbalagudu 1st Phase,Mysore Road  
					<br>Bangalore -560074
					<br>
					<br>
					<b style="font-size: 140%">Credit Note</b>
			</div>
			<div align="center">
				<table width="100%" style="font-size: 120%" border=0 cellpadding="5" cellspacing="0">
					<tbody>
						<tr>
							<td>Dispatch No : <?=$dispatch_id?></td>
							<td align="right">Dated : <?=date("d/m/Y",$inv_createdon)?></td>
						</tr>
						<tr>
							<td align="left">Party Name : <?=$order['bill_person']?></td>
						</tr>
					</tbody>
				</table>	
			</div>
			<div align="center">
				<table width="100%" border=1 cellpadding="5" cellspacing="0">
					<thead>
						<th align="left">Particulars</th>
						<th align="right" width="80">Amount(Rs)</th>
					</thead>
					<tbody>
						<tr>
							<td><p>For membership registration for this month </p></td>
							<td align="right"><b><?php echo $invoice_credit_note_det['amount'];?></b></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div align="right" style="width:99%">
				<br>
				<br>
				<br>
				<br>
				<br>
				<br>
				<b style="font-size: 120%">Authorised Signatory</b>
			</div>
		</div>
<?php 	
		}
	}
	
?>

<div id="customer_acknowlegment" style="display: none;margin-top: 30px;">
	<div style="font-family: arial;">
		<hr style="margin:0px;">	
		<h3 style="margin-top:4px;margin-bottom: 4px;">Customer Acknowledgement</h3> 
		<div style="margin: 3px auto">
		<p>This it to acknowledge that we have received all products corresponding to above listed <?php echo count($ttl_inv_list) ?> Invoices of total sum of Rs <b><?php echo $ttl_inv_amt;?>/-</b>
		<br>
		<?php if($is_pnh) {?>
		<span style="font-size: 14px;"><b><?=$credit_days ?> Days Payment</b></span>
		<?php } ?>
		</p>
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
						<br><br><br>
						Seal &amp; Signature : _______________________
						</div>
					</td>
				</tr>
			</table>
		</div>
		
		<hr>
		<div>
			<h3 style="margin: 0px;margin-bottom: 10px;">For Office use Only</h3>
			<table cellpadding="0" cellspacing="5" width="100%">
				<tr>
					<td colspan="2" align="left">Payment Mode <b style="border:2px solid #000">&nbsp;&nbsp;&nbsp;</b> &nbsp; Cash <b style="border:2px solid #000">&nbsp;&nbsp;&nbsp;</b> &nbsp; Cheque  <b style="border:2px solid #000">&nbsp;&nbsp;&nbsp;</b> &nbsp; DD </td>
					<td colspan="2" align="left">Instrument No : ______________;&nbsp;&nbsp; Instrument Date :_____________</td>
				</tr>
				<tr>
					<td colspan="4">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="4" align="left">Amount(Rs) : _______________ &nbsp;&nbsp;&nbsp;&nbsp; Executive Name :______________________&nbsp;&nbsp;&nbsp;&nbsp; Executive Sign :_______________ </td>
				</tr>
				<tr style="display:none"><td colspan="4" align="left">Notes : ____________________________________________________________________________________________________________________________________________ <br><br> ____________________________________________________________________________________________________________________________________________ </td></tr>
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
		
		$('.top_tax_inv_bar').hide();
		
		$('.pnh_logo').hide();
	var inv_html = '<div style="margin-top:20px;">'+$("#invoice").html().replace('TAX INVOICE','TAX INVOICE');
		$('.pnh_logo').show();
		$('.customer_ship_details').hide();
		inv_html += '<div style="page-break-before:always">';
		inv_html += '</div>';
		
		$('.tax_block_content').hide();
		$('.inv_logo_area').hide();
		//$('.pnh_inv_print_header').show();
		$('.eoe_txt').hide();
		
		
		//inv_html += '<div style="margin-top:30px;">'+$("#invoice").html()+'</div>';
		$('#tax_inv_ack_copy').hide();
		//inv_html += '<div style="margin-top:0px;">';
		//inv_html += $('#tax_inv_ack_copy').html();
		//inv_html += '</div>';
		
		
		
		
		$('.customer_ship_details').hide();
		//inv_html += '<div style="margin-top:0px;">';
		//inv_html += $('#customer_acknowlegment').html();
		//inv_html += '</div>';
		inv_html += '</div>';
		
		if($('#gate_pass_copy').length)
		{
			inv_html += '<div style="page-break-before:always">';
			inv_html += '</div>';
		
			$('#gate_pass_copy').show();
			inv_html += '<div style="margin-top:0px;">';
			inv_html += $('#gate_pass_copy').html();
			inv_html += '</div>';
		}
		
		$('.tax_block_content').show();
		
	myWindow.document.write(inv_html);	
		$('.eoe_txt').show();	
		$('.inv_logo_area').show();
		//$('.pnh_inv_print_header').hide();
		$('.top_tax_inv_bar').show();
		$('#tax_inv_ack_copy').hide();
		$('#gate_pass_copy').hide();
		
<?php }else { ?>
myWindow.document.write($("#invoice").html());
<?php } ?>
myWindow.focus();
myWindow.print();
}




function print_tax_acknowledgement(ele)
{
	myWindow=window.open('','','width=950,height=600,scrollbars=yes,resizable=yes');
	var inv_html = $('#tax_inv_ack_copy').html();
		inv_html += '<div style="margin-top:0px;">';
		inv_html += $('#customer_acknowlegment').html();
		inv_html += '</div>';
	
	myWindow.document.write(inv_html);
	myWindow.focus();
	myWindow.print();
}


function printdispatchdoc(ele)
{
	myWindow=window.open('','','width=950,height=600,scrollbars=yes,resizable=yes');
	myWindow.document.write($('#dispatch_document').html());
	myWindow.focus();
	myWindow.print();
}

function printcreditnote(ele)
{
	inv_html = '';
	if($('#invoice_credit_note').length)
	{
		inv_html = $('#invoice_credit_note').html();
	}
	
	myWindow=window.open('','','width=950,height=600,scrollbars=yes,resizable=yes');
	myWindow.document.write(inv_html);
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
	location.href = location.href; 
}

</script>


<STYLE TYPE="text/css">
     H2.page_break{page-break-before: always}
     .note{margin-bottom:5px;border-bottom:1px solid #e3e3e3;}
</STYLE> 
<h2>&nbsp;</h2>