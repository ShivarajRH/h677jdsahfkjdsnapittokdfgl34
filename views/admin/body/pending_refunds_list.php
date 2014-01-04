<div class="container">
<h2>Pending Refunds</h2>
<table class="datagrid">
<thead><tr><th>Transaction</th><th>Amount</th><th>On</th><th colspan="2">Status</th></tr></thead>
<tbody>
<?php foreach($refunds as $r){?>
<tr><td><a href="<?=site_url("admin/trans/{$r['transid']}")?>"><?=$r['transid']?></a></td>
<td><?=$r['amount']?></td>
<td><?=date("g:ia d/m/y",$r['created_on'])?></td>
<td>pending</td>
<td><a href="<?=site_url("admin/mark_c_refund/{$r['refund_id']}")?>">mark it as complete</a></td>
<?php }?>
</tbody>
</table>
</div>
<?php

