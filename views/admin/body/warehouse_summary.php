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
	padding:0px !important;
	width:42px !important;
	background: none repeat scroll 0 0 #fdfdfd;
}
.stk_wrap
{
	background: none repeat scroll 0 0 #F1F1F1;
    font-size: 12px;
}
.stk_wrap table
{
	width:100%;
}
.stk_wrap table thead th
{
	background: none repeat scroll 0 0 #CCCCCC;
	padding:4px;
}
.stk_wrap table tbody td
{
	 background: none repeat scroll 0 0 #FDFDFD;
	 padding:4px;
}
</style>

<div class="container">
	<h2 style="float: left;margin:0px;width: 50%">Warehouse Summary</h2>
	<div class="filters_wrap"><img class="search_img_wrap" src="<?php echo base_url().'images/search_icon.png'?>">
		<input type="text" name="srch_product" class="deal_prd_blk inp" placeholder="Search by Product Name" >
	</div>
	<div class="legends_outer_wrap">
		<span class="legends_color_notsrc_wrap">&nbsp;</span> - Not Sourceble &nbsp; &nbsp; &nbsp;
		<span class="legends_color_src_wrap">&nbsp;</span> - Sourceble &nbsp; &nbsp; &nbsp;
	</div>
	<div id="cus_jq_alpha_sort_wrap" >
	</div>
</div>

<div id="prod_stk_det_dlg" title="Stock Details">
	<div class="stock_det_wrap">
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
	
	$('#cus_jq_alpha_sort_wrap').jqSuAlphaSort({title:"List of Categories",overview_title:"List of Deals",'char_click':function(chr,ele){ cat_bychar(chr)},'item_click':function(catid,ele){ product_list_bycat(0,catid,0)},'item_click_bybrand':function(brandid,ele){ product_list_bycat(brandid,0,0)}});
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
	product_list_bycat(0,0,2);
	$('.brand_lst_tab').addClass('selected_alpha_list');
});

$('.Brands_bychar_list_content_listdata').live("click",function(){
	var sel_brandid =$(this).attr('bid')*1;
	var sel_catid =$(this).attr('cid')*1;
	var ttl_avg_pur=0;
	var ttl_mrp=0;
	var ttl_qty=0;
	
	$(this).toggleClass("selected_class");
	if($(this).hasClass('selected_class'))
 	{
 		$('.selected_class').removeClass('selected_class');
  		$(this).addClass('selected_class');
  	 	$('.prod_filter_wrap').each(function(){
  	 		brandid=$(this).attr('bid');
  	 		catid=$(this).attr('cid');
  	 		if((sel_brandid==brandid) && (sel_catid==catid))
  	 		{
  	 			$(this).show();
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
  	 		}else
  	 		{
  	 			$(this).hide();	
  	 		}
  	 	});
  	 	$('.qty').text(ttl_qty);
 		$('.ttl_mrp').text(ttl_mrp);
 		$('.avg_pur').text(ttl_avg_pur); 
  	}
  	else
  	{ 
	  $('.prod_filter_wrap').each(function(){
	  	$(this).show();
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
	
	 	$('.qty').text(ttl_qty);
		$('.ttl_mrp').text(ttl_mrp);
		$('.avg_pur').text(ttl_avg_pur); 
  	}
	$('.left_filter_wrap').show();
});

$('input[name="search_name"]').live('keyup',function(){
	var chr=$('input[name="search_name"]').val();
	$(".jq_alpha_sort_alphalist_itemlist_divwrap").each(function(){
		name=$(this).attr('name');
		if(name.match(chr,'ig'))
				$(this).show();
		else
				$(this).hide();
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


$('.cat_lst_tab').live('click',function(){
	$('#cat_lab').val('1');
	$('#brand_lab').val('0');
	product_list_bycat(0,0);
	
	$(".jq_alpha_sort_alphalist_char a:eq(0)").trigger('click');
	$('.cat_lst_tab').removeClass("selected_alpha_list");
	$('.brand_lst_tab').addClass("selected_alpha_list");
});

$('.brand_lst_tab').live('click',function(){
	product_list_bycat(0,0);
	$('#cat_lab').val('0');
	$('#brand_lab').val('1');
	//$('.brands_bychar_list').html('');
	$(".jq_alpha_sort_alphalist_char a:eq(0)").trigger('click');
	$('.brand_lst_tab').removeClass("selected_alpha_list");
	$('.cat_lst_tab').addClass("selected_alpha_list");
});


$('.stock_det_close').live("click",function(){
	var id=$(this).attr('refid');
	$('.stock_det_'+id).hide();
});

function product_list_bycat(brandid,catid)
{
	$('input[name="srch_product"]').val('');
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
	$.post(site_url+'/admin/jx_warehouse_product_stock_det',{brandid:brandid,catid:catid},function(resp){
		$('.sk_deal_container').css('opacity','1');
		if(resp.status == 'error')
			{
				$('.sk_deal_container').html('<div class="page_alert_wrap">No Deals Found</div>');
				return false;
		    }
			else
			{
				var d_lst = '';
				d_lst+='<div class="sk_deal_filter_blk_wrap">';
				d_lst+='<div class="fl_right"><a class="button button-tiny button-rounded" href="javascript:void(0)" onclick="print_warehouse_summary('+brandid+','+catid+')" >Print summary</a></div>';
				d_lst+='<span class="total_wrap">Total Products : '+resp.ttl_prd+'</span></div>';
				
				d_lst+='<div class="sk_deal_container">';
				d_lst+='<table class="sk_deal_blk_wrap" cellpadding="0" cellspacing="0" width="99%">'
				d_lst+='<thead><tr>';
				d_lst+='<th width="8%">Product ID</th><th>Product Name</th><th width="6%" style="text-align:right">MRP</th><th style="text-align:right">Stock Qty</th><th style="text-align:right">MRP Value</th><th style="text-align:right">Avg Purchase Price</th><th style="text-align:right">Avg Total Purchase</th>';
				d_lst+='</tr></thead>';
			 	
			 	$.each(resp.prod_details,function(i,p){
					
					if(p.is_sourceable)
						var background='background:none repeat scroll 0 0 rgba(170, 255, 170, 0.8) !important'/*#40FC36*/;
					else
						var background='background:none repeat scroll 0 0 #FFAAAA !important';
					
					d_lst+='<tr style="'+background+'" class="prod_filter_wrap" name='+p.product_name+' bid='+p.brand_id+' cid='+p.product_cat_id+' qty='+p.stock+' mrp='+p.stock_value+' avg_pur='+p.avg_ttl_purchase+'>';
			 			d_lst+='<td><span style="font-weight:bold;font-size:12px;color:green">'+p.product_id+'</span></td>';
	 					d_lst+='<td><span class="title"><a class="product_name" target="_blank">'+p.product_name+'</a></td>';
						d_lst+='<td style="text-align:right"><span class="prod_det_font_wrap">'+p.mrp+'</span></td>';
						d_lst+='<td style="text-align:right"><button class="button-tiny button-flat stk_dlg" pid='+p.product_id+' pname='+p.product_name+'><span class="prod_det_font_wrap">'+p.stock+'</span></button></td>';
	 					d_lst+='<td style="text-align:right"><span class="prod_det_font_wrap"  style="font-size:12px;color:#000">'+p.stock_value+'</span></td>';
	 					
	 					d_lst+='<td style="text-align:right"><span class="prod_det_font_wrap">'+p.avg+'</span></td>';
	 					d_lst+='<td style="text-align:right"><span class="prod_det_font_wrap">'+p.avg_ttl_purchase+'</span></td>';
	 				d_lst+='</tr>';
				});
				
					d_lst+='<tr style="background:#ccc"><td colspan="2"><b>Total</b><td><td style="text-align:right"><b class="qty">'+resp.ttl_qty+'</b></td><td style="text-align:right"><b class="ttl_mrp">'+resp.ttl_mrp_value+'</b></td><td colspan="2" style="text-align:right"><b class="avg_pur">'+resp.ttl_avg_purchase+'</b></td></tr>';
				d_lst+='</table>';
				d_lst+='</div>';
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

$('.stk_dlg').live('click',function(){
	var pid=$(this).attr('pid');
	var trele=$(this).parents('tr:first');
	var pname=$('.product_name',trele).text();
	$('#prod_stk_det_dlg').data({'pid':pid,'pname':pname}).dialog('open');
});

$("#prod_stk_det_dlg" ).dialog({
		modal:true,
		autoOpen:false,
		width:'400',
		height:'250',
		autoResize:true,
		open:function(){
			var pid=$(this).data('pid');
			var pname=$(this).data('pname');
			$.post(site_url+'/admin/jx_prod_stk_det',{pid:pid,pname:pname},function(resp){
				if(resp.status == 'error')
					{
						alert("Stock not found");
						return false;
				    }
					else
					{
						var stk_lst='';
						stk_lst+='<h4>'+pname+'</h4>';
						stk_lst+='<div class="stk_wrap" >';
						stk_lst+='<table><thead><tr><th style="text-align:left">MRP</th><th style="text-align:left">Quantity</th></tr></thead><tbody>';
						$.each(resp.stock_list,function(i,s){
							stk_lst+= '<tr><td><b>Rs '+s.mrp+'</b></td>  <td>'+s.qty+'</td></tr>';
						});
						stk_lst+=  '</tbody></table>';
						$('.stock_det_wrap').html(stk_lst);
					}
				},'json');
	},
	buttons: {
	    "Close": function() {
	    	$(this).dialog('close');
	   }
	} 
});

function print_warehouse_summary(brandid,catid)
{
	if($('.Brands_bychar_list_content_listdata').hasClass('selected_class'))
		brandid=$('.selected_class').attr('bid');
	
	var print_url = site_url+'/admin/print_brands_summary/'+brandid+'/'+catid;
	window.open(print_url);
}
</script>


