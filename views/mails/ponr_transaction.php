<div style="width:741px;">

<img src="<?=IMAGES_URL?>logowhite.png">

<div style="font-size:13px;margin:15px 35px;background:#F1F6F8;padding:7px;">
<div style="padding:10px;background:#fff;border:1px solid;-moz-border-radius:4px;border-radius:4px;border-color:#DEE6E9 #DEE6E9 #CCD6DB;">

Dear Customer,
<br><br><br>
Your recent transaction <b><?=$transid?></b> didn't complete successfully.<br>
<br>
These were the items in your cart<br>
<table style="font-size:inherit" cellpadding=3 border=1 cellspacing=0>
<tr>
<th>Product Name</th><th>Quantity</th>
</tr>
<?php foreach($orders as $o){?>
<tr><td><?=$o['name']?></td><td><?=$o['quantity']?></td></tr>
<?php }?>
</table>
<br><br>
Your cart is saved in your account. Please log into your account and complete the order.<br>
In case you found any problem with your payment or orders, please send a email to support@snapittoday.com
<br><br><br>
Happy Shopping!
<br>
Snapittoday Team

<br><br><br>

<i>(For any assistance please contact us at <?=CS_EMAIL?>)</i>




</div>
</div>

</div>
<?php
