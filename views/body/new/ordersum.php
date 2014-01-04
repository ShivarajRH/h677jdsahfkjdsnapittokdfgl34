<?php
/*
 * Created on May 30, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
$coupon=$this->session->userdata("coupon");
?>
<div style="padding:20px">
<div style="border-bottom:1px solid #aaa;padding-bottom:2px;font-size:120%;font-weight:bold;">Checkout Summary</div>
<?php 
$items=$this->cart->contents();
$total=$this->cart->total();
$fivedisc=$price_total=0;
$fivefavs=false;

				$mrp_total=0;
				$prods=array();
				foreach($this->cart->contents() as $item)
				{
					$prod=$this->db->query("select i.id,d.catid,d.brandid,i.orgprice,i.orgprice as mrp,i.shipsto from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=?",$item['id'])->row_array();
					$shipsto=$prod['shipsto'];
					$prod['qty']=$item['qty'];
					$prod['price']=$item['price'];
					$mrp_total+=$prod['mrp']*$item['qty'];
					$price_total+=$prod['price']*$item['qty'];
					$prods[]=$prod;
				}


if($coupon)
{
	
	$cats=$brands=array();
/*	if($coupon['type']==0)	
		$total=$total-$coupon['value'];
	elseif($coupon['type']==1)
		$total=$total-floor($total*$coupon['value']/100);
*/

							
	$c_total=$total;
	if($coupon['mode']==1)
		$c_total=$mrp_total;
		
	if($coupon['catid']!="")
	{
		$cats=explode(",",$coupon['catid']);
		$c_total=0;
		foreach($prods as $p)
		if(in_array($p['catid'], $cats))
			$c_total+=($coupon['mode']==1?($p['mrp']*$p['qty']):($p['price']*$p['qty']));
	}	
				
	if($coupon['brandid']!="")
	{
		$brands=explode(",",$coupon['brandid']);
		$c_total=0;
		foreach($prods as $p)
		if(in_array($p['brandid'], $brands))
			$c_total+=($coupon['mode']==1?($p['mrp']*$p['qty']):($p['price']*$p['qty']));
	}
	
	if($coupon['type']==0)	
	{
		$cd_total=$c_total-$coupon['value'];
		if($cd_total<0)
			$cd_total=0;
	}
	elseif($coupon['type']==1)
		$cd_total=$c_total-floor($c_total*$coupon['value']/100);
	
	if($coupon['mode']==1)
	{
		$total=$mrp_total+$cd_total-$c_total;
		foreach($prods as $p)
		if(!empty($brands) || !empty($cats))
		if(!in_array($p['brandid'],$brands) && !in_array($p['catid'],$cats))
			$total-=($p['mrp']-$p['price'])*$p['qty'];
	}
	else
		$total=$this->cart->total()+$cd_total-$c_total;
				

			if($total>$this->cart->total())
				$total=$this->cart->total();
}else{
	$cd_total=$c_total=0;
	$favs=$this->dbm->getallfavids();
	foreach($prods as $prod)
	{
		if(in_array($prod['id'], $favs))
		{
			$fivefavs=true;
			$c_total+=$prod['mrp']*$prod['qty'];
		}
	}
			$cd_total=$c_total-floor($c_total*FAV_DISCOUNT/100);
			$fivedisc=floor($c_total*FAV_DISCOUNT/100);
			$total=$mrp_total+$cd_total-$c_total;
		foreach($prods as $p)
		if(!in_array($p['id'],$favs))
			$total-=($p['mrp']-$p['price'])*$p['qty'];
			if($total>$this->cart->total())
				$total=$this->cart->total();
}

if($total<0)
	$total=0;

?>
<table width="100%" style="margin-top:10px;" cellpadding=3 cellspacing=0>
<?php $qty=0;$pi=0; foreach($items as $item){?>
<tr>
<td><?=$item['name']?></td>
<td align="right" style="white-space:nowrap;"><?=$item['qty']?>x Rs <?=$prods[$pi]['orgprice']?></td>
</tr>
<?php $qty+=$item['qty'];}?>
<tr>
<td>Sub Total</td>
<?php $items=$this->cart->contents();?>
<td align="right">Rs <?=$mrp_total?></td>
</tr>
<?php if($this->session->userdata("coupon")){
?>
<tr>
<td>Coupon</td>
<td align="right">&ndash; Rs <?=$mrp_total-$total?></td>
</tr>	
<?php }else{?>
<tr>
<td>Discount</td>
<td align="right">&ndash; Rs <?=($mrp_total-$total-$c_total+$cd_total)?></td>
</tr>
<?php }?>
<?php if($fivefavs){?>
<tr>
<td><?=FAV_LIMIT?>FAVs Savings</td>
<td align="right">&ndash; Rs <?=$fivedisc?></td>
</tr>	
<?php }?>
<?php if(isset($cod) && $total>MIN_AMT_FREE_SHIP){?>
<tr class="codchge" style="display:none;">
<td><b>COD Charges</b></td>
<td align="right"><b>Rs <?=$cod?></b></td>
</tr>
<?php }else $cod=0; ?>
<?php if($total<MIN_AMT_FREE_SHIP){?>
<tr>
<td><b>Shipping Charges</b><br>
<div style="font-size:90%">as billing amount is less than Rs <?=MIN_AMT_FREE_SHIP?></div>
</td>
<td align="right"><b>Rs <?=SHIPPING_CHARGES?></b></td>
</tr>
<?php }?>
<tr>
<td colspan=2 align="center">Inclusive of Taxes <?php if($total>MIN_AMT_FREE_SHIP){?>+ <span class="green freeship">FREE SHIPPING</span><?php }?></td>
</tr>
</table>
<table width="100%" style="margin-top:5px;" cellspacing=0>
<tr style="font-weight:bold;font-size:120%;">
<td style="border:1px solid #aaa;border-width:1px 0px;padding:5px 0px;">Total</td>
<td style="border:1px solid #aaa;border-width:1px 0px;font-size:110%;padding:5px 0px;color:red;" align="right">
<b class="noncodchge codchge">Rs <?=$total<MIN_AMT_FREE_SHIP?$total+SHIPPING_CHARGES:$total?></b>
<b class="codchge" style="display:none;">Rs <?=$total<MIN_AMT_FREE_SHIP?($total+SHIPPING_CHARGES):($total+$cod)?></b>
</td>
</tr>
</table>

<?php if($this->uri->segment(2)!="step3"){?>
<div align="right" style="margin-top:5px;"><img src="<?=base_url()?>images/editcart.png" onclick='$("#cartlink").click()' style="color:#fff;cursor:pointer;"></div>
<?php } ?>

<?php if($this->dbm->isfsavailable($total)){?>
<div style="margin-top:5px;">
You are eligible for <b>Free Samples</b>!<br>Please click <a href="javascript:void(0)" onclick='$("#freesamples").click()'>here</a> to get your <b>free samples</b> along with your order.
<a href="<?=site_url("jx_freesamples/$total")?>" class="fancylink" id="freesamples"></a>
</div>
<?php }?>


<div align="center" style="border:1px solid #aaa;border-width:1px 0px;font-size:110%;padding:5px;margin-top:30px;background:#fff;font-weight:bold;">
<img src="<?=base_url()?>images/safe.png" style="float:left"> <div style="padding:20px 0px;">Safe &amp; Secure<br>SHOPPING</div>
</div>
</div>

<script>
$(function(){
	$("#cform").submit(function(){
<?php if($total>MIN_AMT_FREE_SHIP){?>
		if($(".codmetho:checked",$(this)).length==1)
			return true;//confirm("You have selected Cash on Delivery option. We will charge you Rs 50 extra towards COD charges. Do you agree to this?");
<?php }?>
	});
});
function fssaved(){
	$("#fsform").html("<h2 align='center'>Your free samples were saved!</h2>");
}
</script>

<?php
