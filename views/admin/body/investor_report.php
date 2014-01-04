<div class="container">

<h2>Investor report</h2>


<div style="background:#eee;padding:15px;" class="investor_report">

	<div>
		<table width="100%">
		<tr>
			<td width="25%"><p><h4>Total Orders</h4><h3><?=number_format($report['orders'])?></h3></p></td>
			<td width="25%"><p><h4>Orders this month</h4><h3><?=$report['m_orders']?></h3></p></td>
			<td width="25%"><p><h4>Avg Order value</h4><h3>Rs <?=number_format($report['a_orders'],2)?></h3></p></td>
			<td width="25%"><p><h4>Avg Order Items</h4><h3><?=number_format($report['a_items'],2)?></h3></p></td>
		</tr>
		</table>
	</div>
	
	
	<div>
		<table width="100%">
		<tr>
			<td width="50%"><p><h4>Total Sales</h4><h3>Rs <?=number_format($report['sales'],0)?></h3></p></td>
			<td width="50%"><p><h4>Sales this month</h4><h3>Rs <?=number_format($report['m_sales'])?></h3></p></td>
		</tr>
		</table>
	</div>
	
	<div>
		<table width="100%">
		<tr>
			<td width="50%"><p><h4>Total Stock Value</h4><h3>Rs <?=number_format($report['stock'],0)?></h3></p></td>
			<td width="50%"><p><h4>Stock purchase this month</h4><h3>Rs <?=number_format($report['m_stock'])?></h3></p></td>
		</tr>
		</table>
	</div>
	
	<div style="border:0px;">
		<table width="100%">
		<tr>
			<td width="50%"><p><h4>Total Registered Users</h4><h3><?=number_format($report['users'])?></h3></p></td>
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
<?php
