<?php $item=$itemdetails; ?>
<div class="item">
<?php /*	<div class="head">Today's Deal</div>*/?>
	<div style="background:#ccc;padding:3px;">
	<div class="itemname"><?=$item['name']?></div>
	</div>
	
	<div class="itempic">
		<img src="<?=base_url()?>images/items/<?=$item['pic']?>.jpg" style="width:100%">
	</div>
	
	<div align="right" style="cursor:pointer;background:#65A100;padding:5px;margin-top:5px;" onclick='$("form",$(this)).submit()'>
		<form action="<?=site_url("jx/addtocart/mob")?>" method="post"><input type="hidden" name="item" value="<?=$item['id']?>"><input type="hidden" name="qty" value="1">
		<input type="image" src="<?=base_url()?>images/buynow_m.png">
		</form>
	</div>
	
	<div style="background:#024769;font-size:110%;margin:3px 0px;padding:3px;color:#fff;">
		<table width="100%">
		<tr>
		<td>Snap it today @ </td>
		<td align="right"><span style="font-size:140%;padding-left:5px;font-weight:bold;">Rs <?=$item['price']?></span></td>
		</tr>
		<tr>
		<td>
		<span style="color:#DF8039;text-decoration:line-through;">Rs <?=($item['orgprice'])?></span>
		</td>
		<td>
		<div align="right" style="font-weight:bold;color:#DF8039;">(Save <?=ceil(($item['orgprice']-$item['price'])/$item['orgprice']*100)?>%)</div>
		</td>
		</tr>
		</table>
	</div>

	<div class="itempic">
		<div><?=$item['description1']?></div>
		<div style="padding:5px;"><a href="javascript:void(0);" onclick='$(".desc").toggle();'>more</a></div>
		<div class="desc" style="display:none;"><?=$item['description2']?></div>
	</div>

	<div class="desc" style="display:none;background:#fff;margin-top:5px;PADDING:3PX;">

	<div style="background:#024769;font-size:110%;margin:3px 0px;padding:3px;color:#fff;">
		<table width="100%">
		<tr>
		<td>Snap it today @ </td>
		<td align="right"><span style="font-size:140%;padding-left:5px;font-weight:bold;">Rs <?=$item['price']?></span></td>
		</tr>
		<tr>
		<td>
		<span style="color:#DF8039;text-decoration:line-through;">Rs <?=($item['orgprice'])?></span>
		</td>
		<td>
		<div align="right" style="font-weight:bold;color:#DF8039;">(Save <?=ceil(($item['orgprice']-$item['price'])/$item['orgprice']*100)?>%)</div>
		</td>
		</tr>
		</table>
	</div>

	<div align="right" style="cursor:pointer;background:#65A100;padding:5px;margin-top:5px;" onclick='$("form",$(this)).submit()'>
		<form action="<?=site_url("jx/addtocart/mob")?>" method="post"><input type="hidden" name="item" value="<?=$item['id']?>">
		<input type="hidden" name="qty" value="1">
		<input type="image" src="<?=base_url()?>images/buynow_m.png" style="cursor:pointer">
		</form>
	</div>

	</div>

	
</div>
