<div class="containe">
	<h2>Config pnh prepaid menus</h2>
	<div>
		<form action="<?php echo site_url('admin/do_config_prepaid_menu');?>" method="post" id="menu_configure_form">
			<table class="datagrid" cellpadding="5" cellspacing="0">
				<thead>
					<tr>
						<th>#</th>
						<th>Menu</th>
						<th>is prepaid</th>
					</tr>
				</thead>
				<tbody>
					<?php if($pnh_menu_list){
						foreach($pnh_menu_list as $i=>$menu)
						{
					?>
						<tr>
							<td><?php echo $i+1; ?></td>
							<td><?php echo $menu['name'];?></td>
							<td><input type="checkbox" name="is_prepaid[]" value="<?php echo $menu['id'];?>" <?php echo ($menu['is_active'])?'checked="checked"':''?>></td>
						</tr>
							
					<?php 	
						}
					}?>
					<tr>
						<td colspan="3" align="right"><input type="submit" value="config"></td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
</div>

<script>
/*$("#menu_configure_form").submit(function(){
	var is_checked=0;
	$("input[name='is_prepaid[]']").each(function(){
		if($(this).is(":checked"))
		{
			is_checked=1;	
		}

	});

	if(!is_checked)
	{
		alert('Please select one menu');
		return false;
	}

	return true;
	
});*/
</script>