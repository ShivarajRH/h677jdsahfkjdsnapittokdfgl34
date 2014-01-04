<div class="container">
<h2>Vars</h2>
<div style="padding:20px;">
<?php
$names=array(1=>"Featured products title");
 foreach($vars as $v){?>
 <h3><?=$names[$v['id']]?></h3>
<form method="post">
<input type="hidden" name="id" value="<?=$v['id']?>">
<input type="text" name="var" value="<?=htmlspecialchars($v['value'])?>" style="width:400px;"><input type="submit" value="update">
</form>
<?php }?>
</div>
</div>
<?php
