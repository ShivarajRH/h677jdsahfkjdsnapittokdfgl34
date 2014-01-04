<?php

$user=$this->session->userdata("user");
$item=$itemdetails;
$special_ckout=false;
if($this->session->userdata("bodyparts_checkout"))
	$special_ckout=true;
?>

<div class="container">
<div class="product_container">
<?php /*?>
				<div class="breadcrumb" align="left">

					<div align="left" style="float:right;padding-bottom:0px;">		
						<div class="showlove">
							<img id="item_disc_tag" src="<?=IMAGES_URL?>snapit.png">
							<div class="clear"></div>
						</div>
					</div>


					<a href="<?=site_url($item['murl'])?>" class="blue"><?=$item['menu']?></a> &raquo; <a href="<?=site_url($item['murl']."/".$item['caturl'])?>" class="blue"><?=ucwords($item['category'])?></a> &raquo; <a href="<?=site_url($item['caturl']."/".$item['burl'])?>" class="blue"><?=ucwords($item['brandname'])?></a> &raquo; <a href="<?=site_url($item['url'])?>" class="blue"><?=ucwords($item['name'])?></a>
				</div>
*/ ?>

	<div class="imgcont">
		<h1><?=$item['name']?></h1>

		<div class="product_image">
			<a id="zoom1" rel="position: 'cz-cnt'" href="<?=IMAGES_URL?>items/big/<?=$itemdetails['pic']?>.jpg" class="cloud-zoom itemphotos">
				<img title="<?=htmlspecialchars($itemdetails['name'])?>" alt="<?=htmlspecialchars($itemdetails['name'])?>" src="<?=IMAGES_URL?>items/<?=$itemdetails['pic']?>.jpg" style="width:100%;">
			</a>
			<?php $itemresources[0][]=array("id"=>$itemdetails['pic']); ?>
		</div>
		<div id="photosvideos">
			<div style="padding-top:3px; width: 100%; text-align: left;">
				<?php 
				if(count($itemresources[0])>0)
				foreach($itemresources[0] as $pic){?>
				<a class="cloud-zoom-gallery" rel="useZoom: 'zoom1', smallImage: '<?=IMAGES_URL?>items/<?=$pic['id']?>.jpg'" style="padding:3px;" href="<?=IMAGES_URL?>items/big/<?=$pic['id']?>.jpg"><img src="<?=IMAGES_URL?>items/thumbs/<?=$pic['id']?>.jpg" height="50" style="background:#fff;border:1px solid #eee;"></a>
				<?php 
				}
				?>
				<?php 
				if(count($itemresources[1])>0)
					echo '<div style="padding-top:10px;padding-bottom:2px;font-size:18px;border-bottom:2px solid #ccc;">Videos</div>';
				foreach($itemresources[1] as $pic){?>
				<a title="<?=$item['name']?>" href="http://www.youtube.com/watch?v=<?=$pic['id']?>&feature=player_embedded" class="vlink"><img src="http://i3.ytimg.com/vi/<?=$pic['id']?>/default.jpg" width="100" style="border:1px solid #eee;"></a>
				<?php 
				}
				?>
			</div>
			<div class="clear:both;"></div>
		</div>
		
		<div class="catbrand">
			<div>Category : <a href="<?=site_url($item['caturl'])?>"><?=$item['category']?></a></div>
			<div>Brand : <a href="<?=site_url($item['burl'])?>"><?=$item['brandname']?></a></div>
		</div>
		
		<div class="writereview">
			<div class="star_rating"><?php for($i=0;$i<5;$i++){?><img src="<?=IMAGES_URL?>star.png"><?php }?></div>
			<h3>Write Review</h3>
			<div class="txt">
				<textarea class="review_copy" style="width:100%;">Write review...</textarea>
			</div>
			<div align="right">
			<a id="writereview_trig" href="#writereview_modal"></a>
				<a href="javascript:void(0)" onclick='showwritereview()'><img src="<?=IMAGES_URL?>submit_review.png"></a>
			</div>
		</div>
		
		<div style="display:none">
		<div class="writereview" id="writereview_modal">
			<h3>Write Review</h3>
			<form id="writereviewfrm">
				<input type="hidden" name="itemid" value="<?=$item['id']?>">
				<input type="hidden" name="rating" value="5">
				<table cellpadding=5 style="clear:right;font-size:120%;">
					<tr>
						<td>Title</td>
						<Td><input type="text" name="title" style="width:320px;"></Td>
					</tr>
					<tr><td>Review</td><td><textarea style="width:350px;height:100px;" name="review"></textarea>
					<div style="font-size:70%;color:#777;"><b>Please do not include</b> HTML, references to other retailers, pricing, personal information, any profane or inflammatory comments</div> 
					</td></tr>				
					<tr <?php if($this->session->userdata("user")){?>style="display:none;"<?php }?>><td>Your&nbsp;Name</td><td><input type="text" name="uname" size=30 <?php if($this->session->userdata("user")){$user=$this->session->userdata("user");?>value="<?=$user['name']?>"<?php }?>></td></tr>
				</table>
				<div class="update" align="right">
					<input type="image" src="<?=IMAGES_URL?>submit_review.png">
				</div>
			</form>
		</div>
		</div>
		
								
		<div class="social">
			<div style="float:left;"><iframe src="http://www.facebook.com/plugins/like.php?href=<?=urlencode(site_url($item['url']))?>&amp;send=false&amp;layout=standard&amp;width=90&amp;show_faces=false&amp;action=recommend&amp;colorscheme=light&amp;font&amp;height=25" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:90px; height:25px;" allowTransparency="true"></iframe></div>
			<div style="float:left;margin:0px 5px;"><g:plusone size="large" count=false></g:plusone></div>
			<div style="float:left;"><iframe frameborder="0" scrolling="no" allowtransparency="true" style="border: medium none; overflow: hidden; width: 50px; height: 25px; vertical-align: top;" src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.facebook.com%2Fsnapittoday&amp;send=false&amp;layout=button&amp;width=60&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font=trebuchet+ms&amp;height=21"></iframe></div>
		</div>

		
	</div>
	<div class="tabscont">
		<ul class="tabs">
			<li class="tab tabselected">
				<a href="#info">Info</a>
			</li>
			<li class="tab">
				<a href="#reviews">Reviews</a>
			</li>
			<li class="tab revtab">
				<a href="#buytogether">Relevant Products</a>
			</li>
		</ul>
			<div id="cz-cnt"></div>
			
			<div class="pricecont">
		
			<h2 class="price">Rs <?=$item['price']?></h2>
		
			<div class="instantcocont">
			<input type="hidden" id="qty" value="1">
<?php if(!$item['live']){?>
				<img src="<?=IMAGES_URL?>/ofs.png">
<?php }else if($itemdetails['quantity']<=$itemdetails['available']){?>
				<span style="color:red;font-size:150%;font-weight:bold;">SOLD OUT</span>
<?php }else{?>
				<div class="qtycont">
				Quantity: 
					<select id="inst_qty">
					<?php
						$mq=MAX_QTY;
						if($item['max_allowed_qty']!=0)
							$mq=$item['max_allowed_qty'];
						$qty_strict=array("372786323396");
						if(in_array($item['id'], $qty_strict)) $mq=1; 
					?>
					<?php for($i=1;$i<=$mq;$i++){?>
					<option value="<?=$i?>"><?=$i?></option>
					<?php }?>
					</select>
				</div>
				<div>
					<a class="instant_trig" onclick="instantco()">Add to cart</a>
				</div>
<?php }?>
			</div>
			</div>

		<div class="tabdivcont" id="infocnt">
			<div class="pricedetails">
				<div class="col">
					<div class="value">Rs <?=$item['orgprice']?></div>
					<div>MRP</div>
				</div>
				<div class="col midcol">
					<div class="value">FREE</div>
	<?php if($item['price']<MIN_AMT_FREE_SHIP){?>
					<div style="font-size:80%;">
					<div>shipping</div>
					<div>for orders above Rs <?=MIN_AMT_FREE_SHIP?></div>
					</div>
	<?php }else{?>
					<div>shipping</div>
	<?php }?>
				</div>
				<div class="col">
					<div class="value"><?=$item['shipsin']?></div>
					<div>Dispatch Time</div>
				</div>
			</div>
			<div class="clear"></div>
			<div class="description">
				<h3>Description</h3>
				<div class="cont">
					<?=$item['description1']?>
					<div>
					<?=$item['description2']?>
					</div>
				</div>
			</div>
		</div>
		
		<div class="tabdivcont" id="reviewscnt">
			<div id="reviewscont"></div>
		</div>
		
		<div class="tabdivcont" id="buytogethercnt">
<?php if(isset($relateds) && !empty($relateds)){?>					
					<div style="margin-top:10px;">
						<img src="<?=IMAGES_URL?>relevant.png">
					</div>
					<div class="related">
		<?php foreach($relateds as $r){?>			
						<div class="item">
							<div class="img"><img src="<?=IMAGES_URL?>items/thumbs/<?=$r['pic']?>.jpg"></div>
							<div class="select">
								Qty :
								<select class="relbuys" name="<?=$r['itemid']?>">
										<option value="0">None</option>
									<?php for($i=1;$i<=$r['max_allowed_qty'];$i++){?>
										<option value="<?=$i?>"><?=$i?></option>
									<?php }?>
								</select>
							</div>
							<div class="name">
							<a href="<?=site_url($r['url'])?>" target="_blank"><?=$r['name']?></a>
							</div>
							<div class="price">Rs <?=$r['price']?></div>
							<div class="clear"></div>
						</div>
		<?php }?>
						<div class="instantcocont" style="float:right;">
							<img onclick='instantco()' src="<?=IMAGES_URL?>addtocart.png">
						</div>
						<div class="clear"></div>
					</div>
<?php }?>
		</div>
		
	</div>
	
	<div class="clear"></div>
	
</div>

</div>

<div class="container">
<div class="product_container" style="padding:0px;">
		<div class="homecont" style="margin-top:20px;padding:0px;border:0px;">
				<div>
					<div class="bender bc-green">
						<div class="matter">Frequently bought together</div>
						<span></span>
					</div>
				</div>
			<div class="spot_deal_cont" style="border:0px;">
<?php foreach($extradeals as $i=>$deal){?>	
						<div class="dealdlist">
							<div class="imgcont" style="width:100%;padding:0px;border:0px;float:none;">
								<a href="<?=site_url("{$deal['url']}")?>" class="scrollman sm_prodt">
									<img class="scrm_load" src="<?=IMAGES_URL?>scroll_load.gif">
									<span class="scrm_data"><?=IMAGES_URL?>items/small/<?=$deal['pic']?>.jpg</span>
								</a>
<?php /*?>							<a href="<?=site_url("{$deal['url']}")?>"><img src="<?=IMAGES_URL?>items/small/<?=$deal['pic']?>.jpg" width=160></a>   */ ?>
							</div>
							<div class="namecont">
								<div><a class="at" href="<?=site_url($deal['caturl'])?>" style="text-decoration:none"><?=$deal['category']?></a></div>
								<div style="font-size:95%;padding:5px 0px;"><?=$deal['name']?></div>
							</div>
							<div class="price">
							<b>Rs <?=number_format($deal['orgprice'])?></b>
<?php if($deal['orgprice']!=$deal['price']){?>
							<div class="instantcashback"> Rs <?=$deal['price']?> <img src="<?=IMAGES_URL?>instantcashback.png"></div>
<?php }?>
							</div>
							<div class="freeshipping">
							<b class="blue">Free Shipping</b>
							<?php if($deal['price']<MIN_AMT_FREE_SHIP){?>
							 <span>on Rs <?=MIN_AMT_FREE_SHIP?> &amp; more</span>
							<?php }?>
							</div>
						</div>
<?php }?>
			<div class="clear"></div>
			</div>
		</div>

</div>
</div>
<style>
.homecont table td {
padding:20px 0px;
}
.container{
width:980px;
}
.spot_deal_cont .dealdlist{
width:222px;
}

</style>
<script>
var itemid='<?=$item['id']?>';
var selectedcoworkers=[],cws_emails=[];
var fb_loggedin=0;
var fbs=[],uids=[],emailar=[],rbuys=[];
var cws_min=0,bpid=0;
var uidstr="",emails="",fbstr="",fbemail="";
var minbuyers=0,maxbuyers=0;
var refund=0,slotprice=0;

$(function(){

	$(".review_copy").focus(function(){
		if($(this).text()=="Write review...")
			$(this).text("");
	}).blur(function(){
		if($(this).text()=="")
			$(this).text("Write review...");
	});
	
	$(".product_image").hover(function(){
		$("#cz-cnt").show();
	},function(){
		$("#cz-cnt").hide();
		window.setTimeout(function(){$("#cz-cnt").hide();},3000);
	});
	$("#cz-cnt").hover(function(){
		$(this).hide();
	});

	$("#writereviewfrm input[name=rating]").val("5");
	$("#writereviewfrm").submit(function(){
		if(!is_required($("textarea",$(this)).val()))
		{
			alert("Please write your review regarding this product");
			return false;
		}
		if(!is_required($("input[name=uname]",$(this)).val()))
		{
			alert("Please mention your name");
			return false;
		}
		$.fancybox.showActivity();
		pst=$(this).serialize();
		$.post("<?=site_url("jx/writereview")?>",pst,function(){
			$.fancybox.hideActivity();
			$.fancybox.close();
			alert("Thanks for writing review. Your review was submitted.");
			getreviews(itemid,function(revs){
				$("#reviewscont").html(revs);
				reviewhover();
			});
		});
		return false;
	});
	$(".star_rating img").hover(function(e){
		$(this).nextAll().attr("src","<?=base_url()?>images/unstar.png");
		$(this).prevAll().attr("src","<?=base_url()?>images/star.png");
		$(this).attr("src","<?=base_url()?>images/star.png");
		e.stopPropagation();
	},function(e){
		i=0;
		$(".star_rating img").attr("src","<?=base_url()?>images/unstar.png").each(function(){
			i++;
			$(this).attr("src","<?=base_url()?>images/star.png");
			if(i==$("#writereviewfrm input[name=rating]").val())
				return false;
		});
		e.stopPropagation();
	}).click(function(){
		$(this).nextAll().attr("src","<?=base_url()?>images/unstar.png");
		$(this).prevAll().attr("src","<?=base_url()?>images/star.png");
		$(this).attr("src","<?=base_url()?>images/star.png");
		$("#writereviewfrm input[name=rating]").val($(this).prevAll().length+1);
	});

	$("#writereview_trig").fancybox();
	
});

function showwritereview()
{
	$("#writereviewfrm textarea").text($(".review_copy").text());
	$("#writereview_trig").click();
}

var instantcheckout=0;
function instantco()
{
<?php if($this->session->userdata("bodyparts_checkout")){?>
	alert("You have an active special checkout! Please clear it or checkout, if you want to add this product to cart");
<?php }else{?>
<?php if($item['live']){?>
	q=$("#inst_qty").val();
	$("#qty").html("<option value='"+q+"'>"+q+"</option>");
	$("#qty").val(q);
	uids=[];emailar=[];uidstr="";emails="";
	instantcheckout=1;
	$(".relbuys").each(function(i){
		if($(this).val()!=0)
			rbuys.push($(this).attr("name")+"-"+$(this).val());
	});
	startbuyp();
<?php }else{?>
alert("Stock not available. Please check back later");
<?php }?>
<?php }?>
}
function startbuyp()
{
	$.fancybox.showActivity();
	rb=rbuys.join(",");
	pst="qty="+$("#qty").val()+"&uids="+uidstr+"&item="+itemid+"&emails="+emails+"&fbs="+fbstr+"&fbemail="+fbemail+"&rbuys="+rb;
	$.post("<?=site_url("jx/startbuyprocess")?>",pst,function(resp){
		bpid=resp.bpid;
		addtocart();
	},"json");
}
function checkusrlr()
{
<?php if(!$this->session->userdata("user")){?>
$(".review a").click(function(e){
	alert("Please login to like this review");
	e.stopPropagation();
});
<?php }?>
}


</script>

<?php
