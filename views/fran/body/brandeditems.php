<div class="container">
<h2>Deal details</h2>
<table style="background:#fff" cellpadding=5 border=1 width="600">
<tr>
<th width=100>Pic</th>
<th>Name</th>
<th>Real Price</th>
<tH>Your Price</tH>
<th>Mark up/down</th>
<th></th>
</tr>
<?php foreach($deal as $d){?>
<tr>
<td><img src="<?=base_url()?>images/items/<?=$d['pic']?>.jpg" height="100"></td>
<td><h3><?=$d['name']?></h3></td>
<td><?=$d['price']?></td>
<td><?=$d['price']+$d['mark']?></td>
<tD><?=$d['mark']?></td>
<td><a href="<?=site_url("franchisee/deal/1/".$d['id'])?>">view mark up/down</a>
<br><br><a href="<?=site_url("franchisee/addmark/1/".$d['id'])?>">add/edit mark up/down</a></td>
</tr>
<?php }?>
</table>
</div>
<?php
