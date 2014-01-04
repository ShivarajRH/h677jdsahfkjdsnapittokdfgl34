<div class="container">

<h2>New Annoucement</h2>
<div><a href="<?=site_url("admin/newann")?>">new annoucment</a></div>

<div style="margin:10px 50px;">
<form action="<?=site_url("admin/announcements")?>" method="post">
<?php if(isset($a['id'])){?>
<input type="hidden" name="id" value="<?=$a['id']?>">
<?php }else $a=array("text"=>"","url"=>"");?>
<table>
<tr>
<td>Text:</td>
<td><input type="text" name="text" value="<?=$a['text']?>"></td>
</tr>
<tr>
<td>Url(optional) : </td>
<td><input type="text" name="url" value="<?=$a['url']?>"></td>
</tr>
<tr>
<td>
</td>
<td><input type="submit" value="submit"></td>
</tr>
</table>
</form>
</div>



</div>
<?php
