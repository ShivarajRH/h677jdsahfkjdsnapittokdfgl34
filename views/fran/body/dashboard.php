<?php $user=$this->session->userdata("fran_auser");?>
<div class="container">
<h2>Dashboard</h2>
<table width="100%" cellspacing=10>
<tr>
<td valign="top" width="50%" style="border:1px solid #777;-moz-border-radius:5px;background:#fff;padding:7px;">
<h3>Orders</h3>
<?php if(empty($orders)){?>
<div>
no orders yet
</div>
<?php }else{?>
<table cellpadding=2 width="100%" cellspacing=0>
<tr>
<th>Order Id</th><th>Item Name</th><th>Paid</th><th>Qty</th><th>Status</th>
</tr>
<?php 
foreach($orders as $order){
?>
<tr onmouseover='$(this).css("background","#eee")' onmouseout='$(this).css("background","#fff")'>
<td><a href="<?=site_url("franchisee/vieworder/".$order['id'])?>"><?=$order['id']?></a></td>
<td><?=$order['name']?></td>
<td><?=$order['paid']?></td>
<td><?=$order['quantity']?></td>
<td><?php 
switch($order['status'])
{
	case 0:
		echo "pending";break;
	case 1:
		echo "processed";break;
	case 2:
		echo "shipped";break;
	case 3:
		echo "rejected/cancelled";break;
}
?></td>
</tr>
<?php }?>

</table>
<?php }?>
</td>
<td valign="top" width="50%" style="border:1px solid #777;-moz-border-radius:5px;background:#fff;padding:7px;">
<a href="<?=site_url("franchisee/transactions")?>" style="float:right">view all</a>
<h3>Transactions</h3>
<?php if(empty($trans)){?>
<div>no transactions yet</div>
<?php }else{?>
<table width="100%" cellpadding=5 border=0>
<tr>
<th>Remark</th>
<th>Withdrawal</th>
<th>Deposit</th>
<th>Closing Bal</th>
</tr>
<?php foreach($trans as $i=>$tran){?>
<tr>
<td><?=$tran['name']?></td>
<td><?=$tran['withdrawal']?></td>
<td><?=$tran['deposit']?></td>
<td><?=$tran['balance']?></td>
</tr>
<?php }?>
</table>
<?php }?>
</td>
</tr>
</table>
</div>