<html>
<head><title>ViaBazaar Widget</title>
<script src="<?=base_url()?>js/jquery.js"></script>
<style>
body{
margin:0px;
padding:0px;
}
.vbw_cat{
font-family:trebuchet ms;
font-size:13px;
}
h1,h2,h3,h4{
padding:0px;
margin:0px;
}
.vbw_cat td{
width:20%;
cursor:pointer;
overflow:hidden;
text-align:center;
border:1px solid #eee;
padding:5px;
background:#E31E24;
font-weight:bold;
color:#fff;
}
.catdealcontainer{
width:595px;
overflow:hidden;
height:200px;
}
img{
border:0px;
}
.viewdeal{
background:#fff url(<?=base_url()?>images/btnbg.png) repeat-x;
-moz-border-radius:5px;
font-family:arial;
font-size:13px;
font-weight:bold;
color:#fff;
text-decoration:none;
padding:5px;
}
.slideback{
float:left;cursor:pointer;position:absolute;top:150px;z-index:100;left:5px;background:#eee;-moz-border-radius:5px;padding:0px 2px;color:#e80021;font-weight:bold;margin-right:-10px;font-size:25px;
}
.slideforw{
float:left;cursor:pointer;position:absolute;top:150px;z-index:101;left:565px;background:#eee;-moz-border-radius:5px;padding:0px 2px;color:#e80021;font-weight:bold;margin-right:-10px;font-size:25px;
}
.cats{
border:1px solid #eee;
border-top:0px;
font-family:arial;
font-size:13px;
background:#fff;
padding:5px;
height:30px;
overflow:hidden;
}
.cats a{
text-decoration:none;
color:blue;
margin:5px;
}
.cats a:hover{
text-decoration:underline;
}
.clear{
font-size:1px;
clear:both;
}
</style>
</head>
<body>
<script>
var totaldeals=new Array();
var curslide=new Array();
$(function(){
//	$("#widget").hover(function(){$(".slideback,.slideforw").show();},function(){$(".slideback,.slideforw").hide();});
	$(".catdealcontainer").each(function(i2){
		totaldeals[i2]=0;
		curslide[i2]=0;
		$(".deal",$(this)).each(function(i){
		$(this).css("position","absolute");
		$(this).css("left",(595*i)+"px");
		totaldeals[i2]++;
		});
	});
	$(".vbw_cat td").click(function(){
		$(".vbw_cat td").css("background","#E31E24").css("border","1px solid #eee").css("color","#fff");
		$(this).css("background","#fff").css("border-bottom","0px").css("color","#333");
	});
});
	function catsel(i)
	{
		$(".catdealcontainer").hide();
		$("#catdeal"+i).show();
	}
	function slideback()
	{
		is=$(".catdealcontainer:not(:hidden)").attr("id");
		i=is.charAt(is.length-1);
		moveslider(i,curslide[i]-1);
	}	
	function slideforward()
	{
		is=$(".catdealcontainer:not(:hidden)").attr("id");
		i=is.charAt(is.length-1);
		moveslider(i,curslide[i]+1);
	}	
	function moveslider(i,i2)
	{
		if(i2>=totaldeals[i])
			i2=0;
		if(i2<0)
			return;
		obj=$("#"+is+" .deal");
			if(i2>curslide[i])
			obj.animate({
				left:"-="+(595*(i2-curslide[i]))+"px"
			},1000);
			else
				obj.animate({
					left:"+="+(595*(curslide[i]-i2))+"px"
				},1000);
			curslide[i]=i2;
	}
</script>
<div id="widget" style="border:4px solid #eee;font-family:trebuchet ms;width:595px;position:relative;overflow:hidden;">
<div style="border:1px solid #eee;border-bottom:0px;padding:5px;font-family:trebuchet ms;color:#e80021">
<div style="padding-top:7px;float:right;font-weight:bold;">Best deals for you!</div> 
<img src="<?=base_url()?>images/logo.png" height="30">
<div class="Clear">&nbsp;</div>
</div>
<table cellspacing="0" class="vbw_cat" width="100%">
<tr>
<?php $i=0; foreach($deals as $cat=>$deal){?>
<td onclick='catsel(<?=$i?>)' <?php if($i==0) echo 'style="background:#fff;color:#333;border-bottom:0px;"'?>><nobr><?=$cat?></nobr></td>
<?php $i++;if($i==5) break;}?>
</tr>
</table>
<div style="border:1px solid #eee;border-width:0px 1px;">
<div onclick="slideback()" class="slideback">&lt;</div>
<div onclick="slideforward()" class="slideforw">&gt;</div>
<?php $i=0;foreach($deals as $cat=>$cd){$i2=0;?>
<div class="catdealcontainer" id="catdeal<?=$i?>" <?php if($i!=0) echo "style='display:none;'"?>>
<?php foreach($cd as $deal){?>
<div class="deal" align="center" style="display:inline;height:200px;">
<table style="width:595px;height:200px;" cellpadding="5">
<tr>
<td valign="middle" align="center" style="width:150px;padding:10px;">
<div style="width:150px;position:relative;overflow:hidden;">
<a target="_blank" href="<?=site_url("deal/".$deal['url'])?>"><img src="<?=base_url()."images/items/".$deal['pic'].".jpg"?>" style="max-height:180px;max-width:150px;"></a>
</div>
</td>
<td valign="top" style="padding:5px;padding-top:20px;">
<div style="height:145px;">
<?php if($deal['logoid']!=NULL){?>
<a target="_blank" href="<?=site_url("brand/".$deal['brandname'])?>"><img src="<?=base_url()?>images/brands/<?=$deal['logoid']?>.jpg" style="margin-right:5px;float:right;max-width:100px;max-height:40px;"></a>
<?php }else{?>
<div style="float:right;margin-right:5px;font-weight:bold;text-transform:uppercase;"><?=$deal['brandname']?></div>
<?php }?>
<h3 style="font-family:arial;margin-bottom:10px;"><?=$deal['name']?></h3>
<div style="padding:10px;">Rs <b style="color:#E80021"><?=$deal['price']?></b> <span style="text-decoration:line-through"><?=$deal['orgprice']?></span></div>
<div style="padding:10px;padding-top:0px;"><b style="color:#E80021"><?=ceil(($deal['orgprice']-$deal['price'])/$deal['orgprice']*100)?>%</b> OFF</div>
<div style="padding:10px 30px;"><a href="<?=site_url("deal/".$deal['url'])?>" target="_blank" class="viewdeal">View Deal</a></div>
</div>
</td>
</tr>
</table>
</div>
<?php $i2++; }?>
</div>
<?php $i++; if($i==5) break;}?>
</div>
<div class="cats">
<?php foreach($deals as $key=>$deal){?>
<a target="_blank" href="<?=site_url("category/".$key)?>"><?=$key?></a>
<?php }?>
</div>
</div>
</body>
</html>
<?php 
/*

<?php 
$ed=$item['enddate'];
?>
<html>
<head>
<title>Localsquare Widget</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script src="http://localsquare.in/js/countdown.js"></script>
<style>
.cont{
 width:510;height:70px;font-family:helvetica;background:url(<?=base_url()?>images/widget.png);
}
#countd{
font-size:14px;
}
.price{
font-size:22px;
font-weight:bold;
color:#000;
float:right;
font-family:trebuchet ms;
margin-top:20px;
}
.price span{
font-weight:normal;
}
.smallcont{
width:220px;
overflow:hidden;
float:left;margin-top:20px;margin-left:0px;color:#fff;font-size:13px;
}
.smallhead{
min-width:45px;
float:left;font-size:10px;padding-left:10px;
}
.smallcnt{
min-width:45px;
padding-left:10px;
float:left;font-weight:bold;font-size:14px;
}
.buynow{
margin:10px;
float:right;
margin-bottom:0px;
}
img{
border:0px;
}
#hiddencont{
width:510;height:70px;padding-top:5px;
clear:both;
}
</style>
</head>
<body>
<script>
$(function(){
	ed=new Date(<?=date("Y,",$ed)?><?=(date("n",$ed)-1)?>,<?=date("j,G,",$ed)?><?=(date("i",$ed)+1-1)?>,<?=(date("s",$ed)+1-1)?>);
	$('#countd').countdown({
		layout: '<b>{dn}</b>d <b>{hnn}</b>:<b>{mnn}</b>:<b>{snn}</b>',
		until: ed});
});
</script>
<div class="cont">
<div class="buynow"<?php if($item['dealtype']==0) echo ' style="margin-top:20px;"'?>>
<a href="#" onclick='$("#hiddencont").slideDown("slow")'><img src="<?=base_url()?>images/buynow.png"></a>
<?php if($item['dealtype']==1 && $item['quantity']!=$item['available']){?>
<div align="center" style="padding-top:2px;font-size:10px;"><b><?=($item['quantity']-$item['available'])?></b> left to get the deal</div>
<?php }?>
</div>
<div class="price"><span>Rs </span><?=$item['price']?></div>
<div class="smallcont">
<div class="smallhead">Value</div>
<div class="smallhead">Discount</div>
<div class="smallhead">Time left</div>
<div style="clear:left;" class="smallcnt"><?=$item['orgprice']?></div>
<div class="smallcnt"><?=ceil(($item['orgprice']-$item['price'])/$item['orgprice']*100)?>%</div>
<div class="smallcnt" id="countd"></div>
</div>
</div>
<div id="hiddencont">
<img src="<?=base_url()?>images/widget_top.png">
<div style="clear:both"><img src="<?=base_url()?>images/widget_bottom.png"></div>
</div>
</body>
</html>
*/
