<div class="container">

<h2>Partner orders log</h2>

<table class="datagrid">
<thead><tr><th>Sno</th><th>Partner</th><Th>Items</Th><th>Total Order Amount</th><th>Complete Order Amount</th><th>Amount Paid</th><th>Created By</th><th>Created On</th></tr></thead>
<tbody>
<?php $i=1; foreach($logs as $l){?>
<tr>
<td><a class="link" href="<?=site_url("admin/view_partner_orders/{$l['id']}")?>"></a><?=$i++?></td>
<td><?=$l['name']?></td>
<td><?=$this->db->query("select count(1) as n from partner_order_items where log_id=?",$l['id'])->row()->n?></td>
<td>Rs <?=number_format($l['amount'])?></td>
<td>Rs <?=$this->db->query("select ifnull(sum(l.i_partner_price*l.qty),0) as s from partner_order_items l join king_orders o on o.transid=l.transid and o.status=2 and log_id=?",$l['id'])->row()->s?></td>
<td>Rs <?=$l['amount_paid']?></td>
<td><?=$l['created_by']?></td>
<td><?=date("d/m/y",$l['created_on'])?></td>
</tr>
<?php }?>
</tbody>
</table>

</div>
<?php
