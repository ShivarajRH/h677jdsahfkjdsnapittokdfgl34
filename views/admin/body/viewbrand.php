<style>

.leftcont
{
	display:none;
}
h4
{
	margin-bottom: 5px !important;
}
</style>

<div class="container viewbrand">
	<div class="">
		<h2 style="margin-left:10px;"><?=ucfirst($brand['name'])?> Brand Details</h2>
	    <ul class="tabs"> 
	        <li rel="brand_det" class="active">Brand Details</li>
	        <li rel="analytics" class="sales_anal">Analytics</li>
	    </ul>
	    
	    <div class="tab_container" >
	    	<!------------- Details Blk Start ------------->
	    	<div id="brand_det" class="tabcontent">
	    		<table width="100%">
	    			<tr>
	    				<td> 
				    		<table class="datagrid">
								<thead>
									<tr>
										<th>Brand Name</th><th>Allotted Rack n bins</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><?=$brand['name']?> <a href="<?=site_url("admin/editbrand/{$brand['id']}")?>" class="link">edit</a></td>
										<td>
											<?php foreach($rbs as $rb){?>
											<div><?=$rb['rack_name']?><?=$rb['bin_name']?></div>
											<?php }?>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</table>
				<table width="100%">
					<tr>
						<td>
							<h4>Products of <?=$brand['name']?> (<?=count($products)?>)</h4>
							<div class="br_max_height_wrap">
								<table class="datagrid" width="100%">
									<thead>
										<tr>
											<th>Product Name</th>
											<th>MRP</th>
											<th>Barcode</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach($products as $p){?>
											<tr>
												<td><a class="link" href="<?=site_url("admin/editproduct/{$p['product_id']}")?>"><?=$p['product_name']?></a></td>
												<td><?=$p['mrp']?></td>
												<td><?=$p['barcode']?></td>
											</tr>
										<?php }?>
									</tbody>
								</table>
							</div>
						</td>
					
						<td>
							<h4>Vendors for <?=$brand['name']?> products (<?=count($vendors)?>)</h4>
							<div class="br_max_height_wrap">
								<table class="datagrid">
									<thead>
										<tr>
											<th>Vendor</th>
											<th>Margin</th>
											<th>City</th>
										</tr>
									</thead>
									
									<tbody>
										<?php foreach($vendors as $v){?>
											<tr>
												<td><a class="link" href="<?=site_url("admin/vendor/{$v['vendor_id']}")?>"><?=$v['vendor_name']?></a></td>
												<td><?=$v['brand_margin']?>%</td>
												<td><?=$v['city_name']?></td>
											</tr>
										<?php }?>
									</tbody>
								</table>
							</div>
						</td>
						<td>
							<h4>Deals of <?=$brand['name']?> (<?=count($deals)?>)</h4>
							<div class="br_max_height_wrap">
								<table class="datagrid" width="100%">
									<thead>
										<tr>
											<th>Deal Name</th>
											<th>URL</th>
											<th>MRP</th>
											<th>Price</th>
										</tr>
									</thead>
									
									<tbody>
										<?php foreach($deals as $p){?>
										<tr>
											<td><a class="link" href="<?=site_url("admin/edit/{$p['dealid']}")?>"><?=$p['name']?></a></td>
											<td><a class="link" href="<?=site_url("{$p['url']}")?>">site</a></td>
											<td><?=$p['orgprice']?></td>
											<td><?=$p['price']?></td>
										</tr>
										<?php }?>
									</tbody>
								</table>
							</div>
						</td>
					</tr>
				</table>
	    	</div>
	    	<!------------- Details Blk end ------------->
	    	<!------------- Analytics Blk Start ------------->
	    	<div id="analytics" class="tabcontent">
	    		<div class="header_wrap">
					<div style="float: right;width: 40%;">
				    	<form id="stat_frm_to" method="post">
					        <div style="text-align: right">
					        	<b>From</b> : <input type="text" style="width: 90px;" id="date_from"
					                    name="date_from" value="<?php echo date('d-m-Y',time()-90*60*60*24)?>" />
					            <b>To</b> : <input type="text" style="width: 90px;" id="date_to"
					                    name="date_to" value="<?php echo date('d-m-Y',time())?>" /> 
					            <button type="submit" class="sbutton small green"><span>Go</span></button>
					        </div>
					    </form>
					</div>
				 </div>
			    
	    		<div id="total_stat">
	    			<h4><div class="stat_head"></div></h4>
	    			<div class="total_stat_view">
	    			</div>
	    		</div>
	    		
	    		<div id="br_cat_stat" style="float:left">
	    			<div class="cat_head"></div>
	    			<div class="br_cat_stat_view">
	    				
	    			</div>
	    		</div>
	    		
	    		<div id="br_vendors_det">
					<div class="br_vendor_head"></div>
					<div class="br_vendors_det_view">
					</div>
				</div>
	    		
	    		<div class="br_sub_blk_wrap">
					<h4 style="margin: 0px;"><div class="top_category_head"></div></h4>
					<div class="br_top_category_list">
				
					</div>
				</div>
				<div class="br_sub_blk_wrap" style="margin-left:10px;">
					<h4 style="margin: 0px;"><div class="top_product_head"></div></h4>
					<div class="br_top_product_list">
				
					</div>
				</div>
				
				<div class="br_sub_blk_wrap" style="margin-left:10px;">
					<h4 style="margin: 0px;"><div class="top_franch_head"></div></h4>
					<div class="br_franchise_list">
				
					</div>
				</div>
				
				<div id="br_top_sale_terr_stat">
					<h4 style="margin: 0px;"><div class="terr_head"></div></h4>
					<div class="terr_stat_view">
					</div>
				</div>
				
				<div id="br_town_stat">
					<h3 class="br_heading_wrapper"><div id="town_head"></div></h3>
					<div class="br_town_stat_view">
					</div>
				</div>
				
				<div id="br_franch_stat">
					<h3 class="br_heading_wrapper"><span id="franch_head"></span></h3><a class="br_franch_popup">All Franchises</a>
					<div class="br_franch_stat_view">
					</div>
				</div>
				
				<div id="br_franch_popup">
				</div>
	    	</div>
	    	 <!------------- Analytics Blk end ------------->
	    </div>
	</div>	
</div>

<script>
$('.sales_anal').click(function(){
	total_sales();
	cat_sales();
	vendors_details();
	prod_sales();
	top_sale_terr();
	$("#date_from,#date_to").datepicker({dateFormat:'dd-mm-yy'});
});


$(document).ready(function() 
{
	$(".tabcontent").hide();
	$(".tabcontent:first").show();
	$("ul.tabs li").click(function() 
	{
		$("ul.tabs li").removeClass("active");
		$(this).addClass("active");
		$(".tabcontent").hide();
		var activeTab = $(this).attr("rel"); 
		$("#"+activeTab).fadeIn(); 
	});
	
	$('.br_franch_popup').hide();
	
});

$("#stat_frm_to").bind("submit",function(e){
    e.preventDefault();
    total_sales();
    prod_sales();
    cat_sales();
    vendors_details();
    top_sale_terr();
    $('#br_town_stat').hide();
    $('#br_franch_stat').hide();
    return false;
});

$('.br_franch_popup').click(function(){
	$('#br_franch_popup').bPopup({
	easing: 'easeOutBack', //uses jQuery easing plugin
	    speed: 450,
	    transition: 'slideDown'
	});
});


function total_sales()
{
	brandid ="<?php echo $this->uri->segment(3);?>";
	var start_date= $('#date_from').val();
	var end_date= $('#date_to').val();
	var brand_name="<?=ucfirst($brand['name'])?>";
	$('.stat_head').html("<h4 style=''>Total Sales for "+brand_name+" from "+start_date+" to "+end_date+"</h4>");
	$('#total_stat .total_stat_view').html("<div class='anmtd_loading_img'><span></span></div>" );
	$.getJSON(site_url+'/admin/jx_brand_sales/'+brandid+'/'+start_date+'/'+end_date,'',function(resp){
		if(resp.summary == 0)
		{
			$('#total_stat .total_stat_view').html("<div class='br_alert_wrap' style='padding:113px 0px'>No Sales statisticks found between "+start_date+" and "+end_date+"</div>" );	
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

function cat_sales()
{
	var brand_name="<?=ucfirst($brand['name'])?>";
	brandid ="<?php echo $this->uri->segment(3);?>";
	var start_date= $('#date_from').val();
	var end_date= $('#date_to').val();
	$('.cat_head').html("<h4>Category Sales for "+brand_name+" from "+start_date+" to "+end_date+" </h4>");
	$('#br_cat_stat .br_cat_stat_view').html("<div class='anmtd_loading_img'><span></span></div>" );
	$.getJSON(site_url+'/admin/jx_catsales_bybrand/'+brandid+'/'+start_date+'/'+end_date,'',function(resp){
	  	if(resp.result == 0)
		{
			$('#br_cat_stat .br_cat_stat_view').html("<div class='br_alert_wrap' style='padding:113px 0px;'>No Category sales found between "+start_date+" and "+end_date+"</div>" );	
		}
		else
		{	
		// reformat data ;
			$('#br_cat_stat .br_cat_stat_view').empty();
			var resp = resp.result;
			plot3 = jQuery.jqplot('br_cat_stat .br_cat_stat_view', [resp], 
			{
				seriesDefaults:{
		            renderer: jQuery.jqplot.PieRenderer,
		            pointLabels: { show: true },
	                rendererOptions: {
	                    // Put data labels on the pie slices.
	                    // By default, labels show the percentage of the slice.
	                    showDataLabels: true,
	                  }
		        },
		        highlighter: {
				    show: true,
				    useAxesFormatters: false, // must be false for piechart   
				    tooltipLocation: 's',
				    formatString:'Category : %s'
				},
				grid: {borderWidth:0, shadow:false,background:'#eaeaea'},
		       legend:{show:true,rendererOptions: {
				            numberColumns: 2
				        } }
		    });
		 }
	});
}

function vendors_details()
{
	var brand_name="<?=ucfirst($brand['name'])?>";
	brandid ="<?php echo $this->uri->segment(3);?>";
	var start_date= $('#date_from').val();
	var end_date= $('#date_to').val();
	$('.br_vendor_head').html("<h4>Vendors for "+brand_name+" from "+start_date+" to "+end_date+" </h4>");
	$('#br_vendors_det .br_vendors_det_view').html("<div class='anmtd_loading_img'><span></span></div>" );
	$.getJSON(site_url+'/admin/jx_vendors_bybrand/'+brandid+'/'+start_date+'/'+end_date,'',function(resp){
	  	if(resp.result == 0)
		{
			$('#br_vendors_det .br_vendors_det_view').html("<div class='br_alert_wrap' style='padding:113px 0px;'>No Vendors found between "+start_date+" and "+end_date+"</div>" );	
		}
		else
		{	
		// reformat data ;
			$('#br_vendors_det .br_vendors_det_view').empty();
			var resp = resp.result;
			plot3 = jQuery.jqplot('br_vendors_det .br_vendors_det_view', [resp], 
			{
				seriesDefaults:{
		            renderer: jQuery.jqplot.PieRenderer,
		            pointLabels: { show: true },
	                rendererOptions: {
	                    // Put data labels on the pie slices.
	                    // By default, labels show the percentage of the slice.
	                    showDataLabels: true,
	                  }
		        },
		        highlighter: {
				    show: true,
				    useAxesFormatters: false, // must be false for piechart   
				    tooltipLocation: 's',
				    formatString:'Vendor : %s'
				},
				grid: {borderWidth:0, shadow:false,background:'#eaeaea'},
		       legend:{show:true,rendererOptions: {
				            numberColumns: 2
				        } }
		    });
		 }
	});
}

function prod_sales()
{
	var brand_name="<?=ucfirst($brand['name'])?>";
	var start_date= $('#date_from').val();
	var end_date= $('#date_to').val();
	 $('.top_category_head').html("<h4>Top sold Categories for "+brand_name+" from "+start_date+" to "+end_date+"</h4>");
	$('.br_top_category_list').html("<div class='anmtd_loading_img'><span></span></div>" );
	$.post(site_url+'/admin/jx_categoriesbybrandid/',{brandid:brandid,start_date:start_date,end_date:end_date},function(resp){
		if(resp.status == 'error')
			{
				$('.br_top_category_list').html("<div class='br_alert_wrap_text'>No Categories sold for "+brand_name+" </div>");
			}else
			{
					var cat_list_html = '';
						cat_list_html +='<table class="datagrid" style="width:100%"><thead><tr><th>Sl.No</th><th>Category Name</th><th>Qty</th><th>Total Sales</th></tr></thead><tbody>'
				 		$.each(resp.cat_list,function(a,b){
							cat_list_html += '<tr>'
													+'<td>'+(++a)+'</td>'
													+'<td width="150px">'+b.cat_name+'</td>'
													+'<td>'+b.qty_sold+'</td>'
													+'<td>'+b.ttl+'</td>'
												+'</tr>';
						});
						cat_list_html +='</tbody></table>'
					$('.br_top_category_list').html(cat_list_html);
			}
	},'json');
	
	 $('.top_product_head').html("<h4>Top sold Products for "+brand_name+" from "+start_date+" to "+end_date+"</h4>");
	$('.br_top_product_list').html("<div class='anmtd_loading_img'><span></span></div>" );
		$.post(site_url+'/admin/jx_topproductbybrandid/',{brandid:brandid,start_date:start_date,end_date:end_date},function(resp){
			if(resp.status == 'error')
			{
				$('.br_top_product_list').html("<div class='br_alert_wrap_text'>No Products sold for "+brand_name+" </div>");
			}else
			{
					var top_prd_list_html = '';
						top_prd_list_html +='<table class="datagrid" style="width:100%"><thead><tr><th>Sl.No</th><th>Product Name</th><th>Qty</th><th>Total Sales</th></tr></thead><tbody>'
				 		$.each(resp.top_prd_list,function(a,b){
							top_prd_list_html += '<tr>'
													+'<td>'+(++a)+'</td>'
													+'<td width="250px">'+'<a href="'+site_url+'/admin/product/'+b.product_id+'" target="blank">'+b.product_name+'</a>'+'</td>'
													+'<td>'+b.qty_sold+'</td>'
													+'<td>'+b.ttl+'</td>'
												+'</tr>';
						});
						top_prd_list_html +='</tbody></table>'
					$('.br_top_product_list').html(top_prd_list_html);
			}
	},'json');
	
	 $('.top_franch_head').html("<h4>Top Franchises for "+brand_name+" from "+start_date+" to "+end_date+"</h4>");
	$('.br_franchise_list').html("<div class='anmtd_loading_img'><span></span></div>" );
		$.post(site_url+'/admin/jx_topfranchisebybrandid/',{brandid:brandid,start_date:start_date,end_date:end_date},function(resp){
			if(resp.status == 'error')
			{
				$('.br_franchise_list').html("<div class='br_alert_wrap_text'>No Products sold for "+brand_name+" </div>");
			}else
			{
					var top_fran_list_html = '';
						top_fran_list_html +='<table class="datagrid" style="width:100%"><thead><tr><th>Sl.No</th><th>Franchise Name</th><th>Qty</th><th>Total Sales</th></tr></thead><tbody>'
				 		$.each(resp.top_fran_list,function(a,b){
							top_fran_list_html += '<tr>'
													+'<td>'+(++a)+'</td>'
													+'<td width="250px">'+'<a href="'+site_url+'/admin/pnh_franchise/'+b.franchise_id+'" target="blank">'+b.franchise_name+'</a>'+'</td>'
													+'<td>'+b.qty_sold+'</td>'
													+'<td>'+b.ttl+'</td>'
												+'</tr>';
						});
						top_fran_list_html +='</tbody></table>'
					$('.br_franchise_list').html(top_fran_list_html);
			}
	},'json');
}


function top_sale_terr()
{
	var brand_name="<?=ucfirst($brand['name'])?>";
	brandid ="<?php echo $this->uri->segment(3);?>";
	var start_date= $('#date_from').val();
	var end_date= $('#date_to').val();
	$('.terr_head').html("<h4>Territory sales for "+brand_name+" from "+start_date+" to "+end_date+" </h4>");
	$('#br_top_sale_terr_stat .terr_stat_view').html("<div class='anmtd_loading_img'><span></span></div>" );
	$.getJSON(site_url+'/admin/jx_top_sale_terr/'+brandid+'/'+start_date+'/'+end_date,'',function(resp){
		if(resp.summary == 0)
		{
			$('#br_top_sale_terr_stat .terr_stat_view').html("<div class='br_alert_wrap_text' style='padding:113px 0px'>No Territories found for "+brand_name+" from "+start_date+" to "+end_date+ "</div>");
		}
		else
		{
			// reformat data ;
			$('#br_top_sale_terr_stat .terr_stat_view').empty();
			var resp = resp.summary;
			plot2 = $.jqplot('br_top_sale_terr_stat .terr_stat_view', [resp], {
		       	seriesDefaults:{
		            renderer:$.jqplot.BarRenderer,
		            rendererOptions: {
		                // Set the varyBarColor option to true to use different colors for each bar.
		                // The default series colors are used.
		                varyBarColor: true
		            },pointLabels: { show: true }
		        },
			    axesDefaults: {
			        tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
			        tickOptions: {
			          fontFamily: 'tahoma',
			          fontSize: '11px',
			          angle: -30
			        }
			    },
			    axes: {
			      xaxis: {
			        renderer: $.jqplot.CategoryAxisRenderer
			      }
			    }
			});
			$('#br_top_sale_terr_stat .terr_stat_view').bind('jqplotDataClick', function(ev,seriesIndex,pointIndex,data) {
				var terr_id = resp[pointIndex][2];
			 	var terr_name = resp[pointIndex][0];
			 	town_sales(terr_id,terr_name);
			 	franch_sales(terr_id,terr_name);
			 	all_ter_franchises(terr_id,terr_name);
			 	//cat_sales(terr_id,terr_name);
				$('#town_head').html('Towns Sales for '+terr_name+' territory');
				$('#br_town_stat .br_town_stat_view').unbind('jqplotDataClick');
				$('#br_town_stat .br_town_stat_view').html("<div class='anmtd_loading_img'><span></span></div>" );	
				$('#franch_head').html('Top Franchise in '+terr_name+' territory');
				$('#br_franch_stat .br_franch_stat_view').unbind('jqplotDataClick');
				$('#br_franch_stat .br_franch_stat_view').html("<div class='anmtd_loading_img'><span></span></div>" );	
				
			});
		}
	});
}

function all_ter_franchises(terr_id,terr_name)
{
	brandid ="<?php echo $this->uri->segment(3);?>";
	start_date= $('#date_from').val();
	end_date= $('#date_to').val();
	$.post(site_url+'/admin/jx_getallfranchisesbybrandid_terrid/',{brandid:brandid,terr_id:terr_id,start_date:start_date,end_date:end_date},function(resp){
	if(resp.status == 'error')
	{
		alert(resp.error);
	}else
	{
		 var fran_list_html = '';
		  fran_list_html += '<h4>Franchise List for '+terr_name+' territory</h4>';
		 fran_list_html +='<span class="popclose_button b-close"><span>X</span></span><div style="overflow:auto;float:left;height:265px;width:600px;">';
		 fran_list_html += '<table class="datagrid" width="100%"><thead><tr><th>Sl.No</th><th>Name</th><th>Total Sales</th></tr></thead><tbody>';
			$.each(resp.fran_list,function(a,b){
				fran_list_html += '<tr>'
										+'<td>'+(++a)+'</td>'
										+'<td>'+b.franchise_name+'</td>'
										+'<td>'+b.ttl+'</td>'
									+'</tr>';
			});
		fran_list_html += '</tbody></table></div>';
		$('#br_franch_popup').html(fran_list_html);
		$('.br_franch_popup').show();
	}
	},'json');
}


function town_sales(terr_id,terr_name)
{
	brandid ="<?php echo $this->uri->segment(3);?>";
	start_date= $('#date_from').val();
	end_date= $('#date_to').val();
	$('#br_town_stat .br_town_stat_view').html("<div class='anmtd_loading_img'><span></span></div>" );
	$.getJSON(site_url+'/admin/jx_gettownsbybrandid/'+brandid+'/'+terr_id+'/'+start_date+'/'+end_date,'',function(resp){
		if(resp.summary == 0)
		{
			$('#br_town_stat .br_town_stat_view').html("<div class='br_alert_wrap' style='padding:113px 0px'>No Town stats</div>" );	
		}
		else
		{
			// reformat data ;
			var resp=resp.summary;
			$('#br_town_stat .br_town_stat_view').empty();
			plot2 = $.jqplot('br_town_stat .br_town_stat_view', [resp], {
		       	seriesDefaults:{
		            renderer:$.jqplot.BarRenderer,
		            rendererOptions: {
		                // Set the varyBarColor option to true to use different colors for each bar.
		                // The default series colors are used.
		                varyBarColor: true
		            },pointLabels: { show: true }
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
			          	label:'Towns',
				          labelOptions:{
				            fontFamily:'Arial',
				            fontSize: '14px'
				          },
				          labelRenderer: $.jqplot.CanvasAxisLabelRenderer
			        },
			        yaxis:{
						  label:'Total Sales in Rs',
				          labelOptions:{
				            fontFamily:'Arial',
				            fontSize: '14px'
				          },
				          labelRenderer: $.jqplot.CanvasAxisLabelRenderer
				        }
			      }
			});
			$('#br_town_stat .br_town_stat_view').bind('jqplotDataClick', function(ev,seriesIndex,pointIndex,data) {
					var town_id = resp[pointIndex][2];
				 	var town_name = resp[pointIndex][0];
				 	//town_brand_sales(town_id,town_name);
				 	town_franch_sales(town_id,town_name);
				 	all_town_franchises(town_id,town_name);
				 	$('#franch_head').html('Top Franchise in '+town_name+' town');
					$('#br_franch_stat .br_franch_stat_view').unbind('jqplotDataClick');
					$('#br_franch_stat .br_franch_stat_view').html("<div class='anmtd_loading_img'><span></span></div>" );	
				 	//town_cat_sales(town_id,town_name);
			});
		  }
		});
	$('#br_town_stat').show();
}

function all_town_franchises(town_id,town_name)
{
	brandid ="<?php echo $this->uri->segment(3);?>";
	start_date= $('#date_from').val();
	end_date= $('#date_to').val();
	$.post(site_url+'/admin/jx_getallfranchisesbybrandid_townid/',{brandid:brandid,town_id:town_id,start_date:start_date,end_date:end_date},function(resp){
	if(resp.status == 'error')
	{
		alert(resp.error);
	}else
	{
		 var fran_list_html = '';
		 fran_list_html += '<h4>Franchise List for '+town_name+' town</h4>';
		 fran_list_html +='<span class="popclose_button b-close"><span>X</span></span><div style="overflow:auto;float:left;height:265px;width:600px;">';
		 fran_list_html += '<table class="datagrid" width="100%"><thead><tr><th>Sl.No</th><th>Name</th><th>Total Sales</th></tr></thead><tbody>';
			$.each(resp.fran_list,function(a,b){
				fran_list_html += '<tr>'
										+'<td>'+(++a)+'</td>'
										+'<td>'+b.franchise_name+'</td>'
										+'<td>'+b.ttl+'</td>'
									+'</tr>';
			});
		fran_list_html += '</tbody></table></div>';
		$('#br_franch_popup').html(fran_list_html);
		$('.br_franch_popup').show();
	}
	},'json');
}



function franch_sales(terr_id,terr_name)
{
	brandid ="<?php echo $this->uri->segment(3);?>";
	start_date= $('#date_from').val();
	end_date= $('#date_to').val();
	$.getJSON(site_url+'/admin/jx_getfranchisebybrand/'+brandid+'/'+terr_id+'/'+start_date+'/'+end_date,'',function(resp){
		if(resp.summary == 0)
		{
			$('#br_franch_stat .br_franch_stat_view').html("<div class='br_alert_wrap' style='padding:113px 0px'>No Franchises Found</div>" );	
		}
		else
		{
			// reformat data ;
			$('#br_franch_stat .br_franch_stat_view').empty();
			plot2 = $.jqplot('br_franch_stat .br_franch_stat_view', [resp.summary], {
		       	seriesDefaults:{
		            renderer:$.jqplot.BarRenderer,
		            rendererOptions: {
		                // Set the varyBarColor option to true to use different colors for each bar.
		                // The default series colors are used.
		                varyBarColor: true
		            },pointLabels: { show: true }
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
			          	label:'Franchise',
				          labelOptions:{
				            fontFamily:'Arial',
				            fontSize: '14px'
				          },
				          labelRenderer: $.jqplot.CanvasAxisLabelRenderer
			        },
			        yaxis:{
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
	$('#br_franch_stat').show();
}

function town_franch_sales(town_id,town_name)
{
	catid ="<?php echo $this->uri->segment(3);?>";
	state_id ="<?php echo $this->uri->segment(3);?>";
	start_date= $('#date_from').val();
	end_date= $('#date_to').val();
	$.getJSON(site_url+'/admin/jx_getfranchisebybrandid_townid/'+catid+'/'+town_id+'/'+start_date+'/'+end_date,'',function(resp){
		    	
    	if(resp.summary == 0)
		{
			$('#br_franch_stat .br_franch_stat_view').html("<div class='br_alert_wrap' style='padding:113px 0px'>No Franchises Found</div>" );	
		}
		else
		{
			// reformat data ;
			$('#br_franch_stat .br_franch_stat_view').empty();
			plot2 = $.jqplot('br_franch_stat .br_franch_stat_view', [resp.summary], {
		       	seriesDefaults:{
		            renderer:$.jqplot.BarRenderer,
		            rendererOptions: {
		                // Set the varyBarColor option to true to use different colors for each bar.
		                // The default series colors are used.
		                varyBarColor: true
		            },pointLabels: { show: true }
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
			          	label:'Franchise',
				          labelOptions:{
				            fontFamily:'Arial',
				            fontSize: '14px'
				          },
				          labelRenderer: $.jqplot.CanvasAxisLabelRenderer
			        },
			        yaxis:{
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



</script>
<?php
