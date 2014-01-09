<?php
/*
 * Created on May 30, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
?>
<?php $coupon=$this->session->userdata("coupon"); ?>
<div style="padding:5px">
<div style="border-bottom:1px solid #aaa;padding-bottom:2px;font-size:120%;font-weight:bold;">Checkout Summary</div>
<?php 
$items=$this->cart->contents();
$total=$this->cart->total();
if($coupon)
	$total=$total-$coupon['value'];
if($total<0)
	$total=0;
?>
<table width="100%" style="margin-top:10px;" cellspacing=0>
<?php $qty=0; foreach($items as $item){?>
<tr>
<td><?=$item['name']?></td>
<td align="right" style="white-space:nowrap;"><?=$item['qty']?>x Rs <?=$item['price']?></td>
</tr>
<?php $qty+=$item['qty'];}?>
<tr>
<td>Sub Total</td>
<?php $items=$this->cart->contents();?>
<td align="right">Rs <?=$total?></td>
</tr>
<?php if($this->session->userdata("coupon")){ $coupon=$this->session->userdata("coupon"); ?>
<tr>
<td>Coupon</td>
<td align="right">&#8212; Rs <?=$total-$coupon['value']?></td>
</tr>	
<?php $total=$coupon['bill_value'];} ?>
<?php if(isset($cod)){?>
<tr class="codchge" style="display:none;">
<td><b>COD Charges</b></td>
<td align="right"><b>Rs <?=$cod?> x<?=$qty?></b></td>
</tr>
<?php }else $cod=0; ?>
<tr>
<td colspan=2 align="center">Inclusive of Taxes + <span class="green freeship">FREE SHIPPING</span></td>
</tr>
</table>
<table width="100%" style="margin-top:5px;" cellspacing=0>
<tr style="font-weight:bold;font-size:120%;">
<td style="border:1px solid #aaa;border-width:1px 0px;padding:5px 0px;">Total</td>
<td style="border:1px solid #aaa;border-width:1px 0px;font-size:110%;padding:5px 0px;color:red;" align="right">
<b class="noncodchge codchge">Rs <?=$total?></b>
<b class="codchge" style="display:none;">Rs <?=$total+($cod*$item['qty'])?></b>
</td>
</tr>
</table>
<?php if($this->uri->segment(2)!="step3"){?>
<div align="right" style="margin-top:5px;"><img src="<?=base_url()?>images/editcart.png" onclick='$("#cartlink").click()' style="background:#444;color:#fff;border:1px solid #aaa;cursor:pointer;"></div>
<?php } ?>

<?php if($this->session->flashdata("couponmsg")){?>
			<div style="color:red;"><b><?=$this->session->flashdata("couponmsg")?></b></div>
<?php } ?>
		<div>
		<table width="100%">
		<tr>
<?php if(!$coupon){?>
			<td style="text-align:left">
			<form method="post" action="<?=site_url("jxcoupon")?>">
				<?php foreach($_POST as $n=>$v){?>	<input type="hidden" name="<?=$n?>" value="<?=htmlspecialchars($v)?>">	<?php } ?>
				Enter coupon code : <input type="text" name="coupon" id="couponcode"> <input type="submit" value="Apply">
			</form>
			</td>
			<td style="text-align:right;color:#F58728;"><b>Rs 0</b></td>
<?php }else{?>
			<td style="text-align:left;">Coupon : <b><?=$coupon['code']?></b></td>
//			<td style="text-align:right;color:#F58728;"><b>&#8212; Rs <?=number_format($coupon['value'])?></b></td>
<?php } ?>
		</tr>
		</table>
		</div>


</div>
<?php
