<?php $colors=array("#024769","#9ABA0D","#00348A","#059CD1","#E9322C","#18161B","#5CB120","#FF8B12");$t=$colors;?>
<div class="container bodyparts">

<img src="<?=IMAGES_URL?>ht_<?=$this->uri->segment(2)?$this->uri->segment(2):"male"?>.png">

<div class="totalpricetrig"></div>
<div class="totalprice">
Total Amount :
<div class="price_counter">
Rs <span class="price">0</span>
</div>
<a href="javascript:void(0)" class="instant_checkout" onclick='buynowbody()'><img src="<?=IMAGES_URL?>instant_checkout.png"></a>
</div>

<h1>Head to Toe</h1>

<div class="specialcart">
<h3 style="margin-bottom:5px;">Your Special Cart</h3>
<?php
$mi=0;
 foreach($parts as $p){ ?>
<div class="cartcont pc_cont<?=$mi?>" id="pc_cont<?=$mi?>">
<div class="empty"><div style="padding:50px 20px;">EMPTY</div></div>

<div class="sc_cont sc_cont<?=$mi?>" style="display:none">
<div class="close"><img src="<?=IMAGES_URL?>close.png"></div>
<div class="cont"></div>
</div>

</div>
<?php $mi++; }?>
</div>

<div style="padding-top:10px;float:left;margin-bottom:-20px;"><h4>Select your products from each category listed below. <br>And you will pay only <b class="red">Rs <?=BODYPARTS_MALE?></b> irrespective of the total amount you get</h4></div>

<table width="800" cellpadding=5 cellspacing=0 class="windowtable">

<?php $mi=0; foreach($parts as $m=>$deals){ $c=array_pop($t);if(empty($t)) $t=$colors;?>
<tr>
<td valign="middle"><h2 style="color:<?=$c?>"><?=$m?></h2></td>
<td>
<div align="right">
<a href="javascript:void(0)" class="prev nextprev<?php if(count($deals)<5){?> nextprevdis<?php }?>">previous</a>
<a href="javascript:void(0)" class="next nextprev<?php if(count($deals)<5){?> nextprevdis<?php }?>">next</a>
</div>
<div class="window">
<div class="cont" id="cont<?=$mi?>">
<?php foreach ($deals as $i=>$d){?>
<div class="wind_deal" style="left:<?=$i*135?>px;">
<input type="hidden" class="itemid" value="<?=$d['id']?>">
<img class="img" src="<?=IMAGES_URL?>items/small/<?=$d['pic']?>.jpg" title="<?=htmlspecialchars($d['name'])?>">
<div class="name"><b><?=$d['name']?></b></div>
<div class="mrp">Rs <span class="m_<?=$d['id']?>"><?=$d['mrp']?></span></div>
</div>
<?php }?>
</div>
</div>
</td>
</tr>
<?php $mi++; }?>

</table>

<div align="right" style="padding-top:10px;">
<a href="javascript:void(0)" onclick='buynowbody()'><img src="<?=IMAGES_URL?>instant_checkout.png"></a>
</div>

</div>
<style>
.bodyparts .window{
width:670px;
padding:7px;
height:127px;
position:relative;
overflow:hidden;
background:#eee;
}
.bodyparts .window .wind_deal{
background:#fff;
position:absolute;
cursor:pointer;
height:117px;
overflow:hidden;
width:120px;
padding:3px;
border:1px solid #ccc;
}
.bodyparts .window .wind_deal img, .cartcont .img{
width:120px;
max-height:90px;
}
.bodyparts .window .wind_deal .name, .cartcont .name{
height:33px;
overflow:hidden;
}
.bodyparts .window .wind_deal .mrp{
display:none;
}
.cartcont .mrp{
color:red;
display:block !important;
}
.bodyparts .nextprev{
font-size:11px;
background:#8cc63f;
padding:2px 5px;
color:#fff;
-moz-border-radius:3px;
border-radius:3px;
text-decoration:none;
display:inline-block;
margin-left:5px;
margin-bottom:3px;
min-width:60px;
text-align:center;
}
.bodyparts .nextprev:hover{
background:#70A42C;
}
.bodyparts .nextprevdis{
background:#ddd !important;
color:#777 !important;
}
.bodyparts .window .cont{
position:relative;
}
.bodyparts .specialcart{
clear:left;
padding-top:25px;
float:right;
width:155px;
min-height:400px;
padding-left:5px;
}
.bodyparts .windowtable{
border-right:1px solid #ccc;
width:820px;
}
.bodyparts .wind_deal_hover, .bodyparts .wind_deal_sel{
border:1px solid #ffaa00 !important;
}
.bodyparts .cartcont{
height:140px;
border:1px solid #ccc;
margin-bottom:20px;
padding:5px;
}
.bodyparts{
padding-top:15px;
}
.bodyparts .cartcont .empty{
background:#f1f1f1;
height:140px;
text-align:center;
color:#aaa;
}
.bodyparts .close{
margin-top:-12px;
margin-left:138px;
position:absolute;
display:none;
cursor:pointer;
}
.bodyparts .totalprice{
background:#fff;
padding:5px 5px 5px 15px;
position:absolute;
font-size:150%;
color:#000;
white-space:nowrap;
left:810px;
z-index:32142424;
}
.bodyparts .totalprice .instant_checkout{
display:inline-block;
}
.bodyparts .totalprice .instant_checkout img{
margin-bottom:-7px;
}
.bodyparts .price_counter{
display:inline;
display:inline-block;
background:#ff9900;
border:1px solid #aaa;
color:#fff;
padding:3px;
width:80px;
}
.bodyparts .price_counter .price{
font-weight:bold;
font-size:110%;
}
.bodyparts .totalprice_rem{
position:fixed;
top:0px;
border:1px solid #ccc;
padding-bottom:7px;
}
</style>

<script>
var items=new Array();
$(function(){
	$(".nextprev").click(function(){
		if($(this).hasClass("nextprevdis"))
			return;
		win=$(this).parent().parent().find(".window .cont");
		if(win.data("ongoinganim")==true)
			return;
		l=win.css("left");
		if(l.length==0)
			l=0;
		l=l.slice(0,-2);
		if($(this).hasClass("next"))
		{
			c=$(".wind_deal",win).length;
			if(l<=-(c-5)*135)
			{
				$(this).addClass("nextprevdis");
				win.css("left",-(c-5)*135+"px");
				return;
			}
			fl=parseInt(l)-135;
			if(fl==-(c-5)*135)
				$(this).addClass("nextprevdis");
			win.data("ongoinganim",true);
			win.animate({left:'-=135'},500,"swing",function(){
				win.data("ongoinganim",false);
				$(this).css("left",fl+"px");
			});
			$(".prev",$(this).parent()).removeClass("nextprevdis");
		}
		else
		{
			if(l>=0)
			{
				win.css("left","0px");
				return;
			}
			fl=parseInt(l)+135;
			if(fl==0)
				$(this).addClass("nextprevdis");
			win.data("ongoinganim",true);
			win.animate({left:'+=135'},500,"swing",function(){
				win.data("ongoinganim",false);
				$(this).css("left",fl+"px");
			});
			$(".next",$(this).parent()).removeClass("nextprevdis");
		}
	});
	$(".bodyparts .prev").addClass("nextprevdis");
	$(".bodyparts .wind_deal").hover(function(){
		$(this).addClass("wind_deal_hover");
	},function(){
		$(this).removeClass("wind_deal_hover");
	});

	$(".bodyparts .wind_deal").click(function(){
		par=$(this).parent();
		if($(".wind_deal_sel",par).length!=0)
		{
			alert("You have already added a product from this category");
			return;
		}
		$(this).addClass("wind_deal_sel");
		c_id=par.attr("id");
		$(".pc_"+c_id+" .empty").hide();
		$(".sc_"+c_id+" .cont").html($(this).html());
		$(".sc_"+c_id).fadeIn("slow");
		items.push(parseInt($(".itemid",$(this)).val()));
		calc_price();
	});
	$(".cartcont").hover(function(){
		$(".close",$(this)).show();
	},function(){
		$(".close",$(this)).hide();
	});
	$(".cartcont .close").click(function(){
		p=$(this).parent().parent();
		$(".cont",p).html("");
		$(".sc_cont",p).hide();
		$(".empty",p).fadeIn("slow");
		c_id=p.attr("id").split("_").pop();
		iid=parseInt($("#"+c_id+" .wind_deal_sel .itemid").val());
		var t=new Array();
		for(i=0;i<items.length;i++)
			if(parseInt(items[i])!=iid)
				t.push(items[i]);
		items=t;
		$("#"+c_id+" .wind_deal").removeClass("wind_deal_sel");
		calc_price();
	});
	$(window).scroll(function(){
		$t=$(window).scrollTop();
		$tt=$(".totalpricetrig").offset().top;
		if($t>$tt)
			$(".totalprice").addClass("totalprice_rem");
		else
			$(".totalprice").removeClass("totalprice_rem");
	});
	$(".price").bind;
});
function incr_countr(f,i)
{
	c=parseInt($(".totalprice .price").html());
	if(!i)
	{
		i=Math.ceil((f-c)/10);
		if(c>f)
			i*=-1;
	}
	c+=i;
	if((i<0 && f>c) || (i>0 && c>f))
		c=f;
	else
		window.setTimeout(function(){incr_countr(f,i);},50);
	$(".totalprice .price").html(c);
}
function calc_price()
{
	t=0;
	for(i=0;i<items.length;i++)
		t+=parseInt($(".wind_deal .m_"+items[i]).html());
	incr_countr(t);
}
function buynowbody()
{
	iids=[];
	ref=true;
	$(".cartcont").each(function(){
		if($(".itemid",$(this)).length==0)
		{
			alert("Oops! You forgot to choose a product from the category. Please select a product from each category to checkout.");
			ref=false;
			return false;
		}
	});
	if(!ref)
		return;
	$(".cartcont").each(function(){
		iids.push($(".itemid",$(this)).val());
	});	
	postparams(site_url+"productsforwholebody",{"itemids":iids.join(",")});
}
</script>

<?php
