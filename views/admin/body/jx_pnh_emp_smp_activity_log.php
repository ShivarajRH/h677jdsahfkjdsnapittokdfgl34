<div align="right">
	<a href="javascript:void(0)" id="export_btn" class="button button-tiny button-primary" >Export</a>
</div>
<div>
<table class="datagrid" width="100%" cellpadding="5" cellspacing="0">
	<thead>
		<tr>
			<th>Employe name</th>
			<th>Role</th>
			<?php if($days)
			{
				foreach($days as $i=>$d)
				{
					if(date('N',strtotime($month.'-'.$i))==7)
						$i='Su';
			?>
					<th><?php echo $i;?></th>
			<?php	
				}
			}?>
			<th>Active days</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		if($employee_list)
		{
			foreach($employee_list as $emp)
			{
				$emp_status = ($emp['is_suspended']?'warning_txt':'');
				?>
				<tr>
					
					<td>
						<a target="_blank" class="<?php echo $emp_status;?>" href="<?php echo site_url('admin/view_employee/'.$emp['employee_id']); ?>">
							<?php echo ucfirst($emp['name']);?>
						</a>
					</td>
					<td><span style="font-size:11px;color:#666666;"><?php echo $emp['short_frm'];?></span></td>
					
					<?php
						if($days)
						{
							$ttl_active_days=0;
							foreach($days as $i=>$d)
							{
								if(isset($emp_sms_activity_log[$emp['employee_id']][$i]['types']))
								{
									$temp_types=array();
									foreach(explode(',',$emp_sms_activity_log[$emp['employee_id']][$i]['types']) as $t)
									{
										$t=strtolower($t);
										if(isset($types[$t]))
											$temp_types[]=$types[$t];
										else 
											$temp_types[]='o';
									}
									$ttl_active_days+=1;
									echo '<td><span style="font-size:9px;">'.ucwords(implode(' ',array_unique($temp_types))).'</span></td>';
								}else{
									echo '<td><span style="font-size:9px;">-</span></td>';
								}
							}
							echo '<td style="text-align:center;">'.$ttl_active_days.'</td>';
						}
					?>
				</tr>
				
				
		<?php	
			}
		}?>
	</tbody>
</table>
</div>


