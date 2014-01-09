<div>
	<div class="container" style="background:#fff;margin-top:20px;padding:20px 10px;">
	<table>
		<tr>
		<td valign="top" align="left">
				<div class="indcont" id="ind_signup_cont">
					<div  id="co_login" style="display:none">
					<h3 style="padding-top:10px;">Enter your corporate Email to sign up</h3>
					<div class="indsignupcont">
					<form id="indemailfrm" class="showco_login">
						<div id="invalidemail" style="margin-top:-15px">Please enter a valid email</div>
						<input type="text" id="workemailinp" class="indemail">
						<input class="loading" type="image" src="<?=base_url()?>images/signup.png" style="margin-bottom:-15px;">
						<img src="<?=IMAGES_URL?>loading.gif" class="loading" style="display:none">
					</form>
					</div>
					</div>
					<form method="post" action="<?=site_url("emailsignup")?>" class="showco_login" id="indemailfrm2">
						<input type="hidden" id="indemailhidden" name="corpemail">
						<table class="indemailtable indsignuptable"  cellpadding=0 cellspacing=0 style="margin-top: 20px;">
							<tr>
								<td colspan="2"><h2 style="background:#FFF2D4;padding:10px;">Sign Up</h2></td>
							</tr>
							<tr class="work_email_cont" style="display:none;">
								<td colspan=2 class="red">
								Work Email : <span id="workemail" style="font-style:italics;font-size:110%;"></span><a onclick='goback()' href="javascript:void(0)" style="color:blue;font-size:12px;padding:0px;">edit</a>
								</td>
							</tr>
							<tr>
								<td colspan=2 style="padding-top:15px !important;">
									Name<span class="red">*</span>
									<div class="input">
									<input type="text" name="firstname">
									</div>
								</td>
							</tr>
							<tr>
								<td>
								Password<span class="red">*</span>
								<div class="input">
								<input type="password" name="password">
								</div>
								</td>
								<td>
								Confirm Password<span class="red">*</span>
									<div class="input">
								<input type="password" name="cpassword">
								</div>
								</td>
							</tr>
							<tr>
								<td colspan=2>
								<span class="work_email_cont" style="display:none">Personal</span> Email<span class="red">*</span><span class="work_email_cont" style="display:none;"> (for communication)<sup>^</sup></span>
									<div class="input">
								<input type="text" name="email">
								</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class="work_email_cont" style="display:none;font-weight:normal;margin-top:-12px;">^ As per few company policies, you may not be able to receive external mail communications. So, please enter your personal email for communication.</div>
								</td><td align="right">
								<input style="width:auto;height:auto;border: none;" type="image" src="<?=base_url()?>images/signup.png"></td>
							</tr>
						</table>
					</form>
				</div>


		</td>
		<td valign="top" align="center" style="border-left:1px solid #ccc;">
		<div class="indexsigninup" align="left">
			<div class="panel">
				<div class="indcont" id="ind_signin_cont" style="margin-top:10px;">
					<form action="<?=site_url("emailsignin")?>" method="post" id="indsigninfrm">
						<table cellpadding=15 class="indemailtable indsignintable" style="margin-left:20px;padding:0px 15px;width:480px;background:url(<?=IMAGES_URL?>signin_bg.png) no-repeat;border:0px;height:255px;width:470px;background:#fff;">
							<tr>
								<td colspan="2"><h2 style="background:#FFF2D4;padding:10px;">Sign In</h2></td>
							</tr>
							<tr class="err" style="color:red;">
								<td colspan="2"><span class="blue">Have an account? Please login using your work or personal email ID</span></td>
							</tr>
							<tr>
								<td>Email ID</td>
								<td>
								<div class="input">
								<input type="text" name="email">
								</div>
								</td>
							</tr>
							<tr>
								<td>Password</td>
								<td>
									<div class="input">
								<input type="password" name="password">
								</div>
								</td>
							</tr>
							<tr>
								<td style="padding:20px;">&nbsp;</td>
							</tr>
							<tr>
							<td></td>
							<td align="right" style="padding-top:14px;"><a href="#forgotpass" class="fancylink" style="display:inline-block;margin-top:5px;float:left;font-size:100%;color:#000;text-decoration:underline;padding:0px;">forgot password?</a>
							<input type="image" src="<?=base_url()?>images/signin.png" style="border:0px;width:auto;height:25px;padding-top:0px;margin-top: -12px;"></td>
							</tr>
						</table>
					</form>
				</div>
			</div>
		</div>
		</td>
		</tr>
	</table>
	</div>
</div>

<div style="display:none;">
	<div id="forgotpass" style="width:450px;padding:10px;">
		<h2>Forgot Password?</h2>
		<form id="forgotpassfrm">
			<table style="margin:20px;" cellpadding=5>
				<tr>
					<td><h3>Your Email : </h3></td>
					<td><input type="text" name="email" style="width:220px"></td>
				</tr>
				<tr>
					<td colspan=2>Please enter your work or personal email id provided while creating your account</td>
				</tr>
				<tr>
					<td></td>
					<td><input type="image" src="<?=IMAGES_URL?>submit.png"></td>
				</tr>
			</table>
		</form>
	</div>
</div>


<script>
function goback(){
	$("#co_login").toggle();
	$("#indemailfrm2").toggle();
}

$(function(){
<?php if($this->session->flashdata("vloginerr")){?>
	$(".indsignintable .err td").html("Invalid email or password");
	$(".indsignintable .err").show();
<?php }?>
	$("#indsigninfrm").submit(function(){
		if(!is_email($("input[name=email]",$(this)).val()))
		{
			$(".indsignintable .err td").html("Please enter a valid email").parent().show();
			return false;
		}
	});
	$("#indemailfrm2").submit(function(){
		ef=true;
		$("input:not([type=image]):not([name=corpemail])",$(this)).each(function(){
			if(!is_required($(this).val()))
			{
				alert("All fields mandatory");
				ef=false;
				return false;
			}
		});
		if(!ef)
			return false;
//		if(!is_mobile($("input[name=mobile]",$(this)).val()))
//		{
//			alert("Please enter a valid mobile number");
//			return false;
//		}
		if($("input[name=password]",$(this)).val()!=$("input[name=cpassword]",$(this)).val())
		{
			alert("Passwords are not same. Please type again");
			return false;
		}
		if(!is_email($("input[name=email]",$(this)).val()))
		{
			alert("Please enter a valid email");
			return false;
		}
		return true;
	});
	$("#forgotpassfrm").submit(function(){
		if(!is_email($("input[name=email]",$(this)).val()))
		{
			alert("Please enter a valid email id");
			return false;
		}
		$.fancybox.showActivity();
		$.post("<?=site_url("jxforpass")?>",$(this).serialize(),function(resp){
			alert(resp);
			$.fancybox.hideActivity();		
		});
		return false;
	});
	$("#indemailfrm").submit(function(){
		$("#invalidemail").hide();
		if(!is_email($(".indemail").val()))
		{
			$("#invalidemail").html("Please enter a valid email").show();
			return false;
		}
		frags=$(".indemail").val().split("@");
		mps=[<?php $mps=$this->db->query("select name from king_mail_providers")->result_array();
		foreach($mps as $i=>$mp){
			if($i>0)
				echo ",";
			echo "'{$mp['name']}'";
		} ?>
		];
		for(i=0;i<mps.length;i++)
			if(frags[1]==mps[i])
			{
				$("#invalidemail").html("Please enter your corporate email").show();
				return false;
			}
		$(".loading").toggle();
		$.post("<?=site_url("jx/isemailavail")?>",'email='+$(".indemail").val(),function(data){
			$(".loading").toggle();
			if(data!="false")
			{
				$(".work_email_cont").show();
				$("#snapitnewage").html(data).css("font-size","105%");
				$("#indemailfrm, .groupbuystrip").hide();
				$("#indemailfrm2").show();
				$("#workemail").html($(".indemail").val());
				$("#indemailhidden").val($(".indemail").val());
			}else
			{
				$(".signlink").click();
				$(".indsignintable input[name=email]").val($(".indemail").val());
				$(".indsignintable .err td").html("Email already exists! Please login").show();
				$(".indsignintable .err").show();
			}
		});
		return false;
	});
	$(".indemail").focus(function(){
		if($(this).val()=="Enter your work email")
			$(this).val("");
	}).blur(function(){
		if($(this).val()=="")
			$(this).val("Enter your work email");
	}).val("Enter your work email");
	$(".signlink").click(function(){
		return false;
		$(".indcont, .groupbuystrip").toggle();
		if($(this).hasClass("selected"))
		{
			$(this).removeClass("selected");
			$("img",$(this)).attr("src","<?=IMAGES_URL?>login.png");
		}else
		{
			$("img",$(this)).attr("src","<?=IMAGES_URL?>register.png");
			$(this).addClass("selected");
		}
		return false;
	});
	trig_blink($("#workemailinp"));
});
</script>
<style>
body{
background:#E9E9E9;
}
</style>