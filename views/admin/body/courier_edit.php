<div class="container">
<h2>Edit courier</h2>

<form method="post" onsubmit="return validate();">

<table cellpadding=3>
    <?php //echo '<pre>'; print_r($couriers[0]); print_r($partners); die();
    $c=$couriers[0];
    $awb = $couriers['awb'];
    
    $pincodes_list='';
    foreach($couriers['pincodes']  as $pin) {
            $pincodes_list .= $pin['pincode'].",";
    }
    ?>
<tr><td>Courier Name :</td><td>
        <input type="hidden" class="inp" name="courier_id" size=30 value="<?= $c['courier_id'];?>">
        <input type="text" class="inp" name="name" size=30 value="<?= $c['courier_name'];?>">
    </td></tr>
<tr><td>AWB Prefix :</td><td><input type="text" class="inp" size=3 name="awb_prefix" value="<?= $awb['awb_no_prefix'];?>"></td></tr>
<tr><td>AWB Suffix :</td><td><input type="text" class="inp" size=3 name="awb_suffix" value="<?= $awb['awb_no_suffix'];?>"></td></tr>
<tr><td>AWB Starting No :</td><td><input type="text" class="inp" size=12 name="awb_start" value="<?= $awb['awb_current_no'];?>"></td></tr>
<tr><td>AWB Ending No :</td><td><input type="text" class="inp" size=12 name="awb_end" value="<?= $awb['awb_end_no'];?>"></td></tr>
<tr><td>COD Available :</td><td><input type="checkbox" name="cod" value="1" <?=($c['cod_available']==1)?'checked':""; ?>/></td></tr>
<tr><td>Pincodes :<br>Comma separated</td>
    <td><textarea class="inp" cols=50 rows=8 name="pincodes"><?=$pincodes_list;?></textarea>
</tr>
<tr><td>Used for</td>
    <td>
        <select name="used_for" id="used_for">
            <option value="00">Choose</option>
<!--            <option value="pnh">PNH</option> 
            <option value="0">SIT</option> -->
            <?php 
            if($c['is_active']==1) {
                echo'<option value="pnh" selected>PNH</option> ';
            }
            else {
                echo'<option value="pnh">PNH</option> ';
            }
            if($c['is_active']==0 and $c['ref_partner_id']==0) {
                echo '<option value="sit" selected>SIT</option> ';
            }
            else {
                echo '<option value="sit">SIT</option> ';
            }
            
            foreach($partners as $pts){
                if($c['ref_partner_id'] == $pts['id']) { ?>
                    <option value="<?=$pts['id']?>" selected>-<?=$pts['name']?></option> 
                <?php 
                }
                else {?> 
                    <option value="<?=$pts['id']?>">-<?=$pts['name']?></option> 
<?php            }
            }
?>
        </select>
    </td>
</tr>

</table>
<?php //echo $c['ref_partner_id']; echo $c['is_active']; ?>
<div style="padding:10px;">
<input type="submit" value="Update Courier">
</div>

</form>


</div>
<script>
    function validate() {
        var used_for=$("#used_for").find(":selected").val();
        if(used_for=='00') {
            alert("Please select courier used for");
            return false;
        }
    }
</script>
<?php
