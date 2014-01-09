<div class="container">

<div>
	<div class="dash_bar">
	<span><?=$this->db->query("select count(1) as l from t_client_invoice_info")->row()->l?></span>
		Total Invoices
	</div>
	<div class="dash_bar">
	<span><?=$this->db->query("select count(1) as l from t_client_invoice_info where payment_status=0 || payment_status=1")->row()->l?></span>
		Payment Pending
	</div>
	<div class="dash_bar">
	<span><?=$this->db->query("select count(1) as l from t_client_invoice_info where payment_status=2")->row()->l?></span>
		Expense Vouchers
	</div>
	<div class="dash_bar">
		Date range: <input type="texT" size="8" class="inp" id="ds_range" value="<?=$this->uri->segment(3)?>"> to <input size="8" type="text" class="inp"id="de_range" value="<?=$this->uri->segment(4)?>"> <input type="button" value="Show" onclick='showrange()'>
	</div>
<div class="clear"></div>
</div>


<h2>Client Invoices <?=!isset($pagetitle)?"this month":$pagetitle?></h2>
<a href="<?=site_url("admin/createclientinvoice")?>">Create new invoice</a>

<table class="datagrid">
<thead><tr><th>Invoice No</th><th>Status</th><th>Payment Status</th><th>Created By</th><th>Created On</th></thead>
<tbody>
<?php foreach($invoices as $inv){?>
<tr>
<td><a class="link" href="<?=site_url("admin/client_invoice/{$inv['invoice_id']}")?>"><?=$inv['invoice_no']?></a></td>
<td><?php 
switch($inv['invoice_status']){
	case 0:
		echo "Not packed / pending";
		break;
	case 1:
		echo "Active";
		break;
}?></td>
<td><?php switch($inv['payment_status']){
	case 0:
		echo 'Pending';
		break;
	case 1:
		echo "Partial";
		break;
	case 2:
		echo "Complete";
		break;
}?> <a href="<?=site_url("admin/payment_client_invoice/{$inv['invoice_id']}")?>">update</a>
</td>
<td><?=$inv['created_by']?></td>
<td><?=$inv['created_date']?></td>
</tr>
<?php }if(empty($invoices)){?><tr><td colspan="100%">no invoices to show</td></tr><?php }?>
</tbody>
</table>

</div>



<script>
$(function(){
	$("#ds_range,#de_range").datepicker();
});
function showrange()
{
	if($("#ds_range").val().length==0 ||$("#ds_range").val().length==0)
	{
		alert("Pls enter date range");
		return;
	}
	location='<?=site_url("admin/client_invoices")?>/'+$("#ds_range").val()+"/"+$("#de_range").val(); 
}
</script>


<?php
