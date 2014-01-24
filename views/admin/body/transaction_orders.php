<style>.leftcont{display:none}</style>
<?php 
$cancelall=true;
$cancelsingle=false;
$order=$orders[0];
$processed=$p_processed=array();
$shipped_oids=array();
$shipped_orders=array();
$transid=$order['transid'];

$order_status_arr=array();
$order_status_arr[0]='Pending';
$order_status_arr[1]='Processed';
$order_status_arr[3]='Cancelled';


$sql_trans_ttls = 'select status,ifnull(amt1,amt2) as amt from (
select b.status,sum((mrp-(discount+credit_note_amt))*a.invoice_qty) as amt1,
	sum(i_orgprice-(i_coup_discount+i_discount)*b.quantity) as amt2
	from king_orders b 
	left join king_invoice a on a.order_id = b.id 
	where b.transid = ?
group by b.status )
as g';

$trans_order_status_amt = $this->db->query($sql_trans_ttls,$transid);

$trans_order_status_amt_arr = array();
$trans_order_status_amt_arr[0] = 0;
$trans_order_status_amt_arr[1] = 0;
$trans_order_status_amt_arr[2] = 0;
$trans_order_status_amt_arr[3] = 0;
$trans_order_status_amt_arr[4] = 0;

foreach($trans_order_status_amt->result_array() as $to_row)
{
	$trans_order_status_amt_arr[$to_row['status']] =  $to_row['amt'];
}

$fran_status_arr=array();
$fran_status_arr[0]="Live";
$fran_status_arr[1]="Permanent Suspension";
$fran_status_arr[2]="Payment Suspension";
$fran_status_arr[3]="Temporary Suspension";
$is_fran_suspended = @$this->db->query("select is_suspended from pnh_m_franchise_info where franchise_id=?",$tran['franchise_id'])->row()->is_suspended;

if($is_fran_suspended)
	$tran['batch_enabled'] = 0;


$pbatch=$this->db->query("select i.p_invoice_no,c.courier_name as courier,bi.shipped,bi.shipped_on,bi.awb,bi.courier_id,bi.batch_id,bi.packed,bi.shipped,i.createdon,i.invoice_status,bi.p_invoice_no from proforma_invoices i left outer join shipment_batch_process_invoice_link bi on bi.p_invoice_no=i.p_invoice_no left outer join m_courier_info c on c.courier_id=bi.courier_id where i.transid=? group by i.p_invoice_no",$transid)->result_array();

foreach($batch as $b)
{
	if($b['invoice_status']==1)
	foreach($this->db->query("select order_id from king_invoice where invoice_no=?",$b['invoice_no'])->result_array() as $i)
		$processed[$i['order_id']]=$b['invoice_no'];
}

foreach($pbatch as $b)
{
	if($this->db->query("select invoice_status as s from proforma_invoices where p_invoice_no=?",$b['p_invoice_no'])->row()->s==1)
	foreach($this->db->query("select i.order_id,o.status from proforma_invoices i join king_orders o on o.id=i.order_id where i.p_invoice_no=?",$b['p_invoice_no'])->result_array() as $i)
		if($i['status']!=0)
			$p_processed[$i['order_id']]=$b['p_invoice_no'];
}
?>
<div class="container transaction">
<div style="float:right;padding:5px;background:#ffaaaa;color:#000;margin:5px;min-width:300px;margin-top:-7px;border:1px dashed #000;">
<?php $user_msg=$this->db->query("select note from king_transaction_notes where transid=? and note_priority=1 order by id asc limit 1",$tran['transid'])->row_array();?>
<?=isset($user_msg['note'])?"<b>{$user_msg['note']}</b>":"<i>no user msg</i>"?>
</div>
<div style="float:right;padding:5px;background:#eea;color:#000;margin:5px;min-width:100px;margin-top:-7px;border:1px dashed #000;">
<?php $c=$this->db->query("select * from king_used_coupons where transid=?",$order['transid'])->row_array();
if(empty($c)) echo '<i>no coupon used</i>';
else {?>
Coupon used : <a href="<?=site_url("admin/coupon/{$c['coupon']}")?>"><?=$c['coupon']?></a>
<?php }?>
</div>
<div style="float:right;padding:5px;background:#FF6347;color:#000;margin:5px;min-width:100px;margin-top:-7px;border:1px dashed #000;">
<?php $c=$this->db->query("select group_concat(voucher_slno) as vslno from pnh_voucher_activity_log where transid=?",$order['transid'])->row()->vslno;
if(empty($c)) echo '<i>no Prepaid Voucher used</i>';
else {?>
Prepaid Voucher used : <?=$c?>
<?php }?>
</div>
<?php foreach($orders as $o){?>
<?php if(!isset($processed[$o['id']]) && $o['status']!=3 && !isset($p_processed[$o['id']])){?>
<?php $cancelsingle=true; } else $cancelall=false;?>
<?php }?>
<?php if($cancelsingle){?>
<div style="float:right;padding-right:20px;">
<form method="post" onsubmit='return confirm("Are you sure want to update batch status?")' action="<?=site_url("admin/endisable_for_batch/{$order['transid']}")?>">
	<input type="submit" class="button button-tiny button-flat-caution button-rounded " value="<?=$tran['batch_enabled']?"Dis":"En"?>able for Batch">
</form>
</div>
<?php if($tran['batch_enabled']){?>
<div style="float:right;padding-right:20px;" id="process_fulltrans">
<form method="post" onsubmit='return confirm("Are you sure want to process this transaction for batch?")' action="<?=site_url("admin/add_batch_process")?>">
<input type="hidden" name="num_orders" value="1">
<input type="hidden" name="transid" value="<?=$tran['transid']?>">
	<input type="submit" class="button button-tiny button-rounded " value="Process to Batch">
</form>
</div>
<div style="float:right;padding-right:20px;" id="process_parttrans">
<form method="post" onsubmit='return confirm("Are you sure want to process this transaction for batch?")' action="<?=site_url("admin/add_batch_process")?>">
<input type="hidden" name="num_orders" value="1">
<input type="hidden" name="process_partial" value="1">
<input type="hidden" name="transid" value="<?=$tran['transid']?>">
	<input type="submit" class="button button-tiny button-rounded " value="Partial Process to Batch">
</form>
</div>
<?php }?>
<?php }?>

<h2 style="margin: 3px 0px;margin-bottom: 10px;">Order Transaction : <?=$tran['transid']?></h2>
<div class="clear"></div>
<table class="datagrid" width="100%">
<thead><tr><th>Transaction ID</th><th>User</th><th>Mode</th><th>Amount</th><th>Paid</th><th>Refund</th><th colspan="2">Payment Status</th><th>Init Time</th><th>Completed on</th></tr></thead>
<tbody>
<tr>
<td><?=$tran['transid']?></td>
<td width="300">
<?php 
$allotted_memids = array();
foreach ($orders as $o){
	if($o['member_id'])
	 	array_push($allotted_memids,$o['member_id']);
}
if(count($allotted_memids) <= 1)
{
	?>
	<a href="<?=site_url("admin/user/{$order['userid']}")?>"><?=$orders[0]['username']?></a>
	<?php	
}else
{
	echo implode(', ',$allotted_memids);
}
$is_pnh = $tran['is_pnh'];
?>

<?php if($tran['is_pnh']){?>
<br>Franchise : <a href="<?=site_url("admin/pnh_franchise/{$tran['franchise_id']}")?>"><?=$this->db->query("select pnh_franchise_id as fid from pnh_m_franchise_info where franchise_id=?",$tran['franchise_id'])->row()->fid?></a>

<?php
	echo $is_fran_suspended?'<b style="color:#cd0000;font-size:11px;">'.$fran_status_arr[$is_fran_suspended].'</b>':'';
?>

<?php }?>
</td>
<td><?php switch($tran['mode']){
case 0:
	echo 'PAYMENT GATEWAY';break;
case 1:
	echo 'COD';break;
case 3:
	echo 'PNH';break;
default:
	echo "Unknown";break;
}?>
</td>
<td>Rs <?=$tran['amount']?></td>
<td>Rs <?=$tran['paid']?><?php if($tran['mode']==0){?><br><a style="font-size:70%" href="<?=site_url("admin/callcenter/trans/{$tran['transid']}")?>">check PG details</a><?php }?></td>
<td width="10" style="white-space:nowrap;"><?php 
$rf=$this->db->query("select sum(amount) as a from t_refund_info where transid=? and status=1",$tran['transid'])->row()->a;
if(!empty($rf))
	echo "Rs $rf (complete)<br>";
$rpf=$this->db->query("select sum(amount) as a from t_refund_info where transid=? and status=0",$tran['transid'])->row()->a;
if(!empty($rpf))
	echo "Rs $rpf (pending)";
if(empty($rf) && empty($rpf))
	echo "0";
?>
</td>
<td><?=$tran['status']==0?"PENDING":"COMPLETED"?></td>
<td width="40">
<div style="white-space:nowrap;float:left;border:1px dashed green;padding:3px 7px;background:<?=$tran['batch_enabled']?"#5f7":"#f57"?>;">
BATCH <?php if($tran['batch_enabled']){?>EN<?php }else{?>DIS<?php }?>ABLED
</div>
</td>
<td><?=format_datetime_ts($tran['init'])?></td>
<td><?=$tran['actiontime']?format_datetime_ts($tran['actiontime']):"na"?></td>
</tr>
</tbody>
</table>
<table>
<tr>
	<td width="30%">
<div>
<h4 style="margin-bottom:0px;">Address Details</h4>
<table style="background:#FFFFEF;width:100%" class="datagrid smallheader noprint">
<tr><th>Shipping Address</th><th>Billing Address</th></tr>
<tr>
<td width="50%">
<?=$order['ship_person']?><br>
<div class="edit_tog">
<?=$order['ship_address']?><br>
<?=$order['ship_landmark']?><br>
<?=$order['ship_city']?><br>
<?=$order['ship_state']?> - <?=$order['ship_pincode']?><br>
<?=$order['ship_phone']?>
<img src="<?=IMAGES_URL?>phone.png" class="phone_small" onclick='makeacall("0<?=$order['ship_phone']?>")'>
<br>
</div>
<div class="edit_tog" style="display:none">
<form action="<?=site_url("admin/changeshipaddr/{$order['transid']}")?>" method="post">
<input type="text" class="inp" name="address" value="<?=$order['ship_address']?>"><br>
<input type="text" class="inp" name="landmark" value="<?=$order['ship_landmark']?>"><br>
<input type="text" class="inp" name="city" value="<?=$order['ship_city']?>"><br>
<input type="text" class="inp" name="state" size=8 value="<?=$order['ship_state']?>"> - <input type="text" class="inp" name="pincode" size=5 value="<?=$order['ship_pincode']?>"><br>
<input type="text" class="inp" name="phone" value="<?=$order['ship_phone']?>"><br>
<input type="submit" value="Update">
</form>
</div>
<?=$order['ship_telephone']?><br>
<?=$order['ship_email']?>
<div class="edit_tog"><a href="javascript:void(0)" onclick='$(".edit_tog").toggle()'>edit</a></div>
</td>
<td width="50%">
<div class="edit_tog">
<?=$order['bill_person']?><br>
<?=$order['bill_address']?><br>
<?=$order['bill_landmark']?><br>
<?=$order['bill_city']?><br>
<?=$order['bill_state']?> - <?=$order['bill_pincode']?><br>
<?=$order['bill_phone']?><img src="<?=IMAGES_URL?>phone.png" class="phone_small" onclick='makeacall("0<?=$order['bill_phone']?>")'>
<br>
<?=$order['bill_email']?>
</div>
<div class="edit_tog" style="display:none">
<form action="<?=site_url("admin/changebilladdr/{$order['transid']}")?>" method="post">
<input type="text" class="inp" name="address" value="<?=$order['bill_address']?>"><br>
<input type="text" class="inp" name="landmark" value="<?=$order['bill_landmark']?>"><br>
<input type="text" class="inp" name="city" value="<?=$order['bill_city']?>"><br>
<input type="text" class="inp" name="state" size=8 value="<?=$order['bill_state']?>"> - <input type="text" class="inp" name="pincode" size=5 value="<?=$order['bill_pincode']?>"><br>
<input type="text" class="inp" name="phone" value="<?=$order['bill_phone']?>"><br>
<input type="submit" value="Update">
</form>
</div>
<div class="edit_tog"><a href="javascript:void(0)" onclick='$(".edit_tog").toggle()'>edit</a></div>
</td>
</tr>
</table>

<div style="margin:5px 0px;padding:5px;border:1px solid #f7f7f7;">
<h4 style="margin:0px;">Resend mails</h4>
<form action="<?=site_url("admin/resend_mails/{$order['transid']}")?>" method="post">
Email : <input type="text" name="email" value="<?=$order['ship_email']?>" size=33>
	<div style="padding:7px;"><input type="submit" name="shipment" value="Shipment">
	<input type="submit" value="Order confirmation"  name="order"></div>
</form>
</div>
</div>
</td>
<td width="10%" align="center">
	<br>
	<div style="margin:5px;margin-top:3px;line-height: 22px;" align="center">
		<b>Order Amount by Status</b>
		<div align="right">Pending Orders (Rs): <span style="width:80px;display: inline-block"><?php echo format_price($trans_order_status_amt_arr[0]+$trans_order_status_amt_arr[1]);?></span></div>
		<div align="right">Shipped Orders (Rs): <span style="width:80px;display: inline-block"><?php echo format_price($trans_order_status_amt_arr[2]);?></span></div>
		<div align="right">Cancelled Orders (Rs): <span style="width:80px;display: inline-block"><?php echo format_price($trans_order_status_amt_arr[3]);?></span></div>
		<div align="right">Returned Orders (Rs): <span style="width:80px;display: inline-block"><?php echo format_price($trans_order_status_amt_arr[4]);?></span></div>
	</div>
	<br>
<div style="float:left;width:200px;padding:0px 20px;" align="center">

<div <?=!$tran['priority']?"class='changeprior'":" title='{$tran['priority_note']}'"?> style="margin-top:10px;width:100px;display:inline-block;text-align:center;font-weight:bold;padding:15px 30px;border:1px solid #f1f1f1;<?=$tran['priority']?"":"cursor:pointer;"?>background:<?=$tran['priority']?"yellow":"#ddd"?>;text-transform:uppercase;">
<?php if($tran['priority']){?>HIGH<?php }else{?>NORMAL<?php }?>
<br>priority
</div>

<div style="margin-top:10px;width:100px;display:inline-block;text-align:center;font-weight:bold;padding:15px 30px;text-transform:uppercase;background:#fafafa;border:1px solid #f1f1f1;">
PROCESSED IN <span style="color:red"><?=(count($pbatch)==0?count($batch):count($pbatch))?></span> BATCHES
</div>

<div style="margin-top:10px;display:inline-block;text-align:left;">
<h4 style="margin:0px;">Charges</h4>
<table class="datagrid smallheader noprint" width="100%">
<thead><tr><th width="33%">Shipping</th><th width="50" align="left">COD</th><th width="33%">Giftwrap</th></tr></thead>
<tbody><tr><td>Rs <?=$tran['ship']?></td><td>Rs <?=$tran['cod']?></td><td>Rs <?=$tran['giftwrap_charge']?></td></tr></tbody>
</table>

</div>

</div>
</td>
<td width="60%">
<div >
<h4 style="margin-bottom:0px;">Invoices Summary</h4>
<table class="datagrid smallheader noprint" style="width: 100%">
<thead><tr><th>Proforma ID</th><th>Invoice</th><th>Batch</th><th colspan="2">Status</th><th>Date</th></thead>
<tbody>
<?php foreach($batch as $b){?>
<tr>
	<td>
		<a href="<?=site_url("admin/proforma_invoice/{$b['p_invoice_no']}")?>" <?=$b['invoice_status']==0?'style="text-decoration:line-through;"':""?>><?=$b['p_invoice_no']?></a>
		<?php
			$p_dispatch_id = @$this->db->query("select dispatch_id from proforma_invoices a where p_invoice_no = ? ",$b['p_invoice_no'])->row()->dispatch_id;
			if($p_dispatch_id)
				echo '(<a target="_blank" href="'.(site_url('admin/proforma_invoice/'.$b['p_invoice_no'])).'">'.$p_dispatch_id.'</a>)'; 
		?>	
	</td>
<td><a href="<?=site_url("admin/invoice/{$b['invoice_no']}")?>" <?=$b['invoice_status']==0?'style="text-decoration:line-through;"':""?>><?=$b['invoice_no']?></a></td>
<td><a href="<?=site_url("admin/batch/{$b['batch_id']}")?>">B<?=$b['batch_id']?></a></td>
<?php if($b['invoice_status']==1){?>
<td>
<?=$b['packed']&&$b['shipped']?"SHIPPED":($b['packed']?"PACKED":"Invoiced")?>
</td>
<?php }else echo "<td colspan='2'>CANCELLED</td>";?>
<?php if($b['invoice_status']==1){?>
<td><a href="<?=site_url("admin/cancel_invoice/{$b['invoice_no']}")?>" class="danger_link">cancel</a></td>
<?php }?>
<td><?=date("d/m/y",$b['createdon'])?>
</tr>
<?php } if(empty($batch)){?>
<tr>
<td colspan="100%">no invoice/batch available</td>
</tr>
<?php }?>
</tbody>
</table>
<?php if($tran['is_pnh']){?>
<div style="padding:5px;">
<a href="javascript:void(0)" class="button button-tiny button-rounded" onclick="window.open('<?=site_url("admin/pnh_cash_bill/{$tran['transid']}")?>')">Print Cash Bill</a>
</div>
<?php }?>

<h4 style="margin-bottom:0px;">Proforma Invoices</h4>
<table class="datagrid smallheader noprint" style="width: 100%">
<thead><tr><th>No</th><th>Batch</th><th colspan="2">Status</th><th>Date</th></thead>
<tbody>
<?php foreach($pbatch as $b){?>
<tr>
<td><a href="<?=site_url("admin/proforma_invoice/{$b['p_invoice_no']}")?>" <?=$b['invoice_status']==0?'style="text-decoration:line-through;"':""?>><?=$b['p_invoice_no']?></a></td>
<td><a href="<?=site_url("admin/batch/{$b['batch_id']}")?>">B<?=$b['batch_id']?></a></td>
<?php if($b['invoice_status']==1){?>
<td>
<?=$b['packed']&&$b['shipped']?"SHIPPED":($b['packed']?"INVOICED":"PENDING")?>
</td>
<?php }else echo "<td>CANCELLED</td>";?>
<?php if($b['invoice_status']==1 && $this->db->query("select invoice_no from shipment_batch_process_invoice_link where p_invoice_no=?",$b['p_invoice_no'])->row()->invoice_no==0){?>
<td><a href="<?=site_url("admin/cancel_proforma_invoice/{$b['p_invoice_no']}")?>" class="danger_link">cancel</a></td>
<?php }else{?><td></td><?php }?>
<td><?=date("d/m/y",$b['createdon'])?>
</tr>
<?php } if(empty($pbatch)){?>
<tr>
<td colspan="100%">no invoice/batch available</td>
</tr>
<?php }?>
</tbody>
</table>

</div>


<div >
<h4 style="margin-bottom:0px;">Shipment </h4>
<table class="datagrid smallheader noprint" style="width: 100%">
<thead><tr><th>Invoice</th><th>AWBs</th><th>Courier</th><th>Batch</th><th>Date</th><th>Current state</th><th>&nbsp;</th><th>&nbsp;</th></thead>
<tbody>
<?php $ssflag=0; foreach($batch as $b){ if(!$b['shipped']) continue;
if($b['invoice_status']==1)
	foreach($this->db->query("select order_id from king_invoice where invoice_no=?",$b['invoice_no'])->result_array() as $r)
		$shipped_oids[]=$r['order_id'];
?>
<tr>
<td>
	<a href="<?=site_url("admin/invoice/{$b['invoice_no']}")?>" <?=$b['invoice_status']==0?'style="text-decoration:line-through;"':""?>><?=$b['invoice_no']?></a>
	<?php
		$p_dispatch_det_res = @$this->db->query("select b.p_invoice_no,dispatch_id from proforma_invoices a join shipment_batch_process_invoice_link b on a.p_invoice_no = b.p_invoice_no where b.invoice_no = ? ",$b['invoice_no']);
		if($p_dispatch_det_res->num_rows())
		{
			$p_dispatch_det = $p_dispatch_det_res->row_array();
			echo '(<a target="_blank" href="'.(site_url('admin/proforma_invoice/'.$p_dispatch_det['p_invoice_no'])).'">'.$p_dispatch_det['dispatch_id'].'</a>)';
		}
	?>	
</td>
<td><?=$b['awb']?></td>
<td><?=$b['courier']?></td>
<td><a href="<?=site_url("admin/batch/{$b['batch_id']}")?>">B<?=$b['batch_id']?></a></td>
<td><?=date("d/m/y",strtotime($b['shipped_on'])	)?></td>
<td><?php 
		$inv_transit_log = array();
		$inv_last_status = '';
		$inv_last_updated_on = '';
		$inv_last_updated_by = '';
		 
		if(1)
		{
			$inv_transit_log_res = $this->db->query("select a.sent_log_id,a.invoice_no,a.logged_on,a.status,ref_id,b.name as handled_byname,c.hndleby_name,c.hndleby_contactno
													from pnh_invoice_transit_log a 
													left join m_employee_info b on a.ref_id = b.employee_id 
													join pnh_m_manifesto_sent_log c on c.id = a.sent_log_id 
													where invoice_no = ?
													order by a.id desc limit 1 ",$b['invoice_no']);
			if($inv_transit_log_res->num_rows())
			{
				$inv_transit_log = $inv_transit_log_res->row_array();
				$inv_last_updated_on = format_datetime($inv_transit_log['logged_on']);
				$inv_last_updated_by = $inv_transit_log['handled_byname'];
				if($inv_transit_log['status'] <= 2)
				{
					$inv_last_status = 'In Transit';
				}else if($inv_transit_log['status'] == 3)
				{
					$inv_last_status = 'Delivered'; 
				}else if($inv_transit_log['status'] == 4)
				{
					$inv_last_status = 'Marked for Return'; 
				} else if ($inv_transit_log['status'] == 5)
				{
					$inv_last_status = 'Picked';
				} 
			}
		}else
		{
			$inv_last_status = 'Delivered';
			$inv_last_updated_on = $b['delivered_on'];
			$inv_last_updated_by = $b['delivered_by'];
		}
		
		echo $inv_last_status;
		
	?></td>
<td>
	<p style="font-size: 11px;margin:0px 2px;">
	<?php 
		echo $inv_last_updated_on.'<br><b>'.$inv_last_updated_by.'</b>';
	?>	
	</p>
</td>
<td><a href="javascript:void(0)" onclick="get_invoicetransit_log(this,<?php echo $b['invoice_no']; ?>)" class="btn">View Transit Log</a></td>
</tr>
<?php $ssflag=1;} if(!$ssflag){?>
<tr>
<td colspan="100%">no shipments made</td>
</tr>
<?php }?>
</tbody>
</table>
<input type="button" class="button button-tiny button-rounded" value="Reship items of this order" id="reship_button">
</div>
</td>
</tr>
</table>

<div class="clear"></div>
<?php
$allow_qty_chng = 0;
?>
<div id="orders_data">

	<form id="cancelform" method="post" action="<?=site_url("admin/cancel_orders")?>">
	<input type="hidden" name="transid" value="<?=$tran['transid']?>">
	<h4>Orders</h4>
	<table class="datagrid nofooter" width="100%">
	<thead><tr><th></th><th>Order ID</th><th>Deal</th><th>Qty</th><th>Stock Product</th><th>MRP</th><th>Offer Price</th><th>Paid</th><th>Available Stock</th><th>Status</th><th>Backend Status</th><th>Last Update on</th></tr></thead>
	<tbody>
	<?php $shipped_oids=array_unique($shipped_oids); foreach($orders as $o){
		
		if($o['status'] == 0)
			$allow_qty_chng = 1;
		?>
	<tr>
	<td>
	<?php if(!isset($processed[$o['id']]) && $o['status']!=3 && !($o['status']==4) && !isset($p_processed[$o['id']])){?>
	<input class="ordercheckbox" type="checkbox" name="oids[]" value="<?=$o['id']?>">
	<?php $cancelsingle=true; } else $cancelall=false;?>
	</td>
	<td>
		<?=$o['id']?>
		<?php
			if($o['member_id'])
				echo '<br><b style="font-size:10px;">MemberID :'.$o['member_id'].'</b>';
		?>
		
	</td>
	<td><a style="color:#000;" href="<?=site_url("admin/deal/{$o['dealid']}")?>"><?=$o['deal']?></a><br><a style="font-size:80%;" href="<?=site_url($o['url'])?>">view deal</a></td>
	<td>
		<?=$o['quantity']?>
		<?php if($o['quantity']>1 && $allow_qty_chng){?>
		<div>
			<a href="javascript:void(0)" onclick='$("div",$(this).parent()).show();$(this).remove();' style="font-size:75%">edit</a>
			<div style="display:none;padding:5px;background:#fff;border:1px dashed #444;">
				<input type="hidden" name="nc_oid" value="<?=$o['id']?>" class="nc_oid">
				<?php if(!$is_prepaid){?>
				Paid for 1 qty : Rs <?=$o['i_price']-$o['i_coup_discount']?>
				<?php }else{?>
				Paid for 1 qty : Rs <?=$o['i_orgprice']-$o['i_coup_discount'];}?>
				<br>
				Total Refund :<input type="text" size="5" class="nc_refund" name="nc_refund"><br>
				New Qty :<select name="nc_qty" class="nc_qty">
				<?php for($i=1;$i<$o['quantity'];$i++){?>
					<option value="<?=$i?>"><?=$i?></option>
				<?php }?>
				</select>
				<input type="button" class="button button-tiny button-rounded changeqtyorder" value="Update">
			</div>
		</div>
		<?php }?>
	</td>
	<td>
	<?php $prods=array(); foreach($this->db->query("select l.qty,p.product_id,p.product_name from m_product_deal_link l join m_product_info p on p.product_id=l.product_id where l.itemid=?",$o['itemid'])->result_array() as $p){ $prods[]=$p['product_id'];?>
	<a href="<?=site_url("admin/product/{$p['product_id']}")?>" style="color:#000"><?=$p['product_name']?></a> <span style="font-size: 11px;font-weight: bold;color:#cd0000"> (<?php echo $p['qty'].'x'.$o['quantity']?>)</span> <br>
	<?php } 
		foreach($this->db->query("select d.qty,p.product_name,p.product_id 
											from products_group_orders o 
											join king_orders o1 on o1.id = o.order_id 
											join m_product_group_deal_link d on d.itemid = o1.itemid 
											join m_product_info p on p.product_id=o.product_id 
											where o.order_id=? ",$o['id'])->result_array() as $p)
		{ 
				$prods[]=$p['product_id']; 
	?>
				<a href="<?=site_url("admin/product/{$p['product_id']}")?>" style="color:#000"><?=$p['product_name']?></a> <span style="font-size: 11px;font-weight: bold;color:#cd0000"> (<?php echo $p['qty'].'x'.$o['quantity']?>)</span> <br>
	<?php }?>
	</td>
	<td><span class="nowrap">Rs <?=$o['i_orgprice']?></span></td>
	<td class="nowrap">Rs <?=$o['i_price']?></td>
	<td class="nowrap">
	<?php if($o['quantity']>1){?>Rs  <?=(($o['i_price']-$o['i_coup_discount']))?> x <?=$o['quantity']?><?php }?>
	<div>Rs<?php if($is_prepaid){?> <?=(($o['i_price']-$o['i_coup_discount'])*$o['quantity']);}else{?> <?=(($o['i_orgprice']-($o['i_discount']+$o['i_coup_discount']))*$o['quantity']);}?></div>
	</td>
	<td>
	<?php foreach($prods as $p){ $s=$this->db->query("select sum(available_qty) as s from t_stock_info where product_id=?",$p)->row()->s; if(!$s) $s=0;?>
	<div align="center">
	<span><?=$s?></span>
	</div>
	<?php }?>
	</td>
	<td>
	<?php $status=array("Confirmed","Processed","Shipped","Cancelled","Returned");?>
	<?=$status[$o['status']]?>
	<?php if(in_array($o['id'],$shipped_oids)) $shipped_orders[]=$o;?>
	</td>
	<td>
	<?php if(isset($p_processed[$o['id']])){?>
	<div>proforma invoice: <a href="<?=site_url("admin/proforma_invoice/{$p_processed[$o['id']]}")?>"><?=$p_processed[$o['id']]?></a></div>
	<?php }?>
	<?php if(isset($processed[$o['id']])){?>
	<div>invoice: <a href="<?=site_url("admin/invoice/{$processed[$o['id']]}")?>"><?=$processed[$o['id']]?></a></div>
	<?php $b=0;foreach($batch as $ba){
		if($ba['invoice_no']==$processed[$o['id']])
			$b=$ba['batch_id'];
	}?>
	<div>batch: <a href="<?=site_url("admin/batch/{$b}")?>"><?=$b?></a></div>
	<?php } if(!isset($processed[$o['id']]) && !isset($p_processed[$o['id']])) echo "na";?>
	</td>
	<td>
	<?=$o['actiontime']?format_datetime_ts($o['actiontime']):"na"?>
	</td>
	</tr>
	<?php }?>
	</tbody>
	</table>
	<?php if($cancelall || $cancelsingle){?>
	<div style="padding:5px;padding-top:3px;background:#eee;float:left;">
	<?php if($cancelsingle){?><input type="submit" value="Cancel selected orders" class="button button-tiny button-rounded" ><?php }?> <?php if($cancelall){?><input type="button" value="Cancel all orders" class="button button-tiny button-rounded" id="cancel_all"><?php }?>
	</div>
	<?php }?>
	</form>
	
	<div style="float:right;padding:10px;background: #f8f8f8;margin-top:3px;line-height: 22px;" align="center">
		<b>Order Amount by Status</b>
		<div align="right">Pending : <span style="width:80px;display: inline-block"><?php echo format_price($trans_order_status_amt_arr[0]+$trans_order_status_amt_arr[1]);?></span></div>
		<div align="right">Shipped : <span style="width:80px;display: inline-block"><?php echo format_price($trans_order_status_amt_arr[2]);?></span></div>
		<div align="right">Cancelled : <span style="width:80px;display: inline-block"><?php echo format_price($trans_order_status_amt_arr[3]);?></span></div>
		<div align="right">Returned : <span style="width:80px;display: inline-block"><?php echo format_price($trans_order_status_amt_arr[4]);?></span></div>
	</div>
	
	<div class="clear"></div>
	
	<div style="font-size:98%;margin-top:20px;"	>
	<div style="width:45%;padding-right:40px;float:left;">
	<h4>Changelog &amp; messages &nbsp; &nbsp; <a onclick='showaddmsg()' style="font-weight:normal;float: right;" href="javascript:void(0)">Add a msg</a></h4>
	<div id="add_msg_cont">
	<form method="post">
	<textarea name="msg" style="width:98%">Message...</textarea>
	<div>
	<input type="checkbox" name="usernote">User Note
	</div>
	<br>
	<input type="submit" value="Add Message">
	</form>
	</div>
	<table class="datagrid smallheader" width="100%">
	<thead>
	<tr><th>Message</th><th>By</th><th>Time</th></tr>
	</thead>
	<tbody>
	<?php foreach($changelog as $c){?>
	<tr>
	<td><?=$c['msg']?></td>
	<td><?=$c['admin']?></td>
	<td><?=format_datetime_ts($c['time'])?>
	</tr>
	<?php } if(empty($changelog)) {?>
	<tr><td colspan=3>no entries</td>
	<?php }?>
	</tbody>
	</table>
	</div>
	
	<div style="width:45%;float:left;">
	<h4>Support Tickets for this transaction &nbsp; &nbsp; <a target="_blank" style="font-weight:normal;float: right;" href="<?=site_url("admin/addticket/{$tran['transid']}")?>">Raise a ticket</a></h4>
	<table class="datagrid smallheader" width="100%">
	<thead><tr><th>Ticket</th><th>Status</th><th>Type</th><th>Last action on</th></tr></thead>
	<tbody>
	<?php foreach($tickets as $t){$ticket=$t;?>
	<tr>
	<td><a class="link" href="<?=site_url("admin/ticket/{$t['ticket_id']}")?>">TK<?=$t['ticket_no']?></a></td>
	<td><?php switch($ticket['status']){
		case 0:
			echo 'Unassigned';
			break;
		case 1:
			echo 'Opened';
			break;
		case 2:
			echo 'in progress';
			break;
		case 3:
			echo 'closed';
			break;
		default:
			echo 'unknown';
	}?>
	</td>
	<td><?php 
	if($ticket['type']==0)
		echo 'Query';
	else if($ticket['type']==1)
		echo 'Order Issue';
	else if($ticket['type']==2)
		echo 'Bug';
	else if($ticket['type']==3)
		echo 'Suggestion';
	else echo 'Commmon';
	?></td>
	<td><?=$ticket['updated_on']?></td>
	</tr>
	<?php } if(empty($tickets)){?>
	<tR><td colspan="100%">no tickets raised</td></tR>
	<?php }?>
	</tbody>
	</table>
	</div>
	<div class="clear"></div>
	</div>
	
	<div style="margin-top:20px;float:left;">
	<h4>Transaction Refunds</h4>
	
	<table class="datagrid smallheader">
	<thead><tr><th>Date</th><th>Amount</th><th>Status</th><th>Order Items</th></tr></thead>
	<tbody>
	<?php if(count($refunds)){ ?>
	<?php foreach($refunds as $r){?>
	<tr>
	<td><?php echo format_datetime_ts($r['created_on']);?></td>
	<td>Rs <?=$r['amount']?></td>
	<td><?=$r['status']==1?"Complete":"Pending"?>
	<?php if($r['status']==0){?>
	<br>
	<a href="<?=site_url("admin/mark_c_refund/{$r['refund_id']}")?>" style="font-size:80%">mark it as complete</a>
	<?php }?>
	</td>
	<td>
	<table>
	<tr><th>Deal</th><th>Qty</th></tr>
	<?php foreach($this->db->query("select * from t_refund_order_item_link where refund_id=?",$r['refund_id'])->result_array() as $ri){?>
	<?php foreach($orders as $o){ if($o['id']!=$ri['order_id']) continue;?><tr><td><?=$o['deal']?></td><td><?=$ri['qty']?></td></tr><?php }?>
	<?php }?>
	</table>
	</td>
	</tr>
	<?php }?>
	<?php }else { ?>
		<tr><td colspan="5" align="center"><b style="font-size: 10px;">No Refunds found</b></td></tr>
	<?php } ?>
	</tbody>
	</table>
	
	</div>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<?php if ($o['has_offer']==1){?>
	<div style="margin-top:34px;float:left;margin-left:140px;">
	<h4>Offer Details</h4>
	<table class="datagrid smallheader">
	<thead><tr><th>Brand</th><th>Category</th><th>Offer Msg</th><th>Action</th></tr></thead>
	<tbody><tr>
	<td><?php echo $o['brand']?$o['brand']:'All Brands';?></td>
	<td><?php echo $o['cat_name']?$o['cat_name']:'All Categories';?></td>
	<td><?php echo $o['offer_text']?></td>
	<td>
	<?php if($o['is_active']==1){?>
	<a class="danger_link" href="<?php echo site_url("admin/pnh_expire_offers/{$o['offer_refid']}") ?>">Disable</a>
	<?php }else{?>
		<a class="danger_link" href="<?php echo site_url("admin/pnh_enable_offers/{$o['offer_refid']}")?>">Enable</a>
	<?php }?>
	</td>
	</tr></tbody>
		</table>
	</div>
	<?php }?>

	<?php if(!empty($freesamples)){?>
	<div style="margin-top:20px;margin-left:30px;float:left;">
	<h4>Free Samples</h4>
	<table class="datagrid">
	<thead><tr><th>Free Sample</th></tr></thead>
	<tbody>
	<?php foreach($freesamples as $r){?>
	<tr>
	<td><?=$r['name']?></td>
	</tr>
	<?php }?>
	</tbody>
	</table>
	</div>
	<?php }?>
	
	<form id="changeqtyorderform" action="<?=site_url("admin/change_qy_order/{$o['transid']}")?>" method="post">
	<input type="hidden" name="nc_refund" class="refund">
	<input type="hidden" name="nc_oid" class="oid">
	<input type="hidden" name="nc_qty" class="qty">
	</form>
</div>

<div id="reship_data" style="display:none">
	<div style="background:#f3f4f5;border:1px solid #555;padding:10px;">
		<input type="button" value="Close" onclick='$("#reship_data").hide();$("#orders_data").show()'>
		<h3>Select items to reship</h3>
		<form id="reship_form" method="post" action="<?=site_url("admin/reship_order")?>">
		<input type="hidden" name="transid" value="<?=$o['transid']?>">
		<table class="datagrid">
		<thead><tr><th></th><th>Ordered item</th><th>Qty</th><th>Status</th><th>Backend Status</th><th>Last Update on</th></tr></thead>
		<tbody>
		<?php foreach($shipped_orders as $o){?>
		<tr>
		<td>
		<input class="reshipcheckbox" type="checkbox" name="oids[]" value="<?=$o['id']?>">
		</td>
		<td><a style="color:#000;" href="<?=site_url("admin/deal/{$o['dealid']}")?>"><?=$o['deal']?></a><br><a style="font-size:80%;" href="<?=site_url($o['url'])?>">view deal</a></td>
		<td><?=$o['quantity']?></td>
		<td>
		<?php $status=array("Confirmed","Processed","Shipped","Cancelled");?>
		<?=$status[$o['status']]?>
		</td>
		<td>
		<?php if(isset($processed[$o['id']])){?>
		<div>invoice: <a href="<?=site_url("admin/invoice/{$processed[$o['id']]}")?>"><?=$processed[$o['id']]?></a></div>
		<?php $b=0;foreach($batch as $ba){
			if($ba['invoice_no']==$processed[$o['id']])
				$b=$ba['batch_id'];
		}?>
		<div>batch: <a href="<?=site_url("admin/batch/{$b}")?>"><?=$b?></a></div>
		<?php }else echo "na";?>
		</td>
		<td>
		<?=$o['actiontime']?format_datetime_ts($o['actiontime']):"na"?>
		</td>
		</tr>
		<?php }?>
		</tbody>
		</table>
		<input type="submit" value="Reship selected items">
		</form>
	</div>
</div>

<?php if($tran['is_pnh']=="1"){?>
<div class="clear">
<h4 style="padding-top:10px;">Commission details</h4>
<table class="datagrid">
<thead><Tr><th>Sno</th><th width="200">Product Name</th><th>MRP</th><th>Offer price/ Dealer price</th><th>Menu Margin (A)</th><th>Scheme discount (B)</th><th>Balance Discount (C)</th><th>Voucher Margin(D)</th><th>Total Discount (A+B+C+D)</th><th>Unit Price</th><th>Qty</th><th>Order price</th><th>Redeem value</th></Tr></thead>
<tbody>
<?php $i=1; foreach($this->db->query("SELECT p.*,i.name,o.i_orgprice AS mrp,o.i_price AS price,c.loyality_pntvalue,o.redeem_value
										FROM pnh_order_margin_track AS p 
										JOIN king_dealitems i ON p.itemid=i.id
										JOIN king_orders o ON o.transid=p.transid AND o.itemid=p.itemid
										JOIN king_deals b ON i.dealid = b.dealid 
										JOIN pnh_menu c ON c.id = b.menuid  where p.transid=? GROUP BY o.id",$tran['transid'])->result_array() as $item){?>
<tr>
<td><?=$i++?></td>
<td><?=$item['name']?></td>
<td><?=$item['mrp']?></td>
<td><?=$item['price']?></td>

<td><b><?=$item['price']/100*$item['base_margin']?> (<?=$item['base_margin']?>%)</b></td>
<td><b><?=$item['price']/100*$item['sch_margin']?> (<?=$item['sch_margin']?>%)</b></td>
<td><?=$item['price']/100*$item['bal_discount']?> (<?=$item['bal_discount']?>%)</td>
<td><?=$item['price']/100*$item['voucher_margin']?> (<?=$item['voucher_margin']?>%)</td>
<td><?=($item['price']/100*($item['sch_margin']+$item['base_margin']+$item['bal_discount']+$item['voucher_margin']))?> (<?=$item['base_margin']+$item['sch_margin']+$item['bal_discount']+$item['voucher_margin']?>%)</td>
<td><?=$item['final_price']?></td>
<td>x<?=$item['qty']?></td>
<td><?=$item['final_price']*$item['qty']?></td>
<td><?=$item['redeem_value']?></td>
</tr>
<?php }?>
</tbody>
</table>

</div>
<?php }?>

</div>

<div id="inv_transitlogdet_dlg" title="Shipment Transit Log">
	<h3 style="margin:3px 0px;"></h3>
	<div id="inv_transitlogdet_tbl">
		
	</div>
</div>

<script>

$(function(){

	$("#reship_button").click(function(){
<?php if(empty($shipped_orders)){?>
	alert("No shipments made");
<?php }else{?>
		$("#reship_data").show();
<?php }?>
	});

	$("#reship_form").submit(function(){
		if($("input.reshipcheckbox:checked",$(this)).length==0)
		{
			alert("Select items for the reshipping");
			return false;
		}
		return true;
	});

	$(".checkall").click(function(){
		if($(this).attr("checked")==true)
			$(".ordercheckbox").attr("checked",true);
		else
			$(".ordercheckbox").attr("checked",false);
	});
	$(".changeprior").click(function(){
		msg=prompt("Enter message:");
		if(msg.length==0)
			return;
		$.post("<?=site_url("admin/setprioritytrans/{$order['transid']}")?>",{msg:msg},function(){
			location.reload(true);
		});
	});
	$("#cancel_all").click(function(){
		$(".ordercheckbox").attr("checked",true);
		$("#cancelform").submit();
	});
	$("#cancelform").submit(function(){
		if($(".ordercheckbox:checked",$(this)).length==0)
		{
			alert("Please select orders to cancel");
			return false;
		}
		return true;
	});
	$(".changeqtyorder").click(function(){
		p=$(this).parent();
		f=$("#changeqtyorderform");
		$(".refund",f).val($(".nc_refund",p).val());
		$(".oid",f).val($(".nc_oid",p).val());
		$(".qty",f).val($(".nc_qty",p).val());
		f.submit();
	});
});

function showaddmsg()
{
	$("#add_msg_cont").show();
}


function get_invoicetransit_log(ele,invno)
{
	$('#inv_transitlogdet_dlg').data({'invno':invno,}).dialog('open');
}

var refcont = null;
$('#inv_transitlogdet_dlg').dialog({width:'900',height:'auto',autoOpen:false,modal:true,
											open:function(){

												
												//,'width':refcont.width()
												//$('div[aria-describedby="inv_transitlogdet_dlg"]').css({'top':(refcont.offset().top+15+refcont.height())+'px','left':refcont.offset().left});
												
												$('#inv_transitlogdet_tbl').html('loading...');
												$.post(site_url+'/admin/jx_invoicetransit_det','invno='+$(this).data('invno'),function(resp){
													if(resp.status == 'error')
													{
														alert(resp.error);
													}else
													{
														var inv_transitlog_html = '<table class="datagrid" width="100%"><thead><th width="30%">Msg</th><th width="10%">Status</th><th width="10%">Handle By</th><th width="10%">Logged On</th><th width="15%">SMS</th></thead><tbody>';
														$.each(resp.transit_log,function(i,log){
															inv_transitlog_html += '<tr><td>'+log[5]+'</td><td>'+log[1]+'</td><td>'+log[2]+'('+log[4]+')</td><td>'+log[3]+'</td><td>'+log[6]+'</td></tr>';
														});
														inv_transitlog_html += '</tbody></table>';
														$('#inv_transitlogdet_tbl').html(inv_transitlog_html);

														$('#inv_transitlogdet_dlg h3').html('Invoice no :<span style="color:blue;font-size:12px">'+resp.invoice_no+'</span>  Franchise name: <span style="color:orange;font-size:12px">'+resp.Franchise_name +'</span> Town : <span style="color:gray;font-size:12px">'+resp.town_name+'</span>'+' ManifestoNo :'+resp.manifesto_id);


														
														
													}
												},'json');
											}
									});

</script>
<style>
.datagrid a{color:brown;}

.smallheader td{font-size: 12px;padding:5px;}
#reship_data{
position:absolute;
top:200px;
left:300px;
}
#add_msg_cont{
background:#eee;
padding:10px;
display:none;
margin:5px;
border:1px dashed #aaa;
}
.changeprior{
cursor:pointer;
}
.transaction h4{
margin-bottom:0px;
}

.btn{background: #FDFDFD;color: #454545;font-size: 10px;font-weight: bold;padding:0px 4px;display: inline-block;margin-top: 3px;text-decoration: underline;}
.span_count_wrap {
    background: none repeat scroll 0 0 #EAEAEA;
    float: left;
    font-size: 11px;
    margin: 10px;
    padding: 5px;
    text-align: center;
    width: 15%;
}
</style>
<?php
