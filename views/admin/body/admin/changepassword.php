<?php $user=$this->session->userdata("admin_user");?>
<style>
#main{
font-family: arial;
font-size: 11px;
}
.tablename{
font-family:arial;
font-size:12px;
color:#444444;
}
.tablename tr td{
width:30px;
height:10px;
font-size:12px;
font-weight:bold;
}
.form{
background: url(<?=base_url()?>images/title-bg-top-small.gif) repeat-x scroll left top;
border:#CCCCCC 2px solid;
-moz-border-radius:10px;
float: left;
padding: 10px;
width:250px;
}
.form input{
-moz-border-radius:5px;
border:1.5px solid #9BCD9B;
#background:#EBF8DC;
width:300px;
}
</style>
<script type="text/javascript">
 $(function(){
 if(isset(<?=$error?>)) {
$('#error').show();
$('#error').val(<?=$error?>);
}
});
</script>
<div class="heading" style="margin-bottom:20px;">
<div class="headingtext container">Change Password</div>
</div>
<div style="clear:both;margin-top:25px;width: 300px;margin-left: 58px;" id="main" align="center">
<p align="left" style="color: red;margin-left: 20px;">Fields Marked with * are required</p>
<div id="error" align="left" style="display:none;color: red;margin-left: 20px;"></div>
<div align="left" class="form_error" style="color: red;margin-left: 20px;"><?=validation_errors("<div>","</div>")?>
<?php if(isset($error)) echo $error; ?>
</div>	
<div class="form">
<form action="<?=site_url('admin/changepassword')?>" method="post">
<?php if(isset($id) && $user["usertype"]==2 && $id!=md5(strtolower($user["username"]))) {?>
<input type="hidden" name="id" id="id" value="<?=$id?>">
<?php } ?>
<table class="tablename">
<?php if(!isset($id) || $id==md5(strtolower($user["username"]))) {?>
<tr><td><label style="color: red;">*</label> <label>Old Password</label></td></tr>
<tr><td><input type="password" name="oldpwd" id="oldpwd" style="width: 180px;"></td></tr>
<?php }?>
<tr><td><label style="color: red;">*</label> <label>New Password</label></td></tr>
<tr><td><input type="password" name="newpwd" id="newpwd" style="width: 180px;"></td></tr>
<tr><td><label style="color: red;">*</label> <label>Confirm Password</label></td></tr>
<tr><td><input type="password" name="confirmpwd" id="confirmpwd" style="width: 180px;"></td></tr>
<tr><td><div align="right" style="padding: 2px;"><input style="margin-left:57px; width:90px; padding: 3px 5px; background: #2D6A2E none repeat scroll 0% 0%; font-family: verdana; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; color: rgb(239, 239, 239); font-weight: bold; font-size: 15px;" type="submit" value="Submit"></div></td></tr>
</table>
</form>
</div>
<br style="clear: both;">
<br style="clear: both;">
<br style="clear: both;">
<br style="clear: both;">
</div>