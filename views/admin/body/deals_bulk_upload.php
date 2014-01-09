<div class="container">

<h2>Deals bulk upload</h2>

<form method="post" target="_blank" enctype="multipart/form-data" id="bk_form">
Upload file : <input type="file" name="deals"><input type="submit" value="Upload">
<div style="color:#888;margin:10px 20px;"><ul><li>Only CSV format supported</li><li>First row as head neglected always</li>
<li>Link Products : <b>Comma separated product_id:qty as key:value pair</b></li>
</ul></div>
</form>

<h4 style="margin:0px;">Template</h4>
<div style="max-width:900px;overflow:auto">
<table class="datagrid noprint">
<?php $template=array("Item Code","Deal Name","Menu (ID)","Secondary menu (ID)","Category (ID)","Brand (ID)","MRP","Offer Price","Image ID","Tax","Start date (dd-mm-yyyy)","End date (dd-mm-yyyy)","Ships in","Quantity","Brief Description","Extra Description","Keywords","Products");
$values=array("D38DK","Livon Hair Gain Tonic - 150ml","2","3","424","42423423","443","421","d9sjd92j23d3","5","21-11-2012","02-04-2013","24 hrs","2424","brief brief blah blah","blah blah blah blah blah blah blah blah","tonic, hair, hair fall, growth","4940492:2,4489354:1");
?>
<thead>
<tr>
<?php foreach($template as $t){?><th><?=$t?></th><?php }?>
</tr>
</thead>
<tbody>
<tr>
<?php foreach($template as $i=>$t){?><td><?=$values[$i]?></td><?php }?>
</tr>
</tbody>
</table>
</div>

<br><br>

<h3 style="margin-bottom:0px;">Deal bulk uploads</h3>
<table class="datagrid">
<theaD><Tr><th>Deals Uploaded</th><th>Image Upload Status</th><th>Created on</th><th>Created By</th><th></th></Tr></theaD>
<tbody>
<?php foreach($this->db->query("select b.id,b.items,b.is_all_image_updated,b.created_on,u.name as admin  from deals_bulk_upload b join king_admin u on u.id=b.created_by order by b.id desc")->result_array() as $u){?>
<tr><td><?=$u['items']?></td><td><?=$u['is_all_image_updated']?"All Done":"Pending"?></td><td><?=date("g:ia d/m/y",$u['created_on'])?></td><td><?=$u['admin']?></td><td><a class="link" href="<?=site_url("admin/deals_bulk_image_update/{$u['id']}")?>">Update Images</a></td></tr>
<?php }?>
</tbody>
</table>


</div>

<script>
$(function(){
	$("#bk_form").submit(function(){
		window.setTimeout(function(){location="<?=site_url("admin/deals_bulk_upload")?>";},5000);
		return true;
	});
});
</script>
<?php
