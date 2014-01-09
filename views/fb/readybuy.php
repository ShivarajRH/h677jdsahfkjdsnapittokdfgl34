<script type="text/javascript">
<!--
$(function(){
	$(".deak").click(function(){
		location="<?=site_url("saleitem")?>/"+$(this).attr("data-dealcode");
	});
	$(".catsidebar").hover(function(){
		$(this).css("background","#B0C436");
		$("a",$(this)).css("color","#fff");
	},function(){
		$(this).css("background","#fff");
		$("a",$(this)).css("color","blue");
	});
});
//-->
</script>

<style>
.catlink{
color:blue;
font-weight:bold;
text-decoration:none;
font-size:14px;
font-family:Arial,sans-serif;
}
.catlink:hover{
color:#ff9900;
}
.deak{
cursor:pointer;
}
.subcat{
 color:blue;font-weight:normal;text-decoration:none;font-size:12px; 
font-family:Arial,sans-serif;
 }
 .subcat:hover{
color:#ff9900;
text-decoration:underline;
}
#content{margin:0px;}
</style>
<div style="clear:both;padding:0px 0px;padding-top:10px;">
<style>
td{text-align:center;}
</style>
<div style="float:left;width:765px;padding-left:10px;font-family:trebuchet ms;">
<!--[if IE]>
<style>
.catimglink img{
height:130px;
}
</style>
<!endif]-->
<table cellpadding="2" cellspacing="" border="0" width="100%">
<tr>
<?php $c=0;foreach($deals as $deal){?>
<td width="25%" height="100%">
<table height="100%" style="padding:5px;border:1px solid #aaa;" width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td align="center">
<a class="catimglink" href="<?=site_url("deal/".$deal['url'])?>">
<img style="max-height:140px;max-width:120px;" src="<?=base_url()?>images/items/<?=$deal['pic']?>.jpg">
</a>
</td>
</tr>
<tr><td style="padding-top:5px;font-size:14px;"><a href="<?=site_url("deal/".$deal['url'])?>" style="font-weight:bold;color:black;text-decoration:none;"><?=$deal['name']?></a></td></tr>
<tr><td><span style=""><img src="<?=base_url()?>images/rs_small.png" title="Rs"></span> <b style="color:#E80021;"><?=$deal['price']?></b> <span style="text-decoration:line-through;"><?=$deal['orgprice']?></span></td></tr>
<tr><td style="font-size:12px;"><b style="color:#f60"><?=ceil(($deal['orgprice']-$deal['price'])/$deal['orgprice']*100)?>%</b> OFF</td></tr>
</table>
</td>
<?php if($c==3){ echo '</tr><tr>'; $c=0;}else $c++;}?>
</tr>
</table>
<?php 
/*$c=0; foreach($deals as $cat=>$cd){?>
<div class="catdeal" style="margin:5px;float:left;<?php if($c>=3) echo "display:;";?>font-family:arial;font-size:13px;border:1px solid #aaa;-moz-border-radius:5px;margin-top:5px;margin-bottom:20px;">
<div style="color:brown;margin-left:10px;padding:0px 5px;margin-top:-10px;background:#fff;float:left;font-size:15px;font-weight:bold;" align="left">
<a href="<?=site_url("category/".$cat)?>" style="color:brown;text-decoration:none;"><?=$cat?></a>
</div>
<table style="clear:left;margin-top:10px;" cellpadding="0" cellspacing="0" width="100%">
<tr>
<?php foreach($cd as $i=>$deal){if($i>3) break;?>
<td width="25%">
<table width="100%">
<tr>
<?php if($c<3) {?>
<td><div align="center" style="max-height:200px;max-width:200px;overflow:hidden;">
<a class="catimglink" href="<?=site_url("deal/".$deal['url'])?>">
<img style="max-height:130px;" src="<?=base_url()?>images/items/<?=$deal['pic']?>.jpg">
</a>
</div>
</td>
<?php }?>
</tr>
<tr><td style="padding-top:5px;"><a href="<?=site_url("deal/".$deal['url'])?>" style="font-weight:bold;color:black;text-decoration:none;"><?=$deal['name']?></a></td></tr>
<tr><td><span style="">Rs</span> <b style="color:#E80021;"><?=$deal['price']?></b> <span style="text-decoration:line-through;"><?=$deal['orgprice']?></span></td></tr>
<tr><td style="font-size:12px;"><b style="color:#f60"><?=ceil(($deal['orgprice']-$deal['price'])/$deal['orgprice']*100)?>%</b> OFF</td></tr>
</table>
</td>
<?php }?>
</tr>
<tr style="">
<?php foreach($cd as $i=>$deal){if($i>3) break;?>
<td style="padding:5px;background:#eee;<?php if($i==0) echo "-moz-border-radius-bottomleft:5px;"; elseif($i==2) echo "-moz-border-radius-bottomright:5px;";?>;">
<a style="color:blue;" href="<?=site_url("brand/".$deal['brand'])?>"><?=$deal['brand']?></a>
</td>
<?php }?>
</tr>
</table>
</div>
<?php $c++;}?>
<?php if(count($deals)>3){?>
<div align="right">
<a href="javascript:void(0)" onclick='$(".catdeal").show("slow");$(this).hide();' style="font-size:12px;font-family:arial;font-weight:bold">view all</a>
</div>
<?php }*/?>
</div>
<!--<div style="font-family:arial;margin-top:0px;float:left;max-width:150px;width:150px;margin-left:5px;padding-left:5px;">-->
<!--<h3 style="color:#98002e;text-align:left;margin:0px;margin-top:10px;">Brands</h3>-->
<!--</div>-->
<div id="branddealcont" style="border-left:0px dashed #ccc;font-family:arial;margin-top:0px;float:right;max-width:160px;width:160px;margin-left:0px;padding-left:5px;">
<?php $c=0; foreach($brands as $name=>$brand){
	if($c>4) break;
?>
<div class="branddeal" style="<?php if($c>4) echo "display:none;"?>clear:left;margin-bottom:0px;" align="left">
<div class="head" align="left">
<a href="<?=site_url("brand/$name")?>" style="text-decoration:none"><?=$name?></a>
</div>
<?php foreach($brand as $deal){?>
<div style="padding:5px;clear:both;border-bottom:1px solid #eee;font-size:12px;">
<div style="float:left;height:50px;width:35px;overflow:hidden;margin-right:3px;">
<a href="<?=site_url("deal/".$deal['url'])?>"><img src="<?=base_url()?>images/items/thumbs/<?=$deal['pic']?>.jpg" style="max-height:50px;"></a>
</div>
<a href="<?=site_url("deal/".$deal['url'])?>" style="color:black;text-decoration:none;font-size:11px;font-family:sans-serif;"><?=$deal['name']?></a>
<div style="padding-top:5px;"><img src="<?=base_url()?>images/rs_small.png" title="Rs"> <b style="color:#E80021"><?=$deal['price']?></b> <span style="text-decoration: line-through"><?=$deal['orgprice']?></span></div>
<div style="clear:left;font-size:1px;">&nbsp;</div>
</div>
<?php }?>
</div>
<?php $c++;}?>
</div>
<?php /* if(count($brands)>5){?>
<div align="right" style="clear:both">
<a href="javascript:void(0)" onclick='$("#branddealcont").css("max-height","inherit").css("overflow","visible");$(".branddeal").show("slow");$(this).hide();' style="font-size:12px;font-family:arial;font-weight:bold">view all</a>
</div>
<?php }*/?>
</div>