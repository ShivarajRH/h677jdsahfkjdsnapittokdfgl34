<div class="container">

<h2>Today's Special Margin products</h2>

<table class="datagrid">
<thead><tr><th>Sno</th><th>PID</th><th>Deal</th><th>Mrp</th><th>Offer Price</th><th>Special Margin</th><th>Landing Cost</th><th>Special Discount</th><th>Expires on</th></tr></thead>
<tbody>
<?php $i=1; foreach($deals as $d){?>
<tr>
<td><?=$i++?></td>
<td><?=$d['pnh_id']?></td>
<td><a class="link" href="<?=site_url("admin/pnh_deal/{$d['id']}")?>"></a><?=$d['name']?></td>
<td>Rs <?=$d['orgprice']?></td>
<td>Rs <?=$d['price']?></td>
<td><?=$d['special_margin']?>%</td>
<td><div style="font-weight:bold;background:#aae;padding:3px;">Rs <?=round($d['price']-($d['price']/100*$d['special_margin']),2)?></div></td>
<td>Rs <?=round($d['price']/100*$d['special_margin'],2)?></td>
<td><?=date("d/m/y",$d['to'])?></td>
</tr>
<?php }?>
</tbody>
</table>

</div>
<?php
