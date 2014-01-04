<div style="padding-bottom:5px;" align="center">

<div class="container">

<div align="left" style="padding-top:10px;margin:10px;">
<div style="border:1px solid #aaa;-moz-border-radius:10px;padding:">

<div style="-moz-border-radius:10px 10px 0px 0px;background:url(<?=base_url()?>images/checkout_bg.png);padding:10px;font-size:18px;font-weight:bold;">

<div class="cc_steps">
<div><span>3</span> PAYMENT</div>
<div>&raquo;&raquo;</div>
<div><span>2</span> ADDRESS</div>
<div>&raquo;&raquo;</div>
<div class="active"><span>1</span> ACCOUNT</div>
</div>

CheckOut
</div>

<div style="background:#fff">
<table width="100%" cellspacing=0>
<tr>
<td style="background:#eee;" valign="top">
<?php $this->load->view("body/new/ordersum");?>
</td>

<td width="75%" valign="top">
<div style="padding:20px;">

<div style="padding:10px;">
<table width="100%" cellspacing=0 cellpadding=0>
<tr>

<td width="50%" style="padding:5px 10px;border-right:2px solid #aaa;" valign="top">

<div style="-moz-border-radius:15px;border-radius:15px;background:#777;color:#fff;margin-top:10px;margin-right:-23px;float:right;padding:5px 7px;">or</div>

<div style="border-bottom:1px solid #aaa;padding-bottom:3px;margin-right:80px;"><b>Sign in</b> using any of the following</div>

<div style="padding-top:5px;padding:10px 5px;">
	<table cellpadding=3>
		<tr>
				<td align="center"><a style="text-decoration:none;float:left;color:inherit;border:0px;" id="fbloginlink" href="<?=$fburl?>"><img src="<?=base_url()?>images/facebook.png"><br>Facebook</a></td>
				<td align="center"><div onclick='google.friendconnect.requestSignIn()' style="cursor:pointer;float:left;margin-left:10px;"><img style="cursor:pointer;" src="<?=base_url()?>images/google.png"><br>Google</div></td>
				<td align="center"><a style="text-decoration:none;float:left;color:inherit;border:0px;margin-left:10px;" href="<?=site_url("twredirect")?>"><img src="<?=base_url()?>images/twitter.png"><br>Twitter</a></td>
		</tr>
	</table>
</div>
</div>

</td>

<td width="50%" style="padding-left:30px;" valign="top">


<div style="margin-bottom:10px;background:#FAFFD3;padding:10px;">
	<div style="border-bottom:1px solid #aaa;padding-bottom:3px;"><b>Express Checkout</b></div>
	<div style="padding-top:3px;">Don't have an Account? Are you a new user?</div>
	<div align="right" style="padding:5px 0px;"><img src="<?=base_url()?>images/continue.png" onclick='location="<?=site_url("checkout/step2")?>"' style="cursor:pointer;"></div>
</div>


	<div style="border-bottom:1px solid #aaa;padding-bottom:3px;margin-top:25px;"><b>Snapittoday</b> Users signin</div>

	<form action="<?=site_url("emailsignin")?>" method="post">
		<div style="padding-right:10px;">
			<div style="padding-top:10px;">
				Email Address<br>
				<input type="text" name="email" style="border:1px solid #bbb;padding:0px;width:100%;">
			</div>
			<div style="padding-top:10px;">
				Password<br>
				<input type="password" name="password" style="border:1px solid #bbb;padding:0px;width:100%;">
			</div>
			<div align="right" style="padding:10px 0px;margin-right:0px;">
				<a href="javascript:void(0)" style="float:left">Forgot password?</a>
				<input type="image" src="<?=base_url()?>images/signin_only.png">
			</div>
		</div>
	</form>

</td>

</tr>
</table>

</div>
</td>
</tr>
</table>
</div>

</div>
</div>

</div>
</div>

<?php
