<div class="container">
<h2>Change your password</h2>
<div style="padding:10px;color:#888;">Password should be atleast in 6 characters length</div>
<form method="post" id="pass_p">
<table>
<tr><td>New password :</td><td><input type="password" name="p" class="pass_p"></td></tr>
<tr><td>Confirm New password :</td><td><input type="password" name="cp" class="pass_cp"></td></tr>
<tr><td></td><td><input type="submit" value="Change password"></td></tr>
</table>
</form>
</div>
<script>
$(function(){
	$("#pass_p").submit(function(){
		if($("input.pass_p").val()!=$("input.pass_cp").val())
		{
			alert("Passwords are not same");
			return false;
		}
		if($("input.pass_p").val().length<6)
		{
			alert("Password should be atleast in 6 chars length");
			return false;
		}
		return true;
	});
});
</script>
<?php
