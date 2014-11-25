<style>
	.subdatagrid th { padding: 4px 0 2px 4px !important;font-size: 11px !important;color: #130C09;background-color: #AEACC2; }
	.content_block .resp_status_msg { padding:5px; color: chocolate; }
	.qvk_imgwrap {
		float: left;
		margin-left: 10px;
		position: relative;
		width: 150px;
	}
</style>
<div class="container">
	<!--<span  style="float: right;margin:10px;"><a	href="<?php echo site_url('admin/list_employee')?>" class="fl_right">List Employee</a></span>-->
	<h1 class="mainbox-title">Partner Stock Transfer</h1>
	<form name="stk_transfer_form" class="stk_transfer_form" enctype="multipart/form-data" method="post" action="" onsubmit="return fn_submit_partner_details(this);">
		<table class="partner_selection">
			<thead>
				<tr>
					<th>Select Partner :<span class="required">*</span></th>
					<td>
					<?php 
					if(!$partner_info)
						echo 'No partners defined.';
					else {
							foreach($partner_info as $i=>$partner) { ?>
							<div style="margin-right:5px; letter-spacing: 3px;font-size: 14px; float: left;">
								<input type="radio" value="<?=$partner['id']; ?>" name="partner_options" id="partner_options<?=$i;?>" partner_name="<?=$partner['name']; ?>">
								<lable for="partner_option<?=$i;?>" style="margin-right:5px;"><?=$partner['name']; ?></lable>
							</div>
				<?php		}
						}
					?>
						<input type="hidden" size="5" name="sel_partner" value="" readonly>
					</td>
				</tr>
			</thead>
		</table>
		
		<div class="content_block hidden" class="">
			<h3 class="head"></h3>
			<div>

				<table colspacing="5" cellpadding="2">
					<tr>
						<td><label style="margin-right: 20px;">Enter Partner product slno :<span class="required">*</span></label></td>
						<td><input type="text" size="20" name="prdt_search" class="prdt_search"></td>
						<td><a href="javascript:void(0);" class="button button-primary button-tiny button-rounded" onclick="fn_search_data(this);" >Go</a></td>
						<td><div class="resp_status_msg"></div></td>
					</tr>
				</table>
				
			</div>
			<table id="deal_list" class="datagrid" cellspacing="5" cellpadding="2" width="100%">
				<thead>
					<tr>
						<!--<th>Slno.</th>-->
						<th>Reference ID</th>
						<th>Image</th>
						<th>Itemid</th>
						<th>Deal Name</th>
						<th>MRP</th>
						<th>Linked Products</th>
						<th>List Products</th>
						<th>Transfer Qty</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody class="deal_list_holder"></tbody>
				
			</table>
			<table width="50%" align="right">
				<tbody>
					<tr><td>
							Expected Transfer Date: <input type="text" name="transfer_exp_date" class="transfer_exp_date" value="<?=date('Y-m-d H:i:s');?>">
						</td>
						<td>
							Transfer Remarks: <textarea name='transfer_remarks' class='transfer_remarks' cols="5" rows="5" style="width:200px; height: 50px;" placeholder="Transfer Remarks"></textarea>
						</td>
						<td>
							<div><br><input type="submit" value="Submit" class="button button-action button-rounded"></div>
						</td>
					</tr>
				</tbody>
			</table>
			
		</div>
	</form>
	<script>
		
		function fn_submit_partner_details(elt)
		{
			
			var sel_partner = $('input[name="sel_partner"]').val();
			
			var trElt=$(elt).find('tr.deal_row');
			var transfer_remarks=$('.transfer_remarks').val();
			var transfer_exp_date=$(".transfer_exp_date").val();
			
			
			var inputdata={};
			

			var error_found=0;
			var msg='';
			$.each( trElt ,function(i,item) {
				inputdata[i]={};
				
				var partner_ref_no= $(item).attr("partner_ref_no"); //$(".partner_ref_no",item).val();
				var itemid=$(item).attr("itemid"); //$(".itemid",item).val();
				var is_group=$(item).attr("is_group"); //parseInt( $(".is_group",item).val() );
				var item_transfer_qty= parseInt($(".item_transfer_qty",item).val() );
				if(item_transfer_qty=='' || item_transfer_qty==0)
				{
					$(".item_transfer_qty",item).focus();
					msg='Item Transfer Quantity cannot be 0 or empty.';
					return error_found=1;
				}
				
				var product_row=$(item).find('tr.product_row'); //$('tr.product_row',item);
				var inputprd_data={};
				$.each(product_row,function(j,prod) {
					
					inputprd_data[j]={};
					//product_id pbarcode mrp  rack_bin_id expiry_on avail_qty
					
					var transfer_qty= $(".transfer_qty", prod ).val();
					
					var product_id= $(prod).attr("product_id");
					var pbarcode= $(prod).attr("pbarcode");
					var mrp= $(prod).attr("mrp");
					var rack_bin_id= $(prod).attr("rack_bin_id");
					var expiry_on= $(prod).attr("expiry_on");
					var avail_qty= $(prod).attr("avail_qty");
					
					if(transfer_qty=='') { //|| transfer_qty==0
						msg='Transfer Quantity cannot be empty.';
						$(".transfer_qty", prod ).focus().css();
						return error_found=1;
					}

				});
				
				inputdata[i]['products']=inputprd_data;
						
				i++;
				
			});
			
			if(error_found==1) {
				alert('Error: '+msg);
				return false;
			}
			
			//======== Global level validations ========
			if( (trElt).length == 0 )
			{
				alert("No products selected to transfer");
				return false;
			}
			
			if(transfer_remarks=='')
			{
				alert("Please enter remarks.");
				return false;
			}
			//======== Global level validations End ========
			
			$.post(site_url+"/admin/partner_transfer_jx/"+sel_partner+"/",$(".stk_transfer_form").serialize(),function(resp) {
				if(resp.status == 'success') {
					alert(resp.message);
					//$('input[name="sel_partner"]').trigger('change');
					window.location=$(location).attr("href");
				}
				else {
					alert("Error: "+resp.message);
				}
				return false;
			},'json');
			
			return false;
		}
		
		function fn_search_data(elt) {
			$(".content_block .resp_status_msg").html("");
			
			var stk_transfer_form = $(".stk_transfer_form");
		
			var sel_partner = $('input[name="sel_partner"]').val();
			
			var srchInp=$(".prdt_search",stk_transfer_form);
			
			var prdt_search = srchInp.val();
			
			if(prdt_search=='') {
				$(".content_block .resp_status_msg").html("Enter search data.");
				return false;
			}
			
			if(!chk_prdt_already_added(prdt_search))
				return false;
			
			var url=site_url+"/admin/get_partner_product_jx/"+prdt_search;
			//alert(url);	return false;
			$.post(url,{search_data:prdt_search},function(resp) {
				var html_content='';
				if(resp.status == 'success')
				{
					var det=resp.item_det;
					html_content += '<tr class="deal_row" partner_ref_no="'+det.partner_ref_no+'" itemid="'+det.itemid+'" is_group="'+det.is_group+'" num_prod_lnk="'+det.num_prod_lnk+'">\n\
										<td>'+det.partner_ref_no+'</td>\n\
										<td>\n\
											<div class="qvk_imgwrap">\n\
												<img height="100" src="'+det.image_url+'" alt="Deal Image"/>\n\
											</div>\n\
										</td>\n\
										<td><a href="'+site_url+'/admin/deal/'+det.itemid+'" target="_blank">'+det.itemid+'</a></td>\n\
										<td><a href="'+site_url+'/admin/deal/'+det.itemid+'" target="_blank">'+det.name+'</a></td>\n\
										<td>'+det.mrp+'</td>\n\
										<td>'+det.num_prod_lnk+'</td>\n\
										<td>\n\
											<input type="hidden" name="partner_ref_no['+det.itemid+']" class="partner_ref_no" value="'+det.partner_ref_no+'" />\n\
											<input type="hidden" name="itemid['+det.itemid+']" class="itemid" value="'+det.itemid+'" />\n\
										';
										var ttl_stk=0;
										if( det.prdts_stk != undefined )
										{	
											html_content += '<div class="sub_content_block">\n\
												<table width="100%">\n\
													<tbody>';
														
											var no_stock_prd=0;
											$.each(det.prdts_stk,function(itemid,prdstk_arr) {
//												print(prdstk_arr);
												
												$.each(prdstk_arr,function(product_id,prdstk) {
													
														html_content += '<tr class="product_row_head"><td>\n\
																			<div style="margin-bottom: 8px;"><a href="'+site_url+'/admin/product/'+prdstk.pid+'" target="_blank">'+prdstk.pid+', '+prdstk.product_name+'</a> * <span class="pqty">'+prdstk.qty+'</span> qty</div>\n\
																				<table width="100%" class="subdatagrid">\n\
																					<tr>\n\
																							<th>#</th>\n\
																							<th>Barcode</th>\n\
																							<th>MRP</th>\n\
																							<th>Rackbin</th>\n\
																							<th>Expiry</th>\n\
																							<th>Avail Qty</th>\n\
																							<th>Alloted Qty <small>(Suggest:<span class="suggest_qty">0</span>)</small></th>\n\
																					</tr>';
															
															if(prdstk.prod_loc_list != '')
															{
																$.each(prdstk.prod_loc_list,function(k,prd) {

																	html_content += '<tr class="product_row"\n\
																									product_id="'+prdstk.pid+'" \n\
																									pbarcode="'+prd.pbarcode+'" mrp="'+prd.mrp+'" \n\
																									rack_bin_id="'+prd.rack_bin_id+'" \n\
																									expiry_on="'+prd.expiry_on+'" avail_qty="'+prd.avail_stk+'">\n\
																						<td>'+(++k)+'</td>\n\
																						<td>'+prd.pbarcode+'</td>\n\
																						<td>'+prd.mrp+'</td>\n\
																						<td>'+prd.rbname+'</td>\n\
																						<td>'+prd.expiry_on+'</td>\n\
																						<td>'+prd.avail_stk+'</td>\n\
																						<td><input type="text" name="transfer_qty['+itemid+']['+prdstk.pid+']['+prd.stock_id+']" class="transfer_qty" value="" size="2" onchange="return fn_req_qty_change(this);" ></td>\n\
																					</tr>';
																});
															}
															else {
																++no_stock_prd;
																html_content += '<tr>\n\
																					<td>No Stock</td>\n\
																				</tr>';

															}
															
														html_content += '</tbody></table>\n\
																	</td></tr>';

														ttl_stk +=parseInt(prdstk.ttl_stk);
													//}
													
												});

											});
											
											html_content += '</tbody>\n\
													</table>\n\
												</div>\n\
												<input type="hidden" name="ttl_stk" class="ttl_stk" value="'+ttl_stk+'" size="2" /> ';
										}
										else {
											html_content += 'No products linked';
										}
								html_content += '<td><input type="text" size="2" name="item_transfer_qty['+det.itemid+']" class="item_transfer_qty" value="0" onchange="return fn_item_req_qty_change(this);"></td>\n\
											</td>\n\
										<td><a href="javascript:void(0);" class="button button-caution button-rounded button-tiny" onclick="fn_removeitem(this);">Remove</a></td>\n\
									</tr>';
						if(no_stock_prd>0)
						{
							alert("Cannot process this deal, stock not available for all products.");
							//return false;
						}
						// Clear input box
						srchInp.val("");
				}
				else {
					$(".content_block .resp_status_msg").html(resp.message);
				}
				$("#deal_list tbody.deal_list_holder").append(html_content);
			},'json');
			
			return false;
		}
		
		function fn_removeitem(elt) {
			var trElt=$(elt).closest('tr');
			trElt.remove();
		}
		
		function chk_prdt_already_added(prdt_search) {
			var tbodyElt = $("#deal_list tbody.deal_list_holder");
			var trElt =    $("tr.deal_row",tbodyElt);
			
			if(tbodyElt.html() == '')
				return true;
			
			var status=1;
			$.each( trElt,function(i,itm) {
				var partner_ref_no= $(itm).attr('partner_ref_no'); 
				
				var itemid= $(itm).attr('itemid'); 
				
				//alert(partner_ref_no+"==="+prdt_search+"===="+itemid );
				
				if(partner_ref_no == prdt_search ) {
					$(".content_block .resp_status_msg").html("Item already added.");
					status=0;
				}
				if(itemid == prdt_search ) {
					$(".content_block .resp_status_msg").html("Item already added.");
					status=0;
				}
			});
			
			if(status==1) {
				return true;
			}
			else {
				return false;
			}
		}
		
		function fn_req_qty_change(elt) {
			var trElt=$(elt).closest("tr");
			var avail_qty = $(trElt).attr('avail_qty');
			var req_qty = parseInt( $(elt).val() );
			if(req_qty > avail_qty)
			{
				alert("Transfer quantity cannot greater than available quantity.");
				$(elt).val(avail_qty);
				return false;
			}
		}
		
		
		function fn_item_req_qty_change(elt) {
			var item_req_qty = parseInt( $(elt).val() );
			
			
			var trEltHd=$(elt).closest(".sub_content_block");
			
			var pqty=$(".pqty",trEltHd);
			$.each(pqty,function(gg,qty) {
				print( $(qty).html() );
			});
			//var pqty = pqty.html();
			
			var suggest_qty=item_req_qty * pqty;
			
			
			print(item_req_qty+"==="+pqty+"===="+suggest_qty);
			
			$('.suggest_qty',trEltHd).html(suggest_qty);
			
		}
		
		$(document).ready(function() {
			
			$('input[name="partner_options"]').change(function() {
				var partnerid=$(this).val();
				var partnername=$(this).attr('partner_name');
				
				$(".content_block").show();
				var head = "";
				head = partnername+" Stock Transfer";
				
				$(".content_block h3").html(head);
				
				var srchInp=$(".prdt_search"); srchInp.focus();
				
				$('input[name="sel_partner"]').val(partnerid);
				
				// Disable to change further again
				$('input[name="partner_options"]').hide().parents('table.partner_selection').hide();//.attr('disabled',true);
				
			});
			
			// Datetime Picker
			$(".transfer_exp_date").datetimepicker( );
			$(".transfer_exp_date").datetimepicker( "option", "dateFormat", 'dd/M/yy' );
		});
	
	</script>