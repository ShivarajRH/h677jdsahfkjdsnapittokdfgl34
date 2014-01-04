<script>
function showchangeform(){
	$("#profileformcontain").show();
}
function showpass(){
	$(".passwordlink").hide();
	$(".password").show();
}
function hidechangeform(){
	$("#profileformcontain").hide();
}
function editprofile(){
	if($("#explo_password").val().length<5 && $("#explo_password").val().length!=0)
	{
		alert("Password should be atleast 5 characters");
		return false;
	}
	if($("#explo_password").val()!=$("#explo_cpassword").val())
	{
		alert("Passwords are not same");
		return false;
	}
	data=$("#profileform").serialize();
	$("#loader").show();
	$("#profileform input").attr("disabled",true);
	$.post("<?=site_url("jx/editprofile")?>",data,function(resp){
			$("#profileform input").attr("disabled",false);
			$("#loader").fadeOut();
			if(resp=="1")
			{
				$("#profileformcontain").hide();
				$("#name_disp").text($("#explo_name").val());
				if($("#explo_mobile").val().length!=0)
					$("#mobile_disp").text($("#explo_mobile").val());
				else
					$("#mobile_disp").text("not entered");
			}
			else
			{
				alert(resp);
			}
		});
	return false;
}
$(function(){
	$("#profileform").submit(editprofile);
	$("#loader").hide();
	$("a.fanblink").fancybox({
		'zoomOpacity'			: true,
		'zoomSpeedIn'			: 300,
		'zoomSpeedOut'			: 200,
		'hideOnContentClick'    : false,
		'callbackOnStart'			: resetHeight
//		'easingIn'				: 'easeOutBack',
//		'easingOut'				: 'easeInBack'
	});
});
</script>

<style>
.profile p{
margin:3px;
}
#profileformcontain{
display:none;
}
.myecontain{
border:1px solid #ccc;background:#eee;width:350px;padding:10px;padding-left:30px;font-family:arial;color:#654;font-size:13px;padding-bottom:20px;
margin-bottom:15px;
}
.referal .list span{
padding:0px 25px;
font-weight:normal;
}
.editprofile input{
width:150px;
}
</style>
<div class="headingtext">My KSale <img id="loader" src="<?=base_url()?>images/loader.gif"></img></div>
<div style="width:100%;" align="left">
<div style="padding-top:10px;padding-left:30px;">

<div id="profileformcontain" class="editprofile myecontain" style="float:right;">
<div style="font-weight:bold;font-family:'trebuchet ms';font-size:18px;color:#ff9900;margin-left:-15px;padding-bottom:10px;">Edit Profile <a href="javascript:void(0)" style="text-decoration:none;margin-top:-5px;margin-right:-3px;float:right;color:#34f;" onclick='hidechangeform()'>X</a></div>
<form id="profileform" method="post" action="asdasdasd">
<div style="width:300px;">
<div style="margin-top:0px;"><input style="float:right" type="text" readonly name="explo_email" value="<?=$user['email']?>">Email : </div>
<div style="clear:both;margin-top:17px;"><input style="float:right" type="text" id="explo_name" name="explo_name" value="<?=$user['name']?>">Name : </div>
<div style="clear:both;margin-top:17px;"><span style="float:right"><input class="password" type="password" id="explo_password" name="explo_password" style="display:none;"><a class="passwordlink" href="javascript:void(0)" style="color:#35f;" onclick='showpass()'>Change password</a></span>Password : </div>
<div class="password" style="clear:both;margin-top:17px;display:none;"><input style="float:right" id="explo_cpassword" type="password" name="explo_cpassword">Confirm Password : </div>
<div style="clear:both;margin-top:17px;"><input style="float:right" type="text" id="explo_mobile" name="explo_mobile" value="<?php if($userfdb['mobile']!=0) echo $userfdb['mobile'];?>">Mobile : </div>
<div style="clear:both;margin-top:25px;margin-right:-40px;" align="right"><input type="submit" style="font-family:verdana;background:#32f;padding:3px 5px;color:#efefef;font-weight:bold;font-size:12px;width:auto;" value="Update"></div>
</div>
</form>
</div>

<div class="profile myecontain">
<div style="font-weight:bold;font-family:'trebuchet ms';font-size:18px;color:#ff9900;margin-left:-15px;padding-bottom:10px;">My Profile <span style="float:right"> <a href="javascript:void(0)" onclick='showchangeform()' style="color:#43f;font-size:12px;">Edit</a></span></div>
<p>Email : <span style="font-size:15px;"><b><i><?=$userfdb['email']?></i></b></span></p>
<p>Name : <span style="font-size:15px;"><b><i id="name_disp"><?=$userfdb['name']?></i></b></span></p>
<p>Password : <span style="font-size:15px;"><b><i>#########</i></b></span>
</p>
<p>Mobile : <span style="font-size:15px;"><b><i id="mobile_disp"><?php if($userfdb['mobile']==0) echo "</b>not entered"; else echo $userfdb['mobile'];?></i></b></span></p>
</div>

<div class="myecontain" style="float:left;width:400px;">
<div style="font-weight:bold;font-family:'trebuchet ms';font-size:18px;color:#ff9900;margin-left:-15px;padding-bottom:10px;">Recently viewed sales</div>
<?php if(!isset($bookmark)) echo '<div style="font-size:23px;color:#444;">None</div>'; ?>
</div>

<div class="myecontain" style="float:left;width:400px;margin-left:20px;font-size:14px;">
<div style="font-weight:bold;font-family:'trebuchet ms';font-size:18px;color:#ff9900;margin-left:-15px;padding-bottom:10px;">My Saved Carts</div>
<?php if(count($savedcarts)==0) echo '<div style="font-size:23px;color:#444;">None</div>'; ?>
<?php foreach($savedcarts as $cart) {?>
<div style="padding:2px;"><b><?=$cart['name']?></b><a style="color:#00f;float:right;font-size:11px;"href="<?=site_url("jx/viewsavedcart/{$cart['cartid']}")?>" class="fanblink">View Cart</a></div>
<?php }?>
</div>

<div class="myecontain" style="clear:both;width:860px;">
<div style="font-weight:bold;font-family:'trebuchet ms';font-size:18px;color:#ff9900;margin-left:-15px;padding-bottom:10px;">Purchased sales <span style="font-size:11px;">(<?php if($orders) echo count($orders); else echo "0"?>)</span></div>
<?php if(count($orders)>0){?>
<style>
th{
background:#777;
color:#fff;
}
td{
padding:5px 10px;
}
</style>
<table width="100%" style="background:#ddd;padding:0px;padding-bottom:5px;" cellpadding="5" cellspacing="0">
<tr>
<th>Item Name</th>
<th>Brand Name</th>
<th>Quantity</th>
<th>Total Price</th>
<th>Status</th>
</tr>
<?php foreach($orders as $order){?>
<tr>
<td><?=$order['name']?></td>
<td><?=$order['brandname']?></td>
<td><?=$order['quantity']?></td>
<td><?=$order['price']*$order['quantity']?></td>
<td>
<?php if($order['status']==0) echo "Pending"; else echo "Processed"?>
</td>
</tr>
<?php }?>
</table>
<?php }else{?>
<div style="font-size:23px;color:#444;">None</div>
<?php }?>
</div>
<div class="referal myecontain" style="clear:both;width:600px;">
<div style="font-weight:bold;font-family:'trebuchet ms';font-size:18px;color:#ff9900;margin-left:-15px;padding-bottom:10px;">My Referals <span style="font-size:11px;">(<?php if($referals) echo count($referals); else echo "0"?>)</span><span style="float:right"> <a href="<?=site_url("invite")?>" style="color:#43f;font-size:12px;">Invite friends</a></span></div>
<?php if(isset($referals[0])) foreach ($referals as $referal){?>
<div class="list" style="margin:5px;font-weight:bold;"><?=$referal['name']?><span><?=$referal['email']?></span></div>
<?php }?>
<?php if($referals==false) echo '<div style="font-size:23px;color:#444;">None</div><a style="color:#34f" href="'.site_url("invite").'">Start inviting friends</a>'; ?>
</div>

</div>
</div>
<br style="clear: both;">