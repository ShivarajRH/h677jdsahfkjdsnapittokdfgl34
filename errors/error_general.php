<?php 
	$url_prts = explode('/',current_url());
	if(in_array('admin',$url_prts))
	{
?>
<html>
<head>
<title>Storeking</title>
<style type="text/css">
body {
background:	#3A2E58 !important;
margin:				40px;
font-family:		arial;
font-size:			15px;
color:				#fff;
text-align: center;
}
</style>
</head>
<body>
<img src="<?=base_url()?>images/paynearhome.png">
<h2>Error!</h2>
<h3><?=$message?></h3>
<div style="font-size:80%">We are not able to process your request</div>
</body>
</html>
<?php }else {
?>
<html>
<head>
<title>Snapittoday.com</title>
<style type="text/css">
body {
background:	#000 !important;
margin:				40px;
font-family:		arial;
font-size:			15px;
color:				#fff;
text-align: left;
}
</style>
</head>
<body>
<img src="<?=base_url()?>images/logowhite.png">
<h2>Error!</h2>
<h3><?=$message?></h3>
<div style="font-size:80%">We are not able to process your request</div>
</body>
</html>
<?php 
}?>