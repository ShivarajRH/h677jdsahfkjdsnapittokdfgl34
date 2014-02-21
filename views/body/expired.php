<?php
if(isset($itemdetails['enddate']))
$ed=$itemdetails['enddate'];
$item=$itemdetails;
?>

<div id="infoc" style="display:none">
<div style="font-size:15px;">
<div class="infoh" style="color:#ff9900;font-size:25px;font-weight:bold;padding-bottom:7px;">Info</div>
<div class="infocnt" id="info"></div>
</div>
</div>
<div id="video" style="display:none;"></div>

<div class="itemmaincont" align="center">
	<div class="topitem" align="center">
		<div class='container' style="margin-left:30px;">
				
				<div class="itemheading"><h1><?=$itemdetails['name']?></h1><div style="font-weight: normal;font-size: 13px;margin-top: -17px;">from <b><?=$itemdetails['brandname']?></b></div></div>
				
				<div class="itempic">
					<div style="border-bottom:5px solid #43B9D6;width:100%;height:299px;position:relative;overflow:hidden;">
						<a rel="photos" href="<?=base_url()?>images/items/big/<?=$itemdetails['pic']?>.jpg" class="fanblink itemphotos">
							<img src="<?=base_url()?>images/items/<?=$itemdetails['pic']?>.jpg" style="width:398px;">
						</a>
						<?php 
						$itemresources[0][]=array("id"=>$itemdetails['pic']);
						if(count($itemresources[0])>0)
						foreach($itemresources[0] as $pic){?>
						<a id="itemphoto<?=$pic['id']?>" rel="photos" style="display:none;padding:3px;" href="<?=base_url()?>images/items/big/<?=$pic['id']?>.jpg" class="fanblink itemphotos">
						<img src="<?=base_url()?>images/items/<?=$pic['id']?>.jpg" style="width:398px;"></a>
						<?php 
						}
						?>
					</div>
					<div id="photosvideos" style="clear:left;float:left;">
						<div style="clear:left;float:left;">
							<a name="extraphotos"></a>
							<div style="padding-top: 0px; width: 100%; text-align: left;">
								<?php 
								if(count($itemresources[0])>0)
								foreach($itemresources[0] as $pic){?>
								<a style="padding:3px;" href="javascript:void(0)" onclick='showitemphoto("<?=$pic['id']?>")'><img src="<?=base_url()?>images/items/thumbs/<?=$pic['id']?>.jpg" height="50" style="background:#fff;border:1px solid #eee;"></a>
								<?php 
								}
								?>
								<?php 
								if(count($itemresources[1])>0)
									echo '<div style="padding-top:10px;padding-bottom:2px;font-size:19px;border-bottom:2px solid #ccc;">Videos</div>';
								foreach($itemresources[1] as $pic){?>
								<a vidid="<?=$pic['id']?>" href="#video" class="vlink"><img src="http://i3.ytimg.com/vi/<?=$pic['id']?>/default.jpg" width="100" style="border:1px solid #eee;"></a>
								<?php 
								}
								?>
							</div>
						</div>
					</div>
				</div>

					<div align="left" style="padding:0px 10px;padding-left:15px;">
						<div style="float:left;margin-top:2px;margin-right:5px;"><g:plusone size="large" count=false></g:plusone></div>
						<fb:like href="<?=site_url("deal/".$itemdetails['url'])?>" send="false" action="recommend" width="350" show_faces="false" font=""></fb:like>
					</div>
			

				<div class="itempricedet">
					<div class="pricebar">
						<div style="border-bottom:1px solid #eee;padding:5px 20px;">
							<div class="snapit">Snap it today @ <br><b>Rs. <?=number_format($item['price'])?></b></div>
							<input type="hidden" name="qty" id="qty" value="1">
							<img src="<?=base_url()?>images/soldout.png" style="margin-left:20px;">
							<div class="clear"></div>
						</div>
						<div align="center" class="cod">
							Now you can pay with <b>Cash On Delivery</b>
							<div align="right" class="bang">Currently in Bangalore</div>
						</div>
						<table width="100%" class="vdiscsa">
							<tr>
								<td width="33%" align="center">
									<span class="disch">Value</span>
									<div class="value">Rs <?=$item['orgprice']?></div>
								</td>
								<td width="33%" align="center">
									<span class="disch">Discount</span>
									<div class="disc"><?=ceil(($item['orgprice']-$item['price'])/$item['orgprice']*100)?>%</div>
								</td>
								<td width="33%" align="center">
									<span class="disch">Save</span>
									<div class="save">Rs <?=($item['orgprice']-$item['price'])?></div>
								</td>
							</tr>
						</table>
					</div>
					<div class="dealends" style="background:#F7DF00;height:48px;width:490px;">
						<div style="font-size:110%;color:#444;"><div style="float:left;margin-top:15px;margin-left:20px;">Still interested in this deal?</div> 
							<a class="fancylink" href="#placearequestexp"><img src="<?=base_url()?>images/placearequest.png" style="cursor:pointer;float:right;margin-top:7px;margin-right:30px;"></a>
							<div style="display:none;">
								<div id="placearequestexp" style="background:#1C1C1C;color:#fff;">
									<form class="requestme" style="margin:0px;">
										<h3>Place a request</h3>
										<input type="hidden" name="id" value="<?=$deal['id']?>">
										<table width="100%" cellpadding=10>
										<tr><td>Mobile :</td><td> <input type="text" name="mobile" class="alrt_m_input" style="width:auto;" value="Enter mobile"></td></tr>
										<tr><td>Email :</td><td><input type="text" name="email" class="alrt_e_input" style="width:auto;" value="Enter email"></td></tr>
										<tr><td></td><td><input type="image" src="<?=base_url()?>images/submit.png"></td></tr>
										</table
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			
				<div style="clear:both;"></div>
			
		</div>	
	</div>	
	<div align="center">
	<div class="container">	
		<div class="bottomitem">
			<div style="float:right;font-size:13px;width:487px;padding-right:10px;" align="left">
			<div style="padding-top:10px;font-weight:bold;padding-bottom:2px;font-size:15px;border-bottom:2px solid #ccc;">Description</div>
			<div style="text-align: justify;font-size: 110%;">
			<?=$itemdetails['description1']?>
			<div style="padding-left:30px;padding-top:5px;">
			<?=$itemdetails['description2']?> </div>
			</div>
			</div>
			
			<div style="float:left;font-size:13px;margin-left:10px;width:383px;padding-right:10px;" align="left">
			
				<div style="clear:left;float:left;padding-right: 20px; padding-left: 0px;">
					<div style="padding-top:10px;padding-bottom:2px;font-size:15px;font-weight:bold;border-bottom:2px solid #ccc;">Leave Comments</div>
					<div id="fb-root"></div>
					<script>  window.fbAsyncInit = function() {    FB.init({status: true, cookie: true,             xfbml: true});  };  (function() {    var e = document.createElement('script'); e.async = true;    e.src = document.location.protocol +      '//connect.facebook.net/en_US/all.js';    document.getElementById('fb-root').appendChild(e);  }());</script>
					<fb:comments href="<?=site_url("deal/".$itemdetails['url'])?>" num_posts="2" width="383"></fb:comments>
				</div>
			
			</div>
			<div style="clear:both"></div>
		
		</div>
	</div>
	</div>
</div>
<style>
#placearequestexp{
	padding:20px;
	width:300px;
	height:210px;
}
#placearequestexp .requestme{
	padding-top:30px;
	display:block;
}
}
</style>
<script>var itemid="<?=$itemdetails['id']?>";</script>