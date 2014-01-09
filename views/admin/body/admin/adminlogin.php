<style>
.labelclass{
font:normal;
font-family: arial;
font-size: 13px;
margin-bottom: 10px;
}
</style>
<div style="margin:30px;" align="center">
<div class="loginform" align="left">
	<span style="text-align:right;width:200px;float:right;">
		<? if(strlen(validation_errors())>0) echo '<span style="margin-left:5px;color:#f00;float:right">:(</span>'.validation_errors('<span style="font-size:11px;color:#f00;">','</span><br>');?>
		<?php if(isset($autherror)) echo '<span style="margin-left:5px;color:#f00;float:right">:(</span><span style="font-size:11px;color:#f00;">'.$autherror.'</span><br>';?>
	</span>
	<p style="margin:0px;margin-bottom:15px;">Sign In</p>
	<form action="<?=site_url("admin/processLogin")?>" method="post">
		<div class="labelclass" style="clear:both;margin-top:0px;margin-left: 120px;">User Name <input value="<?=set_value("explo_email")?>" name="explo_email" type="text" style="margin-left:25px;width:180px;"></div>
		<div class="labelclass" style="margin-left: 120px;">Password <input name="explo_password" type="password" style="margin-left:30px;width:180px;"></div>
		<div align="right"><input type="submit" style="font-family:verdana;background:#333;padding:3px 5px;color:#efefef;font-weight:bold;font-size:15px;margin-right: 30px;" value="Sign In"></div>
	</form>
</div>