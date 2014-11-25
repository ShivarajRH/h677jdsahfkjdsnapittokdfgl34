<div class="container">
<h2>Add new courier</h2>

<form method="post" onsubmit="return validate();">

<table cellpadding=3>
<tr><td>Courier Name :</td><td><input type="text" class="inp" name="name" size=30></td></tr>
<tr><td>Courier Toll Free Number</td><td><input type="text" class="inp" name="courier_no" size=30></td></tr>
<tr><td>AWB Prefix :</td><td><input type="text" class="inp" size=3 name="awb_prefix"></td></tr>
<tr><td>AWB Suffix :</td><td><input type="text" class="inp" size=3 name="awb_suffix"></td></tr>
<tr><td>AWB Starting No :</td><td><input type="text" class="inp" size=12 name="awb_start"></td></tr>
<tr><td>AWB Ending No :</td><td><input type="text" class="inp" size=12 name="awb_end"></td></tr>
<tr><td>COD Available :</td><td><input type="checkbox" name="cod" value="1"></td></tr>
<tr><td>Pincodes :<br>Comma separated</td>
    <td><textarea class="inp" cols=50 rows=8 name="pincodes"></textarea>
</tr>
<tr><td>Used for</td>
    <td>
        <select name="used_for" id="used_for">
            <option value="00">Choose</option>
            <option value="pnh">PNH</option>
            <option value="sit">SIT</option> 
            <?php foreach($partners as $pts){?>
                <option value="<?=$pts['id']?>">-<?=$pts['name']?></option> 
            <?php } ?>
        </select>
    </td>
</tr>
</table>

<div style="padding:10px;">
<input type="submit" value="Add Courier">
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
