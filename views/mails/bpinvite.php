<div style="width:741px;">

<img src="<?=IMAGES_URL?>logowhite.png">


<div style="font-size:13px;margin:15px 35px;background:#F1F6F8;padding:7px;">
<div style="padding:10px;background:#fff;border:1px solid;-moz-border-radius:4px;border-radius:4px;border-color:#DEE6E9 #DEE6E9 #CCD6DB;">

Dear <?=$name?>,<br><br>
I am buying '<b style="color:green;"><?=$item['name']?></b>' at www.snapittoday.com<br><br>
If we have <?=$total_qty?> buyers, we can have a cashback of <b style="color:red">Rs <?=$refund?></b> from the product price <b style="color:green">Rs <?=$price?></b>.
<br><br>
Since this is a limited period offer, the cash-back is valid only for purchases done before <b><?=date("d M Y g:ia",$expires)?></b> using the below link<br><br>
<a href="<?=site_url("buy/$hash")?>"><?=site_url("buy/$hash")?></a>
<br>
<br>
Please join hands with me to buy this product.<br><br>
Thanks!<br>
Snapittoday.com Team
<br><br>
<div style='font-size:80%;color:#777;'>
This email was sent by Snapittoday.com on behalf of <?=$user['name']?><br>
Terms & conditions apply,<br>
E.O.E.
</div>

</div>
</div>

</div>