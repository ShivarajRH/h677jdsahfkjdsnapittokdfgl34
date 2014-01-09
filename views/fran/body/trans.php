<div class="container">
<h1>Transactions</h1>
<?php if(!empty($trans)){?>
<table style="background:#fff;margin:10px;font-size:14px;" cellpadding=10 border=1>
<tr>
<th>Remarks</th>
<th>Withdrawal</th>
<th>Deposit</th>
<th>Closing Balance</th>
<th>Date</th>
</tr>
<?php foreach($trans as $tran){?>
<tr>
<td><?=$tran['name']?></td>
<td><?=$tran['withdrawal']?></td>
<td><?=$tran['deposit']?></td>
<td><?=$tran['balance']?></td>
<td><?=date("g:ia d/m/y",$tran['time'])?></td>
</tr>
<?php }?>
</table>
<?php }else{?>
<h2 style="margin:20px">No transactions yet</h2>
<?php }?>
</div>
<?php
