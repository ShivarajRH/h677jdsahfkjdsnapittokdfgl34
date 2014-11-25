<?php
/**
 * @author Shivaraj <shivaraj@storeking.in>_Sep_12_2014
 */
$prod_imei_list = array();
?>
<style>
#scanned_summ{
	width: 160px;background: tomato;bottom: 0px;left:0px;position: fixed;border-top:5px solid #FFF;
	text-align: center;
	color: #FFF;
	font-size: 32px;
}
#scanned_summ h3{
	font-size: 20px;margin-top:10px;margin-bottom: 0px;
}
.scanned_summ_total{
	padding:5px;
}
.scanned_summ_stats{
	padding:5px;font-size: 15px;font-weight: bold;text-align: left;border-bottom: 1px dotted #FFF;
}
.ttl_num{
	float: right;font-size: 18px;
}
.have {
	background: yellow !important;
	font-weight: bold;
	text-align: center;
}

.scanned {
	background: #aaa;
	font-size: 110%;
}

.scanned .have {
	background: #f55 !important;
	color: #fff;
	font-size: 170%;
}
.partial {
	background: orange !important;
}
.fully_processed {
	background: #D39B9B !important;
}

.disabled {
	background: #aaa !important;
	color: #FFF !important;
}
.done {
	background: #afa;
}
.imeis{width: 250px;}
.remove_scanned{padding:5px;font-size: 11px;color:#cd0000}
.imei_inp_list{padding-left:0px; list-style-type: none;}
</style>
<div class="container">
	<?php $returnmsg='';
	if($transfer_det['transfer_option']==1)
	{
		$title_msg="Scan &amp; Pack Partner Stock - ".$transfer_det['partner_name'];
	}
	else
	{
		$returnmsg="Return ";
		$title_msg="Scan &amp; Pack Return Partner Stock - ".$transfer_det['partner_name'];
	}
	?>
	<div class="fl_right">
		<table>
			<tr>
				<td><?=$returnmsg;?>Transfer Id:</td>
				<td><a href="<?=site_url('admin/partner_transfer_view/'.$transfer_id);?>" target="_blank">#<?=$transfer_id;?></a></td>
				<td>Partner Transfer No.</td>
				<td><?=$transfer_det['partner_transfer_no'];?></td>
			</tr>
			
		</table>
		
	</div>
	<h2><?=$title_msg;?></h2>
	
	<div style="padding: 5px 10px; position: fixed; bottom: 50px; background: #ffaa00; right: 10px;">
		Scan Barcode : <input class="inp" id="scan_barcode" style="padding: 5px;"> <input type="button" value="Go" onclick='validate_barcode()'></div>

	<div id="scanbyimei" style="padding: 5px 10px; position: fixed; bottom: 50px; background: #ffaa00; right: 326px;display:none">
		Scan Imeino : <input class="inp" id="scan_imeino" style="padding: 5px;"> <input type="button" value="Go" onclick='validate_imeino()'>
	</div>
</div>
<form action="<?=site_url("admin/partner_stock_scan_process");?>" method="post" name="topform" class="" onsubmit="return fn_submit_transfer_details(this)">
<table class="datagrid" style="margin-top: 20px;" width=100%>
	<thead>
		<tr>
			<th>Deal Picture</th>
			<th>Deal</th>
			<th>Deal Qty</th>
			<th>Product</th>
			<th>MRP</th>
			<th>Prod. Scan Qty</th>
			<th style="padding: 0px;">
				<div style="padding: 5px;">Stock MRPs</div>

				<table class="subgrid" cellpadding="0" cellspacing="0" style="border: 0px !important; width: 100%; background: #fcfcfc !important; font-size: 11px;">
					<tr>
						<td style="background: #fcfcfc !important; vertical-align: middle; color: #000" width="50">
							<div style="">ProductID</div>
						</td>
						<td style="background: #fcfcfc !important; vertical-align: middle; color: #000" width="40">
							<div style="">MRP</div>
						</td>
						<td style="background: #fcfcfc !important; vertical-align: middle; color: #000" width="60">
							<div style="">Expire On</div>
						</td>
						<td style="background: #fcfcfc !important; vertical-align: middle; color: #000" width="100">
							<div style=""><small>Rack Name</small></div>
						</td>
						<td style="background: #fcfcfc !important; vertical-align: middle; color: #000" width="50">
							<div style="">Reserved Stock</div>
						</td>
						</td>
						<td style="background: #fcfcfc !important; vertical-align: middle; color: #000"  width="100">
							<div style=" ">&nbsp;&nbsp;</div>
						</td>

					</tr>
				</table>

			</th>

			<th>Scanned</th>
			<th>Status</th>
		</tr>
	</thead>
	<tbody class="stk_cont">
		<?php
		//echo '<pre>';print_r($batch_list);echo '</pre>';
		$has_imei_scan=0;
		$total_scan_prod=0;
		foreach($transfer_prod_det as $t_prod)
		{
			$prd_id=$t_prod['product_id'];
			//$stock_info_id=$t_prod['stock_info_id'];
			
			
			
			//echo '<pre>';print_r($t_prod);echo '</pre>';
			
			$p_has_imei_scan = $this->db->query("select is_serial_required from m_product_info where product_id = ? ",$t_prod['product_id'])->row()->is_serial_required;
			
			$has_imei_scan += $p_has_imei_scan;
			
			
			
		?>
		<tr class="bars prod_scan bar<?=$t_prod['barcodes']?> itemid_<?=$t_prod['itemid'];?>" itemid="<?=$t_prod['itemid'];?>">
			<td>
				<div style="width:100px;height: 100px;float: left">
					<a target="_blank" href="<?php echo $t_prod['image_url'];?>"><img width="100%" src="<?php echo $t_prod['image_url'];?>" /></a>
				</div>	
			</td>
			<td>
				<a href="<?=site_url('/admin/deal/'.$t_prod['itemid']);?>" target="_blank"><?=$t_prod['deal_name']?></a>
				
<?php
				if($t_prod['is_combo']==1){
?>
					<br><br>
					<span style="padding:5px;background-color: cadetblue;"><small>Combo Deal</small></span>
<?php
				}
?>
			</td>
			<td>
				<?=$t_prod['item_transfer_qty']?>
			</td>
			<td class="prod">
				<a href="<?=site_url('/admin/product/'.$prd_id);?>" target="_blank"><?=$t_prod['product_name']?></a>
			</td>
			<td><?=(double)$t_prod['mrp']?></td>
			<td class="qty prod_req_qty" prd_lnk_qty="<?=$t_prod['qty']?>" ><?=$t_prod['qty']?></td>
			<td style="padding: 0px;">
					<div style="padding: 0px; margin: 0px; font-size: 85%;">
						
						<input type="hidden" class="itemid" value="<?=$t_prod['itemid']?>" size="5"/>
						<input type="hidden" class="product_id" value="<?=$t_prod['product_id']?>" size="5"/>
						<input type="hidden" class="item_transfer_qty" value="<?=$t_prod['item_transfer_qty']?>" size="3"/>
						<input type="hidden" class="product_transfer_qty" value="<?=$t_prod['product_transfer_qty']?>" size="3" />
						<input type="hidden" class="is_combo" value="<?=$t_prod['is_combo']?>" size="1" />
						<input type="hidden" class="combo_<?=$t_prod['itemid'];?>
												combo_<?=$t_prod['itemid'];?>_<?=$t_prod['product_id']?>"
												value="<?=$t_prod['is_combo']?>" size="1" />
						<input type="hidden" class="combo_ttl_prod" value="<?=$t_prod['combo_ttl_prod']?>" size="3" />
							
						<?php
						
						$stock_loc_list_res=$this->db->query("SELECT stk.stock_id,stk.rack_bin_id,stk.location_id,stk.product_barcode,stk.mrp,stk.expiry_on
																	,rstk.itemid,rstk.qty,CONCAT(rb.rack_name,'-',rb.bin_name) AS rbname,di.name AS deal,di.is_combo,SUM(rstk.qty) AS combo_ttl_prod
													 FROM t_partner_reserved_batch_stock rstk
													 LEFT JOIN t_stock_info stk ON stk.stock_id=rstk.stock_info_id
													 LEFT JOIN m_rack_bin_info rb ON rb.id = stk.rack_bin_id
													 join king_dealitems di on di.id=rstk.itemid
													WHERE rstk.transfer_id=? AND rstk.itemid=? AND rstk.product_id=? GROUP BY rstk.id ",array($transfer_id,$t_prod['itemid'],$prd_id) );
						$stock_loc_list=$stock_loc_list_res->result_array();
			
						$ttl_req_stk=0;
						$show_add_btn = 0;
						$has_reserv_bc_qty = 0;
						foreach($stock_loc_list as $stock_loc)
						{
							$ttl_req_stk+=$stock_loc['qty'];
		
							$prod_bc=$stock_loc['product_barcode'];
							$is_combo=$stock_loc['is_combo'];
							$combo_ttl_prod=$stock_loc['combo_ttl_prod'];
							
							$show_add_btn += (strlen($prod_bc)==0)?1:0;
							
							$scan_by_bc = 0;
							if(strlen($prod_bc))
							{
								$has_reserv_bc_qty += 1;
								$scan_by_bc = 1;
							}	
							$stk_id=$stock_loc['stock_id'];
							$mrp=$stock_loc['mrp'];
							$loc_id=$stock_loc['location_id'];
							$rackbinid=$stock_loc['rack_bin_id'];
							$prod_expiry_on=$stock_loc['expiry_on'];
							
//							if(!round($stock_loc['stk']+$ttl_reserved_qty))
//								continue;
							?>
							<table class="subdatagrid " width="100%" border="0" cellpadding="5">
									
									<tr barcode="<?php echo ($prod_bc? $prod_bc: 'BLANK');?>" class="locations">
										<td width="50">
											<div style=" text-align: center;"><?=$prd_id;?></div>
										</td>
										<td style="vertical-align: middle; text-align: center;" width="40">
											<div><b><?php echo round($mrp,2);?></b></div>
										</td>

										<td style="vertical-align: middle; text-align: center;" width="60">
											<div><small><?php echo $prod_expiry_on;?></small></div>
										</td>

										<td style="vertical-align: middle; text-align: center;" width="90">
											<div style=" color: red; font-size: 12px;">
												<?=$stock_loc['rbname'];?>
											</div>
										</td>
										
										
										
<!--										
										<td style="width: 30px !important;" width="10">
											<div class="scan_proditems" style="width: 30px; text-align: center;">-->
										<td style="vertical-align: middle; ">
											<div class="scan_proditems" style=" text-align: center; width: 60px;">
												<span><?php echo $stock_loc['qty'];?></span>


												<input type="hidden" name="transfer_id" value="<?=$transfer_id;?>">

												<input type="hidden" rb_id="<?=$loc_id.'_'.$rackbinid;?>" rb_name="<?php echo $stock_loc['rbname']?>"
														dealname="<?php echo addslashes($stock_loc['deal']);?>" 
														itemid="<?=$t_prod['itemid'];?>"
														transfer_id="<?=$transfer_id;?>"
														order_id="" consider_for_refund="" disc="" ordmrp=""
														stk_info_id="<?=$stk_id;?>"
														mrp="<?=$mrp; ?>"
														reserv_qty = "<?=$stock_loc['qty']?>" 
														stk="<?=$stock_loc['qty'];?>"
														pid="<?= $prd_id;?>"
														name="scan_det[<?=$t_prod['itemid']?>][<?=$prd_id?>][<?=$stk_id?>][<?php echo ($prod_bc? $prod_bc: 'BLANK');?>]"
														value="0"
														expiry_on="<?=$prod_expiry_on;?>"
														is_combo="<?=$is_combo;?>"
														combo_ttl_prod="<?=$combo_ttl_prod;?>"
														class="scan_proditem pbcode_<?=$prod_bc?>
																		<?php echo $scan_by_bc?'scan_bybc':'' ?>
																	   pbcode_<?=$prod_bc?$prod_bc:$stk_id.'_nobc' ?>
																	   pbcode_<?=$prod_bc?$prod_bc:$stk_id.'_nobc' ?>_<?=(double)$mrp;?>_<?=$loc_id.'_'.$rackbinid;?>
																	   pbcode_<?=$prod_bc?$prod_bc:$stk_id.'_nobc' ?>_<?=(double)$mrp;?>_<?=$loc_id.'_'.$rackbinid;?>_<?=$stk_id;?>_<?=$t_prod['itemid'];?>_<?=$transfer_id;?>_<?=$prod_expiry_on;?>"
																	
														style="width: 20px !important;" />


														&nbsp;&nbsp;&nbsp;&nbsp;

														<input mrp="<?=$mrp; ?>" stk_i="<?=$stk_id ?>"
																	itemid="<?=$t_prod['itemid']?>" pid="<?php echo $prd_id;?>"
																	title="Scan to update via barcode or click here"
																	class="prod_stkselprev <?php echo !$show_add_btn?'disabled':"";?>"
																	ttl_stk="<?php echo $stock_loc['qty'];?>"
																	has_imei_scan="<?=$p_has_imei_scan;?>"
																	onclick="upd_selprodstk(this)" type="button"
																<?php echo !$show_add_btn?'disabled':"";?> value="0">

											</div>
										</td>
										<td style="vertical-align: middle;">
												<div class="scan_proditems" style=" text-align: center;">
												<?php 
													//============= START IMEI CODE ===================
													$imeis=$this->db->query("SELECT a.imei_no,b.mrp,b.product_barcode,b.location_id,b.rack_bin_id,b.stock_id
																		FROM t_imei_no a 
																		JOIN t_stock_info b on a.stock_id = b.stock_id 
																		WHERE a.product_id=? and a.status=0 
																		GROUP BY a.id",array($prd_id) )->result_array();// AND b.stock_id=? $stk_id
													if($p_has_imei_scan)
													{
														//print_r($imeis); //die();
														// prepare imeino list for allotment 
														foreach($imeis as $im)
														{
															$prod_imei_list[$im['imei_no']] = array($prd_id,($im['product_barcode']?$im['product_barcode']:$stk_id.'_nobc').'_'.((int)$im['mrp']).'_'.$im['location_id'].'_'.$im['rack_bin_id'].'_'.$im['stock_id']);
														}

														echo '<ul class="imei_inp_list">';
														for($p=0;$p<$stock_loc['qty'];$p++) //$t_prod['qty']
														{
												?>
															<li style="width: 122px;">
																<input type="text" name="imei_list[<?=$t_prod['itemid'];?>][<?=$prd_id;?>][<?=$stk_id;?>][]" readonly="readonly" order_id="" transfer_id="<?=$transfer_id;?>" itemid="<?=$t_prod['itemid']?>" expiry_on="<?=$prod_expiry_on;?>" class="imei<?=$prd_id;?> imei<?=$prd_id?>_unscanned imeis imeip<?=$p?>" style="width: 100px;padding:2px;font-size: 9px" value="" >
															</li> 
												<?php 
														}
														echo '</ul>';
													}
													//============= END IMEI CODE ===================
												?>


											</div>

										</td>
									</tr>
								</table>
				<?php	}
						?><?php //echo $stk_i;
						
						?>
							
							
							
						<!--</table>-->
					</div>
			</td>
			

			
			<td class="have" style="vertical-align: middle;">0</td>
			<td class="status" style="vertical-align: middle;">PENDING</td>
		</tr>
	<?php 
			$total_scan_prod+=$ttl_req_stk;
		}
		?>
	</tbody>
</table>

<div style="margin-top: 20px;">
	<!--<input type="button" value="Check" style="padding: 7px 10px;" onclick='checknprompt()' >--> 
	<input type="submit" value="Process Stock Transfer" style="float: right; padding: 7px 10px;">
	<!--onclick='process_invoice(1);'-->
</div>

</form>

<table cellpadding="5">
	
				
		<tr>
			<td valign="top">
					<h4 style="margin: 2px 0px;">Transfer Remarks</h4>
					
					<div style="padding: 5px; background: #ffffd0; padding: 5px; font-size: 12px;line-height: 20px;">
						<span class=""><?=$transfer_det['transfer_remarks']; ?></span>
					</div>
			</td>
		</tr>
</table>

<div id="scanned_summ" >
	<h3>Scanned Qty</h3>
	<div class="scanned_summ_total"><span id="summ_scanned_ttl_qty">0</span> / <span id="summ_ttl_qty"><?=$total_scan_prod;?></span></div>
	<div class="scanned_summ_stats"><span style="font-size: 13px;">Deals </span> : <span class="ttl_num" id="summ_ttl_scanned_prod">0</span></div>	
</div>


<div id="mutiple_mrp_barcodes" title="Choose Stock from Multiple Mrps">

<div id="bc_mrp_list">
	<table class="datagrid" cellpadding="0" cellspacing="0" width="100%">
		<thead>
		<th><b>Deal</b></th>	
		<th><b>MRP</b></th>
		<th><b>RackBin</b></th>
		<th><b>Alloted Qty</b></th>
		<th><b>Expiry On</b></th>
		<th>&nbsp;</th>
		</thead>
		<tbody>

		</tbody>
	</table>
</div>
</div>

<script>
// <![CDATA[

	var has_imei_scan=<?=$has_imei_scan;?>;
		
	var prod_imeino_list = new Array();
	var prod_imeino_stock_info = new Array();
	<?php
//			print_r($prod_imei_list); die();
		if($prod_imei_list)
			foreach($prod_imei_list as $p_imeino => $i_prod_det)
			{
	?>
				prod_imeino_list["<?php echo $p_imeino;?>"] = <?php echo $i_prod_det[0];?>; // product_id
				prod_imeino_stock_info["<?php echo $p_imeino;?>"] = "<?php echo $i_prod_det[1];?>"; // stock det
	<?php				
			}
	?>
	
		var transfer_option=<?=$transfer_det['transfer_option'];?>;
//	]]>
</script>
<script type="text/javascript" src="<?=base_url()?>/min/index.php?g=partner_stk_transfer&<?=strtotime(date("Y-m-d"));?>&1=1"></script>
    	 