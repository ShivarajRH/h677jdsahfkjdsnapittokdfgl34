<div class="container">

<div id="barcode_floater" style="display:none;margin:5px 0px;position:fixed;right:0px;bottom:40px;background:#F7EFB9;padding:15px;border:1px solid #aaa;">Highlight product of barcode : <input type="text" id="srch_barcode"></div> 

<div id="loading_bar">
Loading...
</div>

<div class="v_disp_container">
	<div class="v_disp">
	</div>
	<input type="button" value="Choose vendor" id="choosevendor">
</div>

<h2>Purchase Order</h2>
<form method="post" id="purchaseordefrm" autocomplete="off">
<input type="hidden" id="vendoridhidden" name="vendor">
Select Vendor : <select id="vendorsel">
<option value="0">select</option>
<?php foreach($vendors as $v){?>
<option value="<?=$v['vendor_id']?>"><?=$v['vendor_name']?></option>
<?php }?>
</select>

<div class="clear">	</div>

<div class="clear"></div>

<div class="showaftvendor">
<h4>Purchase Products</h4>
<table border=1 width="100%" cellpadding=5 id="pprods" class="datagrid">
<thead>
<tr>

<th>Sno</th>
<th>Product</th>
<th>Qty</th>
<th>Last 90 days order</th>
<th>Open PO Qty</th>
<th></th>
<th>MRP</th>
<th>DP Price</th>
<th>margin</th>
<th>Scheme discount</th>
<th>Discount Type</th>
<th>Purchase Unit price</th>
<th>Total Purchase price</th>
<th>FOC</th>
<th>Has Offer</th>
<th colspan=2>Note</th>
</tr>
</thead>
<tbody>
</tbody>
</table>


<div style="padding:5px;">

<div style="float:right">
<span id="load_brands"></span>
<input type="button" value="Show & load" id="sl_show">
<input type="button" value="Load Unavailable Products" id="load_unavail">
</div>
Search &amp; Add : <input type="text" class="inp" id="po_search" style="width:400px;">
<div id="po_prod_list" class="closeonclick">
</div>
</div>


</div>

<div>
<h3>Total PO Value : Rs <span id="total_po_value">0</span></h3>
</div>


<h4>Purchase Order Details</h4>
<table>
<tr>
<td>Expected Date of Delivery</td>
<td>
	<input type="text"  name="e_dod" class="datetimepick" value="" >
</td>
</tr>
<tr>
<td>Remarks</td>
<td><textarea name="remarks"></textarea></td>
</tr>
</table>


<div style="margin-top:10px;">
<input type="button" onclick="submit_frm=1;$('#purchaseordefrm').trigger('submit');" value="Create purchase order" style="font-size:140%">
</div>

</form>
</div>

 
<div style="display:none">
<table id="sl_prod_template">
<tbody>
<tr class="brand_%brandid%" brandid="%brandid%"><td><input type="checkbox" class="sl_sel_prod" value="%pid%"><input type="hidden" class="pid" value="%pid%"></td><td class="psrcstat"><span class="src_stat">%prod_source_stat%</span> <a href=javascript:void(0)" prod_id="%pid%" onclick="upd_prdsourceablestat(this)" nsrc="%prod_source_stat_val%" >Change</a> </td><td>%pid%</td><td class="name" style="width: 400px;text-align: left;"><a  target="_blank" href="<?php echo site_url('admin/product/%pid%')?>">%product%</a></td><td>Rs <span class="mrp">%mrp%</span></td><td class="margin" style="display: none;" >%margin%</td><td>%stock%</td><td ><input type="text" class="i_po_qty" style="width: 40px;" value="%i_po_qty_val%"></td><td class="orders">%orders%</td></tr>
</tbody>
</table>
<table id="p_clone_template">
<tr class="barcode--barcode-- barcodereset rec_type brand_%brandid%" brandid="%brandid%">
<td>%sno%</td>

<td>
	<a target="_blank" href="<?php echo site_url('admin/product/');?>/product_id">product_name</a></td>
<td><input type="text" class="qty inp" size="2" name="qty[]" value="%qty%"></td>
<td>%orders%</td>
<input type="hidden" class="product" name="product[]" value="%product_id%">
<td style="width:50px;text-align: center" id="is_po_raised_%product_id%"></td>
<td></td>
<td><input type="text" class="mrp inp" size="8" name="mrp[]" value="mrpvalue"></td>
<td>
	<div style="visibility: %dp_price_inp% "><input type="text" class="dp_price inp" size="8" name="dp_price[]" value="%dp_price%"></div>
</td>
<td><input type="text" class="margin inp" size="6" name="margin[]" value="%margin%"></td>
<td><input type="text" class="sdiscount inp" size="6" name="sch_discount[]" value="0"></td>
<td><select class="stype" name="sch_type[]">
<option value="1">percent</option>
<option value="2">value</option>
</select></td>
<td><input type="text" class="inp pprice" size=4 name="price[]" value=""><span class="marg_prc_preview" style="margin-right:3px;width:40px;display: none"><input type="text" name="marg_prc" value="" style="width:27px;font-size:10px" readonly="" class="marg_prc">%</span></td>
<td><input type="text" class="inp tpprice readonly" size=4 readonly="readonly" name="tprice[]" value=""></td>
<td><input type="checkbox" class="inp" name="%foc%" value="1"></td>
<td><input type="checkbox" class="inp" name="%offer%" value="1"></td>
<td><input type="text" class="inp" name="note[]" value=""></td>
<td><a href="javascript:void(0)" onclick='remove_prod_selection(this)'>remove</a></td>
</tr>
</table>


</div>


<div id="sl_products" title="Choose and add to current order">
	<span style="float: right">
			<b>Show</b> :
			<select name="stk_prod_disp">
				<option value="1">All Products</option>
				<option value="2">Orders With no Stock</option>
			</select> 
	</span>
	<br><br>
	<div class="datagrid_cont">
		<table class="datagrid datagridsort" width="100%">
			<thead>
				<tr><th><input type="checkbox" class="chk_all"></th><th>Source</th><th>Product ID</th><th>Product</th><th>Mrp</th><th style="display: none;">Margin</th><th>Stock</th><th>PO Qty</th><th>Orders[90 Days]</th></tr>
			</thead>
			<tbody></tbody>
		</table>
		<div>
		<br><br>
</div>
	</div>
	
 <form id="src_form" action="<?=site_url("admin/jx_mark_src_products")?>" method="post">
<input type="hidden" name="pids" class="pids">
<input type="hidden" name="action" class="action" value="1">
</form>

<form id="unsrc_form" action="<?=site_url("admin/jx_mark_unsrc_products")?>" method="post">
<input type="hidden" name="pids" class="pids">
<input type="hidden" name="action" class="action" value="1">
</form>
</div>

<style>
.src{
	background:#afa;
}
.nsrc{
	background:#faa;
}
#sl_products td {text-align: center;}

.psrcstat a{font-size: 10px;color: blue;}
</style>

<div id="addvenbrandfrm_dlg" title="Add Brand">
	 
		<table width="100%">
			<tr>
				<td><b>Brand</b></td>
				<td><select style="width: 200px;" name="newbrand" data-placeholder="Choose"></select></td>
			</tr>
			<tr>
				<td><b>Margin</b></td>
				<td><input type="text" size="10" value="0" name="newbrandmarg"></td>
			</tr>
		</table>
	 
</div>


<div style="display:none">
	<div id="dlg_openpolist" title="Open PO List"></div>
</div>

<script>

$('input[name="price[]"]').live('change',function(){
	var trEle = $(this).parents('tr:first');
	
	var mrp  = $('input[name="mrp[]"]',trEle).val();
	var dp = $('input[name="dp_price[]"]',trEle).val();
	var marg = $('input[name="margin[]"]',trEle).val();
	var stype = $('select[name="sch_type[]"]',trEle).val()*1;
	
	var ppval = $(this).val()*1; 
		if(isNaN(ppval))
			ppval = 0;
			
		if(stype == "1")
		{
			smarg = dp?(ppval/dp):(ppval/mrp);
			smarg = 100-(smarg*100);
			
		}else
		{
			smarg = dp?(dp-ppval):(mrp-ppval);
		}
		
		smarg = (smarg < 0)?0:smarg;
		
		$('input[name="margin[]"]',trEle).val(smarg);
		
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
$('#dlg_openpolist').dialog({'width':500,autoOpen:false,'height':'auto',modal:true,open:function(){
	var pid = $(this).data('pid');
	$('#dlg_openpolist').html("<h3 align='center'>Loading...</h3>");
	$.post(site_url+'/admin/jx_getopenpolistbypid/'+pid,{},function(resp){
		var html = '<h3 class="pname">'+resp.product_name+'</h3>';
			html += '	<div class="pttl">Total open qty : <b>'+resp.ttl_open_qty+'</b></div>';
			html += '	<div id="openpolist_tbl">';
			html += '	<table width="100%" class="datagrid" cellpadding="5" cellspacing="0">';
			html += '		<thead>';
			html += '			<th>Slno</th><th>Vendor</th><th>PO Date</th><th>POID</th><th>Total Qty</th><th>Action</th>';
			html += '		</thead>';
			html += '		<tbody>';
			$.each(resp.vendor_po_list,function(a,b){
				html += '		<tr><td>'+(a*1+1)+'</td><td><a target="_blank" href="'+site_url+'/admin/vendor/'+b.vendor_id+'">'+b.vendor_name+'</a></td><td>'+get_unixtimetodate(b.po_date)+'</td><td>'+b.po_id+'</td><td>'+b.qty+'</td><td><a class="inline_trig" style="color:blue;font-weight:bold" target="_blank" href="'+(site_url+'/admin/viewpo/'+b.po_id)+'" >View</a></td></tr>';	
			});
			html += '		</tbody>';
			html += '	</table>';
			html += '	</div>';
			$('#dlg_openpolist').html(html);
	},'json');
}}).load(function() {
                $(this).dialog("option", "position", ['center', 'center'] );
            });
	
function load_openpolist(pid)
{
	$('#dlg_openpolist').data('pid',pid).dialog('open');
}

$(".chk_all").click(function(){
	if($(this).attr("checked"))
		$(".sl_sel_prod").attr("checked",true);
	else
		$(".sl_sel_prod").attr("checked",false);
});



//$('.datetimepick').datepicker({minDate:new Date()});

$('.datetimepick').datetimepicker({
	timeFormat: "hh:mm tt"
});


$('#addvenbrandfrm_dlg select[name="newbrand"]').chosen();

$('#addvenbrandfrm_dlg').dialog({
									autoOpen:false,
									width:400,
									height:500,
									autoResize:true,
									modal:true,
									open:function(){
										var dlgEle = $(this);
											$('#addvenbrandfrm_dlg select[name="newbrand"]').html("").trigger("liszt:updated");
											$.post(site_url+'/admin/jx_ven_unavail_brands','ven_id='+$('#vendorsel').val(),function(resp){
												if(resp.status == 'error')
												{
													alert("No brands found");
													dlgEle.dialog('close');	
												}else
												{
													var selOpts = '<option value="">Choose</option>';
														$.each(resp.brandlist,function(a,b){
															selOpts += '<option value="'+b.brandid+'">'+b.brandname+'</option>';
														});
														$('#addvenbrandfrm_dlg select[name="newbrand"]').html(selOpts).trigger("liszt:updated");
												}
											},'json');
									},
									buttons:{
										'Submit': function(){
											var newbrandname = $('#addvenbrandfrm_dlg select[name="newbrand"] option:selected').text();
											var newbrandid = $('#addvenbrandfrm_dlg select[name="newbrand"]').val();
											var newbrandmarg = $('#addvenbrandfrm_dlg input[name="newbrandmarg"]').val();
												if(isNaN(newbrandmarg*1))
												{
													alert("Invalid Margin Entered");
												}else
												{
													$.post(site_url+'/admin/jx_upd_venbrandlink','bid='+newbrandid+'&bmarg='+(newbrandmarg*1)+'&ven_id='+$('#vendorsel').val(),function(resp){
														if(resp.status == 'error')
														{
															alert(resp.error);
														}else
														{
															alert("Brand Linked");
															if($('#sl_sel_brand').length)
															{
																var selOpts = '';
																	$.each(resp.linked_brands,function(a,b){
																		selOpts += '<option value="'+b.brandid+'">'+b.brandname+'</option>';
																	});
																$('#sl_sel_brand').html(selOpts);
															}
															$('#addvenbrandfrm_dlg').dialog('close');
															$('.overview_ven_brandlist').append(newbrandname+', ');
														}
													},'json');
												}
										},
										'Cancel':function(){
										
										}
									}
								});							
function show_addvenbranddlg()
{
	$('#addvenbrandfrm_dlg').dialog('open');
}

var submit_frm = 0; 
$("#load_unavail").click(function(){
	$(this).attr("disabled",true);
	$.post("<?=site_url("admin/jx_load_unavail_products")?>",{hash:<?=time()?>,vid:$("#vendoridhidden").val()},function(data){
		os=$.parseJSON(data); 
		$.each(os,function(i,o){
			var po_qty = 0;
			if((o.available-o.qty) < 0)
				po_qty = (o.qty-o.available);
			else
				po_qty = 0;
				
			addproduct(o.product_id, o.product_name, o.mrp,o.brand_margin,0,0);
		});
 	});
}).attr("disabled",false);

$('select[name="stk_prod_disp"]').change(function(){
	var type = $(this).val();
	if(type == 1)
		$('#sl_products .datagrid tbody tr').show();
	else 
		$('#sl_products .datagrid tbody tr').hide();
	
		if(type == 2)
			$('#sl_products .datagrid tbody tr.ORDERNOSTOCK').show();
			else if(type == 3)
				$('#sl_products .datagrid tbody tr.ORDERSTOCK').show();
				else if(type == 4)
					$('#sl_products .datagrid tbody tr.STOCKNOORDER').show();
					else if(type == 5)
						$('#sl_products .datagrid tbody tr.NOSTOCKNOORDER').show();
});

var added_po=[];
function remove_prod_selection(ele)
{
		var trEle = $(ele).parents('tr:first');
		var rmv_prdid = $('input[name="product[]"]',trEle).val();
			trEle.remove();
			
		var po_prod_sel = [];
			for(var i in added_po)
			{
				if(rmv_prdid != added_po[i])
					po_prod_sel.push(added_po[i]);
			}
			added_po = po_prod_sel;
			
			$('#pprods tbody tr').each(function(i,ele){
				$('td:first',this).text(i*1+1);
			});
			
}
function addproduct(id,name,mrp,margin,orders,qty)
{
	if($.inArray(id,added_po)!=-1)
	{
		alert("Product already added to the current Order");
		return;
	}
	$.post("<?=site_url("admin/jx_productdetails")?>",{id:id},function(data){
		o=$.parseJSON(data);
		i=added_po.length;
		$("#po_prod_list").hide();
		template=$("#p_clone_template tbody").html();
		template=template.replace("product_name",name);
		template=template.replace("mrpvalue",mrp);
		template=template.replace(/%sno%/g,i+1);
		template=template.replace(/%product_id%/g,o.product_id);
		margin = 0;
		template=template.replace(/%margin%/g,"");
		template=template.replace(/%foc%/g,"foc"+i);
		template=template.replace(/%dp_price%/g,o.dp_price);
		
		if(o.is_serial_required*1)
			template=template.replace(/%dp_price_inp%/g,'visible');
		else
			template=template.replace(/%dp_price_inp%/g,'hidden');
		
		template=template.replace(/%qty%/g,qty?qty:o.pen_ord_qty);
		template=template.replace(/%orders%/g,o.orders);
		template=template.replace(/%offer%/g,"offer"+i);
		template=template.replace(/--barcode--/g,o.barcode);
		template=template.replace(/%brandid%/g,o.brand_id);
		
		$("#pprods tbody").append(template);
		var ttl_openpo = 0;
		if(o.is_po_raised != null)
		{
			is_po_raised_html = "";
			$.each(o.is_po_raised,function(i,p){
				if(p.po_order_qty<0)
					p.po_order_qty=0;
					//is_po_raised_html+="<div><ul><a href='"+site_url+'/admin/viewpo/'+p.po_id+"' target='_blank'>"+p.po_id+"</a>"+'-'+'<input type="text" readonly="readonly" style="background-color: #F1F1F1" size="2" value='+p.po_order_qty+' ></ul></div>';
					ttl_openpo += p.po_order_qty*1;
				});
				
		}
		if(ttl_openpo*1 > 0)
			$("#is_po_raised_"+o.product_id).html('<a href="javascript:void(0)" onclick="load_openpolist('+o.product_id+')" ><b>'+ttl_openpo+'</b></a>');
		else
			$("#is_po_raised_"+o.product_id).html('<b>0</b>');
			
		added_po.push(id);

		$("#pprods tbody tr .qty").trigger('change');
		
	});
}


function calc_total_pov()
{
	total=0;
	$("#pprods .sdiscount").each(function(i,o){
		$p=$(o).parents("tr").get(0);
		qty=parseFloat($(".qty",$p).val());
		mrp=parseFloat($(".mrp",$p).val());
		dp_price=parseFloat($(".dp_price",$p).val());
		stype=parseFloat($(".stype",$p).val());
		sdiscount=parseFloat($(".sdiscount",$p).val());
		if(isNaN(sdiscount))
		{
			sdiscount=0;
			$(".sdiscount",$p).val("0");
		}
		margin=parseFloat($(".margin",$p).val());
		
	/*	if(dp_price*1 > 0)
		{
			price=dp_price-(dp_price*margin/100);
			if(stype==1)
				price=price-(dp_price*sdiscount/100);
			else
				price=price-sdiscount;
		}else
		{
			price=mrp-(mrp*margin/100);
			if(stype==1)
				price=price-(mrp*sdiscount/100);
			else
				price=price-sdiscount;
		}*/
		if(dp_price*1 > 0)
		{
			price=dp_price-(dp_price*margin/100);
			if(stype==1)
				price=price-(dp_price*sdiscount/100);
			else
			{
				
				price=dp_price-margin;
				margin_prc=(1-(price/dp_price))*100;
				
			}
				
		}else
		{
			price=mrp-(mrp*margin/100);
			if(stype==1)
				price=price-(mrp*sdiscount/100);
			else
				price=mrp-margin;
				
		}
		
		total+=(price*qty);
	});
	$("#total_po_value").html(Math.round(total,2));
}

$(function(){

	$("#srch_barcode").keyup(function(e){
		if(e.which==13)
		{
			$(".barcodereset").removeClass("highlightprow");
			if($(".barcode"+$(this).val()).length==0)
			{
				alert("Product not found on rising PO");
				return;
			}
			$(".barcode"+$(this).val()).addClass("highlightprow");
			$(document).scrollTop($(".barcode"+$(this).val()).offset().top);
		}
	});
	
	// update bulk product margin by brand  
	$('#pprods .margin').live('keyup',function(){
		
		if(isNaN($(this).val()*1))
		{
			$(this).val(0);
		}
		else
		{
			var $r = $(this).parents('tr:first');
			var prod_brandid = $r.attr('brandid');
			var mrg = $(this).val();
			$('.qty',$r).trigger('change');
			/*
				if($('.stype',$r).val() == 1)
				{
					$('#pprods .brand_'+prod_brandid+' .stype').each(function(){
						if($(this).val() == 1)
						{
							var ref_ele = $(this).parents('tr:first');
								$('.margin',ref_ele).val(mrg);
								$('.qty',ref_ele).trigger('change');	
						}
					});
				}else
				{
					$('.qty',$r).trigger('change');
				}
				*/
		}	
	});

	$("#pprods .sdiscount,#pprods .margin,#pprods .mrp, #pprods .stype, #pprods .qty,#pprods .dp_price").live("change",function(){
		$p=$(this).parents("tr").get(0);
		qty=parseFloat($(".qty",$p).val());
		mrp=parseFloat($(".mrp",$p).val());
		dp_price=parseFloat($(".dp_price",$p).val());
		stype=parseFloat($(".stype",$p).val());
		sdiscount=parseFloat($(".sdiscount",$p).val());
		if(isNaN(sdiscount))
		{
			sdiscount=0;
			$(".sdiscount",$p).val("0");
		}
		margin=parseFloat($(".margin",$p).val());

		margin = isNaN(margin)?0:margin;
		
		if(dp_price*1 > 0)
		{
			price=dp_price-(dp_price*margin/100);
			if(stype==1)
			{
				$('.marg_prc_preview',$p).hide();
				price=price-(dp_price*sdiscount/100);
			}
			else
			{
				
				price=dp_price-margin;
				margin_prc=(1-(price/dp_price))*100;
				$('.marg_prc_preview',$p).show();
				$('.marg_prc').val(margin_prc);
			}
				
		}else
		{
			price=mrp-(mrp*margin/100);
			if(stype==1)
			{
				$('.marg_prc_preview',$p).hide();
				price=price-(mrp*sdiscount/100);
			}
			else
			{
				price=mrp-margin;
				margin_prc=(1-(price/mrp))*100;
				$('.marg_prc_preview',$p).show();
				$('.marg_prc').val(margin_prc);
			}
		}
		
		$(".pprice",$p).val(price);
		$(".tpprice",$p).val(price*qty);
		calc_total_pov();
	});

	$("#po_search").keyup(function(e){
		e.preventDefault();
		q=$(this).val();
		if(q.length<3)
			return true;
		if(jHR!=0)
			jHR.abort();
		window.clearTimeout(search_timer);
		search_timer=window.setTimeout(function(){
		jHR=$.post("<?=site_url("admin/getvendorproducts")?>",{q:q,v:$("#vendoridhidden").val()},function(data){
			$("#po_prod_list").html(data).show();
		});
		},200);
	}).focus(function(){
		if($("#po_prod_list a").length==0)
			return;
		$("#po_prod_list").show();
	}).click(function(e){
		e.stopPropagation();
	});

	$("#purchaseordefrm").submit(function(){
		if(submit_frm)
		{
			if($("#pprods tbody tr").length==0)
			{
				alert("No products added for creating PO, Please add atleast one product");
				return false;
			}
			
			var block_frm_submit = 0;
			var qty_pending = 0;
			var ven_pending = 0;
			var marg_pending = 0;
				$('#pprods tbody tr:visible',this).each(function(){
					qty = $('input[name="qty[]"]',this).val()*1;
					marg = $('input[name="margin[]"]',this).val()*1;
					
					if(isNaN(marg) || marg == 0)
						marg_pending += 1;
					
					if(qty==0)
						qty_pending += 1;
					
				});
				
				if(marg_pending)
				{
					alert("Invalid Margins entered,Please check and update margins");
					return false;
				}
				
				if(qty_pending || ven_pending){
					alert("Unable to submit request, please enter valid qty for purchase");
					return false;
				}
				if(confirm("Are you sure want to create this PO ?"))
					return true;
				else
					return false;
		}
		return false;
	});
	
	$("#vendorsel").change(function(){
		showvendor($(this).val());
	}).val("0").attr("disabled",false);
	$("#choosevendor").click(function(){
		$(".showaftvendor,#barcode_floater").show();
		$("#vendorsel,#choosevendor").attr("disabled",true);
		$("#vendoridhidden").val($("#vendorsel").val());
		$.post("<?=site_url("admin/jx_getbrandsforvendor_json")?>",{vid:$("#vendorsel").val()},function(json){
			data=$.parseJSON(json);
			str='<select id="sl_sel_brand">';
			$.each(data,function(i,v){
				str=str+'<option value="'+v.id+'">'+v.name+'</option>';
			});
			str=str+'</select>';
			$("#load_brands").html(str);
		});
	}).attr("disabled",false);
	$("#sl_show").click(function(){
		$("#sl_products").dialog('open');
	});
});
var brand_prods=[];
var search_timer=0,jHR=0;
function showvendor(vid)
{
	$(".v_disp_container").show();
	$(".v_disp").html("LOADING>>>>");
	$.post("<?=site_url("admin/jx_show_vendor_details")?>",{v:vid},function(data){
		$(".v_disp").html(data);
	});
}

function loadbrandproducts()
{
	$(".sl_sel_prod:checked").each(function(){
		tr=$($(this).parents("tr").get(0));
		addproduct($(".pid",tr).val(),$(".name",tr).html(),$(".mrp",tr).html(),$(".margin",tr).html(),$(".orders",tr).html(),$(".i_po_qty",tr).val());
	});
	$("#sl_products .datagrid tbody").html("");
	$("#sl_products").dialog('close');
}

$("#sl_products").dialog({
							width:973,
							resizable:true,
							modal:true,
							height:550,
							autoOpen:false,
							open: function(event, ui) {
						        $(event.target).dialog('widget')
					            .css({ position: 'fixed' })
					            .position({ my: 'center', at: 'center', of: window });
					            
										$("#sl_products .datagrid tbody").html("<tr><td colspan='8' align='left'>Loading .....</td></tr>");
										bid=$("#load_brands select").val();
										vid=$("#vendoridhidden").val();
										$("#loading_bar").show();
										$.post(site_url+'/admin/jx_getproductsforbrand',{bid:bid,vid:vid},function(json){
											$("#loading_bar").hide();
											data=$.parseJSON(json);
											brand_prods=data;
											$("#sl_products .datagrid tbody").html("");
											if(data.length)
											{
											$.each(data,function(i,p){
													template=$("#sl_prod_template tbody").html();
													template=template.replace(/%id%/g,i);
													template=template.replace(/%pid%/g,p.id);
													template=template.replace(/%product%/g,p.product);
													template=template.replace(/%stock%/g,p.stock);
													template=template.replace(/%margin%/g,p.margin);
													template=template.replace(/%mrp%/g,p.mrp);
													template=template.replace(/%orders%/g,p.orders);
													template=template.replace(/%brandid%/g,bid);
													template=template.replace(/%prod_source_stat%/g,((p.src==1)?'Yes':'No'));
													template=template.replace(/%prod_source_stat_val%/g,p.src);
													var po_ord_qty = 0;
													if(p.pen_ord_qty > p.stock)
														po_ord_qty = (p.pen_ord_qty-(p.stock+p.order_qty));
														//po_ord_qty = p.pen_ord_qty-p.stock;
													else if(p.pen_ord_qty)
														po_ord_qty = 0; 
														
													template=template.replace(/%i_po_qty_val%/g,po_ord_qty<0?0:po_ord_qty);
													$("#sl_products .datagrid tbody").append(template);
													if(p.src==1)
														$("#sl_products .datagrid tbody tr:last").addClass("src");
													else
														$("#sl_products .datagrid tbody tr:last").addClass("nsrc");
													
														
													
													rec_type = '';
										
													if(p.stock && p.orders)
														rec_type += ' ORDERSTOCK ';
													else if(!p.stock && p.orders)
															rec_type += ' ORDERNOSTOCK ';
														else if(p.stock && !p.orders)
															rec_type += ' STOCKNOORDER ';
															else if(!p.stock && !p.orders)
																rec_type += ' NOSTOCKNOORDER ';
										
													$("#sl_products .datagrid tbody tr:last").addClass(rec_type);
													
												});
											$("#sl_products .datagrid").trigger("update");
											$("table").trigger("sorton",[[[1,0]]]); 
											}
											else
											{
												$("#sl_products .datagrid tbody").html("<tr><td colspan='8' align='left'>No products found</td></tr>");
											}
											
										});
							},
							buttons:{
								'Load Selected Products' : function(){
																loadbrandproducts();	
														},

								'Mark as Sourcable' :function()
								{
								
										var dlg = $(this);
										var pids_arr=[];
										$(".sl_sel_prod:checked").each(function(){
											if($(".sl_sel_prod:checked").length)
											{
												pids_arr.push($(this).val());
											}
										});
										pids=pids_arr.join(",");
										$('#src_form input[name="pids"]').val(pids);
										var frm_checksubmit = $("#src_form",this);
										 if($(".sl_sel_prod:checked").length!=0){
											 $.post(site_url+'/admin/jx_mark_src_products',{pids:pids},function(resp){
										            $("#sl_products").dialog('close');
							                      	$("#sl_products").dialog('open');
							                });
							            }
							          
							   
								},

								'Mark as not Sourcable':function()
								{ 
									var dlg = $(this);
									var pids=[];
									$(".sl_sel_prod:checked").each(function(){
										pids.push($(this).val());
										
									});
									pids=pids.join(",");
									$('#unsrc_form input[name="pids"]').val(pids);
									var frm_checksubmit = $("#unsrc_form",this);
									 if($(".sl_sel_prod:checked").length!=0){
										 $.post(site_url+'/admin/jx_mark_unsrc_products',{pids:pids},function(resp){

									            $("#sl_products").dialog('close');
						                      	$("#sl_products").dialog('open');
										 
											 
								                   
						                },'json');
						            }
								},
								'Close':function(){
										$("#sl_products").dialog('close');
								}
							}
						});
$("#sl_products .datagrid").tablesorter({sortList: [[1,0]]});


function upd_prdsourceablestat(ele)
{
	nsrc = $(ele).attr('nsrc');

	prod_id = $(ele).attr('prod_id');
	
	if(confirm("Are you sure want to mark this product "+((nsrc==1)?'Not':'')+' Sourceable ?'))
	{

		$.post(site_url+'/admin/jx_upd_prodsrcstatus',{pid:prod_id,stat:nsrc},function(resp){
			if(resp.status)
			{
				var src_disp_ele = $(ele).parent().find('.src_stat');
				
					if(nsrc==1)
					{
						$(ele).parent().parent().removeClass('src').addClass('nsrc');
						src_disp_ele.text("No");	
						$(ele).attr('nsrc',0);
					}else
					{
						$(ele).parent().parent().removeClass('nsrc').addClass('src');
						src_disp_ele.text("Yes"); 
						$(ele).attr('nsrc',1);	
					}	
			}
		},'json');
			
	}
}
 

</script>
<style>
.v_disp_container{
margin-top:10px;
background:#eee;
padding:5px;
display:none;
}
.v_disp{
min-width:400px;
border:1px solid #ffaa00;
background:#F7EFB9;
padding:5px;
}
.showaftvendor
{
display:none;
}
#po_prod_list{
display:none;
position:absolute;
width:500px;
max-height:230px;
overflow:auto;
background:#eee;
border:1px solid #aaa;
}
#po_prod_list a{
display:block;
padding:5px;
}
#po_prod_list a:hover{
background:blue;
color:#fff;
}
/*
#sl_products{
position:absolute;
width:800px;
min-height:300px;
max-height:500px;
overflow:auto;
top:130px;
left:200px;
border:1px solid #aaa;
display:none;
background:#fff;
padding:10px;
}
#sl_products .datagrid_cont{
margin-bottom:10px;
width:780px;
height:420px;
overflow:auto;
}
*/
.highlightprow{
background:#ff9900;
}
#loading_bar{
display:none;
background:#ff9900;
color:#000;
font-size:16px;
padding:5px 10px;
position:fixed;
top:400px;
right:50px;
}
.margin{border:2px solid #000 !important;width: 40px !important}
.sdiscount{width: 40px !important}
.leftcont{display:none;}
</style>
<?php
