<div class="container">
<h2>Coupon <?=$coupon['code']?></h2>
<a href="<?=site_url("admin/editcoupon/{$coupon['id']}")?>">edit coupon</a>

<?php $c=$coupon;?>

<table cellpadding=7 cellspacing=0 class="datagrid">

<tr>
<th>Coupon</th>
<th>Type</th>
<th>Value</th>
<th>Min Order</th>
<th>on mrp or offer</th>
<th>Unlimited</th>
<th>used</th>
<th>Based on</th>
<th>created on</th>
<th>expires on</th>
<th>Gift Voucher</th>
<th>Remarks</th>
</tr>

<tr>
<td>
<a href="<?=site_url("admin/editcoupon/{$coupon['id']}")?>" class="link" style="display:none;">edit coupon</a>
<a href="<?=site_url("admin/coupon/{$c['code']}")?>"><?=$c['code']?></a></td>
<td><?=$c['type']==0?"value":"percent"?></td>
<td><?=$c['value']?><?=$c['type']==1?"%":""?></td>
<td><?=$c['min']?></td>
<td><?=$c['mode']==1?"mrp":"offer"?></td>
<td><?=$c['unlimited']?"YES":"NO"?></td>
<td><?=$c['used']?></td>
<td>
<?php if($c['brandid']!="") echo "brands"; else if($c['catid']) echo "categories"; else echo "all";?>
</td>
<td><?=date("d/m/y",$c['created'])?></td>
<td><?=date("d/m/y",$c['expires'])?></td>
<td><?=$c['gift_voucher']?"YES":"NO"?></td>
<td><?=$c['remarks']?></td>
</tr>

</table>

<?php if(isset($brands))
{
 $bc=$brands;
 echo '<h3>Brands</h3>';
}
?>
<?php if(isset($cats))
{
 $bc=$cats;
 echo '<h3>Categories</h3>';
}
?>
<?php if(isset($bc)) foreach($bc as $b){?>
<?=$b['name']?>,
<?php }?>

<h4>Used in transactions</h4>
<?php if(empty($used)){?>
<h5>Not used yet</h5>
<?php }else{?>
<table cellpadding=7 cellspacing=0 border=1 class="datagrid">
<tr>
<th>Transaction</th><th>Date</th>
</tr>
<?php foreach($used as $u){?>
<tr>
<td><a href="<?=site_url("admin/trans/{$u['transid']}")?>"><?=$u['transid']?></a></td>
<td><?=date("d/m/y",$u['date'])?></td>
</tr>
<?php }?>
</table>

<?php }?>


<div style="font-size:80%;padding-top:10px;">
<b>Changelog</b>
<table cellpadding=3 cellspacing=0 border=1>
<tr>
<th>Type</th>
<th>Value</th>
<th>Min order</th>
<th>Mrp/offer</th>
<th>Unlimited</th>
<th>Expires</th>
<th>Changed on</th>
</tr>
<?php $acts[]=array('fake'=>true,'type'=>10,"value"=>'252435','min'=>-2132,"mode"=>342,"unlimited"=>-33,"expires"=>-3213,"time"=>-421); foreach($acts as $i=>$c){	if(isset($c['fake']))	continue; ?>
<tr>
<td class="<?=$acts[$i+1]['type']!=$c['type']?"changed":""?>"><?=$c['type']==0?"value":"percent"?></td>
<td class="<?=$acts[$i+1]['value']!=$c['value']?"changed":""?>"><?=$c['value']?><?=$c['type']==1?"%":""?></td>
<td class="<?=$acts[$i+1]['min']!=$c['min']?"changed":""?>"><?=$c['min']?></td>
<td class="<?=$acts[$i+1]['mode']!=$c['mode']?"changed":""?>"><?=$c['mode']==1?"mrp":"offer"?></td>
<td class="<?=$acts[$i+1]['unlimited']!=$c['unlimited']?"changed":""?>"><?=$c['unlimited']?"YES":"NO"?></td>
<td class="<?=$acts[$i+1]['expires']!=$c['expires']?"changed":""?>"><?=date("d/m/y",$c['expires'])?></td>
<td><?=date("g:ia d/m/y",$c['time'])?></td>
</tr>
<?php }?>
</table>
</div>

</div>
<style>
.changed{
background:#ddd;
font-weight:bold;
}
tr:last-child .changed{
background:none !important;
}
</style>
<?php
