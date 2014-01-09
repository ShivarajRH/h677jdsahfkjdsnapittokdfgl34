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
	 
	</div>

</div>

<div class="container" style="font-family:arial;min-height:250px;">


 
<DIV style="font-family:arial;font-size:13px;padding-top:15px;">










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
<?php switch($order->status){
	case 0: echo "pending";break;
	case 1: echo "processed";break;
	case 2: echo "shipped";break;
	case 3: echo "cancelled";break;
} ?>
</td>
<td rowspan="<?php echo $row_span ?>">
	<a class="link" href="<?=site_url("admin/trans/{$order->transid}")?>"><?=$order->transid?></a>
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