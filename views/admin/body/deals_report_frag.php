<table class="datagrid">
<thead><tr><th>Sno</th><th>Item id</th><th>Deal Id</th><th>Pnh id</th><th>Deal Name</th><th>Mrp</th><th>Price</th></tr></thead>
<tbody>
<?php $i=1; foreach($deals as $d){?>
<tr>
<td><?=$i++?></td>
<td><?=$d['id']?></td>
<td><?=$d['dealid']?></td>
<td><?=$d['pnh_id']?></td>
<td><a target="_blank" href="<?=site_url("admin/deal/{$d['id']}")?>"><?=$d['name']?></a></td>
<td>Rs <?=$d['mrp']?></td>
<td>Rs <?=$d['price']?></td>
</tr>
<?php }?>
</tbody>
</table>

<?php
