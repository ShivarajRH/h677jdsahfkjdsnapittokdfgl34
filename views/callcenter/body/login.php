<div style='padding:20px;'>
<h2>Login</h2>
<form method="post">
<table cellpadding=5>
<?php if(isset($error)){?><tr><td colspan=2>invalid username or password</td></tr><?php } ?>
<tr><td>User Name : </td><td><input type="text" name="username"></td></tr>
<tr><td>Password : </td><td><input type="password" name="password"></td></tr>
<tr><td></td><td><input type="submit" value="Login"></td></tr>
</table>
</form>
</div>