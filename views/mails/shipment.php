<div style="width:741px;">

<img src="<?=IMAGES_URL?>logowhite.png">

<div style="font-size:13px;margin:15px 35px;background:#F1F6F8;padding:7px;">
<div style="padding:10px;background:#fff;border:1px solid;-moz-border-radius:4px;border-radius:4px;border-color:#DEE6E9 #DEE6E9 #CCD6DB;">

Dear <?=$name?>,
<br><br>
Greetings from Snapittoday.com!<br><br>
The following items of your order <?=$transid?> are shipped<br>
<table style="font-size:inherit;margin-top:15px;">
<?php foreach($prods as $p){?>
<tr>
<td><?=$p['name']?></td>
<td>&nbsp;&nbsp; &nbsp; <?=$p['quantity']?></td>
</tr>
<?php }?>
</table> 
<br>
Your consignment is sent through <b><?=$medium?></b> and you will receive your products in next couple of days.<br>
Consignment Tracking ID : <b><?=$trackingid?></b><br><br>
<br>
<?php if($partial){?>
Remaining items of your order <?=$transid?> will be shipped as soon as possible. You will receive notification when all your ordered items are shipped.
<?php }else{?>
Please note that this concludes your order <?=$transid?>. All your ordered items are shipped.
<?php }?>
<br><br>
We thank you again for your shopping with us.<br>We look forward to serve you again<br><br>
Have a nice day!
<br><br>
Team Snapittoday.com

</div>
</div>

</div>

<?php
