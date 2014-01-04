<?php $u=false; if(isset($auser)) $u=$auser;?>
<div class="container">
<h2><?=$u?"Update":"Add"?> Admin user</h2>
<?php 
	
?>
<br><Br>
<form method="post">
<table cellpadding=5>
<?php if(!$u){?>
<tr><td>Username :</td><td><input type="text" class="inp" name="username" value="<?=$u?$u['username']:""?>"></tr>
<?php }?>
<tr><td>Name :</td><td><input type="text" class="inp" name="name" value="<?=$u?$u['name']:""?>"></tr>
<tr><td>Email :</td><td><input type="text" class="inp" name="email" value="<?=$u?$u['email']:""?>" size="30"></tr>
<tr><td>User Roles :</td><td>
<?php foreach($roles as $r){?>
<label style="background:#eee;padding:3px 5px;margin:2px;display:inline-block;"><input type="checkbox" <?php if($u){?> <?=((double)$u['access']&(double)$r['value'])>0?"checked":""?><?php }?> name="roles[]" value="<?=$r['value']?>"><?=$r['user_role']?></label>
<?php }?>
</td>
</tr>
<?php if($u){
	$account_blocked = $this->db->query("select account_blocked from king_admin where id= ?  ",$u['id'])->row()->account_blocked;
?>
<tr>
	<td>Cancel/Block Account</td>
	<td>
		<input type="checkbox" value="1" name="account_blocked" <?php echo $account_blocked?'checked':'' ?> />
	</td>
</tr>
<?php }?>
<tr><td></td><td><input type="submit" value="<?=$u?"Update":"Add"?> user"></td></tr>
</table>
</form>

</div>
<?php
