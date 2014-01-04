<div class="container">

<h2>Deals bulk upload</h2>

<form method="post" enctype="multipart/form-data">
Upload file : <input type="file" name="csv"><input type="submit" value="Upload">
<div style="color:#888;margin:10px 20px;"><ul><li>Only CSV format supported</li><li>First row as head neglected always</li></ul></div>
</form>

<h4 style="margin:0px;">Template</h4>

<table class="datagrid noprint">
<?php $template=array("Item ID","Description")?>
<thead>
<tr>
<?php foreach($template as $t){?><th><?=$t?></th><?php }?>
</tr>
</thead>
<tbody>
<tr>
<?php foreach($template as $t){?><td>&nbsp;</td><?php }?>
</tr>
</tbody>
</table>


</div>

<?php

