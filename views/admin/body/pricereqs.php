<script>
$(function(){
	$(".confirmlink").click(function(){
		return confirm("Are you sure want to do this?");
	});
	$("#negodialog").dialog({
		autoOpen:false,
		width:400,
		buttons:{
			"Cancel":function(){
					$(this).dialog("close");
				},
			"Accept":function(){
					$("form",$(this)).submit();
				}
			}
		});
	$("#negodialog form").submit(function(){
		if(!is_naturalnonzero($("input",$(this)).val()))
		{
			$("#error").html("New price should be a non zero number");
			return false;
		}
		return true;
	});
});
function negod(i){
		$("#negodialog form").attr("action","<?=site_url("admin/prchange/")?>"+"/"+pids[i]+"/accept");
		$("#negodialog").dialog("open");
}
pids=new Array();
<?php foreach($reqs as $i=>$pr){?>
pids[<?=$i?>]="<?=$pr['id']?>";
<?php }?>
</script>
<style>
	.dash_bar{
	color:#333;
	background:#f1faf1;
	padding:10px;
	font-size:15px;
	margin:5px;
	font-family:arial;
	float:left;
	width:205px;
	}
	.dash_bar .count{
	font-size:18px;
	font-weight:bold;
	}
	.dash_bar a{
	font-size:12px;
	float:right;
	padding:0px 5px;
	}
	.pric th{
	background:#8D8761;
	color:#fff;
	}
</style>
<div class="heading">
<div class="headingtext container"><?=$pagetitle?> Price Requests</div>
</div>
<div class="container" style="padding:10px;font-family:arial">
<div class="dash_bar">
<a href="<?=site_url("admin/pricereqs/pending")?>">View</a>
<span class="count"><?=$counts[0]?></span> pending
</div>


<div class="dash_bar">
<a href="<?=site_url("admin/pricereqs/accepted")?>">View</a>
<span class="count"><?=$counts[1]?></span> Accepted
</div>


<div class="dash_bar">
<a href="<?=site_url("admin/pricereqs/denied")?>">View</a>
<span class="count"><?=$counts[2]?></span> Denied
</div>


<div class="dash_bar">
<a href="<?=site_url("admin/pricereqs/completed")?>">View</a>
<span class="count"><?=$counts[3]?></span> Completed
</div>
<div id="negodialog" title="Price Negotiation">
<form action="" method="post">
New price : <input type="text" name="nprice">
</form>
</div>
<?php $f=$this->session->flashdata("prchange");
if($f!=false){?>
<div align="center">
<span style="color:red;background:yellow;font-weight:bold;font-size:13px;"><?=$f?></span>
</div>
<?php }?>
<?php if(!empty($reqs)){?>
<table class="pric" cellspacing="0" cellpadding="5" border="1" style="margin-top:10px;font-size:13px;" width="100%">
<tr><th>ID</th><th>User Name</th><th>Agent</th><th>Item Name</th>
<th>Vendor</th>
<th>Qty</th><th>Req. Price</th><th>Offer Price</th>
<th>MRP</th><th>RSP</th>
<th>Requested on</th>
<?php if($pagetitle=="Pending"){?>
<th>Action</th>
<?php }?>
</tr>
<?php foreach($reqs as $i=>$pr){?>
<tr onmouseover='$("td",$(this)).css("background","#eee")' onmouseout='$("td",$(this)).css("background","#fff")'>
<td><?=$pr['id']?></td>
<td><?=$pr['user']?> <a href="javascript:void(0)" style="float:right;font-size:11px;" onclick='$("div",$(this).parent()).show()'>more</a>
<div style="font-size:12px;display:none">
Email : <?=$pr['email']?><br>
Mobile : <?=$pr['mobile']?><br>
</div>
</td>
<td><?=($pr['special']==4?"YES":"NO")?></td>
<td><?=$pr['name']?></td>
<td><?=$pr['vendor']?></td>
<td><?=$pr['quantity']?></td>
<td>
<?php if($pr['areqprice']==0){?>
<?=$pr['reqprice']?> 
<?php if($pr['status']==0){?>
<a href="javascript:void(0)" onclick='negod(<?=$i?>)' style="float:right;font-size:12px;" id="nego">negotiate</a>
<?php } }else{?>
<?=$pr['reqprice']?> (<?=$pr['areqprice']?>)
<?php }?>
</td>
<td><?=$pr['price']?></td>
<td><?=$pr['orgprice']?></td>
<td><?=$pr['rsp']?></td>
<td><?=date("g:ia d/m/y",$pr['time'])?></td>
<?php if($pagetitle=="Pending"){?>
<td>
<a class="confirmlink" href="<?=site_url("admin/prchange/".$pr['id']."/accept")?>" style="color:blue;font-size:12px;">Accept</a> &nbsp; 
<a class="confirmlink" style="color:blue;font-size:12px;" href="<?=site_url("admin/prchange/".$pr['id']."/deny")?>">Deny</a>
</td>
<?php }?>
</tr>
<?php }?>
</table>
<?php }else{?>
<h2 align="center" style="clear:both;padding-top:20px;">No <?=$pagetitle?> price requests available</h2>
<?php }?>
</div>
<?php
