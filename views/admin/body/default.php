<script src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
  google.load('friendconnect', '0.8');
</script>
<script type="text/javascript">   
gloadc=0;
  google.friendconnect.container.loadOpenSocialApi({
    site: '<?=$g_site?>', 
    onload: function(securityToken) {
	    gloadc++;
	    if(gloadc>1)
		    location="<?=site_url("gsignin")?>";
  }
  });
</script>

<script>
$(function(){
		FB.init("<?=$apikey?>", "/xd_receiver.htm",{"reloadIfSessionStateChanged":true});
		google.friendconnect.renderSignInButton({ 'id': 'gfc', 'text' : 'Sign in with Google', 'style': 'standard' });
});
</script>

<style>
#content{
background:transparent;
}
</style>


<div style="margin-top:70px;" align="center">
<div class="loginform" align="left">
<span style="text-align:right;width:200px;float:right;"><? if(strlen(validation_errors())>0) echo '<span style="margin-left:5px;color:#f00;float:right">:(</span>'.validation_errors('<span style="font-size:11px;color:#f00;">','</span><br>'); else echo ":)";?></span>
<div style="float:right;margin:0px;margin-top:15px;padding-right:10px;clear:both;" align="right">
<p><a href="<?=$tw_authUrl?>"><img src="<?=base_url()?>images/twitter_signin.png"></a></p>
<fb:login-button length="long" background="light" size="medium"></fb:login-button>
<p id="gfc"></p>
</div>
<p style="margin:0px;margin-bottom:15px;">Sign In
</p>
<form action="<?=site_url("signin")?>" method="post">
<div class="inlogin" style="margin-top:0px;">Email :<input value="<?=set_value("explo_email")?>" name="explo_email" type="text" style="margin-left:55px;width:180px;"></div>
<div class="inlogin">Password :<input name="explo_password" type="password" style="margin-left:20px;width:180px;"></div>
<div  class="inlogin" style="margin-top:5px;margin-right:335px;font-weight:normal;font-size:12px;"align="right"><input type="checkbox" name="remember">Remember me</div>
<div  class="inlogin" style="margin-right:250px;" align="right"><input type="submit" style="font-family:verdana;background:#333;padding:3px 5px;color:#efefef;font-weight:bold;font-size:15px;" value="Sign In"></div>
</form>
</div>
<!--<div class="inviteform">
<p style="margin:0px;margin-bottom:15px;">Request Invitation</p>
<form>
<div>Email : <input type="text"></div>
</form>
</div>
-->
</div>