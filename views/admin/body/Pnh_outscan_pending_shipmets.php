<div class="container">
	 
	<h2 style="margin-bottom: 3px;">Outscan Manifesto : <?php echo '#'.$manifest_id;?>
		
		<span style="font-size: 70%;margin-left: 10px;float: right;font-weight: normal">
		Delivery Hub : <b><?php echo $this->db->query("select group_concat(distinct d.hub_name) as hub_names 
																from pnh_m_manifesto_sent_log a 
																join pnh_t_tray_invoice_link b on find_in_set(b.invoice_no,a.sent_invoices)
																join pnh_t_tray_territory_link c on c.tray_terr_id = b.tray_terr_id and c.status = 1 and c.is_active = 1   
																join pnh_deliveryhub d on d.id = c.territory_id 
																where a.id = ? ",$manifest_id)->row()->hub_names;
																?></b> 
		</span>
	</h2>
	
	
	<?php if($pnh_pending_shipments_list){?>
	<form id="update_shipflag" action="<?php echo site_url('/admin/update_pnh_pending_shipmets')?>" method="post">
		<input type="hidden" value="<?php echo count($pnh_pending_shipments_list);?>" id="total_inv">
		<input type="hidden" value="<?php echo $manifest_id;?>" name="manifesto_nu">
		<table class="datagrid" width="100%" cellpadding="5" cellspacing="0">
			<thead>
				<tr>
					<th width="5%">#</th>
					<th width="">Tray</th>
					<th width="">Town</th>
					<th width="">Franchise</th>
					<th width="20%" style="display: none;">Invoice</th>
					<th width="20%">Invoice</th>
					<th width="10">Status</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($pnh_pending_shipments_list as $i=>$shipment){
					$dispatch_id = @$this->db->query("select dispatch_id 
																from proforma_invoices a 
																join shipment_batch_process_invoice_link b on a.p_invoice_no = b.p_invoice_no 
																where b.invoice_no = ? 
															group by a.p_invoice_no  ",$shipment['invoice_no'])->row()->dispatch_id;

				?>
					<tr class="row_<?php echo $shipment['invoice_no'];?>">
						<td><?php echo $i+1;?></td>
						<td><?php echo $shipment['tray_name'];?></td>
						<td><?php echo $shipment['town_name'];?></td>
						<td><?php echo $shipment['franchise_name'];?></td>
						<td class="unselectable">
							<span class="<?php echo 'show_inv_'.$shipment['invoice_no'];?> <?php echo 'show_inv_'.$dispatch_id;?>" style="display:none;"><?php echo $shipment['invoice_no'];?></span>
							<input type="checkbox" name="invoices[]"  class="<?php echo 'scan_'.$shipment['invoice_no'];?> <?php echo 'scan_'.$dispatch_id;?>" value="<?php echo $shipment['invoice_no'];?>" style="display:none;">
						</td>
						<td><b>Pending</b></td>
					</tr>
				<?php }?>
			</tbody>
		</table>
		<div style="margin-top: 20px;">
			<input type="submit" value="Submit & print manifesto" style="float: right; padding: 7px 10px;" id="outscan_invoices" >
		</div>
		<textarea name="part_ps_manifesto" style="display: none;"></textarea>
	</form>
	<?php }else{
		echo 'No Pending shipments found';
	}?>
	
	<div id="scanned_summ" >
		<h3>Scanned Qty</h3>
		<div class="scanned_summ_total"><span id="summ_scanned_ttl_qty">0</span> / <span id="summ_ttl_qty">0</span></div>
	</div>
	
	<div style="padding: 5px 10px; position: fixed; bottom: 50px; background: #ffaa00; right: 10px;">
			Scan Invoice : <input class="inp" id="scan_barcode" style="padding: 5px;"> 
			<input type="button" value="Go"onclick='validate_barcode()'>
	</div>
	
	
	
	
</div>


<!-- Modal for partial shipment conformation -->
<div id="scanned_remark_dlg" title="Confirmation Required">
	<p>Are you sure want to process this Manifesto partially
		<input type="checkbox" value="1" name="cnfrm_process_partial">
	</p>
	<b>Remark:</b><br>
	<textarea style="width: 100%;height: 60px;"></textarea>
</div>
<!-- Modal for partial shipment conformation end-->	

<style>
#scanned_summ{
	width: 160px;background: tomato;bottom: 0px;left:0px;position: fixed;border-top:5px solid #FFF;
	text-align: center;
	color: #FFF;
	font-size: 32px;
}
#scanned_summ h3{font-size: 20px;margin-top:10px;margin-bottom: 0px;}
.scanned_summ_total{padding:5px;}
.scanned_summ_stats{padding:5px;font-size: 15px;font-weight: bold;text-align: left;border-bottom: 1px dotted #FFF;}

.have {
	background: lightgreen  !important;
}
</style>

<script>


$("form#update_shipflag input[type=checkbox]").attr("checked",false);
$(function(){
		$("#scan_barcode,#scan_barcode2").keyup(function(e){
			if(e.which==13)
				validate_barcode();
	});

	
});

$('#scan_barcode').focus();
show_ttl_summary();

function validate_barcode()
{
	if($("#scan_barcode").val().length==0)
	{
		alert("Enter barcode");
		return;
	}

	var sbc = $.trim($("#scan_barcode").val());
	var sel_bcstk_ele = $(".scan_"+sbc);
	
	if(sel_bcstk_ele.length == 0 )
	{
		alert('Scanned invoice not found');
		return false;
	}	

	var row_ele=$('.row_'+sbc);
	
	if(row_ele.hasClass("have"))
	{
		alert("This Invoice already scanned");
		return;
	}
	
	sel_bcstk_ele.attr("checked",true);

	if(sel_bcstk_ele.attr("checked"))
	{
		row_ele.addClass('have');
		$('.row_'+sbc+' td:eq(5)').html('<b>Scanned</b>');
		$(".show_inv_"+sbc).show();
	}		

	$("#scan_barcode").val('');
	show_ttl_summary();
	check_all_scanned();
}

function show_ttl_summary()
{
	$('#summ_ttl_scanned_prod').text(0);
	var ttl_qty_scan = 0; 

	var totla_inv=$("#total_inv").val();
	$("#summ_ttl_qty").text(totla_inv);

	var ttl_scaned=$("#summ_scanned_ttl_qty").text();
	var scaned=($('.have').length);
	

	ttl_qty_scan =parseInt(scaned);
	
	$("#summ_scanned_ttl_qty").text(ttl_qty_scan);
	
}

function check_all_scanned()
{
	var ttl_check_box=$("form#update_shipflag input[type=checkbox]").length;
	var ttl_checked_check_box= $("form#update_shipflag input[type=checkbox]:checked").length;
	
	if(ttl_checked_check_box==ttl_check_box)
		$("form#update_shipflag").submit();
	
}

$("#outscan_invoices").click(function(){
	var ttl_checked_check_box= $("form#update_shipflag input[type=checkbox]:checked").length;
	var ttl_check_box=$("form#update_shipflag input[type=checkbox]").length;
	
	if(ttl_checked_check_box==0)
	{
		alert("Please scan atlest one invoice");
		return false;
	}
	
	if(ttl_checked_check_box < ttl_check_box)
	{
		$("#scanned_remark_dlg").dialog('open');
		return false;
	}

	return true;	
});


$('#scanned_remark_dlg').dialog({
	autoOpen:false,
	modal:true,
	width:'450',
	height:'auto',
	autoResize:true,
	buttons:{
		'Process Partial Shipments' : function(){
			 if($('input[name="cnfrm_process_partial"]').attr('checked'))
			 {
				$('textarea[name="part_ps_manifesto"]').val($('textarea',this).val());
				if($.trim($('textarea[name="part_ps_manifesto"]').val()).length > 0)
				{	
				 	$('#update_shipflag').submit();
				}
			 }
			
			
		},
		'Cancel':function(){
			$(this).dialog('close');
		}
	}
	});

</script>
