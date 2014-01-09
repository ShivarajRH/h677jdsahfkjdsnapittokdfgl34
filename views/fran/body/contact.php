<div class="container">
<h1 style="margin-bottom:10px;">Contact Admin</h1>
<form method="post" id="contform">
<table style="font-size:14px">
<tr>
<td>Subject :</td>
<td>
<input type="text" name="sub" class="inp">
</td>
</tr>
<tr>
<td valign="top">Message : </td>
<td>
<textarea class="inp" name="msg" style="width:400px;height:150px;"></textarea>
</td>
</tr>
<tr>
<td></td>
<td>
<input type="submit" value="Submit">
</td>
</tr>
</table>
</form>
</div>
<script>
$(function(){
	$("#contform").submit(function(){
		if(!is_required($("textarea",$(this)).val()))
		{
			alert("Enter message");return false;
		}
		if(!is_required($("input",$(this)).val()))
		{
			alert("Enter subject");return false;
		}
		return true;
	});
});
</script>
<?php
