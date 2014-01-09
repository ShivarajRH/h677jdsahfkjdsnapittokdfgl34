<?php 
	$is_superadmin = $this->erpm->auth(true,true);
?>
<div class="container">
<h2>Cancel items in order & Notify user</h2>
<div style="padding:20px;">
<form method="post">
<input type="hidden" name="transid" value="<?=$_POST['transid']?>">
<input type="hidden" name="oids" value="<?=implode(",",$_POST['oids'])?>">
<table cellpadding=5>
<tr>
<td>Refund :</td><td>Rs <input type="text" <?php echo !$is_superadmin?'readonly':'' ?> name="refund" value="<?=$refund?>" class="inp" size="4"></td>
</tr>
<tr>
<td>Message :</td>
<td><textarea rows=10 cols=80 name="msg">
Dear <?=$user?>,

An amount of Rs <?=$refund?> has been refunded to your account as [PRODUCT_NAME] could not be included due to non-availability.

The refunded amount will reflect in your bank account within 5-7 bank working days.

The rest of your orders are being shipped today, the details of which will be sent to you shortly.

We apologize for the inconvenience.

-- 
Warm Regards
Team SnapItToday
</textarea><br>Note: This message will be sent to the user
<br><label><input type="checkbox" name="no_send" value="1">Don't notify user</label>
</td>
</tr>
<tr>
<td></td>
<td><input type="submit" value="Confirm Cancel"></td>
</tr>
</table>
</form>
</div>

</div>
<?php
