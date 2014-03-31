<!-- styles needed by jScrollPane -->
<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/custom_scrollbar/jquery.jscrollpane.css">

<style>
/**************************************** Brands CSS ********************************************************/
/******** General CSS start********************/
.mod_widget_small
{
	float:left;
	width:34%;
	min-width:34%;
	margin-right:2%;
}
.mod_widget_large
{
	float:left;
	width:64%;
}
.mod_widget_sub
{
	float:left;
	width:50%;
}
.mod_widget_title
{
	background: none repeat scroll 0 0 #D7E2EE;
    border: 1px solid #C7C3C0;
    width:100%;
}
.mod_widget_content
{
	background: none repeat scroll 0 0 #fcfcfc;
    border-bottom: 1px solid #C7C3C0;
    border-left: 1px solid #C7C3C0;
    border-right: 1px solid #C7C3C0;
    height:200px;
    width:100% !important;
}
.mod_widget_content ul
{
	list-style-type:none;
	color:#1D1D1D;
}
.mod_widget_content li
{
	border-bottom: 1px solid #D2D1CF;
    font-size: 12px;
    font-weight: bold;
    height: 18px;
}
.mod_widget_small .mod_widget_content li
{
	margin: 0 2%;
    padding: 1.5% 0.6%;
}
.mod_widget_large .mod_widget_content li
{
	margin: 0 1%;
    padding: 0.7% 0.2%;
}
.mod_widget_title div.heading_wrap
{
	color: #606060;
    display: block;
    font-size: 15px;
    font-weight: bold;
    padding: 10px 0 0 10px;
     height: 29px;
}
.heading_wrap .link
{
	display: inline-block;
    margin-top: -6px;
}
.mod_widget_title img
{
	margin:8px 8px -1px 11px;
}
.mod_widget_small .mod_widget_sub .mod_widget_content ul
{
	margin-top: 8px;
	list-style-type:none;
	color:#1D1D1D;
}
.mod_widget_small .mod_widget_sub .mod_widget_content li
{
    border-bottom: 1px solid #D2D1CF;
    font-size: 12px;
    font-weight: bold;
    height: 28px;
    margin: 4% 4% 0;
    padding: 0 7%;
}
.mod_widget_small .mod_widget_sub .mod_widget_content span
{
	margin-left:5px;
	text-transform:capitalize;
}
.mod_widget_content span
{
	margin-left:4px;
	text-transform:capitalize;
}
.mod_widget_row
{
	width:100%;margin-top:2%;
}
/******** General CSS end *********************/
/******** Custom CSS start ********************/
.leftcont
{
	display:none;
}
.selected{
	background:#fdfdfd;
}
.mrp_wrap
{
    color:#ff0000;
    padding: 0 10px;
    text-align: left;
    width: 11%;
}
.price_wrap
{
	color: #0B0B0E;
	text-align: left;
	width: 11%;
	font-weight: bold;
	font-size: 13px;
}
.barcode_wrap
{
	color: #0B0B0E;
	text-align: left;
	width: 22%;
	font-weight: bold;
	font-size: 13px;
}
.ttl_count
{
	 color: #000000;
    font-size: 12px;
    padding: 4px 6px 6px 8px;
}

#myflashwrapper
{
	background: none repeat scroll 0 0 #FF6347;
    color: #000000;
    display: block;
    font-size: 15px;
    font-weight: bold;
    height: 18px;
    margin-left: 44%;
    margin-top: -47px;
    padding: 10px;
    position: absolute;
    z-index: 1;
}
.vendor_more_wrap
{
	color: #008000;
    font-size: 10px;
    padding: 0 12px;
}
.model_widget_span
{
	float: left;
    margin-right: 8px;
    text-align: right;
    width: 38%;	
}

/*************** Custom analytics css ************************/
#top_sale_terr_stat_frm_to b,#franch_sales_stat_frm_to b,#ttl_sales_stat_frm_to b,#cat_sales_stat_frm_to b
{
	 font-size: 11px;
}
#top_sale_terr_stat_frm_to input,#franch_sales_stat_frm_to input,#ttl_sales_stat_frm_to input,#cat_sales_stat_frm_to input
{
	font-size: 10px;
    font-weight: bold;
    width: 66px;
}
#top_sale_terr_stat_frm_to button,#franch_sales_stat_frm_to button,#ttl_sales_stat_frm_to button,#cat_sales_stat_frm_to button
{
	font-size: 10px;
	margin-top:-2px;
}
#top_sale_terr_stat_frm_to , #franch_sales_stat_frm_to , #ttl_sales_stat_frm_to,#cat_sales_stat_frm_to
{
	float: right;
    width: 61% !important;
}
#stat_frm_to b
{
	color:#000;
}
.total_stat_view
{
	height:200px;
}
.terr_stat_view
{
	height:285px;
}
.br_topcat_list
{
	height: 287px !important;
    margin-top: 0;
    max-height: 300px !important;
    overflow: auto !important;
}
.br_franchise_list
{
    height: 200px !important;
    margin-top: 0;
    max-height: 200px !important;
    overflow: auto !important;
}
.products_list
{
    background: none repeat scroll 0 0 #C3C3C3;
    height: 259px !important;
    overflow: auto;
}
.prod_head {
    font-size: 13px;
    font-weight: bold;
    margin-bottom: 5px;
}
#br_franch_stat {
    margin:0px;
    width: 0%;
    margin-right:2%;
}
#br_town_stat
{
	width:64%;
	margin:0px;
}
#br_franch_stat .mod_widget_content,#br_town_stat .mod_widget_content
{
	height: 340px !important;
	
}

/******** Custom CSS end ********************/
</style>
<script>
$(function()
{
	//Applied custom scrollbar 
	$('.mod_widget_content').jScrollPane();
	
	$('#br_town_stat').hide();
    $('#br_franch_stat').hide();
	
	// Datepicker Function 
	$("#ttl_date_from,#ttl_date_to,#ter_date_from,#ter_date_to,#frch_date_from,#frch_date_to,#cat_date_from,#cat_date_to").datepicker({dateFormat:'dd-mm-yy'});
	
	// Line Graph displays total sales of a brand according to selected dates
	total_sales();
	
	//Function to display category sales of a brand according to selected dates
	brand_sales_by_category();
	//vendors_details();
	
	//Function to display Franchise sales of a brand according to selected dates
	brand_sales_by_franch();
	
	//Function to display territory sales of a brand according to selected dates
	top_sale_terr();
});

</script>

<div class="container outer_blk">
	<!-- -->
	<!----------------------------------Page Title--------------------------->
	<h2> Brand - <?=ucfirst($brand['name'])?><a style="margin-left:5px" href="<?=site_url("admin/editbrand/{$brand['id']}")?>" target="_blank"><img src="<?php echo base_url().'images/pencil.png'?>"></a></h2>
	<div id="myflashwrapper" style="display: none;"></div>
	
	<div class="mod_widget_small">
		<!----------------------------------Rack information block--------------------------->
		<div class="mod_widget_sub">
			<div class="mod_widget_title">
				<div class="heading_wrap">
					Racks<a href="<?=site_url("admin/editbrand/{$brand['id']}")?>" class="link" target="_blank"><img src="<?php echo base_url().'images/pencil.png'?>"></a>
					<span class="fl_right ttl_count">Total : <?=count($rbs)?></span>
				</div>
			</div>
			<div class="mod_widget_content">
				<ul>
					<?php $i=1;foreach($rbs as $rb){ ?>
						<li>
							 <?=$i++?> <span><?=$rb['rack_name']?><?=$rb['bin_name']?></span>
						</li>
					<?php } ?>
				</ul>
			</div>
		</div>
		<!----------------------------------Category information block--------------------------->
		<div class="mod_widget_sub">
			<div class="mod_widget_title">
				<div class="heading_wrap">
					Categories
					<span class="fl_right ttl_count">Total : <?=count($categories)?></span>
				</div>
			</div>
			<div class="mod_widget_content">
				<ul>
					<?php $k=1;foreach($categories as $c){?>
						<li>
							 <?=$k++?> <span><a class="link" target="_blank" href="<?=site_url("admin/viewcat/{$c['brandid']}")?>"><?=$c['cat_name']?></a></span>
						</li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</div>
	
	<!----------------------------------Deals information block--------------------------->
	<div class="mod_widget_large">
		<div class="mod_widget_title">
			<div class="heading_wrap">
				Deals
				<span class="fl_right ttl_count">Total : <?=count($deals)?></span>
			</div>
		</div>
		<div class="mod_widget_content">
			<ul>
				<?php $k=1;foreach($deals as $p){?>
					<li>
						 <?=$k++?> <span><a class="link" target="_blank" href="<?=site_url("admin/edit/{$p['dealid']}")?>"><?=$p['name']?></a></span>
						 <span class="fl_right mrp_wrap">MRP : <?=$p['orgprice']?></span><span class="fl_right price_wrap">Price : <?=$p['price']?></span>
					</li>
				<?php } ?>
			</ul>
		</div>
	</div>
	
	<div class="fl_left mod_widget_row" style="">
		<!----------------------------------Vendors information block--------------------------->
		<div class="mod_widget_small">
			<div class="mod_widget_title">
				<div class="heading_wrap">Vendors
					<span class="fl_right ttl_count">Total : <?=count($vendors)?></span>
				</div>
			</div>
			<div class="mod_widget_content max_height_wrap">
				<ul>
					<?php $k=1;foreach($vendors as $v){?>
						<li>
							 <?=$k++?> <span><a class="link" target="_blank" href="<?=site_url("admin/vendor/{$v['vendor_id']}")?>"><?=$v['vendor_name']?></a></span>
							 <a class="fl_right vendor_more_wrap" vid="<?=$v['vendor_id']?>" vname="<?=$v['vendor_name']?>">more</a>
							 <span class="fl_right"><?=$v['city_name']?></span>
						</li>
					<?php } ?>
				</ul>
			</div>
		</div>
		<!----------------------------------Products information block--------------------------->
		<div class="mod_widget_large">
			<div class="mod_widget_title">
				<div class="heading_wrap">
					Products<a class="allot_rack" style="margin-left:5px"><img class="link" src="<?php echo base_url().'images/pencil.png'?>"></a>
					<span class="fl_right ttl_count">Total : <?=count($products)?></span>
				</div>
			</div>
			<div class="mod_widget_content">
				<ul>
					<?php $k=1;foreach($products as $p){?>
						<li>
							 <?=$k++?> <span><a class="link" href="<?=site_url("admin/editproduct/{$p['product_id']}")?>" target="_blank"><?=$p['product_name']?></a></span>
							 <span class="fl_right mrp_wrap" style="width:12%">MRP : <?=round($p['mrp'],2)?></span><span class="fl_right barcode_wrap">Barcode : <?=$p['barcode']?></span>
						</li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</div>
	
	<div class="fl_left mod_widget_row">
		<!----------------------------------Top Franchises information block--------------------------->
		<div class="mod_widget_small">
			<div class="mod_widget_title">
				<div class="heading_wrap">
					Top Franchises
					<form id="franch_sales_stat_frm_to" method="post" style="width:70%">
				        <div style="text-align: right">
				        	<b>From</b> : <input type="text" id="frch_date_from"
				                    name="date_from" value="<?php echo date('d-m-Y',time()-90*60*60*24)?>" />
				            <b>To</b> : <input type="text" id="frch_date_to"
				                    name="date_to" value="<?php echo date('d-m-Y',time())?>" /> 
				            <button type="submit" class="sbutton small green"><span>Go</span></button>
				        </div>
				    </form>	
				</div>
			</div>
			<div class="mod_widget_content br_franchise_list">
			</div>
		</div>
		
		<!----------------------------------Total sales statisticks information block--------------------------->
		<div class="mod_widget_large">
			<div class="mod_widget_title">
				<div class="heading_wrap">
					Total Sales Statistics
					<form id="ttl_sales_stat_frm_to" method="post">
				        <div style="text-align: right">
				        	<b>From</b> : <input type="text" id="ttl_date_from"
				                    name="date_from" value="<?php echo date('d-m-Y',time()-90*60*60*24)?>" />
				            <b>To</b> : <input type="text" id="ttl_date_to"
				                    name="date_to" value="<?php echo date('d-m-Y',time())?>" /> 
				            <button type="submit" class="sbutton small green"><span>Go</span></button>
				        </div>
				    </form>				
				</div>
			</div>
			<div class="mod_widget_content">
				<div id="total_stat">
	    			<h4 style="margin:0px 22px"><div class="stat_head"></div></h4>
	    			<div class="total_stat_view">
	    			</div>
	    		</div>
			</div>
		</div>
	</div>
	
	<div class="fl_left mod_widget_row">
		<!----------------------------------Top categories information block--------------------------->
		<div class="mod_widget_small">
			<div class="mod_widget_title">
				<div class="heading_wrap">Top Categories
					<form id="cat_sales_stat_frm_to" method="post" style="width:70%">
				        <div style="text-align: right">
				        	<b>From</b> : <input type="text" id="cat_date_from"
				                    name="date_from" value="<?php echo date('d-m-Y',time()-90*60*60*24)?>" />
				            <b>To</b> : <input type="text" id="cat_date_to"
				                    name="date_to" value="<?php echo date('d-m-Y',time())?>" /> 
				            <button type="submit" class="sbutton small green"><span>Go</span></button>
				        </div>
				    </form>	
				</div>
			</div>
			<div class="mod_widget_content br_topcat_list">
			</div>
		</div>
		
		<!----------------------------------Territory sales statisticks information block--------------------------->
		<div class="mod_widget_large">
			<div class="mod_widget_title">
				<div class="heading_wrap">Territory sales for <?=ucfirst($brand['name'])?>
					<form id="top_sale_terr_stat_frm_to" method="post">
				        <div style="text-align: right">
				        	<b>From</b> : <input type="text" id="ter_date_from"
				                    name="date_from" value="<?php echo date('d-m-Y',time()-90*60*60*24)?>" />
				            <b>To</b> : <input type="text" id="ter_date_to"
				                    name="date_to" value="<?php echo date('d-m-Y',time())?>" /> 
				            <button type="submit" class="sbutton small green"><span>Go</span></button>
				        </div>
				    </form>				
				</div>
			</div>
			<div class="mod_widget_content" style="height:286px !important">
				<div id="br_top_sale_terr_stat">
					<div class="terr_stat_view">
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="fl_left mod_widget_row">
		<!----------------------------------Top Franchises information block--------------------------->
		<div class="mod_widget_small" id="br_franch_stat">
			<div class="mod_widget_title">
				<div class="heading_wrap"><span class="ttl"><span> 
				</div>
			</div>
			<div class="mod_widget_content">
				<div class="br_franch_stat_view">
				</div>
			</div>
		</div>
		<!----------------------------------Town sales statisticks information block--------------------------->
		<div class="mod_widget_large" id="br_town_stat">
			<div class="mod_widget_title">
				<div class="heading_wrap"><span class="ttl"><span> 
				</div>
			</div>
			<div class="mod_widget_content">
				<div class="br_town_stat_view">
				</div>
			</div>
		</div>
	</div>
</div>

<!----------------------------------Allocate rack to products of a brand modal html--------------------------->
<div id="allocate_rack_dlg" title="Allocate Rack">
	<div class="prod_head">
		Choose Products to allot
	</div>
	<div class="products_list">
	</div>
	<div class="allocate_rack_dlg_wrap">
		<div class="fl_left" style="width:100%;margin-top:10px">
			<b style="float:left;margin:3px 6px 0 0">Rack :</b> <select name="rack" id="rack_id">
    			<option value="0">Choose</option>
	    			<?php foreach($rbs as $r){?>
						<option value="<?=$r['id']?>" <?=$rb['rack_bin_id']==$r['id']?"selected":""?>>
							<?=$r['rack_name']?>-<?=$r['bin_name']?>
						</option>
					<?php }?>
    		</select>
		 </div>
	</div>
</div>

<!----------------------------------PO Details of a selected vendor--------------------------->
<div id="ven_po_det">
	<div class="mod_widget_large" style="width:100%">
		<div class="mod_widget_title">
			<div class="heading_wrap">
					PO Details
			</div>
		</div>
		<div class="mod_widget_content ven_po_det" style="height:340px">
		</div>
	</div>
</div>


<script>
// Open rack allocate dialog on click
$('.allot_rack').live('click',function(){
	var id='<?php echo $this->uri->segment(3) ?>';
	$('#allocate_rack_dlg').data('brandid',id).dialog('open');
});

// rack allocate dialog
$('#allocate_rack_dlg').dialog({
		modal:true,
		autoOpen:false,
		width:550,
		height:450,
		autoResize:true,
		open:function(){
						dlg = $(this);
						id=dlg.data('brandid');
						
						//ajax function to list out products of a brand
						$.getJSON(site_url+'/admin/jx_prod_bybrand/'+id,'',function(resp){
							if(resp.status == 'error')
							{
								alert(resp.message)	
							}
							else
							{
								prod_html="";
								$.each(resp.products,function(i,p){
									prod_html+='<div class="prod_select_wrap_'+p.product_id+' " prodid="'+p.product_id+'" style="margin:5px 0px;padding:4px"><input type="checkbox" class="prod_check" prodid="'+p.product_id+'"><a target="_blank" href="'+site_url+'/admin/product/'+p.product_id+'">'+p.product_name+'</a></div>';
								});
							}
							$('.products_list').html(prod_html);
						});
		},
		buttons: {
				    "submit": function() { 
				    	var rack_loc = $('#rack_id').val();
				    	var prod_ids=[];
				    	var i=0;
				    	
				    	//selected prodids push to prod_ids array
						$('.selected').each(function(){
							prod_ids.push($(this).attr('prodid'));
						});
				    	
				    	//check if prod_ids array not empty 
				    	if(prod_ids.length != 0)
				    	{
				    		//Ajax function to allocate rack with inputs rack loc,product ids
				    		$.post(site_url+'/admin/jx_allot_rack_forproduct',{rack_loc:rack_loc,ids:prod_ids},function(resp){
				    			if(resp.error=='error')
				    			{
				    				alert(message);
				    				$('#allocate_rack_dlg').dialog('close');
				    			}else
				    			{
				    				$('#myflashwrapper').html('Rack Location Alloted').fadeIn().delay(3000).fadeOut();
				    				$('#allocate_rack_dlg').dialog('close');
				    			}
				    		},'json');
						}else
				    	{
				    		alert('Please Choose atleast 1 product before submit');
				    	}
				  	}
		} 
});

//Select corresponding product row on click checkbox
$('.prod_check').live('click',function(){
	var pid=$(this).attr('prodid');
	
	if($('.prod_select_wrap_'+pid).hasClass('selected'))
		$('.prod_select_wrap_'+pid).removeClass('selected');
	else
		$('.prod_select_wrap_'+pid).addClass('selected');
});

//Call Total sales statistics function of a brand according to selected dates on submit
$("#ttl_sales_stat_frm_to").bind("submit",function(e){
    e.preventDefault();
    total_sales();
    return false;
});

//Call Territory sales function of a brand according to selected dates on submit
$("#top_sale_terr_stat_frm_to").bind("submit",function(e){
    e.preventDefault();
    $('#br_top_sale_terr_stat .terr_stat_view').unbind();
    $('#br_town_stat .town_stat_view').unbind();
   	top_sale_terr();
   	$('#br_town_stat').hide();
    $('#br_franch_stat').hide();
    return false;
});


//Call Top Franchises list function of a brand according to selected dates on submit
$("#franch_sales_stat_frm_to").bind("submit",function(e){
    e.preventDefault();
    brand_sales_by_franch();
    return false;
});

//Call Top Category list function of a brand according to selected dates on submit
$("#cat_sales_stat_frm_to").bind("submit",function(e){
    e.preventDefault();
    brand_sales_by_category();
    return false;
});

//open dialog to give vendor po information
$('.vendor_more_wrap').click(function(){
	var vid=$(this).attr('vid');
	var vname=$(this).attr('vname');
	$('#ven_po_det').data('vendorid',vid).dialog('open');
	$('#ven_po_det').dialog('option', 'title', vname);
});

//PO details dialog of a vendor for selected brand
$('#ven_po_det').dialog({
		modal:true,
		autoOpen:false,
		width:550,
		height:500,
		autoResize:true,
		open:function(){
						dlg = $(this);
						var id=dlg.data('vendorid');
						var brandid='<?php echo $this->uri->segment('3')?>';
						
						//Loading image brfore data load
						$('.ven_po_det').html('<div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
						
						//Ajax function to get po details with inputs vendor id,brand id
						$.post(site_url+'/admin/jx_podet_by_brand/',{brandid:brandid,vendor_id:id},function(resp){
							if(resp.status == 'error')
							{
								$('.br_franchise_list').html("<div class='br_alert_wrap_text'>No POs found</div>");
							}else
							{
									
									var top_po_list_html = '';
										top_po_list_html +='<ul>';
										top_po_list_html +='<li><span class="model_widget_span">Total PO Value ('+resp.ttl_po_count+')  :</span>Rs. '+resp.ttl_po_ttl+'</li>';
										top_po_list_html +='<li><span class="model_widget_span">Total Open PO Value ('+resp.ttl_open_po_count+')  :</span>Rs. '+resp.ttl_open_po_ttl+'</li>';
										top_po_list_html +='<li><span class="model_widget_span">Total Partial Value ('+resp.partial_po_count+')  :</span>Rs. '+resp.partial_po_ttl+'</li>';
										top_po_list_html +='<li><span class="model_widget_span">Total Complete Value ('+resp.complete_po_count+')  :</span>Rs. '+resp.complete_po_ttl+'</li>';
										top_po_list_html +='<li><span class="model_widget_span">Total Cancelled Value ('+resp.cancelled_po_count+')  :</span>Rs. '+resp.cancelled_po_ttl+'</li>';
										top_po_list_html +='<li><span class="model_widget_span">Latest 10 POs  :</span></li>';
										top_po_list_html +='</ul>';
										top_po_list_html +='<table class="datagrid" width="100%"><tr><td><b>Sl No.</b></td><td><b>PO ID</b></td><td><b>Date</b></td><td><b>Total Value</b></td></tr>';
								 		$.each(resp.latest_po_list,function(a,b){
											top_po_list_html += '<tr><td>'+(++a)+'</td>'
																	+'<td><span><a href="'+site_url+'/admin/viewpo/'+b.po_id+'" target="blank">'+b.po_id+'</a></span></td>'
																	+'<td><span>'+b.date+'</span></td>'
																	+'<td><span>'+b.total_value+'</span></td>'
																	+'</tr>'
										});
										top_po_list_html +='</table>'
									$('.ven_po_det').html(top_po_list_html);
							}
						},'json');
	},
	buttons: {
	    "close": function() { 
	    	$('#ven_po_det').dialog('close');
	  	}
	} 
});

//Function to display brand sales statistics
function total_sales()
{
	brandid ="<?php echo $this->uri->segment(3);?>";
	var start_date= $('#ttl_date_from').val();
	var end_date= $('#ttl_date_to').val();
	var brand_name="<?=ucfirst($brand['name'])?>";
	$('#total_stat .total_stat_view').html('<div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
	
	$.getJSON(site_url+'/admin/jx_brand_sales/'+brandid+'/'+start_date+'/'+end_date,'',function(resp){
		if(resp.summary == 0)
		{
			$('#total_stat .total_stat_view').html("<div class='br_alert_wrap' style='padding:113px 0px'>No Sales statistics found between "+start_date+" and "+end_date+"</div>" );	
		}
		else
		{
			$('#total_stat .total_stat_view').empty();
			plot2 = $.jqplot('total_stat .total_stat_view', [resp.summary], {
		       	seriesDefaults: {
			        showMarker:true,
			        pointLabels: { show:true }
			      },
			      
				  axes:{
			        xaxis:{
			          renderer: $.jqplot.CategoryAxisRenderer,
			          	label:'Date',
				          labelOptions:{
				            fontFamily:'Arial',
				            fontSize: '10px'
				          },
				          labelRenderer: $.jqplot.CanvasAxisLabelRenderer
			        },
			        yaxis:{
				          label:'Total Sales in Rs',
				          labelOptions:{
				            fontFamily:'Arial',
				            fontSize: '10px'
				          },
				          labelRenderer: $.jqplot.CanvasAxisLabelRenderer
				        }
			      }
			});
		}
	});
}

//Function to display Top categories
function cat_sales()
{
	var brand_name="<?=ucfirst($brand['name'])?>";
	brandid ="<?php echo $this->uri->segment(3);?>";
	var start_date= $('#date_from').val();
	var end_date= $('#date_to').val();
	$('.cat_head').html("<h4 style='margin:0px 22px;width:20%'>Category Sales</h4>");
	$('#br_cat_stat .br_cat_stat_view').html('<div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>' );
	
	$.getJSON(site_url+'/admin/jx_catsales_bybrand/'+brandid+'/'+start_date+'/'+end_date,'',function(resp){
	  	if(resp.result == 0)
		{
			$('#br_cat_stat .br_cat_stat_view').html("<div class='br_alert_wrap' style='padding:113px 0px;'>No Category sales found between "+start_date+" and "+end_date+"</div>" );	
		}
		else
		{	
		// reformat data ;
			$('#br_cat_stat .br_cat_stat_view').empty();
			var resp = resp.result;
			plot3 = jQuery.jqplot('br_cat_stat .br_cat_stat_view', [resp], 
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
				    formatString:'Category : %s'
				},
				grid: {borderWidth:0, shadow:false,background:'#eaeaea'},
		       legend:{show:true,rendererOptions: {
				            numberColumns: 2
				        } }
		    });
		 }
	});
}

//Function to display Top Vendors for a selected brand
function vendors_details()
{
	var brand_name="<?=ucfirst($brand['name'])?>";
	brandid ="<?php echo $this->uri->segment(3);?>";
	var start_date= $('#date_from').val();
	var end_date= $('#date_to').val();
	$('.br_vendor_head').html("<h4>Vendors </h4>");
	$('#br_vendors_det .br_vendors_det_view').html('<div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
	$.getJSON(site_url+'/admin/jx_vendors_bybrand/'+brandid+'/'+start_date+'/'+end_date,'',function(resp){
	  	if(resp.result == 0)
		{
			$('#br_vendors_det .br_vendors_det_view').html("<div class='br_alert_wrap' style='padding:113px 0px;'>No Vendors found between "+start_date+" and "+end_date+"</div>" );	
		}
		else
		{	
			// reformat data ;
			$('#br_vendors_det .br_vendors_det_view').empty();
			var resp = resp.result;
			plot3 = jQuery.jqplot('br_vendors_det .br_vendors_det_view', [resp], 
			{
				seriesDefaults:{
		            renderer: jQuery.jqplot.PieRenderer,
		            pointLabels: 
		            {
		            	 show: true 
		            },
	                rendererOptions: 
	                {
	                    // Put data labels on the pie slices.
	                    // By default, labels show the percentage of the slice.
	                    showDataLabels: true,
	                 }
		        },
		        highlighter: 
		        {
				    show: true,
				    useAxesFormatters: false, // must be false for piechart   
				    tooltipLocation: 's',
				    formatString:'Vendor : %s'
				},
				
				grid: 
				{
					borderWidth:0, shadow:false,background:'#eaeaea'
				},
		       legend:
		       {
		       	show:true,rendererOptions: {
				      numberColumns: 2
				} 
				}
		    });
		 }
	});
}

//Function to display Top Franchises for a selected brand
function brand_sales_by_franch()
{
	var brand_name="<?=ucfirst($brand['name'])?>";
	var start_date= $('#frch_date_from').val();
	var end_date= $('#frch_date_to').val();
	$('.top_franch_head').html("<h4>Top Franchises for "+brand_name+" from "+start_date+" to "+end_date+"</h4>");
	$('.br_franchise_list').html('<div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
	
	$.post(site_url+'/admin/jx_topfranchisebybrandid/',{brandid:brandid,start_date:start_date,end_date:end_date},function(resp){
		if(resp.status == 'error')
		{
			$('.br_franchise_list').html("<div class='br_alert_wrap_text'>No Products sold for "+brand_name+" </div>");
		}else
		{
			var top_fran_list_html = '';
				top_fran_list_html +='<ul>'
				$.each(resp.top_fran_list,function(a,b){
					top_fran_list_html += '<li>'+(++a)+''
										+'<span><a href="'+site_url+'/admin/pnh_franchise/'+b.franchise_id+'" target="blank">'+b.franchise_name+'</a>'+'</span>'
										+'<span class="fl_right">Total : Rs.'+b.ttl+'</span></li>'
					});
				top_fran_list_html +='</ul>'
				$('.br_franchise_list').html(top_fran_list_html);
		}
	},'json');
	
}

//Function to display Top Categories for a selected brand
function brand_sales_by_category()
{
	var brand_name="<?=ucfirst($brand['name'])?>";
	var start_date= $('#cat_date_from').val();
	var end_date= $('#cat_date_to').val();
	$('.br_topcat_list').html('<div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
	
	$.post(site_url+'/admin/jx_categoriesbybrandid/',{brandid:brandid,start_date:start_date,end_date:end_date},function(resp){
		if(resp.status == 'error')
		{
			$('.br_topcat_list').html("<div class='br_alert_wrap_text'>No Categories sold for "+brand_name+" </div>");
		}else
		{
			var cat_list_html = '';
				cat_list_html +='<ul>'
		 		$.each(resp.cat_list,function(a,b){
					cat_list_html += '<li>'+(++a)+''
											+'<span><a href="'+site_url+'/admin/viewcat/'+b.cid+'" target="blank">'+b.cat_name+'</a>('+b.qty_sold+')</span>'
											+'<span class="fl_right">Rs. '+b.ttl+'</span>'
										+'</li>';
				});
				cat_list_html +='</ul>'
			$('.br_topcat_list').html(cat_list_html);
		}
	},'json');
}

//Function to display brand territory sales statistics
function top_sale_terr()
{
	var brand_name="<?=ucfirst($brand['name'])?>";
	brandid ="<?php echo $this->uri->segment(3);?>";
	var start_date= $('#ter_date_from').val();
	var end_date= $('#ter_date_to').val();
	$('#br_franch_stat .br_franch_stat_view').unbind('jqplotDataClick');
	$('.terr_head').html("<h4>Territory sales for "+brand_name+" from "+start_date+" to "+end_date+" </h4>");
	$('#br_top_sale_terr_stat .terr_stat_view').html('<div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
	
	$.getJSON(site_url+'/admin/jx_top_sale_terr/'+brandid+'/'+start_date+'/'+end_date,'',function(resp){
		if(resp.summary == 0)
		{
			$('#br_top_sale_terr_stat .terr_stat_view').html("<div class='br_alert_wrap_text' style='padding:113px 0px'>No Territories found for "+brand_name+" from "+start_date+" to "+end_date+ "</div>");
		}
		else
		{
			// reformat data ;
			$('#br_top_sale_terr_stat .terr_stat_view').empty();
			var resp = resp.summary;
			plot2 = $.jqplot('br_top_sale_terr_stat .terr_stat_view', [resp], {
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
			          fontFamily: 'Arial',
			          fontSize: '10px',
			          angle: -30
			        }
			    },
			    axes: {
			     xaxis: {
			        renderer: $.jqplot.CategoryAxisRenderer,
			      	 label:'Territories',
				          labelOptions:{
				            fontFamily:'Arial',
				            fontSize: '10px'
				          }
				     },
				    yaxis: {
			      	label:'Total Sales in Rs',
				          labelOptions:{
				            fontFamily:'Arial',
				            fontSize: '10px'
				          },
				      labelRenderer: $.jqplot.CanvasAxisLabelRenderer    
			      }
			    }
			});
			
			$('#br_top_sale_terr_stat .terr_stat_view').bind('jqplotDataClick', function(ev,seriesIndex,pointIndex,data) {
				var terr_id = resp[pointIndex][2];
			 	var terr_name = resp[pointIndex][0];
			 	$('#br_town_stat .br_town_stat_view').empty();
			 	town_sales(terr_id,terr_name);
			 	franch_sales(terr_id,terr_name);
			 	all_ter_franchises(terr_id,terr_name);
			 	//cat_sales(terr_id,terr_name);
				$('#town_head').html('Towns Sales for '+terr_name+' territory');
				$('#br_town_stat .br_town_stat_view').unbind('jqplotDataClick');
				$('#br_town_stat .br_town_stat_view').html('<div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');	
				$('#franch_head').html('Top Franchise in '+terr_name+' territory');
				$('#br_franch_stat .br_franch_stat_view').unbind('jqplotDataClick');
				$('#br_franch_stat .br_franch_stat_view').html('<div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');	
				
			});
		}
	});
}

//Function to display franchises of a territory  
function all_ter_franchises(terr_id,terr_name)
{
	brandid ="<?php echo $this->uri->segment(3);?>";
	start_date= $('#ter_date_from').val();
	end_date= $('#ter_date_to').val();
	$.post(site_url+'/admin/jx_getallfranchisesbybrandid_terrid/',{brandid:brandid,terr_id:terr_id,start_date:start_date,end_date:end_date},function(resp){
	if(resp.status == 'error')
	{
		alert(resp.error);
	}else
	{
		 var fran_list_html = '';
		  fran_list_html += '<h4>Franchise List for '+terr_name+' territory</h4>';
		 fran_list_html +='<span class="popclose_button b-close"><span>X</span></span><div style="overflow:auto;float:left;height:265px;width:600px;">';
		 fran_list_html += '<table class="datagrid" width="100%"><thead><tr><th>Sl.No</th><th>Name</th><th>Total Sales</th></tr></thead><tbody>';
			$.each(resp.fran_list,function(a,b){
				fran_list_html += '<tr>'
										+'<td>'+(++a)+'</td>'
										+'<td>'+b.franchise_name+'</td>'
										+'<td>'+b.ttl+'</td>'
									+'</tr>';
			});
		fran_list_html += '</tbody></table></div>';
		$('#br_franch_popup').html(fran_list_html);
	}
	},'json');
}

//Function to display town sales on territory select 
function town_sales(terr_id,terr_name)
{
	brandid ="<?php echo $this->uri->segment(3);?>";
	start_date= $('#ter_date_from').val();
	end_date= $('#ter_date_to').val();
	$('#br_town_stat .heading_wrap .ttl').html('Town Sales from '+start_date+' to '+end_date+'<span class="fl_right" style="padding:10px;color:#000;font-size:12px">Territory : '+terr_name+'</span>')
	$('#br_town_stat .br_town_stat_view').html('<div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>' );
	$.getJSON(site_url+'/admin/jx_gettownsbybrandid/'+brandid+'/'+terr_id+'/'+start_date+'/'+end_date,'',function(resp){
		if(resp.summary == 0)
		{
			$('#br_town_stat .br_town_stat_view').html("<div class='br_alert_wrap' style='padding:113px 0px'>No Town stats</div>" );	
		}
		else
		{
			// reformat data ;
			var resp=resp.summary;
			$('#br_town_stat .br_town_stat_view').empty();
			plot2 = $.jqplot('br_town_stat .br_town_stat_view', [resp], {
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
			          	label:'Towns',
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
			$('#br_town_stat .br_town_stat_view').bind('jqplotDataClick', function(ev,seriesIndex,pointIndex,data) {
					var town_id = resp[pointIndex][2];
				 	var town_name = resp[pointIndex][0];
				 	//town_brand_sales(town_id,town_name);
				 	town_franch_sales(town_id,town_name);
				 	all_town_franchises(town_id,town_name);
				 	$('#franch_head').html('Top Franchise in '+town_name+' town');
					$('#br_franch_stat .br_franch_stat_view').unbind('jqplotDataClick');
					$('#br_franch_stat .br_franch_stat_view').html('<div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');	
				 	//town_cat_sales(town_id,town_name);
			});
		  }
		});
	$('#br_town_stat').show();
}

function all_town_franchises(town_id,town_name)
{
	brandid ="<?php echo $this->uri->segment(3);?>";
	start_date= $('#ter_date_from').val();
	end_date= $('#ter_date_to').val();
	$.post(site_url+'/admin/jx_getallfranchisesbybrandid_townid/',{brandid:brandid,town_id:town_id,start_date:start_date,end_date:end_date},function(resp){
	if(resp.status == 'error')
	{
		alert(resp.error);
	}else
	{
		 var fran_list_html = '';
		 fran_list_html += '<h4>Franchise List for '+town_name+' town</h4>';
		 fran_list_html +='<span class="popclose_button b-close"><span>X</span></span><div style="overflow:auto;float:left;height:265px;width:600px;">';
		 fran_list_html += '<table class="datagrid" width="100%"><thead><tr><th>Sl.No</th><th>Name</th><th>Total Sales</th></tr></thead><tbody>';
			$.each(resp.fran_list,function(a,b){
				fran_list_html += '<tr>'
										+'<td>'+(++a)+'</td>'
										+'<td>'+b.franchise_name+'</td>'
										+'<td>'+b.ttl+'</td>'
									+'</tr>';
			});
		fran_list_html += '</tbody></table></div>';
		$('#br_franch_popup').html(fran_list_html);
		$('.br_franch_popup').show();
	}
	},'json');
}

//Function to display Franchise sales on territory click
function franch_sales(terr_id,terr_name)
{
	brandid ="<?php echo $this->uri->segment(3);?>";
	start_date= $('#ter_date_from').val();
	end_date= $('#ter_date_to').val();
	$('#br_franch_stat .heading_wrap .ttl').html('<span style="font-size:12px">'+terr_name+' Territory Franchise Sales from '+start_date+' to '+end_date+'</span>')
	$.getJSON(site_url+'/admin/jx_getfranchisebybrand/'+brandid+'/'+terr_id+'/'+start_date+'/'+end_date,'',function(resp){
		if(resp.summary == 0)
		{
			$('#br_franch_stat .br_franch_stat_view').html("<div class='br_alert_wrap' style='padding:113px 0px'>No Franchises Found</div>" );	
		}
		else
		{
			// reformat data ;
			$('#br_franch_stat .br_franch_stat_view').empty();
			plot2 = $.jqplot('br_franch_stat .br_franch_stat_view', [resp.summary], {
		       	 seriesDefaults: {
		                renderer:$.jqplot.BarRenderer,
		                pointLabels: { show: true, location: 'e', edgeTolerance: -15 },
		                shadowAngle: 135,
		                rendererOptions: {
		                    barDirection: 'horizontal'
		                }
		            },
		            axesDefaults: {
				        tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
				        tickOptions: {
				          fontFamily: 'tahoma',
				          fontSize: '11px',
				          angle: -30
				        }
				    },
		            axes: {
		                yaxis: {
		                    renderer: $.jqplot.CategoryAxisRenderer
		                }
		            }
			  });
		   }
		});
	$('#br_franch_stat').show();
}

//Function to display town sales on town click
function town_franch_sales(town_id,town_name)
{
	catid ="<?php echo $this->uri->segment(3);?>";
	state_id ="<?php echo $this->uri->segment(3);?>";
	start_date= $('#ter_date_from').val();
	end_date= $('#ter_date_to').val();
	$('#br_franch_stat .heading_wrap .ttl').html('<span style="font-size:12px">'+town_name+' Town Franchise Sales from '+start_date+' to '+end_date+'</span>')
	$.getJSON(site_url+'/admin/jx_getfranchisebybrandid_townid/'+catid+'/'+town_id+'/'+start_date+'/'+end_date,'',function(resp){
		    	
    	if(resp.summary == 0)
		{
			$('#br_franch_stat .br_franch_stat_view').html("<div class='br_alert_wrap' style='padding:113px 0px'>No Franchises Found</div>" );	
		}
		else
		{
			// reformat data ;
			$('#br_franch_stat .br_franch_stat_view').empty();
			plot2 = $.jqplot('br_franch_stat .br_franch_stat_view', [resp.summary], {
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
			          	label:'Franchise',
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
		   }
		});
}

</script>

<!-- the jScrollPane script -->
<script type="text/javascript" src="<?=base_url()?>/js/custom_scrollbar/jquery.jscrollpane.min.js"></script>

<script type="text/javascript" src="<?=base_url()?>/js/custom_scrollbar/mwheelIntent.js"></script>

<!-- the mousewheel plugin - optional to provide mousewheel support -->
<script type="text/javascript" src="<?=base_url()?>/js/custom_scrollbar/jquery.mousewheel.js"></script>

<style>
.br_max_height_wrap table{width: 100%;}
</style>
<?php
