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

<div style="background:#43B9D6;padding-bottom:5px;" align="center">

<div class="container">


<div align="left" style="padding-top:10px;margin:5px;">

<div style="border:1px solid #aaa;-moz-border-radius:10px;padding:">
<div style="-moz-border-radius:10px 10px 0px 0px;background:url(<?=base_url()?>images/checkout_bg.png);padding:10px;font-size:18px;font-weight:bold;">CheckOut</div>
<table width="100%" cellspacing=0 cellpadding=0 style="background:#fff">
<tr>
<td>

<form action="<?=site_url("checkout/step3")?>" method="post" id="c2form">

<div style="padding:5px">

<div style="border-bottom:1px solid #aaa;font-size:110%;"><b>SHIPPING DETAILS</b></div>

<table width="100%" class="shipdet" cellspacing=5>
<tr>
<td width="33%">Name<span style="color:red">*</span><br><input type="text" name="person" class="mand" value="<?=$user?htmlspecialchars($user['name']):""?>"></td>
</tr>
<tr>
</tr>
<td colspan="2">Address<span style="color:red">*</span><br><textarea name="address" class="mand"><?=$a?$a['address']:""?></textarea></td>
<tr><td width="33%">Landmark<span style="color:red">*</span><br><input type="text"  name="landmark" class="mand" value="<?=$a?htmlspecialchars($a['landmark']):""?>"></td></tr>
<tr><td width="33%">City<span style="color:red">*</span><br>
<?php if(!isset($cities)){?>
<input type="text"  name="city" class="mand" value="<?=$a?htmlspecialchars($a['city']):""?>">
<?php }else{?>
<select name="city">
<?php foreach($cities as $c){?><option value="<?=$c?>" <?=($a && $a['city']==$c)?"checked":""?>><?=$c?></option>
<?php }?>
</select>
<?php }?>
</td></tr>
<tr><td width="33%">State<span style="color:red">*</span><br><input type="text" name="state" class="mand" value="<?=$a?htmlspecialchars($a['state']):""?>"></td></tr>
<tr><td width="33%">Pincode<span style="color:red">*</span><br><input type="text" maxlength="6" name="pincode" class="mand" value="<?=$a?htmlspecialchars($a['pincode']):""?>"></td></tr>
<tr><td width="33%">Mobile<span style="color:red">*</span><br><input type="text" name="mobile" class="mand" value="<?php if(!empty($user)) echo $user['mobile'];?>"></td></tr>
<tr><td width="33%">Telephone<br><input type="text" name="telephone" value="<?=$a?htmlspecialchars($a['telephone']):""?>"></td></tr>
<tr><td width="33%">Email<span style="color:red">*</span><br><input type="text" name="email" class="mand" value="<?php if(!empty($user)) echo $user['email'];?>"></td></tr>
</table>

<div style="padding:5px;">
<input type="checkbox" id="shipbillcheck" name="shipbillcheck">Billing address is same as shipping address
</div>

<div id="shippingaddr" style="display:none;">

<div style="border-bottom:1px solid #aaa;font-size:110%;"><b>BILLING DETAILS</b></div>

<table width="100%" class="shipdet" cellspacing=5>
<tr>
<td width="33%">Name<br><input type="text" name="bill_person" class="mand" value="<?=$user?htmlspecialchars($user['name']):""?>"></td>
</tr>
<tr>
<td colspan="2">Address<br><textarea name="bill_address" class="mand"><?=$a?($a['city']):""?></textarea></td>
</tr>
<tr><td width="33%">Landmark<br><input type="text"  name="bill_landmark" class="mand" value="<?=$a?htmlspecialchars($a['landmark']):""?>"></td></tr>
<tr>
<td width="33%">City<br><input type="text"  name="bill_city" class="mand" value="<?=$a?htmlspecialchars($a['city']):""?>"></td>
</tr>
<tr>
<td width="33%">State<br><input type="text" name="bill_state" class="mand" value="<?=$a?htmlspecialchars($a['state']):""?>"></td>
</tr>
<tr>
<td width="33%">Pincode<br><input type="text" name="bill_pincode" maxlength="6" class="mand" value="<?=$a?htmlspecialchars($a['pincode']):""?>"></td>
</tr>
<tr><td width="33%">Mobile<br><input type="text" name="bill_mobile" value="<?php if(!empty($user)) echo $user['mobile'];?>"></td></tr>
<tr><td width="33%">Telephone<br><input type="text" name="bill_telephone" class="mand" value="<?=$a?htmlspecialchars($a['telephone']):""?>"></td></tr>
<tr><td width="33%">Email<br><input type="text" name="bill_email" class="mand" value="<?php if(!empty($user)) echo $user['email'];?>"></td></tr>

</table>

</div>

<div style="border-top:1px solid #aaa;padding-top:10px;margin-top:5px;" align="right">
<input type="image" src="<?=base_url()?>images/continue_m.png">
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
	$("#shipbillcheck").change(function(){
			$("#shippingaddr").toggle();
	}).attr("checked",true);
	$("#c2form").submit(function(){
		ef=true;
		$(".mand:not(:hidden)").each(function(){
			if($(this).val()=="")
			{
				ef=false;
				alert("All Fields mandatory");
				return false;
			}
		});
		if(ef)
		{
			if(!is_email($("input[name=email]").val()))
			{
				ef=false;
				alert("Please enter a valid email");
			}
		}
		if(ef)
		{
			if(!is_mobile($("input[name=mobile]").val()))
			{
				ef=false;
				alert("Please enter a valid Mobile Number");
			}
		}
		if(ef)
		{
			if(!is_numeric($("input[name=pincode]").val()))
			{
				ef=false;
				alert("Please enter a valid Pincode");
			}
		}
		return ef;
	});
});
</script>
<style>
.shipdet input, .shipdet textarea{
	width:90%;
}
</style>
<?php
