<?php 
$clonecont = <<<EOD
		<table>
			<tr>
				<td colspan=3 align="right"><a href="javascript:void(0)" onclick='$(this).parent().parent().parent().parent().remove()'>remove</a></td>
			</tr>
			<tr>
				<td>Coupon Value</td><td>:</td><td><input type="text" name="value[]" size=5 value="P_value"></td>
			</tr>
			<tr>
				<td>Valid upto</td><td>:</td><td><input type="text" name="valid[]" size=5 value="P_valid"> days</td>
			</tr>
			<tr>
				<td>Minimum Order</td><td>:</td><td>Rs <input type="text" name="min[]" size=5 value="P_min_order"></td>
			</tr>
		</table>
EOD;
?>
<div class="container">
<h2>Define cashback</h2>
	<div id="clone">
<?php 
$p=array("valid"=>"","min_order"=>"","value"=>"");
	$t=$clonecont;
	foreach($p as $c=>$v)
		$t=str_replace("P_$c", $v, $t);
	echo $t;
?>
	</div>
<form method="post">
	<div>
		<input type="button" value="Add coupon" id="addclone" style="font-size:80%">
	</div>
	<div class="clone_target">
<?php 
foreach($pres as $p)
{
	$t=$clonecont;
	foreach($p as $c=>$v)
		$t=str_replace("P_$c", $v, $t);
	echo $t;
}
?>
	</div>
	<div style="clear:both;"></div>
	<div style="padding:5px;background:#eee;margin:10px 0px;">
		<input type="submit" value="Submit">
	</div>
</form>
</div>
<style>
#clone{
display:none;
}
.clone_target table{
	border:1px solid #aaa;
	padding:5px;
	margin:5px;
	float:left;
}
</style>
<script>
$(function(){
	$("#addclone").click(function(){
		$(".clone_target").append($("#clone").html());
		$("#clone").appendTo(".clone_target");
	})<?php if(empty($pres)){?>.click()<?php }?>;
});
</script>
<div style="padding:20px;">
&nbsp;
</div>
<?php
