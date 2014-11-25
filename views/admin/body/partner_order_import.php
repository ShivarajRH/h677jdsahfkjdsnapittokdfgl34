<div class="container">

<h2>Partner Orders</h2>


<form id="frm_partorderimp" method="post" enctype="multipart/form-data" target="_blank">
<div style="color:#999;">Only CSV format supported<br></div>

<div style="margin:20px 0px 40px 40px;">
<div style="min-width:400px;display:inline-block;padding:10px;background:#eee;border:1px solid #aaa;margin:5px;">
Choose Partner : 
<select name="partner" onchange="return fn_change_partner(this)">
	<option value="">Choose</option>
	<?php foreach($this->db->query("select * from partner_info order by name asc")->result_array() as $p){?>
	<option value="<?=$p['id']?>" loc="<?=$p['partner_rackbinid']?>"><?=$p['name']?></option>
	<?php }?>
</select>
</div>
<br>
<INPUT TYPE="HIDDEN" NAME="ASDSDA">
Upload partner orders : <input type="file" name="pords" id="pords"><br><br>
<b>Consider Shipping Charges</b> 
<input type="checkbox" name="cons_ship" value="1" checked="checked"> <br /><br /><br />

<div class="gen_inv_block" style="display: none;">
<b>Generate Invoice?</b>
<input type="radio" name="gen_invoice" value="1">Yes 
<input type="radio" name="gen_invoice" value="0">No <br /><br /><br />
</div>

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
		
		if($('.gen_inv_block:visible').length) {
			if( !$('input[name="gen_invoice"]').is(':checked') )
			{
				alert("Do you want to generate Invoice?");
				return false;
			}
		}
		var pords_file=document.getElementById('pords');
		if(pords_file.value =='') {
			alert("Please select file to import.");
			return false;
		}
			
		if(confirm("Do you want to proceed importing orders?"))
			return true;
		return false;
	});


	
	function fn_change_partner(elt)
	{
		var partner_id=$(elt).val();
		var partner_rackbinid =$('option:selected', elt).attr("loc");
		
		// reset field
		$('input[name="gen_invoice"]').attr("checked",false);
		
		if( partner_rackbinid!=0 ) {
			$(".gen_inv_block").show();
		}
		else {
			$(".gen_inv_block").hide();
		}
		return true;
	}
</script>


<?php
