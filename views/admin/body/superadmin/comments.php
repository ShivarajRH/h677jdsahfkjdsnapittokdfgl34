<?php 
$user=$this->session->userdata("admin_user");
?>
<script>
$(function(){
	$("input:checkbox").attr("checked",false);
	$(".comment").click(function(){
		$("span",this).toggle();
		$("a",this).toggle();
	});
	$("#selectall").click(function(){
		if($(this).attr("checked")==true)
			$("input:checkbox").attr("checked",true);
		else
			$("input:checkbox").attr("checked",false);
	});
});
function acceptcom()
{
	st="";
	if($(".commentcheck:checked").length==0)
	{
		alert("Please select comments to moderate");
		return;
	}
	$(".commentcheck:checked").each(function(){
		st+=$(this).val()+",";
	});
	$("#action").val("accept");
	$("#cids").val(st);
	$("#modform").submit();
}
function flagcom()
{
	st="";
	if($(".commentcheck:checked").length==0)
	{
		alert("Please select comments to flag");
		return;
	}
	$(".commentcheck:checked").each(function(){
		st+=$(this).val()+",";
	});
	alert(st);
	$("#action").val("flag");
	$("#cids").val(st);
	$("#modform").submit();
}
</script>
<form id="modform" method="post" action="<?=site_url("admin/commentsmoderate")?>">
<input type="hidden" id="cids" name="cids" value="">
<input type="hidden" id="action" name="action" value="">
</form>
<div class="heading" style="margin-bottom:0px;">
<div class="headingtext container">
<?php if(isset($pagetitle)) echo $pagetitle; else echo "Comments";?>
</div>
</div>
<style>
.sidepane div{
padding:2px 5px;
}
.hide{
display:none;
}
.show{
display:inline;
}
.comment .spancom{
display:none;
}
.comment a{
font-size:11px;
}
table td{
vertical-align:top;
}
.sidepane{
width:auto;
}
</style>
<div class="container">
<div style="float:left">
<div class="sidepane">
<div style="font-size:15px;">View by status</div>
<div><a style="color:#444;font-weight:bold;margin:0px 5px;font-size:13px;" href="<?=site_url("admin/commentsbystatus/new")?>"><nobr>New</nobr></a></div>
<div><a style="color:#00f;font-weight:bold;margin:0px 5px;font-size:13px;" href="<?=site_url("admin/commentsbystatus/moderated")?>"><nobr>Moderated</nobr></a></div>
<div><a style="color:#f00;font-weight:bold;margin:0px 5px;font-size:13px;" href="<?=site_url("admin/commentsbystatus/flagged")?>"><nobr>Flagged</nobr></a></div>
<div align="right"><a style="color:#00f;font-size:13px;" href="<?=site_url("admin/comments")?>"><nobr>view all</nobr></a></div>
</div>
</div>
<DIV style="font-family:arial;font-size:13px;margin-left:130px;padding-top:10px;">
<div style="margin-top:10px;padding:5px;border:1px solid #ddd;background:url(<?=base_url()?>images/bg.gif) repeat-x;">
<?php if(isset($p)){?>
<div align="right" style="float:right;font-size:12px;">
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
<div style="padding-left:10px;">
<input type="button" value="Accept" onclick='acceptcom()'> &nbsp; <input type="button" onclick='flagcom()' value="Flag">
</div>
<table width="100%" cellspacing="5">
<tr>
<th><input type="checkbox" id="selectall"></th>
<th>User name</th>
<th>Item name</th>
<th>Comment</th>
<th>Status</th>
</tr>
<?php 
if($comments!=false)
foreach($comments as $comment){
?>
<tr>
<td><input class="commentcheck" type="checkbox" value="<?=$comment->id?>"></td>
<td><?=$comment->username?></td>
<td><?=$comment->itemname?></td>
<td width="440">
<div class="comment">
<span><?=substr($comment->comment,0,20)?></span>
<span class="spancom"><?=$comment->comment?></span>
<a href="javascript:void(0)" class="hide">hide</a>
<a href="javascript:void(0)" class="show">show</a>
</div>
</td>
<td>
<?php 
if($comment->flag==1)
	echo "flagged";
else if($comment->new==0)
	echo "new";
else
	echo "moderated"
?>
</td>
</tr>
<?php }?>
</table>
<?php 
if($comments==false) echo "No $pagetitle";?>
</div>
</div>
</div>
