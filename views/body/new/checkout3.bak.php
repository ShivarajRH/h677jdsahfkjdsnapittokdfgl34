<?php
/*
 * Created on Jun 11, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
?>

<div style="padding-bottom:5px;" align="center">

<div class="container">


<div align="left" style="padding-top:10px;margin:10px;">
<div style="border:1px solid #aaa;-moz-border-radius:10px;padding:">
<div style="-moz-border-radius:10px 10px 0px 0px;background:url(<?=base_url()?>images/checkout_bg.png);padding:10px;font-size:18px;font-weight:bold;">
<div class="cc_steps">
<div class="active"><span>3</span> PAYMENT</div>
<div>&raquo;&raquo;</div>
<div class="done"><span>2</span> ADDRESS</div>
<div>&raquo;&raquo;</div>
<div class="done"><span>1</span> ACCOUNT</div>
</div>
CheckOut
</div>

<table width="100%" cellspacing=0 cellpadding=0 style="background:#fff">
<tr>

<td style="background:#eee;" valign="top" width="25%">
<?php $this->load->view("body/new/ordersum");?>
</td>

<td valign="top">

<form method="post" id="cform" action="<?=site_url("checkout/final")?>">

<?php foreach($_POST as $p=>$v){?>
	<input type="hidden" name="<?=$p?>" value="<?=$v?>">
<?php } ?>

<div style="padding:10px;">

<div style="border-bottom:1px solid #aaa;font-size:120%;width:250px;padding-bottom:3px;font-weight:bold;">Select your payment method</div>

<div class="payselect" style="padding:20px;font-weight:bold;">

<div class="paysel"><label><input type="radio" name="paytype" value="card" checked class="paymetho dfmetho"> Credit Card / Debit Card</label>
<div style="margin-left:25px;margin-top:5px;"><img src="<?=base_url()?>images/mastercard.png"><img src="<?=base_url()?>images/visa.png"></div>
</div>

<div class="paysel" style="margin-left:125px;"><label><input type="radio" name="paytype" value="net" class="paymetho"> Internet Banking<div style="padding-left:25px;font-size:80%;color:#888;">Select your bank and pay online</div></label>
<div style="margin-left:25px;margin-top:5px;"><img src="<?=base_url()?>images/banks.jpg"></div>
</div>

<div class="paysel">
<?php if(isset($codavailable)){?>
<label><input type="radio" name="paytype" value="cod" class="paymetho codmetho"> Cash on Delivery<div style="padding-left:25px;font-size:80%;color:#888;">COD only available to Bangalore</div></label>
<?php }/*else{?>
<div style="padding-left:25px;font-size:105%;margin-top:20px;color:red;">Cash On Delivery not available in your location</div>
<?php } */?>
</div>

<div align="right" style="clear:both;margin-top:60px;margin-right:30px;"><input type="image" src="<?=base_url()?>images/proceedtopayment.png">
</div>

</form>

</td>

</tr>
</table>

</div>

</div>

</div>
</div>
