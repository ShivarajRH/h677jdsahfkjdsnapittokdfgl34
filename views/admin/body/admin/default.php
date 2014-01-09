<?php 
	$trans_status_flags = $this->config->item('trans_status');
	$order_status_flags = $this->config->item('order_status');
	$status_color_codes = $this->config->item('status_color_codes');	
?>
<head>
<script>
$(function(){
	$(".confirmdeletebrand").click(confirmdeletebrand);
	$(".confirmdeletebrandadmin").click(confirmdeletebrandadmin);
	$(".confirmdeletedeal").click(confirmdeletedeal);
	$(".confirmdeletecategory").click(confirmdeletecategory);
});
function confirmdeletebrand()
{
	if(confirm("Are you sure want to delete this Brand?")==true)
		return true;
	else
		return false;
}
function confirmdeletebrandadmin()
{
	if(confirm("Are you sure want to delete this Brand Admin?")==true)
		return true;
	else
		return false;
}
function confirmdeletedeal()
{
	if(confirm("Are you sure want to delete this Deal?")==true)
		return true;
	else
		return false;
}
function confirmdeletecategory()
{
	if(confirm("Are you sure want to delete this Category?")==true)
		return true;
	else
		return false;
}
</script>
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
	.dash_bar{
	color:#333;
	background:#f1faf1;
	padding:10px;
	font-size:15px;
	margin:5px;
	font-family:arial;
	float:left;
	width:285px;
	}
	.dash_bar .count{
	font-size:18px;
	font-weight:bold;
	}
	.dash_bar a{
	font-size:12px;
	float:right;
	padding:0px 5px;
	}
</style>
</head>
<?php 
//print_r($brandadminlist);
?>
<div class="heading" style="margin-bottom:10px;margin-top: 40px;">
<div class="headingtext container">Dash Board</div>
</div>
<div id="container" class="container" style="clear:both;">

<div class="dash_bar" style="width:920px">
<a href="<?=site_url("admin/orders")?>">View all</a>
<a href="<?=site_url("admin/ordersbystatus/pending")?>">View pending</a>
<span class="count"><?=$pendingorders?></span> pending orders
</div>
	<table class="layout_table" style="clear:both;width: 100%" cellspacing="10">
		<tr>
			<td class="block">
				<div class="block">
					<div class="block_header">Orders
					    <span class="links" style="float: right;font-size: 11px;">		
							<a href="<?=site_url("admin/ordersbystatus/pending")?>">View pending</a> | <a href="<?=site_url("admin/orders")?>">View All</a> 
						</span>	
					</div>
										<table align="center" style="text-align: left;width: 100%" cellpadding="3" cellspacing="0">
										<tr><th>Item name</th><th>Customer</th><th>Status</th><th></th></tr>
<?php $i=1; if($orders!=false) foreach($orders as $order){?>
	<tr><td><?=$order['itemname']?></td><td><?=$order['name']?>
	<td>
<?php 

	echo "<span style='color:".$status_color_codes[$order['status']]."'>".$order_status_flags[$order['status']]."</span>";
	
?>
	</td><td><a href="<?=site_url("admin/vieworder/{$order['id']}")?>">view</a></td></tr>
<?php $i++;}?>
				</table>
<?php if($orders==false) echo "No orders available"?>
				</div>
			</td>
								<td class="block">
				<div class="block">
					<div class="block_header">Deals
					    <span class="links" style="float: right;font-size: 11px;">																	 
						<a href="<?=site_url("admin/adddeal")?>">Add</a> | <a href="<?=site_url('admin/deals')?>">View All</a> 
						</span>	
					</div>
					<table align="center" style="text-align: left;width: 100%" cellpadding="3" cellspacing="0">
							<?php if(isset($dealslist) && $dealslist!=FALSE)
								{?>
						<tr>
							
							<th><b>Deal</b></th>
							<th><b>Status</b></th>
						</tr>
						
								<?php 
								foreach($dealslist as $deallist)
								{
									?>	<tr>
										<td><nobr><?=substr($deallist->tagline,0,40)?></nobr></td>
										<td>
										<?php if($deallist->startdate>time()) echo "inactive"; else if($deallist->enddate>time()) echo "active"; else echo "expired"; ?>
										</td>
										<td><a href="<?=site_url('admin/deal/'.$deallist->dealid)?>">View</a> <a href="<?=site_url('admin/edit/'.$deallist->dealid).'/'.$deallist->catid.'/'.$deallist->brandid?>">Edit</a> 
										</td>
									</tr>	
									<?php } } else {?>
						
						 <td colspan="6" align="center">No Deals Found... </td>
<?php }?>
				</table>
				</div>
			</td>
		</tr>
		<tr>
										<td class="block">
				<div class="block">
					<div class="block_header">Expired Deals
					    <span class="links" style="float: right;font-size: 11px;">																	 
						<a href="<?=site_url('admin/dealsbystatus/expired')?>">View All</a> 
						</span>	
					</div>
					<table align="center" style="text-align: left;width: 100%" cellpadding="3" cellspacing="0">
							<?php if(isset($expdealslist) && $expdealslist!=FALSE)
								{?>
						<tr>
							
							<th><b>Deal</b></th>
							<th><b>Expired on</b></th>
						</tr>
						
								<?php 
								foreach($expdealslist as $deallist)
								{
									?>	<tr>
										<td><nobr><?=substr($deallist->tagline,0,40)?></nobr></td>
										<td>
										<?=date("ga M d",$deallist->enddate)?>
										</td>
										<td><a href="<?=site_url('admin/deal/'.$deallist->dealid)?>">View</a> <a href="<?=site_url('admin/edit/'.$deallist->dealid).'/'.$deallist->catid.'/'.$deallist->brandid?>">Edit</a> 
										</td>
									</tr>	
									<?php } } else {?>
						
						 <td colspan="6" align="center">No deals expired! </td>
<?php }?>
				</table>
				</div>
			</td>
										<td class="block">
				<div class="block">
					<div class="block_header">Sold Out items
					    <span class="links" style="float: right;font-size: 11px;">																	 
						</span>	
					</div>
					<table align="center" style="text-align: left;width: 100%" cellpadding="3" cellspacing="0">
							<?php if(isset($soldoutitems) && $soldoutitems!=FALSE)
								{?>
						<tr>
							<th><b>Item</b></th>
							<th><b>Deal</b></th>
							<th><b>Expires on</b></th>
						</tr>
						
								<?php 
								foreach($soldoutitems as $item)
								{
									?>	<tr>
										<td><?=$item->itemname?></td>
										<td><nobr><?=substr($item->tagline,0,20)?></nobr></td>
										<td>
										<?=date("ga M d",$deallist->enddate)?>
										</td>
										<td><a href="<?=site_url('admin/deal/'.$deallist->dealid)?>">View</a> <a href="<?=site_url('admin/edit/'.$deallist->dealid).'/'.$deallist->catid.'/'.$deallist->brandid?>">Edit</a> 
										</td>
									</tr>	
									<?php } } else {?>
						
						 <td colspan="6" align="center">No active deals sold out! </td>
<?php }?>
				</table>
				</div>
			</td>
		</tr>
	</table>
</div>