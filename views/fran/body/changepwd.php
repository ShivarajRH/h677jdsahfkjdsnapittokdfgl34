<div class="container">
<h2>Change Password <?php if(isset($user)) echo "for {$user['name']}"?></h2>
<?php if(isset($error)){?>
<div style="background:#fff;margin:10px;padding:5px;color:red"><h3>Error</h3><?=$error?></div>
<?php }?>
<form id="cpas" method="post">
<table cellpadding=4 style="font-size:13px">
<?php if(!isset($user)){?>
<tr><td>Old Password :</td><td><input type="password" name="opass" class="inp"></td></tr>
<?php }else {?>
<input type="hidden" name="id" value="<?=$user['uid']?>">
<?php }?>
<tr><td>New Password :</td><td><input type="password" name="npass" class="inp"></td></tr>
<tr><td>Confirm New Password :</td><td><input type="password" name="cnpass" class="inp"></td></tr>
<tr><td></td><td><input type="submit" value="Change"></td></tr>
</table>
</form>
</div>
<script>
$(function(){
	$("#cpas").submit(function(){
		ef=true;
		$("input",$(this)).each(function(){
			if(!is_required($(this).val()))
				{alert("All fields mandatory");ef=false;return false;}
		});
		return ef;
	});
});
</script>
<?php
