<?php $u=$member; 
$gender=array("Male","Female");
$salutation=array("Mr","Mrs","Ms");
$marital=array("Single","Married","Other");
$expenses=array("&lt; Rs. 2000","Rs 2001 - Rs 5000","Rs 5001 - Rs 10000","&gt; Rs. 10000");
?>
<div class="container vm">
<h2>Storeking Member Details</h2>

<div>
<div class="dash_bar">Member ID : <span><?=$u['pnh_member_id']?></span></div>
<div class="dash_bar">Loyalty Points : <span><?=$u['points']?></span></div>
<div class="dash_bar">Total orders : <span><?=$this->db->query("select count(distinct(transid)) as l from king_orders where userid=?",$u['user_id'])->row()->l?></span></div>
<div class="dash_bar">Total orders value : <span>Rs <?=number_format($this->db->query("select sum(t.amount) as l from king_transactions t where t.transid in (select transid from king_orders o where o.userid=?)",$u['user_id'])->row()->l)?></span></div>
<div class="dash_bar">Franchise : <span><?=$this->db->query("select concat(franchise_name,', ',city) as name from pnh_m_franchise_info where franchise_id=?",$u['franchise_id'])->row()->name?></span></div>
<div class="dash_bar">Activated voucher value : <span>Rs <?=$this->db->query("SELECT SUM(denomination) AS voucher_value  FROM pnh_t_voucher_details l JOIN `pnh_m_voucher`v ON v.voucher_id=l.voucher_id WHERE STATUS>=3 AND member_id=?",$u['pnh_member_id'])->row()->voucher_value?></span></div>
<div class="dash_bar">Voucher Balance : <span>Rs <?=$this->db->query("SELECT SUM(customer_value) AS voucher_bal FROM pnh_t_voucher_details WHERE STATUS in (3,5) AND member_id=?",$u['pnh_member_id'])->row()->voucher_bal?></span></div>
</div>

<div class="clear"></div>

<div class="tab_view">

<ul>
<li><a href="#details">Details</a></li>
<li><a href="#orders">Orders</a></li>
<li><a href="#points">Loyalty points</a></li>
<li><a href="#voucher">Voucher Log</a></li>
</ul>
<div id="voucher">
<div class="tab_view tab_view_inner">

<ul>
<li><a href="#activated_voucher">Activated Voucher</a></li>
<li><a href="#fully_redeemed">Fully Redeemed</a></li>
<li><a href="#partially_redeemed">Partially Redeemed</a></li>
<li><a href="#not_redeemed">Not Redeemed</a></li>
</ul>

<div id="activated_voucher">
<b>Activated Vouchers List</b>
<br></br>
<?php $ac_v=$this->db->query("SELECT t.*,f.franchise_name,v.denomination,l.debit,l.transid,m.name AS menu FROM pnh_t_voucher_details t  JOIN pnh_m_franchise_info f ON f.franchise_id=t.franchise_id  JOIN pnh_m_voucher v ON v.voucher_id=t.voucher_id  JOIN pnh_voucher_activity_log l ON l.voucher_slno=t.voucher_serial_no  JOIN pnh_t_book_voucher_link b ON b.voucher_slno_id=t.id  JOIN pnh_t_book_details c ON c.book_id=b.book_id  JOIN pnh_t_book_allotment e ON e.book_id=c.book_id  JOIN pnh_m_book_template q ON q.book_template_id=c.book_template_id  JOIN pnh_menu m ON m.id=q.menu_ids WHERE t.status>=3 AND t.member_id=? AND l.status=1",$u['pnh_member_id']);
if($ac_v -> num_rows()){?>
<table class="datagrid">
<thead><th>Slno</th><th>Voucher slno</th><th>Menu</th><th>Franchise Name</th><th>Voucher Value</th><th>Activated On</th></thead>
<?php $i=1;
foreach($ac_v->result_array() as $ac ){
?>
<tbody>
<tr>
<td><?php echo $i;?></td>
<td><?php echo $ac['voucher_serial_no'];?></td>
<td><?php echo $ac['menu'];?></td>
<td><?php echo $ac['franchise_name'];?></td>
<td><?php echo $ac['denomination'];?></td>
<td><?php echo format_datetime($ac['activated_on']);?></td>
</tr>
</tbody>
<?php $i++; }?>
</table>
<?php } else echo "<b align='centre'>No Data Found</b>";?>
</div>

<div id="fully_redeemed">
<b>Fully Redeemed Voucher Log</b>
<br></br>
<?php $ac_v=$this->db->query("SELECT t.*,f.franchise_name,v.denomination,l.debit,l.transid,m.name AS menu FROM pnh_t_voucher_details t  JOIN pnh_m_franchise_info f ON f.franchise_id=t.franchise_id  JOIN pnh_m_voucher v ON v.voucher_id=t.voucher_id  JOIN pnh_voucher_activity_log l ON l.voucher_slno=t.voucher_serial_no  JOIN pnh_t_book_voucher_link b ON b.voucher_slno_id=t.id  JOIN pnh_t_book_details c ON c.book_id=b.book_id  JOIN pnh_t_book_allotment e ON e.book_id=c.book_id  JOIN pnh_m_book_template q ON q.book_template_id=c.book_template_id  JOIN pnh_menu m ON m.id=q.menu_ids WHERE t.status=4 AND l.status=1 AND t.member_id=?",$u['pnh_member_id']);
if($ac_v -> num_rows()){?>
<table class="datagrid">
<thead><th>Slno</th><th>Voucher slno</th><th>Menu</th><th>Franchise Name</th><th>Voucher Value</th><th>Transaction Id</th><th>Redeemed Value</th><th>Voucher Balance</th><th>Redeemed On</th></thead>
<?php $i=1;
foreach($ac_v->result_array() as $ac ){
?>
<tbody>
<tr>
<td><?php echo $i;?></td>
<td><?php echo $ac['voucher_serial_no'];?></td>
<td><?php echo $ac['menu'];?></td>
<td><?php echo $ac['franchise_name'];?></td>
<td><?php echo $ac['denomination'];?></td>
<td><a href="<?=site_url("admin/trans/".$ac['transid'])?>" class="link" target="_blank"><?php echo $ac['transid'];?></a></td>
<td><?php echo $ac['debit'];?></td>
<td><?php echo $ac['customer_value'];?></td>
<td><?php echo format_datetime($ac['redeemed_on']);?></td>
</tr>
</tbody>
<?php $i++;}?>
</table>
<?php }else echo "<b align='centre'>No Data Found</b>";?>
</div>

<div id="partially_redeemed">
<b>Partially Redeemed Voucher Log</b>
<br><br>
<?php $ac_v=$this->db->query("SELECT t.*,f.franchise_name,v.denomination,l.debit,l.transid,m.name AS menu FROM pnh_t_voucher_details t  JOIN pnh_m_franchise_info f ON f.franchise_id=t.franchise_id  JOIN pnh_m_voucher v ON v.voucher_id=t.voucher_id  JOIN pnh_voucher_activity_log l ON l.voucher_slno=t.voucher_serial_no  JOIN pnh_t_book_voucher_link b ON b.voucher_slno_id=t.id  JOIN pnh_t_book_details c ON c.book_id=b.book_id  JOIN pnh_t_book_allotment e ON e.book_id=c.book_id  JOIN pnh_m_book_template q ON q.book_template_id=c.book_template_id  JOIN pnh_menu m ON m.id=q.menu_ids WHERE t.status=5 AND l.status=1 AND t.member_id=?",$u['pnh_member_id']);
if($ac_v -> num_rows()){?>
<table class="datagrid">
<thead><th>Slno</th><th>Voucher slno</th><th>Menu</th><th>Franchise Name</th><th>Voucher Value</th><th>Transaction Id</th><th>Redeemed Value</th><th>Voucher Balance</th><th>Redeemed On</th></thead>
<?php $i=1;
foreach($ac_v->result_array() as $ac ){
?>
<tbody>
<tr>
<td><?php echo $i;?></td>
<td><?php echo $ac['voucher_serial_no'];?></td>
<td><?php echo $ac['menu'];?></td>
<td><?php echo $ac['franchise_name'];?></td>
<td><?php echo $ac['denomination'];?></td>
<td><a href="<?=site_url("admin/trans/".$ac['transid'])?>" class="link" target="_blank"><?php echo $ac['transid'];?></a></td>
<td><?php echo $ac['debit'];?></td>
<td><?php echo $ac['customer_value'];?></td>
<td><?php echo format_datetime($ac['redeemed_on']);?></td>
</tr>
</tbody>
<?php $i++;}?>
</table>
<?php } else echo "<b align='centre'>No Data Found</b>";?>
</div>

<div id="not_redeemed">
<b>Activated Vouchers List</b>
<br></br>
<?php $ac_v=$this->db->query("select t.*,f.franchise_name,v.denomination from pnh_t_voucher_details t join pnh_m_franchise_info f on f.franchise_id=t.franchise_id join pnh_m_voucher v on v.voucher_id=t.voucher_id where status=3 and t.member_id=?",$u['pnh_member_id']);
if($ac_v -> num_rows()){?>
<table class="datagrid">
<thead><th>Slno</th><th>Voucher slno</th><th>Franchise Name</th><th>Voucher Value</th><th>Activated On</th></thead>
<?php $i=1;
foreach($ac_v->result_array() as $ac ){
?>
<tbody>
<tr>
<td><?php echo $i;?></td>
<td><?php echo $ac['voucher_serial_no'];?></td>
<td><?php echo $ac['franchise_name'];?></td>
<td><?php echo $ac['denomination'];?></td>
<td><?php echo format_datetime($ac['activated_on']);?></td>
</tr>
</tbody>
<?php $i++; }?>
</table>
<?php } else echo "<b align='centre'>No Data Found</b>";?>
</div>

</div>
</div>
<div id="details">

<div style="background:#eee;padding:5px;font-weight:bold;">Personal Data</div>

<div style="float:right;margin:20px;">
<div class="dash_bar">Is MID Card batch processed? : <span><?=$u['is_card_printed']?"YES":"PENDING"?></span></div>
</div>


<table cellpadding=3>
<tr><td class="label">Gender</td><td><?=$gender[$u['gender']]?></td></tr>
<tr><td class="label">Name</td><td><?=$salutation[$u['salute']]?>. <?="{$u['first_name']} {$u['last_name']}"?></td></tr>
<tr><td class="label">DOB</td><td><?=date("d/m/Y",strtotime($u['dob']))?></td></tr>
<tr><td class="label">Address</td><td><?=nl2br($u['address'])?></td></tr>
<tr><td class="label">City</td><td><?=$u['city']?></td></tr>
<tr><td class="label">Pin Code</td><td><?=$u['pincode']?></td></tr>
<tr><td class="label">Mobile</td><td><?=$u['mobile']?></td></tr>
<tr><td class="label">Email</td><td><?=$u['email']?></td></tr>
</table>


<div style="margin-top:10px;background:#eee;padding:5px;font-weight:bold;">Help us to know you better!</div>

<table cellpadding=0>
<tr>
<td width="50%">
<table cellpadding=3>
<tr><td style="height:35px;" class="label">Marital Status</td><td><?=$marital[$u['marital_status']]?></td></tr>
<tr><td class="label">Spouse Name</td><td><?=$u['spouse_name']?></td></tr>
<tr><td class="label">Child's Name</td><td><?=$u['child1_name']?></td></tr>
<tr><td class="label">Child's Name</td><td><?=$u['child2_name']?></td></tr>
</table>
</td>
<td width="50%">
<table cellpadding=3>
<tr><td class="label">Wedding Anniversary</td><td><?=$u['anniversary']==0?"na":date("d/m/Y",strtotime($u['anniversary']))?></td></tr>
<tr><td class="label">DOB</td><td><?=$u['child1_dob']==0?"na":date("d/m/Y",strtotime($u['child1_dob']))?></td></tr>
<tr><td class="label">DOB</td><td><?=$u['child2_dob']==0?"na":date("d/m/Y",strtotime($u['child2_dob']))?></td></tr>
</table>
</td>
</tr>
</table>

<table cellpadding=5>
<tr><td class="label">Profession</td><td><?=$u['profession']?></td></tr>
<tr>
<td class="label">Monthly Shopping Expense of your Household</td>
<td><?=$expenses[$u['expense']]?></td>
</tr>
</table>

</div>

<div id="orders">
<table class="datagrid">
<thead><tr><th>Transid</th><th>Amount</th><th>Cancelled/Returned</th><th>Payable</th><th>Ordered On</th><th>Status</th><?php /*?><th>Payment Type</th>/*/?></tr></thead>
<tbody>
<?php $status=array("Pending","Invoiced","Shipped","Cancelled"); foreach($this->db->query("select o.*,t.amount,sum((o.i_orgprice-(i_discount+i_coup_discount))*o.quantity) as payable,l.voucher_slno 
	from king_orders o 
	join king_transactions t on t.transid=o.transid 
	left join pnh_voucher_activity_log l on l.transid=o.transid 
	where o.userid=? and o.status not in (3,4)   
	group by o.transid 
	order by sno desc",$u['user_id'])->result_array() as $o){?>
<tr>
<td><a class="link" href="<?=site_url("admin/trans/{$o['transid']}")?>"><?=$o['transid']?></a></td>
<td><?=$o['amount']?></td>
<td><?=$o['amount']-$o['payable']?></td>
<td><?=$o['payable']?></td>
<td><?=date("g:ia d/m/y",$o['time'])?></td>
<td><?=$status[$o['status']]?></td>
<?php /*?>
<td><?php echo  $o['voucher_slno']?'<b>Prepaid</b>':'<b>Postpaid</b>'?></td>
<?php /*/?>
</tr>
<?php }?>
</tbody>
</table>
</div>

<div id="points">
<table class="datagrid">
<thead><tr><th>Transid</th><th>Points</th><th>Allotted on</th></tr></thead>
<tbody>
<?php  foreach($this->db->query("select * from pnh_member_points_track where user_id=?",$u['user_id'])->result_array() as $t){?>
<tr>
<td><a href="<?=site_url("admin/trans/".$t['transid'])?>" class="link"><?=$t['transid']?></a></td>
<td><?=$t['points']?></td>
<td><?=date("d/m/y",$t['created_on'])?></td>
</tr>
<?php }?>
</tbody>
</table>
</div>

</div>



</div>
<style>
.vm #details td{
padding:7px;
background:#dfdfff;
}
.label{
font-weight:bold;
width:100px;
background:#eee !important;
}
</style>
<script>
$('.tab_view').tabs();
</script>
<?php
