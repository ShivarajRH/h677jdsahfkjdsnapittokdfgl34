<html>
<head>
<title>Stock procure list</title>
</head>
<body onload='window.print()' style="font-family:arial;font-size:14px;">
<div style="float:right;color:#aaa;font-size:12px;"><?=date("g:ia d/m/y")?></div>
<h2 style="margin:5px 0px;">Stock procure list</h2>

<?php
	$transids = array();
	
	foreach($stk_rsv_list->result_array() as $stk_resv)
	{
		
		$prods = $this->db->query("select product_id,product,location,sum(rqty) as qty from ( 
select a.product_id,c.product_name as product,concat(concat(rack_name,bin_name),'::',b.mrp) as location,a.qty as rqty 
	from t_reserved_batch_stock a 
	join t_stock_info b on a.stock_info_id = b.stock_id 
	join m_product_info c on c.product_id = b.product_id 
	join m_rack_bin_info d on d.id = b.rack_bin_id 
	join shipment_batch_process_invoice_link e on e.p_invoice_no = a.p_invoice_no and invoice_no = 0 
	where e.p_invoice_no in (?)
group by a.id  ) as g 
group by product_id,location",$stk_resv['p_invoice_no'])->result_array();
		
?>
	<h4 style="margin-bottom:0px;"><?=$stk_resv['transid']?> (<?php echo $stk_resv['p_invoice_no'] ?>)</h4>
	<table border=1 style="font-family:arial;font-size:13px;" cellpadding=3>
		<tr style="background:#aaa">
			<th>Product ID</th><th>Product Name</th><th>Qty</th><Th>MRP</Th><th>Location</th>
		</tr>
	<?php 
		$i=0; 
		foreach($prods as $p){?>
			<tr <?php if($i%2==0){?>style="background:#eee;"<?php }?>>
				<td><a target="_blank" href="<?php echo site_url('admin/product/'.$p['product_id'])?>"><?=$p['product_id']?></a></td>
				<td><?=$p['product']?></td>
				<td><?=$p['qty']?></td>
				<?php list($loc,$mrp) = explode('::',$p['location']);?>
				<td><?=$mrp?></td>
				<td><?=$loc?>&nbsp;</td>
			</tr>
	<?php 
		$i++;
		}
	?>
	</table>
<?php		
	}
?>

<?php /*
<?php $pdata=$prods; foreach($prods as $transid=>$prods){?>
<h4 style="margin-bottom:0px;"><?=$transid?></h4>
<table border=1 style="font-family:arial;font-size:13px;" cellpadding=3>
<tr style="background:#aaa">
<th>Product Name</th><th>Qty</th><Th>MRP</Th><th>Location</th>
</tr>
<?php $i=0; foreach($prods as $p){?>
<tr <?php if($i%2==0){?>style="background:#eee;"<?php }?>>
<td><?=$p['product']?></td>
<td><?=$p['qty']?></td>
<td><?=$p['mrp']?></td>
<td><?=$p['location']?>&nbsp;</td>
</tr>
<?php $i++;}?>
</table>
<?php }?>
 */ ?>


</body>
</html>
<?php
