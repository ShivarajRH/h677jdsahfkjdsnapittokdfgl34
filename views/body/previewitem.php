<?php 
if(isset($itemdetails['enddate']))
$ed=$itemdetails['enddate'];
?>
<script type="text/javascript" src="<?=base_url()?>js/countdown.js"></script>
<script>
$(function(){
	ed=new Date(<?=date("Y,",$ed)?><?=(date("n",$ed)-1)?>,<?=date("j,G,",$ed)?><?=(date("i",$ed)+1-1)?>,<?=(date("s",$ed)+1-1)?>);
	$('#countdown').countdown({
		layout: '<div><b>{dn}</b> days</div><div><b>{hnn}</b> hrs</div><div><b>{mnn}</b> mins</div><div><b>{snn}</b> secs</div>',
		until: ed});
	$("a.fanblink").fancybox({
		'zoomOpacity'			: true,
		'zoomSpeedIn'			: 300,
		'zoomSpeedOut'			: 200,
		'callbackOnStart'			: resetHeight
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
	$(".com").hover(function(){$(this).css("background","#eee");},function(){$(this).css("background","transparent");});
//	$(".com").click(function(){location.href="<?=site_url("comments/{$itemdetails['brandname']}/{$itemdetails['name']}/{$itemdetails['id']}")?>"});
});
</script>
<style>
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

</style>
<div class="headingtext" style="background:#fff;padding:0px;clear:both;margin:10px 0px">
<?=$itemdetails['name']?>
</div>
<div align="left" style="padding:0px;">
<div id="infoc" style="display:none">
<div style="font-size:15px;font-family:'trebuchet ms'">
<div style="color:#ff9900;font-size:25px;font-weight:bold;padding-bottom:7px;">Info</div>
<div id="info"></div>
</div>
</div>
<div id="video" style="display:none;"></div>
<div id="desc" style="background:#fff;display:none">
<div style="font-size:12px;font-family:'trebuchet ms'">
<div style="color:#ff9900;font-size:15px;font-weight:bold;padding-bottom:7px;">Description</div>
<?=$itemdetails['tagline']?>
</div>
</div>
<?php /*?><div class="commentside">
<div class="head">Comments</div>
<a href="#">How can I get two items of...</a>
<div style="padding-top:5px;"><a href="#" class="commentslink">25 comments</a></div>
</div>
*/?>
<div style="float:right;margin-left:5px;">
<div style="font-family:arial;background:#F7F3F0;width:300px;padding:10px;color:#555;">
<b style="font-size:22px;">Rs <?=$itemdetails['price']?></b>
<div style="margin-left:2px;padding-top:5px;height:45px;float:left;width:100%;">
<div style="float:left;width:33%;margin-top:4px;margin-left:-5px;border-right:1px solid #FF9900" align="center">
<div style="color:#bbb;">Value</div>
<span style="text-decoration:line-through;"><?=$itemdetails['orgprice']?>
</div>
<div style="float:left;width:33%;margin-top:4px;border-right:1px solid #FF9900" align="center">
<div style="color:#bbb;">Discount</div>
<span style="font-weight:bold;"><?=ceil(($itemdetails['orgprice']-$itemdetails['price'])/$itemdetails['orgprice']*100)?>%</span>
</div>
<div style="float:left;width:33%;margin-top:4px;" align="center">
<div style="color:#bbb;">Save</div>
<span style="font-weight:bold;color:#ffaa00;"><?=ceil($itemdetails['orgprice']-$itemdetails['price'])?></span>
</div>
</div>
<div style="padding-top:10px;clear:both;">
<div align="right"><a href="javascript:void(0)" style="margin-left:15px;" onclick="alert('Please sign up to buy this item')"><img src="<?=base_url()?>images/buynow.png"></a></div>
</div>
</div>
<div style="margin-top:10px;width:300px;height:98px;background:url(<?=base_url()?>images/time.png)">
<div id="countdown" style="padding-top:30px;padding-left:30px;font-size:13px;font-family:arial;"></div>
</div>

</div>
<div style="font-family:arial;">
<div style="float:left;padding-right:20px;"><img src="<?=base_url()?>images/items/<?=$itemdetails['pic']?>.jpg"></div>
<div>
<div style="color:#8D201E;font-size:25px;font-weight:bold;font-family:'trebuchet ms';padding-bottom:20px;"><b><?=$itemdetails['name']?></b><div style="font-weight:normal;font-size:15px;">from <?=$itemdetails['brandname']?></div></div>
<div style="font-size:14px;"><?=$itemdetails['tagline']?></div>
<div id="countdown" class="countdown" style="float:right;margin-top:20px;margin-right:10px;padding-left:3px;"></div>
<?php /*?>
<div class="smallblock" style="clear:left;width:auto;height:70px;">
<div style="font-family:'trebuchet ms';color:#00f;font-weight:bold;padding-bottom:10px;">Get this!</div>
Quantity : 
<select id="qty">
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
</select>
<!--<div style="padding-left:20px;padding-top:5px;">-->
<input onclick="addtocart()" type="button" value="Add to cart" style="font-family:verdana;background:#f51;padding:3px 5px;color:#fff;font-weight:bold;font-size:13px;">
<a id="infolink" href="#infoc"></a>
<!--</div>-->
</div>
<div class="smallblock" style="width:240px;height:70px;">
<div style="font-family:'trebuchet ms';font-weight:bold;font-size:24px;color:#fff;background:#5F70FF;margin:-5px;padding:2px 10px;margin-bottom:3px;">Rs <?=$itemdetails['price']?></div>
<div align="center" style="float:left;font-size:14px;font-family:verdana;color:#000;width:80px;">Value<br><b>Rs <?=$itemdetails['orgprice']?></b></div>
<div align="center" style="font-size:14px;float:left;font-family:verdana;width:80px;">Discount<br><b><?=ceil(($itemdetails['orgprice']-$itemdetails['price'])/$itemdetails['orgprice']*100)?>%</b></div>
<div align="center" style="font-size:14px;float:left;font-family:verdana;color:#000;width:80px;">You save <br><b>Rs <?=($itemdetails['orgprice']-$itemdetails['price'])?></b></div>
</div>
<div class="smallblock" style="height:70px;border:1px solid #aaa;background:#ddd;">
This deal ends in
<div style="font-size:13px;padding-top:4px;">
<?=$itemdetails['left']?>
</div>
</div>
<div style="padding-top:10px;float:left;clear:left;">
<?php 
if(count($itemresources[0])>0)
	echo '<div style="margin-left:-5px;padding-bottom:5px;color:#8D201E;font-size:14px;">Photos</div>';
foreach($itemresources[0] as $pic){?>
<a rel="photos" style="padding:3px;" href="<?=base_url()?>images/items/<?=$pic['id']?>.jpg" class="fanblink"><img src="<?=base_url()?>images/items/<?=$pic['id']?>.jpg" height="70" style="background:#fff;padding:5px;border:1px solid #eee;"></a>
<?php 
}
?>
</div>
<div style="float:left;clear:left;">
<?php 
if(count($itemresources[1])>0)
	echo '<div style="margin-left:-5px;padding-top:10px;padding-bottom:5px;color:#8D201E;font-size:14px;">Videos</div>';
foreach($itemresources[1] as $pic){?>
<a vidid="<?=$pic['id']?>" href="#video" class="vlink"><img src="http://i3.ytimg.com/vi/<?=$pic['id']?>/default.jpg" width="100" style="border:1px solid #eee;"></a>
<?php 
}
?>
</div>
*/?>
</div>
<br style="clear:both;">
<div style="float:right;width:310px;font-size:13px;">
<div style="margin-bottom:3px;padding-top:10px;padding-bottom:2px;font-family:arial;font-size:19px;border-bottom:2px solid #B9CA02;">Latest Comment</div>
<?php if($lastcomment!=false){?>
<div class="com" style="padding:5px;cursor:pointer;" onclick='location.href="<?=site_url("comments/{$itemdetails['brandname']}/{$itemdetails['name']}/{$itemdetails['id']}")?>"'>
<div style="float:left;margin:3px;padding:2px;border:1px solid #aaa;"><img src="<?=base_url()?>images/user.png"></div>
<div>
<b><?=$lastcomment['name']?></b> commented<br>
<?=$lastcomment['comment']?> <a href="<?=site_url("comments/{$itemdetails['brandname']}/{$itemdetails['name']}/{$itemdetails['id']}")?>#" style="color:#B9CA02;font-size:12px;">Read more</a>
</div>
</div>
<?php }else{?>
<div style="padding:10px 5px;">No comments
</div>
<?php }?>
</div>
<div style="width:600px;font-size:13px;">
<div style="padding-top:10px;padding-bottom:2px;font-family:arial;font-size:19px;border-bottom:2px solid #ffaa00;">Full description</div>
<div style="padding-left:10px;padding-top:5px;">
<b>
We've got your Valentine's preparation covered with fantastic £10 MobDeals all week here at Wahanda. With different treatments every day all at a fantastic price, we're making it easier for you to pamper yourself or treat a loved one to some special indulgence. Come back every day to see which treatment we're offering and get your Valentine's covered from top to toe
Good to know
</b>
<div style="padding-left:30px;padding-top:5px;">
    * Valid for one treatment from the list below (all 30 mins each)<br>
    * Location: The Essence Beauty Rooms, Hammersmith<br>
    * Available Monday - Sunday<br>
    * You will receive an e-voucher to print out and take with you to your appointment<br>
    * Voucher valid for 6 months<br>
    * Cannot be used in conjunction with any other offer<br>
 </div>
</div>
</div>
</div>
</div>