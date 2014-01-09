<div class="container page_wrap">
	<div class="clearboth">
		<div class="fl_left" >
			<h2 class="page_title">Manage Vouchers</h2>
		</div>
		<div class="fl_right stats" >
			<div class="dash_bar_right">
				Total used : <span><?php echo $coupon_details['total_assigned'] ?></span>
			</div>
			<div class="dash_bar_right">
				Total alloted : <span><?php echo $coupon_details['total_alloted'] ?></span>
			</div>
			<div class="dash_bar_right">
				Total Voucher value : <span><?php echo $coupon_details['total_value'] ?></span>
			</div>
			<div class="dash_bar_right">
				Total Vouchers : <span><?php echo $coupon_details['total_coupons'] ?></span>
			</div>
		</div>
	</div>
	
	<div class="page_topbar" >
		<div class="page_action_buttonss fl_right" align="right">
			<a href="<?php echo site_url('admin/pnh_create_voucher');?>" class="button button-rounded button-flat-secondary button-small">Create voucher</a>
		</div>
	</div>
	<div style="clear:both">&nbsp;</div>
	<div class="page_content">
	<?php if($coupon_details['coupons_list'])
	{
		?>
		<table cellpadding="5" cellspacing="0" width="100%" class="datagrid">
			<thead>
				<tr>
					<th>#</th>
					<th>Date</th>
					<th>Value</th>
					<th>Vouchers</th>
					<th>Action</th>
					<th>Created by</th>
				</tr>
			</thead>
			<tbody>
				<?php 
					foreach($coupon_details['coupons_list'] as $i=>$coupon)
					{
						?>
						<tr>
							<td><?php echo $i+1; ?></td>
							<td><?php echo format_datetime($coupon['created_on']); ?></td>
							<td><?php echo $coupon['value']; ?></td>
							<td><a href="javascript:void(0)" class="view_vouvhers" group_id="<?php echo $coupon['group_code']; ?>">view</a></td>
							<td><button class="download_vouchers" group_id="<?php echo $coupon['group_code']; ?>">download</button></td>
							<td><?php echo $coupon['name']; ?></td>
						</tr>
						
				<?php 	
					}?>
						<tr class="pagination">
							<td align="center" colspan="9" class="pagination"><?php echo $coupon_details['pagination']; ?></td>
						</tr>
				</tbody>
		</table>
	<?php 	
		}else{
				echo '<div align="center"><b>No coupons are found</b></div>';
			}
	?>
	</div>
</div>

<!-- Add coupon modal -->
<?php 
	$vouchers=$this->db->query("select * from pnh_m_voucher order by denomination asc")->result_array();
?>
<div id="add_counpon_dlg" title="Add Voucher">
	<form action="<?php echo site_url('admin/pnh_add_voucher')?>" method="post" id="add_coupon_form">
		<table cellpadding="5" cellspacing="0" class="datagrid">
			<tbody>
				<tr>
					<td>Voucher : </td>
					<td>
						<select name="voucher">
						<option value=''>Choose</option>
						<?php if($vouchers){
						foreach($vouchers as $voucher)
						{?>
							
						<option value="<?php echo $voucher['voucher_id'].'|'.$voucher['denomination'] ?>"><?php echo $voucher['voucher_name']; ?></option>
					<?php }
						}?>
						</select>
					</td>
				</tr>
				<tr>
					<td>Voucher qty : </td>
					<td><input type="text" name="coupon_qty"></td>
				</tr>
			</tbody>
		</table>
		
	</form>
</div>
<!-- Add coupon modal end-->

<!-- download voucher -->
<div id="download_voucher_dlg" title="Download voucher ">
	<form action="<?php echo site_url('/admin/pnh_download_vouchers') ?>" method="post" id="download_voucher_form">
		<input type="hidden" name="group_id">
	</form>
</div>
<!-- download voucher end-->

<!-- voucherslist modal -->
<div id="vouchers_by_group" title="Vouchers list">
</div>
<!-- voucherslist end -->

<script>
$("#add_coupons").click(function(){
	$("#add_counpon_dlg").data({}).dialog('open');
});	

$("#add_counpon_dlg").dialog({
	autoOpen:false,
	modal:true,
	height:'210',
	width:'320',
	autoResize:true,
	open:function(){
		$("input[name='coupon_value']").val('');
		$("input[name='coupon_qty']").val('');
		},
	buttons:{
		'Create' : function(){
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

$("#add_coupon_form").submit(function(){
	var coupon_qty=$("input[name='coupon_qty']",this).val();
	var vouchr=$("select[name='voucher']",this).val();

	if(vouchr.length==0)
	{
		alert("Please select Voucher");
		return false;
	}
	
	if(coupon_qty.length==0)
	{
		alert("All the fields are required");
		return false;
	}

	if(isNaN(coupon_qty))
	{
		alert("Invalid data");
		return false;
	}

	return true;
});

//download vouchers
$("#download_voucher").click(function(){
	$("#download_voucher_dlg").data({}).dialog('open');
});

$("#download_voucher_dlg").dialog({
	autoOpen:false,
	modal:true,
	width:'450',
	autoResize:true,
	open:function(){
		var html_cnt='';
		$.post(site_url+'/admin/jx_get_fresh_vouchers_denomination',{},function(res){
			html_cnt+="<table width='100%' cellpadding='5' cellspacing='0' class='datagrid' style='text-align:center;'><thead><tr><th>Voucher vlaue</th><th>Qty</th><th>Require qty</th></tr></thead><tbody>";
			
			$.each(res.fresh_vouchers,function(a,b){
				html_cnt+="<tr>";
				html_cnt+="		<td>"+b.denomination+"<input type='hidden' name='denomination' value='"+b.denomination+"'></td>";
				html_cnt+="		<td> "+b.ttl+"</td>";
				html_cnt+="		<td><input type='text' name='require_qty[]' class='require_qty' size='10' value='0'></td>";	
				html_cnt+="</tr>";				
				});
			html_cnt+="</tbody></table>";
			$("#download_voucher_form").html(html_cnt);
			
		},'json');
		
		},
	buttons:{
		'download' : function(){
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

/*$("#download_voucher_form").submit(function(){
	var is_empty=0;
	var is_zero;
	var is_nan=0;

	$.each($(".require_qty",this),function(){
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
	
});*/

//-------new script--------------
$(".view_vouvhers").click(function(){
	var group_code=$(this).attr('group_id');
	$("#vouchers_by_group").data({'group_code':group_code}).dialog('open');
	
})

$("#vouchers_by_group").dialog({
	autoOpen:false,
	modal:true,
	width:'450',
	height:'400',
	autoResize:true,
	open:function(){
		var html_cnt='';
		var tota1=0;
		$.post(site_url+'/admin/jx_get_vouchers_by_group',{group_id:$(this).data('group_code')},function(res){
			html_cnt='<table width="100%" cellpadding="5" cellspacing="0" class="datagrid"><thead><tr><th>#</th><th>Voucher Name</th><th>Value</th></tr></thead><tbody>';
			$.each(res.vouchers_list,function(a,b){
				html_cnt+="<tr>";
				html_cnt+="		<td>"+(a+1)+"</td>";
				html_cnt+="		<td>"+b.voucher_name+"</td>";
				html_cnt+="		<td>"+b.denomination+"</td>";
				html_cnt+="</tr>";	
				tota1=(tota1*1+b.denomination*1);
			});
			html_cnt+="<tr><td colspan='2'><br>Total value :</b> </td><td>"+tota1+"</td></tr>";
			html_cnt+="</tbody></table>";
			$("#vouchers_by_group").html(html_cnt);	
			
		},'json');	
		
		},
		buttons:{
		'Close':function(){
			$(this).dialog('close');
		}
	}
});

$(".download_vouchers").click(function(){
	var group_id=$(this).attr('group_id');
	$("#download_voucher_form input[name='group_id']").val(group_id);
	$('#download_voucher_form').submit();
	
	
});
</script>