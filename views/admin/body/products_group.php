<div class="container">

<h2><?=$pagetitle?></h2>

<div style="float:right;margin-right:10px;">
<h4 style="margin:0px;">Categories</h4>
<table class="datagrid smallheader noprint" width=250>
<thead><tr><th>ID</th><th>Category Name</th></tr></thead>
<tbody>
<?php foreach($this->db->query("Select * from products_group_category order by name asc")->result_array() as $cat){?>
<tr><td><?=$cat['id']?></td><td><a href="<?=site_url("admin/products_group/{$cat['id']}")?>" class="link"><?=$cat['name']?></a></td></tr>
<?php }?>
</tbody>
</table>
<form method="post" action="<?=site_url("admin/createproductgroupscat")?>">New Category : <input type="text" class="inp" size=10 name="cat"><input type="submit" value="Create"></form>
</div>


<a href="<?=site_url("admin/add_products_group")?>">Create new product group</a>
<table class="datagrid">
<thead><tr><th>Sno</th><th>Group ID</th><th>Group Name</th><th>Category</th><th>Products linked</th><th>Created On</th><th></th></tr></thead>
<tbody>
<?php foreach($groups as $i=>$g){?>
<tr>
<td><?=($i+1)?></td>
<td><?=$g['group_id']?></td>
<td><?=$g['group_name']?></td><Td><?=$g['category']?></Td><td><?=$g['pids']?></td>
<td><?=date("g:ia d/m/y",$g['created_on'])?></td>
<td><a href="<?=site_url("admin/product_group/{$g['group_id']}")?>" class="link">view</a>
</tr>
<?php } ?>
</tbody>
</table>

</div>
<?php
