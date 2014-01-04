<div class="container">
<div class="dash_bar_right">
<span><?=$count?></span>
categories
</div>
<h2 style="margin-top:20px;">Available Categories</h2>
<a href="<?=site_url("admin/addcat")?>">add new category</a>

<div align="center" class="blist_navi">
<?php foreach($alphas as $alpha){?>
<a href="#balpha<?=$alpha?>"><?=strtoupper($alpha)?></a>
<?php }?>
</div>

<ul class="disc_cont disc_tags_cont">
<?php 
foreach($categories as $i=>$cbrands){?>
<li class="col<?=($i+1)?>">
<?php foreach($cbrands as $alpha=>$abrands){?>
<div class="alpha blist" id="balpha<?=$alpha?>">
<h2><a class="head" id="balpha_<?=$alpha?>"><?=strtoupper($alpha)?></a></h2>
<div style="padding:5px;">
<?php foreach($abrands as $brand){?>
<div>
<a href="<?=site_url("admin/viewcat/{$brand['id']}")?>"><?=$brand['name']?></a>
</div>
<?php }if(empty($abrands)){?>
<i style="font-size:85%;font-weight:normal;">none</i>
<?php }?>
</div>
</div>
<?php }?>	
</li>
<?php }?>
</ul>
<div class="clear"></div>

</div>
<?php
 /*?>
<div class="container">
<h2>Brands</h2>
<a href="<?=site_url("admin/addbrand")?>">Add Brand</a>
<table class="datagrid">
<thead><tr><th>Brand Name</th><th>Alloted Rack Bins</th><th></th></tr></thead>
<tbody>
<?php foreach($brands as $b){?>
<tr>
<td><?=$b['name']?></td>
<td><?php foreach($rbs as $r){
if($r['brandid']==$b['id']) 	echo $r['rack_name']."-".$r['bin_name'].", ";
} ?>
</td>
<td><a class="link" href="<?=site_url("admin/editbrand/{$b['id']}")?>">edit</a></td>
</tr>
<?php }?>
</tbody>
</table>
</div>
<?php*/
