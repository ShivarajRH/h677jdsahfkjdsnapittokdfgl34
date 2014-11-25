<?php
/**
 * @author Shivaraj <shivaraj@storeking.in>_Sep_12_2014
 */
?>
<style>
	.page_title { float: left;margin: 0px;width: 40%; }
ul.tabs 
{
	margin: 0;
	padding: 0;
	
	list-style: none;
	width: 100%;
	border:none;
	margin-top:20px;
}

ul.tabs li 
{
	float: left;
	margin: 0;
	cursor: pointer;
	padding: 3px 20px;
	line-height: 25px;
	border: 1px solid #f8f8f8;
	font-weight: bold;
	background: #EEEEEE;
	position: relative;
	border-bottom:0px;
	border-radius:5px 5px 0px 0px;
	margin-right:3px;
}
ul.tabs li:hover 
{
	background: #CCCCCC;
}	
ul.tabs li.active
{
	background: #FAFAF5;
	border: 1px solid #FAFAF5;
}
.tab_container 
{
	
	border-top: none;
	clear: both;
	float: left; 
	width: 100%;
	background: #FAFAF5;
}
.tabcontent 
{
    padding: 10px;
	display: none;
}
.tabs
{
	border-radius : 0px !important;
}
.jqplot-highlighter-tooltip
{
	
}
.container {
	font-size: 13px;
	font-family: arial;
	text-align: left;
}
.page_wrap {
	margin: 5px;
	font-size: 12px;
}
.page_wrap .page_topbar {
	clear: both;
	display: inline-block;
	margin: 10px 0 5px;
	width: 100%;
}
.stat_block {
	display: inline-table;
	/*background: #F4F7EC;*/
	text-align: center;
	font-size: 16px;
	padding: 5px 15px 2px 15px;
	line-height: 20px;
	text-align: center;
	width: 12%;
	border: 1px solid #cccccc;
}
.stat_block b {
	font-size: 11px;
	display: block;
}
.color_green {
	background: #9EDB9E;
}
.color_red {
	background: #ff6666;
}
.color_grey {
	background: #f1f1f1;
}
.color_orange {
	background: #FFA500;
}
.color_blue {
	background: #E4EFF5;
}
</style>

<?php $sts_det=$this->partner->get_transfer_status($p_ord_list['transfer_status']); ?>


<div class="container page_wrap" style="padding:10px">

	<div class="page_topbar" align="left">
		
			<div class="fl_right" style="">
				<a class="button button-action button-rounded button-tiny" href="http://sndev13.snapittoday.com/admin/stk_partner_select" style="">Create Stock Transfer</a>
			</div>
			<div class="fl_right" style="padding:0 5px;">
				<a class="button button-action button-rounded button-tiny" href="http://sndev13.snapittoday.com/admin/partner_transfer_list" style="">View Transfer List</a>
			</div>
			<h2 class="page_title">
				Stock Transfer View #<?=$p_ord_list['transfer_id']?>
			</h2>
<?php
			if( match_in_list($p_ord_list['transfer_status'],'1,4') )
			{
?>
			<a class="button button-primary button-tiny button-rounded fl_right" style="" href="<?=site_url();?>/admin/partner_generate_picklist/<?=$p_ord_list['transfer_id']?>" target="_blank">Generate Picklist</a>
<?php
			}
?>
			<div class="clear" >&nbsp;</div>
<?php		
			if($p_ord_list['transfer_option']==1)
			{
?>
			<div class="stat_block color_green">
					<b>Transfer Id</b> 
					<span>#<?=$p_ord_list['transfer_id']?></span> 
			</div>
<?php
			}
			else {?>
				<div class="stat_block color_red">
						<b>Return Transfer Id</b> 
						<span>#<?=$p_ord_list['transfer_id']?></span> 
				</div>
			<?php }
?>
			<div class="stat_block color_blue">
				<b>Partner</b> 
				<span><?=$p_ord_list['partner_name']?></span>
			</div>
			<div class="stat_block color_grey">
				<b>Partner Transfer No</b> 
				<span><?=$p_ord_list['partner_transfer_no'];?></span>
			</div>
			
			<div class="stat_block color_blue">
				<b>Scheduled Transfer Date</b> 
				<span><?=format_datetime($p_ord_list['scheduled_transfer_date'])?></span>
			</div>
			<div class="stat_block color_green">
				<b>Created On</b> 
				<span><?=format_datetime($p_ord_list['transfer_date'])?></span>
			</div>
			<div class="stat_block color_grey">
				<b>Created By</b> 
				<span><?=ucfirst($p_ord_list['username'])?></span>
			</div>
			
			<div class="stat_block color_gray">
				<b>Status</b> 
				<span><?=$sts_det['msg'];?></span>
			</div>
			
			<!--<div class="clear" >&nbsp;</div>-->
			
		
	</div>
	
<div class="body_center_content">
	<div class="">
		<div class="">
			<ul class="tabs" style="clear:both"> 
		        <li rel="prod_linked" class="active">Items Linked</li>
		    </ul>
		    
	     	<div class="tab_container"> 
	       		<!-- #Deal Details Block start -->
	       		
	       		<!-- #Product Linked Block start -->
	       		<div id="prod_linked" class="tabcontent">
	       			
	       			
	       			<table width="100%">
	       				<tr>
	       					 <td width="99%">
								 
	       						<?php 
	       						if(count($p_ord_list['deals'])>0)
								{
	       						 ?>
					       		<table class="datagrid smallheader noprint" width="100%">
									<thead>
										<tr>
											<th>#</th>
											<!--<th>Menu</th>-->
											<th>Category</th>
											<th>Brand</th>
											<th>Itemid</th>
											<th>Deal Name</th>
											<th>Product Name</th>
											<th>Req. Qty</th>
											<th>Batch Qty</th>
											<th>Scan Qty</th>
											<th>Status</th>
											
											<th>Updated On</th>
											<th>Updated by</th>
											<th><small>Batched By</small></th>
											<th><small>Batched On</small></th>
											<th><small>Packed By</small></th>
											<th><small>Packed On</small></th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
									<?php 
										foreach($p_ord_list['deals'] as $i=>$log_det)
										{
									?>
										<tr tp_id="<?=$log_det['tp_id'];?>" itemid="<?php echo $log_det['itemid'];?>">
											<td><?php echo $i+1;?></td>
											<!--<td><?php echo $log_det['menu'];?></td>-->
											<td><?php echo $log_det['category'];?></td>
											<td><?php echo $log_det['brand'];?></td>
											<td><?php echo $log_det['itemid'];?></td>
											<td><a href="<?=site_url('/admin/deal/'.$log_det['itemid']);?>" target="_blank"><?php echo $log_det['deal_name'];?></a></td>
											<td><a href="<?=site_url('/admin/product/'.$log_det['product_id']);?>" target="_blank"><?php echo $log_det['product_name'];?></a></td>
											<td><?php echo $log_det['item_transfer_qty'];?></td>
											<td><?php echo (int)@($log_det['batch_qty']/$log_det['product_transfer_qty']);?></td>
											<td><?php echo (int)@($log_det['scanned_qty']/$log_det['product_transfer_qty']);?></td>
											<td><?php $sts_det=$this->partner->get_transfer_ord_status($log_det['transfer_status']);
													echo $sts_det['msg'];
											?></td>
											<td><?php echo format_datetime($log_det['modified_on']);?></td>
											<td><?php echo ucfirst($log_det['username']);?></td>
											<td><?php echo ucfirst($log_det['batched_by']);?></td>
											<td><?php echo format_datetime($log_det['batched_on']);?></td>
											
											<td><?php echo ucfirst($log_det['packed_by']);?></td>
											<td><?php echo format_datetime($log_det['packed_on']);?></td>
											<td>
												<!--<?php //echo ($log_det['is_updated']==1)?'Removed':'Added';?>-->
																							
												<?php
													if($log_det['transfer_status']==0)
													{
														if($log_det['is_active']==0) { ?>
															<a class="button button-tiny button-rounded button-caution">Cancelled</a>
														<?php } else { ?>
															<a class="button button-tiny button-rounded button-caution button_fit" onclick="return fn_cancel_transfer_product(this)">Cancel</a>
														<?php }
													}
												?>
											</td>
										</tr>
										
									<?php } ?>
									</tbody>
								</table>
								<?php } else{?>
									<table class="datagrid smallheader noprint" width="88%">
										<tbody>
											<tr><td width="100%" style="margin:10px;font-weight: bold;">No Data</td></tr>
										</tbody>
									</table><?php } ?>
	       					</td>
	       					<td width="1%">
	       						
	       					</td>
	       				</tr>
	       			</table>
					
					 
	       		</div>
	       		<!-- #Product Linked Block End -->
				 
	       	</div>
			
			<div class="clear" >&nbsp;</div>
			
			
			
			<div class="fl_right stat_block color_gray" style="padding:5px;">
				<!--<b>Action</b>--> 
				<?php
				
				$reserv_sts=$this->partner->get_stk_reserve_status($p_ord_list['transfer_id']);
				
				
				
				
				if($p_ord_list['transfer_status'] == 0)  { ?>
				
					<a href="" class="button button-action button-rounded" onclick="return fn_create_batch('<?=$p_ord_list['transfer_id']?>');" >Create Batch</a>
					
			<?php } //else if(match_in_list($p_ord_list['transfer_status'],'1,4') && $p_ord_list['is_active']==0) { 
				elseif($p_ord_list['transfer_status']==3)
				{ 
					$sts_det=$this->partner->get_transfer_status($p_ord_list['transfer_status']);
					echo $sts_det['msg'];
				}
				else if($reserv_sts==0)
				{
			
			?>
					
					<a href="<?=site_url("admin/partner_stock_scan/".$p_ord_list['transfer_id']);?>" target="_blank" class="button button-primary button-rounded" onclick="return fn_pack_transfer(this);">Pack</a>
					
			<?php } 
			
			
			//elseif( match_in_list($p_ord_list['transfer_status'],'2,3') ) { 
			
			elseif($reserv_sts==1) {
			
			?>
					
					<a href="<?=site_url('admin/stock_transfer_summary/'.$p_ord_list['transfer_id']);?>" target="_blank" class=""><i class="fa fa-camera"></i>View Transfer Summary</a>
					
			<?php  }
			
			elseif($reserv_sts==2)
			{ ?>
				
				<span>Batch Cancelled or Partial process</span>
				<br>
				<a href="<?=site_url('admin/stock_transfer_summary/'.$p_ord_list['transfer_id']);?>" target="_blank" class="button button-primary"><i class="fa fa-camera"></i>View Transfer Summary</a>
				
			<?php
			}
			else
			{?>
					<div class="">Transfer Done</div>
			<?php }
?>
			</div>
			
			
			<div class="stat_block color_gray">
				<b>Transfer Remarks</b> 
				<span>
					<?=$p_ord_list['transfer_remarks']?></p>
					
				</span>
			</div>
			
		 </div>      		
	</div>	       			
</div>	       			
</div>

<style>
	#description {padding:10px;background: #fcfcfc;max-height: 200px;overflow: hidden}
	#description table{background: #FFF;font-size: 11px;width: 100%;}
	#description table th{background: #f8f8f8;color:#555}
	#description table td{font-weight: normal}
	.pagination a{background: #cdcdcd;color:#555;}
	.leftcont{display: none;}
</style>

<script>

$(document).ready(function() 
{
	$(".tabcontent").hide();
	$(".tabcontent:first").show(); 
		
	$("ul.tabs li").click(function() 
	{
		$("ul.tabs li").removeClass("active");
		$(this).addClass("active");
		$(".tabcontent").hide();
		var activeTab = $(this).attr("rel"); 
		$("#"+activeTab).fadeIn(); 
	});
});


(function ($) { 

	$('#tab_deal_sales').tabs();
	$('#margin_his').tabs();

	$("#sm_from,#sm_to").datepicker();
	$("#date_from,#date_to").datepicker();
	
	
	$('#description').prepend("<div style='text-align:right'><a stat='1' href='javascript:void(0)' class='tgl_desc'>More</a></div>");

	$('.tgl_desc').click(function(){
		if($(this).attr('stat') == 1)
		{
			$(this).attr('stat',0);
			$('#description').css('max-height','none');
			$(this).text('Less');
		}else
		{
			$(this).attr('stat',1);
			$('#description').css('max-height','200px');
			$(this).text('More');
		}
	});

})(jQuery);

//$(".dealstock").dealstock({
//    popup:false
//    ,change:"text" //text,row
//});

	function fn_create_batch(transfer_id)
	{
		if( confirm("Are you sure you want to process transfer to batch?") )
		{
			$.post(site_url+"/admin/partner_create_batch",{transfer_id:transfer_id},function(resp) {
				if(resp.status=='success')
				{
					alert("Batch created. "+resp.message);
					window.location.href=$(location).attr("href");
				}
				else
				{
					alert("ERROR : "+resp.message);
					//window.location.href=$(location).attr("href");
				}
			},'json');
		}
		
		return false;
	}
	
	function fn_cancel_transfer_product(elt)
	{
		if(confirm("Are you sure you want to cancel this deal for transfer?"))
		{
			var trElt=$(elt).closest('tr');
			var tp_id=trElt.attr('tp_id');
			var itemid=trElt.attr('itemid');

			$.post(site_url+"/admin/cancel_transfer_product",{tp_id:tp_id,itemid:itemid},function(resp) {
				if(resp.status=='success')
				{
					alert(resp.message);

					window.location.href=$(location).attr('href');
				}
				else
				{
					alert("Error: "+resp.message);
				}
			},'json');
		}
		return false;
	}
</script>
<?php
