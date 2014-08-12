/**
 * @author Shivaraj <shivaraj@storeking.in>
 * @desc	Member price for a deal update scripts
 * @date	Jun_16_2014
 * @last_modified_on Jun_25_2014
 */
	$('.chg_member_price').live('focus',function(){
		$('.highlight_row').removeClass('highlight_row');
		$(this).parents('tr:first').addClass('highlight_row');
	});
	
	
	
	
	
	// =========< select menus >=================
	$('select[name="sel_menu"]').change(function(){
		var elt=$(this);
		var menuid = elt.val();
		var bid = $('select[name="sel_brandid"]').val();
		var catid = $('select[name="sel_catid"]').val();
		
		
		// brand list
			
			
			if(menuid) {
			
				var tar_elt = $('select[name="sel_brandid"]');
				
				tar_elt.html('<option value="0">Loading...</option>');
				
				$.getJSON(site_url+'/admin/jx_getbrandsbymenu/'+menuid,'',function(resp){
					var brandlist_html = '<option value="0">All</option>';
					if(resp.brand_list.length)
					{
						$.each(resp.brand_list,function(a,b){
							brandlist_html += '<option value="'+b.brandid+'">'+b.name+'</option>';
						});
					}
					else
					{
						tar_elt.html('<option value="0">No item found</option>');
					}
					tar_elt.html(brandlist_html);	
				});
			}
			
			
			
			
			
		// category list
		if(menuid) {
			var cat_elt = $('select[name="sel_catid"]');
			cat_elt.html('<option value="0">Loading...</option>');

			$.getJSON(site_url+'/admin/jx_getcatbybrand/'+bid+'/'+menuid,'',function(resp){
				var catlist_html = '<option value="0">All</option>';
				if(resp.cat_list.length)
				{
					$.each(resp.cat_list,function(a,b){
						catlist_html += '<option value="'+b.id+'">'+b.name+'</option>';
					});
				}
				else
				{
					cat_elt.html('<option value="0">No item found</option>');
				}
				cat_elt.html(catlist_html);	
			});
		}
		
		$(".updt_brand_margin").html('<a href="javascript:void(0)" onclick="mp_percentage_bulkupdate();" class="button button-tiny button-primary">Update Percentage Margin for Brand</a>');
		$(".mp_end_deep_discount").html('<a href="javascript:void(0)" onclick="mp_end_deep_dicount();" class="button button-tiny button-caution">End Active Deep Discount</a>');
		
	}).trigger('change');
	
	
	
	
	
	
	// =========< select brands >=================
	$('select[name="sel_brandid"]').change(function(){
		var menuid = $('select[name="sel_menu"]').val()*1;
		var catid = $('select[name="sel_catid"]').val()*1;
		var bid = $(this).val();
		
		var cat_elt = $('select[name="sel_catid"]');
		
		// update category list
		if(catid==0) {
				cat_elt.html('<option value="0">Loading...</option>');
				$.getJSON(site_url+'/admin/jx_getcatbybrand/'+bid+"/"+menuid,'',function(resp){
					var catlist_html = '<option value="0">All</option>';
						if(resp.cat_list.length)
						{
							$.each(resp.cat_list,function(a,b){
								catlist_html += '<option value="'+b.id+'">'+b.name+'</option>';
							});
						}
						else
						{
							catlist_html += '<option value="0">No item found</option>';
						}
						cat_elt.html(catlist_html);
				});
				
		}

	});
	
	
	
	
	
	// =========< Select Category >=================
	$('select[name="sel_catid"]').change(function() {
		var menuid = $('select[name="sel_menu"]').val()*1;
		var bid = $('select[name="sel_brandid"]').val()*1;
		var catid = $(this).val();
		
		if(bid == 0) {
			// get  brand list
			var tar_elt = $('select[name="sel_brandid"]');

			tar_elt.html('<option value="0">Loading...</option>');

			$.getJSON(site_url+'/admin/jx_getbrandsbymenu/'+menuid+'/'+ catid,'',function(resp){
				var brandlist_html = '<option value="0">All</option>';
				if(resp.brand_list.length)
				{
					$.each(resp.brand_list,function(a,b){
						brandlist_html += '<option value="'+b.brandid+'">'+b.name+'</option>';
					});
				}
				else
				{
					brandlist_html += '<option value="0">No item found</option>';
				}
				tar_elt.html(brandlist_html);	
			});
		}
		
	});
	
	
	
	
	
	
	
	
	
	
	function filter_form_submit() {
		var mid = $('select[name="sel_menu"]').val()*1;
		var bid = $('select[name="sel_brandid"]').val()*1;
		var cid = $('select[name="sel_catid"]').val()*1;
		load_dealsbybrandcat('');
		return false;
	}
	
	
	
	
	function load_dealsbybrandcat(pagi_url)
	{
		var colcount=12;
		
		var mid = $('select[name="sel_menu"]').val()*1;
		var bid = $('select[name="sel_brandid"]').val()*1;
		var cid = $('select[name="sel_catid"]').val()*1;
		
		
		if(mid == 0) {
			//var myDDL = $('select[name="sel_menu"]');	myDDL[0].selectedIndex = 1;
			//mid=$('select[name="sel_menu"]').val()*1;
		}
		var list_deal = $('select[name="sel_list_deal"]').val();
		var search_key = $('.search_key').val();
		
		var show_deep_disc = $('.show_deep_disc:checked').val();
		if(show_deep_disc == undefined)
			show_deep_disc=0;
		
		var show_all_deals = $('select[name="show_all_deals"]').val()*1;
		if(show_all_deals == undefined)
			show_all_deals=0;
			
		//return false;
		//var pub = 1;$('select[name="sel_publish"]').val();'+pub+'/
		
			mid = isNaN(mid)?0:mid;
			bid = isNaN(bid)?0:bid;
			cid = isNaN(cid)?0:cid;
			if(search_key=='')
				search_key = 0;
			
			$('#deal_list tbody').html('<tr><td colspan="'+colcount+'"><div align="center" style="margin:5px;padding:5px;"><img src="'+base_url+'/images/loading_bar.gif'+'"></div></td></tr>');
			$('#deal_list .pagination').html('');
			
			if(pagi_url == '')
				url = site_url+'/admin/memberprice_dealslist_jx/'+mid+'/'+bid+'/'+cid+'/'+list_deal+'/'+search_key+"/"+show_deep_disc+'/'+show_all_deals+'/0';
			else
				url = pagi_url;	
			//print(url);
			//return false;
			
			$.getJSON(url,'',function(resp){
					 var deallist_html = '';
					 if(resp==null) {
						 $('#deal_list tbody').html('<tr><td colspan="'+colcount+'" align="center"><div>Unable to load the content, Please <a href="javascript:void(0)" onclick="return load_dealsbybrandcat(\'\');">Click here to Reload</a>.</div></td></tr>');
						 return false;
					 }
						if(resp.status == 'success')
						{
							
							if(bid==0 && (resp.brand_info != null || resp.brand_info != undefined) ) {
								var brands_arr = resp.brand_info;//(resp.brand_info).split(',');
								
								var tar_elt = $('select[name="sel_brandid"]');
			
								tar_elt.html('<option value="0">Loading...</option>');
								var brandlist_html = '';
								$.each(brands_arr,function(jj,brands) {
									var brand = (brands).split(':');
									//print("Brand:"+brand[0]+"-"+brand[1]);
									brandlist_html += '<option value="'+brand[0]+'">'+brand[1]+'</option>';
								});
								tar_elt.html('<option value="0">All</option>'+brandlist_html);
							}
							
							
							if(cid == 0 && (resp.cat_info != null || resp.cat_info != undefined) )	{
								var cats_list = $('select[name="sel_catid"]');
								cats_list.html('<option value="0">Loading...</option>');
								var catlist_html='';
								$.each(resp.cat_info,function(je,cats) {
									var cat = (cats).split(':');
									catlist_html += '<option value="'+cat[0]+'">'+cat[1]+'</option>';
									//print("Cats:"+cat[0]+"-"+cat[1]);
								});
								cats_list.html('<option value="0">All</option>\n'+catlist_html);
							}
							

							$.each(resp.deal_list,function(a,b){
								// class="'+((b.publish*1 == 1)?'published':'unpublished')+'"
								var slno=(resp.pg*1+a*1+1);
								deallist_html += '<tr sourceable="'+b.is_sourceable+'" class="row_items rowitem_'+slno+'" itemstock="">';
								deallist_html += '	<td>'+slno+'</td>';
								deallist_html += '	<td><b>'+b.pnh_id+' / '+b.id+'</b></td>';
								var product_nm = '--';
								if(b.name != '')
									product_nm=''+b.name+'';
									
								deallist_html += '	<td><a target="_blank" href="'+site_url+'/admin/pnh_deal/'+b.id+'">'+product_nm+'</a>\n\
														<br><a target="_blank" href="'+site_url+'/admin/pnh_products_by_deal/'+b.id+'" style="font-size:10px;color:blue;margin-top:10px;" class="button button-tiny button-flat-primary">View Product</a>\n\
														<br><br>Category : <a style="color:#F61A11" href="'+site_url+'/admin/viewcat/'+b.catid+'" target="_blank"><b>'+b.catname+'</b></a>, &nbsp;&nbsp;  \n\
																Brand : <a style="color:#F61A11" href="'+site_url+'/admin/viewbrand/'+b.brandid+'" target="_blank"><b>'+b.brandname+'</b></a>';
								
								deallist_html += '		<br>Sold in 60 days: <span style="color:#F61A11"><b>'+b.soldin60+'</b></span>, &nbsp; &nbsp;in 30 days: <span style="color:#F61A11"><b>'+b.soldin30+'</b></span>, &nbsp; &nbsp;in 15 days: <span style="color:#F61A11;"><b>'+b.soldin15+'</b></span>, &nbsp; &nbsp;in 7 days: <span style="color:#F61A11;"><b>'+b.soldin7+'</b></span>\n\
													</td>';
								deallist_html += '	<td class="orgprice">'+b.orgprice+'</td>';
								deallist_html += '	<td class="price">'+b.price+'</td>';
								deallist_html += '	<td><span class="stock_msg">&nbsp;</span><br><a href="javascript:void(0)" class="deal_stock" dealid="'+b.id+'" curr_stock="0">&nbsp;&nbsp;</a></td>';

								//print(b.mp_frn_max_qty+"=="+b.mp_mem_max_qty);
				
								var mp_frn_max_qty = 0;
								var mp_mem_max_qty = 0;
								var is_offer_msg = 0;
								var mp_max_allow_qty = 0;
								if(b.mp_frn_max_qty != null)
									mp_frn_max_qty=b.mp_frn_max_qty;
								if(b.mp_mem_max_qty != null)
									mp_mem_max_qty=b.mp_mem_max_qty;
								if(b.mp_max_allow_qty != null)
									mp_max_allow_qty = b.mp_max_allow_qty;
									
								if( b.mp_is_offer == "1") //b.mp_is_offer != undefined && b.mp_is_offer != "0" &&
									is_offer_msg=' checked="checked" ';
								
								var mp_offer_from= '';
								var mp_offer_to= '';
								var msg_update_offer_dt= '';
								if(b.mp_offer_from != null ) {
									mp_offer_from = b.mp_offer_from;//.split(" ")[0];
								}
								if(b.mp_offer_to != null ) {
									mp_offer_to = b.mp_offer_to;//.split(" ")[0];
								}
								
								
								if(mp_offer_to!=0)
									msg_update_offer_dt = '<br><a href="javascript:void(0)" class="small button button-tiny button-flat" onclick="return fn_updt_offer_dts(this);" itemid="'+b.id+'">Update</a>';//,\''+mp_offer_from+'\',\''+mp_offer_to+'\'
								
								if(b.mp_offer_from == '0000-00-00' || b.mp_offer_to == '0000-00-00' ||  b.mp_is_offer != "1")
								{
									mp_offer_from='';
									mp_offer_to='';
									msg_update_offer_dt='';
								}
								var mp_notemsg_action='';var mp_offer_note = '';
								if(b.mp_offer_note !='' && b.mp_offer_note !== null && b.mp_offer_note!=0)
								{
									mp_offer_note = b.mp_offer_note;
									mp_notemsg_action+='<a href="javascript:void(0)" class="button button-tiny button-flat-primary" itemid="'+b.id+'" onclick="return memberprice_note_update(this,\''+b.id+'\')">Update<a>';
								}
								else
								{
									mp_notemsg_action+='<a href="javascript:void(0)" class="button button-tiny button-flat-action" itemid="'+b.id+'" onclick="return memberprice_note_update(this,\''+b.id+'\')">Add<a>';
								}
								
								var percent = get_mp_percentage(b.member_price,b.price);
								var disabled='';
								if(b.validity == '1' && b.is_offer == "1")
									disabled="disabled";
								
								//print(b.id+" shipsin:"+b.shipsin);
								var shipsin='24-48 Hrs';
								if(b.shipsin != undefined && b.shipsin != '')
									shipsin= $.ucfirst($.trim(b.shipsin) );
								
								// onchange="return fn_row_modify(this);" <option value="0" '+((b.shipsin===0)?'selected':'')+'>Choose</option>
								deallist_html += '	<td><form id="mp_cng_form_'+b.id+'" onsubmit="return chg_member_price_det(this)" itemid="'+b.id+'" orgprice="'+b.orgprice+'" price="'+b.price+'">\n\
														<table border="0" cellspacing="0" cellpadding="0">\n\
																<tr><td>Member Price</td><td><small>All Franchise Max To Sell</small></td><td>MP Fran Max</td><td>MP Mem Max</td><td>ShipsIn</td><td>Actions</td></tr>\n\
																<tr><td><input type="text" size="6" tabindex="mp'+a+'" class="chg_member_price" value="'+b.member_price+'" '+disabled+'><span class="small">(Rs)</span>\n\
																	</td>\n\
																	<td><input type="text" size="2" tabindex="mpmaxallowqty'+a+'" class="chg_mp_max_allow_qty" value="'+mp_max_allow_qty+'" '+disabled+'> <span class="small">(Qty)</span></td>\n\
																	<td><input type="text" size="2" tabindex="mpmaxfrnqty'+a+'" class="chg_mp_frn_max_qty" value="'+mp_frn_max_qty+'" '+disabled+'> <span class="small">(Qty)</span></td>\n\
																	<td><input type="text" size="2" tabindex="mpmaxmemqty'+a+'" class="chg_mp_mem_max_qty" value="'+mp_mem_max_qty+'" '+disabled+'> <span class="small">(Qty)</span></td>\n\
																	<td><select tabindex="shipsin'+a+'" class="mp_offer_shipsin" style="width:76px;" value="" '+disabled+'>\n\
																				<option value="24-48 Hrs" '+((shipsin=="24-48 Hrs")?'selected':'')+'>24-48</option>\n\
																				<option value="48-72 Hrs" '+((shipsin=="48-72 Hrs")?'selected':'')+'>48-72</option>\n\
																				<option value="72-96 Hrs" '+((shipsin=="72-96 Hrs")?'selected':'')+'>72-96</option></select> <span class="small">(Hrs)</span></td>\n\
																	<input type="hidden" class="chg_mp_offer_from" value="'+mp_offer_from+'"/><input type="hidden" class="chg_mp_offer_to" value="'+mp_offer_to+'"/>\n\
																	<td>\n\
																		<input type="submit" value="Go" style="width:30px;" '+disabled+'>\n\
																	</td>\n\
																</tr>\n\
																<tr><td>\n\
																		<div class="mp_percent_block"><span class="mp_percent_snip">'+percent+'</span> % Off</div>\n\
																	</td>\n\
																	<td colspan="4"><div class="resp_disp_block"></div></td>\n\
																	<td>\n\
																		<a href="javascript:void(0);" class="button button-tiny button-flat-primary" onclick="return fn_view_price_chng_log(this)">Log</a>\n\
																	</td>\n\
																</tr>\n\
														</table>\n\
														</form>\n\
													</td>';
								
								//<span class="lbl_memberprice_note_'+b.id+'">'+mp_offer_note+'</span>
								deallist_html += '	<td><div style="margin-top:10px;">'+mp_notemsg_action+'</div><input type="hidden" class="memberprice_note_'+b.id+'" value="'+mp_offer_note+'"/></td>\n\
													<td><input type="checkbox" tabindex="isoffer'+a+'" class="chg_mp_is_offer_'+b.id+'" '+is_offer_msg+' value="1" onchange="return fn_chg_isoffer(this);" itemid="'+b.id+'"></td>\n\
													<td><div class="show_dates_blk_'+b.id+' fl_left"><span class="small lbl_mp_offer_from_'+b.id+'">'+mp_offer_from+'</span> <br>- <span class="small lbl_mp_offer_to_'+b.id+'">'+mp_offer_to+'</span></div>\n\
														'+msg_update_offer_dt+'\n\
													</td>';
								/*deallist_html += '	<td><span class="deal_status">'+(b.publish==1?'Published':'Not Published')+'</span><br><a class="upd_deal_pub button button-tiny button-flat-action"  item_id="'+b.id+'" publish="'+b.publish+'" href="javascript:void(0)" style="font-size:10px;color:blue">Change</a> </td>';*/
								
								var sourceablemsg ='';
								var show_in_tab_msg = '';
								if(b.is_sourceable==='1') {
									sourceablemsg='Sourceable';
									show_in_tab_msg = '<span class="stk_log1" onclick="return intab_status_chk(this)">OK / Accept</span>';//'<input type="checkbox" name="chg_show_in_tab" class="chg_show_in_tab" value="1" checked/>';
								}
								else {
									sourceablemsg='Not Sourceable';
									show_in_tab_msg = '<span class="stk_log2" style="font-weight: bold; color:#991E19;" onclick="return intab_status_chk(this)">Check Status</span>';
									// OK / Sold Out'<input type="checkbox" name="chg_show_in_tab" class="chg_show_in_tab" value="0"/>';
								}
									
								deallist_html += '	<td>'+show_in_tab_msg+'</td>';
								deallist_html += '	<td><span class="p_src_status">'+sourceablemsg+'</span>\n\
														<br><a class="upd_prodstatus button button-tiny button-flat-action" href="javascript:void(0);" style="font-size:9px;" onclick="return sourceable_status_cngfn(this);">Change</a>\n\
														<input type="hidden" class="product_id" value="'+b.product_id+'" />';
								deallist_html += '	</td>';
								deallist_html += '</tr>';
							});
							$('.pagination').html(resp.pagination);
							
						}
						else
						{
							deallist_html += '<tr><td colspan="'+colcount+'" align="center">'+resp.message+'</td></tr>';
						}
						
						$('#deal_list tbody').html(deallist_html);
						
						$("#deal_list tbody .deal_stock").dealstock();//{deal_status:1}
						var show_all_deals_msg = '';
						var deals_display_log_count = 0;
						if(show_all_deals == '0') {
							show_all_deals_msg = 'Published & UnPublished';
							deals_display_log_count=resp.deal_ttl;
						}
						else if(show_all_deals == '1') {
							show_all_deals_msg = 'Published';
							deals_display_log_count=resp.ttl_published;
						}
						else if(show_all_deals == '2') {
							show_all_deals_msg = 'UnPublished';
							deals_display_log_count=resp.ttl_unpublished;
						}
						
						var deal_ttl=0; var ttl_published=0; var ttl_unpublished=0;
						if(resp.deal_ttl!=null)
							deal_ttl=resp.deal_ttl;
						if(resp.ttl_published!=null)
							ttl_published=resp.ttl_published;
						if(resp.ttl_unpublished!=null)
							ttl_unpublished=resp.ttl_unpublished;
						
						$('.total_overview').html('<span class="inlog_1">Total deals <b>'+deal_ttl+'</b> - <span class="inlog_act">Published <b>'+ttl_published+'</b></span> and <span class="inlog_dct">Unpublished <b>'+ttl_unpublished+'</b></span> </span>');
						
						$('.deals_display_log').html('');
						if(resp.pg_count_msg != undefined) {
							$('.deals_display_log').html('<span class="inlog_1">'+resp.pg_count_msg+'</span>');
						}
						
						
			});	
	}
	
	
	
	
	
	
	
	$('#deal_list .pagination a').live('click',function(e){
		e.preventDefault();
		load_dealsbybrandcat($(this).attr('href'));
	});
	
	
	
	
	
	
	//================< UPDATE SOURCEABLE STATUS >=======================
	$("#dlg_sourceable_status_cng").dialog({
		modal:true,autoOpen:false,height:"auto",width:350
		//,open:function(event,i) {	}
		,buttons:{
			Submit:function(e,j) {
				var dlg = $(this);
				var trelt = dlg.data("trelt");
				var product_id = dlg.data("product_id");
				var cng_reason = $(".cng_reason",dlg).val();
				if(cng_reason == '') {
					alert("Please enter reason for change");
					return false;
				}
				
				$('.p_src_status',trelt).text("Updating...");
				
				$.post(site_url+'/admin/pnh_upd_prod_status/'+product_id,{remarks:cng_reason},function(resp){
					$('.p_src_status',trelt).text(resp.pstatus);
					dlg.dialog("close");
				},'json');
			}
			,Cancel:function(d,k) {
				$(this).dialog("close");
			}
		}

	});
	
	function sourceable_status_cngfn(e) {
		if(confirm("Are you sure want to change the product status?"))
		{
			var trElt = $(e).closest('tr');
			var product_id = $(".product_id",trElt).val();
	
			$("#dlg_sourceable_status_cng").data("product_id",product_id).data("trelt",trElt).dialog("open").dialog('option', 'title', 'Sourceable Status Change');

		}
		return false;
	}
	
	function chg_member_price_det(elt)
	{
		//$('.chg_member_price').live('change',function(){
		/*var ele = $(this);
			ele.css('border','1px solid #eee');
		var is_perc = 0;	
			if(ele.hasClass('chg_dp_price_perc'))
				is_perc = 1;*/
		var trelt = $(elt).closest("tr");
		var sourceable = trelt.attr("sourceable");
		var itemid = $(elt).attr('itemid')*1;
		var oprc = $(elt).attr('orgprice')*1;
		var price = $(elt).attr('price')*1;
		
		var stock = $(".deal_stock",trelt).attr("curr_stock");
		//$(".stk_snip",trelt).html(stock);
		
		var el_mp = $(".chg_member_price",elt);
		var el_mp_frn_max_qty=$(".chg_mp_frn_max_qty",elt);
		var	el_mp_mem_max_qty = $(".chg_mp_mem_max_qty",elt);
		var	el_mp_max_allow_qty = $(".chg_mp_max_allow_qty",elt);
		
		var	el_mp_is_offer = $(".chg_mp_is_offer_"+itemid);
		var	el_mp_offer_from = $(".chg_mp_offer_from",elt);
		var	el_mp_offer_to = $(".chg_mp_offer_to",elt);
		var	mp_offer_note = $(".memberprice_note_"+itemid);
		var	el_offer_shipin = $(".mp_offer_shipsin",elt);
		var el_percent_block = $(".mp_percent_snip",elt);
		
		var member_price = $.trim(el_mp.val())*1;
		var mp_max_allow_qty = $.trim(el_mp_max_allow_qty.val())*1;
		var mp_frn_max_qty = $.trim(el_mp_frn_max_qty.val())*1;
		var mp_mem_max_qty = $.trim(el_mp_mem_max_qty.val())*1;
		
		var mp_is_offer = el_mp_is_offer.attr("checked") ? 1 : 0;
		var mp_offer_from = ( el_mp_offer_from.val() != '') ? $.trim(el_mp_offer_from.val()) : 0;
		var mp_offer_to = (el_mp_offer_to.val() != '' ) ? $.trim(el_mp_offer_to.val()) : 0;
		var mp_offer_note = (mp_offer_note.val() != '' ) ? $.trim(mp_offer_note.val()) : '';
		var mp_offer_shipsin  = el_offer_shipin.val();
		
		var mp_percent = get_mp_percentage(member_price,price);
		 	if(isNaN(member_price)) {
		 		alert("Invalid Price entered,please check");
		 		return false;
		 	}
		 	if(member_price == 0) {
		 		alert("Price cannot be 0, Re-Check once");
		 		return false;
		 	}
		 	if(mp_frn_max_qty == 0 || mp_frn_max_qty == '') {
		 		alert("MP Fran Max quantity can not be 0 or empty, Re-Check once");
		 		return false;
		 	}
		 	if(mp_mem_max_qty == 0 || mp_mem_max_qty == '') {
		 		alert("MP Mem Max quantity can not be 0 or empty, Re-Check once");
		 		return false;
		 	}
			
			if( sourceable == "0" && mp_max_allow_qty > stock )
			{
				el_mp_max_allow_qty.val(stock);
				mp_max_allow_qty=stock;
				//alert("Current available Stock ( "+stock+" ) is less than given All Franchise Max To Sell( "+mp_max_allow_qty+" ) quantity."); return false;
			}
			
		 	if(mp_max_allow_qty == 0 || mp_max_allow_qty == '') {
		 		alert("MP Max Allowed quantity can not be 0 or empty, Re-Check once");
		 		return false;
		 	}
		 	if(mp_max_allow_qty < mp_frn_max_qty || mp_max_allow_qty < mp_mem_max_qty) {
		 		alert("MP Max Allowed quantity should be greater than MP Fran Max & MP Mem Max quantity");
		 		return false;
		 	}
			
			if(member_price > oprc || member_price > price)
			{
				alert("Member Price "+member_price+" is greater than MRP price "+oprc+" OR Offer Price "+price); return false;
			}
			if(mp_mem_max_qty > mp_frn_max_qty)
			{
				alert("Order quantity("+mp_mem_max_qty+") is greater than Max quantity "+mp_frn_max_qty+""); el_mp_mem_max_qty.val(mp_frn_max_qty); return false;
			}
			/*if(mp_offer_shipsin == "0")
			{
				alert("Please Choose ShipsIn Hours Range");return false;
			}*/
			var getURL = {price:price,member_price:member_price,id:itemid,is_perc:0,mp_frn_max_qty:mp_frn_max_qty,mp_mem_max_qty:mp_mem_max_qty,mp_max_allow_qty:mp_max_allow_qty,mp_is_offer:mp_is_offer,mp_offer_from:mp_offer_from
				,mp_offer_to:mp_offer_to,mp_offer_note:mp_offer_note,mp_offer_shipsin:mp_offer_shipsin};
			//print(getURL); return false;
			// jx_upd_deal_memberprice => deal_memberprice_upd_jx
			$.post(site_url+'/admin/deal_memberprice_upd_jx',getURL,function(resp){
				if(resp.status == 'success')
				{
					el_mp.css('border','2px solid green');
					el_mp_max_allow_qty.css('border','2px solid green');
					el_mp_frn_max_qty.css('border','2px solid green');
					el_mp_mem_max_qty.css('border','2px solid green');
					$(".show_dates_blk_"+itemid,elt).html('<span class="small lbl_mp_offer_from">'+mp_offer_from+'</span> <br>- <span class="small lbl_mp_offer_to">'+mp_offer_to+'</span>');
					el_percent_block.html(mp_percent);
					$(".resp_disp_block",elt).html(""+resp.message);
				}else
				{
					el_mp.css('border','3px solid #cd0000');
					el_mp_max_allow_qty.css('border','3px solid #cd0000');
					el_mp_frn_max_qty.css('border','3px solid #cd0000');
					el_mp_mem_max_qty.css('border','3px solid #cd0000');
					$(".resp_disp_block",elt).html(""+resp.message);
				}
				setTimeout(function() {//print("TEST");
					$(".resp_disp_block",elt).delay("5000").html("");
				},20000);
			},'json');
			return false;
		//});
	}
	
	function get_mp_percentage(member_price,price)
	{
		var percent='0';
		if(member_price > 0) {
			percent = parseFloat( 100 - (parseFloat(member_price)/parseFloat(price) )*100 );
		}
		return parseFloat(percent).toFixed(2);
	}

$(".view_product").live('click',function(e){
	e.preventDefault();

	var item_id=$(this).attr('item_id');
	//var product_id=0;

	$.post(site_url+'/admin/jx_products_by_deal/'+item_id+'/1',{},function(res){
		$('body').append('<a target="_blank" class="red_newtab" href="'+site_url+'/admin/product/'+res.prods[0].product_id+'"></a>');
		$('.red_newtab').trigger('click');
		$('.red_newtab').remove();
	},'json');

});

/*$(".upd_deal_pub").live('click',function(e){
		e.preventDefault();
		var ele =$(this);
		var item_id=$(this).attr('item_id');
		var is_published=1;//$(this).attr('publish');
		var status='';
		if(confirm("Are you sure want to change the status?")) {
			$.post(site_url+'/admin/pnh_pub_deal/'+item_id+'/'+is_published+'/0',{},function(res){
					status = '';
					if(res.status=='success') {
						if(res.is_published==0)	status='not published';
						else status='published';
						ele.attr('publish',res.is_published?1:0);
						$(".deal_status",ele.parent()).html(status);
						ele.parents('tr:first').removeClass('published');
						ele.parents('tr:first').removeClass('unpublished');
						if(res.is_published==0)
						{
							ele.parents('tr:first').addClass('unpublished');
						}else {
							ele.parents('tr:first').addClass('published');
						}
					}else {
						alert(res.error);
					}
			},'json');
		}
});*/

/**
 * Function to submit the form on every changes
 * @param {type} elt
 * @returns {Boolean}
 */
//function fn_row_modify(elt)
//{
//	var form = $(elt).parents("form");
//	form.submit();
//	return false;
//}
//====================< Update validity code starts >========================
$("#dlg_offer_details_blk").dialog({
	modal:true
	,autoOpen:false
	,height:"auto"
	,width:350
	,open:function(event,i) {
		var dlg = $(this);
		var form = dlg.data("form");
		var offer_from=$(".chg_mp_offer_from",$(form) ).val();
		var offer_to=$(".chg_mp_offer_to",$(form) ).val();
		//alert(offer_from+""+offer_to);
		$(".offer_from",dlg ).val(offer_from);
		$(".offer_to",dlg ).val(offer_to);
	}
	,buttons:{
		Submit:function(e,j) {
			var dlg = $(this);
			
			var itemid = dlg.data("itemid");
			var form = dlg.data("form");
			
			var offer_from=$(".offer_from",dlg ).val();
			var offer_to=$(".offer_to",dlg ).val();
			//alert(offer_from+""+offer_to);

			$(".chg_mp_offer_from",$(form)).val(offer_from);
			$(".chg_mp_offer_to",$(form)).val(offer_to);
			
			$(".lbl_mp_offer_from_"+itemid).html(offer_from);
			$(".lbl_mp_offer_to_"+itemid).html(offer_to);
			
			dlg.dialog("close");
			$(form).submit();
		}
		,Cancel:function(d,k) {
			var dlg = $(this);
			var form = dlg.data("form");
			var itemid = dlg.data("itemid");
			
			//alert(".chg_mp_is_offer_1743727115");
			//if( $(".chg_mp_is_offer_"+itemid,$(form) ).is("checked") )
				//alert("1");
			$(".chg_mp_is_offer_"+itemid,$(form) ).prop("checked",false);
			$(this).dialog("close");
			
		}
	}

});

function fn_chg_isoffer(elt)
{
	initDatePicker(elt);
	//var is_offer = offer_from=offer_to=0;

	var itemid = $(elt).attr('itemid')*1;
	var form = $('#mp_cng_form_'+itemid);//$(elt).parents("form");
	//var oprc = $(form).attr('orgprice')*1;var price = $(form).attr('price')*1;
	
	if( $(elt).is(":checked") ) {
		is_offer =1;
		$("#dlg_offer_details_blk").data("itemid",itemid).data("form",form).dialog("open").dialog('option', 'title', 'Change offers validity');
		
	}
	else {
		
		
		if(!confirm("Are you sure you want to deactivate this offer?")) return false;
		is_offer =0;
		
		$(".chg_mp_offer_from",$(form)).val("");
		$(".chg_mp_offer_to",$(form)).val("");
		form.submit();
	}
	return false;
}

function fn_updt_offer_dts(elt)
{
	initDatePicker(elt);
	
	var itemid = $(elt).attr('itemid')*1;
	var form = $('#mp_cng_form_'+itemid);//$(elt).parents("form");
	
	$("#dlg_offer_details_blk").data("itemid",itemid).data("form",form).dialog("open").dialog('option', 'title', 'Update offers validity');
	return false;
}

$(".offer_from").datetimepicker({
		changeMonth:true
		,changeYear:true
//		,showOtherMonths: true,selectOtherMonths: true
		,minDate:new Date()
	//,minTime:'09:00'
		,timepickerScrollbar:false
		,onClose: function( selectedDate ) {
			$(".offer_to").datetimepicker("option","minDate", selectedDate );
			$(".offer_from").datetimepicker( "option", "dateFormat", 'dd/M/yy' ).datetimepicker( "option", "minDate", null );
		}
		//,numberOfMonths: 1,maxDate:1
	});
	/*().keyup(function(e) {
		if(e.keyCode == 8 || e.keyCode == 46) {
			$.datetimepicker._clearDateTime(this);
		}
	});*/
$(".offer_to").datetimepicker({
	changeMonth:true
	,changeYear:true
	,minDate:new Date()
	//,minTime:'09:00'
//		,showOtherMonths: true,selectOtherMonths: true
	,onClose: function( selectedDate ) {
		$(".offer_from").datetimepicker("option", "maxDate", selectedDate );
		$(".offer_to").datetimepicker( "option", "dateFormat", 'dd/M/yy' ).datetimepicker( "option", "minDate", null );
	}
	//,numberOfMonths: 1,maxDate:1
});
	
function initDatePicker(elt)
{
	var dlg=$("#dlg_offer_details_blk");
	var dt_from=$(".offer_from",dlg);
	var dt_to=$(".offer_to",dlg);
	
	//dateFormat:'yy-mm-dd',maxDate:0,dateFormat:'yy-mm-dd',
	/*dt_from.datetimepicker({
		changeMonth:true
		,changeYear:true
//		,showOtherMonths: true,selectOtherMonths: true
		,minDate:new Date()
		,onClose: function( selectedDate ) {
			dt_to.datetimepicker("option","minDate", selectedDate );
			dt_from.datetimepicker( "option", "dateFormat", 'dd/M/yy' ).datetimepicker( "option", "minDate", null );
		}
		//,numberOfMonths: 1,maxDate:1
	});
	/*().keyup(function(e) {
		if(e.keyCode == 8 || e.keyCode == 46) {
			$.datetimepicker._clearDateTime(this);
		}
	});*/
	/*dt_to.datetimepicker({
		changeMonth:true
		,changeYear:true
		,minDate:new Date()
//		,showOtherMonths: true,selectOtherMonths: true
		,onClose: function( selectedDate ) {
			dt_from.datetimepicker("option", "maxDate", selectedDate );
			dt_to.datetimepicker( "option", "dateFormat", 'dd/M/yy' ).datetimepicker( "option", "minDate", null );
		}
		//,numberOfMonths: 1,maxDate:1
	});*/
	/*.keyup(function(e) {
		if(e.keyCode == 8 || e.keyCode == 46) {
			$.datetimepicker._clearDateTime(this);
		}
	});*/
	
	dt_from.datetimepicker( "option", "dateFormat", 'dd/M/yy' ).datepicker( "option", "maxDate", null );
	dt_to.datetimepicker( "option", "dateFormat", 'dd/M/yy' ).datepicker( "option", "maxDate", null );
//	$.datetimepicker._clearDate(".offer_from");
//	$.datetimepicker._clearDate(".offer_to");
}
//====================< Update validity code ends >========================

$(window).on("resize scroll",function() {
	$("#dlg_offer_details_blk,#dlg_view_m_price_chng_log,#mp_bulkupdate_percentage_block").dialog("option","position",["center","center"]); 
});

// =========================< View Log code starts >==========================
$("#dlg_view_m_price_chng_log").dialog({
	modal:true
	,autoOpen:false
	,height:"auto"
	,width:850
	,open:function(event,i) {
		var dlg = $(this);
		//var form = dlg.data("form");
		var itemid = dlg.data("itemid");

		var cont = '';
		$("table tbody",dlg).html('<div class="loading">&nbsp;&nbsp;&nbsp;&nbsp;</div>');
		$.post(site_url+"/admin/deal_memberprice_chnglog_jx/"+itemid+"/0",{},function(resp) {

			if(resp.status == 'success')
			{
				cont += process_loglist(resp);
				
				$("table tbody",dlg).html(cont);
				$(".log_pagination",dlg).html(resp.pagination);
				$(".show_log",dlg).html(resp.pg_msg);
			}
			else
			{
				alert(resp.message);
				dlg.dialog("close");
			}
		},'json');
	}
	,buttons:{
		Close:function(d,k) {
			$(this).dialog("close");
			
		}
	}

});

function fn_view_price_chng_log(elt)
{
	var form = $(elt).parents("form");
	var itemid = $(form).attr('itemid')*1;
	$("#dlg_view_m_price_chng_log").data("itemid",itemid).data("form",form).dialog("open").dialog('option', 'title', 'View Member Price Change Log');//

	return false;
}

$(document).ready(function() {
	load_dealsbybrandcat('');
	//$("#sel_menu").chosen();
	
	//ON CLICK Paginations link
	$(".log_pagination a").live("click",function(e) {
		e.preventDefault();
		var dlg = $("#dlg_view_m_price_chng_log"); 
		$("table tbody",dlg).html('<div class="loading">&nbsp;&nbsp;&nbsp;&nbsp;</div>');
		var cont='';
		$.post($(this).attr("href"),{},function(resp) {
			cont += process_loglist(resp);
			$("table tbody",dlg).html(cont);
			$(".log_pagination",dlg).html(resp.pagination);
			$(".show_log",dlg).html(resp.pg_msg);
		},"json");
		return false;
	});
		
});
function process_loglist(resp)
{
	var cont='';
	$.each(resp.mplogs,function(i,mp) {
		var mp_is_offer="No";
		var is_active="No";
		var offer_from = '';
		var offer_to = '';
		
		if(mp.is_active == "1"){
			is_active="Yes";
		}
		/*if(mp.mp_is_offer == "1"){
			mp_is_offer="Yes";
		}
		if( mp.mp_offer_from != null){
			offer_from = mp.mp_offer_from;
		}
		if( mp.mp_offer_to != null){
			offer_to = mp.mp_offer_to;
		}
		var validity='';
		if(mp.validity == '1') {
			validity="Active Discount";
		}
		else if(mp.validity == '0') {
			if( mp.mp_offer_to == null){
				validity="Not Set";
			}
			else {
				validity="Expired";
			}
		}*/
		
		cont += '<tr>\n\
				<td>'+(++i)+'</td>\n\
				<td>'+mp.new_member_price+' <span class="small">(Rs)</span></td>\n\
				<td>'+mp.mp_max_allow_qty+'</td>\n\
				<td>'+mp.new_mp_frn_max_qty+'</td>\n\
				<td>'+mp.new_mp_mem_max_qty+'</td>\n\
				<td>'+mp.total_sale+'</td>\n\
				<td>'+$.ucfirst(mp.username)+'</td>\n\
				<td>'+mp.created_on+'</td>\n\
				<td>'+is_active+'</td>\n\
			</tr>';
			/*	<td>'+mp_is_offer+'</td>\n\
				<td>'+offer_from+' - '+offer_to+'</td>\n\
				<td>'+validity+'</td>\n\*/
	});
	return cont;
}
// =========================< View Log code ends >==========================

//==============< MEMBER PRICE NOTE  >================================
$("#dlg_memberprice_note_update").dialog({
	modal:true
	,autoOpen:false
	,height:"auto"
	,width:350
	,open:function(event,i) {
		var dlg = $(this);
		//var form = dlg.data("form");
		var itemid = dlg.data("itemid");
		//get
		var memberprice_note = $(".memberprice_note_"+itemid).val();
		//set
		$(".memberprice_note",dlg ).val(memberprice_note);
	}
	,buttons:{
		Update:function(e,j) {
			var dlg = $(this);
			
			var itemid = dlg.data("itemid");
			//var form = dlg.data("form");
			
			var memberprice_note=$(".memberprice_note",dlg ).val();
			if(memberprice_note == '')
			{
				alert("Please enter note");
				return false;
			}
			//set
			$(".memberprice_note_"+itemid).val(memberprice_note);
			
			$.post(site_url+"/admin/memberprice_note_update_jx",{itemid:itemid,memberprice_note:memberprice_note},function(resp) {
				if(resp.status == 'success') {
					//$(".lbl_memberprice_note_"+itemid).html(memberprice_note);
					dlg.dialog("close");
				}
				else
				{
					alert(""+resp.message);
					dlg.dialog("close");
				}
			},'json');
			//form.submit();
		}
		,Close:function(d,k) {
			$(this).dialog("close");
		}
	}
});

function memberprice_note_update(elt,itemid)
{
	//var form = $(elt).parents("form");.data("form",form)
	//var itemid = $(elt).attr('itemid')*1;
	//alert(itemid);
	$("#dlg_memberprice_note_update").data("itemid",itemid).dialog("open").dialog('option', 'title', 'Update member Price Note');//

	return false;
}
//==============< MEMBER PRICE NOTE END >================================

//==============< MEMBER PRICE PECENTAGE BULK UPDATE CODE STARTS >================================
$("#mp_bulkupdate_percentage_block").dialog({
	modal:true
	,autoOpen:false
	,height:"auto"
	,width:350
	,open:function(event,i) {
		//var dlg = $(this);
		//var form = dlg.data("form");
		//var st = dlg.data("st");
		//print(st);
		//get
		//var memberprice_note = $(".memberprice_note_"+itemid).val();
		//set
		//$(".memberprice_note",dlg ).val(memberprice_note);
	}
	,buttons:{
		"Update All Deals":function(e,j) {
			var dlg = $(this);
			var st = dlg.data("st");
			
			var menuid = st.menuid;
			var bid = st.bid;
			var catid = st.catid;
			
			
			var mp_update_percent=$(".mp_update_percent",dlg ).val();
			var mp_offer_shipsin=$(".mp_offer_shipsin",dlg ).val();
			if(mp_update_percent == '')
			{
				alert("Please enter member price percentage");
				return false;
			}
			
			$(".show_update_sts",dlg).html("<small class='loading'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Updating...</small>");
			$.post(site_url+"/admin/deal_memberprice_percent_bulkupdt_jx",{mp_update_percent:mp_update_percent,menuid:menuid,bid:bid,catid:catid,mp_offer_shipsin:mp_offer_shipsin},function(resp) {
				if(resp.status == 'success')
				{
					alert(""+resp.message);
					load_dealsbybrandcat('');
					dlg.dialog("close");
					$(".show_update_sts",dlg).html("");
				}
				else
				{
					alert("Error"+resp.message);
					$(".show_update_sts",dlg).html("");
				}
			},'json');
			//form.submit();
		}
		,Cancel:function(d,k) {
			$(this).dialog("close");
		}
	}
});

function mp_percentage_bulkupdate()//menuid,bid,catid
{
	var menuid = $('select[name="sel_menu"]').val()*1;
	var bid = $('select[name="sel_brandid"]').val()*1;
	var catid = $('select[name="sel_catid"]').val()*1;

	var list_deal = $('select[name="sel_list_deal"]').val();
	var search_key = $('.search_key').val();

	var show_deep_disc = $('.show_deep_disc:checked').val();
	if(show_deep_disc == undefined)
			show_deep_disc=0;
	
	var show_all_deals = $('select[name="show_all_deals"]').val()*1;
	if(show_all_deals == undefined)
		show_all_deals=0;
	
	menuid = isNaN(menuid)?0:menuid;
	bid = isNaN(bid)?0:bid;
	catid = isNaN(catid)?0:catid;
	if(search_key=='')
		search_key = 0;

	if(catid == undefined) catid=0;
	$("#mp_bulkupdate_percentage_block").data("st",{menuid:menuid,bid:bid,catid:catid}).dialog("open").dialog("option",'title',"Bulk MP Margin Update");
}
//==============< MEMBER PRICE PECENTAGE BULK UPDATE CODE ENDS >================================

//==============< END DEEP DISCOUNT CODE STARTS >================================
function mp_end_deep_dicount()//menuid,bid,catid
{
	var menuid = $('select[name="sel_menu"]').val()*1;
	var bid = $('select[name="sel_brandid"]').val()*1;
	var catid = $('select[name="sel_catid"]').val()*1;

	var list_deal = $('select[name="sel_list_deal"]').val();
	var search_key = $('.search_key').val();

	var show_deep_disc = $('.show_deep_disc:checked').val();
	if(show_deep_disc == undefined)
			show_deep_disc=0;
	
	var show_all_deals = $('select[name="show_all_deals"]').val()*1;
	if(show_all_deals == undefined)
		show_all_deals=0;
	
	menuid = isNaN(menuid)?0:menuid;
	bid = isNaN(bid)?0:bid;
	catid = isNaN(catid)?0:catid;
	if(search_key=='')
		search_key = 0;

	if( confirm("Are you sure you want remove Discounts for selected Menu/Brand/Category deals?") )
	{
		
//		if(catid == undefined) catid=0;
//		$("#mp_bulkupdate_percentage_block").data("st",{menuid:menuid,bid:bid,catid:catid}).dialog("open").dialog("option",'title',"Bulk MP Margin Update");
		$.post(site_url+"/admin/memberprice_end_deep_discount",{menuid:menuid,bid:bid,catid:catid,list_deal:list_deal,search_key:search_key,show_deep_disc:show_deep_disc,show_all_deals:show_all_deals},function(resp) {
			if(resp.status == 'success')
			{
				alert(""+resp.message);
				load_dealsbybrandcat('');
				dlg.dialog("close");
				$(".show_update_sts",dlg).html("");
			}
			else
			{
				alert("Error"+resp.message);
				$(".show_update_sts",dlg).html("");
			}
		},'json');
	}
}

function change_deep_discount(elt) {
	
	if($(elt).is(":checked")) {
		$(".updt_brand_margin").html('<a href="javascript:void(0)" onclick="mp_percentage_bulkupdate();" class="button button-tiny button-primary">Update Percentage Margin for Brand</a>');
		$(".mp_end_deep_discount").html('<a href="javascript:void(0)" onclick="mp_end_deep_dicount();" class="button button-tiny button-caution">End Active Deep Discount</a>');
	}
	return false;
}
//==============< END DEEP DISCOUNT CODE ENDS >================================

//========================< Check in tab status starts >================================
function intab_status_chk(e)
{
	var trelt = $(e).closest('tr');
	var curr_stock = $('.deal_stock',trelt).attr("curr_stock");
	if(curr_stock == '0')
	{
		$(e).html('<span class="stk_log2" onclick="return intab_status_chk(this)">SoldOut</span>');
	}
	else {
		$(e).html('<span class="stk_log1" onclick="return intab_status_chk(this)">OK / Accept</span>');
	}
	return false;
}
//========================< Check in tab status >================================