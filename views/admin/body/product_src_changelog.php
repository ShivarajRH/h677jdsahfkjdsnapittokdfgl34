<div class="container">


<div class="dash_bar_right" style="padding:7px;">
Date range: <input type="texT" size="8" class="inp" id="ds_range" value="<?=$this->uri->segment(3)?>"> to <input size="8" type="text" class="inp"id="de_range" value="<?=$this->uri->segment(4)?>"> <input type="button" value="Show" onclick='showrange()'>
</div>

<h2>Products Sourceable changelog between <?=date("d/m/y",$s)?> and <?=date("d/m/y",$e)?></h2>

<table class="datagrid">
<thead><tr><th>Sno</th><th>Product</th><th>Sourceable</th><th>Updated By</th><th>Logged On</th></tr></thead>
<tbody>
<?php $i=1; foreach($prods as $p){?>
<tr>
<td><?=$i++?></td>
<td><a href="<?=site_url("admin/product/{$p['product_id']}")?>" target="_blank"><?=$p['product_name']?></a> <br/>
	<b style="font-size: 10px;"><?php echo $p['cur_src_status']?'Sourceable':'Not Sourceable';?></b>
</td>
<td><?=$p['is_sourceable']?'Yes':'No'?></td>
<td><?=$p['created_by']?></td>
<td><?=date("d/m/Y g:ia",$p['created_on'])?></td>
</tr>
<?php }?>
</tbody>
</table>

</div>

<script>

function showrange()
{
	location="<?=site_url("admin/product_src_changelog")?>/"+$("#ds_range").val()+"/"+$("#de_range").val();
}

$(function(){
	$("#ds_range,#de_range").datepicker();
});
</script>

<?php
