<div class="container">
<?php $prefix=array();
foreach($outscans as $o)
	if(!in_array(substr($o['transid'],0,3),$prefix))
		$prefix[]=substr($o['transid'],0,3);
?>



<div style="float:right;margin-right:200px;">
Date range : <input type="text" class="inp" size=12 id="from"> to <input type="text" class="inp" size=12 id="to"> <input type="button" value="Go" onclick='date_range()'> 
</div>


<h2><?=$pagetitle?></h2>

<div class="dash_bar" style="min-width:0px;"><b id="count"><?=count($outscans)?></b> items</div>


<div class="dash_bar">Filter :</div>
<?php foreach($prefix as $p){?>
<div class="dash_bar" style="min-width:0px;cursor:pointer;" onclick='filter("<?=$p?>")'><?=$p?></div> 
<?php }?>
<div class="dash_bar" style="min-width:0px;cursor:pointer;" onclick='filter("")'>clear</div>

<div class="clear"></div>

<table class="datagrid">
<thead><tr><th>Sno</th><th>Batch</th><th>Transid</th><th>Proforma Invoice</th><th>Invoice</th><th>Outscanned on</th><th>By</th></tr></thead>
<tbody>
<?php $i=1; foreach($outscans as $b){?>
<tr class="oitems o<?=substr($b['transid'],0,3)?>">
<td><?=$i++?></td>
<td><a href="<?=site_url("admin/batch/{$b['batch_id']}")?>" class="link">B<?=$b['batch_id']?></a></td>
<td><a href="<?=site_url("admin/trans/{$b['transid']}")?>"><?=$b['transid']?></a></td>
<td><a href="<?=site_url("admin/proforma_invoice/{$b['p_invoice_no']}")?>"><?=$b['p_invoice_no']?></a></td>
<td><a href="<?=site_url("admin/invoice/{$b['invoice_no']}")?>"><?=$b['invoice_no']?></a></td>
<td><?=date("g:ia d/m/y",strtotime($b['shipped_on']))?></td>
<td><?=$b['shipped_by']?></td>
</tr>
<?php }?>
</tbody>
</table>

</div>


<script>
$(function(){
	$("#from,#to").datepicker();
});
function date_range()
{
	location="<?=site_url("admin/outscan_list")?>/"+$("#from").val()+"/"+$("#to").val();
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
}
</script>

<?php
