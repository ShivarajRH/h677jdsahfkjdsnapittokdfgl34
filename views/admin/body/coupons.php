<div class="container">

<div style="float:right">
Search: <input type="text" id="srch_coupon"><input onclick='gocoup()' type="button" value="Go">
</div>
<script>
function gocoup()
{
	if($("#srch_coupon").val().length==0)
		alert("enter coupon code!!");
	else
		location="<?=site_url("admin/coupon")?>/"+$("#srch_coupon").val();
}
</script>
<h1>Coupons
<div style="font-size:50%;"><a href="<?=site_url("admin/createcoupons")?>">create coupons</a></div>
</h1>

<h3><?=$title?></h3>

<a href="javascript:void(0)" onclick='$("#refcoupons").show()'>View Coupons</a>
<div id="refcoupons" style="display:none;">

<form method="post">
<table cellpadding=5 style="background:#eee;" class="datagrid">

<tr>
<td>Reference</td><td>
<select name="ref">
<option value="any">any</option>
<?php foreach($refs as $r){?>
<option value="<?=$r?>"><?=$r?></option>
<?php }?>
</select>
</td>
</tr>

<tr>
<td>Status</td><td><select name="status">
<?php foreach(array("any","not used","used") as $v=>$a){?>
<option value="<?=$v?>"><?=$a?></option>
<?php }?>
</select></td>
</tr>

<tr>
<td>Type</td><td><select name="type">
<?php foreach(array("any","value","percent") as $v=>$a){?>
<option value="<?=$v?>"><?=$a?></option>
<?php }?>
</select></td>
</tr>

<tr>
<td>MRP or offer</td><td><select name="mode">
<?php foreach(array("any","offer","mrp") as $v=>$a){?>
<option value="<?=$v?>"><?=$a?></option>
<?php }?>
</select></td>
</tr>

<tr>
<td>Unlimited</td><td><select name="unlimited">
<?php foreach(array("any","no","yes") as $v=>$a){?>
<option value="<?=$v?>"><?=$a?></option>
<?php }?>
</select></td>
</tr>


<tr>
<td></td>
<td><input type="submit" value="find"></td>
</tr>

</table>
</form>

</div>

<div style="padding:10px;">

<h4><?=$found?> Found
<?php if($found>count($coupons)){?>
<br>but showing only <?=count($coupons)?>
<?php }?>
</h4>

<?php if($_POST){?>
<form action="<?=site_url("admin/coupon_usage_history")?>" method="post">
<?php foreach($_POST as $n=>$v){?>
<input type="hidden" name="<?=$n?>" value="<?=$v?>">
<?php }?>
<a href="javascript:void(0)" onclick='$(this).parent().submit()'>generate usage history</a>
</form>
<?php } ?>
<table width="100%" cellspacing=0 class="datagrid">

<tr>
<th>Coupon</th>
<th>Type</th>
<th>Value</th>
<th>Min Order</th>
<th>on mrp or offer</th>
<th>used</th>
<th>Based on</th>
<th>created on</th>
<th>expires on</th>
<th>Gift Voucher?</th>
<th>Remarks</th>
</tr>

<?php foreach($coupons as $c){?>
<tr>
<td><a class="link" href="<?=site_url("admin/coupon/{$c['code']}")?>"><?=$c['code']?></a></td>
<td><?=$c['type']==0?"value":"percent"?></td>
<td><?=$c['value']?><?=$c['type']==1?"%":""?></td>
<td><?=$c['min']?></td>
<td><?=$c['mode']==1?"mrp":"offer"?></td>
<td><?=$c['used']?></td>
<td>
<?php if($c['brandid']!="") echo "brands"; else if($c['catid']) echo "categories"; else echo "all";?>
</td>
<td><?=date("d/m/y",$c['created'])?></td>
<td><?=date("d/m/y",$c['expires'])?></td>
<td><?=$c['gift_voucher']?"YES":"NO"?></td>
<td><?=$c['remarks']?></td>
</tr>
<?php }?>

</table>

</div>

</div>
<?php
