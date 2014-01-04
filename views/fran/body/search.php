<div class="container">
<h1>Search Results</h1>
<?php $head=array("Daily deals","Branded deals","Vanilla deals");
?>
<?php if(empty($deals[0]) && empty($deals[1]) && empty($deals[2])){?>
<h2 align="center" style="padding:20px;">No deals found</h2>
<?php }?>
<?php 
for($i=0;$i<3;$i++){
	if(empty($deals[$i]))
		continue;
?>
<h3 style="padding-top:10px;"><?=$head[$i]?></h3>
<table border=1 cellpadding=5 style="background:#fff;font-size:14px;">
<tr>
<th>Deal</th>
<th>Real Price</th>
<th>Your price</th>
<th>Mark Up/Down</th>
<th></th>
</tr>
<?php foreach($deals[$i] as $deal){?>
<tr>
<td><?=$deal['name']?></td>
<td><?=$deal['price']?></td>
<td><?=$deal['price']+$deal['mark']?></td>
<td><?=$deal['mark']?></td>
<td style="font-size:12px;">
<a href="<?=site_url("franchisee/order/$i/".$deal['id'])?>">Order</a>
<?php if($deal['mark']==0){?>
<a href="<?=site_url("franchisee/addmark/$i/".$deal['id'])?>">Add mark up/down</a>
<?php }else{?> 
<a href="<?=site_url("franchisee/addmark/$i/".$deal['id'])?>">Edit mark up/down</a>
<?php }?>
</td>
</tr>
<?php }?>
</table>
<?php }?>
</div>
</div>
<?php
