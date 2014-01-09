<div class="container page_wrap">
	<div class="clearboth">
		<div class="fl_left" >
			<h2 class="page_title">Create Vouchers</h2>
		</div>
		<div class="fl_right" >
			<a href="<?php echo site_url('admin/pnh_manage_vouchers');?>" class="button button-rounded button-flat-secondary button-small">Vouchers list</a>
		</div>
	</div>
	
	<div class="page_topbar" >
		<div class="page_action_buttons fl_right" align="right">
			
		</div>
	</div>
	<div style="clear:both">&nbsp;</div>
	
	<div class="page_content">
		<form action="<?php echo site_url('/admin/pnh_add_voucher'); ?>" method="post" id="create_voucher_form">
			<table cellpadding="5" cellspacing="0" class="datagrid">
				<thead>
					<tr>
						<th>Voucher</th>
						<th></th>
						<th>Require qty</th>
						<th></th>
						<th>Total(Rs)</th>
					</tr>
					
				</thead>
				<tbody>
					<?php if($vouchers_list){
						foreach($vouchers_list as $list)
						{
					?>
						<tr>
							<td>
								<?php echo $list['voucher_name'];?>
								<input type="hidden" value="<?php echo $list['voucher_id']; ?>" name="voucher_id[]">
								<input type="hidden" value="<?php echo $list['denomination']; ?>" name="denomination[]" class="denomination">
							</td>
							<td>
								x
							</td>
							<td>
								<input type="text" name="require_qty[]" value="0" size="8" class="require_qty">
							</td>
							<td>
								=
							</td>
							<td>
								<input type="text" name="require_qty_total[]" value="0" size="8" class="require_qty_total"  readonly="readonly">
							</td>
						</tr>
					<?php } ?>
						<tr>
							<td colspan="3" align="center"><b>Total : </b></td>
							<td colspan="2" align="right"><input type="text" name="total" value="0" size="8" class="total" readonly="readonly" ></td>
						</tr>
						<tr>
							<td colspan="5" align="right"><input type="submit" value="create"></td>
						</tr>
					<?php }?>
				</tbody>
			</table>
		</form>
	</div>

</div>

<script>

$("#create_voucher_form").submit(function(){
	var is_empty=0;
	var is_zero;
	var is_nan=0;

	$.each($(".require_qty",this),function(){
		if($(this).val()=='')
			$(this).val(0);

		if($(this).val()!='')
			is_empty=1;
		
		if($(this).val()!=0)
			is_zero=1

		if(isNaN($(this).val()))
			is_nan=1;
				
	});

	
	if(!is_empty || !is_zero)
	{
		alert("Please enter require qty");
		return false;
	}

	if(is_nan)
	{
		alert("Require qty must be number");
		return false;
	}
	return true;
	
});

$(".require_qty").keyup(function(){
	var $tr = $(this).parents("tr");
	var voucher_val=$tr.find("td").eq(0).find("input.denomination").val();
	var qty=$(this).val();
	var voucher_total_val=$tr.find("td").eq(4).find("input.require_qty_total");
	var intRegex = /^\d+$/;
	var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
	var total_voucher_val=0;
	var vouchers_total=0;
	
	if(isNaN(qty))
	{
		alert('Please enter numeric value');
		$(this).val(0);
		return false;
	}

	if(intRegex.test(qty) || floatRegex.test(qty)) {
		total_voucher_val=(qty*1)*(voucher_val*1);
		voucher_total_val.val(total_voucher_val);

		$("input.require_qty_total").each(function(){
			if(isNaN($(this).val()))
			{
				$(this).val(0);	
			}else{
				vouchers_total=(vouchers_total*1)+($(this).val()*1);
			}
		});
		
		$(".total").val(vouchers_total);

	}else{
		voucher_total_val.val(0);

		$("input.require_qty_total").each(function(){
			if(isNaN($(this).val()))
			{
				$(this).val(0);	
			}else{
				vouchers_total=(vouchers_total*1)+($(this).val()*1);
			}
		});
		
		$(".total").val(vouchers_total);
		
	}
	
	
});
</script>