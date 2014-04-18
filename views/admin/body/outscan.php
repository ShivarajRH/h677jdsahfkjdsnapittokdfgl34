<div class="outscan">
	<?php 
		if(!$scan_pnh)
		{
	?>
	<span style="float: right;margin:0px 10px;">
		<input type="button" value="Generate Outscan report" onclick="$('#partner_outscan_report').dialog('open')" >
	</span>
	<?php } ?>
	
	<h2 >
		<?php if($scan_pnh)
		{
			/*echo 'Scan packed orders for shipment' .'<span style="font-size:12px; padding:5px;margin:5px;" class="dash_bar_right">Pending Shipments For Packing :'. $this->db->query("select count(distinct a.invoice_no) as total_penpacking from shipment_batch_process_invoice_link a join king_invoice b on b.invoice_no=a.invoice_no and b.invoice_status=1 join king_transactions c on c.transid=b.transid where c.is_pnh=1 and a.packed=0 and inv_manifesto_id=0
			")->row()->total_penpacking.'</span>';*/
			
			echo 'Already Packed Orders - OutScan' .'<div style="font-size:12px; padding:5px;margin:5px;" class="dash_bar_right" id="ttl_penpack"></div>';
		}else{
			echo 'Outscan Order';
		}
		?>
	</h2>
	
	<div  style="padding:0px;">
		<table width="100%">
			<tr>
				<td valign="top" style="vertical-align: top;">
					<select name="scan_by" style="padding:4px;display: none;">
					<?php 
						if($scan_pnh)
							echo '<option value="1">AWB/Invoice No Barcode</option>';
						else
							echo '<option value="2">Partner Orderno</option>';
					?>
					</select>
					
					<?php
					if($scan_pnh)
					{
						echo '<select class="inp" name="tray_no" style="display:none;">';
								$tray_list_res = $this->db->query('select * from m_tray_info order by tray_name ');
								
								echo '<option value="">Choose Tray</option>';
								if($tray_list_res->num_rows())
								{
									foreach ($tray_list_res->result_array()  as $tray_det)
									{
										echo '<option value="'.$tray_det['tray_id'].'">'.$tray_det['tray_name'].'</option>';
									}
								}
								
						
						echo '</select>';
					}
					?>
					 
					<select name="sel_partner_id" style="display: none;padding:4px;">
						<option value="">Orders By</option>
						<option value="0">Snapitoday</option>
						<?php 
							$partner_list_res = $this->db->query("select * from partner_info order by name ");
							if($partner_list_res->num_rows())
							{
								foreach ($partner_list_res->result_array() as $partner_det)
								{
						?>
								<option value="<?php echo $partner_det['id'] ?>"><?php echo ucwords($partner_det['name']); ?></option>
						<?php			
								}	
							}
						?>
					</select>
				 
					<span id="sel_courier_name_blk" style="display: none;">
						<select id="sel_courier_name" name="courier_name[]" placeholder="Choose Couriers" multiple="multiple" class="inp" style="width: 300px !important;" >
							<?php 
								$hs_courier_names_res = $this->db->query("select distinct courier_name from partner_transaction_details where courier_name != '' order by courier_name ");
								if($hs_courier_names_res->num_rows())
								{
									foreach($hs_courier_names_res->result_array() as $cdet)
									{
							?>
										<option value="<?php echo $cdet['courier_name'] ?>"><?php echo $cdet['courier_name'] ?></option>
							<?php 					
									}
								} 
							?>
						</select>
					</span>
					<input class="inp" id="scan_barcode" style="padding:5px;width: 300px;">
					<input type="button" value="Scan" onclick='go_outscan()' style="padding:5px;">
					
					
					<?php
						if($scan_pnh)
						{
							echo '<span id="working_tray">Working Tray : <b>Pending</b></span>';
						} 
					?>
					
				</td>
			</tr>
			
		</table>
	</div>
	
	<?php if(isset($scanned)){?>
	Order with invoice no <a href="<?=site_url("admin/invoice/{$scanned}")?>"><?=$scanned?></a> outscanned
	<?php }?>
	
	<form id="outform" method="post">
		<input type="hidden" name="tray_id" value="">
		<input type="hidden" name="partner_id" value="">
		<input type="hidden" name="inp_courier_name" value="">
		<input type="hidden" name="no_scan_by" value="">
		<input type="hidden" name="awn" class="awn">
	</form>
	
	<br>
		<div id="courier_scansumm" style="position: absolute;top: 230px;right:0px;">
			<h3 style="margin:10px 5px;font-weight: bold;">Scan Summary by Courier </h3> 
			<ul style="margin:5px;"></ul>
		</div>
		
	<?php if($scan_pnh){?>
		<div id="pnh_packed_traysumm" style="position: absolute;top: 230px;right:0px;">
			<h3 style="margin:10px 5px;font-weight: bold;">Shipments in tray </h3> 
			<ul style="margin:5px;"></ul>
		</div>
		
	<?php }?>	
	<h3>
		<span class="outscanned_ttl scan_res" ><?php echo $scan_pnh?'Packed':'Outscanned'?> <b id="outscan_ttl">0</b> </span>
		<span class="cancelled_ttl scan_res" >Cancelled  <b >0</b> </span>
		
		<?php 
			if($scan_pnh)
			{
		?>
			<span class="already_packed_ttl scan_res" >Already Packed <b >0</b> </span>
			<span class="already_shipped_ttl scan_res" >Already Shipped  <b >0</b> </span>
		<?php
			}else 
			{
		?>
			<span class="already_shipped_ttl scan_res" >Already Shipped  <b >0</b> </span>
			<span class="no_manifesto_ttl scan_res" >Not in Manifesto <b >0</b> </span>
			<span class="courier_mismatch_ttl scan_res" >Courier Mismatch <b >0</b> </span>
		
		<?php 		
			}
		?>
		<span class="not_found_ttl scan_res" >Not found <b>0</b> </span>
		=
		<span class="scanned_ttl scan_res" > Scanned <b id="count" style="font-weight: normal;font-size: 42px;">0</b></span>	
	</h3>
	<div id="frames_cont"></div>
	
	<div id="tray_summary">
		<div class="clear"></div>
	</div>
	
	

</div>

<style>
	#courier_scansumm{margin:3px;padding:5px;background: #f8f8f8;padding:5px;}
	#courier_scansumm ul{margin:0px;padding:0px;} 
	#courier_scansumm li {margin:3px 0px;list-style: none;border-bottom: 1px dotted #ccc;font-size: 12px;margin-top: 5px;overflow: hidden;}
	#courier_scansumm li .count{float: right;}
	.scan_res{padding:5px 10px;color: #FFF;font-size: 13px;margin:3px;display: inline-block;min-width: 100px;text-align: center;} 
	.scan_res b{font-weight: bold;font-size: 22px;padding:5px;display: block;text-align: center;} 
	.scanned_ttl{background: #ffffa0;color: #000;}
	.outscanned_ttl,.outscanned{background: #f1f1f1 !important;color: #000 !important;}
	.cancelled_ttl{background: green;}
	.already_shipped_ttl{background: orange;}
	.not_found_ttl{background: #cd0000;}
	.no_manifesto_ttl{background: #cd0000;}
	.courier_mismatch_ttl{background: purple;}
	.already_packed_ttl{background: #cd0000;}
	#tray_summary{width:100%;}
	.tray{margin:5px;width:30%;border:2px solid #a1a1a1;padding:10px 40px;background:#dddddd;border-bottom-right-radius:25px;border-bottom-left-radius:25px;float:left;}
	.tray h3{padding:0px;margin:0px;font-size:12px;border-bottom:1px dotted #ffffff;}
	.scanned_inv{background: none repeat scroll 0 0 #FFFAD6;border: 1px dashed #F4EB9A;color: #333333;float: left;font-family: arial;font-size: 10px;font-weight:bold;margin: 2px;width: 100px;padding: 2px 4px;white-space: nowrap;}
	#pnh_packed_traysumm{margin:3px;padding:5px;background: #f8f8f8;padding:5px;width: 300px;}
	#pnh_packed_traysumm ul{margin:0px;padding:0px;} 
	#pnh_packed_traysumm li {margin:3px 0px;list-style: none;border-bottom: 1px dotted #ccc;font-size: 12px;margin-top: 5px;overflow: hidden;}
	#pnh_packed_traysumm li .count{float: right;}
	#pnh_pack_invoices{margin:3px;padding:5px;background: #f8f8f8;padding:5px;}
	
</style>


<div title="Download Outscan report - HS18" id="partner_outscan_report" style="display: none;;padding:10px;text-align: left;">
	<table cellpadding="8" cellspacing="0" width="100%">
	 <tr>
	 	<td valign="top" style="background: #f7f7f7;">
	 		<form target="hndl_downloadreport" action="<?php echo site_url('admin/download_outscanreport') ?>" method="post">
	 			<table width="100%" cellpadding="5" cellspacing="0" style="font-size: 11px;">
	 				<tr>
	 					<td><b>Invoices From</b></td>
	 					<td>
	 						<select name="d_partner_id" class="chosen" style="width: 200px;">
								<option value="">Choose</option>
								<option value="0">Snapittoday</option>
								<?php 
									$partner_list_res = $this->db->query("select * from partner_info order by name ");
									if($partner_list_res->num_rows())
									{
										foreach ($partner_list_res->result_array() as $partner_det)
										{
								?>
										<option value="<?php echo $partner_det['id'] ?>"><?php echo ucwords($partner_det['name']); ?></option>
								<?php			
										}	
									}
								?>
							</select>
	 					</td>
	 				</tr>
	 				<tr>
	 					<td><b>Courier</b></td>
	 					<td>
	 						<select name="d_courier_name[]" multiple="multiple" placeholder="Choose Couriers" style="width: 200px !important;">
								<?php 
									$hs_courier_names_res = $this->db->query("select distinct courier_name from partner_transaction_details where courier_name != '' order by courier_name ");
									if($hs_courier_names_res->num_rows())
									{
										foreach($hs_courier_names_res->result_array() as $cdet)
										{
								?>
											<option class="partner_courier_list" value="<?php echo $cdet['courier_name'] ?>"><?php echo $cdet['courier_name'] ?></option>
								<?php 					
										}
									}
								?>
								<?php 
									$courier_list_res = $this->db->query("select * from m_courier_info order by courier_name ");
									if($courier_list_res->num_rows())
									{
										foreach ($courier_list_res->result_array() as $courier_det)
										{
								?>
										<option class="sit_courier_list" value="<?php echo $courier_det['courier_id'] ?>"><?php echo ucwords($courier_det['courier_name']); ?></option>
								<?php			
										}	
									}
								?>
							</select>
	 					</td>
	 				</tr>
	 				<tr>
	 					<td><b>From:</b></td>
	 					<td><input class="inp" type="text" style="width: 80px;" id="outscan_stdate" name="outscan_stdate" value="<?php echo date('Y-m-d') ?>"></td>
	 				</tr>
	 				<tr>
	 					<td><b>To:</b></td><td><input class="inp" type="text" style="width: 80px;" id="outscan_endate" name="outscan_endate" value="<?php echo date('Y-m-d') ?>"></td>
	 				</tr>
	 				<tr>
	 					<td colspan="2" align="left"><b>Export All Outscans</b> : <input type="checkbox" value="1" name="allday" ></td>
	 				</tr>
	 				<tr>
	 					<td colspan="2" align="left">
	 						<input type="submit" onclick="" value="Download Manifesto" style="padding:5px 10px;">
	 					</td>
	 				</tr>
	 				
	 			</table>
			</form>
	 	</td>
	 	<td valign="top" >
	 		<div id="partner_manifesto_log" style="width: 440px;height: 350px;padding:5px">
	 			<h3 style="margin:0px 0px;margin-bottom: 5px;">Parter Manifesto Log</h3>
	 			<table class="datagrid" width="100%" cellpadding="0" cellspacing="0" style="font-size: 11px;">
	 				<thead><th>Serialno</th><th>Orders</th><th>LoggedOn</th>
	 					<th>&nbsp;</th>
	 				</thead>
	 				<tbody></tbody>
	 			</table>
	 			<div class="pagination"></div>
	 		</div>
	 	</td>
	 </tr>
	</table>
	<iframe id="hndl_downloadreport" name="hndl_downloadreport" style="width:1px;height:1px;border:none;"></iframe>
	
	<form id="download_byserialno" target="hndl_downloadreport" action="<?php echo site_url('admin/download_outscanreport') ?>" method="post">
		<input type="hidden" name="serial_no" value="0">
	</form>	
</div>

<div id="pnh_pack_invoices" title="Invoices list">
</div> 

<!-- modal for invoices list -->
<div id="invoices_list" title="Invoices list">
</div>
<!-- modal for invoices list end -->

<style>
	.outscan td *{vertical-align: top;}
	#partner_manifesto_log td{padding:4px;}
</style>



<script type="text/javascript">
	
	var d_courier_list = new Array();
		d_courier_list['sit'] = new Array();
		d_courier_list['part'] = new Array();
		
		$('select[name="d_courier_name[]"] option').each(function(){
			if($(this).hasClass('sit_courier_list'))
			{
				d_courier_list['sit'][d_courier_list['sit'].length] = new Array($(this).attr('value'),$(this).text());		
			}else
			{
				d_courier_list['part'][d_courier_list['part'].length] = new Array($(this).attr('value'),$(this).text());
			}
		});
	
	$(function(){
		$('.dg_print').parent().parent().hide();
		$('.chosen').chosen();
	});
	
	function download_manifesto(serial_no)
	{
		$('#download_byserialno input[name="serial_no"]').val(serial_no);
		$('#download_byserialno').submit();
	} 
	
	var ml_pg = 0;
	var ml_limit = 0;
	function load_manifestolog()
	{
		$.getJSON(site_url+'/admin/jx_manifesto_log/'+ml_pg,'',function(resp){
			var ml_data_len = 0;
			if(resp.manifesto_log != undefined)
			{
				var manifestoLogHtml = ''; 
					$.each(resp.manifesto_log,function(i,log){
						manifestoLogHtml += '<tr><td>'+log.serial_no+'</td><td>'+log.ttl+'</td><td>'+log.created_on+'</td><td><input type="button" value="Download" onclick="download_manifesto('+log.serial_no+')"></td></tr>'; 
					});
				$('#partner_manifesto_log tbody').html(manifestoLogHtml);
				ml_data_len = resp.manifesto_log.length;
			}
			
			ml_limit = resp.limit;
			
			var pagiHtml = '';
			if(ml_pg > 0)
				pagiHtml += '<a href="javascript:void(0)" onclick="paginate_manifesto_log(-1)">Prev</a>';
			
			if(ml_pg+ml_data_len < resp.ttl_rows)
				pagiHtml += '&nbsp;&nbsp;<a href="javascript:void(0)" onclick="paginate_manifesto_log(1)">Next</a>';
				 
			$('#partner_manifesto_log .pagination').html(pagiHtml);
		});
	}
	
	function paginate_manifesto_log(n)
	{
		ml_pg = ml_pg+n*ml_limit;
		load_manifestolog();
	}

	function load_courier_scansumm()
	{
		$.post(site_url+'/admin/jx_outscansummbycourier','',function(resp){
			var courierListHtml = '';
				$.each(resp.courier_list,function(a,b){
					courierListHtml += '<li>'+b.courier_name+' <span class="count">'+b.cnt+'</span></li>';
				});
			$('#courier_scansumm ul').html(courierListHtml);	
		},'json');
	}

	$('#partner_outscan_report').dialog({width:800,height:480,autoOpen:false,modal:true,open:function(){load_manifestolog();}});
	
	$('select[name="d_partner_id"]').change(function(){
		$('select[name="d_courier_name[]"] option').remove();
		var sel_part_id = $(this).val();
			if(sel_part_id == "")
			{
				$('select[name="d_courier_name[]"] option').remove();	
			}else if(sel_part_id == 0)
			{
				$.each(d_courier_list['sit'],function(a,b){
					$('select[name="d_courier_name[]"]').append("<option value='"+b[0]+"'>"+b[1]+"</option>");	
				});
				
			}else
			{
				$.each(d_courier_list['part'],function(a,b){
					$('select[name="d_courier_name[]"]').append("<option value='"+b[0]+"'>"+b[1]+"</option>");	
				});
			}
			$('select[name="d_courier_name[]"]').trigger("liszt:updated");
	}).trigger('change');
	
	$('select[name="d_courier_name[]"]').chosen({placeholder_text : "Choose Couriers"});
	$('#sel_courier_name').chosen({placeholder_text : "Choose Couriers"}); 
	
	$( "#outscan_stdate" ).datepicker({
      changeMonth: true,
      onClose: function( selectedDate ) {
        $( "#outscan_endate" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#outscan_endate" ).datepicker({
      changeMonth: true,
      onClose: function( selectedDate ) {
        $( "#outscan_stdate" ).datepicker( "option", "maxDate", selectedDate );
      }
    });

	$('select[name="scan_by"]').change(function(){
		$('select[name="sel_partner_id"]').val("");
		$('#courier_scansumm').hide();
		if($(this).val() == 1)
		{
			$('select[name="sel_partner_id"]').hide();
			$('#sel_courier_name_blk').hide();
			
		}else if($(this).val() == 2)
		{
			$('select[name="sel_partner_id"]').show();
			
		}
	}).trigger('change');
	
	$('select[name="sel_partner_id"]').change(function(){
		$('#partner_otscan_report').hide();
		$('#sel_courier_name_blk').hide();
		$('#courier_scansumm').hide();
		if($(this).val() == 5)
		{
			$('#partner_otscan_report').show();
			$('#sel_courier_name_blk').show();
			$('#courier_scansumm').show();
			load_courier_scansumm();
		}	
	});
	$('#courier_scansumm').hide();
</script>

<style>
.leftcont{display: none}
#frames_cont .scan_output{
width:301px;
font-size:12px;
font-family:arial;
margin:3px;
border:2px dashed red;
color: #FFF;
display: inline-block;
vertical-align: top;
}
#frames_cont .scan_output div{padding: 7px !important;letter-spacing: 0.7px;font-size: 13px;min-height: 30px;}
#working_tray{
font-size: 11px;
padding: 6px;
display: inline-block;
margin-left: 100px;
background: #F5F5F5;
vertical-align: middle;
margin-top: -10px;
text-align: center;
position: absolute;
border: 1px dashed #DFDFDF;
top: 140px;
left: 35%;
}
#working_tray b {font-size: 23px;display: block;min-width: 150px;}
</style>

<script>
var fno=1;


function load_pnh_packedsumm()
{
	$.post(site_url+'/admin/jx_pnh_packedsumm_by_tray','',function(resp){
		var packedsummHtml = '';
		var ttl_penpack='';
		var inv_tbl='';
		var to_check_trays=0;
			
			$.each(resp.packedsumm_Det,function(a,b){
				packedsummHtml += '<li style="padding:5px;"><a href="javascript:void(0)" tray_id="'+b.tray_id+'" class="show_invoices"> '+b.tray_name+' </a><span class="count" style="margin: 0px 5px;"> <b>( '+b.total_shipments+'/'+b.max_allowed+' ) </b></span> <span class="count" style="margin:0px 5px;"> '+b.territory_name+' </span> </li>';
			});

			$.each(resp.ttl_penpak,function(c,d){
				ttl_penpack="Only Invoiced :<span style='font-size:22px'>"+d.total_penpacking+"<br><a href='javascript:void(0)' id='show_invoices_for_pack'>view invoices</a><a href='"+site_url+"/admin/pnh_pending_shipments'>Choose shipments for delivery</a><span>";
			});

			inv_tbl+="<table class='datagrid' cellpadding='5' cellspacing='0' width='100%'>";
			inv_tbl+="	<thead>";
			inv_tbl+="		<tr>";
			inv_tbl+="			<th>#</th>";
			inv_tbl+="			<th>Territory</th>";
			inv_tbl+="			<th>Town</th>";
			inv_tbl+="			<th>Invoices</th>";
			inv_tbl+="		</tr>";
			inv_tbl+="	</thead>";
			inv_tbl+="	<tbody>";

			$.each(resp.invoices,function(e,f){
				inv_tbl+="	<tr>";
				inv_tbl+="		<td>"+(e+1)+"</td>";
				inv_tbl+="		<td>"+f.territory_name+"</td>";
				inv_tbl+="		<td>"+f.town_name+"</td>";
				var temp=new Array();
				$.each(f.inv.split(','),function(g,h){
					temp.push("<a href='"+site_url+"/admin/invoice/"+h+"' target='_blank'>"+h+"</a>");
				});
				inv_tbl+="		<td>"+temp.join(' ');+"</td>";
				inv_tbl+="	</tr>";	
			});
			inv_tbl+="	</tbody>";
			inv_tbl+="</table>";


		$.each(resp.to_check_trays,function(i,j){
			if(j.status==0)
			{
				to_check_trays=1;
			}
		});
			

		if(!to_check_trays)
		{
			//alert("All the trays are in use,please add new tray");
		}
		
		$('#pnh_packed_traysumm ul').html(packedsummHtml);	
		$("#ttl_penpack").html(ttl_penpack);
		$("#pnh_pack_invoices").html(inv_tbl);
		
	},'json');
}

function go_outscan()
{
	if($("#scan_barcode").val().length==0)
		return false;
	
	var tray_id = "";
	var tray_name='';
	/*if($('select[name="tray_no"]').length)
	{
		if(!$('select[name="tray_no"]').val())
		{
			alert("Please choose tray first");
			return false;
		}
		tray_id = $('select[name="tray_no"]').val();
		tray_name = $('select[name="tray_no"] option:selected').text();
	}*/
	
	var sel_type = $('select[name="scan_by"]').val();	
	var sel_partner_id = 0;
		if(sel_type == 2)
		{
				sel_partner_id = $('select[name="sel_partner_id"]').val();
				if(sel_partner_id == "")
				{
					alert("Please select partner");
					return false;
				}
				
				if(sel_partner_id == 5)
				{
					if($('#sel_courier_name').val()=="")
					{
						alert("Please select courier");
						return false;
					}
				}
				
		}
	
	$("#count").text(fno);
	fno++;
	
	$("#outform input[name='tray_id']").val(tray_id);
	$("#outform .awn").val($("#scan_barcode").val());
	$('#outform input[name="partner_id"]').val(sel_partner_id);
	$('#outform input[name="no_scan_by"]').val(sel_type);
	var courier_names = new Array();
		$('#sel_courier_name option:selected').each(function(){
			courier_names.push($(this).attr('value'));
		});
		courier_names = courier_names.join(',');
	
	$('#outform input[name="inp_courier_name"]').val(courier_names);

	var inv=$("#scan_barcode").val();
	$("#scan_barcode").val("");

	if(tray_id)
	{
		 
		build_tray_summary(tray_name,inv,tray_id);
		load_pnh_packedsumm();
	}

	<?php if($scan_pnh){?>

	load_pnh_packedsumm();	

	<?php }?>
		

	$.post(site_url+'/admin/outscan',$('#outform').serialize(),function(resp){
		
		$("#frames_cont").prepend('<div class="scan_output">'+resp+'</div>');
		
		<?php if($scan_pnh){?>

		load_pnh_packedsumm();	

		<?php }?>
		
		build_outscan_summary();
			
		if(sel_partner_id == 5)
			load_courier_scansumm(); 
	});
}

function build_tray_summary(tray_name,inv,tray_id)
{
	var tray_class='.'+tray_id;
	var tray_inner_data='';
	var append_data_class='#tray_summary '+tray_class;
	var tray_html='';

	if($(tray_class)[0])
	{
		tray_inner_data+='<div class="scanned_inv">'+inv+'</div>';
		$(append_data_class).append(tray_inner_data);
	}else{
		tray_html+='<div class="tray '+tray_id+'"><h3 align="center">'+tray_name+'</h3>';
		tray_html+=		'<div class="scanned_inv">'+inv+'</div>';
		tray_html+=	'</div>';
		$("#tray_summary").prepend(tray_html);
	}
}
	
function build_outscan_summary()
{
	var outscan_summ_flags = new Array('outscanned','cancelled','already_shipped','not_found','no_manifesto','courier_mismatch','already_packed');
		$.each(outscan_summ_flags,function(a,b){
			$('.'+b+'_ttl b').text($('#frames_cont .'+b).length);
		});
}




$(function(){
	$("#scan_barcode").keydown(function(e){
		if(e.which==13 || e.which==9)
			go_outscan();
	}).focus();
});


$("#show_invoices_for_pack").live("click",function(){
	$("#pnh_pack_invoices").dialog('open');
});

$("#pnh_pack_invoices").dialog({
	autoOpen:false,
	modal:true,
	height:'300',
	width:'600',
	autoResize:true,
	
	buttons:{
		
		'Close':function(){
			$(this).dialog('close');
		}
	}
});

<?php if($scan_pnh){?>

load_pnh_packedsumm();	

<?php }?>

$(".show_invoices").live('click',function(){
	var tray_id=$(this).attr("tray_id");
	$("#invoices_list").data({'tray_id':tray_id}).dialog('open');
	
});


$("#invoices_list").dialog({
	autoOpen:false,
	modal:true,
	width:'480px',
	height:'auto',
	autoResize:true,
	open:function(){
		$("#invoices_list").html('');
		var tray_id=$(this).data('tray_id');
		var html_cnt='';
		$.post(site_url+'/admin/jx_get_invbytray',{tray_id:tray_id},function(res){
			html_cnt+="<table class='datagrid' cellpadding='5' cellspacing='0' width='100%'>";
			html_cnt+="	<thead>";
			html_cnt+="			<tr>";
			html_cnt+="				<th>#</th>";
			html_cnt+="				<th>Hub</th>";
			html_cnt+="				<th>Town</th>";
			html_cnt+="				<th>Invoice</th>";
			html_cnt+="			</tr>";
			html_cnt+="	</thead>";
			html_cnt+="	<tbody>";
			$.each(res.inv_by_Tray,function(a,b){
				html_cnt+="	<tr>";
				html_cnt+="		<td>"+(a+1)+"</td>";
				html_cnt+="		<td>"+b.territory_name+"</td>";
				html_cnt+="		<td>"+b.town_name+"</td>";
				var tem =new  Array();
				$.each(b.invoice_nos.split(','),function(c,d){
					var inv="<a href="+site_url+"/admin/invoice/"+d+" target='_blank'>"+d+"</a>";
					tem.push(inv);
				});
				html_cnt+="		<td>"+tem.join(' ')+"</td>";
				html_cnt+="	</tr>";	
			});
			html_cnt+="	</tbody></table>";
			$("#invoices_list").html(html_cnt);
			
		},'json');
	},
	buttons:{
		'Close':function(){
			$(this).dialog('close');
		}
	}
});


</script>

<?php

