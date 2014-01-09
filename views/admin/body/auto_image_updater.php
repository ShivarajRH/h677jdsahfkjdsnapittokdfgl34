<div class="container">
<h2>Images Updater Cron Control</h2>

<?php $cron=$this->db->query("select * from cron_image_updater_lock")->row_array();?>

<div class="dash_bar">
Cron Status : <span><?php if($cron['finish_status']==1){?>STOPPED<?php }elseif($cron['is_locked']==0){?>RUNNING<?php }ELSE{?>LOCKED<?php }?></span>
</div>


<div class="dash_bar" style="padding:2px">
<?php if($cron['is_locked']==1){?>
<form method="post" onsubmit='confirm("Are you sure want to unlock the system? Is all the file uploaded to the server? No files in the queue?")'>
<input type="hidden" value="asdasdas" name="asdasdasd">
<input type="submit" style="padding:5px;font-size:120%;" value="Unlock the System">
</form>
<?php }else{?>
<input type="button" style="padding:5px;font-size:120%;" value="Lock the System" onclick='alert("Cron is running. Cannot lock the system")'>
<?php }?>
</div>

<?php if($cron['is_locked']==0){?>
<div class="dash_bar">
Refresh in <span id="secs">40</span> secs
</div>
<?php }?>

<div class="clear"></div>
<br><br>


<table class="datagrid noprint">
<thead><tr><th>Locked?</th><th>Modified On</th><th>Modified By</th><th>Images Updated in last run</th><th>Running?</th><th>Last run on</th></tr></thead>
<tbody>
<tr>
<td><?=$cron['is_locked']?"LOCKED":"NOT LOCKED"?></td>
<td><?=date("g:ia d/m/y",$cron['modified_on'])?></td>
<td><?=$cron['modified_by']==0?"Mr.Cron":$this->db->query("select name from king_admin where id=?",$cron['modified_by'])->row()->name?></td>
<td><b><?=$cron['images_updated']?></b></td>
<td><?=$cron['is_locked']?"NA":($cron['finish_status']?"FINISHED":"RUNNING")?></td>
<td><?=date("g:ia d/m/y",$cron['finished_on'])?></td>
</tr>
</tbody>
</table>

</div>

<?php if($cron['is_locked']==0){?>
<script>
var secs=20;
function timer()
{
	secs=secs-1;
	if(secs<=0)
	{
		location='<?=site_url("admin/auto_image_updater")?>';
		return;
	}
	$("#secs").html(secs);
window.setTimeout("timer()",1000);
}
$(function(){
	timer();
});
</script>
<?php }?>
<?php
