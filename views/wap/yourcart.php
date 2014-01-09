<?php
/*
 * Created on May 30, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
?>
<h2 style="margin:0px;">Your Shopping Cart</h2>
<?php $coupon=$this->session->userdata("coupon"); ?>
<div style="padding:5px;background:#fff;">
<div style="border-bottom:1px solid #aaa;padding-bottom:2px;font-size:120%;font-weight:bold;">Checkout Summary</div>
<?php 
$items=$this->cart->contents();
$total=$this->cart->total();
?>
<table width="100%" style="margin-top:10px;" cellspacing=0 cellpadding=3>
<?php $qty=0; foreach($items as $item){?>
<tr>
<td>
<?=$item['name']?>v	
<br>
<a href="<?=site_url("rmitem/".$item['rowid'])?>">remove</a>
</td>
<td>
<form action="<?=site_url("editqty")?>" method="post">
<input type="hidden" name="mobile" value="true"><input type="hidden" name="id" value="<?=$item['rowid']?>">
<select name="qty"><?php for($i=1;$i<6;$i++){?><option value="<?=$i?>"><?=$i?></option><?php }?></select>
<br>
<input type="submit" value="change">
</form>
</td>
<td align="right" style="white-space:nowrap;">
<div><?=$item['qty']?>x Rs <?=$item['price']?></div>
</td>
</tr>
<?php $qty+=$item['qty'];}?>
<tr>
<td>Sub Total</td>
<tD></tD>
<?php $items=$this->cart->contents();?>
<td align="right">Rs <?=$total?></td>
</tr>
<?php if($this->session->userdata("coupon")){ $coupon=$this->session->userdata("coupon"); ?>
<tr>
<td>Coupon</td>
<td align="right">&#8212; Rs <?=$total-$coupon['bill_value']?></td>
</tr>	
<?php $total=$coupon['bill_value'];} ?>
<?php if(isset($cod)){?>
<tr class="codchge" style="display:none;">
<td><b>COD Charges</b></td>
<td align="right"><b>Rs <?=$cod?> x<?=$qty?></b></td>
</tr>
<?php }else $cod=0; ?>
<tr>
<td colspan=3 align="center">Inclusive of Taxes + <span class="green freeship">FREE SHIPPING</span></td>
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
</div>
<div align="right">
<a href="<?=site_url("checkout")?>"><img src="<?=IMAGES_URL?>continue.png"></a>
</div>
<?php
