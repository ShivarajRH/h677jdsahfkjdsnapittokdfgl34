<html>
<head><title>Print</title></head>
<body style="font-size:12px;font-family:arial;">
<h1><?=$pagetitle?></h1>
<?php $bal=0; foreach($frans as $f) $bal+=$f['current_balance'];?>
<h2>Total Balance : Rs <?=number_format($bal)?></h2>
<?php 
			$fran_status_arr=array();
			$fran_status_arr[0]="Live";
			$fran_status_arr[1]="Permanent Suspension";
			$fran_status_arr[2]="Payment Suspension";
			$fran_status_arr[3]="Temporary Suspension";
		?>
<table cellpadding=5 width="100%" border=1>
<thead><tr><th width=40>Sno</th><th>Franchise</th><th>Locality</th><th>Town</th><th>Balance</th><th>Uncleared</th><th>Security Deposit</th><th>Status</th></tr></thead>
<tbody>
<?php $i=1; foreach($frans as $f){?>
<tr>
<td><?=$i++?></td>
<td><?=$f['franchise_name']?></td>
<td><?=$f['locality']?></td>
<td><?=$f['town_name']?></td>
<td>Rs <?=number_format($f['current_balance'])?></td>
<td>Rs <?=$this->db->query("select sum(receipt_amount) as s from pnh_t_receipt_info where franchise_id=? and receipt_type=1 and status=1",$f['franchise_id'])->row()->s?></td>
<td><?php if($this->db->query("select 1 from pnh_t_receipt_info where franchise_id={$f['franchise_id']} and receipt_type=0 and status=1")->num_rows()>0) echo "YES"; else echo "NO";?>
<td><?=$fran_status_arr[$f['is_suspended']];?></td>
</tr>
<?php }?>
</tbody>
</table>

</body>
</html>
<?php
