<div style="width:741px;">
<img src="<?=IMAGES_URL?>email_hdr/email<?=rand(1,4)?>.png">


<div style="font-size:13px;margin:15px 35px;background:#F1F6F8;padding:7px;">
<div style="padding:10px;background:#fff;border:1px solid;-moz-border-radius:4px;border-radius:4px;border-color:#DEE6E9 #DEE6E9 #CCD6DB;">


Dear Customer, <br>
<br>Your order <?=$transid?> has been shipped.<br>
<br>
Here are the shipment details:
<br><br>
Transaction ID : <b><?=$transid?></b> <br>
Shipped through : <b><?=$courier?></b> <br>
Consignment Tracking No. : <b><?=$awn?></b><br>
Shipped on : <b><?=date("d M y",$shippedon)?></b>
<br><br>The Consignment Tracking No can be used to track your shipment on the mentioned Courier Company's website.<br>
<br>

Product List : <br>
TBL_DISPLAY_SHIPPED_ORDERS
<br/>

Thank you for shopping with SnapItToday.com. We hope to hear from you soon! 
<br><br>

<br><br>
Here’s what’s new at SnapItToday.com!<br><br><br>

<table cellpadding=5 cellspacing=0 border=0 style="font-size:inherit">
<tr>
<th>Re-order</th>
<th>Fav 5's</th>
</tr>
<tr>
<td valign="center">
Did you know you get <?=REORDER_DISCOUNT?>% off on MRP each time you re-order with in <?=REORDER_VALID_DAYS?>Days of previous purchase ?<br><br>
Log into your <a href="<?=site_url("profile")?>">account</a> and click on re-order. Its that simple.
</td>
<td valign="center">
Choose your Favourite 5 products and get <?=FAV_DISCOUNT?>% off on every purchase!
</td>
</tr>
</table>

<br>
Refer your friends to Snapittoday.com and you get a coupon code for your next shopping - isn't it that simple!!
<br>


<BR><BR>
Warm Regards,
Team Snapittoday.com<br>
<br>
<i>(For any assistance please contact us at <?=CS_EMAIL?>)</i> 
 
</div>
</div>

</div>