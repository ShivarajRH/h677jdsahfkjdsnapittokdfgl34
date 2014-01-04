<?php $item=$itemdetails; ?>
<div class="item">
	<div class="itemname"><?=$item['name']?></div>
	<div class="brandname">from <?=$item['brandname']?></div>
	
	
	<div class="itempic">
		<img src="<?=base_url()?>images/items/<?=$item['pic']?>.jpg" style="width:100%">
	</div>

	<div style="background:#fff;margin-top:5px;PADDING:3PX;">
	<table>
	<tr>
	<td>
	<table>
		<tr style="font-size:120%;"><td>Offer Price</td><td><b style="color:#F7DF00;">Rs <?=$item['price']?></b></td></tr>
		<tr><td>Save</td><td><b>Rs. <?=number_format($item['orgprice']-$item['price'])?></b></td></tr>
		<tr><td>Discount</td><td class="price"><?=ceil(($item['orgprice']-$item['price'])/$item['orgprice']*100)?>%</td></tr>
	</table>
	</td>
	<td>
	<input class="buybut" type="button" value="Sold Out">
	</td></tr></table>
	</div>
	
	<div class="itempic">
		<div><?=$item['description1']?></div>
		<div style="padding:5px;"><a href="javascript:void(0)" onclick='$(".desc").toggle();'>more</a></div>
		<div class="desc" style="display:none;"><?=$item['description2']?></div>
	</div>

	<div class="desc" style="display:none;background:#fff;margin-top:5px;PADDING:3PX;">
	<table>
	<tr>
	<td>
	<table>
		<tr style="font-size:120%;"><td>Offer Price</td><td><b style="color:#F7DF00;">Rs <?=$item['price']?></b></td></tr>
		<tr><td>Save</td><td><b>Rs. <?=number_format($item['orgprice']-$item['price'])?></b></td></tr>
		<tr><td>Discount</td><td class="price"><?=ceil(($item['orgprice']-$item['price'])/$item['orgprice']*100)?>%</td></tr>
	</table>
	</td>
	<td>
	<input class="buybut" type="submit" value="Sold Out">
	</td></tr></table>
	</div>

</div>