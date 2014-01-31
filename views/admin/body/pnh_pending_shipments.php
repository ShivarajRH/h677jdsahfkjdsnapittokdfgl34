<div class="container" style="padding-right:10px;">
	<h2>Choose Shipments for Delivery</h2>
	<?php if(1){
		$terr_arr=array();
		$terr_row_span=0;
	?>
	<div>
	
		<div class="dash_bar" style="float:left;">
			Total Shipments ready to be shipped : <b id="penfordelivery_total">0</b>
		</div>
		<div style="float:right;text-align: right;">
			<table cellpadding="4" >
				<tr>
					<td >Hub :</td>
					<td>
						<select name="territory_list" >
							<option value="0">choose</option>
						</select>
					</td>
					<td>Town :</td>
					<td>
						<select name="town_list" >
							<option value="0">choose</option>
						</select>
					</td>
					<td style="display:none;">Tray:</td>
					<td style="display:none;">
						<select name="tray_list">
							<option value="0">choose</option>
						</select>
					</td>
					<td>From : </td>
					<td><input type="text" name="inv_from" id="from_date" size="10"></td>
					<td>To : </td>
					<td><input type="text" name="inv_to" id="to_date" size="10"></td>
				</tr>
				
			</table>
		</div>
		<div class="clear"></div>
		
		<div id="pen_invfordelivery" >
			
		</div>
		
	</div>
	<?php } ?>
	
	<!-- Manifesto update dialogbox -->
	<div id="update_manifesto_dlg" title="Choose Shipment transport details">
		<form action="<?php echo site_url('admin/update_manifesto_detail')?>" method="post" id="manifesto_update_form">
			<input type="hidden" name="manifest_log_id" value="0">
			<div id="mani_invoice_nos"></div>
			<div id="mani_invoice_nos_by_town"></div>
		</form>
	</div>
	<!-- Manifesto update dialogbox end -->
	
	<!-- Add to manifesto modal -->
	<div id="add_inv_manifesto_dlg" title="Update invoices to existing manifesto">
		<form action="<?php echo site_url('/admin/add_inv_to_existing_manifesto')?>" method="post" id="add_inv_to_manifesto_form">
			<div id="mani_invoice_nos1"></div>
			<div id="mani_invoice_nos_by_town1"></div>
		</form>
	</div>
	<!-- Add to manifesto modal -->
	
	<!-- Remove the invoice for packed list -->
	<div id="remove_invoice_in_packed_list" title="Remove invoice from the packed list">
		<form action="<?php echo site_url('admin/pnh_remove_invoice_in_packed_list')?>" method="post" id="remove_inoice_packed_list">
			<div id="mani_invoice_nos1"></div>
			<div id="mani_invoice_nos_by_town1"></div>
		</form>
	</div>
	<!-- Remove the invoice for packed list -->
</div>

<style>
	.sel_invoice_list td{padding:5px;border:1px dotted #555;font-size:12px;font-family: arial}
	.show_invoice{min-width: 150px;}
	.frm_block td{font-size: 11px;}
	#man_selinv_summary{padding:6px;font-weight: bold;}
	.leftcont{display: none}
</style>

<script type="text/javascript">

$('select[name="sel_terr_ids[]"]').chosen();

var sel_invfordelivery = new Array();
var sel_invfordelivery_frdet = new Array();

function load_peninvfordelivery(pg)
{
	var param = {};
		if($('select[name="territory_list"]').val()!=0)
			param.terri_id = $('select[name="territory_list"]').val();
		if($('select[name="town_list"]').val()!=0)
			param.town_id = $('select[name="town_list"]').val();
		if($('input[name="inv_from"]').val()!='' && $('input[name="inv_to"]').val()!='')
		{
			param.st_date = $('input[name="inv_from"]').val();
			param.en_date = $('input[name="inv_to"]').val();
		}
		
		$('#pen_invfordelivery').html("<div align='center' style='padding:10px'><img src='"+base_url+'/images/loading.gif'+"'></div>");
	$.post(site_url+'/admin/jx_peninvoicesfordelivery/'+pg,{param:param},function(resp){
		$('#pen_invfordelivery').html(resp.pen_invfordelivery_list);

		$('#pen_invfordelivery input[name="select"]').each(function(){
			if($.inArray($(this).val(),sel_invfordelivery) >=0)
				$(this).attr('checked',true);
			else
				$(this).attr('checked',false);
		});
		
		
		$('#penfordelivery_total').html(resp.total_pending);

		if($("select[name=\"territory_list\"] option").length <= 1)
		{
			var terr_list_blk='<option value="0">choose</option>';
			$.each(resp.terr_list,function(a,b){
				terr_list_blk+='<option value="'+a+'">'+b+'</option>';	
				});
			$("select[name=\"territory_list\"]").html(terr_list_blk);
		}
		
		if(param.town_id == undefined)
		{
			var town_list_blk='<option value="0">choose</option>';
				$.each(resp.town_list,function(a,b){
						town_list_blk+='<option value="'+a+'">'+b+'</option>';	
				});
				$("select[name=\"town_list\"]").html(town_list_blk);
		}

		if($("select[name=\"tray_list\"] option").length <= 1)
		{
			var tray_list_blk='<option value="0">choose</option>';
			$.each(resp.tray_list,function(a,b){
				tray_list_blk+='<option value="'+a+'">'+b+'</option>';	
			});
			$("select[name=\"tray_list\"]").html(tray_list_blk);
			
		}

		if(!pg)
		{
			// create filter data [territory and town list]
			 
		}
		
	},'json');
}

$('#pen_invfordelivery .pagination a').live('click',function(e){
	e.preventDefault();
	
	var computed_pg = 0;
	var pg_link_arr = $(this).attr('href').split('/');
		computed_pg = pg_link_arr[pg_link_arr.length-1]; 
		
	load_peninvfordelivery(computed_pg);
	
});

load_peninvfordelivery(0);


$("select[name=\"territory_list\"]").change(function(){
	$("select[name=\"town_list\"] option:gt(0)").remove();
	load_peninvfordelivery(0);
});

$("select[name=\"town_list\"]").change(function(){
	load_peninvfordelivery(0);
});

$("input[name=\"inv_from\"]").change(function(){

	if($("input[name=\"inv_to\"]")!='')
	{
		load_peninvfordelivery(0);
	}
});

$("input[name=\"inv_to\"]").change(function(){

	if($("input[name=\"inv_from\"]")!='')
	{
		load_peninvfordelivery(0);
	}
});




$("input[name=select]").live('change',function(){
	if($(this).attr('checked'))
	{
		if($.inArray($(this).val(),sel_invfordelivery)==-1)
		{	
			sel_invfordelivery.push($(this).val());

			$('input[name="invoice_no[]"]',$(this).parent().parent()).each(function(){
				sel_invfordelivery_frdet[$(this).val()] = $(this).attr('fr_name');	
			});
		} 
	}else
	{
		var k = $.inArray($(this).val(),sel_invfordelivery);
			sel_invfordelivery[k] = '';
	}

	clean_array(sel_invfordelivery);
});

function clean_array(arr)
{
	var tmp = new Array();
	$.each(arr,function(a,b){
		if(b.length)
			tmp.push(b);
	});
}


$("input[name=\"select_all\"]").live('change',function(){
	if($(this).attr("checked"))
		$("input[name=select]").attr("checked",true);
	else
		$("input[name=select]").attr("checked",false);

	$("input[name=select]").trigger('change');	
});

$("#save_pending_invoices").live('click',function(){
	$("#update_manifesto_dlg").dialog('open');
});

prepare_daterange('from_date','to_date');





//update manifesto dlg
//get the manifesto invoices
$('#update_manifesto_dlg').dialog({

	autoOpen:false,
	modal:true,
	height:'auto',
	autoResize:true,
	width:1100,
	open:function(){

		$('#mani_invoice_nos',this).html('');
		var invnos = sel_invfordelivery.join(',');
		var dlgEle = $(this); 

		$.post(site_url+'/admin/jx_get_terrtownby_invlist','invnos='+invnos,function(resp){
			var sel_summary_html='<div id="man_selinv_summary">Total Shipment Selected : <b></b></div><div>';
					$.each(resp.sorted_towns,function(town_name,town_id){
								var twn_det = resp.inv_town_link[town_id];
									sel_summary_html+='<div class="town_det">';
									sel_summary_html+='	<h4>'+twn_det.name+'<span class="tgl_blck">&plus;</span><span style="font-size: 9px;font-weight: bold;float:right;padding:3px;margin-top:2px;">Edit-</span><span class="ttl_inv_count">'+' ( '+twn_det.ttl_inv+' ) </span></h4>';
									sel_summary_html+='	<div class="town_fran_list" style="display:none">';
									
									$.each(twn_det.franchises,function(fr_id,fr_det){
										sel_summary_html+="<h3>"+fr_det.name+"</h3>";	
										sel_summary_html+='<div class="sel_invoice_list">';
										$.each(fr_det.invoices,function(l,invoice){
											sel_summary_html += '<span class="fr_invoice_det"><input type="checkbox" name="invoice_nos[]" value="'+invoice+'" checked ><b>'+invoice+'</b></span> ';
										});
										sel_summary_html += '</div>';
									});
									sel_summary_html+="	</div>";
									sel_summary_html+='</div>';
							});
			sel_summary_html+='</div>';
			sel_summary_html += resp.frm_transporter_det;
			$('#mani_invoice_nos_by_town',dlgEle).html(sel_summary_html).show();
			$('#man_selinv_summary b',dlgEle).html(resp.invlist.length);

			if(!$('#shiping_date').hasClass('hasDatepicker'))
			{
				$('#shiping_date').datepicker();
			}
			
		},'json');
		
		
		$('div[aria-describedby="update_manifesto_dlg"]').css({'top':'30px'});
			
			
	},
	buttons:{
		'Generate' : function(){
			var c=confirm("Are you sure to create this manifesto");
			if(c)
				$('form',this).submit();
			else
				return false;
			
			
		},
		'Cancel':function(){
			$(this).dialog('close');
		}
	}
});
//update manifesto dlg end

//select all function
function select_option(ele)
{
	$(".sel").attr("checked",false);
	if($(ele).attr('checked'))
	{
		if($('#terr_list').val())
			$(".show_invoice_"+$('#terr_list').val()+" .sel").attr("checked",true);
	}
}

//manifesto update form validation
$("#manifesto_update_form").submit(function(){
	var check_boxes=0;
	var trans_opt=$("select[name=transport_opts]",this).val();
	var drive_name='';
	var mobile_num='';
	var vehicle_number=$("input[name=vehicle_num]",this).val();
	//var drive_name=$("#other_driver").val().length;
	//var mobile_num=$("input[name=other_driver_ph]").val();
	var shipdate=$("input[name=shiping_date]").val();
	
	$("input[name=\"invoice_nos[]\"]",this).each(function(){
			if($(this).attr("checked"))
			{
				check_boxes=1;
			}
		});

	if(check_boxes==0)
	{
		alert("Please select atleast one invoice");
		return false;
	}

	
	if(trans_opt=='Choose')
	{
		alert('Please select transport');
		return false;
	}

	if($("select[name=drivers_list]",this).val()=='choose' && $("select[name=drivers_list]",this).is(":visible"))
	{
		alert("Please select driver name");
		return false;
	}

	if($("select[name=field_cordinators_list]",this).val()=='choose' && $("select[name=field_cordinators_list]",this).is(":visible"))
	{
		alert("Please select field cordinator");
		return false;
	}

	
	if($("select[name=buses_list]",this).val()=='choose' && $("select[name=buses_list]",this).is(":visible"))
	{
		alert("Please select bus transport");
		return false;
	}

	if($("select[name=bus_det_add]",this).val()=='choose' && $("select[name=bus_det_add]",this).is(":visible"))
	{
		alert("Please select bus transport destination");
		return false;
	}

	
	if($("select[name=tr_tranport_type]",this).val()=='choose' && $("select[name=tr_tranport_type]",this).is(":visible"))
	{
		alert("Please select  transportation type");
		return false;
	}

	//new vali
	if( ($("select[name=fr_list]",this).val()=='' && $("select[name=fr_list]",this).is(":visible") && ( $("select[name=excutives_list]",this).is(':hidden') && $("select[name=territory_manager]",this).is(':hidden') ) ) || ($("select[name=excutives_list]",this).val()=='' && $("select[name=excutives_list]",this).is(":visible")) || ($("select[name=territory_manager]",this).val()=='' && $("select[name=territory_manager]",this).is(":visible")) )
	{
		alert("Please select to be collected by @ destination");
		return false;
	}
	//new vali end
	
	if(vehicle_number.length==0 && $("input[name=vehicle_num]",this).is(":visible"))
	{
		alert("Please enter vehicle number");
		return false;
	}

	if(drive_name==0 && $("#other_driver").is(":visible"))
	{
		alert("Please enter other transport");
		return false;
	}

	if($("input[name=other_driver_ph]").is(":visible"))
	{
		if(mobile_num.length==0)
		{
			alert("Please enter phone number");
			return false;
		}else if(isNaN(mobile_num))
		{		alert('Invalid phone number');
				return false;
		}else if(mobile_num.length <=9)
		{
			alert('Invalid phone number');
			return false;
		}
	}


	if($("select[name=courier_list]",this).val()==0 && $("select[name=courier_list]",this).is(":visible"))
	{
		alert("Please select Courier list");
		return false;
	}


	if(shipdate.length==0)
	{
		alert("Please choose shipdate");
		return false;
	}

	
	

});


//invoice territory vice filter
function select_invoice_by_territory(ele)
{
	var terr_id=$(ele).attr("value");
	var class_name='.show_invoice_'+terr_id;
	$(".sel_all").attr("checked",false);

	if($(".sel").is(':checked'))
	{
		var r=confirm("Some of the invoices select do you want to clear?");
		if(r==true)
		{
			$(".sel").attr("checked",false);
		}else{
				return false;
			}
	}

	if(terr_id!='all')
	{
		$.post(site_url+"/admin/get_executives_and_fc",{territory_id:terr_id},function(response){
			$("#pick-up-by").html(response);
			});
	}
	

	if(terr_id=='all')
	{
		///$(".show_invoice").show();
		$(".show_invoice").hide();
	}else{
			$(".show_invoice").hide();
			$(class_name).show();
	}
}

//transport options
function select_transport(ele)
{
	var value=$(ele).attr("value");
	$("#pick-up-by-blk").hide();
	$("#vehicle_no").hide();
	$("select[name=buses_list]").remove();
	$("select[name=bus_det_add]").remove();
	//$("#pick-up-by").html('');

	$('select[name="excutives_list"]').val('');
	$('select[name="fr_list"]').val('');
	
	$(".trans_opt_blk").hide();
	if(value=='Choose')
	{
		$(".trans_opt_blk").hide();
	}

	if(value==0)
	{
		$("#pick-up-by-blk").show();
		
	}

	if(value==7)
	{
		$("#drivers_list_blk").show();
		$("#vehicle_no").show();
	}
	else if(value==6)
		$("#field_cordinators_list_blk").show();
	else if(value==0)
		$("#other_trans").show();
	else if(value==4)
	{
		
		$("#courier_opt_blk").show();
	}
	
}

$('select[name="excutives_list"]').live('change',function(){
	$('select[name="fr_list"]').val('');
});

$('select[name="fr_list"]').live('change',function(){
	$('select[name="excutives_list"]').val('');
	$('select[name="territory_manager"]').val('');
	$('select[name="pickup_options"]').val(0);
	$(".excutives_list").hide();
	$(".territory_manager").hide();
	
});

//choose tranport type
$("select[name=tr_tranport_type]").live('change',function(){
	var transport_type=$(this).val();
	var html_cnt='';
	$("select[name=buses_list]").remove();
	$("select[name=bus_det_add]").remove();
	if(transport_type!='choose')
	{
		$.post(site_url+'/admin/jx_get_buses_list',{tranport_type:transport_type},function(res){
			html_cnt+='<select name="buses_list" id="busues_list" style="margin:2px;"><option value="choose">Choose Bus</option>';
			$.each(res.buses_list,function(a,b){
				html_cnt+='<option value="'+b.id+'">'+b.name+'('+b.contact_no+')</option>';	
			});
			html_cnt+='</select>';
			$(html_cnt).appendTo("#other_trans");
		},'json');
	}
});


//show bus transport
$("select[name=buses_list]").live('change',(function(){
	var trs_id=$(this).val();
	var transport_type_id=$("select[name=tr_tranport_type]").val();
	var html_cnt='';
	$("select[name=bus_det_add]").remove();
	$.post(site_url+'/admin/jx_get_bustrs_des_address',{bus_id:trs_id,transport_type:transport_type_id},function(res){
		html_cnt+='<select name="bus_det_add" style="margin:2px;"><option value="choose">choose Destination</option>';
		$.each(res.dest_address_list,function(a,b){
			var contact_det=b.contact_no.split(',');
			html_cnt+='<option value="'+b.id+'">'+b.short_name+'('+contact_det[0]+')</option>';
		});	
		html_cnt+='</select>';
		$(html_cnt).appendTo("#other_trans");
	},'json');
}));

$("select[name='pickup_options']").live('change',function(){
	$(".excutives_list").hide();
	$(".territory_manager").hide();
	$('select[name="fr_list"]').val('');

	var val=$(this).val();
	if(val==1)
		$(".excutives_list").show();
	else if(val==2)
		$(".territory_manager").show();
		
});

$(".pickup_options").live('click',function(){
	$(".excutives_list").hide();
	$(".territory_manager").hide();
	$('select[name="fr_list"]').val('');

	var emp_list=$(this).attr('emp_list');
	if(emp_list=='Executives')
		$(".excutives_list").show();
	else if(emp_list=='Territory Manager')
		$(".territory_manager").show();
});


//invoice scan option
function scan_invoice(e)
{
	if(e.which==13)
	{
		
		var invoice_num=$("#srch_barcode").val();
		if(invoice_num=='')
			alert('Invoice number field empty');
		var class_name='.'+invoice_num;
		$(class_name).attr("checked",true);
		if(!$(class_name)[0])
			alert('Invoice number not present');
		var td = $(class_name).closest("td");
		$(td).addClass("highlightprow");
		$("#srch_barcode").val("");
		
	}else if($(e).attr('value')=='scan')
	{
		var invoice_num=$("#srch_barcode").val();
		if(invoice_num=='')
			alert('Invoice number field empty');
		var class_name='.'+invoice_num;
		$(class_name).attr("checked",true);
		if(!$(class_name)[0])
			alert('Invoice number not present');
		var td = $(class_name).closest("td");
		$(td).addClass("highlightprow");
		$("#srch_barcode").val("");
	}
}

//invoices remove option
$(".remove_invoice").live("click",function(){
	var invoice_no=$(this).attr('invoice_no');

	var didConfirm = confirm("Are you sure wnat to remove this invoice?");
	if(didConfirm==true)
	{
		$.post(site_url+'/admin/remove_invoice',{invoice_no:invoice_no},function(res){
			if(res.status)
				var class_name='.rm_'+invoice_no;
				$(class_name).hide();
			},'json');
	}
});


$('.tgl_blck').live('click',function(e){
	e.preventDefault();
	if($(this).parent().parent().find('.town_fran_list').is(':visible'))
	{
		$(this).parent().parent().find('.town_fran_list').hide();
		$(this).html("&plus;");
	}else
	{
		$(this).parent().parent().find('.town_fran_list').show();
		$(this).html("&minus;");
	}
});


$("#add_to_manifesto").live('click',function(){
	$("#add_inv_manifesto_dlg").dialog('open');
});

//update manifesto dlg
//get the manifesto invoices
$('#add_inv_manifesto_dlg').dialog({

	autoOpen:false,
	modal:true,
	height:'auto',
	autoResize:true,
	width:1000,
	open:function(){

		$('#mani_invoice_nos1',this).html('');
		var invnos = sel_invfordelivery.join(',');
		var dlgEle = $(this); 

		$.post(site_url+'/admin/jx_get_terrtownby_invlist','invnos='+invnos,function(resp){
			var sel_summary_html='<div id="man_selinv_summary1">Total Shipment Selected : <b></b></div><div>';
					$.each(resp.sorted_towns,function(town_name,town_id){
								var twn_det = resp.inv_town_link[town_id];
									sel_summary_html+='<div class="town_det">';
									sel_summary_html+='	<h4>'+twn_det.name+'<span class="tgl_blck">&plus;</span><span style="font-size: 9px;font-weight: bold;float:right;padding:3px;margin-top:2px;">Edit-</span><span class="ttl_inv_count">'+' ( '+twn_det.ttl_inv+' ) </span></h4>';
									sel_summary_html+='	<div class="town_fran_list" style="display:none">';
									
									$.each(twn_det.franchises,function(fr_id,fr_det){
										sel_summary_html+="<h3>"+fr_det.name+"</h3>";	
										sel_summary_html+='<div class="sel_invoice_list">';
										$.each(fr_det.invoices,function(l,invoice){
											sel_summary_html += '<span class="fr_invoice_det"><input type="checkbox" name="invoice_nos[]" value="'+invoice+'" checked ><b>'+invoice+'</b></span> ';
										});
										sel_summary_html += '</div>';
									});
									sel_summary_html+="	</div>";
									sel_summary_html+='</div>';
							});
			sel_summary_html+='</div>';
			//sel_summary_html += resp.frm_transporter_det;
			sel_summary_html+='<div><table><tr><td>Manifesto : </td><td><select name="manifesto_list" ><option value="">choose</option>';
			$.each(resp.manifesto_list,function(a,b){
				sel_summary_html+='<option value="'+b.id+'">'+b.id+'</option>';	
			});

			sel_summary_html+='</select></td></tr></table></div>';
			$('#mani_invoice_nos_by_town1',dlgEle).html(sel_summary_html).show();
			$('#man_selinv_summary1 b',dlgEle).html(resp.invlist.length);

			if(!$('#shiping_date').hasClass('hasDatepicker'))
			{
				$('#shiping_date').datepicker();
			}
			
		},'json');
		
		
		$('div[aria-describedby="update_manifesto_dlg"]').css({'top':'30px'});
			
			
	},
	buttons:{
		'Add' : function(){
			var c=confirm("Are you sure to add this invoices to  manifesto");
			if(c)
				$('form',this).submit();
			else
				return false;
			
			
		},
		'Close':function(){
			$(this).dialog('close');
		}
	}
});
//update manifesto dlg end

$("#add_inv_to_manifesto_form").submit(function(){
	
	var manifesto_list=$("select[name='manifesto_list']",this).val();
	var check_boxes=0;
	
	if(manifesto_list=='')
	{
		alert('Please select manifesto');
		return false;
	}

	$("input[name=\"invoice_nos[]\"]",this).each(function(){
		if($(this).attr("checked"))
		{
			check_boxes=1;
		}
	});

	if(check_boxes==0)
	{
		alert("Please select atleast one invoice");
		return false;
	}
	
	return true;
});

$("#remove_invoice_pck_list").live("click",function(){
	$("#remove_invoice_in_packed_list").data({}).dialog('open');
});

$('#remove_invoice_in_packed_list').dialog({

	autoOpen:false,
	modal:true,
	height:'auto',
	autoResize:true,
	width:1000,
	open:function(){

		$('#mani_invoice_nos1',this).html('');
		var invnos = sel_invfordelivery.join(',');
		var dlgEle = $(this); 

		$.post(site_url+'/admin/jx_get_terrtownby_invlist','invnos='+invnos,function(resp){
			var sel_summary_html='<div id="man_selinv_summary1">Total Shipment Selected : <b></b></div><div>';
					$.each(resp.sorted_towns,function(town_name,town_id){
								var twn_det = resp.inv_town_link[town_id];
									sel_summary_html+='<div class="town_det">';
									sel_summary_html+='	<h4>'+twn_det.name+'<span class="tgl_blck">&plus;</span><span style="font-size: 9px;font-weight: bold;float:right;padding:3px;margin-top:2px;">Edit-</span><span class="ttl_inv_count">'+' ( '+twn_det.ttl_inv+' ) </span></h4>';
									sel_summary_html+='	<div class="town_fran_list" style="display:none">';
									
									$.each(twn_det.franchises,function(fr_id,fr_det){
										sel_summary_html+="<h3>"+fr_det.name+"</h3>";	
										sel_summary_html+='<div class="sel_invoice_list">';
										$.each(fr_det.invoices,function(l,invoice){
											sel_summary_html += '<span class="fr_invoice_det"><input type="checkbox" name="invoice_nos[]" value="'+invoice+'" checked ><b>'+invoice+'</b></span> ';
										});
										sel_summary_html += '</div>';
									});
									sel_summary_html+="	</div>";
									sel_summary_html+='</div>';
							});
			sel_summary_html+='</div>';
			//sel_summary_html += resp.frm_transporter_det;
			/*sel_summary_html+='<div><table><tr><td>Manifesto : </td><td><select name="manifesto_list" ><option value="">choose</option>';
			$.each(resp.manifesto_list,function(a,b){
				sel_summary_html+='<option value="'+b.id+'">'+b.id+'</option>';	
			});

			sel_summary_html+='</select></td></tr></table></div>';*/
			$('#mani_invoice_nos_by_town1',dlgEle).html(sel_summary_html).show();
			$('#man_selinv_summary1 b',dlgEle).html(resp.invlist.length);

			if(!$('#shiping_date').hasClass('hasDatepicker'))
			{
				$('#shiping_date').datepicker();
			}
			
		},'json');
		
		
		$('div[aria-describedby="update_manifesto_dlg"]').css({'top':'30px'});
			
			
	},
	buttons:{
		'Remove' : function(){
			var c=confirm("Are you sure to remove this invoices");
			if(c)
				$('form',this).submit();
			else
				return false;
			
			
		},
		'Close':function(){
			$(this).dialog('close');
		}
	}
});

</script>

<style>
.ui-widget{font-family: arial}
	.hidden{
	display:none;
}
.town_det{width: 100%;border-bottom: 1px solid #cdcdcd;font-size: 12px;}
.town_det h4{padding:7px;background: #dfdfdf;font-size: 12px;margin:0px 0px;}
.town_det h3{padding:3px 7px;background: #ffffc0;font-size: 11px;margin:0px 0px;}
.tgl_blck{float: right;font-size: 18px;font-weight: bold;cursor: pointer;}
.fr_invoice_det{display: inline-block;min-width: 120px;}
.sel_invoice_list{max-height: 400px;overflow: auto;background: #FFF !important;padding:3px 4px}
.ttl_inv_count{font-size: 12px;font-weight: bold;float:right;padding:3px;}
</style>
