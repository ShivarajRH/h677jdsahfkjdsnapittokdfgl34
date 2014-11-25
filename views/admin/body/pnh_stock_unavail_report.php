<style>.leftcont{display: none}
.filter_blk
{
	display: inline-block;
    width: 16%;
}	
.filter_blk b
{
	vertical-align: super;
}	

.order_summ
{
	margin:19px 0;
}
.toggle_act
{
    background: none repeat scroll 0 0 #ffffdf;
    border: 1px solid #aaa;
    cursor: pointer;
    float: right;
    margin-right: 12px;
    padding: 3px;
    font-weight:bold;
}
.place_po
{
	cursor:pointer;
}
.stk
{
	cursor:pointer;
}
.stk_sugg_blk
{
	font-size:9px;
}
.selected
{
	color:green !important;
	font-weight:bold;
}
.deal_det_blk div
{
	margin: 3% 0;
}
.deal_det_blk a
{
	font-weight: bold;
}
.deal_det_blk .deals_sel
{
	margin:2%;
}
.highlight_row
{
	background:#ff9600
}
.toggle_date_exceed
{
	float: right;
    margin-right: 12px;
    margin-top: 4px;
    cursor:pointer;
}
</style>
<div class="container">
	<h2>Stock Unavailable report </h2>
	<div>
		<form action="<?php echo site_url('admin/pnh_stock_unavail_report');?>" id="gen_pnh_unavail_report_frm" method="post">
			<div class="filter_blk">
					<b>Orders :</b>
					<select name="date_type" class="chzn-select" data-placeholder="Choose" style="width:130px;"  >
						<option value="0">Till Date</option>
						<option value="1">Date Range</option>
					</select>
			</div>
			<div class="filter_blk">
					<b>Territory :</b>
					<select class="chzn-select" data-placeholder="Choose"  name="tids[]" style="width:130px;" >
					</select>
			</div>
			<div class="filter_blk">
					<b>menu :</b>
					<select class="chzn-select" data-placeholder="Choose"  name="mids[]" style="width:130px;" >
					</select>
			</div>
			<div class="filter_blk">
					<b>Type :</b>
					<select class="chzn-select" data-placeholder="Choose"  name="menu_type" style="width:130px;" >
						<option value="-1">All</option>
						<option value="1">SK</option>
						<option value="0">SIT</option>
					</select>
			</div>
			<div class="filter_blk">
					<b>Publish :</b>
					<select class="chzn-select" data-placeholder="Choose"  name="publish_type" style="width:130px;" >
						<option value="-1">All</option>
						<option value="1">Publish</option>
						<option value="0">Un Publish</option>
					</select>
			</div>
			<!--
			<div class="filter_blk">
					<b>Show :</b>
					<select class="chzn-select" data-placeholder="Choose" name="ship_date_filter" style="width:130px;" >
						<option value='1'>All Products</option>
						<option value='2'>Shipment Date Exceed Products</option>
					</select>
			</div>-->
			<div class="filter_blk">
					<b>Show :</b>
					<select class="chzn-select" data-placeholder="Choose" name="sourceable" style="width:130px;" >
						<option value='all'>All</option>
						<option value='1'>Sourceable</option>
						<option value='0'>Not Sourceable</option>
					</select>
			</div>
			<div id="ordersby_daterange" class="" style="display: none">
					<b>Order Date Range :</b> 
					<input type="text" id="from_date" size="10" name="from" value="<?php echo date('Y-m-01')?>" />
					<input type="text" id="to_date" size="10" name="to" value="<?php echo date('Y-m-d')?>" />
			</div>
			<input type="submit" class="button button-default button-action button-tiny btn-submit" value="Submit" style="display:none"/>
			<!-- Place Purchase button Order for less Stock Products -->
			
		</form>
		<form id="place_po_form" action="<?=site_url("admin/po_product_bytopsold")?>" method="post">
			<input type="hidden" name="po_arr" class="prd_arr" value="">
		</form>
		<div id="pnh_unavail_prod_list">
			<div class="order_summ">
				<span>Total Products: <b>0</b></span>
				<div class="button button-action button-tiny btn-submit place_po ">Place PO</div>
				<span class="toggle_act">OpenAll</span>		
				<a class="toggle_date_exceed toggle_date_exceed_show">Show Ship date exceed products</a>	
			</div>
			<table class="datagrid" width="100%" cellpadding="0" cellspacing="0">
				<thead>
					<th width="20"><b>#</b></th>
					<!--<th width="100"><b>Brand</b></th>-->
					<th ><b>Product</b></th>
					<th width="70" style="text-align: center"><b>Offer Price</b></th>
					<th width="50" style="text-align: center"><b>Member Price</b></th>
					<th width="50" style="text-align: right"><b>Order</b></th>
					<th width="50" style="text-align: right"><b>Stock</b></th>
					<th width="50"><b>Required</b></th>
					<th width="90" style="text-align: center"><b>Status</b></th>
					<th width="30" style="text-align: center"><b>Deal</b></th>
					<th width="30" style="text-align: center"><b>PO's</b><br  /><span ><input type="checkbox" class="all"></span></th>
				</thead>
				<tbody>
					
				</tbody>
			</table>
			<div class="pagination"></div>
		</div>
		<!-- Place Purchase button Order for less Stock Products -->
		<div class="button button-action button-tiny btn-submit place_po ">Place PO</div>
		
		<!-- PO placing product list modal ------>
		<div id="place_po_dlg" title="Place Po">
			<h5>Total of  <span id="prd_ttl"></span> Products selected</h5>
			<div class="place_po_blk"></div>
		</div>
		
		<!-- Update Deal status modal ------>
		<div id="deal_det_dlg" title="Change Deal Status">
			<div class="deal_det_blk"></div>
		</div>
		
		<!-- Update Expected date modal ------>
		<div id="exp_shipdate_dlg" title="Update Shipment Date">
			<div class="ship_blk"></div>
		</div>
		
		<!-- Modal for PO products selected display-->
		<div id="dlg_openpolist" title="Open PO List"></div>
	</div>
</div>	

<style>
	.pagination{
		display: block;
		margin:3px;
		text-align: left;
	}
	.pagination a{
		display: inline-block;
		padding:4px;
		background: #fcfcfc;
		color: #555;
	}
	.pagination strong{
		display: inline-block;
		padding:4px;
		background: #555;
		color: #FFF;
	}
	.datagrid th{border:0px;}
	.smalldatagrid {border-collapse: collapse}
	.smalldatagrid th{background: #fafafa !important;border:0px;text-align: left;}
	.prod_ord_det{float: right;font-weight: bold;font-size: 16px;color:#F1F1F1;background: #999;padding:2px 4px;display: inline-block;}
	.tbl_subgrid_content{display: none}
	.subdatagrid{background: #FFFFF0 !important;}
	.subdatagrid td{padding:5px;font-size: 12px !important;}
	.datagrid td{border:0px;}
	.datagrid td{border-bottom:1px dotted #ccc;} 
	.btn-submit{float:right}
	.row_select
	{
		background:#F5F592;
	}
	#ordersby_daterange{display: inline-block;margin: 10px 0;}
</style>

<script type="text/javascript">

$(function(){
	
});

function load_all_franchises(){
	$('select[name="tids[]"] option').each(function(){
		$(this).attr('selected','selected');
	});
	$('select[name="tids[]"]').trigger("liszt:updated");
}
var jx_req_1 = null;
var jx_req_2 = null; 
var clear_terrlist = 1;

function get_pnh_unavailordersumm(stat){
	if(clear_terrlist)
		$('select[name="tids[]"] option').remove().trigger("liszt:updated");
	
	
	$('select[name="mids[]"] option').remove().trigger("liszt:updated");
	
	$('.order_summ b').html("");
		if(jx_req_1 != null)
			jx_req_1.abort();
	var param = $('#gen_pnh_unavail_report_frm').serialize();
	jx_req = $.post(site_url+'/admin/jx_pnh_stock_unavail_terr_menu',param,function(resp){
			if(resp.status == 'error'){
				alert(resp.message);
			}else{
				
				var total_qty = 0;
				var terr_html_list = '';
					if(clear_terrlist)
					{
						$.each(resp.terr,function(terr_id,terr_det){
							total_qty+= terr_det[1]*1;
							terr_html_list += '<option value="'+terr_id+'">'+terr_det[0]+' </option>';
						});
						terr_html_list = '<option value="0">All Territories </option>'+terr_html_list;
						$('select[name="tids[]"]').append(terr_html_list).trigger("liszt:updated");
						clear_terrlist = 0;	
					}
					total_qty = 0;
				var menu_html_list = '';
					$.each(resp.menu,function(menu_id,menu_det){
						total_qty+= menu_det[1]*1;
						menu_html_list += '<option value="'+menu_id+'">'+menu_det[0]+' </option>';
					});
					menu_html_list = '<option value="0">All Menu </option>'+menu_html_list;
					$('select[name="mids[]"]').append(menu_html_list).trigger("liszt:updated");
					
					reset_form_action();	
			}
		},'json');
	
}

function reset_form_action()
{
	$('#gen_pnh_unavail_report_frm').attr('action',site_url+'/admin/pnh_stock_unavail_report');
	hndl_pnh_unvail_prod_report_frm();
}

function fmt_date_slash(dObj){
	var d = (dObj.getDate())>9?(dObj.getDate()):'0'+(dObj.getDate());
	var m = (dObj.getMonth()+1)>9?(dObj.getMonth()+1):'0'+(dObj.getMonth()+1);
	
	//return dObj.getFullYear()+'-'+m+'-'+d;
	return d+'/'+m+'/'+ dObj.getFullYear();
	
}



function get_franchise_level(d)
{
	if(d >= 0 && d <=30)
		return '<span style="font-size: 9px;background-color:#cd0000;color:#fff;padding:2px 3px;border-radius:3px;">Newbie</span>';
	else if(d > 30 && d <=60)
		return '<span style="font-size: 9px;background-color:orange;color:#fff;padding:2px 3px;border-radius:3px;">MidLevel</span>';
	else if(d > 60 )
		return '<span style="font-size: 9px;background-color:green;color:#fff;padding:2px 3px;border-radius:3px;">Experienced</span>';
}

var tesp_data=new Array();

function hndl_pnh_unvail_prod_report_frm()
{
	
	$('.order_summ b').html('');
	//$('#pnh_unavail_prod_list table tbody').css('opacity',0.5);
	$('#pnh_unavail_prod_list table tbody').html('<tr><td colspan="9"><div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div></td></tr>');
	var param = $('#gen_pnh_unavail_report_frm').serialize();
	if(jx_req_2 != null)
		jx_req_2.abort();
	
	jx_req_2 = $.post($('#gen_pnh_unavail_report_frm').attr('action'),'a=b&'+param,function(resp){
			
			if(resp.status == 'error'){
				alert(resp.message);
				$('#pnh_unavail_prod_list table').hide();
				$('#pnh_unavail_prod_list .pagination').hide();
				$('#pnh_unavail_prod_list table tbody').css('opacity',1);
			}else{
				$('#pnh_unavail_prod_list table').show();
				$('#pnh_unavail_prod_list .pagination').show();
				
				$('.order_summ b').html(resp.total);
				
				var tbl_html = '';
				tesp_data=resp.data;
					$.each(resp.data,function(i,d){
						tbl_html += '<tr class="avail_report avl_report_'+d.product_id+'" pid="'+d.product_id+'" sourceable="'+d.is_sourceable+'" exp_date="'+d.expected_ship_date+'" curr_date="'+resp.curr_date+'">';
						tbl_html += '<td>'+((resp.pg*1)+i+1)+'</td>';
						
						//d.fran_order_det = 'A:2013-04-16:1:2452345235,B:2013-04-16:1:2452345235,C:2013-04-16:1:2452345235';
						
						var fr_order_list = d.fran_order_det.split(',');
						var pqty = 0;
						var fran_order_det_html = '<table width="100%" class="subdatagrid" cellpadding=0 cellspacing=0>';
						var fr_order_list_sorted = new Array();
							
							fran_order_det_html += '<tr>';
							fran_order_det_html += '	<td style="font-weight:bold">Level</td>';
							fran_order_det_html += '	<td style="font-weight:bold">Name</td>';
							fran_order_det_html += '	<td style="font-weight:bold">Town</td>';
							fran_order_det_html += '	<td style="font-weight:bold">Order Date</td>';
							fran_order_det_html += '	<td style="font-weight:bold">Qty</td>';
							fran_order_det_html += '	<td style="font-weight:bold">TransID</td>';
							fran_order_det_html += '	<td style="font-weight:bold;font-size:10px" width="150px">Shipment date expected</td>';
							fran_order_det_html += '</tr>';
							
							$.each(fr_order_list,function(a,b){
								var c = b.split(':');
								fran_order_det_html += '<tr>';
								fran_order_det_html += '	<td style="padding-left:0px;width:80px">'+get_franchise_level(c[6])+'</td>';
								fran_order_det_html += '	<td style="padding-left:0px;">'+c[4]+'</td>';
								fran_order_det_html += '	<td width="50">'+c[7]+'</td>';
								fran_order_det_html += '	<td width="100">'+fmt_date_slash(new Date(c[1]*1000))+'</td>';
								fran_order_det_html += '	<td width="30">'+c[5]+'</td>';
								fran_order_det_html += '	<td width="100"><a href="'+site_url+'/admin/trans/'+c[0]+'">'+c[0]+'</a></td>';
								if(c[8]==0)
									fran_order_det_html += '	<td><div class="exp_'+c[2]+'" style="color:red"><input type="checkbox" class="update_shipdate_status"  transid="'+c[0]+'" product_id="'+d.product_id+'" orderid="'+c[2]+'"></div></td>';
								else
									fran_order_det_html += '	<td><span style="color:red">'+c[8]+'</span></td>';
								/*if(c[7] != 0)
								{
									fran_order_det_html += '	<td width="10">'+(parseInt(c[6])?'<a href="'+site_url+'/admin/viewpo/'+parseInt(c[7])+'" target="_blank">PO'+c[7]+'</a>':'&nbsp;')+'</td>';
								}
								else
								{
									fran_order_det_html += '	<td width="60" style="color:red">Not in PO</td>';
								}*/
								fran_order_det_html += '</tr>';
								pqty += c[5]*1; 
							});
							
							fran_order_det_html += '</table>';
							
						
						tbl_html += '<td class="row_click" pid="'+d.product_id+'"><a target="_blank" href="'+site_url+'/admin/product/'+d.product_id+'"><b>'+d.product_name+'</b></a>'; 
						tbl_html += '<span><a href="javascript:void(0)" id="prod_ord_det_'+d.product_id+'" class="prod_ord_det plus">&plus;</a></span><div class="tbl_subgrid_content">'+fran_order_det_html+'</div></td>';
						
						tbl_html += '<td align="center"><b>'+d.dp_prc+'<b></td>';
						tbl_html += '<td align="center"><b>'+d.member_price+'<b></td>';
						tbl_html += '<td align="right"><b>'+pqty+'<b></td>';
						tbl_html += '<td align="right"><b><span class="stk stock_'+d.product_id+'" product_id="'+d.product_id+'" style="color:blue;text-decoration:underline">'+d.avail_qty+'<b></span><div class="stk_sugg_blk stock_suggestion_'+d.product_id+'"></div></td>';
						tbl_html += '<td align="right"><b>'+(pqty-d.avail_qty)+'<b></td>';
						if(d.is_sourceable == 1)
							d.is_sourceable='<span style="color:green;text-align:right;">Sourceable</span>';
						else
							d.is_sourceable='<span style="color:red;text-align:right;">Not Sourceable</span>';	
						tbl_html += '<td align="right"><b>'+d.is_sourceable+'<b></td>';
						
						tbl_html += '<td align="center"><input type="checkbox" class="change_deal_status" onclick="change_deal_status('+d.product_id+')" product_id="'+d.product_id+'"></td>';
						
						
						//<a target="_blank" href="'+site_url+'/admin/pnh_deal/'+d.itemid+'">'+d.deal_name+'</a>
						/*
						if(d.po_id != 0)
						{
							//tbl_html += '	<td align="right">'+d.po_id+'</td>';
							tbl_html += '	<td align="right" width="80><span style="color:green;font-weight:bold"><a href="javascript:void(0)" onclick="load_openpolist('+d.product_id+')" >PO details</a><input type="checkbox" class="sel_prd" pid="'+d.product_id+'" pname="'+d.product_name+'"></span></td>';
						}
						else
						{
							tbl_html += '	<td align="right" style="color:red" width="80">No PO <input type="checkbox" class="sel_prd" pid="'+d.product_id+'" pname="'+d.product_name+'"></td>';
						}*/
						//tbl_html += '	<td align="right" width="80><span style="color:green;font-weight:bold"><a href="javascript:void(0)" onclick="load_openpolist('+d.product_id+')" >PO details</a><input type="checkbox" class="sel_prd" pid="'+d.product_id+'" pname="'+d.product_name+'"></span></td>';
						if(d.po_qty!=0)
						{
							tbl_html += '	<td align="center" width="80"><span style="color:black;font-size:10px;font-weight:bold"><a href="javascript:void(0)" onclick="load_openpolist('+d.product_id+')" style="color:blue;font-size:12px !important;font-weight:bold;text-decoration:underline">'+d.po_qty+' </a></span></td>';
						}
						else
						{
							tbl_html += '	<td align="center" style="color:red" width="80"><input type="checkbox" class="sel_prd" pid="'+d.product_id+'" pname="'+d.product_name+'"></td>';
						}
						tbl_html += '</tr>';
					});
					$('#pnh_unavail_prod_list table tbody').html(tbl_html);
					$('#pnh_unavail_prod_list .pagination').html(resp.pagination);
					$('#pnh_unavail_prod_list table tbody').css('opacity',1);
			}
		},'json');
	return false;
}

function show_filter_products()
{
	$('.avail_report').each(function(){
		var curr_date=$(this).attr('curr_date');
		var exp_date=$(this).attr('exp_date');
		var pid=$(this).attr('pid');	
			exp_date_arr=new Array();
			exp_date_arr=exp_date.split(",");	
			$.each(exp_date_arr,function(i,e){
				if(e<curr_date && e!=0) 
				{
					//alert('1');
					$('.avl_report_'+pid).addClass('highlight_row');
				}
			});
		});
}

$('.stk').live('click',function(){
	var product_id=$(this).attr('product_id');
	$('.stock_suggestion_'+product_id).html("Loading.....");
	$.post(site_url+'/admin/jx_stock_suggestion/'+product_id,{},function(resp){
		var html='';
		if(resp.status=='error')
		{
			html+='No Stock';
		}else
		{
			$.each(resp.prd_stk_det,function(i,p){
				html+='<div>'+p.rack_name+'-'+p.bin_name+' : <span style="color:red">'+ p.stock+'</span></div>';
			});
		}
		$('.stock_suggestion_'+product_id).html(html);
	},'json');
});

$('.prod_ord_det').live('click',function(e){
	e.preventDefault();
	if($(this).hasClass('plus'))
	{
		$(this).addClass('minus').removeClass('plus').html('&minus;');
		$(this).parent().parent().find('.tbl_subgrid_content').show();
	}else
	{
		$(this).removeClass('minus').addClass('plus').html('&plus;');
		$(this).parent().parent().find('.tbl_subgrid_content').hide();
	}
});

$('.toggle_act').live('click',function(e){
	e.preventDefault();
	var text=$(this).text();
	if(text=='OpenAll')
	{
		$('.toggle_act').text('Collapse');
		$('.prod_ord_det').addClass('minus').removeClass('plus').html('&minus;');
		$('.prod_ord_det').parent().parent().find('.tbl_subgrid_content').show();
	}else if(text=='Collapse')
	{
		$('.toggle_act').text('OpenAll');
		$('.prod_ord_det').removeClass('minus').addClass('plus').html('&plus;');
		$('.prod_ord_det').parent().parent().find('.tbl_subgrid_content').hide();
	}
});
 
$('.toggle_date_exceed').live('click',function(){
	if($('.toggle_date_exceed').hasClass('toggle_date_exceed_show'))
	{
		show_filter_products();
		$('.toggle_date_exceed').addClass('toggle_date_exceed_hide').removeClass('toggle_date_exceed_show').text('Hide Ship date exceed products');
	}
	else
	{
		$('.avail_report').removeClass('highlight_row');
		$('.toggle_date_exceed').addClass('toggle_date_exceed_show').removeClass('toggle_date_exceed_hide').text('Show Ship date exceed products');
	}
});

$('.toggle_ship').live('click',function(e){
	
	var text=$(this).text();
	if(text=='OpenAll')
	{
		$('.toggle_act').text('Collapse');
		$('.prod_ord_det').addClass('minus').removeClass('plus').html('&minus;');
		$('.prod_ord_det').parent().parent().find('.tbl_subgrid_content').show();
	}else if(text=='Collapse')
	{
		$('.toggle_act').text('OpenAll');
		$('.prod_ord_det').removeClass('minus').addClass('plus').html('&plus;');
		$('.prod_ord_det').parent().parent().find('.tbl_subgrid_content').hide();
	}
});
 
$('#gen_pnh_unavail_report_frm').submit(function(){
	reset_form_action()
	return false;
});
$('#pnh_unavail_prod_list .pagination a').live('click',function(e){
	e.preventDefault();
	$('#gen_pnh_unavail_report_frm').attr('action',$(this).attr('href'));
	hndl_pnh_unvail_prod_report_frm();
});

$('.row_click').live('click',function(){
	//var sel_pid = $(this).attr('pid');
		//$('#prod_ord_det_'+sel_pid).trigger('click'); 
});

//Function to change deal status
function change_deal_status(product_id)
{
	if($('.change_deal_status:checked').length)
	{
		$('#deal_det_dlg').data('product_id',product_id).dialog('open');
	}
}

//Function to update Expected date of shipment
$('.update_shipdate_status').live('click',function(){
	
	if($('.update_shipdate_status:checked').length)
	{
		var product_id=$(this).attr('product_id');
		var transid=$(this).attr('transid');
		var orderid=$(this).attr('orderid');
		
		$('#exp_shipdate_dlg').data("pro_data",{'product_id': product_id,'transid': transid,'order_id':orderid}).dialog('open');
		$('.exp_date').addClass('exp_date_'+orderid);
		$('.exp_date_'+orderid).datepicker();//{minDate: new Date()}
		//$('.exp_date_'+orderid).removeClass('hasdatepicker');
		$('.ui-datepicker').hide();
	}
	
});



//Dialog to change deals status for a product
$('#deal_det_dlg').dialog({
		modal:true,
		autoOpen:false,
		width:500,
		height:450,
		autoResize:true,
		open:function(){
		//$('.ui-dialog-buttonpane').find('button:contains("Update")').addClass('placeorder_btn');
		var k=0;
		var pid=$(this).data('product_id');
		var html = '';
		
		$.post(site_url+'/admin/jx_dealsbyproduct_id/'+pid,{},function(resp){
			
			$.each(resp.deal_stk_det,function(i,d){
				html+= '<div>'+(++i)+' . <a href="'+site_url+'/admin/pnh_deal/'+d.id+'" class="prd_deal_'+d.id+'">'+d.name+'</a>';
				html+= '<input type="checkbox" class="deals_sel" product_id="'+pid+'" dealid="'+d.dealid+'"  itemid="'+d.id+'"></div>';
			});
			
			$('#deal_det_dlg .deal_det_blk').html(html);
			
		},'json');
	},
	buttons:{
		
		'Publish':function(){
			var d_arr=[];
			$('.deals_sel:checked').each(function(){
				var dealids=$(this).attr('dealid');
				d_arr.push(dealids);
			});
			d_arr=d_arr.join(",");
			act=1;//Publish
			$.post(site_url+'/admin/jx_change_deal_status',{dealids:d_arr,status:act},function(resp){
				if(resp.status=='success')
				{
					alert(resp.message);
					$('#deal_det_dlg').dialog('close');
				}
				else
				{
					alert('Error...Deal Status not Updated');
				}
			},'json');
		},
		'Un-Publish':function(){
			 var d_arr=[];
			$('.deals_sel:checked').each(function(){
				var dealids=$(this).attr('dealid');
				d_arr.push(dealids);
			});
			d_arr=d_arr.join(",");
			act=0;//unpublish
			$.post(site_url+'/admin/jx_change_deal_status',{dealids:d_arr,status:act},function(resp){
				if(resp.status=='success')
				{
					alert(resp.message);
					$('#deal_det_dlg').dialog('close');
				}
				else
				{
					alert('Error...Deal Status not Updated');
				}
			},'json');
		},
		'Cancel':function(){
			$('#deal_det_dlg').dialog('close');
		}
	}
});  

//Dialog to change deals status for a product
$('#exp_shipdate_dlg').dialog({
		modal:true,
		autoOpen:false,
		width:500,
		height:150,
		autoResize:true,
		open:function(){
		//$('.ui-dialog-buttonpane').find('button:contains("Update")').addClass('placeorder_btn');
		var html = '';
		
		var dlgData = $('#exp_shipdate_dlg').data("pro_data");
		prodid = dlgData.product_id;
		transid = dlgData.transid;
		orderid = dlgData.order_id;
		
		html+= '<div><b>Expected Date of Shipment: </b><input name="exp_date" class="exp_date"></div>';
		$('#exp_shipdate_dlg .ship_blk').html(html);
	},
	buttons:{
		
		'Proceed':function(){
			var dlgData = $('#exp_shipdate_dlg').data("pro_data");
			prodid = dlgData.product_id;
			transid = dlgData.transid;
			orderid = dlgData.order_id;
			date =$('.exp_date').val(); 
			
			$.post(site_url+'/admin/jx_update_shipment_date',{pid:prodid,transid:transid,oid:orderid,date:date},function(resp){
				if(resp.status=='success')
				{
					$('#exp_shipdate_dlg').dialog('close');
					$('.exp_'+orderid).html(resp.date);
					$('#exp_shipdate_dlg .ship_blk').html();
				}
				else
				{
					alert('whoops!Sorry...Shipment Date not Updated');
				}
			},'json');
		},
		'Cancel':function(){
			var dlgData = $('#exp_shipdate_dlg').data("pro_data");
			prodid = dlgData.product_id;
			transid = dlgData.transid;
			orderid = dlgData.order_id;
			date =$('.exp_date').val();
			$('#exp_shipdate_dlg .ship_blk').html();
			$('#exp_shipdate_dlg').dialog('close');
		}
	}
});  

$('.deals_sel').live('click',function(){
	var itemid=$(this).attr('itemid');
	if($(this).attr('checked'))
	{
		$('.prd_deal_'+itemid).addClass('selected');
	}else
	{
		$('.prd_deal_'+itemid).removeClass('selected');
	}
	
});


$(function(){
	$('select[name="date_type"]').chosen();
	$('select[name="tids[]"]').chosen();
	$('select[name="mids[]"]').chosen();
	$('select[name="menu_type"]').chosen();
	$('select[name="publish_type"]').chosen();
	$('select[name="sourceable"]').chosen();
	
	$('select[name="date_type"]').change(function(){
		if($(this).val() == 1)
		{
			$('#ordersby_daterange').show();
		}else
		{
			$('#ordersby_daterange').hide();
		}
		clear_terrlist = 1;
		get_pnh_unavailordersumm();
		
	});
	
	$('#from_date,#to_date').change(function(){
		clear_terrlist = 1;
		get_pnh_unavailordersumm();
	});
	clear_terrlist = 1;
	get_pnh_unavailordersumm();
	
	
	$('select[name="tids[]"]').change(function(){
		clear_terrlist = 0;
		get_pnh_unavailordersumm();
	});
	
	$('select[name="mids[]"]').change(function(){
		reset_form_action();
	});
	
	$('select[name="menu_type"]').change(function(){
		reset_form_action();
	});
	
	$('select[name="publish_type"]').change(function(){
		reset_form_action();
	});		
	
	$('select[name="ship_date_filter"]').change(function(){
		reset_form_action();
	});
	
	prepare_daterange('from_date','to_date');
	$('#gen_stat_frm').submit(function(){
		
		if(!$('select[name="tids[]"] option:selected').length){
			alert("Choose atleast one franchise from the list"); 		
			return false;		
		}

		if(!$('#from_date').val() || !$('#to_date').val()){
			alert("Please enter correct date range"); 		
			return false;		
		}		
		
		if(!confirm("Are you sure want to generate statement ")){
			return false;	
		}
	});
	
});

//Function call to display open PO details for a product
function load_openpolist(pid)
{
	$('#dlg_openpolist').data('pid',pid).dialog('open');
}

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
			html += '			<th><input type="checkbox" class="all_po_chk"></th><th>Slno</th><th>Vendor</th><th>PO Date</th><th>POID</th><th>Total Qty</th><th>Action</th>';
			html += '		</thead>';
			html += '		<tbody>';
			$.each(resp.vendor_po_list,function(a,b){
				html += '		<tr poid="'+b.po_id+'" productid="'+pid+'"><td><input type="checkbox" class="sl_sel_po" value="'+pid+'" po_id="'+b.po_id+'"></td><td>'+(a*1+1)+'</td><td><a target="_blank" href="'+site_url+'/admin/vendor/'+b.vendor_id+'">'+b.vendor_name+'</a></td><td>'+get_unixtimetodate(b.po_date)+'</td><td><a target="_blank" href="'+site_url+'/admin/viewpo/'+b.po_id+'">'+b.po_id+'</a></td><td>'+b.qty+'</td><td><a class="inline_trig" style="color:blue;font-weight:bold" target="_blank" href="'+(site_url+'/admin/viewpo/'+b.po_id)+'" >View</a></td></tr>';	
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
});

//Checkbox action for place po option
$('.sel_prd').live('click',function(){
	var trEle=$(this).parents('tr:first');
	var chk=$(this).attr('checked');
	if(chk == 'checked')
		trEle.addClass('row_select');
	else
		trEle.removeClass('row_select');	
});

$('select[name="sourceable"]').live('change',function(){
	var source_filt_val=$(this).val();
	var i=0;
	$('.avail_report').each(function(){
		var sourceable=$(this).attr('sourceable');
		if(source_filt_val=='all')
		{
			i++;
			$(this).show();
		}else
		{
			if(source_filt_val==sourceable)	
			{
				i++;
				$(this).show();
			}else 	
			{
				$(this).hide();
			}
		}
	});
	$('.order_summ b').html(i);
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
		pid_html='';
		po_product_arr=[];	
		pid_arr=[];
	
		$('.sel_prd').each(function(){
			var trEle=$(this).parents('tr:first');
			var chk=$(this).attr('checked');
			if(chk == 'checked')
			{
				var pid=$(this).attr('pid');
				var pname=$(this).attr('pname');
				
				if($.inArray([pid,pname], po_product_arr) === -1) 
				 	po_product_arr.push([pid,pname]);
			}
		});
		
		$.each(po_product_arr,function(i,p){
			k++;
			selected_product_ids.push(p[0]);
			pid_html+='<div ><span style="margin-right:7px;">'+k+'</span><a target="_blank" href="'+site_url+'/admin/product/'+p[0]+'">'+p[1]+'</a></div>';
			
		});
		$('.place_po_blk').html(pid_html);
		$('.prd_arr').val(selected_product_ids);
		$('#prd_ttl').html(k);
	},
	buttons:{
		'Cancel':function(){
			$('#place_po_dlg').dialog('close');
		},
		'Proceed':function(){
			 $('#place_po_form').submit();
		}
	}
});      
</script>