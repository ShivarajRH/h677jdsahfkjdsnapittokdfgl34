<?php
/**
 * @author Shivaraj <shivaraj@storeking.in>
 */?>
<style>

</style>
<div class="container_block">
	<style>
		* { font-family: arial; }
		table {
			border-collapse: collapse;
		}
		.container_block {
			font-size: 12px;
			font-family: arial;
			text-align: left;
		}
		.page_wrap {
			margin: 5px;
			font-size: 12px;
		}
		.page_wrap .page_topbar {
			clear: both;
			display: inline-block;
			margin: 10px 0 5px;
			width: 100%;
		}
		.stat_block {
			/*text-align: center;*/
		/*	font-size: 16px;
			padding: 5px 15px 2px 15px;
			line-height: 20px;
			text-align: center;
			width: 12%;*/
			/*float:left;*/
		}
		.stat_block td {
			font-size: 11px;
			/*display: block;*/
		}
		.tdhd { text-align: right; padding-right: 10px;}
		#prod_linked .imeis_block { padding: 1px 0; }
		#prod_linked .imeis_block .imei_itm { margin-left: 5%; font-size: 11px; }
		
/*		.hide_print { display:none;}*/
		@media print
		{
			* { font-family: arial; }
			h2 { font-size: 15px; }
			table {
				border-collapse: collapse;
			}
			.container_block {
				font-size: 12px;
				font-family: arial;
				text-align: left;
			}
			.hide_print { display:none; }
			.tdhd { text-align: right; padding-right: 10px;}
			#prod_linked .imeis_block { padding: 1px 0; }
			#prod_linked .imeis_block .imei_itm { margin-left: 5%; font-size: 11px; }
		
		}
	</style>
	
	
	
	<!--<h2>Stock Transfer Summary</h2>-->
	<div align="right" class="hide_print">
		<button onclick="return print_block();" class="button button-primary button-tiny button-rounded ">Print</button>
		<button onclick="return print_block_imei();" class="button button-primary button-tiny button-rounded ">Print IMEIs</button>
	</div>
	<?php
	$sts_det=$this->partner->get_transfer_status($transfer_det['transfer_status']);
	?>
	
	

	
	
	<div class="page_topbar" align="left">
		
		
			<h2 class="page_title">
				Stock Transfer Summary #<?=$transfer_det['transfer_id']?>
			</h2>
<?php
			if( $transfer_det['transfer_status'] == 1 )
			{
?>
				<!--<a class="button button-primary button-tiny button-rounded fl_right" style="" href="<?=site_url();?>/admin/partner_generate_picklist/<?=$transfer_det['transfer_id']?>" target="_blank">Generate Picklist</a>-->
<?php
			}
?>
			<div class="clear" >&nbsp;</div>
		
			<table width="100%" border="1" cellspacing="0" cellpadding="5">
					<tr class="stat_block color_green">
							<td class="tdhd">Transfer Id</td> 
							<td>#<?=$transfer_det['transfer_id']?></td> 
					</tr>
					<tr class="stat_block color_red">
						<td class="tdhd">Partner</td> 
						<td><?=$transfer_det['partner_name']?></td>
					</tr>
					<tr class="stat_block color_red">
						<td class="tdhd">Transfer</td> 
						<td><?php
							if($transfer_det['transfer_option']==1)
								echo 'To Partner';
							else
								echo 'Return From Partner';
							
							?></td>
					</tr>
					<tr class="stat_block color_grey">
						<td class="tdhd">Partner Transfer No</td> 
						<td><?=$transfer_det['partner_transfer_no'];?></td>
					</tr>

					<tr class="stat_block color_orange">
						<td class="tdhd">Scheduled Transfer Date</td> 
						<td><?=format_datetime($transfer_det['scheduled_transfer_date'])?></td>
					</tr>

					<tr class="stat_block color_gray">
						<td class="tdhd">Transfer Remarks</td>
						<td><div><?php echo ''.$transfer_det['transfer_remarks'];?></div></td>
					</tr>
					
					<tr class="stat_block color_gray">
						<td class="tdhd">Status</td> 
						<td><?=$sts_det['msg'];?></td>
					</tr>
					<tr class="stat_block color_red">
						<td class="tdhd">Created</td> 
						<td><?php echo 'By: '.ucfirst($transfer_det['username']).' On: '.format_datetime($transfer_det['transfer_date']); ?></td>
					</tr>
<?php
					if(	isset($transfer_det['deals'][0]) )
					{
						$log_det=$transfer_det['deals'][0];
?>
						<tr class="stat_block color_gray">
							<td class="tdhd">Updated</td>
							<td><?php echo 'By: '.ucfirst($log_det['username']). ' On: '.format_datetime($log_det['modified_on']);?></td>
						</tr>
						<tr class="stat_block color_gray">
							<td class="tdhd">Batched</td>
							<td><?php echo 'By: '.ucfirst($log_det['batched_by']).' On: '.format_datetime($log_det['batched_on']);?></td>
						</tr>

						<tr class="stat_block color_gray">
							<td class="tdhd">Packed</td>
							<td><div><?php echo 'By: '.ucfirst($log_det['packed_by'])." On: ".format_datetime($log_det['packed_on']);?></div></td>
						</tr>
<?php
					}
?>
			</table>
			
		
	</div>
	
	<h3>Deal Details</h3>
	
	<!-- #Product Linked Block start -->
	<div id="prod_linked" class="">


		<table width="100%">
			<tr>
				 <td width="99%">

					 <!--<h4>Linked Items</h4>-->
					<?php 
					if(count($transfer_det['deals'])>0)
					{
					 ?>
					<table class="datagrid smallheader " width="100%" border="1" cellpadding="5" cellspacing="0">
						<thead>
							<tr class="stat_block">
								<td>#</td>
								<td>Deal Name</td>
								<td>Req. Qty</td>
								<td>Batch Qty</td>
								<td>Scan Qty</td>
								<td>Status</td>

							</tr>
						</thead>
						<tbody>
						<?php $no_cols=15;
							
							foreach($transfer_det['deals'] as $i=>$log_det)
							{
						?>
							<tr class="stat_block">
								<td><?php echo $i+1;?></td>
								<td><?php echo $log_det['itemid'];?> - <?php echo $log_det['deal_name'];?></td>
								<td><?php echo $log_det['item_transfer_qty'];?></td>
								<td><?php echo (int)($log_det['batch_qty']/$log_det['product_transfer_qty']);?></td>
								<td><?php echo $scanqty=(int)($log_det['scanned_qty']/$log_det['product_transfer_qty']);?></td>
								<td><?php $sts_det=$this->partner->get_transfer_ord_status($log_det['transfer_status']);
										echo $sts_det['msg'];
								?></td>
								
							</tr>
							<?php
							if($log_det['is_serial_required']==1)
							{
							?>
							<tr class="hide_print">
								<td colspan="<?=$no_cols;?>">
									
										
<?php
										$transfer_id=$transfer_det['transfer_id'];
										$prd_id=$log_det['product_id'];
										$stock_loc_list_res=$this->db->query("SELECT rstk.itemid,rstk.stock_info_id,stk.rack_bin_id,stk.location_id
														,stk.product_barcode,stk.mrp,rstk.qty,CONCAT(rb.rack_name,'-',rb.bin_name) AS rbname,di.name as deal,p.product_name,rstk.tp_id
														
													 FROM t_partner_reserved_batch_stock rstk
													 JOIN t_stock_info stk ON stk.stock_id=rstk.stock_info_id
													 JOIN m_rack_bin_info rb ON rb.id = stk.rack_bin_id
													 JOIN m_product_info p ON p.product_id=rstk.product_id
													 JOIN king_dealitems di ON di.id=rstk.itemid
													WHERE rstk.transfer_id=? AND rstk.itemid=? AND rstk.product_id=? GROUP BY rstk.id ",array($transfer_id,$log_det['itemid'],$prd_id) );
											$stock_loc_list=$stock_loc_list_res->result_array();//,GROUP_CONCAT(imlog.imei_no) AS imei_nos JOIN t_imei_update_log imlog ON imlog.product_id=rstk.product_id AND imlog.stock_id=rstk.stock_info_id
//											echo '<pre>'.$this->db->last_query();

?>
										<div class="imeis_block hide_print">
<?php
				
											$imei_list=array();
											foreach($stock_loc_list as $stock_loc)
											{
												$tp_id=$stock_loc['tp_id'];
												$imei_list_res=$this->db->query("SELECT group_concat(imei.imei_no) as imei_nos
																		FROM t_imei_update_log imei 
																WHERE imei.transfer_prod_link_id=? AND imei.transfer_prod_link_id!=0

																",$tp_id);//GROUP BY a.stock_id,b.product_barcode,b.location_id,b.rack_bin_id

												if($imei_list_res->num_rows())
												{
													$imei_list_ar = $imei_list_res->row_array();
													$imei_no_ar=explode(',',$imei_list_ar['imei_nos']);
													foreach ($imei_no_ar as $imei_no) {
														if($imei_no!='')
															$imei_list[$imei_no]=$imei_no;
													}
												}
											}

											if($imei_list) 
											{
												$imei_list=array_unique($imei_list);
	//											echo '<pre>';print_r($imei_list);
												foreach ($imei_list as $imei_no=>$i)
												{

	?>
														<div class="imei_itm">IMEI: <?=$imei_no;?></div>

	<?php   

												}
											}
											else {
												echo '<div class="imei_itm">No IMEIs found.</div>';
											}
?>
										</div>
										<!--<h5 style="margin:0;">Products Details</h5>-->
										<?php /* <table align="center" border="0" width="50%" cellpadding="5" cellspacing="0">
											<thead>
											<tr>
<!--												<th>Product</th>
												<th>MRP</th>
												<th>Location</th>-->
												<!--<th>IMEI</th>-->
												<!--<th>qty</th>-->
											</tr>
											</thead>
											<tbody>
<?php
												foreach($stock_loc_list as $stock_loc) {
													
													
													$stk_id=$stock_loc['stock_info_id'];
													$mrp=$stock_loc['mrp'];
													$imei_no=$stock_loc['imei_no'];
//													$loc_id=$stock_loc['location_id'];
//													$rackbinid=$stock_loc['rack_bin_id'];
													if($imei_no!=''){
?>
														<tr>
															<!--<td><?=$stock_loc['product_name']?></td>-->
															<!--<td><?=$mrp?></td>-->
															<!--<td><?=$stock_loc['rbname']?></td>-->
															<td>IMEI: <?=$imei_no?></td>
															<!--<td><?=$stock_loc['qty']?></td>-->

														</tr>
<?php
													}
												}
?>
												
											</tbody>
											
										</table>*/?>
									
								</td>
							</tr>
							 
							
							
							<?php } ?>
							
						<?php } ?>
						</tbody>
					</table>
					<?php } 
						else {
					?>
						<table class="datagrid smallheader noprint" width="88%">
							<tbody>
								<tr class="stat_block"><td width="100%" style="margin:10px;font-weight: bold;">No Data</td></tr>
							</tbody>
						</table><?php } ?>
				</td>
			</tr>
		</table>
		
		

	</div>
	<!-- #Product Linked Block End -->

	
	
	
	
</div>

<!--<h3 align="center">Stock Transfer & Packed Summary</h3><p align="center">---In Progress!---</p>-->
<script>
	function print_block()
	{
		$(".container_block").printElement({
			printMode:"popup"
			,pageTitle:"Stock Transfer Summary"
			,leaveOpen:false
			/*,printBodyOptions: { styleToAdd:'padding:10px;margin:10px;color:#FFFFFF !important;',classNameToAdd : 'wrapper2'}*/
		});
		return false;
	}
	function print_block_imei()
	{
		$(".imeis_block").closest('tr').removeClass('hide_print');
		$(".imeis_block").removeClass('hide_print');
		$(".container_block").printElement({
			printMode:"popup"
			,pageTitle:"Stock Transfer Summary"
			,leaveOpen:false
			/*,printBodyOptions: { styleToAdd:'padding:10px;margin:10px;color:#FFFFFF !important;',classNameToAdd : 'wrapper2'}*/
		});
		$(".imeis_block").closest('tr').addClass('imeis_block hide_print');
		$(".imeis_block").addClass('imeis_block hide_print');
		return false;
	}
</script>
<p></p>