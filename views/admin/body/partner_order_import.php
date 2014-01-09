<div class="container">

<h2>Partner Orders</h2>


<form id="frm_partorderimp" method="post" enctype="multipart/form-data" target="_blank">
<div style="color:#999;">Only CSV format supported<br></div>

<div style="margin:20px 0px 40px 40px;">
<div style="min-width:400px;display:inline-block;padding:10px;background:#eee;border:1px solid #aaa;margin:5px;">
Choose Partner : <select name="partner">
	<option value="">Choose</option>
<?php foreach($this->db->query("select * from partner_info order by name asc")->result_array() as $p){?>
<option value="<?=$p['id']?>"><?=$p['name']?></option>
<?php }?>
</select>
</div>
<br>
<INPUT TYPE="HIDDEN" NAME="ASDSDA">
Upload partner orders : <input type="file" name="pords"><br><br>
<b>Consider Shipping Charges</b> 
<input type="checkbox" name="cons_ship" value="1" checked="checked"> <br /><br /><br />
<input type="submit" value="Upload & Update">
</div>


</form>

<div style="overflow:auto;width:900px;">
<h3 style="margin-bottom:0px;">Template</h3>
<table class="datagrid nowrap">
<tr>
<th>Shipping Email</th><th>Notify User</th><th>User Notes</th><th>Product Id<th>	Product	Product<th> Qty<th>	Shipping Charges	<th>Biller Name	<th>Biller Address1	<th>Biller Address2	<th>Biller City	<th>Biller State	<th>Biller Country	<th>Biller Zipcode	<th>Biller Phone	<th>Biller Alt Phone	<th>Biller Mobile	<th>Shipping Name	<th>Shipping Address1	<th>Shipping Address2	<th>Shipping City	<th>Shipping State	<th>Shipping Country	<th>Shipping Zipcode	<th>Shipping Phone	<th>Shipping Alt Phone	<th>Shipping Mobile</th><th>Reference no</th><th>Transaction Mode (cod/pg)</th><th>Order Date (yyyy-mm-dd)</th><th>Courier Name</th><th>AWB NO</th><th>Net Amount</th><th>Partner Transaction refno</th>
</tr>
</table>
</div>


</div>

<script>
	$('#frm_partorderimp').submit(function(){
		if(!$('select[name="partner"]').val())
		{
			alert("Choose partner");
			return false;
		}
	});
	
</script>


<?php
