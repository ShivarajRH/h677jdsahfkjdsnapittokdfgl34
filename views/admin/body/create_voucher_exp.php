<div class="container">
<h2>Create voucher for a expense</h2>
<form method="post">
<table style="background:#FFFFEF;padding:5px;" cellpadding=4>
<tr><td>Bill Number:</td><td><input type="text" class="inp" name="bill"></td></tr>
<tr><td>Expense Type:</td><td>
<select name="type">
<?php foreach(array("Staff welfare","Printing & Stationery","Vehicle maintenance","Courier & postal","Traveling","OPEX","CAPEX","Others") as $i=>$t){?>
<option value="<?=$i?>"><?=$t?></option>
<?php }?>
</select>
</td></tr>
<tr class="aftrvload"><td>Voucher Value</td><td>Rs <input size=9 type="text" class="vvalue" name="vvalue"></td></tr>
<tr class="aftrvload"><td>Payment Mode</td><td>
<select name="mode">
<option value="1">Cash</option>
<option value="2">Cheque</option>
<option value="3">DD</option>
<option value="4">Bank Transfer</option>
</select>
</td></tr>
<tr class="aftrvload"><td>Instrument No</td><td><input type="text" name="inst_no"></td></tr>
<tr class="aftrvload"><td>Instrument Date</td><td><input type="text" name="inst_date" class="idate"></td></tr>
<tr class="aftrvload"><td>Issued Bank</td><td><input type="text" name="bank"></td></tr>
<tr class="aftrvload"><td>Narration</td><td><input type="text" name="narration" style="width:250px;"></td></tr>
</table>
<input type="submit" value="Create Voucher">
</form>
</div>
<script>
$(function(){
	$(".idate").datepicker();
});
</script>
<?php
