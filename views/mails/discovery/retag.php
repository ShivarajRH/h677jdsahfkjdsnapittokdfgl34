<div style="width:741px;">
<img src="<?=IMAGES_URL?>email_hdr/email<?=rand(1,4)?>.png">



<div style="font-size:13px;margin:15px 35px;background:#F1F6F8;padding:7px;">
<div style="padding:10px;background:#fff;border:1px solid;-moz-border-radius:4px;border-radius:4px;border-color:#DEE6E9 #DEE6E9 #CCD6DB;">


Hi <?=$user?>,

<a href="<?=site_url("discovery/user/{$user2['username']}")?>"><?=$user2['name']?></a> re tagged  your pin, '<a href="<?=site_url("discovery/tags/{$tag['url']}")?>"><?=$tag['name']?></a>'.

Tag your lifestyle

-Snapittoday.com"


</div>

</div>
</div>

</div>
<?php
