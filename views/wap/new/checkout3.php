<?php
/*
 * Created on Jun 11, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
?>

<div style="background:#43B9D6;padding-bottom:5px;" align="center">

<div class="container">


<div align="left" style="padding-top:10px;margin:5px;">
<div style="border:1px solid #aaa;-moz-border-radius:10px;padding:">
<div style="-moz-border-radius:10px 10px 0px 0px;background:url(<?=base_url()?>images/checkout_bg.png);padding:10px;font-size:18px;font-weight:bold;">
CheckOut
</div>

<table width="100%" cellspacing=0 cellpadding=0 style="background:#fff">
<tr>

<td style="background:#eee;" valign="top">
<?php $this->load->view("wap/new/ordersum");?>
</td>
</tr>
<tr>
<td valign="top">

<form method="post" id="cform" action="<?=site_url("checkout/final")?>">

<?php foreach($_POST as $p=>$v){?>
	<input type="hidden" name="<?=$p?>" value="<?=$v?>">
<?php } ?>

<div style="padding:5px;">

<div style="border-bottom:1px solid #aaa;font-size:120%;padding-bottom:3px;font-weight:bold;">Select your payment method</div>

<div class="payselect" style="padding:5px;font-weight:bold;">

<div class="paysel"><label><input type="radio" name="paytype" value="card" checked class="paymetho dfmetho"> Credit Card / Debit Card</label>
<div style="margin-left:25px;margin-top:5px;"><img src="<?=base_url()?>images/mastercard.png"><img src="<?=base_url()?>images/visa.png"></div>
</div>

<div class="paysel"><label><input type="radio" name="paytype" value="net" class="paymetho"> Internet Banking<div style="padding-left:25px;font-size:80%;color:#888;">Select your bank and pay online</div></label>
</div>

<div class="paysel">
<?php if(isset($codavailable)){?>
<label><input type="radio" name="paytype" value="cod" class="paymetho codmetho"> Cash on Delivery<div style="padding-left:25px;font-size:80%;color:#888;">Currently available in your location<div style="color:red">Rs. <?=$cod?> extra for each quantity</div></div></label>
<?php }/*else{?>
<div style="padding-left:25px;font-size:105%;color:#f00;margin-top:20px;">Cash On Delivery : Currently not available in your location</div>
<?php } */?>
</div>

<div align="right" style="clear:both;margin-top:60px;margin-right:10px;"><input type="image" src="<?=base_url()?>images/proceedtopayment.png">
</div>

</form>

</td>

</tr>
</table>

</div>

</div>

</div>
</div>
<script>
$(function(){
	$(".paymetho").change(function(){
<?php /*		if($("#cform .codmetho").attr("checked")==true && $("#cform input[name=city]").val().toLowerCase()!="bangalore")
		{
			alert("Sorry! Cash on delivery is currently available only in Bangalore");
			$("#cform .dfmetho").attr("checked",true);
		}
		else */?> if($("#cform .codmetho").attr("checked")==true)
			$(".codchge").toggle();
		else
		{
			$(".codchge").hide();
			$(".noncodchge").show();
		}
	});
	$(".dfmetho").attr("checked",true);
	$("#cform").submit(function(){
		if($(".codmetho",$(this)).attr("checked")==true)
			return confirm("You have selected Cash on Delivery option. We will charge you Rs 50 extra for each quantity towards COD charges. Do you agree to this?");
	});
});
</script>
<style>
.paysel{
	padding:5px 0px;
}
</style>