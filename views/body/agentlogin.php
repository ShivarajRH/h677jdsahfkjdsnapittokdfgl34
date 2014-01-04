<style>
#hd{
padding:0px;
}
body{
}
.mainsubcontainer{
background:transparent;
padding:0px;
margin:0px;
border:0px;
-moz-box-shadow:none;
-webkit-box-shadow:none;
box-shadow:none;
}
#content,.container{
background:transparent;
}
.frontlinks{
padding-top:30px;
margin-right:-10px;
}
.frontlinks a{
color:#555;
font-size:13px;
text-decoration:none;
}
.frontlinks a:hover{
text-decoration:underline;
}
.loginform{
	-moz-box-shadow:2px 0 10px #444;
	-webkit-box-shadow:2px 0 10px #444;
	box-shadow:2px 0 10px #444;
}
</style>


<div style="margin-top:0px;" align="center">
<div style="width:900px;">
<!--<div align="left" style="margin-bottom:10px;"><a href="<?=base_url()?>"><img src="<?=base_url()?>images/kinglogo.png"></a></div>-->
<div class="loginform" align="left" style="background:#fff;margin-top:40px;width:900px;-moz-box-shadow:2px 0 10px #444;">
<div style="padding:10px;padding-left:40px;float:right;margin:10px;margin-right:30px;width:350px;border-left:0px solid #2F0A09;padding-top:100px;">
<!--<div align="center" style="margin-top:10px;">-->
<!--<div style="font-size:12px;padding-top:20px;">India's premium private sale site getting you world's best deals</div>-->
<!--</div>-->
<div style="text-align:left;float:left;">
<? if(strlen(validation_errors())>0) echo '<span style="margin-left:5px;color:#f00;width:100%"></span>'.validation_errors('<span style="font-size:11px;color:#f00;font-size:14px;"><nobr>','</nobr></span><br>');?>
<?php if(isset($autherror)) echo '<span style="font-size:11px;color:#f00;font-size:14px;"><nobr>'."Invalid username or password</span>";?>
</div>
<div style="color:#333;clear:both;margin:0px;margin-top:20px;margin-bottom:35px;margin-left:-20px;">
<img src="<?=base_url()?>images/via.png" style="float:left"> <div style="float:left;padding-left:7px;padding-top:3px;">Agent Sign In</div>
<div class="Clear">&nbsp;</div>
</div>
<form id="loginform" action="<?=site_url("procagentlogin")?>" method="post" style="color:#444;">
<div class="inlogin" style="margin-top:0px;">
<input value="<?=set_value("via_username")?>" name="via_username" type="text" style="width:200px;float:right;margin-right:30px;">User Name :
</div>
<div class="inlogin" style="clear:both;padding-top:10px;"><input name="via_password" type="password" style="width:200px;float:right;margin-right:30px;">Password :</div>
<div  class="inlogin" style="clear:both;padding-top:4px;margin-right:125px;font-weight:normal;font-size:12px;"align="right"></div>
<div  class="inlogin" style="clear:both;margin-top:0px;margin-right:10px;" align="right"><input type="submit" style="font-family:verdana;background:#222;padding:3px 5px;color:#efefef;font-weight:bold;font-size:15px;" value="Sign In"></div>
</form>
</div>
<div><img src="<?=base_url()?>images/viabazaar.png" style="margin:60px 25px;margin-right:0px;width:400px;"></div>
</div>
</div>
</div>