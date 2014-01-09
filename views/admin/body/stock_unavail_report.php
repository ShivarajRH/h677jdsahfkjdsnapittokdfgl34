<div style="padding:5px;font-family:arial;">
<a href="<?=site_url("admin/stock_unavail_report/$partial/$from/$to/$is_pnh/export")?>">Export</a>
<?php if($from!=0){?><h2>For the orders between <?=$from?> and <?=$to?></h2><?php }?>
<table border=1 cellpadding=5>
<thead><tr>
<th>Product Name</th>
<th>Mrp</th>
<th>Purchase Cost</th>
<th>Ordered Qty</th>
<th>Available</th>
<th>Required Qty</th>
<th>Brand</th><th>Vendors</th><th>Order</th></tr></thead>
<tbody>
<?php foreach($reports as $r){?>
<tr>
<td><?=$r['product_name']?></td>
<td><?=$r['mrp']?></td>
<td><?=$r['purchase_cost']?></td>
<td><?=$r['qty']?></td>
<td><?=$r['available']?></td>
<td><?=$r['qty']-$r['available']?></td>
<td><?=$r['brand']?></td>
<td><?=$r['vendors']?></td>
<td><?=$r['transid']?></td>
</tr>
<?php }?>
</tbody>
</table>
</div>
<?php
