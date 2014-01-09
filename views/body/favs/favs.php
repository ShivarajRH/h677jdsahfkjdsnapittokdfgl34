<style>
body{
background:#fff;
}
</style>
<div class="container fav_container">

<h1 class="fav_heading">Choose your Favourite <?=FAV_LIMIT?>'s and get <?=FAV_DISCOUNT?>% off on every purchase!</h1>

<img src="<?=IMAGES_URL?>fav_banner.png">

<div class="fav_desc">
Just choose upto <?=FAV_LIMIT?> products from the categories below.<br>And you will get flat <?=FAV_DISCOUNT?>% discount on MRP of your FAVs every order every time!
</div>

<br>

<div class="fav_cats">
<?php 
$itemids=$this->fdbm->getallfavs();
foreach($cats as $i=>$cat){?>
<div class="cat <?=$i<3?"top":""?> <?=($i%3==0)?"left":""?>">
	<div class="img" style="background:url(<?=IMAGES_URL?>items/small/<?=$cat['pic']?>.jpg) no-repeat;">
		<a href="<?=site_url("choosefav/{$cat['url']}")?>">&nbsp;</a>
	</div>
	<div>
		<a href="<?=site_url("choosefav/{$cat['url']}")?>">
		<span style="color:#aaa">save <?=FAV_DISCOUNT?>% in</span>
		<span class="title"><?=$cat['cat']?></span>
		<img src="<?=IMAGES_URL?>choose_fav.png">
		</a>
	</div>
	<?php if(in_array($cat['catid'], $locked)){
		foreach($itemids as $i)
			if($i['catid']==$cat['catid'])
			{
				$itemid=$i['itemid'];
				break;
			}
		$url=$this->db->query("select url from king_dealitems where id=?",$itemid)->row()->url;
	?>
	<span class="locked">
		<span class="text">LOCKED</span>
		<a style="margin:5px;display:inline;height:auto;" href="<?=site_url($url)?>"><b><?=$i['name']?></b></a>
	</span>
	<?php }?>
</div>
<?php }?>
</div>

<div class="clear"></div>

</div>
<?php

