<div class="container">
<h2>Device Types</h2>

<a href="javascript:void(0)" onclick='$("#add_terry").show()'>add new</a>

<div id="add_terry" style="display:none;padding:4px;">
<form method="post">
<table cellpadding=3>
<tr><td>Device Name : </td><td><input type="text" name="terry" size="20"></td></tr>
<tR><Td>Description : </Td><td><textarea style="width:200px;" name="desc"></textarea></td></tR>
<tr><td></td><td><input type="submit" value="Add"></td></tr>
</table>
</form>
</div>

<table class="datagrid">
<thead><tr><th>Device Name</th><th>Description<th></th></tr></thead>
<tbody>
<?php foreach($devs as $d){?>
<tr><td><?=$d['device_name']?></td>
<td><?=$d['description']?></td>
<td><a href="javascript:void(0)" onclick='update_terry("<?=$d['id']?>","<?=$d['device_name']?>","<?=$d['description']?>")'>edit</a></td>
<?php }?>
</tbody>
</table>

<form id="update_form" method="post">
<input type="hidden" name="edit" value="1">
<input id="upd_id" name="id" type="hidden">
<input type="hidden" name="desc" id="upd_desc">
<input id="upd_name" name="terry" type="hidden">
</form>

<script>
function update_terry(id,name,desc)
{
	name=prompt("Enter device name",name);
	if(name.length==0)
		return;
	desc=prompt("Enter device description",desc);
	if(desc.length==0)
		return;
	
	$("#upd_id").val(id);
	$("#upd_name").val(name);
	$("#upd_desc").val(desc);
	$("#update_form").submit();
}
</script>


</div>
<?php
