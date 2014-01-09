<script type="text/javascript">
<!--
$(function(){
	$('.boxgrid.caption').hover(function(){
		$(".cover", this).stop().animate({top:'120px'},{queue:false,duration:160});
	}, function() {
		$(".cover", this).stop().animate({top:'160px'},{queue:false,duration:160});
	});

	$(".hoteldealcontainer").click(function(){
		location="<?=site_url("roomdeal")?>/"+$(this).attr("deal");
	});
	
});
//-->
</script>
<div class="heading" align="center">
<div class="headingtext container" align="left"><?=$hotelDeal['heading']?></div>
</div>
<div class="container">
<div style="margin-top:10px;" align="center"><img src="<?=base_url()?>images/hotel_images/<?=$hotelDeal['pic']?>.jpg"></div>
<div class="boxcaption" style="font-size:25px;font-weight:bold;font-family:'trebuchet ms';color:#fff;margin-top:-70px;width:935px;height:auto;padding:10px 0px 10px 10px;">
<div style="padding-right:10px;font-size:13px;float:right;">Ends on <?=date("D d M y",$hotelDeal['enddate'])?></div>
<?=$hotelDeal['heading']?> &ndash; <?=$hotelDeal['city']?>
<div style="margin-top:5px;font-size:13px;"><?=$hotelDeal['tagline']?></div>
</div>
<div style="color:#444;font-family:verdana;font-size:15px;font-weight:bold;padding-top:20px;">Rooms</div>
<div>
<?php 
if($roomDetails!=false)
foreach($roomDetails as $roomDetail)
{
?>
<div class="hoteldealcontainer" deal="<?=$roomDetail['roomid']?>">
<div class="boxgrid caption"  style="width:280px;height:200px;">
<img src="<?=base_url()?>images/room_images/thumbs/<?=$roomDetail['pic']?>.jpg">
<div class="cover boxcaption" style="top:160px;">
		<div style="font-size:20px;margin:10px 0px;"><?=$roomDetail['heading']?></div>
		<div style="margin:0px;">Rs <?=$roomDetail['price']?> <span style="text-decoration:line-through;font-size:12px;">Rs <?=$roomDetail['originalprice']?></span></div>
		<div align="right" style="float:right;margin-right:30px;"><span style="font-family:verdana;background:#ff9900;padding:3px 5px;color:#fff;font-weight:bold;font-size:11px;">View Room</span></div>
	</div>
</div>
</div>
<?php 
}
?>
</div>
</div>
<br style="clear:both;">