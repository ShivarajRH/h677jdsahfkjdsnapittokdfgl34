<div class="container featuredland">
	
<div class="home_sidepane" style="margin-top:10px;width:210px;">

	<div class="shopby">
		<h3 class="blue">popular categories</h3>
		<?php foreach($sidepane['cats'] as $b){?>
			<a href="<?=site_url($b['url'])?>">
				<span class="raquo blue">&raquo;</span>
				<?=$b['name']?>
			</a>
		<?php } ?>
	</div>


	<div class="shopby">
		<h3 class="blue">new products</h3>
		<?php foreach($new as $b){?>
			<a href="<?=site_url($b['url'])?>">
				<span class="raquo blue">&raquo;</span>
				<?=$b['name']?>
			</a>
		<?php } ?>
	</div>

	<div class="shopby">
		<h3 class="blue">recently sold</h3>
		<?php foreach($recent as $b){?>
			<a href="<?=site_url($b['url'])?>">
				<span class="raquo blue">&raquo;</span>
				<?=$b['name']?>
			</a>
		<?php } ?>
	</div>


</div>


<div class="featuredbanners">
	<div class="banner">
		<a href="<?=site_url("Pre-seed--Multiuse-Lubricant-40g,-9-applicators-p37t")?>"><img src="<?=IMAGES_URL?>featured_banners/pre-seed_homecheck_offer.png"></a>
	</div>
	<div class="banner">
		<a href="<?=site_url("Nuzen-Gold-Herbal-Hair-Oil---100ml-FREE-Proteion-Shampoo-p39t")?>"><img src="<?=IMAGES_URL?>featured_banners/nuzen_gold.png"></a>
	</div>
	<div class="banner">
		<a href="<?=site_url("Lotus-herbals")?>"><img src="<?=IMAGES_URL?>featured_banners/lotus_herbals.png"></a>
	</div>
	<div class="clear"></div>
</div>


<div class="featured_menu">

<div class="cont">
<?php foreach($deals as $i=>$m){
	if($i%3==0)
		echo '</div><div class="cont">';
?>
	<div class="deal">
		<div class="img">
			<a href="<?=site_url($m['murl'])?>">
				<img src="<?=IMAGES_URL?>items/small/<?=$m['pic']?>.jpg" title="<?=htmlspecialchars($m['name'])?>" alt="<?=htmlspecialchars($m['name'])?>" width="200">
			</a>
		</div>
		<h2><a href="<?=site_url($m['murl'])?>"><?=$m['menu']?></a></h2>
		<div class="cats">
		<?php foreach($m['cats'] as $c){?>
			<a href="<?=site_url($m['murl']."/".$c['url'])?>"><?=$c['name']?></a>
		<?php }?>
		</div>
	</div>
<?php if($i==8) break;} ?>
</div>

<div class="clear"></div>

</div>




<div class="clear"></div>

</div>

<div class="container featuredland">

<div class="featuredtilescont featuredproducts">
	<div class="experts"></div>

<h1 style="padding-top:10px;">
	<?=$this->db->query("select value from king_vars where id=1")->row()->value?>
</h1>

<div class="featuredtiles">
<?php foreach($featured as $i=>$f){?>
<div class="tile" title="Buy <?=htmlspecialchars($f['name'])?> online for Rs <?=$f['price']?>">
	<div class="snapit_tile">
		<input type="hidden" class="itemname" value="<?=htmlspecialchars($f['name'])?>">
		<input type="hidden" class="itemid" value="<?=$f['id']?>">
		<input type="hidden" class="itempic" value="<?=$f['pic']?>">
		<input type="hidden" class="url" value="<?=$f['url']?>">
		<img src="<?=IMAGES_URL?>snapit.png">
	</div>
	<div class="img">
		<a href="<?=site_url("{$f['url']}")?>" class="scrollman sm_f_prodt">
			<img <?php if(B_BROW){?>class="scrm_load" src="<?=IMAGES_URL?>scroll_load.gif"<?php }else{?> title="<?=htmlspecialchars($f['name'])?>" alt="<?=htmlspecialchars($f['name'])?>" src="<?=IMAGES_URL?>items/small/<?=$f['pic']?>.jpg"<?php }?>>
			<?php if(B_BROW){?><span class="scrm_data"><?=IMAGES_URL?>items/300/<?=$f['pic']?>.jpg</span><?php }?>
		</a>
	</div>
	<div class="top">
		<h3>
			<a href="<?=site_url($f['url'])?>"><?=$f['name']?></a>
		</h3>
			<div class="cat">
				<img src="<?=IMAGES_URL?>snap_arrow.png" style="float:right;">
				<a href="<?=site_url($f['burl'])?>">by <?=$f['brand']?></a>
			</div>
	</div>
</div>
<?php }?>
<div class="clear"></div>
</div>

</div>


</div>

<style>
.featuredland{
width:960px;
}
</style>

<script>
var itemid,itempic,itemname;
$(function(){
	$(".featuredscroll .tile").each(function(i){
		$(this).css("margin-left",(i*124)+"px");
	});
	$(".featuredscroll .right").click(function(){
		$(".featuredscroll .tile").animate({left:"-=124"},500);
	});
	$(".featuredscroll .left").click(function(){
		$(".featuredscroll .tile").animate({left:"+=124"},500);
	});
});
</script>
<?php
