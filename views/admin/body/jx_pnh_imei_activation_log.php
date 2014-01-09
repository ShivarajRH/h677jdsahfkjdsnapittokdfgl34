<?php
	$imei_actv_list = $this->db->query("select e.username as activated_byname,imei_activated_on,activated_by,activated_mob_no,activated_member_id,franchise_name,imei_no,imei_reimbursement_value_perunit as imei_credit_amt from t_imei_no a join king_orders b on a.order_id = b.id join king_transactions c on c.transid = b.transid join pnh_m_franchise_info d on d.franchise_id = c.franchise_id left join king_admin e on e.id = a.activated_by where a.is_imei_activated = 1 order by imei_activated_on desc limit 10");
?>
<table class="datagrid" width="100%">
	<thead>
		<th width="20" style="text-align: left">Slno</th>
		<th width="130"  style="text-align: left">Activated On</th>
		<th width="70"  style="text-align: left">Activated By</th>
		<th style="text-align: left">Franchise</th>
		<th style="text-align: left" width="150">IMEI/Serial no</th>
		<th style="text-align: left" width="100">Mobile no</th>
		<th style="text-align: left" width="100">Activated MemberID</th>
		<th style="text-align: left" width="30">Credit</th>
	</thead>
	<tbody>
		<?php
			$i=0;
			foreach($imei_actv_list->result_array() as $imei_det)
			{
		?>
				<tr>
					<td><?php echo ++$i ?></td>
					<td><?php echo format_datetime($imei_det['imei_activated_on']) ?></td>
					<td><?php echo ($imei_det['activated_byname']?$imei_det['activated_byname']:'SMS') ?></td>
					<td><?php echo $imei_det['franchise_name'] ?></td>
					<td><?php echo $imei_det['imei_no'] ?></td>
					<td><?php echo $imei_det['activated_mob_no'] ?></td>
					<td><?php echo $imei_det['activated_member_id'] ?></td>
					<td><?php echo $imei_det['imei_credit_amt'] ?></td>
				</tr>
		<?php				
			}
		?>
	</tbody>
</table>