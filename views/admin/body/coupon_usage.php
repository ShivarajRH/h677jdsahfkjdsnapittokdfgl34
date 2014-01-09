<h3>Coupon usage history for selected criteria</h3>
<table width="100%" cellspacing=0 cellpadding=5 border=1>
<tr>
<th>Coupon</th>
<th>Transid</th>
<th>User Name</th>
<th>Email</th>
<th>Used on</th>
</tr>
<?php foreach($usage as $u){ ?>
<tr>
<td><a href="<?=site_url("admin/coupon/{$u['code']}")?>"><?=$u['code']?></a></td>
<td><a href="<?=site_url("admin/trans/{$u['transid']}")?>"><?=$u['transid']?></a></td>
<td><a href="<?=site_url("admin/user/{$u['userid']}")?>"><?=$u['name']?></a></td>
<td><?=$u['email']?></td>
<td><?=date("d/m/y",$u['time'])?></td>
</tr>
<?php }?>
</table>

<?php
