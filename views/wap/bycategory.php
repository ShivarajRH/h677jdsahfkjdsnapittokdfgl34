<div>
	<div class="container">
		<div class="homecont">
			<div class="homehead">
				<div class="hptitle">
					<div class="cnt"><?=ucfirst($pagetitle)?></div>
				</div>
			</div>
		</div>	
		<div style="padding:0px;">
		<?php foreach($deals as $deal){?>
			<div class="mob_deal">
				<table width="100%" cellpadding=0 cellspacing=0>
					<tr>
						<td width=80>
							<img src="<?=IMAGES_URL?>items/small/<?=$deal['pic']?>.jpg" width="80">
						</td>
						<td valign="top" align="left" style="text-align:left;font-size:9px;">
							<h4 style="margin:0px;"><?=$deal['name']?></h4>
							<div>Snap it today @ <b class="green" style="font-size:9px;">Rs <?=$deal['price']?></b></div>
							<div style="margin-top:3px;">MRP <b style="color:#ff9900;text-decoration:line-through;">Rs <?=$deal['orgprice']?></b></div>
<!--							<div>Discount: </div>-->
							<div><b style="color:#ff9900;"><?=ceil(($deal['orgprice']-$deal['price'])/$deal['orgprice']*100)?>%</b> OFF <b class="green">FREE</b> Shipping</div>
							<div><a href="<?=site_url($deal['url'])?>"><img src="<?=IMAGES_URL?>buynow_m.png"></a></div>
						</td>
					</tr>
				</table>
			</div>
		<?php }?>
		</div>
		<?php if(empty($deals)){?>
				<div align="center" style="padding:50px;"><h2>Sorry! No deals available right now<br>Please check back later</h2></div>
		<?php }?>
	</div>
</div>
<style>
.mob_deal{
font-size:11px;
background:#fff;
padding:3px;
margin:5px;
}
.mob_deal h2{
color:#024769;
margin:0px;
margin-bottom:3px;
}
</style>
<?php
