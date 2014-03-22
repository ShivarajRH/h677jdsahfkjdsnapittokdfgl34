<style>
.viewbrand h4{
margin-bottom:0px;
}
</style>
<div class="container viewbrand">
<div style="float:left;width:35%;">
<h2><?=ucfirst($cat['name'])?> Category Details</h2>

<table class="datagrid noprint">
<tbody>
<tr>
<td>Name</td><td><?=$cat['name']?> <a class="link" href="<?=site_url("admin/editcat/{$cat['id']}")?>" class="link">edit</a></td>
</tr>
<tr>
<td>Main</td><td>
<?php if($cat['type']!=0){?>
<a class="link" href="<?=site_url("admin/viewcat/{$cat['type']}")?>" class="link"><?=$cat['main']?> </a>
<?php }else echo 'none';?>
</td>
</tr>
</tbody>
</table>
</div>


<div style="float:left;width:60%;margin-left:20px;">
<h4>Deals of <?=$cat['name']?> (<?=count($deals)?>)</h4>
<div style="max-height:300px;overflow:auto;">
<table class="datagrid" width="100%">
<thead>
<tr><th>Deal Name</th><th>URL</th><th>MRP</th><th>Price</th><th>Category</th></tr>
</thead>
<tbody>
<?php foreach($deals as $p){?>
<tr>
<td><a class="link" href="<?=site_url("admin/edit/{$p['dealid']}")?>"><?=$p['name']?></a></td>
<td><a class="link" href="<?=site_url("{$p['url']}")?>">site</a></td>
<td><?=$p['orgprice']?></td>
<td><?=$p['price']?></td>
<td><a href="<?=site_url("admin/viewcat/{$p['catid']}")?>"><?=$p['category']?></a></td>
</tr>
<?php }?>
</tbody>
</table>
</div>
</div>

<div class="clear"></div>

</div>
<?php
