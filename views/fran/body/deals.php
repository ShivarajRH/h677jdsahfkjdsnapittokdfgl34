<div class="container">
<h2>Available Branded deals</h2>
<table border=1 cellpadding=7 style="background:#fff" width=500>
<tr><th width="100"></th><th>Deal Name</th><th></th></tr>
<?php foreach($deals as $deal){?>
<tr>
<td><img src="<?=base_url()?>images/items/<?=$deal['pic']?>.jpg" height=100></td>
<td><h3><?=$deal['tagline']?></h3></td>
<td><a href="<?=site_url("franchisee/viewdeal/".$deal['dealid'])?>">view items</a><br><br>
<a href="<?=site_url("franchisee/addmark/10/".$deal['dealid'])?>">mark up/down for all items in this deal</a>
</td>
</tr>
<?php }?>
</table>
</div>
<?php
