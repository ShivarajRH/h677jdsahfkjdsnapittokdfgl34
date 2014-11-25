/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

	$('#scan_barcode').focus();
	
	if(has_imei_scan>0) {
	 	$('#scanbyimei').show();
	}else{
	 	$('#scanbyimei').hide();
	}
	$(function(){
		$(".nobarcode").click(function(){
			p=$(this).parents(".bars").get(0);
			validate_item($(p));
		});
		$("#scan_barcode").keyup(function(e){
			if(e.which==13)
				validate_barcode();
		});
		
		$("#scan_imeino").keyup(function(e){
			if(e.which==13)
			validate_imeino();
		});	
		
	});
	
	///==========================
	function validate_barcode()
	{
		if($("#scan_barcode").val().length==0)
		{
			alert("Enter barcode");
			return;
		}
		var scanbc = $.trim($("#scan_barcode").val());
		
		
		var sel_bcstk_ele = $(".pbcode_"+scanbc);
		//print(".pbcode_"+scanbc);
		if(sel_bcstk_ele.length > 1)
		{
			$('#mutiple_mrp_barcodes').data('m_bc',scanbc).dialog("open");
			return false;
		}
		
//		var tr=sel_bcstk_ele.closest("tr.prod_scan");
		var tr=sel_bcstk_ele.parents('tr:eq(1)');
//		var tr=sel_bcstk_ele.closest('.bars');
		
		
		$("#scan_barcode").val("");
		
		if(tr.length==0)
		{
			alert("Warning: The product is not in transfer scan list");
			return false;
		}
		
		needed=parseInt($(".qty",tr).html());
		
		have=parseInt($(".have",tr).html());
		
		if(needed<=have)
		{
			alert("Required qty is already scanned");
			return;
		}
		
		
		var cur_sel = sel_bcstk_ele.val()*1;
		//alert(cur_sel);
		var ttl_bc_stk = sel_bcstk_ele.attr('stk');
		if(ttl_bc_stk < cur_sel+1)
		{
			alert("No Stock Available for this product");
			return false;
		}

		if((ttl_bc_stk-(cur_sel+1)) == 0)
		{
			sel_bcstk_ele.removeClass('scan_bybc');
		}


		var ttl_stkgrp_items = $('.scan_proditem',sel_bcstk_ele.parent()).length;
		var ttl_scanbybc = $('.scan_bybc',sel_bcstk_ele.parent()).length;

		if(ttl_scanbybc)
		{
			$('.prod_stkselprev',sel_bcstk_ele.parent()).attr('disabled',true).addClass('disabled');
		}else
		{
			$('.prod_stkselprev',sel_bcstk_ele.parent()).attr('disabled',false).removeClass('disabled');
		}

			sel_bcstk_ele.val(cur_sel+1);
			
			sel_bcstk_ele.addClass("sel_stk");

			
		var sel_bcstk_preview_ele = sel_bcstk_ele.parent().find('.prod_stkselprev');
		
		
		
		sel_bcstk_preview_ele.val(sel_bcstk_preview_ele.val()*1 + 1); 

		
		$('#mutiple_mrp_barcodes').dialog("close");		 
		
		$(document).scrollTop(tr.offset().top);		 
		
		validate_item(tr);
	}
	
	//var done_pids=[];
	
	function validate_item(tr)
	{
		needed=parseInt($(".qty",tr).html());
		have=parseInt($(".have",tr).html());
		if(needed<=have)
		{
			alert("Required qty is already scanned");
			return;
		}

		
		/*$('.sel_stk',tr).each(function(){

			if($(this).attr('consider_for_refund')*1)
			{
				var ordmrp = $(this).attr('ordmrp')*1;
				var disc = $(this).attr('disc')*1;
				var mrp = $(this).attr('mrp')*1;
				var qty = $(this).val()*1;
				var paidamt = ordmrp-disc;
				var newamt = mrp-(mrp*disc/ordmrp);
				refund_amt += Math.round((paidamt-newamt)*qty*10000)/10000; 
			}

		}); */

		/*if(refund_amt < 0){
			$('.refund_amt',tr).text(refund_amt).css('color','red');
			refund_alert = 1;
		}else
			$('.refund_amt',tr).text(refund_amt).css('color','#000');
		*/
		have=have+1;
		
		$(".have",tr).html(have);
		
		scanned_highlgt(tr);

		show_ttl_summary();

		var itemid=tr.attr("itemid");
		var ttl_item_row_count=$('tr.itemid_'+itemid).length;
		
		
		if(ttl_item_row_count==1)
		{
			if(have==needed)
			{
				tr.removeClass("partial");
				$(".status",tr).html("OK");
				//done_pids.push($(".pid",tr).val());
				tr.addClass("done");
			}
			else if(have)
			{
				tr.addClass("partial");
			}
		}
		else
		{
			var ttl_items_count=0;
			$('tr.itemid_'+itemid).each(function(i,row) {
				var scanned=parseInt($('.have',this).html());
				var required=parseInt($('.qty',this).html());
				var link_qty=parseInt($('.prod_req_qty',this).html());
				
			
				$(this).removeClass("partial");
				$(this).removeClass("done");
				$(this).removeClass("fully_processed");
				if(scanned%link_qty != 0)
				{
					$(this).addClass("partial");
				}
				else if(scanned==required)
				{
					$(this).addClass("fully_processed");
				}
				else
				{
					$(this).addClass("partial");
				}
				
				
			});
			//======
			
			
			if(!$('tr.itemid_'+itemid+'.partial').length && $('tr.itemid_'+itemid+'.fully_processed').length==ttl_item_row_count)
			{

				$('tr.itemid_'+itemid).removeClass("fully_processed").addClass("done");
			}
			
		}
		
		checkall();
	}
	
	function validate_remove_item(tr)
	{
		needed=parseInt($(".qty",tr).html());
		have=parseInt($(".have",tr).html());
		if(needed<=have)
		{
			alert("Required qty is already scanned");
			return;
		}

		have=have+1;
		
		$(".have",tr).html(have);
		
		scanned_highlgt(tr);

		show_ttl_summary();

		var itemid=tr.attr("itemid");
		var ttl_item_row_count=$('tr.itemid_'+itemid).length;

		if(ttl_item_row_count==1)
		{
			if(have==needed)
			{
				tr.removeClass("partial");
				$(".status",tr).html("OK");
				//done_pids.push($(".pid",tr).val());
				tr.addClass("done");
			}
			else if(have)
			{
				tr.addClass("partial");
			}
		}
		else
		{
			
			$('tr.itemid_'+itemid).each(function(i,row) {
				var scanned=parseInt($('.have',this).html());
				var required=parseInt($('.qty',this).html());
				var link_qty=parseInt($('.prod_req_qty',this).html());
				
				$('tr.itemid_'+itemid).removeClass("partial");
				$('tr.itemid_'+itemid).removeClass("done");
				$('tr.itemid_'+itemid).removeClass("fully_processed");
				if(scanned%link_qty != 0)
				{
					$('tr.itemid_'+itemid).addClass("partial");
				}
				else
				{
					$('tr.itemid_'+itemid).addClass("fully_processed");
				}
			});
			
			if(!$('tr.itemid_'+itemid+'.partial').length && $('tr.itemid_'+itemid+'.fully_processed').length==ttl_item_row_count)
			{
				$('tr.itemid_'+itemid).removeClass("fully_processed").addClass("done");
			}
			
		}
		
		checkall();
	}
	
	function scanned_highlgt(tr)
	{
		//var q=tr;
		tr.addClass("scanned");
		window.setTimeout(function(){
			tr.removeClass("scanned");
		},1000);
	}
	
	
	$('#mutiple_mrp_barcodes').dialog({
		width:800,
		height:'400',
		autoOpen:false,
		modal:true,
		open:function(){
				var mbc = $(this).data('m_bc');

				var mrp_option_list = '';
				$('.pbcode_'+mbc).each(function(){

					if(!$(this).parents('tr.done').length)
					{
						//var l_order_id = $(this).attr("order_id");
						var l_transfer_id = $(this).attr("transfer_id");
						var l_itemid = $(this).attr("itemid");
						var l_mrp = parseFloat($(this).attr("mrp")); 
						var l_rbid = $(this).attr("rb_id");
						var l_stk_id = $(this).attr("stk_info_id");
						var l_expiry_on = $(this).attr("expiry_on");

						var l_stk = $(this).attr("stk");
						var l_stk_reserv = $(this).attr("reserv_qty")*1;
						var l_stk_sel = $(this).val()*1;
							l_stk_sel = !isNaN(l_stk_sel)?l_stk_sel:0;

						if(l_stk*1 > l_stk_sel)
							mrp_option_list += '<tr><td><b>'+$(this).attr("dealname")+'</b></td><td><b>'+$(this).attr("mrp")+'</b></td><td><b>'+$(this).attr("rb_name")+'</b></td><td><b>'+($(this).attr("reserv_qty")*1)+'</b></td><td><b>'+l_expiry_on+'</b></td>\n\
													<td><input type="radio" value="'+mbc+'_'+l_mrp+'_'+l_rbid+'_'+l_stk_id+'_'+l_itemid+'_'+l_transfer_id+'_'+l_expiry_on+'" name="sel_bc_mrp" /></td></tr>';	
					}



				});

				if(mrp_option_list != '')
				{

					$('tbody',this).html(mrp_option_list);
					if($('tbody tr',this).length == 1)
					{
						$('tbody tr input[type="radio"]:first',this).attr("checked",true);
						$('tbody tr input[name="sel_bc_mrp"]',this).trigger('click');	
					}else
					{
						$('tbody tr input[type="radio"]:checked',this).attr("checked",true);
					}
				}else
				{
					$(this).dialog('close');
				}
			},
			buttons:{
				'Submit':function(){
					if($('input[name="sel_bc_mrp"]:checked').length)
					{
						validate_barcode();	
					}else
					{
						alert("Please choose mrp product"); 
					}
				}
			},
			close: function()
			{
				$('#scan_barcode').val('');
			}
	});


	$('input[name="sel_bc_mrp"]').live('click',function(){
//		print($(this).val());
		$('#scan_barcode').val($(this).val());
	});
	
	function show_ttl_summary()
	{
		$('#summ_ttl_scanned_prod').text(0);
		var ttl_prods_scanned = 0;
		var ttl_qty_scan = 0; 

		$('#summ_ttl_scanned_prod').text((ttl_prods_scanned)+'/'+($('.prod_scan').length));

		$('.prod_scan').each(function(){
			var qty_scan = 0;
			$('.prod_stkselprev',this).each(function(){
				qty_scan += $(this).val()*1;
			});
			
//			$('.scan_proditem',this).each(function(){
//				qty_scan += $(this).val()*1;
//			});
			
			if(qty_scan)
			{
				ttl_prods_scanned++;
				$('#summ_ttl_scanned_prod').text((ttl_prods_scanned)+'/'+($('.prod_scan').length));
			}

			ttl_qty_scan+= qty_scan;
		});

		$('#summ_scanned_ttl_qty').text(ttl_qty_scan);

	}
	
	show_ttl_summary();
	
	
	$('.scan_proditems tr').each(function(){
		var ttl_stkgrp_items = $('.scan_proditem',this).length;
		var ttl_scanbybc= $('.scan_bybc',this).length;
		if(ttl_scanbybc)
				{
					$('.prod_stkselprev',this).attr('disabled',true).addClass('disabled');
				}else
				{
					$('.prod_stkselprev',this).attr('disabled',false).removeClass('disabled');
				}
		$('.scan_proditem',this).val("0");
	});





	function checknprompt()
	{
		if(checkall()==false)
		alert("'"+prod+"' is insufficient");
	}

	/**
	 * On form submit
	 * @param {type} elt
	 * @returns {Boolean}
	 */
	function fn_submit_transfer_details(elt)
	{
		
		if(confirm("Are you sure you want to submit?"))
		{
				var chksts=chechpartial();
				//chechpartial()= 0:not scanned, 2:success, 3:partial product, 4: partial deal, 5: partial product, 6: scanned partial combo deal
				if(chksts==0)
				{
//					alert("Error("+chksts+"): Not scanned any products");
					if(confirm("Warning("+chksts+"): Not scanned any products!\n Do you want to revert alloted stock from transfer reservation?"))
					{
						return true;
					}
					else
					{
						return false;
					}
				}
				else if(chksts==3) {
					if(!confirm("Warning("+chksts+"): Do you want to transfer partial deal?"))
					{
						return false;
					}

				}
				else if(chksts==4) {
					if(confirm("Error("+chksts+"): Do you want to proceed partial stock transfer?") )
					{
						return true;
					}
				}
				else if(chksts==5) {
					alert("Error("+chksts+"): Partial product scan quantity cannot be processed!");
					return false;

				}
				else if(chksts==6) {
					alert("Error("+chksts+"): Combo deals cannot scan partially!");
					return false;

				}
				return true;
		}
		return false;
	}
	
	/**
	 * Function to final validation of partial product scan
	 * @returns 0:not scanned, 2:success, 3:partial product, 4: partial deal
	 */
	function chechpartial()
	{
		//f= 0:not scanned, 2:success, 3:partial deal, 4: partial deal, 5: partial product
		var f=2;
		var have_ttl=0;
		var ttl_prods_scanned = 0;
		var combo_scan_qty={};
		
		
		$.each( $(".qty"),function(i,q) {
			var trElt=$(this).closest('tr');
			
			var itemid=trElt.attr("itemid");

			var req=parseInt($(this).html());
			var have=parseInt($(".have",trElt).html());
			//var has_imei_scan=$(trElt).attr("has_imei_scan");
			//var item_transfer_qty=parseInt($('.item_transfer_qty',trElt).val());
			var product_transfer_qty=parseInt($('.product_transfer_qty',trElt).val());
			
			var product_id=$('.product_id',trElt).val();
			var is_combo=$('.is_combo',trElt).val();
			
			
			have_ttl+=have;
			
			//=========================
			// 6. Is combo deal
			if(is_combo==1) {
					if(have>0) {
						//print($(".combo_"+itemid).length);
						combo_scan_qty[itemid+"_"+product_id]={itemid:itemid,product_id:product_id,have:have};

					}

			}//======================
			else
			{
				
			
				//===================
				//5. Partial product quantity 
				var is_fullfil= have % product_transfer_qty;
				if(is_fullfil==1)
				{
					f=5;
					return false;
				}
				//===================
				//1. Partial deal qty
				if(req!=have && have!=0 )
				{
					f=3;
					return false;
				}
			
				//----------------

				//2. Partial transfer
				$('.prod_scan').each(function(){

					var qty_scan = 0;
					$('.prod_stkselprev',this).each(function(){
						qty_scan += $(this).val()*1;
					});

					if(qty_scan)
					{
						ttl_prods_scanned++;
					}

				});
				if(ttl_prods_scanned < $('.prod_scan').length) {
					f=4;
				}
				//-----------
			
			}
			
		});
		
		
		// 6. Is combo deal
		$('.prod_scan').each(function() {
			var trElt=$(this);
			var itemid=trElt.attr("itemid");
			var is_combo=$('.is_combo',trElt).val();
			var req_prod_id=$('.product_id',trElt).val();
			var scanned=parseInt($('.have',trElt).html());
			var product_transfer_qty=parseInt($('.product_transfer_qty',trElt).val());
			
			if(is_combo==1)
			{
			
				if(combo_scan_qty[itemid]!=undefined)
				{
				//$(".combo_"+itemid).each(function(t,tt) {
					
					//var is_fullfil= scanned % product_transfer_qty;
					//print("\nFULFILL"+is_fullfil);
					
					if( combo_scan_qty[itemid+"_"+req_prod_id] == undefined ) //not found
					{
						print("3=>"+"NOT Found="+ req_prod_id);
						//$(".itemid_"+itemid).addClass("partial");
						f=6;
						return false;
					}
					else
					{
						//print("4=>"+"Found="+req_prod_id );
					}

				//});
				}
				
			}
		});
		
		
		//3. Not scanned
		if(have_ttl==0)
			f=0;
		
		return f;

	}

	function checkall()
	{
		var f=true;
		$(".bars").each(function(){
			p=$(this);
			prod=$(".prod",p).html();
			needed=parseInt($(".qty",p).html());
			have=parseInt($(".have",p).html());

			if(needed!=have)
			{
				f=false;
				return false;
			}
			

			
		});

		if(f==true)
		{
			var proceed = 1;
	//		if(refund_alert)
	//			if(!confirm("Did you check refund amount ?"))
	//				proceed = 0;
			if(proceed)
				process_invoice(0);
		}
		return f;
	}
	
	function upd_selprodstk(ele)
	{
		var stat = 0;
		var pbcodes = $(ele).parent().find('nopbcode');
		itm_id = $(ele).attr('itemid');
		mrp = $(ele).attr('mrp');
		stk_i = $(ele).attr('stk_i');
		sel_bcstk_ele = $(ele).parent().find('.pbcode_'+stk_i+'_nobc');
		tr=sel_bcstk_ele.parents('tr:eq(1)');

		$("#scan_barcode").val("");
		if(tr.length==0)
		{
			alert("Warning: The product is not in transfer scan list");
			return;
		}

		needed=parseInt($(".qty",tr).html());
		have=parseInt($(".have",tr).html());
		if(needed<=have)
		{
			alert("Warning: Required qty is already scanned");
			return;
		}

		var ttl_bc_stk = sel_bcstk_ele.attr('stk');
		var cur_sel = sel_bcstk_ele.val()*1;

		if(ttl_bc_stk < cur_sel+1)
		{
			alert("No Stock Available for this product");
			return false;
		}

		if((ttl_bc_stk-(cur_sel+1)) == 0)
		{
			sel_bcstk_ele.removeClass('scan_bybc');
		}

		var ttl_stkgrp_items = $('.scan_proditem',sel_bcstk_ele.parent()).length;
		var ttl_scanbybc = $('.scan_bybc',sel_bcstk_ele.parent()).length;

		if(ttl_scanbybc)
		{
			$('.prod_stkselprev',sel_bcstk_ele.parent()).attr('disabled',true).addClass('disabled');
		}else
		{
			$('.prod_stkselprev',sel_bcstk_ele.parent()).attr('disabled',false).removeClass('disabled');
		}

		sel_bcstk_ele.val(cur_sel+1);
		sel_bcstk_ele.addClass("sel_stk");

		var sel_bcstk_preview_ele = sel_bcstk_ele.parent().find('.prod_stkselprev');
			sel_bcstk_preview_ele.val(sel_bcstk_preview_ele.val()*1+1);


		validate_item(tr);		
	}

	function validate_imeino()
	{
		if($("#scan_imeino").val().length==0)
		{
			alert("Enter IMEI no");
			return;
		}
		var s_imei = $.trim($("#scan_imeino").val());

			// check if valid imei no 
			if(prod_imeino_list[s_imei] == undefined)
			{
				alert("The imei product is not in transfer list");
				return;
			}else
			{
				if(prod_imeino_list[s_imei] == 0)
				{
						alert("Imei no already alloted ");
						return false;
				}

				$.post(site_url+'/admin/jx_chkimeiforpack',{'imeino':s_imei,transfer_option:transfer_option},function(resp){

					if(resp.status == 'error')
					{
						alert(resp.error);
						return;
					}else
					{
						var i_prod_id = prod_imeino_list[s_imei];

						// allot imeino to pending list
						var ttl_imeireq = $('.imei'+i_prod_id).length; 
						var ttl_imeiscanned = $('.imei'+i_prod_id+'_scanned').length;
						if(ttl_imeireq <= ttl_imeiscanned)
							{
								alert("Required Qty of Imei is already scanned");
								return false;
							}

							prod_imeino_list[s_imei] = 0;


							var sel_imei_inpele = $('.imei'+i_prod_id+'_unscanned:eq(0)');
							sel_imei_inpele.parent().append('<a class="remove_scanned" prod_id="'+i_prod_id+'" href="javascript:void(0)" onclick="clear_scannedimeino(this)""><b>X</b></a>');

							sel_imei_inpele.val(s_imei).removeClass('imei'+i_prod_id+'_unscanned').addClass('imei'+i_prod_id+'_scanned');

							$("#scan_imeino").val('').focus();

							var  scn_transfer_id = sel_imei_inpele.attr('transfer_id');
							var  scn_itmid = sel_imei_inpele.attr('itemid');
							var  scn_expiry_on = sel_imei_inpele.attr('expiry_on');
							
							$("#scan_barcode").val(prod_imeino_stock_info[s_imei]+'_'+scn_itmid+'_'+scn_transfer_id+'_'+scn_expiry_on);
							validate_barcode();
					}
				},'json');

			}

	}	


	function clear_scannedimeino(ele)
	{
		var imei_ele = $(ele).parent().find('.imeis');
		var i_prod_id = $(ele).attr('prod_id');
		
		var trA=$(ele).closest('tr');
		var bcinput=$(".scan_proditem",$(trA));
		var bcprevinput=$(".prod_stkselprev",$(trA));
		//======================================================
		prod_imeino_list[imei_ele.val()] = i_prod_id;

		imei_ele.val('');
		imei_ele.removeClass('imei'+i_prod_id+'_scanned').addClass('imei'+i_prod_id+'_unscanned');
		
		
		var tr=$(imei_ele).parents('tr:eq(1)');
		
		var pre_bcin=bcinput.val();
		var pre_bcin2=bcprevinput.val();
		
		bcinput.val(  pre_bcin>0 ? parseInt(pre_bcin)-1:0);
		bcprevinput.val( pre_bcin2 >0 ? parseInt(pre_bcin2)-1:0);
		
		validate_item_remove(tr);
		
		$(ele).remove();
	}
	
	function validate_item_remove(tr)
	{
		needed=parseInt($(".qty",tr).html());
		have=parseInt($(".have",tr).html());
		
		if(have>0)
			have=have-1;
		
		$(".have",tr).html(have);
		
		show_ttl_summary_remove();

		if(have==needed)
		{
			tr.removeClass("partial");
			$(".status",tr).html("OK");
			tr.addClass("done");
		}
		else if(have)
		{
			tr.addClass("partial");
		}
		else if(have<=0)
		{
			tr.removeClass("partial");
			tr.removeClass("done");
		}
		
		checkall();
	}
	
	function show_ttl_summary_remove()
	{
		$('#summ_ttl_scanned_prod').text(0);
		var ttl_prods_scanned = 0;
		var ttl_qty_scan = 0; 

		$('#summ_ttl_scanned_prod').text((ttl_prods_scanned)+'/'+($('.prod_scan').length));

		$('.prod_scan').each(function(){
			var qty_scan = 0;
			$('.prod_stkselprev',this).each(function(){
				qty_scan += $(this).val()*1;
			});
			
//			$('.scan_proditem',this).each(function(){
//				qty_scan += $(this).val()*1;
//			});
			
			if(qty_scan)
			{
				ttl_prods_scanned++;
				$('#summ_ttl_scanned_prod').text((ttl_prods_scanned)+'/'+($('.prod_scan').length));
			}

			ttl_qty_scan+= qty_scan;
		});

		$('#summ_scanned_ttl_qty').text(ttl_qty_scan);

	}
	
	function process_invoice(pmp)
	{
		$("#topform").submit();
		
	}
	
	$(window).on("resize scroll",function() {
       $("#mutiple_mrp_barcodes").dialog("option","position",["center","center"]); 
    });

