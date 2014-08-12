<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/stock_intake.css">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/plot.css">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/deals.css">
<style>
.Brands_bychar_list_head .close_btn
{
	width:auto !important;
}
.prod_det_font_wrap
{
	font-weight:bold;font-size:11px;color:#444;
}
.stk_dlg
{
	cursor:pointer;
	padding:0px !important;
	width:42px !important;
	background: none repeat scroll 0 0 #fdfdfd;
}

.stk_wrap {
    float: left;
    width: 48%;
    margin-right: 2%;
}
.total_wrap {
    background: none repeat scroll 0 0 #F1F1F1;
    float: left;
    font-size: 13px !important;
    font-weight: bold !important;
    margin: 2px;
    padding: 5px 6px;
}
.src_deals {
    background: none repeat scroll 0 0 #F1F1F1;
    float: left;
    font-size: 13px !important;
    font-weight: bold !important;
    margin: 2px;
    padding: 5px 6px;
    color:green;
}
.unsrc_deals {
    background: none repeat scroll 0 0 #F1F1F1;
    float: left;
    font-size: 13px !important;
    font-weight: bold !important;
    margin: 2px;
    padding: 5px 6px;
    color:red;
}
.ttl{
	color:#000000;
}
.sk_deal_blk_wrap {
   float:none !important;
    margin: 0 0.5% 0;
}
.ven_lst
{
	color: #0000FF;
    display: inline-block;
    font-weight: bold;
    margin: 6px 8px;
    cursor:pointer;
}
.det
{
	font-size: 14px;
}
.det span.sno
{
	 display: inline-block;
    padding: 4px 6px;
}
.det span.title
{
	
}
.sk_deal_header_wrap
{
	
}
.sk_deal_content_wrap
{
	height: 610px;
    margin: 0 0 0 0.5%;
    overflow: auto;
}
.sk_deal_content_wrap td
{
	border-bottom: 1px solid #FFFFFF;
    font-size: 12px;
    padding: 5px;
    text-transform: capitalize;
}
.sk_deal_filter_blk_wrap
{
	margin: 12px 0;
}
</style>

<div class="container">
	<h2 style="float: left;margin:0px;width: 30%">Warehouse Summary</h2>
	<div class="filters_wrap"><img class="search_img_wrap" src="<?php echo base_url().'images/search_icon.png'?>">
		<input type="text" name="srch_product" class="deal_prd_blk inp" placeholder="Search by Product Name" >
	</div>
	<div class="filters_wrap"><img class="search_img_wrap" src="<?php echo base_url().'images/search_icon.png'?>">
		<input type="text" name="prd_barcode" class="prd_barcode_srch inp" placeholder="Search by Barcode" >
	</div>
	<div class="legends_outer_wrap" style="width:19% !important">
		<span class="legends_color_notsrc_wrap">&nbsp;</span> - Not sourceable &nbsp; &nbsp; &nbsp;
		<span class="legends_color_src_wrap">&nbsp;</span> - sourceable &nbsp; &nbsp; &nbsp;
	</div>
	<div id="cus_jq_alpha_sort_wrap" >
	</div>
</div>

<div id="ven_list_dlg" title="Vendors List">
	<div class="ven_det">
	</div>
</div>	

<div id="prod_stk_det_dlg" title="Stock Details">
	<div class="stock_det_wrap">
	</div>
	<div class="fl_left" style="width:100%">
		<h4>Purchase Log</h4>
		<table id="ven_po_log" class="datagrid" width="100%">
			<thead>
				<tr>
					<th>Slno</th>
					<th>Date of Intake</th>
					<th>GRN ID</th>
					<th>Vendor Name</th>
					<th>Quantity</th>
					<th>MRP</th>
					<th>Purchase Price</th>
				</tr>
			</thead>
		
			<tbody>
			</tbody>
		</table>
		<div id="ven_log_pagination"></div>
	</div>
</div>

<script>
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
				tmpl += '		<a href="javascript:void(0)" chr="20">T20</a>';
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
	
	$('#cus_jq_alpha_sort_wrap').jqSuAlphaSort({title:"List of Categories",overview_title:"List of Deals",'char_click':function(chr,ele){ cat_bychar(chr)},'item_click':function(catid,ele){ product_list_bycat(0,catid,0,0)},'item_click_bybrand':function(brandid,ele){ product_list_bycat(brandid,0,0,0)}});
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
	$('.jq_alpha_sort_alphalist_vend_head').html('<div class="alphabet_header_wrap"><span><a id="cat_lab" class="cat_lst_tab">Category List</a></span><span><a id="brand_lab" style="margin-right:0px !important" class="brand_lst_tab">Brand List</a></span> <input type="text" name="search_name" class="search_blk inp" placeholder="Search by Name" ><img style="margin-top: 7px;" src="<?php echo base_url().'images/search_icon.png'?>"></div>');
	//product_list_bycat(0,0,0);
	$('.brand_lst_tab').addClass('selected_alpha_list');
	$('.jq_alpha_sort_overview_content').html('<div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
	
});

$('.Brands_bychar_list_content_listdata').live("click",function(){
	var brandid =$(this).attr('bid')*1;
	var catid =$(this).attr('cid')*1;
	
	$(this).toggleClass("selected_class");
	  if($(this).hasClass('selected_class'))
	  {
	  	$('.selected_class').removeClass('selected_class');
	  	$(this).addClass('selected_class');
	  	product_list_bycat(brandid,catid,0);
	  }
	  else
	  { 
	  	product_list_bycat(0,catid,0);
	  }
	$('.left_filter_wrap').show();
});

$('#sourceable').live('change',function(){
	var s=$('#stock_filter').val();// sourceble filter dropdown value
	var is_sourceable=$('#sourceable').val();
	var i=0; 
	var ttl_avg_pur=0;
	var ttl_mrp=0;
	var ttl_qty=0;
	var src=0;
	var unsrc=0;
	var ttl=$(this).attr('total');
	if(is_sourceable != 'choose')
	{
		if(s!='choose')
		{
			$(".prod_filter_wrap").each(function(){
			var	sourceable=$(this).attr('sourceable');
			var qty=$(this).attr('qty');
			
			if(s==1)
			{
				if(is_sourceable==sourceable && qty>0)
				{
					$(this).show();
					i++;
					if(sourceable==1)
						++src;
					if(sourceable==0)
						++unsrc;
					qty=$(this).attr('qty');
			 		mrp=$(this).attr('mrp');
			 		mrp=mrp.replace(/\,/g,'');
			 		mrp=parseInt(mrp,10);
			 		avg_pur=$(this).attr('avg_pur');
			 		avg_pur=avg_pur.replace(/\,/g,'');
			 		avg_pur=parseInt(avg_pur,10);
			 		ttl_avg_pur =Number(ttl_avg_pur) + Number(avg_pur);
			 		ttl_mrp =Number(ttl_mrp) + Number(mrp);
			 		ttl_qty =Number(ttl_qty) + Number(qty);
				}
				else 
				{
					$(this).hide();
				}
				
			}else if(s==0)
			{
				if(is_sourceable==sourceable && qty<=0)
				{
					$(this).show();
					i++;
					if(sourceable==1)
						++src;
					if(sourceable==0)
						++unsrc;
					qty=$(this).attr('qty');
			 		mrp=$(this).attr('mrp');
			 		mrp=mrp.replace(/\,/g,'');
			 		mrp=parseInt(mrp,10);
			 		avg_pur=$(this).attr('avg_pur');
			 		avg_pur=avg_pur.replace(/\,/g,'');
			 		avg_pur=parseInt(avg_pur,10);
			 		ttl_avg_pur =Number(ttl_avg_pur) + Number(avg_pur);
			 		ttl_mrp =Number(ttl_mrp) + Number(mrp);
			 		ttl_qty =Number(ttl_qty) + Number(qty);
				}
				else 
				{
					$(this).hide();
				}
			}	
			});	
		}
		else
		{
			$('#stock_filter').val('choose').trigger('click');
			$(".prod_filter_wrap").each(function(){
			var	sourceable=$(this).attr('sourceable');
			
			if(is_sourceable==sourceable)
			{
				$(this).show();
				i++;
				if(sourceable==1)
					++src;
				if(sourceable==0)
					++unsrc;
				qty=$(this).attr('qty');
		 		mrp=$(this).attr('mrp');
		 		mrp=mrp.replace(/\,/g,'');
		 		mrp=parseInt(mrp,10);
		 		avg_pur=$(this).attr('avg_pur');
		 		avg_pur=avg_pur.replace(/\,/g,'');
		 		avg_pur=parseInt(avg_pur,10);
		 		ttl_avg_pur =Number(ttl_avg_pur) + Number(avg_pur);
		 		ttl_mrp =Number(ttl_mrp) + Number(mrp);
		 		ttl_qty =Number(ttl_qty) + Number(qty);
			}
			else
			{
				$(this).hide();
			}
			});
		}
	}
	else
	{
		
		$(".prod_filter_wrap").each(function(){
			$(this).show();
			var	sourceable=$(this).attr('sourceable');
			
			i++;
			if(sourceable==1)
				++src;
			if(sourceable==0)
				++unsrc;
			qty=$(this).attr('qty');
	 		mrp=$(this).attr('mrp');
	 		mrp=mrp.replace(/\,/g,'');
	 		mrp=parseInt(mrp,10);
	 		avg_pur=$(this).attr('avg_pur');
	 		avg_pur=avg_pur.replace(/\,/g,'');
	 		avg_pur=parseInt(avg_pur,10);
	 		ttl_avg_pur =Number(ttl_avg_pur) + Number(avg_pur);
	 		ttl_mrp =Number(ttl_mrp) + Number(mrp);
	 		ttl_qty =Number(ttl_qty) + Number(qty);
	 	});
	}
	$('.total_wrap a.ttl').html(i+'/'+ttl);
	$('.src_deals a.ttl').html(src+'/'+ttl);
	$('.unsrc_deals a.ttl').html(unsrc+'/'+ttl);
	
 	$('.ttl_mrp').text(ttl_mrp+'.00');
	$('.ttl_avg').text(ttl_avg_pur+'.00');
});

$('#stock_filter').live('change',function(){
	var s=$('#sourceable').val();// sourceble filter dropdown value
	var stk_filt=$('#stock_filter').val();
	var ttl=$(this).attr('total');
	var src=0;
	var unsrc=0;
	if(stk_filt == 1)
	{
		var i=0;
		var ttl_mrp=0;
		var ttl_avg_pur=0;
		$('.prod_filter_wrap').each(function(){
			var qty=$(this).attr('qty');
			var sourceable=$(this).attr('sourceable');
			
		if(s != 'choose')// Check whether sourceble dropdown selected
		{
			if(qty > 0 && s==sourceable)
			{
				i++;
				if(sourceable==1)
					++src;
				if(sourceable==0)
					++unsrc;
					
				var prd_mrp=$(this).attr('mrp');
				prd_mrp=prd_mrp.replace(/\,/g,'');
		 		prd_mrp=parseInt(prd_mrp,10);
		 		avg_pur=$(this).attr('avg_pur');
		 		avg_pur=avg_pur.replace(/\,/g,'');
		 		avg_pur=parseInt(avg_pur,10);
		 		ttl_avg_pur =Number(ttl_avg_pur) + Number(avg_pur);
				ttl_mrp=Number(ttl_mrp)+Number(prd_mrp);
				$(this).show();
			}
			else
			{
				$(this).hide();
			}
		}else
		{
			if(qty > 0)
			{
				i++;
				if(sourceable==1)
					++src;
				if(sourceable==0)
					++unsrc;
				
				var prd_mrp=$(this).attr('mrp');
				prd_mrp=prd_mrp.replace(/\,/g,'');
		 		prd_mrp=parseInt(prd_mrp,10);
		 		avg_pur=$(this).attr('avg_pur');
		 		avg_pur=avg_pur.replace(/\,/g,'');
		 		avg_pur=parseInt(avg_pur,10);
		 		ttl_avg_pur =Number(ttl_avg_pur) + Number(avg_pur);
				ttl_mrp=Number(ttl_mrp)+Number(prd_mrp);
				$(this).show();
			}
			else
			{
				$(this).hide();
			}
		}
			
		});
	$('.total_wrap a.ttl').html(i+'/'+ttl);
	$('.src_deals a.ttl').html(src+'/'+ttl);
	$('.unsrc_deals a.ttl').html(unsrc+'/'+ttl);	
	$('.ttl_mrp').html(ttl_mrp+'.00');	
	$('.ttl_avg').html(ttl_avg_pur+'.00');	
		
	}else if(stk_filt == 0)
	{
		var i=0;
		var ttl_mrp=0;
		var ttl_avg_pur=0;
		var src=0;
		var unsrc=0;
		$('.prod_filter_wrap').each(function(){
			var qty=$(this).attr('qty');
			var sourceable=$(this).attr('sourceable');
			
		if(s != 'choose')// Check whether sourceble dropdown selected
		{
			if(qty == 0 && s==sourceable)
			{
				i++;
				if(sourceable==1)
					++src;
				if(sourceable==0)
					++unsrc;
				var prd_mrp=$(this).attr('mrp');
				prd_mrp=prd_mrp.replace(/\,/g,'');
		 		prd_mrp=parseInt(prd_mrp,10);
		 		ttl_mrp=Number(ttl_mrp)+Number(prd_mrp);
		 		avg_pur=$(this).attr('avg_pur');
		 		avg_pur=avg_pur.replace(/\,/g,'');
		 		avg_pur=parseInt(avg_pur,10);
		 		ttl_avg_pur =Number(ttl_avg_pur) + Number(avg_pur);
				$(this).show();
			}
			else
			{
				$(this).hide();	
			}
		}
		else
		{
			if(qty == 0)
			{
				i++;
				if(sourceable==1)
					++src;
				if(sourceable==0)
					++unsrc;
				var prd_mrp=$(this).attr('mrp');
				prd_mrp=prd_mrp.replace(/\,/g,'');
		 		prd_mrp=parseInt(prd_mrp,10);
		 		ttl_mrp=Number(ttl_mrp)+Number(prd_mrp);
		 		avg_pur=$(this).attr('avg_pur');
		 		avg_pur=avg_pur.replace(/\,/g,'');
		 		avg_pur=parseInt(avg_pur,10);
		 		ttl_avg_pur =Number(ttl_avg_pur) + Number(avg_pur);
				$(this).show();
			}
			else
			{
				$(this).hide();	
			}
		}	
		});
		$('.total_wrap a.ttl').html(i+'/'+ttl);
		$('.src_deals a.ttl').html(src+'/'+ttl);
		$('.unsrc_deals a.ttl').html(unsrc+'/'+ttl);	
		$('.ttl_mrp').html(ttl_mrp+'.00');	
		$('.ttl_avg').html(ttl_avg_pur+'.00');	
	}
	else
	{
		var i=0;
		var ttl_mrp=0;
		var ttl_avg_pur=0;
		var src=0;
		var unsrc=0;
		$('.prod_filter_wrap').each(function(){
			i++;
			var sourceable=$(this).attr('sourceable');
			if(sourceable==1)
				++src;
			if(sourceable==0)
				++unsrc;
			var prd_mrp=$(this).attr('mrp');
			prd_mrp=prd_mrp.replace(/\,/g,'');
	 		prd_mrp=parseInt(prd_mrp,10);
	 		ttl_mrp=Number(ttl_mrp)+Number(prd_mrp);
	 		avg_pur=$(this).attr('avg_pur');
	 		avg_pur=avg_pur.replace(/\,/g,'');
	 		avg_pur=parseInt(avg_pur,10);
	 		ttl_avg_pur =Number(ttl_avg_pur) + Number(avg_pur);
			$(this).show();
		});
		$('.total_wrap a.ttl').html(i+'/'+ttl);
		$('.src_deals a.ttl').html(src+'/'+ttl);
		$('.unsrc_deals a.ttl').html(unsrc+'/'+ttl);	
		$('.ttl_mrp').html(ttl_mrp+'.00');
		$('.ttl_avg').html(ttl_avg_pur+'.00');	
	}
	
});

$('input[name="search_name"]').live('keyup',function(){
	var chr=$('input[name="search_name"]').val();
	var i=0; 
	$(".jq_alpha_sort_alphalist_itemlist_divwrap").each(function(){
		name=$(this).attr('name');
		if(name.match(chr,'ig'))
		{
			$(this).show();
			i++;
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


$('input[name="srch_product"]').live('keyup',function(){
	$('.selected_class').removeClass('selected_class');
	var chr=$('input[name="srch_product"]').val();
	
	$(".prod_filter_wrap").each(function(){
		name=$(this).attr('name');
		if(name.match(chr,'ig'))
				$(this).show();
		else
				$(this).hide();
	});
});

$('input[name="prd_barcode"]').live('keyup',function(){
	$('.selected_class').removeClass('selected_class');
	var chr=$('input[name="prd_barcode"]').val();
	$.post(site_url+'/admin/jx_prd_srch_bybarcode',{chr:chr},function(resp){
	if(resp.status == 'error')
		{
			alert("Barcode not found");
			return false;
	    }
		else
		{
			var pid=resp.p_det.product_id;
			product_list_bycat(0,0,pid);
		}
	},'json');
});

$('.cat_lst_tab').live('click',function(){
	$('#cat_lab').val('1');
	$('#brand_lab').val('0');
	product_list_bycat(0,0,0);
	
	$(".jq_alpha_sort_alphalist_char a:eq(0)").trigger('click');
	$('.cat_lst_tab').removeClass("selected_alpha_list");
	$('.brand_lst_tab').addClass("selected_alpha_list");
	$('.Brands_bychar_list_content').html('<div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
});

$('.brand_lst_tab').live('click',function(){
	product_list_bycat(0,0,0);
	$('#cat_lab').val('0');
	$('#brand_lab').val('1');
	//$('.brands_bychar_list').html('');
	$(".jq_alpha_sort_alphalist_char a:eq(0)").trigger('click');
	$('.brand_lst_tab').removeClass("selected_alpha_list");
	$('.cat_lst_tab').addClass("selected_alpha_list");
	$('.Brands_bychar_list_content').html('<div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
});


$('.stock_det_close').live("click",function(){
	var id=$(this).attr('refid');
	$('.stock_det_'+id).hide();
});

function product_list_bycat(brandid,catid,product_id)
{
	//$('.ven_lst').html('');
	$('input[name="srch_product"]').val('');
	$('.total_wrap a').html('0');
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
				$.each(resp.brand_list,function(i,b){
					brand_linkedcat_html+='<a class="Brands_bychar_list_content_listdata" cid="'+b.catid+'" bid="'+b.brandid+'">'+b.brand_name+'</a>';
					$('.Brands_bychar_list_head').html('<span class="close_btn button button-tiny button-rounded ">Hide</span><h4>List of Brand for <i>'+b.category_name+'</i></h4>');
				});
			}
			$('.Brands_bychar_list_content').html(brand_linkedcat_html);
		});
	}else if(catid == 0 && brandid!=0)
	{
		$('.Brands_bychar_list_content').html('<div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
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
					$('.Brands_bychar_list_head').html('<span class="close_btn button button-tiny button-rounded ">Hide</span><h4>List of Categories for <i>'+b.brand_name+'</i></h4>');
				});
			}
			$('.Brands_bychar_list_content').html(cat_linkedcat_html);
		});
	}
	
	$('.sk_deal_container').html('<div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
	$.post(site_url+'/admin/jx_warehouse_product_stock_det',{brandid:brandid,catid:catid,product_id:product_id},function(resp){
		$('.sk_deal_container').css('opacity','1');
		if(resp.status == 'error')
			{
				$('.sk_deal_container').html('<div class="page_alert_wrap">No Products Found</div>');
				return false;
		    }
			else
			{
				var d_lst = '';
				if(resp.ttl_prd !=0)
				{
					d_lst+='<div class="sk_deal_filter_blk_wrap">';
					d_lst+='<div class="fl_right"><a class="button button-tiny button-rounded" href="javascript:void(0)" onclick="print_warehouse_summary()" >Print summary</a></div>';
					d_lst+='<div class="fl_right" style="margin:3px"><b>Sourceable :</b> <select id="sourceable" total="'+resp.ttl_prd+'"><option value="choose">Choose</option><option value="1">Yes</option><option value="0">No</option></select></div>';
					d_lst+='<div class="fl_right" style="margin:3px"><b>Stock :</b> <select id="stock_filter" total="'+resp.ttl_prd+'"><option value="choose">Choose</option><option value="1">Yes</option><option value="0">No</option></select></div>';
					d_lst+='<div class="fl_left" style="margin-left:3px"><span class="total_wrap">Total : <a class="ttl">'+resp.ttl_prd+'</a></span><span class="src_deals">Sourceable : <a class="ttl">'+resp.src+'</a></span><span class="unsrc_deals">Not Sourceable : <a class="ttl">'+resp.unsrc+'</a></span>';
					d_lst+='</a></span><span class="ven_lst" brandid='+brandid+' catid='+catid+'>Vendors List</span></div>';
					d_lst+='</div>';
					d_lst+='<div class="sk_deal_container" style="overflow:inherit">';
					d_lst+='<div class="sk_deal_header_wrap">';	
					d_lst+='<table class="sk_deal_blk_wrap" cellpadding="0" cellspacing="0" width="99%">'
					d_lst+='<thead><tr>';
					d_lst+='<th width="8%">Product ID</th><th>Product Name</th><th width="6%">DP</th><th width="6%" >MRP</th><th width="10%">Stock Qty</th><th width="9%">15 day Sales </th><th width="9%">30 day Sales </th><th width="9%">60 day Sales </th><th style="text-align:right" width="10%">Total Purchase Value</th>';
					d_lst+='</tr></thead></table>';
				 	d_lst+='</div>';
				 	
				 	d_lst+='<div class="sk_deal_content_wrap">';
				 		d_lst+='<table cellpadding="0" cellspacing="0" width="100%">';	
				 	$.each(resp.prod_details,function(i,p){
						
						if(p.is_sourceable == 1)
							var background='background:none repeat scroll 0 0 rgba(170, 255, 170, 0.8) !important'/*#40FC36*/;
						else
							var background='background:none repeat scroll 0 0 #FFAAAA !important';
						
						d_lst+='<tr style="'+background+'" class="prod_filter_wrap" prdid_'+p.product_id+' sourceable="'+p.is_sourceable+'" name='+p.product+' pid='+p.id+' bid='+p.brandid+' cid='+p.product_cat_id+' qty='+p.stock+' mrp='+p.mrp+' avg_pur='+p.avg_ttl_purchase+'>';
				 			d_lst+='<td width="8%"><span style="font-weight:bold;font-size:12px;color:green">'+p.id+'</span></td>';
		 					d_lst+='<td><span class="title"><a class="product_name" href="'+site_url+'/admin/product/'+p.id+'" target="_blank">'+p.product+'</a><span style="float:right;font-size:9px;color:green"></span></td>';
							d_lst+='<td width="6%"><span class="">'+p.price+'</span></td>';
							d_lst+='<td width="6%"><span class="prod_det_font_wrap">'+p.mrp+'</span></td>';
							d_lst+='<td width="10%"><button class="button-tiny button-flat stk_dlg" pid='+p.id+' pname='+p.product+'><span class="prod_det_font_wrap">'+p.stock+'</span></button></td>';
		 					d_lst+='<td style="text-align:center" width="9%"><span class="">'+p.f_orders+'</span></td>';
		 					d_lst+='<td style="text-align:center" width="9%"><span class="">'+p.t_orders+'</span></td>';
							d_lst+='<td style="text-align:center" width="9%"><span class="">'+p.s_orders+'</span></td>';	 					
		 					//d_lst+='<td style="text-align:right"><span class="prod_det_font_wrap">'+p.avg+'</span></td>';
		 					d_lst+='<td style="text-align:right" width="10%"><span class="prod_det_font_wrap">'+p.avg_ttl_purchase+'<br /><span style="font-size:">('+p.avg+'*'+p.stock+' )</span></td>';
		 				d_lst+='</tr>';
					});
					
						//d_lst+='<tr style="background:#ccc"><td colspan="2"><b>Total</b><td><td style="text-align:right"><b class="qty">'+resp.ttl_qty+'</b></td><td style="text-align:right"><b class="ttl_mrp">'+resp.ttl_mrp_value+'</b></td><td colspan="2" style="text-align:right"><b class="avg_pur">'+resp.ttl_avg_purchase+'</b></td></tr>';
					d_lst+='</table>';
					d_lst+='</div>';
					d_lst+='</div>';
				}
				else
				{
					d_lst+='<div style="text-align:center;margin-top:20%">No Products Found</div>';
				}
				$('.jq_alpha_sort_overview_content').html(d_lst);
				$("#sel_cat").chosen();
			}
	},'json');
	
}


function cat_bychar(ch)
{
	if($('#cat_lab').val() == 1)
	{
		$.post(site_url+'/admin/cat_list_bycharacter',{ch:ch},function(resp){
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
				$(".jq_alpha_sort_alphalist_itemlist_divwrap a:eq(0)").trigger('click');
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
				$('.jq_alpha_sort_alphalist_itemlist').html(b_list);
				$(".jq_alpha_sort_alphalist_itemlist_divwrap a:eq(0)").trigger('click');
			}
    	},'json');
	}else
	{
		$.post(site_url+'/admin/cat_list_bycharacter',{ch:ch},function(resp){
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
				$('.jq_alpha_sort_alphalist_itemlist').html(b_list);
				$(".jq_alpha_sort_alphalist_itemlist_divwrap a:eq(0)").trigger('click');
			}
    	},'json');
	}	
		
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

$('.ven_lst').live('click',function(){
	$('#ven_list_dlg').dialog('open');
});

$('.stk_dlg').live('click',function(){
	var pid=$(this).attr('pid');
	var trele=$(this).parents('tr:first');
	var pname=$('.product_name',trele).text();
	$('#prod_stk_det_dlg').data({'pid':pid,'pname':pname}).dialog('open');
});

$("#prod_stk_det_dlg" ).dialog({
		modal:true,
		autoOpen:false,
		width:'850',
		height:'550',
		autoResize:true,
		open:function(){
			var pid=$(this).data('pid');
			var pname=$(this).data('pname');
			$.post(site_url+'/admin/jx_prod_stk_det',{pid:pid,pname:pname},function(resp){
				if(resp.status == 'error')
					{
						$('.stock_det_wrap').html("<b style='font-weight:bold;color:#ff0000'>Stock not found</b>");
						return false;
				    }
					else
					{
						var stk_lst='';
						stk_lst+='<h3><a href="'+site_url+'/admin/product/'+pid+'" target="_blank">'+pname+'</h3>';
						stk_lst+='<div class="stk_wrap" >';
						stk_lst+='<h4 style="margin:0px">Total Stock : '+resp.in_stk+' </h4>';
						stk_lst+='<table class="datagrid" width="100%"><thead><tr><th>Barcode</th><th>Rackbin</th><th>MRP</th><th>Quantity</th>';
						stk_lst+='</tr></thead><tbody>';
						$.each(resp.stock_list,function(i,s){
							stk_lst+= '<tr><td><b>'+s.pbarcode+'</b></td><td><b>'+s.rbname+'</b></td><td><b>Rs '+s.mrp+'</b></td><td>'+s.s+'</td></tr>';
						});
						stk_lst+=  '</tbody></table></div>';
						
						
						stk_lst+='<div class="stk_wrap" >';
						stk_lst+='<h4 style="margin:0px">Purchase Log </h4>';
						stk_lst+='<table class="datagrid" width="100%"><thead><tr><th>Total Purchased</th><th>Total Sold</th><th>In Stock</th>';
						stk_lst+='</tr></thead><tbody>';
						stk_lst+= '<tr><td><b>'+resp.ttl_purchased+'</b></td>  <td>'+resp.stk_sold+'</td><td>'+resp.in_stk+'</td></tr>';
						stk_lst+=  '</tbody></table></div>';
					$('.stock_det_wrap').html(stk_lst);
					}
				},'json');
			
			load_ven_log(pid,0);
	},
	buttons: {
	    "Close": function() {
	    	$(this).dialog('close');
	   }
	} 
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
		load_ven_log(pid,pg);
	});
	


function print_warehouse_summary()
{
	var brandid =0;
	var catid =0;
	var selType = $('.jq_alpha_sort_alphalist_itemlist_divwrap.selected');
		if($('a',selType).attr('brandid') != undefined)
			brandid = $('a',selType).attr('brandid')*1;
		else if($('a',selType).attr('catid') != undefined)
			catid = $('a',selType).attr('catid')*1;
	if($('.brands_bychar_list .selected_class').length)
	{
		brandid = $('.brands_bychar_list .selected_class').attr('bid');
		catid = $('.brands_bychar_list .selected_class').attr('cid');
	}
	
	window.open(site_url+'/admin/print_brands_summary/'+brandid+'/'+catid);
}




$("#ven_list_dlg" ).dialog({
		modal:true,
		autoOpen:false,
		width:'550',
		height:'300',
		autoResize:true,
		open:function(){
			$('.ven_det').html('');
			brandid=$('.ven_lst').attr('brandid');
			catid=$('.ven_lst').attr('catid');
			$.post(site_url+'/admin/jx_load_vendors',{catid:catid,brandid:brandid},function(resp){
				if(resp.status == 'error')
					{
						alert("Vendors not found");
						return false;
				    }
					else
					{
						var ven_det='';
						var k=1;
						$.each(resp.ven_list,function(i,s){
							ven_det+= '<div class="det"><span class="sno">'+(k++)+'</span><span class="title"><a href="'+site_url+'/admin/vendor/'+s.vendor_id+'" target="_blank">'+s.vendor_name+'</span></div>';
						});
						
					$('.ven_det').html(ven_det);
					}
				},'json');
	},
	buttons: {
	    "Close": function() {
	    	$(this).dialog('close');
	   }
	} 
});

$('input[name="srch_product"]').live('keyup',function(){
	search_othr_products();
});

function search_othr_products()
{
	$('input[name="srch_product"]').autocomplete({
		source:site_url+'/admin/jx_searchprds_json/',
		minLength: 2,
		select:function(event, ui ){
			if(!$('.prdid_'+ui.item.id).length)
				product_list_bycat(0,0,ui.item.id);
		}
	});
}

function search(b_search,c_search)
{
	$('input[name="search_name"]').autocomplete({
		source:site_url+'/admin/jx_search_json/'+b_search+'/'+c_search,
		minLength: 2,
		
			select:function(event, ui ){
				if(b_search==1)
				{
					$('.jq_alpha_sort_alphalist_itemlist').html('<div class="jq_alpha_sort_alphalist_itemlist_divwrap selected" name="'+ui.item.value+'"><a  href="javascript:void(0)"  brandid="'+ui.item.id+'">'+ui.item.value+'</a></div>');
					product_list_bycat(ui.item.id,0,0);
					
				}else if(c_search==1)
				{
					$('.jq_alpha_sort_alphalist_itemlist').html('<div class="jq_alpha_sort_alphalist_itemlist_divwrap selected" name="'+ui.item.value+'"><a  href="javascript:void(0)"  catid="'+ui.item.id+'">'+ui.item.value+'</a></div>');
					product_list_bycat(0,ui.item.id,0);
				}
				
			}
			
	});
}
</script>


