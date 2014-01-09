<div class="container">
<h2>Less margin brands</h2>
<form method="post">
<div id="blist" style="margin:10px;background:#eee;padding:5px;border:1px solid #aaa">
<?php foreach($this->db->query("select b.id,b.name from pnh_less_margin_brands l join king_brands b on b.id=l.brandid order by b.name asc ")->result_array() as $b){?>
<div class="lm_b"><input type="hidden" class="bids" name="bids[]" value="<?=$b['id']?>"><?=$b['name']?> <a href="javascript:void(0)" onclick='$(this).parent().remove()'>x</a></div>
<?php }?>
<div class="clear"></div>
</div>
<input type="submit" value="Submit">
</form>

<br><br>
Add brand : <select id="brand">
<?php foreach($this->db->query("select id,name from king_brands order by name asc")->result_array() as $b){?>
<option value="<?=$b['id']?>"><?=$b['name']?></option>
<?php }?>
</select><input type="button" value="Add" onclick='addbrand()'>

</div>

<script>
function addbrand()
{
	var bids=[];
	id=val=$("#brand").val();
	name=$("#brand option:selected").text();
	$(".bids").each(function(){
		bids.push($(this).val());
	});
	if($.inArray(id,bids)!=-1)
		return;
	$("#blist").prepend('<div class="lm_b"><input type="hidden" class="bids" name="bids[]" value="'+id+'">'+name+' <a href="javascript:void(0)" onclick="$(this).parent().remove()">x</a></div>');
}
</script>
<style>
.lm_b{
margin-right:5px;
background:#fff;
float:left;
padding:3px;
}
</style>
<?php
