<div class="container">
	<h2>PNH Bulk Import Members</h2>
	<form method="post" enctype="multipart/form-data" id="pnh_importm_form" autocomplete="off">
		<table class="datagrid">
			<tr>
				<td><b>Import File</b></td>
				<td>
					<input type="file" name="imp_file" value="">
				</td>
				<td>
					<input type="submit" value="Import">
				</td>
			</tr>
		</table>
	</form>
	<iframe id="hndl_bulk_importmems" name="hndl_bulk_importmems" style="width:800px;height:400px;border:0px;"></iframe>
</div>
<style>
	tfoot{display: none;}
</style>