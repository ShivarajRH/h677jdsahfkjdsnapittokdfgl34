<style>
#content .rightcont {
	background: #f1f1f1 !important;
}
#prod_fea_tab h4
{
	margin:4px 0px;
}
#prod_imei_tab h4
{
	margin:4px 0px;
}
.imei_stat_error
{
	font-size: 10px;color: #cd0000;display: block;
}
.leftcont
{
	display: none
}
.prd_outer_wrap
{
	float:left;
	width:100%;
}
.prd_outer_wrap1
{
	float: left;
    margin-bottom: 1%;
    min-height: 150px;
    width: 100%;
}
.prd_det
{
	 background: none repeat scroll 0 0 #fcfcfc;
    float: left;
    width:19%;
    margin-right: 1%;
    font-size: 12px;
    margin-bottom: 22px;
}
.prd_det span
{
	float: left;
    padding: 3px 1px;
    width: 100%;
}

.prd_outer_wrap1 b
{
	 float: left;
    font-weight: normal;
    padding-bottom: 2px;
    padding-left: 0;
    padding-top: 2px;
    text-align: right;
    width: 30%;
}
.prd_det_span a
{
	color: #000000;
    float: right;
    font-size: 13px;
    font-weight: bold;
    padding: 2px 0;
    width:66%;
}
.prd_det_span .prd_d
{
	color: #000000;
    float: right;
    font-size: 13px;
    font-weight: bold;
    padding: 2px 0;
    width:66%;
    text-align: left;
}
.prd_outer_bottom_wrap1
{
	 background: none repeat scroll 0 0 #FFFFDF;
    float: left;
    width: 100%;
}
.prd_outer_wrap2
{
	float: right;
    width: 80%;
}
.prd_inner_wrap1
{
	float: left;
    width: 19%;
    margin-right: 1%;
}
.prd_inner_wrap2
{
	float: left;
    width: 49%
}
.prd_inner_wrap3
{
	float: left;
	margin-right: 1%;
   	width: 30%
}
.prd_inner_wrap1 .ttl_wrap1
{
	float:left;
	background: none repeat scroll 0 0 #CCCCCC;
    font-size: 13px;
    font-weight: bold;
    padding: 3px 3px 2px 8px;
}
.prd_inner_wrap3 .ttl_wrap1
{
	float:left;
	background: none repeat scroll 0 0 #CCCCCC;
    font-size: 13px;
    font-weight: bold;
    padding: 3px 3px 2px 8px;
}
.prd_det .ttl_wrap1
{
	float:left;
	background: none repeat scroll 0 0 #CCCCCC;
    font-size: 13px;
    font-weight: bold;
    padding: 3px 3px 2px 8px;
}

.prd_inner_wrap2 .ttl_wrap1
{
	background: none repeat scroll 0 0 #CCCCCC;
    float: left;
    font-size: 13px;
    font-weight: bold;
    padding: 3px 9px 2px 8px;
}
.ttl_stk_wrap
{
	color: #FF0000;
    float: right;
    font-size: 13px;
    font-weight: bold;
} 
.upd_stk_prodbc
{
	font-size: 9px;float: right;
}
.btn_correction
{
	padding:0px !important;
	margin: -3px 0 0 5px;
	line-height: 18.4px !important;
	height: 21.4px !important;
}
.prd_inner_wrap2 .prd_inner_sub_wrap1 a
{
	
}
.prd_inner_wrap1 .prd_inner_sub_wrap2
{
	float: left;
    width: 100%;
}
.prd_inner_wrap2 .prd_inner_sub_wrap2
{
	float: left;
}
.prd_inner_wrap3 .prd_inner_sub_wrap2
{
	float: left;
    margin-top: 3%;
    width: 100%;
}
#processed_serial_nos span
{
	font-size: 11px;
    font-weight: bold;
}
#processed_from_date,#processed_to_date
{
	font-size:11px;width:60px;
}
#avl_from_date,#avl_to_date,#from_date,#to_date
{
	font-size:11px;width:60px;
}
.p_imei_filter_submit, .a_imei_filter_submit, .stocklog_filter_submit , .sales_det_submit
{
	font-size: 11px !important;
	height: 19.4px !important;
	padding: 0 0.92px  !important;
	line-height: 17.4px !important;
}

#sc_preview_avail_qty
{
	color: green;font-size: 10px;font-weight: bold;
}
#stk_correction_frm .label_wrap
{
	font-size: 13px;
    font-weight: bold;
    text-align: right;
}
#stk_correction_frm select
{
	width:270px;
}
#stk_correction_frm table td
{
	padding:7px 3px;
}
#dest_prod_stockdet_blk
{
	margin-top:10px;
}
.red {
    background: none repeat scroll 0 0 #FF6655;
    color: #FFFFFF;
    font-weight: bold;
    padding: 3px;
}
.green {
    background: none repeat scroll 0 0 #619C5C;
    color: #FFFFFF;
    font-weight: bold;
    padding: 3px;
}
#prod_fea_tab,#prod_imei_tab
{
	padding:6px;
	float: left;
}
.prd_title_wrap
{
	float: left;
    margin-top: 0 !important;
    width: 50%;
}
.prd_active_wrap
{
	float: right;
    text-align: right;
    width: 50%;
}
.no_linehgt td
{
	line-height: 0px !important;
}
.placeorder_btn
{
	background:#4AA02C !important;
	float:right;
	color:#FFFFFF !important;
}
.vendor_prd_wrap
{
	 float: left;
    font-size: 12px;
    max-height: 238px;
    overflow: auto;
    width: 100%;
}
#stat_frm_to
{
	font-size: 11px;
}
#stat_frm_to input
{
	font-size: 11px;
}

.stat_head
{
	margin-bottom:10px;
}
.total_stat_view
{
	height:150px;
	margin-top:10px;
}
.processed_stock_imei_ttl
{
	font-weight:bold;
}
</style>

<?php 
	$only_superadmin = $this->erpm->auth(true,true);
	$p=$product;
?>

<div class="container">
	<div class="fl_left" style="width: 100%">
		<div class="prd_active_wrap">
			<?php if($p['is_active']){ ?>
				<span class="notifications green">Active</span>
			<?php }else{ ?>	
				<span class="notifications red">Not Active</span>
			<?php } ?>
				
			<span style="font-size:19px"> | </span> 
			
			<?php if($p['is_sourceable']){ ?>
				<span class="notifications green">Sourceble</span>
			<?php }else{ ?>	
				<span class="notifications red">Not Sourceble</span>
			<?php } ?>
		</div>
		<h2 class="prd_title_wrap">
			<?=$p['product_name']?>
			<a href="<?=site_url("admin/editproduct/{$p['product_id']}")?>" target="_blank">
				<img src="<?php echo base_url().'images/pencil.png'?>">
			</a>
		</h2>
		<div class="fl_right" style="width: 50%">
		</div>	
	</div>
	
	<div class="prd_outer_wrap">
		<div class="prd_outer_wrap1">
			<div class="prd_det">
				<span class="prd_det_span"><b>Brand : </b><a href="<?=site_url("admin/viewbrand/{$p['bid']}")?>" target="_blank"><?=$p['brand']?></a></span>
				<span class="prd_det_span"><b>Category : </b><a href="<?=site_url("admin/viewcat/{$p['cid']}")?>" target="_blank"><?=$p['category']?></a></span>
				<span class="prd_det_span"><b>Size : </b><b class="prd_d"><?=$p['size']?></b></span>
				<span class="prd_det_span"><b>MRP(Rs.) : </b><b class="prd_d"><?=$p['mrp']?></b></span>
				<span class="prd_det_span"><b>VAT(%) : </b><b class="prd_d"><?=$p['vat']?></b></span>
			</div>
			
			<div class="prd_inner_wrap3">	
				<div class="prd_inner_sub_wrap1">
					<div class="ttl_wrap1">Product Stock Summary</div>
					<button class="button button-action button-tiny fl_right btn_correction">Correction</button>
					<span class="ttl_stk_wrap">Total Stock : <?=$p['stock']?></span>
					<div class="vendor_prd_wrap" style="max-height: 140px !important;">
						<table class="datagrid fl_left" width="100%">
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
													having sum(available_qty)>0 
													order by mrp asc ";
								?>
								
								<?php foreach($this->db->query($sql,$p['product_id'])->result_array() as $s){?>
									<tr>
										<td>
											<?php echo $s['pbarcode']?$s['pbarcode']:'--na--';?> 
												<?php if($this->erpm->auth(UPDATE_PRODUCT_BARCODE,true)){?>
													<a href="javascript:void(0)" class="upd_stk_prodbc" stk_id="<?php echo $s['stock_id'] ?>" >
															<img src="<?php echo base_url().'images/pencil.png'?>">
													</a>
											<?php }?>	
										</td>
										<td width="40" align="left"><span><?php echo round((float)$s['mrp'],2);?></span></td>
										<td><?php echo $s['rbname'];?></td>
										<td><?php echo round($s['s']);?></td>
									</tr>
								<?php }?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			
			<div class="prd_inner_wrap2">
				<div id="total_stat">
	    			<span class="stat_head ttl_wrap1"></span>
	    			<form id="stat_frm_to" method="post">
				        <div style="text-align: right">
				        	From : <input type="text" style="width: 90px;" id="date_from" value="<?php echo date('Y-m-d',time()-90*60*60*24)?>" />
				            To : <input type="text" style="width: 90px;" id="date_to" value="<?php echo date('Y-m-d',time())?>" /> 
				            <button class="button button-rounded button-action button-tiny sales_det_submit">Go</button>
				        </div>
				    </form>	
	    			<div class="total_stat_view">
	    			</div>
	    		</div>
			</div>
		</div>
		
		<div class="prd_outer_wrap1">
			<div class="prd_inner_wrap1">
				<div class="prd_inner_sub_wrap2" style="width:100%">
					<div class="ttl_wrap1">Linked Deals</div>
					<div class="vendor_prd_wrap">
						<table class="datagrid fl_left" width="100%">
							<thead>
								<tr><th>Sno</th><th>Deal</th><th>Qty</th><th>Type</th></tr>
							</thead>
							<tbody>
								<?php
									$list_deals_res = $this->db->query("select i.is_pnh,i.id,i.name,l.qty,i.price 
																		from m_product_deal_link l 
																		join king_dealitems i on i.id=l.itemid 
																		where l.product_id=?",$p['product_id']); 
									$i=1;
								?>
								
								<?php  
									if($list_deals_res->num_rows())
									{
										foreach($list_deals_res->result_array() as $d){
								?>
									<tr>
										<td><?=$i++?></td>
										<td><a href="<?=site_url("admin/deal/{$d['id']}")?>" target="_blank"><?=$d['name']?></a></td>
										<td><?=$d['qty']?></td>
										<!--<td><?=$d['price']?></td>-->
										<td><?=$d['is_pnh']?"PNH":"SNP"?></td>
									</tr>
								<?php } }else { ?>
									<tr>
										<td>
											No Linked Deals Found
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			
			<div class="prd_inner_wrap3">	
				<div class="prd_inner_sub_wrap1">
					<div class="ttl_wrap1">Sourceble Vendors</div>
					<div class="vendor_prd_wrap">
						<table class="datagrid fl_left" width="100%">
							<thead>
								<tr><th>Sno</th><th>Vendor Name</th></tr>
							</thead>
							<tbody>
								<?php
									$list_vendors_res = $this->db->query("select a.brand_id,c.vendor_name,b.vendor_id from m_product_info a 
									join m_vendor_brand_link b on b.brand_id=a.brand_id
									join m_vendor_info c on c.vendor_id=b.vendor_id
									join m_product_info p on p.brand_id=b.brand_id
									where p.product_id=?
									group by b.vendor_id
									order by vendor_name",$p['product_id']); 
									$j=1;
								?>
								
								<?php  
									if($list_vendors_res->num_rows())
									{
									foreach($list_vendors_res->result_array() as $v){
								?>
									<tr>
										<td><?=$j++?></td>
										<td><a href="<?=site_url("admin/vendor/{$v['vendor_id']}")?>" target="_blank"><?=$v['vendor_name']?></a></td>
									</tr>
								<?php } }else { ?>
									<tr>
										<td>
											No Vendors Found
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			
			<div class="prd_inner_wrap2">	
				<div class="prd_inner_sub_wrap2"  style="width:100%">
					<div class="ttl_wrap1">Purchase Log</div>
					<table id="ven_po_log" class="datagrid" width="100%">
						<thead>
							<tr>
								<th>Slno</th>
								<th>Date of Intake</th>
								<th>GRN ID</th>
								<th>Vendor Name</th>
								<th>Quantity</th>
								<th>MRP</th>
								<th>Purchase Price</th>
							</tr>
						</thead>
					
						<tbody>
						</tbody>
					</table>
					<div id="ven_log_pagination"></div>
				</div>
			</div>
		</div>
		
		<div class="prd_outer_wrap1">
			<div class="prd_inner_wrap2" style="margin-right: 1%;width:50%">
				<div class="ttl_wrap1">Stock Log</div>	
				<div id="prod_fea_tab" class="prd_inner_sub_wrap2" style="width:98%">
					<ul>
						<li><a href="#stock_log">Stock Log</a></li>
						<li><a href="#price_change_log">Price ChangeLog</a></li>
						<li><a href="#sourceable_changelog">Sourceable Change Log</a></li>
					</ul>
					<div id="stock_log">
						<h4 style="float: left;width:50%">Total : <span id="stock_log_ttl">0</span></h4>
						<div style="float: right;width:50%;text-align: right">
							<span>From : </span>
							<input type="text" id="from_date" value="<?php echo date('Y-m-d',time()-60*3600*24)?>" />
							<span>To : </span>
							<input type="text" id="to_date" value="<?php echo date('Y-m-d',time())?>" /> 
							<button class="button button-rounded button-action button-tiny stocklog_filter_submit">Go</button>
						</div>
						<table id="stock_log_list" class="datagrid" width="100%">
							<thead>
								<tr>
									<th width="1%">Slno</th>
									<th width="1%">Stock Intake/Invoice</th>
									<th width="1%">In / Out</th>
									<th width="1%">Stock Before</th>
									<th width="1%">Qty Affected</th>
									<th width="1%">Stock After</th>
									<th width="4%">Created By</th>
									<th width="10%">On</th>
									<th width="1%">Remarks</th></tr>
							</thead>
							
							<tbody>
							</tbody>
						</table>
						<div id="stock_log_pagination" style="display: none"></div>
					</div>
					<div id="price_change_log">
						<table class="datagrid fl_left" width="100%">
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
										<a href="<?=site_url("admin/viewgrn/{$pc['reference_grn']}")?>" target="_blank"><?=$pc['reference_grn']?></a>
										<?php }?>
										</td>
										<td><?=date("g:ia d/m/y",$pc['created_on'])?></td>
									</tr>
								<?php }?>
							</tbody>
						</table>
					</div>
					<div id="sourceable_changelog">
						<table class="datagrid fl_left" width="100%">
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
			<?php
				if($p['is_serial_required'])
				{
			?>
			<div class="prd_inner_wrap3" style="margin-right: 0%;width:49%">
				<div class="ttl_wrap1">Serial Numbers Log</div>	
				<div id="prod_imei_tab" class="prd_inner_sub_wrap1" style="width:98%">
					<ul>
						<li><a href="#avl_serial_nos">Available Serial Numbers</a></li>
						<li><a href="#processed_serial_nos">Processed Serial Numbers</a></li>
					</ul>
					
					<div id="processed_serial_nos">
						<h4 style="float: left;width:50%">Total : <span class="processed_stock_imei_ttl">0</span></h4>
						<div style="float: right;width:50%;text-align: right">
							<span>From : </span>
							<input type="text" id="processed_from_date" value="<?php echo date('Y-m-d',time()-60*3600*24)?>" />
							<span>To : </span>
							<input type="text" id="processed_to_date" value="<?php echo date('Y-m-d',time())?>" /> 
							<button class="button button-rounded button-action button-tiny p_imei_filter_submit">Go</button>
						</div>
						<table id="stock_imei_processed_list" class="datagrid" width="100%">
							<thead><tr><th>#</th><th>Serialno</th><th>GRNID</th><th>Status</th><th>Date</th></tr></thead>
							<tbody>
							
							</tbody>
						</table>
						<div id="stock_imei_processed_pagination" style="display: none"></div>
					</div>
					
					<div id="avl_serial_nos">
						<h4 style="float: left;width:50%">Total : <span class="avl_stock_imei_ttl">0</span></h4>
						<div style="float: right;width:50%;text-align: right">
							<span>From : </span>
							<input type="text" id="avl_from_date" value="<?php echo date('Y-m-d',time()-60*3600*24)?>" />
							<span>To : </span>
							<input type="text" id="avl_to_date" value="<?php echo date('Y-m-d',time())?>" /> 
							<button class="button button-rounded button-action button-tiny a_imei_filter_submit">Go</button>
						</div>
						<table id="stock_imei_available_list" class="datagrid" width="100%">
							<thead><tr><th>#</th><th>Serialno</th><th>GRNID</th><th>Status</th><th>Date</th></tr></thead>
							<tbody>
							
							</tbody>
						</table>
						<div id="stock_imei_available_pagination" style="display: none"></div>
					</div>
				</div>
			</div>
			<?php
				}
			?>
		</div>
	</div>
</div>

<div id="stock_correction_form" title="Stock Correction">
	<form id="stk_correction_frm" method="post" action="<?=site_url("admin/stock_correction")?>">
		<input type="hidden" name="pid" value="<?=$p['product_id']?>">
		<table cellpadding="2" cellspacing="0" width="100%">
			<tr>
				<td class="label_wrap" width="25%">Type :</td>
				<td style="vertical-align: middle;">
					<input type="radio" checked="checked" value="1" name="type" />IN 
					<input type="radio" name="type" value="0" />OUT  
				</td>	
			</tr>
			
			<tr>
				<td class="label_wrap">Stock Product :</td>
				<td>
					<select name="mrp_prod" class="mrp_prod">
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
				</td>
			</tr>
 			
 			<tr class="new_mrp_bc_block" style="display: none;">
 				<td class="label_wrap" width="25%">MRP :</td>
				<td style="vertical-align: middle;">
					<input type="text" name="n_mrp" size="6" value="" />
				</td>
 			</tr>
 		
 			<tr class="new_mrp_bc_block" style="display: none;">
 				<td class="label_wrap" width="25%">Barcode :</td>
				<td style="vertical-align: middle;">
					<input type="text" name="n_barcode"  size="16" value="" />
				</td>
 			</tr>
 			
 			<tr class="location_bytype">
				<td class="label_wrap">Location :</td>
				<td>
					<select name="loc" class="loc_wrap">
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
				<td class="label_wrap">Quantity :</td>
				<td><input type="text" name="corr" size=2>
					<span id="sc_preview_avail_qty">0 Available</span>
				</td>
			</tr>

			<tr class="stk_transfer_blk" id="stk_transfer_cnfrm" >
				<td class="label_wrap">Stock Transfer :</td>
				<td>
					<input type="checkbox" name="stk_transfer" value="1" checked="checked" >
				</td>
			</tr>
			
			<tr class="stk_transfer_blk">
				<td class="c label_wrap" valign="top">Transfer To :</td>
				<td>
					<select name="dest_prodid" class="dest_prodid" data-placeholder="Choose Transfer To">
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
						<select name="dest_prod_stockdet">
							<option value="">Choose Stock</option>
						</select>
						
						<div id="new_dest_stockdet" style="display: none">
							<table>
								<tr><td><b>Barcode</b></td><td><input type="text" size="24" name="dest_prod_newstk_bc"></td></tr>
								<tr><td><b>MRP</b></td><td><input type="text" size="13" name="dest_prod_newstk_mrp" value="0"></td></tr>
								<tr><td><b>Location</b></td><td><select name="dest_prod_newstk_rbid" style="width:200px"></select></td></tr>
							</table>
						</div>
						
						<div style="padding:5px 0px">
							<b>Available Qty :</b> <b id="dest_prod_stock_ttl">0</b>
						</div>
						
						<?php 
							if($p['is_serial_required'])
							{
						?>
							<div style="background: #FFF;padding:0px;width: 95%">
								<b style="padding: 5px 10px;display: block;background: #E3E3E3;width: 94%;">Scan/Enter Serial nos :</b>
								<ol id="stk_transfer_slnos" style="padding-left: 25px;padding-bottom: 10px;"></ol>
							</div>
						<?php 
							} 
						?>
					</div>
				</td>
			</tr>
			
			<tr>
				<td class="label_wrap">Message :</td>
				<td><textarea name="msg" style="width: 73%;"></textarea></td>
			</tr>
			
			<tr>
				<td class="label_wrap" colspan="2" style="text-align:center">
					<button type="submit" class="button button-action">Update</button>
				</td>
			</tr>
		</table>
	</form>	
</div>	

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
<?php }  ?>
<script>
$('.btn_correction').live('click',function(){
	
	$('#stock_correction_form').dialog('open');
	$('.mrp_prod').chosen();
	$('.loc_wrap').chosen();
	$('.dest_prodid').chosen();
});
	$('#stock_correction_form').dialog({
		modal:true,
		autoOpen:false,
		width:500,
		height:450,
		autoResize:true,
		open:function(){
		//$('.ui-dialog-buttonpane').find('button:contains("Update")').addClass('placeorder_btn');
		dlg = $(this);
	}
});
$("#date_from").datepicker();
$("#date_to").datepicker();
$("#processed_from_date").datepicker();
$("#processed_to_date").datepicker();
$("#avl_from_date").datepicker();
$("#avl_to_date").datepicker();
$('#from_date').datepicker();
$("#to_date").datepicker();

</script>
<script type="text/javascript">
		function reset_producttransfer()
		{
			$('select[name="dest_prod_stockdet"]').html('');
		}
		
		$('select[name="dest_prod_stockdet"]').change(function(){
			$('#dest_prod_stock_ttl').text($('option:selected',this).attr('available_qty'));
		});
		//$('select[name="dest_prod_stockdet"]').chosen();
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
						$('select[name="dest_prod_stockdet"]').chosen();
						
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
	<script type="text/javascript">
	$('select[name="mrp_prod"]').change(function(){

		$('.new_mrp_bc_block input').val('');
		$('.new_mrp_bc_block').hide();
		
		if($(this).val())
		{
			if($(this).val() == 'new')
			{
				$('input[name="corr"]').attr('aqty',0);
				$('#sc_preview_avail_qty').text("");

				$('.new_mrp_bc_block input').val('');
				$('.new_mrp_bc_block').show();
				
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
						imei_inp_list += '<li><input type="text" name="s_imeino[]" value=""  style="width:85%;margin-bottom:3px;"><span class="st_lookup_imei imei_stat_chk"></span></li>';
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
				$('.new_mrp_bc_block input').val('');
				$('.new_mrp_bc_block').show();
			}
			$('.loc_wrap').attr('disabled',false);
			$('.stk_transfer_blk').hide();
			$('.location_bytype').show();
		}else
		{
			$('select[name="mrp_prod"]').val('');
			$('#new_stock_prod').hide();	
			$('.new_mrp_bc_block input').val('');
			$('.new_mrp_bc_block').hide();
			$('.loc_wrap').attr('disabled',true);
			$('.location_bytype').hide();
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


	$('#prod_fea_tab').tabs();
	$('#prod_imei_tab').tabs();
	function load_product_stocklog(product_id,sdate,edate,pg)
	{
		$('#stock_log_list tbody').html('<tr><td colspan="6"><div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div></td></tr>');
		$.post(site_url+'/admin/jx_stocklog/'+product_id+'/'+sdate+'/'+edate+'/'+pg+'/25','',function(resp){
			$('#stock_log_list tbody').html(resp.log_data);
			if(resp.ttl*1 > resp.limit*1)
				$('#stock_log_pagination').html(resp.pagi_links).show();
			else
				$('#stock_log_pagination').html("").hide();
			
			$('#stock_log_ttl').html(resp.ttl);	
				
		},'json');
	}
	
	function load_processed_imeino(product_id,sdate,edate,pg)
	{
		$('#stock_imei_processed_list tbody').html('<tr><td colspan="6"><div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div></td></tr>');
		$.post(site_url+'/admin/jx_stockimeilist/'+'processed/'+product_id+'/'+sdate+'/'+edate+'/'+pg+'/25','',function(resp){
			$('#stock_imei_processed_list tbody').html(resp.imei_data);
			if(resp.imei_ttl*1 > resp.limit*1)
				$('#stock_imei_processed_pagination').html(resp.imei_pagi_links).show();
			else
				$('#stock_imei_processed_pagination').html("").hide();
			
			$('.processed_stock_imei_ttl').html(resp.imei_ttl);	
				
		},'json');
	}
	
	function load_available_imeino(product_id,sdate,edate,pg)
	{
		
		$('#stock_imei_available_list tbody').html('<tr><td colspan="6"><div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div></td></tr>');
		$.post(site_url+'/admin/jx_stockimeilist/'+'available/'+product_id+'/'+sdate+'/'+edate+'/'+pg+'/25','',function(resp){
			$('#stock_imei_available_list tbody').html(resp.imei_data);
			if(resp.imei_ttl*1 > resp.limit*1)
				$('#stock_imei_available_pagination').html(resp.imei_pagi_links).show();
			else
				$('#stock_imei_available_pagination').html("").hide();
			
			$('.avl_stock_imei_ttl').html(resp.imei_ttl);	
				
		},'json');
	}
	
	$('#stock_imei_available_pagination .log_pagination a').live('click',function(e){
		e.preventDefault();
		var a_st_date=$('#avl_from_date').val();
		var a_en_date=$('#avl_to_date').val();
		var url_prts = $(this).attr('href').split('/');
			pg = url_prts[url_prts.length-1];
			pg = pg*1;
		
		load_available_imeino(<?php echo $p['product_id'];?>,a_st_date,a_en_date,pg);
	});
	
	$('#stock_imei_processed_pagination .log_pagination a').live('click',function(e){
		e.preventDefault();
		var p_st_date=$('#processed_from_date').val();
		var p_en_date=$('#processed_to_date').val();
		var url_prts = $(this).attr('href').split('/');
			pg = url_prts[url_prts.length-1];
			pg = pg*1;
			
		load_processed_imeino(<?php echo $p['product_id'];?>,p_st_date,p_en_date,pg);
	});
	
	$('#stock_log_pagination .log_pagination a').live('click',function(e){
		e.preventDefault();
		var st_date=$('#from_date').val();
		var en_date=$('#to_date').val();
		var url_prts = $(this).attr('href').split('/');
			pg = url_prts[url_prts.length-1];
			pg = pg*1;
			
		load_product_stocklog(<?php echo $p['product_id'];?>,st_date,en_date,pg);
	});
	
	$('.p_imei_filter_submit').live('click',function(){
		var p_st_date=$('#processed_from_date').val();
		var p_en_date=$('#processed_to_date').val();
		load_processed_imeino(<?php echo $p['product_id'];?>,p_st_date,p_en_date,0);
	});
	$('.a_imei_filter_submit').live('click',function(){
		var a_st_date=$('#avl_from_date').val();
		var a_en_date=$('#avl_to_date').val();
		load_available_imeino(<?php echo $p['product_id'];?>,a_st_date,a_en_date,0);
	});
	
	$('.stocklog_filter_submit').live('click',function(){
		var st_date=$('#from_date').val();
		var en_date=$('#to_date').val();
		load_product_stocklog(<?php echo $p['product_id'];?>,st_date,en_date,0);
	});
	
	function load_ven_log(product_id,pg)
	{
		$('#ven_po_log tbody').html('<tr><td colspan="6"><div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div></td></tr>');
		$.post(site_url+'/admin/jx_prod_purchase_log_det/'+product_id+'/'+pg+'/5','',function(resp){
			$('#ven_po_log tbody').html(resp.log_data);
			if(resp.ttl*1 > resp.limit*1)
				$('#ven_log_pagination').html(resp.pagi_links).show();
			else
				$('#ven_log_pagination').html("").hide();
		},'json');
	}
	
	$('#ven_log_pagination .log_pagination a').live('click',function(e){
		e.preventDefault();
		var url_prts = $(this).attr('href').split('/');
		var pid=$('.log_pagination').attr('prodid');
			pg = url_prts[url_prts.length-1];
			pg = pg*1;
		load_ven_log(<?php echo $p['product_id'];?>,pg);
	});
	
	
	$( document ).ready(function(){
		var p_st_date=$('#processed_from_date').val();
		var p_en_date=$('#processed_to_date').val();
		var a_st_date=$('#avl_from_date').val();
		var a_en_date=$('#avl_to_date').val();
		var st_date=$('#from_date').val();
		var en_date=$('#to_date').val();
		load_product_stocklog(<?php echo $p['product_id'];?>,st_date,en_date,0);
		load_ven_log(<?php echo $p['product_id'];?>,0);
		<?php if($p['is_serial_required']){?>
			load_available_imeino(<?php echo $p['product_id'];?>,a_st_date,a_en_date,0);
			load_processed_imeino(<?php echo $p['product_id'];?>,p_st_date,p_en_date,0);
		<?php } ?>
		
		total_sales();
	});
	
	$("#stat_frm_to").bind("submit",function(e){
		e.preventDefault();
		total_sales();
	});
	
	function total_sales()
	{
		var prodid ="<?php echo $p['product_id'];?>";
		var start_date= $('#date_from').val();
		var end_date= $('#date_to').val();
		$('.stat_head').html("<h4 style='margin:0px !important'>Total Sales</h4>");
		$('#total_stat .total_stat_view').html('<div align="center" style="margin-top:10px"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>' );
		$.getJSON(site_url+'/admin/jx_product_sales/'+prodid+'/'+start_date+'/'+end_date,'',function(resp){
			if(resp.summary == 0)
			{
				$('#total_stat .total_stat_view').html("<div class='br_alert_wrap' style='padding:40px 0px'>No Sales statisticks found between "+start_date+" and "+end_date+"</div>" );	
			}
			else
			{
				// reformat data ;
				if(resp.date_diff <= 31)
			  	{
			  		var interval = 1000000;
			    }
				else
				{
					var interval = 2500000;
				}
				$('#total_stat .total_stat_view').empty();
				plot2 = $.jqplot('total_stat .total_stat_view', [resp.summary], {
			       	
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
					  axes:{
				        xaxis:{
				          renderer: $.jqplot.CategoryAxisRenderer,
				          	label:'Date',
					          labelOptions:{
					            fontFamily:'Arial',
					            fontSize: '14px'
					          },
					          labelRenderer: $.jqplot.CanvasAxisLabelRenderer
				        },
				        yaxis:{
					          min : 0,
							  tickInterval : interval,
							  label:'Total Sales in Rs',
					          labelOptions:{
					            fontFamily:'Arial',
					            fontSize: '14px'
					          },
					          labelRenderer: $.jqplot.CanvasAxisLabelRenderer
					        }
				      }
				});
			}
		});
	}
</script>
