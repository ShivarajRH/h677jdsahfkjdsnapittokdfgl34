<?php
foreach($reviews as $r){?>
<div class="review">
	<div class="rate"><?php for($i=1;$i<=$r['rating'];$i++){?><img src="<?=IMAGES_URL?>star.png"><?php }?><?php for(;$i<=5;$i++){?><img src="<?=IMAGES_URL?>unstar.png"><?php }?></div>
	<div class="name"><?=$r['name']?> :</div>
	<div class="cont"><?=$r['review']?></div>
</div>
<?php } if(empty($reviews)){?>
<h3>No reviews yet</h3>
<?php }?>