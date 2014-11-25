<div class="container">

<h2>Partners</h2>
<a href="<?=site_url("admin/add_partner")?>">Add new partner</a>
<table class="datagrid">
<thead><tr><th>Sno</th><th>Name</th><th>Transaction Prefix</th><th >Transaction Mode</th><th colspan="2">SKU Text</th></tr></thead>
<tbody>
<?php $i=0; $mode=array("PG","COD","Custom1","Custom2","Custom3","Custom4"); foreach($partners as $p){?>
<tr>
<td><?=++$i?></td>
<td><?=$p['name']?></td>
<td><?=$p['trans_prefix']?></td>
<td><?=$mode[$p['trans_mode']]?></td>
<td><?=$p['partner_sku_text']?></td>
<td><a href="<?=site_url("admin/edit_partner/{$p['id']}")?>">edit</a></td>
</tr>
<?php }?>
</tbody>
</table>

</div>
<?php
