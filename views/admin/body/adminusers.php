<div class="container">
<h2>Admin Users</h2>
<a href="<?=site_url("admin/addadminuser")?>">Add admin user</a>
<table class="datagrid" width="100%">
<thead><tr><th>Username</th><th>Name</th><th>Email</th><th>User Roles</th><th></th></tr></thead>
<tbody>
<?php foreach($users as $u){?>
<tr style="<?php echo $u['account_blocked']?'background:#faa':'' ?>">
<td><?=$u['username']?></td>
<td><?=$u['name']?></td>
<td><?=$u['email']?></td>
<td>
<?php foreach($roles as $r){?>
<?=((double)$u['access']&(double)$r['value'])>0?"{$r['user_role']}, ":""?>
<?php }?>
</td>

<td width="130">
<a class="link" href="<?=site_url("admin/editadminuser/{$u['id']}")?>">Edit</a>
 - 
<a onclick='return confirm("Are you sure?")' href="<?=site_url("admin/resetadminuserpass/{$u['id']}")?>">Reset Password</a>
</td>
</tr>
<?php }?>
</tbody>
</table>
</div>
<?php
