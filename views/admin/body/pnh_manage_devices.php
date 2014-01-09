<div class="container">
<a href="<?php echo site_url("admin/pnh_franchise/$fid")?>"style="font-size: 18px;color:red"><?=$this->db->query("select franchise_name from pnh_m_franchise_info where franchise_id=?",$fid)->row()->franchise_name?></h2><h1 style="font-size: 11px;color:black;">(back)</h1></a>
<h2>Manage devices for <?=$this->db->query("select franchise_name from pnh_m_franchise_info where franchise_id=?",$fid)->row()->franchise_name?></h2>

<h4 style="margin-bottom:0px;">Allotted devices</h4>
<table class="datagrid smallheader">
<thead><tr><th>Device Sno</th><th>Type</th><th></th></tr></thead>
<tbody>
<?php foreach($devs as $d){?>
<tr>
<td><?=$d['device_sl_no']?></td>
<td><?=$d['device_name']?></td>
<td><a href="<?=site_url("admin/pnh_removefdevice/{$d['id']}/$fid")?>" class="danger_link">remove</a>
</tr>
<?php }?>
</tbody> 
</table>

<br><br>

<h4 style="margin-bottom:0px;">Allot new devices</h4>
<form method="post">
<table>
<thead><tr><th>Device Sno</th><th>Device Type</th></tr></thead>
<tbody>
<?php
$types=$this->db->query("select * from pnh_m_device_type order by device_name asc")->result_array();
for($i=0;$i<6;$i++){?>
<tr>
<td><input type="text" name="dsno[]" class="inp"></td>
<td><select name="dtype[]">
<?php foreach($types as $t){?>
<option value="<?=$t['id']?>"><?=$t['device_name']?></option>
<?php }?>
</select></td>
</tr>
<?php }?>
</tbody>
</table>
<input type="submit" value="Add device">
</form>

</div>
<?php
