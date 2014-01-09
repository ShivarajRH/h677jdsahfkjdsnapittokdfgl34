<div class="container featuredland">

<div class="featuredtilescont" style="border:0px;">

<h1 style="padding-top:10px;border-bottom:1px dotted #aaa;">Discover <span class="maroon">Products</span> from <?=$brand?></h1>


<div class="featuredtiles featuredproducts">
<?php foreach($featured as $i=>$f){ ?>
<div class="tile" title="Buy <?=htmlspecialchars($f['name'])?> online for Rs <?=$f['price']?>">
	<div class="img" align="center">
		<a href="<?=site_url("{$f['url']}")?>" class="scrollman sm_f_prodt">
			<img <?php if(B_BROW){?>class="scrm_load" src="<?=IMAGES_URL?>scroll_load.gif"<?php }else{?> title="<?=htmlspecialchars($f['name'])?>" alt="<?=htmlspecialchars($f['name'])?>" src="<?=IMAGES_URL?>items/small/<?=$f['pic']?>.jpg"<?php }?>>
			<?php if(B_BROW){?><span class="scrm_data"><?=IMAGES_URL?>items/300/<?=$f['pic']?>.jpg</span><?php }?>
		</a>
	</div>
	<div class="top">
		<h3>
			<a href="<?=site_url($f['url'])?>"><?=$f['name']?></a>
		</h3>
				<div class="cat"><a href="<?=site_url($f['caturl'])?>">in <?=$f['category']?></a></div>
		<div class="price">
			<img src="<?=IMAGES_URL?>snap_arrow.png" class="snaparrow">
			Rs <?=$f['price']?>
		</div>
	</div>
</div>
<?php }?>
<div class="clear"></div>
</div>

</div>

</div>

<?php
