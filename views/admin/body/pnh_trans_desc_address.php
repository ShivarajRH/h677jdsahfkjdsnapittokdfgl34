<div class="containe">
	<h2>Manage destination address for  <a href="<?php echo site_url('admin/pnh_transport_management')?>" style="text-decoration:underline;"><?php echo $trans_name; ?></a></h2>
	<a href="javascript:void(0)" id="add_destination_address" transporter_id="<?php echo $transporter_id; ?>">Add destination address</a>
	<span style="float:right;"><a href="<?php echo site_url('admin/pnh_transport_management') ?>">View Tranport management</a></span><br>
	<?php if($trans_desc_address){?>
		<table class="datagrid" cellpadding="5" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th width="3%">#</th>
					<th width="6%">Type</th>
					<th width="10%">Destination name</th>
					<th width="7%">Contact</th>
					<th>Address</th>
					<th width="6%">City</th>
					<th width="6%">Pincode</th>
					<th width="5%">Action</th>
				<tr>
			</thead>
			<tbody>
				<?php foreach($trans_desc_address as $i=>$des_det){?>
				<tr>
					<td><?php echo $i+1;?></td>
					<td>
						<?php 
							$dest_types = explode(',',$des_det['type']);
							foreach($dest_types as $dtype)
							{
								if($dtype==1)
									echo 'Bus <br>';
								else if($dtype==2)
									echo ' Cargo <br>';
								else if($dtype==3)
									echo ' General packege';
							}
							
						?>
					</td>
					<td><?php echo $des_det['short_name'];?></td>
					<td><?php echo $des_det['contact_no'];?></td>
					<td><?php echo $des_det['address'];?></td>
					<td><?php echo $des_det['city'];?></td>
					<td><?php echo $des_det['pincode'];?></td>
					<td><a href="javascript:void(0)" class="edit_transporter_des_address" transporter_des_id="<?php echo $des_det['id'];?>" transporter_id="<?php echo $transporter_id; ?>">edit</a></td>
				</tr>
				<?php }?>
			</tbody>
		</table>
	<?php }else{
		echo 'No destination address found for '.$trans_name;
	}?>
</div>


<!-- Modal for transported add /dit -->
<div id="transported_det_frm">
	<form method="post" class="form_block">
		<input type="hidden" name="transporter_desc_id" value="0">
		<input type="hidden" name="transporter_id" value="0">
		<div class="form_error_block" style="background: #fffff0;padding:4px;display: none;color: #cd0000">
			<b>Please note:</b>
			<div class="error_content"></div>
		</div>
		<div id="transporter_det_blk">
			<fieldset>
    			<legend><b><?php echo $trans_name;?> Transporter destination address Detail:</b></legend>
    			<table  cellpadding="5" cellspacing="0">
    				<tr>
    					<td>Destination name : </td>
    					<td><input type="text" name="name"></td>
    				</tr>
    				<tr>
    					<td>Contact : </td>
    					<td>
    						<ol id="contactList">
    							<li><input type="text" name="contact[]" class="clearContent"></li>
    						</ol>
    					</td>
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
    					<td>Pincode : </td>
    					<td><input type="text" name="pincode" ></td>
    				</tr>
    				<tr>
    					<td>Transport type: </td>
    					<td>
    						<ol>
    						<?php foreach(explode(',',$transporter_types) as $type){
    							$type_name='';
    							if($type==1)
    								$type_name='Bus';
    							else if($type==2)
    								$type_name='Cargo';
    							else if($type==3)
    								$type_name='General packege';
    							?>
    							<li><?php echo $type_name. ':' ?><input type="checkbox" name="trasport_type[]" value="<?php echo $type; ?>"></li>
    						<?php }?>
    						</ol>
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
$('#add_destination_address').click(function(e){
	e.preventDefault();
	$('#transported_det_frm').data({'transporter_des_id':0,'transporter_id':$(this).attr("transporter_id")}).dialog('open');
});

$('.edit_transporter_des_address').click(function(e){
	e.preventDefault();
	$('#transported_det_frm').data({'transporter_des_id':$(this).attr('transporter_des_id'),'transporter_id':$(this).attr("transporter_id")}).dialog('open');
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
								$('input[name="transporter_desc_id"]',transporter_frm_ele).val("");
								$('input[name="transporter_id"]',transporter_frm_ele).val("");
								$('input[name="name"]',transporter_frm_ele).val('');
								$('input[name="contact"]',transporter_frm_ele).val('');
								$('input[name="address"]',transporter_frm_ele).val('');
								$('input[name="city"]',transporter_frm_ele).val('');
								$('input[name="pincode"]',transporter_frm_ele).val('');
								$('input[type="checkbox"]',transporter_frm_ele).attr('checked',false);


								// get tray id from local data storage in dlg ele   
								var transporter_id = $(this).data('transporter_id'); 
								var transporter_des_id = $(this).data('transporter_des_id'); 
							
								// if tray id is set then get tray det ,for editting 	
									if(transporter_des_id)
									{
										dlg.dialog('option', 'title', 'Edit Transporter destination address Detail');
										
										$.getJSON(site_url+'/admin/jx_get_transporter_dest_address_detbyid/'+transporter_des_id+'/'+transporter_id,'',function(resp){
											if(resp.status == 'error')
											{
												$('.form_error_block .error_content',transporter_frm_ele).html(resp.error);
												$('.form_error_block',transporter_frm_ele).show();
											}else
											{
												if(resp.trans_dest_addr_alloted_types != null)
													types_alloted = resp.trans_dest_addr_alloted_types.split(',');
												else
													types_alloted = new Array();
												
												$('input[name="transporter_id"]',transporter_frm_ele).val(resp.transport_des_address.transpoter_id);
												$('input[name="transporter_desc_id"]',transporter_frm_ele).val(resp.transport_des_address.id);
												$('input[name="name"]',transporter_frm_ele).val(resp.transport_des_address.short_name);
												$('textarea[name="address"]',transporter_frm_ele).val(resp.transport_des_address.address);
												$('input[name="city"]',transporter_frm_ele).val(resp.transport_des_address.city);	
												$('input[name="pincode"]',transporter_frm_ele).val(resp.transport_des_address.pincode);
													
												var bus_trasport_typs=resp.transport_des_address.type;
													$.each(bus_trasport_typs,function(a,b){
														var check_box_ele='input[value="'+b+'"]';
															$(check_box_ele).prop("checked", true);
															 
													});
												
												

												$('input[name="trasport_type[]"]').show();
																									
												$.each(types_alloted,function(c,d){
													$('input[value="'+d+'"]').hide();
												});
												
												//$('input[name="contact"]',transporter_frm_ele).val(resp.transport_des_address.contact_no);
												$("#contactList").html('');
												var contact_html='';
												$.each(resp.transport_des_address.contact_no.split(','),function(a,b){
													contact_html+="<li><input type='text' name='contact[]' class='clearContent' value='"+b+"'></li>";
												});
												$("#contactList").html(contact_html);
												$('#contactList').manageList();
											}
										});
									}else
									{
										$('input[name="transporter_id"]',transporter_frm_ele).val(transporter_id);
										$('input[name="transporter_desc_id"]',transporter_frm_ele).val('');
										$('input[name="name"]',transporter_frm_ele).val('');
										$('textarea[name="address"]',transporter_frm_ele).val('');
										$('input[name="city"]',transporter_frm_ele).val('');	
										$('input[name="pincode"]',transporter_frm_ele).val('');
										$('input[name="contact[]"]',transporter_frm_ele).val('');	
										$("#contactList").html('');
										$("#contactList").html("<li><input type='text' name='contact[]' class='clearContent'></li>");
										$('#contactList').manageList();
										dlg.dialog('option', 'title', 'Add Transporter destination address Detail');
										
									}
							},
							buttons:{
								'Submit' : function(){
											var transporter_des_id = $('form input[name="transporter_desc_id"]',transporter_frm_dlg_ele).val();
											var transporter_id= $('form input[name="transporter_id"]',transporter_frm_dlg_ele).val();
											
											if(confirm("Are you sure to "+(transporter_des_id?'Edit':'Add')+" Transporter destination address Details "))
											{
												var action_func = 'jx_pnh_addtransporter_des_address';
												if(transporter_des_id)
													action_func = 'jx_pnh_edittransporter_des_address';	

												$("input[type='checkbox']",transporter_frm_dlg_ele).removeAttr("disabled", "disabled");
																								
												$.post(site_url+'/admin/'+action_func,$('form',transporter_frm_dlg_ele).serialize(),function(resp){
													if(resp.status == 'success')
													{
														location.href = site_url+'/admin/pnh_manage_trans_desc_address/'+transporter_id;
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

$('#contactList').manageList();

</script>

<style>
.manageListOptions{
	font-weight:bold;
	font-size:14px;
	cursor:pointer;
	background:#555;
	color:#FFF;
	padding:1px 4px;
	margin:1px;
	border-radius:5px;
	margin-left: 5px;
}
</style>

