<div class="page_wrap container">
	
	<div class="page_topbar" >
		<h2 class="page_title fl_left">Add PNH Return</h2>	
		<div class="page_action_buttons fl_right" align="right">
			<a class="button button-rounded button-flat-primary button-tiny" href="<?php echo site_url('admin/pnh_invoice_returns/'.$type);?>">List Returns</a>
		</div>
	</div>
	<div style="clear:both">&nbsp;</div>
	<div class="page_content">
		
		<div class="form_block">
			<div class="scan_invoice_form_wrap">
				<form id="scan_invoice_form" action="<?php echo site_url('admin/jx_getinvoiceorditems/'.$type) ?>" method="post">
					<b>Scan or Enter Invoice no</b> : <input type="text" name="scaninp" value="" class="inp" style="width: 200px;">
					<input type="submit" value="Submit" style="padding:3px 10px;">
				</form>
			</div>
			<br />
			<div id="inv_return_scan_list">
				<form action="<?php echo site_url('admin/process_add_pnh_invoice_return') ?>" method="post">
					<input type="hidden" value="" id="inv_for" name="inv_for">
					<table class="datagrid" width="100%">
						<thead>
							<th width="100"><b>Invoice</b></th>	
							<th width="100"><b>Orderno</b></th>
							<th width="100"><b>Item Name</b></th>
							<th width="100"><b>Product List</b> <span style="font-size: 10px;">(Tick for return)</span></th>
							<th width="30"><b>Invoice Qty</b></th>
							<th width="30"><b>Pending Return Qty</b></th>
							<th width="30"><b>Received Qty</b></th>
							<th width="220" ><b>Product Condition &amp; Remarks</b></th>
						</thead>
						<tbody></tbody>
					</table>
					<div style="padding:10px;">
						<div><b>Returned By </b> : <br><input type="text" class="inp" style="width: 300px;" name="return_by" value=""></div>
						<div>
						<b>Remarks</b> : <br>
						<textarea rows="5" cols="35" name="return_remarks"></textarea>
						</div>
					</div>
					<div align="right" style="padding:5px 0px;">
						<input type="submit" value="Submit Return" style="padding:5px 10px;">
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<style>
	.leftcont{display: none;}
	.listview li{border-bottom:1px dotted #ccc;margin-top:5px;padding:5px;}
	.highlight_return td{background: #dfdfdf !important;}
	#ttl_inv_scanned,#ttl_recv_qty_scanned{font-size: 20px;font-weight: normal !important;margin-right: 10px;}
	.return_frm_prodcont{border-collapse:collapse}
	.return_frm_prodcont td{background: #FFF;border:none;}
	.hasError{color: #cd0000;font-weight: bold;font-size: 11px;background:#cd0000;color:#FFF;border-radius:3px;padding:1px 3px;}
</style>
<script type="text/javascript">
var type="<?php echo $type;?>";	
var return_condhtml = '';
var return_reasonhtml = '';
var return_prod_imei_list = new Array();
	$('#scan_invoice_form').submit(function(){
		var invoice_no = $.trim($('input[name="scaninp"]',this).val());
			$('input[name="scaninp"]',this).val(invoice_no);
			if(!invoice_no)
			{
				alert("Please scan invoice no");
				$('input[name="scaninp"]',this).focus();
			}else
			{
			
				if($('.invno_'+invoice_no).length)
				{
					alert("Invoice is already scanned");
					$('input[name="scaninp"]',this).focus();
					return false;	
				}
			
				$.post($(this).attr('action'),$(this).serialize(),function(resp){
					if(resp.invdet.error)
					{
						alert(resp.invdet.error);
						$('input[name="scaninp"]',this).focus();
					}else
					{
							$("#inv_for").val(resp.inv_for);
						
							return_condhtml = '';
							$.each(resp.return_cond,function(a,b){
								return_condhtml += '<option value="'+a+'">'+b+'</option>';
							});
							return_reasonhtml = '';
							$.each(resp.return_reason,function(a,b){
								return_reasonhtml += '<option value="'+a+'">'+b+'</option>';
							});
							
						//order from det
						var order_from_det='';
						if(resp.order_from)
						{ 
							order_from_det='<span style="font-size:11px;">'+resp.cust+'<br>('+resp.order_from+')</span> ';
						}else{
							order_from_det='<span style="font-size:11px;"><a href="'+site_url+'/admin/pnh_franchise/'+resp.franchise_id+'">'+resp.franchise_name+'</a></span>';
						}

						var ttl_scanned = $('#inv_return_scan_list table tbody tr td.invno').length;
						var rowHtml = '';
						var inv_orders = 0;
						$.each(resp.invdet.itemlist,function(a,b){
							inv_orders++;
						});
						
						var i = 0; 
							$.each(resp.invdet.itemlist,function(oid,item){
								rspan = inv_orders;//*item.product_list.length;
								cspan = 0;
								var last_oid = oid;
								
								if(item.imei_list != undefined)
									return_prod_imei_list[oid] = item.imei_list; 
								ttl_scanned = 0;
								is_all_processed = 1;
								
								$.each(item.product_list,function(k,prod1){
									$.each(prod1,function(j,prod){
										
										if((item.quantity*prod.qty - prod.pen_return_qty) != 0)
									 	{
											
											cspan = 0;
											rowHtml += '<tr>';
											if(j==0)
											{
												if(i==0)
												{
													rowHtml += '	<td rowspan="'+rspan+'"><a href="'+(site_url+'/admin/invoice/'+item.invoice_no)+'" target="_blank">'+item.invoice_no+'</a> <br ><a href="'+(site_url+'/admin/trans/'+item.transid)+'" target="_blank">'+item.transid+'</a> <br > <span style="font-size:11px;"><a href="'+site_url+'/admin/pnh_franchise/'+resp.franchise_id+'">'+resp.franchise_name+'</a></span> </td>';
												}
												cspan = 1;
												rspan = 0;
												if(rspan)
												{
													rowHtml += '	<td rowspan="'+rspan+'" >'+item.order_id+'</td>';
													rowHtml += '	<td width="200" rowspan="'+rspan+'" >'+item.name+'</td>';	
												}else
												{
													rowHtml += '	<td >'+item.order_id+'</td>';
													rowHtml += '	<td width="200">'+item.name+'</td>';
												}
												
											}
												
											rowHtml += '	<td width="200"><input type="checkbox" class="rprod_entry" value="'+prod.product_id+'" name="prod_rcvd_pid['+item.invoice_no+']['+item.order_id+']['+prod.product_id+'][]" > <a target="_blank" href="'+site_url+'/admin/product/'+prod.product_id+'">'+prod.product_name+'</a></td>';
											rowHtml += '	<td>'+item.quantity+'</td>';
											rowHtml += '	<td>'+prod.pen_return_qty+'</td>';
											rowHtml += '	<td><input has_bc="'+(prod.has_barcode)+'" pqty="'+((item.quantity*prod.qty - prod.pen_return_qty) )+'" class="inp rqty" type="text" value="0" disabled size="2" name="prod_rcvd_qty['+item.invoice_no+']['+item.order_id+']['+prod.product_id+'][]" ></td>';
											rowHtml += '	<td width="250">';
											rowHtml += '		<ol class="listview" invoice_no="'+item.invoice_no+'" has_bc="'+(prod.has_barcode)+'" is_serial_required="'+prod.is_serial_required+'"  order_id="'+item.order_id+'" product_id="'+prod.product_id+'" style="margin:0px;padding:0px"></ol>';
											rowHtml += '	</td>';
											
											rowHtml += '</tr>';
											is_all_processed = 0; 
										}else
										{
											
										}
										
									});
								});	
								
								
								i++;
							});
							
							if(is_all_processed)
							{
								alert("All Orders in the invoice are already processed for return");
							}else
							{
								$('#inv_return_scan_list table tbody').html(rowHtml);		
							}
							
						
					}
				},'json');
				
			}
		return false; 
	});
	$('input[name="scaninp"]').focus();
	$(function(){
		$('tfoot').hide(); 
		//$('#scan_invoice_form').trigger('submit');
		
		$('.rqty').live('change',function(){
			var rqty = $(this).val()*1;rqty = isNaN(rqty)?0:rqty;
			var pqty = $(this).attr('pqty');
			
			var olEle = $(this).parent().parent().find('ol');
			
			var itm_ord_id = olEle.attr('order_id');
			var itm_prod_id = olEle.attr('product_id');
			var itm_inv_no = olEle.attr('invoice_no');
			var itm_has_bc = olEle.attr('has_bc')*1;
			 
			var itm_has_serial = olEle.attr('is_serial_required')*1;
			 
				if(rqty > pqty){
					alert("return qty is more than invoice product qty");
					rqty = 0;
				}
			
			$(this).val(rqty); 
			  
			var rowHtml = '';
				for(var i=0;i<rqty;i++)
				{
					rowHtml += '<li style="margin:0px;list-style:none;">';
					rowHtml += '	'+(i+1)+') <table cellpadding="2" cellspacing="0" class="return_frm_prodcont">';
					var style_code = '';
					
					if(itm_has_bc == 0)
						style_code = 'style="display:none"';
						
						rowHtml += '					<tr '+style_code+' ><td  >Barcode </td><td><input type="text" class="barcode '+(itm_has_bc?'chk_barcode':'')+' " pid="'+itm_prod_id+'" style="width:200px;font-size:11px;" name="prod_rcvd_pid_bc['+itm_inv_no+']['+itm_ord_id+']['+itm_prod_id+'][]" value="" ><span class="chk_barcode_stat"></span></td></tr>';
					if(itm_has_serial)
					{
						//rowHtml += '	<tr><td>IMEI NO </td><td><input type="text" style="width:200px;font-size:11px;" name="prod_rcvd_pid_imei['+itm_inv_no+']['+itm_ord_id+']['+itm_prod_id+'][]" value="" ></td></tr>';
						var product_imei_list = '';
							$.each(return_prod_imei_list[itm_ord_id][itm_prod_id],function(a,imei_no){
								product_imei_list += '<option value="'+imei_no+'">'+imei_no+'</option>';
							});
							 
						rowHtml += '	<tr><td>IMEI NO </td><td><select style="width:200px;font-size:11px;" name="prod_rcvd_pid_imei['+itm_inv_no+']['+itm_ord_id+']['+itm_prod_id+'][]" >'+product_imei_list+'</select></td></tr>';
						
					}
						
					
					rowHtml += '	<tr><td>Condition </td><td><select class="inp prod_cond" style="width:200px;font-size:11px;" name="prod_rcvd_cond['+itm_inv_no+']['+itm_ord_id+']['+itm_prod_id+'][]" ><option value="">Choose</option>'+return_condhtml+'</select></div>';
					
					rowHtml += '	<tr><td>Remarks </td></td><td><textarea class="inp prod_cond_remarks" style="width:200px;height:60px;font-size:12px;" name="prod_rcvd_remarks['+itm_inv_no+']['+itm_ord_id+']['+itm_prod_id+'][]" ></textarea></td></tr></table>';
					rowHtml += '</div></li>';
				}
				olEle.html(rowHtml);
				 
		});
		
		$('.rprod_entry').live('change',function(){
			$(this).parent().parent().find('.rqty').attr('disabled',!$(this).attr('checked'));
			if($(this).attr('checked'))
			{
				$(this).parent().parent().addClass('highlight_return');
			}else
			{
				
				if($(this).parent().parent().find('.rqty').val())
				{
					if(confirm('Action will reset the recevice qty details, do you want to proceed ? '))
					{
						$(this).parent().parent().find('.rqty').val(0).trigger('change');
						$(this).parent().parent().removeClass('highlight_return');
					}
				}
			}
		});
		
	});
	
	$('.chk_barcode').live('blur',function(){
		var stat_ele = $(this).parent().find('.chk_barcode_stat');
		
		var bc = $(this).val();
		var pid = $(this).attr('pid');
			$.post(site_url+'/admin/jx_chkvalidprodbc/'+type,{bc:bc,pid:pid},function(resp){
				if(resp.status == 'success')
				{
					stat_ele.removeClass('hasError');
					stat_ele.removeClass('hasBCError');
					stat_ele.text('');
				}
				else
				{
					stat_ele.text('Invalid Barcode');
					stat_ele.addClass('hasBCError');
					stat_ele.addClass('hasError');
				}	
			},'json');
			
	});
	
	$('#inv_return_scan_list form').submit(function(){
		if(!$('.rprod_entry:checked',this).length)
		{
			alert("No products selected");
			return false;
		}
		
		 
		
		if($('.hasBCError',this).length)
		{
			alert("Invalid Barcode Entered");
			return false;
		}
		
		
		var no_cond_rmks = 0;	
			$('.prod_cond_remarks:visible',this).each(function(){
				if(!$(this).val().length)
					no_cond_rmks += 1 ;
			});
			
		var confirm_msg = '';	
		if(no_cond_rmks)
		{
			confirm_msg = ' with out any Product Remarks ';
		}	
		
		
		if(!$('input[name="return_by"]').val())
		{
			alert("Please enter Return by and Remarks ");
			return false;
		}
		
		if(!confirm("Are you sure want to create this return "+confirm_msg+' ? '))
		{
			return false;	
		}
		return true;
		
	});
	
</script>