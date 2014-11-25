<div class="page_wrap">
	<h2 class="page_title">Bulk update partner deals</h2>
	<div class="page_content">
		<form enctype="multipart/form-data" action="<?php echo site_url('admin/process_update_partnerdeals')?>" method="post">
			<input type="file" name="pd_data_file" >
			<input type="submit" name="submit" value="Update Deals">
		</form>
		<br>
		<b>Template</b>
		<table class="datagrid">
			<thead>
				<th>Slno</th>
				<th>PartnerID</th>
				<th>ItemID</th>	
				<th>Refno</th>
			</thead>
		</table>
	</div>
</div>

