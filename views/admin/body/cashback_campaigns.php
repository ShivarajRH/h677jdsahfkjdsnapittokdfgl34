<div class="container">
<h2>Cashback Campaigns</h2>
<a href="<?=site_url("admin/new_cashback")?>">create new campaign</a>

<?php
$heads=array("Active","About to start","Expired","Pulled Off");
$emptys=array("No active cashback campaigns running","No pending campaigns","No expired cashback campaigns","No disabled cashback campaigns");
 foreach($camps_raw as $i=>$camps){?>
<h3><?=$heads[$i]?></h3>
<?php if(empty($camps)){?>
	<h4><?=$emptys[$i]?></h4>
<?php }else{?>
<table cellpadding=5 cellspacing="0" border=1 class="datagrid">
<tr>
<th>Cashback %</th>
<th>Min Transaction Amt</th>
<th>Number of coupons</th>
<th>starts on</th>
<th>Expires on</th>
<th>Coupon Validity</th>
<th>Coupon Min Order</th>
</tr>
<?php foreach($camps  as $a){?>
<tr>
<td><?=$a['cashback']?> %</td>
<td><?=$a['min_trans_amount']?></td>
<td><?=$a['coupons_num']?></td>
<td><?=date("g:ia d/m/y",$a['starts'])?></td>
<td><?=date("g:ia d/m/y",$a['expires'])?></td>
<td><?=$a['coupon_valid']?></td>
<td><?=$a['coupon_min_order']?></td>
<?php if($i!=2){?>
<td><a href="<?=site_url("admin/togglecashbackstatus/{$a['id']}/{$a['status']}")?>"><?=$a['status']?"dis":"en"?>able</a></td>
<?php }?>
</tr>
<?php }?>
</table>
<?php }?>
<?php }?>

</div>

<?php
