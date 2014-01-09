<div class="clear" style="padding-top:10px;"></div>
<div>
	<div class="container" align="center">

<?php if(isset($brands) || isset($cats)){?>
<div class="homecont refine" style="margin-bottom:10px;padding-bottom:0px;">
	<div class="top">
		<div class="head">find more products</div>
	<div class="bottom">
<?php if(isset($brands)){?>
	<div style="clear:both;padding:7px 0px 0px;" id="refine_brand_cont">
		<h3>Brands <span style="font-weight:normal;font-size:80%;">(<?=count($brands)?>)</span></h3>
		<div class="search_fltr_box">
		<div class="scont">
		<?php foreach($brands as $brand){ ?>
			<div class="refine_but refine_pl_brand">
				<a href="<?=site_url("viewby{$uri_part}brand/".$this->uri->segment(2)."/{$brand['url']}")?>"><?=$brand['name']?></a>
			<input type="checkbox" checked="checked" class="ref_brand" value="<?=site_url($this->uri->segment(2)."/{$brand['url']}")?>"></div>
		<?php }?>
		</div>
		</div>
		<div class="clear"></div>
	</div>
<?php }?>
<?php if(isset($cats)){?>
	<div style="clear:both;padding:7px 0px 0px;">
		<h3>Categories <span style="font-weight:normal;font-size:80%;">(<?=count($cats)?>)</span></h3>
		<div class="search_fltr_box" style="height:auto">
		<div class="scont">
		<?php foreach($cats as $bid=>$brand){?>
			<div class="refine_but refine_pl_cat">
				<a href="<?=site_url("viewby{$uri_part}cat/".$this->uri->segment(2)."/".$brand['url'])?>">
					<?=$brand['name']?>
				</a>
			<input type="checkbox" class="ref_brand" checked="checked" value="<?=site_url($this->uri->segment(2)."/".$brand['url'])?>"></div>
		<?php }?>
		</div>
		</div>
	</div>
<?php }?>

	</div>
	</div>

	<div class="clear" style="padding-top:10px;"></div>
</div>
<?php }?>

<?php 
$len=$count;
$limit=PAGED_LIMIT;
$st=(($p-1)*$limit+1);
$et=$st+$limit-1;
if($et>$len)
	$et=$len;
if($st>$et)
	$st=$et;
$total_pages = round($len/$limit);
?>
	
	<div style="padding:5px 0px;float:right;margin-top:-20px;">
<?php if($len>0){?>
<?php if($p>1 && isset($prevurl)){?>
<a style="padding:5px;text-decoration: underline;" href="<?=$prevurl?>">prev</a>
<?php }?>

		showing <?=$st?><?php if($st!=$et){?> - <?=$et?><?php }?> of <?=$len?>

<?php if($et<$len && isset($nexturl)){?>
<span style="float: right">
<a style="padding:5px;text-decoration: underline;" href="<?=$nexturl?>">next</a>
</span>
<?php }}?>

	</div>

	<div class="inner_deals_cont" <?php if(!isset($brands)&&!isset($cats)){?>style="float:none;"<?php }?>>
		<?php if(isset($menupic)){?>
		<div class="menu_page_banner">
			<img src="<?=IMAGES_URL?>menu_banners/<?=$menupic?>.png">
		</div>
		<?php }?>
		<div class="homecont">
			<div>
				<div class="bender bc-orange">
					<div class="matter"><h1 style="font-size:inherit;"><?=ucfirst($pagetitle)?></h1></div>
					<span></span>
				</div>
			</div>
			<div class="spot_deal_cont">
					<?php
					foreach($deals as $i=>$deal){?>
						<div class="dealdlist" id="deal<?=$deal['itemid']?>" <?php //if($i%5==0 || $i==0) echo 'style="border-left:1px solid #ccc"';?>>
							<?php if(!$deal['live']){?><div class="ofs"><img src="<?=IMAGES_URL?>ofs_small.png"></div><?php }?>
							<?php if($deal['groupbuy']){?><div class="gbenabled"><img src="<?=IMAGES_URL?>groupenabled.png"></div><?php }?>
							<div class="imgcont">
								<a href="<?=site_url("{$deal['url']}")?>" class="scrollman sm_prodt">
									<img <?php if(B_BROW){?>class="scrm_load" src="<?=IMAGES_URL?>scroll_load.gif"<?php }else{?> title="<?=htmlspecialchars($deal['name'])?>" alt="<?=htmlspecialchars($deal['name'])?>" src="<?=IMAGES_URL?>items/small/<?=$deal['pic']?>.jpg"<?php }?>>
									<?php if(B_BROW){?><span class="scrm_data"><?=IMAGES_URL?>items/small/<?=$deal['pic']?>.jpg</span><?php }?>
								</a>
<?php /*?>							<a href="<?=site_url("{$deal['url']}")?>"><img src="<?=IMAGES_URL?>items/small/<?=$deal['pic']?>.jpg" width=160></a>   */ ?>
							</div>
							<div class="namecont">
								<div style=""><a class="at" href="<?=site_url($deal['caturl'])?>" style="text-decoration:none"><?=$deal['category']?></a></div>
								<div style="font-size:95%;padding:5px 0px;"><?=$deal['name']?></div>
							</div>
							<div>
							</div>
							<div class="price">
							<b>Rs <?=number_format($deal['orgprice'])?></b>
							<?php if($deal['price']!=$deal['orgprice']){?>
							<div class="instantcashback">Rs <?=$deal['price']?> <img src="<?=IMAGES_URL?>instantcashback.png"></div>
							<?php }?>
							</div>
<?php /*?>							
							<div class="freeshipping">
							<b class="blue">Free Shipping</b>
							<?php if($deal['price']<MIN_AMT_FREE_SHIP){?>
							 <span>on Rs <?=MIN_AMT_FREE_SHIP?> &amp; more</span>
							<?php }?>
							</div>
*/ ?>
<?php if($deal['live']){?>
							<div class="addcart_quick_cont"><label><input type="checkbox" value="<?=$deal['url']?>" style="display:none;" class="addtocart_quick"> <span>Add to cart</span></label></div>
<?php } ?>
						</div>
					<?php if(($i+1)%4==0){?>
					<?php }?>
					<?php }?>
				<div class="clear"></div>
			<div id="noresults" style="clear:both;display:none;margin:40px 20px;" align="center">
				<h3>There are no products to display after refinement. Please reset your filters</h3>
			</div>
			<?php if(empty($deals)){?>
					<div align="center" style="clear:both;padding:50px;"><h2>Sorry! No products available right now<br>Please check back later</h2></div>
			<?php }?>
			</div>
		</div>
		
	</div>	
		<div class="clear"></div>

	<div style="padding:5px 0px;float:right;">
<?php if($len>0){?>
<?php if($p>1 && isset($prevurl)){?>
<a style="padding:5px;text-decoration: underline;" href="<?=$prevurl?>">prev</a>
<?php }?>

		showing <?=$st?><?php if($st!=$et){?>-<?=$et?><?php }?> of <?=$len?>

<?php if($et<$len && isset($nexturl)){?>
<span style="float: right">
<a style="padding:5px;text-decoration: underline;" href="<?=$nexturl?>">next</a>
</span>
<?php }}?>
	</div>

	</div>
</div>

		<div class="clear"></div>

<script>

var brands=[];

$(function(){
	$(".refine_but").click(function(){
		$.fancybox.showActivity();
		if($("input:not(:checked)",$(this).parent()).length==0 && $(".refine_but_selected",$(this).parent()).length==0 && $(".refine_but",$(this).parent()).length>1)
		{
			$(".refine_but",$(this).parent()).addClass("refine_but_unselected");
			$("input",$(this).parent()).attr("checked",false);
		}
		if($("input",$(this)).attr("checked"))
		{
			$(this).addClass("refine_but_unselected");
			$(this).removeClass("refine_but_selected");
			$("input",$(this)).attr("checked",false);
		}else{
			$(this).removeClass("refine_but_unselected");
			$(this).addClass("refine_but_selected");
			$("input",$(this)).attr("checked",true);
		}
		refine();
//		$("input",$(this)).change();
		$.fancybox.hideActivity();
	});
//	$(".refine_but input").change(function(){
//		refine();
//	});
});

var prices=[],cats=[];
<?php foreach($prices as $p=>$price){?>
prices[<?=$p?>]=[<?=implode(",",$price)?>];
<?php }?>

<?php foreach($cats as $c=>$cat){?>
cats[<?=$c?>]=[<?=implode(",",$cat)?>];
<?php }?>

<?php foreach($brands as $p=>$brand){?>
brands[<?=$p?>]=[<?=implode(",",$brand)?>];
<?php }?>
			

</script>

<?php
