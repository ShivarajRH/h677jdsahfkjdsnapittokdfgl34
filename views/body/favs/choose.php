<div class="container fav_container">

<h1 class="fav_heading">Choose your FAV in <?=$prods[0]['cat']?> to get <?=FAV_DISCOUNT?>% discount</h1>


<div class="fav_desc">
Select &amp; lock a product in the below list to get <?=FAV_DISCOUNT?>% discount on MRP in every order<br>Please note that once the product is locked as your FAV, you can't change it for <?=FAV_EXPIRY?> days
</div>

<div class="fav_cats">
<?php foreach($prods as $i=>$cat){?>
<div class="cat <?=$i<3?"top":""?> <?=($i%3==0)?"left":""?>">
	<div class="img" style="background:url(<?=IMAGES_URL?>items/small/<?=$cat['pic']?>.jpg) no-repeat;">
		<a href="<?=site_url("selectfav/{$cat['id']}")?>">&nbsp;</a>
	</div>
	<div>
		<a href="<?=site_url("selectfav/{$cat['id']}")?>">
		<span style="color:#aaa">save <?=FAV_DISCOUNT?>% in</span>
		<span class="title" style="font-size:120%;"><?=$cat['name']?>
		<span style="color:#aaa;font-size:90%;"><br>MRP : Rs <?=$cat['orgprice']?></span>
		</span>
		<img src="<?=IMAGES_URL?>lock_fav.png">
		</a>
	</div>
</div>
<?php }?>
</div>

<div class="clear"></div>

</div>
<?php
