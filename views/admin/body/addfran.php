<?php $e=false;if(isset($fran)) $e=$fran;?>
<div class="container">
<h2><?php if($e) echo "Edit"; else echo "Add";?> Franchisee</h2>
<?php if(isset($error)){?><div style="padding:0px;color:red">Error!<br><?=$error?></div><?php }?>
<form id="franf" method="post">
<table style="font-size:13px;background:#fff;padding:10px;margin:10px;" cellpadding=5>
<tr>
<td>Name : </td>
<td><input type="text" class="inp" name="name" value="<?php if($e) echo $e['name'];?>"></td>
</tr>
<?php if(!$e){?>
<tr>
<td>User Name : </td>
<td><input type="text" class="inp" name="uname" value="<?php if($e) echo $e['username'];?>"></td>
</tr>
<tr>
<td>Password : </td>
<td><input type="text" class="inp" name="password"></td>
</tr>
<tr>
<td>Balance : </td>
<td><input type="text" class="inp" name="balance"></td>
</tr>
<tr>
<td>Deposit Type (cheque/dd) : </td>
<td><input type="text" class="inp" name="dtype"></td>
</tr>
<tr>
<td>Deposit ID No. <br>(cheque/dd number) : </td>
<td><input type="text" class="inp" name="number"></td>
</tr>
<?php }?>
<tr>
<td>Email : </td>
<td><input size=30 type="text" class="inp" name="email" value="<?php if($e) echo $e['email'];?>"></td>
</tr>
<tr>
<td>Mobile : </td>
<td><input size=30 type="text" class="inp" name="mobile" value="<?php if($e) echo $e['mobile'];?>"></td>
</tr>
<tr>
<td>Address : </td>
<td><textarea rows=5 cols=40 name="address"><?php if($e) echo $e['address'];?></textarea></td>
</tr>
<tr>
<td>City : </td>
<td><input type="text" class="inp" name="city" value="<?php if($e) echo $e['city'];?>"></td>
</tr>
<tr>
<td></td>
<td><input type="submit" value="<?php if($e){?>Update<?php }else echo "Add";?>"></td>
</tr>
</table>
</form>
</div>
<script>
$(function(){
	$("#franf").submit(function(){
		ef=true;
		$("input",$(this)).each(function(){
			if(!is_required($(this).val()))
			{
				ef=false;alert("All fields mandatory");return false;
			}
		});
		return ef;
	});
});
</script>
<?php
