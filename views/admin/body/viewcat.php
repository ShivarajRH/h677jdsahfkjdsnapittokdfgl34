<!-- styles needed by jScrollPane -->
<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/custom_scrollbar/jquery.jscrollpane.css">

<style>
/**************************************** Brands CSS ********************************************************/
/******** General CSS start********************/
.mod_widget_small
{
	float:left;
	width:34%;
	min-width:34%;
	margin-right:2%;
}
.mod_widget_large
{
	float:left;
	width:64%;
}
.mod_widget_sub
{
	float:left;
	width:50%;
}
.mod_widget_title
{
	background: none repeat scroll 0 0 #D7E2EE;
    border: 1px solid #C7C3C0;
    width:100%;
}
.mod_widget_content
{
	background: none repeat scroll 0 0 #fcfcfc;
    border-bottom: 1px solid #C7C3C0;
    border-left: 1px solid #C7C3C0;
    border-right: 1px solid #C7C3C0;
    height:200px;
    width:100% !important;
}
.mod_widget_content ul
{
	list-style-type:none;
	color:#1D1D1D;
}
.mod_widget_content li
{
	border-bottom: 1px solid #D2D1CF;
    font-size: 12px;
    font-weight: bold;
    height: 18px;
}
.mod_widget_small .mod_widget_content li
{
	margin: 0 2%;
    padding: 2%;
}
.mod_widget_large .mod_widget_content li
{
	margin: 0 1%;
    padding: 1.2%;
}
.mod_widget_title div.heading_wrap
{
	font-size: 15px;
	font-weight: bold;
	color: #606060;
	height:37px;
}
.mod_widget_title img
{
	margin:8px 8px -1px 11px;
}
.mod_widget_small .mod_widget_sub .mod_widget_content ul
{
	margin-top: 8px;
	list-style-type:none;
	color:#1D1D1D;
}
.mod_widget_small .mod_widget_sub .mod_widget_content li
{
    border-bottom: 1px solid #D2D1CF;
    font-size: 12px;
    font-weight: bold;
    height: 28px;
    margin: 4% 4% 0;
    padding: 0 7%;
}
.mod_widget_small .mod_widget_sub .mod_widget_content span
{
	margin-left:5px;
	text-transform:capitalize;
}
.mod_widget_content span
{
	margin-left:4px;
	text-transform:capitalize;
}
.mod_widget_row
{
	width:100%;margin-top:2%;
}
/******** General CSS end *********************/
/********** Custom CSS ********************/
.mrp_wrap
{
	color: #FF0000;
    font-size: 11px;
    padding: 0 10px;
    text-align: left;
    width: 9%;
}
.price_wrap
{
	color: #0B0B0E;
    font-size: 11px;
    font-weight: bold;
    text-align: left;
    width: 9%;
}
.barcode_wrap
{
 	color: #0B0B0E;
    font-size: 11px;
    font-weight: bold;
    text-align: left;
    width: 17%;
}
.ttl_count
{
	 color: #000000;
    font-size: 12px;
    padding: 12px 6px 6px 8px;
}
</style>
<script>
$(function()
{
	//Applied custom scrollbar 
	$('.border_content_right_blk').jScrollPane();
	$('.border_content_left_blk').jScrollPane();
	$('.mod_widget_content').jScrollPane();
});
</script>

<div class="container outer_blk">
	<!-- -->
	<!----------------------------------Page Title--------------------------->
	<h2> Category - <?=ucfirst($cat['name'])?><a style="margin-left:5px" href="<?=site_url("admin/editcat/{$cat['id']}")?>" target="_blank"><img src="<?php echo base_url().'images/pencil.png'?>"></a></h2>
	
	<div class="mod_widget_small">
		<!----------------------------------Brand information block--------------------------->
		<div class="mod_widget_sub">
			<div class="mod_widget_title">
				<div class="heading_wrap">
					<img class="sub_blk_img" src="<?php echo base_url().'images/home2.png'?>">Brands
					<?php
						$brands_res=$this->db->query("select d.brandid,d.catid,b.name as brandname
													from king_deals d 
													join king_categories c on c.id=d.catid 
													join king_brands b on b.id=d.brandid 
													where c.id=?
													group by b.name
													order by b.name asc",$cat['id'])->result_array();
					?>
					<span class="fl_right ttl_count">Total : <?=count($brands_res)?></span>
				</div>
			</div>
			<div class="mod_widget_content">
				<ul>
					<?php $i=1;foreach($brands_res as $b){ ?>
						<li>
							 <?=$i++?> <span><a target="_blank" href="<?=site_url("admin/viewbrand/{$b['brandid']}")?>"><?=$b['brandname']?></a></span>
						</li>
					<?php } ?>
				</ul>
			</div>
		</div>
		<!----------------------------------Attribute information block--------------------------->
		<div class="mod_widget_sub">
			<div class="mod_widget_title">
				<div class="heading_wrap">
					<img class="sub_blk_img" src="<?php echo base_url().'images/home2.png'?>">Attributes
					<span class="fl_right ttl_count">Total : <?=count($categories)?></span>
				</div>
			</div>
			 <?php
                   $arr_attributes = $this->db->query("select * from m_attributes where FIND_IN_SET(id, '".$cat['attribute_ids']."') order by attr_name asc limit 100")->result_array();
             ?>
			<div class="mod_widget_content">
				<ul>
					<?php 
						if($arr_attributes)
						{
							$k=1;foreach($arr_attributes as $arr){
					?>
						<li>
							 <?=$k++?> <span><?=$arr['attr_name'];?></span>
						</li>
					<?php 
							} 
						}else
						{
					?>	
						<li>
							 <span>No Attributes</span>
						</li>
					<?php		
							}
					?>
				</ul>
			</div>
		</div>
	</div>
	
	<!----------------------------------Deals information block--------------------------->
	<div class="mod_widget_large">
		<div class="mod_widget_title">
			<div class="heading_wrap">
				<img src="<?php echo base_url().'images/home2.png'?>">Deals
				<span class="fl_right ttl_count">Total : <?=count($deals)?></span>
			</div>
		</div>
		<div class="mod_widget_content">
			<ul>
				<?php $k=1;foreach($deals as $p){?>
					<li>
						 <?=$k++?> <span><a class="link" target="_blank" href="<?=site_url("admin/edit/{$p['dealid']}")?>"><?=$p['name']?></a></span>
						 <span class="fl_right mrp_wrap">MRP : <?=$p['orgprice']?></span>
						 <span class="fl_right price_wrap">Price : <?=$p['price']?></span>
					</li>
				<?php } ?>
			</ul>
		</div>
	</div>
	
	<div class="fl_left mod_widget_row" style="">
		<!----------------------------------Vendors information block--------------------------->
		<div class="mod_widget_small">
			<div class="mod_widget_title">
				<div class="heading_wrap"><img src="<?php echo base_url().'images/home2.png'?>">Vendors
					<span class="fl_right ttl_count">Total : <?=count($vendors)?></span>
				</div>
			</div>
			<div class="mod_widget_content max_height_wrap">
				<ul>
					<?php $k=1;foreach($vendors as $v){?>
						<li>
							 <?=$k++?> <span><a class="link" target="_blank" href="<?=site_url("admin/vendor/{$v['vendor_id']}")?>"><?=$v['vendor_name']?></a></span>
							<span class="fl_right">Brand : <a target="_blank" href="<?=site_url("admin/viewbrand/{$v['brandid']}")?>"><?=$v['name']?></a></span>
						</li>
					<?php } ?>
				</ul>
			</div>
		</div>
		<!----------------------------------Products information block--------------------------->
		<div class="mod_widget_large">
			<div class="mod_widget_title">
				<div class="heading_wrap">
					<img src="<?php echo base_url().'images/home2.png'?>">Products
					<span class="fl_right ttl_count">Total : <?=count($products)?></span>
				</div>
			</div>
			<div class="mod_widget_content">
				<ul>
					<?php $k=1;foreach($products as $p){?>
						<li>
							 <?=$k++?> <span><a class="link" href="<?=site_url("admin/editproduct/{$p['product_id']}")?>" target="_blank"><?=$p['product_name']?></a></span>
							 <span class="fl_right mrp_wrap">MRP : <?=round($p['mrp'],2)?></span>
							 <span class="fl_right barcode_wrap">Barcode : <?=$p['barcode']?></span>
						</li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</div>
</div>


<!-- the jScrollPane script -->
<script type="text/javascript" src="<?=base_url()?>/js/custom_scrollbar/jquery.jscrollpane.min.js"></script>

<script type="text/javascript" src="<?=base_url()?>/js/custom_scrollbar/mwheelIntent.js"></script>

<!-- the mousewheel plugin - optional to provide mousewheel support -->
<script type="text/javascript" src="<?=base_url()?>/js/custom_scrollbar/jquery.mousewheel.js"></script>

<?php
