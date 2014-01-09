<style>
#content{
background:transparent;
}
body{
background:#fff;
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
</style>
<script type="text/javascript">
<?php if(isset($signout) && isset($fb_signout)) {?>
FB.init("<?=$apikey?>", "/xd_receiver.htm");
<?php }?>
$(function(){
	window.setTimeout("redir()",<?php if(isset($fb_signout)) echo "2000"; else echo "200";?>);
	});
function redir()
{
	<?php /*
	<?php if(isset($signout) && isset($fb_signout)) {?>
	FB.Connect.logout(function() { location="<?=base_url()?>index.php"; });
	<?php }else{?>
	*/?>
	location="<?php if(isset($signout)) echo site_url(); else{
		$logredir=$this->session->userdata("logred");
		$this->session->unset_userdata("logred");
		if($logredir==false) 
		echo site_url("deals");
		else echo site_url($logredir);
	}
		?>";
	<?php //}?>
}
</script>
<div style="margin-top:70px;" align="center">
<div class="loginform" align="left" style="padding:10px;">
<p style="margin:0px;margin-bottom:0px;">
<?php if(isset($signout)) echo "Signing Out"; else echo "Signing In";?>
<?php if(isset($fbuser)) echo " through Facebook"; else if(isset($fb_signout)) echo "";?>
<span style="float:right;">;)</span>
</p>
<div style="margin:0px;margin-top:0px;">please wait...</div>
</div>
</div>