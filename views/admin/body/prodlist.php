<style>
table{
font-size:11px;
font-family:arial;
}
h3{
font-family:arial;
margin-bottom:0px;
}
body{
margin:0px;
padding:0px;
font-size:11px;
font-family:arial;
}
</style>
<div style="float:right;font-size:11px;"><?=date("g:ia d/m/y")?></div>
<h3>Product procurement list for <?=ucfirst($status)?> orders <?php if(!empty($from)){?>from <?=date("d-m-y",$from)?> to <?=date("d-m-y",$to)?><?php }?></h3>
<table width="100%" cellpadding=3 cellspacing=0 border=1>
<tr>

<th width=50>Sno</th>
<th width="400">Product Name</th>
<th width="40">QTY</th>
<th>Remarks</th>

</tr>

<?php $i=1;foreach($list as $l){?>
<tr>
<td><?=$i?></td>
<td><?=$l['name']?></td>
<td><?=$l['qty']?></td>
<td>&nbsp;</td>
</tr>
<?php $i++;}?>


</table>

<script>
	window.print();
</script>


<?php
