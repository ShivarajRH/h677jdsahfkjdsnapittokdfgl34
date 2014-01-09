<script>
function showsendinvite()
{
	$("#gsendinvite").show();
}
msg='<?=$g_invitemsg?>';
</script>
<div class="headingtext">Invite Friends</div>
<div align="left">
<div class="inviteform">
<span style="font-size:15px;">Your Invite url : <a style="color:#00f;" href="<?=site_url("invite/".$user['inviteid'])?>"><?=site_url("invite/".$user['inviteid'])?></a></span>
<br><span style="font-size:12px;">Please use above url to invite your friends</span>
</div>
<div class="inviteform">
<form action="<?=site_url("invitebyemail")?>" method="post">
By Email <span style="color:#606060;font-size:11px;float:right;">Please enter email addresses separated by comma</span><br>
<textarea name="explo_inviteemails" style="width:100%;height:60px;"></textarea>
<div style="margin-top:5px;text-align:right"><input type="submit"  style="font-family:verdana;background:#333;padding:3px 5px;color:#efefef;font-weight:bold;font-size:12px;" value="Send Invite"></div>
</div>
</form>
<div id="gsendinvite" style="display:none;margin:50px;">
<a href="javascript:void(0);" style="color:blue;font-size:20px;" onclick='google.friendconnect.requestInvite(msg)'>Send Invite to Google friends</a>
</div>
<div style="margin-left:20px;margin-top:20px;color:#444;font-family:'trebuchet ms';">Publish in your social networks</div>
<div align="center" style="margin-top:15px;">
<div style="width:630px;" align="left">
<div style="float:left;"><a href="<?=site_url("fbinvite")?>"><img src="<?=base_url()?>images/facebook.jpg"></a></div>
<div style="float:left;margin:0px 30px;"><a href="javascript:void(0)" onclick='google.friendconnect.requestSignIn()'><img src="<?=base_url()?>images/google.gif"></a></div>
<div style="padding-left:30px;"><a href="<?=site_url("twinvite")?>"><img src="<?=base_url()?>images/twitter.jpg"></a></div>
</div>
</div>
</div>