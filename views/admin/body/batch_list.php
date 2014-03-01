<?php $this->uri->segment(2)=="pending_batch_process"?$pending=true:$pending=false;?>

<div class="container">

<div style="padding:0px;">

<div class="dash_bar">
<span><?=$this->db->query("select count(distinct(transid)) as l from king_orders where status=0")->row()->l?></span>
Pending Orders
</div>
<?php $old=$this->db->query("select transid,time from king_orders where status=0 limit 1")->row_array();
if(empty($old))
	$old=false; ?>

<div class="dash_bar">
<a href="<?=site_url("admin/trans/{$old['transid']}")?>"></a>
Oldest pending order :
<span><?=$old['transid']?></span> on <span><?php if($old){?><?=date("d/m/y",$old['time'])?><?php } else echo 'none'?></span>
</div>

<div class="dash_bar_red">
<a href="<?=site_url("admin/partial_shipment")?>"></a>
Partial Shipment Orders
</div>

<div class="clear"></div>
</div>




<table width="100%">
	<tr>
		<td width="300">
<?php if(!$pending){?>

<h3><b>Create batch process</b></h3>
<form id="batch_process" action="<?=site_url("admin/add_batch_process")?>" method="post">
	<table id="add_batch_process_frm" class="datagrid" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td >
				<h4>Orders by :</h4>
			 	
				<div style="clear: both;">
						
					<div style="padding:4px;margin:2px;background: #e6e6e6;">
						<input type="radio" checked="checked" name="snp_pnh" checked value="pnh">Storeking
					</div>
					<div style="background: #e6e6e6;padding:4px;margin:2px;">
						<input type="radio" name="snp_pnh" value="others" >Others
							
						<div id="snp_pnh_part_sel" style="clear: both;display: none;">
								<div style="display: inline-block;"><input type="checkbox" name="snp_pnh_part[]" value="0">Snapittoday</div>
						 	<?php 
						 		$orders_by = array();
						 		foreach($this->db->query("select id,name,lower(trans_prefix) as short_name from partner_info order by name ")->result_array() as $row)
						 		{
						 			$orders_by[strtolower($row['short_name'])]=$row['name'];
						 	?>
						 		<div style="display: block;"><input type="checkbox" name="snp_pnh_part[]" value="<?php echo $row['id']; ?>"><?php echo ucwords($row['name']); ?></div>
						 	<?php 		
						 		}
						 	?>
						 </div>
					</div>
					
					
				 </div>
			</td>
			 
				<td>
					<div id="part_process_orderby_block" style="display: none;">
						<select id="process_orderby" name="process_orderby" >
							<option value="">Process by ERP Orders</option>
							<option value="1">Process by Partner Orderids</option>
							<option value="2">Process by Brand</option>
						</select>
						<div id="process_by_brand" style="display: none;">
							<select name="by_brandid">
								<option value="">Choose</option>
							</select>
							<span id="by_brandid_loading" style="display: none;">Loading...</span>
						</div>
						<div id="process_by_poids" style="display: none;">
							
							<div style="display: none;"><input type="checkbox" name="by_p_oids" value="1" ></div>
							<div>
								<textarea style="width: 98%;height: 130px;" disabled="disabled" rows="4" cols="25" name="p_oids"></textarea>
							</div>
						</div>
					</div>
					<?php
						$pnh_menu_res = $this->db->query("select  e.id,e.name,count(distinct b.transid) as ttl_orders  
																		from king_transactions a 
																		join king_orders b on a.transid = b.transid 
																		join king_dealitems c on b.itemid = c.id
																		join king_deals d on d.dealid = c.dealid 
																		join pnh_menu e on e.id = menuid 
																	where a.is_pnh = 1 and b.status = 0 and a.batch_enabled = 1   
																	group by e.id 
																	order by e.name asc 
																");
						if($pnh_menu_res->num_rows())
						{
					?>
							<div id="process_by_menu" style="display: none;">
								<b>Process by Menu </b><div><input type="checkbox" name="by_menu" value="1" ></div>
								<div id="process_bymenu_list" style="display: none;">
									<ul >
										<?php 
											foreach($pnh_menu_res->result_array() as $pnh_m)
											{
										?>
											<li> <input type="checkbox" name="pmenu_id[]" value="<?php echo $pnh_m['id']; ?>"> (<?php echo $pnh_m['ttl_orders']; ?>) <?php echo $pnh_m['name']; ?> </li>
										<?php
											}
										?>
									</ul>
								</div>
							</div>
					<?php } ?>
					<div><b>No of Orders</b><div><input type="text" name="num_orders" size=3 class="inp"></div></div>
				</td>
			 	
				<td><b>Process Partial</b><div align="left" style="margin:5px;"><input type="checkbox" name="process_partial" value="1"></div></td>
			 		 
				<td><b>Orders Till</b><div><input type="text" name="en_date" size=10 class="inp" value="<?php echo date('Y-m-d',mktime(0,0,0,date('m'),date('d')-1,date('Y')))?>"></div></td>
			 
				<td>
					<b>Remarks</b>
					<div>
						<textarea style="width: 98%;height: 40px;" name="batch_remarks"></textarea>
					</div>
					<br />
					<div align="right"><input type="submit" value="Create Batch"></div>
				</td>
			</tr>
		 
	</table>
 
</form>


<?php }?>
</td>
</tr>
<tr>
<td style="background: #f8f8f8;padding:5px;">

<?php 
	if(!$pending)
	{
?>
	<div class="dash_bar" style="padding:2px 8px;float: right">
		Date filter : <input type="text" size="8" class="inp" id="ds_range" value="<?=$this->uri->segment(3)?>"> to <input size="8" type="text" class="inp"id="de_range" value="<?=$this->uri->segment(4)?>"> <input type="button" value="Show" onclick='showrange()'>
	</div>
<?php 		
	}
?>
<h2 style="margin-top: 10px;"><?=$pending=="pending_batch_process"?"Pending Packing":"Shipment"?> Batch process <?=isset($pagetitle)?$pagetitle:($pending?"":"this month")?></h2>

<?php 
	$batch_status = array('PENDING','PARTIAL','CLOSED');
?>
<table class="datagrid datagridsort"  style="clear: both;width: 100%;">
<thead><th>Batch ID</th><th>No of Invoices</th><th>OrdersBy</th><th width="80">Pending Packing</th><th>Status</th><th>Created On</th><th>Remarks</th></thead>
<tbody>
<?php foreach($batchs as $b){?>
<tr class="<?php echo strtolower($batch_status[$b['status']])?>_batch">
<td><a class="link" href="<?=site_url("admin/batch/{$b['batch_id']}")?>">BATCH<?=$b['batch_id']?></a></td>

<td width="100"><?=$b['num_orders']?></td>
<td>
	<?php 
	$batch_trans_rows = $this->db->query("select tcode,sum(t) as t from (select substr(b.transid,1,3) as tcode,1 as t from shipment_batch_process_invoice_link a join proforma_invoices b on a.p_invoice_no = b.p_invoice_no where a.batch_id = ? group by b.transid  ) as a group by tcode   order by t desc",$b['batch_id'])->result_array();
	foreach($batch_trans_rows as $row)
		echo '<span class="blk">'.$row['tcode'].' : '.$row['t'].'</span> ';
	?>
</td>
<td>
	<?php 
		echo $this->db->query("select count(*) as ttl_unpacked from (select count(*) as t from shipment_batch_process_invoice_link a 
	join proforma_invoices b on a.p_invoice_no = b.p_invoice_no 
	join king_orders c on c.id = b.order_id 
	where batch_id = ? and packed = 0 and c.status != 3 group by a.id ) as g  ",$b['batch_id'])->row()->ttl_unpacked;
	?>
</td>

<td><?php echo $batch_status[$b['status']]?></td>
<td width="130"><?=format_datetime($b['created_on'])?></td>
<td><?php echo $b['batch_remarks']?></td>
</tr>
<?php } if(empty($batchs)){?>
<tr>
<td colspan="100%">no batchs to show</td>
</tr>
<?php }?>

</tbody>
</table>
</td>
</tr>
</table>
</div>

<style>.blk{padding:3px;font-size: 11px;}
.pending_batch td{}
.partial_batch td{background:#FFAAAA !important}
.closed_batch td{background:#11EE11 !important;}
#process_bymenu_list ul{margin-left: 20px;}
#process_bymenu_list ul li{list-style: none;width: 180px;}
</style>

<?php if(!$pending){?>
<script>
$(function(){
	$("#ds_range,#de_range").datepicker();
	
});

$('input[name="snp_pnh_part[]"]').change(function(){
	if($('input[name="snp_pnh_part[]"]:checked').length == 1)
	{
		$('#part_process_orderby_block').show();
		
		$('select[name="process_orderby"]').val(1).trigger('change');
	}
	else
	{
		$('#part_process_orderby_block').hide();
		
		$('select[name="process_orderby"]').val("").trigger('change');
	}
});

$('input[name="by_p_oids"]').change(function(){
	if($(this).attr('checked'))
	{
		$('textarea[name="p_oids"]').attr('disabled',false);
	}else
	{
		$('textarea[name="p_oids"]').val('').attr('disabled',true);
	}
});

$('select[name="process_orderby"]').change(function(){
	$('#by_brandid_loading').hide();
	if($(this).val() == "")
	{
		$('#process_by_brand').hide();
		$('#process_by_poids').hide();
		$('textarea[name="p_oids"]').val('').attr('disabled',true);
	}
	else if($(this).val() == 1)
	{
		$('#process_by_brand').hide();
		$('#process_by_poids').show();
		$('textarea[name="p_oids"]').val('').attr('disabled',false); 
	}else
	{
		$('#process_by_poids').hide();
		$('textarea[name="p_oids"]').val('').attr('disabled',true);
		$('#process_by_brand').show();
		var sel_partnerid = $('input[name="snp_pnh_part[]"]:checked').val();
		$('select[name="by_brandid"] option:gt(0)').remove();
			$.post(site_url+'/admin/jx_pendingorders_brands','partner_id='+sel_partnerid,function(resp){
				if(resp.status == 'success')
				{
					var brandListHtml = '';
						$.each(resp.brandlist,function(a,b){
							brandListHtml += '<option value="'+b.id+'">'+b.name+'</option>';
						});
					$('select[name="by_brandid"]').append(brandListHtml);	
				}
			},'json');
		
	}
});

$('select[name="by_brandid"]').change(function(){
	if($(this).val())
	{
		$('input[name="num_orders"]').val("Loading...");
		$('#by_brandid_loading').show();
		var params = {};
			params.brandid = $(this).val();
			params.partner_id = $('input[name="snp_pnh_part[]"]:checked').val();
			$.post(site_url+'/admin/jx_get_processable_partnerorders_bybrandid',params,function(resp){
				$('#by_brandid_loading').hide();
				$('input[name="num_orders"]').val(resp.total_orders);	
			},'json');
	}else
	{
		$('input[name="num_orders"]').val("");
	}
});

$('input[name="snp_pnh"]').change(function(){
	$('input[name="num_orders"]').val("");
	$('input[name="snp_pnh_part[]"]:checked').attr('checked',false);
	if($(this).val() == 'others')
	{
		$('input[name="snp_pnh_part[]"]').attr('checked',true);
		$('#snp_pnh_part_sel').show();	
		$('#process_by_menu').hide();
		
	}
	else
	{
		$('#snp_pnh_part_sel').hide();
		$('#process_by_poids').hide();
		$('#process_by_menu').show();
		$('select[name="process_orderby"]').val("").trigger('change');
		$('#part_process_orderby_block').hide();
	}
});

$('input[name="by_menu"]').attr('checked',false);
$('#process_bymenu_list input[type="checkbox"]').attr('checked',false);

$('input[name="by_menu"]').change(function(){
	if($(this).attr('checked'))
	{
		$('#process_bymenu_list').show();
	}else
	{
		$('#process_bymenu_list').hide();
	}
});




$('input[name="en_date"]').datepicker();


function showrange()
{
	if($("#ds_range").val().length==0 ||$("#ds_range").val().length==0)
	{
		alert("Please enter date range");
		return;
	}
	location='<?=site_url("admin/".$this->uri->segment(2))?>/'+$("#ds_range").val()+"/"+$("#de_range").val(); 
}

$('input[name="snp_pnh"]:checked').trigger('change');

$('#batch_process').submit(function(){
	var error_inp_arr = new Array();
	process_frm = true;
	if(!$('input[name="snp_pnh"]:checked').length)
	{
		error_inp_arr.push("Please choose orders by");
		process_frm = false;
	}else
	{
		var sel_orderby = $('input[name="snp_pnh"]:checked').val();
			if(sel_orderby == 'others')
			{
				if(!$('input[name="snp_pnh_part[]"]:checked').length)
				{
					error_inp_arr.push("Please choose atleast one partner");
					process_frm = false;
				}else
				{
					if($('select[name="process_orderby"]').val() != "")
					{
						if($('input[name="snp_pnh_part[]"]:checked').length == 1)
						{
							if($('select[name="process_orderby"]').val() == 1)
							{
								 
									var p_oids = $('textarea[name="p_oids"]').val();
										p_oids = p_oids.replace(/\n/g,",");
										p_oids = p_oids.replace(/,,/g,",");
										p_oids = p_oids.replace(/,,/g,",");
										p_oids = p_oids.replace(/,,/g,",");
										p_oids = p_oids.replace(/,,/g,",");
										
										$('textarea[name="p_oids"]').val(p_oids);
										if(!p_oids.length)
										{
											error_inp_arr.push("Please enter atleast one order no to process");
											process_frm = false;
										}
							 
							}else 
							{
								if($('#process_by_brand select').val() == "")
								{
									error_inp_arr.push("Please choose brand to process orders");
									process_frm = false;
								}
							}
						}
					}
				}
			}else
			{
				if($('input[name="by_menu"]').attr('checked'))
				{
					if(!$('input[name="pmenu_id[]"]:checked').length)
					{
						error_inp_arr.push("Please Choose atleast one menu");
						process_frm = false;
					}
				}
			}
			
			var num_ords = $('input[name="num_orders"]').val()*1;
				num_ords = isNaN(num_ords)?0:num_ords;
				
			if(!$('input[name="num_orders"]').val())
			{
				error_inp_arr.push("Invalid num of orders entered");
				process_frm = false;
			}
				
			
	}
	
	if(error_inp_arr.length)
	{
		alert(error_inp_arr.join("\r\n"));
	}
	
	return process_frm;
})

</script>


<style>
	#add_batch_process_frm tfoot{display:none;}
</style>
<?php }?>
<script>
$(".datagridsort").tablesorter();
</script>

<?php

