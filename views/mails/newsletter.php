<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
</head>
<body>
<style>
p{
margin:0px;padding:0px;
}
</style>

<div align="center" style="font-family:arial;font-size:13px;">
	<div style="width:740px;border:1px solid #eee;" align="left">


		<div style="padding:7px;">
			<div style="float:right;font-size:120%;padding-top:20px;">A new-age ecommerce which helps you to<br>shop along with your coworkers</div>
			<a href="<?=base_url()?>"><img alt="Logo" src="<?=IMAGES_URL?>logo.png" style="border:0px;"></a>
		</div>
		<div align="center" style="margin-top:10px;padding:10px 0px;border:1px solid #eee;border-width:2px 0px 2px 0px;">
			<img src="<?=IMAGES_URL?>nl_groupbuy.png" alt="Group Buying">
		</div>
		<div align="left" style="padding-top:7px;font-size:120%;">
			<h2 style="margin:3px;font-family:trebuchet ms;"><?=$dod['name']?></h2>
			<table width="100%" cellpadding=0 style="font-size:inherit;border:1px solid #eee;border-width:1px 0px;" cellspacing=0>
				<tr>
					<td width=200><a href="<?=site_url($dod['url'])?>"><img alt="<?=htmlspecialchars($dod['name'])?>" src="<?=IMAGES_URL?>items/<?=$dod['pic']?>.jpg" style="border:0px;"></a></td>
					<td align="center" valign="middle" style="padding:5px 10px;background:#001321;color:#fff;">
						<div style="border-bottom:2px solid #001B30;padding:5px;">
							<h1 style="margin:0px;color:yellow;">Rs <?=$dod['price']?></h1>
							<div style="font-size:11px;">invite coworkers and get this @ <b style="color:#7FB319"><?=$this->dbm->getslotprice($dod['slots'],100)?></b></div>
						</div>
						<div style="border-bottom:2px solid #001B30;padding:10px;">
							<h4 style="margin:0px;">MRP <span style="text-decoration:line-through"><?=$dod['orgprice']?></span></h4>
						</div>
						<div style="border-bottom:2px solid #001B30;padding:10px;">
							<table width="100%" style="font-size:inherit;color:#fff;font-weight:bold;">
								<tr>
									<td width="50%" align="center"><b style="color:#7FB319"><?=ceil(($dod['orgprice']-$dod['price'])/$dod['orgprice']*100)?>%</b> OFF</td>
									<td width="50%" align="center">SAVE <b style="color:#7FB319">Rs <?=ceil(($dod['orgprice']-$dod['price']))?></b></td>
								</tr>
							</table>
						</div>
						<div style="border-bottom:2px solid #001B30;padding:10px;">
							SHIPS IN <?=$dod['shipsin']?> DAYS
						</div>
						<div style="font-weight:bold;border-bottom:2px solid #001B30;padding:10px;">
							(Inclusive of Taxes + <b style="color:#7FB319">FREE SHIPPING</b>)
						</div>
						<div style="font-weight:bold;padding:7px;">
							<h2 style="color:yellow">Just for today</h2>
						</div>
					</td>
				</tr>
			</table>
			<div style="padding-left:5px;font-size:12px;padding-top:5px;"><?=$dod['description1']?></div>
		</div>
		<div class="homecont" style="margin-top:20px;padding-top:10px;font-size:12px;">
			<h3 style="padding-left:5px;margin-bottom:0px">You might also be interested in</h3>
			<table width="100%" class="prdt_table" style="margin-top:10px;font-size:inherit;">
				<tr>
<?php foreach($deals as $i=>$deal){?>		
						<td width="25%" class="dealdlist" style="border-right:1px solid #ccc;text-align:center;padding:10px;width:160px;vertical-align:top;<?php if($i==3) echo "border:0px;"?>">
							<div class="imgcont" style="padding-top:5px;height:140px;"><a href="<?=site_url("{$deal['url']}")?>"><img src="<?=IMAGES_URL?>items/small/<?=$deal['pic']?>.jpg" width=160></a></div>
							<div class="namecont" style="padding-top:5px;height:87px;position:relative;overflow:hidden;font-weight:bold;">
								<div style="margin-bottom:5px;padding-top:5px;"><a class="at" href="<?=site_url($deal['caturl'])?>" style="background:#eee;padding:3px 5px;font-weight:normal;color:#000;-moz-border-radius:5px;border-radius:5px;text-decoration:none;display:inline-block;text-decoration:none"><?=$deal['category']?></a></div>
								<div style="font-size:120%;padding-top:5px;padding:5px 0px;"><?=$deal['name']?></div>
							</div>
							<div class="price" style="padding-top:5px;color:red;font-size:140%;"><b>Rs <?=number_format($deal['price'])?></b></div>
							<div class="save" style="padding-top:5px;font-size:90%;padding:0px;"><b style="color:red;font-size:125%;">save <?=ceil(($deal['orgprice']-$deal['price'])/$deal['orgprice']*100)?>% OFF</b></div>
							<div class="mrp" style="padding-top:5px;padding-top:10px;color:#555;"><span style="text-decoration:line-through">Rs <?=$deal['orgprice']?></span> list price</div>
						</td>
<?php }?>
				</tr>
			</table>
		</div>

		<div style="background:#D7D7D8;padding:5px;">
			<table width="100%" style="font-size:inherit;font-weight:bold;color:#333;" cellpadding=0>
				<tr>
					<td width="33%" align="left">
						support@snapittoday.com
					</td>
					<td width="33%" align="center">
						connect with us <a href="http://facebook.com/snapittoday"><img src="<?=IMAGES_URL?>fb_small.png"></a> <a href="http://twitter.com/snapittoday"><img src="<?=IMAGES_URL?>tw_small.png"></a>
					</td>
					<td width="33%" align="right">
						www.snapittoday.com
					</td>
				</tr>
			</table>
		</div>
		
		<div style="padding:5px;color:#777;font-size:12px;">
			About snapittoday.com<br>
			We at Snapittoday.com strive to offer our customers the best deals at the lowest rates we can afford. We aim to be the fastest selling retailer in India.<br>
			You received this email from Snapittoday.com If you'd no longer like to receive our emails, please unsubscribe here.
			<div style="float:right">&copy; 2011 Snapittoday.com * Bangalore, Karnataka, India</div> 
			<div>Please view our <a href="<?=site_url("privacy_policy")?>" style="color:blue">Privacy Policy</a>.</div>
		</div>



	</div>
</div>
</body>
</html>