<div class="container">
<h2>Free Sample config</h2>

<form method="post">
<table style="background:#fff;" border=1 cellpadding=5 id="fsc">
<tr><th>Minimum Order</th><th>No of samples allowed</th></tr>
<?php foreach($config as $c){?>
<tr>
<td><input type="text" name="min[]" value="<?=$c['min']?>"></td>
<td><input type="text" name="limit[]" value="<?=$c['limit']?>"></td>
</tr>
<?php }?>
<tr id="clone">
<td><input type="text" name="min[]"></td>
<td><input type="text" name="limit[]"></td>
</tr>
</table>
<input type="button" value="+" id="addfsc">
<input type="submit" value="update">
</form>
<script>
$(function(){
	$("#addfsc").click(function(){
		$("#fsc").append("<tr>"+$("#clone").html()+"</tr>");
	});
});
</script>
</div>
<?php
