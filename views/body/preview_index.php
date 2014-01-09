<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Local Square</title>
<link type="text/css" rel="stylesheet" href="<?=base_url()?>css/main.css">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>css/jquery.ui.css">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>css/fanb.css">
<script type="text/javascript" src="<?=base_url()?>js/jquery.js"></script>
<script type="text/javascript" src="<?=base_url()?>js/jquery.ui.js"></script>
<script type="text/javascript" src="<?=base_url()?>js/easyslider.js"></script>
<script src="<?=base_url()?>js/fanb.js"></script>
</head>
<body <?php if(isset($smallheader)) echo 'class="maincontainer"'?>>
<div style="float:left;color:red;position:relative;font-size:30px;font-family:times;font-weight:;left:580px;top:25px;height:1px;">&alpha;</div>
<?php if(isset($g_init)){?>
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
<?php if(isset($g_redirect)){?>
	    if(gloadc>1)
		    location="<?=site_url($g_redirect)?>";
<?php }elseif(isset($g_specialnext)){?>
		if(gloadc>1)
    		<?=$g_specialnext?>;
<?php }?>
  }
  });
</script>
<?php 
}
if(isset($fb_init))
{
?>
<script src="http://static.ak.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php" type="text/javascript"></script>
<script>
$(function(){
	<?php if(!isset($fb_signout)&&!isset($fb_reload)) {?>
		FB.init("<?=$apikey?>", "/xd_receiver.htm",{"reloadIfSessionStateChanged":true});
		<?php }else{?>
		FB.init("<?=$apikey?>", "/xd_receiver.htm");
		<?php }?>
});
</script>
<?php }?>
<style>
.accept{
background:#fa3;
color:#fff;
padding:3px 5px;
text-decoration:none;
}
</style>
<div align="center" class="maincontainer">
<div class="mainsubcontainer">
<div align="center" class="container">
<?php 
//	$this->load->view("header");
?>
<?php if(isset($inviteid)){?>
<div class="headerright" style="min-width:250px;font-family:'trebuchet ms';font-size:17px;font-weight:bold;padding:15px 20px;padding-top:7px;margin-top:-10px;">
Sign Up Invitation from <?=$inviteuser?> 
<div style="margin-top:8px;" align="right"><a class="accept" href="<?=site_url("invite/$inviteid")?>">Accept</a></div>
</div>
<?php }?>
<a href="<?=base_url()?>"><img style="margin-top:10px;margin-left:5px;" src="<?=base_url()?>images/kinglogo.png"></a>
<div id="content" align="center">
<?php 
switch($page)
{
	case "sale" :
		$this->load->view("body/preview");
		break; 	
	case "item" :
		$this->load->view("body/previewitem");
		break; 	
}
?>
<div align="center">
<?php 
if($page!="default")
	$this->load->view("footer");
?>
</div>
</div>
</div>
</div>
</body>
</html>
