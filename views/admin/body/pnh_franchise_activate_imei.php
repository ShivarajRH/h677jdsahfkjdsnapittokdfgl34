<link rel="stylesheet" href="<?php echo base_url().'/css/buttons.css'?>" type="text/css">
<div class="page_wrap container">
	<div class="clearboth">
		<div class="fl_left" >
			<h2 class="page_title">Franchise IMEINO Activation</h2>
		</div>
	</div>
	<div class="page_content">
		<table width="100%" cellpadding="0">
			<tr>
				<td width="30%">
					<div class="form" style="background: #fafafa;margin-right:20px;padding:10px;">
						<form action="<?php echo site_url('admin/pnh_process_franchise_imei_activation');?>" id="frm_franimeiactv" method="post">
							<table cellpadding="10" cellspacing="0" border="0" style="border-collapse: collapse">
								<tr style="background: #f1f1f1">
									<td><b style="padding:5px;">Enter IMEI</b> <span class="red_star">*</span></td>	
									<td><input type="text" style="width: 200px;" value="<?php echo set_value('imei_no');?>" name="imei_no" >
										<?php echo form_error('imei_no','<span class="error_msg">','</span>');?>
									</td>
								</tr>
								<tr id="imei_det"></tr>
								<tr>
									<td><b>Mobileno</b> <span class="red_star">*</span></td>	
									<td>
										<input type="hidden" name="franchise_id" value="0">
										<input type="hidden" name="member_id" value="0">
										
										<input maxlength="10" type="text" style="width: 200px;" value="<?php echo set_value('mobno');?>" name="mobno" >
										<span id="mobno_resp_msg" style="font-size: 9px"></span>
										<?php echo form_error('mobno','<span class="error_msg">','</span>');?>
									</td>
								</tr>
								<tr id="new_memname" style="display:none">
									<td><b>Name</b></td>	
									<td>
										<input type="text" style="width: 200px;" name="mem_name" value="">
									</td>
								</tr>		
								<tr id="mobno_det"></tr>
								<tr>
									<td colspan="2" align="left">
										<input type="submit" disabled="" id="actv_submit_btn" class="button button-flat-royal button-small button-rounded" value="Activate IMEI/Serialno" > 
									</td>
								</tr>
							</table>
						</form>
					</div>
				</td>
				<td valign="top" width="70%" align="left">
					 <div>
						<?php
							$imei_actv_list = $this->db->query("select f.invoice_no,e.username as activated_byname,imei_activated_on,activated_by,activated_mob_no,activated_member_id,d.franchise_id,franchise_name,imei_no,imei_reimbursement_value_perunit as imei_credit_amt from t_imei_no a join king_orders b on a.order_id = b.id join king_transactions c on c.transid = b.transid join pnh_m_franchise_info d on d.franchise_id = c.franchise_id left join king_admin e on e.id = a.activated_by join king_invoice f on f.order_id = b.id where a.is_imei_activated = 1 order by imei_activated_on desc limit 10");
						?>
						<h3 style="margin:5px 0px">Latest IMEI/Serialno Activations</h3>
						<table class="datagrid" width="100%">
							<thead>
								<th width="20" style="text-align: left">Slno</th>
								<th width="130"  style="text-align: left">Activated On</th>
								<th width="70"  style="text-align: left">Activated By</th>
								<th  style="text-align: left">Franchise</th>
								<th  style="text-align: left" width="80">Invoiceno</th>
								<th  style="text-align: left" width="150">IMEI/Serial no</th>
								<th  style="text-align: left" width="100">Mobile no</th>
								<th  style="text-align: left" width="100">Activated MemberID</th>
								<th  style="text-align: left" width="30">Credit</th>
							</thead>
							<tbody>
								<?php
									$i=0;
									foreach($imei_actv_list->result_array() as $imei_det)
									{
								?>
										<tr>
											<td><?php echo ++$i ?></td>
											<td><?php echo format_datetime($imei_det['imei_activated_on']) ?></td>
											<td><?php echo ($imei_det['activated_byname']?$imei_det['activated_byname']:'SMS') ?></td>
											<td><?php echo anchor('admin/pnh_franchise/'.$imei_det['franchise_id'],$imei_det['franchise_name'],'target="_blank"') ?></td>
											<td><a href="<?php echo site_url('admin/invoice/'.$imei_det['invoice_no']);?>" target="_blank"><?php echo $imei_det['invoice_no'] ?></a></td> 
											<td><?php echo $imei_det['imei_no'] ?></td>
											<td><?php echo $imei_det['activated_mob_no'] ?></td>
											<td><?php echo $imei_det['activated_member_id'] ?></td>
											<td><?php echo $imei_det['imei_credit_amt'] ?></td>
										</tr>
								<?php				
									}
								?>
							</tbody>
						</table>
					</div>
				</td>
			</tr>
		</table>
		
	</div>
</div>
<style>
	.red_star{color:#cd0000}
	.error_msg{font-size: 10px;background: rgba(205, 0, 0, 0.6);color: #FFF;padding:3px;border-radius:3px;display: inline-block;}
	.leftcont {display:none;}
</style>
<script type="text/javascript">

	$('input[name="imei_no"]').change(function(){
		
		$('input[name="member_id"]').val(0);
		$('input[name="franchise_id"]').val(0);
		
		
		$('#new_memname').hide();
		$('input[name="mem_name"]').val('');
		
		$('#mobno_overview').html('').hide();
		
		$('input[name="mobno"]').val('').attr('disabled',true);
		
		$('#imei_det').html('<td colspan="2" style="background: #ffffa0;padding:10px;color:#333"><div id="imei_overview">Loading...</div></td>');
		
		$('#actv_submit_btn').attr('disabled',true); 
		$.post(site_url+'/admin/jx_getimeidet','imeino='+$(this).val(),function(resp){
			if(resp.error)
			{
				$('#imei_overview').html(resp.error);
			}else
			{
				var html = '<table cellpadding="3" style="font-size: 12px">'
					html +=	'	<tr><td width="120"><b>Franchise</b></td><td><a target="_blank" href="'+site_url+'/admin/pnh_franchise/'+resp.det.franchise_id+'">'+resp.det.franchise_name+'</a></td></tr>'
					html +=	'	<tr><td><b>Product</b></td><td>'+resp.det.product_name+'</td></tr>'
					html +=	'	<tr><td><b>MemberID</b></td><td>'+resp.det.member_id+'</td></tr>'
					html +=	'	<tr><td><b>Invoiceno</b></td><td><a target="_blank" href="'+site_url+'/admin/invoice/'+resp.det.invoice_no+'">'+resp.det.invoice_no+'</a></td></tr>'
					html +=	'	<tr><td><b>TransID</b></td><td><a target="_blank" href="'+site_url+'/admin/trans/'+resp.det.transid+'">'+resp.det.transid+'</a>'+' - ('+resp.det.ordered_on+')'+'</td></tr>';
					html +=	'	<tr><td><b>Scheme Enabled</b></td><td>'+((resp.det.imei_scheme_id*1)?'Yes':'No')+'</td></tr>';
					if(resp.det.imei_scheme_id)
					{
						html +=	'	<tr><td><b>Activation Credit</b></td><td>'+resp.det.imei_reimbursement_value_perunit+'</td></tr>';
						html +=	'	<tr><td><b>Status</b></td><td>'+((resp.det.is_imei_activated*1)?'<b style="color:#cd0000">Already Activated<b>':'<b style="color:green">Not Activated</b>')+'</td></tr>';	
					}
					html +=	'</table>';
				
				$('input[name="member_id"]').val(resp.det.member_id);
				$('input[name="franchise_id"]').val(resp.det.franchise_id);
				
				if(resp.det.is_imei_activated*1)
				{
					$('input[name="mobno"]').attr('disabled',true);
				}else
				{
					$('input[name="mobno"]').attr('disabled',false);
				}
				$('#imei_overview').html(html);
			}
		},'json');	
		 
	});
	
	$('input[name="mobno"]').change(function(){
		
		$('#new_memname').hide();
		$('input[name="mem_name"]').val('');
		$('#actv_submit_btn').attr('disabled',true);
		$('#mobno_det').html('<td colspan="2" style="padding:0px;"><div id="mobno_overview" style="background: #ffffd0;padding:10px;color:#333">Loading...</div></td>');
		
		var mobno=$(this).val();
		var params = {fid:$('input[name="franchise_id"]').val(),mobno:$(this).val(),mid:$('input[name="member_id"]').val()}
			$.post(site_url+'/admin/jx_validate_mobno_imei',params,function(resp){
				if(resp.error != undefined)
				{
					$('#mobno_overview').html(resp.error);
				}else
				{
					var html = '';
						if(resp.member_id*1 != $('input[name="member_id"]').val())
						{
							html = "<div>Mobileno "+mobno+" is already registered to "+resp.member_id+" </div>";
							
							if(resp.pen_ttl_actv)
							{
								html += "<div><br> Do you want to Activate IMEI to this mobile <input type=checkbox name='actv_confrim' value='1' > </div>";
								$('#actv_submit_btn').attr('disabled',false);
							}else
							{
								html += "<div><br> Activation Limit Ended for this MemberID</div>";
								$('#actv_submit_btn').attr('disabled',true);
							}
							
						}else
						{
							if(resp.pen_ttl_actv)
							{
								$('#new_memname').show();
								$('#actv_submit_btn').attr('disabled',false);
							}else
							{
								html += "<div><br> Activation Limit Ended for this mobileno</div>";
								$('#actv_submit_btn').attr('disabled',true);
							}
						}
						
						if(html)
							$('#mobno_overview').html(html).show();
						else
							$('#mobno_overview').html('').hide();
				}
			},'json');
	});
	
	$('input[name="actv_confrim"]').live('change',function(){
		if($(this).attr('checked'))
		{
			$('#actv_submit_btn').attr('disabled',false);
		}else
		{
			$('#actv_submit_btn').attr('disabled',true);
		}
	});
	
</script>
 
