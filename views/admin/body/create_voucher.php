<div class="container">
<h2>Create Voucher</h2>

<form method="post" id="voucherfrm" data-validate="parsley">


<div style="margin:10px 0px;padding:5px;background:#eee;">
<h3 style="margin:0px;">Ready for payment Stocks</h3>
<div style="overflow:auto;width:98%;">
<table cellspacing=5>
<tr>
<?php 
foreach($grns as $i=>$sgrn)
foreach($sgrn as $g=>$grn)
{
	$rpos=$this->db->query("select distinct(po_id) as po_id from t_grn_product_link where grn_id=?",$grn['grn_id'])->result_array();
	$pos=array();
	foreach($rpos as $p)
		$pos[]=$p['po_id'];
	$grns[$i][$g]['pos']=implode(",",$pos);
}
foreach($grns as $vendor=>$ps){?>
<td style="border:1px solid #bbb;background:#F7EFB9;" class="vendor_list venl<?=$ps[0]['vendor_id']?>">
<span class="vid" style="display:none;"><?=$ps[0]['vendor_id']?></span>
<h4 style="margin:3px;background:#555;color:#fff;padding:5px;"><?=$vendor?></h4>
<div class="ajax_loadresult static_pos">
<?php foreach($ps as $p){?>
<div><a href="javascript:void(0)" onclick="$('#vendor_sel').val('<?=$p['vendor_id']?>');addgrn('<?=$p['grn_id']?>','<?=$p['amount']?>','<?=format_datetime($p['created_on'])?>','<?=$p['pos']?>')">GRN<?=$p['grn_id']?> <span><?=date("d/m/y",strtotime($p['created_on']))?></span></a></div>
<?php }?>
</div>
</td>
<?php }?>
</tr>
</table>
</div>
</div>

<div id="loadgrn_po" style="background:#eee;padding:5px;margin-bottom:20px;">
<h4 style="display:inline-block;margin:0px;">Load Stock Intakes/POs</h4>
	Select vendor : <select id="vendor_sel">
<?php foreach($this->db->query("select vendor_name as name,vendor_id as id from m_vendor_info order by name asc")->result_array() as $r){?>
<option value="<?=$r['id']?>"><?=$r['name']?></option>
<?php }?>
	</select>
	<input type="button" value="Load" id="load_grn">
	<input type="button" value="Load" id="load_po" style="display:none;">
	<div class="ajax_loadresult">
		<table width="100%" style="display:none;">
		<tr>
		<td width="50%">
		<h4 style="margin:0px">Stock Intakes</h4>
		<div id="grn_res"></div>
		</td><td width="50%">
		<h4 style="margin:0px">Purchase Orders</h4>
		<div id="pos_res"></div>
		</td>
		</table>
	</div>
</div>

<div id="grns" style="display:none;">
<h3 style="margin-bottom:0px;">Payments for Stocks</h3>
<table class="datagrid nofooter">
<thead>
<tr>
<th>GRN No</th><th>Adjusted Payment</th><th>POs</th><th>Raised on</th>
</tr>
</thead>
<tbody></tbody>
</table>
</div>
<div id="pos" style="display:none;">
<h3 style="margin-bottom:0px;">Advance payment for POs</h3>
<table class="datagrid">
<thead>
<tr>
<th>PO No</th><th>Adjusted Payment</th><th>Raised on</th>
</tr>
</thead>
<tbody></tbody>
</table>
</div>


<div style="padding:15px 0px;">
<h3>Voucher Details</h3>
<table style="background:#FFFFEF;padding:5px;" cellpadding=4>
<tr class="aftrvload"><td>Voucher Value</td><td>Rs <input size=9 type="text" class="vvalue" name="vvalue" data-required="true"></td></tr>
<tr class="aftrvload"><td>Payment Mode</td><td>
<select name="mode" data-required="true">
<option value="1">Cash</option>
<option value="2">Cheque</option>
<option value="3">DD</option>
<option value="4">Bank Transfer</option>
</select>
</td></tr>
<tr class="aftrvload"><td>Instrument No</td><td><input type="text" name="inst_no"></td></tr>
<tr class="aftrvload"><td>Instrument Date</td><td><input type="text" name="inst_date" class="idate"></td></tr>
<tr class="aftrvload"><td>Issued Bank</td><td><input type="text" name="bank"></td></tr>
<tr class="aftrvload"><td>Narration</td><td><input type="text" name="narration" style="width:250px;" data-required="true"></td></tr>
</table>
</div>

<input type="submit" value="Create Voucher" class="button button-rounded button-action button-small" style="float: right;">

</form>

<div style="display:none">
<div id="grn_template">
<table>
<tbody>
<tR>
<td><a href="<?=site_url("admin/viewgrn")?>/%grnno%" target="_blank">GRN%grnno%</a><input type="hidden" name="grns[]" value="%grnno%"></td>
<td>Rs <input type="text" class="inp readonly" name="adjusted_grn[]" value="%grnamount%" size=8 readonly="readonly"></td>
<td>%pos%</td>
<td>%grndate%</td>
</tR>
</tbody>
</table>
</div>
<div id="po_template">
<table>
<tbody>
<tR>
<td><a target="_blank" href="<?=site_url("admin/viewpo")?>/%pono%">PO%pono%</a><input type="hidden" name="pos[]" value="%pono%"></td>
<td>Rs <input type="text" class="inp" name="adjusted_po[]" value="%poamount%" size=5></td>
<td>%podate%</td>
</tR>
</tbody>
</table>
</div>
</div>
</div>

<style>
.aftrvload{
}
</style>

<script>
var grns=[],pos=[];
function addgrn(grn,amount,date,pos)
{
	if($.inArray(grn,grns)!=-1)
	{
		alert("GRN already added");
		return;
	}
	$("#vendor_sel").attr("disabled",true);
	$(".vendor_list").hide();
	$(".venl"+$("#vendor_sel").val()).show();
	temp=$("#grn_template tbody").html();
	temp=temp.replace(/%grnno%/g,grn);
	temp=temp.replace(/%grnamount%/g,amount);
	temp=temp.replace(/%grndate%/g,date);
	pos=pos.split(",");
	pos_str="";
	for(i=0;i<pos.length;i++)
		pos_str+='<a target="_blank" href="<?=site_url("admin/viewpo")?>/'+pos[i]+'">PO'+pos[i]+'</a><br>';
	temp=temp.replace(/%pos%/g,pos_str);
	$("#grns tbody").append(temp);
	$("#grns").show("slow");
	grns.push(grn);
}
function addpo(po,amount,date)
{
	if($.inArray(po,pos)!=-1)
	{
		alert("GRN already added");
		return;
	}
	pos.push(po);
	$("#vendor_sel").attr("disabled",true);
	$(".vendor_list").hide();
	$(".venl"+$("#vendor_sel").val()).show();
	temp=$("#po_template tbody").html();
	temp=temp.replace(/%pono%/g,po);
	temp=temp.replace(/%poamount%/g,amount);
	temp=temp.replace(/%podate%/g,date);
	$("#pos tbody").append(temp);
	$("#pos").show("slow");
}
$(function(){


	$(".static_pos a").click(function(){
			$("td",$($(this).parents("td").get(0)).parent()).hide();
			$($(this).parents("td").get(0)).show();
			vid=$("span.vid",$(this).parents("td").get(0)).text();
			$("#vendor_sel").val(vid).attr("disabled",true);
			$("#load_grn").click();
			$("#load_grn").attr("disabled",true);
	});

	
	
	$(".idate").datepicker();
	$("#vendor_sel").attr("disabled",false);
	$("#load_grn").click(function(){
		$(".ajax_loadresult table").show();
		$.post("<?=site_url("admin/jx_loadforvoucher")?>",{type:1,vendor:$("#vendor_sel").val()},function(data){
			$("#grn_res").html(data);
		});
		$("#load_po").click();
	}).attr("disabled",false);
	$("#load_po").click(function(){
		$.post("<?=site_url("admin/jx_loadforvoucher")?>",{type:2,vendor:$("#vendor_sel").val()},function(data){
			$("#pos_res").html(data);
		});
	}).attr("disabled",false);
	$("#voucherfrm").submit(function(){
		if(grns.length==0 && pos.length==0)
		{
			alert("No GRNs or POs loaded");
			return false;
		}
		var voucher_frm=$("#voucherfrm");
		if(voucher_frm.parsley('validate'))
		{
			 return confirm("Are you sure?");
		}
		
		
	});
});
</script>

<?php
