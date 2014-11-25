<?php
/**
 * @author Shivaraj <shivaraj@storeking.in>_Sep_10_2014
 */
?>
<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/stock_intake.css">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/plot.css">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/deals.css">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/stk_offline_order.css">

<div class="container">
	<?php
		if($transfer_option==1)
		{
			$header_title="Stock transfer to ".$partner_info['name'];
		}
		else
		{
			$header_title="Return from partner ".$partner_info['name'];
		}
	?>
		<h2 style="float: left;margin:0px;width: 25%"><?php echo $header_title; ?></h2>
		
		<div class="fl_left" style="margin-botton:5px;">
			<a class="button button-action button-tiny button-rounded button_fit" href="<?php echo site_url('admin/stk_partner_select');?>" target="" style="min-width:137px;">Change Transfer Option</a>
		</div>
		<div class="fl_left" style="margin-botton:5px;">
			<a class="button button-action button-tiny button-rounded button_fit" href="<?php echo site_url('admin/partner_transfer_list');?>" target="_blank" style="min-width:100px;">View Transfer List</a>
		</div>
	
		
		<div class="filters_wrap" style="width: 17%;"><img class="search_img_wrap" src="<?php echo base_url().'images/search_icon.png'?>">
			<input type="text" name="srch_deals" class="deal_prd_blk inp" placeholder="Search by Deal Name" >
		</div>

		<div class="legends_outer_wrap" style="width: 20%;">
			<span class="legends_color_notsrc_wrap">&nbsp;</span> - Not Available &nbsp; &nbsp; &nbsp;
			<span class="legends_color_src_wrap">&nbsp;</span> - Available &nbsp; &nbsp; &nbsp;
		</div>
		
		<div class="fran_credit_detwrap" style="display: block; float: right;">
<!--			<span><a class="action_wrap" onclick="load_scheme_details()" href="javascript:void(0)">Scheme &amp; Menu Details</a></span>-->
				
			
			<div class="cart-container">
				<div class="cart-btn-cont">
					<a href="" class="btn btn-blue btn-cart">
						<!--<span class="cart-icon" style="padding-left: 5px"></span>-->
						<span class="cart-label">Selected </span>
						<span class="fk-inline-block cart-count" id="item_count_in_cart_top_displayed"><?=$ttl_cart_items_saved;?></span>
					</a>
				</div>
			</div> 
		</div>
	
		<div id="cus_jq_alpha_sort_wrap" >
		</div>
		
		<div id="cart_prod_div" title="<?php echo $header_title; ?>" width="100%" style="display:none;">
			<h5>Selected Items to <?php echo $header_title;?></h5>

		<div class="clear"></div>

			<ul style="clear:both;">
				<li><a href="#cart_items"><b>Items In Cart</b></a></li>
			</ul>
			<div id="cart_items">
				<form method="post" id="order_form" autocomplete="off">
					<input type="hidden" size="5" name="partner_id" class="partner_id" value="<?=$partner_id;?>" />
					<input type="hidden" size="5" name="transfer_option" class="transfer_option" value="<?=$transfer_option;?>" />
					<div class="cart-dialog-header">
						<table id="cart_prod_temp"  width="100%" cellpadding="0" cellspacing="0">
								<thead>
									<tr>
										<th width="3%">Sno</th><th style="text-align:left !important">Deal Name</th>
										<th width="10%">MRP<br>(Rs)</th>
										<th width="6%"><?php echo "Offer price/<br>DP price<br>(Rs)"; ?></th>
										<th width="9%">Qty</th>
										<th width="7%">Sub Total <br> (Rs)</th>
										<th width="3%">Actions</th>
									</tr>
								</thead>
							<tbody></tbody>
							<tfoot>
								<tr>
									<td colspan="4" align="right">Total cart quantity:</td>
									<td colspan="" align="center"><div class="ttl_cart_qty">0</div></td>
									<td colspan=""></td>
								</tr>
							</tfoot>
						</table>
					</div>


					<div class="cart-footer">
							<div>
								<table>
									<tr>
										<th align="right" valign="top">Partner Reference No <span class="required">*</span>: </th>
										<td style="vertical-align: top;">
											<input type="text" name='partner_ref_no' class='partner_ref_no' style="width:200px;" placeholder="Partner Reference Number">
										</td>
									</tr>
									<tr>
										<th align="right" valign="top">Expected Transfer Date <span class="required">*</span>:</th>
										<td style="vertical-align: top;">
											<input type="text" name="transfer_exp_date" class="transfer_exp_date" value="<?=date('Y-m-d H:i:s');?>">
										</td>
									</tr>
									<tr>
										<th align="right" valign="top">Transfer Remarks  <span class="required">*</span>:</th>
										<td style="vertical-align: top;">
											 <textarea name='transfer_remarks' class='transfer_remarks' cols="5" rows="5" style="width:400px; height: 50px;" placeholder="Transfer Remarks"></textarea>
										</td>
									</tr>
								</table>
							</div>
					</div>
				</form>
			</div>
			
		</div>
		<!-- ============<< dialog boxes >>=========================-->


	<table id="template" style="display:none">
		<tbody>
			<tr style="border-bottom:2px solid #000;" cart_rowid="%cart_rowid%" pid="%pid%" itemid="%itemid%"  pimage="%pimage% %pid%" pname="%pname%" mrp="%mrp%" price="%price%" lcost="%lcost%" margin="%margin%" insufee="%insufee%" memfee="%memfee%">
				<td>%sno%</td>
				<td style="padding:10px 0px 0px;background: #FFF;">
					<div  class="img_wrap">
						<img alt="" height="100" src="<?=IMAGES_URL?>items/small/%pimage%.jpg">
						<input class="pids" type="hidden" name="itemid[]" value="%itemid%">
					</div>
					<div  class="prod_detwrap" >
						<input type="hidden" name="menu[]" value="%menuid%" class="menuids">
						<span class="title"><a href="<?=site_url("admin/deal")?>/%itemid%" target="_blank">%pname%</a></span>
						<div class="p_extra pcart_extra"><b>Category : </b><span>%cat%</span> </div>
						<div class="p_extra pcart_extra"><b>Brand : </b><span> %brand%</span></div>
						<div class="p_stk pcart_extra"><b>Deal Stock : </b> <span>%stock%</span></div>
						<div class="p_pendingord pcart_extra"><b>Open Orders: </b> <span>%pending_orders%</span></div>
						<div class="p_confirm_stk pcart_extra"><b>Available Stock: </b> <span>%confirm_stock%</span></div>
					</div>
					
				</td>
				<td style="text-align: center;" valign="top" class="cart_background_wrap1">
					<div class="p_extra p_top"> <b style="font-size: 13px">%mrp%</b>
				</td>
				<td style="text-align: center;" valign="top"  class="cart_background_wrap2">
					<div class="price p_top">%price%</div>
				</td>
				<td  valign="top"  class="cart_background_wrap1" width="12%">
					<div class="p_top"><input type="text" class="qty" pmax_ord_qty="%max_oqty%" size=2 name="qty[]" value="%cart_qty%" onchange="return change_prod_qty(this);" stk="%stock%" pending_orders="%pending_orders%">
				</td>
				<td  style="text-align:center;"  class="cart_background_wrap2_subtotal" valign="top">
					<div class="stotal p_top" style="font-color:white;">%ttllcost%</div>
				</td>
			
				<td style="text-align: center;"  class="cart_background_wrap1" valign="top">
					<a onclick="remove_psel(this)" title="Remove Product"><div class="p_top"><img src=<?php echo base_url().'images/remove-over-red.png';?> style="cursor:pointer;" title="Remove Product from cart"></a></div>
				</td>
			</tr>
		</tbody>
	</table>
	<!-- ============<< dialog boxes >>=========================-->
</div>
<script>
var partner_id='<?=$partner_id;?>';
var transfer_option='<?=$transfer_option;?>';
$('#cart_prod_div').tabs();

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
				tmpl += '		<div class="jq_alpha_sort_alphalist_vend_head"></div>';
				tmpl += '		<div class="jq_alpha_sort_alphalist_char">';
//				tmpl += '		<a href="javascript:void(0)" chr="20">T20</a>';
				tmpl += '		<a href="javascript:void(0)" chr="09">0-9</a>';
				for(var i=0;i<chars.length;i++)
					tmpl += '		<a href="javascript:void(0)" chr="'+chars[i].toUpperCase()+'">'+chars[i].toUpperCase()+'</a>';	
				tmpl += '		</div>';
				tmpl += '		<div class="jq_alpha_sort_alphalist_item_wrap">';
				tmpl += '			<div class="jq_alpha_sort_alphalist_itemlist"></div>';
				tmpl += '		</div>';
				
				tmpl += '	</div>';
				
				tmpl += '	<div class="jq_alpha_sort_overview">';
				tmpl += '		<div class="brands_bychar_list">';
				tmpl += '			<div class="Brands_bychar_list_head"></div>';
				tmpl += '			<div class="Brands_bychar_list_content"></div>';
				tmpl += '		</div>';
				tmpl += '		<div class="sub_category_list">';
				tmpl += '			<div class="sub_category_list_head"></div>';
				tmpl += '			<div class="sub_category_list_content"></div>';
				tmpl += '		</div>';
				//tmpl += '		<div class="jq_alpha_sort_overview_title"><span class="ttl_deals_wrap"></span></div>';
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
            	if($('.cat_lst_tab').hasClass('selected_alpha_list'))
            		options.item_click_bybrand($(this).attr('brandid'),this);
            	else 
            		options.item_click($(this).attr('catid'),this);
            });
            
        });
    };
    $.fn.jqSuAlphaSort.config = {
        title:'Hello',
        click:function(){}
    };
}(jQuery));

$(function(){
	
	$('#cus_jq_alpha_sort_wrap').jqSuAlphaSort({title:"List of Categories",overview_title:"List of Deals",'char_click':function(chr,ele){ cat_bychar(chr)},'item_click':function(catid,ele){ deallist_bycat(0,catid,0,0,0,'')},'item_click_bybrand':function(brandid,ele){ deallist_bycat(brandid,0,0,0,0,'')}});
		$(".jq_alpha_sort_alphalist_char a").click(function() {
	      // remove classes from all
	      $(".jq_alpha_sort_alphalist_char a").removeClass("jq_alpha_active");
	      // add class to the one we clicked
	      $(this).addClass("jq_alpha_active");
	    });
	
	 $(".jq_alpha_sort_alphalist_char a:eq(0)").trigger('click');
   	$('.jq_alpha_sort_alphalist_itemlist').slimScroll({
	    height: '100px'
	});
	
	$(".jq_alpha_sort_alphalist_itemlist_divwrap a:eq(0)").trigger("click");
	
	$('.jq_alpha_sort_alphalist_vend_head').html('<div class="alphabet_header_wrap"><span><a id="cat_lab" class="cat_lst_tab">Category List</a></span><span><a id="brand_lab" style="margin-right:0px !important" class="brand_lst_tab">Brand List</a></span> <input type="text" name="search_name" class="search_blk inp" placeholder="Search by Name" ><img style="margin-top: 7px;" src="<?php echo base_url().'images/search_icon.png'?>"></div>');
	deallist_bycat(0,0,0,0,'');
	$('.brand_lst_tab').addClass('selected_alpha_list');
	
	$('select[name="publish_wrap"]:eq(1)').trigger('click');
//	$(".sel_all, .sel").attr("checked",false);
	
});

$(".sel_all").live('click',function(){
		if($(".sel_all").attr("checked"))
		{
			$(".sel").attr("checked",true);
		}
		else
			{$(".sel").attr("checked",false);}
	});

$('.Brands_bychar_list_content_listdata').live("click",function(){
	var brandid =$(this).attr('bid')*1;
	var catid =$(this).attr('cid')*1;
	
		$(this).toggleClass("selected_class");
	  	if($(this).hasClass('selected_class'))
	  	{
	  		$('.selected_class').removeClass('selected_class');
	  		$(this).addClass('selected_class');
	  	 	deallist_bycat(brandid,catid,0,0,'');
	  	 	//filter_deallist($(this).text(),'brand');
	  	}
	  	else
	  	{ 
	  		deallist_bycat(0,catid,0,0,'');
		  	//filter_deallist('','all');
	  	}
		$('.left_filter_wrap').show();
});

$('.sub_category_list_content_listdata').live("click",function(){
	var brandid =$(this).attr('bid')*1;
	var catid =$(this).attr('cid')*1;
	
		$(this).toggleClass("selected_class");
	  	if($(this).hasClass('selected_class'))
	  	{
	  		$('.selected_class').removeClass('selected_class');
	  		$(this).addClass('selected_class');
	  	 	deallist_bycat(brandid,catid,0,0,'');
	  	 	//filter_deallist($(this).text(),'brand');
	  	}
	  	else
	  	{ 
	  		deallist_bycat(0,catid,0,0,'');
		  	//filter_deallist('','all');
	  	}
		$('.left_filter_wrap').show();
});

$('.all').live('click',function(){
	var catid =$(this).attr('catid')*1;
	var brandid =$(this).attr('brandid')*1;
	$('.all').addClass("selected_type");
	if(catid !=0 && brandid == 0)
		deallist_bycat(0,catid,0,0,'');
	else if(catid ==0 && brandid != 0)
		deallist_bycat(brandid,0,0,0,'');
	else if(catid !=0 && brandid != 0)
		deallist_bycat(brandid,catid,0,0,'');
	else
		deallist_bycat(0,0,0,0,'');
});

$('.latest_sold').live('click',function(){
	var catid =$(this).attr('catid')*1;
	var brandid =$(this).attr('brandid')*1;
	if(catid !=0 && brandid == 0)
		deallist_bycat(0,catid,1,0,'');
	else if(catid ==0 && brandid != 0)
		deallist_bycat(brandid,0,1,0,'');
	else	
	deallist_bycat(brandid,catid,1,0,'');
	$('.latest').addClass("selected_type");
});

$('.latest_added').live('click',function(){
	var catid =$(this).attr('catid')*1;
	var brandid =$(this).attr('brandid')*1;
	if(catid !=0 && brandid == 0)
		deallist_bycat(0,catid,3,0,'');
	else if(catid ==0 && brandid != 0)
		deallist_bycat(brandid,0,3,0,'');
	else	
	deallist_bycat(brandid,catid,3,'');
	$('.latest').addClass("selected_type");
});

$('.most').live('click',function(){
	var catid =$(this).attr('catid')*1;
	var brandid =$(this).attr('brandid')*1;
	if(catid !=0 && brandid == 0)
		deallist_bycat(0,catid,2,0,'');
	else if(catid ==0 && brandid != 0)
		deallist_bycat(brandid,0,2,0,'');
	else	
		deallist_bycat(brandid,catid,2,0,'');
	$('.most').addClass("selected_type");
	
});

$('.cat_lst_tab').live('click',function(){
	$('#cat_lab').val('1');
	$('#brand_lab').val('0');
	deallist_bycat(0,0,0,0,'');
	
	$(".jq_alpha_sort_alphalist_char a:eq(0)").trigger('click');
	$('.cat_lst_tab').removeClass("selected_alpha_list");
	$('.brand_lst_tab').addClass("selected_alpha_list");
});

$('.brand_lst_tab').live('click',function(){
	deallist_bycat(0,0,0,0,'');
	$('#cat_lab').val('0');
	$('#brand_lab').val('1');
	//$('.brands_bychar_list').html('');
	 $(".jq_alpha_sort_alphalist_char a:eq(0)").trigger('click');
	$('.brand_lst_tab').removeClass("selected_alpha_list");
	$('.cat_lst_tab').addClass("selected_alpha_list");
});


$('select[name="publish_wrap"]').live('change',function(){
	var v=$(this).val();
	var brandid=$('.publish_wrap').attr('brandid');
	var catid=$('.publish_wrap').attr('catid');
	var type=$('.publish_wrap').attr('type');
	
	if(v==1)
		v='live';
	else if(v==0)
		v='notlive';
	else if(v==2)
		v='all';
	published_deals(brandid,catid,type,v);
});

function published_deals(brandid,catid,type,v)
{
	deallist_bycat(brandid,catid,type,0,v);
}



$('input[name="search_name"]').live('keyup',function(){
	var chr=$('input[name="search_name"]').val();
	var i=0; 
	$(".jq_alpha_sort_alphalist_itemlist_divwrap").each(function(){
		name=$(this).attr('name');
		if(name.match(chr,'ig'))
			{
				$(this).show();
				
			}
		else
			{
				$(this).hide();
				
			}
		if(i == 0)
		{
			if(!$('#brand_lab').hasClass('selected_alpha_list'))
			{
				search(1,0);
			}
				
			else if(!$('#cat_lab').hasClass('selected_alpha_list'))
			{
				search(0,1);
			}
		}		
	});
});

function filter_deallist(tag,by)
{
	var srch_inp =  $('input[name="srch_deals"]').val();
	var publish_status = $('select[name="publish_wrap"]').val();
	var bc_sel =''; //($('.Brands_bychar_list_content_listdata.selected_class').length?($('.Brands_bychar_list_content_listdata.selected_class').text()):0);
	$(".sk_deal_filter_wrap").each(function(){
			
			search_text=$(this).attr('name')+' '+$(this).attr('brand')+' '+$(this).attr('category');
			
			var row_stat = 1;
//				if(publish_status != $(this).attr('publish') && (publish_status != 'all'))
//					row_stat = 0;
			
			// check if any brand cat is selected 
			if(bc_sel && row_stat)
			{
				tag = bc_sel;
				if(search_text.match(tag,'ig'))
					row_stat = 1;
				else 
					row_stat = 0;
			}
			
			// check if search data is entered 
			if(srch_inp.length && (row_stat==1))
			{
				tag = srch_inp;
				if(!search_text.match(tag,'ig'))
					row_stat = 0;
			}

			if(row_stat == 1)
			{
				$(this).show();
			}
			else
			{
				$(this).hide();
				search_othr_deal();
			}
	});

	//$('.total_wrap').html("Total Deals : "+$(".sk_deal_filter_wrap:visible").length);
}

$('input[name="srch_deals"]').live('keyup',function(){
	filter_deallist($(this).val(),'search');
});

$('.stock_det_close').live("click",function(){
	var id=$(this).attr('refid');
	$('.stock_det_'+id).hide();
});

function search_othr_deal()
{
	var brandid=$('.publish_wrap').attr('brandid');
	var catid=$('.publish_wrap').attr('catid');
	$('input[name="srch_deals"]').autocomplete({
		source:site_url+'/admin/jx_searchsktdeals_json/0/0/0/',
		minLength: 2,
		type:'POST',
		select:function(event, ui ){
			var itemid=ui.item.itemid;
			$('.itemid_'+itemid).show();
			
			if(!$('.itemid_'+itemid).length)
				deallist_bycat(brandid,catid,0,itemid,'');
		}
	});
}

function deallist_bycat(brandid,catid,type,dealid,publish)
{
	$('input[name="srch_deals"]').val('');

	//$('.jq_alpha_sort_overview_content').html('<div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
	$('.sk_deal_container').css('opacity','0.5');//$('.jq_alpha_sort_overview_content').css('opacity','0.5');
	if(catid != 0 && brandid==0)
	{
		$.getJSON(site_url+'/admin/loadbrand_bycat/'+catid,'',function(resp){
			var brand_linkedcat_html='';
			if(resp.status=='error')
			{
				$('.Brands_bychar_list_head').html('<h4>NO Brands Found</h4>');
				$('.Brands_bychar_list_content').html("");
			}
			else
			{
				brand_linkedcat_html+='';
				var brand_linkedcat_head='';
				$.each(resp.brand_list,function(i,b){
					brand_linkedcat_html+='<a class="Brands_bychar_list_content_listdata" cid="'+b.catid+'" bid="'+b.brandid+'">'+b.brand_name+'</a>';
					
					brand_linkedcat_head='<span class="close_btn_dlpg button button-tiny button-rounded ">Hide</span>';
					brand_linkedcat_head='<h4>List of Brand for <i>'+b.category_name+'</i></h4>';
					$('.Brands_bychar_list_head').html(brand_linkedcat_head);
				});
				
			}
			
			

			$('.Brands_bychar_list_content').html(brand_linkedcat_html);
			
		});
		
		$.getJSON(site_url+'/admin/jx_get_subcategories/'+catid,'',function(resp){
			var subcat_linkedcat_html='';
			if(resp.status=='error')
			{
				$('.sub_category_list_head').html('<h4>NO Sub categories Found</h4>');
				$('.sub_category_list_content').html("");
				$('.sub_category_list').hide();
			}
			else
			{
				
				subcat_linkedcat_html+='';
				$.each(resp.subcat_list,function(i,b){
					if(b.subcat.length != 0)
					{
						$.each(b.subcat,function(a,k){
							subcat_linkedcat_html+='<a class="sub_category_list_content_listdata" cid="'+k.id+'" bid="'+brandid+'">'+k.name+'</a>';
							$('.sub_category_list_head').html('<h4>List of Sub Categories for '+b.name+'</h4>');
						});
					}else
					{
						$('.sub_category_list_head').html("");
					}
					
				});
				$('.sub_category_list').show();
			}
			$('.sub_category_list_content').html(subcat_linkedcat_html);
		});
	}else if(catid == 0 && brandid!=0)
	{
		$.getJSON(site_url+'/admin/loadcat_bybrand/'+brandid,'',function(resp){
			var cat_linkedcat_html='';
			if(resp.status=='error')
			{
				$('.Brands_bychar_list_head').html('<h4>NO Categories Found</h4>');
				$('.Brands_bychar_list_content').html("");
			}
			else
			{
				cat_linkedcat_html+='';
				$.each(resp.cat_list,function(i,b){
					cat_linkedcat_html+='<a class="Brands_bychar_list_content_listdata" cid="'+b.catid+'" bid="'+b.brandid+'">'+b.category_name+'</a>';
					$('.Brands_bychar_list_head').html('<span class="close_btn_dlpg button button-tiny button-rounded ">Hide</span><h4>List of Categories for <i>'+b.brand_name+'</i></h4>');
				});
			}
			$('.Brands_bychar_list_content').html(cat_linkedcat_html);
			
		});
	}
	
	//capture saved tr
	//$('.sk_deal_container tbody tr').each(function(){
		
	//});		
	
	$('.sk_deal_container').html('<div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
	//$('.sk_deal_container').css('opacity','0.5');
	
	$.post(site_url+'/admin/jx_deallist_bycat_partner',{brandid:brandid,catid:catid,type:type,dealid:dealid,publish:publish,partner_id:partner_id},function(resp){
		$('.sk_deal_container').css('opacity','1');
		//$('.sk_deal_container').css('opacity','1');
			if(resp.deals_lst.total_rows == 0)
			{
				$('.sk_deal_container').html('<div class="page_alert_wrap">No Deals Found</div>');
				$('.total_wrap').hide();
				$('.src_deals').hide();
				$('.unsrc_deals').hide();
				return false;
		    }
			if(resp.deals_lst.total_rows != 0)
			{
				
				
				$('.total_wrap').show();
				$('.src_deals').show();
				$('.unsrc_deals').show();
				
				var enable='checked="checked"';
				
				var d_lst = '';
				d_lst+='<div class="sk_deal_filter_blk_wrap"><span>';
				
				d_lst+='</span>';
				d_lst+='<span class="left_filter_mrp_wrap">MRP Filter : <input type="text" class="inp" id="f_from" size=4> to <input type="text" class="inp" id="f_to" size=4> <button type="button" style="margin-top:-5px" class="button button-rounded button-action button-tiny" onclick="filter_deals_bymrp()">Filter</button></span>';
				//d_lst+='<span class="ttl_deals_wrap">Total Deals : '+resp.ttl_deals+'</span>';
				d_lst+='<span class="publish_wrap" brandid='+resp.brandid+' catid='+resp.catid+' type='+resp.type+'><b>Available :</b>\n\
							<select name="publish_wrap"><option value="2">All</option><option value="1">Yes</option><option value="0">No</option></select>\n\
						</span>';
//				d_lst+='<span class="left_filter_wrap"><span class="filter_opts"><a class="most" href="javascript:void(0)" val="2"  brandid="'+brandid+'" catid="'+catid+'">Most Sold</a></span><span><a href="javascript:void(0)" class="latest_sold" val="1" brandid="'+brandid+'" catid="'+catid+'" >Latest Sold</a></span><span><a href="javascript:void(0)" class="latest_added" val="3" brandid="'+brandid+'" catid="'+catid+'" >Latest Added</a></span><span><a href="javascript:void(0)" val="0" class="all" brandid="'+brandid+'" catid="'+catid+'" style="border-right:none !important;width:24.2%">Clear</a></span></span></div>';
				d_lst+='<div class="sk_deal_filter_blk_wrap"><span class="total_wrap">Showing '+resp.ttl_deals+' / '+resp.deals_lst.total_rows+' Deals</span>';
				d_lst+='<span class="src_deals" style="color:green">Sourceable Deals : '+resp.deals_lst.src_deals+'</span>';
				d_lst+='<span class="unsrc_deals" style="color:red">Not Sourceable Deals : '+resp.deals_lst.unsrc_deals+'</span></div>';
				d_lst+='<div class="sk_deal_container">';
				d_lst+='<table class="sk_deal_blk_wrap" cellpadding="0" cellspacing="0" width="99%">'
				d_lst+='<thead><tr>';
				d_lst+='<th width="6%">ITEM ID</th><th width="5%">Prod.ID</th><th>Deal Name</th><th>Partner</th><th width="8%">Stock</th><th width="10%" style="">Brand</th><th width="15%">Category</th><th width="6%">MRP</th><th width="10%">DP/Offer Price</th><th>Member Price</th><th width="12%">Published/Actions<br />';
				/*
				if(resp.has_user_edit)
				{
					d_lst+='<input type="checkbox" class="sel_all"><button type="button" class="disab_all" style="padding:2px !important" onclick="endisable_sel('+0+','+brandid+','+catid+')">Disable</button> <button type="button" class="enab_all" style="padding:2px !important" onclick="endisable_sel('+1+','+brandid+','+catid+')">Enable</button>';	
				}*/
				
				d_lst+='</th></tr></thead>';
			 	$.each(resp.deals_lst.deals,function(i,d){
					
					if(d.publish==1)
					{
						var enable='checked="checked"';
						
					}else
					{
						var enable='';
						
					} // style="'+background+'"
//					if(d.publish==1)
//					{
						d_lst+='<tr class="sk_deal_filter_wrap deals_'+d.catid+' itemid_'+d.itemid+'" publish="'+d.publish+'" mrp="'+d.orgprice+'" name="'+d.name+'" brand="'+d.brand+'" category="'+d.category+'" ref_id="'+d.itemid+'" total_row="'+resp.deals_lst.total_rows+'">';
				 			//d_lst+='<img src="'+images_url+'/items/small/'+d.pic+'.jpg'+'">';
		 					d_lst+='<td><span>'+d.itemid+'</span></td>';
		 					d_lst+='<td><span>'+d.product_ids+'</span></td>';
		 					d_lst+='<td><span class="title"><a target="_blank" href="'+site_url+'/admin/deal/'+d.itemid+'">'+d.name+'</a><div class="stock_det_'+d.itemid+'"></div></td>';
							d_lst+='<td><span class="name">'+d.partner_name+'</span></td>';
							d_lst+='<td><span class="stock_msg"></span><br><a href="javascript:void(0)" dealid="'+d.itemid+'" class="tgl_stock_combo deal_stock " curr_stock="0"></a></td>';
							
		 					d_lst+='<td><span><a target="_blank" href="'+site_url+'/admin/viewbrand/'+d.brandid+'">'+d.brand+'</a></span></td>';
		 					d_lst+='<td><span><a target="_blank" href="'+site_url+'/admin/viewcat/'+d.catid+'">'+d.category+'</a></span></td>';
		 					d_lst+='<td><span class="mrp">'+d.orgprice+'</span></td>';
		 					d_lst+='<td align="center"><span>'+d.price+'</span></td>';
		 					d_lst+='<td><span class="mrp">'+d.member_price+'</span></td>';
							
		 					d_lst+='<td>';
		 					//d_lst+='<span class="prod_'+d.pnh_id+'"><a href="javascript:void(0)" onclick="quikview_product('+d.pnh_id+')" class="button button-rounded button-tiny quicklook_btn" >Quick Look</a></span></td></td>';
		 					//d_lst+='<span class="similarprod_'+d.itemid+'"><a href="javascript:void(0)" onclick="add_tocart('+d.itemid+')" class="button button-rounded button-tiny quicklook_btn add_cart_btn" >Add to Cart</a></span></td></td>';
		 					if(d.live==1) {
								d_lst+='<span class="prod_'+d.itemid+'"><a href="javascript:void(0)" onclick="add_tocart(this,'+d.itemid+')" class="button button-rounded button-tiny quicklook_btn add_cart_btn" >Select</a></span></td>';
							}else {
								d_lst+='';
							}
							d_lst+='</td>';
	 					d_lst+='</tr>';
//					}
						
				});
				
				
				d_lst+='</table>';
				d_lst+='</div>';
				
				//polist_byvendor(vids[0]);
				$('.jq_alpha_sort_overview_content').html(d_lst);

				// Call the plugin
				$(".jq_alpha_sort_overview_content .deal_stock").dealstock({
					show_only:1 // only instk items, 0: Default, 1: only available stock
					,transfer_option:transfer_option // 0: Default, 1:To partner, 2: From partner
				});
						
				$("#sel_cat").chosen();
				if(resp.type == 1)
				{
					$('.most').removeClass('selected_type');
					$('.latest_added').removeClass('selected_type');
					$('.latest_sold').addClass('selected_type');
				}else if(resp.type == 0)
				{
					$('.latest_sold').removeClass('selected_type');
					$('.latest_added').removeClass('selected_type');
					$('.most').removeClass('selected_type');
				}
				else if(resp.type == 2)
				{
					$('.most').addClass('selected_type');
					$('.latest_sold').removeClass('selected_type');
					$('.latest_added').removeClass('selected_type');
				}
				else if(resp.type == 3)
				{
					$('.most').removeClass('selected_type');
					$('.latest_sold').removeClass('selected_type');
					$('.latest_added').addClass('selected_type');
				}
				if(resp.publish == 'src')
				{
					$('.unsrc_deals').hide();
				}
				else if(resp.publish == 'unsrc')
				{
					$('.src_deals').hide();
				}
				if(dealid == 0)
				{
					$('.src_deals').show();
					$('.unsrc_deals').show();
				}
				else
				{
					$('.src_deals').hide();
					$('.unsrc_deals').hide();
				}
				
				
			}
	},'json');	
	
}

function cat_bychar(ch)
{
	if($('#cat_lab').val() == 1)
	{
		$.post(site_url+'/admin/cat_list_bycharacter',{ch:ch,partner_id:partner_id},function(resp){
    	if(resp.status == 'error')
			{
				alert("Brands not found for selected character");
				return false;
		    }
			else
			{
					var b_list = '';
					$.each(resp.cat_list,function(i,b){
			 			b_list += '<div class="jq_alpha_sort_alphalist_itemlist_divwrap" name="'+b.name+'"><a  href="javascript:void(0)"  catid="'+b.id+'">'+b.name+'</a></div>';
					});
					//polist_byvendor(vids[0]);
				$('.jq_alpha_sort_alphalist_itemlist').html(b_list);
			}
    	},'json');
    	
	}else if($('#brand_lab').val() == 1)
	{
		
		$.post(site_url+'/admin/brand_list_bycharacter',{ch:ch},function(resp){
    	if(resp.status == 'error')
			{
				alert("Categories not found for selected character");
				return false;
		    }
			else
			{
					var b_list = '';
					$.each(resp.brand_list,function(i,b){
			 			b_list += '<div class="jq_alpha_sort_alphalist_itemlist_divwrap" name="'+b.name+'"><a  href="javascript:void(0)"  brandid="'+b.id+'">'+b.name+'</a></div>';
					});
					//polist_byvendor(vids[0]);
				$('.jq_alpha_sort_alphalist_itemlist').html(b_list);
			}
    	},'json');
	}else
	{
		$.post(site_url+'/admin/cat_list_bycharacter',{ch:ch,partner_id:partner_id},function(resp){
    	if(resp.status == 'error')
			{
				alert("Categories not found for selected character");
				return false;
		    }
			else
			{
					var b_list = '';
					$.each(resp.cat_list,function(i,b){
			 			b_list += '<div class="jq_alpha_sort_alphalist_itemlist_divwrap" name="'+b.name+'"><a  href="javascript:void(0)" catid="'+b.id+'">'+b.name+'</a></div>';
					});
					//polist_byvendor(vids[0]);
				$('.jq_alpha_sort_alphalist_itemlist').html(b_list);
			}
    	},'json');
	}		
}   

function search(b_search,c_search)
{
	$('input[name="search_name"]').autocomplete({
		source:site_url+'/admin/jx_search_json/'+b_search+'/'+c_search+'/'+0,
		minLength: 2,
		
			select:function(event, ui ){
				if(b_search==1)
				{
					//$('.jq_alpha_sort_alphalist_itemlist').html('<div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
					deallist_bycat(ui.item.id,0,0,0,'');
				}else if(c_search==1)
				{
					//$('.jq_alpha_sort_alphalist_itemlist').html('<div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
					deallist_bycat(0,ui.item.id,0,0,'');
				}
				
			}
	});
			
}

$('.Brands_bychar_list_content').hide();
$(".Brands_bychar_list_head span").live("click",function() {
	var $this = $(this);
    // target only the content which is a sibling
    $('.Brands_bychar_list_content').slideToggle(200, function () {
        $this.text($(this).is(':visible') ? 'Hide' : 'Show');
        
    });
    $(".Brands_bychar_list_head").show();
});

function filter_deals_bymrp()
{
	from=$("#f_from").val();
	to=$("#f_to").val();
	if(from == to)
	{
		alert("Filter prices are not valid numbers");
		return;
	}
	if(!is_numeric(from) || !is_numeric(to))
	{		
		alert("Filter prices are not valid numbers");
		return;	
	}
	
	var valid_mrp=[];
	var i=0;
	var pub=0;
	var unpub=0;
	var total_rows=0;
	$(".sk_deal_filter_wrap").each(function(){
		$(this).show();
		mrp=parseInt($(this).attr('mrp')*1);
		publish=$(this).attr('publish');
		total_rows=$(this).attr('total_row');
		if(mrp>=from && mrp<=to)
		{
				valid_mrp.push(mrp);
				$(this).show();
				++i;
				if(publish==1)
					++pub;
				else if(publish==0)
					++unpub;
		}
		else
		{
				$(this).hide();
		}
	});
	$('.total_wrap').html("Showing "+i+" / "+total_rows+" Deals" );
	$('.src_deals').html("Sourceable Deals : "+pub);
	$('.unsrc_deals').html("Not Sourceable Deals : "+unpub);
	if(valid_mrp.length == 0)
	{
		$('.sk_deal_container').html('<div class="page_alert_wrap">No Deals Found between Rs. '+from+' to Rs. '+to+'</div>');
	}
}
//function quikview_product(pid)
//{
//	$("#quick_viewdiv").data('qvk_pid',pid).dialog('open');
//}

	function add_tocart(elt,itemid)
	{


		$.post(site_url+"/admin/partner_jx_add_deal_tocart",{itemid:itemid,partner_id:partner_id},function(resp){

			/*if(resp.mid_status =='error')
			{
				if(confirm(resp.message))
					 mem_reg(pre_selected_fid);
				 return false;
			}*/
			
			if(resp.status=='success')
			{
				$("span.prod_"+itemid).html('<a href="javascript:void(0)" onclick="remove_prod_frmcart('+itemid+')" class="button button-rounded button-tiny remove_cart_btn" title="Remove From Cart" align="center">REMOVE</a>');
				//$("span.prod_"+itemid).html('<a href="javascript:void(0)" onclick="remove_prod_frmcart('+itemid+')" class="button button-rounded button-tiny remove_cart_btn" title="Remove From Cart" align="center">REMOVE</a>');
				//$("span.similarprod_"+itemid).html('<a href="javascript:void(0)" onclick="remove_prod_frmcart('+itemid+')" class="button button-rounded button-tiny remove_cart_btn" title="Remove From Cart" align="center">REMOVE</a>');
				$("#item_count_in_cart_top_displayed").html(resp.ttl_cart_item);

			}else if(resp.status=='error')
			{
				alert(resp.message);

			}else
			{
				//$(elt).html("Selected");
				$("span.prod_"+itemid).html('<a href="javascript:void(0)" onclick="remove_prod_frmcart('+itemid+')" class="button button-rounded button-tiny remove_cart_btn" title="Remove From Cart" align="center" style="min-width:100px;">Remove selected</a>');
				//$('.cart-container').trigger('click');
			}
		},'json');


	}

	$('.cart-container').click(function(){
		
		// Datetime Picker
		$(".transfer_exp_date").datetimepicker({changeMonth:true
			,changeYear:true});
		$(".transfer_exp_date").datetimepicker( "option", "dateFormat", 'dd/M/yy' );
		$(".transfer_exp_date").datetimepicker( "option", "timeFormat", 'HH:mm' );


		$("#cart_prod_div").dialog('open');
		return false;
	});
	
	$(".cart_prodcontinue").click(function(){
		$("#cart_prod_div").dialog('close');
	});
	
	function remove_psel(ele)
	{
		//var sel_pid = $(ele).parents("tr:first").attr('pid');
		var itemid=$(ele).parents("tr:first").attr('itemid');
		if(confirm("Are you sure want to remove product from cart?"))
		{	
			//$.post("<?=site_url("admin/jx_update_tocart")?>",{pid:sel_pid,fid:pre_selected_fid,mid:<?php //echo $mid;?>},function(data){
			
			$.post(site_url+"/admin/partner_jx_update_tocart",{itemid:itemid,partner_id:partner_id},function(data){
				if(data.status=='success')
				{
					$(ele).parents("tr:first").fadeOut().remove();
					$("#item_count_in_cart_top_displayed").html(data.ttl_cart_item);
					//$("span.prod_"+itemid).html('<a href="javascript:void(0)" class="button button-rounded button-tiny quicklook_btn" onclick="quikview_product('+itemid+')" >Quick Look</a>');
					$("span.prod_"+itemid).html('<a href="javascript:void(0)" onclick="add_tocart(this,'+itemid+')" class="button button-rounded button-tiny quicklook_btn add_cart_btn" >Select</a>');
					$('#cart_prod_temp tbody tr').each(function(a,b){
						$('td:first',this).html(a+1);
					});
					change_total_subtotal();
					show_ttl_cart_qty();
				}
				//has_insurance_dl--;
			},'json');
		}

	}

	function remove_prod_frmcart(itemid)
	{
		if(confirm("Are you sure want to remove product from cart?"))
		{
			//$.post("<?=site_url("admin/partner_jx_update_tocart")?>",{pid:pid,fid:pre_selected_fid,mid:<?php //echo $mid;?>},function(resp){
			$.post(site_url+"/admin/partner_jx_update_tocart",{itemid:itemid,partner_id:partner_id},function(resp){
				if(resp.status=='success')
				{
					$("#item_count_in_cart_top_displayed").html(resp.ttl_cart_item);
					//$("span.similarprod_"+itemid).html('<a href="javascript:void(0)" onclick="add_tocart('+itemid+')" class="button button-rounded button-tiny add_cart_btn" >Add to Cart</a>');
					//$("span.prod_"+itemid).html('<a href="javascript:void(0)" onclick="quikview_product('+itemid+')" class="button button-rounded button-tiny quicklook_btn" >Quick Look</a>');
					$("span.prod_"+itemid).html('<a href="javascript:void(0)" onclick="add_tocart(this,'+itemid+')" class="button button-rounded button-tiny quicklook_btn add_cart_btn" >Select</a>');
					change_total_subtotal();
					show_ttl_cart_qty();
				}else
				{
					alert(resp.message);
				}
			},'json');
		}
		return false;
	}
	
	function remove_pid(pid)
	{
		var t_pids=pids;
		pids=[];
		for(i=0;i<t_pids.length;i++)
			if(pid!=t_pids[i])
				pids.push(t_pids[i]);
		 change_total_subtotal(); 
	}

	function change_total_subtotal() 
	{
		var ttl_subtotal=0;
		if($("#cart_prod_temp .stotal").html() == '') {
			ttl_subtotal=0;
//			member_feetotal=0;
//			insu_feetotal=0;
		}
		else {
			$("#cart_prod_temp .stotal").each(function(){
			  ttl_subtotal += format_number($(this).html());
			});
			/*if(selected_mid==0)
			{
				$("#cart_prod_temp .insfeetotal ").each(function(){
					ttl_subtotal += format_number($(this).html());
				  });
				$("#cart_prod_temp .mfeetotal ").each(function(){
					ttl_subtotal += format_number($(this).html());
				  });
			}*/
			//ttl_subtotal=ttl_subtotal+insu_feetotal+member_feetotal;
		}
		$("#cart_totl").html(format_number(ttl_subtotal));
		
	}

	
	//$("#cart_prod_temp .qty").live("change",function(){
	function change_prod_qty(elt)
	{
		var p=$(elt).parents("tr:first");
		var qtyElt=$(".qty",p);
		//sel_pid = $(elt).parents("tr:first").attr('pid');
		//var sel_itemid = p.attr('itemid');//$(elt).parents("tr:first").attr('itemid');
		var cart_rowid = $(p).attr('cart_rowid');
		var qty_e = parseInt(qtyElt.val());
		var p_stock = $(".qty",p).attr('stk')*1;
		var p_pending_ords = $(".qty",p).attr('pending_orders')*1;
		
		var avail_stk=(p_stock-p_pending_ords);
		
		if(avail_stk < qty_e)
		{
			alert("Warning: We have maximum of "+avail_stk+" stock!");
			if(avail_stk<0)
				qty_e = 0;
			else
				qty_e=avail_stk;
		}
		else
		{
			//alert(qty_e+"="+p_stock+"="+p_pending_orders);
			if(isNaN(qty_e*1))
			{
				//alert("Invalid Qty Entered.");
				qty_e = 0;
			}
			else if(qty_e*1 <= 0)
			{
				qty_e = 0;
				alert("Error: Invalid Qty Entered,Please enter atleast one quantity.");
			}

			//var qty_m = $(".qty",p).attr('pmax_ord_qty')*1;


			if(qty_e > avail_stk)
			{
				alert("Warning: Maximum "+avail_stk+" Qty can be Ordered ");
				qty_e = avail_stk;
			}
		}
		
		$.post(site_url+'/admin/part_jx_update_cartqty/',{cart_qty:qty_e,cart_rowid:cart_rowid},function(resp){
			if(resp.status=='success')
				return true;
		},'json');

		qtyElt.val(qty_e).focus();
		
		var sub_total = parseFloat( $(".price",p).text())*parseInt($(".qty",p).val() ) ;
		 $(".stotal",p).html( format_number( sub_total ) );

		change_total_subtotal();
		show_ttl_cart_qty();
	}
	//});
	
	
	
	var ppids=[];
	
	$("#cart_prod_div").dialog({
		autoOpen:false,
		width:1250,
		modal:true,
		height:600
		,title:"Cart Items"
		,open:function(event, ui) {
	        $(event.target).dialog('widget')
            .css({ position: 'fixed' })
            .position({ my: 'center', at: 'center', of: window });
			$(".ui-dialog-titlebar .ui-dialog-titlebar-close").css({"display":"block"});
	        $('.ui-dialog-buttonpane .ui-dialog-buttonset').css({"display":"block","float":"none"});
			$('.ui-dialog-buttonpane').find('button:contains("Add more products")').addClass('continue_btn');
			$('.ui-dialog-buttonpane').find('button:contains("Create Transfer List")').addClass('placeorder_btn');
			$('.ui-dialog-buttonpane').find('button:contains("Submit")').css({"float":"right"});
			var dlg=$(this);
			var html_cnt='';
			
				//$.post("<?=site_url("admin/jx_getsaved_item_incart")?>",{fid:pre_selected_fid,mid:<?php //echo $mid;?>},function(data){
				$.post(site_url+"/admin/part_jx_getsaved_item_incart",{partner_id:partner_id,transfer_option:transfer_option},function(data){
					if(data.status =='success')
					{
						$.each(data.saved_cart_itms.items,function(i,p){
								obj=p;
								$("#p_pid").attr("disabled",false);
								$(".add_product").val('Add');
								$("#p_pid").val("");
								template=$("#template tbody").html();
								template=template.replace(/%sno%/g,(i*1+1));
								template=template.replace(/%cart_rowid%/g,p.cart_rowid);
								template=template.replace(/%pimage%/g,p.pic);
								template=template.replace(/%pid%/g,p.pid);
								template=template.replace(/%itemid%/g,p.itemid);
								template=template.replace(/%menuid%/g,p.menuid);
								template=template.replace(/%attr%/g,p.attr);
								template=template.replace(/%pname%/g,p.name);
								template=template.replace(/%cat%/g,p.cat);
								template=template.replace(/%brand%/g,p.brand);
								template=template.replace(/%margin%/g,p.margin);
								if(p.oldmrp == '-')
									template=template.replace(/%dspmrp%/g,'none');
								else
									template=template.replace(/%dspmrp%/g,'block');

								if(p.key_member==0)
									template=template.replace(/%key_mem%/g,'none');
								else
									template=template.replace(/%key_mem%/g,'block');

								template=template.replace(/%mfee%/g,p.mem_fee);
								template=template.replace(/%ifee%/g,p.insurance_fee);
								template=template.replace(/%oldmrp%/g,p.oldmrp);
								template=template.replace(/%newmrp%/g,p.mrp);
								template=template.replace(/%mrp%/g,p.mrp);
								template=template.replace(/%price%/g,p.price);
								template=template.replace(/%lcost%/g,p.lcost);
								template=template.replace(/%stock%/g,p.stock);
								template=template.replace(/%pending_orders%/g,p.pending_orders);
								template=template.replace(/%confirm_stock%/g,p.confirm_stock);
								template=template.replace(/%margin_amt%/g,Math.round(p.price-p.lcost));
								template=template.replace(/%lcl_distribtor_mrgn%/g,p.local_distbtr_margin);
								
								var ttlcost=0;var cart_qty=0;
								if(p.stock > p.pending_orders )
								{
									ttlcost=format_number(p.svd_cartqty*p.price);
									cart_qty=p.svd_cartqty;
								}
								template=template.replace(/%cart_qty%/g,cart_qty);
								template=template.replace(/%ttllcost%/g,ttlcost);
								
								
								if(p.max_allowed_qty*1 == 0 && p.mp_mem_max_qty*1 == 0)
								{
									template=template.replace(/%max_oqty%/g,500);
									template=template.replace(/%max_ord_qty%/g,"");
								}else
								{
									template=template.replace(/%max_oqty%/g,p.max_ord_qty);
									template=template.replace(/%max_ord_qty%/g,p.max_ord_qty);
									//template=template.replace(/%max_ord_qty%/g,"<span class='tip_popup max_qty_wrap' title='Maximum Allowed Quantity'>/&nbsp;("+p.max_ord_qty+"&nbsp;Qty)</span>");
								}
								
								template=template.replace(/%mrp%/g,p.mrp);

								html_cnt += template;
								
						});
						$("#cart_prod_temp tbody").html(html_cnt);
						
						show_ttl_cart_qty();
						
						tooltip_popup();
						change_total_subtotal();
						$('.cart-footer div').show();
						$(".confirm_bloc").show();
						$("#cart_prod_temp thead").show();

					}
					else
					{
						$("#cart_prod_temp thead").hide();
						$(".confirm_bloc").hide();
						$('.cart-footer div').hide();
						$("#cart_prod_temp tbody").html("<tr><td colspan='6' align='center'><div class='empty-cart-message' >There are no items in this cart. </div></td></tr>");
					}
								
				},'json');	

		},
		buttons:{
			'Create Transfer List':function(){
				var msg='';
				var errors=0;
					if( $(".cart-footer .partner_ref_no").val() == '' )
					{
						errors+=1;
						msg+=errors+". Please enter partner reference number.\n";
					}
					if( $(".cart-footer .transfer_exp_date").val() == '' )
					{
						errors+=1;
						msg+=errors+". Please select expected transfer date.\n";
					}
					if( $(".cart-footer .transfer_remarks").val() == '' )
					{
						errors+=1;
						msg+=errors+". Please enter transfer remarks.\n";
					}
					var ttlqty=0;
					$.each($("#cart_prod_temp .qty"),function(i,qty) {
						var qty=$(qty).val();
						if(qty == '' || qty == NaN ){
							errors+=1;
							msg+=errors+". Quantity fields cannot be empty\n";
						}
						/*else if( $(qty).val() == 0 ) {
							errors+=1;
							msg+=errors+". Quantity fields be 0 quantity\n";
						}*/
						ttlqty += qty;
					});
					
					if(ttlqty==0)
					{
						errors+=1;
						msg+=errors+". Quantity not defined for any deals\n";
					}
					if(errors)
					{
						alert(msg);
						return false;
					}
					
					if(confirm("Are you sure want to submit details to transfer?") )
						$("#order_form").submit();
			},
			'Add more products':function() {
				$(this).dialog('close');
			}
		}
	});

	function tooltip_popup(){

		Tipped.create('.tip_popup',{
		 skin: 'black',
		  hook: 'topleft',
		  hideOn: false,
		  closeButton: true,
			opacity: .5,
			hideAfter: 200,
		 });

	}
	
	$('input[name="srch_deals"]').live('keyup',function(){
		filter_deallist($(this).val(),'search');
		search_othr_deal();
		$('.src_deals').hide();
		$('.unsrc_deals').hide();
	});

	function show_ttl_cart_qty()
	{
		var ttl_cart_qty=0;
		$.each($("#cart_prod_temp .qty"),function(i,qty) {
			ttl_cart_qty +=parseInt($(qty).val());
		});

		$("#cart_prod_temp .ttl_cart_qty").html(ttl_cart_qty);
	}

	$(window).on("resize scroll",function() {
       $("#cart_prod_div").dialog("option","position",["center","center"]); 
    });
	
</script>
<style>
.quicklook_btn {
	background: none repeat scroll 0 0 #000000 !important;
	border-color: #000;
	color: #FFFFFF !important;
	font-size: 10px;
	font-weight: bold;
	height: 17px;
	line-height: 16px;
	padding: 3px 6px;
	text-shadow: none;
	width: 66px;
}
.button-tiny {
	padding: 0 2.92px;
}
.button {margin: 3px;}
.button_fit {padding: 4px; margin: 2px; }
.ttl_cart_qty {
	padding: 6px; font-weight: bold;
}
.pcart_extra b
{
	width: 25%;
}
.pcart_extra span 
{
	width: 70%;
}
.p_confirm_stk span { font-weight: bold; }
</style>
<?php
