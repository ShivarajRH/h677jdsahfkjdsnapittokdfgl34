<?php $user=$this->session->userdata("fran_auser");?>
<div class="header" style="padding:20px 10px" align="left">
<div style="float:right;padding-right:20px;font-size:14px;">
Welcome <b><?=$user['username']?></b> <a href="<?=site_url("franchisee/logout")?>" class="signout">Logout</a>
</div>
<img src="<?=base_url()?>images/logo.png" style="float:left;">
<div style="float:left;margin-top:0px;margin-left:30px;">
<h3 style="margin:20px 0px 30px 50px;">FRANCHISEE ADMIN</h3>
<form action="<?=site_url("franchisee/search")?>" method="post" id="csrcform"><input name="q" type="text" class="inp srchbox" value="Search... (Emp NO, Users)"></form>
</div>
<div style="clear:both;padding-top:5px;">
<table class="hlinks" cellpadding="10">
<tr>
<td><a href="<?=site_url("franchisee/dashboard")?>">Dashboard</a></td>
<td><a href="<?=site_url("franchisee/transactions")?>">Account Statement</a></td>
<td><a href="<?=site_url("franchisee/deals")?>">Deals</a></td>
<td><a href="<?=site_url("franchisee/marks")?>">Mark up/downs</a></td>
<td><a href="<?=site_url("franchisee/orders")?>">Orders</a></td>
<td><a href="<?=site_url("franchisee/account")?>">My Account</a></td>
<td><a href="<?=site_url("franchisee/changepwd")?>">Change password</a></td>
<td><a href="<?=site_url("franchisee/contact")?>">Contact Admin</a></td>
</tr>
</table>
</div>
</div>
<style>
.srchbox{
color:#aaa;
width:350px;
}
</style>
<script>
$(function(){
	$("#csrcform input").val("Search deals...");
	$("#csrcform input").focus(function(){
		if($(this).val()=="Search deals...")
			$(this).val("").css("color","#000");
	}).blur(function(){
		if($(this).val()=="")
			$(this).val("Search deals...").css("color","#aaa");
	});
	$("#csrcform").submit(function(){
		if($("input",$(this)).val().length<3)
		{
			alert("Enter a minimum of 3 characters");return false;
		}
		return true;
	});
});
</script>
<?php 