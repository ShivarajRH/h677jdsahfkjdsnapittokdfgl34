<div class="container" align="left">
	<h2>Update Shipment byFile</h2>
	<form method="post" action="<?php echo site_url('admin/p_updateshipmentbyfile')?>" enctype="multipart/form-data" target="hndl_shipment_update">
		<div class="form_box">
			<table cellspacing="0" cellpadding="0">
				<tbody>
					<tr>
						<td><b>Upload File : </b></td>
						<td><input size="30" type="file" value="" name="import_file" class="inputbox">
							<input type="submit" value="Submit" class="sbutton">
						</td>
					</tr>
									 
				</tbody>
			</table>
		</div>
		
		<iframe id="hndl_shipment_update" name="hndl_shipment_update" style="width:0px;height:0px;border:none"></iframe>
		
	</form>
<br>
<div>
<h3>Upload history</h3>
<table class="table_grid_view" style="width:600px;">
	<thead>
		<th>Slno</th>
		<th>Filename</th>
		<th>Total in File</th>
		<th>Uploaded on</th>
		<th>&nbsp;</th>
	</thead>
	<tbody>
	<?php 
		$i=0;
		$upd_list_res = $this->db->query("select uniq_id,file_name,count(*) as total_infile,logged_on from king_shipment_update_filedata group by uniq_id order by logged_on desc ");
		foreach($upd_list_res->result_array() as $upd_list_row)
		{
			 
			echo '<tr class="'.(($i%2)?'even_row':'odd_row').'">
						<td>'.++$i.'</td>
						<td>'.$upd_list_row['file_name'].'</td>
						<td>'.$upd_list_row['total_infile'].'</td>
						<td>'.$upd_list_row['logged_on'].'</td>
						<td  align="center"><a href="'.site_url('admin/download_shpupdfile/'.md5($upd_list_row['uniq_id'])).'">Download</a></td>
				  </tr>';
		}
	?>
	</tbody>
</table>	
</div>
<br>
<br>
</div>
<script type="text/javascript">
function show_updlog(resp){
	alert(resp.message);
	location.href = location.href ; 
}
</script>
