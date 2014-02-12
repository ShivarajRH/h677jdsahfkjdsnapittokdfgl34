<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/grn.css" />
<?php
	$user = $this->erpm->auth(); 
	$grn['po_id'] = $this->db->query("select group_concat(distinct po_id) as po_id from t_grn_product_link where grn_id = ? ",$grn['grn_id'])->row()->po_id;
	$grn_date = $this->db->query("select created_on from t_grn_product_link where grn_id = ? ",$grn['grn_id'])->row()->created_on;
	$grn_value = $this->db->query("select sum(received_qty*purchase_price) as grn_val from t_grn_product_link where grn_id = ? ",$grn['grn_id'])->row()->grn_val;
?>
<div class="container">
<div>
<h2>Stock Intake Details(GRN<?=$grn['grn_id']?>)</h2>
  <span style="float: right;padding: 1px 17px 5px 7px;;margin-top: -41px;" ><b style="font-size: 15px;">Status:</b>
<?php switch($grn['payment_status']){
	case 0: echo '<b>'."Un Accounted".'</b>';?>&nbsp;<a href="<?=site_url("admin/account_grn/{$grn['grn_id']}")?>" class="button button-rounded button-action button-small">Account</a> <?php break;
	case 1: echo '<b>'."Accounted, ready for payment".'</b>';?>&nbsp;<a href="<?=site_url("admin/create_voucher")?>" class="button button-rounded button-action button-small">make payment</a> <?php break;
	case 2: echo '<b>'."Payment made".'</b>';break;
}?>
</span>
<!--<span style="float: right;padding: 1px 17px 5px 7px;margin-top: -41px;"">
<b>Status:</b>
<?php $grn_pmt_status_arr=array("UnAccounted","Accounted ready for payment","Payment made");?>
</span>-->
</div>


<div width="50%">
<table width="100%">
<tr>
	<td>
		<fieldset style="height: 200px;width: 450px;">
			<legend><b>GRN Info</b></legend>
			<table cellpadding="5">
				<tr><td>Vendor</td><td>|</td><td><a href="<?=site_url("admin/vendor/{$grn['vendor_id']}")?>"><?=$grn['vendor_name']?></a></td></tr>
				<tr><td>GRN Id</td><td>|</td><td><?=$grn['grn_id']?></td></tr>
				<tr><td>GRN Value</td><td>|</td><td><?=format_price($grn_value)?></td></tr>
				<tr><td>Stock Taken By</td><td>|</td><td><?=$this->db->query("select a.name from king_admin a join t_stock_update_log l on l.grn_id=? where a.id=l.created_by",$grn['grn_id'])->row()->name?></td></tr>
				<tr><td>Stock Taken On</td><td>|</td><td><?=format_datetime($this->db->query("select l.created_on from king_admin a join t_stock_update_log l on l.grn_id=? where a.id=l.created_by",$grn['grn_id'])->row()->created_on)?></td></tr>
				<tr><td>Remarks </td><td>|</td><td><?=$grn['remarks']?></td></tr>
			</table>
		</fieldset>
	</td>
	<td>
		<fieldset style="height: 200px;width: 300px;">
			<legend><b>Payment Terms</b></legend>
			<table cellpadding="5">
				<tr><td>Credit Days</td><td>|</td><td><?=$grn['credit_days'].' '.Days ?></td></tr>
				<tr><td>Payment Type</td><td>|</td><td><?$pmt_typs=array("Not Defined","Cheque","Cash","DD"); echo $pmt_typs[$grn['payment_type']]?></td></tr>
			</table>
		</fieldset>
	</td>
	<td>
		<fieldset >
			<legend><b>PO List</b></legend>
			<table border="1" style="border-collapse: collapse;width: 100%" cellpadding="5">
				<thead><th>PO Id</th><td>PO Value</td><td>Created On</td><td>Created By</td></thead>
				<tbody>
			<?php 
				$grn_polist_res = $this->db->query("select a.*,sum(order_qty*(purchase_price)) as po_value from (
								select a.po_id,b.created_on as po_created,ifnull(c.username,'') as po_createdby
									from t_grn_product_link a
									join t_po_info b on a.po_id = b.po_id 
									left join king_admin c on c.id = b.created_by
									where grn_id = ? 
								group by po_id ) as a 
								join t_po_product_link b on a.po_id = b.po_id 
								group by b.po_id ",$grn['grn_id']);
				if($grn_polist_res->num_rows())
				{
					foreach($grn_polist_res->result_array() as $grn_podet)
					{
			?>
				<tr>
					<td><a href="<?=site_url("admin/viewpo/{$poinfo['po_id']}")?>"><?=$grn_podet['po_id']?></a></td>
					<td>Rs&nbsp;&nbsp;<?=format_price($grn_podet['po_value'])?></td>
					<td><?=format_datetime($grn_podet['po_created'])?></td>
					<td><?=$grn_podet['po_createdby']?$grn_podet['po_createdby']:'-na-'?></td>
				</tr>
			<?php 	
					} 
				}
			?>	
			</table>
		</fieldset>
	</td>
	
</tr>
</table>

</div>

<div class="clear"></div><br>
<div class="tabs-view">
<ul>
<li><a href="#stck_intake_polist"><b>Products List</b></a></li>
<li><a href="#grn_invoice_info"><b>Invoice Details</b></a></li>
<li><a href="#voucher_info"><b>Voucher Details</b></a></li>

</ul>
<div id="stck_intake_polist">

<table class="datagrid nofooter" width="100%" id="grn_prod_list">
<thead><th>Sno</th><th>Product</th><th style="text-align: left">PO ID</th><th  style="text-align: right">Invoiced <br>Qty</th><th  style="text-align: right">Received <br>Qty</th><th  style="text-align: right">MRP</th><th  style="text-align: right">DP Price</th><th  style="text-align: right">Base Price</th><th  style="text-align: right">Tax</th><th  style="text-align: right">Margin</th><th  style="text-align: right">Scheme discount</th><th  style="text-align: right">Purchase Price</th><th  style="text-align: right">SubTotal</th></thead>
<?php $sno=0; foreach($prods as $p){
	$total_inv_qty += $p['invoice_qty'];
	$total_rcvd_qty += $p['received_qty'];
	$total_pprice += $p['received_qty']*$p['purchase_price'];
	$st_prod_val = round(($p['purchase_price']*$p['received_qty']),2);
	?>
<tr>
<td><?=++$sno?></td>
<td ><a target="_blank" href="<?php echo site_url('admin/product/'.$p['product_id']) ?>"><?=$p['product_name']?></a></td>
<td><a href="<?=site_url("admin/viewpo/{$p['po_id']}")?>" target="_blank">PO<?=$p['po_id']?></a></td>
<td align="right"><?=$p['invoice_qty']?></td>
<td align="right"><?=$p['received_qty']?></td>
<td align="right"><?=$p['mrp']?></td>
<td align="right"><?=$p['dp_price']?></td>
<td class="hide"  align="right"><?=$p['purchase_price']-($p['purchase_price']*$p['tax_percent']/100)?></td>
<td align="right"><?=$p['tax_percent']?></td>
<td align="right"><?=$p['margin']?>%</td>
<td align="right"><?=$p['scheme_discount_value']?>%</td>
<td align="right"><?=$p['purchase_price']?></td>
<td align="right"><?=$st_prod_val?></td>
</tr>
<?php }?>
<!--  <tr class="tbl_stats_row">
		<td colspan="3" align="right">Total</td>
		<td align="right"><?php echo $total_inv_qty; ?></td>
		<td align="right"><?php echo $total_rcvd_qty; ?></td>
		<td colspan="4">&nbsp;</td>
		<td align="right"><?php echo format_price($total_pprice,4); ?></td>
		<td colspan="4">&nbsp;</td>
	</tr>-->
</tbody>
 <tfoot>
<tr>
	<td align="right" colspan="4" style="text-align: left !important;"><b>Total Inv.Qty : <?=$ttl_in_stk_qty['invoice_qty']?></b></td>
	<td  style="text-align: left !important;"><b>Total Rec.Qty: <?=$ttl_in_stk_qty['received_qty']?></b></td>
	<td colspan="8" style="text-align: right !important;"><b>Total Purchase Value : Rs&nbsp;&nbsp;<?=format_price($total_pprice)?></b></td>
</tr>

</tfoot>
</table>
<div style="text-align: right;padding-top:5px;">
	<a href="javascript:void(0)" class="button button-tiny button-info" onclick="print_grndoc()">Print</a> 
</div>
</div>

<div id="grn_invoice_info">

<table id="grn_inv_list" class="datagrid">
<thead><tr><th>Invoice No</th><th>Invoice date</th><th>Invoice Value</th><th class="hideinprint">Scan Copy</th></thead>
<tbody>
<?php foreach($invoices as $inv){?>
<tr>
<td><?=$inv['purchase_inv_no']?></td>
<td><?= format_date($inv['purchase_inv_date'])?></td>
<td><?=$inv['purchase_inv_value']?></td>
<td class="hideinprint"><?php if(file_exists(ERP_PHYSICAL_IMAGES."invoices/{$inv['id']}.jpg")){?>
<a target="_blank" href="<?=ERP_IMAGES_URL?>invoices/<?=$inv['id']?>.jpg">view</a><?php } else {?> <input type='file' name='scan_0' class='scan_file'>&nbsp;<input type='submit' value='upload' action='<?php echo site_url("admin/do_upload_scan_invoice/{$inv['grn_id']}")?>'><?php }?>
</td>
</tr>
<?php }?>
</tbody>
</table>
</div>

<div id="voucher_info">
<table class="datagrid" width="100%">
<thead><tr><th>Voucher</th><th>Total Value</th><th>Adjusted amount for GRN</th><th>Created on</th><th>Created by</th></tr></thead>
<tbody>
<?php  if($vouchers){foreach($vouchers as $v){?>
<tr>
<td><a href="<?=site_url("admin/voucher/{$v['voucher_id']}")?>"><?=$v['voucher_id']?></a></td>
<td>Rs <?=$v['voucher_value']?></td>
<td>Rs <?=$v['adjusted_amount']?></td>
<td><?=$v['created_on']?></td>
<td><?=$v['created_by']?></td>

<?php }?>
<?php }else {?>
<td><div align="center"><b>No Records Found</b></div></td>
<?php }?>
</tr>
</tbody>
</table>
</div>

</div>







</div>
<script>
$(".tabs-view").tabs();


function print_grndoc()
{
	$('#grn_inv_list tfoot').hide();
	var grninvhtml = '<table border=1 cellpadding=2 cellspacing=0 style="font-size:10px;">'+$('#grn_inv_list').html()+'</table>';
	$('#grn_inv_list tfoot').show();
	var html = '<div><style> body{font-size:12px;font-family:arial;} .hideinprint{display:none}</style> <h2 align="center">GRN Document</h2> <div> <b style="float:right"> <br> Printed On : <?php echo format_datetime_ts(time());?> <br> '+grninvhtml+'  </b> <b style="font-size:14px;">GRN: #<?=$grn['grn_id']?> - (<?php echo format_datetime($grn_date); ?>) <br> Vendor: <?=$grn['vendor_name']?> <br> PO: #<?=$grn['po_id']?> </b>  </div><table cellpadding=5 cellspacing=0 border=1 width="100%" style="font-size:12px;font-family:arial;">'+$('#grn_prod_list').html()+'</table></div>';
		prw=window.open("",'');
		prw.document.write(html);
		prw.focus();
		prw.print();
}

/*$(function(){
	$('#grn_prod_list').jq_fix_header_onscroll();
});*/
</script>

<style>
/*#grn_prod_list th{text-align:right;}*/
.tbl_stats_row td{background:#ffffD0 !important}
</style>