<div class="container">

<div>
	<div class="dash_bar">
	<span><?=$this->db->query("select count(1) as l from t_voucher_info")->row()->l?></span>
		Total Vouchers
	</div>
	<div class="dash_bar">
	<span><?=$this->db->query("select count(1) as l from t_voucher_info v join t_voucher_document_link d on d.voucher_id=v.voucher_id")->row()->l?></span>
		Purchase Vouchers
	</div>
	<div class="dash_bar">
	<span><?=$this->db->query("select count(1) as l from t_voucher_info v join t_voucher_expense_link e on e.voucher_id=v.voucher_id")->row()->l?></span>
		Expense Vouchers
	</div>
	<div class="dash_bar">
		Total Voucher Value : 
	<span>Rs <?=$this->db->query("select sum(voucher_value) as l from t_voucher_info")->row()->l?></span>
	</div>
	<div class="dash_bar">
		Total Purchase Value :
	<span>Rs <?=$this->db->query("select sum(voucher_value) as l from t_voucher_info v join t_voucher_document_link d on d.voucher_id=v.voucher_id")->row()->l?></span>
	</div>
	<div class="dash_bar">
		Total Expense Value :
	<span>Rs <?=$this->db->query("select sum(voucher_value) as l from t_voucher_info v join t_voucher_expense_link e on e.voucher_id=v.voucher_id")->row()->l?></span>
	</div>
<div class="clear"></div>
</div>

<h2>Vouchers <?=isset($pagetitle)?"$pagetitle":" this month"?></h2>

<fieldset style="float:left;margin-right:20px;">
<legend>Create voucher</legend>
<a href="<?=site_url("admin/create_voucher")?>">For a purchase</a>&nbsp; &nbsp;/ &nbsp;
<a href="<?=site_url("admin/create_voucher_exp")?>">For a expense</a>
</fieldset>

<div class="dash_bar">
Date range: <input type="texT" size="8" class="inp" id="ds_range" value="<?=$this->uri->segment(3)?>"> to <input size="8" type="text" class="inp"id="de_range" value="<?=$this->uri->segment(4)?>"> <input type="button" value="Show" onclick='showrange()'>
</div>

<div class="clear"></div>

<table class="datagrid" style="margin-top:20px;">
<thead>
<tr><th>ID</th><th>Type</th><th>Date</th><th>Value</th><th>Payment mode</th><th>Instrument No</th><th>Instrument Date</th><th>Bank</th><th>Narration</th><th>Created On</th><th>Created By</th></tr>
</thead>
<tbody>
<?php foreach($vouchers as $v){?>
<tr>
<td><a href="<?=site_url("admin/voucher/{$v['voucher_id']}")?>" class="link"><?=$v['voucher_id']?></td>
<td><?=$v['voucher_type_id']==1?"Payment":"Receipt"?></td>
<td><?=$v['voucher_date']?></td>
<td>Rs <?=$v['voucher_value']?></td>
<td><?php switch($v['payment_mode']){
	case 1:
		echo "Cash";break;
	case 2:
		echo "Cheque";break;
	case 3:
		echo "DD";break;
	case 4:
		echo "Bank Transfer";break;
}?>
</td>
<td><?=$v['instrument_no']?></td>
<td><?=$v['instrument_date']?></td>
<td><?=$v['instrument_issued_bank']?></td>
<td><?=$v['narration']?></td>
<td><?=$v['created_on']?></td>
<td><?=$this->db->query("select name from king_admin where id=?",$v['created_by'])->row()->name?></td>
</tr>
<?php } if(empty($vouchers)){?><tr>
<td colspan="100%">no vouchers to show</td>
</tr>
<?php }?>
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
	location='<?=site_url("admin/vouchers")?>/'+$("#ds_range").val()+"/"+$("#de_range").val(); 
}
</script>


<?php
