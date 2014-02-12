<style type="text/css">
h2 {    width: 60%;    float: left; }
.filter_used_for {float:right;margin-top: 15px;}
.datagrid td { padding: 7px; }
.datagrid th { background: #443266;color: #C3C3E5; }
.hide { display: none; }
.show { display: block; }
</style>
<div class="container">
<h2>Couriers</h2>
<div class="filter_used_for">
    <select id="sel_used_for" name="sel_used_for"  style="margin-right:20px;">
        <option value="00">Choose for</option>
        <?php  foreach($couriers_used_for as $used_for): 
             /* //if(isset($used_for['territory_id']) and $used_for['territory_id'] == $terr['id']) {?>
                <option value="<?php echo $terr['id'];?>" selected><?php echo $terr['territory_name'];?></option>
            }else {//}*/?>
                <option value="<?php echo $used_for['trans_prefix'];?>"><?php echo $used_for['used_for'];?></option>
        <?php endforeach;  ?>
    </select>
    <a align="right" href="<?=site_url("admin/add_courier")?>">Add new courier</a>
</div>
<table class="datagrid datagridsort" width="100%">
<thead>
    <tr><th>Id</th><th>Courier Name</th><th>Used for</th><th>Enabled for pincodes</th><th>Edit pincodes</th><th>Current AWB</th><th>Remaining AWBs</th><th>Update AWB series</th><th>Actions</th></tr>
</thead>
<tbody class="row_container">
<?php foreach($couriers as $c){?>
<tr class="<?=$c['trans_prefix']?>">
<td><?=$c['courier_id']?></td>
<td><?=$c['courier_name']?></td>
<td><?=$c['used_for']?></td>

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
<script>
$("#sel_used_for").change(function() {
    
    var sel_used_for = $(this).val();
    if(sel_used_for == '00') {
        $(".row_container tr").show();
    }
    else {
        $(".row_container tr").hide();
        $(".row_container tr."+sel_used_for).show();
    }
});
$(".datagridsort").tablesorter({sortList:[[0,0]]});
</script>
<?php
