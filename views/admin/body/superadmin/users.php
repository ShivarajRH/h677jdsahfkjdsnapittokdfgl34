<style>
	.dash_bar{
	color:#333;
	background:#f1faf1;
	padding:10px;
	font-size:15px;
	margin:5px;
	font-family:arial;
	float:left;
	width:205px;
	}
	.dash_bar .count{
	font-size:18px;
	font-weight:bold;
	}
	.dash_bar a{
	font-size:12px;
	float:right;
	padding:0px 5px;
	}
	.userdet div{
	margin-left:30px;
	padding:3px;
	}
</style>
<script>
$(function(){
	$("#block").click(function(){
		$("#blockform").submit();
	});
});
</script>
<div class="heading" style="margin-bottom:0px;">
<div class="headingtext container">
<?php if(isset($pagetitle)) echo $pagetitle; else echo "Users";?>
</div>
</div>
<div class="container" style="padding-top:10px;font-family:arial;min-height:250px;">

<div class="dash_bar">
<a href="<?=site_url("admin/users")?>">View</a>
<span class="count"><?=$totalusers?></span> total users
</div>

<?php /*?>
<div class="dash_bar">
<a href="<?=site_url("admin/usersbylogin/facebook")?>">View</a>
<span class="count"><?=$facebookusers?></span> Facebook users
</div>

<div class="dash_bar">
<a href="<?=site_url("admin/usersbylogin/twitter")?>">View</a>
<span class="count"><?=$twitterusers?></span> Twitter users
</div>
*/ ?>
<div class="dash_bar" style="width:auto;">
<?php $acs=$this->db->query("select * from king_corporates where alias=0 order by name asc")->result_array();?>
By corporate : <select id="acs_sel">
<option value="0">select a corporate</option>
<?php foreach($acs as $ac){?>
<option value="<?=$ac['id']?>"><?=$ac['name']?></option>
<?php }?>
</select>
</div>

<script>
$(function(){
	$("#acs_sel").change(function(){
		if($(this).val()!=0)
			location="<?=site_url("admin/usersbycorp")?>/"+$(this).val();
	});
});
</script>

<div style="clear:both">
<div style="font-family:arial;font-size:13px;margin-left:20px;padding-top:15px;">
<?php if(isset($p)){?>
<div align="right" style="margin-top:-12px;margin-bottom:-18px;font-size:12px;">
<?php 
$limit=20;
$st=(($p-1)*$limit+1);
$et=$st+$limit-1;
if($et>$len)
	$et=$len;
?>
<?php if($len>0){?>
<?php if($p>1 && isset($prevurl)){?>
<a style="padding:5px;" href="<?=$prevurl?>">previous</a>
<?php }?>
showing <?=$st?><?php if($st!=$et){?>-<?=$et?><?php }?> of <?=$len?>
<?php if($et<$len && isset($nexturl)){?>
<a style="padding:5px;" href="<?=$nexturl?>">next</a>
<?php }}?>
</div>
<?php }else{?>
<div align="right" style="margin-top:-12px;margin-bottom:-18px;font-size:12px;">&nbsp;</div>
<?php }?>
<div style="margin-top:20px;padding:5px;border:1px solid #ddd;background: #fff url(<?=base_url()?>images/bg.gif) repeat-x;">
<?php if(!isset($userdetails)){?>
<table width="100%" class="datagrid" cellpadding="5">
<tr>
<th>User name<br><span style="font-size:10px;font-weight:normal;">sort</span><a href="<?=$url?>/1/name/a"><img src="<?=base_url()?>images/desc.gif"></a><a href="<?=$url?>/1/name/d"><img src="<?=base_url()?>images/asc.gif"></a></th>
<th>Email<br><span style="font-size:10px;font-weight:normal;">sort</span><a href="<?=$url?>/1/email/a"><img src="<?=base_url()?>images/desc.gif"></a><a href="<?=$url?>/1/email/d"><img src="<?=base_url()?>images/asc.gif"></a></th>
<th>Corp Email</th>
<th>Created on<br><span style="font-size:10px;font-weight:normal;">sort</span><a href="<?=$url?>/1/created/a"><img src="<?=base_url()?>images/desc.gif"></a><a href="<?=$url?>/1/created/d"><img src="<?=base_url()?>images/asc.gif"></a></th>
<th></th>
</tr>
<?php foreach($users as $user){?>
<tr>
<td style="max-width:160px;"><a class="link" href="<?=site_url("admin/user/{$user->userid}")?>" style="color:#00f;"><?=$user->name?></a></td>
<td><?=$user->email?></td>
<td>
<?=$user->corpemail?>
</td>
<td><?php echo date("M d, g:i a",$user->createdon);?></td>
<td style="width:70px;"><a style="font-size:11px;" href="<?=site_url("admin/ordersbyuser/{$users[0]->userid}")?>">View Orders</a></td>
</tr>
<?php }?>
</table>
<?php }else{?>
<div style="float:right;margin-top:20px;margin-right:40px;">
<?php if($users[0]->block==0){?>
<input type="button" id="block" value="Block this user">
<?php }else{?>
<input type="button" id="block" value="Unblock this user">
<?php }
$acts=array("block","unblock");
?>
<br><br>
<?php if($users[0]->verified==1){?>
<div style="color:blue">Verified User</div>
<?php }else{?>
<div style="color:red">Unverified User</div>
<?php }?>
</div>
<form id="blockform" method="post" action="<?=site_url("admin/blockuser")?>">
<input type="hidden" name="userid" value="<?=$users[0]->userid?>"></input>
<input type="hidden" name="action" value="<?=$acts[$users[0]->block]?>"></input>
</form>
<div class="userdet" style="margin-left:10px;margin-top:5px;font-size:12px;">
<span style="font-size:17px;font-weight:bold;">User Details</span>
<div>User Name : <b><?=$users[0]->name?></b></div>
<div>Email : <b><?=$users[0]->email?></b></div>
<div>Corporate Email : <b><?=$users[0]->corpemail?></b></div>
<div>Corporate: <a href="<?=site_url("admin/corporate/".$users[0]->corpid)?>"><b><?=$users[0]->corp?></b></a> <a href="<?=site_url("admin/usersbycorp/".$users[0]->corpid)?>">view members</a></div>
<div>Created On : <b><?=date("g:ia d M Y",$users[0]->createdon)?></b></div>
<div>Login : <b>
<?php switch($users[0]->special){
	case 0:
		echo "Normal";break;
	case 2:
		echo "Facebook";break;
	case 3:
		echo "Google";break;
	case 1:
		echo "Twitter";break;
}?>
</b>
</div>
<div>Referred by : <?php if(isset($users[1])){?>
<a href="<?=site_url("admin/user/{$users[1]->userid}")?>"><?=$users[1]->name?></a>
<?php }else{?>
<i>n/a</i>
<?php }?>
</div>
<?php if($users[0]->block==1){?>
<div style="color:red">This user is blocked</div>
<?php }?>
</div>
<div style="font-weight:bold;font-size:16px;margin:10px;">Referrals</div>
<div style="margin:5px 20px;">
<?php foreach($referrals as $ref){?>
<a href="<?=site_url("admin/user/{$ref->userid}")?>"><?=$ref->name?></a>
<?php }?>
<?php if(empty($referrals)) echo "No referrals";?>
</div>
<div style="font-weight:bold;font-size:16px;margin:10px;">Orders</div>
<div style="font-weight:bold;font-size:13px;margin:10px 20px;"><a href="<?=site_url("admin/ordersbyuser/{$users[0]->userid}")?>">View Orders</a></div>
<?php }?>
<?php if(empty($users)){?>
<div style="padding:10px;font-size:15px;">No users to show</div>
<?php }?>
</div>
</div>
</div>
</div>