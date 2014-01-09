<script>
function postinvite()
{
	if($(".frndcheck:checked").length==0)
	{
		alert("Please select friends to share link");
		return;
	}
	uids="";
	$(".frndcheck:checked").each(function(){
		if(uids=="")
			uids=$("#friend"+$(this).attr("fid")).attr("fuid");
		else
			uids+=","+$("#friend"+$(this).attr("fid")).attr("fuid");
	});
	$.post("<?=site_url("jx/fbthisuser/$dealid/$itemid")?>","uids="+uids,function(data){
			alert("Sale link is shared with your friends");
		});
}
</script>
<div style="float:left;padding:10px;margin:10px;background:#fff">
<div class="headingtext">Share sale with Facebook friends</div>
<div align="left" style="padding-top:15px;font-family:arial;font-size:13px;">
Please choose friends to share this sale link
<div style="font-size:11px;margin-left:170px;margin-top:15px;font-weight:bold;">select <a href="javascript:void(0)" onclick='$(".frndcheck").attr("checked",true)'>all</a> &nbsp; <a onclick='$(".frndcheck").attr("checked",false)' href="javascript:void(0)">none</a></div>
<div style="padding:5px;background:#eee;margin-left:50px;width:200px;<?php if(count($friends)>10) {?>height:300px;overflow:scroll;<?php }?>">
<?php 
$i=0;
foreach($friends as $friend){
?>
<div id="friend<?=$i?>" fuid="<?=$friend['uid']?>">
<input type="checkbox" checked class="frndcheck" fid="<?=$i?>"> <?=$friend['name']?>
</div>
<?php $i++; }?>
</div>
<div style="margin-left:50px;margin-top:10px;"><input onclick='postinvite()' style="padding: 3px 5px; background: rgb(51, 51, 51) none repeat scroll 0% 0%; font-family: verdana; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; color: rgb(239, 239, 239); font-weight: bold; font-size: 14px;" type="button" value="Post share url"></div>
</div>
</div>