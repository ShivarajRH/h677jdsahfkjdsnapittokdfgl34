<div class="container">
	<h2>Sales Analytics</h2>
	<div id="graph_year_sales_stat" style="height: 300px;">
		<div class="graph_options" align="right">
			<select name="sel_year">
				<option value="2011">2011</option>
				<option value="2012" selected>2012</option>
				<option value="2013" >2013</option>
			</select>
		</div>
		<div class="graph_view">
		
		</div>
	</div>
</div>
<link class="include" rel="stylesheet" type="text/css" href="<?php echo base_url();?>/js/jq_plot/jquery.jqplot.min.css" />
<script class="include" type="text/javascript" src="<?php echo base_url();?>/js/jq_plot/jquery.jqplot.min.js"></script>
<script class="include" type="text/javascript" src="<?php echo base_url();?>/js/jq_plot/plugins/jqplot.barRenderer.min.js"></script>
<script class="include" type="text/javascript" src="<?php echo base_url();?>/js/jq_plot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
<script>

function load_year_sales(y)
{
	$.getJSON(site_url+'/admin/sales_get_salesbyyear/'+y,'',function(resp){
		if(resp.status == 'error')
		{
			alert(resp.message);	
		}
		else
		{
			
				// reformat data ;
			$('#graph_year_sales_stat .graph_view').empty();
			 
		    plot2 = $.jqplot('graph_year_sales_stat .graph_view', [resp.summary.all, resp.summary.sit,resp.summary.pnh,resp.summary.part], {
		        seriesDefaults: {
		            renderer:$.jqplot.BarRenderer,
		            pointLabels: { show: true }
		        },
	            legend: {
	                show: true,
	                location: 't',
	                placement: 'inside'
	            },
		        axes: {
		            xaxis: {
		                renderer: $.jqplot.CategoryAxisRenderer,
		                ticks: resp.ticks
		            }
		        }
		    });

		    
		}
	});
	
}

$(document).ready(function(){

    $('select[name="sel_year"]').change(function(){
        load_year_sales($(this).val());
    }).trigger('change');
 
});
</script>