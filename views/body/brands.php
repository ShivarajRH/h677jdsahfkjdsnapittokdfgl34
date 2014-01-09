<style>
.container{width:990px;}
</style>

<div class="container">

<h2 style="margin-top:20px;">Available Brands</h2>

<div align="center" class="blist_navi">
<?php foreach($alphas as $alpha){?>
<a href="#balpha<?=$alpha?>"><?=strtoupper($alpha)?></a>
<?php }?>
</div>

<ul class="disc_cont disc_tags_cont">
<?php 
foreach($brands as $i=>$cbrands){?>
<li class="col<?=($i+1)?>">
<?php foreach($cbrands as $alpha=>$abrands){?>
<div class="alpha blist">
<h2><a id="balpha_<?=$alpha?>" name="balpha<?=$alpha?>"><?=strtoupper($alpha)?></a></h2>
<?php foreach($abrands as $brand){?>
<div><a href="<?=site_url($brand['url'])?>"><?=$brand['name']?></a></div>
<?php }if(empty($abrands)){?>
<i style="font-size:85%;font-weight:normal;">none</i>
<?php }?>
</div>
<?php }?>	
</li>
<?php }?>
</ul>
<div class="clear"></div>

</div>
<?php
