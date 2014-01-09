<div class="container">
<h2>Reviews</h2>

<div id="revfrm" style="display:none;padding:10px;" align="right">
<form method="post">
<input type="hidden" name="itemid" id="itemid" value="">
Reply : <textarea name="reply" rows=5 cols=50 ></textarea>
<input type="submit" value="Submit">
</form>
</div>

<table class="datagrid" width="100%" style="background:#fff url(<?=base_url()?>images/bg.gif) repeat-x;" cellpadding=5>
<tr>
	<th>User</th><th>Name</th><th>Rating</th><th>Review</th><th>Product</th>
</tr>
<?php foreach($reviews as $r){?>
<tr>
	<td><?php if($r['userid']!=0){?><a href="<?=site_url("admin/user/".$r['userid'])?>"><?=$r['name']?></a><?php }else echo 'Guest';?></td>
	<td><?=$r['name']?></td>
	<td><?=$r['rating']?></td>
	<td><?=$r['review']?></td>
	<td><a href="<?=site_url("admin/deal/".$r['dealid'])?>"><?=$r['product']?></a></td>
	<td><a href="javascript:void(0)" onclick='writereview(<?=$r['itemid']?>)'>reply</a> <?php if($r['status']==1){?><a href="<?=site_url("admin/delreview/".$r['id'])?>">delete</a><?php }else {?>DELETED<?php }?></td>
</tr>
<?php }?>
</table>
</div>
<script>
function writereview(iid)
{
	$("#revfrm").slideUp("fast");
	$("#itemid").val(iid);
	$("#revfrm").slideDown("slow");
}
</script>