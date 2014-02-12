<?php 
	$only_superadmin = $this->erpm->auth(true,true);
	$p=$product;
?>
<style>
.leftcont{display: none}
</style>
<div class="container">
<h2><?=$p['product_name']?> <a style="font-size: 11px;" href="<?=site_url("admin/editproduct/{$p['product_id']}")?>">edit</a></h2>

 
			<table class="datagrid nofooter" width="100%">
				<thead>
				<tr>
					<th>SKU Code</th>
					<th>Brand</th>
					<th>MRP</th>
					<th>VAT</th>
					<th>Stock</th>
					<th>Sourcable?</th>
					<th>Serial Required</th>
					<th>Size</th>
					<th>UOM</th>
				</tr>
				</thead>
				<tbody>
				<tr>
				<td><?=$p['sku_code']?$p['sku_code']:"-na-"?></td>
				<td><a href="<?=site_url("admin/viewbrand/".$p['brand_id'])?>"><?=$p['brand']?></a></td>
				<td><?=$p['mrp']?></td>
				<td><?=$p['vat']?>%</td>
				<td>
					<?=$p['stock']?>
					<table width="100%" cellpadding="3" cellspacing="0" style="background: #FFF;">
							<thead>
								<th><b>Barcode</b></th>
								<th><b>MRP</b></th>
								<th><b>Rackbin</b></th>
								<th><b>Stock</b></th>
							</thead>
						<tbody>
						<?php 
							$sql = "select sum(available_qty) as s,mrp,
											a.location_id,a.rack_bin_id,
											concat(rack_name,bin_name) as rbname,
											ifnull(product_barcode,'') as pbarcode,
											a.stock_id     
										from t_stock_info  a 
										join m_storage_location_info b on b.location_id = a.location_id 
										join m_rack_bin_info c on c.id = a.rack_bin_id  
										where product_id=? 
										group by mrp,pbarcode,a.location_id,a.rack_bin_id 
										having sum(available_qty)>=0 
										order by mrp asc ";
						?>
						
						<?php foreach($this->db->query($sql,$p['product_id'])->result_array() as $s){?>
							<tr>
								<td>
									<?php echo $s['pbarcode']?$s['pbarcode']:'--na--';?> 
									<?php if($this->erpm->auth(UPDATE_PRODUCT_BARCODE,true)){?>
									<a style="font-size: 9px;float: right" href="javascript:void(0)" class="upd_stk_prodbc" stk_id="<?php echo $s['stock_id'] ?>" >Edit</a>
									<?php }?>	
								</td>
								<td width="40" align="left"><span><?php echo round((float)$s['mrp'],2);?></span></td>
								<td align="left"><?php echo $s['rbname'];?></td>
								<td align="center"><?php echo round($s['s']);?></td>
							</tr>
						<?php }?>
						</tbody>
						</table>
				</td>
				<td><?=$p['is_sourceable']?"Sourcable":"Not Sourcable"?></td>
				<td><?=$p['is_serial_required']?"Yes":"No"?></td>
				<td><?=$p['size']?></td>
				<td><?=$p['uom']?></td>
				</tr>
				</tbody>
			</table>

<div id="prod_fea_tab" style="clear:both">
	<ul>
		<li><a href="#stock_log">Stock Log</a></li>
		<?php
			if($p['is_serial_required'])
			{
				echo '<li><a href="#prod_serial_nos">Product Serial nos</a></li>';
			}
		?>
		<li><a href="#price_change_log">Price ChangeLog</a></li>
		<li><a href="#linked_deals">Linked Deals</a></li>
		<li><a href="#grp_linked_deals">Group Linked Deals</a></li>
		<li><a href="#sourceable_changelog">Sourceable Change Log</a></li>
	</ul>
<div id="stock_log">

<?php 
	if($this->erpm->auth(STOCK_CORRECTION,true))
	{
?>
<div style="float:right;background:#efefef;padding:5px;border:1px solid #fcfcfc;margin-top:30px;font-size: 12px;width: 400px;">
	<h3 align="center">Stock Correction</h3>
<form id="stk_correction_frm" method="post" action="<?=site_url("admin/stock_correction")?>">
<input type="hidden" name="pid" value="<?=$p['product_id']?>">
<table cellpadding="2" cellspacing="0" width="100%">
<tr>
	<td>Type :</td>
	<td style="vertical-align: middle;">
		<input type="radio" checked="checked" value="1" name="type" />IN 
		<input type="radio" name="type" value="0" />OUT  
	</td>	
</tr>
<tr>
	<td>Stock Product:</td>
	<td>
		<select name="mrp_prod" style="max-width: 300px">
			<option value="">Choose</option>
			<option id="new_stock_prod" value="new">New</option>
		<?php 
			$sql_stkmrpprod = "select  a.stock_id,a.location_id,a.rack_bin_id,concat(rack_name,bin_name) as rb_name,a.product_barcode,a.mrp,sum(a.available_qty) as available_qty,concat('Rs',ifnull(a.mrp,0),' - ',rack_name,bin_name,' - ',a.product_barcode) as stk_prod 
								from t_stock_info a
								join m_rack_bin_info b on a.rack_bin_id = b.id 
								where a.product_id = ? 
								group by a.mrp,a.location_id,a.rack_bin_id,a.product_barcode  
								order by a.mrp asc 
							";
			$stkmrpprod_res = $this->db->query($sql_stkmrpprod,$p['product_id']);
			if($stkmrpprod_res->num_rows())
			{
				foreach($stkmrpprod_res->result_array() as $stkmrppro)
				{
		?>
				<option stk_id="<?php echo $stkmrppro['stock_id'];?>" avail_qty="<?php echo $stkmrppro['available_qty'];?>" value="<?php echo $stkmrppro['product_barcode'].'_'.$stkmrppro['mrp'].'_'.$stkmrppro['location_id'].'_'.$stkmrppro['rack_bin_id'] ?>"><?php echo $stkmrppro['stk_prod']?></option>
		<?php
				} 				
			}else
			{
		?>
				<option avail_qty="0" value="<?php echo $p['barcode'].'_'.$p['mrp'].'_1_10' ?>"><?php echo $p['barcode'].'_'.$p['mrp']?></option>
		<?php 		
			}
		?>
		</select>
		
		<div id="new_mrp_bc_block" style="display: none;">
			<div>
				MRP: &nbsp;&nbsp;&nbsp; <input type="text" name="n_mrp" size="6" value="" /> <br />  
				Barcode: <input type="text" name="n_barcode"  size="16" value="" />
			</div>
		</div>	
	</td>
</tr>
 
<tr>
	<td>Location :</td>
	<td>
		<select name="loc">
			<option value="">Choose</option>
		<?php 
			$sql_stklocs = "select  location_id,rack_bin_id,concat(rack_name,bin_name) as rb_name 
									from m_rack_bin_brand_link a 
									join m_rack_bin_info b on a.rack_bin_id = b.id 
									where brandid = ? ";
			$stk_locs_res = $this->db->query($sql_stklocs,$p['brand_id']);
			if($stk_locs_res->num_rows())
			{
				foreach($stk_locs_res->result_array() as $stk_loc)
				{
		?>
				<option value="<?php echo $stk_loc['location_id'].'_'.$stk_loc['rack_bin_id'] ?>"><?php echo $stk_loc['rb_name']?></option>
		<?php
				} 				
			}
		?>
		</select>
	</td>
</tr>
<tr>
	<td>Quantity:</td>
	<td><input type="text" name="corr" size=2>
		<span id="sc_preview_avail_qty" style="color: green;font-size: 10px;font-weight: bold;">0 Available</span>
</td></tr>


<tr class="stk_transfer_blk" id="stk_transfer_cnfrm" >
	<td>Stock Transfer:</td>
	<td>
		<input type="checkbox" name="stk_transfer" value="1" checked="checked" >
	</td>
</tr>
<tr class="stk_transfer_blk">
	<td>Transfer To:</td>
	<td>
		<select name="dest_prodid" data-placeholder="Choose Transfer To" style="width: 300px;">
			<option value="">Choose</option>
			<?php
			
				$prod_item_det_res = $this->db->query("select * from (
													(select itemid,c.brandid,c.catid,c.menuid 
														from m_product_deal_link a 
														join king_dealitems b on a.itemid = b.id
														join king_deals c on c.dealid = b.dealid
														join m_product_info d on d.product_id = a.product_id 
														where a.product_id = ? )
													union (
													select itemid,c.brandid,c.catid,c.menuid 
														from m_product_group_deal_link a 
														join products_group_pids e on e.group_id=a.group_id 
														join king_dealitems b on a.itemid = b.id
														join king_deals c on c.dealid = b.dealid
														join m_product_info f on f.product_id = e.product_id 
														where e.product_id = ? ) ) as g 
													group by itemid 
												",array($p['product_id'],$p['product_id'])); 
												
				if($prod_item_det_res->num_rows())
				{
					$prod_item_det = $prod_item_det_res->row_array();
					$similar_prods_res = $this->db->query('select * from (
																(select d.product_id,d.product_name 
																	from m_product_deal_link a 
																	join king_dealitems b on a.itemid = b.id
																	join king_deals c on c.dealid = b.dealid
																	join m_product_info d on d.product_id = a.product_id 
																	where d.brand_id = ? and c.catid = ? )
																union (
																select f.product_id,f.product_name
																	from m_product_group_deal_link a 
																	join products_group_pids e on e.group_id=a.group_id 
																	join king_dealitems b on a.itemid = b.id
																	join king_deals c on c.dealid = b.dealid
																	join m_product_info f on f.product_id = e.product_id 
																	where f.brand_id = ? and c.catid = ? ) ) as g 
																group by product_id 
																order by product_name ',array($prod_item_det['brandid'],$prod_item_det['catid'],$prod_item_det['brandid'],$prod_item_det['catid']));
				}else
				{
					$similar_prods_res = $this->db->query('select * from m_product_info where brand_id = ? and product_id != ? ',array($p['brand_id'],$p['product_id']));
				}
					if($similar_prods_res->num_rows())
					{
						foreach($similar_prods_res->result_array() as $similar_prod)
						{
							if($similar_prod['product_id'] == $p['product_id'])
								continue;
							echo '<option value="'.($similar_prod['product_id']).'">'.($similar_prod['product_id'].' - '.$similar_prod['product_name']).'</option>';
						} 
					}
										
			
				
			?>
		</select>
		<div id="dest_prod_stockdet_blk">
			<select name="dest_prod_stockdet" style="width: 300px">
				<option value="">Choose Stock</option>
			</select>
			<div id="new_dest_stockdet" style="display: none">
				<table>
					<tr><td><b>Barcode</b></td><td><input type="text" size="20" name="dest_prod_newstk_bc"></td></tr>
					<tr><td><b>MRP</b></td><td><input type="text" size="15" name="dest_prod_newstk_mrp" value="0"></td></tr>
					<tr><td><b>Location</b></td><td><select name="dest_prod_newstk_rbid"></select></td></tr>
				</table>
			</div>
			<div style="padding:5px 0px">
				Available Qty : <b id="dest_prod_stock_ttl">0</b>
			</div>
			<?php 
				if($p['is_serial_required'])
				{
			?>
			<div style="background: #FFF;padding:0px;width: 95%">
				<b style="padding: 5px 10px;display: block;background: #E3E3E3;width: 94%;">Scan/Enter Serial nos :</b>
				<ol id="stk_transfer_slnos" style="padding-left: 25px;padding-bottom: 10px;"></ol>
			</div>
			<?php } ?>
		</div>
	</td>
</tr>
<tr><td>Message :</td><td><textarea name="msg" style="width: 95%;min-height: 60px;"></textarea></td></tr>
<tr><td colspan="2" align="right"><input type="submit" value="Update" style="padding:3px 6px;"></td></tr>
</table>
</form>
</div>
<?php } ?>

<script type="text/javascript">
	function reset_producttransfer()
	{
		$('select[name="dest_prod_stockdet"]').html('');
	}
	
	$('select[name="dest_prod_stockdet"]').change(function(){
		$('#dest_prod_stock_ttl').text($('option:selected',this).attr('available_qty'));
	});
	$('select[name="dest_prodid"]').change(function(){
		$('select[name="dest_prod_stockdet"]').html("Loading...");
		var dest_pid = $(this).val();
		var s_stk_id = $('select[name="mrp_prod"] option:selected').attr('stk_id');
			$.post(site_url+'/admin/jx_getdestproductstkdet/'+s_stk_id+'/'+dest_pid,{},function(resp){
				var dest_stklist = '<option available_qty="0" value="">Choose</option>';
					$.each(resp.stk_list,function(a,b){
						dest_stklist += '<option available_qty="'+(b.available_qty)+'" value="'+b.product_barcode+'_'+b.mrp+'_'+b.location_id+'_'+b.rack_bin_id+'">'+b.stk_prod+'</option>';
					});
					dest_stklist += '<option available_qty="0" value="new">New</option>';
					$('select[name="dest_prod_stockdet"]').html(dest_stklist);
					
				var dest_newstklochtml = '<option value="">Choose</option>';
					$.each(resp.location,function(a,b){
						dest_newstklochtml += '<option  value="'+b.rack_bin_id+'">'+b.rb_name+'</option>';
					});
					$('select[name="dest_prod_newstk_rbid"]').html(dest_newstklochtml);
					
					$('input[name="dest_prod_newstk_bc"]').val("");
					$('input[name="dest_prod_newstk_mrp"]').val(resp.mrp);
					
			},'json');
			$('select[name="dest_prod_stockdet"]').trigger('change');
	});
	
	$('select[name="dest_prod_stockdet"]').change(function(){
		if($(this).val() == "new")
		{
			$('#new_dest_stockdet').show();
		}else
		{
			$('#new_dest_stockdet').hide();
		}
	});
	
	
	
</script>
<?php 
	if($only_superadmin  || 1){
?>
	<div id="upd_stk_prodbc_dlg" title="Update Product Stock Barcode">
		<form id="upd_stk_prodbc_frm" method="post">
			<table>
				<tr><td><b>Old Barcode</b></td><td><input id="upd_old_bc" class="inp" type="text" disabled="disabled"></td></tr>
				<tr><td><b>New Barcode</b></td><td><input id="upd_new_bc" class="inp" autocomplete="off" type="text" value=""><input type="submit" value="Submit" style="visibility: hidden;" ></td></tr>
			</table>
		</form>	
	</div>
	
	
	<script type="text/javascript">
		$('#upd_stk_prodbc_dlg').dialog({
										autoOpen:false,
										width:400,
										height:200,
										modal:true,
										open:function(){
											stk_id = $(this).data('stk_id');
											$.getJSON(site_url+'/admin/jx_get_stkprobyid/'+stk_id,'',function(resp){
												if(resp.status == 'error')
												{
													alert(resp.error);
												}else
												{
													$('#upd_old_bc').val(resp.stkdet.product_barcode);
													$('#upd_new_bc').val('');
												}
											});
										},
										buttons:{
											'Cancel':function(){
												$('#upd_stk_prodbc_dlg').dialog('close');
											},
											'Update':function(){
												$(".ui-dialog-buttonpane button:contains('Update')").button().button("disable");
												var newbc = $('#upd_new_bc').val();
												
													$.post(site_url+'/admin/jx_upd_stkprodbc','stk_id='+stk_id+'&newbc='+newbc,function(resp){
														if(resp.status == 'error')
														{
															$(".ui-dialog-buttonpane button:contains('Update')").button().button("enable");
															alert(resp.error);
														}else
														{
															alert("Barcode updated successfully");
															location.href = location.href;
														}
													},'json');
												 
											}
										}
								});

		$('.upd_stk_prodbc').click(function(){
			$('#upd_stk_prodbc_dlg').data('stk_id',$(this).attr("stk_id")).dialog('open');
		});

		$('#upd_stk_prodbc_frm').submit(function(){
			$(".ui-dialog-buttonpane button:contains('Update')").button().trigger('click');
			return false;
		});
		
	</script>
	
<?php }?>

<script type="text/javascript">
	$('select[name="mrp_prod"]').change(function(){

		$('#new_mrp_bc_block input').val('');
		$('#new_mrp_bc_block').hide();
		
		if($(this).val())
		{
			if($(this).val() == 'new')
			{
				$('input[name="corr"]').attr('aqty',0);
				$('#sc_preview_avail_qty').text("");

				$('#new_mrp_bc_block input').val('');
				$('#new_mrp_bc_block').show();
				
			}else
			{
				avail_qty = $('option:selected',this).attr('avail_qty');
				$('#sc_preview_avail_qty').text(avail_qty+" Available");
				$('input[name="corr"]').attr('aqty',avail_qty);
			}	
			
		}else
		{
			$('input[name="corr"]').attr('aqty',0);
			$('#sc_preview_avail_qty').text("");
			
		}
		
	}).trigger("change");	

	$('input[name="corr"]').keyup(function(){
		
		var inp_corr = $(this).val()*1;
		
		$('.stk_transfer_blk').hide();
		
		if($('input[name="type"]:checked').val() == 0)
		{
			if($(this).val()>$(this).attr('aqty')*1)
			{
				alert("You have only "+$(this).attr('aqty')+" Qty Available");
				$(this).val(0);
			}else
			{
				$('#stk_transfer_cnfrm').show();
				$('#stk_transfer_cnfrm input[name="stk_transfer"]').attr('checked',false).trigger('change');
				var imei_inp_list = '';
					for(var k=0;k<inp_corr;k++)
					{
						imei_inp_list += '<li><input type="text" name="s_imeino[]" value=""  style="width:85%;"><span class="st_lookup_imei imei_stat_chk"></span></li>';
					}
				$('#stk_transfer_slnos').html(imei_inp_list);
			}
		}
	});
	
	
	$('#stk_transfer_slnos input[name="s_imeino[]"]').live('keyup keypress blur',function(e){
		var code = e.keyCode || e.which; 
			if (code == 13) {               
				e.preventDefault();
				
				var s_pid = $('#stk_correction_frm input[name="pid"]').val();	
				var s_imeino = $(this).val();
					
				if($('.s_imei_'+s_imeino).length)
				{
					alert("IMEI is already scanned in list");
					$(this).select();
				}else
				{
					var imei_loader_ele = $(this).parent().find('span.st_lookup_imei');
						imei_loader_ele.addClass('imei_stat_chk');
						imei_loader_ele.html('Loading');
					
					
						
						$.post(site_url+'/admin/jx_chkimeifortransfer/'+s_pid+'/'+s_imeino,{},function(resp){
							if(resp.status == 'error')
							{
								imei_loader_ele.html(resp.error);
								imei_loader_ele.addClass("imei_stat_error");
								imei_loader_ele.parents('li:first').removeClass('s_imei_'+s_imeino);
							}else
							{
								imei_loader_ele.html('');
								imei_loader_ele.removeClass("imei_stat_chk");
								imei_loader_ele.removeClass("imei_stat_error");
								imei_loader_ele.parents('li:first').addClass('s_imei_'+s_imeino);
								imei_loader_ele.parents('li:first').next().find('input').focus();
								
							}
						},'json');
				}
					
				return false;
			}
	});

	$('input[name="type"]').change(function(){
		$('select[name="loc"]').val('');
		if($(this).val() == 1)
		{
			$('#new_stock_prod').show();
			if($('select[name="mrp_prod"]').val() == 'new')
			{
				$('#new_mrp_bc_block input').val('');
				$('#new_mrp_bc_block').show();
			}
			$('select[name="loc"]').attr('disabled',false);
			$('.stk_transfer_blk').hide();
		}else
		{
			$('select[name="mrp_prod"]').val('');
			$('#new_stock_prod').hide();	
			$('#new_mrp_bc_block input').val('');
			$('#new_mrp_bc_block').hide();
			$('select[name="loc"]').attr('disabled',true);
			
			$('#stk_transfer_cnfrm').show();
			$('#stk_transfer_cnfrm input[name="stk_transfer"]').attr('checked',false).trigger('change');
			
		}
		
	});
	
	
	$('#stk_transfer_cnfrm input[name="stk_transfer"]').change(function(){
		if($(this).attr('checked'))
		{
			$('#dest_prod_stockdet_blk select').html('');
			$('.stk_transfer_blk').show();	
		}else
		{
			$('.stk_transfer_blk').hide();
			$('#stk_transfer_cnfrm').show();
		}
	});

	$('input[name="type"]:checked').trigger('change');
	
	
	

	$('#stk_correction_frm').submit(function(){
		var error_msg = new Array();
		if(!$('select[name="mrp_prod"]',this).val())
		{
			error_msg.push("-Please Choose Stock product");
		}
		if(!($('input[name="corr"]',this).val()*1))
		{
			error_msg.push("-Please Enter Qty");
		}
		if($('input[name="type"]:checked',this).val() == "1")
		{
			if(!$('select[name="loc"]',this).val())
			{
				error_msg.push("-Please Choose Location");
			}
		}else
		{
			if($('input[name="stk_transfer"]').attr('checked'))
			{
				if($('select[name="dest_prodid"]').val() == "")
					error_msg.push("-Please Choose Destination Product To Transfer ");
				if($('select[name="dest_prod_stockdet"]').val() == "")
					error_msg.push("-Please Choose Destination Product Stock Details ");
					
				if($('.imei_stat_chk').length != 0 || $('.imei_stat_error').length !=0 )
				{
					error_msg.push("-Please Check entered Serialno Details ");
				}	
				
				if($('select[name="dest_prod_stockdet"]').val() == "new")
				{
					if($('select[name="dest_prod_newstk_bc"]').val() == "")
						error_msg.push("-Please Enter Destination Product Barcode");
						
					if($('select[name="dest_prod_newstk_mrp"]').val() == "")
						error_msg.push("-Please Enter Destination Product Stock MRP");
						
					if($('select[name="dest_prod_newstk_rbid"]').val() == "")
						error_msg.push("-Please Choose Destination Product Stock Rackbin");	
				}
					
			}
		}
		if(error_msg.length)
		{
			alert("Unable to submit form \n"+error_msg.join("\n"));
			return false;
		}
		 
	});

	
	
</script>
<h4>Total : <span id="stock_log_ttl">0</span></h4>
	
		<table id="stock_log_list" class="datagrid" width="60%">
			<thead>
			<tr><th>Slno</th><th>In / Out</th><th>Stock Intake/Invoice</th><th>Qty Affected</th><th>Stock After</th><th>Created By</th><th width="120">On</th><th></th></tr>
			</thead>
			
			<tbody>
			
			</tbody>
		</table>
		<div id="stock_log_pagination" style="display: none"></div>
	</div>


	<?php
		if($p['is_serial_required'])
		{
	?>
		<div id="prod_serial_nos">
			<h4>Total : <span id="stock_imei_ttl">0</span></h4>
			<table id="stock_imei_list" class="datagrid" width="60%">
				<thead><tr><th>#</th><th>Serialno</th><th>GRNID</th><th>Status</th><th>Date</th></tr></thead>
				<tbody>
				
				</tbody>
			</table>
			<div id="stock_imei_pagination" style="display: none"></div>
		</div>
	<?php		
		}
	?>

	<div id="price_change_log">
	
		
		<table class="datagrid ">
			<thead>
				<tr><th>Sno</th><th>Old MRP</th><th>New MRP</th><th>Reference</th><th>Date</th></tr>
			</thead>
			
			<tbody>
				<?php $i=1; foreach($this->db->query("select * from product_price_changelog where product_id=? order by id desc",$p['product_id'])->result_array() as $pc){?>
					<tr>
						<td><?=$i++?></td>
						<td>Rs <?=$pc['old_mrp']?></td>
						<td>Rs <?=$pc['new_mrp']?></td>
						<td>
						<?php if($pc['reference_grn']==0) echo "MANUAL";else{?>
						<a href="<?=site_url("admin/viewgrn/{$pc['reference_grn']}")?>"><?=$pc['reference_grn']?></a>
						<?php }?>
						</td>
						<td><?=date("g:ia d/m/y",$pc['created_on'])?></td>
					</tr>
				<?php }?>
			</tbody>
		</table>
	</div>

	<div id="linked_deals">
		
		<table class="datagrid ">
			<thead>
				<tr><th>Sno</th><th>Deal</th><th>Qty</th><th>Type</th></tr>
			</thead>
			
			<tbody>
				<?php $i=1; foreach($this->db->query("select i.is_pnh,i.id,i.name,l.qty from m_product_deal_link l join king_dealitems i on i.id=l.itemid where l.product_id=?",$p['product_id'])->result_array() as $d){?>
					<tr>
						<td><?=$i++?></td>
						<td><a href="<?=site_url("admin/deal/{$d['id']}")?>"><?=$d['name']?></a></td>
						<td><?=$d['qty']?></td>
						<td><?=$d['is_pnh']?"PNH":"SNP"?></td>
					</tr>
				<?php }?>
			</tbody>
		</table>
	</div>

	<div id="grp_linked_deals">
		
			<table class="datagrid ">
				<thead>
					<tr><th>Sno</th><tH>Group</tH><th>Deal</th><th>Qty</th><th>Type</th></tr>
				</thead>
				
				<tbody>
					<?php $i=1; foreach($this->db->query("select i.is_pnh,i.id,i.name,l.qty,g.group_id from products_group_pids g join m_product_group_deal_link l on l.group_id=g.group_id join king_dealitems i on i.id=l.itemid where g.product_id=? group by i.id",$p['product_id'])->result_array() as $d){?>
						<tr>
						<td><?=$i++?></td>
						<td><a href="<?=site_url("admin/product_group/{$d['group_id']}")?>"><?=$this->db->query("select group_name from products_group where group_id=?",$d['group_id'])->row()->group_name?></a></td>
						<td><a href="<?=site_url("admin/deal/{$d['id']}")?>"><?=$d['name']?></a></td>
						<td><?=$d['qty']?></td>
						<td><?=$d['is_pnh']?"PNH":"SNP"?></td>
						</tr>
					<?php }?>
				</tbody>
		</table>
	</div>

	<div id="sourceable_changelog">
		
		<table class="datagrid">
			<thead>
				<tr><th>Changed to</th><th>By</th><th>On</th></tr>
			</thead>
			
			<tbody>
				<?php foreach($this->db->query("select s.is_sourceable,s.created_on,u.name as `by` from products_src_changelog s join king_admin u on u.id=s.created_by where s.product_id=? order by s.id desc",$p['product_id'])->result_array() as $c){?>
				<tr><td><?=$c['is_sourceable']=="1"?"SOURCEABLE":"NOT SOURCEABLE"?></td><td><?=$c['by']?></td><td><?=date("g:ia d/m/y",$c['created_on'])?></td></tr>
				<?php }?>
			</tbody>
		</table>
	</div>
</div>
</div>

<script type="text/javascript">
	$('#prod_fea_tab').tabs();
	
	
	
	function load_product_stocklog(product_id,pg)
	{
		$('#stock_log_list tbody').html('<tr><td colspan="6"><div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div></td></tr>');
		$.post(site_url+'/admin/jx_stocklog/'+product_id+'/'+pg+'/25','',function(resp){
			$('#stock_log_list tbody').html(resp.log_data);
			if(resp.ttl*1 > resp.limit*1)
				$('#stock_log_pagination').html(resp.pagi_links).show();
			else
				$('#stock_log_pagination').html("").hide();
			
			$('#stock_log_ttl').html(resp.ttl);	
				
		},'json');
	}
	
	function load_imeino(product_id,pg)
	{
		$('#stock_imei_list tbody').html('<tr><td colspan="6"><div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div></td></tr>');
		$.post(site_url+'/admin/jx_stockimeilist/'+product_id+'/'+pg+'/25','',function(resp){
			$('#stock_imei_list tbody').html(resp.imei_data);
			if(resp.imei_ttl*1 > resp.limit*1)
				$('#stock_imei_pagination').html(resp.imei_pagi_links).show();
			else
				$('#stock_imei_pagination').html("").hide();
			
			$('#stock_imei_ttl').html(resp.imei_ttl);	
				
		},'json');
	}
	
	$('#stock_imei_pagination .log_pagination a').live('click',function(e){
		e.preventDefault();
		var url_prts = $(this).attr('href').split('/');
			pg = url_prts[url_prts.length-1];
			pg = pg*1;
			
		load_imeino(<?php echo $p['product_id'];?>,pg);
	});
	
	$('#stock_log_pagination .log_pagination a').live('click',function(e){
		e.preventDefault();
		var url_prts = $(this).attr('href').split('/');
			pg = url_prts[url_prts.length-1];
			pg = pg*1;
			
		load_product_stocklog(<?php echo $p['product_id'];?>,pg);
	});
	
	
	$( document ).ready(function(){
		load_product_stocklog(<?php echo $p['product_id'];?>,0);
		<?php if($p['is_serial_required']){?>
			load_imeino(<?php echo $p['product_id'];?>,0);
		<?php } ?>
	});
	
</script>
<style>
	#prod_fea_tab h4{margin:4px 0px;}
	.imei_stat_error{font-size: 10px;color: #cd0000;display: block;}
</style>
<?php
