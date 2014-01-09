<div class="container">
<a href="<?php echo site_url("admin/pnh_franchise/$fid")?>"style="font-size: 18px;color: red;"><?=$this->db->query("select franchise_name from pnh_m_franchise_info where franchise_id=?",$fid)->row()->franchise_name?></h2><h1 style="font-size: 11px;color:black;">(back)</h1></a>
<h2>Assign Executives</h2>
<form method="post">
<table class="datagrid" id="assgn">
<thead><Tr><th>Executive</th><th><input type="button" value="add" onclick='$("#assgn tbody").append("<tr>"+$("#temp").html()+"></tr>")'></th></Tr></thead>
<tbody>
<?php foreach($exec as $e){?>
<tr><td>
<select name="admins[]">
<?php foreach($admins as $a){ if(!$this->erpm->is_user_role($a['id'],PNH_EXECUTIVE_ROLE)) continue; ?>
<option value="<?=$a['id']?>" <?=$a['id']==$e['admin']?"selected":""?>><?=$a['name']?></option>
<?php }?>
</select>
</td><td><a href="javascript:void(0)" onclick='$($(this).parents("tr").get(0)).remove()'>remove</a></td></tr>
<?php }?>
</tbody>
</table>
<input type="submit" value="Submit">
</form>
</div>

<div style="display:none">
<table>
<tr id="temp">
<td>
<select name="admins[]">
<?php foreach($admins as $a){ if(!$this->erpm->is_user_role($a['id'],PNH_EXECUTIVE_ROLE)) continue; ?>
<option value="<?=$a['id']?>"><?=$a['name']?></option>
<?php }?>
</select>
</td><td><a href="javascript:void(0)" onclick='$($(this).parents("tr").get(0)).remove()'>remove</a></td>
</tr>
</table>
</div>

<?php
