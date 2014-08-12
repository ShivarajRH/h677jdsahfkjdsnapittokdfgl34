<div class="page_container">
	<div align="left">
		<a href="<?php echo site_url('admin/add_asset')?>" class="button button-royal" style="float:right;">Add Asset</a>
		<h3 class="page_title">Asset List</h3>
		
	</div>
	<div>
		<?php
			if($asset_list){
		?>
				<table width="100%"  class="datagrid" cellpadding="5" cellspacing="0">
					<thead>
						<th width = "10"><b>#</b></th>
						<th width = "90"><b>Asset Name</b></th>
						<th width = "100"><b>Brand</b></th>
						<th width = "100"><b>Category</b></th>
						<th width = "100"><b>Created On</b></th>
						<th width = "70"><b>Created By</b></th>
						<th width = "100"><b>Actions</b></th>
						<th width = "100"><b></b></th>
					</thead>
					<tbody>
						<?php 
							$i = 1;
							if($asset_list)
							{

									foreach ($asset_list as $asset){
								?>
									<tr>
										<td><?php echo $i;?></td>
										<td><?php echo $asset['asset_name'];?></td>
										<td><?php echo $asset['brand_name'];?></td>
										<td><?php echo $asset['category_name'];?></td>
										<td><?php echo $asset['created_date'];?></td>
										<td><?php echo $asset['created_byname'];?></td>
										<td><?php echo anchor('admin/edit_asset/'.$asset['asset_id'],'Edit')?></td>
										<td><?php echo anchor('admin/view_asset/'.$asset['asset_id'],'View')?></td>
									</tr>
								<?php 
										$i++;
									} 
							
							}else{ ?>
								<tr>
									<td><?php echo "No data available "?> </td>
								</tr>
						<?php 	}
						?>
					</tbody>
			<tfoot>
				<tr>
					<td colspan="10" align="right">
						<div id="pagination">
							<?php echo $pagination?>
					
					</td>
					</div>
				</tr>
			</tfoot>
		</table>
		<?php 		
			}else{
				echo "no data found";		
			}
		?>
	</div> 
	 
</div>