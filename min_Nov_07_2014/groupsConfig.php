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
	
 // =========================< JS CODE START >================================
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
	$min_assets['js'][] = '../js/gen_validatorv4.js';//BY Shivaraj
	$min_assets['js'][] = '../js/jquery.print-objects.js';//BY Shivaraj
	$min_assets['js'][] = '../js/jquery.timeago.js';//BY S
	$min_assets['js'][] = '../js/tipped.js'; //BY S
	$min_assets['js'][] = '../js/jquery-ui-timepicker-addon.js'; //R
	$min_assets['js'][] = '../js/jquery.printElement.js'; //BY Shivaraj
	$min_assets['js'][] = '../js/jquery.bpopup.js';//BY S
	$min_assets['js'][] = '../js/jquery-sticky.js';//BY S
	$min_assets['js'][] = '../js/jquery.slimscroll.min.js';//BY Sur
	$min_assets['js'][] = '../js/jquery.fix_header_onscroll.js';//BY S
	$min_assets['js'][] = '../js/header_scripts.js'; //BY Shivaraj
	$min_assets['js'][] = '../js/erp-dealstock.js'; // Shivaraj plugin
	$min_assets['js'][] = '../js/erp.js';
	
	$min_assets['offers_js'][] = '../js/manage_offers_script.js'; // Shivaraj
	
	$min_assets['partner_stk_transfer'][] = '../js/partner_stk_transfer_script.js'; // Shivaraj-Sep_29_2014
	
	$min_assets['reservations_js'][] = '../js/manage_trans_reservations_script.js'; // Shivaraj
	$min_assets['reservations_css'][] = '../css/manage_reservations_style.css';//BY Shivaraj
	
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
	
	$min_assets['member_price_update_js'][] = '../js/member_price_update_script.js'; // Shivaraj
	
	$min_assets['tickets_script'][] = '../js/tickets_script.js'; // Shivaraj
	
	$min_assets['viewproduct_js'][] = '../js/viewproduct_js.js'; // Shivaraj
	
	$min_assets['unconfirmed_orderlist'][] = '../js/unconfirmed_orderlist.js'; //Roopa
	
	// =========================< JS CODE ENDS >================================
	
	// =========================< CSS CODE STARTS >================================
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
	// =========================< CSS CODE ENDS >================================
	
	
	// =========================< CSS CODE DATATABLES START >================================
	
	
	$min_assets['datatable_css'][] = '../css/datatable/css/jquery.dataTables.css';
	$min_assets['datatable_css'][] = '../css/datatable/dataTables.tableTools.css';
	$min_assets['datatable_css'][] = '../css/datatable/datatable.common.css';
	// =========================< CSS CODE DATATABLES END >================================
	
	// =========================< JS CODE DATATABLES START >================================
	
	$min_assets['datatable_js'][] = '../js/datatable/jquery.dataTables.js';
	$min_assets['datatable_js'][] = '../js/datatable/jquery.dataTables.min.js';
	$min_assets['datatable_js'][] = '../js/datatable/dataTables.tableTools.js';
	$min_assets['datatable_js'][] = '../js/datatable/jquery.jeditable.js';
	$min_assets['datatable_js'][] = '../js/datatable/jquery.dataTables.editable.js';
	$min_assets['datatable_js'][] = '../js/datatable/dataTables.fixedHeader.js';
	
	// =========================< JS CODE DATATABLES END >================================
	
	
	

return array(

	'js'=>array("../js/jquery.js","../js/jquery.ui.js","../js/cookie.js","../js/func.js","../js/jquery.easing.js","../js/fanb.js","../js/common.js","../js/jquery.pngFix.js","../js/countdown.js","../js/cloud-zoom.1.0.2.min.js"),
	
	'livefeed'=>array("../js/livefeed.js"),
	
	'css'=>array("../css/common.css","../css/jquery.ui.css","../css/fancyb/fancy.css","../css/cloud-zoom.css"),
	'erp_js' =>$min_assets['js'],
	'jqplot_js' =>$min_assets['plot_js'],
	'erp_css' =>$min_assets['css'],
	'jqplot_css' =>$min_assets['plot_css']	
	
	,'reservations_css' =>$min_assets['reservations_css']
	,'reservations_js' =>$min_assets['reservations_js']
	
	,'offers_js' =>$min_assets['offers_js']
		
	,'member_price_js' =>$min_assets['member_price_update_js']
		
	,'tickets_script' =>$min_assets['tickets_script']
	
	,'viewproduct_js' =>$min_assets['viewproduct_js']
		
	,'unconfirmed_orderlist_js'=>$min_assets['unconfirmed_orderlist']
 	,'datatable_css' =>$min_assets['datatable_css']
   ,'datatable_js' =>$min_assets['datatable_js']
	,'partner_stk_transfer'=>$min_assets['partner_stk_transfer']
);
