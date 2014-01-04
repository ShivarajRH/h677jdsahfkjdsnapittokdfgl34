<h2>Recent Transactions</h2>
<div style="float:right;">*PONR - point of no return</div>
<table cellpadding=5 width="100%" border=1 class="datagrid">
<tr>
<th>TransID</th>
<th>Amount</th><th>Paid</th><th>Mode</th><th>Status</th><th>Start Time</th><th>End Time</th>
</tr>
<?php foreach($trans as $tran){ ?>
	<tr>
		<td><a href="<?=site_url("callcenter/trans/".$tran['transid'])?>"><?=$tran['transid']?></a></td>
		<td><?=$tran['amount']?></td>
		<td><?=$tran['paid']?></td>
		<td><?=$tran['mode']==0?"PG":"COD"?></td>
		<td><?=$tran['status']==0?"PONR":"RETURNED"?></td>
		<td><?=date("g:ia d/m/y",$tran['init'])?></td>
		<td><?=$tran['actiontime']==0?"n/a":date("g:ia d/m/y",$tran['init'])?></td>
	</tr>
<?php } ?>
</table>