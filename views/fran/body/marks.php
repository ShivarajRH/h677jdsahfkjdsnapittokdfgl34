<div class="container">
<h1>Mark Up/Down</h1>
<div style="padding:10px;background:#fff;margin:10px;">
<?php if(empty($marks[0]) && empty($marks[1]) && empty($marks[2])){?>
<h2>No mark up/down set</h2>
<?php }?>
<h4>showing only active and available</h4>
<?php 
$head=array("Daily deals","Branded deals","Vanilla deals");
for($i=0;$i<3;$i++){
	if(empty($marks[$i]))
		continue;
?>
<h3 style="padding-top:15px"><?=$head[$i]?></h3>
<table border=1 cellpadding=5 cellspacing=1>
<tr>
<th>Type</th>
<th>For deal</th>
<th>Mark Up/Down</th>
<th>Real price</th>
<th>Your Price</th>
<th></th>
</tr>
<?php foreach($marks[$i] as $m){?>
<tr>
<td><?php if($m['mark']<0) echo "down"; else echo "up";?></td>
<td><?=$m['name']?></td>
<td><?=$m['mark']?></td>
<td><?=$m['price']?></td>
<td><?=($m['price']+$m['mark'])?></td>
<td>
<a href="<?=site_url("franchisee/addmark/$i/".$m['itemid'])?>">Edit</a>  
<a href="<?=site_url("franchisee/delmark/".$m['id'])?>">Delete</a> 
<?php if($i==2){?>
<a href="<?=site_url("store/shop/{$m['url']}")?>">View deal</a>
<?php }else{?>
<a href="<?=site_url("retaildeals/{$m['url1']}/{$m['url2']}")?>">View deal</a>
<?php }?> 
</td>
</tr>
<?php }?>
</table>
<?php }?>
</div>
</div>
<?php
