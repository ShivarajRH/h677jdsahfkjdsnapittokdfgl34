<style>.leftcont{display:none}</style>

<?php 
	$cancelall=true;
	$cancelsingle=false;
	$order=$orders[0];
	$processed=$p_processed=array();
	$shipped_oids=array();
	$shipped_orders=array();
	$transid=$order['transid'];
	$price_type=$order['is_memberprice'];
	$order_status_arr=array();
	$order_status_arr[0]='Pending';
	$order_status_arr[1]='Processed';
	$order_status_arr[3]='Cancelled';


	$sql_trans_ttls = 'select status,ifnull(amt1,amt2) as amt from (
	select b.status,sum((mrp-(discount+credit_note_amt))*a.invoice_qty) as amt1,
		sum((i_orgprice-(i_coup_discount+i_discount))*b.quantity) as amt2
		from king_orders b 
		left join king_invoice a on a.order_id = b.id and invoice_status = 1  
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
	$is_fran_suspended_res = $this->db->query("select is_suspended from pnh_m_franchise_info where franchise_id=?",$tran['franchise_id']);
	$fran_price_type=$price_type==1?'Member Price':'Dealer price/Offer Price'; 
	
	if($is_fran_suspended_res->num_rows() >0 ) {
	    $is_fran_suspended=$is_fran_suspended_res->row()->is_suspended;
	
	    if($is_fran_suspended == 1)
		$tran['batch_enabled'] = 0;
	}

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
	
	$partner_id=$tran['partner_id'];
	
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
<?php
    foreach($orders as $o){
            if( !isset($processed[$o['id']]) && $o['status']!=3 && !isset($p_processed[$o['id']])){
                    $cancelsingle=true; 
            } 
            else $cancelall=false;
    }
    if($cancelsingle){ ?>
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
<thead><tr><th>Transaction ID</th><th>User</th><th>Billing Type</th><th>Mode</th><?php if($tran['is_pnh']){?> <th>Payment Days</th><th>Price Type</th> <?php } ?><th>Amount</th><th>Paid</th><th>Refund</th><th>Payment</th><th style="text-align:center">Status</th><th>Ordered On</th><th>Created By</th><th>Completed on</th></tr></thead>

<tbody>
<tr>
<td><?=$tran['transid']?></td>
<td width="250">
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
        $allotted_memids = array_unique($allotted_memids);
	echo implode(', ',$allotted_memids);
}
$is_pnh = $tran['is_pnh'];
?>

<?php if($tran['is_pnh']){
	$f_det=$this->db->query("select * from pnh_m_franchise_info where franchise_id=?",$tran['franchise_id'])->row_array();
?>	
<br>Franchise : <a href="<?=site_url("admin/pnh_franchise/{$tran['franchise_id']}")?>" target="_blank"><?=$f_det['pnh_franchise_id']?></a>
<br><a href="<?=site_url("admin/pnh_franchise/{$tran['franchise_id']}")?>" target="_blank"><?=$f_det['franchise_name']?></a>

<?php
	echo $is_fran_suspended?'<b style="color:#cd0000;font-size:11px;">'.$fran_status_arr[$is_fran_suspended].'</b>':'';
?>

<?php }?>
</td>
<?php $billing_type=$this->db->query("select billing_type from king_orders where transid=?",$transid)->row()->billing_type;?>
<td width="200"><?php echo $billing_type==1?'<b>B2C</b>':'<b>B2B</b>';?>&nbsp;<?php echo $tran['is_consolidated_payment']==1?'<b>[is consolidated]</b>':'';?></td>
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
<?php if($tran['is_pnh']){?>
<td>
	<b><?php echo $tran['credit_days']?>&nbsp;Days Payment</b>
</td>
<td><?php echo $price_type==1?'<b>Member Price</b>':'<b>Offer Price</b>';?></td>
<?php }?>

<?php   
    if($tran['is_pnh'])
    {
		$trans_ins_fee=$this->db->query("SELECT IFNULL(SUM(insurance_amount),0) AS insu_amt FROM king_orders WHERE transid=? AND STATUS !=3",$tran['transid'])->row()->insu_amt;
        if( $tran['order_for'] == 2 )
        {
                
                $trans_mem_fee=$this->db->query("SELECT IFNULL(SUM(pnh_member_fee),0) AS memfee FROM king_orders WHERE transid=? AND STATUS !=3",$tran['transid'])->row()->memfee;
        }
        else
        {
                $trans_mem_fee=$tran['pnh_member_fee'];
        }
        $paid=$trans_mem_fee+$trans_ins_fee+$tran['paid'];
    }
    else
    {
        $paid=$tran['paid'];
    }
?>
		
<td>Rs <?=$tran['amount']?></td>
<!--<td>Rs <?=$paid?></td>-->


<td>Rs <?php //echo $tran['paid']?> <?=$paid?><?php if($tran['mode']==0){?><br><a style="font-size:70%" href="<?=site_url("admin/callcenter/trans/{$tran['transid']}")?>">check PG details</a><?php }?></td>
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
<td><?=$this->db->query("select name from king_admin where id=?",$tran['trans_created_by'])->row()->name?></td>
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

<?php
	if($tran['is_pnh'])
	{
		$offers_q = $this->db->query("select a.*,b.first_name,b.user_id from pnh_member_offers a 
										join pnh_member_info b on b.pnh_member_id=a.member_id 
										where a.transid_ref=?",$order['transid']);// and a.offer_type not in (0,3) ;
		if($offers_q->num_rows())
		{ ?>
<div style="margin:5px 0px;padding:5px;border:1px solid #f7f7f7;">
   
        <h4>Offers</h4>
        <table class="datagrid smallheader" width="100%">
            <tr>
                <th>#</th>
                <th>Created on</th>
                <th>Member Name</th>
                <th>Type</th>
                <th>Value</th>
                <th>Status</th>
            </tr>
            
            <?php
            $offers = $offers_q->result_array();
            foreach($offers as $i=>$offer) { 
				$ofr_sts = $this->erpm->get_offer_process_status($offer['sno']);
			?>
            <tr>
                <td><?=++$i;?></td>
                <td><?=format_datetime($offer['created_on']);?></td>
                <td><a href="<?=site_url("/admin/pnh_viewmember/".$offer['user_id']);?>" target="_blank"><?=$offer['first_name'];?></a></td>
                <td><?= $ofr_sts['offer_type']; ?></td>
                <td><?= ( $offer['offer_value'] == 0 ) ? 'Free' : "Rs. ".formatInIndianStyle($offer['offer_value']);?></td>
                <td><?=$ofr_sts['status_msg']; ?></td>
            </tr>
            <!--<div style="padding:4px 5px;border-bottom:1px solid #DDDDDD;">Rs. <?=formatInIndianStyle($offer['offer_value']);?> worth of <?=$arr_offer_type[$offer['offer_type']];?> given</div>-->
    <?php   }?>
            
        </table>
</div>
<?php
        }

	}
?>
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
		<div align="right">Member Fee (Rs): <span style="width:80px;display: inline-block"><?=format_price($tran['pnh_member_fee'])?></span></div>
		<div align="right">Insurance Fee (Rs): <span style="width:80px;display: inline-block"><?=format_price($this->db->query("SELECT IFNULL(SUM(insurance_amount),0) AS insu_amt FROM king_orders WHERE transid=? AND STATUS !=3",$tran['transid'])->row()->insu_amt)?></span></div>
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
<?php
if($tran['is_pnh'])
{
    if($tran['order_for'] != 2 && $tran['pnh_member_fee'] > 0) {
?>
    <div style="white-space:nowrap;float:left;border:1px dashed green;padding:6px 7px;background:#DFDB82;margin: 12px 0 0 14px;">
        Other than Key Member Fee: <br>Rs. <?=$tran['pnh_member_fee']?>
    </div>
<?php
    }
}
?>
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
	<thead><tr><th></th><th>Order ID</th><th>Deal</th><th>Qty</th><th>Stock Product</th><th>MRP</th><th><?php echo $fran_price_type?></th>
<?php
               if($tran['is_pnh'] && $tran['order_for'] == 2)
               {
?>
                    <th>M-Fee</th>
<?php
               }
?>
                <th>I-Fee</th>
                <th>sub total</th><th>Paid</th><th>Available Stock</th><th>Status</th><th>Backend Status</th><th>Last Update on</th></tr></thead>
	<tbody>
	<?php $shipped_oids=array_unique($shipped_oids); foreach($orders as $o){
		
		if($o['status'] == 0)
			$allow_qty_chng = 1;
		
		$alloted_imei_det_res = $this->db->query("select * from t_imei_update_log where alloted_order_id = ? order by id desc limit 1;",$o['id']);
		
		$non_sk_imei_res = $this->db->query("select * from non_sk_imei_insurance_orders where order_id=? and transid=?",array($o['id'],$o['transid']));
		?>
	<tr class="<?php echo $o['has_nonsk_imei_insurance']==1?"has_nonskinsu":'';?>">
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
	<?php $prods=array(); 
            if(!empty($o['order_product_id']))
            {
                $order_product_id = $o['order_product_id'];
                $order_product_msg = ' and p.product_id = '.$order_product_id;
            }
            else 
                $order_product_msg='';
            
			foreach($this->db->query("select l.qty,p.product_id,p.product_name 
											from m_product_deal_link l 
											join m_product_info p on p.product_id=l.product_id 
											where l.itemid=? ".$order_product_msg,$o['itemid'])->result_array() as $p)
			{ 
				$prods[]=$p['product_id'];
	?>
				<a href="<?=site_url("admin/product/{$p['product_id']}")?>" style="color:#000"><?=$p['product_name']?></a> <span style="font-size: 11px;font-weight: bold;color:#cd0000"> (<?php echo $p['qty'].'x'.$o['quantity']?>)</span> <br>
	<?php 	
				if(!empty($o['order_product_id']))
				{
					echo '<span style="background:#f1f1f1;padding:3px;display:inline-block;width:auto">'.($this->db->query("select group_concat(concat('<b>',attr_name,'</b> : ',attr_value) order by attr_id desc SEPARATOR '<br>' ) as p_attr_det 
										from m_product_info a 
										join m_product_deal_link b on a.product_id = b.product_id  
										join m_product_attributes c on c.pid = b.product_id 
										join m_attributes d on d.id = c.attr_id 
										where b.itemid = ?  and a.product_id = ? 
										group by a.product_id ",array($o['itemid'],$o['order_product_id']))->row()->p_attr_det).'</span>';
				}
			}
	 
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
<?php
        // if keymember member fee applicable
        $insurance_amount=($o['insurance_amount']=='')?0:$o['insurance_amount'];
        if($tran['is_pnh'] && $tran['order_for'] == 2)
        {
                $pnh_member_fee=$o['pnh_member_fee'];
?>
		<td><?php echo $pnh_member_fee; ?></td>
<?php                 
        }
        else
        {
                $pnh_member_fee=0;

        }
?>
	<td>
			<?php echo $insurance_amount; ?>
			<!--Opt Insurance-->
			<?php 
			if($this->erpm->auth(true,true))
			{
			$ofr_det_res=$this->db->query("SELECT * FROM pnh_member_offers WHERE transid_ref=? AND order_id=?",array($transid,$o['id']));
			if($ofr_det_res->num_rows()==0)
			{
				$itm_det=$this->db->query("SELECT di.*,d.menuid FROM king_dealitems di JOIN king_deals d ON d.dealid = di.dealid WHERE id=?",array($o['itemid']))->row_array();
				
				$offer_res=$this->db->query("SELECT COUNT(*) AS offers FROM pnh_member_offers mo
													JOIN pnh_member_info mif ON mif.pnh_member_id=mo.member_id
													WHERE mif.user_id=? AND mo.transid_ref!=?
													GROUP BY mif.user_id",array($o['userid'],$transid));
				$free_offer=1;
				if($offer_res->num_rows())
				{
					$free_offer=0;
				}
				
				if($itm_det['has_insurance']==1 && $o['status']==0)
				{
?>
		
					<br><a onclick="return fn_opt_insurance_for_deal(this);" o_qty="<?=$o['quantity']?>" pnh_id="" itemid="<?=$o['itemid']?>" order_id="<?=$o['id'];?>" o_price="<?=$o['i_price']?>" transid="<?=$transid;?>" franchise_id="<?=$tran['franchise_id'];?>" mid="<?=$o['member_id'];?>" userid="<?=$o['userid'];?>" pnh_member_fee="<?=$pnh_member_fee;?>" free_offer="<?=$free_offer;?>" class="button button-tiny button-rounded">Opt Insurance</a>
<?php
					}
				}
			}
			?>
			<!--Opt Insurance-->
	</td>
	<td class="nowrap">
	<?php if($o['quantity']>1){?>Rs  <?=(($o['i_price']-$o['i_coup_discount']))?> x <?=$o['quantity']?><?php }?>
	<div>Rs<?php if($is_prepaid){?> <?=(($o['i_price']-$o['i_coup_discount'])*$o['quantity']);}else{?> <?=(($o['i_orgprice']-($o['i_discount']+$o['i_coup_discount']))*$o['quantity']);}?></div>
	</td>
	<td><?php echo ( ($o['i_price']-$o['i_coup_discount']) + ($pnh_member_fee+$insurance_amount) )*$o['quantity'] ;?>	</td>
	
	<td>
		<?php foreach($prods as $p)
		{
			$join_cond='';
			$cond=" AND b.is_damaged!=1 ";
			if($partner_id==7)
			{
				$join_cond.=" LEFT JOIN partner_info pt ON pt.partner_rackbinid=a.rack_bin_id AND pt.partner_rackbinid!=0 ";
				$cond.=" AND pt.id=".$partner_id;
			}
			
			 $s=$this->db->query("select sum(available_qty) as s 
							from t_stock_info a 
							join m_rack_bin_info b on a.location_id = b.location_id and a.rack_bin_id = b.id 
							$join_cond
							where product_id = ? $cond ",$p)->row()->s;
			if(!$s) $s=0;?>
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
	<?php } if(!isset($processed[$o['id']]) && !isset($p_processed[$o['id']])) echo "na";

	$alloted_imei_det = array();
	if($alloted_imei_det_res->num_rows())
	{
		$alloted_imei_det = $alloted_imei_det_res->row_array();
		
	}else
	{
		$alloted_imei_det = $this->db->query("select * from t_imei_no where order_id = ? ", $o['id'])->row_array();
	}
	
	if($non_sk_imei_res->num_rows())
	{
		$non_sk_imei_res=$non_sk_imei_res->row_array();
		echo '<div style="font-size:11px;background:#fcfcfc;padding:5px;width:155px;text-align:center">
		<b>Non SK IMEI : '.$non_sk_imei_res['nonsk_imei_no'].'</b>';
	}
	if(count($alloted_imei_det))
	{
		echo '<div style="font-size:11px;background:#fcfcfc;padding:5px;width:155px;text-align:center">
		<b>IMEI : '.$alloted_imei_det['imei_no'].'</b> <br>';
		echo '		<b>Activation Credit : Rs '.$o['imei_reimbursement_value_perunit'].'</b>
		';
		// is imei activated
		$imei_actv_det_res = $this->db->query("select * from t_imei_no where imei_no = ? ", $alloted_imei_det['imei_no']);
		if($imei_actv_det_res->num_rows())
		{
			$imei_actv_det = $imei_actv_det_res->row_array();
			echo ' <div class="legend" >'.($imei_actv_det['is_imei_activated']?'<span style="background:green;color:white;padding:3px 5px;">Activated</span>':'<span style="background:#cd0000;color:white;padding:3px 5px;">Not Activated</span>').'</div>';
		}
		echo '</div>';
	}
	
	?>
	
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
		<div align="right">Member Fee : <span style="width:80px;display: inline-block"><?=format_price($tran['pnh_member_fee'])?></span></div>
		<div align="right">Insurance Fee : <span style="width:80px;display: inline-block"><?=format_price($this->db->query("SELECT IFNULL(SUM(insurance_amount),0) AS insu_amt FROM king_orders WHERE transid=? AND STATUS !=3",$tran['transid'])->row()->insu_amt)?></span></div>
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
	<tr><td colspan="100%">no tickets raised</td></tr>
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
<thead><Tr><th>Sno</th><th width="250">Product Name</th><th>MRP</th><th><?php echo $fran_price_type;?></th><th>Menu Discount (A)</th><th>Scheme discount (B)</th><th>IMEI Discount (C)</th><th>Balance Discount (D)</th><th>Voucher Discount (E)</th><th width="100">Total Discount (A+B+C+D+E)</th><th>Unit Price</th><th>Order Qty</th><th>Order price</th><th>Redeem value</th></Tr></thead>
<tbody>
<?php $i=1; foreach($this->db->query("SELECT p.*,i.name,o.imei_reimbursement_value_perunit,o.i_orgprice AS mrp,o.i_price AS price,c.loyality_pntvalue,o.redeem_value,i_coup_discount
										FROM pnh_order_margin_track AS p 
										JOIN king_dealitems i ON p.itemid=i.id
										JOIN king_orders o ON o.transid=p.transid AND o.itemid=p.itemid
										JOIN king_deals b ON i.dealid = b.dealid 
										JOIN pnh_menu c ON c.id = b.menuid  where p.transid=? GROUP BY o.id",$tran['transid'])->result_array() as $item){
										
	$item['imei_marg'] = round(($item['imei_reimbursement_value_perunit']/($item['price']-$item['i_coup_discount']))*100,2);		
?>
<tr>
<td><?=$i++?></td>
<td><?=$item['name']?></td>
<td><?=$item['mrp']?></td>
<td><?=$item['price']?></td>

<td><b><?=$item['price']/100*$item['base_margin']?> <br>(<?=$item['base_margin']?>%)</b></td>
<td><b><?=$item['price']/100*$item['sch_margin']?> <br>(<?=$item['sch_margin']?>%)</b></td>
<td><b><?=$item['imei_reimbursement_value_perunit']?> <br>(<?=$item['imei_marg']?>%)</b></td>
<td><?=$item['price']/100*$item['bal_discount']?> <br>(<?=$item['bal_discount']?>%)</td>
<td><?=$item['price']/100*$item['voucher_margin']*1?> <br>(<?=$item['voucher_margin']*1?>%)</td>
<td><?=($item['price']/100*($item['sch_margin']+$item['base_margin']+$item['bal_discount']+$item['voucher_margin']+$item['imei_marg']))?> <br>(<?=$item['base_margin']+$item['sch_margin']+$item['bal_discount']+$item['imei_marg']+$item['voucher_margin']?>%)</td>
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
.has_nonskinsu{
background-color: orange !important;
}
/*================< INSURANCE RELATED STYLES >========================*/
.payment_pending {color:orange;font-weight:bold;font-size:13px; }
.view_insurance_lnk {float:right; margin-right: 25px;}

.details_pending {
	background: #F8F8F2;
	color: rgb(235, 131, 111);
	font-weight: bold;
	font-size: 11px;
	padding: 4px 2px;
}
.delivery_pending {
	background: #F1F1D9;
	color: #754D4D;
	font-weight: bold;
	font-size: 13px;
	padding: 4px 2px;
}
/*================< INSURANCE RELATED STYLES >========================*/
</style>
<!--Insurance related code changes-->
				<style>
					/************************ Insurance details form css ***********************************/
					.ui-widget {
						font-family: arial;
					}
					#insurance_option span
					{
						margin-top:5px;
					}
					.form_label_wrap
					{
						float: left;
						font-size: 13px;
						font-weight: bold;
						height: 30px;
						text-align: right;
						width: 30%;
					}
					.form_input_wrap
					{
						float: right;
						text-align: left;
						width: 70%;
						height: 30px;
						font-size: 11px;
						font-weight: bold;
					}
					.form_input_wrap .max_width
					{
						width:40% !important;
					}
					.block-name {
					padding: 4px 5px;
					background-color: aliceblue;
					text-align: center;
					}
					.clear {
						list-style: none;
						clear: both;
						float: none !important;
					}
					/*.notification_blk {
						display:none;float:right;padding:4px;margin-top:23px;background: #f1f1f1;font-size: 16px;
					}
					.hide { display:none; }
					textarea,input { padding: 2px 4px; }
					.process_status {
						margin:20px;
					}*/
				</style>
				<div  style="display:none;">
					<div id="dlg_update_member_insurance_details">
						<h4 style="background-color:#F6F6F6;padding:5px;text-align:center;">Member Insurance Details</h4>
						<!--<div id="insurance_option" title="Member Insurance Details" >-->
								<div id="crdet_insurance_blk insurance_option" title="Member Insurance Details">

									<div id="member_info_bloc">
											<form id="crdet_insurance" data-validate="parsley" method="post">
													<span class="form_label_wrap">Itemid:</span>
													<span class="form_input_wrap"><input class="max_width" type="text" name="itemid" id="itemid" value="" max-width="12" data-required="true" readonly="true"></span>
													
													<span class="form_label_wrap">OrderID:</span>
													<span class="form_input_wrap"><input class="max_width" type="text" name="order_id" id="order_id" value="" max-width="12" data-required="true" readonly="true"></span>
													
													<span class="form_label_wrap">Order Qty:</span>
													<span class="form_input_wrap"><input class="max_width" type="text" name="o_qty" id="o_qty" value="" max-width="5" data-required="true" readonly="true"></span>
													
													<span class="form_label_wrap">Order Price:</span>
													<span class="form_input_wrap"><input class="max_width" type="text" name="o_price" id="o_price" value="" max-width="5" data-required="true" readonly="true"></span>
													
													<input class="max_width" type="hidden" name="transid" id="transid" value="" max-width="12" data-required="true">
													<input class="max_width" type="hidden" name="userid" id="userid" value="" max-width="12" data-required="true">
													<input class="max_width" type="hidden" name="member_id" id="member_id" value="" max-width="12" data-required="true">
													<input class="max_width" type="hidden" name="franchise_id" id="franchise_id" value="" max-width="12" data-required="true">
													<input class="max_width" type="hidden" name="pnh_member_fee" id="pnh_member_fee" value="" max-width="12" data-required="true">
													<input class="max_width" type="hidden" name="free_offer" id="free_offer" value="" max-width="12" data-required="true">
													<!-- ===================< Member Details >==================== -->
													<div class="clear block-name">Member Details</div>

													<span class="form_label_wrap">Mobile <b class="red_star">*</b>: </span>
													<span class="form_input_wrap"><input class="max_width" type="text" name="membermob" id="membermob" value="" maxlength="10" data-required="true"></span>

													<span class="form_label_wrap">First Name :</span>
													<span class="form_input_wrap">
														<input class="max_width" type="text" name="memberfname" id="memberfname" value="" data-required="true">
													</span>

													<span class="form_label_wrap">Last Name :</span>
													<span class="form_input_wrap"><input class="max_width" type="text" name="i_memberlname" id="i_memberlname" value="" ></span>


													<!-- ===================< Insurance Details >==================== -->
													<div class="clear block-name">Insurance Details</div>

													<span class="form_label_wrap">Proof Type :</span>
													<span class="form_input_wrap">
															<select name="crd_insurence_type" class="max_width" id="crd_insurence_type">
																		<option value="">Select</option>
																		<?php $insurance_types=$this->db->query("select * from insurance_m_types order by name asc")->result_array();
																				if($insurance_types){
																				foreach($insurance_types as $i_type){
																		?>
																				<option value="<?php echo $i_type['id']?>"><?php echo $i_type['name']?></option>
																		<?php }}?>
																		<option value="others">Others</option>
															</select>
													</span>
													<span class="othrs_proofname form_label_wrap">Proof Name <b class="red_star">*</b>:</span>
													<span class="othrs_proofname form_input_wrap"><input class="max_width" type="text" name="proof_name" id="proof_name" value=""></span>

													<span class="form_label_wrap">Proof Id :</span>
													<span class="form_input_wrap"><input class="max_width" type="text" name="proof_id" id="proof_id" value=""></span>

													<span class="form_label_wrap" style="height:63px !important">Proof Address :</span>
													<span class="form_input_wrap" style="height:63px !important"><textarea class="max_width" name="crd_insurance_mem_address" id="crd_insurance_mem_address"></textarea></span>

													<span class="form_label_wrap">City  :</span>
													<span class="form_input_wrap"><input class="max_width" type="text" name="i_member_city" id="i_member_city" value=""></span>

													<span class="form_label_wrap">PinCode :</span>
													<span class="form_input_wrap"><input class="max_width" type="text" name="i_member_pcode" id="i_member_pcode" value=""></span>

													<span class="form_label_wrap">Retailer Invoice No. :</span>
													<span class="form_input_wrap"><input class="max_width" type="text" name="i_member_receipt_no" id="i_member_receipt_no" value=""></span>

													<span class="form_label_wrap">Retailer Invoice Amount :</span>
													<span class="form_input_wrap"><input class="max_width" type="text" name="i_member_receipt_amount" id="i_member_receipt_amount" value=""></span>

													<span class="form_label_wrap">Retailer Invoice Date :</span>
													<span class="form_input_wrap"><input class="max_width" type="text" name="i_member_receipt_date" id="i_member_receipt_date" value=""></span>

													<span class="form_label_wrap confirm_feedback"></span>

											</form>
									</div>
							</div>
						<!--</div>-->
					</div>
				</div>
			<script>
			$("#dlg_update_member_insurance_details").dialog({
				modal:true
				,autoOpen:false
				,height:"auto"
				,width:700
				,open:function(event,i) {
						var dlg=$(this);
						var det = dlg.data("det");
						
						$("#transid",dlg).val(det.transid);
						$("#itemid",dlg).val(det.itemid);
						$("#order_id",dlg).val(det.order_id);
						$("#o_qty",dlg).val(det.o_qty);
						$("#o_price",dlg).val(det.o_price);
						
						$("#userid",dlg).val(det.userid);
						$("#franchise_id",dlg).val(det.franchise_id);
						$("#pnh_member_fee",dlg).val(det.pnh_member_fee);
						$("#free_offer",dlg).val(det.free_offer);
						
						$.post(site_url+"/admin/jx_get_member_det",{mid:det.mid,userid:det.userid},function(mem) {
							if(mem.status=='success')
							{
								var mdet=mem.i_memdet;
								$("#member_id",dlg).val(mdet.pnh_member_id);
								$("#membermob",dlg).val(mdet.mobile);
								$("#memberfname",dlg).val(mdet.first_name);
								$("#i_memberlname",dlg).val(mdet.last_name);
								$("#i_member_city",dlg).val(mdet.city);
								$("#i_member_pcode",dlg).val(mdet.pincode);
								$("#crd_insurance_mem_address",dlg).val(mdet.address);
							}
							else
							{
								alert("Error: "+mem.message);
								return false;
							}
						},'json');
				}
				,buttons:{
						Submit:function(e,j) {
							var dlg=$(this);
							var can_edit = dlg.data("can_edit");
							if(can_edit == 0)
							{
								alert("The offers already processed, You cant update insurance details now.");
								return false;
							}
							var mob_no = $("#membermob",dlg).val();

							if( mob_no == '' )
							{
								alert("Mobile Number is required.");
								return false;
							}
							if( isNaN(mob_no) )
							{
								alert("Invalid mobile number");
								return false;
							}
							if($("#crd_insurence_type",dlg).val()!='')
							{
								if($("#proof_id",dlg).val()=='')
								{
									alert("Please specify proof id");
									return false;
								}
							}
							if($("#crd_insurence_type",dlg).val()=='others')
							{
								if($("#proof_name",dlg).val()=='') {
									alert("Please specify proof name");
									return false;
								}
							}
							
							//post form input
							$.post(site_url+"/admin/jx_create_insurance_det",$("#crdet_insurance").serialize(),function(resp) {
								if(resp.status == 'success')
								{
									alert(resp.message);
									dlg.dialog("close");
									//load_contents(); //
									location.href=$(location).attr("href");
								}
								else
								{
									alert(resp.message);
								}
							},'json');
						}
						,Cancel:function(d,k) {
							$(this).dialog("close");
						}
				}
				,title: "Update member insurance details"
					
			});
			function fn_opt_insurance_for_deal(elt)
			{
				if(confirm("Do you want to opt insurance for this deal?"))
				{
					var e=$(elt);
					var can_edit=1;
					var det={};
					det['mid']=e.attr('mid');
					det['userid']=e.attr('userid');
					det['franchise_id']=e.attr('franchise_id');
					det['transid']=e.attr('transid');
					det['itemid']=e.attr('itemid');
					det['order_id']=e.attr('order_id');
					det['o_qty']=e.attr('o_qty');
					det['o_price']=e.attr('o_price');
					det['pnh_member_fee']=e.attr('pnh_member_fee');
					det['free_offer']=e.attr('free_offer');
					
					$("#dlg_update_member_insurance_details").data({"can_edit":can_edit,det:det}).dialog("open");
				}
				return false;
			}
			$(window).on("resize scroll",function() {
				$("#dlg_update_member_insurance_details").dialog("option","position",["center","center"]); 
			});

			//===================< OTHER TYPE OF PROOF NAME >=======================================
			$('.othrs_proofname').hide();
			$("#crd_insurence_type").live('change',function(){
				if($(this).val()=='others')
				{
					$('.othrs_proofname').show();
				}
				else
				{
					$('.othrs_proofname').hide();
				}
			});
			//===================< END OTHER TYPE OF PROOF NAME >=======================================
			$("#i_member_receipt_date").datepicker();
			</script>
			
			<!--End Opt Insurance changes-->
			
<?php
