<div class="container">
<h2>PNH Members <?=isset($pagetitle)?$pagetitle:""?></h2>

<div class="dash_bar_right" style="max-width:1000px;">
View members of
<select id="fid">
<option value="0">select</option>
<?php foreach($this->db->query("select franchise_id,franchise_name as name,city from pnh_m_franchise_info order by name asc")->result_array() as $f){?>
<option value="<?=$f['franchise_id']?>"><?=$f['name']?>, <?=$f['city']?></option>
<?php }?>
</select>
</div>


<div class="dash_bar">
<a href="<?=site_url("admin/pnh_members")?>"></a>
<span><?=$this->db->query("select count(1) as l from pnh_member_info")->row()->l;?></span>
Members
</div>


<div class="clear"></div>
<a href="<?=site_url("admin/pnh_addmember")?>">Add/Update Member</a>
&nbsp;
<a href="<?=site_url("admin/pnh_bulkaddmembers")?>">Bulk Import Members</a>

<h4>Recently created</h4>
<table class="datagrid" width="100%">
<thead>
<tr><th>MID</th><th>Name</th><th>Email</th><th>Mobile</th><th>Franchise</th><th>Total orders</th><th>MID Card processed?</th>
	<th>Action</th>
</tr>
</thead>
<tbody>
<?php 
foreach($users as $u){?>
<tr>
<td>
<a href="<?=site_url("admin/pnh_viewmember/{$u['user_id']}")?>" class="link"></a>
<?=$u['pnh_member_id']?></td><td><?=$u['first_name']?> <?=$u['last_name']?></td><td><?=$u['email']?></td><td><?=$u['mobile']?></td><td>
<a href="<?=site_url("admin/pnh_franchise/{$u['franchise_id']}")?>"><?=$u['fran']?></a></td>
<td><?=$u['orders']?></td>
<td><?=$u['is_card_printed']?"YES":"NO"?></td>
<td> <a href="<?php echo site_url('admin/pnh_editmember/'.$u['pnh_member_id']);?>">Edit</a> </td>
</tr>
<?php }?>
</tbody>
</table>


</div>

<script>
$(function(){
	$("#fid").change(function(){
		location="<?=site_url("admin/pnh_members")?>/"+$(this).val();
	});
});
</script>
<?php
