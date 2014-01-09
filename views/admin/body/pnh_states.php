
<div class="container">
<h2>States</h2>
<?php 
	$id = $this->uri->segment(3);
	$i=1;
?>

<table class="datagrid">
	<thead>
		<tr>
			<th>Sl.No</th>
			<th>State Name</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($states as $s) { ?>
		<tr>
			<td><?=$i++?></td>
			<td><?=$s['state_name']?></td>
			<td><a href="<?=site_url("admin/pnh_state_analytics/{$s['state_id']}")?>">analytics</a></td>
		</tr>
		<?php } ?>
	</tbody>
</table>

</div>
<?php
