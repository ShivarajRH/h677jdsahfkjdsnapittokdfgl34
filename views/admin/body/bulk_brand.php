<div class="container">
<h2>Add Brand (Bulk)</h2>
<form method="post" id="ae_brand">
<table>
<tr>
 <td>
	<fieldset>
		<legend>
		<h4>Brand Names</h4>
		</legend>
		<textarea name="bulk_brandname" id="bulk_brandname"  rows="10" cols="50"></textarea><span style="color: red;"> <?php if(validation_errors()) { echo form_error('bulk_brandname');}?></span>
	</fieldset>
	<div style="padding:10px 0px;">
		 <input type="submit" name="submit" value="Add">
	</div>
 </td>
</tr>
</table>
</form>
</div>
<script>
$(function(){
	$("#ae_brand").submit(function(){
		if(!$.trim($('#bulk_brandname').val()))
		{
			alert("Brand name is required");
			return false;
		}
		return true;
	});
	
});
</script>
<?php
