<div class="container">

<h2>Products bulk upload</h2>

<form method="post" enctype="multipart/form-data">
Upload file : <input type="file" name="prods"><input type="submit" value="Upload">
<div style="color:#888;margin:10px 20px;"><ul><li>Only CSV format supported</li><li>First row as head neglected always</li></ul></div>
</form>

<h4 style="margin:0px;">Template</h4>
<table class="datagrid noprint">
<?php $template=array("Product Name","SKU Code","Short Description","Brand (ID)","Size","Unit of measurement","MRP","VAT %","Purchase Cost","Barcode","Is offer (0 or 1)","Is Sourceable (0 or 1)","Is serial Required","Group ID","Attribute data");?>
<thead>
<tr>
<?php foreach($template as $t){?><th><?=$t?></th><?php }?>
</tr>
</thead>
<tbody>
<tr>
<?php foreach($template as $i=>$t){?><td><?php if($i==12){?>2000013<?php }else if($i==13){?>size:small,color:blue<?php }else{?>&nbsp;<?php }?></td><?php }?>
</tr>
</tbody>
</table>


</div>
<?php
