<div class="container">

<h2>Announcements</h2>
<div><a href="<?=site_url("admin/newann")?>">new announcment</a></div>

<div style="margin:10px 50px;">

<table class="datagrid" border=1 cellpadding=5 cellspacing=0>
<tr>
<th>Text</th>
<th>URL</th>
<th></th>
<th></th>
</tr>

<?php foreach($aas as $a){?>
<tr>
<td><?=$a['text']?></td>
<td><?=$a['url']?></td>
<td><?=$a['enable']==1?"enabled":"disabled"?></td>
<td>
<a class="link" href="<?=site_url("admin/editann/{$a['id']}")?>">edit</a>
<a href="<?=site_url("admin/disenann/{$a['id']}/{$a['enable']}")?>"><?=$a['enable']==0?"enable":"disable"?></a>
</td>
</tr>
<?php }?>
</table>

</div>



</div>
<?php
