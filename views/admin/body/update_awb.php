<div class="container">
<h2>Update AWB Series</h2>

<form method="post">
<input type="hidden" name="courier" value="<?=$cid?>">
<table>
<tr><td>AWB Prefix :</td><td><input type="text" class="inp" size=3 name="awb_prefix" value="<?=$awb['awb_no_prefix']?>"></td></tr>
<tr><td>AWB Suffix :</td><td><input type="text" class="inp" size=3 name="awb_suffix" value="<?=$awb['awb_no_suffix']?>"></td></tr>
<tr><td>AWB Starting No :</td><td><input type="text" class="inp" size=12 name="awb_start" value="<?=$awb['awb_start_no']?>"></td></tr>
<tr><td>AWB Ending No :</td><td><input type="text" class="inp" size=12 name="awb_end" value="<?=$awb['awb_end_no']?>"></td></tr>
</table>
<input type="submit" value="Update">
</form>

</div>
<?php
