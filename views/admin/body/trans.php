<?php 
$trans_status_flags =  $this->config->item('trans_status');
$order_status_flags = $this->config->item('order_status');
$status_color_codes = $this->config->item('status_color_codes');
$invoice=$this->db->query("select 1 from king_invoice where transid=?",$trans['transid'])->num_rows();
if($invoice>0)
	$invoice=true;
else
	$invoice=false;
	
$user=$this->session->userdata("admin_user");;
$processed=false;
$pending=false;
$pros=$shiis=$rejs=0;
foreach($orders as $o)
{
	if($o['admin_order_status']==0)
		$pending=true;
	if($o['admin_order_status']==1 || $o['admin_order_status']==2)
		$processed=true;
	if($o['admin_order_status']==2)
		$shiis++;
	if($o['admin_order_status']==6)
		$rejs++;
}
$shipped=false;
if($shiis+$rejs==count($orders))
{
	$shipped=true;
	$processed=true;
}
$coupon=$this->db->query("select coupon from king_used_coupons where transid=? and status=1",$trans['transid'])->row_array();
$total_rf_amount = $this->db->query("select sum(amount) as total_rf_amount from king_refunds where transid = ? ",$trans['transid'])->row()->total_rf_amount;



$giftcard_recp_dets = array();
// check for giftcard availability in transaction orders
foreach($orders as $p){
	if($p['is_giftcard'])
	{
		$giftcard_recp_dets['name'] = $p['gc_recp_name'];
		$giftcard_recp_dets['email'] = $p['gc_recp_email'];
		$giftcard_recp_dets['mobile'] = $p['gc_recp_mobile'];
		$giftcard_recp_dets['message'] = $p['gc_recp_msg'];
	}
}
 


?>


<style type="text/css">
	#trans_stat_blk{
		padding:5px;color: #666;
		border:0px solid #e3e3e3;
		padding-left:0px;
		margin:10px 0px;
	}
	#trans_stat_blk b{ 
		padding:2px;
		color: #000; 
		text-align:right;
		margin-left:15px;
	}
	.ui-widget-header{
		background: #e3e3e3;
		border:none;
	}
</style> 

<script type="text/javascript">
	function toggle_priority(transid){
		if(confirm("Are you sure you want to change transaction priority ?")){
			show_loading("Changing  Priority,Please wait...");
			$.post(site_url+'/admin/chng_transpriority','transid='+transid,function(resp){
				reload_page();
			});
		}
	}
</script>
<?php 
	$p = (isset($orders[0])?$orders[0]:'');
	
?>

<div class="container" align="left" style="margin: 0px 10px;width: 98%">
	<?php 
			$inv_stockalert = $this->session->flashdata('inv_stock_alert'); 
			if($inv_stockalert)
				echo '<div style="font-weight:bold;color:#cd0000;font-size:13px;text-transform:capitalize;background:#ffffa0;padding:10px;" align="center">'.$inv_stockalert.'</div>';
	?>
	<h2 style="font-size: 16px;">
	
		<span style="font-size: 12px;font-weight: bold;float: right">Transaction Priority : <?php echo ($trans['priority']?'<span style="color:#cd0000">High</span>':'Low')?>  
 <?php 
 	if($trans['admin_trans_status'] <5){
 ?>
 - <a href="javascript:void(0)" onclick="toggle_priority('<?php echo  $trans['transid'];?>')">Change</a>
 <?php }?>
 </span>
		<span style="float: right;font-size: 12px;margin-right: 10px;">
			<a style="font-size: 12px;" target="_blank" href="<?=site_url("callcenter/trans/{$trans['transid']}")?>">Check PG details</a>
		</span>
		Transaction : <?=$trans['transid'].' - ('.$trans['id'].')'?>  
	</h2>
	
	<table width="100%" cellpadding=5 cellspacing=0>
		<tr>
			<td valign="top">
				<!-- Transaction status Block Start -->
				<div id="trans_stat_blk" style="margin:0px;">
					<table cellpadding="0" width="100%" cellspacing="0" border=0 style="border: 1px solid #aaa;background: #f7f7f7;font-size: 12px;padding:10px;line-height: 25px;">
					<tr>
						<td align="left" valign="top">
							 
							<b>Ordered On </b> : <?=date("d/M/Y g:ia ",$orders[0]['time'])?>
							<br />
							<b>Status  </b> : <span style="color: <?php echo $status_color_codes[$trans['admin_trans_status']]?>;font-weight: bold;"><?php echo $trans_status_flags[$trans['admin_trans_status']];?> </span>
							<br />
							<b>Package Type  </b> : <span style="color: <?php echo $status_color_codes[$trans['admin_trans_status']]?>;font-weight: bold;color:green;background: yellow;padding:5px;"><?php echo ($p['giftwrap_order']?'Gift Wrap'.'(Rs '.$trans['giftwrap_charge'].')':'Normal');?> </span>
							 
						</td>
						<td align="left" valign="top"> <b>Total Amount  </b> : Rs <?=$trans['amount']?>
							<br />
							<b>Coupon  </b> : <?=empty($coupon)?"<i>none</i>":$coupon['coupon']?> 
							
						</td> 
						<td align="left" valign="top">
						 	<b>Payment Mode </b>: Rs <span style="background:yellow;font-size: 11px;padding:3px;"><?=$trans['mode']==0?"Payment Gateway":"CASH ON DELIVERY"?></span>
						 	 
						 	<br /> <b>Refund Amount </b>:Rs <?php echo $total_rf_amount*1; ?> <a href="javascript:void(0)" onclick="show_refundbox('<?php echo  $trans['transid'];?>')">add</a>
						</td> 
					</tr>
					</table>
				</div>
				<!-- Transaction status Block End -->
				
				
				
				
				<?php 
					if(isset($giftcard_recp_dets['name'])){
				?>	
				<div style="background: margin:5px 0px;background: #ffffd0;padding:10px;">	
					<h3 align="left" style="margin:0px;clear:both;">Giftcard Recipient Details</h3>
					<div style="padding:3px;">
						<b>Name</b> : <?php echo $giftcard_recp_dets['name'];?> <br />
						<b>Email</b> : <?php echo $giftcard_recp_dets['email'];?> <br />
						<b>Mobile</b> : <?php echo $giftcard_recp_dets['mobile'];?> <br />
						<b>Message</b> : <?php echo $giftcard_recp_dets['message'];?> <br />
					</div>
				</div>
				<?php }?>
				
				<br />
				<!-- Orders in Transaction Start -->
				<div id="trans_orderlist">
					<h3 align="left" style="margin:0px;clear:both;">Orders in transaction</h3>
					<table class="table_grid_view table_order_list" style="background:#fff;" border=0 cellspacing=0 cellpadding=5 width="100%">
					<thead>
							<th> <input type="checkbox" value="1" name="sel_all_orders"  > </th>
							<th>Order ID</th>
							<th>Status</th>
							<th>Type</th>
							<th>Product Name</th>
							<th align="center">QTY</th>
							<th align="center">Stock</th>		
							<th>Priority</th>
							<th>Invoiceno</th>
							<th>Last updatedon</th>
							 
					</thead>
					<tbody>
					<?php 
					
					
						$allow_create_invoice = 0; 
						$j = 0;
						foreach($orders as $p){ 
						
						$order_stock_status = 0;
						$stock_available = 1;
						$stock = 0;
						if($p['admin_order_status']==0){
							$stock=$this->db->query("select available from king_stock where itemid=?",$p['itemid'])->row_array();
							if(empty($stock)) $stock=0; else $stock=$stock['available'];
								if($stock<$p['quantity']){
									$order_stock_status = '<span style="background:red;color:#fff;padding:3px;">'.$stock.'</span>';
									$stock_available = 0; 
								}else{
									$order_stock_status = '<span >'.$stock.'</span>';
								}
							$allow_create_invoice += 1;
						}else{
							$order_stock_status = "";
						}
						
						
						$prod=$this->db->query("select url,dealid from king_dealitems where id=?",$p['itemid'])->row();
						
						$j++;
					?>
					<tr class="<?php echo (($j%2==0)?'even_row':'odd_row');?>" >
					
					<td width="30">
						<?php 
							
							if($p['admin_order_status'] < 6 && $p['admin_order_status']!=4 ){
						?>
								<input ord_stat="<?php echo $p['admin_order_status']; ?>" stock_avial="<?php echo $stock;?>" type="checkbox" name="oids[]" value="<?=$p['id']?>" class="trans_oids">
						<?php 
							}
						?>
					</td>
					
					<td width="80"><a href="<?=site_url("admin/vieworder/{$p['id']}")?>"><?=$p['id']?></a></td>
					
					<td width="80" align="left">
					<b><?=$order_status_flags[$p['admin_order_status']];?></b><br>
					</td>
					<td width="80"><b><?=$p['is_giftcard']?'Giftcard':'Product'?></b></td>
					<td width="400">
					<a href="<?=site_url("admin/deal/".$prod->dealid)?>" target="_blank"><?=$p['item']?></a>
					
					<?php $buyer_options=unserialize($p['buyer_options']);
					if(is_array($buyer_options))
					foreach($buyer_options as $mean=>$opt){?>
					<div><?=$mean?>:<?=$opt?></div>
					<?php }?>
					
					<div style="margin-top:5px;text-align: left;">
						<span style="float: right;margin: 5px;"><a href="<?=site_url($prod->url)?>" target="_blank" style="font-size: 11px;color: #cd0000;">view deal</a></span>
						<span style="color: #565656;font-size: 11px">
						<b>MRP</b> : <?php echo 'Rs '.$p['i_orgprice'];?> |
						<b>Offer</b> : <?php echo 'Rs '.$p['i_price'];?> |
						<b>Disc</b> : <?php echo 'Rs '.round($p['i_coup_discount']+$p['i_discount']);?>
						</span>
						
					</div>
					
					</td>
					
					<td align="center">
					<div align="center">
					<?=$p['quantity']?>
					</div>
					<?php if($p['quantity'] > 1 && $p['admin_order_status'] == 0 ){?>
					<a href="javascript:void(0)" onclick='$(".cqtyform",$(this).parent()).show();$(this).hide();'>edit</a>
					
					<div class="cqtyform" style="display:none;">
					<form action="<?=site_url("admin/changeqty/{$p['id']}")?>" method="post">
					<select name="qty">
					<?php for($i=1;$i<=$p['quantity']-1;$i++){?>
					<option value="<?=$i?>"><?=$i?></option>
					<?php }?>
					</select>
					<br />
					<input type="submit" value="Change">
					
					<br />
					<input type="button" value="Cancel" onclick="$(this).parent().parent().hide();$(this).parent().parent().prev().show()">
					</form>
					</div>
					<?php }?>
					
					</td>
					
					
					<td align="center">
					<?php 
						echo $order_stock_status; 
						if($p['admin_order_status'] == 0){
							if(!$stock_available){
								echo '<br /><a style="padding-top:5px;" target="_blank" href="'.site_url('admin/stock/'.base64_encode($p['item'])).'">Update</a>';
							}
						}
					?>
					</td>
					 
					
					
					<td align="left"><?php if($p['priority']){?><span style="background:green;color:#fff;">HIGH</span><br>
					 
					<?php }else{?>none<?php }?></td>
					
					<td>
						<a href="<?php echo site_url('admin/invoice/'.$p['invoice_no'])?>" target="_blank" ><?php echo $p['invoice_no']; ?></a>
					</td>
					<td><?=$p['actiontime']==0?"na":date("g:ia d/m/y",$p['actiontime'])?></td>
					</tr>
					<?php } ?>
					</tbody>
					</table>

					<?php 
						$fs_list_res = $this->db->query("select fs.name,fs.pic,fs.available,fso.id,fso.fsid,fso.invoice_no 
																from king_freesamples_order fso 
																join king_freesamples fs on fs.id = fso.fsid 
																where transid = ? ",$trans['transid']);
						if($fs_list_res->num_rows()){
					?>
					
					<div style="padding:10px;background: #e3e3e3">
						<fieldset>
							<legend style="padding:5px;background: #cdcdcd">Free Samples in transactions</legend>
							<p style="margin: 0px;font-size: 10px;">
									Choose to add free samples to the invoice  - <a href="javascript:void(0);" onclick="$('.fs_sels').attr('checked',true)">Selectall</a> - <a href="javascript:void(0);" onclick="$('.fs_sels').attr('checked',false)">UnSelectall</a> 
								</p>
							<div style="padding:10px;">
								
								<?php 
									foreach($fs_list_res->result_array() as $fs){
											$fs_style = '';
											
											if($fs['available']){
												if($fs['invoice_no']){
													$fs_style="background:#fff;";
													$choose_fs = '<b>FS Sent - INVOICENO:'.$fs['invoice_no'].'</b>';
												}else{
													$choose_fs =  '<input type="checkbox" name="fs_sels['.$fs['id'].']" class="fs_sels" value="'.$fs['id'].'"> - <b>FreeSample not sent</b>';
													$fs_style="background:#ffffa0;";
												}
											}else{
												$fs_style="background:pink;";
												$choose_fs = '<b>FS not available</b>';
											}
								 ?>
									<div style="float:left;width: 200px;padding:10px;height:150px;border:2px dotted #e3e3e3;<?php echo $fs_style;?>">
										<?php 
											 echo '<div align="left">'.$choose_fs.'</div>';
										?>
											
											<div style="padding:5px;margin: 10px;height:70px;text-align: center;">
												<img alt="<?php echo   $fs['name'];?>" title="<?php echo   $fs['name'];?>" src="<?php echo IMAGES_URL?>/items/thumbs/<?php echo $fs['pic'].'.jpg';?>" >
											</div>
											 
											<h4 style="font-size: 10px;margin-top: 10px;"><?=$fs['name']?></h4>
									</div>
								<?php 		
									}
								?>
							</div>
						</fieldset>
					</div>
					<?php } ?>


					<div align="left" style="background: #ccc;padding:10px;">
						<?php 
							if($allow_create_invoice){
						?>
							<a href="javascript:void(0)" id="proc_t_formtrig">Create Invoice</a>  | 
						<?php 				
							} 
						?>
						<?php 
							if($trans['admin_trans_status'] < 5){
						?>
						<a href="javascript:void(0);cancel_orders()" id="cancel_orders_trig">Cancel Orders</a>  
						<?php }?>
					</div>
				</div>
				<!-- Orders in transaction end -->
				
			</td>
			<td style="width: 300px;" valign="top">
				<!-- Invoice Block Start -->
				
					<div id="invoice_details" >
						<h3 style="margin:0px;">Invoice Details</h3>
							 
							<?php
								$row_invlist = $this->db->query("select i.invoice_no,invoice_status,
																		tracking_id,delivery_medium,total_prints,
																		is_delivered,
																		date(shipdatetime) as shipdatetime,
																		if((sum(o.admin_order_status)/count(i.order_id)) = 3,0,1) as is_shipped 
																		from king_invoice i 
																		join king_orders o on o.id = i.order_id  
																		where i.transid = ? 
																		group by i.invoice_no 
																		order by invoice_status asc",$trans['transid'])->result_array();
								if(count($row_invlist)){
							?>
								<table class="table_grid_view" cellpadding="5" cellspacing="0" border="0"> 
										<thead>
											<th align="center" width="80">Invoice no</th>
											<th align="center">Shipment Details</th>
											<th align="center">Action</th>
										</thead>
										<tbody>
									<?php 		
										$j = 1;
											foreach($row_invlist as $inv_ind=>$row_inv){
												if($row_inv['invoice_status'] == 2){
													$style = 'color:#999';
												}else{
													$style = '';
												}
									?>
												<tr class="<?php echo (($j%2==0)?'even_row':'odd_row');?>" style="<?php echo $style;?>">
													 
													<td align="center" width="70">
														<a href="<?php echo site_url('admin/invoice/'.$row_inv['invoice_no'].'/auditing')?>" target="_blank" ><?php echo $row_inv['invoice_no']; ?></a>
														<br />
														<?php 
															if($row_inv['total_prints']){
														?>
														<b style="font-size: 11px;color:#666"><?php echo '('.$row_inv['total_prints'].' prints)';?></b>
														<?php }?>
														<br />
														<b><?php echo (($row_inv['invoice_status']==1 )?'Active':'Invoice Cancelled')?></b>
													</td>
													 
													<td align="left" width="200">
														<?php 
															if($row_inv['tracking_id']){
														?>
														<div>
															<b>Medium :</b> <span><?php echo $row_inv['delivery_medium']; ?></span>
															<br />
															<b>TrackID :</b> <span><?php echo $row_inv['tracking_id']; ?></span>
															<br />
															<b>ShipDate :</b> <span><?php echo $row_inv['shipdatetime']; ?></span>
															<br />
														</div>
														<?php }?>
														<div align="left">
															<?php 
																if($row_inv['invoice_status'] == 1 && $trans['admin_trans_status'] <= 4 ){
															?>
																<a href="javascript:void(0);update_shipment(<?php echo $row_inv['invoice_no'];?>)" >
																	<?php echo ($row_inv['tracking_id']?'Update/Send Details':'Update Details'); ?>
																</a> 
																
															<?php }?>
														</div>
													</td>
													 
													<td align="center">
														<?php 
															if($row_inv['invoice_status'] == 1){
																if($row_inv['is_delivered'] == 1){
																	echo "Delivered";
																}else{
																	echo (($row_inv['invoice_status']==1  && $trans['admin_trans_status'] < 5 )?'<a href="javascript:void(0)" onclick=cancel_invoice("'.$row_inv['invoice_no'].'"); style="color:#cd0000;text-decoration:underline">Cancel</a>':'');
																}
															}
														?>
														 
														
														 
														<?php 
																if(!$row_inv['is_shipped'] && $row_inv['invoice_status'] == 1){
																	echo '<div align="center" style="margin: 5px 0px;">OR</div>  <a href="javascript:void(0);" onclick=mark_delivered("'.$row_inv['invoice_no'].'") style="text-decoration:underline" >Mark Delivered</form>';
																}
														?>
															
														
													</td>
												</tr>
									<?php 		
													$j++;	
											}
									?>
									</tbody>
								</table>
							<?php }else{?>
								<div> invoice not available</div>
							<?php }?>
							</div>
				
				
				<!-- Invoice Block END -->
				<br />
				<!-- Customer Details Start -->
				<div id="customer_details">
					<h3 style="margin:0px">Customer Details</h3>
					<table class="customer_det" style="background:#f3f3f3;" border=0 cellspacing=3 cellpadding=3 width=98%>
					<tr>
						<td>
							<label><b>Name</b></label> : 
							<a href="<?=site_url("admin/user/{$transactor['userid']}")?>"><?=$transactor['name']?></a>
						</td>
					</tr>
					<tr>
						<td>
							<label><b>Email</b></label> : 
							<?=$transactor['email']?>
						</td>
					</tr>
					<?php 
						$is_billing_shipping_same = 1;
						if(is_array($p)){
							if(($p['bill_person'] != $p['ship_person']) || ($p['bill_address'] != $p['ship_address']) || ($p['bill_landmark'] != $p['ship_landmark']) || ($p['bill_city'] != $p['ship_city']) || ($p['bill_state'] != $p['ship_state']) || ($p['bill_pincode'] != $p['ship_pincode']) || ($p['bill_phone'] != $p['ship_phone']) || ($p['bill_email'] != $p['ship_email']) ){
								$is_billing_shipping_same = 0;
							}
					?>
					<tr>
						<td>
							<div id="billshipaddr_tabs" style="padding:5px;">
								<ul>
									<li><a href="#bill_addressview" style="padding:5px;font-size: 11px;">Billing</a></li>
									<li><a href="#ship_addressview" style="padding:5px;font-size: 11px;">Shipping</a></li>
								</ul>
								<div id="bill_addressview" style="padding:5px;">
									<div style="background: #ffffd0;padding:5px;margin: 0px;font-size: 12px;">
										<span style="float: right;">
											<a href="javascript:void(0)" onclick='edit_address("<?php echo $trans['transid']?>")'>Edit</a>
										</span>
										<?='<b style="font-size:12px;">'.$p['bill_person'].'</b> <p style="margin:0px;">'.$p['bill_address']."<br>".$p['bill_landmark']."<br>".$p['bill_city']."<br>".$p['bill_state']."<br>".$p['bill_pincode']." </p> <br> <b>Phone</b> :: ".$p['bill_phone']."<br> <b>Email</b> ::  ".$p['bill_email']?>
									</div>
								</div>
								<div id="ship_addressview" style="padding:5px;">
									<div style="background: #ffffd0;padding:5px;margin: 0px;font-size: 12px;line-height: 16px;">
										<?='<b style="font-size:12px;">'.$p['ship_person'].'</b> <p style="margin:0px;">'.$p['ship_address']."<br>".$p['ship_landmark']."<br>".$p['ship_city']."<br>".$p['ship_state']."<br>".$p['ship_pincode']." </p><br> <b>Phone</b> :: ".$p['ship_phone']."<br> <b>Email</b> :: ".$p['ship_email']?>
									</div>
								</div>
							</div>
							
							<script type="text/javascript">
								$('#billshipaddr_tabs').tabs();
							</script>
						</td>
					</tr>
					<?php } ?>
					</table>
				</div>
				<!-- Customer Details End -->
				
				<!-- Transaction notes start -->
				<br />
				<div id="transaction_notes" style="clear:both">
					<h3 style="margin:0px;clear:both;">Transaction Notes</h3>
					<div id="transnotes_feed" style="padding:0px;max-height: 150px;overflow: auto;">
						
					</div>
					<div style="padding:3px;">
							<div><b>Add note</b></div>
							<form id="transnote" >
								<input type="hidden" value="<?php echo $trans['transid']?>" name="transid">
								<textarea rows="2"  name="transnotes" style="padding:2px;color: #333;-moz-border-radius:10px;border:1px solid #e3e3e3;width: 98%"></textarea>
							<div>
								<input style="float: right" type="button" value="Submit" onclick="add_note()" class="sbutton">
								<input type="checkbox" name="prio_transnotes" value="1"> High Priority
								&nbsp;
								&nbsp;
								&nbsp;
							</div>
							</form>
						</div>
				</div>
				
				<!-- Transaction notes end -->
				
			</td>
		</tr>
	</table>
	

 
		
		
			 
			<div style="border:0px solid #ccc;background:#fff;padding:0px 10px;">
			 
				<div id="cancel_order" style="padding:20px 20px;display: none;">
				 		<h3 style="margin: 5px 0px">Order Cancellation</h3>
							<div style="font-size: 12px;padding:5px;">
								<textarea style="width:100%;height:200px;" name="cancellation_mail_text">Dear <?=$transactor['name']?>,
									
									We regret to inform you that below mentioned orders in transaction <?=$trans['transid']?> was cancelled due to non-availability.
									TBL_DISPLAY_CANCELLED_ORDERS
									The amount of these items upon discount has been refunded.
									The refunded amount will reflect in your bank account within 7-10 working days.
									
									We regret the inconvenience.
									
									Warm Regards,
									Team SnapItToday</textarea>
							</div>
						<input type="checkbox" value="1" name="notify_cus" checked="checked"> Send Notification to Customer 	
				</div>
				<form action="<?=site_url("admin/transstatus")?>" id="proc_t_form" method="post" style="display: none;">
					<input type="hidden" value="<?=$trans['transid']?>" name="transid">
					<input type="hidden" value="0" name="fs_linkids">
					<input type="hidden" value="process" name="action" >
					<input type="hidden" id="trans_oids_f" name="oids">
				</form>
			 
				<div id="update_order_shipment" style="display: none;">
					<h3 style="margin:5px 0px;font-size: 16px;">Update shipment Details</h3>
					<form id="upd_shipment_form"  action="<?=site_url("admin/transstatus")?>">
						<input type="hidden" name="action" value="shipment">
						<div id="ship_response" style="padding:3px;background: #ffffd0;font-size: 12px;text-align: center;display: none;"></div> 
						<table width="100%" cellpadding="5" cellspacing="3" style="font-size: 12px;">
							<tr>
								<td><b>Invoice No</b></td>
								<td>
									<input type="text" name="ship_invoice_no[]" value="0" >
								</td>
							</tr>
							<tr>
								<td><b>Ship emailid</b></td>
								<td>
									<input type="text" name="ship_email" value="<?php echo $p['ship_email']?>" >
								</td>
							</tr>
							<tr>
								<td><b>Delivery Medium</b></td>
								<td>
									<input class="ship_inputs" type="text" name="dmed">
								</td>
							</tr>
							<tr>
								<td><b>Tracking ID</b></td>
								<td>
									<input class="ship_inputs" type="text" name="track">
								</td>
							</tr>
							<tr>
								<td><b>Shipped on</b></td>
								<td>
									<input class="ship_inputs" type="text" name="shipdate" id="shipdate"> 
								</td>
							</tr>
							<tr>
								<td><b>Notify Customer</b></td>
								<td>
									<input class="ship_inputs" type="checkbox" name="notify_customer" > 
								</td>
							</tr>
							<tr>
								 
								<td colspan="2" align="right">
									<input type="submit" name="update_shipment" value="Update & Send Details" style="background: none repeat scroll 0 0 #FFED00;color: #000000;font-weight: bold;padding: 3px;"> 
									<input type="button" value="Close" onclick="$('#update_order_shipment').dialog('close')" style="background: none repeat scroll 0 0 #FFED00;color: #000000;font-weight: bold;padding: 3px;">
								</td>
							</tr>
						</table>
					</form>
				</div>
			</div>
	</td>
</tr>
<tr>
<td>
	<br />
	<div id="transaction_summary">
		<h3 style="margin: 0px;">Transaction Summary</h3> 
		<div style="max-height: 360px;overflow: auto;overflow-x:hidden ">
			 <?php 
			 $order_statuslog_summary = $this->db->query("select *  
			 												from king_transaction_activity 
													 		where reference_trans_id = ? 
													 		order by logged_on desc  
													 	",$trans['transid']);
			 if($order_statuslog_summary->num_rows()){
				 	$order_statuslog_summary = $order_statuslog_summary->result_array();
				 	foreach($order_statuslog_summary as $ord_summary){
			?>
						<div style="border-bottom: 2px dotted #aaa;padding:5px;background: #f7f7f7;font-size: 12px;">
							<p style="margin:0px;padding:3px;line-height: 18px;">
								<?php echo $ord_summary['message'];?>
							</p>
							<div align="right" style="font-size: 11px;text-align: right;color: #555">
						 		<?php 
						 			echo date("d/M/Y g:ia ",strtotime($ord_summary['logged_on']));
						 		?>
					 		</div>
					 	</div>
			<?php  	
					 }
			 } 
			?>
		</div>
	</div>
</td>
</tr>

</table>



<br />

</div>


<style type="text/css">

.ui-dialog .ui-dialog-titlebar{
	display:none; 
}
#ui-datepicker-div{
	z-index:99999 !important;
	font-size:12px;
}
.ui-dialog{
	font-size:12px;
}
.sbutton{
	background: none repeat scroll 0pt 0pt rgb(255, 237, 0); color: rgb(0, 0, 0); font-weight: bold; padding: 3px;
}
</style>

<div id="loading_dlg" align="center">
	<p id="loading_dlg_action" style="font-size: 16px;"></p>
	<img id="loading_dlg_icon" src="<?php echo IMAGES_URL.'/scroll_load.gif'?>" height="30" />
</div>

		<div id="refund_amount_block" style="display: none;">
					<h3 style="margin:5px 0px;font-size: 16px;">Refund Amount</h3>
					<form id="refund_amount_form"  action="<?=site_url("admin/refund_amount")?>">
						<input type="hidden" name="transid" value="<?php echo $trans['transid']?>">
						<div id="rfamount_response" style="padding:3px;background: #ffffd0;font-size: 12px;text-align: center;display: none;"></div> 
						<table width="100%" cellpadding="5" cellspacing="3" style="font-size: 12px;">
							<tr>
								<td><b>Refund Amount</b></td>
								<td>
									<input type="text" name="rf_amount" value="0">
								</td>
							</tr>
							<tr>
								<td><b>Tracking ID</b></td>
								<td>
									<input  type="text" name="rf_track_id">
								</td>
							</tr>
							<tr>
								<td><b>Datetime</b></td>
								<td>
									<input type="text" name="rf_datetime" id="rf_datetime"> 
								</td>
							</tr>
							<tr>
								<td colspan="2" align="right">
									<input type="submit" name="update_shipment" class="sbutton" value="Refund" > 
									<input type="button" value="Close" class="sbutton" onclick="$('#refund_amount_block').dialog('close')" >
								</td>
							</tr>
						</table>
					</form>
				</div>
<?php $p = (isset($orders[0])?$orders[0]:'');?>	
<div id="edit_address">
	<h3 style="margin:5px;">Edit Billing/Shipping Details </h3>
	<div style="padding:5px;background: #e3e3e3">
		<form id="edit_billshipaddress" action="<?php echo site_url('admin/upd_transaddress')?>" method="post">
		<input type="hidden" value="<?php echo $trans['transid']?>" name="ea_trans_id">
		<fieldset>
			<legend><h4 style="padding:5px;margin: 5px 0px">Billing Address</h4></legend>
		<table cellpadding="3" cellspacing="3" width="100%">
			<tr>
				<td><b>Name</b></td>
				<td> <input type="text" name="ed[bill_person]" value="<?php echo $p['bill_person']?>"></td>
			</tr>	
			<tr>
				<td><b>Address</b></td>
				<td><textarea rows="2" cols="25" name="ed[bill_address]"><?php echo $p['bill_address']?></textarea></td>
			</tr>	
			<tr>
				<td><b>Landmark</b></td>
				<td> <input type="text" name="ed[bill_landmark]" value="<?php echo $p['bill_landmark']?>"></td>
			</tr>
			<tr>
				<td><b>City</b></td>
				<td> <input type="text" name="ed[bill_city]" value="<?php echo $p['bill_city']?>"></td>
			</tr>	
				<tr>
				<td><b>State</b></td>
				<td> <input type="text" name="ed[bill_state]" value="<?php echo $p['bill_state']?>"></td>
			</tr>	
			<tr>
				<td><b>Phone</b></td>
				<td> <input type="text" name="ed[bill_phone]" value="<?php echo $p['bill_phone']?>"></td>
			</tr>	
			<tr>
				<td><b>Pincode</b></td>
				<td> <input type="text" name="ed[bill_pincode]" value="<?php echo $p['bill_pincode']?>"></td>
			</tr>
		</table>
		</fieldset>
		<fieldset>
			<legend><h4 style="padding:5px;margin: 5px 0px">Shipping Address - <input type="checkbox" value="1" id="copy_billing" name="copy_billing"> same as billing </h4></legend>
		<table cellpadding="3" id="edit_shipaddress" cellspacing="0" width="100%" style="display: none;"   >
			<tr>
				<td><b>Name</b></td>
				<td> <input type="text" name="ed[ship_person]" value="<?php echo $p['ship_person']?>"></td>
			</tr>	
			<tr>
				<td><b>Address</b></td>
				<td><textarea rows="2" cols="25" name="ed[ship_address]"><?php echo $p['ship_address']?></textarea></td>
			</tr>	
			<tr>
				<td><b>Landmark</b></td>
				<td> <input type="text" name="ed[ship_landmark]" value="<?php echo $p['ship_landmark']?>"></td>
			</tr>
			<tr>
				<td><b>City</b></td>
				<td> <input type="text" name="ed[ship_city]" value="<?php echo $p['ship_city']?>"></td>
			</tr>	
			<tr>
				<td><b>State</b></td>
				<td> <input type="text" name="ed[ship_state]" value="<?php echo $p['ship_state']?>"></td>
			</tr>	
			<tr>
				<td><b>Phone</b></td>
				<td> <input type="text" name="ed[ship_phone]" value="<?php echo $p['ship_phone']?>"></td>
			</tr>	
			<tr>
				<td><b>Pincode</b></td>
				<td> <input type="text" name="ed[ship_pincode]" value="<?php echo $p['ship_pincode']?>"></td>
			</tr>	
		</table>
		</fieldset>
		
		<div align="right">
			<input type="submit"  class="sbutton" value="Submit">
			<input type="button"  class="sbutton" onclick="$('#edit_address').dialog('close')" value="Close">
		</div>
		</form>
		
		
		
	</div>
</div>				
		
		
		
<div id="cancel_invorders" style="padding:20px 20px;display: none;">
 		<h3 style="margin: 5px 0px">Invoice Cancellation</h3>
 		<p>
 			You are about to cancel <span id="cnl_invoice_no"></span> 
 			<br />
 			Do you need to Cancel Orders in invoice <input type="checkbox" value="1" id="cnl_invorders" name="cnl_invorders" >  
 		</p>
			<div id="cnl_invordersconfirm" style="font-size: 12px;padding:5px;display: none;">
				<textarea style="width:100%;height:200px;" name="invcancellation_mail_text">Dear <?=$transactor['name']?>,
					We regret to inform you that below mentioned orders in transaction <?=$trans['transid']?> was cancelled due to non-availability.
					TBL_DISPLAY_CANCELLED_ORDERS
					The amount of these items upon discount has been refunded.
					The refunded amount will reflect in your bank account within 7-10 working days.
					
					We regret the inconvenience.
					
					Warm Regards,
					Team SnapItToday</textarea>
					
				<input type="checkbox" value="1" name="invnotify_cus" checked="checked"> Send Notification to Customer
			</div>
		 	
</div>				
				
<script type="text/javascript">
var site_url = '<?php echo site_url()?>';
function edit_address(transid){
	$('#edit_address').dialog('open');

	$('#edit_billshipaddress').unbind('submit').submit(function(){
		show_loading("Updating Please wait...");
		$.post($(this).attr('action'),$(this).serialize(),function(resp){
			update_loading(resp.message);
			if(resp.status){
				setTimeout('reload_page()',2000);	
			}else{
				//hide_loading();
			}
			
		},'json');
		
		return false;
	});
}

$('#cnl_invorders').change(function(){
	if($(this).attr('checked')){
		$('#cnl_invordersconfirm').show();
	}else{
		$('#cnl_invordersconfirm').hide();
	}
});

$('#copy_billing').change(function(){
	if($(this).attr('checked')){
		$('#edit_shipaddress').hide();
	}else{
		$('#edit_shipaddress').show();
	}
});

<?php 
	if(!$is_billing_shipping_same){
?>
	$('#copy_billing').trigger('change');
<?php 
	}else{
?>
	$('#copy_billing').attr('checked',true);
<?php 
	}
?>


$('#edit_address').dialog({width:400,autoOpen:false,modal:true});

	$('.table_grid_view tr').hover(function(){
		$(this).addClass('highlight_row');
		
	},function(){
		$(this).removeClass('highlight_row');
	});



	$('#refund_amount_block').dialog({
										width:450,
										height:'auto',
										autoOpen:false,
										modal:true
									});
 
	
	function show_refundbox(transid){
		$('#refund_amount_block').dialog('open');
	}

	$('#refund_amount_form').submit(function(){
		
		var error_msg = 0;
		if(!$('input[name="rf_amount"]').val()){
			$('input[name="rf_amount"]').addClass('invalid_input');
			error_msg += 1;
		}
		 
		if(!$('input[name="rf_track_id"]').val()){
			$('input[name="rf_track_id"]').addClass('invalid_input');
			error_msg += 1;
		}
		if(!$('input[name="rf_datetime"]').val()){
			$('input[name="rf_datetime"]').addClass('invalid_input');
			error_msg += 1;
		}

		$('#rfamount_response').html("").show();
		if(!error_msg){
			 
			show_loading("Updating Please wait...");
			$.post(site_url+'/admin/upd_refundamount',$(this).serialize(),function(resp){
				update_loading(resp.message);
				if(resp.status){
					$('#refund_amount_block').dialog("close");
					setTimeout('reload_page()',2000);
				}
			},'json');
		}else{
			$('#rfamount_response').html("Invalid Inputs found").show();
		}
	 
	return false;
	});

	load_notes();
	function load_notes(){
		var postdata = $('#transnote').serialize();
			show_loading("Loading notes,Please wait...");
			$.post(site_url+'/admin/transnote/show',postdata,function(resp){
				$('#transnotes_feed').html(resp);
				hide_loading();
			});	
	}

	function mark_delivered(invoice_no){
		var postdata = 'invoice_no='+invoice_no;
			show_loading("Updating Invoice Status,Please wait...");
			$.post(site_url+'/admin/upd_invoicestatustodelivered',postdata,function(resp){
				update_loading(resp.message);
				if(resp.status){
					setTimeout('reload_page()',1000);	
				}
			},'json');	
	}

	function remove_note(nid){
		if(confirm("Are you sure you want to delete this note?")){
			var postdata = $('#transnote').serialize()+'&nid='+nid;
			show_loading("Deleting note,Please wait...");
			$.post(site_url+'/admin/transnote/remove',postdata,function(resp){
					load_notes();	
			},'json');
		}
	}
	
	function add_note(){
		var postdata = $('#transnote').serialize();
			show_loading("Adding notes,Please wait...");
			$.post(site_url+'/admin/transnote/add',postdata,function(resp){
			 
					load_notes();	
				 
			},'json');
	}

	$('#upd_shipment_form').submit(function(){
		var error_msg = 0;
			if(!$('input[name="ship_invoice_no[]"]').val()){
				$('input[name="ship_invoice_no[]"]').addClass('invalid_input');
				error_msg += 1;
			}
			if(!$('input[name="dmed"]').val()){
				$('input[name="dmed"]').addClass('invalid_input');
				error_msg += 1;
			}
			if(!$('input[name="track"]').val()){
				$('input[name="track"]').addClass('invalid_input');
				error_msg += 1;
			}
			if(!$('input[name="shipdate"]').val()){
				$('input[name="shipdate"]').addClass('invalid_input');
				error_msg += 1;
			}

			$('#ship_response').html("").show();
			if(!error_msg){
				 
				show_loading("Updating Please wait...");
				$.post(site_url+'/admin/upd_shipment_det',$(this).serialize(),function(resp){

					update_loading(resp.message);

					 
					if(resp.status){
						$('#update_order_shipment').dialog("close");
						 
						setTimeout('reload_page()',2000);
					}
				},'json');
			}else{
				$('#ship_response').html("Invalid Inputs found").show();
			}
		 
		return false;
	});

	$('#update_order_shipment').dialog({
											width:450,
											height:'auto',
											autoOpen:false,
											modal:true,
											open:function(){
												var inv_no = $(this).data('inv_no');
												var dlg = $(this);

													$('input[name="ship_invoice_no[]"]').val(inv_no);
												
													$.post(site_url+'/admin/get_invoice_shipdet','inv_no='+inv_no,function(resp){
														if(resp.status){
															inv_det = resp.inv_det;

															$('input[name="ship_invoice_no[]"]').val(inv_det.invoice_no).attr('readonly',true);
															$('input[name="dmed"]').val(inv_det.delivery_medium);
															$('input[name="track"]').val(inv_det.tracking_id);
															$('input[name="shipdate"]').val(inv_det.shipdatetime);
															$('input[name="notify_customer"]').attr('checked',inv_det.notify_custmer);

															if(inv_det.invoice_status == 2){
																$('input[name="update_shipment"]',dlg).hide();
																$('#ship_response').html("<b>Invoice already Cancelled</b>").show();
																$('.ship_inputs,',dlg).attr('disabled',true);																
															}else if(inv_det.tracking_id == 0){
																$('input[name="update_shipment"]',dlg).show();
																$('#ship_response').html("").hide();
																$('.ship_inputs,',dlg).attr('disabled',false);
															}else{	 
																$('.ship_inputs,',dlg).attr('disabled',false);
															 	$('input[name="track"],',dlg).attr('disabled',false);
															 	$('input[name="action"]',dlg).val('tracking_id'); 
																$('input[name="update_shipment"]',dlg).show();
																$('#ship_response').html("<b>Shipment details already updated</b>").show();
															}
														}else{
															$('input[name="action"]',dlg).val('shipment');
															//$('#ship_response').html("<b>Shipment details not updated</b>").show();
														}

														
													},'json');																								
											}
										});

	function show_loading(msg){
		$('#loading_dlg_action').html(msg);
		$('#loading_dlg').dialog('open');
		$('#loading_dlg_icon').show();
	}

	function hide_loading(){
		$('#loading_dlg_action').html('');
		$('#loading_dlg').dialog('close');
		$('#loading_dlg_icon').hide(); 
	}

	function update_loading(resp){
		$('#loading_dlg_action').html(resp+'<br /><a href="javascript:void(0);hide_loading()" style="color:#cd0000;font-size:12px;margin-top:10px;">Close</a>');
		$('#loading_dlg_icon').hide();
	}
	
	function cancel_invoice(inv_no){


		$('#cancel_invorders').data('inv_no',inv_no).dialog('open');

		/*
		if(confirm("Are You sure you want to cancel invoice :: #"+inv_no)){
			show_loading("Cancelling Invoice , Please wait... ");
			$.post(site_url+'/admin/cancel_invoice','inv_no='+inv_no,function(resp){
				update_loading(resp.message);
				if(resp.status){
					setTimeout('reload_page()',1000);
				}
			},'json');
		}
		*/
	}


	function update_shipment(inv_no){
		$('#update_order_shipment').data('inv_no',inv_no).dialog('open');
	}

	/* 	
		show_loading("Cancelling Selected Orders , Please wait... ");
		setTimeout('update_loading("Selected Orders are Cancelled ")',3000);
		setTimeout('hide_loading()',6000);
		setTimeout('reload_page()',7000);
	 
	*/


	$('#cancel_invorders').dialog({
		autoOpen:false,
		width:380,
		height:'auto',
		modal:true,
		open:function(){
			var inv_no_text = ' Invoice #'+$(this).data('inv_no');
			$('#cnl_invoice_no').text(inv_no_text);
		},
		buttons:{
			'Proceed for Cancellation': function(){
								var dlg = $(this);
								var inv_no = $(this).data('inv_no'); 
								
									cnl_invorders = 0;											
									show_loading("Cancelling Invoice, Please wait... ");
									var notify_cus = 0;
									if($('input[name="invnotify_cus"]').attr('checked')){
										notify_cus = 1;
									}
									if($('input[name="cnl_invorders"]').attr('checked')){
										cnl_invorders = 1;
									}
									
								
									$.post(site_url+'/admin/cancel_invoice','inv_no='+inv_no+'&cancellation_mail_text='+$('textarea[name="invcancellation_mail_text"]').val()+'&notify_cus='+notify_cus+'&cnl_orders='+cnl_invorders,function(resp){
										update_loading(resp.message);
										if(resp.status){
											setTimeout('reload_page()',1000);
										}
									},'json');
								 
				},
				'Close':function(){
					$(this).dialog('close');
					hide_loading();
				}	
		}
	});


	$('#cancel_order').dialog({
								autoOpen:false,
								width:380,
								height:'auto',
								modal:true,
								buttons:{
									'Proceed for Cancellation': function(){
														var dlg = $(this);
														var selected_oids_fromtrans = dlg.data('selected_oids_fromtrans');	
														var total_selected_orders = selected_oids_fromtrans.split(',').length;
														
														if(confirm("Are You sure you want to cancel selected "+total_selected_orders+" order(s)")){															
															show_loading("Checking Orders for Cancellataion, Please wait... ");
															var postdata = 'order_ids='+selected_oids_fromtrans;
															$.post(site_url+'/admin/check_orders_for_cancellation',postdata,function(resp){
																if(!resp.status){
																	hide_loading();
																	show_loading("Cancelling selected "+total_selected_orders+" Orders , Please wait... ");

																	var notify_cus = 0;
																		if($('input[name="notify_cus"]').attr('checked')){
																			notify_cus = 1;
																		}
																	
																	$.post(site_url+'/admin/cancel_order','order_ids='+selected_oids_fromtrans+'&cancellation_mail_text='+$('textarea[name="cancellation_mail_text"]').val()+'&notify_cus='+notify_cus,function(resp){
																		update_loading(resp.message);
																		if(resp.status){
																			setTimeout('reload_page()',1000);
																		}
																	},'json');
																}else{
																	update_loading(resp.message);
																}
															},'json');
														}
										},
										'Close':function(){
											$(this).dialog('close');
											hide_loading();
										}	
								}
							});
	
	function process_order_cancellation(){
		$('#cancel_order').dialog('open');
	}
	
	function cancel_orders(){
		var order_ids_ele = $('input[name="oids[]"]:checked');
		var total_selected_orders = order_ids_ele.length;
		var selected_oids = new Array();
		
			order_ids_ele.each(function(i,item){
				selected_oids.push($(item).val()); 
			});
		var	selected_oids_fromtrans = selected_oids.join(','); 
			
			if(!total_selected_orders){
				alert("Please choose atleast one order to cancel");
			}else{
					show_loading("Checking Orders for Cancellataion, Please wait... ");
					var postdata = 'order_ids='+selected_oids_fromtrans;
						$.post(site_url+'/admin/check_orders_for_cancellation',postdata,function(resp){
							if(!resp.status){
								$('#cancel_order').data('selected_oids_fromtrans',selected_oids_fromtrans).dialog('open');
							}else{
								update_loading(resp.message);
							}
						},'json');
			}
			
	}

	function reload_page(){
		location.href = '<?php echo current_url();?>';  
	}

	function check_if_fsavail(){
		var totalfs = $('.fs_sels').length;
		var totalfs_chked = $('.fs_sels:checked').length;

		if(totalfs==totalfs_chked){
			return false;
		}else{
			return true;
		}
	}
	
$(function(){

	$('#loading_dlg').dialog({width:400,height:100,modal:true,autoOpen:false});
	


	$('input[name="sel_all_orders"]').click(function(){
		if($(this).attr('checked')){
			$('input[name="oids[]"]').attr('checked',true);
			$('.table_order_list tr').addClass('selected_row');
		}else{
			$('input[name="oids[]"]').attr('checked',false);
			$('.table_order_list tr').removeClass('selected_row');
		}
	});

	$('input[name="oids[]"]').click(function(){
		if($(this).attr('checked')){
			$($(this).parent().parent()).addClass('selected_row');
		}else{
			$($(this).parent().parent()).removeClass('selected_row');
		}
	});

	
	$(".trans_oids").attr("checked",false);
	$("#shipdate").datepicker({showOn: 'both', dateFormat: 'yy-mm-dd' , buttonImage: '<?=base_url()?>images/calendar_old.png', buttonImageOnly: true});
	$("#rf_datetime").datepicker({showOn: 'both', dateFormat: 'yy-mm-dd' , buttonImage: '<?=base_url()?>images/calendar_old.png', buttonImageOnly: true});

	$("#proc_t_formtrig").click(function(){
		if($(".trans_oids:checked").length==0)
		{
			alert("Select the orders which are ready for processing");
			return false;
		}
		oids=[];
		var nostock = 0 ;
		$(".trans_oids:checked").each(function(){
		 
			if(!($(this).attr('stock_avial')*1) ) {
				nostock = 1;
			}
			
			oids.push($(this).val());
			$("#trans_oids_f").val(oids.join(","));
		});

		if(nostock){
			alert("Insufficient Stock for some of the products");
		}else{

			/*	
			if(check_if_fsavail()){
				if(confirm("There are freesamples do you want to add free samples to invoice")){
					$('.fs_sels').attr('checked',true);

					var fs_linkids = new Array();
						$('.fs_sels:checked').each(function(){
							fs_linkids.push($(this).val());
						});
					$('input[name="fs_linkids"]').val(fs_linkids.join(','));
					
					$("#proc_t_form").submit();
				}else{
					$('input[name="fs_linkids"]').val('');
					$("#proc_t_form").submit();
				}
			}else{
				$('input[name="fs_linkids"]').val('');
				$("#proc_t_form").submit();	
			}
			*/
			$('input[name="fs_linkids"]').val('');
			if($('.fs_sels').length){
				var fs_linkids = new Array();
					$('.fs_sels:checked').each(function(){
						fs_linkids.push($(this).val());
					});
				$('input[name="fs_linkids"]').val(fs_linkids.join(','));
			}
			$("#proc_t_form").submit();
			
		}
		
		return false;
	});
	

	$("#shipformtrig").submit(function(){
		if($(".trans_oids:checked").length==0)
		{
			alert("Select the orders which are ready for shipping");
			return false;
		}
		oids=[];
		$(".trans_oids:checked").each(function(){
			oids.push($(this).val());
			$("#trans_oids_fs").val(oids.join(","));
		});
		$("#shipf").submit();
		return false;
	});

	$("#shipformwtrig").submit(function(){
		if($(".trans_oids:checked").length==0)
		{
			alert("Select the orders which are ready for shipping");
			return false;
		}
		oids=[];
		$(".trans_oids:checked").each(function(){
			oids.push($(this).val());
			$("#trans_oids_fsw").val(oids.join(","));
		});
		$("#shipwf").submit();
		return false;
	});
});
</script>