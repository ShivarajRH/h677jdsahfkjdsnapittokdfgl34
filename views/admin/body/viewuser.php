<div class="container">
<h2>User Details</h2>

<div style="float:left">
<table class="datagrid">
<thead><tr><th colspan=2>Basic Info</th></tr></thead>
<tbody>
<tr><td>Name :</td><td><?=$userdet['name']?></td></tr>
<tr><td>Email :</td><td><?=$userdet['email']?></td></tr>
<tr><td>Mobile :</td><td><?=$userdet['mobile']?></td></tr>
<tr><td>Login Type:</td><td><?php if($userdet['special']==0) echo 'Email';else if($userdet['special']==1) echo "Twitter"; else if($userdet['special']==2) echo 'Facebook'; else if($userdet['special']==3) echo 'Google';?></td></tr>
<tr><td>Verified :</td><td><?=$userdet['verified']?"YES":"NO"?></td></tr>
<tr><td>City :</td><td><?=$userdet['city']?></td></tr>
<tr><td>User Since :</td><td><?=date("d/m/Y",$userdet['createdon'])?></td></tr>
</tbody>
</table>
</div>

<div style="float:left">
<div class="dash_bar">
Total Order Value : <span>Rs <?=number_format($this->db->query("select sum(t.amount) as l from king_transactions t where t.transid in (select transid from king_orders o where o.userid=?)",$userdet['userid'])->row()->l)?></span>
</div>
<div class="dash_bar">
Total Transactions : <span><?=$this->db->query("select count(distinct(transid)) as l from king_orders where userid=?",$userdet['userid'])->row()->l?></span>
</div>
<div class="dash_bar">
Total No of support tickets : <span><?=$this->db->query("select count(1) as l from support_tickets where user_id=?",$userdet['userid'])->row()->l?></span>
</div><br>
<div class="dash_bar">
Coupons used : <span><?=$this->db->query("select count(distinct(o.transid)) as l from king_orders o join king_used_coupons c on c.transid=o.transid where o.userid=?",$userdet['userid'])->row()->l?></span>
</div>

<div style="float:right;clear:left;padding-top:10px;">
<table class="datagrid" style="width:400px;">
<thead><tr><th colspan="100%">Shipping Address</th></tr></thead>
<tbody>
<tr><td>Address</td><td><?=$userdet['address']?></td></tr>
<tr><td>Landmark</td><td><?=$userdet['landmark']?></td></tr>
<tr><td>City</td><td><?=$userdet['city']?></td></tr>
<tr><td>State</td><td><?=$userdet['state']?></td></tr>
</tbody>
</table>
</div>

<div style="clear:left;"></div>
<div style="padding:30px;">
<?php $s=$userdet['temperament']; $temps=array("Very Good","Good","Normal","Bad","Very Bad");$texts=array(":)",":)",":|",":(",":(");$colors=array("green","#2f2","#fa0","#f55","red");?>
<div class="smiley" style="background:<?=$colors[$s]?>;">
<?=$texts[$s]?>
</div>
<div style="float:left;margin-left:60px;margin-top:5px;">
<span id="temp_text_disp" style="color:<?=$colors[$s]?>;font-weight:bold;">
<?=$temps[$s]?>
</span><br>
<a href="javascript:void(0)" onclick='$(this).hide();$("#temp_text_disp").hide();$("#temperament_change").show();' style="color:blue;font-size:60%;">change</a>
<select id="temperament_change" STYLE="display:none;margin-top:-20px;">
<?php foreach($temps as $i=>$t){?>
<option value="<?=$i?>" <?=$s==$i?"selected":""?>><?=$t?></option>
<?php }?>
</select>
</div>
</div>
<div style="clear:left;padding-top:10px;">
</div>
</div>
<div class="clear"></div>

<div style="float:left">
<h3> Recent Orders by User</h3>
<table class="datagrid">
<thead><tr><th>TRANSID</th><th>ORDERED ON</th><th>LAST UPDATED</th></tr></thead>
<tbody>
<?php foreach($orders as $o){?>
<tr>
<td><a class="link" href="<?=site_url("admin/trans/".$o['transid'])?>"><?=$o['transid']?></a></td>
<td><?=date("g:ia d/m/y",$o['time'])?></td>
<td><?=date("g:ia d/m/y",$o['actiontime'])?></td>
</tr>
<?php }?>
</tbody>
</table>
</div>


<div style="float:left;margin-left:30px;">
<h3>Recent failed transactions</h3>
<table class="datagrid">
<thead><tr><th>TRANSID</th><th>TIME</th></tr></thead>
<tbody>
<?php foreach($ftrans as $t){?>
<tr>
<td><a class="link" href="<?=site_url("callcenter/trans/{$t['transid']}")?>"><?=$t['transid']?></a></td>
<td><?=date("g:ia d/m/y",$t['time'])?></td>
</tr>
<?php }?>
</tbody>
</table>
</div>

<div style="float:left;margin-left:30px;">
<h3>Recent support tickets</h3>
<table class="datagrid">
<thead><tr><th>Ticket</th><th>Status</th><th>Created on</th></tr></thead>
<tbody>
<?php foreach($tickets as $t){?>
<tr>
<td><a class="link" href="<?=site_url("admin/ticket/{$t['ticket_id']}")?>">TK<?=$t['ticket_no']?></a></td>
<td><?php switch($t['status']){
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
<td><?=$t['created_on']?></td>
</tr>
<?php }?>
</tbody>
</table>
</div>


</div>

<style>
.smiley{
border:1px solid #ccc;
background:yellow;
color:#000;
-moz-border-radius:30px;
border-radius:30px;
-moz-transform:rotate(-270deg); 
-moz-transform-origin: bottom left;
-webkit-transform: rotate(-270deg);
-webkit-transform-origin: bottom left;
-o-transform: rotate(-270deg);
-o-transform-origin:  bottom left;
filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=1);
font-weight:bold;
height:30px;
width:20px;
font-size:200%;
float:left;
padding:15px;
padding-top:5px;
margin-top:-60px;
}
</style>
<script>
$(function(){
	$("#temperament_change").change(function(){
		location="<?=site_url("admin/changeusertemp/{$userdet['userid']}")?>/"+$(this).val();
	});
});
</script>
<?php
