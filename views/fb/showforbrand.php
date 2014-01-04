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

$(".dealcontainer").click(function(){
	location="<?=site_url("sale")?>/"+$(this).attr("deal");
});

});
</script>

<style>
a{color:blue;}
.itemcontnr{
margin-right:10px;
margin-bottom:10px;
}
</style>
<div style="font-family:trebuchet ms;font-weight:bold;font-size:17px;padding-top:0px;clear:both;" align="left">
<?php if($brand['logoid']==NULL){?>
<?=$brand['name']?>
<?php }else{?>
<img src="<?=base_url()?>images/brands/<?=$brand['logoid']?>.jpg">
<?php }?>
</div>
<div align="left" style="color:blue;font-size:13px;padding:5px;padding-bottom:7px;font-family:arial;">Found <b><?=count($activedeals)?></b> deals</div> 
<div style="font-size:13px;font-family:arial;padding:10px;" align="left">
<div style="padding:0px 5px;"><?=$brand['description']?></div>
<?php if(strlen($brand['website'])>4){?>
<div style="font-size:11px;padding-top:5px;"><a href="<?=$brand['website']?>">website</a></div>
<?php }?>
</div>
<div align="left" style="padding-top:0px;">
<table cellpadding="0" cellspacing="10" width="100%">
<tr>
<?php
$i=1; 
foreach($items as $item)
{
?>
<td valign="top" width="50%"> 
<div class="itemcontnr">
<div style="float:left;width:180px;text-align:center;margin-right:10px;background:#fff;">
<?php if(isset($preview)){?>
<a href="<?=site_url("previewitem/".$this->uri->segment(2)."/{$item['id']}")?>"><img src="<?=base_url()?>images/items/<?=$item['pic']?>.jpg" style="border:1px solid #DFD7D1;margin-right:10px;vertical-align:center;max-width:180px;max-height:250px;"></a>
<?php }else{?>
<a href="<?=site_url("deal/{$item['url']}")?>"><img src="<?=base_url()?>images/items/<?=$item['pic']?>.jpg" style="border:1px solid #DFD7D1;margin-right:10px;vertical-align:center;max-width:180px;max-height:250px;"></a>
<?php }?>
</div>
<div style="padding:5px;font-family:arial;padding-top:10px;">
<div style="font-family:'trebuchet ms';color:#8D201E;font-size:<?php if(strlen($item['itemname'])>20){ if(ceil(20-(strlen($item['itemname'])/5))>9) echo ceil(20-(strlen($item['itemname'])/5)); else echo "9";} else echo "20";?>px;"><?=$item['itemname']?></div>
<div style="padding-left:20px;float:left;">
<div style="padding-top:5px;"><img src="<?=base_url()?>images/rs_small.png" title="Rs"> <b style="color:#ff9900;font-size:20px;"><?=$item['price']?></b> <span style="color:#777;text-decoration:line-through;"><?=$item['orgprice']?></span></div>
<div style="padding-top:5px;font-size:12px;">at <b style="font-size:13px;color:#ff9900;"><?=ceil(($item['orgprice']-$item['price'])/$item['orgprice']*100)?>%</b> discount</div>
<div style="margin-left:-15px;padding-top:10px;font-weight:bold;font-size:12px;"><?=$item['category']?></div>
</div>
<div style="float:left;padding-top:0px;">
<?php if($item['enddate']>time()){?>
<?php if($item['dealtype']==1){?>
<div style="margin-top:10px;float:right;width:90px;height:32px;background:#fafaf0;border:0px solid #ff9900;border-width:0px 1px 1px 0px;">
<div style="float:left;height:32px;max-width:90px;width:<?=floor($item['available']/$item['quantity']*90+1)?>px;background:#ffdd00;"></div>
<img src="<?=base_url()?>images/tip2.png" style="margin-top:-32px;left:0px;">
</div>
<?php  if($item['available']<$item['quantity']){?>
<div style="float:left;padding-top:10px;font-size:11px;"><span style="color:blue;font-weight:bold;font-size:15px;"><?=($item['quantity']-$item['available'])?></span> more needed<br>to get the deal</div>
<?php }else{?>
<div style="float:left;color:red;padding-top:10px;">SOLD OUT</div>
<?php }}}else{?>
<h5 style="float:left;margin:0px;Color:red">EXPIRED</h5>
<?php }?>
</div>
<div style="clear:left;float:left;padding-top:10px;" align="right">
<div style="float:right">
<?php if(isset($preview)){?>
<a class="viewdeal" href="<?=site_url("previewitem/".$this->uri->segment(2)."/{$item['id']}")?>">preview</a>
<?php }else{?>
<a class="viewdeal" href="<?=site_url("deal/{$item['url']}")?>">view deal</a>
<?php }?>
</div>
</div>
<div style="font-size:1px;clear:both">&nbsp;</div>
</div>
</div>
</td>
<?php if($i%2==0) echo "</tr><tr>"; $i++; }
if($i==2)
 echo "<td width='50%'></td>";
?>
</tr>
</table>
<?php /*?>
<table>
<tr>
<?php
$i=0; 
foreach($activedeals as $deal)
{
?>
<td width="460">
<div class="itemcontnr">
<a href="<?=site_url("sale/{$deal['categoryname']}/{$deal['name']}/{$deal['dealid']}")?>"><img src="<?=base_url()?>images/items/<?=$deal['pic']?>.jpg" style="border:1px solid #DFD7D1;margin-right:10px;vertical-align:center;float:left;width:180px;max-height:250px;"></a>
<div style="padding:5px;font-family:arial;padding-top:10px;">
<div style="font-family:'trebuchet ms';height:85px;color:#8D201E;font-size:17px;"><?=$deal['tagline']?></div>
<div style="padding-left:20px;float:right;font-size:13px;">
<div style="padding-top:10px;">Ends <b style="color:#ff9900;font-size:15px;"><?=date("ga d/m",$deal['enddate'])?></b></div>
</div>
<div style="clear:right;float:right;padding-top:20px;" align="right">
<a class="viewdeal" href="<?=site_url("sale/{$deal['categoryname']}/{$deal['name']}/{$deal['dealid']}")?>">
<!--<img src="<?=base_url()?>images/buynow.png">-->
view deal
</a></div>
</div>
<br style="clear:both;">
</div>
</td>
<?php $i++; if($i%2==0) echo "</tr><tr>";
}?>
<?php
foreach($inactivedeals as $deal)
{
?>
<td width="460">
<div class="itemcontnr">
<a href="<?=site_url("sale/{$deal['categoryname']}/{$deal['name']}/{$deal['dealid']}")?>"><img src="<?=base_url()?>images/items/<?=$deal['pic']?>.jpg" style="border:1px solid #DFD7D1;margin-right:10px;vertical-align:center;float:left;width:180px;max-height:250px;"></a>
<div style="padding:5px;font-family:arial;padding-top:10px;">
<div style="font-family:'trebuchet ms';color:#8D201E;font-size:20px;height:85px;"><?=$deal['tagline']?></div>
<div style="padding-left:20px;float:right;font-size:13px;">
<div style="padding-top:10px;">Starts at <b style="color:#ff9900;font-size:15px;"><?=date("ga d/m",$deal['startdate'])?></b></div>
</div>
</div>
<br style="clear:both;">
</div>
</td>
<?php $i++; if($i%2==0) echo "</tr><tr>";}?>
</tr>
</table>
*/?>
<?php 
if(!isset($activedeals[0]) && !isset($inactivedeals[0]))
{
?>
<div align="center" style="padding:5% 0px;">Currently, no deals available in this category. Please check back later!</div>
<?php }?>
</div>