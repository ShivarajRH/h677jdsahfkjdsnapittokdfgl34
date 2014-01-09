<?php 
	$paf_flags = array();
	$paf_flags[0] = '';
	$paf_flags[1] = 'Open';
	$paf_flags[2] = 'Closed';
	$paf_flags[3] = 'Cancelled';
	
?>
<div class="container">
	<h2>PAF List</h2>
	<table class="datagrid">
		<thead>
			<tr>
				<th>CreatedOn</th>
				<th>PAF ID</th>
				<th>Handled By</th>
				<th>Status</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php 
				$i = 0;
				foreach($paflist as $paf)
				{
			?>
					<tr>
						<td><?php echo date('d/m/Y h:i a',strtotime($paf['created_on'])); ?></td>
						<td><?php echo anchor('admin/editpaf/'.$paf['paf_id'],$paf['paf_id'])?></td>
						<td><?php echo $paf['handled_by_name']?></td>
						<td><?php echo $paf_flags[$paf['paf_status']]?></td>
						<td><?php echo anchor('admin/editpaf/'.$paf['paf_id'],'View')?>
							<?php 
								if($paf['paf_status'] == 1)
								{
									echo '&nbsp;&nbsp;&nbsp;'.anchor('admin/paf_stockintake/'.$paf['paf_id'],'StockIntake');	
								}
							?>
						</td>
					</tr>
			<?php 
				} 
			?>
		</tbody>
	</table>
</div>

