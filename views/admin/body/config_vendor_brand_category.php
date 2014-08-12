<div class="page_wrap container">
	<div class="clearboth">
		<div class="fl_left" >
			<h2 class="page_title">Manage vendor categories/brands</h2>
		</div>
	</div>
	
	<div class="page_topbar">
		<div class="page_topbar_left fl_left" >
		</div>
		<div class="page_action_buttonss fl_right" align="right">
		</div>
	</div>
	
	<div style="clear:both">&nbsp;</div>
	
	<div class="page_content">
		<div class="tab_view tab_view_inner">
			<ul>
				<li ref="menu" ><a href="#menu" class="menu_tab" >Menu</a></li>
				<li ref="brand" ><a href="#brands" class="brand_tab" >Brands</a></li>
				<li ref="cat" ><a href="#category" class="cat_tab" >Category</a></li>
			</ul>
		
			<div id="brands">
				<div class="page_topbar">
					<div class="page_topbar_left fl_left" >
						<b>Total:</b><?php echo $brand_ttl;?>
					</div>
					<div class="page_action_buttonss fl_right" align="right">
						Show Brands starts with: 
						<select class="alphasrt">
							<option value="0">All</option>
							<option value="NUM">0-9</option>
							<?php 
								for($i=65;$i<91;$i++)
									echo '<option value="'.chr($i).'">'.chr($i).'</option>';
							?>
						</select>
					</div>
				</div>
				<div style="clear:both">
				<?php 
					if($brand_det)
					{
						?>
						<table width="100%" cellpadding="5" cellspacing="0" class="datagrid">
							<thead>
								<tr>
									<th width="3%">Si</th>
									<th width="10%">Vendor brand</th>
									<th width="30%">Link brand</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<?php 
									foreach($brand_det as $i=>$b)
									{
										?>
										<tr>
											<td><?php echo ($i+1); ?></td>
											<td><?php echo  $b['client_brand']; ?></td>
											<td>
												<?php 
													$show_txt='';
													if($b['brand_id']==0)
													{
														
													}else{
														echo '<span>'.$b['e_brand'].'</span>';
														$show_txt='hidden';
													}
												?>
												<div class="<?php echo $show_txt ?> linkking_block">
													<div class="txt_block fl_left">
															<input type="text" name="e_brand" value="" brand_id="" class="src_brand" log_id="<?php echo $b['id'];?>">
															<div class="search_list <?php echo $b['id']?>_brand_list brand_list" class="closeonclick"></div>
															<input type="hidden" name="c_brand_lodId" value="<?php echo $b['id']; ?>" >
														</div>
														<div class="msg fl_left" style="display:none;">
															<img src="<?=IMAGES_URL?>loader_gold.gif" style="float:right">
														</div>
														<div style="clear:both"></div> 
												</div>	
											</td>
											<td>
												<?php if($b['brand_id']!=0){?>
												<a href="javascript:void(0)" type="brand" log_id="<?php echo $b['id'];?>" class="remove_config">delete</a>
												<?php }?>
											</td>
										</tr>
										<?php 
									}
								?>
								
								<tr>
									<td class="pagination" colspan="4" align="right"><?php echo $brand_pag;?></td>
								</tr>
							</tbody>
						</table>
						<?php 
					}else{
						echo '<h3 aling="center">No brands found</h3>';
					}?>
					</div>
				
			</div>
			
			<div id="category">
				<div class="page_topbar">
					<div class="page_topbar_left fl_left" >
						<b>Total:</b><?php echo $cat_ttl;?>
					</div>
					<div class="page_action_buttonss fl_right" align="right">
						Show Categories starts with: 
						<select class="alphasrt">
							<option value="">All</option>
							<option value="NUM">0-9</option>
							<?php 
								for($i=65;$i<91;$i++)
									echo '<option value="'.chr($i).'">'.chr($i).'</option>';
							?>
						</select>
					</div>
				</div>
				<div style="clear:both">
				<?php 
					if($cat_det)
					{
						?>
						<table width="100%" cellpadding="5" cellspacing="0" class="datagrid">
							<thead>
								<tr>
									<th width="3%">Si</th>
									<th width="10%">Main category</th>
									<th width="10%">Vendor category</th>
									<th width="30%">Link category</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<?php 
									foreach($cat_det as $i=>$c)
									{
										?>
										<tr>
											<td><?php echo ($i+1); ?></td>
											<td><?php echo  ($c['main_category'])?$c['main_category']:'-NA-'; ?></td>
											<td><?php echo  $c['client_category']; ?></td>
											<td>
												<?php 
													$show_txt='';
													if($c['category_id']==0)
													{
														
													}else{
														echo '<span>'.$c['e_cat'].'</span>';
														$show_txt="hidden";
													}
												?>
												
												<div class="<?php echo $show_txt; ?> linkking_block">
													<div class="txt_block fl_left">
															<input type="text" name="e_cat" value="" cat_id="" class="src_cat" log_id="<?php echo $c['id'];?>">
															<div class="search_list <?php echo $c['id']?>_cat_list cat_list" class="closeonclick"></div>
															<input type="hidden" name="c_cat_lodId" value="<?php echo $c['id']; ?>" >
														</div>
														<div class="msg fl_left" style="display:none;">
															<img src="<?=IMAGES_URL?>loader_gold.gif" style="float:right">
														</div>
													 	<div style="clear:both"></div>
												</div>
											</td>
											<td>
												<?php if($c['category_id']!=0){?>
												<a href="javascript:void(0)" type="cat" log_id="<?php echo $c['id'];?>" class="remove_config">delete</a>
												<?php }?>
											</td>
										</tr>
										<?php 
									}
								?>
								
								<tr>
									<td class="pagination" colspan="4" align="right"><?php echo $cat_pag;?></td>
								</tr>
							</tbody>
						</table>
						<?php 
					}else{
						echo '<h3 aling="center">No Categories found</h3>';
					}
				?>
				</div>
			</div>
			
			<div id="menu">
				<div class="page_topbar">
					<div class="page_topbar_left fl_left" >
						<b>Total:</b><?php echo $menu_ttl;?>
					</div>
					<div class="page_action_buttonss fl_right" align="right">
					</div>
				</div>
				<div style="clear:both">&nbsp;</div>
				<?php 
					if($menu_det)
					{
						?>
						<table width="100%" cellpadding="5" cellspacing="0" class="datagrid">
							<thead>
								<tr>
									<th>Si</th>
									<th>Vendor menu</th>
									<th>Link menu</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<?php 
									foreach($menu_det as $i=>$m)
									{
										?>
										<tr>
											<td><?php echo ($i+1); ?></td>
											<td><?php echo  $m['client_menu']; ?></td>
											<td>
												<?php 
													$show_txt='';
													if($m['menu_id']==0)
													{
														?>
														
														<?php 
													}else{
														echo '<span>'.$m['e_menu'].'</span>';
														$show_txt="hidden";
													}
												?>
												
												<div class="<?php echo $show_txt; ?> linkking_block">
													<div class="txt_block fl_left">
															<input type="text" name="e_menu" value="" menu_id="" class="src_menu" log_id="<?php echo $m['id'];?>">
															<div class="search_list <?php echo $m['id']?>_menu_list menu_list" class="closeonclick"></div>
															<input type="hidden" name="c_menu_lodId" value="<?php echo $m['id']; ?>" >
														</div>
														<div class="msg fl_left" style="display:none;">
															<img src="<?=IMAGES_URL?>loader_gold.gif" style="float:right">
														</div>
														<div style="clear:both"></div>
												</div>
											</td>
											<td>
												<?php 
													if($m['menu_id']!=0)
													{?>
														<a href="javascript:void(0)" type="menu" log_id="<?php echo $m['id'];?>" class="remove_config">delete</a>
												<?php }?>
											</td>
										</tr>
										<?php 
									}
								?>
								
								<tr>
									<td class="pagination" colspan="4" align="right"><?php echo $menu_pag;?></td>
								</tr>
							</tbody>
						</table>
						<?php 
					}else{
						echo '<div aling="center">No Menus found</div>';
					}
				?>
			</div>
		</div>
	</div>
</div>

<style>
.search_list{
	display:none;
	position:absolute;
	width:230px;
	max-height:230px;
	overflow:auto;
	background:#eee;
	border:1px solid #aaa;
}

.search_list a{
	display:block;
	padding:5px;
}
.search_list a:hover{
	background:blue;
	color:#fff;
}

.msg{
	border:1px dashed #aaa;
	width:
}

.hidden{
	display:none;
}
</style>

<script>
	$('.tab_view').tabs();
	

	var type='<?php echo $type; ?>';
	$("."+type+"_tab").click();

	$(".src_brand").keyup(function(){
		var jHR=0,search_timer=0;
		q=$(this).val();
		var src_brands_class='';
		src_brands_class="."+$(this).attr("log_id")+"_brand_list";
		if(q.length<3)
			return true;
		if(jHR!=0)
			jHR.abort();
		window.clearTimeout(search_timer);
		search_timer=window.setTimeout(function(){
		jHR=$.post("<?=site_url("admin/jx_brand")?>",{q:q},function(data){
			$(src_brands_class).html(data).show();
		});
		},200);
	}).focus(function(){
		if($(src_brands_class).length==0)
			return;
		$(src_brands_class).show();
	}).click(function(e){
		e.stopPropagation();
	});

	/**
	function brand linking
	*/

	$(".brand_list a").live("click",function(){

		$(".search_list").hide();
		var brand_id=$(this).attr("brand_id");
		var brand_name=$(this).attr("brand_name");
		var parent_ele=$(this).closest('td');
		var log_id=$('input[name="c_brand_lodId"]',parent_ele).val();
		$('input[type="text"]',parent_ele).val(brand_name);
		$(".msg",parent_ele).show();
		$.post(site_url+'/admin/jx_link_api_vendor_brand',{log_id:log_id,brand_id:brand_id},function(data){
				if(data.status='error')
				{
					$(".msg",parent_ele).text("Not updated");
					$(".msg",parent_ele).hide();
				}else{
					$(".msg",parent_ele).text("Updated");
					$(".msg",parent_ele).hide();
				}
		},'json');
			
	});	

	//get category list
	$(".src_cat").keyup(function(){
		var jHR=0,search_timer=0;
		q=$(this).val();
		var src_cat_class='';
		src_cat_class="."+$(this).attr("log_id")+"_cat_list";
		if(q.length<3)
			return true;
		if(jHR!=0)
			jHR.abort();
		window.clearTimeout(search_timer);
		search_timer=window.setTimeout(function(){
		jHR=$.post("<?=site_url("admin/jx_category")?>",{q:q},function(data){
			$(src_cat_class).html(data).show();
		});
		},200);
	}).focus(function(){
		if($(src_cat_class).length==0)
			return;
		$(src_cat_class).show();
	}).click(function(e){
		e.stopPropagation();
	});

	/**
	function category linking
	*/

	$(".cat_list a").live("click",function(){

		$(".search_list").hide();
		var cat_id=$(this).attr("cat_id");
		var cat_name=$(this).attr("cat_name");
		var parent_ele=$(this).closest('td');
		var log_id=$('input[name="c_cat_lodId"]',parent_ele).val();
		$('input[type="text"]',parent_ele).val(cat_name);
		$(".msg",parent_ele).show();
		$.post(site_url+'/admin/jx_link_api_vendor_cat',{log_id:log_id,cat_id:cat_id},function(data){
				if(data.status='error')
				{
					$(".msg",parent_ele).text("Not updated");
					$(".msg",parent_ele).hide();
				}else{
					$(".msg",parent_ele).text("Updated");
					$(".msg",parent_ele).hide();
				}
		},'json');
			
	});	


	//get menu list
	$(".src_menu").keyup(function(){
		var jHR=0,search_timer=0;
		q=$(this).val();
		var src_menu_class='';
		src_menu_class="."+$(this).attr("log_id")+"_menu_list";
		if(q.length<3)
			return true;
		if(jHR!=0)
			jHR.abort();
		window.clearTimeout(search_timer);
		search_timer=window.setTimeout(function(){
		jHR=$.post("<?=site_url("admin/jx_menu")?>",{q:q},function(data){
			$(src_menu_class).html(data).show();
		});
		},200);
	}).focus(function(){
		if($(src_menu_class).length==0)
			return;
		$(src_menu_class).show();
	}).click(function(e){
		e.stopPropagation();
	});

	/**
	function menu linking
	*/

	$(".menu_list a").live("click",function(){

		$(".search_list").hide();
		var menu_id=$(this).attr("menu_id");
		var menu_name=$(this).attr("menu_name");
		var parent_ele=$(this).closest('td');
		var log_id=$('input[name="c_menu_lodId"]',parent_ele).val();
		$('input[type="text"]',parent_ele).val(menu_name);
		$(".msg",parent_ele).show();
		$.post(site_url+'/admin/jx_link_api_vendor_menu',{log_id:log_id,menu_id:menu_id},function(data){
				if(data.status='error')
				{
					$(".msg",parent_ele).text("Not updated");
					$(".msg",parent_ele).hide();
				}else{
					$(".msg",parent_ele).text("Updated");
					$(".msg",parent_ele).hide();
				}
		},'json');
			
	});	

	$('.alphasrt').change(function(){

		sel_tab = $('li.ui-state-active').attr('ref'); 
		location.href = site_url+'/admin/config_vendor_brand_cat_link/'+sel_tab+'/'+$(this).val()+'/0'; 
	}).val('<?php echo $alpha?>');

	$(function(){
		$('.alphasrt:visible').val('<?php echo $alpha?>');
	});
	
	//remove config
	$(".remove_config").click(function(){
		var t=$(this).attr("type");
		var log_id=$(this).attr("log_id");
		var parent_ele=$(this).closest('tr');
		var html_tm='';
		
		if(t=='brand')
		{
			$.post(site_url+'/admin/jx_link_api_vendor_brand/',{brand_id:0,log_id:log_id},function(resp){
				if(resp.status=='success')
				{
					$("td:nth-child(3) span",parent_ele).text('');
					$(".linkking_block",parent_ele).removeClass('hidden');
					$("td:nth-child(4)",parent_ele).html('');
				}
			},'json');
		}

		if(t=='cat')
		{
			$.post(site_url+'/admin/jx_link_api_vendor_cat/',{cat_id:0,log_id:log_id},function(resp){
				if(resp.status=='success')
				{
					$("td:nth-child(3) span",parent_ele).text('');
					$(".linkking_block",parent_ele).removeClass('hidden');
					$("td:nth-child(4)",parent_ele).html('');
				}
			},'json');
		}

		if(t=='menu')
		{
			$.post(site_url+'/admin/jx_link_api_vendor_menu/',{menu_id:0,log_id:log_id},function(resp){
				if(resp.status=='success')
				{
					$("td:nth-child(3) span",parent_ele).text('');
					$(".linkking_block",parent_ele).removeClass('hidden');
					$("td:nth-child(4)",parent_ele).html('');
				}
			},'json');
		}
	});
	
	
</script>
