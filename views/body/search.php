<div class="container">
	<div class="homecont">
				<div>
					<div class="bender">
						<div class="matter">Search Deals</div>
						<span></span>
					</div>
				</div>
		<div class="spot_deal_cont">
		<table cellspacing=10>
			<tr>
				<?php
				foreach($deals as $i=>$deal){?>
					<td class="dealdlist" <?php if($i%5==0 || $i==0) echo 'style="border-left:1px solid #ccc"';?>>
							<?php if($deal['groupbuy']){?><div class="gbenabled"><img src="<?=IMAGES_URL?>groupenabled.png"></div><?php }?>
							<div class="imgcont"><a href="<?=site_url("{$deal['url']}")?>"><img src="<?=base_url()?>images/items/small/<?=$deal['pic']?>.jpg" width=160></a></div>
							<div class="namecont">
								<div style=""><a class="at" href="<?=site_url($deal['caturl'])?>" style="text-decoration:none"><?=$deal['category']?></a></div>
								<div style="font-size:95%;"><?=$deal['name']?></div>
							</div>
							<div class="price"><b>Rs <?=number_format($deal['price'])?></b></div>
							<div class="save"><b>save <?=ceil(($deal['orgprice']-$deal['price'])/$deal['orgprice']*100)?>%</b></div>
							<div class="mrp"><span style="text-decoration:line-through">Rs <?=$deal['orgprice']?></span> list price</div>
					</td>
				<?php if(($i+1)%5==0) echo '</tr><tr>';}?>
			</tr>
		</table>
		</div>
		<?php if(empty($deals)){?>
				<div align="center" style="padding:50px;"><h2>No results found<br>Please refine your search keyword</h2></div>
		<?php }?>
	</div>
</div>