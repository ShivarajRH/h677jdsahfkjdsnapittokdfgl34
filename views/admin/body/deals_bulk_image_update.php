<div class="container">
<h2>Images update for bulk upload</h2>
<div>This page has dynamic upload. You can upload images simultaneously for multiple deals.</div>
<?php foreach($items as $i=>$item){?>
<div id="deal<?=$i?>" class="deals">
<h3><a style="color:#000;" href="<?=site_url("admin/deal/{$item['dealid']}")?>" target="_blank"><?=$item['name']?></a></h3>
<?php if(!$item['is_image_updated']){?>
<div style="padding:5px">
<form target="formsub<?=$i?>" method="post" enctype="multipart/form-data" action="<?=site_url("admin/bu_img_update")?>">
<input type="hidden" name="i" value="<?=$i?>">
<input type="hidden" name="itemid" value="<?=$item['item_id']?>">
<input type="file" name="pic"><input type="submit" value="Upload">
</form>
<div class="msg" style="display:none;">
<img src="<?=IMAGES_URL?>loader_gold.gif" style="float:right">
Uploading...
</div>
</div>
<iframe id="formsub<?=$i?>" name="formsub<?=$i?>" style="display:none;"></iframe>
<?php }else{?>
<div class="msg">Updated already</div>
<?php }?>
<div style="padding-top:5px;">mark it as
<input class="but_actions" type="button" onclick='pub_deal(<?=$item['dealid']?>,<?=$item['publish']==1?0:1?>)' value="<?=$item['publish']==0?"Publish!":"Unpublish"?>">
<input class="but_actions" type="button" onclick='live_deal(<?=$item['item_id']?>,<?=$item['live']==1?0:1?>)' value="<?=$item['live']==0?"In Stock!":"Out of stock"?>">
</div>
</div>
<?php if(($i+1)%3==0) echo '<div class="clear"></div>';
}?>

</div>

<style>
.deals{
float:left;
width:300px;
margin:7px;
padding:10px;
border:1px solid #aaa;
background:#eee;
}
.deals h3{
margin:0px;
}
.deals .msg{
font-weight:bold;
font-size:110%;
padding:5px;
border:1px dashed #aaa;
background:#fff;
}
</style>

<script>

function pub_deal(did,pub)
{
	$.post("<?=site_url("admin/jx_pub_deal")?>",{did:did,pub:pub});
}

function live_deal(id,pub)
{
	$.post("<?=site_url("admin/jx_live_deal")?>",{id:id,live:pub});
}


$(function(){

	$(".but_actions").click(function(){
		$(this).attr("disabled",true);
	}).attr("disabled",false);
	
	$(".deals form").submit(function(){
		$(this).hide();
		$(".msg",$(this).parent()).show();
		return true;
	});
});
function updatedimg(i,err)
{
	if(err!="0")
		$("#deal"+i+" .msg").html("Upload failed");
	else
		$("#deal"+i+" .msg").html("Uploaded & updated!");
}
</script>
<?php
