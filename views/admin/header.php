<script type="text/javascript" src="<?php echo base_url();?>/js/jquery.jgrowl.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>/css/jquery.jgrowl.min.css" type="text/css"/>
<style>
	div.jGrowl div.jGrowl-notification, div.jGrowl div.jGrowl-closer,div.jGrowl div.jGrowl-message
	{background: #fcfcfc !important;opacity:1;font-family: arial}
	div.caller_info{}
	div.caller_info h3{margin:3px 0px;display: inline-block}
	div.caller_info .button{color:#FFF !important;}
</style>
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

	$subs=array("prod"=>"Products","stock"=>"Warehousing","front"=>"Front-end","selling"=>"Sales","shipment"=>"Shipment","crm"=>"Customer Relationship","accounting"=>"Accounting & Admin","marketing"=>"Marketing","pnh"=>"Pay Near Home","streams"=>"Streams");
	
	$menu["prod"]=array("products"=>"Products","products_group"=>"Products Group","categories"=>"Categories","brands"=>"Brands","prods_bulk_upload"=>"Product bulk upload","products_group_bulk_upload"=>"Products group bulk upload","export_data"=>"Export Data","prod_mrp_update"=>"MRP Update","product_price_changelog"=>"Price Changelog","product_src_changelog"=>"Product Sourceable ChangeLog","list_deals_nsrc_prod"=>"Deals - Products not sourceable");
	$menu['stock']=array("storage_locs"=>"Storage Locations","rackbins"=>"Rack & Bins","vendors"=>"Vendors","purchaseorders"=>"View POs","purchaseorder"=>"Create PO","apply_grn"=>"Stock Intake","stock_intake_list"=>"Stock Intakes Summary","stock_unavail_report"=>"Stock Unavailability Report","warehouse_summary"=>"Warehouse Summary","unavail_product_ageing_report"=>"Ageing Report");
	$menu['front']=array("menu"=>"Menu","deals"=>"Deals","deals_table"=>"Deals table","deals_bulk_upload"=>"Deals Bulk upload","freesamples"=>"Free Samples","variants"=>"Variants","cache_control"=>"Cache Control","activity"=>"Activity","vars"=>"Vars","deal_price_changelog"=>"Price Changelog","partner_deal_prices"=>"Bulk partner deal price update","auto_image_updater"=>"Auto Image updater");
	$menu["selling"]=array("orders"=>"Orders","order_summary"=>"Order Summary","bulk_cancelorders"=>"Bulk Cancel Orders","partner_orders"=>"Partner Orders","partner_order_import"=>"Partner Order Import","uplaod_partner_settelment_details"=>"Upload partner payment","offline"=>"Offline Orders","callcenter"=>"Recent Transaction","sales_analytics_graph"=>'Sales Graph');
	$menu['shipment']=array("batch_process"=>"Shipment batch process","pending_batch_process"=>"Pending Shipment batch process","manage_trans_reservations"=>"Manage Transaction Reservations",'update_partnerorder_manifesto'=>"Update HS18 Manifesto","outscan/0"=>"Outscan Order" ,"generate_kfile"=>"Generate kfile","print_franlabels"=>"Print Franchise Delivery Labels","pnh_pending_shipments"=>"PNH Manifesto","update_ship_kfile"=>"Update shipment kfile","courier"=>"Courier","pnh_shipment_sms_notify"=>"PNH Shipment SMS Notification","packed_list"=>"Packed Summary","outscan_list"=>"Outscan Summary","pnh_ship_log"=>"Ship Log","pnh_invoice_returns/sit"=>"Invoice return");
	$menu['crm']=array("support"=>"Customer Support/Tickets","users"=>"Site Users","review"=>"Reviews","callcenter"=>"Recent transactions","stock_checker"=>"Stock Checker");
	$menu['accounting']=array("vouchers"=>"Vouchers","create_voucher"=>"Create Voucher","pending_pay_grns"=>"Ready for payment","add_bankdetails"=>"Add Bank Details","pending_grns"=>"Unaccounted Stock Intakes","adminusers"=>"Admin Access","roles"=>"User Roles","clients"=>"Corporate Clients","client_orders"=>"Corporate Orders","client_invoices"=>"Corporate Invoices","pending_refunds_list"=>"Pending Refunds","partners"=>"Partners","deals_report"=>"Deals Report","pnh_investor_report"=>"PNH Sales Report","investor_report"=>"Investor Report","pnh_executive_account_log"=>"PNH Executive Paid Log");
	$menu['marketing']=array("featured_newsletter"=>"Newsletter","announcements"=>"Announcements","stats"=>"Stats","coupons"=>"Coupons","cashback_campaigns"=>"Cashback Campaigns","pointsys"=>"Loyalty Points","headtotoe"=>"Head to toe");
	
	//$menu['pnh']=array("pnh_franchises"=>"Franchises","pnh_class"=>"Admin","pnh_deals"=>"Deals","pnh_members"=>"Members","list_employee"=>"Employees","pnh_special_margins"=>"Special Margins","pnh_offline_order"=>"Place Order",'pnh_invoice_returns'=>"PNH Invoice Returns","pnh_quotes"=>"Franchise Requests","pnh_pending_receipts"=>"Pending Receipts","pnh_comp_details"=>"Company Details","pnh_catalogue"=>"Products Catalogue","pnh_special_margins"=>"Discounts","pnh_add_credits"=>"Add Credit","pnh_gen_statement"=>"Generate Account Statement",'pnh_sales_report'=>"PNH Sales Report","pnh_employee_sales_summary"=>"Employee Sales Summary","export_pnh_sales_report"=>"Export PNH Franchise Sales");
	
	$menu['pnh']=array("pnh_franchises"=>"Franchises","pnh_class"=>"Admin","pnh_deals"=>"Deals","list_allmenumargin"=>"Manage Menu Margin","pnh_members"=>"Members","list_employee"=>"Employees","pnh_special_margins"=>"Special Margins","pnh_receiptsbytype/1"=>"Pending Receipts","pnh_receipt_upd_log"=>"Receipts Update Log","pnh_special_margins"=>"Discounts","pnh_reports"=>"Reports","pnh_voucher_book"=>"Manage Voucher Books");
	$menu['streams']=array("streams"=>"View Streams","stream_create"=>"Create Stream",'streams_manager'=>"Streams Manager");
	$submenu['list_employee']=array("list_employee"=>"Employees","add_employee"=>"Add Employees","assignment_histroy"=>"Assignment Histroy","roletree_view"=>"Role Tree View","calender"=>"Calender View","manage_routes"=>"Routes","pnh_exsms_log"=>"PNH SMS Log","employee_exp_incentive_track"=>"Employee Expense and Incentive Tracking");
	$submenu['add_bankdetails']=array("list_allbanks"=>"Banks");
	$submenu['products']=array("addproduct"=>"Add Product","products_report"=>"Product report");
	$submenu['categories']=array("addcat"=>"Add Category");
	$submenu['brands']=array("addbrand"=>"Add Brand");
	$submenu['vendors']=array("addvendor"=>"Add Vendor","vendor_margin_bulk_update"=>"vendor margin bulk update");
	$submenu['support']=array("addticket"=>"Add Ticket");
	//$submenu['generate_manifesto']=array("pnh_pending_shipments"=>"Pending shipments for delivery","generate_manifesto"=>"Generate Manifesto","view_manifesto_sent_log"=>"View Driver Sent Log");
	$submenu['pnh_pending_shipments']=array("outscan/1"=>"Already Packed- Outscan","pnh_pending_shipments"=>"Choose shipments for delivery","view_manifesto_sent_log"=>"Print manifesto ","shipments_transit_log"=>"Shipments Transit log","update_bulk_lrdetails"=>"Bulk Update LR Details","pnh_scan_delivery_akw"=>"Scan delivery acknowledgement","print_invoice_acknowledgementbydate"=>"Manage Acknowledgements");
	$submenu['courier']=array("towns_courier_priority"=>"Manage Towns Courier Priority");
	$submenu['uplaod_partner_settelment_details']=array("view_partner_settelment_log"=>"View Settelment Log");

	
	
	//$submenu['paflist'] = array("createpaf"=>"Create PAF","paflist"=>"List all PAF");
	
	
	$submenu['pnh_reports']=array("pnh_gen_statement"=>"Generate Account Statement",'pnh_sales_report'=>"PNH Sales Report","pnh_sales_bydeal_report"=>'PNH Sales by Deal Report',"pnh_employee_sales_summary"=>"Employee Sales Summary","pnh_comp_details"=>"Company Details","pnh_exsms_log"=>"PNH SMS Log","export_salesfortally"=>"Export Sales Report - Tally Import","list_activesuperscheme"=>"super scheme Log","pnh_imei_activation_log"=>'IMEI Activation Log');
	$submenu['pnh_members']=array("pnh_addmember"=>"Add Member");
	$submenu['clients']=array("addclient"=>"Add Client");
	$submenu['client_orders']=array("addclientorder"=>"Add Client Order");
	$submenu['pnh_franchises']=array("pnh_addfranchise"=>"Add franchise","orders_status_summary"=>"Order Status Summary","fr_hyg_anlytcs_report"=>"Franchise Hygenie Analytics ","pnh_quotes"=>"Franchise Requests",'pnh_invoice_returns'=>"Manage Returns","pnh_add_credits"=>"Add Credit","export_pnh_sales_report"=>"Export PNH Franchise Sales","list_activesuperscheme"=>"Super Scheme Log","pnh_franchise_activate_imei"=>"Franchise IMEI Activation","pnh_activation"=>"SMS Alternative Activations");
	
	$submenu['menu']=array("addmenu"=>"Add Menu");
	$submenu['deals']=array("adddeal"=>"Add Deal","pnh_catalogue"=>"Products Catalogue","deal_product_link_update_log"=>"Deal product link updates log","deal_img_currection"=>"Deal image currection");
	
	$submenu['orders']=array('orders/1'=>'Pending Orders','partial_shipment'=>'Partial Shipment Orders','disabled_but_possible_shipment'=>'Disabled But Possible','product_order_summary'=>'Product Order Summ Last 90 Days');
	
	$submenu['stock_unavail_report']=array('stock_unavail_report/0/0/0/0'=>'Show All Orders','stock_unavail_report/0/0/0/2'=>'Show Snapittoday Orders','stock_unavail_report/0/0/0/1'=>'Show PNH Orders','pnh_stock_unavail_report'=>'Advanced PNH Unavailable Report');
	
 
	
	$submenu['purchaseorder']=array("purchaseorder"=>"Vendorwise","po_product"=>"Productwise",'bulk_createpo_byfile'=>"Bulk Create PO");
	$submenu['pnh_class']=array("pnh_class"=>"Class","pnh_less_margin_brands"=>"Less margin brands","pnh_sms_log"=>"SMS Log","pnh_device_type"=>"Device Types","pnh_loyalty_points"=>"Loyalty points","pnh_states"=>"States","pnh_territories"=>"Territories","pnh_towns"=>"Towns","pnh_app_versions"=>"App Versions","pnh_order_import"=>"Import orders","pnh_member_card_batch"=>"MID card printing batch","pnh_version_price_change"=>"Version price changes","pnh_sms_campaign"=>"SMS Campaign","pnh_tray_management"=>'Tray Management',"pnh_transport_management"=>"Transport Management",'pnh_manage_delivery_hub'=>"Manage Delivery Hubs","pnh_employee_sms_activity_log"=>"Employee activity log");
	$submenu['pnh_special_margins']=array("pnh_special_margins"=>"Special Margins","pnh_sch_discounts"=>"List scheme discounts","pnh_bulk_sch_discount"=>"Add Scheme Discounts","pnh_bulk_offeradd"=>"Manage Offers");
	$submenu['pnh_deals']=array("pnh_adddeal"=>"Add Deal","pnh_deals"=>"List Deals","pnh_deals_bulk_upload"=>"Deals Bulk upload","pnh_update_description"=>"Update description","pnh_deals_bulk_update"=>"Deals Bulk Update",'pnh_update_dp_price'=>'Update DP Price');
	$submenu['pnh_voucher_book']=array("pnh_prepaid_menus"=>"Config prepaid menus","pnh_book_template"=>"Manage book template","pnh_voucher_book"=>"Manage Voucher book","pnh_manage_book_allotments"=>"Manage book allotments");
	
	
	
	$extras['front']=array("edit");
	$extras['marketing']=array("coupon");
	$extras['crm']=array("ticket","user");
	$extras['selling']=array("orders","transbystatus","batch","trans");
	$extras['stock']=array("apply_grn","viewpo");
	$extras['pnh']=array("pnh_franchise","pnh");
	
	$submenu['ticket']=array("addticket"=>"Add Ticket");
	
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
		<div id="searchformbox" style="clear:right;float:right;padding-top:5px;">
			<input type="text" id="searchbox" value="Search..." style="width:250px;"><input type="button" id="searchtrigh" value="Go!">
			<div id="suggestions"></div>
		</div>
	</div>
	<div style="float:right;margin-top:20px;margin-right:20px;">
		<a href="<?=site_url("admin/pnh_offline_order")?>"><img src="<?=IMAGES_URL?>storeking_icon_orders.png" style="cursor:pointer;"></a>
	</div>
	<div style="float:right;margin-top:20px;margin-right:0px;">
<!--            place_order.png /phone.png-->
		<img src="<?=IMAGES_URL?>storeking_icon_calls.png" style="cursor:pointer;" onclick='$("#phone_booth").toggle()'>
	</div>
	<div style="float:right;margin-top:20px;">
		<a href="<?=site_url("admin/streams")?>"><img src="<?=IMAGES_URL?>storeking_icon_streams.png" style="cursor:pointer;"></a>
	</div>
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

#searchbox{	color:#aaa;	font-size:14px;	}
 

/* SEARCH FORM */
#searchformbox {}
#searchformbox div { color:#eeeeee; }
#searchformbox div input { font-size:18px; padding:5px; width:294px; }
#hd #suggestions{ position: absolute;
margin-left: 0px;
width: 294px;
display: none;z-index: 999}

/* SEARCHRESULTS */
#hd #searchresults { border-width: 0px;
border-color: #8897A7;
border-style: solid;
width: 292px;
background-color: #633D72;
font-size: 11px;
line-height: 14px;
margin: 0px;}
#hd #searchresults a { display: block;
background-color: #F7F7F7;
clear: left;
text-decoration: none;
padding: 5px;
border-bottom: 1px dotted #DFDFDF; }
#hd #searchresults a:hover { background-color:#ccc; color:#ffffff; }
#hd #searchresults a img { float:left; padding:5px 10px; }
#hd #searchresults a span.searchheading { display:block; font-weight:bold; color:#191919; }
#hd #searchresults a:hover span.searchheading { color:#ffffff; }
#hd #searchresults a span { color:#555555; }
#hd #searchresults a:hover span { color:#f1f1f1; }
#hd #searchresults span.category { font-size: 11px;
margin: 0px;
display: block;
color: #FFF;
padding: 5px;
background: #6B6A6A;}
#hd #searchresults span.seperator { float:right; padding-right:15px; margin-right:5px;background-repeat:no-repeat; background-position:right; }
#hd #searchresults span.seperator a { background-color:transparent; display:block; margin:5px; height:auto; color:#ffffff; }

#hd #searchresults a span.viewall { text-align: center;}

</style>

<div style="display:none">
	<div id="franchise_call_panel" title="Franchise Call Panel"></div>
</div>

<script type="text/javascript">
    var userid="<?php echo $user["userid"];?>";

    
	

    function get_panel_alerts()
	{
		
		 $.post(site_url+'/admin/get_panel_alerts','',function(resp){
		 	if(resp.status == 'success')
		 	{
		 		$('#strip_itemlist_wrap').show();
		 		var tick_width = resp.quote_list.length*500; 
		 		
		 		var ticker_html = '<div class="strip_itemlist" style="width:'+tick_width+'px;font-size:12px;position:relative;left:0px;">';
		 			$.each(resp.quote_list,function(a,b){
		 				ticker_html += '<div class="strip_item"  style="width:400px;display:inline;margin-right:10px;">  '+(a+1)+') '+b+' </div> ';
		 			});
		 			ticker_html += '</div>';
		 		$('#strip_itemlist_wrap').html(ticker_html);
		 		
		 		var tick_width = 0;
		 			$('#strip_itemlist_wrap .strip_item').each(function(){
		 				tick_width += ($(this).width()+10)*1;
		 			});
		 			var tspan = tick_width*20;
		 			
		 			if(tick_width < $(document).width())
		 				tick_width = $(document).width();
		 				
		 			$('#strip_itemlist_wrap .strip_itemlist').width(tick_width);
		 			
		 		animate_strip(tick_width,tspan);
		 	}else
		 	{
		 		$('#strip_itemlist_wrap').hide();
		 		setTimeout(function(){
		 			get_panel_alerts();
		 		},10000);
		 	}
		 	
		 	if(resp.call_info.callsid != undefined)
		 	{
			 	if(!$('.caller_info_'+resp.call_info.callsid).length)
			 	{
				 	var caller_det_html = '';
				 		caller_det_html += '<div class="caller_info caller_info_'+resp.call_info.callsid+'">';
				 		caller_det_html += '	<p>You have a Call from <b>'+resp.call_info.from_type+'</b></p>';
				 		caller_det_html += '	<div>Name : <h3>'+resp.call_info.caller_name+'</h3> </div> ';
			 			caller_det_html += '	<div>Mobile : <h3>'+resp.call_info.from+'</h3> </div> ';
				 		if(resp.call_info.from_type == 'franchise')
				 		{
				 			caller_det_html += '	<div><a target="_blank" fid="'+resp.call_info.ref_id+'" href="'+site_url+'/admin/pnh_franchise/'+resp.call_info.ref_id+'" class="button button-tiny button-royal button-rounded fran_call_btn">View More info</a> <a href="javascript:void(0)" onclick="close_callerinfo()" class="button button-tiny button-caution button-rounded">Close</a></div> ';
				 		}else if(resp.call_info.from_type == 'employee')
				 		{
				 			caller_det_html += '	<div><a target="_blank" href="'+site_url+'/admin/view_employee/'+resp.call_info.ref_id+'" class="button button-tiny button-success button-rounded">View more info</a></div> ';
				 		}
				 		caller_det_html += '</div>';
				 	
			 		$.jGrowl(caller_det_html, {
			 			sticky:true,
						beforeClose: function() {
							return true;
						},
						position:'bottom-right'
					});
			 		$('.jGrowl-close').hide();
			 	}
		 	}else
		 	{
		 		$('.jGrowl-close').trigger('click');
			}
		 },'json');
	}

    $(function(){

    	$('#franchise_call_panel').dialog({
    		autoOpen:false,
    		width:'1000px',
    		height:'auto',
    		autoResize:true,
    		modal:true,
    		open:{
    			
        	}
    	});
    	$('.fran_call_btn').live('click',function(e){
        	e.preventDefault();
    		var param = {fid:$(this).attr('fid')};
    			$.post(site_url+'/admin/pnh_jx_loadfranchisebyid',param,function(resp){
    				$('#franchise_call_panel').html(resp).dialog('open');
    			});
    			
    		return false;
    	});
    });
	

	function close_callerinfo()
	{
		$('.jGrowl-close').trigger('click');
	}
	
</script>
<style>
.ui-dialog{z-index:999999 !important;}
</style>
<?php 
$menu=$tmenu;
?>