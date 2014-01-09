<div class="container">
<h2>Variants Group</h2>
<a href="<?=site_url("admin/addvariant")?>">add new variant</a>

<table class="datagrid">
<thead>
<tr><th>Variant Name</th><th>Type</th><th>Created on</th><th></th></tr>
</thead>
<tbody>
<?php foreach($variants as $v){?>
<tr>
<td><?=$v['variant_name']?></td>
<td><?php switch($v['variant_type']){
	case 0:
		echo 'size (ml)';break;
	case 1:
		echo 'size (g)';break;
	case 2:
		echo "hexacolor";break;
	case 3:
		echo "others";
}?>
</td>
<td><?=$v['created_on']?></td>
<td><a class="link" href="<?=site_url("admin/view_variant/{$v['variant_id']}")?>">view</a></td>
</tr>
<?php }?>
</tbody>
</table>

</div>
<?php
