<?php $item=$itemdetails;?>
<div class="container featureditem">


<div style="display:none">
	<div id="writereview" style="background:#fff;width:500px;" align="center">
		<h3 align="left">Write a Review</h3>
		<div class="cont">
		<form id="writereviewfrm">
			<input type="hidden" name="itemid" value="<?=$item['id']?>">
			<input type="hidden" name="rating" value="5">
			<table cellpadding=5 style="font-size:120%;padding:10px;margin-left:30px;">
				<tr>
					<td>Rating</td>
					<td class="star_rating"><?php for($i=0;$i<5;$i++){?><img src="<?=IMAGES_URL?>star.png"><?php }?></td>
				</tr>
				<tr><td>Review</td><td><textarea style="width:300px;height:100px;" name="review"></textarea></td></tr>				
				<tr <?php if($this->session->userdata("user")){?>style="display:none;"<?php }?>><td>Your Name</td><td><input type="text" name="uname" size=30 <?php if($this->session->userdata("user")){$user=$this->session->userdata("user");?>value="<?=$user['name']?>"<?php }?>></td></tr>
			</table>
			<div class="update" align="right">
				<input type="image" src="<?=IMAGES_URL?>submit_review.png">
			</div>
		</form>
		</div>
	</div>
</div>


<div class="pic">
					<div>
						<a id="zoom1" rel="position: 'cz-cnt'" href="<?=IMAGES_URL?>items/big/<?=$itemdetails['pic']?>.jpg" class="cloud-zoom itemphotos">
							<img title="<?=htmlspecialchars($itemdetails['name'])?>" alt="<?=htmlspecialchars($itemdetails['name'])?>" src="<?=IMAGES_URL?>items/<?=$itemdetails['pic']?>.jpg">
						</a>
						<?php 	$itemresources[0][]=array("id"=>$itemdetails['pic']); ?>
					</div>
					<div id="photosvideos" style="clear:left;float:left;">
						<div style="clear:left;float:left;">
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
						</div>
					</div>
</div>

<div class="details">
						<div id="cz-cnt"></div>
	<h1><?=$item['name']?></h1>
	<div style="float:right"><img id="item_disc_love" src="<?=IMAGES_URL?>loveit.png">
	<img id="item_disc_tag" src="<?=IMAGES_URL?>snapit.png">
	</div>
	<h2 class="brandname">
	by <?=$item['brandname']?>
	</h2>
	
	<div class="pricedetails">
		<div class="inside">
			<div class="cartcont">
			<?php if($item['live']){?>
				<a href="<?=site_url("api/buy/".$item['url'])?>"><img src="<?=IMAGES_URL?>addtocartfeatured.png"></a>
			<?php }else{?>
				<img src="<?=IMAGES_URL?>ofs.png">
			<?php }?>
			</div>
			<div class="price">
				<h2>Rs <?=$item['price']?></h2>
				<h3>Rs <?=$item['orgprice']?> MRP</h3>
			</div>
			<div class="qty">
				Quantity
				<select id="inst_qty">
				<?php
					$mq=MAX_QTY;
					if($item['max_allowed_qty']!=0)
						$mq=$item['max_allowed_qty'];
				?>
				<?php for($i=1;$i<=$mq;$i++){?>
				<option value="<?=$i?>"><?=$i?></option>
				<?php }?>
				</select>
			</div>
			<div class="clear"></div>
		</div>
	</div>
	<div class="description">
		<?=$item['description1']?>
			<div><?=$item['description2']?></div>
	</div>

	<div class="reviewcontpar">
		<div style="padding-top:10px;margin-bottom:5px;font-weight:bold;padding-bottom:5px;font-size:15px;border-bottom:2px solid #ccc;">Reviews</div>
		<div align="right"><a class="fancylink" href="#writereview" style="color:blue;font-size:90%;">write a review</a></div>
		<div id="reviewscont"></div>
	</div>
	
	
</div>

<div class="clear"></div>
</div>


<script>
var itemid="<?=$itemdetails['id']?>";var itemname="<?=$itemdetails['name']?>";var itempic="<?=$itemdetails['pic']?>";
$(function(){
	$(".featureditem .pic").hover(function(){
		$("#cz-cnt").show();
	},function(){
		$("#cz-cnt").hide();
		window.setTimeout(function(){$("#cz-cnt").hide();},3000);
	});
	getreviews(itemid,function(revs){
		$("#reviewscont").html(revs);
	});
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
		$.fancybox.close();
		$.fancybox.showActivity();
		pst=$(this).serialize();
		$.post("<?=site_url("jx/writereview")?>",pst,function(){
			$.fancybox.hideActivity();
			alert("Thanks for writing review. Your review was submitted.");
			getreviews(itemid,function(revs){
				$("#reviewscont").html(revs);
			});
		});
		return false;
	});
	$("#writereviewfrm input[name=rating]").val("5");
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
});
</script>

<style>
#writereview h3{
border-bottom:2px solid #aaa;
border-radius:15px 15px 0px 0px;
font-size:100%;
padding:10px 20px;
background:rgb(245, 245, 245);
}
#writereview .cont{
background:#F1F6F8;
}
#writereview .update{
margin-top:20px;
padding:10px 20px;
background:#D6E9F1;
}
</style>

<?php
