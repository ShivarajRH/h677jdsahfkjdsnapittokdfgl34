<div>
	<div class="container">
	
<div class="home_sidepane" style="margin-top:17px;">
	<div class="shopby">
		<h3 class="blue">top brands</h3>
		<?php foreach($sidepane['brands'] as $b){?>
			<a href="<?=site_url($b['url'])?>">
				<span class="raquo blue">&raquo;</span>
				<?=$b['name']?>
			</a>
		<?php } ?>
	</div>

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
		<h3 class="blue">top products</h3>
		<?php foreach($tops as $b){?>
			<a href="<?=site_url($b['url'])?>">
				<span class="raquo blue">&raquo;</span>
				<?=$b['name']?>
			</a>
		<?php } ?>
	</div>


</div>

<div class="home_main">

	<div class="banners">
		<div class="hbanner">
			<img src="<?=IMAGES_URL?>banners/onestop-beauty-wellness-healthcare.png">
		</div>
		<div class="boxbanner"><a href="<?=site_url("Lotus-Herbals")?>"><img src="<?=IMAGES_URL?>banners/lotus_herbals.jpg" alt="Save upto 32% on Lotus Herbals products" title="Save upto 32% on Lotus Herbals products"></a></div>
		<div class="boxbanner"><a href="<?=site_url("Actilife")?>"><img src="<?=IMAGES_URL?>banners/actilife-for-adults.png" alt="Introducing Actilife for adults" title="Introducing Actilife for adults"></a></div>
		<div class="clear"></div>
	</div>
	
<?php foreach($deals as $i=>$m){?>
	<div class="deal">
		<h2><a href="<?=site_url($m['murl'])?>"><?=$m['menu']?></a></h2>
		<div class="img">
			<a href="<?=site_url($m['url'])?>">
				<img src="<?=IMAGES_URL?>items/small/<?=$m['pic']?>.jpg" title="<?=htmlspecialchars($m['name'])?>" alt="<?=htmlspecialchars($m['name'])?>" width="160">
			</a>
		</div>
		<div class="cats">
		<?php foreach($m['cats'] as $c){?>
			<a href="<?=site_url($m['murl']."/".$c['url'])?>"><?=$c['name']?> &raquo;</a>
		<?php }?>
		</div>
	</div>
<?php if(($i+1)%4==0){?>
<div class="horibar"></div>
<?php } } ?>
</div>

<div class="clear"></div>

		<div class="homecont">
			<div>
				<div class="bender bc-green">
					<div class="matter">Recently sold</div>
					<span></span>
				</div>
			</div>
			<div class="spot_deal_cont">
						<?php
						foreach($recent as $i=>$deal){?>
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
						<?php if($i>2) break;}?>
						<div class="clear"></div>
			</div>
		</div>



<?php /*?>

<?php $colors=array("green","violet","lightblue","red","black","darkgreen",'orange');	unset($deals['dod']); $mdeals=$deals; foreach($mdeals as $menu=>$deals){?>
		<div class="homecont">
			<div>
				<div class="bender bc-<?=array_pop($colors)?>">
					<div class="matter"><img src="<?=IMAGES_URL?>latest/<?=$menu?>.png"></div>
					<span></span>
				</div>
			</div>
			<div class="spot_deal_cont">
				<table width="100%" cellspacing=15>
					<tr>
						<?php
						foreach($deals as $i=>$deal){?>
							<td style="text-align:center;" class="dealdlist">
							<?php if(!$deal['live']){?><div class="ofs"><img src="<?=IMAGES_URL?>ofs_small.png"></div><?php }?>
							<?php if($deal['groupbuy']){?><div class="gbenabled"><img src="<?=IMAGES_URL?>groupenabled.png"></div><?php }?>
								<div class="imgcont">
								<a href="<?=site_url("{$deal['url']}")?>" class="scrollman sm_prodt">
									<img <?php if(B_BROW){?>class="scrm_load" src="<?=IMAGES_URL?>scroll_load.gif"<?php }else{?> title="<?=htmlspecialchars($deal['name'])?>" alt="<?=htmlspecialchars($deal['name'])?>" src="<?=IMAGES_URL?>items/small/<?=$deal['pic']?>.jpg"<?php }?>>
									<?php if(B_BROW){?><span class="scrm_data"><?=IMAGES_URL?>items/small/<?=$deal['pic']?>.jpg</span><?php }?>
								</a>
								</div>
								<div class="namecont">
									<div><a class="at" href="<?=site_url($deal['caturl'])?>" style="text-decoration:none"><?=$deal['category']?></a></div>
									<div style="font-size:95%;padding:5px 0px 0px;"><?=$deal['name']?></div>
								</div>
								<div class="price"><b>Rs <?=number_format($deal['price'])?></b></div>
							<?php if($deal['price']!=$deal['orgprice']){?>
								<div class="save"><b><?=ceil(($deal['orgprice']-$deal['price'])/$deal['orgprice']*100)?>%</b> OFF</div>
								<div class="mrp"><b><span style="text-decoration:line-through">Rs <?=$deal['orgprice']?></span></b> list price</div>
							<?php }?>
							</td>
						<?php if($i>3) break;}?>
					</tr>
				</table>
			</div>
		</div>
<?php }?>		
*/?>
<style>
.spot_deal_cont .dealdlist{
width:222px;
}
</style>
<?php
