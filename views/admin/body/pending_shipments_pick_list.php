<html>
	<head>
		<title>Pick list For Pending Shipments</title>
		<script type="text/javascript" src="<?=base_url()?>js/jquery-1.8.1.min.js"></script>
	</head>
	<body style="font-family:arial;font-size:14px;">
		<div style="width:960px;margin:0 auto;">
			<h3>Pending Shipments Pick list  <span style="float:right">Manifesto Id:<?php echo $manifest_id;?></span></h3>
			<?php if($pick_list_data){?>
			<table width="100%" border=1 style="font-family:arial;font-size:13px;" cellpadding=3 cellspacing="0" class="datagrid">
				<thead>
					<tr style="background:#aaa">
						<td>#</td>
						<td>Tray</td>
						<td>Town</td>
						<td>Franchise Name</td>
						<td>Invoice</td>
					</tr>
				</thead>
				<tbody>
				<?php foreach($pick_list_data as $i=>$data){?>
					<tr>
						<td><?php echo $i+1; ?></td>
						<td><?php echo $data['tray_name']; ?></td>
						<td><?php echo $data['town_name']; ?></td>
						<td><?php echo $data['franchise_name']; ?></td>
						<td><?php echo $data['invoice_no']; ?></td>
					</tr>
				<?php }?>
				</tbody>
				<tfoot>
					<tr>
						<td align="right" colspan="6"> <a class="dg_print" href="javascript:void(0)">print</a></td>
					</tr>
				</tfoot>
			</table>
			<?php }else{
				echo 'No More Pick list found';
			}?>
		</div>
	</body>
</html>

<script>
$(".dg_print").click(function(){
	var html="";
	prw=window.open("",'','width=10,height=10');
	if($("thead",$(this).parents(".datagrid")).length!=0)
		html=$("thead",$(this).parents(".datagrid")).html();	
	html=html+$("tbody",$(this).parents(".datagrid")).html();
	prw.document.write('<table border=1 width="100%" style="font-size:12px;font-family:arial;">'+html+'</table>');
	prw.focus();
	prw.print();
});
			
</script>