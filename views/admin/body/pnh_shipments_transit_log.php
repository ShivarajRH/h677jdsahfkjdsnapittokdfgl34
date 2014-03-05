<div class="page_wrap container">
	<div class="clearboth">
		<div class="fl_left" >
			<h2 class="page_title">Shipment Transit Log for - <span id="ship_date_for"></span></h2>
		</div>
		<div class="fl_right">
			<a href="javascript:void(0)" id="show_sms_manual">sms manual</a>
		</div>
	</div>
	<div class="page_topbar" >
		<div class="page_topbar_left fl_left" >
		</div>
		<div class="page_action_buttonss fl_right" align="right">
			<b>Manifesto id</b> : <input type="text" name="manifesto_id" size="8">&nbsp;
			<b>From</b> : <input type="text" name="inv_from" id="from_date" class="inp fil_style" size="10">
			<b>To</b> : <input type="text" name="inv_to" id="to_date" class="inp fil_style" size="10">&nbsp; &nbsp;
			<b>Territory</b> : <select name="territory-list" ><option value="0">All</option></select>&nbsp; &nbsp; 
			<b>Town</b> : <select name="town-list" ><option value="0">All</option></select>&nbsp; &nbsp;
			<b>Hub</b> : <select name="hub-list" ><option value="0">All</option></select>&nbsp; &nbsp; 
			<b>Driver/Fc</b> : <select name="driver-list" ></select>&nbsp; &nbsp; 
			<b>Bus Transport</b> : <select name="buses-list" ></select>
		</div>
	</div>
	<div style="clear:both">&nbsp;</div>
	
	<div class="page_content">
		<div id="shipments-transit-log"></div>
	</div>
	
</div>


<div id="inv_transitlogdet_dlg" title="Shipment Transit Log">
	<h3 style="margin:3px 0px;"></h3>
	<div id="inv_transitlogdet_tbl">
		
	</div>
</div>

<div id="sms_manual_dlg" title="Shipments sms manual">
	<h3>Shipment Update Notification </h3>
	<table cellpadding="5" cellspacing="0">
		<tbody>
			<tr>
				<td>1)</td>
				<td>Manifesto pickup :</td>
				<td>
					<b>M [MANIFESTO ID]</b> <br>
                    ex : M 1234
				</td>
			</tr>
			<tr>
				<td>2)</td>
				<td>Delivery Update   :</td>
				<td>
					<b>D [Invoiceno]</b><br> 
                     ex : D 2014100200<br> 
                     multiple shipment invoice updates<br>
                     ex : D 2014100200,2014100201
				</td>
			</tr>
			<tr>
				<td>3)</td>
				<td>Manifesto Handover : </td>
				<td>
					<b>E [Invoiceno]-[EXECUTIVEMOBNO]</b><br> 
                     ex : E 2014100200-9999999999<br> 
                     multiple shipment invoice updates<br>
                     ex : E 2014100200,2014100201-9999999999
				</td>
			</tr>
		</tbody>
	</table>
</div>
<style>
#inv_transitlogdet_tbl table{font-size: 11px;}
.leftcont{display: none}
</style>

<script>
var refcont = null;
$('#inv_transitlogdet_dlg').dialog({width:'960',height:'auto',autoOpen:false,modal:true,
											open:function(){

												refcont = $(this).data('ref_container');
												var updated_by='';
												//,'width':refcont.width()
												//$('div[aria-describedby="inv_transitlogdet_dlg"]').css({'top':(refcont.offset().top+15+refcont.height())+'px','left':refcont.offset().left});
												
												$('#inv_transitlogdet_tbl').html('<div align="center"><img src="'+base_url+'/images/loading.gif'+'" ></div>');
												$.post(site_url+'/admin/jx_invoicetransit_det','invno='+$(this).data('invno'),function(resp){
													if(resp.status == 'error')
													{
														alert(resp.error);
													}else
													{
														var inv_transitlog_html = '<table class="datagrid" width="100%"><thead><th width="50%">Msg</th><th width="10%">Status</th><th width="10%">Shipped By</th><th width="10%">Logged On</th><th width="15%">SMS</th></thead><tbody>';
														$.each(resp.transit_log,function(i,log){

															updated_by=log[4]?log[2]+'('+log[4]+')':log[2];
															log[7]?	updated_by+='<br><span style="font-size:9px;">Alternative number : '+log[7]+'</span>':'';
															inv_transitlog_html += '<tr><td>'+log[5]+'</td><td>'+log[1]+'</td><td>'+updated_by+'</td><td>'+log[3]+'</td><td>'+log[6]+'<br><span style="font-size:9px;">'+log[8]+'</span></td></tr>';
														});
														inv_transitlog_html += '</tbody></table>';
														$('#inv_transitlogdet_tbl').html(inv_transitlog_html);

														$('#inv_transitlogdet_dlg h3 ').html('Invoice no : <span style="color:blue;font-size:12px">'+resp.invoice_no+'</span> '+' ManifestoNo :'+resp.manifesto_id);
														var title='Shipment Transit Log - Franchise name:'+resp.Franchise_name +' Town :'+resp.town_name;

														$('#inv_transitlogdet_dlg').dialog('option', 'title', title);

														
														
													}
												},'json');
											}
									});
function load_peninvfordelivery()
{	
	var param = {};
	if($('select[name="territory-list"]').val()!=0)
		param.terri_id = $('select[name="territory-list"]').val();
	
	if($('select[name="town-list"]').val()!=0)
		param.town_id = $('select[name="town-list"]').val();

	if($('input[name="inv_from"]').val()!='' && $('input[name="inv_to"]').val()!='')
	{
		param.st_date = $('input[name="inv_from"]').val();
		param.en_date = $('input[name="inv_to"]').val();
	}

	if($('select[name="driver-list"]').val()!=0)
		param.driver_id = $('select[name="driver-list"]').val();
	if($('select[name="buses-list"]').val()!=0)
		param.bus_id = $('select[name="buses-list"]').val();

	if($('select[name="hub-list"]').val()!=0)
		param.hubid = $('select[name="hub-list"]').val();
	
	if($("input[name='manifesto_id']").val()!='')
		param.manifesto_id = $("input[name='manifesto_id']").val();

	$('#shipments-transit-log').html('<div align="center"><img src="'+base_url+'/images/loading.gif'+'" ></div>');
	$.post(site_url+'/admin/jx_getshipments_transit_log',{param:param},function(resp){
		$('#shipments-transit-log').html(resp.pnh_shipmets_transit_log);

		if($("select[name=\"territory-list\"]").val() == 0 && param.hubid == undefined )
		{
			var terr_list_blk='';
			terr_list_blk+='<option value="0">All</option>';
			$.each(resp.territory_list,function(i,terr){
				terr_list_blk+='<option value="'+i+'">'+terr+'</option>';	
				});
			$('select[name=\"territory-list\"]').html(terr_list_blk);
		}
		
		if(param.terri_id != undefined && param.town_id == undefined)
		{
			var town_blk='';
			town_blk+='<option value="0">All </option>';
			$.each(resp.towns_list,function(k,town){
				town_blk+='<option value="'+k+'">'+town+'</option>';	
				});
			$('select[name=\"town-list\"]').html(town_blk);
		}

		if(param.driver_id == undefined && param.bus_id == undefined)
		{
			var driver_blk='';
			if(resp.driver_list)
			{
				driver_blk+='<option value="0">choose </option>';
				$.each(resp.driver_list,function(k,driver){
					if(driver==0)
					{
						driver_blk+='<option value="0">No drivers found</option>';		
					}else{
							driver_blk+='<option value="'+k+'">'+driver+'</option>';	
					}
					});
			}
			$('select[name=\"driver-list\"]').html(driver_blk);
		}

		if(param.bus_id == undefined && param.driver_id == undefined)
		{
			var buses_blk='';
			
			if(resp.bus_list)
			{
				buses_blk+='<option value="0">choose </option>';
				$.each(resp.bus_list,function(k,bus){
					if(bus==0)
					{
						buses_blk+='<option value="0">No Bus transport found</option>';		
					}else{
						buses_blk+='<option value="'+k+'">'+bus+'</option>';	
					}
					});
			}
			$('select[name=\"buses-list\"]').html(buses_blk);
		}

		var hub_blk='';
		if(resp.hub_list && param.hubid==undefined && param.terri_id == undefined )
		{
			hub_blk+='<option value="0">All </option>';
			$.each(resp.hub_list,function(h,hub){
				hub_blk+="<option value='"+hub.territory_id+"'>"+hub.hub_name+"</option>"
			});
			$('select[name=\"hub-list\"]').html(hub_blk);
		}

		$("#ship_date_for").html(resp.shiped_date);
	},'json');
}

$('#shipments-transit-log').html('loading...');
load_peninvfordelivery();

$("select[name=\"territory-list\"]").change(function(){
	$("select[name=\"town-list\"] option:gt(0)").remove();
	$("select[name=\"hub-list\"]").val(0);
	load_peninvfordelivery();
});

$("select[name=\"town-list\"]").change(function(){
	$("select[name=\"hub-list\"]").val(0);
	load_peninvfordelivery();
});

$("select[name=\"hub-list\"]").change(function(){
	$("select[name=\"territory-list\"]").val(0);
	$("select[name=\"town-list\"]").val(0);
	load_peninvfordelivery();
});


$("input[name=\"inv_from\"],input[name=\"inv_to\"]").change(function(){

	if($("input[name=\"inv_from\"]").val() && $("input[name=\"inv_to\"]").val())
	{
		$("select[name=\"territory-list\"] option:gt(0)").remove();
		$("select[name=\"town-list\"] option:gt(0)").remove();
		$("select[name=\"driver-list\"] option:gt(0)").remove();
		$("select[name=\"buses-list\"] option:gt(0)").remove();
		$("select[name=\"hub-list\"] option:gt(0)").remove();
		$("input[name='manifesto_id']").val('');
		
		load_peninvfordelivery();
	}
});
 

$("select[name=\"driver-list\"]").change(function(){

	if($("select[name=\"driver-list\"]").val()!='')
	{
		$("select[name=\"buses-list\"]").val(0).attr("selected", "selected");
		load_peninvfordelivery();
	}
});

$("select[name=\"buses-list\"]").change(function(){

	if($("input[name=\"buses-list\"]").val()!='')
	{
		$("select[name=\"driver-list\"]").val(0).attr("selected", "selected");
		load_peninvfordelivery();
	}
});

$("input[name='manifesto_id']").keyup(function(){
	if(isNaN($(this).val()))
		alert('Invalid manifesto id');
	else
	{
		$("select[name=\"territory-list\"] option:gt(0)").remove();
		$("select[name=\"town-list\"] option:gt(0)").remove();
		$("select[name=\"driver-list\"] option:gt(0)").remove();
		$("select[name=\"buses-list\"] option:gt(0)").remove();
		$("select[name=\"hub-list\"] option:gt(0)").remove();
		$("input[name=\"inv_from\"]").val(''); 
		$("input[name=\"inv_to\"]").val('');

		load_peninvfordelivery();
	}
});
prepare_daterange('from_date','to_date');

function get_invoicetransit_log(ele,invno)
{
	$('#inv_transitlogdet_dlg').data({'invno':invno,'ref_container':$(ele).parents('.show_invoice:first')}).dialog('open');
}

$("#show_sms_manual").click(function(){
	$("#sms_manual_dlg").dialog('open');
});

$("#sms_manual_dlg").dialog({
	width:'450',
	height:'400',
	autoOpen:false,
	modal:true,
	buttons:{
		'Close':function(){
			$(this).dialog('close');
		}
		}
	
});
</script>