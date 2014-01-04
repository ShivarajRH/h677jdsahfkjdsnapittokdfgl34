<?php 
$user=$this->session->userdata("admin_user");
?>
<script>
$(function(){
	$(".viewmore").click(function(){
		$("select",$(this).parent()).show();
	});
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
Transaction Notes 
</div>
</div>
<div class="container" style="font-family:arial;min-height:250px;">
<DIV style="font-family:arial;font-size:13px;padding-top:15px;">
<?php if(isset($p)){?>
<div align="right" style="margin-top:-12px;margin-bottom:-18px;font-size:12px;">
<?php 
$limit=25;
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
<?php if(count($transnotes)){?>
<table width="100%" cellpadding="5">
<tr>
<th>Transno</th>

<th>Note</th>
<th>Time</th>
</tr>
<?php foreach($transnotes->result() as $note){?>
<tr>
<td width="120"><?=anchor_popup('admin/trans/'.$note->transid,$note->transid)?>
	<div style="color: #cd0000"><b><?php echo $note->note_priority?'High Priority':''?></b></div>
</td>
<td><?=str_replace('User Note:','<b>User Note:</b>',$note->note)?></td>
<td><?=date("g:ia d/m/y",strtotime($note->created_on));?></td>
</tr>
<?php }?>
</table>
<?php }?>
<?php if(empty($transnotes)){?>
<div style="padding:10px;font-size:15px;">No notes available</div>
<?php }?>
</div>
</div>
</div>
<br>
<br>