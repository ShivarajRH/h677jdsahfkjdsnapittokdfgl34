<script>
$(function(){
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
});
</script>
<style>
.itemcontnr{
margin-right:10px;
margin-bottom:10px;
}
</style>
<div class="headingtext" style="clear:both;margin-bottom: 10px;">
	Category: <?=$category?>
	<span style="font-size: 14px;font-weight: normal;color: #E31E24;text-align: right;margin-left: 20px;">(Found <b><?=count($activedeals)?></b> deals)</span>
</div>

<div align="left">
<table>
<tr>
<?php
$i=0; 
foreach($activedeals as $deal)
{
?>
<td width="460" valign="top">
<div class="itemcontnr" deal="<?=$deal['name']."/".$deal['dealid']?>">
<table width="100%">
<tr>
<td align="center" valign="center" style="background:#fff;padding:5px;width:185px;text-align:center;overflow:hidden;margin-right:5px;">
<a href="<?=site_url("deal/".$deal['url'])?>">
<img src="<?=base_url()?>images/items/<?=$deal['pic']?>.jpg" style="border:1px solid #DFD7D1;margin-right:10px;vertical-align:center;min-height:120px;max-height:250px;max-width:185px;">
</a>
</td>
<td valign="top">
<div style="padding:5px;padding-top:10px;">
<div style="min-height:55px;color:#8D201E;font-size:20px;"><?=$deal['itemname']?>
<div style="font-size:13px;color:#000;padding-top:3px;text-align: right">from <b><?=$deal['name']?></b></div>
</div>
<?php if($deal['dealtype']==1){?>
<?php if($deal['quantity']>$deal['available']){?>
<div style="float:right;font-size:12px;color:#E80021;">
<b><?=$deal['quantity']-$deal['available']?></b> more needed
</div>
<?php }else{?>
<div style="float:right;font-size:12px;color:blue;">
<b><?=$deal['available']?></b> got the deal
</div>
<?php }?>
<div style="clear:right;float:right;padding-bottom:10px;padding-top:0px;" align="center">
<div style="margin-top:5px;clear:both;width:140px;height:10px;-moz-border-radius:5px;background:#fafafa;border:0px solid #ff9900;border-width:1px 1px 1px 1px;">
<div style="margin:0px;-moz-border-radius:5px;float:left;height:10px;max-width:140px;width:<?=floor($deal['available']/$deal['quantity']*140+1)?>px;background:#ff9900;"></div>
</div>
<div style="width:150px;">
<span style="font-size:12px;margin-left:0px;clear:both;float:left">0</span>
<span style="font-size:12px;margin-right:0px;float:right"><?=$deal['quantity']?></span>
</div>
</div>
<?php }?>
<div style="clear:right;padding-left:20px;float:right;font-size:13px;width: 200px">
<div align="left" style="padding-top:10px;width: 150px;float: left">
	This deals ends on <br /><br /><b style="color:#ff9900;font-size:15px;"><?=date("l, M m ga",$deal['enddate'])?></b>
</div>
<div align="right" style="width: 50px;float: left">
<img alt="" src="<?php echo base_url() ?>images/hourglass.gif" />
</div>
</div>
<div style="clear:right;float:right;padding-top:20px;" align="right">
<a class="viewdeal" href="<?=site_url("deal/".$deal['url'])?>">
<!--<img src="<?=base_url()?>images/buynow.png">-->
view deal
</a></div>
</div>
</td>
</tr>
</table>
<div class="clear">&nbsp;</div>
</div>
</td>
<?php $i++; if($i%2==0) echo "</tr><tr>";}?>
<?php
foreach($inactivedeals as $deal)
{
?>
<td width="460">
<div class="itemcontnr" deal="<?=$deal['name']."/".$deal['dealid']?>">
<a href="<?=site_url("sale/{$category}/{$deal['name']}/{$deal['dealid']}")?>"><img src="<?=base_url()?>images/items/<?=$deal['pic']?>.jpg" style="border:1px solid #DFD7D1;margin-right:10px;vertical-align:center;float:left;width:180px;max-height:250px;"></a>
<div style="padding:5px;padding-top:10px;">
<div style="color:#8D201E;font-size:20px;height:70px;"><?=$deal['tagline']?>
<div style="color:#000;font-size:13px;padding-top:3px;">from <b><?=$deal['name']?></b></div>
</div>
<div style="padding-left:20px;font-size:14px;float:right;">
<div style="padding-top:10px;">Starts at <b style="color:#ff9900;font-size:15px;"><?=date("ga d/m",$deal['startdate'])?></b></div>
</div>
</div>
<div style="clear:both;font-size:1px;">&nbsp;</div>
</div>
</td>
<?php $i++; if($i%2==0) echo "</tr><tr>"; }?>
</tr></table>
</div>
<?php 
if(!isset($activedeals[0]) && !isset($inactivedeals[0]))
{
?>
<div align="center" style="padding:5% 0px;">Currently, no deals available in this category. Please check back later!</div>
<?php }?>
