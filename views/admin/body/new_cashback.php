<div class="container">
<h2>Create new cashback campaign</h2>

<form method="post">

<table cellpadding=5>

<tr>
<td>Cashback percentage</td><td>:</td><td><input type="text" name="cashback" size=4>%</td>
</tr>

<tr>
<td>Min Transaction amount</td><td>:</td><td>Rs <input type="text" name="min" size=6></td>
</tr>

<tr>
<td>No of coupons</td><td>:</td><td><input type="text" name="coupons" size=4></td>
</tr>

<tr>
<td>Coupon validity</td><td>:</td><td><input type="text" name="c_valid" size=3> days</td>
</tr>

<tr>
<td>Coupon Min order</td><td>:</td><td>Rs <input type="text" name="c_min" size=6></td>
</tr>

<tr>
<td>Starts in</td><td>:</td>
<td><select name="starts_h">
<?php for($i=0;$i<24;$i++){?>
<option value="<?=$i?>"><?=$i>=10?$i:"0$i"?></option>
<?php }?>
</select>:<select name="starts_m">
<?php for($i=0;$i<60;$i++){?>
<option value="<?=$i?>"><?=$i>=10?$i:"0$i"?></option>
<?php }?>
</select> <input type="text" name="starts_d" id="start">
</tr>

<tr>
<td>Expires on</td><td>:</td>
<td><select name="expires_h">
<?php for($i=0;$i<24;$i++){?>
<option value="<?=$i?>"><?=$i>=10?$i:"0$i"?></option>
<?php }?>
</select>:<select name="expires_m">
<?php for($i=0;$i<60;$i++){?>
<option value="<?=$i?>"><?=$i>=10?$i:"0$i"?></option>
<?php }?>
</select> <input type="text" name="expires_d" id="expires">
</tr>

<tr>
<td colspan=3 align="right">
<input type="submit" value="Create">
</td>
</tr>

</table>

</form>

</div>

<script>
$(function(){
	$("#expires, #start").datepicker({ dateFormat: 'dd-mm-yy' });
});
</script>

<?php
