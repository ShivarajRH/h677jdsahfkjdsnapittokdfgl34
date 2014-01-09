<div class="container">
	<h2>Manage Transport</h2>
	<a href="javascript:void(0)" id="add_transporter">add transporter</a><br>
	<?php if($transporters_details){?>
		<table class="datagrid" cellpadding="5" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th width="3%">#</th>
					<th width="10%">Name</th>
					<th width="7%">Contact</th>
					<th>Address</th>
					<th width="6%">City</th>
					<th width="6%">Pincode</th>
					<th width="19%">Destination address</th>
					<th width="15%">Types</th>
					<th width="5%">status</th>
					<th width="5%">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($transporters_details as $i=>$det){?>
				<tr>
					<td><?php echo $i+1;?></td>
					<td><?php echo $det['name'];?></td>
					<td><?php echo $det['contact_no'];?></td>
					<td><?php echo $det['address'];?></td>
					<td><?php echo $det['city'];?></td>
					<td><?php echo $det['pincode'];?></td>
					<td>
						<?php 
							$transport_types=explode(',',$det['allowed_transport']);
							echo '<a href="'.site_url('/admin/pnh_manage_trans_desc_address/'.$det['id']) .'">Manage destination address</a>';
						?>
					</td>
					<td>
						<?php 
						$type_det=array();
						foreach(explode(',',$det['allowed_transport']) as $type)
						{
							if($type==1)
								$type_det[]='Bus';
							else if($type==2)
								$type_det[]='Cargo';
							else if($type==3)
								$type_det[]='General packege';
						}
						echo implode(',',$type_det);
						?>
					</td>
					<td>
						<?php if($det['active']==1)
						{
							echo 'Active';
						}else{
							echo 'In active';
						}?>
					</td>
					<td><a href="javascript:void(0)" transporter_id="<?php echo $det['id']; ?>" class="edit_transporter">edit</a></td>
				</tr>
				<?php }?>
			</tbody>
		</table>
	<?php }else{
		echo 'No Transporters found';
	}?>
</div>

<!-- Modal for transported add /dit -->
<div id="transported_det_frm">
	<form method="post" class="form_block">
		<input type="hidden" name="transporter_id" value="0">
		<div class="form_error_block" style="background: #fffff0;padding:4px;display: none;color: #cd0000">
			<b>Please note:</b>
			<div class="error_content"></div>
		</div>
		<div id="transporter_det_blk">
			<fieldset>
    			<legend><b>Transporter Details:</b></legend>
    			<table  cellpadding="5" cellspacing="0">
    				<tr>
    					<td>Name : </td>
    					<td><input type="text" name="name"></td>
    				</tr>
    				<tr>
    					<td>Contact : </td>
    					<td><input type="text" name="contact"></td>
    				</tr>
    				<tr>
    					<td>Address : </td>
    					<td><textarea name="address" cols="25" rows="5"></textarea></td>
    				</tr>
    				<tr>
    					<td>City : </td>
    					<td><input type="text" name="city" ></td>
    				</tr>
    				<tr>
    					<td>Picode : </td>
    					<td><input type="text" name="pincode" ></td>
    				</tr>
    				<tr>
    					<td>Transport type:</td>
    					<td>
    						<table cellpadding="2" cellspacing="0">
    							<tr>
    								<td>Bus : <td>
    								<td><input type="checkbox" name="transport_type[]" value="1" ><td>
    							</tr>
    							<tr>
    								<td>Cargo : <td>
    								<td><input type="checkbox" name="transport_type[]" value="2"><td>
    							</tr>
    							<tr>
    								<td>General packege : <td>
    								<td><input type="checkbox" name="transport_type[]" value="3"><td>
    							</tr>
    						</table>
    					</td>
    				</tr>
    			  </table>
  			</fieldset>
		</div>
	</form>
</div>
<!-- Modal for transported add /dit end-->

<style>
	.error_content p{font-size: 11px;margin:3px 0px}
</style>

<script>
$('#add_transporter').click(function(e){
	e.preventDefault();
	$('#transported_det_frm').data({'transporter_id':0}).dialog('open');
});

$('.edit_transporter').click(function(e){
	e.preventDefault();
	$('#transported_det_frm').data({'transporter_id':$(this).attr('transporter_id')}).dialog('open');
});


var transporter_frm_dlg_ele = $('#transported_det_frm');
//dlg for add a tray
transporter_frm_dlg_ele .dialog({
							autoOpen:false,
							modal:true,
							height:'auto',
							width:'auto',
							autoResize:true,
							open:function(){
								// get dlg object to local var  
								var dlg = $(this);
								
								// get form ele in dialog to local var 
								var transporter_frm_ele = $('form',dlg);

								$('.form_error_block',transporter_frm_ele).hide();
								$('.form_error_block .error_content',transporter_frm_ele).html();

								// clear all form inputs in dlg  
								$('input[name="transporter_id"]',transporter_frm_ele).val("");
								$('input[name="name"]',transporter_frm_ele).val('');
								$('input[name="contact"]',transporter_frm_ele).val('');
								$('input[name="address"]',transporter_frm_ele).val('');
								$('textarea[name="address"]',transporter_frm_ele).val('');
								$('input[name="city"]',transporter_frm_ele).val('');
								$('input[name="pincode"]',transporter_frm_ele).val('');
								$('input[type="checkbox"]',transporter_frm_ele).attr("checked",false);

								// get tray id from local data storage in dlg ele   
								var transporter_id = $(this).data('transporter_id'); 
							
								// if tray id is set then get tray det ,for editting 	
									if(transporter_id)
									{
										dlg.dialog('option', 'title', 'Edit Transporter Details');
										
										$.getJSON(site_url+'/admin/jx_get_transporterdetbyid/'+transporter_id,'',function(resp){
											if(resp.status == 'error')
											{
												$('.form_error_block .error_content',transporter_frm_ele).html(resp.error);
												$('.form_error_block',transporter_frm_ele).show();
											}else
											{
												$('input[name="transporter_id"]',transporter_frm_ele).val(resp.transport_det.id);
												$('input[name="name"]',transporter_frm_ele).val(resp.transport_det.name);
												$('input[name="contact"]',transporter_frm_ele).val(resp.transport_det.contact_no);
												$('textarea[name="address"]',transporter_frm_ele).val(resp.transport_det.address);
												$('input[name="city"]',transporter_frm_ele).val(resp.transport_det.city);	
												$('input[name="pincode"]',transporter_frm_ele).val(resp.transport_det.pincode);	

												var bus_trasport_typs=resp.transport_det.allowed_transport.split(',');
												$.each(bus_trasport_typs,function(a,b){
													var check_box='input[value="'+b+'"]';	
													$(check_box).prop("checked", true);
												});
											}
										});
									}else
									{
										dlg.dialog('option', 'title', 'Add Transporter Details');
									}
							},
							buttons:{
								'Submit' : function(){
											var transporter_id = $('form input[name="transporter_id"]',transporter_frm_dlg_ele).val();
											if(confirm("Are you sure to "+(transporter_id?'Edit':'Add')+" Transporter Details "))
											{
												var action_func = 'jx_pnh_addtransporter';
												if(transporter_id)
													action_func = 'jx_pnh_edittransporter';	
												
												$.post(site_url+'/admin/'+action_func,$('form',transporter_frm_dlg_ele).serialize(),function(resp){
													if(resp.status == 'success')
													{
														location.href = site_url+'/admin/pnh_transport_management';
													}else
													{
														$('form .form_error_block .error_content',transporter_frm_dlg_ele).html(resp.error);
														$('form .form_error_block',transporter_frm_dlg_ele).show();
													}
												},'json');
											}	
											else
												return false;
									 },
								'Cancel':function(){
									$(this).dialog('close');
								}
							}
});

</script>

<!-- <div id="desc_details">
			<h4>Destination deatils</h4>
			<table cellpadding="5" cellspacing="0">
				<tr>
					<td>Destination Name :</td>
					<td><input type="text" name="desc_name[]"></td>
				</tr>
				<tr>
					<td>Contact : </td>
					<td><input type="text" name="desc_contact[]"></td>
				</tr>
				<tr>
					<td>Address :</td>
					<td><textarea name="desc_address[]" cols="25" rows="5"></textarea></td>
				</tr>
				<tr>
					<td>City :</td>
					<td><input type="text" name="dsc_city[]"></td>
				</tr>
				<tr>
					<td>Pincode :</td>
					<td><input type="text" name="dsc_pincode[]"></td>
				</tr>
			</table>
		</div>
		<div></div> -->
