<?php 
$item=$itemdetails;
?>
<?php
if(isset($itemdetails['enddate']))
$ed=$itemdetails['enddate'];
?>
<script type="text/javascript" src="<?=base_url()?>js/countdown.js"></script>
<script>
$(function(){
	ed=new Date(<?=date("Y,",$ed)?><?=(date("n",$ed)-1)?>,<?=date("j,G,",$ed)?><?=(date("i",$ed)+1-1)?>,<?=(date("s",$ed)+1-1)?>);
	$('#countdown').countdown({
//		layout: '<div style="float:left;padding-right:5px;"><b>{dn}</b> days</div><div><b>{hnn}</b> hrs</div><div style="padding-top:5px;"><div style="padding-right:5px;clear:left;float:left;"><b>{mnn}</b> mins</div><div><b>{snn}</b> secs</div></div>',
			layout: '{d<}<div style="float:left;padding-right:5px;"><b>{dn}</b> {dl}</div>{d>}<div style="float:left;padding-right:10px;"><b>{hnn}</b> hrs</div><div style="padding-top:5px;"><div style="padding-right:5px;float:left;"><b>{mnn}</b> mins</div><div style="float:left;"><b>{snn}</b> sec</div></div>',		until: ed});
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
		$("#showprice").html("Rs "+$("#qty").val()*<?=$itemdetails['price']?>);
	});
	$(".com").hover(function(){$(this).css("background","#eee");},function(){$(this).css("background","transparent");});
	$(".com").click(function(){location.href="<?=site_url("comments/{$itemdetails['brandname']}/{$itemdetails['name']}/{$itemdetails['id']}")?>"});
	$("#qty").val(1);
});
function fbthis()
{
	window.open("<?=site_url("fbthis/{$itemdetails['dealid']}/{$itemdetails['id']}")?>","fbthis","location=no,height=600,width=800,menubar=0,status=no,toolbar=no,top=200,left=200");
}
function twthis()
{
	window.open("<?=site_url("twthis/{$itemdetails['dealid']}/{$itemdetails['id']}")?>","fbthis","location=no,height=400,width=800,menubar=0,status=no,toolbar=no,top=200,left=200");
}
function addtocart()
{
	var itemid=<?=$itemdetails['id']?>;
	qty=$("#qty").val();
	$(this).attr("disabled",true);
	$(".infocnt").html("Please wait...");
	$("#fancy_inner").css("height","30%");			
	$("a#infolink").click();
	$(".infocnt").css("font-size","30px");
	$(".infoh").hide();
	pd="item="+itemid+"&qty="+qty;
	$.post("<?=site_url('jx/addtocart')?>",pd,function(resp){
		$(this).attr("disabled",false);
		if(resp==0)
			$("#info").html("This sale is not valid anymore!");
		else if(resp==1)
			$("#info").html("Please reduce your quantity. Items are not available for given quantity");
		else if(resp==2)
			$("#info").html("This item is already present in your cart. If you want to edit it, please remove it first.");
		else if(resp==3)
			$("#info").html("This item added to cart!");
		else
			$("#info").html("Unknown error occured. Please try again. Sorry for this inconvenience."); 	
		if(resp==3)
			window.location.href="<?=site_url("checkout")?>";
//			$("a#cartlink").click();
		else
		{
			$(".infocnt").css("font-size","15px");
			$("#fancy_inner").css("height","30%");
			$("#fancy_div").html($("#infoc").html());			
			$(".infoh").show();
//			$("a#infolink").click();
		}
	});
}
function sendmail()
{
	$("#sendmail").hide();
	$("#inf").html("Please wait...").show();
	em=$("#emails").val();
	$.post("<?=site_url("jx/sendmail")?>",{email:em,deal:<?=$itemdetails['dealid']?>,item:<?=$itemdetails['id']?>},function(resp){$("#inf").html("Email sent").fadeOut(2000);});
}
</script>

<style>
.cntdwn{
font-size:16px;
font-family:trebuchet ms;
font-size:15px;
}
.cntdwn div b{
color:#e06823;
font-size:18px;
}

</style>

<div id="infoc" style="display:none">
<div style="font-size:15px;font-family:'trebuchet ms'">
<div class="infoh" style="color:#ff9900;font-size:25px;font-weight:bold;padding-bottom:7px;">Info</div>
<div class="infocnt" id="info"></div>
</div>
</div>


<div style="margin:0px -16px;padding:10px 0px;border:2px solid #ccc;border-width:2px 0px;background:#fafaf0;">


<div style="background:#fff;margin:0px;padding:10px;border:2px solid #ccc;">
<div style="clear:both;background:#2E2E2D;padding:10px" align="left">
<div style="font-weight:bold;font-family:trebuchet ms;font-size:15px;color:#fff"><?=$item['name']?></div>
</div>
<div style="clear:both;padding:10px;" align="left">
<img src="<?=base_url()?>images/brands/<?=$item['brandlogoid']?>.jpg" style="max-height:50px;float:right">

<div align="center" style="float:left;width:300px;">
<img style="max-height:300px;" src="<?=base_url()?>images/items/<?=$item['pic']?>.jpg">
</div>


<div style="float:left">

<div style="float:left;font-family:arial;font-size:13px;padding:20px;" align="left">
<div style="padding:15px 0px;padding-bottom:30px;">
Retail Price : <div style="text-decoration:line-through;margin-left:5px;float:right;font-family:trebuchet ms;font-size:14px;font-weight:bold;color:#aaa;">Rs <?=$item['orgprice']?></div>
</div>
<div style="clear:both;font-weight:bold;">
Deal Price : <div style="float:right;margin-top:-15px;margin-left:15px;-moz-border-radius:5px;background:#ff9900;font-family:trebuchet ms;font-size:14px;font-weight:bold;padding:10px;color:white;">Rs <span style="font-size:21px;"><?=$item['price']?></span></div>
</div>
</div>

<div style="clear:left;float:left;margin-left:5px;margin-right:20px;margin-bottom:20px;">
<?php if($itemdetails['enddate']>time() && $itemdetails['dealtype']==1){?>
<div style="clear:right;width:300px;font-family:arial;float:right;margin-top:10px;"> 
<div style="background:#fff;border:0px solid #e80021;-moz-border-radius:5px;padding:15px;padding-bottom:7px;">
<div style="color:#ff4400;font-weight:bold;padding-bottom:5px;">
<?php if($itemdetails['available']<$itemdetails['quantity']){?>
<span style="float:right;font-size:15px;color:#22f;"><?=$itemdetails['available']?> bought</span>
Deal in progress
<?php }else{?>
<span style="color:#006699;">Got the deal!</span>
<?php }?>
</div>
<div style="clear:both;float:right;width:140px;height:50px;background:#fafafa;border:0px solid #ff9900;border-width:0px 1px 1px 0px;">
<div style="float:left;height:50px;max-width:140px;width:<?=floor($itemdetails['available']/$itemdetails['quantity']*140+1)?>px;background:#ffdd00;"></div>
<img src="<?=base_url()?>images/tipwhite.png" style="margin-top:-50px;left:0px;">
</div>
<div align="center" style="float:left;font-size:11px;padding:5px 0px 0px 10px;">
<?php if($itemdetails['available']<$itemdetails['quantity']){?>
<div style="font-weight:bold;font-size:20px;padding-bottom:5px;" align="center"><?=($itemdetails['quantity']-$itemdetails['available'])?></div>
more needed <br>to get the deal
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


</div>


<div style="clear:right;float:right;padding-top:10px;">

<div style="clear:right;background:#fafafa;float:right;font-family:arial;width:200px;padding:15px 10px;padding-bottom:10px;color:#555;margin-bottom:40px;margin-top:20px;-moz-border-radius:5px;border:1px solid #f90;">
<?php if($itemdetails['enddate']>time()){
	if($itemdetails['available']<$itemdetails['quantity']){
	if(!isset($preview)){?>
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
<div style="clear:both;margin-left:65px;padding-top:15px;"><a href="javascript:void(0)" onclick="addtocart()"><img src="<?=base_url()?>images/addtocart.png"></a></div>
</div>
<?php }else{?>
Sign Up to buy
<?php }}else{?>
<h3 style="color:red;">SOLD OUT</h3>
<?php }}else{?>
<h3 style="color:red;">DEAL EXPIRED</h3>
<?php }?>
</div>

<div style="clear:right;float:right">
<div class="cntdwn" style="padding-bottom:3px;background:url(<?=base_url()?>images/clock.png) center right no-repeat;padding-top:0px;padding-right:50px;float:right;margin-right:20px;" id="countdown">
</div>
</div>

<div style="clear:both;font-size:1px">&nbsp;</div>
</div>
<div style="clear:both;font-size:1px">&nbsp;</div>
</div>
</div>


<div style="background:#fafaf0;padding:10px 0px;margin:0px;">

<div align="left" style="font-family:arial;font-size:13px;padding:10px;background:#fff;float:left;width:650px;padding-right:7px;border:2px solid #ccc;">

<h3 style="color:#E8002E;padding:5px;font-family:trebuchet ms;border-bottom:1px solid #E8002E;">Description</h3>
<?=$item['description1']?>
<div style="font-weight:bold;">
<?=$item['description2']?>
</div>
</div>

<div align="left" style="min-height:130px;font-family:arial;font-size:13px;padding:10px;background:#fff;float:left;width:264px;margin-left:10px;border:2px solid #ccc;">
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
</div>

<div style="clear:both;font-size:1px;">&nbsp;</div>
</div>

</div>
<?php
