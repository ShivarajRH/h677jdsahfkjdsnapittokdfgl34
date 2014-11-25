<?php 
$only_superadmin = $this->erpm->auth(true,true);
$p=$product;
// get damage qty
$dmg_qty = @($this->db->query("select sum(available_qty)  as t     
													from t_stock_info  a 
													join m_storage_location_info b on b.location_id = a.location_id 
													join m_rack_bin_info c on c.id = a.rack_bin_id  
													where product_id=? and c.is_damaged=1 
													having sum(available_qty)>=0 ",$p['product_id'])->row()->t)*1;
// Partner transfer reserved
$part_reserv_res=$this->db->query("SELECT si.stock_id,SUM(pstk.qty) AS reserv_qty,si.mrp,si.location_id,si.rack_bin_id,CONCAT(ri.rack_name,ri.bin_name) AS rbname,ri.is_damaged,IFNULL(si.product_barcode,'') AS pbarcode
										FROM t_partner_reserved_batch_stock pstk
										JOIN t_stock_info si ON si.stock_id=pstk.stock_info_id
										JOIN m_rack_bin_info ri ON ri.id = si.rack_bin_id
										WHERE pstk.`status`=0 AND pstk.transfer_option=1 AND pstk.product_id=?",$p['product_id']);

$part_reserv_qty=0;
if($part_reserv_res->num_rows())
{
	$part_reserv=$part_reserv_res->row_array();
	$part_reserv_qty=$part_reserv['reserv_qty'];
	$part_pbarcode=$part_reserv['pbarcode'];
	$part_rbname=$part_reserv['rbname'];
}
// Partner return reserved
$part_return_reserv_res=$this->db->query("SELECT si.stock_id,SUM(pstk.qty) AS reserv_qty,si.mrp,si.location_id,si.rack_bin_id,CONCAT(ri.rack_name,ri.bin_name) AS rbname,ri.is_damaged,IFNULL(si.product_barcode,'') AS pbarcode
										FROM t_partner_reserved_batch_stock pstk
										JOIN t_stock_info si ON si.stock_id=pstk.stock_info_id
										JOIN m_rack_bin_info ri ON ri.id = si.rack_bin_id
										WHERE pstk.`status`=0 AND pstk.transfer_option=2 AND pstk.product_id=?",$p['product_id']);
$part_return_reserv_qty=0;
if($part_return_reserv_res->num_rows())
{
	$part_reserv=$part_return_reserv_res->row_array();
	$part_return_reserv_qty=$part_reserv['reserv_qty'];
	$part_pbarcode=$part_reserv['pbarcode'];
	$part_rbname=$part_reserv['rbname'];
}
$ttl_stk = @($this->db->query("select sum(available_qty)  as t
		from t_stock_info  a
		join m_storage_location_info b on b.location_id = a.location_id
		join m_rack_bin_info c on c.id = a.rack_bin_id
		where product_id=? and c.is_damaged=0
		having sum(available_qty)>=0 ",$p['product_id'])->row()->t)*1;


//get attr det
$attr_det=$this->db->query("select a.attr_name,pa.attr_value from m_product_attributes as pa
					join m_attributes a on a.id=pa.attr_id
					where pid=?",$p['product_id'])->result_array();
?>
<style>
#content .rightcont {
	background: #fafafa !important;
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
	min-height: 265px;
	background: #FFF;
}
.prd_inner_wrap2
{
	float: left;
    width: 49%;
    background: #FFF;
}
.prd_inner_wrap3
{
	float: left;
	margin-right: 1%;
   	width: 30%;
   	background: #FFF;
}
.prd_inner_wrap1 .ttl_wrap1
{
	float: left;
	background: none repeat scroll 0 0 #CCC;
	font-size: 13px;
	font-weight: bold;
	padding: 5px 10px;
	background: orange;
	color: #FFF;
	border-radius: 5px 5px 0px 0px;
}
.prd_inner_wrap3 .ttl_wrap1
{
	float: left;
	background: none repeat scroll 0 0 #CCC;
	font-size: 13px;
	font-weight: bold;
	padding: 5px 10px;
	background: #A8AEB1;
	color: #FFF;
	border-radius: 5px 5px 0px 0px;
}
.prd_det .ttl_wrap1
{
	float: left;
	background: none repeat scroll 0 0 #CCC;
	font-size: 13px;
	font-weight: bold;
	padding: 5px 10px;
	background: #A8AEB1;
	color: #FFF;
	border-radius: 5px 5px 0px 0px;
}

.prd_inner_wrap2 .ttl_wrap1
{
	float: left;
	background: none repeat scroll 0 0 #CCC;
	font-size: 13px;
	font-weight: bold;
	padding: 5px 10px;
	background: #A8AEB1;
	color: #FFF;
	border-radius: 5px 5px 0px 0px;
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

#reserved_serial_nos span
{
	font-size: 11px;
    font-weight: bold;
}
#reserved_from_date,#reserved_to_date
{
	font-size:11px;width:60px;
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
	height:200px;
	margin-top:10px;
	padding:10px;
}
.processed_stock_imei_ttl
{
	font-weight:bold;
}
</style>

<div class="container">
	<div class="fl_left" style="width: 100%">
		<div class="prd_active_wrap">
			
			<?php if($p['end_of_life'] == 1){ ?>
				<span class="notifications red">Product Closed</span>
			<?php }else{?>
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
			<?php } ?>
		</div>
		<h2 class="prd_title_wrap">
			<?=$p['product_name']?>
			<?php if($this->erpm->auth(UPDATE_PRODUCT_DEAL_ROLE,true)){?>
				<a href="<?=site_url("admin/editproduct/{$p['product_id']}")?>" target="_blank">
					<img src="<?php echo base_url().'images/pencil.png'?>">
				</a>
			<?php } ?>
		</h2>
		<div class="fl_right" style="width: 50%">
		</div>	
	</div>
	
	<div class="prd_outer_wrap">
		<div class="prd_outer_wrap1">
			<div class="prd_det">
				<span class="prd_det_span"><b>Brand : </b><a href="<?=site_url("admin/viewbrand/{$p['brand_id']}")?>" target="_blank"><?=$p['brand']?></a></span>
				<span class="prd_det_span"><b>Category : </b><a href="<?=site_url("admin/viewcat/{$p['cat_id']}")?>" target="_blank"><?=$p['cat']?></a></span>
				<span class="prd_det_span"><b>Size : </b><b class="prd_d"><?=$p['size']?></b></span>
				<span class="prd_det_span"><b>MRP(Rs.) : </b><b class="prd_d"><?=$p['mrp']?></b></span>
				<span class="prd_det_span"><b>VAT(%) : </b><b class="prd_d"><?=$p['vat']?></b></span>
				<?php 
					if($attr_det)
					{
						foreach($attr_det as $a)
						{
							?>
							<span class="prd_det_span"><b><?php echo $a['attr_name']; ?> Attribute  : </b><b class="prd_d"><? echo $a['attr_value']; ?></b></span>
							<?php 
						}
					}
				?>
			</div>
			
			<div class="prd_inner_wrap3">	
				<div class="prd_inner_sub_wrap1">
					<div class="ttl_wrap1">Product Stock Summary</div>
					
					<div class="clear" style="padding:5px;background: #f1f1f1;overflow: hidden;">
						<span class="fl_left" style="margin-left:2px">Total Stock : <?php echo $ttl_stk;?></span>
<?php 
							if($dmg_qty)
							{
?>
								<span  class="addrow_tooltip fl_left" title="Damaged Quantity" style="color:red;margin-left:6px;font-size: 10px;">Damaged:(<?php echo $dmg_qty;?>)</span>
<?php
							}
							if($part_reserv_qty>0)
							{
?>
								<span  class="addrow_tooltip fl_left" title="Partner Stock Transfer Reserved,Rackbin:<?=$part_rbname;?>,Barcode:<?=$part_pbarcode;?>" style="color:#E72CC2;margin-left:6px;font-size: 11px;">Reserved:(<?php echo $part_reserv_qty;?>)</span>
<?php 
							}
							if($part_return_reserv_qty>0)
							{
?>
								<span  class="addrow_tooltip fl_left" title="Partner Return Stock Reserved,Rackbin:<?=$part_rbname;?>,Barcode:<?=$part_pbarcode;?>" style="color:#03D70E;margin-left:6px;font-size: 11px;">Return Reserved:(<?php echo $part_return_reserv_qty;?>)</span>
<?php 
							}
							if($this->erpm->auth(STOCK_CORRECTION,true)){?>
								<div class="fl_right"><button class="button button-action button-tiny fl_right btn_correction">Correction</button></div>
						<?php } ?>
					</div>
					<div class="vendor_prd_wrap" style="max-height: 200px !important;">
						<table class="datagrid fl_left" width="100%">
							<thead>
								<th><b>Barcode</b></th>
								<th><b>MRP</b></th>
								<th><b>Rackbin</b></th>
								<th><b>Stock</b></th>
								<th><b>Expiry</b></th>
							</thead>
							<tbody>
								<?php 
									$sql = "select sum(available_qty) as s,mrp,
													a.location_id,a.rack_bin_id,
													concat(c.rack_name,bin_name) as rbname,c.is_damaged,
													ifnull(product_barcode,'') as pbarcode,
													a.stock_id,date_format(a.expiry_on, '%d-%m-%Y') as expiry_on,offer_note     
													from t_stock_info  a 
													join m_storage_location_info b on b.location_id = a.location_id 
													join m_rack_bin_info c on c.id = a.rack_bin_id  
													where product_id=? 
													group by a.stock_id,mrp,pbarcode,a.location_id,a.rack_bin_id,a.expiry_on,a.offer_note  
													order by s desc,mrp asc ";
								?>
								
								<?php foreach($this->db->query($sql,$p['product_id'])->result_array() as $s){
								if(($s['is_damaged'] !=1)  || ($s['is_damaged'] ==1 && round($s['s']) > 0))
								{	
									?>
									<tr <?php ($s['is_damaged'] != '1')?'':'style="background:rgb(248, 143, 143)"'?> >
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
										<td width="90">
											<?php if($s['expiry_on'] == '00-00-0000' || empty($s['expiry_on'])){echo '-na-';} else { echo $s['expiry_on'];} ?>
											<?php if($this->erpm->auth(UPDATE_PRODUCT_BARCODE,true)){?>	
												<a href="javascript:void(0)" class="upd_expiry" stk_id="<?php echo $s['stock_id'] ?>" >
													<img src="<?php echo base_url().'images/pencil.png'?>">
												</a>
											<?php } ?>	
										</td>
									</tr>
									<?php 
										if(!empty($s['offer_note']))
										{
									?>
										<tr><td colspan="10"><?php echo $s['offer_note'];?></td></tr>	
									<?php 		
										}
									?>
									
								<?php } }?>
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
			
			<div class="prd_inner_wrap3">
				<div class="prd_inner_sub_wrap1" style="width:100%">
					<div class="ttl_wrap1" >Linked Deals</div>
					<div class="vendor_prd_wrap">
						<table class="datagrid fl_left" width="100%">
							
								<?php
									$list_deals_res = $this->db->query("select i.is_pnh,i.id,i.name,l.qty,i.price,i.member_price
																		from m_product_deal_link l 
																		join king_dealitems i on i.id=l.itemid 
																		where l.product_id=?",$p['product_id']); 
									$i=1;
								?>
								
								<?php  
									if($list_deals_res->num_rows())
									{
								?>
									<thead>
										<tr><th>Deal For</th><th>Sno</th><th>Deal</th><th>Qty</th><th>OfferPrice</th><th>MemberPrice</th></tr>
									</thead>
									<tbody>
								<?php 		
										foreach($list_deals_res->result_array() as $d)
										{
								?>
									<tr>
										<td><?=$i++?></td>
										<td><?=$d['is_pnh']?"PNH":"SNP"?></td>
										<td><a href="<?=site_url("admin/deal/{$d['id']}")?>" target="_blank"><?=$d['name']?></a></td>
										<td><?=$d['qty']?></td>
										<td><?=  formatInIndianStyle($d['price']);?> <small>(Rs)</small></td>
										<td><?=  formatInIndianStyle($d['member_price']);?> <small>(Rs)</small></td>
									</tr>
								<?php 
										} 
									}else 
									{ 
										$list_grpdeals_res = $this->db->query("select i.is_pnh,i.id,i.name,l.qty,i.price
												from m_product_group_deal_link l
												join products_group_pids g on g.group_id = l.group_id 
												join king_dealitems i on i.id=l.itemid
												where g.product_id=?",$p['product_id']);
										
										if($list_grpdeals_res->num_rows())
										{
											?>
											<thead>
												<tr><th>Sno</th><th>Group Deal</th><th>Type</th></tr>
											</thead>
											<tbody>
											<?php
											
											foreach($list_grpdeals_res->result_array() as $d)
											{
								?>
												<tr>
													<td><?=$i++?></td>
													<td><a href="<?=site_url("admin/deal/{$d['id']}")?>" target="_blank"><?=$d['name']?></a></td>
													<td><?=$d['is_pnh']?"PNH":"SNP"?></td>
												</tr>
								<?php
											}
										}
										else
										{	
								?>
									<tr>
										<td>
											No Linked Deals Found
										</td>
									</tr>
								<?php 
										} 
									}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			
								
			
			<div class="prd_inner_wrap2">	
				<div class="prd_inner_sub_wrap2"  style="width:100%">
					<div class="ttl_wrap1">Purchase Log</div>
					<?php 
						if($this->erpm->auth(PURCHASE_ORDER_ROLE,true))
						{
					?>
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
					<?php } ?>
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
								<tr><th>On</th><th>Changed to</th><th>By</th><th>Remarks</th></tr>
							</thead>
							
							<tbody>
								<?php foreach($this->db->query("select s.is_sourceable,s.created_on,u.name as `by`,s.remarks from products_src_changelog s join king_admin u on u.id=s.created_by where s.product_id=? order by s.id desc",$p['product_id'])->result_array() as $c){?>
								<tr><td><?=date("g:ia d/m/y",$c['created_on'])?></td><td><?=$c['is_sourceable']=="1"?"SOURCEABLE":"NOT SOURCEABLE"?></td><td><?=$c['by']?></td><td><?=$c['remarks'];?></td></tr>
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
						<li><a href="#reserved_serial_nos">Reserved Serial Numbers</a></li>
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
							<thead><tr><th>#</th><th>Serialno</th><th>MRP</th><th>Rack Name</th><th>GRNID</th><th>Status</th><th>Date</th></tr></thead>
							<tbody>
							
							</tbody>
						</table>
						<div id="stock_imei_processed_pagination" style="display: none"></div>
					</div>
					
					<div id="reserved_serial_nos">
						<h4 style="float: left;width:50%">Total : <span class="reserved_stock_imei_ttl">0</span></h4>
						<div style="float: right;width:50%;text-align: right">
							<span>From : </span>
							<input type="text" id="reserved_from_date" value="<?php echo date('Y-m-d',time()-60*3600*24)?>" />
							<span>To : </span>
							<input type="text" id="reserved_to_date" value="<?php echo date('Y-m-d',time())?>" /> 
							<button class="button button-rounded button-action button-tiny p_imei_filter_submit">Go</button>
						</div>
						<table id="stock_imei_reserved_list" class="datagrid" width="100%">
							<thead><tr><th>#</th><th>Serialno</th><th>MRP</th><th>Rack Name</th><th>GRNID</th><th>Batch</th><th>Transaction</th><th>Status</th><th>Date</th></tr></thead>
							<tbody>
							
							</tbody>
						</table>
						<div id="stock_imei_reserved_pagination" style="display: none"></div>
					
					</div>
					
					
					<div id="avl_serial_nos">
						<h4 style="float: left;width:50%">Total : <span class="avl_stock_imei_ttl">0</span></h4>
						<div style="float: right;width:50%;text-align: right">
							<span>From : </span>
							<input type="text" id="avl_from_date" value="2012-01-01" />
							<span>To : </span>
							<input type="text" id="avl_to_date" value="<?php echo date('Y-m-d',time())?>" /> 
							<button class="button button-rounded button-action button-tiny a_imei_filter_submit">Go</button>
						</div>
						<table id="stock_imei_available_list" class="datagrid" width="100%">
							<thead><tr><th>#</th><th>Serialno</th><th>MRP</th><th>Rack Name</th><th>GRNID</th><th>Status</th><th>Date</th></tr></thead>
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
							$sql_stkmrpprod = "select  a.stock_id,a.location_id,a.rack_bin_id,concat(rack_name,bin_name) as rb_name,a.product_barcode,a.mrp,sum(a.available_qty) as available_qty,concat('Rs',ifnull(a.mrp,0),' - ',rack_name,bin_name,' - ',if(length(a.product_barcode),a.product_barcode,'NO BARCODE'),' - ',IFNULL(a.expiry_on,'')) as stk_prod,expiry_on 
												from t_stock_info a
												join m_rack_bin_info b on a.rack_bin_id = b.id 
												where a.product_id = ? 
												group by a.mrp,a.location_id,a.rack_bin_id,a.product_barcode,a.stock_id  
												order by a.mrp asc 
											";
							$stkmrpprod_res = $this->db->query($sql_stkmrpprod,$p['product_id']);
							if($stkmrpprod_res->num_rows())
							{
								foreach($stkmrpprod_res->result_array() as $stkmrppro)
								{
						?>
								<option stk_id="<?php echo $stkmrppro['stock_id'];?>" avail_qty="<?php echo $stkmrppro['available_qty'];?>" value="<?php echo $stkmrppro['product_barcode'].'_'.$stkmrppro['mrp'].'_'.$stkmrppro['location_id'].'_'.$stkmrppro['rack_bin_id'].'_'.$stkmrppro['expiry_on'].'_'.$stkmrppro['stock_id'] ?>"><?php echo $stkmrppro['stk_prod']?></option>
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
							/*
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
							*/
							$similar_prods_res = $this->db->query('select a.product_id,a.product_name 
															from m_product_info a
															join (select product_id,brand_id,product_cat_id from m_product_info where product_id = ? ) as b 
															on a.brand_id = b.brand_id and a.product_cat_id = b.product_cat_id  
															where a.is_active = 1  
														order by a.product_id',array($p['product_id']));

							if($similar_prods_res->num_rows())
							{
								foreach($similar_prods_res->result_array() as $similar_prod)
								{
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
								<tr><td><b>Expiry</b></td><td><input type="text" name="dest_prod_newstk_expiry" id="exp_dates"></td></tr>
								<!--<tr><td><b>Offer Note</b></td><td><input type="text" name="dest_prod_newstk_offer"></td></tr>-->
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
				<td class="label_wrap" valign="top">Message :</td>
				<td><textarea name="msg" style="width: 98%;min-height:130px"></textarea></td>
			</tr>
			
			<tr>
				<td class="label_wrap" colspan="2" style="text-align:right">
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
	
	<div id="upd_stk_expiry_dlg" title="Update Product Expiry">
		<form id="upd_stk_expiry_frm" method="post">
			<table>
				<tr><td><b>Old Expiry</b></td><td><input id="upd_old_expiry" class="inp" type="text" disabled="disabled"></td></tr>
				<tr><td><b>New Expiry</b></td><td><input id="upd_new_expiry" class="inp" ><input type="submit" value="Submit" style="visibility: hidden;" ></td></tr>
			</table>
		</form>	
	</div>
<?php }  ?>
<script>
var product_id=<?php echo $p['product_id']; ?>;
var is_serial_required =<?php echo $p['is_serial_required']; ?>;
</script>
<script type="text/javascript" src="<?=base_url()?>/min/index.php?g=viewproduct_js&<?php echo strtotime(date('Y-m-d'));?>&1=1"></script>