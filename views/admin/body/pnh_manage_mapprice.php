<div class="container page_wrap">
	<h2>Manage Minimum Advertising Price Brands</h2>
	<div class="">
		<div class="form">
			<form action="" id="frm_mapprice" method="post">
				<table>
					<tr>
						<td><b>Menu</b></td>
						<td>
							<select name="menuid">
								<option value="">Choose</option>
								<?php
									foreach($menu_list as $m)
									{
								?>
										<option value="<?php echo $m['id'];?>"><?php echo $m['name'];?></option>
								<?php		
									}
								?>
							</select>
						</td>
						<td>
							<select name="brandid">
								<option value="0">Choose</option>
							</select>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('select[name="menuid"]').change(function(){
		var menuid = $(this).val();
			if(menuid)
			{
				$('select[name="brandid"]').html("<option value=''>Loading...</option>");
				$.post(site_url+'/admin/jx_getbrandsbymenuid',{menuid:menuid},function(resp){
					var optList = '<option value="0">All</option>';
						if(resp.status != 'error')
						{
							optList += '<option value="0">All</option>';
							$.each(resp.brand_list,function(i,b){
								optList += '<option value="'+b.id+'">'+b.name+'</option>';
							});
						}
						$('select[name="brandid"]').html(optList);
				});
			}else
			{
				$('select[name="brandid"]').html("<option value=''>Choose</option>");
			}
	});
	
	$('#frm_mapprice').submit(function(){
		var menuid = $('select[name="menuid"]').val()*1;
		var brandid = $('select[name="brandid"]').val()*1;
			
			if(isNaN(menuid) ||  menuid == 0)
			{
				alert("Please Choose Menu");
				return false;	
			}
		
			if(isNaN(brandid))
			{
				alert("Please Choose Brand");
				return false;
			}
			
			if(brandid == 0)
				if(!confirm("Are you sure want to create this config with All Brands in the Menu"))
					return false;
					
	});
</script>
