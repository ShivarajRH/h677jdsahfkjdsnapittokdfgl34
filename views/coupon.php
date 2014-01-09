<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Local Square - Coupon System <?php if(isset($title)) echo "- $title"?></title>
<link type="text/css" rel="stylesheet" href="<?=base_url()?>css/coupon/main.css">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>css/coupon/admin.css">
<link rel="shortcut icon" href="<?=base_url()?>images/saleico.png" type="image/vnd.microsoft.icon" />
<link rel="icon" href="<?=base_url()?>images/saleico.png" type="image/vnd.microsoft.icon" /> 
<script type="text/javascript" src="<?=base_url()?>js/jquery.js"></script>
<script type="text/javascript" src="<?=base_url()?>js/func.js"></script>
</head>
<body>
<div align="center">

<div id="main_container" align="left">
<?php $this->load->view("coupon/header");?>

<div id="content">
<?php if(isset($page))$this->load->view("coupon/body/$page");?>
</div>

<?php $this->load->view("coupon/footer");?>

</div>

</div>
</body>
</html>
