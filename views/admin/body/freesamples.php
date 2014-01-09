<div class="container">

<h2>Free Samples</h2>
<h4>Config <a href="<?=site_url("admin/fsconfig")?>">edit</a></h4>
<?php if(!empty($config)){?>
<table class="datagrid">
<tr>
<th>Order Amount</th>
<th>No of samples allowed</th>
</tr>
<?php foreach($config as $c){?>
<tr>
<td><?=$c['min']?></td><td><?=$c['limit']?></td>
</tr>
<?php }?>
</table>
<?php }else{?>
No config
<?php }?>

<h3>Samples List <a href="<?=site_url("admin/fsedit")?>">new</a></h3>
<table class="datagrid">
<tr>
<th>Image</th><th>Name</th><th>Available</th><th>Min Order</th><th></th>
</tr>
<?php foreach($samples as $s){?>
<tr>
<td><img src="<?=IMAGES_URL?>items/small/<?=$s['pic']?>.jpg"></td>
<td>
<span><?=$s['name']?></span>
<input type="hidden" name="fsid" value="<?=$s['id']?>" class="inp_qe">
<input type="text" name="name" value="<?=$s['name']?>" class="inp inp_qe">
</td>
<td>
	<span><?=$s['available']?"YES":"NO"?></span>
	<select class="inp_qe" name="available">
		<option <?=$s['available']?"selected":""?> value="1">YES</option>
		<option <?=!$s['available']?"selected":""?> value="0">NO</option>	
	</select>
</td>
<td>
<span><?=$s['min']?></span>
<input type="text" class="inp_qe" name="min" value="<?=$s['min']?>" style="width:60px;">
</td>
<td>
<span>
<a href="javascript:void(0)" class="qe_trig" style="display:none;"></a>
<a href="<?=site_url("admin/fsedit/{$s['id']}")?>">edit</a>
</span>
<input type="button" id="fs_qe_submit" class="inp_qe qe_submit"  value="save">
</td>
</tr>
<?php }?>
</table>
</div>
<script>
function fs_qe_submit(obj,row)
{
	$.post("<?=site_url("admin/fsedit")?>/"+obj.fsid,obj,function(){
		qe_callback_done(row);
	});
}
</script>


<?php
