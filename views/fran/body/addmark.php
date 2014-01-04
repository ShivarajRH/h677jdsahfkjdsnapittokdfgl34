<div class="container">
<h1 style="margin-bottom:10px"><?php if(isset($mark)) {?>Edit<?php }else{?>Add<?php }?> Mark Up/Down</h1>
<form method="post" id="markform">
<table style="font-size:14px">
<tr>
<td>Type :</td>
<td>
<select class="inp" name="type">
<option value="1" <?php if(isset($mark) && $mark['mark']>0) echo "selected"?>>Mark Up</option>
<option value="2" <?php if(isset($mark) && $mark['mark']<0) echo "selected"?>>Mark Down</option>
</select>
</td>
</tr>
<tr>
<td>Amount :</td>
<?php 
if(isset($mark) && $mark['mark']<0)
	$mark['mark']*=-1;
?>
<td><input type="text" class="inp" name="price" <?php if(isset($mark)) echo 'value="'.$mark['mark'].'"'?>></td>
</tr>
<tr>
<td></td>
<td><input type="submit" value="Submit"></td>
</tr>
</table>
</form>
</div>
<script>
$(function(){
	$("#markform").submit(function(){
		if(!is_natural($("input",$(this)).val()))
		{
			alert("Invalid amount");
			return false;
		}
		return true;
	});
});
</script>
<?php
