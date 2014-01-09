<script>
$(function(){
	$("#f_start,#f_end").datepicker({dateFormat:'dd-mm-yy'});
	$("#f_go").click(function(){
		if($("#f_start").val().length==0 || $("#f_start").val().length==0)
		{
			alert("Input!");
			return;
		}
		location="<?=site_url("admin/statistics")?>/"+$("#f_start").val()+"/"+$("#f_end").val();
	});
});
</script>
<?php
$analytics = array(); 
$analytics['total_success_trans'] = $strans;
$analytics['total_success_pg_trans'] = $spgs;
$analytics['total_success_cod_trans'] = $cods;
$analytics['total_shipped_orders'] = $sorders;
$analytics['total_pending_orders'] = $porders;
$analytics['total_orders'] = $analytics['total_shipped_orders']+$analytics['total_pending_orders'] ;

$analytics['total_amount'] = $total_amount;

?>

<div class="container" align="center" style="width:800px;margin:10px;">

	
	 
	<div style="background: #f7f7f7;width: 800px;">
		<table width=100%>
			<tr>
				<td>
					<b style="font-size: 14px;margin-left: 10px;">Analytics</b>
				</td>
				<td valign="top" align="right" style="padding:0px;" colspan=2 >
					<div style="font-size: 12px;">
					 
						<table>
							<tr>
								<td><b>From</b></td>
								<td>
									<input size="10" type="text" id="f_start" value="<?php echo $from;?>">
								</td>
							 
								<td><b>To</b></td>
								<td>
									<input size="10" type="text" id="f_end" value="<?php echo $to;?>">
								</td>
							 
								<td colspan="2" align="left">
									<input type="button" value="Go" id="f_go">
								</td>
							</tr>
						</table>	
					</div>
				</td>
			</tr>
			
			<tr>
				 
				<td valign="top" style="padding:0px 5px;">
					
					<table width="100%"  style="background:#fff;" cellpadding=5 cellspacing=0 class="datagrid">
						<tr>
							<th colspan=3>Transactions</th>
						</tr>
						<tr>
							<td>Payment Gateway</td>
							<td>:</td>
							<td><?=$analytics['total_success_pg_trans']?></td>
						</tr>
						<tr>
							<td>Cash on Delivery</td>
							<td>:</td>
							<td><?=$analytics['total_success_cod_trans']?></td>
						</tr>
						<tr style="background: #e3e3e3;">
							<td>Total Transactions</td>
							<td>:</td>
							<td><?=$analytics['total_success_trans']?></td>
						</tr>
					</table>
				</td>
				<td valign="top" style="padding:0px 5px;">
					<table width="100%"  style="background:#fff;" cellpadding=5 cellspacing=0 class="datagrid">
						<tr>
							<th colspan=3>Orders</th>
						</tr>
						<tr>
							<td>Shipped Orders</td>
							<td width="10">:</td>
							<td width="20"><?=$analytics['total_shipped_orders']?></td>
						</tr>
						<tr>
							<td>Pending Orders</td>
							<td>:</td>
							<td><?=$analytics['total_pending_orders']?></td>
						</tr>
						<tr style="background: #e3e3e3;">
							<td>Total Orders</td>
							<td>:</td>
							<td><?=$analytics['total_orders']?></td>
						</tr>
					</table>
				</td>
				<td valign="top" style="padding:0px 5px;" align="center" width="200px;">
					 <div style="height: 45px;background: #ffffa0;padding:30px;font-size: 16px;border:1px dotted #ccc">
					 	<b>Total Amount</b> <br />
					 	<span style="font-size: 18px;"><?php echo 'Rs '.number_format($analytics['total_amount']);?></span> 
					 </div>
				</td>
			</tr>
			<tr>
				 
				<td colspan="1" valign="top" style="padding:5px;">
					<table style="background:#fff;" cellpadding=5 cellspacing=0 width="100%" class="datagrid">
						<tr>
							<th colspan=3>Top 10 Cities</th>
						</tr>
						<tr style="background: #fffff0 !important;color: brown">
							<td><b>City name</b></td>
							<td>&nbsp;</td>
							<td><b>Orders</b></td>
						</tr>
						<?php 
							foreach($top_ten_city as $topcity){
						?>
						<tr>
							<td><?php echo ucwords($topcity['ship_city']);?></td>
							<td width="10">:</td>
							<td width="50"><?=$topcity['total']?></td>
						</tr>
						<?php } ?>
						 
					</table>
				</td>
				<td colspan="2" valign="top" style="padding:5px;" >
					<table style="background:#fff;" cellpadding=5 cellspacing=0 width="100%" class="datagrid">
						<tr>
							<th colspan=3>Product Sales summary</th>
						</tr>
						<tr>
							<td width="30"><b>Popular</b></td>
							<td width="10">:</td>
							<td width="300"><?=$mostpopular?></td>
						</tr>
						<tr>
							<td><b>Top Sold</b></td>
							<td width="10">:</td>
							<td ><?=$mostbought?></td>
						</tr> 
					</table>
					<br />
					<table id="calender_view" style="background:#fff;" cellpadding=5 cellspacing=0 width="100%" class="datagrid">
						<tr id="calender_header">
							<th colspan=3>Calendar</th>
							<th colspan="1" align="right">
								<a href="javascript:void(0)" style="color: #FFF;font-size: 14px;" onclick="get_monthsummary(1);" id="prev_monthdet">&lt;</a>
								&nbsp;
								<a href="javascript:void(0)" style="color: #FFF;font-size: 14px;" onclick="get_monthsummary(-1);" id="next_monthdet">&gt;</a>
							</th>
						</tr>
						<tr style="font-weight: bold;font-size: 11px;">
							<td width="50" >
								&nbsp;
							</td>
							<td width="100" id="cal_month_1" align="left">&nbsp;</td>
							<td width="100"  id="cal_month_2" align="left">&nbsp;</td>
							<td width="100"  id="cal_month_3" align="left">&nbsp;</td> 
						</tr>
						<tr>
							<td><b>Transactions</b></td>
							<td id="cal_trans_1">&nbsp;</td>
							<td id="cal_trans_2">&nbsp;</td>
							<td id="cal_trans_3">&nbsp;</td>
						</tr> 
						<tr>
							<td><b>Orders</b></td>
							<td id="cal_orders_1">&nbsp;</td>
							<td id="cal_orders_2">&nbsp;</td>
							<td id="cal_orders_3">&nbsp;</td>
						</tr>
						<tr>
							<td><b>Amount</b></td>
							<td id="cal_amount_1">&nbsp;</td>
							<td id="cal_amount_2">&nbsp;</td>
							<td id="cal_amount_3">&nbsp;</td>
						</tr> 
					</table>
				</td>
			</tr>
		</table>
	</div>
</div>
 
 <style>
.datagrid th{
background:#777;
color:#fff;
font-size: 12px;
}
 
.datagrid td{
	border-bottom:1px dotted #e3e3e3;
}
</style>
 <script type="text/javascript">
 var cur = 0;
	function get_monthsummary(pagi){
		cur = cur+pagi;
		if(cur < -1){
			cur = -1; 
		}
		$.getJSON(site_url+'/admin/get_monthsummary/'+cur,'',function(resp){
			 
			 $.each(resp.summary_det,function(i,item){
				 
				$('#cal_month_'+(i+1)).html(item.month.substr(0,3)+' '+item.year);
				$('#cal_trans_'+(i+1)).html(item.total_trans);
				$('#cal_orders_'+(i+1)).html(item.total_orders);
				$('#cal_amount_'+(i+1)).html('Rs '+item.total_amount);
				
			 });
		});
		
		
	}
	get_monthsummary(-1);


	 
	
 </script>
<?php
