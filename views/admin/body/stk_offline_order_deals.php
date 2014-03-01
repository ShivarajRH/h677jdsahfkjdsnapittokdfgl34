<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/stock_intake.css">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/plot.css">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/deals.css">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/stk_offline_order.css">
<link rel='stylesheet' type='text/css' href="<?php echo base_url().'css/fullcalendar.css'?>">
<script type='text/javascript' src="<?php echo base_url().'js/fullcalendar.min.js'?>"></script>

<?php 
$has_fid=0;
$fid=$this->uri->segment(3); 
$mid=$this->uri->segment(4);
if($fid!='')
{
$has_fid=1;
$fdetails=$this->db->query("select * from pnh_m_franchise_info where franchise_id=?",$fid)->row_array();
$franid=$fdetails['franchise_id'];
$acc_statement = $this->erpm->get_franchise_account_stat_byid($franid);
$pending_payment = format_price($acc_statement['shipped_tilldate']-($acc_statement['paid_tilldate']+$acc_statement['acc_adjustments_val']+$acc_statement['credit_note_amt']));
$uncleared_payment = format_price($acc_statement['uncleared_payment']);
$fran_crdet = $this->erpm->get_fran_availcreditlimit($franid);
$current_balance = $fran_crdet[3];
$ttl_cart_items_saved=$this->db->query("select count(*) as ttl from pnh_api_franchise_cart_info where status=1 and franchise_id=? and member_id=?",array($franid,$mid))->row()->ttl;
if($mid!=0)
	$mid_entrytype=0;
else 
	$mid_entrytype=1;
}
$fran_status=$fdetails['is_suspended'];
$fr_reg_diff = ceil((time()-$fdetails['created_on'])/(24*60*60));
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

$fran_status_arr=array();
$fran_status_arr[0]="Live";
$fran_status_arr[1]="Permanent Suspension";
$fran_status_arr[2]="Payment Suspension";
$fran_status_arr[3]="Temporary Suspension";
?>
<div class="container">
	<div style="position:absolute;top:0px;right:7px;"><a href="javascript:void(0)" class="ttl_menu_btn" onclick='tgl_menubar(this)'>Show Menu</a></div>
		<h2 style="float: left;margin:0px;"><a href="<?php echo site_url("admin/pnh_franchise/$franid")?>" target="_blank"><?php echo $fdetails['franchise_name']?></a></h2>&nbsp;<a href="javascript:void(0)" style="font-size:11px;" id="change_fran">change franchise</a>
		<span style="font-size: 11px;align:center;">
			Status:
			<b class="level_wrapper" style="background-color:<?php echo $fr_status_color?>;">
				<?php echo $fran_status_arr[$fran_status];?>
			</b>|
				
			<b class="level_wrapper" style="margin-left:2px;background-color:<?php echo $fr_reg_level_color;?>;">
					 <?php echo $fr_reg_level;?>
			</b>
		</span>
		<div class="filters_wrap"><img class="search_img_wrap" src="<?php echo base_url().'images/search_icon.png'?>">
			<input type="text" name="srch_deals" class="deal_prd_blk inp" placeholder="Search by Deal Name" >
		</div>
		
		<div class="fran_credit_detwrap">
			<span><a class="action_wrap" onclick="load_scheme_details()" href="javascript:void(0)">Scheme & Menu Details</a></span>
			<div id="show_scheme_details" title="Scheme Details" style="display: none;">
				<ul >
					<li><a href="#fran_menu">Menu</a></li>
					<li><a href="#fran_schdisc">Scheme Discount</a></li>
					<li><a href="#fran_suprsch">Super Scheme</a></li>
					<li><a href="#fran_imeisch">IMEI Scheme</a></li>
				</ul>
				<div id="fran_menu">
					<fieldset>
						<legend><b>Alloted Menu</b></legend>
						<table class="datagrid  noprint" id="menu">
							<tbody></tbody>
						</table>
					</fieldset>
				</div>
				
				
				<div id="fran_schdisc">
					<fieldset>
						<legend><b>Active Scheme Discounts</b></legend>
						<table class="datagrid  noprint" id="active_scheme_discount">
							<thead><th>Menu</th><th>Brand</th><th>Category</th><th>Discount(%)</th><th>Valid From</th><th>Valid To</th></thead>
							<tbody></tbody>
						</table>
					</fieldset>
				</div>
				
				<div id="fran_suprsch">
					<fieldset>
						<legend><b>Active Super Scheme</b></legend>
						<table class="datagrid  noprint" id="active_super_scheme">
							<thead><th>Menu</th><th>Brand</th><th>Category</th><th>Target Sales</th><th>Credit(%)</th><th>Valid From</th><th>Valid To</th></thead>
							<tbody></tbody>
						</table>
					</fieldset>
				</div>
				
				<div id="fran_imeisch">
					<fieldset>
						<legend><b>Active IMEI Scheme</b></legend>
						<table class="datagrid  noprint" id="active_imei_scheme">
							<thead><th>Menu</th><th>Brand</th><th>Category</th><th>Scheme Type</th><th>Credit value</th><th>Valid From</th><th>Applied From</th><th>Valid To</th></thead>
							<tbody></tbody>
						</table>
					</fieldset>
				</div>
			</div>

			<div class="cart-container">
				<div class="cart-btn-cont">
					<a href="" class="btn btn-blue btn-cart">
						<span class="cart-icon" style="padding-left: 5px"></span>
						<span class="cart-label">Cart </span>
						<span class="fk-inline-block cart-count" id="item_count_in_cart_top_displayed"><?php echo $ttl_cart_items_saved ? $ttl_cart_items_saved:0 ?></span>
					</a>
				</div>
			</div> 
		</div>
	<div id="cus_jq_alpha_sort_wrap"></div>
		
	<div id="cart_prod_div" title="<?php echo $fdetails['franchise_name']?>" width="100%">
		
			<h5><div class="fran_bal" width="100%">
							<div  width="50%">
							<span>Courier :</span>
								<?php
	                                   $courier = $this->erpm->get_fran_courier_details($franid);
                                            if($courier === false) {
                                             echo 'Not set.'; ?>
                                              <?php }
                                        else {
								?>			
                              <span>
                                   <b><?=$courier["c1"]['courier_name']?> / </b>
                                   <b><?=$courier["c1"]['delivery_hours_1']?> Hrs</b> / 
                                   Deliver <b><?php echo $this->erpm->get_delivery_type_msg($courier["c1"]['delivery_type_priority1']); ?></b>
                               </span>
                                  <?php } ?>
				</div>
				<div style="float:right;" width="20%">
					<span>Available Limit : </span><span>Rs.<?php echo format_price($current_balance);?></span>
				</div>
		</div></h5><br>
	<div class="clear"></div>
		<div style="float: right;margin:0 11px -30px 0;" id="fran_note_edit_div"><a href="javascript:void(0)" class="button button-rounded button-action button-small" onclick="franchise_note(<?php echo $fid?>)">Transaction Note</a></div>	
		<ul style="clear:both;">
			<li><a href="#cart_items"><b>Items In Cart</b></a></li>
			<li><a href="#analytics" class="analytics">Franchise Analytics</a></li>
			<li><a href="#ship_log" class="ship_log">Shipped &amp; Delivered Log</a></li>
		</ul>
		<div id="cart_items">
			<form method="post" id="order_form" autocomplete="off">
				<input type="checkbox" id="redeem_r" name="redeem" value="1" style="display:none;">
				<input type="hidden" id="redeem_p" name="redeem_points" value="0" style="display:none;">
				<input type="hidden"  name="creditdays" value="" style="display:none;">
				<input type="hidden" name="frannote" value="" style="display:none;">
				
				<div style="clear:both;overflow: hidden;background: #fcfcfc">
					<b class="ordr_fortext">Order For : </b><div id="member_ids" class="membr_block"></div>
					<div style="margin-top: 4px;"><span class="change_mber_link_wrap"><a href='javascript:void(0)' onclick='change_member()'><b>Change member</b></a></span></div>
					<div id="mem_fran"></div>
				</div>
				<div class="cart-dialog-header">
					<table id="cart_prod_temp"  width="100%" cellpadding="0" cellspacing="0">
						<input type="hidden" name="fid" id="i_fid" value="<?php echo $fid;?>">
						<input  type="hidden" class="mid" name="mid" size=18 value="<?php echo $mid?>">
						<input  type="hidden" class="mid" name="mid_entrytype" size=18 value="<?php echo $mid_entrytype?>">
							<thead>
								<tr>
									<th width="3%">Sno</th><th style="text-align:left !important">Product Name</th><th width="10%">MRP<br>(Rs)</th><th width="6%">Offer price/<br>DP price<br>(Rs)</th>
									<th width="8%">Discount <br>(Rs)</th><th width="8%">Landing Price<br>(Rs)</th>
									<th width="9%">Qty</th>
									<th width="7%">Sub Total <br> (Rs)</th><th width="3%">Actions</th>
								</tr>
							</thead>
						<tbody></tbody>
					</table>
				</div>
				<div class="cart-footer">
					<div style="float:left;margin-top: -12px;padding: 2px 0 0 6px;display: none;"><span class="red_star"  style="display: none;">*</span><span  style="display: none;"><b>Credit Days : </b></span><span style="display: none;"><input type="text" value="0" size="4" class="credit_days" style="border: 2px solid #000000;width: 50px;"></span></div>
					<div class="cart_estimated_ttl_wrap">Estimated Total :Rs. <span id="cart_totl"></span></div>
					<div class="clear"></div>
					<div  style="<?php echo $mid?'':'display:none';'background-color:none repeat scroll 0 0 #E5F2FF';'width:100%'?>"> 
					<h4 class="module_title">Redeem loyalty points</h4> 
					<?php $mpointsr=$this->db->query("select points,concat(first_name,' ',last_name) as m_name from pnh_member_info where pnh_member_id=?",$mid)->row_array(); $mpoints=0; if(!empty($mpointsr)) $mpoints=$mpointsr['points'];?>
							<table class="datagrid noprint" width="100%">
							
							<thead>
								<th>MemberID</th>
								<th>Name</th>
								<th>Points</th>
							</thead>
							<tbody>
								<tr>
									<td><?php echo $mid ?></td>
									<td><b><?=$mpointsr['m_name']?></b></td>
									<td>
										<div style="padding:5px;background: #FFF">
										<b><?=$mpoints?></b> Available 
										<br />
										<?php if($mpoints>=150){?>
												<span id=""><input type="checkbox" id="redeem_cont" name="redeem" value="1"></span>Redeem <input class="redeem_points" type="text" class="inp" size=4 name="redeem_points" value="150" disabled="disabled"> (max. 150)
											<?php }else echo 'Minimum of 150 points required to redeem';?>
										</div>	
									</td>
								</tr>
							</tbody>
							</table>
						</div>
				</div>
			</form>
		</div>
		<div id="analytics">
			<?php /*?>
			<div class="fran_credit_detwrap" >
				<span>Credit Limit : <b id="fran_credit"></b></span>
				<span>Available Limit : <b id="fran_balance"></b></span>
				<span>Activated Members : <b class="total_mem"></b></span>
				<span>Orders : <b class="total_ord"></b></span>
				<span>Last OrderedOn : <b class="last_ord"></b></span>
			</div>
			<?*/?>
			<table width="100%">
				<tr>
					<td width="70%">
						<div style="float:left;width:100%">
							<span id="ttl_order_amt"></span>
							<span id="shipped_order_amt"></span>
							<span id="paymrent_order_amt"></span>
						</div>
					</td>
					<td width="30%">
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
		
		<!-- Ship Log block Start-->
		<div id="ship_log">
			<div class='shipment_log' style="margin:10px;clear:both"></div>
		</div>
		<div id="ship_log_dlg" >
			<div id="ship_log_dlg_wrap"></div>
		</div>
		<div id="delivery_log_dlg" >
			<div id="delivery_log_dlg_wrap"></div>
		</div>
		<div id="ordered_log_dlg" >
			<div id="order_log_dlg_wrap"></div>
		</div>
	
		<!-- Ship Log block end-->
	</div>
	 <div id="reg_mem_dlg" title="Instant Member Registration" style="display:none;">
		<form id="reg_mem_frm" action="<?php echo site_url('admin/jx_reg_newmem')?>" method="post">
			<input type="hidden" name="franchise_id" value="<?php echo $fid;?>" id="memreg_fid">
			<table>
				<tr><td>Member Name</td><td>:<span class="red_star">*</span></td><td><input type="text" name="memreg_name" id="memreg_name" ></td></tr>
				<tr><td>Mobile Number</td><td>:<span class="red_star">*</span></td><td><input type="text" name="memreg_mobno" id="memreg_mobno" data-required="true" maxlength="10"></td></tr>
			</table>
		</form>
	</div>

	<table id="template" style="display:none">
		<tbody>
			<tr style="border-bottom:2px solid #000;" pid="%pid%"  pimage="%pimage% %pid%" pname="%pname%" mrp="%mrp%" price="%price%" lcost="%lcost%" margin="%margin%" >
				<td>%sno%</td>
				<td style="padding:10px 0px 0px;background: #FFF;">
					<div  class="img_wrap">
						<img alt="" height="100" src="<?=IMAGES_URL?>items/small/%pimage%.jpg"><b style="display: block;background: #f7f7f7;padding:2px;width:95px;text-align: center;">%pid%</b>
						<input class="pids" type="hidden" name="pid[]" value="%pid%">
					</div>
					<div  class="prod_detwrap" >
						<input type="hidden" name="menu[]" value="%menuid%" class="menuids">
						<span class="title"><a href="<?=site_url("admin/pnh_deal")?>/%pid%" target="_blank">%pname%</a></span>
						<div class="p_extra pcart_extra"><b>Category : </b><span>%cat%</span> </div>
						<div class="p_extra pcart_extra"><b>Brand : </b><span> %brand%</span></div>
						<div class="p_stk pcart_extra"><b>Stock : </b> <span>%stock%</span></div>
						<div class="p_attr pcart_extra">%confirm_stock%</div>
						<div class="p_attr pcart_extra">%attr%</div>
					</div>
					<span></span>
					<div class="sch_wrap">%super_sch%</div>
				</td>
				<td style="text-align: center;" valign="top" class="cart_background_wrap1">
					<div class="p_extra p_top"> <b style="font-size: 13px">%mrp%</b><div style="padding-top:10px;color: #cd0000;font-size: 11px;display: %dspmrp%;"><b>OldMRP :</b> %oldmrp%</div></div>
				</td>
				<td style="text-align: center;" valign="top"  class="cart_background_wrap2">
					<div class="price p_top">%price%</div>
				</td>
				<td  style="text-align: center;" valign="top"  class="cart_background_wrap1">
					<div class="price p_top"> %margin_amt% &nbsp;(%margin%%)</div>
				</td>
				<td style="text-align: center;" valign="top"  class="cart_background_wrap2_lprice">
					<div class="lcost p_top"><b>%lcost%</b><br>%imei_sch_disc% </div>
				</td>
				<td style="text-align: center;" valign="top"  class="cart_background_wrap1">
					<div class="p_top"><input type="text" class="qty" pmax_ord_qty="%max_oqty%" size=2 name="qty[]" value="%cart_qty%"> %max_ord_qty%</div>
				</td>
				<td  style="text-align:center;"  class="cart_background_wrap2_subtotal" valign="top">
					<div class="stotal p_top" style="font-color:white;">%ttllcost%</div>
				</td>
			
				<td style="text-align: center;"  class="cart_background_wrap1" valign="top">
					<a onclick="remove_psel(this)" title="Remove Product"><div class="p_top"><img src=<?php echo base_url().'images/remove-over-red.png';?> style="cursor:pointer;" title="Remove Product from cart"></a></div>
				</td>
			</tr>
		</tbody> 
	</table>

	<div id="franchise_note" Title="Franchise Note for the Pending Amount">
		<div width="100%">
			<div style="float:left;margin: 5px;"><b>Pending Amount : </b><b style="color:#CD0000;font-size: 16px;">Rs.<?php echo $pending_payment;?></b></div>
			<div style="float:right;margin: 5px;"><b>Uncleared Amount : </b><b>Rs.<?php echo $uncleared_payment;?>&nbsp;<a href="javascript:void(0)" onclick="view_uncleardrecipts(<?php echo $franid?>)" style="font-size: 11px;color: blue;">view</a></b></div>
		</div></br>
		<h4 style="color:#CD0000;margin:17px 0px 15px 6px;">Note : Pending Payment details is Mandatorily collected, please communicate this to franchisee and its a reponsibility of a Franchsiee support executive to recover this and only upon satisfaction place orders</h4>
		
		<form method="post"  id="fran_note_form" data-validate="parsley">
			<table width="100%" cellspacing="5">
				<tr><td valign="top"><b>Remarks :</b></td><td><textarea style="width: 726px; height: 198px;" data-required="true" name="fran_note" class="parsley-validated" value="" id="fran_note"></textarea></td></tr>
				<tr><td></td><td style="font-size: 11px"><span class="red_star">*</span><b>Minimum 50 characters</b></td></tr>
			</table>
		</form>
	</div>
	<div id="franlogin_div" title="StoreKing Offline Order">
		<div class="stk_ordr_inpwrap"><b>State : </b><select class="fran_det chzn-select" data-placeholder="Choose State" name="sel_state" id="sel_state" style="width: 250px;">
														<option value="">Select State</option>
														<?php $states=$this->db->query("select state_id,state_name from pnh_m_states group by state_id order by state_name asc ")->result_array();
																if($states){foreach($states as $state){?>
														<option value="<?=$state['state_id']?>"><?=$state['state_name']?></option>
														<?php }}?></select>
		</div>
		<div  class="stk_ordr_inpwrap"><b>Territory : </b><select class="fran_det chzn-select" data-placeholder="Choose Territory" name="sel_terr" id="sel_terr" style="width: 250px;"></select></div>
		<div class="stk_ordr_inpwrap"><b>Franchise : </b> <select class=" chzn-select" data-placeholder="Choose Franchise "  name="fid" id="fid" style="width:250px;" ></select></div>
		<div class="stk_ordr_inpwrap"><b>Order For : </b><select name="mid_entrytype" class="mid_entrytype" style="width:200px;"><option value="0">Registered Member</option><option value="1">Not Registered Member</option></select></div>
		<div class="stk_ordr_inpwrap mid_blk"><b>Member Id : </b><input style="font-size:120%" maxlength="8"  type="text" class="membrid" name="mid" size=18 ></div>
		<div class="signin" style="display:none;float:right;"><input type="button" value="Proceed" onclick='load_franchisebyid()' class="button button-rounded button-action"></div>
	</div>

	<div id="mid_det" title="Member Details"><div id="mem_det"></div></div>
	
	<div id="authentiacte_blk" title="Franchisee Authentacation" ><div id="franchise_det"></div></div>

	<div id="change_member_blk" title="Member Details" style="display:none;">
		<div width="100%">
			<form id="change_mem_frm" method="post" action="<?php echo site_url('admin/jx_check_forvalid_mid')?>">
				<div class="stk_ordr_inpwrap"><b>Order For : </b><select name="mid_entrytype" class="mid_entrytype" style="width:200px;"><option value="0">Registered Member</option><option value="1">Not Registered Member</option></select></div>
				<div class="stk_ordr_inpwrap mid_blk"><b>Member Id : </b><input style="font-size:120%" maxlength="8"  type="text" class="chngd_membrid" name="mid" size=18 ></div>
			</form>
		</div>
	</div>

	<div id="uncleared_receipts_div" Title="Uncleared Amount Details">
		<table  class="datagrid nofooter" id="uncleared_receipt_tbl" width="100%">
			<h4>Uncleared Amount : <b>Rs.<?php echo $uncleared_payment;?></h4>
			<thead><th>Cheque no</th><th>Cheque Amount (Rs)</th><th>Cheque Date</th><th>Bank Name</th></thead>
			<tbody></tbody>
		</table>
	</div>

	<div id="quick_viewdiv" title="Quick View">
		<table id="qvk_prod_temp">
			<tbody>	</tbody>
		</table>
	</div>
	<table id="qvkview_template" style="display:none;">
		<tbody>
			<tr qvk_pname="%qvk_pname%" qvk_mrp="%qvk_mrp%" qvk_price="%qvk_price%" qvk_lcost="%qvk_lcost%" qvk_pid="%qvk_pid%" qvk_mrp="%qvk_mrp%" qvk_ofrprice="%qvk_price%" qvk_lcost="%qvk_lcost%" qvk_margin="%qvk_mrgn%%" qvk_mrgn_amt="%qvk_margin_amt%">
				<td style="padding:10px 0px 0px;background: #FFF;">
					<div  class="qvk_imgwrap">
						<img alt="" height="100" src="<?=IMAGES_URL?>items/small/%qvk_image%.jpg">
						<input class="pids" type="hidden" name="pid[]" value="%qvk_pid%">
					</div>
					  <div class="qvk_productguide_wrap" >
						  	<h5>PNH ID : %qvk_pid%</h5>
						  	<h3><a href="<?=site_url("admin/pnh_deal")?>/%qvk_pid%" target="_blank">%qvk_pname%</a></h3>
						  	<span>MRP : <b>Rs. %qvk_mrp%</b></span>
						  	<span>Offer Price : <b>Rs. %qvk_price%</b></span>
						  	<span>Landing Cost : <b>Rs. %qvk_lcost% </b></span>
					  </div>
				  </td>
			</tr>
		</tbody>
	</table>
	<div id="product_priceqoute" title="Product Price Quote">
		<form id="price_quoteform"  method="post" data-validate="parsley">
			<table class="datagrid nofooter" id="prod_pricequote">
				<thead><th>Product</th><th width="8%">MRP</th><th width="8%">Offer Price(Rs)</th><th width="12%">Discount(Rs)</th><th width="16%">Landing Price(Rs)</th><th width="16%">franchise price?</th></thead>
				<tbody></tbody>
			</table>
		</form>
	</div>
</div>

<SCRIPT>

function quikview_product(pid)
{
	$("#quick_viewdiv").data('qvk_pid',pid).dialog('open');
}
$("#quick_viewdiv").dialog({
	autoOpen:false,
	height:'auto',
	width:'auto',
	open:function(){
		$("#qvk_prod_temp tbody").html("");
        $('.ui-dialog-buttonpane .ui-dialog-buttonset').css({"display":"block","float":"none"});
		$('.ui-dialog-buttonpane').find('button:contains("Add to cart")').addClass('add_to_cartbtn');
		
		$('.ui-dialog-buttonpane').find('button:contains("Price Query")').css({"float":"left"});
	
		var dlg=$(this);
		
		$.post("<?=site_url("admin/pnh_jx_loadpnhprod")?>",{pid:dlg.data('qvk_pid'),fid:pre_selected_fid,cartdeal:1},function(data){
			obj=p=$.parseJSON(data);
			template=$("#qvkview_template tbody").html();
			template=template.replace(/%qvk_image%/g,p.pic);
			template=template.replace(/%qvk_pid%/g,p.pid);
			template=template.replace(/%qvk_menuid%/g,p.menuid);
			template=template.replace(/%qvk_pname%/g,p.name);
			template=template.replace(/%qvk_margin%/g,p.margin);
			template=template.replace(/%qvk_mrp%/g,p.mrp);
			template=template.replace(/%qvk_price%/g,p.price);
			template=template.replace(/%qvk_lcost%/g,p.lcost);
			template=template.replace(/%qvk_mrgn%/g,p.margin);
			template=template.replace(/%qvk_margin_amt%/g,Math.ceil(p.price-p.lcost));
			$("#qvk_prod_temp tbody").html(template);
			if(! p.allow_order.length || p.is_publish==0)
				$('.ui-dialog-buttonpane').find('button:contains("Add to cart")').css({'display':'none'});
			else
				$('.ui-dialog-buttonpane').find('button:contains("Add to cart")').css({'display':'block'});
			
		});
	
	},

	buttons:{
		
			'Add to cart':function(){
				add_product(p.pid);
				$(this).dialog('close');
			},
		
		
		'Price Query':function(){
			pricenotmatch_product(p.pid);
		}, 
	}
	
});

	$('#show_scheme_details').tabs();
	
	$(".fran_det").chosen();
	$("#fid").chosen();
	
	var mid="<?php echo $mid ?>";
	$(".p_top img[title]").tooltip();
	$('#cart_prod_div').tabs();
	$("#fran_note_edit_div").hide();
	$("#frm,#to").datepicker({dateFormat:'dd-mm-yy'});
	if(mid!=0)
	{
		$.post("<?=site_url("admin/jx_pnh_getmid")?>",{mid:mid},function(data){
			$("#mem_fran").html(data).show();
		});
	}else
	{
		$("#mem_fran").html("Not Registered Member").addClass('ordr_prefixtext ').show();
	}

/** jQuery  Custom Alpha sort pluin 
 * @author Suresh 
 **/ 
(function ($) {
    $.fn.jqSuAlphaSort = function (options) {
        options = $.extend({}, $.fn.jqSuAlphaSort.config, options);
        return this.each(function () {
        	var ele = $(this);
        		ele.options = options;
        	var chars = 'abcdefghijklmnopqrstuvwxyz';
        	var tmpl = '<div class="jq_alpha_sort_wrap">';
				tmpl += '	<div class="jq_alpha_sort_alphalist">';
				tmpl += '		<div class="jq_alpha_sort_alphalist_vend_head"></div>';
				tmpl += '		<div class="jq_alpha_sort_alphalist_char">';
				tmpl += '		<a href="javascript:void(0)" chr="20" id="ttwnty">T20</a>';
				tmpl += '		<a href="javascript:void(0)" chr="09">0-9</a>';
				for(var i=0;i<chars.length;i++)
					tmpl += '		<a href="javascript:void(0)" chr="'+chars[i].toUpperCase()+'">'+chars[i].toUpperCase()+'</a>';	
				tmpl += '		</div>';
				tmpl += '		<div class="jq_alpha_sort_alphalist_item_wrap">';
				tmpl += '			<div class="jq_alpha_sort_alphalist_itemlist"></div>';
				tmpl += '		</div>';
				
				tmpl += '	</div>';
				
				tmpl += '	<div class="jq_alpha_sort_overview">';
				tmpl += '		<div class="brands_bychar_list">';
				tmpl += '			<div class="Brands_bychar_list_head"></div>';
				tmpl += '			<div class="Brands_bychar_list_content"></div>';
				tmpl += '		</div>';
				
				tmpl += '		<div class="jq_alpha_sort_overview_content"></div>';
				tmpl += '	</div>';
				tmpl += '</div>';
            	ele.prepend(tmpl);

            $('.jq_alpha_sort_alphalist_char a',ele).live('click',function(){
            	options.char_click($(this).attr('chr'),this);
            	
            });
          
            $('.jq_alpha_sort_alphalist_item_wrap a',ele).live('click',function(){
            	$('.jq_alpha_sort_alphalist_item_wrap .selected a').parent().removeClass('selected');
            	$(this).parent().addClass('selected');
            	if($('.cat_lst_tab').hasClass('selected_alpha_list'))
            		options.item_click_bybrand($(this).attr('brandid'),this);
            	else 
            		options.item_click($(this).attr('catid'),this);
            });
            
        });
    };
    $.fn.jqSuAlphaSort.config = {
        title:'Hello',
        click:function(){}
    };
}(jQuery));

var pre_selected_fid = "<?php echo $franid?>";
var selected_mid="<?php echo $mid?>";
if(pre_selected_fid!='' || pre_selected_fid!=0)
{
	$("#hd").hide();
	selected_fran(pre_selected_fid);
	$('.fran_credit_detwrap').show();
	
}	
$('.fran_credit_detwrap').hide();

function endisable_sel(act,brandid,catid)
{
	var ids=[];
	$(".sel:checked").each(function(){
		ids.push($(this).val());
	});
	ids=ids.join(",");
	$.post(site_url+'/admin/pnh_pub_unpub_deals',{ids:ids,act:act},function(resp){
	if(resp.status == 'error')
	{
		
	}
	else
	{
		
	}
},'json');
	deallist_bycat(brandid,catid,0,pre_selected_fid,dealid);
}


$(function(){
	
	$('#cus_jq_alpha_sort_wrap').jqSuAlphaSort({title:"List of Categories",overview_title:"List of Deals",'char_click':function(chr,ele){ cat_bychar(chr)},'item_click':function(catid,ele){ deallist_bycat(0,catid,0,pre_selected_fid,0)},'item_click_bybrand':function(brandid,ele){ deallist_bycat(brandid,0,0,pre_selected_fid,0)}});
		$(".jq_alpha_sort_alphalist_char a").click(function() {
	      $(".jq_alpha_sort_alphalist_char a").removeClass("jq_alpha_active");
	      $(this).addClass("jq_alpha_active");
	    });
	
	 $(".jq_alpha_sort_alphalist_char a:eq(0)").trigger('click');
	 $('.jq_alpha_sort_alphalist_itemlist').slimScroll({
	    height: '100px',
	});
	$('.jq_alpha_sort_alphalist_vend_head').html('<div class="alphabet_header_wrap"><span><a id="cat_lab" class="cat_lst_tab">Category List</a></span><span><a id="brand_lab" style="margin-right:0px !important" class="brand_lst_tab">Brand List</a></span> <input type="text" name="search_name" class="search_blk inp" placeholder="Search by Name" ><img style="margin-top: 7px;" src="<?php echo base_url().'images/search_icon.png'?>"></div>');
	deallist_bycat(0,0,2,pre_selected_fid,0);
	$('.brand_lst_tab').addClass('selected_alpha_list');
});

$(".sel_all").live('click',function(){
		if($(".sel_all").attr("checked"))
		{
			$(".sel").attr("checked",true);
		}
		else
			{$(".sel").attr("checked",false);}
	});

$('.Brands_bychar_list_content_listdata').live("click",function(){
	var brandid =$(this).attr('bid')*1;
	var catid =$(this).attr('cid')*1;
	
	$(this).toggleClass("selected_class");
	  if($(this).hasClass('selected_class'))
	  {
	  	$('.selected_class').removeClass('selected_class');
	  	$(this).addClass('selected_class');
	  	filter_deallist($(this).text(),'brand');
	  	
	  }
	  else
	  { 
	  	filter_deallist('','all');
	  }
		 $('.left_filter_wrap').show();
});

$('.all').live('click',function(){
	var catid =$(this).attr('catid')*1;
	var brandid =$(this).attr('brandid')*1;
	$('.all').addClass("selected_type");
	if(catid !=0 && brandid == 0)
		deallist_bycat(0,catid,0,pre_selected_fid,0);
	else if(catid ==0 && brandid != 0)
		deallist_bycat(brandid,0,0,pre_selected_fid,0);
	else if(catid !=0 && brandid != 0)
		deallist_bycat(brandid,catid,0,pre_selected_fid,0);
	else
		deallist_bycat(0,0,0,pre_selected_fid,0);
});

$('.latest').live('click',function(){
	var catid =$(this).attr('catid')*1;
	var brandid =$(this).attr('brandid')*1;
	if(catid !=0 && brandid == 0)
		deallist_bycat(0,catid,1,pre_selected_fid,0);
	else if(catid ==0 && brandid != 0)
		deallist_bycat(brandid,0,1,pre_selected_fid,0);
	else	
	deallist_bycat(brandid,catid,1,pre_selected_fid,0);
	$('.latest').addClass("selected_type");
});

$('.most').live('click',function(){
	var catid =$(this).attr('catid')*1;
	var brandid =$(this).attr('brandid')*1;
	if(catid !=0 && brandid == 0)
		deallist_bycat(0,catid,2,pre_selected_fid,0);
	else if(catid ==0 && brandid != 0)
		deallist_bycat(brandid,0,2,pre_selected_fid,0);
	else	
		deallist_bycat(brandid,catid,2,pre_selected_fid,0);
	$('.most').addClass("selected_type");
	
});

$('.cat_lst_tab').live('click',function(){
	$('#cat_lab').val('1');
	$('#brand_lab').val('0');
	deallist_bycat(0,0,0,pre_selected_fid);
	
	$(".jq_alpha_sort_alphalist_char a:eq(0)").trigger('click');
	$('.cat_lst_tab').removeClass("selected_alpha_list");
	$('.brand_lst_tab').addClass("selected_alpha_list");
});

$('.brand_lst_tab').live('click',function(){
	deallist_bycat(0,0,0,pre_selected_fid,0);
	$('#cat_lab').val('0');
	$('#brand_lab').val('1');
	$(".jq_alpha_sort_alphalist_char a:eq(0)").trigger('click');
	$('.brand_lst_tab').removeClass("selected_alpha_list");
	$('.cat_lst_tab').addClass("selected_alpha_list");
});


$('select[name="publish_wrap"]').live('change',function(){
	var v=$(this).val();
	published_deals(v);
});

function published_deals(v)
{
	filter_deallist('','all');
	return ;
	if(v != 'all')
	{
		var i=1;
		$('.sk_deal_blk_wrap thead').show();
		$(".sk_deal_filter_wrap").each(function(){
			publish=parseInt($(this).attr('publish')*1);
			if(publish == v)
			{
				$('.total_wrap').html("Total Deals : "+(i++));
				$(this).show();
			}
			else 
			{
				$(this).hide();
			}
		});
	}
	else
	{
		return;
	}
}

function tgl_menubar(ele)
{
	$(ele).parent().hide();
	if(!$("#hd").is(':visible'))
	{
		$(ele).html("Hide Menu");
		$("#hd").slideDown("slow");
		setTimeout(function(){
			$(ele).parent().css('top','121px').show();
		},1000);
	}else
	{
		$(ele).html("Show Menu");
		$("#hd").slideUp("slow");
		setTimeout(function(){
			$(ele).parent().css('top','0px').show();
		},1000);
	}
	
}

function filter_deallist(tag,by)
{
	var srch_inp =  $('input[name="srch_deals"]').val();
	var publish_status = $('select[name="publish_wrap"]').val();
	var bc_sel = ($('.Brands_bychar_list_content_listdata.selected_class').length?($('.Brands_bychar_list_content_listdata.selected_class').text()):0);
	$(".sk_deal_filter_wrap").each(function(){
			
			search_text=$(this).attr('pnh_id')+' '+$(this).attr('name')+' '+$(this).attr('brand')+' '+$(this).attr('category');
			var row_stat = 1;
			if(publish_status != $(this).attr('publish') && (publish_status != 'all'))
					row_stat = 0;
			
			// check if any brand cat is selected 
			if(bc_sel && row_stat)
			{
				tag = bc_sel;
				if(search_text.match(tag,'ig'))
					row_stat = 1;
				else 
					row_stat = 0;
			}
			
			// check if search data is entered 
			if(srch_inp.length && (row_stat==1))
			{
				tag = srch_inp;
				if(!search_text.match(tag,'ig'))
					row_stat = 0;
			}

			if(row_stat == 1)
			{
				$(this).show();
			}
			else
			{
				$(this).hide();
				search_othr_deal();
			}
		
	});

	$('.total_wrap').html("Total Deals : "+$(".sk_deal_filter_wrap:visible").length);
}

$('input[name="srch_deals"]').live('keyup',function(){
	filter_deallist($(this).val(),'search');
});

/*$('.tgl_stock_combo').live('click',function(){
	var trele=$(this).parents('tr:first');
	var ref_id  = $(this).attr('ref_id');
	
	$.post(site_url+'/admin/pnh_jx_dealstock_det',{refid:ref_id,is_pnh:1},function(resp){
			if(resp.status == 'error')
			{
				$('.stock_det_'+ref_id).html("No Details found");
			}
			else
			{
				
				var qcktiphtml = '<span style="float:right;color:red;cursor:pointer" refid="'+ref_id+'" class="stock_det_close">X</span> <div style="float:left;width:100%">';
					qcktiphtml += '<table width="100%" border=1 class="datagrid" cellpadding=3 cellspacing=0>';
					qcktiphtml += '<thead><tr><th>Product Name</th><th>Stock</th></tr></thead><tbody>';
					$.each(resp.itm_stk_det,function(a,b){
						qcktiphtml+='<tr>';
						qcktiphtml+='	<td width="80%" style="font-size:10px">'+b.product_name+'</td>';
						qcktiphtml+='	<td width="20%" style="font-size:10px">'+b.stk+'</td>';
						qcktiphtml+='</tr>';
					});
					qcktiphtml += '</tbody></table></div>';
					$('.stock_det_'+ref_id).html(qcktiphtml);
					$('.stock_det_'+ref_id).show();
			}
			
		},'json');
		
	
});*/


$('input[name="search_name"]').live('keyup',function(){
	var chr=$('input[name="search_name"]').val();
	
	$(".jq_alpha_sort_alphalist_itemlist_divwrap").each(function(){
		name=$(this).attr('name');
		if(name.match(chr,'ig'))
			{
				$(this).show();
			}
		else
			{
				$(this).hide();
			}
	});
});

$('input[name="deal_name"]').live('keyup',function(){
	var chr=$('input[name="deal_name"]').val();
	
	$(".sk_deal_filter_wrap").each(function(){
		name=$(this).attr('name');
		if(name.match(chr,'ig'))
			{
				$(this).show();
			}
		else
			{
				$(this).hide();
			}
	});
});

$('.stock_det_close').live("click",function(){
	var id=$(this).attr('refid');
	$('.stock_det_'+id).hide();
});

function deallist_bycat(brandid,catid,type,pre_selected_fid,dealid)
{
	
	$('.sk_deal_container').css('opacity','0.5');
	if(catid != 0 && brandid==0)
	{
		$.getJSON(site_url+'/admin/loadbrand_bycat/'+catid+'/'+pre_selected_fid,'',function(resp){
			var brand_linkedcat_html='';
			if(resp.status=='error')
			{
				$('.Brands_bychar_list_head').html('<h4>NO Brands Found</h4>');
				$('.Brands_bychar_list_content').html("");
			}
			else
			{
				brand_linkedcat_html+='';
				$.each(resp.brand_list,function(i,b){
					brand_linkedcat_html+='<a class="Brands_bychar_list_content_listdata" cid="'+b.catid+'" bid="'+b.brandid+'">'+b.brand_name+'</a>';
					$('.Brands_bychar_list_head').html('<h4>List of Brand for '+b.category_name+'</h4><span class="close_btn">Hide</span>');
				});
			}
			$('.Brands_bychar_list_content').html(brand_linkedcat_html);
		});
	}else if(catid == 0 && brandid!=0)
	{
		$.getJSON(site_url+'/admin/loadcat_bybrand/'+brandid,'',function(resp){
			var cat_linkedcat_html='';
			if(resp.status=='error')
			{
				$('.Brands_bychar_list_head').html('<h4>NO Categories Found</h4>');
				$('.Brands_bychar_list_content').html("");
			}
			else
			{
				cat_linkedcat_html+='';
				$.each(resp.cat_list,function(i,b){
					cat_linkedcat_html+='<a class="Brands_bychar_list_content_listdata" cid="'+b.catid+'" bid="'+b.brandid+'">'+b.category_name+'</a>';
					$('.Brands_bychar_list_head').html('<h4>List of Categories for '+b.brand_name+'</h4><span class="close_btn">Hide</span>');
				});
			}
			$('.Brands_bychar_list_content').html(cat_linkedcat_html);
			
		});
	}

	if(!dealid)
		$('.sk_deal_container').html('<div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
		
	$.post(site_url+'/admin/jx_deallist_bycat',{brandid:brandid,catid:catid,type:type,pre_selected_fid:pre_selected_fid,dealid:dealid},function(resp){
		
		$('.sk_deal_container').css('opacity','1');
		if(resp.status == 'error')
			{
				$('.sk_deal_container').html('<div class="page_alert_wrap">No Deals Found</div>');
				return false;
		    }
			else
			{
				var enable='checked="checked"';
				var d_lst = '';
				d_lst+='<div class="sk_deal_filter_blk_wrap"><span>';
				
				d_lst+='</span>';
				d_lst+='<span class="left_filter_mrp_wrap">DP Filter : <input type="text" class="inp" id="f_from" size=4> to <input type="text" class="inp" id="f_to" size=4> <button type="button" style="margin-top:-5px" class="button button-rounded button-action button-tiny" onclick="filter_deals_bymrp()">Filter</button></span>';
				d_lst+='<span class="publish_wrap"><b>Published :</b> <select name="publish_wrap"><option value="all">Choose</option><option value="1">Yes</option><option value="0">No</option></select></span>';
				d_lst+='<span class="left_filter_wrap"><span class="filter_opts"><a class="most" href="javascript:void(0)" val="2"  brandid="'+brandid+'" catid="'+catid+'">Most</a></span><span><a href="javascript:void(0)" class="latest" val="1" brandid="'+brandid+'" catid="'+catid+'" >Latest</a></span><span><a href="javascript:void(0)" val="0" class="all" brandid="'+brandid+'" catid="'+catid+'" style="border-right:none !important;width:34.4%">Clear</a></span></span>';
				d_lst+='<span class="total_wrap">Total Deals : '+resp.ttl_deals+'</span>';
				d_lst+='<span class="legends_outer_wrap"><span class="legends_color_notsrc_wrap">&nbsp;</span> - Out Of Stock &nbsp; &nbsp; &nbsp;<span class="legends_color_src_wrap">&nbsp;</span> - In Stock &nbsp; &nbsp; &nbsp;</span></div>';
				d_lst+='<div class="sk_deal_container">';
				d_lst+='<table class="sk_deal_blk_wrap" cellpadding="0" cellspacing="0" width="99%">'
				d_lst+='<thead><tr>';
				d_lst+='<th width="6%">PNH ID</th><th>Deal Name</th><th width="8%">Stock</th><th width="10%" style="">Brand</th><th width="15%">Category</th><th width="6%">MRP</th><th width="10%">DP/Offer Price</th><th width="1%" style="align:center;">Actions<br />';
			
				d_lst+='</th></tr></thead>';
				var tmp_tbl_list = '';
			 	$.each(resp.deals_lst,function(i,d){
			 		if(d.allow_order.length)
					{
						dstock = 'In Stock';
						var background='background:none repeat scroll 0 0 rgba(170, 255, 170, 0.8) !important'/*#40FC36*/;
					}else
					{
						dstock = 'Out of Stock';
						var background='background:none repeat scroll 0 0 #FFAAAA !important';
					}
				
						var tbl_list = '';
							tbl_list+='<tr style="'+background+'" class="sk_deal_filter_wrap pnhid_'+d.pnh_id+' deals_'+d.catid+'"  mrp="'+d.orgprice+'" pnh_id="'+d.pnh_id+'" name="'+d.name+'" ref_id="'+d.itemid+'" brand="'+d.brand+'" category="'+d.category+'" dp="'+d.price+'" publish="'+d.publish+'" >';
							tbl_list+='		<td><span>'+d.pnh_id+'</span></td>';
							tbl_list+='		<td><span class="deal_title"><a target="_blank" href="'+site_url+'/admin/pnh_deal/'+d.itemid+'">'+d.name+'</a><div class="stock_det_'+d.itemid+'"></div></td>';
							tbl_list+='		<td><a href="javascript:void(0)" ref_id="'+d.itemid+'" dealid="'+d.itemid+'" class="tgl_stock_combo deal_stock">'+(dstock)+'</a></td>';
							tbl_list+='		<td><span><a target="_blank" href="'+site_url+'/admin/viewbrand/'+d.brandid+'">'+d.brand+'</a></span></td>';
							tbl_list+='		<td><span><a target="_blank" href="'+site_url+'/admin/viewcat/'+d.catid+'">'+d.category+'</a></span></td>';
							tbl_list+='		<td><span class="mrp">'+d.orgprice+'</span></td>';
							tbl_list+='		<td align="justify"><span>'+d.price+'</span></td>';
							tbl_list+='		<td align="center"><span class="prod_'+d.pnh_id+'"><a href="javascript:void(0)" onclick="quikview_product('+d.pnh_id+')" class="button button-rounded button-tiny quicklook_btn" >Quick Look</a></span></td>';
		 					tbl_list+='</tr>';
		 					
		 					tmp_tbl_list += tbl_list;
		 					d_lst += tbl_list;
		 					
				});
				
				d_lst+='</table>';
				d_lst+='</div>';

				
				if(dealid)
					$('.jq_alpha_sort_overview_content .sk_deal_container tbody').prepend(tmp_tbl_list);
				else
					$('.jq_alpha_sort_overview_content').html(d_lst);

				   // Call the plugin
                $(".jq_alpha_sort_overview_content .deal_stock").dealstock();
			
				$("#sel_cat").chosen();
				if(resp.type == 1)
				{
					$('.most').removeClass('selected_type');
					$('.latest').addClass('selected_type');
				}else if(resp.type == 0)
				{
					$('.latest').removeClass('selected_type');
					$('.most').removeClass('selected_type');
				}
				else if(resp.type == 2)
				{
					$('.most').addClass('selected_type');
					$('.latest').removeClass('selected_type');
				}
			}
	},'json');	
	
}

function cat_bychar(ch)
{
	if($('#cat_lab').val() == 1)
	{
		$.post(site_url+'/admin/cat_list_bycharacter',{ch:ch,pre_selected_fid:pre_selected_fid},function(resp){
			
    	if(resp.status == 'error')
			{
				alert("Brands not found for selected character");
				return false;
		    }
			else
			{
				
					var b_list = '';
					$.each(resp.cat_list,function(i,b){
			 			b_list += '<div class="jq_alpha_sort_alphalist_itemlist_divwrap" name="'+b.name+'"><a  href="javascript:void(0)"  catid="'+b.id+'">'+b.name+'</a></div>';
					});
				$('.jq_alpha_sort_alphalist_itemlist').html(b_list);
			}
    	},'json');
    	
	}else if($('#brand_lab').val() == 1)
	{
		
		$.post(site_url+'/admin/brand_list_bycharacter',{ch:ch,pre_selected_fid:pre_selected_fid},function(resp){
    	if(resp.status == 'error')
			{
				alert("Categories not found for selected character");
				return false;
		    }
			else
			{
					var b_list = '';
					$.each(resp.brand_list,function(i,b){
			 			b_list += '<div class="jq_alpha_sort_alphalist_itemlist_divwrap" name="'+b.name+'"><a  href="javascript:void(0)"  brandid="'+b.id+'">'+b.name+'</a></div>';
					});
				$('.jq_alpha_sort_alphalist_itemlist').html(b_list);
			}
    	},'json');
	}else
	{
	
		$.post(site_url+'/admin/cat_list_bycharacter',{ch:ch,pre_selected_fid:pre_selected_fid},function(resp){
    	if(resp.status == 'error')
			{
				alert("Categories not found for selected character");
				return false;
		    }
			else
			{
					var b_list = '';
					$.each(resp.cat_list,function(i,b){
			 			b_list += '<div class="jq_alpha_sort_alphalist_itemlist_divwrap" name="'+b.name+'"><a  href="javascript:void(0)" catid="'+b.id+'">'+b.name+'</a></div>';
					});
				$('.jq_alpha_sort_alphalist_itemlist').html(b_list);
			}
    	},'json');
	}		
}   



$('.Brands_bychar_list_content').hide();
$(".Brands_bychar_list_head span").live("click",function() {
	var $this = $(this);
    $('.Brands_bychar_list_content').slideToggle(200, function () {
        $this.text($(this).is(':visible') ? 'Hide' : 'Show');
        
    });
    $(".Brands_bychar_list_head").show();
});

function filter_deals_bymrp()
{
	from=$("#f_from").val();
	to=$("#f_to").val();
	if(from == to)
	{
		alert("Filter prices are not valid numbers");
		return;
	}
	if(!is_numeric(from) || !is_numeric(to))
	{		
		alert("Filter prices are not valid numbers");
		return;	
	}
	
	var valid_dp=[];
	$(".sk_deal_filter_wrap").each(function(){
		$(this).show();
		dp=parseInt($(this).attr('dp')*1);
		if(dp>=from && dp<=to)
		{
				valid_dp.push(dp);
				$(this).show();
		}
		else
		{
				$(this).hide();
		}
	});
	if(valid_dp.length == 0)
	{
		$('.sk_deal_container').html('<div class="page_alert_wrap">No Deals Found between Rs. '+from+' to Rs. '+to+'</div>');
	}
}

var pids=[];



function selected_fran(pre_selected_fid)
{
	
	$.post("<?=site_url("admin/pnh_jx_getfranbalance")?>",{id:pre_selected_fid},function(data){
		o=$.parseJSON(data);
		credit=parseInt(o.credit);
		balance= format_number(o.balance);
		if(balance < 5000)
			$("#fran_balance").addClass('warning');
		else
			$("#fran_balance").removeClass('warning');
			
		$("#fran_balance").text(balance);
		$("#fran_credit").text(credit);

		$('.fran_credit_detwrap .total_ord').text(o.total_ord);
		$('.fran_credit_detwrap .last_ord').text(o.last_ordon);
		$('.fran_credit_detwrap .total_mem').text(o.total_mem);
		$('.fran_credit_detwrap').show();
		var fran_status=[];
		fran_status[2]='Payment Suspension';
		fran_status[3]='Tempervary Suspension';
		var fran_type_msg = '';
		var fran_sus_msg = '';
	});
}
function load_scheme_details()
{
	$("#show_scheme_details").dialog('open');
}

$("#show_scheme_details").dialog({
	model:true,
	autoOpen:false,
	width:'800',
	height:'500',
	open:function(){
		dlg = $(this);
		$('#active_scheme_discount tbody').html("");
		$('#active_super_scheme tbody').html("");
		$('#active_imei_scheme tbody').html("");
		$('#menu tbody').html("");
		$.post("<?=site_url("admin/pnh_jx_load_scheme_details")?>",{fid:pre_selected_fid},function(result){
				if(result.active_schdisc != undefined)
				{
					$.each(result.active_schdisc,function(k,v){
						if(v.brand_name == undefined)
						{v.brand_name='All brands'; }
						if(v.cat_name== undefined)
						{v.cat_name='All categories';}
						 var activesch_row =
							 "<tr>"
							  +"<td>"+v.menu_name+"</td>"
							  +"<td>"+v.brand_name+"</td>"
							  +"<td>"+v.cat_name+"</td>"
							  +"<td>"+v.discount+"</td>"
							  +"<td>"+v.validfrom+"</td>"
							  +"<td>"+v.validto+"</td>"
							  +"</tr>";
							  $("#active_scheme_discount tbody").append(activesch_row);
					});
				}
				else
				{
					 $("#active_scheme_discount tbody").append('<tr><td style="text-align:center;"><b>No Data Found</b></td></tr>');
				}

				  
				if(result.active_supersch != undefined)
				{
					$.each(result.active_supersch,function(k,v){
						if(v.brand_name == undefined)
						{v.brand_name='All brands'; }
						if(v.cat_name == undefined)
						{v.cat_name='All categories';}
						 var supersch_row =
							 "<tr>"
							  +"<td>"+v.menu_name+"</td>"
							  +"<td>"+v.brand_name+"</td>"
							  +"<td>"+v.cat_name+"</td>"
							  +"<td>"+v.target_value+"</td>"
							  +"<td>"+v.credit_prc+"</td>"
							  +"<td>"+v.validfrom+"</td>"
							  +"<td>"+v.validto+"</td>"
							  +"<td></td>"
							  +"</tr>";
							  $("#active_super_scheme tbody").append(supersch_row);
							
					});
				}
				else
				{
					 $("#active_super_scheme tbody").append('<tr><td style="text-align:center;"><b>No Data Found</b></td></tr>');
				}
				if(result.active_imeischeme != undefined)
				{
					$.each(result.active_imeischeme,function(k,v){
						if(v.brand_name == undefined)
						{v.brand_name='All brands'; }
						if(v.cat_name == undefined)
						{v.cat_name='All categories';}
						if(v.scheme_type == 1)
						{v.scheme_type='Percentage'; }
						if(v.scheme_type == 0)
						{v.scheme_type='Fixed Value'; }
						 var imeisch_row =
							 "<tr>"
							  +"<td>"+v.menu_name+"</td>"
							  +"<td>"+v.brand_name+"</td>"
							  +"<td>"+v.cat_name+"</td>"
							  +"<td>"+v.scheme_type+"</td>"
							  +"<td>"+v.credit_value+"</td>"
							  +"<td>"+v.validfrom+"</td>"
							  +"<td>"+v.apply_from+"</td>"
							  +"<td>"+v.validto+"</td>"
							  +"<td></td>"
							  +"</tr>";
							  $("#active_imei_scheme tbody").append(imeisch_row);
							
					});
				}
				else
				{
					 $("#active_imei_scheme tbody").append('<tr><td><b>No Data Found</b></td></tr>');
				}
				if(result.menu != undefined)
				{
					$.each(result.menu,function(k,v){
						 var menutbl_row =
							 "<tr>"
							  +"<td width='400'><span style='display:inline-block;min-width:100px;'>"+v.menu+"</span></td>"
							  +"</tr>";
							  $("#menu tbody").append(menutbl_row);
					});
				}
		},'json');
	},
});

function add_product(pid)
{
	$.post("<?=site_url("admin/jx_add_deal_tocart")?>",{pid:pid,fid:pre_selected_fid,mid:<?php echo $mid;?>},function(resp){
		if(resp.status=='success')
		{
			$("span.prod_"+pid).html('<a href="javascript:void(0)" onclick="remove_prod_frmcart('+pid+')" class="button button-rounded button-tiny remove_cart_btn" title="Remove From Cart" align="center">REMOVE</a>');
			$("#item_count_in_cart_top_displayed").html(resp.ttl_cart_item);
		}else if(resp.status=='error')
		{
			alert(resp.message);
			
		}else
		{
			$('.cart-container').trigger('click');
		}
	},'json');

	
}

var ppids=[];
	
	$("#cart_prod_div").dialog({
		autoOpen:false,
		width:1250,
		modal:true,
		height:600,
		open:function(event, ui) {
	        $(event.target).dialog('widget')
            .css({ position: 'fixed' })
            .position({ my: 'center', at: 'center', of: window });
	        $('.ui-dialog-buttonpane .ui-dialog-buttonset').css({"display":"block","float":"none"});
			$('.ui-dialog-buttonpane').find('button:contains("Continue Shopping")').addClass('continue_btn');
			$('.ui-dialog-buttonpane').find('button:contains("Place Order")').addClass('placeorder_btn');
			$('.ui-dialog-buttonpane').find('button:contains("Submit")').css({"float":"right"});
			var dlg=$(this);
			var html_cnt='';
					$.post("<?=site_url("admin/jx_getsaved_item_incart")?>",{fid:pre_selected_fid,mid:<?php echo $mid;?>},function(data){
					if(data.status =='success')
					{
						$.each(data.saved_cart_itms.items,function(i,p){
								obj=p;
								$("#p_pid").attr("disabled",false);
								$(".add_product").val('Add');
								$("#p_pid").val("");
								template=$("#template tbody").html();
								template=template.replace(/%sno%/g,(i*1+1));
								template=template.replace(/%pimage%/g,p.pic);
								template=template.replace(/%pid%/g,p.pid);
								template=template.replace(/%menuid%/g,p.menuid);
								template=template.replace(/%attr%/g,p.attr);
								template=template.replace(/%pname%/g,p.name);
								template=template.replace(/%cat%/g,p.cat);
								template=template.replace(/%brand%/g,p.brand);
								template=template.replace(/%margin%/g,p.margin);
								if(p.oldmrp == '-')
									template=template.replace(/%dspmrp%/g,'none');
								else
									template=template.replace(/%dspmrp%/g,'block');
								
								template=template.replace(/%oldmrp%/g,p.oldmrp);
								template=template.replace(/%newmrp%/g,p.mrp);
								template=template.replace(/%mrp%/g,p.mrp);
								template=template.replace(/%price%/g,p.price);
								template=template.replace(/%lcost%/g,p.lcost);
								template=template.replace(/%stock%/g,p.stock);
								template=template.replace(/%confirm_stock%/g,p.confirm_stock);
								template=template.replace(/%margin_amt%/g,Math.ceil(p.price-p.lcost));
								template=template.replace(/%lcl_distribtor_mrgn%/g,p.local_distbtr_margin);
								template=template.replace(/%cart_qty%/g,p.svd_cartqty);
								
								template=template.replace(/%ttllcost%/g,format_number(p.svd_cartqty*p.lcost));
								
								if(p.max_allowed_qty*1 == 0)
								{
									template=template.replace(/%max_oqty%/g,500);
									template=template.replace(/%max_ord_qty%/g,"");
								}else
								{
									template=template.replace(/%max_oqty%/g,p.max_ord_qty);
									
									template=template.replace(/%max_ord_qty%/g,"<span class='tip_popup max_qty_wrap' title='Maximum Allowed Quantity'>/&nbsp;("+p.max_ord_qty+"&nbsp;Qty)</span>");
								}
								
								if(p.imei_disc!= 0)
								{
									if(p.imei_disc.scheme_type==0)
										var imei=p.imei_disc.credit_value;
									else
										var imei=(p.lcost*p.imei_disc.credit_value)/100 ;
										
									template=template.replace(/%imei_sch_disc%/g,"<span style='font-size:11px;' class='tip_popup imei_wrap' title='On IMEI Activation'><b>IMEI Activation: </b>(Rs."+format_number(imei)+") </span> ");
								}else
								{
									template=template.replace(/%imei_sch_disc%/g,"");
								}
								
								if(p.super_sch!=0)
								{
									var super_sch_det="<b>Credit : </b>"+p.super_sch.credit_prc+"%"+"<br> "+"<b>Target value :</b>"+p.super_sch.target_value+"<br>"+"<b>Valid Till :</b>"+p.super_sch.valid_to;
									template=template.replace(/%super_sch%/g,"Super Scheme : <span style='font-size:11px;color:#000;'> <span class='tip_popup' title='"+super_sch_det+"' style='cursor:pointer;'>Available </span></span> ");
								}else
								{
									template=template.replace(/%super_sch%/g,"Super Scheme : <span style='font-size:11px;color:#000;'>No</span> ");
								}

								template=template.replace(/%mrp%/g,p.mrp);

								html_cnt += template;
								
								jQuery(document).ready(function() {
								 	Tipped.create('.tip_popup',{
								 	 skin: 'black',
								 	  hook: 'topleft',
								 	  hideOn: false,
								 	  closeButton: true,
								 	 	opacity: .5,
								 	 	hideAfter: 200,
									 });
								});
						});
						$("#cart_prod_temp tbody").html(html_cnt);
						
						change_total_subtotal();
						$('.cart-footer div').show();
						$(".confirm_bloc").show();
						$("#cart_prod_temp thead").show();
					}
					else
					{
						$("#cart_prod_temp thead").hide();
						$(".confirm_bloc").hide();
						$('.cart-footer div').hide();
						$("#cart_prod_temp tbody").html("<tr><td colspan='6' align='center'><div class='empty-cart-message' >There are no items in this cart. </div></td></tr>");
					}
								
				},'json');	

			
		},
	buttons:{
		'Place Order':function(){
				$("#order_form").submit();
			},
		'Continue Shopping':function()
		{
			$(this).dialog('close');
		},
		
		}
	});

function change_total_subtotal() 
{
    var ttl_subtotal=0;
    if($("#cart_prod_temp .stotal").html() == '') {
        ttl_subtotal=0;
    }
    else {
        $("#cart_prod_temp .stotal").each(function(){
          ttl_subtotal += format_number($(this).html());
        });
    }
    $("#cart_totl").html(format_number(ttl_subtotal));
}

$("#cart_prod_temp .qty").live("change",function(){

	p=$(this).parents("tr").get(0);
	sel_pid = $(this).parents("tr:first").attr('pid');
	var qty_e = $(".qty",p).val()*1;

	if(isNaN(qty_e*1))
	{
		alert("Invalid Qty Entered.");
		qty_e = 0;
	}
	else if(qty_e*1 <= 0)
	{
		alert("Invalid Qty Entered,Please enter atleast one quantity.");
		qty_e = 0;
	}
		
	var qty_m = $(".qty",p).attr('pmax_ord_qty')*1;
	
	if(qty_e > qty_m)
	{
		alert("Maximum "+qty_m+" Qty can be Ordered ");
		qty_e = qty_m;
	}
	if(qty_e == 0 && qty_e > 0)
	{
		qty_e = 1;
	}
	$.post(site_url+'/admin/jx_update_cartqty/',{cart_qty:qty_e,pid:sel_pid,fid:pre_selected_fid,mid:<?php echo $mid;?>},function(resp){
		if(resp.status=='success')
			return true;
	},'json');
	
	$(".qty",p).val(qty_e);
	var sub_total = parseFloat( $(".lcost",p).text())*parseInt($(".qty",p).val() ) ;
	$(".stotal",p).html( format_number( sub_total ) );
    change_total_subtotal();
       
});
function remove_psel(ele)
{
	var sel_pid = $(ele).parents("tr:first").attr('pid');

	if(confirm("Are you sure want to remove product from cart?"))
	{	
		$.post("<?=site_url("admin/jx_update_tocart")?>",{pid:sel_pid,fid:pre_selected_fid,mid:<?php echo $mid;?>},function(data){
			if(data.status=='success')
			{
				$(ele).parents("tr:first").fadeOut().remove();
				$("#item_count_in_cart_top_displayed").html(data.ttl_cart_item);
				$("span.prod_"+sel_pid).html('<a href="javascript:void(0)" class="button button-rounded button-tiny quicklook_btn" onclick="quikview_product('+sel_pid+')" >Quick Look</a>');
	
				$('#cart_prod_temp tbody tr').each(function(a,b){
					$('td:first',this).html(a+1);
				});
				  change_total_subtotal();
			}
		},'json');
	}
	
}

function remove_pid(pid)
{
	var t_pids=pids;
	pids=[];
	for(i=0;i<t_pids.length;i++)
		if(pid!=t_pids[i])
			pids.push(t_pids[i]);
     change_total_subtotal(); 
}

$('.cart-container').click(function(){
	$("#cart_prod_div").dialog('open');
	return false;
});

$(".cart_prodcontinue").click(function(){
	$("#cart_prod_div").dialog('close');
});

var submit_order=0;
$("#order_form").submit(function(){
	
	total=0;
	ppids=[];
	qty=[];
	menuids=[];
	
	$("#cart_prod_div .stotal").each(function(){
		total+=parseFloat($(this).html());
	});
	total = format_number(total);
	
	$("#cart_prod_div .pids").each(function(){
		ppids.push($(this).val());
	});
	
	$("#cart_prod_div .menuids").each(function(){
		menuids.push($(this).val());
	});
	
	$("#cart_prod_div .qty").each(function(){
		qty.push($(this).val());
	});
	
		var menu_qty=qty;
		var menuid=menuids;
	//	var mid = $("input[name='mid']",$(this)).val();
	    var mid = selected_mid;
		var fran_note=$("#fran_note").val();

		var stk_confirm_prods = $('input[name="confirm_stock"]').length;
		
		var stk_confirm_prods_checked = $('input[name="confirm_stock"]:checked').length;

		if(stk_confirm_prods != stk_confirm_prods_checked && stk_confirm_prods > 0)
		{
			alert("Please verify whether stock for the footwear is available?");
			return false;
		}
		
		for (var i = 0; i < menuids.length; i++)
		 {
			var menu_id=menuids[i];
			var menu_qty=qty[i];
			if(menu_qty>1 && mid!=0 && menu_id  == 112)
			{
				alert("More than 1 qty of Electronics Item for 1 member can't be processed");
				return false;
			}

			if(mid==0 && menu_id != 112)
			{
				if(confirm("Instant Registration is required because Other than Electronic items are there in the Cart"))
					 mem_reg(pre_selected_fid);
				 return false;
			}
		 }
		var credit_days=$(".credit_days").val();
		/*
		if(credit_days==0)
		{
			alert("Please enter Credit Days");
			return false;
		}
		*/
		if(credit_days>5)
		{
			alert("Credit Days can't be greater than 5 Days");
			return false;
		}
		$("input[name='creditdays']").val($('.credit_days').val());		
		$("input[name='frannote']").val(fran_note);
		/*
		$("#fran_note_edit_div").show();

		 
		if(fran_note.length<50)
		{
			franchise_note(pre_selected_fid);
			return false;
		}*/
	

		if(stk_confirm_prods != stk_confirm_prods_checked && stk_confirm_prods > 0)
		{
			alert("Please verify whether stock for the footwear is available?");
			return false;
		}
		
		if(submit_order==0)
		{
			if(confirm("Total order value : Rs "+total+"\nAre you sure want to place the order?") )
			{
				attr=$(".attr").serialize();
				$.post(site_url+"/admin/pnh_jx_checkstock_order",{attr:attr,pids:ppids.join(","),qty:qty.join(","),fid:pre_selected_fid,mid:$("input[name='mid']",$(this)).val(),credit_days:$('.credit_days').val()}, function(resp){
					
					if(resp.e == 1)
					{
						submit_order=0;
						alert("ERROR!\n\n"+resp.msg);
						return false;
					}
					else
					{
						submit_order=1;	
						$("#order_form").submit();
					}
				
				},'json');
			}
			return false;
		}
		
});

function mem_reg(fid)
{
	
	$('#reg_mem_dlg').dialog('open');
}
$('#reg_mem_dlg').dialog({
			autoOpen:false,
			width:336,
			modal:true,
			height:'auto',
			open:function(){
				$('.ui-dialog-buttonpane .ui-dialog-buttonset').css({"display":"block","float":"none"});
				$('.ui-dialog-buttonpane').find('button:contains("Register")').css({"float":"right"});
				$('.ui-dialog-buttonpane').find('button:contains("Cancel")').css({"float":"right"});
				var dlg=$(this);
				var fid=pre_selected_fid;
					$('#reg_mem_frm input[name="franchise_id"]',this).val(fid);
				},
				buttons:{
					'Register':function(){
						$(this)
						var error_list = new Array();
						// register member 
						var mem_regname = $.trim($('input[name="memreg_name"]').val());
						var mem_mobno = $.trim($('input[name="memreg_mobno"]').val());
                        	if(mem_regname.length == 0)
                                error_list.push("Please Enter Member name.");

							if(mem_mobno.length == 0)
                                error_list.push("Please Enter Mobile Number.");
	                        else
	                        {
	                                mem_mobno = mem_mobno*1	;
	                                if(isNaN(mem_mobno))
	                                        error_list.push("Please Enter valid Mobile number.");	
	                        }	

                        if(error_list.length)
                        {
                                alert(error_list.join("\r\n"));
                        }else
                        {
                                $.post(site_url+'/admin/jx_reg_newmem',$('#reg_mem_frm').serialize(),function(resp){
                                        if(resp.status == 'success')
                                        {
                                            
                                                var mid=$('input[name="mid"]').val(resp.mid);
                                                location="<?=site_url("admin/stk_offline_order_deals") ?>/"+pre_selected_fid+'/'+resp.mid;
                                                $.post("<?=site_url("admin/jx_pnh_getmid")?>",{mid:resp.mid},function(data){
                                        			$("#mem_fran").html(data).show();
                                        			
                                        		});
                                                $('#reg_mem_dlg').dialog('close');
                                            	 
                                        }else
                                        {
                                                $('input[name="mid"]').val('');
                                                alert(resp.error);
                                        }
                                },'json');
                        }
					},
					'Cancel':function(){
						$(this).dialog('close');
					}
				}
});

function franchise_note(fid)
{
	$('#franchise_note').data('fid',fid).dialog('open');
}

$('#franchise_note').dialog({
	modal:true,
	height:'auto',
	width:'855',
	autoOpen:false,
	open:function(){
		$('.ui-dialog-buttonpane').find('button:contains("Save Note")').css({"float":"right"});
	},
	buttons:{
		'Save Note':function()
		{
			var realize_frm = $("#fran_note_form",this);
			 if(realize_frm.parsley('validate'))
				{
					if($("#fran_note").val().length<50)
					{
						alert("Minimum 50 characters should be entered");
						return false;
					}
					else
						$(this).dialog('close');
				}
		}
	}
});

function remove_prod_frmcart(pid)
{
	if(confirm("Are you sure want to remove product from cart?"))
	{
		$.post("<?=site_url("admin/jx_update_tocart")?>",{pid:pid,fid:pre_selected_fid,mid:<?php echo $mid;?>},function(resp){
			if(resp.status=='success')
			{
				$("#item_count_in_cart_top_displayed").html(resp.ttl_cart_item);
				$("span.prod_"+pid).html('<a href="javascript:void(0)" onclick="quikview_product('+pid+')" class="button button-rounded button-tiny quicklook_btn" >Quick Look</a>');
			}else
			{
				alert(resp.message);
			}
		},'json');
	}
	return false;
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
			// reformat data ;
			$('#ttl_order_amt').html("Total Ordered : "+resp.ttl_summary);
			$('#paymrent_order_amt').html("Total Paid : "+resp.ttl_payment);
			$('#shipped_order_amt').html("Total Shipped : "+resp.ttl_shipped);
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
		      axesDefaults: {
			        tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
			        tickOptions: {
			          fontFamily: 'tahoma',
			          fontSize: '11px',
			          angle: -30
			        }
			    },
			    legend: {
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

$('.analytics').click(function(){
	setTimeout(function(){
		fran_menu_stat();
		payment_order_stat();
		brands_byfranch();	
	},400);
});

$("#grid_list_frm_to").click(function(e){
	e.preventDefault();
	$('#payment_stat .payment_stat_view').unbind('jqplotDataClick');
    fran_menu_stat();
    payment_order_stat();
    brands_byfranch();
    return false;
});

/*function sel_fran()
{
	$("#franlogin_div").dialog('open');
}*/
$("#change_fran").live('click',function(){

	$("#franlogin_div").dialog('open');

});
$("#franlogin_div").dialog({
	modal:true,
	width:'447',
	height:'337',
	autoOpen:false,
	open:function(){
		$(".membrid").val(" ");
		
		$('#sel_state').val('').trigger('liszt:updated');
		$('#sel_terr').val('').trigger('liszt:updated');
		$('#fid').val('').trigger('liszt:updated');
		$('.mid_entrytype').val('0').trigger('liszt:updated');
		
	},
});

$("#sel_state").change(function(){
	$("#sel_terr").html('').trigger("liszt:updated");
	var sel_stateid=$("#sel_state").val();
	if(sel_stateid!=0)
	{
		$.getJSON(site_url+'/admin/jx_load_all_territories_bystate/'+sel_stateid,'',function(resp){
			var state_html='';
			if(resp.status=='error')
			{
				alert(resp.message);
			}
			else
			{
				state_html+='<option value=""></option>';
				state_html+='<option value="0">All</option>';
				$.each(resp.terrs_bystate,function(i,b){
					state_html+='<option value="'+b.id+'">'+b.territory_name+'</option>';
				});
			}
			$("#sel_terr").html(state_html).trigger("liszt:updated");
			$("#sel_terr").trigger('change');
			});
	}
});

$(".fran_det").change(function(){
	$("#fid").html('').trigger("liszt:updated");
	var sel_stateid=$("#sel_state").val();
	var sel_terrid=$("#sel_terr").val();
	if(sel_stateid!=0)
	{
		$.post(site_url+'/admin/jx_load_all_franchise_bystate_territory',{stateid:sel_stateid,terrid:sel_terrid},function(resp){
			var state_html='';
			if(resp.status=='error')
			{
				alert(resp.message);
			}
			else
			{
				state_html+='<option value=""></option>';
				state_html+='<option value="0">All</option>';
				$.each(resp.fran_bystateterry,function(i,b){
					state_html+='<option value="'+b.franchise_id+'">'+b.franchise_name+'</option>';
				});
			}
			$("#fid").html(state_html).trigger("liszt:updated");
			$("#fid").trigger('change');
			},'json');
	}
	
});

$(".membrid").change(function(){
	sel_state=$("#sel_state").val();
	sel_fran=$("#fid").val();
	sel_mtype=$(".mid_entrytype").val();
	if(sel_state=='' || sel_state==0)
		{$('select[name="sel_state"]').addClass('error_inp');return;}
	if(sel_fran=='' || sel_fran==0)
		{$("#fid").addClass('error_inp');return;}
	if($(".membrid").val().length!=0)
	{
		$("#franlogin_div").dialog('close');
		$("#mid_det").data('mid',$(this).val()).dialog('open');
	}
});

$('.mid_entrytype').live('change',function(){
	$('input[name="mid"]').val("");
	if($(this).val()==0)
	{
		$('.mid_blk').show();
		$(".signin").hide();
	}
	else
	{
		$('.mid_blk').hide();
		$(".signin").show();
	}
});

$("#mid_det").dialog({
	model:true,
	width:'400',
	height:'300',
	autoOpen:false,
	open:function()
	{
		dlg=$(this);
		$("#mem_det").html("");
		$.post("<?=site_url("admin/jx_pnh_getmid")?>",{mid:dlg.data('mid'),more:1},function(data){
			$("#mem_det").html(data).show();
		});
	},
	buttons:{
		'Proceed':function(){
			load_franchisebyid();
			$(this).dialog('close');
		},
		'Cancel':function(){
			$(this).dialog('close');
			
		},
	}
		
	});

function load_franchisebyid()
{
	/*sel_state=$("#sel_state").val();
	sel_fran=$("#sel_fid").val();
	sel_mtype=$(".mid_entrytype").val();
	if(sel_state=='' || sel_state==0)
		return;
	if(sel_fran=='' || sel_fran==0)
		return;
	if(sel_mtype==0)
	{
		if($(".mid").val().length==0)
			return;
	}*/
		$("#authentiacte_blk").dialog('open');
		
}
$( "#authentiacte_blk" ).dialog({
	modal:true,
	autoOpen:false,
	width:1000,
	height:670,
	autoResize:true,
	open:function(){
	dlg = $(this);
	$.post("<?=site_url("admin/pnh_jx_loadfranchisebyid")?>",{fid:$("#fid").val()},function(data){
		$("#franchise_det").html(data).show();
		});
	},
	
});

function select_fran(fid)
{
	fid=$("#fid").val();
	mid=$(".membrid").val();
	if($(".mid_entrytype").val() == 1)
		{ mid=0; }
	$("#hd").slideDown("slow");$(this).parent().hide();$("#prod_suggest_list").css({"top":"184px"});
	location="<?=site_url("admin/stk_offline_order_deals") ?>/"+fid+'/'+mid; 
}

function change_member()
{
	$("#change_member_blk").dialog('open');
}

$("#change_member_blk").dialog({
	modal:true,
	width:'414',
	height:'auto',
	autoOpen:false,
	open:function(){
		$('.ui-dialog-buttonpane .ui-dialog-buttonset').css({"display":"block","float":"none"});
		$('.ui-dialog-buttonpane').find('button:contains("Submit")').css({"float":"right"});
		$('.ui-dialog-buttonpane').find('button:contains("Cancel")').css({"float":"right"});
		
	},
	buttons:{
		'Submit':function(){
			var change_mbrid_form=$("#change_mem_frm");
			
					 $.post(site_url+'/admin/jx_check_forvalid_mid',$('#change_mem_frm').serialize(),function(resp){
		                 if(resp.status == 'success')
		                 {
			                 if(resp.midentry_type==0)
			                 {
		                     	var changd_mid=resp.mem_det.pnh_member_id;
		                        var mid=$('input[name="mid"]').val(changd_mid);
		                        $.post("<?=site_url("admin/jx_pnh_getmid")?>",{mid:changd_mid},function(data){
		                 			$("#mem_fran").html(data).show();
		                 		});
		                        $('#change_member_blk').dialog('close');
		                        location="<?=site_url("admin/stk_offline_order_deals") ?>/"+pre_selected_fid+'/'+changd_mid;
			                 }
			                 else
			     			{
			     				var changd_mid=0;
			     				$("#mem_fran").hide();
			     				$('#change_member_blk').dialog('close');
			     				location="<?=site_url("admin/stk_offline_order_deals") ?>/"+pre_selected_fid+'/'+changd_mid; 
			     				
			     			}	 
		                 }else
		                 {
		                         $('input[name="mid"]').val('');
		                         alert(resp.msg);
		                 }
		         },'json');
			
				
		},
		'Cancel':function(){
			   $('#change_member_blk').dialog('close');
		},
	}
});

function search_othr_deal()
{
	$('input[name="srch_deals"]').autocomplete({
		source:site_url+'/admin/jx_searchsktdeals_json/'+pre_selected_fid,
		minLength: 2,
		select:function(event, ui ){
			if(!$('.pnhid_'+ui.item.id).length)
				deallist_bycat(0,0,0,pre_selected_fid,ui.item.id);
		}
	});
}

function view_uncleardrecipts(fid)
{
	$("#uncleared_receipts_div").data('fid',fid).dialog('open');
}

$("#uncleared_receipts_div").dialog({
	modal:true,
	width:'694',
	height:'auto',
	autoOpen:false,
	open:function(){
		var dlg=$(this);
		var receipt_table_html='';
		$("#uncleared_receipt_tbl tbody").html("");
		$.post(site_url+'/admin/jx_load_franuncleared_receiptdet',{fid:dlg.data('fid')},function(resp){
			if(resp.status=='success')
			{
				$.each(resp.uncleared_receipts,function(i,r){
				receipt_table_html+='<tr>';
				receipt_table_html+='<td>'+r.instrument_no+'</td>';
				receipt_table_html+='<td>'+format_number(r.receipt_amount)+'</td>';
				receipt_table_html+='<td>'+r.instrument_date+'</td>';
				receipt_table_html+='<td>'+r.bank_name+'</td>';
				receipt_table_html+='</tr>';
				
				});
				$("#uncleared_receipt_tbl tbody").append(receipt_table_html);
			}
		},'json');
	},
	
});

function pricequery_product(pid)
{
	$.post(site_url+'/admin/fran_cartprodprice_enqry',{pid:pid,fid:pre_selected_fid},function(resp){
	if(resp.status=='success')
		return true;
	else
		return false;
		},'json');
}

function pricenotmatch_product(pid)
{
	$("#product_priceqoute").data('pid',pid).dialog('open');
}
$("#product_priceqoute").dialog({
	width:'549',
	height:'auto',
	autoOpen:false,
	open:function(){
		var dlg=$(this);
		
		$('.ui-dialog-buttonpane .ui-dialog-buttonset').css({"display":"block","float":"none"});
		$('.ui-dialog-buttonpane').find('button:contains("Price Enquiry")').addClass('price_enquiry_btn');
		$('.ui-dialog-buttonpane').find('button:contains("Price Not Matching")').css({"float":"right"});
			
		if($('#qvkview_template tbody tr').length)
		{
			$('#prod_pricequote').show();
			var req_plist = '';
			
			$('#qvk_prod_temp tbody tr').each(function(j,itm){
				req_plist += '<tr>';
				req_plist += '	<input type="hidden" name="pquote_pid" value='+$(this).attr('qvk_pid')+'> <input type="hidden" name="pquote_fid" value='+pre_selected_fid+'> <input type="hidden" name="mrp" value='+$(this).attr('qvk_mrp')+'> <input type="hidden" name="offprice" value='+$(this).attr('qvk_price')+'> <input type="hidden" name="lprice" value='+$(this).attr('qvk_lcost')+'></td>';
				req_plist += '	<td>'+$(this).attr('qvk_pname')+'</td>';
				req_plist += '	<td>'+$(this).attr('qvk_mrp')+'</td>';
				req_plist += '	<td>'+$(this).attr('qvk_price')+'</td>';
				req_plist += '	<td>'+$(this).attr('qvk_mrgn_amt')+'('+$(this).attr('qvk_margin') +')'+'</td>';
				req_plist += '	<td>'+$(this).attr('qvk_lcost')+'</td>';
				req_plist += '	<td><input type="text" size="6" name="quote" data-required="true" ></td>';
			});
			$('#prod_pricequote tbody').html(req_plist);
		}
		 pid = $('input[name="pquote_pid"]').val();
	},
	buttons:{
		
	'Price Not Matching':function(){
		var quote_form=$('#price_quoteform',this);
			if(quote_form.parsley('validate'))
			{
				$.post("<?=site_url("admin/fran_pricequote")?>",$("#price_quoteform").serialize(),function(resp){
					if(resp.status =='success')
					{
						$('#product_priceqoute').dialog('close');
					}
				},'json');
			}
			return false;
		},

		'Price Enquiry':function(){
			pricequery_product(pid);
			$(this).dialog('close');
		},
	},
});

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
					
				 	$('.shipment_log').fullCalendar('removeEvents')
				 		$.post(site_url+'/admin/jx_franchise_shipment_logonload',{'start': start.getTime(),'end': end.getTime(), 'fid':pre_selected_fid},function(result){
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
				 
						if(event_type == "shipment")
						{
							var title = 'Shipment Log on '+sel_date+'/'+(sel_mnth)+'/'+sel_year;
								$( "#ship_log_dlg" ).data({'sel_date':sel_date,'sel_mnth':sel_mnth, 'sel_year':sel_year, 'fid':pre_selected_fid, 'ship_date':ship_date }).dialog('open','option','title',title);
								$( "#ship_log_dlg" ).dialog('option','title',title);
						}else if(event_type == "delivery")
						{
							var title = 'Delivery Log on '+sel_date+'/'+(sel_mnth)+'/'+sel_year;
								$( "#delivery_log_dlg" ).data({'sel_date':sel_date,'sel_mnth':sel_mnth, 'sel_year':sel_year, 'fid':pre_selected_fid, 'delivery_date':delivery_date }).dialog('open','option','title',title);
								$( "#delivery_log_dlg" ).dialog('option','title',title);
						 }else if(event_type == "ordered")
						{
							var title = 'Items Ordered on '+sel_date+'/'+(sel_mnth)+'/'+sel_year;
								$( "#ordered_log_dlg" ).data({'sel_date':sel_date,'sel_mnth':sel_mnth, 'sel_year':sel_year, 'fid':pre_selected_fid, 'ordered_date':ordered_date }).dialog('open','option','title',title);
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
</script>
<?php
