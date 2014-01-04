<?php $c=false; if(isset($coupon)) $c=$coupon;
$sbrands=array();
$scats=array();
if($c)
{
	$c['brands']=$c['brandid'];
	$c['cats']=$c['catid'];
	$sbrands=explode(",",$c['brands']);
	$scats=explode(",",$c['cats']);
}
?>


<div class="container">


<h3><?=$c?"Edit":"Create"?> Coupon<?=$c?"":"s"?></h3>
<form method="post">
<table width="100%" style="padding:10px;" cellpadding=5>
<?php if(!$c){?>
<tr>
	<td width=150>How many?</td><td width=10>:</td><td><select name="many"><option value="0">only one</option><option value="1">two or more</option></select></td>
</tr>
<tr style="display:none" class="numc">
	<td>Number of coupons</td><td>:</td><td><input type="text" name="num" size=6></td>
</tr>
<tr style="display:none" class="code">
	<td>Coupon code</td><td>:</td><td><input disabled="disabled" type="text" name="code" size=10 maxlength="10"><input type="checkbox" name="autogen" value="yes" checked="checked">auto generate</td>
</tr>
<?php }?>
<tr>	
	<td>Type</td><td></td><td><select name="type"><option value="0" <?=$c&&!$c['type']?"selected":""?>>Value</option><option value="1" <?=$c&&$c['type']?"selected":""?>>Percent</option></select>
</tr>
<tr>
	<td>Value/Percent</td><td>:</td><td><input type="text" name="value" size=4 value="<?=$c?$c['value']:""?>"></td>
</tr>
<tr>
	<td>Discount</td><td>:</td><td><select name="mode"><option value="0" <?=$c&&!$c['mode']?"selected":""?>>Offer price</option><option value="1" <?=$c&&$c['mode']?"selected":""?>>MRP</option></select></td>
</tr>
<tr>
	<td>Expires on EOD of</td><td>:</td><td><input type="text" name="expires" id="datepicker" value="<?=$c?date("d-m-Y",$c['expires']):""?>"></td>
</tr>
<tr>
	<td>Min Bill Amount</td><td>:</td><td><input type="text" size=4 name="min" value="<?=$c?$c['min']:"0"?>"></td>
</tr>
<tr>
	<td>All/Brands/Cats</td>
	<td>:</td>
	<td><select name="brandcats">
	<option value="all" <?=$c&&$c['brands']==""&&$c['cats']==""?"selected":""?>>All</option>
	<option value="brands" <?=$c&&$c['brands']!=""&&$c['cats']==""?"selected":""?>>Brands</option>
	<option value="cats" <?=$c&&$c['brands']==""&&$c['cats']!=""?"selected":""?>>Cats</option>
	</select>
</tr>
<tr class="brandscont" <?php if(!$c||$c['brands']==""){?> style="display:none"<?php }?>>
	<td valign="top">Brands</td><td valign="top">:</td>
	<td>
		<div class="brands" style="max-height:200px;overflow:auto;">
		<table cellpadding=0 cellspacing=0>
		<tr>
		<?php foreach($brands as $i=>$b){?>
		<td>
			<span style="background:#eee;margin:2px 5px;display:inline-block;"><label><input type="checkbox" name="brands[]" value="<?=$b['id']?>" <?=in_array($b['id'],$sbrands)?"checked":""?>><?=$b['name']?></label></span>
		</td>
		<?php if(($i+1)%7==0) echo '</tr><tr>';}?>
		</tr>
		</table>
		</div>
	</td>
</tr>
<tr class="catscont" <?php if(!$c||$c['cats']==""){?> style="display:none"<?php }?>>
	<td valign="top">Categories</td><td valign="top">:</td>
	<td>
		<div class="cats" style="max-height:200px;overflow:auto;">
		<table cellpadding=0 cellspacing=0>
		<tr>
		<?php foreach($cats as $i=>$b){?>
		<td>
			<span style="background:#eee;margin:2px 5px;display:inline-block;"><label><input type="checkbox" name="cats[]" value="<?=$b['id']?>" <?=in_array($b['id'],$scats)?"checked":""?>><?=$b['name']?></label></span>
		</td>
		<?php if(($i+1)%7==0) echo '</tr><tr>'; }?>
		</tr>
		</table>
		</div>
	</td>
</tr>
<tr>
	<td>Unlimited</td><td>:</td>
	<td>
		<select name="unlimited">
			<option value="0" <?=$c&&!$c['unlimited']?"selected":""?>>No</option>
			<option value="1" <?=$c&&$c['unlimited']?"selected":""?>>Yes</option>
		</select>
	</td>
</tr>
<tr>
	<td>Name this activity (remarks)</td><td>:</td><td><input type="text" name="remarks" value="<?=$c?$c['remarks']:""?>"><div style="color:#aaa">something rememberable to be used as reference for searching</div></td>
</tr>
<tr>
<td>Gift Voucher</td><td>:</td>
<td>
	<input type="checkbox" name="gift" value="1">
</td>
<tr>
<td colspan=2></td>
<td><input type="submit" value="Create"></td>
</tr>
</table>
</form>

</div>

<script>
$(function(){
	$("#datepicker").datepicker({ dateFormat: 'dd-mm-yy' });
	$(".numc").hide();
	$(".code").show();

	$("select[name=many]").val("0").change(function(){
		if($(this).val()=="0")
		{
			$(".numc").hide();
			$(".code").show();
		}else
		{
			$(".numc").show();
			$(".code").hide();
		}
	});
	
	$("input[name=code]").attr("disabled",true);

	$("input[name=autogen]").attr("checked",true).click(function(){
		$("input[name=code]").attr("disabled",false);
		if($(this).attr("checked"))
		$("input[name=code]").attr("disabled",true).val("");
	});
	
	$("select[name=brandcats]")<?php if(!$c){?>.val("all")<?php }?>.change(function(){
		v=$(this).val();
		$(".brandscont, .catscont").hide();
		if(v=="brands")
			$(".brandscont").show();
		else if(v=="cats")
			$(".catscont").show();
			
	});
	
	
	
});
</script>
<?php
