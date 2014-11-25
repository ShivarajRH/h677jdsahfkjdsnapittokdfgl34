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
	$po_status_arr[3]="Cancelled";
	$po_status_arr[4]="Pending for Approval";
?>
<style>

.prd_orders {
    background: none repeat scroll 0 0 #ccc;
    display: inline-block;
    margin: 8px 4px;
    padding: 5px 9px;
   color:green;font-weight:bold;
 }
 .prd_sales_orders {
    background: none repeat scroll 0 0 #ccc;
    display: inline-block;
    margin: 8px 4px;
    padding: 5px 9px;
   color:green;font-weight:bold;
 }
 .row_select
{
	background:#F5F592;
}
</style>

<div class="container">
	<span class="fl_right">
		<?php if($po['po_status']!="2" && $po['po_status']!="3" && $po['is_doa_po']=="0"){?>
			<a onclick='closepo( )' href="javascript:void(0)" class="button button-tiny button-caution button-rounded" >Close PO</a>
		<?php } ?>
		<?php 
			if($po['po_status']!="3"){?>
				<a class="button button-tiny" onclick="export_po(<?=$po['po_id']?>,'api_ven')" style="cursor: pointer;">Export CSV</a>
				<a class="button button-tiny" onclick="print_po(<?=$po['po_id']?>,'acct')" style="cursor: pointer;">Print Accounts Copy</a>
				<a class="button button-tiny" onclick="print_po(<?=$po['po_id']?>,'sour')" style="cursor: pointer;">Print Sourcing Copy</a>
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
					
					<?php 
						if($po['po_status'] == 4)
						{
					?>
							<br><a href="<?php echo site_url('admin/push_potovendor/'.$po['po_id'])?>" >Approve and send PO</a>
					<?php 		
						}
					?>
					
					<?php }?>
					</td></tr>
					
					<tr>
						<td>Notify Vendor</td>
						<td>|</td>
						<td>
							<?php 
								if($po['po_status'] == 4)
								{
									echo "<b>Approval Pending...</b>";
								}else if($po['po_status'] != 3)
								{
								$vendor_email_res = $this->db->query("select concat(email_id_1,',',email_id_2) from m_vendor_contacts_info where vendor_id = ? and (email_id_1 != '' or email_id_2 != '') limit 1",$po['vendor_id']);
								if($vendor_email_res->num_rows())
								{
									echo $po['notify_vendor']?'Notified':'<a href="javascript:void(0)" id="notify_vendorbymail" class="button button-tiny button-action">Notify</a>';
								}else
								{
									echo "<b>Vendor Email not found</b>";
								}
								}else
								{
									echo "Cancelled PO";
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

<div style="text-align: right"><input class="duplicate_po button-rounded button button-flat-action button-small" value="Duplicate PO" style="cursor: pointer;"></div>

<div class="tab_view">
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
				<th width="350" style="text-align:left">Product Name</th>
				<th style="text-align: right">Available <br> Qty</th>
				<th style="text-align: right" >Required <br> Qty</th>
				<th style="text-align: right">PO <br> Order <br> Qty</th>
				<th style="text-align: right">Received <br> Qty</th>
				<th style="text-align: right">MRP</th>
				<th style="text-align: right">DP Price</th>
				<th style="text-align: right">Margin</th>
				<th style="text-align: right">Scheme <br> Discount</th>
				<th style="text-align:right;">Unit Price</th>
				<th style="text-align:right;">Sub Total </th>
				<th><span class="select_po"></span></th>
			</tr>
			</thead>
			<tbody>
				<?php $sno=1; foreach($items as $i){
					$partner_sales_30days=$this->partner->get_partner_sales($i['product_id'],$i['created_on'],1); // 1 months sale
					$partner_sales_60days=$this->partner->get_partner_sales($i['product_id'],$i['created_on'],2); // 2 months sale
					
					$i['sales_60days']=$this->erpm->get_sales_count($i['product_id'],$i['created_on'],2); // from 2 month sales
					$i['sales_30days']=$this->erpm->get_sales_count($i['product_id'],$i['created_on'],1); // from 1 month sales
					
					$i['pen_ord_qty']=$this->db->query("select ifnull(sum(o.quantity*l.qty),0) as s from m_product_deal_link l join king_orders o on o.itemid=l.itemid where l.product_id=? and o.status = 0 and o.time < ? ",array($i['product_id'],strtotime($po['created_on'])))->row()->s;
					
					$prd_stock = $this->erpm->get_product_stock($i['product_id']);
					$i['cur_avail_qty'] = $prd_stock['current_stock'];
					
					$partner_sale_msg1='';
					$partner_sale_msg2='';
					
					if($partner_sales_30days > 0)
					{
						//$partner_sale_msg1=' (P: '.($partner_sales_30days*1).')';
					}
					if($partner_sales_60days > 0)
					{
						$partner_sale_msg2=' (Partner: '.($partner_sales_60days*1).')';
					}
					
				?>
				<tr class="po_det_wrap" product_id="<?=$i['product_id']?>">
					<td><?=$sno++?></td>
					<td>
					<div><a href="<?=site_url("admin/product/{$i['product_id']}")?>" target="_blank"><?=$i['product_name']?></a><br>
					<span class="prd_sales_orders" product_id="<?=$i['product_id']?>">30 day Sales : <?=($i['sales_30days']*1). $partner_sale_msg1; ?></span>
					<span class="prd_sales_orders" product_id="<?=$i['product_id']?>">60 day Sales : <?=($i['sales_60days']*1). $partner_sale_msg2; ?></span>
					<?php
						$prd_orders=$this->db->query("select count(distinct b.id) as total 
														from king_transactions a 
														join king_orders b on a.transid = b.transid
														join m_product_deal_link l on b.itemid=l.itemid and l.is_active = 1 
														where b.status = 0  and l.product_id=?",$i['product_id'])->row()->total;
						
						if($prd_orders > 0)
						{
						?>
							<span class="prd_orders" product_id="<?=$i['product_id']?>">Pending Orders : <?=$prd_orders?></span>
							&nbsp;
							<span class="ord_blk_<?=$i['product_id']?>" style="display:none"></span>
						<?php	
						}
						
							$link = @$this->db->query("select vendor_site_link as l from m_vendor_product_link where product_id = ? ",$i['product_id'])->row()->l;
							if($link)
								echo '<div><a href="'.($link).'" style="font-size:10px;" target="_blank">View Vendor Product</a></div>';
						?>
							
					</td>
					<td align="right"><?=$i['cur_avail_qty']*1?></td>
					<td align="right"><?=$i['pen_ord_qty']*1?></td>
					<td align="right"><?=$i['order_qty']*1?></td>
					<td align="right"><?=$i['received_qty']?></td>
					<td style="text-align: right"><?=$i['mrp']?></td>
					<td style="text-align: right"><?=$i['dp_price']?></td>
					<td style="text-align: right"><?=$i['margin']?>%</td>
					<td style="text-align: right"><?=$i['scheme_discount_value']?$i['scheme_discount_value']:0?>%</td>
					<td style="text-align:right;"><?=format_price($i['purchase_price'])?></td>
					<td style="text-align:right;"><?=format_price($i['purchase_price']*$i['order_qty'])?></td>
				
				<?php if($i['received_qty']==0){?>
					<td align="center">
						<a href="javascript:void(0)" onclick="remove_prod_frmpo(<?php echo $i['product_id']?>)" prodid=<?php echo $i['product_id'];?> >
							<img  src="<?php echo base_url().'images/icon_delete13.gif'?>">
						</a>
						<span class="chk_action"></span>
					</td>
				<?php }else {
				?>	
					<td align="center"><span class="chk_action"></span></td>
				<?php }?>
				</tr>
				<?php }?>
			</tbody>
			<tfoot class="nofooter">
				<tr>
					<td align="left" colspan="4">Grand Totals</td>
					<td align="right" style="text-align: right !important;"><b><?=$ttl_po_val['total_qty']?></b></td>
					<td align="right" style="text-align: right !important;"><b><?=$ttl_po_val['received_qty']?></b></td>
					<td colspan="6" style="text-align: right !important;"><b>Total Purchase Value &nbsp;: &nbsp;Rs&nbsp;<?=format_price($ttl_po_val['total_value'])?></b></td>
				</tr>
			</tfoot>
		</table>
	<br>
		<div class="action_wrap" style="text-align: right;display:none">
			<button type="button" class="proceed_po button-rounded button button-action button-small" vendor="<?=$po['vendor_id'] ?>">Proceed</button>
			<button type="button" class="cancel_po button-rounded button button-caution button-small">Cancel</button>
		</div>	
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
	
	<form id="place_po_form" action="<?=site_url("admin/po_product_bytopsold")?>" method="post">
		<input type="hidden" name="po_arr" class="prd_arr" value="">
		<input type="hidden" name="vendor" class="sel_ven" value="">
	</form>
<?php 
	$email_tmpl = 'Dear '.$po['vendor_name'].',

	PFA of Purchase Order : #'.$po['po_id'].'



	Regards
'.$user['username'];
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
$(function(){
	$('.leftcont').hide();
	$('.tab_view').tabs();
});

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

function print_po(poid,type)
{
	window.open(site_url+'/admin/print_po/'+poid+'/'+type);	
}

function export_po(poid,type)
{
	window.open(site_url+'/admin/export_po/'+poid+'/'+type);	
}

function remove_prod_frmpo(pid)
{
	var prodid=pid;
	if(confirm("Are you sure  want to remove this product from PO?"))
		location="<?=site_url('admin/remove_prodfrmpo/'.$po['po_id'])?>/"+prodid;
}

$('.prd_orders').click(function(){
	pid=$(this).attr('product_id');
	$.getJSON(site_url+'/admin/get_product_pending_orderdetails/'+pid,function(resp){
			
    	if(resp.status == 'error')
			{
				alert("Order Detail not found");
				return false;
		    }
			else
			{
				
					var b_list = '';
					b_list+= '<table class="datagrid"><thead><th>Sl.No</th><th>TransID</th><th>Orderid</th><th>Created On</th><th>Created By</th></thead><tbody>';
					
					$.each(resp.order_list,function(i,b){
			 			b_list+= '<tr><td>'+(i+1)+'</td><td><a href="'+site_url+'/admin/trans/'+b.transid+'">'+b.transid+'</a></td><td>'+b.orderid+'</td><td>'+b.time+'</td><td>'+b.name+'</td></tr>';
					});
					b_list+= '</tbody></table>';
				//$('.jq_alpha_sort_alphalist_itemlist').html(b_list);
				$('.ord_blk_'+pid).html(b_list);
				$('.ord_blk_'+pid).show();
			}
    	});
});

$('.duplicate_po').live('click',function(){
	$('.action_wrap').show();
	$('.duplicate_po').hide();
	
	$('.po_det_wrap').each(function(){
		var trele=$(this);
		var pid=$(this).attr('product_id');
		$('span.chk_action',trele).html("<input type='checkbox' class='sel_po' product_id='"+pid+"'>");
		$('.select_po').html("<b>Select All <input type='checkbox' class='all'></b>");
	});
});

$('.cancel_po').live('click',function(){
	$('.action_wrap').hide();
	$('.duplicate_po').show();
	$('.po_det_wrap').each(function(){
		var trele=$(this);
		$('.chk_action',trele).html("");
		$('.select_po').html("");
	});
});

//Checkbox check and uncheck all action 
$('.all').live('click',function(){
	if($('.all').attr('checked'))
		$('.sel_po').attr('checked',true);
	else
		$('.sel_po').attr('checked',false);
	
	$('.sel_po').each(function(){
		var trEle=$(this).parents('tr:first');
		var chk=$(this).attr('checked');
		if(chk == 'checked')
			trEle.addClass('row_select');
		else
			trEle.removeClass('row_select');
	});
});

//Checkbox action for place po option
$('.sel_po').live('click',function(){
	var trEle=$(this).parents('tr:first');
	var chk=$(this).attr('checked');
	if(chk == 'checked')
		trEle.addClass('row_select');
	else
		trEle.removeClass('row_select');	
});

$('.proceed_po').live('click',function(){
	var po_product_arr=[];
	var vendor=$(this).attr('vendor');
	$('.sel_po').each(function(){
		var trEle=$(this).parents('tr:first');
		var chk=$(this).attr('checked');
		if(chk == 'checked')
		{
			var pid=$(this).attr('product_id');
			if($.inArray(pid, po_product_arr) === -1) 
			 	po_product_arr.push(pid);
		}
	});
	
	if(po_product_arr.length==0)
	{
		alert("Please select products before proceed");
		return false;
	}
	$('.sel_ven').val(vendor);
	$('.prd_arr').val(po_product_arr);
	$('#place_po_form').submit();
});	
</script>
