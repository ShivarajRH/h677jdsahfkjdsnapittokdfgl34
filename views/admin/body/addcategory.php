<?php $c=false;if(isset($cat)) $c=$cat;?>
<div class="container">
<h2><?=$c?"Edit":"Add"?> category</h2>
<form method="post">
Category Name : <input type="text" class="inp" name="cat_name" value="<?=$c?$c['name']:""?>">
<br><br>
Main Category : <select name="main">
<option value="0">no main category</option>
<?php foreach($this->db->query("select id,name from king_categories where type=0 order by name asc")->result_array() as $m){?>
<option <?=$c&&$m['id']==$c['type']?"selected":""?> value="<?=$m['id']?>"><?=$m['name']?></option>
<?php }?>
</select><br><br>
<input type="submit" value="<?=$c?"Update":"Add"?>">
</form>
</div>
<?php
