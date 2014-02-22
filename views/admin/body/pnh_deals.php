<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/stock_intake.css">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/plot.css">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/deals.css">

<div class="container">
	<h2 style="float: left;margin:0px;width: 50%">Manage PNH Deals</h2>
		
		<div class="filters_wrap"><img class="search_img_wrap" src="<?php echo base_url().'images/search_icon.png'?>">
			<input type="text" name="srch_deals" class="deal_prd_blk inp" placeholder="Search by Deal Name" >
		</div>
		<div class="legends_outer_wrap">
			<span class="legends_color_notsrc_wrap">&nbsp;</span> - Out Of Stock &nbsp; &nbsp; &nbsp;
			<span class="legends_color_src_wrap">&nbsp;</span> - In Stock &nbsp; &nbsp; &nbsp;
		</div>
		<div id="cus_jq_alpha_sort_wrap" >
		</div>
		
		
</div>

<script>
function endisable_sel(act,brandid,catid)
{
	//alert(brandid);
	var ids=[];
	$(".sel:checked").each(function(){
		ids.push($(this).val());
	});
	ids=ids.join(",");
	$.post(site_url+'/admin/pnh_pub_unpub_deals',{ids:ids,act:act},function(resp){
	if(resp.status == 'error')
	{
		
	}
	else
	{
		
	}
},'json');
	deallist_bycat(brandid,catid,0);
}

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
	
	$('#cus_jq_alpha_sort_wrap').jqSuAlphaSort({title:"List of Categories",overview_title:"List of Deals",'char_click':function(chr,ele){ cat_bychar(chr)},'item_click':function(catid,ele){ deallist_bycat(0,catid,0,0)},'item_click_bybrand':function(brandid,ele){ deallist_bycat(brandid,0,0,0)}});
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
	deallist_bycat(0,0,2);
	$('.brand_lst_tab').addClass('selected_alpha_list');
	
	//$(".sel_all, .sel").attr("checked",false);
	
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
	  	 	//deallist_bycat(brandid,catid,0);
	  	 	filter_deallist($(this).text(),'brand');
	  	}
	  	else
	  	{ 
		  	//deallist_bycat(0,catid,0);
	  		filter_deallist('','all');
	  	}
		$('.left_filter_wrap').show();
});

$('.all').live('click',function(){
	var catid =$(this).attr('catid')*1;
	var brandid =$(this).attr('brandid')*1;
	$('.all').addClass("selected_type");
	if(catid !=0 && brandid == 0)
		deallist_bycat(0,catid,0);
	else if(catid ==0 && brandid != 0)
		deallist_bycat(brandid,0,0);
	else if(catid !=0 && brandid != 0)
		deallist_bycat(brandid,catid,0);
	else
		deallist_bycat(0,0,0);
});

$('.latest').live('click',function(){
	var catid =$(this).attr('catid')*1;
	var brandid =$(this).attr('brandid')*1;
	if(catid !=0 && brandid == 0)
		deallist_bycat(0,catid,1);
	else if(catid ==0 && brandid != 0)
		deallist_bycat(brandid,0,1);
	else	
	deallist_bycat(brandid,catid,1);
	$('.latest').addClass("selected_type");
});

$('.most').live('click',function(){
	var catid =$(this).attr('catid')*1;
	var brandid =$(this).attr('brandid')*1;
	if(catid !=0 && brandid == 0)
		deallist_bycat(0,catid,2);
	else if(catid ==0 && brandid != 0)
		deallist_bycat(brandid,0,2);
	else	
		deallist_bycat(brandid,catid,2);
	$('.most').addClass("selected_type");
	
});

$('.cat_lst_tab').live('click',function(){
	$('#cat_lab').val('1');
	$('#brand_lab').val('0');
	deallist_bycat(0,0,0);
	
	$(".jq_alpha_sort_alphalist_char a:eq(0)").trigger('click');
	$('.cat_lst_tab').removeClass("selected_alpha_list");
	$('.brand_lst_tab').addClass("selected_alpha_list");
});

$('.brand_lst_tab').live('click',function(){
	deallist_bycat(0,0,0);
	$('#cat_lab').val('0');
	$('#brand_lab').val('1');
	//$('.brands_bychar_list').html('');
	 $(".jq_alpha_sort_alphalist_char a:eq(0)").trigger('click');
	$('.brand_lst_tab').removeClass("selected_alpha_list");
	$('.cat_lst_tab').addClass("selected_alpha_list");
});


$('select[name="publish_wrap"]').live('change',function(){
	var v=$(this).val();
	published_deals(v);
});

function published_deals(v)
{
	filter_deallist('','all');
	return ;
	if(v != 'all')
	{
		var i=1;
		$('.sk_deal_blk_wrap thead').show();
		$(".sk_deal_filter_wrap").each(function(){
			publish=parseInt($(this).attr('publish')*1);
			if(publish == v)
			{
				$('.total_wrap').html("Total Deals : "+(i++));
				$(this).show();
			}
			else 
			{
				$(this).hide();
			}
		});
	}
	else
	{
		return;
	}
}


/*$('.tgl_stock_combo').live('click',function(){
	var trele=$(this).parents('tr:first');
	var ref_id  = $(this).attr('ref_id');
	
	$.post(site_url+'/admin/pnh_jx_dealstock_det',{refid:ref_id,is_pnh:1},function(resp){
			if(resp.status == 'error')
			{
				$('.stock_det_'+ref_id).html("No Details found");
			}
			else
			{
				
				var qcktiphtml = '<span style="float:right;color:red;cursor:pointer" refid="'+ref_id+'" class="stock_det_close">X</span> <div style="float:left;width:100%">';
					qcktiphtml += '<table width="100%" border=1 class="datagrid" cellpadding=3 cellspacing=0>';
					qcktiphtml += '<thead><tr><th>Product Name</th><th>Stock</th></tr></thead><tbody>';
					$.each(resp.itm_stk_det,function(a,b){
						qcktiphtml+='<tr>';
						qcktiphtml+='	<td width="80%" style="font-size:10px">'+b.product_name+'</td>';
						qcktiphtml+='	<td width="20%" style="font-size:10px">'+b.stk+'</td>';
						qcktiphtml+='</tr>';
					});
					qcktiphtml += '</tbody></table></div>';
					$('.stock_det_'+ref_id).html(qcktiphtml);
					$('.stock_det_'+ref_id).show();
			}
			
		},'json');
		//$('.stock_det',ele.parent().parent()).html("Loading...").show(); 
	
});*/

$('input[name="check_status"]').live("click",function(){
	if(confirm("Are you sure want to change published status"))
	{
		itemid=$(this).attr('itemid')*1;
		brandid=$(this).attr('brandid')*1;
		catid=$(this).attr('catid')*1;
		i=$(this).attr('publish')*1;
		$.getJSON(site_url+'/admin/pnh_pub_deal/'+itemid+'/'+i+'/0',{},function(resp){
			if(resp.status == 'error')
			{
				alert(resp.error);
				return false;
		    }
			else
			{
				deallist_bycat(brandid,catid,$('.filter_opts .selected_type').attr('val'));			
			}
		});
	}
	else
	{
		return false;
	}
});


$('input[name="search_name"]').live('keyup',function(){
	var chr=$('input[name="search_name"]').val();
	
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
	});
});

function filter_deallist(tag,by)
{
	var srch_inp =  $('input[name="srch_deals"]').val();
	var publish_status = $('select[name="publish_wrap"]').val();
	var bc_sel = ($('.Brands_bychar_list_content_listdata.selected_class').length?($('.Brands_bychar_list_content_listdata.selected_class').text()):0);
	$(".sk_deal_filter_wrap").each(function(){
			
			search_text=$(this).attr('name')+' '+$(this).attr('brand')+' '+$(this).attr('category');
			
			var row_stat = 1;
				if(publish_status != $(this).attr('publish') && (publish_status != 'all'))
					row_stat = 0;
			
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
				$(this).show();
			else
				$(this).hide();
		
	});

	$('.total_wrap').html("Total Deals : "+$(".sk_deal_filter_wrap:visible").length);
}

$('input[name="srch_deals"]').live('keyup',function(){
	filter_deallist($(this).val(),'search');
});

$('.stock_det_close').live("click",function(){
	var id=$(this).attr('refid');
	$('.stock_det_'+id).hide();
});

function deallist_bycat(brandid,catid,type)
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
	
	//capture saved tr
	//$('.sk_deal_container tbody tr').each(function(){
		
	//});		
	
	$('.sk_deal_container').html('<div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
	//$('.sk_deal_container').css('opacity','0.5');
	$.post(site_url+'/admin/jx_deallist_bycat',{brandid:brandid,catid:catid,type:type},function(resp){
		$('.sk_deal_container').css('opacity','1');
		//$('.sk_deal_container').css('opacity','1');
		if(resp.status == 'error')
			{
				$('.sk_deal_container').html('<div class="page_alert_wrap">No Deals Found</div>');
				return false;
		    }
			else
			{
				var enable='checked="checked"';
				var d_lst = '';
				d_lst+='<div class="sk_deal_filter_blk_wrap"><span>';
				
				d_lst+='</span>';
				d_lst+='<span class="left_filter_mrp_wrap">MRP Filter : <input type="text" class="inp" id="f_from" size=4> to <input type="text" class="inp" id="f_to" size=4> <button type="button" style="margin-top:-5px" class="button button-rounded button-action button-tiny" onclick="filter_deals_bymrp()">Filter</button></span>';
				//d_lst+='<span class="ttl_deals_wrap">Total Deals : '+resp.ttl_deals+'</span>';
				d_lst+='<span class="publish_wrap"><b>Published :</b> <select name="publish_wrap"><option value="all">Choose</option><option value="1" selected >Yes</option><option value="0">No</option></select></span>';
				d_lst+='<span class="left_filter_wrap"><span class="filter_opts"><a class="most" href="javascript:void(0)" val="2"  brandid="'+brandid+'" catid="'+catid+'">Most</a></span><span><a href="javascript:void(0)" class="latest" val="1" brandid="'+brandid+'" catid="'+catid+'" >Latest</a></span><span><a href="javascript:void(0)" val="0" class="all" brandid="'+brandid+'" catid="'+catid+'" style="border-right:none !important;width:34.4%">Clear</a></span></span>';
				d_lst+='<span class="total_wrap">Total Deals : '+resp.ttl_deals+'</span></div>';
				d_lst+='<div class="sk_deal_container">';
				d_lst+='<table class="sk_deal_blk_wrap" cellpadding="0" cellspacing="0" width="99%">'
				d_lst+='<thead><tr>';
				d_lst+='<th width="6%">PNH ID</th><th>Deal Name</th><th width="8%">Stock</th><th width="10%" style="">Brand</th><th width="15%">Category</th><th width="6%">MRP</th><th width="10%">DP/Offer Price</th><th width="12%">Published/Actions<br />';
				if(resp.has_user_edit)
				{
					d_lst+='<input type="checkbox" class="sel_all"><button type="button" class="disab_all" style="padding:2px !important" onclick="endisable_sel('+0+','+brandid+','+catid+')">Disable</button> <button type="button" class="enab_all" style="padding:2px !important" onclick="endisable_sel('+1+','+brandid+','+catid+')">Enable</button>';	
				}
				
				d_lst+='</th></tr></thead>';
			 	$.each(resp.deals_lst,function(i,d){
					if(d.allow_order.length)
					{
						dstock = 'In Stock';
						var background='background:none repeat scroll 0 0 rgba(170, 255, 170, 0.8) !important'/*#40FC36*/;
					}else
					{
						dstock = 'Out of Stock';
						var background='background:none repeat scroll 0 0 #FFAAAA !important';
					}
					if(d.publish==1)
					{
						var enable='checked="checked"';
						
					}else
					{
						var enable='';
						
					} //
						d_lst+='<tr style="'+background+'" class="sk_deal_filter_wrap deals_'+d.catid+'" publish="'+d.publish+'" mrp="'+d.orgprice+'" name="'+d.name+'" brand="'+d.brand+'" category="'+d.category+'" ref_id="'+d.itemid+'">';
				 			//d_lst+='<img src="'+images_url+'/items/small/'+d.pic+'.jpg'+'">';
		 					d_lst+='<td><span>'+d.pnh_id+'</span></td>';
		 					d_lst+='<td><span class="title"><a target="_blank" href="'+site_url+'/admin/pnh_deal/'+d.itemid+'">'+d.name+'</a><div class="stock_det_'+d.itemid+'"></div></td>';
							d_lst+='<td><a href="javascript:void(0)" dealid="'+d.itemid+'" class="tgl_stock_combo deal_stock ">'+(dstock)+'</a></td>';//
		 					d_lst+='<td><span><a target="_blank" href="'+site_url+'/admin/viewbrand/'+d.brandid+'">'+d.brand+'</a></span></td>';
		 					d_lst+='<td><span><a target="_blank" href="'+site_url+'/admin/viewcat/'+d.catid+'">'+d.category+'</a></span></td>';
		 					d_lst+='<td><span class="mrp">'+d.orgprice+'</span></td>';
		 					d_lst+='<td align="center"><span>'+d.price+'</span></td>';
		 					
		 					d_lst+='<td>';
		 					if(resp.has_user_edit)
		 					{
		 						d_lst+='	<input type="checkbox" class="sel" value="'+d.itemid+'" name="check_status" publish="'+d.publish+'" brandid="'+brandid+'" catid="'+catid+'" dealid="'+d.dealid+'" itemid="'+d.itemid+'" '+enable+'>';
								d_lst+='	<a target="_blank" href="'+site_url+'/admin/pnh_editdeal/'+d.itemid+'"><img style="margin-left:10px" src="'+base_url+'/images/pencil.png"></a> <a target="_blank" href="'+site_url+'/admin/pnh_deal/'+d.itemid+'"><img style="margin-left:10px" src="'+base_url+'/images/preview.png"></a>';	
		 					}else
		 					{
		 						d_lst+='<a target="_blank" href="'+site_url+'/admin/pnh_deal/'+d.itemid+'"><img style="margin-left:10px" src="'+base_url+'/images/preview.png"></a>';	
		 					}
		 					d_lst+='</td>';
	 					d_lst+='</tr>';
				});
				
				d_lst+='</table>';
				d_lst+='</div>';
				
				//polist_byvendor(vids[0]);
				$('.jq_alpha_sort_overview_content').html(d_lst);

                // Call the plugin
                $(".jq_alpha_sort_overview_content .deal_stock").dealstock();
                        
				$("#sel_cat").chosen();
				if(resp.type == 1)
				{
					//$('.all').removeClass('selected_type');
					$('.most').removeClass('selected_type');
					$('.latest').addClass('selected_type');
				}else if(resp.type == 0)
				{
					//$('.all').addClass('selected_type');
					$('.latest').removeClass('selected_type');
					$('.most').removeClass('selected_type');
				}
				else if(resp.type == 2)
				{
					//$('.all').removeClass('selected_type');
					$('.most').addClass('selected_type');
					$('.latest').removeClass('selected_type');
				}

				$('select[name="publish_wrap"]').trigger('change');
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
					//polist_byvendor(vids[0]);
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
					//polist_byvendor(vids[0]);
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
	$(".sk_deal_filter_wrap").each(function(){
		$(this).show();
		mrp=parseInt($(this).attr('mrp')*1);
		if(mrp>=from && mrp<=to)
		{
				valid_mrp.push(mrp);
				$(this).show();
		}
		else
		{
				$(this).hide();
		}
	});
	if(valid_mrp.length == 0)
	{
		$('.sk_deal_container').html('<div class="page_alert_wrap">No Deals Found between Rs. '+from+' to Rs. '+to+'</div>');
	}
}
</script>

<?php
