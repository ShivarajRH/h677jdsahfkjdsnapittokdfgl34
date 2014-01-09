<?php 
	$paf_flags = array();
	$paf_flags[0] = '';
	$paf_flags[1] = 'Open';
	$paf_flags[2] = 'Closed';
	$paf_flags[3] = 'Cancelled';
	 
?>
<div class="container" style="width: 90%;">

<h2>PAF Stock Intake</h2>

<?php 
	if($pafdata[0]['paf_status'] == 1)
	{
?>
<form method="post" id="poprodfrm" enctype="multipart/form-data" action="<?php echo site_url('admin/process_paf_stockintake');?>" >
<?php } ?>
	<input type="hidden" value="<?php echo $pafdata[0]['paf_id']?>" name="paf_id" />
	<div style="padding:5px;">
		<b>PAF ID &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :</b> &nbsp;&nbsp;&nbsp;
		<?php 
			echo $pafdata[0]['paf_id'].' ';
		?>
	</div>
	<div style="padding:5px;">
		<b>Handled by : </b>&nbsp;&nbsp;&nbsp;
		<select name="handled_by" disabled="disabled">
			<option value="">Choose</option>
			<?php 
				$emp_list_res = $this->db->query('select id,name,mobile from m_employee_list where role = "DELIVARY_MANAGER" order by name ');
				if($emp_list_res->result_array())
				{
					foreach($emp_list_res->result_array() as $row)
					{
						$sel = (($pafdata[0]['handled_by'] == $row['id'])?'selected':'');
						echo '<option '.$sel.' value="'.$row['id'].'">'.$row['name'].'-'.$row['mobile'].'</option>';
					}
				}
			?>	
		</select>
	</div>
 
	<div style="padding:5px;">
		<b>Search &amp; Add :</b> <input type="text" class="inp" id="po_search" style="width:400px;"> <input type="button" id="load_unavail" value="Load stock unavailable products">
		<div class="srch_result_pop closeonclick" id="po_prod_list"></div>
	</div>
	 
	<table class="datagrid" id="pprods" width="100%">
	<thead>
		<tr>
			<th>S.No</th>
			<th width="30">PAF Prod Refno</th>
			<th>Product</th>
			<th align="center">PAF Qty</th>
			<th align="center">Recevied Qty</th>
			<th align="center">OLD MRP</th>
			<th align="center">NEW MRP</th>
			<th align="center">Price</th>
			<th width="200">Vendor</th>
		  	<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			$i=0;
			foreach($pafdata  as $paf)
			{
		?>
			<tr class="barcode<?php echo $paf['barcode']?> barcodereset oldpafdata">
				<td><?php echo ++$i;?></td>
				<td><?php echo $paf['prod_paf_id']?></td>
				<td>
					<input type="hidden" name="product[]" value="<?php echo $paf['product_id']?>">
					<input type="hidden" name="prod_paf_id[]" value="<?php echo $paf['prod_paf_id']?>">
					<span class="pname"><?php echo $paf['product_name']?></span>
					<div><span style="font-size:70%"><a href="javascript:void(0)"  onclick='show_add_barcode(event,"<?php echo $paf['prod_paf_id']?>")'><?php echo $paf['barcode']?'Update Barcode':'Add Barcode'?></a></span></div>
				</td>
				<td align="center"><span style="padding-top:5px;"><?php echo $paf['qty']?></span></td>
				<td align="center"><input type="text" class="inp" size=2 name="qty[]" oqty="<?php echo $paf['qty']?>" value="<?php echo $paf['qty']?>"></td>
				<td align="left"><span style="padding-top:5px;"><?php echo $paf['mrp']?></span></td>
				<td align="right"><input type="text" class="inp" size=2 name="mrp[]" value="<?php echo $paf['mrp']?>"></td>
				<td align="right"><input type="text" class="inp" size=2 name="pprice[]" value="0"></td>
				<td >
					<select class="vendor" name="vendor[]">
						<option value="0">Unknown</option>
					<?php 
						$sel_ven_id = $paf['vendor_id'];
						$ven_list = explode(',',$paf['vendors']);
						foreach($ven_list as $ven)
						{
							list($ven_id,$ven_name,$ven_margin) = explode('::',$ven);
							
							$sel = ($sel_ven_id == $ven_id)?'selected':'';
							
							echo '<option  '.$sel.' margin="'.$ven_margin.'" value="'.$ven_id.'">'.$ven_name.'('.$ven_margin.'%)'.'</option>';
						}
					?>
						
					</select>
				</td>
				 
				<td>
					<a href="javascript:void(0)" onclick="split_row(this)" >Add</a>&nbsp;
					<a href="javascript:void(0)" onclick='remove_row(this)'>Remove</a>
				</td>
			</tr>
		<?php 		
			}
		?>
	</tbody>
	</table>
	
	<br />
	<div align="left">
		<input type="button" value="Add Invoices" onclick="$(this).hide();$('#add_inv_det').show();build_invoice_form()">
	</div>
	<div id="add_inv_det" style="display: none;">
		<h3>Add Invoice Details</h3>
		<a style="float: right" href="javascript:void(0)" onclick='clonevendorinvoice(0)'>link another invoice</a>
		<a href="javascript:void(0)" onclick='build_invoice_form()'>load vendors from above</a>
		<table class="datagrid invoice_tab" width="100%">
			<thead>
				<tr>
					<th>Vendor</th>
					<th>Invoice No</th>
					<th>Date</th>
					<th>Invoice Amount</th>
					<th>Scanned Copy</th>
				</tr>
			</thead>
			<tbody>
				 
			</tbody>
		</table>
		
		<div style="padding:20px 0px;">
			<div>
				<input type="submit" value="Create PO and Update Stock Details">
			</div>
		</div>
	</div>
	
	<div id="invoice_template" style="display:none">
		<table>
			<tbody>
				<tr>
					<td><select type="text" name="inv_vendor_id[]" class="inp" >%vendorlist%</select></td>
					<td><input type="text" name="invno[]" class="inp" class="invno"></td>
					<td><input type="text" name="invdate[]" class="inp datepick%dpi%" class="invdate" style="width: 100px;"></td>
					<td>Rs. <input size=7 type="text" class="inp" name="invamount[]" class="invamount"></td>
					<td><input type="file" name="scan_%dpi%"></td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<?php 
	if($pafdata[0]['paf_status'] == 1)
	{
?>
	
	</form>
	<?php 
	}
?>


<div style="display:none">
<table id="p_clone_template">
	<tr class="barcode--barcode-- barcodereset new_prods">
		<td>%sno%</td>
		<td>New</td>
		<td><input type="hidden" name="product[]" value="%product_id%">
			<input type="hidden" name="prod_paf_id[]" value="0"><input type="hidden" class="brand" name="brand[]" value="%brand_id%"><span class="pname">%product_name%</span><br /><b>(%product_brand%)</b>
			<div><span style="font-size:70%"><a href="javascript:void(0)"  onclick='show_add_barcode(event,"%product_id%")'>%update_barcode%</a></span></div>
		</td>
		<td align="center"> 0 </td>
		<td align="center"><input type="text" class="inp" size=2 name="qty[]" value="%require_qty%"></td>
		<td align="left"><span>%mrpvalue%</span></td>
		<td align="center"><input type="text" class="inp" size=2 name="mrp[]" value="%mrpvalue%"></td>
		<td><input type="text" class="inp" size=2 name="pprice[]" value="%pprice%"></td>
		<td><select class="vendor" name="vendor[]">%vendorlist%</select></td>
		<td>
			<a href="javascript:void(0)" onclick="split_row(this)" >Add</a>&nbsp;
					<a href="javascript:void(0)" onclick='remove_row(this)'>Remove</a>
		</td>
	</tr>
</table>
</div>


</div>
<div style="display: none;">
	<iframe id="hndl_frame_action" name="hndl_frame_action" style="width: 1px;height: 1px;border:0px;"></iframe>
</div>

<div style="margin:5px 0px;position:fixed;right:0px;bottom:40px;background:#F7EFB9;padding:15px;border:1px solid #aaa;">Highlight product of barcode : <input type="text" id="srch_barcode"></div>


<div id="add_barcode_dialog">
<input type="hidden" value="" id="abd_pid">
Enter Barcode : <input type="text" class="inp" style="width:200px;" id="abd_barcode">
</div>
<style>
#add_barcode_dialog,#add_imei_dialog{
position:fixed;
top:0px;
left:0px;
display:none;
padding:5px;
background:#eee;
border:1px solid #f90;
}
</style>
<script>
dpi = 0;
$("#abd_barcode").keydown(function(e){
	if(e.which==13)
	{
		$.post("<?=site_url("admin/update_barcode")?>",{pid:$('#abd_pid').val(),barcode:$('#abd_barcode').val()});
		$("#add_barcode_dialog").hide();
	}
	return true;
});
$(document).keydown(function(e){
	if(e.which==27)
		$("#add_barcode_dialog").hide();
	return true;
});
function show_add_barcode(e,pid)
{
	x=e.clientX;
	y=e.clientY;
	$("#add_imei_dialog").hide();
	$("#add_barcode_dialog").css("top",y+"px").css("left",x+"px").show();
	$("#abd_barcode").focus().val("");
	$("#abd_pid").val(pid);
}
function clonevendorinvoice(vid)
{
	dpi++;
	temp=$("#invoice_template tbody tr").html();
	temp=temp.replace(/%dpi%/g,dpi);
	temp=temp.replace(/%vendorlist%/g,ven_sellist);
	$(".invoice_tab tbody").append("<tr>"+temp+"</tr>");
	$(".datepick"+dpi).datepicker();
	 
	$(".invoice_tab tbody tr:last select[name=\"inv_vendor_id[]\"]").val(vid);
}

function remove_row(ele)
{
	$(ele).parent().parent().remove();
}
function split_row(ele)
{
	var rele = $(ele).parent().parent();
	var ttl = prompt("How many no of rows to be created ?");
		if(ttl*1)
		{
			var ttl_qty = rele.find('input[name="qty"]').val();
				for(var i=0;i<ttl-1;i++)
				{
					rele.after('<tr class="barcode--barcode-- barcodereset new_prods">'+rele.html()+'</tr>');
				}
		}
		 
}
    
$('#poprodfrm').submit(function(){

	var block_frm_submit = 0;
	var qty_pending = 0;
	var ven_pending = 0;
		$('#pprods tbody tr',this).each(function(){
			qty = $('input[name="qty[]"]',this).val()*1;
			ven = $('select[name="vendor[]"]',this).val()*1;

			if(!qty)
				qty_pending += 1;

			if(!ven)
				ven_pending += 1; 

			 
			
			 
		});

		if(qty_pending || ven_pending){
			alert("Unable to submit request, please check recevied qty and vendors");
			return false;
		}


	var selected_pro_ven = new Array();
		$('select[name="vendor[]"]').each(function(){
			if($(this).val()*1)
				selected_pro_ven[$(this).val()] = 1;
		});

		$('select[name="inv_vendor_id[]"]').each(function(){
			if($(this).val()*1)
				selected_pro_ven[$(this).val()] = 0;
		});

		var stat = 1; 
		for(var i in selected_pro_ven)
		{
			if(selected_pro_ven[i] == 1)
				stat = 0;
		}
		
		if(!stat){
			alert("Unable to submit request, Atleast one invoice need to be added for the selected vendors ");
			return false;
		}

		stat = 1; 
		$('#add_inv_det tbody tr').each(function(){
			ven_id = $('select[name="inv_vendor_id[]"]',this).val();
			if(ven_id)
			{
				invno = $('input[name="invno[]"]',this).val();
				invamt = $('input[name="invamount[]"]',this).val();
				if(invno == '' && invamt == '')
				{
					stat = 0;
				}
			}
			
		});

		if(!stat){
			alert("Unable to submit request,Please enter invoice no or invoice amount  ");
			return false;
		}
		
	
	
});
 
var added_po=[];

function addproduct(id,name,mrp,require)
{
	$('#notify_handler_block').show();
	$('input[name="notify_handler"]').attr('checked',true);
	
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

		var update_barcode = 'update barcode';
		if(o.barcode==null || o.barcode.length==0)
			update_barcode="add barcode";
		
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
		template=template.replace(/%update_barcode%/g,update_barcode);
		
		mrp=parseInt(o.mrp);
		pprice=mrp-(mrp*parseInt(o.margin)/100);
		template=template.replace(/%pprice%/g,pprice);
		vendors="";
		
		$.each(o.vendors,function(i,v){
			vendors=vendors+'<option margin="'+v.ven_margin+'" value="'+v.vendor_id+'">'+v.vendor+'</option>';
		});
		vendors+="<option value='0'>Unknown</option>";
		template=template.replace(/%vendorlist/g,vendors);
		$("#pprods tbody").append(template);
		added_po.push(id);
		compose_msg();
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
				alert("Product not found in the list");
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

$('input[name="qty[]"]').live('change',function(){
	if($(this).attr('oqty')*1 != $(this).val())
	{
		$(this).parent().parent().find('.notify').attr('checked',true);
	}else
	{
		$(this).parent().parent().find('.notify').attr('checked',false);
	}
	
 
	compose_msg();
});


$('input[name="product[]"]').each(function(){
	added_po.push($(this).val()); 
});

$('.notify').live('change',function(){
	compose_msg();
});
$('select[name="vendor[]"]').live('change',function(){
	var vmar = $('option:selected',this).attr('margin');
	var mrp = $(this).parent().parent().find('input[name="mrp[]"]').val();
	var pprice_ele = $(this).parent().parent().find('input[name="pprice[]"]');
	var margin_amt = mrp-(mrp*vmar/100);
		pprice_ele.val(isNaN(margin_amt)?0:margin_amt);
		
}).trigger('change');


var ven_sellist = '<option value="">Choose</option>';

function build_invoice_form()
{
	var vendor_list = new Array();
		$('select[name="vendor[]"]').each(function(){
			$('option:selected',this).each(function(){
				
				if($(this).text() != 'Unknown' ){
					vendor_list[$(this).attr('value')] = $(this).text(); 
				}
					
			});
		});
		
		$('.invoice_tab tbody').html('');
		ven_sellist = '<option value="">Choose</option>';
		for(var vid in vendor_list)
		{
			vname = vendor_list[vid];
			if(vname != 'undefined')
				ven_sellist += '<option value="'+vid+'">'+vname+'</option>';					
		}
			
		for(var vid in vendor_list)
		{
			clonevendorinvoice(vid);
		}  
			
}

build_invoice_form();

</script>

<style>
.highlightprow{
background:#ff9900;
}
</style>


<?php
