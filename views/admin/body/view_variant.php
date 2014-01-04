<div class="container">
<h2>View Variant</h2>

<table class="datagrid">
<thead>
<tr><th>Variant Name</th><th>Type</th><th>Created on</th></tr>
</thead>
<tbody>
<?php $variants=array($variant); foreach($variants as $v){?>
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
</tr>
<?php }?>
</tbody>
</table>

<h4>Deals linked</h4>
<table class="datagrid">
<thead>
<tr><th>Deal Name</th><th>Variant value (size/hexadec color)</th><th>Website link</th></tr>
</thead>
<tbody>
<?php foreach($items as $item){?>
<tr>
<td><a class="link" href="<?=site_url("admin/deal/{$item['dealid']}")?>"><?=$item['name']?></a></td>
<td><?=$item['variant_value']?></td>
<td><a href="<?=site_url($item['url'])?>">link</a>
</tr>
<?php }?>
</tbody>
</table>

</div>
<?php
