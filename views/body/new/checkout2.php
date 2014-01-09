<?php
$user=$this->session->userdata("user");
$a=false;
if(isset($addrdet))
	$a=$addrdet;
/*
 * Created on May 30, 2011
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
<div><span>3</span> PAYMENT</div>
<div>&raquo;&raquo;</div>
<div class="active"><span>2</span> ADDRESS</div>
<div>&raquo;&raquo;</div>
<div class="done"><span>1</span> ACCOUNT</div>
</div>
CheckOut
</div>
<table width="100%" cellspacing=0 cellpadding=0 style="background:#fff">
<tr>

<td style="background:#eee;" valign="top">
<?php $this->load->view("body/new/ordersum");?>
</td>

<td width="600">

<form action="<?=site_url("checkout/step3")?>" method="post" id="c2form">

<div style="padding:20px">

<div style="border-bottom:1px solid #aaa;font-size:110%;"><b>SHIPPING DETAILS</b></div>

<table width="650" class="shipdet" cellspacing=5 cellpadding=3>
<tr>
<td width="33%">Name<span style="color:red">*</span><br><input type="text" name="person" class="mand" value="<?=$user?htmlspecialchars($user['name']):""?>"></td>
</tr>
<tr>
<td colspan="2">Address<span style="color:red">*</span><br><input type="text" name="address" class="mand" value="<?=$a?htmlspecialchars($a['address']):""?>"></td>
</tr>
<tr>
<td colspan="2">Landmark<span style="color:red">*</span><br><input type="text" name="landmark" class="mand" value="<?=$a?htmlspecialchars($a['landmark']):""?>"></td>
</tr>
<tr>
<td width="33%" valign="top">City<span style="color:red">*</span><br>
<?php if(!isset($cities)){?>
<input type="text"  name="city" class="mand" value="<?=$a?htmlspecialchars($a['city']):""?>">
<?php }else{?>
<select name="city">
<?php foreach($cities as $c){?><option value="<?=$c?>" <?=($a && $a['city']==$c)?"checked":""?>><?=$c?></option>
<?php }?>
</select>&nbsp;&nbsp;<span title="One or more items in your cart can be shipped to this city only!" style="font-size:140%;cursor:pointer;cursor:help;color:red;font-weight:bold;">!</span>
<?php }?>
</td>
<td width="33%" valign="top">State<span style="color:red">*</span><br><input class="mand" type="text" name="state" value="<?=$a?htmlspecialchars($a['state']):""?>"></td>
<td width="33%" valign="top">Pincode<span style="color:red">*</span><br><input class="mand" type="text" name="pincode" maxlength="6" value="<?=$a?htmlspecialchars($a['pincode']):""?>">
<?php if($this->dbm->is_cod_available()){?>
<br><div style="font-weight:normal;color:red;font-size:80%">COD is available in selected cities</div>
<input type="button" value="Check COD Availability" onclick='checkcod()' style="font-size:80%;width:auto;color:auto;padding:3px 5px;">
<script>
function checkcod()
{
	$.fancybox.showActivity();
	$.post("<?=site_url("jx/checkcodpin")?>","pin="+$("input[name=pincode]").val(),function(data){
		$.fancybox.hideActivity();
		alert(data);
	});
}
</script>
<?php }?>
</td>
</tr>
<tr>
	<td width="33%">Country<br><select style="width:100%"><option value="India">India</option></select></td>
	<td colspan="2" style="font-weight:normal">Currently shipping only in India, coming up with more countries shortly</td>
</tr>
<tr>
<td width="33%">Mobile<span style="color:red">*</span><br><input class="mand" type="text" name="mobile" value="<?php if(!empty($user)) echo $user['mobile'];?>"></td>
<td width="33%">Email<span style="color:red">*</span><br><input class="mand" type="text" name="email" value="<?php if(!empty($user)) echo $user['email'];?>"></td>
<td width="33%">Telephone (optional)<br><input type="text" name="telephone" value="<?=$a?htmlspecialchars($a['telephone']):""?>"></td>
</tr>

</table>

<div style="padding:5px;">
<input type="checkbox" id="shipbillcheck" name="shipbillcheck">Billing address is same as shipping address
</div>

<div id="shippingaddr" style="display:none;">

<div style="border-bottom:1px solid #aaa;font-size:110%;"><b>BILLING DETAILS</b></div>

<table width="650" class="shipdet" cellspacing=5>
<tr>
<td width="33%">Name<br><input type="text" name="bill_person" class="mand" value="<?=$user?htmlspecialchars($user['name']):""?>"></td>
</tr>
<tr>
<td colspan="2">Address<br><input type="text" name="bill_address" class="mand" value="<?=$a?htmlspecialchars($a['address']):""?>"></td>
</tr>

<tr>
<td colspan="2">Landmark<br><input type="text" name="bill_landmark" value="<?=$a?htmlspecialchars($a['landmark']):""?>"></td>
</tr>

<tr>
<td width="33%">City<br><input type="text"  name="bill_city" class="mand" value="<?=$a?htmlspecialchars($a['city']):""?>"></td>
<td width="33%">State<br><input type="text" name="bill_state" class="mand" value="<?=$a?htmlspecialchars($a['state']):""?>"></td>
<td width="33%">Pincode<br><input type="text" name="bill_pincode" maxlength="6" class="mand" value="<?=$a?htmlspecialchars($a['pincode']):""?>"></td>
</tr>

<tr>
<td width="33%">Mobile<br><input type="text" name="bill_mobile" value="<?php if(!empty($user)) echo $user['mobile'];?>"></td>
<td width="33%">Email<br><input type="text" name="bill_email" class="mand" value="<?php if(!empty($user)) echo $user['email'];?>"></td>
<td width="33%">Telephone (optional)<br><input type="text" name="bill_telephone" class="mand" value="<?=$a?htmlspecialchars($a['telephone']):""?>"></td>
</tr>

</table>

</div>

<div style="border-top:1px solid #aaa;padding-top:10px;margin-top:5px;" align="right">

<div style="float:left">
<input type="checkbox" checked="checked" id="check18yrs"> I am atleast 18 years old and I agree to <a href="<?=site_url("terms")?>">Terms and Conditions</a> 
</div>

<input type="image" src="<?=base_url()?>images/continue.png">
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
trig_cart_update = function(){
	if(!cart_updated)
		return;
	$.fancybox.showActivity();
	location.reload(true);
}
</script>
<?php
