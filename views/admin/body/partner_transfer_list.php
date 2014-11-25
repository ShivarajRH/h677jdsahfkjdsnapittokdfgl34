<?php
/**
 * @author Shivaraj <shivaraj@storeking.in>_Sep_10_2014
 */
?>
<style>
	.subdatagrid th { padding: 4px 0 2px 4px !important;font-size: 11px !important;color: #130C09;background-color: #AEACC2; }
	.content_block .resp_status_msg { padding:5px; color: chocolate; }
	.qvk_imgwrap {
		float: left;
		margin-left: 10px;
		position: relative;
		width: 150px;
	}
	
	
	/** FILTER STYLE **/
	.level1_filters {
		float: left;
		margin-top: 5px;
		width: 100%;
	}
	fieldset {
		padding: 0 0px 0 12px !important;
		background-color: #F1F0F5;
		border-color: #E6E6E6;
	}
	.close_filters {
		display: table;
		width: 100%;
		float: right;
		cursor: pointer;
		background-color: #DFE0F0;
		padding: 2px 7px 2px 6px;
	}
	.close_btn {
		float: right;
		margin-right: 8px;
		cursor: pointer;
		color: #D50C0C;
		font-weight: bold;
		font-size: 11px;
	}
	h3.filter_heading {
		margin-bottom: 0px;
		margin-top: 0;
		width: 788px;
	}
	.filters_block {
		float: left;
		padding-bottom: 5px;
		width: 99%;
		display: block;
		display: none;
	}
	.filters_block .date_filter {
		float: right;
		margin-top: 10px;
	}
	.limit_display_block {
		float: left;
		margin-right: 00px;
		margin-top: 15px;
	}
	.button {margin: 3px; cursor: pointer; }
	.button_fit {padding: 4px; color: #000000;color: #5160D1;}
	/** FILTER STYLE **/
</style>
<div class="container">
	<!--<span  style="float: right;margin:10px;"><a	href="<?php echo site_url('admin/list_employee')?>" class="fl_right">List Employee</a></span>-->
	<h2 style="float: left;margin: 0px;width: 40%;">Stock Transfer List</h2>
	<div class="fl_right" style="">
		<a class="button button-action button-rounded button-tiny" href="<?php echo site_url('admin/stk_partner_select');?>" target="_blank" style="">Create Stock Transfer</a>
	</div>
	<!--Filter code starts-->
	<div class="level1_filters">
		
		<fieldset>
			<form onsubmit="return fn_submit_filters(this);">
				<span title="Toggle Filter Block" class="close_filters"><span class="close_btn">Show</span>
					<h3 class="filter_heading">Filters:</h3>
				</span>
				<div class="filters_block">
						<!--<div class="fl_right"><a class="reset_all button button-rounded button-tiny button-caution" onclick="javascript:btn_fn_reset_filters();">Reset</a></div>-->
						<form action="" name="form_filters" id="form_filters">
							<table width="100%" cellpadding="5" border="0">
								<tr>
									<td width="60%">
										<table width="100%" cellpadding="5" cellspacing="0">
											<tr>
												<th align="right">Transfer Options:</th>
												<td><select id="sel_transfer_option" name="sel_transfer_option" style="width: 104px;">
														<option value="00" <?=$transfer_option=='0'?' selected':'';?>>All</option>
														<option value="1" <?=$transfer_option=='1'?' selected':'';?>>To Partner</option>
														<option value="2" <?=$transfer_option=='2'?' selected':'';?>>From Partner</option>
													</select>
												</td>
											</tr>
											<tr>
												<th align="right">Batch Status:</th>
												<td><select id="sel_batch_group_type" name="sel_batch_group_type" style="width: 104px;">
														<option value="0" <?=$trasfer_status=='0'?' selected':'';?>>All</option>
														<option value="1" <?=$trasfer_status=='1'?' selected':'';?>>Pending</option>
														<option value="2" <?=$trasfer_status=='2'?' selected':'';?>>Reserved</option>
														<option value="3" <?=$trasfer_status=='3'?' selected':'';?>>Packed</option>
														<option value="4" <?=$trasfer_status=='4'?' selected':'';?>>Canceled</option>
													</select>
												</td>
											</tr>
											<tr>
												<th align="right">
													Show transfers :
												</th>
												<td>
													<label for="date_from">From :</label><input type="text" id="date_from" name="date_from" value="<?=$date_from;?>">
													<br>
													<label for="date_to" style="margin-right:16px;">To :</label><input type="text" id="date_to" name="date_to" value="<?=$date_to;?>"> 
												</td>
											</tr>
											<tr>
												<th align="right">Show</th>
												<td>
														<select name="limit_filter" id="limit_filter">
															<option value="10" selected="">10</option>
															<option value="20" selected="">20</option>
															<option value="50">50</option>
															<option value="100">100</option>
														</select>
													items per page.
												</td>
											</tr>
										</table>
									</td>
									<td width="40%">
<!--										<table></table>-->
									</td>
								</tr>
								<tr>
									<td align="center">
											<input type="reset" value="Reset" class="button button-primary button-rounded">
											<input type="submit" value="Submit" class="button button-royal button-rounded">
									</td>
									<td></td>
								</tr>
							</table>
							
						
						
				</div>
				<!--<input type="hidden" name="pg_num" class="page_num" value="0" size="3">-->
			</form>
		</fieldset>
	</div><!--Filter code ends-->
	
	
	
	
	<div class="clear">&nbsp;</div>
	
	<?php
	if(!isset($p_ord_list))
	{ ?>
		<p align="center">No records found</p>
<?php
	}
	else
	{
?>
	<div class="block" width="100%">
		<div class="pagination fl_right">
			<?php echo ''.$pagination; ?>
		</div>
		<div class="log fl_left " style="padding:5px;">
			<span class="block_notify" style="font-size: 14px; background-color: #7db500 !important;"><?php echo $pg_count_msg; ?></span>
		</div>
		
		<button class="button button-primary button-tiny button-rounded fl_right" style="" onclick="return fn_generate_picklist();">Generate Picklist</button>
		
	</div>
	<div class="clear"></div>
	<form name="stk_transfer_form" class="stk_transfer_form" enctype="multipart/form-data" method="post" action="" onsubmit="return fn_submit_partner_details(this);">
		<?php // echo '<pre>';print_r($p_ord_list); ?>
		<table class="datagrid" width="100%">
			<thead>
				<th width="2%">#</th>
				<th width="6%">CreatedOn</th>
				<th width="5%"><small>Partner Transfer ID</small></th>
				<th width="5%">Transfer</th>
				<th width="5%">TransferID</th>
				<th width="7%">Partner</th>
				<th>Deals</th>
				<th width="12%">Remarks</th>
				<th width="7%"><small>Exp.Transfer Date</small></th>
				<th width="2%">Status</th>
				<th width="2%"><small>Select Pickslip</small><br><input type="checkbox" name="sel_allpickslip" class="sel_allpickslip" value="" onclick="return fn_chkall_trans(this)"/></th>
				<th width="7%">Actions</th>
			</thead>
			<tbody>
			<?php
			foreach($p_ord_list as $i=>$p_ord)
			{?>
			<tr transfer_id="<?=$p_ord['transfer_id'];?>" partner_id="<?=$p_ord['transfer_id'];?>">
				<td><?=(++$i+$pg);?></td>
				<td>
					<?=format_datetime($p_ord['transfer_date']);?>
					<div><br>Created By:<?= ucfirst($p_ord['username']);?></div>
				</td>
				<td><?=$p_ord['partner_transfer_no'];?></td>
				<td>
						<b><?php if($p_ord['transfer_option']==1)
								echo 'Transfer to Partner';
							else
								echo 'Partner Return';
							?>
						</b>
				</td>
				<td><a href="<?=site_url("admin/partner_transfer_view/".$p_ord['transfer_id']);?>" class="block_notify" style="background-color:#738173;" target="_blank"><?=$p_ord['transfer_id'];?></a></td>
				<td><?=$p_ord['partner_name'];?></td>
				<td><?php
					$def_limit=4;
					if(!empty($p_ord['deals'] )){ ?>
					<table class="subdatagrid" width="100%">
						<thead>
							<tr>
								<th width="1%">#</th>
								<th width="5%">ItemId</th>
								<th width="">Item Name</th>
								<th width="9%"><small>Qty</small></th>
								<th width="10%">Status</th>
							</tr>
						</thead>
						<tbody>
							<div class="fl_left block_notify" style="background-color:#D8B8B8;">Total Linked Deals: <b><?=count($p_ord['deals']);?></b></div>
							<?php
							$cols=5;
							
							
								foreach($p_ord['deals'] as $j=>$prd) {
									if($j>=$def_limit)
										break;
								?>
									<tr>
										<td><?=(++$j);?></td>
										<td><?=$prd['itemid'];?></td>
										<td><a href="<?=site_url('admin/deal/'.$prd['itemid']);?>" target="_blank"><?=$prd['deal_name'];?></a></td>
										<td><?=$prd['item_transfer_qty'];?></td>
										<td><?php 
										
										$sts_det=$this->partner->get_transfer_ord_status($prd['transfer_status']);
										echo $sts_det['msg'];
										
												?>
										</td>
									</tr>
								<?php } ?>

						</tbody>
					</table>
						<?php
								
							if(count($p_ord['deals']) > $def_limit)
							{ ?>
								<div class="fl_right" style="padding:5px;">
									<a href="<?=site_url("admin/partner_transfer_view/".$p_ord['transfer_id']);?>" class="button button-rounded button-tiny button-flat-secondary" target="_blank">View More</a>
								</div>
						<?php }	?>
					
			<?php }	?>
				</td>
				<td><?=$p_ord['transfer_remarks'];?></td>
				<td><?=format_datetime($p_ord['scheduled_transfer_date']);?></td>
				<td>
					<?php 
					$sts_det=$this->partner->get_transfer_status($p_ord['transfer_status']);
					echo $sts_det['msg'];
					?>
				</td>
				<td>
<?php
					if( $p_ord['transfer_status'] == 1 )
					{
?>
					<input type="checkbox" name="sel_pickslip[<?=$p_ord['transfer_id'];?>][]" class="sel_picklist" value="" transfer_id="<?=$p_ord['transfer_id'];?>"/>
<?php
					}
?>
				</td>
				<td>
					<!--<a href="" class="button button-action button-tiny" onclick="return fn_create_batch(this);" >Create Batch</a>-->
					<a href="<?=site_url("admin/partner_transfer_view/".$p_ord['transfer_id']);?>" class="button button-rounded button-tiny button-primary" target="_blank">View List</a>
					<?php
					if($p_ord['transfer_status']==0)
					{
						if($p_ord['is_active']==0) { ?>
							<a class="button button-tiny button-rounded button-caution">Cancelled</a>
						<?php } else { ?>
							<a class="button button-tiny button-rounded button-caution button_fit" onclick="return fn_cancel_transfer(this)">Cancel Transfer</a>
						<?php }
					}
					?>
				</td>
			</tr>
			<?php
			}
			?>
			</tbody>
		</table>
		
	</form>
	
<?php
	}
?>
</div>

<div style="display:none;"><!--dialog box content-->
	<div class="picklist_block">
		<form name="picklist_form" class="picklist_form" action="<?=site_url();?>/admin/partner_generate_picklist" method="post" target="_blank">
			<input type="hidden" name="transferids" value="" class="transferids">
		</form>
	</div>
</div>

<script>
	var filter=<?=$filter;?>;
	function fn_generate_picklist()
	{
		var transfer_id_arr=[];
		var transfer_id_str='';
		$.each($(".sel_picklist"),function(i,elt) {
			if($(elt).is(":checked"))
			{
				transfer_id=$(elt).attr('transfer_id');
				transfer_id_arr.push(transfer_id);
				
			}
			
		});
		
		if(transfer_id_arr.length > 0)
		{
			transfer_id_str=(transfer_id_arr).join(',');
			$('.transferids').val(transfer_id_str);
			$(".picklist_form").submit();
		}
		else
		{
			alert("Not selected any transfers");
		}
		return false;
	}
	
	//===========filter box show/hide================
	if(filter==1) // if filters applied show filter box
	{
		$(".close_filters .close_btn").html("Hide");
		$(".filters_block").slideDown();
	}
	$(".close_filters").toggle(function() {
		$(".close_filters .close_btn").html("Hide");
		$(".filters_block").slideDown();
	//    $(".level1_filters").animate({"width":"100%"});
	},function() {
		$(".filters_block").slideUp();
		$(".close_filters .close_btn").html("Show");
	});
	//===========filter box show/hide================
	
	function fn_chkall_trans(elt)
	{
		if( $(elt).is(":checked") )
			$(".sel_picklist").attr("checked",true);
		else
			$(".sel_picklist").attr("checked",false);
	}
	// ========== Form submit =================
	function fn_submit_filters(elt) 
	{
		var transfer_option=$("#sel_transfer_option").val();
		var batch_sts=$("#sel_batch_group_type").val();
		var dt_frm=encodeURIComponent($("#date_from").val());
		var dt_to=encodeURIComponent($("#date_to").val());
		var limit=$("#limit_filter").val();
		
			dt_frm = dt_frm?dt_frm:'0';
			dt_to = dt_frm?dt_to:'0';
		
		
		var url=site_url+"/admin/partner_transfer_list/"+batch_sts+'/'+transfer_option+'/'+dt_frm+'/'+dt_to+'/'+limit;
		
		window.location.href=url;
		
		return false;
	}
	// ========== EndForm submit code =================
	$(document).ready(function() {
		prepare_daterange("date_from","date_to");
	});
	
	function erp_datepicker(elt1,elt2)
	{
			// Date Picker
			elt1.datepicker({changeMonth:true,changeYear:true
				,dateFormat:'dd-M-yy'//,timeFormat: 'HH:mm'
				,onClose: function( selectedDate ) {
					elt2.datepicker( "option", "minDate", selectedDate );
				}
			});
			elt2.datepicker({changeMonth:true,changeYear:true
				,dateFormat:'dd-M-yy'//,timeFormat: 'HH:mm'
				,onClose: function( selectedDate ) {
					elt1.datepicker( "option", "maxDate", selectedDate );
				}
			});
	}
	function fn_cancel_transfer(elt)
	{
		if(confirm("Are you sure you want to cancel this stock transfer?"))
		{
			var trElt=$(elt).closest('tr');
			var transfer_id=trElt.attr('transfer_id');
			$.post(site_url+"/admin/cancel_transfer",{transfer_id:transfer_id},function(resp) {
				if(resp.status=='success')
				{
					alert(resp.message);

					window.location.href=$(location).attr('href');
				}
				else
				{
					alert("Error: "+resp.message);
				}
			},'json');
		}
	}
</script>
