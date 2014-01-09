<?php 
$user=$this->session->userdata("fb_user");
?>
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
	<title>ViaBazaar on Facebook</title>
<style>
			.c-fb-button, .c-fb-button-selected{
			display:inline-block;
			padding:5px 10px;
			font-size:12px;
			font-weight:normal;
			font-family:arial;
			margin:0px 4px;
			color:#23385F;
			border:1px solid #ccc;
			text-decoration:none;
			font-weight:bold;
			}
			.c-fb-cont{
			padding-top:10px;
			}
			.clear{
			clear:both;
			font-size:1px;
			}
			.c-fb-button:hover, .c-fb-button-selected{
			background:#3B5998;
			color:#fff;
			}
			.c-fb-submit{
				font-size:14px;
				font-weight:bold;
				color:#fff;
				background:#3B5998;
				padding:5px 10px;
			}
			h1,h2,h3,h4,h5,h6{
			padding:0px;
			margin:0px;
			}
.my_xfbml iframe{
width:760px !important;
}

</style>
  </head>
<link type="text/css" rel="stylesheet" href="<?=base_url()?>css/fb.css">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>css/jquery.ui.css">
<script src="<?=base_url()?>js/jquery.js"></script>
<script type="text/javascript" src="<?=base_url()?>js/jquery.ui.js"></script>
<body style="width:760px">
    <div id="fb-root"></div>
    <script>
      window.fbAsyncInit = function() {
        FB.init({
          appId   : '<?php echo $this->facebook->getAppId(); ?>',
          session : <?php echo json_encode($session); ?>, 
          status  : true,
          cookie  : true,
          xfbml   : true
        });
        FB.Canvas.setAutoResize();
      };
      (function() {
        var e = document.createElement('script');
        e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
        e.async = true;
        document.getElementById('fb-root').appendChild(e);
      }());
    </script>
<div class="header">
<div style="float:right;padding-right:20px;">
Welcome <img src="<?=base_url()?>images/special2.png"> <b><?=$user['name']?></b>
</div>
<div style="padding-top:10px;float:left"><a target="_top" href="http://apps.facebook.com/<?=$fb_url?>"><img src="<?=base_url()?>images/logo_fb.png"></a></div>
	<div id="nav_tabs">
		
		<ul style="">
<!--			<li><a id="air_h_menu" href="<?=site_url("air")?>">Brand Sale</a></li>-->
			<li style=""><a id="fb_invite" href="<?php echo site_url('fb/invite')?>"  <?php if(stristr($this->uri->segment(2),"invite")!==false){?>class="selected"<?php }?>>Invite Friends</a></li>
			<li><a id="air_h_menu" href="<?=site_url("fb/groupsales")?>" <?php if($this->uri->segment(2)=="groupsales") echo 'class="selected"';?>>Group Sales</a></li>
			<li style="padding-left:0px;"><a id="home" <?php if($this->uri->segment(2)=="deals") echo "class='selected'"; ?> href="<?=site_url("fb/deals"); ?>">Brand Sales</a></li>
			 
		</ul>
	</div>
		<div class="clear">&nbsp;</div>
</div>
<?php 
$user=$this->session->userdata("fb_user");
?>
<?php if(isset($page)){
	?>
	<div class="content" style="padding:10px 20px">    
	<?php 
	switch($page)
	{
		case "info":
				$this->load->view("fb/info");
				break;
		default:
				$this->load->view("fb/$page");
				break;
}?>
</div>
<?php } ?>    
</body>
</html>
 