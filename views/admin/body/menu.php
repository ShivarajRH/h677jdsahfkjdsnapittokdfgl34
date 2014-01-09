<div class="heading" style="margin-bottom:20px;">
<div  class="headingtext container">Menu
<div style="float:right;"><a style="font-size:14px;" href="<?=site_url("admin/addmenu")?>">Add new menu</a></div>
</div>
</div>

<div class="container">
<h3>Available</h3>
<?php $dmenu=array(); if(empty($menu)){?>
	<div style="margin:10px;">No menu available. Add one!</div>
<?php }else{$i=0;
foreach($menu as $m){
	if($m['status']==0)
	{
		$dmenu[]=$m;continue;
		print_r($dmenu);
	}
	?>
	<div style="background:#eee;margin:10px;padding:3px;">
	 <a href="<?=site_url("admin/menustatus/{$m['id']}/0")?>" style="float:right;">disable</a>
	 <?=$m['name']?></div>
<?php } } 
if(!empty($menu) && $i==0){?><div style="margin:10px;">No menu available. Add one!</div><?php } ?>

<h3>Disabled</h3>
<?php if(empty($dmenu)){?>
	<div style="margin:10px;">No disabled menu</div>
<?php }else{
foreach($dmenu as $m){?>
	<div style="background:#eee;margin:10px;padding:3px;">
	 <a href="<?=site_url("admin/menustatus/{$m['id']}/1")?>" style="float:right;">enable</a>
	 <?=$m['name']?></div>
<?php } } ?>



</div>
<?php
