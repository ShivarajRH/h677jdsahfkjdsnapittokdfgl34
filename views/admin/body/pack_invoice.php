<style>
#scanned_summ{
	width: 160px;background: tomato;bottom: 0px;left:0px;position: fixed;border-top:5px solid #FFF;
	text-align: center;
	color: #FFF;
	font-size: 32px;
}
#scanned_summ h3{
	font-size: 20px;margin-top:10px;margin-bottom: 0px;
}
.scanned_summ_total{
	padding:5px;
}
.scanned_summ_stats{
	padding:5px;font-size: 15px;font-weight: bold;text-align: left;border-bottom: 1px dotted #FFF;
}
.ttl_num{
	float: right;font-size: 18px;
}
.have {
	background: yellow !important;
	font-weight: bold;
	text-align: center;
}

.scanned {
	background: #aaa;
	font-size: 110%;
}

.scanned .have {
	background: #f55 !important;
	color: #fff;
	font-size: 170%;
}
.partial {
	background: orange !important;
}

.disabled {
	background: #aaa !important;
	color: #FFF !important;
}
</style>
<div class="container">
<?php 
	$prod_imei_list = array();
	$batch_id = $this->db->query("select batch_id from shipment_batch_process_invoice_link where p_invoice_no = ? ",$invoice[0]['p_invoice_no'])->row()->batch_id;
	$p_inv_no = $invoice[0]['p_invoice_no']; 
	
	$t_order_det = $this->db->query("select a.init,a.franchise_id,a.is_pnh,a.partner_id,a.transid,b.ship_person,b.ship_city from king_transactions a join king_orders b on a.transid = b.transid where b.id = ? ",$invoice[0]['order_id'])->row_array();  
	if($t_order_det['partner_id'])
		{
			$order_placed_by = $this->db->query("select name from partner_info where id = ? ",$t_order_det['partner_id'])->row()->name;
		}else
		{
			if($t_order_det['is_pnh'])
				{
					$order_placed_by = 'Paynearhome';
				}else
				{
			$order_placed_by = 'Snapittoday';
				}
		}
?>

<span style="float: right">
	<?php 
		if($t_order_det['is_pnh'])
		{
			$fr_det = $this->db->query("select franchise_id,franchise_name,territory_name,town_name 
												from pnh_m_franchise_info a 
												join pnh_m_territory_info b on a.territory_id = b.id 
												join pnh_towns c on c.id = a.town_id
												where franchise_id = ?  ",$t_order_det['franchise_id'])->row_array();
			echo '<b>Franchise : </b> '.$fr_det['franchise_name'].' <b>Territory : </b> '.$fr_det['territory_name'].' <b>Town : </b> '.$fr_det['town_name'];
	?>
			<b>Trans#</b> : <?php echo anchor_popup('admin/trans/'.$t_order_det['transid'],$t_order_det['transid']); ?> &nbsp;
			<b>OrderedOn</b> : <?php echo format_datetime(date('Y-m-d H:i:s',$t_order_det['init'])); ?> &nbsp;
	
	<?php
		}else 
		{
	?>
			<b>Trans#</b> : <?php echo anchor_popup('admin/trans/'.$t_order_det['transid'],$t_order_det['transid']); ?> &nbsp;
			<b>OrderedOn</b> : <?php echo format_datetime(date('Y-m-d H:i:s',$t_order_det['init'])); ?> &nbsp;
			<b>Ship Details</b> : <?php echo $t_order_det['ship_person'].', '.$t_order_det['ship_city']; ?> &nbsp;
	<?php } ?>
	 
	
</span>
	<h2>Scan &amp; pack proforma invoice : <?=$invoice[0]['p_invoice_no']?></h2>
	<div style="padding: 5px 10px; position: fixed; bottom: 50px; background: #ffaa00; right: 10px;">
	Scan Barcode : <input class="inp" id="scan_barcode" style="padding: 5px;"> <input type="button" value="Go" onclick='validate_barcode()'></div>

	<div id="scanbyimei" style="padding: 5px 10px; position: fixed; bottom: 50px; background: #ffaa00; right: 326px;display:none">
		Scan Imeino : <input class="inp" id="scan_imeino" style="padding: 5px;"> <input type="button" value="Go" onclick='validate_imeino()'>
	</div>
	
<table class="datagrid" style="margin-top: 20px;" width=100%>
	<thead>
		<tr>
			<th>Deal Picture</th>
			<th>Product name</th>
			<th>Deal</th>
			<th>MRP</th>
			<th>Order MRP</th>
			<th>Qty</th>
			<th style="padding: 0px;">
			<div style="padding: 5px;">Stock MRPs</div>

			<table class="subgrid" cellpadding="0" cellspacing="0" style="border: 0px !important; width: 100%; background: #fcfcfc !important; font-size: 11px;">
				<tr>
					<td style="background: #fcfcfc !important; vertical-align: middle; color: #000">
					<div style="width: 60px; text-align: center;">MRP</div>
					</td>
					
					<td style="background: #fcfcfc !important; vertical-align: middle; color: #000; text-align: center;display:none">
					<div style="width: 30px;">Stock</div>
					</td>
					
					<td style="background: #fcfcfc !important; vertical-align: middle; color: #000; text-align: center;">
						<div style="width: 60px; text-align: center;">Alloted</div>
					</td>
					
					<td style="background: #fcfcfc !important; vertical-align: middle; width: 30px !important; color: #000" width="10">
						<div style="width: 30px; text-align: center;">Scan</div>
					</td>
				</tr>
			</table>

			</th>
			<th>
			<div style="width: 60px; text-align: center;">Refund Amount</div>
			</th>
			<th>Scanned</th>
			<th>Status</th>
		</tr>
	</thead>
	<tbody>
		<?php
			
			$has_imei_scan = 0;
			foreach($invoice as $i)
				{
					$consider_for_refund = 0;
					if($i['is_pnh'])
					$consider_for_refund = $this->db->query("select consider_mrp_chng from pnh_menu where id = ? ",$i['menuid'])->row()->consider_mrp_chng;
					
					$p_has_imei_scan = $this->db->query("select is_serial_required from m_product_info where product_id = ? ",$i['product_id'])->row()->is_serial_required;
					
					$has_imei_scan += $p_has_imei_scan;
					
		?>
		<tr class="bars bar<?=$i['barcode']?> prod_scan">
			<td>
				<div style="width:100px;height: 100px;float: left">
					<a target="_blank" href="<?php echo IMAGES_URL.'/items/big/'.$i['pic'].'.jpg'?>"><img width="100%" src="<?php echo IMAGES_URL.'/items/small/'.$i['pic'].'.jpg'?>" /></a>
				</div>	
			</td>
			<td class="prod"><input type="hidden" class="pid" value="<?=$i['product_id']?>"><?=$i['product_name']?> -  <?=anchor_popup('admin/product/'.$i['product_id'],'view')?>
		<?php //$imeis=$this->db->query("select * from t_imei_no where status=0 and product_id=?",$i['product_id'])->result_array();?>
		<?php $imeis=$this->db->query("select a.imei_no,b.mrp,b.product_barcode,b.location_id,b.rack_bin_id,b.stock_id
										from t_imei_no a 
										left join t_stock_info b on a.stock_id = b.stock_id and status=0 
										where a.product_id=? ",$i['product_id'])->result_array();?>
										
		<?php if($p_has_imei_scan){
			
				// prepare imeino list for allotment 
				foreach($imeis as $im)
				{
					$prod_imei_list[$im['imei_no']] = array($i['product_id'],($im['product_barcode']?$im['product_barcode']:'0_nobc').'_'.((int)$im['mrp']).'_'.$im['location_id'].'_'.$im['rack_bin_id'].'_'.$im['stock_id']);
				}
				
				echo '<ol class="imei_inp_list">';
				for($p=0;$p<$i['qty'];$p++)
				{
		?>
					<li><input type="text" readonly="readonly" order_id="<?=$i['order_id']?>" itemid="<?=$i['itemid']?>" class="imei<?=$i['product_id']?> imei<?=$i['product_id']?>_unscanned imeis imeip<?=$p?>" style="width: 100px;padding:2px;font-size: 9px" value="" ></li> 
		<?php 
				}
				echo '</ol>';
		}?>
			</td>
			<td>
				<?=$i['deal']?>	
			</td>

			<td><?=(double)$i['mrp']?></td>
			<td class="ord_mrp"><?=$i['order_mrp']?></td>
			<td class="qty prod_req_qty"><?=$i['qty']?></td>

			<td style="padding: 0px;">
			<div style="background: #ccc; padding: 0px; margin: 0px; font-size: 85%;">
		 
		<?php 
			$prd_id = $i['product_id'];  
			$mrp_stock_det = array();
			
			foreach($this->db->query("select stock_id,product_id,location_id,rack_bin_id,concat(location_id,'-',rack_bin_id) as rbid,product_barcode,sum(available_qty) as s,mrp from t_stock_info where product_id={$i['product_id']} group by rbid,mrp,product_barcode,stock_id having sum(available_qty)>=0 order by mrp asc")->result_array() as $s){
				
				if(!isset($mrp_stock_det[$s['mrp'].'_'.$s['rbid']]))
					$mrp_stock_det[$s['mrp'].'_'.$s['rbid']] = array('pid'=>$prd_id,'stk'=>0,'det'=>array());
					
				$mrp_stock_det[$s['mrp'].'_'.$s['rbid']]['stk']+=$s['s'];
				
				$mrp_alloted_qty = 0;
				$rb_name = '';
				//$mrp_alloted_res = $this->db->query("select rack_name,bin_name,a.qty from t_reserved_batch_stock a join t_stock_info b on a.stock_info_id = b.stock_id join m_rack_bin_info c on c.id = b.rack_bin_id where a.batch_id = ? and a.order_id = ? and a.product_id = ? and b.stock_id = ? ",array($batch_id,$i['order_id'],$i['product_id'],$s['stock_id']));
				
				$mrp_alloted_res = $this->db->query("select rack_name,bin_name,
																ifnull(a.qty,0) as qty 
															from m_rack_bin_info c
															join t_stock_info b on c.id = b.rack_bin_id 
															left join t_reserved_batch_stock a on a.stock_info_id = b.stock_id 
															and a.batch_id = ? and a.order_id = ? and a.product_id = ?  and a.p_invoice_no = ?  
															where b.stock_id = ? ",array($batch_id,$i['order_id'],$i['product_id'],$p_inv_no,$s['stock_id']));
				
				if($mrp_alloted_res->num_rows())
					{
						$reserv_res = $this->db->query("select qty from t_reserved_batch_stock where order_id = ? and p_invoice_no = ? and stock_info_id = ? ",array($i['order_id'],$p_inv_no,$s['stock_id']));
						if($reserv_res->num_rows())
							{
								$mrp_alloted_qty = $reserv_res->row()->qty;
								$rb_name = $mrp_alloted_res->row()->rack_name.'-'.$mrp_alloted_res->row()->bin_name;
							}else
							{
								if($i['mrp'] == $s['mrp'])
									{
										$mrp_alloted_qty = 0;
										$rb_name = $mrp_alloted_res->row()->rack_name.'-'.$mrp_alloted_res->row()->bin_name;
									}
							}
					
					}else{
						if(!$this->db->query("select count(*) as t from t_reserved_batch_stock where order_id = ? and p_invoice_no = ? ",array($i['order_id'],$p_inv_no))->row()->t)
							{
								$mrp_alloted_qty = $i['qty'];
							}
					}
				
					array_push($mrp_stock_det[$s['mrp'].'_'.$s['rbid']]['det'],array($s['product_barcode'],$s['s'],$s['location_id'],$s['rack_bin_id'],'reserv_qty'=>$mrp_alloted_qty,'rb_name'=>$rb_name,'stock_id'=>$s['stock_id']));
			}
			
			$stk_i=0;
			foreach($mrp_stock_det as $mrp_rb=>$mrp_list){
			list($mrp,$l_rb_id) = explode('_',$mrp_rb);
			$reserv_qty_summ = '';
			$ttl_reserved_qty = 0;
			foreach($mrp_list['det'] as $mrp_b)
				{
					if($mrp_b['reserv_qty'])
						{
							$reserv_qty_summ .= '<div><b>'.$mrp_b['reserv_qty'].'</b><span style="font-size:8px;">('.$mrp_b['rb_name'].')</span></div>';
							$ttl_reserved_qty += $mrp_b['reserv_qty'];
						}
				}
				if(!round($mrp_list['stk']+$ttl_reserved_qty))
				continue;
		?>
			<table class="subgrid" cellpadding="0" cellspacing="0" style="border: 0px !important; width: 100%; background: #f9f9f9; font-size: 13px;">
				<tr>
					<td style="vertical-align: middle;">
					<div style="width: 40px; text-align: center;"><?php echo round($mrp,2);?></div>
					</td>
					
					<td style="vertical-align: middle; text-align: center;display:none">
					<div style="width: 30px;"><b><?=round($mrp_list['stk']+$ttl_reserved_qty)?></b></div>
					</td>
					
					<td style="vertical-align: middle; text-align: center;">
					<div style="width: 60px; color: red; font-size: 12px;">
					<?php echo $reserv_qty_summ;?>
					</div>
					</td>
					<td style="vertical-align: middle; width: 30px !important;" width="10">
					<div class="scan_proditems" style="width: 30px; text-align: center;">
						<?php 
							$show_add_btn = 0;
							$has_reserv_bc_qty = 0; 
							foreach($mrp_list['det'] as $mrp_b)
							{
								if(!($mrp_b[1]+$mrp_b['reserv_qty']))
								continue;
									
								$show_add_btn += (strlen($mrp_b[0])==0)?1:0;
								$scan_by_bc = 0;
								if(strlen($mrp_b[0]))
									{
										$has_reserv_bc_qty += 1;
										$scan_by_bc = 1;
									}			
						?>
							<input rb_id="<?php echo $mrp_b[2].'_'.$mrp_b[3]?>" rb_name="<?php echo $mrp_b['rb_name']?>"
							dealname="<?php echo addslashes($i['deal'])?>" 
							itemid="<?php echo $i['itemid']?>"
							order_id="<?php echo $i['order_id']?>" 
							consider_for_refund="<?php echo $consider_for_refund;?>"
							disc="<?php echo $i['discount']?>"
							ordmrp="<?php echo $i['order_mrp'];?>"
							stk_info_id="<?php echo $mrp_b['stock_id'] ?>"
							mrp="<?php echo $mrp ?>"
							reserv_qty = "<?php echo $mrp_b['reserv_qty'] ?>" 
							stk="<?php echo $mrp_b[1]+$mrp_b['reserv_qty'];?>" type="hidden"
							pid="<?php echo $prd_id;?>"
							name="pbc[<?php echo $i['itemid'].'_'.$prd_id.'_'.($mrp_b[0]?$mrp_b[0]:'BLANK').'_'.$mrp_b['stock_id'].'_'.$i['order_id'];?>]" value="0"
							class="scan_proditem <?php echo $scan_by_bc?'scan_bybc':'' ?> pbcode_<?php echo $mrp_b[0]?$mrp_b[0]:$stk_i.'_nobc' ?> pbcode_<?php echo $mrp_b[0]?$mrp_b[0]:$stk_i.'_nobc' ?>_<?php echo (double)$mrp;?>_<?php echo $mrp_b[2].'_'.$mrp_b[3];?> pbcode_<?php echo $mrp_b[0]?$mrp_b[0]:$stk_i.'_nobc' ?>_<?php echo (double)$mrp;?>_<?php echo $mrp_b[2].'_'.$mrp_b[3];?>_<?php echo $mrp_b['stock_id'];?>_<?php echo $i['itemid'];?>_<?php echo $i['order_id'];?>"
							style="width: 20px !important;" />
						<?php 		
						}
							 
						?>
							<input mrp="<?php echo $mrp ?>" stk_i="<?php echo $stk_i;?>"
							itemid="<?php echo $i['itemid']?>" pid="<?php echo $prd_id;?>"
							title="Scan to update via barcode or click here"
							class="prod_stkselprev <?php echo !$show_add_btn?'disabled':"";?>"
							ttl_stk="<?php echo $mrp_list['stk'];?>"
							onclick="upd_selprodstk(this)" type="button"
						<?php echo !$show_add_btn?'disabled':"";?> value="0"></div>
						</td>
					</tr>
			</table>
		<?php }
		?>
			</div>
			</td>
			
			<td style="vertical-align: middle; color: #000" width="10">
			<div style="width: 60px; text-align: center;"><b class="refund_amt">0</b>
			</div>
			</td>
			
			<td class="have" style="vertical-align: middle;">0</td>
			<td class="status" style="vertical-align: middle;">PENDING</td>

		<?php 
		/*
	if(empty($i['barcode'])){?>
	<td><input type="button" value="+" class="nobarcode"></td>
	<?php }
		*/
		?>
		</tr>
		<?php }?>
	</tbody>
</table>

<script type="text/javascript">
	$('.scan_proditems').each(function(){
		var ttl_stkgrp_items = $('.scan_proditem',this).length;
		var ttl_scanbybc= $('.scan_bybc',this).length;
		if(ttl_scanbybc)
				{
					$('.prod_stkselprev',this).attr('disabled',true).addClass('disabled');
				}else
				{
					$('.prod_stkselprev',this).attr('disabled',false).removeClass('disabled');
				}
	});
</script>

<div style="margin-top: 20px;"><input type="button" value="Check" style="padding: 7px 10px;" onclick='checknprompt()'> 
	<input type="button" value="Process Invoice" style="float: right; padding: 7px 10px;" onclick='process_invoice(1);'> 
</div>

<?php 
	$trans_id = $this->db->query("select transid from proforma_invoices where p_invoice_no = ? ",$invoice[0]['p_invoice_no'])->row()->transid;
?>

	<table cellpadding="5">
		<tr>
			<td valign="top">
			<div>
			<h4 style="margin: 2px 0px;"></h4>
				<table class="datagrid">
					<thead>
					<tr>
						<th>Free Samples with this order</th>
					</tr>
					</thead>
					<tbody>
						<?php $samps=$this->db->query("select f.name,o.id,o.invoice_no from proforma_invoices i join king_freesamples_order o on o.transid=i.transid join king_freesamples f on f.id=o.fsid where i.p_invoice_no=? order by f.name",$i['p_invoice_no'])->result_array();?>
						<?php //$samps=array();$samps[]=array('name'=>'himalaya _shampoo','id'=>2); ?>
						<?php if(empty($samps)){?><tr>
						<td colspan="100%">No free samples ordered</td>
					</tr><?php }?>
						<?php foreach($samps as $s){?>
					<tr>
						<Td class="free-samples-data"><?=$s['name']?></Td>
					</tr>
						<?php }?>
					</tbody>
				</table>
			</div>
			</td>
			
			<td valign="top">

			<h4 style="margin: 2px 0px;">Transaction Notes</h4>
			<div style="padding: 5px; background: #ffffd0; padding: 5px; font-size: 12px;line-height: 20px;">
			<?php $user_msg=$this->db->query("select note from king_transaction_notes where transid=? and note_priority=1 order by id asc limit 1",$trans_id)->row_array();?>
			<?=isset($user_msg['note'])?"<b>".str_replace('update :','<br>update :',$user_msg['note'])."</b>":"<i>no user msg</i>"?>
			</div>
			</td>
		</tr>
	</table>

<div id="scanned_summ" >
	<h3>Scanned Qty</h3>
	<div class="scanned_summ_total"><span id="summ_scanned_ttl_qty">0</span> / <span id="summ_ttl_qty">0</span></div>
	<div class="scanned_summ_stats"><span style="font-size: 13px;">Products </span> : <span class="ttl_num" id="summ_ttl_scanned_prod">0</span></div>	
</div>

<div id="mutiple_mrp_barcodes" title="Choose Stock from Multiple Mrps">

<div id="bc_mrp_list">
	<table class="datagrid" cellpadding="0" cellspacing="0">
		<thead>
		<th><b>Deal</b></th>	
		<th><b>MRP</b></th>
		<th><b>RackBin</b></th>
		<th><b>Alloted Qty</b></th>
		<th>&nbsp;</th>
		</thead>
		<tbody>

		</tbody>
	</table>
</div>
</div>

<div id="freesample_list_dlg" title="Free Samples with this order">
	<table class="datagrid" width="100%">
		<thead>
			<tr>
				<th>Free Samples</th>
				<th align="center" style="text-align: center;"><input type="checkbox" id="fs_check_list_all"></th>
			</tr>
		</thead>
		<tbody>
			<?php //$samps=$this->db->query("select f.name from proforma_invoices i join king_freesamples_order o on o.transid=i.transid join king_freesamples f on f.id=o.fsid where i.p_invoice_no=? order by f.name",$i['p_invoice_no'])->result_array();?>
			<?php 
				if($samps)
					foreach($samps as $s)
					{
			?>
					<tr>
						<td><?=$s['name']?></td>
						<td align="center">
							<?php if($s['invoice_no'])
								{
									echo $s['invoice_no'];
								}else{
							?>
								<input type="checkbox"  name="fs_ids" value="<?php echo $s['id'];?>" class="sel fs_ids">
							<?php }?>
						</td>
					</tr>
			<?php 
					}
			?>
		</tbody>
	</table>
</div>


<script type="text/javascript">
	var is_fs_confimed = 0;
	
	var prod_imeino_list = new Array();
	var prod_imeino_stock_info = new Array();
	<?php
		if($prod_imei_list)
			foreach($prod_imei_list as $p_imeino => $i_prod_det)
			{
	?>
				prod_imeino_list["<?php echo $p_imeino;?>"] = <?php echo $i_prod_det[0];?>;
				prod_imeino_stock_info["<?php echo $p_imeino;?>"] = "<?php echo $i_prod_det[1];?>";
	<?php				
			} 
	?>
	
	$('#freesample_list_dlg').dialog({
	autoOpen:false,
	modal:true,
	open:function(){
		is_fs_confimed = 0;
	},
	buttons:{
		'Cancel':function(){
			is_fs_confimed = 0;
			$(this).dialog('close');
		},
		'Proceed' : function(){
			is_fs_confimed = 1;
			process_invoice(1);
		}
	}
	});

	$("#freesample_list_dlg .sel").change(function(){
	var fs_dlg = $("#freesample_list_dlg");
		if($(".sel",fs_dlg).length == $(".sel:checked",fs_dlg).length)
			$("#fs_check_list_all").attr("checked",true);	
		else
			$("#fs_check_list_all").attr("checked",false);
	});


	$("#fs_check_list_all").change(function(){
	$("#freesample_list_dlg .sel").attr("checked",$(this).attr('checked')?true:false);
	}).attr('checked',false).trigger('change');

	//select all function
	function fs_select_all()
	{
	
		if($(".sel_all").attr("checked"))
			{
				$(".sel").attr("checked",true);
			}
			else
				$(".sel").attr("checked",false);
			}

		var summ_ttl_qty = 0;
			$('.prod_req_qty').each(function(){
			summ_ttl_qty += $(this).text()*1;
			});
			$('#summ_ttl_qty').text(summ_ttl_qty);
		function show_ttl_summary()
			{
				$('#summ_ttl_scanned_prod').text(0);
				var ttl_prods_scanned = 0;
				var ttl_qty_scan = 0; 
	
			$('#summ_ttl_scanned_prod').text((ttl_prods_scanned)+'/'+($('.prod_scan').length));
	
			$('.prod_scan').each(function(){
			var qty_scan = 0;
			$('.prod_stkselprev',this).each(function(){
			qty_scan += $(this).val()*1;
			});
		
		if(qty_scan)
			{
				ttl_prods_scanned++;
				$('#summ_ttl_scanned_prod').text((ttl_prods_scanned)+'/'+($('.prod_scan').length));
			}
		
			ttl_qty_scan+= qty_scan;
		});
	
		$('#summ_scanned_ttl_qty').text(ttl_qty_scan);
	 
			}
			show_ttl_summary();

		var refund_alert = 0;

		function upd_selprodstk(ele)
			{
				var stat = 0;
				var pbcodes = $(ele).parent().find('nopbcode');
				itm_id = $(ele).attr('itemid');
				mrp = $(ele).attr('mrp');
				stk_i = $(ele).attr('stk_i');
				sel_bcstk_ele = $(ele).parent().find('.pbcode_'+stk_i+'_nobc');
				p=sel_bcstk_ele.parents('tr:eq(1)');
		
		$("#scan_barcode").val("");
		if(p.length==0)
			{
				alert("The product is not in invoice");
				return;
			}
		
		needed=parseInt($(".qty",p).html());
		have=parseInt($(".have",p).html());
		if(needed<=have)
			{
				alert("Required qty is already scanned");
				return;
			}

		var ttl_bc_stk = sel_bcstk_ele.attr('stk');
		var cur_sel = sel_bcstk_ele.val()*1;

		if(ttl_bc_stk < cur_sel+1)
			{
				alert("No Stock Available for this product");
				return false;
			}
			
		if((ttl_bc_stk-(cur_sel+1)) == 0)
			{
				sel_bcstk_ele.removeClass('scan_bybc');
			}
		
		var ttl_stkgrp_items = $('.scan_proditem',sel_bcstk_ele.parent()).length;
		var ttl_scanbybc = $('.scan_bybc',sel_bcstk_ele.parent()).length;
			
		if(ttl_scanbybc)
			{
				$('.prod_stkselprev',sel_bcstk_ele.parent()).attr('disabled',true).addClass('disabled');
			}else
			{
				$('.prod_stkselprev',sel_bcstk_ele.parent()).attr('disabled',false).removeClass('disabled');
			}
		
			sel_bcstk_ele.val(cur_sel+1);
			sel_bcstk_ele.addClass("sel_stk");
			
			var sel_bcstk_preview_ele = sel_bcstk_ele.parent().find('.prod_stkselprev');
				sel_bcstk_preview_ele.val(sel_bcstk_preview_ele.val()*1+1);

				
		validate_item(p);		
	}

		function process_invoice(pmp)
		{
		var fs_id_list_arr = [];
		var fs_id_list='';
		if($(".free-samples-data").length)
			{
				if(!is_fs_confimed)
				{
					$('#freesample_list_dlg').dialog('open');
					return false;
				}
	
		$('.fs_ids:checked').each(function(i){
		fs_id_list_arr[i] = $(this).val();
	    });

		if(fs_id_list_arr)
		{
			fs_id_list=fs_id_list_arr.join(',');
		}
	        
		}

	
	

		if(pmp==1 && !confirm("Are you sure want to partially process this proforma invoice?"))
		return;
		if(done_pids.length==0)
		{
			alert("No products were cleared to pack. Invoice can't be empty");
			return;
		}
			f=true;
			$(".imeis").each(function(){
		if($(this).val()==0)
		{
			f=false;
			alert("One of the serial No is not selected. Serial nos are mandatory to select against quantity");
			return false;
		}
		});
		if(f==false) return;
		$(".imeip1").each(function(){
		p=$($(this).parents("tr").get(0));
		if($(".imeis",p).length<1)
		return;
		var imeis=[];
		$(".imeis",p).each(function(){
		if($.inArray($(this).val(),imeis)!=-1)
		{
			f=false;
			alert("Duplicate serial nos! Check serial nos");
			return false;
		}
			imeis.push($(this).val());
		});
		if(f==false)
		return false;
		});
		if(f==false) return;
		imei_payload="";
		var imei_payload_confirm = new Array();
		for(i=0;i<done_pids.length;i++)
		{
			if($(".imei"+done_pids[i]).length!=0)
			{
				$(".imei"+done_pids[i]).each(function(){
					if(imei_payload_confirm[$(this).val()] == undefined)
					{
						imei_payload_confirm[$(this).val()] = 1;
						imei_payload=imei_payload+'<input type="hidden" name="imei'+done_pids[i]+'[]" value="'+$(this).val()+'">';
					}
				});
			}
		}

		var sel_stk_inps = '';
		$('.sel_stk').each(function(){
		sel_stk_inps += '<input type="hidden" name="'+$(this).attr("name")+'"  value="'+$(this).val()+'_'+$(this).attr("mrp")+'_'+$(this).attr("stk_info_id")+'" >'; 
		});

		if(!sel_stk_inps)
		{
			alert("Problem in process");
			return false;
		}	
	
		msg='<form id="packform" method="post"><?php $r=rand(303,34243234);?><input type="hidden" name="fs_ids" value="'+fs_id_list+'"><input type="hidden" name="pids" value="'+done_pids.join(",")+'"<input type="hidden" name="pass" value="<?=md5("$r {$invoice[0]['p_invoice_no']} svs snp33tdy")?>">	<input type="hidden" name="key" value="<?=$r?>"><input type="hidden" name="invoice" value="<?=$invoice[0]['p_invoice_no']?>">'+imei_payload+' '+sel_stk_inps+'	</form>';
		$(".container").append(msg);
	
		if(confirm("Confirm if Free samples for order has been added for packing ? "))
		{
			$("#packform").submit();
		}
	}

		function checkall()
		{
			var f=true;
			$(".bars").each(function(){
			p=$(this);
			prod=$(".prod",p).html();
			needed=parseInt($(".qty",p).html());
			have=parseInt($(".have",p).html());
			if(needed!=have)
				{
					f=false;
					return false;
				}
		});

		if(f==true)
		{
			var proceed = 1;
				if(refund_alert)
					if(!confirm("Did you check refund amount ?"))
						proceed = 0;		
			if(proceed)
				process_invoice(0);
		}
	 
		return f;
		}

		function checknprompt()
		{
			if(checkall()==false)
			alert("'"+prod+"' is insufficient");
		}
		
		function validate_imeino()
		{
			if($("#scan_imeino").val().length==0)
			{
				alert("Enter IMEI no");
				return;
			}
			var s_imei = $.trim($("#scan_imeino").val());
			
				// check if valid imei no 
				if(prod_imeino_list[s_imei] == undefined)
				{
					alert("The product is not in invoice");
				}else
				{
					if(prod_imeino_list[s_imei] == 0)
					{
							alert("Imei no already alloted ");
							return false;
					}
					
					$.post(site_url+'/admin/jx_chkimeiforpack',{'imeino':s_imei},function(resp){
						
						if(resp.status == 'error')
						{
							alert(resp.error);
						}else
						{
							var i_prod_id = prod_imeino_list[s_imei];
								
							// allot imeino to pending list
							var ttl_imeireq = $('.imei'+i_prod_id).length; 
							var ttl_imeiscanned = $('.imei'+i_prod_id+'_scanned').length;
							if(ttl_imeireq <= ttl_imeiscanned)
								{
									alert("Required Qty of Imei is already scanned");
									return false;
								}
								
								prod_imeino_list[s_imei] = 0;
								
									
							var sel_imei_inpele = $('.imei'+i_prod_id+'_unscanned:eq(0)');
								sel_imei_inpele.parent().append('<a class="remove_scanned" prod_id="'+i_prod_id+'" href="javascript:void(0)" onclick="clear_scannedimeino(this)""><b>X</b></a>');
										
								sel_imei_inpele.val(s_imei).removeClass('imei'+i_prod_id+'_unscanned').addClass('imei'+i_prod_id+'_scanned');
							
								$("#scan_imeino").val('').focus();
								
								var  scn_oid = sel_imei_inpele.attr('order_id');
								var  scn_itmid = sel_imei_inpele.attr('itemid');
								
								$("#scan_barcode").val(prod_imeino_stock_info[s_imei]+'_'+scn_itmid+'_'+scn_oid);
								validate_barcode();
						}
					},'json');
						
				}
			
		}	
		
		function clear_scannedimeino(ele)
		{
			var imei_ele = $(ele).parent().find('.imeis');
			var i_prod_id = $(ele).attr('prod_id');
			
				prod_imeino_list[imei_ele.val()] = i_prod_id;
				
				imei_ele.val('');
				imei_ele.removeClass('imei'+i_prod_id+'_scanned').addClass('imei'+i_prod_id+'_unscanned');
				
				$(ele).remove();
		}

		function validate_barcode()
		{
			if($("#scan_barcode").val().length==0)
				{
					alert("Enter barcode");
					return;
				}
			var sbc = $.trim($("#scan_barcode").val());
		
			var sel_bcstk_ele = $(".pbcode_"+sbc);
			
			if(sel_bcstk_ele.length > 1)
				{
					$('#mutiple_mrp_barcodes').data('m_bc',sbc).dialog("open");
					return false;
				}
				p=sel_bcstk_ele.parents('tr:eq(1)');
				$("#scan_barcode").val("");
			if(p.length==0)
				{
					alert("The product is not in invoice");
					return false;
				}
				needed=parseInt($(".qty",p).html());
				have=parseInt($(".have",p).html());
			if(needed<=have)
				{
					alert("Required qty is already scanned");
					return;
				}
			var ttl_bc_stk = sel_bcstk_ele.attr('stk');
			var cur_sel = sel_bcstk_ele.val()*1;
			if(ttl_bc_stk < cur_sel+1)
				{
					alert("No Stock Available for this product");
					return false;
				}
	
			if((ttl_bc_stk-(cur_sel+1)) == 0)
				{
					sel_bcstk_ele.removeClass('scan_bybc');
				}
		
			var ttl_stkgrp_items = $('.scan_proditem',sel_bcstk_ele.parent()).length;
			var ttl_scanbybc = $('.scan_bybc',sel_bcstk_ele.parent()).length;
		
			if(ttl_scanbybc)
				{
					$('.prod_stkselprev',sel_bcstk_ele.parent()).attr('disabled',true).addClass('disabled');
				}else
				{
					$('.prod_stkselprev',sel_bcstk_ele.parent()).attr('disabled',false).removeClass('disabled');
				}	
	
				sel_bcstk_ele.val(cur_sel+1);
				sel_bcstk_ele.addClass("sel_stk");

			var sel_bcstk_preview_ele = sel_bcstk_ele.parent().find('.prod_stkselprev');
				sel_bcstk_preview_ele.val(sel_bcstk_preview_ele.val()*1+1); 

				$('#mutiple_mrp_barcodes').dialog("close");		 
				$(document).scrollTop(p.offset().top);		 
				validate_item(p);
		}

			var done_pids=[];

		function validate_item(p)
		{
			needed=parseInt($(".qty",p).html());
			have=parseInt($(".have",p).html());
			if(needed<=have)
				{
					alert("Required qty is already scanned");
					return;
				}

		$('.refund_amt',p).text('');
	
		var refund_amt = 0;
		$('.sel_stk',p).each(function(){
			
			if($(this).attr('consider_for_refund')*1)
			{
				var ordmrp = $(this).attr('ordmrp')*1;
				var disc = $(this).attr('disc')*1;
				var mrp = $(this).attr('mrp')*1;
				var qty = $(this).val()*1;
				var paidamt = ordmrp-disc;
				var newamt = mrp-(mrp*disc/ordmrp);
				refund_amt += Math.round((paidamt-newamt)*qty*10000)/10000; 
			}
			 
		}); 

		if(refund_amt < 0){
			$('.refund_amt',p).text(refund_amt).css('color','red');
			refund_alert = 1;
		}else
			$('.refund_amt',p).text(refund_amt).css('color','#000');
	
		have=have+1;
		$(".have",p).html(have);
		scanned_highlgt(p);
	
		show_ttl_summary();
	
		if(have==needed)
		{
			p.removeClass("partial");
			$(".status",p).html("OK");
			done_pids.push($(".pid",p).val());
			p.addClass("done");
		}else if(have)
		{
			p.addClass("partial");
		}
		checkall();
		}

		function scanned_highlgt(p)
		{
			var q=p;
			q.addClass("scanned");
			window.setTimeout(function(){
			q.removeClass("scanned");
			},1000);
		}

		$(function(){
		$(".nobarcode").click(function(){
		p=$(this).parents(".bars").get(0);
		validate_item($(p));
		});
		$("#scan_barcode").keyup(function(e){
		if(e.which==13)
			validate_barcode();
		});
		
		$("#scan_imeino").keyup(function(e){
			if(e.which==13)
			validate_imeino();
		});	
		
	});


	$('#mutiple_mrp_barcodes').dialog({
	width:800,
	height:'400',
	autoOpen:false,
	modal:true,
	open:function(){
			var mbc = $(this).data('m_bc');
			 
			var mrp_option_list = '';
				$('.pbcode_'+mbc).each(function(){
					
					if(!$(this).parents('tr.done').length)
					{
						var l_order_id = $(this).attr("order_id");
						var l_itemid = $(this).attr("itemid");
						var l_mrp = parseFloat($(this).attr("mrp")); 
						var l_rbid = $(this).attr("rb_id");
						var l_stk_id = $(this).attr("stk_info_id");
						
						var l_stk = $(this).attr("stk");
						var l_stk_reserv = $(this).attr("reserv_qty")*1;
						var l_stk_sel = $(this).val()*1;
							l_stk_sel = !isNaN(l_stk_sel)?l_stk_sel:0;
						
						if(l_stk*1 > l_stk_sel)
							mrp_option_list += '<tr><td><b>'+$(this).attr("dealname")+'</b></td><td><b>'+$(this).attr("mrp")+'</b></td><td><b>'+$(this).attr("rb_name")+'</b></td><td><b>'+($(this).attr("reserv_qty")*1)+'</b></td><td><input type="radio" value="'+mbc+'_'+l_mrp+'_'+l_rbid+'_'+l_stk_id+'_'+l_itemid+'_'+l_order_id+'" name="sel_bc_mrp" /></td></tr>';	
					}
					
					
						
				});
				
				if(mrp_option_list != '')
 				{
					
					$('tbody',this).html(mrp_option_list);
					if($('tbody tr',this).length == 1)
					{
						$('tbody tr input[type="radio"]:first',this).attr("checked",true);
						$('tbody tr input[name="sel_bc_mrp"]',this).trigger('click');	
					}else
					{
						$('tbody tr input[type="radio"]:checked',this).attr("checked",true);
					}
				}else
				{
					$(this).dialog('close');
				}
			},
			buttons:{
				'Submit':function(){
					if($('input[name="sel_bc_mrp"]:checked').length)
					{
						validate_barcode();	
					}else
					{
						alert("Please choose mrp product"); 
					}
				}
			},
			close: function()
			{
				$('#scan_barcode').val('');
			}
		});

		$('input[name="sel_bc_mrp"]').live('click',function(){
			$('#scan_barcode').val($(this).val());
		});

		$('#scan_barcode').focus();
		
		$(function(){
			$('.leftcont').hide();
		});

		
	 <?php if($has_imei_scan) { ?>
	 	$('#scanbyimei').show();
	 <?php }else{ ?>
	 	$('#scanbyimei').hide();
	 <?php } ?>
    	 
  	
</script>

<style>
.done {
	background: #afa;
}
.imeis{width: 250px;}
.remove_scanned{padding:5px;font-size: 11px;color:#cd0000}
.imei_inp_list{padding-left:30px;}
#mutiple_mrp_barcodes table {width: 100%;}
</style>

</div>
<?php
