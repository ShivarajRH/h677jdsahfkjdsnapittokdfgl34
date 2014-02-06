/* 
 * @contact: shivaraj@storeking.in
 */
var twnlnk_franchise_html='';

function load_ship_del_calender()
{
		  var date = new Date();
		  var d = date.getDate();
		  var m = date.getMonth();
		  var y = date.getFullYear();

		  $('.shipment_log').fullCalendar({
			  	editable: false,
			  	droppable: false,
		   		draggable: false,
		   		
			   	header: {
		 		left: 'prev,next today',
		 		center: 'title',
		 		right: 'month,basicWeek,agendaDay'
		 		},
		 		
		   	
		   	selectable: true,
			selectHelper: true,
			
			events: function(start, end, callback){
					 var franchise_id= franchise_id;
					 // $('.ttl_amount_shipped').text(0);
					  //$('.ttl_amount_delivered').text(0);
				 	$('.shipment_log').fullCalendar('removeEvents')
				 		$.post(site_url+'/admin/jx_franchise_shipment_logonload',{'start': start.getTime(),'end': end.getTime(), 'fid':franchise_id},function(result){
				 				//$('.ttl_amount_shipped').html("Rs. "+result.ttl_amt_shipped);
				 				//$('.ttl_amount_delivered').html("Rs. "+result.ttl_amt_delivered);
                    		    callback(result.ship_del_list);
                    	},'json');
                    	
              },
		    eventRender: function(event, element) {
				var amount = event.amount;
					if(event.type == 'shipment')
					{element.find('.fc-event-title').html('Shipped Value <br /><b>Rs. '+amount+'</b>').parent().addClass('shipped_event');}
					else if(event.type == 'delivery')
					{element.find('.fc-event-title').html('Delivered Value <br /><b>Rs. '+amount+'</b>').parent().addClass('delivered_event');}
						
		    },
		     eventClick: function(calEvent, jsEvent, view) {
					var date=calEvent.start;
					var event_type=calEvent.type;
				 	var sel_date = (date.getDate())>9?(date.getDate()):'0'+(date.getDate());
				 	var sel_mnth = (date.getMonth()+1)>9?(date.getMonth()+1):'0'+(date.getMonth()+1);
			  	 	var sel_year = date.getFullYear();
			     	var ship_date = sel_year+'-'+(sel_mnth)+'-'+sel_date;
			     	var delivery_date = sel_year+'-'+(sel_mnth)+'-'+sel_date;
				 	var franchise_id= franchise_id;
				 
						if(event_type == "shipment")
						{
							var title = 'Shipment Log on '+sel_date+'/'+(sel_mnth)+'/'+sel_year;
								$( "#ship_log_dlg" ).data({'sel_date':sel_date,'sel_mnth':sel_mnth, 'sel_year':sel_year, 'fid':franchise_id, 'ship_date':ship_date }).dialog('open','option','title',title);
								$( "#ship_log_dlg" ).dialog('option','title',title);
						}else if(event_type == "delivery")
						{
							var title = 'Delivery Log on '+sel_date+'/'+(sel_mnth)+'/'+sel_year;
								$( "#delivery_log_dlg" ).data({'sel_date':sel_date,'sel_mnth':sel_mnth, 'sel_year':sel_year, 'fid':franchise_id, 'delivery_date':delivery_date }).dialog('open','option','title',title);
								$( "#delivery_log_dlg" ).dialog('option','title',title);
						 }
		  }
	});
	
}

	$(function(){

		if(location.hash != '#ship_log')
		{
			$('.ship_log').click(function(e){
				if(!$('.shipment_log').hasClass( 'fc' ))
					load_ship_del_calender();
			});
		}else
	
	
	
		{
			load_ship_del_calender();
		}
	});
	
	   
	$("#ship_log_dlg" ).dialog({
			modal:true,
			autoOpen:false,
			width:'1000',
			height:'450',
			autoResize:true,
			open:function(){
			dlg = $(this);
	
			var ship_date=$(this).data('ship_date');
			var sel_date=$(this).data('sel_date');
			var sel_mnth=$(this).data('sel_mnth');
			var sel_year=$(this).data('sel_year');
			// ajax request fetch task details
		   $.post(site_url+'/admin/jx_franchise_shipment_log_bydate',{sel_date:$(this).data('sel_date'), sel_mnth:$(this).data('sel_mnth'), sel_year:$(this).data('sel_year'), fid:$(this).data('fid'), ship_date:$(this).data('ship_date')},function(result){
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

	$("#delivery_log_dlg" ).dialog({
			modal:true,
			autoOpen:false,
			width:'1000',
			height:'450',
			autoResize:true,
			open:function(){
			dlg = $(this);
	
			var delivery_date=$(this).data('delivery_date');
			var sel_date=$(this).data('sel_date');
			var sel_mnth=$(this).data('sel_mnth');
			var sel_year=$(this).data('sel_year');
			// ajax request fetch task details
		   $.post(site_url+'/admin/jx_franchise_delivery_log_bydate',{sel_date:$(this).data('sel_date'), sel_mnth:$(this).data('sel_mnth'), sel_year:$(this).data('sel_year'), fid:$(this).data('fid'), delivery_date:$(this).data('delivery_date')},function(result){
		   if(result.status == 'failure')
			{
				 $('#delivery_log_dlg_wrap').html('No deliveries on '+delivery_date);
				 return false;
		    }
		    else
			{
		    	var delivery_det='';
		    	var k=1;
		    	 
		    	 delivery_det +='<table class="datagrid" width="100%"  ><tr><th width="5%">Sl.No</th><th>TransID</th><th>Invoice</th>';
		    	 delivery_det +='<th>Item</th><th>Quantity</th><th>Amount</th><th></th></tr>';
		    	 $.each(result.delivery_det,function(i,s1){
		    	 	s = s1[0];
		    	 	
		    	 		delivery_det +='<tr>';
		    	 		delivery_det +='	<td rowspan="'+s1.invoices.length+'">'+(k++)+'</td>';
		    	 		delivery_det +='	<td rowspan="'+s1.invoices.length+'"><a href="'+site_url+'/admin/trans/'+s1.transid+'" target="_blank">'+s1.transid+'</a><br /><span style="font-size:10px;font-weight:bold">Ordered On : '+s1.ord_on+'</span></td>';
		    	 		j=0;
		    	 		$.each(s1.invoices,function(a,b){
		    	 			if(j!=0)
		    	 				delivery_det +='<tr>';			
		    	 			delivery_det +='<td><a href="'+site_url+'/admin/invoice/'+b.invoice_no+'" target="_blank">'+b.invoice_no+'</a></td>';
			    	 		delivery_det +='	<td><a href="'+site_url+'/admin/pnh_deal/'+b.itemid+'" target="_blank">'+b.name+'</a></td>';
			    	 		delivery_det +='	<td>'+b.qty+'</td>';
			    	 		delivery_det +='	<td>'+b.amount+'</td>';
			    	 		delivery_det +='	<td><a class="link_btn" onclick="get_invoicetransit_log(this,'+b.invoice_no+')" href="javascript:void(0)">View Transit Log</a></td>';
			    	 		if(j!=0)
		    	 				delivery_det +='</tr>';
		    	 				j++;	
		    	 		});
		    	 		delivery_det +='</tr>';
		    	 	});
		    	  delivery_det +='<tfoot class="nofooter"><tr><td>Total </td><td></td><td></td><td></td><td style="text-align:left">'+result.ttl_qty+'</td><td style="text-align:left">Rs.'+result.ttl_amt+'</td><td></td><td></td><td></td></tr></tfoot>';
		    	 $('#delivery_log_dlg_wrap').html(delivery_det);	
			
			}
		  },'json');
		}
	});
	
	function get_invoicetransit_log(ele,invno)
	{
		$('#inv_transitlogdet_dlg').data({'invno':invno}).dialog('open');
	}
	
	var refcont = null;
	$('#inv_transitlogdet_dlg').dialog({width:'900',height:'auto',autoOpen:false,modal:true,
				open:function(){

				
				//,'width':refcont.width()
				//$('div[aria-describedby="inv_transitlogdet_dlg"]').css({'top':(refcont.offset().top+15+refcont.height())+'px','left':refcont.offset().left});
				
				$('#inv_transitlogdet_tbl').html('loading...');
				$.post(site_url+'/admin/jx_invoicetransit_det','invno='+$(this).data('invno'),function(resp){
					if(resp.status == 'error')
					{
						alert(resp.error);
					}else
					{
						var inv_transitlog_html = '<table class="datagrid" width="100%"><thead><th width="30%">Msg</th><th width="10%">Status</th><th width="10%">Handle By</th><th width="10%">Logged On</th><th width="15%">SMS</th></thead><tbody>';
						$.each(resp.transit_log,function(i,log){
							inv_transitlog_html += '<tr><td>'+log[5]+'</td><td>'+log[1]+'</td><td>'+log[2]+'('+log[4]+')</td><td>'+log[3]+'</td><td>'+log[6]+'</td></tr>';
						});
						inv_transitlog_html += '</tbody></table>';
						$('#inv_transitlogdet_tbl').html(inv_transitlog_html);

						$('#inv_transitlogdet_dlg h3').html('Invoice no :<span style="color:blue;font-size:12px">'+resp.invoice_no+'</span>  Franchise name: <span style="color:orange;font-size:12px">'+resp.Franchise_name +'</span> Town : <span style="color:gray;font-size:12px">'+resp.town_name+'</span>'+' ManifestoNo :'+resp.manifesto_id);


						
						
					}
				},'json');
			}
	});	
	
$('.tab_view').tabs();
$('#loading_container_div').remove();
$('.container_div').css('visibility','visible');



$('.select_brand').chosen();
$('.select_cat').chosen();
$('.sus_type').chosen();

$('.sus_type').change(function(){

	if($(this).val()==2)
		$('.credit_edit').show();
	else
		$('.credit_edit').hide();
});
/*function load_allbrands()
{
	var brands_html='<option value=""></option>';
	$.getJSON(site_url+'/admin/jx_getallbrands','',function(resp){
		if(resp.status == 'error')
		{
			alert(resp.message);
		}
		else
		{
			brands_html+='<option value="0">All</option>';
			$.each(resp.brand_list,function(i,b){
			brands_html+='<option value="'+b.id+'">'+b.name+'</option>';
			});
		}
		
		$('select[name="brand"]').html(brands_html);
		$('select[name="brand"]').trigger("liszt:updated");
		
	});
}

function load_allcatgory()
{
	var cat_html='<option value=""></option>';
	$.getJSON(site_url+'/admin/jx_getallcategory','',function(resp){
		if(resp.status == 'error')
		{
			alert(resp.message);
		}
		else
		{
			cat_html+='<option value="0">All</option>';
			$.each(resp.cat_list,function(i,c){
				cat_html+='<option value="'+c.id+'">'+c.name+'</option>';
			});
		}
		
		$('select[name="cat"]').html(cat_html);
		$('select[name="cat"]').trigger("liszt:updated");
		
	});
}*/

	var fil_ordersby = 'all';
	
	function load_franchise_orders(stat)
	{
		if(stat != 1)
			fil_ordersby = stat;
				
		
		$('.tab_list .selected').removeClass('selected');
		$('.tab_list .'+fil_ordersby).addClass('selected');
			
				
		$('#franchise_ord_list_frm input[name="type"]').val(fil_ordersby);
		
		$('.franchise_ord_list_content').html("<div style='margin-top:15px'>Loading...</div>");
		$.post($('#franchise_ord_list_frm').attr('action'),$('#franchise_ord_list_frm').serialize()+'&stat='+stat,function(resp){
			$('.franchise_ord_list_content').html(resp);
		});
		return false;
	}
$(function(){
	$('#menu_det_tab').hide();
	
	var fran_reg_date = f_created_on;
	prepare_daterange('ord_fil_from','ord_fil_to');
	$("#d_start,#d_end").datepicker({minDate:0});
	$("#frm,#to").datepicker({dateFormat:'dd-mm-yy'});
	prepare_daterange('msch_start','msch_end');
	$('#msch_applyfrm').datepicker();
	$("#sec_date").datepicker();
	$( "#d_ac_from").datepicker({
	      changeMonth: true,
	      dateFormat:'yy-mm-dd',
	      numberOfMonths: 1,
	      maxDate:0,
	      minDate: new Date(fran_reg_date),
	    	onClose: function( selectedDate ) {
	        $( "#d_ac_to" ).datepicker( "option", "minDate", selectedDate );
	      }
	    });
	    $( "#d_ac_to" ).datepicker({
	      changeMonth: true,
	      dateFormat:'yy-mm-dd',
	      numberOfMonths: 1,
	      maxDate:0,
	      onClose: function( selectedDate ) {
	        $( "#d_ac_from" ).datepicker( "option", "maxDate", selectedDate );
	      }
	    });
	
	
	 
	$(".inst_type").change(function(){
		$(".inst").hide();
		if($(this).val()=="1")
		{
			$(".inst").show().val("");
			$(".inst_no .label").html("Cheque No");
			$(".inst_date .label").html("Cheque Date");
		}
		else if($(this).val()=="2")
		{
			$(".inst").show().val("");
			$(".inst_no .label").html("DD No");
			$(".inst_date .label").html("DD Date");
		}
		else if($(this).val()=="3")
		{
			$(".inst").show().val("");
			$(".inst_date .label").html("Transfer Date");
			$(".inst_no").hide();
		}
	}).val("0").change();

					
	$("#sch_form").data('psubumit',false).submit(function(){
		if($("#d_start").val().length==0 || $("#d_end").val().length==0)
		{
			$("#sch_form").data('psubumit',false);
			alert("Enter start date and end date");
			return false;
		}
		
		var sch_disc = $('input[name="discount"]',this).val();
			sch_disc = $.trim(sch_disc)*1;
			if(isNaN(sch_disc))
			{
				$("#sch_form").data('psubumit',false);
				alert("invalid discount entered");
				return false;
			}else
			{
				$.post(site_url+'/admin/jx_check_schemexist/'+franchise_id,'',function(resp){
					if(resp.status == 'error')
					{
						$("#sch_form").data('psubumit',false);
						alert(resp.message);
												
					}else
					{
						$("#sch_form").data('psubumit',true);
						$("#sch_form").submit();
						$("#sch_hist").dialog('close');
					} 
				});
			}
		//alert(resp.message);
		//return $(this).data('psubumit');
	});
	

	$('.analytics').click(function(){
		setTimeout(function(){
			fran_menu_stat();
			payment_order_stat();
			brands_byfranch();	
		},400);
	});
	
	
	if(location.hash == '#analytics')
		$('.analytics').click();
	
	
	$(".credit_form").submit(function(){
		if(!is_integer($("input[name=limit]",$(this)).val()))
		{
			alert("Enter a number");
			return false;
		}
		reason=prompt("Please mention a resaon");
		if(reason.length==0)
			return false;
		$(".c_reason",$(this)).val(reason);
		return true;
	});
	$("#bank_form").submit(function(){
		f=true;
		$("input",$(this)).each(function(){
			if($(this).val().length==0)
			{
				alert($("td:first",$(this).parents("tr").get(0)).text());
				f=false;
				return false;
			}
			return f;
		});
	});

	$("#acc_change_form").submit(function(){
		 $('input[type="text"]').each(function(){
		 	$(this).val($.trim($(this).val()))
		 });
		 
		var error_msgs = new Array();
		
			if(!$('input[name="amount"]',this).val().length)
				error_msgs.push("Enter amount");
				
			if(!$('input[name="desc"]',this).val().length)
				error_msgs.push("Enter description");
			
			if(error_msgs.length)
			{
				alert("Invalid Inputs Entered \n\n"+error_msgs.join("\n"));
				return false;
			}
			
			if(!confirm("Are you sure you want to make this correction ?"))
				return false;
		 
	});
	

	$("#allot_mid_form").submit(function(){
		if($("input[name=start]",$(this)).val().length!=8 || $("input[name=end]",$(this)).val().length!=8 || $("input[name=start]",$(this)).val().charAt(0)!="2" || $("input[name=end]",$(this)).val().charAt(0)!="2")
		{
			alert("Please enter valid MID");
			return false;
		}
		return true;
	});

	$("#d_ac_form").submit(function(){
		if($("#d_ac_from").val().length==0 || $("#d_ac_to").val().length==0)
		{
			alert("Please enter valid from and to date");
			return false;
		}
		return true;
	});

	$("#fran_ver_change").change(function(){
		if($(this).val()=="0")
			return;
		if(confirm("Are you sure to change the version?"))
			location=frn_version_url+"/"+$(this).val();
	});
	load_franchise_orders('all');
});
function give_sch_discnt_frm()
{
	$('#sch_hist').dialog('open');
	//load_allbrands();
	//load_allcatgory();
}
$( "#sch_hist" ).dialog({
	modal:true,
	autoOpen:false,
	width:500,
	height:500,
	autoResize:true,
	open:function(){
	dlg = $(this);
	
	},
	buttons:{
		'Cancel' :function(){
		 $(this).dialog('close');
		},
		'Submit':function(){
			var sch_form=$("#sch_form",this);
			if(sch_form.parsley('validate'))
			{
				//sch_form.submit();

				if($("#d_start").val().length==0 || $("#d_end").val().length==0)
				{
					alert("Enter start date and end date");
					return false;
				}

				var sch_disc = $('input[name="discount"]',this).val();
					sch_disc = $.trim(sch_disc)*1;
					if(isNaN(sch_disc))
					{
						alert("invalid discount entered");
						return false;
					}
					if(sch_disc > 20)
					{
						alert("Maximum 10% is allowed for scheme discount");
						return false;
					}	

				$.post(site_url+'/admin/jx_check_schemexist/'+franchise_id,$("#sch_form").serialize(),function(resp){
					if(resp.status == 'error')
					{
						alert(resp.message);
					}else
					{
						$("#sch_form").submit();
						$("#sch_hist").dialog('close');
					} 
				},'json');
			}
			else
			{
				alert("All fileds are required");
			}
		}
	}
});

function load_bankdetails()
{
	$('#bank').dialog('open');
}

$( "#bank" ).dialog({
	modal:true,
	autoOpen:false,
	width:'600',
	height:'auto',
	open:function(){
	dlg = $(this);
	},
	buttons:{
		'Close' :function(){
		 $(this).dialog('close');
		}
	}
});

//------menber list handle------
function members_details(fid)
{
	$('#members').data({'fid':fid}).dialog('open');
}

$( "#members" ).dialog({
	modal:true,
	autoOpen:false,
	width:'700',
	height:'auto',
	open:function(){
		dlg = $(this);
		var fid=$(this).data('fid');
		var html_cnt='';
		$( "#members table" ).remove();	
		load_members(fid,0);
	},
	buttons:{
		'Close' :function(){
		 	$(this).dialog('close');
		}
	}
});

$("#members .m_pg a").live("click",function(e){
	e.preventDefault();
	var link_parts=$(this).attr('href').split('/');
		load_members($( "#members" ).data('fid'),link_parts[link_parts.length-1]*1);
});

function load_members(fid,pg)
{
	$( "#members table" ).remove();
	var html_cnt='';
	$.post(site_url+"/admin/jx_get_members_by_franchise/1/"+fid+'/'+pg,{},function(res){
		if(res.status=='error')
		{
			alert(res.msg);
		}else{
			html_cnt+="<table class='datagrid' width='100%'><thead><tr><th>Member ID</th><th>Name</th><th>City</th><th>Created On</th></tr></thead><tody>";
			$.each(res.members,function(a,b){
				html_cnt+="<tr>";
				html_cnt+="	<td><a href='"+site_url+"admin/pnh_viewmember/"+b.user_id+"' class='link'>"+b.pnh_member_id+"</a></td>";
				html_cnt+="	<td>"+b.first_name+" "+b.last_name+"</td>";
				html_cnt+="	<td>"+b.city+"</td>";
				
				html_cnt+="	<td>"+((b.created_on==0)?'registration form not updated yet':(get_unixtimetodatetime(b.created_on)))+"</td>";	
				html_cnt+="</tr>";
				
			});
			html_cnt+="<tr>";
			html_cnt+="	<td colspan='4' align='right' class='m_pg pagination'>"+res.pagination+"</td>";		
			html_cnt+="</tr>";
			html_cnt+="</tbody></table>";
				
		}
		
		$("#members").append(html_cnt);
			
	},'json');
	
}

//------menber list handle end------

//-----------receipts handle---------
function load_receipts(ele,type,pg,fid,limit)
{
	$($(ele).attr('href')+' div.tab_content').html('<div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');

	$.post(site_url+'/admin/jx_pnh_franchise_reports/'+fid+'/'+type+'/'+limit+'/'+pg*1,'',function(resp){
		$($(ele).attr('href')+' div.tab_content').html(resp.page);
		$($(ele).attr('href')+' div.tab_content .datagridsort').tablesorter();
		
	},'json');
}

$(".receipt_pg a").live('click',function(e){
	e.preventDefault();
	var link_det=$(this).attr('href').split('/');
	var fid=link_det[2];
	var type=link_det[3];
	var pg=link_det[4];
	
	$.post(site_url+'/admin/jx_pnh_franchise_reports/'+fid+'/'+type+'/'+pg*1,'',function(resp){
		$("#"+type+' div.tab_content').html(resp.page);
		$("#"+type+' div.tab_content .datagridsort').tablesorter();
		
	},'json');
});

$(".account_statement").click(function(){$(".pending_receipt").trigger('click');});
//-----------receipts handle end---------
function load_scheme_disc_history()
{
	$('#schme_disc_history').dialog('open');
}

$( "#schme_disc_history" ).dialog({
	modal:true,
	autoOpen:false,
	width:'900',
	height:'auto',
	open:function(){
		dlg = $(this);
	},
	buttons:{
		'Close' :function(){
		 $(this).dialog('close');
		}
	}
});


function init_frmap() {
  	$('.fran_menu').chosen();
}
$(function(){
	$('.leftcont').hide();	
});
$('.schmenu').chosen();

var sel_menuid=0;

$(function(){

	$('.select_cat').change(function(){
	//alert($(this).val());
	//sel_menuid=$('.schmenu').val();
	sel_catid=$(this).val();
	if(sel_catid!='0')
	{
		$(".select_brand").html('').trigger("liszt:updated");
		$.getJSON(site_url+'/admin/jx_load_allbrandsbycat/'+sel_catid,'',function(resp){
		var brands_html='';
		if(resp.status=='error')
		{
			alert(resp.message);
		}
		else
		{
			brands_html+='<option value=""></option>';
			brands_html+='<option value="0">All</option>';
			$.each(resp.brand_list,function(i,b){
			brands_html+='<option value="'+b.brandid+'">'+b.name+'</option>';
			});
		}
		 $('.select_brand').html(brands_html).trigger("liszt:updated");
		 $('.select_brand').trigger('change');
		});
	}
/*	else
	{
		load_allbrands();
	}*/
});

$('.schmenu').change(function()
{
	var sel_menuid=$(this).val();
	//var sel_brandid=$(this).val();
	if(sel_menuid!='0')
	{
		$(".select_cat").html('').trigger("liszt:updated");
		$.getJSON(site_url+'/admin/jx_load_allcatsbymenu/'+sel_menuid,'',function(resp){
			var cats_html='';
				if(resp.status=='error')
				{
					alert(resp.message);
				}
				else
				{
					cats_html+='<option value=""></option>';
					cats_html+='<option value="0">All</option>';
					$.each(resp.cat_list,function(i,b){
					cats_html+='<option value="'+b.catid+'">'+b.name+'</option>';
					});
				}
		 	$('.select_cat').html(cats_html).trigger("liszt:updated");
		 	$('.select_cat').trigger('change');
		});
	}

});

});


$( ".fran_tabs a" ).click(function()
{
	window.location.hash = $(this).attr('href');   
	window.scrollTo(0,0); 
});

/*$(".transit_link").click(function(e){
	if(!confirm("Are you sure want change to 'IN Hand' status?"))
	{
		e.preventDefault();
		return false;
	}
	return true;
});*/

$('#r_type').change(function(){
	r=$(this).val();
	if(r=='0')
	{
		$(".inst_type option[value="+1+"]").hide();
	}
	else
	{
		$(".inst_type option[value="+1+"]").show();
	}
});

function change_status(rid)
{
	$('#remarks_changestatus').data('receipt_id',rid).dialog('open');
}

$('#remarks_changestatus').dialog({

	model:true,
	autoOpen:false,
	width:'500',
	height:'330',
	open:function(){
		dlg = $(this);
		$('#transit_rmks input[name="rid"]',this).val(dlg.data('receipt_id'));
		$("#r_receiptid b",this).html(dlg.data('receipt_id'));
		$("#transit_rmks",this).attr('action',site_url+'/admin/pnh_change_receipt_trans_type/'+dlg.data('receipt_id')); 
	},
	buttons:{
		'Submit':function(){
			var transit_rmksfrm = $("#transit_rmks",this);
			 	if(transit_rmksfrm.parsley('validate'))
				{
					$('#transit_rmks').submit();
					$(this).dialog('close');
				}
		       else
		       {
		       		alert('Remarks Need to be addedd!!!');
		       }
		},
		'Cancel':function()
		{
			$(this).dialog('close');
		}
	}
	
});


function give_supersch()
{
	$("#pnh_superschme").dialog('open');
}

$('#fran_misc_logs').tabs();
$('#activity_menu_tabs').tabs();

$("#pnh_superschme").dialog({
	modal:true,
	autoOpen:false,
	width:'500',
	height:'500',
	open:function(){
		
	},
	buttons:{
		'Cancel':function(){
			$(this).dialog('close');
		},
		'Submit':function(){
			var sch_form=$("#super_schform",this);
			if(sch_form.parsley('validate'))
			{
				$.post(site_url+'/admin/jx_check_schemexist/'+franchise_id,$("#super_schform").serialize(),function(resp){
					if(resp.status == 'error')
					{
						alert(resp.message);
						return false;
					}else
					{
						$("#super_schform").data('psubumit',true);
						$("#super_schform").submit();
						$("#pnh_superschme").dialog('close');
					} 
				},'json');
			}
			else
			{
				alert("All fileds are required");
			}
		}
	}
});

function give_membrsch()
{
	$('#pnh_membersch').dialog('open');
}

$("#pnh_membersch").dialog({
	modal:true,
	autoOpen:false,
	width:'500',
	height:'500',
	open:function(){

	},
	buttons:{
			'Cancel':function(){
				$(this).dialog('close');
			},
			'Submit':function(){
				var mbr_schfrm=$('#membr_schform');
				if(mbr_schfrm.parsley('validate'))
				{
					$.post(site_url+'/admin/jx_check_mbrschmenu/'+franchise_id,$("#membr_schform").serialize(),function(resp){

						if(resp.status == 'error')
						{
							alert(resp.message);
							return false;
						}else
						{
							$('#membr_schform').data('psubumit',true);
							$('#membr_schform').submit();
							$("#pnh_membersch").dialog('close');
						}

					},'json');
				}
				else
				{
					alert("All fileds are required");
				}
		}
	}
});

		$('input[name="return_on_date"],input[name="return_on_date_end"]').datepicker({});
		
		$('input[name="return_on_date"],input[name="return_on_date_end"]').change(function(){
			$('input[name="return_kwd_srch"]').val('');
			load_return_prods(0);
		});

		$('input[name="return_kwd_srch"]').change(function(){
			$('input[name="return_on_date"]').val('');
			$('input[name="return_on_date_end"]').val('');
		});
		
		//$('select[name="order_by_mnth"]').change(function(){
			//fran_menu_stat();
		//});

		$("#grid_list_frm_to").bind("submit",function(e){
			$('#payment_stat .payment_stat_view').unbind('jqplotDataClick');
			e.preventDefault();
			fran_menu_stat();
			payment_order_stat();
			brands_byfranch();
			return false;
		});

		function load_all_return_prods(pg)
		{
			$('input[name="return_kwd_srch"]').val('');
			$('input[name="return_on_date"]').val('');
			$('input[name="return_on_date_end"]').val('');
			load_return_prods(pg);
		}

		function load_return_prods(pg)
		{
			$('#return_products .module_cont_block_grid .datagrid tbody').html('<tr><td colspan="8"><div align="center"><img src="'+base_url+'/images/loading_bar.gif'+'"> </div></td></tr>');
			
			var ret_params = {};
			ret_params.fid = franchise_id;
			ret_params.return_on = $('input[name="return_on_date"]').val();
			ret_params.return_on_end = $('input[name="return_on_date_end"]').val();
			ret_params.return_srch_kwd = $('input[name="return_kwd_srch"]').val();
			
			if(!(ret_params.return_on && ret_params.return_on_end))
			{
				ret_params.return_on = '';
				ret_params.return_on_end = '';
			}
			
			$('#return_products .module_cont_block_grid_total .total b').text("");
			
			$.post(site_url+'/admin/jx_getreturnprodsbyfid/'+pg,ret_params,function(resp){
				if(resp.status == 'error')
				{
					alert(resp.error)
				}else
				{
					$('#return_products .module_cont_block_grid_total .total b').text(resp.total);
					if(resp.fran_rplist.length == 0)
					{
						$('#return_products .module_cont_block_grid .datagrid tbody').html('<tr><td colspan="12"><div align="center">No Data found</div></td></tr>');			
					}else
					{
						var ret_prodlist_html = '';
						$.each(resp.fran_rplist,function(a,b){
							ret_prodlist_html += '<tr>'
														+'<td>'+(pg+a+1)+'</td>'
														+'<td><a target="_blank" href="'+site_url+'/admin/view_pnh_invoice_return/'+b.return_id+'"><b>'+b.return_id+'</b></a></td>'
														+'<td>'+formatDateTime(new Date(b.created_on*1000))+'</td>'
														+'<td>'+b.return_by+'</td>'
														+'<td>'+b.invoice_no+'</td>'
														+'<td>'+b.order_id+'</td>'
														+'<td style="line-height:20px;"><a href="'+site_url+'/admin/product/'+b.product_id+'"><b>'+b.product_name+'</b></a>  '+(b.barcode?' <br> Barcode :'+b.barcode:'')+' '+(b.imei_no?' <br> IMEINO :'+b.imei_no:'')+' '+' </td>'
														+'<td>'+b.qty+'</td>'
														+'<td>'+resp.return_cond[b.condition_type]+'</td>'
														+'<td>'+resp.return_process_cond[b.status]+'</td>'
														+'<td>'+formatDateTime(new Date(b.remarks.created_on*1000))+'</td>'
														+'<td>'+b.remarks.remark_by+'</td>'
														+'<td>'+b.remarks.remarks+'</td>'
														
													+'</tr>';
							});

						$('#return_products .module_cont_block_grid .datagrid tbody').html(ret_prodlist_html);
						
						$('#return_products .module_cont_grid_block_pagi').html(resp.fran_rplist_pagi);

						$('#return_products .module_cont_grid_block_pagi a').unbind('click').click(function(e){
								e.preventDefault();
								var link_part = $(this).attr('href').split('/');
								var link_pg = link_part[link_part.length-1]*1;
								if(isNaN(link_pg))
									link_pg = 0;
								load_return_prods(link_pg);	
						});
					}
				}
			},'json');
		}

		load_return_prods(0);
		
		function load_credit_notes(pg)
		{
			$.post(site_url+'/admin/jx_getfrancreditnotes/'+pg,'fid='+franchise_id,function(resp){
				if(resp.status == 'error')
				{
					alert(resp.error);
				}else
				{
					$('#credit_notes .module_cont_block_grid_total .total b').text(resp.total);
					if(resp.fran_crnotelist.length == 0)
					{
						$('#credit_notes .module_cont_block_grid .datagrid tbody').html('<tr><td colspan="12"><div align="center">No Data found</div></td></tr>');			
					}else
					{
						var crnotelist_html = '';
							$.each(resp.fran_crnotelist,function(a,b){
								crnotelist_html += '<tr>'
														+'<td>'+(pg+a+1)+'</td>'
														+'<td>'+b.credit_note_id+'</td>'
														+'<td><a target="_blank" href="'+site_url+'/admin/invoice/'+b.invoice_no+'"><b>'+b.invoice_no+'</b></a></td>'
														+'<td>'+b.order_id+'</td>'
														+'<td>'+b.credit_note_amt+'</td>'
														+'<td>'+formatDateTime(new Date(b.createdon*1000))+'</td>'
													+'</tr>';
							});
						$('#credit_notes .module_cont_block_grid .datagrid tbody').html(crnotelist_html);
						
						$('#credit_notes .module_cont_grid_block_pagi').html(resp.fran_crnotelist_pagi);
						
						$('#credit_notes .module_cont_grid_block_pagi a').unbind('click').click(function(e){
								e.preventDefault();
								
							var link_part = $(this).attr('href').split('/');
							var link_pg = link_part[link_part.length-1]*1;
								if(isNaN(link_pg))
									link_pg = 0;
									
								load_credit_notes(link_pg);	
						});
						
					}
				}
			},'json');
		}
		
		load_credit_notes(0)

		//console.log(franchise_id).val();
		function  load_voucher_activity(ele,type,franchise_id,pg)
		{

			$($(ele).attr('href')+' div.tab_content').html('<div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
			$.post(site_url+'/admin/jx_getpnh_voucher_activitylog/'+type+'/'+franchise_id+'/'+pg*1,'',function(resp){
				$($(ele).attr('href')+' div.tab_content').html(resp.log_data+resp.pagi_links);
				$($(ele).attr('href')+' div.tab_content .datagridsort').tablesorter();
				
			},'json');
		}

		$("#voucher_tab").click(function(){
				$("#book_orders_tab").trigger("click");
		});

	/*	function load_allshipped_imei(ele,type,franchise_id,pg)
		/*{
			type = 1;
			pg = 0;
			var franchise_id = franchise_id;
			$('#shipped_imeimobslno div.tab_content').html('<div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
			$.post(site_url+'/admin/jx_load_all_shipped_mobimei/'+type+'/'+franchise_id+'/'+pg*1,'',function(resp){
				$('#shipped_imeimobslno div.tab_content').html(resp.log_data+resp.pagi_links);
				$('#shipped_imeimobslno div.tab_content .datagridsort').tablesorter();
			},'json');
		}*/

		$('input[name="active_ondate"],input[name="active_ondate_end"]').datepicker();

		$('input[name="active_ondate"],input[name="active_ondate_end"],select[name="date_type"]').change(function(){
			if($('input[name="active_ondate"]').val() != '' && $('input[name="active_ondate_end"]').val() != '')
			{
				load_shipped_imei(0);
				$('input[name="imei_srch_kwd"]').val('');
			}
		});

		$('select[name="imei_status"]').change(function(){
				load_shipped_imei(0);
				$('input[name="imei_srch_kwd"]').val('');
		});

		function load_allshipped_imei(pg)
		{
			$('select[name="imei_status"]').val('');
			$('input[name="imei_srch_kwd"]').val('');
			$('input[name="active_ondate"]').val('');
			$('input[name="active_ondate_end"]').val('');
			load_shipped_imei(pg);
		}
	
	function load_shipped_imei(pg)
	{
		$('#shipped_imeimobslno .module_cont_block_grid .datagrid tbody').html('<tr><td colspan="8"><div align="center"><img src="'+base_url+'/images/loading_bar.gif'+'"> </div></td></tr>');
		var imei_params = {};
		
			imei_params.fid = franchise_id;
			imei_params.date_type = $('select[name="date_type"]').val();
			imei_params.imei_status = $('select[name="imei_status"]').val();
			imei_params.active_ondate = $('input[name="active_ondate"]').val();
			imei_params.active_ondate_end = $('input[name="active_ondate_end"]').val();
			imei_params.imei_srch_kwd = $('input[name="imei_srch_kwd"]').val();
			
			if(!(imei_params.active_ondate && imei_params.active_ondate_end))
			{
				imei_params.active_ondate = '';
				imei_params.active_ondate_end = '';	
			}
			if(!imei_params.imei_status)
				imei_params.imei_status = '';
		
			$.post(site_url+'/admin/jx_load_all_shipped_mobimei/'+pg,imei_params,function(resp){
				
				if(resp.status == 'error')
				{
					alert(resp.error);
				}else
				{
					$('#shipped_imeimobslno .module_cont_block_grid_total .total b').text(resp.total_rows);
					if(resp.ship_imei_det.length == 0)
					{
						$('#shipped_imeimobslno .module_cont_block_grid .datagrid tbody').html('<tr><td colspan="12"><div align="center">No Data found</div></td></tr>');			
					}else
					{
						var shipped_imeilist_html = '';
							$.each(resp.ship_imei_det,function(a,b){
								if(b.is_imei_activated==0)
									b.is_imei_activated='No';
								else
									b.is_imei_activated='Yes';
								if(b.imei_activated_on === null)
									b.imei_activated_on='--na--';
								shipped_imeilist_html += '<tr>'
														+'<td>'+(pg+a+1)+'</td>'
														+'<td>'+b.product_name+'</td>'
														+'<td><a target="_blank" href="'+site_url+'/admin/invoice/'+b.invoice_no+'"><b>'+b.invoice_no+'</b></a></td>'
														+'<td>'+b.imei_no+'</td>'
														+'<td>'+b.paid+'</td>'
														+'<td>'+b.orderd_on+'</td>'
														+'<td>'+b.is_imei_activated+'</td>'
														+'<td>'+b.credit_value+''+resp.imei_cre_type[b.scheme_type]+'</td>'
														
														
														+'<td>'+b.imei_activation_credit+'</td>'
														+'<td>'+b.imei_activated_on+'</td>'
													+'</tr>';
							});
						$('#shipped_imeimobslno .module_cont_block_grid .datagrid tbody').html(shipped_imeilist_html);
						
						$('#shipped_imeimobslno .module_cont_grid_block_pagi').html(resp.shipped_imeilist_pagi);
						
						$('#shipped_imeimobslno .module_cont_grid_block_pagi a').unbind('click').click(function(e){
								e.preventDefault();
								
							var link_part = $(this).attr('href').split('/');
							var link_pg = link_part[link_part.length-1]*1;
								if(isNaN(link_pg))
									link_pg = 0;
									
								load_shipped_imei(link_pg);	
						});
						
					}
				}
			},'json');
				
	}

	load_shipped_imei(0);

	$('.log_pagination a').live('click',function(e){
		e.preventDefault();
		$.post($(this).attr('href'),'',function(resp){
			$('#'+resp.type+' div.tab_content').html(resp.log_data+resp.pagi_links);
			$('#'+resp.type+' div.tab_content .datagridsort').tablesorter();
		},'json');
	});

	function reson_forsuspenfran(fid)
	{
		$("#fran_suspend").data('franchise_id',fid).dialog('open');
	}
		
	$("#fran_suspend").dialog({
		modal:true,
		autoOpen:false,
		width:'519',
		height:'300',
		open:function(){
			var dlg=$(this);
			$('.credit_edit').hide();
			$('#suspend_reasonfrm input[name="franchise_id"]',this).val(dlg.data('franchise_id'));
			$('#suspend_reasonfrm select[name="sus_type"]',this).trigger('change');
			//$("#r_receiptid b",this).html(dlg.data('receipt_id'));
			$("#suspend_reasonfrm",this).attr('action',site_url+'/admin/pnh_suspend_fran/'+dlg.data('franchise_id'));
		},
		buttons:{
			'Submit':function(){
				var dlg= $(this);
				var frm_fransuspend = $("#suspend_reasonfrm",this);
				if(frm_fransuspend.parsley('validate')){

					frm_fransuspend.submit();
					$("#fran_suspend").dialog('close');
				}
				else
				{
					alert('All Fields are required!!!');
				}
		},
		'Cancel':function(){
			$(this).dialog('close');
		}
	}
});

	function reson_forunsuspension(fid)
	{
		$("#unsuspend_fran").data('unsuspend_fid',fid).dialog('open');
	}

	$("#unsuspend_fran").dialog({
		modal:true,
		autoOpen:false,
		width:'500',
		height:'300',
		open:function(){
			var dlg=$(this);
			$('#unsuspend_reasonfrm input[name="unsuspend_fid"]',this).val(dlg.data('unsuspend_fid'));
			$("#unsuspend_reasonfrm",this).attr('action',site_url+'/admin/pnh_unsuspend_fran/'+dlg.data('unsuspend_fid'));
		},
		buttons:{
		'Submit':function(){
			var unsuspendfran_form=$("#unsuspend_reasonfrm",this);
			if(unsuspendfran_form.parsley('validate')){
				unsuspend_reasonfrm.submit();
				$("#unsuspend_fran").dialog('close');
			}
			else
			{

			}
		},
		'Cancel':function(){
			$(this).dialog('close');
			}
		}
	});

	function payment_order_stat()
	{
		var start_date=$('#frm').val();
		var end_date=$('#to').val();
		var franid = franchise_id;
		$('.head_wrap').html("Orders & Payments summary for period of "+start_date+" "+end_date);
		$('#payment_stat .payment_stat_view').html('<div class="anmtd_loading_img"><span></span></div>'); 
		$.getJSON(site_url+'/admin/jx_order_payment_det/'+start_date+'/'+end_date+'/'+franid,'',function(resp)
		{
			if(resp.summary == 0 && resp.payment == 0 && shipped==0)
			{
				$('#payment_stat .payment_stat_view').html("<div class='fr_alert_wrap' style='padding:113px 0px'>No Sales statisticks found between "+start_date+" and "+end_date+"</div>" );	
			}
			else
			{
				// reformat data ;
				$('#ttl_order_amt').html("Total Ordered : "+resp.ttl_summary);
				$('#paymrent_order_amt').html("Total Paid : "+resp.ttl_payment);
				$('#shipped_order_amt').html("Total Shipped : "+resp.ttl_shipped);
				 var types = ['Order Placed','shipped', 'Cheque Date','Cash in Bank'];
				$('#payment_stat .payment_stat_view').empty();
				var summary=resp.summary;
				var payment=resp.payment;
				var shipped=resp.shipped;
				var realized=resp.realized;
				plot2 = $.jqplot('payment_stat .payment_stat_view', [summary,shipped,payment,realized], {
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
			legend: {
				show: true,
				location: 'ne',
				placement: 'inside',
				labels: types
			},
			axes:{
				xaxis:{
					renderer: $.jqplot.CategoryAxisRenderer,
					ticks:resp.ticks,
					label:'Date',
					labelOptions:{
						fontFamily:'Arial',
						fontSize: '14px'
					},
					labelRenderer: $.jqplot.CanvasAxisLabelRenderer
			},
			yaxis:{
					min : 0,
					label:'Sales & Payment in Rs',
					labelOptions:{
						fontFamily:'Arial',
						fontSize: '14px'
					},
					labelRenderer: $.jqplot.CanvasAxisLabelRenderer
			}
		}
	});

			$('#payment_stat .payment_stat_view').bind('jqplotDataClick', function(ev,seriesIndex,pointIndex,data) {
				if(seriesIndex == 0)
				{
					var date = summary[pointIndex][2];
					var amt = summary[pointIndex][1];
					ord_det(date,amt,franid);
				}
			});
		}	
    });
}

	function ord_det(date,amt,franid)
	{
		$.post(site_url+'/admin/jx_order_det_franchise_id/',{date:date,franid:franid},function(resp){
		if(resp.status == 'error')
		{
			alert(resp.error);
		}else
		{
			var list = '';
			list += '<h4>Order Details on '+date+'<span style="float:right">Total Value : '+amt+'</span></h4>';
			list +='<span class="popclose_button b-close"><span>X</span></span><div style="overflow:auto;float:left;height:265px;width:600px;">';
			list += '<table class="datagrid" width="100%"><thead><tr><th>Sl.No</th><th>Product Name</th><th>Brand Name</th><th>Quantity</th><th>Amount</th></tr></thead><tbody>';
				$.each(resp.ord_det,function(a,b){
					list += '<tr>'
								+'<td>'+(++a)+'</td>'
								+'<td>'+b.product_name+'</td>'
								+'<td>'+b.name+'</td>'
								+'<td>'+b.q+'</td>'
								+'<td>'+b.total_value+'</td>'
							+'</tr>';
				});
			list += '</tbody></table></div>';
			$('#fr_det_popup').html(list);

		}
		},'json');
		$('#fr_det_popup').bPopup({
		easing: 'easeOutBack', //uses jQuery easing plugin
		    speed: 450,
		    transition: 'slideDown'
		});
	}

	function fran_menu_stat()
	{
		var start_date=$('#frm').val();
		var end_date=$('#to').val();
		var franid = franchise_id;

		$.getJSON(site_url+'/admin/jx_order_getsales_bymenu/'+franid+'/'+start_date+'/'+end_date,'',function(resp){
			// reformat data ;
			$('#fr_order_stat .order_piestat_view').empty();
			var resp = resp.result;
			plot3 = jQuery.jqplot('fr_order_stat .order_piestat_view', [resp], 
			{
				seriesDefaults:{
					renderer: jQuery.jqplot.PieRenderer,
					pointLabels: { show: true },
					rendererOptions: {
						// Put data labels on the pie slices.
						// By default, labels show the percentage of the slice.
						showDataLabels: true
					}
				},
				highlighter: {
					show: true,
					useAxesFormatters: false, // must be false for piechart   
					tooltipLocation: 's',
					formatString:'Menu : %s'
				},
				grid: {borderWidth:0, shadow:false,background:'#ccc'},
				legend:{show:true}
			});
			$('#fr_order_stat .order_piestat_view').bind('jqplotDataClick', function(ev,seriesIndex,pointIndex,data) {
				$('.fr_menu_by_mn').show();
				$('#menu_det_tab').show();
				var menu_id = resp[pointIndex][2];
				var menu_name = resp[pointIndex][0];

				//top sold products list for selected menu
				prods_bymenu(menu_id,menu_name);
				// top sold brands for selected menu
				brands_bymenu(menu_id,menu_name);
			});
		});
	}
		
	function brands_byfranch()
	{
		var franid = franchise_id;
		var fran_name=franchise_name;
		var start_date=$('#frm').val();
		var end_date=$('#to').val();
		$('#top_brand_bymenu_stat .fr_brand_stat_view').html('<div class="bar"><span></span></div>'); 

		$.getJSON(site_url+'/admin/jx_brandsbyfranid/'+franid+'/'+start_date+'/'+end_date,'',function(resp){
		$('#top_brand_bymenu_stat .stat_head_wrap').html("Top Brands for "+fran_name);
		if(resp.summary == 0)
		{
			$('#top_brand_bymenu_stat .fr_brand_stat_view').html("<div class='fr_alert_wrap' style='padding:113px 10px'>No brands ordered from "+start_date+" to "+end_date+"</div>")	
		}
		else
		{
			// reformat data ;
			$('#top_brand_bymenu_stat .fr_brand_stat_view').empty();
			plot2 = $.jqplot('top_brand_bymenu_stat .fr_brand_stat_view', [resp.summary], {
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
			$('#top_brand_bymenu_stat .fr_brand_stat_view').bind('jqplotDataClick', function(ev,seriesIndex,pointIndex,data) {
			});
		}
		});
	}
	
	function brands_bymenu(id,name)
	{
		var franid = franchise_id;
		var start_date=$('#frm').val();
		var end_date=$('#to').val();
		$('#top_brand_bymenu_stat .fr_brand_stat_view').html('<div class="bar"><span></span></div>'); 
		$.getJSON(site_url+'/admin/jx_brandsbymenuid/'+id+'/'+franid+'/'+start_date+'/'+end_date,'',function(resp){
		$('#top_brand_bymenu_stat .stat_head_wrap').html("Brands of "+name+" menu ");
		if(resp.summary == 0)
		{
			$('#top_brand_bymenu_stat .fr_brand_stat_view').html("<div class='fr_alert_wrap' style='padding:113px 10px'>No brands for "+name+" menu</div>")
		}
		else
		{
			// reformat data ;
			$('#top_brand_bymenu_stat .fr_brand_stat_view').empty();
			plot2 = $.jqplot('top_brand_bymenu_stat .fr_brand_stat_view', [resp.summary], {
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
				$('#top_brand_bymenu_stat .fr_brand_stat_view').bind('jqplotDataClick', function(ev,seriesIndex,pointIndex,data) {

				});
			}
		});
	}
	
	function prods_bymenu(id,name)
	{
		var franid = franchise_id;
		var start_date=$('#frm').val();
		var end_date=$('#to').val();
		$('.fr_top_sold').html('<div class="bar"><span></span></div>'); 
		$.post(site_url+"/admin/jx_prod_det_bymenu",{menu_id:id,franid:franid,start_date:start_date,end_date:end_date},function(resp){
			$('#menu_det_tab .stat_head_wrap').html("Top sold Products of "+name+" menu ");
			var topsold_prod_list="";
			
			if(resp.status == 'error')
			{
				topsold_prod_list +='<span>'+resp.message+'</span>';
				$('.fr_top_sold').html(topsold_prod_list);
			}
			else
			{
				topsold_prod_list +='<table class="datagrid" width="100%"><thead><tr><th>Product Name</th><th>Date</th><th>Qty Sold</th><th>Total Value</th></tr></thead><tbody>';
				$.each(resp.top_prod_list,function(i,p){
					topsold_prod_list +='<tr><td><a target="blank" href="'+site_url+'/admin/product/'+p.product_id+'">'+p.product_name+'</a></td><td>'+p.d+'</td><td>'+p.qty_sold+'</td><td>'+p.total_value+'</td></tr>';	
				});
				topsold_prod_list +='</tbody></table>';
				$('.fr_top_sold').html(topsold_prod_list);
			}
		},'json');
	}

	function mark_prepaid_franchise(fid,flag)
	{
		$("#mark_prepaid_franchise").data('franchise_id',fid,'p_flg',flag).dialog('open');
	}

	$("#mark_prepaid_franchise").dialog({
		modal:true,
		autoOpen:false,
		width:'519',
		height:'300',
		open:function(){
			var dlg=$(this);
			if(dlg.data('p_flg'))
				$('#mark_prepaid_franchise').dialog('option', 'title','Unmark prepaid reason');
			else
				$('#mark_prepaid_franchise').dialog('option', 'title','Mark prepaid reason');
			
			$('#mark_prepaid_franchise_form input[name="prepaid_franchise_id"]',this).val(dlg.data('franchise_id'));
			$("#mark_prepaid_franchise_form",this).attr('action',site_url+'/admin/mark_prepaid_franchise/'+dlg.data('franchise_id'));
		},
		buttons:{
			'Submit':function(){
				var dlg= $(this);
				var prepaid_reson = $("#mark_prepaid_franchise_form",this);
				if(prepaid_reson.parsley('validate')){
					prepaid_reson.submit();
					$("#mark_prepaid_franchise").dialog('close');
				}
				else
				{
					alert('All Fields are required!!!');
				}
			},
			'Cancel':function(){
				$(this).dialog('close');
			}
		}
	});
	 
	$('#dlg_add_security_cheque_det').dialog({
		width:400,
		height:'auto',
		autoOpen:false,
		modal:true,
		buttons:{
			'Add':function(){
				var dlg = $('#dlg_add_security_cheque_det');

				var f_schq_bankname = $('input[name="f_schq_bankname"]',dlg).val();
				var f_schq_no = $('input[name="f_schq_no"]',dlg).val();
				var f_schq_date = $('input[name="f_schq_date"]',dlg).val();
				var f_schq_amt = $('input[name="f_schq_amt"]',dlg).val();
				var f_schq_colon = $('input[name="f_schq_colon"]',dlg).val();

				var error_str = new Array();
				
				if(!f_schq_bankname.length)
					error_str.push("Please enter bankname");
				if(!f_schq_no.length)
					error_str.push("Please cheque no");
				if(!f_schq_date.length)
					error_str.push("Please cheque date");
				if(!f_schq_amt.length)
					error_str.push("Please cheque amount(Rs)");
				if(!f_schq_colon.length)
					error_str.push("Please enter cheque collected date");

				if(error_str.length)
				{
					alert(error_str.join("\r\n"));
				}else
				{
					$.post(site_url+'/admin/jx_add_security_chqdet',$('form',dlg).serialize(),function(resp){
						if(resp.status == 'error')
						{
							alert(resp.error);
						}else
						{
							$('#security_cheques table tbody').append('<tr><td>'+$('#security_cheques table tbody tr').length+'</td><td>'+f_schq_no+'</td><td>'+f_schq_bankname+'</td><td>'+f_schq_date+'</td><td>'+f_schq_amt+'</td><td>'+f_schq_colon+'</td></tr>');
							dlg.dialog('close');
						}
					},'json');
				}
			}
		}
	});

	function load_add_security_cheque_dlg()
	{
		$('#dlg_add_security_cheque_det').dialog('open');
	}

	$('input[name="f_schq_date"],input[name="f_schq_colon"]').datepicker({dateFormat:'yy-mm-dd'});

	function tgl_addcalllog_msg(ele)
	{
		if($(ele).hasClass('show_addmsgfrm'))
		{
			$(ele).removeClass('show_addmsgfrm');
			$(ele).text('Add Message');
			$(ele).removeClass('close_btn');
			$(ele).removeClass('fl_right');
		}else
		{
			$(ele).addClass('show_addmsgfrm');
			$(ele).addClass('close_btn');
			$(ele).addClass('fl_right');
			$(ele).text('X');
		}
		
		$("form",$(ele).parents('td:first')).toggle();
	}

	function load_recent_calllog(url,fid)
	{
		if(url)
			req_url = url;
		else
			req_url = site_url+'/admin/jx_get_franchise_calllog/'+fid;
			
		$.post(req_url,'',function(resp){
				if(resp.status == 'error')
				{
					$('#recent_call_log tbody').html("<tr><td colspan='3' align='center'>No Data found</td></tr>");
					$('#recent_call_log #call_log_pagi').html("");
				}else
				{
					var html = '';
					$.each(resp.call_log_list,function(i,det){
							html += '<tr>';
							html += '	<td width="140">'+(det.created_on)+'</td>';
							html += '	<td width="150" style="text-transform:capitalize">'+det.name+'</td>';
							html += '	<td> '+(det.msg.length?'<p style="margin:0px;padding:5px;background:#fdfdfd">'+det.msg+'</p>':'')+' ';
							html += '		<div style="padding:0px;" ><a href="javascript:void(0)" class="" style="font-size: 85%;" onclick=tgl_addcalllog_msg(this)  >Add Message</a>';
							html += '		<form method="post" style="display: none;" action="'+site_url+'/admin/pnh_update_call_log/'+det.id+'" >';
							html += '		<textarea name="msg" style="width:99%;height:60px;clear:both">'+det.msg+'</textarea>';
							html += '		<input type="submit" value="Submit">';
							html += '	</form></div></td>';
							html += '</tr>';	
					});
					$('#recent_call_log tbody').html(html);
					$('#recent_call_log #call_log_pagi').html(resp.call_log_pagi);
					
					$('#call_log_pagi a').unbind('click').click(function(e){
						e.preventDefault();
						load_recent_calllog($(this).attr('href'));
					});
				}
		},'json');
	}
	load_recent_calllog('',franchise_id);
	
	function load_credit_notes(pg)
    {
            $.post(site_url+'/admin/jx_getfrancreditnotes/'+pg,'fid='+franchise_id,function(resp){
                    if(resp.status == 'error')
                    {
                            alert(resp.error)
                    }else
                    {
                            $('#credit_notes .module_cont_block_grid_total .total b').text(resp.total);
                            if(resp.fran_crnotelist.length == 0)
                            {
                                    $('#credit_notes .module_cont_block_grid .datagrid tbody').html('<tr><td colspan="12"><div align="center">No Data found</div></td></tr>');			
                            }else
                            {
                                    var crnotelist_html = '';
                                            $.each(resp.fran_crnotelist,function(a,b){
                                                    crnotelist_html += '<tr>'
                                                                                                    +'<td>'+(pg+a+1)+'</td>'
                                                                                                    +'<td>'+b.credit_note_id+'</td>'
                                                                                                    +'<td><a target="_blank" href="'+site_url+'/admin/invoice/'+b.invoice_no+'"><b>'+b.invoice_no+'</b></a></td>'
                                                                                                    +'<td>'+b.order_id+'</td>'
                                                                                                    +'<td>'+b.credit_note_amt+'</td>'
                                                                                                    +'<td>'+formatDateTime(new Date(b.createdon*1000))+'</td>'
                                                                                            +'</tr>';
                                            });
                                    $('#credit_notes .module_cont_block_grid .datagrid tbody').html(crnotelist_html);

                                    $('#credit_notes .module_cont_grid_block_pagi').html(resp.fran_crnotelist_pagi);

                                    $('#credit_notes .module_cont_grid_block_pagi a').unbind('click').click(function(e){
                                                    e.preventDefault();

                                            var link_part = $(this).attr('href').split('/');
                                            var link_pg = link_part[link_part.length-1]*1;
                                                    if(isNaN(link_pg))
                                                            link_pg = 0;

                                                    load_credit_notes(link_pg);	
                                    });

                            }
                    }
            },'json');
    }

	$("#top_form").submit(function(){
	 
		 $('input[type="text"]').each(function(){
		 	$(this).val($.trim($(this).val()))
		 });
	 
		var error_msgs = new Array();
		var inst_type = $('select[name="type"]',this).val();
		var bank = $('input[name="bank"]',this).val();
		var inst_no = $('input[name="no"]',this).val();
		var dateval = $('input[name="date"]',this).val();
	
		if(!is_numeric($(".amount",$(this)).val()))
			error_msgs.push("Enter a valid amount");
		
		var inst_type_str = 'Cash';	
		if(inst_type == 1)
			inst_type_str = 'Cheque';
		else if(inst_type == 2) 	
			inst_type_str = 'DD';
		else if(inst_type == 3) 	
			inst_type_str = 'Transfer';
				
		// validate cash entry 
		if(inst_type != 0)
		{
			if(!bank.length)
				error_msgs.push("Enter Bank Name");
				
			if(!inst_no.length && inst_type != 3)
				error_msgs.push("Enter "+inst_type_str+" no");
				
			if(!dateval.length)
				error_msgs.push("Enter "+inst_type_str+" Date ");
		}
		
		if($(".msg",$(this)).val().length==0)
			error_msgs.push("Please enter Message");
	
        var sts = validate_selected_invoice_val();
        if(sts !== true) {
            error_msgs.push(sts);
        }
        var reconciled_total= format_number( $("abbr",".reconciled_total").html() );
        $("#total_val_reconcile").val(reconciled_total);
                    
		if(error_msgs.length)
		{
			alert("Errors:\n"+error_msgs.join("\n"));
			return false;
		}
                
		if(!confirm("Are you sure you want add this receipt ?"))
			return false;
		
		if(inst_type == 0)
		{
			$('input[name="bank"]',this).val("");
			$('input[name="date"]',this).val("");
		}
		
		if(inst_type == 3 || inst_type == 0 )
			$('input[name="no"]',this).val("");
		
	});

/** RECONCILE Code Block 1**/

        function validate_selected_invoice_val() {
            var err_status=true;
            $(".amt_unreconcile").each(function(i,row){
                if($(row).val() != '') { // selected invoice
                    var addi_val = $(".amt_adjusted:eq("+i+")");
                    if(addi_val.val() == '') { //additional value is empty
                        //$(".error_status").html("Please enter additional amount!");
                        return err_status = "Please specify adjusted amount for selected invoices";
                        addi_val.focus();
                    }
                }
            });
            return err_status;
        }
        
        var icount=0;
        
        $(".clone_rows").live("click",function() {
            
            var rtype = $("#r_type").find(":selected").val();
            if(rtype == 0) { alert("Security Deposit of type can not reconcile the Amount."); return false; }
            
            var receipt_amount = $("#receipt_amount").val();
            if( $(".error_status").html() != '' && receipt_amount != '') {
                return false;
            }
            
            var reconciled_total= parseFloat( $("abbr",".reconciled_total").html() );
            if(receipt_amount != '' && reconciled_total == receipt_amount) {
                alert("All Amount Adjusted.");
                return false;
            }
            //alert(icount);
            var html='';
            
            if(icount == 0)
                html +="<tr><th>&nbsp;</th><th>Invoice No</th><th>Un-reconcile Amount</th><th>Adjusted Amount</th></tr>";
            
            icount = icount+1;
            html += "<tr class='invoice_row' id='reconcile_row_"+icount+"'>\n\
                            <td><span class='button button-tiny_wrap cursor button-caution' onclick='remove_row("+icount+");'>-</span></td>\n\
                            <td>\n\
                                <select size='2' name='sel_invoice[]' id='selected_invoices_"+icount+"' class='sel_invoices' onchange='fn_inv_selected(this,"+icount+");'>\n\
                                </select>\n\
                            </td>\n\
                            <td><input type='text' class='inp amt_unreconcile money' name='amt_unreconcile[]' id='amt_unreconcile_"+icount+"' size=6></td>\n\
                            <td><input type='text' class='inp amt_adjusted money' name='amt_adjusted[]' id='amt_adjusted_"+icount+"' size=6 value=''></td>\n\
                        </tr>";

                $("#reconcile_row").append(html);
                var invs_id = $("#selected_invoices_"+icount);
                load_unconciled_invoices(invs_id);
        });
        
        function load_unconciled_invoices(invs_sel) {
            //on click
            var invs = "<option value='00'>Choose</option>\n";
            $.post(site_url+"/admin/jx_get_unreconciled_invoice_list/"+franchise_id,{},function(resp) {
                    if(resp.fran_invoices.length) {
                        $.each(resp.fran_invoices,function(i,invoice) {
                            invs += "<option value='"+invoice.invoice_no+"' inv_amount='"+invoice.inv_amount+"'>"+invoice.invoice_no+" (Rs."+invoice.inv_amount+") </option>\n";
                        });
                    }
                    else 
                        alert(resp);
                    invs_sel.html(invs);
                    invs_sel.chosen();
            },'json');
        }
        
    var arr_invs = [];
    function remove_row(rowid) {
        var tbody=$("#reconcile_row");
        update_values(rowid);
        $("#reconcile_row_"+rowid,tbody).remove();

    }
    
    function update_values(rowid) {
        //remove invoice
        //subtract ajusted amount
        var sel_inv = $("#selected_invoices_"+rowid).find(':selected').val();
        if(arr_invs.length)
            arr_invs.remove(sel_inv);
//            else $("#reconcile_row").html("");

        var amt_adjusted = $("#amt_adjusted_"+rowid).val();
        var reconciled_total= parseFloat( $("abbr",".reconciled_total").html() );
        var sub_amount = reconciled_total - amt_adjusted;
        $("abbr",".reconciled_total").html(sub_amount);

        if(rowid == 0)
            $("#reconcile_row").parent().html("");
    }
        
    function fn_inv_selected(elt,count) {

        var receipt_amount = $("#receipt_amount").val();
        var error_status = $(".error_status");
        var sel_invoice_dropbox = $("#selected_invoices_"+count);
        var reconciled_total= parseFloat( $("abbr",".reconciled_total").html() );
        var sel_inv = $(elt).find(':selected').val();
        var sel_inv_amount = parseFloat( $(elt).find(':selected').attr("inv_amount") );
        var invoice_row = $("#reconcile_row_"+count);
        
        error_status.html("");
        if(receipt_amount == 0) {
            error_status.html("Please specify receipt amount"); $("#receipt_amount:first:visible").focus(); return false;
        }
        
                if($(".invoice_row").hasClass("inv_"+sel_inv)) {//if row having inv_{invid} class then the invoice already selected
                    sel_invoice_dropbox.val("").trigger("liszt:updated");
                    error_status.html("Already this invoice selected"); return false;
               }
               else {
                    invoice_row.removeClass();
                    invoice_row.addClass("invoice_row inv_"+sel_inv);
                    arr_invs.push(sel_inv);
               }
           
        //if(reconciled_total < receipt_amount) {

            var i_sub_total = sel_inv_amount + reconciled_total;
            if(i_sub_total < receipt_amount) {
                if(count) {
                    $("#amt_unreconcile_"+count).val(sel_inv_amount);
                    $("#amt_adjusted_"+count).val(sel_inv_amount);
                }
                /*else {$("#amt_unreconcile").val(sel_inv_amount);$("#amt_adjusted").val(sel_inv_amount);}*/
            }
            else {
                var add_inv_btn = false;
                var i_sub_total = receipt_amount - reconciled_total;
                $("#amt_unreconcile_"+count).val(sel_inv_amount);
                $("#amt_adjusted_"+count).val(i_sub_total);
                //alert("Invoice amount cannot be more than the receipt amount!");
            }
            $(".amt_adjusted").trigger("change");
            //} else { alert("Invoice amount cannot be more than the receipt!"); return false; }
    }
        
        
    $("#receipt_amount").keyup(function() {
       $(".error_status").html(""); 
    });

    $(".amt_adjusted").live("change",function() {
        show_unconcile_total();
    });
        
    function show_unconcile_total() {
        var invs_total = 0;
        $(".amt_adjusted").each(function(i,row) {
            var amount = $(this).val();
            if(amount!='') {
                //print( parseFloat(amount ) );
                invs_total += parseFloat(amount);
            }
        });

        $("abbr",".reconciled_total").html( format_number ( invs_total ) );
    }

    function myInArray(needle, haystack) {
        return $.inArray(needle, haystack) !== -1;
    }

/** RECONCILE Code Block 2**/

    $("#dlg_unreconcile_view_list").dialog({
        modal:true,
	autoOpen:false,
	width:600,
	height:506,
	autoResize:true
        ,buttons:{
            "Close":function() {
                $(this).dialog("close");
            }
        }
    });
    
    function clk_view_reconciled(elt,receipt_id,franchise_id) {
        var recon_list='';
        $.post(site_url+"/admin/jx_get_fran_reconcile_list/"+receipt_id+"/"+franchise_id,{},function(resp) {
            if(resp.status == 'success') {
                recon_list += "<h3>View reconciled list for Receipt #"+receipt_id+"</h3>\n\
                                <table class='datagrid1'>\n\
                                    <tr><td>Receipt #</td><th>"+resp.receipt_det.receipt_id+"</th></tr>\n\
                                    <tr><td>Receipt Amount</td><th>Rs. "+resp.receipt_det.receipt_amount+"</th></tr>\n\
                                    <tr><td>Un reconciled Amount</td><th>Rs. "+resp.receipt_det.unreconciled_value+" </th></tr>\n\
                                    <tr><td>Created On</td><th>"+resp.receipt_det.created_date+"</th></tr></table>";
                recon_list += "<br><table width='100%' class='datagrid'><tr><th>#</th><th>Invoice No</th><th>Invoice Amount</th><th>Reconciled Value</th><th>Unreconciled Amount</th><th>Created By</th><th>Created On</th></tr>";
                $.each(resp.reconcile_list,function(i,recon) {
                    recon_list += "<tr><td>"+(++i)+"</td><td>"+recon.invoice_no+"</td><td>"+recon.inv_amount+"</td><td>"+recon.reconcile_amount+"</td><td>"+recon.unreconciled+"</td><td>"+recon.username+"</td><td>"+recon.created_date+"</td>";
                });
                recon_list += "</table>";
            }
            else if(resp.status == 'fail') {
                recon_list += resp.response;
            }
            else {
                alert(resp);return false;
            }
            $("#dlg_unreconcile_view_list").html(recon_list).dialog('open').dialog("option","title","Reconciled list of Receipt #"+receipt_id);
        },'json');
    }
    
    var dg_icount = 1;
   $("#dlg_unreconcile_form").dialog({
        modal:true,
	autoOpen:false,
	width:"600",
	height:"630",
	autoResize:true
        ,buttons:{
            "Submit":function() {
                var dl_submit_reconcile_form= $("#dl_submit_reconcile_form");
                if(dl_submit_reconcile_form.parsley('validate')) {
                    $.post(site_url+"/admin/jx_dl_submit_reconcile_form/"+franchise_id,dl_submit_reconcile_form.serialize(),function(resp) {
                        if(resp.status=='success') {
                            //load_receipts(this,'unreconcile',0,franchise_id,100);
                            alert("Receipt reconcilation done.");
                            $("#dlg_unreconcile_form").dialog("close");
                            window.location.reload();
                            //history.go(0);window.location.href=window.location.href;
                        }
                        else {
                            print(resp.message);
                        }
                        
                    },'json');
                }
                else {
                    alert("All fields are required.");
                }
            }
            ,"Close":function() {
                //$("#dl_submit_reconcile_form").clearForm();
                $(".dg_amt_unreconcile,.dg_amt_adjusted,.dg_l_total_adjusted_val,.dg_ttl_unreconciled_after").val(0);
                $(".dg_sel_invoices").val("").trigger("liszt:updated");
                $(".dg_invoice_row").removeClass().addClass("dg_invoice_row");
                dg_icount = 1;
                $(this).dialog("close");
            }
        }
    });
    
    function clk_reconcile_action(elt,receipt_id,franchise_id,receipt_amount,unreconciled_value) {
            /*$("#clk_reconcile_action").live("click",function() {
            var elt=$(this); var receipt_id = elt.attr("receipt_id"); var franchise_id = elt.attr("franchise_id"); var receipt_amount = elt.attr("receipt_amount");var unreconciled_value = elt.attr("unreconciled_value");*/

            // set data
            var dlg = $("#dlg_unreconcile_form");
//            $(".dg_l_receipt_id",dlg).html(receipt_id);$(".dg_l_receipt_amount",dlg).html(receipt_amount+" Rs.");$(".dg_l_unreconciled_value",dlg).html(unreconciled_value+" Rs.");
            
            $("#dg_i_receipt_id",dlg).val(receipt_id);$("#dg_i_receipt_amount",dlg).val(receipt_amount);$("#dg_i_unreconciled_value",dlg).val(unreconciled_value);

            $("#dlg_unreconcile_form").dialog('open').dialog("option","title","Reconcile the Receipt #"+receipt_id);

            var invs_id = $("#dlg_selected_invoices_1");
            load_unconciled_invoices(invs_id);
    }
    
    function dg_add_invoice_row(elt) {
            var recon_list = '';
            var dg_i_unreconciled_value= format_number( $("#dg_i_unreconciled_value").val() );
            var dg_l_total_adjusted_val= format_number( $(".dg_l_total_adjusted_val").val() );
            
            if( dg_i_unreconciled_value == dg_l_total_adjusted_val ) { // if unredconciled and adjusted amount is same no more adjustments
                alert("All amount adjusted."); return false;
            }
            dg_icount = dg_icount + 1;
            //alert(dg_icount);
            recon_list += "<tr class='dg_invoice_row' id='dg_reconcile_row_"+dg_icount+"'>\n\
                            <td>\n\
                                <select size='2' name='sel_invoice[]' id='dg_selected_invoices_"+dg_icount+"' class='dg_sel_invoices' onchange='dg_fn_inv_selected(this,"+dg_icount+");'></select>\n\
                            </td>\n\
                            <td><input type='text' readonly='true' class='inp dg_amt_unreconcile money' name='amt_unreconcile[]' id='dg_amt_unreconcile_"+dg_icount+"' size=6></td>\n\
                            <td><input type='text' class='inp dg_amt_adjusted money' name='amt_adjusted[]' id='dg_amt_adjusted_"+dg_icount+"' size=6 value=''></td>\n\
                            <td><a href='javascript:void(0);' class='button button-tiny_wrap cursor button-caution' onclick='dg_remove_row("+dg_icount+");'>-</a></td>\n\
                        </tr>";
                var dlg = $("#dlg_unreconcile_form");
                $(".dlg_invs_list",dlg).append(recon_list);

                var invs_id = $("#dg_selected_invoices_"+dg_icount,dlg);
                load_unconciled_invoices(invs_id);
    }
    
    var dg_add_inv_btn = true;
    function dg_fn_inv_selected(elt,dg_icount)
    {
            var rpt_unreconciled_value = $("#dg_i_unreconciled_value").val();
            var error_status = $(".dg_error_status").html("");
            
            if(rpt_unreconciled_value == 0) {
                error_status.html("No Unreconciled amount."); return false;
            }
            var amt_unreconcile = $("#dg_amt_unreconcile_"+dg_icount);
            var amt_adjusted = $("#dg_amt_adjusted_"+dg_icount);
            var invoice_row = $("#dg_reconcile_row_"+dg_icount);
            var sel_invoice_dropbox = $("#dg_selected_invoices_"+dg_icount);
            
            var reconciled_total= format_number( $(".dg_l_total_adjusted_val").val() );
            var sel_inv = $(elt).find(':selected').val();
            var sel_inv_amount = format_number( $(elt).find(':selected').attr("inv_amount") );
            
            //if(reconciled_total < rpt_unreconciled_value) {
                   
                    if($(".dg_invoice_row").hasClass("inv_"+sel_inv)) { //if row having inv_{invid} class then the invoice already selected
                         sel_invoice_dropbox.val("").trigger("liszt:updated");
                         error_status.html("Already this invoice selected"); return false;
                    }
                    else 
                         invoice_row.removeClass().addClass("dg_invoice_row inv_"+sel_inv);

                    //new 
                    var i_sub_total = sel_inv_amount + reconciled_total;
                    if(i_sub_total < rpt_unreconciled_value) {
                        if(dg_icount) {
                            amt_unreconcile.val(sel_inv_amount);
                            amt_adjusted.val(sel_inv_amount);
                        }
                        /*else {$("#amt_unreconcile").val(sel_inv_amount);$("#amt_adjusted").val(sel_inv_amount);}*/
                    }
                    else {
                        var i_sub_total = rpt_unreconciled_value - reconciled_total;
                        amt_unreconcile.val(sel_inv_amount);
                        amt_adjusted.val(i_sub_total);
                        //alert("Invoice amount cannot be more than the receipt amount!");
                    }
                    $(".dg_amt_adjusted").trigger("change");

            //} else {   alert("Invoice amount cannot be more than the receipt!");  }
            return false;
        }
                
        $(".dg_amt_adjusted").live("change",function() {
            dg_show_unconcile_total();
        });
        
        function dg_show_unconcile_total() {
            var invs_total = 0;
            $(".dg_amt_adjusted").each(function(i,row) {
                var amount = $(this).val();
                if(amount!='') {
                    invs_total += format_number(amount);
                }
            });
            //var ttl_unreconciled_after = parseFloat( $(".dg_ttl_unreconciled_after").val() );
            var unreconcile_receipt_amount = format_number( $("#dg_i_unreconciled_value").val() );
            
            $(".dg_l_total_adjusted_val").val( format_number ( invs_total ) );
            $(".dg_ttl_unreconciled_after").val( format_number ( unreconcile_receipt_amount - invs_total ) );
        }

    var dg_arr_invs = [];
    function dg_remove_row(rowid) {
        var tbody=$(".dlg_invs_list");
        dg_update_values(rowid);
        $("#dg_reconcile_row_"+rowid,tbody).remove();

    }
    
    function dg_update_values(rowid) {
        //Remove invoice
        //Subtract ajusted amount
        var sel_inv = $("#dg_selected_invoices_"+rowid).find(':selected').val();
        var reconciled_total= format_number( $(".dg_l_total_adjusted_val").val() );
        if(dg_arr_invs.length)
            dg_arr_invs.remove(sel_inv);
//            else $("#reconcile_row").html("");

        var amt_adjusted = $("#dg_amt_adjusted_"+rowid).val();
        
        var sub_amount = reconciled_total - amt_adjusted;
       $(".dg_l_total_adjusted_val").val( format_number( sub_amount ) );
    }
    
$(window).resize(function() {
   $("#dlg_unreconcile_form,#dlg_unreconcile_view_list").dialog("option","position",["center","center"]); 
});