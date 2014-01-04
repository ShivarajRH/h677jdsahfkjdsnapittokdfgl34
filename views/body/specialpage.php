<div class="container">
<h1>Buy <?=ucfirst($this->uri->segment(1))?> products</h1>
<div class="specialpage">
	<div class="product_left">
<?php $deals=$deal; foreach($deals as $deal){?>	
		<div class="product">
			<div class="img">
				<a href="<?=site_url($deal['url'])?>"><img src="<?=IMAGES_URL?>items/<?=$deal['pic']?>.jpg"></a>
			</div>
			<div class="head"><?=$deal['name']?></div>
			<div class="details">
			<div style="float:right">
			<a href="<?=site_url($deal['url'])?>" class="addtocart_glob"><div class="icon"></div>Buy now</a>
			</div>
				<div class="price">Rs <?=$deal['price']?> <span style="text-decoration:line-through"><?=$deal['orgprice']?></span></div>
				<div class="instockava green">In Stock</div>
			</div>
		</div>
<?php }?>
	</div>
	
	<div class="cats">
	<h3>Explore more</h3>
<?php foreach($cats as $cat){?>
		<div class="cat" style="background-image:url(<?=IMAGES_URL?>items/small/<?=$cat['pic']?>.jpg);">
		<a href="<?=site_url($cat['murl']."/".$cat['caturl'])?>">
			<div class="overlay"><?=$cat['category']?></div>
		</a>
		</div>
<?php }?>
	</div>

</div>
</div>
<?php
