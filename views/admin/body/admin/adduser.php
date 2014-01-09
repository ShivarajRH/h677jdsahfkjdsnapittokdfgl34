<style>
#main{
font: 13px;
font-family:'trebuchet ms';
}
.form{
background: url(<?=base_url()?>images/title-bg-top-small.gif) repeat-x scroll left top;
border:#CCCCCC 2px solid;
-moz-border-radius:10px;
padding: 10px;
width:500px;
}
input{
-moz-border-radius:5px;
border:1.5px solid #9BCD9B;
#background:#EBF8DC;
width:300px;
}
</style>
<div class="heading" style="margin-bottom:20px;">
<div class="headingtext container">Add User</div>
</div>
<div style="margin-top:30px;" align="center">
<div id="main" class="form" align="left">
<span style="text-align:right;width:200px;"><? if(strlen(validation_errors())>0) echo '<span style="margin-left:5px;color:#f00;float:right">:(</span>'.validation_errors('<span style="font-size:11px;color:#f00;">','</span><br>');?></span>
<form action="<?=site_url("admin/processAddUser")?>" method="post">
<div style="margin-top:30px; margin-left: 40px;margin-bottom: 10px;"><input value="<?=set_value("username")?>" name="username" type="text" style="float:right;width:180px;margin-right:110px;">User Name </div>
<div style="margin-bottom: 10px;margin-left: 40px;"><input name="password" type="password" style="float:right; width:180px;margin-right:110px;">Password </div>
<div style="margin-bottom: 10px;margin-left: 40px;"><input name="confirmpwd" type="password" style="float:right;width:180px;margin-right:110px;">Confirm Password </div>
<div align="right"><input type="submit" style="width:100px;font-family:verdana;background:#436D34;padding:3px 5px;color:#efefef;font-weight:bold;font-size:15px;" value="Add User"></div>
</form>
</div>
</div>