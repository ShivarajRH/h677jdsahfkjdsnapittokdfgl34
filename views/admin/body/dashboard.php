<style>
.leftcont{
display:none;
}
</style>

<?php 
	if($load_dashboard)
	{
?>

<div class="container">
<h2>Dashboard</h2>
<table width="100%">
<tr>
<td valign="top" width="25%" style="background:#efefef;padding:5px;">
<?php 
if($this->erpm->auth(PNH_EXECUTIVE_ROLE,true) && !$this->erpm->auth(true,true)){
$adm=$this->erpm->getadminuser();
?>
<div class="dash_bar2">
<div class="head">Total Overall Orders
<b><?=$this->db->query("select count(distinct(o.transid)) as l from king_orders o join king_transactions t on t.transid=o.transid join pnh_franchise_owners ow on ow.admin=? and ow.franchise_id=t.franchise_id",$adm['userid'])->row()->l?></b>
</div>
<div class="body">
<span>Total value : </span><b><span>Rs <?=$this->db->query("select sum((o.i_price-o.i_coup_discount)*quantity) as l from king_orders o join king_transactions t on t.transid=o.transid join pnh_franchise_owners ow on ow.admin=? and ow.franchise_id=t.franchise_id",$adm['userid'])->row()->l?></span></b><br>
</div>
</div>

<div class="dash_bar2">
<div class="head">Total Orders this month
<b><?=$this->db->query("select count(distinct(o.transid)) as l from king_orders o join king_transactions t on t.transid=o.transid join pnh_franchise_owners ow on ow.admin=? and ow.franchise_id=t.franchise_id where time>".mktime(0,0,0,date("n"),1),$adm['userid'])->row()->l?></b>
</div>
<div class="body">
<span>Total value : </span><b><span>Rs <?=$this->db->query("select sum((i_price-o.i_coup_discount)*quantity) as l from king_orders o  join king_transactions t on t.transid=o.transid join pnh_franchise_owners ow on ow.admin=? and ow.franchise_id=t.franchise_id where time>".mktime(0,0,0,date("n"),1),$adm['userid'])->row()->l?></span></b>
</div>
</div>

<div class="dash_bar2">
<div class="head">Total Orders previous month
<b><?=$this->db->query("select count(distinct(o.transid)) as l from king_orders o join king_transactions t on t.transid=o.transid join pnh_franchise_owners ow on ow.admin=? and ow.franchise_id=t.franchise_id where o.time between ? and ?",array($adm['userid'],mktime(0,0,0,date("n")-1,1),mktime(0,0,0,date("n"),date("t"))))->row()->l?></b>
</div>
<div class="body">
<span>Total value : </span><b><span>Rs <?=$this->db->query("select sum(i_price*quantity) as l from king_orders o join king_transactions t on t.transid=o.transid join pnh_franchise_owners ow on ow.admin=? and ow.franchise_id=t.franchise_id  where o.time between ? and ?",array($adm['userid'],mktime(0,0,0,date("n")-1,1),mktime(0,0,0,date("n"),1)))->row()->l?></span></b>
</div>
</div>
<?php }?>

<?php if($this->erpm->auth(true,true)){?>

<div class="dash_bar2">
<div class="head">Total Overall Orders
<b><?=$this->db->query("select count(distinct(transid)) as l from king_orders")->row()->l?></b>
</div>
<div class="body">
<span>Total value : </span><b><span>Rs <?=$this->db->query("select sum(i_price*quantity) as l from king_orders")->row()->l?></span></b><br>
<span>Pending : </span><b><span><?=$this->db->query("select count(1) as l from king_orders where status=0")->row()->l?></span></b>
</div>
</div>

<div class="dash_bar2">
<div class="head">Total Orders this month
<b><?=$this->db->query("select count(distinct(transid)) as l from king_orders where time>?",mktime(0,0,0,date("n"),1))->row()->l?></b>
</div>
<div class="body">
<span>Total value : </span><b><span>Rs <?=$this->db->query("select sum(i_price*quantity) as l from king_orders where time>?",mktime(0,0,0,date("n"),1))->row()->l?></span></b>
</div>
</div>

<div class="dash_bar2">
<div class="head">Total Orders previous month
<b><?=$this->db->query("select count(distinct(transid)) as l from king_orders where time between ? and ?",array(mktime(0,0,0,date("n")-1,1),mktime(0,0,0,date("n"),date("t"))))->row()->l?></b>
</div>
<div class="body">
<span>Total value : </span><b><span>Rs <?=$this->db->query("select sum(o.i_price*o.quantity) as l from king_orders o where time between ? and ?",array(mktime(0,0,0,date("n")-1,1),mktime(0,0,0,date("n"),1)))->row()->l?></span></b>
</div>
</div>

<div class="dash_bar2">
<div class="head">
Total POs this month <b><?=$this->db->query("select count(1) as l from t_po_info where created_on>?",date("Y-m-d",mktime(0,0,0,date("n"),1)))->row()->l?></b></div>
<div class="body">
<span>Total value : </span><b><span>Rs <?=number_format($this->db->query("select sum(total_value) as l from t_po_info where created_on>?",date("Y-m-d",mktime(0,0,0,date("n"),1)))->row()->l)?></span></b>
</div>
</div>

<div class="dash_bar2">
<div class="head">
Total POs previous month <b><?=$this->db->query("select count(1) as l from t_po_info where created_on between ? and ?",array(date("Y-m-d",mktime(0,0,0,date("n")-1,1)),date("Y-m-d",mktime(0,0,0,date("n"),date("t")))))->row()->l?></b></div>
<div class="body">
<span>Total value : </span><b><span>Rs <?=number_format($this->db->query("select sum(total_value) as l from t_po_info where created_on between ? and ?",array(date("Y-m-d",mktime(0,0,0,date("n")-1,1)),date("Y-m-d",mktime(0,0,0,date("n"),date("t")))))->row()->l)?></span></b>
</div>
</div>
<?php }?>

<?php if($this->erpm->auth(CALLCENTER_ROLE,true)){?>

<div class="dash_bar2">
<div class="head"><b><?=$this->db->query("select count(1) as l from support_tickets")->row()->l?></b>Total tickets</div>
<div class="body">
<span>Unassigned :</span><?=$this->db->query("select count(1) as l from support_tickets where status=1")->row()->l?>, 
<span>Opened :</span><?=$this->db->query("select count(1) as l from support_tickets where status=1")->row()->l?>, 
<span>In Progress :</span><?=$this->db->query("select count(1) as l from support_tickets where status=1")->row()->l?>, 
<span>Closed :</span><?=$this->db->query("select count(1) as l from support_tickets where status=1")->row()->l?> 
</div>
</div>

<?php }?>

<?php if($this->erpm->auth(true,true)){?>

<div class="dash_bar2">
<div class="head">
Average ticket resolve time</div>
<div class="body">
<span>
<?php $sec=round($this->db->query("select avg(UNIX_timestamp(updated_on)-unix_timestamp(created_on)) as l from support_tickets where status=3")->row()->l);
$d2 = new DateTime();
$d2->add(new DateInterval('PT'.$sec.'S'));
$iv = $d2->diff(new DateTime());
echo $iv->format("%m months %d days %i minutes");
?></span>
</div>
</div>
<?php }?>

</td>


<td valign="top" class="nobmargin" width="75%" style="background:#fafafa;padding-left:10px;">

<?php if($this->erpm->auth(PNH_EXECUTIVE_ROLE,true) && !$this->erpm->auth(true,true)){ ?>

<div>
<div class="dash_bar">
Total Executives : <span><?=$this->db->query("select count(1) as l from pnh_franchise_owners where admin=?",$adm['userid'])->row()->l?></span>
</div>
<div class="dash_bar">
Total Members : <span><?=$this->db->query("select count(1) as l from pnh_franchise_owners ow join pnh_member_info m on m.franchise_id=ow.franchise_id where admin=?",$adm['userid'])->row()->l?></span>
</div>
<div class="clear"></div>
</div>

<h4>Franchises</h4>
<a href="<?=site_url("admin/pnh_addfranchise")?>">Add Franchise</a>

<table class="datagrid" width="100%" style="margin-top:10px;">
<thead><tr><th>Franchise Name</th><th>FID</th><th>Assigned to</th><th>City</th><th>Territory</th><th>Assigned to</th><th>Class</th><th>Margin</th><th></th></tr></thead>
<tbody>
<?php foreach($this->erpm->pnh_getfranchises() as $f){?>
<tr>
<td><a class="link" href="<?=site_url("admin/pnh_franchise/{$f['franchise_id']}")?>"></a><?=$f['franchise_name']?></td>
<td><?=$f['pnh_franchise_id']?></td>
<td><?=$f['owners']?></td>
<td><?=$f['city']?></td>
<td><?=$f['territory_name']?></td>
<td><?=$f['assigned_to']?></td>
<td><?=$f['class_name']?></td>
<td><?=$f['margin']?></td>
<td>
<a style="white-space:nowrap" href="<?=site_url("admin/pnh_franchise/{$f['franchise_id']}")?>">view</a> &nbsp;&nbsp;&nbsp; 
<a style="white-space:nowrap" href="<?=site_url("admin/pnh_edit_fran/{$f['franchise_id']}")?>">edit</a> &nbsp;&nbsp;&nbsp; 
<a style="white-space:nowrap" href="<?=site_url("admin/pnh_manage_devices/{$f['franchise_id']}")?>">manage devices</a> &nbsp;&nbsp;&nbsp;
<a  style="white-space:nowrap" href="<?=site_url("admin/pnh_assign_exec/{$f['franchise_id']}")?>">assign executives</a>
</td>
</tr>
<?php }?>
</tbody>
</table>
<?php } ?>

<?php if($this->erpm->auth(CALLCENTER_ROLE,true)){?>
<h4>Recent Orders</h4>
<div>
<?php $orders=$this->erpm->getordersbytransaction();?>
<table class="datagrid grey noprint" width="100%">
<thead>
<tr>
<th>Transaction ID</th>
<th>User</th>
<th>Items</th>
<th>Amount</th>
<th>Ship To</th>
<th>Ordered on</th>
<th>Last action on</th>
</tr>
</thead>
<tbody>
<?php foreach($orders as $i=>$o){ if($i>4) break;?>
<tr>
<td><a href="<?=site_url("admin/trans/{$o['transid']}")?>" class="link"><?=$o['transid']?></a></td>
<td><a href="<?=site_url("admin/user/{$o['userid']}")?>"><?=$o['name']?></a></td>
<td><?=$o['items']?></td>
<td>Rs <?=$o['amount']?></td>
<td><?=ucfirst($o['ship_city'])?></td>
<td><?=date("g:ia d/m/y",$o['init'])?></td>
<td><?=$o['actiontime']==0?"na":date("g:ia d/m/y",$o['actiontime'])?></td>
</tr>
<?php }?>
</tbody>
</table>
</div>

<?php }?>
<?php if($this->erpm->auth(true,true)){?>
<h4>Recent Purchase Orders</h4>
<table class="datagrid grey noprint" width="100%">
<thead>
<tr>
<th>ID</th>
<th>Created On</th>
<th>Vendor</th>
<th>Value</th>
<th>Purchase Status</th>
<th>Stock Status</th>
<th></th>
<th>Remarks</th>
</tr>
</thead>
<tbody>
<?php		$pos=$this->erpm->getpos();
 foreach($pos as $i=>$p){ if($i>4) break;?>
<tr>
<td>PO<?=$p['po_id']?></td>
<td><?=$p['created_on']?></td>
<td><a href="<?=site_url("admin/vendor/{$p['vendor_id']}")?>"><?=$p['vendor_name']?></a></td>
<td>Rs <?=number_format($p['total_value'])?></td>
<td><?php switch($p['po_status']){
	case 1:
	case 0: echo 'Open'; break;
	case 2: echo 'Complete'; break;
	case 3: echo 'Cancelled';
}?></td>
<td>
<?php switch($p['po_status']){
	case 0: echo 'Not received'; break;
	case 1: echo 'Partially received'; break;
	case 2: echo 'Fully received'; break;
	case 3: echo 'NA';
}?>
</td>
<td>
<a class="link" href="<?=site_url("admin/viewpo/{$p['po_id']}")?>">view</a>
<?php if($p['po_status']!=2 && $p['po_status']!=3){?>
&nbsp;&nbsp;&nbsp;<a href="<?=site_url("admin/apply_grn/{$p['po_id']}")?>">Stock Intake</a>
<?php }?>
</td>
<td><?=$p['remarks']?></td>
</tr>
<?php }?>
</tbody>
</table>

<?php }?>

<?php 

if($this->erpm->auth(CALLCENTER_ROLE,true)){

$prioritys=array("Low","Medium","High","Urgent");
$tickets=$this->erpm->gettickets("all");
?>
<h4>Recent Support Tickets</h4>
<table class="datagrid grey noprint">
<thead>
<tr>
<th>Ticket No</th>
<th>User</th>
<th>Email</th>
<th>Transaction</th>
<th>Status</th>
<th>Type</th>
<th>Priority</th>
<th>Assigned To</th>
<th>Created on</th>
<th>Last activity on</th>
</tr>
</thead>
<tbody>
<?php foreach($tickets as $i=>$ticket){if($i>4) break;?>
<tr>
<td><a class="link" href="<?=site_url("admin/ticket/{$ticket['ticket_id']}")?>">TK<?=$ticket['ticket_no']?></a></td>
<td><?=$ticket['user']?></td>
<td><?=$ticket['email']?></td>
<td><?=$ticket['transid']?></td>
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
<td><?=$prioritys[$ticket['priority']]?></td>
<td><?=$ticket['assignedto']?></td>
<td><?=$ticket['created_on']?></td>
<td><?=$ticket['updated_on']?></td>
</tr>
<?php }?>
</tbody>
</table>

<?php }?>

</td>
</tr>
</table>
</div>
<?php
}else
{
?>
	<div id="main_dashholder">
		<h3 align="center">Loading Dashboard, Please wait... <br> <img src="<?php echo base_url().'/images/loading_bar.gif';?>" ></h3>
	</div>
	<script type="text/javascript">
		$.get(site_url+'/admin/jx_load_dashboard','',function(resp){
			$('#main_dashholder').html(resp);
		});
	</script>
<?php 	
}
	
