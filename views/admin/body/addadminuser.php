<!--
	@desc Validations added
	@last_modify shivaraj<shivaraj@storeking.in>_Oct_16_2014
-->
<?php $u=false; if(isset($auser)) $u=$auser;?>
<div class="container">
<h2><?=$u?"Update":"Add"?> Admin user</h2>
<?php 
	
?>
<br><Br>
<form method="post" onsubmit="return validate_formdata(this);">
<table cellpadding=5>
<?php if(!$u){?>
<tr>
	<td>Username :</td>
	<td><input type="text" class="inp" name="username" value="<?=$u?$u['username']:""?>">
		<span style="color: red;"> <?php if(validation_errors()) { echo form_error('username');}?>
		</span>
	</td>
</tr>
<?php }?>
<tr><td>Name :</td><td><input type="text" class="inp" name="name" value="<?=$u?$u['name']:""?>"><span style="color: red;"> <?php if(validation_errors()) { echo form_error('name');}?></span></tr>
<tr><td>Email :</td><td><input type="text" class="inp" name="email" value="<?=$u?$u['email']:""?>" size="30"><span style="color: red;"> <?php if(validation_errors()) { echo form_error('email');}?></span></tr>
<tr><td>User Roles :</td><td>
<?php foreach($roles as $r){?>
		<label style="background:#eee;padding:3px 5px;margin:2px;display:inline-block;"><input type="checkbox" <?php if($u){?> <?=((double)$u['access']&(double)$r['value'])>0?"checked":""?><?php }?> name="roles[]" value="<?=$r['value']?>" class="roles"><?=$r['user_role']?></label>
<?php }?>
</td>
</tr>
<?php if($u){
	$user_det_row = $this->db->query("select block_ip_addr,account_blocked from king_admin where id= ?  ",$u['id'])->row_array();
?>
<tr>
	<td>Cancel/Block Account</td>
	<td>
		<input type="checkbox" value="1" name="account_blocked" <?php echo $user_det_row['account_blocked']?'checked':'' ?> />
	</td>
</tr>
<tr>
	<td>Block IP Address</td>
	<td>
		<input type="checkbox" value="1" name="block_ip_addr" <?php echo $user_det_row['block_ip_addr']?'checked':'' ?> />
	</td>
</tr>
<?php }?>
<tr><td></td><td><input type="submit" name="submit" value="<?=$u?"Update":"Add"?> user"></td></tr>
</table>
</form>

</div>
<script>
	function validate_formdata(elt)
	{
		var username=$("input[name='username']",$(elt)).val();
		var name=$("input[name='name']",$(elt)).val();
		var email=$("input[name='email']",$(elt)).val();
		var elt_roles=$(".roles:checked",$(elt));
		var err=0;
		var err_msg=[];
		
		if(username != undefined )
		{
			if(username=='' || !is_nospace(username) )
			{
				err_msg.push("Username is required OR Invalid username.");
				err=1;
			}
		}
		if(name=='')
		{
			err_msg.push("Name is required.");
			err=1;
		}
		if(email=='' || !is_email(email))
		{
			err_msg.push("Email is empty OR Invalid Email.");
			err=1;
		}
		if(elt_roles.length==0)
		{
			err_msg.push("Assign atleast one role.");
			err=1;
		}
		
		if(err==1){
			var mstr='Please check following errors:\n';
			$.each(err_msg,function(i,msg) {
				mstr +="\n"+(++i)+". "+msg;
			});
			alert(mstr);
			return false;
		}
		return true;
	}
</script>
<?php
