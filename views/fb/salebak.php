<?php 
if($dealstatus=="active")
$ed=$dealdetails[0]['enddate'];
else
$ed=$dealdetails[0]['startdate'];
?>
<script type="text/javascript" src="<?=base_url()?>js/countdown.js"></script>
<script>
$(function(){
	ed=new Date(<?=date("Y,",$ed)?><?=(date("n",$ed)-1)?>,<?=date("j,G,",$ed)?><?=(date("i",$ed)+1-1)?>,<?=(date("s",$ed)+1-1)?>);
	$('#countdown').countdown({
		layout: '<div><b>{dn}</b> days</div><div><b>{hnn}</b> hrs</div><div><b>{mnn}</b> mins</div><div><b>{snn}</b> secs</div>',
		until: ed});
$('.boxgrid.caption').hover(function(){
	$(".cover", this).stop().animate({top:'160px'},{queue:false,duration:160});
}, function() {
	$(".cover", this).stop().animate({top:'215px'},{queue:false,duration:160});
});

$('.boxgrid2.caption').hover(function(){
	$(".cover", this).stop().animate({top:'120px'},{queue:false,duration:160});
}, function() {
	$(".cover", this).stop().animate({top:'150px'},{queue:false,duration:160});
});

$(".itemcontainer").click(function(){
<?php if($dealstatus=="active"){?>
	location="<?=site_url("saleitem/$category/{$dealdetails[0]['brandname']}")?>/"+$(this).attr("item");
<?php }?>
});
});
function fbthis()
{
	window.open("<?=site_url("fbthis/{$dealdetails[0]['dealid']}")?>","fbthis","location=no,height=600,width=800,menubar=0,status=no,toolbar=no,top=200,left=200");
}
function twthis()
{
	window.open("<?=site_url("twthis/{$dealdetails[0]['dealid']}")?>","fbthis","location=no,height=400,width=800,menubar=0,status=no,toolbar=no,top=200,left=200");
}
function sendmail()
{
	$("#sendmail").hide();
	$("#inf").html("Please wait...").show();
	em=$("#emails").val();
	$.post("<?=site_url("jx/sendmail")?>",{email:em,deal:<?=$dealdetails[0]['dealid']?>},function(resp){$("#inf").html("Email sent").fadeOut(2000);});
}
</script>
<style>
/*#countdown{
margin-left:5px;
background:#fff url(<?=base_url()?>images/countdown<?php if($dealstatus!='active') echo "2";?>.png) no-repeat;
height:58px;
width:307px;
}
#countdown div{
float:left;
padding-top:23px;
}*/
#countdown{
font-size:12px;
}
.boxgrid2{
width:205px;
height:180px;
}
 .boxgrid2 .boxcaption{
 top:150px;
 font-size:11px;
 }
 .headingtext{
 background:url(<?=base_url()?>images/fade.png) repeat-x;
 height:46px;
 color:#222;
 padding:8px 0px 0px 15px;
 font-weight:normal;
 }
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
 </style>
<div class="headingtext" style="margin-top:20px;">
<?=$dealdetails[0]['brandname']?>
<div style="display:none;float:right;font-size:13px;" align="right">
<div style="padding-bottom:5px;">In <a href="<?=site_url("category/$category")?>" style="font-size:17px;text-decoration:none;color:#7d332a;"><?=$category?></a></div>
<?php if($dealstatus=="active") echo "ends on ".date("d/m",$dealdetails[0]['enddate']); else if($dealstatus=="inactive") echo "starts on ".date("d/m",$dealdetails[0]['startdate']); else echo "Expired";?>
</div>
</div>
<div align="left" style="padding-top:10px;">
<?php 
if($dealstatus=="expired")
	echo '<div style="font-size:25px;color:#343;padding:50px 0px;" align="center">Sorry! This sale is no longer active</div>';
else
{
?>
<?php /*?>
<div class="commentside">
<div class="head">Comments</div>
<?php 
if($comment!=false){?>
<a href="#"><?=$comment['comment']?></a>
<div style="padding-top:5px;"><a href="#" class="commentslink"><?=$comment['countc']?> comments</a></div>
<?php }else{?>
<a href="#">Be the first to comment!</a>
<?php }?>
</div>
*/
?>
<div>
<div style="float:right;margin-left:5px;">
<div style="font-family:arial;background:#F7F3F0;height:80px;width:300px;padding:10px;color:#555;">
<?php if($dealstatus=="active"){?>
<b>Rs <?=$prices['min']?> to Rs <?=$prices['max']?></b>
<div style="margin-left:2px;height:45px;float:left;width:100%;">
<div style="float:left;width:33%;margin-top:4px;margin-left:-5px;border-right:1px solid #FF9900" align="center">
<div style="color:#bbb;">Value</div>
<span style="text-decoration:line-through;"><?=$prices['minorg']?>
</div>
<div style="float:left;width:33%;margin-top:4px;border-right:1px solid #FF9900" align="center">
<div style="color:#bbb;">Discount</div>
<span style="font-weight:bold;"><?=ceil(($prices['minorg']-$prices['min'])/$prices['minorg']*100)?>%</span>
</div>
<div style="float:left;width:33%;margin-top:4px;" align="center">
<div style="color:#bbb;">Save</div>
<span style="font-weight:bold;color:#ffaa00;"><?=ceil($prices['minorg']-$prices['min'])?></span>
</div>
</div>
<div style="padding-top:5px;">Items for sale : <b><?=count($dealdetails)?></b></div>
<?php }else{?>
This sale is not yet active
<div style="padding-top:5px;">Starts in <?=date("d/m",$dealdetails[0]['startdate'])?></div>
<?php }?>
</div>
<div style="margin-top:10px;width:300px;height:98px;background:url(<?=base_url()?>images/time<?php if($dealstatus=="inactive") echo "2"?>.png)">
<div id="countdown" style="padding-top:30px;padding-left:30px;font-size:13px;font-family:arial;"></div>
</div>
</div>
<div><img style="float:left;margin-bottom:10px;margin-right:10px;" src="<?=base_url()?>images/items/<?=$dealdetails[0]['dealpic']?>.jpg">
<div style="padding:0px;font-family:verdana;font-size:12px;">
<div style="color:#8D201E;font-size:25px;font-weight:bold;font-family:'trebuchet ms';padding-bottom:20px;text-transform:uppercase;"><?=$dealdetails[0]['brandname']?></div>
<div style="padding-left:10px;"><?=$dealdetails[0]['description']?></div>
<?php 
/*
<div class="smallblock">
<div style="font-weight:bold;">
<?php if($dealstatus=="active"){?>
Time left
<?php }else{?>
Starts in
<?php }?>
</div>
<div style="height:100%;vertical-align: center;"><?=$dealdetails[0]['left']?></div>
</div>
<div class="smallblock" style="background:#ddd;border:1px solid #aaa;height:auto;">
This deal is <b><?php if($dealstatus=="active") echo "On"; else echo "Off";?></b>!
<?php if($dealstatus=="active") echo "since <b>".date("d/m",$dealdetails[0]['startdate']); else echo "starts <b>".date("d/m",$dealdetails[0]['startdate']);?></b>
</div>
*/
?>
</div>
</div>
<br style="clear:both;"></br>
<!--<img src="<?=base_url()?>images/share.png">-->
<div class="share">
<div class="mail" onclick='$("#sendmail").show()'></div>
<div class="fb" onclick='fbthis()'></div>
<div class="tweet" onclick='twthis()'></div>
</div>
<div id="inf"></div>
<div id="sendmail" style="displays:none">
<div style="font-family:'trebuchet ms';font-size:19px;margin-bottom:5px;">Send this deal to friends</div>
<div style="float:right;font-size:11px;">please separate email addresses with comma</div>
<div><b>Email addresses : </b></div><textarea id="emails" style="width:450px;height:90px;"></textarea>
<div align="right" style="margin-top:5px;"><input type="button" value="Send" onclick='sendmail()'></div>
</div>
<div style="clear:both;padding-top:10px;font-family:'trebuchet ms';font-size:19px;font-weight:bold;color:#8D201E;">Items for sale</div>
<?php
$i=0; 
foreach($dealdetails as $item)
{
?>
<div class="itemcontainer" item="<?=$item['itemname']."/".$item['id']?>">
<div class="boxgrid2 caption" style="vertical-align:center;">
<img src="<?=base_url()?>images/items/<?=$item['pic']?>.jpg" style="vertical-align:center;">
<div class="cover boxcaption" <?php if($dealstatus!="active") echo' style="background:#ccc;color:#555;"';?>>
		<div class="head"><?=$item['itemname']?></div>
<?php  if($dealstatus=="active") {?>
		<div>Price Rs <b><?=$item['price']?></b> <span style="text-decoration:line-through;"><?=$item['orgprice']?></span></div>
		<div style="margin-right:30px;">you save Rs <b><?=($item['orgprice']-$item['price'])?></b> <?=ceil(($item['orgprice']-$item['price'])/$item['orgprice']*100)?>%<span style="margin-top:-10px;margin-right:-10px;float:right;font-family:verdana;background:#ff9900;padding:2px 5px;color:#fff;font-weight:bold;font-size:11px;">Get it!</span></div>
<?php }else{?>
		<div style="margin-right:30px;"><span style="float:right;font-family:verdana;background:#aaa;padding:2px 5px;color:#fff;font-weight:bold;font-size:11px;">Coming Soon</span></div>
<?php }?>
</div>
</div>
</div>
<?php }?>
<br style="clear:both;">
</div>
<?php }?>

</div>