<div class="container">
	<h2>Deal product link update log</h2>
	<table class="datagrid">
		<thead>
			<tr>
				<th>#</th>
				<th>Deal name</th>
				<th>Linked product</th>
				<th>Action</th>
				<th>Updated on</th>
				<th>Update by</th>
			</tr>
		</thead>
		<tbody>
			<?php if($log_details){
				foreach($log_details as $i=>$det)
				{
					?>
					<tr>
						<td><?php echo $i+1;?></td>
						<td><?php echo $det['d_name'];?></td>
						<td><?php echo $det['product_name'];?></td>
						<td>
							<?php 
								if($det['is_updated']==1)
									echo 'Product Removed';
								else if($det['is_updated']==2)
									echo 'Product Added';
								
							?>
						</td>
						<td><?php echo date('d/m/Y',strtotime($det['perform_on']));?></td>
						<td><?php echo $det['username'];?></td>
						
					</tr>
					
		<?php 	}			
			}?>
		</tbody>
	</table>
</div>

