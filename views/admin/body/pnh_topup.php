<div class="container">

<h2>Topup for <?=$this->db->query("select franchise_name from pnh_m_franchise_info where franchise_id=?",$fid)->row()->franchise_name?></h2>

<form method="post">
<table cellpadding=5>
<tr><td>Amount :</td><td>Rs <input type="text" class="inp" name="amount" size=5></td></tr>
<tr><td>Bank name :</td><td><input type="text" name="bank" size=30></td></tr>
<tr><td>Instrument Type :</td><td><select name="type">
<option value="0">Cash</option>
<option value="1">Cheque</option>
<option value="2">DD</option>
<option value="3">Transfer</option>
</select></td></tr>
<tr><td>Instrument No :</td><td><input type="text" name="no" size=10></td></tr>
<tr><td>Instrument Date :</td><td><input type="text" name="date" id="sec_date" size=15></td></tr>
</table>
<input type="submit" value="Add Topup">
</form>

</div>

<script>
$(function(){
	$("#sec_date").datepicker();
});
</script>
<?php
