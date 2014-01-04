<style>
body{
background:#fff;
}
</style>

<div class="container fav_container" style="padding:0px;">

<h1 class="fav_heading">Weekly Savings</h1>

<?php if(!empty($prods)){?>

<div class="fav_desc">
We have hand picked some awesome deals for this week
</div>

<div class="fav_cats">
<?php foreach($prods as $i=>$cat){?>
<div class="cat <?=$i<3?"top":""?> <?=($i%3==0)?"left":""?>">
	<div class="img" style="background:url(<?=IMAGES_URL?>items/small/<?=$cat['pic']?>.jpg) top center no-repeat;" title="<?=htmlspecialchars($cat['name'])?>">
		<a href="<?=site_url("{$cat['url']}")?>">&nbsp;</a>
	</div>
	<div>
		<a href="<?=site_url("{$cat['url']}")?>">
		<span class="title" style="font-size:110%;"><?=breakstring($cat['name'],50)?>
		<span style="color:#aaa;font-size:90%;"><br>Rs <?=$cat['price']?></span>
		</span>
		<img src="<?=IMAGES_URL?>viewproduct.png" style="float:right">
		</a>
	</div>
</div>
<?php }?>
</div>

<?php }else{?>
<div class="info" style="margin-bottom:80px;font-size:13px;">
<div style="font-size:20px;padding-bottom:5px;">Argh! No great savings running now</div>
<div>Please check back later</div>
</div>
<?php }?>

<div class="clear"></div>

</div>
<?php
