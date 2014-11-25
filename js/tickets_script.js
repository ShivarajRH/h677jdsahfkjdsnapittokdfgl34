/**
 * @author Shivaraj <shivaraj@storeking.in>_Jul_14_2014
 * @desc	Support Tickets Page Scripts
 * @date	Jul_14_2014
 * @last_modified_on Jul_14_2014
 */
$(document).ready(function() {
	$("#ds_range,#de_range").datepicker({
		changeMonth:true
		,changeYear:true
	});
	
	load_tab_content(1);
});

$( "#manage_tickets_tab" ).tabs({
	beforeLoad: function( event, ui ) {
	  ui.panel.html('<div class="loading">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Loading...</div>');

	  ui.jqXHR.error(function() {
		ui.panel.html("<p>Error while loading...</p>");
	  });
	},load:function( event, ui ) { /* After page load*/ }
});
	
// Pagination links
/*$(".pagination_snip a").live("click",function(e){
	  var url_str =$(this).attr("href");
	  $( "#manage_offers_tab .ui-tabs-panel.ui-widget-content" ).html('<div class="loading">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Loading...</div>');
	  $( "#manage_offers_tab .ui-tabs-panel.ui-widget-content" ).load(url_str);
	  return false;
});*/


$('.pagination_link a').live('click',function(e){
	e.preventDefault();
	load_tickets($(this).attr('href'));
});

function showtickets()
{
//	if($("#ds_range").val().length==0 ||$("#ds_range").val().length==0){
//		alert("Pls enter date range");	return false;
//	}
	//location='<?php //echo site_url("admin/support/$filter")?>/'+$("#ds_range").val()+"/"+$("#de_range").val(); 
	load_tickets();
	return false;
}
function load_tab_content(source) {
	$(".source").val(source);
	
	if(source=='2') {
		$("#franchise_id").val('0');
		$("#franchise_filter_block").hide();
	}
	else
		$("#franchise_filter_block").show();
	
	load_tickets("");
}
function change_related_to(elt)
{
	
}

function load_tickets(pagi_url)
{
	var colcount=16;
	var source = $(".source").val(); //$("#related_to").val();
	var date_from = $("#ds_range").val();
	var date_to = $("#de_range").val();
	
	var franchise_id = $("#franchise_id").val();
	var status = $("#status").val();
	var priority = $("#priority").val();
	var tickets_from = $("#tickets_from").val();
	
	var type = $(".type").val();
	var related_to = $(".related_to").val();
	
	if(date_from=='')
		date_from=0;
	if(date_to=='')
		date_to=0;
	
	
	$('.pagination_link').html('');
	$('.pg_msg').html('');
	var urlpath='';
	if(pagi_url == '' || pagi_url == undefined)
		urlpath = site_url+"/admin/support_tickts_jx/"+source+"/"+date_from+"/"+date_to+"/"+franchise_id+"/"+status+"/"+priority+"/"+tickets_from+"/"+type+"/"+related_to+"/0"; // /all/
	else
		urlpath = pagi_url;
	
	
	$(".show_tickets_tbl tbody").html('<tr><td colspan="'+colcount+'" align="center"><div class="loading">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Loading...</div></td></tr>');
	//print(urlpath); //return false;
	$.post(urlpath,{},function(rdata) {
		var htmldata ='';

		if(rdata.status == "success")
		{
			$.each(rdata.tickets,function(i,tkt) {
				
				var status_msg='';
				switch( tkt.status ) {
					case "0":
						status_msg='Pending';
						break;
					case "1":
						status_msg='Opened';
						break;
					case "2":
						status_msg='In Progress';
						break;
					case "3":
						status_msg='Closed';
						break;
					default:
						status_msg='Unknown';
						break;
				}
				
				var user_det='';
				if(tkt.source==1)
					user_det=tkt.req_mem_name;
				else {
					if(tkt.name!=null && tkt.name!='' )
						user_det=''+tkt.name+'';
				}
			
				/*var user='--';
				if(tkt.franchise_name!=null)
					user = tkt.franchise_name;
				else if(tkt.user!=null)
					user=tkt.user;*/
				
				
				var franchise_name='--';
				if(tkt.franchise_name!='' && tkt.franchise_name!=null)
					franchise_name='<a href="'+site_url+'/admin/pnh_franchise/'+tkt.franchise_id+'" target="_blank">'+tkt.franchise_name+'</a>';
				
				var created_by='--';
				if(tkt.created_by!=null && tkt.created_by!='')
					created_by=tkt.created_by;
				
				var email='--';
				if(tkt.email!='')
					email=tkt.email;
				
				var transid='--';
				if(tkt.transid!='')
					transid=tkt.transid;
				
				htmldata +='<tr>\n\
					<td>'+((rdata.pg*1)+(i*1)+1)+'</td>\n\
					<td>'+tkt.created_on+'</td>\n\
					<td>'+tkt.updated_on+'</td>\n\
					<td><a class="link" href="'+site_url+'/admin/ticket/'+tkt.ticket_id+'">TK'+tkt.ticket_no+'</a></td>\n\
					<td>'+franchise_name+'</td>\n\
					<td>'+user_det+'</td>';
						
					//===============			
					var type_msg = '';	
					if(tkt.type==0)
						type_msg = 'Query';
					else if(tkt.type==1)
						type_msg = 'Order Issue';
					else if(tkt.type==2)
						type_msg = 'Bug';
					else if(tkt.type==3)
						type_msg = 'Suggestion';
					else if(tkt.type==4)
						type_msg = 'Common';
					else if(tkt.type==5)
						type_msg = 'PNH Returns';		
					else if(tkt.type==6)
						type_msg = 'Courier Followups';
					//--new services--
					else if(tkt.type==10)
						type_msg = 'Request';
					else if(tkt.type==11)
						type_msg = 'Complaint';
					
					var prioritys= new Array("Low","Medium","High","Urgent");
					//================
					var pririty_msg='';
					if(tkt.priority==0){
						pririty_msg = '<div  class="button-flat-primary inpadding">'+prioritys[tkt.priority]+'</div>';
					}
					else if(tkt.priority==1) {
						pririty_msg = '<div class="button-flat-royal inpadding">'+prioritys[tkt.priority]+'</div>';
					}
					else if(tkt.priority==2) {
						pririty_msg = '<div class="button-flat-highlight inpadding">'+prioritys[tkt.priority]+'</div>';
					}
					else if(tkt.priority==3) {
						pririty_msg = '<div class="button-flat-caution inpadding">'+prioritys[tkt.priority]+'</div>';
					}
					var related_to_name='--';
					if(tkt.related_to_name!=null)
						related_to_name=tkt.related_to_name;
					
					/*var related_to_msg = '';
					if(tkt.related_to) {
						related_to_msg = ''
					}*/
					
				
					var assignedto = tkt.assignedto;
					if(tkt.assignedto == null)
						assignedto = '--';
					
					var depts = '';
					if(tkt.dept_dets!=null) {
						depts = tkt.dept_dets;
						//var dept_arr=new Array();
						//var dept_arr = (tkt.dept_dets).slice(',');
						//print(dept_arr);
						/*$.each(dept_arr,function(i,dept_a) {
							print(dept_a);
							var dept = dept_a.slice(':');
							depts += ','+dept[1];
						});*/
					}
					//<td>'+depts+'</td>\n\
					
					var from_app='ERP / Web';
					if(tkt.from_app==1)
						from_app='Mobile / API';
					
					
								
					htmldata +='<td>'+status_msg+'</td>\n\
								<td>'+type_msg+'</td>\n\
								<td>'+related_to_name+'</td>\n\
								<td>'+pririty_msg+'</td>\n\
								<td>'+assignedto+'</td>\n\
								<td>'+depts+'</td>\n\
								<td>'+from_app+'</td>\n\
								<td>'+email+'</td>\n\
								<td>'+transid+'</td>\n\
								<td>'+created_by+'</td>\n\
						</tr>';
			});
			
			$(".pg_msg").html(rdata.pg_msg);
			$(".ttl_tickets").html(rdata.ttl_tickets);
			//ttl_unassinged ttl_open ttl_inprogress ttl_closed
			$(".ttl_unassinged").html(rdata.ttl_unassinged);
			$(".ttl_open").html(rdata.ttl_open);
			$(".ttl_inprogress").html(rdata.ttl_inprogress);
			$(".ttl_closed").html(rdata.ttl_closed);
			$(".avg_resolve_time").html(rdata.avg_resolve_time);
			
			if(rdata.pagination != undefined) {
				$(".pagination_link").html(rdata.pagination);
			}
		}
		else
		{	
			htmldata +='<tr><td colspan="'+colcount+'"><b>'+rdata.message+'</b></td></tr>'; //no tickets to show
		}
		
		$(".show_tickets_tbl tbody").html(htmldata);
		
	},'json');
	return false;
}

function show_tkts(tkt_sts) {
	if(tkt_sts==0)
		$("#status").val(0);
	if(tkt_sts==1)
		$("#status").val(1);
	if(tkt_sts==2)
		$("#status").val(2);
	if(tkt_sts==3)
		$("#status").val(3);
	if(tkt_sts==4)
		$("#status").val(4);
	load_tickets();
}

function reset_filters() {
	$(".source").val(0);
	$("#ds_range").val("");
	$("#de_range").val("");
	
	$("#franchise_id").val(0);
	$("#status").val(0);
	$("#priority").val(0);
	$("#tickets_from").val(0);
	load_tickets();
}
