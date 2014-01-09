<?php 
$user=$this->session->userdata ( "admin_user" );
?>
<script src="<?=base_url()?>js/jquery.tablesorter.js"></script>
<script>
$(function(){

	$(".viewmore").click(function(){
		$("select",$(this).parent()).show();
	});

$("#tsort").tablesorter({
	headers:{
	2:{sorter:false}}
});

$('.showeditadmin').click(function(){	
	i=$(this).attr("rel");
	$('#brandadminname'+i).hide();
$('#brandadminnameedit'+i).show();});

$("#catsel").change(function(){
	obj=$(this);
	if(obj.val()==0)
		return;
	location.href="<?=site_url("admin/brandsforcategory")?>/"+obj.val();
});

$('.submiteditadmin').click(function(){
	a=$(this).attr("alt");
	$('#formadmin_'+a).submit();});
});
function submiteditadmin(i)
{
	alert(i);
	//$('#formadmin_'+i).submit();
}
</script>
<style>
.container{
font-family:arial;
}
table{
width:80%;
}
tr td{
width:auto;
height:20px;
font-family: arial;
font-size: 11px;
}
label{
font-family: arial;
font-size: 11px;
color:black;
}
</style>
<style>
#viewagent table td{
-moz-background-clip:border;
-moz-background-inline-policy:continuous;
-moz-background-origin:padding;
color:#222;
padding:8px;

}

#viewagent table th{
-moz-background-clip:border;
-moz-background-inline-policy:continuous;
-moz-background-origin:padding;
border-bottom:1px solid #FFFFFF;
#border-top:2px solid #D3DDFF;
color:#fff;
font-size:13px;
font-weight:normal;
padding:8px;
font-weight: bold;
font-family:arial;
text-align:left;
}
#viewagent td{
font-weight:bold;
font-size:12px;
font-family:arial;
border-bottom:1px solid #fff;
text-align:left;
}

#viewagent table tr:hover td {
-moz-background-clip:border;
-moz-background-inline-policy:continuous;
-moz-background-origin:padding;
background:#DFDCD1;
font-family:arial;
}

#viewagent table{
width: 600px;

}
#viewagent table td a{
color:#00f;
text-decoration:none;
font-weight:normal;
font-size:11px;
}
#viewagent table td a:hover{
text-decoration:underline;
}
div.nextprev{
color:#00f;
margin-right: 75px;
margin-bottom:0px;
padding: 5px;
font-family: arial;
font-weight:bold;
font-size: 11px;
}
div.nextprev img{
margin-bottom:-4px;
}
</style>
<script>
$(function(){
$(".confirmdel").click(confirmdelete);
});
function confirmdelete()
{
	if(confirm("Are you sure want to delete the Admin?")==true)
		return true;
	else
		return false;
}
</script>
<div class="heading" style="margin-bottom:0px;margin-top: 40px;">
<div class="headingtext container">
<?php if($user['usertype']==1){?>
<div style="float:right;"><a style="font-size:14px;color:blue;" href="<?=site_url("admin/addbrand")?>">Add brand</a></div>
<?php }?>
Brands <?php if(isset($curcategory)){?> under <?=$curcategory->name?><?php }?></div>
</div>
<div class="container" align="center" style="padding-top:5px;">
<div class="sidepane">
<div style="font-size:15px;">View brands by category</div>
<?php $ic=0;
foreach($categories as $category){
?>
<a style="margin:0px 5px;font-size:13px;" href="<?=site_url("admin/brandsforcategory/{$category->id}")?>"><?=$category->name?></a>
<?php $ic++;if($ic==10) break;}?>
<?php if(count($categories)>10){?>
<div align="center"> 
<a href="javascript:void(0)" class="viewmore" style="font-size:13px;float:right;font-weight:bold;">more</a>
<select id="catsel" style="display:none">
<?php foreach($categories as $cat){?>
<option value="<?=$cat->id?>"><?=$cat->name?></option>
<?php }?>
</select>
</div>
<?php }?>
</div>
<div id="viewagent" style="width:600px;margin-top:0px;margin-left:0px;padding-left:230px;">
<div align="right" style="font-size:12px;">
<?php 
$st=(($p-1)*10+1);
$et=$st+9;
if($et>$len)
	$et=$len;
?>
<?php if($len>0){?>
<?php if($p>1){?>
<a style="padding:5px;" href="<?=site_url("admin/brands/".($p-1))?>">previous</a>
<?php }?>
showing <?=$st?>-<?=$et?> of <?=$len?>
<?php if($et<$len){?>
<a style="padding:5px;" href="<?=site_url("admin/brands/".($p+1))?>">next</a>
<?php }}?>
</div>
<?php
//print_r($userdetails);
if($userdetails!==FALSE)
{
?>
<table class="tablesorter" id="tsort" cellspacing="0" cellpadding="0">
<thead>
<tr>
<th>
Brand Name
</th>
<th style="text-align:right;padding-right:70px;">
Actions
</th>
</tr>
</thead>
<tbody>
<?php 
$i=0;
foreach($userdetails as $details){
?>
<tr>
<td align="center" valign="center" >
<div><?=ucfirst(strtolower($details->brandname));?></div>
</td>
<?php /*?>
<td align="center" valign="center" >
<?=(strtolower($details->brandadmin));?>
</td>
<?php */?>
<td align="right" style="text-align:right">
<a style="margin-left:5px;" href="<?=site_url('admin/editbrand/'.$details->brandid)?>">Edit Brand</a>
|
<a href="<?=site_url('admin/dealsforbrand/'.$details->brandid)?>">View deals</a>
</td>
</tr>
<?php
$i++;}
?>
</tbody>
</table>
<?php
}
else {
?>
<p style="font-size:15px;color:#222;font-weight:bold;font-family:arial;">No brands available<?php if(isset($curcategory)){?> for <?=$curcategory->name?><?php }?>! 
</p>
<?} ?>
<?php if(isset($curcategory)){?>
<a style="font-size:12px;" href="<?=site_url("admin/categories/".$curcategory->id)?>">Assign a brand for <?=$curcategory->name?> category</a>
<?php }?>
</div>
<br style="clear:both;">
</div>