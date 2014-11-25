<div id="page_wrap">
	<div class="page_top">
		<div class="page_topbar_left">
			<h2 class="page_title">MarketPlace Seller List</h2>
		</div>
		
		<div style="float:right;margin-top: -45px;">
			<div class="page_topbar_right">
				<a href="<?php echo site_url("admin/add_marketplace_seller")?>" class="button button-rounded button-action button-small">Add Market Seller</a>
			</div>
		</div>
	</div>
	<div class="page_content clearboth">
	<div class="clear"></div>
	<table class="datagrid" width="100%">
		<thead>
			<th>Sl no</th>
			<th>Seller Name</th>
			<th>Contact Number</th>
			<th>Town|Territory</th>
			<th>Created By</th>
			<th>Created On</th>
			<th>Modified By</th>
			<th>Modified On</th>
			<th>Actions</th>
		</thead>
		<tbody>
			<?php if($seller_list->num_rows()){
					$i=1;
					foreach($seller_list->result_array() as $s){
					
				?>
			<tr>
				<td><?php echo $i;?></td>
				<td><?php echo ucfirst($s['seller_name'])?></td>
				<td><?php echo $s['contact_no']?></td>
				<td><?php echo $s['town_name']?> | <?php echo $s['territory_name']?></td>
				<td><?php echo $s['createdby']?></td>
				<td><?php echo $s['created_on']?></td>
				<td><?php echo $s['modifiedby']?>	</td>
				<td><?php echo $s['modified_on']?></td>
				<td><a href="<?php echo site_url("admin/edit_m_seller/{$s['seller_id']}")?>" target="_blank" class="button button-tiny button-flat-action">Edit</a>&nbsp;
					<a href="<?php echo site_url("admin/view_m_seller/{$s['seller_id']}")?>" target="_blank" class="button button-tiny button-flat-primary">View</a>
				</td>
			</tr>
			<?php  $i++;}}else{?>
			<tr>
				<td><div align="center">No Data Found</div>
				</td>
			</tr>
			<?php }?>
		</tbody>
	</table>
	</div>
</div>
