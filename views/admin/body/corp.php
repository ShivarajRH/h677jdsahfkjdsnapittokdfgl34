<div class="container">

<h3>Corporate Details</h3>

<table cellpadding=3>
<tr><td>Name</td><td>:</td><td><?=$corp['name']?></td></tr>
<tr><td>Email</td><td>:</td><td>@<?=$corp['email']?></td></tr>
<tr><td>Members</td><td>:</td><td><?=$len?></td></tr>
</table>

<table width="100%">
<tr>
<td>
<h4 style="margin-bottom:0px;">Change Name</h4>
<form method="post">
Name : <input type="text" name="name" value="<?=$corp['name']?>"> <input type="submit" value="Change">
</form>
</td>
<td>
<h4 style="margin-bottom:0px;">Make this as alias</h4>
<form method="post">
Alias for : <select name="alias">
<?php foreach($corps as $c){ if($c['id']==$corp['id']) continue;?>
<option value="<?=$c['id']?>"><?=$c['name']?></option>
<?php }?>
</select>
 <input type="submit" value="Change">
</form>
<span style="color:red">*one way process, non-undo-able</span>
</td>
</tr>

<tr>
<td>
<h4 style="margin-bottom:0px;">Aliases</h4>
<table cellpadding=4 border=1 style="background:#fff;padding:10px;min-width:250px;" cellspacing=0>
<?php  if(!empty($aliases)){?>
<tr>
<th>Name</th><th>Email</th>
</tr>
<?php foreach($aliases as $a){?>
<tr>
<td><?=$a['name']?></td>
<td><?=$a['email']?></td>
</tr>
<?php }}else echo '<h5 style="margin-top:0px;">No aliases set</h5>';?>
</table>
</td>

<td valign="top">
<h4 style="margin-bottom:0px;">Members</h4>
<a href="<?=site_url("admin/usersbycorp/{$corp['id']}")?>">view members</a>
</td>
</tr>
</table>

</div>