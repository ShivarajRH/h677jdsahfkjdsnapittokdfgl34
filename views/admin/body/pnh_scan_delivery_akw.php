<div class="page_wrap container">
	<div class="clearboth">
		<div class="fl_left" >
			<h2 class="page_title">Scan delivery acknowledgement</h2>
		</div>
		<div class="fl_right stats" ></div>
	</div>
	<div class="page_topbar">
		<div class="page_topbar_left fl_left" ></div>
		<div class="page_action_buttonss fl_right" align="right"></div>
	</div>
	<div style="clear:both">&nbsp;</div>
	
	<div class="page_content">
		<div  style="padding:0px;">
			<form id="scan_inv" method="post" onsubmit="go_akwscan();return false;">
			<table width="100%">
				<tbody>
					<tr>
						<td>
							<input class="inp" id="scan_invoice" style="padding:5px;width: 300px;">
							<input type="submit" value="submit" style="padding:5px;">
						</td>
					</tr>
				</tbody>
			</table>
			</form>
		</div>
		
		<form id="outform" method="post">
			<input type="hidden" name="invoice_no" class="invoice_no">
		</form>
		
		<br>
		<h3 >
			<span class="awkscanned_ttl scan_res" >Acknowleged <b id="outscan_ttl">0</b> </span>
			<span class="already_scaned_ttl scan_res" >Already Acknowleged <b >0</b> </span>
			<span class="invoice_not_found_ttl scan_res" >Invoice not found  <b >0</b> </span>
			<span class="invoice_not_shipped_ttl scan_res" >Invoice not shipped<b >0</b> </span>
			<span class="invoice_not_delivered_ttl scan_res" >Invoice not delivered<b >0</b> </span>
			=
			<span class="scanned_ttl scan_res" > Scanned <b id="count" style="font-weight: normal;font-size: 42px;">0</b></span>	
		</h3>
		<div id="frames_cont"></div>
	</div>
</div>

<script>
var fno=1;
function go_akwscan()
{
	if($("#scan_invoice").val().length==0)
	{
		return false;
	}

	$("#outform .invoice_no").val($("#scan_invoice").val());
	$("#count").text(fno);
	fno++;
	$.post(site_url+'/admin/scan_delivery_akw',$('#outform').serialize(),function(resp){
		$("#frames_cont").prepend('<div class="scan_output">'+resp+'</div>');
		build_outscan_summary();
	});
}

function build_outscan_summary()
{
	var outscan_summ_flags = new Array('invoice_not_found','invoice_not_delivered','invoice_not_shipped','awkscanned');
		$.each(outscan_summ_flags,function(a,b){
			$('.'+b+'_ttl b').text($('#frames_cont .'+b).length);
		});

		
}
</script>

<style>
.scan_res{padding:5px 10px;color: #FFF;font-size: 13px;margin:3px;display: inline-block;min-width: 100px;text-align: center;} 
.scan_res b{font-weight: bold;font-size: 22px;padding:5px;display: block;text-align: center;} 
.scanned_ttl{background: #ffffa0;color: #000;}
.awkscanned_ttl{background: #f1f1f1 !important;color: #000 !important;}
.already_scaned_ttl{background: green;color:#000000}
.invoice_not_found_ttl{background: #cd0000;}
.invoice_not_shipped_ttl{background: purple;}
.invoice_not_delivered_ttl{background: orange;}
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

</style>