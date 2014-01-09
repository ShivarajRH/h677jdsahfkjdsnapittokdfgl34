<div class="container">
<h2>Available vanilla deals
<?php if($this->uri->segment(3)){
	foreach($cats as $cat)
		if($cat['id']==$this->uri->segment(3))
			echo " ({$cat['name']})"; 
}
?>
</h2>
<div style="padding:5px 0px">
view by category : 
<?php foreach($cats as $cat){?>
<a href="<?=site_url("franchisee/vanilla/".$cat['id'])?>"><?=$cat['name']?></a>
<?php }?>
</div>
<table border=1 cellpadding=7 style="background:#fff">
<tr><th></th><th>Deal Name</th><th>Real price</th>
<tH>Your price</tH>
<th>Mark Up/Down</th>
<th></th>
</tr>
<?php foreach($deals as $deal){?>
<tr>
<td><img src="<?=base_url()?>images/items/<?=$deal['pic']?>.jpg" height=100></td>
<td><h3><?=$deal['name']?></h3></td>
<td><?=$deal['price']?></td>
<td><?=$deal['price']+$deal['mark']?></td>
<td><?=$deal['mark']?></td>
<td><a href="<?=site_url("franchisee/addmark/2/".$deal['id'])?>">add/edit mark up</a></td>
</tr>
<?php }?>
</table>
</div>
<?php
