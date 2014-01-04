<style>
#content{
background:transparent;
}
</style>
<script type="text/javascript">
<?php if(isset($signout) && isset($fb_signout)) {?>
FB.init("<?=$apikey?>", "/xd_receiver.htm");
<?php }?>
$(function(){
	<?php if(isset($signout) && isset($fb_signout)) {?>
	FB.Connect.logout(function() { location="<?=base_url()?>"; });
	<?php }else{?>
	window.setTimeout("redir()",200);
	<?php }?>
	});
function redir()
{
	location="<?php if(isset($signout)) echo site_url(); else echo site_url("deals")?>";
}
</script>
<div style="margin-top:70px;" align="center">
<div class="loginform" align="left">
<p style="margin:0px;margin-bottom:0px;">
<?php if(isset($signout)) echo "Signing Out"; else echo "Signing In";?>
<span style="float:right;">;)</span>
</p>
<div style="margin:0px;margin-top:0px;">please wait...</div>
</div>
</div>