<?php $user=$this->session->userdata("user"); ?>
<?php /*?>
<div>
	<div class="container">
	<div style="padding:40px;font-size:110%;">
		<h1>Please verify this account</h1>
		<div style="padding:10px;font-weight:bold;">Dear <?=$user['name']?></div>
		<div style="padding:0px 10px 10px 10px;">We have sent you an email to <b><?=$user['email']?></b> with a verification link</div>
		<div style="padding:0px 10px 10px 10px;">If you have not received this mail, please click here to <a href="<?=site_url("getverified")?>">resend</a></div>

		<div style="padding:20px 10px 10px 10px;">To activate your account using mobile, please <a href="<?=site_url("getverifiedbymob")?>">click</a> here</div>
	</div>
	</div>
</div>
*/ ?>
<div>
	<div class="container">
		<div class="getverified">
			<div class="head">Please choose the verification process</div>
			<table width="100%" cellpadding=10>
				<tr>
					<td align="center" valign="top" width="50%">
						<h3 class="blue">Email</h3>
							<br>
						<div align="left">
							We have sent you an email to <b><?=$user['email']?></b> with a verification link.
							<br>
							<br>
							If you have not received the mail, please click here to <a href="javascript:void(0)" class="gv_mailsend">resend</a>
						</div>
					</td>
					<td align="center" valign="top" style="border-left:2px solid #aaa;" width="50%">
						<h3 class="blue">Mobile Phone</h3>
						<br>
						<div align="left">
						<?php if($user['mobile']!=0){?>
							To receive a verification code in text message to this number : <?=$user['mobile']?>, please click <a href="javascript:void(0)" class="gv_sendsms">here</a>
		<br>
		<br>
							<form method="post">
								<table cellpadding=3>
									<?php if(isset($error)){?>
									<tr>
										<td colspan=3 style="color:red;font-weight:bold;"><?=$error?></td>
									</tr>
									<?php }?>
									<tr>
										<td><b>Enter access Code</b></td><td>:</td><td><input type="text" name="code" style="width:200px;"></td>
									</tr>
									<tr>
										<td colspan=2></td><td><input type="submit" value="Get Verified!"></td>
									</tr>
								</table>
							</form>

						<?php }else{?>
						<form method="post" action="<?=site_url("jx/updatemob")?>" id="gv_upmob">
							To receive a verification code in text message, please enter your mobile number : <input type="text" name="mobile"> <input type="submit" value="Go">
						</form>
						<?php }?>

						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
<script>
$(function(){
	$("#gv_upmob").submit(function(){
		if(!is_mobile($("input[name=mobile]",$(this)).val()))
		{
			alert("Please enter a valid mobile number");
			return false;
		}
		return true;
	});
	$(".gv_mailsend").click(function(){
		$.fancybox.showActivity();
		$.get("<?=site_url("getverified/sendmail")?>",function(){
			$.fancybox.hideActivity();
			alert("Verification mail sent!");
		});
	});
	$(".gv_sendsms").click(function(){
		$.fancybox.showActivity();
		$.get("<?=site_url("getverified/sendsms")?>",function(){
			$.fancybox.hideActivity();
			alert("Verification sms with access code sent to your mobile!");
		});
	});
});
</script>
<?php
