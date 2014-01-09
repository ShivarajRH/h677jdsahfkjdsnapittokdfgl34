<table class="datagrid">
<thead><tr><th>Sno</th><th>Product id</th><th>Product Name</th><th>Mrp</th></tr></thead>
<tbody>
<?php $i=1; foreach($prods as $d){?>
<tr>
<td><?=$i++?></td>
<td><?echo $d['product_id']; ?></td>
<td><a target="_blank" href="<?=site_url("admin/product/{$d['product_id']}")?>"><?=$d['product_name']?></a></td>
<td>Rs <?=$d['mrp']?></td>
</tr>
<?php }?>
</tbody>
</table>

<?php
