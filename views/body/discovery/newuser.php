<div class="container" style="min-height:200px;">
<div class="info">
	<h3>Welcome!</h3>
	<div style="padding:10px">
		<h5>Please choose a username</h5>
		<form method="post" id="bua_form">
			<table cellpadding=5>
				<tr>
					<td>User Name :</td><td><input type="text" name="username" <?php if(isset($username)){?>value="<?=$username?>"<?php }?> class="inp" size=40></td><td><img id="b_ua_load" src="<?=IMAGES_URL?>loading.gif"></td>
				</tr>
				<tr id="b_useravail">
				</tr>
				<tr>
					<td></td><td><input class="submit" type="submit" value="Create my boader profile" disabled="disabled"></td>
				</tr>
			</table>
		</form>
	</div>
</div>
</div>
<script>
function checkua()
{
	if(!is_required($("#bua_form .inp").val()) || ou==$("#bua_form .inp").val())
		return;
	$("#bua_form .submit").attr("disabled",true);
	$("#b_useravail").hide();
	$("#b_ua_load").show();
	$.post('<?=site_url("discovery/jx_checkua")?>','username='+$("#bua_form .inp").val(),function(resp){
		resps="Error"
		if(resp=="len")
			resps='<span style="color:red">Please enter atleast 5 characters</span>';
		if(resp=="fmt")
			resps='<span style="color:red">Username can contain only alphabets, numbers and underscore</span>';
		if(resp=="nop")
			resps='<span style="color:red">Oops! someone taken it..</span>';
		if(resp=="ava")
		{
			resps='<span style="color:green">Ok! you got that!!</span>';
			$("#bua_form .submit").attr("disabled",false);
		}
		$("#b_ua_load").hide();
		$("#b_useravail").show().html('<td></td><td colspan=2>'+resps+'</td>');
	});
	ou=$("#bua_form .inp").val();
}
var ou="";
var bua_timer;
$(function(){
	$("#bua_form .inp").keypress(function(){
		window.clearTimeout(bua_timer);
		bua_timer=window.setTimeout('checkua()',700);
	});
	$("#b_ua_load").hide();
<?php if(isset($username)){?>
	$("#bua_form .inp").keypress();
<?php }?>
});
</script>
<style>
#b_useravail{
font-size:80%;
}
</style>
<?php
