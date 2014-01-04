<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/stock_intake.css">
<style>
*{outline: medium}
h2
{
	width:80%;float:left;
}
#grn a:focus{font-weight: bold;font-size: 14px;}
#grn .iqty,#grn .rqty{min-width: 30px !important;border: 2px solid #000 !important}
.rightcont .container
{
	 overflow: hidden;
    padding: 5px;
}
</style>
<div class="container">
	<h2><span class="page_title"></span></h2>
	<span class="cancel_stock"><input type="button" class="button button-caution button-rounded button-tiny cursor form_cancel" value="Cancel" class=""></span>
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
				<table class="datagrid" style="width: 86% !important	">
					<thead>
						<tr>
							<th width="20px">S.no</th>
							<th width="">Product</th>
							<th width="147px">Storage</th>
							<th width="150px" style="text-align: center">Price</th>
							<th width="50px">Invoice Qty</th>
							<th width="50px">Receiving Qty</th>
							<th width="83px">SubTotal</th>
							<th></th>
						</tr>
					</thead>
					<tbody></tbody>
					<tfoot>
						<tr>
							<td colspan="5" align="right" class="recv_qty_wrapper" style="">Total Value of Purchased Items</td>
							<td align="right" ><b id="grn_ttl_rqty">0</b></td>
							<td align="right"><b id="grn_ttl_value">0</b></td>
						</tr>
					</tfoot>
				</table>
				<!--<h4 class="recv_qty_wrapper">Total value of receiving quantity : <span style="font-size:140%;" id="value_receiving"></span></h4>-->
			</div>

			<div class="clearboth">
				<div class="fl_left" style="width:86%">
				 	<div style="padding:10px 10px 0">
						<h3 style="width: 100%">Invoice Details 
							<a style="padding: 5px 0px;font-size: 57%" href="javascript:void(0)" onclick='cloneinvoice()'>[ link another invoice ]</a></h3>
						<table class="datagrid invoice_tab" style="width: 53%;float:left">
							<thead>
								<tr>
									<th>Invoice No</th>
									<th>Date</th>
									<th>Invoice Amount</th>
									<th width="90px">Scanned Copy</th>
								</tr>
							</thead>
							
							<tbody>
								<tr>
									<td><input type="text" name="invno[]" class="inp inv_inp_blk"></td>
									<td><input type="text" name="invdate[]" class="inp inv_inp_blk datepick"></td>
									<td>Rs. <input size=7 type="text" class="inp inv_inp_amount" name="invamount[]"></td>
									<td><input type="file" name="scan_0" class="scan_file"></td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="inv_textarea_wrap">
						<textarea name="remarks" class="remarks"  placeholder="Remarks" cols="61" rows=3></textarea>
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
					<tr class="barcode%bcode% barcodereset edit_prod_row" bcode="%bcode%"  has_serialno="%has_serialno%">
						<td>%sno%
							<input type="checkbox" value="1" class="edit_row">
							</td>
						<td>
	                        <input type="hidden" name="imei%pid%[]" class="imeisvvv imei%prodid%" id="list_imei_%prodid%" value="">
							<span style="font-size:80%"><span class="name">%name%</span>
                                <input type="hidden" name="pid%pid%[]" class="prod_addcheck" value="%prodid%">
                                <input type="hidden" name="prodid[]" class="prod_addcheck" value="%prodid%">
                                
                            </span>
							
							<div class="imei_cont"></div>
							
							<div class="add_barcode_wrap" style="">
								<input type="hidden" style="" class="scan_pbarcode pbcodecls%prodid%" value="" name="pbarcode%pid%[]" />
								<span class="pb_blk view_barcode%prodid%" prodid="%prodid%"></span>
								<span style="font-size:88%;"><a style="color:#B62D64" href="javascript:void(0)" class="bcode_upd" prodid="%prodid%" pid="%pid%" >%update_barcode%</a></span>								
							</div>
							
							<div class="bcode_scanned_status"></div>
							<div class="imei_scanned_status"></div>
						</td>
						
						<td class="po_det_wrap_blk">
							<select class="stkloc" name="storage%pid%[]">==rbs==</select>
							<br /><br />
							<b>PO ID : </b><span><a href="<?php echo site_url('admin/viewpo/%pid%') ?>" target="_blank">%pid%</a></span>
							&nbsp;&nbsp;&nbsp;
							<b>Order Qty : </b>
							<span class="poqty">%qty%<input type="hidden" class="popqty" value="%qty%"></span>
						</td>
						<td style="text-align: center">
							<div class="po_qty_wrap">
								<b>MRP <span class="red_star">*</span> : </b>
										<input type="text" class="inp prod_mrp" name="mrp%pid%[]" size=5 pmrp="%mrp%" dp_price="%dp_price%" value="">
										
										<div class="upd_pmrp_blk" align="center">
											<input type="checkbox" value="1" class="upd_pmrp fl_right" name="upd_pmrp%pid%[]" >
											<span>Update Product &amp; Deal MRP</span>
										</div>
										<div>
											<b style="visibility:%dp_price_inp%">PO DP : </b>
											<span style="visibility:%dp_price_inp%">
												<input type="text" class="inp dp_price" name="dp_price%pid%[]" size=5 readonly="readonly" dp_price="%dp_price%" value="%dp_price%">
													<div class="upd_dp_price_blk" align="center"> 
														<span>Update DP Price</span>
														<input type="checkbox" value="1" class="upd_dp_price" name="upd_dp_price%pid%[]" >
													</div>
											</span>
										</div>
										<div><b>Pur. Price : </b><input type="text" class="inp pprice" name="price%pid%[]" readonly="readonly" size=5 value="%ppur_price%"></div>
							  </div>
						</td>
						
						<td>
							<input type="text" class="inp iqty" name="oqty%pid%[]" id="oqty_%prodid%" size=3 value="0">
						</td>
						
						<td>
	                        <input type="text" class="inp rqty qtychange" name="rqty%pid%[]" size=3 value="0" prodid="%prodid%" pid="%pid%">
	                       	<input type="hidden" value="%prodid%" name="prodid_%prodid%" id="prodid_%prodid%"/>
	                       	<a href="javascript:void(0)" class="view_imei" prodid="%prodid%">view IMEI</a>
	                       	<span class="imeis_nos_view_%prodid%"></span>
                            <!--<span style="font-size:70%"><a href="javascript:void(0)" style="color:red;" onclick='show_add_imei(event,"%prodid%")'>%add_serial%</a></span>-->
                        </td>
                        <td>
							<input type="text" disabled="" class="inp sub_ttl subttl_%prodid%" size=3 value="0" >
						</td>
						<td width="50">
							<span class="addrow_tooltip" title="Click to add new MRP Details"><a href="javascript:void(0)" class="add_product_row button button-tiny cursor">+</a></span>
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
					<td><input type="text" name="invno[]" class="inp inv_inp_blk" class="invno"></td>
					<td><input type="text" name="invdate[]" class="inp inv_inp_blk datepick%dpi%" class="invdate"></td>
					<td>Rs. <input size=7 type="text" class="inp inv_inp_amount" name="invamount[]" class="invamount"></td>
					<td><input type="file" name="scan_%dpi%"></td>
					<td>
						<a href="javascript:void(0)" onclick="$(this).parent().parent().remove()"><img  src="<?php echo base_url().'images/icon_delete13.gif'?>"></a>	
					</td>
				</tr>
			</tbody>
		</table>
	</div>	
</div>

<div id="scanned_summ" >
	<div class="scanned_summ_total"><div id="scan_title">Scanned Product</div>
	<div class="scanned_summ_total_qty">
		<span id="summ_scanned_ttl_qty">0</span> / <span id="summ_ttl_qty">0</span></div>
	</div>
</div>

</div>
	<div id="grn_color_legends" style="display: none;">
		<h4>Color Legends</h4>
		<div class="legend_wrap"><b>UnScanned : </b><span class="unsc"></span></div>
		<div class="legend_wrap"><b>Row Highlight : </b><span class="row_hg"></span></div>
		<div class="legend_wrap"><b>Last Scanned : </b><span class="last_sc"></span></div>
		<div class="legend_wrap"><b>Scanned : </b><span class="sc"></span></div>
	</div>

	<div id="add_barcode_dialog" class="add_barcode_dialog" title="Scan Barcode">
		<input type="hidden" value="" id="abd_pid">
			<input type="text" class="inp" style="width:200px;" id="abd_barcode">
	</div>
	
	<div id="rqty_imei_blk_dlg" title="IMEI Numbers">
		<input type="hidden" value="" id="imei_pid">
		<div class="imei_nos_dlg prodid_%prodid%"></div>
	</div>

<style>

.hidden
{
    display: none;
}
</style>
<SCRIPT>


//var imeis=[];
var pids_selected=[];
var ttl_imei_row_scanned=0;
$('#rqty_imei_blk_dlg').dialog({
			autoOpen:false,
			width:300,
			height:300,
			modal:true,
			open:function(){
				 	var dlgData = $("#rqty_imei_blk_dlg").data("pro_data");
				 	var imeilist_html ='';
				 	var scanned_imei = '';
				 	
						prodid = dlgData.prodid;
						tot_rqty = dlgData.ttl_qty;	
						pid = dlgData.pid;
						ref_trele = dlgData.ref_tr;
						//alert(prodid);
						
					var p_imei_scanned = $("#list_imei_"+prodid,ref_trele).val();
						p_imei_scanned_arr = p_imei_scanned.split(',');
						//imeis.push(p_imei_scanned_arr);
						
						imeilist_html = '<ol>'; 
				    	for(i=0;i<tot_rqty;i++) 
				    	{	
				    			scanned_imei = ((p_imei_scanned_arr[i]==undefined)?"":p_imei_scanned_arr[i]);				    		
								imeilist_html += '<li>';
								imeilist_html += '	<input type="text" class="inp imei_input dlg_imei_inp" placeholder="" name="imei_input_'+prodid+'[]" id="imei_input_'+prodid+'_'+i+'"  onchange="return validate_imeino_input(this,'+prodid+');" value="'+scanned_imei+'">';
								imeilist_html += '	<span class="append_imei_items_'+prodid+'_'+i+'"></span>';
								imeilist_html += '</li>';
						}
						imeilist_html+='</ol>';
				 
					$('.imei_nos_dlg').html(imeilist_html);
					
					$('.dlg_imei_inp:first').focus();
			},
			buttons:{
				'Submit':function(){
					
					var dlgData = $("#rqty_imei_blk_dlg").data("pro_data");
					ref_trele = dlgData.ref_tr;
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
								$(this).addClass('imei_error_inp');
							else
							{
								$(this).attr('scninp_imei','imei_'+scn_imei);
								scanned_imei_nos.push(scn_imei);
								//alert(scanned_imei_nos);
								
							}
						}
					});
					
					if($('.imei_error_inp').length)
					{
						alert("Please enter valid imei/serialnos ");
						return false;
					}
					
					if(scanned_imei_nos.length == tot_rqty) 
					{
						
						$.post(site_url+'/admin/jx_chkimeiforgrn',{'imeino':scanned_imei_nos.join(","),'prodid':prodid},function(resp){
							if(resp.status == 'error')
							{
								alert(resp.error);
								
								if(resp.duplicate != undefined)
								{
									$.each(resp.duplicate,function(a,dup_imei){
										$('input[scninp_imei="imei_'+dup_imei+'"]').addClass('imei_error_inp');
									});
								}
								
								return false;
							}else
							{
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
										alert((imei_sort_result.join(","))+" serial number is repeated");
									}
									else
									{
										$("#imei_pid").val(pid);
										$("#imei_pid").data('prodid',prodid);
										
										// added scanned imeis to hidden input 
										$("#list_imei_"+prodid,ref_trele).val(resp.imeinos);
										
										
										if($("#list_imei_"+prodid,ref_trele).val())
										{
											//ttl_imei_row_scanned++;
											$('.imei'+prodid,ref_trele).parents('tr:first').addClass('bcode_scanned');
											$('.imei_scanned_status',ref_trele).html("IMEI scanned");
											$('.imei_scanned_status',ref_trele).show();
										}else
										{
											$('.imei'+prodid,ref_trele).parents('tr:first').removeClass('bcode_scanned');
										}
											
										//$('#summ_scanned_ttl_qty').text(ttl_imei_row_scanned);	
										$('.pbcodecls'+prodid,ref_trele).parents('tr:first').removeClass('lastScanned').removeClass('unsc');
										
										$('#rqty_imei_blk_dlg').dialog('close');
										
									}
								
							}
						},'json');
					}
				}
			}
	});	
	
	
	var ttl_pbcode_row_scanned=0;
	$('#add_barcode_dialog').dialog({
	modal:true,
	autoOpen:false,
	width:250,
	height:150,
	autoResize:true,
	open:function(){
	dlg = $(this);
	
	},
	buttons:{
		'Cancel' :function(){
		 $(this).dialog('close');
		},
		'Submit':function(){
			
			var ref_trele = $(this).data('ref_tr');
			var b_code=$("#add_barcode_dialog",this);
			var inp_barcode=$("#abd_barcode").val();
			var ttl_qty=$('.rqty',ref_trele).val()
			
				if(isNaN(inp_barcode))
				{
					alert("Please enter valid Barcode");
					return false;
				}			
				if(inp_barcode.length > 10)
				{
					// deprecated for losing logical errors  
					/*
					$.post("<?=site_url("admin/update_barcode")?>",{pid:$('#abd_pid').val(),barcode:$('#abd_barcode').val()},function(resp){
						$("#add_barcode_dialog").prepend();	
					});
					*/
					var chk_prodid = $('#abd_pid').data('prodid');
				
					$('.pbcodecls'+chk_prodid,ref_trele).val($('#abd_barcode').val());
					$("#add_barcode_dialog").hide();
					
					$('.view_barcode'+chk_prodid).html($('#abd_barcode').val());
					
					if($('#abd_barcode').val())
					{
						$('.pbcodecls'+chk_prodid,ref_trele).parents('tr:first').addClass('bcode_scanned');
						$('.bcode_scanned_status',ref_trele).html("Barcode scanned");
						$('.bcode_scanned_status',ref_trele).show();
						$('.pb_blk',ref_trele).show();
						
						//
					}else
					{
						$('.pbcodecls'+chk_prodid,ref_trele).parents('tr:first').removeClass('bcode_scanned');
					}
					if(ttl_qty != 0)
					{
						ttl_pbcode_row_scanned++;
					}
					else
					{
						ttl_pbcode_row_scanned--;
					}
					$('#summ_scanned_ttl_qty').text(ttl_pbcode_row_scanned);		
					$('.pbcodecls'+chk_prodid,ref_trele).parents('tr:first').removeClass('lastScanned').removeClass('unsc');
					
					$(this).dialog('close');	
				}
				else
				{
					alert('barcode should be minimum 10 characters');
				}
		},
	}
});

$('.dlg_imei_inp').live('keypress',function(e){
	if((e.keyCode || e.which) == 13)
	{
		
		if($(this).parent().next().find('.dlg_imei_inp') != undefined)
			$(this).parent().next().find('.dlg_imei_inp').focus();
		return false;
	}
})

$('.show').click(function(){
	var pos=pids_selected;
	//$('.imei_nos').hide();
	//alert(pos);
	if(pos != '')
	{
		loadpo(pos);
		var pos_arr=[];
		$.post('<?=site_url('admin/ven_list_bypo')?>',{pos:pos},function(data){
			$('.page_title').html("Stock Intake for "+data.ven_det.vendor_name);
			
			var pos_arr ="";
			
			$.each(data.po_id_list,function(i,p){
				pos_arr+="<a href='"+site_url+'/admin/viewpo/'+p+"'>"+p+"</a>";
				if(data.po_id_list.length-1 != i)
				{
					pos_arr+=",";	
				}
			});
			
			$('.selected_po_list').html("<span style='float:left'>Selected POs : "+pos_arr+"</span><span style='float:right'>Total PO Value : "+data.ven_det.ttl+"</span>");
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
		
		
	}
	else
	{
		alert('PO not selected');
		return false;
	}
	
});

$('.view_imei').live('click',function(){
	tot_rqty=$('.rqty',$(this).parent()).val();
	 var trele=$(this).parents('tr:first');
	$('#rqty_imei_blk_dlg').data("pro_data",{'prodid': $(this).attr("prodid"),'ttl_qty':tot_rqty,'ref_tr' : trele}).dialog('open');
	/*
	
	var prodid = $(this).attr('prodid')*1;
	var p_imei_scanned = $("#list_imei_"+prodid).val();
		p_imei_scanned_arr = p_imei_scanned.split(',');
		//imeis.push(p_imei_scanned_arr);
		imeilist_html = '<ol>'; 
    	for(i=0;i<tot_rqty;i++) 
    	{	
    			scanned_imei = ((p_imei_scanned_arr[i]==undefined)?"":p_imei_scanned_arr[i]);				    		
				imeilist_html += '<li>';
				imeilist_html += scanned_imei;
				imeilist_html += '</li>';
		}
		imeilist_html+='</ol>';
 
	$('.imeis_nos_view_'+prodid).html(imeilist_html);*/
});

function vendors(ch)
{
	 $('.show').hide();
	 //$(".jq_alpha_sort_alphalist_char a").addClass("jq_alpha_active");
	 
	 $.post(site_url+'/admin/vendors_list',{ch:ch},function(resp){
    	if(resp.status == 'error')
			{
				alert("Vendor not found for selected character");
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

/** function to add more product rows */
$('.add_product_row').live('click',function(e){
	e.preventDefault();

	var trele = $(this).parents('tr:first');
	var trele_html = trele.html();
	var trele_bcode = trele.attr('bcode');
	var trele_has_serialno = trele.attr('has_serialno');
		
	trele.after('<tr class="barcodereset '+trele_bcode+' revscan"  bcode="'+trele_bcode+'"  has_serialno="'+trele_has_serialno+'" >'+trele_html+'</tr>');
	var new_trele = trele.next();
	$('span.name',new_trele).hide();
	$('td:first',new_trele).html('&nbsp;');
	$('td:last a',new_trele).html('x').removeClass('add_product_row').addClass('remove_product_row');
	$('.upd_pmrp',new_trele).attr('checked',false);
	$('.upd_dp_price',new_trele).attr('checked',false);
	var tabindex_cnt = 1;
	reset_tabindex(new_trele,function(row){
		$('input.prod_mrp',row).focus();
	});
	
		
});

$('.remove_product_row').live('click',function(e){
	e.preventDefault();
	$(this).parents('tr:first').fadeOut().remove();
});

function polist_byvendor(id)
{
	 $('.show').hide();
	pids_selected=[];
	$.post(site_url+'/admin/polist_byvendor',{vid:id},function(resp){
		
			var po_list = '';
			po_list +='<div class="ajax_loadresult static_pos">';
			$.each(resp.po_list,function(i,p){
				$('.jq_alpha_sort_overview_title').html("<h4>Purchase Order List for <span>"+p.vendor_name+"</span></h4>")
	 			po_list +='<a class="" href="javascript:void(0)" onclick=pid_container('+p.po_id+')>';
	 			
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
	 window.location.assign(site_url+'/admin/apply_grn');
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
	$('.show').show();
}

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
				alert("Product not found on loaded PO");
				return;
			}
			bcodeRow.addClass("highlightprow").removeClass("bcode_scanned").removeClass("unsc");
			
			if(bcodeRow.length > 1)
			{
				alert(bcodeRow.length+" Products found with same barcode");
			}
			
			bcodeRow.addClass("lastScanned");
			
			setTimeout(function(){
				$(".barcode"+scan_bc).addClass("bcode_scanned");
			},500);
			$(".barcode"+$(this).val()+' .scan_pbarcode').val($(this).val());
			$(document).scrollTop($(".barcode"+$(this).val()).offset().top);
		}
	});

/*
	$('.edit_row').live('change',function(){
		var trele=$(this).parents('tr:first');
		if($(this).attr('checked'))
		{
			if($('.edit_row:checked').length > 1 )
			{
				var e_pmrp=$('.edit_prod_row .prod_mrp').val()*1;
				var e_prqty=$('.edit_prod_row .rqty').val()*1;
				//var status=0;
				if((isNaN(e_pmrp) || e_pmrp == 0))
				{
					if(confirm("MRP and Required Qty need to be updated,Do you want to proceed ?"))
					{
						//trele.addClass('edit_mode');
						// partalliy editted 
						$(this).attr('checked',false);
					}else
					{
						trele.addClass('edit_mode');
						$(this).attr('checked',true);
						//trele.parents('tr:first').removeClass('edit_mode');
					}
				}else
				{
					trele.parents('tr:first').addClass('edit_mode');
				}
			}
		}else
		{
			
		}
		
	});
*/
	
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
	
/*
	$('#apply_grn_form .prod_mrp,#apply_grn_form .rqty').live('blur',function(){
		var trele = $(this).parents('tr:first');
		
		if($('.edit_mode').length > 0)
		{
			var e_pmrp=$('.edit_mode .prod_mrp').val()*1;
			var e_prqty=$('.edit_mode .rqty').val()*1;
			var status=1;
			var mrp_inp_error = 1;
			var rqty_inp_error = 1;
			
			if(isNaN(e_prqty) || e_prqty == 0)
			{
				rqty_inp_error = 0;
				status=0;
			}
			
			if((isNaN(e_pmrp) || e_pmrp == 0))
			{
				mrp_inp_error = 0;
				status=0;
			}
			
			if(status == 0)
			{
				if(confirm("MRP and Required Qty need to be updated,Do you want to proceed ?"))
				{
					$('.edit_mode').removeClass('edit_mode');
				}else
				{
					if(mrp_inp_error == 0)
					{
						$('.edit_mode .prod_mrp').focus();
					}else if(mrp_inp_error == 0)
					{
						$('.edit_mode .rqty').focus();
					}
				}
			}
			else
			{
				$('.edit_mode').removeClass('edit_mode');
			}
		}
		if($('.edit_mode').length == 0)
			trele.addClass('edit_mode');
	});
	*/
	
	$('#apply_grn_form .prod_mrp').live('keyup',function(){
		var pmrp = $(this).attr('pmrp')*1;
		var tot_rqty=$('.rqty').val();
		var trele = $(this).parents('tr:first');
		var upd_mrp_blk = $(this).parent().find('.upd_pmrp_blk');
			if(($(this).attr('pmrp')*1 != $(this).val()*1) && ($(this).val() != "") ){
				$('.upd_pmrp',upd_mrp_blk).attr('checked',true);
				upd_mrp_blk.show();
			}
			else
			{
				$('.upd_pmrp',upd_mrp_blk).attr('checked',false);
				upd_mrp_blk.hide();	
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
		
		var mrp_flag = true;
		$('.prod_mrp',this).each(function(){
			if($(this).val() == "" || $(this).val() == "0" || isNaN($(this).val()))
			{
				mrp_flag=false;
			}
		});

		if(!mrp_flag)
		{
			alert("Please enter valid mrp details");
			$('.prod_mrp').addClass('imei_error_inp');
			return false;
		}
		else
		{
			$('.prod_mrp').removeClass('imei_error_inp');
		}
			
		
		$(".invno,.invdate,.invamount").each(function(){
			if($(this).val().length==0)
			{
				alert("Enter invoice details");
				flag=false;
				return false;
			}
		});
		
		$('input[name="invno[]"],input[name="invdate[]"],input[name="invamount[]"]',this).each(function(){
			if($(this).val().length==0)
			{
				alert("Enter valid invoice details");
				$('.inv_inp_blk').addClass('imei_error_inp');
				flag=false;
				return false;
			}
			else
			{
				$('.inv_inp_blk').removeClass('imei_error_inp');
			}
		});
		
		
		
		if($('#apply_grn_form .unsc').length)
		{
			alert("Please scan products");
			return false;
		}
		
		var remarks = $('.remarks').val();
		if(!remarks)
		{
			alert("Please enter Remarks");
			$('.remarks').addClass('imei_error_inp');
			return false;
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
			//$("#form_submit").attr("disabled",true);
		return flag;
	});
	
	$(".expdate, .datepick").datepicker();
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

$('.prod_mrp').live('change',function(){
	var pmrp = $(this).val();
	var dp_price = $(this).attr('dp_price')*1;
	if(dp_price != 0)
	{
		if(pmrp <= dp_price)
		{
			alert('MRP should be greater than DP price');
			$('.prod_mrp').val('');
		}
	}
});


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
		
		//var trEle = $(this).next().parents('tr:first');
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
	$("#grn_ttl_value").html(r_total.toFixed(2));
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
	$(".datepick"+dpi).datepicker();
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
				update_barcode="add barcode";
			else
			{
				update_barcode="Update barcode";
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
			$(".expdate"+dpe).datepicker();
			dpes.push(".expdate"+dpe);
			$("#grn_pids").append('<input type="hidden" name="poids[]" value="'+poi.po_id+'">');
			added_pos.push(poi.po_id);
		});
		
		//alert(ime);
		$("#grn .datagrid tbody").append(g_rows);
		
		$(dpes.join(", ")).datepicker();
		
		reset_tabindex(0,function(){});
		
		$("#po_loading").hide();
		$("#loadafterselect").show();
		calc_rec_value();
		
		var summ_ttl_qty = 0;
		$('#grn .datagrid tbody tr').each(function(){
				summ_ttl_qty ++;
		});
		$('#summ_ttl_qty').text(summ_ttl_qty);
		
		Tipped.create('.addrow_tooltip',{
			skin: 'black',
			hook: 'topmiddle',
			hideOn: false,
			closeButton: false,
			opacity: .5,
			hideAfter: 200,
		});
	});
}

function reset_tabindex(row,cb)
{
	//$('span.name').parents('tr:first').attr('tabindex',tabindex_cnt);
	$("#grn .datagrid tbody tr").each(function(){
		// pr index 1 loc
		// pr index 1 mrp
		// pr index 1 inv qty 
		// pr index 1 recv qty
		$('span.name',this).attr('tabindex',tabindex_cnt);
		tabindex_cnt++;
		$('.add_barcode_wrap a',this).attr('tabindex',tabindex_cnt);
		tabindex_cnt++;
		
		$('select.stkloc',this).attr('tabindex',tabindex_cnt);
		tabindex_cnt++;
		$('input.prod_mrp',this).attr('tabindex',tabindex_cnt);
		tabindex_cnt++;
		$('input.upd_pmrp',this).attr('tabindex',tabindex_cnt);
		tabindex_cnt++;
		//$('input.dp_price',this).attr('tabindex',tabindex_cnt);
		//tabindex_cnt++;
		//$('input.pprice',this).attr('tabindex',tabindex_cnt);
		//tabindex_cnt++;
		$('input.iqty',this).attr('tabindex',tabindex_cnt);
		tabindex_cnt++;
		$('input.rqty',this).attr('tabindex',tabindex_cnt);
		tabindex_cnt++;
		
	});
	
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

$('.bcode_upd').live('click',function(e){
	var trEle = $(this).parents('tr:first');
	var pid = $(this).attr('pid');
	var prodid = $(this).attr('prodid');
		
		$("#add_imei_dialog").hide();
		$("#add_barcode_dialog").data('ref_tr',trEle).dialog('open');
		$("#abd_barcode").focus().val("");
		$("#abd_pid").val(pid);
		$("#abd_pid").data('prodid',prodid);
		//$(".add_barcode_dialog").dialog('open');
});

function show_add_barcode(pid,prodid)
{
	$("#add_imei_dialog").hide();
	$("#add_barcode_dialog").dialog('open');
	$("#abd_barcode").focus().val("");
	$("#abd_pid").val(pid);
	$("#abd_pid").data('prodid',prodid);
	//$(".add_barcode_dialog").dialog('open');
}

function print_imei_inputs(tot_rqty,prodid) {
	
	var imei_out =' <ol>';
    var c=0;
    for(i=0;i<tot_rqty;i++) {
            c +=1; //<input type="text" value="" id="aid_pid">
            imei_out +='<li>';
			imei_out +='	<input type="text" class="inp imei_input" placeholder="IMEI No" name="imei_input_'+prodid+'[]" id="imei_input_'+prodid+'_'+c+'"" onchange="return validate_imeino_input(this,'+prodid+');" value="">';
			imei_out +='	<span class="imei_remove" onclick="remove_input_imei('+prodid+','+c+');" id="imei_remove_'+prodid+'_'+c+'">&nbsp; X </span>';
			imei_out +='	<span class="append_imei_items_'+prodid+'_'+c+'"></span>';
			imei_out +='</li>';                        
    }
    imei_out+='</ol><!--<span class="imei_add" onclick="add_input_imei('+prodid+');" id="imei_add_'+prodid+'">&nbsp; Add </span>-->';
    return imei_out;
}



$(".datagrid .rqty").live("change",function(){
	
    var tot_rqty = $(this).val();
    var prodid = $(this).attr('prodid');
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
			
			if(tot_rqty)
	    		$('#rqty_imei_blk_dlg').data("pro_data",{'prodid': $(this).attr("prodid"),'ttl_qty':tot_rqty,'pid' : $(this).attr('pid'),'ref_tr' : trele}).dialog('open');
	    	
	    }
	   
});

var imeis_dump=[];

function validate_imeino_input(e,prodid) 
{
    var imei_no=$(e).val();
	    if(isNaN(imei_no)) 
	    {
	        alert("Only Numbers allowed"); $(e).focus().val(""); 
	        return false;
	    }
	    imeis_dump.push(imei_no);
}
$(".jq_alpha_sort_alphalist_itemlist_divwrap a").click(function() {
	alert('1');   	

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

 

$(function(){
	var a='Vendor Po List';
	$('#cus_jq_alpha_sort_wrap').jqSuAlphaSort({title:"Vendors List",'overview_title':a,'char_click':function(chr,ele){ vendors(chr)},'item_click':function(id,ele){ polist_byvendor(id)}});
});

$(function(){
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
   $('.show').hide();
   $('.imei_scanned_status').hide();
   $('.bcode_scanned_status').hide();
   $('.cancel_stock').hide();
   $('.pb_blk').hide();
});
</script>

<?php
