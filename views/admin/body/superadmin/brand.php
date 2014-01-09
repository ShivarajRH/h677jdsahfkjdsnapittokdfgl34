<?php $user=$this->session->userdata("admin_user");?>
<div class="heading" style="margin-bottom:0px;margin-top: 40px;">
<div class="headingtext container">
<div style="float:right;"><a style="font-size:14px;color:blue;" href="<?=site_url("admin/addbrand")?>">Add brand</a></div>
Brand Details</div>
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
<div style="width:700px;margin-top:15px;margin-left:0px;padding-left:230px;">
<div style="-moz-border-radius:10px;border:1px solid #ccc;padding:10px;font-family:arial;background:url(<?=base_url()?>images/bg.gif) repeat-x;">
<a href="<?=site_url("admin/editbrand/{$brand['id']}")?>" style="float:right;font-size:15px;">Edit</a>
<div style="font-family:trebuchet ms;font-size:25px;"><?=$brand['brandname']?></div>
<div style="padding:10px;">
<div style="padding:3px 0px;">Number of deals : <?=$dealsnum?> <a href="<?=site_url("admin/dealsforbrand/{$brand['id']}")?>" style="font-size:12px;">view deals</a></div>
</div> 
<div style="font-family:trebuchet ms;font-size:19px;margin-bottom:5px;">Categories assigned </div>
<?php foreach($brandcategories as $bc){?>
<span style="margin-left:40px;"><b><a href="<?=site_url("admin/categories/{$bc->id}")?>"><?=$bc->name?></a></b></span>
<?php }?>
</div>
</div>
</div>