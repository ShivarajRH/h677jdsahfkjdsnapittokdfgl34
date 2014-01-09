<div class="container">

<h2>Territories</h2>

<a href="javascript:void(0)" onclick='$("#add_terry").show()'>add new</a>

<div id="add_terry" style="display:none;padding:4px;">
<form method="post">
Territory Name : <input type="text" name="terry" size="40"><input type="submit" value="Add">
</form>
</div>

<table class="datagrid">
<thead><tr><Th>Sno</Th><th>Territory name</th><th></th></tr></thead>
<tbody>
<?php $i=0; foreach($terrys as $t){?>
<tr>
<td><?=++$i?></td><td><?=$t['territory_name']?></td>
<td>
	<a href="javascript:void(0)" onclick='update_terry("<?=$t['id']?>","<?=$t['territory_name']?>")'>edit</a> &nbsp; 
	<a href="<?=site_url("admin/pnh_towns/{$t['id']}")?>">view towns</a>
</td>
</tr>
<?php }?>
</tbody>
</table>

<form id="update_form" method="post">
<input type="hidden" name="edit" value="1">
<input id="upd_id" name="id" type="hidden">
<input id="upd_name" name="terry" type="hidden">
</form>

<script>
function update_terry(id,name)
{
	name=prompt("Enter new territory name",name);
	if(name.length==0)
		return;
	$("#upd_id").val(id);
	$("#upd_name").val(name);
	$("#update_form").submit();
}
</script>

</div>
<?php
