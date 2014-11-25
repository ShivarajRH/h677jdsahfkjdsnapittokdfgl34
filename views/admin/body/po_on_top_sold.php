
<style>
.row_select
{
	background:#ff9600;
}
.filters
{
	display: inline-block;
    margin: 0 0 11px;
    width: 100%;
}
h2
{
	width: 250px;
	display: inline-block;
}

#reorder_qty
{

    width: 30px;
}
#sel_menu
{
	margin-right: 20px;
   
}
#sel_cat 
{
   margin-right: 20px;
  
}
#sel_terr 
{
   margin-right: 20px;
   
}
#sel_state 
{
   margin-right: 20px;
   
}
#sel_po
{
    
    margin-right: 15px;
    
}
#sel_brand
{
	
	margin-right: 20px;
}
#sel_vendor
{
	
}
.sort_po
{
	background: none repeat scroll 0 0 #AAAAAA;
    display: block;
    margin: 3px;
    padding: 5px;
}
.print{
	margin: 0px 12px 11px 12px;
}
.status
{
    border: 1px solid #aaa;
    color: #000;
    float: right;
    font-size: 13px;
    font-weight: bold;
    padding: 3px;
}
.change_status
{
	font-size:10px;margin:-12px 0 2px;display:inline:block;text-decoration:none;float:right;cursor:pointer;
}
.prd_dets
{
	margin:10px 0;
}
.place_po_blk div
{
	margin: 10px 2px;
}
.show_all,.show_filterd_sales
{
	float: right;
    margin: 4px 0;
}
.filters_block .filter select {
    font-size: 12px;
    margin-top: 5px;
    width: 180px;
}  
.filters_block .filter label {
    display: inline-block;
    font-size: 12px;
    font-weight: bold;
    margin-right: 10px;
    text-align: right;
    width: 71px;
} 
select {
    width: 177px;
}
#sel_brand_chzn
{
	margin:10px 0;
} 
.chzn-container
{
	font-size: 10px;
} 
.btn-adjst
{
	display: block;
    margin: 17px 7px;
}
#sel_po
{
	width:70px;
}
.filters_block .filter
{
	min-height: 88px;
}
</style>
<div class="container">
	<div class="filters_block fl_left" style="width:100%">
		<h2 class="fl_left">Top Sold Products</h2>
			<div class="filter fl_right" style="float:right !important;min-height: 32px !important;">
				<label style="width:148px">Stock Unavailable Report :</label>
				<input type="text"  id="sel_po" placeholder="Enter Quantity">
				<a class="button button-tiny button-rounded" href="javascript:void(0)" onclick="print_products_summary(1,1)" >Export</a>
			</div>
			<!-- Export CSV Button -->
			<a class="button button-tiny button-rounded fl_right btn-adjst" href="javascript:void(0)" onclick="print_products_summary(1,0)" >Export CSV</a>
			<!-- Print Button -->
			<a class="button button-tiny button-rounded fl_right btn-adjst" href="javascript:void(0)" onclick="print_products_summary(0,0)" >Print</a>
		</div>	
	<div class="filters_block">
		<div class="filter">
			<label>State :</label>
				<select name="sel_state" id="sel_state">
					<option value="0">Choose</option>
					<?php foreach($states as $s) { ?>
						<option value="<?=$s['state_id']?>"><?=$s['state_name']?></option>
					<?php } ?>
				</select>
			</br>
			
			<label>Territory :</label>
			<select name="sel_terr" id="sel_terr">
				<option value="0">Choose</option>
				<?php foreach($terr as $t) { ?>
					<option value="<?=$t['id']?>"><?=$t['territory_name']?></option>
				<?php } ?>
			</select>
			<br />
			<label>Menu :</label>
			<select name="" id="sel_menu">
				<option value="0">Choose</option>
				<?php foreach($menu as $m) { ?>
					<option value="<?=$m['id']?>"><?=$m['name']?></option>
				<?php } ?>
				<?php foreach($snp_menu as $sm) { ?>
					<option value="<?=$sm['id']?>"><?=$sm['name']?></option>
				<?php } ?>
			</select>
		</div>
		<div class="filter">
			<label>Category :</label>
			<select name="cat" id="sel_cat">
				<option value="0">Choose</option>
				<?php foreach($categories as $c) { ?>
					<option value="<?=$c['id']?>"><?=$c['name']?></option>
				<?php } ?>
			</select>
			</br>
			<label style="vertical-align: top;margin-top: 15px;">Brand :</label>
			<select name="brand[]" id="sel_brand" multiple="true" style="margin: 10px 0;">
				<?php foreach($brands as $b) { ?>
					<option value="<?=$b['id']?>"><?=$b['name']?></option>
				<?php } ?>
			</select>
		</div>
	 	
	 	<div class="filter">
			<label>Show :</label>
			<select name="sales" id="sel_sales">
				<option value="60">60 day sales</option>
				<option value="30">30 day sales</option>
				<option value="7">7 day sales</option>
				<option value="all">Overall sales</option>
			</select>
			<br />
			
			<span class="pagi_filter">
			<label>From/To :</label>
			<select name="filter_start" id="sel_from_start">
				<option value="0">1-200</option>
				<option value="200">201-400</option>
				<option value="400">401-600</option>
				<option value="600">601-800</option>
				<option value="800">801-1000</option>
			</select>
			</span>	
		</div>
		
		<div class="filter">
	 		<label>Type :</label>
			<select name="sel_state" id="sel_type">
				<option value="all">All</option>
				<option value="1">SK</option>
				<option value="0">SIT</option>
			</select>
			</br>
			<!--
	 		<label>Place :</label>
				<select  id="sel_po">
					<option value="0">Choose</option>
					<option value="1">All</option>
					<option value="2">Place PO's</option>
				</select>
			</br>
		-->
			<label>Vendors :</label>
			<select name="vendors" id="sel_vendor">
				<option value="0">Choose</option>
				<?php foreach($vendors as $v) { ?>
					<option value="<?=$v['vendor_id']?>"><?=$v['vendor_name']?></option>
				<?php } ?>
			</select>
		</div>
	 </div>		
	<!--Container to load Products --> 
	<table class="datagrid" width="100%">
		<thead>
			<tr>
				<th>Sl.No</th>
				<th>Product</th>
				<th>Menu</th>
				<th>MRP</th>
				<th>Offer Price</th>
				<th><span class="total_sold">Sold in 60 days</span></th>
				<th>Current Stock</th>
				<th>Expected Stock</th>
				<th width="140px" style="text-align: center !important">PO Action  <br /><a class="select_all">Select All<input type="checkbox" class="all"></a><a class="po_act">All PO<input type="checkbox" class="po_all"></a></th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	
	<!-- Place Purchase button Order for less Stock Products -->
	<div class="submit fl_right"><button type="submit" class="button button-action place_po ">Place PO</button></div>
	<div style="display:none">
		<!-- Modal for PO products selected display-->
		<div id="dlg_openpolist" title="Open PO List"></div>
	</div>
	
	<form id="place_po_form" action="<?=site_url("admin/po_product_bytopsold")?>" method="post">
		<input type="hidden" name="po_arr" class="prd_arr" value="">
		<input type="hidden" name="vendor" class="vid_selected" value="">
	</form>
	
	<div id="place_po_dlg" title="Place Po">
		<h5>Total of  <span id="prd_ttl"></span> Products selected</h5>
		<div class="place_po_blk"></div>
		<!--
		<div class="fl_left" style="margin-top:15px"><b>Choose Vendor :</b> 
			<select name="cat" id="sel_vendor">
				<option value="0">Choose</option>
				<?php foreach($vendors as $v) { ?>
					<option value="<?=$v['vendor_id']?>"><?=$v['vendor_name']?></option>
				<?php } ?>
				</select>
		</div>-->
	</div>
	<div id="change_status_dlg" title="PO">
		<div class="prd_dets"></div>
		<b style="vertical-align: top">Remarks :</b>  <textarea name="remarks" class="remarks" placeholder="Please enter minimum 20 characters"></textarea>
		
	</div>	
</div>

<script>

$(function(){
	$('#sel_menu').val('0');
	$('#sel_brand').val('0');
	$('#sel_cat').val('0');
	brand_arr=[];
	cat_arr=[];
	menu_arr=[];
	$('#sel_brand').chosen(); 
	$('table.datagrid tbody').html('<tr><td colspan="9"><div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div></td></tr>');
	product_list(0,0,0,0,0,0,60,'all',0,0);
})
po_product_arr=[];	
pid_arr=[];

//Open PO product view block
$('#dlg_openpolist').dialog({'width':800,autoOpen:false,height:600,modal:true,open:function(){
	var pid = $(this).data('pid');
	$('#dlg_openpolist').html("<h3 align='center'>Loading...</h3>");
	$.post(site_url+'/admin/jx_getopenpolistbypid/'+pid,{},function(resp){
		var html = '<h3 class="pname">'+resp.product_name+'</h3>';
			html += '	<div class="pttl">Total open qty : <b>'+resp.ttl_open_qty+'</b></div>';
			html += '	<div id="openpolist_tbl">';
			html += '	<table width="100%" class="datagrid" cellpadding="5" cellspacing="0">';
			html += '		<thead>';
			html += '			<th><input type="checkbox" class="all_po_chk"></th><th>Slno</th><th>Remove product in PO</th><th>Vendor</th><th>PO Date</th><th>POID</th><th>Total Qty</th><th>Action</th>';
			html += '		</thead>';
			html += '		<tbody>';
			$.each(resp.vendor_po_list,function(a,b){
				html += '		<tr poid="'+b.po_id+'" productid="'+pid+'"><td><input type="checkbox" class="sl_sel_po" value="'+pid+'" po_id="'+b.po_id+'"></td><td>'+(a*1+1)+'</td><td><a href="javascript:void(0)" onclick="remove_prodfrmpo(this)" poid="'+b.po_id+'" pid="'+pid+'" style="font-size:11px;color:blue;" >Cancel product in PO</a></td><td><a target="_blank" href="'+site_url+'/admin/vendor/'+b.vendor_id+'">'+b.vendor_name+'</a></td><td>'+get_unixtimetodate(b.po_date)+'</td><td>'+b.po_id+'</td><td>'+b.qty+'</td><td><a class="inline_trig" style="color:blue;font-weight:bold" target="_blank" href="'+(site_url+'/admin/viewpo/'+b.po_id)+'" >View</a></td></tr>';	
			});
			html += '		</tbody>';
			html += '	</table>';
			html += '	</div>';
			$('#dlg_openpolist').html(html);
	},'json');
},

buttons:{
	'Cancel selected product in PO':function()
	{
		var dlg = $(this);
		var poids=[];
		$(".sl_sel_po:checked").each(function(){
			poids.push($(this).attr('po_id'));
			
		});
		poids=poids.join(",");
		var pid=$('.sl_sel_po').val();
		if($(".sl_sel_po:checked").length!=0){
			 $.post(site_url+'/admin/jx_update_poprod_status',{poid:poids,pid:pid},function(resp){
			if(resp.status)
			{
				$("#dlg_openpolist").dialog('close');
           		$("#dlg_openpolist").dialog('open');
          		$("#is_po_raised_"+pid).html('<a href="javascript:void(0)" onclick="load_openpolist('+pid+')" ><b>'+resp.ttl_open_qty+'</b></a>');
   			 
			} 
	                   
            },'json');
        }
	}
}

}).load(function() {
                $(this).dialog("option", "position", ['center', 'center'] );
            });
            
//Category change event -- to load products for a specified Category      
$('#sel_cat').live('change',function(){
	var cat=$(this).val();
	var menu=$('#sel_menu').val();
	var brand=$('#sel_brand').val();
	var vendor=$('#sel_vendor').val();
	var state=$('#sel_state').val();
	var terr=$('#sel_terr').val();
	var type=$('#sel_type').val();
	var days=$('#sel_sales').val();
	var start=$('#sel_from_start').val();
	$('.pagi_filter').show();
	$("#sel_brand").html("Loading ...");
	$('table.datagrid tbody').html('<tr><td colspan="9"><div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div></td></tr>');
	$.getJSON(site_url+'/admin/jx_load_allbrandsbycat/'+cat+'/'+menu,'',function(resp){
		var brand_html='';
		if(resp.status=='error')
		{
			alert(resp.message);
		}
		else
		{
			brand_html+='<option value="0">All</option>';
			$.each(resp.brand_list,function(i,b){
				brand_html+='<option value="'+b.brandid+'">'+b.name+'</option>';
			});
		}
		$("#sel_brand").html(brand_html).trigger("liszt:updated");
		//$(".recharge_town_filter").trigger('change');
 	});
 	if(cat==0)
	{
		$('#sel_sales').val(60);
		days=$('#sel_sales').val();
	}
	product_list(menu,cat,brand,vendor,terr,state,days,type,start,vendor);
	
});

//Menu change event -- to load products for a specified brand,load categories,brands in a select filter
$('#sel_menu').live('change',function(){
	var menu=$(this).val();
	var cat=$('#sel_cat').val();
	var brand=$('#sel_brand').val();
	var vendor=$('#sel_vendor').val();
	var state=$('#sel_state').val();
	var terr=$('#sel_terr').val();
	var type=$('#sel_type').val();
	var days=$('#sel_sales').val();
	var start=$('#sel_from_start').val();
	$('.pagi_filter').show();
	$("#sel_cat").html("Loading ...");
	$("#sel_brand").html("Loading ...");
	$('table.datagrid tbody').html('<tr><td colspan="9"><div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div></td></tr>');
	 $.getJSON(site_url+'admin/jx_load_allcatsbymenu/'+menu+'/'+0,'',function(resp){
		var cat_html='';
		if(resp.status=='error')
		{
			alert(resp.message);
		}
		else
		{
			cat_html+='<option value="0">All</option>';
			$.each(resp.cat_list,function(i,c){
				cat_html+='<option value="'+c.catid+'">'+c.name+'</option>';
			});
		}
		$("#sel_cat").html(cat_html).trigger("liszt:updated");
	});
    
    $.getJSON(site_url+'admin/jx_load_allbrandsbymenucat/'+menu+'/'+0,'',function(resp){
		var brand_html='';
		if(resp.status=='error')
		{
			alert(resp.message);
		}
		else
		{
			brand_html+='<option value="0">All</option>';
			$.each(resp.cat_list,function(i,b){
				brand_html+='<option value="'+b.brandid+'">'+b.brandname+'</option>';
			});
		}
		$("#sel_brand").html(brand_html).trigger("liszt:updated");
	});
	if(menu==0)
	{
		$('#sel_sales').val(60);
		days=$('#sel_sales').val();
	}
	product_list(menu,cat,brand,vendor,terr,state,days,type,start,vendor);
	
});

//Brand change event -- to load products for a specified brand
$('#sel_brand').live('change',function(){
	var brand=$(this).val();
	var menu=$('#sel_menu').val();
	var cat=$('#sel_cat').val();
	var vendor=$('#sel_vendor').val();
	var state=$('#sel_state').val();
	var terr=$('#sel_terr').val();
	var days=$('#sel_sales').val();
	var type=$('#sel_type').val();
	var start=$('#sel_from_start').val();
	$('table.datagrid tbody').html('<tr><td colspan="9"><div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div></td></tr>');
	if(brand)
	{
		$('.pagi_filter').hide();
	}else
	{
		$('#sel_sales').val(60);
		days=$('#sel_sales').val();
		$('.pagi_filter').show();
	}
	
	if(brand=='')
	{
		$('.pagi_filter').show();
	}
	
	product_list(menu,cat,brand,vendor,terr,state,days,type,start,vendor);
});

//Day sales
$('#sel_sales').live('change',function(){
	var brand=$('#sel_brand').val();
	var menu=$('#sel_menu').val();
	var cat=$('#sel_cat').val();
	var vendor=$('#sel_vendor').val();
	var state=$('#sel_state').val();
	var terr=$('#sel_terr').val();
	var days=$('#sel_sales').val();
	var type=$('#sel_type').val();
	var start=$('#sel_from_start').val();
	$('.pagi_filter').show();
	if(!brand)
		brand=0;
	if(days==60)
		$('span.total_sold').html("Sold in 60 days");
	else if(days==30)
		$('span.total_sold').html("Sold in 30 days");
	else if(days==7)
		$('span.total_sold').html("Sold in 1 Week");
	else if(days=='all')
	{
		$('span.total_sold').html("Total sold");
		if(brand==0 && cat==0 && menu==0)
		{
			alert("Please Choose either Menu,Category or Brand before proceed");
			return false;
		}
	}
					
	$('table.datagrid tbody').html('<tr><td colspan="9"><div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div></td></tr>');
	product_list(menu,cat,brand,vendor,terr,state,days,type,start,vendor);
});

//start sales
$('#sel_from_start').live('change',function(){
	var brand=$('#sel_brand').val();
	var menu=$('#sel_menu').val();
	var cat=$('#sel_cat').val();
	var vendor=$('#sel_vendor').val();
	var state=$('#sel_state').val();
	var terr=$('#sel_terr').val();
	var days=$('#sel_sales').val();
	var type=$('#sel_type').val();
	var start=$('#sel_from_start').val();
	$('.pagi_filter').show();
	$('table.datagrid tbody').html('<tr><td colspan="9"><div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div></td></tr>');
	product_list(menu,cat,brand,vendor,terr,state,days,type,start,vendor,vendor);
});

//Vendor change event -- to get brands asigned for a vendor
$('#sel_vendor').live('change',function(){
	var vendor=$(this).val();
	var brand=$('#sel_brand').val();
	var menu=$('#sel_menu').val();
	var cat=$('#sel_cat').val();
	var state=$('#sel_state').val();
	var terr=$('#sel_terr').val();
	var days=$('#sel_sales').val();
	var type=$('#sel_type').val();
	var start=$('#sel_from_start').val();
	
	if(vendor==0)
		$('.pagi_filter').show();
					 	else
		$('.pagi_filter').hide();	
	
	$('table.datagrid tbody').html('<tr><td colspan="9"><div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div></td></tr>');
	product_list(menu,cat,brand,vendor,terr,state,days,type,start,vendor);
});

//state change event
$('#sel_state').live('change',function(){
	var state=$(this).val();
	var terr=$('#sel_terr').val();
	var brand=$('#sel_brand').val();
	var menu=$('#sel_menu').val();
	var cat=$('#sel_cat').val();
	var vendor=$('#sel_vendor').val();
	var days=$('#sel_sales').val();
	var type=$('#sel_type').val();
	var start=$('#sel_from_start').val();
	$('.pagi_filter').show();
	//product_list(menu,cat,brand,vendor);
	$.getJSON(site_url+'admin/jx_load_all_territories_bystate/'+state,{},function(resp){
		var terr_html='';
		if(resp.status=='error')
		{
			alert(resp.message);
		}
		else
		{
			terr_html+='<option value="0">All</option>';
			$.each(resp.terrs_bystate,function(i,t){
				terr_html+='<option value="'+t.id+'">'+t.territory_name+'</option>';
			});
		}
		$("#sel_terr").html(terr_html).trigger("liszt:updated");
	});	
	product_list(menu,cat,brand,vendor,terr,state,days,type,start,vendor);
});

//state change event
$('#sel_type').live('change',function(){
	var state=$('#sel_state').val();
	var terr=$('#sel_terr').val();
	var brand=$('#sel_brand').val();
	var menu=$('#sel_menu').val();
	var cat=$('#sel_cat').val();
	var vendor=$('#sel_vendor').val();
	var days=$('#sel_sales').val();
	var type=$('#sel_type').val();
	var start=$('#sel_from_start').val();
	$('.pagi_filter').show();
	$('table.datagrid tbody').html('<tr><td colspan="9"><div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div></td></tr>');
	product_list(menu,cat,brand,vendor,terr,state,days,type,start,vendor);
});

$('#sel_terr').live('change',function(){
	var brand=$('#sel_brand').val();
	var menu=$('#sel_menu').val();
	var cat=$('#sel_cat').val();
	var vendor=$('#sel_vendor').val();
	var state=$('#sel_state').val();
	var terr=$(this).val();
	var type=$('#sel_type').val();
	var days=$('#sel_sales').val();
	var start=$('#sel_from_start').val();
	$('.pagi_filter').show();
	$('table.datagrid tbody').html('<tr><td colspan="9"><div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div></td></tr>');
	product_list(menu,cat,brand,vendor,terr,state,days,type,start,vendor);
});

//change sourceable/not sourceable status of a product
$('.change_status').live('click',function(){
	var pids=[];
	var pid=$(this).attr('product_id');
	var pname=$(this).attr('product_name');
	var src=$(this).attr('sourceable');
	pids.push(pid);
	pids=pids.join(",");
	
	$('#change_status_dlg').data("pro_data",{'pids': pids,'pname': pname,'status': src}).dialog('open');	
});

$("#change_status_dlg" ).dialog({
		modal:true,
		autoOpen:false,
		width:'300',
		height:'300',
		autoResize:true,
		open:function(){
			var dlgData = $("#change_status_dlg").data("pro_data");
			var pid=dlgData.pids;
			var pname=dlgData.pname;
			var pstatus=dlgData.status;
			var html='';
			var src_data=''
			
			if(pstatus==0)
				src_data='Not Sourceable';
			else if(pstatus==1)
				src_data='Sourceable';	
			
			html+='<div class=""><b>Current Status</b> : '+src_data+'</div>';
			$('.ui-widget-header .ui-dialog-title').html(pname);
			$('.prd_dets').html(html);
	},
	buttons: {
		"Proceed": function() {
			var dlgData = $("#change_status_dlg").data("pro_data");
			var pid=dlgData.pids;
			var pname=dlgData.pname;
			var pstatus=dlgData.status;
			var remarks=$('.remarks').val();
			if(!remarks || remarks.length<20)
			{
				alert("Please enter minimum 20 characters");
				return false;
			}
			change_status(pid,pname,pstatus,remarks);
			
	    	$(this).dialog('close');
	    },	
	    "Close": function() {
	    	$(this).dialog('close');
	   }
	} 
});

function change_status(pid,pname,pstatus,remarks)
{
	$('.change_stat_'+pid).html('<a class="change_status">Please wait....</a>');
	$.post('<?=site_url('admin/jx_change_prd_status')?>',{pids:pid,status:pstatus,remarks:remarks},function(resp){
		
		if(resp.status=='error')
		{
			
		}else
		{
			if(pstatus==1)
			{
				$('.src_blk_'+pid).html('<span class="status"  href="javascript:void(0)" sourceable="0" product_id="'+pid+'" style="background-color:rgb(255, 170, 170)">Not Sourceable</span>');
				$('.change_stat_'+pid).html('<a style="" class="change_status"  href="javascript:void(0)" sourceable="0" product_id="'+pid+'" >Change Status</a>');
			
			}
			else if(pstatus==0)
			{
				$('.src_blk_'+pid).html('<span class="status"  href="javascript:void(0)" sourceable="1" product_id="'+pid+'" style="background-color:rgba(170, 255, 170, 0.8)">Sourceable</span>');
				$('.change_stat_'+pid).html('<a style="" class="change_status"  href="javascript:void(0)" sourceable="1" product_id="'+pid+'" >Change Status</a>');
			}
				
		}
	},'json');
}

//Function to append products based on menu,category,brand,vendor etc filter value
function product_list(menu,cat,brand,vendor,terr,state,days,type,start,vendor)
{
	$.post('<?=site_url('admin/jx_top_sold_byqty')?>',{menu:menu,cat:cat,brand:brand,vendor:vendor,terr:terr,state:state,days:days,type:type,sales_type:'total',start:start,vid:vendor},function(data){
		if(data.prd_list)
		{
			html='';
			k=parseInt(data.start)+1;
			c='';
			console.log(brand);
			console.log(vendor);
			if(brand!=null || vendor!=0)
					k=1;
			
			$.each(data.prd_list,function(i,p){
				
				if(p.is_sourceable == 1)
				{
					var s='Sourceable';
					var background = 'background-color:rgba(170, 255, 170, 0.8)';
					
				}else if(p.is_sourceable == 0)
				{
					var s='Not Sourceable';
					var background = 'background-color:rgb(255, 170, 170)';
				}
				
				if(pid_arr.indexOf(p.product_id)==-1)
				{
					$('.sel_prd',this).attr('checked',true);
					 var c='';
				}
				else
				  var c='row_select';
				
				html +='<tr class="prd_row '+c+'" cur_stk="'+p.stk+'" exp_stk="'+p.exp_stk+'" brandid="'+p.brandid+'" product_id="'+p.product_id+'" product_name="'+p.product_name+'">';
				html +='<td>'+(k++)+'</td>';
				html +='<td><a target="_blank"  style="font-size:14px;margin:1px 0 2px;display:block;display:inline:block;text-decoration:none;" href="'+site_url+'/admin/product/'+p.product_id+'">'+p.product_name+' <b class="src_blk_'+p.product_id+'"><span class="status" style="'+background+'">'+s+'</span></b></a><br />';
				html +='<b>PNH ID :</b> <a target="_blank" href="'+site_url+'/admin/pnh_deal/'+p.pnh_id+'" style="margin-right:15px;color:green">'+p.pnh_id+'</a>';
				html +='<b>Brand :</b> <a target="_blank" style="margin-right:15px;color:green" href="'+site_url+'/admin/viewbrand/'+p.brandid+'">'+p.brandname+'</a>';
				html +='<b>Category :</b> <a target="_blank" style="margin-right:15px;color:green" href="'+site_url+'/admin/viewcat/'+p.cat_id+'">'+p.cat_name+'</a>';
				if(p.is_combo==1)
				{
					html +='<span style="color:red;font-size:11px;">Combo Product</span>';
				}
				html +='<b class="change_stat_'+p.product_id+'"><a style="font-size:10px;margin:-12px 0 2px;display:inline:block;text-decoration:none;float:right" class="change_status" product_id="'+p.product_id+'" product_name="'+p.product_name+'" sourceable="'+p.is_sourceable+'" href="javascript:void(0)">Change Status</a></b></td>';
				html +='<td>'+p.menu+'</td>';
				html +='<td>'+p.mrp+'</td>';
				html +='<td>'+p.offer_price+'</td>';
				html +='<td>'+p.sold_qty+'</td>';
				html +='<td align="center"><b>'+p.stk+'</b></td>';
				if(days!='all')
				html +='<td align="center"><b>'+p.exp_stk+'</b><br />';
				else
					html +='<td align="center"><b>N/A</b><br />';	
				html +='<b>Open PO :<a href="javascript:void(0)" onclick="load_openpolist('+p.product_id+')" >'+p.po_qty+'</b></a><br />';
				html +='Req. Stock :<b>'+p.pending_order+'</b></td>';
				
				if(parseInt(p.exp_stk) > parseInt(p.stk))
				{
					if(pid_arr.indexOf(p.product_id)==-1)
					{
					html +='<td align="center"><input type="checkbox" pid="'+p.product_id+'" product_name="'+p.product_name+'" class="sel_prd"></td>';
					}
					else
					{
						html +='<td align="center"><input type="checkbox" pid="'+p.product_id+'" product_name="'+p.product_name+'" class="sel_prd" checked></td>';
					}
				}
					
				else
					html +='<td align="center"><span class="po_chk_'+p.product_id+'" product_id="'+p.product_id+'"></span></td>';
				  	
				html +='</tr>';
				
			});
			
			$('table.datagrid tbody').html(html);
		}
		else
		{
			$('table.datagrid tbody').html('<tr><td colspan="9"><div class="page_alert_wrap" style="margin:10% 0;color:red">No Products Found</div></td></tr>');
		}	
	},'json');
}

// insufficient stock products dislay -- onchange event 
$('#sel_po').live('change',function(){
	var v=$(this).val();
	if(v==1)
	{
		$('.prd_row').each(function(){
			 var c_stk=$(this).attr('cur_stk')*1;
			 var exp_stk=$(this).attr('exp_stk')*1;
			 $(this).show();
		});
	}else if(v==2)
	{
		$('.prd_row').each(function(){
			 var c_stk=$(this).attr('cur_stk')*1;
			 var exp_stk=$(this).attr('exp_stk')*1;
			 if(exp_stk > c_stk)
			 	$(this).show();
			 else
			 	$(this).hide();	
		});
		
	}else
	{
		$('.prd_row').each(function(){
			 var c_stk=$(this).attr('cur_stk')*1;
			 var exp_stk=$(this).attr('exp_stk')*1;
			 $(this).show();
		});
	}
	
});

//Checkbox check and uncheck all action 
$('.all').live('click',function(){
	if($('.all').attr('checked'))
		$('.sel_prd').attr('checked',true);
	else
		$('.sel_prd').attr('checked',false);
	
	$('.sel_prd').each(function(){
		var trEle=$(this).parents('tr:first');
		var chk=$(this).attr('checked');
		if(chk == 'checked')
			trEle.addClass('row_select');
		else
			trEle.removeClass('row_select');
	});
	
	$('.sel_prd').each(function(){
		var trEle=$(this).parents('tr:first');
		var chk=$(this).attr('checked');
		if(chk == 'checked')
		{
			//trEle.addClass('row_select');
			var pid=$(this).attr('pid');
			var pname=$(this).attr('product_name');
			if($.inArray(pid, pid_arr) === -1)
			{
				po_product_arr[pid] = pname;
				pid_arr.push(pid);
			} 
		}
		else
		{
			var pid=$(this).attr('pid');
			var pname=$(this).attr('product_name');
			//trEle.removeClass('row_select');
			var i = pid_arr.indexOf(pid);
			if(i != -1) {
				pid_arr.splice(i, 1);
				delete po_product_arr[pid];
			}
		}
	});
});

//Checkbox check and uncheck all action 
$('.po_all').live('click',function(){
	if($('.po_all').attr('checked'))
	{
		$('.prd_row').each(function(){
			 var pid=$(this).attr('product_id');
			 var pname=$(this).attr('product_name');
			 $('.po_chk_'+pid).html('<input type="checkbox" pid="'+pid+'" product_name="'+pname+'" class="sel_prd">');
		});
	}	
	else
	{
		$('.prd_row').each(function(){
			var pid=$(this).attr('product_id');
			 var rpid=$('.po_chk_'+pid).attr('product_id');
			 var trEle=$('.po_chk_'+pid).parents('tr:first');
			 trEle.removeClass('row_select');
			 var i = pid_arr.indexOf(rpid);
				if(i != -1) {
					pid_arr.splice(i, 1);
					delete po_product_arr[rpid];
				}
			 $('.po_chk_'+pid).html("");
		});
	}
});

//Checkbox action for place po option
$('.sel_prd').live('click',function(){
	var trEle=$(this).parents('tr:first');
	var chk=$(this).attr('checked');
	if(chk == 'checked')
	{
		trEle.addClass('row_select');
		var pid=$(this).attr('pid');
		var pname=$(this).attr('product_name');
		
		if($.inArray(pid, pid_arr) === -1)
		{
			po_product_arr[pid] = pname;
			///po_product_arr.push([pid,pname]);
			pid_arr.push(pid);
		} 
	}
	else
	{
		var pid=$(this).attr('pid');
		var pname=$(this).attr('product_name');
		trEle.removeClass('row_select');	
		var i = pid_arr.indexOf(pid);
		if(i != -1) {
			pid_arr.splice(i, 1);
			delete po_product_arr[pid];
			}
	}
});

//Action on place po button pressed
$('.place_po').click(function(){
	$('#place_po_dlg').dialog('open');
});

//Dialog to proceed to place PO for insufficient stock products
$('#place_po_dlg').dialog({
		modal:true,
		autoOpen:false,
		width:500,
		height:450,
		autoResize:true,
		open:function(){
			
		//$('.ui-dialog-buttonpane').find('button:contains("Update")').addClass('placeorder_btn');
		dlg = $(this);
		var k=0;
		var selected_product_ids=[];
		//pid_html='';
		var pid_html = '';
		for (var x in po_product_arr) {
		  if (po_product_arr.hasOwnProperty(x)) {
		   pid_html+='<div ><span style="margin-right:7px;">'+(++k)+'</span><a target="_blank" href="'+site_url+'/admin/product/'+x+'">'+po_product_arr[x]+'</a></div>';
		   selected_product_ids.push(x)	;
		  }
			}
			
		$('.place_po_blk').html(pid_html);
		$('.prd_arr').val(selected_product_ids);
		$('#prd_ttl').html(k);
	},
	buttons:{
		'Cancel':function(){
			$('#place_po_dlg').dialog('close');
		},
		'Proceed':function(){
			if(po_product_arr.length)
			{
				 $('.vid_selected').val($('#sel_vendor').val());
			 $('#place_po_form').submit();
		}
			else
			{
				alert("Please Choose product before proceed");
				return false;
			}
		}
	}
});

//Function to print summary and CSV export
function print_products_summary(type,stk_type)//type===>print/export ,,,   stk_type===> all products/ qty selected products
{
	var menuid =$('#sel_menu').val();
	var catid =$('#sel_cat').val();
	var brandid =$('#sel_brand').val();
	var terrid =$('#sel_terr').val();
	var stateid =$('#sel_state').val();
	var menu_type=$('#sel_type').val();
	var days=$('#sel_sales').val();
	var start=$('#sel_from_start').val();
	var vendor=$('#sel_vendor').val();
	var stk_qty=$('#sel_po').val();
	
	if(!stk_qty)
		stk_qty=0;
	
	if(stk_type==1)
	{
		if(stk_qty==0)
		{
			alert("Please Choose Qty before export");return false;
		}
	}	
	if(!brandid)
		brandid=0;
	window.open(site_url+'/admin/print_top_product_summary/'+menuid+'/'+catid+'/'+brandid+'/'+type+'/'+stk_type+'/'+stk_qty+'/'+vendor+'/'+terrid+'/'+stateid+'/'+days+'/'+'total'+'/'+menu_type+'/'+start);
}

//Function call to display open PO details for a product
function load_openpolist(pid)
{
	$('#dlg_openpolist').data('pid',pid).dialog('open');
}

function get_unixtimetodate(utime)
{
	var date = new Date(utime * 1000);
	var y=date.getFullYear();
    var m=date.getMonth()+1;
    var d=date.getDate();
    var h=(date.getHours() > 9)?date.getHours()-12:date.getHours();
    var mi=date.getMinutes();
    var s=date.getSeconds();
    var datetime=d+'/'+m+'/'+y;
    return datetime;
}
</script>	