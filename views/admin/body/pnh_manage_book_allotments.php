<div class="container page_wrap">
	<div class="clearboth">
		<div class="fl_left" >
			<h2 class="page_title">Manage Book allotments</h2>
		</div>
		<div class="fl_right stats" >
			<div class="dash_bar_right">
				Total alloted books : <span><?php echo $total_records; ?></span>
			</div>
		</div>
	</div>
	
	<div class="page_topbar" >
		<div class="page_action_buttons fl_right" align="right">
			<form method="post" id="filter_form"> 
				Search : <input type="text" name="src">&nbsp;&nbsp;
				Status : <select name="status">
							<option value=" ">All</option>
							<option value="1">Pending</option>
							<option value="2">Activated</option>
						</select>
				<input type="submit" value="submit">
			</form>
		</div>
	</div>
	<div style="clear:both">&nbsp;</div>
	<div class="page_content">
	<?php 
		if($fran_book_link_det)
		{
	?>
		<table class="datagrid" cellpadding="5" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th width="20">#</th>
					<th width="80">Allotment Id</th>
					<th>Book name</th>
					<th>Book slno</th>
					<th>Franchise</th>
					<th>Amount(Rs)</th>
					<th>Purchase details</th>
					<th>Payment</th>
					<th>Receipt details</th>
				</tr>
			</thead>
			<tbody>
				<?php  
					foreach($fran_book_link_det as $i=>$det)
					{
				?>
				<tr>
					<td><?php echo $i+1;?></td>
					<td><?php echo $det['allotment_id']; ?></td>
					<td><?php echo $det['book_type_name']; ?></td>
					<td><?php echo $det['book_slno']; ?></td>
					<td><?php echo $det['franchise_name']; ?></td>
					<td><?php echo $det['franchise_value'];?></td>
					<td>
						<span >TransId : <a target="_blank" href="<?php echo site_url('admin/trans/'.$det['transid']);?>"><?php echo $det['transid']; ?></a></span><br>
						<span>Invoice : <a target="_blank" href="<?php echo site_url('admin/invoice/'.$det['invoice_no']);?>"><?php echo $det['invoice_no']; ?></a></span><br>
					</td>
					<td>
						<?php if($det['status']==2)
							 {  
							 	 echo 'Done';
							 }else 
							 { 
							 		echo 'Pending ';
							 		?>
								<a href="javascript:void(0)" class="update_reciept_dlg" allotment_id="<?php echo $det['allotment_id'];?>">Update</a>	 		
						<?php 		
							 }
							  ?>
					</td>
					<td><a href="javascript:void(0)" class="receipt_details" book_id="<?php echo $det['book_id']; ?>">View</a></td>
				</tr>		
				<?php	
					}
				?>
				<tr>
					<td colspan="8" align="right"><?php echo $pagination;?></td>
				</tr>
			</tbody>
		</table>
		<?php 
		}else{
			echo '<div align="center"><b>No data found</b></div>';
		}
		?>
	</div>
</div>

<!-- update reciept block -->
<div id="update_receipt" title="Update receipt">
	<form action="<?php echo site_url('admin/pnh_update_book_receipts')?>" id="update_reciept_form" method="post">
	</form>
</div>
<!-- update reciept block end-->

<!-- Receipt details -->
<div id="receipt_details_dlg" title="Receipt details"></div>
<!-- Receipt details -->

<script>
$(".update_reciept_dlg").click(function(){
	var allotment_id=$(this).attr('allotment_id');
	$("#update_receipt").data({'allotment_id':allotment_id}).dialog('open');
});

$("#update_receipt").dialog({
	autoOpen:false,
	modal:true,
	height:'350',
	width:'600',
	autoResize:true,
	open:function(){
		var html_cnt='';
		var adjust_value='';
		var adjust_value_pr='';
		var count=0;
		var complete_ttl=0;
		$("#update_reciept_form").html('');
		
		$.post(site_url+'/admin/jx_get_book_detby_allotment',{allotment_id:$(this).data('allotment_id')},function(res){
			html_cnt+="<table class='datagrid' cellpadding='5' cellspacing='0' width='100%'><thead><tr><th>#</th><th>Book name</th><th>Book value</th><th>Receipt id</th><th>Adjusment value</th><th>Balance</th></tr></thead><tbody>";
			$.each(res.book_det,function(a,b){
				adjust_value='';
				adjust_value_pr='';
				if((b.book_price*1)==(b.payed*1))
				{
					complete_ttl+=1;
					adjust_value=b.payed;	
					adjust_value_pr='disabled="disabled"'
				}
				html_cnt+="<tr>";
				html_cnt+="		<td>"+(a+1)+"</td>";
				html_cnt+="		<td>"+b.book_type_name+"<input type='hidden' name='book_ids[]' value='"+b.book_id+"' "+adjust_value_pr+"><input type='hidden' name='franchise_ids[]' value='"+b.franchise_id+"' "+adjust_value_pr+"><input type='hidden' name='allotments_ids[]' value='"+b.allotment_id+"' "+adjust_value_pr+"></td>";
				html_cnt+="		<td>"+b.book_value+"</td>";
				html_cnt+="		<td><input type='text' name='receipt_id[]' size='8' "+adjust_value_pr+"></td>";
				html_cnt+="		<td><input type='text' name='adjusted_value[]' size='8' value='"+adjust_value+"' "+adjust_value_pr+"></td>";
				html_cnt+="     <td>"+b.balance+"</td>";
				html_cnt+="</tr>";
				count+=1;
			});
			html_cnt+="</tbody></table>";
			$("#update_reciept_form").html(html_cnt);

			if(count==complete_ttl)
			{
				$("#update_receipt").dialog("option", "buttons", {});
			}

		},'json');
	},
	buttons:{
		'update' : function(){
			var c=confirm("Are you sure to  submit this details");
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

$("#update_reciept_form").submit(function(){
	var reciept_ids=$("input[name='receipt_id[]']");
	var adjusted_value=$("input[name='adjusted_value[]']");
	var have_value=0;
	var ad_is_integer=0;
	var have_receipt=0;
	var re_int=0;

	$.each(adjusted_value,function(){
		if($(this).val()!='' || $(this).val()!=0)
			have_value=1;
		
			
		if(isNaN($(this).val()))
			ad_is_integer=1;
	});

	$.each(reciept_ids,function(){
		if($(this).val()!='' || $(this).val()!=0)
			have_receipt=1;

		if(isNaN($(this).val()))
			re_int=1;
			
	});

	if(ad_is_integer)
	{
		alert("Amount must be integer");
		return false;
	}

	if(!have_value)
	{
		alert("Amount must be require");
		return false;
	}

	if(!have_receipt)
	{
		alert("Receipt must be require");
		return false;
	}

	if(re_int)
	{
		alert("Receipt must be integer");
		return false;
	}

	$.post(site_url+'/admin/pnh_update_book_receipts',$('#update_reciept_form').serialize(),function(resp){
		if(resp.status=='error')
		{
			alert(resp.msg);
			return false;
		}else if(resp.status=='success')
		{
			location.href=site_url+'/admin/pnh_manage_book_allotments';
		}
		},'json');
	return false;
});

$(".receipt_details").click(function(){
	var book_id=$(this).attr('book_id');
	$("#receipt_details_dlg").data({'book_id':book_id}).dialog('open');
});


$("#receipt_details_dlg").dialog({
	autoOpen:false,
	modal:true,
	height:'350',
	width:'600',
	autoResize:true,
	open:function(){
		$("#receipt_details_dlg").html('');
		var html_cnt='';
		$.post(site_url+'/admin/jx_get_book_receipt_link_det',{book_id:$(this).data('book_id')},function(resp){
			html_cnt+="<table width='100%' cellpadding='5' cellspacing='0' class='datagrid'><thead><tr><th>#</th><th>Book value</th><th>Receipt value</th><th>Receipt id</th><th>Adjusted value</th></tr></thead><tbody>";
			
			$.each(resp.receipt_det,function(a,b){
				html_cnt+="<tr>";
				html_cnt+="		<td>"+(a+1)+"</td>";
				html_cnt+="		<td>"+b.book_value+"</td>";
				html_cnt+="		<td>"+b.receipt_amount+"</td>";
				html_cnt+="		<td>"+b.receipt_id+"</td>";
				html_cnt+="		<td>"+b.adjusted_value+"</td>";
				html_cnt+="</tr>";
			});
			html_cnt+="</tbody></table>";
			$("#receipt_details_dlg").html(html_cnt);
		},'json');		
	},
	buttons:{
		'Close':function(){
			$(this).dialog('close');
		}
	}
	
});

$("#filter_form").submit(function(){
	var search_query=$("input[name='src']",this).val();
	var status=$("select[name='status']",this).val();

	if(!search_query)
		search_query=0;
	if(!status)
		status=0;
	
	location.href = site_url+'admin/pnh_manage_book_allotments/'+search_query+'/'+status;
	return false;
});
</script>

