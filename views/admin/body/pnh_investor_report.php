<div class="container">

<h2>PNH Sales Report for <select id="sel_f">
<option value="0">All Franchises</option>
<?php foreach($this->db->query("select franchise_id as id,concat(franchise_name,', ',city) as name from pnh_m_franchise_info order by name asc")->result_array() as $f){?>
<option value="<?=$f['id']?>" <?=$this->uri->segment(3)==$f['id']?"selected":""?>><?=$f['name']?></option>
<?php }?>
</select></h2>


<div style="background:#eee;padding:15px;" class="investor_report">

	<div>
		<table width="100%">
		<tr>
			<td width="25%"><h4>Total Orders</h4><h3><?=number_format($report['orders'])?></h3></td>
			<td width="25%"><h4>Orders this month</h4><h3><?=$report['m_orders']?></h3></td>
			<td width="25%"><h4>Avg Order value</h4><h3>Rs <?=number_format($report['a_orders'],2)?></h3></td>
			<td width="25%"><h4>Avg Order Items</h4><h3><?=number_format($report['a_items'],2)?></h3></td>
		</tr>
		</table>
	</div>
	
	
	<div>
		<table width="100%">
		<tr>
			<td width="50%"><h4>Total Sales</h4><h3>Rs <?=number_format($report['sales'],0)?></h3></td>
			<td width="50%"><h4>Sales this month</h4><h3>Rs <?=number_format($report['m_sales'])?></h3></td>
		</tr>
		</table>
	</div>
	
	<div>
		<table width="100%">
		<tr>
			<td width="50%"><h4>Order Value This Month Vs Last Month</h4><h3><?=number_format($report['c_a_orders'])?>%</h3></td>
			<td width="50%"><h4>No of orders This Month Vs Last Month</h4><h3><?=number_format($report['c_n_orders'])?>%</h3></td>
		</tr>
		</table>
	</div>
	
	<div>
		<table width="100%">
		<tr>
			<td width="50%"><h4>Total Commission</h4><h3>Rs <?=number_format($report['comm'],0)?></h3></td>
			<td width="50%"><h4>Commission this month</h4><h3>Rs <?=number_format($report['m_comm'])?></h3></td>
		</tr>
		</table>
	</div>
	
	<div>
		<table width="100%">
		<tr>
			<td width="25%"><h4>Total Credit Transactions</h4><h3><?=number_format($report['credits'],2)?></h3></td>
			<td width="25%"><h4>Total Debit Transactions</h4><h3><?=number_format($report['debits'],2)?></h3></td>
			<td width="25%"><h4>Credit Transactions this month</h4><h3>Rs <?=number_format($report['m_credits'],2)?></h3></td>
			<td width="25%"><h4>Debit Transactions this month</h4><h3><?=number_format($report['m_debits'],2)?></h3></td>
		</tr>
		</table>
	</div>
	
	<div>
		<table width="100%">
		<tr>
			<td width="25%"><h4>Total Topup Amount</h4><h3><?=number_format($report['topup'])?></h3></td>
			<td width="25%"><h4>Total Topup Amount Realised</h4><h3><?=number_format($report['r_topup'])?></h3></td>
			<td width="25%"><h4>Topup amount this month</h4><h3>Rs <?=number_format($report['m_topup'])?></h3></td>
			<td width="25%"><h4>Topup amount realised this month</h4><h3><?=number_format($report['m_r_topup'])?></h3></td>
		</tr>
		</table>
	</div>
	
	<div style="border:0px;">
		<table width="100%">
		<tr>
			<td width="50%"><h4>Total Members</h4><h3><?=number_format($report['users'])?></h3></td>
			<td width="50%"><h4>Members this month</h4><h3><?=number_format($report['m_users'])?></h3></td>
		</tr>
		</table>
	</div>

</div>

</div>

<style>
.investor_report div{
padding:25px 10px;
border-bottom:1px solid #ccc;
}
.investor_report td{
text-align:center;
}
.investor_report td p{
display:inline-block;
font-size:100%;
background:#aaa;
margin:0px;
}
.investor_report td h4{
margin:0px;
font-size:120%;
margin-bottom:5px;
}
.investor_report td h3{
margin:5px;
min-width:100px;
border-radius:10px;
font-size:250%;
display:inline-block;
background:#ea5;
padding:5px 10px;
}
</style>

<script>
$(function(){

	$("#sel_f").change(function(){
		location='<?=site_url("admin/pnh_investor_report")?>/'+$(this).val();
	});
});
</script>

</div>

<?php
