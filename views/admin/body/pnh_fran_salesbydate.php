<div class="page_wrap">
	<div class="fl_right">
		Sales From 
		<select name="sales_frm">
			<option value="">Choose</option>
			<?php for($i=0;$i<$max_mnths;$i++){?>
				<option value="<?php echo $max_mnths-$i;?>" <?php echo set_select('sales_frm',$i,($sales_from==($max_mnths-$i)))?>><?php echo date('M Y',mktime(0,0,0,date('m')+$i*-1,1,date('Y')))?></option>
			<?php }?>
		</select>
	</div>
	<h2 class="page_title">Franchise Sales Report</h2>
	<div class="page_topbar">
		<table id="fr_sales_list" class="datagrid" width="100%">
			<thead>
				<tr>
					<th width="20">Slno</th>
					<th width="150" style="text-align: left">Territory</th>
					<th width="150" style="text-align: left">Town</th>
					<th style="text-align: left">Franchise</th>
					
					<?php 
						for($j=$sales_from;$j<=$max_mnths;$j++)
						{
					?>
						<th width="100" align="right" style="text-align: right"><?php echo date('M Y',mktime(0,0,0,date('m')+($max_mnths-$j)*-1,1,date('Y')))?></th>
					<?php 	
						}
					?>
					<th width="100" style="text-align: center">Suspended</th>
				</tr>	
			</thead>
			<tbody>
				<?php 
					foreach($fran_list as $i=>$fran)
					{
				?>
						<tr>
							<td><?php echo $i+1;?></td>
							<td><?php echo $fran['territory_name'];?></td>
							<td><?php echo $fran['town_name'];?></td>
							<td><?php echo $fran['franchise_name'];?></td>
							<?php 
								for($j=$sales_from;$j<=$max_mnths;$j++)
								{
									
									$fsales_on = date('Y-m-d',mktime(0,0,0,date('m')+($max_mnths-$j)*-1,1,date('Y'))); 
									$fsale_val = $this->db->query("select sum(b.invoice_qty*(b.mrp-(b.discount+b.credit_note_amt))) as amt from king_orders a	join king_invoice b on a.id = b.order_id and b.invoice_status = 1 join king_transactions c on c.transid = a.transid	where c.franchise_id = ? and a.status != 3 and c.init > ? group by franchise_id ",array($fran['franchise_id'],strtotime($fsales_on.' 00:00:00')))->row()->amt;
							?>
								<td width="100" align="right">
									<?php
											echo '<a href="javascript:void(0)" fid="'.$fran['franchise_id'].'" mnth="'.$fsales_on.'" class="fr_mnth_prodsales">'.format_price($fsale_val,2).'</a>';
									?>
								</td>
							<?php 	
								}
							?>
							<td align="center">
								<?php 
									if($fran['is_suspended'] == 1)
										echo '<span style="color:#cd0000">Permanent</span>';
									else if($fran['is_suspended'] == 2)
										echo 'Temporary';
									if($fran['is_suspended'] == 0)
										echo 'No';
								?>
							</td>
						</tr>
				<?php 		
					}
				?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="12" align="right">
						<a href="javascript:void(0)" class="dg_print">Print</a>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

<div id="fr_mnth_prodsales_dlg" title="Franchise Product Purchase List">
	
	<div style="line-height: 20px;">
		<div><b>Franchise</b>: <span class="fname"></span></div>
		<div><b>Sales Month</b>: <span class="f_s_mnth"></span></div>
	</div>
	<div style="overflow: auto;height: 450px">
		<table class="datagrid" width="100%">
			<thead>
				<tr>
					<th width="20">Slno</th>
					<th width="80" style="text-align: left">Date</th>
					<th style="text-align: left">Name</th>
					<th width="20" align="center">Qty </th>
					<th width="80" style="text-align: right">MRP</th>
					<th width="80" style="text-align: right">Discount</th>
					<th width="80" style="text-align: right">Landing</th>
					<th width="80" align="center">Subtotal </th>
				</tr>	
			</thead>
			<tbody></tbody>
			<tfoot>
				<tr>
					<td colspan="8" align="right"><a href="javascript:void(0)" class="dg_print">Print</a></td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

<script>
	$('#fr_mnth_prodsales_dlg').dialog({
										width:'800',
										height:'550',
										autoOpen:false,
										modal:true,
										open:function(){
											var fid = $(this).data('fid');
											var mnth = $(this).data('mnth');
												$('#fr_mnth_prodsales_dlg tbody').html("");
												$('#fr_mnth_prodsales_dlg .fname').html("");
												$('#fr_mnth_prodsales_dlg .f_s_mnth').html("");
												$.post(site_url+'/admin/jx_getfranproductsales','fid='+fid+'&mnth='+mnth,function(resp){

													$('#fr_mnth_prodsales_dlg .fname').html(resp.fname);
													$('#fr_mnth_prodsales_dlg .f_s_mnth').html(resp.f_s_mnth);
													
													if(resp.status == 'success')
													{
														var fr_plist = '';
															$.each(resp.fr_plist,function(a,b){
																fr_plist += '<tr>';
																fr_plist += '	<td>'+(a+1)+'</td>';
																fr_plist += '	<td>'+b.purchased_on+'</td>';
																fr_plist += '	<td><a target="_blank" href="'+site_url+'/admin/pnh_deal/'+b.itemid+'">'+b.itemname+'</a></td>';
																fr_plist += '	<td align="center">'+b.pqty+'</td>';
																fr_plist += '	<td align="right">'+b.mrp+'</td>';
																fr_plist += '	<td align="right">'+b.disc+'</td>';
																fr_plist += '	<td align="right">'+b.landing_price+'</td>';
																fr_plist += '	<td align="center">'+b.pqty*b.landing_price+'</td>';
																fr_plist += '<tr>';
															});
															$('#fr_mnth_prodsales_dlg tbody').html(fr_plist);
															$('#fr_mnth_prodsales_dlg .datagrid').tablesorter();
													}else
													{
														$('#fr_mnth_prodsales_dlg tbody').html("<tr><td colspan='7' align='center'>No data found</td></tr>");
													}
												},'json');
										}
									});
$(function(){
	$('select[name="sales_frm"]').change(function(){
		$('.page_wrap').css('opacity','0.5');
		location.href = site_url+'/admin/pnh_fran_salesbydate/'+$(this).val();
	});
	$('#fr_sales_list .datagrid').jq_fix_header_onscroll();

	$('.fr_mnth_prodsales').click(function(){
		var mnth = $(this).attr('mnth');
		var fid = $(this).attr('fid');
			$('#fr_mnth_prodsales_dlg').data({'fid':fid,'mnth':mnth}).dialog('open');
	});
	
});
	
</script>
<style>
	.ui-dialog{position: fixed !important;}
</style>
