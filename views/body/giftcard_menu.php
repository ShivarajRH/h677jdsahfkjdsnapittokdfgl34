<div class="container" style="padding-bottom:50px;">
<h1>Gift Cards</h1>
<?php if(empty($cards)){?>
<div class="info">
No Gift cards available now. Please check back later.
</div>
<?php }else{
	foreach($cards as $card){?>
	
	<div class="giftcard_cont">
		<img src="<?=IMAGES_URL?>items/<?=$card['pic']?>.jpg" style="float:left;margin-right:10px;">
		<h2 style="padding-top:15px;"><?=$card['name']?></h2>
		<div style="padding-top:15px;font-size:120%;">Price : <b>Rs <?=$card['price']?></b></div>
		<div style="padding:10px;background:#ccc;border:1px solid #999;border-left:0px;margin-top:10px;margin-right:-10px;">
		<form class="giftcardform">
			<input type="hidden" name="itemid" class="iid" value="<?=$card['itemid']?>">
			<div>Delivery Medium : <select><option value="email">Email</option></select></div>
			<div>Please enter recipient's Email ID : <input class="email" type="text" name="email" style="width:200px;"></div>
			<div style="padding:5px;"><input type="image" src="<?=IMAGES_URL?>instant_checkout.png"></div>
		</form>
		</div>
		<div class="clear"></div>
	</div>

<?php }?>
<?php }?>
</div>
<style>
.giftcard_cont{
margin:20px 15px;
background:#eee;
}
</style>
<script>
$(function(){
	$(".giftcardform").submit(function(){
		p=$(this);
		if(!is_email($("input[name=email]",$(this)).val()))
		{
			alert("Please enter a valid email id");
			return false;
		}
		pst={item:$(".iid",p).val(),qty:1,gift_email:$(".email",p).val()};
		$.post("<?=site_url("jx/addtocart")?>",pst,function(){
			location="<?=site_url("shoppingcart")?>";
		});
		return false;
	});
});
</script>
<?php
