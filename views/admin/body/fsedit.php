<div class="container">

<h2>Add/edit Freesample</h2>
<form enctype="multipart/form-data" method="post" class="fsform">

<div>
Name : <input type="text" name="name" value="<?=$fs['name']?>">
</div>

<div>
Image : <input type="file" name="pic">
</div>

<div>
Minimum order : <input type="text" name="min" value="<?=$fs['min']?>">
</div>

<div>
Available : <select name="available"><option value="1" <?=$fs['available']==1?"selected":""?>>YES</option><option value="0" <?=$fs['available']==0?"selected":""?>>NO</option></select>
</div>

<div>
<input type="submit" value="Add/New">
</div>

</form>
</div>

<style>
.fsform div{
padding:5px;
}
</style>
<?php
