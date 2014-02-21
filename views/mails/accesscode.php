<div style="width:741px;">

<img src="<?=IMAGES_URL?>logowhite.png">

<div style="font-size:13px;margin:15px 35px;background:#F1F6F8;padding:7px;">
<div style="padding:10px;background:#fff;border:1px solid;-moz-border-radius:4px;border-radius:4px;border-color:#DEE6E9 #DEE6E9 #CCD6DB;">

Dear <?=$name?>,
<br><br>

Please click on below link to activate your account at SnapItToday.com<br>
<a href="<?=site_url("verifyh/".md5($code))?>"><?=site_url("verifyh/".md5($code))?></a><br>
<br><br>
Warm Regards,<br>
Snapittoday.com

<br><br>
<i>(For any assistance please contact us at <?=CS_EMAIL?>)</i>

</div>
</div>

</div>