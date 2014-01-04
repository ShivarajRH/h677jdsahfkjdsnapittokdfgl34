<div class="container">

<h3>Priority and buyer note update for <?=$for?></h3>

<form method="post">
<table style="background:#fff;margin:15px;" cellpadding=10>

<tr>
<td>Buyer Note:</td><td><textarea name="note" cols=40><?=$note?></textarea>
</tr>

<tr>
<td>Priority :</td><td><select name="priority"><option value="yes" <?=$priority?"selected":""?>>high</option><option value="no" <?=!$priority?"selected":""?>>no</option></select>
</tr>

<tr>
<td>Priority Note:</td><td><textarea name="pnote" cols=40><?=$pnote?></textarea>
</tr>

<tr>
<td></td>
<td><input type="submit" value="Update">
</tr>

</table>
</form>

</div>
<?php
