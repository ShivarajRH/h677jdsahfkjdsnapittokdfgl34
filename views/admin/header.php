<?php 

	$pnh_exec_const_val=$this->db->query("select value from user_access_roles where const_name='PNH_EXECUTIVE_ROLE'")->row()->value;
	

	$tmenu=array();
	if(isset($menu))
		$tmenu=$menu;
	$menu=array();
	$user=$this->session->userdata("admin_user");
	$is_pnh_exec_only=false;
	if($user['access']==$pnh_exec_const_val)
			$is_pnh_exec_only=true;
	$sub="prod_menu";
/*
	$subs=array("prod"=>"Products","selling"=>"Selling","stock"=>"Warehousing","control"=>"Website Control","marketing"=>"Marketing","accounting"=>"Accounting","crm"=>"Customer Relationship","admin"=>"Admin Control");
	
	$menu["prod"]=array("menu"=>"Menu","categories"=>"Categories","deals"=>"Deals","brands"=>"Brands","products"=>"Products","freesamples"=>"Free Samples","vendors"=>"Vendors","variants"=>"Variants");
	$menu["selling"]=array("orders"=>"Orders","offline"=>"Offline Orders","callcenter"=>"callcenter","batch_process"=>"Shipment batch process","pending_batch_process"=>"Pending Shipment batch process","outscan"=>"Outscan Order","courier"=>"Courier");
	$menu['stock']=array("purchaseorders"=>"View POs","purchaseorder"=>"Purchase Order","po_product"=>"PO Productwise","apply_grn"=>"Update Stock","barcode"=>"Barcode","storage_locs"=>"Storage Locations","rackbins"=>"Rack & Bins");
	$menu['marketing']=array("featured_newsletter"=>"Newsletter","announcements"=>"Announcements","stats"=>"Stats","reports/kfile"=>"Generate K file","reports/order_summary"=>"Order Summary","coupons"=>"Coupons","cashback_campaigns"=>"Cashback Campaigns","pointsys"=>"Loyalty Points","headtotoe"=>"Head to toe");
	$menu['accounting']=array("vouchers"=>"Vouchers","create_voucher"=>"Create Voucher","pending_pay_grns"=>"Ready for payment","pending_grns"=>"Pending Unaccounted GRNs");
	$menu['crm']=array("support"=>"Customer Support","users"=>"Site Users","review"=>"Reviews","corporate"=>"Corporates","callcenter"=>"Call Center","tools"=>"Tools");
	$menu['control']=array("cache_control"=>"Cache Control","activity"=>"Activity","vars"=>"Vars");
	$menu['admin']=array("adminusers"=>"Admin Access","roles"=>"User Roles");
*/	
	
	//$subs=array("prod"=>"Products","stock"=>"Warehousing","front"=>"Front-end","selling"=>"Sales","shipment"=>"Shipment","crm"=>"Customer Relationship","accounting"=>"Accounting & Admin","marketing"=>"Marketing","pnh"=>"StoreKing","streams"=>"Streams");
	$subs=array();
	$subs["prod"] = "Products";
	$subs["stock"] = "Warehousing";
	$subs["front"] = "Front-end";
	$subs["selling"] = "Sales";
	$subs["shipment"] = "Shipment";
	$subs["crm"] = "Customer Relationship";
	$subs["accounting"] = "Accounting";
	$subs["marketing"] = "Marketing";
	$subs["pnh"] = "StoreKing";
	$subs["streams"] = "Streams";
	$subs["manage"] = "Manage";
	
	$menu["prod"]=array("products"=>"Products","products_group"=>"Products Group","categories"=>"Categories","brands"=>"Brands","prods_bulk_upload"=>"Product bulk upload","products_group_bulk_upload"=>"Products group bulk upload","export_data"=>"Export Data","prod_mrp_update"=>"Product Update","product_changelog"=>"Product Changelog");
	
	$menu['stock']=array("storage_locs"=>"Storage Locations","rackbins"=>"Rack & Bins","m_seller_list"=>"Marketplace Seller","vendors"=>"Vendors","purchaseorders"=>"View POs","purchaseorder"=>"Create PO","apply_grn"=>"Stock Intake","stock_intake_list"=>"Stock Intakes Summary","stock_unavail_report"=>"Stock Unavailability Report","warehouse_summary"=>"Warehouse Summary","unavail_product_ageing_report"=>"Ageing Report");
	
	$menu['front']=array("menu"=>"Menu","deals"=>"Deals","deals_table"=>"Deals table","deals_bulk_upload"=>"Deals Bulk upload","freesamples"=>"Free Samples","variants"=>"Variants","cache_control"=>"Cache Control","activity"=>"Activity","vars"=>"Vars","deal_price_changelog"=>"Price Changelog","partner_deal_prices"=>"Bulk partner deal price update","bulk_update_partner_deals"=>"Update Partner Deals refno","auto_image_updater"=>"Auto Image updater");
	$menu["selling"]=array("orders"=>"Orders","order_summary"=>"Order Summary","bulk_cancelorders"=>"Bulk Cancel Orders","partner_orders"=>"Partner Orders","partner_order_import"=>"Partner Order Import","uplaod_partner_settelment_details"=>"Upload partner payment","callcenter"=>"Recent Transaction");
	$menu['shipment']=array("batch_process"=>"Shipment batch process","pending_batch_process"=>"Pending Shipment batch process","manage_trans_reservations"=>"Manage Transaction Reservations",'update_partnerorder_manifesto'=>"Update HS18 Manifesto","outscan/0"=>"Outscan Order" ,"generate_kfile"=>"Generate kfile","print_franlabels"=>"Print Franchise Delivery Labels","pnh_pending_shipments"=>"PNH Manifesto","update_ship_kfile"=>"Update shipment kfile","courier"=>"Courier","pnh_shipment_sms_notify"=>"PNH Shipment SMS Notification","pnh_ship_log"=>"Ship Log","stock_transfer"=>"Partner Stock Transfer");
	$menu['crm']=array("support"=>"Customer Support/Tickets","users"=>"Site Users","review"=>"Reviews","callcenter"=>"Recent transactions","stock_checker"=>"Stock Checker");
	$menu['accounting']=array("vouchers"=>"Vouchers","pending_pay_grns"=>"Ready for payment","list_allbanks"=>"Bank Accounts","pending_grns"=>"Unaccounted Stock Intakes","clients"=>"Corporate Clients","client_orders"=>"Corporate Orders","client_invoices"=>"Corporate Invoices","pending_refunds_list"=>"Pending Refunds","partners"=>"Partners","deals_report"=>"Deals Report","pnh_executive_account_log"=>"PNH Executive Paid Log","make_payment"=>"Trade Discount Payment");
	$menu['marketing']=array("featured_newsletter"=>"Newsletter","announcements"=>"Announcements","stats"=>"Stats","coupons"=>"Coupons","cashback_campaigns"=>"Cashback Campaigns","pointsys"=>"Loyalty Points","headtotoe"=>"Head to toe");
	
	if($this->erpm->auth(PAF_ROLE,true) && !$this->erpm->auth(true,true) && ($user['access'] == 32768) )
	{
		$menu = array();
		$menu['pnh']=array("pnh_reports"=>"Reports");
	}else
	{
		$menu['pnh']=array("pnh_franchises"=>"Franchises","asset_list"=>"Manage Assets","pnh_class"=>"Admin","pnh_deals"=>"Deals","pnh_members"=>"Members","subscriptionplan_members_list"=>"Members Combo Plan","list_employee"=>"Employees","pnh_special_margins"=>"Special Margins","pnh_receiptsbytype/1"=>"Pending Receipts","pnh_receipt_upd_log"=>"Receipts Update Log","pnh_special_margins"=>"Discounts","pnh_reports"=>"Reports","pnh_voucher_book"=>"Manage Voucher Books");
	}
	
	//$menu['pnh']=array("pnh_franchises"=>"Franchises","pnh_class"=>"Admin","pnh_deals"=>"Deals","pnh_members"=>"Members","list_employee"=>"Employees","pnh_special_margins"=>"Special Margins","pnh_offline_order"=>"Place Order",'pnh_invoice_returns'=>"PNH Invoice Returns","pnh_quotes"=>"Franchise Requests","pnh_pending_receipts"=>"Pending Receipts","pnh_comp_details"=>"Company Details","pnh_catalogue"=>"Products Catalogue","pnh_special_margins"=>"Discounts","pnh_add_credits"=>"Add Credit","pnh_gen_statement"=>"Generate Account Statement",'pnh_sales_report'=>"PNH Sales Report","pnh_employee_sales_summary"=>"Employee Sales Summary","export_pnh_sales_report"=>"Export PNH Franchise Sales");
	$menu['streams']=array("streams"=>"View Streams","stream_create"=>"Create Stream",'streams_manager'=>"Streams Manager");
	
	if($this->erpm->auth(PAF_ROLE,true) && !$this->erpm->auth(true,true) && ($user['access'] == 32768) )
	{
		$menu = array();
		$menu['pnh']=array("pnh_reports"=>"Reports");
	}
	
	$menu['pnh']["pnh_comp_details"]="Company Details";
	
	$menu['manage'] = array("adminusers"=>"Admin Access","roles"=>"User Roles");
	
	$submenu['vouchers']=array("vouchers"=>"Vouchers List","create_voucher"=>"Create Voucher");
	$submenu['list_employee']=array("list_employee"=>"Employees","add_employee"=>"Add Employees","assignment_histroy"=>"Assignment Histroy","roletree_view"=>"Role Tree View","calender"=>"Calender View","manage_routes"=>"Routes","pnh_exsms_log"=>"PNH SMS Log");
	$submenu['list_allbanks']=array("list_allbanks"=>"List Bank Accounts",'add_bankdetails'=>'Add New Bank');
	
	$submenu['products']=array("addproduct"=>"Add Product","products_report"=>"Product report",'get_barcode_info'=>'Add Barcode',"sk_product_deal_report"=>"Products Deals Report","products_list"=>"Manage Products","deals_list"=>"Manage Deals");
	$submenu['categories']=array("addcat"=>"Add Category", "view_categories_detail"=>"View Category");
	$submenu['categories']=array("addcat"=>"Add Category");
	$submenu['brands']=array("addbrand"=>"Add Brand","add_bulk_brand"=>"Add Brand(Bulk)");
	$submenu['vendors']=array("addvendor"=>"Add Vendor","vendor_margin_bulk_update"=>"vendor margin bulk update");
	$submenu['support']=array("addticket"=>"Add Ticket");
	//$submenu['generate_manifesto']=array("pnh_pending_shipments"=>"Pending shipments for delivery","generate_manifesto"=>"Generate Manifesto","view_manifesto_sent_log"=>"View Driver Sent Log");
	$submenu['pnh_pending_shipments']=array("outscan/1"=>"Already Packed- Outscan","pnh_pending_shipments"=>"Choose shipments for delivery","view_manifesto_sent_log"=>"Print manifesto ","shipments_transit_log"=>"Shipments Transit log","update_bulk_lrdetails"=>"Bulk Update LR Details","pnh_scan_delivery_akw"=>"Scan delivery acknowledgement","print_invoice_acknowledgementbydate"=>"Manage Acknowledgements");
	$submenu['courier']=array("towns_courier_priority"=>"Manage Towns Courier Priority");
	$submenu['uplaod_partner_settelment_details']=array("view_partner_settelment_log"=>"View Settelment Log");
	$submenu['stock_transfer']=array('stk_partner_select'=>"Create Stock Transfer",'partner_transfer_list'=>"Stock Transfer Summary");

	
	
	//$submenu['paflist'] = array("createpaf"=>"Create PAF","paflist"=>"List all PAF");
	
	$submenu['pnh_reports']=array("pnh_fran_salesbydate"=>"Franchise Montly Sales","packed_list"=>"Packed Summary","outscan_list"=>"Outscan Summary","pnh_gen_statement"=>"Generate Account Statement","pnh_states"=>"States",'pnh_sales_report'=>"PNH Sales Report","pnh_sales_bydeal_report"=>'PNH Sales by Deal Report',"pnh_employee_sales_summary"=>"Employee Sales Summary","pnh_exsms_log"=>"PNH SMS Log","export_salesfortally"=>"Export Sales Report - Tally Import","list_activesuperscheme"=>"super scheme Log","fr_hyg_anlytcs_report"=>"Franchise Hygenie Analytics ","export_pnh_sales_report"=>"Export PNH Franchise Sales");
	
	$submenu['pnh_members']=array("pnh_addmember"=>"Add Member");
	$submenu['subscriptionplan_members_list']=array("manage_subscription_plan"=>"Manage Subscription Plan","member_detail_page"=>"Member Add Plan","addfranchise_comboplan"=>"Franchise add combo Plan","member_feedback_detail"=>"Member add Feedback","combo_plan_categories"=>"Combo Plan Categories","subscriptionplan_members_list"=>"Manage member Plan","franchise_combo_plan_list"=>"Manage Franchise combo Plan","franchise_member_plan_list"=>"Manage Franchise member Plan","fetch_memberplan_orderdetail"=>"Manage Member Plan Order","fetch_memberfeedback_detail"=>"Manage Member Feedback");
	$submenu['clients']=array("addclient"=>"Add Client");
	$submenu['client_orders']=array("addclientorder"=>"Add Client Order");
	
	if($this->erpm->auth(FINANCE_ROLE,true))
	{
		$submenu['pnh_franchises']=array("pnh_addfranchise"=>"Add franchise","pnh_franchise_bulk_upload"=>"Add Bulk franchise","orders_status_summary"=>"Order Status Summary","pnh_gen_statement"=>"Generate Account Statement","pnh_quotes"=>"Franchise Requests",'pnh_invoice_returns'=>"Manage Returns","pnh_add_credits"=>"Add Credit","pnh_activation"=>"SMS Alternative Activations","pnh_imei_activation_log"=>'IMEI Activation Log','manage_offers'=>'Manage Member Offers','disable_batch_log'=>'Batch Disabled Log',"add_bulk_rf_franchise"=>"Bulk Assign RF Franchise");
	}else
	{
		$submenu['pnh_franchises']=array("pnh_addfranchise"=>"Add franchise","orders_status_summary"=>"Order Status Summary","pnh_quotes"=>"Franchise Requests",'pnh_invoice_returns'=>"Manage Returns","pnh_add_credits"=>"Add Credit","pnh_franchise_activate_imei"=>"Franchise IMEI Activation","pnh_activation"=>"SMS Alternative Activations","pnh_imei_activation_log"=>'IMEI Activation Log','manage_offers'=>'Manage Member Offers',"add_bulk_rf_franchise"=>"Bulk Assign RF Franchise");
	}
	
	if($this->erpm->auth(CALLCENTER_ROLE,true))
	{
		$submenu['pnh_franchises']['franch_sales_calender_view']='Franchise sales';
	}
	
	$submenu['menu']=array("addmenu"=>"Add Menu");
	$submenu['deals']=array("adddeal"=>"Add Deal","pnh_catalogue"=>"Products Catalogue","deal_product_link_update_log"=>"Deal product link updates log");
	$submenu['asset_list']=array("add_franchise_asset"=>"Add Franchise Asset");
	
	$submenu['orders']=array('orders/1'=>'Pending Orders','partial_shipment'=>'Partial Shipment Orders','disabled_but_possible_shipment'=>'Disabled But Possible','product_order_summary'=>'Product Order Summ Last 90 Days','userend_orders'=>'DIY - User Generated Orders','unconfirmed_order_list'=>'Unconfirmed Credit Orders');
	
	$submenu['stock_unavail_report']=array('stock_unavail_report/0/0/0/0'=>'Show All Orders','stock_unavail_report/0/0/0/2'=>'Show Snapittoday Orders','stock_unavail_report/0/0/0/1'=>'Show PNH Orders','pnh_stock_unavail_report'=>'Advanced PNH Unavailable Report');
	
	
	$submenu['warehouse_summary']=array("po_on_top_sold"=>"Top sold Products","po_on_new_product_top_sold"=>"Newly Added Top Sold Products");
	$submenu['purchaseorder']=array("purchaseorder"=>"Vendorwise","po_product"=>"Productwise",'bulk_createpo_byfile'=>"Bulk Create PO");
	$submenu['pnh_class']=array("list_allmenumargin"=>"Manage Menu","reset_franchise_limit"=>"Reset Franchisee Limit","pnh_class"=>"Class","pnh_less_margin_brands"=>"Less margin brands","pnh_sms_log"=>"SMS Log","pnh_device_type"=>"Device Types","pnh_loyalty_points"=>"Loyalty points","manage_skinsurance_bymenu"=>"Manage Insurance Menu","pnh_states"=>"States","pnh_territories"=>"Territories","pnh_towns"=>"Towns","manage_townslist"=>"Manage State Territory Town","pnh_app_versions"=>"App Versions","pnh_order_import"=>"Import orders","pnh_member_card_batch"=>"MID card printing batch","pnh_version_price_change"=>"Version price changes","pnh_sms_campaign"=>"SMS Campaign","pnh_tray_management"=>'Tray Management',"pnh_transport_management"=>"Transport Management",'pnh_manage_delivery_hub'=>"Manage Delivery Hubs","pnh_employee_sms_activity_log"=>"Employee activity log","reverse_reconciled_invoice"=>"Reverse Reconciled Invoice","depts_request"=>"Departments");
	$submenu['pnh_special_margins']=array("pnh_special_margins"=>"Special Margins","pnh_sch_discounts"=>"List scheme discounts","pnh_bulk_sch_discount"=>"Add Scheme Discounts","pnh_bulk_offeradd"=>"Manage Offers");
	$submenu['pnh_deals']=array("pnh_adddeal"=>"Add Deal","pnh_deals"=>"List Deals","pnh_deals_bulk_upload"=>"Deals Bulk upload","pnh_update_description"=>"Update description","pnh_deals_bulk_update"=>"Deals Bulk Update",'pnh_update_dp_price'=>'Update DP Price','member_price_update'=>'Member Price Update','mp_loyaltypoint_config'=>'MP Loyaltypoint Config',"vendor_deal_import"=>"Vendor deal import");
	$submenu['pnh_voucher_book']=array("pnh_prepaid_menus"=>"Config prepaid menus","pnh_book_template"=>"Manage book template","pnh_voucher_book"=>"Manage Voucher book","pnh_manage_book_allotments"=>"Manage book allotments");
	
	
	
	$extras['front']=array("edit");
	$extras['marketing']=array("coupon");
	$extras['crm']=array("ticket","user");
	$extras['selling']=array("orders","transbystatus","batch","trans");
	$extras['stock']=array("apply_grn","viewpo");
	$extras['pnh']=array("pnh_franchise","pnh");
	
	
	
	
	$submenu['ticket']=array("addticket"=>"Add Ticket");
	
	// check if the link is valid for the user
	if(count($menu['accounting']))
		if(in_array($this->uri->segment(2),array_keys($menu['accounting'])))
			$this->erpm->auth(FINANCE_ROLE);
	
	if(count($menu['stock']))
		if(in_array($this->uri->segment(2),array_keys($menu['stock'])))
			$this->erpm->auth(PURCHASE_ORDER_ROLE);
	
	if(count($submenu['pnh_reports']))
		if(in_array($this->uri->segment(2),array_keys($submenu['pnh_reports'])))
			$this->erpm->auth(PAF_ROLE);
	
	if(!$this->erpm->auth(PURCHASE_ORDER_ROLE,true))
		unset($menu['stock']);
	
	if(!$this->erpm->auth(FINANCE_ROLE,true))
		unset($menu['accounting']);
	
	if(!$this->erpm->auth(PAF_ROLE,true))
		unset($menu['pnh']['pnh_reports']);
	
	
	if(!$this->erpm->auth(PRODUCT_MANAGER_ROLE,true))
		unset($menu['product']);
	
	if(!$this->erpm->auth(DEAL_MANAGER_ROLE,true))
		unset($menu['pnh']['pnh_deals']);
	
	if(!$this->erpm->auth(true,true))
		unset($menu['manage']);
	
	
	
	if($this->uri->segment(2)!="dashboard")
	{
		
		
		
		$uri=substr(strstr(substr($this->uri->uri_string(),1),"/"),1);
		foreach($menu as $id=>$m)
		{
		foreach($m as $u=>$s)
			if(strstr($uri,$u)!==false)
			{
				$sub="{$id}_menu";
				break;
			}
		if(isset($extras[$id]))
			foreach($extras[$id] as $e)
				if(strstr($uri,$e)!==false)
				{
					$sub="{$id}_menu";
					break;
				}
		}
		echo '<script>submenu="'.$sub.'"</script>';
	}
	
	if($is_pnh_exec_only)
		foreach(array("prod","stock","front","selling","shipment","crm","accounting","marketing") as $i)
			unset($menu[$i]);
?>
<div id="hd" class="container">

	<div >
	<form style="display:none;" id="searchform" action="<?=site_url("admin/search")?>" method="post">
		<input type="hidden" name="q" id="searchkeyword">
	</form>

	<div class="logo_cont">
		<a href="<?=site_url("admin/dashboard")?>">
			<img style="margin:16px 0px 0px 0px;width:170px;margin-left:5px;" src="<?=base_url()?>images/paynearhome.png">
		</a>
	</div>

<?php if($user){ ?>
	<div class="welcomeuser">
            <div class="username" align="right">Welcome <b><?php echo $user["username"];?></b>  
                <a class="notify_block" id="notify_block" href="<?=site_url("admin/streams")?>" title="Stream Notification"></a>
		<a href="<?=site_url("admin/changepasswd")?>" style="color:#fff;font-size:75%;text-decoration: underline;">change password</a> 
		<a class="signout" href="<?=site_url("admin/logout")?>">Sign Out</a></div>
			<div id="searchformbox" style="clear:right;padding-top:5px;">
			<input type="text" id="searchbox" value="Search..." style="width:250px;"><input type="button" id="searchtrigh" value="Go!">
			<div id="suggestions"></div>
		</div>
			<div class="fl_left" style="padding-top: 4px;">
				<input type="radio" name="srch_opt" value="0" checked> All
				<input type="radio" name="srch_opt" value="1"> Barcode
				<input type="radio" name="srch_opt" value="2"> IMEI
			</div>	
	</div>
	<div style="float:right;margin-top:20px;margin-right:20px;">
		<a href="<?=site_url("admin/stk_offline_order")?>"><img src="<?=IMAGES_URL?>storeking_icon_orders.png" style="cursor:pointer;"></a>
	</div>
	<div style="float:right;margin-top:20px;margin-right:0px;">
<!--            place_order.png /phone.png-->
		<img src="<?=IMAGES_URL?>storeking_icon_calls.png" style="cursor:pointer;" onclick='$("#phone_booth").toggle()'>
	</div>
	<div style="float:right;margin-top:20px;">
		<a href="<?=site_url("admin/streams")?>"><img src="<?=IMAGES_URL?>storeking_icon_streams.png" style="cursor:pointer;"></a>
	</div>
	<!-- Modal for PO products selected display-->
		<div id="prd_det_dlg" title="Product Details"></div>
	<script>
	$(function(){
		$("#phone_booth form").submit(function(){
			if(!is_required($(".pb_customer",$(this)).val()))
			{
				alert("Please enter Customer number");
				return false;
			}
			if(!is_required($(".pb_agent",$(this)).val()))
			{
				alert("Please enter Agent number");
				return false;
			}
			$(".loading",$(this)).show();
			$.post("<?=site_url("admin/makeacall")?>",$(this).serialize(),function(data){
				$("#phone_booth .loading").hide();
				if(data=="0")
					show_popup("Error in initiating call");
				else
				{
					$("#phone_booth").hide();
					show_popup("Call Initiated");
				}
			});
			return false;
		});
	});
	
	//Open PO product view block
	$('#prd_det_dlg').dialog({'width':1150,autoOpen:false,height:600,modal:true,open:function(){
	},
	buttons:{
		'Close' : function()
			{
				$(this).dialog('close');
			}	
		}
	});
	            
	</script>
	
	<div id="phone_booth">
		<form>
			<div style="color:#ccc;" align="center">Prefix '0' for mobile numbers</div>
			<table>
				<tr><td>Customer Number : </td><td><input type="text" class="inp pb_customer" name="customer"></td></tr>
				<tr><td>Your number : </td><td><input type="text" class="inp pb_agent" name="agent" value="<?=$this->session->userdata("agent_mobile")?>"></td></tr>
				<tr><td><img src="<?=IMAGES_URL?>loader.gif" class="loading" style="display:none;"></td><td><input type="submit" value="Call Customer"><input type="button" value="Close" onclick='$("#phone_booth").hide()'></td></tr>
			</table>
		</form>
	</div>
	
	<div class="menu_cont">
	<ul class="menu">
		<li>
			<a href="<?=site_url("admin/dashboard")?>">Dashboard</a>
		</li>
	<?php foreach($menu as $id=>$m){?>
		<li id="<?=$id?>_menu">
			<a href="<?=site_url("admin/".key($m))?>"><?=$subs[$id]?></a>
			<ul>
				<?php foreach($m as $u=>$s){?>
				<li>
					<?php if(isset($submenu[$u])){?>
					<span>&raquo;</span>
					<ul class="submenu <?=(($u=="pnh_class"||$u=="pnh_reports"||$u=="list_employee"||$u=="pnh_franchises"||$u=="pnh_deals"||$u=="pnh_members"||$u=="pnh_special_margins")?"submenuright":"")?>">
						<?php foreach($submenu[$u] as $ur=>$sm){?>
							<li><a href="<?=site_url("admin/$ur")?>" <?=$this->uri->segment(2)==$u?"class='selected'":""?>><?=$sm?></a></li>
						<?php }?>
					</ul>
					<?php }?>
					<a href="<?=site_url("admin/$u")?>" <?=$this->uri->segment(2)==$u?"class='selected'":""?>><?=$s?></a>
				</li>
				<?php }?>
			</ul>
		</li>
	<?php }?>
		<li class="clear"></li>
	</ul>
	<div class="clear"></div>
	</div>
<?php }?>

	
	</div>
	<div class="clear"></div>
</div>
<style type="text/css">
.notify_block {     color:white;font-size: 12px;  border-radius: 10px;  }

</style>

<script type="text/javascript">
    var userid="<?php echo $user["userid"];?>";    
</script>
<?php 
$menu=$tmenu;
?>