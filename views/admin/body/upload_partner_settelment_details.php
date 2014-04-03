<div class="container page_wrap" style="padding-top:7px;margin-top: 10px;">
<a href="<?php echo site_url('admin/view_partner_settelment_log')?>" style="float:right;" target="_blank" class="myButton_pnhfranpg">View Log</a>

<h2>Upload Partner Settelment Details</h2>
<form enctype="multipart/form-data" method="post" id="upload_partner_settelment">
<table width="100%" cellspacing="5">
<tr>
<td>Choose Partner:<select name="partner_id">
						<option value = "0">Select Partner</option>
						<?php foreach($this->db->query("select * from partner_info order by name asc")->result_array() as $p){?>
						<option value="<?php echo $p['id']?>"><?php echo $p['name']?></option>
						<?php }?>
						</select>
</td></tr>
<tr><td>Upload File :<input type="file" name="partner_settlement_det"> &nbsp; <input id="file_submit" type="submit" value="Upload & Update"></td></tr>
<tr><div style="color:#888;margin:10px 20px;"><ul><li><b>Only CSV format supported</b></ul></div>
</tr>
</table>
</form>
<h4 style="margin:0px;">Template Format</h4>
<table class="datagrid noprint" width="100%">
<?php $template=array("Order Id","Value","Shipping Charges","Payment Id","Payment Amount","Payment Date(dd/mm/yyyy)");?>
<thead>
<tr>
<?php foreach($template as $t){?><th><?=$t?></th><?php }?>
</tr>
</thead>
<tbody>
<tr>
<?php foreach($template as $i=>$t){?><td>&nbsp;<?php }?></td>
</tr>
</tbody>
</table>


</div>
<script>
$('#upload_partner_settelment').submit(function(){
	if($('select[name="partner_id"]').val() == 0)
	{
		alert('Please Select Partner');
		return false;
	}
});
</script>