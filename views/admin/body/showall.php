<script type="text/javascript">
<!--
$(document).ready(function(){
	//To switch directions up/down and left/right just place a "-" in front of the top/left attribute
	//Vertical Sliding
	$('.boxgrid.slidedown').hover(function(){
		$(".cover", this).stop().animate({top:'-260px'},{queue:false,duration:300});
	}, function() {
		$(".cover", this).stop().animate({top:'0px'},{queue:false,duration:300});
	});
	//Horizontal Sliding
	$('.boxgrid.slideright').hover(function(){
		$(".cover", this).stop().animate({left:'325px'},{queue:false,duration:300});
	}, function() {
		$(".cover", this).stop().animate({left:'0px'},{queue:false,duration:300});
	});
	//Diagnal Sliding
	$('.boxgrid.thecombo').hover(function(){
		$(".cover", this).stop().animate({top:'260px', left:'325px'},{queue:false,duration:300});
	}, function() {
		$(".cover", this).stop().animate({top:'0px', left:'0px'},{queue:false,duration:300});
	});
	//Partial Sliding (Only show some of background)
	$('.boxgrid.peek').hover(function(){
		$(".cover", this).stop().animate({top:'90px'},{queue:false,duration:160});
	}, function() {
		$(".cover", this).stop().animate({top:'0px'},{queue:false,duration:160});
	});
	//Full Caption Sliding (Hidden to Visible)
	$('.boxgrid.captionfull').hover(function(){
		$(".cover", this).stop().animate({top:'160px'},{queue:false,duration:160});
	}, function() {
		$(".cover", this).stop().animate({top:'260px'},{queue:false,duration:160});
	});
	//Caption Sliding (Partially Hidden to Visible)
	$('.boxgrid.caption').hover(function(){
		$(".cover", this).stop().animate({top:'160px'},{queue:false,duration:160});
	}, function() {
		$(".cover", this).stop().animate({top:'215px'},{queue:false,duration:160});
	});

	$('.boxgrid2.caption').hover(function(){
		$(".cover", this).stop().animate({top:'170px'},{queue:false,duration:160});
	}, function() {
		$(".cover", this).stop().animate({top:'200px'},{queue:false,duration:160});
	});

	$(".hoteldealcontainer").click(function(){
		location="<?=site_url("deal")?>/"+$(this).attr("deal");
	});
});
//-->
</script>
<div class="heading" align="center">
<div class="headingtext container">Today's Sale</div>
</div>
<div class="container" style="padding-top:5px;">
<?php
$i=0; 
foreach($hotelDeals as $hotelDeal)
{
?>
<div class="hoteldealcontainer" deal="<?=$hotelDeal['dealid']?>">
<div class="boxgrid<?php if($i>1) echo "2";?> caption">
<img src="<?=base_url()?>images/hotel_images/thumbs/<?=$hotelDeal['pic']?>.jpg">
<div class="cover boxcaption">
		<div class="head"><?=$hotelDeal['heading']." &ndash; ".$hotelDeal['city']?></div>
		<div><?=$hotelDeal['tagline']?></div>
		<div align="right" style="margin-right:30px;"><span style="font-family:verdana;background:#ff9900;padding:3px 5px;color:#fff;font-weight:bold;font-size:11px;">View Deal</span></div>
	</div>
</div>
</div>
<?php 
$i++;
}
?>
<?php 
$detail=array(
			"E.J.Victor",
			"Culin Home",
			"Prouna",
			"Serena & Lily",
			"Bojay",
			"Arte Italica",
			"Nature Stone",
			"Modern Twist",
			"Jardins En Fleur"
);
for($i=1;$i<=9;$i++)
{
?>
<div class="dealcontainer" deal="<?=$hotelDeal['dealid']?>">
<div class="boxgrid2 caption" style="vertical-align:center;">
<img src="<?=base_url()?>images/dummy/<?=$i?>.jpg" style="vertical-align:center;">
<div class="cover boxcaption">
		<div class="head"><?=$detail[$i-1]?></div>
		<div>Ends 20/12</div>
		<div align="right" style="margin-right:30px;"><span style="font-family:verdana;background:#ff9900;padding:3px 5px;color:#fff;font-weight:bold;font-size:11px;">View Deal</span></div>
	</div>
</div>
</div>
<?php }?>
</div>
<br style="clear:both;">
<?php 
/*
<script>
$(function(){
	$("#eslide").easySlider({
		auto: true,
		continuous: true,
		controlsShow: false
	});
});
</script>
<div style="margin-top:10px;font-family:'trebuchet ms';color:#f38;font-size:23px;font-weight:bold;">Hotel Deals</div>
<div id="eslide">
<ul>
	<li>
		<div class="imagecontainer">
			<img src="<?=base_url()?>images/hotel_images/1.jpg">
		</div>
		<div class="description">
			<div class="heading">Marigot Bay - St. Lucia</div>
			<div class="tagline">Modern marina lifestyle in the Caribbean</div>
		</div>
	</li>
	<li>
		<div class="imagecontainer">
			<img src="<?=base_url()?>images/hotel_images/2.jpg">
		</div>
		<div class="description">
			<div class="heading">Marigot Bay - St. Lucia</div>
			<div class="tagline">Modern marina lifestyle in the Caribbean</div>
		</div>
	</li>
	<li>
		<div class="imagecontainer">
			<img src="<?=base_url()?>images/hotel_images/3.jpg">
		</div>
		<div class="description">
			<div class="heading">Marigot Bay - St. Lucia</div>
			<div class="tagline">Modern marina lifestyle in the Caribbean</div>
		</div>
	</li>
	<li>
		<div class="imagecontainer">
			<img src="<?=base_url()?>images/hotel_images/4.jpg">
		</div>
		<div class="description">
			<div class="heading">Marigot Bay - St. Lucia</div>
			<div class="tagline">Modern marina lifestyle in the Caribbean</div>
		</div>
	</li>
</ul>
</div>"
*/
?>