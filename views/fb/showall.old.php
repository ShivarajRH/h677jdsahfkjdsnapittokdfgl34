<script type="text/javascript" src="<?=base_url()?>js/countdown.js"></script>
<script type="text/javascript"><!--
<!--
endDates=new Array();
$(document).ready(function(){
	$(".hvr").hover(function(){$(this).css("background","#eee");},function(){$(this).css("background","transparent");});
	//Caption Sliding (Partially Hidden to Visible)3
	for(i=0;i<endDates.length;i++)
	{
//		var newYear = new Date(); 
//		endDates[i] = new Date(newYear.getFullYear() + 1, 1 - 1, 1); 
//		alert(endDates[i].getHours());
		ed=endDates[i];
		$('#countdown'+i).countdown({
			layout: '{d<}<div style="float:left;padding-right:5px;"><b>{dn}</b> {dl}</div>{d>}<div style="float:left;padding-right:10px;"><b>{hnn}</b> hrs</div><div style="padding-top:5px;"><div style="padding-right:5px;float:left;"><b>{mnn}</b> mins</div><div style="float:left;"><b>{snn}</b> sec</div></div>',
//			layout: '{d<}<div class="days"><div style="color:#bbb">{dl}</div>{dn}</div>{>d}<div class="hrs"><div style="color:#bbb">hrs</div>{hnn}</div><div class="mins"><div style="color:#bbb">mins</div>{mnn}</div><div class="secs"><div style="color:#bbb">secs</div>{snn}</div>',
			until: ed});
	}	
	$('.boxgrid.caption').hover(function(){
		$(".cover", this).stop().animate({top:'160px'},{queue:false,duration:160});
	}, function() {
		$(".cover", this).stop().animate({top:'215px'},{queue:false,duration:160});
	});

	$('.boxgrid2.caption').hover(function(){
		$(".cover", this).stop().animate({top:'130px'},{queue:false,duration:160});
	}, function() {
		$(".cover", this).stop().animate({top:'160px'},{queue:false,duration:160});
	});

	$(".activedealcontainer").click(function(){
		location="<?=site_url("brand")?>/"+$(this).attr("deal");
	});
	$(".activedealcontainer").each(function(i){
//		$(this).css("float","left");
//		$(this).css("width","690px");
		$(this).css("left",(690*i)+"px");
		$(this).css("position","absolute");
		totaldeals++;
	});
//	$(".activedealcontainer").hover(function(){window.clearTimeout(sliderTimer);},function(){sliderTimer=window.setTimeout("slider()",3000);});
//	sliderTimer=window.setTimeout("slider()",5000);
	$(".sliderlinks").click(function(){
		$("#sliderlink"+curslide).css("border-left-width","1px");
		$("#sliderlink"+curslide).css("background","#EAE5DC");
		movesliderto($(this).attr("sliderid"));
		$(this).css("border-left-width","0px");
		$(this).css("background","#F7F3F0");
	});
	$(".dealcontainer").click(function(){
		location="<?=site_url("category")?>/"+$(this).data("catid");
	});

});
totaldeals=0;
curslide=0;
function movesliderto(i)
{
	i--;
//	window.clearTimeout(sliderTimer);
	obj=$(".activedealcontainer");
		if(i>curslide)
		obj.animate({
			left:"-="+(690*(i-curslide))+"px"
		},1000);
		else
			obj.animate({
				left:"+="+(690*(curslide-i))+"px"
			},1000);
		curslide=i;
//		sliderTimer=window.setTimeout("slider()",5000);
}
function slider(i)
{
//	window.clearTimeout(sliderTimer);
	obj=$(".activedealcontainer");
	if(curslide+1==totaldeals)
	{
	obj.animate({
		left:"+="+(690*(totaldeals-1))+"px"
	},3000);
	curslide=-1;
//	sliderTimer=window.setTimeout("slider()",5000);
	}
	else
	{
		obj.animate({
			left:"-=690px"
		},1000);
//		sliderTimer=window.setTimeout("slider()",5000);
	}
	curslide++;
}
//-->
--></script>
<div class="headingtext" style="font-size:24px;padding-top:5px;padding-left:5px;padding-bottom:0px;"></div>
<div style="padding-top:0px;" align="left">
<?php 
/*
<?php
$i=0; 
foreach($activedeals as $deal)
{
?>
<div class="dealcontainer" deal="<?=$deal['category']."/".$deal['name']."/".$deal['dealid']?>"">
<div class="boxgrid2 caption" style="vertical-align:center;">
<img src="<?=base_url()?>images/items/<?=$deal['pic']?>.jpg" style="vertical-align:center;">
<div class="cover boxcaption">
		<div class="head"><?=$deal['name']?></div>
		<div>Ends <?=date("d/m",$deal['enddate'])?></div>
		<div style="margin-right:30px;"><?=$deal['left']?> left<span style="margin-top:-10px;float:right;font-family:verdana;background:#ff9900;padding:2px 5px;color:#fff;font-weight:bold;font-size:11px;">View Sale</span></div>
	</div>
</div>
</div>
<?php }?>
<?php
$i=0; 
foreach($inactivedeals as $deal)
{
?>
<div class="dealcontainer" deal="<?=$deal['category']."/".$deal['name']."/".$deal['dealid']?>">
<div class="boxgrid2 caption" style="vertical-align:center;">
<img src="<?=base_url()?>images/items/<?=$deal['pic']?>.jpg" style="vertical-align:center;">
<div class="cover boxcaption" style="background:#ccc;color:#555;">
		<div class="head"><?=$deal['name']?></div>
		<div>Starts <?=date("d/m",$deal['startdate'])?></div>
		<div style="margin-right:30px;"><?=$deal['left']?> to go<span style="margin-top:-10px;float:right;font-family:verdana;background:#aaa;padding:2px 5px;color:#fff;font-weight:bold;font-size:11px;">Coming Soon</span></div>
	</div>
</div>
</div>
<?php }?>
*/
?>
<style>
.activedealcontainer{
	font-family:'trebuchet ms';
	font-size:14px;
	width:690px;
	display:inline;
	height:100%;
	cursor:pointer;
}
.boxc{
width:200px;
font-family:arial;
margin:0px 20px;
border:1px solid #AFA799;
padding:3px;
-moz-border-radius:5px;
text-align:left;
}
.boxc .head{
background:#AFA799;
color:#fff;
font-family:'trebuchet ms';
font-size:16px;
font-weight:bold;
padding:5px;
}
.sliderlinks{
border-bottom:1px solid #E1DACC;
background:#EAE5DC;
}
#sliderlink0{
background:#F7F3F0;
border-left-width:0px;
}
#sliderlink4{
border-bottom:0px;
}
#sliderlink4 img{
border:0px;
background:#fff;
}
.days,.hrs,.mins,.secs{
width:50px;
text-align:center;
float:left;
height:35px;
width:60px;
margin:5px 0px;
}
.days,.hrs,.mins{
border-right:1px solid #ff9900;
}
.cntdwn{
font-size:16px;
}
.cntdwn div b{
color:#e06823;
font-size:18px;
}

</style>
<?php /*
<div style="float:right">
<div class="boxc" style="width:135px;margin:0px;">
<div class="head" style="background:#444;">Top Categories</div>
<?php $i=0;foreach($categories[0] as $category){?>
<div style="margin:3px;font-size:13px;font-family:arial;"><a style="text-decoration:none;color:#222;" href="<?=site_url("category/{$category['name']}")?>"><?=$category['name']?></a></div>
<?php if($i>4) break;$i++;}?>
</div>
<div class="boxc" style="margin:0px;margin-top:5px;width:135px;">
<div class="head" style="background:#444;">Top Brands</div>
<?php $i=0;foreach($activedeals as $deal){?>
<div align="left" style="font-size:13px;margin:3px;"><a style="text-decoration:none;color:#222;" href="<?=site_url("brand/".$deal['name'])?>"><?=$deal['name']?></a></div>
<?php if($i>4) break;$i++;}?>
</div>
</div>
*/
?>
<div align="left" style="width:920px;margin-top:10px;margin-bottom:10px;">
<div style="width:220px;float:right;font-family:'trebuchet ms';font-size:13px;height:245px;border:0px solid #BFBBB3;border-left-width:0px">
<!-- <h3>Available Brands</h3>--->
<div style="height:100%;padding-top:11px;">
<?php 
$i=0; 
foreach($activedeals as $deal)
{
?>
<div class="sliderlinks" id="sliderlink<?=$i?>" align="center" style="<?php if($i==0){?>-moz-border-radius-topright:5px;<?php }?><?php if($i==3 || count($activedeals)==($i+1)){?>-moz-border-radius-bottomright:5px;border-bottom:0px;<?php }?>cursor:pointer;padding:10px;height:40px;" sliderid="<?=($i+1)?>"><img style="vertical-align:middle;max-width:130px;height:30px;margin-top:5px;" src="<?=base_url()?>images/brands/<?=$deal['brandlogoid']?>.jpg"></div>
<?php 
if($i==3)break;$i++;}?>
</div>
</div>
<div id="maindcontainer" style="padding-top:15px;-moz-border-radius:10px 5px 5px 10px;background:#F7F3F0;border:0px solid #BFBBB3;border-right-width:0px;position:relative;width:690px;height:255px;overflow:hidden;float:right;">
<?php
$i=0; 
foreach($activedeals as $deal)
{
$ed=$deal['enddate'];
?>
<div class="activedealcontainer" align="center" deal="<?=$deal['brandname']?>">
<div align="right" style="float:left;padding-left:20px;padding-right:20px;padding-top:10px;padding-bottom:5px;">
<img src="<?=base_url()?>images/items/<?=$deal['pic']?>.jpg" height="190" style="float:left;vertical-align:center;border:2px solid #EF9A48;max-width:200px">
<img src="<?=base_url()?>images/sale.png" style="display:none;margin-top:5px;margin-left:-70px;z-index:20000;">
</div>
<div style="padding-left:20px;padding-top:0px;">
<div style="margin-top:-7px;font-weight:bold;float:right;color:#444;font-size:13px;padding-top:0px;padding-right:10px;" align="right">Ends <?=date("d/m",$deal['enddate'])?></div>
<div class="headingtext" align="left" style="overflow:hidden;padding-top:10px;color:#8D201E;font-size:30px;">
<a style="text-decoration: none;color:#8D201E;" href="<?=site_url("sale/".$deal['category']."/".$deal['name']."/".$deal['dealid'])?>"><nobr><?=$deal['name']?></nobr></a>
</div>
<div align="left" style="font-size:16px;text-transform:uppercas;margin-left:0px;margin-right:10px;padding-top:10px;height:50px;overflow:hidden;"><?=$deal['tagline']?></div>
<div class="price" align="left">Rs <b><?=$deal['price']?></b></div>
</div>
<div style="clear:right;margin-top:13px;">
<div style="width:100%;padding-top:10px;padding-left:5px;" align="left">
<a style="padding:5px;color:#fff;background:#f90;font-weight:bold;text-decoration:none;font-family:trebuchet ms;font-size:14px;" href="<?=site_url("saleitem/".$deal['itemid'])?>">
<!--<img src="<?=base_url()?>images/buyme.png">-->view deal
<!--<div style="float:left;width:99px;height:37px;padding-top:7px;font-weight:bold;padding-left:40px;background:url(<?=base_url()?>images/blue_buy.gif);font-size:25px;color:#fff;">Buy</div>-->
</a>
<?php /*?>
<div style="margin-left:50px;height:45px;float:left;background:#fff;width:215px;">
<div style="float:left;width:70px;margin-top:4px;margin-left:0px;border-right:1px solid #FF9900" align="center">
<div style="color:#999;">Value</div>
<span style="text-decoration:line-through;"><?=$deal['minorgprice']?>
</div>
<div style="float:left;width:70px;margin-top:4px;border-right:1px solid #FF9900" align="center">
<div style="color:#999;">Discount</div>
<span style="font-weight:bold;"><?=ceil(($deal['minorgprice']-$deal['minprice'])/$deal['minorgprice']*100)?>%</span>
</div>
<div style="float:left;width:70px;margin-top:4px;" align="center">
<div style="color:#999;">Save</div>
<span style="font-weight:bold;color:#ffaa00;"><?=ceil($deal['minorgprice']-$deal['minprice'])?></span>
</div>
</div>
*/?>
<div class="cntdwn" style="padding-bottom:3px;background:url(<?=base_url()?>images/clock.png) center right no-repeat;padding-top:0px;padding-right:50px;float:right;margin-right:20px;" id="countdown<?=$i?>">
</div>
<?php /*?>
<div class="pricebar">
<div style="padding-top:22px;height:38px;">
<div class="buy"><a class="buy" href="<?=site_url("sale/".$deal['category']."/".$deal['name']."/".$deal['dealid'])?>"></a></div>
<div class="from">Rs <b><?=$deal['minprice']?></b></div>
<div class="to">Rs <b><?=$deal['maxprice']?></b></div>
<div id="countdown<?=$i?>" class="countdown"><?=$deal['left']?> left</div>
</div>
</div>
<?php */?>
</div>
</div>
<?php $item=$deal;?>
<div style="padding-left:15px;clear:both;color:#5F0806;font-family:trebuchet ms;font-size:18px;font-weight:bold;">
<div style="text-align:left;float:left;padding-right:10px;">
<div style="float:left;text-align:left;min-width:100px;padding-top:10px;overflow:hidden"><nobr>
<a href="<?=site_url("saleitem/".$item['itemid'])?>" style="color:#5F0806;text-decoration:none;"><?=$item['itemname']?></a></nobr></div>
<?php if($deal['dealtype']==1){?>
<div style="margin-top:10px;margin-left:5px;float:left;width:50px;height:18px;background:#fafafa;border:0px solid #ff9900;border-width:0px 1px 1px 0px;">
<div style="float:left;height:18px;max-width:50px;width:<?=floor($item['available']/$item['quantity']*50+1)?>px;background:#ffdd00;"></div>
<img src="<?=base_url()?>images/tip3.png" style="margin-top:-18px;left:0px;">
</div>
<?php }?>
</div>
</div>
<script>
endDates[<?=$i?>]=new Date(<?=date("Y,",$ed)?><?=(date("n",$ed)-1)?>,<?=date("j,G,",$ed)?><?=(date("i",$ed)+1-1)?>,<?=(date("s",$ed)+1-1)?>);
</script>
<br style="clear:both">
</div>
<?php if($i>3) break;$i++;}?>
</div>
<br style="clear:both;">
</div>
<?php 
/*
<div style="width:100%;margin-top:30px;float:left;">
<div style="width:33%;float:left;" align="center">
<div class="boxc">
<div class="head">Top Brands</div>
<?php $i=0;foreach($activedeals as $deal){?>
<div align="center" style="padding:5px 0px;"><a href="<?=site_url("brand/".$deal['name'])?>"><img src="<?=base_url()?>images/brands/<?=$deal['brandid']?>.jpg"></a></div>
<?php if($i>4) break;$i++;}?>
</div>
</div>
<div style="width:33%;float:left;" align="center">
<div class="boxc">
<div class="head">Top Categories</div>
<?php $i=0;foreach($categories[0] as $category){?>
<div style="cursor:pointer;margin:5px;"><a style="text-decoration:none;color:#222;" href="<?=site_url("category/{$category['name']}")?>"><?=$category['name']?></a></div>
<?php if($i>4) break;$i++;}?>
</div>
</div>
<div style="width:33%;float:left;" align="center">
<div class="boxc">
<div class="head">Online Stores</div>
<?php $stores=array("Reebok Online","Buy Samsung","Get Tissot","Great Sale!","Exclusive VIA");?>
<?php for($i=0;$i<=4;$i++){?>
<div style="cursor:pointer;padding:5px;"><?=$stores[$i]?></div>
<?php }?>
</div>
</div>
<br style="clear:both;">
</div>
<div syle="padding:10px;" align="center">
<?php 
foreach($menu[1] as $brand){?>
<a href="<?=site_url("brand/{$brand['name']}")?>" style="padding:5px;"><img src="<?=base_url()?>images/brands/<?=$brand['id']?>.jpg"></a>
<?php }?>
</div>
*/?>
<div style="margin-left:10px;margin-top:0px;">
<?php /*?>
<div style="float:right;width:250px;" align="left">
<div style="font-family:'trebuchet ms';font-size:18px;padding-bottom:2px;margin-bottom:5px;margin-top:10px;border-bottom:2px solid #B9CA02;">The Community</div>
<?php foreach($comments as $comment){?>
<div class="com hvr" onclick='location.href="<?=site_url("showcomments/{$comment['itemid']}")?>#<?=$comment['id']?>"' style="cursor:pointer;clear:left;padding:5px;border-bottom:1px solid #ccc;">
<div style="float:left;margin-right:5px;border:1px solid #aaa;padding:2px;"><img src="<?=base_url()?>images/items/<?=$comment['pic']?>.jpg" style="max-height:80px;" width="50"></div>
<div style="font-family:arial;font-size:13px;">
<span style="color:#B9CA02;font-weight:bold;"><?=$comment['itemname']?></span>
<div style="font-size:12px;">
<?php if($comment['special']!=0){?><img src="<?=base_url()?>images/special<?=$comment['special']?>.png"><?php }?> <b><?=$comment['username']?></b> <i>commented</i><br>
<?=$comment['comment']?> <a class="link1" href="<?=site_url("showcomments/{$comment['itemid']}")?>#<?=$comment['id']?>" style='color:#B9CA02;font-size:11px;'><nobr>Read more</nobr></a>
</div>
</div>
</div>
<?php }?>
</div>
*/?>
<?php /*?>
<div style="width:650px;float:left">
<div style="color:#222;font-family:'trebuchet ms';font-size:22px;font-weight:bold;margin-bottom:0px;margin-top:10px;">Top Brands</div>
<?php 
$msgs=array("upto 50% off","from rs.400","flat sale 60%","free gifts","just rs.500","upto 70% OFF","upto 12% off","upto 40% off","upto 20% off");$i=0;
foreach($activedeals as $deal){?>
<div style="float:left;width:160px;text-align: center;padding:5px 0px;height:60px;">
<a style="color:#ff9900;text-decoration: none;" href="<?=site_url("brand/{$deal['name']}")?>">
<img src="<?=base_url()?>images/brands/<?=$deal['brandlogoid']?>.jpg">
</a>
</div>
<?php $i++;}?>
<div style="clear:left;color:#222;font-family:'arial';padding-bottom:1px;border-bottom:2px solid #ffaa00;font-size:18px;padding-top:25px;margin-bottom:10px;">Best selling deals of the month</div>
<table style="font-size:13px;font-family:arial;" width="100%" cellpadding="0" cellspacing="0">
<?php foreach($activedeals as $deal){?>
<tr class="hvr">
<td style="padding:5px;"><a style="color:#122;" href="<?=site_url("sale/{$deal['category']}/{$deal['name']}/{$deal['dealid']}")?>"><?=breakstring($deal['description'],65)?></a></td>
<td align="center"><?=$deal['name']?></td>
<td align="center" style="font-size:12px;font-weight:bold;color:#f00;">Rs <?=$deal['minprice']?></td>
</tr>
<?php }foreach($inactivedeals as $deal){?>
<tr class="hvr">
<td style="padding:5px;"><a style="color:#122;" href="<?=site_url("sale/{$deal['category']}/{$deal['name']}/{$deal['dealid']}")?>"><?=substr($deal['description'],0,65)?></a></td>
<td align="center"><?=$deal['name']?></td>
<td align="center" style="font-size:12px;font-weight:bold;color:#f00;">Rs <?=rand(25,450)?>0</td>
</tr>
<?php }?>
</table>
</div>
<?php */?>
</div>
<div style="font-family:arial">
<h3 style="margin-bottom:5px;margin-top:20px;">Categories</h3>
<div style="margin-left:10px;">
<?php $i=0;foreach($deals as $cat=>$deal){?>
<div class="dealcontainer" id="dealcontainer<?=$i?>">
<div class="boxgrid2 caption" style="vertical-align:center;">
<img src="<?=base_url()?>images/items/<?=$deal[0]['pic']?>.jpg" style="min-height:160px;vertical-align:center;">
<div class="cover boxcaption">
		<div class="head"><?=$cat?></div>
	</div>
</div>
<script>
$("#dealcontainer<?=$i?>").data("catid","<?=$cat?>");
</script>
</div>
<?php $i++;}?>
</div>
</div>
<div style="clear:both;font-family:arial;padding-top:10px;">
<h3 style="margin-bottom:0px;">Brands</h3>
<div style="margin-left:10px;">
<?php $i=0;foreach($brands as $brandname=>$brand){?>
<a href="<?=site_url("brand/{$brandname}")?>"><img src="<?=base_url()?>images/brands/<?=$brand?>.jpg"></a>
<?php $i++;}?>
</div>
</div>
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