<?php
/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */

/** 
 * You may wish to use the Minify URI Builder app to suggest
 * changes. http://yourdomain/min/builder/
 **/


 $min_assets = array('js'=>array(),'css'=>array());
	
	$min_assets['js'][] = '../js/jquery-1.8.1.min.js';
	$min_assets['js'][] = '../js/jquery-ui-1.10.2.js';
	$min_assets['js'][] = '../js/jqmanageList.js';
	$min_assets['js'][] = '../js/chosen.jquery.min.js';
	$min_assets['js'][] = '../js/jquery.inlineclick.js';
	$min_assets['js'][] = '../js/jquery.qtip.min.js';
	$min_assets['js'][] = '../js/jquery.tablesorter.js';
	$min_assets['js'][] = '../js/func.js';
	$min_assets['js'][] = '../js/parsley.js';
	$min_assets['js'][] = '../js/jquery.jqEasyCharCounter.min.js';
	$min_assets['js'][] = '../js/jquery.mtz.monthpicker.js';
	$min_assets['js'][] = '../js/jquery.tablesorter.js';
	$min_assets['js'][] = '../js/gen_validatorv4.js';//BY S
	$min_assets['js'][] = '../js/jquery.print-objects.js';//BY S
	$min_assets['js'][] = '../js/jquery.timeago.js';//BY S
	$min_assets['js'][] = '../js/tipped.js'; //BY S
	$min_assets['js'][] = '../js/jquery-ui-timepicker-addon.js'; //R
	$min_assets['js'][] = '../js/jquery.printElement.js'; //BY S

	$min_assets['js'][] = '../js/jquery.bpopup.js';//BY S
	$min_assets['js'][] = '../js/jquery-sticky.js';//BY S
	$min_assets['js'][] = '../js/jquery.slimscroll.min.js';//BY Sur
	$min_assets['js'][] = '../js/jquery.fix_header_onscroll.js';//BY S
	$min_assets['js'][] = '../js/header_scripts.js'; //BY S
	$min_assets['js'][] = '../js/erp-dealstock.js'; // Shiv plugin
	$min_assets['js'][] = '../js/erp.js';
	
	//JQplot Plugins for analytics view
	$min_assets['plot_js'][] = '../js/jq_plot/jquery.jqplot.min.js';//BY Sur
	$min_assets['plot_js'][] = '../js/jq_plot/plugins/jqplot.highlighter.min.js';//BY Sur
	$min_assets['plot_js'][] = '../js/jq_plot/plugins/jqplot.pointLabels.min.js';//BY Sur
	$min_assets['plot_js'][] = '../js/jq_plot/plugins/jqplot.cursor.min.js';//BY Sur
	$min_assets['plot_js'][] = '../js/jq_plot/plugins/jqplot.dateAxisRenderer.min.js';//BY Sur
	$min_assets['plot_js'][] = '../js/jq_plot/plugins/jqplot.barRenderer.min.js';//BY Sur
	$min_assets['plot_js'][] = '../js/jq_plot/plugins/jqplot.pieRenderer.min.js';//BY Sur
	$min_assets['plot_js'][] = '../js/jq_plot/plugins/jqplot.categoryAxisRenderer.min.js';//BY Sur
	$min_assets['plot_js'][] = '../js/jq_plot/plugins/jqplot.canvasAxisLabelRenderer.min.js';//BY Sur
	$min_assets['plot_js'][] = '../js/jq_plot/plugins/jqplot.canvasTextRenderer.min.js';//BY Sur
	$min_assets['plot_js'][] = '../js/jq_plot/plugins/jqplot.canvasAxisTickRenderer.min.js';//BY Sur
	
	$min_assets['plot_css'][] = '../js/jq_plot/jquery.jqplot.min.css';//BY Sur
	$min_assets['plot_css'][] = '../css/plot.css';//BY Sur
	
        
	//$min_assets['css'][] = '../css/jquery-ui-lib/jquery-ui-1.10.2.custom.min.css';
	//$min_assets['css'][] = '../css/jquery-ui/redmond/jquery-ui-1.10.2.custom.min.css';
	$min_assets['css'][] = '../css/jquery-ui/sk-grey/jquery-ui-1.10.4.custom.min.css';

	$min_assets['css'][] = '../css/chosen.css';
	$min_assets['css'][] = '../css/jquery.qtip.min.css';
	$min_assets['css'][] = '../css/tipped.css';
	$min_assets['css'][] = '../css/admin.css';
	$min_assets['css'][] = '../css/buttons.css';
	$min_assets['css'][] = '../css/jquery-ui-timepicker-addon.css';// R
	$min_assets['css'][] = '../css/erp.css';
	

return array(

	'js'=>array("../js/jquery.js","../js/jquery.ui.js","../js/cookie.js","../js/func.js","../js/jquery.easing.js","../js/fanb.js","../js/common.js","../js/jquery.pngFix.js","../js/countdown.js","../js/cloud-zoom.1.0.2.min.js"),
	
	'livefeed'=>array("../js/livefeed.js"),
	
	'css'=>array("../css/common.css","../css/jquery.ui.css","../css/fancyb/fancy.css","../css/cloud-zoom.css"),
	'erp_js' =>$min_assets['js'],
	'jqplot_js' =>$min_assets['plot_js'],
	'erp_css' =>$min_assets['css'],
	'jqplot_css' =>$min_assets['plot_css']	
);
