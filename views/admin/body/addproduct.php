<?php $p=false; if(isset($prod)) $p=$prod;?>
<div class="container">
<h2><?=$p?"Edit":"Add new"?> product</h2>

<form method="post" data-validate="parsley">
<table>
<tr><td>Product Name :</td><td><input type="text"  name="pname" size=30 value="<?=$p?$p['product_name']:""?>" data-required="true"></td></tr>
<tr><td>SKU Code :</td><td><input type="text" name="sku_code" size=15 value="<?=$p?$p['sku_code']:""?>" ></td></tr>
<tr><td>Short Description :</td><td><input type="text" name="pdesc" size=50 value="<?=$p?$p['short_desc']:""?>" ></td></tr>
<tr><td>Size :</td><td><input type="text" name="psize" size=5 value="<?=$p?$p['size']:""?>" data-required="true"></td></tr>
<tr><td>Unit of measurement :</td><td><input type="text" name="puom" value="<?=$p?$p['uom']:""?>" ></td></tr>
<tr><td>MRP :</td><td><input type="text" name="pmrp" size=4 value="<?=$p?$p['mrp']:""?>" data-required="true"></td></tr>
<tr><td>VAT :</td><td><input type="text" name="pvat" size=2 value="<?=$p?$p['vat']:""?>" data-required="true">%</td></tr>
<tr><td>Purchase Cost :</td><td><input type="text" name="pcost" value="<?=$p?$p['purchase_cost']:""?>" data-required="true"></td></tr>
<tr><td>Barcode:</td><td><input type="text" name="pbarcode" value="<?=$p?$p['barcode']:""?>"></td></tr>
<tr><td>Is Offer :</td><td><input type="checkbox" name="pisoffer" value=1 <?=$p?($p['is_offer']?"checked":""):""?>></td></tr>
<tr><td>Is Sourceable :</td><td><input type="checkbox" name="pissrc" value="1" <?=$p?($p['is_sourceable']?"checked":""):""?> ></td></tr>
<tr><td>Is Serial No.required :</td><td><input type="checkbox" name="pissno" value="1" <?=$p?($p['is_serial_required']?"checked":""):""?>></td></tr>
<tr><td>Brand :</td><td>
<select name="pbrand" data-required="true">
<option value="">Choose</option>
<?php foreach($this->db->query("select id,name from king_brands order  by name asc")->result_array() as $b){?>
<option value="<?=$b['id']?>" <?=$p?($p['brand_id']==$b['id']?"selected":""):""?>><?=$b['name']?></option>
<?php }?>
</select>
</td></tr>
<tr style="display:none;"><td>Rackbin :</td><td>
<select name="prackbin">
<option value="">Choose</option>
<?php foreach($this->db->query("select * from m_rack_bin_info order by rack_name asc")->result_array() as $b){?>
<option value="<?=$b['id']?>"><?=$b['rack_name']?><?=$b['bin_name']?></option>
<?php }?>
</select>
</td></tr>
<tr><td>MOQ :</td><td><input type="text" name="pmoq" value="<?=$p?$p['moq']:""?>"></td></tr>
<tr><td>Reorder Level :</td><td><input type="text" name="prorder" value="<?=$p?$p['reorder_level']:""?>"></td></tr>
<tr><td>Reorder Qty :</td><td><input type="text" name="prqty" value="<?=$p?$p['reorder_qty']:""?>"></td></tr>
<tr><td>Remarks :</td><td><input type="text" name="premarks" value="<?=$p?$p['remarks']:""?>"></td></tr>
<tr><td>Is Active :</td><td><input type="checkbox" name="is_active" value="1" <?=$p&&$p['is_active']?"checked":""?> ></td></tr>
<tr><td></td><td><input type="submit" value="<?=$p?"Update":"Add"?> product">
</table>
</form>

</div>
<?php
