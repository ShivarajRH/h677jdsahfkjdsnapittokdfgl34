<div class="upcomingcont">
	
	<div class="upcominghead">
		<div class="container">
			<h1>Upcoming great deals from snapittoday, exclusively for snapittoday members.</h1>
		</div>
	</div>
	
	<div class="container">
		<div class="upcominglist">
			<table width="100%">
					<?php $i=0; foreach($deals as $deal){?>
						<tr>
						<td>
							<div class="list">
								<div class="discount"><?=ceil(($deal['orgprice']-$deal['price'])/$deal['orgprice']*100)?>% off</div>
								<div><img src="<?=base_url()?>images/items/<?=$deal['pic']?>.jpg" style="width:100%"></div>
								<h2 style="margin:0px 0px 0px 0px;"><?=$deal['name']?></h2>
	<?php /*
								<div style="font-size:110%;padding-bottom:15px;">Set alert for your deal, we shall intimate you</div>
								<img src="<?=base_url()?>images/alertme.png" class="alertmetrig">
								<form class="alertme"><input type="hidden" name="id" value="<?=$deal['id']?>">
									<div style="padding-bottom:5px;"><input type="text" name="mobile" class="alrt_m_input" value="Enter mobile"></div>
									<div style="padding-bottom:5px;"><input type="text" name="email" class="alrt_e_input" value="Enter email"></div>
									<div align="right"><input type="image" src="<?=base_url()?>images/submit.png"></div>
								</form>
		*/ ?>
							</div>
						</td>
						</tr>
					<?php $i++; if($i==4) break; } ?>
			</table>
			<?php if(empty($deals)){?>
				<h1>No recent deals</h1>
			<?php } ?>
		</div>
	</div>

</div>