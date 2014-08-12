<style>
	.leftcont{display: none}
	.highlight_row td{ background: #F7E8E5 !important; }
	.pagination a{padding:5px 10px;background: #dfdfdf;color: #000;font-weight: bold;font-size: 13px;}
	
	.published td{background: rgba(0, 128, 0, 0.2) !important}
	.unpublished td{background: rgba(205, 0, 0, 0.2) !important}
	.small { font-size: 9px; }
	.updt_brand_margin { margin: 6px 5px 6px 0; }
	.mp_percent_block { padding: 4px;
						color: #fdfcfa;
						background-color: #fdad6a;
						width: 60px;
						border-radius: 9px;
						text-align: center; }
	.ui-widget .ui-widget {
		font-size: 1em;
		background-color: cadetblue;
	}
	.page_topbar select { width: 200px; }
	.total_overview { margin-right:27%;  }
	.total_overview .inlog_1 {background-color: #B68362; color:#FFFFFF;
						float: left;
						font-size: 16px;
						padding: 5px;
						text-align: center;
						margin: 0 0 0 8px; }
	.deals_display_log .inlog_1 { background-color: #85658B; color:#FFFFFF;
						float: left;
						font-size: 16px;
/*						width: 25%; */
						padding: 5px;
						text-align: center;
						margin: 0 0 0 8px;}
	.pagination { float: right; }
/*	.inlog_act { color: #006600; }
	.inlog_dct { color: #fdad6a; }*/
	.stk_log1 { font-weight: bold; color:#187541;cursor: pointer; } .stk_log2 { font-weight: bold; color:#991E19;cursor: pointer; }
	/*===========< Default filters style - Shivarj >==============*/
	.filters_block .filter select { width:180px;margin-top:5px;font-size: 12px; }
	/*===========< END Default filters style - Shivarj >==============*/
</style>
<div class="page_wrap container" style="width: 98%;">
	<h2 class="page_title">Update Member price</h2>
	
	<div class="page_topbar" >
		<form onsubmit="return filter_form_submit();">
		<div class="filters_block">
			<!--<div class="page_action_buttonss fl_right" align="right">-->
			<div class="filter">
				<label style="margin-right: 18px;">Menu :</label> 
				<select name="sel_menu" id="sel_menu">
					<option value="0">All</option>
					<?php foreach($this->db->query("select * from pnh_menu order by name asc")->result_array() as $i=>$m){?>
								<option value="<?=$m['id']?>" <?=($i==0) ? "selected":""; ?>><?=$m['name']?></option>
					<?php }?>
				</select>
				<br>
				<label style="margin-right: 15px;">Brand :</label>
				<select name="sel_brandid">
					<option value="0">All</option>
					<?php
	//					foreach($brand_list as $brand)
	//					{ echo '<option value="'.$brand['id'].'">'.$brand['name'].'</option>';
					?>
				</select>
				<br>
				<label style="margin-right: 0px;">Category :</label> <select name="sel_catid"><option value="">All</option></select>
			</div>
			
			<div class="filter">

					<label style="margin-right: 5px;">Search :</label>	<input type="input" value="" name="search_key" class="inp search_key" size="26" placeholder="ID(PNHID), itemid"/>
					
					<br/>
					<label style="margin-right: 8px;">Status :</label>
					<select name="show_all_deals" class="inp show_all_deals">
						<option value="0">All</option>
						<option value="1" selected>Published</option>
						<option value="2">Unpublished</option>
					</select>
					<br>
					<label style="margin-right: 10px;">Sort By:</label>
					<select name="sel_list_deal" id="">
						<option value="0">All</option>
						<option value="1" selected>Most Sold</option>
						<option value="2">Recently Sold</option>
						<option value="3">Newly Added</option>
						<option value="4">Name</option>
						<!--<option value="3">By MP quantity</option>-->
					</select>
			</div>
			
			
			<div class="filter">
				<label style="margin-right: 10px;">Show Deep Discounts:</label>	<input type="checkbox" value="1" name="show_deep_disc" class="inp show_deep_disc" onchange="return change_deep_discount(this);"/>
				<br>
				<div class="updt_brand_margin"></div>
				<div class="mp_end_deep_discount" style="margin-top: 0px;"></div>
			</div>

			<div class="filter">
<!--				<input type="reset" value="Reset Filters" class="reset_filters button button-caution" /><br/>-->
				<input type="submit" value="Go" class="button button-action" />
			</div>
		</div>
		</form>
		
	</div>
	
	<!--<div style="clear:both">&nbsp;</div>-->
	
	<div id="deal_list" class="page_content">
		<div class="pagination fl_right"></div>
		<div class="total_overview fl_right"></div>
		<div class="deals_display_log fl_left"></div>
		
		<div class="clear">&nbsp;</div>
		
		<table  class="datagrid" width="100%">
			<thead>
				<th width="2%">Slno</th>
				<th width="4%"><b>PNHID / ITEMID</b></th>
				<th width="30%"><b>Name</b></th>
				<th width="4%"><b>MRP</b> <small>(Rs)</small></th>
				<th width="6%"><b>Offer/DP Price</b> <small>(Rs)</small></th>
				<th width="6%"><b>Stock</b></th>
				<th width="35%"><b>Update Member Price</b></th>
				<th width="6%"><b>Product Note</b></th>
				<th width="3%">Deep Discount?</th>
				<th width="20%">Offer Between</th>
				<th width="17%">In Tab</th>
				<th width="12%"><b>Product Status</b></th>
			</thead>
			<tbody>
			</tbody>
		</table>
		
		<div class="pagination" align="right"></div>
	</div>
</div>
<!--=========================< DIABLOG BOX CODE STARTS >=========================-->
<div style="display:none;">
	<div id="dlg_offer_details_blk">
		<p>Please select validity date range:</p>
		<table width="100%">
			<tr>
				<td>Date From:</td>
				<td><input type="text" name="offer_from" class="offer_from"></td>
			</tr>
			<tr>
				<td>Date To:</td>
				<td><input type="text" name="offer_to" class="offer_to"></td>
			</tr>
		</table>
	</div>
	
	<div id="dlg_view_m_price_chng_log">
		<p>
			<span class="show_log"></span>
		</p>
		<!--<p>Please select validity date range:</p>-->
		<table width="100%" class="datagrid datasorter">
			<thead>
			<tr>
				<th>#</th>
				<th>Member Price</th>
				<th>All Franchise Max To Sell</th>
				<th>MP Fran order Qty</th>
				<th>MP Mem order Qty</th>
<!--				<th>Is Offer</th>
				<th>Offer Between</th>
				<th>Offer Validity</th>-->
				<th>Total Sale</th>
				<th>Modified By</th>
				<th>Modified On</th>
				<th>Is Active</th>
				
			</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<p>
			<span class="log_pagination"></span>
		</p>
	</div>
	
	<div id="dlg_memberprice_note_update">
		<p>Please enter Member Price Note:</p>
		<table width="100%">
			<tr>
				<!--<td>Enter Note:</td>-->
				<td><textarea class="memberprice_note" cols="17" rows="8" style=" width: 100%;"></textarea></td>
			</tr>
		</table>
	</div>
	
	<div id="mp_bulkupdate_percentage_block">
		
		<table width="100%">
			<tr>
				<!--<td>Enter Percentage <span class="req">*</span> :</td>-->
				<td>Bulk Member Price Percentage :</td><td><input type="text" class="mp_update_percent" style=" width: 80px;"/>%</td>
			</tr>
			<tr><td>Default Shipsin Time :</td>
				<td>
						<select tabindex="shipsin22" class="mp_offer_shipsin" style="width:103px;" value="">
								<option value="0"> -- Not Set -- </option>
								<option value="24-48 Hrs" >24-48</option>
								<option value="48-72 Hrs" >48-72</option>
								<option value="72-96 Hrs" >72-96</option>
						</select>
				</td>
			</tr>
			<tr><td><div class="show_update_sts"></div></td></tr>
		</table>
	</div>
	<div id="dlg_sourceable_status_cng">
		<table width="100%">
			<tr align="left">
				<th>Reason to change:</th>
			</tr>
				<tr><td>
						<textarea class="cng_reason" name="cng_reason" cols="7" rows="5" style="width: 100%;"></textarea>
				</td></tr>
		</table>
	</div>
</div>
<!--=========================< DIABLOG BOX CODE ENDS >=========================-->
<script type="text/javascript" src="<?=base_url()?>/min/index.php?g=member_price_js&<?php echo strtotime(date('Y-m-d'));?>&1=1"></script>
