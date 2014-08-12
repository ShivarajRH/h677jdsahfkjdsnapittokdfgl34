<style>
.hide
{
	display:none !important;
}
.select_filter 
{
    margin: 10px 0;
}
.select_filter b 
{
    display: inline-block;
    text-align: right;
    width: 70px;
}
.select_filter select 
{
    width: 180px;
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
	<!-- Form to bulk download of data --- All Menus,All cats,All brands,All products -->
	<form method="post">
		<input type="hidden" name="filter_type" value="0">
		<select name="type">
			<option value="0">Products</option>
			<option value="1">Brands</option>
			<option value="2">Categories</option>
			<option value="3">Menu</option>
		</select>
		<input type="submit" value="Export">
	</form>
	
	<!-- Form to download of data --- Menu-wise categories,brands,products -->
	<form method="post" id="export_by_opt" style="margin-top: 15px">
		<input type="hidden" name="filter_type" value="1">
		<!-- Menu Filter -->
		<div class="menu select_filter">
			<?php $menu=$this->db->query("select id,name from king_menu order by name asc")->result_array() ?> 	
			<b>Menu :</b>	<select name="menu" id="sel_menu">
						<option value="0">Choose</option>
						<?php foreach($menu as $m) { ?>
							<option value="<?=$m['id']?>"><?=$m['name']?></option>
						<?php }?>	
					</select>
		</div>
		<!-- Category Filter -->		
		<div class="cat select_filter hide">		
			<b>Category :</b> <select name="category" id="sel_cat">
						</select>
		</div>
		<!-- Brand Filter -->
		<div class="brand select_filter hide">
			<b>Brand :</b> <select name="brand" id="sel_brand">
						</select>
		</div>		
		<!-- Products Filter -->
		<div class="products select_filter hide">		
			<b>Products :</b> <select name="products" id="sel_prd">
						</select>
		</div>									 		
		<div class="submit_export"><input type="submit" value="Export" class="export_data" disabled="disabled"></div>		
	</form>
</div>


<script>

//Menu onchange event - load category list 
$('#sel_menu').live('change',function(){
	var menu=$(this).val();
	$('.cat').addClass('hide');
	$('.brand').addClass('hide');
	$('.products').addClass('hide');
	if(menu!=0)
	{
		$('.export_data').attr('disabled',false);
		$.getJSON(site_url+'admin/jx_load_allcatsbymenu/'+menu+'/'+0,'',function(resp){
			var cat_html='';
			if(resp.status=='error')
			{
				alert(resp.message);
			}
			else
			{
				cat_html+='<option value="0">All</option>';
				$.each(resp.cat_list,function(i,c){
					cat_html+='<option value="'+c.catid+'">'+c.name+'</option>';
				});
			}
			$("#sel_cat").html(cat_html).trigger("liszt:updated");
		});
	    
	    $('.cat').removeClass('hide');
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
$('#sel_cat').live('change',function(){
	var cat=$(this).val();
	
	$.getJSON(site_url+'/admin/jx_load_allbrandsbycat/'+cat,'',function(resp){
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
		$("#sel_brand").html(brand_html).trigger("liszt:updated");
 	});
 	$('.brand').removeClass('hide');
});

//Brand onchange event  
$('#sel_brand').live('change',function(){
	$("#sel_prd").html('<option value="0">All</option>');
	$('.products').removeClass('hide');
});
</script>