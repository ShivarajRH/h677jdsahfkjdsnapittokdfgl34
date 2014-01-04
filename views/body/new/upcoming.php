<div class="upcomingcont">
	
	<div class="upcominghead">
		<div class="container">
			<h1>Upcoming great deals from snapittoday, exclusively for snapittoday members.</h1>
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
								<div style="height:300px;"><img src="<?=base_url()?>images/items/<?=$deal['pic']?>.jpg" style="width:260px;max-height:300px;"></div>
								<h2 style="margin:10px 0px 0px 0px;"><?=$deal['name']?></h2>
								<div style="font-size:110%;padding-bottom:15px;">Set alert for your deal, we shall intimate you</div>
								<img src="<?=base_url()?>images/alertme.png" class="alertmetrig">
								<form class="alertme"><input type="hidden" name="id" value="<?=$deal['id']?>">
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