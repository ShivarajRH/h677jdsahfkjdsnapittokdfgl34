<?php
if(isset($itemdetails['enddate']))
$ed=$itemdetails['enddate'];
$item=$itemdetails;
?>
<script type="text/javascript" src="<?=base_url()?>js/countdown.js"></script>
<script>
$(function(){
	$("#tellurpricedialog").dialog({
		autoOpen:false,
		width:400,
		buttons:{
			"Cancel":function(){
			$(this).dialog("close");
				},
			"Submit":function(){
				$("form",$(this)).submit();
//				$(this).dialog("close");
				}
		},
		modal:true
	});
	ed=new Date(<?=date("Y,",$ed)?><?=(date("n",$ed)-1)?>,<?=date("j,G,",$ed)?><?=(date("i",$ed)+1-1)?>,<?=(date("s",$ed)+1-1)?>);
	$('#countdown').countdown({
		layout: '<div style="float:left;padding-right:5px;"><b>{dn}</b> days</div><div style="float:left"><b>{hnn}</b> hrs</div><div style="padding-top:5px;clear:left;"><div style="padding-right:5px;clear:left;float:left;"><b>{mnn}</b> mins</div><div style="float:left"><b>{snn}</b> secs</div></div>',
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
	$("#qty").val(1);
	$("#pricereqform").submit(function(){
		msg="";
		if(!is_natural($("input[name=price]",$(this)).val()))
			msg+="<div>Enter price as number</div>";
		if(!is_natural($("input[name=qua]",$(this)).val()))
			msg+="<div>Enter quantity as number</div>";
		if(msg!="")
		{
			$("#priceerror").html(msg).show();
			return false;
		}
		return true;
	});
});
<?php if(isset($user)){?>
function addtocart()
{
	$(".infocnt").html("Please wait...");
	$("#fancy_inner").css("height","30%");			
	$("a#infolink").click();
	$(".infocnt").css("font-size","30px");
	$(".infoh").hide();
	pd="item=<?=$itemdetails['id']?>&qty="+$("#qty").val();
}
function sendmail()
{
	$("#sendmail").hide();
	$("#inf").html("Please wait...").show();
	em=$("#emails").val();
	$.post("<?=site_url("jx/sendmail")?>",{email:em,deal:<?=$itemdetails['dealid']?>,item:<?=$itemdetails['id']?>},function(resp){$("#inf").html("Email sent").fadeOut(2000);});
}
<?php }?>
</script>
<style>
 #sendmail{
 margin:10px;
 padding:10px;
 padding-top:10px;
 background:#FCF8F5;
 float:left;
 font-family:arial;
 font-size:13px;
 clear:left;
 display:none;
 }
 #inf{
 display:none;
 background:#FCF8F5;
 padding:5px;
 margin:5px;
 font-size:14px;
 font-family:arial;
 font-weight:bold;
 float:left;
 width:250px;
 }
div#fancy_div{
background:#fff;
color:#000;
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
 color:#222;
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
font-family:arial;
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
margin-top:15px;margin-right:0px;float:right;font-family:arial;border:1px solid #f1f3f0;background:#fff url(<?=base_url()?>images/sale.png) center right no-repeat;background-position: 245px -16px;width:280px;padding:10px;color:#555;
}
#priceerror{
color:red;
display:none;
}
</style>
<div id="infoc" style="display:none">
<div style="font-size:15px;font-family:'trebuchet ms'">
<div class="infoh" style="color:#ff9900;font-size:25px;font-weight:bold;padding-bottom:7px;">Info</div>
<div class="infocnt" id="info"></div>
</div>
</div>
<div id="tellurpricedialog" title="Name your price">
<form action="<?=site_url("newpricerequest")?>" method="post" style="background:#eee;padding:5px;font-weight:bold;font-size:13px;margin-top:10px;" id="pricereqform">
<input type="hidden" name="deal" value="<?=$item['url']?>">
<div id="priceerror"></div>
<table cellpadding="5">
<tr>
<td style="text-align:left">Unit Price : </td><td style="text-align:right"><img src="<?=base_url()?>images/rs_small.png"> <input style="bordeR:1px solid #aaa;text-align:left;" type="text" name="price" size="10"></td></tr>
<tr>
<td style="text-align:left">Quantity needed : </td><td style="text-align:right"><input type="text" name="qua" size="10" style="bordeR:1px solid #aaa;text-align:right;"></td></tr>
</table>
</form>
<div style="padding-top:5px;font-size:11px;color:#999;">If you feel the price for this item could be cheaper, quote your preferred price for the item and the quantity. We’ll get back to you, if we find it acceptable.</div>
</div>
<div id="video" style="display:none;"></div>
<div style="color:#3B5998;font-size:20px;font-weight:bold;font-family:'trebuchet ms';padding-bottom:0px;">
<a href="<?=site_url("fb/brand/{$itemdetails['brandname']}")?>" style="float:right"><img style="max-height:40px;" src="<?=base_url()?>images/brands/<?=$itemdetails['brandlogoid']?>.jpg"></a>
<b><?=$itemdetails['name']?></b><div style="font-weight:normal;font-size:15px;">from <?=$itemdetails['brandname']?>
</div>
</div>

<?php if($item['dealtype']==0){?>
<div align="left" style="padding:0px;">
<?php /*?><div class="commentside">
<div class="head">Comments</div>
<a href="#">How can I get two items of...</a>
<div style="padding-top:5px;"><a href="#" class="commentslink">25 comments</a></div>
</div>
*/?>
<div style="float:left;margin-bottom:15px;float:left;min-width:300px;min-height:150px;text-align:center;padding-right:20px;">
<img src="<?=base_url()?>images/items/<?=$itemdetails['pic']?>.jpg" style="max-height:200px;">
</div>
<div style="float:left">
<!--[if IE 6]>
<style>
.saleconter{
background:#fff;
background-image:none;
}
</style>
<![endif]-->

<div class="saleconter">
<span style="font-size:22px;"><img src="<?=base_url()?>images/rs.png" title="Rs"> <b style="color:#ff9900"><?=$itemdetails['price']?></b></span>
<div style="margin-left:2px;padding-top:5px;height:45px;float:left;width:100%;">
<div style="float:left;width:33%;margin-top:4px;margin-left:-5px;border-right:1px solid #FF9900" align="center">
<div style="color:#888;">Value</div>
<span style="text-decoration:line-through;"><?=$itemdetails['orgprice']?></span>
</div>
<div style="float:left;width:33%;margin-top:4px;border-right:1px solid #FF9900" align="center">
<div style="color:#888;">Discount</div>
<span style="font-weight:bold;"><?=ceil(($itemdetails['orgprice']-$itemdetails['price'])/$itemdetails['orgprice']*100)?>%</span>
</div>
<div style="float:left;width:33%;margin-top:4px;" align="center">
<div style="color:#888;">Save</div>
<span style="font-weight:bold;color:#ffaa00;"><?=ceil($itemdetails['orgprice']-$itemdetails['price'])?></span>
</div>
</div>
</div>
<?php if($itemdetails['shipsin']!="" && $itemdetails['shipsin']!="0"){?>
<div style="clear:both;font-family:arial;font-size:13px;font-weight:bold;color:#333;padding:10px;padding-bottom:0px;float:right;">
Ships in <span style="color:#e80021"><?=$itemdetails['shipsin']?> days</span>
</div>
<?php }?>
<?php if($itemdetails['available']<$itemdetails['quantity'] && $itemdetails['enddate']>time()){?>
<div style="float:right;clear:right;margin-top:10px;width:300px;height:98px;background:url(<?=base_url()?>images/time.png)">
<div style="padding-top:35px;padding-left:30px;font-size:16px;font-family:arial;">
Ends on <div style="color:#ff9900;padding-top:5px;font-weight:bold;"><?=date("l, d M, y",$itemdetails['enddate'])?></div>
</div>
</div>
<?php }?>



</div>
<div style="float:right;clear:both;padding-top:10px;">
<?php if($itemdetails['live']==1){?>
<?php if($itemdetails['enddate']>time()){
	if($itemdetails['available']<$itemdetails['quantity']){
	//if(isset($user))
	{
	if($itemdetails['live']==1){?>
<a style="margin:0px 10px;" href="<?php if(isset($user)){?>javascript:void(0)<?php }else echo site_url("agent");?>" <?php if(isset($user)){?>onclick="addtocart()"<?php }?>><img src="<?=base_url()?>images/addtocart.png"></a>
<img src="<?=base_url()?>images/tellusprice.png">
<?php }?>
<?php }?>
<?php }else{?>
<h3 style="color:red;">SOLD OUT</h3>
<?php }}else{?>
<h3 style="color:red;">DEAL EXPIRED</h3>
<?php }?>
<?php }?>
</div>


<style>
td{text-align:center;}
</style>
<!--[if IE]>
<style>
.extimglink img{
height:130px;
}
</style>
<![endif]-->
<div style="float:left;padding-top:20px;padding-left:20px;clear:both">
<div style="margin-top:10px;padding:0px;font-family:arial;font-size:13px;border:1px solid #aaa;-moz-border-radius:5px;margin-top:5px;margin-bottom:15px;">
<div style="color:brown;margin-left:10px;padding:0px 5px;margin-top:-10px;background:#fff;float:left;font-size:15px;font-weight:bold;" align="left">
You might also be interested in
</div>
<table style="clear:left" cellpadding="0" cellspacing="0">
<tr>
<?php foreach($extradeals as $i=>$deal){if($i>3) break;?>
<td width="25%" style="padding:10px;">
<table width="100%">
<tr><td align="center">
<div align="center" style="display:inline;max-height:200px;max-width:200px;overflow:hidden;">
<a class="extimglink" href="<?=site_url("fb/deal/".$deal['url'])?>">
<img style="max-height:130px;max-width:180px;" src="<?=base_url()?>images/items/thumbs/<?=$deal['pic']?>.jpg"></a>
</div>
</td></tr>
<tr><td style="padding-top:5px;"><a href="<?=site_url("fb/deal/".$deal['url'])?>" style="font-weight:bold;color:black;text-decoration:none;"><?=$deal['name']?></a></td></tr>
<tr><td><span style="color:#ff9900"><img src="<?=base_url()?>images/rs_small.png" title="Rs"></span> <b style="color:#ff9900;"><?=$deal['price']?></b> <span style="text-decoration:line-through;"><?=$deal['orgprice']?></span></td></tr>
<tr><td style="font-size:12px;"><b><?=ceil(($deal['orgprice']-$deal['price'])/$deal['orgprice']*100)?>%</b> OFF</td></tr>
</table>
</td>
<?php }?>
</tr>
<tr style="">
<?php foreach($extradeals as $i=>$deal){if($i>3) break;?>
<td align="center" style="padding:5px;background:#eee;<?php if($i==0) echo "-moz-border-radius-bottomleft:5px;"; elseif($i==2) echo "-moz-border-radius-bottomright:5px;";?>;">
<a style="color:blue;" href="<?=site_url("fb/brand/".$deal['brand'])?>"><?=$deal['brand']?></a>
</td>
<?php }?>
</tr>
</table>
</div>
</div>
</div>




<?php }else{?>




<style>
.cntdwn{
font-size:16px !important;
font-family:trebuchet ms;
font-size:15px !important;
}
.cntdwn div b{
color:#e06823 !important;
font-size:18px !important;
}

</style>




<div style="background:#fff;margin:0px;padding:0px;border:0px solid #ccc;">
<div style="clear:both;background:#fff;padding:0px" align="left">
<div style="font-weight:bold;font-family:trebuchet ms;font-size:22px;color:#8D201E;">
<img src="<?=base_url()?>images/brands/<?=$item['brandlogoid']?>.jpg" style="float:right;max-height:50px;">
<?=$item['name']?>
</div>
</div>
<div style="padding:10px;" align="left">

<table width="100%" cellpadding="0" cellspacing="0">
<tr>

<td align="center" valign="top" width="33%">
<a href="<?=base_url()?>images/items/big/<?=$itemdetails['pic']?>.jpg" class="fanblink"><img style="max-height:300px;" src="<?=base_url()?>images/items/<?=$item['pic']?>.jpg"></a>
<div align="center" style="color:#aaa">click image to enlarge</div>
</td>


<td width="33%" valign="top" align="left">

<div style="font-family:arial;font-size:13px;padding:0px 20px;padding-top:5px;margin-left:30px;" align="left">
<div style="padding:15px 0px;padding-bottom:20px;">
<div style="text-decoration:line-through;margin-left:5px;float:right;font-family:trebuchet ms;font-size:14px;font-weight:bold;color:#aaa;">Rs <?=$item['orgprice']?></div>Retail Price : 
</div>
<div style="clear:both;font-weight:bold;">
<div style="float:right;margin-top:-15px;margin-left:15px;-moz-border-radius:5px;background:#ff9900;font-family:trebuchet ms;font-size:14px;font-weight:bold;padding:3px 7px;color:white;">Rs <span style="font-size:21px;"><?=$item['price']?></span></div>Deal Price : 
</div>
</div>

<div style="margin-left:5px;margin-right:20px;margin-bottom:20px;">
<?php if($itemdetails['enddate']>time() && $itemdetails['dealtype']==1){?>
<div style="width:300px;font-family:arial;float:right;margin-top:10px;margin-left:30px;"> 
<div style="background:#eee;margin-left:10px;border:0px solid #e80021;-moz-border-radius:5px;padding:15px;padding-bottom:7px;">
<div style="color:#ff4400;font-weight:bold;padding-bottom:5px;font-size:13px;">
<?php if($itemdetails['available']<$itemdetails['quantity']){?>
<span style="float:right;font-size:17px;color:#22f;"><?=$itemdetails['available']?> bought</span>
Deal in progress
<?php }else{?>
<span style="color:#006699;">Got the deal!</span>
<?php }?>
</div>
<div style="float:right;" align="center">
<div style="margin-top:5px;clear:both;width:140px;height:15px;-moz-border-radius:5px;background:#fafafa;border:0px solid #ff9900;border-width:1px 1px 1px 1px;">
<div style="margin:0px;-moz-border-radius:5px;float:left;height:15px;max-width:140px;width:<?=floor($itemdetails['available']/$itemdetails['quantity']*140+1)?>px;background:#ff9900;"></div>
</div>
<div style="width:150px;">
<span style="font-size:12px;margin-left:0px;clear:both;float:left">0</span>
<span style="font-size:12px;margin-right:0px;float:right"><?=$itemdetails['quantity']?></span>
</div>
</div>
<div align="center" style="float:left;font-size:11px;padding:5px 0px 0px 10px;">
<?php if($itemdetails['available']<$itemdetails['quantity']){?>
<div style="font-weight:bold;font-size:20px;padding-bottom:5px;" align="center"><?=($itemdetails['quantity']-$itemdetails['available'])?></div>
<div>more needed </div><div>to get the deal</div>
<?php }else{?>
<div style="font-weight:bold;font-size:20px;padding-bottom:5px;" align="center"><?=($itemdetails['quantity'])?></div>
buyers <br>got this deal
<?php }?>
</div>
<?php if($itemdetails['available']<$itemdetails['quantity']){?>
<div style="clear:both;margin-top:5px;float:right;color:#444;font-size:12px;font-family:arial;"> 
<a title="Share this with a friend" href="#maildeal" onclick='$("#sendmail").show("slow")' style="margin-left:5px;"><img src="<?=base_url()?>images/mail_small.png"></a>
<a title="Tweet this deal" onclick="twthis()" href="javascript:void(0)" style="margin-left:5px;"><img src="<?=base_url()?>images/tw_small.png"></a>
<a title="Facebook this deal" href="javascript:void(0)" onclick="fbthis()" style="margin-left:5px;"><img src="<?=base_url()?>images/fb_small.png"></a>
</div>
<?php }?>
<br style="clear:both">
</div>
</div>
<?php }?>


<div style="clear:right;float:right;margin-bottom:10px;margin-top:5px;">
<?php ?>
</div>

</div>


</td>

<td align="left" width="33%" valign="center">
<div style="float:right;clear:right;padding-top:10px;">
<div style="clear:right;float:right;width:200px;">
<div align="right" class="cntdwn" style="padding-bottom:3px;padding-top:0px;padding-right:50px;float:right;margin-right:20px;text-align:right;" id="countdown">
</div>
</div>

<?php if($itemdetails['live']==1){?>
<div id="cartbox" style="clear:right;background:#f7f3f0;float:right;font-family:arial;width:200px;padding:15px 10px;padding-bottom:10px;color:#555;margin-bottom:20px;margin-top:20px;-moz-border-radius:5px;border:1px solid #f90;">
<?php if($itemdetails['enddate']>time()){
	if($itemdetails['available']<$itemdetails['quantity']){
	if(isset($user)){
	if($itemdetails['live']==1){?>
<div style="padding-left:20px;padding-top:3px;font-size:14px;font-family:'trebuchet ms';color:#333;">
Rs <b style="color:#ff9900;"><?=$itemdetails['price']?></b> at <b style="color:#ff9900;"><?=ceil(($itemdetails['orgprice']-$itemdetails['price'])/$itemdetails['orgprice']*100)?>%</b> discount
</div>
<div style="padding-top:10px;padding-left:10px;clear:both;">
<div style="float:right;font-weight:bold;padding-top:5px;padding-left:5px;" id="showprice"></div>
<div style="float:left;padding-left:0px;padding-top:3px;vertical-align:middle;font-size:14px;">Quantity : 
<select id="qty">
<option value="1" selected>1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
</select></div>
<a id="infolink" href="#infoc"></a>
<div style="clear:both;margin-left:0px;padding-top:15px;" align="center"><a href="javascript:void(0)" onclick="addtocart()"><img src="<?=base_url()?>images/addtocart.png"></a></div>
</div>
<?php }?>
<?php }else{?>
<h3 style="margin-top:-7px;color:#e80021;font-size:20px;">Sign Up : <span style="font-size:17px;">follow the links in top Menu</span></h3>
<?php }}else{?>
<h3 style="color:red;">SOLD OUT</h3>
<?php }}else{?>
<h3 style="color:red;">DEAL EXPIRED</h3>
<?php }?>
</div>
<?php }?>
<?php /*if(isset($user)){?>
<div style="float:right;clear:right;padding-top:0px;">
<div style="font-family:trebuchet ms" align="right">
<a href="javascript:void(0)" onclick='$("#pricereqform").show("slow");$("#cartbox").hide("slow");' style="color:#e80021;font-size:15px;font-weight:bold;">Tell your price
<?php if($item['live']==0) echo "to get the deal";?>
</a>
</div>
<form action="<?=site_url("newpricerequest")?>" method="post" style="background:#fafafa;padding:5px;font-weight:bold;font-size:13px;display:none;margin-top:10px;" id="pricereqform">
<input type="hidden" name="deal" value="<?=$item['url']?>">
<div id="priceerror"></div>
<table>
<tr>
<td style="text-align:left">Unit Price : </td><td style="text-align:left">Rs <input style="bordeR:1px solid #000;text-align:right;" type="text" name="price" size="5"></td></tr>
<tr>
<td style="text-align:left">Quantity needed : </td><td style="text-align:right"><input type="text" name="qua" size="5" style="bordeR:1px solid #000;text-align:right;"></td></tr>
</table>
<div align="left"><input type="submit" value="Submit" style="font-size:12px"></div>
</form>
</div>
<?php }*/?>
<style>
.cntdwn{
background:url(<?=base_url()?>images/clock.png) center right no-repeat;
}
</style>
<!--[if IE 6]>
<style>
.cntdwn{
background:#fff;
background-image:none;
}
</style>
<![endif]-->


<div style="clear:both;font-size:1px">&nbsp;</div>
</div>
</td>



</tr>
</table>
<div id="photosvideos" style="clear:both">
<a name="extraphotos"></a>
<div style="padding-top:0px;padding-right:20px;float:left;clear:left;">
<?php 
if(count($itemresources[0])>0)
	echo '<div style="margin-left:-5px;padding-bottom:5px;color:#8D201E;font-size:14px;font-weight:bold;">Photos</div>';
foreach($itemresources[0] as $pic){?>
<a rel="photos" style="padding:3px;" href="<?=base_url()?>images/items/big/<?=$pic['id']?>.jpg" class="fanblink"><img src="<?=base_url()?>images/items/<?=$pic['id']?>.jpg" height="70" style="background:#fff;padding:5px;border:1px solid #eee;"></a>
<?php 
}
?>
</div>
<div style="clear:left;float:left;">
<?php 
if(count($itemresources[1])>0)
	echo '<div style="font-weight:bold;margin-left:-5px;padding-top:10px;padding-bottom:5px;color:#8D201E;font-size:14px;">Videos</div>';
foreach($itemresources[1] as $pic){?>
<a vidid="<?=$pic['id']?>" href="#video" class="vlink"><img src="http://i3.ytimg.com/vi/<?=$pic['id']?>/default.jpg" width="100" style="border:1px solid #eee;"></a>
<?php 
}
?>
</div>
</div>
<div class="clear">&nbsp;</div>
</div>
</div>

<div style="background:#fff;padding:10px 0px;margin:0px;">
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td valign="top" align="left" style="font-family:arial;font-size:13px;padding:10px;background:#fff;width:650px;padding-right:7px;border:0px solid #ccc;">

<h3 style="color:#E8002E;padding:5px;font-family:trebuchet ms;border-bottom:1px solid #E8002E;">Description</h3>
<div style="padding:10px;">
<?=$item['description1']?>
<div style="font-weight:bold;">
<?=$item['description2']?>
</div>
</div>
</td>
<td width="10">&nbsp;</td>
<td valign="top" align="left" style="min-height:130px;font-family:arial;font-size:13px;padding:10px;background:#fff;width:264px;border:0px solid #ccc;">
<h3 style="color:#E8002E;padding:5px;font-family:trebuchet ms;border-bottom:1px solid #E8002E;">Recent Comment</h3>
<?php if($lastcomment!=false){?>
<div class="com" style="padding:5px;cursor:pointer;" onclick='location.href="<?=site_url("comments/{$itemdetails['brandname']}/{$itemdetails['name']}/{$itemdetails['id']}")?>"'>
<div style="float:left;margin:3px;padding:2px;border:1px solid #aaa;"><img src="<?=base_url()?>images/user.png"></div>
<div>
<b><?=$lastcomment['name']?></b> commented<br>
<?=$lastcomment['comment']?> <a href="<?=site_url("comments/{$itemdetails['brandname']}/{$itemdetails['name']}/{$itemdetails['id']}")?>#" style="color:#B9CA02;font-size:12px;">Read more</a>
</div>
<br style="clear:left">
</div>
<?php }else{?>
<div style="padding:10px 5px;">
<?php if(!isset($preview)){?>
<a class="link1" href="<?=site_url("comments/{$itemdetails['brandname']}/{$itemdetails['name']}/{$itemdetails['id']}")?>#" style="color:#B9CA02;font-size:16px;font-weight:bold;font-style:italic;">First to comment!</a>
<?php }else echo "<h4 style='margin-top:0px;'>No Comments</h4>";?>
</div>
<?php }?>
</td>
</tr>
</table>
</div>
<!--[if IE]>
<style>
.extimglink img{
height:130px;
}
</style>
<![endif]-->

<div style="background:#fff;border:0px solid #ccc;padding-right:15px;">
<div style="padding-top:20px;padding-left:20px;clear:both">
<div style="margin-top:10px;padding:0px;font-family:arial;font-size:13px;border:1px solid #aaa;-moz-border-radius:5px;margin-top:5px;margin-bottom:15px;">
<div style="color:brown;margin-left:10px;padding:0px 5px;margin-top:-10px;background:#fff;float:left;font-size:15px;font-weight:bold;" align="left">
You might also be interested in
</div>
<table style="clear:left" cellpadding="0" cellspacing="0">
<tr>
<?php foreach($extradeals as $i=>$deal){if($i>3) break;?>
<td width="25%" style="padding:10px;" align="center">
<table width="100%">
<tr><td align="center">
<div align="center" style="display:inline;max-height:200px;max-width:200px;overflow:hidden;">
<a class="extimglink" href="<?=site_url("deal/".$deal['url'])?>">
<img style="max-height:130px;max-width:180px;" src="<?=base_url()?>images/items/<?=$deal['pic']?>.jpg"></a>
</div>
</td></tr>
<tr><td align="center" style="padding-top:5px;"><a href="<?=site_url("deal/".$deal['url'])?>" style="font-weight:bold;color:black;text-decoration:none;"><?=$deal['name']?></a></td></tr>
<tr><td align="center"><span style="color:#ff9900">Rs</span> <b style="color:#ff9900;"><?=$deal['price']?></b> <span style="text-decoration:line-through;"><?=$deal['orgprice']?></span></td></tr>
<tr><td align="center" style="font-size:12px;"><b><?=ceil(($deal['orgprice']-$deal['price'])/$deal['orgprice']*100)?>%</b> OFF</td></tr>
</table>
</td>
<?php }?>
</tr>
<tr style="">
<?php foreach($extradeals as $i=>$deal){if($i>3) break;?>
<td align="center" style="padding:5px;background:#eee;<?php if($i==0) echo "-moz-border-radius-bottomleft:5px;"; elseif($i==2) echo "-moz-border-radius-bottomright:5px;";?>;">
<a style="color:blue;" href="<?=site_url("brand/".$deal['brand'])?>"><?=$deal['brand']?></a>
</td>
<?php }?>
</tr>
</table>
</div>
</div>
</div>

<?php }?>