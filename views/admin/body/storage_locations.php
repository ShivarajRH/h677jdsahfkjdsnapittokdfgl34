<div class="container">
<h2>Storage Locations</h2>
<a href="<?=site_url("admin/add_storage_loc")?>">add new storage location</a>

<table class="datagrid" style="margin-top:10px;">
<thead>
<tr><th>LOcation name</th><th>Is damaged</th><th>Created ON</th></tr>
</thead>
<?php foreach($locs as $l){?>
<tr>
<td><?=$l['location_name']?></td>
<td><?=$l['is_damaged']?"YES":"NO"?></td>
<td><?=$l['created_on']?></td>
</tr>
<?php }?>
</table>

</div>
<?php
