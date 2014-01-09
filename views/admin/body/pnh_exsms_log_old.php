<div id="container">
<h2 class="page_title">PNH SMS Log</h2>
<div class="tab_view" >
<ul>
<li><a href="#executive">Executive</a></li>
<li><a href="#driver">Driver</a></li>
<li><a href="#franchisee">Franchisee</a></li>
</ul>
<div id="executive">
<div class="tab_view tab_view_inner">

<ul>
<li><a href="#sys_2execu">System To Executive</a></li>
<li><a href="#execu_2system">Executive To System</a></li>
</ul>
<div id="execu_2system" style="padding:0px !important;">
<div class="tab_view tab_view_inner">
<ul>
<li><a href="#paid" class="trg_onload" onclick="load_smslog_data(this,'paid',0)">Paid</a></li>
<li><a href="#new" onclick="load_smslog_data(this,'new',0)">New</a></li>
<li><a href="#existing" onclick="load_smslog_data(this,'existing',0)">Existing</a></li>
<li><a href="#task" onclick="load_smslog_data(this,'task',0)" >Task</a></li>
<li><a href="#ship" onclick="load_smslog_data(this,'ship',0)" >Ship</a></li>
<li><a href="#inv_pickup" onclick="load_smslog_data(this,'inv_pickup',0)" >Pickup</a></li>
<li><a href="#inv_handover" onclick="load_smslog_data(this,'inv_handover',0)" >Handover</a></li>
<li><a href="#ship_delivered" onclick="load_smslog_data(this,'ship_delivered',0)" >Delivered</a></li>
<li><a href="#returned_invoicesms" onclick="load_smslog_data(this,'returned_invoicesms',0)" >Returned</a></li>
</ul>
<div id="paid" >
	<h4>Paid SMS Log</h4> 
	<div class="tab_content"></div>
</div>
<div id="new">
<h4>New Franchises Identified By Executive and TM -  SMS Log</h4>
<div class="tab_content"></div>
</div>



<div id="existing">
<h4>Existing Franchises Issues Solved By Executive Or TM -  SMS Log</h4>
<div class="tab_content"></div>
</div>


<div id="task">
<h4>Task SMS Log</h4>
<div class="tab_content"></div>
</div>

<div id="inv_pickup">
<h4>Manifesto Pickup Log</h4>
<div class="tab_content"></div>
</div>

<div id="inv_handover">
<h4>Invoices handover log</h4>
<div class="tab_content"></div>
</div>

<div id="ship">
<h4>Invoice Ship Log</h4>
<div class="tab_content"></div>
</div>

<div id="ship_delivered">
<h4>Invoice delivered Log</h4>
<div class="tab_content"></div>
</div>

<div id="returned_invoicesms">
<h4>Invoice delivered Log</h4>
<div class="tab_content"></div>
</div>



</div>
</div>
<div id="sys_2execu" style="padding:0px !important;">
<div class="tab_view tab_view_inner">
<ul>
<li><a href="#payment_collection" class="trg_onload" onclick="load_smslog_data(this,'payment_collection',0)">Start Day SMS: Payment Collection</a></li>
<li><a href="#offer_dytoemp"  onclick="load_smslog_data(this,'offer_dytoemp',0)">Offer Of the Day SMS</a></li>
<li><a href="#daysales_summary"  onclick="load_smslog_data(this,'daysales_summary',0)">End Day SMS: Sales Summary</a></li>
<li><a href="#task_remainder" onclick="load_smslog_data(this,'task_remainder',0)">Task Reminder</a></li>
<li><a href="#emp_bouncesms" onclick="load_smslog_data(this,'emp_bouncesms',0)">Cheque Bounce SMS</a></li>
<li><a href="#shipmnet_ntfy" onclick="load_smslog_data(this,'shipmnet_ntfy',0)">Shipments notifications</a></li>
<li><a href="#lr_number_updates" onclick="load_smslog_data(this,'lr_number_updates',0)">LR number updates</a></li>
</ul>
<div id="payment_collection">
<h4>Payment Collection</h4>
<div class="tab_content"></div>
</div>

<div id="offer_dytoemp">
<h4>Offer Of the Day SMS</h4>
<div class="tab_content"></div> 
</div>

<div id="daysales_summary">
<h4>Day Sales</h4>
<div class="tab_content"></div>
</div>

<div id="task_remainder">
<h4>Task Reminder</h4>
<div class="tab_content"></div>
</div>

<div id="emp_bouncesms">
<h4>Cheque Bounce SMS Log</h4>
<div class="tab_content"></div>
</div>

<div id="shipmnet_ntfy">
<h4>Pnh shipments notifications</h4>
<div class="tab_content"></div>
</div>

<div id="lr_number_updates">
<h4>LR number updates</h4>
<div class="tab_content"></div>
</div>




</div>
</div>
</div>
</div>

<div id="driver">
<div class="tab_view tab_view_inner">
<ul>
<li><a href="#sys_2driver" >System To Driver</a></li>
<li><a href="#driver_2system">Driver To System</a></li>
</ul>
<div id="driver_2system" style="padding:0px !important;">
<div class="tab_view tab_view_inner">
<ul>
<li><a href="#delivered_invoicesms" onclick="load_smslog_data(this,'delivered_invoicesms',0)">Delivered</a></li>
<li><a href="#returned_invoicesms" onclick="load_smslog_data(this,'returned_invoicesms',0)" >Returned</a></li>
<li><a href="#dr_excu_invsms"  onclick="load_smslog_data(this,'dr_excu_invsms',0)">Delivered To Executive</a></li>
</ul>
<div id="delivered_invoicesms">
<h4>Delivered Invoice SMS Log</h4>
 	<div class="tab_content"></div>
</div>

<div id="returned_invoicesms">
<h4>Returned Invoice SMS Log</h4>

</div>

<div id="dr_excu_invsms" style="padding:0px !important;">
<h4>Invoices To Executive By Driver SMS Log</h4>
<div class="tab_content"></div>

</div>
</div>
</div>
</div>
</div>


<div id="franchisee">
<div class="tab_view tab_view_inner">
<ul>
<li><a href="#sys_fran" >System To Franchisee</a></li>
<li><a href="#fran_sys">Franchisee To System</a></li>
</ul>
<div id="fran_sys" style="padding:0px !important;">
<div class="tab_view tab_view_inner">
<ul>
<li><a href="#call"  onclick="load_smslog_data(this,'call',0)">Call</a></li>
</ul>
<div id="call">
<h4>Call SMS Log</h4>
<div class="tab_content"></div> 
</div>

</div>
</div>

<div id="sys_fran" style="padding:0px !important;">
<div class="tab_view tab_view_inner">
<ul>
<li><a href="#start_dysmsfran"  onclick="load_smslog_data(this,'start_dysmsfran',0)">Start Day SMS:Current Bal</a></li>
<li><a href="#offer" onclick="load_smslog_data(this,'offer',0)" >Offer</a></li>
<li><a href="#dinvoicesms_tofran" onclick="load_smslog_data(this,'end_dysms',0)" >Delivered Invoice SMS </a></li>
<li><a href="#rinvoicesms_tofran" onclick="load_smslog_data(this,'end_dysms',0)" >Returned Invoice SMS </a></li>
<li><a href="#end_dysms" onclick="load_smslog_data(this,'end_dysms',0)" >End Day SMS </a></li>
<li><a href="#fran_chqbounce" onclick="load_smslog_data(this,'fran_chqbounce',0)">Cheque Bounce SMS</a></li>


</ul>
<div id="start_dysmsfran">
<h4>Start Day SMS Log</h4>
<div class="tab_content"></div> 
</div>

<div id="offer">
<h4>Offer Of The Day SMS Log</h4>
<div class="tab_content"></div> 
</div>


<div id="dinvoicesms_tofran">
<h4>Delivered Invoice SMS Log</h4>
<div class="tab_content"></div> 
</div>

<div id="rinvoicesms_tofran">
<h4>Returned Invoice SMS Log</h4>
<div class="tab_content"></div> 
</div>

<div id="end_dysms">
<h4>End Day SMS Log</h4>
<div class="tab_content"></div>
</div>

<div id="fran_chqbounce">
<h4>Cheque Bounce  SMS Log</h4>
<div class="tab_content"></div>
</div>

</div>
</div>
</div>
</div>

</div>
</div>

<script>
var loaded_logtype = '';
var loaded_logele = '';
	function show_log()
	{
		location.href = site_url+'/admin/pnh_exsms_log/'+$('#inp_date').val();
	}
	
		
	function  load_smslog_data(ele,type,pg,terr_id)
	{
		loaded_logele = ele;
		loaded_logtype = type;
		terr_id=$('#logdet_disp_terry').val()*1;
		$($(ele).attr('href')+' div.tab_content').html('<div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
		$.post(site_url+'/admin/jx_getpnh_exsms_log/'+type+'/'+terr_id+'/'+pg,'',function(resp){
			$($(ele).attr('href')+' div.tab_content').html(resp.log_data+resp.pagi_links);
			$($(ele).attr('href')+' div.tab_content .datagridsort').tablesorter();
		},'json');
	}
	
	$('#inp_date').datepicker();
	$("#logdet_disp_terry").change(function(){
		load_smslog_data(loaded_logele,loaded_logtype,0,$(this).val()*1);
	});

	$('.tab_view').tabs();
	$('.datagridsort').tablesorter( {sortList: [[0,0]]} );

	$('.log_pagination a').live('click',function(e){
		e.preventDefault();
		$.post($(this).attr('href'),'',function(resp){
			$('#'+resp.type+' div.tab_content').html(resp.log_data+resp.pagi_links);
			$('#'+resp.type+' div.tab_content .datagridsort').tablesorter();
		},'json');
	});
	
	$('.trg_onload').trigger('click');

	$("#logdet_disp_terry").chosen();	

</script>