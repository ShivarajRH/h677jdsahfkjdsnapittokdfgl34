<?php 
$user=$this->session->userdata("admin_user");
?>
<script>
$(function(){
	$(".viewmore").click(function(){
		$("select",$(this).parent()).show();
	});
	$("#brandsel").change(function(){
		obj=$(this);
		if(obj.val()==0)
			return;
		location.href="<?=site_url("admin/activitybybrand")?>/"+obj.val();
	});

	//	$(".actmsg").click(function(){
//		$("span",this).toggle();
//		$("a",this).toggle();
//	});
});
</script>
<style>
.actmsg div{
margin-left:50px;
}
.lngactmsg{
font-size:14px;
font-family:arial;
padding:10px;
padding-left:40px;
}
.lngactmsg div{
color:#f00;
margin-left:40px;
margin-top:10px;
}
</style>
<div class="heading" style="margin-bottom:0px;">
<div class="headingtext container">
<?php if(isset($pagetitle)) echo $pagetitle; else echo "Activity";?>
</div>
</div>
<div class="container" style="font-family:arial;min-height:250px;">
<div style="float:left;padding-top:5px;">
<?php if(isset($brands)){?>
<div class="sidepane">
<div style="font-size:15px;">View activity by brand</div>
<?php $ic=0; foreach($brands as $brand){?>
<a style="margin:0px 5px;font-size:13px;" href="<?=site_url("admin/activitybybrand/{$brand->id}")?>"><nobr><?=$brand->name?></nobr></a>
<?php $ic++;if($ic==10) break;}?>
<?php if(count($brands)>10){?>
<div align="center"> 
<a href="javascript:void(0)" class="viewmore" style="font-size:13px;float:right;font-weight:bold;">more</a>
<select id="brandsel" style="display:none">
<option value="0">--select--</option>
<?php foreach($brands as $brand){?>
<option value="<?=$brand->id?>"><?=$brand->name?></option>
<?php }?>
</select>
</div>
<?php }?>
</div>
<?php }?>
</div>
<DIV style="font-family:arial;font-size:13px;margin-left:220px;padding-top:15px;">
<?php if(isset($p)){?>
<div align="right" style="margin-top:-12px;margin-bottom:-18px;font-size:12px;">
<?php 
$limit=10;
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
<?php }?>
<div style="margin-top:20px;padding:5px;border:1px solid #ddd;background:#fff url(<?=base_url()?>images/bg.gif) repeat-x;">
<?php if(count($activity)!=1){?>
<table width="720" cellpadding="5">
<tr>
<th>Activity</th>
<th>Deal</th>
<th>Brand</th>
<th>User Name</th>
<th>Time</th>
</tr>
<?php foreach($activity as $act){?>
<tr>
<td class="actmsg"><span><?=substr($act->msg,0,21)?>...</span> <a href="<?=site_url("admin/viewactivity/".$act->id)?>">more</a>
<span style="display:none"><?=$act->msg?></span></td>
<td><?=$act->tagline?></td>
<td><?=$act->brandname?></td>
<td><?=$act->username?></td>
<td><?=date("g:ia d/m/y",$act->time)?></td>
</tr>
<?php }?>
</table>
<?php }else{$act=$activity[0];?>
<div style="font-family:'trebuchet ms';font-size:23px;padding:5px;">
<div style="float:right"><a style="font-size:12px;" href="<?=site_url("admin/deal/{$act->dealid}")?>">View Deal</a></div>
<?=$act->tagline?></div>
<div class="lngactmsg"><?=$act->msg?></div>
<div style="color:#00f;font-size:18px;padding:10px;">by <b><?=$act->username?></b> on <b><?=date("g:ia d M",$act->time)?></b></div>
<?php }?>
<?php if(empty($activity)){?>
<div style="padding:10px;font-size:15px;">No activity</div>
<?php }?>
</div>
</div>
</div>