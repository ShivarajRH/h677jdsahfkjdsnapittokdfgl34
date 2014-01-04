<?php  
$colors=array("green","violet","lightblue","red","black","darkgreen",'orange');
?>
<div>
	<div class="container" style="margin-top:15px;">

<div class="homecont refine" style="margin-bottom:10px;padding-bottom:0px;">
	<div class="top">
		<div class="head">product list by<br>Brands & Categories</div>
	<div class="bottom">
	<div style="clear:both;padding:7px 0px 0px;">
		<h3>Available Categories <span style="font-weight:normal;font-size:80%;">(<?=count($cats)?>)</span></h3>
		<div class="search_fltr_box">
		<div class="scont">
		<?php foreach($cats as $cat){?>
			<div class="refine_but refine_cat" onclick='location="<?=site_url($this->uri->segment(1)."/".$cat['url'])?>"'>
<?php if(stripos($_SERVER['HTTP_USER_AGENT'],"Googlebot")!==false){?>
				<a href="<?=(rand(0,3)==1?site_url("viewbymenucat/".$this->uri->segment(1)."/".$cat['url']):site_url($this->uri->segment(1)."/".$cat['url']))?>">
<?php }else{?>
			 	<a href="<?=site_url($this->uri->segment(1)."/".$cat['url'])?>">
<?php } ?>
				<?=$cat['name']?></a>
			</div>
		<?php }?>
		</div>
		</div>
	</div>
	<div style="clear:both;padding:7px 0px 0px;" id="refine_brand_cont">
		<h3>Available Brands</h3>
		<div class="search_fltr_box">
		<div class="scont">
		<?php foreach($brands as $brand){ ?>
			<div class="refine_but refine_brand" onclick='location="<?=site_url($this->uri->segment(1)."/".$brand['url'])?>"'>
<?php if(stripos($_SERVER['HTTP_USER_AGENT'],"Googlebot")!==false){?>
				<a href="<?=(rand(0,3)==1?site_url("viewbymenubrand/".$this->uri->segment(1)."/".$brand['url']):site_url($this->uri->segment(1)."/".$brand['url']))?>">
<?php }else{?>
			 	<a href="<?=site_url($this->uri->segment(1)."/".$brand['url'])?>">
<?php } ?>
			 	<?=$brand['name']?></a>
			 </div>
		<?php }?>
		</div>
		</div>
		<div class="clear"></div>
	</div>
	</div>

	<div class="clear"></div>
	</div>
</div>


<div class="inner_deals_cont">	

<?php $h=0; foreach($deals as $cdeals){?>
		<div class="homecont">
			<div>
			<?php	if(count($cdeals)>3){ ?>
				<a class="blue" href="<?=site_url("$menuurl/{$cdeals[0]['caturl']}")?>" style="font-weight:bold;display:inline-block;margin:11px;font-size:100%;float:right;"><?=count($cdeals)?> products in <?=$brandscount[$h]?> brands available</a>
			<?php }?>
				<div class="bender bc-<?=$colors[rand(0,count($colors)-1)]?>">
					<div class="matter"><?=ucfirst($heads[$h])?></div>
					<span></span>
				</div>
			</div>
			<div class="spot_deal_cont">
					<?php
					foreach($cdeals as $i=>$deal){?>
						<div class="dealdlist" id="mdeal<?=$h?>_<?=$i?>">
							<?php if(!$deal['live']){?><div class="ofs"><img src="<?=IMAGES_URL?>ofs_small.png"></div><?php }?>
							<?php if($deal['groupbuy']){?><div class="gbenabled"><img src="<?=IMAGES_URL?>groupenabled.png"></div><?php }?>
							<div class="imgcont">
								<a href="<?=site_url("{$deal['url']}")?>" class="scrollman sm_prodt">
									<img <?php if(B_BROW){?>class="scrm_load" src="<?=IMAGES_URL?>scroll_load.gif"<?php }else{?> title="<?=htmlspecialchars($deal['name'])?>" alt="<?=htmlspecialchars($deal['name'])?>" src="<?=IMAGES_URL?>items/small/<?=$deal['pic']?>.jpg"<?php }?>>
									<?php if(B_BROW){?><span class="scrm_data"><?=IMAGES_URL?>items/small/<?=$deal['pic']?>.jpg</span><?php }?>
								</a>
<?php /*?>							<a href="<?=site_url("{$deal['url']}")?>"><img src="<?=IMAGES_URL?>items/<?=$deal['pic']?>.jpg" width=160></a>   */ ?>
							</div>
							<div class="namecont">
								<div><a class="at" href="<?=site_url($deal['caturl'])?>" style="text-decoration:none"><?=$deal['category']?></a></div>
								<div style="font-size:95%;padding:5px 0px;"><?=$deal['name']?></div>
							</div>
							<div class="price">
							<b>Rs <?=number_format($deal['orgprice'])?></b>
							<?php if($deal['price']!=$deal['orgprice']){?>
							<div class="instantcashback">Rs <?=$deal['price']?> <img src="<?=IMAGES_URL?>instantcashback.png"></div>
							<?php }?>
							</div>
<?php if($deal['live']){?>
							<div class="addcart_quick_cont"><label><input type="checkbox" value="<?=$deal['url']?>" style="display:none;" class="addtocart_quick"> <span>Add to cart</span></label></div>
<?php } ?>
						</div>
					<?php if($i==2) break;}?>
				<div class="clear"></div>
			</div>
			<?php if(empty($deals)){?>
					<div align="center" style="padding:50px;"><h2>Sorry! No deals available right now<br>Please check back later</h2></div>
			<?php }?>
		</div>
<?php $h++;}?>
<?php if(empty($deals)){ ?>
		<div style="padding:20px 0px" class="homecont">
			<div class="homehead">
				<div class="hptitle">
					<div class="cnt"><?=$pagetitle?></div>
				</div>
			</div>
			<?php if(empty($deals)){?>
					<div align="center" style="padding:50px;"><h2>Sorry! No deals available right now<br>Please check back later</h2></div>
			<?php }?>
		</div>
<?php }?>
</div>
	</div>
	<div class="clear"></div>
</div>

<?php
