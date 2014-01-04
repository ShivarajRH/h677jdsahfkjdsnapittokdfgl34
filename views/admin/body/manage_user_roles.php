<div class="container">
	<h2 class="page_title">Manage ERP user roles</h2>
	
	<div class="page_topbar" >
		<div class="page_topbar_left fl_left" >
		</div>
		
		<div class="page_action_buttonss fl_right" align="right" >
			Select all:<input type="checkbox" name="select_all_modules">
		</div>
	</div>
	
	<div style="clear:both">&nbsp;</div>
	
	<form action="<?php echo site_url('admin/userrole_access_permission_link_process') ?>" method="post" id="role_access_link_form">
		<table class="datagrid" width="100%" cellpadding="5" cellspacing="0">
			<tbody>
				<tr>
					<td width="10%"><b>User roles : </b></td>
					<td>
						<select name="user_role">
							<option value=''>Choose</option>
						<?php 
							if($erp_user_roles)
							{
								foreach($erp_user_roles as $role)
								{
						?>
										<option value="<?php echo $role['role_id'] ?>"><?php echo $role['role_name']?></option>					
						<?php	}
							}
						?>
						</select>
					</td>
				</tr>
				
				<tr>
					<td><b>Modules : </b></td>
					<td>
						<?php
							if($module_access_permission_link)
							{
								foreach($module_access_permission_link as $module=>$permission_link)
								{
							?>
									<div>
										<fieldset>
											<legend><input type="checkbox" name="select_all_acccess" value="<?php echo $module;?>"> <b><?php echo ucfirst($module); ?></b></legend>
												<ul style="list-style: none;padding:2px;">
													<?php 
														foreach($permission_link as $id=>$access)
														{
													?>
														<li style="display:inline-block;padding:2px;"><input type="checkbox" value="<?php echo $id;?>" name="permission_access[]" class="access_permission_<?php echo $id.' '.$module;?>"><?php echo ucwords(str_replace('_',' ',$access));?> </li>		
													<?php
														}
													?>
												</ul>
										</fieldset>
									</div>
						<?php 			
								}
							} 
						?>
					</td>
				</tr>
				
				<tr>
					<td colspan="2" align="right"><input type="submit" value="submit"></td>
				</tr>
			</tbody>
		</table>
	</form>
</div>

<script>
	$("select[name='user_role']").change(function(){
		var role_id=$(this).val();
		$("input[name='permission_access[]']").attr("checked",false);
		$.post(site_url+'/admin/jx_get_userrole_access_permission_link',{role_id:role_id},function(res){
		$.each(res.access_permission_det,function(a,b){
			$(".access_permission_"+b.access_id).prop('checked',true);
		});
				
		},'json');
	});	

	$("#role_access_link_form").submit(function(){
		var user_role=$("select[name='user_role']").val();
		var access_permission=$("input[name='permission_access[]']:checked").val();

		if(!user_role)
		{
			alert('Please select user role');
			return false;
		}

		if(!access_permission)
		{
			alert('Please select access permission');
			return false;
		}

		if(confirm("Are you sure to create this configuration"))
		{
			return true;
		}else{
			return false;
		}
	});	

	$("input[name='select_all_modules']").click(function(){

		if($(this).attr('checked'))
		{
			$("input[name='permission_access[]']").attr('checked',true);
		}else{
			$("input[name='permission_access[]']").attr('checked',false);

		}
	});

	$("input[name='select_all_acccess']").click(function(){

		var module=$(this).val();
		if($(this).attr('checked'))
		{
			
			$("."+module).attr('checked',true);
		}else{
			$("."+module).attr('checked',false);

		}
	});				
					
</script>
