<?php
if($status == 'success')
{
?>

<div class="filters_block">
	<div class="filter">
		<form style="margin-top:5px;">
				<b>From :</b><input type="text" class="date_from" size="11" value="<?=($date_from =='0')?'':$date_from; ?>" placeholder="YYYY-MM-DD">
				<b>To :</b><input type="text" class="date_to" size="11" value="<?=($date_to=='0')?'':$date_to; ?>" placeholder="YYYY-MM-DD">
				<button type="button" class="button button-tiny date_filter_submit" style="color:#000000 !important">Go</button>
		</form>
	</div>
	<div class="filter">
		<form style="margin-top:5px;">
				<b>Search Invoice No. :</b><input type="text" class="srh_invoice_no" size="11" value="<?=($srh_invoice_no == 0)?'':$srh_invoice_no; ?>" placeholder="Invoice number">
		</form>
	</div>
	<div align="right"  class="log_pagination fl_right">
		<?php echo $log_det_pagination; ?>
	</div>
</div>
<div class="block_notify">Total <?=$total_rows;?> items found. Showing <?=$pg_from;?> to <?=$pg_limit;?></div>
<div class="clear">&nbsp;</div>
<table class="datagrid" cellpadding='4' style='padding:5px;' width='100%'>
	<thead>
	<tr>
		<th>#</th>
		<th>Created On</th>
		<th>Created By</th>
		<!--<th>Log id</th>-->
		<th>Credit Note Id</th>
		<th>Receipt id</th>
		<th>reconcile_id</th>
		<th>Is Invoice Cancelled</th>
		<th>Reconcile Amount</th>
		<th>Is Receipt Reversed?</th>
		<th>Invoice no</th>
		<th>Debit Note Id</th>
		<th>Amount</th>
		<th>Remarks</th>
	</tr>
	</thead>
	<tbody>
<?php
	foreach($log_det as $i=>$log)
	{
?>
	<tr>
		<td><?=(++$i + $pg);?></td>
		<td><?=$log['created_on'];?></td>
		<td><?=ucfirst($log['username']);?></td>
		<!--<td><?=$log['logid'];?></td>-->
		<td><?=($log['credit_note_id']==0) ? '--':$log['credit_note_id']; ?></td>
		<td><?=($log['receipt_id']==0) ? '--':$log['receipt_id']; ?></td>
		<td><?=$log['reconcile_id'];?></td>
		<td><?=($log['is_invoice_cancelled']==1)?'Yes':"No";?></td>
		<td><?=$log['reconcile_amount'];?></td>
		<td><?= ($log['is_reversed']==1)?'Yes':"No";?></td>
		<td>
			<?php  echo ($log['invoice_no']==0) ? '--' : '<a href="'.site_url("admin/invoice/".$log['invoice_no']).'" target="_blank">'.$log['invoice_no'].'</a>';?>
		</td>
		<td><?= ($log['debit_note_id']==0) ? '--':$log['debit_note_id']; ?></td>
		<td><?=$log['inv_amount'];?></td>
		<td><?=$log['remarks'];?></td>
	</tr>
<?php
	}
?>
	</tbody>
</table>
<div align="right"  class="log_pagination fl_right">
	<?php echo $log_det_pagination; ?>
</div>
<?php 
}
else
{
	echo '<p align="center">'.$msg.'</p>';
}
?>
<script>$('.date_from,.date_to').datepicker({changeMonth: true,changeYear: true,dateFormat:'yy-mm-dd'});</script>