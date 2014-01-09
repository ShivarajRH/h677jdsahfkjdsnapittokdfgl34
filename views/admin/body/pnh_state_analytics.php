<style>
.leftcont
{
	display:none;
}
.rightcont
{
	background:#f0f0f0;	padding:0px !important;
}

</style>

<?php
	$sid=$this->uri->segment(3);
	$i=1;$j=1;$k=1;$l=1;$m=1;$n=1;
?>

<div >
	<div style="background:#CCCC99">
		 <?php $state_name=$this->db->query("select state_name as name from pnh_m_states where state_id='".$sid."'")->row()->name?>
	</div>
	
	<div class="st_header_wrap">
		<div style="float: right;margin:15px 0px 13px 0px;">
	    	<form id="st_stat_frm_to" method="post">
		        <div style="margin:2px 0px;font-size:12px;">
		        	<b>From</b> : <input type="text" style="width: 90px;" id="date_from"
		                    name="date_from" value="<?php echo date('d-m-Y',time()-45*60*60*24)?>" />
		            <b>To</b> : <input type="text" style="width: 90px;" id="date_to"
		                    name="date_to" value="<?php echo date('d-m-Y',time())?>" /> 
		            <button type="submit" class="sbutton small green"><span>Go</span></button>
		        </div>
		    </form>
		    <div style="float: right;margin-right: 0; width: 177px;">
				<b>State</b> : <select name="state" id="sel_state">
									<?php foreach($this->db->query("select * from pnh_m_states order by state_id asc")->result_array() as $s){ ?>
										<option value="<?=$s['state_id']?>" <?php echo (($sid==$s['state_id'])?'selected':'');?> ><?=$s['state_name']?></option>
									<?php } ?>
								</select>
			</div>
	    </div>
	    <h2 class="st_header_wrap_content"></h2>
	 </div>
	 <div class="container">
		<table width="100%" style="margin-top:15px;">
			<tr>
				<td width="100%">
					<div id="st_state_sales">
						<h3 class="st_heading_wrapper"><span id="stat_head"></span></h3>
						
						<div class="st_sales_view">
						</div>
					</div>
					
					<div id="st_terr_stat">
						<h3 class="st_heading_wrapper"><span id="terr_head"></span><span id="st_more_wrap1">(Click On Territory to view more)</span></h3>
						<div class="st_terr_stat_view">
						</div>
					</div>
				</td>
			</tr>
		</table>
	
		<table width="100%">
			<tr>
				<td width="50%">
					<div id="st_town_stat">
						<h3 class="anal_head_wrap"><span id="town_head"></span><span id="st_more_wrap2">(Click On Town to view more)</span></h3>
						<div class="st_town_stat_view">
						</div>
					</div>
				</td>
				<td width="50%">
					<div id="st_brand_stat">
						<a class="st_brand_popup">All Brands</a>
						<h3 class="anal_head_wrap"><span id="brand_head"></span></h3>
						<div class="st_brand_stat_view">
						</div>
					</div>
					<div id="st_brand_popup">
					</div>
				</td>
			</tr>
		</table>
		<table width="100%">
			<tr>
				<td width="50%">
					<div id="st_franch_stat">
						<a class="st_franch_popup">All Franchises</a>
						<h3 class="anal_head_wrap"><span id="franch_head"></span></h3>
						<div class="st_franch_stat_view">
						</div>
					</div>
					<div id="st_franch_popup">
					</div>
				</td>
				<td>
					<div id="st_cat_stat">
						<h3 class="anal_head_wrap"><span id="cat_head"></span></h3>
						<div class="st_cat_stat_view">
						</div>
					</div>
					
				</td>
			</tr>
		</table>
	</div>
</div>

<script>
 (function(){
    $('.st_header_wrap').sticky({
        stickyClass: 'sticky',
        anchorClass: 'sticky-anchor'
    });
}());

$(function(){
	var state_name='<?php echo $state_name ?>';
	$('.st_header_wrap_content').html(state_name+' State Analytics for period of '+$('#date_from').val()+' and '+$('#date_to').val());
	$("#date_from,#date_to").datepicker({dateFormat:'dd-mm-yy'});
	$('#st_state_sales .st_sales_view').html('<div class="anmtd_loading_img"><span></span></div>');
    $('#st_terr_stat .st_terr_stat_view').html('<div class="anmtd_loading_img"><span></span></div>');
	order_stat();
	st_terr_stat();
	$('#st_top_sale_det').tabs();
	$('#st_top_prd_brand_det').tabs();
	$('.st_brand_popup').hide();
	$('.st_franch_popup').hide();
	$('#st_more_wrap1').hide();
	$('#st_more_wrap2').hide();
	state_id ="<?php echo $this->uri->segment(3);?>";
	
	$("#sel_state").change(function(){
		v=$(this).val();
		if(v==0)
			v = '';
		location="<?=site_url("admin/pnh_state_analytics")?>/"+v;
	});
});

$("#st_stat_frm_to").bind("submit",function(e){
    e.preventDefault();
    order_stat();
    st_terr_stat();
    $('#st_brand_stat').hide();
    $('#st_town_stat').hide();
     $('#st_franch_stat').hide();
    $('#st_cat_stat').hide();
   
   $('#st_state_sales .st_sales_view').html('<div class="anmtd_loading_img"><span></span></div>');
   $('#st_terr_stat .st_terr_stat_view').html('<div class="anmtd_loading_img"><span></span></div>');
    $('#st_cat_stat .st_cat_stat_view').unbind('jqplotDataClick');
   	$('#st_terr_stat .st_terr_stat_view').unbind('jqplotDataClick');
    $('#st_brand_stat .st_brand_stat_view').unbind('jqplotDataClick');
    $('#st_town_stat .st_town_stat_view').unbind('jqplotDataClick');
    return false;
});

function order_stat()
{
	state_id ="<?php echo $this->uri->segment(3);?>";
	start_date= $('#date_from').val();
	end_date= $('#date_to').val();
	$.getJSON(site_url+'/admin/jx_state_sales/'+start_date+'/'+end_date+'/'+state_id,'',function(resp){
		if(resp.summary == 0)
		{
			$('#st_state_sales .st_sales_view').html("<div class='st_alert_wrap' style='padding:113px 0px'>No Sales statisticks found between "+start_date+" and "+end_date+"</div>" );	
		}
		else
		{
			// reformat data ;
			$('#stat_head').html("Sales from "+start_date+" to "+end_date);
			if(resp.date_diff <= 30)
		  	{
		  		var interval = 100000;
		    }
			else if(resp.date_diff > 30 && resp.date_diff <= 45)
			{
				var interval = 100000;
			}
			else
			{
				var interval = 2500000;
			}
			$('#st_state_sales .st_sales_view').empty();
			plot2 = $.jqplot('st_state_sales .st_sales_view', [resp.summary], {
		       	
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

function st_terr_stat()
{
	state_id ="<?php echo $this->uri->segment(3);?>";
	start_date= $('#date_from').val();
	end_date= $('#date_to').val();
	
	//$('#stat_head').html("Sales from "+start_date+" to "+end_date);
	$.getJSON(site_url+'/admin/jx_getterritorybystateid/'+state_id+'/'+start_date+'/'+end_date,'',function(resp){
		$('#terr_head').html('Territory Sales');
		if(resp.summary == 0)
		{
			$('#st_terr_stat .st_terr_stat_view').html("<div class='st_alert_wrap' style='padding:113px 0px'>No territory stats Found</div>" );	
		}
		else
		{
			// reformat data ;
			$('#st_more_wrap1').show();
			$('#st_terr_stat .st_terr_stat_view').empty();
			var resp=resp.summary;
			plot2 = $.jqplot('st_terr_stat .st_terr_stat_view', [resp], {
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
			          	label:'Territories',
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
			     },
			      cursor: {
			        show: false
			      }
			});
		}
		//$.jqplot.eventListenerHooks.push(['jqplotClick', myClickHandler]);
		$('#st_terr_stat .st_terr_stat_view').bind('jqplotDataClick', function(ev,seriesIndex,pointIndex,data) {
				var terr_id = resp[pointIndex][2];
			 	var terr_name = resp[pointIndex][0];
			 	town_sales(terr_id,terr_name);
			 	brand_sales(terr_id,terr_name);
			 	franch_sales(terr_id,terr_name);
			 	all_ter_brands(terr_id,terr_name);
			 	all_ter_franchises(terr_id,terr_name);
			 	cat_sales(terr_id,terr_name);
			 	
			 	$('#town_head').html('Towns Sales for '+terr_name+' territory');
			 	$('#cat_head').html('Categories for '+terr_name+' territory');
			 	$('#brand_head').html('Brand Sales for '+terr_name+' territory');
			 	$('#franch_head').html('Top Franchise in '+terr_name+' territory');
			 	
				$('#st_town_stat .st_town_stat_view').unbind('jqplotDataClick');
				$('#st_town_stat .st_town_stat_view').html('<div class="anmtd_loading_img"><span></span></div>');
				$('#st_franch_stat .st_franch_stat_view').unbind('jqplotDataClick');
				$('#st_franch_stat .st_franch_stat_view').html('<div class="anmtd_loading_img"><span></span></div>');
				$('#st_cat_stat .st_cat_stat_view').html('<div class="anmtd_loading_img"><span></span></div>');
				$('#st_brand_stat .st_brand_stat_view').html('<div class="anmtd_loading_img"><span></span></div>');
		});
	});
}

function all_ter_brands(terr_id,terr_name)
{
	start_date= $('#date_from').val();
	end_date= $('#date_to').val();
	$.post(site_url+'/admin/jx_getallbrandsbyterrid/',{terr_id:terr_id,start_date:start_date,end_date:end_date},function(resp){
	if(resp.status == 'error')
	{
		alert(resp.error);
	}else
	{
		 var prod_list_html = '';
		 prod_list_html += '<h4>Brand List for '+terr_name+' Territory</h4>';
		 prod_list_html +='<span class="popclose_button b-close"><span>X</span></span><div style="overflow:auto;float:left;height:265px;width:600px;">';
		 prod_list_html += '<table class="datagrid" width="100%"><thead><tr><th>Sl.No</th><th>Name</th><th>Qty</th><th>Total Sales</th></tr></thead><tbody>';
			$.each(resp.brand_list,function(a,b){
				prod_list_html += '<tr>'
										+'<td>'+(++a)+'</td>'
										+'<td><a target="blank" href="<?php echo site_url("admin/viewbrand/'+b.id+'") ?>#analytics">'+b.name+'</a></td>'
										+'<td>'+b.qty_sold+'</td>'
										+'<td>'+b.ttl+'</td>'
									+'</tr>';
			});
		prod_list_html += '</tbody></table></div>';
		$('#st_brand_popup').html(prod_list_html);
		$('.st_brand_popup').show();
	}
	},'json');
}

function all_ter_franchises(terr_id,terr_name)
{
	start_date= $('#date_from').val();
	end_date= $('#date_to').val();
	$.post(site_url+'/admin/jx_getallfranchisesbyterrid/',{terr_id:terr_id,start_date:start_date,end_date:end_date},function(resp){
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
										+'<td><a target="blank" href="<?php echo site_url("admin/pnh_franchise/'+b.franchise_id+'") ?>#analytics">'+b.franchise_name+'</a></td>'
										+'<td>'+b.ttl+'</td>'
									+'</tr>';
			});
		fran_list_html += '</tbody></table></div>';
		$('#st_franch_popup').html(fran_list_html);
		$('.st_franch_popup').show();
	}
	},'json');
	
}


$('.st_brand_popup').click(function(){
	$('#st_brand_popup').bPopup({
	easing: 'easeOutBack', //uses jQuery easing plugin
	    speed: 450,
	    transition: 'slideDown'
	});
});

$('.st_franch_popup').click(function(){
	$('#st_franch_popup').bPopup({
	easing: 'easeOutBack', //uses jQuery easing plugin
	    speed: 450,
	    transition: 'slideDown'
	});
});

function town_sales(terr_id,terr_name)
{
	state_id ="<?php echo $this->uri->segment(3);?>";
	start_date= $('#date_from').val();
	end_date= $('#date_to').val();
	$('#st_more_wrap2').show();
	$.getJSON(site_url+'/admin/jx_gettownsbystateid/'+state_id+'/'+terr_id+'/'+start_date+'/'+end_date,'',function(resp){
		    	
    	if(resp.summary == 0)
		{
			$('#st_town_stat .st_town_stat_view').html("<div class='st_alert_wrap' style='padding:113px 0px'>No Town stats</div>" );	
		}
		else
		{
			// reformat data ;
			var resp=resp.summary;
			$('#st_town_stat .st_town_stat_view').empty();
			plot2 = $.jqplot('st_town_stat .st_town_stat_view', [resp], {
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
			$('#st_town_stat .st_town_stat_view').bind('jqplotDataClick', function(ev,seriesIndex,pointIndex,data) {
					var town_id = resp[pointIndex][2];
				 	var town_name = resp[pointIndex][0];
				 	
				 	$('#brand_head').html('Brand Sales for '+town_name+' town');
				 	$('#franch_head').html('Top Franchises for '+town_name+' town');
				 	$('#cat_head').html("Top categories sold in "+town_name+" town");
				 	$('#st_cat_stat .st_cat_stat_view').html('<div class="anmtd_loading_img"><span></span></div>');
					$('#st_brand_stat .st_brand_stat_view').html('<div class="anmtd_loading_img"><span></span></div>');
					$('#st_franch_stat .st_franch_stat_view').html('<div class="anmtd_loading_img"><span></span></div>');
				 	town_brand_sales(town_id,town_name);
				 	town_franch_sales(town_id,town_name);
				 	town_cat_sales(town_id,town_name);
				 	all_town_brands(town_id,town_name);
				 	all_town_franchises(town_id,town_name);
			});
		  }
		});
	$('#st_town_stat').show();
}

function all_town_franchises(town_id,town_name)
{
	start_date= $('#date_from').val();
	end_date= $('#date_to').val();
	$.post(site_url+'/admin/jx_getallfranchisesbytownid/',{town_id:town_id,start_date:start_date,end_date:end_date},function(resp){
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
		$('#st_franch_popup').html(fran_list_html);
	}
	},'json');
}

function all_town_brands(town_id,town_name)
{
	start_date= $('#date_from').val();
	end_date= $('#date_to').val();
	$.post(site_url+'/admin/jx_getallbrandsbytownid/',{town_id:town_id,start_date:start_date,end_date:end_date},function(resp){
	if(resp.status == 'error')
	{
		alert(resp.error);
	}else
	{
		 var prod_list_html = '';
		  prod_list_html += '<h4>Brand List for '+town_name+' Territory</h4>';
		 prod_list_html +='<span class="popclose_button b-close"><span>X</span></span><div style="overflow:auto;float:left;height:265px;width:600px;">';
		 prod_list_html += '<table class="datagrid" width="100%"><thead><tr><th>Sl.No</th><th>Name</th><th>Total Sales</th></tr></thead><tbody>';
			$.each(resp.brand_list,function(a,b){
				prod_list_html += '<tr>'
										+'<td>'+(++a)+'</td>'
										+'<td>'+b.name+'</td>'
										+'<td>'+b.ttl+'</td>'
									+'</tr>';
			});
		prod_list_html += '</tbody></table></div>';
		$('#st_brand_popup').html(prod_list_html);
	}
	},'json');
}

function brand_sales(terr_id,terr_name)
{
	state_id ="<?php echo $this->uri->segment(3);?>";
	start_date= $('#date_from').val();
	end_date= $('#date_to').val();
	$.getJSON(site_url+'/admin/jx_get_territory_brand_sales_by_stateid/'+state_id+'/'+terr_id+'/'+start_date+'/'+end_date,'',function(resp){
		    	
    	if(resp.summary == 0)
		{
			$('#st_brand_stat .st_brand_stat_view').html("<div class='st_alert_wrap' style='padding:113px 0px'>No Brand stats found</div>" );	
		}
		else
		{
			// reformat data ;
			$('#st_brand_stat .st_brand_stat_view').empty();
			plot2 = $.jqplot('st_brand_stat .st_brand_stat_view', [resp.summary], {
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
			          	label:'Brands',
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
	$('#st_brand_stat').show();
}

function town_brand_sales(town_id,town_name)
{
	
	state_id ="<?php echo $this->uri->segment(3);?>";
	start_date= $('#date_from').val();
	end_date= $('#date_to').val();
	$.getJSON(site_url+'/admin/jx_get_town_brand_sales_by_stateid/'+state_id+'/'+town_id+'/'+start_date+'/'+end_date,'',function(resp){
		    	
    	if(resp.summary == 0)
		{
			$('#st_brand_stat .st_brand_stat_view').html("<div class='st_alert_wrap' style='padding:113px 0px'>No brand stats found</div>" );	
		}
		else
		{
			// reformat data ;
			$('#st_brand_stat .st_brand_stat_view').empty();
			plot2 = $.jqplot('st_brand_stat .st_brand_stat_view', [resp.summary], {
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
			          	label:'Brands',
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
	$('#st_brand_stat').show();
}

function franch_sales(terr_id,terr_name)
{
	state_id ="<?php echo $this->uri->segment(3);?>";
	start_date= $('#date_from').val();
	end_date= $('#date_to').val();
	$.getJSON(site_url+'/admin/jx_getfranchisebyterritoryid/'+state_id+'/'+terr_id+'/'+start_date+'/'+end_date,'',function(resp){
		    	
    	
		if(resp.summary == 0)
		{
			$('#st_franch_stat .st_franch_stat_view').html("<div class='st_alert_wrap' style='padding:113px 0px'>No Franchises Found</div>" );	
		}
		else
		{
			// reformat data ;
			$('#st_franch_stat .st_franch_stat_view').empty();
			plot2 = $.jqplot('st_franch_stat .st_franch_stat_view', [resp.summary], {
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
	$('#st_franch_stat').show();
}


function town_franch_sales(town_id,town_name)
{
	state_id ="<?php echo $this->uri->segment(3);?>";
	start_date= $('#date_from').val();
	end_date= $('#date_to').val();
	$.getJSON(site_url+'/admin/jx_getfranchisebytown/'+state_id+'/'+town_id+'/'+start_date+'/'+end_date,'',function(resp){
		    	
    	if(resp.summary == 0)
		{
			$('#st_franch_stat .st_franch_stat_view').html("<div class='st_alert_wrap' style='padding:113px 0px'>No Franchises Found</div>" );	
		}
		else
		{
			// reformat data ;
			$('#st_franch_stat .st_franch_stat_view').empty();
			plot2 = $.jqplot('st_franch_stat .st_franch_stat_view', [resp.summary], {
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
	$('#st_franch_stat').show();
}


function cat_sales(terr_id,terr_name)
{
	state_id ="<?php echo $this->uri->segment(3);?>";
	start_date= $('#date_from').val();
	end_date= $('#date_to').val();
	$.getJSON(site_url+'/admin/jx_catsalesbyterritoryid/'+state_id+'/'+terr_id+'/'+start_date+'/'+end_date,'',function(resp){
	  	$('#cat_head').html("Top Categories sold in "+terr_name+" territory");
		if(resp == 0)
		{
			$('#st_cat_stat .st_cat_stat_view').html("<div class='st_alert_wrap' style='padding:113px 0px;'>No Category sales Found</div>" );	
		}
		else
		{	
		// reformat data ;
			$('#st_cat_stat .st_cat_stat_view').empty();
			var resp = resp.summary;
			plot3 = jQuery.jqplot('st_cat_stat .st_cat_stat_view', [resp], 
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
	$('#st_cat_stat').show();
}

function town_cat_sales(town_id,town_name)
{
	state_id ="<?php echo $this->uri->segment(3);?>";
	start_date= $('#date_from').val();
	end_date= $('#date_to').val();
	$.getJSON(site_url+'/admin/jx_catsalesbytown/'+state_id+'/'+town_id+'/'+start_date+'/'+end_date,'',function(resp){
	  	
		if(resp == 0)
		{
			$('#st_cat_stat .st_cat_stat_view').html("<div class='st_alert_wrap' style='padding:113px 0px;'>No Category sales Found</div>" );	
		}
		else
		{	
		// reformat data ;
			$('#st_cat_stat .st_cat_stat_view').empty();
			var resp = resp.summary;
			plot3 = jQuery.jqplot('st_cat_stat .st_cat_stat_view', [resp], 
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
	$('#st_cat_stat').show();
}



</script>
