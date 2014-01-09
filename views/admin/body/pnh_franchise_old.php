<?php $f=$fran; ?>
<style>
.stats {padding:5px;font-size: 14px;}
.stats span{padding:5px;background: #ffffd0;margin:5px;display: inline-block;padding:10px;text-align: center;}
.stats span b{font-weight: bold;display: block;}
.datagrid_stat{border-collapse: collapse;}
.datagrid_stat th{background: #fcfcfc;padding:5px 10px;border:1px solid #dedede;background: #C97033;color: #FFF;;font-size: 12px}
.datagrid_stat td{border:1px solid #dedede}
</style>
<div class="container">
<?php 
	$total_invoiced = $this->db->query("select sum(debit_amt) as amt from pnh_franchise_account_summary where action_type = 1 and franchise_id = ? ",$f['franchise_id'])->row()->amt;
	$pending_amt = $this->db->query("select sum(i_orgprice-(i_coup_discount+i_discount)) as amt from king_orders a join king_transactions b on a.transid = b.transid where b.franchise_id = ? and a.status = 0;",$f['franchise_id'])->row()->amt;
	$receipt_total = $this->db->query("select sum(credit_amt) as amt from pnh_franchise_account_summary where action_type in (3,5) and franchise_id = ?",$f['franchise_id'])->row()->amt;
	$bal_amt = $total_invoiced+$pending_amt-$receipt_total;
?>


<table width="100%">
	<tr>
		<td>
			<h2 style="margin-top: 15px !important;"><?php echo $f['franchise_name']?></h2>
		</td>
		<td align="right" style="vertical-align: top;">
			 <a style="white-space:nowrap" href="<?=site_url("admin/pnh_edit_fran/{$f['franchise_id']}")?>">Edit</a> &nbsp;&nbsp; 
			<a style="white-space:nowrap" href="<?=site_url("admin/pnh_manage_devices/{$f['franchise_id']}")?>">Manage devices</a> &nbsp;&nbsp;
			<a style="white-space:nowrap" href="<?=site_url("admin/pnh_assign_exec/{$f['franchise_id']}")?>">Assign Executives</a> &nbsp;&nbsp; 
			<a style="white-space:nowrap" href="<?=site_url("admin/pnh_upload_images/{$f['franchise_id']}")?>">Upload Images</a> &nbsp;&nbsp; 
			<a style="white-space:nowrap" href="<?=site_url("admin/pnh_sms_log/{$f['franchise_id']}")?>">SMS Log</a> &nbsp;&nbsp;
			<a style="white-space:nowrap" href="<?=site_url("admin/pnh_investor_report/{$f['franchise_id']}")?>">Sales Report</a> &nbsp;&nbsp;
			<?php if($f['is_suspended']==0){?> 
			<a href="<?=site_url("admin/pnh_suspend_fran/{$f['franchise_id']}")?>" class="danger_link">Suspend Account</a>
			<?php }else{?>
			<a href="<?=site_url("admin/pnh_unsuspend_fran/{$f['franchise_id']}")?>" class="danger_link">Unsuspend Account</a>
			<?php }?>
			 &nbsp;&nbsp;<a style="white-space:nowrap" href="<?=site_url("admin/pnh_offline_order#{$f['pnh_franchise_id']}")?>">Place Order</a>
			
			 &nbsp;&nbsp;<a style="white-space:nowrap" href="<?=site_url("admin/pnh_quotes/{$f['franchise_id']}")?>">Order Quotes</a>
		</td>
	</tr>
</table>

 

<div class="tabs">
<ul>
<li><a href="#name">Basic Details</a></li>
<li><a href="#contacts">Contacts</a></li>
<?php if($this->erpm->auth(PNH_EXECUTIVE_ROLE,true)){?>
<li><a href="#bank">Bank Details</a></li>
<li><a href="#actions">Credits and MIDs</a></li>
<?php }?>
<li><a href="#statement">Account Statement &amp; Topup</a></li>
<li><a href="#sch_hist">Scheme discount</a></li>
<li><a href="#devices">Devices</a></li>
<li><a href="#members" onclick='loadmember()'>Members</a></li>
<li><a href="#orders" >Orders</a></li>
<li><a href="#images">Photos</a></li>
</ul>


<div id="name">
<table class="datagrid" width="100%" style="margin-top:10px;">
<thead><tr><th>Franchise Name</th><th>FID</th><th>Type</th><th>City</th><th>Territory</th><th>Current Balance</th><th>Credit Limit</th><th>Deposit</th><th>Class</th><th>Margin</th><th>Scheme Discount</th><th>Scheme available for</th></tr></thead>
<tbody>
<tr>
<td><a href="<?=site_url("admin/pnh_franchise/{$f['franchise_id']}")?>"></a><?=$f['franchise_name']?></td>
<td><?=$f['pnh_franchise_id']?></td>
<td><?=$f['is_lc_store']?"LC Store":"Franchise"?></td>
<td><?=$f['city']?></td>
<td><?=$f['territory_name']?></td>
<td>Rs <?=$f['current_balance']?></td>
<td>Rs <?=$f['credit_limit']?></td>
<td>Rs <?=$f['security_deposit']?></td>
<td><?=$f['class_name']?></td>
<td><?=$f['margin']?>%</td>
<td><?=$f['sch_discount']?>%</td>
<td>
<?php if($f['sch_discount_start']!=0){?>
<?=date("d/m/y",$f['sch_discount_start'])?> to <?=date("d/m/y",$f['sch_discount_end'])?> 
<?php }?>
</td>
</tr>
</tbody>
</table>

<div style="float:right;padding-left:20px;">
<h4 style="margin-bottom:0px">Recent Account statement</h4>
<table class="datagrid smallheader">
<thead><tr><th>Type</th><th>Date</th><th>Desc</th><Th>amount</Th><th>Balance After</th></tr></thead>
<tbody>
<?php foreach($this->db->query("select * from pnh_franchise_account_stat where franchise_id=? order by id desc limit 7",$f['franchise_id'])->result_array() as $t){?>
<tr><td><?=$t['type']==0?"In":"Out"?></td><td><?=date("d/m/y",$t['created_on'])?></td><td><?=$t['desc']?></td><td><?=$t['amount']?></td><td><?=$t['balance_after']?></td></tr>
<?php }?>
</tbody>
</table>
</div>


<div style="float:right">
<h4 style="margin-bottom:0px;">Allotted Member IDs</h4>
<table class="datagrid smallheader">
<thead><tr><th>Start</th><th>End</th><th>Allotted on</th><th>Allotted by</th></tr></thead>
<tbody>
<?php foreach($this->db->query("select m.*,a.name as admin from pnh_m_allotted_mid m join king_admin a on a.id=m.created_by where franchise_id=?",$f['franchise_id'])->result_array() as $m){?>
<tr><td><?=$m['mid_start']?></td><td><?=$m['mid_end']?></td><td><?=date("d/m/y",$m['created_on'])?></td><td><?=$m['admin']?></td>
<?php }?>
</tbody>
</table>
</div>


<div>
<h4 style="margin-bottom:0px">LOGIN</h4>
<table class="datagrid noprint">
<thead><tr><th>Mobile1</th><th>Mobile2</th></tr></thead>
<tbody><tr><td><?=$f['login_mobile1']?> <img src="<?=IMAGES_URL?>phone.png" class="phone_small" onclick='makeacall("0<?=$f['login_mobile1']?>")'>
</td><td><?=$f['login_mobile2']?> <img src="<?=IMAGES_URL?>phone.png" class="phone_small" onclick='makeacall("0<?=$f['login_mobile2']?>")'>
</td></tbody>
</table>
</div>

<div style="padding-top:15px;">
	<?php 
		$app_v = '';
		$app_v_res = $this->db->query("select version_no from pnh_app_versions where id=? ",$f['app_version']);
		if($app_v_res->num_rows())
			$app_v=$app_v_res->row()->version_no; 
	?>
	<div class="dash_bar">App Version : <span><?=$app_v?></span></div>
	<br><br><br><br>
	<form action="<?=site_url("admin/pnh_change_app_version")?>">
		<input type="hidden" name="fid" value="<?=$f['franchise_id']?>">
		Change to New Version : <select id="fran_ver_change">
		<option value="0">select</option>
		<?php foreach($this->db->query("select version_no,id from pnh_app_versions where id>?",$f['app_version'])->result_array() as $v){?>
		<option value="<?=$v['id']?>"><?=$v['version_no']?></option>
		<?php }?>
		</select>
	</form>
</div>

<div style="padding-top:20px;">
<h4 style="margin:0px;">Recent Call Log</h4>
<table class="datagrid smallheader noprint">
<thead><tr><th>Msg</th><tH>Call Made on</tH><th>By</th></thead>
<tbody>
<?php foreach($this->db->query("select l.*,a.name,l.created_on from pnh_call_log l join king_admin a on a.id=l.created_by where franchise_id=? order by l.created_on desc limit 10",$fran['franchise_id'])->result_array() as $l){?>
<tr><td><?=$l['msg']?> <a href="javascript:void(0)" style="font-size:85%;" onclick='$("form",$(this).parent()).toggle()'>add msg</a>
<form method="post" style="display:none;" action="<?=site_url("admin/pnh_update_call_log/{$l['id']}")?>"><textarea name="msg"><?=$l['msg']?></textarea><input type="submit" value="add"></form>
</td><td><?=date("g:ia d/m/y",$l['created_on'])?></td><td><?=$l['name']?></td></tr>
<?php }?>
</tbody>
</table>
</div>

<div class="clear"></div>

</div>

<div id="contacts">

<div style="float:left;margin-left:20px;">
<h4 style="margin-bottom:0px">Contacts</h4>
<table class="datagrid noprint">
<thead><tr><th>Contact Name</th><th>Designation</th><th>Mobile</th><th>Email</th><th>Telephone</th></thead>
<tbody>
<?php foreach($this->db->query("select * from pnh_m_franchise_contacts_info where franchise_id=?",$f['franchise_id'])->result_array() as $c){?>
<tr><td><?=$c['contact_name']?></td><td><?=$c['contact_designation']?></td><td><?=$c['contact_mobile1']?>,<br><?=$c['contact_mobile2']?></td><td><?=$c['contact_email1']?>,<br><?=$c['contact_email2']?></td><td><?=$c['contact_telephone']?>,<br><?=$c['contact_fax']?></td>
<?php }?>
</tbody>
</table>
</div>

<div style="float:left;margin-left:20px;">
<h4 style="margin-bottom:0px">Shop Details</h4>
<table class="datagrid noprint">
<thead><tr><th>Shop Name</th><th>Type of Business</th><th>No of employees</th><th>Area</th></thead>
<tr><td><?=$f['store_name']?></td><td><?=$f['business_type']?></td><td><?=$f['no_of_employees']?></td><td><?=$f['store_area']?> sqft</td></tr>
</table>
</div>

<div class="clear"></div>

<table class="datagrid">
<thead><tr><th>Address</th><th>Locality</th><th>City</th><th>Postcode</th><th>State</th></tr></thead>
<tbody>
<tr>
<td><?=$f['address']?></td>
</tr>
</tbody>
</table>

</div>


<div id="statement">
		
		
		<div class="stats">
						<span><b>Total Shipped</b> <br /> Rs <?=number_format($total_invoiced,2)?> Pending : </span>
						<span><b>Pending Against Shipped</b> <br />Rs <?=number_format(($total_invoiced-$receipt_total > 0)?$total_invoiced-$receipt_total:0,2)?></span>
						<span><b>UnShipped Products</b> <br />Rs <?=number_format($pending_amt,2)?></span>
						<span><b>Paid Tilldate</b> <br /> Rs <?=number_format($receipt_total,2)?></span>
						<span><b>Total Balance</b> <br /> Rs <?=number_format($bal_amt,2)?></span>
					</div>
					
		
		<div>
			<ul class="stat_tabs">
				<li><a href="#recent_acct_stat">Recent Account statement</a></li>
				<li><a href="#topup_secu_deposit">Make a Topup/Security Deposit</a></li>
				<li><a href="#acc_stat_corr">Account Statement Correction</a></li>
				<li><a href="">Receipts</a></li>
			</ul>
				<div  id="recent_acct_stat">
				
					<table class="datagrid smallheader">
					<thead><tr><th>Type</th><th>Date</th><th>Desc</th><Th>amount</Th><th>Balance After</th></tr></thead>
					<tbody>
					<?php foreach($this->db->query("select * from pnh_franchise_account_stat where franchise_id=? order by id desc limit 7",$f['franchise_id'])->result_array() as $t){?>
					<tr><td><?=$t['type']==0?"In":"Out"?></td><td><?=date("d/m/y",$t['created_on'])?></td><td><?=$t['desc']?></td><td><?=$t['amount']?></td><td><?=$t['balance_after']?></td></tr>
					<?php }?>
					</tbody>
					</table>
					<div style="background:#eee;padding:5px;">
					<form id="d_ac_form" action="<?=site_url("admin/pnh_download_stat/{$f['franchise_id']}")?>" method="post">
					<h4 style="margin:0px;">Download account statement</h4> 
					from <input type="text" name="from" id="d_ac_from" size=10> To <input size=10 type="text" name="to" id="d_ac_to"> <input type="submit" value="Go">
					</form>
					</div>
				</div>
				
				<div id="topup_secu_deposit">
				<h4 style="background:#C97033;color:#fff;padding:5px;margin:-5px -5px 5px -5px;">Make a Topup/Security Deposit</h4>
				<form method="post" id="top_form" action="<?=site_url("admin/pnh_topup/{$fran['franchise_id']}")?>">
				<table cellpadding=3>
				<tr><td>Type :</td><td><select name="r_type"><option value="1">Topup</option><option value="0">Security Deposit</option></select></td></tr>
				<tr><td>Amount :</td><td>Rs <input type="text" class="inp amount" name="amount" size=5></td></tr>
				<tr><td>Instrument Type :</td><td><select name="type" class="inst_type">
				<option value="0">Cash</option>
				<option value="1">Cheque</option>
				<option value="2">DD</option>
				<option value="3">Transfer</option>
				</select></td></tr>
				<tr class="inst inst_name"><td class="label">Bank name :</td><td><input type="text" name="bank" size=30></td></tr>
				<tr class="inst inst_no"><td class="label">Instrument No :</td><td><input type="text" name="no" size=10></td></tr>
				<tr class="inst inst_date"><td class="label">Instrument Date :</td><td><input type="text" name="date" id="sec_date" size=15></td></tr>
				<tr class="inst_msg"><td>Message :</td><td><input type="Text" class="inp msg" name="msg" size=30></td></tr>
				<tr><td></td><td><input type="submit" value="Add Topup"></td></tr>
				</table>
				</form>
				</div>
				
				
				<div id="acc_stat_corr">
				<h4 style="background:#C97033;color:#fff;padding:5px;margin:-5px -5px 5px -5px;">Account Statement Correction</h4>
				<form method="post" id="acc_change_form" action="<?=site_url("admin/pnh_acc_stat_c/{$fran['franchise_id']}")?>">
				<table cellpadding=3>
				<tr><td>Type :</td><td><select name="type"><option value="0">In (credit)</option><option value="1">Out (debit)</option></select></td></tr>
				<tr><td>Amount :</td><td>Rs <input type="text" name="amount" class="inp" size=5></td></tr>
				<tr><td>Description :</td><td><input type="text" name="desc" class="inp" size=30></td></tr>
				<tr><td></td><td><label><input type="checkbox" name="sms" value="1">Send SMS to Franchise</label></td></tr>
				<tr><td></td><td><input type="submit" value="Make correction"></td></tr>
				</table>
				</form>
				</div>
				
				
				 
				
				<div id="receipt_list">
				<h4 style="margin-bottom:0px">Receipts <a href="<?=site_url("admin/pnh_pending_receipts")?>" style="font-size:75%">activate/cancel</a></h4>
				<table class="datagrid smallheader">
				<thead><tr><th>Receipt ID</th><th>Type</th><Th>Amount</Th><tH>Bank</tH><th>Instrument Type</th><Th>Instrument Date</Th><th>Instrument No</th><th>Remarks</th><th>Status</th><th>Added on</th><th>Created By</th><th>Activated/Cancelled By</th><th>Activated/Cancelled On</th><th>Activation/cancel reason</th></tr></thead>
				<tbody>
				<?php foreach($receipts as $r){?>
				<tr>
				<td><?=$r['receipt_id']?></td>
				<td><?=$r['receipt_type']==0?"Deposit":"Topup"?></td>
				<td>Rs <?=$r['receipt_amount']?></td>
				<td><?=$r['bank_name']?></td>
				<td><?php $modes=array("cash","Cheque","DD","Transfer");?><?=$modes[$r['payment_mode']]?></td>
				<td><?=$r['instrument_date']!=0?date("d/m/y",$r['instrument_date']):""?></td>
				<td><?=$r['instrument_no']?></td>
				<td><?=$r['remarks']?></td>
				<td><?php if($r['status']==1) echo 'Activated'; else if($r['status']==0) echo 'Pending'; else if($r['status']==3) echo 'Reversed'; else echo 'Cancelled';?>
				<?php if($r['status']==1 && $r['receipt_type']==1){?>
				<br><a class="danger_link" href="<?=site_url("admin/pnh_reverse_receipt/{$r['receipt_id']}")?>">reverse</a>
				<?php }?>
				</td>
				<td><?=date("g:ia d/m/y",$r['created_on'])?></td>
				<td><?=$r['admin']?></td>
				<td><?=$r['act_by']?></td>
				<td><?=$r['activated_on']!=0?date("g:ia d/m/y",$r['activated_on']):""?></td>
				<td><?=$r['reason']?></td>
				</tr>
				<?php }?>
				</tbody>
				</table>
				</div>
			
		</div>
		
					
		


</div>



<?php if($this->erpm->auth(PNH_EXECUTIVE_ROLE,true)){?>


<div id="actions">

<div style="float:left;">
<h4 style="margin-bottom:0px;">Allotted Member IDs</h4>
<table class="datagrid smallheader">
<thead><tr><th>Start</th><th>End</th><th>Allotted on</th><th>Allotted by</th></tr></thead>
<tbody>
<?php foreach($this->db->query("select m.*,a.name as admin from pnh_m_allotted_mid m join king_admin a on a.id=m.created_by where franchise_id=?",$f['franchise_id'])->result_array() as $m){?>
<tr><td><?=$m['mid_start']?></td><td><?=$m['mid_end']?></td><td><?=date("d/m/y",$m['created_on'])?></td><td><?=$m['admin']?></td>
<?php }?>
</tbody>
</table>
<h4 style="margin-bottom:0px;">Allot Member IDs</h4>
<form action="<?=site_url("admin/pnh_allot_mid/{$f['franchise_id']}")?>" id="allot_mid_form" method="post">
From <input type="text" name="start" class="inp" size=7 maxlength="8"> to <input maxlength="8" type="text" name="end" class="inp" size=7> <input type="submit" value="Allot"> 
</form>
</div>

<div style="float:left;margin-left:20px;">
<h4 style="margin-bottom:0px;">Give Credit</h4>
<form method="post" class="credit_form" action="<?=site_url("admin/pnh_give_credit")?>">
<input type="hidden" name="reason" class="c_reason">
<input type="hidden" name="fid" value="<?=$f['franchise_id']?>">
Enhance credit limit : Rs <?=$f['credit_limit']?> + <input type="text" class="inp" size=4 name="limit"> <input type="submit" value="Add Credit">
</form>
<table class="datagrid smallheader">
<thead><tr><th>Credit Added</th><th>New credit limit</th><th>Reason</th><th>Added by</th><th>Added On</th></tr></thead>
<tbody>
<?php foreach($this->db->query("select c.*,a.name as admin from pnh_t_credit_info c join king_admin a on a.id=c.credit_given_by where franchise_id=? order by id desc",$f['franchise_id'])->result_array() as $c){?>
<tr>
<td><?=$c['credit_added']?></td>
<td><?=$c['new_credit_limit']?></td>
<td><?=$c['reason']?></td>
<td><?=$c['admin']?></td>
<td><?=date("g:ia d/m/y",$c['created_on'])?></td>
</tr>
<?php }?>
</tbody>
</table>
<form method="post" class="credit_form" action="<?=site_url("admin/pnh_give_credit")?>">
<input type="hidden" name="reason" class="c_reason">
<input type="hidden" name="reduce" value="1">
<input type="hidden" name="fid" value="<?=$f['franchise_id']?>">
Reduce credit limit : Rs <?=$f['credit_limit']?> - <input type="text" class="inp" size=4 name="limit"> <input type="submit" value="Reduce Credit">
</form>
</div>

<div class="clear"></div>

</div>


<?php } ?>

<div id="sch_hist">


<div style="float:left;margin-left:20px;width:400px;">
<h4 style="margin-bottom:0px;">Give Scheme Discount</h4>
<form id="sch_form" method="post" action="<?=site_url("admin/pnh_give_sch_discount/{$f['franchise_id']}")?>">
<div style="padding:5px;border:1px solid #aaa;margin:5px 0px;">
<table>
<tr><Td>
Scheme Discount : </td><td><select name="discount">
<?php for($i=1;$i<=10;$i++){?><option value="<?=$i?>"><?=$i?>%</option><?php }?>
</select></td></tr>
<tr><td>Brand :</td><td><select name="brand">
<option value="0">All brands</option>
<?php foreach($this->db->query("select * from king_brands order by name asc")->result_array() as $b){?>
<option value="<?=$b['id']?>"><?=$b['name']?></option>
<?php }?>
</select>
</td></tr>
<tr><td>Category :</td><td><select name="cat">
<option value="0">All categories</option>
<?php foreach($this->db->query("select * from king_categories order by name asc")->result_array() as $b){?>
<option value="<?=$b['id']?>"><?=$b['name']?></option>
<?php }?>
</select>
</td></tr>
<tr><td>
From </td><td><input type="text" class="inp" size="10" name="start" id="d_start"> to <input type="text" class="inp" size="10" name="end" id="d_end"></td></tr>
<tr><td>
Reason </td><td><textarea class="inp" name="reason" style="width:300px;"></textarea></td></tr>
<tr><td></td><td><input type="submit" value="Add Scheme discount"></td></tr>
</table>
</div>
</form>
</div>

<div style="margin-left:20px;float:left;">
<h4 style="margin-bottom:0px;">Scheme Discount History</h4>
<table class="datagrid smallheader">
<thead><tr><th>Discount</th><th>Brand</th><th>Category</th><th>From</th><th>To</th><th>Reason</th><th>Given by</th><th>On</th></tr></thead>
<tbody>
<?php foreach($this->db->query("select h.*,a.name as admin from pnh_sch_discount_track h left outer join king_admin a on a.id=h.created_by where franchise_id=? order by h.id desc",$f['franchise_id'])->result_array() as $h){?>
<tr>
<td><?=$h['sch_discount']?>%</td>
<td><?=$h['brandid']==0?"All Brands":$this->db->query("select name from king_brands where id=?",$h['brandid'])->row()->name?></td>
<td><?=$h['catid']==0?"All Categories":$this->db->query("select name from king_categories where id=?",$h['catid'])->row()->name?></td>
<td><?=date("d/m/y",$h['sch_discount_start'])?></td>
<td><?=date("d/m/y",$h['sch_discount_end'])?></td>
<td><?=$h['reason']?></td>
<td><?=$h['admin']?></td>
<td><?=date("g:ia d/m/y",$h['created_on'])?></td>
</tr>
<?php }?>
</tbody>
</table>
<div style="padding:10px;background:#eee;">
Current Scheme Discount for all brands : <b><?=$f['sch_discount']?>%</b><br>
Valid from : <?=date("d/m/y",$f['sch_discount_start'])?> &nbsp; &nbsp; Valid upto : <?=date("d/m/y",$f['sch_discount_end'])?><br>
<?php if($f['is_sch_enabled']){?>
Status : Enabled <a style="float:right" class="danger_link" href="<?=site_url("admin/pnh_disenable_sch/{$f['franchise_id']}/0")?>">disable</a>
<?php }else{?>
Status : Disabled <a style="float:right" class="danger_link" href="<?=site_url("admin/pnh_disenable_sch/{$f['franchise_id']}/1")?>">enable</a>
<?php }?>
<div class="clear"></div>
</div>
</div>
<div class="clear"></div>

<div style="padding:10px;">
<h4 style="margin-bottom:0px;">Active scheme discounts for brands &amp; categories</h4>
<table class="datagrid smallheader noprint">
<thead><tr><th>Brand</th><th>Category</th><th>Discount</th><th>Valid from</th><TH>Valid upto</TH><th>Added on</th><th>Added by</th><th></th></tr></thead>
<tbody>
<?php foreach($this->db->query("select s.*,a.name as admin,b.name as brand,c.name as category from pnh_sch_discount_brands s left outer join king_brands b on b.id=s.brandid left outer join king_categories c on c.id=s.catid join king_admin a on a.id=s.created_by where s.franchise_id=? and ? between valid_from and valid_to order by id desc",array($fran['franchise_id'],time()))->result_array() as $s){?>
<tr>
<td><?=empty($s['brand'])?"All brands":$s['brand']?></td>
<td><?=empty($s['category'])?"All categories":$s['category']?></td>
<td><?=$s['discount']?>%</td>
<td><?=date("d/m/y",$s['valid_from'])?></td>
<td><?=date("d/m/y",$s['valid_to'])?></td>
<td><?=date("d/m/y",$s['created_on'])?></td>
<td><?=$s['admin']?></td>
	<td><a href="<?=site_url("admin/pnh_expire_scheme_discount/{$s['id']}")?>" class="danger_link">expire</a></td>
</tr>
<?php }?>
</tbody>
</table>
</div>

</div>


<Div id="devices">
<div style="margin-left:20px;">
<h4 style="margin-bottom:0px">Alloted Devices</h4>
<table class="datagrid smallheader">
<thead><tr><th>Device Serial No</th><th>Type</th><th>Allotted on</th></tr></thead>
<tbody>
<?php foreach($devices as $d){?>
<tr>
<td><?=$d['device_sl_no']?></td>
<td><?=$d['device_name']?></td>
<td><?=date("d/m/y",$d['created_on'])?></td>
</tr>
<?php } ?>
</tbody>
</table>
</div>
</div>

<div id="members">

<div>
<div class="dash_bar">
Total Members : <span><?=$this->db->query("select count(1) as l from pnh_member_info where franchise_id=?",$f['franchise_id'])->row()->l?></span>
</div>
<div class="dash_bar">
Last month registered : <span><?=$this->db->query("select count(1) as l from pnh_member_info where franchise_id=? and created_on between ".mktime(0,0,0,-1,1)." and ".mktime(23,59,59,-1,31),$f['franchise_id'])->row()->l?></span>
</div>
<div class="dash_bar">
This month registered : <span><?=$this->db->query("select count(1) as l from pnh_member_info where franchise_id=? and created_on >".mktime(0,0,0,date("m"),1),$f['franchise_id'])->row()->l?></span>
</div>
</div>

<table class="datagrid" width="100%">
<thead><tr><th>Member ID</th><th>Name</th><th>City</th><th>Created On</th></tr></thead>
<tbody>
<?php foreach($this->db->query("select * from pnh_member_info where franchise_id=?",$f['franchise_id'])->result_array() as $m){?>
<tr>
<td><a href="<?=site_url("admin/pnh_viewmember/{$m['user_id']}")?>" class="link"><?=$m['pnh_member_id']?></a></td>
<td><?=$m['first_name']?> <?=$m['last_name']?></td>
<td><?=$m['city']?></td>
<td><?=$m['created_on']==0?"registration form not updated yet":date("g:ia d/m/y",$m['created_on'])?></td>
</tr>
<?php }?>
</tbody>
</table>
</div>



<div id="orders">

<div>
<div class="dash_bar">
Total Orders : <span><?=$this->db->query("select count(1) as l from king_transactions where franchise_id=?",$f['franchise_id'])->row()->l?></span>
</div>
<div class="dash_bar">
Orders last month : <span><?=$this->db->query("select count(1) as l from king_transactions where franchise_id=? and init between ".mktime(0,0,0,date("m")-1,01,date('Y'))." and ".(mktime(0,0,0,date("m"),01,date('Y'))-1),$f['franchise_id'])->row()->l?></span>






</div>
<div class="dash_bar">
Orders this month : <span><?=$this->db->query("select count(1) as l from king_transactions where franchise_id=? and init >".mktime(0,0,0,date("m"),1),$f['franchise_id'])->row()->l?></span>
</div>

<div class="dash_bar">
Total Order value : <span>Rs <?=number_format($this->db->query("select sum(amount) as l from king_transactions where franchise_id=?",$f['franchise_id'])->row()->l,0)?></span>
</div>
<div class="dash_bar">
Value last month : <span>Rs <?=number_format($this->db->query("select sum(amount) as l from king_transactions where franchise_id=? and init between ".mktime(0,0,0,-1,1)." and ".mktime(23,59,59,-1,31),$f['franchise_id'])->row()->l,2)?></span>
</div>
<div class="dash_bar">
Value this month : <span>Rs <?=number_format($this->db->query("select sum(amount) as l from king_transactions where franchise_id=? and init >".mktime(0,0,0,date("m"),1),$f['franchise_id'])->row()->l,2)?></span>
</div>
<div class="dash_bar">
Total commission : <span>Rs <?=number_format($this->db->query("select sum(o.i_coup_discount) as l from king_transactions t join king_orders o on o.transid=t.transid where t.franchise_id=?",$f['franchise_id'])->row()->l,2)?></span>
</div>
<div class="dash_bar">
Total commission this month : <span>Rs <?=number_format($this->db->query("select sum(o.i_coup_discount) as l from king_transactions t join king_orders o on o.transid=t.transid where t.franchise_id=? and o.time>".mktime(0,0,0,date("m"),1),$f['franchise_id'])->row()->l,2)?></span>
</div>
</div>
<div style="clear:both;float: right;">
	<form id="franchise_ord_list_frm" method="post" action="<?php echo site_url('admin/jx_pnh_getfranchiseordersbydate')?>">
		<input type="hidden" name="fid" value="<?php echo $f['franchise_id']?>">
		<b>Show Orders </b> : 
		From :<input type="text" style="width: 90px;" id="ord_fil_from" name="ord_fil_from" value="<?php echo date('Y-m-d',time())?>" />
		To :<input type="text" style="width: 90px;" id="ord_fil_to" name="ord_fil_to" value="<?php echo date('Y-m-d',time())?>" />
			<input type="button" onclick="load_franchise_orders(1)" value="Submit"> 
	</form>
</div>
<div id="franchise_ord_list" style="clear: both">
	
</div>

</div>

<div id="images">
<table width="100%" cellpadding=10>
<tr>
<?php foreach($this->db->query("select * from pnh_franchise_photos where franchise_id=?",$f['franchise_id'])->result_array() as $i=>$img){?>
<td align="center">
<a href="<?=ERP_IMAGES_URL?>franchises/<?=$img['pic']?>" target="_blank"><img src="<?=ERP_IMAGES_URL?>franchises/<?=$img['pic']?>" width="200"></a>
<div><?=$img['caption']?></div>
</td>
<?php if(($i+1)%4==0) echo '</tr><tr>'; }?>
</tr>
</table>
</div>

<div id="bank">
<div style="margin:5px;">
<table class="datagrid">
<thead><tR><th>Bank Name</th><th>Account No</th><th>Branch Name</th><th>IFSC Code</th></tR></thead>
<tbody>
<?php foreach($this->db->query("select * from pnh_franchise_bank_details where franchise_id=?",$f['franchise_id'])->result_array() as $b){?>
<tr>
<td><?=$b['bank_name']?></td>
<td><?=$b['account_no']?></td>
<td><?=$b['branch_name']?></td>
<td><?=$b['ifsc_code']?></td>
</tr>
<?php } ?>
</tbody>
</table>
<?php if(empty($b)) echo "No bank details linked"?>
</div>
<input type="button" value="Add new bank details" onclick='$("#bank_form").show();$(this).hide();'>
<form id="bank_form" method="post" action="<?=site_url("admin/pnh_franchise_bank_details/{$f['franchise_id']}")?>" style="display:none;">
<table style="background:#dedede;padding:5px;margin:10px;" cellpadding=5>
<tr><Th colspan="100%">Add new bank details</Th></tr>
<tr><Td>Bank Name </td><td>:</Td><td><input type="text" class="inp mand bank_name" name="bank_name" size="30"></td></tr>
<tr><Td>Account No </td><td>:</Td><td><input type="text" class="inp  mand account_no" name="account_no" size="20"></td></tr>
<tr><Td>Branch Name </td><td>:</Td><td><input type="text" class="inp mand branch_name" name="branch_name" size="40"></td></tr>
<tr><Td>IFSC Code </td><td>:</Td><td><input type="text" class="inp mand ifsc_code" name="ifsc_code" size="20"></td></tr>
<tr><Td></Td><td><input type="submit" value="Add bank details"></td></tr>
</table>
</form>
</div>


</div>


</div>


<style>
	.subdatagrid{width: 100%}
	.subdatagrid th{padding:5px;font-size: 11px;background: #F4EB9A;color: maroon}
	.subdatagrid td{padding:3px;font-size: 12px;}
	.subdatagrid td a{color: #121213;}
	.cancelled_ord td{text-decoration: line-through;color: #cd0000 !important;}
	.cancelled_ord td a{text-decoration: line-through;color: #cd0000 !important;}
	.tabs ul.tabsul li a {display: block;padding: 5px 10px;}
</style>

<script>

	function load_franchise_orders(stat)
	{
		$('#franchise_ord_list').html("Loading...");
		$.post($('#franchise_ord_list_frm').attr('action'),$('#franchise_ord_list_frm').serialize()+'&stat='+stat,function(resp){
			$('#franchise_ord_list').html(resp);
		});
		return false;
	}
$(function(){

	$('.stat_tabs').tabs();
	
	prepare_daterange('ord_fil_from','ord_fil_to');
	
	$("#d_start,#d_end,#sec_date,#d_ac_from,#d_ac_to").datepicker();
	$(".inst_type").change(function(){
		$(".inst").hide();
		if($(this).val()=="1")
		{
			$(".inst").show().val("");
			$(".inst_no .label").html("Cheque No");
			$(".inst_date .label").html("Cheque Date");
		}
		else if($(this).val()=="2")
		{
			$(".inst").show().val("");
			$(".inst_no .label").html("DD No");
			$(".inst_date .label").html("DD Date");
		}
		else if($(this).val()=="3")
		{
			$(".inst").show().val("");
			$(".inst_date .label").html("Transfer Date");
			$(".inst_no").hide();
		}
	}).val("0").change();
	$("#sch_form").submit(function(){
		if($("#d_start").val().length==0 || $("#d_end").val().length==0)
		{
			alert("Enter start date and end date");
			return false;
		}
		return true;
	});
	$(".credit_form").submit(function(){
		if(!is_integer($("input[name=limit]",$(this)).val()))
		{
			alert("Enter a number");
			return false;
		}
		reason=prompt("Please mention a resaon");
		if(reason.length==0)
			return false;
		$(".c_reason",$(this)).val(reason);
		return true;
	});
	$("#bank_form").submit(function(){
		f=true;
		$("input",$(this)).each(function(){
			if($(this).val().length==0)
			{
				alert($("td:first",$(this).parents("tr").get(0)).text());
				f=false;
				return false;
			}
			return f;
		});
	});

	$("#top_form").submit(function(){
		if(!is_numeric($(".amount",$(this)).val()))
		{
			alert("Enter a valid amount");
			return false;
		}
		if($(".msg",$(this)).val().length==0)
		{
			alert("Enter a message");
			return false;
		}
	});

	$("#allot_mid_form").submit(function(){
		if($("input[name=start]",$(this)).val().length!=8 || $("input[name=end]",$(this)).val().length!=8 || $("input[name=start]",$(this)).val().charAt(0)!="2" || $("input[name=end]",$(this)).val().charAt(0)!="2")
		{
			alert("Please enter valid MID");
			return false;
		}
		return true;
	});

	$("#d_ac_form").submit(function(){
		if($("#d_ac_from").val().length==0 || $("#d_ac_to").val().length==0)
		{
			alert("Please enter valid from and to date");
			return false;
		}
		return true;
	});

	$("#fran_ver_change").change(function(){
		if($(this).val()=="0")
			return;
		if(confirm("Are you sure to change the version?"))
			location="<?=site_url("admin/pnh_fran_ver_change/{$fran['franchise_id']}")?>/"+$(this).val();
	});
	load_franchise_orders(0);
});
</script>

<?php
