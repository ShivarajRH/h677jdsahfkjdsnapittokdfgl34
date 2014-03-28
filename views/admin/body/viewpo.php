<?php 
	$user=$this->erpm->auth(PURCHASE_ORDER_ROLE);
	//check if po has grn
	$has_grn=0;
	if($this->db->query("select * from t_grn_product_link where po_id=?",$po['po_id'])->num_rows()!=0)
	$has_grn=1;
	?>
<?php $po_status_arr=array();
$po_status_arr[0]="Open";
$po_status_arr[1]="Partially Received";
$po_status_arr[2]="Complete";
$po_status_arr[3]="Cancelled";?>
<div class="container">
	<span class="fl_right">
		<?php if($po['po_status']!="2" && $po['po_status']!="3"){?>
			<a onclick='closepo( )' href="javascript:void(0)" class="button button-tiny button-caution button-rounded" >Close PO</a>
		<?php } ?>
		<?php if($po['po_status']!="3"){?>
		<a class="button button-tiny" onclick="print_po(<?=$po['po_id']?>)" style="cursor: pointer;">Print po</a>
		<?php }?>
	</span>
	<h2>Purchase Order : <?=$po['po_id']?></h2>

<fieldset style="width: 70%;">
<legend><b>PO Details</b></legend>
<table>
<tr>
	<td width="45%">
		<div width="50%">
			<table cellspacing="5" width="100%">
				<tbody>
					<tr><td><b>Supplier</b></td><td>|</td><td><a target="_blank" href="<?php echo site_url("/admin/vendor/{$po['vendor_id']}")?>"><?=$po['vendor_name'] ?></a></td></tr>
					<tr><td><b>Purchase Order</b></td><td>|</td><td><?=$po['po_id'] ?></td></tr>
					<tr><td><b>Created Date</b></td><td>|</td><td><?=format_date($po['created_on'] )?></td></tr>
					<?php if($po['date_of_delivery'] && $po['remarks']){?>
					<tr><td><b>Scheduled Date</b></td><td>|</td><td><?=format_date($po['date_of_delivery'] )?></td></tr>
					<tr><td><b>Remarks</b></td><td>|</td><td><?=$po['remarks'] ?></td></tr>
					<?php }?>
					<tr><td><b>Created By</b></td><td>|</td><td><?=$po['created_byname'] ?></td></tr>
					<?php if(!$po['date_of_delivery'] || !$po['remarks']){?>
					<tr><td><input onclick='update_po_det(<?php echo $po['po_id'] ?>)' class="update_link button-rounded button button-flat-caution button-small" value="Update Remarks" style="cursor: pointer;"></td></tr>
					<?php }?>
				</tbody>
			</table>
		</div>
	</td>
	<td width="45%">
		<div width="50%" style="float:right;margin-left:117px;">
			<table cellspacing="5" width="100%">
			<tbody>
			<tr><td><b>Po value</b></td><td>|</td><td>Rs <?=format_price($ttl_po_val['total_value'])?></td></tr>
			<tr><td><b>Status</b></td><td>|</td>
			<td>
			<?if($po['po_status']==0){?>
			<span style="color: orange"><b><?php echo $po_status_arr[$po['po_status']]?></b></span>
			
			<?php }elseif($po['po_status']==3){?>
			<span style="color: red"><b><?php echo $po_status_arr[$po['po_status']] ?></b></span>
			<?php }else{?>
			<span><b><?php echo $po_status_arr[$po['po_status']] ?></b></span>
			<?php }?>
			</td></tr>
			
			<tr>
				<td>Notify By Mail</td>
				<td>|</td>
				<td>
					<?php 
						$vendor_email_res = $this->db->query("select concat(email_id_1,',',email_id_2) from m_vendor_contacts_info where vendor_id = ? and (email_id_1 != '' or email_id_2 != '') limit 1",$po['vendor_id']);
						if($vendor_email_res->num_rows())
						{
							echo $po['notify_vendor']?'Notified':'<a href="javascript:void(0)" id="notify_vendorbymail" class="button button-tiny button-action">Notify</a>';
						}else
						{
							echo "<b>Vendor Email not found</b>";
						}
					?>
				</td>
			</tr>
			<?php if($po['modified_on']!=null){?>
			<tr><td><b>Updated on</b></td><td>|</td><td><?=format_date($po['modified_on']) ?></td></tr>
			<tr><td><b>Status Remarks</b></td><td>|</td><td><?=$po['status_remarks']?></td></tr>
			<tr><td><b>Updated by</b></td><td>|</td><td><?=$po['modified_byname'] ?></td></tr>
			<?php }?>
			</tbody>
			</table>
		</div>
	</td>
</tr>
</table>
</fieldset>

<div class="tab_view ">
<ul>
<li><a href="#po_list"><b>Product List</b></a></li>
<li><a href="#po_removedlist"><b>Removed Product List</b></a></li>
<?php if($has_grn == '1') {?><li><a href="#grn_info"><b>Grn Details</b></a></li><?php }?>
</ul>
<div id="po_list">
<table class="datagrid nofooter" width="100%">
<thead>
	<tr>
	<th>Slno</th>
	<th>Product Name</th>
	<th>Order Qty</th>
	<th style="text-align:center">Received Qty</th>
	<th>MRP</th>
	<th>DP Price</th>
	<th>Margin</th>
	<th>Scheme Discount</th>
	<th style="text-align:right;">Unit Price</th>
	<th style="text-align:right;">Sub Total</th>
	<th></th>
</tr>
</thead>
<tbody>
<?php $sno=1; foreach($items as $i){
	
?>
<tr>
<td><?=$sno++?></td>
<td><a href="<?=site_url("admin/product/{$i['product_id']}")?>"><?=$i['product_name']?></a></td>
<td><?=$i['order_qty']?></td>
<td align="center"><?=$i['received_qty']?></td>
<td><?=$i['mrp']?></td>
<td><?=$i['dp_price']?></td>
<td><?=$i['margin']?>%</td>
<td><?=$i['scheme_discount_value']?$i['scheme_discount_value']:0?>%</td>
<td style="text-align:right;"><?=format_price($i['purchase_price'])?></td>
<td style="text-align:right;"><?=format_price($i['purchase_price']*$i['order_qty'])?></td>
<?php if($i['received_qty']==0){?>
<td style="text-align:right;"><a href="javascript:void(0)" onclick="remove_prod_frmpo(<?php echo $i['product_id']?>)" prodid=<?php echo $i['product_id'];?> ><img  src="<?php echo base_url().'images/icon_delete13.gif'?>"></a></td>
<?php }?>
</tr>
<?php }?>
</tbody>
<tfoot class="nofooter">
<tr>
	<td align="right" colspan="3" style="text-align: right !important;padding-right: 62px !important"><b>Total O.Qty : <?=$ttl_po_val['total_qty']?></b></td>
<td style="text-align: right !important;padding-right: 59px !important"><b>Total : <?=$ttl_po_val['received_qty']?></b></td>
<td colspan="7" style="text-align: right !important;padding-right: 55px !important"><b>Total Purchase Value : Rs&nbsp;&nbsp;<?=format_price($ttl_po_val['total_value'])?></b></td>
	</tr>
</tfoot>
</table>
<br>

</div>

<div id="po_removedlist">
<table class="datagrid" width="100%">
<thead>
<th>Slno</th>
<th>Product Name</th>
<th>Order Qty</th>
<th>Received Qty</th>
<th>MRP</th>
<th>DP Price</th>
<th>Margin</th>
<th>Scheme Discount</th>
<th>Unit Price</th>
<th>Sub Total</th>
<th>Removed By</th>
<th>Removed on</th>
</thead>
<tbody>
<?php $sno=1;if($removed_poprod_list){foreach($removed_poprod_list as $r_po){?>
<tr>
<td><?=$sno++?></td>
<td><a href="<?=site_url("admin/product/{$r_po['product_id']}")?>"><?=$r_po['product_name']?></a></td>
<td><?=$r_po['order_qty']?></td>
<td><?=$r_po['received_qty']?></td>
<td><?=$r_po['mrp']?></td>
<td><?=$r_po['dp_price']?></td>
<td><?=$r_po['margin']?>%</td>
<td><?=$r_po['scheme_discount_value']?$i['scheme_discount_value']:0?>%</td>
<td><?=format_price($r_po['purchase_price'])?></td>
<td><?=format_price($r_po['purchase_price']*$r_po['order_qty'])?></td>
<td><?=$r_po['modifiedby'] ?></td>
<td><?=format_datetime($r_po['modified_on'])?></td>
</tr>
<?php }} else{ echo "<tr><td colspan='12'><div align='center'><b>No Data Found</b></div></td></tr>";}?>

</tbody>
</table>
</div>
<?php if($has_grn == '1') {?>
<div id="grn_info">
<table class="datagrid" width="100%">
<thead><th>Slno</th><th>Grn ID</th><th>Product</th><th>Invoiced Qty</th><th>Received Qty</th><th>MRP</th><th>Tax</th><th>Purchase Price</th><th>Invoice</th><th>Amount</th><th>Accounted Status</th><th>Stock Intake By</th><th>Created on</th></thead>
<tbody>
<?php if($grns){ $i=1;foreach($grns->result_array() as $grn){?>
<tr><td><?=$i++;?></td><td><a target="_blank" href="<?php echo site_url("admin/viewgrn/{$grn['grn_id']}")?>"><?=$grn['grn_id']?></a></td><td><a href="<?=site_url("admin/product/{$grn['product_id']}")?>"><?=$grn['product_name']?></a></td><td><?=$grn['invoice_qty']?></td><td><?=$grn['received_qty']?></td><td><?=$grn['mrp']?></td><td><?=$grn['tax_percent']?></td><td><?=$grn['purchase_price']?></td><td><?=$grn['purchase_inv_no']?></td><td><?=$grn['purchase_inv_value']?></td><td><b><?=$grn['payment_status']==0?'UnAccounted':'Accounted';?></b></td><td><?=$this->db->query("select a.name from king_admin a join t_stock_update_log l on l.grn_id=? where a.id=l.created_by",$grn['grn_id'])->row()->name?></td><td><?=format_datetime($grn['created_on'])?></td></tr>
<?php }?>
<?php }?>
</tbody>
</table>
</div>
<?php }?>
</div>

<div id="status_rmrks_div" title="Reason for Closing Purchase order">
<form action="<?php echo site_url("admin/closepo/{$po['po_id']}")?>" method="post" data-validate="parsley" id="remrks_update_frm">
	<textarea name="status_remarks"  style="width: 100%;height: 100px;" data-required="true"></textarea>
</form>
</div>

<div id="update_po_delivery_det" title="Update Expected Delivery Details" style="display:none;">
<form method="post" action="<?php echo site_url('admin/updatedeliverydate/'.$po['po_id'])?>" id="delivery_det_frm"  data-validate="parsley" >
<table cellpadding="5">
<tr><td valign="top">Expected Delivery Date</td><td><input type="text" name="po_deliverydate" id="po_deliverydate" value="" data-required="true" readonly="readonly"></td></tr>
<tr><td valign="top">Remarks</td><td><textarea name="po_remarks" value="" data-required="true" style="width:303px;height:140px;"></textarea></td></tr>
</table>
</form>
</div>
<?php 
	$email_tmpl = 'Dear '.$po['vendor_name'].',

PFA of Purchase Order : #'.$po['po_id'].'



Regards
'.$user['email'];
?>
<div style="display: none">
	<div id="notify_vendorbymail_dlg" title="Send Email Notification to Vendor">
		<form action="<?php echo site_url('admin/jx_notifypobymail')?>" method="post">
			<input type="hidden" name="poid" value="<?=$po['po_id'] ?>">
			<table style="border-collapse: collapse;background: #f1f1f1;" width="100%" cellpadding="5" cellspacing="0">
				<tr>
					<td width="100"><b>PO no</b><br><span id="notify_vendorbymail_poid"><?=$po['po_id'] ?></span></td>
				</tr>
				<tr>
					<td><b>Vendor</b><br><span id="notify_vendorbymail_venname"><?=$po['vendor_name'] ?></span></td>
				</tr>
				<tr>
					<td><b>Subject</b><br><input type="text" name="notify_vendorbymail_subject" value="Storeking Purchase Order:<?=$po['po_id'] ?>"  id="notify_vendorbymail_subject" style="width: 99%"></td>
				</tr>
				<tr>
					<td valign="top"><b>Message</b><br><textarea name="notify_vendorbymail_message" id="notify_vendorbymail_message" style="width: 99%;height:150px;"><?php echo $email_tmpl;?></textarea></td>
				</tr>
			</table>
		</form>
	</div>
</div>

<script>
$('.leftcont').hide();
$('.tab_view').tabs();
function update_po_det(po_id)
{
	$('#update_po_delivery_det').data('po_id',po_id).dialog('open');
}

$('#notify_vendorbymail').click(function(){
	$('#notify_vendorbymail_dlg').dialog('open');
});

$('#notify_vendorbymail_dlg').dialog({
	modal:true,
	autoOpen:false,
	autoResize:true,
	width:'600',
	height:'auto',
	open:function(){
		
	},
	buttons:{
		'Cancel' :function(){
		 	$(this).dialog('close');
		},
		'Submit':function(){
			$(this).dialog('close');
			 $.post($('form',this).attr('action'),$('form',this).serialize(),function(resp){
				location.href=location.href; 
			 },'json');
		},
	}
});


$('#update_po_delivery_det').dialog({
	modal:true,
	autoOpen:false,
	autoResize:true,
	width:'484',
	height:'320',
	open:function(){
		
	},
	buttons:{
		'Cancel' :function(){
		 $(this).dialog('close');
		},
		'Submit':function(){
			var dlg= $(this);
			var frm_podetails = $("#delivery_det_frm",this);
				 if(frm_podetails.parsley('validate')){
						frm_podetails.submit();
					 $("#delivery_det_frm").dialog('close');
				}
	            else
	            {
	            	alert('All Fields are required!!!');
	            }
		},
	}
});
$("#po_deliverydate").datepicker({ minDate: 0 });
	
function closepo()
{
	/*if(confirm("Are you sure?"))
		location="<?//=site_url("admin/closepo/{$po['po_id']}")?>";*/
	$("#status_rmrks_div").dialog('open');
}
$("#status_rmrks_div").dialog({
modal:true,
autoOpen:false,
width:400,
height:'auto',
open:function(){
},
buttons:{
	'Cancel':function(){
		$(this).dialog('close');
	},
	'Submit':function(){
		var dlg= $(this);
		var status_rmrks_frm = $("#remrks_update_frm",this);
			 if(status_rmrks_frm.parsley('validate')){
				 status_rmrks_frm.submit();
				 $("#status_rmrks_div").dialog('close');
			}
            else
            {
            	alert('All Fields are required!!!');
            }
	},
	
}
});

function updateexpected_podeliverydate()
{
	if(confirm("Are you sure ?"))
		location="<?=site_url("admin/updatedeliverydate/{$po['po_id']}")?>";
}

function print_po(poid)
{
	var print_url = site_url+'/admin/print_po/'+poid;
		window.open(print_url);
}

function remove_prod_frmpo(pid)
{
	var prodid=pid;
	if(confirm("Are you sure  want to remove this product from PO?"))
		location="<?=site_url('admin/remove_prodfrmpo/'.$po['po_id'])?>/"+prodid;
}
</script>
 
