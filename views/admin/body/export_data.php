<style>
.hide
{
	display:none !important;
}
.export_data_wrap
{
	 padding: 20px 6px;
}
.select_filter 
{
    margin: 10px 0;
}
.select_filter span 
{
    display: inline-block;
    font-size: 12px;
    font-weight: bold;
    text-align: right;
    width: 70px;
}
.select_filter select 
{
    width: 180px;
    font-size: 12px;
}
.export_data_wrap select
{
	font-size: 13px;
}
#export_by_opt
{
	background: none repeat scroll 0 0 #f1f1f1;
    padding: 2px;
}
.submit_export
{
	margin: 14px 0;
    padding-left: 116px;
}
</style>
<div class="container">
	<!-- Page Title -->
	<h2>Export Data</h2>
	<div id="exp_tab_view">
		<ul>
			<li><a href="#normal_prd_export" class="normal_prd_export">Normal Product Export</a></li>
			<li><a href="#new_prd_export">Newly Added Products Export</a></li>
			<li><a href="#brand_cat_export" class="brand_cat_export">Brand and Category Export</a></li>
			<li><a href="#bulk_export">Bulk Export</a></li>
		</ul>
		<div id="bulk_export">
	<!-- Form to bulk download of data --- All Menus,All cats,All brands,All products -->
			<form method="post" class="export_data_wrap">
		<input type="hidden" name="filter_type" value="0">
		<select name="type">
			<option value="0">Products</option>
			<option value="1">Brands</option>
			<option value="2">Categories</option>
			<option value="3">Menu</option>
		</select>
				<input type="submit" class="button button-flat-action button-rounded button-tiny" value="Export">
	</form>
		</div>	
		<div id="normal_prd_export">
			<!-- Form to download of data --- products -->
			<form method="post" id="export_by_opt" class="export_data_wrap" style="padding-top: 15px">
		<input type="hidden" name="filter_type" value="1">
		<!-- Menu Filter -->
		<div class="menu select_filter">
					<?php $sit_menu=$this->db->query("select id,name from pnh_menu order by name asc")->result_array(); ?>
					<?php $sk_menu=$this->db->query("select id,name from king_menu order by name asc")->result_array(); ?> 	
					<span>Menu :</span>	<select name="menu" class="sel_menu">
						<option value="0">Choose</option>
								<?php foreach($sit_menu as $m) { ?>
									<option value="<?=$m['id']?>"><?=$m['name']?></option>
								<?php }?>
								<?php foreach($sk_menu as $m) { ?>
							<option value="<?=$m['id']?>"><?=$m['name']?></option>
						<?php }?>	
					</select>
		</div>
		<!-- Brand Filter -->
		<div class="brand select_filter hide">
					<span>Brand :</span> <select name="brand" class="sel_brand">
						</select>
		</div>		
				
				<!-- Category Filter -->		
				<div class="cat select_filter hide">		
					<span>Category :</span> <select name="category" class="sel_cat">
								</select>
				</div>
						
		<!-- Products Filter -->
		<div class="products select_filter hide">		
					<span>Products :</span> <select name="products" class="sel_prd">
						</select>
		</div>									 		
				<div class="submit_export"><input type="submit" value="Export" class="button button-flat-action button-rounded button-tiny export_data" disabled="disabled"></div>		
	</form>
		</div>
		<div id="new_prd_export">
			<!-- Form to bulk download of data --- Newly added products -->
			<form method="post" class="export_data_wrap">
				<input type="hidden" name="filter_type" value="2">
				<select name="days">
					<option value="15">Last 15 Days</option>
					<option value="30">Last 30 Days</option>
					<option value="60">Last 60 Days</option>
					<option value="90">Last 90 Days</option>
				</select>
				<input type="submit" class="button button-flat-action button-rounded button-tiny" value="Export">
			</form>
		</div>	
		<div id="brand_cat_export">
			<!-- Form to download of data --- products -->
			<form method="post" id="export_by_opt" class="export_data_wrap" style="padding-top: 15px">
				<input type="hidden" name="filter_type" value="3">
				<!-- Menu Filter -->
				<div class="menu select_filter">
					<?php $sit_menu=$this->db->query("select id,name from pnh_menu order by name asc")->result_array(); ?>
					<?php $sk_menu=$this->db->query("select id,name from king_menu order by name asc")->result_array(); ?> 	
					<span>Menu :</span>	<select name="menu" class="sel_menu">
								<option value="0">Choose</option>
								<?php foreach($sit_menu as $m) { ?>
									<option value="<?=$m['id']?>"><?=$m['name']?></option>
								<?php }?>
								<?php foreach($sk_menu as $m) { ?>
									<option value="<?=$m['id']?>"><?=$m['name']?></option>
								<?php }?>	
							</select>
				</div>
				<!-- Brand Filter -->
				<div class="brand select_filter hide">
					<span>Brand :</span> <select name="brand" class="sel_brand">
								</select>
				</div>
				
				<!-- Category Filter -->		
				<div class="cat select_filter hide">		
					<span>Category :</span> <select name="category" class="sel_cat1">
												<option value="0">All</option>
											</select>
				</div>
				<div class="submit_export"><input type="submit" value="Export" class="button button-flat-action button-rounded button-tiny export_data" disabled="disabled"></div>		
			</form>
		</div>	
	</div>	
</div>


<script>

$('#exp_tab_view').tabs();
//Menu onchange event - load category list 
$('.sel_menu').live('change',function(){
	var menu=$(this).val();
	$('.cat').addClass('hide');
	$('.brand').addClass('hide');
	$('.products').addClass('hide');
	if(menu!=0)
	{
		$('.export_data').attr('disabled',false);
		$.getJSON(site_url+'admin/jx_getbrandsbymenu/'+menu+'/'+0,'',function(resp){
			var brand_html='';
			if(resp.status=='error')
			{
				alert(resp.message);
			}
			else
			{
				brand_html+='<option value="0">All</option>';
				$.each(resp.brand_list,function(i,b){
					brand_html+='<option value="'+b.brandid+'">'+b.name+'</option>';
				});
			}
			$(".sel_brand").html(brand_html).trigger("liszt:updated");
		});
	    
	    $('.brand').removeClass('hide');
	}
	else
	{
		$('.export_data').attr('disabled',true);
		$('.cat').addClass('hide');
		$('.brand').addClass('hide');
		$('.products').addClass('hide');
		return false;
	}
});

//Category onchange event - load brands list 
$('.sel_brand').live('change',function(){
	var brand=$(this).val();
	
	$.getJSON(site_url+'/admin/jx_load_allcatsbybrand/'+brand,'',function(resp){
		var cat_html='';
		if(resp.status=='error')
		{
			alert(resp.message);
		}
		else
		{
			cat_html+='<option value="0">All</option>';
			$.each(resp.cat_list,function(i,b){
				cat_html+='<option value="'+b.catid+'">'+b.cat_name+'</option>';
			});
		}
		$(".sel_cat").html(cat_html).trigger("liszt:updated");
 	});
 	$('.cat').removeClass('hide');
 	if(brand==0)
 	{
 		$('.cat').addClass('hide');
 	}
});

//Brand onchange event  
$('.sel_cat').live('change',function(){
	$(".sel_prd").html('<option value="0">All</option>');
	$('.products').removeClass('hide');
});

$('#export_by_opt').submit(function(){
	var brand=$('#sel_brand').val();
	if(brand == 0)
	{
		alert("Please Choose brand before proceed");
		return false;
	}
});

$('.normal_prd_export').live('click',function(){
	
	//$('.sel_menu').trigger("change");
	$('.brand').addClass('hide');
	$('.cat').addClass('hide');
	$('.products').addClass('hide');
	$('.export_data').attr('disabled',true);
});
$('.brand_cat_export').live('click',function(){
	
	//$('.sel_menu').trigger("change");
	$('.export_data').attr('disabled',true);
	$('.brand').addClass('hide');
	$('.cat').addClass('hide');
});
</script>