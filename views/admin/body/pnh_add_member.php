<div class="container">
<h2>Add Member</h2>

<form method="post" id="pnh_addm_form" autocomplete="off">


Enter Member ID : <input maxlength="8" type="text" class="mid_inp" name="mid" size=12 style="padding:5px;font-size:120%">
<div style="display:inline-block;"><div id="mem_fran"></div><div class="clear"></div></div>
<br><br>

<div style="background:#eee;padding:5px;font-weight:bold;">Personal Data</div>

<table cellpadding=3>
<tr><td class="label">Gender</td><td><input checked="checked" type="radio" name="gender" value="0">Male <input type="radio" name="gender" value="1">Female  </td></tr>
<tr><td class="label">Salutation</td><td><input checked="checked" type="radio" name="salute" value="0">Mr <input type="radio" name="salute" value="1">Mrs <input type="radio" name="salute" value="2">Ms</td></tr>
<tr><td class="label">First Name</td><td><input class="inp mand" type="text" name="fname" size=30></td></tr>
<tr><td class="label">Last Name</td><td><input class="inp mand" type="text" name="lname" size=30></td></tr>
<tr><td class="label">DOB</td><td><input class="inp" type="text" name="dob_d" maxlength="2" size=2><input class="inp" type="text" name="dob_m" maxlength="2" size=2><input class="inp" maxlength="4" type="text" name="dob_y" size=4> (dd/mm/yyyy)</td></tr>
<tr><td class="label">Address</td><td><textarea class="inp mand" rows=5 cols=90 name="address"></textarea></td></tr>
<tr><td class="label">City</td><td><input class="inp mand" type="text" name="city" size=30></td></tr>
<tr><td class="label">Pin Code</td><td><input class="inp" type="text" name="pincode" size=10></td></tr>
<tr><td class="label">Mobile</td><td><input class="inp mand mob_inp" maxlength="10" type="text" name="mobile" size=20><span id="mob_error"></span></td></tr>
<tr><td class="label">Email</td><td><input class="inp email_inp" type="text" name="email" size=50><span id="email_error"></span></td></tr>
</table>


<div style="margin-top:10px;background:#eee;padding:5px;font-weight:bold;">Help us to know you better!</div>

<table cellpadding=0>
<tr>
<td width="50%">
<table cellpadding=3>
<tr><td style="height:35px;" class="label">Marital Status</td><td><input checked="checked" type="radio" name="marital" value="1">Married <input type="radio" name="marital" value="0">Single <input type="radio" name="marital" value="2">Other</td></tr>
<tr><td class="label">Spouse Name</td><td><input class="inp" type="text" name="spouse" size=30></td></tr>
<tr><td class="label">Child's Name</td><td><input class="inp" type="text" name="cname1" size=30></td></tr>
<tr><td class="label">Child's Name</td><td><input class="inp" type="text" name="cname2" size=30></td></tr>
</table>
</td>
<td width="50%">
<table cellpadding=3>
<tr><td class="label">Wedding Anniversary</td><td><input type="text" maxlength="2" name="dow_d" size=2><input type="text" name="dow_m" maxlength="2" size=2><input maxlength="4" type="text" name="dow_y" size=4> (dd/mm/yyyy)</td></tr>
<tr><td class="label">DOB</td><td><input maxlength="2" type="text" name="dobc1_d" size=2><input type="text" name="dobc1_m" size=2 maxlength="2"><input type="text" maxlength="4" name="dobc1_y" size=4> (dd/mm/yyyy)</td></tr>
<tr><td class="label">DOB</td><td><input maxlength="2" type="text" name="dobc2_d" size=2><input type="text" name="dobc2_m" size=2 maxlength="2"><input type="text" maxlength="4" name="dobc2_y" size=4> (dd/mm/yyyy)</td></tr>
</table>
</td>
</tr>
</table>

<table cellpadding=5>
<tr><td class="label">Profession</td><td>
<?php foreach(array("Corporate Employee","Self-Employed","Govt. Employee","Homemaker","Student","Not Employed","Other") as $i=>$p){?>
<label style="padding:5px;margin:0px 3px;"><input <?php if($i==0){?>checked="checked"<?php }?> type="radio" name="profession" value="<?=$p?>"><?=$p?></label>
<?php if(($i+1)%3==0) echo '<br>'; }?>
</td></tr>
<tr>
<td class="label">Monthly Shopping Expense of your Household</td>
<td>
<label style="padding:5px;margin:0px 3px;"><input type="radio" name="expense" value="0" checked="checked">&lt; Rs. 2000</label>
<label style="padding:5px;margin:0px 3px;"><input type="radio" name="expense" value="1">Rs 2001 - Rs 5000</label>
<label style="padding:5px;margin:0px 3px;"><input type="radio" name="expense" value="2">Rs 5001 - Rs 10000</label>
<label style="padding:5px;margin:0px 3px;"><input type="radio" name="expense" value="3">&gt; Rs. 10000</label>
</td>
</tr>
</table>

<div style="margin:10px 0px">
<input type="submit" value="Add member" style="padding:5px;font-size:18px;">
</div>

</form>

</div>

<script>
var mobok=0;
var emailok=0;
$(function(){
	$(".mid_inp").change(function(){
		$.post("<?=site_url("admin/jx_pnh_getmid")?>",{mid:$(this).val()},function(data){
			$("#mem_fran").html(data).show();
		});
	});
	$(".mob_inp").change(function(){
		mobok=0;
		m=$(this).val();
		if(!is_mobile(m))
		{
			$("#mob_error").html("Invalid mobile").css("color","red");
			return;
		}
		$.post("<?=site_url("admin/jx_pnh_checkmemmob")?>",{mob:m,mid:$(".mid_inp").val()},function(data){
			if(data=="1")
			{
				mobok=1;
				$("#mob_error").html("Ok").css("color","green");
			}
			else
				$("#mob_error").html("Mobile number already exists").css("color","red");
		});
	}).change();
	$(".email_inp").change(function(){
		emailok=0;
		e=$(this).val();
		if(e.length==0)
		{
			emailok=1;
			return;
		}
		if(!is_email(e))
		{
			$("#email_error").html("Invalid email").css("color","red");
			return;
		}
		$.post("<?=site_url("admin/jx_pnh_checkmememail")?>",{email:e},function(data){
			if(data=="1")
			{
				emailok=1;
				$("#email_error").html("Ok").css("color","green");
			}
			else
				$("#email_error").html("Email already exists").css("color","red");
		});
	}).change();;
	$("#pnh_addm_form").submit(function(){
		if(mobok==0 || emailok==0)
		{
			alert("Please check mobile number and email");
			return false;
		}
		f=true;
		$(".mand",$(this)).each(function(){
			if($(this).val().length==0)
			{
				alert($(".label",$(this).parents("tr").get(0)).text()+" is missing");
				f=false;
				return false;
			}
		});
		if(f)
		{
			d=parseInt($("input[name=dob_d]",$(this)).val());
			m=parseInt($("input[name=dob_m]",$(this)).val());
			y=parseInt($("input[name=dob_y]",$(this)).val());
//			if(isNaN(d)||isNaN(m)||isNaN(y)||d>31||m>12||y>2011)
//			{
//				alert("Please enter valid DOB");
//				return false;
//			}
			if(!is_mobile($("input[name=mobile]",$(this)).val()))
			{
				alert("Please enter valid mobile");
				return false;
			}
			if($("input[name=email]",$(this)).val().length!=0 && !is_email($("input[name=email]",$(this)).val()))
			{
				alert("Please enter valid email");
				return false;
			}
		}
		return f;
	});
});
</script>

<style>
#mem_fran{
margin:5px 0px;
background:#eee;
padding:5px;
min-width:400px;
font-size:110%;
font-weight:bold;
display: none;
}
.label{
font-weight:bold;
width:100px;
}
</style>
<?php
