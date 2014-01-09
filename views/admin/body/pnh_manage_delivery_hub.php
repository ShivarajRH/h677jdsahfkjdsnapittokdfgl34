
<link rel="stylesheet" href="<?php echo base_url().'/css/buttons.css'?>" type="text/css">

<div class="page_wrap container" style="width: 98%;">
	
	<h2 class="page_title">Manage Delivery Hubs</h2>
	
	<div class="page_topbar" >
		<div class="page_topbar_left fl_left" >
			<span class="total_overview">Total Hubs : <b><?php echo count($hub_list); ?> </b> </span>
		</div>
		<div class="page_action_buttonss fl_right" align="right">
			<a href="javascript:void(0)" id="add_delhub" class="button button-rounded button-flat-primary">Add Hub</a>
		</div>
	</div>
	
	<div style="clear:both">&nbsp;</div>
	
	<div class="page_content">
		<?php 
			if($hub_list)
			{ 
		?>
		<table class="datagrid">
			<thead>
				<th width="30">Slno</th>
				<th width="200"><b>Hub Name</b></th>
				<th width="200"><b>Linked Towns</b></th>
				<th width="200"><b>Linked FCs</b></th>
				<th width="150"><b>Created By</b></th>
				<th width="130"><b>Created On</b></th>
				<th width="50"><b>&nbsp;</b></th>
			</thead>
			<tbody>
				<?php foreach($hub_list as $i=>$hub){
						$linked_towns = explode(',',$hub['linked_towns']);
						sort($linked_towns);
						
						$linked_fcs = explode(',',$hub['linked_fcs']);
						sort($linked_fcs);
						
					?>
					<tr>
						<td><?php echo ($i+1);?></td>
						<td><?php echo $hub['hub_name'];?></td>
						<td><ol class="ordered_list"><?php echo '<li>'.implode('</li><li>',$linked_towns).'</li>';?></ol></td>
						<td><ol class="ordered_list"><?php if($hub['linked_fcs']) echo '<li>'.implode('</li><li>',$linked_fcs).'</li>';?></ol></td>
						<td><?php echo $hub['created_byname'];?></td>
						<td><?php echo format_datetime($hub['created_on']);?></td>
						<td>
							<a href="javascript:void(0)" hub_id="<?php echo $hub['id'];?>" class="edit_delhub">Edit</a> 
							<a href="javascript:void(0)" hub_id="<?php echo $hub['id'];?>" class="delete_delhub">Delete</a>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<?php 
			}else
			{
				echo '<h4><b>No Hubs Found</b></h4>';		
			}
		?>
	</div>
	
</div>

<div id="manage_delivery_hub" >
	<form action="<?php echo site_url('admin/pnh_upd_hubinfo');?>" method="post">
		<input type="hidden" value="0" name="hub_id" >
		<table>
			<tr><td><b>Hub Name</b></td> <td><input type="text" name="hub_name" value=""></td></tr>
			<tr><td><b>Link Towns</b></td> <td><select name="town_id[]"  data-placeholder="Choose"  style="width: 300px" multiple="true"  class="sel_multi_town"></select> </td></tr>
			<tr>
				<td><b>Link Field Coordinators</b></td> 
				<td><select name="fc_id[]" data-placeholder="Choose" style="width: 300px" multiple="true"  class="sel_fc_list"></select> </td>
			</tr>
		</table>
	</form>
</div>

<script type="text/javascript">
	
	
	$('#manage_delivery_hub').data('hub_id',0);
	$('#manage_delivery_hub').dialog({
										width:500,
										height:450,
										autoOpen:false,
										modal:true,
										open:function(){
											var dlg = $(this);
											var hub_id = dlg.data('hub_id');
												
												
												$('input[name="hub_id"]',dlg).val(hub_id);
												$('input[name="hub_name"]',dlg).val('');
												$('select[name="town_id[]"]').html('').trigger('liszt:updated');
												$('select[name="fc_id[]"]').html('').trigger('liszt:updated');
												
												dlg.dialog('option', 'title', 'Add Delivery Hub');
												
												if(hub_id)
													dlg.dialog('option', 'title', 'Edit Delivery Hub Details');
												
												$.getJSON(site_url+'/admin/jx_gethubdet/'+hub_id,function(resp){
														if(resp.status == 'error')
														{
															alert(resp.message);
														}else
														{
															var town_opthtml = fc_opthtml = '';
															
															if(resp.hubdet != undefined) 
																$('input[name="hub_name"]',dlg).val(resp.hubdet.hub_name);
																
															if(resp.town_list.length)
															{
																$.each(resp.town_list,function(a,b){
																	var stat = 0;
																		if(hub_id == b.hub_id && b.is_linked == 1)
																			stat = 1;
																		else if(b.hub_id == 0 && b.is_linked == 0)
																		 	stat = 1;
																	if(stat)
																		town_opthtml += '<option value="'+b.id+'" '+((hub_id == b.hub_id && b.hub_id != 0 && b.is_linked == 1 )?'selected':'')+' >'+b.town_name+'</option>';	
																});
															}
															$('select[name="town_id[]"]',dlg).html(town_opthtml).trigger('liszt:updated');
															
															if(resp.fc_list.length)
															{
																$.each(resp.fc_list,function(a,b){
																	fc_opthtml += '<option value="'+b.employee_id+'"  '+((b.is_linked==1)?'selected':'')+' >'+b.emp_name+'</option>';	
																});
															}
															$('select[name="fc_id[]"]',dlg).html(fc_opthtml).trigger('liszt:updated');
														}
												});
												
												
										},
										buttons:{
											'Submit' : function(){
												var frmEle = $('#manage_delivery_hub form');
												var error_msg = new Array();
												
													$('input[name="hub_name"]',frmEle).val($.trim($('input[name="hub_name"]',frmEle).val()));
													
													if(!$.trim($('input[name="hub_name"]',frmEle).val()).length)
														error_msg.push("Enter Hub name");
													
													if($('select[name="town_id[]"]',frmEle).val() == null)
														error_msg.push("Select atleast one Town");
													
												
													if(error_msg.length)
													{
														alert(error_msg.join("\r\n"));
													}else
													{
														$.post(frmEle.attr('action'),frmEle.serialize(),function(resp){
															if(resp.status == 'error')
															{
																alert(resp.error);
															}else
															{
																alert(resp.message);
																location.href = location.href ;
															}
														},'json');
													}
													
													
											},
											'Cancel' : function(){
												$('#manage_delivery_hub').dialog('close');
											}
										}
								});
	
	$('#add_delhub').click(function(e){
		e.preventDefault();
		$('#manage_delivery_hub').data('hub_id',0).dialog('open');
	});
	$('.edit_delhub').click(function(e){
		e.preventDefault();
		$('#manage_delivery_hub').data('hub_id',$(this).attr('hub_id')).dialog('open');
	});
	
	$('.delete_delhub').click(function(e){
		e.preventDefault();
		if(confirm("Are you sure want to delete this hub ?"))
		{
			$.getJSON(site_url+'/admin/pnh_delete_hubdet/'+$(this).attr('hub_id'),{},function(resp){
				if(resp.status == 'error')
				{
					alert(resp.message);
					location.href = location.href;
				}else
				{
					alert("Hub deleted Successfully");
				}
			});	
		}
	});
	
	
	$('select[name="town_id[]"]').chosen();
	$('select[name="fc_id[]"]').chosen();	
	
	 
	
</script>

<style>
	.leftcont{display: none}
	.fl_left{float: left;}
	.fl_right{float: right;}
	.clearboth{clear:both}
	
	.page_wrap{width: 99%;}
	.page_wrap .page_title{margin:7px 0px}
	.page_wrap .page_topbar{clear: both;}
	
	.page_wrap .page_topbar .page_topbar_left{width: 49%;}
	.page_wrap .page_topbar .page_topbar_right{width: 49%;}
	.page_wrap .page_content{clear:both}
	
	.page_wrap .page_topbar .total_overview{padding:5px 0px;font-size: 16px;}
	
	.ordered_list{margin:0px;padding-left:10px;}
</style>
