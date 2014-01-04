<?php 
	$order_status_flags = $this->config->item('order_status');
	$status_color_codes = $this->config->item('status_color_codes');
	$trans_status_flags = $this->config->item('trans_status');
?>
 
<style>

.table_grid_view th{
	padding:5px !important;
}
.sidepane{
	background:none;
}
</style>
<?php 
$user=$this->session->userdata("admin_user");
 


 


?>


<div style="margin-bottom:0px;padding-bottom:30px;">

<div class="container" style="font-family:arial;width: 98%">
 <h3>
 	<span style="float: right">
 		<a href="<?php echo site_url('admin/allotment_list')?>">View Allotment List</a> 
 		|
 		<a target="_blank" href="<?php echo site_url('admin/product_stock_unavailble_report')?>">Stock Unavailble Report</a>
 	</span>
 	Bulk Invoice Orders
 </h3>
 
<?php 
$trans_allot_stat = array();

foreach($orders as $order)
{
	if(!isset($trans_allot_stat[$order->transid]))
		$trans_allot_stat[$order->transid] = 1;

	$trans_allot_stat[$order->transid] *= (($order->avail_qty-$order->quantity)<0)?0:1;
	$trans_allot_stat[$order->transid] = 1;
}

$stat = in_array(1,$trans_allot_stat);
$stat = 1;
if($stat){
?> 
 
<div style="margin-top:5px;">

<table width="100%" class="table_grid_view" cellpadding="0" cellspacing="0" border="0">
<thead>
	<th>Transaction</th>
	<th>Order ID</th>
	<th>Type</th>
	<th>Item name</th>
	<th>Customer</th>
	<th>Order Qty</th>
	<th>Avail Qty</th>
	<th>Alloted Qty</th>
	<th>Ordered on</th>
	<th>
		<div style="width: 50px">
			<input type="button" onclick="auto_allot_trans()" style="color: #000;background: #FFF;padding:4px;" value="Auto Allot">
		</div>
	</th>
</thead>
<tbody>
<?php




$k=0;
$tmp_transid = '';

$row_span = 1;
foreach($orders as $order)
{ 

	if(!$trans_allot_stat[$order->transid])
		continue;
	
	if($tmp_transid=='')
	{
		$tmp_transid = $order->transid; 
		$row_span = 0;
	}
	else
	{
		if($order->transid == $tmp_transid)
		{
			$row_span = 1;
		}
		else
		{
			$tmp_transid = $order->transid; 
			$k++; 
			$row_span = 0;
		}
	}
	
?>
<tr class="orderlink_<?=$order->id?> <?php echo ($k%2?'even_row':'odd_row')?>">
<td width="60">
	<?php  if($row_span == 0){ ?>
	<a href="<?=site_url("admin/trans/{$order->transid}")?>"><?=$order->transid?></a>
	(<b><?php echo $trans_status_flags[$order->admin_trans_status]?></b>)
	<br /> 
	<?php }else{ echo '&nbsp;';}?>
</td>
<td width="80"><a href="<?=site_url("admin/vieworder/".$order->id)?>"><?=$order->id?></a></td>
<td width="60"><b><?=($order->is_giftcard?'<span style="color:#cd0000">GiftCard</span>':'Product')?></b></td>
<td style="max-width:160px;">
	<a href="<?=site_url("admin/edit/".$order->dealid)?>" target="_blank"><?=$order->itemname?></a>
</td>
<td>
	<?=$order->username?>
	<br />
	<span style="font-size: 11px;font-weight: bold;">(<?=$order->ship_city?>)</span>
</td>
<td><?=$order->quantity?></td>
<td><input type="text" disabled="disabled" size="4" name="avail_qty[<?php echo $order->itemid;?>]" value="<?php echo $order->avail_qty;?>"></td>
<td><input type="text" disabled="disabled" size="4" name="alloted_qty[<?php echo $order->id;?>]" value="0"></td>
<td><?=date("M d, g:i a",$order->time)?></td>
<td align="center" style="font-weight:bold;">
	<input o_id="<?php echo $order->id?>" o_qty="<?php echo $order->quantity?>" class="ordered_item" itm_id="<?php echo $order->itemid;?>" trans_id="<?php echo $order->transid;?>" type="checkbox" value="<?php echo $order->id;?>" name="sel_orders[<?php echo $order->transid;?>][]">
</td>
</tr>
<?php }?>
</tbody>
</table>

<div align="right" style="padding:10px 0px;">
	<a href="javascript:void(0)" onclick="bulk_create_invoice()" style="padding:5px 10px;background-color: #30464E;color:#FFF;float: right">Create Invoice</a>
</div>

<script type="text/javascript">
var stock_det = new Array();
var tmp_stock_det = stock_det;
<?php
foreach($orders as $order)
{
	$itmid = $order->itemid;
	$avail_qty = $order->avail_qty;
	if($itmid && $avail_qty){
		echo "	stock_det['$itmid'] = $avail_qty;\n";
		echo "	tmp_stock_det['$itmid'] = $avail_qty;\n";
	}
}
?>

var unallotable_array = new Array();

function auto_allot_trans(){
	//$('.ordered_item').attr('checked',true);

	if($(this).data('status') == undefined){
		$(this).data('status',1);
	}else{
		alert("Already Executed");
		return ;
	}
		

	var prev_trans_id = '';
		
	$('.ordered_item').each(function(i,ele){

		if($(this).attr('checked')){
			
		}else{

			var trans_id = $(this).attr('trans_id');
			var o_id = $(this).attr('o_id');
			var o_qty = $(this).attr('o_qty');
			var itm_id = $(this).attr('itm_id');
			
			k = $.inArray(trans_id,unallotable_array);
			if(k == -1){
				//console.log(itm_id);
				var stk_avail = tmp_stock_det[itm_id];

				if(stk_avail == undefined){
					stk_avail = 0;
					tmp_stock_det[itm_id] = 0;
				}
					
				//console.log(tmp_stock_det[itm_id]+' '+stock_det[itm_id]);
				var rem_qty = parseFloat(stk_avail)-parseFloat(o_qty);
				 
					if(rem_qty < 0){
						$(this).attr('checked',false);
						$('input[name="sel_orders['+trans_id+'][]"]').each(function(){
							if($(this).attr('checked')){
								
							}else{
								$('input[name="sel_orders['+trans_id+'][]"].auto_allotment_processed:checked').each(function(){

									itm_id_tmp = $(this).attr('itm_id');
									o_qty_tmp = $(this).attr('o_qty');
									o_id_tmp = $(this).attr('o_id');

									stk_avail_tmp = $('input[name="avail_qty['+itm_id_tmp+']"]').val();
									
									tmp_stock_det[itm_id_tmp] = stk_avail_tmp = parseFloat(tmp_stock_det[itm_id_tmp])+parseFloat(o_qty_tmp);
									
									$('input[name="avail_qty['+itm_id_tmp+']"]').val(stk_avail_tmp);

									$('input[name="alloted_qty['+o_id_tmp+']"]').val(0);

									$(this).attr('checked',false);
								});
							}
						});
						unallotable_array.push(trans_id);
					}else{
						stk_avail = rem_qty;
						$(this).attr('checked',true);
						$(this).addClass('auto_allotment_processed');
						$('input[name="avail_qty['+itm_id+']"]').val(stk_avail);
						$('input[name="alloted_qty['+o_id+']"]').val(o_qty);
					}
					tmp_stock_det[itm_id] = stk_avail;	
			}
		}
		
	});

	if(!$('.ordered_item:checked').length){
		alert("No Orders Where Alloted, Due to non availablity of Stock");
	}

	
	
}

 
 
$('.ordered_item').change(function(){
	var trans_id = $(this).attr('trans_id');
	var o_id = $(this).attr('o_id');
	var o_qty = $(this).attr('o_qty');
	var itm_id = $(this).attr('itm_id');
	
	var stk_avail = tmp_stock_det[itm_id];
	if(stk_avail == undefined){
		stk_avail = 0;
		tmp_stock_det[itm_id] = 0;
	}

	
	if($(this).attr('checked')){
		var rem_qty = parseFloat(stk_avail)-parseFloat(o_qty);
			if(rem_qty < 0){
				$(this).attr('checked',false);
			}else{
				tmp_stock_det[itm_id] = rem_qty;
				stk_avail = rem_qty;
				$('input[name="alloted_qty['+o_id+']"]').val(o_qty);
			}
	}else{
		stk_avail = parseFloat(stk_avail)+parseFloat(o_qty);
		tmp_stock_det[itm_id] = stk_avail;
		$('input[name="alloted_qty['+o_id+']"]').val(0);
	}

		$('input[name="avail_qty['+itm_id+']"]').val(stk_avail);
});


	$('.table_grid_view tr').hover(function(){
		$(this).addClass('highlight_row');
		
	},function(){
		$(this).removeClass('highlight_row');
	});


function bulk_create_invoice(){
	var process_order_list  = new Array();
		$('.ordered_item:checked').each(function(){
			process_order_list.push($(this).attr('o_id')); 
		});

		if(process_order_list.length){
			$.post(site_url+'/admin/p_bulk_invoice','ord_ids='+process_order_list.join(','),function(resp){
				alert(resp)
				location.href = '<?php echo current_url();?>';
			});
		}else{
			alert("No Orders Selected, Please Auto Allot Orders and Proceed"); 
		}
		
}	

	 
	
</script>
</div>
<?php }else{
?>
	<div style="padding:10px;font-size:15px;">No orders available for auto allotment</div>
<?php 	
} ?>
</div>

