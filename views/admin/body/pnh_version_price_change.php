<div class="container">

<h2>Showing consolidated final price changes made after version : <?=$ver['version_no']?></h2>
<div class="dash_bar">
Select Version : 
	<select id="pnh_version">
		<option value="0">select</option>
		<?php foreach($this->db->query("select * from pnh_app_versions order by id asc")->result_array() as $v){?>
			<option value="<?=$v['id']?>"><?=$v['version_no']?></option>
		<?php }?>
	</select> 
</div>

<div class="dash_bar">
<a href="<?=site_url("admin/pnh_version_price_change/{$ver['id']}/1")?>"></a>
Export as CSV</div>

<div class="clear"></div>
<Br>
<table class="datagrid">
<thead><Tr><th>Sno</th><th>Deal Name</th><th>PNH ID</th><th>Old MRP</th><th>New MRP</th><th>Old Offer price</th><th>New Offer price</th><th>Changed on</th></Tr></thead>
<tbody>
<?php $i=1; foreach($deals as $d){?>
<tr><td><?=$i++?></td><td><a href="<?=site_url("admin/pnh_deal/{$d['pnh_id']}")?>"><?=$d['name']?></a></td><td><?=$d['pnh_id']?></td><td><?=$d['old_mrp']?></td><td><?=$d['new_mrp']?></td><td><?=$d['old_price']?></td><td><?=$d['new_price']?></td><td><?=$d['changed_on']?></td></tr>
<?php }?>
</tbody>
</table>

</div>
<script>
$(function(){
	$("#pnh_version").change(function(){
		location="<?=site_url("admin/pnh_version_price_change")?>/"+$(this).val();
	});
});
</script>
<?php
