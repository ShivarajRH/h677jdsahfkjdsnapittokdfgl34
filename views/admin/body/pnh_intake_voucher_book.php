<div class="container page_wrap">
	<div class="clearboth">
		<div class="fl_left" >
			<h2 class="page_title">Create voucher book</h2>
		</div>
		
		<div class="fl_right stats" >
			<a href="<?php echo site_url('admin/pnh_voucher_book') ?>" class="button" target="_blank" >List Books</a>&nbsp;
		</div>
	</div>
	 
	<div style="clear:both">&nbsp;</div>
	<div class="page_content">
		<form action="<?php echo site_url('/admin/pnh_process_create_book') ?>" method="post" id="create_book_form">
			<div>
				<table width="100%" cellpadding="5" cellspacing="0">
					<tr>
						<td width="400" valign="top">
							<table  cellpadding="5" cellspacing="0" class="datagrid">
								<tr>
									<td width="100"><b>Book SerialNo :</b> </td>
									<td width="300"><input type="text" name="book_serialid" ></td>
								</tr>
								<tr>
									<td><b>Book Type:</b></td>
									<td>
										<select name="book_type" class="book_types">
											<option value=''>Choose</option>
											<?php foreach($book_types as $type){ ?>
												<option value="<?php echo $type['book_template_id']; ?>"><?php echo $type['book_type_name']; ?></option>
											<?php }?>
										</select>
									</td>
								</tr>
								<tr>
									<td valign="top" colspan="2" align="left" width="100%" style="display:none;">
										<div id="book_type_denomination" ></div>
									</td>
								</tr>
							</table>
						</td>
						<td>
							<div>
								<b>Scan Voucher Serial no : </b> 
								<input class="voucher_sino" id="scan_voucher_sino" style="padding:5px;width:200px;">
								<input type="button" value="Scan" onclick='go_slno_scan()' style="padding:2px 10px;">
							</div>
							<div id="voucher_scan_summary">
								<table cellpadding="5" cellspacing="0" class="datagrid" width="100%">
									<thead>
										<tr>
											<th>#</th>
											<th>Voucher Serial no</th>
											<th style="text-align: right">Denomination</th>
										</tr>
									</thead>
									<tbody>
									
									</tbody>
									<tfoot>
										<tr>
											<td><b>Total : </b></td>
											<td align="right" colspan="2" style="text-align: right"><input disabled="disabled" type="text" value="0" class="scanned_voucher_total" style="border:none;text-align: right" readonly="readonly"></td>
										</tr>
									</tfoot>
								</table>
							</div>
					</td>
					</tr>
					<tr>
						<td colspan="2" align="right" class="submit_blk">
							<input type="submit"  value="Create Book">
						</td>
					</tr>
					</table>
					
				</div>
				
				
			</form>	
				
		</div>
</div>

<form id="outform" method="post">
	<input type="hidden" name="book_temp_id" value="">
	<input type="hidden" name="voucher_serial_no" value="" class="voucher_serial_no">
</form>



<script>
$(".book_types").change(function(){
	var temp_id=$(this).val();
	var html_cnt='';

	if($("#voucher_scan_summary table tbody tr").length > 0)
	{
		if(confirm("Do you want to clear"))
		{
			$("#voucher_scan_summary table tbody").html('');
			sino_list=[];
		}else{
			return false;
		}
	}
	
	if(temp_id=='')
		return false;
	$("#book_type_denomination").html('');
	$(".submit").remove();
	
	$.post(site_url+'/admin/jx_get_template_denomination',{temp_id:temp_id},function(res){
		html_cnt+="<table align='center' cellpadding='5' width='100%' cellspacing='0' class='datagrid'><thead><tr><th>#</th><th>Denomination</th><th>Qty</th><th>Scanned</th></tr></thead><tbody>";
		$.each(res.template_denomination,function(a,b){
			
			html_cnt+="<tr>";
			html_cnt+="		<td>"+(a+1)+"</td>";
			html_cnt+="		<td><input type='hidden' value='"+b.denomination+"' class='denomination'>"+b.denomination+"</td>";
			html_cnt+="		<td><input type='hidden' value='"+b.no_of_voucher+"' class='no_of_voucher'>"+b.no_of_voucher+"</td>";
			html_cnt+="		<td><input type='text' size='5' value='0' class='scanned_qty scanned_qty_"+b.denomination+"' readonly='readonly'></td>";	
			html_cnt+="</tr>";	
		});
		html_cnt+='<tr>';
		html_cnt+='		<td colspan="2">Total :</td>';
		html_cnt+='		<td><input type="hidden" value="'+res.template_details.value+'" class="template_total_value">'+res.template_details.value+'</td>';
		html_cnt+='		<td><input type="text" class="scanned_total_value" size="5" value="0" readonly="readonly"></td>';	
		html_cnt+='</tr>';
		$("#book_type_denomination").append(html_cnt);
		$("#book_type_denomination").closest("td").show();
	},'json');
});	

var sino_list= new Array();
var is_oddvalue_book=0;
$(".book_types").val('');
$(".submit").remove();

function go_slno_scan()
{
	$("#scan_voucher_sino").select().focus();
	
	if($("#scan_voucher_sino").val().length==0)
	{
		return false;
	}
	
	var book_serial_number=$("input[name='book_serialid']").val();
	var book_type=$("select[name='book_type']").val();
	var html_cnt='';
	var row_count=$("#voucher_scan_summary table tbody tr").length;
	$(".submit").remove();
	
	if(book_serial_number.length==0)
	{
		alert("Please enter book serial number");
		return false;
	}

	if(book_type.length==0)
	{
		alert("Please select book type");
		return false;
	}
	
	if($.inArray($("#scan_voucher_sino").val(), sino_list)!=-1)
	{
		alert('This voucher already scanned');
		return false;
	}
	
	$("#outform .voucher_serial_no").val($("#scan_voucher_sino").val());
	$.post(site_url+'/admin/jx_scan_voucher_slno',$('#outform').serialize(),function(resp){

		if(resp.status=='error')
		{
			alert(resp.msg);
		}else if(resp.status=='success')
		{
			html_cnt+="<tr>";
			html_cnt+="		<td>"+(row_count+1)+"</td>";
			html_cnt+="		<td> <input type='hidden' value='"+resp.data.voucher_serial_no+"' name='voucher_serial_number[]'>"+resp.data.voucher_serial_no+"</td>";
			html_cnt+="		<td align='right'> <input type='hidden' value='"+resp.data.value+"' class='scanned_denomination'>"+resp.data.value+"</td>";
			html_cnt+="</tr>";

			$("#voucher_scan_summary table tbody").append(html_cnt);
			sino_list.push($("#scan_voucher_sino").val());
			$(".scanned_qty_"+resp.data.value).val(($(".scanned_qty_"+resp.data.value).val()*1+1*1));

			//compute the template value in left side
			var total_tem_value=0;
			$.each($("#book_type_denomination table tbody tr"),function(){
				if($('.denomination',this).length)
				{
					var row_value=($('.denomination',this).val()*1)*($('.scanned_qty',this).val()*1);
					total_tem_value=(total_tem_value+row_value);
				}
			})

			$(".scanned_total_value").val(total_tem_value);

			//compute the scanned vouchers total
			var scanned_vouchers_total=0;
			$.each($("#voucher_scan_summary table tbody tr"),function(){
				if($(".scanned_denomination",this).length)
				{
					scanned_vouchers_total=(scanned_vouchers_total*1+$(".scanned_denomination",this).val()*1);
				}
			});

			//$(".scanned_voucher_total").val(total_tem_value);
			$(".scanned_voucher_total").val(scanned_vouchers_total);
		}
		
	},'json');
}

$("#scan_voucher_sino").keypress(function(e) {
    if(e.which == 13) {
    	go_slno_scan();
        return false;
    }
});

$("#create_book_form").submit(function(){
	var book_serial_number=$("input[name='book_serialid']").val();
	var book_type=$("select[name='book_type']").val();
	var scanned_vouchers=$("#voucher_scan_summary table tbody tr input[name='voucher_serial_number[]']").length;
	var scanned_vouchers_total=$(".scanned_voucher_total").val();
	var template_total_val=$(".template_total_value").val();
	var is_odd_value_book=0;
	
	

	if(book_serial_number.length==0)
	{
		alert("Please enter book serial number");
		return false;
	}

	if(book_type=='')
	{
		alert("Please select Book type");
		return false;
	}

	if(scanned_vouchers==0)
	{
		alert("Please scann vouchers");
		return false;
	}

	$.each($("#book_type_denomination table tbody tr"),function(){
		if($(".no_of_voucher",this).length)
		{
			if($(".no_of_voucher",this).val()!=$(".scanned_qty",this).val())
			{
				is_odd_value_book=1;
			}
		}
	});

	if(scanned_vouchers_total!=template_total_val)
		is_odd_value_book=1;

	if(!is_odd_value_book)
		$(".submit").remove();
	
	if(is_odd_value_book && $(".submit").length==0)
	{
		var html_cnt='<span class="submit">This is odd value book : <input type="checkbox" name="is_oddvalue_book"></span> ';
		$(".submit_blk").prepend(html_cnt);
		alert("This is a odd value book do you want to process");
		return false;
	}

	if($(".submit").length > 0)
	{
		if($("input[name='is_oddvalue_book']").is(":checked"))
		{
			return true;
		}else{
			alert("This is a odd value book please confirm to process");
			return false;
		}
	}
	return true;
});

</script>

<style>
#book_type_denomination{background: #fcfcfc;}
#book_type_denomination td{background: #FFF !important;}
</style>