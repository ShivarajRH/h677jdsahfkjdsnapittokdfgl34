<div class="container">


<div class="dash_bar_right" style="padding:7px;">
Date range: <input type="texT" size="8" class="inp" id="ds_range" value="<?=$this->uri->segment(3)?>"> to <input size="8" type="text" class="inp"id="de_range" value="<?=$this->uri->segment(4)?>"> <input type="button" value="Show" onclick='showrange()'>
</div>

<h2>Products price changelog between <?=date("d/m/y",$s)?> and <?=date("d/m/y",$e)?></h2>

<table class="datagrid">
<thead><tr><th>Sno</th><th>Product</th><th>Old MRP</th><th>New MRP</th><th>Reference</th><th>Created By</th><th>Created On</th></tr></thead>
<tbody>
<?php $i=1; foreach($prods as $p){?>
<tr>
<td><?=$i++?></td>
<td><a href="<?=site_url("admin/product/{$p['product_id']}")?>" target="_blank"><?=$p['product_name']?></a></td>
<td><?=$p['old_mrp']?></td>
<td><?=$p['new_mrp']?></td>
<td><a href="<?=site_url("admin/viewgrn/{$p['reference_grn']}")?>">GRN<?=$p['reference_grn']?></a></td>
<td><?=$p['created_by']?></td>
<td><?=date("g:ia d/m/y",$p['created_on'])?></td>
</tr>
<?php }?>
</tbody>
</table>

</div>

<script>

function showrange()
{
	location="<?=site_url("admin/product_price_changelog")?>/"+$("#ds_range").val()+"/"+$("#de_range").val();
}

$(function(){
	$("#ds_range,#de_range").datepicker();
});
</script>

<?php
