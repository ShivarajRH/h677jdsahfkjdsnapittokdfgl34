<div class="container">
<h2>Update  PNH Member Details </h2>

<form method="post" id="pnh_editm_form" autocomplete="off">
<?php
	list($m_dob_y,$m_dob_m,$m_dob_d) = explode('-',$mem_det['dob']);
	list($m_wed_y,$m_wed_m,$m_wed_d) = explode('-',$mem_det['anniversary']);
	list($m_c1_dob_y,$m_c1_dob_m,$m_c1_dob_d) = explode('-',$mem_det['child1_dob']);
	list($m_c2_dob_y,$m_c2_dob_m,$m_c2_dob_d) = explode('-',$mem_det['child2_dob']);
?>

Member ID : <input maxlength="8" type="text" readonly="" class="mid_inp" name="mid" value="<?php echo $mem_det['pnh_member_id'];?>" size=12 style="padding:5px;font-size:120%">
<div style="display:inline-block;"><div id="mem_fran"></div><div class="clear"></div></div>
<br><br>

<input type="hidden" value="<?php echo $mem_det['user_id'];?>" name="userid" >

<div style="background:#eee;padding:5px;font-weight:bold;">Personal Data</div>

<table cellpadding=3>
<tr><td class="label">Gender</td><td><input checked="checked" type="radio" name="gender" value="0" <?php echo set_radio('gender',0,($mem_det['user_id']==0)?true:false);?> >Male <input type="radio" name="gender" value="1" <?php echo set_radio('gender',1,($mem_det['gender']==1)?true:false);?> >Female  </td></tr>
<tr><td class="label">Salutation</td><td><input checked="checked" type="radio" <?php echo set_radio('salute',0,($mem_det['salute']==0)?true:false);?> name="salute" value="0">Mr <input type="radio" name="salute" value="1" <?php echo set_radio('salute',1,($mem_det['salute']==1)?true:false);?> >Mrs <input type="radio" name="salute" value="2" <?php echo set_radio('salute',2,($mem_det['salute']==2)?true:false);?> >Ms</td></tr>
<tr><td class="label">First Name <b class="red_star">*</b></td><td><input class="inp mand" type="text" value="<?php echo set_value('fname',$mem_det['first_name']);?>" name="fname" size=30></td></tr>
<tr><td class="label">Last Name <b class="red_star">*</b></td><td><input class="inp mand" type="text" name="lname" size=30 value="<?php echo set_value('lname',$mem_det['last_name']);?>" ></td></tr>
<tr><td class="label">DOB</td><td><input class="inp" type="text" name="dob_d" maxlength="2" size=2 value="<?php echo set_value('dob_d',$m_dob_d);?>" ><input class="inp" type="text" name="dob_m" maxlength="2" size=2 value="<?php echo set_value('dob_m',$m_dob_m);?>" ><input class="inp" maxlength="4" type="text" name="dob_y" size=4 value="<?php echo set_value('dob_y',$m_dob_y);?>"> (dd/mm/yyyy)</td></tr>
<tr><td class="label">Address <b class="red_star">*</b></td><td><textarea class="inp mand" rows=5 cols=90 name="address"><?php echo $mem_det['address'];?></textarea></td></tr>
<tr><td class="label">City <b class="red_star">*</b></td><td><input class="inp mand" type="text" name="city" size=30 value="<?php echo $mem_det['city'];?>" ></td></tr>
<tr><td class="label">Pin Code</td><td><input class="inp" type="text" name="pincode" size=10 value="<?php echo $mem_det['pincode'];?>" ></td></tr>
<tr><td class="label">Mobile <b class="red_star">*</b></td><td><input class="inp mand mob_inp" maxlength="10" type="text" name="mobile" size=20 value="<?php echo $mem_det['mobile'];?>" ><span id="mob_error"></span></td></tr>
<tr><td class="label">Mobile Network <b class="red_star">*</b></td><td><input name="mob_nk" id="mob_nk" class="inp mand mob_nk" value="<?php echo $mem_det['mobile_network'];?>"></td></tr>
<tr><td class="label">Email</td><td><input class="inp email_inp" type="text" name="email" size=50 value="<?php echo $mem_det['email'];?>" ><span id="email_error" value="<?php echo $mem_det['email'];?>" ></span></td></tr>
</table>


<div style="margin-top:10px;background:#eee;padding:5px;font-weight:bold;">Help us to know you better!</div>

<table cellpadding=0>
<tr>
<td width="50%">
<table cellpadding=3>
<tr><td style="height:35px;" class="label">Marital Status</td><td><input checked="checked" type="radio" name="marital" value="1" <?php echo set_radio('marital',1,($mem_det['marital_status']==1)?true:false);?> >Married <input type="radio" name="marital" value="0" <?php echo set_radio('marital',0,($mem_det['marital_status']==0)?true:false);?> >Single <input type="radio" name="marital" value="2" <?php echo set_radio('marital',2,($mem_det['marital_status']==2)?true:false);?> >Other</td></tr>
<tr><td class="label">Spouse Name</td><td><input class="inp" type="text" name="spouse" size=30 value="<?php echo set_value('spouse',$mem_det['spouse_name']);?>" ></td></tr>
<tr><td class="label">Child's Name</td><td><input class="inp" type="text" name="cname1" size=30 value="<?php echo set_value('cname1',$mem_det['child1_name']);?>" ></td></tr>
<tr><td class="label">Child's Name</td><td><input class="inp" type="text" name="cname2" size=30 value="<?php echo set_value('cname2',$mem_det['child2_name']);?>" ></td></tr>
</table>
</td>
<td width="50%">
<table cellpadding=3>
<tr><td class="label">Wedding Anniversary</td><td><input type="text" maxlength="2" name="dow_d" size=2 value="<?php echo set_value('dow_d',$m_wed_d);?>" ><input type="text" name="dow_m" maxlength="2" size=2 value="<?php echo set_value('dow_m',$m_wed_m);?>" ><input maxlength="4" type="text" name="dow_y" size=4 value="<?php echo set_value('dow_y',$m_wed_y);?>" > (dd/mm/yyyy)</td></tr>
<tr><td class="label">DOB</td><td><input maxlength="2" type="text" name="dobc1_d" size=2 value="<?php echo set_value('dobc1_d',$m_c1_dob_d);?>" ><input type="text" name="dobc1_m" size=2 maxlength="2" value="<?php echo set_value('dobc1_m',$m_c1_dob_m);?>"  ><input type="text" maxlength="4" name="dobc1_y" size=4 value="<?php echo set_value('dobc1_y',$m_c1_dob_y);?>"  > (dd/mm/yyyy)</td></tr>
<tr><td class="label">DOB</td><td><input maxlength="2" type="text" name="dobc2_d" size=2 value="<?php echo set_value('dobc2_d',$m_c2_dob_d);?>" ><input type="text" name="dobc2_m" size=2 maxlength="2" value="<?php echo set_value('dobc2_m',$m_c2_dob_m);?>"  ><input type="text" maxlength="4" name="dobc2_y" size=4 value="<?php echo set_value('dobc2_y',$m_c2_dob_y);?>"  > (dd/mm/yyyy)</td></tr>
</table>
</td>
</tr>
</table>

<table cellpadding=5>
<tr><td class="label">Profession</td><td>
<?php foreach(array("Corporate Employee","Self-Employed","Govt. Employee","Homemaker","Student","Not Employed","Other") as $i=>$p){?>
<label style="padding:5px;margin:0px 3px;"><input <?php echo set_radio('profession',$p,($mem_det['profession']==$p)?true:false);?> type="radio" name="profession" value="<?=$p?>"><?=$p?></label>
<?php if(($i+1)%3==0) echo '<br>'; }?>
</td></tr>
<tr>
<td class="label">Monthly Shopping Expense of your Household</td>
<td>
<label style="padding:5px;margin:0px 3px;"><input type="radio" name="expense" value="0"  <?php echo set_radio('expense',0,($mem_det['expense']==0)?true:false);?>  >&lt; Rs. 2000</label>
<label style="padding:5px;margin:0px 3px;"><input type="radio" name="expense" value="1" <?php echo set_radio('expense',1,($mem_det['expense']==1)?true:false);?>   >Rs 2001 - Rs 5000</label>
<label style="padding:5px;margin:0px 3px;"><input type="radio" name="expense" value="2" <?php echo set_radio('expense',2,($mem_det['expense']==2)?true:false);?>   >Rs 5001 - Rs 10000</label>
<label style="padding:5px;margin:0px 3px;"><input type="radio" name="expense" value="3" <?php echo set_radio('expense',3,($mem_det['expense']==3)?true:false);?>   >&gt; Rs. 10000</label>
</td>
</tr>
</table>

<div style="margin:10px 0px">
<input type="submit" value="Update" style="padding:5px;font-size:18px;">
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
		$.post("<?=site_url("admin/jx_pnh_checkmememail")?>",{email:e,mid:$(".mid_inp").val()},function(data){
			if(data=="1")
			{
				emailok=1;
				$("#email_error").html("Ok").css("color","green");
			}
			else
				$("#email_error").html("Email already exists").css("color","red");
		});
	}).change();
	$("#pnh_editm_form").submit(function(){
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
width:128px;
}
</style>
<?php
