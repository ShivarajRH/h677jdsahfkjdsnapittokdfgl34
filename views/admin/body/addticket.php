<?php
$trans=false; 
if($this->uri->segment(3))
	$trans=$this->db->query("select distinct(o.transid) as transid, u.name,u.mobile,u.email from king_orders o join king_users u on u.userid=o.userid where o.transid=?",$this->uri->segment(3))->row_array();
?>
<div class="container">
<h2>Add new support ticket</h2>

<form method="post">
<table cellpadding=7>
<tr><td>Name :</td><td><input type="text" class="inp" name="name" size=30 value="<?=$trans?$trans['name']:""?>"></td></tr>
<tr><td>Email :</td><td><input type="text" class="inp" name="email" size=50 value="<?=$trans?$trans['email']:""?>"></td></tr>
<tr><td>Mobile :</td><td><input type="text" class="inp" name="mobile" size=20 value="<?=$trans?$trans['mobile']:""?>"></td></tr>
<tr><td>Transaction ID :</td><td><input type="text" class="inp" name="transid" value="<?=$trans?$trans['transid']:""?>"></td></tr>
<tr><td>Type :</td><td><select name="type">
	<option value="0">Query</option>
	<option value="1">Order Issue</option>
	<option value="2">Bug</option>
	<option value="3">Suggestion</option>
	<option value="4">Common</option>
	<option value="5">PNH Returns</option>
	<option value="6">Courier Followups</option>
	<!--new services-->
	<option value="10">Request</option>
	<option value="11">Complaint</option>
</select></td></tr>
<tr>
	<td>Related To :</td><td><select name="related_to">
		<!--new services-->
		<option value="0">Not specific</option>
		<option value="1">Product</option>
		<option value="2">Payment</option>
		<option value="3">Shipment</option>
		<option value="4">Insurance</option>
		<option value="5">Technical</option>
	</select></td>
</tr>
<tr><td>Medium :</td><td><select name="medium">
<option value="0">Email</option>
<option value="1">Phone</option>
<option value="2">Other</option>
</select></td></tr>
<tr><td>Priority :</td><td><select name="priority">
<option value=0>Low</option>
<option value=1>Medium</option>
<option value=2>High</option>
<option value=3>Urgent</option>
</select>
</td></tr>
<tr><td>Message :</td><td><textarea name="msg" cols=90 rows=20></textarea></td></tr>
<tr><td></td><td><input type="submit" value="Add ticket"></td></tr>
</table>
</form>

</div>
<?php
