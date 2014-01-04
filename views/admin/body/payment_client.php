<div class="container">
<h2>Client Invoice Payment</h2>
<form method="post">
<table class="datagrid">
<tbody>
<tr><td>Amount :</td><td><input type="text" name="amount" size=4></td></tr>
<tr><td>Payment Type :</td><td><select name="type"><option value="1">Cash</option><option value="2">Cheque</option><option value="3">Transfer</option><option value="4">DD</option></select></td></tr>
<tr><td>Instrument No:</td><td><input type="text" name="inst_no" class="inp"></td></tr>
<tr><td>Instrument Date:</td><td><input type="text" name="inst_date" id="inst_date" class="inp"></td></tr>
<tr><td>Bank :</td><td><input type="text" name="bank" class="inp"></td></tr>
<tr><td>Is Cleared :</td><td><input type="checkbox" name="cleared" value="1"></td></tr>
<tr><td>Remarks :</td><td><input type="text" name="remarks" size=30 class="inp"></td></tr>
<tr><td>Payment Finished :</td><td><input type="checkbox" name="done" value="1"></td></tr>
</tbody>
</table>
<div style="padding-top:10px;">
<input type="submit" value="Add Payment">
</div>
</form>

</div>
<script>
$(function(){
	$("#inst_date").datepicker();
});
</script>

<?php
