<div class="container">
<h2>PNH Orders Import</h2>

<form method="post" enctype="multipart/form-data" style="padding:10px;">
<input type="file" name="csv"><input type="submit" value="Upload">
</form>

<ul style="margin-left:30px;color:#888;">
<li>First row always ignored as heading</li>
<li>Product IDs and Qtys are comma seperated with respect to each other</li>
</ul>

<?php 
$cols=array("PNH Franchise ID","Member ID","PNH Product IDs","Qtys","User Notes","Member Name","Member address","Member City","Member Pincode","Member email","Member mobile","Attributes (JSON data) array('pnh_ID'=>'ATTR_DATA')");
$ex_c=array('31234567','21234567',"11234567,12345678,11223344",'2,5,3','user note',"Raju Signth","433/1, Ambedkar veethi","Bangalore","560030","rajusignth@ymail.com","9553553531",'{"11223344":"Size:7,Color:Khakhi"}');
?>
<h4>Template</h4>
<table class="datagrid">
<thead><tr><?php foreach($cols as $c){?><th><?=$c?></th><?php }?></tr></thead>
<tbody><tr>
<?php foreach($ex_c as $e){?><td><?=$e?></td><?php }?>
</tr></tbody>
</table>

</div>
<?php
