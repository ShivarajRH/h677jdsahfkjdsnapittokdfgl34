<div class="container">

<h2>View/Update partner deal prices for '<?=$this->db->query("select name from king_dealitems where id=?",$this->uri->segment(3))->row()->name?>'</h2>

<form method="post">

<table class="datagrid">
<thead><tr><th>Partner</th><th>Customer Price</th><th>Partner's purchase Price</th><th>Created On</th><th>Created By</th><th>Modified On</th><th>Modified By</th></tr></thead>
<tbody>
<?php foreach($this->db->query("select p.*,p.id as partner_id from partner_info p order by p.name asc")->result_array() as $p){?>
<tr><td><?=$p['name']?><input type="hidden" name="partner_id[]" value="<?=$p['partner_id']?>"></td>
<?php $price=$this->db->query("select p.*,p.customer_price,p.partner_price,c.name as created_by,m.name as modified_by from partner_deal_prices p join king_admin c on c.id=p.created_by left outer join king_admin m on m.id=p.modified_by where partner_id=? and itemid=?",array($p['partner_id'],$itemid))->row_array(); if(empty($price)) $price=array("customer_price"=>'',"partner_price"=>'',"created_by"=>'',"created_on"=>0,"modified_on"=>"","modified_by"=>''); ?>
<td>Rs <input size=5 type="text" name="customer_price[]" value="<?=$price['customer_price']?>"></td>
<td>Rs <input size=5 type="text" name="partner_price[]" value="<?=$price['partner_price']?>"></td>
<td><?=$price['modified_on']!=0?date("d/m/y",$price['created_on']):"na"?></td>
<td><?=$price['created_by']?></td>
<td><?=$price['modified_on']!=0?date("d/m/y",$price['modified_on']):"na"?></td>
<td><?=$price['modified_by']?></td>
</tr>
<?php }?>
</tbody>
</table>
<input type="submit" value="Submit">
</form>

</div>
<?php
