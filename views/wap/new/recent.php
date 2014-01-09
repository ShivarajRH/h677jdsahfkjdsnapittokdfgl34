<div class="upcomingcont">
	
	<div class="upcominghead" style="background:#9A0093;color:#fff;">
		<div class="container">
			<h1>recently sold deals on snapittoday.com</h1>
		</div>
	</div>
	
	<div class="container">
		<div class="upcominglist">
			<table width="100%">
					<?php $i=0; foreach($deals as $deal){?>
						<tr>
						<td width="33%">
							<div class="list">
								<div class="discount"><?=ceil(($deal['orgprice']-$deal['price'])/$deal['orgprice']*100)?>% off</div>
								<div><a href="<?=site_url("deal/{$deal['url']}")?>"><img src="<?=base_url()?>images/items/<?=$deal['pic']?>.jpg" style="width:100%;"></a></div>
								<h2 style="margin:0px 0px 0px 0px;"><?=$deal['name']?></h2>
								<h3>Rs <?=$deal['price']?></h3>
		<?php /*
								<div style="font-size:110%;padding-bottom:15px;">Want to get the deal?</div>
								<img src="<?=base_url()?>images/request.png" class="alertmetrig">
								<form class="requestme"><input type="hidden" name="id" value="<?=$deal['id']?>">
									<div style="padding-bottom:5px;"><input type="text" name="mobile" class="alrt_m_input" value="Enter mobile"></div>
									<div style="padding-bottom:5px;"><input type="text" name="email" class="alrt_e_input" value="Enter email"></div>
									<div align="right"><input type="image" src="<?=base_url()?>images/submit.png"></div>
								</form>
								
			*/?>
							</div>
						</td>
						</tr>
					<?php $i++; if($i>4) break; } ?>
			</table>
			<?php if(empty($deals)){?>
				<h1>No recent deals</h1>
			<?php } ?>
		</div>
	</div>

</div>