<html>
<head><title>Print</title></head>
<h1><?php echo "Super Scheme Sales Summary Of ".$fran['franchise_name']." Target value set:".$super_scheme_details['target_value']." Rs"." Cash back value:".$super_scheme_details['credit_prc'].'%' ?></h1>
<body style="font-size:12px;font-family:arial;">
<?php if($sales_res->num_rows()){?>

<table cellpadding=5 width="100%" border=1>
<thead><tr><th width=40>Sno</th><th>Product</th><th>Quantity</th><th>Cost</th><th>Orderd Date</th></tr></thead>
<tbody>
<?php $i=1; foreach($sales_res->result_array() as $f){?>

<tr>
<td><?=$i++?></td>

<td><?php echo $f['deal']?></td>
<td><?php echo $f['deal_qty']?></td>
<td><?php echo $f['landing_cost']?></td>
<td><?php echo $f['order_date']?></td>
</tr>
</tbody>
<?php }?>
</table>
<h3>Total Sales acheived till date:</h3>
<?php }?>
</body>
</html>

