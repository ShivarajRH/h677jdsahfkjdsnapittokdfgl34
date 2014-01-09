<?php
if(isset($itemdetails['enddate']))
$ed=$itemdetails['enddate'];
$item=$itemdetails;
?>
<script type="text/javascript" src="<?=base_url()?>js/countdown.js"></script>
<script>
$(function(){
	ed=new Date(<?=date("Y,",$ed)?><?=(date("n",$ed)-1)?>,<?=date("j,G,",$ed)?><?=(date("i",$ed)+1-1)?>,<?=(date("s",$ed)+1-1)?>);
	$('#countdown').countdown({
		layout: '{hnn} <span style="color:#ff9900">Hrs</span> {mnn} <span style="color:#ff9900">Mins</span> {snn} <span style="color:#ff9900">Secs</span>',
		until: ed});
	
	$("a.fanblink").fancybox({
		'zoomOpacity'			: true,
		'zoomSpeedIn'			: 300,
		'zoomSpeedOut'			: 200,
		'callbackOnStart'		: resetHeight
//		'easingIn'				: 'easeOutBack',
//		'easingOut'				: 'easeInBack'
	});
	$("a.vlink").click(function(){
		id=$(this).attr("vidid");
		$("div#video").html('<object width="560" height="340"><param name="movie" value="http://www.youtube.com/v/'+id+'&hl=en_US&fs=1&"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/'+id+'&hl=en_US&fs=1&" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="560" height="340"></embed></object>');
	});
	$("a.vlink,a#infolink").fancybox({
		'zoomOpacity'			: true,
		'zoomSpeedIn'			: 300,
		'zoomSpeedOut'			: 200,
		'loadOnClose'			: resetHeight
//		'easingIn'				: 'easeOutBack',
//		'easingOut'				: 'easeInBack'
	});
	$("#qty").change(function(){
		$("#showprice").html("<img src='<?=base_url()?>images/rs_small.png'> "+$("#qty").val()*<?=$itemdetails['price']?>);
	});
	$(".com").hover(function(){$(this).css("background","#eee");},function(){$(this).css("background","transparent");});
	$(".com").click(function(){location.href="<?=site_url("comments/{$itemdetails['brandname']}/{$itemdetails['name']}/{$itemdetails['id']}")?>"});
	$("#qty").val(1);
});
<?php if(1){?>
function fbthis()
{
	window.open("<?=site_url("fbthis/{$itemdetails['dealid']}/{$itemdetails['id']}")?>","fbthis","location=no,height=600,width=800,menubar=0,status=no,toolbar=no,top=200,left=200");
}
function twthis()
{
	window.open("<?=site_url("twthis/{$itemdetails['dealid']}/{$itemdetails['id']}")?>","fbthis","location=no,height=400,width=800,menubar=0,status=no,toolbar=no,top=200,left=200");
}
function sendmail()
{
	$("#sendmail").hide();
	$("#inf").html("Please wait...").show();
	em=$("#emails").val();
	$.post("<?=site_url("jx/sendmail")?>",{email:em,deal:<?=$itemdetails['dealid']?>,item:<?=$itemdetails['id']?>},function(resp){$("#inf").html("Email sent").fadeOut(2000);});
}

function showitemphoto(id)
{
	$(".itemphotos").hide();
	$("#itemphoto"+id).fadeIn("slow");
}
<?php }?>
</script>
<style>
 #inf{
 display:none;
 background:#FCF8F5;
 padding:5px;
 margin:5px;
 font-size:14px;
 font-weight:bold;
 float:left;
 width:250px;
 }
div#fancy_div{
background:#fff;
}
/*
#countdown{
margin-left:5px;
background:#fff url(<?=base_url()?>images/countdown.png) no-repeat;
height:58px;
width:307px;
}
#countdown div{
float:left;
padding-top:27px;
}*/
 .headingtext{
 background:url(<?=base_url()?>images/fade.png) repeat-x;
 height:46px;
 padding:8px 0px 0px 15px;
 font-weight:normal;
 }
#countdown{
font-size:16px;
}
#countdown div b{
color:#e06823;
font-size:18px;
}
.breadcrumbs{
font-size:12px;
color:#606060;
}
.breadcrumbs a{
font-size:11px;
color:blue;
text-decoration:underline;
}
.breadcrumbs a:hover{
text-decoration:none;
}
.saleconter{
	font-size:150%;
margin-top:10px;margin-right:0px;float:left;border:2px solid #ccc;width:350px;padding:10px;color:#555;
}
#priceerror{
color:red;
display:none;
}
</style>

<div style="font-family:tahoma;font-size:14px;font-weight:bold;padding-bottom:5px;text-align: left"><h1><?=$itemdetails['name']?></h1><div style="font-weight: normal; color: rgb(136, 136, 136); font-size: 13px;">from <b><?=$itemdetails['brandname']?></b></div></div>

<div id="infoc" style="display:none">
<div style="font-size:15px;">
<div class="infoh" style="color:#ff9900;font-size:25px;font-weight:bold;padding-bottom:7px;">Info</div>
<div class="infocnt" id="info"></div>
</div>
</div>
<div id="video" style="display:none;"></div>

<?php if($item['dealtype']==0){?>

<div align="left" style="padding:0px;">

<div style="float:left;margin-bottom:15px;float:left;min-width:460px;min-height:150px;text-align:center;padding-right:20px;">

<a rel="photos" href="<?=base_url()?>images/items/big/<?=$itemdetails['pic']?>.jpg" class="fanblink itemphotos" style="display:block;background:#000 url(<?=base_url()?>images/items/<?=$itemdetails['pic']?>.jpg) bottom right no-repeat;">
	<img src="<?=base_url()?>images/ended.png" style="width:460px;">
</a>

<?php 
if(count($itemresources[0])>0)
foreach($itemresources[0] as $pic){?>
<div  id="itemphoto<?=$pic['id']?>" style="background:#000 url(<?=base_url()?>images/items/<?=$pic['id']?>.jpg) bottom right no-repeat;display:none;" class="itemphotos">
<a rel="photos" style="padding:3px;" href="<?=base_url()?>images/items/big/<?=$pic['id']?>.jpg" class="fanblink">
<img src="<?=base_url()?>images/ended.png" style="width:460px;"></a>
</div>
<?php 
}
?>

</div>

</div>
<div align="left" style="padding:0px 10px;">
<div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like href="<?=site_url("deal/".$itemdetails['url'])?>" send="true" width="350" show_faces="false" font=""></fb:like>
</div>

<div style="float:left">

	<div style="float:left;clear:left;">
		<!--[if IE 6]>
		<style>
		.saleconter{
		background:#fff;
		background-image:none;
		}
		</style>
		<![endif]-->
		
		<div class="saleconter">
		<div style="margin:15px 0px;padding-top:0px;height:60px;float:left;width:100%;border-bottom:1px solid #eee;">
		<div style="float:left;width:33%;" align="center">
		<div>Value</div>
		<span style="text-decoration: line-through; margin-top: 5px; display: inline-block; font-size: 110%; color: #999">Rs. <?=number_format($itemdetails['orgprice']);?></span>
		</div>
		<div style="float:left;width:33%;" align="center">
		<div>Discount</div>
		<span style="color: #ff9900; margin-top: 5px; display: inline-block; font-size: 110%;"><?=ceil(($itemdetails['orgprice']-$itemdetails['price'])/$itemdetails['orgprice']*100)?>%</span>
		</div>
		<div style="float:left;width:33%;" align="center">
		<div>Save</div>
		<span style="color:#ff9900; margin-top: 5px; display: inline-block; font-weight: normal; font-size: 110%;">Rs. <?=number_format(ceil($itemdetails['orgprice']-$itemdetails['price']));?></span>
		</div>
		</div>
		<?php /* if($itemdetails['shipsin']!="" && $itemdetails['shipsin']!="0"){?>
		<div style="font-size:100%;width:100px;color:#3ED247;clear:both;font-weight:bold;padding:10px;padding-bottom:0px;float:right;">
		In Stock
		<div style="color: rgb(153, 153, 153); margin-top: 5px; font-weight: normal; font-size: 80%;">Ships in <span style="color:#e80021"><?=$itemdetails['shipsin']?> days</span></div>
		</div>
		<?php } */?>
		<?php if($itemdetails['available']<$itemdetails['quantity'] && $itemdetails['enddate']>time()){?>
		<?php }?>
		<div style="padding:0px;padding-top:0px;float:left;">
		<div style="color:#444;">Snap it today @
		<span style="margin-left:20px;color:#ff9900;font-weight:bold;margin-top: 5px;font-size: 140%">Rs. <?=$itemdetails['price']?></span>
		</div>
		</div>
	</div>
		
</div>

<div style="clear:both;float:left;"></div>
<div style="float:left;font-size:60%;font-weight:bold;margin-top: 5px;">
<img src="<?=base_url()?>images/soldout.png" style="float:left">
<img style="float:left;margin-left:10px;" src="<?=base_url()?>images/cod.png">
</div>

<div align="center" style="clear:both;float:left;width:370px;margin:5px 0px;padding:5px;background:#F7DF00;">
<div style="font-size:160%;">Still interested in this deal?</div>
<div>Place a request below and we will get back to you shortly</div>
<div style="margin-top:5px;"><img src="<?=base_url()?>images/placearequest.png"></div>
</div>

</div>


<div style="display: inline-block;width: 100%">

<div style="float:right;font-size:13px;width:545px;padding-right:10px;" align="left">
<div style="padding-top:10px;margin-top:-5px;padding-bottom:2px;font-size:19px;border-bottom:2px solid #ccc;">Description</div>
<div style="text-align: justify;font-size: 110%;">
<?=$itemdetails['description1']?>
<div style="padding-left:30px;padding-top:5px;">
<?=$itemdetails['description2']?> </div>
</div>
</div>

<div style="float:left;font-size:13px;width:305px;padding-right:10px;" align="left">
<div id="photosvideos" style="width: 100%;display: inline-block;padding-right: 20px; padding-left: 10px;">
<div style="padding-top:10px;padding-bottom:2px;font-size:15px;border-bottom:2px solid #ccc;"><b>More Photos &amp; Videos</b></div>
<div style="clear:left;float:left;">
<a name="extraphotos"></a>
<div style="padding-top: 0px; width: 100%; text-align: left;">
<?php 
if(count($itemresources[0])>0)
foreach($itemresources[0] as $pic){?>
<a rel="photos" style="padding:3px;" href="javascript:void(0)" onclick='showitemphoto("<?=$pic['id']?>")'><img src="<?=base_url()?>images/items/thumbs/<?=$pic['id']?>.jpg" height="50" style="background:#fff;padding:5px;border:1px solid #eee;"></a>
<?php 
}
?>
<?php 
if(count($itemresources[1])>0)
	echo '<div style="padding-top:10px;padding-bottom:2px;font-size:19px;border-bottom:2px solid #ccc;">Videos</div>';
foreach($itemresources[1] as $pic){?>
<a vidid="<?=$pic['id']?>" href="#video" class="vlink"><img src="http://i3.ytimg.com/vi/<?=$pic['id']?>/default.jpg" width="100" style="border:1px solid #eee;"></a>
<?php 
}
?>
</div>
</div>

<div style="clear:left;float:left;padding-right: 20px; padding-left: 0px;">
<div style="padding-top:10px;padding-bottom:2px;font-size:13px;font-weight:bold;border-bottom:2px solid #ccc;">Leave Comments</div>
<div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:comments href="<?=site_url("deal/".$itemdetails['url'])?>" num_posts="2" width="300"></fb:comments>
</div>

<div style="clear:both"></div>
</div>
</div>

</div>
<!--[if IE]>
<style>
.extimglink img{
height:130px;
}
</style>
<![endif]-->
<?php }else{?>
<?php }?>