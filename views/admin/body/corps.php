<div class="container">

<h3>Corporates</h3>

<table class="datagrid" width="100%" style="background:#fff url(<?=base_url()?>images/bg.gif) repeat-x;padding:5px;" border=0 cellspacing="0" cellpadding="5">
<tr>
<th>Name</th><th>Email</th><th>Members</th><th>Aliases</th>
</tr>
<?php foreach($corps as $c){?>
<tr>
<td><a href="<?=site_url("admin/corporate/{$c['id']}")?>"><?=$c['name']?></a></td>
<td>@<?=$c['email']?></td>
<td><a href="<?=site_url("admin/usersbycorp/".$c['id'])?>"><?=$c['len']?></a></td>
<td><?=$c['aliases']?></td>
</tr>
<?php }?>
</table>

</div>
