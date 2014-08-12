<style>
	.response_block { padding:0px;color:#008000; }
	.filters_block { margin-top:0px; float:right; margin-bottom:8px;}
    .filters_block .filter { float: left; padding: 0px 8px; }
    .filters_block .filter select { width:180px;margin-top:5px;font-size: 12px; }
	.filters_block b {
     	font-size: 13px;
     	margin:5px;
	}
	#date_from,#date_to {
	   width:80px;
	   font-size: 11px;
	}
	.date_filter_submit {
	   font-size: 11px !important;
	   height: 18px !important;
	   line-height: 14.4px !important;
	   padding: 0;
	}
	.block_notify {
		background: #95DB95;padding:5px 5px 6px;border-radius:3px; width: auto; float: left;margin-top: 7px;
		color: black;font-size: 12px;
    }
	a {color: #000000; }
</style>
<div class="container">
	<h2>Reverse Reconciled Invoice</h2>
	<div class="response_block"></div>
	<div class="fl_right">
		<input type='submit' class="reset_filters" class='button button-tiny button-circle' value="Reset Filters">
	</div>
	<form id="invs" method="post">
		<table style="font-size:13px;background:#fff;padding:10px;margin:10px;" cellpadding=5>
		<tr>
			<td>Enter Invoice Number <span class="required"><b>*</b></span>: </td>
			<td>Remarks <span class="required"><b>*</b></span>: </td>
			<td></td>
		</tr>
		<tr>
			<td><input type="text" class="inp" name="invoice_no" id='invoice_no' value="" placeholder='Enter invoice number to reverse' style="width:200px;height: 30px;"></td>
			<td><textarea class="inp" name="remarks" id='remarks' rows="7" cols="18" style="width:350px;height: 30px;" placeholder="Enter remarks or reason to reverse"></textarea></td>
			<td><input type="submit" value="Reverse Reconcilation" class="button button-action button-pill fl_right"></td>
		</tr>
		</table>
	</form>
	<!--<hr/>-->
	<div class="clear">&nbsp;</div>
	<!-- REVERSE INVOICE LOG STARTS -->
	<h3>View Reconciled Invoices Log: </h3>
	<div class="invoice_log_block"></div>
	<!-- REVERS INVOICE LOG ENDS -->
</div>
<script>
function get_url() {
		var elt = $( ".invoice_log_block" ).attr("id");
        var from=$('.date_from',elt).val();
		var to=$('.date_to',elt).val();
		var srh_invoice_no=$('.srh_invoice_no',elt).val();
		
        
        if(from=='' || from==undefined) from=0;
        if(to=='' || to==undefined) to=0;
        if(srh_invoice_no=='' || srh_invoice_no==undefined) srh_invoice_no=0;
        
        var filter_url = from+"/"+to+"/"+srh_invoice_no;
//        print(filter_url);return false;
        return filter_url;
}

function load_invoice_log()
{
	var filter_url = get_url();

	$(".invoice_log_block").html('<div class="loading">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Loading...</div>');
	$.post(site_url+"/admin/reverse_reconciled_invoice_log_jx/"+filter_url+"/0",{},function(rdata) {
		$(".invoice_log_block").html(rdata);
	});
}

$(function(){
	
	load_invoice_log();
	
	//================< ONSUBMIT THE FORM CODE STARTS >===========================
	$("#invs").submit(function(){
		$(".response_block").css({"color":"#008000"}).slideDown();
		ef=true;
		$("input,textarea",$(this)).each(function(){
			if(!is_required($(this).val())) {
				ef=false; resp_cont = 'Error: <span class="required"><b>*</b></span> marked input fields are required';
				$(".response_block").html(resp_cont).delay(5000).slideUp("slow");
				return false;
			}
		});
		if(ef === false) { return ef; }
		
		var invoice_no = $("#invoice_no").val();
		var remarks = $("#remarks").val();
		$.post(site_url+"/admin/reverse_reconciled_invoice_jx",{invoice_no:invoice_no,remarks:remarks},function(rdata) {
				if(rdata.status == 'success') {
					resp_cont = (rdata.msg);
					$(".response_block").html(resp_cont).delay(5000).slideUp("slow");
					$("#invs").clearForm();
					load_invoice_log();
				}
				else {
					resp_cont = (rdata.msg);
					$(".response_block").css({"color":"red"}).html(resp_cont).delay(5000).slideUp("slow");
					$("#invs").clearForm();
				}
		},"json");
		return false;
	});
	//================< ONSUBMIT THE FORM CODE ENDS >===========================
	
	//========================= PAGINATION CODE STARTS =========================
	$(".log_pagination a").live("click",function(e) {
		e.preventDefault();
		//load_invoice_log();
	
		//$(".page_num").val=pg;
		$(".invoice_log_block").html('<div class="loading">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Loading...</div>');
		$.post($(this).attr("href"),{},function(rdata) {
			$(".invoice_log_block").html(rdata);
		});
		return false;
	});
	//========================= PAGINATION CODE ENDS =========================
	
	$(".date_filter_submit").live("click",function(e){
            load_invoice_log();
    });
	
	$(".srh_invoice_no").live("change",function(e){
            load_invoice_log();
    });
	
	$(".reset_filters").live("click",function(e){
			var elt = $( ".invoice_log_block" ).attr("id");
			$('.date_from',elt).val("");
			$('.date_to',elt).val("");
			$('.srh_invoice_no',elt).val("");
            load_invoice_log();
    });
});
</script>
<?php
