<div class="page_wrap container">
	<div class="clearboth">
		<div class="fl_left" >
			<h2 class="page_title">Config Maximum orders for deal</h2>
		</div>
		<div class="fl_right stats" >
		</div>
	</div>
	
	<div class="page_topbar" >
		<div class="page_topbar_left fl_left" >
			<span class="total_overview"></span>
		</div>
		<div class="page_action_buttonss fl_right" align="right">
		</div>
	</div>
	
	<div style="clear:both">&nbsp;</div>
	
	<div class="page_content">
		<table width="100%">
			<tr>
				<td width="30%" >
					<div class="form" style="background: #fafafa;margin-right:20px;padding:10px;">
						<form action="<?php echo site_url('admin/pnh_process_config_deal_max_order')?>" method="post" id="config_form">
							<table cellpadding="10" cellspacing="0" border="0" style="border-collapse: collapse">
								<tr>
									<td><b>Menu : </b><span class="red_star">*</span></td>
										
									<td>
										<select name="menu" style="width: 200px;" id="menu_list">
											<option value="">Choose</option>
											<?php 
												if($menu_list)
												{
													foreach($menu_list as $menu)
													{
														?>
														<option value="<?php echo $menu['id']; ?>"><?php echo $menu['name']; ?></option>
														<?php 
													}
												}
											?>
										</select>
									</td>
								</tr>
								<tr>
									<td><b>Category : </b></td>
									<td>
										<select name="category" style="width: 200px;" id="cat_list">
											<option value="">All</option>
										</select>
									</td>
									
								</tr>
								<tr>
									<td><b>Brand : </b></td>
									<td>
										<select name="brand" style="width: 200px;" id="brand_list">
											<option value=''>All</option>
										</select>
									</td>
								</tr>
								<tr>
									<td><b>Qty</b></td>
									<td><input type="text" name="qty" style="width: 60px;" id="rq_qty"></td>
								</tr>
								<tr>
									<td colspan="2" align="right"><input type="submit" value="submit" class="button button-flat-royal button-small button-rounded" ></td>
								</tr>
							</table>
						</form>
					</div>
				</td>
				<td width="70%"> 
					<div>
						<table class="datagrid" cellpadding="5" cellspacing="0" width="100%">
							<thead>
								<tr>
									<th>#</th>
									<th>Menu</th>
									<th>Category</th>
									<th>Brand</th>
									<th>Order qty</th>
									<th>Created by</th>
									<th>Created on</th>
								</tr>
							</thead>
							<tbody>
								<?php if($config_upt_log)
								{
									foreach($config_upt_log as $i=>$log)
									{
										?>
										<tr>
											<td><?php echo $i+1; ?></td>
											<td><?php echo $log['menu']; ?></td>
											<td><?php echo $log['cat']; ?></td>
											<td><?php echo $log['brand']; ?></td>
											<td><?php echo $log['qty']; ?></td>
											<td><?php echo $log['username']; ?></td>
											<td><?php echo format_datetime($log['created_on']); ?></td>
										</tr>
										<?php
									}
									?>
										<tr>
											<td colspan="7" align="right" class="pagination"><?php echo $pagination;?></td>
										</tr>
									<?php
									
								}else{
									echo '<tr><td align="center">No data found</td></tr>';
								}?>
								
							</tbody>
						</table>
					</div>
				</td>
			</tr>
		</table>
	</div>
</div>

<style>
.leftcont{display:none;}
</style>

<script>
	$("#menu_list").change(function(){
		var menu_id=$(this).val();
		var cat_list='';

		$("#brand_list").html("<option value=''>All</option>");
		$("#cat_list").html("<option value=''>All</option>");
		if(menu_id)
		{
			$.post(site_url+"/admin/jx_load_allcatsbymenu/"+menu_id,{},function(res){
				if(res.status=='error')
				{
					alert(res.message);
					return;
				}else{
					cat_list+="<option value='0'>All</option>"
					$.each(res.cat_list,function(a,b){
						cat_list+="<option value='"+b.catid+"'>"+b.name+"</option>";
					});
					$("#cat_list").html(cat_list);
				}
			},'json');
		}
	});	


	$("#cat_list").change(function(){
		var cat_id=$("#cat_list").val()?$("#cat_list").val():0;
		var menu_id=$("#menu_list").val()?$("#menu_list").val():0;
		var brand_list='';
		$("#brand_list").html("<option value=''>All</option>");
		
		if(!cat_id)
		{
			alert("Please select category");
			return false;
		}else if(!menu_id)
		{
			alert("Please select menu");
			return false;
		}

		$.post(site_url+"/admin/jx_load_allbrandsbymenucat/"+menu_id+"/"+cat_id,{},function(res){
			if(res.status=='error')
			{
				alert(res.message);
				return;
			}else{
				brand_list+="<option value='0'>All</option>";
				$.each(res.cat_list,function(a,b){
					brand_list+="<option value='"+b.brandid+"'>"+b.brandname+"</option>";
				});
				$("#brand_list").html(brand_list);
			}
		},'json');
		
	});	

	$("#config_form").submit(function(){
		var menue=$("#menu_list").val();
		var rq_qty=$("#rq_qty").val();

		if(menue=='' || menue.length==0)
		{
			alert("Please select menu");
			return false;
		}

		if(rq_qty=='' || rq_qty==0 || rq_qty.length==0)
		{
			alert("Please enter qty");
			return false;
		}

		if(confirm("Do you want to create this config"))
		{
			return true;
		}else{
			return false;
		}
	});								
</script>
