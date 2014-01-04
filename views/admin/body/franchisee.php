<div class="container">
<a href="<?=site_url("admin/addfran")?>" style="float:right;font-weight:bold;font-size:13px;">Add Franchisee</a>
<h2>Franchisees</h2>
<div style="margin:10px;background:#fff;padding:10px;">
<?php if(empty($frans)){?>
<h2>No franchisee added</h2>
<?php }else{?>
<table width="100%">
<tr>
<th>Sno</th>
<th>Name</th>
<th>User Name</th>
<th>Email</th>
<th>Balance</th>
<th>City</th>
<th></th>
</tr>
<?php foreach($frans as $i=>$fran){?>
<tr>
<td><?=$i+1?></td>
<td><?=$fran['name']?></td>
<td><?=$fran['username']?></td>
<td><?=$fran['email']?></td>
<td><?=$fran['balance']?> <a href="<?=site_url("admin/franaddbal/".$fran['id'])?>">credit</a>
</td>
<td><?=$fran['city']?></td>
<td><a href="<?=site_url("admin/editfran/".$fran['id'])?>">edit</a></td>
</tr>
<?php }?>
</table>
<?php }?>
</div>
</div>
<?php
