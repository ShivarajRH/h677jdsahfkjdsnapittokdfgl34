<script src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
  google.load('friendconnect', '0.8');
</script>
<script type="text/javascript">   
$(function(){
gloadc=0;
  google.friendconnect.container.loadOpenSocialApi({
    site: '<?=$g_site?>', 
    onload: function(securityToken) {
	    gloadc++;
	    if(gloadc>1)
	    {
		    $("#loginform").hide();
		    $("#gsign").show();
		    location="<?=site_url("gsignin")?>";
	    }
  }
  });
});
</script>

<script>
function fbredirect()
{
	$("#fbsignbut").hide();
	$("#fbsign").show();
	location="<?=site_url("fblogin")?>";
}
$(function(){
	
		FB.init("<?=$apikey?>", "/xd_receiver.htm");
		google.friendconnect.renderSignInButton({ 'id': 'gfc', 'text' : 'Google', 'style': 'long' });
		$("#fbloginlink").click(function(){
			FB.Connect.requireSession(function() { location='<?=site_url("fblogin")?>'; })
		});
});
</script>

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
	color:#444;
}
</style>


<div style="margin-top:0px;" align="center">
<div style="width:900px;">
<!--<div align="left" style="margin-bottom:10px;"><a href="<?=base_url()?>"><img src="<?=base_url()?>images/kinglogo.png"></a></div>-->
<div class="loginform" align="left" style="background:#fff;margin-top:40px;width:900px;-moz-box-shadow:2px 0 10px #444;">
<div style="padding:10px;padding-left:40px;float:right;margin:10px;margin-right:30px;width:350px;border-left:0px solid #2F0A09;">
<!--<div align="center" style="margin-top:10px;">-->
<!--<div style="font-size:12px;padding-top:20px;">India's premium private sale site getting you world's best deals</div>-->
<!--</div>-->
<?php if(!isset($fblogin)){?>
<div style="text-align:left;float:left;"><? if(strlen(validation_errors())>0) echo '<span style="margin-left:5px;color:#f00;width:100%"></span>'.validation_errors('<span style="font-size:11px;color:#f00;font-size:14px;"><nobr>','</nobr></span><br>');?></div>
<div style="clear:both;font-size:18px;margin:0px;margin-top:50px;margin-bottom:15px;margin-left:-20px;">Are you a VIA Travel Agent?</div>
<div id="fbsign" style="display:none;clear:both;margin:0px;margin-top:0px;margin-bottom:15px;margin-left:0px;">Please wait... Signing in with Facebook</div>
<div id="gsign" style="display:none;clear:both;margin:0px;margin-top:0px;margin-bottom:15px;margin-left:0px;">Please wait... Signing in with Google</div>
<form id="loginform" action="<?=site_url("procagentlogin")?>" method="post" style="color:#444;">
<div class="inlogin" style="margin-top:0px;">
<input value="<?=set_value("via_username")?>" name="via_username" type="text" style="width:200px;float:right;margin-right:30px;">Mobile No. :
</div>
<div class="inlogin" style="clear:both;padding-top:10px;"><input name="via_password" type="password" style="width:200px;float:right;margin-right:30px;">Password :</div>
<div  class="inlogin" style="clear:both;padding-top:4px;margin-right:125px;font-weight:normal;font-size:12px;"align="right"></div>
<div  class="inlogin" style="clear:both;margin-top:0px;margin-right:10px;" align="right"><input type="submit" style="font-family:verdana;background:#222;padding:3px 5px;color:#efefef;font-weight:bold;font-size:15px;" value="Sign In"></div>
</form>
<div style="clear:both;font-size:18px;margin:0px;margin-top:30px;margin-bottom:15px;margin-left:-20px;">Are you a VB Franchisee?</div>
<form id="loginform" action="<?=site_url("franchisee/login/true")?>" method="post">
<div class="inlogin" style="margin-top:0px;">
<input value="<?=set_value("explo_email")?>" name="fran_username" type="text" style="width:200px;float:right;margin-right:30px;">User Name :
</div>
<div class="inlogin" style="clear:both;padding-top:10px;"><input name="fran_password" type="password" style="width:200px;float:right;margin-right:30px;">Password :</div>
<div  class="inlogin" style="clear:both;padding-top:4px;margin-right:125px;font-weight:normal;font-size:12px;"align="right">
<!--<input type="checkbox" name="remember">Remember me-->
</div>
<div  class="inlogin" style="margin-top:0px;margin-right:10px;" align="right"><input type="submit" style="font-family:verdana;background:#222;padding:3px 5px;color:#efefef;font-weight:bold;font-size:15px;" value="Sign In"></div>
</form>
<?php /*?>
<div style="clear:both;margin:0px;margin-top:10px;margin-bottom:5px;margin-left:-20px;font-size:17px;">Sign in with</div>
<div style="margin:0px;margin-top:5px;margin-right:0px;padding-right:0px;clear:both;" align="right">
<a href="<?=$tw_authUrl?>" style="padding:0px 5px;"><img src="<?=base_url()?>images/twitter_signin.png"></a>
<a id="fbloginlink" href="javascript:void(0)"><img src="<?=base_url()?>images/fblogin.jpg"></a>
<span id="gfc" style="padding-left:10px;float:right"></span>
</div>
<?php */?>
<?php /*?>
<div style="clear:both;margin:0px;margin-top:35px;margin-bottom:15px;margin-left:-20px;font-size:19px;">Request Invite</div>
<div class="inlogin" style="margin-top:0px;"><input value="<?=set_value("explo_email")?>" name="explo_email" type="text" style="float:right;margin-left:5px;width:250px;">Email :  </div>
<div class="inlogin" align="right" style="padding-top:5px;"><input type="submit" style="font-family:verdana;background:#21f;padding:2px 5px;color:#efefef;font-weight:bold;font-size:12px;margin-left:10px;" value="Request Invite"></div>
*/?>
<div style="clear:both;padding-top:50px;font-size:14px;" align="right">
<a href="<?=site_url("agent")?>" style="color:blue">Agent Login</a>
</div>
<!--<div align="right" class="frontlinks">-->
<!--<a href="#">CONTACT</a> <a href="#">ABOUT US</a> -->
<!--</div>-->
<?php }else{?>
<div align="center" style="padding:90px 0px;">
<div id="fbsign" style="display:none;clear:both;margin:0px;margin-top:0px;margin-bottom:15px;margin-left:0px;">Please wait... Signing in with Facebook</div>
<div id="fbsignbut">Please click below button to sign in with Facebook
<fb:login-button onlogin="fbredirect()" length="large" background="light" size="large"></fb:login-button>
</div>
</div>
<?php }?>
</div>
<div><img src="<?=base_url()?>images/viabazaar.png" style="margin:60px 25px;margin-right:0px;width:400px;"></div>
</div>
</div>
</div>