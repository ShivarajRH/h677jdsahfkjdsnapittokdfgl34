
<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/stock_intake.css">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/plot.css">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/deals.css">

<!-- Container -->
<div class="container">
	<h2 style="float: left;margin:0px;width: 50%">Products</h2>
	<div class="filters_wrap"><img class="search_img_wrap" src="<?php echo base_url().'images/search_icon.png'?>">
		<input type="text" name="srch_deals" class="deal_prd_blk inp" placeholder="Search by Product Name" >
	</div>
	<div class="legends_outer_wrap">
		<span class="legends_color_notsrc_wrap">&nbsp;</span> - Not Sourceable &nbsp; &nbsp; &nbsp;
		<span class="legends_color_src_wrap">&nbsp;</span> - Sourceable &nbsp; &nbsp; &nbsp;
	</div>
	<div id="cus_jq_alpha_sort_wrap" >
	</div>
</div>

<script>
//Function to publish and unpublish deals
function endisable_sel(act,brandid,catid)
{
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
	deallist_bycat(brandid,catid);
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
          
          		// Events on click list content
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
	// Call plugin
	$('#cus_jq_alpha_sort_wrap').jqSuAlphaSort({title:"List of Categories",overview_title:"List of Deals",'char_click':function(chr,ele){ cat_bychar(chr)},'item_click':function(catid,ele){ deallist_bycat(0,catid)},'item_click_bybrand':function(brandid,ele){ deallist_bycat(brandid,0)}});
		$(".jq_alpha_sort_alphalist_char a").click(function() {
	      // remove classes from all
	      $(".jq_alpha_sort_alphalist_char a").removeClass("jq_alpha_active");
	      // add class to the one we clicked
	      $(this).addClass("jq_alpha_active");
	    });
	
	// Default click of first character
	 $(".jq_alpha_sort_alphalist_char a:eq(0)").trigger('click');
 
	$('.jq_alpha_sort_alphalist_vend_head').html('<div class="alphabet_header_wrap"><span><a id="cat_lab" class="cat_lst_tab">Category List</a></span><span><a id="brand_lab" style="margin-right:0px !important" class="brand_lst_tab">Brand List</a></span> <input type="text" name="search_name" class="search_blk inp" placeholder="Search by Name" ><img style="margin-top: 7px;" src="<?php echo base_url().'images/search_icon.png'?>"></div>');
	deallist_bycat(0,0);
	$('.brand_lst_tab').addClass('selected_alpha_list');
	
});

//function to select all checkboxes 
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

//Function to display categories on list click
$('.cat_lst_tab').live('click',function(){
	$('#cat_lab').val('1');
	$('#brand_lab').val('0');
	deallist_bycat(0,0);
	
	$(".jq_alpha_sort_alphalist_char a:eq(0)").trigger('click');
	$('.cat_lst_tab').removeClass("selected_alpha_list");
	$('.brand_lst_tab').addClass("selected_alpha_list");
});

//Function to display brands on list click
$('.brand_lst_tab').live('click',function(){
	deallist_bycat(0,0);
	$('#cat_lab').val('0');
	$('#brand_lab').val('1');
	//$('.brands_bychar_list').html('');
	 $(".jq_alpha_sort_alphalist_char a:eq(0)").trigger('click');
	$('.brand_lst_tab').removeClass("selected_alpha_list");
	$('.cat_lst_tab').addClass("selected_alpha_list");
});

//Function to sort sourceble and unsourceble products
$('select[name="sourceable_wrap"]').live('change',function(){
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
			
			sourceable=parseInt($(this).attr('sourceable')*1);
			if(publish == v)
			{
				$('.total_wrap').html("Total Products : "+(i++));
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

//Function to search products by character
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
	var publish_status = $('select[name="sourceable_wrap"]').val();
	var bc_sel = ($('.Brands_bychar_list_content_listdata.selected_class').length?($('.Brands_bychar_list_content_listdata.selected_class').text()):0);
	$(".sk_deal_filter_wrap").each(function(){
			
			search_text=$(this).attr('name')+' '+$(this).attr('brand')+' '+$(this).attr('category');
			
			var row_stat = 1;
				if(publish_status != $(this).attr('sourceable') && (publish_status != 'all'))
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

function deallist_bycat(brandid,catid)
{
	$('input[name="srch_deals"]').val('');

	//$('.jq_alpha_sort_overview_content').html('<div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
	//$('.sk_deal_container').css('opacity','0.5');//$('.jq_alpha_sort_overview_content').css('opacity','0.5');
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
					$('.Brands_bychar_list_head').html('<span class="close_btn_dlpg button button-tiny button-rounded ">Hide</span><h4>List of Brand for <i>'+b.category_name+'</i></h4>');
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
					$('.Brands_bychar_list_head').html('<span class="close_btn_dlpg button button-tiny button-rounded ">Hide</span><h4>List of Categories for <i>'+b.brand_name+'</i></h4>');
				});
			}
			$('.Brands_bychar_list_content').html(cat_linkedcat_html);
			
		});
	}

	$('.sk_deal_container').html('<div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
	
	$.post(site_url+'/admin/jx_product_list',{brandid:brandid,catid:catid},function(resp){
		//$('.sk_deal_container').css('opacity','1');
		//$('.sk_deal_container').css('opacity','1');
		if(resp.status == 'error')
			{
				$('.sk_deal_container').html('<div class="page_alert_wrap">No Products Found</div>');
				return false;
		    }
			else
			{
				var enable='checked="checked"';
				var d_lst = '';
				d_lst+='<div class="sk_deal_filter_blk_wrap">';
				d_lst+='<span class="left_filter_mrp_wrap">MRP Filter : ';
				d_lst+='<input type="text" class="inp" id="f_from" size=4> to';
				d_lst+=' <input type="text" class="inp" id="f_to" size=4> <button type="button" style="margin-top:-5px" class="button button-rounded button-action button-tiny" onclick="filter_deals_bymrp()">Filter</button></span>';
				d_lst+='<span class="ttl_deals_wrap total_wrap" style="float:left">Total Products : '+resp.ttl_prds+'</span>';
				d_lst+='<span class="sourceable_wrap"><b>Sourceable :</b> <select name="sourceable_wrap"><option value="all">Choose</option><option value="1" selected >Yes</option><option value="0">No</option></select></span>';
				d_lst+='</span></div>';
				d_lst+='<div class="sk_deal_container">';
					d_lst+='<table class="sk_deal_blk_wrap" cellpadding="0" cellspacing="0" width="99%">'
						d_lst+='<thead><tr>';
							d_lst+='<th>Product Name</th>';
							d_lst+='<th width="6%">MRP</th>';
							d_lst+='<th width="8%">Stock</th>';
							d_lst+='<th width="10%" style="">Brand</th>';
							d_lst+='<th width="15%">Category</th>';
							d_lst+='<th width="6%">Barcode</th>';
							d_lst+='<th width="12%">Actions<br />';
							d_lst+='</th></tr></thead><tbody>';
			 				
			 				$.each(resp.prds_lst,function(i,p){
			 					if(p.is_sourceable == 1)
			 						background = 'background-color: rgba(170, 255, 170, 0.8)';
			 					else
			 						background = 'background-color: #FFAAAA';
			 						
								d_lst+='<tr style="'+background+'" class="sk_deal_filter_wrap prds_'+p.catid+'" mrp="'+p.orgprice+'" name="'+p.product_name+'" brand="'+p.brand+'" category="'+p.category+'" sourceable="'+p.is_sourceable+'">'
					 				d_lst+='<td><span><a target="_blank" href="'+site_url+'/admin/product/'+p.product_id+'">'+p.product_name+'</a></span></td>';
			 						d_lst+='<td><span class="title">'+p.mrp+'</span></td>';
									d_lst+='<td>'+p.stock+'</td>';
			 						d_lst+='<td><span><a target="_blank" href="'+site_url+'/admin/viewbrand/'+p.brandid+'">'+p.brand+'</a></span></td>';
			 						d_lst+='<td><span><a target="_blank" href="'+site_url+'/admin/viewcat/'+p.catid+'">'+p.category+'</a></span></td>';
			 						d_lst+='<td><span class="title">'+p.barcode+'</div></td>';
			 						d_lst+='<td><span style="margin-right:10px"><a target="_blank" href="'+site_url+'/admin/editproduct/'+p.product_id+'">Edit</a></span><span><a target="_blank" href="'+site_url+'/admin/viewlinkeddeals/'+p.product_id+'">View deals</a></span></td>';
		 						d_lst+='</tr>';
							});
						d_lst+='</tbody></table>';
				d_lst+='</div>';
				
				//polist_byvendor(vids[0]);
				$('.jq_alpha_sort_overview_content').html(d_lst);

				// Call the plugin
				$(".jq_alpha_sort_overview_content .product_stock").dealstock();
                        
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

				$('select[name="sourceable_wrap"]').trigger('change');
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
