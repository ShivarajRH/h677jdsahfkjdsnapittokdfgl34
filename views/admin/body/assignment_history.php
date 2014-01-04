
<div class="container">
	<div id="main_column" class="clear">
		<div class="cm-notification-container "></div>
		<div class="tools-container">
						<h1 class="mainbox-title">Assignment Histroy of PNH Employees</h1>
		</div>
		</br>
		<table class="table sorter">
		<?php if($assignment_histroy){?>
			<thead>
			    <th>Sl no</th>
			    <th>Employee name</th>
			    <th>Role</th>
			    <th>Assigned Under</th>
				<th>From</th>
				<th>To</th>
				
			</thead>
			<tbody>
			<?php 
			$i=1;
			foreach($assignment_histroy as $assignment_det){?>
				<tr>
				     <td><?php echo $i;?></td>
				     <td><?php echo ucwords($assignment_det['emp_name'])?></td>
				     <td><?php echo $assignment_det['role_name']?></td>
				     <td><?php echo ucwords($assignment_det['name'].'---'.$assignment_det['assigned_under'])?></td>
					<td><?php echo $assignment_det['assigned_on']?></td>
					<td><?php echo $assignment_det['modified_on']?></td>
				</tr>
				<?php 
				$i++;
                 }?>



			</tbody>
		</table>
		<?php }?>
	</div>
</div>