<div class="container">
<h2>PNH Shipment SMS notification</h2>


<?php if(isset($orders)){?>
<div>
<form method="post">
<input type="hidden" name="fid" value="<?=$fran['franchise_id']?>">
<div style="padding:5px;">Franchise/Store name : <?=$fran['franchise_name']?></div>
<table class="datagrid">
<tr><th></th><th>Transid</th><th>Member Name</th><th>Member Mobile</th><th>Shipped on</th></tr>
<?php $i=1; foreach($orders as $o){?>
<tr>
<td><input type="checkbox" <?php if(empty($o['member_mobile'])){?>disabled<?php }?> name="transid[]" value="<?=$o['transid']?>"></td> 
<td><a href="<?=site_url("admin/trans/{$o['transid']}")?>" target="_blank"><?=$o['transid']?></a></td>
<td><?=$o['first_name']?> <?=$o['last_name']?></td>
<td><?=$o['member_mobile']?></td>
<td><?=date("g:ia d/m/y",$o['actiontime'])?></td>
</tr>
<?php }?> 
</table>
<div style="margin-top:20px;">
<b>Template :</b><br>
<textarea name="temp" style="width:500px;height:75px;">
Dear %mname%, your order %transid% is now available in our store. Please come and collect at your convenience. Thank you for your order
</textarea><br>
<div style="font-size:85%">%mname% - Member name<br>
%transid% - Order id<br>
%fname% - Franchise name</div>
</div>
<div style="padding:10px 0px;"><input type="submit" value="Submit"></div>
</form>
</div>
<?php }else{?>

<form method="post">
<table cellpadding=5>
<tr><td>
Select Franchise :</td><td> 
<select id="sms_fran" name="fid">
<?php foreach($this->db->query("select franchise_id,franchise_name from pnh_m_franchise_info where is_lc_store=1 order by franchise_name asc")->result_array() as $f){ ?>
<option value="<?=$f['franchise_id']?>"><?=$f['franchise_name']?></option>
<?php }?>
</select>
</td></tr>
<tr><td>Order Date range : </td><td><input type="text" name="start" id="ds_start"> to <input type="text" name="end" id="ds_end"></td></tr>
<tr><Td></Td><td><input type="submit" value="Submit"></td></tr>
</table>
</form>
<script>
$(function(){
	$("#ds_start,#ds_end").datepicker();
});
</script>

<?php }?>


</div>
<?php

