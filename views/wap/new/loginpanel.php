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
<div id="loginpanelcont" style="color:#000;margin:5px;">
	<div id="loginpanel">
					<h2 style="margin:0px;">Member Sign In</h2>
					<div>Login with your existing info</div>
					<form action="<?=site_url("emailsignin")?>" method="post">
						<table cellpadding=2 width="99%" style="font-size:120%">
  <?php if(isset($error)){?><tr><td><div style="color:#F7DF00;font-weight:bold;font-size:120%;">Invalid email or password</div></td></tr><?php }?>
							<tr><td style="padding-top:10px;">Email : </td></tr>
							<tr><td><input class="inpb" type="text" name="email" style="width:100%"></td></tr>
							<tr><td style="padding-top:10px;">Password : </td></tr>
							<tr><td><input class="inpb" type="password" name="password" style="width:100%"></td></tr>
							<tr><td style="padding-top:10px;" align="right"><input type="image" src="<?=base_url()?>images/signinbig.png"></td></tr>
						</table>
					</form>
	</div>
</div>
<style>
.inpb{
	border:1px solid #aaa;
	padding:3px;
}
</style>
<script>
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