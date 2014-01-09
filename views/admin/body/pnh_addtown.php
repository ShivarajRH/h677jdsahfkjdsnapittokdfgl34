<div class="container">
<h2>PNH Add new town</h2>

<form method="post">
<table cellpadding=3>
<tr><td>
Territory : </td><td><select name="territory">
<?php foreach($this->db->query("select id,territory_name from pnh_m_territory_info order by territory_name asc")->result_array() as $t){?>
<option value="<?=$t['id']?>"><?=$t['territory_name']?></option>
<?php }?>
</select></td></tr>
<tr><td>Town Name : </td><td><input type="text" class="inp" name="town" size=40></td></tr>
<tr><td></td><td><input type="submit" value="Submit"></td></tr>
</table>
</form>

</div>
<?php
