<div class="container">
	<h3>Update Homeshop18 Orders Status to Manifesto</h3>
	<form id="upd_orderstatus" action="<?php echo site_url('admin/process_upd_partordstatus_manifesto') ?>" method="post" target="hndl_upd_orderstatus" style="width: 60%;">
		<b>Enter HS18 reference order nos (comma separated)</b><br />  
		<textarea style="width: 100%;height: 150px;" name="partner_ordernos"></textarea>
		<br />
		<div align="right">
			<input type="submit" value="Update">
		</div>
	</form>
	<iframe id="hndl_upd_orderstatus" name="hndl_upd_orderstatus" style="width: 1px;height: 1px;border:none"></iframe>
</div>
<script type="text/javascript">
	$('#upd_orderstatus').submit(function(){
		var p_oids = $('textarea[name="partner_ordernos"]').val();
			p_oids = p_oids.replace(/\n/g,",");
			p_oids = p_oids.replace(/,,/g,",");
			p_oids = p_oids.replace(/,,/g,",");
			$('textarea[name="partner_ordernos"]').val(p_oids);
		if(!p_oids.length)
		{
			alert("No ordernos added");
			return false;
		}
		
	});
</script>