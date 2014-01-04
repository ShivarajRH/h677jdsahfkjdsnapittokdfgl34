<?php 
$prioritys=array("Low","Medium","High","Urgent");
?>
<div class="container">
<div>

<div class="dash_bar">
<a href="<?=site_url("admin/support")?>"></a>
<span><?=$this->db->query("select count(1) as l from support_tickets")->row()->l?></span>
Total tickets
</div>
<div class="dash_bar">
<a href="<?=site_url("admin/support/unassigned")?>"></a>
<span><?=$this->db->query("select count(1) as l from support_tickets where status=0")->row()->l?></span>
Unassigned tickets
</div>
<div class="dash_bar">
<a href="<?=site_url("admin/support/opened")?>"></a>
<span><?=$this->db->query("select count(1) as l from support_tickets where status=1")->row()->l?></span>
Opened tickets
</div>
<div class="dash_bar">
<a href="<?=site_url("admin/support/inprogress")?>"></a>
<span><?=$this->db->query("select count(1) as l from support_tickets where status=2")->row()->l?></span>
In-progress tickets
</div>
<div class="dash_bar">
<a href="<?=site_url("admin/support/closed")?>"></a>
<span><?=$this->db->query("select count(1) as l from support_tickets where status=3")->row()->l?></span>
Closed tickets
</div>
<div class="clear"></div>
<div class="dash_bar">
Average resolve time : <span>
<?php $sec=round($this->db->query("select avg(UNIX_timestamp(updated_on)-unix_timestamp(created_on)) as l from support_tickets where status=3")->row()->l);
$d2 = new DateTime();
$d2->add(new DateInterval('PT'.$sec.'S'));
$iv = $d2->diff(new DateTime());
echo $iv->format("%a days %i minutes");
?></span>
</div>

<div class="dash_bar">
Date range: <input type="texT" size="8" class="inp" id="ds_range" value="<?=$this->uri->segment(4)?>"> to <input size="8" type="text" class="inp"id="de_range" value="<?=$this->uri->segment(5)?>"> <input type="button" value="Show" onclick='showrange()'>
</div>

<div class="clear"></div>

</div>

<h2><?=ucfirst($filter)?> support tickets <?=isset($pagetitle)?$pagetitle:"last 30 days"?></h2>

<a href="<?=site_url("admin/addticket")?>">Add new ticket</a>
<table class="datagrid">
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
<?php foreach($tickets as $ticket){?>
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
else if($ticket['type']==4)
	echo 'Common';
else if($ticket['type']==5)
	echo 'PNH Returns';		
else if($ticket['type']==6)
	echo 'Courier Followups';	

?></td>
<td><?=$prioritys[$ticket['priority']]?></td>
<td><?=$ticket['assignedto']?></td>
<td><?=$ticket['created_on']?></td>
<td><?=$ticket['updated_on']?></td>
</tr>
<?php }if(empty($tickets)){?>
<tr><td colspan="100%">no tickets to show</td></tr>
<?php }?>
</tbody>
</table>

</div>


<script>
$(function(){
	$("#ds_range,#de_range").datepicker();
});
function showrange()
{
	if($("#ds_range").val().length==0 ||$("#ds_range").val().length==0)
	{
		alert("Pls enter date range");
		return;
	}
	location='<?=site_url("admin/support/$filter")?>/'+$("#ds_range").val()+"/"+$("#de_range").val(); 
}
</script>



<?php
