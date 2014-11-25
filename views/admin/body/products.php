<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/stock_intake.css">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/plot.css">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/deals.css">
<style>
.src_btn
{
	font-size: 9px;
    margin: 10px 0 0;
}
.left_filter_mrp_wrap
{
	text-align:right;
}
.prd_dets
{
	margin:10px 0;
}
.recently_added
{
	color: blue;
    cursor: pointer;
    margin-right: 15px;
    text-decoration: underline;
}
</style>
<!-- Container -->
<div class="container">
	<h2 style="float: left;margin:0px;width: 50%">Products</h2>
	<div class="filters_wrap"><img class="search_img_wrap" src="<?php echo base_url().'images/search_icon.png'?>">
		<input type="text" name="srch_product" class="deal_prd_blk inp" placeholder="Search by Product Name" >
	</div>
	<div class="legends_outer_wrap">
		<span class="legends_color_notsrc_wrap">&nbsp;</span> - Not Sourceable &nbsp; &nbsp; &nbsp;
		<span class="legends_color_src_wrap">&nbsp;</span> - Sourceable &nbsp; &nbsp; &nbsp;
	</div>
	<div id="cus_jq_alpha_sort_wrap" >
	</div>
</div>

<div id="change_status_dlg" title="Change Status">
	<div class="prd_dets"></div>
	<b style="vertical-align: top">Remarks :</b>  <textarea name="remarks" class="remarks" placeholder="Please enter minimum 20 characters"></textarea>
	
</div>

<script>
$('.mark_all').live('click',function(){
	$(".sel").attr('checked',true);
	$('.mark_all').addClass('unmark_all').text('Unselect All').removeClass('mark_all');
});

$('.unmark_all').live('click',function(){
	$(".sel").attr('checked',false);
	$('.unmark_all').addClass('mark_all').text('Select All').removeClass('unmark_all');
});

$('.src_btn').live('click',function(){
	var v=$(this).val();
	var count=0;
	var brandid=$(this).attr('brandid');
	var catid=$(this).attr('catid');
	$(".sel:checked").each(function(){
		count++;
	});
	
	if(count>0)
		endisable_sel(v,brandid,catid);
	else
	{
		alert('Please Choose atleast one product');return false;
	}
			
});
//Function to publish and unpublish deals
function endisable_sel(act,brandid,catid)
{
	var ids=[];
	var c=0;
	$(".sel:checked").each(function(){
		ids.push($(this).val());
	});
	ids=ids.join(",");
	if(act==1)
		act=0;
	else if(act==0)
		act=1;	
	
	$('#change_status_dlg').data("pro_data",{'pids': ids,'status': act,'brandid' : brandid,'catid' : catid}).dialog('open');
}

$(".end_of_life").live("change",function(){
	var brandid=$(this).attr('brandid');
	var catid=$(this).attr('catid');
	if($(".end_of_life").attr("checked"))
	{
		product_list(brandid,catid,0,'',0,1);
	}else
	{
		product_list(brandid,catid,0,'',0,0);
	}
});

$("#change_status_dlg" ).dialog({
		modal:true,
		autoOpen:false,
		width:'300',
		height:'200',
		autoResize:true,
		open:function(){
	},
	buttons: {
		"Proceed": function() {
			var dlgData = $("#change_status_dlg").data("pro_data");
			var pid=dlgData.pids;
			var pstatus=dlgData.status;
			var brandid=dlgData.brandid;
			var catid=dlgData.catid;
			var remarks=$('.remarks').val();
			
			if(!remarks || remarks.length < 20)
			{
				alert("Please enter minimum 20 characters");
				return false;
			}
			change_status(brandid,catid,pid,pstatus,remarks);
			
	    	$(this).dialog('close');
	    },	
	    "Close": function() {
	    	$(this).dialog('close');
	   }
	} 
});

function change_status(brandid,catid,pid,pstatus,remarks)
{
	$.post('<?=site_url('admin/jx_change_prd_status')?>',{pids:pid,status:pstatus,remarks:remarks},function(resp){
		
		if(resp.status=='error')
		{
			
		}else
		{
			alert("Product status successfully changed");
				product_list(brandid,catid,0,'',0,0);
		}
	},'json');
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
	$('#cus_jq_alpha_sort_wrap').jqSuAlphaSort({title:"List of Categories",overview_title:"List of Deals",'char_click':function(chr,ele){ cat_bychar(chr)},'item_click':function(catid,ele){ product_list(0,catid,0,'',0,0)},'item_click_bybrand':function(brandid,ele){ product_list(brandid,0,0,'',0,0)}});
		$(".jq_alpha_sort_alphalist_char a").click(function() {
	      // remove classes from all
	      $(".jq_alpha_sort_alphalist_char a").removeClass("jq_alpha_active");
	      // add class to the one we clicked
	      $(this).addClass("jq_alpha_active");
	    });
	
	// Default click of first character
	 $(".jq_alpha_sort_alphalist_char a:eq(0)").trigger('click');
 
	$('.jq_alpha_sort_alphalist_vend_head').html('<div class="alphabet_header_wrap"><span><a id="cat_lab" class="cat_lst_tab">Category List</a></span><span><a id="brand_lab" style="margin-right:0px !important" class="brand_lst_tab">Brand List</a></span> <input type="text" name="search_name" class="search_blk inp" placeholder="Search by Name" ><img style="margin-top: 7px;" src="<?php echo base_url().'images/search_icon.png'?>"></div>');
	product_list(0,0,0,'',0,0);
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
	  	 	product_list(brandid,catid,0,'',0,0);
	  	 	//filter_deallist($(this).text(),'brand');
	  	}
	  	else
	  	{ 
		  	product_list(0,catid,0,'',0,0);
	  		//filter_deallist('','all');
	  	}
		$('.left_filter_wrap').show();
});

//Function to display categories on list click
$('.cat_lst_tab').live('click',function(){
	$('#cat_lab').val('1');
	$('#brand_lab').val('0');
	product_list(0,0,0,'',0,0);
	
	$(".jq_alpha_sort_alphalist_char a:eq(0)").trigger('click');
	$('.cat_lst_tab').removeClass("selected_alpha_list");
	$('.brand_lst_tab').addClass("selected_alpha_list");
});

//Function to display brands on list click
$('.brand_lst_tab').live('click',function(){
	product_list(0,0,0,'',0,0);
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
	var b=$('.sourceable_wrap').attr('brandid');
	var c=$('.sourceable_wrap').attr('catid');
	
	if(v==1)
	 	v='src';
	else if(v==0)
		v='unsrc';
	else if(v==2)
		v='all';
	else
		v='';		
	published_deals(b,c,v);	 
});


function published_deals(b,c,v)
{
	product_list(b,c,0,v,0,0);
	/*
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
	*/
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
				product_list(brandid,catid,$('.filter_opts .selected_type').attr('val'),'',0,0);			
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

function filter_deallist(tag,by)
{
	var srch_inp =  $('input[name="srch_product"]').val();
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

$('input[name="srch_product"]').live('keyup',function(){
	filter_deallist($(this).val(),'search');
	search_othr_products();
});

function search_othr_products()
{
	$('input[name="srch_product"]').autocomplete({
		source:site_url+'/admin/jx_searchprds_json/',
		minLength: 2,
		select:function(event, ui ){
			if(!$('.prdid_'+ui.item.id).length)
				product_list(0,0,ui.item.id,'',0,0);
		}
	});
}

$('.stock_det_close').live("click",function(){
	var id=$(this).attr('refid');
	$('.stock_det_'+id).hide();
});

function product_list(brandid,catid,pid,src,type,is_life_ended)
{
	//type==0  ======> All Products
	//type==1  ======> Recently added
	$('input[name="srch_product"]').val('');
	
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
	
	$.post(site_url+'/admin/jx_product_list',{brandid:brandid,catid:catid,pid:pid,src:src,type:type,show_prd_closed:is_life_ended},function(resp){
		//$('.sk_deal_container').css('opacity','1');
		//$('.sk_deal_container').css('opacity','1');
		if(resp.status == 'error')
			{
				$('.sk_deal_container').html('<div class="page_alert_wrap">No Products Found</div>');
				$('.total_wrap').hide();
				$('.src_deals').hide();
				$('.unsrc_deals').hide();
				return false;
		    }
			else
			{
				$('.total_wrap').show();
				$('.src_deals').show();
				$('.unsrc_deals').show();
				var enable='checked="checked"';
				var d_lst = '';
				d_lst+='<div class="sk_deal_filter_blk_wrap">';
				
				d_lst+='<span class="left_filter_mrp_wrap">';
				if(is_life_ended==1)
				{
					d_lst+='<span class="sourceable_wrap" brandid='+brandid+' catid='+catid+'>Products Closed : <input type="checkbox" class="end_of_life" brandid="'+brandid+'" catid="'+catid+'" style="width:0px !important;height:0px !important" checked></span>';
				}else
				{
					d_lst+='<span class="sourceable_wrap" brandid='+brandid+' catid='+catid+'>Products Closed : <input type="checkbox" class="end_of_life" brandid="'+brandid+'" catid="'+catid+'" style="width:0px !important;height:0px !important" ></span>';
				}
				
				d_lst+='<span class="sourceable_wrap" brandid='+brandid+' catid='+catid+'>Sourceable : <select name="sourceable_wrap"><option value="all">Choose</option><option value="2">All</option><option value="1">Yes</option><option value="0">No</option></select></span>';
				d_lst+='<span class="sourceable_wrap">MRP Filter : <input type="text" class="inp" id="f_from" size=4> to';
				d_lst+=' <input type="text" class="inp" id="f_to" size=4> <button type="button" style="margin-top:-5px" class="button button-rounded button-action button-tiny" onclick="filter_deals_bymrp()">Filter</button></span>';
				d_lst+='</div>';
				d_lst+='<div class="sk_deal_filter_blk_wrap">';
				d_lst+='<div class="fl_right"><span class="recently_added" brandid='+brandid+' catid='+catid+' style="display:none">Recently Added</span> Mark : <button type="button" class="src_btn" value="1" brandid="'+brandid+'" catid="'+catid+'">Sourceable</button><button type="button" class="src_btn" value="0" brandid="'+brandid+'" catid="'+catid+'">Not Sourceable</button></span></div>';
				d_lst+='<span class="ttl_deals_wrap total_wrap" style="float:left">Showing '+resp.ttl_prds+' / '+resp.ttl_rows+' products</span>';
				d_lst+='<span class="src_deals" style="color:green">Sourceable Products : '+resp.src_prds+'</span>';
				d_lst+='<span class="unsrc_deals" style="color:red">Not Sourceable Products : '+resp.unsrc_prds+'</span></div>';
				d_lst+='<div class="sk_deal_container">';
					d_lst+='<table class="sk_deal_blk_wrap" cellpadding="0" cellspacing="0" width="99%">'
						d_lst+='<thead><tr>';
							d_lst+='<th>Product Name</th>';
							d_lst+='<th width="6%">MRP</th>';
							d_lst+='<th width="8%">Stock</th>';
							d_lst+='<th width="10%" style="">Brand</th>';
							d_lst+='<th width="15%">Category</th>';
							d_lst+='<th width="6%">Barcode</th>';
							d_lst+='<th width="12%">Actions <a class="mark_all" style="font-size:10px;margin-left:6px;">Select All</a><br />';
							d_lst+='</th></tr></thead><tbody>';
			 				
			 				$.each(resp.prds_lst,function(i,p){
			 					if(p.is_sourceable == 1)
			 						background = 'background-color: rgba(170, 255, 170, 0.8)';
			 					else
			 						background = 'background-color: #FFAAAA';
			 					
			 					if(p.is_sourceable == 1)
			 					{
			 						d_lst+='<tr style="'+background+'" class="sk_deal_filter_wrap prds_'+p.catid+' prdid_'+p.product_id+'" mrp="'+p.mrp+'" name="'+p.product_name+'" brand="'+p.brand+'" category="'+p.category+'" sourceable="'+p.is_sourceable+'" total_row="'+resp.ttl_rows+'">'
						 				d_lst+='<td><span><a target="_blank" href="'+site_url+'/admin/product/'+p.product_id+'">'+p.product_name+'</a></span></td>';
				 						d_lst+='<td><span class="title">'+p.mrp+'</span></td>';
										d_lst+='<td>'+p.stock+'</td>';
				 						d_lst+='<td><span><a target="_blank" href="'+site_url+'/admin/viewbrand/'+p.brandid+'">'+p.brand+'</a></span></td>';
				 						d_lst+='<td><span><a target="_blank" href="'+site_url+'/admin/viewcat/'+p.catid+'">'+p.category+'</a></span></td>';
				 						d_lst+='<td><span class="title">'+p.barcode+'</div></td>';
				 						if(resp.has_user_edit == 1)
				 							d_lst+='<td><span style="margin-right:10px"><a target="_blank" href="'+site_url+'/admin/editproduct/'+p.product_id+'">Edit</a></span><span><a target="_blank" href="'+site_url+'/admin/viewlinkeddeals/'+p.product_id+'">View deals</a></span><input type="checkbox" class="sel" value="'+p.product_id+'"></td>';
				 						else 
		 									d_lst+='<td>--n/a--</td>';
			 						d_lst+='</tr>';
			 					}else
			 					{
			 						d_lst+='<tr style="'+background+'" class="sk_deal_filter_wrap prds_'+p.catid+' prdid_'+p.product_id+'" mrp="'+p.mrp+'" name="'+p.product_name+'" brand="'+p.brand+'" category="'+p.category+'" sourceable="'+p.is_sourceable+'" total_row="'+resp.ttl_rows+'">'
						 				d_lst+='<td><span><a target="_blank" href="'+site_url+'/admin/product/'+p.product_id+'">'+p.product_name+'</a></span></td>';
				 						d_lst+='<td><span class="title">'+p.mrp+'</span></td>';
										d_lst+='<td>'+p.stock+'</td>';
				 						d_lst+='<td><span><a target="_blank" href="'+site_url+'/admin/viewbrand/'+p.brandid+'">'+p.brand+'</a></span></td>';
				 						d_lst+='<td><span><a target="_blank" href="'+site_url+'/admin/viewcat/'+p.catid+'">'+p.category+'</a></span></td>';
				 						d_lst+='<td><span class="title">'+p.barcode+'</div></td>';
				 						
				 						if(resp.has_user_edit == 1)
				 							d_lst+='<td><span style="margin-right:10px"><a target="_blank" href="'+site_url+'/admin/editproduct/'+p.product_id+'">Edit</a></span><span><a target="_blank" href="'+site_url+'/admin/viewlinkeddeals/'+p.product_id+'">View deals</a></span><input type="checkbox" class="sel" value="'+p.product_id+'"></td>';
				 						else 
		 									d_lst+='<td>--n/a--</td>';	
			 						d_lst+='</tr>';
			 					}	
								
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
				
				if(pid == 0)
				{
					$('.src_deals').show();
					$('.unsrc_deals').show();
				}
				else
				{
					$('.src_deals').hide();
					$('.unsrc_deals').hide();
				}
				
				if(brandid==0 && catid==0)
				{
					$('.recently_added').show();
				}
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
	var i=0;
	var pub=0;
	var unpub=0;
	var total_rows=0;
	$(".sk_deal_filter_wrap").each(function(){
		$(this).show();
		mrp=parseInt($(this).attr('mrp')*1);
		src=$(this).attr('sourceable');
		total_rows=$(this).attr('total_row');
		if(mrp>=from && mrp<=to)
		{
				valid_mrp.push(mrp);
				$(this).show();
				++i;
				if(src==1)
					++pub;
				else if(src==0)
					++unpub;
		}
		else
		{
				$(this).hide();
		}
	});
	$('.total_wrap').html("Showing "+i+" / "+total_rows+" Products" );
	$('.src_deals').html("Sourceable Deals : "+pub);
	$('.unsrc_deals').html("Not Sourceable Deals : "+unpub);
	if(valid_mrp.length == 0)
	{
		$('.sk_deal_container').html('<div class="page_alert_wrap">No Deals Found between Rs. '+from+' to Rs. '+to+'</div>');
	}
}

function search(b_search,c_search)
{
	$('input[name="search_name"]').autocomplete({
		source:site_url+'/admin/jx_search_json/'+b_search+'/'+c_search,
		minLength: 2,
		
			select:function(event, ui ){
				if(b_search==1)
				{
					product_list(ui.item.id,0,0,'',0,0);
				}else if(c_search==1)
				{
					product_list(0,ui.item.id,0,'',0,0);
				}
			}
	});
}

$('.recently_added').live('click',function(){
	brandid=$(this).attr('brandid');
	catid=$(this).attr('catid');
	product_list(brandid,catid,0,'',1,0);
});

</script>
<?php
