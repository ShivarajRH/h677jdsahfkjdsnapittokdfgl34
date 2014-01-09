<div class="page_wrap container" style="width: 98%;">
	<a  id="add_delhub" href="<?php echo site_url('admin/pnh_franchise_activate_imei');?>" class="button button-rounded button-small fl_right button-flat-primary"> Activate IMEI Now</a>
	<h2 class="page_title">IMEI Activation Log <span style="color:#555;font-size: 18px;"><?php echo ($st_d?(' for '.format_date($st_d).' To '.format_date($en_d)):''); ?></span></h2>
	
	<div class="page_content clearboth" style="clear: both">
		 <div id="shipped_imeimobslno">
			<?php 	
			
				 
					
				$ttl_purchased = 0;	
				$ttl_inactiv_msch = 0;
				$ttl_activated_msch = 0;
				$ttl_purchased = $ttl_activated_msch+$ttl_inactiv_msch;
					
				//$ttl_purchased=$this->db->query("SELECT COUNT(DISTINCT i.id) AS ttl FROM king_orders o JOIN king_transactions t ON t.transid=o.transid JOIN t_imei_no i ON i.order_id=o.id WHERE o.imei_scheme_id > 0 and t.franchise_id=?",$f['franchise_id'])->row()->ttl;
				//$ttl_activated_msch=$this->db->QUERY("SELECT COUNT(DISTINCT i.id) AS active_msch FROM king_orders o JOIN king_transactions t ON t.transid=o.transid JOIN t_imei_no i ON i.order_id=o.id WHERE is_imei_activated=1 AND o.imei_scheme_id > 0 AND t.franchise_id=? ",$f['franchise_id'])->ROW()->active_msch;
				//$ttl_inactiv_msch=$this->db->QUERY("SELECT COUNT(DISTINCT i.id) AS inactive_msch FROM king_orders o JOIN king_transactions t ON t.transid=o.transid JOIN t_imei_no i ON i.order_id=o.id WHERE is_imei_activated=0 AND o.imei_scheme_id > 0 AND t.franchise_id=?",$f['franchise_id'])->ROW()->inactive_msch;
				
				$ttl_imei_activation_credit=0;  
			?>
		<div class="module_cont">
			<!--  <h3 class="module_cont_title">IMEI List</h3>-->
			<div class="module_cont_block">
				<div class="module_cont_block_grid_total fl_left" style="padding:5px;">
						<span class="stat total">Total Purchased : <b id="ttl_purchased"><?php echo  $ttl_purchased ;?></b></span> 
						<span class="stat total">Active : <b id="ttl_actv" ><?php echo $ttl_activated_msch?></b></span> 
						<span class="stat total">Inactive : <b id="ttl_inactv"><?php echo $ttl_inactiv_msch?></b></span> 
				</div>
				<div class="module_cont_block_grid_total fl_right" style="padding:5px;">
						<span class="stat total  fl_right">Total Credit On activation : <b id="ttl_purchased_credit"><?php echo 'Rs '.format_price($ttl_imei_activation_credit)?></b></span> 
				</div>
				
				<div class="module_cont_block_filters clearboth" style="background: #f5f5f5;margin:0px;height: 27px;padding:3px;">
					
					<span class="filter_bykwd fl_right" >
						Search : <input type="text" name="imei_srch_kwd" style="font-size: 12px;padding:3px 7px;width: 200px;" value="" placeholder="" >
						<input type="button" onclick = "load_shipped_imei(0)" value="Search" />
					</span>
					
					<span style="margin-right:10px;padding:5px;font-size:12px;" >
						<b>Filter IMEI By</b>&nbsp;Activated Date : 
						<input type="text" name="active_ondate" style="font-size: 12px;padding:3px 7px;width: 80px;" value="" placeholder="" >
						to 
						<input type="text" name="active_ondate_end" style="font-size: 12px;padding:3px 7px;width: 80px;" value="" placeholder="" >
						&nbsp;&nbsp;
						Activated Status :
						<select name="imei_status" id="imei_status">
						<option value="0">All</option>
						<option value="1">In Active</option>
						<option value="2">Active</option>
						</select>
						<select name="fid">
							<option value="">Choose</option>
						<?php
							$fran_list_res = $this->db->query("select f.franchise_id,f.franchise_name
																			from t_imei_no i
																			join king_orders b on i.order_id = b.id
																			join king_dealitems c on c.id = b.itemid
																			join m_product_deal_link d on d.itemid = c.id
																			join king_transactions e on e.transid = b.transid
																			join pnh_m_franchise_info f on f.franchise_id = e.franchise_id 
																			where b.status in (1,2) and b.imei_scheme_id > 0  
																			group by f.franchise_id
																			order by franchise_name 
																	");
							if($fran_list_res->num_rows())
							{
								foreach($fran_list_res->result_array() as $fran_det)
								{
						?>
								<option value="<?php echo $fran_det['franchise_id'] ?>"><?php echo $fran_det['franchise_name'] ?></option>
						<?php			
								}		
							}																		
						?>
						</select>
					</span>
				</div>
				<div class="module_cont_block_grid" style="clear:both">
					<table class="datagrid" width="100%">
						<thead>
							<th>Sno</th>
							<th>Franchise</th>
							<th>Product Name</th>
							<th>Invoice Slno</th>
							<th>IMEI Slno</th>
							<th>Selling Price</th>
							<th>Orderd On</th>
							<th>Is Activated</th>
							<th>Applied Credit</th>
							<th>Credit Value(Rs)</th>
							<th>Activated on</th>
						</thead>
						<tbody></tbody>
					</table>	
				</div>
				<div class="module_cont_grid_block_pagi">
					
				</div>	
			</div>
		</div>
	</div>
	</div>
</div>
 <style>
		.module_cont{padding;2px;}
		.module_cont .module_cont_title{margin:5px 0px;}
		.module_cont .module_cont_block{clear:both;}
		.module_cont .module_cont_block .module_cont_block_grid_total{margin:3px 0px;}
		.module_cont .module_cont_grid_block_pagi{padding:3px;text-align: right;}
		.module_cont .module_cont_grid_block_pagi a{padding:5px 10px;color:#454545;background: #f1f1f1;display: inline-block}
	
	.leftcont{display: none}
	.fl_left{float: left;}
	.fl_right{float: right;}
	.clearboth{clear:both}
	
	.page_wrap{width: 99%;}
	.page_wrap .page_title{margin:10px 0px}
	.page_wrap .page_topbar{clear: both;overflow: hidden !important;margin-bottom: 3px;clear: both}
	
	.page_wrap .page_topbar .page_topbar_left{width: 49%;}
	.page_wrap .page_topbar .page_topbar_right{width: 49%;}
	.page_wrap .page_content{clear:both}
	
	.page_wrap .page_topbar .total_overview{padding:0px 0px;font-size: 15px;}
	
	.ordered_list{margin:0px;padding-left:10px;}
	ol.total_overview {margin:0px;}
	ol.total_overview li{display: inline-block;margin:0px;background: #ffffe0;padding:4px 8px;overflow: hidden;text-align: left;min-width: 60px;}
	ol.total_overview li b{margin-left: 5px;float: right;background: #FFF;padding:1px 5px;font-size: 12px;}
	ol.total_overview li:hover{background: #ffffa0;cursor:pointer;}
</style>
<script>
	prepare_daterange('d_st','d_en');
	
	function fil_bydate()
	{
		var d_st = $('#d_st').val()?$('#d_st').val():0;
		var d_en = $('#d_en').val()?$('#d_en').val():0;
		
			if(d_st == 0 || d_en == 0 )
			{
				alert("Please select valid date range");
				return false;
			}
		
		location.href = site_url+'/admin/pnh_imei_activation_log/'+d_st+'/'+d_en+'/0';
	}
	
	$('input[name="active_ondate"],input[name="active_ondate_end"]').datepicker();

		$('input[name="active_ondate"],input[name="active_ondate_end"],select[name="imei_status"]').change(function(){
			$('input[name="imei_srch_kwd"]').val('');
				load_shipped_imei(0);
		});
	
	$('input[name="active_ondate"],input[name="active_ondate_end"],select[name="imei_status"],select[name="fid"]').change(function(){
			$('input[name="imei_srch_kwd"]').val('');
				load_shipped_imei(0);
	});
	
	function load_allshipped_imei(pg)
	{
		$('select[name="imei_status"]').val('');
		$('input[name="imei_srch_kwd"]').val('');
		$('input[name="active_ondate"]').val('');
		$('input[name="active_ondate_end"]').val('');
		load_shipped_imei(pg);
	}
	
	function load_shipped_imei(pg)
	{
		$('#shipped_imeimobslno .module_cont_block_grid .datagrid tbody').html('<tr><td colspan="8"><div align="center"><img src="'+base_url+'/images/loading_bar.gif'+'"> </div></td></tr>');
		var imei_params = {};
		
			imei_params.fid = $('select[name="fid"]').val();
			imei_params.imei_status = $('select[name="imei_status"]').val();
			imei_params.active_ondate = $('input[name="active_ondate"]').val();
			imei_params.active_ondate_end = $('input[name="active_ondate_end"]').val();
			imei_params.imei_srch_kwd = $('input[name="imei_srch_kwd"]').val();
			
			if(!(imei_params.active_ondate && imei_params.active_ondate_end))
			{
				imei_params.active_ondate = '';
				imei_params.active_ondate_end = '';	
			}
			if(!imei_params.imei_status)
				imei_params.imei_status = '';
		
			$.post(site_url+'/admin/jx_load_all_shipped_mobimei/'+pg,imei_params,function(resp){
				
				if(resp.status == 'error')
				{
					alert(resp.error);
				}else
				{
					$('#shipped_imeimobslno .module_cont_block_grid_total .total b').text(resp.total_rows);
					if(resp.ship_imei_det.length == 0)
					{
						$('#shipped_imeimobslno .module_cont_block_grid .datagrid tbody').html('<tr><td colspan="12"><div align="center">No Data found</div></td></tr>');			
					}else
					{
						
						$('#ttl_actv').text(resp.total_actv_imei);
						$('#ttl_inactv').text(resp.total_inactv_imei);
						$('#ttl_purchased').text(resp.total_actv_imei*1+resp.total_inactv_imei*1);
						$('#ttl_purchased_credit').text('Rs '+(resp.total_actv_amt));
						
						var shipped_imeilist_html = '';
							$.each(resp.ship_imei_det,function(a,b){
								if(b.is_imei_activated==0)
									b.is_imei_activated='No';
								else
									b.is_imei_activated='Yes';
								if(b.imei_activated_on === null)
									b.imei_activated_on='--na--';
								shipped_imeilist_html += '<tr>'
														+'<td>'+(pg+a+1)+'</td>'
														+'<td><a target="_blank" href="'+(site_url+'/admin/pnh_franchise/'+b.franchise_id)+'">'+b.franchise_name+'</a></td>'
														+'<td>'+b.product_name+'</td>'
														+'<td><a target="_blank" href="'+site_url+'/admin/invoice/'+b.invoice_no+'"><b>'+b.invoice_no+'</b></a></td>'
														+'<td>'+b.imei_no+'</td>'
														+'<td>'+b.paid+'</td>'
														+'<td>'+b.orderd_on+'</td>'
														+'<td>'+b.is_imei_activated+'</td>'
														+'<td>'+b.credit_value+''+resp.imei_cre_type[b.scheme_type]+'</td>'
														+'<td>'+b.imei_activation_credit+'</td>'
														+'<td>'+b.imei_activated_on+'</td>'
													+'</tr>';
							});
						$('#shipped_imeimobslno .module_cont_block_grid .datagrid tbody').html(shipped_imeilist_html);
						
						$('#shipped_imeimobslno .module_cont_grid_block_pagi').html(resp.shipped_imeilist_pagi);
						
						$('#shipped_imeimobslno .module_cont_grid_block_pagi a').unbind('click').click(function(e){
								e.preventDefault();
								
							var link_part = $(this).attr('href').split('/');
							var link_pg = link_part[link_part.length-1]*1;
								if(isNaN(link_pg))
									link_pg = 0;
									
								load_shipped_imei(link_pg);	
						});
						
					}
				}
			},'json');
				
	}
	load_shipped_imei(0);
		
	$('.log_pagination a').live('click',function(e){
		e.preventDefault();
		$.post($(this).attr('href'),'',function(resp){
			$('#'+resp.type+' div.tab_content').html(resp.log_data+resp.pagi_links);
			$('#'+resp.type+' div.tab_content .datagridsort').tablesorter();
		},'json');
	});
		
	
</script>