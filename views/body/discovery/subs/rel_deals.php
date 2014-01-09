<?php foreach($deals as $d){?>
<div style="padding:5px;border-bottom:1px solid #ccc;">
<a href="<?=site_url($d['url'])?>"><img src="<?=IMAGES_URL?>items/small/<?=$d['pic']?>.jpg" style="float:left;"></a>
<div align="left">
<a href="<?=site_url($d['url'])?>" class="redlink"><h5><?=$d['name']?></h5></a>
</div>
<div align="left">
<a href="<?=site_url($d['caturl'])?>" class="blacklink" style="float:right"><h5><?=$d['category']?></h5></a>
<h5>Rs <?=$d['price']?></h5>
</div>
</div>
<?php }?>
<?php
