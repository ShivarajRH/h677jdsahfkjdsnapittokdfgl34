<table class="datagrid smallheader" width="100%">
<thead><tr><th>Sno</th><th width="100">Product</th><th>MRP</th><th>Offer</th><th><span style="font-size:80%;">Landing Cost</span><th>Stock</th></tr></thead>
<tbody>

<?php $sno=1; foreach($prods as $p){
	 
?>
<tr onclick='trig_loadpnh("<?=$p['pnh_id']?>")' style="cursor:pointer;">
<td><?=$sno++?></td>
<td width="60"><a href="<?=site_url("admin/pnh_deal/{$p['pnh_id']}")?>" target="_blank"><?=$p['name']?></a></td>
<td><?=$p['mrp']?></td>
<td><?=$p['price']?></td>

<?php /*?>
<td><?=($p['price']*$p['margin']/100)?> (<?=$p['margin']?>%)</td>
<?php */ ?>
<td><?=round($p['price']-($p['price']*$p['margin']/100),2)?></td>
<td style="background: #b4defe !important;text-align: center;font-weight: bold;font-size: 14px;"><?=$p['stock']?></td>
</tr>
<?php } if(empty($prods)){?><tr><td colspan="100%">no products to suggest</td></tr><?php }?>
</tbody>
</table>

<?php
