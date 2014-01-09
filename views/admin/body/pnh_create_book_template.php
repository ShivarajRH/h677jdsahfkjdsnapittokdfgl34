<div class="container page_wrap">
	<div class="clearboth">
		<div class="fl_left">
			<h2 class="page_title">Create Book Template</h2>
		</div>
		<div class="fl_right" >
			<a href="<?php echo site_url('admin/pnh_book_template') ?>" target="_blank" class="button button-rounded button-flat-secondary button-small" >Book templates</a>
		</div>
	</div>
	
	<div class="page_topbar" >
		<div class="page_action_buttonss fl_right" align="right">
			 
		</div>
	</div>
	
	<div style="clear:both">&nbsp;</div>
	<div class="page_content">
		<form action="<?php echo site_url('admin/pnh_process_create_book_template')?>" method="post" id="create_coupon_book_form">
			<table class="datagrid" cellpadding="6" cellspacing="0">
				<tbody>
					<tr>
						<td>Template name<span class="red_star">*</span> :</td>
						<td>
							<input type="text" name="book_name" value="<?php echo set_value('book_name')?>"><span class="error"><?php echo form_error('book_name'); ?></span>
						</td>
					</tr>
					<tr>
						<td>Value<span class="red_star">*</span> : </td>
						<td>
							<input type="text" name="book_value" size="8" value="<?php echo set_value('book_value')?>"><span class="error"><?php echo form_error('book_value'); ?></span>
						</td>
					</tr>
					<tr>
						<td>Link product<span class="red_star">*</span> : </td>
						<td>
							Search : <input type="text" class="inp" size=60 id="po_search">
							<div id="po_prod_list" class="closeonclick"></div>
							<table id="pprods" width="500" class="datagrid smallheader" style="margin-top:10px;">
								<thead>
									<tr>
										<th>Product Name</th>
										<th>MRP</th>
										<th>Qty</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</td>
					</tr>
					<?php if($voucher_det){ ?>
					<tr><td colspan="2" align="center"><b>Configure denomination</b></td></tr>
					<tr>
						<td colspan="2">
							<table cellpadding="0" style="border-collapse: collapse;">
								<thead>
									<tr>
										<th>Voucher</th>
										<th></th>
										<th>Require qty</th>
										<th></th>
										<th>Total</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach($voucher_det as $i=>$voucher)
									{?>
									<tr>
										<td>
											<?php echo $voucher['voucher_name'];?>
											<input type="hidden" name="coupon_value[]" value="<?php echo $voucher['denomination']; ?>" class="coupon_value">
											<input type="hidden" name="voucher_id[]" value="<?php echo $voucher['voucher_id']; ?>">
										</td>
										<td>
											X
										</td>
										<td>
											<input type="text" name="need_qty[]" size="8" class="coupon_qty" value="0"><span class="error"><?php echo form_error('need_qty['.$i.']'); ?></span>
										</td>
										<td>
											=
										</td>
										<td>
											<input type="text" name="coupon_total_value" value="0" size="10" class="coupon_total_value" readonly="readonly">
										</td>
									</tr>
									<?php }?>
									<tr>
										<td colspan="4">
											<b>Total :</b>
										</td>
										<td  align="right"><input type="text" name="coupons_total_value" size="10" readonly="readonly" class="coupons_total_value"><span class="error"><?php echo form_error('_validate_denomination_link'); ?></span></td>
									</tr>	
								</tbody>
							</table>
						</td>
					</tr>
					<?php 
						}else{ ?>
							<tr><td colspan="2" align="center" style="color:red;">No Voucher are available please create a coupon</td></tr>
					<?php }?>
					
					<tr>
						<td>Menu<span class="red_star">*</span> : </td>
						<td>
							<select name="menu_list[]" class='chzn-select' multiple='multiple' id="choose_menu">
								<?php if($menu_list)
								{
									foreach($menu_list as $menu)
									{
									?>
									<option value="<?php echo $menu['id'];?>"><?php echo $menu['name'];?></option>
								<?php
									}
								 }
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="right" colspan="2"><input type="submit" value="create"></td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
</div>


<div style="display:none" id="p_clone_template">
	<table>
		<tbody>
			<tr>
				<td><input type="hidden" class="p_pids" name="pid[]" value="%pid%">%name%</td>
				<td>%mrp%</td>
				<td><input type="text" class="inp" size=3 name="qty[]" value="1"></td>
				<td class="added_ped_remove"></td>
			</tr>
		</tbody>
	</table>
</div>

<script>
$('#choose_menu').chosen();
$(".chzn-select").chosen({no_results_text: "No results matched"});
								
$("#create_coupon_book_form").submit(function(){
	var book_name=$("input[name='book_name']").val();
	var book_value=$("input[name='book_value']").val();
	var check_num=0;
	var is_emty=0;
	var check_gty_val=0;
	var denomination_total=$(".coupons_total_value").val();
	
	if(book_name.length==0 || book_value.length==0)
	{
		alert('All the fields are rquired');
		return false;
	}

	if(isNaN(book_value))
	{
		alert("Value must be number");
		return false;
	}

	if($("input[name='need_qty[]']").length==0)
	{
		alert("No coupons are available please create coupon");
		return false;
	}

	if($(".p_pids",this).length==0)
	{
		alert("No products linked");
		return false;
	}
	
	$("input[name='need_qty[]']").each(function(){
		if(isNaN($(this).val()))
		{
			check_num=1;
		}

		if($(this).val()!=0)
			check_gty_val=1;

		if($(this).val()=='')
			$(this).val(0);
	});

	if(is_emty)
	{
		alert("Please enter coupon qty");
		return false;
	}

	if(!check_gty_val)
	{
		alert("Please enter coupon qty");
		return false;
	}
	
	if(check_num)
	{
		alert("Coupons qty are must be number");
		return false;
	}

	if($(".coupons_total_value").val()*1 > book_value*1)
	{
		alert("Your Denomination total value grater then book value please reset");
		return false;
	}

	if($(".coupons_total_value").val()*1 < book_value*1)
	{
		alert("The denomination value lower then book value are you sure");
		return false;
	}

	if(confirm("Do you want to proceed creating this Book Template ?"))
		return true;
	else
		return false;

	
});

$(".coupon_qty").keyup(function(){
	var $tr = $(this).parents("tr");
	var coupon_val=$tr.find("td").eq(0).find("input.coupon_value").val();
	var qty=$(this).val();
	var total_coupons_val=0;
	var template_total=$("input[name='book_value']").val();
	var total_val=$tr.find("td").eq(4).find("input.coupon_total_value");
	var intRegex = /^\d+$/;
	var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
	var total_value=0;
	$(".coupons_total_value").css("color","black");
	
	if(isNaN(template_total) || template_total==0)
	{
		alert('Book template value must be number');
		$("input[name='book_value']").val(0);
		$(".coupon_total_value").val(0);
		$(".coupons_total_value").val(0);
		return false;
	}
	
	if(isNaN(qty))
	{
		alert('Please enter numeric value');
		$(this).val(0);
		return false;
	}

	if($(".coupons_total_value").val()*1 > template_total*1)
		$(".coupons_total_value").css("color","red");
	else if($(".coupons_total_value").val()*1 == template_total*1)
		$(".coupons_total_value").css("color","green");
	
	if(intRegex.test(qty) || floatRegex.test(qty)) {
		total_coupons_val=(qty*1)*(coupon_val*1);
		total_val.val(total_coupons_val);

		$("input.coupon_total_value").each(function(){
			if(isNaN($(this).val()))
			{
				$(this).val(0);	
			}else{
				total_value=(total_value*1)+($(this).val()*1);
			}
		});
		
		$(".coupons_total_value").val(total_value);

		if($(".coupons_total_value").val()*1 > template_total*1)
			$(".coupons_total_value").css("color","red");
		else if($(".coupons_total_value").val()*1 == template_total*1)
			$(".coupons_total_value").css("color","green");
	}else{
		total_val.val(0);

		$("input.coupon_total_value").each(function(){
			if(isNaN($(this).val()))
			{
				$(this).val(0);	
			}else{
				total_value=(total_value*1)+($(this).val()*1);
			}
		});
		
		$(".coupons_total_value").val(total_value);

		if($(".coupons_total_value").val()*1 > template_total*1)
			$(".coupons_total_value").css("color","red");
		else if($(".coupons_total_value").val()*1 == template_total*1)
			$(".coupons_total_value").css("color","green");
		return false;
	}
});

//---product--link--code---
var jHR=0,search_timer=0;
var added_po=new Array();
$("#po_search").keyup(function(){
	q=$(this).val();
	if(q.length<3)
		return true;
	if(jHR!=0)
		jHR.abort();
	window.clearTimeout(search_timer);
	search_timer=window.setTimeout(function(){
	jHR=$.post("<?=site_url("admin/jx_searchproductsfordeal")?>",{q:q,type:'prod'},function(data){
		$("#po_prod_list").html(data).show();
	});
	},200);
}).focus(function(){
	if($("#po_prod_list a").length==0)
		return;
	$("#po_prod_list").show();
}).click(function(e){
	e.stopPropagation();
});

function addproduct(id,name,mrp,margin)
{
	var alredy_linked=0;
	id = parseInt(id);
	if($.inArray(parseInt(id),added_po)!=-1)
	{
		alert("Product already added to the current Order");
		return;
	}

	if($("#pprods .p_pids").length > 0)
	{
		alert('Only one product able to link');
		return false;
	}
	
	$.post(site_url+'/admin/jx_prd_alredy_linked',{pid:id},function(res){
		if(res.status)
		{
			alredy_linked=1;
			alert("This book already linked to another template");
			return false;	
		}else{
			i=added_po.length;
			$("#po_prod_list").hide();
			template=$("#p_clone_template tbody").html();
			template=template.replace(/%pid%/,id);
			template=template.replace(/%name%/,name);
			template=template.replace(/%mrp%/,mrp);
			template=template.replace(/%sno%/g,i+1);
			$("#pprods tbody").append(template);
			if($(".added_ped_remove a").length)
			{
				$(".added_ped_remove a").remove();
			}
			$(".added_ped_remove").append("<a href='javascript:void(0)' onClick='remove_added_product(this)'>Remove</a>");
			added_po.push(id);
		}
			
	},'json');

	
}

function remove_added_product(e)
{
	if (confirm('Are you sure want to remove this product?')) {
		$(e).closest('tr').remove(); 
    }
}

//---product--link--code--end-
</script>

<style>
.hide{display:none;}
.error{color:red;}

#po_prod_list{
display:none;
position:absolute;
width:600px;
max-height:230px;
overflow:auto;
background:#eee;
border:1px solid #aaa;
}
#po_prod_list a{
display:block;
padding:5px;
}
#po_prod_list a:hover{
background:blue;
color:#fff;
}
</style>
