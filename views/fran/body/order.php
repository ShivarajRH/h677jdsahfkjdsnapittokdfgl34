<div class="container">
<h2 style="padding-bottom:10px">Place Order</h2>
<form method="post">
<table cellpadding=5 border=0>
<tr>
<td>Quantity :</td>
<td>
<select class="inp" style="width:auto;">
<?php for($i=1;$i<=10;$i++){?>
<option value="<?=$i?>"><?=$i?></option>
<?php }?>
</select>
</td>
</tr>
<tr>
<td>Ship to person :</td>
<td><input type="text" name="s_person" value="<?=$fran['name']?>" class="inp"></td>
</tr>
<tr>
<td valign="top">Shipping Address :</td>
<td>
<textarea name="s_address" class="inp" style="width:300px;height:90px;"><?=$fran['address']?></textarea>
</td>
</tr>
<tr>
<td>City :</td>
<td><input type="text" name="s_city" class="inp"></td>
</tr>
<tr>
<td>Pincode :</td>
<td><input type="text" name="s_pin" class="inp"></td>
</tr>
<tr>
<td></td>
<td>
<input type="submit" value="Place Order">
</td>
</tr>
</table>
<input type="hidden" name="b_person" value="<?=htmlspecialchars($fran['name'])?>">
<input type="hidden" name="b_address" value="<?=htmlspecialchars($fran['address'])?>">
<input type="hidden" name="b_city" value="<?=htmlspecialchars($fran['city'])?>">
<input type="hidden" name="b_pin" value="">

</form>
</div>
<?php
