<link rel='stylesheet' type='text/css' href="<?php echo base_url().'css/sk_franchise.css'?>">
<link rel='stylesheet' type='text/css' href="<?php echo base_url().'css/fullcalendar.css'?>">
<script type='text/javascript' src="<?php echo base_url().'js/fullcalendar.min.js'?>"></script>
<script type='text/javascript' src="<?php echo base_url().'js/jquery-ui-timepicker-addon.js'?>"></script>
<script>
$(function(){
    $( ".tsk_stdate" ).datepicker({
      changeMonth: true,
     dateFormat: "dd/mm/yy",
    	minDate: new Date(),
      numberOfMonths:1,
      onClose: function( selectedDate ) {
        $( ".tsk_endate" ).datepicker( "option", "minDate", selectedDate );
    
      }
    });
    $( ".tsk_endate" ).datepicker({
     	 changeMonth: true,
   	 	dateFormat: "dd/mm/yy",
    	minDate: new Date(),
      	numberOfMonths: 1,
      	onClose: function( selectedDate ) {
        $( ".tsk_stdate" ).datepicker( "option", "maxDate", selectedDate );
      
      }
    });
});
</script>

<?php 
	$f=$fran; 
	$menus=array();
	$menu_list=$this->db->query("select id,name from pnh_menu")->result_array();
	foreach($menu_list as $menu_li)
	{
		$menus[$menu_li['id']]=$menu_li['name'];
	}
?>

<div class="container page_wrap" style="">
<?php
	$fid=$this->uri->segment(3);
	$acc_statement = $this->erpm->get_franchise_account_stat_byid($f['franchise_id']);	
	$net_payable_amt = $acc_statement['net_payable_amt'];
	$credit_note_amt = $acc_statement['credit_note_amt'];
	$shipped_tilldate = $acc_statement['shipped_tilldate'];
	$paid_tilldate = $acc_statement['paid_tilldate'];
	$uncleared_payment = $acc_statement['uncleared_payment'];		
	$cancelled_tilldate = $acc_statement['cancelled_tilldate'];
	$ordered_tilldate = $acc_statement['ordered_tilldate'];
	$not_shipped_amount = $acc_statement['not_shipped_amount'];
	$acc_adjustments_val = $acc_statement['acc_adjustments_val'];
	$pending_payment = $acc_statement['pending_payment'];
	
	$fr_reg_diff = ceil((time()-$f['created_on'])/(24*60*60));
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
	if($fran_status != 0)
	{
		$fr_status_color='red';
	}
	else
	{				
		$fr_status_color='green';
	}
?>

<div class="page_topbar_left">
	<div style="width:50%;float:left; margin-bottom: 9px;">
		
		<?php 
			$fran_status_arr=array();
			$fran_status_arr[0]="Live";
			$fran_status_arr[1]="Permanent Suspension";
			$fran_status_arr[2]="Payment Suspension";
			$fran_status_arr[3]="Temporary Suspension";
		?>
		
		Status:
		<b class="level_wrapper" style="background-color:<?php echo $fr_status_color?>;">
			<?php echo $fran_status_arr[$fran_status];?>
		</b>|
			
		<b class="level_wrapper" style="margin-left:2px;background-color:<?php echo $fr_reg_level_color;?>;">
				 <?php echo $fr_reg_level;?>
		</b>
		
		<?php if($is_prepaid){?>
			|<span class="paid_wrapper" ><?php echo "<b>Prepaid</b>"?></span>
		<?php }?>
		<?php if($have_prepaid_menu) {?>
			<span class="fran_suspendlink paid_unmark_wrapper" onclick="mark_prepaid_franchise(<? echo $f['franchise_id'].','.$is_prepaid?>)">
					<?php echo $f['is_prepaid']?'[Unmark]':'[Mark Prepaid]' ?>
			</span>
		<?php }?>
		<h2 class="franch_header_wrap"><?php echo $f['franchise_name']?><a style="margin-left: 10px; font-size: 12px;"href="<?php echo site_url('admin/pnh_edit_fran'.'/'.$f['franchise_id'])?>">(edit)</a></h2>
	</div>
	
	<div style="float:right;width:40%;margin-bottom: 6px;">
		<ul class="actions_wrap" style="">
			<li>
				<a class="fran_suspendlink" target="_blank" style="float: none" href="<?=site_url("admin/pnh_quotes/{$f['franchise_id']}")?>">
					Franchise Requests
				</a> 
			</li>
			
			<?php if($f['is_suspended']==0){?> 
				<li>
					<a class="fran_suspendlink" href="javascript:void(0)" onclick="reson_forsuspenfran(<?=$f['franchise_id']?>)">Suspend Account</a>
				</li>
			<?php }else{?>
				<li>
					<a  class="fran_suspendlink" href="javascript:void(0)" onclick="reson_forunsuspension(<?=$f['franchise_id']?>)" >Unsuspend Account</a>
				</li>
			<?php }?>
			
			<li><a class="fran_suspendlink" target="_blank" style="float: none" href="<?=site_url("admin/pnh_sms_log/{$f['franchise_id']}")?>">SMS Log</a></li>
		</ul>
	</div>
	
	
	<div style="margin-top: 10px;float:left">
		
	</div>	

<?php /*/?>	
<?php if($this->erpm->auth(FINANCE_ROLE,true) || $this->erpm->auth(CALLCENTER_ROLE,true)){ ?>
	<div class="dash_bar_right" style="background: tomato">
		Pending Payment : <span>Rs <?=format_price($shipped_tilldate-($paid_tilldate+$acc_adjustments_val+$credit_note_amt),2)?></span>
	</div>
<?php } ?>
	
	<div class="dash_bar_right">
		UnCleared Payments : <span>Rs <?=format_price($uncleared_payment,2)?></span>
	</div>

<?php /*?>	
<?php if($this->erpm->auth(FINANCE_ROLE,true) || $this->erpm->auth(CALLCENTER_ROLE,true)){ ?>
	<div class="dash_bar_right">
		Credit Limit : <span>Rs <?=format_price($f['credit_limit'])?></span>
	</div>
	<?php } ?>
</div>
<?php /*/?>


<div class="page_topbar_right"></div>

<div class="container_div" style="margin-top: 0px;">
	<div class="tab_view">
		<ul class="fran_tabs">
			<li><a href="#name">Basic Details</a></li>
				<?php if($this->erpm->auth(PNH_EXECUTIVE_ROLE|CALLCENTER_ROLE,true)){?>
					<li><a href="#actions" onclick="load_receipts(this,'actions',0,<?=$f['franchise_id']?>,100)">Credits and MIDs</a></li>
				<?php }?>
			<li><a href="#statement" class="account_statement">Account Statement &amp; Topup</a></li>
			<li><a href="#orders" >Orders</a></li>
			<li><a href="#return_products" onclick="load_all_return_prods(0)" >Returns</a></li>
			<li><a href="#credit_notes" onclick="load_credit_notes(0)" >Invoice Credit Notes</a></li>
				<?php if($is_prepaid){?>
					<li>
						<a href="#voucher_activity" onclick="load_voucher_activity(0)" id="voucher_tab">Voucher</a>
					</li>
				<?php }?>
				<?php if($is_membrsch_applicable){?>
					<li><a href="#shipped_imeimobslno" onclick="load_allshipped_imei(0)">IMEINO Activations</a></li>
				<?php }?>
			<?php /*?>	
			<li><a href="#status_log">Status Log</a></li>
			<?php /*/?>
			<li><a href="#analytics" class="analytics">Analytics</a></li>
			<li><a href="#ship_log" class="ship_log">Shipped &amp; Delivered Log</a></li>
			<!--<li><a href="<?=site_url("admin/pnh_addfranchise/$fid")?>">SMS Log</a></li>-->
		</ul>
	
		<!-- Invoice Credit Notes START -->
		<div id="credit_notes">
			<div class="module_cont">
				<h3 class="module_cont_title">Credit Notes Raised</h3>
				<div class="module_cont_block">
					<div class="module_cont_block_grid_total fl_left">
						<span class="stat total">Total : <b>10</b></span> 
					</div>
					
					<div class="module_cont_block_filters fl_right">&nbsp;</div>
					
					<div class="module_cont_block_grid" style="clear:both">
						<table class="datagrid" width="100%">
							<thead>
								<th width="20">#</th>
								<th width="30">CreditNote ID</th>
								<th width="30">Invoice no</th>
								<th width="30">Order no</th>
								<th width="30">Amount (Rs)</th>
								<th width="100">Created On</th>
							</thead>
							<tbody></tbody>
						</table>	
					</div>
					
					<div class="module_cont_grid_block_pagi">
					</div>
				</div>
			</div>	
		</div>
		<!-- Invoice Credit Notes START -->
		
		<?php /*?>
		<!-- franchise status log START -->
		<div id="status_log">
			<div class="tab_view">
				<ul class="fran_tabs">
					  <li><a href="#permant_suspn">Permanent Suspension</a></li>
					<li><a href="#payment_suspn">Payment Suspension</a></li>
					<li><a href="#temp_suspn">Temporary Suspension</a></li>
					<li><a href="#live_suspn">Unsuspended Log</a></li>
				</ul>
				
				<?php $log_res=$this->db->query("SELECT l.*,a.name FROM franchise_suspension_log l JOIN king_admin a ON a.id=suspended_by WHERE franchise_id=? AND suspension_type IN(0,1,2,3)",$f['franchise_id'])?>
				<?php if($log_res->num_rows()){?>
					<div id="permant_suspn">
						<table class="datagrid">
							<thead>
								<th>Suspended Type</th>
								<th>Reason</th>
								<th>Suspended On</th>
								<th>Suspended By</th>
							</thead>
							
							<?php
								foreach($log_res->result_array() as $l){
									if( $l['suspension_type']==1){?>
									<tr>
									<td><?php echo $fran_status_arr[$l['suspension_type']]?></td>
									<td><?php echo $l['reason']?></td>
									<td><?php echo format_datetime_ts($l['suspended_on'])?></td>
									<td><?php echo $l['name']?></td>
									</tr>
									<?php }?>
								<?php } ?>
						</table>
					 </div>
			
					<div id="payment_suspn">
						<table class="datagrid">
							<thead>
								<th>Suspended Type</th><th>Reason</th><th>Suspended On</th><th>Suspended By</th>
							</thead>
							<?php 
							foreach($log_res->result_array() as $l){
								if( $l['suspension_type']==2){?>
									<tr>
										<td><?php echo $fran_status_arr[$l['suspension_type']]?></td>
										<td><?php echo $l['reason']?></td>
										<td><?php echo format_datetime_ts($l['suspended_on'])?></td>
										<td><?php echo $l['name']?></td>
									</tr>
								<?php } ?>
							 <?php }?>
						</table>
					</div>
			
					<div id="live_suspn">
						<table class="datagrid">
							<thead>
								<th>Reason</th><th>Unsuspended On</th><th>Suspended By</th>
							</thead>
						
							<?php 
							foreach($log_res->result_array() as $l){
								if( $l['suspension_type']==0){?>
									<tr>
										<td><?php echo $l['reason']?></td>
										<td><?php echo format_datetime_ts($l['suspended_on'])?></td>
										<td><?php echo $l['name']?></td>
									</tr>
								<?php } ?>
							<?php }?>
						</table>
					</div>
			
					<div id="temp_suspn">
						<table class="datagrid">
							<thead>
								<th>Suspended Type</th><th>Reason</th><th>Suspended On</th><th>Suspended By</th>
							</thead>
						
							<?php 
							foreach($log_res->result_array() as $l){
								if( $l['suspension_type']==3){?>
									<tr>
										<td><?php echo $fran_status_arr[$l['suspension_type']]?></td>
										<td><?php echo $l['reason']?></td>
										<td><?php echo format_datetime_ts($l['suspended_on'])?></td>
										<td><?php echo $l['name']?></td>
									</tr>
								<?php }?>
							<?php }?>
						</table>
					</div>
				<?php }else{
					echo '<div align="center">No data found</div>';
				}?>
			</div>
		</div>
		<!-- franchise status log END -->
		<?php */?>
		
		<!-- Analytics graph section start ---->
		<div id="analytics">
			<table width="100%">
				<tr>
					<td width="50%">
						<div style="float:left;width:100%">
							<span id="ttl_order_amt"></span>
							<span id="shipped_order_amt"></span>
							<span id="paymrent_order_amt"></span>
						</div>
					</td>
					<td width="50%">
						<div class="fr_menu_by_mn">
							<form id="grid_list_frm_to" method="post">
                                    <div style="margin:2.5px 0px;font-size:12px;text-align: right">
                                    	<b>From</b> : <input type="text" style="width: 90px;" id="frm"
                                                name="date_from" value="<?php echo date('d-m-Y',time()-90*60*60*24)?>" />
                                        <b>To</b> : <input type="text" style="width: 90px;" id="to"
                                                name="date_to" value="<?php echo date('d-m-Y',time())?>" /> 
                                        <button type="submit" class="sbutton small green"><span>Go</span></button>
                                    </div>
                            </form>
						</div>
					</td>
				</tr>
			</table>
			 
			<div id="payment_stat">
				<div class="payment_stat_view">
				</div>
			</div>
			
			<div id="fr_det_popup">
			</div>
					 
			<table width="100%">
				<tr>
					<td width="30%">
						<div id="fr_order_stat">
							<h5 style="margin: 4px 6px;">Menu Sales</h5>
							<div class="order_piestat_view">
							</div>
						</div>
					</td>
					<td>
						<div id="top_brand_bymenu_stat">
							<h5 style="margin:6px 43px"><div class="stat_head_wrap"></div></h5>
							<div class="fr_brand_stat_view">
							</div>
						</div>
					</td>
				</tr>
			</table>
			
			<table width="100%">
				<tr>
					<td width="35%">
						<div id="menu_det_tab" style="clear: both;">
							<h5 style="margin:4px 6px"><div class="stat_head_wrap"></div></h5>
							<div class="fr_top_sold"></div>
						</div>
					</td>
					<td></td>
				</tr>
			</table>
		</div>
		<!-- Analytics graph section end ---->
	
		<!-- Ship Log block Start-->
		<div id="ship_log">
			<!--<span class="ttl_amount_wrap">Total Shipped this month: <span class="ttl_amount_shipped"></span></span>
			<span class="ttl_amount_wrap">Total Delivered this month: <span class="ttl_amount_delivered"></span></span>-->
			<div class='shipment_log' style="margin:10px;clear:both"></div>
		</div>
		<!-- Ship Log block end-->
	
		<!-- List Franchise Returns Start -->
		<div id="return_products">
			<div class="module_cont">
				<h3 class="module_cont_title">Return List</h3>
				<div class="module_cont_block">
					<div class="module_cont_block_grid_total fl_left">
						<span class="stat total">Total : <b>10</b></span> 
					</div>
					
					<div class="module_cont_block_filters fl_right">
						<span class="filter_bydate">
							Filter by Date : 
							<input type="text" name="return_on_date" style="font-size: 12px;padding:3px 7px;width: 80px;" value="" placeholder="" >
							to 
							<input type="text" name="return_on_date_end" style="font-size: 12px;padding:3px 7px;width: 80px;" value="" placeholder="" >
						</span>
						<span class="filter_bykwd" style="margin-left:10px;">
							Search : <input type="text" name="return_kwd_srch" style="font-size: 12px;padding:3px 7px;width: 200px;" value="" placeholder="" >
							<input type="button" onclick="load_return_prods(0)" value="Search" />
						</span>
					</div>
					
					<div class="module_cont_block_grid" style="clear:both">
						<table class="datagrid" width="100%">
							<thead>
								<th width="20">Sno</th>
								<th width="30">Return ID</th>
								<th width="150">Returned On</th>
								<th width="80">Returned By</th>
								<th width="30">Invoice no</th>
								<th width="30">Order no</th>
								<th width="200">Product name</th>
								<th width="30">Qty</th>
								<th width="100">Returned For</th>
								<th width="100">Current Status</th>
								<th width="100">Last Updated On</th>
								<th width="100">Last Updated By</th>
								<th width="100">Remarks</th>
							</thead>
							<tbody></tbody>
						</table>	
					</div>
					<div class="module_cont_grid_block_pagi">
					</div>	
				</div>
			</div>
		</div>	
		<!-- List Franchise Returns End -->
	
		<?php  if($is_prepaid){?>
		<!-- prepaid voucher activity Start -->
		<div id="voucher_activity">
			<b>Voucher Activity</b>
			<div class="dash_bar_right" style="padding: 12px 6px;">
				<b>Voucher Book Value:<?php echo 'Rs'.format_price($this->db->query("SELECT SUM(`value`) AS ttl_value FROM `pnh_t_voucher_details` WHERE franchise_id=? AND `status`<=1 AND is_alloted=1 ",$f['franchise_id'])->row()->ttl_value)?></b>
			</div>
	
			<div class="dash_bar_right" style="padding: 12px 6px;">
				<b>Activated Voucher Value:<?php echo 'Rs'.format_price($this->db->query("SELECT SUM(`value`) AS ttl_value FROM `pnh_t_voucher_details` WHERE franchise_id=? AND `status`>=3 AND is_alloted=1 and is_activated=1",$f['franchise_id'])->row()->ttl_value)?></b>
			</div>
	
			<div class="dash_bar_right" style="padding: 12px 6px;">
				<b>Not Activated Voucher Value:<?php echo 'Rs'.format_price($this->db->query("SELECT SUM(`value`) AS ttl_value FROM `pnh_t_voucher_details` WHERE franchise_id=? AND `status`<=1 AND is_alloted=1 and is_activated=0",$f['franchise_id'])->row()->ttl_value)?></b>
			</div>
			<br> <br>
	
			<div class="tab_view">
				<ul class="fran_tabs">
					<li>
						<a href="#book_orders" onclick="load_voucher_activity(this,'book_orders',0,0)" id="book_orders_tab">Book orders</a>
					</li>
					<li>
						<a href="#inactivated_vouchers" onclick="load_voucher_activity(this,'inactivated_vouchers',0,0)">Inactive
							Vouchers</a>
					</li>
							
					<li>
						<a href="#activated_vouchers"
							onclick="load_voucher_activity(this,'activated_vouchers',0,0)">Activated
						</a>
					</li>
					
					<li>
						<a href="#fully_redeemed_vouchers"
								onclick="load_voucher_activity(this,'fully_redeemed_vouchers',0,0)">Fully
						Redeemed</a>
					</li>
					
					<li>
						<a href="#partially_redeemed_vouchers"
							onclick="load_voucher_activity(this,'partially_redeemed_vouchers',0,0)">Partially
							Redeemed</a>
					</li>
				</ul>
				
				<div id="book_orders">
					<h3>Book orders</h3>
					<div class="tab_content"></div>
				</div>
				
				<div id="inactivated_vouchers">
					<h3>Inactive Vouchers</h3>
					<div class="tab_content"></div>
				</div>
				
				<div id="activated_vouchers">
					<h3>Activated Voucher</h3>
					<div class="tab_content"></div>
				</div>

				<div id="fully_redeemed_vouchers">
					<h3>Fully Redeemed Voucher</h3>
					<div class="tab_content"></div>
				</div>

				<div id="partially_redeemed_vouchers">
					<h3>Partially Redeemed Voucher</h3>
					<div class="tab_content"></div>
				</div>
			</div>
			<!-- End of tab -->
		</div>
		<!-- Activity div blk end -->
	<!-- prepaid voucher activity End -->
	<?php }?>
	
	<!-- IMEI slno log Start-->
	<?php if($is_membrsch_applicable){?>
		<div id="shipped_imeimobslno">
			<?php 	
				$ttl_imei_status_res = $this->db->query("select franchise_id,is_imei_activated,count(distinct i.id) as ttl_orders
					from t_imei_no i
					join king_orders b on i.order_id = b.id
					join king_dealitems c on c.id = b.itemid
					join m_product_deal_link d on d.itemid = c.id
					join king_transactions e on e.transid = b.transid
					where b.status in (1,2) and b.imei_scheme_id > 0 and e.franchise_id = ?  
					group by is_imei_activated ",$f['franchise_id']);
					
				$ttl_purchased = 0;	
				$ttl_inactiv_msch = 0;
				$ttl_activated_msch = 0;
				foreach($ttl_imei_status_res->result_array() as $ttl_imei_det)
				{
					if($ttl_imei_det['is_imei_activated'])
						$ttl_activated_msch = $ttl_imei_det['ttl_orders'];
					else
						$ttl_inactiv_msch = $ttl_imei_det['ttl_orders'];
					
				}	
				
				$ttl_purchased = $ttl_activated_msch+$ttl_inactiv_msch;
					
				$ttl_imei_activated_credit=$this->db->QUERY("select sum(imei_reimbursement_value_perunit) as imei_credit  
																from king_orders a 
																join t_imei_no b on a.id = b.order_id 
																join king_transactions c on c.transid = a.transid
																where is_imei_activated = 1 and franchise_id = ? ",$f['franchise_id'])->ROW()->imei_credit;  
				$ttl_imei_pending_credit=$this->db->QUERY("select sum(imei_reimbursement_value_perunit) as imei_credit
						from king_orders a
						join t_imei_no b on a.id = b.order_id
						join king_transactions c on c.transid = a.transid
						where is_imei_activated = 0 and franchise_id = ? ",$f['franchise_id'])->ROW()->imei_credit;
				
			?>
			<div class="module_cont">
				<!--  <h3 class="module_cont_title">IMEI List</h3>-->
				<div class="module_cont_block">
					<div class="module_cont_block_grid_total fl_left" style="padding:5px;">
							<span class="stat total">Total Purchased : <b><?php echo  $ttl_purchased*1 ;?></b></span> 
							<span class="stat total">&nbsp;&nbsp;Active : <b><?php echo $ttl_activated_msch*1?></b></span> 
							<span class="stat total">&nbsp;&nbsp;Inactive : <b><?php echo $ttl_inactiv_msch*1?></b></span> 
					</div>
					
					<div class="module_cont_block_grid_total fl_right" style="padding:5px;">
						<span class="stat total " >Total Credit : <b style="background: #F3EE81;padding:3px 6px;border-radius:3px;"><?php echo 'Rs '.format_price($ttl_imei_activated_credit+$ttl_imei_pending_credit)?></b> &nbsp;&nbsp;</span>
						<span class="stat total " >Activated : <b style="background: #95DB95;padding:3px 6px;border-radius:3px;"><?php echo 'Rs '.format_price($ttl_imei_activated_credit)?></b>&nbsp;&nbsp;</span>
						<span class="stat total " >Pending : <b style="background: #F39381;padding:3px 6px;border-radius:3px;"><?php echo 'Rs '.format_price($ttl_imei_pending_credit)?></b>&nbsp;&nbsp;</span>
					</div>
					
					<div class="module_cont_block_filters clearboth" style="background: #f5f5f5;margin:0px;height: 27px;padding:3px;">
						
						<span class="filter_bykwd fl_right" >
							Search : <input type="text" name="imei_srch_kwd" style="font-size: 12px;padding:3px 7px;width: 200px;" value="" placeholder="" >
							<input type="button" onclick = "load_shipped_imei(0)" value="Search" />
						</span>
						
						<span style="margin-right:10px;padding:5px;font-size:12px;" >
							<b>Filter IMEI By</b>&nbsp; : 
							
							<select name="date_type">
								<option value="0" selected>Activated Date</option>
								<option value="1">Ordered Date</option>
							</select>
							
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
						</span>
					</div>
					
					<div class="module_cont_block_grid" style="clear:both">
						<table class="datagrid" width="100%">
							<thead>
								<th>Sno</th>
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
	<?php }?>
	<!-- IMEI slno log END-->
	
	<!-- sTART of name block -->	
		<div id="name">
	 		<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td valign="top" width="35%" >
						<div class="emp_bio">
							<div class="vm">
								<table cellspacing="5"  id="details">
									<tr>
										<td class="label">FID </td><td><b><?=$f['pnh_franchise_id']?></b></td>
									</tr>
							
									<?php if($f['is_lc_store']){?><tr><td>Type </td><td><b><?=$f['is_lc_store']?"LC Store":"Franchise"?> </b></td></tr><?php }?>
										<?php if($f['login_mobile1']){?>
											<tr><td class="label">Mobile </td>
												<td><?=$f['login_mobile1']?><img src="<?=IMAGES_URL?>phone.png" class="phone_small" onclick='makeacall("0<?=$f['login_mobile1']?>")'>,<?php }?>
													<?php if($f['login_mobile2']){?>
														<?=$f['login_mobile2']?>
														<img src="<?=IMAGES_URL?>phone.png" class="phone_small" onclick='makeacall("0<?=$f['login_mobile2']?>")'>
													<?php }?>
												</td>
											</tr>
								
											<?php foreach($this->db->query("select * from pnh_m_franchise_contacts_info where franchise_id=?",$f['franchise_id'])->result_array() as $c){?>
											<?php if($c['contact_name']){?><tr><td class="label">Contact Person </td><td><b><?=$c['contact_name']?> </b></td></tr><?php }?>
											<?php if($c['contact_designation']){?><tr><td class="label">Designation </td><td><b><?=$c['contact_designation']?> </b></td></tr><?php }?>
											<?php if($c['contact_telephone']){?><tr><td class="label">Telephone </td><td><b><?=$c['contact_telephone']?>,<?=$c['contact_fax']?></b></td></tr><?php }?>
											<?php }?>
											<?php if($f['address']){?><tr><td class="label">Address</td><td><b><?=$f['address']?></b></td></tr><?php }?>
											<?php if($f['store_area']){?><tr><td class="label">Area</td><td><b><?=$f['store_area']?>sqft</b></td></tr><?php }?>
											<?php
												$f['town_name'] = $this->db->query("select town_name from pnh_towns a join pnh_m_franchise_info b on a.id = b.town_id where franchise_id = ? ",$f['franchise_id'])->row()->town_name; 
											?>
											<tr><td class="label">Town  | Territory</td><td><b><?=$f['town_name']?> | </b><b><?=$f['territory_name']?></b></td></tr>
											<tr><td class="label">Town Courier Priorities</td><td style="background: #f9f9f9">
				                            <?php
	                                            $courier = $this->erpm->get_fran_courier_details($f['franchise_id']);
                                                if($courier === false) {
                                                    echo 'Not set.'; ?>
                                                    <a href="<?=site_url().'admin/towns_courier_priority/'.$f['territory_id'];?>" target="_blank" style="float:right;">Update</a>
                                                <?php }
                                                else {
											?>
                                            
                                            <ol style="padding:5px 20px">
                                                <li><span>
                                                    <b><?=$courier["c1"]['courier_name']?> / </b>
                                                    <b><?=$courier["c1"]['delivery_hours_1']?> Hrs</b> / 
                                                    Deliver <b><?php echo $this->erpm->get_delivery_type_msg($courier["c1"]['delivery_type_priority1']); ?></b>
                                                </span></li>
                                                <?php if($courier["c2"]['courier_name']){?>
                                                <li><span>
                                                    <b><?=$courier["c2"]['courier_name']?> / </b>
                                                    <b><?=$courier["c2"]['delivery_hours_2']?> Hrs</b> / 
                                                    Deliver <b><?php echo $this->erpm->get_delivery_type_msg($courier["c2"]['delivery_type_priority2']); ?></b>
                                                </span></li>
                                                <?php } ?>
                                                <?php if($courier["c3"]['courier_name']){?>
                                                <li><span>
                                                    <b><?=$courier["c3"]['courier_name']?> / </b>
                                                    <b><?=$courier["c3"]['delivery_hours_3']?> Hrs</b> / 
                                                    Deliver <b><?php echo $this->erpm->get_delivery_type_msg($courier["c3"]['delivery_type_priority3']); ?></b>
                                                </span></li>
                                                <?php } ?>
                                            </ol>
										<?php } ?>
	                                    </td></tr>
									<?php if($f['lat']){?><tr><td class="label">Latitude</td><td><b><?=$f['lat']?></b></td></tr><?php }?>
									<?php if($f['long']){?><tr><td class="label">Longitude</td><td><b><?=$f['long']?></b></td></tr><?php }?>
									<tr><td class="label">Registered</td><td><b><?php echo format_date(date('Y-M-d H:i:s',$f['created_on']))?></b></td></tr>
								</table>
							</div>
						</div>
						
						<div id="activity_menu_tabs" style="font-size: 12px;" >
							<ul>
								<li><a href="#activity">Activity</a></li>
								<li><a href="#alloted_menu">Alloted Menu</a></li>		
							</ul>	
							<div id="activity">
								<table width="100%">
									<tr>
										<td width="100%">
											<a style="white-space:nowrap" href="<?=site_url("admin/pnh_manage_devices/{$f['franchise_id']}")?>" class="button button-tiny ">Manage devices</a> &nbsp;&nbsp;
											<a style="white-space:nowrap" href="<?=site_url("admin/pnh_assign_exec/{$f['franchise_id']}")?>" class="button button-tiny ">Assign Executives</a> &nbsp;&nbsp; 
											<a style="white-space:nowrap" href="<?=site_url("admin/pnh_upload_images/{$f['franchise_id']}")?>" class="button button-tiny ">Upload Images</a> &nbsp;&nbsp; 
										 	<a  onclick="members_details(<?php echo $f['franchise_id']; ?>)" href="javascript:void(0)" class="button button-tiny ">Members</a>&nbsp;&nbsp; 
											<a onclick="load_bankdetails()" href="javascript:void(0)" class="button button-tiny ">Bank Details</a>&nbsp;&nbsp;
										</td>
									</tr>
								</table>
							</div>
							
							<div id="alloted_menu">
								<a class="edit_wrapper" style="font-size: 11px;color: blue;" href="<?php echo site_url("admin/pnh_edit_fran/{$f['franchise_id']}#v_shop")?>" style="float: right;margin-left: 12px;">Add/Edit</a>
								<?php
									if($fran_menu)
									{ 
								?>
										<ol start="1">
											<?php foreach($fran_menu as $fmenu){?>
												<li><?php echo $fmenu['menu'] ?></li>
											<?php }?>
										</ol>
								<?php 
									}else
									{
										echo '<b>No menus linked</b>';
									}
								?>
							</div>
						</div>	
					</td>
					
					<td valign="top">
						<div id="fran_misc_logs" style="font-size: 12px;" >
							<ul>
								<li><a href="#recent_call_log">Recent Call Log</a></li>
								<li><a href="#account_statement_summ">Recent Account statement</a></li>		
								<li><a href="#member_offers_log">Member Offers Log</a></li>		
							</ul>	
							<div id="recent_call_log">
								<table class="datagrid smallheader noprint" width="100%">
									<thead>
										<tr>
											<tH width="140">Call Made on</tH>
											<th width="150">By</th>
											<th>Message</th>									
										</tr>	
									</thead>
									<tbody></tbody>
								</table>
								<div id="call_log_pagi" class="pagination" align="right"></div>
							</div>
							
							<div id="account_statement_summ">
								<?php	
									if($this->erpm->auth(true,true)){
										$action_type_list = array(); 
										$action_type_list[1] = 'Invoice';
										$action_type_list[2] = 'Deposit';
										$action_type_list[3] = 'Topup';
										$action_type_list[4] = 'Membership';
										$action_type_list[5] = 'Correction';
										$action_type_list[7] = 'Credit Note';
								?>
									
									<table class="datagrid noprint" width="100%">
										<thead>
											<tr>
												<th>Type</th>
												<th>Date</th>
												<th>Credit</th>
												<th>Debit</th>
												<th>Remarks</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($this->db->query("select * from pnh_franchise_account_summary where franchise_id = ? order by statement_id desc limit 7 ",$f['franchise_id'])->result_array() as $t){?>
											<tr>
												<td><?=$action_type_list[$t['action_type']]?></td>
												<td><?=format_datetime($t['created_on'])?></td>
												<td><?=$t['credit_amt']?></td>
												<td><?=$t['debit_amt']?></td>
												<td width="200"><?=$t['remarks']?></td>
											</tr>
											<?php }?>
										</tbody>
									</table>
								<?php } ?>
										
									
								<div style="background: #eee; padding: 5px;">
									<form id="d_ac_form"
										action="<?=site_url("admin/pnh_download_stat/{$f['franchise_id']}")?>"
										method="post">
										<h4 style="margin: 0px;">Download account statement</h4>
										From <input type="text" name="from" id="d_ac_from" value="<?php echo (date('Y-m-d',$f['created_on']))?>" size=10> To
										<input size=10 type="text" name="to" id="d_ac_to" value="<?php echo (date('Y-m-d'))?>"> 
										<select name="type" style="display: none">
											<option value="new">New</option>
										</select>
										<b>Output</b> : 
										<select name="op_type">
											<option value="pdf">PDF</option>
											<option value="xls">XLS</option>
										</select>
										<input type="submit" value="Go">
									</form>
								</div>
							</div>
                                                        <div id="member_offers_log">
                                                                <div style="margin:5px 0px;padding:5px;border:1px solid #f7f7f7;">
                                                                        <?php $offers_q = $this->db->query("select a.*,b.first_name,b.user_id from pnh_member_offers a join pnh_member_info b on b.pnh_member_id=a.member_id where a.franchise_id=?",$f['franchise_id']);
                                                                        if($offers_q->num_rows())
                                                                        {?>
                                                                            <table class="datagrid smallheader" width="100%">
                                                                                    <tr>
                                                                                        <th>#</th>
                                                                                        <th>Created on</th>
                                                                                        <th>Member Name</th>
                                                                                        <th>Transid</th>
                                                                                        <th>Type</th>
                                                                                        <th>Value</th>
                                                                                        <th>Status</th>
                                                                                        <th>Actions</th>
                                                                                    </tr>

                                                                                    <?php
                                                                                    $offers = $offers_q->result_array();
                                                                                    $arr_offer_type = array(0=>"Insurance Opted",1=>"Free Recharge",2=>"Free Insurance",3=>"N/A or Not Opted",4=>"Requested for Insurance");
                                                                                    $arr_offer_status = array(0=>"Not Processed",1=>"Ready to Process",2=>"Processed");
                                                                                    foreach($offers as $i=>$offer) { ?>
                                                                                    <tr>
                                                                                        <td><?=++$i;?></td>
                                                                                        <td><?=format_datetime($offer['created_on']);?></td>
                                                                                        <td><a href="<?=site_url("/admin/pnh_viewmember/".$offer['user_id']);?>" target="_blank"><?=$offer['first_name'];?></a></td>
                                                                                        <td><a href="<?=site_url("/admin/trans/".$offer['transid_ref']);?>" target="_blank"><?=$offer['transid_ref'];?></a></td>
                                                                                        <td><?=$arr_offer_type[$offer['offer_type']];?></td>
                                                                                        <td>Rs. <?=formatInIndianStyle($offer['offer_value']);?></td>
                                                                                        <td><?=$arr_offer_status[$offer['process_status']];?></td>
                                                                                        <td><?php
                                                                                            if($offer['insurance_id'] != '') {?>
                                                                                                <a href="<?=site_url("admin/insurance_print_view/".$offer['insurance_id']);?>" target="blank">View</a>
                                                                                        <?php }
                                                                                                else echo '--';
                                                                                            ?></td>
                                                                                    </tr>
                                                                                    <!--<div style="padding:4px 5px;border-bottom:1px solid #DDDDDD;">Rs. <?=formatInIndianStyle($offer['offer_value']);?> worth of <?=$arr_offer_type[$offer['offer_type']];?> given</div>-->
                                                                        <?php   }
                                                                            } ?>
                                                                            </table>
                                                                    </div>
                                                        </div>
                                                    
						</div>
					</td>
				</tr>
			</table>
			 
			<br>
			<table width="100%">
				<tr>
					<td>
						<div id="members" Title="Members Details">
							<div>
								<div class="dash_bar">
									Total Members : <span><?=$this->db->query("select count(1) as l from pnh_member_info where franchise_id=?",$f['franchise_id'])->row()->l?>
									</span>
								</div>
								<div class="dash_bar">
									Last month registered : <span><?=$this->db->query("select count(1) as l from pnh_member_info where franchise_id=? and created_on between ".mktime(0,0,0,-1,1)." and ".mktime(23,59,59,-1,31),$f['franchise_id'])->row()->l?>
									</span>
								</div>
								<div class="dash_bar">
									This month registered : <span><?=$this->db->query("select count(1) as l from pnh_member_info where franchise_id=? and created_on >".mktime(0,0,0,date("m"),1),$f['franchise_id'])->row()->l?>
									</span>
								</div>
							</div>
						</div>
					</td>
				</tr>
				
				<tr>
					<td>
					<br>
					<fieldset >
					<legend><b>Scheme Discount</b></legend>
						<div>
							<a onclick="give_sch_discnt_frm()" href="javascript:void(0)" class="myButton_pnhfranpg">Give Scheme Discount</a>&nbsp;&nbsp;&nbsp;
							<a onclick = "give_supersch()" href="javascript:void(0)" class="myButton_pnhfranpg">Give Super Scheme</a>&nbsp;&nbsp;&nbsp;
							<?php if($is_membrsch_applicable ){?>
											<a onclick="give_membrsch()" href="javascript:void(0)"
												class="myButton_pnhfranpg">Give IMEI/Serialno Scheme</a>&nbsp;&nbsp;&nbsp;
											<?php }?>
							<a onclick="load_scheme_disc_history()" href="javascript:void(0)" class="myButton_pnhfranpg" style="float:right">Scheme Discount History</a>&nbsp;&nbsp;&nbsp;
							<h4 style="margin-bottom: 0px;">Active scheme discounts for brands &amp; categories</h4>
							
							
								<div class="tab_view tab_view_inner">
									<ol>
										<li><a href="#sch_disc">Scheme Discount</a></li>
										<li><a href="#super_sch">Super Scheme</a></li>
										<?php if($is_membrsch_applicable){?>								
										<li><a href="#membr_sch">IMEI/Serialno Scheme</a></li>
											<?php }?>
									</ol>
									<div id="sch_disc">
										<div class="tab_view">
											<ol>
												<li><a href="#sch_disc_active">Active</a></li>
												<li><a href="#super_sch_active">Expired</a></li>
											</ol>
											<div id="sch_disc_active" class="tab_view_inner">
												<table class="datagrid" width="100%">
												<thead>
													<tr>
														<th>Menu</th>
														<th>Brand</th>
														<th>Category</th>
														<th>Deals</th>
														<th>Discount</th>
														<th>Valid from</th>
														<TH>Valid upto</TH>
														<th>Added on</th>
														<th>Added by</th>
														<th></th>
													</tr>
												</thead>
												<tbody>
													<?php foreach($this->db->query("select s.*,a.name as admin,b.name as brand,c.name as category,s.menuid,i.name AS menu,d.name AS deal from pnh_sch_discount_brands s left outer join king_brands b on b.id=s.brandid left outer join king_categories c on c.id=s.catid join king_admin a on a.id=s.created_by JOIN `pnh_franchise_menu_link` m ON m.fid=s.franchise_id JOIN pnh_menu i ON i.id=s.menuid LEFT JOIN king_dealitems d ON d.id=s.dealid where s.franchise_id=? and ? between s.valid_from and s.valid_to GROUP BY s.id order by id desc ",array($fran['franchise_id'],time()))->result_array() as $s){
													
														if(!$s['is_sch_enabled'])
																continue;
													?>
													<?php// foreach($this->db->query("select h.*,a.name as admin,m.name AS menu  from pnh_sch_discount_track h left outer join king_admin a on a.id=h.created_by LEFT JOIN pnh_menu m ON m.id=h.sch_menu where franchise_id=? and ? between valid_from and valid_to and sch_type=1 order by h.id desc",array($fran['franchise_id'],time()))->result_array()  as $s){?>
													<tr>
														<td><?=$s['menu']?></td>
														<td><?=empty($s['brand'])?"All brands":$s['brand']?></td>
														<td><?=empty($s['category'])?"All categories":$s['category']?>
														</td>
														<td><?=empty($s['deal'])?"All deals":$s['deal']?></td>
														<td><?=$s['discount']?>%</td>
														<td><?=date("d/m/Y",$s['valid_from'])?></td>
														<td><?=date("d/m/Y",$s['valid_to'])?></td>
														<td><?=date("d/m/Y",$s['created_on'])?></td>
														<td><?=$s['admin']?></td>
														<td><?php if($s['is_sch_enabled']==1){?><a href="<?=site_url("admin/pnh_expire_scheme_discount/{$s['id']}")?>" class="danger_link">expire</a><?php }else{?><?php echo "<b>Expired</b>" ;}?></td>
													</tr>
													<?php }?>
												</tbody>
												</table>
											</div>
											<div id="super_sch_active" class="tab_view_inner">
												<table class="datagrid" width="100%">
												<thead>
													<tr>
														<th>Menu</th>
														<th>Brand</th>
														<th>Category</th>
														<th>Discount</th>
														<th>Valid from</th>
														<TH>Valid upto</TH>
														<th>Added on</th>
														<th>Added by</th>
														<th></th>
													</tr>
												</thead>
												<tbody>
													<?php foreach($this->db->query("select s.*,a.name as admin,b.name as brand,c.name as category,s.menuid,i.name AS menu from pnh_sch_discount_brands s left outer join king_brands b on b.id=s.brandid left outer join king_categories c on c.id=s.catid join king_admin a on a.id=s.created_by JOIN `pnh_franchise_menu_link` m ON m.fid=s.franchise_id JOIN pnh_menu i ON i.id=s.menuid where s.franchise_id=? and ? between s.valid_from and s.valid_to  GROUP BY s.id order by id desc ",array($fran['franchise_id'],time()))->result_array() as $s){
															if($s['is_sch_enabled'])
																continue;
													?>
													<?php// foreach($this->db->query("select h.*,a.name as admin,m.name AS menu  from pnh_sch_discount_track h left outer join king_admin a on a.id=h.created_by LEFT JOIN pnh_menu m ON m.id=h.sch_menu where franchise_id=? and ? between valid_from and valid_to and sch_type=1 order by h.id desc",array($fran['franchise_id'],time()))->result_array()  as $s){?>
													<tr>
														<td><?=$s['menu']?></td>
														<td><?=empty($s['brand'])?"All brands":$s['brand']?></td>
														<td><?=empty($s['category'])?"All categories":$s['category']?>
														</td>
														<td><?=$s['discount']?>%</td>
														<td><?=date("d/m/Y",$s['valid_from'])?></td>
														<td><?=date("d/m/Y",$s['valid_to'])?></td>
														<td><?=date("d/m/Y",$s['created_on'])?></td>
														<td><?=$s['admin']?></td>
														<td><?php if($s['is_sch_enabled']==1){?><a href="<?=site_url("admin/pnh_expire_scheme_discount/{$s['id']}")?>" class="danger_link">expire</a><?php }else{?><?php echo "<b>Expired</b>" ;}?></td>
													</tr>
													<?php }?>
												</tbody>
												</table>
											</div>
										</div>
									</div>
									<div id="super_sch">
										<div class="tab_view">
											<ol>
												<li><a href="#super_sch_active">Active</a></li>
												<li><a href="#super_sch_expired">Expired</a></li>
											</ol>
											<div id="super_sch_active" class="tab_view_inner">
												<?php $t=$this->db->query("SELECT s.*,a.name AS admin,b.name AS brand,c.name AS category,s.menu_id,i.name AS menuname FROM pnh_super_scheme s  LEFT OUTER JOIN king_brands b ON b.id=s.brand_id LEFT OUTER JOIN king_categories c ON c.id=s.cat_id JOIN king_admin a ON a.id=s.created_by  JOIN `pnh_franchise_menu_link` m ON m.fid=s.franchise_id  JOIN pnh_menu i ON i.id=s.menu_id WHERE s.franchise_id=? AND ? between s.valid_from and s.valid_to AND is_active=1 GROUP BY s.id ORDER BY id DESC ",array($fran['franchise_id'],time()));
												
												if($t->num_rows()){
												$super_sch=	$t->result_array();
												?>
												<table class="datagrid">
													<thead><tr><th>Menu</th><th>Brand</th><th>Category</th><th>Target Sales</th><th>Credit</th><th>Valid From</th><th>Valid To</th><th>Added On</th><th>Added By</th><th></th></tr></thead>
													<tbody>
														<?php foreach($super_sch as $super_sh){
																if(!$super_sh['is_active'])
																	continue;
														?>
														<tr>
														<td><?php echo $super_sh['menuname']?></td>
														<td><?=empty($super_sh['brand'])?"All brands":$super_sh['brand']?></td>
														<td><?=empty($super_sh['category'])?"All categories":$super_sh['category']?></td>
														<td><?php echo $super_sh['target_value'];?></td>
														<td><?php echo $super_sh['credit_prc'];?>%</td>
														<td><?=date("d/m/Y",$super_sh['valid_from'])?></td>
														<td><?=date("d/m/Y",$super_sh['valid_to'])?></td>
														<td><?=date("d/m/Y",$super_sh['created_on'])?></td>
														<td><?=$super_sh['admin']?></td>
														<td><?php if($super_sh['is_active']==1){?><a href="<?=site_url("admin/pnh_expire_superscheme/{$super_sh['id']}")?>" class="danger_link">expire</a><?php }else{?><?php echo "<b>Expired</b>"; }?></td>
														</tr>
													<?php }?>
													</tbody>
												</table>
												<?php }?>
											</div>
											<div id="super_sch_expired" class="tab_view_inner">
												<?php $t=$this->db->query("SELECT s.*,a.name AS admin,b.name AS brand,c.name AS category,s.menu_id,i.name AS menuname FROM pnh_super_scheme s  LEFT OUTER JOIN king_brands b ON b.id=s.brand_id LEFT OUTER JOIN king_categories c ON c.id=s.cat_id JOIN king_admin a ON a.id=s.modified_by  JOIN `pnh_franchise_menu_link` m ON m.fid=s.franchise_id  JOIN pnh_menu i ON i.id=s.menu_id WHERE s.franchise_id=?  AND is_active=0 GROUP BY s.id ORDER BY id DESC limit 10",$fran['franchise_id']);
												if($t->num_rows()){
												$super_sch=	$t->result_array();
												?>
												<table class="datagrid">
													<thead><tr><th>Menu</th><th>Brand</th><th>Category</th><th>Target Sales</th><th>Credit</th><th>Valid From</th><th>Valid To</th><th>Modified On</th><th>Modified By</th><th>Status</th></tr></thead>
													<tbody>
														<?php foreach($super_sch as $super_sh){
															if($super_sh['is_active'])
																continue;
														?>
														<tr>
														<td><?php echo $super_sh['menuname']?></td>
														<td><?=empty($super_sh['brand'])?"All brands":$super_sh['brand']?></td>
														<td><?=empty($super_sh['category'])?"All categories":$super_sh['category']?></td>
														<td><?php echo $super_sh['target_value'];?></td>
														<td><?php echo $super_sh['credit_prc'];?>%</td>
														<td><?=date("d/m/Y",$super_sh['valid_from'])?></td>
														<td><?=date("d/m/Y",$super_sh['valid_to'])?></td>
														<td><?=date("d/m/Y",$super_sh['created_on'])?></td>
														<td><?=$super_sh['admin']?></td>
														<td><?php if($super_sh['is_active']==1){?><a href="<?=site_url("admin/pnh_expire_superscheme/{$super_sh['id']}")?>" class="danger_link">expire</a><?php }else{?><?php echo "<b>Expired</b>"; }?></td>
														</tr>
													<?php }?>
													</tbody>
												</table>
												<?php }?>
									
											</div>
										</div>	
									</div>
									<?php if($is_membrsch_applicable){?>
									<div id="membr_sch">
													<div class="tab_view">
														<ol>
															<li><a href="#membr_sch_active">Active</a></li>
															<li><a href="#membr_sch_expired">Expired</a></li>
														</ol>
														<div id="membr_sch_active" class="tab_view_inner">
															<?php $m=$this->db->query("SELECT a.*,m.name AS menu,c.name AS cat_name,b.name AS brand_name,ad.name AS admin,a.id as sch_id,a.is_active as sch_active FROM imei_m_scheme a JOIN pnh_menu m ON m.id=a.menuid LEFT JOIN king_categories c ON c.id=a.categoryid LEFT JOIN king_brands b ON b.id=a.brandid JOIN king_admin ad ON ad.id=a.created_by WHERE a.is_active=1 and franchise_id=?",$fran['franchise_id']);?>
															<?php if($m->num_rows()){ 
																	$membr_sch=	$m->result_array();
															?>
															<table class="datagrid">
																<thead>
																	<tr>
																		<th>Menu</th>
																		<th>Brand</th>
																		<th>Category</th>
																		<th>Scheme Type</th>
																		<th>Credit Value</th>
																		<th>Valid from</th>
																		<TH>Valid upto</TH>
																		<th>Applicable from</th>
																		<th>Added on</th>
																		<th>Added by</th>
																		<th></th>
																	</tr>
																</thead>
																<tbody>
																	<tr>
																		<?php 
																			$mbr_schtypes=array("Fixed Fee","Percentage");
																			foreach($membr_sch as $membr_sh){
																		?>
																		<td><?php echo $membr_sh['menu']?></td>
																		<td><?php echo $membr_sh['brand_name']?$membr_sh['brand_name']:"All brands"?></td>
																		<td><?php echo($membr_sh['cat_name'])?$membr_sh['cat_name']:"All categories"?>
																		</td>
																		<td><?php  echo $mbr_schtypes[$membr_sh['scheme_type']]?>
																		</td>
																		<td><?php echo $membr_sh['credit_value']?></td>
																		<td><?php echo date("d/m/Y",$membr_sh['scheme_from'])?></td>
																		<td><?php echo date("d/m/Y",$membr_sh['scheme_to'])?>
																		</td>
	
																		<td><?php echo date("d/m/Y",$membr_sh['sch_apply_from'])?>
																		</td>
																		<td><?php echo date("d/m/Y",$membr_sh['created_on'])?>
																		</td>
																		<td><?php echo $membr_sh['admin']?></td>
																		<td><?php if($membr_sh['is_active']==1){?><a
																			href="<?=site_url("admin/pnh_expire_membrscheme/{$membr_sh['sch_id']}")?>"
																			class="danger_link">expire</a> <?php }else{?> <?php echo "<b>Expired</b>"; }?>
																		</td>
	
																	</tr>
																	<?php }?>
																</tbody>
															</table>
															<?php }?>
														</div>
	
														<div id="membr_sch_expired" class="tab_view_inner">
															<?php $m=$this->db->query("SELECT a.*,m.name AS menu,c.name AS cat_name,b.name AS brand_name,ad.name AS admin,a.is_active as sch_active FROM imei_m_scheme a JOIN pnh_menu m ON m.id=a.menuid LEFT JOIN king_categories c ON c.id=a.categoryid LEFT JOIN king_brands b ON b.id=a.brandid JOIN king_admin ad ON ad.id=a.modified_by WHERE a.is_active=0 and franchise_id=?",$fran['franchise_id']);?>
															<?php if($m->num_rows()){
																	$membr_sch=	$m->result_array();
															?>
															<table class="datagrid">
																<thead>
																	<tr>
																		<th>Menu</th>
																		<th>Brand</th>
																		<th>Category</th>
																		<th>Scheme Type</th>
																		<th>Credit Value</th>
																		<th>Valid from</th>
																		<TH>Valid upto</TH>
																		<th>Applicable from</th>
																		<th>Modified on</th>
																		<th>Modified by</th>
																		<th></th>
																	</tr>
																</thead>
																<tbody>
																	<tr>
																		<?php foreach($membr_sch as $membr_sh){
																			if($membr_sh['sch_active'])
																				continue;
														?>
																		<td><?php echo $membr_sh['menu']?></td>
																		<td><?php echo $membr_sh['brand_name']?$membr_sh['brand_name']:"All brands"?></td>
																		<td><?php echo($membr_sh['cat_name'])?$membr_sh['cat_name']:"All categories"?>
																		
																		<td><?php $mbr_schtypes=array("Fixed Fee","Percentage"); echo $mbr_schtypes[$membr_sh['scheme_type']]?>
																		</td>
																		<td><?php echo $membr_sh['credit_value']?></td>
																		<td><?php echo date("d/m/Y",$membr_sh['scheme_from'])?>
																		</td>
																		<td><?php echo date("d/m/Y",$membr_sh['scheme_to'])?></td>
																		<td><?php echo date("d/m/Y",$membr_sh['sch_apply_from'])?>
																		</td>
																		<td><?php echo date("d/m/Y",$membr_sh['modified_on'])?>
																		</td>
																		<td><?php echo $membr_sh['admin']?></td>
																		<td><?php if($membr_sh['sch_active']==1){?><a
																			href="<?=site_url("admin/pnh_expire_membrscheme/{$membr_sh['id']}")?>"
																			class="danger_link">expire</a> <?php }else{?> <?php echo "<b>Expired</b>"; }?>
																		</td>
	
																	</tr>
																	<?php }?>
																</tbody>
															</table>
															<?php }?>
														</div>
													</div>
												</div>
												<?php }?>
									</div>
							</div>
						
						</fieldset>
					</td>
				</tr>
			</table>
		</fieldset>
		<br>
			
		<fieldset>
			<legend><b>Device Information</b></legend>
				<table width="100%">
					<tr>
						<td width="20%">
							<?php 
								$app_v = '';
								$app_v_res = $this->db->query("select version_no from pnh_app_versions where id=? ",$f['app_version']);
								if($app_v_res->num_rows())
									$app_v=$app_v_res->row()->version_no;
							?>
						<p>
							<h4 style="margin: 0px; font-size: 15px;">
								<b>App Version : </b><span><?=$app_v?></span>
							</h4>
						</p>
						
						<form action="<?=site_url("admin/pnh_change_app_version")?>">
							<input type="hidden" name="fid" value="<?=$f['franchise_id']?>">
								<p>	Change to New Version : <select id="fran_ver_change">
																<option value="0">select</option>
																<?php foreach($this->db->query("select version_no,id from pnh_app_versions where id>?",$f['app_version'])->result_array() as $v){?>
																<option value="<?=$v['id']?>">
																	<?=$v['version_no']?>
																</option>
																<?php }?>
															</select>
								</p>
						</form>
					</td>	
				</tr>
			</table>
		</fieldset>
	</div>
	<!-- End of name block -->
	
	<?php if(1){?>	
		<div id="statement">
			<div class="fl_left">
				<div class="dash_bar_right">
					Adjustments : <span>Rs <?=format_price($acc_adjustments_val,2)?></span>
				</div>
				<?php /*
				<div class="dash_bar_right">
					Paid till Date : <span>Rs <?=format_price($paid_tilldate,2)?></span>
				</div>*/?>
			
				<div class="dash_bar_right">
					Credit Notes Raised : <span>Rs <?=format_price($credit_note_amt,2)?></span>
				</div>
				
				<div class="dash_bar_right">
					Bounced/Cancelled : <span>Rs <?=format_price($cancelled_tilldate,2)?></span>
				</div>
				<?php /*
				<?php if($this->erpm->auth(true,true)){?>
				<div class="dash_bar_right">
					Unshipped : <span>Rs <?=format_price($not_shipped_amount,2)?></span>
				</div>
				
				<div class="dash_bar_right">
					Shipped : <span>Rs <?=format_price($shipped_tilldate,2)?></span>
				</div>
				
				<div class="dash_bar_right">
					Ordered : <span>Rs <?=format_price($ordered_tilldate,2)?></span>
				</div>
				<?php } ?>
				<?php /*/?>
			</div>
                        <div class="clear"></div>
			<?php if(1){?>
				<div style="float: left; margin-top: 33px; margin-left: 20px; background: #f9f9f9; padding: 5px; min-width: 500px;font-size: 12px;">
					<h4 style="background: #9B9794; color: #fff; padding: 10px; margin: -5px -5px 5px -5px;">Make a Topup/Security Deposit</h4>
					<form method="post" id="top_form" action="<?=site_url("admin/pnh_topup/{$fran['franchise_id']}")?>">
						<table cellpadding=3 width="100%">
							<tr>
                                <td></td>
                                <td></td>
                                <td><span class="error_status"></span></td>
                            </tr>
							<tr>
								<td>Type</td><td>:</td>
								<td>
									<select name="r_type" id="r_type">
										<option value="1">Topup</option>
										<option value="0">Security Deposit</option>
									</select>
								</td>
							</tr>
							<tr>
								<td>Amount (Rs)</td><td>:</td>
								<td><input type="text" class="inp amount money" name="amount" id="receipt_amount" size=5 value="">
                                        <span class="reconciled_total">Total Adjusted (Rs.): <abbr title="Amount">0</abbr>
                                            <input type="hidden" name="total_val_reconcile" id="total_val_reconcile" value="" />
                                        </span>
								</td>
							</tr>
							<tr>
								<td>Instrument Type</td><td> :</td>
								<td><select name="type" class="inst_type">
										<option value="0">Cash</option>
										<option value="1">Cheque</option>
										<option value="2">DD</option>
										<option value="3">Transfer</option>
								</select></td>
							</tr>
							<tr>
								<td>Instrument Status</td><td> :</td>
								<td><select name="transit_type">
										<option value="0">In Hand</option>
										<option value="1">Via Courier</option>
										<option value="2">With Executive</option>
								</select></td>
							</tr>
							<tr class="inst inst_name">
								<td class="label">Bank name </td><td>:</td>
								<td><input type="text" name="bank" size=30></td>
							</tr>
							<tr class="inst inst_no">
								<td class="label">Instrument No</td><td> :</td>
								<td><input type="text" name="no" size=10></td>
							</tr>
							<tr class="inst inst_date">
								<td class="label">Instrument Date</td><td> :</td>
								<td><input type="text" name="date" id="sec_date" size=15></td>
							</tr>
                            <tr>
                                    <td>Reconcile</td><td> :</td>
                                    <td>
                                            <a href="javascript:void(0);" class="button button-tiny_wrap cursor button-primary clone_rows_invoice">+</a>
                                            <table border="0" cellspacing="0" cellpadding="2">
                                                <tbody id="reconcile_row"></tbody>
                                            </table>
                                    </td>
                            </tr>
                            <tr class="inst_msg">
								<td>Message</td><td> :</td>
								<td><textarea class="msg" name="msg" style="width:350px;height:80px;" ></textarea></td>
							</tr>
							<tr>
								<td align="right" colspan="3"><input type="submit" class="button button-rounded button-small button-flat-action" value="Submit"></td>
							</tr>
						</table>
					</form>
				</div>

				<div style="float: left; margin-top: 33px; margin-left: 20px; background: #f9f9f9; padding: 5px; width: 580px;font-size: 12px;">
						<h4	style="background: #9B9794; color: #fff; padding: 10px; margin: -5px -5px 5px -5px;">Account Statement Correction</h4>
						<form method="post" id="acc_change_form" action="<?=site_url("admin/pnh_acc_stat_c/{$fran['franchise_id']}")?>">
							<input type="hidden" name="is_manual_corr" value="1">
							<table cellpadding=3>
								<tr>
									<td width="100">Type</td><td>:</td>
									<td><select name="type">
											<option value="0">In (credit)</option>
											<option value="1">Out (debit)</option>
									</select></td>
								</tr>
								<tr>
									<td>Amount (Rs)</td><td>:</td>
									<td><input type="text" name="amount" class="inp" size=5>
									</td>
								</tr>
								<tr>
									<td>Description</td><td>:</td>
									<td><textarea name="desc" class="inp" style="width:400px;height:100px;" ></textarea></td>
								</tr>
								<tr>
								<td>Send SMS to Franchise</td><td>:</td>
									<td><label><input type="checkbox" name="sms" value="1"></label></td>
								</tr>
								<tr>
									<td colspan="3" align="right"><input type="submit" class="button button-rounded button-small button-flat-action" value="Make correction" ></td>
								</tr>
							</table>
						</form>
					</div>
				<?php }?>
				<div class="clear"></div></br></br>

				<div class="tab_view">
					<h4 style="margin-bottom: 0px">
						Receipts <a href="<?=site_url("admin/pnh_receiptsbyfranchise/1/".$f['franchise_id'])?>" style="font-size: 75%">activate/cancel</a>
					</h4>
						
					<ul>
						<li><a href="#pending" onclick="load_receipts(this,'pending',0,<?=$f['franchise_id']?>,100)" class="pending_receipt">Pending</a></li>
						<li><a href="#processed" onclick="load_receipts(this,'processed',0,<?=$f['franchise_id']?>,100)">Processed</a></li>
						<li><a href="#realized" onclick="load_receipts(this,'realized',0,<?=$f['franchise_id']?>,100)">Realized</a></li>
						<li><a href="#cancelled" onclick="load_receipts(this,'cancelled',0,<?=$f['franchise_id']?>,100)">Cancelled/Bounced</a></li>
						<li><a href="#acct_stat" onclick="load_receipts(this,'acct_stat',0,<?=$f['franchise_id']?>,100)">Account Correction</a></li>
						<?php if($this->erpm->auth(FINANCE_ROLE,true)){ ?>
						<li><a href="#security_cheques" >Security Cheque Details</a></li>
						<?php } ?>
						<li><a href="#unreconcile" onclick="load_receipts(this,'unreconcile',0,<?=$f['franchise_id']?>,10)">Un-Reconciled</a></li>
					</ul>
					
					<div id="pending">
						<div class="tab_content"></div>
					</div>
						
					<?php if($this->erpm->auth(FINANCE_ROLE,true)){ ?>
						<div id="security_cheques" style="min-height: 100px">
							<div class="clearboth" align="left" style="padding:10px;">
								<a href="javascript:void(0)" onclick="load_add_security_cheque_dlg()" style="font-size: 12px;"><b>Add security cheque</b></a>
							</div>	
							
							<div class="grid_view clearboth">
								<table class="datagrid">
									<thead><th>Slno</th><th>Cheque no</th><th>Bank name</th><th>Cheque Date</th><th>Amount(Rs)</th><th>Collected on</th></thead>
									<tbody>
										<?php
											$v_fran_security_chq_res = @$this->db->query("select * from pnh_m_fran_security_cheques where franchise_id = ? ",$f['franchise_id']);
											if($v_fran_security_chq_res->num_rows())
											{
										?>
								
										<?php
											$ttl = 1;
											foreach($v_fran_security_chq_res->result_array() as $v_fran_security_chq_row)
											{
										?>
											<tr>
												<td><?php echo $ttl++;?></td>
												<td><?php echo $v_fran_security_chq_row['cheque_no'] ?></td>
												<td><?php echo $v_fran_security_chq_row['bank_name'] ?></td>
												<td><?php echo $v_fran_security_chq_row['cheque_date'] ?></td>
												<td><?php echo $v_fran_security_chq_row['amount'] ?></td>
												<td><?php echo $v_fran_security_chq_row['collected_on'] ?></td>
											</tr>
										<?php				
											}
										?>
									<?php				
										}
									?>
								</tbody>
							</table>
						</div>
					</div>
				<?php				
					}
				?>
						
				<div id="processed">
					<div class="tab_content"></div>
				</div>
				
				<div id="realized">
					<div class="tab_content"></div>
				</div>
				
				<div id="cancelled">
					<div class="tab_content"></div>
				</div>
				
				<div id="acct_stat">
					<div class="tab_content"></div>
				</div>
                <div id="unreconcile">
					<div class="tab_content"></div>
				</div>
			</div>
			<div class="clear"></div>
		</div>
	<?php } ?>
				
	<?php if($this->erpm->auth(PNH_EXECUTIVE_ROLE|CALLCENTER_ROLE,true)){?>
		<div id="actions">
			<table width="100%">
				<tr>
					<td>
						<div>
							<fieldset>
								<legend><b>Allotted Member IDs</b></legend>
								<table class="datagrid" width="100%">
									<thead>
										<tr>
											<th>Start</th>
											<th>End</th>
											<th>Allotted on</th>
											<th>Allotted by</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach($this->db->query("select m.*,a.name as admin from pnh_m_allotted_mid m join king_admin a on a.id=m.created_by where franchise_id=?",$f['franchise_id'])->result_array() as $m){?>
										<tr>
											<td><?=$m['mid_start']?></td>
											<td><?=$m['mid_end']?></td>
											<td><?=date("d/m/y",$m['created_on'])?></td>
											<td><?=$m['admin']?></td>
											<?php }?>
									
									</tbody>
								</table>
								
								<?php if($this->erpm->auth(PNH_EXECUTIVE_ROLE,true)){?>
									<h4 style="margin-bottom: 0px;">Allot Member IDs</h4>
									<form
										action="<?=site_url("admin/pnh_allot_mid/{$f['franchise_id']}")?>"
										id="allot_mid_form" method="post">
							
										From 
										<input type="text" name="start" class="inp" size=7
											maxlength="8"> to <input maxlength="8" type="text" name="end"
												class="inp" size=7> <input type="submit" value="Allot">
									</form>
								<?php } ?>
							</fieldset>
						</div>
					</td>
				</tr>
				
				<tr></tr>
				
				<tr>
					<td>
						<div>
							<fieldset>
								<legend><b>Give Credit</b></legend>
								<form method="post" class="credit_form"
									action="<?=site_url("admin/pnh_give_credit")?>">
									<input type="hidden" name="reason" class="c_reason"> <input
										type="hidden" name="fid" value="<?=$f['franchise_id']?>">
									Enhance credit limit : Rs
									<?=$f['credit_limit']?>
									+ <input type="text" class="inp" size=4 name="limit"> <input
										type="submit" value="Add Credit">
								</form>
							
								<div class="tab_content">
								</div>
							
								<form method="post" class="credit_form"
									action="<?=site_url("admin/pnh_give_credit")?>">
									<input type="hidden" name="reason" class="c_reason"> <input
										type="hidden" name="reduce" value="1"> <input type="hidden"
										name="fid" value="<?=$f['franchise_id']?>"> Reduce credit limit
									: Rs
									<?=$f['credit_limit']?>
									- <input type="text" class="inp" size=4 name="limit"> <input
										type="submit" value="Reduce Credit">
								</form>
							</fieldset>
						</div>
						
						<p>
							<div id="sch_hist" title=" Give Scheme Discount" style="overflow: hidden">
								<form id="sch_form" method="post" action="<?=site_url("admin/pnh_give_sch_discount/{$f['franchise_id']}")?>" data-validate="parsley">
									<table cellspacing="10">
										<tr>
											<Td>Scheme Discount</td><td>:</td>
											<td>
												<input type="text" name="discount" value="1" size="5" data-required="true">%
											</td>
										</tr>
										<tr>
											<td>Menu </td><td>:</td>
											<td><select name="menu" class="schmenu" data-placeholder="Select Menu" style="width:250px;" data-required="true">
											<option value="0"></option>
											<?php foreach($this->db->query("SELECT distinct a.menuid as id,b.name FROM `pnh_franchise_menu_link`a JOIN pnh_menu b ON b.id=a.menuid WHERE a.status=1 and fid=? group by id order by b.name asc",$f['franchise_id'])->result_array() as $menu){?>
											<option value="<?php echo $menu['id']?>"><?php echo $menu['name']?></option>
											<?php }?>
											</select></td>

										</tr>
										
										<tr>
											<td>Category </td><td>:</td>
											<td><select name="cat" class="select_cat"  data-placeholder="Select Category" style="width:250px;" data-required="true"></select>
											</select>
											</td>
										</tr>
											
										<tr>
											<td>Brand </td><td>:</td>
											<td><select name="brand"  class="select_brand"  data-placeholder="Select Brand" style="width:250px;" data-required="true"></select>
											</td>
										</tr>
										
										<tr>
											<td>From</td><td>:</td>
											<td><input type="text" class="inp" size="10" name="start" id="d_start" data-required="true">
											 to <input type="text" class="inp" size="10" name="end" id="d_end" data-required="true"></td>
										</tr>
										
										<tr>
											<td valign="top">Reason</td><td valign="top">:</td>
											<td><textarea class="inp" name="reason" style="width: 300px;height: 100px;" data-required="true" ></textarea>
											</td>
										</tr>
										
										<tr>
											<td>Expire Previous Schemes</td><td>:</td><td><input type="checkbox" name="expire_schdisc" value="1" checked></td>
										</tr>
									</table>
								</form>
							</div>
						</p>
						
						<p>
							<div id="pnh_superschme" title=" Give Super Scheme" style="overflow: hidden">
								<form id="super_schform" method="post" action="<?=site_url("admin/pnh_give_super_sch/{$f['franchise_id']}")?>" data-validate="parsley">
									<table cellspacing="10">
										<tr>
											<Td>Cash Back</td><td>:</td>
											<td>
												<select name="credit" data-required="true">
													<?php for($i=1;$i<=4;$i++){?>
														<option value="<?=$i?>"><?=$i?>%</option>
													<?php }?>
												</select>
											</td>
										</tr>
											
										<tr>
											<td>Total Sales Value</td><td>:</td><td><input type="text" name="ttl_sales_value" data-required="true"></td></tr>
										<tr>
											<td>Menu </td><td>:</td>
											<td>
												<select name="super_schmenu" class="schmenu" data-placeholder="Select Menu" style="width:250px;" data-required="true">
													<option value="0"></option>
													<?php foreach($this->db->query("SELECT distinct a.menuid as id,b.name FROM `pnh_franchise_menu_link`a JOIN pnh_menu b ON b.id=a.menuid WHERE a.status=1 and fid=? group by id order by b.name asc",$f['franchise_id'])->result_array() as $menu){?>
													<option value="<?php echo $menu['id']?>"><?php echo $menu['name']?></option>
													<?php }?>
												</select>
											</td>
										</tr>
										
										<tr>
											<td>Category </td><td>:</td>
												<td>
													<select name="cat" class="select_cat"  data-placeholder="Select Category" style="width:250px;" data-required="true" ></select>
													</select>
												</td>
										</tr>
											
										<tr>
											<td>Brand </td><td>:</td>
											<td><select name="brand"  class="select_brand"  data-placeholder="Select Brand" style="width:250px;" data-required="true" ></select>
											</td>
										</tr>
											<!--  <tr>
												<td>From</td><td>:</td>
												<td><input type="text" class="inp" size="10" name="super_schstart" id="supersch_start" data-required="true">
												 to <input type="text" class="inp" size="10" name="super_schend" id="supersch_end" data-required="true"></td>
													
											</tr>-->
										<tr>
											<td valign="top">Reason</td><td valign="top">:</td>
											<td><textarea class="inp" name="reason" style="width: 300px;height: 100px;" data-required="true" ></textarea>
											</td>
										</tr>
											
										<tr><td>Validity</td><td>:</td><td><b>Valid For the Calender Month</b></td></tr>
										<tr><td>Expire Previous Schemes</td><td>:</td><td><input type="checkbox" name="expire_supersch" value="1" checked></td></tr>
									</table>
								</form>
							</div>
						</div>
					</p>
					
					<div id="pnh_membersch" title="Give Member Scheme">
						<form id="membr_schform" method="post"
										action="<?=site_url("admin/pnh_give_member_sch/{$f['franchise_id']}")?>"
										data-validate="parsley">

							<table cellspacing="10">
								<tr>
									<td>Scheme Type</td>
									<td>:</td>
									<td><select name="ime_schtype">
											<option value="0">Fixed Fee</option>
											<option value="1">percentage</option>
									</select></td>
								 </tr>
								 <tr>
									<Td>Cash Back</td>
									<td>:</td>
									<td><input type="text" name="mbrsch_credit" size="25">
									</td>
								</tr>

											<!--  <tr>
												<td>Menu </td><td>:</td>
												<td><b><?php //echo $is_membrsch_applicable['menu'] ;?></b></td>
											</tr>-->
								<tr>
									<td>Menu</td>
									<td>:</td>
									<td><select name="mbr_schmenu" class="schmenu"
											data-placeholder="Select Menu" style="width: 250px;"
											data-required="true">
												<option value="0"></option>
												<?php foreach($this->db->query("SELECT distinct a.menuid as id,b.name FROM `pnh_franchise_menu_link`a JOIN pnh_menu b ON b.id=a.menuid WHERE a.status=1 and fid=? group by id order by b.name asc",$f['franchise_id'])->result_array() as $menu){?>
												<option value="<?php echo $menu['id']?>">
													<?php echo $menu['name']?>
												</option>
												<?php }?>
										</select>
									</td>
								</tr>

								<tr>
									<td>Category</td>
									<td>:</td>
									<td><select name="mbr_schcat" class="select_cat"
										data-placeholder="Select Category" style="width: 250px;"
										data-required="true"></select> </select>
									</td>
								</tr>

								<tr>
									<td>Brand</td>
									<td>:</td>
									<td><select name="mbr_schbrand" class="select_brand"
										data-placeholder="Select Brand" style="width: 250px;"
										data-required="true"></select>
									</td>
								</tr>

								<tr>
									<td>From</td>
									<td>:</td>
									<td><input type="text" class="inp" size="10"
										name="msch_start" id="msch_start" data-required="true"> to
										<input type="text" class="inp" size="10" name="msch_end"
										id="msch_end" data-required="true"></td>

								</tr>
								<tr>
									<td>Apply From</td>
									<td>:</td>
									<td><input type="text" class="inp" size="10"
										name="mbrsch_applyfrm" id="msch_applyfrm"
										data-required="true">
									</td>
								</tr>
								<tr>
									<td width="50%">Expire Previous Scheme </td><td>:</td><td><input type="checkbox" name="expire_msch" value="1" checked></td>
								</tr>
							</table>
						</div>
					</form>
				</div>	
			</tr>			
	</div>

					</td>
				

				<div class="clear"></div>
			</table>
		</div>
				<?php } ?>
		 <div class="clear"></div>
					<div id="schme_disc_history" title="Scheme Discount History" >
						<h4 style="margin-bottom: 0px;">Scheme Discount History</h4>
						<table class="datagrid" width="100%">
							<thead>
								<tr>
									<th>Discount</th>
									<th>Menu</th>
									<th>Brand</th>
									<th>Category</th>
									<th>From</th>
									<th>To</th>
									<th>Reason</th>
									<th>Given by</th>
									<th>On</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($this->db->query("select h.*,a.name as admin,m.name AS menu  from pnh_sch_discount_track h left outer join king_admin a on a.id=h.created_by LEFT JOIN pnh_menu m ON m.id=h.sch_menu where franchise_id=? order by h.id desc",$f['franchise_id'])->result_array() as $h){?>
								<tr>
									<td><?=$h['sch_discount']?>%</td>
									<td><?php echo $h['menu'];?> </td>
									<td><?=$h['brandid']==0?"All Brands":$this->db->query("select name from king_brands where id=?",$h['brandid'])->row()->name?>
									</td>
									<td><?=$h['catid']==0?"All Categories":$this->db->query("select name from king_categories where id=?",$h['catid'])->row()->name?>
									</td>
									<td><?=date("d/m/y",$h['sch_discount_start'])?></td>
									<td><?=date("d/m/y",$h['sch_discount_end'])?></td>
									<td><?=$h['reason']?></td>
									<td><?=$h['admin']?></td>
									<td><?=date("g:ia d/m/y",$h['created_on'])?></td>
								</tr>
								<?php }?>
							</tbody>
						</table>
						<div style="padding: 10px; background: #eee;">
							Current Scheme Discount for all brands : <b><?=$f['sch_discount']?>%</b><br>
							Valid from :
							<?=date("d/m/y",$f['sch_discount_start'])?>
							&nbsp; &nbsp; Valid upto :
							<?=date("d/m/y",$f['sch_discount_end'])?>
							<br>
							<?php if($f['is_sch_enabled']){?>
							Status : Enabled <a style="float: right" class="danger_link"
								href="<?=site_url("admin/pnh_disenable_sch/{$f['franchise_id']}/0")?>">disable</a>
							<?php }else{?>
							Status : Disabled <a style="float: right" class="danger_link"
								href="<?=site_url("admin/pnh_disenable_sch/{$f['franchise_id']}/1")?>">enable</a>
							<?php }?>
							<div class="clear"></div>
						</div>
					</div>
					<div class="clear"></div>


				<div id="orders">

					<div>
						<?php /*?>
						<?php if($this->erpm->auth(true,true)){?>
						<div class="dash_bar_right">
							Total Order value : <span>Rs <?=format_price($this->db->query("select sum(amount) as l from king_transactions where franchise_id=?",$f['franchise_id'])->row()->l,0)?></span>
						</div>
						<div class="dash_bar_right">
							Total Orders : <span><?=$this->db->query("select count(1) as l from king_transactions where franchise_id=?",$f['franchise_id'])->row()->l?></span>
						</div>
						<?php } ?>
						<?php /*/?>
						
						<!--<div class="dash_bar">
							Orders last month : <span><?=$this->db->query("select count(1) as l from king_transactions where franchise_id=? and init between ".mktime(0,0,0,date("m")-1,01,date('Y'))." and ".(mktime(0,0,0,date("m"),01,date('Y'))-1),$f['franchise_id'])->row()->l?>
							</span>
						</div>
						<div class="dash_bar">
							Orders this month : <span><?=$this->db->query("select count(1) as l from king_transactions where franchise_id=? and init >".mktime(0,0,0,date("m"),1),$f['franchise_id'])->row()->l?>
							</span>
						</div>
						<div class="dash_bar">
							Value last month : <span>Rs <?=format_price($this->db->query("select sum(amount) as l from king_transactions where franchise_id=? and init between ".mktime(0,0,0,-1,1)." and ".mktime(23,59,59,-1,31),$f['franchise_id'])->row()->l,2)?>
							</span>
						</div>
						<div class="dash_bar">
							Value this month : <span>Rs <?=format_price($this->db->query("select sum(amount) as l from king_transactions where franchise_id=? and init >".mktime(0,0,0,date("m"),1),$f['franchise_id'])->row()->l,2)?>
							</span>
						</div>
						<div class="dash_bar">
							Total commission : <span>Rs <?=format_price($this->db->query("select sum(o.i_coup_discount) as l from king_transactions t join king_orders o on o.transid=t.transid where t.franchise_id=?",$f['franchise_id'])->row()->l,2)?>
							</span>
						</div>
						<div class="dash_bar">
							Total commission this month : <span>Rs <?=format_price($this->db->query("select sum(o.i_coup_discount) as l from king_transactions t join king_orders o on o.transid=t.transid where t.franchise_id=? and o.time>".mktime(0,0,0,date("m"),1),$f['franchise_id'])->row()->l,2)?>
							</span>
						</div>
						-->
						<div class="tab_list" style="float:left">
							<ol>
								<li><a class="all" href="javascript:void(0)" onclick="load_franchise_orders('all')" >All</a></li>
								<li><a class="shipped" href="javascript:void(0)" onclick="load_franchise_orders('shipped')">Shipped</a></li>
								<li><a class="unshipped" href="javascript:void(0)" onclick="load_franchise_orders('unshipped')">UnShipped</a></li>
								<li><a class="cancelled" href="javascript:void(0)" onclick="load_franchise_orders('cancelled')">Cancelled</a></li>
								<li><a class="part_ship" href="javascript:void(0)" onclick="load_franchise_orders('part_ship')">Partially Shipped</a></li>
								<li><a class="batch_closed" href="javascript:void(0)" onclick="load_franchise_orders('batch_closed')">Disabled from Batch</a></li>
								<li><a class="product_enquired" href="javascript:void(0)" onclick="load_franchise_orders('product_enquired')">Product Enquired</a></li>
								<li><a class="fran_prodpricequote" href="javascript:void(0)" onclick="load_franchise_orders('fran_prodpricequote')">Franchise Price Quote</a></li>
								
							</ol>
						</div>
					</div>
					<div id="franchise_order_list_wrapper" style="clear: both; z-index: 9 !important;">
						
						<div id="franchise_ord_list" style="clear: both;overflow: hidden">
							<div >
								<form id="franchise_ord_list_frm" method="post"
									action="<?php echo site_url('admin/jx_pnh_getfranchiseordersbydate')?>">
									<input type="hidden" value="all" name="type">
									<input type="hidden" name="fid"
										value="<?php echo $f['franchise_id']?>"> <b>Show Orders : </b>
									From : <input type="text" style="width: 90px;" id="ord_fil_from"
										name="ord_fil_from" value="<?php echo date('Y-m-01',time())?>" />
									To : <input type="text" style="width: 90px;" id="ord_fil_to"
										name="ord_fil_to" value="<?php echo date('Y-m-d',time())?>" /> <input
										type="button" onclick="load_franchise_orders(1)" value="Submit">
								</form>
							</div>
							<div class="franchise_ord_list_content" style="margin-top: -10px;"></div>
						
							
							<div id="remarks_changestatus" title="Change Amount Transit Status">
								<form id="transit_rmks" method="post" action="<?php echo site_url("admin/pnh_change_receipt_trans_type/{$pr['receipt_id']}")?>" date-validate="parsley">
									<table>
										<tr><td><b>Receipt Id</b></td><td><b>:</b></td><td id="r_receiptid"><b></b></td></tr>
										<tr><td><b>Remarks</b></td><td><b>:</b></td><td><textarea name="transit_rmks" data-required="true" style="width: 331px; height: 172px;"></textarea></td></tr>
										
										<tr><td><input type="hidden" name="rid" value=""></td></tr>
									</table>
								</form>
							</div>
						</div>  
					</div>
				</div>
				<div id="images">
					<table width="100%" cellpadding=10>
						<tr>
							<?php foreach($this->db->query("select * from pnh_franchise_photos where franchise_id=?",$f['franchise_id'])->result_array() as $i=>$img){?>
							<td align="center"><a
								href="<?=ERP_IMAGES_URL?>franchises/<?=$img['pic']?>"
								target="_blank"><img
									src="<?=ERP_IMAGES_URL?>franchises/<?=$img['pic']?>"
									width="200"> </a>
								<div>
									<?=$img['caption']?>
								</div>
							</td>
							<?php if(($i+1)%4==0) echo '</tr><tr>'; }?>
						</tr>
					</table>
				</div>

				<div id="bank" title="Bank Details" style="display: none;">
					<div style="margin: 5px;">
						<table class="datagrid">
							<thead>
								<tR>
									<th>Bank Name</th>
									<th>Account No</th>
									<th>Branch Name</th>
									<th>IFSC Code</th>
								</tR>
							</thead>
							<tbody>
								<?php foreach($this->db->query("select * from pnh_franchise_bank_details where franchise_id=?",$f['franchise_id'])->result_array() as $b){?>
								<tr>
									<td><?=$b['bank_name']?></td>
									<td><?=$b['account_no']?></td>
									<td><?=$b['branch_name']?></td>
									<td><?=$b['ifsc_code']?></td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
						<?php if(empty($b)) echo "No bank details linked"?>
					</div>
					<input type="button" value="Add new bank details"
						onclick='$("#bank_form").show();$(this).hide();'>
					<form id="bank_form" method="post"
						action="<?=site_url("admin/pnh_franchise_bank_details/{$f['franchise_id']}")?>"
						style="display: none;">
						<table style="background: #dedede; padding: 5px; margin: 10px;"
							cellpadding=5>
							<tr>
								<Th colspan="100%">Add new bank details</Th>
							</tr>
							<tr>
								<Td>Bank Name</td>
								<td>:</Td>
								<td><input type="text" class="inp mand bank_name"
									name="bank_name" size="30"></td>
							</tr>
							<tr>
								<Td>Account No</td>
								<td>:</Td>
								<td><input type="text" class="inp  mand account_no"
									name="account_no" size="20"></td>
							</tr>
							<tr>
								<Td>Branch Name</td>
								<td>:</Td>
								<td><input type="text" class="inp mand branch_name"
									name="branch_name" size="40"></td>
							</tr>
							<tr>
								<Td>IFSC Code</td>
								<td>:</Td>
								<td><input type="text" class="inp mand ifsc_code"
									name="ifsc_code" size="20"></td>
							</tr>
							<tr>
								<Td></Td>
								<td><input type="submit" value="Add bank details"></td>
							</tr>
						</table>
					</form>
				</div>


				</div>


				</div>
<!-- Franchise suspend form START -->
	<div id="fran_suspend" title="Suspend Franchise" style="overflow: hidden;">
		<form id="suspend_reasonfrm" method="post"
			action="<?php echo site_url("admin/pnh_suspend_fran/{$f['franchise_id']}")?>"
			data-validate="parsley">
			<input type="hidden" name="franchise_id" id="franchise_id">
			<table cellspacing="5" width="100%">
				<tr>
					<td valign="top"><b>Suspension Type</b></td>
					<td valign="top"><b>:</b></td>
					<td valign="top"><select name="sus_type" class="sus_type"
						data-placeholder="Select Suspension type" style="width:230px;" data-required="true">
							<option value="1"> Permanent Suspension</option>
							<option value="2">Payment Suspension</option>
							<option value="3">Temporary Suspension</option>
					</select></td>
				</tr>
				<tr class="credit_edit">
					<td valign="top"><b style="color:#cd0000">Mark Credit Limit 0</b></td>
					<td valign="top"><b>:</b></td>
					<td valign="top"><input type="checkbox" checked name="credit_edit" value="1"></td>
				</tr>
				<tr>
					<td valign="top"><b>Reason</b></td>
					<td valign="top"><b>:</b></td>
					<td valign="top"><textarea name="sus_reason" data-required="true"
							style="width: 320px; height: 110px;"></textarea></td>
				</tr>
			</table>
		</form>
	</div>
	<!-- Franchise suspend form END -->
	
	<div id="unsuspend_fran" title="Reason for unsuspending Franchise">
		<form id="unsuspend_reasonfrm" method="post" data-validate="parsley">
		<input type="hidden" name="unsuspend_fid" id="unsuspend_fid">
				<table cellspacing="5" width="100%">
				<tr>
						<td valign="top" valign="top"><b>Reason</b></td>
						<td valign="top"><b>:</b></td>
						<td valign="top"><textarea name="unsus_reason" data-required="true" style="width: 320px; height: 110px;"></textarea></td>
				</tr>
				</table>
		</form>
	</div>
	
	<!-- mark prepaid franchise modal-->
	<div id="mark_prepaid_franchise">
		<form id="mark_prepaid_franchise_form" action="<?php echo site_url('/admin/mark_prepaid_franchise') ?>" method="post" data-validate="parsley">
			<table class="datagrid" cellpadding="5" cellspacing="0" width="100%">
				<tr>
					<td>
						Reason
						<input type="hidden" name="prepaid_franchise_id" value="0">
					</td>
					<td>
						<textarea name="prepaid_reason" style="width:289px;height:73px" data-required="true"></textarea>
					</td>
				</tr>
			</table>
		</form>
	</div>
	<!-- mark prepaid franchise modal end-->
	<div id="ship_log_dlg" >
		<div id="ship_log_dlg_wrap"></div>
	</div>
	<div id="delivery_log_dlg" >
		<div id="delivery_log_dlg_wrap"></div>
	</div>
	<div id="ordered_log_dlg" >
		<div id="order_log_dlg_wrap"></div>
	</div>
	
	<div id="inv_transitlogdet_dlg" title="Shipment Transit Log">
		<h3 style="margin:3px 0px;"></h3>
		<div id="inv_transitlogdet_tbl">
			
		</div>
	</div>		
        <!--============================================<< Re-concilation Dialog box Start >>===================================-->

        <div id="dlg_unreconcile_view_list" style="display:none;">
        </div>
        
        <div id="dlg_unreconcile_form" style="display:none;">
                <h3>Select invoices for reconciliation </h3>
                <form id="dl_submit_reconcile_form">
                    <table class="datagrid1" width="100%">
                        <tr><td width="150">Receipt #</td><th>
                                <input type="text" readonly='true' id="dg_i_receipt_id" name="dg_i_receipt_id" value="" size="6" class="inp"/></th></tr>
                        <tr><td width="150">Receipt Amount</td><th>
                                Rs. <input type="text" readonly='true' id="dg_i_receipt_amount" name="dg_i_receipt_amount" value="" size="6" class="inp money"/></th></tr>
                        <tr><td width="150">Unreconcile Amount</td><th>
                                Rs. <input type="text" readonly='true' id="dg_i_unreconciled_value" name="dg_i_unreconciled_value" value="" size="6" class="dg_i_unreconciled_value inp money"/></th></tr>
                    </table>
                    <div>&nbsp;</div>
                    <div class="dg_error_status"></div>
                        <table class="datagrid nofooter" width="100%">
                            <thead> <tr><th>#</th><th>Document type</th><th>Document No</th><th width="100">Document Amount (Rs.)</th><th width="100">Adjusted Amount (Rs.)</th><th>&nbsp;</th></tr></thead>
                            <tbody class='dlg_invs_list'>
                                    <tr id='dg_reconcile_row_1' class="dg_invoice_row">
                                        <td>1</td>
                                        <td><select class="document_type" id='document_type' name='document_type[]' onchange="dg_recon_change_document_type(this,'dlg_selected_invoices_1','dg_reconcile_row_1','dlg_invs_list','dlg_unreconcile_form');"><option value=''>Choose</option><option value='inv' selected>Invoice</option><option value='dr'>Debit Note</option></select></td>
                                        <td>
                                            <select size='2' name='sel_invoice[]' id='dlg_selected_invoices_1' class='dg_sel_invoices' onchange="dg_fn_inv_selected(this,1,'dg_reconcile_row_1','dlg_invs_list','dlg_unreconcile_form');"></select>
                                        </td>
                                        <td><input type='text' readonly='true' class='inp dg_amt_unreconcile money' name='amt_unreconcile[]' id='dg_amt_unreconcile_1' size=6></td>
                                        <td><input type='text' class='inp dg_amt_adjusted money' name='amt_adjusted[]' id='dg_amt_adjusted_1' size=6 value='' onchange="dg_show_unconcile_total(this);" dialog-name="dlg_unreconcile_form"></td>
                                        <td>
                                            <a href='javascript:void(0)' class='button button-tiny_wrap button-primary' onclick="dg_add_invoice_row(this,'dg_invoice_row','dlg_invs_list','dlg_unreconcile_form');"> + </a>
                                        </td>
                                    </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4">
                                        <span style="float:right;">Total reconciled (Rs.):</span><br>
                                        <span style="float:right;">Un-reconciled after Reconcile (Rs.):</span>
                                    </td>
                                    <td align="left">
                                        <input type="text" readonly='true' name="ttl_reconciled" class="dg_l_total_adjusted_val money" value="0" size="6" /><br>
                                        <input type="text" readonly='true' name="ttl_unreconciled_after" class="dg_ttl_unreconciled_after money" value="0" size="6" />
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                            <!--<tr><td width="150">Reconcile Remarks</td><th><textarea id="dg_i_remarks" name="remarks" class="textarea" style="width:193px; height: 70px;"></textarea></th></tr>-->
                        </table>
                    </form>
            </div>
        
        
        
            <div id="dlg_credit_note_block" style="display:none;"><!-- Credit note dialog -->
                <h3>Reconcile the Credit Note</h3>
                <form id="dg_credit_note_form">
                    <table class="datagrid1" width="100%">
                        <tr><td width="150">Credit Note id</td><th>
                                #<input type="text" readonly='true' id="dg_i_credit_note_id" name="dg_i_credit_note_id" value="" size="6" class="inp"/></th></tr>
                        <tr><td width="150">Credit Amount</td><th>
                                Rs. <input type="text" readonly='true' id="dg_i_credit_amount" name="dg_i_credit_amount" value="" size="6" class="inp money"/></th></tr>
                        <tr><td width="150">Unreconcile Amount</td><th>
                                Rs. <input type="text" readonly='true' id="dg_i_unreconciled_value" name="dg_i_unreconciled_value" value="" size="6" class="dg_i_unreconciled_value inp money"/></th></tr>
<!--                        <tr><td width="150">Reconcile Remarks</td><th>
                                <textarea id="dg_i_remarks" name="remarks" class="textarea" style="width:193px; height: 70px;"></textarea></th></tr>-->
                    </table>
                   <div>&nbsp;</div>
                   
                    <div class="dg_error_status"></div>
                        <table class="datagrid nofooter" width="100%">
                            <thead> <tr><th>#</th><th>Document type</th><th>Document No</th><th width="100">Document Amount (Rs.)</th><th width="100">Adjusted Amount (Rs.)</th><th>&nbsp;</th></tr></thead>
                            <tbody class='dlg_credits_list'>
                                    <tr id='dg_credit_row_1' class="dg_credit_row">
                                        <td>1</td>
                                        <td><select class="document_type" id='document_type' name='document_type[]' onchange="dg_recon_change_document_type(this,'dlg_selected_invoices_1','dg_credit_row_1','dlg_credits_list','dlg_credit_note_block');"><option value=''>Choose</option><option value='inv' selected>Invoice</option><option value='dr'>Debit Note</option></select></td>
                                        <td>
                                            <select size='2' name='sel_invoice[]' id='dlg_selected_invoices_1' class='dg_sel_invoices' onchange="dg_fn_inv_selected(this,1,'dg_credit_row_1','dlg_credits_list','dlg_credit_note_block');"></select>
                                        </td>
                                        <td><input type='text' readonly='true' class='inp dg_amt_unreconcile money' name='amt_unreconcile[]' id='dg_amt_unreconcile_1' size=6></td>
                                        <td><input type='text' class='inp dg_amt_adjusted money' name='amt_adjusted[]' id='dg_amt_adjusted_1' size=6 value='' onchange="dg_show_unconcile_total(this);" dialog-name="dlg_credit_note_block"></td>
                                        <td>
                                            <a href='javascript:void(0)' class='button button-tiny_wrap button-primary' onclick="dg_add_invoice_row(this,'dg_credit_row','dlg_credits_list','dlg_credit_note_block');"> + </a>
                                        </td>
                                    </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4">
                                        <span style="float:right;">Total reconciled (Rs.):</span><br>
                                        <span style="float:right;">Un-reconciled after Reconcile (Rs.):</span>
                                    </td>
                                    <td align="left">
                                        <input type="text" readonly='true' name="ttl_reconciled" class="dg_l_total_adjusted_val money" value="0" size="6" /><br>
                                        <input type="text" readonly='true' name="ttl_unreconciled_after" class="dg_ttl_unreconciled_after money" value="0" size="6" />
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                </form>
            </div>
        <!--============================================<< Re-concilation Dialog box End >>===================================-->
        
</div>

<!--============================================<< Javascript Code Start >>===================================-->
<script>

 var twnlnk_franchise_html='';

function load_ship_del_calender()
{
		  var date = new Date();
		  var d = date.getDate();
		  var m = date.getMonth();
		  var y = date.getFullYear();

		  $('.shipment_log').fullCalendar({
			  	editable: false,
			  	droppable: false,
		   		draggable: false,
		   		
			   	header: {
		 		left: 'prev,next today',
		 		center: 'title',
		 		right: 'month,basicWeek,agendaDay'
		 		},
		 		
		   	
		   	selectable: true,
			selectHelper: true,
			
			events: function(start, end, callback){
					 var franchise_id='<?php echo $fran['franchise_id'] ?>';
					 // $('.ttl_amount_shipped').text(0);
					  //$('.ttl_amount_delivered').text(0);
				 	$('.shipment_log').fullCalendar('removeEvents')
				 		$.post(site_url+'/admin/jx_franchise_shipment_logonload',{'start': start.getTime(),'end': end.getTime(), 'fid':franchise_id},function(result){
				 				//$('.ttl_amount_shipped').html("Rs. "+result.ttl_amt_shipped);
				 				//$('.ttl_amount_delivered').html("Rs. "+result.ttl_amt_delivered);
                    		    callback(result.ship_del_list);
                    	},'json');
                    	
              },
		    eventRender: function(event, element) {
				var amount = event.amount;
					if(event.type == 'shipment')
					{element.find('.fc-event-title').html('Shipped Value <br /><b>Rs. '+amount+'</b>').parent().addClass('shipped_event');}
					else if(event.type == 'delivery')
					{element.find('.fc-event-title').html('Delivered Value <br /><b>Rs. '+amount+'</b>').parent().addClass('delivered_event');}
					else if(event.type == 'ordered')
					{element.find('.fc-event-title').html('Ordered Value <br /><b>Rs. '+amount+'</b>').parent().addClass('ordered_event');}
						
		    },
		     eventClick: function(calEvent, jsEvent, view) {
					var date=calEvent.start;
					var event_type=calEvent.type;
				 	var sel_date = (date.getDate())>9?(date.getDate()):'0'+(date.getDate());
				 	var sel_mnth = (date.getMonth()+1)>9?(date.getMonth()+1):'0'+(date.getMonth()+1);
			  	 	var sel_year = date.getFullYear();
			     	var ship_date = sel_year+'-'+(sel_mnth)+'-'+sel_date;
			     	var delivery_date = sel_year+'-'+(sel_mnth)+'-'+sel_date;
			     	var ordered_date = sel_year+'-'+(sel_mnth)+'-'+sel_date;
				 	var franchise_id='<?php echo $fran['franchise_id'] ?>';
				 
						if(event_type == "shipment")
						{
							var title = 'Shipment Log on '+sel_date+'/'+(sel_mnth)+'/'+sel_year;
								$( "#ship_log_dlg" ).data({'sel_date':sel_date,'sel_mnth':sel_mnth, 'sel_year':sel_year, 'fid':franchise_id, 'ship_date':ship_date }).dialog('open','option','title',title);
								$( "#ship_log_dlg" ).dialog('option','title',title);
						}else if(event_type == "delivery")
						{
							var title = 'Delivery Log on '+sel_date+'/'+(sel_mnth)+'/'+sel_year;
								$( "#delivery_log_dlg" ).data({'sel_date':sel_date,'sel_mnth':sel_mnth, 'sel_year':sel_year, 'fid':franchise_id, 'delivery_date':delivery_date }).dialog('open','option','title',title);
								$( "#delivery_log_dlg" ).dialog('option','title',title);
						 }else if(event_type == "ordered")
						{
							var title = 'Items Ordered on '+sel_date+'/'+(sel_mnth)+'/'+sel_year;
								$( "#ordered_log_dlg" ).data({'sel_date':sel_date,'sel_mnth':sel_mnth, 'sel_year':sel_year, 'fid':franchise_id, 'ordered_date':ordered_date }).dialog('open','option','title',title);
								$( "#ordered_log_dlg" ).dialog('option','title',title);
						 }
		  }
	});
	
}

$(function(){

	if(location.hash != '#ship_log')
	{
		$('.ship_log').click(function(e){
			if(!$('.shipment_log').hasClass( 'fc' ))
				load_ship_del_calender();
		});
	}else
	{
		load_ship_del_calender();
	}
});

$("#ordered_log_dlg" ).dialog({
		modal:true,
		autoOpen:false,
		width:'1000',
		height:'450',
		autoResize:true,
		open:function(){
		dlg = $(this);

		var ordered_date=$(this).data('ordered_date');
		var sel_date=$(this).data('sel_date');
		var sel_mnth=$(this).data('sel_mnth');
		var sel_year=$(this).data('sel_year');
		// ajax request fetch task details
	   $.post(site_url+'/admin/jx_franchise_ordered_log_bydate',{sel_date:$(this).data('sel_date'), sel_mnth:$(this).data('sel_mnth'), sel_year:$(this).data('sel_year'), fid:$(this).data('fid'), ordered_date:$(this).data('ordered_date')},function(result){
	   if(result.status == 'failure')
		{
			 $('#order_log_dlg_wrap').html('No Orders on '+ordered_date);
			 return false;
	    }
	    else
		{
	    	var order_det='';
	    	var k=1;
	    	 
	    	 order_det +='<table class="datagrid" width="100%"  ><tr><th width="5%">Sl.No</th><th>TransID</th><th>Itemid</th>';
	    	 order_det +='<th>Item</th><th>Quantity</th><th>Commission</th><th>Amount</th></tr>';
	    	 $.each(result.order_det,function(i,s1){
	    	 	s = s1[0];
	    	 	
	    	 		order_det +='<tr>';
	    	 		order_det +='	<td rowspan="'+s1.itemid.length+'">'+(k++)+'</td>';
	    	 		order_det +='	<td rowspan="'+s1.itemid.length+'"><a href="'+site_url+'/admin/trans/'+s1.transid+'" target="_blank">'+s1.transid+'</a></td>';
	    	 		j=0;
	    	 		$.each(s1.itemid,function(a,b){
	    	 			if(j!=0)
	    	 				order_det +='<tr>';			
	    	 			order_det +='<td>'+b.itemid+'</a></td>';
		    	 		order_det +='	<td><a href="'+site_url+'/admin/pnh_deal/'+b.itemid+'" target="_blank">'+b.name+'</a></td>';
		    	 		order_det +='	<td>'+b.qty+'</td>';
		    	 		order_det +='	<td>'+b.com+'</td>';
		    	 		order_det +='	<td>'+b.amount+'</td>';
		    	 		if(j!=0)
	    	 				order_det +='</tr>';
	    	 				j++;	
	    	 		});
	    	 		order_det +='</tr>';
	    	 });			
	    	 order_det +='<tfoot class="nofooter"><tr><td>Total </td><td></td><td></td><td></td><td style="text-align:left">'+result.ttl_qty+'</td><td style="text-align:left">Rs.'+result.ttl_com+'</td><td style="text-align:left">Rs.'+result.ttl_amt+'</td><td></td><td></td><td></td></tr></tfoot>';
	    	 $('#order_log_dlg_wrap').html(order_det);	
		}
	  },'json');
	}
});   
$("#ship_log_dlg" ).dialog({
		modal:true,
		autoOpen:false,
		width:'1000',
		height:'450',
		autoResize:true,
		open:function(){
		dlg = $(this);

		var ship_date=$(this).data('ship_date');
		var sel_date=$(this).data('sel_date');
		var sel_mnth=$(this).data('sel_mnth');
		var sel_year=$(this).data('sel_year');
		// ajax request fetch task details
	   $.post(site_url+'/admin/jx_franchise_shipment_log_bydate',{sel_date:$(this).data('sel_date'), sel_mnth:$(this).data('sel_mnth'), sel_year:$(this).data('sel_year'), fid:$(this).data('fid'), ship_date:$(this).data('ship_date')},function(result){
	   if(result.status == 'failure')
		{
			 $('#ship_log_dlg_wrap').html('No Shipments on '+ship_date);
			 return false;
	    }
	    else
		{
	    	var shipment_det='';
	    	var k=1;
	    	 
	    	 shipment_det +='<table class="datagrid" width="100%"  ><tr><th width="5%">Sl.No</th><th>TransID</th><th>Invoice</th>';
	    	 shipment_det +='<th>Item</th><th>Quantity</th><th>Commission</th><th>Amount</th><th></th></tr>';
	    	 $.each(result.ship_det,function(i,s1){
	    	 	s = s1[0];
	    	 	
	    	 		shipment_det +='<tr>';
	    	 		shipment_det +='	<td rowspan="'+s1.invoices.length+'">'+(k++)+'</td>';
	    	 		shipment_det +='	<td rowspan="'+s1.invoices.length+'"><a href="'+site_url+'/admin/trans/'+s1.transid+'" target="_blank">'+s1.transid+'</a><br /><span style="font-size:10px;font-weight:bold">Ordered On : '+s1.ord_on+'</span> </td>';
	    	 		j=0;
	    	 		$.each(s1.invoices,function(a,b){
	    	 			if(j!=0)
	    	 				shipment_det +='<tr>';			
	    	 			shipment_det +='<td><a href="'+site_url+'/admin/invoice/'+b.invoice_no+'" target="_blank">'+b.invoice_no+'</a></td>';
		    	 		shipment_det +='	<td><a href="'+site_url+'/admin/pnh_deal/'+b.itemid+'" target="_blank">'+b.name+'</a></td>';
		    	 		shipment_det +='	<td>'+b.qty+'</td>';
		    	 		shipment_det +='	<td>'+b.com+'</td>';
		    	 		shipment_det +='	<td>'+b.amount+'</td>';
		    	 		shipment_det +='	<td><a class="link_btn" onclick="get_invoicetransit_log(this,'+b.invoice_no+')" href="javascript:void(0)">View Transit Log</a></td>';
		    	 		if(j!=0)
	    	 				shipment_det +='</tr>';
	    	 				j++;	
	    	 		});
	    	 		shipment_det +='</tr>';
	    	 });			
	    	 shipment_det +='<tfoot class="nofooter"><tr><td>Total </td><td></td><td></td><td></td><td style="text-align:left">'+result.ttl_qty+'</td><td style="text-align:left">Rs.'+result.ttl_com+'</td><td style="text-align:left">Rs.'+result.ttl_amt+'</td><td></td><td></td><td></td></tr></tfoot>';
	    	 $('#ship_log_dlg_wrap').html(shipment_det);	
		}
	  },'json');
	}
});
$("#delivery_log_dlg" ).dialog({
		modal:true,
		autoOpen:false,
		width:'1000',
		height:'450',
		autoResize:true,
		open:function(){
		dlg = $(this);

		var delivery_date=$(this).data('delivery_date');
		var sel_date=$(this).data('sel_date');
		var sel_mnth=$(this).data('sel_mnth');
		var sel_year=$(this).data('sel_year');
		// ajax request fetch task details
	   $.post(site_url+'/admin/jx_franchise_delivery_log_bydate',{sel_date:$(this).data('sel_date'), sel_mnth:$(this).data('sel_mnth'), sel_year:$(this).data('sel_year'), fid:$(this).data('fid'), delivery_date:$(this).data('delivery_date')},function(result){
	   if(result.status == 'failure')
		{
			 $('#delivery_log_dlg_wrap').html('No deliveries on '+delivery_date);
			 return false;
	    }
	    else
		{
	    	var delivery_det='';
	    	var k=1;
	    	 
	    	 delivery_det +='<table class="datagrid" width="100%"  ><tr><th width="5%">Sl.No</th><th>TransID</th><th>Invoice</th>';
	    	 delivery_det +='<th>Item</th><th>Quantity</th><th>Commission</th><th>Amount</th><th></th></tr>';
	    	 $.each(result.delivery_det,function(i,s1){
	    	 	s = s1[0];
	    	 	
	    	 		delivery_det +='<tr>';
	    	 		delivery_det +='	<td rowspan="'+s1.invoices.length+'">'+(k++)+'</td>';
	    	 		delivery_det +='	<td rowspan="'+s1.invoices.length+'"><a href="'+site_url+'/admin/trans/'+s1.transid+'" target="_blank">'+s1.transid+'</a><br /><span style="font-size:10px;font-weight:bold">Ordered On : '+s1.ord_on+'</span></td>';
	    	 		j=0;
	    	 		$.each(s1.invoices,function(a,b){
	    	 			if(j!=0)
	    	 				delivery_det +='<tr>';			
	    	 			delivery_det +='<td><a href="'+site_url+'/admin/invoice/'+b.invoice_no+'" target="_blank">'+b.invoice_no+'</a></td>';
		    	 		delivery_det +='	<td><a href="'+site_url+'/admin/pnh_deal/'+b.itemid+'" target="_blank">'+b.name+'</a></td>';
		    	 		delivery_det +='	<td>'+b.qty+'</td>';
		    	 		delivery_det +='	<td>'+b.com+'</td>';
		    	 		delivery_det +='	<td>'+b.amount+'</td>';
		    	 		delivery_det +='	<td><a class="link_btn" onclick="get_invoicetransit_log(this,'+b.invoice_no+')" href="javascript:void(0)">View Transit Log</a></td>';
		    	 		if(j!=0)
	    	 				delivery_det +='</tr>';
	    	 				j++;	
	    	 		});
	    	 		delivery_det +='</tr>';
	    	 	});
	    	  delivery_det +='<tfoot class="nofooter"><tr><td>Total </td><td></td><td></td><td></td><td style="text-align:left">'+result.ttl_qty+'</td><td style="text-align:left">Rs.'+result.ttl_com+'</td><td style="text-align:left">Rs.'+result.ttl_amt+'</td><td></td><td></td><td></td></tr></tfoot>';
	    	 $('#delivery_log_dlg_wrap').html(delivery_det);	
		
		}
	  },'json');
	}
});

function get_invoicetransit_log(ele,invno)
{
	$('#inv_transitlogdet_dlg').data({'invno':invno,}).dialog('open');
}

var refcont = null;
$('#inv_transitlogdet_dlg').dialog({width:'900',height:'auto',autoOpen:false,modal:true,
											open:function(){

												
												//,'width':refcont.width()
												//$('div[aria-describedby="inv_transitlogdet_dlg"]').css({'top':(refcont.offset().top+15+refcont.height())+'px','left':refcont.offset().left});
												
												$('#inv_transitlogdet_tbl').html('loading...');
												$.post(site_url+'/admin/jx_invoicetransit_det','invno='+$(this).data('invno'),function(resp){
													if(resp.status == 'error')
													{
														alert(resp.error);
													}else
													{
														var inv_transitlog_html = '<table class="datagrid" width="100%"><thead><th width="30%">Msg</th><th width="10%">Status</th><th width="10%">Handle By</th><th width="10%">Logged On</th><th width="15%">SMS</th></thead><tbody>';
														$.each(resp.transit_log,function(i,log){
															inv_transitlog_html += '<tr><td>'+log[5]+'</td><td>'+log[1]+'</td><td>'+log[2]+'('+log[4]+')</td><td>'+log[3]+'</td><td>'+log[6]+'</td></tr>';
														});
														inv_transitlog_html += '</tbody></table>';
														$('#inv_transitlogdet_tbl').html(inv_transitlog_html);

														$('#inv_transitlogdet_dlg h3').html('Invoice no :<span style="color:blue;font-size:12px">'+resp.invoice_no+'</span>  Franchise name: <span style="color:orange;font-size:12px">'+resp.Franchise_name +'</span> Town : <span style="color:gray;font-size:12px">'+resp.town_name+'</span>'+' ManifestoNo :'+resp.manifesto_id);


														
														
													}
												},'json');
											}
									});	
	
$('.tab_view').tabs();
$('#loading_container_div').remove();
$('.container_div').css('visibility','visible');



$('.select_brand').chosen();
$('.select_cat').chosen();
$('.sus_type').chosen();

$('.sus_type').change(function(){

	if($(this).val()==2)
		$('.credit_edit').show();
	else
		$('.credit_edit').hide();
});
/*function load_allbrands()
{
	var brands_html='<option value=""></option>';
	$.getJSON(site_url+'/admin/jx_getallbrands','',function(resp){
		if(resp.status == 'error')
		{
			alert(resp.message);
		}
		else
		{
			brands_html+='<option value="0">All</option>';
			$.each(resp.brand_list,function(i,b){
			brands_html+='<option value="'+b.id+'">'+b.name+'</option>';
			});
		}
		
		$('select[name="brand"]').html(brands_html);
		$('select[name="brand"]').trigger("liszt:updated");
		
	});
}

function load_allcatgory()
{
	var cat_html='<option value=""></option>';
	$.getJSON(site_url+'/admin/jx_getallcategory','',function(resp){
		if(resp.status == 'error')
		{
			alert(resp.message);
		}
		else
		{
			cat_html+='<option value="0">All</option>';
			$.each(resp.cat_list,function(i,c){
				cat_html+='<option value="'+c.id+'">'+c.name+'</option>';
			});
		}
		
		$('select[name="cat"]').html(cat_html);
		$('select[name="cat"]').trigger("liszt:updated");
		
	});
}*/

	var fil_ordersby = 'all';
	
	function load_franchise_orders(stat)
	{
		if(stat != 1)
			fil_ordersby = stat;
				
		
		$('.tab_list .selected').removeClass('selected');
		$('.tab_list .'+fil_ordersby).addClass('selected');
			
				
		$('#franchise_ord_list_frm input[name="type"]').val(fil_ordersby);
		
		$('.franchise_ord_list_content').html("<div style='margin-top:15px'>Loading...</div>");
		$.post($('#franchise_ord_list_frm').attr('action'),$('#franchise_ord_list_frm').serialize()+'&stat='+stat,function(resp){
			$('.franchise_ord_list_content').html(resp);
		});
		return false;
	}
$(function(){
	$('#menu_det_tab').hide();
	
	var fran_reg_date = "<?php echo date('m/d/Y',$f['created_on'])?>";
	prepare_daterange('ord_fil_from','ord_fil_to');
	$("#d_start,#d_end").datepicker({minDate:0});
	$("#frm,#to").datepicker({dateFormat:'dd-mm-yy'});
	prepare_daterange('msch_start','msch_end');
	$('#msch_applyfrm').datepicker();
	$("#sec_date").datepicker();
	$( "#d_ac_from").datepicker({
	      changeMonth: true,
	      dateFormat:'yy-mm-dd',
	      numberOfMonths: 1,
	      maxDate:0,
	      minDate: new Date(fran_reg_date),
	    	onClose: function( selectedDate ) {
	        $( "#d_ac_to" ).datepicker( "option", "minDate", selectedDate );
	      }
	    });
	    $( "#d_ac_to" ).datepicker({
	      changeMonth: true,
	      dateFormat:'yy-mm-dd',
	      numberOfMonths: 1,
	      maxDate:0,
	      onClose: function( selectedDate ) {
	        $( "#d_ac_from" ).datepicker( "option", "maxDate", selectedDate );
	      }
	    });
	
	
	 
	$(".inst_type").change(function(){
		$(".inst").hide();
		if($(this).val()=="1")
		{
			$(".inst").show().val("");
			$(".inst_no .label").html("Cheque No");
			$(".inst_date .label").html("Cheque Date");
		}
		else if($(this).val()=="2")
		{
			$(".inst").show().val("");
			$(".inst_no .label").html("DD No");
			$(".inst_date .label").html("DD Date");
		}
		else if($(this).val()=="3")
		{
			$(".inst").show().val("");
			$(".inst_date .label").html("Transfer Date");
			$(".inst_no").hide();
		}
	}).val("0").change();

					
	$("#sch_form").data('psubumit',false).submit(function(){
		if($("#d_start").val().length==0 || $("#d_end").val().length==0)
		{
			$("#sch_form").data('psubumit',false);
			alert("Enter start date and end date");
			return false;
		}
		
		var sch_disc = $('input[name="discount"]',this).val();
			sch_disc = $.trim(sch_disc)*1;
			if(isNaN(sch_disc))
			{
				$("#sch_form").data('psubumit',false);
				alert("invalid discount entered");
				return false;
			}else
			{
				$.post(site_url+'/admin/jx_check_schemexist/<?php echo $fran['franchise_id']?>','',function(resp){
					if(resp.status == 'error')
					{
						$("#sch_form").data('psubumit',false);
						alert(resp.message);
												
					}else
					{
						$("#sch_form").data('psubumit',true);
						$("#sch_form").submit();
						$("#sch_hist").dialog('close');
					} 
				});
			}
		//alert(resp.message);
		//return $(this).data('psubumit');
	});
	

	$('.analytics').click(function(){
		setTimeout(function(){
			fran_menu_stat();
			payment_order_stat();
			brands_byfranch();	
		},400);
	});
	
	
	if(location.hash == '#analytics')
		$('.analytics').click();
	
	
	$(".credit_form").submit(function(){
		if(!is_integer($("input[name=limit]",$(this)).val()))
		{
			alert("Enter a number");
			return false;
		}
		reason=prompt("Please mention a resaon");
		if(reason.length==0)
			return false;
		$(".c_reason",$(this)).val(reason);
		return true;
	});
	$("#bank_form").submit(function(){
		f=true;
		$("input",$(this)).each(function(){
			if($(this).val().length==0)
			{
				alert($("td:first",$(this).parents("tr").get(0)).text());
				f=false;
				return false;
			}
			return f;
		});
	});

	$("#top_form").submit(function(){
		 
		 $('input[type="text"]').each(function(){
		 	$(this).val($.trim($(this).val()))
		 });
		 
		var error_msgs = new Array();
		var inst_type = $('select[name="type"]',this).val();
		var bank = $('input[name="bank"]',this).val();
		var inst_no = $('input[name="no"]',this).val();
		var dateval = $('input[name="date"]',this).val();
		
			if(!is_numeric($(".amount",$(this)).val()))
				error_msgs.push("Enter a valid amount");
			
			var inst_type_str = 'Cash';	
				if(inst_type == 1)
					inst_type_str = 'Cheque';
				else if(inst_type == 2) 	
					inst_type_str = 'DD';
				else if(inst_type == 3) 	
					inst_type_str = 'Transfer';
					
			// validate cash entry 
			if(inst_type != 0)
			{
				if(!bank.length)
					error_msgs.push("Enter Bank Name");
					
				if(!inst_no.length && inst_type != 3)
					error_msgs.push("Enter "+inst_type_str+" no");
					
				if(!dateval.length)
					error_msgs.push("Enter "+inst_type_str+" Date ");
						
			}
			
			if($(".msg",$(this)).val().length==0)
				error_msgs.push("Please enter Message");

            var sts = validate_selected_invoice_val();
            if(sts !== true) {
               error_msgs.push(sts);
            }
            var reconciled_total= format_number( $("abbr",".reconciled_total").html() );
            $("#total_val_reconcile").val(reconciled_total);

		if(error_msgs.length)
		{
			alert("Errors:\n"+error_msgs.join("\n"));
			return false;
		}
		
		if(!confirm("Are you sure you want add this receipt ?"))
			return false;
		
		if(inst_type == 0)
		{
			$('input[name="bank"]',this).val("");
			$('input[name="date"]',this).val("");
		}
		
		if(inst_type == 3 || inst_type == 0 )
			$('input[name="no"]',this).val("");
		
	});
	
	$("#acc_change_form").submit(function(){
		 $('input[type="text"]').each(function(){
		 	$(this).val($.trim($(this).val()))
		 });
		 
		var error_msgs = new Array();
		
			if(!$('input[name="amount"]',this).val().length)
				error_msgs.push("Enter amount");
				
			if(!$('input[name="desc"]',this).val().length)
				error_msgs.push("Enter description");
			
			if(error_msgs.length)
			{
				alert("Invalid Inputs Entered \n\n"+error_msgs.join("\n"));
				return false;
			}
			
			if(!confirm("Are you sure you want to make this correction ?"))
				return false;
		 
	});
	

	$("#allot_mid_form").submit(function(){
		if($("input[name=start]",$(this)).val().length!=8 || $("input[name=end]",$(this)).val().length!=8 || $("input[name=start]",$(this)).val().charAt(0)!="2" || $("input[name=end]",$(this)).val().charAt(0)!="2")
		{
			alert("Please enter valid MID");
			return false;
		}
		return true;
	});

	$("#d_ac_form").submit(function(){
		if($("#d_ac_from").val().length==0 || $("#d_ac_to").val().length==0)
		{
			alert("Please enter valid from and to date");
			return false;
		}
		return true;
	});

	$("#fran_ver_change").change(function(){
		if($(this).val()=="0")
			return;
		if(confirm("Are you sure to change the version?"))
			location="<?=site_url("admin/pnh_fran_ver_change/{$fran['franchise_id']}")?>/"+$(this).val();
	});
	load_franchise_orders('all');
});
function give_sch_discnt_frm()
{
	$('#sch_hist').dialog('open');
	//load_allbrands();
	//load_allcatgory();
}
$( "#sch_hist" ).dialog({
	modal:true,
	autoOpen:false,
	width:500,
	height:500,
	autoResize:true,
	open:function(){
	dlg = $(this);
	
	},
	buttons:{
		'Cancel' :function(){
		 $(this).dialog('close');
		},
		'Submit':function(){
			var sch_form=$("#sch_form",this);
			if(sch_form.parsley('validate'))
			{
				//sch_form.submit();

				if($("#d_start").val().length==0 || $("#d_end").val().length==0)
				{
					alert("Enter start date and end date");
					return false;
				}

				var sch_disc = $('input[name="discount"]',this).val();
					sch_disc = $.trim(sch_disc)*1;
					if(isNaN(sch_disc))
					{
						alert("invalid discount entered");
						return false;
					}
					if(sch_disc > 20)
					{
						alert("Maximum 10% is allowed for scheme discount");
						return false;
					}	

				$.post(site_url+'/admin/jx_check_schemexist/<?php echo $f['franchise_id']?>',$("#sch_form").serialize(),function(resp){
					if(resp.status == 'error')
					{
						alert(resp.message);
					}else
					{
						$("#sch_form").submit();
						$("#sch_hist").dialog('close');
					} 
				},'json');
			}
			else
			{
				alert("All fileds are required");
			}
		},
	}
});



function load_bankdetails()
{
	$('#bank').dialog('open');
}

$( "#bank" ).dialog({
	modal:true,
	autoOpen:false,
	width:'600',
	height:'auto',
	open:function(){
	dlg = $(this);
	},
	buttons:{
		'Close' :function(){
		 $(this).dialog('close');
		}
	}
});

//------menber list handle------
function members_details(fid)
{
	$('#members').data({'fid':fid}).dialog('open');
}

$( "#members" ).dialog({
	modal:true,
	autoOpen:false,
	width:'700',
	height:'auto',
	open:function(){
		dlg = $(this);
		var fid=$(this).data('fid');
		var html_cnt='';
		$( "#members table" ).remove();	
		load_members(fid,0);
	},
	buttons:{
		'Close' :function(){
		 	$(this).dialog('close');
		}
	}
});

$("#members .m_pg a").live("click",function(e){
	e.preventDefault();
	var link_parts=$(this).attr('href').split('/');
		load_members($( "#members" ).data('fid'),link_parts[link_parts.length-1]*1);
});

function load_members(fid,pg)
{
	$( "#members table" ).remove();
	var html_cnt='';
	$.post(site_url+"/admin/jx_get_members_by_franchise/1/"+fid+'/'+pg,{},function(res){
		if(res.status=='error')
		{
			alert(res.msg);
		}else{
			html_cnt+="<table class='datagrid' width='100%'><thead><tr><th>Member ID</th><th>Name</th><th>City</th><th>Created On</th></tr></thead><tody>";
			$.each(res.members,function(a,b){
				html_cnt+="<tr>";
				html_cnt+="	<td><a href='"+site_url+"admin/pnh_viewmember/"+b.user_id+"' class='link'>"+b.pnh_member_id+"</a></td>";
				html_cnt+="	<td>"+b.first_name+" "+b.last_name+"</td>";
				html_cnt+="	<td>"+b.city+"</td>";
				
				html_cnt+="	<td>"+((b.created_on==0)?'registration form not updated yet':(get_unixtimetodatetime(b.created_on)))+"</td>";	
				html_cnt+="</tr>";
				
			});
			html_cnt+="<tr>";
			html_cnt+="	<td colspan='4' align='right' class='m_pg pagination'>"+res.pagination+"</td>";		
			html_cnt+="</tr>";
			html_cnt+="</tbody></table>";
				
		}
		
		$("#members").append(html_cnt);
			
	},'json');
	
}

//------menber list handle end------

//-----------receipts handle---------
function load_receipts(ele,type,pg,fid,limit)
{
	$($(ele).attr('href')+' div.tab_content').html('<div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
    var cr_tab = 0;
	$.post(site_url+'/admin/jx_pnh_franchise_reports/'+fid+'/'+type+'/'+cr_tab+'/'+limit+'/'+pg*1,'',function(resp){
		$($(ele).attr('href')+' div.tab_content').html(resp.page);
		$($(ele).attr('href')+' div.tab_content .datagridsort').tablesorter();
		
	},'json');
}

$(".receipt_pg a").live('click',function(e){
	e.preventDefault();
	var link_det=$(this).attr('href').split('/');
	var fid=link_det[2];
	var type=link_det[3];
	var cr_tab=link_det[4];
	var pg=link_det[5];
	var limit=link_det[6];
        
    $.post(site_url+'/admin/jx_pnh_franchise_reports/'+fid+'/'+type+'/'+cr_tab+'/'+pg*1+'/'+limit,'',function(resp){
		$("#"+type+' div.tab_content').html(resp.page);
		$("#"+type+' div.tab_content .datagridsort').tablesorter();
		
	},'json');
});

$(".account_statement").click(function(){$(".pending_receipt").trigger('click');});
//-----------receipts handle end---------
function load_scheme_disc_history()
{
	$('#schme_disc_history').dialog('open');
}

$( "#schme_disc_history" ).dialog({
	modal:true,
	autoOpen:false,
	width:'900',
	height:'auto',
	open:function(){
		dlg = $(this);
	},
	buttons:{
		'Close' :function(){
		 $(this).dialog('close');
		}
	}
});


function init_frmap() {
  	$('.fran_menu').chosen();
}
$(function(){
	$('.leftcont').hide();	
});
$('.schmenu').chosen();

var sel_menuid=0;

$(function(){

	$('.select_cat').change(function(){
	//alert($(this).val());
	//sel_menuid=$('.schmenu').val();
	sel_catid=$(this).val();
	if(sel_catid!='0')
	{
		$(".select_brand").html('').trigger("liszt:updated");
		$.getJSON(site_url+'/admin/jx_load_allbrandsbycat/'+sel_catid,'',function(resp){
		var brands_html='';
		if(resp.status=='error')
		{
			alert(resp.message);
		}
		else
		{
			brands_html+='<option value=""></option>';
			brands_html+='<option value="0">All</option>';
			$.each(resp.brand_list,function(i,b){
			brands_html+='<option value="'+b.brandid+'">'+b.name+'</option>';
			});
		}
		 $('.select_brand').html(brands_html).trigger("liszt:updated");
		 $('.select_brand').trigger('change');
		});
	}
/*	else
	{
		load_allbrands();
	}*/
});

$('.schmenu').change(function()
{
	var sel_menuid=$(this).val();
	//var sel_brandid=$(this).val();
	if(sel_menuid!='0')
	{
		$(".select_cat").html('').trigger("liszt:updated");
		$.getJSON(site_url+'/admin/jx_load_allcatsbymenu/'+sel_menuid,'',function(resp){
			var cats_html='';
				if(resp.status=='error')
				{
					alert(resp.message);
				}
				else
				{
					cats_html+='<option value=""></option>';
					cats_html+='<option value="0">All</option>';
					$.each(resp.cat_list,function(i,b){
					cats_html+='<option value="'+b.catid+'">'+b.name+'</option>';
					});
				}
		 	$('.select_cat').html(cats_html).trigger("liszt:updated");
		 	$('.select_cat').trigger('change');
		});
	}

});

});


$( ".fran_tabs a" ).click(function()
{
	window.location.hash = $(this).attr('href');   
	window.scrollTo(0,0); 
});


/*$(".transit_link").click(function(e){
	if(!confirm("Are you sure want change to 'IN Hand' status?"))
	{
		e.preventDefault();
		return false;
	}
	return true;
});*/

$('#r_type').change(function(){
	r=$(this).val();
	if(r=='0')
	{
		$(".inst_type option[value="+1+"]").hide();
	}
	else
	{
		$(".inst_type option[value="+1+"]").show();
	}
});

function change_status(rid)
{
	$('#remarks_changestatus').data('receipt_id',rid).dialog('open');
}

$('#remarks_changestatus').dialog({

	model:true,
	autoOpen:false,
	width:'500',
	height:'330',
	open:function(){
		dlg = $(this);
		$('#transit_rmks input[name="rid"]',this).val(dlg.data('receipt_id'));
		$("#r_receiptid b",this).html(dlg.data('receipt_id'));
		$("#transit_rmks",this).attr('action',site_url+'/admin/pnh_change_receipt_trans_type/'+dlg.data('receipt_id')); 
	},
	buttons:{
		'Submit':function(){
			var transit_rmksfrm = $("#transit_rmks",this);
			 	if(transit_rmksfrm.parsley('validate'))
				{
					$('#transit_rmks').submit();
					$(this).dialog('close');
				}
		       else
		       {
		       		alert('Remarks Need to be addedd!!!');
		       }
		},
		'Cancel':function()
		{
			$(this).dialog('close');
		},
	}
	
});


function give_supersch()
{
	$("#pnh_superschme").dialog('open');
}

$('#fran_misc_logs').tabs();
$('#activity_menu_tabs').tabs();

$("#pnh_superschme").dialog({
	modal:true,
	autoOpen:false,
	width:'500',
	height:'500',
	open:function(){
		
	},
	buttons:{
		'Cancel':function(){
			$(this).dialog('close');
		},
		'Submit':function(){
			var sch_form=$("#super_schform",this);
			if(sch_form.parsley('validate'))
			{
				$.post(site_url+'/admin/jx_check_schemexist/<?php echo $f['franchise_id']?>',$("#super_schform").serialize(),function(resp){
					if(resp.status == 'error')
					{
						alert(resp.message);
						return false;
					}else
					{
						$("#super_schform").data('psubumit',true);
						$("#super_schform").submit();
						$("#pnh_superschme").dialog('close');
					} 
				},'json');
			}
			else
			{
				alert("All fileds are required");
			}
		},
	}
});

function give_membrsch()
{
	$('#pnh_membersch').dialog('open');
}

$("#pnh_membersch").dialog({
	modal:true,
	autoOpen:false,
	width:'500',
	height:'500',
	open:function(){
		
	},
	buttons:{
		'Cancel':function(){
			$(this).dialog('close');
		},
		'Submit':function(){
			var mbr_schfrm=$('#membr_schform');
			if(mbr_schfrm.parsley('validate'))
			{
				$.post(site_url+'/admin/jx_check_mbrschmenu/<?php echo $f['franchise_id']?>',$("#membr_schform").serialize(),function(resp){
					if(resp.status == 'error')
					{
						alert(resp.message);
						return false;
					}else
					{
						$('#membr_schform').data('psubumit',true);
						$('#membr_schform').submit();
						$("#pnh_membersch").dialog('close');
					}
				},'json');
			}
				else
				{
					alert("All fileds are required");
				}
			},
		}
	
	});
</script>

	<script>
		$('input[name="return_on_date"],input[name="return_on_date_end"]').datepicker({});
		
		$('input[name="return_on_date"],input[name="return_on_date_end"]').change(function(){
			$('input[name="return_kwd_srch"]').val('');
			load_return_prods(0);
		});
		
		$('input[name="return_kwd_srch"]').change(function(){
			$('input[name="return_on_date"]').val('');
			$('input[name="return_on_date_end"]').val('');
		});
		
		//$('select[name="order_by_mnth"]').change(function(){
			
			//fran_menu_stat();
		//});
		$("#grid_list_frm_to").bind("submit",function(e){
			$('#payment_stat .payment_stat_view').unbind('jqplotDataClick');
		    e.preventDefault();
		    fran_menu_stat();
		    payment_order_stat();
		    brands_byfranch();
		    return false;
		});
		
		
		 
		function load_all_return_prods(pg)
		{
			$('input[name="return_kwd_srch"]').val('');
			$('input[name="return_on_date"]').val('');
			$('input[name="return_on_date_end"]').val('');
			load_return_prods(pg);
		}
		function load_return_prods(pg)
		{
			$('#return_products .module_cont_block_grid .datagrid tbody').html('<tr><td colspan="8"><div align="center"><img src="'+base_url+'/images/loading_bar.gif'+'"> </div></td></tr>');
			
			var ret_params = {};
				ret_params.fid = "<?php echo $f['franchise_id'] ?>";
				ret_params.return_on = $('input[name="return_on_date"]').val();
				ret_params.return_on_end = $('input[name="return_on_date_end"]').val();
				ret_params.return_srch_kwd = $('input[name="return_kwd_srch"]').val();
				
				if(!(ret_params.return_on && ret_params.return_on_end))
				{
					ret_params.return_on = '';
					ret_params.return_on_end = '';
				}
				
				$('#return_products .module_cont_block_grid_total .total b').text("");
				
			$.post(site_url+'/admin/jx_getreturnprodsbyfid/'+pg,ret_params,function(resp){
				if(resp.status == 'error')
				{
					alert(resp.error)
				}else
				{
					$('#return_products .module_cont_block_grid_total .total b').text(resp.total);
					if(resp.fran_rplist.length == 0)
					{
						$('#return_products .module_cont_block_grid .datagrid tbody').html('<tr><td colspan="12"><div align="center">No Data found</div></td></tr>');			
					}else
					{
						var ret_prodlist_html = '';
							$.each(resp.fran_rplist,function(a,b){
								ret_prodlist_html += '<tr>'
														+'<td>'+(pg+a+1)+'</td>'
														+'<td><a target="_blank" href="'+site_url+'/admin/view_pnh_invoice_return/'+b.return_id+'"><b>'+b.return_id+'</b></a></td>'
														+'<td>'+formatDateTime(new Date(b.created_on*1000))+'</td>'
														+'<td>'+b.return_by+'</td>'
														+'<td>'+b.invoice_no+'</td>'
														+'<td>'+b.order_id+'</td>'
														+'<td style="line-height:20px;"><a href="'+site_url+'/admin/product/'+b.product_id+'"><b>'+b.product_name+'</b></a>  '+(b.barcode?' <br> Barcode :'+b.barcode:'')+' '+(b.imei_no?' <br> IMEINO :'+b.imei_no:'')+' '+' </td>'
														+'<td>'+b.qty+'</td>'
														+'<td>'+resp.return_cond[b.condition_type]+'</td>'
														+'<td>'+resp.return_process_cond[b.status]+'</td>'
														+'<td>'+formatDateTime(new Date(b.remarks.created_on*1000))+'</td>'
														+'<td>'+b.remarks.remark_by+'</td>'
														+'<td>'+b.remarks.remarks+'</td>'
														
													+'</tr>';
							});
						$('#return_products .module_cont_block_grid .datagrid tbody').html(ret_prodlist_html);
						
						$('#return_products .module_cont_grid_block_pagi').html(resp.fran_rplist_pagi);
						
						$('#return_products .module_cont_grid_block_pagi a').unbind('click').click(function(e){
								e.preventDefault();
								
							var link_part = $(this).attr('href').split('/');
							var link_pg = link_part[link_part.length-1]*1;
								if(isNaN(link_pg))
									link_pg = 0;
									
								load_return_prods(link_pg);	
						});
						
					}
				}
			},'json');
		}
		load_return_prods(0);
		
		function load_credit_notes(pg)
		{
			$.post(site_url+'/admin/jx_getfrancreditnotes/'+pg,'fid=<?php echo $f['franchise_id'] ?>',function(resp){
				if(resp.status == 'error')
				{
					alert(resp.error)
				}else
				{
					$('#credit_notes .module_cont_block_grid_total .total b').text(resp.total);
					if(resp.fran_crnotelist.length == 0)
					{
						$('#credit_notes .module_cont_block_grid .datagrid tbody').html('<tr><td colspan="12"><div align="center">No Data found</div></td></tr>');			
					}else
					{
						var crnotelist_html = '';
							$.each(resp.fran_crnotelist,function(a,b){
								crnotelist_html += '<tr>'
														+'<td>'+(pg+a+1)+'</td>'
														+'<td>'+b.credit_note_id+'</td>'
														+'<td><a target="_blank" href="'+site_url+'/admin/invoice/'+b.invoice_no+'"><b>'+b.invoice_no+'</b></a></td>'
														+'<td>'+b.order_id+'</td>'
														+'<td>'+b.credit_note_amt+'</td>'
														+'<td>'+formatDateTime(new Date(b.createdon*1000))+'</td>'
													+'</tr>';
							});
						$('#credit_notes .module_cont_block_grid .datagrid tbody').html(crnotelist_html);
						
						$('#credit_notes .module_cont_grid_block_pagi').html(resp.fran_crnotelist_pagi);
						
						$('#credit_notes .module_cont_grid_block_pagi a').unbind('click').click(function(e){
								e.preventDefault();
								
							var link_part = $(this).attr('href').split('/');
							var link_pg = link_part[link_part.length-1]*1;
								if(isNaN(link_pg))
									link_pg = 0;
									
								load_credit_notes(link_pg);	
						});
						
					}
				}
			},'json');
		}
		
		load_credit_notes(0)

		//console.log(franchise_id).val();
		function  load_voucher_activity(ele,type,franchise_id,pg)
		{
			var franchise_id = '<?php echo $f['franchise_id'];?>';
			
			$($(ele).attr('href')+' div.tab_content').html('<div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
			$.post(site_url+'/admin/jx_getpnh_voucher_activitylog/'+type+'/'+franchise_id+'/'+pg*1,'',function(resp){
				$($(ele).attr('href')+' div.tab_content').html(resp.log_data+resp.pagi_links);
				$($(ele).attr('href')+' div.tab_content .datagridsort').tablesorter();
				
			},'json');
		}

		$("#voucher_tab").click(function(){
				$("#book_orders_tab").trigger("click");
			});

	/*	function load_allshipped_imei(ele,type,franchise_id,pg)
		/*{
			type = 1;
			pg = 0;
			var franchise_id = '<?php echo $f['franchise_id'];?>';
			$('#shipped_imeimobslno div.tab_content').html('<div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
			$.post(site_url+'/admin/jx_load_all_shipped_mobimei/'+type+'/'+franchise_id+'/'+pg*1,'',function(resp){
				$('#shipped_imeimobslno div.tab_content').html(resp.log_data+resp.pagi_links);
				$('#shipped_imeimobslno div.tab_content .datagridsort').tablesorter();
			},'json');
		}*/

		$('input[name="active_ondate"],input[name="active_ondate_end"]').datepicker();

		$('input[name="active_ondate"],input[name="active_ondate_end"],select[name="date_type"]').change(function(){
			if($('input[name="active_ondate"]').val() != '' && $('input[name="active_ondate_end"]').val() != '')
			{
				load_shipped_imei(0);
				$('input[name="imei_srch_kwd"]').val('');
			}
		});

		$('select[name="imei_status"]').change(function(){
				load_shipped_imei(0);
				$('input[name="imei_srch_kwd"]').val('');
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
		
			imei_params.fid = "<?php echo $f['franchise_id'] ?>";
			imei_params.date_type = $('select[name="date_type"]').val();
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


		function reson_forsuspenfran(fid)
		{
			$("#fran_suspend").data('franchise_id',fid).dialog('open');
		}
		
		$("#fran_suspend").dialog({
			modal:true,
			autoOpen:false,
			width:'519',
			height:'300',
			open:function(){
				var dlg=$(this);
				$('.credit_edit').hide();
				$('#suspend_reasonfrm input[name="franchise_id"]',this).val(dlg.data('franchise_id'));
				$('#suspend_reasonfrm select[name="sus_type"]',this).trigger('change');
				//$("#r_receiptid b",this).html(dlg.data('receipt_id'));
				$("#suspend_reasonfrm",this).attr('action',site_url+'/admin/pnh_suspend_fran/'+dlg.data('franchise_id'));
			},
		buttons:{
			'Submit':function(){
				var dlg= $(this);
				var frm_fransuspend = $("#suspend_reasonfrm",this);
					 if(frm_fransuspend.parsley('validate')){

						 frm_fransuspend.submit();
						 $("#fran_suspend").dialog('close');
					}
		            else
		            {
		            	
		            }
			},
			'Cancel':function(){
				$(this).dialog('close');
			}
		}
		});

		function reson_forunsuspension(fid)
		{
			$("#unsuspend_fran").data('unsuspend_fid',fid).dialog('open');
		}

		$("#unsuspend_fran").dialog({
			modal:true,
			autoOpen:false,
			width:'500',
			height:'300',
			open:function(){
				var dlg=$(this);
				$('#unsuspend_reasonfrm input[name="unsuspend_fid"]',this).val(dlg.data('unsuspend_fid'));
				$("#unsuspend_reasonfrm",this).attr('action',site_url+'/admin/pnh_unsuspend_fran/'+dlg.data('unsuspend_fid'));
			},
			buttons:{
			'Submit':function(){
				var unsuspendfran_form=$("#unsuspend_reasonfrm",this);
				if(unsuspendfran_form.parsley('validate')){
					unsuspend_reasonfrm.submit();
					$("#unsuspend_fran").dialog('close');
				}
				else
				{
					
				}
			},
			'Cancel':function(){
				$(this).dialog('close');
				}
			}

		});
		
		function payment_order_stat()
		{
			var start_date=$('#frm').val();
    		var end_date=$('#to').val();
			var franid = "<?php echo $this->uri->segment(3);?>";
			$('.head_wrap').html("Orders & Payments summary for period of "+start_date+" "+end_date);
			$('#payment_stat .payment_stat_view').html('<div class="anmtd_loading_img"><span></span></div>'); 
			$.getJSON(site_url+'/admin/jx_order_payment_det/'+start_date+'/'+end_date+'/'+franid,'',function(resp)
	    	{
	    		if(resp.summary == 0 && resp.payment == 0 && shipped==0)
				{
					$('#payment_stat .payment_stat_view').html("<div class='fr_alert_wrap' style='padding:113px 0px'>No Sales statisticks found between "+start_date+" and "+end_date+"</div>" );	
				}
				else
				{
					<?php if($this->erpm->auth(true,true)){?>
					// reformat data ;
					$('#ttl_order_amt').html("Total Ordered : "+resp.ttl_summary);
					$('#paymrent_order_amt').html("Total Paid : "+resp.ttl_payment);
					$('#shipped_order_amt').html("Total Shipped : "+resp.ttl_shipped);
					<?php }else{
					?>
					$('#ttl_order_amt').html('').hide();
					$('#paymrent_order_amt').html('').hide();
					$('#shipped_order_amt').html('').hide();
					<?php } ?>

					$('#ttl_order_amt').html('').hide();
					$('#paymrent_order_amt').html('').hide();
					$('#shipped_order_amt').html('').hide();
					
					 var types = ['Order Placed','shipped', 'Cheque Date','Cash in Bank'];
					$('#payment_stat .payment_stat_view').empty();
					var summary=resp.summary;
					var payment=resp.payment;
					var shipped=resp.shipped;
					var realized=resp.realized;
					plot2 = $.jqplot('payment_stat .payment_stat_view', [summary,shipped,payment,realized], {
				       	seriesDefaults: {
							showMarker:true,
							pointLabels: { show:true }
						},
					     series:[
					     			{showMarker:true,pointLabels: { show:true }},
					     			{showMarker:true,pointLabels: { show:true }},
					     			{showMarker:true,pointLabels: { show:true }},
					     			{showMarker:true,pointLabels: { show:true }},
					     		],
				      axesDefaults: {
					        tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
					        tickOptions: {
					          fontFamily: 'tahoma',
					          fontSize: '11px',
					          angle: -30
					        }
					    },
					    legend: {
					    	//renderer: $.jqplot.EnhancedLegendRenderer,
			                show: true,
			                location: 'ne',
			                placement: 'inside',
			                labels: types
			            },
				        axes:{
					        xaxis:{
					          renderer: $.jqplot.CategoryAxisRenderer,
					          ticks:resp.ticks,
					          	label:'Date',
						          labelOptions:{
						            fontFamily:'Arial',
						            fontSize: '14px'
						          },
						          labelRenderer: $.jqplot.CanvasAxisLabelRenderer
					        },
					        yaxis:{
						          min : 0,
								  label:'Sales & Payment in Rs',
						          labelOptions:{
						            fontFamily:'Arial',
						            fontSize: '14px'
						          },
						          labelRenderer: $.jqplot.CanvasAxisLabelRenderer
						        }
					      }
					});	
					
					$('#payment_stat .payment_stat_view').data('jqplotObj',plot2);
					$('#payment_stat .payment_stat_view table.jqplot-table-legend tr.jqplot-table-legend').live('click',function(){
						var indx = $('#payment_stat .payment_stat_view table.jqplot-table-legend tr.jqplot-table-legend').index(this);
						var jqplotObj =$('#payment_stat .payment_stat_view').data('jqplotObj');
						
							if($('#payment_stat .payment_stat_view').data('selectedSeries') == undefined)
								$('#payment_stat .payment_stat_view').data('selectedSeries',[true,true,true,true]);
								
							var serSelArr = $('#payment_stat .payment_stat_view').data('selectedSeries');
								if(serSelArr[indx])
									serSelArr[indx] = false;
								else
									serSelArr[indx] = true;
								 
								 $('#payment_stat .payment_stat_view').data('selectedSeries',serSelArr);
					        
					        jqplotObj.options.series[indx].showLine=serSelArr[indx];
					        jqplotObj.options.series[indx].showMarker=serSelArr[indx];
					        jqplotObj.options.series[indx].pointLabels.show=serSelArr[indx];
					        jqplotObj.replot(jqplotObj.options);
					});
					
					$('#payment_stat .payment_stat_view').bind('jqplotDataClick', function(ev,seriesIndex,pointIndex,data) {
						if(seriesIndex == 0)
						{
							var date = summary[pointIndex][2];
							var amt = summary[pointIndex][1];
							ord_det(date,amt,franid);
						}
					});
				}	
		    });
		}
		
		function ord_det(date,amt,franid)
		{
			$.post(site_url+'/admin/jx_order_det_franchise_id/',{date:date,franid:franid},function(resp){
			if(resp.status == 'error')
			{
				alert(resp.error);
			}else
			{
				 var list = '';
				 list += '<h4>Order Details on '+date+'<span style="float:right">Total Value : '+amt+'</span></h4>';
				 list +='<span class="popclose_button b-close"><span>X</span></span><div style="overflow:auto;float:left;height:265px;width:600px;">';
				 list += '<table class="datagrid" width="100%"><thead><tr><th>Sl.No</th><th>Product Name</th><th>Brand Name</th><th>Quantity</th><th>Amount</th></tr></thead><tbody>';
					$.each(resp.ord_det,function(a,b){
						list += '<tr>'
												+'<td>'+(++a)+'</td>'
												+'<td>'+b.product_name+'</td>'
												+'<td>'+b.name+'</td>'
												+'<td>'+b.q+'</td>'
												+'<td>'+b.total_value+'</td>'
											+'</tr>';
					});
				list += '</tbody></table></div>';
				$('#fr_det_popup').html(list);
				
			}
			},'json');
			$('#fr_det_popup').bPopup({
                            easing: 'easeOutBack', //uses jQuery easing plugin
                                speed: 450,
                                transition: 'slideDown'
			});
		}
		
		
		function fran_menu_stat()
		{
			var start_date=$('#frm').val();
    		var end_date=$('#to').val();
			var franid = "<?php echo $this->uri->segment(3);?>";
			
			$.getJSON(site_url+'/admin/jx_order_getsales_bymenu/'+franid+'/'+start_date+'/'+end_date,'',function(resp){
				// reformat data ;
				$('#fr_order_stat .order_piestat_view').empty();
				var resp = resp.result;
				plot3 = jQuery.jqplot('fr_order_stat .order_piestat_view', [resp], 
				{
					seriesDefaults:{
			            renderer: jQuery.jqplot.PieRenderer,
			            pointLabels: { show: true },
		                rendererOptions: {
		                    // Put data labels on the pie slices.
		                    // By default, labels show the percentage of the slice.
		                    showDataLabels: true,
		                  }
			        },
			        highlighter: {
					    show: true,
					    useAxesFormatters: false, // must be false for piechart   
					    tooltipLocation: 's',
					    formatString:'Menu : %s'
					},
					grid: {borderWidth:0, shadow:false,background:'#ccc'},
			        legend:{show:true}
			        
			    });
			    $('#fr_order_stat .order_piestat_view').bind('jqplotDataClick', function(ev,seriesIndex,pointIndex,data) {
			    	$('.fr_menu_by_mn').show();
			    	$('#menu_det_tab').show();
			    	var menu_id = resp[pointIndex][2];
			    	var menu_name = resp[pointIndex][0];
			    	
			    	 //top sold products list for selected menu
					 prods_bymenu(menu_id,menu_name);
					 
					 // top sold brands for selected menu
					 brands_bymenu(menu_id,menu_name);
			    });
			});
		}
		
		function brands_byfranch()
		{
			var franid = "<?php echo $this->uri->segment(3);?>";
			var fran_name="<?php echo $f['franchise_name']?>";
			var start_date=$('#frm').val();
    		var end_date=$('#to').val();
    		$('#top_brand_bymenu_stat .fr_brand_stat_view').html('<div class="bar"><span></span></div>'); 
							
			$.getJSON(site_url+'/admin/jx_brandsbyfranid/'+franid+'/'+start_date+'/'+end_date,'',function(resp){
			$('#top_brand_bymenu_stat .stat_head_wrap').html("Top Brands for "+fran_name);
			if(resp.summary == 0)
			{
				$('#top_brand_bymenu_stat .fr_brand_stat_view').html("<div class='fr_alert_wrap' style='padding:113px 10px'>No brands ordered from "+start_date+" to "+end_date+"</div>")	
			}
			else
			{
				// reformat data ;
				$('#top_brand_bymenu_stat .fr_brand_stat_view').empty();
				plot2 = $.jqplot('top_brand_bymenu_stat .fr_brand_stat_view', [resp.summary], {
			       	 seriesDefaults:{
			            renderer:$.jqplot.BarRenderer,
			            rendererOptions: {
			                // Set the varyBarColor option to true to use different colors for each bar.
			                // The default series colors are used.
			                varyBarColor: true
			            },pointLabels: { show: true }
			        },
				    axesDefaults: {
				        tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
				        tickOptions: {
				          fontFamily: 'tahoma',
				          fontSize: '11px',
				          angle: -30
				        }
				    },
				    axes:{
				        xaxis:{
				          renderer: $.jqplot.CategoryAxisRenderer,
				          	label:'Brands',
					          labelOptions:{
					            fontFamily:'Arial',
					            fontSize: '14px'
					          },
					          labelRenderer: $.jqplot.CanvasAxisLabelRenderer
				        },
				        yaxis:{
					          
							  label:'Total Sales in Rs',
					          labelOptions:{
					            fontFamily:'Arial',
					            fontSize: '14px'
					          },
					          labelRenderer: $.jqplot.CanvasAxisLabelRenderer
					        }
				      }
					});
					$('#top_brand_bymenu_stat .fr_brand_stat_view').bind('jqplotDataClick', function(ev,seriesIndex,pointIndex,data) {
				    	
				    });
				}
			});
		}
		
		function brands_bymenu(id,name)
		{
			var franid = "<?php echo $this->uri->segment(3);?>";
			var start_date=$('#frm').val();
    		var end_date=$('#to').val();
			$('#top_brand_bymenu_stat .fr_brand_stat_view').html('<div class="bar"><span></span></div>'); 
			$.getJSON(site_url+'/admin/jx_brandsbymenuid/'+id+'/'+franid+'/'+start_date+'/'+end_date,'',function(resp){
			$('#top_brand_bymenu_stat .stat_head_wrap').html("Brands of "+name+" menu ");
			if(resp.summary == 0)
			{
				$('#top_brand_bymenu_stat .fr_brand_stat_view').html("<div class='fr_alert_wrap' style='padding:113px 10px'>No brands for "+name+" menu</div>")
			}
			else
			{
				// reformat data ;
				$('#top_brand_bymenu_stat .fr_brand_stat_view').empty();
				plot2 = $.jqplot('top_brand_bymenu_stat .fr_brand_stat_view', [resp.summary], {
			       	 seriesDefaults:{
			            renderer:$.jqplot.BarRenderer,
			            rendererOptions: {
			                // Set the varyBarColor option to true to use different colors for each bar.
			                // The default series colors are used.
			                varyBarColor: true
			            },pointLabels: { show: true }
			        },
				    axesDefaults: {
				        tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
				        tickOptions: {
				          fontFamily: 'tahoma',
				          fontSize: '11px',
				          angle: -30
				        }
				    },
				    axes:{
				        xaxis:{
				          renderer: $.jqplot.CategoryAxisRenderer,
				          	label:'Brands',
					          labelOptions:{
					            fontFamily:'Arial',
					            fontSize: '14px'
					          },
					          labelRenderer: $.jqplot.CanvasAxisLabelRenderer
				        },
				        yaxis:{
					          
							  label:'Total Sales in Rs',
					          labelOptions:{
					            fontFamily:'Arial',
					            fontSize: '14px'
					          },
					          labelRenderer: $.jqplot.CanvasAxisLabelRenderer
					        }
				      }
					});
					$('#top_brand_bymenu_stat .fr_brand_stat_view').bind('jqplotDataClick', function(ev,seriesIndex,pointIndex,data) {
				    	
				    });
				}
			});
		}
		
		function prods_bymenu(id,name)
		{
			var franid = "<?php echo $this->uri->segment(3);?>";
			var start_date=$('#frm').val();
    		var end_date=$('#to').val();
    		$('.fr_top_sold').html('<div class="bar"><span></span></div>'); 
			$.post("<?=site_url("admin/jx_prod_det_bymenu")?>",{menu_id:id,franid:franid,start_date:start_date,end_date:end_date},function(resp){
				$('#menu_det_tab .stat_head_wrap').html("Top sold Products of "+name+" menu ");
					var topsold_prod_list="";
				 
				 	if(resp.status == 'error')
				 	{
				 		topsold_prod_list +='<span>'+resp.message+'</span>';
				 		$('.fr_top_sold').html(topsold_prod_list);
				 	}
				 	else
				 	{
				 		topsold_prod_list +='<table class="datagrid" width="100%"><thead><tr><th>Product Name</th><th>Date</th><th>Qty Sold</th><th>Total Value</th></tr></thead><tbody>';
				 		$.each(resp.top_prod_list,function(i,p){
							topsold_prod_list +='<tr><td><a target="blank" href="'+site_url+'/admin/product/'+p.product_id+'">'+p.product_name+'</a></td><td>'+p.d+'</td><td>'+p.qty_sold+'</td><td>'+p.total_value+'</td></tr>';	
						});
				 		topsold_prod_list +='</tbody></table>';
				 		$('.fr_top_sold').html(topsold_prod_list);
				 	}
			 },'json');
		}
		
		function mark_prepaid_franchise(fid,flag)
		{
			$("#mark_prepaid_franchise").data('franchise_id',fid,'p_flg',flag).dialog('open');
		}
		
		$("#mark_prepaid_franchise").dialog({
			modal:true,
			autoOpen:false,
			width:'519',
			height:'300',
			open:function(){
				var dlg=$(this);
				if(dlg.data('p_flg'))
					$('#mark_prepaid_franchise').dialog('option', 'title','Unmark prepaid reason');
				else
					$('#mark_prepaid_franchise').dialog('option', 'title','Mark prepaid reason');
				
				$('#mark_prepaid_franchise_form input[name="prepaid_franchise_id"]',this).val(dlg.data('franchise_id'));
				$("#mark_prepaid_franchise_form",this).attr('action',site_url+'/admin/mark_prepaid_franchise/'+dlg.data('franchise_id'));
			},
		buttons:{
			'Submit':function(){
				var dlg= $(this);
				var prepaid_reson = $("#mark_prepaid_franchise_form",this);
					 if(prepaid_reson.parsley('validate')){

						 prepaid_reson.submit();
						 $("#mark_prepaid_franchise").dialog('close');
					}
		            else
		            {
		            	alert('All Fields are required!!!');
		            }
			},
			'Cancel':function(){
				$(this).dialog('close');
			}
		}
		});
	 
					$('#dlg_add_security_cheque_det').dialog({
																width:400,
																height:'auto',
																autoOpen:false,
																modal:true,
																buttons:{
																	'Add':function(){
																		var dlg = $('#dlg_add_security_cheque_det');
																		
																		var f_schq_bankname = $('input[name="f_schq_bankname"]',dlg).val();
																		var f_schq_no = $('input[name="f_schq_no"]',dlg).val();
																		var f_schq_date = $('input[name="f_schq_date"]',dlg).val();
																		var f_schq_amt = $('input[name="f_schq_amt"]',dlg).val();
																		var f_schq_colon = $('input[name="f_schq_colon"]',dlg).val();
																		
																		var error_str = new Array();
																			
																			if(!f_schq_bankname.length)
																				error_str.push("Please enter bankname");
																			if(!f_schq_no.length)
																				error_str.push("Please cheque no");
																			if(!f_schq_date.length)
																				error_str.push("Please cheque date");
																			if(!f_schq_amt.length)
																				error_str.push("Please cheque amount(Rs)");
																			if(!f_schq_colon.length)
																				error_str.push("Please enter cheque collected date");
																			
																			if(error_str.length)
																			{
																				alert(error_str.join("\r\n"));
																			}else
																			{
																				$.post(site_url+'/admin/jx_add_security_chqdet',$('form',dlg).serialize(),function(resp){
																					if(resp.status == 'error')
																					{
																						alert(resp.error);
																					}else
																					{
																						
																						$('#security_cheques table tbody').append('<tr><td>'+$('#security_cheques table tbody tr').length+'</td><td>'+f_schq_no+'</td><td>'+f_schq_bankname+'</td><td>'+f_schq_date+'</td><td>'+f_schq_amt+'</td><td>'+f_schq_colon+'</td></tr>');
																						dlg.dialog('close');
																					}
																				},'json');
																			}
																	}
																}
														});
					function load_add_security_cheque_dlg()
					{
						$('#dlg_add_security_cheque_det').dialog('open');
					}
					
					$('input[name="f_schq_date"],input[name="f_schq_colon"]').datepicker({dateFormat:'yy-mm-dd'});
					
		function tgl_addcalllog_msg(ele)
		{
			if($(ele).hasClass('show_addmsgfrm'))
			{
				$(ele).removeClass('show_addmsgfrm');
				$(ele).text('Add Message');
				$(ele).removeClass('close_btn');
				$(ele).removeClass('fl_right');
			}else
			{
				$(ele).addClass('show_addmsgfrm');
				$(ele).addClass('close_btn');
				$(ele).addClass('fl_right');
				$(ele).text('X');
			}
			
			$("form",$(ele).parents('td:first')).toggle();
			
		}
		function load_recent_calllog(url,fid)
		{
			if(url)
				req_url = url;
			else
				req_url = site_url+'/admin/jx_get_franchise_calllog/'+fid;
				
			$.post(req_url,'',function(resp){
					if(resp.status == 'error')
					{
						$('#recent_call_log tbody').html("<tr><td colspan='3' align='center'>No Data found</td></tr>");
						$('#recent_call_log #call_log_pagi').html("");
					}else
					{
						var html = '';
						$.each(resp.call_log_list,function(i,det){
								html += '<tr>';
								html += '	<td width="140">'+(det.created_on)+'</td>';
								html += '	<td width="150" style="text-transform:capitalize">'+det.name+'</td>';
								html += '	<td> '+(det.msg.length?'<p style="margin:0px;padding:5px;background:#fdfdfd">'+det.msg+'</p>':'')+' ';
								html += '		<div style="padding:0px;" ><a href="javascript:void(0)" class="" style="font-size: 85%;" onclick=tgl_addcalllog_msg(this)  >Add Message</a>';
								html += '		<form method="post" style="display: none;" action="'+site_url+'/admin/pnh_update_call_log/'+det.id+'" >';
								html += '		<textarea name="msg" style="width:99%;height:60px;clear:both">'+det.msg+'</textarea>';
								html += '		<input type="submit" value="Submit">';
								html += '	</form></div></td>';
								html += '</tr>';	
						});
						$('#recent_call_log tbody').html(html);
						$('#recent_call_log #call_log_pagi').html(resp.call_log_pagi);
						
						$('#call_log_pagi a').unbind('click').click(function(e){
							e.preventDefault();
							load_recent_calllog($(this).attr('href'));
						});
					}
			},'json');
		}
		load_recent_calllog('',<?php echo $f['franchise_id'];?>);
			
	
</script>

<style type="text/css">
	.jqplot-table-legend{z-index: 99999;cursor: pointer}
	#ui-datepicker-div{z-index: 99999999 !important}
</style>

<script>
// <![CDATA[
    var franchise_id = "<?=$franchise_id;?>";
    var frn_version_url = "<?=site_url("admin/pnh_fran_ver_change/{$fran['franchise_id']}")?>";
    var f_created_on = "<?php echo date('m/d/Y',$f['created_on'])?>";
    var franchise_name = "<?php echo $f['franchise_name']?>";
// ]]>
</script>
<script type="text/javascript" src='<?=base_url()."/js/pnh_franchise_reconcile.js"; ?>'></script>
 
<?php
	