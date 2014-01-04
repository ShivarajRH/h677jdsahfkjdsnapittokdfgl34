<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>Snapittoday promo email</title>
</head>
<body style="margin:0px;padding:0px;">

<div style="background:#F2F2F2;font-family:arial;font-size:13px;" align="center">

	<div style="width:690px;background:#fff;" align="center">
		<div style="width:620px;" align="left">
			<div align="right" style="font-style:italic;font-size:11px;">
				<div style="color:#444;">If you are unable to see this mail, please click <a href="<?=$_SERVER['REQUEST_URI']?>">here</a></div>
				<div style="color:#999;">Please add contact@snapittoday.com to your address book<br>to ensure delivery of this email</div>
			</div>
			<div style="margin-top:10px;padding-bottom:20px;border-bottom:1px dotted #aaa;">
				<img src="<?=IMAGES_URL?>discover_redefine_text.png" style="float:right;margin-top:20px;">
				<a href="<?=base_url()?>"><img src="<?=IMAGES_URL?>logowhite.png"></a>
			</div>
			<div style="border-top:1px dotted #aaa;margin-top:2px;padding-top:20px;">
				<h2 style="margin:0px;font-size:18px;">
					<a href="<?=base_url()?>" style="float:right"><img src="<?=IMAGES_URL?>view_all.gif"></a>
					Featured <span style="color:red;">Brands</span>
				</h2>
				<div style="clear:both;padding-top:5px;border-bottom:1px dotted #aaa;padding-bottom:15px;margin-bottom:2px;">
					<table cellpadding=0 cellspacing=0 border=0 style="font-size:inherit">
						<tr>
<?php foreach($brands as $i=>$b){?>					
							<td height="150" width="150" style="padding-bottom:3px;padding-right:3px;">
								<table width="150" height="150" background="<?=IMAGES_URL?>items/small/<?=$b['pic']?>.jpg" cellpadding=0 cellspacing=0 style="background-color:#aaa;font-size:inherit;">
									<tr>
										<td height="110">&nbsp;</td>
									</tr>
									<tr>	
										<td height="40" background="<?=IMAGES_URL?>alpha.png" style="padding:7px;height:40px;overflow:hidden;">
											<a style="text-decoration:none;color:#fff;" href="<?=site_url("featured_brand/{$b['url']}")?>"><?=$b['brand']?></a>
										</td>
									</tr>
								</table>
							</td>
<?php if(($i+1)%4==0) echo '</tr><tr>';}?>
						</tr>
					</table>
				</div>
				<div style="border:1px dotted #aaa;border-width:1px 0px;padding-top:20px;margin-bottom:2px;padding-bottom:15px;">
					<h2 style="margin:0px;font-size:18px;">Discover <span style="color:red;">Products</span></h2>
					<table width="100%" style="font-size:inherit">
						<tr>
<?php foreach($items as $i=>$item){?>
							<td width="300" height="300">
								<table width="300" height="300" cellspacing=0 cellpadding=0 background="<?=IMAGES_URL?>items/300/<?=$item['pic']?>.jpg" style="background-color:#aaa;font-size:inherit;">
									<tr>
										<td width="300" height="250">&nbsp;</td>
									</tr>
									<tr>
										<td height="50" background="<?=IMAGES_URL?>alpha.png" style="padding:7px;height:40px;overflow:hidden;">
											<a style="margin-top:10px;float:right" href="<?=site_url($item['url'])?>"><img src="<?=IMAGES_URL?>snap_arrow.png"></a>
											<a href="<?=site_url($item['url'])?>" style="text-decoration:none;color:#fff;"><b><?=$item['name']?></b></a>
										</td>
									</tr>
								</table>
							</td>
<?php if(($i+1)%2==0) echo '</tr><tr>'; }?>
						</tr>
					</table>
				</div>
				<div style="border-top:1px dotted #aaa;padding-bottom:10px;">
					<a href="http://facebook.com/snapittoday" style="float:right;display:block;margin:4px 3px;"><img src="<?=IMAGES_URL?>big_facebook.png"></a>
					<a href="http://twitter.com/snapittoday" style="float:right;display:block;margin:4px 3px;"><img src="<?=IMAGES_URL?>big_twitter.png"></a>
					<table cellpadding=5 cellspacing=0 style="font-size:11px;">
						<tr>
							<td><a style="color:#000;text-decoration:none;" href="<?=site_url("privacy_policy")?>">Privacy</a></td>
							<td><a style="color:#000;text-decoration:none;" href="<?=site_url("shipping_policy")?>">Shipping</a></td>
							<td><a style="color:#000;text-decoration:none;" href="<?=site_url("cancellation_policy")?>">Returns</a></td>
							<td><a style="color:#000;text-decoration:none;" href="<?=site_url("terms")?>">Terms of service</a></td>
							<td><a style="color:#000;text-decoration:none;" href="<?=site_url("about_us")?>">About</a></td>
							<td>hello@snapittoday.com</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>


</div>


</body>
</html>