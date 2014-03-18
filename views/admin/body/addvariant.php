<div class="container">
<h2>Add variant group</h2>

<div style="padding:20px;">Search &amp; add deals:
<input type="text" class="inp" id="deal_srch_inp">
<div id="deal_srch_res" class="srch_result_pop"></div>
</div>

<form method="post" id="var_form">
<div>Variant Name : <input type="text" class="inp" name="name"></div>
<div style="padding-top:7px;">Variant Type : <select name="type"><option value="0">size (ml)</option><option value="1">size (g)</option><option value="2">hexacolor</option><option value="3">others</option></select></div>

<h4>Deal variants</h4>

<table class="datagrid deal_vars">
<thead>
<tr><th>Deal Name</th><th>Variant name/color hexadecimal value/size</th></tr>
</thead>
<tbody>
</tbody>
</table>

<div style="padding:20px;">
<input type="submit" value="Submit">
</div>

</form>

<div id="template" style="display:none">
<table>
<tbody>
<tr>
<td><input type="hidden" name="itemid[]" value="%itemid%">%name%</td>
<td><input type="text" class="inp" name="value[]"></td>
</tbody>
</table>
</div>

<script>
var items=[];
function adddealitem(id,name)
{
	$("#deal_srch_res").hide();
	if($.inArray(id,items)!=-1)
	{
		alert("Deal already added");
		return;
	}
	temp=$("#template table tbody").html();
	temp=temp.replace(/%itemid%/g,id);
	temp=temp.replace(/%name%/g,name);
	$(".deal_vars tbody").append(temp);
	items.push(id);
}

$(function(){
	$("#var_form").submit(function(){
		if($(".deal_vars tbody td").length==0)
		{
			alert("add some deals please");
			return false;
		}
		return true;
	});
	$("#deal_srch_inp").keyup(function(){
		$.post("<?=site_url("admin/jx_search_deals")?>",{q:$(this).val()},function(data){
			$("#deal_srch_res").html(data).show();
		});
	}).focus(function(){
		if($("#deal_srch_res a").length!=0)
			$("#deal_srch_res").show();
	});
});
</script>

</div>
<?php
