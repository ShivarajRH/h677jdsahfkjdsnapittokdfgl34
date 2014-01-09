<script>
function postinvite()
{
	if($(".frndcheck:checked").length==0)
	{
		alert("Please select friends to send invite");
		return;
	}
	uids="";
	$(".frndcheck:checked").each(function(){
		if(uids=="")
			uids=$("#friend"+$(this).attr("fid")).attr("fuid");
		else
			uids+=","+$("#friend"+$(this).attr("fid")).attr("fuid");
	});
	$.post("<?=site_url("jx/fbinviteuser")?>","uids="+uids,function(data){
			alert("Your friends are invited");
		});
}
</script>
<div class="heading" align="center">
<div class="headingtext container">Invite friends from Facebook</div>
</div>
<div class="container" style="padding-top:15px;font-family:arial;font-size:13px;">
Please choose friends to post your invitation url
<div style="font-size:11px;margin-left:200px;margin-top:15px;">select <a href="javascript:void(0)" onclick='$(".frndcheck").attr("checked",true)'>all</a> <a onclick='$(".frndcheck").attr("checked",false)' href="javascript:void(0)">none</a></div>
<div style="padding:5px;background:#eee;margin-left:50px;width:200px;<?php if(count($friends)>10) {?>height:200px;overflow:scroll;<?php }?>">
<?php 
$i=0;
foreach($friends as $friend){
?>
<div id="friend<?=$i?>" fuid="<?=$friend['uid']?>">
<input type="checkbox" checked class="frndcheck" fid="<?=$i?>"> <?=$friend['name']?>
</div>
<?php $i++; }?>
</div>
<div style="margin-left:50px;margin-top:10px;"><input onclick='postinvite()' style="padding: 3px 5px; background: rgb(51, 51, 51) none repeat scroll 0% 0%; font-family: verdana; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; color: rgb(239, 239, 239); font-weight: bold; font-size: 14px;" type="button" value="Post invite url"></div>
</div>