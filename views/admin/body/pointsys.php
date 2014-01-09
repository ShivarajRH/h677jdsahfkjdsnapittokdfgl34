<div class="container">
<h2>Loyalty Points System</h2>

<table id="ps_clone" style="display:none">
<tr>
	<td>Rs <input type="text" name="amount[]" class="inp"></td><td><input class="inp" type="text" name="points[]"></td>
</tr>
</table>

<form method="post">

<div align="right" style="width:400px;">
<span style="float:left;">
	<input type="button" onclick='$("#ps_define .qe_all_trig").click();' value="Edit">
</span>
<input type="button" value="+" style="font-size:150%;padding:0px;font-weight:bold;height:25px;" onclick='clone()'>
<table border=1 cellspacing=0 cellpadding=7 width="100%" class="datagrid" id="ps_define">
<tr>
	<th>Amount</th><th>Points</th>
</tr>
<tr>
	<td>
		<a href="javascript:void(0)" class="qe_all_trig"></a>
	Rs 0</td><td>0 points</td>
</tr>
<?php foreach($sys as $s){?>
<tr>
	<td>Rs <span><?=$s['amount']?></span><input type="text" name="amount[]" class="inp inp_qe" value="<?=$s['amount']?>"></td>
	<td><span><?=$s['points']?> points</span><input class="inp inp_qe" type="text" name="points[]" value="<?=$s['points']?>"></td>
</tr>
<?php }?>
</table>
</div>

<input type="submit" value="Submit" style="margin-top:20px;">

</form>

</div>
<script>

function clone()
{
	$("#ps_define").append($("#ps_clone").html());
}

</script>
<?php
