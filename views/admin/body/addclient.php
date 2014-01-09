<?php 
$c=false;if(isset($client)) $c=$client;
?>
<div class="container">
<h2><?=$c?"Edit/View":"Add new"?> client</h2>
<?php if($c){?>
<a href="<?=site_url("admin/addclientorder/{$c['client_id']}")?>">Create new order</a>
<?php }?>
<form method="post">
<div class="tabs">
<ul>
	<li><a href="#basicinfo">Basic Info</a></li>
	<li><a href="#financials">Financials</a></li>
	<li><a href="#contacts">Contacts</a></li>
</ul>
<div id="basicinfo">
	<table>
	<tr><td>Name :</td><td><input type="text" name="name" value="<?=$c?$c['client_name']:""?>"></td></tr>
	<tr><td>Address (line 1) :</td><td><input type="text" name="addr1" size="40" value="<?=$c?$c['address_line1']:""?>"></td></tr>
	<tr><td>Address (line 2) :</td><td><input type="text" name="addr2" size="40" value="<?=$c?$c['address_line2']:""?>"></td></tr>
	<tr><td>Locality :</td><td><input type="text" name="locality" size="25" value="<?=$c?$c['locality']:""?>"></td></tr>
	<tr><td>City :</td><td><input type="text" name="city" value="<?=$c?$c['city_name']:""?>"></td></tr>
	<tr><td>State :</td><td><input type="text" name="state" value="<?=$c?$c['state_name']:""?>"></td></tr>
	<tr><td>Country :</td><td><input type="text" name="country" value=" value="<?=$c?$c['client_name']:"India"?>""></td></tr>
	<tr><td>Remarks :</td><td><input type="text" name="remarks" value="<?=$c?$c['remarks']:""?>"></td></tr>
	</table>
</div>

<div id="financials">
<table>
<tr><td>Credit Limit</td><td>Rs <input type="text" name="credit_limit" size=6 value="<?=$c?$c['credit_limit_amount']:""?>"></td></tr>
<tr><td>Credit Days</td><td><input type="text" name="credit_days" size=3 value="<?=$c?$c['credit_days']:""?>"></td></tr>
<tr><td>CST No</td><td><input type="text" name="cst" value="<?=$c?$c['cst_no']:""?>"></td></tr>
<tr><td>PAN No</td><td><input type="text" name="pan" value="<?=$c?$c['pan_no']:""?>"></td></tr>
<tr><td>VAT No</td><td><input type="text" name="vat" value="<?=$c?$c['vat_no']:""?>"></td></tr>
<tr><td>Service tax No</td><td><input type="text" name="tax" value="<?=$c?$c['service_tax_no']:""?>"></td></tr>
</table>
</div>

<div id="contacts">
<input type="button" value="+ contact" id="addcontact">
<div id="contacts_cont">
<?php if($c){ foreach($contacts as $cnt){?>
	<div style="margin:5px;padding:7px;border:1px solid #aaa;display:inline-block;">
		<table>
			<tr><td>Name :</td><td><input type="text" name="c_name[]" value="<?=$cnt['contact_name']?>"></td></tr>
			<tr><td>Designation :</td><td><input type="text" name="c_design[]" value="<?=$cnt['contact_designation']?>"></td></tr>
			<tr><td>Mobile 1 :</td><td><input type="text" name="c_mob1[]" value="<?=$cnt['mobile_no_1']?>"></td></tr>
			<tr><td>Mobile 2 :</td><td><input type="text" name="c_mob2[]" value="<?=$cnt['mobile_no_2']?>"></td></tr>
			<tr><td>Email 1 :</td><td><input type="text" name="c_email1[]" value="<?=$cnt['email_id_1']?>"></td></tr>
			<tr><td>Email 2 :</td><td><input type="text" name="c_email2[]" value="<?=$cnt['email_id_2']?>"></td></tr>
			<tr><td>Fax :</td><td><input type="text" name="c_fax[]" value="<?=$cnt['fax_no']?>"></td></tr>
		</table>
	</div>
<?php }}?>
</div>
</div>

</div>
<input type="submit" value="<?=$c?"Update":"Add"?> client" style="font-weight:bold;font-size:120%;">
</form>

<div id="temp_contact" style="display:none;">
	<div style="margin:5px;padding:7px;border:1px solid #aaa;display:inline-block;">
		<table>
			<tr><td>Name :</td><td><input type="text" name="c_name[]"></td></tr>
			<tr><td>Designation :</td><td><input type="text" name="c_design[]"></td></tr>
			<tr><td>Mobile 1 :</td><td><input type="text" name="c_mob1[]"></td></tr>
			<tr><td>Mobile 2 :</td><td><input type="text" name="c_mob2[]"></td></tr>
			<tr><td>Email 1 :</td><td><input type="text" name="c_email1[]"></td></tr>
			<tr><td>Email 2 :</td><td><input type="text" name="c_email2[]"></td></tr>
			<tr><td>Fax :</td><td><input type="text" name="c_fax[]"></td></tr>
		</table>
	</div>
</div>

</div>

<script>
$(function(){
	$("#addcontact").click(function(){
		$("#contacts_cont").append($("#temp_contact").html());
	})<?php if(!$c){?>.click()<?php }?>;
});
</script>

<?php
