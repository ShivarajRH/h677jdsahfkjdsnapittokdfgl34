<div class="container">
<div style="float:right">
<b>Filter : </b> Starting from <input size=8 type="text" id="sdate" <?php if($sdate!=0) echo 'value="'.date("d-m-Y",$sdate).'"'?>> and/or ending <input size=8 type="text" id="edate" <?php if($edate!=0) echo 'value="'.date("d-m-Y",$edate).'"'?>> <input type="button" id="gofilter" value="Filter">
</div>
<h2>Audit</h2>
<b style="font-size:14px;text-transform:capitalize;">
<?php 
if($sdate!=0 || $edate!=0)
{
	if($sdate!=0 && $edate!=0)
		echo 'between '.date("d/m/y",$sdate).' and '.date("d/m/y",$edate);
	elseif($sdate!=0)
		echo 'starting from '.date("d/m/y",$sdate);
	else
		echo 'ending '.date("d/m/y",$edate);
}
?>
</b>
<?php if(empty($audits)){?>
<h3>No audit available</h3>
<?php }else{?>
<div style="background:#fff;padding:5px;margin:10px;font-size:14px;font-weight:bold;color:blue;">
<div>Income : <?=$income?></div>
<div>Expense : <?=$expense?></div>
</div>
<table cellpadding=5 style="background:#fff;margin:10px;padding:5px;width:100%;">
<tr>
<th>Name</th>
<th>Credit</th>
<th>Debit</th>
<th>User Responsible</th>
<th>Time</th>
</tr>
<?php foreach($audits as $audit){?>
<tr>
<td><?=$audit['name']?> <a href="javascript:void(0)" onclick='$("div",$(this).parent()).show();$(this).hide();'>more</a>
<div style="display:none;padding-left:5px;"><?=$audit['description']?></div>
</td>
<td><?=$audit['credit']?></td>
<td><?=$audit['debit']?></td>
<td><?=$audit['user']?></td>
<td><?=date("g:ia d/m/y",$audit['time'])?></td>
</tr>
<?php }?>
<tr>
<th align="right">Total</th>
<th><?=$income?></th>
<th><?=$expense?></th>
</div>
</tr>
</table>
<?php }?>
</div>
<script>
$(function(){
	$("#sdate,#edate").datepicker({dateFormat:"dd-mm-yy"});
	$("#gofilter").click(function(){
		sdate=$("#sdate").val();
		edate=$("#edate").val();
		if(sdate.length==0 && edate.length==0)
		{
			alert("Please select atleast one date");return false;
		}
		if(sdate.length==0)
			sdate=0;
		if(edate.length==0)
			edate=0;
		location="<?=site_url("admin/audit")?>/"+sdate+"/"+edate;
	});
});
</script>
<?php
