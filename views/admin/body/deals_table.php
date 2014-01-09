<div class="container">

<div class="dash_bar_right">
Brand : <select id="brand">
<option value="0">select</option>
<?php foreach($this->db->query("select id,name from king_brands order by name asc")->result_array() as $b){?>
<option value="<?=$b['id']?>"><?=$b['name']?></option>
<?php }?>
</select>
</div>

<div class="dash_bar_right">
Category : <select id="category">
<option value="0">select</option>
<?php foreach($this->db->query("select id,name from king_categories order by name asc")->result_array() as $b){?>
<option value="<?=$b['id']?>"><?=$b['name']?></option>
<?php }?>
</select>
</div>

<div class="dash_bar_right">
Menu : <select id="menu">
<option value="0">select</option>
<?php foreach($this->db->query("select id,name from king_menu order by name asc")->result_array() as $b){?>
<option value="<?=$b['id']?>"><?=$b['name']?></option>
<?php }?>
</select>
</div>

<h2>Deals <?=isset($pagetitle)?" for $pagetitle":""?></h2>

<div class="clear"></div>


<div style="float:right">
<span><img src="<?=IMAGES_URL?>no_photo.png" style="float:left;margin-top:-2px;"> -No image</span> &nbsp; &nbsp; &nbsp;
<span style="background:#f55;height:15px;width:15px;display:inline-block;">&nbsp;</span>-Not Published & out-of-stock &nbsp; &nbsp; &nbsp;
<span style="background:#faa;height:15px;width:15px;display:inline-block;">&nbsp;</span>-Not Published & In-stock &nbsp; &nbsp; &nbsp;
<span style="background:#afa;height:15px;width:15px;display:inline-block;">&nbsp;</span>-Published & out-of-Stock &nbsp; &nbsp; &nbsp;
<span style="background:#1e1;height:15px;width:15px;display:inline-block;">&nbsp;</span>-Published & In-Stock &nbsp; &nbsp; &nbsp;
</div>

<div class="dash_bar">
<span><?=count($deals)?></span> deals found
</div>


<div class="clear"></div>

<?php $color=array("p0l0"=>"#f55","p0l1"=>"#faa","p1l0"=>"#afa","p1l1"=>"#1e1");?>

<table class="datagrid">
<thead><tr><th>Sno</th><th><input type="checkbox" class="sel_all"></th><th>Name</th><th>MRP</th><th>Price</th><th>Brand</th><th>Category</th><th>Menu1</th><th>Menu2</th><th>Status</th><th></th></tr></thead>
<tbody>
<?php $i=1; foreach($deals as $d){?>
<tr style="background:<?=$color["p".$d['publish']."l".$d['live']]?>">
<td><?=$i++?></td>
<td><input type="checkbox" class="sel" value="<?=$d['id']?>"></td>
<td><?=$d['name']?><?php if(empty($d['pic'])){?> <img style="float:right;" src="<?=IMAGES_URL?>no_photo.png"><?php }?></td>
<td><?=$d['mrp']?></td>
<td><?=$d['price']?></td>
<td><?=$d['brand']?></td>
<td><?=$d['category']?></td>
<td><?=$d['menu1']?></td>
<td><?=$d['menu2']?></td>
<td>
<?=$d['publish']?"Published":"Unpublished"?>, 
<?=$d['live']?"In-Stock":"Out of Stock"?>
</td>
<td>
	<a href="<?=site_url("admin/edit/{$d['dealid']}")?>">edit</a>&nbsp;&nbsp;&nbsp; 
	<a href="<?=site_url("admin/deal/{$d['dealid']}")?>">view</a>
</td>
</tr>
<?php }?>
</tbody>
</table>
<div>
With selected : <input type="button" value="Publish" onclick="endisable_sel('1')"> <input type="button" value="Unpublish" onclick='endisable_sel("0")'>
</div>
</div>


<form id="endisable_form" method="post" action="<?=site_url("admin/pnh_pub_unpub_deals")?>">
<input type="hidden" name="action" id="endis_act">
<input type="hidden" name="itemids" id="endis_ids">
</form>



<script>



function endisable_sel(act)
{
	var ids=[];
	$(".sel:checked").each(function(){
		ids.push($(this).val());
	});
	ids=ids.join(",");
	$("#endis_act").val(act);
	$("#endis_ids").val(ids);
	$("#endisable_form").submit();
}



$(function(){

	$(".sel_all").click(function(){
		if($(".sel_all").attr("checked"))
			$(".sel").attr("checked",true);
		else
			$(".sel").attr("checked",false);
	});
	$(".sel_all, .sel").attr("checked",false);

	
	$("#menu").change(function(){
		location="<?=site_url("admin/dealsbymenu_table")?>/"+$(this).val();
	}).val(0);
	$("#brand").change(function(){
		location="<?=site_url("admin/dealsbybrand_table")?>/"+$(this).val();
	}).val(0);;
	$("#category").change(function(){
		location="<?=site_url("admin/dealsbycategory_table")?>/"+$(this).val();
	}).val(0);
});
</script>

<?php
