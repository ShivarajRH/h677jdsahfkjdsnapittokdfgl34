<div class="page_wrap">
	<h2 class="page_title">Bulk update member price</h2>
	<div class="page_content">
		<form enctype="multipart/form-data" action="<?php echo site_url('admin/process_update_memberprices')?>" method="post">
			<input type="file" name="mp_data_file" >
			<input type="submit" name="submit" value="Update prices">
		</form>
		<br>
		<b>Template</b>
		<table class="datagrid">
			<thead>
				<th>Itemid</th>	<th>MRP</th><th>Price</th>	<th>Member Price</th>	<th>mp_frn_max_qty</th>	<th>mp_mem_max_qty</th>	<th>mp_max_allow_qty</th>
			</thead>
		</table>
	</div>
</div>

