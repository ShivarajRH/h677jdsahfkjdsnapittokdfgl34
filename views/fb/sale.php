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
		layout: '<div style="float:left;padding-right:5px;"><b>{dn}</b> days</div><div><b>{hnn}</b> hrs</div><div style="padding-top:5px;"><div style="padding-right:5px;clear:left;float:left;"><b>{mnn}</b> mins</div><div><b>{snn}</b> secs</div></div>',
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
font-size:16px;
}
#countdown div b{
color:#e06823;
font-size:18px;
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
<div style="margin-top:5px;">
<!--<?=$dealdetails[0]['brandname']?> Sale-->
<div style="display:none;float:right;font-size:13px;" align="right">
<div style="padding-bottom:5px;">In <a href="<?=site_url("category/$category")?>" style="font-size:17px;text-decoration:none;color:#7d332a;"><?=$category?></a></div>
<?php if($dealstatus=="active") echo "ends on ".date("d/m",$dealdetails[0]['enddate']); else if($dealstatus=="inactive") echo "starts on ".date("d/m",$dealdetails[0]['startdate']); else echo "Expired";?>
</div>
</div>
<div align="left" style="padding-top:10px;">
<?php 
if($dealstatus=="expireds")
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
<div>
<div style="float:right;margin-top:20px;">
<div style="float:right;margin-top:5px;font-family:arial;height:60px;width:280px;padding:10px;padding-top:20px;font-size:20px;color:#555;background:#f8f4f1 url(<?=base_url()?>images/sale.png) center right no-repeat;">
<?php if($dealstatus!="inactive"){?>
<?php if($prices['min']!=$prices['max']){?>
<b>Rs <span style="color:#ff9900;"><?=$prices['min']?></span> to Rs <span style="color:#ff9900;"><?=$prices['max']?></span></b>
<?php }else{?>
<b>Rs <span style="color:#ff9900;"><?=$prices['min']?></span></b>
<?php }?>
<?php /*?>
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
<?php */?>
<div style="padding-top:5px;">at <span style="color:#ff9900;"><?=ceil(($prices['minorg']-$prices['min'])/$prices['minorg']*100)?>%</span> Discount</div>
<!--<div style="padding-top:5px;">Items for sale : <b style="color:#ff9900"><?=count($dealdetails)?></b></div>-->
<?php }else{?>
<div style="font-size:14px;">This sale is not yet active
<div style="padding-top:5px;">Starts in <b><?=date("d/m",$dealdetails[0]['startdate'])?></b></div>
</div>
<?php }?>
</div>
<div style="clear:right;float:right;margin-top:10px;margin-left:0px;width:300px;height:98px;background:url(<?=base_url()?>images/time<?php if($dealstatus=="inactive") echo "2"?>.png)">
<?php if($dealdetails[0]['enddate']>time()){?>
<div id="countdown" style="padding-top:35px;padding-left:30px;font-family:arial;"></div>
<?php }else {?>
<div style="padding-top:50px;color:red;font-family:arial;padding-left:30px;font-weight:bold;text-transform: uppercase;">Deal Expired</div>
<?php }?>
</div>
</div>
<div style="padding:0px;font-family:verdana;font-size:12px;">
<div style="color:#8D201E;font-size:25px;font-weight:bold;font-family:'trebuchet ms';padding-bottom:20px;text-transform:uppercase;"><?=$dealdetails[0]['brandname']?></div>
<div style="padding:0px 20px;">
<a href="<?=site_url("brand/{$dealdetails[0]['brandname']}")?>"><img style="float:left;margin-bottom:10px;margin-right:10px;" src="<?=base_url()?>images/brands/<?=$dealdetails[0]['brandlogoid']?>.jpg"></a>
<?php if($dealstatus=="active"){?>
<div style="margin-top:-20px;margin-left:80px;float:left;background:#f8f4f1;font-family:'trebuchet ms';padding:10px;font-size:13px;">
<div style="padding:3px 0px;">Started on <b><?=date("d/m",$dealdetails[0]['startdate'])?></b></div>
<div>Ends on <b style="color:#e06823;font-size:14px;"><?=date("ga d/m",$dealdetails[0]['enddate'])?></b></div>
</div>
<?php }?>
<div style="clear:left;padding-left:10px;padding-top:5px;"><?=$dealdetails[0]['description']?></div>
</div>
<div style="text-transform:uppercase;color:#8D201E;margin-top:20px;font-size:13px;font-weight:bold;"><?=$dealdetails[0]['tagline']?></div>
<!--<div style="padding-top:15px;"><a href="#items" class="link1" style="color:#00f;">View items for sale</a></div>-->
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
<!--<img src="<?=base_url()?>images/share.png">-->
<?php if($dealstatus!="inactive"){?>
<div style="clear:both;padding-top:10px;font-family:'trebuchet ms';font-size:20px;color:#8D201E;">Items for sale</div>
<a name="items"></a>
<table cellpadding="0" cellspacing="10" width="100%">
<tr>
<?php
$i=1; 
foreach($dealdetails as $item)
{
?>
<td valign="top" width="50%"> 
<div class="itemcontnr">
<?php if(isset($preview)){?>
<a href="<?=site_url("previewitem/".$this->uri->segment(2)."/{$item['id']}")?>"><img src="<?=base_url()?>images/items/<?=$item['pic']?>.jpg" style="border:1px solid #DFD7D1;margin-right:10px;vertical-align:center;float:left;width:180px;max-height:250px;"></a>
<?php }else{?>
<a href="<?=site_url("saleitem/$category/{$dealdetails[0]['brandname']}/{$item['itemname']}/{$item['id']}")?>"><img src="<?=base_url()?>images/items/<?=$item['pic']?>.jpg" style="border:1px solid #DFD7D1;margin-right:10px;vertical-align:center;float:left;width:180px;max-height:250px;"></a>
<?php }?>
<div style="float:left;padding:5px;font-family:arial;padding-top:10px;">
<div style="font-family:'trebuchet ms';color:#8D201E;font-size:<?php if(strlen($item['itemname'])>20) echo ceil(20-(strlen($item['itemname'])/20)); else echo "20";?>px;"><?=$item['itemname']?></div>
<div style="padding-left:20px;float:left;">
<div style="padding-top:10px;">Rs <b style="color:#ff9900;font-size:20px;"><?=$item['price']?></b> <span style="color:#777;text-decoration:line-through;"><?=$item['orgprice']?></span></div>
<div style="padding-top:10px;">at <b style="font-size:19px;color:#ff9900;"><?=ceil(($item['orgprice']-$item['price'])/$item['orgprice']*100)?>%</b> discount</div>
</div>
<div style="clear:left;width:200px;float:left;padding-top:10px;">
<?php if($item['enddate']>time()){?>
<?php if($item['dealtype']==1){?>
<div style="margin-top:10px;float:right;width:90px;height:32px;background:#fafaf0;border:0px solid #ff9900;border-width:0px 1px 1px 0px;">
<div style="float:left;height:32px;max-width:90px;width:<?=floor($item['available']/$item['quantity']*90+1)?>px;background:#ffdd00;"></div>
<img src="<?=base_url()?>images/tip2.png" style="margin-top:-32px;left:0px;">
</div>
<?php if($item['available']<$item['quantity']){?>
<div style="padding-top:10px;font-size:11px;"><span style="color:blue;font-weight:bold;font-size:15px;"><?=($item['quantity']-$item['available'])?></span> more needed<br>to get the deal</div>
<?php }else{?>
<div style="color:red;padding-top:10px;">SOLD OUT</div>
<?php }}}else{?>
<h5 style="margin:0px;Color:red">EXPIRED</h5>
<?php }?>
</div>
<div style="clear:both;padding-top:20px;" align="right">
<?php if(isset($preview)){?>
<a class="viewdeal" href="<?=site_url("previewitem/".$this->uri->segment(2)."/{$item['id']}")?>">preview</a>
<?php }else{?>
<a class="viewdeal" href="<?=site_url("saleitem/$category/{$dealdetails[0]['brandname']}/{$item['itemname']}/{$item['id']}")?>">view deal</a>
<?php }?>
</div>
</div>
<br style="clear:both;">
</div>
</td>
<?php if($i%2==0) echo "</tr><tr>"; $i++; }
if($i==2)
 echo "<td width='50%'></td>";
?>
</tr>
</table>
<?php }?>
<br style="clear:both;">
</div>
<?php }?>
<?php if(!isset($preview)){?>
<div class="share" style="clear:left;float:left;margin-top:20px;">
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
<?php }?>

</div>