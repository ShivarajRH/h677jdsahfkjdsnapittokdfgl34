<div class="container">

<h2>User Roles</h2>

<table class="datagrid">
<thead><tr><th>User Role</th><th>Identifier</th></tr></thead>
<tbody>
<?php foreach($roles as $r){?>
<tr><td><?=$r['user_role']?></td><td><?=$r['const_name']?></td></tr>
<?php }?>
</tbody>
</table>

</div>
<?php
