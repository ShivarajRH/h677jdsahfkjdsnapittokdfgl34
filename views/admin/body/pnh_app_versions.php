<div class="container">

<h2>App Versions</h2>

<div style="float:right;margin-right:30px;background:#eee;border:1px solid #aaa;padding:10px;">
<h4 style="margin:5px;margin-left:0px;">Add new version</h4>
<form method="post">
Version Number : <input type="text" name="no" size=3> <input type="submit" value="Add Version">
</form>
</div>

<table class="datagrid">
<theaD><tr><th>Version No</th><th>Version date</th><th>Created By</th></tr></theaD>
<tbody>
<?php foreach($this->db->query("select v.*,a.name as admin from pnh_app_versions v left outer join king_admin a on a.id=v.created_by order by v.id asc")->result_array() as $v){?>
<tr>
<td><?=$v['version_no']?></td><td><?=date("g:ia d/m/y",$v['version_date'])?></td><td><?=$v['admin']?></td>
</tr>
<?php }?>
</tbody>
</table>

</div>

<?php
