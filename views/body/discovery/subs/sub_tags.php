<?php 
if(!isset($cols))
	$cols=4;
$disp_tags=array();
for($i=0;$i<$cols;$i++)
	$disp_tags[$i]=array();
$i=0;
foreach($tags as $t)
{
	$disp_tags[$i][]=$t;
	$i++;
	if($i>=$cols)
		$i=0;
}
	
?>
<?php
if(!isset($showuser))
	$showuser=false;
foreach($disp_tags as $i=>$tgs){ ?>
<li class="col<?=($i+1)?>">
<?php foreach($tgs as $t){?>
			<div class="d_s_tag">
				<a class="img" href="<?=site_url("discovery/tag/{$t['url']}")?>"><img src="<?=IMAGES_URL?>tags/small/<?=$t['pic']?>.jpg"></a>
				<h3><?=$t['name']?></h3>
				<div class="bottom">Tagged onto <a href="<?=site_url("discovery/board/{$t['boardurl']}")?>"><?=$t['board']?></a> <?php if($showuser){?>by <a href="<?=site_url("discovery/user/{$t['username']}")?>"><?=$t['user']?></a><?php }?> on <?=date("g:ia d/m/y",$t['created_on'])?></div>
			</div>
<?php }?>
</li>
<?php }?>
