<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/stock_intake.css">
<style>

*{outline: medium}
h2
{
	width:80%;float:left;
}

.rightcont .container
{
	 overflow: hidden;
    padding: 5px;
}
.hidden
{
    display: none;
}
.view_imei
{
	 color: #008000;
     margin: 0 2px;
}

#grn input[type="text"]:focus,#grn select:focus,#grn a:focus{
	font-weight:bolder !important;
	font-size: 110% !important; 
	border:1px solid blue !important;
	display: inline-block;
	width: auto;
	height: auto;
}


</style>

<div class="container">
	<h2>
		<span class="page_title"></span>
	</h2>
	<span class="cancel_stock">
		<input type="button" class="button button-caution button-rounded button-tiny cursor form_cancel" value="Cancel Intake" style="padding:0px;">
	</span>
	<span class="show button button-action button-small button-rounded cursor">Proceed</span>
	<div id="cus_jq_alpha_sort_wrap" ></div>
	<div class="selected_po_list"></div>
	<span class="show button button-action button-small button-rounded cursor">Proceed</span>
	<div id="loadafterselect" style="display:none">
		<div class="barcode_fixed_wrap">
			<h4 style="margin: 0px 0px 0px 5px">Scan Barcode</h4>
			<input type="text" placeholder="Barcode" id="srch_barcode">
		</div> 
	
		<form method="post" id="apply_grn_form" enctype="multipart/form-data">
			<div id="grn_pids"></div>
			
			<div id="grn">
				<table class="datagrid nofooter" style="width: 86% !important	">
					<thead>
						<tr>
							<th width="20px">S.no</th>
							<th width="">Product</th>
							<th width="147px">Storage</th>
							<th width="50px">Receiving Qty</th>
							<th width="50px">Invoice Qty</th>
							<th width="30px">Vat(%)</th>
							<th width="160px" style="text-align: center">Price</th>
							<th width="83px">SubTotal</th>
							<th></th>
						</tr>
					</thead>
					<tbody></tbody>
					<tfoot>
						<tr>
							<td colspan="3" align="right" class="recv_qty_wrapper" style="">Total Value of Purchased Items</td>
							<td align="right" ><b id="grn_ttl_rqty">0</b></td>
							<td align="right" colspan="4" ><b id="grn_ttl_value">0</b><div id="grn_ttl"></div></td>
						</tr>
					</tfoot>
				</table>
			</div>

			<div class="clearboth">
				<div class="fl_left" style="width:86%">
				 	<div style="padding:10px 10px 0">
						<h3 style="width: 100%">Invoice Details </h3>
						<table class="datagrid invoice_tab nofooter" style="width: 53%;float:left">
							<thead>
								<tr>
									<th>Sl.No</th>
									<th>Invoice No</th>
									<th>Date</th>
									<th>Invoice Amount(Rs.)</th>
									<th>Scanned Copy</th>
									<th></th>
								</tr>
							</thead>
							
							<tbody>
								<tr>
									<td>1</td>
									<td><input type="text" name="invno[]" class="inp inv_inp_blk"></td>
									<td><input type="text" name="invdate[]" readonly="readonly" class="inp inv_inp_datepick_blk datepick"></td>
									<td><input size=7 type="text" class="inp invamount inv_inp_amount" name="invamount[]"></td>
									<td><input type="file" name="scan_0" class="scan_file"></td>
									<td><span class="addrow_tooltip" title="Add New Invoice Details"><a href="javascript:void(0)" onclick='cloneinvoice()'><div class="button button-tiny_wrap cursor">+</div></a></span></td>
								</tr>
							</tbody>
							
							<tfoot>
								<tr>
									<td align="right" style="" class="recv_qty_wrapper" colspan="3">Total Invoice Value</td>
									<td align="left" colspan="3"><b id="invoice_ttl_value">Rs.</b></td>
								</tr>
							</tfoot>
						</table>
					</div>
					<div class="inv_textarea_wrap">
						<textarea name="remarks" class="remarks"  placeholder="Remarks" cols="57" rows='6'></textarea>
					</div>
				</div>
				
				<div class="fl_left button_blk_wrap" >
					<input type="button" class="button button-caution button-rounded cursor form_cancel" value="Cancel" >
					<input type="submit" class="button button-action button-rounded cursor" value="Submit" id="form_submit">
				</div>
			</div>
		</form>
	</div>
	
	<div id="grn_template" style="display:none">
		<div class="right">
			<table>
				<tbody>
					<tr class="barcode%bcode% barcodereset pending_mode edit_prod_row " bcode="%bcode%" prodid="%prodid%" pid="%pid%" qty="%qty%" dp_price="%dp_price%" brandid="%brandid%" p_price="%price%" has_serialno="%has_serialno%">
						<td>%sno%</td>
						<td>
	                        <input type="hidden" name="imei%pid%[]" class="imeisvvv imei%prodid%" id="list_imei_%prodid%" value="">
	                        <input type="hidden" name="pid%pid%[]" class="prod_addcheck" value="%prodid%">
                            <input type="hidden" name="prodid[]" class="prod_addcheck" value="%prodid%">
                            
							<div style="font-size:80%;margin-bottom: 10px">
								<span class="name">%name%</span>
                            </div>
							<div class="imei_cont"></div>
							
							<div class="bcode_scanned_div" style="clear:both">
								<div style="float: left;">
									Barcode Scanned : <span class="bcode_scanned_status">No</span>
								</div>
								<div class="add_barcode_wrap" style="float: left;margin-left:10px;">
									<input type="hidden" style="" class="scan_pbarcode pbcodecls%prodid%" value="" name="pbarcode%pid%[]" />
									<span class="pb_blk view_barcode%prodid%" prodid="%prodid%"></span>
									<span style="font-size:88%;"><a style="color:#B62D64" href="javascript:void(0)" class="bcode_upd"  bcode="%bcode%" prodid="%prodid%" pid="%pid%" >%update_barcode%</a></span>								
								</div>
							</div>
							
							<div class="imei_scanned_div" style="clear:both">
								<span style="margin-left:23px">IMEI Scanned :</span> 
								<span class="imei_scanned_status">NO</span>
								<a href="javascript:void(0)" class="view_imei" brandid="%brandid%" prodid="%prodid%">View</a>
							</div>
							<div class="row_error_inp"></div>
						</td>
						
						<td class="po_det_wrap_blk">
							<select class="stkloc" name="storage%pid%[]">==rbs==</select>
							<br /><br />
							<b>PO ID : </b><span><a href="<?php echo site_url('admin/viewpo/%pid%') ?>" target="_blank">%pid%</a></span>
							&nbsp;&nbsp;&nbsp;
							<b>Order Qty : </b>
							<span class="poqty">%qty%<input type="hidden" class="popqty" value="%qty%"></span>
						</td>
						<td>
	                        <input type="text" class="inp rqty qtychange" name="rqty%pid%[]" size=3 value="0" brandid="%brandid%" prodid="%prodid%" pid="%pid%">
	                       	<input type="hidden" value="%prodid%" name="prodid_%prodid%" id="prodid_%prodid%"/>
	                       	
	                       	<span class="imeis_nos_view_%prodid%"></span>
                        </td>
                        <td>
							<input type="text" class="inp iqty"  name="oqty%pid%[]" id="oqty_%prodid%" size=3 value="0">
						</td>
						<td>
							<input type="text" class="inp vat_prc"  name="vat%pid%[]" id="vat_%prodid%" size=3 value="">
						</td>
                        <td style="text-align: center">
							<div class="po_qty_wrap">
								<b>MRP(Rs.) <span class="red_star">*</span> : </b>
										<input type="text" class="inp prod_mrp mrp_%prodid% readonly" readonly="readonly" name="mrp%pid%[]" size=5 pmrp="%mrp%" placeholder="%po_mrp%" po_mrp="%po_mrp%" dp_price="%dp_price%" prodid="%prodid%" value="">
										
										<div class="upd_pmrp_blk" align="center">
											<input type="checkbox" prodid="%prodid%" value="1" class="upd_pmrp upd_mrp_chk%prodid% fl_right" name="upd_pmrp%pid%[]" >
											<input type="hidden" name="upd_pmrp_flag%pid%[]"  value="0" class="upd_pmrp_flag">
											<span>Update Product &amp; Deal MRP</span>
										</div>
										
										<div>
											<b style="visibility:%dp_price_inp%">PO DP(Rs.) : </b>
											<span style="visibility:%dp_price_inp%">
												<input type="text" class="inp dp_price" name="dp_price%pid%[]" size=5 readonly="readonly" dp_price="%dp_price%" value="%dp_price%">
													<div class="upd_dp_price_blk" align="center"> 
														<span>Update DP Price</span>
														<input type="checkbox" class="upd_dp_price" name="upd_dp_price%pid%[]" >
													</div>
											</span>
										</div>
										<div><b>Margin(%) : </b>%po_margin%(%)</div>
										<div><b>Pur. Price(Rs.) : </b><input type="text" class="inp pprice" name="price%pid%[]" p_price="%price%"  readonly="readonly" size=5 value="%price%"></div>
							  </div>
						</td>
                        <td>
							<input type="text" disabled="" class="inp sub_ttl subttl_%prodid%" size=3 value="0" >
						</td>
						<td width="50">
							<span class="addrow_tooltip" title="Click to add new MRP Details"><a href="javascript:void(0)" class="validate_edit_mode add_product_row"><div class="button button-tiny_wrap cursor">+</div></a></span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<div id="invoice_template" style="display:none">
		<table>
			<tbody>
				<tr>
					
					<td><?=$i++?></td>
					<td><input type="text" name="invno[]" class="inp inv_inp_blk" class="invno"></td>
					<td><input type="text" name="invdate[]" class="inp invdate inv_inp_datepick_blk datepick%dpi%" class=""></td>
					<td><input size=7 type="text" class="inp invamount inv_inp_amount" name="invamount[]" class=""></td>
					<td><input type="file" name="scan_%dpi%"></td>
					<td align="center">
						<a href="javascript:void(0)" onclick="removeInvoiceRow(this)"><div class="button button-tiny_wrap cursor"><img width="6px" height="6px" style="margin-top: 4px" src="<?php echo base_url().'images/remove.png'?>"></div></a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>	
</div>

<div id="scanned_summ" >
	<div class="scanned_summ_total"><div id="scan_title">Scanned Rows</div>
	<div class="scanned_summ_total_qty">
		<span id="summ_scanned_ttl_qty">0</span> / <span id="summ_ttl_qty">0</span></div>
	</div>
</div>

<div id="grn_color_legends" style="display: none;">
	<h4>Color Legends</h4>
	<div class="legend_wrap"><b>Row Highlight : </b><span class="row_hg"></span></div>
	<div class="legend_wrap"><b>Edit Mode : </b><span class="edit_mode_color"></span></div>
	<div class="legend_wrap"><b>Processed : </b><span class="processed_mode_color"></span></div>
</div>

<div id="add_barcode_dialog" class="add_barcode_dialog" title="Scan Barcode">
	<form >
		<span class="prod_title"></span>
		<input type="hidden" value="" id="abd_pid">
		<span>Barcode : <input type="text" class="inp" style="width:200px;" id="abd_barcode"></span>
		<span class="error_inp"></span>
	</form>
</div>
	
<div id="rqty_imei_blk_dlg" class="rqty_imei_blk_dlg" title="IMEI Numbers">
	<form>
		<span class="prod_title"></span>
		<input type="hidden" value="" id="imei_pid">
		<div class="imei_nos_dlg prodid_%prodid%"></div>
		<span class="imei_error_text"></span>
	</form>
</div>

<script type="text/javascript">

var pids_selected=[];
var ttl_imei_row_scanned=0;

function vendors(ch)
{
	 $.post(site_url+'/admin/vendors_list',{ch:ch},function(resp){
    	if(resp.status == 'error')
			{
				//alert("Vendor not found for selected character");
				$('.jq_alpha_sort_alphalist_itemlist').html("<div class='po_alert_wrap'>NO Vendors Found</div>");
				$('.jq_alpha_sort_overview_content').html("<div class='po_alert_wrap'>NO Purchase Orders Found</div>");
				return false;
		    }
			else
			{
				var vend_list = '';
				$.each(resp.vendor_list,function(i,v){
		 			vend_list += '<div class="jq_alpha_sort_alphalist_itemlist_divwrap"><a  href="javascript:void(0)" vid="'+v.vendor_id+'">'+v.vendor_name+'</a></div>';
				});
				//polist_byvendor(vids[0]);
			$('.jq_alpha_sort_alphalist_itemlist').html(vend_list);
			$('.jq_alpha_sort_alphalist_itemlist a:first').trigger('click');		
			}
    },'json');
}


function polist_byvendor(id)
{
	pids_selected=[];
	$.post(site_url+'/admin/polist_byvendor',{vid:id},function(resp){
		
			var po_list = '';
			po_list +='<div class="ajax_loadresult static_pos">';
			$.each(resp.po_list,function(i,p){
				$('.jq_alpha_sort_overview_title').html("<h4>Purchase Order List for <span>"+p.vendor_name+"</span></h4>")
	 			po_list +='<a class="" id="pos_'+p.po_id+'" poid="'+p.po_id+'" href="javascript:void(0)" onclick=pid_container('+p.po_id+')>';
	 			
	 			po_list +='<div id="pricing_plan2" class="four columns">';
            	po_list +='<dl class="plans">';
                po_list +='</dl>';
            	po_list +='<dl class="plan" id="two">';
            	po_list +='<dd class="plan_features" style="font-weight:bold;font-size:16px">';
                po_list +='<div class="feature_desc">PO : <span class="title_highlight" style="font-weight:bold;font-size:16px;color:#000">'+p.po_id+'</span></div>';
                po_list +='</dd>';
            	po_list +='<dd class="plan_features">';
                po_list +='<div class="feature_desc">Value : <span class="title_highlight">'+p.total_value+'</span></div>';
                po_list +='</dd>';
                po_list +='<dd class="plan_features">';
                po_list +='<div class="feature_desc" style="font-size:12px;">Qty : <span class="title_highlight">'+p.order_qty+'</span></div>';
                po_list +='</dd>';
               	po_list +='<dd class="plan_features">';
                po_list +='<div class="feature_desc">D. Date : <span class="title_highlight">'+p.delivery_date+'</span> </div>';
                po_list +='</dd>';
                
                /*
                if(p.remarks)
 				{
                    po_list +='<dd class="plan_features">';
					po_list +='<div class="feature_desc">Remarks : <span class="title_highlight">'+p.remarks+'</span> </div>';
                    po_list +='</dd>';
                }*/
                
            	po_list +='</dl>';
        		po_list +='</div>';
	 			
				po_list +="</a>";
			});
			po_list +="</div>";
		$('.jq_alpha_sort_overview_content').html(po_list);
	},'json');	
}

$('.form_cancel').click(function(){
	if(confirm("Warning: \r\nAre you sure , do you want to cancel this Stock Intake Process ?"))
	{
		window.location.assign(site_url+'/admin/apply_grn');	
	}
});


 $(".po_list_byvendor_wrap a").change(function() {
 	 $(".po_list_byvendor_wrap div").removeClass("jq_alpha_active");
      // add class to the one we clicked
      $(this).addClass("jq_alpha_active");
   });
   
   
var added_pos=[];

function pid_container(pid)
{
	
	if($.inArray(pid,pids_selected)!=-1)
	{
		pids_selected.splice(pids_selected.indexOf('pid'), 1);
		
	}
	else
	{
		pids_selected.push(pid);
	}
}

function check_dup_imei(imei,prodid)
{
	var imeis=[];
	$(".imeis").each(function(){
		c=$(this).val().split(",");
		imeis=imeis.concat(c);
	});
	 
	if($.inArray(imei,imeis)!=-1)
	{
		alert("This Serial no is already entered");return false;
	}
	return true;
}

function cal_invoice_value()
{
	inv_total=0;
	$(".invoice_tab	 tbody tr").each(function(){
		$i=$(this);
		inv_inp_amount=parseFloat($(".inv_inp_amount",$i).val());
		inv_inp_amount=isNaN(inv_inp_amount)?"":inv_inp_amount;
		
		inv_total +=inv_inp_amount;
	});
	$("#invoice_ttl_value").html(inv_total);
}


function calc_rec_value()
{
	r_total=0;
	r_total_qty = 0;
	$("#grn .datagrid tbody tr").each(function(){
		$p=$(this);
		var prodid = $('input[name="prodid[]"]',$p).val();
		rqty=parseInt($(".rqty",$p).val());
		rqty=isNaN(rqty)?"0":rqty;
		pprice=parseFloat($(".pprice",$p).val());
		pprice=isNaN(pprice)?"0":pprice;
		
		$('.subttl_'+prodid,$p).val(rqty*pprice);
		
		r_total+=rqty*pprice;
		r_total_qty += rqty;
		
		if(!$.trim($('.scan_pbarcode',this).val()))
		{
			if(rqty == 0)
			{	
				if($(this).hasClass('unsc'))
				{
					$(this).addClass('revscan').removeClass('unsc');
				}
			}else
			{
				if($(this).hasClass('revscan'))
				{
					$(this).addClass('unsc').removeClass('revscan');
				}
			}
		}
		
	});
	$("#grn_ttl_value").html("Rs "+r_total.toFixed(2));
	$("#grn_ttl").html(r_total.toFixed(0));
	$("#grn_ttl_rqty").html(r_total_qty);
	$("#value_receiving").html("Rs "+r_total.toFixed(2));
}

var dpi=0,dpe=0;
function cloneinvoice()
{
	dpi++;
	temp=$("#invoice_template tbody tr").html();
	temp=temp.replace(/%dpi%/g,dpi);
	$(".invoice_tab tbody").append("<tr>"+temp+"</tr>");
	$(".datepick"+dpi).datepicker({minDate:0});
	
	$(".invoice_tab tbody tr").each(function(i,tr){
		$('td:first',this).text(i*1+1);
		$('.inv_inp_blk',this).select();
	});
}

function removeInvoiceRow(ele)
{
	$(ele).parents('tr:first').remove();
	var ttl_prod_rows = $('#grn .datagrid tbody tr').length;
	$(".invoice_tab tbody tr").each(function(i,tr){
		$('td:first',this).text(i*1+1);
		$('.inv_inp_blk',this).select();
	});
}

var tabindex_cnt = 1;
var ime=[];
function loadpo(pids)
{
	$("#po_loading").show();
	$(".venl"+$("#grn_vendor").val()).show();
	$.post('<?=site_url('admin/jx_grn_load_po')?>',{p:pids},function(data){
		
		pois=$.parseJSON(data);
		g_rows="";
		dpes=[];
		$.each(pois,function(i,poi){
			dpe++;
			grow=$("#grn_template .right table tbody").html();
			need_scan = 0;
			update_barcode='';
			ime.push(poi.imei_nos);
			if(poi.bcodes.length==0)
				update_barcode="Add";
			else
			{
				update_barcode="Update";
				need_scan = 1;
			}
			var add_imei='';
			var imei_out='';
            var tot_rqty = parseInt(poi.order_qty)-parseInt(poi.received_qty);
          
			if(poi.is_serial_required==1)
			{
				add_imei=" no.";
				grow=grow.replace(/imeisvvv/g,"imeis");
				
				imei_out = print_imei_inputs(tot_rqty,poi.product_id);
            }
             grow=grow.replace(/%imei_nos%/g,imei_out);
             
            var prodbcodes = '';
				if(poi.barcode)
					poi.bcodes.push(poi.barcode);
				if(poi.bcodes.length)
					prodbcodes += poi.bcodes.join(' barcode');
					
			if(need_scan)
				prodbcodes +=' unsc ';
			
			grow=grow.replace(/%has_serialno%/g,poi.is_serial_required);
			grow=grow.replace(/%brandid%/g,poi.brand_id);
			grow=grow.replace(/%update_barcode%/g,update_barcode);
			grow=grow.replace(/%add_serial%/g,add_imei);
			grow=grow.replace(/%bcode%/g,prodbcodes);
			grow=grow.replace(/%prodid%/g,poi.product_id);
			grow=grow.replace(/%sno%/g,dpe);
			grow=grow.replace(/%pid%/g,poi.po_id);
			grow=grow.replace(/%name%/g,'<a href="'+site_url+'/admin/product/'+poi.product_id+'" target="_blank">'+poi.product_name+'</a>');
			grow=grow.replace(/%qty%/g,poi.order_qty);
			
			grow=grow.replace(/%pqty%/g,tot_rqty);
            grow=grow.replace(/%po_mrp%/g,poi.mrp);
            grow=grow.replace(/%po_margin%/g,poi.margin);
			grow=grow.replace(/%mrp%/g,poi.prod_mrp);
			if(poi.is_serial_required*1)
			{
				grow=grow.replace(/%dp_price%/g,poi.dp_price);
				grow=grow.replace(/%dp_price_inp%/g,'visible');
			}
			else
			{
				grow=grow.replace(/%dp_price%/g,0);
				grow=grow.replace(/%dp_price_inp%/g,'hidden');
			}
				
			grow=grow.replace(/%price%/g,poi.purchase_price);
			grow=grow.replace(/%rqty%/g,poi.received_qty);
			
			grow=grow.replace(/%ppur_price%/g,(poi.prod_mrp*poi.purchase_price/poi.mrp));
			
			if(poi.rbs)
				grow=grow.replace(/==rbs==/g,poi.rbs);
			else
				grow=grow.replace(/==rbs==/g,'<option value="10">A11-Default Rack</option>');
			
			grow=grow.replace(/%dpe%/g,dpe);
			offer=foc="NO";
			if(poi.is_foc=="1")
				foc="YES";
			if(poi.has_offer=="1")
				offer="YES";
			grow=grow.replace(/%foc%/g,foc);
			grow=grow.replace(/%offer%/g,offer);
			g_rows=g_rows+grow;
			$(".expdate"+dpe).datepicker({minDate:0});
			dpes.push(".expdate"+dpe);
			$("#grn_pids").append('<input type="hidden" name="poids[]" value="'+poi.po_id+'">');
			added_pos.push(poi.po_id);
		});
		
		$("#grn .datagrid tbody").append(g_rows);
		
		$(dpes.join(", ")).datepicker({minDate:0});
		
		reset_tabindex(0,function(){});
		
		$("#po_loading").hide();
		$("#loadafterselect").show();
		calc_rec_value();
		
		$('#grn .datagrid tbody tr').each(function(){
			if($(this).attr('has_serialno') == 1)
			
				$('.imei_scanned_div',this).show();
			else
				$('.imei_scanned_div',this).hide();
				
		});
		
		scanned_rows();
		Tipped.create('.addrow_tooltip',{
			skin: 'black',
			hook: 'topmiddle',
			hideOn: false,
			closeButton: false,
			opacity: .5,
			hideAfter: 200,
		});
		$('#grn .datagrid tbody tr:first').focus();
	});
}

function scanned_rows(prodid)
{
	$('.upd_mrp_radio'+prodid).attr('checked',true);
	var ttl_prod_rows = $('#grn .datagrid tbody tr').length;
	var ttl_prod_rows_scanned = 0;
	$('#grn .datagrid tbody tr').each(function(){
		if($(this).hasClass('processed_mode'))
				ttl_prod_rows_scanned ++;
				
	});
	$('#summ_scanned_ttl_qty').text(ttl_prod_rows_scanned);
	$('#summ_ttl_qty').text(ttl_prod_rows);
}

function reset_tabindex(row,cb)
{
	//$('span.name').parents('tr:first').attr('tabindex',tabindex_cnt);
	$("#grn .datagrid tbody tr").each(function(){
		// pr index 1 loc
		// pr index 1 mrp
		// pr index 1 inv qty 
		// pr index 1 recv qty
	
		$('.add_barcode_wrap a',this).attr('tabindex',tabindex_cnt);
		tabindex_cnt++;
		
		$('select.stkloc',this).attr('tabindex',tabindex_cnt);
		tabindex_cnt++;
		$('input.rqty',this).attr('tabindex',tabindex_cnt);
		tabindex_cnt++;
		$('input.iqty',this).attr('tabindex',tabindex_cnt);
		tabindex_cnt++;
		$('input.vat_prc',this).attr('tabindex',tabindex_cnt);
		tabindex_cnt++;
		$('input.prod_mrp',this).attr('tabindex',tabindex_cnt);
		tabindex_cnt++;
		$('input.upd_pmrp',this).attr('tabindex',tabindex_cnt);
		tabindex_cnt++;
		$('.validate_edit_mode',this).attr('tabindex',tabindex_cnt);
		tabindex_cnt++;
	
	});
	
	$('.recv_qty_wrapper').attr('tabindex',tabindex_cnt);
	tabindex_cnt++;
	return cb(row);
}



function reset_rec_f()
{
	v=parseInt($("#reset_rec").val());
	if(isNaN(v))
	{
		alert("Not a number");return;
	}
	if(confirm("Are you sure want to reset all receiving qty to "+v+" ?"))
		$("#apply_grn_form .rqty").val(v);
	calc_rec_value();
}

function reset_inv_f()
{
	v=parseInt($("#reset_inv").val());
	if(isNaN(v))
	{
		alert("Not a number");
		return false;
	}
	if(confirm("Are you sure want to reset all invoice qty to "+v+" ?"))
		$("#apply_grn_form .iqty").val(v).change();
}


function print_imei_inputs(tot_rqty,prodid) {
	
	var imei_out =' <ol>';
    var c=0;
    for(i=0;i<tot_rqty;i++) {
            c +=1; //<input type="text" value="" id="aid_pid">
            imei_out +='<li>';
			imei_out +='	<input type="text" class="inp imei_input" placeholder="IMEI No" name="imei_input_'+prodid+'[]" id="imei_input_'+prodid+'_'+c+'"" onchange="return validate_imeino_input(this,'+prodid+');" value="">';
			imei_out +='	<span class="imei_remove" onclick="remove_input_imei('+prodid+','+c+');" id="imei_remove_'+prodid+'_'+c+'">&nbsp; <img src="'+site_url+'/images/cross.png'+'"> </span>';
			imei_out +='	<span class="append_imei_items_'+prodid+'_'+c+'"></span>';
			imei_out +='</li>';                        
    }
    imei_out+='</ol><!--<span class="imei_add" onclick="add_input_imei('+prodid+');" id="imei_add_'+prodid+'">&nbsp; Add </span>-->';
    return imei_out;
}


$('.prod_mrp').live('change',function(){
	
	var trele = $(this).parents('tr:first');
	var prodid = $(this).attr('prodid')*1;
	var mrp = $(this).val();
	var dp_price = $(this).attr('dp_price')*1;
	var prodid = $(this).attr('prodid')*1;
	var pmrp = $(this).attr('pmrp')*1;
	var po_mrp=$(this).attr('po_mrp')*1;
	var tot_rqty=$('.rqty').val();
	var upd_mrp_blk = trele.find('.upd_pmrp_blk');
	var pprice = $('.pprice',trele).attr('p_price');
	
		if(dp_price != 0)
		{
			if(mrp <= dp_price)
			{
				$('.row_error_inp',trele).html('<span>MRP should be greater than DP price</span>');
				$('.prod_mrp',trele).val("");
				return false;
			}
		}
		else
		{
			// compute new pprice
			new_pprice = (mrp*pprice)/po_mrp;
			$('.pprice',trele).val(new_pprice);
			calc_rec_value();
		}
});


$('.prod_mrp').live('keyup',function(){
	
	var trele = $(this).parents('tr:first');
	var mrp = $(this).val();
	var pmrp = $(this).attr('pmrp')*1;
	var upd_mrp_blk = trele.find('.upd_pmrp_blk');
		if(pmrp != mrp)
			upd_mrp_blk.show();
		else
			upd_mrp_blk.hide();
});

$('.upd_pmrp').unbind("change").live("change",function(){
	var prodid = $(this).attr('prodid');
	var cur_check_stat = $(this).attr('checked');
		$('input.upd_mrp_chk'+prodid).parents('tr:first').find('.upd_mrp_flag').val("0");
		$('input.upd_mrp_chk'+prodid).attr('checked', false);
		
		if(cur_check_stat)
		{
			$('.upd_pmrp_flag',$(this).parents('tr:first')).val("1");
			$(this).attr('checked',true);
		}
	
});
$('.inv_inp_amount').live("change",function(){
	
	trele=$(this).parents('tr:first');
	var amount = $(this).val();
	$(this).val(amount);
	if(amount < 0)
	{
		alert("Please give valid Invoice number")
		$(this).val("");
		$('.inv_inp_amount',trele).focus();
	}
	else
	{
		cal_invoice_value();
	}
});


$('.bcode_upd').live('click',function(e){
	var trEle = $(this).parents('tr:first');
	var pid = $(this).attr('pid');
	var prodid = $(this).attr('prodid');
	var pname = $('span.name',trEle).text();
	
		$("#add_imei_dialog").hide();
		$("#add_barcode_dialog").data('ref_tr',trEle).dialog('open');
		$(".prod_title").html("Product : "+pname);
		$("#abd_barcode").focus().val("");
		$("#abd_pid").val(pid);
		$("#abd_pid").data('prodid',prodid);
		//$(".add_barcode_dialog").dialog('open');
});


$(".datagrid .iqty").live("change",function(){
	trele=$(this).parents('tr:first');
	var tot_rqty = $('.rqty',trele).val();
	var tot_iqty = $(this).val();
    	tot_iqty = isNaN(tot_iqty)?0:tot_iqty;
    	$(this).val(tot_iqty);
    	if(tot_iqty.toString().indexOf('.') != -1 || tot_iqty < 0)
    	{
    		alert("Please give valid Invoice quantity");
    		$(this).val("0");
    		return false;
    	}else if(tot_iqty > tot_rqty)
    	{
    		if(confirm("Warning: \r\nInvoice Qty is greater than required Qty.Do you want to Proceed"))
			{
				$('.edit_mode .vat_prc').select();	
			}
			else
			{
				$(this).val("0").select();
			}
    	}
    
});

$(".inv_inp_blk").live("change",function(){
	trele=$(this).parents('tr:first');
    	$(this).val(inv);
    	if(inv.toString().indexOf('.') != -1 || inv < 0)
    	{
    		alert("Please give valid Invoice number");
    		$(this).val("");
    		return false
    	}
});

$(".datagrid .rqty").live("change",function(){
		
		trele=$(this).parents('tr:first');
		var tot_rqty = $(this).val();
    	tot_rqty = isNaN(tot_rqty)?0:tot_rqty;
    	if(tot_rqty.toString().indexOf('.') != -1 || tot_rqty < 0)
    	{
    		alert("Please give valid required Qty");
    		$(this).val("0");
    		$('.edit_mode .rqty').select();
    	}
    	else
    	{
    		 $(this).val(tot_rqty);
    		 var prodid = $(this).attr('prodid');
	  		 var brandid = $(this).attr('brandid');
	    	 var pid = $(this).attr('pid');
	    	 var trele=$(this).parents('tr:first');
	   		 var inp_barcode=$("#abd_barcode",trele).val();
	  		 has_serialno=trele.attr('has_serialno')*1;
		     calc_rec_value();
		   	
		   	if(has_serialno == 1)
		     {
		     	if(tot_rqty > 0)
			     {
			     	$('.view_imei',trele).show();
			     }
			     else
			     {
			     	$('.view_imei',trele).hide();
			     }
		     }else
		     {
		     	$('.view_imei',trele).hide();
		     }
			 
		    if(has_serialno == 1)
		    {
		    	var p_imei_scanned = $("#list_imei_"+prodid,trele).val();
					p_imei_scanned_arr = p_imei_scanned.split(',');
					
		    		if($("#list_imei_"+prodid,trele).val().length > 0)	
				{
					if(!confirm("Warning:\nRequired quantity is changed. All scanned IMEIs will be cleared.\nDo you want to proceed?")) 
		            { 
		            	// rset scannred imei ttl
			    		$(this).val(p_imei_scanned_arr.length);
		            	return false; 
		            }
		            else
		            {
		            	$("#list_imei_"+prodid,trele).val("");
		            }
		            
				}
				if(tot_rqty != 0)
		    		$('#rqty_imei_blk_dlg').data("pro_data",{'prodid': $(this).attr("prodid"),'brandid': $(this).attr("brandid"),'ttl_qty':tot_rqty,'pid' : $(this).attr('pid'),'ref_tr' : trele,'state':'add'}).dialog('open');
		    	
		    }
		    
		    var imeilist_html ='';
		 	var scanned_imei = '';
	
			var p_imei_scanned = $("#list_imei_"+prodid,trele).val();
				p_imei_scanned_arr = p_imei_scanned.split(',');
				//imeis.push(p_imei_scanned_arr);
				
				imeilist_html = '<ol>'; 
		    	for(i=0;i<tot_rqty;i++) 
		    	{
		    			scanned_imei = ((p_imei_scanned_arr[i]==undefined)?"":p_imei_scanned_arr[i]);				    		
						imeilist_html += '<li>';
						imeilist_html += '	<input type="text" class="inp imei_input dlg_imei_inp" placeholder="" name="imei_input_'+prodid+'[]" id="imei_input_'+prodid+'_'+i+'" value="'+scanned_imei+'">';
						imeilist_html += '	<span class="append_imei_items_'+prodid+'_'+i+'"></span>';
						imeilist_html += '</li>';
				}
				imeilist_html+='</ol>';
				
		 	$('.imei_nos_dlg').html(imeilist_html);
			$('.dlg_imei_inp:first').focus();
		    
		    
		    if(isNaN(tot_rqty*1))
			{
				$('.prod_mrp',trele).attr('readonly',true).addClass('readonly');
			}else
			{
				$('.prod_mrp',trele).attr('readonly',false).removeClass('readonly');
				$('.iqty',trele).val("0");
			}
    	}
});


$(function(){
	
	$('.page_title').html("Stock Intake");
	$('.selected_po_list').hide();
	$(document).keydown(function(e){
		if(e.which==27)
			$("#add_barcode_dialog,#add_imei_dialog").hide();
		return true;
	});

	$("#srch_barcode").keyup(function(e){
		if(e.which==13)
		{
			var scan_bc =  $(this).val();
			
			$(".barcodereset").removeClass("highlightprow");
			
			$('.lastScanned').removeClass('lastScanned');
			
			var bcodeRow = $(".barcode"+scan_bc);
			
			if(bcodeRow.length==0)
			{
				alert("Product not found for this Barcode");
				return;
			}
			bcodeRow.addClass("row_hg").removeClass("bcode_scanned").removeClass("unsc");
			
			if(bcodeRow.length > 1)
			{
				alert(bcodeRow.length+" Products found with same barcode");
			}
			
			bcodeRow.addClass("lastScanned");
			$('.lastScanned .rqty').select();
			
			
			setTimeout(function(){
				$(".barcode"+scan_bc).addClass("bcode_scanned");
			},500);
			$(".barcode"+$(this).val()+' .scan_pbarcode').val($(this).val());
			$(document).scrollTop($(".barcode"+$(this).val()).offset().top);
			
		}
	});

	
	$(".static_pos a").click(function(){
			$($(this).parents("td").get(0)).show();
			vid=$("span.vid",$(this).parents("td").get(0)).text();
			$("#grn_vendor").val(vid).attr("disabled",true);
			$("#grn_po_load").attr("disabled",true);
	});

	$("#apply_grn_form input").live("keydown",function(e){
		if(e.which==13)
		{
			e.stopPropagation();
			e.preventDefault();
			return false;
		}
	});
	

	$('#apply_grn_form .dp_price').live('keyup',function(){
		var dp_price = $(this).attr('dp_price')*1;
		var upd_dp_price_blk = $(this).parent().find('.upd_dp_price_blk');
			if(($(this).attr('dp_price')*1 != $(this).val()*1) && ($(this).val() != "") ){
				$('.upd_dp_price',upd_dp_price_blk).attr('checked',true);
				upd_dp_price_blk.show();
			}
			else
			{
				$('.upd_dp_price',upd_dp_price_blk).attr('checked',false);
				upd_dp_price_blk.hide();	
			}
	});
	
	
	var chk_for_vendor_ids = 1;
	$("#apply_grn_form").submit(function(){
		flag=true;
		
		var inv_ttl = $("#invoice_ttl_value").text();
		var grn_ttl = $("#grn_ttl").text();
		
		
		var scan_status = $(".processed_mode").length;
		if(scan_status == 0)
		{
			alert("Please scan atleast 1 products from List before submitting");
			flag=false;
			return flag;
		}	
	
		
		if($(".prod_addcheck").length==0)
		{
			alert("Please Load a PO");
			flag=false;
			return flag;
		}
		var stkloc_flag = true;
		$('.stkloc',this).each(function(){
			if($(this).val() == "")
			{
				stkloc_flag=false;
			}
		});

		if(!stkloc_flag)
		{
			alert("Please choose rackbin");
			return false;
		}
		
		
		$('.invoice_tab tbody tr',this).each(function(){
			
			var trele=$(this).parents('tr:first');
			var inv_no =$('.inv_inp_blk',trele).val(); 
			var inv_date =$('.inv_inp_datepick_blk',trele).val(); 
			var inv_mrp =$('.inv_inp_amount',trele).val(); 
		
			
			if(inv_no.length == 0 || inv_date.length == 0 || inv_mrp.length == 0 || isNaN(inv_mrp*1))
			{
				alert("Please enter valid invoice Details");
				
				if(inv_no.length == 0)
				{
					$('.inv_inp_blk').select();
					$('.inv_inp_blk').addClass('imei_error_inp');
				}
				else if(inv_date.length == 0)
				{
					$('.inv_inp_datepick_blk').addClass('imei_error_inp');
				}
				else if(inv_mrp.length == 0 || isNaN(inv_mrp*1))
				{
					$('.inv_inp_amount').addClass('imei_error_inp');
				}
				else
				{
					$('.inv_inp_blk').removeClass('imei_error_inp');
					$('.inv_inp_datepick_blk').removeClass('imei_error_inp');
					$('.inv_inp_amount').removeClass('imei_error_inp');
				}
				flag=false;
				return false;
			}
		});
		var remarks = $('.remarks').val();
		if(!remarks)
		{
			alert("Please enter Remarks");
			$('.remarks').addClass('imei_error_inp');
			flag=false;
			return flag;
		}else
		{
			$('.remarks').removeClass('imei_error_inp');
		} 
		
		if(chk_for_vendor_ids && flag)
		{
			var frm_ele = $(this);
			$.post(site_url+'/admin/check_vendor_invs',$(this).serialize(),function(resp){
				if(resp.error)
				{
					alert(resp.error);
				}else
				{
					chk_for_vendor_ids = 0;
					flag = false;
					frm_ele.submit();
				}
			},'json');
			flag = false;
		}
		
		if(flag)
			$("#form_submit").attr("disabled",true);
		return flag;
	});
	
	$(".expdate, .datepick").datepicker({minDate:0});
	$("#grn_vendor").attr("disabled",false);
	
	<?php if(isset($po)){?>
		$("#grn_vendor").attr("disabled",true);
		added_pos.push(<?=$po['po_id']?>);
	<?php } ?>
	
	$("#grn_po_load").click(function(){
		$("#po_loading").show();
		$.post('<?=site_url('admin/jx_getpos')?>',{v:$("#grn_vendor").val()},function(d){
			$("#po_loading").hide();
			
			$("#pending_pos").html(d.ven_det_name);
			$("#grn_vendor").attr("disabled",true);
		});
	});
	
	$("#grn .datagrid .qtychange").live("change",function(){
		$p=$(this).parents("tr").get(0);
		q=parseInt($(".popqty",$p).val())-parseInt($(".rqty",$p).val());
		if(q<0)
			q="("+(q*-1)+")";
		$(".pqty",$p).html(q);
                
	});
        
	$("#grn .datagrid .pprice, #grn .datagrid .rqty").live("change",function(){
		calc_rec_value();
	});
	
	$("#aid_imei").keydown(function(e){
		if(e.which==13)
			add_imei();
		return true;
	});
});


/** function to add more product rows */
$('.add_product_row').live('click',function(e){
	e.preventDefault();
	
	var trele = $(this).parents('tr:first');
	var new_trele = trele.next();
	var trele_html = trele.html();
	var trele_bcode = trele.attr('bcode');
	var trele_rqty = $('.rqty',trele).val()*1;
	var trele_vat = $('.vat_prc',trele).val()*1;
	var trele_iqty = $('.iqty',trele).val()*1;
	var trele_mrp = $('.prod_mrp',trele).val()*1;
	var trele_name = $('span.name',trele).text();
	var trele_pmrp = $('.prod_mrp',trele).attr('pmrp')*1;
	var trele_prodid = trele.attr('prodid');
	var trele_oqty = trele.attr('qty');
	var trele_dpprice = trele.attr('dp_price');
	var trele_purprice = trele.attr('p_price');
	var trele_brandid = trele.attr('brandid');;
	var trele_pid = trele.attr('pid');
	var trele_rackname = $('.stkloc').html();
	var trele_has_serialno = trele.attr('has_serialno');
	var trele_bcscanned = trele.hasClass('bcode_scanned');
	//alert(trele_has_serialno);
	
	
	process_edit_validation(function (stat) {
		                if (stat) {
		                    $('.edit_mode').removeClass('edit_mode');
		                    
							grow=$("#grn_template .right table tbody").html();
							update_barcode='';
							if(trele_bcode.length==0)
								update_barcode="Add";
							else
							{
								update_barcode="Update";
							}
							
					      	grow=grow.replace(/%has_serialno%/g,trele_has_serialno);
					      	grow=grow.replace(/%bcode%/g,trele_bcode);
							grow=grow.replace(/%update_barcode%/g,update_barcode);
							grow=grow.replace(/%prodid%/g,trele_prodid);
							grow=grow.replace(/%name%/g,trele_name);
							grow=grow.replace(/%pid%/g,trele_pid);
							grow=grow.replace(/%qty%/g,trele_oqty);
							
							if(trele_has_serialno*1)
							{
								grow=grow.replace(/%dp_price%/g,trele_dpprice);
								grow=grow.replace(/%dp_price_inp%/g,'visible');
							}
							else
							{
								grow=grow.replace(/%dp_price%/g,0);
								grow=grow.replace(/%dp_price_inp%/g,'hidden');
							}
							
							grow=grow.replace(/%mrp%/g,trele_pmrp);
							
							grow=grow.replace(/%price%/g,trele_purprice);
							grow=grow.replace(/%rqty%/g,0);
							grow=grow.replace(/%ppur_price%/g,trele_purprice);
							grow=grow.replace(/%brandid%/g,trele_brandid);
							
							if(trele_rackname)
								grow=grow.replace(/==rbs==/g,trele_rackname);
							else
								grow=grow.replace(/==rbs==/g,'<option value="10">A11-Default Rack</option>');
							
							trele.after(grow);
							
							var new_trele = trele.next();
							$('span.name',new_trele).hide();
							$('td:first',new_trele).html('');
							
							
							if(trele_has_serialno != 1)
								$('.imei_scanned_div',new_trele).hide();
							else
								$('.imei_scanned_div',new_trele).show();
								
							$('.pb_blk',new_trele).css('display','none');
							$('.view_imei',new_trele).css('display','none');
							$('.edit_mode',new_trele).removeClass('processed_mode');
							$('td:last a',new_trele).html('<span style="margin:4px 0px 0px 14px;float:left"><img width="12px" height="12px" src="'+base_url+'/images/remove.png'+'"></span>').removeClass('add_product_row').addClass('remove_product_row');
							
							
							$('.prod_mrp',new_trele).val('');
							
							$('.upd_pmrp',new_trele).attr('checked',false);
								tabindex_cnt = 1;
								reset_tabindex(new_trele,function(row){
									scanned_rows();										
									setTimeout(function(){
										$('.rqty',row).trigger('blur');	
									},200)
								});
						} else {
    						return false;
						}
    			});	
});


$('.remove_product_row').live('click',function(e){
	e.preventDefault();
	var trele = $(this).parents('tr:first');
	
	if(confirm("Do you want to delete"))
	{
		$(this).parents('tr:first').fadeOut().remove();
		trele.addClass('edit_mode');
		scanned_rows();
	}else
	{
		
		trele.addClass('edit_mode');
		$('.edit_mode .rqty').select();
		return false;
	}
});

var imei_dup_list=[];
$('#rqty_imei_blk_dlg form').submit(function(){
	var dlgData = $("#rqty_imei_blk_dlg").data("pro_data");
	ref_trele = dlgData.ref_tr;
	prodid = dlgData.prodid;
	brandid = dlgData.brandid;
	pid = dlgData.pid;
	tot_rqty =$('.rqty',ref_trele).val();
	
	$('.imei_error_inp').removeClass('imei_error_inp');
	// check if rty == imei input boxes 
	if($('.dlg_imei_inp').length != tot_rqty)
	{
		alert("Require qty is not matching with total imei inputs ");
		return false;
	}
	
	var scanned_imei_nos = new Array();
	// check ig all imei inp is entered 
	$('.dlg_imei_inp').each(function(){
		var scn_imei = $.trim($(this).val());
		$(this).val(scn_imei);
		if(scn_imei == "")
		{
			$(this).addClass('imei_error_inp');
		}
		else
		{	
			if($.inArray(scn_imei,scanned_imei_nos) != -1)
			{
				$('.imei_error_text').html("Please enter valid imei/serialnos : "+scn_imei+" serial no already exists");
				$(this).addClass('imei_error_inp');
			}
			else
			{
				$(this).attr('scninp_imei','imei_'+scn_imei);
				scanned_imei_nos.push(scn_imei);
			}
		}
	});
	
	if($('.imei_error_inp').length)
	{
		return false;
	}
	
	if(scanned_imei_nos.length == tot_rqty) 
	{
		$.post(site_url+'/admin/jx_chkimeiforgrn',{'imeino':scanned_imei_nos.join(","),'prodid':prodid,'brandid':brandid},function(resp){
			if(resp.status == 'error')
			{
				if(resp.duplicate != undefined)
				{
					imei_dup_list=[];
					ref_trele = dlgData.ref_tr;
					$.each(resp.duplicate,function(a,dup_imei){
						imei_dup_list.push(dup_imei);
						$('input[scninp_imei="imei_'+dup_imei+'"]').addClass('imei_error_inp');
					});
					$('#rqty_imei_blk_dlg .imei_error_inp:first').focus();
				}
			}else
			{
				imei_dup_list=[];
				var imei_nos=resp.imeinos.split(",");
				var imei_sort=imei_nos.sort();
				
				var imei_sort_result=[];
					for(var i=0;i<imei_nos.length - 1;i++)
					{
						if (imei_sort[i + 1] == imei_sort[i]) 
						{
					    	imei_sort_result.push(imei_sort[i]); 
					     }
					}
				
					if(imei_sort_result.length > 0 )
					{
						$('.imei_error_text').html((imei_sort_result.join(","))+" serial number is repeated");
					}
					else
					{
						$("#imei_pid").val(pid);
						$("#imei_pid").data('prodid',prodid);
						
						// added scanned imeis to hidden input 
						$("#list_imei_"+prodid,ref_trele).val(resp.imeinos);
						
						if($("#list_imei_"+prodid,ref_trele).val())
						{
							$('.imei_scanned_status',ref_trele).addClass('active').html("YES");
							$('.imei_scanned_status',ref_trele).show();
						}else
						{
						
						}
					}
					$("#rqty_imei_blk_dlg").dialog('close');
					$('.iqty',ref_trele).select();
			}
		},'json');
	}
	
	
	return false;
});

	$('#add_barcode_dialog form').submit(function(){
		var dlgEle = $('#add_barcode_dialog');
		var ref_trele = dlgEle.data('ref_tr');
		var b_code=$("#add_barcode_dialog",this);
		var inp_barcode=$("#abd_barcode").val();
		var ttl_qty=$('.rqty',ref_trele).val();
			//var product_name=$('.name',trele).text();
			
			if(isNaN(inp_barcode))
			{
				$('.error_inp',dlgEle).html("Please enter valid Barcode");
				return false;
			}else
			{		
				dlgEle.dialog('close');
					
				var chk_prodid = $('#abd_pid').data('prodid');
					$('.pbcodecls'+chk_prodid,ref_trele).val($('#abd_barcode').val());
					$("#add_barcode_dialog").hide();
					
					$('.view_barcode'+chk_prodid,ref_trele).html($('#abd_barcode').val());
					if($('#abd_barcode').val())
					{
						$('.pbcodecls'+chk_prodid,ref_trele).parents('tr:first').addClass('bcode_scanned');
						$('.bcode_scanned_status',ref_trele).addClass('active').html("YES");
						$('.bcode_scanned_status',ref_trele).show();
						$('.pb_blk',ref_trele).show();
					}else
					{
						$('.pbcodecls'+chk_prodid,ref_trele).parents('tr:first').removeClass('bcode_scanned');
					}
							
					$('.pbcodecls'+chk_prodid,ref_trele).parents('tr:first').removeClass('lastScanned').removeClass('unsc');
						setTimeout(function(){
							process_edit_validation(function (stat) {
				                if (stat) {
				                    $('.edit_mode').removeClass('edit_mode');
				                } else {
				                    return false;
				                }
				            });	
					},300)
			 }
		return false;
	});
	
	$('#add_barcode_dialog').dialog({
		modal:true,
		autoOpen:false,
		width:350,
		height:150,
		autoResize:true,
		open:function(){
		dlg = $(this);
	}
});

$('#rqty_imei_blk_dlg').dialog({
		modal:true,
		autoOpen:false,
		width:350,
		height:400,
		autoResize:true,
			
		 buttons: {
		    "Ok": function() { 
		    	$('#rqty_imei_blk_dlg form').submit();},
		    "cancel": function(){
		    	$('#rqty_imei_blk_dlg').data('process_by','');
				if($('#rqty_imei_blk_dlg').data('process_by') == '')
				{
					//alert($('#rqty_imei_blk_dlg').data('state'));
					var dlgData = $("#rqty_imei_blk_dlg").data("pro_data");
						prodid = dlgData.prodid;
						brandid = dlgData.brandid;
						tot_rqty = dlgData.ttl_qty;	
						pid = dlgData.pid;
						state=dlgData.state;
						ref_trele = dlgData.ref_tr;
						
						if(state == 'add')
						{
							$("#list_imei_"+prodid,ref_trele).val("");
							$('.view_imei',ref_trele).hide();
							$('.rqty',ref_trele).val("0").select();
						}else
						{
							$('.iqty',ref_trele).val("0").select();
						}
				}else
				{
					$('.iqty',ref_trele).val("0").select();
				}
				$("#rqty_imei_blk_dlg").dialog('close');
			} 
		  },	
		open:function(){
			dlg = $(this);
			$('.ui-dialog-titlebar-close').hide();
			$('#rqty_imei_blk_dlg').data('process_by','');
			if(!imei_dup_list.length)
				$('.imei_error_text').html("");
			else
				$('.imei_error_text').html("Duplicate IMEI found : "+imei_dup_list);
	}
});

$('.imei_input').live('keypress',function(e){
	if((e.keyCode || e.which) == 13)
	{
	
		if($(this).parent().next().find('.imei_input').length == 1)
			$(this).parent().next().find('.imei_input').focus();
		else
			$("#rqty_imei_blk_dlg").parent().find(".ui-dialog-buttonpane button:contains('Ok')").focus()
		return true;
	}
});

$('.show').click(function(){
	var pos=[];
	
	$('.ajax_loadresult a.selected').each(function(){
		pos.push($(this).attr('poid'));
	});
	
	if(pos.length)
	{
		loadpo(pos);
		var pos_arr=[];
		$.post('<?=site_url('admin/ven_list_bypo')?>',{pos:pos},function(data){
			$('.page_title').html("Stock Intake for <span class='ven_title_wrap'>"+data.ven_det.vendor_name+"</span>");
			
			var pos_arr ="";
			
			$.each(data.po_id_list,function(i,p){
				pos_arr+="<a target='_blank' href='"+site_url+'/admin/viewpo/'+p+"'>"+p+"</a>";
				if(data.po_id_list.length-1 != i)
				{
					pos_arr+=",";	
				}
			});
			
			$('.selected_po_list').html("<span style='float:left'>Selected PO Ids : "+pos_arr+"</span><span style='float:right'>Total PO Value : Rs."+data.ven_det.ttl+"</span>");
		},'json');
		
		$('.show').hide();
		$('.selected_po_list').show();
		$('#apply_grn_form').show();
		$('.barcode_fixed_wrap').show();
		 $('#grn_color_legends').show();
		$('.jq_alpha_sort_wrap').hide();
		$('#scanned_summ').show();
		$('#grn span.name:first').focus();
		$('.cancel_stock').show();
		$('.inv_inp_blk').html("");
		$('.invamount').html("");
		$('.invdate').html("");
	}
	else
	{
		alert('Please Choose PO before Proceed');
		return false;
	}
	
});

$('.view_imei').live('click',function(){
	//tot_rqty=$('.rqty',$(this).parent()).val();
	 var trele=$(this).parents('tr:first');
	 var tot_rqty=$('.rqty',trele).val();
	$('#rqty_imei_blk_dlg').data("pro_data",{'prodid': $(this).attr("prodid"),'brandid':$(this).attr("brandid"),'ttl_qty':tot_rqty,'ref_tr' : trele,'state':'edit'}).dialog('open');
	
});


function process_edit_validation(cb) {

    // extra check to check if validation need to be done     
    if (!$('.edit_mode').length) return cb(false);
    var edt_trele = $('#grn tr.edit_mode');
    $('#grn tr .error_inp').removeClass('error_inp');
    var rqty = $('.rqty', edt_trele).val() * 1;
    var iqty = $('.iqty', edt_trele).val() * 1;
    var ivat = $('.vat_prc', edt_trele).val() * 1;
    var imrp = $('.prod_mrp', edt_trele).val() * 1;
    var has_bc = $('.bcode_upd', edt_trele).attr('bcode').length;
	var bc_scanned = edt_trele.hasClass('bcode_scanned');
	
		if(bc_scanned) 
		{
			if (rqty) {
		        if (iqty) {
		            if (ivat) {
		                if (imrp) {
		                	edt_trele.addClass('processed_mode');
		                } else {
		                    $('.prod_mrp', edt_trele).addClass('error_inp');
		                }
		            } else {
		                $('.vat_prc', edt_trele).addClass('error_inp');
		            }
		        } else {
		            $('.iqty', edt_trele).addClass('error_inp');
		        }
		    } else {
		        $('.rqty', edt_trele).addClass('error_inp');
		    }
	    }else
	    {
	    	$('.bcode_upd', edt_trele).addClass('error_inp');
	    }
    
    jump_torow = 1;
    if ($('.error_inp', edt_trele).length) {
        if ((bc_scanned?1:0)+rqty + iqty + ivat + imrp != 0) {
            jump_torow = 0;
            if($('#grn tr .error_inp').hasClass('bcode_upd'))
            {
            	$('#grn tr .error_inp').trigger('click');
            }else
            {
            	$('#grn tr .error_inp').trigger('select');
            }
        }
    }
   	scanned_rows();	
    
    return cb(jump_torow);
}

$(function(){
	$('#cus_jq_alpha_sort_wrap').jqSuAlphaSort({title:"Vendors List",'overview_title':"Vendor Po List",'char_click':function(chr,ele){ vendors(chr)},'item_click':function(id,ele){ polist_byvendor(id)}});
	
	cal_invoice_value();
	$('#scanned_summ').hide();
	$('.view_imei').hide();
   	$(".jq_alpha_sort_alphalist_char a").click(function() {
      // remove classes from all
      $(".jq_alpha_sort_alphalist_char a").removeClass("jq_alpha_active");
      // add class to the one we clicked
      $(this).addClass("jq_alpha_active");
   });
    $(".jq_alpha_sort_alphalist_char a:eq(1)").trigger('click');
   
   $('.barcode_fixed_wrap').hide();
   $('#grn_color_legends').hide();
   $('.cancel_stock').hide();
   $('.pb_blk').hide();
   
   
   
   
$('#grn tr').unbind('focus').live('focus', function () {
    if (!$('.edit_mode').length) {
    	$(this).addClass('edit_mode');
    	trele = $(this);
    	process_edit_validation(function(){
        	if(!trele.hasClass('bcode_scanned'))
        		$('.bcode_upd',trele).trigger('click');
        });
        
    } else {
        if ($(this).hasClass('edit_mode')) { // do nothing           
            
        } else { // validate edit_mode row             // if valid to proceeed             
            process_edit_validation(function (stat) {
                if (stat) {
                    $('.edit_mode').removeClass('edit_mode');
                    $(this).addClass('edit_mode');
                } else {
                    return false;
                }
            });
        }
    }
});

$(".datagrid tr .rqty,.datagrid tr .iqty,.datagrid tr .vat_prc,.datagrid tr .bcode_upd").live('blur',function(e){
	e.preventDefault();
	if($(this).parents('tr:first').attr('has_serialno') != "1" && $(this).hasClass('rqty'))
	{
		process_edit_validation(function (stat) {
                if (stat) {
                    $('.edit_mode').removeClass('edit_mode');
                } else {
                    return false;
                }
            });
     }
});

});

</script>

<script type="text/javascript">
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
				tmpl += '		<div class="jq_alpha_sort_alphalist_vend_head"><h4>'+options.title+'</h4></div>';
				tmpl += '		<div class="jq_alpha_sort_alphalist_char">';
				tmpl += '		<a href="javascript:void(0)" chr="09">0-9</a>';
				for(var i=0;i<chars.length;i++)
					tmpl += '		<a href="javascript:void(0)" chr="'+chars[i].toUpperCase()+'">'+chars[i].toUpperCase()+'</a>';	
				tmpl += '		</div>';
				tmpl += '		<div class="jq_alpha_sort_alphalist_item_wrap">';
				tmpl += '			<div class="jq_alpha_sort_alphalist_itemlist"></div>';
				tmpl += '		</div>';
				tmpl += '	</div>';
				tmpl += '	<div class="jq_alpha_sort_overview">';
				tmpl += '		<div class="jq_alpha_sort_overview_title"><h4>'+options.overview_title+'</h4></div>';
				tmpl += '		<div class="jq_alpha_sort_overview_content"></div>';
				tmpl += '	</div>';
				tmpl += '</div>';
            	ele.prepend(tmpl);
            
            // Events on click on character  
            $('.jq_alpha_sort_alphalist_char a',ele).live('click',function(){
            	options.char_click($(this).attr('chr'),this);
            	
            });
            $('.jq_alpha_sort_alphalist_item_wrap a',ele).live('click',function(){
            	$('.jq_alpha_sort_alphalist_item_wrap .selected a').parent().removeClass('selected');
            	$(this).parent().addClass('selected');
            	options.item_click($(this).attr('vid'),this);
            }); 
            
        });
    };
    $.fn.jqSuAlphaSort.config = {
        title:'Hello',
        click:function(){}
    };
}(jQuery));
</script>

<?php
