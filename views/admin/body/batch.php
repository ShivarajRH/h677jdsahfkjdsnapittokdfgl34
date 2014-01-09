<style>
	.leftcont{display: none}
	.subdatagrid{width: 100%}
	.subdatagrid th{padding:5px;font-size: 11px;background: #F4EB9A;color: maroon}
	.subdatagrid td{padding:3px;font-size: 12px;}
	.subdatagrid td a{color: #121213;}
	.processed_ord td,.shipped_ord td{color: green  !important;}
	.processed_ord td a,.shipped_ord td a{color: green !important;}
	.cancelled_ord td{color: #cd0000 !important;}
	.cancelled_ord td a{color: #cd0000 !important;}
	.tgl_ord_prod {display: block;min-width: 300px;padding:5px;background: #fafafa;}
	.tgl_ord_prod a{display: block;text-align: center;color: #333;font-size: 12px;text-decoration: underline;}
	.tgl_ord_prod_content {display: none;}
	.dashbar_width {min-width: 0px;}
</style>

<div class="container " >
	<div style="margin:16px 3px 3px;float: right;background: #ffffa0;padding:5px;">
		<b>Remarks : </b>
		<p style="margin:3px;font-size: 11px;min-width: 100px;display: inline;"><?php echo $batch['batch_remarks']?$batch['batch_remarks']:'--na--'; ?></p>
	</div>

	<?php
		$is_pnh=false;
		$inv=$invoices[0];
		
		$fil_sorted_products = array();
		$fil_products = array();
		$fil_couriers = array();

		if(empty($inv['transid'])) $inv['transid']=$inv['pi_transid'];
		 
	?>

	<div style="float:right;margin:20px 8px 8px;"><a target="_blank" href="<?=site_url("admin/product_proc_list_for_batch/{$batch['batch_id']}")?>">Generate product procurement list</a></div>	

	<h2 >Shipment Batch Process : BATCH<?=$batch['batch_id']?> - 
		<b style="font-size: 90%">
			<?php
				if($batch['status'] == 0)
					echo '<span  style="color:#cd0000">Open</span>';
				else if($batch['status'] == 1)
				 	echo '<span  style="color:#cd0000">Partial</span>';
				else if($batch['status'] == 2)
				 	echo '<span  style="color:green">Closed</span>';
			?>
		</b>
	</h2>

	<?php
		if($this->db->query("select is_pnh from king_transactions where transid=?",$inv['transid'])->row()->is_pnh==1){ $is_pnh=true; }
					
		
		$trans_ids = array();
		if(empty($inv['transid'])) 
			$inv['transid']=$inv['pi_transid'];
		foreach($invoices as $inv)
		{
			if(empty($inv['transid'])) 
				$inv['transid']=$inv['pi_transid'];
			array_push($trans_ids,"'".$inv['transid']."'");
		}
	?>
	<div style="clear:both;float: left;">
		<div class="dash_bar dash_bar_right dashbar_width">
				Total : <b><?=$this->db->query("select count(distinct p.p_invoice_no) as t from proforma_invoices p
				join shipment_batch_process_invoice_link s on s.p_invoice_no = p.p_invoice_no 
				where batch_id=? ",$batch['batch_id'])->row()->t;?></b>
		</div>
		<div class="dash_bar dash_bar_right dashbar_width">
				Cancelled :<b><?=$this->db->query("select count(distinct p.p_invoice_no) as t from proforma_invoices p
				join shipment_batch_process_invoice_link s on s.p_invoice_no = p.p_invoice_no 
				where batch_id=? and invoice_status = 0 ",$batch['batch_id'])->row()->t;?></b>
		</div>
		<div class="dash_bar dash_bar_right dashbar_width">
				Packed : <b><?=$this->db->query("select count(distinct p.p_invoice_no) as t from proforma_invoices p
				join shipment_batch_process_invoice_link s on s.p_invoice_no = p.p_invoice_no 
				where batch_id=? and invoice_status = 1 and invoice_no != 0 ",$batch['batch_id'])->row()->t;?> </b>
		</div>
		<div class="dash_bar dash_bar_right dashbar_width">
				Pending :<b><?=$this->db->query("select count(distinct p.p_invoice_no) as t from proforma_invoices p
				join shipment_batch_process_invoice_link s on s.p_invoice_no = p.p_invoice_no 
				where batch_id=? and invoice_status = 1 and invoice_no = 0 ",$batch['batch_id'])->row()->t;?></b>
		</div>
		<?php
			if($this->db->query("select count(distinct p.p_invoice_no) as t from proforma_invoices p
				join shipment_batch_process_invoice_link s on s.p_invoice_no = p.p_invoice_no 
				where batch_id=? and invoice_status = 1 and invoice_no = 0 ",$batch['batch_id'])->row()->t == 0)
				{
					$this->db->query("update shipment_batch_process set status = 2 where batch_id = ? and status != 2 limit 1",$batch['batch_id']);
				}
		?>
	</div>
	
	<?php if($is_pnh) { ?>
		<span style="float:right;margin-top:7px;">
		Filter by : <select id="territory" name="territory" style="width: 150px">
						<option value="0">All Territories</option>
						<?php
							$terr_list = $this->db->query("select t.territory_name,t.id from king_transactions ta join pnh_m_franchise_info f on f.franchise_id=ta.franchise_id join pnh_m_territory_info t on t.id=f.territory_id where ta.transid in (".(implode(',',$trans_ids)).") group by territory_id ")->result_array(); 
							foreach($terr_list as $tran)
							{
						?>
							<option value="<?=$tran['id']?>"><?=$tran['territory_name']?></option>
						<?php
							}
						?> 
					</select>
	
	
					<select id="town" name="town" style="width: 150px">
						<option value="0">All Towns</option>
						<?php 	
	  						$town_list = $this->db->query("select tw.id,tw.town_name from king_transactions ta join pnh_m_franchise_info f on f.franchise_id=ta.franchise_id join pnh_towns tw on tw.id=f.town_id where ta.transid in (".(implode(',',$trans_ids)).") group by tw.id ")->result_array(); 
	  						foreach($town_list as $town)
						 	{
						 ?>
								<option value="<?=$town['id']?>"><?=$town['town_name']?></option>
						<?php 
							}
						?>
					</select>
	  
					<select id="franchise" name="franchise">
						<option value="0">All Franchises</option>
					</select>
	
		</span>
	<?php }else{?>

		<span style="float: right;padding:3px;">
		
			Courier:<select name="filterbycourier_list" class="inp" style="display: none;width: 150px;">
						<option value="">ALL</option>
					</select>
			&nbsp;
			
			Product:<select name="filterbyprod_list" class="inp" style="display: none;width: 300px;">
						<option value="">All</option>
					</select>
			&nbsp;
			&nbsp;
		</span>
	<?php } ?>	
		
	</div>
 
	<table id="batch_orders" class="datagrid datagridsort" width="99%">
		<thead>
			<tr>
				<th><input type="checkbox" class="chk_all"></th>
				<th>Sno</th>
				<th>Proforma Invoice</th>
				<th>Invoice Number</th>
				<th>Order</th>
				<th>Ordered On</th>
				<th>Packed</th>
				<th>Shipped</th>
				<th>Packed On</th>
				<th>Shipped On</th>
				<?php if($is_pnh){?>
					<th>Territory</th>
					<th>Franchise</th>
				<?php }?>
					<th>CourierName</th>
					<th>Action</th>
					<th>
						<a href="javascript:void(0)" style="text-align:center;width:100%;float:left" id="exp_col_list">Show/hide Orders</a>
					</th>
			</tr>
		</thead>

		<tbody>
			<?php 
				$sno=0; foreach($invoices as $inv){ if(empty($inv['transid'])) $inv['transid']=$inv['pi_transid'];
				$is_fran_suspended = 0;
				$fr_reg_level = $fr_reg_level_color = '';
				if($is_pnh)
					{
						$fran=$this->db->query("select f.created_on as registered_on,f.is_suspended,f.franchise_name,f.franchise_id,t.territory_name,t.id as terry_id,tw.id as town_id from king_transactions ta join pnh_m_franchise_info f on f.franchise_id=ta.franchise_id join pnh_m_territory_info t on t.id=f.territory_id join pnh_towns tw on tw.id=f.town_id where ta.transid=?",$inv['transid'])->row_array();
						if(!$fran)
							continue;
						
						$is_fran_suspended = $fran['is_suspended']?1:0;
						
						$fr_reg_diff = ceil((time()-$fran['registered_on'])/(24*60*60));
						 
						if($fr_reg_diff <= 30)
						{
							$fr_reg_level_color = '#cd0000';
							$fr_reg_level = 'Newbie';
						}
						else if($fr_reg_diff > 30 && $fr_reg_diff <= 60)
						{
							$fr_reg_level_color = 'orange';
							$fr_reg_level = 'Mid Level';
						}else if($fr_reg_diff > 60)
						{
							$fr_reg_level_color = 'green';
							$fr_reg_level = 'Experienced';
						}
								
					}
			?>

			<tr <?php if($is_pnh){?>class="tbatch courier_<?=str_replace(' ','_',$inv['p_courier_name']);?> tw<?=$fran['town_id']?> ty<?=$fran['terry_id']?> trans_row fran<?=$fran['franchise_id']?>"<?php }else{ ?>class="trans_row courier_<?=str_replace(' ','_',$inv['p_courier_name']);?> " <?php }?>>
				<td width=1 align="center">
					<?php if(!$inv['packed']){?>
						<input type="checkbox" class="chk" value="<?=$inv['p_invoice_no']?>">
					<?php }?>
				</td>
				
				<td width="10">
					<?=++$sno?>
				</td>
				
				<td>
					<a href="<?=site_url("admin/proforma_invoice/{$inv['p_invoice_no']}")?>" <?=!$inv['p_invoice_status']?'style="text-decoration:line-through;"':''?>><?=$inv['p_invoice_no']?></a>
					<?php echo ($is_fran_suspended?'<br><b style="color:#cd0000;font-size:10px;">Franchise Suspended</b>':'');?>
				</td>
				
				<td>
					<?php if($inv['invoice_no']!=0){?>
						<a href="<?=site_url("admin/invoice/{$inv['invoice_no']}")?>" <?=!$inv['invoice_status']?'style="text-decoration:line-through;"':''?>><?=$inv['invoice_no']?></a>
					<?php }?>
				</td>
				
				<td>
					<a href="<?=site_url("admin/trans/{$inv['transid']}")?>"><?=$inv['transid']?></a>
				</td>
			
				<td>
					<?=format_date(date('Y-m-d H:i:s',$inv['ordered_on']))?>
				</td>
				
				<td>
					<?=$inv['packed']?"YES":"NO"?>
				</td>
				
				<td>
					<?=$inv['shipped']?"YES":"NO"?>
				</td>
				
				<td>
					<?=$inv['packed']?$inv['packed_on']:"na"?>
				</td>
				
				<td>
					<?=$inv['shipped']?$inv['shipped_on']:"na"?>
				</td>
				
				<?php if($is_pnh){?>
					<td>
						<?=$fran['territory_name']?>
					</td>
					
					<td>
						<?=$fran['franchise_name']?>
						<span style="font-size: 11px;color:<?php echo $fr_reg_level_color;?>">(<b><?=$fr_reg_level?></b>)</span>
					</td>
				<?php }?>
				
				<td>
					<?php echo $inv['p_courier_name']; $fil_couriers[str_replace(' ','_',$inv['p_courier_name'])]=$inv['p_courier_name'];?>
				</td>
				
				<td>
					<?php 
					$is_packed = 1;
					
					if($inv['p_invoice_status']==0 && $inv['p_invoice_no']!=0) 
						echo 'Proforma Cancelled';
					else
					{
						if(!$inv['packed'])
						{
							 $is_packed = 0;
							 if($inv['invoice_no'])
							 {
							 	if($is_pnh)
								{
							?>
									<a class="link" href="<?=site_url("admin/outscan/1")?>">Update as packed</a>
							<?php
								}else
								{
							?>
								 
							<?php		
								}	
							 }else
							 {
						?>
								<a class="link" href="<?=site_url("admin/pack_invoice/{$inv['p_invoice_no']}")?>"  target="blank">Prepare invoice</a> 
						<?php	 		
							 }
						?>
						
					<?php 
						}elseif(!$inv['shipped']){
							
							if(!$is_pnh)
							{
					?>
								<a class="link" href="<?=site_url("admin/outscan")?>">ship products</a>
					<?php			
							}
						}
						else
						{
					?>
						<a class="link" href="<?=site_url("admin/invoice/{$inv['invoice_no']}")?>">view</a>
					<?php 
						}
					}
					?>
				</td>
				<td style="padding:0px;background: #fafafa !important;">
					<div class="tgl_ord_prod"><a href="tgl_ord_prods">Show Deals</a></div>
					<div class="tgl_ord_prod_content">
						<table class="subdatagrid" cellpadding="0" cellspacing="0">
							<thead>
								<th>Slno</th>
								<th>OID</th>
								<th width="200">ITEM</th>
								<th>QTY</th>
								<th>MRP</th>
								<th>Amount</th>
							</thead>
							<tbody>
								<?php 
									$o_item_list = $this->db->query("select t.partner_id,a.status,a.id,a.itemid,b.name,a.quantity,i_orgprice,i_price,i_discount,i_coup_discount 
																		from king_orders a
																		join king_transactions t on a.transid = t.transid 
																		join king_dealitems b on a.itemid = b.id 
																		join proforma_invoices c on a.id = c.order_id and a.transid = c.transid  
																		where a.transid = ? and p_invoice_no = ? order by a.status 
																	",array($inv['transid'],$inv['p_invoice_no']))->result_array();
									$oi = 0;
									foreach($o_item_list as $o_item)
									{
										$is_partner_order_batch = $o_item['partner_id'];
										$filter_byprod = ' ';
										$filter_byprod_class = '';
										$ord_qty = $prod_qty = 0;
										if($is_partner_order_batch && $is_packed==0)
										{
											$sql_part = "(select a.product_id,a.product_name,(b.qty*c.quantity) as ttl_qty,b.qty as pqty,c.quantity as oqty  
																from m_product_info a 
																join m_product_deal_link b on a.product_id = b.product_id 
																join king_orders c on c.itemid = b.itemid  
																where c.id = ? 
															)
															union(
																select a.product_id,a.product_name,(d.qty*c.quantity) as ttl_qty,d.qty as pqty,c.quantity as oqty  
																from m_product_info a 
																join products_group_orders b on a.product_id = b.product_id 
																join king_orders c on c.id = b.order_id 
																join m_product_group_deal_link d on d.itemid = c.itemid 
																where b.order_id = ?
															)";
											$p_prod_det = $this->db->query($sql_part,array($o_item['id'],$o_item['id']))->row_array();
											$filter_byprod = ' prod_id="'.$p_prod_det['product_id'].'" prod_name="'.$p_prod_det['product_name'].'" qty="'.$p_prod_det['ttl_qty'].'" ';
											$filter_byprod_class = ' filter_byprod selprod_'.$p_prod_det['product_id'];
											
											if(!isset($fil_products[$p_prod_det['product_id']]))
											{
												$fil_products[$p_prod_det['product_id']] = array($p_prod_det['product_name'],0);
												$fil_sorted_products[$p_prod_det['product_id']] = $p_prod_det['product_name'];
											}
											
											$fil_products[$p_prod_det['product_id']][1] += $p_prod_det['ttl_qty'];
											$prod_qty = $p_prod_det['pqty'];
											$ord_qty = $p_prod_det['oqty'];
										}
										
										$is_cancelled = ($o_item['status']==3)?1:0;
										$ord_stat_txt = '';
										if($o_item['status'] == 0)
											$ord_stat_txt = 'pending';
										else if($o_item['status'] == 1)
										 	$ord_stat_txt = 'processed';
										else if($o_item['status'] == 2)
										 	$ord_stat_txt = 'shipped';
										 else if($o_item['status'] == 3)
										 	$ord_stat_txt = 'cancelled';	
								?>
									<tr pqty="<?php echo $prod_qty; ?>" oqty="<?php echo $ord_qty; ?>" class="prodfil <?php echo $ord_stat_txt.'_ord'?> <?php echo $filter_byprod_class; ?>" <?php echo $filter_byprod; ?> >
										<td width="20"><?php echo ++$oi; ?></td>
										<td width="40"><?php echo $o_item['id'] ?></td>
										<td><?php echo anchor('admin/pnh_deal/'.$o_item['itemid'],$o_item['name']) ?></td>
										<td width="20"><?php echo $o_item['quantity'] ?></td>
										<td width="40"><?php echo $o_item['i_orgprice'] ?></td>
										<td width="40"><?php echo round($o_item['i_orgprice']-($o_item['i_coup_discount']+$o_item['i_discount']),2) ?></td>
									</tr>	
								<?php 		
									}
								?>
							</tbody>
						</table>
					</div>
				</td>
			</tr>
			<?php }?>
		</tbody>
	</table>

	<div style="background:#eee;padding:3px;">With Selected : <input type="button" class="do_sw" value="Generate stock procure list">
		<form id="f_sw" action="<?=site_url("admin/stock_procure_list")?>" method="post" target="_blank">
			<input type="hidden" class="f_tids" name="tids" >
		</form>
		
		<div align="left" style="padding:10px;background: #fcfcfc;line-height: 22px;">
			
			<form action="<?php echo site_url('admin/process_bulkorderinvoice') ?>" target="hndl_process_bulkinvoice" id="process_bulk_invoice_frm" method="post" style="display: none;">
				<b>Bulk Invoice Orders</b> - <span id="sel_prodname"></span> <br>
				<input type="hidden" value="<?php echo $batch['batch_id']; ?>" name="batch_id">
				<textarea name="pinv_nos" style="display: none;"></textarea> 
				<b>Selected Invoices</b> <input type="text" readonly="readonly" size="5" class="inp" value="0" id="bulk_orderttl" >
				<b>Required Qty </b> <input type="text" readonly="readonly" size="5" class="inp" value="0" id="process_bulkorderinv_ttlqty" >
				<b>Allot Qty </b> <input type="text" size="5" class="inp" value="0" id="process_bulkorderinv_allotqty" >
				<input type="submit" value="Generate Bulk Invoice">
			</form>
			
			<iframe id="hndl_process_bulkinvoice" name="hndl_process_bulkinvoice" style="height: 50px;border:0px solid #cdcdcd;"></iframe>
			
			<div id="bulk_invoiceprocesslog" style="display: none;">
				<h3>Bulk Invoice Orders Log</h3>
					<table class="datagrid">
						<thead>
							<th>Groupno</th><th>Product Name</th><th>Total</th><th>CreatedOn</th><th>&nbsp;</th>
						</thead>
						<tbody>
						</tbody>
					</table>
			</div>
		</div>
	</div>
</div>

<script>
var batch_id = '<?php echo $batch['batch_id']; ?>';
function load_bulkinvoice_process()
{
	$.getJSON(site_url+'/admin/jx_loadbulkinvoicelog/'+batch_id,function(resp){
		if(resp.ttl)
		{
			var logdata_tbl = '';
				$.each(resp.logdata,function(a,b){
					logdata_tbl += '<tr><td>'+b.grpno+'</td><td>'+b.product_name+'</td><td>'+b.pinv_ttl+'</td><td>'+b.created_on+'</td><td><input type="button" value="Print Invoices" onclick="print_bulkinvs('+b.grpno+')" ></td></tr>';
				});
				$('#bulk_invoiceprocesslog tbody').html(logdata_tbl);
				$('#bulk_invoiceprocesslog').show();
		}else
		{
			$('#bulk_invoiceprocesslog').hide();	
		}
	});
}

function print_bulkinvs(grpno)
{
 	if(confirm("Are you sure want to Print Invoices"))
	{
		window.open(site_url+'/admin/print_bulkorderinvs/'+grpno);
	}
}

$('#process_bulk_invoice_frm').submit(function(){
	if(!$('.trans_row .chk:checked').length)
	{
		alert("Please select atleast one proforma invoice");
		return false;
	}
	var pinv_list = new Array();
		$('.trans_row:visible .chk:checked').each(function(){
			pinv_list.push($(this).val());
		});
		if(!pinv_list.length)
		{
			alert("Please select atleast one proforma invoice");
			return false;
		}
	$('textarea[name="pinv_nos"]').val(pinv_list.join(','));
	
	if($('#process_bulkorderinv_allotqty').val()!= $('#process_bulkorderinv_ttlqty').val())
	{
		alert("Required Qty is not matching with the allot qty");
		return false;
	}
	 
	 if(confirm("Are you sure want to process bulk orders"))
	 {
		$.post(site_url+'/admin/process_batchorderinvoice',$(this).serialize(),function(resp){
			if(resp.grpno)
			{
				alert("Total "+resp.processed+' Invoices Created');
				load_bulkinvoice_process();
			}else
			{
				alert("error");
			}
		},'json');
	}
	
	return false;
});

load_bulkinvoice_process();

var filcourlist = new Array();
var filprodlist = new Array();
var filprodlist_qty = new Array();
var filprodlistHtml = '';
<?php 
	asort($fil_sorted_products);
	foreach ($fil_sorted_products as $fpid => $fpname)
	{
?>		
		filprodlistHtml += '<option value="<?php echo $fpid ?>"><?php echo htmlspecialchars(addslashes($fpname)).' ('.$fil_products[$fpid][1].' Qty)' ?></option>';
<?php 				
	}
?>

	/*
	$('.filter_byprod').each(function(){
		var f_pid =  $(this).attr('prod_id');
			if(filprodlist_qty[f_pid] == undefined)
				filprodlist_qty[f_pid] = 0;
			
		filprodlist_qty[f_pid] += $(this).attr('qty')*1;
		filprodlist[f_pid] = $(this).attr('prod_name');	
	});
	var filprodlistHtml = '';
		for(var a in filprodlist)
		{
			filprodlistHtml += '<option value="'+a+'">'+filprodlist[a]+' ('+filprodlist_qty[a]+')</option>'; 
		}
	*/	
	
	if(filprodlistHtml)
		$('select[name="filterbyprod_list"]').append(filprodlistHtml).show();
	
	function chk_bulkorderprocess_qty()
	{
		var ttl_qty = 0; 
			$('.trans_row:visible .chk:checked').each(function(){
				var pqty = $('.prodfil',$(this).parent().parent()).attr('pqty')*1;
				var oqty = $('.prodfil',$(this).parent().parent()).attr('oqty')*1;
					ttl_qty += pqty*oqty;
			});
			
			$('#bulk_orderttl').val($('.trans_row:visible .chk:checked').length);
			
			$('#process_bulkorderinv_ttlqty').val(ttl_qty);
		
	}
	
	$('select[name="filterbyprod_list"]').change(function(){
	
		$('.chk_all').attr('checked',false);
		$('.trans_row .chk').attr('checked',false);
		
		$('#process_bulk_invoice_frm').hide();
		
		$('#sel_prodname').html($('option:selected',this).text());
		
		$('#bulk_orderttl').val(0);
		$('select[name="filterbycourier_list"]').val("");
		if($(this).val() == "")
		{
			$('.trans_row').show();
		}else
		{
			$('.trans_row').hide();
			var selprodid = $(this).val();
				$('.selprod_'+selprodid).each(function(){
					$(this).parents('.trans_row:first').show();
				});	
				$('#bulk_orderttl').val($('.selprod_'+selprodid).length);
			$('.chk_all').attr('checked',true).trigger('change');
			
			$('#process_bulk_invoice_frm').show();	
		}
		
		chk_bulkorderprocess_qty();
		
	});
	$('#bulk_orderttl').val(0);
var filcourlisthtml = ''; 	
<?php 
	asort($fil_couriers);
	foreach($fil_couriers as $cr_indx=>$cr_name)
	{
?>
		filcourlisthtml += '<option value="<?php echo $cr_indx ?>"><?php echo $cr_name; ?></option>';
<?php		
	}
?>	
	if(filcourlisthtml)
		$('select[name="filterbycourier_list"]').append(filcourlisthtml).show();
	
	$('select[name="filterbycourier_list"]').change(function(){
		$('select[name="filterbyprod_list"]').val("");
		if($(this).val() == "")
		{
			$('.trans_row').show();
		}else
		{
			$('.trans_row').hide();
			var selcouriername = $(this).val();
				$('.courier_'+selcouriername).show();	
		}
	});


	$('.chk').change(function(){
		chk_bulkorderprocess_qty()
	});
$('.tgl_ord_prod a').click(function(e){
	e.preventDefault();
	if($(this).parent().next().is(':visible'))
	{
		$(this).text('Show Deals');
		$(this).parent().next().hide();
	}else
	{
		$(this).text('Hide Deals');
		$(this).parent().next().show();
	}
});


$('#exp_col_list').click(function(e){
	e.preventDefault();
	if($(this).data('collapse'))
	{
		$(this).data('collapse',false);
		$('.tgl_ord_prod a').text('Hide Deals');
		$('.tgl_ord_prod_content').show();
	}else
	{
		$(this).data('collapse',true);
		$('.tgl_ord_prod a').text('Show Deals');
		$('.tgl_ord_prod_content').hide();
	}
}).data('collapse',false);


$(function(){
	$('#exp_col_list').trigger('click');
	
	$("#town").change(function(){
		$(".tbatch").hide();
		tw=$("#town").val();
		s=".tbatch";
		if(tw!=0)
			{
				//alert('tw');
        		$('select[name="franchise"]').html('').trigger("liszt:updated");
        		var batch_id = <?=$batch['batch_id']?>;
      			$.post("<?=site_url("admin/jx_batchfranch")?>",{tw:$("#town").val(),batch_id:batch_id},function(resp){
					if(resp.status == 'error'){
						alert(resp.message);
						//$('select[name="territory"]').val('0');
					}else{
						var fran_list_html = '';
							fran_list_html += '<option>Select</option>';
						
							$.each(resp.fran_list,function(i,itm){
								fran_list_html += '<option value="'+itm.franchise_id+'">'+itm.franchise_name+'</option>';
							});
							$('select[name="franchise"]').html(fran_list_html).trigger("liszt:updated");
					}
				},'json');
				s=s+".tw"+tw+"";
			}
		$(s).show();
	});
	
	$("#territory").change(function(){
		$('select[name="town"]').val('0');
		$('select[name="franchise"]').val('0');
		$(".tbatch").hide();
		ty=$("#territory").val();
		s=".tbatch";
		if(ty!=0)
		{
			$('select[name="town"]').html('').trigger("liszt:updated");
			$('select[name="franchise"]').html('').trigger("liszt:updated");
        		var batch_id = <?=$batch['batch_id']?>;
      			$.post("<?=site_url("admin/jx_gettown")?>",{ty:$("#territory").val(),batch_id:batch_id},function(resp){
					if(resp.status == 'error'){
						alert(resp.message);
						//$('select[name="territory"]').val('0');
					}else{
						var town_list_html = '';
						var franch_list_html = '';
						
							town_list_html += '<option>Select</option>';
							franch_list_html += '<option>Select</option>';
						
							$.each(resp.town_list,function(i,itm){
								town_list_html += '<option value="'+itm.id+'">'+itm.town_name+'</option>';
							});
							$.each(resp.franch_list,function(i,itm){
								franch_list_html += '<option value="'+itm.franchise_id+'">'+itm.franchise_name+'</option>';
							});
							$('select[name="town"]').html(town_list_html).trigger("liszt:updated");
							$('select[name="franchise"]').html(franch_list_html).trigger("liszt:updated");
					}
				},'json');
			s=s+".ty"+ty+"";
		}
		$(s).show();
	});
	
	$("#franchise").change(function(){
		$(".tbatch").hide();
		fran=$("#franchise").val();
		s=".tbatch";
		if(fran!=0)
			s=s+".fran"+fran+"";
		$(s).show();
	});

	$(".chk").attr("checked",false);
	$(".chk_all").change(function(){
		if($(this).attr("checked"))
			$(".chk").attr("checked",true);
		else
			$(".chk").attr("checked",false);
	}).attr("checked",false);
	
	

	$(".do_sw").click(function(){
		var tids=[];
		$(".chk:checked").each(function(){
			tids.push($(this).val());
		});
		if(tids.length==0)
		{
			alert("No items selected");
			return false;
		}
		$(".f_tids").val(tids.join(","));
		$("#f_sw").submit();
	});	
	$("#batch_orders").tablesorter({headers:{0:{sorter:false}},sortList: [[<?php echo $is_pnh?11:1; ?>,0]]});
	
	$('#batch_orders tr.trans_row').each(function(i,itm){
		$('td:eq(1)',this).text(i+1);
	});
	
});

</script>


<?php
