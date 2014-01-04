<div class="container page_wrap">
	<div class="clearboth">
		<div class="fl_left" >
			<h2 class="page_title">Create voucher book</h2>
		</div>
		<div class="fl_right" >
			<a href="<?php echo site_url('admin/pnh_voucher_book') ?>" target="_blank" class="button button-rounded button-flat-secondary button-small">Voucher book list</a>&nbsp;
		</div>
	</div>
	<div style="clear:both">&nbsp;</div>
	
	<div class="page_content">
		<form action="<?php echo site_url('admin/pnh_book_create_process'); ?>" id="create_book_form" method="post">
		
			<table class="datagrid" cellpadding="5" cellspacing="0">
				<tbody>
					<tr>
						<td>Book template<span class="red_star">*</span> : </td>
						<td>
							<select name="book_templates" id="book_templates">
								<option value="">choose</option>
								<?php if($book_template)
								{
									foreach($book_template as $bt)
									{
									?>
									<option value="<?php echo $bt['book_template_id']; ?>" bt_value="<?php echo $bt['value']; ?>"><?php echo $bt['book_type_name']; ?></option>
								<?php 
									}
								}
									?>
							</select>
						</td>
					</tr>
					<tr id="bt_denominations" style="dispaly:none;">
					</tr>
					<tr id="bt_value" style="dispaly:none;">
					</tr>
					<tr id="book_menu">
					</tr>
					<tr>
						<td>Qty<span class="red_star">*</span> : </td>
						<td><input type="text" name="require_qty" size="5"></td>
					</tr>
					<tr>
						<td colspan="2" align="right"><input type="submit" value="create"></td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
</div>

<script>
	$('#choose_menu').chosen();
	$(".chzn-select").chosen({no_results_text: "No results matched"});
								
	$("#book_templates").change(function(){
		var book_value=$("option:selected",this).attr("bt_value");
		var book_temp_id=$(this).val();
		var html_cnt='';
		var html_cnt2='';
		var html_cnt3='';
		var total=0;
		$("#bt_value").html('');
		$("#bt_denominations").html('');
	
		if(book_temp_id)
		{
			html_cnt+="<td>Book template value : </td><td>"+book_value+"</td>";
			$("#bt_value").html(html_cnt);
	
			$.post(site_url+"/admin/jx_get_template_denomination",{temp_id:book_temp_id},function(res){
				html_cnt2+='<td>Book denomination : </td><td><table class="datagrid" cellpadding="5" cellspacing="0"><thead><tr><th>#</th><th>Voucher</th><th>Value(Rs)</th><th></th><th>Qty</th><th></th><th>Total</th></tr></thead><tbody>';
				$.each(res.template_denomination,function(a,b){
					html_cnt2+="<tr>";
					html_cnt2+="	<td>"+(a+1)+"</td>";
					html_cnt2+="	<td>"+b.voucher_name+"</td>";
					html_cnt2+="	<td>"+b.denomination+"</td>";
					html_cnt2+="	<td>*</td>";
					html_cnt2+="	<td>"+b.no_of_voucher+"</td>";
					html_cnt2+="	<td>=</td>";
					html_cnt2+="	<td>"+(b.denomination*1)*(b.no_of_voucher*1)+"</td>";
					html_cnt2+="</tr>";
					total=(total*1)+(b.denomination*1)*(b.no_of_voucher*1);
						});
					html_cnt2+="<tr>";
					html_cnt2+="	<td colspan='6' align='center'><b>Total : </b></td>";
					html_cnt2+="	<td aling='right'>"+total+"</td>";
					html_cnt2+="<tr>";
					html_cnt2+"</td>";
				
					html_cnt3+="  <td>Menu : </td>";
					html_cnt3+="  <td>"; 
				$.each(res.menus,function(a,b){
					html_cnt3+=b.name+"<br>";
					});
					html_cnt3+=" 	 </td>"; 
					
				
				$("#bt_denominations").html(html_cnt2);
				$("#book_menu").html(html_cnt3);
				
				},'json');	
		}
	});

	$("#create_book_form").submit(function(){
		var book_tem=$("select[name='book_templates']",this).val();
		var book_qty=$("input[name='require_qty']",this).val();
		

		if(book_tem.length==0)
		{
			alert("Please choose book template");
			return false;
		}

		if(book_qty.length==0)
		{
			alert("Please enter book require qty");
			return false;
		}

		if(isNaN(book_qty))
		{
			alert("Book qty must be numeric");
			return false;
		}

		if(confirm("Do you want to create this book "))
			return true;
		else
			return false;
		return true;
	});								
</script>