<?php
$sus_fran_list = array();
$sus_fran_list_res = $this->db->query('select franchise_id from pnh_m_franchise_info where is_suspended = 1');
if($sus_fran_list_res->num_rows())
{
	foreach($sus_fran_list_res->result_array() as $fr)
	{ 
		$sus_fran_list[] = $fr['franchise_id'];
	}
}
?>

<div class="container">
<?php $prefix=array();
foreach($packed as $o)
	if(!in_array(substr($o['transid'],0,3),$prefix))
		$prefix[]=substr($o['transid'],0,3);
?>


<div style="float:right;margin-right:200px;">
Date range : <input type="text" class="inp" size=12 id="from"> to <input type="text" class="inp" size=12 id="to"> <input type="button" value="Go" onclick='date_range()'> 
</div>

<h2><?=$pagetitle?></h2>

<div class="dash_bar" style="min-width:0px;"><b id="count"><?=count($packed)?></b> items</div>

<div class="dash_bar">Filter :</div>
<?php foreach($prefix as $p){?>
<div class="dash_bar" style="min-width:0px;cursor:pointer;" onclick='filter("<?=$p?>")'><?=$p?></div> 
<?php }?>
<div class="dash_bar" style="min-width:0px;cursor:pointer;" onclick='filter("")'>clear</div>

<div id="fil_byterr_sel" class="dash_bar" style="display: none">
		Territory : 
	<select name="fil_terrid">
		<option value="">All</option>
		
	</select>
</div>

<div class="clear"></div>
<?php
	$terr_list = array();
		
?>



<table class="datagrid datagridsort">
<thead><tr><th>Sno</th><th>Batch</th><th>Transid</th><th>Proforma Invoice</th><th>Invoice</th><th>Invoiced By</th><th width="120">Invoiced on</th><th width="120">Packed on</th><th>By</th><th>Is Shipped?</th><th>Partner ID</th><th>Total Prints</th><th width="120">Invoice PrintedOn</th><th>CourierName</th><th width="120">OutScan Time</th><th>Customer Name</th></tr></thead>
<tbody>
<?php $i=1; foreach($packed as $b){
	$terr_list[$b['territory_id']] = $b['territory_name']; 	
?>
<tr class="oitems o<?=substr($b['transid'],0,3)?> filby_terr tr_<?php echo $b['territory_id'];?>">
<td><?=$i++?></td>
<td><a href="<?=site_url("admin/batch/{$b['batch_id']}")?>" class="link">B<?=$b['batch_id']?></a></td>
<td><a href="<?=site_url("admin/trans/{$b['transid']}")?>"><?=$b['transid']?></a>
	
	<?php
		echo (in_array($b['franchise_id'],$sus_fran_list)?'<br><b style="color:#cd0000;font-size:9px;">Franchise Suspended</b>':'');
	?>
	
</td>
<td><a href="<?=site_url("admin/proforma_invoice/{$b['p_invoice_no']}")?>"><?=$b['p_invoice_no']?></a></td>
<td><a href="<?=site_url("admin/invoice/{$b['invoice_no']}")?>"><?=$b['invoice_no']?></a></td>
<?php if($b['franchise_id'] > 0){ ?>
<td><?=$this->db->query("select username from king_admin where id = ? ",$b['invoiced_by'])->row()->username?></td>
<td><?=format_datetime($b['invoiced_on'])?></td>
<?php }else{
?>
	<td><?=$b['packed_by']?></td>
	<td><?=format_datetime($b['packed_on'])?></td>
<?php 	
} ?>

<td><?=format_datetime($b['packed_on'])?></td>
<td><?=$b['packed_by']?></td>
<td><?=$b['shipped']==1?"YES":"NO"?></td>
<td><?=$b['partner_reference_no']?></td>
<td><?=$b['total_prints']?></td>
<td><?=$b['last_printedon']?format_datetime($b['last_printedon']):'-na-'?></td>
<td><?=$b['partner_courier_name']?></td>
<td><?=$b['o_shipped_on']?format_datetime($b['o_shipped_on']):'-na-'?></td>
<td><?=$b['ship_person'].($b['territory_name']?'<br><b style="font-size:10px;color:#555">'.$b['territory_name'].'</b>':'')?></td>
</tr>
<?php }?>
</tbody>
</table>

</div>

<?php
	asort($terr_list);
?>

<script>

<?php
	foreach($terr_list as $tr_id=>$tr_name)
	{
		if(!$tr_id)
			continue;
?>
		$('select[name="fil_terrid"]').append('<option value="<?php echo $tr_id ?>"><?php echo $tr_name ?> ('+$('.tr_<?php echo $tr_id;?>').length+')</option>');
<?php 			
	}
?>

$('.datagridsort').tablesorter();
$(function(){
	$("#from,#to").datepicker();
});
function date_range()
{
	location="<?=site_url("admin/packed_list")?>/"+$("#from").val()+"/"+$("#to").val();
}

$('select[name="fil_terrid"]').change(function(){
	$(".oPNH").hide();
	if($(this).val())
		$(".tr_"+$(this).val()).show();
	else
		$(".oPNH").show();
});

function filter(prefix)
{
	$('select[name="fil_terrid"]').val("");
	if(prefix == 'PNH')
		$('#fil_byterr_sel').show();
	else
		$('#fil_byterr_sel').hide();
		
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
