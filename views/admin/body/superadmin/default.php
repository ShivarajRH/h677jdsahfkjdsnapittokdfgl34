<style>
#container{
font-size: 12px;
font-family: arial;
}
.layout_table{
font-size:13px;
}
	div.block{
//		background: #f1f1f1;
		height:100%;
		padding:5px;
	}	
	div.block_header{
//		background: silver;
		padding: 3px;
		font-weight: bold;
		font-size: 14px;
	}
	div.block_body{
		padding: 5px;
		
	}
	#statistics {
		width: 88%;
	}
	#statistics li{
		padding: 3px;
		background: #fffff0;
		margin-bottom: 1px;
	}
	.total_count{
		float: right;
		margin-right: 10px; 
	}
	#container .layout_table td.block{
		width: 50%;
		background:#fff url(<?=base_url()?>/images/bg.gif) repeat-x;
		border:1px solid #ccc;
		-moz-border-radius:10px;
	}
	.layout_table td.block div.block_header a{
	font-size:11px;
	}
	.layout_table td.block a{
	padding-right:5px;
	font-size:10px;
	}
	.boxtype{
		padding:1px;
		background: #fffff0;
	}
	.boxtype:hover{
	 	color:#555;
		background:#b4defe;
	}
	b{
	font-size: 12px;
	font-family: arial;
	}
</style>
<div class="heading" style="margin-bottom:10px;margin-top: 40px;">
<div class="headingtext container">
Dash Board</div>
</div>
<div id="container" class="container" style="clear:both;">

<div class="dash_bar">
<a href="<?=site_url("admin/ordersbystatus/pending")?>">View</a>
<span class="count"><?=$pendingorders?></span> pending orders
</div>

<div class="dash_bar">
<a href="<?=site_url("admin/dealsbystatus/unpublished")?>">View</a>
<span class="count"><?=$unpublisheddeals?></span> unpublished deals
</div>

<div class="dash_bar">
<a href="<?=site_url("admin/commentsbystatus/new")?>">View</a>
<span class="count"><?=$newcomments?></span> new comments
</div>
<div class="dash_bar" style="float:right;">
<a href="<?=site_url("admin/pricereqs/pending")?>">View</a>
<span class="count"><?=$pricereqs?></span> price requests
</div>

	<table class="layout_table" style="clear:both;width: 100%" cellspacing="10">
		<tr>
			<td class="block">
				<div class="block">
					<div class="block_header">Recent Activity
					    <span class="links" style="float: right;font-size: 11px;">		
							<a href="<?=site_url("admin/activity")?>">View All</a> 
						</span>	
					</div>
										<table align="center" style="text-align: left;width: 100%" cellpadding="3" cellspacing="0">
<?php $i=1; foreach($activity as $act){?>
	<tr><td><?=$i?>. <?=substr($act['msg'],0,20)?> by <b><?=$act['name']?></b> on <?=date("g:i a d/m",$act['time'])?> <a href="<?=site_url("admin/viewactivity/".$act['id'])?>">view</a></td></tr>
<?php $i++;}?>
				</table>
				</div>
			</td>
			<td class="block">
				<div class="block">
					<div class="block_header">Brands
					    <span class="links" style="float: right;font-size: 11px;">
					    <a href="<?=site_url('admin/addbrand')?>">Add</a>
					    |																	 
							<a href="<?=site_url('admin/brands')?>">View All</a> 
						</span>	
					</div>
					<table align="center" style="text-align: left;width: 100%" cellpadding="3" cellspacing="0">
						<?php if(isset($brandadminlist) && $brandadminlist!=FALSE)
								{?>
						<tr>
							
							<th><b>Brand Name</b></th>
						</tr>
						
							<?php 
								foreach($brandadminlist as $brandadmin)
								{
									?>	<tr>
										<td><?=$brandadmin->name?></td>
										<td align="right" style="font-size:10px;"><a href="<?=site_url("admin/editbrand/{$brandadmin->id}")?>">Edit</a> </td>
									</tr>	
									<?php } }else {?>
						
						 <td colspan="6" align="center">No Brands... </td>
						<?php }?>
			</table>
				</div>
			</td>
		</tr>
		<tr>
					<td class="block">
				<div class="block">
					<div class="block_header">Categories
					    <span class="links" style="float: right;font-size: 11px;">																	 
							<a href="<?=site_url('admin/categories')?>">View All</a> 
						</span>	
					</div>
					<table align="center" style="text-align: left;width: 100%" cellpadding="3" cellspacing="0">
						<tr>
							
							<th><b>Category Name</b></th>
						</tr>
						
								<?php if(isset($categories) && $categories!=FALSE)
								{
								foreach($categories as $category)
								{
									?>	<tr>
										<td><?=$category->name?></td>
										<td><a href="<?=site_url("admin/categories/".$category->id)?>">Edit</a></td>
									</tr>	
									<?php } }else {?>
						
						 <td colspan="6" align="center">No Categories Found... </td>
						 <?php }?>

			</table>
				</div>
			</td>
								<td class="block">
				<div class="block">
					<div class="block_header">Deals
					    <span class="links" style="float: right;font-size: 11px;">																	 
							<a href="<?=site_url('admin/deals')?>">View All</a> 
						</span>	
					</div>
					<table align="center" style="text-align: left;width: 100%" cellpadding="3" cellspacing="0">
							<?php if(isset($dealslist) && $dealslist!=FALSE)
								{?>
						<tr>
							
							<th><b>Deal</b></th>
							<th><b>Brand Name</b></th>
						</tr>
						
								<?php 
								foreach($dealslist as $deallist)
								{
									?>	<tr>
										<td><nobr><?=substr($deallist->tagline,0,40)?></nobr></td>
										<td><?=$deallist->brandname?></td>
										<td><a href="<?=site_url('admin/deal/'.$deallist->dealid)?>">View</a> <a href="<?=site_url('admin/edit/'.$deallist->dealid)?>">Edit</a> 
										</td>
									</tr>	
									<?php } } else {?>
						
						 <td colspan="6" align="center">No Deals Found... </td>
<?php }?>
				</table>
				</div>
			</td>
		</tr>
	</table>
</div>