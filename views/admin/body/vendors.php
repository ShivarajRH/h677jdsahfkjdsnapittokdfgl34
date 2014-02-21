<script>
$(function(){
	$("#addvendor form").submit(function(){
		if(!is_alphanum($("input[name=username]").val()))
		{
			alert("Invalid user name. Can contain only alphabets or number");
			return false;
		}
		if(!is_required($("input[name=password]").val()) || !is_required($("input[name=cvpassword]").val()) || $("input[name=password]").val()!=$("input[name=cpassword]").val())
		{
			alert("Passwords are not same");
			return false;
		}
	});
});
</script>
<div class="heading" style="margin-bottom:10px;margin-top: 40px;">
<div class="headingtext container">
Vendors</div>
</div>
<div class="container" style="font-family:arial;">
<div style="background:#f1faf1;min-height:100px;padding:5px;margin:10px;">
<?php if(!empty($vendors)){?>
<table cellspacing="20">
<tr>
<th>Vendor name</th>
<th>User name</th>
</tr>
<?php foreach($vendors as $vendor){?>
<tr>
<td><?=$vendor['name']?></td>
<td><?=$vendor['username']?></td>
</tr>
<?php }?>
</table>
<?php }else{?>
<div style="font-size:14px;">No vendors. Add one!</div>
<?php }?>
</div>
<div style="text-align:right;margin:10px;">
<a style="color:blue;font-size:13px;font-weight:bold;" href="javascript:void(0)" onclick='$("#addvendor").show("medium")'>Add Vendor</a>
<div align="left" style="padding-top:10px;display:none" id="addvendor">
<h4 style="margin:0px;">Add vendor</h4>
<form action="<?=site_url("admin/addvendor")?>" method="post">
<table cellspacing="10" style="font-size:13px;">
<tr><td valign="top">Name*</td><td>:</td><td><input type="text" name="vname"></td></tr>
<tr><td valign="top">User Name*</td><td>:</td><td><input type="text" name="username"></td></tr>
<tr><td valign="top">Password*</td><td>:</td><td><input type="password" name="password"></td></tr>
<tr><td valign="top">Confirm Password*</td><td>:</td><td><input type="password" name="cpassword"></td></tr>
<tr><td valign="top">Contact Person*</td><td>:</td><td><input type="text" name="contact"></td></tr>
<tr><td valign="top">Address</td><td valign="top">:</td><td><textarea name="address" rows="7" cols="50"></textarea></td></tr>
<tr><td>Email*</td><td>:</td><td><input type="text" name="email" size="40"></td></tr>
<tr><td>Telephone</td><td>:</td><td><input type="text" name="telephone" size="40"></td></tr>
<tr><td>Mobile*</td><td>:</td><td><input type="text" name="mobile" size="40"></td></tr>
<tr><td valign="top">Description</td><td valign="top">:</td><td><textarea name="desc" rows="7" cols="50"></textarea></td></tr>
<tr><td></td><td></td><td><input type="submit" value="Add Vendor"></td></tr>
</table>
</form>
</div>
</div>
</div>
<?php
