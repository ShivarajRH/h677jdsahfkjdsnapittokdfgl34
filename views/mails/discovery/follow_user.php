<div style="width:741px;">
<img src="<?=IMAGES_URL?>email_hdr/email<?=rand(1,4)?>.png">



<div style="font-size:13px;margin:15px 35px;background:#F1F6F8;padding:7px;">
<div style="padding:10px;background:#fff;border:1px solid;-moz-border-radius:4px;border-radius:4px;border-color:#DEE6E9 #DEE6E9 #CCD6DB;">

Hi <?=$user?>,<br><br>

<?=$user2['name']?> started following all of your Lifestyle Discovery. <a href="<?=site_url("discovery/user/{$user2['username']}")?>">Follow <?=$user2['name']?> back?</a><br><br>

Tag your Lifestyle!<br>
<br>
- Snapittoday.com


</div>

</div>
</div>

</div>
<?php
