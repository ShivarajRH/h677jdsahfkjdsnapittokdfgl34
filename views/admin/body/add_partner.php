<?php $p=false;
if(isset($partner))
	$p=$partner;
?>
<div class="container">

<h2>Add new partner</h2>

<form method="post">
<?php if($p){?><input type="hidden" name="pid" value="<?=$p['id']?>"><?php } ?>
<table cellpadding=5>
<tr><td>Name :</td><td><input type="text" class="inp" name="name" size=50 value="<?=$p?$p['name']:""?>"></td></tr>
<tr><td>Transaction Prefix :</td><td><input type="text" size=3 maxlength="3" class="inp" name="trans_prefix" value="<?=$p?$p['trans_prefix']:""?>"></td></tr>
<tr><td>Transaction Mode :</td><td>
<select name="trans_mode">
<?php foreach(array("PG","COD","Custom1","Custom2","Custom3","Custom4") as $mv=>$m){?>
<option value="<?=$mv?>" <?=$p&&$mv==$p['trans_mode']?"selected":""?>><?=$m?></option>
<?php }?>
</select>
</td></tr>
<tr><td></td><td><input type="submit" value="<?=$p?"Update":"Add"?> Partner"></td></tr>
</table>

</form>

</div>
<?php
