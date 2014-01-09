<script>
cpic=1;
totalpic=<?php if(isset($roomResources[0][0])) echo count($roomResources[0])+1; else echo "1";?>;
$(function(){
	$("#datepickr").datepicker({minDate: new Date(<?=date("Y",$startDate)?>,<?=(date("n",$startDate)-1)?>,<?=date("j",$startDate)?>), maxDate: new Date(<?=date("Y",$endDate)?>,<?=(date("n",$endDate)-1)?>,<?=date("j",$endDate)?>)});
	$("#nextbutton").click(nextpic);
	$("#previousbutton").click(prevpic);
	$(".rilink").click(function(){imgjump($(this).attr("imgi"));});
	$(".ytlink").click(function(){showvideo($(this).attr("vidi"));});
	$(".roomimage,#navbuttons").hover(function(){$("#navbuttons").show();},function(){$("#navbuttons").hide()});
});
function showvideo(id)
{
	$("#yttv").html('<object width="560" height="340"><param name="movie" value="http://www.youtube.com/v/'+id+'&hl=en_US&fs=1&"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/'+id+'&hl=en_US&fs=1&" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="560" height="340"></embed></object>');
	$("#yttv").show();
}
function nextpic(){
	if(cpic==totalpic)
			return;
	$("#ri"+cpic).hide();
	cpic++;
	$("#ri"+cpic).show();
}
function prevpic(){
	if(cpic==1)
		return;
	$("#ri"+cpic).hide();
	cpic--;
	$("#ri"+cpic).show();
}
function imgjump(i){
	$("#ri"+cpic).hide();
	cpic=i;
	$("#ri"+cpic).show();
}
</script>
<style>
#yttv{
clear:both;
display:none;
text-align:center;
padding:10px;
}
.ytlink,.rilink{
cursor:pointer;
}
.ui-datepicker{
font-size:10px;
}
.ui-datepicker td{
height:auto;
}
</style>
<div class="heading">
<div class="headingtext container">
<?=$roomDetail['heading']?> <span style="color:#eee;font-size:14px;"><?=$hotelDeal['heading']?></span>
</div>
</div>
<div class="container">
<div style="float:right;width:285px;max-width:285px;">
<div class="boxcaption" style="top:auto;left:auto;position:relative;height:auto;background:#000;float:right;width:265px;max-width:265px;margin-top:10px;padding:10px;padding-bottom:15px;" align="left">
<span style="font-family:'trebuchet ms';font-weight:bold;font-size:29px;color:#ff9900;">Rs <?=$roomDetail['price']?><span style="color:#fff;font-size:15px;">/night</span></span><br>
<span style="font-size:14px;font-family:verdana;color:#888;">Hotel's Price : Rs <?=$roomDetail['originalprice']?></span><br>
<span style="font-family:verdana;color:#777;">You save <span style="color:#ffaa00;">Rs <?=($roomDetail['originalprice']-$roomDetail['price'])?> (<?=ceil(($roomDetail['originalprice']-$roomDetail['price'])/$roomDetail['originalprice']*100)?>%)</span></span><br>
<div style="font-family:verdana;color:#eee;margin-top:5px;">
<b>Address</b> <br>
<div style="padding-left:20px;font-family:arial;font-size:14px;"><?=$hotelDeal['name']."<br>".$hotelDeal['address']?></div>
</div>
<div align="right" style="padding-top:10px;">
<input type="button" value="Add to cart" style="font-family:verdana;background:#f51;padding:3px 5px;color:#fff;font-weight:bold;font-size:13px;">
</div>
</div>
<div>
<img style="margin-top:10px;" src="http://maps.google.com/maps/api/staticmap?zoom=11&center=<?=$hotelDeal['latlong']?>&size=285x200&maptype=roadmap&markers=label:H|<?=$hotelDeal['latlong']?>&sensor=false&key=ABQIAAAAWyD0uIpgEIykuCpUwxqPkBRL_ux2NtmJmnSFnXVJl3L7ZGls-RRS9iAY0llKSZIZoXRxu5pvOXhv-A">
</div>
</div>
<div align="left" style="padding-top:10px;">
<div id="navbuttons" style="padding:5px;position:absolute;padding-top:175px;width:640px;">
<img id="nextbutton" src="<?=base_url()?>images/next.gif" style="cursor:pointer;float:right;"> <img id="previousbutton" style="cursor:pointer;" src="<?=base_url()?>images/previous.gif">
</div>
<img class="roomimage" id="ri1" src="<?=base_url()?>images/room_images/<?=$roomDetail['pic']?>.jpg">
<?php 
$i=2;
if(isset($roomResources[0][0]))
foreach($roomResources[0] as $res)
{
?>
<img class="roomimage" style="display:none;" id="ri<?=$i?>" src="<?=base_url()?>images/room_images/<?=$res?>.jpg">
<?php
$i++; 
}
?>
<div class="boxcaption" style="margin-top:-30px;color:#fff;font-family:verdana;width:640px;height:auto;padding:5px;" align="right"><?=$roomDetail['heading']." &ndash; ".$hotelDeal['heading']?></div>
</div>
<div style="clear:both;margin-top:15px;">
<div style="float:left;font-family:arial;color:#333;font-weight:bold;">
<p style="margin:0px;margin-bottom:5px;">Availability</p>
<div style="margin-left:10px;" id="datepickr"></div>
</div>
<?php 
/*
<div style="margin-left:15px;float:left;font-family:arial;color:#eee;font-weight:bold;">
Photos
<p style="margin:5px;width:300px;height:150px;">
<img src="<?=base_url()?>images/room_images/thumbs/<?=$roomDetail['pic']?>.jpg" height="75">
<?php if(isset($roomResources[0][0])) foreach($roomResources[0] as $res){?>
<img src="<?=base_url()?>images/room_images/thumbs/<?=$res?>.jpg" width="75">
<?php }?>
</p>
</div>
<div style="margin-left:15px;float:left;font-family:arial;color:#eee;font-weight:bold;">
Videos
</div>
*/
?>
<div style="margin-left:15px;float:left;font-family:arial;color:#333;font-weight:bold;">
Amenities
<div style="margin-top:10px;margin-left:20px;">
<table cellpadding="0" cellspacing="3" border="0" style="float:left;width:180px;">
<?php
$i=0; 
$i2=0;
foreach($amenities as $amenity => $value)
{
	if($i>2)
	{
		echo"</table><table cellpadding='0' cellspacing='3' border='0' style='float:left;width:180px;'>";
		$i=0;
	}
?>
<tr>
<td align="center"><img style="<?php if($hotelAmenities[$i2]==0) echo"opacity:0.2;";?>margin:3px;" src="<?=base_url()?>images/amenities/<?=$amenity?>.gif"></td>
<td align="left" width="100%" valign="center" <?php if($hotelAmenities[$i2]==0) echo "style='color:#aaa;text-decoration:line-through;opacity:0.2;'";?>><?=$value?></td>
</tr>
<?php
$i2++;
$i++; 
}
?>
</table>
</div>
</div>
</div>
<div style="clear:both;font-family:arial;font-size:17px;font-weight:bold;color:#333;padding-top:10px;">
Photos <span style="font-size:11px;">(<?=(count($roomResources[0])+1)?>)</span>
<p style="margin:10px 0px 0px 15px;">
<a imgi="1" class="rilink" href="#"><img src="<?=base_url()?>images/room_images/thumbs/<?=$roomDetail['pic']?>.jpg" height="80"></a>
<?php $i=2; if(isset($roomResources[0][0])) foreach($roomResources[0] as $res){?>
<a  class="rilink" imgi="<?=$i?>" href="#"><img style="float:left;margin-right:20px;" src="<?=base_url()?>images/room_images/thumbs/<?=$res?>.jpg" height="80"></a>
<?php $i++;}?>
</p>
</div>
<div style="clear:both;font-family:arial;font-size:17px;font-weight:bold;color:#333;margin-top:15px;">
Videos <span style="font-size:11px;">(<?=(count($roomResources[1]))?>)</span>
<p style="margin:10px 0px 0px 15px;">
<?php if(isset($roomResources[0][0])) foreach($roomResources[1] as $res){?>
<img class="ytlink" vidi="<?=$res?>" style="float:left;margin-right:20px;" src="http://i3.ytimg.com/vi/<?=$res?>/default.jpg" height="90">
<?php }?>
</p>
</div>
<div id="yttv"></div>
</div>
<br style="clear:both;">