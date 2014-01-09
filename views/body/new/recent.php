<div class="upcomingcont">
	
	<div class="upcominghead" style="background:#9A0093;">
		<div class="container">
			<h1>recently sold deals on snapittoday.com</h1>
		</div>
	</div>
	
	<div class="container">
		<div class="upcominglist">
			<table width="100%">
				<tr>
					<?php $i=0; foreach($deals as $deal){?>
						<td width="33%">
							<div class="list">
								<div class="discount"><?=ceil(($deal['orgprice']-$deal['price'])/$deal['orgprice']*100)?>% off</div>
								<div style="height:250px;"><a href="<?=site_url("{$deal['url']}")?>"><img src="<?=base_url()?>images/items/<?=$deal['pic']?>.jpg" style="width:260px;max-height:250px;"></a></div>
								<h2 style="margin:10px 0px 0px 0px;"><?=$deal['name']?></h2>
								<h3>Rs <?=$deal['price']?></h3>
								<div style="font-size:110%;padding-bottom:15px;">Want to get the deal?</div>
								<img src="<?=base_url()?>images/request.png" class="alertmetrig">
								<form class="requestme"><input type="hidden" name="id" value="<?=$deal['id']?>">
									<div style="padding-bottom:5px;"><input type="text" name="mobile" class="alrt_m_input" value="Enter mobile"></div>
									<div style="padding-bottom:5px;"><input type="text" name="email" class="alrt_e_input" value="Enter email"></div>
									<div align="right"><input type="image" src="<?=base_url()?>images/submit.png"></div>
								</form>
							</div>
						</td>
					<?php $i++; if($i==3) {$i=0; echo '</tr><tr>';} } ?>
					<?php for(;$i<=3;$i++){?><td></td><?php } ?>
				</tr>
			</table>
			<?php if(empty($deals)){?>
				<h1>No recent deals</h1>
			<?php } ?>
		</div>
	</div>

</div>