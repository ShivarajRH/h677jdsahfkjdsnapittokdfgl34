<script>
$(function(){
	$("#cartlink").attr("href","#cartdisabled");
	$("#cartlink").click(function(){$("#fancy_inner").css("height","80px");});
	$(".carthlink").click(function(){
		$(".headingtext").html("Please wait...");
		$.get($(this).attr("href"),function(resp){
			location='<?=site_url("viewcart")?>';
			});
		return false;
	});
	$(".couponlinks").click(function(){
		par=$(this).parent();
		$("div.cpform",par).show();
		$(".cperror",par).hide();
	});
	$("form.cpform").submit(function(){
		cid=parseInt($(".cp",$(this)).val());
		iid=$(".iid",$(this)).val();
		if(isNaN(cid)==true || cid<=999999999999999)
		{
			$("div",$(this).parent().parent()).hide();
			$("span.cperror",$(this).parent().parent()).html("Invalid coupon").show();
			return false;
		}
		d="coupon="+cid+"&id="+iid;
		err=$(this).parent().parent();
		$.post("<?=site_url("deals/couponen")?>",d,function(data){
			$(".headingtext").html("Please wait...");
			msg="";
			if(data==2)
				location.href="<?=site_url("checkout")?>";
			else if(data==1)
				msg="Coupon is already used";
			else if(data==3)
				msg="Coupon is invalid for this item";
			else if(data==4)
				msg="Coupon already used in another item";
			else
				msg="Invalid Coupon";
			if(msg!="")
			{	
				$(".cperror",err).html(msg).show();
				$("div",err).hide();
			}
			$(".headingtext").html("Checkout");
		});
		return false;
	});
});
</script>
<div id="cartdisabled" style="display: none;">
<div style="color: #000;">
<div
	style="font-family: 'trebuchet ms'; font-size: 23px; color: #ff9900;">Cart
disabled</div>
<div style="padding-top: 10px;">Shopping cart is disabled while checking
out!</div>
</div>
</div>
<style>
th {
	text-align: left;
	padding: 3px;
	padding-top: 15px;
}

td {
	padding-top: 15px;
	padding-left: 10px;
	height:25px;
}
div.cpform{
display:none;
}
span.cperror{
display:none;
background:#ff0;
color:red;
font-size:12px;
font-family:arial;
padding:3px;
}
</style>

<div class="headingtext ">Checkout</div>
<div align="left" style="padding-top: 7px;">
<div style="width:650px;float:left;">
<div style="font-family: 'trebuchet ms'; font-size: 19px;">Your shopping cart
items</div>
<div align="center"	style="margin-right: 10px; MARGIN-LEFT:20px; margin-top: 5px; font-family: arial; font-size: 14px">
<?php if(isset($naitem)){?>
<div align="left" style="margin:0px -30px;font-size:15px;color:#f00;margin-bottom:10px;background:#ff0;padding:7px;">One or more items in your cart are not available. Please remove those before checkout.</div>
<?php }?>
<div style="font-size:12px;padding-right:0px;margin-top:-5px;float:right"><a href="<?=base_url()?>" class="link1" style="color:#00f;">Continue shopping & add more items</a></div>
<div align="left" style="font-size: 11px;">You are about to buy the following items</div>
<table width="100%" cellpadding="0" cellspacing="0"
	style="border: 1px solid #ccc; background: #fff url(<?=base_url()?>images/title_bg.gif) repeat-x;">
	<tr style="font-weight: bold;">
		<th></th>
		<th>Item Name</th>
		<th>Quantity</th>
		<th>Price</th>
		<th></th>
	</tr>
	<tr>
	<td style="padding:0px;height:1px;"></td>
	<td colspan="3" style="padding-top:10px;border-bottom:1px solid #ccc;height:1px;"></td>
	</tr>
	<?php
	$total=$save=0;
	foreach($items as $item)
	{
		?>
		<tr>
		<td style="padding:0px;padding-bottom:15px;" width='75' align="right">
		<img src="<?=base_url()?>images/items/thumbs/<?=$item['pic']?>.jpg"></td><td style='padding-left:15px;padding-top:15px;' valign='top'><b><?=$item['name']?></b><div style="font-size:13px;padding-top:10px;">Ships in <b style="color:#e80021"><?=$item['shipsin']?></b> days</div>
		<?php if(!isset($item['na'])){?>
		<?php if(!isset($item['coupon'])){?>
		<div style="display:none"><a href="javascript:void(0)" class="couponlinks" style="font-size:11px;">use coupon</a>
		<div class="cpform"><form class="cpform"><input class="iid" type="hidden" value="<?=$item['rowid']?>"></input><input type="text" class="cp"></input><input type="submit" value="Use"></input></form></div>
		<span class="cperror"></span>
		</div>
		<?php }else{?>
		<div style="margin-top:5px;font-family:arial;font-size:12px;"><span style="padding:1px 3px;background:#ff9900;color:#fff;"><?=sprintf("%s",$item['coupon']/100)?>% discount</span> coupon used</span></div>
		<?php }?>
		<?php }?>
		</td>
		<td valign="top"><span><?=$item['qty']?></span> 
		<?php if($this->session->userdata("specialcot")==false){?>
		<a href="javascript:void(0)" onclick='$(this).hide();$("span",$(this).parent()).hide();$("form",$(this).parent()).show();' style="color:blue;font-size:9px;">edit</a>
		<form action="<?=site_url("vb/editqty")?>" style="display:none" method="post">
		<input type="hidden" name="id" value="<?=$item['rowid']?>">
		<select style="font-size:11px;" name="qty">
		<?php for($ci=1;$ci<=4;$ci++){?>
		<option value="<?=$ci?>" <?php if($item['qty']==$ci) echo "selected";?>><?=$ci?></option>
		<?php }?>
		</select><input type="submit" style="padding:0px;font-size:10px;" value="Go">
		</form>
		<?php }else{?>
		<span style="background:red;color:#fff;font-size:9px;font-weight:bold;">SPECIAL</span>
		<?php }?>
		</td>
		<?php 
		$t=$item['price']*$item['qty'];
		if(isset($item['coupon']))
		$t-=($t*$item['coupon']/100/100);
		?>
		<td valign="top" align="right" style="font-weight:bold">Rs <?php
		if(isset($item['coupon']))
		echo '<span style="text-decoration:line-through">'.($item['price']*$item['qty']).'</span> '.$t;
		else echo $t; 
		?>
		<div style="font-size:11px;color:#777;"><b>Save :</b> Rs <?=(($item['orgprice']-$item['price'])*$item['qty'])?> (<?=ceil(($item['orgprice']-$item['price'])/$item['orgprice']*100)?>%)</div>
		</td>
		<td valign="top"><a class='carthlink' href="<?=site_url("jx/deletecartitem/".$item['id'])?>"><img src="<?=base_url()?>images/remove.png"></a>
		<?php 
		if(isset($item['na']))
			echo " <span style='color:red;font-size:11px;'>NOT AVAILABLE</span>";
		echo "</td></tr>";
		$total+=$t;
		$save+=($item['orgprice']-$item['price'])*$item['qty'];
	}
	?>
</table>
<div style="padding: 10px 20px;" align="right">Total : <b>Rs <?=sprintf("%.2f",$total)?></b></div>
</div>
</div>
<?php if(!isset($naitem)){?>
<div style="margin-top:40px;font-family:trebuchet ms;-moz-border-radius:5px;padding:10px;float:right;width:250px;border: 1px solid #ccc; background: #fff url(<?=base_url()?>images/title_bg.gif) repeat-x;">
<h3>Purchase Details</h3>
<div style="padding-top:5px;font-size:15px;font-family:arial;">
Total Savings : <span style="font-weight:bold;color:#E80021;">Rs <?=$save?></span>
</div>
<div style="padding:5px;font-size:13px;">Your Bazaar price</div>
<div style="text-align:center;font-size:20px;font-weight:bold;padding:10px;">
<span style="color:#fff;background:#E80021;padding:7px 10px;-moz-border-radius:7px;">Rs <?=sprintf("%.2f",$total)?></span>
</div>
<div align="center" style="padding-top:5px;">
<a href="<?=site_url("checkout")?>"><img src="<?=base_url()?>images/checkout.gif"></a>
</div>
</div>
<?php }?>
</div>

