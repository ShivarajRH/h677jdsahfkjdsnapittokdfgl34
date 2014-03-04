<?php 

	$return_request_cond = $this->config->item('return_request_cond');
	$condition_type_arr = $this->config->item('return_cond');
	$return_process_cond = array();
	$return_process_cond[0] = 'Pending';
	$return_process_cond[1] = 'Out for Service';
	$return_process_cond[2] = 'Move to Warehouse Stock';
	$return_process_cond[3] = 'Ready to ship';
	
	$return_process_cond_str = $return_process_cond;
	
	$return_process_cond_str[4] = 'Return From Service';
	
?>
<div class="page_wrap container">
	
	<div class="page_topbar" >
		<h2 class="page_title fl_left">PNH Return Details</h2>	
		
		<div class="page_action_buttons fl_right" align="right">
			<a href="<?php echo site_url('admin/add_pnh_invoice_return') ?>">Add Return</a>
			&nbsp;
			&nbsp;
			<a href="<?php echo site_url('admin/pnh_invoice_returns') ?>">List All Returns</a>
		</div>
		
		<br />
		<div class=" " style="clear:both">
			 <table cellpadding="10" cellspacing="0"   border=1 style="border-collapse: collapse;border-color:#ccc;text-align: center">
				<tr>
					<td width="100"><b>Return ID</b> <br> <?php echo $return_det['det']['return_id'] ?></td>
					<td width="140"><b>Returned On</b> <?php echo format_datetime($return_det['det']['returned_on']) ?></td>
					<td width="200"><b>Transaction / Ticket no</b> <br/><a target="_blank" href="<?php echo site_url('admin/trans/'.$return_det['det']['transid']);?>"><?php echo $return_det['det']['transid']; ?></a></td>
					<td width="100"><b>InvoiceNo</b> <br/><a target="_blank" href="<?php echo site_url('admin/invoice/'.$return_det['det']['invoice_no']);?>"><?php echo $return_det['det']['invoice_no']; ?></a></td>
					<td width="200"><b>Franchise Name</b> <br/><a target="_blank" href="<?php echo site_url('admin/pnh_franchise/'.$return_det['det']['franchise_id']);?>" ><?php echo $return_det['det']['franchise_name'];?></a></td>
					<td width="100"><b>Handled By</b> <?php echo $return_det['det']['handled_by_name'] ?></td>
					<td width="100">
							<b style="font-size: 18px;top: 5px;position: relative"><?php echo $return_request_cond[$return_det['det']['status']] ?></b>
					</td>
				</tr>
			</table>
		</div>
		
	</div>
	<div style="clear:both">&nbsp;</div>
	<div class="page_content form_block">
		 
		 
		<table class="datagrid" width="100%">
			<thead>
				<tr>
					<th width="30">Slno</th>
					<th width="100">OrderID</th>
					<th width="150">ProductDetails</th>
					<th width="150">Return Reason/Condition </th>
					<th width="150">Status </th>
					<th width="300">Remarks</th>
				</tr>
			</thead>
			<tbody>
				<?php 
					 
					$i=0;
					$gen_return_receipt = 0; 
					foreach($return_det['product_list']  as $prod_det)
					{
						$return_prod_allow = 0;
				?>
					<tr>
						<td width="30"><?php echo $i+1; ?></td>
						<td width="100"><?php echo $prod_det['order_id'] ?></td>
						<td width="150">
							<?php echo anchor_popup('admin/product/'.$prod_det['product_id'],$prod_det['product_name']); ?> <br /><br />
							Barcode : <?php echo $prod_det['barcode'] ?><br />
							<?php 
								if($prod_det['imei_no'])
								{
							?>
								IMEI NO : <?php echo $prod_det['imei_no'] ?> 
							<?php									
								}
							?>	
						</td>
						<td width="150">
							<?php echo $condition_type_arr[$prod_det['condition_type']] ?>
							
							<?php
								if($prod_det['status']==3 && $prod_det['readytoship'])
								{
							?>
									<div><?php echo '<b>Packed</b> : '.(($prod_det['is_packed'])?'Yes':'No '); ?></div>
									<div><?php echo '<b>Shipped</b> : '.(($prod_det['is_shipped'])?'Yes':'No'); ?></div>
							<?php
									if($prod_det['is_packed'] == 1 && $prod_det['is_shipped'] == 0)
									{
										
									}	
									
									echo '<a target="_blank" href="'.site_url('admin/print_return_receipt/'. $return_det['det']['invoice_no'].'/'.$prod_det['return_product_id']).'">Generate Return Receipt</a>';		
								}
							?>
							
							<div><?php echo (($prod_det['status']==2)?('<b>Moved to Stock :</b> '.($prod_det['is_stocked']?'Yes':'No')):'') ?></div>
							<div><?php echo (($prod_det['status']==2)?('<b>Refund Processed :</b> '.($prod_det['is_refunded']?'Yes':'No')):'') ?></div>
						</td>
						<td width="150" >
							<form action="<?php echo site_url('admin/jx_upd_invretprodremark') ?>" class="remark_add_frm" method="post">
								
								<?php 
									$allow_stat_upd = 1;
									if($prod_det['status'] == 2)
									{
										echo '<b>Moved to Stock</b>';
										$allow_stat_upd = 0;
									}else if($prod_det['status'] == 3 )
									{
										if($prod_det['is_shipped'] == 1)
										{
											echo '<b>Shipped</b>';	
											$allow_stat_upd = 0;
										}else if($prod_det['readytoship'] == 1){
											$allow_stat_upd = 0;
											echo '<b>Ready to Ship</b>';	
											$gen_return_receipt = 1;
										}
									}
									if($allow_stat_upd) 
									{
								?>
										<select pstatus="<?php echo $prod_det['status']; ?>" class="sel_prod_status" name="return_prod_status">
								<?php
										if($prod_det['status'] == 2)
										{
											echo '<option value="2">Moved to Stock</option>';
											$return_prod_allow = 1;
										}elseif($prod_det['status'] == 1)
										{
											echo '<option value="">Choose</option>';
											echo '<option value="4">Return From Service</option>';
											echo '<option value="5">Part to Part Replacement</option>';
										}elseif($prod_det['status'] == 3)
										{
											echo '<option value="3">Mark as Ready To Ship</option>';
											echo '<option value="2">Move to Stock</option>';
										}else
										{
											foreach ($return_process_cond as $ic=>$cond)
											{ 
												if($prod_det['status'] > $ic)
													continue;
								?>
													<option value="<?php echo $ic; ?>"><?php echo $cond; ?></option>
								<?php 		}
										} 
								?>
										</select>
								<?php		
									} 	
								?>
								
								
								<div class="out_for_service_opts" style="display: none;text-align: left;">
									<table width="100%" cellpadding="0" cellspacing="0" >
										<tr>
											<td style="width: 80px;"><b>Sent To : </b> </td>
											<td><input type="text" style="width: 200px;" name="sent_to" value="" /></td>
										</tr>
										<tr>
											<td><b>Expected On : </b> </td>
											<td><input type="text" size="10" name="expected_on" value="" /></td>
										</tr>
									</table>
								</div>
								<div class="product_replacement_opts" style="display: none;text-align: left;">
									<table width="100%" cellpadding="0" cellspacing="0" >
										<tr>
											<td style="width: 80px;"><b>OLD IMEINO  : </b> </td>
											<td><input type="text" readonly="readonly" style="width: 200px;" name="old_imei_no" value="<?php echo $prod_det['imei_no'];?>" /></td>
										</tr>
										<tr>
											<td style="width: 80px;"><b>New IMEINO  : </b> </td>
											<td><input type="text" style="width: 200px;" name="new_imei_no" value="" /></td>
										</tr>
									</table>
								</div>
								<div class="return_for_service_opts" style="display: none;text-align: left;">
									<b>Service Return On : </b> <input type="text" name="serv_return_handl_on" value="" />
								</div>
								
							 	<div class="capture_status_upd_msg" style="display: none;">
							 		<input type="hidden" name="return_prod_id" value="<?php echo $prod_det['return_product_id']; ?>">
									<textarea name="return_prod_remark" style="width: 90%;height: 50px;" ></textarea>
									<div align="left">
										<input type="submit" value="Update Status">
									</div>	
								</div> 
							</form>
						</td>
						<td width="300" style="padding:0px;">
							<div class="product_remarks_list"> 
							<?php 
								$return_prod_remarks = $this->erpm->get_pnh_invreturnprod_remarks($prod_det['return_product_id']);
								if($return_prod_remarks)
								{
									foreach ($return_prod_remarks as $rp_remarkdet)
									{
										echo '<div class="remark_info">
													<div> Status : '.$return_process_cond_str[$rp_remarkdet['product_status']].' </div>
													<div>'.$rp_remarkdet['remarks'].'</div>
													<div align="left" style="padding:5px 0px;font-size:11px;">
														<span style="float:left">by <b>'.$rp_remarkdet['created_by_name'].'</b></span>
														<span style="float:right">on <b>'.format_datetime($rp_remarkdet['created_on']).'</b></span>
													</div>
											  </div>';
									}
								}
							?>
							</div> 
						</td>
					</tr>	
				<?php 	
						$i++;	
					}
				?>
			</tbody>
		</table>
		
	</div>
	
	<div align="right">
		<?php if($gen_return_receipt){?>
			<br >
			<input type="button" style="display: none" onclick="alert('comming soon...')" value="Generate Return Receipt for All products" >
		<?php }?>
	</div>
	</div>
</div>
<style>
	.leftcont{display: none;}
	.product_remarks_list{margin-top:5px;}
	.product_remarks_list .remark_info{border-bottom:1px solid #cdcdcd;background: #FFF;padding:10px;overflow: hidden;};
	.product_remarks_list .remark_info p{background: #f1f1f1 !important;margin:2px 0px !important;}
</style>

<script type="text/javascript">
	$('input[name="expected_on"]').datepicker({minDate:new Date()});
	$('input[name="serv_return_handl_on"]').datepicker({minDate:new Date()});
	
	$('.sel_prod_status').change(function(){
		
		if($(this).attr('pstatus') == $(this).val() && $(this).val() != 3)
		{
			$(this).parent().find('.capture_status_upd_msg').hide();
		}else
		{
			$(this).parent().find('.capture_status_upd_msg').show();
		}
		
		if($(this).val() == 1)
		{
			$(this).parent().find('.out_for_service_opts').show();	
		}else
		{
			$(this).parent().find('.out_for_service_opts').hide();
		}
		
		if($(this).val() == 5)
		{
			$(this).parent().find('.product_replacement_opts').show();	
		}else
		{
			$(this).parent().find('.product_replacement_opts').hide();
		}
		
		
		
	}).trigger('change');

	$('.remark_add_frm').submit(function(){
		var stat = $('select[name="return_prod_status"]',this).val()*1; 
		if(!stat)
		{
			alert("Choose Status First");
			return false;
		}
		
		// check if out for stock is selected 
		if(stat == 1)
		{
			if($('input[name="sent_to"]',this).val().length <= 0)
			{
				alert("Please input sent to");
				return false;
			} 
			if($('input[name="expected_on"]',this).val().length <= 0)
			{
				alert("Please input expected date from service.");
				return false;
			} 
		}
		
		$.post(site_url+'/admin/jx_upd_invretprodremark',$(this).serialize(),function(resp){
			if(resp.status == 'success')
			{
				alert("Remark posted sucessfully");
				location.href = location.href;
			}else
			{
				alert(resp.error);
			}
		},'json');
		return false;
	});
</script>
