<div class="container">

<?php $prefix=array();
foreach($orders as $transid=>$o)
	if(!in_array(substr($transid,0,3),$prefix))
		$prefix[]=substr($transid,0,3);
?>



<div style="float:right;margin-right:200px;">
Date range : <input type="text" class="inp" size=12 id="from"> to <input type="text" class="inp" size=12 id="to"> <input type="button" value="Go" onclick='date_range()'> 
</div>

<h2>Order summary between <?=$s?> and <?=$e?></h2>


<div class="dash_bar" style="min-width:0px;"><b id="count"><?=count($orders)?></b> items</div>

<div class="dash_bar">Filter :</div>
<?php foreach($prefix as $p){?>
<div class="dash_bar" style="min-width:0px;cursor:pointer;" onclick='filter("<?=$p?>")'><?=$p?></div> 
<?php }?>
<div class="dash_bar" style="min-width:0px;cursor:pointer;" onclick='filter("")'>clear</div>

<div class="show_for_pnh" style="float: right;display: none;">
	<div align="right">
		Filter by Menu
		<select name="sel_pnh_menu" style="width: 150px;">
			<option value="">All</option>
			<?php foreach($pnh_menu as $pmenu){ ?>
			<option class="sel_menuid_<?php echo $pmenu['id'] ?>" value="<?php echo $pmenu['id'] ?>"><?php echo $pmenu['name']  ?></option>
			<?php } ?>
		</select>
	</div>
	<div align="right">
		Filter by Territory 
		<select name="sel_pnh_terr" style="width: 150px;">
			<option value="">All</option>
			
			<?php foreach($pnh_terr as $pterr){ ?>
			<option class="sel_terrid_<?php echo $pterr['id'] ?>" value="<?php echo $pterr['id'] ?>"><?php echo $pterr['territory_name']  ?></option>
			<?php } ?>
		</select>
	</div>
</div>

<div class="clear"></div>


<table class="datagrid">
<thead>
	<tr>
		<th>Sno</th><th>Transid</th>
		<th class="show_for_pnh" style="display: none;">Franchise Name</th>
		<th>Deal</th>
		<th>Product(s)</th>
		<th>Stocks</th><th>Amount</th><th>Time</th></tr></thead>
<tbody>
<?php $i=1; foreach($orders as $transid=>$ord){ foreach($ord as $o){ $s=true;foreach($deal["{$transid}-{$o['itemid']}"] as $p) if(floor($p['stock'])==0) $s=false;?>
<tr terr_id="<?php echo $o['territory_id'] ?>" terr_name="<?php echo $o['territory_name'] ?>" menu_id="<?php echo $o['menuid'] ?>" class="oitems o<?=substr($transid,0,3)?> <?=($o['franchise_id'])?'pnh_order':'' ?> pnh_m<?echo $o['menuid'] ?> pnh_t<?echo $o['territory_id'] ?>" <?php if(!$s){?>style="background:#ea8;"<?php }?>>
<Td><?=$i++?></Td>
<td><a href="<?=site_url("admin/trans/$transid")?>" class="link"><?=$transid?></a></td>
<td class="show_for_pnh" style="display: none;"><a href="<?=site_url("admin/pnh_franchise/".$o['franchise_id'])?>" class="link"><?=$o['franchise_name']?></a></td>
<td><a href="<?=site_url("admin/deal/{$o['itemid']}")?>"><?=$o['deal']?></a></td>
<td>
<ul style="margin-left:20px;">
<?php foreach($deal["{$transid}-{$o['itemid']}"] as $p){?>
<li><div><a href="<?=site_url("admin/product/{$p['product_id']}")?>"><?=$p['product_name']?></a></div></li>
<?php }?>
</ul>
</td>
<td>
<ul style="margin-left:20px;padding-left:0px;">
<?php foreach($deal["{$transid}-{$o['itemid']}"] as $p){?>
<li><div><?=floor($p['stock'])?> <?php if(floor($p['stock'])==0){?><a href="<?=site_url("admin/vendorsbybrand/{$p['brand_id']}")?>">vendors</a>
<?php }?></div></li>
<?php }?>
</ul>
</td>
<td>Rs <?=$o['amount']?></td>
<td><?=date("g:ia d/m/y",$o['time'])?></td>
</tr>
<?php } }?>
</tbody>
</table>

</div>

<script>
$(function(){
	$("#from,#to").datepicker();
});
function date_range()
{
	location="<?=site_url("admin/order_summary")?>/"+$("#from").val()+"/"+$("#to").val();
}
</script>


<script>
function filter(prefix)
{
	if(prefix.length==0)
		$(".oitems").show();
	else{
		$(".oitems").hide();
		$(".o"+prefix).show();
	}
	$("#count").text($(".oitems:not(:hidden)").length);
	
	if(prefix == 'PNH')
	{
		$('.show_for_pnh').show();
		$('select[name="sel_pnh_menu"]').val("");
		$('select[name="sel_pnh_terr"]').val("");
	}else
	{
		$('.show_for_pnh').hide();
	}
	
}

$('select[name="sel_pnh_menu"] option:gt(0)').hide();
$('select[name="sel_pnh_terr"] option:gt(0)').hide();
$('.pnh_order').each(function(){
	var mid = $(this).attr('menu_id');
		$('.sel_menuid_'+mid).show();
		
	var terr_id = $(this).attr('terr_id');
		$('.sel_terrid_'+terr_id).show();	
});

$('select[name="sel_pnh_menu"]').change(function(){
	$('select[name="sel_pnh_terr"]').val("");
	if($(this).val() == "")
	{
		$(".oPNH").show();
	}else
	{
		$(".oPNH").hide();
		$('.pnh_m'+$(this).val()).show();
	}
	
	$("#count").text($(".oitems:not(:hidden)").length);
});

$('select[name="sel_pnh_terr"]').change(function(){
	$('select[name="sel_pnh_menu"]').val("");
	if($(this).val() == "")
	{
		$(".oPNH").show();
	}else
	{
		$(".oPNH").hide();
		$('.pnh_t'+$(this).val()).show();
	}
	
	$("#count").text($(".oitems:not(:hidden)").length);
});

filter("");

</script>



<?php
