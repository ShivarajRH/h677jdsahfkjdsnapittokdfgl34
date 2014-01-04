<div class="container">
<h2>Linked deals for product '<?=$product?>'</h2>
<table class="datagrid">
<tr><th>Deal</th><th>Quantity</th></tr>
<tbody>
<?php foreach($deals as $d){?>
<tr>
<td><a class="link" href="<?=site_url("admin/deal/{$d['dealid']}")?>"><?=$d['name']?></a></td>
<td><?=$d['qty']?></td>
</tr>
<?php }if(empty($deals)){?><tr><td colspan="100%">no deals linked</td></tr>
<?php }?>
</tbody>
</table>
</div>
<?php
