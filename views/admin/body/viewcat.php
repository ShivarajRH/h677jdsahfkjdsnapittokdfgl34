<style>
.viewbrand h4{
margin-bottom:0px;
}
</style>
<div class="container viewbrand">
	<div class="">
		<h2 style="margin-left:10px;"><?=ucfirst($cat['name'])?> Category Details</h2>
		<ul class="tabs"> 
	        <li rel="brand_det" class="active">Category Details</li>
	    </ul>
	    
	    <div class="tab_container" >
	    	<!------------- Details Blk Start ------------->
	    	<div id="brand_det" class="tabcontent">
	    		<table width="100%">
	    			<tr>
	    				<td> 
				    		<table class="datagrid">
								<thead>
									<tr>
										<th>Category Name</th><th>Main Category</th><th>Associated Brands</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><?=$cat['name']?> <a class="link" href="<?=site_url("admin/editcat/{$cat['id']}")?>" class="link">edit</a></td>
										<td>
											<?php if($cat['type']!=0){?>
												<a class="link" href="<?=site_url("admin/viewcat/{$cat['type']}")?>" class="link"><?=$cat['main']?> </a>
											<?php }else echo 'none';?>
										</td>
										<td>
											<?php
											$brands_res=$this->db->query("select d.brandid,d.catid,b.name as brandname
																			from king_deals d 
																			join king_categories c on c.id=d.catid 
																			join king_brands b on b.id=d.brandid 
																			where c.id=?
																			group by b.name
																			order by b.name asc",$cat['id'])->result_array(); 
											foreach($brands_res as $b){ 
											?>
												<span><a target="_blank" href="<?=site_url("admin/viewbrand/{$b['brandid']}")?>"><?=$b['brandname']?></a> , </span>
											<?php 
											}
											?>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</table>
				<table width="100%">
					<tr>
						<td>
							<h4>Products of <?=$cat['name']?> (<?=count($products)?>)</h4>
							<div class="br_max_height_wrap">
								<table class="datagrid" width="100%">
									<thead>
										<tr>
											<th>Product Name</th>
											<th>MRP</th>
											<th>Barcode</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach($products as $p){?>
											<tr>
												<td><a class="link" href="<?=site_url("admin/editproduct/{$p['product_id']}")?>"><?=$p['product_name']?></a></td>
												<td><?=$p['mrp']?></td>
												<td><?=$p['barcode']?></td>
											</tr>
										<?php }?>
									</tbody>
								</table>
							</div>
						</td>
					
						<td>
							<h4>Vendors for <?=$cat['name']?> (<?=count($vendors)?>)</h4>
							<div class="br_max_height_wrap">
								<table class="datagrid" width="100%">
									<thead>
										<tr>
											<th>Sl No</th>
											<th>Vendor Name</th>
											<th>Brand</th>
										</tr>
									</thead>
									
									<tbody>
										<?php $i=1; foreach($vendors as $v){?>
											<tr>
												<td><?=$i++?></td>
												<td><a class="link" href="<?=site_url("admin/vendor/{$v['vendor_id']}")?>"><?=$v['vendor_name']?></a></td>
												<td><a target="_blank" href="<?=site_url("admin/viewbrand/{$v['brandid']}")?>"><?=$v['name']?></td>
											</tr>
										<?php }?>
									</tbody>
								</table>
							</div>
						</td>
						<td>
							<h4>Deals of <?=$cat['name']?> (<?=count($deals)?>)</h4>
							<div class="br_max_height_wrap">
								<table class="datagrid" width="100%">
									<thead>
										<tr>
											<th>Deal Name</th>
											<th>URL</th>
											<th>MRP</th>
											<th>Price</th>
											<th>Category</th>
										</tr>
									</thead>
									
									<tbody>
										<?php foreach($deals as $p){?>
											<tr>
												<td><a class="link" href="<?=site_url("admin/edit/{$p['dealid']}")?>"><?=$p['name']?></a></td>
												<td><a class="link" href="<?=site_url("{$p['url']}")?>">site</a></td>
												<td><?=$p['orgprice']?></td>
												<td><?=$p['price']?></td>
												<td><a href="<?=site_url("admin/viewcat/{$p['catid']}")?>"><?=$p['category']?></a></td>
											</tr>
										<?php }?>
									</tbody>
								</table>
							</div>
						</td>
					</tr>
				</table>
	    	</div>
	    	<!------------- Details Blk end ------------->
		</div>
	</div>	
</div>
<script>
$(document).ready(function() 
{
	$(".tabcontent:first").show();
	$("ul.tabs li").click(function() 
	{
		$("ul.tabs li").removeClass("active");
		$(this).addClass("active");
		$(".tabcontent").hide();
		var activeTab = $(this).attr("rel"); 
		$("#"+activeTab).fadeIn(); 
	});
	
});
</script>
<?php
