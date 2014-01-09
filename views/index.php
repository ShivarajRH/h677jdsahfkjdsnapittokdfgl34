<?php
if(!defined("APL_VER"))
	define("APL_VER",rand(0,9).".".rand(1,40).".".rand(100,999));
if(isset($adminheader) || isset($superadmin)){
	$this->load->view('admin_index');
}else{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>
<?php if(isset($title)) 
		echo htmlspecialchars(ucfirst($title))." | Snapittoday.com"; 
	else if(isset($tag))
	{?>
<?=htmlspecialchars($tag['name'])?> | Snapittoday.com
<?php 
}
else 
{ ?>
Snapittoday.com - Online shop for all your Beauty, healthcare and wellness needs in India - 100 brands and 4000 products on display
<?php }?></title>

<meta property="og:title" content="Snap It Today : <?php if(isset($tag)){?><?=htmlspecialchars($tag['name'])?><?php }else if(isset($title)){ ?><?=htmlspecialchars($title)?><?php }else{?>The one stop shop for all your Fashion,Beauty,cosmetics <?php }?>" />
<meta property="og:type" content="product" />
<meta property="og:image" content="<?=IMAGES_URL?><?php if(isset($itemdetails)){?>items/<?=$itemdetails['pic']?>.jpg<?php }else if(isset($tag)){ ?>tags/small/<?=$tag['pic']?>.jpg<?php }else{?>logo_wap.png<?php }?>" />
<meta property="og:description" content="<?php if(isset($itemdetails)) echo strip_tags(str_replace('"',"",$itemdetails['description1'])); else{?>SnapItToday.com:The one stop shop for all your Fashion,Beauty,cosmetics and Lifestyle needs at the lowest prices. Free Shipping in India.<?php }?>" />
<meta property="og:site_name" content="Snapittoday.com" />
<meta property="og:url" content="http://<?=$_SERVER['HTTP_HOST']?><?=$_SERVER['REQUEST_URI']?>">
<meta property="fb:admins" content="710699654" />
<link rel="search" type="application/opensearchdescription+xml" href="<?=base_url()?>os.xml" title="Snapittoday" />
<meta name="Keywords" content="<?php if(isset($itemdetails)) echo $itemdetails['keywords']; else{?>shop online, online shopping india, online shopping website, online shopping store, best, deal, online, snapittoday, lowest price, india, cheapest, lifestyle, fashion, beauty, cosmetics, indiagetonline, india get online, website, buy skin care products india, perfume, skin care products, cosmetics, hair care products, deodorant, makeup, beauty care products, personal care, products, body care products, makeup products, buy, online, purchase, order, for men, women, male, female, india,Shop, Perfumes, Deodorants, Makeup, Cosmetics, Skincare, 
<?php }?>"/>
<meta name="Description" content="<?php if(isset($itemdetails)) echo strip_tags(str_replace('"',"",$itemdetails['description1'])); else{?>SnapItToday.com:The one stop shop for all your Fashion,Beauty,cosmetics and Lifestyle needs at the lowest prices. Free Shipping in India.<?php }?>"/>


<link type="text/css" rel="stylesheet" href="<?=base_url()?>min/?g=css&<?=str_replace(".","",APL_VER)?>">

<?php /*
<!-----<link href='http://fonts.googleapis.com/css?family=Philosopher:regular,italic,bold,bolditalic' rel='stylesheet' type='text/css'>----------------------------------->
*/ ?>
<script>images_path="<?=IMAGES_URL?>";base_url="<?=base_url()?>";site_url="<?=site_url()?>";</script>
<script src="<?=base_url()?>min/?g=js&<?=str_replace(".","",APL_VER)?>"></script>

<?php if($_SERVER['HTTP_HOST']=="snapittoday.com"){?>

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-16384379-5']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script> 

<?php }?>

<?php if(isset($socio)){?>
<script src="http://www.google.com/jsapi"></script>
<script type="text/javascript">google.load('friendconnect', '0.8');</script>
<script type="text/javascript">
gloadc=0;
google.friendconnect.container.loadOpenSocialApi({    site: '<?=GM_SITEID?>',    onload: function(securityToken) {	    gloadc++;	    if(gloadc>1)		    location="<?=site_url("auth/gm")?>";  }  });
</script>
<?php }?>
</head>


<body <?php if(isset($smallheader)) echo 'class="maincontainer"'?>>

<div align="center" class="maincontainer">

<?php if($page!="signup_alone"){

	$this->load->view("foretop"); ?>

<div class="header"<?php if($this->uri->segment(1)=="login" || $this->uri->segment(1)=="signup"){?> style="padding-bottom:0px;"<?php }?>>
<div align="center" class="container" style="background:transparent;">
<?php 
	$this->load->view("header");
?>
</div>
</div>
<?php }?>

<div class="mainsubcontainer" <?php if(!isset($notrans)){?>style="background:transparent;"<?php } ?>>

<div id="content" align="center" style="clear:both;">

<?php if(mobile_device_detect(true,false,true,true,true,true,true,false,false)){?>
	<div style="padding:5px;background:#ff9900;"><a style="color:#fff;text-decoration:none;" href="<?=site_url("gomobile")?>"><b>Go back to mobile site</b></a></div>
<?php } ?>

<?php
	$this->load->view("discovery_head");
?>

<?php
	switch($page)
	{
		case "showitem":
				$this->load->view("body/item");
				break;
		default:
				$this->load->view("body/$page");
				break;
	}
?>
</div>
</div>
<?php if(!isset($noheader)){?>
<div align="center" class="footercont">

<div class="container">

<?php 
if($page!="default" && $page!="agentlogin")
	$this->load->view("footer");
?>
</div>
</div>
<?php }?>

</div>

<?php
// if(in_array($page,array("showitem"))){
 if(0){
	$sidepopper=$this->dbm->getsidepopper();
?>
<div id="sidepopper">
<div class="head">
<a class="close">x</a>
RECOMMENDED FOR YOU
</div>
<div class="sidepoppercont">
	<a href="<?=site_url($sidepopper['url'])?>" class="url">
		<img width=100 src="<?=IMAGES_URL?>items/small/<?=$sidepopper['pic']?>.jpg" alt="<?=htmlspecialchars($sidepopper['name'])?>">
	</a>
	<h4><?=$sidepopper['name']?></h4>
	<h5>Rs <?=$sidepopper['price']?><img src="<?=IMAGES_URL?>instantcashback.png"></h5>
	<h6>Rs <?=$sidepopper['mrp']?></h6>
</div>
<div class="butt">
<a class="mlinks" href="<?=site_url($sidepopper['type']==0?"weeklysavings":"favs")?>">powered by <b><?=$sidepopper['type']==0?"Weekly Savings":"Fav".FAV_LIMIT?></b></a>
</div>
</div>
<?php }?>


<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
</body>
</html>
<?php } ?>
