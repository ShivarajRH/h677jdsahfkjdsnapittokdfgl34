<script>
$(function(){
	$("#startdate, #enddate").datepicker({showOn: 'both', dateFormat: 'yy-mm-dd' , buttonImage: '<?=base_url()?>/images/calendar_old.png', buttonImageOnly: true});
});
</script>
<div class="headingtext">Sales Report</div>
<div style="padding:10px;font-size:13px;" align="left">
<form action="<?=site_url("salesreport")?>" method="post">
Generate sales report for date from <input size="10" value="<?=$this->input->post("start")?>" type="text" name="start" id="startdate"> to <input type="text" name="end" id="enddate" value="<?=$this->input->post("end")?>" size="10"> <input type="submit" value="Go">
</form>
</div>
<style>
.salesrep{
width:100%;
font-size:13px;
}
.salesrep td{
vertical-align:top;
}
</style>
<?php if(isset($salesrep)){?>
<div style="padding:20px 10px;">
<?php if(empty($salesrep)){?>
<h2>No transactions to show for selected date range</h2>
<?php }else{?>
<?php $t=0;foreach($salesrep as $sr) $t+=$sr['com'];?>
<div align="right" style="padding-bottom:10px;font-size:14px;">Total Commission from these sales : Rs <b><?=$t?></b></div>
<table border="1" class="salesrep" cellspacing="0" cellpadding="5">
<tr>
<th>S.No</th>
<th>Order ID</th>
<th>VIA Trans ID</th>
<th>Product Name</th>
<th>Qty</th>
<th>Price</th>
<th>Comm.</th>
<th>Paid</th>
<th>Shipped to</th>
<th>Shipped on</th>
<th>Order Date</th>
</tr>
<?php foreach($salesrep as $i=>$sr){?>
<tr>
<td><?=($i+1)?></td>
<td><?=$sr['orderid']?></td>
<td><?=$sr['via_transid']?></td>
<td><?=$sr['name']?></td>
<td><?=$sr['qty']?></td>
<td><?=$sr['price']?></td>
<td><b><?=$sr['com']?></b></td>
<td><b><?=$sr['paid']?></b></td>
<td>
<?=$sr['ship_person']?><br>
<?=nl2br($sr['ship_address'])?><br>
<?=$sr['ship_city']." ".$sr['ship_pincode']?>
</td>
<td>pending</td>
<td><?=date("g:ia d/m/y",$sr['time'])?></td>
</tr>
<?php }?>
</table>
<?php }?>
</div>
<?php }?>
<?php
