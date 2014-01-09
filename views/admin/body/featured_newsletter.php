<div class="container">

<h2>Featured Newsletter</h2>

<input type="button" value="Create mail" onclick='location="<?=site_url("admin/create_fnewsletter")?>"'>
<br><br>
<table width="600" cellpadding=5 border=1 cellspacing=0 class="datagrid">
<tr>
<th>Sno</th><th>Url</th><th>Products</th><th>Brands</th><th>Created on</th>
</tr>
<?php foreach($mails as $m){?>
<tr>
<td><?=$m['id']?></td>
<td><?=site_url("promo_email/".$m['url'])?>
<br><a class="link" href="<?=site_url("promo_emails/".$m['url'])?>" target="_blank">view</a>
</td>
<td><?=count(explode(",",$m['items']))?></td>
<td><?=count(explode(",",$m['brands']))?></td>
<td><?=date("d/m/y g:ia",$m['time'])?>
</tr>
<?php }?>
</table>

<br><br><br>

</div>
<?php
