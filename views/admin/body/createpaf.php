<div class="container">
<h2>Create PAF</h2>





<form method="post" id="poprodfrm" action="<?php echo site_url('admin/process_create_paf');?>" autocomplete="off">
	<div style="padding:5px;">
		<b>Handled by : </b>&nbsp;&nbsp;&nbsp;
		<select name="handled_by">
			<option value="">Choose</option>
			<?php 
				$emp_list_res = $this->db->query('select id,name,mobile from m_employee_list where role = "DELIVARY_MANAGER" order by name ');
				if($emp_list_res->result_array())
				{
					foreach($emp_list_res->result_array() as $row)
					{
						echo '<option value="'.$row['id'].'">'.$row['name'].'-'.$row['mobile'].'</option>';
					}
				}
			?>	
		</select>
	</div>
	<div style="padding:5px;">
		<b>Search &amp; Add :</b> <input type="text" class="inp" id="po_search" style="width:400px;"> <input type="button" id="load_unavail" value="Load stock unavailable products">
		<div class="srch_result_pop closeonclick" id="po_prod_list"></div>
	</div>

	<table class="datagrid" id="pprods" >
	<thead>
		<tr>
			<th width="10">
				<input type="checkbox" value="1" checked="checked" name="check_select_all" >
			</th>
			<th width="20">S.No</th>
			<th width="300">Product</th>
			<th width="30">Current Stock</th>
			<th width="50">Ordered in Past 90 Days</th>
			<th width="30">Qty</th>
			<th width="30">MRP</th>
			<th colspan=2 width="200">Vendor</th>
		</tr>
	</thead>
	<tbody>
	
	</tbody>
	</table>
	<br />
	<div align="left">
		<b>Remarks</b><br />
		<textarea name="remarks" rows="3" cols="45"></textarea>
	</div>
	<div style="padding:20px 0px;">
		<input type="submit" value="Create PAF File">
	</div>
</form>

<div style="display:none">
<table id="p_clone_template">
	<tr class="barcode--barcode-- barcodereset">
		<td><input type="checkbox" value="1" class="check_prod" checked="checked" name="prod_sel[%product_id%]" /></td>
		<td class="slno_indx">%sno%</td>
		<td>
			<input type="hidden" name="product[]" value="%product_id%">
			<input type="hidden" class="brand" name="brand[]" value="%brand_id%">
			%product_name%
			<br />
			<b>(%product_brand%)</b>
		</td>
		<td><input type="text" class="inp" size=2 value="%current_stock%"></td>
		<td><input type="text" class="inp" size=2 value="%past_order_qty%"></td>
		<td><input type="text" class="inp" size=2 name="qty[]" value="%require_qty%"></td>
		<td><input type="text" class="inp calc_pp mrp" size=4 name="mrp[]" value="%mrpvalue%"></td>
		<td><select class="vendor" name="vendor[]">%vendorlist%</select></td>
		<td><a href="javascript:void(0)" onclick='delete_tbl_row(this)'>remove</a></td>
	</tr>
</table>
</div>


</div>

<script>


$('input[name="check_select_all"]').change(function(){
	if($(this).attr('checked'))
	{
		$('.datagrid .check_prod').attr('checked',true);
	}
	else
	{
		$('.datagrid .check_prod').attr('checked',false);
	}
});

$('#poprodfrm').submit(function(){


	if($('select[name="handled_by"]').val() == '')
	{
		alert("Please select handled by");
		return false;
	}

	if(!$('.datagrid tbody tr',this).length)
	{
		alert("Unable to submit your request, Please add atleast one product into paf ");
		return false;
	}
	
	var block_frm_submit = 0;
	var qty_pending = 0;
	var ven_pending = 0;
		$('.datagrid tbody tr',this).each(function(){

			if($('.check_prod',this).attr('checked'))
			{
				qty = $('input[name="qty[]"]',this).val()*1;
				ven = $('select[name="vendor[]"]',this).val()*1;
	
				if(!qty)
					qty_pending += 1;
			}
			 
		});

	if(qty_pending || ven_pending){
		alert("Unable to submit request, qty is missing");
		return false;
	}

	$('.datagrid tbody tr',this).each(function(){

		if(!$('.check_prod',this).attr('checked'))
		{
			$(this).remove();
		}
		 
	});

	 
	
});

function delete_tbl_row(ele)
{
	$(ele).parent().parent().remove();
	$('.slno_indx').each(function(i,itm){
		$(this).text(i+1);
	});
}




var added_po=[];
function addproduct(id,name,mrp,require)
{
	require = (typeof require === "undefined") ? "" : require;
	$("#po_prod_list").hide();
	if($.inArray(id,added_po)!=-1)
	{
		alert("Product already added to the current Order");
		return;
	}
	$.post("<?=site_url("admin/jx_productdetails")?>",{id:id},function(data){
		o=$.parseJSON(data);
		i=added_po.length;
		template=$("#p_clone_template tbody").html();
		template=template.replace(/%sno%/g,i+1);
		template=template.replace(/%require_qty%/g,require);
		template=template.replace(/%product_id%/g,o.product_id);
		template=template.replace(/%brand_id%/g,o.brand_id);
		template=template.replace(/%product_name%/g,o.product_name);
		template=template.replace(/%product_brand%/g,o.brand_name);
		template=template.replace(/--barcode--/g,o.barcode);
		template=template.replace(/%mrpvalue%/g,o.mrp);
		template=template.replace(/%margin%/g,o.margin);
		template=template.replace(/%foc%/g,"foc"+i);
		template=template.replace(/%offer%/g,"offer"+i);
		template=template.replace(/%current_stock%/g,o.cur_stk);
		template=template.replace(/%past_order_qty%/g,o.orders);
		
		
		
		mrp=parseInt(o.mrp);
		pprice=mrp-(mrp*parseInt(o.margin)/100);
		template=template.replace(/%pprice%/g,pprice);
		vendors="";
		$.each(o.vendors,function(i,v){
			vendors=vendors+'<option value="'+v.vendor_id+'">'+v.vendor+'</option>';
		});
		vendors+="<option value='0'>Unknown</option>";
		template=template.replace(/%vendorlist/g,vendors);
		$("#pprods tbody").append(template);
		added_po.push(id);
	});
}
var search_timer=0;
var jHR=0;
$(function(){

	$("#srch_barcode").keyup(function(e){
		if(e.which==13)
		{
			$(".barcodereset").removeClass("highlightprow");
			if($(".barcode"+$(this).val()).length==0)
			{
				alert("Product not found on rising PO");
				return;
			}
			$(".barcode"+$(this).val()).addClass("highlightprow");
			$(document).scrollTop($(".barcode"+$(this).val()).offset().top);
		}
	});

	
	$("#load_unavail").click(function(){
		$(this).attr("disabled",true);
		$.post("<?=site_url("admin/jx_load_unavail_products")?>",{hash:<?=time()?>},function(data){
			os=$.parseJSON(data);
			$.each(os,function(i,o){
				addproduct(o.product_id, "", "",o.qty-o.available);
			});
		});
	}).attr("disabled",false);
	$("#po_search").keyup(function(){
		q=$(this).val();
		if(jHR!=0)
			jHR.abort();
		clearTimeout(search_timer);
		search_timer=setTimeout(function(){
		jHR=$.post("<?=site_url("admin/jx_searchproducts")?>",{q:q},function(data){
			$("#po_prod_list").html(data).show();
		});},100);
	}).focus(function(){
		if($("#po_prod_list a").length==0)
			return;
		$("#po_prod_list").show();
	}).click(function(e){
		e.stopPropagation();
	});
	$("#pprods .calc_pp").live("change",function(){
		$r=$(this).parents("tr").get(0);
		mrp=parseInt($(".mrp",$r).val());
		margin=parseInt($(".margin",$r).val());
		discount=parseInt($(".discount",$r).val());
		mmrp=mrp-(mrp*margin/100);
		if($(".type",$r).val()==1)
			mmrp=mmrp-(mrp*discount/100);
		else
			mmrp=mmrp-discount;
		if(isNaN(mmrp))
			mmrp="-";
		$(".pprice",$r).val(mmrp);
	});
	$("#pprods .vendor").live("change",function(){
		$r=$(this).parents("tr").get(0);
		$.post("<?=site_url("admin/jx_getbrandmargin")?>",{v:$(this).val(),b:$(".brand",$r).val()},function(data){
				$(".margin",$r).val(data).change();
		});
	});
});
</script>

<style>
.highlightprow{
background:#ff9900;
}
</style>


<?php
