<div class="Container">
<h2>Franchise Class Config</h2>

<div id="tab_class">
<a href="javascript:void(0)" onclick='$("#add_class").show()'>Add new class</a>
<div id="add_class" style="display:none;padding:5px;margin:4px;">
<form method="post">
<input type="hidden" name="new" value="1">
<table>
<tr><td>Class name :</td><td><input type="text" name="class"></td></tr>
<tr><td>Margin :</td><td><input type="text" name="margin" size=3></td></tr>
<tr><td>Combo Margin :</td><td><input type="text" name="combo_margin" size=3></td></tr>
<tr><td>Less-Margin brands margin :</td><td><input type="text" name="less_margin_brands" size=3></td></tr>
<tr><td></td><td><input type="submit" value="Add Class">
</table>
</form>
</div>


<table class="datagrid" style="margin-top:20px;">
<thead><tr><th>Class  Name</th><th>Default Margin</th><th>Less-margin Brands</th><Th colspan=2>Combo margin</Th></tr></thead>
<tbody>
<?php foreach($class as $c){$o=$c;?>
<tr>
<td><?=$c['class_name']?></td><td><?=$c['margin']?></td>
<td><?=$c['less_margin_brands']?></td>
<td><?=$c['combo_margin']?></td>
<td><a href="javascript:void(0)" onclick='edit_class("<?=$o['id']?>","<?=$o['margin']?>","<?=$o['combo_margin']?>","<?=$o['less_margin_brands']?>")'>edit</a></td>
</tr>
<?php }?>
</tbody>
</table>
</div>

<form id="update_c_form" style="display:none" method="post">
<h2>Update form</h2>
<input type="hidden" name="id" class="uc_id">
<table>
<tr><td>Margin :</td><td><input type="text" name="margin" size=3 class="uc_margin"></td></tr>
<tr><td>Combo Margin :</td><td><input type="text" name="combo_margin" size=3 class="uc_cmargin"></td></tr>
<tr><td>Less-margin brands margin :</td><td><input type="text" name="less_margin" size=3 class="uc_lcmargin"></td></tr>
<tr><td></td><td><input type="submit" value="Update Class"></td></tr>
</table>
</form>

</div>
<script>
function edit_class(id,m,cm,lcm)
{
	$(".uc_id").val(id);
	$(".uc_margin").val(m);
	$(".uc_cmargin").val(cm);
	$(".uc_lcmargin").val(lcm);
	$("#tab_class").hide();
	$("#update_c_form").show();
}
</script>
<?php
