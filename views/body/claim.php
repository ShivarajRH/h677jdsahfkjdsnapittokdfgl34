<div class="container" style="padding:20px 0px;">

<h1>Claim your <?=isset($points)?"points":"cashback"?> : <b style="color:red">Rs <?=isset($points)?$points:$cashback['amount']?></b></h1>

<?php if(isset($points)){?>
<ul class="claim_side_help">
<li>Your points can be redeemed and a coupon code is generated for the value you insist</li>

<li>Please select the coupon code Value and the quantity of the coupon codes</li>

<li>You can generate the coupon code for the complete value of your points earned ( must be greater than 200)</li>

<li>Coupons generated is stored in ( My credits / Cash back Tab)</li>

<li>You can use the coupon code for your next transaction</li>
</ul> 
<?php }?>

<form method="post">

<table class="claimtab" cellpadding=7 cellspacing=0 style="margin:20px 50px;">
<tr style="background:#eee;">
<td colspan=3 style="border:0px;">Please choose your cashback snapcoupons</td>
</tr>
<tr class="head">
<th width=100>Coupon Value</th>
<th>Minimum Order</th>
<th>Select your quantity</th>
</tr>
<?php foreach($config as $c){?>
<tr>
<td>
<b>Rs <?=$c['value']?></b></td>
<td>Rs <?=$c['min']?></td>
<td>
<input type="hidden" class="c_value" name="v[]" value="<?=$c['value']?>">
<img class="arrow_bullet arrow_bullet_l" src="<?=IMAGES_URL?>bullet_arrow_left.png"><input class="nums" type="text" readonly="readonly" name="nums[]" size=2><img class="arrow_bullet arrow_bullet_r" src="<?=IMAGES_URL?>bullet_arrow_right.png"></td>
</tr>
<?php }?>
<tr style="font-weight:bold;background:#eee;">
<td colspan=2>Remaining Cashback</td>
<td><div class="remcash">Rs 0</div></td>
</tr>
</table>

<div style="width:450px;" align="right">
<input type="submit" value="Create my snapcoupons">
</div>
<?php if(!isset($points)){?>
<div style="margin:20px;width:500px;background:#eee;padding:7px 10px;">
Please try to utilize your total cashback amount of <b>Rs <?=$cashback['amount']?></b><br>This is a one time process and your remaining cashback amount won't be available for further redemption, once you click on 'Create my snapcoupons'.
</div>
<?php }?>
</form>

</div>
<style>
.nums{
text-align:center;
width:20px;
}
.remcash{
font-weight:bold;
}
.claimtab{
border:1px solid #777;
width:400px;
}
.claimtab th{
background:#777;
color:#fff;
}
.claimtab td{
border-top:1px dashed #aaa;
}
.arrow_bullet{
margin:2px;
margin-bottom:-3px;
display:inline-block;
cursor:pointer;
}
</style>
<script>
var cashbk=<?=isset($points)?$points:$cashback['amount']?>;
function changemad(o)
{
	p=cashbk;
	$(".nums").each(function(){
		v=parseInt($(".c_value",$(this).parent()).val());
		n=parseInt($(this).val());
		p-=v*n;
	});
	if(p<0)
		return false;
	$(".remcash").html("Rs "+p);
	return true;
}
$(function(){
	$(".nums").val("0");
	$(".arrow_bullet").click(function(){
		o=$(".nums",$(this).parent());
		if($(this).hasClass("arrow_bullet_l"))
		{
			if(parseInt(o.val())==0)
				return false;
			o.val(parseInt(o.val())-1);
		}
		else
			o.val(parseInt(o.val())+1);
		r=changemad();
		if(!r)
			o.val(parseInt(o.val())-1);
		changemad();			
		if(!r)
			alert("Sorry! you don't have enough <?=isset($points)?"points":"cashback"?>");
	});
	changemad();
});
</script>
<?php
