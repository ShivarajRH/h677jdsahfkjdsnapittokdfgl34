<div class="page_wrap container">
	<div class="clearboth">
		<div class="fl_left" >
			<h2 class="page_title">Stock in Voucher books</h2>
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
			<form id="scan_bookslno" method="post" onsubmit="go_scan();return false;">
			<table width="100%">
				<tbody>
					<tr>
						<td>
							<input class="inp" id="bookslno" style="padding:5px;width: 300px;">
							<input type="submit" value="submit" style="padding:5px;">
						</td>
					</tr>
				</tbody>
			</table>
			</form>
		</div>
		
		<form id="outform" method="post">
			<input type="hidden" name="bookslno" class="bookslno">
		</form>
		<br>
		<h3 >
			<span class="stock_intaked_ttl scan_res" >Stock in take <b id="outscan_ttl">0</b> </span>
			<span class="book_not_found_ttl scan_res" >Book not found  <b >0</b> </span>
			<span class="stock_not_intaked_ttl scan_res" >Stock not in taked<b >0</b> </span>
			<span class="bookslno_alexist_ttl scan_res" >Book slno already exist<b >0</b> </span>
			=
			<span class="scanned_ttl scan_res" > Scanned <b id="count" style="font-weight: normal;font-size: 42px;">0</b></span>	
		</h3>
		<div id="frames_cont"></div>
	</div>
</div>

<script>
var fno=1;
function go_scan()
{
	if($("#bookslno").val().length==0)
	{
		return false;
	}
	
	$("#outform .bookslno").val($("#bookslno").val());
	$("#count").text(fno);
	fno++;
	$.post(site_url+'/admin/pnh_process_stock_intake_books',$('#outform').serialize(),function(resp){
		$("#frames_cont").prepend('<div class="scan_output">'+resp+'</div>');
		build_outscan_summary();
	});
}

function build_outscan_summary()
{
	var outscan_summ_flags = new Array('book_not_found','stock_not_intaked','stock_intaked','bookslno_alexist');
		$.each(outscan_summ_flags,function(a,b){
			$('.'+b+'_ttl b').text($('#frames_cont .'+b).length);
		});

		
}
</script>



<style>
.scan_res{padding:5px 10px;color: #FFF;font-size: 13px;margin:3px;display: inline-block;min-width: 100px;text-align: center;} 
.scan_res b{font-weight: bold;font-size: 22px;padding:5px;display: block;text-align: center;} 
.scanned_ttl{background: #ffffa0;color: #000;}
.stock_intaked_ttl{background: #f1f1f1 !important;color: #000 !important;}
.already_scaned_ttl{background: green;color:#000000}
.book_not_found_ttl{background: #cd0000;}
.bookslno_alexist_ttl{background: purple;}
.stock_not_intaked_ttl{background: orange;}
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