<form action="<?=site_url("franchisee/login")?>" method="post">
<?php if(isset($error)){?>
<h3 style="color:red">Invalid username or password</h3>
<?php }?>
<table style="font-size:14px;">
<tr>
<td>User Name :</td>
<td><input type="text" class="inp" name="fran_username"></td>
</tr>
<tr>
<td>Password :</td>
<td><input type="password" class="inp" name="fran_password"></td>
</tr>
<tr>
<td></td>
<td><input type="submit" value="Login"></td>
</tr>
</table>
</form>