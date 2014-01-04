<?php 
	$order_status_flags = $this->config->item('order_status');
	$status_color_codes = $this->config->item('status_color_codes');
	$trans_status_flags = $this->config->item('trans_status');
?>
<script>
oids=new Array();
$(function(){
<?php if(isset($brands)){?>	
	$(".viewmore").click(function(){
		$("select",$(this).parent()).show();
	});
	$("#brandsel").change(function(){
		obj=$(this);
		if(obj.val()==0)
			return;
		location.href="<?=site_url("admin/ordersforbrand")?>/"+obj.val();
	});
<?php }?>	
//$("tr[class^=orderlink_]").click(function(){
//	cl=$(this).attr("class");
//	ar=cl.split("_");
//	location.href="<?=site_url("admin/vieworder")?>/"+ar[1];
//});
});
</script>
<style>
.ostatus {
	padding:0px;
}
.ostatus ul{
	margin:0px;
	padding-left:0px;
	
}
.ostatus ul li{
	margin:0px;
	list-style:none;
}
.ostatus a{
	display:block;
	margin:0px !important;
	
	padding:5px;
}
.ostatus a.selected_status{
	background:#aaa !important;
	color:#000 !important;
	-moz-border-radius:0px 10px 10px 0px;
	
}
.table_grid_view th{
	padding:5px !important;
}
.sidepane{
	background:none;
}
</style>
<?php 
$user=$this->session->userdata("admin_user");

$status="";
if($this->uri->segment(2)=="ordersbystatus")
	$status=$this->uri->segment(3)."/";
if($this->uri->segment(2)=="ordersbyuser")
	$status=$this->uri->segment(3)."/";	
	
$gourl="/admin/".$this->uri->segment(2)."/".$status."$p/$sort/$order";


 

$f_gourl1="/admin/".$this->uri->segment(2)."/";
$f_gour2 ="/$p/$sort/$order";

?>

<script>
var site_url = '<?php echo site_url()?>';

$(function(){
	$("#f_start,#f_end").datepicker({dateFormat:'dd-mm-yy'});
	$("#f_go").click(function(){
		if($("#f_start").val().length==0 || $("#f_start").val().length==0)
		{
			alert("Please enter valid from and to Inputs"); 
			return;
		}

		var stck_avail = 0;
		var high_prio = 0;
		if($('#f_stockavail').attr('checked')){
			stck_avail = 1;
		}
		if($('#f_highpriority').attr('checked')){
			high_prio = 1;
		}

		var srch_status = '';
		if($('#f_srchon').val() == 'tran'){
			srch_status = $("#f_tstatus").val();
		}else{
			srch_status = $("#f_ostatus").val();
		}
		

		var search_url = site_url+'/admin/'+$('#f_srchon').val()+'sbystatus/'+srch_status+'/1/ordertime/desc/';
			location.href=search_url+$("#f_start").val()+"/"+$("#f_end").val()+'/'+high_prio+'/'+stck_avail;
		
	});

	$('#f_srchon').live('change',function(){
		if($(this).val() == 'tran'){
			$("#f_tstatus").show();
			$("#f_ostatus").hide();
		}else{
			$("#f_ostatus").show();
			$("#f_tstatus").hide();
		}
	});

	$('#f_srchon').trigger('change');
	
});
</script>

<div class="heading" style="margin-bottom:0px;">

<div class="headingtext container">

	<?php if(isset($pagetitle)) echo $pagetitle; else echo "Orders";?>
	<div align="right" style="text-transform: capitalize;">
		<a href="<?=site_url("admin/orderlist/".($status==""?"all/":$status)."/$from/$to")?>" target="_blank" style="margin-left:30px;font-size:50%;">order list</a>
		<a href="<?=site_url("admin/prodlist/".($status==""?"all/":$status)."/$from/$to")?>" target="_blank" style="margin-left:30px;font-size:50%;">product procurement list</a>
	</div>
	 
	</div>

</div>
<div class="container" style="font-family:arial;min-height:250px;">
<div style="float:left;padding-top:5px;">
<div class="sidepane ostatus" style="width:140px;">
<div style="font-size:13px;padding-bottom:5px;padding:5px;font-weight: bold;background: #30464E;color: #FFF;margin-bottom: 5px;">Orders by status</div>

<ul>
	<li><a id="view_all_orders" style="color:red;font-weight:bold;margin:5px;font-size:13px;" href="<?=site_url("admin/orders")?>"><nobr>View All Orders</nobr></a></li>
	<li><a id="priority_orders" style="color:#cd0000;font-weight:bold;margin:5px;font-size:13px;" href="<?=site_url("admin/ordersbystatus/priority")?>"><nobr>High Priority</nobr></a></li>
	<li><a id="pending_orders" style="color:green;font-weight:bold;margin:5px;font-size:13px;" href="<?=site_url("admin/ordersbystatus/pending")?>"><nobr>Pending</nobr></a></li>
	<li><a id="notshipped_orders" style="color:darkblue;font-weight:bold;margin:5px;font-size:13px;" href="<?=site_url("admin/ordersbystatus/notshipped")?>"><nobr>Invoiced</nobr></a></li>
	<li><a id="shipped_orders" style="color:#444;font-weight:bold;margin:5px;font-size:13px;" href="<?=site_url("admin/ordersbystatus/shipped")?>"><nobr>Shipped</nobr></a></li>
	<li><a id="delivered_orders" style="color:#00f;font-weight:bold;margin:5px;font-size:13px;" href="<?=site_url("admin/ordersbystatus/delivered")?>"><nobr>Closed/Delivered</nobr></a></li>
	<li><a id="returned_orders" style="color:gray;font-weight:bold;margin:5px;font-size:13px;" href="<?=site_url("admin/ordersbystatus/returned")?>"><nobr>Returned</nobr></a></li>
	<li><a id="rejected_orders" style="color:red;font-weight:bold;margin:5px;font-size:13px;" href="<?=site_url("admin/ordersbystatus/rejected")?>"><nobr>Rejected/Cancelled</nobr></a></li>
</ul>
</div>

<div class="sidepane ostatus" style="width:140px;">
<div style="font-size:13px;padding-bottom:5px;padding:5px;font-weight: bold;background: #30464E;color: #FFF;margin-bottom: 5px;">Trans by status</div>

<ul>
	<li><a id="priority_trans" style="color:#cd0000;font-weight:bold;margin:5px;font-size:13px;" href="<?=site_url("admin/transbystatus/priority")?>"><nobr>High Priority</nobr></a></li>
	<li><a id="pending_trans" style="color:green;font-weight:bold;margin:5px;font-size:13px;" href="<?=site_url("admin/transbystatus/pending")?>"><nobr>Pending</nobr></a></li>
	<li><a id="notshipped_trans" style="color:darkblue;font-weight:bold;margin:5px;font-size:13px;" href="<?=site_url("admin/transbystatus/pinvoiced")?>"><nobr>Partially Invoiced</nobr></a></li>
	<li><a id="shipped_trans" style="color:darkblue;font-weight:bold;margin:5px;font-size:13px;" href="<?=site_url("admin/transbystatus/invoiced")?>"><nobr>Invoiced</nobr></a></li>
	<li><a id="delivered_trans" style="color:green;font-weight:bold;margin:5px;font-size:13px;" href="<?=site_url("admin/transbystatus/pshipped")?>"><nobr>Partially Shipped</nobr></a></li>
	<li><a id="delivered_trans" style="color:green;font-weight:bold;margin:5px;font-size:13px;" href="<?=site_url("admin/transbystatus/shipped")?>"><nobr>Shipped</nobr></a></li>
	<li><a id="delivered_trans" style="color:#00f;font-weight:bold;margin:5px;font-size:13px;" href="<?=site_url("admin/transbystatus/closed")?>"><nobr>Closed</nobr></a></li>
	<li><a id="rejected_trans" style="color:red;font-weight:bold;margin:5px;font-size:13px;" href="<?=site_url("admin/transbystatus/cancelled")?>"><nobr>Cancelled</nobr></a></li>
</ul>
</div>

<?php 
	if($this->uri->segment(2)=='ordersbystatus'){
		$selected_status = $this->uri->segment(3);
	}else{
		$selected_status = 'view_all';
	}
	$selected_status .= '_orders';
?>	
<script type="text/javascript">
	$('a#<?php echo $selected_status;?>').addClass('selected_status');
</script>

<?php if(isset($brands)){?>
<div class="sidepane" style="width:130px;padding:0px;">
 
<div style="font-size:13px;padding-bottom:5px;padding:5px;font-weight: bold;background: #30464E;color: #FFF;margin-bottom: 5px;">Orders for brand</div>

<?php $ic=0; foreach($brands as $brand){?>
<a style="margin:5px;font-size:13px;display: block;" href="<?=site_url("admin/ordersforbrand/{$brand->id}")?>"><?=$brand->name?></a>
<?php $ic++;if($ic==10) break;}?>
<?php if(count($brands)>10){?>
<div align="center"> 
<a href="javascript:void(0)" class="viewmore" style="font-size:13px;float:right;font-weight:bold;">more</a>
<select id="brandsel" style="display:none">
<option value="0">--select--</option>
<?php foreach($brands as $brand){?>
<option value="<?=$brand->id?>"><?=$brand->name?></option>
<?php }?>
</select>
</div>
<?php }?>
</div>
<?php }?>
</div>


 
<DIV style="font-family:arial;font-size:13px;margin-left:150px;padding-top:15px;">








<table width="99%" cellpadding="0" cellspacing="0">
	<tr>
		<td align="left">
			<div align="left" style="font-size:12px;font-weight: bold;">
				 
				<b>Search</b>
				<br />
				
				<table class="infobar" cellpadding="5" cellspacing="3">
					<tr>	
						 
					 	<td>
							<select id="f_srchon" name="f_srchon">
								<option value="tran">Transaction</option>
								<option value="order">Orders</option>
							</select>
						</td>
						<td>
							<input type="checkbox" id="f_highpriority" value="1" name="f_highpriority" <?php echo (($this->uri->segment(9))?'checked=checked':'')?>> High Priority
							<input type="checkbox" id="f_stockavail" value="1" name="f_stockavail" <?php echo (($this->uri->segment(10))?'checked=checked':'')?> > Stock Available
						</td>
					 
						<td>		
							Choose Status 						 		
							<select id="f_ostatus" name="f_ostatus" style="display: none;">
								<option value="">Choose Status</option>
								<option value="pending" <?php echo (($this->uri->segment(3) == 'pending')?'selected':'')?> >Pending</option>
								<option value="notshipped" <?php echo (($this->uri->segment(3) == 'notshipped')?'selected':'')?> >Invoiced</option>
								<option value="shipped" <?php echo (($this->uri->segment(3) == 'shipped')?'selected':'')?> >Shipped</option>
								<option value="delivered" <?php echo (($this->uri->segment(3) == 'delivered')?'selected':'')?> >Delivered</option>
								<option value="returned" <?php echo (($this->uri->segment(3) == 'returned')?'selected':'')?> >Returned</option>
								<option value="rejected" <?php echo (($this->uri->segment(3) == 'rejected')?'selected':'')?> >Rejected</option>
							</select>
							
							<select id="f_tstatus" name="f_tstatus" style="display: none;">
								<option value="">Choose Status</option>
								<option value="pending" <?php echo (($this->uri->segment(3) == 'pending')?'selected':'')?> >Pending</option>
								<option value="pinvoiced" <?php echo (($this->uri->segment(3) == 'pinvoiced')?'selected':'')?> >Partially Invoiced</option>
								<option value="invoiced" <?php echo (($this->uri->segment(3) == 'invoiced')?'selected':'')?> >Invoiced</option>
								<option value="pshipped" <?php echo (($this->uri->segment(3) == 'pshipped')?'selected':'')?> >Partially Shipped</option>
								<option value="shipped" <?php echo (($this->uri->segment(3) == 'shipped')?'selected':'')?> >Shipped</option>
								<option value="closed" <?php echo (($this->uri->segment(3) == 'closed')?'selected':'')?> >Closed</option>
								<option value="cancelled" <?php echo (($this->uri->segment(3) == 'cancelled')?'selected':'')?> >Cancelled</option>
							</select>
							
						</td>	
					 
						<td>
						 From <input type="text" id="f_start" value="<?php echo $this->uri->segment(7); ?>" size="15"> 
						 To <input type="text" id="f_end" value="<?php echo $this->uri->segment(8); ?>" size="15">
						 <input type="button" value="Search" id="f_go">
						</td>	
					</tr>
				</table> 
				
				
				
			</div>		
		</td>
		<td align="right" valign="bottom"> 
		
</td>

</tr>
</table>


<div align="right" style="padding:5px;overflow: hidden;">
<?php if(isset($p)){?>
<div style="font-size:13px;font-weight: bold;">
<?php 
$limit=20;
$st=(($p-1)*$limit+1);
$et=$st+$limit-1;
if($et>$len)
	$et=$len;
	
	$total_pages = round($len/$limit);
?>
<span style="float: left">
	showing <?=$st?><?php if($st!=$et){?>-<?=$et?><?php }?> of <?=$len?>
</span>
<?php if($len>0){?>
<?php if($p>1 && isset($prevurl)){?>
<a style="padding:5px;text-decoration: underline;" href="<?=$prevurl?>">prev</a>
<?php }?>


<?php if($et<$len && isset($nexturl)){?>
<span style="float: right">
<a style="padding:5px;text-decoration: underline;" href="<?=$nexturl?>">next</a>
</span>
<?php }}?>

<span style="float: right;display: none;">
<?php 
	$pg_dspstatus = 0;
	for($pg=0;$pg<=$total_pages;$pg++){
		   
?>
	<a style="padding:5px;text-decoration: underline;background: <?php echo (($pg+1 == $p)?'#ccc':'#e3e3e3');?>" href="<?=str_replace('PAGINATE',$pg+1,$navurl)?>"><?php echo ($pg+1);?></a>
<?php 		
	}
?>
</span>

</div>
<?php }?>
</div>

<div style="margin-top:5px;padding:5px;border:1px solid #ddd;repeat-x;clear: right;">

<table width="100%" class="datagrid" cellpadding="5" cellspacing="0" border="0">
<tr>
<th>Order ID</th>
<th>Type</th>
<th>Item name<br><span style="font-size:10px;font-weight:normal;">sort</span><a href="<?=$url?>/1/itemname/a/<?=$from?>/<?=$to?>/<?=$high_priority?>/<?=$stock_avail?>"><img src="<?=base_url()?>images/desc.gif"></a><a href="<?=$url?>/1/itemname/d/<?=$from?>/<?=$to?>/<?=$high_priority?>/<?=$stock_avail?>"><img src="<?=base_url()?>images/asc.gif"></a></th>
<th>Customer<br><span style="font-size:10px;font-weight:normal;">sort</span><a href="<?=$url?>/1/customer/a/<?=$from?>/<?=$to?>/<?=$high_priority?>/<?=$stock_avail?>"><img src="<?=base_url()?>images/desc.gif"></a><a href="<?=$url?>/1/customer/d/<?=$from?>/<?=$to?>/<?=$high_priority?>/<?=$stock_avail?>"><img src="<?=base_url()?>images/asc.gif"></a></th>
<th>Brand<br><span style="font-size:10px;font-weight:normal;">sort</span><a href="<?=$url?>/1/brand/a/<?=$from?>/<?=$to?>/<?=$high_priority?>/<?=$stock_avail?>"><img src="<?=base_url()?>images/desc.gif"></a><a href="<?=$url?>/1/brand/d/<?=$from?>/<?=$to?>/<?=$high_priority?>/<?=$stock_avail?>"><img src="<?=base_url()?>images/asc.gif"></a></th>
<th>Quantity<br><span style="font-size:10px;font-weight:normal;">sort</span><a href="<?=$url?>/1/quantity/a/<?=$from?>/<?=$to?>/<?=$high_priority?>/<?=$stock_avail?>"><img src="<?=base_url()?>images/desc.gif"></a><a href="<?=$url?>/1/quantity/d/<?=$from?>/<?=$to?>/<?=$high_priority?>/<?=$stock_avail?>"><img src="<?=base_url()?>images/asc.gif"></a></th>
<th>Ordered on<br><span style="font-size:10px;font-weight:normal;">sort</span><a href="<?=$url?>/1/ordertime/a/<?=$from?>/<?=$to?>/<?=$high_priority?>/<?=$stock_avail?>"><img src="<?=base_url()?>images/desc.gif"></a><a href="<?=$url?>/1/ordertime/d/<?=$from?>/<?=$to?>/<?=$high_priority?>/<?=$stock_avail?>"><img src="<?=base_url()?>images/asc.gif"></a></th>
<th>Action on<br><span style="font-size:10px;font-weight:normal;">sort</span><a href="<?=$url?>/1/actiontime/a/<?=$from?>/<?=$to?>/<?=$high_priority?>/<?=$stock_avail?>"><img src="<?=base_url()?>images/desc.gif"></a><a href="<?=$url?>/1/actiontime/d/<?=$from?>/<?=$to?>/<?=$high_priority?>/<?=$stock_avail?>"><img src="<?=base_url()?>images/asc.gif"></a></th>
<th>Status<br><span style="font-size:10px;font-weight:normal;">sort</span><a href="<?=$url?>/1/status/a/<?=$from?>/<?=$to?>/<?=$high_priority?>/<?=$stock_avail?>"><img src="<?=base_url()?>images/desc.gif"></a><a href="<?=$url?>/1/status/d/<?=$from?>/<?=$to?>/<?=$high_priority?>/<?=$stock_avail?>"><img src="<?=base_url()?>images/asc.gif"></a></th>
<th>Transaction</th>
</tr>
<tbody>
<?php 
$k=0;
$tmp_transid = '';


foreach($orders as $order){
	
	$row_span = 1;
	if($tmp_transid==''){
		$tmp_transid = $order->transid; 
	}
	
	if($order->transid != $tmp_transid){
		$tmp_transid = $order->transid; 
		$k++; 
		 
	}
	
?>
<tr class="orderlink_<?=$order->id?> <?php echo ($k%2?'even_row':'odd_row')?>">
<td><a href="<?=site_url("admin/vieworder/".$order->id)?>"><?=$order->id?></a></td>
<td><b><?=($order->is_giftcard?'<span style="color:#cd0000">GiftCard</span>':'Product')?></b></td>
<td style="max-width:160px;">
	<?=$order->itemname?>
</td>
<td>
	<?=$order->username?>
	<br />
	<span style="font-size: 11px;font-weight: bold;">(<?=$order->ship_city?>)</span>
</td>
<td><?=$order->brandname?></td><td><?=$order->quantity?></td><td><?=date("M d, g:i a",$order->time)?></td>
<td><?php if($order->actiontime==0) echo "n/a"; else echo date("M d, g:i a",$order->actiontime);?></td>
<td style="font-weight:bold;">
<?php echo "<span style='color:".$status_color_codes[$order->admin_order_status]."'>".$order_status_flags[$order->admin_order_status]."</span>";?>
</td>
<td rowspan="<?php echo $row_span ?>">
	<a class="link" href="<?=site_url("admin/trans/{$order->transid}")?>"><?=$order->transid?></a><br /> 
	(<b><?php echo $trans_status_flags[$order->admin_trans_status]?></b>)
	 
	
</td>
</tr>
<?php }?>
</tbody>
</table>

<script type="text/javascript">
	$('.table_grid_view tr').hover(function(){
		$(this).addClass('highlight_row');
		
	},function(){
		$(this).removeClass('highlight_row');
	});


	<?php 
		if($this->uri->segment(2)=='ordersbystatus'){
	?>
		$('#f_srchon').val('order');
		$('#f_ostatus').show();
	<?php 		
		}else{
	?>
		$('#f_srchon').val('tran');
		$('#f_tstatus').show(); 
	<?php 		
		}
	?>
	
</script>

<?php if(empty($orders)){?>
<div style="padding:10px;font-size:15px;">No orders available</div>
<?php }?>
</div>
</div>
</div>