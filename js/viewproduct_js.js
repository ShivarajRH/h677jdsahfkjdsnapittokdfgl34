/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(function(){
	Tipped.create('.addrow_tooltip',{
			skin: 'black',
			hook: 'topmiddle',
			hideOn: false,
			closeButton: false,
			opacity: .5,
			hideAfter: 200,
		});
		$('#upd_new_expiry',this).datepicker( {
	        changeMonth: true,
	        changeYear: true,
	        showButtonPanel: true,
	        dateFormat: 'yy-mm',
	        onClose: function(dateText, inst) { 
	            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
	            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
	            $(this).datepicker('setDate', new Date(year, month, 1));
	        }
	    });
});
$('.btn_correction').live('click',function(){
	
	$('#stock_correction_form').dialog('open');
	$('#exp_dates').datepicker({
	      dateFormat: 'yy-mm-dd'
	});
	//$('.mrp_prod').chosen();
	//$('.loc_wrap').chosen();
	//$('.dest_prodid').chosen();
});
	$('#stock_correction_form').dialog({
		modal:true,
		autoOpen:false,
		width:500,
		height:'auto',
		autoResize:true,
		open:function(){
		//$('.ui-dialog-buttonpane').find('button:contains("Update")').addClass('placeorder_btn');
		dlg = $(this);
	}
});
$("#date_from").datepicker();
$("#date_to").datepicker();
$("#processed_from_date").datepicker();
$("#processed_to_date").datepicker();
$("#avl_from_date").datepicker();
$("#avl_to_date").datepicker();
$('#from_date').datepicker();
$("#to_date").datepicker();

		function reset_producttransfer()
		{
			$('select[name="dest_prod_stockdet"]').html('');
		}
		
		$('select[name="dest_prod_stockdet"]').change(function(){
			$('#dest_prod_stock_ttl').text($('option:selected',this).attr('available_qty'));
		});
		//$('select[name="dest_prod_stockdet"]').chosen();
		$('select[name="dest_prodid"]').change(function(){
			$('select[name="dest_prod_stockdet"]').html("Loading...");
			var dest_pid = $(this).val();
			var s_stk_id = $('select[name="mrp_prod"] option:selected').attr('stk_id');
				$.post(site_url+'/admin/jx_getdestproductstkdet/'+s_stk_id+'/'+dest_pid,{},function(resp){
					
					var dest_stklist = '<option available_qty="0" value="">Choose</option>';
						$.each(resp.stk_list,function(a,b){
							dest_stklist += '<option available_qty="'+(b.available_qty)+'" value="'+b.product_barcode+'_'+b.mrp+'_'+b.location_id+'_'+b.rack_bin_id+'_'+b.expiry_on+'_'+b.stock_id+'">'+b.stk_prod+'</option>';
						});
						dest_stklist += '<option available_qty="0" value="new">New</option>';
						$('select[name="dest_prod_stockdet"]').html(dest_stklist);
						//$('select[name="dest_prod_stockdet"]').chosen();
						
					var dest_newstklochtml = '<option value="">Choose</option>';
						$.each(resp.location,function(a,b){
							dest_newstklochtml += '<option  value="'+b.rack_bin_id+'">'+b.rb_name+'</option>';
						});
						$.each(resp.damage,function(a,b){
							dest_newstklochtml += '<option  value="'+b.id+'">'+b.rb_name+'</option>';
						});
						
						$('select[name="dest_prod_newstk_rbid"]').html(dest_newstklochtml);
						
						$('input[name="dest_prod_newstk_bc"]').val("");
						$('input[name="dest_prod_newstk_mrp"]').val(resp.mrp);
						
				},'json');
				$('select[name="dest_prod_stockdet"]').trigger('change');
		});
		
		$('select[name="dest_prod_stockdet"]').change(function(){
			if($(this).val() == "new")
			{
				$('#new_dest_stockdet').show();
			}else
			{
				$('#new_dest_stockdet').hide();
			}
		});

		$('#upd_stk_prodbc_dlg').dialog({
						autoOpen:false,
						width:400,
						height:200,
						modal:true,
						open:function(){
							stk_id = $(this).data('stk_id');
							$.getJSON(site_url+'/admin/jx_get_stkprobyid/'+stk_id,'',function(resp){
								if(resp.status == 'error')
								{
									alert(resp.error);
								}else
								{
									$('#upd_old_bc').val(resp.stkdet.product_barcode);
									$('#upd_new_bc').val('');
								}
							});
						},
						buttons:{
							'Cancel':function(){
								$('#upd_stk_prodbc_dlg').dialog('close');
							},
							'Update':function(){
								$(".ui-dialog-buttonpane button:contains('Update')").button().button("disable");
								var newbc = $('#upd_new_bc').val();
								
									$.post(site_url+'/admin/jx_upd_stkprodbc','stk_id='+stk_id+'&newbc='+newbc,function(resp){
										if(resp.status == 'error')
										{
											$(".ui-dialog-buttonpane button:contains('Update')").button().button("enable");
											alert(resp.error);
										}else
										{
											alert("Barcode updated successfully");
											location.href = location.href;
										}
									},'json');
								 
							}
						}
				});

		$('.upd_stk_prodbc').click(function(){
			$('#upd_stk_prodbc_dlg').data('stk_id',$(this).attr("stk_id")).dialog('open');
		});

		$('#upd_stk_prodbc_frm').submit(function(){
			$(".ui-dialog-buttonpane button:contains('Update')").button().trigger('click');
			return false;
		});
	
		$('.upd_expiry').click(function(){
			$('#upd_stk_expiry_dlg').data('stk_id',$(this).attr("stk_id")).dialog('open');
		});

		$('#upd_stk_expiry_frm').submit(function(){
			$(".ui-dialog-buttonpane button:contains('Update')").button().trigger('click');
			return false;
		});
		
		$('#upd_stk_expiry_dlg').dialog({
						autoOpen:false,
						width:400,
						height:200,
						modal:true,
						open:function(){
							stk_id = $(this).data('stk_id');
							
							$.getJSON(site_url+'/admin/jx_get_stkprobyid/'+stk_id,'',function(resp){
								if(resp.status == 'error')
								{
									alert(resp.error);
								}else
								{
									$('#upd_old_expiry').val(resp.stkdet.expiry_on);
									$('#upd_new_expiry').val('');
								}
							});
						},
						buttons:{
							'Cancel':function(){
								$('#upd_stk_expiry_dlg').dialog('close');
							},
							'Update':function(){
								$('#upd_stk_expiry_dlg').dialog('close');
								$(".ui-dialog-buttonpane button:contains('Update')").button().button("disable");
								var new_exp = $('#upd_new_expiry').val();
								
									$.post(site_url+'/admin/jx_upd_stkexpiry','stk_id='+stk_id+'&new_exp='+new_exp,function(resp){
										if(resp.status == 'error')
										{
											$(".ui-dialog-buttonpane button:contains('Update')").button().button("enable");
											alert(resp.error);
										}else
										{
											alert("Expiry date updated successfully");
											location.href = location.href;
										}
									},'json');
								 
							}
						}
				});
				
	$('select[name="mrp_prod"]').change(function(){

		$('.new_mrp_bc_block input').val('');
		$('.new_mrp_bc_block').hide();
		
		if($(this).val())
		{
			if($(this).val() == 'new')
			{
				$('input[name="corr"]').attr('aqty',0);
				$('#sc_preview_avail_qty').text("");

				$('.new_mrp_bc_block input').val('');
				$('.new_mrp_bc_block').show();
				
			}else
			{
				avail_qty = $('option:selected',this).attr('avail_qty');
				$('#sc_preview_avail_qty').text(avail_qty+" Available");
				$('input[name="corr"]').attr('aqty',avail_qty);
			}	
			
		}else
		{
			$('input[name="corr"]').attr('aqty',0);
			$('#sc_preview_avail_qty').text("");
			
		}
		
	}).trigger("change");	

	$('input[name="corr"]').keyup(function(){
		
		var inp_corr = $(this).val()*1;
		
		$('.stk_transfer_blk').hide();
		
		if($('input[name="type"]:checked').val() == 0)
		{
			if($(this).val()>$(this).attr('aqty')*1)
			{
				alert("You have only "+$(this).attr('aqty')+" Qty Available");
				$(this).val(0);
			}else
			{
				$('#stk_transfer_cnfrm').show();
				$('#stk_transfer_cnfrm input[name="stk_transfer"]').attr('checked',false).trigger('change');
				var imei_inp_list = '';
					for(var k=0;k<inp_corr;k++)
					{
						imei_inp_list += '<li><input type="text" name="s_imeino[]" value=""  style="width:85%;margin-bottom:3px;"><span class="st_lookup_imei imei_stat_chk"></span></li>';
					}
				$('#stk_transfer_slnos').html(imei_inp_list);
			}
		}
	});
	
	
	$('#stk_transfer_slnos input[name="s_imeino[]"]').live('keyup keypress blur',function(e){
		var code = e.keyCode || e.which; 
			if (code == 13) {               
				e.preventDefault();
				
				var s_pid = $('#stk_correction_frm input[name="pid"]').val();	
				var s_imeino = $(this).val();
					
				if($('.s_imei_'+s_imeino).length)
				{
					alert("IMEI is already scanned in list");
					$(this).select();
				}else
				{
					var imei_loader_ele = $(this).parent().find('span.st_lookup_imei');
						imei_loader_ele.addClass('imei_stat_chk');
						imei_loader_ele.html('Loading');
					
					
						
						$.post(site_url+'/admin/jx_chkimeifortransfer/'+s_pid+'/'+s_imeino,{},function(resp){
							if(resp.status == 'error')
							{
								imei_loader_ele.html(resp.error);
								imei_loader_ele.addClass("imei_stat_error");
								imei_loader_ele.parents('li:first').removeClass('s_imei_'+s_imeino);
							}else
							{
								imei_loader_ele.html('');
								imei_loader_ele.removeClass("imei_stat_chk");
								imei_loader_ele.removeClass("imei_stat_error");
								imei_loader_ele.parents('li:first').addClass('s_imei_'+s_imeino);
								imei_loader_ele.parents('li:first').next().find('input').focus();
								
							}
						},'json');
				}
					
				return false;
			}
	});

	$('input[name="type"]').change(function(){
		$('select[name="loc"]').val('');
		if($(this).val() == 1)
		{
			$('#new_stock_prod').show();
			if($('select[name="mrp_prod"]').val() == 'new')
			{
				$('.new_mrp_bc_block input').val('');
				$('.new_mrp_bc_block').show();
			}
			$('.loc_wrap').attr('disabled',false);
			$('.stk_transfer_blk').hide();
			$('.location_bytype').show();

			$('select[name="mrp_prod"] option').show();
			
		}else
		{

			$('select[name="mrp_prod"] option').each(function(){
				if($(this).attr('avail_qty') != undefined)
				{
					if(parseInt($(this).attr('avail_qty')*1))
						$(this).show();
					else
						$(this).hide();
				}
			});
			
			$('select[name="mrp_prod"]').val('');
			$('#new_stock_prod').hide();	
			$('.new_mrp_bc_block input').val('');
			$('.new_mrp_bc_block').hide();
			$('.loc_wrap').attr('disabled',true);
			$('.location_bytype').hide();
			$('#stk_transfer_cnfrm').show();
			$('#stk_transfer_cnfrm input[name="stk_transfer"]').attr('checked',false).trigger('change');
		}

		//$('select[name="mrp_prod"]').trigger('liszt:updated');
		
	});
	
	$('#stk_transfer_cnfrm input[name="stk_transfer"]').change(function(){
		if($(this).attr('checked'))
		{
			$('#dest_prod_stockdet_blk select').html('');
			$('.stk_transfer_blk').show();	
		}else
		{
			$('.stk_transfer_blk').hide();
			$('#stk_transfer_cnfrm').show();
		}
	});

	$('input[name="type"]:checked').trigger('change');
	
	$('#stk_correction_frm').submit(function(){
		var error_msg = new Array();
		if(!$('select[name="mrp_prod"]',this).val())
		{
			error_msg.push("-Please Choose Stock product");
		}
		if(!($('input[name="corr"]',this).val()*1))
		{
			error_msg.push("-Please Enter Qty");
		}
		if($('input[name="type"]:checked',this).val() == "1")
		{
			if(!$('select[name="loc"]',this).val())
			{
				error_msg.push("-Please Choose Location");
			}
		}else
		{
			if($('input[name="stk_transfer"]').attr('checked'))
			{
				if($('select[name="dest_prodid"]').val() == "")
					error_msg.push("-Please Choose Destination Product To Transfer ");
				if($('select[name="dest_prod_stockdet"]').val() == "")
					error_msg.push("-Please Choose Destination Product Stock Details ");
				/*
				if($('.imei_stat_chk').length != 0 || $('.imei_stat_error').length !=0 )
				{
					error_msg.push("-Please Check entered Serialno Details ");
				}	
				*/
				if($('select[name="dest_prod_stockdet"]').val() == "new")
				{
					if($('select[name="dest_prod_newstk_bc"]').val() == "")
						error_msg.push("-Please Enter Destination Product Barcode");
						
					if($('select[name="dest_prod_newstk_mrp"]').val() == "")
						error_msg.push("-Please Enter Destination Product Stock MRP");
						
					if($('select[name="dest_prod_newstk_rbid"]').val() == "")
						error_msg.push("-Please Choose Destination Product Stock Rackbin");	
				}
					
			}
		}
		if(error_msg.length)
		{
			alert("Unable to submit form \n"+error_msg.join("\n"));
			return false;
		}
		 
	});


	$('#prod_fea_tab').tabs();
	$('#prod_imei_tab').tabs();
	function load_product_stocklog(product_id,sdate,edate,pg)
	{
		$('#stock_log_list tbody').html('<tr><td colspan="6"><div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div></td></tr>');
		$.post(site_url+'/admin/jx_stocklog/'+product_id+'/'+sdate+'/'+edate+'/'+pg+'/25','',function(resp){
			$('#stock_log_list tbody').html(resp.log_data);
			if(resp.ttl*1 > resp.limit*1)
				$('#stock_log_pagination').html(resp.pagi_links).show();
			else
				$('#stock_log_pagination').html("").hide();
			
			$('#stock_log_ttl').html(resp.ttl);	
				
		},'json');
	}
	
	function load_processed_imeino(product_id,sdate,edate,pg)
	{
		$('#stock_imei_processed_list tbody').html('<tr><td colspan="6"><div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div></td></tr>');
		$.post(site_url+'/admin/jx_stockimeilist/'+'processed/'+product_id+'/'+sdate+'/'+edate+'/'+pg+'/25','',function(resp){
			$('#stock_imei_processed_list tbody').html(resp.imei_data);
			if(resp.imei_ttl*1 > resp.limit*1)
				$('#stock_imei_processed_pagination').html(resp.imei_pagi_links).show();
			else
				$('#stock_imei_processed_pagination').html("").hide();
			
			$('.processed_stock_imei_ttl').html(resp.imei_ttl);	
				
		},'json');
	}
	
	function load_available_imeino(product_id,sdate,edate,pg)
	{
		
		$('#stock_imei_available_list tbody').html('<tr><td colspan="6"><div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div></td></tr>');
		$.post(site_url+'/admin/jx_stockimeilist/'+'available/'+product_id+'/'+sdate+'/'+edate+'/'+pg+'/25','',function(resp){
			$('#stock_imei_available_list tbody').html(resp.imei_data);
			if(resp.imei_ttl*1 > resp.limit*1)
				$('#stock_imei_available_pagination').html(resp.imei_pagi_links).show();
			else
				$('#stock_imei_available_pagination').html("").hide();
			
			$('.avl_stock_imei_ttl').html(resp.imei_ttl);	
				
		},'json');
	}
	

	function load_reserved_imeino(product_id,sdate,edate,pg)
	{
		
		$('#stock_imei_available_list tbody').html('<tr><td colspan="6"><div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div></td></tr>');
		$.post(site_url+'/admin/jx_stockimeilist/'+'reserved/'+product_id+'/'+sdate+'/'+edate+'/'+pg+'/25','',function(resp){
			$('#stock_imei_reserved_list tbody').html(resp.imei_data);
			if(resp.imei_ttl*1 > resp.limit*1)
				$('#stock_imei_reserved_pagination').html(resp.imei_pagi_links).show();
			else
				$('#stock_imei_reserved_pagination').html("").hide();
			
			$('.reserved_stock_imei_ttl').html(resp.imei_ttl);	
				
		},'json');
	}
	
	$('#stock_imei_available_pagination .log_pagination a').live('click',function(e){
		e.preventDefault();
		var a_st_date=$('#avl_from_date').val();
		var a_en_date=$('#avl_to_date').val();
		var url_prts = $(this).attr('href').split('/');
			pg = url_prts[url_prts.length-1];
			pg = pg*1;
		
		load_available_imeino(product_id,a_st_date,a_en_date,pg);
	});
	
	$('#stock_imei_processed_pagination .log_pagination a').live('click',function(e){
		e.preventDefault();
		var p_st_date=$('#processed_from_date').val();
		var p_en_date=$('#processed_to_date').val();
		var url_prts = $(this).attr('href').split('/');
			pg = url_prts[url_prts.length-1];
			pg = pg*1;
			
		load_processed_imeino(product_id,p_st_date,p_en_date,pg);
	});
	
	$('#stock_log_pagination .log_pagination a').live('click',function(e){
		e.preventDefault();
		var st_date=$('#from_date').val();
		var en_date=$('#to_date').val();
		var url_prts = $(this).attr('href').split('/');
			pg = url_prts[url_prts.length-1];
			pg = pg*1;
			
		load_product_stocklog(product_id,st_date,en_date,pg);
	});
	
	$('.p_imei_filter_submit').live('click',function(){
		var p_st_date=$('#processed_from_date').val();
		var p_en_date=$('#processed_to_date').val();
		load_processed_imeino(product_id,p_st_date,p_en_date,0);
	});
	$('.a_imei_filter_submit').live('click',function(){
		var a_st_date=$('#avl_from_date').val();
		var a_en_date=$('#avl_to_date').val();
		load_available_imeino(product_id,a_st_date,a_en_date,0);
	});
	
	$('.stocklog_filter_submit').live('click',function(){
		var st_date=$('#from_date').val();
		var en_date=$('#to_date').val();
		load_product_stocklog(product_id,st_date,en_date,0);
	});
	
	function load_ven_log(product_id,pg)
	{
		$('#ven_po_log tbody').html('<tr><td colspan="6"><div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div></td></tr>');
		$.post(site_url+'/admin/jx_prod_purchase_log_det/'+product_id+'/'+pg+'/5','',function(resp){
			$('#ven_po_log tbody').html(resp.log_data);
			if(resp.ttl*1 > resp.limit*1)
				$('#ven_log_pagination').html(resp.pagi_links).show();
			else
				$('#ven_log_pagination').html("").hide();
		},'json');
	}
	
	$('#ven_log_pagination .log_pagination a').live('click',function(e){
		e.preventDefault();
		var url_prts = $(this).attr('href').split('/');
		var pid=$('.log_pagination').attr('prodid');
			pg = url_prts[url_prts.length-1];
			pg = pg*1;
		load_ven_log(product_id,pg);
	});
	
	
	$( document ).ready(function(){
		var p_st_date=$('#processed_from_date').val();
		var p_en_date=$('#processed_to_date').val();
		var a_st_date=$('#avl_from_date').val();
		var a_en_date=$('#avl_to_date').val();
		var st_date=$('#from_date').val();
		var en_date=$('#to_date').val();
		load_product_stocklog(product_id,st_date,en_date,0);
		load_ven_log(product_id,0);
		if(is_serial_required){
			load_available_imeino(product_id,a_st_date,a_en_date,0);
			load_processed_imeino(product_id,p_st_date,p_en_date,0);
			load_reserved_imeino(product_id,p_st_date,p_en_date,0);
		}
		
		total_sales();
	});
	
	$("#stat_frm_to").bind("submit",function(e){
		e.preventDefault();
		total_sales();
	});
	
	function total_sales()
	{
		var prodid ="product_id";
		var start_date= $('#date_from').val();
		var end_date= $('#date_to').val();
		$('.stat_head').html("<h4 style='margin:0px !important'>Total Sales</h4>");
		$('#total_stat .total_stat_view').html('<div align="center" style="margin-top:10px"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>' );
		$.getJSON(site_url+'/admin/jx_product_sales/'+prodid+'/'+start_date+'/'+end_date,'',function(resp){
			if(resp.summary == 0)
			{
				$('#total_stat .total_stat_view').html("<div class='br_alert_wrap' style='padding:40px 0px'>No Sales statisticks found between "+start_date+" and "+end_date+"</div>" );	
			}
			else
			{
				// reformat data ;
				if(resp.date_diff <= 31)
			  	{
			  		var interval = 1000000;
			    }
				else
				{
					var interval = 2500000;
				}
				$('#total_stat .total_stat_view').empty();
				plot2 = $.jqplot('total_stat .total_stat_view', [resp.summary], {
			       	
			       	 seriesDefaults: {
				        showMarker:true,
				        pointLabels: { show:true }
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
				          	label:'Date',
					          labelOptions:{
					            fontFamily:'Arial',
					            fontSize: '14px'
					          },
					          labelRenderer: $.jqplot.CanvasAxisLabelRenderer
				        },
				        yaxis:{
					          min : 0,
							  tickInterval : interval,
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