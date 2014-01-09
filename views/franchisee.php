<?php $user=$this->session->userdata("fran_auser");?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>ViaBazaar - Franchisee Admin</title>
<link type="text/css" rel="stylesheet" href="<?=base_url()?>css/mainv2.css">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>css/sellerv2.css">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>css/corpv2.css">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>css/jquery.ui.css">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>css/fanb.css">
<link rel="shortcut icon" href="<?=base_url()?>images/saleico.png" type="image/vnd.microsoft.icon" />
<link rel="icon" href="<?=base_url()?>images/saleico.png" type="image/vnd.microsoft.icon" /> 
<script type="text/javascript" src="<?=base_url()?>js/jquery.js"></script>
<script type="text/javascript" src="<?=base_url()?>js/jquery.ui.js"></script>
<script type="text/javascript" src="<?=base_url()?>js/easyslider.js"></script>
<script type="text/javascript" src="<?=base_url()?>js/func.js"></script>
<script src="<?=base_url()?>js/fanb.js"></script>
</head>
<body <?php if(isset($smallheader)) echo 'class="maincontainer"'?>>
<?php if(!isset($noheader)){?>
<div align="center" class="maincontainer">
<?php 
if($user!=false)
$this->load->view("fran/header");
else{?>
<img src="<?=base_url()?>images/logo.png">
<h2 style="padding-top:20px">Franchisee Admin Login</h2>
<?php }}?>
<div class="bg">
<div class="mainsubcontainer" style="background:none;-moz-border-radius:5px;margin-top:5px;margin-bottom:10px;">
<div id="content" align="center" style="min-height:300px">
<?php
if($this->session->flashdata("info"))
{?>
<span style="display:inline-block;padding:3px 5px 3px 10px;background:yellow;font-weight:bold;color:#000;"><?=$this->session->flashdata("info")?> <span style="font-weight:normal;display:inline-block;color:blue;cursor:pointer;margin-left:5px;font-weight:bold;font-size:10px;background:#B3E0F3;padding:0px 3px;" onclick='$(this).parent().hide()'>OK</span></span>
<?php }?>
<?php 
if(isset($page))
	$this->load->view("fran/body/$page");
?>
<div style="clear:both;font-size:1px;">&nbsp;</div>
</div>
</div>
</div>
<div class="container" style="text-align: left;font-size: 12px;">
&copy; Copyright 2011 <b>ViaBazaar</b>
</div>
<style>
.form{
background-color:#fff;
}
</style>
</div>
</body>
</html>
<?php 