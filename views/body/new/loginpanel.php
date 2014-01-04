<div id="fb-root"></div>

<?php /*
<script>
    FB.init({appId: '7d7a395938b7475be93415f68933f2e0', status: true, cookie: true, xfbml: true});
		$("#fbloginlinklp").click(function(){
			FB.login(function(response) {
			  if (response.session) {
			  	location="<?=site_url("signin")?>";
			  } else {
			  }
			});
		});
</script>
*/ ?>
<div id="loginpanelcont" style="color:#fff;background:#333;width:800px;padding:20px 0px;">
		<img src="<?=base_url()?>images/logo.png" style="margin-left:20px;">
	<div id="loginpanel">
		<table width="100%">
			<tr>
				<td width="50%" valign="top" style="padding:0px 20px;border-right:2px solid #fff;">
					<h2>Sign Up</h2>
					<div style="float:right;font-size:110%;-moz-border-radius:15px;padding:7px;border-radius:15px;color:#444;background:#F7DF00;margin-right:-37px;margin-top:40px;"><b>OR</b></div>
					<form action="<?=site_url("emailsignup")?>" id="emailsignupform" method="post">
						<table cellpadding=10 width="100%" style="white-space:nowrap;">
							<tr><td>Email : </td><td><input class="inpb" type="text" name="email"></td></tr>
							<tr><td>Password : </td><td><input class="inpb" type="password" name="password"></td></tr>
							<tr><td>Confirm : </td><td><input class="inpb" type="password" name="cpassword"></td></tr>
							<tr><td>Mobile : </td><td><input class="inpb" type="mobile" name="mobile"></td></tr>
							<tr><td colspan="2" style="color:#F58728;font-size:110%;font-weight:bold;">Note: Your email address will not be used<br>for spamming</td></tr>
							<tr><td colspan="2" align="center"><input type="image" src="<?=base_url()?>images/becomeamember.png"></td></tr>
						</table>
					</form>
				</td>
				<td width="50%" valign="top" style="padding:0px 20px;">

					<h2 style="margin:0px;">Sign in using any of the following social media sites</h2>
					<div style="padding:10px 50px;">
						<a style="float:left" id="fbloginlinklp" href="<?=$fburl?>">
							<img src="http://snapittoday.com/images/facebook.png">
						</a>
						<div style="float:left;margin-left:10px;">
							<img style="cursor:pointer;" src="http://snapittoday.com/images/google.png" onclick='google.friendconnect.requestSignIn()'>
						</div>
						<a style="float:left;margin-left:10px;" href="http://snapittoday.com/twredirect">
							<img src="http://snapittoday.com/images/twitter.png">
						</a>
					</div>
					<div class="clear" style="padding:10px 0px; margin-bottom:15px;border-bottom:1px solid #aaa;"></div>
					<h2>Member Sign In</h2>
					<div>Login with your existing info</div>
					<form action="<?=site_url("emailsignin")?>" method="post">
						<table cellpadding=10 class="forpasscont">
  <?php if(isset($error)){?><tr><td colspan="2"><div style="color:#F7DF00;font-weight:bold;font-size:120%;">Invalid email or password</div></td></tr><?php }?>
							<tr><td>Email : </td><td><input class="inpb" type="text" name="email"></td></tr>
							<tr><td>Password : </td><td><input class="inpb" type="password" name="password"></td></tr>
							<tr><td></td><td><a href="javascript:void(0)" style="color:#fff;" onclick='$(".forpasscont").toggle()'>forgot password?</a></td></tr>
							<tr><td></td><td><input type="image" src="<?=base_url()?>images/signinbig.png"></td></tr>
						</table>
					</form>
					<form id="forpassform">
						<table class="forpasscont" style="display:none;">
							<tr><td colspan="2"><h4>Forgot password?</h4></td></tr>
							<tr><td>Your Email : </td><td><input type="text" class="inpb" name="forpassemail" id="forpasstextemail"></td></tr>
							<tr><td></td><td><input type="submit" value="Reset Password"></td></tr>
						</table>
					</form>
					<div class="forpaassresp"></div>
				</td>
			</tr>
		</table>
	</div>
</div>
<style>
.inpb{
	border:1px solid #aaa;
	padding:3px;
	width:90%;
}
</style>
<script>
	$("#forpassform").submit(function(){
		if(!is_email($("#forpasstextemail").val()))
		{ 
			alert("Please enter a valid email");
			return false;
		}
		pst=$(this).serialize();
		$.post("<?=site_url("jxforpass")?>",pst,function(rep){
			$(".forpasscont").hide();
			$(".forpaassresp").show().html(rep);
		});
		return false;
	});
	$("#emailsignupform").submit(function(){
		ef=true;
		if(!is_email($("input[name=email]",$(this)).val()))
		{
			ef=false;
			alert("enter a valid email");
		}
		if(ef && !is_mobile($("input[name=mobile]",$(this)).val()))
		{
			ef=false;
			alert("enter a valid mobile");
		}
		if(ef && $("input[name=password]",$(this)).val()!=$("input[name=cpassword]").val())
		{
			ef=false;
			alert("passwords are not same");
		}
		if(ef==false)
			return false;
		return true;
	});
</script>