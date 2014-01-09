<div class="container">
	<h2>Manage Trays</h2>
	<a href="javascript:void(0)" id="add_tray" >Add tray</a>
	<?php if($trays_list){?>
		<table class="datagrid">
			<thead>
				<tr>
					<th>#</th>
					<th>Tray</th>
					<th>Volume</th>
					<th>Tray Status</th>
					<th>Assigned to</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($trays_list as $i=>$tray){?>
				<tr>
					<td><?php echo $i+1;?></td>
					<td><?php echo $tray['tray_name'];?></td>
					<td><?php echo $tray['max_allowed'];?></td>
					<td>
						<?php 
							if($tray['tray_status']==1)
								echo '<span style="color:green;"><b>free</b></span>';
							else 
								echo '<span style="color:red;"><b>In-use - </b><a href="javascript:void(0)" tray_id="'.$tray['tray_id'].'" class="show_invoices">View Invoices</a></span>';
						?>
					</td>
					<td><?php echo $tray['territory_name']; ?></td>
					<td>
						<?php 
							if($tray['tray_status']==1)
							{
						?>
								<a class="edit_tray" href="javascript:void(0)" tray_id="<?php echo $tray['tray_id'];?>" >edit</a>
						<?php 
							}
						?>
					</td>
				</tr>
				<?php }?>
			</tbody>
		</table>
		
	<?php }else{
		echo 'No Trays Found';
	}?>
</div>

<!-- Tray ADD/EDIT modal start -->
<div id="tray_frm_dlg" title="Add New Tray">
	<form method="post" class="form_block">
		<input type="hidden" name="tray_id" value="0">
		<div class="form_error_block" style="background: #fffff0;padding:4px;display: none;color: #cd0000">
			<b>Please note:</b>
			<div class="error_content"></div>
		</div>
		<table align="center" cellpadding="5" cellspacing=0>
			<tbody>
				<tr>
					<td>Tray Name:</td>
					<td><input type="text" name="tray_name"></td>
				</tr>
				<tr>
					<td>Max allowed:</td>
					<td><input type="text" name="max_allowed" size="8"></td>
				</tr>
			</tbody>
		</table>
	</form>
</div>
<!-- Tray ADD/EDIT modal end-->

<!-- modal for invoices list -->
<div id="invoices_list" title="Invoices list">
</div>
<!-- modal for invoices list end -->

<script>
	var tray_frm_dlg_ele = $('#tray_frm_dlg');
	//dlg for add a tray
	tray_frm_dlg_ele.dialog({
								autoOpen:false,
								modal:true,
								 
								height:'auto',
								autoResize:true,
								open:function(){
									// get dlg object to local var  
									var dlg = $(this);
									
									// get form ele in dialog to local var 
									var tray_frm_ele = $('form',dlg);

									$('.form_error_block',tray_frm_ele).hide();
									$('.form_error_block .error_content',tray_frm_ele).html();

									// clear all form inputs in dlg  
									$('input[name="tray_id"]',tray_frm_ele).val("");
									$('input[name="tray_name"]',tray_frm_ele).val('');
									$('input[name="max_allowed"]',tray_frm_ele).val('');

									// get tray id from local data storage in dlg ele   
									var tray_id = $(this).data('tray_id'); 
								
									// if tray id is set then get tray det ,for editting 	
										if(tray_id)
										{
											dlg.dialog('option', 'title', 'Edit Tray Details');
											
											$.getJSON(site_url+'/admin/jx_get_traydetbyid/'+tray_id,'',function(resp){
												if(resp.status == 'error')
												{
													$('.form_error_block .error_content',tray_frm_ele).html(resp.error);
													$('.form_error_block',tray_frm_ele).show();
												}else
												{
													$('input[name="tray_id"]',tray_frm_ele).val(resp.tray_det.tray_id);
													$('input[name="tray_name"]',tray_frm_ele).val(resp.tray_det.tray_name);
													$('input[name="max_allowed"]',tray_frm_ele).val(resp.tray_det.max_allowed);	
												}
											});
										}else
										{
											dlg.dialog('option', 'title', 'Add Tray Details');
										}
								},
								buttons:{
									'Submit' : function(){
												var tray_id = $('form input[name="tray_id"]',tray_frm_dlg_ele).val();
												if(confirm("Are you sure to "+(tray_id?'Edit':'Add')+" Tray Details "))
												{
													var action_func = 'jx_pnh_addtray';
													if(tray_id)
														action_func = 'jx_pnh_edittray';	
													
													$.post(site_url+'/admin/'+action_func,$('form',tray_frm_dlg_ele).serialize(),function(resp){
														if(resp.status == 'success')
														{
															location.href = site_url+'/admin/pnh_tray_management';
														}else
														{
															$('form .form_error_block .error_content',tray_frm_dlg_ele).html(resp.error);
															$('form .form_error_block',tray_frm_dlg_ele).show();
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

	$('#add_tray').click(function(e){
		e.preventDefault();
		$('#tray_frm_dlg').data({'tray_id':""}).dialog('open');
	});
	$('.edit_tray').click(function(e){
		e.preventDefault();
		$('#tray_frm_dlg').data({'tray_id':$(this).attr('tray_id')}).dialog('open');
	});


	$(".show_invoices").click(function(){
		var tray_id=$(this).attr("tray_id");
		$("#invoices_list").data({'tray_id':tray_id}).dialog('open');
		
	});

	$("#invoices_list").dialog({
		autoOpen:false,
		modal:true,
		width:'480px',
		height:'auto',
		autoResize:true,
		open:function(){
			$("#invoices_list").html('');
			var tray_id=$(this).data('tray_id');
			var html_cnt='';
			$.post(site_url+'/admin/jx_get_invbytray',{tray_id:tray_id},function(res){
				html_cnt+="<table class='datagrid' cellpadding='5' cellspacing='0' width='100%'>";
				html_cnt+="	<thead>";
				html_cnt+="			<tr>";
				html_cnt+="				<th>#</th>";
				html_cnt+="				<th>Hub</th>";
				html_cnt+="				<th>Town</th>";
				html_cnt+="				<th>Invoice</th>";
				html_cnt+="			</tr>";
				html_cnt+="	</thead>";
				html_cnt+="	<tbody>";
				$.each(res.inv_by_Tray,function(a,b){
					html_cnt+="	<tr>";
					html_cnt+="		<td>"+(a+1)+"</td>";
					html_cnt+="		<td>"+b.territory_name+"</td>";
					html_cnt+="		<td>"+b.town_name+"</td>";
					var tem =new  Array();
					$.each(b.invoice_nos.split(','),function(c,d){
						var inv="<a href="+site_url+"/admin/invoice/"+d+" target='_blank'>"+d+"</a>";
						tem.push(inv);
					});
					html_cnt+="		<td>"+tem.join(' ')+"</td>";
					html_cnt+="	</tr>";	
				});
				html_cnt+="	</tbody></table>";
				$("#invoices_list").html(html_cnt);
				
			},'json');
		},
		buttons:{
			'Close':function(){
				$(this).dialog('close');
			}
		}
	});
			
	
</script>
<style>
	.error_content p{font-size: 11px;margin:3px 0px}
</style>
