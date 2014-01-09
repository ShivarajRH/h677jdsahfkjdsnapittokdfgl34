<?php 
if(!defined("APL_VER"))
	define("APL_VER",rand(0,9).".".rand(1,40).".".rand(100,999));
$user=$this->session->userdata("user");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Snap It Today <?php if(isset($title)) echo "- $title"?></title>
<meta name="viewport" content="width=device-width" />
<link type="text/css" rel="stylesheet" href="<?=base_url()?>css/wap.css?<?=str_replace(".","",APL_VER)?>">
<link rel="shortcut icon" href="<?=base_url()?>images/saleico.png" type="image/vnd.microsoft.icon" />
<link rel="icon" href="<?=base_url()?>images/saleico.png" type="image/vnd.microsoft.icon" /> 

<script type="text/javascript" src="<?=base_url()?>js/jquery.js"></script>
<script type="text/javascript" src="<?=base_url()?>js/func.js"></script>
<script>base_url="<?=base_url()?>";site_url="<?=site_url()?>";</script>
</head>
<body>
<div id="content">

	<div id="hd">
	<table width="100%">
		<tr>
			<td><a href="<?=base_url()?>"><img src="<?=base_url()?>images/logo_wap.png"></a></td>
			<td><a href="<?=site_url("yourcart")?>" style="color:blue">my cart</a></td>
		</tr>
	</table>
	</div>
	
	<div align="right" style="padding:3px;">
	<?php if($user){?>
		Welcome <b><?=$user['name']?></b> <a href="<?=site_url("signout")?>" style="color:blue;">Signout</a>
	<?php } else {?>
	<?php } ?>
	</div>
	
	<div align="right" style="margin-right:5px;">
	<form action="<?=site_url("search")?>" method="post">
	<table width="100%">
	<tr>
		<td>
		<input style="width:100%" type="text" name="snp_q">
		</td>
		<tD width="60">
		<input type="submit" value="Search" style="border:1px solid #aaa;background:#65A100;color:#fff;font-weight:bold;">
		</tD>
	</tr>
	</table>
	</form>
	</div>
	
	<?php $this->load->view("wap/$page"); ?>

	<table class="botlinks" width="100%" cellpadding=5>
	<tr>
		<td><a href="<?=site_url("nomobile")?>">Full Site</a></td>
		<?php /*?>
		<td><a href="<?=site_url("recent")?>">Recent Deals</a></td>
		<td><a href="<?=site_url("upcoming")?>">Upcoming Deals</a></td>
		*/ ?>
	</tr>
	</table>


</div>
</body>
</html>
