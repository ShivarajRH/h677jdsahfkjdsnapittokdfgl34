<style>
.fieldname{
width:120px;
float:left;
font-size:12px;
}
#content{
}
</style>
<div class="heading">
<div class="headingtext container">Sign Up</div>
</div>
<div class="container">
<div style="float:right">
<fb:login-button length="long" background="light" size="medium"></fb:login-button>
</div>
<div class="info" style="margin-top:30px;margin-left:40px;width:350px;padding-top:15px;">
<?=validation_errors('<div style="font-weight:normal;font-size:11px;color:#f00;">','</div>')?>
Please enter following details
<form method="post">
<div style="margin:0px;padding:10px 10px;padding-top:15px;">
<div style="margin:5px;"><div class="fieldname">Email :</div> <input type="text" name="explo_email" value="<?=set_value("explo_email")?>"></div>
<div style="margin:5px;"><div class="fieldname">Name :</div> <input type="text" name="explo_name"  value="<?=set_value("explo_name")?>"></div>
<div style="margin:5px;"><div class="fieldname">Password :</div> <input type="password" name="explo_password"></div>
<div style="margin:5px;"><div class="fieldname">Confirm Password :</div> <input type="password" name="explo_cpassword"></div>
<div style="margin:5px;"><div class="fieldname">Mobile <span style="font-size:9px">(optional)</span> :</div> <input type="text" name="explo_mobile"></div>
<div align="right" style="padding-top:10px;"><input type="submit" value="Sign Up" style="padding: 3px 5px; background: rgb(51, 51, 51) none repeat scroll 0% 0%; font-family: verdana; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; color: rgb(239, 239, 239); font-weight: bold; font-size: 14px;"></div>
</div>
</form>
</div>
</div>