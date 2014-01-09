<div class="container">
<h2><?php if($brand['name']==""){?>Add<?php }else{?>Edit<?php }?> Brand</h2>

<form method="post" id="ae_brand">


<div style="padding:0px 10px;">
Brand Name  : <input type="text" id="brandname" class="inp" name="name" value="<?=$brand['name']?>">
</div>

<h4>Linked rack bins: <input type="button" value="+" class="addlrb"></h4>
<table cellpadding=5>
<tbody id="lrb">
<?php foreach($rbs as $rb){?>
<tr>
<td><select name="rb[]">
<?php foreach($rackbins as $r){?>
<option value="<?=$r['id']?>" <?=$rb['rack_bin_id']==$r['id']?"selected":""?>><?=$r['rack_name']?>-<?=$r['bin_name']?></option>
<?php }?>
</select>
</td>
<td><a href="javascript:void(0)" onclick='$($(this).parents("tr").get(0)).remove()'>delete</a></td>
</tr>
<?php }?>
</tbody>
</table>

<div id="template" style="display:none">
<table>
<tbody>
<tr>
<td>
<select name="rb[]">
<option value="">Choose</option>
<?php foreach($rackbins as $r){?>
<option value="<?=$r['id']?>"><?=$r['rack_name']?>-<?=$r['bin_name']?></option>
<?php }?>
</select>
</td>
<td><a href="javascript:void(0)" onclick='$($(this).parents("tr").get(0)).remove()'>delete</a></td>
</tr>
</tbody>
</table>

</div>

<div style="padding:10px 0px;">
<input type="submit" value="Update">
</div>

</form>
</div>

<script>
$(function(){
	$("#ae_brand").submit(function(){
		if(!$.trim($('#brandname').val()))
		{
			alert("Brand name is required");
			return false;
		}
		if(!$('#lrb select[name="rb[]"]').val())
		{
			alert("atleast one rack bin needed");
			return false;
		}
		return true;
	});
	$(".addlrb").click(function(){
		$("#lrb").append($("#template table tbody").html());
	});
});
</script>
<?php
