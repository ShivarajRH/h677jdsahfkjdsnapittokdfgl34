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
<?php /*?>
<div style="float:left;padding:5px;margin-top:20px;padding-left:10px;font-family:trebuchet ms;">
<?php $i=0; foreach($activedeals as $deal){?>
<div class="deak" data-dealcode="<?=$deal['itemid']?>" style="margin-bottom:10px;padding:3px;-moz-border-radius:3px;border:1px solid #C2C6C5;height:160px;<?php if($i==0) echo 'width:600px;'; else echo 'margin-left:10px;float:left;width:290px;';?><?php if($i%2==1) echo 'margin-left:0px;clear:left;';?>">
<table cellpadding="0" cellspacing="0" style="width:100%;height:100%;background:url(<?=base_url()?>images/dealbg.gif) repeat-x top;">
<tr>
<td align="left" style="padding:5px;padding-top:15px;width:155px;max-width:155px;overflow:hidden;">
<?php if($i==0){?>
<div style="height:85px;max-height:75px;overflow:hidden;"><img src="<?=base_url()?>images/brands/<?=$deal['brandlogoid']?>.jpg"></div>
<?php }else{?>
<div style="text-transform:uppercase;"><?=$deal['itemname']?></div>
<?php }?>
<div style="padding-left:30px;color:#98002e;font-weight:bold;padding-top:0px;">
<div style="font-size:25px;"><?=ceil(($deal['orgprice']-$deal['price'])/$deal['orgprice']*100)?>%</div>
<div style="font-size:18px;">OFF</div>
</div>
</td>
<?php if($i==0){?>
<td align="left" valign="top" style="padding-top:30px;">
<div style="text-transform:uppercase;"><?=$deal['itemname']?></div>
<div style="font-size:15px;font-weight:bold;padding-top:10px;">Rs <span style="color:#98002e;"><?=$deal['price']?></span></div>
</td>
<?php }?>
<td align="center" valign="middle" style="padding-top:10px;padding-right:10px;<?php if($i==0){?>width:200px<?php }?>;max-width:200px;overflow:hidden;">
<div style="max-height:140px;overflow:hidden;">
<a href="<?=site_url("saleitem/".$deal['itemid'])?>">
<img src="<?=base_url()?>images/items/<?=$deal['pic']?>.jpg" style="<?php if($i==0){?>max-height:140px;<?php }else{?>max-width:160px;<?php }?>">
</a>
</div>
</td>
</tr>
</table>
</div>
<?php $i++;}?>
</div>
*/?>
<style>
td{text-align:center;}
</style>
<!--[if IE]>
<style>
.catimglink img{
height:130px;
}
</style>
<!endif]-->
<style>
td.dealtab{
border:1px solid #eee;
}
</style>
<table class="dealtab" style="clear:left;margin-top:10px;" cellpadding="0" cellspacing="0" width="100%">
<tr>
<?php $tr=0;$c=0; foreach($deals as $cat=>$cd){?>
<?php foreach($cd as $i=>$deal){?>
<td width="20%" class="dealtab">
<table width="100%" height="100%"  clicktoshowdialog="my_dialog">
<tr>
<td><div align="center" style="max-height:200px;max-width:200px;overflow:hidden;">
<a class="catimglink" href="<?=site_url("fb/deal/".$deal['url'])?>">
<img style="max-height:130px;" src="<?=base_url()?>images/items/thumbs/<?=$deal['pic']?>.jpg">
</a>
</div>
</td>
</tr>
<tr><td style="padding-top:5px;"><a href="<?=site_url("fb/deal/".$deal['url'])?>" style="font-weight:bold;color:black;text-decoration:none;"><?=$deal['name']?></a></td></tr>
<tr><td><span style=""><img src="<?=base_url()?>images/rs_small.png"></span> <b style="color:#E80021;"><?=$deal['price']?></b> <span style="text-decoration:line-through;"><?=$deal['orgprice']?></span></td></tr>
<tr><td style="font-size:12px;"><b style="color:#f60"><?=ceil(($deal['orgprice']-$deal['price'])/$deal['orgprice']*100)?>%</b> OFF</td></tr>
</table>
</td>
<?php $c++; if($c>4) { $c=0; echo '</tr><tr>'; $tr++;if($tr>4) break;}}
if($tr>4) break;}?>
</tr>
</table>
<!--<div style="font-family:arial;margin-top:0px;float:left;max-width:150px;width:150px;margin-left:5px;padding-left:5px;">-->
<!--<h3 style="color:#98002e;text-align:left;margin:0px;margin-top:10px;">Brands</h3>-->
<!--</div>-->
<?php /* if(count($brands)>5){?>
<div align="right" style="clear:both">
<a href="javascript:void(0)" onclick='$("#branddealcont").css("max-height","inherit").css("overflow","visible");$(".branddeal").show("slow");$(this).hide();' style="font-size:12px;font-family:arial;font-weight:bold">view all</a>
</div>
<?php }*/?>
</div>