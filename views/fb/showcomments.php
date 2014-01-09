<?php 
if(isset($itemdetails['enddate']))
$ed=$itemdetails['enddate'];
?>
<script type="text/javascript" src="<?=base_url()?>js/countdown.js"></script>
<script>
$(function(){
	ed=new Date(<?=date("Y,",$ed)?><?=(date("n",$ed)-1)?>,<?=date("j,G,",$ed)?><?=(date("i",$ed)+1-1)?>,<?=(date("s",$ed)+1-1)?>);
	$('#countdown').countdown({
		layout: '<div style="float:left;padding-right:5px;"><b>{dn}</b> days</div><div><b>{hnn}</b> hrs</div><div style="padding-top:5px;"><div style="padding-right:5px;clear:left;float:left;"><b>{mnn}</b> mins</div><div><b>{snn}</b> secs</div></div>',
		until: ed});
	$("a.fanblink").fancybox({
		'zoomOpacity'			: true,
		'zoomSpeedIn'			: 300,
		'zoomSpeedOut'			: 200,
		'callbackOnStart'			: resetHeight
	});
	$("a.vlink").click(function(){
		id=$(this).attr("vidid");
		$("div#video").html('<object width="560" height="340"><param name="movie" value="http://www.youtube.com/v/'+id+'&hl=en_US&fs=1&"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/'+id+'&hl=en_US&fs=1&" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="560" height="340"></embed></object>');
	});
	$("a.vlink,a#infolink").fancybox({
		'zoomOpacity'			: true,
		'zoomSpeedIn'			: 300,
		'zoomSpeedOut'			: 200,
		'loadOnClose'			: resetHeight
	});
});
function addtocart()
{
	item="<?=$itemdetails['id']?>";
	qty=$("#qty").val();
	$(this).attr("disabled",true);
	pd="item="+item+"&qty="+qty;
	$.post("<?=site_url('jx/addtocart')?>",pd,function(resp){
		$(this).attr("disabled",false);
		if(resp==0)
			$("#info").html("This sale is not valid anymore!");
		else if(resp==1)
			$("#info").html("Please reduce your quantity. Items are not available for given quantity");
		else if(resp==2)
			$("#info").html("This item is already present in your cart. If you want to edit it, please remove it first.");
		else if(resp==3)
			$("#info").html("This item added to cart!");
		else
			$("#info").html("Unknown error occured. Please try again. Sorry for this inconvenience."); 	
		if(resp==3)
			$("a#cartlink").click();
		else
		{
			$("#fancy_inner").css("height","30%");			
			$("a#infolink").click();
		}
	});
}
</script>
<style>
div#fancy_div{
background:#fff;
color:#000;
}
/*
#countdown{
margin-left:5px;
background:#fff url(<?=base_url()?>images/countdown.png) no-repeat;
height:58px;
width:307px;
}
#countdown div{
float:left;
padding-top:27px;
}*/
#countdown{
font-size:16px;
}
#countdown div b{
color:#e06823;
font-size:18px;
}
 .headingtext{
 background:url(<?=base_url()?>images/fade.png) repeat-x;
 height:46px;
 color:#222;
 font-weight:normal;
 padding:8px 0px 0px 15px;
 }

</style>
<div class="headingtext" style="clear:both;margin:10px 0px">
<span style="font-size:18px;">Comments for</span> 
<!--<a href="<?=site_url("saleitem/{$itemdetails['category']}/{$itemdetails['brandname']}/{$itemdetails['name']}/{$itemdetails['id']}")?>" style="color:#222;font-size:20px;"><?=$itemdetails['name']?></a>-->
<a href="<?=site_url("deal/{$itemdetails['url']}")?>" style="color:#222;font-size:20px;"><?=$itemdetails['name']?></a>
</div>
<div align="left" style="padding:0px;">
<div id="infoc" style="display:none">
<div style="font-size:15px;font-family:'trebuchet ms'">
<div style="color:#ff9900;font-size:25px;font-weight:bold;padding-bottom:7px;">Info</div>
<div id="info"></div>
</div>
</div>
<div style="float:right">
<?php if(!isset($itemexpired)){?>
<div style="font-family:arial;background:#F7F3F0;width:300px;padding:10px;color:#555;">
<b style="font-size:22px;">Rs <?=$itemdetails['price']?></b>
<div style="margin-left:2px;padding-top:5px;height:45px;float:left;width:100%;">
<div style="float:left;width:33%;margin-top:4px;margin-left:-5px;border-right:1px solid #FF9900" align="center">
<div style="color:#bbb;">Value</div>
<span style="text-decoration:line-through;"><?=$itemdetails['orgprice']?></span>
</div>
<div style="float:left;width:33%;margin-top:4px;border-right:1px solid #FF9900" align="center">
<div style="color:#bbb;">Discount</div>
<span style="font-weight:bold;"><?=ceil(($itemdetails['orgprice']-$itemdetails['price'])/$itemdetails['orgprice']*100)?>%</span>
</div>
<div style="float:left;width:33%;margin-top:4px;" align="center">
<div style="color:#bbb;">Save</div>
<span style="font-weight:bold;color:#ffaa00;"><?=ceil($itemdetails['orgprice']-$itemdetails['price'])?></span>
</div>
</div>
<div style="padding-top:10px;clear:both;">
<div style="float:left;padding-left:20px;padding-top:2px;vertical-align:middle">Quantity : 
<select id="qty">
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
</select></div>
<a href="javascript:void(0)" style="margin-left:15px;" onclick="addtocart()"><img src="<?=base_url()?>images/addtocart.png"></a>
</div>
</div>
<div style="margin-top:10px;width:300px;height:98px;background:url(<?=base_url()?>images/time.png)">
<div id="countdown" style="padding-top:35px;padding-left:30px;font-size:13px;font-family:arial;"></div>
</div>
<?php }else{?>
<div style="font-family:arial;background:#F7F3F0;width:300px;padding:10px;color:#555;">
Item not available! <br>Deal ended on <b><?=date("ga d/m",$itemdetails['enddate'])?></b>
</div>
<?php }?>
</div>
<div style="float:left;padding:0px 5px;">
<!--<div style="font-family:'trebuchet ms';font-size:20px;font-weight:bold;margin-bottom:10px;">Comments</div>-->
<?php foreach($comments as $comment){?>
<a name="<?=$comment['id']?>" onfocus='alert("asdasd")' onclick='alert(<?=$comment['id']?>)'></a>
<div style="padding:5px;margin-right:0px;width:590px;">
<div style="float:left;margin-top:5px;"><img src="<?=base_url()?>images/user.png"></div>
<div style="min-height:50px;font-size:13px;font-family:arial;padding:5px;padding-left:35px;margin-left:55px;background:#f7f3f0 url(<?=base_url()?>images/comment_bubble.gif) no-repeat;">
<div style="font-size:11px;"><b style="padding-right:10px;"><?php if($comment['special']!=0){?><img src="<?=base_url()?>images/special<?=$comment['special']?>.png"><?php }?> <?=$comment['name']?> </b> commented <?=$comment['time']?> ago</div>
<div style="padding:5px;"><?=$comment['comment']?></div>
</div>
</div>
<?php }?>
<div <?php if(count($comments)!=0){?>style="padding-top:20px;"<?php }?>><a href="javascript:void(0)" onclick='$("#writecomment").show()' style="text-decoration:none;font-family:'trebuchet ms';font-size:20px;font-weight:bold;color:#00f;"><?php if(count($comments)==0){?>Be the first to comment!<?php }else{?>Write a comment!<?php }?></a></div>
<div id="writecomment" <?php if(count($comments)!=0){?>style="display:none"<?php }?>>
<div style="padding:5px;margin-right:50px;">
<div style="float:left;margin-top:5px;"><img src="<?=base_url()?>images/user.png"></div>
<div style="font-size:13px;font-family:arial;padding:5px;padding-left:35px;margin-left:55px;background:#f7f3f0 url(<?=base_url()?>images/comment_bubble.gif) no-repeat;">
<div style="font-size:11px;"><b style="padding-right:10px;"><?=$user['name']?> </b>write your comment below</div>
<div style="padding:5px;">
<form method="post" style="margin:0px;padding:0px;">
<textarea name="comdata" style="width:98%;height:80px;"></textarea>
<div align="right" style="margin-top:5px;"><input type="submit" value="submit"></div>
</form>
</div>
</div>
</div>
</div>

</div>
</div>