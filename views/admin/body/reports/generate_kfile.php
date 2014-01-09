<div align="left" style="padding:10px;min-height:400px;">
	<h3 class="page_title">Generate K File</h3>
	<div class="form_block" >
		<form target="hndl_genkfile_frm" action="<?php echo site_url('admin/reports/generate_kfile')?>" method="post">
			<b>Enter Invoice no's</b>
			<p style="margin: 0px;font-size: 11px;">Please separete each invoice by comma</p>
			 
			<input type="text" name="inv_nos" value="" class="inputbox" style="width:300px;padding:5px;font-size: 14px;">
			<input type="submit" value="Generate" name="submit" class="sbutton">
			<br><b style="font-size: 11px;">Ex : 1000,1001,1002</b>
		</form>
		
		<iframe id="hndl_genkfile_frm" name="hndl_genkfile_frm" style="width:0px;height:0px;border: none;"></iframe>
	</div>
</div>
<style type="text/css">
h3.page_title{
	font-size: 16px;
	margin: 2px;
}
.form_block{
	padding:5px;
}
</style>