<div style="width:741px;">

<img src="<?=IMAGES_URL?>logowhite.png">



<div style="font-size:13px;margin:15px 35px;background:#F1F6F8;padding:7px;">
<div style="padding:10px;background:#fff;border:1px solid;-moz-border-radius:4px;border-radius:4px;border-color:#DEE6E9 #DEE6E9 #CCD6DB;">


Dear <?=$name?>, <br><br>

Welcome to <a href="<?=base_url()?>">SnapItToday</a>! - the one stop shop for all your Beauty and Healthcare needs. 
<br>
Your <a href="<?=base_url()?>">SnapItToday</a> account has been created.<br> 
We’re thrilled that you’ve chosen us, so get ready to experience a whole new world of shopping!
<br><br>
Please click on the link below to activate your <a href="<?=base_url()?>">SnapItToday</a> account.<br>
<a href="<?=site_url("verifyh/".md5($code))?>"><?=site_url("verifyh/".md5($code))?></a>
<br><br><br>

Here’s what’s new at SnapItToday.com!<br><br>

<table cellpadding=5 cellspacing=0 border=0 style="font-size:inherit">
<tr>
<th>Re-order</th>
<th>Fav 5's</th>
</tr>
<tr>
<td valign="center">
Did you know you get <?=REORDER_DISCOUNT?>% off each time you re-order a previous purchase?<br><br>
Log into your <a href="<?=site_url("profile")?>">account</a> and click on re-order. Its that simple.
</td>
<td valign="center">
Choose your Favourite 5 products and get <?=FAV_DISCOUNT?>% off on every purchase!
</td>
</tr>
</table>

<br>
Refer your friends to Snapittoday.com and you get a coupon code for your next shopping - isn't it that simple!!
<br><br><br>
Warm Regards,<br>
Team SnapitToday.com

<br><br>
<i>(For any assistance please contact us at <?=CS_EMAIL?>)</i>


</div>
</div>

</div>