<div class="container">
<h2>Rackbins</h2>
<a href="javascript:void(0)" onclick='$("#rackbin_add").show()'>Add New</a>

<form action="<?=site_url("admin/addrackbin")?>" method="post">
<table id="rackbin_add">
<tr>
<td>Location Name :</td>
<td><select name="loc">
<?php foreach($locs as $l){?>
<option value="<?=$l['location_id']?>"><?=$l['location_name']?></option>
<?php }?>
</select></td>
</tr>
<tr>
<td>Rack Name :</td>
<td><input type="text" class="inp" name="rack" value=""></td>
</tr>
<tr>
<td>Bin Name :</td>
<td><input type="text" class="inp" name="bin" value=""></td>
</tr>
<tr>
<td></td><td><input type="submit" value="Add rackbin"></td>
</tr>
</table>
</form>

<table class="datagrid" style="width:400px;">
<thead>
<tr>
<th>Rack</th><th>Bin</th><th>Code</th>
</tr>
</thead>
<tbody>
<?php foreach($rackbins as $r) {?>
<tr>
<td>
<span><?=$r['rack_name']?></span>
<input type="hidden" class="inp_qe" name="id" value="<?=$r['id']?>">
<input type="text" class="inp inp_qe" name="rack" value="<?=$r['rack_name']?>">
</td>
<td>
<span><?=$r['bin_name']?></span>
<input type="text" class="inp inp_qe" name="bin" value="<?=$r['bin_name']?>">
</td>
<td>
<span><?=$r['rack_name']?><?=$r['bin_name']?></span>
<input type="button" class="inp_qe qe_trig qe_submit" id="rackbin_qe" value="save">
</td>
</tr>
<?php }?>
</tbody>
</table>
</div>
<style>
#rackbin_add{
display:none;
margin:20px;
padding:5px;
border:1px solid #ccc;
}
</style>
<script>
function rackbin_qe(inp,row)
{
	$.post("<?=site_url("admin/addrackbin")?>",inp,function(){
		qe_callback_done(row);
		location="<?=site_url("admin/rackbins")?>";
	});
}
</script>
<?php
