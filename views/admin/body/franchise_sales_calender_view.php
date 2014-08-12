<style>
.f_sale
{
	margin-top: 42px;
    overflow: auto;
    max-width:1300px;
}
#date_range
{
	width: 300px;
	display: inline-block;
}
.fran_det_sales
{
    background: none repeat scroll 0 0 #0000FF;
   	border-radius: 5px;
	color: #FFFFFF;
	font-size: 14px;
	padding: 5px 0;
	margin: 3px auto;
	text-align: center;
	width: 200px;
	cursor: pointer;
}
.fran_det_shipment
{
    background: none repeat scroll 0 0 #008000;
	border-radius: 5px;
	color: #FFFFFF;
	font-size: 14px;
	padding: 5px 0;
	margin: 3px auto;
	text-align: center;
	width: 200px;
	cursor: pointer;
}
.fran_det_realized
{
    background: none repeat scroll 0 0 #777;
    border-radius: 5px;
	color: #FFFFFF;
	font-size: 14px;
	padding: 5px 0;
	margin: 3px auto;
	text-align: center;
	width: 200px;
}
.custom1 {
	border-bottom: 1px solid #ccc;
    border-right: 1px solid #ccc;
    height: 126px;
    width: 400px;
    }
.custom2 {
    border-bottom: 1px solid #ccc;
    border-right: 1px solid #ccc;
    height: 106px !important;
    padding: 10px 0;
    }
.table1 {
    float: left;   
    }        
.table2 {
    overflow: auto;  
    }
.tab1_custom_th
{
	padding:7px;background:none repeat scroll 0 0 #D7E2EE;border-right:1px solid #000 !important;text-align:center;
}
.tab2_custom_th
{
	background: none repeat scroll 26px 17px #D7E2ED;
	border-right: 1px solid #ccc !important;
	text-align: center;
	height: 30px;
	font-size: 12px;
	min-width: 225px;
}
.title_wrap
{
	width:30%;
	display:inline-block;
}
.filters_blk
{
	display: inline-block;
    text-align: right;
    width: 69%;
}
.franch_font
{
	color: #000000;
    display: inline-block;
    font-size: 15px;
    text-decoration: none !important;
}
.town
{
	color: #777777;
    font-size: 11px;
}
.ttl_wrap_sale
{
	 background: none repeat scroll 0 0 #0000FF;
    color: #FFFFFF;
    display: inline-block;
    font-size: 12px;
    font-weight: bold;
   margin:14px 9px 15px;
    padding: 5px;
}
.ttl_wrap_shipment
{
	 background: none repeat scroll 0 0 #008000;
    color: #FFFFFF;
    display: inline-block;
    font-size: 12px;
    font-weight: bold;
    margin: 14px 9px 15px;
    padding: 5px;
}
.ttl_wrap_payment
{
	 background: none repeat scroll 0 0 #777777;
    color: #FFFFFF;
    display: inline-block;
    font-size: 12px;
    font-weight: bold;
    margin:14px 9px 15px;
    padding: 5px;
}
</style>
<div class="container">
	<h2 class="title_wrap">Franchise Sales</h2>
	
	<div class="filters_blk">
		<!-- State Filter  -->
		<b>State :</b> 
		<select name="state" id="sid">
			<?php $states=$this->db->query("select * from pnh_m_states group by state_id")->result_array(); 
				foreach($states as $s)
				{
			?>
				<option value="<?=$s['state_id']?>"><?=$s['state_name']?></option>
			<?php		
				}	
			?> 
		</select>
		
		<!-- Territory Filter by State  -->		
		<b>Territory :</b> 
		<select name="territory" id="territoryid">
			<?php $terr=$this->db->query("select * from pnh_m_territory_info  group by id order by territory_name")->result_array(); 
				foreach($terr as $t)
				{
			?>
				<option value="<?=$t['id']?>"><?=$t['territory_name']?></option>
			<?php		
				}	
			?> 
     	</select>		
				
		<!-- Months  -->		
		<b>Month :</b> 
		<select name="month" class="month" id="month">
			<option value="0">Choose</option>
			<option value='01'>January</option>
			<option value='02'>Febraury</option>
			<option value='03'>March</option>
			<option value='04'>April</option>
			<option value='05'>May</option>
			<option value='06'>June</option>
			<option value='07'>July</option>
			<option value='08'>August</option>
			<option value='09'>September</option>
			<option value='10'>October</option>
			<option value='11'>November</option>
			<option value='12'>December</option>
		</select>	
		
		<!-- Year filter -->
		<b>Year :</b> 
		<select name="year" class="year">
			<?php
				for($k=date('Y');$k>=2012;$k--)
					echo '<option value="'.$k.'">'.$k.'</option>'; 
			?>
		</select>
	</div>
	
	<div class="fl_left">
		<!-- Days Registered Filter -->
		<select name="days" class="days_registered">
			<option value="0">Choose</option>
			<option value="15">Last 15 days registered</option>
			<option value="30">Last 30 days registered</option>
			<option value="60">Last 60 days registered</option>
		</select>	
	</div>
		
	<div class="f_sale">
		
	</div>								
</div>

<!-- Ordered detail modal on select date-->
<div id="ordered_log_dlg" >
	<div id="order_log_dlg_wrap"></div>
</div>

<!-- Shipment detail modal on select date -->
<div id="ship_log_dlg" >
		<div id="ship_log_dlg_wrap"></div>
	</div>
<script>
$(function(){
	var mnth=$('.month').val();//month selected
	var year=$('.year').val();//Year selected
	var sel_terr = $('#territoryid').val();//Territory selected
	var sel_state = $('#sid').val();//State selected
		cur_mnth=<?php echo date('m') ?>;
		cur_year=<?php echo date('Y') ?>;	
		
		
		if(mnth == 0 || year == 0 )// Check if whether both month and year selected
			franch_calender_det(cur_mnth,cur_year,sel_terr,sel_state,0);
		else  //  if not print current month sales,shipment details
			franch_calender_det(mnth,year,sel_terr,sel_state,0);
	
		//State dropdown change event
		$("#sid").change(function(){
			var sid = $(this).val();
				$("#territoryid").html('<option>loading...</option>');
				if(sid)
				{
					$.getJSON(site_url+'/admin/jx_load_all_territories_bystate/'+sid,'',function(resp){
						var terlist_html = '<option value="">Choose</option>';
							if(resp.terrs_bystate.length)
							{
								$.each(resp.terrs_bystate,function(a,b){
									terlist_html += '<option value="'+b.id+'">'+b.territory_name+'</option>';
								});
							}
						$("#territoryid").html(terlist_html);	
					});
				}
		});
	
		//Year dropdown change event
		$('.year').live('change',function(){
			var mnth = $('.month').val();//month selected
			var year = $('.year').val();//Year selected
			var sel_terr = $('#territoryid').val();//Territory selected
			var sel_state = $('#sid').val();//State selected
			$('.days_registered').val('0');
			if(mnth != 0 && year!=0)
				franch_calender_det(mnth,year,sel_terr,sel_state,0);
		});
		
		//Territory dropdown change event
		$('#territoryid').live('change',function(){
			var mnth = $('.month').val();//month selected
			var year = $('.year').val();//Year selected
			var sel_terr = $('#territoryid').val();//Territory selected
			var sel_state = $('#sid').val();//State selected
			$('.days_registered').val('0');
			if(mnth != 0 && year!=0)
				franch_calender_det(mnth,year,sel_terr,sel_state,0);
		});
		
		//Month dropdown change event
		$('.month').live('change',function(){
			var mnth = $('.month').val();//month selected
			var year = $('.year').val();//Year selected
			var sel_terr = $('#territoryid').val();//Territory selected
			var sel_state = $('#sid').val();//State selected
			$('.days_registered').val('0');
			if(mnth != 0 && year!=0)
				franch_calender_det(mnth,year,sel_terr,sel_state,0);
		});
	});
	
	$('.days_registered').live('change',function(){
			var days=$(this).val();
			var mnth = $('.month').val();//month selected
			var year = $('.year').val();//Year selected
			var sel_terr = $('#territoryid').val();//Territory selected
			var sel_state = $('#sid').val();//State selected
			if(mnth != 0 && year!=0)
			franch_calender_det(mnth,year,sel_terr,sel_state,days);
	});
	
	//Franchise sales details on click 
	$('.fran_det_sales').live('click',function(){
		var fid=$(this).attr('fid');//Franchise Id selected
		var ord_date=$(this).attr('ord_date');//Dates
		var title = 'Items Ordered on '+ord_date;
		$("#ordered_log_dlg" ).data("pro_data",{'fid': fid,'ord_date':ord_date}).dialog('open')
		$( "#ordered_log_dlg" ).dialog('option','title',title);
	});
	
	//Franchise Shipment details on click 
	$('.fran_det_shipment').live('click',function(){
		var fid=$(this).attr('fid');
		var ord_date=$(this).attr('ord_date');
		var title = 'Items Shipped on '+ord_date;
		$("#ship_log_dlg" ).data("pro_data",{'fid': fid,'ord_date':ord_date}).dialog('open')
		$( "#ship_log_dlg" ).dialog('option','title',title);
	});
	
	
function franch_calender_det(mnth,year,sel_terr,sel_state,days_registered)
{
	var def_month=$('.month').val();//month selected
	var def_year=$('.year').val();//Year selected
	var el = document.getElementById('month');
	var text = el.selectedIndex == -1 ? null : el.options[el.selectedIndex].text;
	var cur_mnth='<?php echo date('M') ?>';
	var cur_year='<?php echo date('Y') ?>';
	
	if(def_month == 0 || def_year == 0)
	{
		$('.title_wrap').html("Franchise Sales - "+cur_mnth+","+cur_year);	
	}else
	{
		$('.title_wrap').html("Franchise Sales - "+text+","+year);
	}
	
		$('.f_sale table thead tr').html("");
		$('.f_sale table').html("<img src='"+base_url+"/images/loading_bar.gif"+"'>");
		$.getJSON(site_url+'/admin/jx_franchise_activitybydates/'+sel_state+'/'+sel_terr+'/'+mnth+'/'+year+'/'+days_registered,'',function(resp){
			
			var report_html = '';
				report_html += '<div>';
				report_html += '<table class="table1" cellpadding="0" cellspacing="0" border="0">';
			    report_html += '        <thead>';
			    report_html += '  			  <tr>';
			    report_html += '            <th class="tab1_custom_th" style="" width="100px">Franchise Name</th>';
			    report_html += '    		 </tr>';
			    report_html += '        </thead>';
			   $.each(resp.report,function(k,f_date_sales){
			   			var ttl_sales=0;var ttl_ship=0;var ttl_realized=0;
						report_html += '<tr>';
						report_html += 		'<td width="100px" class="custom1"><div style="padding:10px"><a class="franch_font" target="blank" href="'+site_url+'/admin/pnh_franchise/'+f_date_sales.id+'">'+f_date_sales.name+'</a></div>';
						report_html += 			'<div class="town" style="padding:9px">Town : '+f_date_sales.town+'</div>';
						
						$.each(resp.dates,function(a,b){
							
							if(f_date_sales.data.sales[b] != undefined)
							{
								ttl_sales = (+ttl_sales) + (+f_date_sales.data.sales[b][0]);
							}
														
							if(f_date_sales.data.shipment[b] != undefined)
							{
								ttl_ship = (+ttl_ship) + (+f_date_sales.data.shipment[b][0]);
							}
							
							if(f_date_sales.data.realized[b] != undefined)
							{
								ttl_realized = (+ttl_realized) + (+f_date_sales.data.realized[b][0]);
							}
							
						});
					report_html += '<div class="ttl_wrap_sale">Sales: '+ttl_sales+'</div><div class="ttl_wrap_shipment">Shipment: '+ttl_ship+'</div><div class="ttl_wrap_payment">Cash in Bank: '+ttl_realized+'</div>';
					report_html += 		'</td>';
					report_html += '</tr>'; 
				});
			    report_html += '</table> '; 
			    report_html += '</div>';  
			
			    report_html += '<div class="table2"  id="table2">';
				report_html += '    <table class="table1" cellpadding="0" cellspacing="0" border="0">';
				report_html += '        <thead>';
				report_html += '        	<tr>';
				$.each(resp.headers,function(a,b){
					report_html += '				<th class="tab2_custom_th">'+b+'</th>';
				});
				report_html += '				<th  class="tab2_custom_th">Total Value</th>';
				
				report_html += '        	</tr>';
				report_html += '       </thead>';
				$.each(resp.report,function(k,f_date_sales){
					var ttl_sales=0;var ttl_ship=0;var ttl_realized=0;
					report_html += '<tr>';
						$.each(resp.dates,function(a,b){
							report_html += '<td width="250px" class="custom2">'; 
							
							if(f_date_sales.data.sales[b] != undefined)
							{
								ttl_sales = (+ttl_sales) + (+f_date_sales.data.sales[b][0]);
								report_html += '<div class="fran_det_sales" fid="'+f_date_sales.id+'" ord_date="'+f_date_sales.data.sales[b][1]+'" ><div >Sales : <b>Rs. '+f_date_sales.data.sales[b][0]+'</b></div></div>';
							}
														
							if(f_date_sales.data.shipment[b] != undefined)
							{
								ttl_ship = (+ttl_ship) + (+f_date_sales.data.shipment[b][0]);
								report_html += '<div class="fran_det_shipment" fid="'+f_date_sales.id+'" ord_date="'+f_date_sales.data.shipment[b][1]+'" ><div>Shipment : <b>Rs. '+f_date_sales.data.shipment[b][0]+'</b></div></div>';
							}
							
							if(f_date_sales.data.realized[b] != undefined)
							{
								ttl_realized = (+ttl_realized) + (+f_date_sales.data.realized[b][0]);
								report_html += '<div class="fran_det_realized"><div>Realized : <b>Rs. '+f_date_sales.data.realized[b][0]+'</b></div></div>';
							}
							
							report_html += '</td>';	
							
						});
					report_html += '<td width="250px" class="last_item na_class custom2" style=""><div class="fran_det_sales">Total sales: '+ttl_sales+'</div><div class="fran_det_shipment">Total shipment: '+ttl_ship+'</div><div class="fran_det_realized">Total Cash in Bank: '+ttl_realized+'</div></td>';
					report_html += '</tr>'; 
				});   
				report_html += '    </table> ';   
			    report_html += '</div>';			
			
			$(".f_sale").html(report_html);
			$('.f_sale').show();	
		});
}

//Dialog for franchise sales
$("#ordered_log_dlg" ).dialog({
		modal:true,
		autoOpen:false,
		width:'1000',
		height:'450',
		autoResize:true,
		open:function(){
		

		var dlgData = $("#ordered_log_dlg").data("pro_data");
		var ordered_date=dlgData.ord_date;
		var fid=dlgData.fid;
		
		$('#order_log_dlg_wrap').html('<div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');	
		$.post(site_url+'/admin/jx_franchise_ordered_log_bydate',{ fid:fid, ordered_date:ordered_date},function(result){
	   if(result.status == 'failure')
		{
			 $('#order_log_dlg_wrap').html('No Orders on '+ordered_date);
			 return false;
	    }
	    else
		{
	    	var order_det='';
	    	var k=1;
	    	 
	    	 order_det +='<table class="datagrid" width="100%"  ><tr><th width="5%">Sl.No</th><th>TransID</th><th>Itemid</th>';
	    	 order_det +='<th>Item</th><th>Quantity</th><th>Commission</th><th>Amount</th></tr>';
	    	 $.each(result.order_det,function(i,s1){
	    	 	s = s1[0];
	    	 	
	    	 		order_det +='<tr>';
	    	 		order_det +='	<td rowspan="'+s1.itemid.length+'">'+(k++)+'</td>';
	    	 		order_det +='	<td rowspan="'+s1.itemid.length+'"><a href="'+site_url+'/admin/trans/'+s1.transid+'" target="_blank">'+s1.transid+'</a></td>';
	    	 		j=0;
	    	 		$.each(s1.itemid,function(a,b){
	    	 			if(j!=0)
	    	 				order_det +='<tr>';			
	    	 			order_det +='	<td>'+b.itemid+'</a></td>';
		    	 		order_det +='	<td><a href="'+site_url+'/admin/pnh_deal/'+b.itemid+'" target="_blank">'+b.name+'</a></td>';
		    	 		order_det +='	<td>'+b.qty+'</td>';
		    	 		order_det +='	<td>'+b.com+'</td>';
		    	 		order_det +='	<td>'+b.amount+'</td>';
		    	 		if(j!=0)
	    	 				order_det +='</tr>';
	    	 				j++;	
	    	 		});
	    	 		order_det +='</tr>';
	    	 });			
	    	 order_det +='<tfoot class="nofooter"><tr><td>Total </td><td></td><td></td><td></td><td style="text-align:left">'+result.ttl_qty+'</td><td style="text-align:left">Rs.'+result.ttl_com+'</td><td style="text-align:left">Rs.'+result.ttl_amt+'</td><td></td><td></td><td></td></tr></tfoot>';
	    	 $('#order_log_dlg_wrap').html(order_det);	
		}
	  },'json');
	}
});
 
//Dialog for franchise shipment
$("#ship_log_dlg" ).dialog({
		modal:true,
		autoOpen:false,
		width:'1000',
		height:'450',
		autoResize:true,
		open:function(){
			
		var dlgData = $("#ship_log_dlg").data("pro_data");
		var ship_date=dlgData.ord_date;
		var fid=dlgData.fid;
		
		$('#ship_log_dlg_wrap').html('<div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');	
		// ajax request fetch task details
	   $.post(site_url+'/admin/jx_franchise_shipment_log_bydate',{ fid:fid, ship_date:ship_date},function(result){
	   if(result.status == 'failure')
		{
			 $('#ship_log_dlg_wrap').html('No Shipments on '+ship_date);
			 return false;
	    }
	    else
		{
	    	var shipment_det='';
	    	var k=1;
	    	 
	    	 shipment_det +='<table class="datagrid" width="100%"  ><tr><th width="5%">Sl.No</th><th>TransID</th><th>Invoice</th>';
	    	 shipment_det +='<th>Item</th><th>Quantity</th><th>Amount</th><th></th></tr>';
	    	 $.each(result.ship_det,function(i,s1){
	    	 	s = s1[0];
	    	 	
	    	 		shipment_det +='<tr>';
	    	 		shipment_det +='	<td rowspan="'+s1.invoices.length+'">'+(k++)+'</td>';
	    	 		shipment_det +='	<td rowspan="'+s1.invoices.length+'"><a href="'+site_url+'/admin/trans/'+s1.transid+'" target="_blank">'+s1.transid+'</a><br /><span style="font-size:10px;font-weight:bold">Ordered On : '+s1.ord_on+'</span> </td>';
	    	 		j=0;
	    	 		$.each(s1.invoices,function(a,b){
	    	 			if(j!=0)
	    	 				shipment_det +='<tr>';			
	    	 			shipment_det +='<td><a href="'+site_url+'/admin/invoice/'+b.invoice_no+'" target="_blank">'+b.invoice_no+'</a></td>';
		    	 		shipment_det +='	<td><a href="'+site_url+'/admin/pnh_deal/'+b.itemid+'" target="_blank">'+b.name+'</a></td>';
		    	 		shipment_det +='	<td>'+b.qty+'</td>';
		    	 		shipment_det +='	<td>'+b.amount+'</td>';
		    	 		shipment_det +='	<td><a class="link_btn" onclick="get_invoicetransit_log(this,'+b.invoice_no+')" href="javascript:void(0)">View Transit Log</a></td>';
		    	 		if(j!=0)
	    	 				shipment_det +='</tr>';
	    	 				j++;	
	    	 		});
	    	 		shipment_det +='</tr>';
	    	 });			
	    	 shipment_det +='<tfoot class="nofooter"><tr><td>Total </td><td></td><td></td><td></td><td style="text-align:left">'+result.ttl_qty+'</td><td style="text-align:left">Rs.'+result.ttl_amt+'</td><td></td><td></td><td></td></tr></tfoot>';
	    	 $('#ship_log_dlg_wrap').html(shipment_det);	
		}
	  },'json');
	}
});

$('.scroll').live('click',function(){
	alert('1');
	$('.table2').animate({'left':'+=300px'});
});
</script>