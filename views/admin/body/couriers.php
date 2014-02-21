<style type="text/css">
h2 {    width: 60%;    float: left; }
.filter_used_for {float:right;margin-top: 15px;}
.datagrid td { padding: 7px; }
.datagrid th { background: #443266;color: #C3C3E5; }
</style>
<div class="container">
<h2>Couriers</h2>
<a href="<?=site_url("admin/add_courier")?>">Add new courier</a>
<table class="datagrid" width="100%">
<thead>
    <tr><th>Id</th><th>Courier Name</th><th>Used for</th><th>Enabled for pincodes</th><th>Edit pincodes</th><th>Current AWB</th><th>Remaining AWBs</th><th>Update AWB series</th><th>Actions</th></tr>
</thead>
<tbody>
<?php foreach($couriers as $c){?>
<tr>
<td><?=$c['courier_id']?></td>
<td><?=$c['courier_name']?></td>
<?php
    if($c['is_active']==1)  
            $used_for = "PNH";
    elseif($c['ref_partner_id']== 0) {
        $used_for = "SIT";
    }
    elseif(in_array($c['ref_partner_id'],range(1,6)) ) {
        $used_for = $c['trans_prefix'];
    }
   ?>

<td><?php
        echo $used_for;
    ?></td>
<td><?=$c['pincodes']?></td>
<td><a href="<?=site_url("admin/edit_pincodes/{$c['courier_id']}")?>">view/edit pincodes</a></td>
<td><?=$c['awb']?></td>
<td><?=$c['rem_awb']?></td>
<td><a href="<?=site_url("admin/update_awb/{$c['courier_id']}")?>">Update AWB</a></td>
<td><a href="<?=site_url("admin/courier_edit/{$c['courier_id']}")?>">Edit</a></td>
</tr>
<?php }?>
</tbody>
</table>
</div>
<?php
