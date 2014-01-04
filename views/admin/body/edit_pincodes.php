<div class="container">
<h2>Edit assigned pincodes for courier</h2>

<form method="post">
<input type="hidden" name="courier_id" value="<?=$courier_id?>">
Enter pincodes separated by comma<br>
<textarea style="width:600px;height:150px;" name="pincodes"><?=implode(",",$pincodes)?></textarea>
<br>
<input type="submit" value="Update">
</form>

</div>
<?php
