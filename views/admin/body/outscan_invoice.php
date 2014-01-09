<div align="left" style="padding:10px;margin:10px auto;">
	<h3 style="margin: 0px;">Outscan Invoice</h3>
	<form target="hndl_outscaninvoice" action="<?php echo site_url('admin/p_outscaninvoice')?>" method="post">
		<input type="text" id="o_barcode" style="padding:2px;font-size: 18px;width: 300px;color: #666" name="o_barcode" onclick="$(this).select()" value="Enter Invoiceno">
		<input type="submit" value="OUTSCAN" class="sbutton" style="padding:5px;">
		
		<span style="padding:3px;color: green" id="resp_message"></span>
		
	</form>
	<iframe id="hndl_outscaninvoice" name="hndl_outscaninvoice" style="width:0px;height:0px;border:none;"></iframe>
	<br />
	<table id="outscan_products" class="table_grid_view"  cellpadding=0 cellspacing=0 border=0>
		<caption style="padding:3px;font-weight: bold;text-align: left;">List of Orders</caption>
		<thead>
			<th align="left" width="40">Slno</th>
			<th align="left" width="80">Invoiceno</th>
			<th align="left" width="150">Orderno</th>
			<th align="left" width="150">TransactionID</th>
			<th align="left">Product Name</th>
			<th align="left" width="60">MRP</th>
			<th align="left" width="100">Status</th>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
<style type="text/css">
	.table_grid_view td{
		border-bottom:1px dotted #ccc;
	}
</style>
<script type="text/javascript">
	$('#o_barcode').select();
	var toggle_bg = 1;
	function add_tolist(outscaned_orders){

		if(toggle_bg==0){
			row_style = 'even_row';
			toggle_bg  = 1;
		}else
		{
			row_style = 'odd_row';
			toggle_bg  = 0;
		} 
		var inv_no = '';
		$.each(outscaned_orders,function(i,orderdet){
			var total_inlist = $('#outscan_products tbody tr').length;

			inv_no = orderdet.invoice_no;
			
			var lastele = $('#outscan_products tbody ');
			var orderdet_html = '<tr class="'+row_style+'">';
				orderdet_html += '	<td>'+(total_inlist+1)+'</td>';
				orderdet_html += '	<td>'+orderdet.invoice_no+'</td>';
				orderdet_html += '	<td>'+orderdet.order_id+'</td>';
				orderdet_html += '	<td>'+orderdet.trans_id+'</td>';
				orderdet_html += '	<td>'+orderdet.product_name+'</td>';
				orderdet_html += '	<td> Rs '+orderdet.o_mrp+'</td>';
				orderdet_html += '	<td>'+orderdet.order_status+'</td>';
				orderdet_html += '</tr>';	
				$(lastele).append(orderdet_html); 
		});


		 $('#resp_message').html("Total <b>"+outscaned_orders.length+" Orders</b> in invoice #"+inv_no+' are Outscanned ').fadeIn();

		 $('#o_barcode').select();
		setTimeout(function(){
			 $('#resp_message').fadeOut('slow');
		},5000);
	}

	function show_error(msg){
		alert(msg);
		$('#o_barcode').select();
	}
	
</script>