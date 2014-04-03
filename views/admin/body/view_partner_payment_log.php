<div class="container page_wrap" style="padding-top:7px;margin-top: 10px;">
<h2>Partner Settelment Log &nbsp;<?php echo $pagetitle;?></h2>
<div class ="module_cont_block_filters" style="background: #e6e6e6;padding:12px;margin:0px;">
<b>Total:</b><?php echo $ttl;?>

<div style="float:right;" class="module_cont_block_filters">
<b>Filter by</b>
<select name="choose_partner" id="partner">
<option value="0">Select Partner</option>
<?php foreach($partners as $p){?>
<option value="<?php echo $p['id']?>" <?php echo $p['id']==$partnrid?"selected":""?>><?php echo $p['name']?></option>
<?php }?>
</select>
Date Range:<input type="text" name="st_dt" id="st_dt" value=""> to<input type="text" name="en_dt" id="en_dt" value=""> &nbsp;<input type="submit" value="submit" id="dt_range_submit">
</div>
</div>
<table class="datagrid" width="100%">
<thead><th>Sl no</th><th>Partner Name</th><th>Order Details</th><th>Payment Details</th><!-- <th>Trans ID</th><th>Order value(Rs)</th> <th>Payment Amount(Rs)</th><th>Payment Id</th><th>Payment Date</th>--><th>Updated By</th><th>Updated On</th></thead>
<?php if($partner_settelment_log->num_rows()){ 
		$i=1;
		foreach($partner_settelment_log->result_array() as $p_det){
?>
<tbody>
<tr><td><?php echo $i;?></td><td><?php echo $p_det['partner']?></td><td><?php $ttl_orders=explode(',',$p_det['orderids']); echo sizeof($ttl_orders);?> &nbsp;&nbsp;<a href="javascript:void(0)" onclick="view_orders(<?php echo $p_det['payment_uploaded_id']?>)" style="font-size:10px;">View</a><p  style="font-size:11px; font-weight: bold">Total order amount : Rs <?php echo $p_det['ttl_amt']?></p></td><td style="font-size:11px; font-weight: bold">Payment reference number : <?php echo $p_det['payment_id'];?><p>Payment Amount : Rs <?php echo $p_det['payment_amount']?></p><p>Payment Date : <?php echo format_date($p_det['payment_date'])?></p></td><!--  <td><a href="<?php echo site_url('admin/trans/ '.$p_det['transid'])?>"  target="_blank"><?php echo $p_det['transid']?></a></td><td><?php echo $p_det['amount']?></td><td><?php echo $p_det['payment_amount']?></td><td><?php echo $p_det['payment_id']?></td><td><?php echo format_date($p_det['payment_date'])?></td>--><td><?php echo $p_det['updated_by']?></td><td><?php echo format_datetime_ts($p_det['logged_on'])?></td></tr>
 </tbody>
<?php $i++; }  }else{?>

<tfoot>
<tr><td colspan="12" align="right"><?php echo '<b>No Data Found</b>';?></td></tr>
<?php }?>

</tfoot>

</table>
</div>
<?php if(isset($pagination)){?>
<div class="pagination" align="right">
	<?php echo $pagination;?>
</div>
<?php } ?>
<div id="view_ordrs_dlg" title="Order Details">

	<table class="datagrid" width="100%" id="payment_order_det" >
		<thead>
			<th>Sl no</th>
			<th>Order ID</th>
			<th>Trans ID</th>
			<th>Amount</th>
			<th>Order Date</th>
		</thead>
		<tbody>
			<tr></tr>
		</tbody>
	</table>

</div>
<script>
prepare_daterange('st_dt','en_dt');

$('#partner').change(function(){
	partner_id=$(this).val();
		location="<?php echo site_url('admin/view_partner_settelment_log/')?>"+'/'+partner_id;
});



$('#dt_range_submit').click(function(){
	
	st_dt=$('input[name="st_dt"]').val();
	en_dt=$('input[name="en_dt"]').val();
	partner_id=$('select[name="choose_partner"]').val();
	location="<?php echo site_url('admin/view_partner_settelment_log/')?>"+'/'+partner_id+'/'+st_dt+'/'+en_dt;
});

function view_orders(upld_id)
{
	$('#view_ordrs_dlg').data('uploaded_id',upld_id).dialog('open');
	
}

$("#view_ordrs_dlg").dialog({
	modal:true,
	autoOpen:false,
	width:'750',
	height:'auto',
	
	autoResize:true,
	open:function(){
	dlg = $(this);
	 uploaded_id=dlg.data('uploaded_id');
	$('#payment_order_det tbody').html('');
	$.getJSON(site_url+'/admin/jx_get_partner_payment_order_details/'+uploaded_id,function(resp){
		if(resp.status == 'success')
		{
			i=1;
			$.each(resp.payment_order_details,function(a,b){
			
				 var tblRow =
					"<tr>"
					+"<td>"+ i++ +"</td>"
					+"<td>"+b.orderid+"</td>"
					+"<td><a  href='"+site_url+'/admin/trans/'+b.transid+"' target='_blank' class='link'>"+b.transid+"</a></td>"
					+"<td>"+b.amount+"</td>"
					+"<td>"+b.orderd_on+"</td></tr>";
					i=i++;
					 $(tblRow).appendTo("#payment_order_det tbody");
				
			});
			
		}
		
	},'json');
	},
	buttons:{
	'Close':function(){
		$(this).dialog('close');
		},
	}	
});
</script>