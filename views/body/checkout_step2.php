<?php $user=$this->session->userdata("user");?>
<script type="text/javascript">
<!--
$(function(){
	$("#cartlink").attr("href","#cartdisabled");
	$("#cartlink").click(function(){$("#fancy_inner").css("height","80px");});
	$("input[name=payment]").change(function(){
		$("input[name=payment]").each(function(){
			if($(this).attr("checked")==true)
				$($(this).parent().parent()).css("background","#FFD700");
			else
				$($(this).parent().parent()).css("background","#fff");
		});
	}).each(function(){
		$(this).attr("checked",false);
		});
	$("input[name=sav_address]").change(function(){
		$("#s_adr").val($(this).val());
		$("input[name=sav_address]").each(function(){
			if($(this).attr("checked")==true)
				$($(this).parent()).css("background","#ADD8E6");
			else
				$($(this).parent()).css("background","#eee");
		});
	}).each(function(){$(this).attr("checked",false);});
	$("#s_addrf").attr("checked",true);
	$("textarea[name=address],textarea[name=bill_address]").focus(function(){
		$(this).css("height","90px").css("width","300px");
	}).css("height","90px").css("width","300px");
	$("input[name=billship]").change(function(){
		if($(this).attr("checked")==false)
			$("#billing").show();
		else
			$("#billing").hide();
	}).attr("checked",true);

	$("#checkoform").submit(function(){
		msg="";
		$("input.mand:not(:hidden),textarea.mand:not(:hidden)",$(this)).each(function(){
			if($(this).val().length==0)
				msg="<div>All fields are mandatory</div>";
		});
<?php if(!isset($user['aid']) && !$this->session->userdata("fran_auser")){?>
		if($("input[name=payment]:checked",$(this)).length==0)
			msg+="<div>Please select a payment method</div>";
<?php }?>
		if($("input[name=phone]:hidden",$(this)).length==0)
		{
		if(!is_naturalnonzero($("input[name=phone]:not(:hidden)",$(this)).val()))
				msg+="<div>Invalid Mobile number</div>";
		if(!is_naturalnonzero($("input[name=pincode]:not(:hidden)",$(this)).val()))
					msg+="<div>Invalid Pincode</div>";
		}
<?php if(isset($user['aid'])){?>
		if(!is_email($("input[name=buy_email]",$(this)).val()))
			msg+="<div>Invalid Buyer's email</div>";
        if(!is_mobile_strict($("input[name=buy_mobile]",$(this)).val()))
           	msg+="<div>Invalid Buyer's mobile number</div>";
<?php }?>
		if(msg!="")
		{
			$(".error").html(msg).show();
			return false;
		}
<?php if(isset($user['aid'])){?>		
		if(!confirm("Your current balance is Rs <?=sprintf("%.2f",$agent_balance/100)?>\nGrand total price : Rs <?=$payable?>\nYour Commission : Rs <?=$comm?>\nAmount to be paid : Rs <?=($payable-$comm)?>\nYour balance after transaction : Rs <?=sprintf("%.2f",($agent_balance-(($payable-$comm)*100))/100)?> \nAre you sure want to do this transaction?"))
			return false;
<?php }?>
		return true;
	});

	$("#shiptome").change(function(){
		if($(this).attr("checked")==true)
			$("#shipcont").hide();
		else
			$("#shipcont").show();
	});
	
});
//-->
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
<div style="padding: 0px;">
<form action="<?=site_url("processCheckout")?>" method="post" id="checkoform">
<div style="float: right; font-size: 14px; font-family: trebuchet ms;">You
are saving <b style="font-size: 18px; color: #E80021;">Rs <?=$save?> (<?=$per?>%)</b></div>
<div align="left" style="float: left; width: 650px; font-family: arial;">
<h2>Your Order Details</h2>
<style>
.orderdet td {
	border: 1px solid #aaa;
	padding: 5px;
	text-align: right;
	vertical-align: top;
	height: 60px;
	font-size: 13px;
	font-family: arial;
	color: #444;
}

.orderdet th {
	color: #444;
	font-size: 13px;
	background: #eee;
	padding: 5px;
	border: 1px solid #aaa;
}

.payment {
	-moz-border-radius: 5px;
	text-align: left;
	margin: 10px 10px 10px 20px;
	background: #fafafa;
	border: 1px solid #bbb;
	padding: 10px;
}

.addresstable {
	font-size: 13px;
	margin-top: 10px;
}

.addresstable td {
	padding: 2px 5px;
}

.addresstable input,.addresstable textarea {
	border: 1px solid #606060;
	padding: 3px;
}
.error{
color:red;
font-size:12px;
font-weight:bold;
padding:5px;
display:none;
background:#fafafa;
border:1px solid red;
margin:10px;
}
</style>
<div style="padding-top: 5px;">
<table width="100%" cellspacing="3" class="orderdet">
	<tr>
		<th>Item Name</th>
		<th>Save</th>
		<th>Price</th>
		<th>Qty</th>
		<th>Sub Total</th>
	</tr>
	<?php $total=0;foreach($items as $item){?>
	<tr>
		<td style="text-align: left; font-size: 14px; font-weight: bold; color: #E80021;"><?=$item['name']?>
<div style="color:#222;padding-top:10px;font-weight:normal;font-size:13px;">Ships in <b style="color:#e80021"><?=$item['shipsin']?></b> days</div>
		</td>
		<td>
<?php if($item['qty']>1){?>		<span style="font-size:9px;">Rs <?=($item['orgprice']-$item['price'])?> X <?=$item['qty']?></span><br><?php }?>
		Rs <?=($item['orgprice']-$item['price'])*$item['qty']?> (<?=ceil(($item['orgprice']-$item['price'])/$item['orgprice']*100)?>%)</td>
		<td>Rs <?=$item['price']?></td>
		<td>X <?=$item['qty']?></td>
		<td style="font-size: 15px; font-weight: bold;">Rs <?=$item['price']*$item['qty']?></td>
	</tr>
	<?php $total+=$item['price']*$item['qty']; }?>
	<tr>
		<td
			style="background: #eee; text-align: right; height: auto; color: black;">
			<b>Amount Payable</b></td>
		<td colspan="4"
			style="font-size: 15px; background: #eee; text-align: right; height: auto; color: black;">Rs
		<b><?=$total?></b></td>
	</tr>
</table>
</div>
<?php if(!isset($user['aid']) && !$this->session->userdata("fran_auser")){?>
<div style="padding-top: 15px;">
<h2>Shipping Address</h2>
<div class="error"></div>
<div align="center" id="shipcont" <?php if(!empty($addresses[0])){?>style="display:none"<?php }?>>
<table style="width: 500px" class="addresstable">
	<tr>
		<td width="120" align="right">Name :</td>
		<td align="left"><input class="mand" type="text" name="person" size="28"></td>
	</tr>
	<tr>
		<td align="right">Mobile Number :</td>
		<td align="left"><input class="mand" type="text" name="phone"></td>
	</tr>
	<tr>
		<td align="right" valign="top">Address :</td>
		<td align="left"><textarea class="mand" rows=1 cols="30" name="address"></textarea></td>
	</tr>
	<tr>
		<td align="right">City :</td>
		<td align="left"><input class="mand" type="text" name="city"></td>
	</tr>
	<tr>
		<td align="right">Pin Code :</td>
		<td align="left"><input class="mand" style="width: 70px;" type="text"
			name="pincode"></td>
	</tr>
</table>
</div>
<div id="shipaddr" style="padding:10px;">
<?php foreach($addresses[0] as $ai=>$adr){?>
<div style="margin:5px;font-size:13px;padding:5px;float:left;width:200px;border:1px solid #aaa;background:#eee;<?php if($ai==0){?>background:#ADD8E6;<?php }?>">
<input <?php if($ai==0) {?>id="s_addrf"<?php }?> type="radio" name="sav_address" style="float:right" value="<?=$adr['id']?>">
<div style="font-weight:bold;"><?=$adr['name']?></div>
<div><?=nl2br($adr['address'])?></div>
<div><?=$adr['city']?> <?=$adr['pincode']?></div>
<div><?=$adr['phone']?></div>
<div align="right"><a href="javascript:void(0)" style="color:blue" onclick='$("input",$(this).parent().parent()).attr("checked",true).change()'>use this address</a></div>
</div>
<?php }?>
<?php if(!empty($addresses[0])){?>
<div align="left" style="clear:left">
<a href="javascript:void(0)" style="color:blue;" onclick='$("#s_adr").val("0");$("#shipcont").show();$("#shipaddr").hide();'>Click to enter new address</a>
</div>
<input type="hidden" value="<?=$addresses[0][0]['id']?>" name="s_adrid" id="s_adr">
<?php }else{?>
<input type="hidden" value="0" name="s_adrid" id="s_adr">
<?php }?>
<div class="clear">&nbsp;</div>
</div>
</div>
<div align="center" style="padding-top:10px;">
<label><input name="billship" type="checkbox" checked> Billing address is same as shipping address</label>
</div>

<div style="padding-top: 15px;display:none;" id="billing">
<h2>Billing Address</h2>
<div align="center">
<table style="width: 500px" class="addresstable">
	<tr>
		<td width="120" align="right">Name :</td>
		<td align="left"><input class="mand" type="text" name="bill_person" size="30"></td>
	</tr>
	<tr>
		<td align="right">Mobile Number :</td>
		<td align="left"><input class="mand" type="text" name="bill_phone"></td>
	</tr>
	<tr>
		<td align="right" valign="top">Address :</td>
		<td align="left"><textarea class="mand" rows=1 cols="30" name="bill_address"></textarea></td>
	</tr>
	<tr>
		<td align="right">City :</td>
		<td align="left"><input class="mand" type="text" name="bill_city"></td>
	</tr>
	<tr>
		<td align="right">Pin Code :</td>
		<td align="left"><input class="mand" style="width: 70px;" type="text" name="bill_pincode"></td>
	</tr>
</table>
</div>
</div>

<?php }else{?>
<?php if(isset($user['aid'])){?>
<div style="float:left;font-size:13px;margin:5px;padding:5px;border:1px solid red;background:#eee;color:red;">
This transaction is done through your VIA deposit account. <br>Amount payable will be debited from your account.
</div>
<?php }else{?>
<div style="float:left;font-size:13px;margin:5px;padding:5px;border:1px solid red;background:#eee;color:red;">
This transaction is done through your ViaBazaar franchisee account
</div>
<?php }?>
<div style="clear:left;padding-top: 15px;">
<h2>Shipping Address</h2>
<div align="left" style="padding:10px;font-size:13px;">
<label><input type="checkbox" name="shiptome" id="shiptome" checked> Ship this product to my address</label> 
</div>
<div class="error"></div>
<div align="center" id="shipcont" style="display:none">
<table style="width: 500px" class="addresstable">
	<tr>
		<td width="120" align="right">Name :</td>
		<td align="left"><input class="mand" type="text" name="person" size="28"></td>
	</tr>
	<tr>
		<td align="right">Mobile Number :</td>
		<td align="left"><input class="mand" type="text" name="phone"></td>
	</tr>
	<tr>
		<td align="right" valign="top">Address :</td>
		<td align="left"><textarea class="mand" rows=1 cols="30" name="address"></textarea></td>
	</tr>
	<tr>
		<td align="right">City :</td>
		<td align="left"><input class="mand" type="text" name="city"></td>
	</tr>
	<tr>
		<td align="right">Pin Code :</td>
		<td align="left"><input class="mand" style="width: 70px;" type="text"
			name="pincode"></td>
	</tr>
</table>
</div>
</div>

<div style="padding:5px 0px;">
<h2>Buyer's Info</h2>
<div align="center">
<table width="500" class="addresstable">
<tr>
<td align="right">Email :</td><td><input class="mand" type="text" name="buy_email" value="<?=$user['email']?>"></td>
</tr>
<tr>
<td align="right">Mobile :</td><td><input class="mand" type="text" name="buy_mobile" value="<?=$user['mobile']?>"></td>
</tr>
</table>
</div>
</div>

<div style="padding-top: 15px;" id="billing">
<h2>Billing Address</h2>
<div align="center">
<?php $address=$addresses;?>
<table style="width: 500px" class="addresstable">
	<tr>
		<td width="120" align="right">Name :</td>
		<td align="left"><input class="mand" type="text" name="bill_person" size="30" value="<?php if(isset($address[1][0])) echo $address[1][0]['name'];?>"></td>
	</tr>
	<tr>
		<td align="right">Mobile Number :</td>
		<td align="left"><input class="mand" type="text" name="bill_phone" value="<?php if(isset($address[1][0])) echo $address[1][0]['phone'];?>"></td>
	</tr>
	<tr>
		<td align="right" valign="top">Address :</td>
		<td align="left"><textarea class="mand" rows=1 cols="30" name="bill_address"><?php if(isset($address[1][0])) echo $address[1][0]['address'];?></textarea></td>
	</tr>
	<tr>
		<td align="right">City :</td>
		<td align="left"><input class="mand" type="text" name="bill_city" value="<?php if(isset($address[1][0])) echo $address[1][0]['city'];?>"></td>
	</tr>
	<tr>
		<td align="right">Pin Code :</td>
		<td align="left"><input class="mand" style="width: 70px;" type="text" name="bill_pincode" value="<?php if(isset($address[1][0])) echo $address[1][0]['pincode'];?>"></td>
	</tr>
</table>
</div>
</div>

<?php }?>

<div style="text-align:right">
<input type="image" src="<?=base_url()?>images/placeorder.gif">
</div>

</div>

<div style="margin-top: 10px; width: 280px; font-size: 15px; float: right; background: #eee; border: 1px solid #aaa;">
<?php if(!isset($user['aid']) && !$this->session->userdata("fran_auser")){?>
<div class="payment" align="left"><label><input type="radio"
	name="payment" value="cc"> Pay by <b>Credit Card</b></label></div>
<div class="payment" align="left"><label><input type="radio"
	name="payment" value="netbank"> Pay by <b>Net Banking</b></label>
<div style="text-align: left; padding-top: 5px;"><select>
	<option value="0">select</option>
	<option value="sbi">State Bank of India</option>
</select></div>
</div>
<div class="payment" align="left"><label><input type="radio"
	name="payment" value="dc"> Pay by <b>Debit Card</b></label></div>
<?php }elseif(isset($user['aid'])){?>
<div align="center" style="padding:10px;">
<img src="<?=base_url()?>images/via.png" style="float:left"> <div style="padding-left:5px;font-size:18px;font-weight:bold;padding-top:5px;float:left;">Agent</div>
<div style="clear:both;font-weight:bold;color:red;padding:15px;" align="center">
<span style="-moz-border-radius:5px;background:#fafafa;padding:7px;border:1px solid #aaa;"><?=$user['name']?></span>
</div>
<div style="padding:10px;padding-left:0px;font-size:13px;" align="right">VIA Deposit Balance : Rs <b style="font-size:18px;"><?=sprintf("%.2f",$agent_balance/100)?></b></div>
</div>
<?php }else{?>
<div align="center" style="padding:10px;">
<img src="<?=base_url()?>images/via.png" style="float:left"> <div style="padding-left:5px;font-size:18px;font-weight:bold;padding-top:5px;float:left;">Bazaar Franchisee</div>
<div style="clear:both;font-weight:bold;color:red;padding:15px;" align="center">
<span style="-moz-border-radius:5px;background:#fafafa;padding:7px;border:1px solid #aaa;"><?=$user['name']?></span>
</div>
<div style="padding:10px;padding-left:0px;font-size:13px;" align="right">Franchisee A/c Balance : Rs <b style="font-size:18px;"><?=$user['balance']?></b></div>
</div>
<?php }?>
</div>
</form>
</div>
<?php
