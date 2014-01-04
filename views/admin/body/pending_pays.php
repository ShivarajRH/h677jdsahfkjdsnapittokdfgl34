<div class="container">
<h2>Stocks Ready for Payments</h2>
<table class="datagrid">
<thead>
<tr>
<th>GRN</th><th>PO Status</th><th>Purchase orders</th><th>Created On</th><th>Action</th>
</tr>
</thead>
<tbody>
<?php foreach($grns as $grn){?>
<tr>
<td><a href="<?=site_url("admin/viewgrn/{$grn['grn_id']}")?>">GRN<?=$grn['grn_id']?></a></td>
<td><?=$grn['postatus']?"ALL CLOSED/COMPLETE":"PENDING POs"?></td>
<td>
<?php foreach($grn['pos'] as $po){?>
<a href="<?=site_url("admin/viewpo/{$po['po_id']}")?>">PO<?=$po['po_id']?></a>
<?php }?>
</td>
<td><?=$grn['created_on']?></td>
<td>
<a class="link" href="<?=site_url("admin/create_voucher/{$grn['grn_id']}")?>">Create voucher</a>
</td>
</tr>
<?php }?>
</tbody>
</table>
</div>
<?php
