<div class="container" id="createclientinvoice">
<h2>Create client invoice</h2>
Select client : 
<select id="client_select">
<option value="0">select</option>
<?php foreach($clients as $c){?>
<option value="<?=$c['client_id']?>"><?=$c['client_name']?>, <?=$c['city_name']?></option>
<?php }?>
</select> <input type="button" value="Load orders" id="client_select_but">

<div id="load_orders" class="ajax_loadresult" style="display:none;padding:10px;background:#eee;"></div>


<form method="post" id="ci_form">
<input type="hidden" name="client" id="inp_client" value="">

<table class="datagrid" id="pprods" style="margin:10px;">
<thead>
<tr><th>Product</th><th>MRP</th><th>Offer price</th><th>Tax</th><th>Ordered Qty</th><th>Invoiced Qty</th><th>Stock</th><th>Qty to invoice</th></tr>
</thead>
<tbody>
</tbody>
</table>

<div style="padding:10px;">
Invoice Date : <input type="text" class="inp" size=12 id="invoice_date" name="invoice_date">
</div>

<input type="submit" value="Create Invoice">

</form>

<div id="template" style="display:none">
<table>
<tbody>
<tr>
<td><input type="hidden" name="products[]" value="%prodid%">
<input type="hidden" name="order_id[]" id="inp_order" value="%order%">
%prod%
</td>
<td>Rs <input type="text" class="inp" name="mrp[]" size=4 value="%mrp%"></td>
<td>Rs <input type="text" class="inp offer" name="offer[]" size=4 value=""></td>
<td><input type="text" class="inp" name="tax[]" size=3 value="%tax%">%</td>
<td>%oqty%</td>
<td>%iqty%</td>
<td class="stock">%stock%</td>
<td><input type="text" class="inp iqty readonly%rclass%" name="iqty[]" size=3 value="0" alt="%readonly%"></td>
</tr>
</tbody>
</table>
</div>

</div>
<?php 
$mindate=$this->db->query("select invoice_date from t_client_invoice_info order by invoice_date desc limit 1")->row_array();
if(empty($mindate))
	$mindate="2009-01-01";
else 
	$mindate=$mindate['invoice_date'];
list($mindate)=explode(" ",$mindate);
$mindate=explode("-",$mindate);
?>

<script>
var cid=0;
var loaded=[];
function loadorder(oid){
	if($.inArray(oid,loaded)!=-1)
	{
		alert("Order already loaded");
		return;
	}
	loaded.push(oid);
	$.post("<?=site_url("admin/jx_loadclientordersforinvoice")?>",{oid:oid},function(data){
		obj=$.parseJSON(data);
		$.each(obj,function(i,o){
			$("#inp_order").val(o.order_id);
			temp=$("#template table tbody").html();
			temp=temp.replace(/%order%/g,o.order_id);
			temp=temp.replace(/%prodid%/g,o.product_id);
			temp=temp.replace(/%prod%/g,o.product);
			temp=temp.replace(/%mrp%/g,o.mrp);
			temp=temp.replace(/%oqty%/g,o.order_qty);
			temp=temp.replace(/%iqty%/g,o.invoiced_qty);
			temp=temp.replace(/%tax%/g,o.tax);
			temp=temp.replace(/%stock%/g,o.stock);
			if(o.stock==0)
			{
				temp=temp.replace(/%readonly%/g,'" readonly="readonly');
				temp=temp.replace(/%rclass%/g,' ');
			}
			else
				temp=temp.replace(/%readonly%/g,' ');
			$("#pprods tbody").append(temp);
		});
	});
}

$(function(){
	$("#invoice_date").datepicker({minDate : new Date(<?=$mindate[0]?>,<?=$mindate[1]-1?>,<?=$mindate[2]?>)});
	$("#client_select_but").click(function(){
		if($("#client_select").val()==0)
		{	alert("Please select client");return false;	}
		$(this).attr("disabled",true);
		cid=$("#client_select").val();
		$("#inp_client").val(cid);
		$.post("<?=site_url("admin/jx_listclientordersforinvoice")?>",{cid:cid},function(data){
			$("#load_orders").html(data).show();
		});
	}).attr("disabled",false);
});


</script>

<?php
