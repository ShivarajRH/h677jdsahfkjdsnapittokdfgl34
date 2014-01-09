
<link class="include" rel="stylesheet" type="text/css" href="<?php echo base_url();?>/js/jq_plot/jquery.jqplot.min.css" />
<script class="include" type="text/javascript" src="<?php echo base_url();?>/js/jq_plot/jquery.jqplot.min.js"></script>
<script class="include" type="text/javascript" src="<?php echo base_url();?>/js/jq_plot/plugins/jqplot.pieRenderer.min.js"></script>
<script class="include" type="text/javascript" src="<?php echo base_url();?>/js/jq_plot/plugins/jqplot.categoryAxisRenderer.min.js"></script>

<div class="container">
	<h3 class="page_title">PNH Sales report</h3>
	<div class="form_block" style="padding:10px;">
		
			<?php 
				if($_POST && $this->erpm->auth(EXPORT_PNH_SALES_REPORT,true))
				{
			?>
				<div style="float: right">
					<form id="exp_sales_summ" method="post" target="hnl_export_sales_summ" action="<?php echo site_url('admin/pnh_export_sales_summary')?>">
						<input type="hidden" name="e_st_d" value="<?php echo $st_d;?>" />
						<input type="hidden" name="e_en_d" value="<?php echo $en_d;?>" />
						<select name="type">
							<option value="1">By Territory</option>
							<option value="2">By Town</option>
							<option value="3">By Franchise</option>
						</select>
						<input type="submit" value="Export CSV" >
					</form>
					<iframe id="hnl_export_sales_summ" name="hnl_export_sales_summ" style="width: 1px;border:1px;height: 1px;"></iframe>
				</div>
				 
				
			<?php 
				} 
			?>
			<form method="post" action=""> 
				<table >
					<tbody>
					<tr>
						<td>
							<b>From</b>  
							<input type="text" id="from_date" class="input" size="7"  name="st_d" value="<?php echo $st_d?>" />
						</td>
					 	
						<td>
							<b>To</b>  
							<input type="text" id="to_date" class="input" size="7" name="en_d" value="<?php echo $en_d?>" />
						</td>
						<td>
							<input type="submit" value="Generate" />
						</td>
					</tr>
					</tbody>
				</table>
			</form>
			
			
	</div>
	 
	 
	 
	
		<div id="pnh_sales_analytics" style="width: 100%;float: left;min-width: 300px;">
				
					<div style="background: #f7f7f7f;border:1px solid #ccc">
						<h3 style="padding:10px;margin:0px;background: #000;color: #FFF;padding-right:0px;">
							<span>
								Sales Summary from <?php echo format_date($st_d).' to '.format_date($en_d)?> 
							</span>
							<span id="ttl_orders" style="float: right;">&nbsp;</span>
						</h3>
						<?php 
							list($ty,$tm,$td) = explode('-',$st_d);
							$st_d_ts = mktime(0,0,0,$tm,$td,$ty);
							
							list($ty,$tm,$td) = explode('-',$en_d);
							$en_d_ts = mktime(23,59,59,$tm,$td,$ty);
							
							
						?>
						<table width="100%">
						<tbody>
						<?php 
								$ttl_members = 0;
								$ttl_orders = 0;
								$ttl_order_value = 0;
								$terrritory_list = $this->db->query("select id,territory_name from pnh_m_territory_info  order by territory_name ")->result_array();
								foreach($terrritory_list as $t_det)
								{
									$terri_id = $t_det['id'];
									$terri_sales = 0;
									$terri_order_value = 0;
									$terri_members = 0;
						?>
									<tr>
										<td style="border-bottom: 1px dotted #555">	
											<h3  class="territory_link" style="margin:0px;padding:5px 0px;overflow: hidden;border-left: 3px solid purple"> 
												<span style="padding:23px;display: inline-block;"><?php echo $t_det['territory_name'];?></span>
												<a href="javascript:void(0)" terr_id="<?php echo $terri_id;?>" class="icon icon_plus fl_right">&nbsp;</a>
												<div id="terri_sales_<?php echo $terri_id;?>" class="boxy" style="margin-right: 10px;float: right;">
													0 Sales 
												</div>
											</h3> 
											<div style="background: #FFF;padding:10px;display: none;margin: 10px;">
												<table width="100%" cellpadding="0" cellspacing="0">
													<tr>
														<td width="60%" valign="top">
															<?php 
																
																
																
																$town_list = $this->db->query("select id,town_name from pnh_towns where territory_id = ?  order by town_name ",$t_det['id'])->result_array();
																foreach($town_list as $town_det)
																{
																	$town_id = $town_det['id'];
																	$town_ttl_orders=0;
																	$town_ttl_order_value = 0;
																	$town_ttl_members = 0;
																	$town_ttl_pen_payment = 0;
															?>
																
																	<table class="datagrid" width="100%" class="datagrid" >
																		<thead>
																			<th>
																				<span style="float: right">
																					<a href="javascript:void(0)" onclick="load_pnh_saleschart('town','<?php echo $t_det['id']?>','<?php echo $town_id?>',0,0)">View</a>
																				</span>
																				<b>
																					Town : <?php echo $town_det['town_name'];?>
																				</b>
																				
																			</th>
																		</thead>
																		<tbody>
																			<tr>
																			<td style="padding:0px">
																				<div style="margin:0px;">
																			<?php 
																				$franchise_order_res = $this->db->query("select b.franchise_id,o.time as created_on,b.franchise_name,town_id,territory_id,
																																	count(distinct a.transid) as total_orders,
																																	sum((o.i_orgprice-o.i_discount-o.i_coup_discount)*o.quantity) as total_order_value 
																															from pnh_m_franchise_info b 
																															left join king_transactions a on a.franchise_id = b.franchise_id and is_pnh = 1 and a.init between ? and ?      
																															left join king_orders o on o.transid = a.transid and o.transid is not null   
																															where b.town_id = ? and b.territory_id = ?  
																															group by b.franchise_id
																														",array($st_d_ts,$en_d_ts,$town_id,$terri_id));
																				
																			 
																				
																				if($franchise_order_res->num_rows())
																				{
																			?>
																				 
																				<table class="subgrid" width="100%">
																					<thead>
																						<th width="30" >FID</th>
																						<th >Francise Name</th>
																						<th width="100" align="right">Pending Payment</th>
																						<th width="100" align="right">Members</th>
																						<th width="40" align="right">Orders</th>
																						<th width="40" align="right">Value</th>
																						<th width="100">&nbsp;</th>
																					</thead>
																			<?php 		
																					
																					foreach($franchise_order_res->result_array() as $fr_det)
																					{
																						
																						$ttl_reg_members = $this->db->query("select count(*) as total from pnh_member_info where created_on between ? and ? and franchise_id = ? ",array($st_d_ts,$en_d_ts,$fr_det['franchise_id']))->row()->total;
																						$fran_acc_statement = $this->erpm->get_franchise_account_stat_byid($fr_det['franchise_id']);
																						$fran_pen_payment = ($fran_acc_statement['shipped_tilldate']-$fran_acc_statement['paid_tilldate']+$fran_acc_statement['acc_adjustments_val']-$fran_acc_statement['credit_note_amt']);
																						$town_ttl_pen_payment += $fran_pen_payment?$fran_pen_payment:0;
																						 
																			?>
																						<tr>
																							<td><?php echo $fr_det['franchise_id'] ?></td>
																							<td><?php echo anchor_popup('admin/pnh_franchise/'.$fr_det['franchise_id'],$fr_det['franchise_name']) ?></td>
																							<td align="right"><?php echo formatInIndianStyle($fran_pen_payment,2); ?></td>
																							<td align="right"><?php echo formatInIndianStyle($ttl_reg_members); ?></td>
																							<td align="right"><?php echo formatInIndianStyle($fr_det['total_orders']) ?></td>
																							<td align="right"><?php echo formatInIndianStyle($fr_det['total_order_value']) ?></td>
																							<td align="center">
																								<a href="javascript:void(0)" onclick="load_pnh_saleschart('town','<?php echo $t_det['id']?>','<?php echo $town_id?>','<?php echo $fr_det['franchise_id'];?>',0)">Analytics</a>
																								 | 
																								<a href="javascript:void(0)" onclick="load_pnh_saleschart('town','<?php echo $t_det['id']?>','<?php echo $town_id?>','<?php echo $fr_det['franchise_id'];?>',1)">Orders</a>
																								
																							</td>	
																						</tr>
																			<?php 		
																						$town_ttl_orders += $fr_det['total_orders'];
																						$town_ttl_order_value += $fr_det['total_order_value'];	
																						$town_ttl_members +=  $ttl_reg_members;
																						$town_ttl_pending_payment +=  $town_ttl_pen_payment;
																					}
																			?>
																					<tr>
																						<td align="right" colspan="3">Total</td>
																						<td align="right"><?php echo $town_ttl_members; ?></td>
																						<td align="right"><?php echo $town_ttl_orders ?></td>
																						<td align="right"><?php echo round($town_ttl_order_value) ?></td>
																					</tr>
																				</table>
																			<?php 			
																				}
																			?>
																				</div>
																			</td>
																			</tr>
																		</tbody>
																	</table>	
																		<br />
															<?php 		
																	$terri_sales += $town_ttl_orders;
																	$ttl_orders += $town_ttl_orders;
																	$terri_order_value +=$town_ttl_order_value;
																	$ttl_order_value +=$town_ttl_order_value;
																	$terri_members +=  $town_ttl_members;
																	$ttl_members += $town_ttl_members;
																}
															?>
												
														</td>
														<td width="40%" valign="top" >
															<div id="chart_preview_<?php echo $t_det['id'];?>" style="width: 100%;min-height: 700px;background: #f8f8f8;border:1px solid #dfdfdf;padding:7px;">
																<h3 id="chart_title_<?php echo $t_det['id'];?>">Territory Summary</h3>
																<div id="chartview_<?php echo $t_det['id'];?>" style="height: 300px;"></div>
																<div class="sales_summary" style="width: 100%;background: #eee;">
																	
																</div>
															</div>
														</td>
													</tr>
												</table> 
												
												<script type="text/javascript">
													$('#terri_sales_<?php echo $t_det['id']?>').html('<?php echo '<span >  Members <br /> <b>'.(formatInIndianStyle($terri_members)).'</b> </span> <span >  Orders <br /> <b>'.(formatInIndianStyle($terri_sales)).'</b> </span> <span> Rs <br /> <b>'.(formatInIndianStyle(round($terri_order_value))).'</b></span>';?>');
												</script>	
													 
											</div>
										</td>
									</tr>
						<?php 		
									 
								}
								
						?>
						</tbody>
						</table>
					</div>
					
		</div>
		 
		
		
</div>
	<script type="text/javascript">
		$('#ttl_orders').html('<?php echo 'New Members : '.(formatInIndianStyle($ttl_members)).'  Total '.(formatInIndianStyle($ttl_orders)).' Orders  Order Value : Rs '.(formatInIndianStyle($ttl_order_value));?>');
	</script>	

<div id="order_summary_dlg" title="Order Summary">
	<div style="padding:5px 0px;">
		<span class="fl_right">Total : <b class="f_ttl_orders"></b></span>
		<span>Franchise : <b class="f_name"></b></span>
	</div>
	<div style="overflow: auto;height:370px">
		<table class="datagrid" width="100%">
			<thead>
				<th>Slno</th>
				<th>OrderedOn</th>
				<th>TransID</th>
				<th>OrderID</th>
				<th>Action</th>
				<th>Status</th>
				<th>Item</th>
				<th>Price</th>
				<th>Quantity</th>
				<th>Subtotal</th>
			</thead>
			<tbody>
				
			</tbody>
		</table>
	</div>
</div>
	 
<style>
.tbl_style_1{border-collapse:collapse;}
.tbl_style_1 thead th{padding:5px;font-weight: bold;font-size:13px;background: #232323;color: #FFF}
.tbl_style_1 tbody tr td{padding:5px;font-size:11px;color:#232323}
.tbl_style_1 tbody tr.even td{background: #F9F9F9;}
.tbl_style_1 tbody tr.odd td.odd{background: #fcfcfc;}

#ttl_orders{padding:10px;background: #42c5ef;color: #FFF;margin-top: -10px;}

#pnh_sales_analytics a{color: #333;text-decoration: none;font-size: 11px;}
#pnh_sales_analytics a:hover{color: #FFF}
.subgrid{font-size: 11px;border-collapse: collapse; }
.subgrid th{background: #555;padding:3px;}
.subgrid td{background: #FFF;padding:3px;border:0px;border-bottom:1px dotted #555}	

.territory_link{text-transform: capitalize;}
.jqplot-table-legend{right: 0px !important;right: 0px !important;display: none;}

.icon{padding:8px;background: #FFF url(<?php echo base_url().'/images/erp_icon_set.png';?>) no-repeat ;}
.icon_plus{padding: 8px;background-position: -525px -114px !important;}
.icon_minus{padding: 8px;background-position: -557px -114px !important;}
.fl_right{float: right;}
.boxy span{padding:5px;background: #ffffa0;margin:8px;min-width: 140px;display: inline-block;font-size: 11px !important;text-align: center;padding:5px;border:1px dotted yellow;}
.boxy span b{font-size: 18px !important;font-weight: bold;}
.input{padding:2px 5px;}
</style>	 

<script>
prepare_daterange('from_date','to_date');
$(function(){
	$('.dg_print').parent().remove();	
});

$('#order_summary_dlg').dialog({width:940,height:450,modal:true,autoOpen:false});


function load_pnh_saleschart(type,terr_id,town_id,fid,orders){

	if(fid)
		$('#chart_title_'+terr_id).html('Franchise Sales Summary');
	else if(town_id)
			$('#chart_title_'+terr_id).html('Town Sales Summary');
		else if(terr_id)
				$('#chart_title_'+terr_id).html('Territory Sales Summary');
	
		
	$('#chartview_'+terr_id).html("Loading...");
	$('#chartview_'+terr_id).parent().find('.sales_summary').html('');

	st_d = $('#from_date').val();
	en_d = $('#to_date').val();

	$.post(site_url+'/admin/pnh_getsalesanalytics',{'terr_id':terr_id,'town_id':town_id,'fid':fid,'st_d':st_d,'en_d':en_d,'orders':orders},function(resp){
		$('#chartview_'+resp.terr_id).html("");
		var total_sales = resp.total_sales;
		
 
		var data = [];
			var status = 0;
			$.each(resp.analytics,function(a,b){
				if(b.sales){
					data.push([b.name,(b.amt/resp.total_order_value)*100]);
					status = 1;
				}
			});
		 
		  	if(status)
		  	{
		           var plot1 = jQuery.jqplot ('chartview_'+resp.terr_id, [data], 
		             {
		               background:'#FFFFFF', 
		               seriesDefaults: {
		                 // Make this a pie chart.
		                 renderer: jQuery.jqplot.PieRenderer, 
		                 rendererOptions: {
		                   // Put data labels on the pie slices.
		                   // By default, labels show the percentage of the slice.
		                   showDataLabels: true
		                 }
		               }, 
		               legend: { show:true, location: 'e' }
		             });
		
		
		           var menu_tbl_html = '<table class="tbl_style_1" width="100%" cellpadding=4 cellspacing=0>'; 
						menu_tbl_html += '<thead><th width="10">&nbsp;</th><th>Category</th><th width="100" align="right">Value</th></thead>';
						menu_tbl_html += '<tbody>';
				 
				var row_i = 0;
				var total_orders = 0;
				var total_order_value = 0;
					$.each(resp.analytics,function(a,b){
						if(b.sales){
							total_orders += parseFloat(b.sales);
							total_order_value += parseFloat(b.amt);
							swatch_html = $('#chartview_'+resp.terr_id+' tr.jqplot-table-legend:eq('+row_i+') td:eq(0) div:first').html();
							perc = (b.amt/resp.total_order_value)*100;
							prec_f = Math.round((b.amt/resp.total_order_value)*100);
			
							menu_tbl_html += '<tr class="'+((row_i%2==0)?'even':'odd')+'"><td>'+swatch_html+'</td><td>'+b.name+'</td><td width="100" align="right">'+b.amt+' (<b>'+((perc<1)?' <1':prec_f)+'%</b>)</td></tr>';
							row_i ++;
						}
					});
		
					
					
					menu_tbl_html += '</tbody>';
					menu_tbl_html += '</table>';
		
					$('#chartview_'+terr_id).parent().find('.sales_summary').html(menu_tbl_html);
					
					var order_summ_tab = $('#order_summary_dlg table');
					$('tbody',order_summ_tab).html('<tr><td colspan="'+($('th',order_summ_tab).length)+'" align="center">Loading...</td></tr>');
					
					var order_summ_list = '';
					var franchise_name = '';
					var total_order_summ_value = 0;
					if(resp.fran_order_list.length)
					{
						$.each(resp.fran_order_list,function(a,b){
							
							var status_txt = 'Processed';
								if(b.status == 0)
									status_txt = 'Pending';
								 
							var batch_stat = '';	  
								if(b.status == 0)
								{
									if(b.batch_enabled == 1)
										batch_stat = '<a href="javascript:void(0)" onclick=update_orderbatch_status(this,"'+b.transid+'",0) class="legend_success" >Suspend</a>';
									else
										batch_stat = '<a href="javascript:void(0)" onclick=update_orderbatch_status(this,"'+b.transid+'",1) class="legend_warning" >Unsuspend</a>';
								}else
								{
									batch_stat = 'Order Processed';
								}
							franchise_name = b.franchise_name;
							order_summ_list += '<tr>';
							order_summ_list += '	<td width="30">'+(a+1)+'</td>';
							order_summ_list += '	<td width="80">'+b.ordered_on+'</td>';
							order_summ_list += '	<td width="60"><a target="_blank" href="'+(site_url+'/admin/trans/'+b.transid)+'">'+b.transid+'</a></td>';
							order_summ_list += '	<td width="100">'+b.order_id+'</td>';
							order_summ_list += '	<td width="80">'+batch_stat+'</td>';
							order_summ_list += '	<td width="80">'+status_txt+'</td>';
							order_summ_list += '	<td width="200">'+b.dealname+'</td>';
							order_summ_list += '	<td width="40">'+(b.landing_price)+'</td>';
							order_summ_list += '	<td width="30">'+b.quantity+'</td>';
							order_summ_list += '	<td width="80">'+((b.landing_price)*b.quantity)+'</td>';
							order_summ_list += '</tr>';
							
							total_order_summ_value+= ((b.mrp-b.disc)*b.quantity);
							
						});
					}
					
					$('tbody',order_summ_tab).html(order_summ_list);
					$('#order_summary_dlg .f_name').html(franchise_name);
					$('#order_summary_dlg .f_ttl_orders').html('Rs '+total_order_summ_value);
					
					
					
					$('#order_summary_dlg').dialog('open');
					
		  	}else
		  	{
		  		$('#chartview_'+terr_id).html('<h3 align="center">No Sales found</h3>');
			 }

           
           
	},'json');

	 
	           
}

$('.territory_link a').click(function(){
	if($(this).hasClass('showing_grid')){
		$(this).removeClass('showing_grid');
		$(this).parent().next().hide();
		$(this).addClass('icon_plus');
		$(this).removeClass('icon_minus');
		
	}else{
		$(this).addClass('showing_grid');
		$(this).parent().next().show();
		$(this).removeClass('icon_plus');
		$(this).addClass('icon_minus');
		load_pnh_saleschart('terr',$(this).attr('terr_id'),0,0);
	}
	 
});

function update_orderbatch_status(ele,transid,stat)
{
	if(confirm("Are your sure want to "+(stat?'Unsuspend':'Suspend')+" this Transaction from processing ?"))
	{
		$(ele).data('prev',$(ele).text());
		$(ele).html("loading");
		$.post(site_url+'/admin/jx_updateorderbatchstatus',{transid:transid,stat:stat},function(resp){
			if(resp.status == 'error')
			{
				$(ele).text($(ele).data('prev'));
				alert(resp.error);
			}else
			{
				if(resp.is_batch_enabled*1)
					$(ele).parent().append("<a href='javascript:void(0)' onclick=update_orderbatch_status(this,'"+transid+"',0) class='legend_success' >Suspend</a>");
				else
					$(ele).parent().append("<a href='javascript:void(0)' onclick=update_orderbatch_status(this,'"+transid+"',1) class='legend_warning' >Unsuspend</a>");
					
					$(ele).remove();
			}
		},'json');
	}
}

</script>
<style>
	#order_summary_dlg table th{text-align: left !important;}
	.legend_success{background: #00B5E5;color:#FFF !important;font-size: 10px;padding:3px 5px;border-radius:3px;}
	.legend_warning{background: #C43C35;color:#FFF !important;font-size: 10px;padding:3px 5px;border-radius:3px;}
</style>