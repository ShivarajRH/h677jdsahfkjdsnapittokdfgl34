Hermione reporting status:
<table cellpadding=10>
<tr>
<?php 
$j=0;
foreach($valid as $i=>$v){
	$count=0;
	$cron=false;
	foreach($counts as $c)
		if($c['cron']==$i)
		{
			$cron=$c;
			$count=$c['count'];
			break;
		}
	$perc=$count/$v*100;
	if($perc>=98)
		$status="<div style='color:blue;'>GOOD</div>";
	elseif($perc>85)
		$status="<div style='color:#ff9900;'>NORMAL</div>";
	elseif($perc>70)
		$status="<div style='color:yellow;'>OK</div>";
	elseif($perc>48)
		$status="<div style='color:red;'>BAD</div>";
	elseif($perc>20)
		$status="<div style='color:red;'>CRITICAL</div>";
	else 
		$status="<div style='color:red;text-decoration:underline;'>SEVERE!</div>";
?>
<td>
<div style="padding:10px 0px;">
<h4 style="margin:0px;text-decoration:underline;"><?=$names[$i]?></h4>
<div>
	<div style="padding:7px 30px;"><b><?=$status?></b></div>
	<div style="padding:0px;">Out of <b><?=$v?></b>, successful runs : <b><?=$count?></b>
	<?php if($cron && $cron['start']>$cron['count']){?>
	<br><span style="color:red"><b><?=$cron['start']-$cron['count']?></b> ended with no success</span>
	<?php }?>
	</div>
</div>
</div>
</td>
<?php $j++; if($j%3==0) echo '</tr><tr>'; }?>
</tr>
</table>
<div style="padding-top:30px;color:#777;font-size:13px;">
Neo is reporting healthy & fine!<br>
Morpheus is reporting from Nabonidus!<br>
Agent Smith lost in phone call!!
</div>
<div style="color:#999;" align="right">
 -- Cron log -- <?=date("r")?>
</div>