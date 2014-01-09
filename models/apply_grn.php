<div class="container">

<?php if($this->session->flashdata("grn_done")){?>
<div style="background:#ff9900;padding:5px;color:#fff;" align="center"><h3>Stock Intake done successfully</h3></div>
<?php }?>

<h2>Stock In</h2>
<h4>Stock Intake No : <?=$this->db->query("select grn_id from t_grn_info order by grn_id desc")->row()->grn_id+1?></h4>

<div style="margin:10px 0px;padding:5px;background:#eee;">
<h3 style="margin:0px;">Ready for stock intake</h3>
<div style="overflow:auto;width:98%;">
<table cellspacing=5>
<tr>
<?php foreach($pos as $vendor=>$ps){?>
<td style="border:1px solid #bbb;background:#F7EFB9;" class="vendor_list venl<?=$ps[0]['vendor_id']?>">
<span class="vid" style="display:none;"><?=$ps[0]['vendor_id']?></span>
<h4 style="margin:3px;background:#555;color:#fff;padding:5px;"><?=$vendor?></h4>
<div class="ajax_loadresult static_pos">
<?php foreach($ps as $p){?>
<div><a href="javascript:loadpo('<?=$p['po_id']?>')"><?=$p['remarks']?> : PO<?=$p['po_id']?> <span><?=date("d/m/y",strtotime($p['created_on']))?></span></a></div>
<?php }?>
</div>
</td>
<?php }?>
</tr>
</table>
</div>
<?php if(empty($pos)){?>POs are not available for stock intake<?php }?>
</div>

<div style="margin:10px 0px;padding:5px;background:#eee;">
<b>Load Purchase Order By Vendor</b><br>
Vendor : <select id="grn_vendor">
<?php foreach($this->db->query("select * from m_vendor_info")->result_array() as $v){?>
<option value="<?=$v['vendor_id']?>" <?php if($v['vendor_id']==$this->uri->segment(3)){?>selected<?php }?>><?=$v['vendor_name']?></option>
<?php }?>
</select><input type="button" id="grn_po_load" value="Load">
		<img src="<?=IMAGES_URL?>loading_maroon.gif" id="po_loading" style="display:none;">
	<div id="pending_pos" class="ajax_loadresult">
	</div>
</div>



<div id="loadafterselect" style="display:none">
<div style="margin:5px 0px;position:fixed;right:0px;bottom:40px;background:#F7EFB9;padding:15px;border:1px solid #aaa;">Highlight product of barcode : <input type="text" id="srch_barcode"></div> 
<form method="post" id="apply_grn_form" enctype="multipart/form-data">
<div id="grn_pids">
<?php if(isset($po)){?>
<input type="hidden" name="poids[]" value="<?=$po['po_id']?>">
<?php }?>
</div>
<div id="grn">
	<table class="datagrid">
		<thead><tr><th>S.no</th><th>Product</th><th>PO Qty</th><th>Invoice Qty<div style="font-size:80%;">reset to<br><input type="text" id="reset_inv" value="0" style="width:20px;font-size:90%;padding:0px;"><input type="button" value="ok" style="font-size:85%;padding:0px;" onclick='reset_inv_f()'></div></th><th>Receiving Qty<div style="font-size:80%">reset to<br><input type="text" id="reset_rec" value="0" style="width:20px;font-size:90%;padding:0px;"><input type="button" value="ok" onclick='reset_rec_f()' style="font-size:85%;padding:0px;"></div></th><th>PO MRP</th><th>MRP</th><th>PO Purchase Price</th><th>Purchase Price</th><th>Storage</th><th>Received<br>Till Date</th><th>Pending Qty</th><th>FOC</th><th>Has Offer</th><th>Expiry date</th></tr></thead>
		<tbody>
<?php if(isset($po)){?>
		<?php foreach($po_items as $i){?>
		<tr>
		<td></td>
		<td><span style="font-size:80%"><?=$i['product_name']?><input type="hidden" class="prod_addcheck" name="pid<?=$i['po_id']?>[]" value="<?=$i['product_id']?>"></span></td>
		<td><?=$i['order_qty']?><input type="hidden"  class="popqty" value="<?=$i['order_qty']-$i['received_qty']?>"></td>
		<td><input type="text" class="inp iqty" name="oqty<?=$i['po_id']?>[]" size=3 value="<?=$i['order_qty']-$i['received_qty']?>"></td>
		<td><input type="text" class="inp rqty qtychange" name="rqty<?=$i['po_id']?>[]" size=3 value="<?=$i['order_qty']-$i['received_qty']?>"></td>
		<td><?=$i['mrp']?></td>
		<td><input type="text" class="inp" name="mrp<?=$i['po_id']?>[]" size=5 value="<?=$i['mrp']?>"></td>
		<td><?=$i['purchase_price']?></td>
		<td><input type="text" class="inp" name="price<?=$i['po_id']?>[]" size=5 value="<?=$i['purchase_price']?>"></td>
		<td>
		<select name="storage<?=$i['po_id']?>[]">
		<?php foreach($this->db->query("select * from m_rack_bin_info")->result_array() as $r){?>
		<option value="<?=$r['id']?>" <?php if($i['default_rackbin_id']==$r['id']) echo 'selected';?>><?=$r['rack_name']?>-<?=$r['bin_name']?></option>
		<?php }?>
		</select>
		</td>
		<td><?=$i['received_qty']?></td>
		<td class="pqty">0</td>
		<td><?=$i['is_foc']?"YES":"NO"?></td>
		<td><?=$i['has_offer']?"YES":"NO"?></td>
		<td><input type="text" class="inp edate expdate" name="expdate[]"></td>
		</tr>
		<?php }?>
<?php }?>
		</tbody>
	</table>
</div>

<div style="margin:10px;clear:both;">
<h3>Total value of receiving quantity : <span style="font-size:140%;" id="value_receiving"></span></h3>
</div>

<div style="float:right;margin-top:20px;padding:10px;background:#F7EFB9;">
<h4>Remarks</h4>
<textarea name="remarks" cols=40 rows=5></textarea>
</div>
<div>
<h3>Invoice Details</h3>
<a href="javascript:void(0)" onclick='cloneinvoice()'>link another invoice</a>
<table class="datagrid invoice_tab">
<thead>
<tr>
<th>Invoice No</th>
<th>Date</th>
<th>Invoice Amount</th>
<th>Scanned Copy</th>
</tr>
</thead>
<tbody>
<tr>
<td><input type="text" name="invno[]" class="inp"></td>
<td><input type="text" name="invdate[]" class="inp datepick"></td>
<td>Rs. <input size=7 type="text" class="inp" name="invamount[]"></td>
<td><input type="file" name="scan_0" class="scan_file"></td>
</tr>
</tbody>
</table>
</div>
<div style="padding:20px 0px;">
<input type="submit" value="Submit Stock">
</div>
</form>
</div>

<div id="grn_template" style="display:none">
	<div class="right">
		<table>
		<tbody>
			<tr class="barcode%bcode% barcodereset">
			<td>%sno%</td>
			<td>
				<span style="font-size:80%">%name%<input type="hidden" name="pid%pid%[]" class="prod_addcheck" value="%prodid%"></span>
				<span style="font-size:70%"><a href="javascript:void(0)" onclick='show_add_barcode(event,"%prodid%")'>%update_barcode%</a></span>
			</td>
			<td class="poqty">%qty%<input type="hidden"  class="popqty" value="%qty%"></td>
			<td><input type="text" class="inp iqty" name="oqty%pid%[]" size=3 value="%pqty%"></td>
			<td><input type="text" class="inp rqty qtychange" name="rqty%pid%[]" size=3 value="%pqty%"></td>
			<td>%mrp%</td>
			<td><input type="text" class="inp" name="mrp%pid%[]" size=5 value="%mrp%"></td>
			<td>%price%</td>
			<td><input type="text" class="inp pprice" name="price%pid%[]" size=5 value="%price%"></td>
			<td>
			<select name="storage%pid%[]">
			==rbs==
			</select>
			</td>
			<td>%rqty%</td>
			<td class="pqty">0</td>
			<td>%foc%</td>
			<td>%offer%</td>
			<td><input type="text" name="expdate[]" class="inp edate expdate%dpe%"></td>
			</tr>
		</tbody>
		</table>
	</div>
</div>

<div id="invoice_template" style="display:none">
<table>
<tbody>
<tr>
<td><input type="text" name="invno[]" class="inp" class="invno"></td>
<td><input type="text" name="invdate[]" class="inp datepick%dpi%" class="invdate"></td>
<td>Rs. <input size=7 type="text" class="inp" name="invamount[]" class="invamount"></td>
<td><input type="file" name="scan_%dpi%"></td>
</tr>
</tbody>
</table>
</div>

</div>
<style>
.highlightprow{
background:#ff9900;
}
</style>
<script>
var added_pos=[];
$(function(){

	$(document).keydown(function(e){
		if(e.which==27)
			$("#add_barcode_dialog").hide();
		return true;
	});

	$("#srch_barcode").keyup(function(e){
		if(e.which==13)
		{
			$(".barcodereset").removeClass("highlightprow");
			if($(".barcode"+$(this).val()).length==0)
			{
				alert("Product not found on loaded PO");
				return;
			}
			$(".barcode"+$(this).val()).addClass("highlightprow");
			$(document).scrollTop($(".barcode"+$(this).val()).offset().top);
		}
	});
	
	$(".static_pos a").click(function(){
			$("td",$($(this).parents("td").get(0)).parent()).hide();
			$($(this).parents("td").get(0)).show();
			vid=$("span.vid",$(this).parents("td").get(0)).text();
			$("#grn_vendor").val(vid).attr("disabled",true);
			$("#grn_po_load").attr("disabled",true);
	});

	$("#apply_grn_form input").live("keydown",function(e){
		if(e.which==13)
		{
			e.stopPropagation();
			e.preventDefault();
			return false;
		}
	});
	
	$("#apply_grn_form").submit(function(){
		flag=true;
		if($(".prod_addcheck").length==0)
		{
			alert("Please Load a PO");
			flag=false;
			return flag;
		}
		$(".invno,.invdate,.invamount").each(function(){
			if($(this).val().length==0)
			{
				alert("Enter invoice details");
				flag=false;
				return false;
			}
		});
		return flag;
	});
	
	$(".expdate, .datepick").datepicker();
	$("#grn_vendor").attr("disabled",false);
<?php if(isset($po)){?>
	$("#grn_vendor").attr("disabled",true);
	added_pos.push(<?=$po['po_id']?>);
<?php } ?>
	$("#grn_po_load").click(function(){
		$("#po_loading").show();
		$.post('<?=site_url('admin/jx_getpos')?>',{v:$("#grn_vendor").val()},function(d){
			$("#po_loading").hide();
			$("#pending_pos").html(d);
			$("#grn_vendor").attr("disabled",true);
		});
	});
	$("#grn .datagrid .qtychange").live("change",function(){
		$p=$(this).parents("tr").get(0);
		q=parseInt($(".popqty",$p).val())-parseInt($(".rqty",$p).val());
		if(q<0)
			q="("+(q*-1)+")";
		$(".pqty",$p).html(q);
	});
	$("#grn .datagrid .pprice, #grn .datagrid .rqty").live("change",function(){
		calc_rec_value();
	});
	$("#abd_barcode").keydown(function(e){
		if(e.which==13)
		{
			$.post("<?=site_url("admin/update_barcode")?>",{pid:$('#abd_pid').val(),barcode:$('#abd_barcode').val()});
			$("#add_barcode_dialog").hide();
		}
		return true;
	});
});
function calc_rec_value()
{
	r_total=0;
	$("#grn .datagrid tr").each(function(){
		$p=$(this);
		rqty=parseInt($(".rqty",$p).val());
		rqty=isNaN(rqty)?"0":rqty;
		pprice=parseFloat($(".pprice",$p).val());
		pprice=isNaN(pprice)?"0":pprice;
		r_total+=rqty*pprice;
	});
	$("#value_receiving").html("Rs "+r_total.toFixed(2));
}
var dpi=0,dpe=0;
function cloneinvoice()
{
	dpi++;
	temp=$("#invoice_template tbody tr").html();
	temp=temp.replace(/%dpi%/g,dpi);
	$(".invoice_tab tbody").append("<tr>"+temp+"</tr>");
	$(".datepick"+dpi).datepicker();
}

function loadpo(pid)
{
	if($.inArray(pid,added_pos)!=-1)
	{
		alert("This purchase order already loaded");
		return;
	}
	$("#po_loading").show();
	$(".vendor_list").hide();
	$(".venl"+$("#grn_vendor").val()).show();
	$.post('<?=site_url('admin/jx_grn_load_po')?>',{p:pid},function(data){
		pois=$.parseJSON(data);
		g_rows="";
		dpes=[];
		$.each(pois,function(i,poi){
			dpe++;
			grow=$("#grn_template .right table tbody").html();
			update_barcode='';
			if(poi.barcode.length==0)
				update_barcode="add barcode";
			grow=grow.replace(/%update_barcode%/g,update_barcode);
			grow=grow.replace(/%bcode%/g,poi.barcode);
			grow=grow.replace(/%prodid%/g,poi.product_id);
			grow=grow.replace(/%sno%/g,dpe);
			grow=grow.replace(/%pid%/g,pid);
			grow=grow.replace(/%name%/g,poi.product_name);
			grow=grow.replace(/%qty%/g,poi.order_qty);
			grow=grow.replace(/%pqty%/g,parseInt(poi.order_qty)-parseInt(poi.received_qty));
			grow=grow.replace(/%mrp%/g,poi.mrp);
			grow=grow.replace(/%price%/g,poi.purchase_price);
			grow=grow.replace(/%rqty%/g,poi.received_qty);
			grow=grow.replace(/==rbs==/g,poi.rbs);
			grow=grow.replace(/%dpe%/g,dpe);
			offer=foc="NO";
			if(poi.is_foc=="1")
				foc="YES";
			if(poi.has_offer=="1")
				offer="YES";
			grow=grow.replace(/%foc%/g,foc);
			grow=grow.replace(/%offer%/g,offer);
			g_rows=g_rows+grow;
			$(".expdate"+dpe).datepicker();
			dpes.push(".expdate"+dpe);
		});
		$("#grn .datagrid tbody").append(g_rows);
		$(dpes.join(", ")).datepicker();
		$("#grn_pids").append('<input type="hidden" name="poids[]" value="'+pid+'">');
		added_pos.push(pid);
		$("#po_loading").hide();
		$("#loadafterselect").show();
		calc_rec_value();
	});
}

function reset_rec_f()
{
	v=parseInt($("#reset_rec").val());
	if(isNaN(v))
	{
		alert("Not a number");return;
	}
	if(confirm("Are you sure want to reset all receiving qty to "+v+" ?"))
		$("#apply_grn_form .rqty").val(v);
	calc_rec_value();
}

function reset_inv_f()
{
	v=parseInt($("#reset_inv").val());
	if(isNaN(v))
	{
		alert("Not a number");return;
	}
	if(confirm("Are you sure want to reset all invoice qty to "+v+" ?"))
		$("#apply_grn_form .iqty").val(v).change();
}

function show_add_barcode(e,pid)
{
	x=e.clientX;
	y=e.clientY;
	$("#add_barcode_dialog").css("top",y+"px").css("left",x+"px").show();
	$("#abd_barcode").focus();
	$("#abd_pid").val(pid);
}


</script>

<div id="add_barcode_dialog">
<input type="hidden" value="" id="abd_pid">
Enter Barcode : <input type="text" class="inp" style="width:200px;" id="abd_barcode">
</div>

<style>
#add_barcode_dialog{
position:fixed;
top:0px;
left:0px;
display:none;
padding:5px;
background:#eee;
border:1px solid #f90;
}
.edate{
width:70px;
}
#grn{
margin-left:10px;
background:#eee;
padding:5px;
}
.grn_po{
background:#eee;
padding-top:5px;
}
.grn_po .datagrid, #grn .datagrid{
width:100%;
}
</style>
</div>
<?php
